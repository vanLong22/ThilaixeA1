<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
	<link rel="stylesheet" href="style.css">
    <style>
		.mota {
            display: flex; /* Sử dụng mô hình Flexbox */
            justify-content: center; /* Căn giữa các phần tử con theo chiều ngang */
            background-color: #f0f0f0; /* Màu nền của phần tử */
        }

    </style>
</head>
<body>
	<!-- Header gồm logo, name, và button đăng nhập-->
    <div class="header">
        <a href="GDadmin.php">
            <img src="https://th.bing.com/th/id/OIP.mx3kx2bpORHJwWuYx1hXwwHaHa?pid=ImgDet&w=200&h=200&c=7&dpr=1.3" style="width: auto; height: 50px;" alt="Logo">
        </a>
        <div class="name">HỆ THỐNG QUẢN LÝ THI LÁI XE HẠNG A1</div>
		<div>
			<?php
            session_start();
            if (!isset($_SESSION['tdnAdmin'])) {
                $loggedIn = false;
            ?>
				<button style="background-color: blue;" onclick="window.location.href='GDDangnhap.php'">Đăng nhập</button>
            <?php
            } else {
                $loggedIn = true;
            ?>
				<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
					<?php echo $_SESSION['tdnAdmin']; ?>
					<button class="btn-exit" type="submit" name="dangxuat" >Đăng xuất</button>
				</form>
            <?php
            }
            ?>
		</div>
    </div>

    <!-- Đăng nhập để sử dụng các chức năng này -->
    <div class="menu-container">
        <div class="menu">
			<?php if ($loggedIn) { ?>
                <form action="GDthongke.php" method="POST" >
					<button type="submit" >Thống kê</button>
				</form>
				<form action="GDtimkiem.php" method="POST" >
					<button type="submit" >Tìm kiếm</button>
				</form>
				<form action="GDcapnhap.php" method="POST" >
					<button type="submit" >Cập nhập</button>
				</form>
            <?php } else { ?>
                <div>
                    <p>Bạn cần phải đăng nhập để sử dụng chức năng này.</p>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Mô tả content -->
    <div class="mota">
        <h2>Chào mừng bạn đến với hệ thống</h2>
    </div>
</body>
</html>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dangxuat'])) {
	session_unset(); // Xóa tất cả các biến session
	session_destroy(); // Hủy session
	header("Location: GDadmin.php"); // Chuyển hướng người dùng về trang admin
	exit();
}
?>






