<?php

// If param not exist
if (!isset($_SERVER['PATH_INFO'])) {
    exit;
}

// JSON Response
function respond($arr)
{
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

// Init controller
$uri = $_SERVER["PATH_INFO"];
$uri = trim($uri, "/");
$uri = explode("/", $uri);

require_once("api/" . $uri[0] . ".php");

// Set Controller
$controller = new $uri[0];
unset($uri[0]);

$method = "index";

if (isset($uri[1])) {
    $method = $uri[1];
    unset($uri[1]);
}

if (!method_exists($controller, $method)) {
    return respond([
        "code" => 404,
        "status" => "error",
        "message" => "Method not found"
    ]);
}

// Get All Values
$params = array_values($uri);

// Run
call_user_func_array([$controller, $method], $params);