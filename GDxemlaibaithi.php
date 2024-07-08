<?php
session_start();

// nếu thí sinh đăng xuất 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dangxuat'])) {
	session_unset(); // Xóa tất cả các biến session
	session_destroy(); // Hủy session
	header("Location: GDtrangchu.php"); // Chuyển hướng người dùng về trang chủ
	exit();
}

// ket noi database
$connect = mysqli_connect("localhost", "root", "", "thilaixea1");

// Khởi tạo mảng để lưu trữ dữ liệu
$questions = array();

// Thực hiện truy vấn SQL
$dataQuery = "SELECT * FROM luubaithi where username=".($_SESSION['tdnThiSinh'])." ";
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
        "CTL" => $row['CauTraLoi'] // câu trả lời thí sinh đã chọn
		
    );

    // Thêm câu hỏi vào mảng chính
    $questions[] = $question;
}

// dong ket noi
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

// lấy thông tin kết quả thi của thí sinh này



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
            <h1 style="text-align: center; padding: 10px;">XEM LẠI BÀI THI </h1>
        </div>
		
		<!--Begin form-->
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
			<?php 
				for ($i = $startIndex; $i <= $endIndex; $i++){ 
					if(isset($questions[$i])){ // nếu tồn tại câu hỏi này
						$question = $questions[$i]; 
					}
					// Kiểm tra xem có câu trả lời được chọn cho câu hỏi này hay không
					if(isset($question['CTL'])){   
						if($question['CTL'] == $question['correct_answer']){
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
							<div class="cautraloi <?php if(isset($question['CTL']) && $question['CTL'] == 1 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($question['correct_answer'] == 1 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($question['CTL']) && $question['CTL'] == 1 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
								<label class="checkbox-inline" for="radioA">
									<input type="radio" id="radioA" name="<?php echo $i; ?>" value="A"> 
									<?php echo $question['answers']['A']; ?>
								</label><br>
							</div>
						</label> 
						 
						<label>
							<div class="cautraloi <?php if(isset($question['CTL']) && $question['CTL'] == 2 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($question['correct_answer'] == 2 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($question['CTL']) && $question['CTL'] == 2 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
								<label class="checkbox-inline" for="radioB">                                       
									<input type="radio" id="radioB" name="<?php echo $i; ?>"  value="B">
									<?php echo $question['answers']['B']; ?>
								</label><br>
							</div>
						</label>
						
						<label>
							<?php if ($question['answers']['C'] != 0): ?>
								<div class="cautraloi <?php if(isset($question['CTL']) && $question['CTL'] == 3 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($question['correct_answer'] == 3 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($question['CTL']) && $question['CTL'] == 3 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
									<label class="checkbox-inline" for="radioC">
										<input type="radio" id="radioC" name="<?php echo $i; ?>" value="C">
										<?php echo $question['answers']['C']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
						
						<label>
							<?php if ($question['answers']['D'] != 0): ?>
								<div class="cautraloi <?php if(isset($question['CTL']) && $question['CTL'] == 4 && isset($is_checked_CTLDung)) echo $is_checked_CTLDung; ?> <?php if($question['correct_answer'] == 4 && isset($is_checked_DapAn)) echo $is_checked_DapAn; ?> <?php if(isset($question['CTL']) && $question['CTL'] == 4 && isset($is_checked_CTLSai)) echo $is_checked_CTLSai; ?>">
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
				<div class="right-button">
					<div class="panel-body">
						<div align="right" colspan="2">
							<?php
							for ($page = 1; $page <= $totalPages; $page++) {
								if(isset($questions[$i])){ // nếu tồn tại câu hỏi này
									$question = $questions[$page - 1]; 
								}
								if(isset($question['CTL']) && isset($question['correct_answer'])) {
									if ($question['CTL'] == $question['correct_answer']) {
										$is_checked = "checkedGreen"; // nếu câu trả lời đã được chọn và đúng 
									} else {
										$is_checked = "checkedRed"; // nếu câu trả lời đã được chọn nhưng sai   
									}
									
									if($question['CTL'] == ""){
										$is_checked = "checkedBlue"; // nếu chưa chọn câu trả lời 
									}
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