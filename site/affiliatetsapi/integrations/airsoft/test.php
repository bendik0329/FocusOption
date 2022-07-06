<?php

include_once 'AirsoftAPI.php';

/**
 * Auth
 */
$airsoft = new AirsoftAPI();
$airsoft->setCredentials('FuP6uC6npl', 'https://www.faexchange.com');
/*****************************/

/**
 * Create lead
 */
$test['createRegistration'] = $airsoft->sendRequest('POST',[
    'method' => 'createLead',
    'first_name' => 'John',	//First Name
    'last_name' => 'Miles',	//Last name
    'email_address' => 'john.miles@mail.com',	//Email adress
    'phone' => '9725555555',	//Phone number
    'countryISO' => 'UA',	//Country iso code
    'currency' => 'BTC',	//currency code
    'custom_refer' => 'free text',	//(optional - visible value in CRM)
    'campaign_id' => '27',	//create leads/customer under specific campaign
    'campaign_keyword' => 'mykeword',	//(optional - add filter keyword to the campaign)
    'is_lead_only' => 0,	//(optional - 0 for register user,1 for lead only)
    'comment' => 'mycomment',	//(required campaign id)
    'send_register_email' => 1,	//send register email template	(default 0)
]);



echo "<pre>";
print_r($test);
//echo "<br>*********<br>";
//print_r($archer->errors);
//echo "<br>*********<br>";
//print_r($archer->requests);
echo "</pre>";
