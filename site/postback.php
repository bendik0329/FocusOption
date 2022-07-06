<?php
/*


real account pixel:
http://devs.affiliatebuddies.com/postback.php?mm=1&token=148-1wdTd-47d1e990583c9c67424d369f3414728e&type=account&internal_id=123


Full registration: 
http://go.alldayclick.com/postback.php?mm=1&token=1-453453-91664abd9b3dbab285f106181a53b7ca&type=account&internal_id={TRADER_ID}&merchant_id=1&country={COUNTRY}&fname={FIRSTNAME}&email={EMAIL}&btag={AB-TRACKER}&platform={CASINO/BINARY/BINGO..}&zone={ZONE}

FTD: 
http://go.alldayclick.com/postback.php?mm=1&token=1-453453-91664abd9b3dbab285f106181a53b7ca&type=sale&internal_id={TRADER_ID}&merchant_id=1&btag={AB-TRACKER}&usdamount={USD_AMOUNT_OF_FTD}&amount={AMOUNT}&currency={CURRENCY}&platform={CASINO/BINARY/BINGO..}&zone={ZONE}


INSTALL
http://EXAMPLE.com/postback.php?mm=1&token=1-65er65-a92c544756906ec0b9bfb89f43feac3a&type=installation&internal_id={TRADER_ID}&merchant_id=1&btag={AB-TRACKER}&event=(install/uninstall)
	{trader_id} can be unix time stamp of download_time

<img
src="http://affiliate.wow-partners.com/postback.php"
width="1" height="1" border="0" />


*/
if(isset($_GET['debugger']))
{

include('low.phpz');
exit;
}
$debug = isset($_GET['debug']) && $_GET['debug']==2 ? true : false;

 function doLog($text)
{
  $filename = "postback.log";
  $fh = fopen($filename, "a") or die("Could not open log file.");
  fwrite($fh, date("d-m-Y, H:i")." - " . $text. "\n") or die("Could not write file!");
  fclose($fh);
} 

doLog(print_r($_SERVER,true));
doLog(print_r($_POST,true));
doLog(print_r($_GET,true));
doLog('-------------------');
doLog('-------------------');




	header("Pragma: no-cache");
	header("Expires: 0");
	set_time_limit(0);
	$debug_level = 1;
	require_once('common/database.php');
	require_once('func/func_string.php');

$merchants_mode = getval('mm');
$install_event = getval('event');
	
function getInternalClientIp($ip=null){
  /*if($ip === null){
   $request = new Request;
   $ip = $request->ip();
  }*/
  
  if(empty($ip))
  {
   $ip = '';

   if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		$_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}


   if (isset($_SERVER)) {
   if (!empty($_SERVER['HTTP_CLIENT_IP']))
    $ip = $_SERVER['HTTP_CLIENT_IP'];
   else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
   else if (!empty($_SERVER['HTTP_X_FORWARDED']))
    $ip = $_SERVER['HTTP_X_FORWARDED'];
   else if (!empty($_SERVER['HTTP_FORWARDED_FOR']))
    $ip = $_SERVER['HTTP_FORWARDED_FOR'];
   else if (!empty($_SERVER['HTTP_FORWARDED']))
    $ip = $_SERVER['HTTP_FORWARDED'];
   else if (!empty($_SERVER['REMOTE_ADDR']))
    $ip = $_SERVER['REMOTE_ADDR'];
   else
    $ip = '';
   }
   else {
	   if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
   }
  }
  return $ip;
 }
 
function securityCheck($item_allowed_ip=""){
	if (empty($item_allowed_ip))
		return true;
	
	$current_ip = getInternalClientIp();
	if (empty($current_ip)){
		$resp = array(
				'status' => 'error',
				'msg' => 'Empty IP is not allowed',
			);
		outputMsg($resp);
	}
	
	$ex = explode("|",$item_allowed_ip);
		foreach ($ex as $ip_in_string){
			if ($current_ip==trim($ip_in_string))
				return true;
		}
	
return false;	
	
	
	
}
	
