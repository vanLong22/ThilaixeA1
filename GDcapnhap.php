<?php
    // kết nối csdl 
    $connect = mysqli_connect("localhost", "root", "", "thilaixea1");
    // Số lượng câu hỏi trên mỗi trang
    $questions_per_page = 10;
    // Tính tổng số trang
    $query_all = "SELECT COUNT(*) AS total FROM cauhoi"; // tính tổng số dòng trong db cauhoi
    $result_all = mysqli_query($connect, $query_all);
    $row_all = mysqli_fetch_assoc($result_all);
    $total_pages = ceil($row_all['total'] / $questions_per_page);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách câu hỏi</title>
	<link rel="stylesheet" href="style.css">
    <style>
        /* Định dạng cho bảng */
        table {
            width: 100%; /* Làm cho bảng chiếm 100% chiều rộng của container */
            border-collapse: collapse; /* Gộp các đường viền của bảng lại với nhau */
        }

        th, td {
            padding: 8px; /* Thêm 8px padding bên trong các ô tiêu đề và ô dữ liệu */
            text-align: left; /* Canh chỉnh văn bản sang trái */
            border-bottom: 2px solid #ddd; /* Thêm đường viền dưới màu xám nhạt cho mỗi ô */
        }

        /* Định dạng cho hình ảnh */
        img {
            max-width: 100px; /* Thiết lập chiều rộng tối đa của hình ảnh là 100px */
            max-height: 100px; /* Thiết lập chiều cao tối đa của hình ảnh là 100px */
			width: auto; 
			height: 50px;
        }

        /* Định dạng cho phân trang */
        .pagination {
            display: flex; /* Sử dụng flexbox để định dạng */
            justify-content: center; /* Canh giữa các điều khiển phân trang theo chiều ngang */
            margin-top: 20px; /* Thêm khoảng cách trên cho phân trang */
        }

        .pagination a {
            padding: 8px 16px; /* Thêm padding bên trong các liên kết phân trang */
            margin: 0 4px; /* Thêm khoảng cách giữa các liên kết phân trang */
            text-decoration: none; /* Xóa bỏ gạch chân của các liên kết */
            color: #333; /* Thiết lập màu văn bản là màu xám đậm */
            border: 1px solid #ccc; /* Thêm đường viền màu xám nhạt */
            border-radius: 4px; /* Bo tròn các góc của đường viền */
            transition: background-color 0.3s; /* Thêm hiệu ứng chuyển đổi màu nền */
        }

        .pagination .active {
            background-color: #4CAF50; /* Thiết lập màu nền của liên kết đang hoạt động là màu xanh lá cây */
            color: white; /* Thiết lập màu văn bản của liên kết đang hoạt động là màu trắng */
        }

        /* Định dạng cho nút hành động */
        .action-btn1, .action-btn2 {
            padding: 10px;
            cursor: pointer; /* Thay đổi con trỏ thành hình bàn tay khi di chuột qua */
            background-color: #4CAF50; /* Thiết lập màu nền là màu xanh lá cây */
            color: white; /* Thiết lập màu văn bản là màu trắng */
            border: none; /* Xóa bỏ đường viền */
            border-radius: 4px; /* Bo tròn các góc của nút */
            font-size: 100%;
            text-decoration: none; /* Xóa bỏ gạch chân của liên kết */
            display: inline-block; /* Đảm bảo các nút hiển thị inline-block */
        }

        .action-btn2 {
            margin-top: 5px; /* Thêm khoảng cách giữa các nút trong bảng */
        }
    </style>
</head>
<body>
    <div>
        <div class="header">
            <h2>Danh sách câu hỏi</h2>
            <div>
                <?php echo "<a href='GDsua.php?total=" . $row_all['total'] . "' class='action-btn1'>Thêm câu hỏi</a>"; ?>
                <a href="GDadmin.php" class="action-btn1">Thoát</a>
            </div>
        </div>
        
        <table>
            <tr>
                <th>Số thứ tự</th>
                <th>Câu hỏi</th>
                <th>Đáp án A</th>
                <th>Đáp án B</th>
                <th>Đáp án C</th>
                <th>Đáp án D</th>
                <th>Đáp án đúng</th>
                <th>Hình ảnh</th>
                <th>Hành động</th>
            </tr>
            <?php

            // Kiểm tra kết nối
            if (!$connect) {
                die("Kết nối không thành công: " . mysqli_connect_error());
            }

            // Xác định trang hiện tại
            $current_page = isset($_GET['page']) ? $_GET['page'] : 1;

            

            // Tính vị trí bắt đầu của câu hỏi cho trang hiện tại
            $start_from = ($current_page - 1) * $questions_per_page;

            // Truy vấn SQL để lấy dữ liệu câu hỏi cho trang hiện tại
            $query = "SELECT * FROM cauhoi LIMIT $start_from, $questions_per_page";
            $result = mysqli_query($connect, $query);

            // Kiểm tra xem có dữ liệu trả về không
            if (mysqli_num_rows($result) > 0) {
                // Hiển thị dữ liệu câu hỏi trong bảng
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['SttCau'] . "</td>";
                    echo "<td>" . $row['CauHoi'] . "</td>";
                    echo "<td>" . $row['CauA'] . "</td>";
                    echo "<td>" . $row['CauB'] . "</td>";
                    echo "<td>" . $row['CauC'] . "</td>";
                    echo "<td>" . $row['CauD'] . "</td>";
                    echo "<td>" . $row['DapAn'] . "</td>";
                    echo "<td>";
                    if (!empty($row['HinhAnh'])) {
                        echo '<img src="' . $row['HinhAnh'] . '" alt="Hình ảnh câu hỏi">';
                    } else {
                        echo "";
                    }
                    echo "</td>";
                    echo "<td>";
                    echo "<button class='action-btn2' onclick='deleteQuestion(" . $row['SttCau'] . ")' style='width: 50px'>Xóa</button>";
                    echo "<a href='GDsua.php?sttCau=" . $row['SttCau'] . "' class='action-btn2'>Sửa</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>Không có câu hỏi nào.</td></tr>";
            }
            ?>
        </table>
        
        <div class="pagination">
        <?php
            // Hiển thị các liên kết phân trang
			for ($page = 1; $page <= $total_pages; $page++) {
				if ($page == $current_page) {
					echo "<a class='active' href='?page=$page'>$page</a>";
				} else {
					echo "<a href='?page=$page'>$page</a>";
				}
			}
            ?>
        </div>


    </div>

    <script>
        // JavaScript function to delete a question
        function deleteQuestion(sttCau) {
            if (confirm("Bạn có chắc chắn muốn xóa câu hỏi này không?")) {
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "GDxoa.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        alert(this.responseText);
                        window.location.reload();
                    }
                };
                xhr.send("sttCau=" + sttCau);
            }
        }
    </script>
<?php
    // Đóng kết nối đến cơ sở dữ liệu
    mysqli_close($connect);
?>
</body>
</html>
