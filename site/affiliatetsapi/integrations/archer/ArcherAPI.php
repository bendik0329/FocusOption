<?php

Class ArcherAPI {

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
            'username' => $this->apiUsername,
            'password' => $this->apiPassword
        ];
        
        $authorization = $this->sendRequest('POST', '/Login', $params);
        
        if(!empty($authorization->ArcherCtsToken)){
            $token = $authorization->ArcherCtsToken;
        }
        
        return $token;
    }
    
    
    private function sendRequest($method = 'GET', $action, $params) {
        $curl = curl_init();

       $header = [
                "Cache-Control: no-cache",
                "Content-Type: application/json",
            ];
        
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
    
   
    public function getLeads($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/PartnerInfo/GetLeadsPartner/', $data);
        
    }
    
    public function getWalletsTradedSummary($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/PartnerInfo/GetWalletsTradedSummary/', $data);
        
    }
    
    public function getClientInfo($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/ClientInfo/Get/'.$data['ArcherCtsTokenId'].'/GetClientInfo', $data);
        
    }
    
    public function getWalletsInfo($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/ClientInfo/Get/'.$data['ArcherCtsTokenId'].'/WalletsInfo', $data);
        
    }
    
    public function getTrades($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('POST', '/Trades', $data);
        
    }
    
    
    public function getFTD($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/PartnerInfo/GetPartnerFTDs', $data);
        
    }

    public function getTransactions($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/PartnerInfo/GetPartnerTransactions', $data);
        
    }
    
    public function getWithdrawals($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('GET', '/ClientInfo/Get/'.$data['ArcherCtsTokenId'].'/WithdrawalsInfo', $data);
        
    }    
    
    
    public function setLiveRegistration($data) {
        
        //$data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('POST', '/LiveRegistration', $data);
        
    }    
    
    public function setDemoRegistration($data) {
        
        //$data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('POST', '/DemoRegistration', $data);
        
    } 
    
    public function setLandingPageRegistration($data) {
        
        //$data['ArcherCtsTokenId'] =  $this->getToken();
        
        return $this->sendRequest('POST', '/LandingPageRegistration', $data);
        
    } 
    
    public function getAutologin($data) {
        
        $data['ArcherCtsTokenId'] =  $this->getToken();
        return $this->sendRequest('POST', '/Login', $data);
        
    } 
    
    
    
}
