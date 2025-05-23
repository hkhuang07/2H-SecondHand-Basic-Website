<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "config.php";
include_once "library.php";

// Nếu không có sản phẩm được chọn thì quay về giỏ
if (!isset($_SESSION['selected_cart']) || empty($_SESSION['selected_cart'])) {
    Redirect("index.php?do=shoppingcart");
}
$userData = [
    'name' => $_SESSION["FullName"] ?? '',
    'email' => $_SESSION["Email"] ?? '',
    'phone' => $_SESSION["Phone"] ?? '',
];


// Thông tin người dùng (nếu đã đăng nhập)

if (isset($_SESSION['UserID'])) {
    $stmt = $connect->prepare("SELECT FullName, Email, Phone FROM tbl_users WHERE UserID = ?");
    $stmt->bind_param("i", $_SESSION['UserID']);
    $stmt->execute();
    $stmt->bind_result($userData['name'], $userData['email'], $userData['phone']);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>

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

</head>

<body>
    <div class="container mt-4">
        <div class="card-header">Checkout</div>

        <!-- Thông tin người dùng -->
        <form method="post" action="checkout_process.php" class="form">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($userData['name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($userData['email']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" required value="<?= htmlspecialchars($userData['phone']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2" required></textarea>
                </div>
            </div>

            <!-- Hiển thị giỏ hàng -->
            <div class="card-header">Order Summary</div>
           <table class="table table-bordered">
            <thead>
                <tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($_SESSION['selected_cart'] as $item):
                    $subtotal = $item['price'] * $item['quantity'];
                    $total += $subtotal;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price']) ?>₫</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($subtotal) ?>₫</td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-end fw-bold">Total</td>
                    <td class="fw-bold"><?= number_format($total) ?>₫</td>
                </tr>
            </tbody>
        </table>

        <div class="text-end">
            <button type="submit" class="btn btn-success">Place Order</button>
        </div>
    </form>
    </div>
</body>

</html>