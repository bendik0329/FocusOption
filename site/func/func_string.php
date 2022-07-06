<?php
require_once 'func_debug.php';
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */


/**
 * Find and translate given substring within a resource string.
 * 
 * @param  string $strResource
 * @param  string $strParam
 * @return string
 */
 
$loadedLangsArray = array();
 

function div($a, $b) {         

// echo 'a: ' . $a .'<br>.b: ' . $b.'<br><Br>';

    if($b === 0 || $a === 0 || empty($a) || empty($b))
      return 0;

    return $a/$b;
}
 
 function getStaticReportMonths() {
	global $set;
	$paramFromSettings = isset($set->affiliateStaticReportMonths) && !empty($set->affiliateStaticReportMonths) ? $set->affiliateStaticReportMonths : 3;	
	return $paramFromSettings;
	 
 }
 
 
 
 function addDayToDate($date){
	 
	$MonthsAgooDate = date('Y-m-d', strtotime($date. ' + 1 days')); 
	return $MonthsAgooDate;
 }
 
 function decreaseDayFromDate($date){
	 
	$MonthsAgooDate = date('Y-m-d', strtotime($date. ' - 1 days')); 
	return $MonthsAgooDate;
 }
 
 function getXmonthsAgoDate($monthAgo){
	 
	$MonthsAgooDate = date("Y-m-d" ,strtotime("-".$monthAgo." months")); 
	return $MonthsAgooDate;
 }
 
 function isDateConsiderStatic($rdate) {
	
	$paramFromSettings = getStaticReportMonths();
	$MonthsAgooDate = getXmonthsAgoDate($paramFromSettings);
	
	 if ($rdate<$MonthsAgooDate)
		 return true;
	 return false;
}

 
 function encrypt_decrypt($action, $string) {
    $output = false;

    $encrypt_method = "AES-256-CBC";
    $secret_key = '5e4wl2a1q433465tg76ft76frt76dr7r';
    $secret_iv = '6f8ifd78tf7td76td76tdf76';

    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);

    if( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    }
    else if( $action == 'decrypt' ){
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }

    return $output;
}


function changeDate($date,$change){ 
	$date =  date("Y-m-d", strtotime($change." Month",strtotime($date)));//.' 23:59:59';
	return $date;
	// $int = new TimeInterval($change, TimeInterval::MONTH);
	// $date = date_create($date);
	// $future = $int->addToDate($date, true);
	// return $future->format('Y-m-d');
}



function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}




 
function translateInnerWord($strResource, $strParam)
{
    if (false !== stripos($strResource, $strParam)) {
        $strTranslated = lang($strParam);
        $strResource   = str_ireplace($strParam, $strTranslated, $strResource);
    }
    
    return $strResource;
}



function StripTags($txt) {
	global $set;
	
	$text=strip_tags($txt,"<p><a><b><u><i><img><font><span><h1><h2><h3><h4><h5><h6><strong><br><br /><li><ol><ul><hr /><div><span><object><embed><table><tr><td>");
	$text=str_replace('\\','', $text);
	$text=str_replace("&nbsp;"," ", $text);
	$text=str_replace("<br>","<br />", $text);
	return $text;
	}
	
function LatinReplace($string) {
	global $set;
	$find=Array('"','\'');
	$replace=Array("&#34;","&#39;");
	$string = str_replace($find,$replace,$string);
	$string = StripTags($string);
	return $string;
	}
	
function LatinReplaceTurn($string) {
	global $set;
	$find=Array("&#34;","&#39;");
	$replace=Array('"','\'');
	$string = str_replace($find,$replace,$string);
	$string = StripTags($string);
	return $string;
	}
	
function charLimit($string,$limit="200") {
	$string=StripTags($string);
	$string=str_replace("&quot;",'"',$string);
	if (strlen($string) <= $limit) $string=$string; else $string=trim(substr($string,0,$limit)).'...';
	return $string;
	}

function lang($title) {
	global $set,$loadedLangsArray;
	if (empty($title))
		return $title;

	
	$set->userInfo['lang'] = isset($_GET['lang']) && !empty($_GET['lang']) ? $_GET['lang'] : $set->userInfo['lang'];
	if ($set->userInfo['lang']=='ENG')
		return $title;

	$title = strip_tags($title);
	$title = (trim($title));
	
	$source = trim(strtolower($title));
	$ww = getLang($source);
	
	if ($ww['id']) {
		// $title = $ww['lang'.(strlen($set->userInfo['lang']) >= 3 ? $set->userInfo['lang'] : 'ENG')];
		$title = $ww['lang'.(strlen($set->userInfo['lang']) >= 3 ? $set->userInfo['lang'] : $set->defaultLangOfSystem)];
		if (!$title) $title = $ww['langENG'];
		} else {

		
		$qry = "INSERT INTO translate (`source`,`langENG`) VALUES ('".$title."','".$title."')";
		function_mysql_query($qry,__FILE__,__FUNCTION__);
		//"INSERT INTO translate VALUES (NULL,'".$title."','".$title."','','','','','','','','','')");
		}
	return $title;
	}
	
