<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dangxuat'])) {
	session_unset(); // Xóa tất cả các biến session
	session_destroy(); // Hủy session
	header("Location: GDtrangchu.php"); // Chuyển hướng người dùng về trang chủ
	exit();
}

// Xử lý dữ liệu lấy kết quả thi 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ketqua'])) {
    $data = explode(',', $_POST['ketqua']);
    $cauDung = $data[0];
    $cauDiemLiet = $data[1];
}
else{
	echo "Không tìm thấy form được gửi tới";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem kết quả thi </title>
	<link rel="stylesheet" href="style.css">
	<style>
		.thongBao{
			display: flex;
            flex-direction: column;
            height: 10vh; /* Chiều cao 100% viewport để nội dung ở giữa trang */
            text-align: center; /* Căn giữa nội dung văn bản */
		}
		.btnxemlaibaithi{
			background-color: #4CAF50;
            border: none; /* loại bỏ các đường viền nào xung quanh nút */
            color: white;
            padding: 15px 32px; /*khoảng cách giữa chữ của nút và đường viền của nó */
            text-align: center; /* canh giữa văn bản bên trong nút theo chiều ngang */
            font-size: 16px;
            margin: 4px 2px; /*khoảng cách giũa các nút */
			border-radius: 8px; /* bo góc tròn */
		}
	</style>
</head>
<body>
    <div class="thongBao">
		<div class="header">
			<a href="GDtrangchu.php">
				<img src="https://th.bing.com/th/id/OIP.mx3kx2bpORHJwWuYx1hXwwHaHa?pid=ImgDet&w=200&h=200&c=7&dpr=1.3" style="width: auto; height: 50px;" alt="Logo">
			</a>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<?php echo $_SESSION['tdnThiSinh']; ?>
				<button class="btn-exit" type="submit" name="dangxuat" >Đăng xuất</button>
			</form>
        </div>
        <!-- Tính mã đề -->
		<?php
			$phanNguyen = floor(($_SESSION['maDe']+1) / 25);
			$phanDu = ($_SESSION['maDe']+1) % 25; 
			$soDe = $phanNguyen + $phanDu;
		?>
        <h2>KẾT QUẢ THI LÁI XE A1 ĐỀ <?php echo $soDe; ?></h2>
		<label>Số câu đúng: <span style="color: red;"><?php echo $cauDung ; ?></span></label><br>
		<label>Số câu điểm liệt sai: <span style="color: red;"><?php echo $cauDiemLiet ; ?></span></label><br>
        <?php
			if ($cauDiemLiet > 0) {
				$ketQuaThi = "Trượt";
				echo 'Kết quả: <span style="color: red;">KHÔNG ĐẠT - Sai ' . $cauDiemLiet . ' câu điểm liệt</span>';	
			} 
			else {
				if ($cauDung >= 21) {
					$ketQuaThi = "Đậu";
					echo 'Kết quả: <span style="color: red;"> Đạt</span>';
				}
				else {
					$ketQuaThi = "Trượt";
					echo 'Kết quả: <span style="color: red;">KHÔNG ĐẠT - Sai ' . 25 - $cauDung . ' câu</span>';
				}
			}
			
			/* upload dữ liệu(kết quả thi) của thí sinh sau khi thi xong. */
			//kết nối dữ liệu 
			$connect = mysqli_connect("localhost", "root", "", "thilaixea1") or die("Không kết nối được dl");

			//xay dung truy van dl
			$query = "UPDATE thongtinthisinh SET KetQua='" .($ketQuaThi) . "', SoCTLDung='" . ($cauDung) . "' WHERE username='" . ($_SESSION['tdnThiSinh']) . "'";

			mysqli_query($connect,"SET NAMES 'utf8'");

			// thuc hien cau lenh truy van
			$query_result = mysqli_query($connect, $query);

			// kiểm tra dữ liệu đã được upload chưa
			if (!$query_result) {
				die("Upload không thành công " . mysqli_error($connection));
			}
			else{
				echo "Upload thành công";
			}

			// đóng kết nối db 
			mysqli_close($connect);
        ?>
		<form method="POST" action="GDxemlaiketquathi.php">
			<button class="btnxemlaibaithi"> Xem bài thi </button>
		</form>
    </div>
</body>
</html>

