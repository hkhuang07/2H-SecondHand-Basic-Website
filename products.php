<?php
require_once 'config.php';
require_once 'library.php';

// Lấy danh sách sản phẩm kèm tên danh mục
$sql = "
    SELECT 
        p.ProductID,
        p.ProductCode,
        p.ProductName,
        c.CategoryName,
        p.Price,
        p.Quantity,
        p.Discount,
        p.Description,
        p.Image,
        p.Config,
        p.View,
        p.PurchaseDate,
        p.PurchaseCount,
        p.FavoriteCount,
        p.Status
    FROM tbl_products p
    LEFT JOIN tbl_categories c ON p.CategoryID = c.CategoryID
    ORDER BY p.ProductID DESC
";
$stmt = $connect->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="card">
    <div class="card-header">
        Product List
    </div>

    <div class="table-responsive">
        <!-- Liên kết đến trang thêm người dùng mới -->
        <div style="padding: 2px;">
            <a href="index.php?do=product_add" class="btn btn-success button">Add New
            </a>
        </div>

        <table class="table table-striped table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Product Code</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Purchased</th>
                    <th>View</th>
                    <th>Status</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['ProductID']) ?></td>
                        <td><?= htmlspecialchars($row['ProductCode']) ?></td>
                        <td>
                            <a href="index.php?do=product_details&id=<?= $row['ProductID'] ?>">
                                <?= htmlspecialchars($row['ProductName']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($row['CategoryName']) ?></td>
                        <td><?= number_format($row['Price'], 0, '.', ',') ?></td>
                        <td><?= htmlspecialchars($row['Quantity']) ?></td>
                        <td><?= htmlspecialchars($row['PurchaseCount']) ?></td-->
                        <td><?= htmlspecialchars($row['View']) ?></td>
                        <td>
                            <?php if ($row['Status'] == 1): ?>
                                <span class="badge bg-success">In stock</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Out of stock</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <img src="images/<?= htmlspecialchars($row['Image']) ?>"
                                alt="Image" style="width:50px;height:50px;object-fit:cover;">
                        </td>
                        <td>
                            <a href="index.php?do=shoppingcart_add&id=<?= $row['ProductID'] ?>"
                                class="btn btn-success">
                                <i class="bi bi-cart-plus"></i>
                            </a>
                            <a href="index.php?do=product_edit&id=<?= $row['ProductID'] ?>"
                                class="btn btn-sm btn-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="index.php?do=product_delete&id=<?= $row['ProductID'] ?>"
                                class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>



<!--?php
// 1. Tạo câu lệnh SQL để lấy thông tin sản phẩm và danh mục sản phẩm
// Sử dụng cú pháp JOIN hiện đại để kết nối bảng `tbl_products` và `tbl_categories`
// Điều này giúp câu lệnh dễ hiểu hơn và tránh các vấn đề bảo trì sau này
$sql = "SELECT P.*, C.CategoryName
            FROM tbl_products P
            JOIN tbl_categories C ON P.CategoryID = C.CategoryID
            ORDER BY P.ProductID DESC";

// 2. Thực thi câu lệnh SQL
$list = $connect->query($sql);

// 3. Kiểm tra nếu truy vấn không thành công, hiển thị thông báo lỗi và thoát
if (!$list) {
    die("Unable to: " . $connect->connect_error); // Lỗi trong câu truy vấn SQL
    exit();
}
?-->

<!-- 4. Hiển thị danh sách sản phẩm trong bảng >
<div class="card-header">Products List</div>
<div class="table-responsive">
    <table class="table table-striped table-bordered align-middle">
        <tr>
            <th>Index</th>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Image</th>
            <th colspan="2">Action</th>
        </tr>
        <--?php
        // 5. Khởi tạo biến index để đánh số thứ tự cho các sản phẩm
        $index = 1;

        // 6. Lặp qua tất cả các sản phẩm trong kết quả truy vấn
        while ($row = $list->fetch_array(MYSQLI_ASSOC)) {
            echo "<tr>";

            // 7. Hiển thị dữ liệu sản phẩm, bao gồm các thông tin như ProductID, ProductName, CategoryName và Image
            echo "<td>" . $index . "</td>";
            echo "<td>" . $row['ProductID'] . "</td>";

            // 8. Sử dụng đúng giá trị của $row['ProductID'] để tạo liên kết chi tiết sản phẩm
            echo "<td><a href='index.php?do=product_details&id=" . $row['ProductID'] . "'>" . $row['ProductName'] . "</a></td>";
            echo "<td>" . $row['CategoryName'] . "</td>";
            echo "<td><img src='" . $row['Image'] . "' width='100'/></td>";

            // 9. Liên kết chỉnh sửa và xóa sản phẩm
            echo "<td align='center'><a href='index.php?do=product_edit&id=" . $row['ProductID'] . "'><img src='images/edit.png' /></a></td>";
            echo "<td align='center'><a href='index.php?do=product_delete&id=" . $row['ProductID'] . "' onclick='return confirm(\"Do you want to delete the post " . $row['ProductName'] . " ?\")'><img src='images/delete.png' /></a></td>";

            echo "</tr>";
            $index++; // Tăng số thứ tự
        }
        ?>
    </table>
</div>
<! 10. Thêm liên kết để thêm sản phẩm mới >
<a href="index.php?do=product_add">Add Product</a-->