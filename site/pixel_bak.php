<?php
require('common/global.php');

//$exp=explode("-",$_GET['ctag']);

$ctagArray = array();
$ctagArray = getBtag($_GET['ctag']);

$affiliate_id=$ctagArray['affiliate_id'];
$banner_id=$ctagArray['banner_id'];

if (!$banner_id=='')
	$banner_id=0;
		
// $affiliate_id=substr($exp[0],1); // a
// $banner_id=substr($exp[1],1); // b
if (!$affiliate_id) die("No Affiliate ID");
//if (!$banner_id) die("No Banner ID");

$getBanner = dbGet($banner_id,"merchants_creative");
$merchant_id = $getBanner['merchant_id'];
if ($merchant_id=='') {
	$merchant_id = $_GET['merchant_id'];
}
	

$DynamicParameter = $banner_id=$ctagArray['freeParam'];

//  new method

//$ctag=$_GET['ctag'];
$profile_id=$ctagArray['profile_id'];
$country=$ctagArray['country'];
$uid=$ctagArray['uid'];
$freeParam=$ctagArray['freeParam'];
// new method

$subid = isset($_GET['subid']) ? $_GET['subid'] : '';


function doPost($url){

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
		
		// split the result header from the content
		$result = explode("\r\n\r\n", $response, 2);
		$content = isset($result[1]) ? $result[1] : '';
		//die ($content);
		return $content;
		
	}
}

switch ($act) {
	case "account":
		/*
			This pixel can fire the: trader_id, ctag, type
			<iframe src="http://aff.bestforexpartners.com/pixel.php?act=account&ctag=%PARAM%&trader_id=%ID%&trader_alias=%ID%&type=lead" scrolling="no" frameborder="0" width="1" height="1"></iframe>
		*/
		$find = Array("&#39;","#34;","{ctag}","{trader_id}","{trader_alias}","{type}","{dynamic_parameter}","{affiliate_id}","{uid}","{subid}");
		$replace = Array('"','"',$_GET['param'],$_GET['trader_id'],$_GET['trader_alias'],$_GET['type'],$DynamicParameter,$affiliate_id,$uid,$subid);
		//$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='account' LIMIT 1"));
		$qry = "SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='account'";
		
		$rslts = (mysql_query($qry));
		while($getPixel = mysql_fetch_assoc($rslts)){
		
			
			if ($getPixel['id']) {
				$pixelCode=str_replace($find,$replace,$getPixel['pixelCode']);
				mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
				$qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`) VALUES ('".$pixelCode."',".$getPixel['id'].")";
				mysql_query ($qry);
				
				if (filter_var($pixelCode, FILTER_VALIDATE_URL)){
					echo doPost($pixelCode);
				}else
					echo $pixelCode;
			}
		}
		break;
	
	case "deposit":
		/*
			This pixel can fire the: trader_id, ctag, amount, currency, tranz
			<iframe src="http://aff.bestforexpartners.com/pixel.php?act=deposit&ctag=%PARAM%&trader_id=%ID%&tranz=%TRANSACTIONID%&type=deposit&currency=USD&amount=%DEPOSITAMOUNT%" scrolling="no" frameborder="0" width="1" height="1"></iframe>
		*/
		
		$find = Array("&#39;", "#34;","{ctag}","{trader_id}","{tranz}","{type}","{currency}","{amount}","{dynamic_parameter}","{affiliate_id}","{uid}","{subid}");
		$replace = Array('"','"',$_GET['ctag'],$_GET['trader_id'],$_GET['tranz'],$_GET['type'],$_GET['currency'],$_GET['amount'],$DynamicParameter,$affiliate_id,$uid,$subid);
		
		//$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='sale' LIMIT 1"));
		$rslts = (mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$merchant_id."' AND valid='1' AND type='sale'"));
		while($getPixel = mysql_fetch_assoc($rslts)){
			if ($getPixel['id']) {
			
			mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
				
				$pixelCode=str_replace($find,$replace,$getPixel['pixelCode']);
				$qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`) VALUES ('".$pixelCode."',".$getPixel['id'].")";
				mysql_query ($qry);
				if (filter_var($pixelCode, FILTER_VALIDATE_URL))
					echo doPost($pixelCode);
				else
					echo $pixelCode;
			}
		}
		break;

	default:
		_goto();
	break;
		
	}
die();
?>