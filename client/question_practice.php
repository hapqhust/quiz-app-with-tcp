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
    <link href="assets/css/question.css" rel="stylesheet" />
</head>

<body id="question">

    <?php
    require_once 'ProtocolCode/RequestCode.php';
    require_once 'ProtocolCode/ResponseCode.php';
    require_once 'Entity/Practice.php';

    use ProtocolCode\ResponseCode;
    use ProtocolCode\RequestCode;


    session_start();
    if (isset($_SESSION['mode']) && $_SESSION['mode'] == "practice" && $_SESSION['is_begin']) {

        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

        // connect to server
        $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

        $msg = RequestCode::JOIN_PRACTICE . "|" . $_SESSION['practice_id'] . "|"  . "0|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");
        //echo $response;

        // split response from server
        $response = explode("|", $response);

        if ($response[0] == ResponseCode::LIST_QUESTION_ID) {
            $sum = $response[1];
            $_SESSION["list_question_id"] = array();
            for ($i = 1; $i <= $sum; $i++) {
                $_SESSION["list_question_id"][$i] = $response[$i + 1];
            }
        } else {
            echo "<script>alert('Loading fail');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }
        $_SESSION['is_begin'] = false;
        socket_close($socket);
    }
    ?>
    <?php
    if (isset($_POST['next'])) {

        unset($_POST['next']);
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

        // connect to server
        $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

        if (isset($_POST['answer'])) {
            $answer = $_POST['answer'];
        } else {
            $answer = 5;
        }
        $msg = RequestCode::ANSWER . "|" . $_SESSION["question_id"] . "|" . $answer . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");
        //echo $response;

        // split response from server
        $response = explode("|", $response);

        if ($response[0] == ResponseCode::ANSWER_CORRECT) {
            $_SESSION["score"] += 1;
        } elseif ($response[0] == ResponseCode::ANSWER_INCORRECT) {
            $_SESSION["score"] += 0;
        } else {
            echo "<script>alert('Loading fail');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }

        $_SESSION["current_question"] = $_SESSION["current_question"] + 1;
        if ($_SESSION["current_question"] > $_SESSION["total_question"]) {
            echo "<script>window.location.href = 'score.php';</script>";
        }
        // close socket
        socket_close($socket);
    }
    ?>

    <?php
    if (isset($_POST['submit'])) {
        unset($_POST['submit']);
        echo "<script>sessionStorage.removeItem('end2');</script>";
        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

        // connect to server
        $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

        if (isset($_POST['answer'])) {
            $answer = $_POST['answer'];
        } else {
            $answer = 5;
        }
        $msg = RequestCode::ANSWER . "|" . $_SESSION["question_id"] . "|" . $answer . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");
        //echo $response;

        // split response from server
        $response = explode("|", $response);

        if ($response[0] == ResponseCode::ANSWER_CORRECT) {
            $_SESSION["score"] += 1;
        } elseif ($response[0] == ResponseCode::ANSWER_INCORRECT) {
            $_SESSION["score"] += 0;
        } else {
            echo "<script>alert('Loading fail');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }

        echo "<script>
            alert('Bạn đã nộp bài thành công');
            window.location.href = 'score.php'
        </script>";

        socket_close($socket);
    }
    ?>

    <?php
    if (isset($_SESSION['mode']) && $_SESSION['mode'] == "practice" && $_SESSION["current_question"] <= $_SESSION["total_question"]) {

        $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

        // connect to server
        $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

        $msg =  RequestCode::JOIN_PRACTICE . "|" . $_SESSION['practice_id'] . "|" . $_SESSION['list_question_id'][$_SESSION['current_question']] . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");
        //echo $response;

        // split response from server
        $response = explode("|", $response);

        if ($response[0] == ResponseCode::QUESTION) {
            $_SESSION["question_id"] = $response[1];
            $_SESSION["question"] = $response[2];
            $_SESSION["answerA"] = $response[3];
            $_SESSION["answerB"] = $response[4];
            $_SESSION["answerC"] = $response[5];
            $_SESSION["answerD"] = $response[6];
        } else {
            echo "<script>alert('Loading fail');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }
        socket_close($socket);
    }
    ?>
    <?php include_once("navbar.php") ?>
    <section class="page-section" id="contact">
        <div class="container">
            <!-- Contact Section Heading-->
            <h4 class="page-section-heading text-end text-uppercase text-secondary mt-4 mb-" 2>Câu:
                <?php echo (string)$_SESSION["current_question"] . '/' . (string)$_SESSION["total_question"] ?></h4>
            <!-- Contact Section Form-->
            <div class="row">
                <h5 class="time-left" style="margin-top:-36px; font-size: 1.2rem;">Thời gian còn lại:</h5>
                <h6 id="countdown2" class="time-left" style="font-size: 1.4rem;"></h6>
            </div>
            <h5 class="page-section-question text-center text-secondary mt-4 mb-5">
                <?php echo $_SESSION["question"] ?></h5>
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <form id="questionForm2" action="./question_practice.php" method="POST">
                        <!-- Topic input-->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="answer" id="answer1" value="1" required>
                            <label class="form-check-label" for="answer1">
                                <?php echo $_SESSION["answerA"] ?>
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="answer" id="answer2" value="2">
                            <label class="form-check-label" for="answer2">
                                <?php echo $_SESSION["answerB"] ?>
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="answer" id="answer3" value="3">
                            <label class="form-check-label" for="answer2">
                                <?php echo $_SESSION["answerC"] ?>
                            </label>
                        </div>
                        <div class=" form-check mb-4">
                            <input class="form-check-input" type="radio" name="answer" id="answer4" value="4">
                            <label class="form-check-label" for="answer4">
                                <?php echo $_SESSION["answerD"] ?>
                            </label>
                        </div>
                        <!-- Submit Button-->
                        <div class="row justify-content-center">
                            <input class="col-3 button btn btn-primary btn-lg mt-5 mx-3" id="submitButtonMain" type="submit" name="submit" value="Nộp bài"></input>
                            <?php
                            if ($_SESSION["current_question"] < $_SESSION["total_question"]) {
                                echo "<input class=\"col-3 button btn btn-secondary2 btn-lg mt-5 mx-3\" id=\"submitButton\" type=\"submit\" name=\"next\" value=\"Tiếp tục\" onclick=\"myFunction()\"> </input>";
                            }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <script>
        // automatically trigger the click event of button A
        // Set the target date and time (in this practiceple, it's set to February 28, 2023 at 10:00 AM)
        if (sessionStorage.getItem("end2") === null) {
            var target_date = new Date();
            var now = new Date();
            var time = <?php echo $_SESSION["practice_list"][$_SESSION['practice_id']]->get_time() ?>;
            target_date.setTime(now.getTime() + time * 60 * 1000);
            sessionStorage.setItem('end2', target_date);
        }
        // Update the countdown every second
        var countdown = setInterval(function() {
            // Get the current date and time
            var current_date = new Date();

            // Calculate the remaining time in seconds
            var remaining_seconds = Math.floor((Date.parse(sessionStorage.getItem("end2")) - current_date) / 1000);

            if (remaining_seconds <= 0) {
                // Clear the interval to stop the countdown
                clearInterval(countdown);
                sessionStorage.removeItem("end2");
                alert("Hết thời gian làm bài!");
                window.location.href = 'score.php';
                // Execute some code when the countdown reaches 0
            } else {
                // Convert the remaining time to days, hours, minutes, and seconds
                var hours = Math.floor((remaining_seconds) / (60 * 60));
                var minutes = Math.floor((remaining_seconds - (hours * 60 * 60)) / 60);
                var seconds = Math.floor(remaining_seconds - (hours * 60 * 60) - (minutes * 60));

                // Display the remaining time in the HTML element with id="countdown"
                document.getElementById("countdown2").innerHTML = "" + hours + " : " + minutes + " : " + seconds;
            }
        }, 1000);
    </script>

</body>

</html>