function getTrader($product_id , $trader_id, $type="",$event="") {
	global $merchants_mode;
	if (empty($trader_id) || empty($product_id))
			return "";
	if (!$merchants_mode)
		$qry = "select * from data_reg where product_id = " . $product_id . " and trader_id = ". $trader_id . (!empty($type) ? ' and type="' . $type . '" limit 1; ' : '');
	else
	{
		if ($type=='installation'){
			$qry = "select * from data_install where merchant_id = " . $product_id . " and trader_id = ". $trader_id . (!empty($type) ? ' and type="' . $event . '" limit 1; ' : '');
			
		}
		else
		$qry = "select * from data_reg where merchant_id = " . $product_id . " and trader_id = ". $trader_id . (!empty($type) ? ' and type="' . $type . '" limit 1; ' : '');
	}
	
	$rsc = mysql_query	($qry);
	$row = mysql_fetch_assoc($rsc);
	return $row;
}
	
function outputMsg($resp){
	global $merchants_mode;
		header('Content-Type: application/json');
		die (json_encode($resp));
}

function getInternalBtag($btag){
	global $merchants_mode;
if (empty($btag))
	return $btag;
$btag = str_replace("--","-",$btag);
	$exp=explode("-",$btag);
	
	$bt = Array();
	
	for($i=0;$i<count($exp);$i++){
			$additional="";
		$preParam = (substr($exp[$i],0,1));
			// die($preParam);
			
			if ($preParam =='a' && empty($bt['affiliate_id'])) {
				$tag = 'affiliate_id';	//break;
			}
			elseif ($preParam =='b' && empty($bt['banner_id'])){
				$tag = 'banner_id';		//break;
			}
			elseif ($preParam =='p' && empty($bt['profile_id'])) {
				$tag = 'profile_id';	//break;
			}
			elseif ($preParam =='g' && empty($bt['product_id'])) {
                $tag = 'product_id';	//break;
            }
			elseif ($preParam =='c' && empty($bt['country'])) {
				$tag = 'country';		//break;
			}
			elseif ($preParam =='u' && empty($bt['uid'])){
				$tag = 'uid';	//	break;
			}
	elseif ($preParam =='f'){
		$tag = 'freeParam';		//break;
	}
		else  {
			$tag = 'freeParam';	
			$additional = "-";
	}
		
		$thevalue =substr($exp[$i],1);
		$bt[$tag].=$additional . $thevalue;
	}
		return $bt;
}
	
function getval($param=""){
	global $merchants_mode;
	if (empty($param))
		return $param;
	$param = isset($_GET[$param]) ? $_GET[$param] : "";
		if (empty($param))
	$param = isset($_POST[$param]) ? $_POST[$param] : "";

	return $param;
}
function validate($param="",$fieldName=""){
	global $merchants_mode;
	if (empty($fieldName) && empty($param)) {
		$resp = array(
				'status' => 'error',
				'msg' => 'Empty fieldname or param',
			);
	outputMsg($resp);
		
	}
	if (empty($param))
			$resp = array(
				'status' => 'error',
				'msg' => $fieldName .' is missing',
			);
		outputMsg($resp);
	
}

$ctag = getval('btag');

$internal_id = getval('internal_id');


$trader_id = getval('trader_id');
$merchant_id = getval('merchant_id');
$country = getval('country');
$usdamount = getval('usdamount');
$fname = getval('fname');
$email = getval('email');
$type = getval('type');
$token = getval('token');


$amount = getval('amount');
$currency = getval('currency');
$zone = getval('zone');
$platform = getval('platform');


if (empty($token) || strpos($token,'-')===false){
		$resp = array(
				'status' => 'error',
				'msg' => 'Token is invalid',
			);
	outputMsg($resp);
}
	

$exp = explode('-',$token);
if (empty($exp[0]) || empty($exp[1])|| empty($exp[2])){
		$resp = array(
				'status' => 'error',
				'msg' => 'token is invalid',
			);
		outputMsg($resp);
}
	
