<?php
// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối đến cơ sở dữ liệu
include_once "config.php";

// Kiểm tra quyền admin
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 1) {
    die("Access denied!");
}

// Nếu người dùng xác nhận xóa
if (isset($_POST['confirm_delete'])) {
    $UserID = intval($_POST['UserID']);

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql_delete = "DELETE FROM tbl_users WHERE UserID = ?";
    $stmt_delete = $connect->prepare($sql_delete);

    if (!$stmt_delete) {
        echo '<div class="alert alert-danger" role="alert">
                Prepare failed: ' . htmlspecialchars($connect->error) . '
              </div>';
        exit();
    }

    $stmt_delete->bind_param('i', $UserID);

    if ($stmt_delete->execute()) {
        echo '<div class="alert alert-success" role="alert">
                User deleted successfully. Redirecting...
              </div>
              <script>
                setTimeout(function() {
                  window.location.href = "index.php?do=users";
                }, 2000);
              </script>';
        $stmt_delete->close();
        exit();
    } else {
        echo '<div class="alert alert-danger" role="alert">
                Unable to delete the user: ' . htmlspecialchars($stmt_delete->error) . '
              </div>';
        $stmt_delete->close();
    }

} elseif (isset($_GET['id'])) {
    // Hiển thị form xác nhận xóa
    $UserID = intval($_GET['id']);

    if (!$connect) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT UserName, FullName FROM tbl_users WHERE UserID = ?";
    $stmt = $connect->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . htmlspecialchars($connect->error));
    }

    $stmt->bind_param('i', $UserID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo '
        <div class="card mt-4">
            <div class="card-header">Delete User</div>
            <div class="card-body">
                <h5 class="card-title">Are you sure you want to delete this user?</h5>
                <p class="card-text">FullName: ' . htmlspecialchars($user['FullName']) . ' (Username: ' . htmlspecialchars($user['UserName']) . ')</p>
                <form action="" method="post">
                    <input type="hidden" name="UserID" value="' . $UserID . '" />
                    <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete</button>
                    <a href="index.php?do=users" class="btn btn-secondary">No, Cancel</a>
                </form>
            </div>
        </div>';
    } else {
        echo '<div class="alert alert-warning" role="alert">User not found.</div>';
    }

    $stmt->close();

} else {
    // Nếu không có hành động nào hợp lệ, chuyển hướng bằng JavaScript
    echo '<script>window.location.href = "index.php?do=users";</script>';
    exit();
}
?>
