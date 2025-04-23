<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

header('Content-Type: application/json');

require_once(__DIR__ . "/../vendor/autoload.php");

use Curl\Curl;

class SMS_API {

    protected $local_address = "192.168.7.39:8080";
    protected $curl;
    protected $config = [
        'user_name' => 'sms',
        'user_password' => '_3PCFY52'
    ];

    protected $configTable = "config_sms";
    
    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setBasicAuthentication($this->config['user_name'], $this->config['user_password']);
    }

    public function getStatusGateway()
    {
        $this->curl->get('192.168.25.169:8080');
        if ($this->curl->error) {
            echo 'Error: ' . $this->curl->errorMessage . "\n";
            $this->curl->diagnose();
        } else {
            echo json_encode($this->curl->response);
        }
    }

}

$api = new SMS_API();

if (!isset($_GET['r'])) {
    $api->getStatusGateway();
}