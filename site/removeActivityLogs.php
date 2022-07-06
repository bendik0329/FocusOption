<?php
header('Access-Control-Allow-Origin: *');
require('common/global.php');

$sql = "delete from logs_activity where rdate <  DATE_SUB( NOW( ) , INTERVAL 1 WEEK )";

mysql_query($sql);
die;
?>