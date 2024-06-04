<?php
// Kết nối đến cơ sở dữ liệu
$connect = mysqli_connect("localhost", "root", "", "thilaixea1");

// Kiểm tra kết nối
if (!$connect) {
    die("Kết nối không thành công: " . mysqli_connect_error());
}

// Khởi tạo biến để lưu thông tin của câu hỏi
$sttCau = $cauHoi = $cauA = $cauB = $cauC = $cauD = $dapAn = $hinhAnh = $cauhoiliet = "";

$isEdit = false;

$title = 'Thêm câu hỏi';

$btnTitle = 'Thêm câu hỏi';

$total = 0;

// Kiểm tra xem đã có tham số sttCau trên URL chưa
if (isset($_GET['sttCau'])) { // nếu là sửa câu hỏi
    // Lấy sttCau từ URL
    $sttCau = $_GET['sttCau'];

    $isEdit = true;

    $title = 'Sửa câu hỏi';

    $btnTitle = 'Cập nhật';

    // Truy vấn SQL để lấy thông tin của câu hỏi dựa trên sttCau
    $query = "SELECT * FROM cauhoi WHERE SttCau = $sttCau";
    $result = mysqli_query($connect, $query);

    // Kiểm tra xem có dữ liệu trả về không
    if (mysqli_num_rows($result) == 1) {
        // Lấy thông tin của câu hỏi từ kết quả truy vấn
        $row = mysqli_fetch_array($result);
        $cauHoi = $row['CauHoi'];
        $cauA = $row['CauA'];
        $cauB = $row['CauB'];
        $cauC = $row['CauC'];
        $cauD = $row['CauD'];
        $dapAn = $row['DapAn'];
        $hinhAnh = $row['HinhAnh'];
        $cauhoiliet = $row['CauHoiLiet'];
    } else {
        echo "Không tìm thấy câu hỏi.";
    }
} else if (isset($_GET['total'])) { // nếu là thêm câu hỏi
    $isEdit = false;
    $total = $_GET['total'];
    $sttCau = $total + 1;
}

// Xử lý khi form được gửi đi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $cauHoi = $_POST['cauHoi'];
    $cauA = $_POST['cauA'];
    $cauB = $_POST['cauB'];
    $cauC = $_POST['cauC'];
    $cauD = $_POST['cauD'];
    $dapAn = $_POST['dapAn'];
    $hinhAnh = $_POST['hinhAnh'];
    $cauhoiliet = $_POST['cauhoiliet'];

    $target_file = '';

    if (!$isEdit) { // nếu là sửa câu hỏi
        // Xử lý upload hình ảnh
        $target_dir = "image/"; // Thư mục lưu trữ hình ảnh
        $target_file = $target_dir . basename($_FILES["hinhAnh"]["name"]); // Đường dẫn tới hình ảnh
        echo  $target_file ;
        move_uploaded_file($_FILES["hinhAnh"]["tmp_name"], $target_file); // Di chuyển hình ảnh vào thư mục lưu trữ
    } else { // nếu là thêm câu hỏi
        // Xử lý tải lên hình ảnh nếu có
        if (isset($_FILES['hinhAnh']['name']) && $_FILES['hinhAnh']['name'] !== '') {
            $target_dir = "image/";
            $target_file = $target_dir . basename($_FILES['hinhAnh']['name']);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Kiểm tra xem file hình ảnh có hợp lệ không
            $check = getimagesize($_FILES['hinhAnh']['tmp_name']);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "File không phải hình ảnh.";
                $uploadOk = 0;
            }

            //kiểm tra kích thước file ảnh
            if ($_FILES['hinhAnh']['size'] > 50000000) {
                echo "Xin lỗi, file của bạn quá lớn.";
                $uploadOk = 0;
            }
            // Kiểm tra định dạng file
            if (
                $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif"
            ) {
                echo "Chỉ các file JPG, JPEG, PNG & GIF được phép.";
                $uploadOk = 0;
            }

            // Nếu không có lỗi, tiến hành tải lên file
            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES['hinhAnh']['tmp_name'], $target_file)) {
                    $hinhAnh = $target_file;
                } else {
                    echo "Xin lỗi, có lỗi xảy ra khi tải lên file của bạn.";
                }
            }
        } else {
            // Nếu không có hình ảnh mới được tải lên, sử dụng giá trị 0 hoặc trống cho trường hình ảnh
            $hinhAnh = $row['HinhAnh'];
        }
    }



    // Thực hiện truy vấn cập nhật
    $query_update = '';
    if ($isEdit == true) { // nếu là sửa câu hỏi
        $query_update = "UPDATE cauhoi  SET CauHoi = '$cauHoi', CauA = '$cauA', CauB = '$cauB', CauC = '$cauC', CauD = '$cauD', DapAn = '$dapAn', HinhAnh = '$hinhAnh', CauHoiLiet = '$cauhoiliet' WHERE SttCau = $sttCau";
    } else { // nếu là thêm câu hỏi
        $query_update = "INSERT INTO cauhoi (SttCau, CauHoi, HinhAnh, CauA, CauB, CauC, CauD, DapAn, CauHoiLiet) 
        VALUES ('$sttCau', '$cauHoi', '$target_file', '$cauA', '$cauB', '$cauC', '$cauD', '$dapAn','$cauhoiliet')";
    }

    if (mysqli_query($connect, $query_update)) {
        echo "<p style='text-align: center;'>Câu hỏi đã được cập nhật thành công.</p>";
        header("Location: GDcapnhap.php");
    } else {
        echo "<p style='text-align: center;'>Lỗi: " . mysqli_error($connect) . "</p>";
    }
}