if (empty($type)) {
		$resp = array(
				'status' => 'error',
				'msg' => 'type is missing',
			);
		outputMsg($resp);
	
}

if ($type=='installation'){

	if(empty($install_event)) {
		$resp = array(
				'status' => 'error',
				'msg' => 'event is missing',
			);
		outputMsg($resp);
	}
	
// die ('1ctag: ' . $ctag);	
	if(empty($ctag)) {
		$resp = array(
				'status' => 'error',
				'msg' => 'tracker (btag) is missing',
			);
		outputMsg($resp);
	}
}


if (!$type=='installation'){
if (!is_numeric($internal_id))
{
		$resp = array(
				'status' => 'error',
				'msg' => 'Internal ID has to be numeric',
			);
		outputMsg($resp);
}
}


//01-cee631121c2ec9232f3a2f028ad5c89b
$product_id = $exp[0];
$product_random_code = $exp[1];
$productID_MD5 = $exp[2];

if ($merchants_mode)
	$merchant_id = $product_id;

$ctagArray = array();
$ctagArray = getInternalBtag($ctag);
if (empty($ctagArray)) {
	$ctagArray['affiliate_id']=	500;
	$ctagArray['banner_id']=	0;
	$ctagArray['group_id']=	0;
	$ctagArray['profile_id'] = 0;
	$ctagArray['product_id'] = 0;
	$ctagArray['country']=	"";
	$ctagArray['freeParam']=	"";
	$ctagArray['uid']=	0;
}else {
	$affRow = mysql_fetch_assoc(mysql_query("select * from affiliates where id = " . $ctagArray['affiliate_id'] . " limit 1; "));
	$ctagArray['group_id']= $affRow['group_id'];
}

$affiliate_id = $ctagArray['affiliate_id'];
$group_id = $ctagArray['group_id'];
$banner_id = $ctagArray['banner_id'];
$uid = $ctagArray['uid'];
$profile_id = $ctagArray['profile_id'];
$product_id = $ctagArray['product_id'];
$country = $ctagArray['country'];

if (empty($country))
	$country= $ctagArray['country'];
$freeParam = $ctagArray['freeParam'];


/* if (md5($ctagArray['affiliate_id'])!=$productID_MD5) {
	die ('Invalid Token');
} */

