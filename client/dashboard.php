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
</head>

<body id="page-top">
    <?php
    require_once 'ProtocolCode/RequestCode.php';
    require_once 'ProtocolCode/ResponseCode.php';
    require_once 'Entity/Result.php';

    use ProtocolCode\RequestCode;
    use ProtocolCode\ResponseCode;

    session_start();

    $socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

    // connect to server
    $result = socket_connect($socket, $_SESSION['host_server'], $_SESSION['port']) or die("socket_connect() failed.\n");

    $msg = RequestCode::DASHBOARD . "|" . "0" . "|" . $_SESSION['view_exam'] . "|";

    $ret = socket_write($socket, $msg, strlen($msg));
    if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

    // receive response from server
    $response_first = socket_read($socket, 1024);
    if (!$response_first) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");

    $response_first = explode("|", $response_first);

    if ($response_first[0] == ResponseCode::DASHBOARD_NUM_ROW) {
        $_SESSION['num_row'] = $response_first[1];
        $_SESSION['result_list'] = array();
    } else {
        echo "<script>alert('Loading fail');</script>";
        echo "<script>window.location.href = 'index.php';</script>";
    }
    for ($i = 1; $i <= $_SESSION['num_row']; $i++) {
        $msg = RequestCode::DASHBOARD . "|" . $response_first[1 + $i] . "|" . $_SESSION['view_exam'] . "|";

        $ret = socket_write($socket, $msg, strlen($msg));
        if (!$ret) die("client write fail:" . socket_strerror(socket_last_error()) . "\n");

        // receive response from server
        $response = socket_read($socket, 1024);
        if (!$response) die("client read fail:" . socket_strerror(socket_last_error()) . "\n");
        //echo $response;

        // split response from server
        $response = explode("|", $response);

        if ($response[0] == ResponseCode::DASHBOARD_INFO) {
            $p = new Result();
            $p->set_name($response[1]);
            $p->set_score(round($response[2] / $_SESSION['total_question'] * 100, 2));
        } else {
            echo "<script>alert('Loading fail');</script>";
            echo "<script>window.location.href = 'index.php';</script>";
        }
        $_SESSION["result_list"][$i] = $p;
    }
    socket_close($socket);
    ?>
    <?php include_once("navbar.php") ?>
    <section class="page-section dashboard">
        <div class="container">
            <div class="card p-5">
                <table class="table align-middle mb-0 bg-white">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col">
                                <h6 class="fw-bold my-3">#</h6>
                            </th>
                            <th scope="col">
                                <h6 class="fw-bold my-3">Tên tài khoản</h6>
                            </th>
                            <th scope="col">
                                <h6 class="fw-bold my-3">Điểm số</h6>
                            </th>
                            <th scope="col">
                                <h6 class="fw-bold my-3">Thời gian làm bài</h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        foreach ($_SESSION["result_list"] as $result) {
                            echo "
                            <tr>
                            <th scope=\"row\">" . $i . "</th>
                                <td>" . $result->get_name() . "</td>
                                <td>" . $result->get_score() . "</td>
                                <td>NULL</td>
                            </tr>";
                            $i += 1;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</body>

</html>