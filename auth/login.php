<?php
session_start();
include("../includes/koneksi.php");

$error = "";

if (isset($_POST['login'])) {
  $username = mysqli_real_escape_string($koneksi, $_POST['username']);
  $password = md5($_POST['password']); // samakan dengan database

  $query = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username' AND password='$password'");
  if (mysqli_num_rows($query) > 0) {
    $_SESSION['login'] = true;
    $_SESSION['username'] = $username;

    // Tambahkan flash message
    $_SESSION['success_login'] = "Selamat Datang Lino's";

    header("Location: ../dashboard/index.php");
    exit;
  } else {
    $error = "⚠️ Username atau Password salah!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kulino Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet" />
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

    .login-box {
      width: 380px;
      padding: 40px;
      background: rgba(255, 255, 255, 0.88);
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
      text-align: center;
      backdrop-filter: blur(10px);
    }

    .login-box img {
      width: 85px;
      margin-bottom: 15px;
    }

    .login-box h1 {
      font-size: 26px;
      font-weight: 600;
      margin-bottom: 25px;
      color: #333;
    }

    .login-box input {
      width: 100%;
      padding: 14px 16px;
      margin: 10px 0;
      border: none;
      border-radius: 10px;
      background: #f3f3f3;
      font-size: 14px;
      outline: none;
      transition: 0.3s;
    }

    .login-box input:focus {
      background: #e9e9e9;
    }

    .login-box button {
      width: 100%;
      padding: 14px;
      margin-top: 20px;
      background: #667eea;
      color: #fff;
      border: none;
      border-radius: 25px;
      font-size: 15px;
      font-weight: 500;
      cursor: pointer;
      transition: 0.3s;
    }

    .login-box button:hover {
      background: #5a67d8;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .error {
      color: red;
      font-size: 14px;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <video id="bg-video" autoplay muted playsinline>
    <source src="../assets/gif/gif-login1.mp4" type="video/mp4" />
  </video>

  <div class="login-box">
    <img src="../assets/icon/kulino-logo-blue.png" alt="Kulino Logo" />
    <h1>Sign In</h1>
    <?php if ($error) {
      echo "<p class='error'>$error</p>";
    } ?>
    <form method="post">
      <input type="text" name="username" placeholder="Username" required />
      <input type="password" name="password" placeholder="Password" required />
      <button type="submit" name="login">Sign In</button>
    </form>
  </div>
</body>

</html>