<?php
session_start();
if(isset($_SESSION['tdnThiSinh'])){
	
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn đề </title>
    <link rel="stylesheet" href="style.css">
	<style> 
		/* button chọn đề */
		.btnChonDe {
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
    <div class="header">
		<a href="GDtrangchu.php">
			<img src="https://th.bing.com/th/id/OIP.mx3kx2bpORHJwWuYx1hXwwHaHa?pid=ImgDet&w=200&h=200&c=7&dpr=1.3" style="width: auto; height: 50px;" alt="Logo">
		</a>
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<?php  echo $_SESSION['tdnThiSinh']; ?>
			<button class="btn-exit" type="submit" name="dangxuat" >Đăng xuất</button>
		</form>
	</div>
    <div>
        <h1 style="text-align: center; padding: 10px;">Chọn đề để ôn tập</h1>
    </div>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" style="text-align: center;">
        <button type="submit" name="de" value="de1" class="btnChonDe">Đề 1</button>
        <button type="submit" name="de" value="de2" class="btnChonDe">Đề 2</button>
        <button type="submit" name="de" value="de3" class="btnChonDe">Đề 3</button>
        <button type="submit" name="de" value="de4" class="btnChonDe">Đề 4</button>
        <button type="submit" name="de" value="de5" class="btnChonDe">Đề 5</button>
        <button type="submit" name="de" value="de6" class="btnChonDe">Đề 6</button>
        <button type="submit" name="de" value="de7" class="btnChonDe">Đề 7</button>
        <button type="submit" name="de" value="de8" class="btnChonDe">Đề 8</button>
    </form>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["de"])) {
	$_SESSION['selectedAnswer'] = array(); // Mảng chứa câu trả lời đã chọn
	$_SESSION['answerCorrects'] = array(); // Mảng chứa câu trả lời đúng
	$_SESSION['checkDiemLiet'] = array(); // mảng lưu trữ các câu hỏi dưới hình thức điểm liệt 

	$de = $_POST["de"];
	switch($de){
		case "de1":
			$_SESSION['maDe'] = 0;
			header('location:GDThiThu.php');
			break;
		case "de2":
			$_SESSION['maDe'] = 25;
			header('location:GDThiThu.php');
			break;
		case "de3":
			$_SESSION['maDe'] = 50;
			header('location:GDThiThu.php');
			break;
		case "de4":
			$_SESSION['maDe'] = 75;
			header('location:GDThiThu.php');
			break;	
		case "de5":
			$_SESSION['maDe'] = 100;
			header('location:GDThiThu.php');
			break;
		case "de6":
			$_SESSION['maDe'] = 125;
			header('location:GDThiThu.php');
			break;
		case "de7":
			$_SESSION['maDe'] = 150;
			header('location:GDThiThu.php');
			break;
		case "de8":
			$_SESSION['maDe'] = 175;
			header('location:GDThiThu.php');
			break;
		default:
			$_SESSION['maDe'] = 201;
			break;			
	}
}
?>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dangxuat'])) {
	session_unset(); // Xóa tất cả các biến session
	session_destroy(); // Hủy session
	header("Location: GDtrangchu.php"); // Chuyển hướng người dùng về trang chủ
	exit();
}
} // đóng điều kiện kiểm tra tên đăng nhập 
?>
