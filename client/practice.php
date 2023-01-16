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
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="assets/css/navbar.css" rel="stylesheet" />
    <link href="assets/css/practice.css" rel="stylesheet" />
</head>

<body id="practice-list">
    <?php include_once("navbar.php") ?>
    <section class="page-section practice" id="practice">
        <div class="container">
            <div class="practice-title justify-content-between my-5">
                <h2 class="page-section-heading text-uppercase text-secondary mb-0">Danh sách các bài luyện tập</h2>
                <a href="./add_new_practice.php" class="btn btn-primary">Thêm mới <i class="fas fa-plus mx-1"></i> </a>
            </div>
            <div class="row justify-content-center">
                <?php
                for ($i = 1; $i <= 8; $i++) {
                    echo ("<div class=\"col-md-6 col-lg-3 mb-5 text-center \">
                                <div class=\"card\">
                                    <div class=\"image\">
                                        <img class=\"img-fluid-practice\" src=\"assets/img/practice/practice.jpg\" alt=\"...\" />
                                    </div>
                                        <div class=\"card-body\">
                                        <h5 class=\"card-title\">Card title</h5>
                                        <p class=\"card-text\">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                                        <a href=\"#\" class=\"btn btn-primary\">Go somewhere</a>
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