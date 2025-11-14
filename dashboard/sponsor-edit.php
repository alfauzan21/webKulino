<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'];
$data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_sponsor WHERE id=$id"));

if (isset($_POST['update'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $link = mysqli_real_escape_string($koneksi, $_POST['link']);
    $gambar = $_FILES['gambar']['name'];

    if ($gambar) {
        // hapus gambar lama
        if (!empty($data['gambar']) && file_exists("../uploads/" . $data['gambar'])) {
            unlink("../uploads/" . $data['gambar']);
        }

        $ext = pathinfo($gambar, PATHINFO_EXTENSION);
        $newname = time() . '.' . $ext;
        move_uploaded_file($_FILES['gambar']['tmp_name'], "../uploads/" . $newname);
    } else {
        $newname = $data['gambar'];
    }

    mysqli_query($koneksi, "UPDATE tb_sponsor SET nama='$nama', link='$link', gambar='$newname' WHERE id=$id");
    header("Location: sponsor-index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Sponsor</title>
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
    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>

    <?php
    include("../includes/koneksi.php");
    include("../includes/sidebar.php");
    ?>


    <!-- Content -->
    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-pencil-square"></i> Edit Sponsor</h3>
            <?php include("../includes/dark-mode.php"); ?>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Nama Sponsor</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($data['nama']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Link Sponsor</label>
                        <input type="url" name="link" value="<?= htmlspecialchars($data['link']) ?>" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gambar Sponsor</label><br>
                        <img src="../uploads/<?= htmlspecialchars($data['gambar']) ?>" width="100" class="rounded mb-2 border">
                        <input type="file" name="gambar" class="form-control mt-2" accept="image/*">
                    </div>

                    <button type="submit" name="update" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Perubahan
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