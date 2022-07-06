<?php
	require_once('../../common/database.php');

include ('helper.php');	


		
	$apiToken = isset($_GET['apiToken']) ? $_GET['apiToken'] : -1;
	if ($apiToken<0)
	die ('<Error>#1009</Error>');

	$qry = "select * from config_api_n_feeds where 1=1 and APIaccessType='getFTDs'  ";
	$rsc = mysql_query($qry);
	$row = mysql_fetch_assoc($rsc);
	
	if ($apiToken != $row['apiToken'])
	die ('<Error>#1008</Error>');
		
		
		

	
$ip = getip();

$isAffiliateBuddiesOffice=false;

 if ($ip =='31.168.107.219') 
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
	
		
		
		
	
	if ($row['apiAccessType']=='getFTDs' || $row['apiAccessType']=='all') {
		
	
		$where = " where 1=1";
		$qry = "SELECT  `affiliate_id`, `product_id`, `country`, `trader_id`, `merchant_id`, `initialftddate`, `ftdamount` FROM `data_reg`  " . $where;
		
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
			$from = isset($_GET['from']) ? $_GET['from'] : date("Y-m-d", strtotime("-7 days"));
			$startdate = $from;
			$qry .= " and initialftddate between '" . $startdate ."' and '" . date("Y-m-d 23:59:59") . "'";
		}
		
		if ($_GET['debug']==5)
			echo $qry.'<br>';
		
		
		$qqq=mysql_query($qry) or die (mysql_error()); 
		$qqq2= $qqq;
			$counter=0;
		if($row['outputType'] == "XML"){
			header('Content-Type: text/xml');
	
			echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
			echo '<data>'."\n";
			$list="";
			$xml .= '<FTDs>';
					while ($row=mysql_fetch_assoc($qqq)) {
						$counter++;
				var_dump($row);
					$list .= '<ftdRecord>';
							
							foreach($row as $k=>$v)
							{
								$list .= '<'. $k .'>'.$v.'</'. $k .'>';
							}
						$list .= '</ftdRecord>';
					
				}
				
							flush();
				$xml .= $list . '</FTDs>';
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
				
				if (empty($arr))
					die ('No Results');
				echo json_encode($arr);
				die;
		}
	}
	else {
		die ('<Error>404.09</Error>');
	}