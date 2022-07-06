<?php
	require_once('../../common/database.php');

include ('helper.php');	
	
	
	$apiToken = isset($_GET['apiToken']) ? $_GET['apiToken'] : -1;
	if ($apiToken<0)
	die ('<Error>#1009</Error>');

	$qry = "select * from config_api_n_feeds where 1=1 and APIaccessType='merchants'  ";
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
	
	
		
		
		
	
	if ($row['apiAccessType']=='merchants' || $row['apiAccessType']=='all') {
		
	
		
		$qry = "select id,producttype,valid,name,email from merchants;";
		$qqq=mysql_query($qry); 
			$counter=0;
		$list="";
		
	if($row['outputType'] == "XML"){
		header('Content-Type: text/xml');
	
		echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
		echo '<data>'."\n";
			$data .= '<Merchants>';
					while ($row=mysql_fetch_assoc($qqq)) {
						$counter++;
				
					$list .= '<merchantInfo>';
							$list .= '<MerchantID>'.$row['id'].'</MerchantID>';
							$list .= '<name>'. htmlspecialchars($row['name']).'</name>';
							$list .= '<type>'.htmlspecialchars($row['producttype']).'</type>';
							// $list .= '<mail>'.htmlspecialchars($row['mail']).'</mail>';
							$list .= '<status>'.($row['valid']==1 ? true : false).'</status>';
						$list .= '</merchantInfo>';
					
				}
				
							flush();
				$data .= $list . '</Merchants>';
			mysql_close();
			echo ($data);
			echo '</data>'."\n";
			die;
	}
	else{
			$arr = array();
			while ($row=mysql_fetch_assoc($qqq)) {
				array_push($arr,$row);
			}
			
			echo json_encode($arr);
			die();
	}
	
	}
	
	else {
		die ('<Error>404.09</Error>');
	}