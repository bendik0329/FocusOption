<?php
// require('common/global.php');
require('common/database.php');
require('common/config.php');
require('func/func_common.php');
require('func/func_string.php');
// require('func/func_debug.php');
$queryString = $_SERVER['QUERY_STRING'];
if (isHackingAttempt($queryString))	{
    writeToLog('**SERVER**','HackingAttempt');
writeToLog(print_r($_SERVER,TRUE),'HackingAttempt');
writeToLog('**GET**','HackingAttempt');
writeToLog(print_r($_GET,TRUE),'HackingAttempt');
writeToLog('**POST**','HackingAttempt');
writeToLog(print_r($_POST,TRUE),'HackingAttempt');
writeToLog('-------------------------','HackingAttempt');
writeToLog('-------------------------','HackingAttempt');	

	die('<div class="403"></div>');
}

$writeLog = isset($set->writeFinalTrackingUrlToLog) && $set->writeFinalTrackingUrlToLog==1? true: false;

$justOutputFinalURL = isset($_GET['justOutputFinalURL'])  ? $_GET['justOutputFinalURL'] : false;


$appTable = "traffic";
$postbackparam =0;
$debug =isset($_GET['debug']) ? $_GET['debug'] : 0;

/* $dynamic_params = array();
$p1 =isset($_GET['param']) ? mysql_real_escape_string($_GET['param']) : "";
if (empty($p1))
$p1 =isset($_GET['p1']) ? mysql_real_escape_string($_GET['p1']) : "";

$p2 =isset($_GET['p2']) ? mysql_real_escape_string($_GET['p2']) : "";
if (empty($p2)){
	if (isset($_GET['subid'])) {
		$p2 = mysql_real_escape_string($_GET['subid']);
	}
}

$p3 =isset($_GET['p3']) ? mysql_real_escape_string($_GET['p3']) : "";
$p4 =isset($_GET['p4']) ? mysql_real_escape_string($_GET['p4']) : "";
$p5 =isset($_GET['p5']) ? mysql_real_escape_string($_GET['p5']) : "";

 */
 
 $dynamic_params = array();
$p1 =isset($_GET['param']) ? html_escape($_GET['param']) : "";
if (empty($p1))
$p1 =isset($_GET['p1']) ? html_escape($_GET['p1']) : "";

$p2 =isset($_GET['p2']) ? html_escape($_GET['p2']) : "";
if (empty($p2)){
	if (isset($_GET['subid'])) {
		$p2 = html_escape($_GET['subid']);
	}
}

$p3 =isset($_GET['p3']) ? html_escape($_GET['p3']) : "";
$p4 =isset($_GET['p4']) ? html_escape($_GET['p4']) : "";
$p5 =isset($_GET['p5']) ? html_escape($_GET['p5']) : "";


$dynamic_params_json="";
if (
!empty($p1) ||
!empty($p2) ||
!empty($p3) ||
!empty($p4) ||
!empty($p5)
){
$dynamic_params= array("p1"=>$p1 ,"p2"=>$p2 ,"p3"=>$p3 ,"p4"=>$p4 ,"p5"=>$p5 );
$dynamic_params_json = json_encode($dynamic_params);

}
$getip = $_SERVER["HTTP_CF_CONNECTING_IP"];
if (empty($getip))
$getip	= getRealIP();



$ctag=strip_tags(str_replace("=","",$_GET['ctag']));
// $ctag = mysql_real_escape_string($ctag);
$ctag = html_escape($ctag);
// $exp=explode("-",$ctag);
// $affiliate_id=substr($exp[0],1);


//  new method
$ctagArray = array();
//$ctag=$_GET['ctag'];
$ctagArray = getBtag($ctag);




if (empty($ctagArray)) {

getDefaultAffiliateID('1');
$ctagArray['affiliate_id']=	 $set->defaultAffiliateID;
$ctagArray['banner_id']=	1;
$ctagArray['group_id']=	0;
$ctagArray['profile_id']=	0;
$ctagArray['country']=	"";
$ctagArray['uid']=	0;

	
}
if (empty($ctagArray['affiliate_id'])) {
	getDefaultAffiliateID('2');
		$ctagArray['affiliate_id']=	 $set->defaultAffiliateID;
}
if (empty($ctagArray['banner_id'])) {
		$ctagArray['banner_id']=	1;
		
}
	

$affiliate_id=$ctagArray['affiliate_id'];


