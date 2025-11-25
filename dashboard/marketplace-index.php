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
    <title>Marketplace Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="../includes/sidebar.css" rel="stylesheet">
</head>
<body>
    <?php 
    $currentPage = basename($_SERVER['PHP_SELF']); 
    include("../includes/sidebar.php"); 
    ?>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold"><i class="bi bi-shop"></i> Marketplace Management</h2>
            <div class="d-flex gap-2">
                <?php include("../includes/dark-mode.php"); ?>
                <a href="marketplace-tambah.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Product
                </a>
            </div>
        </div>

        <!-- Category Tabs -->
        <ul class="nav nav-tabs mb-3" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#all-products">All Products</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#aksesoris">Aksesoris</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#boardgame">Board Game</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- All Products -->
            <div class="tab-pane fade show active" id="all-products">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="10%">Image</th>
                                    <th width="15%">Product Name</th>
                                    <th width="10%">Category</th>
                                    <th width="10%">Sub-Category</th>
                                    <th width="10%">Price</th>
                                    <th width="8%">Stock</th>
                                    <th width="8%">Status</th>
                                    <th width="15%">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $sql = mysqli_query($koneksi, "SELECT * FROM tb_marketplace ORDER BY created_at DESC");
                                if (mysqli_num_rows($sql) > 0) {
                                    while ($product = mysqli_fetch_array($sql)) {
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?php if ($product['image']): ?>
                                            <img src="../uploads/marketplace/<?= htmlspecialchars($product['image']) ?>" 
                                                 class="img-thumbnail" width="80">
                                        <?php else: ?>
                                            <span class="text-muted">No image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold text-primary"><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td>
                                        <span class="badge bg-info"><?= htmlspecialchars($product['category']) ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($product['subcategory']) ?></span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="text-success">Rp <?= number_format($product['price'], 0, ',', '.') ?></strong>
                                            <?php if ($product['original_price']): ?>
                                                <br><small class="text-muted text-decoration-line-through">
                                                    Rp <?= number_format($product['original_price'], 0, ',', '.') ?>
                                                </small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($product['stock'] > 0): ?>
                                            <span class="badge bg-success"><?= $product['stock'] ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Out of Stock</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="marketplace-edit.php?id=<?= $product['id'] ?>" 
                                           class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-square"></i> Edit
                                        </a>
                                        <a href="marketplace-hapus.php?id=<?= $product['id'] ?>" 
                                           class="btn btn-sm btn-danger btn-delete">
                                            <i class="bi bi-trash3"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php 
                                    }
                                } else { 
                                ?>
                                <tr>
                                    <td colspan="9" class="text-muted">No products available</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Aksesoris Tab -->
            <div class="tab-pane fade" id="aksesoris">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Sub-Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $sql = mysqli_query($koneksi, "SELECT * FROM tb_marketplace WHERE category='Aksesoris' ORDER BY subcategory, product_name");
                                while ($product = mysqli_fetch_array($sql)) {
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <img src="../uploads/marketplace/<?= htmlspecialchars($product['image']) ?>" 
                                             class="img-thumbnail" width="60">
                                    </td>
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($product['subcategory']) ?></span></td>
                                    <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <a href="marketplace-edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="marketplace-hapus.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger btn-delete">Delete</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Board Game Tab -->
            <div class="tab-pane fade" id="boardgame">
                <div class="card shadow-lg">
                    <div class="card-body p-4">
                        <table class="table table-bordered table-hover align-middle text-center">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th>Sub-Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $sql = mysqli_query($koneksi, "SELECT * FROM tb_marketplace WHERE category='Board Game' ORDER BY subcategory, product_name");
                                while ($product = mysqli_fetch_array($sql)) {
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <img src="../uploads/marketplace/<?= htmlspecialchars($product['image']) ?>" 
                                             class="img-thumbnail" width="60">
                                    </td>
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars($product['subcategory']) ?></span></td>
                                    <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <a href="marketplace-edit.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="marketplace-hapus.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger btn-delete">Delete</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('href');
                
                Swal.fire({
                    title: 'Delete Product?',
                    text: "This action cannot be undone!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
        });
    </script>
</body>
</html>