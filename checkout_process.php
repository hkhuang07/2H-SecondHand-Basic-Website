<?php
// Kiểm tra nếu session chưa được khởi tạo thì khởi tạo nó
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bao gồm file cấu hình cơ sở dữ liệu và thư viện tiện ích
include_once "config.php";
include_once "library.php";

// Kiểm tra nếu không có sản phẩm nào được chọn để thanh toán thì chuyển hướng lại giỏ hàng
if (empty($_SESSION['selected_cart'])) {
    Redirect("index.php?do=shoppingcart");
    exit;
}

// Lấy danh sách sản phẩm đã được chọn từ session
$selected_items = $_SESSION['selected_cart'];

// Chuẩn bị thông tin người dùng từ session (nếu có)
$userData = [
    'name'  => $_SESSION["FullName"] ?? '',
    'email' => $_SESSION["Email"] ?? '',
    'phone' => $_SESSION["Phone"] ?? '',
];

// Lấy ID người dùng nếu có đăng nhập
$user_id = $_SESSION['UserID'] ?? null;

// Nếu có ID người dùng thì truy vấn thêm thông tin từ CSDL
if ($user_id) {
    $stmt = $connect->prepare("SELECT FullName, Email, Phone FROM tbl_users WHERE UserID = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id); // Gán tham số cho truy vấn
        $stmt->execute(); // Thực thi truy vấn
        $stmt->bind_result($userData['name'], $userData['email'], $userData['phone']); // Gán kết quả vào mảng userData
        $stmt->fetch(); // Lấy dữ liệu
        $stmt->close(); // Đóng statement
    }
}

// Khởi tạo biến lỗi và thành công
$errors = [];
$success = '';

