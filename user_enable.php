<?php
// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Nhúng file cấu hình và thư viện
include_once "config.php";
include_once "library.php";

// Dùng bộ đệm đầu ra để tránh lỗi "headers already sent"
ob_start();

// Kiểm tra và xử lý nếu URL có tham số 'role' để tăng/hạ quyền người dùng
if (isset($_GET['role']) && isset($_GET['id'])) {
    $role = intval($_GET['role']);
    $id = intval($_GET['id']);

    // Cập nhật quyền người dùng trong bảng tbl_users
    $sql = "UPDATE `tbl_users` SET `Role` = $role WHERE `UserID` = $id";
    $result = $connect->query($sql);

    if (!$result) {
        // Nếu truy vấn thất bại, hiển thị lỗi
        ErrorMessage("Lỗi cập nhật quyền: " . $connect->error);
    } else {
        // Nếu thành công, chuyển hướng về trang danh sách người dùng
        header("Location: index.php?do=users");
        exit;
    }
}

// Kiểm tra và xử lý nếu URL có tham số 'Key' để khóa/mở khóa tài khoản
elseif (isset($_GET['Key']) && isset($_GET['id'])) {
    $key = intval($_GET['Key']);
    $id = intval($_GET['id']);

    // Cập nhật trạng thái tài khoản
    $sql = "UPDATE `tbl_users` SET `Key` = $key WHERE `UserID` = $id";
    $result = $connect->query($sql);

    if (!$result) {
        ErrorMessage("Lỗi cập nhật trạng thái tài khoản: " . $connect->error);
    } else {
        header("Location: index.php?do=users");
        exit;
    }
}

// Nếu không có role hoặc key, chuyển hướng về trang danh sách người dùng
else {
    header("Location: index.php?do=users");
    exit;
}

// Kết thúc bộ đệm đầu ra (không bắt buộc nếu đã exit trước đó, nhưng tốt để an toàn)
ob_end_flush();
?>
