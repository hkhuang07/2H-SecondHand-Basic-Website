<?php
// Kiểm tra nếu chưa có phiên làm việc nào thì bắt đầu phiên mới
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bao gồm file cấu hình để kết nối cơ sở dữ liệu
include_once "config.php";

// Bao gồm file thư viện chứa các hàm tiện ích như Message(), Redirect(), ErrorMessage()
include_once "library.php";

// Lấy dữ liệu từ biểu mẫu POST và loại bỏ khoảng trắng ở đầu/cuối chuỗi
$CategoryName = trim($_POST['CategoryName']);

// Kiểm tra dữ liệu đầu vào: nếu tên danh mục bị để trống thì báo lỗi
if ($CategoryName == "")
    ErrorMessage("Category name cannot be left blank!"); // Gọi hàm hiển thị thông báo lỗi
else {
    // Lấy ID người dùng từ session (nếu có), dùng để xử lý nếu cần lưu theo người tạo
    $UserID = $_SESSION['UserID'] ?? null;

    // Chuẩn bị câu lệnh SQL dùng Prepared Statement để tránh lỗi SQL Injection
    $sql = "INSERT INTO `tbl_categories` 
    (`CategoryName`)
    VALUES (?)";

    // Chuẩn bị statement với đối tượng kết nối $connect
    $stmt = $connect->prepare($sql);

    // Nếu chuẩn bị câu lệnh thất bại, dừng chương trình và hiển thị lỗi
    if ($stmt === false) {
        die("Prepare failed: " . $connect->error);
    }

    // Gán biến $CategoryName vào dấu hỏi chấm (?) trong câu SQL, kiểu dữ liệu là 's' (string)
    $stmt->bind_param(
        "s", // s: kiểu chuỗi
        $CategoryName
    );

    // Thực thi câu lệnh SQL
    if ($stmt->execute()) {
        // Nếu thành công, hiển thị thông báo và chuyển hướng về trang danh mục
        Message("Category added successfully!");
        Redirect("index.php?do=categories");
    } else {
        // Nếu thất bại, hiển thị thông báo lỗi
        ErrorMessage("Failed to add category: " . $stmt->error);
    }

    // Đóng đối tượng statement sau khi thực hiện xong
    $stmt->close();
}
?>
