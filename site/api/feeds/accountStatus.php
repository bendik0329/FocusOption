<?php
	require_once('../../common/database.php');

include ('helper.php');	


		
	$apiToken = isset($_GET['apiToken']) ? $_GET['apiToken'] : -1;
	if ($apiToken<0)
	die ('<Error>#1009</Error>');

	$qry = "select * from config_api_n_feeds where 1=1 and APIaccessType='leadStatus'  ";
	$rsc = mysql_query($qry);
	$row = mysql_fetch_assoc($rsc);
	
	if ($apiToken != $row['apiToken'])
	die ('<Error>#1008</Error>');
		
		
		

	
$ip = getip();

$isAffiliateBuddiesOffice=false;

 if ($ip =='62.219.229.235') 
	$isAffiliateBuddiesOffice=true;	

		/* if (($row['apiStaticIP']!=$ip || strlen($ip)==0 || empty ($row['apiStaticIP'])) && !$isAffiliateBuddiesOffice) {
			
			die ('error: 1011');
		} */
		
		if ((false===checkIPInList($ip,$row['apiStaticIP']) || strlen($ip)==0 || empty ($row['apiStaticIP'])) && !$isAffiliateBuddiesOffice) {
				die ('error: 1011');
		}

	
	if ($row['apiAccessType']=='None' || $row['apiAccessType']=='') {
		die ('<Error>1012: No permissions to view modules</Error>');
	} 
	
	if ($row['status']==0) {
		die ('<Error>Feed is not active.</Error>');
	}
	
 
	
	set_time_limit(0);
	
		
		
		
	
	if ($row['apiAccessType']=='leadStatus' || $row['apiAccessType']=='all') {
		
	
		$where = " where 1=1";
		$qry = "select id,trader_id,merchant_id,affiliate_id,saleStatus,rdate from data_reg " . $where;
		
		if(isset($_GET['merchant_id']) && !empty($_GET['merchant_id'])){
			$qry .= " and merchant_id = " . $_GET['merchant_id'];
		}
		if(isset($_GET['affiliate_id']) && !empty($_GET['affiliate_id'])){
			$qry .= " and affiliate_id = " . $_GET['affiliate_id'];
		}
		if(isset($_GET['trader_id']) && !empty($_GET['trader_id'])){
			$qry .= " and trader_id = " . $_GET['trader_id'];
		}
		
		
		if(!isset($_GET['trader_id']) && empty($_GET['trader_id'])){
			$startdate = date("Y-m-d", strtotime("-7 days"));
			$qry .= " and rdate between '" . $startdate ."' and '" . date("Y-m-d 23:59:59") . "'";
		}
		
		if ($_GET['debug']==5)
			echo $qry.'<br>';
		
		
		$qqq=mysql_query($qry); 
		$qqq2= $qqq;
			$counter=0;
		if($row['outputType'] == "XML"){
			header('Content-Type: text/xml');
	
			echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
			echo '<data>'."\n";
			$list="";
			$xml .= '<LeadStatus>';
					while ($row=mysql_fetch_assoc($qqq)) {
						$counter++;
				
					$list .= '<leadStatusInfo>';
							
							foreach($row as $k=>$v)
							{
								$list .= '<'. $k .'>'.$v.'</'. $k .'>';
							}
						$list .= '</leadStatusInfo>';
					
				}
				
							flush();
				$xml .= $list . '</LeadStatus>';
			mysql_close();
			echo ($xml);
			echo '</data>'."\n";
			die();
		}
		else{
				$arr = array();
				while ($row=mysql_fetch_assoc($qqq2)) {
					array_push($arr,$row);
				}
				echo json_encode($arr);
				die;
		}
	}
	else {
		die ('<Error>404.09</Error>');
	}