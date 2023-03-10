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
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.1.0/mdb.min.css" rel="stylesheet" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="assets/css/navbar.css" rel="stylesheet" />
    <link href="assets/css/home.css" rel="stylesheet" />
</head>

<body id="page-top">
    <?php
    session_start();
    $host = "127.0.0.1";
    $port = 8888;
    $_SESSION['host_server'] = $host;
    $_SESSION['port'] = $port;

    if (!$_SESSION["username"]) {
        header("Location: login.php");
    }
    ?>
    <script type="text/javascript" language="JavaScript">
        function moveToPractice() {
            window.location = './practice.php'
        }

        function moveToExam() {
            window.location = './exam.php'
        }
    </script>
    <?php include_once("navbar.php") ?>
    <section class="page-section portfolio" id="portfolio">
        <div class="container">
            <div class=" d-flex align-items-center flex-column">
                <img class="masthead-avatar mb-5" src="assets/img/avataaars.svg" alt="..." />
            </div>
            <h2 class="page-section-heading text-center text-uppercase text-secondary mb-0">H??y ch???n trong s??? c??c
                ch???c
                n??ng d?????i ????y
            </h2>
            <!-- Icon Divider-->
            <div class="divider-custom">
                <div class="divider-custom-line"></div>
                <div class="divider-custom-icon"><i class="fas fa-star"></i></div>
                <div class="divider-custom-line"></div>
            </div>
            <div class="row justify-content-center mt-3">
                <!-- Portfolio Item 1-->
                <div class="col-md-6 col-lg-4 mb-5">
                    <div class="portfolio-item mx-auto" onclick="moveToPractice()">
                        <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                            <div class="portfolio-item-caption-content text-center text-white">
                                <h2>CH??? ????? LUY???N T???P</h2>
                            </div>
                        </div>
                        <img class="img-fluid" src="assets/img/portfolio/game.png" alt="..." />
                    </div>
                </div>
                <!-- Portfolio Item 2-->
                <div class="col-md-6 col-lg-4 mb-5">
                    <div class="portfolio-item mx-auto" onclick="moveToExam()">
                        <div class="portfolio-item-caption d-flex align-items-center justify-content-center h-100 w-100">
                            <div class="portfolio-item-caption-content text-center text-white">
                                <h2>CH??? ????? THI</h2>
                            </div>
                        </div>
                        <img class="img-fluid" src="assets/img/portfolio/submarine.png" alt="..." />
                    </div>
                </div>

            </div>
        </div>
    </section>
</body>

</html>