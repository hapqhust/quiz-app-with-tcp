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
    <link href="assets/css/practice.css" rel="stylesheet" />

</head>

<body id="practice-list">
    <?php
    session_start();

    if (isset($_POST['begin'])) {
        $practice_id = $_POST['id'];
        $_SESSION['mode'] = "practice";
        $_SESSION['total_question'] = $_SESSION["practice_list"][$practice_id]->get_num_question();
        $_SESSION['current_question'] = 1;
        $_SESSION['practice_id'] = $practice_id;
        $_SESSION['is_begin'] = true;
        $_SESSION["score"] = 0;
        echo "<script> alert('$practice_id'); </script>";
        echo "<script>window.location.href = 'question.php';</script>";


        // $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

        // // connect to server
        // $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

        // $msg = "06|" . $name . "|" . $topic . "|" . $num_question . "|" . $time . "|" . $created_at . "|";

        // $ret = socket_write($socket, $msg, strlen($msg));
        // if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // // receive response from server
        // $response = socket_read($socket, 1024);
        // if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");

        // $response = explode("|", $response);

        // if ($response[0] == "3") {
        //     echo "<script>alert('Creating a new practice is unsuccessful !');</script>";
        //     echo "<script>window.location.href = 'index.php';</script>";

        // } else if ($response[0] == "5") {
        //     echo "<script>window.location.href = 'practice.php';</script>";
        // }
        // socket_close($socket);
    }
    ?>
    <?php
    class Practice
    {
        // Properties
        public $id;
        public $name;
        public $topic;
        public $time;
        public $num_question;

        // Methods
        function set_id($id)
        {
            $this->id = $id;
        }
        function get_id()
        {
            return $this->id;
        }

        function set_name($name)
        {
            $this->name = $name;
        }
        function get_name()
        {
            return $this->name;
        }
        function set_topic($topic)
        {
            $this->topic = $topic;
        }
        function get_topic()
        {
            return $this->topic;
        }

        function set_time($time)
        {
            $this->time = $time;
        }
        function get_time()
        {
            return $this->time;
        }

        function set_num_question($num_question)
        {
            $this->num_question = $num_question;
        }
        function get_num_question()
        {
            return $this->num_question;
        }
    }
    ?>
    <?php
    session_start();

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

    // connect to server
    $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

    $msg = "03|" . "0" . "|";

    $ret = socket_write($socket, $msg, strlen($msg));
    if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

    // receive response from server
    $response = socket_read($socket, 1024);
    if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");

    $response = explode("|", $response);

    if ($response[0] == "1") {
        $_SESSION['num_practice'] = $response[1];
        $_SESSION["position"] = 1;
        $_SESSION['practice_list'] = array();
    } else {
        echo "<script>alert('Game loading fail');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
    }
    while ($_SESSION['position'] <= $_SESSION['num_practice']) {
        $msg = "03|" . $_SESSION["position"] . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");
        //echo $response;

        // split response from server
        $response = explode("|", $response);

        if ($response[0] == "2") {
            $p = new Practice();
            $p->set_id($response[1]);
            $p->set_name($response[2]);
            $p->set_topic($response[3]);
            $p->set_num_question($response[4]);
            $p->set_time($response[5]);
        } else {
            echo "<script>alert('Game loading fail');</script>";
            echo "<script>window.location.href = 'home.php';</script>";
        }
        $_SESSION["practice_list"][$_SESSION["position"]] = $p;
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
    <section class="page-section practice" id="practice">
        <div class="container">
            <div class="practice-title justify-content-between my-5">
                <h2 class="page-section-heading text-uppercase text-secondary mb-0">Danh sách các bài luyện tập</h2>
                <a href="./add_new_practice.php" class="btn btn-primary">Thêm mới <i class="fas fa-plus mx-1"></i>
                </a>
            </div>
            <div class="row justify-content-start">
                <?php
                if (isset($_SESSION['num_practice']))
                    $total = $_SESSION['num_practice'];
                else
                    $total = 0;
                for ($i = 1; $i <= $total; $i++) {
                    echo ("<div class=\"col-md-6 col-lg-3 mb-5 text-center \">
                                <div class=\"card\">
                                    <div class=\"image\">
                                        <img class=\"img-fluid-practice\" src=\"assets/img/practice/practice.jpg\" alt=\"...\" />
                                    </div>
                                        <div class=\"card-body\">
                                        <h5 class=\"card-title\">" . $_SESSION['practice_list'][$i]->get_name() . "</h5>
                                        <p class=\"card-text mb-0\"> Chủ đề: " . $_SESSION['practice_list'][$i]->get_topic() . "</p>
                                        <p class=\"card-text mb-0\"> Số câu hỏi: " . $_SESSION['practice_list'][$i]->get_num_question() . " câu </p>
                                        <p class=\"card-text mb-2\"> Thời gian: " . $_SESSION['practice_list'][$i]->get_time() . " phút</p>
                                        <form id=\"startForm\" action=\"practice.php\"  method=\"POST\">
                                            <input type=\"hidden\" name=\"id\" value=\"" . $_SESSION['practice_list'][$i]->get_id() . "\"/>
                                            <input type=\"submit\" id=\"" . $_SESSION['practice_list'][$i]->get_id() . "\" class=\"btn btn-primary\" name=\"begin\" value =\"Bắt đầu làm bài\">
                                        </form>
                                    </div>
                                </div>
                            </div>");
                }
                ?>
            </div>
        </div>
    </section>
</body>

</html>