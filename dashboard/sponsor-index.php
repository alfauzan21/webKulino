<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Sponsor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        .table thead {
            background: linear-gradient(135deg, #0d6efd, #667eea);
            color: white;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f3f5;
            transition: 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            border: none;
        }

        .btn-warning {
            background: #0d6efd;
            border: none;
            color: #fff;
        }

        .btn-danger {
            background: #dc3545;
            border: none;
        }

        .img-thumbnail {
            border-radius: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
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
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-people"></i> Data Sponsor</h2>
            <?php include("../includes/dark-mode.php"); ?>
        </div>

        <div class="card shadow-lg">
            <div class="card-body p-4">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Nama Sponsor</th>
                            <th>Link</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $sql = mysqli_query($koneksi, "SELECT * FROM tb_sponsor ORDER BY id DESC");
                        if (mysqli_num_rows($sql) > 0) {
                            while ($data = mysqli_fetch_array($sql)) {
                        ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <img src="../uploads/<?= $data['gambar'] ?>" class="img-thumbnail" width="100">
                                    </td>
                                    <td class="fw-semibold text-primary"><?= htmlspecialchars($data['nama']) ?></td>
                                    <td>
                                        <a href="<?= htmlspecialchars($data['link']) ?>" target="_blank" class="text-decoration-none">
                                            <?= htmlspecialchars($data['link']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <a href="sponsor-edit.php?id=<?= $data['id'] ?>" class="btn btn-sm btn-warning shadow-sm">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="sponsor-hapus.php?id=<?= $data['id'] ?>"
                                            class="btn btn-sm btn-danger shadow-sm btn-delete">
                                            <i class="bi bi-trash3"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="5" class="text-muted fst-italic">Belum ada sponsor.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const deleteButtons = document.querySelectorAll(".btn-delete");

            deleteButtons.forEach(button => {
                button.addEventListener("click", function(e) {
                    e.preventDefault();
                    const url = this.getAttribute("href");

                    Swal.fire({
                        title: "Apakah kamu yakin?",
                        text: "Sponsor yang dihapus tidak bisa dikembalikan!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Ya, hapus!",
                        cancelButtonText: "Batal",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = url;
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>