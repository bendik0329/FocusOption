<?php 



function getPendingPixels(){
	
		echo 'Firing Pixels:<br>';
		
		
		
		$qry = "SELECT * FROM pixel_logs WHERE status=0";
		$rslts = (mysql_query($qry));
		while($getPixel = mysql_fetch_assoc($rslts)){
		
				/* $url = $getPixel['url'];
				$method = $getPixel['method'];
				if ($method =='curlPostOneUrl') {
								if (strpos($url,'?')>0) {
									$resp = curlPostOneUrl($url);
								}
								else {
									$arr = array();
									$resp = curlPost($url,$arr);
								}
								if ($debug ==1) {
									var_dump($url);
									echo '<br>';
									echo '<br>';
									var_dump($arr);
									echo '<br>';
									var_dump($resp);
									// die();
								}
							} else if ($method =='curlGet') {
								
								$resp = curlGet($url);
								
								if ($debug ==1) {
									var_dump($url);
									echo '<br>';
									echo '<br>';
									var_dump($resp);
									// die();
								}
							}
							 else if ($method =='doGet') {
								
								$resp = doGet($url);
								
								if ($debug ==1) {
									var_dump($url);
									echo '<br>';
									echo '<br>';
									var_dump($resp);
									// die();
								}
							}
							 else if ($method =='doGetSSL') {
								
								$resp = doGetSSL($url);
								
								if ($debug ==1) {
									var_dump($url);
									echo '<br>';
									echo '<br>';
									var_dump($resp);
									// die();
								}
							}
							 else if ($method =='doPost') {
								
								$resp = doPost($url);
								if ($debug ==1) {
									var_dump($url);
									echo '<br>';
									echo '<br>';
									var_dump($resp);
									// die();
								}
							}
							 else if ($method =='filegetcontent') {
								
								$resp = file_get_contents(urldecode($url));
								if ($debug ==1) {
									var_dump($url);
									echo '<br>';
									echo '<br>';
									var_dump($resp);
									// die();
								}
							} */
		$curDate = @date('Y-m-d H:i:s');
		echo '<br>'.$getPixel['type'].' pixel (method: ' . $method . '  -   '.$resp.')  --   '.$url.'<br>';					
		mysql_query("UPDATE pixels SET totalfire=totalfire+1 WHERE id='".$getPixel['pixel_id']."'");
		mysql_query("UPDATE pixel_logs SET dateTime ='" .$curDate. "' , status=1,pixelResponse ='".mysql_real_escape_string($resp)."' WHERE id='".$getPixel['id']."'");
		}
}

function html_escape($html_escape) {
// mysql_real_escape_string
        $html_escape =  htmlspecialchars($html_escape, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $html_escape =  strip_tags($html_escape);
        return $html_escape;
}
	
	

function getRealIP($ip=null,$debug=false){
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
 
 
function clearAscii($str){
	
	$str = html_entity_decode($str, ENT_QUOTES, 'iso-8859-1');
// remove any entities that remain
// $str = preg_replace('/&#(x[0-9]{4}|\d+);/', '', $str);
$str = preg_replace('/[^\x00-\x7F]+/', '', $str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);
$str = str_replace('','',$str);

return $str;
	
}

function get_unique_int()
{
 
 /* $t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$micro = substr($micro, 0, 1000000);
$time =date("His");
$time = date('His', strtotime('+8 hours', $time));
$uid =date('Ymd') .$time.$micro;//.   $affiliate_id ;
 */
 
 
 // $micro = round(microtime(true) * 1000)
 
  $unique = str_replace(' ', '', microtime());
 // echo $unique;
 // echo "<br/>";
 $rand = mt_rand(0,99);
 // echo $rand;
 // echo "<br/>";
 $unique = str_replace('0.', $rand, $unique);
 return $unique;
 
}


function getIPCountry($ip=-1) {
//die ('$IPaddr' . $ip);

	global $_SERVER;
	if ($ip==-1)
	$IPaddr = getClientIp();
	else
	$IPaddr = $ip;
	if ($IPaddr == "") return false;
		else {
			$ips = explode(".", $IPaddr);
			$ipno = ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
			}
	$qy = "SELECT * FROM ip2country WHERE 1=1 and ipFROM <= '".$ipno."' AND ipTo >= '".$ipno."' LIMIT 1";
	//die ($qy);
	$sql = mysql_query($qy);
	$ww=mysql_fetch_assoc($sql);
	return $ww;
	}
	
	function GetOverrideDynamicTracker ($affiliate_id=0 , $uid=0,$recordDate='') {
	if ($affiliate_id>0 && $uid>0) {
		
		if (empty($recordDate))
		$recordDate = date('Y-m-d H:i:s');	
		
		// $qry = "select  DynamicTracker from TrackerConversion where affiliate_id = " . $affiliate_id . " and uid= " . $uid;

		// $dateBeforeMonthsAgo =  date("Y-m-d H:i:s", strtotime( date( "Y-m-d H:i:s", strtotime( date("Y-m-d H:i:s") ) ) . "-1 week" ) );
		// $dateBeforeMonthsAgo =  date("Y-m-d H:i:s", strtotime( date( "Y-m-d H:i:s", strtotime( $recordDate ) ) . "-1 week" ) );
		$dateBeforeMonthsAgo =  date("Y-m-d H:i:s", strtotime( date( "Y-m-d H:i:s", strtotime( $recordDate ) ) . "-2 days" ) );


		$qry = "select  DynamicTracker from TrackerConversion where 1=1 and " .
		(true  ? ' rdate> "'. $dateBeforeMonthsAgo . '"  and ' : '' ).
		" affiliate_id = " . $affiliate_id . " and uid= " . $uid . " limit 1 ;";
		$resource = mysql_query($qry);
		$row = mysql_fetch_assoc($resource);
		if (!empty($row)) {
			
			if (isJson($row['DynamicTracker']))
				return (json_decode($row['DynamicTracker'],true));
		else
			return $row['DynamicTracker'];

		}
	}
	return NULL;
}

function checkUserFirewallIP($ip){
		
			$sql = "select IPs from users_firewall where type='traffic' and valid=1";
			$res = function_mysql_query($sql,__FILE__,__FUNCTION__);
			$iparray = array();
			while($ips = mysql_fetch_assoc($res)){
				
				if(strpos($ips['IPs'],"|")){
					$all_ips = explode("|",$ips['IPs']);
					$iparray = array_merge($iparray , $all_ips);
				}
				else{
					$iparray[] = $ips['IPs'];
				}
			}
			
			foreach($iparray as $k=>$ip_val){
				
				
				if(strpos($ip_val,"*")){
					$newip   = explode(".",$ip_val);
					unset($newip[count($newip)-1]);
					$newip = implode("." , $newip);
				
					if(false !== strpos($ip,$newip)){
						return true;
					}
				}
				else{
					if($ip == $ip_val){
						return true;
					}
				}
				
			}
			return false;
	
	}
	
	
	function insert_new_traffic_record($data){
	
	$fields = implode(',',array_keys($data));
	$values = implode(',', array_map(function($value) {
			return '"' . $value . '"';
	}, array_values($data)));
	
	function_mysql_query("INSERT INTO traffic ($fields) VALUES ($values)",__FILE__,__FUNCTION__);
}


function startsWith($haystack, $needle)
{
     $length = strlen($needle);
     return (substr($haystack, 0, $length) === $needle);
}

function endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
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
			$additional = "-".$preParam;
	}
		
		$thevalue =substr($exp[$i],1);
		// echo $tag . '    ' . $thevalue.'<br>';
		$bt[$tag].=$additional . $thevalue;
	}
	
	
	
	
	foreach($bt as $name=>$value){
		if (empty($bt[$name])){
		
			$bt[$name]="0";
		}
	}

		return $bt;
		
		
