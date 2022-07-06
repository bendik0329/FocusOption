<?php

require('common/global.php');

$ctag=$_GET['ctag'];
$exp=explode("-",$ctag);
$affiliate_id=substr($exp[0],1);
$banner_id=substr($exp[1],1);
$profile_id=substr($exp[2],1);
$cookieName='affCtagsub_'.$ctag;

$getAffiliate = dbGet($affiliate_id,"affiliates");
$getBanner = dbGet($banner_id,"sub_banners");



if ($getBanner['file']) list($width,$height) = @getimagesize($getBanner['file']);
	else {
	$width = $getBanner['width'];
	$height = $getBanner['height'];
	}

	
	$url = $set->webAddress.'click_sub.php?ctag='.$ctag;
if ($getBanner['type'] == "flash") {

	$html .= '<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="'.$width.'" height="'.$height.'">
			<param name="movie" value="'.$set->webAddress.'view_sub.php?ctag='.$ctag.'">
			<param name="allowScriptAccess" value="always">
			<param name="wmode" value="transparent">
			<param name="flashvars" value="creativeURL='.$set->webAddress.'click_sub.php?ctag='.$ctag.'">
			<embed src="'.$set->webAddress.'view_sub.php?ctag='.$ctag.'" width="'.$width.'" height="'.$height.'" allowScriptAccess="always" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer" flashvars="creativeURL='.$set->webAddress.'click_sub.php?ctag='.$ctag.'" wmode="transparent"></embed>
		</object>';
		
	} else if ($getBanner['type'] == "image") {
	$html .= '<a href="'.$set->webAddress.'click_sub.php?ctag='.$ctag.'" target="_blank"><img border="0" src="'.$set->webAddress.'view_sub.php?ctag='.$ctag.'" alt="" width="'.$width.'" height="'.$height.'" /></a>';

	}
else  {


	$html .= '<a href="'.$set->webAddress.'click_sub.php?ctag='.$ctag.'" target="_blank">link</a>';

	
}


//$content = str_replace(Array("\n","\t"),Array("",""),$html);


//die ($content);
// $content = $html;

$content = $url;
header('location: ' . $content);



/*
$content = "var js = '".str_replace(Array("\n","\t"),Array("",""),$html)."';"."\n";
$content .= "document.write(js);";
*/
echo $content;
mysql_close();
die();
?>