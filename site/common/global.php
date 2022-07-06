<?php
@session_start();

ob_start();
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

/**
 * If the input date is in d-m-Y format - then converts to Y-m-d, otherwise - does nothing.
 *
 * @param  string $strDate
 * @return string
 */
 
 function messagesSearchNReplace($managerRow=array(),$text){
	 global $set;
	 $ww= $managerRow;
	 // var_dump($ww);
	 // die();

$brandname = $set->webTitle;
// die ($brandname);
	// $find = Array("{custom_field}","{affiliate_username}","{affiliate_name}","{affiliate_email}","{accountManager_name}","{accountManager_email}","{accountManager_IM}","{brand_name}","{Brand Name}","{affiliate_password}", "{web_address}", "{web_mail}", "{aff_uname}", "{aff_fname}", "{merchant_name}", "{ticket_id}", "{base_url}", "{first_name}", "{last_name}");
	// $replace = Array($set->custom_field,$ww['username'],$ww['first_name'].' '.$ww['last_name'],$ww['mail'],$set->userInfo['first_name'].' '.$set->userInfo['last_name'],$set->userInfo['email'],$set->userInfo['IMUser'],$brandname,$brandname, $ww, $set->webAddress, $set->webMail, $set->aff_uname, $set->aff_fname, $merchantName, $ticketID, $set->webAddress, $FirstName, $LastName);

// $ww = getAffiliateRow($set->userInfo['id']);	
			
	$find = Array("{custom_field}","{affiliate_username}","{accountManager_name}","{accountManager_email}","{affiliate_name}","{affiliate_email}","{accountManager_IM}","{brand_name}","{Brand Name}","{affiliate_password}", "{web_address}", "{web_mail}", "{aff_uname}", "{aff_fname}", "{merchant_name}", "{ticket_id}", "{base_url}", "{first_name}", "{last_name}");
	$replace = Array($set->custom_field,$set->userInfo['username'],$ww['first_name'].' '.$ww['last_name'],$ww['email'],$set->userInfo['first_name'].' '.$set->userInfo['last_name'],$set->userInfo['mail'],$ww['IMUser'],$brandname,$brandname, $ww, $set->webAddress, $set->webMail, $set->aff_uname, $set->aff_fname, $merchantName, $ticketID, $set->webAddress, $FirstName, $LastName);
	// var_dump($ww);
	// die();
	return str_replace($find,$replace,$text);
 }					
 
 function commonGlobalSetYmd($strDate)
{
	$arrDate  = explode(' ', $strDate);
	$strDate  = $arrDate[0];
	$arrDate  = explode('-', $strDate);
	
	if (2 == strlen($arrDate[0])) {
		// Current format is d-m-Y.
		$strDate  = $arrDate[0];
		$strMonth = $arrDate[1];
		$strYear  = $arrDate[2];
		
		return $strYear . '-' . $strMonth . '-' . $strDate;
		
	} else {
		// Current format is Y-m-d.
		return $strDate;
	}
}


/**
 * Q&D walkaround SQL-calendars bug.
 *
 * @param  string &$from
 * @param  string &$to
 * @return void
 */
function commonGlobalSetTimeRange(&$from, &$to)
{
	$from = commonGlobalSetYmd($from);
	$to   = commonGlobalSetYmd($to) . ' 23:59:59';
}


// session_cache_expire(30);

global $set;



extract($_GET); extract($_POST); extract($_SERVER);

date_default_timezone_set('Atlantic/Azores'); // Summer Clock
// date_default_timezone_set('Europe/Dublin'); // Winter Clock

require_once('common/database.php');
require_once('common/config.php');
require_once('common/theme.php');
require_once('common/aes/aes.class.php');     // AES PHP implementation
require_once('common/aes/aesctr.class.php');  // AES Counter Mode implementation
// require_once('log_auth.php');

$set->captureAffiliateLogs = 1;


	
$from = commonGlobalSetYmd($from);
$to   = commonGlobalSetYmd($to);


	if (strpos($set->basepage, '/admin') === false AND strpos($set->basepage, '/manager') === false AND $from) {
		$from       = str_replace('%2F', '/', $from);  // %2F - url-date separator.
		$chkMinDate = explode('/', $from);
		$strtotime  = 0;
		
		if (1 == count($chkMinDate)) {
			$strtotime = strtotime($chkMinDate[0]);
		} else {
			$strtotime = strtotime($chkMinDate[2] . '/' . $chkMinDate[1] . '/' . $chkMinDate[0]);
		}
		
		/*if (isset($_GET['debug'])) {
			print_r($chkMinDate);
			die;
			echo 'strtotime: ', strtotime('10/01/2014'), '<br>',
				 'strtotime: ', strtotime($chkMinDate[2] . '/' . $chkMinDate[1] . '/' . $chkMinDate[0]);
			die;
		}*/
		
		if (strtotime('10/01/2014') > $strtotime) {
			$result = mysql_fetch_assoc(mysql_query("SELECT showDataForAffiliateSince FROM merchants LIMIT 0, 1"));
			$from   = $result['showDataForAffiliateSince'];
		}
	}
	
	// OLD VERSION
	/*if (strpos($set->basepage, '/admin') === false AND strpos($set->basepage, '/manager') === false AND $from) {
		$chkMinDate = explode('/',$from);
		
		if (strtotime('10/01/2014')>strtotime($chkMinDate[2].'/'.$chkMinDate[1].'/'.$chkMinDate[0])) {
			$from = '01/10/2014';
			$to = '01/10/2014';
		}
		
		$from .= ' 00:00:00';
	}*/
	
	
	if($to){
		$to.= ' 23:59:59';
	}
	
	if($sdate){
		$sdate.= ' 00:00:00';
	}
	if($edate){
		$edate.= ' 23:59:59';
	}
