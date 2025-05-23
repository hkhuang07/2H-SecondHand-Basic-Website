<?php
// Nếu chưa có session nào được khởi động, thì khởi động session mới
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp file cấu hình kết nối CSDL
require_once "config.php";

// Nạp các hàm tiện ích có thể dùng chung
require_once "library.php";

// Lấy từ khóa tìm kiếm từ URL, nếu không có thì mặc định là chuỗi rỗng
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

// Khởi tạo mảng rỗng để chứa danh sách sản phẩm
$products = [];

if ($keyword !== '') {
    // Nếu có từ khóa tìm kiếm thì thực hiện câu truy vấn có điều kiện lọc theo tên sản phẩm
    $sql = "
        SELECT p.*, c.CategoryName 
        FROM tbl_products p
        LEFT JOIN tbl_categories c ON p.CategoryID = c.CategoryID
        WHERE p.ProductName LIKE ?
        ORDER BY p.ProductID DESC
    ";

    // Chuẩn bị câu lệnh truy vấn để tránh SQL injection
    $stmt = $connect->prepare($sql);

    // Thêm ký tự % để tìm kiếm từ khóa nằm bất kỳ đâu trong tên sản phẩm
    $likeKeyword = '%' . $keyword . '%';

    // Gán tham số vào câu truy vấn với kiểu 's' là string
    $stmt->bind_param('s', $likeKeyword);

    // Thực thi truy vấn
    $stmt->execute();

    // Lấy kết quả truy vấn
    $result = $stmt->get_result();

    // Lấy tất cả dữ liệu thành mảng kết hợp
    $products = $result->fetch_all(MYSQLI_ASSOC);

    // Đóng đối tượng statement để giải phóng tài nguyên
    $stmt->close();
} else {
    // Nếu không có từ khóa tìm kiếm thì lấy toàn bộ sản phẩm
    $sql = "
        SELECT p.*, c.CategoryName 
        FROM tbl_products p
        LEFT JOIN tbl_categories c ON p.CategoryID = c.CategoryID
        ORDER BY p.ProductID DESC
    ";

    // Thực thi truy vấn không có điều kiện
    $result = $connect->query($sql);

    // Lấy tất cả sản phẩm
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!-- Giao diện kết quả tìm kiếm -->
<div class="container mt-4">
    <!-- Tiêu đề kết quả tìm kiếm -->
    <div class="card-header">
        Search results for: <strong><?= htmlspecialchars($keyword) ?: 'All Products' ?></strong>
    </div>

    <!-- Nếu có sản phẩm tìm thấy -->
    <?php if (count($products) > 0): ?>
        <div class="row">
            <!-- Lặp qua từng sản phẩm và hiển thị -->
            <?php foreach ($products as $product): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm">
                        <!-- Hình ảnh sản phẩm -->
                        <img src="images/<?= htmlspecialchars($product['Image']) ?>" class="card-img-top" style="height: 180px; object-fit: cover;">

                        <div class="card-body d-flex flex-column">
                            <!-- Tên sản phẩm -->
                            <h5 class="card-title"><?= htmlspecialchars($product['ProductName']) ?></h5>

                            <!-- Thông tin thêm -->
                            <p class="card-text text-muted mb-1">
                                <strong>Code:</strong> <?= htmlspecialchars($product['ProductCode']) ?><br>
                                <strong>Category:</strong> <?= htmlspecialchars($product['CategoryName']) ?><br>
                                <strong>Price:</strong> $<?= number_format($product['Price'], 2) ?>
                            </p>

                            <!-- Trạng thái còn hàng -->
                            <p class="mb-2">
                                <?php if ($product['Status'] == 1): ?>
                                    <span class="badge bg-success">In stock</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of stock</span>
                                <?php endif; ?>
                            </p>

                            <!-- Nút chức năng -->
                            <div class="mt-auto">
                                <!-- Thêm vào giỏ hàng -->
                                <a href="index.php?do=shoppingcart_add&id=<?= $product['ProductID'] ?>" class="btn btn-success btn-sm mb-1 w-100">
                                    <i class="bi bi-cart-plus"></i> Add to Cart
                                </a>

                                <!-- Xem chi tiết -->
                                <a href="index.php?do=product_details&id=<?= $product['ProductID'] ?>" class="btn btn-outline-primary btn-sm mb-1 w-100">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Nếu không có sản phẩm nào -->
        <div class="alert alert-warning mt-3">No products found.</div>
    <?php endif; ?>
</div>
