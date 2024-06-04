<?php
session_start();

// nếu người dùng đăng xuất 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dangxuat'])) {
	session_unset(); // Xóa tất cả các biến session
	session_destroy(); // Hủy session
	header("Location: GDtrangchu.php"); // chuyển hướng người dùng về trang chủ
	exit(); // ngừng thực thi mã sau khi chuyển hướng
}

// ket noi database
$connect = mysqli_connect("localhost", "root", "", "thilaixea1");

// Khởi tạo mảng để lưu trữ dữ liệu câu hỏi
$questions = array();

// Thực hiện truy vấn SQL
$dataQuery = "SELECT CauHoi, CauA, CauB, CauC, CauD, DapAn, HinhAnh, CauHoiLiet FROM cauhoi LIMIT " . ($_SESSION['maDe']) . ", 25";
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
        "cauHoiLiet" => $row['CauHoiLiet']
    );

    // Thêm  dư liệu câu hỏi vào mảng      
    $questions[] = $question;
	
	// cần sử dụng để tính toán kết quả 
	$_SESSION['answerCorrects'][] = $row['DapAn'];
    $_SESSION['checkDiemLiet'][] = $row['CauHoiLiet'];
}

// dong ket noi
mysqli_close($connect);

// Xử lý dữ liệu form gửi về
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['questionID'])) { // kiểm tra câu hỏi tồn tại hay không
	$questionID = $_POST['questionID'];
	if(isset($_POST[$questionID])){ // kiểm tra và lưu câu trả lời đã chọn vào session
		$chonDapAn = $_POST[$questionID];
		switch($chonDapAn) {
			case "A":
				$_SESSION['selectedAnswer'][$questionID] = 1;
				break;
			case "B":
				$_SESSION['selectedAnswer'][$questionID] = 2;
				break;
			case "C":
				$_SESSION['selectedAnswer'][$questionID] = 3;
				break;
			case "D":
				$_SESSION['selectedAnswer'][$questionID] = 4;
				break;
			default:
				// trường hợp ko chọn câu trả lời nào
				$_SESSION['selectedAnswer'][$questionID] = 0;
				break;
		}
	}
}

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

// Tính chỉ số bắt đầu và kết thúc của mục trên trang hiện tại
$startIndex = ($currentpage - 1) * $itemsPerPage;
$endIndex = min($startIndex + $itemsPerPage - 1, $totalItems - 1);

// 2 button điều hướng câu hỏi
$previousPage = max(1, $currentpage - 1);
$nextPage = min($totalPages, $currentpage + 1);

