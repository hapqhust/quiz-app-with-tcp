<html>

<head>
    <title>Hiển thị thông tin</title>
</head>

<body>
    <?php
    // session_start();

    if (isset($_POST['submit'])) {
        $topic = $_POST['topic'];
        $num_quesion = $_POST['num_quesion'];
        $success = 1;

        if (!isset($topic)) {
            $username_error = "Tên chủ đề là bắt buộc";
            $success = 0;
        } elseif (!preg_match("/^[a-zA-Z]+$/", $topic)) {
            $username_error = "Chủ đề không hợp lệ";
            $success = 0;
        }
        if (!isset($num_quesion)) {
            $num_quesion_error = "Số lượng câu hỏi là bắt buộc";
            $success = 0;
        } elseif ($num_quesion < 3 || $num_quesion > 20) {
            $num_quesion_error = "Số lượng câu hỏi chỉ được nằm trong khoảng 3-20";
            $success = 0;
        }
    }
    echo "error"
    ?>
</body>

</html>