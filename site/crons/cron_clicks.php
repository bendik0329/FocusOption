<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
// require_once('common/global.php');
$runthis = $_GET['runthis']==2 ? true : false;
if (!$runthis)
	die('.');


require(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');
set_time_limit(60);
//$sql = "SELECT * FROM traffic where userAgent !='' and os='' and rdate  BETWEEN DATE_SUB(NOW(), INTERVAL 2 DAY) AND NOW()";


include_once(__DIR__ .'/../common/BrowserDetection.php');
require_once(__DIR__ .'/../mobileDetect/Mobile_Detect.php');

$browser = new BrowserDetection();

$userAgent       = $browser->getUserAgent();               //string
$hasData = true;

while ($hasData==true) {
$sql = "SELECT id,userAgent FROM traffic where rdate  BETWEEN DATE_SUB(NOW(), INTERVAL 2 DAY) AND NOW() and userAgent !='' and os='' limit 50;";
$hasData = false;
$result = function_mysql_query($sql,__FILE__);
while($rowData = mysql_fetch_assoc($result)){
		$hasData = true;
		$browser->setUserAgent($rowData['userAgent']);
		
		$browserName     = $browser->getName();                    //string
		$browserVer      = $browser->getVersion();                 //string
		$platformFamily  = $browser->getPlatform();                //string
		$platformVer     = $browser->getPlatformVersion(true);     //string
		$platformName    = $browser->getPlatformVersion();         //string
		
		
		
		$detect = new Mobile_Detect;
		$detect->setUserAgent($rowData['userAgent']);
		$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'Tablet' : 'Mobile') : 'Desktop');
	
		//Test if the user uses Microsoft Edge
		if ($browser->getName() == BrowserDetection::BROWSER_EDGE) {
			echo 'You are using Edge!';
		}

		//Test if the user uses specific versions of Internet Explorer
		if ($browser->getName() == BrowserDetection::BROWSER_IE) {
			//As you can see you can compare major and minor versions under a string format '#.#.#' (no limit in depth)
			if ($browser->compareVersions($browser->getVersion(), '11.0.0.0') < 0) {
				echo 'You are using IE < 11.';
			}

			if ($browser->compareVersions($browser->getVersion(), '11.0.0') == 0) {
				echo 'You are using IE 11.';
			}

			if ($browser->compareVersions($browser->getVersion(), '11.0') > 0) {
				echo 'You are using IE > 11.';
			}

			if ($browser->compareVersions($browser->getVersion(), '11') >= 0) {
				echo 'You are using IE 11 or greater.';
			}
		}
		
		$sql = "UPDATE traffic SET platform='". $deviceType ."', os='". $platformName ."', osVersion='". $platformVer . "', browser='". $browserName ."', broswerVersion='". $browserVer ."' where id=" . $rowData['id'];
		function_mysql_query($sql,__FILE__);
		
		echo "Update traffic ID : ".$rowData['id'] . "<br/>";
}
}
if(!$hasData){
	echo "No rows found !!!";die;
}
?>