<?php
require_once("config.php");
require_once("library.php");

$UserID   = intval($_POST['UserID']);
$UserName = trim($_POST['UserName']);
$FullName = trim($_POST['FullName']);
$Role     = intval($_POST['Role']);
$Key      = intval($_POST['Key']);
$Email   = trim($_POST['Email']);
$Phone   = trim($_POST['Phone']);
$Address = trim($_POST['Address']);

// 1. Validate input
if ($UserName === "") {
    ErrorMessage("Username cannot be left blank!");
    exit;
} elseif ($FullName === "") {
    ErrorMessage("Full name cannot be left blank!");
    exit;
} elseif (!in_array($Role, [1, 2])) {
    ErrorMessage("Invalid role selected!");
    exit;
} elseif (!in_array($Key, [0, 1])) {
    ErrorMessage("Invalid account status!");
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


// 2. Lấy avatar cũ
$currentAvatar = 'avatar.jpg';
if ($sel = $connect->prepare("SELECT Avatar FROM tbl_users WHERE UserID = ?")) {
    $sel->bind_param("i", $UserID);
    $sel->execute();
    $res = $sel->get_result();
    if ($res && $row = $res->fetch_assoc()) {
        $currentAvatar = $row['Avatar'];
    }
    $sel->close();
}

// 3. Chuẩn bị thư mục upload
$uploadDir = __DIR__
    . DIRECTORY_SEPARATOR . 'images'
    . DIRECTORY_SEPARATOR . 'avatars'
    . DIRECTORY_SEPARATOR;

// Tạo thư mục nếu chưa tồn tại
if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
    ErrorMessage("Failed to create directory for avatar uploads: $uploadDir");
    exit;
}

// Kiểm tra quyền ghi
if (!is_writable($uploadDir)) {
    ErrorMessage("Upload directory is not writable: $uploadDir");
    exit;
}

$AvatarToSave = $currentAvatar;

// 4. Xử lý upload avatar mới (nếu có)
if (
    isset($_FILES['Avatar']) &&
    $_FILES['Avatar']['error'] === UPLOAD_ERR_OK &&
    is_uploaded_file($_FILES['Avatar']['tmp_name'])
) {
    $tmpPath  = $_FILES['Avatar']['tmp_name'];
    $origName = basename($_FILES['Avatar']['name']);
    // Sanitization: giữ lại ký tự an toàn
    $safeName = preg_replace('/[^A-Za-z0-9\.\-_]/', '_', $origName);
    $ext      = strtolower(pathinfo($safeName, PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($ext, $allowed)) {
        ErrorMessage("Invalid image format. Allowed types: " . implode(', ', $allowed));
        exit;
    }

    $newName = $safeName;
    $target  = $uploadDir . $newName;

    if (!move_uploaded_file($tmpPath, $target)) {
        ErrorMessage("Failed to move uploaded file to: $target");
        exit;
    }

    // Xóa avatar cũ (nếu khác và không phải default)
    if ($currentAvatar !== 'avatar.jpg' && $currentAvatar !== $newName) {
        $oldFile = $uploadDir . $currentAvatar;
        if (file_exists($oldFile)) {
            @unlink($oldFile);
        }
    }

    $AvatarToSave = $newName;
}

// 5. Cập nhật database (gồm cả Avatar)
$sql = "UPDATE tbl_users
        SET UserName = ?, FullName = ?, Email = ?, Phone = ?, Address = ?, Avatar = ?, Role = ?, `Key` = ?
        WHERE UserID = ?";
$stmt = $connect->prepare($sql);
if (!$stmt) {
    ErrorMessage("Prepare failed: " . $connect->error);
    exit;
}

// Kiểu param: s = string, i = integer
$stmt->bind_param("sssssssii",
    $UserName,
    $FullName,
    $Email,
    $Phone,
    $Address,
    $AvatarToSave,
    $Role,
    $Key,
    $UserID
);

if ($stmt->execute()) {
    Message("User updated successfully!");
    echo '<script>setTimeout(function(){ window.location.href = "index.php?do=users"; }, 2000);</script>';
} else {
    ErrorMessage("Update failed: " . $stmt->error);
    echo '<script>setTimeout(function(){ window.location.href = "index.php?do=user_edit&id=' . $UserID . '"; }, 2000);</script>';
}

$stmt->close();
