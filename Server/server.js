// server-claim-example.js
require('dotenv').config();
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const fs = require('fs');
const bs58 = require('bs58');
const nacl = require('tweetnacl');
const { Connection, PublicKey, Keypair, Transaction, sendAndConfirmRawTransaction } = require('@solana/web3.js');
const { getAssociatedTokenAddress, createAssociatedTokenAccountInstruction, createTransferCheckedInstruction } = require('@solana/spl-token');

const app = express();
app.use(cors());
app.use(bodyParser.json());

const RPC_URL = process.env.RPC_URL || 'https://api.mainnet-beta.solana.com';
const connection = new Connection(RPC_URL, 'confirmed');

// CONFIG - sesuaikan
const MINT_ADDRESS = "E5chNtjGFvCMVYoTwcP9DtrdMdctRCGdGahAAhnHbHc1";
const MINT_DECIMALS = 6;
const RELAYER_KEYPAIR_PATH = process.env.RELAYER_KEYPAIR || './relayer.json';

// load relayer keypair (format: array of numbers)
const relayerSecret = JSON.parse(fs.readFileSync(RELAYER_KEYPAIR_PATH, 'utf8'));
const relayer = Keypair.fromSecretKey(Uint8Array.from(relayerSecret));
console.log('Loaded relayer pubkey:', relayer.publicKey.toString());

app.post('/api/claim', async (req, res) => {
  try {
    const { message, signature, publicKey } = req.body;
    if (!message || !signature || !publicKey) return res.status(400).json({ success:false, error:'missing_fields' });

    // verify signature (ed25519)
    const msgBytes = Buffer.from(message, 'utf8');
    const sigBytes = bs58.decode(signature);
    const pubkeyBytes = bs58.decode(publicKey);

    const ok = nacl.sign.detached.verify(msgBytes, sigBytes, pubkeyBytes);
    if (!ok) return res.status(400).json({ success:false, error:'invalid_signature' });

    const obj = JSON.parse(message);
    const userAddress = obj.address || publicKey;
    const amount = Number(obj.amount || 0);
    if (!userAddress || amount <= 0) return res.status(400).json({ success:false, error:'invalid_message' });

    // compute amount in raw units
    const amountRaw = BigInt(Math.floor(amount * Math.pow(10, MINT_DECIMALS)));

    // prepare mint & ATAs
    const mintPubkey = new PublicKey(MINT_ADDRESS);
    const sourceATA = await getAssociatedTokenAddress(mintPubkey, relayer.publicKey);
    const destPubkey = new PublicKey(userAddress);
    const destATA = await getAssociatedTokenAddress(mintPubkey, destPubkey);

    const ix = [];
    // if dest ATA doesn't exist, add create ATA instruction
    const destInfo = await connection.getAccountInfo(destATA);
    if (!destInfo) {
      ix.push(createAssociatedTokenAccountInstruction(
        relayer.publicKey, // payer
        destATA,
        destPubkey,
        mintPubkey
      ));
    }

    // transferChecked instruction
    ix.push(createTransferCheckedInstruction(
      sourceATA,
      mintPubkey,
      destATA,
      relayer.publicKey,
      Number(amountRaw), // note: JS Number may not cover huge amounts; here amounts small
      MINT_DECIMALS
    ));

    // build transaction
    const tx = new Transaction().add(...ix);
    tx.feePayer = relayer.publicKey;
    tx.recentBlockhash = (await connection.getLatestBlockhash()).blockhash;

    // sign
    tx.sign(relayer);
    const raw = tx.serialize();
    const txid = await connection.sendRawTransaction(raw, { skipPreflight: false });
    await connection.confirmTransaction(txid, 'confirmed');

    console.log('Sent tx', txid, 'to', userAddress, 'amountRaw=', amountRaw.toString());
    return res.json({ success:true, txHash: txid });
  } catch (e) {
    console.error('Claim error', e);
    return res.status(500).json({ success:false, error: String(e) });
  }
});

const port = process.env.PORT || 3000;
app.listen(port, ()=> console.log(`Server running on http://localhost:${port}`));
