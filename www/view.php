<?php

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $view = $_GET['view'];
    $viewPath = __DIR__ . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view . '.html';
    if (file_exists($viewPath)) {
        echo file_get_contents($viewPath);
        exit;
    }
}