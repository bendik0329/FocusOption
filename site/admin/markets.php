<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'market';

switch ($act) {

	case "item_valid":
		$db=dbGet($id,$appTable.'_items');
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable.'_items',"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=item_valid&id='.$db['id'].'\',\'market_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "upload_banner":
		$db['last_update'] = dbDate();
		if (chkUpload('file')) {
			$getOldBanner=dbGet($db['id'],"market_banners");
			if (file_exists($getOldBanner['file'])) ftDelete($getOldBanner['file']);
			$db['file'] = UploadFile('file','5120000','jpg,gif,swf,jpeg,png','','files/markets/');
			$exp=explode(".",$db['file']);
			$ext = strtolower($exp[count($exp)-1]);
			if ($ext == "swf") $db['type'] = "flash";
				else $db['type'] = "image";
			list($db['width'],$db['height']) = getimagesize($db['file']);
			}
		if (!$db['id']) $db['rdate'] = dbDate();
		if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
		dbAdd($db,"market_banners");
		_goto($set->SSLprefix.$set->basepage.'?act=items&id='.$db['item_id']);
		break;
	
	case "item_add":
		if (!$db['title']) $errors['title'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			$db[valid] = 0;
			if ($langs) $db['languages'] = '|'.implode('|',$langs).'|';
				else $db['languages'] = '';
			if (chkUpload('image')) $db['image'] = UploadFile('image','5120000','jpg,gif,jpeg,png','','files/markets/');
			dbAdd($db,$appTable.'_items');
			_goto($set->SSLprefix.$set->basepage.'?act=items');
			}
	

	case "items":
		if ($id) {
			$db = dbGet($id,$appTable.'_items');
			$pageTitle = 'Editing Item: '.$db['title'];
			} else $pageTitle = 'Add New Item';
		$catqq=function_mysql_query("SELECT * FROM ".$appTable."_cats ORDER BY id ASC",__FILE__);
		while ($catww=mysql_fetch_assoc($catqq)) $catsList .= '<option value="'.$catww['id'].'" '.($catww['id'] == $db['cat_id'] ? 'selected="selected"' : '').'>'.$catww['title'].'</option>';
		
		$pageTitle = 'Markets Items';
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$lang_exp=explode("|",$db['languages']);
		$i=0;
		$langqq=function_mysql_query("SELECT * FROM languages WHERE valid='1' ORDER BY title ASC",__FILE__);
		while ($langww=mysql_fetch_assoc($langqq)) {
			if ($i >= 5) {
				$allLangs .= '</tr><tr>';
				$i=0;
				}
			if ($i < 5) {
				$allLangs .= '<td><label><input type="checkbox" name="langs[]" value="'.$langww['id'].'" '.(@in_array($langww['id'],$lang_exp) ? 'checked="checked"' : '').' /> '.$langww['title'].'</label></td>';
				$i++;
				}
			}
		$affiliateqq=function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
		while ($affiliateww=mysql_fetch_assoc($affiliateqq)) $allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $db['affiliate_id'] ? 'selected' : '').'>'.$affiliateww['username'].' ['.$affiliateww['id'].'] ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
		
		if ($id) {
			$banner=dbGet($banner_id,"market_banners");
			$l=0;
			$qq=function_mysql_query("SELECT * FROM market_banners WHERE item_id='".$db['id']."' ORDER BY id DESC",__FILE__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$l++;
				$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM market_stats_banners WHERE banner_id='".$ww['id']."' AND item_id='".$ww['item_id']."'",__FILE__));
				$allCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$ww['id'].'</td>
								<td><a href="'.$set->basepage.'?act=items&id='.$ww['item_id'].'&banner_id='.$ww['id'].'">Edit</a></td>
								<td>'.$ww['title'].'</td>
								<td align="center">'.($ww['type'] == "image" || $ww['type'] == "flash" ? getBanner($ww['file'],'25') : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>
								<td align="center">'.$ww['type'].'</td>
								<td align="center">'.($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</td>
								<td align="center">'.number_format($totalTraffic['totalViews'],0).'</td>
								<td align="center">'.number_format($totalTraffic['totalClicks'],0).'</td>
								<td align="center" id="bbnr_'.$ww['id'].'">
									<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid_banner&id='.$ww['id'].'\',\'bbnr_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a>
									'.($delete ? '<a href="'.$set->SSLprefix.$set->basepage.'?act=del_banner&id='.$ww['id'].'">.</a>' : '').'
								</td>
							</tr>';
				}
				
			$set->content .= '
			<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="act" value="upload_banner" />
				<input type="hidden" name="db[id]" value="'.$banner['id'].'" />
				<input type="hidden" name="db[item_id]" value="'.$id.'" />
				<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">Add Banner</div>
				<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_3">
				<tr><td align="left">Title:</td><td><input type="text" name="db[title]" value="'.$banner['title'].'" /></td></tr>
				<tr><td align="left">File:</td><td>'.fileField('file',$banner['file']).'</td></tr>
				<tr><td></td><td><input type="checkbox" name="valid" checked /> Publish</td></tr>
				<tr><td></td><td align="left"><input type="submit" value="Upload" /></td></tr>
				</table>
			</form><hr />
			<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_4\').slideToggle(\'fast\');">Banners List</div>
			<table width="100%" border="0" cellpadding="2" cellspacing="0" bgcolor="#EFEFEF" id="tab_4" class="normal">
				<thead>
					<tr>
						<td>#</td>
						<td>Options</td>
						<td>Creative Name</td>
						<td>Preview</td>
						<td>Format</td>
						<td>Size (Width x Height)</td>
						<td>Impressions</td>
						<td>Clicks</td>
						<td>Available</td>
					</tr></thead><tfoot>'.$allCreative.'</tfoot>
			</table><hr />
			';
			}
		
		$set->content .= '<form method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="item_add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.$pageTitle.'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">Category:</td><td><select name="db[cat_id]"><option value="">Choose Category</option>'.$catsList.'</select></td></tr>
							<tr><td align="left">Item Title:</td><td><input type="text" name="db[title]" value="'.$db['title'].'" /></td></tr>
							<tr><td align="left">URL:</td><td><input type="text" name="db[url]" value="'.$db['url'].'" /></td></tr>
							<tr><td align="left">Affiliate:</td><td><select name="db[affiliate_id]"><option value="">Choose Affiliate</option>'.$allAffiliates.'</select></td></tr>
							<tr><td align="left">CPA Amount Affiliate:</td><td><input type="text" name="db[cpaAmountAffiliate]" value="'.$db['cpaAmountAffiliate'].'" /></td></tr>
							<tr><td align="left">CPA Amount:</td><td><input type="text" name="db[cpaAmount]" value="'.$db['cpaAmount'].'" /></td></tr>
							<tr><td align="left" valign="top">Languages:</td><td><table><tr>'.$allLangs.'</tr></table></td></tr>
							<tr><td align="left" valign="top">Explanation:</td><td><textarea name="db[text]" cols="60" rows="6">'.$db['text'].'</textarea></td></tr>
							<tr><td align="left">Image:</td><td>'.fileField('image',$db['image']).' (230x100)</td></tr>
							<tr><td></td><td align="left"><input type="submit" value="Save" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />';
					
			$qq=function_mysql_query("SELECT * FROM ".$appTable."_items ORDER BY id ASC",__FILE__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$l++;
				$catName = dbGet($ww['cat_id'],$appTable."_cats");
				$exp=explode("|",$ww['languages']);
				unset($languagesList);
				for ($i=0; $i<=count($exp); $i++) {
					if (!$exp[$i]) continue;
					$langww = dbGet($exp[$i],"languages");
					$languagesList[] = $langww['title'];
					}
				$affInfo = dbGet($ww['affiliate_id'],"affiliates");
				$creativeList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>
							<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?act=items&id='.$ww['id'].'">Edit</a></td>
							<td><img border="0" src="'.$ww['image'].'" alt="" /></td>
							<td>'.$ww['title'].'</td>
							<td align="center">'.dbDate($ww['rdate']).'</td>
							<td align="center">'.$catName['title'].'</td>
							<td align="center"><a href="/admin/affiliates.php?act=items&id='.$ww['affiliate_id'].'">'.$affInfo['username'].'</a></td>
							<td align="center">'. $set->currency .' '.$ww['cpaAmountAffiliate'].'</td>
							<td align="center">'. $set->currency .' '.$ww['cpaAmount'].'</td>
							<td align="center">'.@implode(", ",$languagesList).'</td>
							<td align="center"><a href="'.$ww['url'].'" target="_blank">'.$ww['url'].'</a></td>
							<td align="center" id="market_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=item_valid&id='.$ww['id'].'\',\'market_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
						</tr>';
				}
			
			$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">Items List</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td align="center">Options</td>
								<td>Image</td>
								<td>Item Name</td>
								<td align="center">Added Date</td>
								<td align="center">Category</td>
								<td align="center">Affiliate</td>
								<td align="center">CPA Amount Affiliate</td>
								<td align="center">CPA Amount</td>
								<td align="center">Languages</td>
								<td align="center">URL</td>
								<td align="center">Available</td>
							</tr></thead><tfoot>'.$creativeList.'</tfoot>
						</table>';
			
		theme();
		break;

	/* ------------------------------------ [ Manage Markets ] ------------------------------------ */
	case "del_banner":
		$ww=dbGet($id,"market_banners");
		if (file_exists($ww['file'])) ftDelete($ww['file']);
		function_mysql_query("DELETE FROM market_banners WHERE id='".$ww['id']."'",__FILE__);
		die('<a href="'.$set->SSLprefix.$set->basepage.'?act=items&id='.$ww['item_id'].'">Go Back!</a>');
		break;
		
	case "valid_banner":
		$db=dbGet($id,"market_banners");
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit("market_banners","valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid_banner&id='.$db['id'].'\',\'bbnr_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "valid":
		$db=dbGet($id,$appTable.'_cats');
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable.'_cats',"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'market_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "add":
		if (!$db['title']) $errors['title'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			$db[valid] = 0;
			dbAdd($db,$appTable.'_cats');
			_goto($set->SSLprefix.$set->basepage);
			}
	
	default:
		$pageTitle = 'Manage Categories';
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$qq=function_mysql_query("SELECT * FROM ".$appTable."_cats ORDER BY id ASC",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$marketList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td align="center"><a href="'.$set->basepage.'?id='.$ww['id'].'">Edit</a></td>
						<td>'.$ww['title'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center" id="market_'.$ww['id'].'"><a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'market_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
					</tr>';
			}
		if ($id) $db = dbGet($id,$appTable.'_cats');
		$set->content = '<form method="post">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">Add New Category</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">Category Name:</td><td><input type="text" name="db[title]" value="'.$db['title'].'" '.($errors['title'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><td></td><td align="left"><input type="submit" value="Save" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">Categories List</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td align="center">Options</td>
								<td>Category Name</td>
								<td align="center">Added Date</td>
								<td align="center">Available</td>
							</tr></thead><tfoot>'.$marketList.'</tfoot>
						</table>';
		theme();
		break;
	}

?>