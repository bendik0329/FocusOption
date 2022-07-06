<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class IntenseAPI
{

    private $_username = '';
    private $_password = '';
    private $_url = '';
    public $errors = [];
    public $requests = [];

    /**
     * @param $token
     * @param $url
     */
    public function setCredentials($username, $password, $url) {

        $this->_username = $username;
        $this->_password = $password;
        $this->_url = $url;
    }

    private function sendRequest($method = 'GET', $action, $params)
    {
        $curl = curl_init();

        $header = [
            "Cache-Control: no-cache",
            "Content-Type: application/json"
        ];

        if($params['Authorization']){
            $header[] = 'Authorization: Bearer '.$params['Authorization'];
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_url . $action . "?" . http_build_query($params),
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

    public function getToken()
    {
        $token = false;

        $params = [
            'email' => $this->_username,
            'password' => $this->_password
        ];

        $authorization = $this->sendRequest('GET', '/affts_auth', $params);

        if(!empty($authorization->token)){
            $token = $authorization->token;
        }

        return $token;
    }

    public function getLeads($data)
    {
        $data['Authorization'] =  $this->getToken();
        return $this->sendRequest('GET', '/affts_query/leads', $data);
    }

    public function getTransactions($data)
    {
        $data['Authorization'] =  $this->getToken();
        return $this->sendRequest('GET', '/affts_query/transactions', $data);
    }

    public function getTraders($data)
    {
        return $this->getLeads($data);
    }
}
