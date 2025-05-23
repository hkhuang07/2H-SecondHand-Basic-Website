<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "config.php";
include_once "library.php";

$ProductCode = trim($_POST['ProductCode']);
$ProductName = trim($_POST['ProductName']);
$CategoryID  = trim($_POST['CategoryID']);
$Description = trim($_POST['Description']);
$Price       = trim($_POST['Price']);
$Quantity    = trim($_POST['Quantity']);
$Discount    = trim($_POST['Discount']);
$Config      = trim($_POST['Config']);

// Kiểm tra dữ liệu
if ($ProductCode == "")
    ErrorMessage("Product code cannot be left blank!");
elseif ($CategoryID == "")
    ErrorMessage("No category selected!");
elseif ($ProductName == "")
    ErrorMessage("Product name cannot be left blank!");
elseif ($Description == "")
    ErrorMessage("Description cannot be left blank!");
elseif ($Config == "")
    ErrorMessage("Configuration cannot be left blank!");
elseif (!is_numeric($Discount))
    ErrorMessage("Discount must be a number!");
elseif (!is_numeric($Price))
    ErrorMessage("Price must be a number!");
elseif (!is_numeric($Quantity))
    ErrorMessage("Quantity must be a number!");
else {
    $filename = basename($_FILES['Image']['name']);
    $imagePath = "images/" . $filename;
    $imagePathCopy = "../TrangTin/images/" . $filename; // Copy sang nơi khác nếu cần

    if (move_uploaded_file($_FILES['Image']['tmp_name'], $imagePath)) {
        copy($imagePath, $imagePathCopy); // Đảm bảo copy đúng đường dẫn
    } else {
        echo "File upload failed.";
        exit;
    }

    $UserID = $_SESSION['UserID'] ?? null;

    $sql = "INSERT INTO `tbl_products` 
    (`ProductCode`, `ProductName`, `CategoryID`, `Image`, `Price`, `Description`, `Quantity`, `Discount`, `Config`, `View`)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)";
    $stmt = $connect->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $connect->error);
    }

    $stmt->bind_param(
        "ssisdsiis",
        $ProductCode,$ProductName,$CategoryID,$filename,$Price,
        $Description,$Quantity,$Discount,$Config);


    if ($stmt->execute()) {
        Message("Product added successfully!");
        Redirect("index.php?do=products");
    } else {
        ErrorMessage("Failed to add product: " . $stmt->error);
    }

    $stmt->close();
}
