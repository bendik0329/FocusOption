<?php

Class PandaAPI {

    private $apiUsername = '';
    private $apiPassword = '';
    private $apiUrl = '';
    public $errors = [];
    public $requests = [];

    /**
     * Get token
     */
    public function setCredentials($username, $password, $url) {

        $this->apiUsername = $username;
        $this->apiPassword = $password;
        $this->apiUrl = $url;
    }
    
    private function getToken() {
        $token = false;
        
        $time = time();
        
        $params = [
            'partnerId' => $this->apiUsername,
            'time' => $time,
            'accessKey' => sha1($this->apiUsername . time() . $this->apiPassword)
        ];
        
        $authorization = $this->sendRequest('POST', '/api/v3/authorization', $params);
        
        if(!empty($authorization->data->token)){
            $token = $authorization->data->token;
        }
        
        return $token;
    }

    public function createLead($data) {
        
        $data['Authorization'] =  $this->getToken();
        return $this->sendRequest('POST', '/api/v3/leads', $data);
        
    }
    
    public function getLeads($data) {
        
        $data['Authorization'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/api/v3/leads', $data);
        
    }
    
    public function getTransactions($data) {
        
        $data['Authorization'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/api/v3/customers/transactions', $data);
        
    }
    
    public function createCustomer($data) {
        
        $data['Authorization'] =  $this->getToken();
        
        return $this->sendRequest('POST', '/api/v3/customers', $data);
        
    }
    
    public function getCustomers($data) {
        
        $data['Authorization'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/api/v3/customers', $data);
        
    }
    
    public function getCustomer($data) {
        
        if(empty($data['email'])){
            return false;
        }
        
        $data['Authorization'] =  $this->getToken();

        return $this->sendRequest('GET', '/api/v3/customers/'.urlencode($data['email']), $data);
        
    }
    
    public function getCustomerTradingAccounts($data) {
        
        if(empty($data['email'])){
            return false;
        }
        
        $data['Authorization'] =  $this->getToken();

        return $this->sendRequest('GET', '/api/v3/customers/'.urlencode($data['email']).'/tradingAccounts', $data);
        
    }
    
    public function getTradingAccounts($data) {
        
        $data['Authorization'] =  $this->getToken();

        return $this->sendRequest('GET', '/api/v3/customers/tradingAccounts', $data);
        
    }
    
    public function getCustomerTransactions($data) {
        
        if(empty($data['email'])){
            return false;
        }
        
        $data['Authorization'] =  $this->getToken();

        return $this->sendRequest('GET', '/api/v3/customers/'.urlencode($data['email']).'/transactions', $data);
        
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