if ($affiliate_id=="" || !is_numeric($affiliate_id)) {
	$affiliate_id=$defAffID['id'];
	$ctagArray['affiliate_id'] = $affiliate_id;
}
$banner_id=$ctagArray['banner_id'];
if ($banner_id=="" || !is_numeric($banner_id)) {
	$banner_id=0;
	$ctagArray['banner_id'] = $banner_id;
}
$profile_id=$ctagArray['profile_id'];
if ($profile_id=="" || !is_numeric($profile_id)) {
	$profile_id=0;
	$ctagArray['profile_id'] = $profile_id;
}






$group_id=$ctagArray['group_id'];


$country=$ctagArray['country'];

if (!$justOutputFinalURL)
	$uid=$ctagArray['uid'];
else
	$uid = 0;



// $freeParam= mysql_real_escape_string($ctagArray['freeParam']);
$freeParam= html_escape($ctagArray['freeParam']);
if (!empty($p1) && empty($freeParam))
	$freeParam=$p1;


if (!$justOutputFinalURL)
$uid = get_unique_int();


// dynamic tracker part 
function CreateOverrideDynamicTracker ($affiliate_id=0 , $uid=0, $tracker='') {
	if ($affiliate_id>0 && strlen($uid)>0) {
			$dynamicTracker = null;//GetOverrideDynamicTracker($affiliate_id,$uid,date('Y-m-d H:i:s'));
			
			if (empty($dynamicTracker) && strlen($tracker)>0) {
				$qry = "INSERT INTO `TrackerConversion`(`affiliate_id`, `uid`, `DynamicTracker`) VALUES (". $affiliate_id . ", '" . $uid . "','" . $tracker . "');";
				$resource = function_mysql_query($qry,__FILE__);
				return $tracker;
			}
			else if ($tracker=='') {
				return -1;
			}
			
			else	{
				return ($dynamicTracker);
			}
	}
	return -1;
}
/* function oGetOverrideDynamicTracker ($affiliate_id=0 , $uid=0) {
	if ($affiliate_id>0 && strlen($uid)>0) {
		// $qry = "select  DynamicTracker from TrackerConversion where affiliate_id = " . $affiliate_id . " and uid= '" . $uid."'";
		$qry = "select  DynamicTracker from TrackerConversion where affiliate_id = " . $affiliate_id . " and uid= " . $uid . " order by id desc limit 1 ;";
		
		$resource = function_mysql_query($qry,__FILE__);
		$row = mysql_fetch_assoc($resource);
		if (!empty($row)) {
			return ($row['DynamicTracker']);
		}
	}
			return NULL;
}
 */





$removeSubid= false;
if (strtolower($p2)=='{subid}'){
	$removeSubid= true;
}
else {
	$removeSubid=false;
	
	if (!empty($dynamic_params_json) && !$justOutputFinalURL)
	$rslt = CreateOverrideDynamicTracker($affiliate_id,$uid,$dynamic_params_json);
}
if (empty($p1))
		$p1 = $p2;

//end of dynamic tracker part
	


$additionalExternalParamsFromTracker='';
$additionalExternalParamsFromTracker=($set->uri);
$additionalExternalParamsFromTracker=str_replace('&justOutputFinalURL=1','',$additionalExternalParamsFromTracker);


if ($additionalExternalParamsFromTracker!='') {
	
	$onlyParams = explode('.php?',$additionalExternalParamsFromTracker);
	$additionalExternalParamsFromTracker= $onlyParams[1];
	
	if ($ctag!='') {
	$additionalExternalParamsFromTracker = explode('ctag='.$ctag,$additionalExternalParamsFromTracker);
	// var_dump($additionalExternalParamsFromTracker);
	// die();
	$additionalExternalParamsFromTracker = rtrim($additionalExternalParamsFromTracker[0],'&') .$additionalExternalParamsFromTracker[1];
	$additionalExternalParamsFromTracker = str_replace('&ctag=','',$additionalExternalParamsFromTracker);
	$additionalExternalParamsFromTracker = str_replace('ctag=&','',$additionalExternalParamsFromTracker);
	}
	

}



if ($_GET['random']) {
	$banner=mysql_fetch_assoc(function_mysql_query("SELECT id FROM merchants_creative WHERE valid='1' ORDER BY RAND()",__FILE__));
	$banner_id = $banner['id'];
	} else {
		$banner_id=$banner_id;
	}
	
