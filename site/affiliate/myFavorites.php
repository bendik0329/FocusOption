<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

/**
 * This function will be called from the "save_deal" case.
 */
require_once('common/global.php');
$userLevel = "affiliate";

//$userLevel = $set->userInfo['level'];
if (empty($userLevel)){
$level = $_SERVER['REQUEST_URI'];
$userLevel = 'affiliate';
}

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);


include('common/myFavorites_common.php');
