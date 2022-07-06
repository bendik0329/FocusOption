<?php

include 'integrations/octagon/OctagonAPI.php';

class Octagon extends Api {

    private $connect;

    function __construct($merchant) {
        $api = new OctagonAPI();
        $api->setCredentials($merchant->APIpass, $merchant->APIurl);

        $this->connect = $api;
    }

    function createLead($email, $firstName, $lastName, $phone, $language, $country, $btag, $additioanal = null) {
        return self::createCustomer($email, '', $firstName, $lastName, '', '', $phone, $language, $country, $btag, $additioanal);
    }

    function createCustomer($email, $password, $firstName, $lastName, $currency, $accountType, $phone, $language, $country, $btag, $additioanal = null) {
        try {

            $result = $this->connect->createCustomer([
                'name' => trim($firstName),
                'lastname' => trim($lastName),
                'email' => trim($email),
                'country' => trim($country),
                'language' => trim($language),
                'ip' => (!empty($additioanal['ip'])) ? $additioanal['ip'] : '',
                'phone' => trim($phone),
                'site' => (!empty($additioanal['site'])) ? $additioanal['site'] : '',
                'source' => (!empty($additioanal['source'])) ? $additioanal['source'] : '',
                'campaign' => (!empty($additioanal['campaign'])) ? $additioanal['campaign'] : '',
                'affiliate' => (!empty($additioanal['affiliate'])) ? $additioanal['affiliate'] : '',
                'btag' => trim($btag),
            ]);

            if (strtolower($result->state) == 'ok') {
                return ['status' => true, 'trader_id' => $result->data->user_id, 'autologin' => (!empty($result->data->autologin))?$result->data->autologin:''];
            } else {
                return ['status' => false, 'errors' => (array) $result->errors];
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function getAutologin($param) {
        $result = $this->connect->getAutologin($param);
        if ($result->state == 'OK') {
            return $result->data->autologin;
        } else {
            return $result->errors;
        }
    }

    /**
     *
     * @param int $param
     * @return type
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

            $result = $this->connect->getLeads(['from' => $FromDate, 'to' => date('Y-m-d', strtotime($ToDate . ' +1 day'))]);

            $array = [];

            if (!empty($result->data)) {
                foreach ($result->data as $item) {

                    if (empty($item->user_id)) {
                        continue;
                    }

                    $name_array = explode(' ', $item->full_name);
                    
                    $array[] = [
                        'email' => $item->email,
                        'phone' => '',
                        'trader_id' => $item->user_id,
                        'created_date' => date('Y-m-d H:i:s', strtotime($item->reg_date)),
                        'client_status' => 'real',
                        'sale_status' => $item->status,
                        'first_name' => (!empty($name_array[0]))?$name_array[0]:'',
                        'last_name' => (!empty($name_array[1]))?$name_array[1]:'',
                        'campaign' => $item->client_uid,
                        'btag' => (!empty($item->btag))?$item->btag:'a500-b1-p',
                        'country' => $item->country,
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

        try {

            if (empty($param['FromDate'])) {
                throw new \Exception('FromDate is required!');
            }

            if (empty($param['ToDate'])) {
                throw new \Exception('ToDate is required!');
            }

            $FromDate = $param['FromDate'];
            $ToDate = $param['ToDate'];

            $deposits = $this->connect->getDeposits(['from' => $FromDate, 'to' => $ToDate]);

            $array = [];

            if (!empty($deposits->data)) {
                foreach ($deposits->data as $item) {
                    $array[] = [
                        'trader_id' => $item->user_id,
                        'created_time' => date('Y-m-d H:i:s', strtotime($item->created)),
                        'transaction_id' => 'D'.date('YmdHis', strtotime($item->created)),
                        'transaction_type' => 'deposit',
                        'amount' => $item->pay_amount,
                        'currency' => $item->pay_currency,
                        'transaction_approval' => 'approved',
                        'btag' => '',
                    ];
                }
            }
            
            $withdrawals = $this->connect->getWithdrawals(['from' => $FromDate, 'to' => $ToDate]);

            if (!empty($withdrawals->data)) {
                foreach ($withdrawals->data as $item) {
                    $array[] = [
                        'trader_id' => $item->user_id,
                        'created_time' => date('Y-m-d H:i:s', strtotime($item->complete_stamp)),
                        'transaction_id' => 'W'.date('YmdHis', strtotime($item->complete_stamp)),
                        'transaction_type' => 'withdrawal',
                        'amount' => $item->amount,
                        'currency' => $item->currency,
                        'transaction_approval' => 'approved',
                        'btag' => '',
                    ];
                }
            }
            
            $pnlAndValues = $this->connect->getPnlAndValues(['from' => $FromDate, 'to' => $FromDate]);

            if (!empty($pnlAndValues->data)) {
                foreach ($pnlAndValues->data as $item) {
                    $array[] = [
                        'trader_id' => $item->user_id,
                        'created_time' => date('Y-m-d H:i:s', strtotime($FromDate)),
                        'transaction_id' => 'P'.$item->user_id.'-'.date('Ymd', strtotime($FromDate)),
                        'transaction_type' => 'PNL',
                        'amount' => $item->pnl,
                        'currency' => 'USD',
                        'transaction_approval' => 'approved',
                        'btag' => '',
                    ];
                    
                    $array[] = [
                        'trader_id' => $item->user_id,
                        'created_time' => date('Y-m-d H:i:s', strtotime($FromDate)),
                        'transaction_id' => 'V'.$item->user_id.'-'.date('Ymd', strtotime($FromDate)),
                        'transaction_type' => 'volume',
                        'amount' => $item->value,
                        'currency' => 'USD',
                        'transaction_approval' => 'approved',
                        'btag' => '',
                    ];
                }
            }


            return $array;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

}
