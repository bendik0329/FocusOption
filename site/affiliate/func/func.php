<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function chkMerchant($mer_id=0) {
	global $set;
	$exp=explode("|",$set->userInfo['merchants']);
	if (in_array($mer_id,$exp)) return true;
		else return false;
	}

function listProfiles($profile_id=0) {
	global $set;
	$getUser = dbGet($set->userInfo['id'],"affiliates");
	$html .= '<option value="0">'.$getUser['website'].' ['.lang('Default').']</option>';
	$qq=function_mysql_query("SELECT * FROM affiliates_profiles WHERE affiliate_id='".$set->userInfo['id']."' AND valid='1' ORDER BY id",__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) $html .= '<option value="'.$ww['id'].'" '.($profile_id == $ww['id'] ? 'selected' : '').'>'.$ww['url'].' ['.lang('Site ID').': '.$ww['id'].']</option>';
	return $html;
	}

	
	
	
////////////////////////////////////////////////////////////////////////////////////////////////////////////////// DEFAULT TIME SETTINGS - RELEVANT FOR REPORTS AND DASHBOARD:

$dtf = mysql_fetch_assoc(function_mysql_query('SELECT defaultTimeFrameForAffiliate AS def ,defaultTimeFrameForAffiliateReports AS defR FROM settings LIMIT 0,1',__FILE__,__FUNCTION__));

if (strpos((strtolower($_SERVER['REQUEST_URI'])),'reports.php?' )>0){

if($dtf['defR']){
	switch($dtf['defR']){
		case "Today":		$defTimeFrame=1;	break;
		case "Yesterday":	$defTimeFrame=2;	break;
		case "This Week":	$defTimeFrame=6;	break;
		case "This Month":	$defTimeFrame=3;	break;
		case "Last Month":	$defTimeFrame=4;	break;
	}
	
}
}

else {  // other pages than reports

	if($dtf['def']){
		switch($dtf['def']){
			case "Today":		$defTimeFrame=1;	break;
			case "Yesterday":	$defTimeFrame=2;	break;
			case "This Week":	$defTimeFrame=6;	break;
			case "This Month":	$defTimeFrame=3;	break;
			case "Last Month":	$defTimeFrame=4;	break;
		}
		
	}
}

//$defTimeFrame = 4;

switch($defTimeFrame){
	case 1: //today
		if(!$from)	$from = date("d/m/Y 00:00:00");
		if(!$to)	$to = date("d/m/Y 23:59:59");
	break;
	case 2: //yesterday
		if(!$from)	$from = date("d/m/Y 00:00:00",strtotime("-1 Day",time()));
		if(!$to)	$to = date("d/m/Y 23:59:59",strtotime("-1 Day",time()));
	break;
	case 6: //this week
		if(!$from)	$from = date("d/m/Y 00:00:00",strtotime("-6 Day",time()));
		if(!$to)	$to = date("d/m/Y 23:59:59",time());
	break;
	case 3: //this month
		if(!$from)	$from = date("01/m/Y 00:00:00",time());
		if(!$to)	$to = date("d/m/Y 23:59:59",time());
	break;
	case 4: //last month
		if(!$from)	$from = date("01/m/Y 00:00:00",strtotime("-1 Month",time()));
		if(!$to)	$to = date("t/m/Y 23:59:59",strtotime("-1 Month",time()));
	break;
	default: //default to today
		if(!$from)	$from = date("d/m/Y 00:00:00");
		if(!$to)	$to = date("d/m/Y 23:59:59");
	break;
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////// END OF DEFAULT TIME SETTINGS - RELEVANT FOR REPORTS AND DASHBOARD:




function checkIPBlocked($ip){
		
		$sql = "select count(*) as cntIps from users_firewall where IPs like '%" . $ip. "%' and (type='login' or lower(type)='all' or lower(type)='' )  and valid=1";
		$ww = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__) );
		if($ww['cntIps'] > 0)
			return true;
		return false;
	}
	
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
	
	
?>