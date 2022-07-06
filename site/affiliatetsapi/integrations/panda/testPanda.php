<?php

include_once 'Panda.php';

/**
 * Auth
 */
$panda = new Panda();
$panda->setCredentials('72224', 'bfc99b469a015cdaa81ad2537797c887f2fa005d115653210f4f2753aa27240a', 'https://fxpmarket.pandats-api.io');
/*****************************/

/**
 * Create lead
 */
//$test = $panda->createLead(['email' => 'anton@affiliatets.com','firstName' => 'AffiliateTS','lastName' => 'Test','phone' => '+380501234567','language' => 'ukr','country' => 'ua','leadSource' => '','referral' => 'btag=a500-b1-p',]);

/**
 * Get leads
 */
//$test = $panda->getLeads([]);


/**
 * Create Customer
 */
//$test = $panda->createCustomer(['email' => 'anton2@affiliatets.com','password' => '1a2s3d','firstName' => 'Affiliate2','lastName' => 'Test2','country' => 'ua','currency' => 'usd','accountType' => 'demo','phone' => '+380501111111','language' => 'ukr','referral' => 'btag=a500-b1-p',]);


/**
 * Get Customers
 */
//$test = $panda->getCustomers(['filter' => ['createdTime' => ['min' => '2018-04-05T00:00:00+00:00','max' => '2018-04-18T00:00:00+00:00']]]);

/**
 * Get Customer
 */
//$test = $panda->getCustomer(['email' => 'anton@affiliatets.com']);

/**
 * Get Customer Trading Accounts
 */
//$test = $panda->getCustomerTradingAccounts(['email' => 'anton@affiliatets.com']);

/**
 * Get Trading Accounts
 */
//$test = $panda->getTradingAccounts(['filter' => ['createdTime' => ['min' => '2018-04-01T00:00:00+00:00','max' => '2018-04-12T00:00:00+00:00']]]);

/**
 * Get Customer Transactions
 */
//$test = $panda->getCustomerTransactions(['email' => 'anton2@affiliatets.com']);

/**
 * Get Transactions
 */
$test = $panda->getTransactions(['filter' => ['createdTime' => ['min' => '2018-03-01T00:00:00+00:00','max' => '2018-04-30T00:00:00+00:00']]]);


echo "<pre>";
print_r($test);
//echo "<br>*********<br>";
//print_r($panda->errors);
//echo "<br>*********<br>";
//print_r($panda->requests);
echo "</pre>";
