<?php
include 'integrations/intense/IntenseAPI.php';

class Intense extends Api {

    private $connect;

    function __construct($merchant) {
        $api = new IntenseAPI();
        $api->setCredentials($merchant->APIuser, $merchant->APIpass, $merchant->APIurl);

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

            $result = $this->connect->getLeads(['from' => $FromDate, 'to' => $ToDate]);

            $array = [];

            if (!empty($result->data)) {
                foreach ($result->data as $item) {

                    if (empty($item->UserID)) {
                        continue;
                    }

                    $btag = $this->getBTag($item->cTag);

                    $array[] = [
                        'email' => $item->Email,
                        'phone' => '',
                        'trader_id' => $item->UserID,
                        'created_date' => date('Y-m-d H:i:s'),
                        'client_status' => 'real',
                        'sale_status' => 'real',
                        'first_name' => $item->FirstName,
                        'last_name' => $item->LastName,
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
     * @param array $param
     * @return array
     * @throws \Exception
     */
    function getTransactions($param)
    {

        try {

            if (empty($param['FromDate'])) {
                throw new \Exception('FromDate is required!');
            }

            if (empty($param['ToDate'])) {
                throw new \Exception('ToDate is required!');
            }

            $FromDate = $param['FromDate'];
            $ToDate = $param['ToDate'];

            $result = $this->connect->getTransactions(['from' => $FromDate, 'to' => $ToDate]);

            $array = [];

            if (!empty($result->data)) {
                foreach ($result->data as $item) {

                    $isVolume = false;

                    if($item->ActionName == "bet" and $item->Amount > 0){
                        $isVolume = true;
                        $item->ActionName = "PNL";
                        $item->Amount = $item->Amount*(-1);
                    }

                    if($item->ActionName == "win" and $item->Amount > 0){
                        $item->ActionName = "PNL";
                        $item->Amount = $item->Amount;
                    }

                    if (in_array($item->ActionName, ['deposit','positions','revenue','bonus','withdrawal','volume','chargeback','PNL'])){
                        
                        $array[] = [
                            'trader_id' => $item->UserID,
                            'created_time' => date('Y-m-d H:i:s', strtotime($item->TransactionDate)),
                            'transaction_id' => $item->TransactionID,
                            'transaction_type' => $item->ActionName,
                            'amount' => $item->Amount,
                            'currency' => $item->Currency,
                            'transaction_approval' => 'approved',
                            'btag' => '',
                        ];

                        if($isVolume){
                            $array[] = [
                                'trader_id' => $item->UserID,
                                'created_time' => date('Y-m-d H:i:s', strtotime($item->TransactionDate)),
                                'transaction_id' => 'v'.$item->TransactionID,
                                'transaction_type' => 'volume',
                                'amount' => abs($item->Amount),
                                'currency' => $item->Currency,
                                'transaction_approval' => 'approved',
                                'btag' => '',
                            ];
                        }

                    }

                }
            }


            return $array;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

}
