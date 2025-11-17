<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID tidak valid!");
}
$id = (int) $_GET['id'];

// Ambil data lama
$sql = mysqli_query($koneksi, "SELECT * FROM tb_berita WHERE id=$id");
$data = mysqli_fetch_assoc($sql);
if (!$data) {
    die("Data tidak ditemukan!");
}

// Proses update
if (isset($_POST['update'])) {
    $judul     = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $link      = mysqli_real_escape_string($koneksi, $_POST['link']);


    $gambar = $_FILES['gambar']['name'];
    $tmp    = $_FILES['gambar']['tmp_name'];

    if (!empty($gambar)) {
        // Hapus file lama
        if ($data['gambar'] && file_exists("../uploads/" . $data['gambar'])) {
            unlink("../uploads/" . $data['gambar']);
        }
        $ext = pathinfo($gambar, PATHINFO_EXTENSION);
        $newname = time() . '.' . $ext;
        move_uploaded_file($tmp, "../uploads/" . $newname);
    } else {
        $newname = $data['gambar']; // tetap pakai gambar lama
    }

    $update = "UPDATE tb_berita 
               SET judul='$judul', deskripsi='$deskripsi', link='$link', gambar='$newname' 
               WHERE id=$id";
    if (mysqli_query($koneksi, $update)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal update: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Berita</title>
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
            background: linear-gradient(135deg, #0d6efd, #667eea);
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
        }

        .content {
            margin-left: 240px;
            padding: 40px;
        }

        .card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            border: none;
            border-radius: 12px;
        }

        .btn-secondary {
            border-radius: 12px;
        }

        .img-preview {
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>

    <?php
    include("../includes/koneksi.php");
    include("../includes/sidebar.php");
    ?>


    <!-- Content -->
    <div class="content">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card p-4">
                    <h3 class="fw-bold mb-4 text-center text-primary"><i class="bi bi-pencil-square"></i> Edit Berita</h3>
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Judul Berita</label>
                            <input type="text" name="judul" class="form-control" value="<?= htmlspecialchars($data['judul']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" rows="5" class="form-control" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
                        </div>


                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gambar</label><br>
                            <?php if ($data['gambar']) { ?>
                                <img src="../uploads/<?= htmlspecialchars($data['gambar']) ?>" width="150" class="mb-3 img-preview"><br>
                            <?php } ?>
                            <input type="file" name="gambar" class="form-control">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Link Baca Selengkapnya</label>
                            <input type="url" name="link" class="form-control" value="<?= htmlspecialchars($data['link']) ?>" placeholder="https://contoh.com/artikel">
                        </div>


                        <div class="d-flex justify-content-between mt-4">
                            <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left-circle"></i> Kembali</a>
                            <button type="submit" name="update" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>