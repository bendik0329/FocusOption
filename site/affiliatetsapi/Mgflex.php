<?php
include 'integrations/mgflex/MgflexAPI.php';

class Mgflex extends Api {

    private $connect;

    function __construct($merchant) {
        $api = new MgflexAPI();
        $api->setCredentials($merchant->APIuser, $merchant->api_token2, $merchant->APIurl);

        $this->connect = $api;
    }

    function createLead($email,$firstName,$lastName,$phone,$language,$country,$btag, $additioanal = null) {
        try {
            
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    function createCustomer($email,$password,$firstName,$lastName,$currency,$accountType,$phone,$language,$country,$btag, $additioanal = null) {
        try {

            
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }
    
    function getAutologin($param) {
        return false;
    }

    /**
     *
     * @param array $param
     * @return array
     * @throws \Exception
     */
    function getLeads($param) {
        try {

            if (empty($param['FromDate'])) {
                throw new \Exception('fromDate is required!');
            }

            if (empty($param['ToDate'])) {
                throw new \Exception('toDate is required!');
            }

            $FromDate = $param['FromDate'];
            $ToDate = $param['ToDate'];

            $result = $this->connect->getLeads(['fromDate' => $FromDate, 'toDate' => $ToDate]);

            $array = [];

            if (!empty($result)) {
                foreach ($result as $item) {

                    if (empty($item->clientId)) {
                        continue;
                    }

                    $btag = $this->getBTag($item->affiliateId);

                    $array[] = [
                        'email' => '',
                        'phone' => '',
                        'trader_id' => $item->clientId,
                        'created_date' => date('Y-m-d H:i:s'),
                        'client_status' => 'real',
                        'sale_status' => 'real',
                        'first_name' => $item->firstName,
                        'last_name' => $item->lastName,
                        'campaign' => '',
                        'btag' => $btag,
                        'country' => '',
                    ];
                }
            }


            return $array;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    /**
     *
     * @param string $tag
     * @return string
     */
    private function getBTag($tag) {

     /*   $array = [
            'btag' => '',
            'trader_id' => '',
        ];

        if (!empty($client_id)) {
            list($array['btag'], $array['trader_id']) = explode('CRMA', $client_id, 2);
        }

        $array['btag'] = str_replace('btag=','',$array['btag']);

        return $array;*/

        return $tag;
    }

    /**
     *
     * @param array $param
     * @return array
     * @throws \Exception
     */
    function getFTD($param)
    {
        try {
            if (empty($param['FromDate'])) {
                throw new \Exception('FromDate is required!');
            }

            if (empty($param['ToDate'])) {
                throw new \Exception('ToDate is required!');
            }

            if (empty($param['affilateId'])) {
                throw new \Exception('affilateId is required!');
            }

            $FromDate = $param['FromDate'];
            $ToDate = $param['ToDate'];
            $affilateId = $param['affilateId'];

            $result = $this->connect->getFTDForDateRange(['affilateId' => $affilateId, 'fromDate' => $FromDate, 'toDate' => $ToDate]);
            return $result;
        } catch (Exception $e) {
            die($e->getMessage());
        }
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
