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
	$qq=mysql_query("SELECT * FROM affiliates_profiles WHERE affiliate_id='".$set->userInfo['id']."' AND valid='1' ORDER BY id");
	while ($ww=mysql_fetch_assoc($qq)) $html .= '<option value="'.$ww['id'].'" '.($profile_id == $ww['id'] ? 'selected' : '').'>'.$ww['url'].' ['.lang('Site ID').': '.$ww['id'].']</option>';
	return $html;
	}




	function getTag($tag, $endtag, $xml) {
		if (!$endtag) $endtag=$tag;
		preg_match_all("/".$tag."(.*?)".$endtag."/", $xml, $matches);
		return $matches[1][0];
		}
	

	function doPostCurl($url, $params)
	{
		$strParams = '';
		
		/*foreach ($params as $v) {
			$arrField = get_object_vars($v);
			$strParams .= $arrField['title'] . '=' . $arrField['val'] . '&';
		}*/
		
		foreach ($params as $v) {
			$strParams .= $v->title . '=' . $v->val . '&';
		}
		
		
		
	$ch = curl_init();
        
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $strParams);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
	}
	
	
	function doPost($url,$param=null){
		$parse_url=parse_url($url);
		
		$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
		
		if (!$da) {
			echo "$errstr ($errno)<br/>\n";
			echo $da;
			} else {
			
			$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
			$params .= "Host: ".$parse_url['host']."\r\n";
			$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$params .= "User-Agent: AffiliateBuddies Agent\r\n";
			$params .= "Content-Length: ".strlen($parse_url['query'])."\r\n";
			$params .= "Connection: close\r\n\r\n";
			$params .= $parse_url['query'];
			if ($param!=null)
				$params.= $param;
				
			fputs($da, $params);
			while (!feof($da)) $response .= fgets($da);
			fclose($da);
			
			// split the result header from the content
			$result = explode("\r\n\r\n", $response, 2);
			$content = isset($result[1]) ? $result[1] : '';
			return $content;
			}
		}
	
	/*
	function getUSD($price='0',$to='USD') {
		if (strtolower($to) == "usd") return $price;
		if (strtolower($to) == "rub") return round($price*0.03,2);
			else {
			$qq=mysql_query("SELECT rate FROM currencies WHERE lower(coin)='".strtolower($to)."'");
			$ww=mysql_fetch_assoc($qq);
			return round($price*$ww['rate'],2);
			}
		}
	*/
	function getSiteID($string="") {
		if (!$string) return false;
		$exp=explode("_",$string); // a_20b_115
		return substr($exp[1], 0, -1);
		}
	
	function setLog($text='',$site_id=0,$flag='green') {
		if (!$text) return false;
		$qq=mysql_query("SELECT id FROM logs WHERE text='".$text."' AND merchant_id='".$site_id."'");
		$ww=mysql_fetch_assoc($qq);
		if ($ww['id']) {
			updateUnit("logs","rdate='".dbDate()."'","id='".$ww['id']."'");
			return false;
			} else mysql_query("INSERT INTO logs (rdate,flag,merchant_id,text) VALUES ('".dbDate()."','".$flag."','".$site_id."','".mysql_escape_string($text)."')");
		}
	
	function btagValid($tag='') { // a_20b_100
		if (!$tag) return false;
		if ($tag==null OR $tag=='null' OR $tag=='NULL' OR trim($tag)==false) return false;
		$exp=explode("_",$tag);
		if ($exp[0] == "a" AND substr($exp[1],-1) == "b") return true;
		return false;
		}
	
	function ctagValid($tag='') { // a20-b100-p
		if (!$tag) return false;
		if ($tag==null OR $tag=='null' OR $tag=='NULL' OR trim($tag)==false) return false;
		$exp=explode("-",$tag);
		if (substr($exp[0],0,1) == "a" AND substr($exp[1],0,1) == "b") return true;
		return false;
		}
	
	function ctagMarketValid($tag='') { // a-20-b100-p
		if (!$tag) return false;
		if ($tag==null OR $tag=='null' OR $tag=='NULL' OR trim($tag)==false) return false;
		$exp=explode("-",$tag);
		if (substr($exp[0],0,1) == "a" AND substr($exp[2],0,1) == "b") return true;
		return false;
		}	
?>