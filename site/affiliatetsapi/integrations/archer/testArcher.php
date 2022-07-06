<?php

include_once 'Archer.php';

/**
 * Auth
 */
$archer = new ArcherAPI();
$archer->setCredentials('eug@affiliatets.com', 'qwe123', 'https://msgroupapiservices.archercts.com/archerctsapi');
/*****************************/

/**
 * Get leads
 */
$test['leads'] = $archer->getLeads(['Status' => 3,'FromDate'=>'2018-05-01','ToDate' => '2018-05-30']);

$test['trades_sum'] = $archer->getWalletsTradedSummary(['CurrencyCode' => 'USD','FromDate'=>'2018-05-01','ToDate' => '2018-05-30']);

$walletsInfo = $archer->getWalletsInfo([]);


$wallets = $archer->getClientInfo([]);
if(!empty($wallets->WalletNumbers)){
    foreach($wallets->WalletNumbers as $wallet){
        $test['trades'][$wallet->Value] = $archer->getTrades(['WalletKey' => $wallet->Key,'TypeOfTrade'=>'2','FromDate'=>'2018-05-01','ToDate' => '2018-05-30']);
    }
}

$test['ftd'] = $archer->getFTD(['CurrencyCode' => 'USD','FromDate'=>'2018-05-01','ToDate' => '2018-05-30']);

$test['transactions'] = $archer->getTransactions(['CurrencyCode' => 'USD','FromDate'=>'2018-05-01','ToDate' => '2018-05-30']);

$test['withdrawals'] = $archer->getWithdrawals(['CurrencyCode' => 'USD','FromDate'=>'2018-05-01','ToDate' => '2018-05-30']);



echo "<pre>";
print_r($test);
//echo "<br>*********<br>";
//print_r($archer->errors);
//echo "<br>*********<br>";
//print_r($archer->requests);
echo "</pre>";
