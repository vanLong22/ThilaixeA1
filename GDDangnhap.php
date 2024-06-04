<?php
	session_start();
		
	if (isset($_POST['taikhoan'])) {
		// lay du lieu tu form(tai khoan, mat khau duoc nhap vao)
		$tk = $_POST['taikhoan'];
		$mk = $_POST['matkhau'];
		
		// ket noi du lieu
		$kn = mysqli_connect("localhost", "root", "", "thilaixea1");
		
		// xây dựng truy vấn thông tin admin 
		$strlenhAdmin = "select * from tkadmin where username = '".$tk."' and password = '".$mk."' ";
		$kqAdmin = mysqli_query($kn, $strlenhAdmin);
		// lay ket qua tra ve 
		$dongAdmin = mysqli_fetch_array($kqAdmin);
		if (mysqli_num_rows($kqAdmin) > 0) { // nếu người đăng nhập là admin
			$_SESSION['tdnAdmin'] = $dongAdmin['username'];
			header('location:GDadmin.php');
		}
		else{ // nếu người đăng nhập là thí sinh
			// xay dung truy van thông tin thí sinh 
			$strlenhThiSinh = "select * from thongtinthisinh where username = '".$tk."' and password = '".$mk."' ";
			// thuc hien cau lenh truy van
			$kqThiSinh = mysqli_query($kn, $strlenhThiSinh);
			// lay ket qua tra ve 
			$dongThiSinh = mysqli_fetch_array($kqThiSinh);
			if (mysqli_num_rows($kqThiSinh) <= 0) {
				$errorMessage = "Đăng nhập với mật khẩu hoặc tài khoản không đúng.";
			}else {
				$_SESSION['tdnThiSinh'] = $dongThiSinh['username'];
				header('location:GDtrangchu.php');
			}			
		}
		
		// dong ket noi, bo qua buoc don dep tai nguyen
		mysqli_close($kn);
	}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 10px;
            text-align: center;
            width: 300px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
		/** thiết lập kiểu dáng cho các trường nhập liệu*/
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="checkbox"] {
            margin: 20px 0;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-dangky {
			width: 100%; /* Chiều rộng của nút chiếm toàn bộ chiều rộng của phần tử cha */
			padding: 10px; /* Độ rộng bên trong nút */
			border: none; /* Loại bỏ viền */
			border-radius: 5px; /* Bo tròn góc của nút */
			color: white; /* Màu chữ trắng */
			cursor: pointer; /* Biểu tượng con trỏ khi di chuột qua nút */
			font-size: 16px; /* Kích thước chữ */
			background-color: #28a745; /* Màu nền của nút (màu xanh lá cây) */
			margin-top: 10px; /* Khoảng cách phía trên của nút */
        }
		/** căn trái checkbox */
        .form-luumk {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Đăng nhập</h1>
		<?php if (isset($errorMessage)): ?>
            <div style="color: red;"><?php echo $errorMessage; ?></div>
        <?php endif; ?>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
            <input type="text" name="taikhoan" placeholder="Tài khoản" required>
            <input type="password" name="matkhau" placeholder="Mật khẩu" required>
            <div class="form-luumk">
                <input type="checkbox" name="luumk">
                <label><i>Lưu mật khẩu</i></label>
            </div>
            <input type="submit" name="login" value="Đăng nhập">
        </form>
        <form action="GDdangky.php" method="POST">
            <button type="submit" class="btn-dangky">Đăng ký</button>
        </form>
    </div>
</body>
</html>
