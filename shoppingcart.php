<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "config.php";
include_once "library.php";

$items = [];

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý xóa sản phẩm khỏi giỏ
if (isset($_GET['remove'])) {
    $code = $_GET['remove'];
    unset($_SESSION['cart'][$code]);
    Redirect("index.php?do=shoppingcart");
    exit;
}

/*if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected'])) {
    $selectedCodes = $_POST['selected'];
    $_SESSION['selected_cart'] = [];

    // Duyệt toàn bộ giỏ hàng
    foreach ($_SESSION['cart'] as $code => $item) {
        if (in_array($code, $selectedCodes)) {
            // Các sản phẩm được chọn → thêm vào selected_cart
            $_SESSION['selected_cart'][$code] = $item;
        }
    }

    // Giữ lại sản phẩm KHÔNG được chọn trong giỏ
    foreach ($selectedCodes as $code) {
        unset($_SESSION['cart'][$code]);
    }

    // Điều hướng sang trang checkout
    Redirect("index.php?do=checkout");
    exit;
}*/
// Khi người dùng bấm "Checkout Selected"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected'])) {
    $selectedCodes = $_POST['selected'];
    $_SESSION['selected_cart'] = [];

    foreach ($_SESSION['cart'] as $code => $item) {
        if (in_array($code, $selectedCodes)) {
            $_SESSION['selected_cart'][$code] = $item;
        }
    }

    foreach ($_SESSION['cart'] as $code => $item) {
        if (in_array($code, $selectedCodes)) {
            $_SESSION['selected_cart'][$code] = $item;
            unset($_SESSION['cart'][$code]); // xóa khỏi cart nếu đã chuyển sang selected_cart
        }
    }

    Redirect("index.php?do=checkout");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./css/style.css" />
    <title>My Shopping Cart</title>
</head>

<body>
    <div class="container mt-4">
        <div class="card-header"><i class="bi bi-cart4"></i> My Shopping Cart</div>

        <?php if (empty($_SESSION['cart'])): ?>
            <div class="alert alert-warning">Your cart is currently empty.</div>
            <a href="index.php?do=products" class="btn btn-outline-primary ms-2"><i class="bi bi-box-arrow-left"></i> Continue Shopping</a>
        <?php else: ?>
            <form method="post" class="form">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th><input type="checkbox" id="select-all" /></th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($_SESSION['cart'] as $code => $item):
                                $subtotal = $item['price'] * $item['quantity'];
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td><input type="checkbox" name="selected[]" value="<?= htmlspecialchars($code) ?>"></td>
                                    <td><?= htmlspecialchars($item['name']) ?></td>
                                    <td><?= number_format($item['price']) ?>₫</td>
                                    <td><?= $item['quantity'] ?></td>
                                    <td><?= number_format($subtotal) ?>₫</td>
                                    <td>
                                        <a href="index.php?do=shoppingcart_edit&code=<?= urlencode($code) ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="index.php?do=shoppingcart&remove=<?= urlencode($code) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove this item?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Total</td>
                                <td colspan="2" class="fw-bold"><?= number_format($total) ?>₫</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <button type="submit" class="btn btn-success"><i class="bi bi-cash-coin"></i> Checkout Selected</button>

                        <a href="index.php?do=products" class="btn btn-outline-primary ms-2"><i class="bi bi-box-arrow-left"></i> Continue Shopping</a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        document.getElementById("select-all").addEventListener("click", function() {
            const checkboxes = document.querySelectorAll('input[name="selected[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>

</html>