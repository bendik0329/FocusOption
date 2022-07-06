<?php

require('common/global.php');


$loopMerchantsSrc = mysql_query("select * from merchants");
while ($ww = mysql_fetch_assoc($loopMerchantsSrc)){
echo 'processing merchant: ' . $ww['name'].'<br>';
		// $qry =  "SELECT * FROM data_reg WHERE initialftddate='0000-00-00 00:00:00' and  lastProcessFTDDate='0000-00-00 00:00:00' and merchant_id = ".$ww['id'] ." ";
		$qry =  "SELECT dr.* FROM data_reg dr 
inner join (select min(rdate) , trader_id , merchant_id,id from data_sales where type='deposit' and merchant_id = ".$ww['id']." group by trader_id  ) ds
on ds.merchant_id = dr.merchant_id and  dr.trader_id  =  ds.trader_id 

WHERE dr.initialftddate='0000-00-00 00:00:00' and dr.lastProcessFTDDate='0000-00-00 00:00:00' and dr.merchant_id = ".$ww['id'];
		$rsc = mysql_query($qry);
// die ($qry);
		while ($traderInfo = mysql_fetch_assoc($rsc)){
		
			$currDate = date('Y-m-d H:i:s');

			// if ($traderInfo['ftdamount']==0 && $db['type']=='deposit' && $traderInfo['initialftdtranzid']=='') {
			if ($traderInfo['ftdamount']==0 && $traderInfo['initialftdtranzid']=='') {
					// die('rfgergere');
							$GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from data_sales where 
													trader_id = " . $traderInfo['trader_id'] .  " and merchant_id = " . $traderInfo['merchant_id'] . " and type='deposit'  order by rdate limit 0,1";
												
									$GetFTDforTrader =mysql_fetch_assoc( mysql_query($GetFTDforTraderQuery));
									if (!empty($GetFTDforTrader)) {
											$UpdateFTDforTrader = "update data_reg set  ftdamount = " .$GetFTDforTrader['amount']." , initialftdtranzid = '"
											.$GetFTDforTrader['tranz_id']."' , lastProcessFTDDate = '" .$currDate ."' , initialftddate = '" .$GetFTDforTrader['rdate'] ."'  where trader_id= ".$traderInfo['trader_id']. "  
											and merchant_id = " . $traderInfo['merchant_id'];
											// die ($UpdateFTDforTrader);
											mysql_query($UpdateFTDforTrader) or die(mysql_error());
											echo $currDate . ' :    New FTD record added to Data_Reg, TraderID: ' . $traderInfo['trader_id'].'<br>';
									}
			}
	}
}

die ('Done');		
/* 

 SELECT tb1.*
FROM data_sales AS tb1

WHERE 1 =1
	AND tb1.merchant_id =1
	AND tb1.rdate
	BETWEEN  '2014-11-22' AND  '2016-11-22 23:59:59'
	AND tb1.type =  'deposit'
	AND tb1.affiliate_id =1025
	AND tb1.trader_id NOT 
	IN (
		SELECT trader_id
		FROM data_sales
		WHERE 
			merchant_id =1
			AND affiliate_id =1025
			AND rdate < tb1.rdate
			and trader_id = tb1.trader_id
			AND TYPE =  'deposit'
		order by merchant_id,trader_id,rdate
        
	)
GROUP BY tb1.trader_id


?>



+ Options
 Full texts	
id
rdate
ctag
affiliate_id
group_id
banner_id
profile_id
product_id
country
trader_id
sub_trader_id
phone
trader_alias
type
freeParam
freeParam2
merchant_id
status
lastUpdate
platform
uid
saleStatus
lastSaleNote
lastSaleNoteDate
lastTimeActive
initialftddate
initialftdtranzid
ftdamount */



?>