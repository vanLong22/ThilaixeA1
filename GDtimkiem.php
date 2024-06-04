<?php
session_start(); // Bắt đầu phiên làm việc để sử dụng session

// Kết nối cơ sở dữ liệu
$connect = mysqli_connect("localhost", "root", "", "thilaixea1");

// Kiểm tra xem form có được submit chưa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["tenThiSinh"])) {
    $tenThiSinh = $_POST['tenThiSinh']; // Lấy tên thí sinh từ form

    // Truy vấn kết quả thí sinh từ cơ sở dữ liệu
    $query = "SELECT * FROM thongtinthisinh WHERE username='$tenThiSinh'";
    $result = mysqli_query($connect, $query);

    // Nếu có kết quả, lấy thông tin thí sinh
	$row = mysqli_fetch_array($result);
	if (!empty($row['username']) && $row['KetQua'] === "") {
		// nếu thí sinh chưa thi 
		$ketQuaThi = 0;
	}
	else if(mysqli_num_rows($result) <= 0){
		// không tồn tại thí sinh này
		$ketQuaThi = -1;
	}
    else { // nếu thí sinh đã thi thì lấy kết quả thi 
        $soCauDung = intval($row['SoCTLDung']); // lấy số câu trả lời đúng từ db và chuyển thành số nguyên
        $soCauSai = 25 - $soCauDung; // tính số câu sai
        $ketQuaThi = $row['KetQua']; // Lấy kết quả thi (Đậu hoặc Trượt)
    }
}

// Đóng kết nối cơ sở dữ liệu
mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị - Xem Kết Quả Thí Sinh</title>
    <link rel="stylesheet" href="style.css"> <!-- Liên kết đến tệp CSS -->
    <style>
        .container {
            text-align: center; /* Căn giữa nội dung trong container */
            margin-top: 50px; /* Tạo khoảng cách trên cùng của trang */
        }

        .result-table {
            width: 100%; /* Đặt chiều rộng của bảng là 50% */
            margin: 20px auto; /* Căn giữa bảng */
            border-collapse: collapse; /* Gộp các đường viền của bảng */
        }

        .result-table th, .result-table td {
            border: 1px solid #ddd; /* Đặt viền cho các ô trong bảng */
            padding: 8px; /* Tạo khoảng cách bên trong các ô */
            text-align: center; /* Căn giữa văn bản trong các ô */
        }

        .exit {
            top: 20px;
            right: 20px;
            background-color: red;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
    </style>
</head>
<body>
	<div class="header">
		<a href="GDadmin.php">
			<img src="https://th.bing.com/th/id/OIP.mx3kx2bpORHJwWuYx1hXwwHaHa?pid=ImgDet&w=200&h=200&c=7&dpr=1.3" style="width: auto; height: 50px;" alt="Logo">
		</a>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<?php echo $_SESSION['tdnAdmin']; ?>
			<button class="btn-exit" type="submit" name="dangxuat" >Đăng xuất</button>
		</form>
	</div>
	
    <div class="container">
        <h1>Quản Trị - Xem Kết Quả Thí Sinh</h1>
        <div class="form-container">
            <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <label>Nhập tên thí sinh:</label>
				<input type="text" id="tenThiSinh" name="tenThiSinh" <?php if(isset($tenThiSinh)): ?> value="<?php echo $tenThiSinh; ?>" <?php endif; ?>>
                <button type="submit">Xem kết quả</button>
            </form>
        </div>

        <!-- Hiển thị kết quả nếu tìm thấy thí sinh -->
		<?php if (isset($ketQuaThi) && $ketQuaThi === 0) : ?>
				<label for="query">Thí sinh chưa thi</label>
		<?php elseif (isset($ketQuaThi) && $ketQuaThi === -1) : ?>
				<label for="query">Thí sinh không tồn tại</label>
		<?php elseif (isset($ketQuaThi)): ?>
            <table class="result-table">
                <tr>
                    <th>Tên thí sinh</th>
                    <th>Kết quả</th>
                    <th>Số câu đúng</th>
                    <th>Số câu sai</th>
                </tr>
                <tr>
					<td><?php echo $row['username']; ?></td>
					<td><?php echo $ketQuaThi; ?></td>
					<td><?php echo $soCauDung; ?></td>
					<td><?php echo $soCauSai; ?></td>
				</tr>
            </table>
		<?php endif; ?>
    </div>
</body>
</html>
 
 