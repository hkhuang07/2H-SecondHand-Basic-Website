<?php
require_once 'config.php';
require_once 'library.php';

$category_id = $_GET['id'] ?? null;

if (!$category_id || !is_numeric($category_id)) {
    echo "Invalid or missing Category ID!";
    exit;
}

$category_id = (int)$category_id;

$stmt = $connect->prepare("SELECT * FROM tbl_categories WHERE CategoryID = ?");
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();

if (!$category) {
    echo "Category not found!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
    <!-- Jquery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" type="text/css" href="./css/style.css" />
</head>
<body>
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 600px;">
        <div class="card-header">Edit Category</div>
        <div class="card-body">
            <form action="category_edit_process.php" method="post" class="form" novalidate>
                <input type="hidden" name="CategoryID" value="<?= htmlspecialchars($category['CategoryID']) ?>">

                <div class="mb-3">
                    <label for="CategoryName" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="CategoryName" name="CategoryName" required value="<?= htmlspecialchars($category['CategoryName']) ?>">
                    <div class="invalid-feedback">Category name cannot be left blank.</div>
                </div>

                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="index.php?do=categories" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
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
