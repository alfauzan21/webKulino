<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['simpan'])) {
    $title = mysqli_real_escape_string($koneksi, $_POST['title']);
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);
    $game_url = mysqli_real_escape_string($koneksi, $_POST['game_url']);
    $badge = mysqli_real_escape_string($koneksi, $_POST['badge']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];

    // Upload image
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $ext = pathinfo($image, PATHINFO_EXTENSION);
        $imageName = time() . '_img.' . $ext;
        move_uploaded_file($tmp, "../assets/" . $imageName);
    }

    // Upload hover video (optional)
    $videoName = null;
    if (!empty($_FILES['video_hover']['name'])) {
        $video = $_FILES['video_hover']['name'];
        $tmp_video = $_FILES['video_hover']['tmp_name'];
        $ext_video = pathinfo($video, PATHINFO_EXTENSION);
        $videoName = time() . '_video.' . $ext_video;
        move_uploaded_file($tmp_video, "../assets/video/" . $videoName);
    }

    $sql = "INSERT INTO tb_games (title, description, image, video_hover, game_url, badge, is_featured, is_active, sort_order) 
            VALUES ('$title', '$description', '$imageName', '$videoName', '$game_url', '$badge', $is_featured, $is_active, $sort_order)";
    
    if (mysqli_query($koneksi, $sql)) {
        header("Location: games-index.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Game</title>
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

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #667eea);
            border: none;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-check-label {
            font-weight: 500;
        }
    </style>
</head>

<body>
    <?php $currentPage = basename($_SERVER['PHP_SELF']); ?>
    <?php include("../includes/sidebar.php"); ?>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-plus-circle"></i> Tambah Game Baru</h3>
            <?php include("../includes/dark-mode.php"); ?>
        </div>

        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php } ?>

        <div class="card">
            <div class="card-body p-4">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Title Game *</label>
                                <input type="text" name="title" class="form-control" placeholder="Contoh: Free Fire" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea name="description" rows="4" class="form-control" placeholder="Epic battle royale game..." required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Game URL / ID *</label>
                                <input type="text" name="game_url" class="form-control" placeholder="blox-d" required>
                                <small class="text-muted">URL atau ID game yang akan diakses</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Badge (Optional)</label>
                                <select name="badge" class="form-select">
                                    <option value="">-- No Badge --</option>
                                    <option value="New">New</option>
                                    <option value="Hot">Hot</option>
                                    <option value="Top Rated">Top Rated</option>
                                    <option value="Updated">Updated</option>
                                    <option value="Popular">Popular</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Upload Image *</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <small class="text-muted">Recommended: 800x450px (16:9 ratio)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload Hover Video (Optional)</label>
                                <input type="file" name="video_hover" class="form-control" accept="video/*">
                                <small class="text-muted">Video akan play saat hover (Featured games only)</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="0" min="0">
                                <small class="text-muted">Urutan tampilan (semakin kecil semakin depan)</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" checked>
                                    <label class="form-check-label" for="isFeatured">
                                        <i class="bi bi-star-fill text-warning"></i> Featured Game
                                    </label>
                                    <small class="d-block text-muted">Tampil di section Featured Games</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" checked>
                                    <label class="form-check-label" for="isActive">
                                        <i class="bi bi-check-circle-fill text-success"></i> Active
                                    </label>
                                    <small class="d-block text-muted">Non-aktif = Coming Soon</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="games-index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Kembali
                        </a>
                        <button type="submit" name="simpan" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Simpan Game
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>