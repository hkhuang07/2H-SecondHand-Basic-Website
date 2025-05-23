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

// Lấy dữ liệu từ form (POST)
$productID = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'buy'; // Mặc định là 'buy'

// Nếu không có product_id thì thử lấy từ GET 
// Nếu muốn hỗ trợ thêm qua GET, cần kiểm tra kỹ không tự động tăng nếu đã có trong giỏ
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $productID = intval($_GET['id']);
    $action = 'buy';

    if (isset($_SESSION['cart'][$productID])) {
        ErrorMessage("This product is already in the cart. You can edit quantity in cart.");
        Redirect("index.php?do=shoppingcart");
        exit;
    }
}


// Trường hợp không có product ID hợp lệ
if ($productID <= 0) {
    Redirect("index.php?do=products");
    exit;
}

// Lấy thông tin sản phẩm từ CSDL
$stmt = $connect->prepare("SELECT * FROM tbl_products WHERE ProductID = ?");
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if ($product) {
    $code = $product['ProductID']; // Dùng ProductID làm key
    $stock = intval($product['Quantity']); // Số lượng tồn kho

    // Số lượng hiện tại trong giỏ hàng (nếu đã có)
    $currentQty = isset($_SESSION['cart'][$code]) ? $_SESSION['cart'][$code]['quantity'] : 0;
    $newQty = $currentQty + 1;

    // Kiểm tra tồn kho
    if ($product) {
        $code = $product['ProductID']; // Dùng ProductID làm key
        $stock = intval($product['Quantity']); // Số lượng tồn kho

        // Số lượng hiện tại trong giỏ hàng (nếu đã có)
        $currentQty = isset($_SESSION['cart'][$code]) ? $_SESSION['cart'][$code]['quantity'] : 0;
        $newQty = $currentQty + 1;  // Cập nhật số lượng sản phẩm khi thêm vào giỏ

        // Kiểm tra tồn kho
        if ($newQty > $stock) {
            ErrorMessage("Only $stock unit(s) of '{$product['ProductName']}' available in stock.");
        } else {
            $_SESSION['cart'][$code] = [
                'id'       => $product['ProductID'], 
                'name'     => $product['ProductName'],
                'price'    => $product['Price'],
                'quantity' => $newQty,
                'status'   => $action
            ];

            Message("Added product '{$product['ProductName']}' to cart successfully!");
        }
    }
} else {
    ErrorMessage("Product not found!");
}
Redirect("index.php?do=homepage");

// Chuyển về giỏ hàng
//Redirect("index.php?do=shoppingcart");
