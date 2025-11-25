<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid ID!";
    header("Location: marketplace-index.php");
    exit;
}

$id = (int) $_GET['id'];
$sql = mysqli_query($koneksi, "SELECT * FROM tb_marketplace WHERE id=$id");
$data = mysqli_fetch_assoc($sql);

if (!$data) {
    $_SESSION['error_message'] = "Product not found!";
    header("Location: marketplace-index.php");
    exit;
}

$error = "";

if (isset($_POST['update'])) {
    $product_name = mysqli_real_escape_string($koneksi, trim($_POST['product_name']));
    $category = mysqli_real_escape_string($koneksi, $_POST['category']);
    $subcategory = mysqli_real_escape_string($koneksi, $_POST['subcategory']);
    $description = mysqli_real_escape_string($koneksi, trim($_POST['description']));
    $price = floatval($_POST['price']);
    $original_price = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : NULL;
    $stock = intval($_POST['stock']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

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
            if ($data['image'] && file_exists("../uploads/marketplace/" . $data['image'])) {
                unlink("../uploads/marketplace/" . $data['image']);
            }
            
            $imageName = time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = "../uploads/marketplace/" . $imageName;
            
            if (!move_uploaded_file($tmp, $uploadPath)) {
                $error = "Failed to upload image!";
            }
        }
    }

    if (empty($error)) {
        $original_price_sql = $original_price !== NULL ? $original_price : 'NULL';
        
        $update = "UPDATE tb_marketplace SET 
                   product_name='$product_name', 
                   category='$category', 
                   subcategory='$subcategory', 
                   description='$description', 
                   price=$price, 
                   original_price=$original_price_sql, 
                   image='$imageName', 
                   stock=$stock, 
                   is_active=$is_active,
                   updated_at=NOW()
                   WHERE id=$id";
        
        if (mysqli_query($koneksi, $update)) {
            $_SESSION['success_message'] = "Product updated successfully!";
            header("Location: marketplace-index.php");
            exit;
        } else {
            $error = "Database Error: " . mysqli_error($koneksi);
        }
    }
}

$categories = [
    'Aksesoris' => ['Baju', 'Ganci', 'Topi', 'Celana', 'Gelas'],
    'Board Game' => ['Monopoly', 'Ular Tangga']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Product - Marketplace</title>
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
            <h3 class="fw-bold"><i class="bi bi-pencil-square"></i> Edit Product</h3>
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
                                <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" class="form-control" 
                                       value="<?= htmlspecialchars($data['product_name']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" id="category" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="Aksesoris" <?= $data['category'] == 'Aksesoris' ? 'selected' : '' ?>>Aksesoris</option>
                                    <option value="Board Game" <?= $data['category'] == 'Board Game' ? 'selected' : '' ?>>Board Game</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sub-Category <span class="text-danger">*</span></label>
                                <select name="subcategory" id="subcategory" class="form-select" required>
                                    <!-- Will be populated by JavaScript -->
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="4" class="form-control" required><?= htmlspecialchars($data['description']) ?></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Image</label><br>
                                <?php if ($data['image'] && file_exists("../uploads/marketplace/" . $data['image'])): ?>
                                    <img src="../uploads/marketplace/<?= htmlspecialchars($data['image']) ?>" 
                                         width="200" class="mb-3 img-thumbnail"><br>
                                <?php else: ?>
                                    <p class="text-muted">No image</p>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" 
                                       value="<?= $data['price'] ?>" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Original Price (Optional)</label>
                                <input type="number" name="original_price" class="form-control" 
                                       value="<?= $data['original_price'] ?>" min="0">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" 
                                       value="<?= $data['stock'] ?>" min="0" required>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" 
                                           id="isActive" <?= $data['is_active'] ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="isActive">
                                        <i class="bi bi-check-circle-fill text-success"></i> Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-between">
                        <a href="marketplace-index.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left-circle"></i> Back
                        </a>
                        <button type="submit" name="update" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const categories = <?= json_encode($categories) ?>;
        const currentCategory = "<?= $data['category'] ?>";
        const currentSubcategory = "<?= $data['subcategory'] ?>";
        
        function updateSubcategories() {
            const category = document.getElementById('category').value;
            const subcategorySelect = document.getElementById('subcategory');
            
            subcategorySelect.innerHTML = '<option value="">-- Select Sub-Category --</option>';
            
            if (category && categories[category]) {
                subcategorySelect.disabled = false;
                categories[category].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.textContent = sub;
                    if (sub === currentSubcategory) {
                        option.selected = true;
                    }
                    subcategorySelect.appendChild(option);
                });
            } else {
                subcategorySelect.disabled = true;
            }
        }
        
        // Initialize on load
        updateSubcategories();
        
        document.getElementById('category').addEventListener('change', updateSubcategories);
    </script>
</body>
</html>