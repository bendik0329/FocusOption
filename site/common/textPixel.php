<?php
chdir('../');
require_once('common/database.php');
$debug = isset($_GET['dbg']) ? $_GET['dbg'] : false;

function doPostPixel($url){
		$parse_url=parse_url($url);
		$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
		
		if (!$da) {
			echo "$errstr ($errno)<br/>\n";
			echo $da;
			} else {
			$response ="";
			$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
			$params .= "Host: ".$parse_url['host']."\r\n";
			$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$params .= "User-Agent: AffiliateBuddies Agent\r\n";
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
function get_redirect_target($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $headers = curl_exec($ch);
    curl_close($ch);
    // Check if there's a Location: header (redirect)
    if (preg_match('/^Location: (.+)$/im', $headers, $matches))
        return trim($matches[1]);
    // If not, there was no redirect so return the original URL
    // (Alternatively change this to return false)
    return $url;
}
// FOLLOW ALL REDIRECTS:
// This makes multiple requests, following each redirect until it reaches the
// final destination.
function ctagValid($tag='') { // a20-b100-p
		if (!$tag) return false;
		if ($tag==null OR $tag=='null' OR $tag=='NULL' OR trim($tag)==false) return false;
		$exp=explode("-",$tag);
		if (substr($exp[0],0,1) == "a" AND substr($exp[1],0,1) == "b") return true;
		return false;
}

function getBtag($btag){
if (empty($btag))
	return $btag;
$btag = str_replace("--","-",$btag);
$btag = str_replace(" ","%20",$btag);
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
			$additional = "-".$preParam;
	}
		
		$thevalue =substr($exp[$i],1);
		$bt[$tag].=$additional . $thevalue;
	}
		return $bt;
}
		
function get_redirect_final_target($url)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // follow redirects
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1); // set referer on redirect
    curl_exec($ch);
    $target = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    if ($target)
        return $target;
    return false;
}


$url = isset($_GET['url']) ? $_GET['url'] : "";
$url = urldecode($url);
$pixelurl = isset($_GET['pixelurl']) ? $_GET['pixelurl'] : "";
$pixelurl = urldecode($pixelurl);
$method = isset($_GET['method']) ? $_GET['method'] : "";


if (empty($url)  || empty($pixelurl)){
	if ($debug)
		die ('url or pixelurl are empty');
	die();
}

$last_redirect_url = get_redirect_final_target($url);
$ex = explode('?',$last_redirect_url,2);
$last_url_query_string = $ex[1];
parse_str($last_url_query_string,$arr);
$bag = "";
foreach ($arr as $value){
	if (ctagValid($value) && empty($btag)){
		$btag = $value;
		// break;
	}
}




if (empty($btag)) {
	if ($debug)
		die ('btag is empty');
	die();
}

$btagArray = getBtag($btag);

$DynamicParameter = $arr['p1'];
if (empty($DynamicParameter))
	$DynamicParameter = $btagArray['freeParam'];

$DynamicParameter2 = $arr['p2'];
$DynamicParameter3 = $arr['p3'];


if ( 
(strpos($pixelurl,'{dynamic_parameter}')!==false || strpos($pixelurl,'{p1}')!==false)
	&&
 (empty($DynamicParameter) || strpos($DynamicParameter,'{')!==false || strpos($DynamicParameter,'<')!==false)
 ) 
 {
	$msg = ('Tracking URL is incorrect, make sure the dynamic parameter is being pushed correctly into "p1" parameter');
	$response = array('display'=>1,'msg'=>$msg);
	die (json_encode($response));
}



	$find = Array("&#39;","#34;","{email}","{ctag}","{trader_id}","{ip}","{trader_alias}","{type}","{p1}","{p2}","{dynamic_parameter}","{dynamic_parameter2}","{dynamic_parameter3}","{p3}","{affiliate_id}","{uid}","{subid}","{merchant_id}","{product_id}");
	$replace = Array('"','"',$email,$_GET['param'],$trader_id,$user_ip,$_GET['trader_alias'],$_GET['type'],$DynamicParameter,$DynamicParameter2,$DynamicParameter,$DynamicParameter2,$DynamicParameter3,$DynamicParameter3,$affiliate_id,$uid, $subid,$merchant_id,$product_id);
	$pixelCode=str_replace($find,$replace,trim($pixelurl));

	
if ($method=='get'){
file_get_contents($pixelCode);
$response = array('display'=>0,'msg'=>'Done GET');
die (json_encode($response));


}else if ($method=='post'){
doPostPixel($pixelCode);
$response = array('display'=>0,'msg'=>'Done POST');
die (json_encode($response));
}
else
{
	$response = array('display'=>0,'msg'=>'No method');
	die (json_encode($response));
}
		

