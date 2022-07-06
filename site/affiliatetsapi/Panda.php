<?php
include 'integrations/panda/PandaAPI.php';
class Panda extends Api {

    private $connect;

    function __construct($merchant) {
        $api = new PandaAPI();
        $api->setCredentials($merchant->APIuser, $merchant->api_token2, $merchant->APIurl);

        $this->connect = $api;
    }

    function createLead($email,$firstName,$lastName,$phone,$language,$country,$btag, $additioanal = null) {
        try {

            $result = $this->connect->createLead([
                 'email' => trim($email),
                 'firstName' => trim($firstName),
                 'lastName' => trim($lastName),
                 'phone' => trim($phone),
                 'language' => trim($language),
                 'country' => trim($country),
                 'leadSource' => '',
                 'referral' => ($btag)?'btag='.trim($btag):'',
                 ]);

            if($result->data->status == 'ok'){
                return true;
            }else{
                return false;
            }
            
            
            
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    function createCustomer($email,$password,$firstName,$lastName,$currency,$accountType,$phone,$language,$country,$btag, $additioanal = null) {
        try {

            $result = $this->connect->createCustomer([
                'email' => trim($email),
                'firstName' => trim($firstName),
                'lastName' => trim($lastName),
                'phone' => trim($phone),
                'language' => trim($language),
                'country' => trim($country),
                
                'password' => trim($password),
                'currency' => trim($currency),
                'accountType' => trim($accountType),
                
                'leadSource' => '',
                'referral' => ($btag)?'btag='.trim($btag):'',
                ]);

            if($result->data->status == 'ok'){
                return true;
            }else{
                return false;
            }
            
            
            
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    function getAutologin($param) {
        return false;
    }

    /**
     * 
     * @param int $param
     * @return type
     * @throws \Exception
     */
    function getLeads($param) {
        return false;
    }

    /**
     * 
     * @param type $param
     * @return type
     * @throws \Exception
     */
    function getFTD($param) {
        return false;
    }

    /**
     * 
     * @param type $param
     * @return type
     * @throws \Exception
     */
    function getTrades($param) {
        return false;
    }

    /**
     * 
     * @param type $param
     * @return type
     * @throws \Exception
     */
    function getTransactions($param) {
        return false;
    }

}