// Đóng kết nối đến cơ sở dữ liệu
mysqli_close($connect);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <style>
        /* CSS để căn giữa bảng */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            width: 80%;
            /* Đặt chiều rộng của form */
            margin: auto;
            /* Canh giữa form */
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
        }

        input[type="text"],
        textarea,
        select {
            width: calc(100% - 16px);
            /* Chiều rộng trừ đi khoảng cách padding */
            padding: 8px;
            margin-top: 6px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="file"] {
            margin-top: 6px;
            margin-bottom: 16px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            /* Đặt chiều rộng button bằng 100% */
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }

        .quaylai-button {
            color :white;
            background-color: #4CAF50;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            padding: 12px 20px;
            width: 90%;
            margin-left: 20px; 
        }

    </style>
</head>

<body>
    <div class="container" style="height: inherit;">
        <h2 style="text-align: center;"><?php echo $title; ?></h2>
        <form action="<?php echo $_SERVER['PHP_SELF'] . ($isEdit ? '?sttCau=' . $sttCau : '?total=' . $total); ?>" method="POST" enctype="multipart/form-data">
            <label>Câu hỏi:</label><br>
            <textarea name="cauHoi" rows="4" cols="50"><?php echo $cauHoi; ?></textarea><br>

            <label>Đáp án A:</label><br>
            <input type="text" name="cauA" value="<?php echo $cauA; ?>"><br>

            <label>Đáp án B:</label><br>
            <input type="text" name="cauB" value="<?php echo $cauB; ?>"><br>

            <label>Đáp án C:</label><br>
            <input type="text" name="cauC" value="<?php echo $cauC; ?>"><br>

            <label>Đáp án D:</label><br>
            <input type="text" name="cauD" value="<?php echo $cauD; ?>"><br>

            <label>Câu hỏi liệt:</label><br>
            <select id="cauhoiliet" name="cauhoiliet">
                <option value="01" >Có</option>
                <option value="0" >Không</option>
            </select><br>

            <label>Đáp án đúng:</label><br>
            <select id="dapAn" name="dapAn">
                <option value="1" <?php if ($dapAn == "1")
                    echo "selected"; ?>>1</option>
                <option value="2" <?php if ($dapAn == "2")
                    echo "selected"; ?>>2</option>
                <option value="3" <?php if ($dapAn == "3")
                    echo "selected"; ?>>3</option>
                <option value="4" <?php if ($dapAn == "4")
                    echo "selected"; ?>>4</option>
            </select><br>

            <label>Hình ảnh:</label><br>
            <input type="file" id="hinhAnh" name="hinhAnh"><br>

            <div class = "button-container">
                <input type="submit" value="<?php echo $btnTitle; ?>" >
                <a href="GDcapnhap.php" class="quaylai-button">Quay lại</a>        
            </div>
    
        </form>

    </div>
</body>

</html>