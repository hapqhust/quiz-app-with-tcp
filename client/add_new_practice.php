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
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="assets/css/navbar.css" rel="stylesheet" />
    <link href="assets/css/add_new.css" rel="stylesheet" />

    <?php
    session_start();

    if (isset($_POST['submit'])) {
        $name = $_POST['name'];
        $topic = $_POST['topic'];
        $num_question = $_POST['num_question'];
        $time = $_POST['time'];
        $date   = new DateTime(); //this returns the current date time
        $created_at = $date->format('d/m/Y-H:i:s');
        $success = 1;

        // echo "<script> alert('$topic . $num_question . $time'); </script>";

        // if (!isset($name) || strlen($name) == 0) {
        //     $name_error = "Tên chủ đề là bắt buộc";
        //     $success = 0;
        // } elseif (!preg_match("/^[a-zA-Z]+$/", $name)) {
        //     $name_error = "Chủ đề không hợp lệ";
        //     $success = 0;
        // }

        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

        // connect to server
        $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

        $msg = "06|" . $name . "|" . $topic . "|" . $num_question . "|" . $time . "|" . $created_at . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");

        $response = explode("|", $response);

        if ($response[0] == "3") {
            echo "<script>alert('Creating a new practice is unsuccessful !');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        } else if ($response[0] == "5") {
            echo "<script>window.location.href = 'practice.php';</script>";
        }
        socket_close($socket);
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
    for ($i = 1; $i <= $_SESSION['num_topic']; $i++) {
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
                    <form id="contactForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                        <div class="form-group row">
                            <div class=" col-lg-12 mt-2">
                                <label class="col-form-label-lg text-right col-lg-4" style="color: #6c757d; font-size: 1.1rem "> Tên bài luyện tập <span class="text-danger">*</span></label>
                                <input type="text" class="form-control py-2" placeholder="Nhập tên bài luyện tập " id="name" name="name" required />
                            </div>
                        </div>
                        <!-- Topic input-->
                        <div class="form-group row">
                            <label class="col-form-label-lg text-right col-lg-4" style="color: #6c757d; font-size: 1.1rem ">Chủ đề
                                bài thi <span class="text-danger">*</span></label>
                            <div class=" col-lg-12 mt-2">
                                <select class="form-control py-2 form-select" id="topic" name="topic" required>
                                    <?php if (isset($_SESSION['topic_list'])) {
                                        foreach ($_SESSION['topic_list'] as $key => $value) {
                                            echo "<option>$value</option>";
                                        }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <!-- Number Question input-->
                        <div class="form-group row">
                            <label class="col-form-label-lg text-right col-lg-4" style="color: #6c757d; font-size: 1.1rem ">Số lượng câu hỏi <span class="text-danger">*</span></label>
                            <div class=" col-lg-12 mt-2">
                                <select class="form-control py-2 form-select" id="num_question" name="num_question" required>
                                    <?php
                                    for ($i = 2; $i <= 10; $i++) {
                                        echo "<option>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <!-- Time input-->
                        <div class="form-group row">
                            <label class="col-form-label-lg text-right col-lg-4" style="color: #6c757d; font-size: 1.1rem ">Thời gian làm bài <span class="text-danger">*</span></label>
                            <div class=" col-lg-12 mt-2">
                                <select class="form-control py-2 form-select" id="time" name="time" required>
                                    <option>5</option>
                                    <option>10</option>
                                    <option>15</option>
                                    <option>20</option>
                                </select>
                            </div>
                        </div>
                        <!-- Submit Button-->
                        <input class=" form-control button btn btn-primary btn-lg mt-5" id="submitButton" type="submit" name="submit" value="Tạo bài luyện tập mới"> </input>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>

</html>