<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $judul     = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link      = mysqli_real_escape_string($koneksi, $_POST['link']);

    // Upload gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    $newname = null;
    if (!empty($gambar)) {
        $ext = pathinfo($gambar, PATHINFO_EXTENSION);
        $newname = time() . '.' . $ext;
        if (!move_uploaded_file($tmp, "../uploads/" . $newname)) {
            die("Upload gambar gagal!");
        }
    }

    $sql = "INSERT INTO tb_berita (judul, deskripsi, link, gambar) 
            VALUES ('$judul','$deskripsi','$link','$newname')";
    $query = mysqli_query($koneksi, $sql);

    if ($query) {
        header("Location: index.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Berita</title>
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

        .sidebar h4 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
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

        .card {
            border-radius: 15px;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #667eea);
            border: none;
        }

        .btn-secondary {
            border: none;
        }
    </style>
</head>

<body>
    <?php
    $currentPage = basename($_SERVER['PHP_SELF']);
    ?>

    <?php
    include("../includes/koneksi.php");
    include("../includes/sidebar.php");
    ?>


    <!-- Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-file-earmark-plus"></i> Tambah Berita</h3>
            <?php include("../includes/dark-mode.php"); ?>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Judul Berita</label>
                        <input type="text" name="judul" class="form-control" placeholder="Masukkan judul berita..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi" rows="5" class="form-control" placeholder="Tulis deskripsi berita..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Gambar</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link Baca Selengkapnya</label>
                        <input type="url" name="link" class="form-control" placeholder="https://contoh.com/artikel" required>
                    </div>

                    <button type="submit" name="simpan" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left-circle"></i> Kembali
                    </a>
                </form>
            </div>
        </div>
    </div>

</body>

</html>