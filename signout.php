<?php
// Đảm bảo ob_start được gọi nếu cần
ob_start();

// Hủy SESSION
unset($_SESSION['UserID']);
unset($_SESSION['FullName']);
unset($_SESSION['Role']);
unset($_SESSION['Email']) ;
unset($_SESSION['Phone']);


// Gọi hàm Message để thông báo đăng xuất thành công
include_once 'library.php';  // Đảm bảo file library.php đã được bao gồm

Message("You have successfully logged out. You will be redirected to the homepage.");

echo '<script>
    // Đợi 2 giây sau khi thông báo thành công, rồi chuyển hướng về trang index.php
    setTimeout(function() {
        window.location.href = "index.php";
    }, 2000);
</script>
';

// Đảm bảo không có bất kỳ mã nào thực thi sau header
ob_end_flush();
exit();  // Đảm bảo không có bất kỳ mã nào thực thi sau header
?>
