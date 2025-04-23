<?php

require_once(__DIR__ . "/../vendor/autoload.php");

use Curl\Curl;

class SMS {

    protected $local_address = "192.168.7.39:8080";
    protected $curl, $db;
    protected $config = [];

    protected $configTable = "config_sms";

    function __construct()
    {
        if ($this->init_db()) {
            $this->get_config();
            if (count($this->config) > 0) {
                $this->curl = new Curl();
                $this->curl->setBasicAuthentication($this->config['user_name'], $this->config['user_password']);
            }
        }
    }

    private function init_db() {
        
        $this->db = new SQLite3(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'a03.db', SQLITE3_OPEN_READWRITE);
        
        if (!$this->db) {
            return false;
        } else {
            return true;
        }
    }

    private function get_config()
    {
        $configData = [];
        $results = $this->db->query("SELECT * FROM config_sms");
            
        while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
            $configData[] = $row;
        }

        if (count($configData) > 0) {
            $this->config = $configData[0];
            return $this->config;
        }

    }

    public function config()
    {

        $requestMethod = $_SERVER["REQUEST_METHOD"];
        
        // Get Data Config
        if ($requestMethod == "GET") {
            return respond([
                'code' => 200,
                'status' => 'success',
                'data' => $this->config
            ]);
        }

        // Set Data Config
        if ($requestMethod == "POST") {

            if (!$this->db) {
                return respond([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Gagal terhubung dengan database'
                ]);
            } else {

                $this->get_config();

                $request = $_POST;
                // var_dump($request);
                // exit;

                $uip = $request['local_address'];
                $uname = $request['user_name'];
                $upass = $request['user_password'];

                $query = "INSERT INTO $this->configTable VALUES ('$uip', '$uname', '$upass')";

                if (count($this->config) > 0) {
                    $query = "UPDATE $this->configTable SET local_address='$uip', user_name='$uname', user_password='$upass'";
                }

                $this->db->exec($query);

                if ($this->db->changes() > 0) {
                    return respond([
                        'code' => 200,
                        'status' => 'success',
                        'message' => 'Berhasil update config sms'
                    ]);
                }
                
                return respond([
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Gagal update config sms'
                ]);
            }
        }
        
    }

    public function send()
    {

        // var_dump($_POST);
        // exit;
        
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            return respond([
                'code' => 404,
                'status' => 'error',
                'message' => 'Method not allowed'
            ]);
        }

        // $jsonData = file_get_contents('php://input');
        // $data = json_decode($jsonData, true);

        $data = $_POST;
        

        if (is_null($data)) {
            return respond([
                'code' => 501,
                'status' => 'error',
                'message' => 'JSON not valid'
            ]);

        }

        // var_dump($data);
        // exit;

        $this->curl->setHeader('Content-Type', 'application/json');

        $this->curl->post($this->config['local_address'] . '/message', [
            'phoneNumbers' => [$data['phone']],
            'message' => $data['message']
        ]);

        if ($this->curl->error) {
            return respond([
                "code" => 500,
                "status" => "error",
                "message" => "Gagal kirim ke gateway"
            ]);
        } else {
            return respond([
                "code" => 200,
                "status" => "success",
                "message" => "Berhasil kirim ke gateway",
                "data" => [
                    "id" => $this->curl->response->id
                ]
            ]);
        }
        
    }

    public function index()
    {
        
        $this->curl->get($this->config['local_address']);

        if ($this->curl->error) {
            return respond([
                "code" => 500,
                "status" => "error",
                "message" => "Error when connecting to phone"
            ]);
        } else {
            return respond([
                "code" => 200,
                "status" => "success",
                "data" => $this->curl->response
            ]);
        }
    }

    public function status($id = null)
    {

        if ($_SERVER['REQUEST_METHOD'] == "GET") {
            if (is_null($id)) {
                return respond([
                    "code" => 501,
                    "status" => "error",
                    "message" => "ID Pesan tidak boleh kosong"
                ]);
            }
        } else {
            return respond([
                "code" => 404,
                "status" => "error",
                "message" => "Not found"
            ]);
        }

        $this->curl->get($this->config['local_address'] . '/message/' . $id);

        if ($this->curl->error) {
            echo 'Error: ' . $this->curl->errorMessage . "\n";
        } else {
            
            $result = $this->curl->response->recipients[0];
            $state = strtolower($result->state);
            $errorMsg = "Error saat mengirim pesan";

            // var_dump($this->curl->response);

            // var_dump($result);
            // exit;

            switch ($state) {

                case 'failed':
                
                    switch ($result->error) {
                        case 'Send result: RESULT_ERROR_GENERIC_FAILURE':
                            $errorMsg = "Silahkan cek pulsa atau kartu sim";
                            break;
                        case 'Send result: RESULT_NO_DEFAULT_SMS_APP':
                            $errorMsg = "Cek pengaturan tentukan SIM untuk mengirim pesan";
                            break;
                            case 'Sending: Invalid phone number':
                                $errorMsg = "Nomor telepon tidak sesuai";
                            break;
                        case 'Sending: Invalid phone number type':
                            $errorMsg = "Nomor penerima tidak valid";
                            break;
                    }

                    return respond([
                        "code" => 500,
                        "status" => "error",
                        "data" => [
                            "state" => $state,
                            "message" => $errorMsg
                        ]
                    ]);
                    break;

                default:
                    return respond([
                        "code" => 200,
                        "status" => "success",
                        "data" => [
                            "state" => $state
                        ]
                    ]);
                    break;
            }
        }

    }

}