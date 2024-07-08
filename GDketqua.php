<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dangxuat'])) {
	session_unset(); // Xóa tất cả các biến session
	session_destroy(); // Hủy session
	header("Location: GDtrangchu.php"); // Chuyển hướng người dùng về trang chủ
	exit();
}
// khởi tạo Session chỉ định cho từng dạng bài thi 
if(isset($_POST['ketquathithu'])) {
	$_SESSION['ketquathithu'] = true;
}
if(isset($_POST['ketquathithiet'])) {
	$_SESSION['ketquathithiet'] = true;
}
if(isset($_POST['ketquathicauhoiliet'])) {
	$_SESSION['ketquathicauhoiliet'] = true;
}

// khởi tạo các session lưu kết quả thi
if(!isset($_SESSION['cauDung']) || !isset($_SESSION['cauSai']) || !isset($_SESSION['cauDiemLiet'])){
	$_SESSION['cauDung'] = 0;
	$_SESSION['cauSai'] = 0;
	$_SESSION['cauDiemLiet'] = 0;
} 

// Lấy kết quả thi thử
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ketquathithu'])) {
    $data = explode(',', $_POST['ketquathithu']);
    $_SESSION['cauDung'] = isset($data[0]) ? (int)$data[0] : 0;
    $_SESSION['cauDiemLiet'] = isset($data[1]) ? (int)$data[1] : 0;
}

// lấy kết quả thi thiệt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ketquathithiet'])) {
    $data = explode(',', $_POST['ketquathithiet']);
    $_SESSION['cauDung'] = isset($data[0]) ? (int)$data[0] : 0;
    $_SESSION['cauDiemLiet'] = isset($data[1]) ? (int)$data[1] : 0;	
}

// Lấy kết quả thi bộ câu hỏi liệt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ketquathicauhoiliet'])) {
    $_SESSION['cauDung'] = (int)$_POST['ketquathicauhoiliet'];
}

