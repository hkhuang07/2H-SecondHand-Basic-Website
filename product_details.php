<?php
// File: product_details.php

require_once 'config.php';
require_once 'library.php';

// Lấy ProductID từ query
$productID = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($productID <= 0) {
    die('<div class="alert alert-warning mt-4">Invalid product ID.</div>');
}

// Truy vấn chi tiết sản phẩm cùng tên danh mục
$sql = "SELECT 
            P.ProductID,
            P.ProductCode,
            P.ProductName,
            C.CategoryName,
            P.Price,
            P.Quantity,
            P.Discount,
            P.Description,
            P.Image,
            P.Config,
            P.View,
            P.PurchaseDate,
            P.PurchaseCount,
            P.FavoriteCount,
            P.Status
        FROM tbl_products P
        LEFT JOIN tbl_categories C ON P.CategoryID = C.CategoryID
        WHERE P.ProductID = ?";

$stmt = $connect->prepare($sql);
$stmt->bind_param('i', $productID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

// Nếu không tìm thấy sản phẩm
if (!$row) {
    echo '<div class="container mt-4"><div class="alert alert-warning">Product not found.</div>';
    echo '<a href="index.php?do=products" class="btn btn-secondary">Back to Product List</a></div>';
    exit;
}

// Tăng lượt xem
$upd = $connect->prepare("UPDATE tbl_products SET View = View + 1 WHERE ProductID = ?");
$upd->bind_param('i', $productID);
$upd->execute();
$upd->close();
?>
<div class="container mt-4">
    <div class="row justify-content-center"> <!-- Thêm dòng này để canh giữa -->
        <div class="col-lg-10"> <!-- Giới hạn chiều rộng nội dung -->
            <div class="card shadow">
                <div class="card-body">
                    <div class="row g-10">
                        <!-- Thông tin chi tiết -->
                        <div class="col-md-8">
                            <div class="card-header"><?php echo htmlspecialchars($row['ProductName']); ?></div>
                            <table class="table reponsive">
                                <tbody>
                                    <tr>
                                        <th>Code</th>
                                        <td><?php echo htmlspecialchars($row['ProductCode']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td><?php echo htmlspecialchars($row['CategoryName']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Price</th>
                                        <td><?php echo number_format($row['Price'], 0, ',', '.'); ?> VND</td>
                                    </tr>
                                    <tr>
                                        <th>Quantity</th>
                                        <td><?php echo htmlspecialchars($row['Quantity']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Discount</th>
                                        <td><?php echo htmlspecialchars($row['Discount']); ?>%</td>
                                    </tr>
                                    <tr>
                                        <th>Views</th>
                                        <td><?php echo htmlspecialchars($row['View']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Purchased</th>
                                        <td><?php echo htmlspecialchars($row['PurchaseCount']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Favorited</th>
                                        <td><?php echo htmlspecialchars($row['FavoriteCount']); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Status</th>
                                        <td>
                                            <?php if ($row['Status'] == 1): ?>
                                                <span class="badge bg-success">In Stock</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Out of Stock</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Purchase Date</th>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['PurchaseDate'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td><?php echo nl2br(htmlspecialchars($row['Description'])); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Ảnh sản phẩm -->
                        <div class="col-md-4 text-center">
                            <img src="images/<?php echo htmlspecialchars($row['Image']); ?>"
                                alt="Product Image" class="img-fluid rounded border"
                                style="max-height:300px;object-fit:contain;">
                        </div>

                        <!-- Hành động -->
                        <div class="mt-4 d-flex justify-content-between">
                            <a href="index.php?do=products" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Product List
                            </a>
                            <!--a href="index.php?do=shoppigncart_add&id=<--?php echo $productID; ?>" class="btn btn-success">
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </a-->

                            <form action="index.php?do=shoppingcart_add" method="post" class="d-flex gap-2">
                                <input type="hidden" name="product_id" value="<?php echo $productID; ?>">
                                <button type="submit" name="action" value="buy" class="btn btn-success">
                                    <i class="bi bi-cart-plus"></i> Add to Cart for Purchase
                                </button>
                               
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- col-lg-10 -->
    </div> <!-- row justify-content-center -->
</div>