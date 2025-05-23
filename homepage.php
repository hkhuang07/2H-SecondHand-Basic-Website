<?php
// Kiểm tra xem session đã được khởi tạo hay chưa, nếu chưa thì khởi tạo session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nhúng file cấu hình cơ sở dữ liệu và thư viện dùng chung
require_once "config.php";
require_once "library.php";

// Lấy số trang hiện tại từ tham số GET, mặc định là 1 nếu không có hoặc nhỏ hơn 1
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$hotPage = isset($_GET['hot_page']) ? max(1, intval($_GET['hot_page'])) : 1;
$viewPage = isset($_GET['view_page']) ? max(1, intval($_GET['view_page'])) : 1;

// Thiết lập số sản phẩm trên mỗi trang và tính offset để phân trang chính (tất cả sản phẩm)
$pageSize = 12;
$offset = ($page - 1) * $pageSize;

// Thiết lập phân trang cho sản phẩm bán chạy (Best-Selling)
$hotPageSize = 10;
$hotOffset = ($hotPage - 1) * $hotPageSize;

// Thiết lập phân trang cho sản phẩm được xem nhiều nhất (Most Viewed)
$viewPageSize = 6;
$viewOffset = ($viewPage - 1) * $viewPageSize;

// Truy vấn danh sách sản phẩm bán chạy nhất, sắp xếp theo số lần mua giảm dần
$sqlHot = "
    SELECT p.*, c.CategoryName 
    FROM tbl_products p
    LEFT JOIN tbl_categories c ON p.CategoryID = c.CategoryID
    ORDER BY p.PurchaseCount DESC
    LIMIT $hotPageSize OFFSET $hotOffset
";
$hotProducts = $connect->query($sqlHot)->fetch_all(MYSQLI_ASSOC);

// Truy vấn sản phẩm được xem nhiều nhất, sắp xếp theo lượt xem giảm dần
$sqlView = "
    SELECT p.*, c.CategoryName 
    FROM tbl_products p
    LEFT JOIN tbl_categories c ON p.CategoryID = c.CategoryID
    ORDER BY p.View DESC
    LIMIT $viewPageSize OFFSET $viewOffset
";
$viewProducts = $connect->query($sqlView)->fetch_all(MYSQLI_ASSOC);

// Truy vấn tất cả sản phẩm, sắp xếp theo ID giảm dần (sản phẩm mới nhất trước)
$sqlAll = "
    SELECT p.*, c.CategoryName 
    FROM tbl_products p
    LEFT JOIN tbl_categories c ON p.CategoryID = c.CategoryID
    ORDER BY p.ProductID DESC
    LIMIT $pageSize OFFSET $offset
";
$allProducts = $connect->query($sqlAll)->fetch_all(MYSQLI_ASSOC);
?>

<!-- Bắt đầu phần HTML hiển thị giao diện sản phẩm -->
<div class="container mt-4">

    <!-- 🔥 VÙNG 1: Best-Selling Products -->
    <div class="card-header">🔥 Best-Selling Products</div>
    <div class="d-flex overflow-auto mb-2">
        <?php foreach ($hotProducts as $product): ?>
            <!-- Hiển thị từng sản phẩm bán chạy trong khung trượt ngang -->
            <div class="card me-3" style="min-width: 200px; max-width: 200px;">
                <!-- Hiển thị ảnh sản phẩm -->
                <img src="images/<?= htmlspecialchars($product['Image']) ?>" class="card-img-top" style="height: 140px; object-fit: cover;">
                <div class="card-body p-2">
                    <!-- Tên sản phẩm -->
                    <h6 class="card-title"><?= htmlspecialchars($product['ProductName']) ?></h6>
                    <!-- Giá sản phẩm -->
                    <p class="mb-1 text-muted">$<?= number_format($product['Price'], 2) ?></p>
                    <!-- Nút xem chi tiết -->
                    <a href="index.php?do=product_details&id=<?= $product['ProductID'] ?>" class="btn btn-sm btn-primary w-100">View</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Nút xem thêm sản phẩm bán chạy -->
    <div class="text-center mb-4">
        <a href="index.php?do=homepage&hot_page=<?= $hotPage + 1 ?>&page=<?= $page ?>&view_page=<?= $viewPage ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-down-circle"></i> More Best-Sellers
        </a>
    </div>

    <!-- 🛍 VÙNG 2 + 👀 VÙNG 3 -->
    <div class="row">
        <!-- 🛍 VÙNG 2: All Products -->
        <div class="col-md-9">
            <div class="card-header">🛍 All Products</div>
            <div class="row">
                <?php foreach ($allProducts as $product): ?>
                    <!-- Mỗi sản phẩm là 1 cột -->
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <!-- Ảnh sản phẩm -->
                            <img src="images/<?= htmlspecialchars($product['Image']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <!-- Tên sản phẩm -->
                                <h5 class="card-title"><?= htmlspecialchars($product['ProductName']) ?></h5>
                                <!-- Thông tin danh mục và giá -->
                                <p class="card-text text-muted mb-1">
                                    <strong>Category:</strong> <?= htmlspecialchars($product['CategoryName']) ?><br>
                                    <strong>Price:</strong> $<?= number_format($product['Price'], 2) ?>
                                </p>
                                <!-- Nút thêm vào giỏ hàng và xem -->
                                <div class="mt-auto">
                                    <a href="index.php?do=shoppingcart_add&id=<?= $product['ProductID'] ?>" class="btn btn-success btn-sm w-100 mb-1">
                                        <i class="bi bi-cart-plus"></i> Add to Cart
                                    </a>
                                    <a href="index.php?do=product_details&id=<?= $product['ProductID'] ?>" class="btn btn-outline-primary btn-sm w-100">View</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Nút phân trang để xem thêm sản phẩm -->
            <div class="text-center">
                <a href="index.php?do=homepage&page=<?= $page + 1 ?>&hot_page=<?= $hotPage ?>&view_page=<?= $viewPage ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-down-circle"></i> Next Products
                </a>
            </div>
        </div>

        <!-- 👀 VÙNG 3: Most Viewed -->
        <div class="col-md-3">
            <div class="card-header">👀 Most Viewed</div>
            <?php foreach ($viewProducts as $product): ?>
                <!-- Hiển thị danh sách sản phẩm được xem nhiều nhất theo chiều dọc -->
                <div class="card mb-3">
                    <div class="row g-0">
                        <!-- Ảnh nhỏ của sản phẩm -->
                        <div class="col-4">
                            <img src="images/<?= htmlspecialchars($product['Image']) ?>" class="img-fluid" style="height: 60px; object-fit: cover;">
                        </div>
                        <!-- Tên, giá và nút xem -->
                        <div class="col-8">
                            <div class="card-body p-2">
                                <h6 class="card-title mb-1"><?= htmlspecialchars($product['ProductName']) ?></h6>
                                <p class="mb-1 text-muted">$<?= number_format($product['Price'], 2) ?></p>
                                <a href="index.php?do=product_details&id=<?= $product['ProductID'] ?>" class="btn btn-sm btn-outline-primary w-100">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Nút phân trang để xem thêm sản phẩm được xem nhiều -->
            <div class="text-center">
                <a href="index.php?do=homepage&view_page=<?= $viewPage + 1 ?>&page=<?= $page ?>&hot_page=<?= $hotPage ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-down-circle"></i> More Viewed Products
                </a>
            </div>
        </div>
    </div>
</div>
