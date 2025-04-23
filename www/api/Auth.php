<?php

require_once(__DIR__ . "/../vendor/autoload.php");

use Curl\Curl;

class Auth {

    public function login()
    {
        // echo "/api/auth/login";

        $request = $_POST;
        $requestKey = ['email', 'password'];

        if (count($request) < 1) {
            respond([
                'error' => 'Data empty'
            ]);
        }

        // Validation

        foreach ($requestKey as $key) {
            if (!array_key_exists($key, $request)) {
                respond([
                    'error' => $key . ' tidak valid'
                ]);
            }

            if (strlen($request[$key]) < 1) {
                respond([
                    'error' => $key . ' tidak boleh kosong'
                ]);
            }
        }

        $curl = new Curl("http://localhost:8080/");
        $curl->post("login", $request);
        $result = $curl->getResponse();

        var_dump($result->code);

        // var_dump($request);

        setcookie("authtoken", $result->token, time() + 60 * 5, "/", "", false, true);
        var_dump($_COOKIE);

        // if (!isset($_COOKIE['authtoken'])) {
        //     echo("FUCK YOU BELUM LOGIN!");
        //     echo "<script>setTimeout(function(){window.location.href='login.php'}, 1000)</script>";
        // }

        // $curl->post("");
        // $curl = new Curl();

        exit;
    }

}