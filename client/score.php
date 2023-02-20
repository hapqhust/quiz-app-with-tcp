<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>QUIZ APP - HomePage</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="assets/css/navbar.css" rel="stylesheet" />
    <link href="assets/css/score.css" rel="stylesheet" />
</head>

<body id="score">

    <script type="text/javascript" language="JavaScript">
        function moveToHome() {
            window.location = './index.php'
        }
    </script>
    <?php
    require_once 'ProtocolCode/RequestCode.php';
    require_once 'ProtocolCode/ResponseCode.php';

    use ProtocolCode\ResponseCode;
    use ProtocolCode\RequestCode;

    session_start();


    if ($_SESSION['permission'] == 'user' && $_SESSION['mode'] == 'exam') {

        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

        // connect to server
        $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");
        
        $msg = RequestCode::SAVE_RESULT . "|" . $_SESSION["username"] . "|" . $_SESSION['exam_id'] . "|" . $_SESSION['score'] . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");

        $response = explode("|", $response);
        if ($response[0] == ResponseCode::SAVE_RESULT_SUCCESSFUL) {
        } 
    }

    $_SESSION['mode'] = "none";
    ?>
    <?php include_once("navbar.php") ?>
    <section class="page-section">
        <div class="container">
            <div class=" d-flex align-items-center flex-column">
                <img class="masthead-avatar my-5" src="assets/img/success.png" alt="..." />
            </div>
            <h2 class="page-section-heading text-center text-uppercase text-success mb-0">Chúc mừng bạn đã hoàn thành
                bài thi</h2>
            <!-- Icon Divider-->
            <div class="divider-custom">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <div class="group-2">
                <h2 class="page-section-score text-center text-uppercase text-danger mt-3"> <?php echo (string)$_SESSION["score"] . '/' . (string)$_SESSION["total_question"] ?></h2>
                <button class="btn btn-primary btn-xl mt-4" type="button" onclick="moveToHome()">Quay về trang
                    chủ</button>
            </div>
        </div>
    </section>
</body>

</html>