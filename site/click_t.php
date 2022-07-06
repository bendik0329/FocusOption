<?php
require('common/global.php');

$appTable = "stats_banners";



$ctag=strip_tags(str_replace("=","",$_GET['ctag']));
// $exp=explode("-",$ctag);
// $affiliate_id=substr($exp[0],1);


//  new method
$ctagArray = array();
//$ctag=$_GET['ctag'];
$ctagArray = getBtag($ctag);

$affiliate_id=$ctagArray['affiliate_id'];
$banner_id=$ctagArray['banner_id'];
$profile_id=$ctagArray['profile_id'];
$country=$ctagArray['country'];
$uid=$ctagArray['uid'];
$freeParam=$ctagArray['freeParam'];
// new method


$t = microtime(true);
$micro = sprintf("%06d",($t - floor($t)) * 1000000);
$micro = substr($micro, 0, 1000000);
$tmp =date("His");
$tmp = date('His', strtotime('-4 hours', $tmp));
$uid =$tmp.$micro.  date('mYd') . $affiliate_id ;



$additionalExternalParams='';
$additionalExternalParams=($set->uri);
if ($additionalExternalParams!='' AND $ctag!='') {
	$additionalExternalParams = explode($ctag,$additionalExternalParams);
	$additionalExternalParams = $additionalExternalParams[1];
}


if ($_GET['random']) {
	$banner=mysql_fetch_assoc(mysql_query("SELECT id FROM merchants_creative WHERE valid='1' ORDER BY RAND()"));
	$banner_id = $banner['id'];
	} else $banner_id=$banner_id;
	
$profile_id=$profile_id;
$cookieName='affClickCtag_'.$affiliate_id.$banner_id.$profile_id;
$getAffiliate = dbGet($affiliate_id,"affiliates");
$getProfile = dbGet($profile_id,"affiliates_profiles");
$getBanner = dbGet($banner_id,"merchants_creative");
if (!$getBanner['id']) {
	$banner=mysql_fetch_assoc(mysql_query("SELECT id FROM merchants_creative WHERE valid='1' ORDER BY RAND()"));
	$banner_id = $banner['id'];
	$getBanner = dbGet($banner_id,"merchants_creative");
	}
$getMerchant = dbGet($getBanner['merchant_id'],"merchants");

if (!$getMerchant['id'] OR !$getMerchant['valid']) $miss=1;

$userCountry = getIPCountry();
if ($userCountry['countrySHORT']) $ctag .= '-c'.$userCountry['countrySHORT'];

$pexp=explode(",",$getMerchant['params']);
$parse_url=parse_url($getBanner['url']);
for ($i=0; $i<=count($pexp)-1; $i++) {
	if ($i == "0") {
		if (!$parse_url['query']) $moreParams .= '?'.$pexp[$i].'='.$ctag;
			else $moreParams .= '&'.$pexp[$i].'='.$ctag;
		} else $moreParams .= '&'.$pexp[$i].'='.$ctag;
	}

	
	$moreParams .='-u'.$uid;
	
	
if (!$miss) {
	$getStat = mysql_fetch_assoc(mysql_query("SELECT * FROM ".$appTable." WHERE ctag='".$ctag."' AND rdate='".date("Y-m-d")."'"));
	if ($getStat['id']) {
		if (!$_COOKIE[$cookieName]) {
			updateUnit($appTable,"clicks=clicks+1","id='".$getStat['id']."'");
			setcookie($cookieName,substr(time(),-7)."A",time()+3600);
			}
		} else {
		$db['rdate'] = date("Y-m-d");
		$db['ctag'] = $ctag;
		if ($getAffiliate['id'] AND $getAffiliate['valid']) $db['affiliate_id'] = $affiliate_id;
			else $db['affiliate_id'] = '1';
		$db['group_id'] = $getAffiliate['group_id'];
		$db['merchant_id'] = $getBanner['merchant_id'];
		$db['banner_id'] = $banner_id;
		if ($getProfile['id'] AND $getProfile['valid']) $db['profile_id'] = $profile_id;
		$db['clicks'] = 1;
		dbAdd($db,$appTable);
		setcookie($cookieName,substr(time(),-7)."A",time()+3600);
		}
	}

// campaign fields manipulation
$campParamName=$getMerchant['campaignparamname'];
$isCampInCtag=$getMerchant['campaignispartofparams'];
$campaignID=$getMerchant['campaignid'];

if(strpos($getBanner['url'],$campParamName)===false){
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

$extraMemberParam = '';
if($getMerchant['extraMemberParamName']!='' AND $getMerchant['extraMemberParamName']!=NULL){
	$extraMemberParam.='&'.$getMerchant['extraMemberParamName'].'=';
	if(strtolower($getMerchant['extraMemberParamValue'])==strtolower('campID')){
if($affiliate_id){		
$camp = mysql_fetch_assoc(mysql_query('SELECT campID AS id FROM affiliates_campaigns_relations WHERE affiliateID='.$affiliate_id));
}
		if($affiliate_id AND $camp['id']!='' AND $camp['id']!=NULL){
			$extraMemberParam.=$camp['id'];
		}else{
			$extraMemberParam.=$affiliate_id;
		}
	}else{
		$extraMemberParam.=$affiliate_id;
	}
	
	$moreParams.=$extraMemberParam;

}

// Save Traffic
if ($_SERVER['HTTP_REFERER']) {
	$sql=mysql_query("SELECT id,ip FROM affiliates_traffic WHERE lower(refer_url)='".strtolower(trim($_SERVER['HTTP_REFERER']))."' AND affiliate_id='".$affiliate_id."'");
	$ww=mysql_fetch_assoc($sql);
	if ($ww['ip'] != $_SERVER['REMOTE_ADDR']) if ($ww['id']) mysql_query("UPDATE affiliates_traffic SET visits=visits+1,rdate='".dbDate()."' WHERE id='".$ww[id]."'");
		else mysql_query("INSERT INTO affiliates_traffic (rdate,ip,affiliate_id,merchant_id,refer_url,visits) VALUES ('".dbDate()."','".$_SERVER['REMOTE_ADDR']."','".$affiliate_id."','".$getMerchant['id']."','".trim($_SERVER['HTTP_REFERER'])."','1')");
		}

		
		

// Save Traffic
if ($_GET['isDebug'] == '1') {
die ($getBanner['url'].$moreParams);
}



//die ($getBanner['url'].$moreParams.$additionalExternalParams);
echo '
<script type="text/javascript">
window.onload = function() {
	window.location="'.$getBanner['url'].$moreParams.$additionalExternalParams.'";
	}
</script>';

mysql_close();
die();
?>