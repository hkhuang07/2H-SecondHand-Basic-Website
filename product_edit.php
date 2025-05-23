<?php
require_once 'config.php';
require_once 'library.php';

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    echo "Product ID is missing!";
    exit;
}

// Lấy thông tin sản phẩm theo ID
$stmt = $connect->prepare("SELECT * FROM tbl_products WHERE ProductID = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <!--Bootstrap-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
	<!--Jquery-->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="scripts/ckeditor/ckeditor.js"></script>
	<!-- Ckeditor -->
	<script src="ckeditor/ckeditor.js"></script>

	<link rel="stylesheet" type="text/css" href="./css/style.css" />

    <title>Edit Product</title>
</head>
<body>
<div class="container mt-5">
    <div class="card mt-3">
        <div class="card-header">Edit Product</div>
        <div class="card-body">
            <form enctype="multipart/form-data" action="product_edit_process.php" method="post" class="form" novalidate>
                <input type="hidden" name="ProductID" value="<?= htmlspecialchars($product['ProductID']) ?>">

                <div class="mb-3">
                    <label for="ProductCode" class="form-label">Product Code</label>
                    <input type="text" class="form-control" id="ProductCode" name="ProductCode" required value="<?= htmlspecialchars($product['ProductCode']) ?>">
                    <div class="invalid-feedback">Product code cannot be left blank.</div>
                </div>

                <div class="mb-3">
                    <label for="ProductName" class="form-label">Product Name</label>
                    <input type="text" class="form-control" id="ProductName" name="ProductName" required value="<?= htmlspecialchars($product['ProductName']) ?>">
                    <div class="invalid-feedback">Product name cannot be left blank.</div>
                </div>

                <div class="mb-3">
                    <label for="CategoryID" class="form-label">Category</label>
                    <select class="form-select" id="CategoryID" name="CategoryID" required>
                        <option value="">-- Select Category --</option>
                        <?php
                        $sql = "SELECT * FROM tbl_categories ORDER BY CategoryName ASC";
                        $list = $connect->query($sql);
                        while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
                            $selected = $row['CategoryID'] == $product['CategoryID'] ? "selected" : "";
                            echo "<option value='{$row['CategoryID']}' $selected>{$row['CategoryName']}</option>";
                        }
                        ?>
                    </select>
                    <div class="invalid-feedback">You must select a category.</div>
                </div>

                <div class="mb-3">
                    <label for="Description" class="form-label">Description</label>
                    <textarea class="form-control" id="Description" name="Description" required><?= htmlspecialchars($product['Description']) ?></textarea>
                    <div class="invalid-feedback">Description cannot be left blank.</div>
                </div>

                <div class="mb-3">
                    <label for="Image" class="form-label">Image</label>
                    <input class="form-control" type="file" id="Image" name="Image">
                    <img src="<?= htmlspecialchars($product['Image']) ?>" width="100" class="mt-2" />
                </div>

                <div class="mb-3">
                    <label for="Price" class="form-label">Price</label>
                    <input type="number" class="form-control" id="Price" name="Price" required value="<?= htmlspecialchars($product['Price']) ?>">
                    <div class="invalid-feedback">Price must be a number and cannot be left blank.</div>
                </div>

                <div class="mb-3">
                    <label for="Quantity" class="form-label">Quantity</label>
                    <input type="number" class="form-control" id="Quantity" name="Quantity" required value="<?= htmlspecialchars($product['Quantity']) ?>">
                    <div class="invalid-feedback">Quantity must be a number and cannot be left blank.</div>
                </div>

                <div class="mb-3">
                    <label for="Discount" class="form-label">Discount (%)</label>
                    <input type="number" class="form-control" id="Discount" name="Discount" required value="<?= htmlspecialchars($product['Discount']) ?>">
                    <div class="invalid-feedback">Discount must be a number and cannot be left blank.</div>
                </div>

                <div class="mb-3">
                    <label for="Config" class="form-label">Configuration</label>
                    <textarea class="form-control" id="Config" name="Config" required><?= htmlspecialchars($product['Config']) ?></textarea>
                    <div class="invalid-feedback">Configuration cannot be left blank.</div>
                </div>

                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="index.php?do=products" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
    CKEDITOR.replace('Config');

    // Bootstrap validation
    (() => {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>
</body>
</html>
