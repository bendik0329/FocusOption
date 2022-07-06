<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

require_once('common/global.php');


$userLevel = $set->userInfo['level'];

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/". $userLevel."/";
if (!isManager()) _goto($lout);

include('common/affiliateCampaignRelation.php');

die();

?>