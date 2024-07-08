<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['dangxuat'])) {
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    header("Location: GDtrangchu.php"); // Redirect to the homepage
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background-color: #f5f5f5;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2e8b57;
            padding: 10px 20px;
            color: white;
        }

        .header .logo img {
            height: 50px;
            width: auto;
        }

        .header .name {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            flex-grow: 1;
        }

        .header .user-info {
            position: relative;
            display: flex;
            align-items: center;
        }

        .header .user-info .username {
            cursor: pointer;
            margin-right: 10px;
            position: relative;
        }

        .header .user-info .dropdown {
            display: none;
            position: absolute;
            top: 30px;
            right: 0;
            background-color: white;
            color: black;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .header .user-info .dropdown button {
            display: block;
            background-color: #218838;
            color: white;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            width: 100%;
            text-align: left;
            border-radius: 0;
        }

        .header .user-info .dropdown button:hover {
            background-color: #1e7e34;
        }

        .header button {
            background-color: #218838;
            color: white;
            border: none;
            padding: 10px 15px;
            margin-left: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .header button:hover {
            background-color: #1e7e34;
        }

        .nav {
            background-color: #2f9e41;
            overflow: hidden;
        }

        .nav a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .nav a:hover {
            background-color: #1e7e34;
        }

        .menu-container {
            display: flex;
            justify-content: center;
            padding: 20px;
        }

        .menu button, .menu div {
            margin: 10px;
            padding: 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s;
        }

        .menu button:hover, .menu div:hover {
            background-color: #218838;
        }

        .mota {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: #e9f7ef;
            padding: 50px 20px;
            text-align: center;
        }

        .mota h2 {
            font-size: 28px;
            color: #155724;
        }

        .mota p {
            font-size: 16px;
            color: #155724;
            max-width: 800px;
        }

        .footer {
            background-color: #2f9e41;
            color: white;
            text-align: center;
            padding: 10px 20px;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .footer a {
            color: #d4edda;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer a:hover {
            color: white;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="GDtrangchu.php" class="logo">
            <img src="image/logo.jpg" alt="Logo">
        </a>
        <div class="name">TRANG CHỦ THI LÁI XE A1</div>
        <div class="user-info">
            <?php if (!isset($_SESSION['tdnThiSinh'])): ?>
                <button onclick="window.location.href='GDDangnhap.php'">Đăng nhập</button>
                <button onclick="window.location.href='GDdangky.php'">Đăng ký</button>
            <?php else: ?>
                <span class="username" onclick="toggleDropdown()"><?php echo $_SESSION['tdnThiSinh']; ?></span>
                <div class="dropdown" id="dropdownMenu">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <button type="submit" name="dangxuat">Đăng xuất</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="nav">
        <?php if (isset($_SESSION['tdnThiSinh'])): ?>
            <a href="GDchonde.php">Thi thử</a>
            <a href="GDThiThiet.php">Thi thiệt</a>
            <a href="GDThicauhoiliet.php">Câu hỏi liệt</a>
            <a href="GDxemlaibaithi.php">Xem lại bài thi</a>
        <?php endif; ?>
    </div>

    <div class="mota">
        <h2>Chào mừng bạn đến với hệ thống</h2>
        <p>Cấu trúc bộ đề thi sát hạch giấy phép lái xe hạng A1 sẽ bao gồm 25 câu hỏi, mỗi câu hỏi chỉ có duy nhất 1 đáp trả lời đúng. Khác hẳn với bộ đề thi luật cũ là 2 đáp án. Mỗi đề thi chúng tôi sẽ bố trí từ 2 - 4 câu hỏi điểm liệt để học viên có thể làm quen và ghi nhớ, tránh việc làm sai câu hỏi liệt.</p>
        <p><strong>Số lượng câu hỏi:</strong> 25 Câu.<br>
        <strong>Yêu cầu:</strong> Làm đúng 21/25 Câu.<br>
        <strong>Thời gian:</strong> 19 Phút.<br>
        <strong>Lưu ý đặc biệt:</strong> Tuyệt đối không được làm sai câu hỏi điểm liệt, vì trong kỳ thi thật nếu học viên làm sai "Câu Điểm Liệt" đồng nghĩa với việc "KHÔNG ĐẠT" dù cho các câu khác trả lời đúng!</p>
        <img src="image/thilaixea1.jpg" style="width: auto; height: auto;" alt="Ảnh minh họa">
    </div>
    <div class="mota">
        <p>Phần mềm được phát triển dựa trên cấu trúc bộ đề thi sát hạch lý thuyết lái xe mô tô hạng A1 do Tổng Cục Đường Bộ Việt Nam quy định trong kỳ thi sát hạch chính thức.</p>
        <p>Để tập phần thi lý thuyết bằng lái xe A1 tốt nhất, các học viên có thể sử dụng trực tiếp 8 bộ đề thi này. Bởi chúng tôi đã tổng hợp đầy đủ 200 câu hỏi thi bằng lái xe máy A1 đã đánh dấu sẵn đáp án và câu hỏi điểm liệt.</p>
        <p>Học viên có thể sử dụng trực tiếp phần mềm luyện ôn thi GPLX A1 online này trực tiếp trên điện thoại iphone & android hoặc máy tính mà không cần phải tải về hay cài đặt. Chỉ yêu cầu có kết nối mạng wifi/4G vô cùng tiện lợi.</p>
        <p>Khi hoàn thành đủ 8 bộ đề thi bằng lái xe A1 sẽ giúp các bạn có thể nắm rõ được toàn bộ nội dung 200 câu hỏi A1 do Tổng Cục Đường Bộ VN áp dụng trong kỳ thi sát hạch GPLX A1 ở thời điểm hiện tại.</p>
        <p>Nếu có bất kỳ thắc mắc cần giải đáp về câu hỏi trong đề thi, học viên hãy nhập theo cú pháp "thứ tự câu hỏi + đề số..." để chúng tôi giải đáp trực tiếp!</p>
    </div>

    <div class="mota">
        <h2>Video hướng dẫn thi sa hình</h2>
        <iframe width="700" height="400" src="https://www.youtube.com/embed/ISJeeUw_xKs?ab_channel=TRƯỜNGDẠYLÁIXETÂNSƠN" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>

    <div class="footer">
        <p>&copy; 2024 Thi Thử Lái Xe A1. All rights reserved.</p>
        <p><a href="contact.php">Liên hệ chúng tôi</a></p>
    </div>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdownMenu");
            if (dropdown.style.display === "none" || dropdown.style.display === "") {
                dropdown.style.display = "block";
            } else {
                dropdown.style.display = "none";
            }
        }

        window.onclick = function(event) {
            if (!event.target.matches('.username')) {
                var dropdowns = document.getElementsByClassName("dropdown");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.style.display === "block") {
                        openDropdown.style.display = "none";
                    }
                }
            }
        }
    </script>
</body>
</html>
