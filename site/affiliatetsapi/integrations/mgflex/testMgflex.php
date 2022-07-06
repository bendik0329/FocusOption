<?php

include_once 'MgflexAPI.php';

/**
 * Auth
 */
$mgflex = new MgflexAPI();
$mgflex->setCredentials('#{aff_222}#Tag303', 'eyJhbGciOiJSUzI1NiIsImtpZCI6IkFDMTMyRjM2MjBGMDg1NTFCNTAyQTZBMjA1OEZCMkYzMjY3REZEOEUiLCJ0eXAiOiJKV1QiLCJ4NXQiOiJyQk12TmlEd2hWRzFBcWFpQlkteTh5WjlfWTQifQ.eyJuYmYiOjE1NDUzMDEyMjgsImV4cCI6MTYzMTcwMTIyOCwiaXNzIjoiaHR0cHM6Ly9pZGVudGl0eS5meGdsb2JlL', 'https://form.mgflex.com/api/');
$result = $mgflex->getLeads([
    'fromDate' => '2018-12-26',
    'toDate' => '2019-01-03'
]);
/*
$result = $mgflex->getFTDForDateRange([
    'affilateId' => '500',
    'fromDate' => '2018-12-26',
    'toDate' => '2019-01-03'
]);*/

echo "<pre>";
print_r($result);
echo "</pre>";
