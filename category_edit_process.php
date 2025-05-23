<?php
// Nạp tệp cấu hình để kết nối đến cơ sở dữ liệu
require_once 'config.php';

// Nạp tệp thư viện chứa các hàm tiện ích như ErrorMessage()
require_once 'library.php';

// =====================
// LẤY DỮ LIỆU TỪ FORM
// =====================

// Lấy ID danh mục từ form (nếu có), nếu không thì gán là null
$CategoryID = $_POST['CategoryID'] ?? null;

// Lấy tên danh mục từ form, loại bỏ khoảng trắng đầu/cuối, nếu không có thì gán là chuỗi rỗng
$CategoryName = trim($_POST['CategoryName'] ?? '');

// =====================
// KIỂM TRA DỮ LIỆU
// =====================

// Nếu không có ID hoặc ID không phải là số → thông báo lỗi
if (!$CategoryID || !is_numeric($CategoryID)) {
    ErrorMessage("Invalid Category ID!");
    exit; // Dừng chương trình
}

// Nếu tên danh mục rỗng → thông báo lỗi
if ($CategoryName === '') {
    ErrorMessage("Category name cannot be left blank!");
    exit; // Dừng chương trình
}

// =====================
// CẬP NHẬT DỮ LIỆU
// =====================

// Tạo prepared statement để tránh SQL Injection
$stmt = $connect->prepare("UPDATE tbl_categories SET CategoryName = ? WHERE CategoryID = ?");

// Nếu chuẩn bị câu lệnh thất bại → thông báo lỗi
if (!$stmt) {
    ErrorMessage("Failed to prepare SQL: " . $connect->error);
    exit;
}

// Gán dữ liệu vào câu lệnh SQL: "s" là chuỗi, "i" là số nguyên
$stmt->bind_param("si", $CategoryName, $CategoryID);

// Thực thi câu lệnh SQL
$result = $stmt->execute();

// Nếu thực thi thất bại → thông báo lỗi
if (!$result) {
    ErrorMessage("Update failed: " . $stmt->error);
    exit;
}

// =====================
// CHUYỂN HƯỚNG KHI THÀNH CÔNG
// =====================

// Nếu cập nhật thành công, chuyển hướng về trang danh sách danh mục
Redirect("index.php?do=categories");
exit;
?>
