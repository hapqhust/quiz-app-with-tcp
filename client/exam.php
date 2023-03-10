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
    <link href="assets/css/exam.css" rel="stylesheet" />
</head>

<body id="exam-list">
    <?php
    require_once 'ProtocolCode/RequestCode.php';
    require_once 'ProtocolCode/ResponseCode.php';
    require_once 'Entity/Exam.php';

    use ProtocolCode\RequestCode;
    use ProtocolCode\ResponseCode;

    session_start();

    if (isset($_POST['begin'])) {

        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");
        // connect to server
        $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

        $msg = RequestCode::CHECK_JOINED_EXAM . "|" . $_SESSION["username"] . "|" . $_POST['id'] . "|" . $_SESSION['permission'] . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");

        $response = explode("|", $response);

        if($response[0] == ResponseCode::QUERY_FAIL) {
            echo "<script>alert('Loading fail');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }

        if ($response[0] == ResponseCode::NOT_PARTICIPATED && (!isset($_SESSION['mode']) || $_SESSION['mode'] == "none")) {
            $exam_id = $_POST['id'];
            $_SESSION['mode'] = "exam";
            $_SESSION['total_question'] = $_SESSION["exam_list"][$exam_id]->get_num_question();
            $_SESSION['current_question'] = 1;
            $_SESSION['exam_id'] = $exam_id;
            $_SESSION['is_begin'] = true;
            $_SESSION["score"] = 0;
            echo "<script> alert(B???n s??? b???t ?????u thi '$exam_id'); </script>";
            echo "<script>window.location.href = 'question_exam.php';</script>";
        } else {
            if($response[0] == ResponseCode::PARTICIPATED){
                echo "<script>
                    alert('B???n ch??? c?? th??? thi m???t l???n. H??y ch???n m???t b??i thi m???i.'); 
                </script>";
            }
            elseif ($_SESSION['mode'] == "practice") {
                echo "<script>
                    alert('B???n ??ang ??? trong m???t b??i luy???n t???p kh??c, h??y ti???p t???c?') 
                    window.location.href = 'question_practice.php';
                </script>";
            } else {
                echo "<script>
                    alert('B???n ??ang ??? trong m???t b??i thi kh??c, h??y ti???p t???c?') 
                    window.location.href = 'question_exam.php';
                </script>";
            }
        }
    }elseif(isset($_POST['view-rank'])){
        $exam_id = $_POST['id'];
        $_SESSION['view_exam'] = $exam_id;
        $_SESSION['total_question'] = $_SESSION["exam_list"][$exam_id]->get_num_question();
        echo "<script>window.location.href = 'dashboard.php';</script>";
    }
    ?>
    <?php

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

    // connect to server
    $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

    $msg = RequestCode::SHOW_LIST_EXAM . "|" . "0" . "|";

    $ret = socket_write($socket, $msg, strlen($msg));
    if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

    // receive response from server
    $response = socket_read($socket, 1024);
    if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");

    $response = explode("|", $response);

    if ($response[0] == ResponseCode::NUM_EXAM) {
        $_SESSION['num_exam'] = $response[1];
        $_SESSION["position"] = 1;
        $_SESSION['exam_list'] = array();
    } else {
        echo "<script>alert('Loading fail');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
    }
    while ($_SESSION['position'] <= $_SESSION['num_exam']) {
        $msg = RequestCode::SHOW_LIST_EXAM . "|" . $_SESSION["position"] . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");
        //echo $response;

        // split response from server
        $response = explode("|", $response);

        if ($response[0] == ResponseCode::SHOW_EXAM_DETAIL) {
            $p = new Exam();
            $p->set_id($response[1]);
            $p->set_name($response[2]);
            $p->set_topic($response[3]);
            $p->set_num_question($response[4]);
            $p->set_time($response[5]);
            $p->set_time_start($response[6]);
            $p->set_time_close($response[7]);
        } else {
            echo "<script>alert('Loading fail');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }
        $_SESSION["exam_list"][$_SESSION["position"]] = $p;
        $_SESSION["position"] += 1;
    }
    socket_close($socket);
    ?>
    <script type="text/javascript" language="JavaScript">
        function moveToQuestion() {
            window.location = './question.php';
        }
    </script>
    <?php include_once("navbar.php") ?>
    <section class="page-section exam" id="exam">
        <div class="container">
            <div class="exam-title justify-content-between my-5">
                <h2 class="page-section-heading text-uppercase text-secondary mb-0">Danh s??ch c??c b??i ki???m tra</h2>
                <?php
                if ($_SESSION['permission'] == "admin")
                    echo "<a href=\"./add_new_exam.php\" class=\"btn btn-primary\">Th??m m???i <i class=\"fas fa-plus mx-1\"></i></a>"
                ?>
            </div>
            <div class="row justify-content-start">
                <?php
                if (isset($_SESSION['num_exam']))
                    $total = $_SESSION['num_exam'];
                else
                    $total = 0;
                for ($i = 1; $i <= $total; $i++) {
                    echo "<div class=\"col-md-6 col-lg-3 mb-5 text-center \">
                                <div class=\"card\">
                                    <div class=\"image\">
                                        <img class=\"img-fluid-exam\" src=\"assets/img/exam/exam.jpg\" alt=\"...\" />
                                    </div>
                                        <div class=\"card-body\">
                                        <h5 class=\"card-title\">" . $_SESSION['exam_list'][$i]->get_name() . "</h5>
                                        <p class=\"card-text mb-0\"> Ch??? ?????: " . $_SESSION['exam_list'][$i]->get_topic() . "</p>
                                        <p class=\"card-text mb-0\"> S??? c??u h???i: " . $_SESSION['exam_list'][$i]->get_num_question() . " c??u </p>
                                        <p class=\"card-text mb-2\"> Th???i gian l??m b??i: " . $_SESSION['exam_list'][$i]->get_time() . " ph??t</p>
                                        <p class=\"card-text mb-2\"> Th???i gian b???t ?????u: " . $_SESSION['exam_list'][$i]->get_time_start() . "</p>
                                        <p class=\"card-text mb-2\"> Th???i gian k???t th??c: " . $_SESSION['exam_list'][$i]->get_time_close() . "</p>
                                        <form id=\"startForm\" action=\"exam.php\"  method=\"POST\">
                                            <input type=\"hidden\" name=\"id\" value=\"" . $_SESSION['exam_list'][$i]->get_id() . "\"/>";

                    $ends = DateTime::createFromFormat('d/m/Y-H:i:s', trim($_SESSION['exam_list'][$i]->get_time_close()));
                    $start = DateTime::createFromFormat('d/m/Y-H:i:s', trim($_SESSION['exam_list'][$i]->get_time_start()));
                    // var_dump($start);
                    // var_dump($_SESSION['exam_list'][$i]->get_time_start());
                    $now = new DateTime();
                    if ($now->getTimestamp() > $ends->getTimestamp()) {
                        // echo "<input type=\"submit\" id=\"" . $_SESSION['exam_list'][$i]->get_id() . "\" class=\"btn btn-primary mx-1 mt-2\" name=\"view-result\" value =\"K???t qu???\">";
                        echo "<input type=\"submit\" id=\"" . $_SESSION['exam_list'][$i]->get_id() . "\" class=\"btn btn-primary mx-1 mt-2\" name=\"view-rank\" value =\"B???ng x???p h???ng\">";
                    } elseif (($now->getTimestamp() >= $start->getTimestamp() && $now->getTimestamp() <= $ends->getTimestamp()) ||  $_SESSION['permission'] == "admin") {
                        echo "<input type=\"submit\" id=\"" . $_SESSION['exam_list'][$i]->get_id() . "\" class=\"btn btn-primary mt-2\" name=\"begin\" value =\"B???t ?????u l??m b??i\">";
                    } else {
                        echo "<input type=\"submit\" id=\"" . $_SESSION['exam_list'][$i]->get_id() . "\" class=\"btn btn-primary mt-2\" name=\"begin\" value =\"B???t ?????u l??m b??i\" disabled>";
                    }

                    echo "</form>
                                    </div>
                                </div>
                            </div>";
                }
                ?>
            </div>
        </div>
    </section>
</body>

</html>