// Kiểm tra nếu form được gửi bằng phương thức POST
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['name'])) {
    // Lấy và làm sạch dữ liệu từ form
    $name    = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $email   = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
    $phone   = trim(filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING));
    $address = trim(filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING));

    // Kiểm tra các trường có bị rỗng không
    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $errors[] = "All fields are required.";
    }

    // Kiểm tra tồn kho cho từng sản phẩm đã chọn
    foreach ($selected_items as $item) {
        if (!isset($item['id']) || !is_numeric($item['id'])) {
            $errors[] = "Invalid Product ID for item '{$item['name']}'.";
            continue;
        }

        $stmt_check = $connect->prepare("SELECT Quantity FROM tbl_products WHERE ProductID = ?");
        $stmt_check->bind_param("i", $item['id']);
        $stmt_check->execute();
        $stmt_check->bind_result($stock);

        if ($stmt_check->fetch()) {
            if ($stock < $item['quantity']) {
                $errors[] = "Product '{$item['name']}' only has $stock in stock.";
            }
        } else {
            $errors[] = "Product '{$item['name']}' not found in inventory.";
        }

        $stmt_check->close();
    }

    // Nếu không có lỗi nào xảy ra thì tiến hành tạo đơn hàng
    if (empty($errors)) {
        // Tính tổng tiền
        $total = array_reduce($selected_items, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $connect->begin_transaction(); // Bắt đầu giao dịch
        try {
            // Thêm vào bảng tbl_orders
            $stmt = $connect->prepare("INSERT INTO tbl_orders (OrderDate, UserID, Address, Status, TotalAmount) VALUES (NOW(), ?, ?, 'Pending', ?)");
            $stmt->bind_param("isd", $user_id, $address, $total);
            if (!$stmt->execute()) {
                throw new Exception("Could not create order.");
            }
            $order_id = $stmt->insert_id; // Lấy ID đơn hàng vừa tạo
            $stmt->close();

            // Chuẩn bị 2 câu lệnh: thêm chi tiết đơn hàng và cập nhật tồn kho
            $stmt_item = $connect->prepare("INSERT INTO tbl_order_items (OrderID, ProductID, ProductName, Price, Quantity) VALUES (?, ?, ?, ?, ?)");
            $stmt_update = $connect->prepare("UPDATE tbl_products SET Quantity = Quantity - ?, PurchaseCount = PurchaseCount + ? WHERE ProductID = ? AND Quantity >= ?");

            // Duyệt qua từng sản phẩm để thực hiện thêm và cập nhật tồn kho
            foreach ($selected_items as $item) {
                $stmt_item->bind_param("iisdi", $order_id, $item['id'], $item['name'], $item['price'], $item['quantity']);
                $stmt_item->execute();

                $stmt_update->bind_param("iiii", $item['quantity'], $item['quantity'], $item['id'], $item['quantity']);
                $stmt_update->execute();

                // Nếu không cập nhật tồn kho được (sản phẩm đã hết hàng), hủy đơn hàng
                if ($stmt_update->affected_rows === 0) {
                    throw new Exception("Failed to update inventory for product '{$item['name']}'.");
                }
            }

            $connect->commit(); // Commit giao dịch

            // Xóa sản phẩm đã mua khỏi giỏ hàng
            foreach ($selected_items as $code => $item) {
                unset($_SESSION['cart'][$code]);
            }

            unset($_SESSION['selected_cart']); // Xóa danh sách đã chọn
            $success = "Order placed successfully!"; // Gán thông báo thành công

        } catch (Exception $e) {
            $connect->rollback(); // Rollback nếu có lỗi
            $errors[] = "Checkout failed: " . $e->getMessage();
            error_log("Checkout Error: " . $e->getMessage()); // Ghi log lỗi
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Checkout Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./css/style.css" />
</head>


<body>
    <div class="container-fluid">
        <div id="Header"></div>
        <div id="navbar">
            <?php include "inc/navbar.php"; ?>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <div id="MiddlePart">
					<div id="Left" class="sidebar">
						<ul class="nav flex-column text-white">

							<li class="nav-item ">
								<a class="nav-link text-white d-flex align-items-center" href="index.php" aria-expanded="false">
									<i class="bi bi-house-door me-2"></i> <span class="menu-label"> Home</span>
								</a>
							</li>


							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="bi bi-gear-fill me-2"></i> <span class="menu-label">Dashboard</span>
								</a>
								<ul class="dropdown-menu bg-dark border-0">
									<?php
									if (!isset($_SESSION['UserID'])) {
										echo '<li><a class="dropdown-item text-white" href="index.php?do=signin">Sign In</a></li>';
										echo '<li><a class="dropdown-item text-white" href="index.php?do=signup">Sign Up</a></li>';
									} else {
										echo '<li><a class="dropdown-item text-white" href="index.php?do=shoppingcart">My Shopping Card</a></li>';
										if ($_SESSION['Role'] == 1) {
											echo '<li><a class="dropdown-item text-white" href="index.php?do=categories">Categories List</a></li>';
											echo '<li><a class="dropdown-item text-white" href="index.php?do=products">Products List</a></li>';
											echo '<li><a class="dropdown-item text-white" href="index.php?do=users">Users List</a></li>';
										}
									}
									?>
								</ul>
							</li>

							<?php if (isset($_SESSION['UserID'])): ?>
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="bi bi-person-circle me-2"></i> <span class="menu-label">Profile</span>
									</a>
									<ul class="dropdown-menu bg-dark border-0">
										<li><a class="dropdown-item text-white" href="index.php?do=personal_profile">Personal Profile</a></li>
										<li><a class="dropdown-item text-white" href="index.php?do=changepass">Change Password</a></li>
									</ul>
								</li>
							<?php endif; ?>

							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="bi bi-three-dots me-2"></i> <span class="menu-label">More</span>
								</a>
								<ul class="dropdown-menu bg-dark border-0">
									<li><a class="dropdown-item text-white" href="#">Help</a></li>
									<li><a class="dropdown-item text-white" href="index?do=home">Web Information</a></li>
								</ul>
							</li>

						</ul>
					</div>
					<!-- Phần giữa -->
					  <div id="Middle" class="container-fluid w-100">
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5>There were some issues:</h5>
                                <ul>
                                    <?php foreach ($errors as $e): ?>
                                        <li><?= htmlspecialchars($e) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <a href="index.php?do=checkout" class="btn btn-warning">Go Back to Checkout</a>
                        <?php elseif ($success): ?>
                            <div class="alert alert-success text-center">
                                <h5><?= htmlspecialchars($success) ?></h5>
                            </div>
                            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                        <?php endif; ?>
                    </div>

					<!-- Quảng cáo bên phải -->
					<div id="Right">
						
						<img src="images/clothes.gif" alt="Advertisement" />
						<br />
						<img src="images/clothes1.gif" alt="Advertisement" />
						<br />
						<img src="images/clothes2.gif" alt="Advertisement" />
						<br />
						<img src="images/fashion.gif" alt="Advertisement" />
						<br />
						<img src="images/dienmayxanh.gif" alt="Advertisement" />
						<br />
						<img src="images/grap.gif" alt="Advertisement" />
						<br />
						<img src="images/iphone.gif" alt="Advertisement" />
						<br />
						<img src="images/shopee.gif" alt="Advertisement" />
					</div>

				</div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $("#navbar").load("inc/navbar.html", function() {
                // callback sau khi navbar load xong
                // kích hoạt lại các event của Bootstrap (nếu cần)
            });
            $("#footer").load("inc/footer.html");
        });
    </script>
</body>

</html>