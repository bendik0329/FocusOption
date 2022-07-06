<?php
require('common/global.php');

$ctag=$_GET['ctag'];
$exp=explode("-",$ctag);
$affiliate_id=substr($exp[0],1);
$banner_id=substr($exp[1],1);
$profile_id=substr($exp[2],1);

echo 'This banner is working fine!';

mysql_close();
die();
?>