function ptitle($title,$override_producttype='') {
	global $set;
	$title = mysql_real_escape_string(trim($title));
	$qq=function_mysql_query("SELECT * FROM producttitles WHERE lower(source)='".trim(strtolower($title))."'",__FILE__,__FUNCTION__);
	$ww=mysql_fetch_assoc($qq);
	if ($ww['id']) {
		//die($set->userInfo['productType']);

		$ptype = $ww[$set->userInfo['productType']] ;
		if (!empty($override_producttype)) {
			$ptype = $ww[$override_producttype];
			// if (isset($_GET['dbg']) && strtolower($title=='brokers')) {
			// var_dump($ww);
			// echo '0: ' . $override_producttype. '<br><br>';
		    
			// }
		}
		//$title = strlen($set->userInfo['productType']) > 0 ? $ptype : $title;
		$title = strlen($ptype) > 0 ? $ptype : $title;
		// if (isset($_GET['dbg'])) die ($title);
		
		//die ($title);
		if (!$title) 
			$title = $ww['source'];
		//return 'aaa'.$title;
	} else {
		$qry = "INSERT INTO producttitles VALUES (NULL,'".$title."','','','','','','','','','','','')";
		//INSERT INTO `affiliat_demo`.`producttitles` (`id`, `source`, `Casino`, `Sports`, `BinaryOption`, `Forex`, `Download`, `Gaming`, `Mobile`, `Ecommerce`, `Dating`) VALUES (NULL, 'merchant', '', '', '', '', '', '', '', '', '');
		//die ($qry);
		function_mysql_query($qry,__FILE__,__FUNCTION__);
	}
		
	return $title;
}
	
	
	
function price($price=0) {
	global $set;
	$num = @number_format($price,2);
	if ($num < 0) $color = 'red';
		else if ($num > 0) $color = 'green';
		else $color = 'none';
	return '<span style="color: '.$color.';">'. $set->currency. ' ' . $num.'</span>';
	}
	
function boxMsg($title='',$msg='') {
	$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td><img border="0" src="images/table/green_left.jpg" alt="" /></td>
					<td width="99%" align="left" style="background: #64B23B; font-weight: bold; font-size: 16px; color: #FFF;">'.$title.'</td>
					<td><img border="0" src="images/table/green_right.jpg" alt="" /></td>
				</tr><tr>
					<td colspan="3" align="left" valign="top" style="border-left: 1px #D9D8D9 solid; padding: 10px; font-size: 14px; color: #5C5C5C; line-height: 20px; border-right: 1px #D9D8D9 solid; border-bottom: 1px #D9D8D9 solid; background: #F9FEF6;">'.nl2br($msg).'</td>
				</tr>
			</table>';
	return $html;
	}
	
        

$set->ProFeatureTooltip = ' title="' . lang('This feature is not active on basic version').'.  ' . lang('Contact our support to upgrade your software').'."  class="feature-tooltip" '  ;


/* function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
} */

function getLang($source){
	global $loadedLangsArray;
		if (empty($source))
			return $source;

		if (isset($loadedLangsArray[$source]) ) {
				$langRow = 					$loadedLangsArray[$source] ;
		}
		else {
					
					$qry = "SELECT * FROM translate WHERE lower(source)=lower('". mysql_real_escape_string($source)."')";
					
					$qq=function_mysql_query($qry,__FILE__,__FUNCTION__) ;
                    $langRow   = mysql_fetch_assoc($qq);
					
					
					$loadedLangsArray[$source] = $langRow;
		}
		return $langRow;
}

function hexentities($str) {
    $return = '';
    for($i = 0; $i < strlen($str); $i++) {
        // $return .= '&#x'.bin2hex(substr($str, $i, 1)).';';
        // $return .= '&#'.bin2hex(substr($str, $i, 1)).'';
        $return .= '&#'.ord(substr($str, $i, 1)).'';
    }
    return $return;
}

/* $encoded = hexentities('Test');
var_dump($encoded);
$file = 'encodeing.txt';
// Open the file to get existing content
$current = file_get_contents($file);
// Append a new person to the file
$current .= hexentities('Test').'\n\r'; */

function splitStringByCapitalLetters($camelCaseString) {
        $re = '/(?<=[a-z])(?=[A-Z])/x';
        $a = preg_split($re, $camelCaseString);
        return join($a, " " );
}




 function writeToLog($text,$fileName="log")
{
  $filename = $fileName.".log";
  $fh = fopen($filename, "a") or die("Could not open log file.");
  fwrite($fh, date("d-m-Y, H:i")." - " . $text. "\n") or die("Could not write file!");
  fclose($fh);
}  

?>