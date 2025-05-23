<?php
// Kiểm tra xem có ID trên URL không và có phải số không
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	die("Invalid ID parameter.");
}

// Lấy ID cần sửa, ép kiểu về số nguyên cho an toàn
$CategoryID = (int)$_GET['id'];

// Chuẩn bị câu lệnh SQL an toàn
$stmt = $connect->prepare("SELECT * FROM `tbl_categories` WHERE `CategoryID` = ?");
if (!$stmt) {
	die("Prepare failed: " . $connect->error);
}

// Bind tham số vào câu SQL
$stmt->bind_param("i", $CategoryID);

// Thực thi câu lệnh
if (!$stmt->execute()) {
	die("Execute failed: " . $stmt->error);
}

// Lấy kết quả trả về
$result = $stmt->get_result();

// Kiểm tra nếu không tìm thấy dòng nào
if ($result->num_rows === 0) {
	die("No category found with the provided ID.");
}

// Lấy dòng dữ liệu thành mảng liên kết
$dong = $result->fetch_array(MYSQLI_ASSOC);

// Giải phóng tài nguyên
$stmt->close();
?>
<div class="card">
	<div class="card-header">Edit Category</div>
	<div class="table-responsive">

		<!-- Form cập nhật danh mục -->
		<form action="index.php?do=category_edit_process" method="post" class="form">
			<!-- Input ẩn: gửi ID của danh mục cần cập nhật -->
			<input type="hidden" name="CategoryID" value="<?php echo htmlspecialchars($dong['CategoryID']); ?>" />

			<tr>
				<td>Category Name:</td>
				<td>
					<input type="text" name="CategoryName" value="<?php echo htmlspecialchars($dong['CategoryName']); ?>" required />
				</td>
			</tr>
			</table>

			<!-- Nút submit -->
			<input type="submit" value="Update" />
		</form>

	</div>
</div>