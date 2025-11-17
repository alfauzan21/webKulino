<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $link = mysqli_real_escape_string($koneksi, $_POST['link']);
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];

    if ($gambar) {
        $ext = pathinfo($gambar, PATHINFO_EXTENSION);
        $newname = time() . '.' . $ext;
        move_uploaded_file($tmp, "../uploads/" . $newname);
        mysqli_query($koneksi, "INSERT INTO tb_sponsor (nama, link, gambar) VALUES ('$nama','$link','$newname')");
        header("Location: sponsor-index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Sponsor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 220px;
            background: linear-gradient(135deg, #667eea, #667eea);
            color: white;
            padding-top: 20px;
        }

        .sidebar img {
            width: 80px;
            margin-bottom: 15px;
            border-radius: 12px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            transition: 0.3s;
            border-radius: 8px;
            margin: 5px 10px;
            font-weight: 500;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.2);
            font-weight: 600;
        }

        .content {
            margin-left: 240px;
            padding: 40px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #667eea);
            border: none;
        }
    </style>
</head>

<body>

    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

    <?php
    include("../includes/koneksi.php");
    include("../includes/sidebar.php");
    ?>


    <div class="content">
        <h3 class="fw-bold mb-4"><i class="bi bi-person-plus"></i> Tambah Sponsor</h3>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Sponsor</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link Sponsor</label>
                        <input type="url" name="link" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Gambar</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" name="simpan" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="sponsor-index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>