<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
//require_once('common/BarcodeQR.php');
require_once ('common/ShortUrl.php');
require_once('common/InStyle.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/manager/";
if (!isManager()) _goto($lout);

$appTable = 'merchants_creative';
$appProm = 'merchants_promotions';

$group_id="";
if ($set->userInfo['level']=='manager')  
$group_id = $set->userInfo['group_id'] ;

$set->webAddress = $set->isHttps?$set->webAddressHttps:$set->webAddress;

$max_upload = 15; // Max Upload Per Page
//die('Act: ' . $act);

$isNew = (isset($_GET['new']) && $_GET['new']==1 ? 1 : 0);
//die(var_dump($isNew));

switch ($act) {
	// ----------------------------------------- [ Edit Banner ] -----------------------------------------
	case "deletePromotions":
				
				if ($set->AllowManagerEditrCreative==0){
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
				}
		
				// var_dump($_POST);
				
				$title = isset($_POST['promotionName']) ? $_POST['promotionName'] : "";
				$merchant_id = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : "";
				if (!empty($title)) {
				if ($_POST['deleteAllWithSamePrefix']=='on') {
					$title = str_replace(substr($title, strrpos($title, '_') + 0),'',$title);
				$qry = "delete from $appProm where title like '".$title."_%' AND merchant_id=". $merchant_id;
						// die($qry);
				}else{
				$qry = "delete from $appProm where title = '".$title."' AND merchant_id=". $merchant_id;
				}
				function_mysql_query(
				$qry,__FILE__);
				}
				
			if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
		
			break;
	
	
	case "deletecreative":
	
				$merchant_id = isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "1";
			if ($set->AllowManagerEditrCreative==0){
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
			}
		
		
				// var_dump($_GET);
				$id = isset($_GET['id']) ? $_GET['id'] : "";
				if ($id) {
				function_mysql_query("update merchants_creative set valid = -1 where id = " .$id,__FILE__);
				}	
	
				
				//$qrys = $qrys .'merchant_id=' . (isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "1");
				$qrys = $qrys .'&type=' .( isset($_GET['type']) ? $_GET['type'] : "");
				$qrys = $qrys .'&category_id=' .(isset($_GET['category_id']) ? $_GET['category_id'] : "");
				$qrys = $qrys .'&lang=' .( isset($_GET['lang']) ? $_GET['lang'] : "");
				$qrys = $qrys .'&creativedimenstion=' .( isset($_GET['creativedimenstion']) ? $_GET['creativedimenstion'] : "");
				$qrys = $qrys .'&promotion=' .( isset($_GET['promotion']) ? $_GET['promotion'] : "");
				$qrys = $qrys .'&valid=' .( isset($_GET['valid']) ? $_GET['valid'] : 1);
				$qrys = $qrys .'&q=' .( isset($_GET['q']) ? $_GET['q'] : "");
				
	
				if ($merchant_id>0)
					_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id.$qrys);
				else  {
					
					_goto($set->SSLprefix.$set->basepage.'?1=1'.$qrys);
				}
		
			break;
				case "restore":
				// var_dump($_GET);
				$id = isset($_GET['id']) ? $_GET['id'] : "";
				$merchant_id = isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "1";
				
						if ($set->AllowManagerEditrCreative==0){
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
						}
		
		
				
				if ($id) {
				function_mysql_query("update merchants_creative set valid = 0 where id = " .$id,__FILE__);
				}	
				
				if ($merchant_id>0)
					_goto($set->SSLprefix.$set->basepage.'?act=trash&merchant_id='.$merchant_id);
				else  {
					// die ('gerger');
					_goto($set->SSLprefix.$set->basepage.'?act=trash');
				}
		
			break;
			

	case "del_banner":
		
			if ($set->AllowManagerEditrCreative==0) {
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
			}
		
		
		
		$ww=dbGet($id,$appTable);
		if (file_exists($ww['file'])) ftDelete($ww['file']);
		function_mysql_query("DELETE FROM ".$appTable." WHERE id='".$ww['id']."'",__FILE__);
		die('<a href="'.$set->SSLprefix.$set->basepage.'?merchant_id='.$ww['merchant_id'].'">'.lang('Go Back').'!</a>');
		break;
		
	case "save_banner":
	
			if ($set->AllowManagerEditrCreative==0){
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
			}
		
			include("common/creatives/edit_save_banner.php");
		
		_goto($set->SSLprefix.$set->basepage . '?act=edit_banner&id=' . $db['id'] . '&ty=1');
		break;

		
	case "edit_banner":
	
		include("common/creatives/edit_banner.php");	
		theme();
		break;
	
	// ----------------------------------------- [ Edit Banner ] -----------------------------------------
	
                
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'bnr_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	
	case "add":
		include("common/creatives/save_banner_case.php");
	
	case "trash":
		$pageTitle = lang('Creative Materials'). ' ' .lang('Recycle Bin');
					$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

		// $merchant_id = '1';
		$numqq = function_mysql_query("SELECT id FROM merchants",__FILE__);
		if (!$merchant_id) {
			$numww = mysql_fetch_assoc($numqq);
			
			if (mysql_num_rows($numqq) <= 1) { 
				_goto($set->SSLprefix.$set->basepage . '?act=trash&merchant_id=' . $numww['id']);
			}
			
			        $set->content .= '<script>var hash = "";</script>';
                    //$set->content  = '<script>var hash = window.location.hash ? "#tab_3" : "";</script>';
			$set->content .= '<div align="center"><b>' . lang('Choose merchant to manage his creative material') 
						  . ':</b><br /><br /><select onchange="location.href=\'' . $set->SSLprefix.$set->basepage . '?'.($isNew==1 ? 'new=1&' : '' ) .'act=trash&' . 'merchant_id=\'+this.value + hash;"><option value="">' 
						  . lang('Choose Merchant') . '</option>' . listMerchants() . '</select></div>';
                        
			theme();
			die();
			
		} else {
			$set->pageTitle .= ' ' . listMerchants($merchant_id, 1);
						
		}
		
          // die ('grgtr');      
		$qq = function_mysql_query("SELECT * FROM " . $appProm . " WHERE merchant_id='" . $merchant_id . "' ORDER BY title ASC",__FILE__);
		$distincPromotions = array();
		while ($ww = mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!in_array($ww['title'],$distincPromotions)) {
			$promotions_list .='<option id="'.$ww['id'].'">'.$ww['title'] . '</option>';
			$distincPromotions[] = $ww['title'];
			}
			
			
			$PromList .= '<tr ' . ($l % 2 ? 'class="trLine"' : '') . '>
						<td>' . $ww['id'] . '</td>
						<td align="left">' . $ww['title'] . '</td>
						<td align="center">' . dbDate($ww['rdate']) . '</td>
						<td align="center">' . ($ww['affiliate_id'] ? $ww['affiliate_id'] : lang('ALL')) . '</td>
						<td align="center"><a href="' . $set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&prom_id=' . $ww['id'] . '">' . lang('Edit') . '</a></td>
						<td align="center" id="promlng_' . $ww['id'] . '"><a onclick="ajax(\'' . $set->SSLprefix.$set->basepage . '?act=prom_valid&id=' . $ww['id'] . '\',\'promlng_' 
						. $ww['id'] . '\');" style="cursor: pointer;">' . xvPic($ww['valid']) . '</a></td>
					</tr>';
		}
		
		if ($prom_id) {
			$db = dbGet($prom_id, $appProm);
		}
		
		if ($set->AllowManagerEditrCreative==1)
		if (mysql_num_rows($numqq) > 1) { 
			$set->content .= '<div align="right" style="padding-bottom: 10px;"><b>' . lang('Switch Merchant') . ':</b> <select onchange="location.href=\'' 
						  . $set->SSLprefix.$set->basepage . '?act=trash&merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id,0,0,1) . '</select></div>';
		}
		
		
		
			
		if ($merchant) $where .= " AND merchant_id='".$merchant."'";
		if ($type) $where .= " AND type='".$type."'";
		if ($lang) $where .= " AND language_id='".$lang."'";
		if ($promotion) $where .= " AND promotion_id='".$promotion."'";
		if ($category_id) $where .= " AND category_id='".$category_id."'";
		if ($width) $where .= " AND width='".$width."'";
		if ($height) $where .= " AND height='".$height."'";
		
			
		
