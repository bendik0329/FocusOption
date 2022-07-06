<?php

include_once 'IntenseAPI.php';

/**
 * Auth
 */
$intense = new IntenseAPI();
$intense->setCredentials('api_pov@casinointense.com', '@34rewsdff', 'https://secret.casinointense.com/api/');
/*
$result = $intense->getLeads([
    'from' => '2019-01-01 00:00:00',
    'to' => '2019-02-01 00:00:00'
]);*/

$result = $intense->getTransactions([
    'from' => '2019-01-01 00:00:00',
    'to' => '2019-02-01 00:00:00'
]);

echo "<pre>";
print_r($result);
echo "</pre>";