$profile_id=$profile_id;
$cookieName='affClickCtag_'.$affiliate_id.$banner_id.$profile_id;
// $getAffiliate = dbGet($affiliate_id,"affiliates");

// $getProfile = dbGet($profile_id,"affiliates_profiles");





if ($banner_id) {

	//$q = "select mc.*, l.title as languagename from merchants_creative mc left join languages l on l.id = mc.language_id where mc.id = " . $banner_id;
	/* $q = "select mp.affiliate_id as promotion_affiliate_id, mc.*, l.title as languagename from merchants_creative mc 
							INNER JOIN merchants m on m.id=mc.merchant_id 
							left join merchants_promotions mp  on  mc.promotion_id= mp.id and mp.valid=1    
							left join languages l on l.id = mc.language_id 
							where mc.id = " . $banner_id . ($affiliate_id==$set->defaultAffiliateID ? "" : " and m.valid=1"); */
	/* $q = "select p.valid as product_valid, m.valid as merchant_valid , mp.affiliate_id as promotion_affiliate_id, mc.*, l.title as languagename from merchants_creative mc 
							left JOIN merchants m on m.id=mc.merchant_id 
							left JOIN products_items p on p.id=mc.product_id 
							left join merchants_promotions mp  on  mc.promotion_id= mp.id and mp.valid=1    
							left join languages l on l.id = mc.language_id 
							where mc.id = " . $banner_id . ($affiliate_id==$set->defaultAffiliateID ? "" : " and m.valid=1");
							 */
	
	// getDefaultAffiliateID('3');
/* 	$q = "select p.valid as product_valid, m.valid as merchant_valid , mp.affiliate_id as promotion_affiliate_id, mc.*, l.title as languagename from merchants_creative mc 
							left JOIN merchants m on m.id=mc.merchant_id " . ($affiliate_id==$set->defaultAffiliateID ? "" : "and m.valid=1 ") . " 
							left JOIN products_items p on p.id=mc.product_id 
							left join merchants_promotions mp  on  mc.promotion_id= mp.id and mp.valid=1    
							left join languages l on l.id = mc.language_id 
							where mc.id = " . $banner_id ; */
							
							$q = "select p.valid as product_valid, m.valid as merchant_valid , mp.affiliate_id as promotion_affiliate_id, mc.*, l.title as languagename from merchants_creative mc 
							left JOIN merchants m on m.id=mc.merchant_id  and m.valid=1  
							left JOIN products_items p on p.id=mc.product_id 
							left join merchants_promotions mp  on  mc.promotion_id= mp.id and mp.valid=1    
							left join languages l on l.id = mc.language_id 
							where mc.id = " . $banner_id ;
							
							
	// die ($q);
	$getBanner = mysql_fetch_assoc(mysql_query($q));
	



}
 //var_dump($getBanner);
 //die();

/*
// avoid performance issue
 if ($affiliate_id ==$set->defaultAffiliateID)
	 $allowPromotion= true;
 else
	 */
 $allowPromotion = ((($getBanner['promotion_affiliate_id']==$affiliate_id&& $getBanner['promotion_id']>0) || $getBanner['promotion_id']==0)  || $getBanner['promotion_affiliate_id']==0);
 // $allowPromotion = ((($getBanner['promotion_affiliate_id']==$affiliate_id&& $getBanner['promotion_id']>0) || $getBanner['promotion_id']==0));

 
 /*
 // avoid performance issue
if ($affiliate_id ==$set->defaultAffiliateID) {
	
}
else 
	*/
{

	if (!isset($getBanner['valid']) && $allowPromotion){
		$non_valid_merchant_id = -2;
		$non_valid_product_id = -2;
	}
	else if ($getBanner['valid']<1 || !$allowPromotion){
		$non_valid_merchant_id = $getBanner['merchant_id'];
		$non_valid_product_id = $getBanner['product_id'];
		unset($getBanner);
	}else {
	$non_valid_merchant_id=-1;	
	$non_valid_product_id = -1;
	}
}


// $getBanner = dbGet($banner_id,"merchants_creative");