//		if ($categoryname) $where .= " AND categoryid='".$categoryid."'";
		if ($q) $where .= " AND (lower(title) LIKE '%".strtolower($q)."%' OR id='".$q."')";
		if ($creativedimenstion<>'')  {
		$spltA = explode("X",$creativedimenstion);
			$where .= " AND width='".$spltA[0]."' AND height='".$spltA[1]."'";
		}

		
		
		$typesArray = array();
		$isDownloadSession = false;
		if (isset($_GET['download']) && strlen($_GET['download'])>0) {
				$isDownloadSession = true;
		}
		
		/*$qry = "select distinct type as type from  ".$appTable." WHERE type not like 'coupon' and merchant_id in (".$merchant_id.") ";//.$where;
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$typesArray[]=$ww['type'];
			
		}
		$langsArray = array();
		$qry = "select distinct language_id as  language_id from  ".$appTable." WHERE merchant_id in (".$merchant_id.") ";//.$where;
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$langsArray[]=$ww['language_id'];
		}
		*/

		
		$getPos = $set->itemsLimit;
		$pgg=$_GET['pg'] * $getPos;
		if ($isDownloadSession) 
			$creativeQry  = "SELECT * FROM ".$appTable." WHERE valid=-1 and type not like 'coupon' and merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC";
		else
			$creativeQry  = "SELECT * FROM ".$appTable." WHERE valid = -1 and type not like 'coupon' and merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC LIMIT $pgg,$getPos";
		
		// die($creativeQry);
		$qq=function_mysql_query($creativeQry,__FILE__);
		$bottomNav = GetPages($appTable,"WHERE merchant_id='".$merchant_id."' ".$where,$pg,$getPos);
		$fileArrays = array();
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!empty($ww['file']))
			$fileArrays[] = $ww['file'];
			
			// $q = "SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE banner_id='".$ww['id']."' AND merchant_id='".$ww['merchant_id']."'";
			
			//$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE  rdate> DATE_SUB(curdate(), INTERVAL 1 WEEK) and banner_id='".$ww['id']."' AND merchant_id='".$ww['merchant_id']."'",__FILE__));

			$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(Impressions) AS totalViews, SUM(Clicks) AS totalClicks FROM merchants_creative_stats WHERE Date> DATE_SUB(curdate(), INTERVAL 1 WEEK) and  BannerID='".$ww['id']."' AND MerchantID='".$ww['merchant_id']."'",__FILE__));

			$allCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>
							<td><a href="'.$set->SSLprefix.$set->basepage.'?act=restore&merchant_id='.$ww['merchant_id'].'&id='.$ww['id'].'">'.lang('Restore').'</a></td>
							<td align="left">'.$ww['title'].'</td>
							<td align="center">'.($ww['type'] == "image" || $ww['type'] == "flash" || $ww['type'] == "mobilesplash" || $ww['type'] == "mobileleader" ? getFixedSizeBanner($ww['file'],80,80) : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>
							<td align="center">'.ucwords($ww['type']).'</td>
							<td align="center">'. ($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</td>
							<td align="center">'.listLangs($ww['language_id'],1).'</td>
							<td align="center">'.listPromotions($ww['promotion_id'],$merchant_id,1,"",1).'</td>
							<td align="center">'.listCategory($ww['category_id'],$category_id,1,1).'</td>
							
							
							
							<td align="center"><a href="'.$ww['url'].'" target="_blank" title="'.$ww['url'].'">'.$ww['url'].'</a></td>
							<td align="center">'.$ww['alt'].'</td>
							<td align="center">'.number_format($totalTraffic['totalViews'],0).'</td>
							<td align="center">'.number_format($totalTraffic['totalClicks'],0).'</td>
							<td align="center" id="bnr_'.$ww['id'].'">
								
							</td>
						</tr>';
			}
		// var_dump ($fileArrays);
		if ($isDownloadSession) {
			
			$isDownloadSession=false;
				$file_names = $fileArrays ; //array('iMUST Operating Manual V1.3a.pdf','iMUST Product Information Sheet.pdf');

				//Archive name
				// $archive_file_name=$name.'iMUST_Products.zip';

				//Download Files path
				// $file_path=$_SERVER['DOCUMENT_ROOT'];//.$set->ftp_path ;// '/Harshal/files/';
				// die($file_path);
				
				// zipFilesAndDownload($file_names,$archive_file_name,$file_path);
				$result = create_zip($file_names,'files/bannersPackage.zip');
				//create_zip($file_names,$archive_file_name,$file_path);

	
		}
		
          
		/*
		$comboq = "SELECT count(CONCAT(width, 'x', height)) as count, CONCAT(width, ' X ', height) as dim,`merchants_creative`.height ,`merchants_creative`.width FROM `merchants_creative` WHERE merchant_id = '".$merchant_id."' and  `merchants_creative`.height>0 and `merchants_creative`.width>0 and `merchants_creative`.valid =1
group by CONCAT(width, ' X ', height) order by height, width";

$combolist = '<select name="creativedimenstion" style="width: 140px;"><option value="">'.lang('Show All').'</option>';
$qqcombo=function_mysql_query($comboq,__FILE__);
		while ($wwcombo=mysql_fetch_assoc($qqcombo)) {
			$combolist.= '<option value="'.str_replace(' ','',$wwcombo['dim']) .'">'. $wwcombo['dim'] . '  (' . $wwcombo['count']. ')'.'</option>';
		}
$combolist.='</select>';

                
		
			$set->content .= '
						<br>
                                                
                                                <div class="normalTableTitle" style="cursor: pointer;" >'.lang('Trash').' ' . lang('Creatives List for').': <b>'.listMerchants($merchant_id,1).'</b></div>
                                                    <br>
						<div id="tab_41" ' .(!$isNew==1 ? '>' : 'style="display:none!important;">' );
                                                
						
						
							
						$set->content .= '
						<form method="get" id="formSearch">
						<input type="hidden" name="result" value="1" />
						<input type="hidden" name="act" value="trash" />
						<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>';
							
							if ($set->AllowManagerEditrCreative==1)
						if (mysql_num_rows($numqq) > 1) { 
			$set->content .= '<td  class="blueText" style="padding-bottom: 10px;">' . lang('Switch Merchant') . ':</td>';
						}
						
			$set->content .= '<td align="left" class="blueText">'.lang('Creative Type').':</td>
								<td align="left" class="blueText">'.lang('Category').':</td>
								<td align="left" class="blueText">'.lang('Language').':</td>
									<td style="width:20px";></td>
										<td align="left" class="blueText">'.lang('Width').':</td>
								<td align="left" class="blueText">'.lang('Height').':</td>
								<td align="left" width="110px" ><span class="blueText">'.lang('Choose Size').':</span><br><span style="font-size:10px">'.lang('Width'). ' X ' . lang('Height') . ' ( '.lang('Count').' )</span></td>
								<td style="width:20px";></td>
								<td align="left" class="blueText">'.lang('Promotion').':</td>
								
								<td align="left" class="blueText">'.lang('Creative Name / ID').':</td>
										
						
						</tr>
						<tr>
						';
						
						
						
						
						if (mysql_num_rows($numqq) > 1) {
									$set->content .= '	
											<td><select onchange="location.href=\''  . $set->SSLprefix.$set->basepage . '?merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id) . '</select></td>
										
									';
						}
		$set->content .= '
								<td align="left">
									<select name="type" style="width: 100px;">
										<option value="">'.lang('All').'</option>
										' . (in_array("image",$typesArray) ? '<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>' : "") . '
										' . (in_array("mobileleader",$typesArray) ? '<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>' : "") . '
										' . (in_array("mobilesplash",$typesArray) ? '<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>' : "") . '
										' . (in_array("flash",$typesArray) ? '<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>' : "") . '
										' . (in_array("widget",$typesArray) ? '<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>' : "") . '
										' . (in_array("link",$typesArray) ? '<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>' : "") . '
										' . (in_array("mail",$typesArray) ? '<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>' : "") . '
										' . (in_array("content",$typesArray) ? '<option value="content" '.($type == "content" ? 'selected' : '').'>'.lang('Content').'</option>' : "") . '
									</select>
									</td>
								<td align="left"><select name="category_id" style="width: 100px;"><option value="">'.lang('All').'</option>'.listCategory($category_id,$merchant_id).'</select></td>
								<td align="left"><select name="lang" style="width: 100px;"><option value="">'.lang('All').'</option>'.listLangs($lang,0,$langsArray).'</select></td>
								<td style="width:20px";></td>
								<td align="left"><input type="text" name="width" value="'.$width.'" style="width: 40px; text-align: center;" /></td>
								<td align="left"><input type="text" name="height" value="'.$height.'" style="width: 40px; text-align: center;" /></td>
								<td align="left" width="130">'.$combolist.'</td>
								<td style="width:20px";></td>
								<td align="left"><select name="promotion" style="width: 140px;"><option value="">'.lang('General').'</option>'.listPromotions($promotion,$merchant_id).'</select></td>
								
								<td align="left"><input type="text" name="q" value="'.$q.'" style="width: 120px;" /></td>
								<td style="width:20px";></td>
								<td><input type="submit" value="'.lang('Search').'" /></td><td>&nbsp;&nbsp;</td><td><input name="download" type="submit" value="'.lang('Download').'" /></td>
								
								
								
							</tr></table>
						</div>
							</form>
                                                  
						*/
						include("common/creatives/banner_filters.php");
						$set->content .= '<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td>'.lang('Creative Name').'</td>
								<td>'.lang('Preview').'</td>
								<td>'.lang('Type').'</td>
								<td>'.lang('Size (Width x Height)').'</td>
								<td>'.lang('Language').'</td>
								<td>'.lang('Promotion').'</td>
								<td>'.lang('Category').'</td>
								<td>'.lang('Landing URL').'</td>
								<td>'.lang('ALT').'</td>
								<td>'.lang('Impressions').'</td>
								<td>'.lang('Clicks').'</td>
								
							</tr></thead><tfoot>'.$allCreative.'</tfoot>
						</table><br />
                                                
					<div align="left" style="padding: 5px;">'.$bottomNav.'</div>';
					
		if ($result) $set->content .= '<script type="text/javascript">
			$(document).ready(function(){
				var scrollToElement = $("#formSearch");
					$(\'html,body\').animate({scrollTop:(scrollToElement.offset().top-210)}, 0);
				});
			</script>
                        ';
		
                $set->content .='</div>';
/*		popup errors! - need to be fix
		if($errors['creative_url']!=null){
			$set->content.='<script type="text/javascript">alert("'.$errors['creative_url'].'");</script>';
		}
*/
		
		theme();
		break;
		
		
	
	
	
		
	default:
		$pageTitle = lang('Creative Materials for');
					$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

		// $merchant_id = '1';
		$numqq = function_mysql_query("SELECT id FROM merchants where valid=1",__FILE__);
                
		if (!$merchant_id) {
			$numww = mysql_fetch_assoc($numqq);
			if( empty($numww) ){
				$set->content .= "<style>
				.noMerchantsDiv{
					    display: block;
						text-align: center;
						font-size: 24px;
						padding: 10px 0;
				}
				</style>";
				$set->content .="<div class='noMerchantsDiv'>".  lang("There are no active merchants in the system.") . "<a href='manager/index.php'>". lang("Click here") . "</a>" . lang(" to got to dashboard  ") ."</div>";
				theme();
				die();
			}
			if (mysql_num_rows($numqq) <= 1) { 
				_goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $numww['id']. ($isNew==1 ? "&new=1" : "" ));
			}
			
                        
                        $set->content .= '<script>var hash = "";</script>';
                        //$set->content  = '<script>var hash = window.location.hash ? "#tab_3" : "";</script>';
			$set->content .= '<div align="center"><b>' . lang('Choose merchant to manage his creative material') 
						  . ':</b><br /><br /><select onchange="location.href=\'' . $set->SSLprefix.$set->basepage . '?'.($isNew==1 ? 'new=1&': '') . 'merchant_id=\'+this.value + hash;"><option value="">' 
						  . lang('Choose Merchant') . '</option>' . listMerchants(0,0,0,1) . '</select></div>';
                        
			theme();
			die();
			
		} else {
			$set->pageTitle .= ' ' . listMerchants($merchant_id, 1);
					
		}
		
		$qq = function_mysql_query("SELECT * FROM " . $appProm . " WHERE merchant_id='" . $merchant_id . "' ORDER BY title ASC",__FILE__);
		$distincPromotions = array();
		while ($ww = mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!in_array($ww['title'],$distincPromotions)) {
			$promotions_list .='<option id="'.$ww['id'].'">'.$ww['title'] . '</option>';
			$distincPromotions[] = $ww['title'];
			}
			
			
			$PromList .= '<tr ' . ($l % 2 ? 'class="trLine"' : '') . '>
						<td>' . $ww['id'] . '</td>
						<td align="left">' . $ww['title'] . '</td>
						<td align="center">' . dbDate($ww['rdate']) . '</td>
						<td align="center">' . ($ww['affiliate_id'] ? $ww['affiliate_id'] : lang('ALL')) . '</td>
						<td align="center"><a href="' . $set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&prom_id=' . $ww['id'] . '">' . lang('Edit') . '</a></td>
						<td align="center" id="promlng_' . $ww['id'] . '"><a onclick="ajax(\'' . $set->SSLprefix.$set->basepage . '?act=prom_valid&id=' . $ww['id'] . '\',\'promlng_' 
						. $ww['id'] . '\');" style="cursor: pointer;">' . xvPic($ww['valid']) . '</a></td>
					</tr>';
		}
		
		if ($prom_id) {
			$db = dbGet($prom_id, $appProm);
		}
		
		if ($set->AllowManagerEditrCreative==1)
		if (mysql_num_rows($numqq) > 1) { 
			$set->content .= '<div align="right" style="padding-bottom: 10px;"><b>' . lang('Switch Merchant') . ':</b> <select onchange="location.href=\'' 
						  . $set->basepage . '?merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id,0,0,1) . '</select></div>';
		}
		
		$set->content .= getImageGrowerScript();

		// var_dump($_GET);
		// die();
			
		if ($merchant) $where .= " AND merchant_id='".$merchant."'";
		if ($type) $where .= " AND type='".$type."'";
		if ($lang) $where .= " AND language_id='".$lang."'";
		if ($promotion) $where .= " AND promotion_id='".$promotion."'";
		if ($category_id) $where .= " AND category_id='".$category_id."'";
		if ($width) $where .= " AND width='".$width."'";
		if ($height) $where .= " AND height='".$height."'";
		if ($valid==1) {
			$where .= " AND valid='".$valid."'";
		}
		elseif ($valid==0) {
			$where .= " AND valid>-1";
		}
