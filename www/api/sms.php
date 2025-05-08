<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once(__DIR__ . "/../vendor/autoload.php");

use Curl\Curl;

class SMS_API {

    protected $curl;
    protected $configs = [];
    protected $db;
    
    public function __construct()
    {
        $db = new SQLite3(__DIR__ . '/a03.db');

        if (!$db) {
            echo json_encode([
                'status' => 500,
                'message' => 'Gagal terhubung ke database'
            ]);
            exit;
        }

        $this->db = $db;

        $query = "SELECT * FROM config_sms";
        $result = $db->query($query);

        $this->configs = $result->fetchArray(SQLITE3_ASSOC);

        // var_dump($this->configs);

        $this->curl = new Curl();
        $this->curl->setBasicAuthentication($this->configs['user_name'], $this->configs['user_password']);
    }

    public function getStatusGateway()
    {
        
        $ip = 'http://' . $this->configs['local_address'];

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
  
        $ip = 'http://' . $this->configs['local_address'];

        $this->curl->setTimeout(1);
        $this->curl->setHeader('Content-Type', 'application/json');
        
        $this->curl->post($ip . '/message', [
            'phoneNumbers' => [$data['phoneNumber']],
            'message' => $data['message'],
            'simNumber' => $this->configs['sim_number']
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
        echo json_encode([
            'status' => 201,
            'data' => $this->configs
        ]);

        exit;
    }

    public function saveConfig($data)
    {

        $query = "UPDATE config_sms SET local_address = '" . $data['localAddress'] . "', user_name = '" . $data['username'] . "', user_password = '" . $data['password'] . "', sim_number = '" . $data['simNumber'] . "'";

        if (!$this->db->exec($query)) {
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