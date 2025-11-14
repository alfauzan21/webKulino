<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logout - Kulino</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap"
        rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            filter: brightness(0.6);
        }

        .logout-box {
            width: 380px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.88);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .logout-box img {
            width: 85px;
            margin-bottom: 15px;
        }

        .logout-box h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
        }

        .logout-box p {
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }

        .logout-box a {
            display: inline-block;
            padding: 12px 25px;
            background: #667eea;
            color: #fff;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: 0.3s;
        }

        .logout-box a:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <video autoplay muted loop playsinline>
        <source src="../assets/gif/gif-login.mp4" type="video/mp4" />
    </video>

    <div class="logout-box">
        <img src="../assets/icon/kulino-logo-blue.png" alt="Kulino Logo" />
        <h1>Berhasil Logout</h1>
        <p>Anda sudah keluar dari sistem.</p>
        <a href="login.php">🔑 Login Lagi</a>
    </div>
</body>

</html>