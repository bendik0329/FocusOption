<?php
session_start();
set_time_limit(0);
header("Pragma: no-cache");
header("Expires: 0");

ini_set("memory_limit","128M");
$debug = isset($_GET['debug']) ? $_GET['debug'] : 0 ;
require_once('common/global.php');



//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');


$ww = dbGet('1',"merchants");

	
echo '<style type="text/css">html,body { font-size: 11px; font-family: Tahoma; } </style>';



if ($_GET['m_date']) {
	$exp_mdate=explode("-",$_GET['m_date']);
	
	if ($_GET['monthly']) {
		$scanDateFrom = date("Y-m-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-m-01", strtotime("+1 Month",strtotime($scanDateFrom)));//.' 23:59:59';
		$totalPage = 30;
	} else if ($_GET['yearly']) {
		$scanDateFrom = date("Y-01-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-12-01", ($scanDateFrom));//.' 23:59:59';
		die ($scanDateTo);
		//$futureDate=date('Y-m-01', strtotime('+1 year', strtotime($$futureDate=date('Y-m-d', strtotime('+1 year', strtotime($startDate)) );)) );
		$totalPage = 30;
	} else {
		$scanDateFrom = date("Y-m-d", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));//.' 23:59:59';
	}
		
} else {
	$scanDateFrom = date("Y-m-d", strtotime("-1 Day"));//.' 00:00:00';
	$scanDateTo = date("Y-m-d", strtotime("+1 Day"));//.' 00:00:00';
	//$scanDateTo = date("Y-m-d");//.' 23:59:59';
}

if (!$totalPage) {
	$totalPage = 2;
}


echo 'From: <u>'.$scanDateFrom.'</u> To: <u>'.$scanDateTo.'</u>';

$campscount = 0;

echo '<hr /><b>Pushing Position as Volume to data_sales <span style="color:blue">Page: <u>'.$page.'</u>...</span></b><br />';
$qry = "select * from data_stats where rdate between '" . $scanDateFrom . "' and '" . $scanDateTo . "' and merchant_id = " . $ww['id'] . " and type='position'" ;
// $qry = "select * from data_stats where rdate between '" . $scanDateFrom . "' and '" . $scanDateTo . "' and merchant_id = " . $ww['id'] . " and type='position'   and `trader_id` = 73710" ;


$rsc = mysql_query($qry);
	$counter=0;	
while ( $db  = mysql_fetch_assoc($rsc)) {
if ($debug==1) {
$counter++;
echo $counter .'<Br>';
}


if ($isTest>0) {
	var_dump($xml_line);
	echo '<br>';
}
	$db['type']='volume';
	
	$chkdbl = "SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'";
	if ($debug==1) {
		echo $chkdbl.'<br>';
	}
	$chkExist=mysql_fetch_assoc(mysql_query($chkdbl));
	/* if ($chkExist['id']) {
		if ($debug==1)
		echo 'exists: ' . $chkExist['id'] . '<br>';
		$tranzExist = 1;
		}
		else {
			var_dump($db);
			echo '<br>';
			echo '<br>';
		} */
	
	// Check cTag From Trader
	$traderInfo = mysql_fetch_assoc(mysql_query("SELECT * FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."'"));
	if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
		
	// Check cTag From Trader
		
	
	
					$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
				//	$db['freeParam']= overRideDynamicParameter($db['freeParam']);
	
	$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
	if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
		$db['affiliate_id'] = $defaultAffiliateID;
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		}
	$db['group_id'] = $getAffiliate['group_id'];
	
	// if (count($db) > 1 AND !$tranzExist) {
if (!$chkExist['id'] && $db['amount']>0) {
		// dbAdd($db,"data_sales_".strtolower($ww['name']));
		
			$insQry = "INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,
			trader_id,trader_alias,type,amount,freeParam, campaign_id) 
			VALUES
			(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
			'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
			'".$db['amount']."','".$db['freeParam']."', '" . $traderInfo['campaign_id'] . "')";
			// die ($insQry);
		mysql_query($insQry) or die(mysql_error());
		
		
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
		$sales_total++;
		
					
		flush();
	} else {
		if ($isTest >0) 
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
	}
	$intProcessed++;
	if ($intProcessed % 100 == 0) {
		echo $intProcessed . ' Processed <br>';
	}
	$totalRowsFoundThisPage++;
	}
	
	
	if ($totalRowsFoundThisPage>$totalRowsFoundSoFar)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			
	
if ($intProcessed>0)
	echo 'Total processed so far: ' . $intProcessed . '<br>';
	
	


	echo '<br>Ending time' . date('h:i:s') . '<br>';
	echo 'Cron is done!';
exit;

?>