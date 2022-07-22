<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */
function theme($theme=0,$location="",$direction='LTR') {
	global $set;
	$SSLprefix  = $set->SSLprefix;
	$SSLswitch = $set->SSLswitch;

	$pageTitle = ($set->breadcrumb_title ?$set->breadcrumb_title : ($set->pageTitle ? $set->pageTitle.' - ' : '').$set->webTitle);
	
        
	if ($set->getFolder[1] == "affiliate") {
            $getGroup = mysql_fetch_assoc(function_mysql_query("SELECT zopimChat FROM admins WHERE group_id='".$set->userInfo['group_id']."' LIMIT 1",__FILE__,__FUNCTION__));
            $zopimChat = $getGroup['zopimChat'];
        }

$logoPath = @strpos($set->logoPath,$SSLprefix)!==false ? $set->logoPath : $SSLprefix.$set->logoPath ;

$logoPath = '/files/design/1637323637y37HB.png';


$altTextLogo = '<div class="knockout" style=" background: url(\'/images/coma_misti.jpg\') -80px -80px;
  color: red;
  -webkit-text-fill-color: transparent;
  -webkit-background-clip: text;
  font-weight: bold;
  font-size: 74px;
  font-family: arial, helvetica;
  width: 100%;
  max-height:85px;
  /* margin: 50px auto; */
  text-align: left;
">'.$set->dashBoardMainTitle.'</div>';


$theLogoText = (!empty($set->logoPath) && strpos($set->logoPath,"/tmp")===false?'<img class="headerLogo" height="84px" border="0" src="'.$logoPath.'" alt="" />':$altTextLogo);




if ($_GET['dddd']==1)
	die ($logoPath);
		
	$headerCode .= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>'.$pageTitle.'</title>
	<base href="'.$set->webAddress.'" />
	<link href="'.$SSLprefix.'css/style.css?v1='.date('Ymd').'" rel="stylesheet" type="text/css" />
	<link href="'.$SSLprefix.''.$set->getFolder[1].'/css/style.css" rel="stylesheet" type="text/css" />
	<link href="'.$SSLprefix.'css/dropdown.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="'.$SSLprefix.'css/colorbox.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="'.$SSLprefix.'css/default.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="'.$SSLprefix.'css/tooltips.css" media="screen" rel="stylesheet" type="text/css" />
	<link href="//fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet" type="text/css">
        ';
		if (!empty($set->metaTrackingHeader && $location=='mainHomePage') ){

		$headerCode .=$set->metaTrackingHeader;
		}
		$headerCode .='
        <!--<script type="text/javascript" src="'.$SSLprefix.'js/jquery.js"></script>-->
		<script src="'.$SSLprefix.'js/jquery-1.9.1.min.js"></script>
        
        <!-- Bootstrap JavaScript requires jQuery version 1.9.1 or higher -->
        <!--link id="bootstrap-style" href="'.$SSLprefix.'css/bootstrap.min.css" rel="stylesheet">
	<link href="'.$SSLprefix.'css/bootstrap-responsive.min.css" rel="stylesheet">
        <script src="'.$SSLprefix.'js/bootstrap.min.js"></script-->
        
	<script src="'.$SSLprefix.'js/jquery.colorbox-min.js"></script>
	<script language="javascript" type="text/javascript" src="'.$SSLprefix.'js/global.js"></script>
	<link type="text/css" href="'.$SSLprefix.'css/redmond/jquery-ui-1.8.21.custom.css" rel="stylesheet" />
	
	<!--added for new version of fancybox -->
	<script type="text/javascript" src="'.$SSLprefix.'js/jquery-ui-1.9.2.custom.min.js"></script>
	
	<!--<script type="text/javascript" src="'.$SSLprefix.'js/jquery-ui-1.8.21.custom.min.js"></script>
	<link href="'.$SSLprefix.'fancybox/fancybox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="'.$SSLprefix.'fancybox/fancybox.js"></script>-->
	<link href="'.$SSLprefix.'fancybox/jquery.fancybox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="'.$SSLprefix.'fancybox/jquery.fancybox.js"></script>
	'.($set->faviconPath && strpos($set->faviconPath,"/tmp")===false?'<link rel="shortcut icon" href="' .($set->faviconPath).'"  />':'').'
	
	'.($set->sortTable ? (empty($set->sortTableCssDisable)?'<link rel="stylesheet" href="'.$SSLprefix.'pages/css/sort_table.css" type="text/css" media="print, projection, screen" />':'').'
	<script type="text/javascript" src="'.$SSLprefix.'pages/js/__jquery.tablesorter.js"></script>
	<script type="text/javascript" src="'.$SSLprefix.'pages/js/jquery.tablesorter.pager.js"></script>
	'.($set->sortTableScript ? '<script type="text/javascript">
	$(function() {
		$("table.tablesorter")
			.tablesorter({
				widthFixed: true,
                                widgets: [\'zebra\'],
				//dateFormat : "mmddyyyy",
				dateFormat : "uk",
				
				headers: {
				  1: { sorter: "shortDate", dateFormat: "ddmmyyyy" },
				}
			})
		.tablesorterPager({container: $("#pager"),size:'.$set->rowsNumberAfterSearch.'});
	});
	</script>' : '') : '').'

        <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script>toastr.options = {"closeButton": true,"debug": false,"newestOnTop": true,"progressBar": true,"positionClass": "toast-top-right","preventDuplicates": false,"onclick": null,"showDuration": "300","hideDuration": "1000","timeOut": "5000","extendedTimeOut": "1000","showEasing": "swing","hideEasing": "linear","showMethod": "fadeIn","hideMethod": "fadeOut"};</script>

	<!--[if lt IE 7]>
		<script type="text/javascript" src="'.$SSLprefix.'js/jquery.dropdown.js"></script>
	<![endif]-->
	<!-- / END -->
	<script type="text/javascript">
		$(document).ready(function() {
			if ($("body").height() < $(window).height()) {
				$("#bottom_table").css("position", "fixed");
				$("#bottom_table").css("bottom", "0");
			}
		
			$("a[rel=fancybox]").fancybox({
				openEffect: "elastic",
				closeEffect: "elastic",
				openSpeed: 400,
				closeSpeed: 400,
				showNavArrows: false
				});
		
			});
	</script>
	'.$zopimChat.'
	'.$set->analyticsCode.'
</head>
<body class="" dir="ltr">
<div align="center">';
	$header = $headerCode.'
<div class="headerSite" >
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr class="topHeaderBar">
			<td height="80" class="logo-td"><a href="'.($set->getFolder[1] != "index.php" ? $set->getFolder[1] : '').'/">'.$theLogoText.'</a></td>
			<td align="right" height="80" valign="top" class="login">
				'.($set->userInfo['id'] ? '
					<span class="welcome">
						[#'.$set->userInfo['id'].'] '.lang('Welcome Back').' <b>'.$set->userInfo['first_name'].'</b> | <a href="'.$set->getFolder[1].'/?act=logout">'.lang('Logout').' &nbsp; <img border="0" src="'.$SSLprefix.'images/header/logout.png" alt="" align="absmiddle" /></a><br /><br />
						<span class="lastLogin" style="font-size: 11px;"><u>'.lang('Last Login').':</u> '.dbDate($set->userInfo['lastvisit']).'</span><br />
						<span class="lastLogin" style="font-size: 11px;"><u>'.lang('Current Time').':</u> '.date('M d, Y').' '.lang('at').' '.date('H:i a', strtotime ("+1 hour")).' GMT</span>
					</span>
					' : '').'
			</td>
		</tr>'.($set->userInfo['id'] ? '<tr>
			<td colspan="2" class="adminMenu">'.adminMenu().'</td>
		</tr>' : '').'
	</table>
	'.($set->pageTitle || $set->rightBar ? '<div class="pageTitle">
		<div class="title">'.$set->pageTitle.'</div>
		<div class="engine">'.$set->rightBar.'</div>
	</div>' : '').'
</div>
<div class="titleOnPage" style="height: '.($set->pageTitle || $set->rightBar ? '180' : '115').'px;">&nbsp;</div>

';
$footer = '
</div>
<div align="center" id="bottom_table" style="z-index:99999">
	<table width="99%" border="0" cellpadding="0" cellspacing="0"><tr>
		<td '.($direction=='RTL'?'align="right"':'align="left"').'>'.lang('Copyright').' © '.date('Y').' '.$set->webTitle.'. '.lang('All Rights Reserved').'</td>
		<td '.($direction=='RTL'?'align="left"':'align="right"').' style="font-size: 10px;">'.lang('Version').': '.$set->AffiliateBuddiesVersion.' / '.lang('Powered By').': <a href="http://www.affiliatets.com/" target="_blank">AffiliateTS</a></td>
	</tr></table>
</div>
<link href="'.$SSLprefix.'css/verticalDots.css" media="screen" rel="stylesheet" type="text/css" />
</body>
</html>';

	if ($set->print) {
            $theme = 1;
        }
        
	$html .= $header;
	if ($set->userInfo['id'] AND !$theme){ $html .= '
					<div class="smallBar" style="height: 10px;"></div>
					<div class="content">
						<div align="left">'.$set->content.'</div>
					</div>
					'.$footer;
        }else if ($theme == "1"){ 
            print $headerCode.$set->content;
            die();
        }else{ $html = $header.'
					<div class="smallBar" style="height: 10px;"></div>
						'.$set->content.'
					'.$footer;
        }  
        
        
	        if(($theme == 2 || $theme == 0) && (!empty($set->userInfo['type']) && strtolower($set->userInfo['type']) == 'affiliate') ){
	    	    
	    	    
	    	    $logoPath = @strpos($set->logoPath,$SSLprefix)!==false ? $set->logoPath : $SSLprefix.$set->logoPath ;
	    	    $theLogoText = (!empty($set->logoPath) && strpos($set->logoPath,"/tmp")===false?'<img class="headerLogo" height="84px" border="0" src="'.$logoPath.'" alt="" />':$altTextLogo);
	    	    
	    	    
	    	    //ini_set('display_errors', 1);
	    	    //ini_set('display_startup_errors', 1);
	    	    //error_reporting(E_ALL);
	        
                    define('SITE_INCLUDE_START',true);
                    //echo "|".  __DIR__ . '/design/layout.php'."|";
                    include_once realpath( __DIR__ . '/design/layout.php');
                    die();
                }
                
		print $html;
                
                
                
		mysql_close();
		die();
	}
	
?>
