<?php
	require_once('../../common/database.php');
	require_once('../../func/func_string.php');

	
$debug = isset($_GET['debug']) ? $_GET['debug'] : false;

include ('helper.php');	
$ip = getip();




	

	
	$apiToken = isset($_GET['apiToken']) ? $_GET['apiToken'] : -1;
	if ($apiToken<0)
	die ('<Error>#1009</Error>');

	$qry = "select * from config_api_n_feeds where 1=1 and APIaccessType='affiliates'  ";
	$rsc = mysql_query($qry);
	$row = mysql_fetch_assoc($rsc);
	
	if ($apiToken != $row['apiToken'])
	die ('<Error>#1008</Error>');
		
		

$isAffiliateBuddiesOffice=false;
if ($ip =='62.219.229.235')
	$isAffiliateBuddiesOffice=true;	

	/* if (($row['apiStaticIP']!=$ip || strlen($ip)==0 || empty ($row['apiStaticIP'])) && !$isAffiliateBuddiesOffice) {
		
		die ('error: 1011');
	} */

	if ($debug){
			$log1 = 'ip: ' . $ip.'<br>';
			echo $log1;
			writeToLog($log1);
			$log2= 'apiStaticIP: ' . $row['apiStaticIP'].'<br>';
			echo $log2;
			writeToLog($log2);
	}
	

	$check_A = false===checkIPInList($ip,$row['apiStaticIP']);
	$check_B = strlen($ip)==0 ;
	$check_C = empty ($row['apiStaticIP']) ;
	$check_D = !$isAffiliateBuddiesOffice;
		if ($debug){
			
			echo 'check_a : ' . $check_A .'<br>';
			echo 'check_b : ' . $check_B .'<br>';
			echo 'check_c : ' . $check_C .'<br>';
			echo 'check_d : ' . $check_D .'<br>';
			
			
			writeToLog('check_a : ' . $check_A .'<br>');
			writeToLog('check_b : ' . $check_B .'<br>');
			writeToLog('check_c : ' . $check_C .'<br>');
			writeToLog('check_d : ' . $check_D .'<br>');
			
		}
		
		
	if (($check_A || $check_B || $check_C) && $check_D) {
				die ('error: 1011');
	}

	
	if ($row['apiAccessType']=='None' || $row['apiAccessType']=='') {
		die ('<Error>1012: No permissions to view modules</Error>');
	}
	
	if ($row['status']==0) {
		die ('<Error>Feed is not active.</Error>');
	}
	
	set_time_limit(0);
	
		
		
		
	
	if ($row['apiAccessType']=='affiliates' || $row['apiAccessType']=='all') {
		
	
		
		$qry = "select id,valid,first_name,username,mail from affiliates;";
		$qqq=mysql_query($qry); 
		
		if($row['outputType'] == "XML"){
			header('Content-Type: text/xml');
	
	echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
	echo '<data>'."\n";
			$counter=0;
		$list="";
	$xml .= '<Affiliates>';
			while ($row=mysql_fetch_assoc($qqq)) {
				$counter++;
		
			$list .= '<affiliateInfo>';
					$list .= '<AffiliateID>'.$row['id'].'</AffiliateID>';
					$list .= '<first_name>'. htmlspecialchars($row['first_name']).'</first_name>';
					$list .= '<username>'.htmlspecialchars($row['username']).'</username>';
					// $list .= '<mail>'.htmlspecialchars($row['mail']).'</mail>';
					$list .= '<status>'.($row['valid']==1 ? true : false).'</status>';
				$list .= '</affiliateInfo>';
			
		}
		
		flush();
		$xml .= $list . '</Affiliates>';
		mysql_close();
		echo ($xml);
		echo '</data>'."\n";
		die();
		}
		else{
			$arr = array();
			while ($row=mysql_fetch_assoc($qqq)) {
				array_push($arr,$row);
			}
			mysql_close();
			echo json_encode($arr);
			die;
		}
		
	}
	else {
		die ('<Error>404.09</Error>');
	}