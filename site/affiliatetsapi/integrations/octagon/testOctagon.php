<?php

include_once 'OctagonAPI.php';

/**
 * Auth
 */
$client = new OctagonAPI();
$client->setCredentials('uslspqw009982hanzdkriew', 'https://distributor.octagonfx.com');


$test['CreateLead'] = $client->createCustomer([
    'name' => 'Test',
    'lastname' => 'Test',
    'email' => 'affiliatets14@test.com',
    'country' => 'ua',
    'language' => 'en',
    'ip' => '127.0.0.1',
    'phone' => '+380500000014',
    'site' => 'http://site.com/',
    'source' => 'test',
    'campaign' => '1'
]);


//$test['Autologin'] = $client->getAutologin(['id' => '5997']);



$test['Getlead'] = $client->getLeads([
    'from' => '2019-10-21',
    'to' => '2019-10-22'
]);

/*
  $test = $client->getDeposits([
  'from' => '2019-10-01',
  'to' => date('Y-m-t')
  ]);
 */

/*
  $test = $client->getWithdrawals([
  'from' => '2019-10-01',
  'to' => date('Y-m-t')
  ]);
 */
/*
  $test = $client->getPnlAndValues([
  'from' => '2019-09-01',
  'to' => date('Y-m-t')
  ]);
 */
/* * ************************** */

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
//$test = $panda->getTransactions(['filter' => ['createdTime' => ['min' => '2018-03-01T00:00:00+00:00','max' => '2018-04-30T00:00:00+00:00']]]);


echo "<pre>";
foreach ($test as $k => $r) {
    echo "<h3>".$k."</h3>";
    print_r($r);
}
//echo "<br>*********<br>";
echo "<h3>Errors</h3>";
print_r($client->errors);
//echo "<br>*********<br>";
//print_r($panda->requests);
echo "</pre>";
