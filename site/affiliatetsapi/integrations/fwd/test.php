<?php
$username = 'aff-bu';
$password = 'vMkCVUtgxAN2swvO';
$secret = '8VuWjQyaW6ZI4uRo82QTLaZ9BD1U1lyX';

$token = $username.'--'.md5($password).'--'.md5($username.$password.$secret).'/';
$string = "GetAllLeadsStatus";
$post = array();

$url = 'http://fxwdc0788.forexwebdesign.net/api/v1/';

$ch = curl_init($url.$token.$string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
$data = curl_exec($ch);
curl_close($ch);
$get = json_decode($data);

echo $url.$token.$string;
echo "<br>";
var_dump($get);
