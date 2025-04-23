<?php

function route_view() {
    
    $defaultView = 'sms-sender';
    
    if (isset($_GET['route'])) {
        $defaultView = $_GET['route'];
    }

    include_once('views/' . $defaultView . '.php');
}

function active_page($page = null)
{
    $route =  $_GET['route'];
    if (is_null($page)) {
        return $route;
    }

    if ($page != $route) {
        return false;
    }

    return true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>A03</title>

    <!-- BS 4 -->
    <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">

    <!-- Toastify -->
    <link rel="stylesheet" href="/assets/toastify/toastify.min.css">
    <script src="/assets/toastify/toastify.js"></script>

    <!-- Quantum Alert -->
    <link rel="stylesheet" href="/assets/quantumalert/quantumalert.css">
    <script src="/assets/quantumalert/quantumalert.js" charset="utf-8"></script>


    <!-- Jquery -->
    <script src="/assets/jquery/jquery.js"></script>

    <!-- My CSS -->
    <link rel="stylesheet" href="/assets/css/loading.css">
    <link rel="stylesheet" href="/assets/css/style.css">

</head>

<body>

    <?php include_once('partials/sidebar.php'); ?>

    <section id="app">

        <style>
            .ldBar path.mainline {
                stroke-width: 10;
                stroke: #09f;
                stroke-linecap: round;
            }

            .ldBar path.baseline {
                stroke-width: 14;
                stroke: #f1f2f3;
                stroke-linecap: round;
                filter: url(#custom-shadow);
            }
        </style>

        <?php route_view() ?>

    </section>

    <script>
        function toast(msg, color = "success") {
            const bg = {
                error: "linear-gradient(90deg, rgba(108,10,10,1) 16%, rgba(213,14,14,1) 100%)",
                success: "teal"
            }
            Toastify({
                text: msg,
                duration: 2500,
                newWindow: true,
                close: true,
                gravity: "top",
                position: "center",
                style: {
                    background: bg[color],
                }
            }).showToast();
        }
    </script>

    <script src="/assets/js/main.js"></script>
    <script src="/assets/bootstrap/js/bootstrap.min.js"></script>

    <!-- <script src="/router.js"></script> -->

</body>

</html>