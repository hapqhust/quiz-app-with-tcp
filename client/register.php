<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>QUIZ APP - Register</title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <!-- Google fonts-->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic" rel="stylesheet" type="text/css" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="assets/css/navbar.css" rel="stylesheet" />
    <?php
    use ProtocolCode\RequestCode;
    use ProtocolCode\ResponseCode;
    
    session_start();
    if (isset($_POST['signup'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];
        $validate = 1;

        if (!preg_match("/^[A-Za-z0-9]+$/", $username)) {
            $username_error = "Username is only character and number";
            $validate = 0;
        }
        if (strlen($password) < 5) {
            $password_error = "Password must be minimum of 5 characters";
            $validate = 0;
        }
        if ($password != $cpassword) {
            $cpassword_error = "Password and Confirm Password doesn't match";
            $validate = 0;
        }

        if ($username && $password && $validate) {
            // create socket
            $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

            // connect to server
            $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

            // send username, password to server
            $msg = RequestCode::REGISTER . "|" . $username . "|" . md5($password);

            $ret = socket_write($socket, $msg, strlen($msg));
            if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

            // receive response from server
            $response = socket_read($socket, 1024);
            if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");
            echo $response;

            // split response from server
            $response = explode("|", $response);

            if ($response[0] == ResponseCode::REGISTER_SUCCESS) {
                $_SESSION["username"] = $username;
                $_SESSION['permission'] = $response[1];
                echo "<script>alert('Register success!');</script>";
                echo "<script>window.location.href = 'index.php';</script>";
            }else
            {
                echo "<script>alert('" . $response[1] . "');</script>";
                echo "<script>window.location.href = 'register.php';</script>";
            }

            // close socket
            socket_close($socket);
        }
    }

    ?>
</head>

<body>
    <?php include('navbar.php'); ?>
    <section class="page-section" id="register">
        <div class="container">
            <h2 class="page-section-heading text-uppercase text-secondary mt-5 mb-0 text-center">ĐĂNG KÝ TÀI KHOẢN</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <form action="register.php" method="post">
                        <div class="form-group ">
                            <div class=" col-lg-12 mt-2">
                                <label class="col-form-label-lg text-right col-lg-4" style="color: #6c757d; font-size: 1.1rem "> Tên đăng nhập </label>
                                <input type="text" class="form-control py-2" id="username" name="username" required />
                                <span class="text-danger"><?php if (isset($username_error)) echo $username_error; ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" col-lg-12 mt-2">
                                <label class="col-form-label-lg text-right col-lg-4" style="color: #6c757d; font-size: 1.1rem "> Mật khẩu </label>
                                <input type="password" class="form-control py-2" id="password" name="password" required />
                                <span class="text-danger"><?php if (isset($password_error)) echo $password_error; ?></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" col-lg-12 mt-2">
                                <label class="col-form-label-lg text-right col-lg-4" style="color: #6c757d; font-size: 1.1rem "> Nhập lại mật khẩu </label>
                                <input type="password" class="form-control py-2" id="cpassword" name="cpassword" required />
                                <span class="text-danger"><?php if (isset($cpassword_error)) echo $password_error; ?></span>
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class=" col-lg-12 mt-2">
                                <a href="login.php" class="link-register mt-3 text-center">Bạn đã có tài khoản ? Đăng nhập ngay</a>
                            </div>
                        </div>
                        <br>
                        <input type="submit" class="form-control button btn btn-primary btn-lg mt-3" name="signup" value="ĐĂNG KÝ" style="background-color: #092745;">
                    </form>
                </div>
            </div>
        </div>
    </section>
</body>


</html>
