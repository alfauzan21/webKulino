<?php
include("../includes/koneksi.php");
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: ../login.php");
    exit;
}

$error = "";

if (isset($_POST['simpan'])) {
    $product_name = mysqli_real_escape_string($koneksi, trim($_POST['product_name']));
    $category = mysqli_real_escape_string($koneksi, $_POST['category']);
    $subcategory = mysqli_real_escape_string($koneksi, $_POST['subcategory']);
    $description = mysqli_real_escape_string($koneksi, trim($_POST['description']));
    $price = floatval($_POST['price']);
    $original_price = !empty($_POST['original_price']) ? floatval($_POST['original_price']) : NULL;
    $stock = intval($_POST['stock']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Upload image
    $imageName = null;
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];
        $ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($ext, $allowed)) {
            $error = "Invalid image format!";
        } else {
            // Create marketplace folder if not exists
            $uploadDir = "../uploads/marketplace/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $imageName = time() . '_' . uniqid() . '.' . $ext;
            $uploadPath = $uploadDir . $imageName;
            
            if (!move_uploaded_file($tmp, $uploadPath)) {
                $error = "Failed to upload image!";
            }
        }
    } else {
        $error = "Image is required!";
    }

    if (empty($error)) {
        $original_price_val = $original_price !== NULL ? $original_price : 'NULL';
        
        $sql = "INSERT INTO tb_marketplace 
                (product_name, category, subcategory, description, price, original_price, image, stock, is_active) 
                VALUES 
                ('$product_name', '$category', '$subcategory', '$description', $price, $original_price_val, '$imageName', $stock, $is_active)";
        
        if (mysqli_query($koneksi, $sql)) {
            $_SESSION['success_message'] = "Product added successfully!";
            header("Location: marketplace-index.php");
            exit;
        } else {
            $error = "Database Error: " . mysqli_error($koneksi);
        }
    }
}

// Category options
$categories = [
    'Aksesoris' => ['Baju', 'Ganci', 'Topi', 'Celana', 'Gelas'],
    'Board Game' => ['Monopoly', 'Ular Tangga']
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Add Product - Marketplace</title>
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
            <h3 class="fw-bold"><i class="bi bi-plus-circle"></i> Add New Product</h3>
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
                                       placeholder="e.g. Kulino T-Shirt Premium" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Category <span class="text-danger">*</span></label>
                                <select name="category" id="category" class="form-select" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="Aksesoris">Aksesoris</option>
                                    <option value="Board Game">Board Game</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Sub-Category <span class="text-danger">*</span></label>
                                <select name="subcategory" id="subcategory" class="form-select" required disabled>
                                    <option value="">-- Select Category First --</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" rows="4" class="form-control" 
                                          placeholder="Product description..." required></textarea>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Product Image <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <small class="text-muted">Recommended: 800x800px. Max 5MB</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price (Rp) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" 
                                       placeholder="150000" min="0" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Original Price (Optional)</label>
                                <input type="number" name="original_price" class="form-control" 
                                       placeholder="200000 (for discount display)" min="0">
                                <small class="text-muted">Leave empty if no discount</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Stock <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" 
                                       value="0" min="0" required>
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" 
                                           id="isActive" checked>
                                    <label class="form-check-label" for="isActive">
                                        <i class="bi bi-check-circle-fill text-success"></i> Active (visible on website)
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
                        <button type="submit" name="simpan" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Save Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dynamic subcategory based on category
        const categories = <?= json_encode($categories) ?>;
        
        document.getElementById('category').addEventListener('change', function() {
            const category = this.value;
            const subcategorySelect = document.getElementById('subcategory');
            
            subcategorySelect.innerHTML = '<option value="">-- Select Sub-Category --</option>';
            
            if (category && categories[category]) {
                subcategorySelect.disabled = false;
                categories[category].forEach(sub => {
                    const option = document.createElement('option');
                    option.value = sub;
                    option.textContent = sub;
                    subcategorySelect.appendChild(option);
                });
            } else {
                subcategorySelect.disabled = true;
            }
        });
    </script>
</body>
</html>