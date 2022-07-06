<?php
	
	require_once('../../common/database.php');
include ('helper.php');	
require_once '../../func/func_string.php';

	
	
	$apiToken = isset($_GET['apiToken']) ? $_GET['apiToken'] : -1;
	if ($apiToken<0)
		die ('error: #1009');
	

		$qry = "select * from config_api_n_feeds where 1=1 and APIaccessType='creative'  ";
	$rsc = mysql_query($qry);
	$row = mysql_fetch_assoc($rsc);
	
	if ($apiToken != $row['apiToken'])
	die ('<Error>#1008</Error>');
		
		
$ip = getip();

$isAffiliateBuddiesOffice=false;
if ($ip =='62.219.229.235')
	$isAffiliateBuddiesOffice=true;	

	
	if ((false===checkIPInList($ip,$row['apiStaticIP']) || strlen($ip)==0 || empty ($row['apiStaticIP'])) && !$isAffiliateBuddiesOffice) {
				die ('error: 1011');
	}
	

	$merchants= array();
$merrsc = (mysql_query("select * from merchants where valid = 1"));
	while( $merrow = mysql_fetch_assoc($merrsc)) {
		$merchants[$merrow['id']]=$merrow;
	}
	
	
	if ($row['apiAccessType']=='None' || $row['apiAccessType']=='') {
		die ('error: 1012: No permissions to view modules');
	}
	
	if ($row['status']==0) {
		die ('<Error>Feed is not active.</Error>');
	}

	/* 
	
	$endDate = date('Y-m-d', strtotime('+24 hours')) ; //date("Y-m-d",strtotime("+1 Days"));
	
	$where .= " AND rdate >= '".$fromdate."'";
	$where .= " AND rdate <= '".$endDate."'"; */
	$whereActivity = $where;
	

	 
	
	set_time_limit(0);
	
		
	
	if ($row['apiAccessType']=='creative' || $row['apiAccessType']=='all') {
		
			
			$qry = 	"SELECT * from merchants_creative where valid =1";
			
			$qqq=mysql_query($qry); 
			$counter=0;
			if($row['outputType'] == "XML"){
				header('Content-Type: text/xml');
	
	echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
	echo '<data>'."\n";
			while ($ww=mysql_fetch_assoc($qqq)) {
				$counter++;
				
					echo '<Creative_'.$counter.'>'."\n";
					echo '<Merchant>'.$merchants[$ww['merchant_id']]['name'].'</Merchant>'."\n";
					echo '<Merchant_Id>'.$ww['merchant_id'].'</Merchant_Id>'."\n";
					echo '<Creative_id>'.$ww['id'].'</Creative_id>'."\n";
					echo '<type>'.$ww['type'].'</type>'."\n";
					echo '<title>'.$ww['title'].'</title>'."\n";
					echo '<alt>'.$ww['alt'].'</alt>'."\n";
					echo '</Creative_'.$counter.'>'."\n";
					flush();
					}
				echo '</data>'."\n";
				die();
			}
			else{
				
				$arr = array();
				while ($ww=mysql_fetch_assoc($qqq)) {
					array_push($arr,$ww);
				}
				
				echo json_encode($arr);
				die;
				
			}
				
	}
	
	die();
?>