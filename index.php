<?php
ob_start(); // Khởi tạo ghi đệm ở đầu file

//session_set_cookie_params(30); // 1800 giây = 30 phút
session_start();


include_once "config.php";

include_once "library.php";

// Tách riêng xử lý điều hướng nếu là user_enable
if (isset($_GET['do']) && $_GET['do'] === 'user_enable') {
	include "user_enable.php"; // không include qua `Admin/` vì bạn đã ở trong Admin/
	exit; // Bắt buộc exit để không tiếp tục load HTML bên dưới
}

ob_end_flush(); // Cuối file

?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<!--Bootstrap-->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
	<!--Jquery-->
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="scripts/ckeditor/ckeditor.js"></script>
	<!-- Ckeditor -->
	<script src="ckeditor/ckeditor.js"></script>

	<link rel="stylesheet" type="text/css" href="./css/style.css" />

	<title>2H Secondhand Storage</title>
	<meta charset="utf-8" />
</head>

<body>
	<div class="container-fluid">
		<div id="Header">
		</div>
		<div id="navbar">
			<?php include "inc/navbar.php"; ?>
		</div>
		<div class="card mt-3">
			<div class="card-body">

				<div id="MiddlePart">
					<div id="Left" class="sidebar">
						<ul class="nav flex-column text-white">

							<li class="nav-item ">
								<a class="nav-link text-white d-flex align-items-center" href="index.php" aria-expanded="false">
									<i class="bi bi-house-door me-2"></i> <span class="menu-label"> Home</span>
								</a>
							</li>


							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="bi bi-gear-fill me-2"></i> <span class="menu-label">Dashboard</span>
								</a>
								<ul class="dropdown-menu bg-dark border-0">
									<?php
									if (!isset($_SESSION['UserID'])) {
										echo '<li><a class="dropdown-item text-white" href="index.php?do=signin">Sign In</a></li>';
										echo '<li><a class="dropdown-item text-white" href="index.php?do=signup">Sign Up</a></li>';
									} else {
										echo '<li><a class="dropdown-item text-white" href="index.php?do=shoppingcart">My Shopping Card</a></li>';
										echo '<li><a class="dropdown-item text-white" href="index.php?do=product_add">Create Your Products</a></li>';

										if ($_SESSION['Role'] == 1) {
											echo '<li><a class="dropdown-item text-white" href="index.php?do=categories">Categories List</a></li>';
											echo '<li><a class="dropdown-item text-white" href="index.php?do=products">Products List</a></li>';
											echo '<li><a class="dropdown-item text-white" href="index.php?do=users">Users List</a></li>';
										}
									}
									?>
								</ul>
							</li>

							<?php if (isset($_SESSION['UserID'])): ?>
								<li class="nav-item dropdown">
									<a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
										<i class="bi bi-person-circle me-2"></i> <span class="menu-label">Profile</span>
									</a>
									<ul class="dropdown-menu bg-dark border-0">
										<li><a class="dropdown-item text-white" href="index.php?do=personal_profile">Personal Profile</a></li>
										<li><a class="dropdown-item text-white" href="index.php?do=changepass">Change Password</a></li>
									</ul>
								</li>
							<?php endif; ?>

							<li class="nav-item dropdown">
								<a class="nav-link dropdown-toggle text-white d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
									<i class="bi bi-three-dots me-2"></i> <span class="menu-label">More</span>
								</a>
								<ul class="dropdown-menu bg-dark border-0">
									<li><a class="dropdown-item text-white" href="#">Help</a></li>
									<li><a class="dropdown-item text-white" href="index?do=home">Web Information</a></li>
								</ul>
							</li>

						</ul>
					</div>
					<!-- Phần giữa -->
					<div id="Middle" class="container-fluid w-100">
						<?php

						if (isset($_GET['msg']) && $_GET['msg'] == 'updated') {
							Message("Product has been successfully updated!");
						}
						$do = isset($_GET['do']) ? $_GET['do'] : "homepage";
						if (file_exists($do . ".php")) {
							include $do . ".php";
						} else {
							echo "<div class='alert alert-danger'>Page not found!</div>";
						}
						?>

					</div>

					<!-- Quảng cáo bên phải -->
					<div id="Right">
						<div class="ads-container">
							<img src="images/clothes.gif" alt="Advertisement" />
							<img src="images/clothes1.gif" alt="Advertisement" />
							<img src="images/clothes2.gif" alt="Advertisement" />
							<img src="images/fashion.gif" alt="Advertisement" />
							<img src="images/dienmayxanh.gif" alt="Advertisement" />
							<img src="images/grap.gif" alt="Advertisement" />
							<img src="images/iphone.gif" alt="Advertisement" />
							<img src="images/shopee.gif" alt="Advertisement" />
						</div>
					</div>

				</div>
			</div>
			<div id="product-list"></div>
			<div id="footer"></div>
		</div>

	</div>

	<script>
		$(function() {
			$("#navbar").load("inc/navbar.html", function() {
				// callback sau khi navbar load xong
				// kích hoạt lại các event của Bootstrap (nếu cần)
			});
			$("#footer").load("inc/footer.html");
		});
	</script>

</body>

</html>