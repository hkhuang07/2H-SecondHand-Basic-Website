<?php
include_once "config.php";
include_once "library.php";

// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đảm bảo giỏ hàng tồn tại
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý khi xác nhận xóa
if (isset($_POST['confirm_delete'])) {
    $code = $_POST['ProductID'];

    // Xóa sản phẩm khỏi giỏ nếu tồn tại
    if (isset($_SESSION['cart'][$code])) {
        unset($_SESSION['cart'][$code]);
        Message("Item removed from cart. Redirecting...");
    } else {
        ErrorMessage("Item not found in cart.");
    }

    Redirect("index.php?do=shoppingcart");
    exit();
}

// Hiển thị form xác nhận nếu có mã sản phẩm từ URL
elseif (isset($_GET['code'])) {
    $code = $_GET['code'];

    // Lấy thông tin sản phẩm từ giỏ hàng để xác nhận
    if (isset($_SESSION['cart'][$code])) {
        $product = $_SESSION['cart'][$code];
        ?>

        <div class="card mt-4">
            <div class="card-body">
                <div class="card-header">Confirm Deletion</div>
                <p>Are you sure you want to remove the item: 
                    <strong><?= htmlspecialchars($product['name']) ?></strong> 
                    from your cart?
                </p>
                <form method="post" class="form">
                    <input type="hidden" name="ProductID" value="<?= htmlspecialchars($code) ?>">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Remove</button>
                    <a href="index.php?do=shoppingcart" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>

        <?php
    } else {
        ErrorMessage("Item not found in your cart!");
        echo '<a href="index.php?do=shoppingcart" class="btn btn-primary mt-2">Back to Cart</a>';
    }
} else {
    Redirect("index.php?do=shoppingcart");
    exit();
}
?>
