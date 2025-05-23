<!-- Thẻ div chính dùng để hiển thị nội dung dưới dạng thẻ Bootstrap -->
<div class="card">
  <!-- Tiêu đề của card -->
  <div class="card-header">Add New Category</div> 

  <!-- Vùng hiển thị bảng có thể cuộn ngang nếu nội dung vượt quá -->
  <div class="table-responsive">

    <!-- Form gửi dữ liệu đến tệp category_add_process.php thông qua phương thức POST -->
    <!-- enctype="multipart/form-data" để hỗ trợ upload file nếu cần trong tương lai -->
    <form enctype="multipart/form-data" action="index.php?do=category_add_process" method="post" class="form" novalidate>

      <!-- Nhóm form nhập tên danh mục -->
      <div class="mb-3">
        <!-- Nhãn cho input -->
        <label for="CategoryName" class="form-label">Category Name</label>

        <!-- Trường nhập tên danh mục, bắt buộc phải nhập (required) -->
        <input type="text" class="form-control" id="CategoryName" name="CategoryName" required>

        <!-- Hiển thị thông báo lỗi nếu bỏ trống, dùng bởi Bootstrap 5 validation -->
        <div class="invalid-feedback">Category name cannot be left blank.</div>
      </div>

      <!-- Nút bấm để gửi form -->
      <button type="submit" class="btn btn-success">
        <i class="bi bi-plus-square"></i> Add Category <!-- Biểu tượng cộng từ Bootstrap Icons -->
      </button>
    </form>
  </div>
</div>

<!-- Script kiểm tra hợp lệ theo chuẩn Bootstrap 5 -->
<script>
  // Bootstrap 5 validation script
  (() => {
    'use strict';

    // Lấy tất cả các form có class "needs-validation"
    const forms = document.querySelectorAll('.needs-validation');

    // Duyệt qua từng form để gắn sự kiện khi submit
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        // Nếu form không hợp lệ thì ngăn chặn gửi đi
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }

        // Thêm class để kích hoạt hiển thị lỗi của Bootstrap
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>
