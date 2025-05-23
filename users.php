<?php
// Bắt đầu phiên làm việc nếu chưa khởi tạo
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Dùng bộ đệm đầu ra để tránh lỗi "headers already sent"
ob_start();

// Kiểm tra người dùng đã đăng nhập và có quyền là admin (Role = 1)
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 1) {
    die("Access denied!"); // Nếu không, ngăn truy cập
}

// Kết nối đến cơ sở dữ liệu (cần đảm bảo file này tồn tại và đúng đường dẫn)
include_once "config.php";
include_once "library.php"; // Nhúng file thư viện chứa các hàm hỗ trợ

// Câu truy vấn SQL lấy tất cả người dùng
$sql = "SELECT * FROM `tbl_users`";
$list = $connect->query($sql);

// Kiểm tra lỗi trong quá trình thực hiện truy vấn
if (!$list) {
    die("SQL Error: " . $connect->error);
}

// Kết thúc bộ đệm đầu ra (không bắt buộc nếu đã exit trước đó, nhưng tốt để an toàn)
ob_end_flush();
?>


<!-- Giao diện bảng danh sách người dùng -->
<div class="card">
    <div class="card-header">
        Users List
    </div>

    <div class="table-responsive">
        <!-- Liên kết đến trang thêm người dùng mới -->
        <div style="padding: 2px;">
            <a href="index.php?do=signup" class="btn btn-success button">Add New User
            </a>
        </div>

        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Avatar</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>User Name</th>
                    <th>Role</th>
                    <th colspan="3">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $list->fetch_array(MYSQLI_ASSOC)) { ?>
                    <tr>
                        <td><?= $row["UserID"] ?></td>

                        <!-- Avatar -->
                        <td style="width: 180px;">
                            <img src="./images/<?= htmlspecialchars($row['Avatar']) ?>" alt="Avatar" class="img-thumbnail" style="width: 150px; height: 180px; object-fit: cover;">
                        </td>

                        <!-- Full name -->
                        <td><?= htmlspecialchars($row["FullName"]) ?></td>

                        <!-- Email -->
                        <td><?= htmlspecialchars($row["Email"]) ?></td>

                        <!-- Phone -->
                        <td><?= htmlspecialchars($row["Phone"]) ?></td>

                        <!-- Address -->
                        <td><?= nl2br(htmlspecialchars($row["Address"])) ?></td>

                        <!-- Username -->
                        <td><?= htmlspecialchars($row["UserName"]) ?></td>

                        <!-- Role -->
                        <td>
                            <?php if ($row["Role"] == 1): ?>
                                Administrator (<a href="index.php?do=user_enable&id=<?= $row["UserID"] ?>&role=2">Demote</a>)
                            <?php else: ?>
                                Member (<a href="index.php?do=user_enable&id=<?= $row["UserID"] ?>&role=1">Promote</a>)
                            <?php endif; ?>
                        </td>

                        <!-- Actions -->
                        <td align="center">
                            <?php if ($row["Key"] == 0): ?>
                                <a href="index.php?do=user_enable&id=<?= $row["UserID"] ?>&key=1" title="Activate account"><img src="images/active.png" alt="Activate"></a>
                            <?php else: ?>
                                <a href="index.php?do=user_enable&id=<?= $row["UserID"] ?>&key=0" title="Block account"><img src="images/ban.png" alt="Block"></a>
                            <?php endif; ?>
                        </td>
                        <td align="center">
                            <a href="index.php?do=user_edit&id=<?= $row["UserID"] ?>"><img src="images/edit.png" alt="Edit"></a>
                        </td>
                        <td align="center">
                            <a href="index.php?do=user_delete&id=<?= $row["UserID"] ?>"><img src="images/delete.png" alt="Delete"></a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>