if (!$getBanner['id']) {
	
	
	if ($non_valid_product_id>0){

	getDefaultAffiliateID('4');
	$crtvQuery = 	"
	(SELECT mc.* FROM merchants_creative mc left join products_items p on p.id = mc.product_id WHERE mc.product_id = ".$non_valid_product_id." ". ($affiliate_id==$set->defaultAffiliateID ? "" : " and p.valid=1 and mc.valid='1' " )." ORDER BY RAND() limit 1)
	union
	(SELECT mc.* FROM merchants_creative mc left join products_items p on p.id = mc.product_id WHERE mc.product_id <> ".$non_valid_product_id."  ".($affiliate_id==$set->defaultAffiliateID ? "" : " and p.valid=1 and mc.valid='1' "). " ORDER BY RAND() limit 1)
	";
	
	}
	else {
		getDefaultAffiliateID('5');
	$crtvQuery = 	"
	(SELECT mp.affiliate_id as promotion_affiliate_id,mc.* FROM merchants_creative mc left join merchants m on m.id = mc.merchant_id  left join merchants_promotions mp  on  mc.promotion_id= mp.id and mp.valid=1    WHERE (mp.affiliate_id = " . $affiliate_id  . " or mc.promotion_id=0 ) and mc.merchant_id = ".$non_valid_merchant_id." " . ($affiliate_id==$set->defaultAffiliateID ? "" : " and m.valid=1 and mc.valid='1' "). " ORDER BY RAND() limit 1)
	union
	(SELECT mp.affiliate_id as promotion_affiliate_id,mc.* FROM merchants_creative mc left join merchants m on m.id = mc.merchant_id  left join merchants_promotions mp  on  mc.promotion_id= mp.id and mp.valid=1   WHERE (mp.affiliate_id = " . $affiliate_id  . " or mc.promotion_id=0 ) and mc.merchant_id <> ".$non_valid_merchant_id." " . ($affiliate_id==$set->defaultAffiliateID ? "" : " and m.valid=1 and mc.valid='1' "). " ORDER BY RAND() limit 1)
	";
// die 	('<br>'.$crtvQuery);
}

// $allowPromotion = (($getBanner['promotion_affiliate_id']==$affiliate_id&& $getBanner['promotion_id']>0) || $getBanner['promotion_id']==0);
	$banner=mysql_fetch_assoc(function_mysql_query($crtvQuery));
	$getBanner = $banner;
	$banner_id = $banner['id'];
	
	// $getBanner = dbGet($banner_id,"merchants_creative");
	}

	
$isProductSession = false;
if ($getBanner['merchant_id']==0 && ($getBanner['product_id']>0)) {
	$q = "select *,param as params ,exportUniqueIdWithName as product_click_uid ,id as product_id from products_items where id = " . $getBanner['product_id'];
	// die ($q);
	$getMerchant = mysql_fetch_assoc(mysql_query($q));
	$isProductSession = true;
}
else {
// $getMerchant = dbGet($getBanner['merchant_id'],"merchants");
$getMerchant = mysql_fetch_assoc(function_mysql_query("select id,campaignparamname,campaignispartofparams,params,campaignid,extraMemberParamName , extraMemberParamValue from merchants where id =  " . $getBanner['merchant_id'] . " limit 1; " ));
}

//if (!$getMerchant['id'] OR !$getMerchant['valid']) $miss=1;

$userCountry = $_SERVER['HTTP_CF_IPCOUNTRY'];
$country_id = $userCountry;

if (empty($userCountry)){
	$userCountry = getIPCountry();
	if ($userCountry['countrySHORT']) {
		$ctag .= '-c'.$userCountry['countrySHORT'];
		$country_id = $userCountry['countrySHORT'];
	}
}
if (trim($country_id)=='-')
	$country_id="";


if (!$isProductSession)
{
	if ($profile_id && $affiliate_id) {
	$qry = "select af.*,acr.campID as campID from affiliates af left join affiliates_campaigns_relations acr on acr.affiliateID = af.id  " . ($profile_id>0 ? " and acr.profile_id = ".$profile_id : "" ) . " where merchantid = ". $getMerchant['id'] ."  and af.id = " . $affiliate_id. "  limit 1; ";
	// die ($qry);
	$getAffiliate = mysql_fetch_assoc(function_mysql_query($qry,__FILE__)); //,"affiliates");
	}
	if (!$getAffiliate['id']) {
	// $qry = "select af.*,acr.campID as campID from affiliates af left join affiliates_campaigns_relations acr on acr.affiliateID = af.id   and acr.isDefaultCamp=1 where merchant_id = ". $getMerchant['id'] ." and af.id = " . $affiliate_id. " limit 1; ";
	$qry = "select af.*,acr.campID as campID from affiliates af left join affiliates_campaigns_relations acr on acr.affiliateID = af.id   and  merchantid = ". $getMerchant['id'] ." where 1=1 and af.id = " . $affiliate_id. " order by acr.isDefaultCamp desc limit 1; ";
	// die ($qry);
		$getAffiliate = mysql_fetch_assoc(function_mysql_query($qry,__FILE__)); //,"affiliates");
	}
}else{
	
	if ($affiliate_id) {
	$qry = "select af.* from affiliates af where 1=1 and af.id = " . $affiliate_id. "  limit 1; ";
	$getAffiliate = mysql_fetch_assoc(function_mysql_query($qry,__FILE__)); //,"affiliates");
	}
}