/* 	$btag = str_replace("--","-",$btag);
	$exp=explode("-",$btag);
	
	$bt = Array();
	
	for($i=0;$i<count($exp);$i++){
		switch(substr($exp[$i],0,1)){
			
			
			
			case 'a':	$tag = 'affiliate_id';	break;
			case 'b':	$tag = 'banner_id';		break;
			case 'p':	$tag = 'profile_id';	break;
			case 'c':	$tag = 'country';		break;
			case 'u':	$tag = 'uid';		break;
			case 'f':	$tag = 'freeParam';		break;
			
			default:  {
				$tag = 'freeParam';	
				$additional = "-";
				break;
			}
			
		}
		
		$bt[$tag].=$additional . substr($exp[$i],1);
		
	return $bt;
	} */
	
	
	
	/*
	$bt['affiliate_id']=substr($exp[0],1); // a
	$bt['banner_id']=substr($exp[1],1); // b
	$bt['profile_id']=substr($exp[2],1); // p
	$bt['freeParam']=substr($exp[3],1); // f
	$bt['country']=substr($exp[4],1); // c
	$bt['clickID']=substr($exp[5],1); // u
	*/
	
	
	//$db['country']=substr($exp[count($exp)-1],1); // c

}


function getClientIp($ip=null){
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
 
 function getDefaultAffiliateID($string="0"){
	 global $set;
	 if (!isset($set->defaultAffiliateID)){
		 
		 $defAffID = mysql_fetch_assoc(mysql_query("select id from affiliates where ".$string . " = ".$string . " and  valid=1 and isDefaultAffiliate=1 limit 1;"));
	$set->defaultAffiliateID = empty($defAffID['id']) ? 500 : $defAffID['id'];
		 
	 }
	 
 }
 function getUrlsFromHtml($htmlContent = '')
{    
	$origImageSrc = array();
        
        if (empty($htmlContent)) {
            return $origImageSrc;
        }
        
	preg_match_all('/<img[^>]+>/i', $htmlContent, $imgTags);
        
	for ($i = 0; $i < count($imgTags[0]); $i++) {
	    preg_match('/src=[\'"]+([^[\'"]*]+)/i',$imgTags[0][$i], $imgage);
	    $origImageSrc[] = str_ireplace( 'src=[\'"]+', '',  $imgage[0]);
	}
        
	preg_match_all('/<a[^>]+>/i',$htmlContent, $imgTags);
        
	for ($i = 0; $i < count($imgTags[0]); $i++) {
	    preg_match('/href="([^"]+)/i',$imgTags[0][$i], $imgage);
	    $origImageSrc[] .= str_ireplace( 'src=[\'"]+', '',  $imgage[0]);
	}
        
	if (empty($origImageSrc)) {
            $origImageSrc[] = $htmlContent;
	}
        
	return ($origImageSrc);
}

function strposa($haystack, $needles=array(), $offset=0) {
    $chr = array();
    foreach($needles as $needle) {
            $res = @strpos($haystack, $needle, $offset);
            if ($res !== false) $chr[$needle] = $res;
    }
    if(empty($chr)) return false;
    return min($chr);
}
function isHackingAttempt($queryString){

$queryString = strtolower($queryString);
	
	$array  = array("select*", " union ", "and sleep","for delay",";SELECT ", "select 1", "char(", "avdsinjectionheader", "var _object = document.querySelector");
if (strposa($queryString, $array, 1)) {
	return true;
}

return false;

}

 
 ?>