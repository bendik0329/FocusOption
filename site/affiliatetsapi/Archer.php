<?php

include 'integrations/archer/ArcherAPI.php';

class Archer extends Api {

    private $connect;
    private $merchant;

    function __construct($merchant) {
        $archer = new ArcherAPI();
        $archer->setCredentials($merchant->APIuser, $merchant->APIpass, $merchant->APIurl);
        
        $this->merchant = $merchant;
        $this->connect = $archer;
    }

    function createLead($email, $firstName, $lastName, $phone, $language, $country, $btag, $additioanal = null) {
        return 'Lead created in Archer';
    }

    function createCustomer($email, $password, $firstName, $lastName, $currency, $accountType, $phone, $language, $country, $btag, $additioanal = null) {
        try {
            
            $additional_params = $this->merchant->api_token2;
            
            $PartnerCode = '';
            $WalletGroupType = '';
            $BranchCode = '';
            
            if(!empty($additional_params)){
                list($PartnerCode, $WalletGroupType, $BranchCode) = explode('|',$additional_params);
            }
            
            $result = $this->connect->setLiveRegistration([
                'FirstName' => trim($firstName),
                'LastName' => trim($lastName),
                'Email' => trim($email),
                'Password' => trim($password),
                'PhoneNumber' => trim($phone),
                'CountryCodeIso2' => trim($country),
                'CurrencyCode' => trim($currency),
                'LanguageCode' => 'EN',//trim($country),
                'AFCode' => trim($btag),
                'PartnerCode' => $PartnerCode,
                'WalletGroupType' => $WalletGroupType,
                'BranchCode' => $BranchCode
            ]);

            if ($result->CodeMessage === 0 || ( !empty($result->ErrorMessage) && strlen($result->ErrorMessage)<=1 )) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

    function getAutologin($param) {
        return $this->connect->getAutologin($param);
    }

    /**
     * 
     * @param string $client_id
     * @return array
     */
    private function separateClientId($client_id) {

        $array = [
            'btag' => '',
            'trader_id' => '',
        ];

        if (!empty($client_id)) {
            list($array['btag'], $array['trader_id']) = explode('-CRMA', $client_id, 2);
        }

        $array['btag'] = str_replace('btag=','',$array['btag']);

        return $array;
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
                throw new \Exception('FromDate is required!');
            }

            if (empty($param['ToDate'])) {
                throw new \Exception('ToDate is required!');
            }

            if (empty($param['ToDStatusate'])) {
                $param['Status'] = 3;
            }

            $FromDate = $param['FromDate'];
            $ToDate = $param['ToDate'];
            $Status = $param['Status'];

            $result = $this->connect->getLeads(['Status' => $Status, 'FromDate' => $FromDate, 'ToDate' => $ToDate]);

            $array = [];

            if (!empty($result->LeadsPartnerData)) {
                foreach ($result->LeadsPartnerData as $item) {

                    $separateClientId = $this->separateClientId($item->ClientId);

                    list($first_name, $last_name) = explode(' ', $item->FullName, 2);

                    $array[] = [
                        'email' => '',
                        'phone' => '',
                        'trader_id' => substr($separateClientId['trader_id'], 0, 29),
                        'created_date' => date('Y-m-d', strtotime($item->CreatedDate)),
                        'client_status' => 'real',
                        'sale_status' => 'real',
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'campaign' => '',
                        'btag' => $separateClientId['btag'],
                        'country' => $item->Country,
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
        try {
            if (empty($param['FromDate'])) {
                throw new \Exception('FromDate is required!');
            }

            if (empty($param['ToDate'])) {
                throw new \Exception('ToDate is required!');
            }

            if (empty($param['CurrencyCode'])) {
                throw new \Exception('CurrencyCode is required!');
            }

            $FromDate = $param['FromDate'];
            $ToDate = $param['ToDate'];
            $CurrencyCode = $param['CurrencyCode'];

            $result = $this->connect->getFTD(['CurrencyCode' => $param['CurrencyCode'], 'FromDate' => $FromDate, 'ToDate' => $ToDate]);
            return $result->FTDsPartnerData;
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

        try {
            if (empty($param['FromDate'])) {
                throw new \Exception('FromDate is required!');
            }

            if (empty($param['ToDate'])) {
                throw new \Exception('ToDate is required!');
            }

            if (empty($param['CurrencyCode'])) {
                $param['CurrencyCode'] = 'USD';
                //throw new \Exception('CurrencyCode is required!');
            }

            $FromDate = $param['FromDate'];
            $ToDate = $param['ToDate'];
            $CurrencyCode = $param['CurrencyCode'];

            $result = $this->connect->getWalletsTradedSummary(['CurrencyCode' => $param['CurrencyCode'], 'FromDate' => $FromDate, 'ToDate' => $ToDate]);

            if (!empty($result->WalletsTradedSummaryData)) {
                foreach ($result->WalletsTradedSummaryData as $item) {
                    $separateClientId = $this->separateClientId($item->ClientId);

                    $array[] = [
                        'trader_id' => substr($separateClientId['trader_id'], 0, 29),
                        'created_time' => date('Y-m-d H:i:s'),
                        'transaction_id' => substr($separateClientId['trader_id'], 0, 5) . time(),
                        'amount' => $item->SumNetPL,
                        'currency' => $CurrencyCode,
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
    function getTransactions($param) {

        try {

            if (empty($param['FromDate'])) {
                throw new \Exception('FromDate is required!');
            }

            if (empty($param['ToDate'])) {
                throw new \Exception('ToDate is required!');
            }

            if (empty($param['CurrencyCode'])) {
                $param['CurrencyCode'] = 'USD';
                //throw new \Exception('CurrencyCode is required!');
            }

            $FromDate = $param['FromDate'];
            $ToDate = $param['ToDate'];
            $CurrencyCode = $param['CurrencyCode'];

            $result = $this->connect->getTransactions(['CurrencyCode' => $param['CurrencyCode'], 'FromDate' => $FromDate, 'ToDate' => $ToDate]);

            $ftd = $this->connect->getFTD(['CurrencyCode' => $param['CurrencyCode'], 'FromDate' => $FromDate, 'ToDate' => $ToDate]);

            $array = [];
            $array_ftd = [];

            if (!empty($ftd->FTDsPartnerData)) {
                foreach ($ftd->FTDsPartnerData as $item) {
                    $separateClientId = $this->separateClientId($item->ClientId);
                    $array_ftd[$separateClientId['trader_id']] = $item->FTDAmount;
                }
            }

            if (!empty($result->TransactionsPartnerData)) {
                foreach ($result->TransactionsPartnerData as $item) {
                    $separateClientId = $this->separateClientId($item->ClientId);

                    if (!empty($array_ftd[$separateClientId['trader_id']])) {

                        if ($item->DepositContact > $array_ftd[$separateClientId['trader_id']]) {
                            // FTD
                            $array[] = [
                                'trader_id' => substr($separateClientId['trader_id'], 0, 29),
                                'created_time' => date('Y-m-d H:i:s'),
                                'transaction_id' => 'f' . substr($separateClientId['trader_id'], 0, 5) . time(),
                                'transaction_type' => 'deposit',
                                'amount' => $array_ftd[$separateClientId['trader_id']],
                                'currency' => $CurrencyCode,
                                'transaction_approval' => 'approved',
                                'btag' => $separateClientId['btag'],
                            ];
                        }
                    }
                    // Deposit
                    $array[] = [
                        'trader_id' => substr($separateClientId['trader_id'], 0, 29),
                        'created_time' => date('Y-m-d H:i:s'),
                        'transaction_id' => 'd' . substr($separateClientId['trader_id'], 0, 5) . time(),
                        'transaction_type' => 'deposit',
                        'amount' => $item->DepositContact,
                        'currency' => $CurrencyCode,
                        'transaction_approval' => 'approved',
                        'btag' => $separateClientId['btag'],
                    ];

                    // Withdrawal
                    $array[] = [
                        'trader_id' => substr($separateClientId['trader_id'], 0, 29),
                        'created_time' => date('Y-m-d H:i:s'),
                        'transaction_id' => 'w' . substr($separateClientId['trader_id'], 0, 5) . time(),
                        'transaction_type' => 'withdrawal',
                        'amount' => $item->WithdrawalContact,
                        'currency' => $CurrencyCode,
                        'transaction_approval' => 'approved',
                        'btag' => $separateClientId['btag'],
                    ];
                }
            }


            return $array;
        } catch (Exception $e) {
            die($e->getMessage());
        }
    }

}
