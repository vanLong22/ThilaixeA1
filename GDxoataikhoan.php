<?php
    $errorMessage = "";
    $successMessage = false;
	
    if (isset($_POST['username'])) {
        $tendangnhap = $_POST['username'];

        // Kiểm tra xem tên đăng nhập đã tồn tại hay chưa
        $connect = mysqli_connect("localhost", "root", "", "thilaixea1");
        $kiemtra = "SELECT * FROM thongtinthisinh WHERE username='$tendangnhap'";
        $result = mysqli_query($connect, $kiemtra);

        if (mysqli_num_rows($result) <= 0) {
            $errorMessage = "Không tồn tại tài khoản này";
        } else {
			// xóa tài khoản
			$xoaTK = "DELETE FROM thongtinthisinh WHERE username='$tendangnhap' ";
			if ($connect->query($xoaTK) === TRUE) {
				$successMessage = true;
			} else {
				$errorMessage = "Lỗi: " . $connect->error;
			}
        }
    }
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Tài Khoản</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 500px;
            text-align: center;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .btn {
            padding: 10px 20px;
            background-color: #dc3545;
            border: none;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #c82333;
			text-decoration: underline;
        }
        .btn-cancel {
            background-color: #007bff;
            margin-right: 10px;
			text-decoration: none;
        }
        .btn-cancel:hover {
			text-decoration: underline;
            background-color: #0056b3;
        }
    </style>
	
	<script>
        function showAlertAndRedirect() {
            alert("Xóa tài khoản thành công");
            window.location.href = 'GDxoataikhoan.php';
        }
    </script>
	
</head>
<body>
    <div class="container">
        <h1>Xóa Tài Khoản</h1>
        <p>Nhập tên người dùng để xác nhận xóa tài khoản:</p>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?> " method="POST">
			<?php if ($errorMessage): ?>
				<div style="color: red;"><?php echo $errorMessage; ?></div>
			<?php elseif ($successMessage): ?>
				<script>showAlertAndRedirect();</script>
			<?php endif; ?>
			
            <input type="text" name="username" placeholder="Tên người dùng" required>
            <button type="submit" class="btn ">Xác nhận xóa</button>
            <a href="GDadmin.php" class="btn btn-cancel">Hủy</a>
        </form>
    </div>
</body>
</html>
