<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once('config.php');
include_once('library.php');

if (!isset($_SESSION['UserID'])) {
    ErrorMessage("Access denied. Please login first.");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ErrorMessage("Invalid request.");
    exit;
}

$UserID   = intval($_POST['UserID']);
$FullName = trim($_POST['FullName']);
$Email    = trim($_POST['Email']);
$Phone    = trim($_POST['Phone']);
$Address  = trim($_POST['Address']);

// Kiểm tra user
if ($UserID !== intval($_SESSION['UserID'])) {
    ErrorMessage("You can only update your own profile.");
    exit;
}
if ($FullName === "") {
    ErrorMessage("Full Name cannot be empty.");
    exit;
}
if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
    ErrorMessage("Invalid email format.");
    exit;
}

// Lấy avatar cũ
$currentAvatar = 'avatar.jpg';
if ($sel = $connect->prepare("SELECT Avatar FROM tbl_users WHERE UserID = ?")) {
    $sel->bind_param("i", $UserID);
    $sel->execute();
    $res = $sel->get_result();
    if ($res && $r = $res->fetch_assoc()) {
        $currentAvatar = $r['Avatar'];
    }
    $sel->close();
}

// Xử lý upload avatar
$AvatarToSave = $currentAvatar;
$uploadDir = __DIR__
           . DIRECTORY_SEPARATOR . 'uploads'
           . DIRECTORY_SEPARATOR . 'avatars'
           . DIRECTORY_SEPARATOR;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (isset($_FILES['Avatar']) && $_FILES['Avatar']['error'] === UPLOAD_ERR_OK) {
    $tmp  = $_FILES['Avatar']['tmp_name'];
    $name = basename($_FILES['Avatar']['name']);
    $safe = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $name);
    $ext  = strtolower(pathinfo($safe, PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif'];
    if (in_array($ext, $allowed)) {
        $target = $uploadDir . $safe;
        if (move_uploaded_file($tmp, $target)) {
            if ($currentAvatar !== 'avatar.jpg' && $currentAvatar !== $safe) {
                @unlink($uploadDir . $currentAvatar);
            }
            $AvatarToSave = $safe;
        } else {
            ErrorMessage("Failed to upload avatar.");
            exit;
        }
    } else {
        ErrorMessage("Invalid image format.");
        exit;
    }
}

// Cập nhật database
$upd = $connect->prepare("UPDATE tbl_users
                         SET FullName = ?, Email = ?, Phone = ?, Address = ?, Avatar = ?
                         WHERE UserID = ?");
$upd->bind_param("sssssi", $FullName, $Email, $Phone, $Address, $AvatarToSave, $UserID);

if (!$upd->execute()) {
    ErrorMessage("Update failed: " . $upd->error);
    exit;
}
$upd->close();

// Cập nhật session
$_SESSION['FullName'] = $FullName;
$_SESSION['Avatar']   = $AvatarToSave;
$_SESSION['Email']    = $Email;

Message("Profile updated successfully.");
Redirect('index.php?do=personal_profile');
ob_end_flush();
?>


<!--?php
// Đảm bảo ob_start được gọi ngay lập tức
ob_start(); 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include_once('config.php');

if (!isset($_SESSION['UserID'])) {
    ErrorMessage("Access denied. Please login first."); // Gọi hàm ErrorMessage từ library
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ErrorMessage("Invalid request.");
}

$UserID = intval($_POST['UserID']);
$FullName = trim($_POST['FullName']);

// Đảm bảo ID trùng với tài khoản hiện tại
if ($UserID !== intval($_SESSION['UserID'])) {
    ErrorMessage("You can only update your own profile.");
}

if (empty($FullName)) {
    ErrorMessage("Full Name cannot be empty.");
}

// Cập nhật thông tin
$sql = "UPDATE tbl_users SET FullName = ? WHERE UserID = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    ErrorMessage("Prepare failed: " . $connect->error);
}
$stmt->bind_param("si", $FullName, $UserID);
if (!$stmt->execute()) {
    ErrorMessage("Execute failed: " . $stmt->error);
}

// Cập nhật lại FullName trong session
$_SESSION['FullName'] = $FullName;

// Gọi hàm Message để thông báo thành công
Message("Profile updated successfully.");

// Redirect ngay sau khi xử lý xong
header("Location: index.php?do=personal_profile");
exit();

// Kết thúc ghi đệm output nếu có
ob_end_flush(); 
?-->
