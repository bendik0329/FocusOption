<?php
	
	require_once('../common/database.php');
	
	// var_dump($_SERVER);
	
	$buddies = isset($_GET['buddies']) ? $_GET['buddies'] : 0;
	$affiliate_id = isset($_GET['affiliate_id']) ? $_GET['affiliate_id'] : -1;
	if ($affiliate_id<0)
		die ('error: #1008 - Affiliate ID issue');
	
	$apiToken = isset($_GET['apiToken']) ? $_GET['apiToken'] : -1;
	if ($apiToken<0)
		die ('error: #1009 - APItoken issue');
	
	
	
	$fromdate = isset($_GET['fromdate']) ? $_GET['fromdate'] : date('Y-m-d');
	
	$qry = "select * from affiliates where valid = 1 and id = " . $affiliate_id . " limit 1 ;";
	$rsc = mysql_query($qry);
	$row = mysql_fetch_assoc($rsc);
	if (!isset($row['id']) || empty($row['id'])) {
		die ('error: 1010');
	}
	
$ip = "";	
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
	
}

$isAffiliateBuddiesOffice=false;
if ($ip =='80.178.187.235')
	$isAffiliateBuddiesOffice=true;	

	$explode = explode('|',$row['apiStaticIP']);
	$isit = false;
	foreach ($explode as $ex) {
		$exp_ip = $ex;
		if (trim($exp_ip)==$ip ){
			$isit = true;
			break;
		}
	}
		
	if ($isAffiliateBuddiesOffice) {
	}
	else 	if ((!$isit || strlen($ip)==0 || empty ($row['apiStaticIP']))) {

	$file = 'api-feed_log.txt';
// Open the file to get existing content
$current = file_get_contents($file);
// Append a new person to the file
$current .= "http".$set->SSLswitch."://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] .'    ' .$ip  . '    ' . $row['id'] . '    ' .date('Y-m-d H:i:s'). PHP_EOL ;
// Write the contents back to the file
file_put_contents($file, $current);

		die ('error #1011 - IP related Issue');
	}
	

	$merchants= array();
$merrsc = mysql_query("select * from merchants where valid = 1");
	while( $merrow = mysql_fetch_assoc($merrsc)) {
		$merchants[$merrow['id']]=$merrow;
	}
	
	
	if ($row['apiAccessType']=='None' || $row['apiAccessType']=='') {
		die ('error: 1012: No permissions to view modules');
	}
	

	
	
	$endDate = date('Y-m-d', strtotime('+24 hours')) ; //date("Y-m-d",strtotime("+1 Days"));
	
	$where .= " AND rdate >= '".$fromdate."'";
	$where .= " AND rdate <= '".$endDate."'";
	$whereActivity = $where;
	
// die ($whereActivity);
	 
	
	set_time_limit(0);
	header('Content-Type: text/xml');
	
	echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
	echo '<Traders>'."\n";
		
	
	if ($row['apiAccessType']=='transactions' || $row['apiAccessType']=='all') {
		
			// $qry = "SELECT * FROM data_sales sales left join data_reg reg on reg.trader_id = data_sales.trader_id WHERE (sales.affiliate_id = '".$affiliate_id."')  AND sales.rdate >= '".$fromdate . " AND sales.rdate <= '" .$endDate."' GROUP BY sales.trader_id";
			$qry = 	"SELECT reg.freeParam as param, reg.email as email,reg.campaign_id as campaign_id, reg.ctag as regCtag , sales.rdate as TranzDate, sales.type as TranzType, sales.*  FROM data_sales sales INNER JOIN data_reg reg ON reg.trader_id = sales.trader_id WHERE sales.affiliate_id = '".$affiliate_id."' AND sales.rdate >= '".$fromdate ."' AND sales.rdate <= '" .$endDate . "'";
			// die ($qry);
			$qqq=mysql_query($qry); 
			$counter=0;
			while ($ww=mysql_fetch_assoc($qqq)) {
				$counter++;
				
					echo '<Trader_Transactions_'.$counter.'>'."\n";
					echo '<Merchant>'.$merchants[$ww['merchant_id']]['name'].'</Merchant>'."\n";
					echo '<Merchant_Id>'.$ww['merchant_id'].'</Merchant_Id>'."\n";
					echo '<Campaign>'.$ww['campaign_id'].'</Campaign>'."\n";
					echo '<ctag>'.$ww['regCtag'].'</ctag>'."\n";
					if ($buddies==888){
					echo '<email>'.$ww['email'].'</email>'."\n";
					}
					echo '<dynamicParameter>'.$ww['param'].'</dynamicParameter>'."\n";
					echo '<country>'.$ww['country'].'</country>'."\n";
					echo '<TransactionTime>'.$ww['TranzDate'].'</TransactionTime>'."\n";
					echo '<TraderID>'.$ww['trader_id'].'</TraderID>'."\n";
					echo '<TransactionID>'.$ww['tranz_id'].'</TransactionID>'."\n";
					echo '<Type>'.$ww['TranzType'].'</Type>'."\n";
					echo '<Amount>'.$ww['amount'].'</Amount>'."\n";
					// echo '</Trader_Transactions>'."\n";
					echo '</Trader_Transactions_'.$counter.'>'."\n";
					flush();
					}
				
	}

	// var_dump($row['apiAccessType']);
	// die();
	
	if ($row['apiAccessType']=='accounts' || $row['apiAccessType']=='all') {
	
			$qry = "SELECT * FROM data_reg reg WHERE affiliate_id = '".$affiliate_id."' AND reg.rdate >= '".$fromdate ."' AND reg.rdate <= '" .$endDate . "'";
			// die ($qry);
			$qq=mysql_query($qry);
			$counter=0;
			while ($ww=mysql_fetch_assoc($qq)) {
				$counter++;
				echo '<Trader_Account_'.$counter.'>'."\n";
				echo '<regTime>'.$ww['rdate'].'</regTime>'."\n";
				
				$qry2 = "SELECT amount,rdate FROM data_sales WHERE type='deposit' AND rdate >= '".$ww['rdate'] ."'  AND trader_id='".$ww['trader_id']."' ORDER BY id ASC LIMIT 1";
				// echo ($qry2);
				$FirstDeposit = mysql_fetch_assoc(mysql_query($qry2));
				
			
				echo '<Merchant>'.$merchants[$ww['merchant_id']]['name'].'</Merchant>'."\n";
				echo '<Merchant_Id>'.$ww['merchant_id'].'</Merchant_Id>'."\n";
				echo '<Campaign>'.$ww['campaign_id'].'</Campaign>'."\n";
				echo '<dynamicParameter>'.$ww['freeParam'].'</dynamicParameter>'."\n";
				echo '<saleStatus>'.$ww['saleStatus'].'</saleStatus>'."\n";
				echo '<ctag>'.$ww['ctag'].'</ctag>'."\n";
					if ($buddies==888){
					echo '<email>'.$ww['email'].'</email>'."\n";
					}
				echo '<country>'.$ww['country'].'</country>'."\n";
				echo '<type>'.$ww['type'].'</type>'."\n";
				echo '<TraderID>'.$ww['trader_id'].'</TraderID>'."\n";
				echo '<FirstDepositDate>'.$FirstDeposit['rdate'].'</FirstDepositDate>'."\n";
				echo '<FirstDepositAmount>'.$FirstDeposit['amount'].'</FirstDepositAmount>'."\n";
				// echo '</Trader_Account>'."\n";
				echo '</Trader_Account_'.$counter.'>'."\n";
				flush();
				}
			
}	
			
				
			
	
	echo '</Traders>'."\n";
	mysql_close();
	die();
?>