if (!$merchants_mode){

		$qry = "select * from products_items where id = " . $product_id;

		$productRow = mysql_fetch_assoc(mysql_query($qry));
		
		securityCheck($productRow['postbackIPlimit']);
		

		if ($productRow['randomKey'] !=''.$product_random_code){
			
				$resp = array(
						'status' => 'error',
						'msg' => 'Wrong product key',
					);
				outputMsg($resp);
		}

		if (md5($productRow['randomKey']) !=''.$productID_MD5) {
					$resp = array(
						'status' => 'error',
						'msg' => 'Wrong product key.',
					);
				outputMsg($resp);
		}

}
else {  // as merchants
	

		$qry = "select * from merchants where id = " . $merchant_id;

		$productRow = mysql_fetch_assoc(mysql_query($qry));
		securityCheck($productRow['postbackIPlimit']);
		
		if ($productRow['randomKey'] !=''.$product_random_code){
				$resp = array(
						'status' => 'error',
						'msg' => 'Wrong merchant key',
					);
				outputMsg($resp);
		}

		if (md5($productRow['randomKey']) !=''.$productID_MD5) {
					$resp = array(
						'status' => 'error',
						'msg' => 'Wrong merchant key.',
					);
				outputMsg($resp);
		}

		
	
	
}

		
	if($type!='lead' && $type!='account' && $type!='' && $type!='installation') {
		
				$resp = array(
				'status' => 'error',
				'msg' => 'Type is not valid',
			);
		outputMsg($resp);
		
	}
		
	if ($type=='account')
		$type='real';
		
	if ($debug_level=1) {
	mysql_query('INSERT INTO logs (rdate, flag, merchant_id, text, ip, url) VALUES (NOW(), "green", '.$product_id.', "PostBack called by '.$product_id.'", "'.$_SERVER['REMOTE_ADDR'].'", "http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI].'")');
	}

	$rdate = date('Y-m-d H:i:s');
	
	
	// LET THE FUN BEGIN ***************************************************************
if($type=='lead'){


	$chk = getTrader($product_id,$internal_id,'',$install_event);
	if($chk['id']){
			$resp = array(
				'status' => 'error',
				'msg' => 'trader (lead/real) already exist',
			);
			outputMsg($resp);
			
			}
			else {
	
	
			$q = 'INSERT INTO data_reg (affiliate_id,profile_id,group_id,banner_id,freeParam,uid,rdate, ctag, trader_id, trader_alias,merchant_id,product_id, type, email, phone, country) VALUES 
			("'.$affiliate_id.'","'.$profile_id.'","'.$group_id.'","'.$banner_id.'","'.$freeParam.'","'.$uid.'","'.$rdate.'","'.$ctag.'","'.$internal_id.'","'.$traderAlias.'",'.$merchant_id.','.$product_id.',"'.($type).'","'.$email.'","'.$phone.'","'.$country.'")';
		
		mysql_query($q);
// die ('gerger');		
			$resp = array(
				'status' => 'success',
				'msg' => 'lead inserted: ' . $internal_id,
			);
		outputMsg($resp);
			
	
			
			

		}
} 
else if( $type=='real'){

	$chk = getTrader($product_id,$internal_id,'');
	if($chk['id'] && $chk['type']=='real'){
			$resp = array(
				'status' => 'error',
				'msg' => 'trader (account) already exist',
			);
				header('Content-Type: application/json');
		die (json_encode($resp));
		
			}
			else if($chk['id'] && $chk['type']=='lead'){
				
				if (!$merchants_mode)
						$upd = "update data_reg set type= 'real'  where product_id = " . $product_id . " and trader_id = " . $internal_id;
					else
						$upd = "update data_reg set type= 'real'  where merchant_id = " . $merchant_id . " and trader_id = " . $internal_id;
						
		mysql_query($upd);
		
		
		$resp = array(
				'status' => 'success',
				'msg' => 'lead converted into account',
			);
			outputMsg($resp);
			
			}
			else {
	
			// $q = 'INSERT INTO data_reg (rdate, ctag, trader_id, trader_alias,product_id, type, email, phone, country) VALUES ("'.$rdate.'","'.$ctag.'","'.$internal_id.'","'.$traderAlias.'",'.$product_id.',"'.($type).'","'.$email.'","'.$phone.'","'.$country.'")';
			
			$q = 'INSERT INTO data_reg (affiliate_id,profile_id,group_id,banner_id,freeParam,uid,rdate, ctag, trader_id, trader_alias,merchant_id,product_id, type, email, phone, country) VALUES 
			("'.$affiliate_id.'","'.$profile_id.'","'.$group_id.'","'.$banner_id.'","'.$freeParam.'","'.$uid.'","'.$rdate.'","'.$ctag.'","'.$internal_id.'","'.$traderAlias.'",'.$merchant_id.','.$product_id.',"'.($type).'","'.$email.'","'.$phone.'","'.$country.'")';
			
			
		// die ($q);
		mysql_query($q);
// die ('gerger');		
			$resp = array(
				'status' => 'success',
				'msg' => 'account inserted: ' . $internal_id,
			);
			outputMsg($resp);
		}
	} 
else if($type=='sale'){
//$qrr = 'SELECT id FROM sales_'.$merchant['name'] .' WHERE trader_id="'.$trader_id.'" AND tranz_id="'.$tranzID.'"';
			$chkTrader = getTrader($product_id,$internal_id,'');
			
			if (!$chkTrader['id'])
			{
				$resp = array(
				'status' => 'error',
				'msg' => 'cant handle sale without having an account',
			);
			outputMsg($resp);
				
			}
			
if (!$merchants_mode)
			$qrr = 'SELECT  id FROM data_sales  WHERE  product_id = ' . $product_id . ' and  `tranz_id` = "'.$internal_id.'" AND `trader_id` = ' . $internal_id;
		else
			$qrr = 'SELECT  id FROM data_sales  WHERE  merchant_id = ' . $merchant_id . ' and  `tranz_id` = "'.$internal_id.'" AND `trader_id` = ' . $internal_id;
		
//die ($qrr);
		$chk = mysql_fetch_assoc(mysql_query($qrr));
			
		
		if($chk['id']){
			$exist = 1;
			$resp = array(
				'status' => 'error',
				'msg' => 'tranz already exist',
			);
			outputMsg($resp);
		}
		else {
			$type = 'deposit';
		$qry =  ('INSERT INTO data_sales (country,rdate,tranz_id,ctag,trader_id,type,amount,status,merchant_id,product_id,affiliate_id,group_id,banner_id,freeParam) VALUES ("'.$chkTrader['country'].'","'.$rdate.'","'.$internal_id.'","'.$chk['ctag'].'","'.$internal_id.'","'.$type.'","'.$usdamount.'","'.$status.'",'.$merchant_id .','.$product_id .','.$chkTrader['affiliate_id'].','.$chkTrader['group_id'].','.$chkTrader['banner_id'].',"'.$chkTrader['freeParam'].'")');
		// die ($qry);
		mysql_query ($qry);
			$resp = array(
				'status' => 'success',
				'msg' => 'sale inserted: ' . $trader_id,
			);
			outputMsg($resp);
			
		}
}
else if($type=='installation'){

$where = " and trader_id ='" . $internal_id . "' ";
if (empty($internal_id))
	$where = " and uid ='" . $uid . "' ";
			
		
		
if (!$merchants_mode)			
	$qrr = 'SELECT  id FROM data_install  WHERE   product_id = ' . $product_id . $where . ' and  `type` = "' . $install_event. '" ';
else
	$qrr = 'SELECT  id FROM data_install  WHERE  merchant_id = ' . $merchant_id .  $where  . ' and  `type` = "' . $install_event. '" ';

// die ($qrr);
		$chk = mysql_fetch_assoc(mysql_query($qrr));
			
		
		if($chk['id']){
			$exist = 1;
			$resp = array(
				'status' => 'error',
				'msg' => 'installation is already exist',
			);
			outputMsg($resp);
		}
		else {
			
			
			$q = 'INSERT INTO data_install (affiliate_id,profile_id,group_id,banner_id,freeParam,uid,rdate, ctag, trader_id, trader_alias,merchant_id,product_id, type, email, phone, country) VALUES 
			("'.$affiliate_id.'","'.$profile_id.'","'.$group_id.'","'.$banner_id.'","'.$freeParam.'","'.$uid.'","'.$rdate.'","'.$ctag.'","'.$internal_id.'","'.$traderAlias.'",'.$merchant_id.',"'.($product_id).'","'.($install_event).'","'.$email.'","'.$phone.'","'.$country.'")';
			
			
		
		mysql_query ($q);
		
		
		include('pixel.php');
		$siteURL = $set->sitebaseurl;
		$pixelurl = $siteURL  . '/pixel.php?act=install&product_id='.$product_id.'&merchant_id='.$merchant_id.'&trader_id='.$internal_id.'&trader_alias='. str_replace(' ','%20',$trader_alias) . '&ctag=' . $ctag . '&click_id='.$click_id. '&type='.$type;

			if ($debug==1)
					echo $pixelurl.'<br>';
				// die ('pixelurl: '  . $pixelurl);
				$pixelContent  = firePixel($pixelurl);
				if (strlen($pixelContent)>1 && $debug==1) {
					echo '<br><br><span style="color:blue">Firing Account Pixel, Affiliate_ID = ' . $affiliate_id.' -- '.$pixelurl.'</span><br>';
					echo $pixelContent;
				}
		
		
		
		
			$resp = array(
				'status' => 'success',
				'msg' => 'installation inserted: ' . $internal_id,
			);
			outputMsg($resp);
			
		}
}
	
	
	
	header('Content-Type: application/json');
	$a = json_encode($resp);
	die ($a);
	
?>
