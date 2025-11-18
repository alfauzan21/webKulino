<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

$error = "";
$success = "";

if (isset($_POST['simpan'])) {
    $title = mysqli_real_escape_string($koneksi, trim($_POST['title']));
    $description = mysqli_real_escape_string($koneksi, trim($_POST['description']));
    $game_url = mysqli_real_escape_string($koneksi, trim($_POST['game_url']));
    $badge = mysqli_real_escape_string($koneksi, trim($_POST['badge']));
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];

    // Validasi input required
    if (empty($title) || empty($description) || empty($game_url)) {
        $error = "Title, Description, and Game URL are required!";
    } else {
        // Upload image
        $imageName = null;
        if (!empty($_FILES['image']['name'])) {
            $image = $_FILES['image']['name'];
            $tmp = $_FILES['image']['tmp_name'];
            $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (!in_array($ext, $allowed)) {
                $error = "Invalid image format! Allowed: JPG, PNG, GIF, WEBP";
            } else {
                $imageName = time() . '_img.' . $ext;
                $uploadPath = "../assets/" . $imageName;
                
                if (!move_uploaded_file($tmp, $uploadPath)) {
                    $error = "Failed to upload image!";
                }
            }
        }

        // Upload hover video (optional)
        $videoName = null;
        if (!empty($_FILES['video_hover']['name']) && empty($error)) {
            $video = $_FILES['video_hover']['name'];
            $tmp_video = $_FILES['video_hover']['tmp_name'];
            $ext_video = strtolower(pathinfo($video, PATHINFO_EXTENSION));
            $allowed_video = ['mp4', 'webm', 'ogg'];
            
            if (!in_array($ext_video, $allowed_video)) {
                $error = "Invalid video format! Allowed: MP4, WEBM, OGG";
            } else {
                // Buat folder video jika belum ada
                if (!is_dir("../assets/video")) {
                    mkdir("../assets/video", 0755, true);
                }
                
                $videoName = time() . '_video.' . $ext_video;
                $uploadPath = "../assets/video/" . $videoName;
                
                if (!move_uploaded_file($tmp_video, $uploadPath)) {
                    $error = "Failed to upload video!";
                }
            }
        }

        // Insert ke database jika tidak ada error
        if (empty($error)) {
            $sql = "INSERT INTO tb_games (title, description, image, video_hover, game_url, badge, is_featured, is_active, sort_order, created_at) 
                    VALUES ('$title', '$description', '$imageName', '$videoName', '$game_url', '$badge', $is_featured, $is_active, $sort_order, NOW())";
            
            if (mysqli_query($koneksi, $sql)) {
                $_SESSION['success_message'] = "Game berhasil ditambahkan!";
                header("Location: games-index.php");
                exit;
            } else {
                $error = "Database Error: " . mysqli_error($koneksi);
            }
        }
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
    <link href="../includes/sidebar.css" rel="stylesheet">
</head>

<body>
    <?php 
    $currentPage = basename($_SERVER['PHP_SELF']); 
    include("../includes/sidebar.php"); 
    ?>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="bi bi-plus-circle"></i> Tambah Game Baru</h3>
            <?php include("../includes/dark-mode.php"); ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-body p-4">
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Title Game <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Contoh: Free Fire" required value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : '' ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="4" class="form-control" placeholder="Epic battle royale game..." required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Game URL / ID <span class="text-danger">*</span></label>
                                <input type="text" name="game_url" class="form-control" placeholder="blox-d" required value="<?= isset($_POST['game_url']) ? htmlspecialchars($_POST['game_url']) : '' ?>">
                                <small class="text-muted">URL atau ID game yang akan diakses</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Badge (Optional)</label>
                                <select name="badge" class="form-select">
                                    <option value="">-- No Badge --</option>
                                    <option value="New" <?= (isset($_POST['badge']) && $_POST['badge'] == 'New') ? 'selected' : '' ?>>New</option>
                                    <option value="Hot" <?= (isset($_POST['badge']) && $_POST['badge'] == 'Hot') ? 'selected' : '' ?>>Hot</option>
                                    <option value="Top Rated" <?= (isset($_POST['badge']) && $_POST['badge'] == 'Top Rated') ? 'selected' : '' ?>>Top Rated</option>
                                    <option value="Updated" <?= (isset($_POST['badge']) && $_POST['badge'] == 'Updated') ? 'selected' : '' ?>>Updated</option>
                                    <option value="Popular" <?= (isset($_POST['badge']) && $_POST['badge'] == 'Popular') ? 'selected' : '' ?>>Popular</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Upload Image <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <small class="text-muted">Recommended: 800x450px (16:9 ratio). Max 5MB</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Upload Hover Video (Optional)</label>
                                <input type="file" name="video_hover" class="form-control" accept="video/*">
                                <small class="text-muted">Video akan play saat hover (Featured games only). Max 10MB</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="<?= isset($_POST['sort_order']) ? $_POST['sort_order'] : '0' ?>" min="0">
                                <small class="text-muted">Urutan tampilan (semakin kecil semakin depan)</small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" <?= (isset($_POST['is_featured']) || !isset($_POST['simpan'])) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isFeatured">
                                        <i class="bi bi-star-fill text-warning"></i> Featured Game
                                    </label>
                                    <small class="d-block text-muted">Tampil di section Featured Games</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?= (isset($_POST['is_active']) || !isset($_POST['simpan'])) ? 'checked' : '' ?>>
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
