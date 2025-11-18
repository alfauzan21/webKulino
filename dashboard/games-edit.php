<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "ID tidak valid!";
    header("Location: games-index.php");
    exit;
}

$id = (int) $_GET['id'];

// Ambil data game
$sql = mysqli_query($koneksi, "SELECT * FROM tb_games WHERE id=$id");
$data = mysqli_fetch_assoc($sql);

if (!$data) {
    $_SESSION['error_message'] = "Game tidak ditemukan!";
    header("Location: games-index.php");
    exit;
}

$error = "";

// Proses update
if (isset($_POST['update'])) {
    $title = mysqli_real_escape_string($koneksi, trim($_POST['title']));
    $description = mysqli_real_escape_string($koneksi, trim($_POST['description']));
    $game_url = mysqli_real_escape_string($koneksi, trim($_POST['game_url']));
    $badge = mysqli_real_escape_string($koneksi, trim($_POST['badge']));
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];

    // Handle image upload
    $imageName = $data['image'];
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($ext, $allowed)) {
            $error = "Invalid image format!";
        } else {
            // Delete old image
            if ($data['image'] && file_exists("../assets/" . $data['image'])) {
                unlink("../assets/" . $data['image']);
            }
            
            $imageName = time() . '_img.' . $ext;
            if (!move_uploaded_file($tmp, "../assets/" . $imageName)) {
                $error = "Failed to upload image!";
            }
        }
    }

    // Handle video upload
    $videoName = $data['video_hover'];
    if (!empty($_FILES['video_hover']['name']) && empty($error)) {
        $video = $_FILES['video_hover']['name'];
        $tmp_video = $_FILES['video_hover']['tmp_name'];
        $ext_video = strtolower(pathinfo($video, PATHINFO_EXTENSION));
        $allowed_video = ['mp4', 'webm', 'ogg'];
        
        if (!in_array($ext_video, $allowed_video)) {
            $error = "Invalid video format!";
        } else {
            // Delete old video
            if ($data['video_hover'] && file_exists("../assets/video/" . $data['video_hover'])) {
                unlink("../assets/video/" . $data['video_hover']);
            }
            
            $videoName = time() . '_video.' . $ext_video;
            if (!move_uploaded_file($tmp_video, "../assets/video/" . $videoName)) {
                $error = "Failed to upload video!";
            }
        }
    }

    // Update database if no errors
    if (empty($error)) {
        $update = "UPDATE tb_games SET 
                   title='$title', 
                   description='$description', 
                   image='$imageName', 
                   video_hover='$videoName', 
                   game_url='$game_url', 
                   badge='$badge', 
                   is_featured=$is_featured, 
                   is_active=$is_active, 
                   sort_order=$sort_order,
                   updated_at=NOW()
                   WHERE id=$id";
        
        if (mysqli_query($koneksi, $update)) {
            $_SESSION['success_message'] = "Game berhasil diupdate!";
            header("Location: games-index.php");
            exit;
        } else {
            $error = "Database Error: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Game</title>
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
            <h3 class="fw-bold"><i class="bi bi-pencil-square"></i> Edit Game</h3>
            <?php include("../includes/dark-mode.php"); ?>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
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
                                <label class="form-label">Title Game</label>
                                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($data['title']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" rows="4" class="form-control" required><?= htmlspecialchars($data['description']) ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Game URL / ID</label>
                                <input type="text" name="game_url" class="form-control" value="<?= htmlspecialchars($data['game_url']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Badge</label>
                                <select name="badge" class="form-select">
                                    <option value="">-- No Badge --</option>
                                    <option value="New" <?= $data['badge'] == 'New' ? 'selected' : '' ?>>New</option>
                                    <option value="Hot" <?= $data['badge'] == 'Hot' ? 'selected' : '' ?>>Hot</option>
                                    <option value="Top Rated" <?= $data['badge'] == 'Top Rated' ? 'selected' : '' ?>>Top Rated</option>
                                    <option value="Updated" <?= $data['badge'] == 'Updated' ? 'selected' : '' ?>>Updated</option>
                                    <option value="Popular" <?= $data['badge'] == 'Popular' ? 'selected' : '' ?>>Popular</option>
                                </select>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Image</label><br>
                                <?php if ($data['image'] && file_exists("../assets/" . $data['image'])): ?>
                                    <img src="../assets/<?= htmlspecialchars($data['image']) ?>" width="200" class="mb-3 img-thumbnail"><br>
                                <?php else: ?>
                                    <p class="text-muted fst-italic">No image</p>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Hover Video (Optional)</label><br>
                                <?php if ($data['video_hover']): ?>
                                    <span class="badge bg-info mb-2">Current: <?= htmlspecialchars($data['video_hover']) ?></span><br>
                                <?php endif; ?>
                                <input type="file" name="video_hover" class="form-control" accept="video/*">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="<?= $data['sort_order'] ?>" min="0">
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_featured" id="isFeatured" <?= $data['is_featured'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isFeatured">
                                        <i class="bi bi-star-fill text-warning"></i> Featured Game
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="isActive" <?= $data['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isActive">
                                        <i class="bi bi-check-circle-fill text-success"></i> Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="games-index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Kembali
                        </a>
                        <button type="submit" name="update" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
