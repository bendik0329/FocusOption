<?php
// cron job v2.0 without table foreach merchant.

ini_set("memory_limit","128M");
require_once('common/global.php');

$find = Array("\n","\t");
$replace = Array("","");
$sentAffiliates = Array();

set_time_limit(0);





function ctagValid($tag='') { // a20-b100-p
	if (!$tag) return false;
	$exp=explode("-",$tag);
	if (substr($exp[0],0,1) == "a" AND substr($exp[1],0,1) == "b") return true;
	return false;
	}

function getTag($tag, $endtag, $xml) {
	if (!$endtag) $endtag=$tag;
	preg_match_all("/".$tag."(.*?)".$endtag."/", $xml, $matches);
	if (isset($matches[1][0])) return $matches[1][0];
}
	
	  
function fire_pixel ($type , $affiliate_id)
{
	if ($api_type =='account') {
	echo 'about to get to getPixel...<BR>';
							$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND valid='1' AND type='".$type."')"));
							if ($getPixel['id']) {
								echo 'In getPixel1...<BR>';
								if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) {
									echo 'In getPixel2...<BR>';
									doPost($getPixel['pixelCode']);
								}else {
									echo 'In getPixel3...<BR>';
									echo $getPixel['pixelCode'];
								}
								echo 'In getPixel --> About to update DB...<BR>';
								mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
								}
		} 
		else if ($type =='lead') {
		$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND valid='1' AND type='account'"));
						if ($getPixel['id']) {
							if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) doPost($getPixel['pixelCode']);
								else echo $getPixel['pixelCode'];
							mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
							}
		} 
		else if ($type =='sale') {
		$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND valid='1' AND type='sale'"));
				if ($getPixel['id']) {
					if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) doPost($getPixel['pixelCode']);
						else echo $getPixel['pixelCode'];
					mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
					}
		}
		else {
		
		}
}


function convertToHttpsPostRequest($strUrl)
{
	$strUrl = trim($strUrl);
	$arrUrl = explode(':', $strUrl);
	
	if ('http' == $arrUrl[0]) {
		$arrUrl[0] = 'https';
		return implode(':', $arrUrl);
	} else {
	    return $strUrl;
	}
}


/**
 * @return string
 */
function encodeDateTimeWithinUrl($url)
{
	$arrUrl = explode(':', $url);
	$url 	= 'nups' == trim($arrUrl[0]) ? implode(':', array_slice($arrUrl, 1)) : trim($url);
	
	unset($arrUrl);
	$arrUrl                = explode('?', $url);
	$arrQueryString        = explode('&', $arrUrl[1]);
	$strEncodedQueryString = '';
	
	foreach ($arrQueryString as $strPair) {
		$arrPair        = explode('=', $strPair);
		$strEncodedPair = '';
		
		if ('startdate' == $arrPair[0]) {
			$strEncodedPair = 'startdate=' . str_replace(' ', '%20', $arrPair[1]) . '&';
		} elseif ('enddate' == $arrPair[0]) {
			$strEncodedPair = 'enddate=' . str_replace(' ', '%20', $arrPair[1]) . '&';
		} else {
			$strEncodedPair = implode('=', $arrPair) . '&';
		}
		
		$strEncodedQueryString .= $strEncodedPair;
		unset($strEncodedPair, $strPair, $arrPair);
	}
	
	$arrUrl[1] = $strEncodedQueryString;
	$url       = implode('?', $arrUrl);
	
	return $url;
}


function populateDBwithCtag(&$db)
{
	$db['ctag']         = str_replace("--", "-", $db['ctag']);
	$ctag               = $db['ctag'];
	$ctagArray          = array();
	$ctagArray          = getBtag($ctag);
	$db['affiliate_id'] = $ctagArray['affiliate_id'];
	$db['banner_id']    = $ctagArray['banner_id'];
	$db['profile_id']   = $ctagArray['profile_id'];
	$db['country']      = $ctagArray['country'];
	$db['uid']          = $ctagArray['uid'];
	$db['freeParam']    = $ctagArray['freeParam'];
}


/*function dogetnir($url)
{
	
}*/


function doGet($url)
{
	//$result = dogetnir($url);
	
	$result =  file_get_contents(str_replace('https','http',$url));
	
	
	if (false === $result) {
		echo('<br>There is a problem with the API url, please contact system administrator<br>');
	}

	$error  = strtolower(getTag('<error>', '<\/error>', $result));
	
	if ($error == 'noresults') {
		echo '<span style="color:red">' . ucwords(getTag('<error>', '<\/error>', $result)) . '</span>';
		return '';
	} else {
		return $result;
	}
}


function doPost($url)
{
	$arrUrl  = explode('?', $url);	
	$ch      = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	
	/*if ($errno = curl_errno($ch)) {
		$error_message = curl_error($ch);  // From PHP 5.5
		echo "<hr>cURL error ({$errno}):\n {$error_message}<hr>$url";
	}*/
	
	curl_close($ch);
	
	$error = strtolower(getTag('<error>', '<\/error>', $result));
	
	if ($error == 'noresults') {
		echo '<span style="color:red">' . ucwords(getTag('<error>', '<\/error>', $result)) . '</span>';
		return '';
	} else {
		return $result;
	}
	
	/*$parse_url = parse_url($url);
	$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
	
	if (!$da) {
		echo "$errstr ($errno)<br/>\n";
		echo $da;
	} else {
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
		return $content;
	}*/
}

