<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */

require_once('common/global.php');
// $userLevel = "admin";

$userLevel = $set->userInfo['level'];
if (empty($userLevel)){
$level = $_SERVER['REQUEST_URI'];
if (strpos(strtolower($level),'admin')!==false)
	$userLevel = 'admin';
else if (strpos(strtolower($level),'manager')!==false)
	$userLevel = 'manager';
else
	_goto('/');
	
}

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/". $userLevel .  "/";

if (!isAdmin()) _goto($lout);

include('common/products/product_categories.php');

die();

?>