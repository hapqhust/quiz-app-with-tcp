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
    <link href="assets/css/question.css" rel="stylesheet" />

    <?php
    session_start();
    // if ((time() - $_SESSION['in_game_timestamp']) > 180) {
    //     echo "<script>alert('Time out');</script>";
    //     unset($_SESSION["in_game_timestamp"]);
    //     header("location:home.php");
    // } else {
    //     $_SESSION["in_game_timestamp"] = time();
    // }
    if(!isset($_SESSION["position"])){
        $_SESSION["position"] = 1;
    }
    if(!isset($_SESSION["total"])){
        $_SESSION["total"] = 10;
    }

    if (isset($_POST['next'])) {
        $answer = $_POST['answer'];
        
        $_SESSION["position"] = $_SESSION["position"] + 1;
        if ($_SESSION["position"] > $_SESSION["total"]) {
            echo "<script>alert('You win!');</script>";
            echo "<script>window.location.href = 'score.php';</script>";
        } else{
            echo "<script>alert('Your answer is $answer');</script>";
            // echo "<script>window.location.href = 'score.php';</script>";
        }
    }
    ?>
</head>

<body id="question">
    <?php include_once("navbar.php") ?>
    <section class="page-section" id="contact">
        <div class="container">
            <!-- Contact Section Heading-->
            <h4 class="page-section-heading text-end text-uppercase text-secondary mt-4 mb-" 2>Câu:
                <?php echo (string)$_SESSION["position"].'/'.(string)$_SESSION["total"] ?></h4>
            <!-- Contact Section Form-->

            <h5 class="page-section-question text-center text-secondary mt-4 mb-5">Đây là câu hỏi số
                <?php echo $_SESSION["position"] ?>? Hãy lựa chọn đáp án đúng nhất</h5>
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-6">
                    <form id="questionForm" action="./question.php" method="POST">
                        <!-- Topic input-->
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="answer" id="answer1" value="option1">
                            <label class="form-check-label" for="answer1">
                                Đáp án 1
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="answer" id="answer2" value="option2">
                            <label class="form-check-label" for="answer2">
                                Đáp án 2
                            </label>
                        </div>
                        <div class="form-check mb-4">
                            <input class="form-check-input" type="radio" name="answer" id="answer3" value="option3">
                            <label class="form-check-label" for="answer2">
                                Đáp án 3
                            </label>
                        </div>
                        <div class=" form-check mb-4">
                            <input class="form-check-input" type="radio" name="answer" id="answer4" value="option4">
                            <label class="form-check-label" for="answer4">
                                Đáp án 4
                            </label>
                        </div>
                        <!-- Submit Button-->
                        <div class="row justify-content-center">
                            <input class="col-3 button btn btn-primary btn-lg mt-5 mx-3" id="submitButton" type="submit"
                                name="submit" value="Nộp bài"></input>
                            <input class="col-3 button btn btn-secondary2 btn-lg mt-5 mx-3" id="submitButton"
                                type="submit" name="next" value="Tiếp tục"> </input>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>

</html>