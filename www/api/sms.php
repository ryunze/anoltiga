<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once(__DIR__ . "/../vendor/autoload.php");

use Curl\Curl;

class SMS_API {

    protected $local_address = "192.168.219.211:8080";
    protected $curl;
    protected $config = [
        'user_name' => 'sms',
        'user_password' => '_3PCFY52'
    ];
    
    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setBasicAuthentication($this->config['user_name'], $this->config['user_password']);
    }

    public function getStatusGateway()
    {

        $this->curl->setTimeout(1);

        $db = new SQLite3(__DIR__ . '/a03.db');

        if (!$db) {
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal terhubung ke database'
            ]);
            exit;
        }

        $query = "SELECT * FROM config_sms";
        $result = $db->query($query);
        $result = $result->fetchArray(SQLITE3_ASSOC);

        
        $ip = 'http://' . $result['local_address'];

        $this->curl->get($ip);

        if ($this->curl->error) {
            // echo 'Error: ' . $this->curl->errorMessage . "\n";
            // $this->curl->diagnose();
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal terhubung ke ponsel'
            ]);
            exit;
        } else {
            // echo json_encode($this->curl->response);
            echo json_encode([
                'status' => 200,
                'message' => 'Berhasil terhubung ke ponsel'
            ]);
            exit;
        }
    }

    public function sendMessage($data)
    {
        $db = new SQLite3(__DIR__ . '/a03.db');

        if (!$db) {
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal terhubung ke database'
            ]);
            exit;
        }

        $query = "SELECT * FROM config_sms";
        $result = $db->query($query);
        $result = $result->fetchArray(SQLITE3_ASSOC);

        
        $ip = 'http://' . $result['local_address'];

        $this->curl->setTimeout(1);
        $this->curl->setHeader('Content-Type', 'application/json');
        
        $this->curl->post($ip . '/message', [
            'phoneNumbers' => [$data['phoneNumber']],
            'message' => $data['message']
        ]);

        if ($this->curl->error) {
            echo json_encode([
                "code" => 500,
                "status" => "error",
                "message" => $this->curl->errorMessage
            ]);
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

    public function getConfig()
    {
        $db = new SQLite3(__DIR__ . '/a03.db');

        if (!$db) {
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal terhubung ke database'
            ]);
            exit;
        }

        $query = "SELECT * FROM config_sms";
        $result = $db->query($query);

        echo json_encode([
            'status' => 201,
            'data' => $result->fetchArray(SQLITE3_ASSOC) 
        ]);

    }

    public function saveConfig($data)
    {
        // var_dump($data);

        $db = new SQLite3(__DIR__ . '/a03.db');

        if (!$db) {
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal terhubung ke database'
            ]);
            exit;
        }

        $query = "UPDATE config_sms SET local_address = '" . $data['localAddress'] . "', user_name = '" . $data['username'] . "', user_password = '" . $data['password'] . "'";

        if (!$db->exec($query)) {
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal update config'
            ]);
            exit;
        }

        echo json_encode([
            'status' => 200,
            'message' => 'Berhasil update config'
        ]);
        exit;
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
        case 'config':
            $api->getConfig();
        break;
        case 'save-config':
            $data = file_get_contents('php://input');
            $data = json_decode($data, true);
            $api->saveConfig($data);
        break;
    }
}