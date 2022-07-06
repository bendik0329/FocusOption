<?php
require('common/global.php');

$ctagArray = array();
$ctag=$_GET['ctag'];
$ctagArray = getBtag($ctag);

$affiliate_id=$ctagArray['affiliate_id'];
$banner_id=$ctagArray['banner_id'];
$profile_id=$ctagArray['profile_id'];
$country=$ctagArray['country'];
$uid=$ctagArray['uid'];
$freeParam=$ctagArray['freeParam'];

//$exp=explode("-",$ctag);
// $affiliate_id=substr($exp[0],1);
// $banner_id=substr($exp[1],1);
// $profile_id=substr($exp[2],1);

$cookieName='affCtag_'.$ctag;

$getAffiliate = dbGet($affiliate_id,"affiliates");

if ($getAffiliate['valid']<0){
	die ($set->webAddress.'images/1px.gif');
}

$getBanner = dbGet($banner_id,"merchants_creative");
$getMerchant = dbGet($getBanner['merchant_id'],"merchants");
if (!$getAffiliate['id'] OR !$getAffiliate['valid']) $miss=1;
if (!$getBanner['id'] OR !$getBanner['valid']) $miss=1;
if (!$getMerchant['id'] OR !$getMerchant['valid']) $miss=1;

if ($getBanner['file']) list($width,$height) = @getimagesize($getBanner['file']);
	else {
	$width = $getBanner['width'];
	$height = $getBanner['height'];
	}

$html = '<div style="width: '.$width.'px; height: '.$height.'px; position: relative;"'.($width > 200 ? ' onmouseover="document.getElementById(\\\'ads_'.$ctag.'_'.$width.'\\\').style.display=\\\'block\\\';" onmouseout="document.getElementById(\\\'ads_'.$ctag.'_'.$width.'\\\').style.display=\\\'none\\\';"' : '').'>
	<div style="position: absolute; z-index: 9;">';
if ($getBanner['type'] == "flash") {
	$html .= '<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="'.$width.'" height="'.$height.'">
			<param name="movie" value="'.$set->webAddress.'view.php?ctag='.$ctag.'">
			<param name="allowScriptAccess" value="always">
			<param name="wmode" value="transparent">
			<param name="flashvars" value="'.($getMerchant['flashTag'] ? $getMerchant['flashTag'] : 'creativeURL').'='.$set->webAddress.'click.php?ctag='.$ctag.'">
			<embed src="'.$set->webAddress.'view.php?ctag='.$ctag.'" width="'.$width.'" height="'.$height.'" allowScriptAccess="always" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer" flashvars="'.($getMerchant['flashTag'] ? $getMerchant['flashTag'] : 'creativeURL').'='.$set->webAddress.'click.php?ctag='.$ctag.'" wmode="transparent"></embed>
		</object>';
	} else if ($getBanner['type'] == "script") {
	$link = $set->webAddress.'click.php?ctag='.$ctag;
	$html = str_replace('{ctag}',$link,$getBanner['scriptCode']);
	} else if ($getBanner['type'] == "image") {
	$html .= '<a href="'.$set->webAddress.'click.php?ctag='.$ctag.'" target="_blank"><img border="0" src="'.$set->webAddress.'view.php?ctag='.$ctag.'" alt="'.$getBanner['alt'].'" width="'.$width.'" height="'.$height.'" /></a>';
	} else if ($getBanner['type'] == "mobileleader") {
	$html .= '<div style="height: 300px;"></div><div style="width: 100%; background: #000; text-align: center; position: fixed; bottom: 0; z-index: 9999;"><a href="'.$set->webAddress.'click.php?ctag='.$ctag.'" target="_blank"><img border="0" src="'.$set->webAddress.'view.php?ctag='.$ctag.'" alt="'.$getBanner['alt'].'" width="95%" /></a></div>';
	
	require_once('mobileDetect/Mobile_Detect.php');
	$detect = new Mobile_Detect;
	$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
	if ($deviceType == 'computer') $html = '';
	} else if ($getBanner['type'] == "mobilesplash") {
	
	


	$html = '
	<style type="text/css">
		body {
			overflow: hidden;
			}
		#mobileLoaderWindow {
			width: 100%;
			height: 100%;
			padding-top: 3%;
			background: url('.$set->webAddress.'images/opacity.png);
			position: absolute;
			top: 0;
			z-index: 99999;
			}
	</style>
	<script src="'.$set->webAddress.'js/jquery.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		setTimeout(function() {
			$(\\\'body\\\').scrollTop(\\\'0\\\');
			}, 1500);
		setTimeout(function() {
			hideAd();
			}, 5000);
		});
	function hideAd() {
		$(\\\'body\\\').css(\\\'overflow\\\',\\\'auto\\\');
		$("#mobileLoaderWindow").fadeOut();
		}
	</script>
	<div align="center" id="mobileLoaderWindow">
		<a href="'.$set->webAddress.'click.php?ctag='.$ctag.'" target="_blank"><img border="0" src="'.$set->webAddress.'view.php?ctag='.$ctag.'" alt="'.$getBanner['alt'].'" width="90%" /></a>
		<br /><br />
		<a onclick="hideAd();" style="color: #FFF;">Skip AD</a>
	</div>';
	if ($_COOKIE['AdSkipper'.$getBanner['id']]) $html = '';
		else setcookie('AdSkipper'.$getBanner['id'],substr(time(),-7)."Loader",time()+300);
	
	require_once('mobileDetect/Mobile_Detect.php');
	$detect = new Mobile_Detect;
	$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');
	if ($deviceType == 'computer') $html = '';
	}
$html .= '</div>
	'.($width > 200 ? '<!-- div align="center" id="ads_'.$ctag.'_'.$width.'" style="display: none; position: absolute; right: 0; bottom: 0; width: 153px; height: 20px; line-height: 20px; background: url('.$set->webAddress.'images/ad_bg.png) no-repeat; z-index: 10;"><a href="'.$set->webAddress.'" target="_blank" style="font-family: tahoma; font-size: 11px; font-weight: bold; color: #474747; text-decoration: none;">'.$set->webTitle.'</a></div -->' : '').'
</div>';
$content = "var js = '".str_replace(Array("\n","\t"),Array("",""),$html)."';"."\n";
$content .= "document.write(js);";

if ($getBanner['type'] == "script") $content = $html;
echo $content;
mysql_close();
die();
?>