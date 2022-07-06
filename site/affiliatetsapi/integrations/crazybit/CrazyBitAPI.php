<?php

Class CrazyBitAPI {

    private $apiToken = '';
    private $apiUrl = '';
    public $errors = [];
    public $requests = [];

    /**
     * Get token
     */
    public function setCredentials($token, $url) {

        $this->apiToken = $token;
        $this->apiUrl = $url;
    }
    
    private function getToken()
    {
        return $this->apiToken;
    }

    public function createLead($data) {

        return false;

    }

    public function getLeads($data) {

        return false;

    }

    public function createCustomer($data) {
        
        $data['_token'] =  $this->getToken();

        return $this->sendRequest('POST', '/ats/leads', $data);
        
    }
    
    private function sendRequest($method = 'GET', $action, $params) {
        $curl = curl_init();

       $header = [
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ];
        
        if($params['Authorization']){
            $header[] = 'Authorization: Bearer '.$params['Authorization'];
        }
        
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
