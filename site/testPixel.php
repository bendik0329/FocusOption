<?php

set_time_limit(0);


header("Pragma: no-cache");
header("Expires: 0");
$debug = isset($_GET['debug'])? $_GET['debug']: 0;
ini_set("memory_limit","128M");
require_once('common/database.php');
require_once('common/global.php');
include('pixel.php');


$pixelurl = isset($_GET['pixelurl']) ? $_GET['pixelurl'] : "";
if (empty($pixelurl))
	die('pixelurl is empty');

$pixelContent  = firePixel($pixelurl);
				
					echo '<br><br><span style="color:blue">Firing install Pixel, Affiliate_ID = ' . $affiliate_id.' -- '.$pixelurl.'</span><br>';
					echo $pixelContent;
				
				