//		if ($categoryname) $where .= " AND categoryid='".$categoryid."'";
		if ($q) $where .= " AND (lower(title) LIKE '%".strtolower($q)."%' OR id='".$q."')";
		if ($creativedimenstion<>'')  {
		$spltA = explode("X",$creativedimenstion);
			$where .= " AND width='".$spltA[0]."' AND height='".$spltA[1]."'";
		}

		
		
		$typesArray = array();
		$isDownloadSession = false;
		if (isset($_GET['download']) && strlen($_GET['download'])>0) {
				$isDownloadSession = true;
		}
		
		$qry = "select distinct type as type from  ".$appTable." WHERE type not like 'coupon' and merchant_id in (".$merchant_id.") ";//.$where;
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$typesArray[]= strtolower($ww['type']);
			
		}
		$langsArray = array();
		$qry = "select distinct language_id as  language_id from  ".$appTable." WHERE merchant_id in (".$merchant_id.") ";//.$where;
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$langsArray[]=$ww['language_id'];
		}
		

		
		$getPos = $set->itemsLimit;
                if(!empty($_GET['show_all'])){
                    $getPos = 10000;    
                }
		$pgg=$_GET['pg'] * $getPos;
		if ($isDownloadSession) 
			$creativeQry  = "SELECT * FROM ".$appTable." WHERE type not like 'coupon' and merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC";
		else
			$creativeQry  = "SELECT * FROM ".$appTable." WHERE type not like 'coupon' and merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC LIMIT $pgg,$getPos";
		
		
		$qq=function_mysql_query($creativeQry,__FILE__);
		$bottomNav = GetPages($appTable,"WHERE merchant_id='".$merchant_id."' ".$where,$pg,$getPos);
		$fileArrays = array();
		 
		 	
				
				//$qrys = $qrys .'merchant_id=' . (isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "1");
				$qrys = $qrys .'&type=' .( isset($_GET['type']) ? $_GET['type'] : "");
				$qrys = $qrys .'&category_id=' .(isset($_GET['category_id']) ? $_GET['category_id'] : "");
				$qrys = $qrys .'&lang=' .( isset($_GET['lang']) ? $_GET['lang'] : "");
				$qrys = $qrys .'&creativedimenstion=' .( isset($_GET['creativedimenstion']) ? $_GET['creativedimenstion'] : "");
				$qrys = $qrys .'&promotion=' .( isset($_GET['promotion']) ? $_GET['promotion'] : "");
				$qrys = $qrys .'&valid=' .( isset($_GET['valid']) ? $_GET['valid'] : 1);
				$qrys = $qrys .'&q=' .( isset($_GET['q']) ? $_GET['q'] : "");
				// die ($qrys);
		 				
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!empty($ww['file']))
			$fileArrays[] = $ww['file'];
			
			
			//$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE  rdate> DATE_SUB(curdate(), INTERVAL 1 WEEK) and banner_id='".$ww['id']."' AND merchant_id='".$ww['merchant_id']."'",__FILE__));

			$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(Impressions) AS totalViews, SUM(Clicks) AS totalClicks FROM merchants_creative_stats WHERE Date> DATE_SUB(curdate(), INTERVAL 1 WEEK) and  BannerID='".$ww['id']."' AND MerchantID='".$ww['merchant_id']."'",__FILE__));

			$allCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>
							<td><a href="'.$set->webAddress.ltrim($set->basepage, '/').'?act=edit_banner&id='.$ww['id'].'" class="inline">'.lang('Edit').'</a>'.($set->AllowManagerEditrCreative==1 ? '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'.$set->SSLprefix.$set->basepage.'?act=deletecreative&merchant_id='.$ww['merchant_id'].'&id='.$ww['id'].$qrys.'">'.lang('Delete').'</a>':'').'</td>
							<td align="left">'.$ww['title'].'</td>'
							.(strpos($ww['file'],"/tmp")?
							'<td align="center" class="tooltip">'.($ww['type'] == "image" || $ww['type'] == "flash" || $ww['type'] == "mobilesplash" || $ww['type'] == "mobileleader" ?  '<img src="images/wheel.gif" width=28 height =28><span class="tooltiptext" style="padding-bottom:15px">'. lang("System is checking for virus. Please refresh in a minute.") .'</span>' : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>':
							'<td align="center" class="img-wrap">'.($ww['type'] == "image" || $ww['type'] == "flash" || $ww['type'] == "mobilesplash" || $ww['type'] == "mobileleader" ? getFixedSizeBanner($ww['file'],50,50) : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>') .
							
							'<td align="center">'.ucwords($ww['type']).'</td>
							<td align="center" class="dimantion-wrap">'. ($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</td>
							<td align="center">'.listLangs($ww['language_id'],1).'</td>
							<td align="center">'.listPromotions($ww['promotion_id'],$merchant_id,1,"",1).'</td>
							<td align="center">'.listCategory($ww['category_id'],$category_id,1,1).'</td>
							
							
							
							<td align="center"><a href="'.$ww['url'].'" target="_blank" title="'.$ww['url'].'">'.$ww['url'].'</a></td>
							<td align="center">'.$ww['alt'].'</td>
							<td align="center">'.number_format($totalTraffic['totalViews'],0).'</td>
							<td align="center">'.number_format($totalTraffic['totalClicks'],0).'</td>
							<td align="center" id="bnr_'.$ww['id'].'">
								'.($set->AllowManagerEditrCreative==1 ? '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'bnr_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a>' : xvPic($ww['valid']) ).'
								'.($delete ? '<a href="'.$set->SSLprefix.$set->basepage.'?act=del_banner&id='.$ww['id'].'">.</a>' : '').'
							</td>
						</tr>';
			}
		// var_dump ($fileArrays);
		if ($isDownloadSession) {
			
			$isDownloadSession=false;
				$file_names = $fileArrays ; //array('iMUST Operating Manual V1.3a.pdf','iMUST Product Information Sheet.pdf');

				//Archive name
				// $archive_file_name=$name.'iMUST_Products.zip';

				//Download Files path
				// $file_path=$_SERVER['DOCUMENT_ROOT'];//.$set->ftp_path ;// '/Harshal/files/';
				// die($file_path);
				
				// zipFilesAndDownload($file_names,$archive_file_name,$file_path);
				$result = create_zip($file_names,'files/bannersPackage.zip');
				//create_zip($file_names,$archive_file_name,$file_path);

	
		}
		
                
	
		$comboq = "SELECT count(CONCAT(width, 'x', height)) as count, CONCAT(width, ' X ', height) as dim,`merchants_creative`.height ,`merchants_creative`.width FROM `merchants_creative` WHERE merchant_id = '".$merchant_id."' and  `merchants_creative`.height>0 and `merchants_creative`.width>0 and `merchants_creative`.valid =1
group by CONCAT(width, ' X ', height) order by height, width";

$combolist = '<select name="creativedimenstion" style="width: 140px;"><option value="">'.lang('Show All').'</option>';
$qqcombo=function_mysql_query($comboq,__FILE__);
		while ($wwcombo=mysql_fetch_assoc($qqcombo)) {
			$combolist.= '<option value="'.str_replace(' ','',$wwcombo['dim']) .'">'. $wwcombo['dim'] . '  (' . $wwcombo['count']. ')'.'</option>';
		}
$combolist.='</select>';

                
		if ($set->AllowManagerEditrCreative==1){
		include("common/creatives/add_banner_form.php");
						$set->content .='<br>
                                                
                                                <div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_41\').slideToggle(\'fast\');">'.lang('Creatives List for').': <b>'.listMerchants($merchant_id,1,0,1).': </b>'.listMerchants($merchant_id,1).'</b></div>
                                                    <br>
						<div id="tab_41" ' .(!$isNew==1 ? '>' : 'style="display:none!important;">' );
                                                
		}
						
							
						$set->content .= '
						<form method="get" id="formSearch">
						<input type="hidden" name="result" value="1" />
						<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>';
							
							if ($set->AllowManagerEditrCreative==1)
						if (mysql_num_rows($numqq) > 1) {
			$set->content .= '<td  class="blueText" style="padding-bottom: 10px;">' . lang('Switch Merchant') . ':</td>';
						}
						
			$set->content .= '<td align="left" class="blueText">'.lang('Creative Type').':</td>
								<td align="left" class="blueText">'.lang('Category').':</td>
								<td align="left" class="blueText">'.lang('Language').':</td>
									<td style="width:20px";></td>
										<td align="left" class="blueText">'.lang('Width').':</td>
								<td align="left" class="blueText">'.lang('Height').':</td>
								<td align="left" width="110px" ><span class="blueText">'.lang('Choose Size').':</span><br><span style="font-size:10px">'.lang('Width'). ' X ' . lang('Height') . ' ( '.lang('Count').' )</span></td>
								<td style="width:20px";></td>
								<td align="left" class="blueText">'.lang('Promotion').':</td>
								<td align="left" class="blueText">'.lang('Available').':</td>
								<td align="left" class="blueText">'.lang('Creative Name / ID').':</td>
										
						
						</tr>
						<tr>
						';
						
						
						
						
						if (mysql_num_rows($numqq) > 1) {
									$set->content .= '	
											<td><select onchange="location.href=\''  . $set->SSLprefix.$set->basepage . '?merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id) . '</select></td>
										
									';
						}
		$set->content .= '
								<td align="left">
									<select name="type" style="width: 100px;">
										<option value="">'.lang('All').'</option>
										' . (in_array("image",$typesArray) ? '<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>' : "") . '
										' . (in_array("mobileleader",$typesArray) ? '<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>' : "") . '
										' . (in_array("mobilesplash",$typesArray) ? '<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>' : "") . '
										' . (in_array("flash",$typesArray) ? '<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>' : "") . '
										' . (in_array("widget",$typesArray) ? '<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>' : "") . '
										' . (in_array("link",$typesArray) ? '<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>' : "") . '
										' . (in_array("mail",$typesArray) ? '<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>' : "") . '
										' . (in_array("content",$typesArray) ? '<option value="content" '.($type == "content" ? 'selected' : '').'>'.lang('Content').'</option>' : "") . '
									</select>
									</td>
								<td align="left"><select name="category_id" style="width: 100px;"><option value="">'.lang('All').'</option>'.listCategory($category_id,$merchant_id).'</select></td>
								<td align="left"><select name="lang" style="width: 100px;"><option value="">'.lang('All').'</option>'.listLangs($lang,0,$langsArray).'</select></td>
								<td style="width:20px";></td>
								<td align="left"><input type="text" name="width" value="'.$width.'" style="width: 40px; text-align: center;" /></td>
								<td align="left"><input type="text" name="height" value="'.$height.'" style="width: 40px; text-align: center;" /></td>
								<td align="left" width="130">'.$combolist.'</td>
								<td style="width:20px";></td>
								<td align="left"><select name="promotion" style="width: 140px;"><option value="">'.lang('General').'</option>'.listPromotions($promotion,$merchant_id).'</select></td>
								<td align="left"><select name="valid" style="width: 110px;"><option '. ($valid==0 ? 'selected' :'') .'value="0">'.lang('Show All').'</option><option '. ($valid==1 ? 'selected' :'') .' value="1">'.lang('Active Only').'</option></select></td>
								<td align="left"><input type="text" name="q" value="'.$q.'" style="width: 120px;" /></td>
								<td style="width:20px";></td>
								<td><input type="submit" value="'.lang('Search').'" /></td><td>&nbsp;&nbsp;</td><td><input name="download" type="submit" value="'.lang('Download').'" /></td>
								
								
								
							</tr></table>
						</div>
							</form>
                                                  
						

						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td>'.lang('Creative Name').'</td>
								<td>'.lang('Preview').'</td>
								<td>'.lang('Type').'</td>
								<td>'.lang('Size (Width x Height)').'</td>
								<td>'.lang('Language').'</td>
								<td>'.lang('Promotion').'</td>
								<td>'.lang('Category').'</td>
								<td>'.lang('Landing URL').'</td>
								<td>'.lang('ALT').'</td>
								<td>'.lang('Impressions').' *</td>
								<td>'.lang('Clicks').' *</td>
								<td>'.lang('Available').'</td>
							</tr></thead><tfoot>'.$allCreative.'</tfoot>
						</table><br />* '. lang('Last Week Stats').'<br />
                                                
					<div align="left" style="padding: 5px;">'.$bottomNav.'</div>';
                
                if(!empty($_GET['show_all'])){
                    $set->content .= '<div align="left" style="padding: 5px;"><a href="/admin/creative.php?merchant_id='.$merchant_id.$qrys.'">'.lang('Show pagination').'</a></div>';
                }else{
                    $set->content .= '<div align="left" style="padding: 5px;"><a href="/admin/creative.php?show_all=1&merchant_id='.$merchant_id.$qrys.'">'.lang('Show all').'</a></div>';
                }			
					
		if ($result) $set->content .= '<script type="text/javascript">
			$(document).ready(function(){
				var scrollToElement = $("#formSearch");
					$(\'html,body\').animate({scrollTop:(scrollToElement.offset().top-210)}, 0);
				});
			</script>
                        ';
		
                $set->content .='</div>';
/*		popup errors! - need to be fix
		if($errors['creative_url']!=null){
			$set->content.='<script type="text/javascript">alert("'.$errors['creative_url'].'");</script>';
		}
*/
		
		theme();
		break;
	
	/* ------------------------------------ [ Promotions ] ------------------------------------ */
	
	case "prom_valid":
		$db=dbGet($id,$appProm);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appProm,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=prom_valid&id='.$db['id'].'\',\'promlng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "prom_add":
	
	
	$title = isset($_POST['db']['title'])? $_POST['db']['title'] : "";
	$aff = isset($_POST['db']['affiliate_id'])? $_POST['db']['affiliate_id'] : "";
	if ($title=='')
		_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
	
	$groupid = isset($_POST['group_id']) && $_POST['group_id']>-1 ? $_POST['group_id'] : -1;
	$autoApproveAll = isset($_POST['autoApproveAll']) && 'on'==$_POST['autoApproveAll'] ? 1 : 0;
	if (empty($aff)) {
		
		if ($groupid > -1 ) {
			$qry = "select id from affiliates where valid = 1 and group_id = " . $group_id;
			$rsc = function_mysql_query($qry,__FILE__);
			while ($row = mysql_fetch_assoc($rsc)) {
					
					if (!$db['title']) $errors['title'] = 1;
						if (!$db['merchant_id']) $errors['merchant_id'] = 1;
						if (empty($errors)) {
							$affiliate_id = $row['id'];
							$db['affiliate_id'] = $affiliate_id;
							$db['rdate'] = dbDate();
							$db['title'] = $title. '_'.$db['affiliate_id'];
							$db['valid'] = $autoApproveAll;
							dbAdd($db,$appProm);
						}
				}
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
			}
			else {

				if (!$db['title']) $errors['title'] = 1;
					if (!$db['merchant_id']) $errors['merchant_id'] = 1;
					if (empty($errors)) {
						$db['rdate'] = dbDate();
						
						$db['valid'] = $autoApproveAll;
						dbAdd($db,$appProm);
						_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
					
					}
			}
			
	} 
	else if (!empty($aff)) {
		
	$affiliate_ids = explode('|',$aff);
	foreach ($affiliate_ids as $aff_id) {
	// var_dump($aff_id);
	if (!$db['title']) $errors['title'] = 1;
		if (!$db['merchant_id']) $errors['merchant_id'] = 1;
		if (empty($errors)) {
			$db['affiliate_id'] = $aff_id;
			$db['rdate'] = dbDate();
			$db['title'] = $title. '_'.$db['affiliate_id'];
			$db['valid'] = $autoApproveAll;
			dbAdd($db,$appProm);
		}
	}
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
	}
	
	
	/* ------------------------------------ [ Promotions ] ------------------------------------ */
	
	case "showMail":
		$db = dbGet($id, $appTable);
		// var_dump($db);
			// die();
			
			
		if ($db['type'] != "mail") die("ERROR");
		echo str_replace("{ctag}", $ctag,  $db['scriptCode']);
		die();
		break;
	
	case "showContent":
		$db=dbGet($id,$appTable);
		if ($db['type'] != "content") die("ERROR");
		echo str_replace("{ctag}",$ctag,$db['scriptCode']);
		die();
		break;
	
	}

?>