if ($getAffiliate['valid']<0){
	$status = $getAffiliate['valid']==-1 ? "deleted" : "rejected";
	die ('You are not allowed to push new traffic into the system since your account status is '.$status.', please contact your account manager if you wish to send new traffic');
}

if (isset($getAffiliate['blockNewTraffic']) && $getAffiliate['blockNewTraffic']==1)
	die ('You are not allowed to push new traffic into the system, please contact your account manager if you wish to send new traffic');



if (!empty($getAffiliate)){
	$group_id = $getAffiliate['group_id'];
	$ctagArray['group_id'] = $group_id;
}
// if ($_GET['debug']) {
	// var_dump($getAffiliate);
	// die();
// }
$ctag = "a". $affiliate_id . "-" . "b". $banner_id . "-" . "p". $profile_id . "-" . "c". $country_id . "-" . "u". $uid . (!empty($freeParam) ? "-f". $freeParam : "" ); 
	
$pexp=explode(",",$getMerchant['params']);
$parse_url=parse_url($getBanner['url']);



$queryStringParams = array();

if ($parse_url['fragment']) {
				
						$ex = explode ('?',$parse_url['fragment']);
						if (isset($ex[1]))
						$queryStringParams[] = $ex[1];
				
}
				
				
for ($i=0; $i<=count($pexp)-1; $i++) {
	if ($i == "0") {
		if (!$parse_url['query']) {
			
			// $moreParams .= '?'.$pexp[$i].'='.$ctag;
			$moreParams .= '&'.$pexp[$i].'='.$ctag;
		}
		else 
			$moreParams .= '&'.$pexp[$i].'='.$ctag;
		} 
		else {
			$moreParams .= '&'.$pexp[$i].'='.$ctag;
		}
}

if (isset($getMerchant['product_click_uid']) && !empty($getMerchant['product_click_uid'])){
	
	$moreParams .= '&' . $getMerchant['product_click_uid'] . '=' . $uid;
	
}
	

	$finalCtag = $ctag  ;//. '-u'.$uid;
	
	
/* if (!$miss) {
	$ctag = ($ctag);
	//$getStat = mysql_fetch_assoc(function_mysql_query("SELECT * FROM ".$appTable." WHERE ctag='".$ctag."' AND rdate='".date("Y-m-d")."'",__FILE__));
	if ($getStat['id']) {
		if (!$_COOKIE[$cookieName]) {
			
			$informationArray = array();
				
			$informationArray['ip']           = $_SERVER['REMOTE_ADDR'];
			$informationArray['ctag']         = $ctag;
			$informationArray['uid']          = $uid;
			$informationArray['banner_id']    = $banner_id;
			$informationArray['group_id']     = $group_id;
			$informationArray['profile_id']   = $profile_id;
			$informationArray['affiliate_id'] = $affiliate_id;
			$informationArray['country_id']   = $country_id;
			$informationArray['merchant_id']  = $getMerchant['id'];
			$informationArray['refer_url']    = trim($_SERVER['HTTP_REFERER']);
			$informationArray['clicks']       = 1;
			$informationArray['userAgent']    = substr($_SERVER['HTTP_USER_AGENT'],0,253);
			$informationArray['type']         = 'traffic'; 
			$informationArray['param']        = $freeParam; 
			
			if ($freeParam != $param && !empty($param))
				$informationArray['param2']         = $param; 
			
			insert_new_traffic_record($informationArray);
				
			//updateUnit($appTable,"clicks=clicks+1","id='".$getStat['id']."'");
			setcookie($cookieName,substr(time(),-7)."A",time()+3600);
			}
		} else {
	
		setcookie($cookieName,substr(time(),-7)."A",time()+3600);
		}
	}

	 */
	
	if($set->activateLogs==1 && !$justOutputFinalURL){
	
	$ip =$getip;
	// die ($ip);
	if(checkUserFirewallIP($ip)){
	
			$activityLogUrl = "http://".$_SERVER['SERVER_NAME']. "/ajax/saveLogActivity.php?user_id=".$getAffiliate['id']."&ip=".$getip."&country=" . $country_id."&location=tracking&userType=affiliate&theChange=blockedAffiliateTraffic&_function_=".__FUNCTION__ . "&_file_=". __FILE__ . "&queryString=" . urlencode($queryString);
			 //die($activityLogUrl);
			doCurlPost($activityLogUrl);
			
			$url = 'http://'.$_SERVER['SERVER_NAME']."/404.php";
				// die ($url);
				header("Location: ".$url);
			die('--');
		}
		
		
	}
	
		function doCurlPost($url){
	

	$arrUrl  = explode('?', $url);
	
	$ch      = curl_init();
	
	$tmpparams = $arrUrl[1];
	$exp = explode('&',$tmpparams);
	
	$params = array();
	foreach ($exp as $item) {
		$itm = explode('=',$item);
		$params[$itm[0]] = $itm[1];
	}

	curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
	curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,20); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 50); //timeout in seconds
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	return $result;
	

}


