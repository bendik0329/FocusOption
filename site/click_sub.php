<?php
require('common/global.php');

$appTable = "sub_stats";

$ctag=str_replace("=","",$_GET['ctag']);
// $exp=explode("-",$ctag);
// $affiliate_id=substr($exp[0],1);
// $banner_id=substr($exp[1],1);
// $profile_id=substr($exp[2],1);
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




$cookieName='affSubClickCtag_'.$ctag;
$getAffiliate = dbGet($affiliate_id,"affiliates");
$getProfile = dbGet($profile_id,"affiliates_profiles");
$getBanner = dbGet($banner_id,"sub_banners");

$parse_url=parse_url($getBanner['url']);
if (!$parse_url['query']) $moreParams .= '?ctag='.$ctag;
	else $moreParams .= '&ctag='.$ctag;
	
	
	$moreParams .='-u'.$uid;
	
	
if (!$miss) {
	$ctag = clearInjection($ctag);
	$getStat = mysql_fetch_assoc(function_mysql_query("SELECT * FROM ".$appTable." WHERE ctag='".$ctag."' AND rdate='".date("Y-m-d")."'",__FILE__));
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
		$db['banner_id'] = $banner_id;
		if ($getProfile['id'] AND $getProfile['valid']) $db['profile_id'] = $profile_id;
		$db['clicks'] = 1;
		dbAdd($db,$appTable);
		setcookie($cookieName,substr(time(),-7)."A",time()+3600);
		}
	}
	
// Special Scripts
	
echo '
<script type="text/javascript">
window.onload = function() {
	window.location="'.$getBanner['url'].$moreParams.'";
	}
</script>';
mysql_close();
die();
?>