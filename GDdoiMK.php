<?php
    $errorMessage = "";
    $successMessage = false;
    if (isset($_POST['username'])) {
        $tendangnhap = $_POST['username'];
        $matkhaumoi = $_POST['matkhau'];
        $nhaplaimatkhau = $_POST['nhaplaimatkhau'];

        // Kiểm tra xem tên đăng nhập đã tồn tại hay chưa
        $connect = mysqli_connect("localhost", "root", "", "thilaixea1");
        $kiemtra = "SELECT * FROM thongtinthisinh WHERE username='$tendangnhap'";
        $result = mysqli_query($connect, $kiemtra);

        if (mysqli_num_rows($result) <= 0) {
            $errorMessage = "Không tồn tại tên đăng nhập này";
        } else {
            if ($matkhaumoi != $nhaplaimatkhau) {
                $errorMessage = "Nhập lại mật khẩu không đúng";
            } else {
                // Đổi mật khẩu
                $doiMK = "UPDATE thongtinthisinh SET password='".($matkhaumoi)."' WHERE username='$tendangnhap' ";
                if ($connect->query($doiMK) === TRUE) {
                    $successMessage = true;
                } else {
                    $errorMessage = "Lỗi: " . $connect->error;
                }
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu</title>
    <style>
        /* Đặt kiểu dáng cho toàn bộ trang */
        body {
            display: flex; /* Đặt phần tử body sử dụng mô hình flexbox */
            justify-content: center; /* Căn giữa các phần tử con theo chiều ngang */
            align-items: center; /* Căn giữa các phần tử con theo chiều dọc */
            height: 100vh; /* Đặt chiều cao của body bằng 100% chiều cao của viewport (khung nhìn) */
            font-family: 'Arial', sans-serif; /* Thiết lập font chữ cho văn bản trong body */
            background-color: #f0f0f0; /* Đặt màu nền cho body */
        }

        /* Đặt kiểu dáng cho form đăng ký */
        .register-form {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            width: 320px;
            box-sizing: border-box;
        }
        .register-form h2 {
            margin-bottom: 25px; /* Khoảng cách giữa tiêu đề và các phần tử khác */
            text-align: center; /* Căn giữa tiêu đề */
            font-size: 24px; /* Cỡ chữ cho tiêu đề */
            color: #333; /* Màu chữ cho tiêu đề */
        }
        .register-form input {
            width: 100%; /* Chiều rộng của các ô nhập liệu */
            padding: 12px; /* Khoảng cách giữa nội dung trong ô nhập liệu và viền của ô */
            margin: 10px 0; /* Khoảng cách giữa các ô nhập liệu */
            border: 1px solid #ccc; /* Viền của ô nhập liệu */
            border-radius: 5px; /* Đường cong viền của ô nhập liệu */
            box-sizing: border-box; /* Thiết lập kích thước của ô nhập liệu để bao gồm viền và padding */
        }

        .register-form button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            box-sizing: border-box;
        }
    </style>
    <script>
        function showAlertAndRedirect() {
            alert("Cập nhật mật khẩu thành công");
            window.location.href = 'GDDangnhap.php';
        }
    </script>
</head>
<body>
    <!-- Form đăng ký -->
    <form class="register-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h2>Đổi mật khẩu</h2>
        <?php if ($errorMessage): ?>
            <div style="color: red;"><?php echo $errorMessage; ?></div>
        <?php elseif ($successMessage): ?>
            <script>showAlertAndRedirect();</script>
        <?php endif; ?>
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="matkhau" placeholder="Mật khẩu mới" required>
        <input type="password" name="nhaplaimatkhau" placeholder="Nhập lại mật khẩu" required>
        <button type="submit">Cập nhật</button>
    </form>
</body>
</html>
