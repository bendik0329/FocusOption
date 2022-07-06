<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MgflexAPI
 *
 * @author Anton
 */
class MgflexAPI
{

    private $_username = '';
    private $_token = '';
    private $_url = '';
    public $errors = [];
    public $requests = [];

    /**
     * @param $token
     * @param $url
     */
    public function setCredentials($username, $token, $url) {

        $this->_username = $username;
        $this->_token = $token;
        $this->_url = $url;
    }

    private function sendRequest($method = 'GET', $action, $params)
    {
        $curl = curl_init();

        $header = [
            "Cache-Control: no-cache",
            "Content-Type: application/json",
            "Authorization: Bearer ".$this->_token,
        ];

        $params['affilatePlatformId'] = $this->_username;

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

    public function getLeads($data)
    {
        return $this->sendRequest('POST', '/Affilates/GetAllLeads', $data);
    }

    public function getFTDByAffiliateId($data)
    {
        return $this->sendRequest('POST', '/Affilates/GetFtdsByAffliateId', $data);
    }

    public function getFTDForDateRange($data)
    {
        return $this->sendRequest('POST', '/Affilates/GetFtdsBetween', $data);
    }

    public function getTraders($data)
    {
        return $this->getLeads($data);
    }
}
