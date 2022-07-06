<?php
chdir("../");
require('common/global.php');
ini_set('memory_limit', '-1');
set_time_limit(0);


$where = "";

$m_date = isset($_GET['m_date']) ? $_GET['m_date'] : "";
$to = isset($_GET['from']) ? $_GET['from'] : date('Y-m-d');
$from = date("Y-m-d" ,strtotime("-1 months"));	


 if(isset($m_date) && !empty($m_date) ){
	$from =  $m_date . " 00:00:00";
	$to =  $m_date . " 23:59:59";
 }
 

$rsc = function_mysql_query("select * from data_install where trader_id ='' and uid>0 and rdate>'" . $from ."' and rdate<'" . $to . "' ");
$counter = 0;
while ($row = mysql_fetch_assoc($rsc)){
	$counter++;
	$traderInfo = mysql_fetch_assoc(function_mysql_query("select * from data_reg where rdate>'" . $row['rdate'] ."' and uid='". $row['uid'] . "' limit 1;"));
	$q = "update data_install set merchant_id = " . $traderInfo['merchant_id'] . " and trader_id = '" . $traderInfo['trader_id'] . "' where id = " . $traderInfo['id'];
	unset($traderInfo);
	function_mysql_query($q);
	echo $counter.'<br>';

}

die ('done');
