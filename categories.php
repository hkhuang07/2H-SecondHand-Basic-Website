<?php
// Câu lệnh SQL lấy tất cả dữ liệu từ bảng `tbl_categories`
$sql = "SELECT * FROM `tbl_categories` WHERE 1";
// Thực thi câu lệnh SQL và lưu kết quả vào biến $list
$list = $connect->query($sql);
// Nếu truy vấn thất bại (biến $list là false) thì báo lỗi và dừng chương trình
if (!$list) {
	die("Unable to execute  SQL statement " . $connect->connect_error);
	exit();
}
?>
<div class="card-header">Categories List</div>
<!-- DanhSach -->

<div class="table-responsive">

	<a href="index.php?do=category_add" class="btn btn-success button" style="padding: 2px;">
		<i class="bi bi-plus-lg"></i> Add New
	</a>

	<table class="table table-striped table-bordered align-middle"> <!-- Bảng chứa danh sách các danh mục -->
		<tr> <!-- Dòng tiêu đề bảng -->
			<th width="30%">Category ID</th> <!-- Cột mã danh mục -->
			<th width="55%">Category Name</th> <!-- Cột tên danh mục -->
			<th width="15%" colspan="2">Action</th> <!-- Cột hành động: sửa và xóa -->
		</tr>
		<?php
		// Vòng lặp duyệt từng dòng dữ liệu lấy từ bảng tbl_categories
		while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
			// Bắt đầu một dòng (row) mới trong bảng
			echo "<tr  bgcolor='#ffffff' 
				onmouseover='this.style.background=\"#dee3e7\"' 
				onmouseout='this.style.background=\"#ffffff\"'>";

			// Cột 1: Hiển thị CategoryID
			echo "<td>" . $row["CategoryID"] . "</td>";

			// Cột 2: Hiển thị CategoryName
			echo "<td>" . $row["CategoryName"] . "</td>";

			// Cột 3: Nút Sửa (Edit)
			echo "<td align='center'>
					<a href='index.php?do=category_edit&id=" . $row["CategoryID"] . "'>
						<img src='images/edit.png' />
					</a>
				</td>";

			// Cột 4: Nút Xóa (Delete) - có confirm khi nhấn
			echo "<td align='center'>
					<a href='index.php?do=catregory_delete&id=" . $row["CategoryID"] . "' >
						<img src='images/delete.png' />
					</a>
				</td>";

			echo "</tr>"; // Kết thúc dòng
		}
		?>
	</table>
</div> <!-- Kết thúc bảng danh sách -->
</form>