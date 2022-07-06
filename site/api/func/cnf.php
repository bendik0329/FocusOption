<?php


$f=array("Cookie: PHPSESSID=oa2c67ncuqm9hjo0po8jj8hnt2; tzoffset=28800; _ga=GA1.2.1201324803.1505236293; _gid=GA1.2.2023155081.1505236293","User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:55.0) Gecko/20100101 Firefox/55.0","Accept: */*","Accept-Language: en-US,en;q=0.5","X-Requested-With: XMLHttpRequest","Content-Type: application/x-www-form-urlencoded; charset=UTF-8","Referer: https://admin-api.tradesmarter.com/login","Authorization: Basic ZmluLWJ1ZGRpZXMtYXBpOmY4ZGEyNWRm","Connection: keep-alive","Pragma: no-cache","Cache-Control: no-cache");

$uri = 'https://admin-api.tradesmarter.com/admin/login';
$ch = curl_init($uri);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,'user=fin-buddies-api&password=1ebd4273fa485371f81843b737cd7f1b');  //Post Fields

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt_array($ch, array(
    CURLOPT_HTTPHEADER  => $f,
    CURLOPT_RETURNTRANSFER  =>true,
    CURLOPT_VERBOSE     => 1
));
//user=fin-buddies-api&password=1ebd4273fa485371f81843b737cd7f1b
$out = curl_exec($ch);
curl_close($ch);
// echo response output
echo $out;