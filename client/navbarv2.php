<script type="text/javascript" language="JavaScript">
    function login() {
        window.location = './login.php';
    }

    function register() {
        window.location = './register.php';
    }
</script>

<nav class="navbar navbar-expand-lg bg-secondary text-uppercase fixed-top" id="mainNav2">
    <div class="container">
        <a class="navbar-brand" href="./index.php">Quiz App</a>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <div class="navbar-nav ms-auto">
                <div class="nav-item mx-0 mx-lg-1">
                    <button onclick="login()" class="btn btn-primary">Đăng Nhập</button>
                </div>
                <div class="nav-item mx-0 mx-lg-1">
                    <button onclick="register()" class="btn btn-primary">Đăng Ký</button>
                </div>
            </div>
        </div>
    </div>
</nav>