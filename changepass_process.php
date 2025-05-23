<?php
if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

require_once("config.php");
require_once("library.php"); // Chứa các hàm thông báo như Message, ErrorMessage

// Nhận dữ liệu từ form
$UserID = $_POST['UserID'] ?? '';
$OldPass = trim($_POST['OldPass'] ?? '');
$NewPass = trim($_POST['NewPass'] ?? '');
$ConfirmPass = trim($_POST['ConfirmPass'] ?? '');

// Kiểm tra dữ liệu đầu vào
if ($OldPass === '') {
	ErrorMessage("Old password cannot be left blank!");
} elseif ($NewPass === '') {
	ErrorMessage("New password cannot be left blank!");
} elseif ($ConfirmPass === '') {
	ErrorMessage("Confirm password cannot be left blank!");
} elseif ($NewPass !== $ConfirmPass) {
	ErrorMessage("Confirm password does not match!");
} else {
	// Lấy mật khẩu cũ từ cơ sở dữ liệu
	$sql = "SELECT Password FROM tbl_users WHERE UserID = ?";
	$stmt = $connect->prepare($sql);
	$stmt->bind_param("i", $UserID);
	$stmt->execute();
	$stmt->bind_result($dbPassword);
	$stmt->fetch();
	$stmt->close();

	if (!$dbPassword || !password_verify($OldPass, $dbPassword)) {
		ErrorMessage("Old password is incorrect!");
		exit;
	}

	// Mã hóa mật khẩu mới
	$hashedNewPass = password_hash($NewPass, PASSWORD_DEFAULT);

	// Cập nhật mật khẩu mới
	$sql_update = "UPDATE tbl_users SET Password = ? WHERE UserID = ?";
	$stmt_update = $connect->prepare($sql_update);
	$stmt_update->bind_param("si", $hashedNewPass, $UserID);

	if ($stmt_update->execute()) {
		Message("Password changed successfully!");
	} else {
		ErrorMessage("An error occurred while updating password.");
	}
	$stmt_update->close();
}

$connect->close();
