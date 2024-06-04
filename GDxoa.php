<?php
// Kiểm tra xem có dữ liệu POST được gửi từ JavaScript hay không
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sttCau'])) {
    // Lấy sttCau từ dữ liệu POST
    $sttCau = $_POST['sttCau'];

    // Kết nối đến cơ sở dữ liệu
    $connect = mysqli_connect("localhost", "root", "", "thilaixea1");

    // Kiểm tra kết nối
    if (!$connect) {
        die("Kết nối không thành công: " . mysqli_connect_error());
    }

    // Xây dựng câu lệnh SQL để xóa câu hỏi với sttCau tương ứng
    $sql = "DELETE FROM cauhoi WHERE SttCau = $sttCau";

    // Thực thi câu lệnh SQL
    if (mysqli_query($connect, $sql)) {
        echo "Câu hỏi đã được xóa thành công!";
    } else {
        echo "Lỗi khi xóa câu hỏi: " . mysqli_error($connect);
    }

    // Đóng kết nối
    mysqli_close($connect);
}
?>
