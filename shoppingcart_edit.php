<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once "config.php";
include_once "library.php";

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Lấy mã sản phẩm từ URL
$code = $_GET['code'] ?? null;

// Nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newQty = intval($_POST['quantity']);

    if (isset($_SESSION['cart'][$code])) {
        if ($newQty > 0) {
            $_SESSION['cart'][$code]['quantity'] = $newQty;
        } else {
            // Nếu số lượng bằng 0 thì xóa khỏi giỏ
            unset($_SESSION['cart'][$code]);
        }
    }

    // Quay lại giỏ hàng
    Redirect("index.php?do=shoppingcart");
    exit;
}

// Nếu không tìm thấy sản phẩm
if (!$code || !isset($_SESSION['cart'][$code])) {
    Redirect("index.php?do=shoppingcart");
    exit;
}

$item = $_SESSION['cart'][$code];
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">

    <!--Bootstrap-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
	<!--Jquery-->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="scripts/ckeditor/ckeditor.js"></script>
	<!-- Ckeditor -->
	<script src="ckeditor/ckeditor.js"></script>

	<link rel="stylesheet" type="text/css" href="./css/style.css" />
    <title>Edit Cart Item</title>
  
</head>

<body>
    <div class="container mt-5">
        <div class="card-header">My Shopping Cart</div>
        
        <form method="POST" class="form">
            <div class="mb-3">
                <label class="form-label">Product</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($item['name']) ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input type="number" name="quantity" class="form-control" value="<?= $item['quantity'] ?>" min="0">
                <div class="form-text">Set quantity to 0 to remove item from cart.</div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="cart.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>

</html>