// NẾU LÀ THI THỬ ----------------------------------------------------------------------------------------------------------
if(isset($_SESSION['ketquathithu']))
{
	
// Kết nối database
$connect = mysqli_connect("localhost", "root", "", "thilaixea1");

// Khởi tạo mảng để lưu trữ dữ liệu
$questions = array();

// Thực hiện truy vấn SQL
$dataQuery = "SELECT CauHoi, CauA, CauB, CauC, CauD, DapAn, HinhAnh, CauHoiLiet FROM cauhoi LIMIT " . ($_SESSION['maDe']) . ", 25";
$resultQuery = mysqli_query($connect, $dataQuery);

// Lặp qua kết quả của truy vấn và đưa dữ liệu vào mảng
while ($row = mysqli_fetch_assoc($resultQuery)) {
    $question = array(
        "question" => $row['CauHoi'],
        "answers" => array(
            "A" => $row['CauA'],
            "B" => $row['CauB'],
            "C" => $row['CauC'],
            "D" => $row['CauD']
        ),
        "correct_answer" => $row['DapAn'],
        "image" => $row['HinhAnh'],
        "cauHoiLiet" => $row['CauHoiLiet']
    );

    // Thêm câu hỏi vào mảng chính
    $questions[] = $question;

    // Cần sử dụng để tính toán kết quả
    $_SESSION['answerCorrects'][] = $row['DapAn'];
    $_SESSION['checkDiemLiet'][] = $row['CauHoiLiet'];
}

// Đóng kết nối
mysqli_close($connect);


// Số lượng mục trên mỗi trang
$itemsPerPage = 1;

// Tổng số mục
$totalItems = 25;

// Tính tổng số trang
$totalPages = ceil($totalItems / $itemsPerPage);

// Lấy trang hiện tại, mặc định là trang 1
$currentpage = isset($_POST['page']) ? $_POST['page'] : 1;

// Đảm bảo trang hiện tại không nhỏ hơn 1 và không lớn hơn tổng số trang
$currentpage = max(1, min($currentpage, $totalPages));

// Tính chỉ số bắt đầu và kết thúc của mục trên trang hiện tại
$startIndex = ($currentpage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);

// 2 button điều hướng câu hỏi
$previousPage = max(1, $currentpage - 1);
$nextPage = min($totalPages, $currentpage + 1);

?>

<!DOCTYPE html>
<html lang="vi">
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem kết quả thi </title>
	<link rel="stylesheet" href="style.css">
	<style>
		.checkedBlue {
            background-color: blue;
        }
        .checkedGreen {
            background-color: green;
        }
        .checkedRed {
            background-color: red;
        }
		
		.ketQuaThi {
			 width: 100%;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			font-size: 20px;
			border: 1px solid;
			border-radius: 10px;
			background-color: #f0f0f0; /* Màu nền */
			padding: 10px; /* Khoảng cách bên trong */
		}
		.ketQuaThi label {
			display: block;
			margin-bottom: 5px;
			font-weight: bold;
		}

		.buttonChuyenTrang {
            display: flex; /*bố trí các nút theo hàng ngang */
            justify-content: space-between;  /* tạo khoảng cách giữa các nút*/
            margin-top: 20px; /** Tạo khoảng cách trên 20 pixel giữa các phần tử(button) buttonChuyenTrang và phần tử phía trên nó. */        
        }
        .buttonChuyenTrang.btn {
            width: 100px;  /*  Đặt chiều rộng cố định cho các nút bên trong phần tử buttonChuyenTrang là 100 pixel */
        }
	</style>
</head>
<body>
    <div>
		<div class="header">
			<a href="GDtrangchu.php">
				<img src="https://th.bing.com/th/id/OIP.mx3kx2bpORHJwWuYx1hXwwHaHa?pid=ImgDet&w=200&h=200&c=7&dpr=1.3" style="width: auto; height: 50px;" alt="Logo">
			</a>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<?php
				if (isset($_SESSION['tdnThiSinh'])) {
					echo $_SESSION['tdnThiSinh'];
				}
				?>
				<button class="btn-exit" type="submit" name="dangxuat">Đăng xuất</button>
			</form>
        </div>
		<!-- tính mã đề -->
		<?php
			$phanNguyen = floor(($_SESSION['maDe'] + 1) / 25);
			$phanDu = ($_SESSION['maDe'] + 1) % 25; 
			$soDe = $phanNguyen + $phanDu;
		?>
		
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<!-- Title -->
			<div class="title">
				<?php
					$phanNguyen = floor(($_SESSION['maDe'] + 1) / 25); // Phần nguyên
					$phanDu = ($_SESSION['maDe'] + 1) % 25; // Phần dư
					$soDe = $phanNguyen + $phanDu;
				?>
				<h1 style="text-align: center; padding: 10px;">XEM LẠI ĐỀ <?php echo $soDe; ?></h1>
			</div>
			<!-- End Title -->

			<div class="left-body">
				<?php 
					for ($i = $startIndex; $i <= $endIndex; $i++) { 
						if (isset($questions[$i])) {
							$question = $questions[$i];
						}
						// Kiểm tra xem có câu trả lời được chọn cho câu hỏi này hay không
						if (isset($_SESSION['selectedAnswer'][$i])) {   
							if ($_SESSION['selectedAnswer'][$i] == $_SESSION['answerCorrects'][$i]) {
								$is_checked_CTLDung = "checkedGreen";
							} else {
								$is_checked_DapAn = "checkedGreen";
								$is_checked_CTLSai = "checkedRed";
							}
						} else { // không chọn câu trả lời
							$is_checked_DapAn = "checkedGreen";
						}
				?>
					<div class="question">
						<?php if (isset($questions[$i])): ?>
							<input type="hidden" name="questionID" value="<?php echo $i; ?>">
							<strong><?php echo "Câu " . ($i + 1) . ": "; echo $question['question']; ?></strong><br>
							<?php if ($question['image'] != 0): ?>
								<img src="<?php echo $question['image']; ?>" width="300" alt="Ảnh minh họa"><br>
							<?php endif; ?>
						<?php else: echo "Câu hỏi này không tồn tại."; ?>
						<?php endif; ?>
					</div>
					
					<div>
						<label>
							<div class="cautraloi <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 1 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrects'][$i] == 1 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 1 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
								<label class="checkbox-inline" for="radioA">
									<input type="radio" id="radioA" name="<?php echo $i; ?>" value="A"> 
									<?php echo $question['answers']['A']; ?>
								</label><br>
							</div>
						</label>
						 
						<label>
							<div class="cautraloi <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 2 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrects'][$i] == 2 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 2 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
								<label class="checkbox-inline" for="radioB">                                       
									<input type="radio" id="radioB" name="<?php echo $i; ?>"  value="B">
									<?php echo $question['answers']['B']; ?>
								</label><br>
							</div>
						</label>
						
						<label>
							<?php if ($question['answers']['C'] != 0): ?>
								<div class="cautraloi <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 3 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrects'][$i] == 3 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 3 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
									<label class="checkbox-inline" for="radioC">
										<input type="radio" id="radioC" name="<?php echo $i; ?>" value="C">
										<?php echo $question['answers']['C']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
						
						<label>
							<?php if ($question['answers']['D'] != 0): ?>
								<div class="cautraloi <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 4 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrects'][$i] == 4 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 4 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
									<label class="checkbox-inline" for="radioD">
										<input type="radio" id="radioD" name="<?php echo $i; ?>" value="D">
										<?php echo $question['answers']['D']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
					</div>
				<?php } ?>
				<!-- button điều hướng câu hỏi -->
				<div class="buttonChuyenTrang">
					<button class="btn" align="left" type="submit" name="page" value="<?php echo $previousPage; ?>" <?php if ($currentpage == 1) echo 'disabled'; ?>>Previous</button>
					<button class="btn" align="right" type="submit" name="page" value="<?php echo $nextPage; ?>" <?php if ($currentpage == $totalPages) echo 'disabled'; ?>>Next</button>
				</div>
			</div>
			
			<div class="right-body">
				<!-- hiển thị thông tin kết quả thi --> 
				<div class="ketQuaThi">
					<label>Số câu đúng: <span style="color: red;"><?php echo $_SESSION['cauDung'] ; ?></span></label><br>
					<label>Số câu điểm liệt sai: <span style="color: red;"><?php echo $_SESSION['cauDiemLiet'] ; ?></span></label><br>
					<?php
						if ($_SESSION['cauDiemLiet']  > 0) {
							$ketQuaThi = "Trượt";
							echo 'Kết quả: <span style="color: red;">KHÔNG ĐẠT - Sai ' . $_SESSION['cauDiemLiet']  . ' câu điểm liệt</span>';	
						} else {
							if ($_SESSION['cauDung']  >= 21) {
								$ketQuaThi = "Đậu";
								echo 'Kết quả: <span style="color: red;"> Đạt</span>';
							} else {
								$ketQuaThi = "Trượt";
								echo 'Kết quả: <span style="color: red;">KHÔNG ĐẠT - Sai ' . (25 - $_SESSION['cauDung'] ) . ' câu</span>';
							}
						}
					?>
				</div>
				<!-- start 25 button -->
				<div class="right-button">
					<div class="panel-body">
						<div align="right" colspan="2">
							<?php
								for ($page = 1; $page <= $totalPages; $page++) {
									if (isset($_SESSION['selectedAnswer'][$page - 1]) && isset($_SESSION['answerCorrects'][$page - 1])) {
										if ($_SESSION['selectedAnswer'][$page - 1] == $_SESSION['answerCorrects'][$page - 1]) {
											$is_checked = "checkedGreen"; // nếu câu trả lời đã được chọn và đúng 
										} else {
											$is_checked = "checkedRed"; // nếu câu trả lời đã được chọn nhưng sai   
										}
									} else {
										$is_checked = "checkedBlue"; // nếu chưa có câu trả lời 
									}

									echo "<button class='btn btn-cauhoi $is_checked' type='submit' name='page' value='$page'>$page</button>";
									// sau 5 nút thì xuống dòng
									if ($page % 5 == 0) {
										echo "<br>";
									}
								}
							?>
						</div>
					</div>
				</div>
				<!-- end 25 button -->
			</div>
		</form>
    </div>
</body>
</html>

<?php
}
// NẾU LÀ THI THIỆT ----------------------------------------------------------------------------------------------------------
else if(isset($_SESSION['ketquathithiet']))
{

// Lưu trữ trang hiện tại vào session
if (isset($_POST['page'])) {
	$_SESSION['currentpage'] = $_POST['page'];
} else {
	// Nếu không có trang hiện tại trong session, mặc định là trang đầu tiên
	if (!isset($_SESSION['currentpage'])) {
		$_SESSION['currentpage'] = 1;
	}
}

// Số lượng câu hỏi trên mỗi trang
$itemsPerPage = 1;

// Tổng số câu hỏi
$totalItems = 25;

// Tính tổng số trang
$totalPages = ceil($totalItems / $itemsPerPage);

// Lấy trang hiện tại, mặc định là trang 1
$currentpage = $_SESSION['currentpage'];

// Đảm bảo trang hiện tại không nhỏ hơn 1 và không lớn hơn tổng số trang
$currentpage = max(1, min($currentpage, $totalPages));

// xác định số lượng câu hỏi cần hiển thị trên trang hiện tại 
$startIndex = ($currentpage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);

// 2 button điều hướng câu hỏi
$previousPage = max(1, $currentpage - 1);
$nextPage = min($totalPages, $currentpage + 1);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thi lái xe A1</title>
    <link rel="stylesheet" href="style.css">
	<style>	
		.checkedBlue {
            background-color: blue;
        }
        .checkedGreen {
            background-color: green;
        }
        .checkedRed {
            background-color: red;
        }
		
		.ketQuaThi {
			 width: 100%;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			font-size: 20px;
			border: 1px solid;
			border-radius: 10px;
			background-color: #f0f0f0; /* Màu nền */
			padding: 10px; /* Khoảng cách bên trong */
		}
		.ketQuaThi label {
			display: block;
			margin-bottom: 5px;
			font-weight: bold;
		}

		.buttonChuyenTrang {
            display: flex; /*bố trí các nút theo hàng ngang */
            justify-content: space-between;  /* tạo khoảng cách giữa các nút*/
            margin-top: 20px; /** Tạo khoảng cách trên 20 pixel giữa các phần tử(button) buttonChuyenTrang và phần tử phía trên nó. */        
        }
        .buttonChuyenTrang.btn {
            width: 100px;  /*  Đặt chiều rộng cố định cho các nút bên trong phần tử buttonChuyenTrang là 100 pixel */
        }
	</style>
</head>	
<body>
	 <!--Begin container-->
	<div>
        <!--Begin head-->
        <div class="header">
			<a href="GDtrangchu.php">
				<img src="https://th.bing.com/th/id/OIP.mx3kx2bpORHJwWuYx1hXwwHaHa?pid=ImgDet&w=200&h=200&c=7&dpr=1.3" style="width: auto; height: 50px;" alt="Logo">
			</a>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<?php echo $_SESSION['tdnThiSinh']; ?>
				<button class="btn-exit" type="submit" name="dangxuat" >Đăng xuất</button>
			</form>
        </div>
        <!--End head-->
		
		<!--Begin form-->
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="radioForm">
			<!-- Title -->
			<div class="title">
				<h1 style="text-align: center; padding: 10px;">THI LÁI XE A1 </h1>
			</div>
			<!-- End Title -->
			
			<!--Begin left-->
			<div class="left-body">
				<?php 
					for ($i = $startIndex; $i <= $endIndex; $i++) { 
						if (isset($_SESSION['randomQuestion'][$i])) {
							$question = $_SESSION['randomQuestion'][$i];
						}
						// Kiểm tra xem có câu trả lời được chọn cho câu hỏi này hay không
						if (isset($_SESSION['selectedAnswer'][$i])) {   
							if ($_SESSION['selectedAnswer'][$i] == $_SESSION['answerCorrects'][$i]) {
								$is_checked_CTLDung = "checkedGreen";
							} else {
								$is_checked_DapAn = "checkedGreen";
								$is_checked_CTLSai = "checkedRed";
							}
						} else { // không chọn câu trả lời
							$is_checked_DapAn = "checkedGreen";
						}
				?>
					<div class="question">
						<?php if (isset($_SESSION['randomQuestion'][$i])): ?>
							<input type="hidden" name="questionID" value="<?php echo $i; ?>">
							<strong><?php echo "Câu " . ($i + 1) . ": "; echo $question['question']; ?></strong><br>
							<?php if ($question['image'] != 0): ?>
								<img src="<?php echo $question['image']; ?>" width="300" alt="Ảnh minh họa"><br>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					
					<div>
						<label>
							<div class="cautraloi <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 1 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrects'][$i] == 1 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 1 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
								<label class="checkbox-inline" for="radioA">
									<input type="radio" id="radioA" name="<?php echo $i; ?>" value="A"> 
									<?php echo $question['answers']['A']; ?>
								</label><br>
							</div>
						</label>
						 
						<label>
							<div class="cautraloi <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 2 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrects'][$i] == 2 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 2 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
								<label class="checkbox-inline" for="radioB">                                       
									<input type="radio" id="radioB" name="<?php echo $i; ?>"  value="B">
									<?php echo $question['answers']['B']; ?>
								</label><br>
							</div>
						</label>
						
						<label>
							<?php if ($question['answers']['C'] != 0): ?>
								<div class="cautraloi <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 3 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrects'][$i] == 3 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 3 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
									<label class="checkbox-inline" for="radioC">
										<input type="radio" id="radioC" name="<?php echo $i; ?>" value="C">
										<?php echo $question['answers']['C']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
						
						<label>
							<?php if ($question['answers']['D'] != 0): ?>
								<div class="cautraloi <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 4 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrects'][$i] == 4 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 4 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
									<label class="checkbox-inline" for="radioD">
										<input type="radio" id="radioD" name="<?php echo $i; ?>" value="D">
										<?php echo $question['answers']['D']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
					</div>
				<?php } ?>
				<!-- button điều hướng câu hỏi -->
				<div class="buttonChuyenTrang">
					<button class="btn" align="left" type="submit" name="page" value="<?php echo $previousPage; ?>" <?php if ($currentpage == 1) echo 'disabled'; ?>>Previous</button>
					<button class="btn" align="right" type="submit" name="page" value="<?php echo $nextPage; ?>" <?php if ($currentpage == $totalPages) echo 'disabled'; ?>>Next</button>
				</div>
			</div>
			
			<!--Begin Right-->
			<div class="right-body">
                <!-- hiển thị thông tin kết quả thi --> 
				<div class="ketQuaThi">
					<label>Số câu đúng: <span style="color: red;"><?php echo $_SESSION['cauDung'] ; ?></span></label><br>
					<label>Số câu điểm liệt sai: <span style="color: red;"><?php echo $_SESSION['cauDiemLiet'] ; ?></span></label><br>
					<?php
						if ($_SESSION['cauDiemLiet']  > 0) {
							$ketQuaThi = "Trượt";
							echo 'Kết quả: <span style="color: red;">KHÔNG ĐẠT - Sai ' . $_SESSION['cauDiemLiet']  . ' câu điểm liệt</span>';	
						} else {
							if ($_SESSION['cauDung']  >= 21) {
								$ketQuaThi = "Đậu";
								echo 'Kết quả: <span style="color: red;"> Đạt</span>';
							} else {
								$ketQuaThi = "Trượt";
								echo 'Kết quả: <span style="color: red;">KHÔNG ĐẠT - Sai ' . (25 - $_SESSION['cauDung'] ) . ' câu</span>';
							}
						}
						
						/* Upload dữ liệu (kết quả thi) của thí sinh sau khi thi xong. */
						// Kết nối dữ liệu
						$connect = mysqli_connect("localhost", "root", "", "thilaixea1");

						if ($connect) {
							// Xây dựng truy vấn dữ liệu
							$query = "UPDATE thongtinthisinh SET KetQua='$ketQuaThi', SoCTLDung='" . $_SESSION['cauDung'] . "' WHERE username='" . $_SESSION['tdnThiSinh'] . "'";
						
							// Thiết lập bộ mã ký tự của kết nối MySQL thành UTF-8
							mysqli_query($connect, "SET NAMES 'utf8'");

							// Thực hiện câu lệnh truy vấn
							$query_result = mysqli_query($connect, $query);

							// Kiểm tra dữ liệu đã được upload chưa
							if (!$query_result) {
								die("Upload không thành công: " . mysqli_error($connect));
							} else {
								echo "Upload thành công";
							}

							// Đóng kết nối db
							mysqli_close($connect);
						} else {
							die("Không kết nối được CSDL: " . mysqli_connect_error());
						}
					?>
				</div>
				<!-- start 25 button -->
				<div class="right-button">
					<div class="panel-body">
						<div align="right" colspan="2">
							<?php
								for ($page = 1; $page <= $totalPages; $page++) {
									if (isset($_SESSION['selectedAnswer'][$page - 1]) && isset($_SESSION['answerCorrects'][$page - 1])) {
										if ($_SESSION['selectedAnswer'][$page - 1] == $_SESSION['answerCorrects'][$page - 1]) {
											$is_checked = "checkedGreen"; // nếu câu trả lời đã được chọn và đúng 
										} else {
											$is_checked = "checkedRed"; // nếu câu trả lời đã được chọn nhưng sai   
										}
									} else {
										$is_checked = "checkedBlue"; // nếu chưa có câu trả lời 
									}

									echo "<button class='btn btn-cauhoi $is_checked' type='submit' name='page' value='$page'>$page</button>";
									// sau 5 nút thì xuống dòng
									if ($page % 5 == 0) {
										echo "<br>";
									}
								}
							?>
						</div>
					</div>
				</div>
				<!-- end 25 button -->
			</div>
			<!-- End right -->
		</form>
		<!-- End form -->
	</div>
	<!-- End container -->		
	</body>
</html>
<?php
	// Kết nối database
	$connect = mysqli_connect("localhost", "root", "", "thilaixea1");

	if (!$connect) {
		die("Connection failed: " . mysqli_connect_error());
	}

	// Duyệt vòng lặp để chèn dữ liệu

	for ($i = 0; $i < 25; $i++) {
		if (isset($_SESSION['randomQuestion'][$i])) {
			$question = $_SESSION['randomQuestion'][$i];

			$username = $_SESSION['tdnThiSinh'];
			$CauHoi = $question['question'];
			$HinhAnh = $question['image'];
			$CauA = $question['answers']['A'];
			$CauB = $question['answers']['B'];
			$CauC = $question['answers']['C'];
			$CauD = $question['answers']['D'];
			$DapAn = $question['correct_answer'];
			if(isset($_SESSION['selectedAnswer'][$i])){
				$CauTraLoiDaChon = $_SESSION['selectedAnswer'][$i];
			}
			else{
				$CauTraLoiDaChon = "";
			}
			

			/*
			$CTLDung = 0; // Giả sử số câu trả lời đúng ban đầu là 0
			$CauHoiLietTLSai = 0; // Giả sử câu hỏi không phải là câu hỏi liệt TLSai
			$KetQua = ""; // Giả sử chưa có kết quả
			*/

			// thực hiện truy vấn SQL chèn dữ liệu
			$insertQuery = "INSERT INTO luubaithi (username, CauHoi, HinhAnh, CauA, CauB, CauC, CauD, DapAn, CauTraLoi) 
							VALUES ('$username', '$CauHoi', '$HinhAnh', '$CauA', '$CauB', '$CauC', '$CauD', '$DapAn', '$CauTraLoiDaChon')";
			$connect->query($insertQuery) ;
		}
	}

	// Đóng  kết nối
	mysqli_close($connect);
?>

<?php
}
// NẾU LÀ ÔN TẬP CÂU HỎI LIỆT ------------------------------------------------------------------------------------------------------------
else{
// ket noi database
$connect = mysqli_connect("localhost", "root", "", "thilaixea1");

// Khởi tạo mảng để lưu trữ dữ liệu câu hỏi
$questions = array();

// Thực hiện truy vấn SQL
$dataQuery = "SELECT CauHoi, CauA, CauB, CauC, CauD, DapAn, HinhAnh FROM cauhoiliet ";

// Câu truy vấn SQL để đếm số câu hỏi
$resultQuery = mysqli_query($connect, $dataQuery);

// lặp qua từng dòng kết quả của truy vấn và đưa dữ liệu vào mảng
while ($row = mysqli_fetch_assoc($resultQuery)) {
    $question = array(
        "question" => $row['CauHoi'],
        "answers" => array(
            "A" => $row['CauA'],
            "B" => $row['CauB'],
            "C" => $row['CauC'],
            "D" => $row['CauD']
        ),
        "correct_answer" => $row['DapAn'],
        "image" => $row['HinhAnh'],
    );

    // Thêm  dư liệu của 25 câu hỏi vào mảng chính      
    $questions[] = $question;
}

// đếm tổng số câu hỏi liệt
$totalQuestions = count($questions);

// dong ket noi
mysqli_close($connect);


// Lưu trữ trang hiện tại vào session
if (isset($_POST['page'])) {
	$_SESSION['currentpage'] = $_POST['page'];
} else {
	// Nếu không có trang hiện tại trong session, mặc định là trang đầu tiên
	if (!isset($_SESSION['currentpage'])) {
		$_SESSION['currentpage'] = 1;
	}
}

// Số lượng câu hỏi trên mỗi trang
$itemsPerPage = 1;

// Tổng số câu hỏi
$totalItems = $totalQuestions;

// Tính tổng số trang
$totalPages = ceil($totalItems / $itemsPerPage);

// Lấy trang hiện tại, mặc định là trang 1
$currentpage = $_SESSION['currentpage'];

// Đảm bảo trang hiện tại không nhỏ hơn 1 và không lớn hơn tổng số trang
$currentpage = max(1, min($currentpage, $totalPages));

// xác định số lượng câu hỏi cần hiển thị trên trang hiện tại 
$startIndex = ($currentpage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);

// 2 button điều hướng câu hỏi
$previousPage = max(1, $currentpage - 1);
$nextPage = min($totalPages, $currentpage + 1);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xem bài thi</title>
    <link rel="stylesheet" href="style.css">
	<style>
		.checkedBlue {
            background-color: blue;
        }
        .checkedGreen {
            background-color: green;
        }
        .checkedRed {
            background-color: red;
        }
		
		.ketQuaThi {
			 width: 100%;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			font-size: 20px;
			border: 1px solid;
			border-radius: 10px;
			background-color: #f0f0f0; /* Màu nền */
			padding: 10px; /* Khoảng cách bên trong */
		}
		.ketQuaThi label {
			display: block;
			margin-bottom: 5px;
			font-weight: bold;
		}

		.buttonChuyenTrang {
            display: flex; /*bố trí các nút theo hàng ngang */
            justify-content: space-between;  /* tạo khoảng cách giữa các nút*/
            margin-top: 20px; /** Tạo khoảng cách trên 20 pixel giữa các phần tử(button) buttonChuyenTrang và phần tử phía trên nó. */        
        }
        .buttonChuyenTrang.btn {
            width: 100px;  /*  Đặt chiều rộng cố định cho các nút bên trong phần tử buttonChuyenTrang là 100 pixel */
        }
	</style>
</head>	
<body>
	 <!--Begin container-->
	<div>
        <!--Begin head-->
        <div class="header">
			<a href="GDtrangchu.php">
				<img src="https://th.bing.com/th/id/OIP.mx3kx2bpORHJwWuYx1hXwwHaHa?pid=ImgDet&w=200&h=200&c=7&dpr=1.3" style="width: auto; height: 50px;" alt="Logo">
			</a>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
				<?php echo $_SESSION['tdnThiSinh']; ?>
				<button class="btn-exit" type="submit" name="dangxuat" >Đăng xuất</button>
			</form>
        </div>
        <!--End head-->
		
        <div class="title">
            <h1 style="text-align: center; padding: 10px;">XEM LẠI CÂU HỎI LIỆT </h1>
        </div>
		
		<!--Begin form-->
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<?php 
				for ($i = $startIndex; $i <= $endIndex; $i++){ 
					if(isset($questions[$i])){ // nếu tồn tại câu hỏi này
						$question = $questions[$i]; 
					}
					// Kiểm tra xem có câu trả lời được chọn cho câu hỏi này hay không
					if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i])){   
						if($_SESSION['selectedAnswerCauHoiLiet'][$i] == $_SESSION['answerCorrectsDapAnLiet'][$i]){
							$is_checked_CTLDung = "checkedGreen";
						}else{
							$is_checked_DapAn = "checkedGreen";
							$is_checked_CTLSai = "checkedRed";
						}
					}else{ // không chọn câu trả lời
						$is_checked_DapAn = "checkedGreen";
					}
			?>
				<!--Begin left-->
				<div class="left-body">
				<?php if(isset($questions[$i])): ?>
					<div class="left-question">
						<input type="hidden" name="questionID" value="<?php echo $i; ?>">
						<strong><?php echo "Câu " . $i+1 . ": "; echo $question['question']; ?></strong><br>
						<?php if ($question['image'] != 0): ?>
							<img src="<?php echo $question['image']; ?>" width="300" alt="Ảnh minh họa"><br>
						<?php endif; ?>
					</div>
					
					<div class="left-button">
						<label>
							<div class="cautraloi <?php if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i]) && $_SESSION['selectedAnswerCauHoiLiet'][$i] == 1 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrectsDapAnLiet'][$i] == 1 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i]) && $_SESSION['selectedAnswerCauHoiLiet'][$i] == 1 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
								<label class="checkbox-inline" for="radioA">
									<input type="radio" id="radioA" name="<?php echo $i; ?>" value="A"> 
									<?php echo $question['answers']['A']; ?>
								</label><br>
							</div>
						</label>
						 
						<label>
							<div class="cautraloi <?php if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i]) && $_SESSION['selectedAnswerCauHoiLiet'][$i] == 2 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrectsDapAnLiet'][$i] == 2 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i]) && $_SESSION['selectedAnswerCauHoiLiet'][$i] == 2 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
								<label class="checkbox-inline" for="radioB">                                       
									<input type="radio" id="radioB" name="<?php echo $i; ?>"  value="B">
									<?php echo $question['answers']['B']; ?>
								</label><br>
							</div>
						</label>
						
						<label>
							<?php if ($question['answers']['C'] != 0): ?>
								<div class="cautraloi <?php if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i]) && $_SESSION['selectedAnswerCauHoiLiet'][$i] == 3 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrectsDapAnLiet'][$i] == 3 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i]) && $_SESSION['selectedAnswerCauHoiLiet'][$i] == 3 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
									<label class="checkbox-inline" for="radioC">
										<input type="radio" id="radioC" name="<?php echo $i; ?>" value="C">
										<?php echo $question['answers']['C']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
						
						<label>
							<?php if ($question['answers']['D'] != 0): ?>
								<div class="cautraloi <?php if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i]) && $_SESSION['selectedAnswerCauHoiLiet'][$i] == 4 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($_SESSION['answerCorrectsDapAnLiet'][$i] == 4 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($_SESSION['selectedAnswerCauHoiLiet'][$i]) && $_SESSION['selectedAnswerCauHoiLiet'][$i] == 4 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
									<label class="checkbox-inline" for="radioD">
										<input type="radio" id="radioD" name="<?php echo $i; ?>" value="D">
										<?php echo $question['answers']['D']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
					</div>
				<?php else: echo "Câu hỏi này không tồn tại."; ?>
				<?php endif; ?>
				
					<!-- button điều hướng câu hỏi -->
					<div class="buttonChuyenTrang">
						<button class="btn" align="left" type="submit" name="page" value="<?php echo $previousPage; ?>" <?php if ($currentpage == 1) echo 'disabled'; ?>>Previous</button>
						<button class="btn" align="right" type="submit" name="page" value="<?php echo $nextPage; ?>" <?php if ($currentpage == $totalPages) echo 'disabled'; ?>>Next</button>
					</div>
				</div>
				<!-- End left --> 
			<?php } ?>
			<!-- End for --> 
			
			<!--Begin Right-->
			<div class="right-body">
				<!-- hiển thị thông tin kết quả thi --> 
				<div class="ketQuaThi">
					<label>Số câu đúng: <span style="color: red;"><?php echo $_SESSION['cauDung'] ; ?></span></label><br>
					<label>Số câu sai: <span style="color: red;"><?php echo $totalQuestions - $_SESSION['cauDung'] ; ?></span></label><br>
				</div>
				<!-- 25 button -->
				<div class="right-button">
					<div class="panel-body">
						<div align="right" colspan="2">
							<?php
							for ($page = 1; $page <= $totalPages; $page++) {
								if(isset($_SESSION['selectedAnswerCauHoiLiet'][$page-1]) && isset($_SESSION['answerCorrectsDapAnLiet'][$page-1])) {
									if ($_SESSION['selectedAnswerCauHoiLiet'][$page-1] == $_SESSION['answerCorrectsDapAnLiet'][$page-1]) {
										$is_checked = "checkedGreen"; // nếu câu trả lời đã được chọn và đúng 
									} else {
										$is_checked = "checkedRed"; // nếu câu trả lời đã được chọn nhưng sai   
									}
								} else {
									$is_checked = "checkedBlue"; // nếu chưa có câu trả lời 
								}

								echo "<button class='btn btn-cauhoi clickcauhoi btn-1 $is_checked' type='submit' name='page' value='$page'>$page</button>";
								// sau 5 nút thì xuống dòng
								if($page % 5 == 0){
									echo "<br>";
								}
							}				
							?>
						</div>
					</div>
				</div>
			</div>
			<!-- End right -->
		</form>
		<!-- End form -->
	</div>
	<!-- End container -->		
	</body>
</html>
<?php
}
?>