<?php

Class OctagonAPI {

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

    public function createLead($data) {
        return self::sendRequest('POST', 'create-lead', $data);
    }

    public function getLeads($data) {

        return self::sendRequest('POST', 'get-leads-by-date', $data);
    }
    
    public function getLead($data) {

        return self::sendRequest('POST', 'get-lead-info', $data);
    }

    public function getDeposits($data) {

        return self::sendRequest('POST', 'get-deposit-by-date', $data);
    }

    public function getWithdrawals($data) {

        return self::sendRequest('POST', 'get-withdrawals-by-date', $data);
    }

    public function getPnlAndValues($data) {

        return self::sendRequest('POST', 'get-pnl-and-values', $data);

    }

    public function createCustomer($data) {

        return self::createLead($data);
    }
    
    public function getAutologin($data) {
        return self::sendRequest('POST', 'get-lead-info', $data);
    }

    private function sendRequest($method = 'GET', $action, $params) {
        $curl = curl_init();

        $url = $this->apiUrl . '/api/' . $this->apiToken . '/' . $action . '/?'. http_build_query($params);
        
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
        ));

        $response = curl_exec($curl);

        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        $err = curl_error($curl);

        $this->requests[] = curl_getinfo($curl);

        curl_close($curl);

        if ($err || $httpcode != 200) {
            $this->errors[] = "cURL Error #:" . $err .'( HTTP code: '.$httpcode.')';
            return false;
        } else {

            return json_decode($response);

        }
    }

}
