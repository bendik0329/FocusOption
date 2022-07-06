<?php
require('common/global.php');

$appTable = "sub_stats";

$ctag=str_replace("=","",$_GET['ctag']);
$exp=explode("-",$ctag);
$affiliate_id=substr($exp[0],1);
$banner_id=substr($exp[1],1);
$profile_id=substr($exp[2],1);
$cookieName='affSubCtag_'.$ctag;

$getAffiliate = dbGet($affiliate_id,"affiliates");
$getProfile = dbGet($profile_id,"affiliates_profiles");
$getBanner = dbGet($banner_id,"sub_banners");

if (!$miss) {
	$ctag = clearInjection($ctag);
	$getStat = mysql_fetch_assoc(function_mysql_query("SELECT * FROM ".$appTable." WHERE ctag='".$ctag."' AND rdate='".date("Y-m-d")."'",__FILE__));
	if ($getStat['id']) {
		if (!$_COOKIE[$cookieName]) {
			updateUnit($appTable,"views=views+1","id='".$getStat['id']."'");
			setcookie($cookieName,md5(time()),time()+3600);
			}
		} else {
		$db['rdate'] = date("Y-m-d");
		$db['ctag'] = $ctag;
		if ($getAffiliate['id'] AND $getAffiliate['valid']) $db['affiliate_id'] = $affiliate_id;
			else $db['affiliate_id'] = '1';
		$db['banner_id'] = $banner_id;
		if ($getProfile['id'] AND $getProfile['valid']) $db['profile_id'] = $profile_id;
		$db['views'] = 1;
		dbAdd($db,$appTable);
		setcookie($cookieName,md5(time()),time()+3600);
		}
	}
_goto($set->webAddress.$getBanner['file']);
mysql_close();
die();
?>