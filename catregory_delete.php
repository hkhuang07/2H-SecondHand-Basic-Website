<?php
require_once 'config.php';
require_once 'library.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delete Category</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div class="container mt-5">
        <?php
        if (isset($_POST['confirm_delete'])) {
            $CategoryID = intval($_POST['CategoryID']);
        
            // Kiểm tra xem có sản phẩm nào đang sử dụng category này không
            $sql_check = "SELECT COUNT(*) AS total FROM tbl_products WHERE CategoryID = ?";
            $stmt_check = $connect->prepare($sql_check);
            $stmt_check->bind_param('i', $CategoryID);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            $row_check = $result_check->fetch_assoc();
            $stmt_check->close();
        
            if ($row_check['total'] > 0) {
                echo "<div class='alert alert-warning'>Cannot delete this category because there are {$row_check['total']} products associated with it.</div>";
                echo "<a href='index.php?do=categories' class='btn btn-secondary mt-2'><i class='bi bi-arrow-left'></i> Back</a>";
            } else {
                // Không có sản phẩm liên quan, thực hiện xóa
                $sql_delete = "DELETE FROM tbl_categories WHERE CategoryID = ?";
                $stmt_delete = $connect->prepare($sql_delete);
                $stmt_delete->bind_param('i', $CategoryID);
        
                if ($stmt_delete->execute()) {
                    echo "<div class='alert alert-success'>Category deleted successfully. Redirecting...</div>";
                    Redirect("index.php?do=categories");
                } else {
                    echo "<div class='alert alert-danger'>Unable to delete the category: " . $stmt_delete->error . "</div>";
                }
        
                $stmt_delete->close();
            }
        } elseif (isset($_GET['id'])) {
            // Hiển thị form xác nhận xóa
            $CategoryID = intval($_GET['id']);

            if (!$connect) {
                die("<div class='alert alert-danger'>Connection failed: " . mysqli_connect_error() . "</div>");
            }

            $sql = "SELECT CategoryName FROM tbl_categories WHERE CategoryID = ?";
            $stmt = $connect->prepare($sql);
            $stmt->bind_param('i', $CategoryID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $category = $result->fetch_assoc();
        ?>
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Delete</h5>
                    </div>
                    <div class="card-body">
                        <p>Are you sure you want to delete the following category?</p>
                        <p><strong>Category:</strong> <?= htmlspecialchars($category['CategoryName']) ?></p>

                        <form action="" method="post" class="d-flex gap-2">
                            <input type="hidden" name="CategoryID" value="<?= $CategoryID ?>" />
                            <button type="submit" name="confirm_delete" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Yes, Delete
                            </button>
                            <a href="index.php?do=categories" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Cancel
                            </a>
                        </form>
                    </div>
                </div>
        <?php
            } else {
                echo "<div class='alert alert-warning'>Category not found!</div>";
            }

            $stmt->close();
        } else {
            // Nếu không có tham số id hoặc POST
            Redirect("index.php?do=categories");
        }
        ?>
    </div>

</body>

</html>