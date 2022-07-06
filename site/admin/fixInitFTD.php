<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('../common/database.php');


	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
	
}

if (!($ip == '212.199.106.82') and !($ip=='212.143.60.28')){
	die('!!');
}

		$set->pageTitle = ('fix FTD dates in data reg');
		echo 'hi<br>';
		
		$merchant_id = 22;
		

		for ($i = 0; $i < 1000 ;$i++) 
		{


		$sql = "SELECT dr .* 
					FROM data_reg dr
					INNER JOIN data_sales ds ON dr.trader_id = ds.trader_id
					AND dr.merchant_id = ds.merchant_id
					WHERE dr.initialftdtranzid =  '' and dr.ftdamount=0 and ds.type='deposit' and dr.type='real'  and dr.merchant_id = " . $merchant_id . " 
					LIMIT 200
					";
		

		/*
		$sql = "SELECT MIN(ds.rdate) as rdate, ds.amount as amount, ds.tranz_id  as tranz_id ,ds.trader_id as trader_id ,ds.merchant_id  as merchant_id
					FROM data_reg dr
					INNER JOIN data_sales ds ON dr.trader_id = ds.trader_id
					AND dr.merchant_id = ds.merchant_id
					WHERE dr.initialftdtranzid =  '' and dr.ftdamount=0 and ds.type='deposit' and dr.type='real' 
					LIMIT 10
					";
			*/
		
		$getDataRegRecords = function_mysql_query($sql,__FILE__);
		
		if (!$getDataRegRecords)
			return;
			
		$count=0;
		while($row = mysql_fetch_assoc($getDataRegRecords)){
						
						$GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from data_sales where 
										trader_id = " . $row['trader_id'] .  " and merchant_id = " . $row['merchant_id'] . " and type='deposit' 	order by rdate limit 0,1";
									
						$GetFTDforTrader =mysql_fetch_assoc( function_mysql_query($GetFTDforTraderQuery,__FILE__));
						if (!empty($GetFTDforTrader)) {
								$UpdateFTDforTrader = "update data_reg set  ftdamount = " .$GetFTDforTrader['amount']." , initialftdtranzid = '"
								.$GetFTDforTrader['tranz_id']."' , initialftddate = '" .$GetFTDforTrader['rdate'] ."' where trader_id= ".$row['trader_id']. "  
								and merchant_id = " . $row['merchant_id'];
								
								function_mysql_query($UpdateFTDforTrader,__FILE__);

								$count++;
								
								if($count % 10 == 0) {
								echo "Counter: " . $count. '<br>';
									if($count % 80 == 0) 
									echo ($UpdateFTDforTrader . '<br>');
								}
						}
		}
		
	}
					
	//	theme();
		//break;
	

?>