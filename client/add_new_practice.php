<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>QUIZ APP - Pratice</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet"
        type="text/css" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="assets/css/navbar.css" rel="stylesheet" />
    <link href="assets/css/add_new.css" rel="stylesheet" />

    <?php
    session_start();

    if (isset($_POST['submit'])) {
        $topic = $_POST['topic'];
        $num_question = $_POST['num_question'];
        $success = 1;

        if (!isset($topic) || strlen($topic) == 0) {
            $topic_error = "Tên chủ đề là bắt buộc";
            $success = 0;
        } elseif (!preg_match("/^[a-zA-Z]+$/", $topic)) {
            $topic_error = "Chủ đề không hợp lệ";
            $success = 0;
        }
        if (!isset($num_question)) {
            $num_question_error = "Số lượng câu hỏi là bắt buộc";
            $success = 0;
        } elseif ($num_question < 3 || $num_question > 20) {
            $num_question_error = "Số lượng câu hỏi chỉ được nằm trong khoảng 3-20";
            $success = 0;
        }
        // if($success){
            echo "<script>window.location.href = 'question.php';</script>";

        // }
    }
    ?>
</head>

<body id="add_new_practice">
    <?php
    session_start();

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

    // connect to server
    $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

    $msg = "05|";

    $ret = socket_write($socket, $msg, strlen($msg));
    if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

    // receive response from server
    $response = socket_read($socket, 1024);
    if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");

    $response = explode("|", $response);

    if ($response[0] == "4") {
        $_SESSION['num_topic'] = $response[1];
        $_SESSION['topic_list'] = array();
    }
    for($i = 1; $i <= $_SESSION['num_topic']; $i++){
        $_SESSION['topic_list'][$i] = $response[$i + 1];
    }
    echo print_r($_SESSION['topic_list']);
    socket_close($socket);
    ?>
    <?php include_once("navbar.php") ?>
    <section class="page-section" id="contact">
        <div class="container">
            <!-- Contact Section Heading-->
            <h2 class="page-section-heading text-center text-uppercase text-secondary mt-4 mb-0">Thiết lập thông số bài
                luyện tập
                <?php if(isset($_SESSION['topic_list'])) echo print_r($_SESSION['topic_list']); ?>
            </h2>
            <!-- Icon Divider-->
            <div class="divider-custom">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <!-- Contact Section Form-->
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <form id="contactForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
                        <!-- Topic input-->
                        <div class="form-floating mb-3">
                            <input class="form-control" id="topic" name="topic" type="text" required />
                            <label for="name">Chủ đề bài thi</label>
                        </div>
                        <!-- Number Question input-->
                        <div class="form-floating mb-3">
                            <input class="form-control" id="num_question" name="num_question" type="number" min=3 max=20
                                required />
                            <label for="name">Số lượng câu hỏi</label>
                        </div>
                        <!-- Time input-->
                        <div class="form-floating mb-3">
                            <input class="form-control" id="time" name="time" type="number" min=15 step=15 max=60
                                required />
                            <label for="name">Thời gian làm bài</label>
                        </div>
                        <!-- Submit Button-->
                        <input class=" form-control button btn btn-primary btn-xl mt-5" id="submitButton" type="submit"
                            name="submit" value="Bắt đầu làm bài"> </input>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>

</html>