// cài đặt và quản lý thời gian thi (19 phút)
if (!isset($_SESSION['exam_deadline'])) {
    $current_time = time();
    $exam_duration = 19 * 60;
    $deadline = $current_time + $exam_duration;
    $_SESSION['exam_deadline'] = $deadline;
} else {
    $deadline = $_SESSION['exam_deadline'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thi lái xe A1</title>
    <link rel="stylesheet" href="style.css">
	<style>	
		.checked { /* chuyển màu câu hỏi đã tick chọn radio */
			background-color: green;
			color: white;
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
	
	<script>
		// gửi dữ liệu từ biểu mẫu ngay lập tức khi nhấn chọn radio(đáp án)
        function submitForm(page) {
            document.getElementById('radioForm').submit();
        }
	</script>
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
			<?php // tính mã đề
				$phanNguyen = floor(($_SESSION['maDe']+1) / 25);
				$phanDu = ($_SESSION['maDe']+1) % 25; 
				$soDe = $phanNguyen + $phanDu;
			?>
            <h1 style="text-align: center; padding: 10px;">THI THỬ BẰNG LÁI XE ĐỀ <?php echo $soDe; ?> </h1>
        </div>
		
		<!--Begin form-->
		<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" id="radioForm">
			<input type="hidden" name="currentpage" id="currentpage" value="<?php echo $currentpage; ?>">
			<?php for ($i = $startIndex; $i <= $endIndex; $i++){
					if(isset($questions[$i])){ // nếu tồn tại câu hỏi này
						$question = $questions[$i]; 
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
							<div class="cautraloi">
								<label class="checkbox-inline" for="radioA">
									<input type="radio" type="submit" id="radioA" name="<?php echo $i; ?>" value="A" onclick="submitForm(<?php echo $currentpage; ?>)" <?php  if (isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 1) echo "checked='checked'";?>> 
									<?php echo $question['answers']['A']; ?>
								</label><br>
							</div>
						</label>
						
						<label>
							<div class="cautraloi">
								<label class="checkbox-inline" for="radioB">                                       
									<input type="radio" type="submit" id="radioB" name="<?php echo $i; ?>"  value="B" onclick="submitForm(<?php echo $currentpage; ?>)" <?php if (isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 2) echo "checked='checked'";?>>
									<?php echo $question['answers']['B']; ?>
								</label><br>
							</div>
						</label>
						
						<label>
							<?php if ($question['answers']['C'] != 0): ?>
								<div class="cautraloi">
									<label class="checkbox-inline" for="radioC">
										<input type="radio" type="submit" id="radioC" name="<?php echo $i; ?>" value="C" onclick="submitForm(<?php echo $currentpage; ?>)" <?php if (isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 3) echo "checked='checked'";?>>
										<?php echo $question['answers']['C']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
						
						<label>
							<?php if ($question['answers']['D'] != 0): ?>
								<div class="cautraloi">
									<label class="checkbox-inline"for="radioD">
										<input type="radio" type="submit" id="radioD" name="<?php echo $i; ?>" value="D" onclick="submitForm(<?php echo $currentpage; ?>)" <?php if (isset($_SESSION['selectedAnswer'][$i]) && $_SESSION['selectedAnswer'][$i] == 4) echo "checked='checked'";?>>
										<?php echo $question['answers']['D']; ?>
									</label><br>
								</div>
							<?php endif; ?>
						</label>
						<!-- button điều hướng câu hỏi -->
						<div class="buttonChuyenTrang">
							<button class="btn" align="left" type="submit" name="page" value="<?php echo $previousPage; ?>" <?php if ($currentpage == 1) echo 'disabled'; ?>>Previous</button>
							<button class="btn" align="right" type="submit" name="page" value="<?php echo $nextPage; ?>" <?php if ($currentpage == $totalPages) echo 'disabled'; ?>>Next</button>
						</div>
					</div>
				<?php else: echo "Câu hỏi này không tồn tại."; ?>
				<?php endif; ?>
				</div>
				<!-- End left --> 
			<?php } ?>
			<!-- End for --> 
			
			<!--Begin Right-->
			<div class="right-body">
				<input type="hidden" id="deadline" name="deadline" value="<?php echo $deadline; ?>">
                <div class="right-time" id="countdown">
                    <script>
                        function time_remaining(endtime) {
                            var total = Date.parse(endtime) - Date.parse(new Date());
                            var seconds = Math.floor((total / 1000) % 60);
                            var minutes = Math.floor((total / 1000 / 60) % 60);
                            return {
                                'total': total,
                                'minutes': minutes,
                                'seconds': seconds
                            };
                        }
                        
                        function run_clock(id, endtime) {
                            var clock = document.getElementById(id);
                            function update_clock() {
                                var t = time_remaining(endtime);
                                clock.innerHTML = 'Thời gian còn lại: ' + t.minutes + ' phút ' + t.seconds + ' giây.';
                                if (t.total <= 0) {
                                    clearInterval(timeinterval);
                                    // Tự động click vào nút "Nộp bài" khi thời gian kết thúc
									document.getElementById("submit_button").click();
                                }
                            }
                            update_clock();
                            var timeinterval = setInterval(update_clock, 1000);
                        }
                        
                        var deadline = new Date(<?php echo $_SESSION['exam_deadline'] * 1000; ?>);
                        run_clock('countdown', deadline);
                    </script>
				</div>
				<div class="right-button">
					<div class="panel-body">
						<div align="right" colspan="2">
							<?php
							for ($page = 1; $page <= $totalPages; $page++) {
								$is_checked = (isset($_SESSION['selectedAnswer'][$page-1])) ? "checked" : ""; // Kiểm tra xem radio được chọn hay không
								echo "<button class='btn btn-cauhoi $is_checked' type='submit' name='page' id='btncauhoi' value='$page'>$page</button>";
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
		
		<!-- click để xem kết quả thi   -->
		<div>
			<form action="GDketqua.php" method="POST">
				<?php 
					$ketQuaThi = sumQuestionCorrect();
				?>
				<button type="submit" class="btn-submit" id="submit_button" name="ketqua" value="<?php echo $ketQuaThi[0] . ',' . $ketQuaThi[1]; ?>">Nộp bài</button>
			</form>
		</div>
	</div>
	<!-- End container -->		
	</body>
</html>

<?php
// hàm tính kết quả sau khi thi
function sumQuestionCorrect(){
    $countCorrect = 0; // đếm số câu hỏi đúng
    $countIncorrectDiemLiet = 0; //đếm số câu hỏi liệt trả lời sai.

	// duyệt 25 câu hỏi
    for($i = 0; $i <= 24; $i++){
		// kiểm tra nếu là câu hỏi liệt
        if($_SESSION['checkDiemLiet'][$i] == 01){
			// nếu tồn tại session vị trí $i
			if(isset($_SESSION['selectedAnswer'][$i]) && isset($_SESSION['answerCorrects'][$i])){
				// nếu câu trả lời của câu hỏi liệt khác đáp án thì tăng số câu hỏi liệt sai lên 1 đơn vị    
				if($_SESSION['selectedAnswer'][$i] != $_SESSION['answerCorrects'][$i]){
					$countIncorrectDiemLiet++;
				}
				// ngược lại tăng số câu trả lời đúng
				else{
					$countCorrect++;
				}
			}
			else{
				$countIncorrectDiemLiet++;
			}
        }
		// không là câu hỏi liệt 
        else {
			// nếu câu trả lời trùng với đáp án thì tăng số câu trả lời đúng
            if(isset($_SESSION['selectedAnswer'][$i]) && isset($_SESSION['answerCorrects'][$i]) && $_SESSION['selectedAnswer'][$i] == $_SESSION['answerCorrects'][$i]){
                $countCorrect++;
            }
        }		
    }
	
	// trả về dưới dạng mảng tổng số câu trả lời đúng và tổng số câu hỏi liệt sai
    return array($countCorrect, $countIncorrectDiemLiet);
}
?>



