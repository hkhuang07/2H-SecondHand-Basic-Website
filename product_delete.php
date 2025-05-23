<?php
include_once "config.php";
include_once "library.php";
// Xử lý khi xác nhận xóa
if (isset($_POST['confirm_delete'])) {
    $ProductID = intval($_POST['ProductID']);

    $sql_delete = "DELETE FROM tbl_products WHERE ProductID = ?";
    $stmt_delete = $connect->prepare($sql_delete);
    $stmt_delete->bind_param('i', $ProductID);

    if ($stmt_delete->execute()) {
        // Xóa thành công, chuyển hướng
        Message("Product deleted successfully. Redirecting...");
        Redirect("index.php?do=products");
    } else {
        ErrorMessage("Unable to delete the product: " . $stmt_delete->error );
    }

    $stmt_delete->close();
    exit();
}

// Hiển thị form xác nhận xóa
elseif (isset($_GET['id'])) {
    $ProductID = intval($_GET['id']);

    // Kiểm tra kết nối
    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Lấy thông tin sản phẩm để xác nhận xóa
    $sql = "SELECT ProductName FROM tbl_products WHERE ProductID = ?";
    $stmt = $connect->prepare($sql);
    $stmt->bind_param('i', $ProductID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu tồn tại sản phẩm
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        ?>

        <div class="card mt-4">
            <div class="card-body">
                <div class="card-header">Confirm Deletion</div>
                <p>Are you sure you want to delete the product: <strong><?= htmlspecialchars($product['ProductName']) ?></strong>?</p>
                <form method="post" class="form">
                    <input type="hidden" name="ProductID" value="<?= $ProductID ?>">
                    <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
                    <a href="index.php?do=products" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>

        <?php
    } else {
        echo "<div class='alert alert-warning'>Product not found!</div>";
    }

    $stmt->close();
} else {
    // Nếu không có gì, quay về trang sản phẩm
    Redirect("index.php?do=products");
    exit();
}
?>
