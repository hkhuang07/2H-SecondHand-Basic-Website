<?php
require_once 'config.php';
require_once 'library.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Nhận dữ liệu từ form
    $ProductID = $_POST['ProductID'] ?? null;
    $ProductCode = $_POST['ProductCode'] ?? '';
    $ProductName = $_POST['ProductName'] ?? '';
    $CategoryID = $_POST['CategoryID'] ?? '';
    $Description = $_POST['Description'] ?? '';
    $Price = $_POST['Price'] ?? 0;
    $Quantity = $_POST['Quantity'] ?? 0;
    $Discount = $_POST['Discount'] ?? 0;
    $Config = $_POST['Config'] ?? '';
    $Image = '';

    if (!$ProductID || empty($ProductCode) || empty($ProductName)) {
        ErrorMessage("Invalid input. Please fill in required fields.");
    }

    //Lấy ảnh hiện tại từ database
    $stmt = $connect->prepare("SELECT Image FROM tbl_products WHERE ProductID = ?");
    $stmt->bind_param("i", $ProductID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $currentImage = $row['Image'] ?? '';
    $stmt->close();

    // Xử lý upload ảnh nếu có file mới
    if (!empty($_FILES['Image']['name']) && $_FILES['Image']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'images/';
        $filename = time() . '_' . basename($_FILES['Image']['name']);
        $imagePath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['Image']['tmp_name'], $imagePath)) {
            // Xóa ảnh cũ nếu có
            $Image = $filename;
        } else {
            ErrorMessage("Failed to upload image.");
        }
    } else {
        // Nếu không có file mới, giữ nguyên ảnh cũ
        $Image = $currentImage;
    }
    $Status = ($Quantity == 0) ? 0 : 1;

    // Cập nhật dữ liệu sản phẩm
    $stmt = $connect->prepare("UPDATE tbl_products SET 
    ProductCode = ?, ProductName = ?, CategoryID = ?, Price = ?, Quantity = ?, Discount = ?, 
    Description = ?, Image = ?, Config = ?, Status = ?
    WHERE ProductID = ?");
    $stmt->bind_param("ssiidisssii", $ProductCode, $ProductName, $CategoryID, $Price, $Quantity, $Discount, $Description, $Image, $Config, $Status, $ProductID);

    if ($stmt->execute()) {
        Message("Product updated successfully!");
        Redirect("index.php?do=products");
    } else {
        ErrorMessage("Update failed: " . $stmt->error);
    }

    $stmt->close();
    $connect->close();
}
