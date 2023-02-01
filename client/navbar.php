<nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav2">
    <div class="container">
        <a class="navbar-brand" href="./index.php">Quiz App</a>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <div class="navbar-nav ms-auto">
                <div class="nav-item mx-0 mx-lg-1">
                    <p class="nav-link px-0 px-lg-3"> <?php if (isset($_SESSION['username'])) echo $_SESSION['username']; ?></p>
                </div>
                <div class="nav-item mx-0 mx-lg-1">
                    <?php
                    if (isset($_SESSION['username'])) echo
                    "<img class=\"pt-1\" src=\"assets/img/user.png\" alt=\"User\" width=\"35\">";
                    ?>
                </div>
                <div class="nav-item mx-2 mx-lg-1">
                    <?php
                    if (isset($_SESSION['username'])) echo
                    "<form action=\"logout.php\" method=\"post\">
                            <input type=\"submit\" class=\"form-control btn btn-primary\" name=\"logout\" value=\"ĐĂNG XUẤT\"/>
                        </form>";
                    ?>
                </div>
            </div>
        </div>
    </div>
</nav>