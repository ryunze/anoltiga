<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once(__DIR__ . "/../vendor/autoload.php");

use Curl\Curl;

class SMS_API {

    protected $local_address = "192.168.219.211:8080";
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
        $this->curl->get($this->local_address);
        if ($this->curl->error) {
            echo 'Error: ' . $this->curl->errorMessage . "\n";
            $this->curl->diagnose();
        } else {
            echo json_encode($this->curl->response);
        }
    }

    public function sendMessage($data)
    {
        $this->curl->setHeader('Content-Type', 'application/json');
        
        $this->curl->post($this->local_address . '/message', [
            'phoneNumbers' => [$data['phoneNumber']],
            'message' => $data['message']
        ]);

        if ($this->curl->error) {

            var_dump($this->curl->errorMessage);
            // echo json_encode([
            //     "code" => 500,
            //     "status" => "error",
            //     "message" => "Gagal kirim ke gateway"
            // ]);
            exit;
        } else {
            echo json_encode([
                "code" => 200,
                "status" => "success",
                "message" => "Berhasil kirim ke gateway",
                "data" => [
                    "id" => $this->curl->response->id
                ]
            ]);
            exit;
        }

    }

}

$api = new SMS_API();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

// header('Content-Type: application/json');

if (!isset($_GET['r'])) {
    $api->getStatusGateway();
} else {
    $route = $_GET['r'];
    switch ($route) {
        case 'send':
            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            $api->sendMessage($data);
        break;
    }
}