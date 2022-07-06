<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

/**
 * This function will be called from the "save_deal" case.
 */
require_once('common/global.php');
// $userLevel = "admin";

$userLevel = $set->userInfo['level'];
if (empty($userLevel)){
$level = $_SERVER['REQUEST_URI'];
if (strpos(strtolower($level),'admin')!==false)
	$userLevel = 'admin';
else if (strpos(strtolower($level),'manager')!==false)
	$userLevel = 'manager';
/* else
	_goto('/'); */
	
}



if(isset($set->userInfo['level']))
{
	$userLevel = $set->userInfo['level'];
}
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/".$userLevel."/";
if (!isManager() ) _goto($lout);


include('common/myFavorites_common.php');