//}






/////////////////////////////////////////////////////////// AES ENCRYPTION HANDLING
$aesKey = 'ilikebfp8';

function aesEnc($str){
	return AesCtr::encrypt($str, $aesKey, 256);
}

function aesDec($str){
	return AesCtr::decrypt($str, $aesKey, 256);
};

/////////////////////////////////////////////////////////// END OF AES ENCRYPTION HANDLING

$set->itemsLimit = 15;

if ($fedDir = opendir('func/'))
	while (($file=readdir($fedDir)) !== false) 
		if ($file != "." AND $file != ".." AND file_exists('func/'.$file)) 
			include_once('func/'.$file);
			
$set->getFolder = explode("/",$_SERVER['SCRIPT_NAME']);
if (is_dir($set->getFolder[1])) {
	if ($fedDir = opendir($set->getFolder[1].'/func/')) while (($file=readdir($fedDir)) !== false) if ($file != "." AND $file != ".." AND file_exists($set->getFolder[1].'/func/'.$file)) include_once($set->getFolder[1].'/func/'.$file);

	if ($set->getFolder[1] == "admin") {
		$set->userInfo = adminInfo();
		updateUnit("admins","lastactive='".dbDate()."',logged='1'","id='".$set->userInfo['id']."'");
		} else if ($set->getFolder[1] == "affiliate") {
			if($set->captureAffiliateLogs){
				writeToLog("1. Common Global - before affiliateInfo() - set->userInfo[id]---- " .$set->userInfo['id']);
			}
		$set->userInfo = affiliateInfo();
			if($set->captureAffiliateLogs){
				writeToLog("2. Common Global - before affiliateInfo() - set->userInfo[id]---- " .$set->userInfo['id']);
			}
		updateUnit("affiliates","lastactive='".dbDate()."',logged='1'","id='".$set->userInfo['id']."'");
		} else if ($set->getFolder[1] == "manager") {
		
		$set->userInfo = managerInfo();
		updateUnit("admins","lastactive='".dbDate()."',logged='1'","id='".$set->userInfo['id']."'");
		}
		else if ($set->getFolder[1] == "advertiser") {
		
		$set->userInfo = advertiserInfo();
		updateUnit("admins","lastactive='".dbDate()."',logged='1'","id='".$set->userInfo['id']."'");
		}
		
	}
	
	//moved function after including all the func files to get the value of login_session_duration from settings table
	sessionX($set->login_session_duration);

if (strtolower($set->userInfo['preferedCurrency'])!='usd') {
	
	$resulta=mysql_query("SELECT * FROM  `exchange_rates` ");
	// $row= mysql_fetch_assoc($resulta);
	while( $row = mysql_fetch_assoc( $resulta)){
    $var[] = $row; // Inside while loop
}
	
	$set->currencies=$var;
	
}




function encode($string) {
	$string=base64_encode($string);
	return $string;
	}

function decode($string) {
	$string=base64_decode($string);
	return $string;
	}
	

$isNetworkQ = mysql_fetch_assoc(mysql_query('SELECT isNetwork FROM settings'));

$set->isNetwork = $isNetworkQ['isNetwork'];


$multiMerchantsQ = mysql_fetch_assoc(mysql_query('SELECT multiMerchantsPerTrader FROM settings'));

$set->multiMerchants = $multiMerchantsQ['multiMerchantsPerTrader'];


//die();
if($set->userInfo['level']=='manager' AND $set->isNetwork){
	if(isset($_REQUEST['mid']) AND aesDec($_REQUEST['mid'])!=aesDec($_COOKIE['mid'])){
		_goto($set->basepage.'?merchant_id='.aesDec($_COOKIE['mid']));
	}
} 


function switchRdateAccordingToTradeingPlatformTime($rdate) {
	global $set;
	$a = date( "Y-m-d H:i:s", strtotime( $rdate ." +".$set->cronRecordsTimeDif." hours" ) ); 
	return $a;
	
}



//countryLONG
//countrySHORT

# Session Logout after in activity 
function sessionX($timeout){ 
// var_dump($timeout);
// echo '<br><Br>';

    $logLength = $timeout * 60; # time in seconds :: 3600 = 60 minutes 
    
    $currentTime = strtotime("now"); # Create a time from a string 
    # If no session time is created, create one 
	
    if(!isset($_SESSION['sessionX'])){  
        # create session time 
        $_SESSION['sessionX'] = $currentTime;  
		
    }else{ 
	    # Check if they have exceded the time limit of inactivity 
		
        if(((strtotime("now") - $_SESSION['sessionX']) > $logLength) && isset($_SESSION['session_id'])){ 
            # If exceded the time, log the user out 
            // unset($_SESSION);
			session_unset();
			session_destroy();
			header("Location: " . $set->basepage);
        }else{ 
            # If they have not exceded the time limit of inactivity, keep them logged in 
            $_SESSION['sessionX'] = $currentTime; 
        } 
    } 
} 



	
	$set->userRealIP = getRealIP();
	

	
ob_end_clean();
?>