if ($set->blockTrafficFromInactiveAffiliate==1 && $getAffiliate['valid']!=1 && $set->activateLogs==1 && !$justOutputFinalURL){

$activityLogUrl = "http://".$_SERVER['SERVER_NAME']. "/ajax/saveLogActivity.php?user_id=".$getAffiliate['id']."&ip=".$getip."&country=" . $country_id."&location=tracking&userType=affiliate&theChange=blockedAffiliateTraffic&_function_=".__FUNCTION__ . "&_file_=". __FILE__;
// die($activityLogUrl);
doCurlPost($activityLogUrl);

	
		
		$url = 'http://'.$_SERVER['SERVER_NAME']."/404.php";
		// die ($url);
		header("Location: ".$url);
	die('--');
		
}

	
	
		// campaign fields manipulation
		$campParamName=$getMerchant['campaignparamname'];
		
		$isCampInCtag=$getMerchant['campaignispartofparams'];
		$campaignID=$getMerchant['campaignid'];
		 if ($campParamName<>'' && $getAffiliate['campID']) {
			$campaignID = $getAffiliate['campID'];
			if(strpos($getBanner['url'],$campParamName)===false && !empty($campaignID)){
				if($isCampInCtag){
					if ($campParamName=='') {
					$moreParams.='|'.$campaignID;
					} else {
					$moreParams.='|'.$campParamName.'-'.$campaignID;
					}
				}
				else {
						$moreParams.='&'.$campParamName.'='.$campaignID;
						}
				}
		}
		else if ($campParamName<>'') {
			if(strpos($getBanner['url'],$campParamName)===false && !empty($campaignID)){
				if($isCampInCtag){
					if ($campParamName=='') {
					$moreParams.='|'.$campaignID;
					} else {
					$moreParams.='|'.$campParamName.'-'.$campaignID;
					}
				}
				else {
						$moreParams.='&'.$campParamName.'='.$campaignID;
						}
				}
		}
		

		$queryStringParams[] = $moreParams;

if (!$isProductSession) {

		$extraMemberParam = '';
		if($getMerchant['extraMemberParamName']!='' AND $getMerchant['extraMemberParamName']!=NULL){
			$extraMemberParam.='&'.$getMerchant['extraMemberParamName'].'=';
			if(strtolower($getMerchant['extraMemberParamValue'])==strtolower('campID')){
		if($affiliate_id){		
		$profile_id_text="";
		$orderby="";
		if ($profile_id){
			$orderby = " order by profile_id desc; " ; 
			$profile_id_text = " and profile_id in (0," . $profile_id . ") ";
		}
		$qqcamp = 'SELECT campID AS id FROM affiliates_campaigns_relations WHERE affiliateID='.$affiliate_id . ' and merchant_id = ' . $getMerchant['id']. $profile_id_text. $orderby;
		if ($debug==1) {
			echo $qqcampa	.'<br>';
		}
		$camp = mysql_fetch_assoc(function_mysql_query($qqcamp,__FILE__));
		}
				if($affiliate_id AND $camp['id']!='' AND $camp['id']!=NULL){
					$extraMemberParam.=$camp['id'];
				}else{
					$extraMemberParam.=$affiliate_id;
				}
			}else{
				$extraMemberParam.=$affiliate_id;
			}
			
			// $moreParams.=$extraMemberParam;
			$queryStringParams[] = $extraMemberParam;

		}


}

