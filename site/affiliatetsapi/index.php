<?php

include '../common/database.php';
include 'AffiliateTS.php';

try {
    
    #file_put_contents('./affiliatets_api_debug.log', print_r($_SERVER,1)."\n REQUEST: ".print_r($_REQUEST,1)."\n POST: ".print_r($_POST,1)."\n GET: ".print_r($_GET,1)."\n\n-----------------\n\n", FILE_APPEND | LOCK_EX);
    
    $merchantId = (int) $_REQUEST['merchant_id'];
    if(!empty($_REQUEST['btag'])){
        $affiliateId = (int) mb_substr( $_REQUEST['btag'], 1);
    }else{
        $affiliateId = (int) $_REQUEST['affiliate_id'];
    }
    
    $affiliateKey = $_REQUEST['affiliate_key'];
    $apiToken = $_REQUEST['apiToken'];
    $action = $_REQUEST['action'];


    if (!is_integer($merchantId) AND ! empty($merchantId)) {
        throw new \Exception('Merchant ID must be a integer!');
    }

    if (!is_integer($affiliateId) AND ! empty($affiliateId)) {
        throw new \Exception('Affiliate ID must be a integer!');
    }

    if (empty($action)) {
        throw new \Exception('Action not specified!');
    }

    
    $api = new AffiliateTS($merchantId, $ss->db_hostname, $ss->db_username, $ss->db_password, $ss->db_name);

    if(empty($apiToken)){

        if (empty($affiliateKey)) {
            throw new \Exception('Affiliate key is wrong!');
        }

        $api->setAffiliateCredentials($affiliateId, $affiliateKey);
        if ($api->checkAffiliateCredentials() === false) {
            throw new \Exception('Affiliate credentials is wrong!');
        }

    }else{
        $api->setSystemCredentials($affiliateId, $apiToken);
        if ($api->checkSystemCredentials() === false) {
            throw new \Exception('API Token credentials is wrong!');
        }        
    }

    switch ($action) {
        
        case 'add_event_count': 
            
            
            // Validation
            if(empty($_REQUEST['date'])){
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Date'
                ]);
            }
            
            if(!strtotime($_REQUEST['date'])){
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Date Format'
                ]);
            }
            
            if(empty($_REQUEST['type'])){
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Type'
                ]);
            }
            
            if(empty($_REQUEST['event'])){
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Event'
                ]);
            }
            
            if(empty($_REQUEST['event_quantity'])){
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Event Quantity'
                ]);
            }
            
            $data = [];
            $data['date'] = $_REQUEST['date'];
            $data['type'] = $_REQUEST['type'];
            $data['event'] = strtolower($_REQUEST['event']);
            $data['event_quantity'] = (int)$_REQUEST['event_quantity'];

            if($_REQUEST['type'] == 'update_events_counts'){

                switch($data['event']){
                    case 'click': 
                    case 'view':     
                        $result = $api->internal()->updateEventCount(
                                $merchantId,
                                $affiliateId,
                                $data['date'],
                                $data['event'],
                                $data['event_quantity']
                        );
                        echo json_encode($result);
                        break;
                    default:
                        echo json_encode([
                            'status' => 'error',
                            'message' => 'Invalid Event'
                        ]);
                }
                
            }else{
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Invalid Event Type'
                ]);
            }

            
            break;
        
        case 'create_customer':

            $lead['email'] = $_REQUEST['email'];
            $lead['password'] = $_REQUEST['password'];
            $lead['firstName'] = $_REQUEST['firstName'];
            $lead['lastName'] = $_REQUEST['lastName'];
            $lead['phone'] = $_REQUEST['phone'];
            $lead['language'] = $_REQUEST['language'];
            $lead['country'] = $_REQUEST['country'];


            $lead['affiliate_id'] = $affiliateId;
            $lead['btag'] = 'a' . $affiliateId . '-b1-p';
            if (empty($lead['password'])) {
                $lead['password'] = 'AffTS' . rand(100, 1000);
            }
            $lead['currency'] = (!empty($_POST['currency'])) ? $_POST['currency'] : 'usd';
            $lead['accountType'] = (!empty($_POST['accountType'])) ? $_POST['accountType'] : 'real';

            $additioanal = [
                'site' => (!empty($_REQUEST['site']))?$_REQUEST['site']:'',
                'source' => (!empty($_REQUEST['source']))?$_REQUEST['source']:'',
                'campaign' => (!empty($_REQUEST['campaign']))?$_REQUEST['campaign']:'',
                'affiliate' => $affiliateId,
            ];
            
            
            
            // Add Customer
            $result = $api->integrations()->createCustomer(
                    $lead['email'], $lead['password'], $lead['firstName'], $lead['lastName'], $lead['currency'], $lead['accountType'], $lead['phone'], $lead['language'], $lead['country'], $lead['btag'], $additioanal
            );

            echo json_encode($result);

            break;

        case 'add_lead':
            
            $lead['date'] = $_REQUEST['date'];
            $lead['btag'] = $_REQUEST['btag'];
            $lead['saleStatus'] = $_REQUEST['saleStatus'];
            $lead['country'] = $_REQUEST['country']; // ISO2
            $lead['trader_id'] = $_REQUEST['trader_id'];
            $lead['type'] = 'lead';
            $lead['email'] = $_REQUEST['email'];

            $result = $api->internal()->createLead(
                    $lead['date'], $lead['btag'], $lead['saleStatus'], $lead['country'], $lead['trader_id'], $lead['type'], $lead['email']
            );

            echo json_encode($result);

            break;

        case 'add_real':

            $lead['date'] = $_REQUEST['date'];
            $lead['btag'] = $_REQUEST['btag'];
            $lead['saleStatus'] = $_REQUEST['saleStatus'];
            $lead['country'] = $_REQUEST['country']; // ISO2
            $lead['trader_id'] = $_REQUEST['trader_id'];
            $lead['type'] = 'real';
            $lead['email'] = $_REQUEST['email'];
            $lead['name'] = $_REQUEST['name'];
            $lead['phone'] = $_REQUEST['phone'];

            $result = $api->internal()->createCustomer(
                    $lead['date'],
                    $lead['btag'],
                    $lead['saleStatus'],
                    $lead['country'],
                    $lead['trader_id'],
                    $lead['type'],
                    $lead['email'],
                    $lead['name'],
                    $lead['phone']
            );

            echo json_encode($result);

            break;

        case 'add_customer':

            $lead['date'] = date('Y-m-d H:i:s');

            $lead['email'] = $_REQUEST['email'];
            $lead['password'] = $_REQUEST['password'];
            $lead['firstName'] = $_REQUEST['firstName'];
            $lead['lastName'] = $_REQUEST['lastName'];
            $lead['phone'] = $_REQUEST['phone'];
            $lead['language'] = $_REQUEST['language'];
            $lead['country'] = $_REQUEST['country'];

            $lead['affiliate_id'] = $affiliateId;
            if(empty($_REQUEST['btag'])){
            $lead['btag'] = 'a' . $affiliateId . '-b1-p';
            }else{
                $lead['btag'] = $_REQUEST['btag'];
            }
            if (empty($lead['password'])) {
                $lead['password'] = 'AffTS' . rand(100, 1000);
            }
            $lead['currency'] = (!empty($_POST['currency'])) ? $_POST['currency'] : 'usd';
            $lead['accountType'] = (!empty($_POST['accountType'])) ? $_POST['accountType'] : 'real';

            $additioanal = [
                'site' => (!empty($_REQUEST['site']))?$_REQUEST['site']:'',
                'source' => (!empty($_REQUEST['source']))?$_REQUEST['source']:'',
                'campaign' => (!empty($_REQUEST['campaign']))?$_REQUEST['campaign']:'',
                'affiliate' => $affiliateId,
            ];
            
            // Add Customer
            $result = $api->integrations()->createCustomer(
                $lead['email'], $lead['password'], $lead['firstName'], $lead['lastName'], $lead['currency'],
                $lead['accountType'], $lead['phone'], $lead['language'], $lead['country'], $lead['btag'], $additioanal
            );

            if ($result['status'] === true ) {
                $lead['btag'] = $_REQUEST['btag'];
                $lead['saleStatus'] = $_REQUEST['saleStatus'];
                $lead['country'] = $_REQUEST['country']; // ISO2
                $lead['trader_id'] = $result['trader_id'];//$_REQUEST['trader_id'];
                $lead['type'] = 'real';
                $lead['email'] = $_REQUEST['email'];
                $lead['name'] = $lead['firstName'].' '.$lead['lastName'];

                $internal = $api->internal()->createLead(
                    $lead['date'], $lead['btag'], $lead['saleStatus'], $lead['country'], $lead['trader_id'], $lead['type'], $lead['email'], $lead['name']
                );

                if(!empty($result['autologin'])){
                    $internal['autologin'] = $result['autologin'];
                }

                echo json_encode($internal);
            } else {
                echo json_encode(['status' => 'fail', 'msg' => 'On API customer is not created!','errors' => $result['errors']]);
            }

            break;

        case 'add_transaction':
            
            
            $data['date'] = $_REQUEST['date'];
            $data['trader_id'] = $_REQUEST['trader_id'];
            $data['tranz_id'] = $_REQUEST['tranz_id'];
            $data['type'] = $_REQUEST['type'];
            $data['currency'] = $_REQUEST['currency'];
            $data['amount'] = $_REQUEST['amount'];
            
            $result = $api->internal()->createTransaction(
                    $data['date'], $data['trader_id'], $data['tranz_id'], $data['type'], $data['currency'], $data['amount']
            );

            echo json_encode($result);

            break;

        default:
            throw new \Exception('Unavailable action!');
            break;
    }
} catch (Exception $e) {
    die($e->getMessage());
}
