<?php
require('common/global.php');

$appTable = "stats_banners";

$ctag=strip_tags(str_replace("=","",$_GET['ctag']));
$exp=explode("-",$ctag);
$affiliate_id=substr($exp[0],1);

if ($_GET['random']) {
	$banner=mysql_fetch_assoc(mysql_query("SELECT id FROM merchants_creative WHERE valid='1' ORDER BY RAND()"));
	$banner_id = $banner['id'];
	} else $banner_id=substr($exp[1],1);
$profile_id=substr($exp[2],1);
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

// Save Traffic
if ($_SERVER['HTTP_REFERER']) {
	$sql=mysql_query("SELECT id,ip FROM affiliates_traffic WHERE lower(refer_url)='".strtolower(trim($_SERVER['HTTP_REFERER']))."' AND affiliate_id='".$affiliate_id."'");
	$ww=mysql_fetch_assoc($sql);
	if ($ww['ip'] != $_SERVER['REMOTE_ADDR']) if ($ww['id']) mysql_query("UPDATE affiliates_traffic SET visits=visits+1,rdate='".dbDate()."' WHERE id='".$ww[id]."'");
		else mysql_query("INSERT INTO affiliates_traffic (rdate,ip,affiliate_id,merchant_id,refer_url,visits) VALUES ('".dbDate()."','".$_SERVER['REMOTE_ADDR']."','".$affiliate_id."','".$getMerchant['id']."','".trim($_SERVER['HTTP_REFERER'])."','1')");
		}
// Save Traffic

echo '
<script type="text/javascript">
window.onload = function() {
	window.location="'.$getBanner['url'].$moreParams.'";
	}
</script>';
mysql_close();
die();
?>