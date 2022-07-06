<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

/**
 * This function will be called from the "save_deal" case.
 */
require_once('common/global.php');
// $userLevel = "admin";

$userLevel = $set->userInfo['level'];
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/". $userLevel."/";
if (!isManager()) _goto($lout);

if (strpos(strtolower($_SERVER['HTTP_REFERER']),'/advertiser/')>0){
 $lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/advertiser/";
_goto($lout);
// _goto($set->SSLprefix.'advertiser/');
}
	



include('common/affiliate_edit.php');