// die ($extraMemberParam);



if (!isset($set->isBasicVer) || $set->isBasicVer==0) {
	// Save Traffic
	
				$informationArray = array();
				
				$informationArray['ip']           = $getip;
				$informationArray['ctag']         = $ctag;
				$informationArray['uid']          = $uid;
				$informationArray['banner_id']    = $banner_id;
				$informationArray['group_id']     = $group_id;
				$informationArray['profile_id']   = $profile_id;
				$informationArray['affiliate_id'] = $affiliate_id;
				$informationArray['country_id']   = $country_id;
				$informationArray['bannerType']   = $getBanner['type'];
				if ($isProductSession)
				$informationArray['product_id']  = $getMerchant['id'];
			else
				$informationArray['merchant_id']  = $getMerchant['id'];
			
				$informationArray['refer_url']    = trim($_SERVER['HTTP_REFERER']);
			
			$date = new DateTime();
			 $informationArray['unixRdate'] = $date->getTimestamp();

			 
				$informationArray['clicks']       = 1;
				$informationArray['userAgent']    = substr($_SERVER['HTTP_USER_AGENT'],0,253);
				$informationArray['type']         = 'traffic'; 
				$informationArray['param']        = $freeParam; 
				
				if ($freeParam != $p1 && !empty($p1))
					$informationArray['param2']         = $p1; 
				else
				$informationArray['param2']         = $p2; 
					
				$informationArray['param3']         = $p3; 
				$informationArray['param4']         = $p4; 
				$informationArray['param5']         = $p5; 
	

			if (!$justOutputFinalURL)
				insert_new_traffic_record($informationArray);
}




$utmTags='';
if (strlen($set->utmtags)>0){
	$utmTags = "&" . $set->utmtags;
	$utmTags = str_replace('{AffiliateID}',$affiliate_id, $utmTags) ;
	$utmTags = str_replace('{AffUserName}',$getAffiliate['username'], $utmTags) ;
$utmTags = str_replace('{BannerID}',$banner_id, $utmTags) ;
		
}

$extraAffiliateParam="";
if (isset($set->exportAffiliateIDonTrackerFieldName) && !empty($set->exportAffiliateIDonTrackerFieldName))
{	
	$extraAffiliateParam = "&".$set->exportAffiliateIDonTrackerFieldName."=" .$affiliate_id;
}

if (isset($set->exportProfileNameToTrackerFieldName) && !empty($set->exportProfileNameToTrackerFieldName))
{	
	$getProfile = mysql_fetch_assoc(function_mysql_query("select description from affiliates_profiles where id =  " . $profile_id . " limit 1; " ));
	$extraAffiliateParam .= "&".$set->exportProfileNameToTrackerFieldName."=" .$getProfile['description'];
}
if (!empty($extraAffiliateParam)){
	$queryStringParams[] = $extraAffiliateParam;
}


$creativeName = "";
if (!empty($set->exportCreativeNameWithParam)) {
	$creativeName =  '&'. $set->exportCreativeNameWithParam . '=' . str_replace(' ','_',$getBanner['title']) . ($set->exportLangCreativeNameWithParam==1 ? '--' . str_replace(' ','_',$getBanner['languagename']) : "" ) ;
	$queryStringParams[] = $creativeName;
}



if ($postbackparam==1) {
//file_get_contents('http://rummyroyal.com/aff.php?afb=' .$moreParams);
}


$firstPart = $getBanner['url'];
$hashedPart ="";
if (strpos($firstPart,'#')>0) {
	$ex = explode('#',$firstPart);
	$firstPart = $ex[0];
	$hashedPart = $ex[1];
	$ex = explode('?',$hashedPart,2);
	$hashedPart= $ex[0];
}
else if (strpos($firstPart,'?')>0) {
	$ex = explode('?',$firstPart);
	$firstPart = $ex[0];
	 
	$queryStringParams[]=$ex[1];
	
}

//$additionalExternalParamsFromTracker = rtrim($additionalExternalParamsFromTracker[0],'&') .$additionalExternalParamsFromTracker[1];

 
 if (endsWith($additionalExternalParamsFromTracker,'&') && startsWith($utmTags, '&')){
	$additionalExternalParamsFromTracker = rtrim($additionalExternalParamsFromTracker,'&');
 }
 if ( startsWith($utmTags, '&&')){
	$utmTags = '&' . ltrim($utmTags,'&');
 }
  
  
  $queryStringParams[] = $utmTags;
  $queryStringParams[] = $additionalExternalParamsFromTracker;
  

