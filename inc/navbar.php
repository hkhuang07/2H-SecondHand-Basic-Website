<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: midnightblue;">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php?do=homepage">
      <i class="bi bi-cart-dash-fill"></i> 2H Secondhand Store
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!-- Trang chính -->
        <li class="nav-item">
          <a class="nav-link active" href="index.php">
            <i class="bi bi-house-door"></i> Home
          </a>
        </li>

        <?php if (isset($_SESSION['UserID'])): ?>
          <?php if (!empty($_SESSION['Role']) && $_SESSION['Role'] == 1): ?>
            <!-- Quản trị viên -->
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-gear"></i> Management
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="index.php?do=categories"><i class="bi bi-tags"></i> Categories</a></li>
                <li><a class="dropdown-item" href="index.php?do=products"><i class="bi bi-bag-dash"></i> Products</a></li>
                <li><a class="dropdown-item" href="index.php?do=users"><i class="bi bi-people"></i> Users</a></li>
              </ul>
            </li>
          <?php endif; ?>

          <!-- Người dùng đã đăng nhập -->
          <li class="nav-item">
            <a class="nav-link" href="index.php?do=shoppingcart">
              <i class="bi bi-bag-dash"></i> Shopping Cart
            </a>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person"></i> <?= htmlspecialchars($_SESSION['FullName']) ?>
            </a>

            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="index.php?do=product_add"><i class="bi bi-bag-dash"></i> Create Your Products</a></li>
              <li><a class="dropdown-item" href="index.php?do=personal_profile"><i class="bi bi-person-circle me-2"></i> Personal Profile</a></li>
              <li><a class="dropdown-item" href="index.php?do=changepass"><i class="bi-lock	"></i> Change Password</a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="index.php?do=signout">
              <i class="bi bi-box-arrow-right"></i> Log out
            </a>
          </li>
        <?php else: ?>
          <!-- Khách chưa đăng nhập -->
          <li class="nav-item">
            <a class="nav-link" href="index.php?do=signup">
              <i class="bi bi-person-add"></i> Sign up
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="index.php?do=signin">
              <i class="bi bi-box-arrow-in-right"></i> Sign in
            </a>
          </li>
        <?php endif; ?>
      </ul>

      <!-- Thanh tìm kiếm >
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Find product" aria-label="Search" />
        <button class="btn btn-outline-success" type="submit">
          <i class="bi bi-search"></i>
        </button>
      </form-->
      <!-- Thanh tìm kiếm -->
      <form class="d-flex" method="GET" action="index.php?do=search">
        <input type="hidden" name="do" value="search">
        <input class="form-control me-2" type="search" name="keyword" placeholder="Find product" aria-label="Search" required>
        <button class="btn btn-outline-success" type="submit">
          <i class="bi bi-search"></i>
        </button>
      </form>
    </div>
  </div>
</nav>