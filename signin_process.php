<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("config.php"); // Đảm bảo bạn có file kết nối $connect
require_once("library.php");  // Chứa hàm ErrorMessage

// Dùng bộ đệm đầu ra để tránh lỗi "headers already sent"
ob_start();


// Lấy thông tin từ FORM (UserName và Password)
// Lấy giá trị tên người dùng
$UserName = trim($_POST['UserName'] ?? '');
// Lấy giá trị mật khẩu
$Password = $_POST['Password'] ?? '';

// Kiểm tra nếu UserName hoặc Password để trống
if (trim($UserName) == "")  // Kiểm tra nếu UserName trống
	ErrorMessage("UserName cannot be left blank!");  // Hiển thị thông báo lỗi
elseif (trim($Password) == "")  // Kiểm tra nếu Password trống
	ErrorMessage("Password cannot be left blank!");  // Hiển thị thông báo lỗi
else  // Nếu cả 2 trường UserName và Password đều có giá trị
{
	$stmt = $connect->prepare("SELECT * FROM tbl_users WHERE UserName = ?");  // Chuẩn bị câu lệnh SQL với tham số
	$stmt->bind_param("s", $UserName);  // Gán giá trị cho tham số UserName
	$stmt->execute();  // Thực thi câu lệnh SQL
	$result = $stmt->get_result();  // Lấy kết quả trả về từ câu lệnh SQL

	if ($result && $row = $result->fetch_assoc()) {
		if (password_verify($Password, $row['Password'])) {
			if ($row['Key'] == 0) {
				$_SESSION['UserID'] = $row['UserID'];
				$_SESSION['FullName'] = $row['FullName'];
				$_SESSION['Role'] = $row['Role'];
				$_SESSION['Email'] = $row['Email'];
				$_SESSION['Phone'] = $row['Phone'];

				Redirect("index.php"); // <-- dùng Redirect thay cho header

			} else {
				// Nếu tài khoản bị khóa, hiển thị thông báo lỗi
				ErrorMessage("User account has been locked !");
			}
		} else {
			// Nếu mật khẩu không đúng, hiển thị thông báo lỗi
			ErrorMessage("Incorrect username or password!");
		}
	} else {
		// Nếu không có tài khoản phù hợp, hiển thị thông báo lỗi
		ErrorMessage("Incorrect username or password!");
	}
}

// Kết thúc bộ đệm đầu ra (không bắt buộc nếu đã exit trước đó, nhưng tốt để an toàn)
ob_end_flush();
