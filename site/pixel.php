<?php

//header('Access-Control-Allow-Origin: *');
require_once('func/func_common.php');
require_once('func/func_db.php');
require_once('func/func_debug.php');
require_once('common/database.php');
// require('common/global.php');

// $url = "http://affiliate.wow-partners.com/pixel.php?act=account&ctag=a500-b106-p0-cGB-u92795949001488892212&merchant_id=1&trader_id=3478285&trader_alias=&#116&#101&#115&#116%20&subid=";
// echo $url.'<br><br>';
// $a = firePixel ($url);
// var_dump($a);
// die ();
if ($_GET['clientMode']==1){
	$actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	firePixel($actual_link);
	die();
}

function firePixel($url=""){
	// var_dump($_GET);
	// die();
	if (empty($url) && strpos($url,'pixel.php')===false)
		return "";

	$ex = explode('?',$url,2);
	// var_dump($url);
parse_str($ex[1],$values);

$debug = isset($values['debug']) ? $values['debug'] : 0;
$clientMode = isset($values['clientMode']) ? 1 : 0;
$theTracker = isset($values['ctag']) ? $values['ctag'] :( isset($values['btag']) ? $values['btag'] : 0);
if (empty($theTracker))
	$theTracker = $_GET['ctag'];

$theTracker= clearAscii($theTracker);

$act = isset($values['act']) ? $values['act'] :"";

$trader_id = isset($values['trader_id']) ? $values['trader_id'] :"";
$subid = isset($values['subid']) ? $values['subid'] :"";
$_GET = $values;





// $act = isset($_GET['act']) ? $_GET['act'] : (isset($_POST['act']) ? $_POST['act'] : "" );
// $trader_id = isset($_GET['trader_id']) ? $_GET['trader_id'] : "";


$ctagArray = array();






if (!empty($theTracker)){
	$exp = explode(' ',$theTracker,2);
	$theTracker = $exp[0];
}

$ctagArray = getBtag($theTracker);

$affiliate_id=isset($ctagArray['affiliate_id']) ? $ctagArray['affiliate_id'] : 0;

	if($set->activateLogs==1){
		$ip = getClientIp();
		if(checkUserFirewallIP($ip)){

			
				$activityLogUrl = "http://".$_SERVER['SERVER_NAME']. "/ajax/saveLogActivity.php?user_id=".$affiliate_id."&ip=".$ip."&country=" . $country_id."&location=Pixels&userType=affiliate&theChange=Blocked user trying to enter the system&_function_=".__FUNCTION__ . "&_file_=". __FILE__ . "&queryString=" . urlencode($queryString);
				 //die($activityLogUrl);
				doPost2($activityLogUrl);
			
				
				$url = 'http://'.$_SERVER['SERVER_NAME']."/404.php";
					// die ($url);
					header("Location: ".$url);
				die('--');

		}
	}
	




$banner_id=isset($ctagArray['banner_id']) ? $ctagArray['banner_id'] : 0;

if (strtolower($act)=='sale'){
	$act = 'deposit';
}




// if (!$banner_id=='' && $clientMode==0)
	// $banner_id=0;


// $affiliate_id=substr($exp[0],1); // a
// $banner_id=substr($exp[1],1); // b
if (!$affiliate_id) echo("No Affiliate ID");
//if (!$banner_id) die("No Banner ID");
$getBanner = dbGet($banner_id,"merchants_creative");
$merchant_id = $getBanner['merchant_id'];
if ($merchant_id=='') {
	$merchant_id = $_GET['merchant_id'];
}
$product_id = $getBanner['product_id'];
	
	
	// echo 'mer: ' . $merchant_id ,'<br>';
	// echo 'prod: ' . $product_id ,'<br>';
	// die();

// $DynamicParameter = $ctagArray['freeParam'];
$DynamicParameter = isset($ctagArray['freeParam']) ? $ctagArray['freeParam'] : "";

$DynamicParameter= clearAscii($DynamicParameter);

$DynamicParameter2 = "";
// $DynamicParameter = $banner_id=$ctagArray['freeParam'];

//  new method

//$ctag=$_GET['ctag'];
$profile_id= 0 ;
$country= "";
$uid= 0;
$freeParam="";

if (!empty($ctagArray)){
$profile_id=$ctagArray['profile_id'];
$country=$ctagArray['country'];
$uid=$ctagArray['uid'];
$freeParam=$ctagArray['freeParam'];
}


switch ($act) {
	
		case "account":


	/*
			This pixel can fire the: trader_id, ctag, type
			<iframe src="http://aff.bestforexpartners.com/pixel.php?act=account&ctag=%PARAM%&trader_id=%ID%&trader_alias=%ID%&type=lead" scrolling="no" frameborder="0" width="1" height="1"></iframe>
		*/
		//$getPixel = mysql_fetch_assoc(function_mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='account' LIMIT 1",__FILE__));
		if ($merchant_id>0)
			$qry = "SELECT * FROM pixel_monitor WHERE " . ($clientMode==1 ? ' ' : " method<>'client' and ")."  affiliate_id='".$affiliate_id."' AND merchant_id ='".$merchant_id."' AND valid='1' AND type='account'";
		else
			$qry = "SELECT * FROM pixel_monitor WHERE " . ($clientMode==1 ? ' ' : " method<>'client' and ")."  affiliate_id='".$affiliate_id."' AND product_id='".$product_id."' AND valid='1' AND type='account'";
		
		
		
			
			
		$rslts = (function_mysql_query($qry,__FILE__));
		while($getPixel = mysql_fetch_assoc($rslts)){
			
			if (isset($getPixel['banner_id']) && $getPixel['banner_id']>0){
				if ($banner_id!=$getPixel['banner_id']) // pixel was related to specific banner
					continue;
			}
		
			// var_dump($getPixel);
			// die();
			if ($getPixel['id']) {
				$urls = getUrlsFromHtml(trim($getPixel['pixelCode']));
				$urlFromPixel=!empty($urls[0]) ? ($urls[0]) : "" ;
				
				
				
				if (strpos($getPixel['pixelCode'],'{ip}')>0 || strpos($getPixel['pixelCode'],'{dynamic_parameter2}')>0 || strpos($getPixel['pixelCode'],'{p2}')>0 || strpos($getPixel['pixelCode'],'{dynamic_parameter3}')>0 || strpos($getPixel['pixelCode'],'{p3}')>0 || strpos($getPixel['pixelCode'],'{p4}')>0 || strpos($getPixel['pixelCode'],'{p5}')>0){
					$q = "select ip,param2,param3,param4,param5 from traffic where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; ";
					// die ($q);
					$trafficRow = mysql_fetch_assoc(mysql_query($q));
					$user_ip = $trafficRow['ip'];
					$DynamicParameter2 = $trafficRow['param2'];
					$DynamicParameter3 = $trafficRow['param3'];
                                        $DynamicParameter4 = $trafficRow['param4'];
                                        $DynamicParameter5 = $trafficRow['param5'];
					if (empty($DynamicParameter2) || empty($DynamicParameter3) || empty($DynamicParameter4) || empty($DynamicParameter5)){
						// $q = "select param2 from TrackerConversion where 1=1 and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; ";
						$q = "select freeParam2,freeParam3,freeParam4,freeParam5 from data_reg where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id = '" . $trader_id . "' limit 1; ";
						// die ($q);
					$trafficRow = mysql_fetch_assoc(mysql_query($q					));
					$DynamicParameter2 = empty($DynamicParameter2) ? $trafficRow['freeParam2']: $DynamicParameter2;
					$DynamicParameter3 = empty($DynamicParameter3) ? $trafficRow['freeParam3']: $DynamicParameter3;
					$DynamicParameter4 = empty($DynamicParameter4) ? $trafficRow['freeParam4']: $DynamicParameter4;
                                        $DynamicParameter5 = empty($DynamicParameter5) ? $trafficRow['freeParam5']: $DynamicParameter5;
					}
				}
				

				if (strpos($getPixel['pixelCode'],'{email}')>0){
					$trafficRow = mysql_fetch_assoc(mysql_query("select email from data_reg where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id  = '" . $trader_id . "' limit 1; "));
					$email = $trafficRow['email'];
				}
				
				
				
				
		$find = Array("&#39;","#34;","{email}","{ctag}","{trader_id}","{ip}","{trader_alias}","{type}","{p1}","{p2}","{dynamic_parameter}","{dynamic_parameter2}","{dynamic_parameter3}","{p3}","{affiliate_id}","{uid}","{subid}","{merchant_id}","{product_id}","{p4}","{p5}");
		$replace = Array('"','"',$email,$_GET['param'],$trader_id,$user_ip,$_GET['trader_alias'],$_GET['type'],$DynamicParameter,$DynamicParameter2,$DynamicParameter,$DynamicParameter2,$DynamicParameter3,$DynamicParameter3,$affiliate_id,$uid, $subid,$merchant_id,$product_id,$DynamicParameter4,$DynamicParameter5);
				
				
				$pixelCode=str_replace($find,$replace,trim($getPixel['pixelCode']));
				
				
				function_mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'",__FILE__);
				/* $qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`) VALUES ('".$pixelCode."',".$getPixel['id'].")";
				function_mysql_query ($qry,__FILE__); */
				
				if (filter_var($pixelCode, FILTER_VALIDATE_URL)){
	
					if ($debug==2) {
						echo $pixelCode .'<br>';
					}
				
					if ($getPixel['method']=='get')
					$resp =  doGet($pixelCode);
				else
					$resp =  doPostPixel($pixelCode);
					
					if ($debug==2) echo $resp;
				}
				else if (filter_var($urlFromPixel, FILTER_VALIDATE_URL)) {
								
								if ($getPixel['method']=='get')
									$resp =  doGet($urlFromPixel);
								else
								$resp = doPostPixel($urlFromPixel);
					
				
				
							if ($debug==2) echo '<br>in pixel (extracted: '.$resp.')<br>';
								
				
				}else
				{
					if ($debug==2) echo '<br>in pixel (echo)<br>';
								
								echo $getPixel['pixelCode'];

				}
				
				$resp = mysql_real_escape_string(clearAscii($resp));
				
				$qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`tracker`,`pixelResponse`) VALUES ('".$pixelCode."',".$getPixel['id'].",'".$theTracker."','".$resp."')";
				if ($debug==5)
					die ($qry);
				
				function_mysql_query ($qry,__FILE__);
				
				
			}
		}
		break;
		
		
		case "lead":
		
		/*
			This pixel can fire the: trader_id, ctag, type
			<iframe src="http://aff.bestforexpartners.com/pixel.php?act=account&ctag=%PARAM%&trader_id=%ID%&trader_alias=%ID%&type=lead" scrolling="no" frameborder="0" width="1" height="1"></iframe>
		*/
		//$getPixel = mysql_fetch_assoc(function_mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='account' LIMIT 1",__FILE__));
		if ($merchant_id>0)
			$qry = "SELECT * FROM pixel_monitor WHERE  " . ($clientMode==1 ? ' ' : " method<>'client' and ")." affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='lead'";
		else
			$qry = "SELECT * FROM pixel_monitor WHERE  " . ($clientMode==1 ? ' ' : " method<>'client' and ")." affiliate_id='".$affiliate_id."' AND product_id='".$product_id."' AND valid='1' AND type='lead'";

		// echo $qry . '<Br>';
		$rslts = (function_mysql_query($qry,__FILE__));
		while($getPixel = mysql_fetch_assoc($rslts)){
			
			if (isset($getPixel['banner_id']) && $getPixel['banner_id']>0){
				if ($banner_id!=$getPixel['banner_id']) // pixel was related to specific banner
					continue;
			}
		
			
			if ($getPixel['id']) {
				$urls = getUrlsFromHtml(trim($getPixel['pixelCode']));
				$urlFromPixel=!empty($urls[0]) ? ($urls[0]) : "" ;
				
				if (strpos($getPixel['pixelCode'],'{ip}')>0 ||strpos($getPixel['pixelCode'],'{dynamic_parameter2}')>0 || strpos($getPixel['pixelCode'],'{p2}')>0 || strpos($getPixel['pixelCode'],'{dynamic_parameter3}')>0 || strpos($getPixel['pixelCode'],'{p3}')>0 || strpos($getPixel['pixelCode'],'{p4}')>0 || strpos($getPixel['pixelCode'],'{p5}')>0){
					$trafficRow = mysql_fetch_assoc(mysql_query("select ip,param2,param3,param4,param5 from traffic where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; "));
					$user_ip = $trafficRow['ip'];
					$DynamicParameter2 = $trafficRow['param2'];
					$DynamicParameter3 = $trafficRow['param3'];
                                        $DynamicParameter4 = $trafficRow['param4'];
                                        $DynamicParameter5 = $trafficRow['param5'];
					if (empty($DynamicParameter2) || empty($DynamicParameter3) || empty($DynamicParameter4) || empty($DynamicParameter5)){
						// $q = "select param2 from TrackerConversion where 1=1 and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; ";
						$q = "select freeParam2,freeParam3,freeParam4,freeParam5 from data_reg where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id = '" . $trader_id . "' limit 1; ";
						// die ($q);
					$trafficRow = mysql_fetch_assoc(mysql_query($q					));
					$DynamicParameter2 = empty($DynamicParameter2) ? $trafficRow['freeParam2']: $DynamicParameter2;
					$DynamicParameter3 = empty($DynamicParameter3) ? $trafficRow['freeParam3']: $DynamicParameter3;
					$DynamicParameter4 = empty($DynamicParameter4) ? $trafficRow['freeParam4']: $DynamicParameter4;
                                        $DynamicParameter5 = empty($DynamicParameter5) ? $trafficRow['freeParam5']: $DynamicParameter5;
					}
				}
				if (strpos($getPixel['pixelCode'],'{email}')>0){
					$trafficRow = mysql_fetch_assoc(mysql_query("select email from data_reg where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id  = '" . $trader_id . "' limit 1; "));
					$email = $trafficRow['email'];
				}
				
				//it has to be after dynamic parameter 2 thing
		$find = Array("&#39;","#34;","{email}","{ctag}","{trader_id}","{ip}","{trader_alias}","{type}","{dynamic_parameter}","{dynamic_parameter2}","{p1}","{p2}","{dynamic_parameter3}","{p3}","{affiliate_id}","{uid}","{subid}","{merchant_id}","{product_id}","{p4}","{p5}");
		$replace = Array('"','"',$email,$_GET['param'],$trader_id,$user_ip,$_GET['trader_alias'],$_GET['type'],$DynamicParameter,$DynamicParameter2,$DynamicParameter,$DynamicParameter2,$DynamicParameter3,$DynamicParameter3,$affiliate_id,$uid, $subid,$merchant_id,$product_id,$DynamicParameter4,$DynamicParameter5);
				
				
				$pixelCode=str_replace($find,$replace,trim($getPixel['pixelCode']));
				function_mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'",__FILE__);
				/* $qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`) VALUES ('".$pixelCode."',".$getPixel['id'].")";
				function_mysql_query ($qry,__FILE__); */
				
				if (filter_var($pixelCode, FILTER_VALIDATE_URL)){
	//				echo doPost2($pixelCode);
					if ($getPixel['method']=='get')
						$resp =  doGet($pixelCode);
					else
						$resp =  doPostPixel($pixelCode);
						
					if ($debug==2) echo $resp;
				}
				else if (filter_var($urlFromPixel, FILTER_VALIDATE_URL)) {
								
							if ($getPixel['method']=='get')
								$resp = doGet($urlFromPixel);
							else
								$resp = doPostPixel($urlFromPixel);
							
							if ($debug==2) echo '<br>in pixel (extracted: '.$resp.')<br>';
								
				
				}else
				{
					if ($debug==2) echo '<br>in pixel (echo)<br>';
								
								
								echo $getPixel['pixelCode'];

				}
				// $qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`pixelResponse`) VALUES ('".$pixelCode."',".$getPixel['id'].",'".$resp."')";
				$resp = mysql_real_escape_string(clearAscii($resp));
				$qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`tracker`,`pixelResponse`) VALUES ('".$pixelCode."',".$getPixel['id'].",'".$theTracker."','".$resp."')";
				function_mysql_query ($qry,__FILE__);
				
			}
		}
		break;
		
	
	
	case "install":

	

	/*
			This pixel can fire the: trader_id, ctag, type
			<iframe src="http://aff.bestforexpartners.com/pixel.php?act=account&ctag=%PARAM%&trader_id=%ID%&trader_alias=%ID%&type=lead" scrolling="no" frameborder="0" width="1" height="1"></iframe>
		*/
		//$getPixel = mysql_fetch_assoc(function_mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='account' LIMIT 1",__FILE__));
		if ($merchant_id>0)
			$qry = "SELECT * FROM pixel_monitor WHERE " . ($clientMode==1 ? ' ' : " method<>'client' and ")."  affiliate_id='".$affiliate_id."' AND merchant_id ='".$merchant_id."' AND valid='1' AND type='install'";
		else
			$qry = "SELECT * FROM pixel_monitor WHERE " . ($clientMode==1 ? ' ' : " method<>'client' and ")."  affiliate_id='".$affiliate_id."' AND product_id='".$product_id."' AND valid='1' AND type='install'";
		
		
		
			
			
		$rslts = (function_mysql_query($qry,__FILE__));
		while($getPixel = mysql_fetch_assoc($rslts)){
			
			if (isset($getPixel['banner_id']) && $getPixel['banner_id']>0){
				if ($banner_id!=$getPixel['banner_id']) // pixel was related to specific banner
					continue;
			}
		
			// var_dump($getPixel);
			// die();
			if ($getPixel['id']) {
				$urls = getUrlsFromHtml(trim($getPixel['pixelCode']));
				$urlFromPixel=!empty($urls[0]) ? ($urls[0]) : "" ;
				
				
				
				if (strpos($getPixel['pixelCode'],'{ip}')>0 || strpos($getPixel['pixelCode'],'{dynamic_parameter2}')>0 || strpos($getPixel['pixelCode'],'{p2}')>0 || strpos($getPixel['pixelCode'],'{dynamic_parameter3}')>0 || strpos($getPixel['pixelCode'],'{p3}')>0 || strpos($getPixel['pixelCode'],'{p4}')>0 || strpos($getPixel['pixelCode'],'{p5}')>0){
					$q = "select ip,param2,param3,param4,param5 from traffic where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; ";
					// die ($q);
					$trafficRow = mysql_fetch_assoc(mysql_query($q));
					$user_ip = $trafficRow['ip'];
					$DynamicParameter2 = $trafficRow['param2'];
					$DynamicParameter3 = $trafficRow['param3'];
                                        $DynamicParameter4 = $trafficRow['param4'];
                                        $DynamicParameter5 = $trafficRow['param5'];
					if (empty($DynamicParameter2) || empty($DynamicParameter3) || empty($DynamicParameter4) || empty($DynamicParameter5)){
						// $q = "select param2 from TrackerConversion where 1=1 and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; ";
						$q = "select freeParam2,freeParam3,freeParam4,freeParam5 from data_install where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id = '" . $trader_id . "' limit 1; ";
						// die ($q);
					$trafficRow = mysql_fetch_assoc(mysql_query($q					));
					$DynamicParameter2 = empty($DynamicParameter2) ? $trafficRow['freeParam2']: $DynamicParameter2;
					$DynamicParameter3 = empty($DynamicParameter3) ? $trafficRow['freeParam3']: $DynamicParameter3;
					$DynamicParameter4 = empty($DynamicParameter4) ? $trafficRow['freeParam4']: $DynamicParameter4;
                                        $DynamicParameter5 = empty($DynamicParameter5) ? $trafficRow['freeParam5']: $DynamicParameter5;
					
					}
				}
				

				if (strpos($getPixel['pixelCode'],'{email}')>0){
					$trafficRow = mysql_fetch_assoc(mysql_query("select email from data_install where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id  = '" . $trader_id . "' limit 1; "));
					$email = $trafficRow['email'];
				}
				
				
				
				
		$find = Array("&#39;","#34;","{email}","{ctag}","{trader_id}","{ip}","{trader_alias}","{type}","{p1}","{p2}","{dynamic_parameter}","{dynamic_parameter2}","{dynamic_parameter3}","{p3}","{affiliate_id}","{uid}","{subid}","{merchant_id}","{product_id}","{p4}","{p5}");
		$replace = Array('"','"',$email,$_GET['param'],$trader_id,$user_ip,$_GET['trader_alias'],$_GET['type'],$DynamicParameter,$DynamicParameter2,$DynamicParameter,$DynamicParameter2,$DynamicParameter3,$DynamicParameter3,$affiliate_id,$uid, $subid,$merchant_id,$product_id,$DynamicParameter4,$DynamicParameter5);
				
				
				$pixelCode=str_replace($find,$replace,trim($getPixel['pixelCode']));
				
				
				function_mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'",__FILE__);
				/* $qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`) VALUES ('".$pixelCode."',".$getPixel['id'].")";
				function_mysql_query ($qry,__FILE__); */
				
				if (filter_var($pixelCode, FILTER_VALIDATE_URL)){
	
					if ($debug==2) {
						echo $pixelCode .'<br>';
					}
				
					if ($getPixel['method']=='get')
					$resp =  doGet($pixelCode);
				else
					$resp =  doPostPixel($pixelCode);
					
					if ($debug==2) echo $resp;
				}
				else if (filter_var($urlFromPixel, FILTER_VALIDATE_URL)) {
								
								if ($getPixel['method']=='get')
									$resp =  doGet($urlFromPixel);
								else
								$resp = doPostPixel($urlFromPixel);
					
				
				
							if ($debug==2) echo '<br>in pixel (extracted: '.$resp.')<br>';
								
				
				}else
				{
					if ($debug==2) echo '<br>in pixel (echo)<br>';
								
								echo $getPixel['pixelCode'];

				}
				
				$resp = mysql_real_escape_string(clearAscii($resp));
				
				$qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`tracker`,`pixelResponse`) VALUES ('".$pixelCode."',".$getPixel['id'].",'".$theTracker."','".$resp."')";
				if ($debug==5)
					die ($qry);
				
				function_mysql_query ($qry,__FILE__);
				
				
			}
		}
		break;
		
		
		case "lead":
		
		/*
			This pixel can fire the: trader_id, ctag, type
			<iframe src="http://aff.bestforexpartners.com/pixel.php?act=account&ctag=%PARAM%&trader_id=%ID%&trader_alias=%ID%&type=lead" scrolling="no" frameborder="0" width="1" height="1"></iframe>
		*/
		//$getPixel = mysql_fetch_assoc(function_mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='account' LIMIT 1",__FILE__));
		if ($merchant_id>0)
			$qry = "SELECT * FROM pixel_monitor WHERE  " . ($clientMode==1 ? ' ' : " method<>'client' and ")." affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='lead'";
		else
			$qry = "SELECT * FROM pixel_monitor WHERE  " . ($clientMode==1 ? ' ' : " method<>'client' and ")." affiliate_id='".$affiliate_id."' AND product_id='".$product_id."' AND valid='1' AND type='lead'";

		// echo $qry . '<Br>';
		$rslts = (function_mysql_query($qry,__FILE__));
		while($getPixel = mysql_fetch_assoc($rslts)){
			
			if (isset($getPixel['banner_id']) && $getPixel['banner_id']>0){
				if ($banner_id!=$getPixel['banner_id']) // pixel was related to specific banner
					continue;
			}
		
			
			if ($getPixel['id']) {
				$urls = getUrlsFromHtml(trim($getPixel['pixelCode']));
				$urlFromPixel=!empty($urls[0]) ? ($urls[0]) : "" ;
				
				if (strpos($getPixel['pixelCode'],'{ip}')>0 ||strpos($getPixel['pixelCode'],'{dynamic_parameter2}')>0 || strpos($getPixel['pixelCode'],'{p2}')>0 || strpos($getPixel['pixelCode'],'{dynamic_parameter3}')>0 || strpos($getPixel['pixelCode'],'{p3}')>0 || strpos($getPixel['pixelCode'],'{p4}')>0 || strpos($getPixel['pixelCode'],'{p5}')>0){
					$trafficRow = mysql_fetch_assoc(mysql_query("select ip,param2,param3,param4,param5 from traffic where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; "));
					$user_ip = $trafficRow['ip'];
					$DynamicParameter2 = $trafficRow['param2'];
					$DynamicParameter3 = $trafficRow['param3'];
                                        $DynamicParameter4 = $trafficRow['param4'];
                                        $DynamicParameter5 = $trafficRow['param5'];
					if (empty($DynamicParameter2) || empty($DynamicParameter3)){
						// $q = "select param2 from TrackerConversion where 1=1 and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; ";
						$q = "select freeParam2,freeParam3,freeParam4,freeParam5 from data_reg where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id = '" . $trader_id . "' limit 1; ";
						// die ($q);
					$trafficRow = mysql_fetch_assoc(mysql_query($q					));
					$DynamicParameter2 = empty($DynamicParameter2) ? $trafficRow['freeParam2']: $DynamicParameter2;
					$DynamicParameter3 = empty($DynamicParameter3) ? $trafficRow['freeParam3']: $DynamicParameter3;
					$DynamicParameter4 = empty($DynamicParameter4) ? $trafficRow['freeParam4']: $DynamicParameter4;
                                        $DynamicParameter5 = empty($DynamicParameter5) ? $trafficRow['freeParam5']: $DynamicParameter5;
					}
				}
				if (strpos($getPixel['pixelCode'],'{email}')>0){
					$trafficRow = mysql_fetch_assoc(mysql_query("select email from data_reg where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id  = '" . $trader_id . "' limit 1; "));
					$email = $trafficRow['email'];
				}
				
				//it has to be after dynamic parameter 2 thing
		$find = Array("&#39;","#34;","{email}","{ctag}","{trader_id}","{ip}","{trader_alias}","{type}","{dynamic_parameter}","{dynamic_parameter2}","{p1}","{p2}","{dynamic_parameter3}","{p3}","{affiliate_id}","{uid}","{subid}","{merchant_id}","{product_id}","{p4}","{p5}");
		$replace = Array('"','"',$email,$_GET['param'],$trader_id,$user_ip,$_GET['trader_alias'],$_GET['type'],$DynamicParameter,$DynamicParameter2,$DynamicParameter,$DynamicParameter2,$DynamicParameter3,$DynamicParameter3,$affiliate_id,$uid, $subid,$merchant_id,$product_id,$DynamicParameter4,$DynamicParameter5);
				
				
				$pixelCode=str_replace($find,$replace,trim($getPixel['pixelCode']));
				function_mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'",__FILE__);
				/* $qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`) VALUES ('".$pixelCode."',".$getPixel['id'].")";
				function_mysql_query ($qry,__FILE__); */
				
				if (filter_var($pixelCode, FILTER_VALIDATE_URL)){
	//				echo doPost2($pixelCode);
					if ($getPixel['method']=='get')
						$resp =  doGet($pixelCode);
					else
						$resp =  doPostPixel($pixelCode);
						
					if ($debug==2) echo $resp;
				}
				else if (filter_var($urlFromPixel, FILTER_VALIDATE_URL)) {
								
							if ($getPixel['method']=='get')
								$resp = doGet($urlFromPixel);
							else
								$resp = doPostPixel($urlFromPixel);
							
							if ($debug==2) echo '<br>in pixel (extracted: '.$resp.')<br>';
								
				
				}else
				{
					if ($debug==2) echo '<br>in pixel (echo)<br>';
								
								
								echo $getPixel['pixelCode'];

				}
				// $qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`pixelResponse`) VALUES ('".$pixelCode."',".$getPixel['id'].",'".$resp."')";
				$resp = mysql_real_escape_string(clearAscii($resp));
				$qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`tracker`,`pixelResponse`) VALUES ('".$pixelCode."',".$getPixel['id'].",'".$theTracker."','".$resp."')";
				function_mysql_query ($qry,__FILE__);
				
			}
		}
		break;
		
		
	
	case "deposit":
		/*
			This pixel can fire the: trader_id, ctag, amount, currency, tranz
			<iframe src="http://aff.bestforexpartners.com/pixel.php?act=deposit&ctag=%PARAM%&trader_id=%ID%&tranz=%TRANSACTIONID%&type=deposit&currency=USD&amount=%DEPOSITAMOUNT%" scrolling="no" frameborder="0" width="1" height="1"></iframe>
		*/
		
		//$getPixel = mysql_fetch_assoc(function_mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='sale' LIMIT 1",__FILE__));
		if ($merchant_id>0)
		$rslts = (function_mysql_query("SELECT * FROM pixel_monitor WHERE  " . ($clientMode==1 ? ' ' : " method<>'client' and ")." affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='sale'",__FILE__));
		else
		$rslts = (function_mysql_query("SELECT * FROM pixel_monitor WHERE  " . ($clientMode==1 ? ' ' : " method<>'client' and ")." affiliate_id='".$affiliate_id."' AND product_id='".$product_id."' AND valid='1' AND type='sale'",__FILE__));
		
		while($getPixel = mysql_fetch_assoc($rslts)){
			
			
			if (isset($getPixel['banner_id']) && $getPixel['banner_id']>0){
				if ($banner_id!=$getPixel['banner_id']) // pixel was related to specific banner
					continue;
			}
			
			
			if ($getPixel['id']) {
			
			
			if (strpos($getPixel['pixelCode'],'{ip}')>0 || strpos($getPixel['pixelCode'],'{dynamic_parameter2}')>0 || strpos($getPixel['pixelCode'],'{p2}')>0 || strpos($getPixel['pixelCode'],'{dynamic_parameter3}')>0 || strpos($getPixel['pixelCode'],'{p3}')>0 || strpos($getPixel['pixelCode'],'{p4}')>0 || strpos($getPixel['pixelCode'],'{p5}')>0){
					$q = "select ip,param2,param3,param4,param5 from traffic where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; ";
					$trafficRow = mysql_fetch_assoc(mysql_query($q					));
					$DynamicParameter3 = $trafficRow['param3'];
					$user_ip = $trafficRow['ip'];
					$DynamicParameter2 = $trafficRow['param2'];
                                        $DynamicParameter4 = $trafficRow['param4'];
                                        $DynamicParameter5 = $trafficRow['param5'];
					if (empty($DynamicParameter2) || empty($DynamicParameter3)){
						// $q = "select param2 from TrackerConversion where 1=1 and affiliate_id = " . $affiliate_id . " and uid = '" . $uid . "' limit 1; ";
						$q = "select freeParam2,freeParam3,freeParam4,freeParam5 from data_reg where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id = '" . $trader_id . "' limit 1; ";
						// die ($q);
					$trafficRow = mysql_fetch_assoc(mysql_query($q					));
					$DynamicParameter2 = empty($DynamicParameter2) ? $trafficRow['freeParam2']: $DynamicParameter2;
					$DynamicParameter3 = empty($DynamicParameter3) ? $trafficRow['freeParam3']: $DynamicParameter3;
					$DynamicParameter4 = empty($DynamicParameter4) ? $trafficRow['freeParam4']: $DynamicParameter4;
                                        $DynamicParameter5 = empty($DynamicParameter5) ? $trafficRow['freeParam5']: $DynamicParameter5;
					}
				}
				
			
				if (strpos($getPixel['pixelCode'],'{email}')>0){
					$trafficRow = mysql_fetch_assoc(mysql_query("select email from data_reg where merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id . " and trader_id  = '" . $trader_id . "' limit 1; "));
					$email = $trafficRow['email'];
				}
				
				//it has to be after dynamic parameter 2 thing
		$find = Array("&#39;", "#34;","{email}","{ctag}","{ip}","{trader_id}","{tranz}","{type}","{currency}","{amount}","{dynamic_parameter3}","{p3}","{dynamic_parameter}","{dynamic_parameter2}","{p1}","{p2}","{affiliate_id}","{uid}","{subid}","{merchant_id}","{product_id}","{p4}","{p5}");
		$replace = Array('"','"',$email,$theTracker,$user_ip,$trader_id,$_GET['tranz'],$_GET['type'],$_GET['currency'],$_GET['amount'],$DynamicParameter3,$DynamicParameter3,$DynamicParameter,$DynamicParameter2,$DynamicParameter,$DynamicParameter2,$affiliate_id,$uid,$subid,$merchant_id,$product_id,$DynamicParameter4,$DynamicParameter5);
		
			
			function_mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'",__FILE__);
				
				$pixelCode=str_replace($find,$replace,trim($getPixel['pixelCode']));
			if ($debug==1){
				echo 'dyn2: ' . $DynamicParameter2 .'<Br>';
				echo 'dyn3: ' . $DynamicParameter3 .'<Br>';
				echo $getPixel['pixelCode'].'<Br>';
				var_dump($pixelCode);
				echo '<Br>';
			}
				/* if (filter_var($pixelCode, FILTER_VALIDATE_URL))
					echo doGet($pixelCode);
				else
					echo $pixelCode; */
				
				if (filter_var($pixelCode, FILTER_VALIDATE_URL)){
	//				echo doPost($pixelCode);
						if ($getPixel['method']=='get')
					$resp =  doGet($pixelCode);
						else
					$resp =  doPostPixel($pixelCode);
							
					if ($debug==2) echo $resp;
				}
				else if (filter_var($urlFromPixel, FILTER_VALIDATE_URL)) {
								
							if ($getPixel['method']=='get')
							$resp = doGet($urlFromPixel);
							else	
							$resp = doPostPixel($urlFromPixel);
							if ($debug==2) echo '<br>in pixel (extracted: '.$resp.')<br>';
								
				
				}else
				{
					if ($debug==2) echo '<br>in pixel (echo)<br>';
								
								echo $getPixel['pixelCode'];

				}
				$resp = mysql_real_escape_string(clearAscii($resp));
				// $qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`pixelResponse`) VALUES ('".$pixelCode."',".$getPixel['id'].",'".$resp."')";
				$qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`tracker`,`pixelResponse`) VALUES ('".$pixelCode."',".$getPixel['id'].",'".$theTracker."','".$resp."')";
				function_mysql_query ($qry,__FILE__);
				
				
				
			}
		}
		break;

	default:
	echo ('');//	_goto();
	break;
		
	}


}

	function doGet($url){
	
	$ch = curl_init();  
	//echo '<BR>'.$url.'<BR>';
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  "Content-Type: application/json; charset=utf-8"
	));
	
    $output=curl_exec($ch);
	
	if($output === false){
		//echo 'Curl error: ' . curl_error($ch);
	}
 
    curl_close($ch);
    return $output;
	
}