function doGetCurl ($url) { 
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$json = '';
if( ($json = curl_exec($ch) ) === false)
{
    echo 'Curl error: ' . curl_error($ch);
}
else
{
    echo 'Operation completed without any errors';
}

// Close handle
curl_close($ch);
}




function doPost2($url){
	
	$ch = curl_init();  
	
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	"Content-Type: application/json; charset=utf-8"
	));
	
    $output=curl_exec($ch);
	
	if($output === false){
		echo 'Curl error: ' . curl_error($ch);
	}
 
    curl_close($ch);
    return $output;
	
	
}




function checkCurrencies(){
	global $set;
	$currentTime = new DateTime();
	$startTime = new DateTime('01:00');
	$endTime = new DateTime('04:00');
	if (($currentTime >= $startTime && $currentTime <= $endTime) OR ($_REQUEST['currency'])) {
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://".$set->http_host."/getCurrency.php"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch); 
		echo $output;
	}else{
		echo '<BR>Currencies will be update in other time.<BR>';
	}
}
checkCurrencies();
	
		
echo '<style type="text/css">html,body { font-size: 11px; font-family: Tahoma; } </style>';


if ($_GET['m_date']) {
	$exp_mdate=explode("-",$_GET['m_date']);
	if ($_GET['monthly']) {
		$scanDateFrom = date("Y-m-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])).' 00:00:00';
		$scanDateTo = date("Y-m-01", strtotime("+1 Month",strtotime($scanDateFrom))).' 23:59:59';
		$totalPage = 30;
	} else {
		$scanDateFrom = date("Y-m-d", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])).' 00:00:00';
		$scanDateTo = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]))).' 23:59:59';
	}
} else {
	$scanDateFrom = date("Y-m-d", strtotime("-1 Day")).' 00:00:00';
	$scanDateTo = date("Y-m-d").' 23:59:59';
}

// TEST.
//$scanDateFrom = '2014-12-01%2000:00:00';
//$scanDateTo   = '2014-12-10%2000:00:00';


if (!$totalPage) {
	$totalPage = 1;
}
	
//$merchants = mysql_query('SELECT * FROM merchants WHERE valid=1 AND id = 19');
$merchants = mysql_query('SELECT * FROM merchants WHERE   id = 19');

/*$arrTest = array();
while($merchant = mysql_fetch_assoc($merchants)) {
	$arrTest[] = $merchant['apiType'];
}
die(print_r($arrTest));  // Array ( [0] => winner ) */


while($merchant = mysql_fetch_assoc($merchants)){

	$query = 'select campID from affiliates_campaigns_relations where merchantid ='.$merchant['id'];
	
	$campsQ = mysql_query($query);
	$campaignIDs = Array();
	while($row = mysql_fetch_assoc($campsQ)){
		array_push($campaignIDs, $row['campID']);
	}
	
	/*
	$campaignIDs = Array(
	"0" => "53" // RBO Affiliate Program
	);
	*/
	
	$api_url = $merchant['APIurl'];  //including ftp url's for file type brand
	$api_user = $merchant['APIuser']; //ftp user in case of file type brand
	$api_pass = $merchant['APIpass']; //ftp pass in case of file type brand
	
	$api_type = $merchant['apiType'];
	//$api_type="file";
	
	
	if ($api_type == 'winner') {
		
		$api_url  = $merchant['APIurl'];   // including ftp url's for file type brand
		$api_user = $merchant['APIuser']; // ftp user in case of file type brand
		$api_pass = $merchant['APIpass']; // ftp pass in case of file type brand
		$api_type = $merchant['apiType'];
		
		require 'api/api_winnerEMP.php';
		
		/*require 'api/ProcessCasinoData.php';
		$processCasinoData = new ProcessCasinoData('delrio', $defaultAffiliateID, $merchant, $scanDateFrom, $scanDateTo);
		$processCasinoData->processSignups();
		$processCasinoData->processStats();
		$processCasinoData->processTransactions();
		$processCasinoData->processTransactions2();
		unset($processCasinoData);*/
		
	} else {
		echo '<BR><BR>NO API TYPE FOR MERCHANT: '.$merchant['name'].'<BR><BR>';
	}
}

$takeMonth = @mysql_fetch_assoc(mysql_query("SELECT id FROM cron_logs WHERE month='".date("n")."' AND year='".date("Y")."' AND merchant_id='".$ww['id']."'"));
if ($takeMonth['id']) @mysql_query("UPDATE cron_logs SET lastscan='".dbDate()."',reg_total=reg_total+".$reg_total.",sales_total=sales_total+".$sales_total." WHERE id='".$takeMonth['id']."'");
	else @mysql_query("INSERT INTO cron_logs (lastscan,month,year,merchant_id,merchant_name,success,reg_total,sales_total) VALUES ('".dbDate()."','".date("n")."','".date("Y")."','".$ww['id']."','".strtolower($ww['name'])."','1','".$reg_total."','".$sales_total."')");
exit;

?>