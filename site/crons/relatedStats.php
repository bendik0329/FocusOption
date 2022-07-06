<?php

// require('../common/global.php');
require(__DIR__ .'/../func/func_debug.php');
require(__DIR__ .'/../common/database.php');

$debug = isset($_GET['dbg1']) ? $_GET['dbg1']: 0;
$loopMerchantsSrc = mysql_query("select * from merchants");
while ($ww = mysql_fetch_assoc($loopMerchantsSrc)){
echo 'processing merchant: ' . $ww['name'].'<br>';

		$qry = "select id,merchant_id,trader_id,rdate from data_reg where lastDepositRecordDate='0000-00-00 00:00:00' and merchant_id = ".$ww['id']. " and trader_id<>'';";//. and trader_id = '2617277'  limit 1;" ;
		if ($debug) echo $qry.'<Br>';
		$rsc = mysql_query($qry);

		while ($traderInfo = mysql_fetch_assoc($rsc)){
			
			$currDate = date('Y-m-d H:i:s');

							$getTotalDeposits = "select max(rdate) as maxrdate ,sum(amount) as amount from data_sales where 
													trader_id = " . $traderInfo['trader_id'] .  " and merchant_id = " . $traderInfo['merchant_id'] . " and type='deposit'  ";
		if ($debug) echo $getTotalDeposits.'<Br>';									
											// var_dump($traderInfo);
											// die	($getTotalDeposits);
									$traderValue =mysql_fetch_assoc( mysql_query($getTotalDeposits));
									if (!empty($traderValue['maxrdate'])) {
										
										
											$UpdateTraderValue = "update data_reg set  traderValue = " .$traderValue['amount']." , lastDepositRecordDate = '"
											.$traderValue['maxrdate']."' where trader_id= ".$traderInfo['trader_id']. "  
											and merchant_id = " . $traderInfo['merchant_id'];
											
											mysql_query($UpdateTraderValue) or die(mysql_error());
											
											echo $currDate . ' :    New TraderValue record added to Data_Reg'.$traderInfo['rdate'].', TraderID: ' . $traderInfo['trader_id'].'<br>';
									}
									else {
											$UpdateTraderValue = "update data_reg set  traderValue = 0 , lastDepositRecordDate = '"
											.$currDate."' where trader_id= ".$traderInfo['trader_id']. "  
											and merchant_id = " . $traderInfo['merchant_id'];
											
											mysql_query($UpdateTraderValue) or die(mysql_error());
									}
			}
			
			echo '<br>Starting Trades<br>';
		$qry = "select id,merchant_id,trader_id,rdate from data_reg where lastStatsRecordDate='0000-00-00 00:00:00' and merchant_id = ".$ww['id']. " and trader_id<>'';";//." and trader_id = '2617277'  limit 1;" ;
		if ($debug) echo $qry.'<Br>';
		$rsc = mysql_query($qry);

		while ($traderInfo = mysql_fetch_assoc($rsc)){
			
			$currDate = date('Y-m-d H:i:s');

							$getTotalDeposits = "select max(rdate) as maxrdate ,sum(amount) as amount,count(amount) as count from data_sales where 
													trader_id = " . $traderInfo['trader_id'] .  " and merchant_id = " . $traderInfo['merchant_id'] . " and type='volume'  ";
												
							if ($getTotalDeposits) echo $qry.'<Br>';
												
												
									$traderValue =mysql_fetch_assoc( mysql_query($getTotalDeposits));
									if (!empty($traderValue['maxrdate'])) {
											$UpdateTraderValue = "update data_reg set  traderTrades = " .$traderValue['count']." , 
											traderVolume = " .$traderValue['amount']." , lastStatsRecordDate = '"
											.$traderValue['maxrdate']."' where trader_id= ".$traderInfo['trader_id']. "  
											and merchant_id = " . $traderInfo['merchant_id'];
											
											mysql_query($UpdateTraderValue) or die(mysql_error());
											
											echo $currDate . ' :    New Stats record added to Data_Reg '.$traderInfo['rdate'].', TraderID: ' . $traderInfo['trader_id'].'<br>';
									}
									else {
										
											$UpdateTraderValue = "update data_reg set  traderTrades = 0 , 
											traderVolume = 0 , lastStatsRecordDate = '"
											.$currDate."' where trader_id= ".$traderInfo['trader_id']. "  
											and merchant_id = " . $traderInfo['merchant_id'];
											
											mysql_query($UpdateTraderValue) or die(mysql_error());
									}
			}
			
			
	}


die ('Done');		

?>