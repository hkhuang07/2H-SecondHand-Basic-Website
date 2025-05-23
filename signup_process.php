<?php
require_once("library.php"); // Nhúng file chứa các hàm hỗ trợ (ví dụ: ErrorMessage, Message)
require_once("config.php");  // Nhúng cấu hình kết nối cơ sở dữ liệu

// Lấy dữ liệu từ form POST
$FullName = trim($_POST['FullName']);
$Email = trim($_POST['Email']);
$Phone = trim($_POST['Phone']);
$Address = trim($_POST['Address']);
$UserName = trim($_POST['UserName']);
$Password = $_POST['Password'];
$ConfirmPassword = $_POST['ConfirmPassword'] ?? '';

// Kiểm tra dữ liệu hợp lệ
if ($FullName === "")
	ErrorMessage("Full name cannot be left blank!");
elseif ($Email === "" || !filter_var($Email, FILTER_VALIDATE_EMAIL))
	ErrorMessage("Invalid or blank email!");
elseif ($Phone === "")
	ErrorMessage("Phone number cannot be left blank!");
elseif ($Address === "")
	ErrorMessage("Address cannot be left blank!");
elseif ($UserName === "")
	ErrorMessage("Username cannot be left blank!");
elseif ($Password === "")
	ErrorMessage("Password cannot be left blank!");
elseif ($Password !== $ConfirmPassword)
	ErrorMessage("Confirm password does not match!");
else {
	// Kiểm tra xem tên người dùng đã tồn tại chưa
	$stmt_check = $connect->prepare("SELECT UserID FROM tbl_users WHERE UserName = ?");
	if (!$stmt_check) {
		ErrorMessage("Prepare failed: " . $connect->error);
		exit;
	}
	$stmt_check->bind_param("s", $UserName);
	$stmt_check->execute();
	$stmt_check->store_result();

	if ($stmt_check->num_rows > 0) {
		ErrorMessage("Username already exists!");
	} else {
		// Mã hóa mật khẩu bằng bcrypt
		$hashedPassword = password_hash($Password, PASSWORD_DEFAULT);

		// Thêm người dùng mới vào bảng tbl_users
		$stmt_insert = $connect->prepare("INSERT INTO tbl_users (FullName, Email, Phone, Address, UserName, Password, Role, `Key`) VALUES (?, ?, ?, ?, ?, ?, 2, 0)");
		if (!$stmt_insert) {
			ErrorMessage("Prepare failed: " . $connect->error);
			exit;
		}
		$stmt_insert->bind_param("ssssss", $FullName, $Email, $Phone, $Address, $UserName, $hashedPassword);

		// Thực thi câu lệnh
		if ($stmt_insert->execute()) {
			Message("Registration successful!");
			echo '<script>
				setTimeout(function() {
					window.location.href = "index.php";
				}, 2000);
			</script>';
			exit();
		} else {
			ErrorMessage("Registration failed: " . $connect->error);
			echo '<script>
				setTimeout(function() {
					window.location.href = "index.php?do=signup";
				}, 2000);
			</script>';
			exit();
		}
		$stmt_insert->close();
	}
	$stmt_check->close();
}
?>