function doPostPixel($url){
		$parse_url=parse_url($url);
		$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
		
		if (!$da) {
			//echo "$errstr ($errno)<br/>\n";
			//echo $da;
			} else {
			$response ="";
			$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
			$params .= "Host: ".$parse_url['host']."\r\n";
			$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$params .= "User-Agent: AffiliateTS Agent\r\n";
			$params .= "Content-Length: ".strlen($parse_url['query'])."\r\n";
			$params .= "Connection: close\r\n\r\n";
			$params .= $parse_url['query'];
			fputs($da, $params);
			while (!feof($da)) $response .= fgets($da);
			fclose($da);
			
			// split the result header from the content
			
			$result = explode("\r\n\r\n", $response, 2);
			$content = isset($result[0]) ? $result[0] : "";
			if ($content=="")
			return "-01";
		
			$contentStatus = explode("\r\n", $content, 2);
			$content = isset($contentStatus[0]) ? $contentStatus[0] : "";
				if ($content=="")
			return "-02";
		
			$contentStatus = explode(" ", $content, 2);
			$content = isset($contentStatus[1]) ? $contentStatus[1] :  "";
				if ($content=="")
			return "-03";
		
			return $content;
			}
		}

function doPost2($url){
	$parse_url=parse_url($url);
	$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
	if (!$da) {
		
		echo "$errstr ($errno)<br/>\n";
		echo $da;
		} else {
		//die($url);
		$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
		$params .= "Host: ".$parse_url['host']."\r\n";
		$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$params .= "User-Agent: ".$set->webTitle." Agent\r\n";
		$params .= "Content-Length: ".strlen($parse_url['query'])."\r\n";
		$params .= "Connection: close\r\n\r\n";
		$params .= $parse_url['query'];
		fputs($da, $params);
		while (!feof($da)) $response .= fgets($da);
		fclose($da);
		
//var_dump($response);
//die();
		// split the result header from the content
		$result = explode("\r\n\r\n", $response, 2);
		
		$content = isset($result[1]) ? $result[1] : '';
		//die ($content);
		return $content;
		
	}
}