/* $secondPart = $additionalExternalParamsFromTracker.$utmTags.$extraAffiliateParam;
if (!empty($secondPart) && strpos($secondPart, '&') !== 0){
	
	$secondPart ="&" . $secondPart;
	
} */


$secondPart = "";
$queryStringParams=  array_filter($queryStringParams);
foreach ($queryStringParams as $queryparam) {
	// var_dump($queryparam);
	// echo '<br>';
	$secondPart .= "&" . trim(ltrim(ltrim($queryparam,'?'),'&'));
}

$secondPart = '?'.trim(ltrim($secondPart,'&'));

$finalUrl = trim($firstPart).trim($secondPart);

$forceTrackerParams = $set->forceParamsForTracker;
if (!empty($forceTrackerParams) ) {
	
	$exp = explode(',',$forceTrackerParams);
	
	
	$forceTrackerParams = "";
	foreach ($exp as $val) {

		if (strpos(strtolower($finalUrl),strtolower($val).'=')>0) {	}
		else {
				if (empty($forceTrackerParams)) {
						$forceTrackerParams =$val . '=' . $finalCtag;
				}
				else {
					$forceTrackerParams .= '&' . $val . '=' . $finalCtag;
				}
		}
		
	}
}
else {
	$forceTrackerParams = "";
}
				

if (!empty($forceTrackerParams)) {
	
	$queryStringParams[] = $forceTrackerParams;
	/* 
	$firstPart =  $getBanner['url'].    $moreParams;
	$secondPart = $forceTrackerParams .$additionalExternalParamsFromTracker.$utmTags.$extraAffiliateParam.$creativeName;
	
	if (!empty($secondPart) && strpos($secondPart, '&') !== 0){
	
	$secondPart ="'&amp;" . $secondPart;
	
}
	$finalUrl =$firstPart . $secondPart; */
	
}

$secondPart = "";
foreach ($queryStringParams as $queryparam) {
	if ($isProductSession  && $getMerchant['ignoreOtherInternalParameters']==1){
		if (strpos(strtolower($queryparam),strtolower($set->exportAffiliateIDonTrackerFieldName))!==false)
			continue;
		
	}
	
	$secondPart .= "&" . trim(ltrim(ltrim($queryparam,'?'),'&'));
}
$secondPart = '?'.trim(ltrim($secondPart,'&'));


$finalUrl = trim($firstPart).trim($secondPart);
$finalUrl = str_replace('??','?',$finalUrl);
$finalUrl = $finalUrl . (!empty($hashedPart) ? "#". $hashedPart : "");


	// $finalUrl =.('&amp;').$forceTrackerParams .$additionalExternalParamsFromTracker.$utmTags.$extraAffiliateParam ;

if ($removeSubid) {
	$finalUrl = str_replace('&subid={subid}','',$finalUrl);
}
	
if ($debug==1) {
	echo 'banner: ' .  $getBanner['url'].'<br>';
	echo 'more: ' . $moreParams .('&amp;').'<br>';
	echo 'forceTrackerParams: ' . $forceTrackerParams .'<br>';
	echo 'additionalExternalParamsFromTracker: ' . $additionalExternalParamsFromTracker .'<br>';
	echo 'utmTags: ' . $utmTags .'<br>';
	echo 'extraAffiliateParam: ' . $extraAffiliateParam .'<br>';
	echo '$creativeName: ' . $creativeName .'<br>';
	die (( $finalUrl));
}

if($non_valid_merchant_id == -2 && $non_valid_product_id==-2 && !empty($set->defaultTrackingUrl)){
	$finalUrl = $set->defaultTrackingUrl;
}
mysql_close();

/* 
echo '
<script type="text/javascript">
window.onload = function() {
	window.location="'.$finalUrl.'";
	}
</script>'; */

if ($justOutputFinalURL)
die($finalUrl);

if (!empty($set->metaTrackingHeader) && false ){

echo '
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
' . $set->metaTrackingHeader . '
</head>';
}

if ($writeLog){
	writeToLog($getip);
	writeToLog(print_r($_SERVER,true));
	writeToLog($finalUrl);
}


header("Location: ".$finalUrl);


die();
?>