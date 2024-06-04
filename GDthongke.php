<?php
session_start();

$tongSoThiSinh = 0;
$soTSDaThi = 0;
$soTSChuaThi = 0;
$soTSDau = 0;
$soTSTruot = 0;

//kết nối dữ liệu 
$connect = mysqli_connect("localhost", "root", "", "thilaixea1") or die("Không kết nối được dl");

//xay dung truy van dl
$query = "select * from thongtinthisinh";

mysqli_query($connect,"SET NAMES 'utf8'");


// Truy vấn để tính tổng số thí sinh đã thi và chưa thi
$sqlTSDaThi = "SELECT COUNT(*) AS total_dathi FROM thongtinthisinh WHERE KetQua IS NOT NULL AND KetQua != '' ";
$sqlTSChuaThi = "SELECT COUNT(*) AS total_chuathi FROM thongtinthisinh WHERE KetQua IS NULL OR KetQua = '' "; 

// thuc hien cau lenh truy van
$query_resultDaThi = mysqli_query($connect, $sqlTSDaThi);
$query_resultChuaThi = mysqli_query($connect, $sqlTSChuaThi);

// lấy dl sau khi truy vấn 
if($query_resultDaThi ){
	$soTSDaThi = mysqli_fetch_array($query_resultDaThi)['total_dathi'];
}
if($query_resultChuaThi){
	$soTSChuaThi = mysqli_fetch_array($query_resultChuaThi)['total_chuathi'];
}

// tổng số thí sinh
$tongSoThiSinh = $soTSDaThi + $soTSChuaThi;

// Truy vấn để tính tổng số thí sinh đã thi đậu và trượt......
$sqlTSThiDau = "SELECT COUNT(*) AS total_thidau FROM thongtinthisinh WHERE KetQua = 'Đậu' ";
$sqlTSThiTruot = "SELECT COUNT(*) AS total_thitruot FROM thongtinthisinh WHERE KetQua = 'Trượt' ";   

// thuc hien cau lenh truy van
$query_resultThiDau = mysqli_query($connect, $sqlTSThiDau);
$query_resultThiTruot = mysqli_query($connect, $sqlTSThiTruot);

// lấy dl sau khi truy vấn 
if($query_resultThiDau){
	$soTSDau = mysqli_fetch_array($query_resultThiDau)['total_thidau'];
}
if($query_resultThiTruot){
	$soTSTruot = mysqli_fetch_array($query_resultThiTruot)['total_thitruot'];
}

// close connect db 
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thống kê thí sinh dự thi</title>
    <link rel="stylesheet" href="style.css">
	<style>
		body {
			font-family: sans-serif;
		}

		.container {
			display: flex;
			flex-wrap: wrap;
			justify-content: space-around;
			margin: 20px;
		}
		
		/* các thẻ hiển thị nội dung sau khi đã thống kê */ 
		.card {
			width: 300px;
			padding: 20px;
			border: 1px solid;
			border-radius: 5px;
			text-align: center;
		}
		
		h1{
			text-align: center;
			color: red;
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
	
    <h1>Thống kê thí sinh dự thi</h1>

    <div class="container">
        <div class="card">
            <h2>Tổng số thí sinh dự thi</h2>
            <p id="total-candidates"><?php echo $tongSoThiSinh; ?></p>
        </div>

        <div class="card">
            <h2>Số thí sinh đã thi</h2>
            <p id="attended-candidates"><?php echo $soTSDaThi; ?></p>
        </div>

        <div class="card">
            <h2>Số thí sinh chưa thi</h2>
            <p id="not-attended-candidates"><?php echo $soTSChuaThi; ?></p>
        </div>
	</div>
	
	<div class="container">
		<div class="card">
            <h2>Tổng số thí sinh đậu</h2>
            <p id="not-attended-candidates"><?php echo $soTSDau; ?></p>
        </div>
		
		<div class="card">
            <h2>Tổng số thí sinh trượt</h2>
            <p id="not-attended-candidates"><?php echo $soTSTruot; ?></p>
        </div>
    </div>

    <script src="script.js"></script>
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