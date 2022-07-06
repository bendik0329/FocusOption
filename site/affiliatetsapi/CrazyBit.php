<?php
include 'integrations/crazybit/CrazyBitAPI.php';

class CrazyBit extends Api {

    private $connect;

    function __construct($merchant)
    {
        $api = new CrazyBitAPI();
        $api->setCredentials($merchant->apiToken, $merchant->APIurl);

        $this->connect = $api;
    }

    function createLead($email,$firstName,$lastName,$phone,$language,$country,$btag, $additioanal = null)
    {
        return false;
    }
    
    function createCustomer($email,$password,$firstName,$lastName,$currency,$accountType,$phone,$language,$country,$btag, $additioanal = null)
    {
        try {

            $result = $this->connect->createCustomer([
                'email' => trim($email),
                'first_name' => trim($firstName),
                'last_name' => trim($lastName),
                'name' => trim($firstName).' '.trim($lastName),
                'phone' => trim($phone),
                'status' => trim($accountType),
                'country' => trim($country),
                'btag' => trim($btag)
            ]);

            if($result->msg == 'success'){
                return $result->id;
            }else{
                throw new \Exception($result);
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
