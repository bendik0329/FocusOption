<?php

Class AirsoftAPI {

    private $apiPassword = '';
    private $apiUrl = '';
    public $errors = [];
    public $requests = [];

    /**
     * Set Credentials
     */
    public function setCredentials($password, $url) {

        $this->apiPassword = $password;
        $this->apiUrl = $url.'/back.php/affiliate/externalSorce/api';
    }
    
    public function sendRequest($method = 'GET', $params) {
        $curl = curl_init();

        $header = [
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ];
        
        $params['key'] = $this->apiPassword;
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->apiUrl . $action . "?" . http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => $header,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        $this->requests[] = curl_getinfo($curl);
        
        curl_close($curl);

        if ($err) {
            $this->errors[] = "cURL Error #:" . $err;
            return false;
        } else {
            return json_decode($response);
        }
    }
    
}
