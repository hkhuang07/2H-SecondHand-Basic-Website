<div class="card">
  <div class="card-header">Add New Product</div>
  <div class="card-body">
    <form enctype="multipart/form-data" action="index.php?do=product_add_process" method="post" class="form" novalidate>

      <div class="mb-3">
        <label for="ProductCode" class="form-label">Product Code</label>
        <input type="text" class="form-control" id="ProductCode" name="ProductCode" required>
        <div class="invalid-feedback">Product code cannot be left blank.</div>
      </div>

      <div class="mb-3">
        <label for="ProductName" class="form-label">Product Name</label>
        <input type="text" class="form-control" id="ProductName" name="ProductName" required>
        <div class="invalid-feedback">Product name cannot be left blank.</div>
      </div>

      <div class="mb-3">
        <label for="CategoryID" class="form-label">Category</label>
        <select class="form-select" id="CategoryID" name="CategoryID" required>
          <option value="">-- Select Category --</option>
          <?php
          $sql = "SELECT * FROM `tbl_categories` ORDER BY CategoryName ASC";
          $list = $connect->query($sql);
          if (!$list) {
            die("Unable to execute SQL statement: " . $connect->connect_error);
          }
          while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
            echo "<option value='" . $row['CategoryID'] . "'>" . $row['CategoryName'] . "</option>";
          }
          ?>
        </select>
        <div class="invalid-feedback">You must select a category.</div>
      </div>

      <div class="mb-3">
        <label for="Description" class="form-label">Description</label>
        <textarea class="form-control" id="Description" name="Description" required></textarea>
        <div class="invalid-feedback">Description cannot be left blank.</div>
      </div>

      <div class="mb-3">
        <label for="Image" class="form-label">Image</label>
        <input class="form-control" type="file" id="Image" name="Image" required>
        <div class="invalid-feedback">Please select an image.</div>
      </div>

      <div class="mb-3">
        <label for="Price" class="form-label">Price</label>
        <input type="number" class="form-control" id="Price" name="Price" required>
        <div class="invalid-feedback">Price must be a number and cannot be left blank.</div>
      </div>

      <div class="mb-3">
        <label for="Quantity" class="form-label">Quantity</label>
        <input type="number" class="form-control" id="Quantity" name="Quantity" required>
        <div class="invalid-feedback">Quantity must be a number and cannot be left blank.</div>
      </div>

      <div class="mb-3">
        <label for="Discount" class="form-label">Discount (%)</label>
        <input type="number" class="form-control" id="Discount" name="Discount" required>
        <div class="invalid-feedback">Discount must be a number and cannot be left blank.</div>
      </div>

      <div class="mb-3">
        <label for="Config" class="form-label">Configuration</label>
        <textarea class="form-control" id="Config" name="Config" required></textarea>
        <div class="invalid-feedback">Configuration cannot be left blank.</div>
      </div>

      <button type="submit" class="btn btn-success">
        <i class="bi bi-plus-square"></i> Add Product
      </button>
    </form>
  </div>
</div>

<script>
  // Kích hoạt CKEditor cho Config
  CKEDITOR.replace('Config');

  // Bootstrap 5 validation
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