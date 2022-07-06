<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$userLevel = "manager";
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/". $userLevel."/";
if (!isManager()) _goto($lout);

$appTable = 'affiliates_status';
$pageTitle = lang('Affiliates Category');
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
switch ($act) {
	/* ------------------------------------ [ Manage Groups ] ------------------------------------ */
	
	case "valid":

	$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		//echo '<a  style="cursor: pointer;">'.xvPic($valid).'</a>';
		 echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "delete":
		
		$sql = "delete from " . $appTable . " where id=" . $status_id;
		mysql_query($sql);
		_goto($set->SSLprefix.$set->basepage);
		break;
		
	case "add":
		if (!$db['title']) $errors['title'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			if (isset($status_id)) {
			$db['id'] = $status_id;
			}
			else
			$db[valid] = 0;
			
			dbAdd($db,$appTable);
			_goto($set->SSLprefix.$set->basepage);
			}
			
	
	default:
	
			$row = '';
			$sql  = "SELECT * FROM ".$appTable." where created_by_admin_id = ". $set->userInfo['id'] ." ORDER BY id ASC";
			$qq=function_mysql_query($sql,__FILE__,__FUNCTION__);
		while ($ww=mysql_fetch_assoc($qq)) {
			
			if (!empty($status_id) && $status_id == $ww['id'])
				$row = $ww;
				
			
			$l++;
			$totalAffiliates=mysql_result(function_mysql_query("SELECT COUNT(id) FROM affiliates WHERE status_id='".$ww['id']."'",__FILE__,__FUNCTION__),0);
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td align="center"><a href="'.$set->SSLprefix.'manager/affiliatesStatus.php?act=add&status_id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="'.$set->SSLprefix.'manager/affiliatesStatus.php?act=delete&status_id='.$ww['id'].'">'.lang('Delete').'</a></td>
						<td>'.$ww['title'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center"><a href="'.$set->SSLprefix.'manager/affiliates_list.php?status_id='.$ww['id'].'">'.$totalAffiliates.'</a></td>
						<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
					</tr>';
			}
		$totalAffiliates=mysql_result(function_mysql_query("SELECT COUNT(id) FROM affiliates WHERE status_id='0'",__FILE__,__FUNCTION__),0);
		
		
			
		
		$set->content = '<form method="post" >
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="db[created_by_admin_id]" value="'. $set->userInfo['id'] .'" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Cateogry').'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1" style="">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">'.lang('Category Name').':</td><td><input type="text" name="db[title]" value="'. (!empty($row['title']) ? ($row['title']) :   $db['title']) .'" '.($errors['title'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Affiliates Category List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Action').'</td>
								<td>'.lang('Category Name').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Total Affiliates').'</td>
								<td align="center">'.lang('Available').'</td>
							</tr></thead><tfoot><tr>
								<td>0</td>
								<td></td>
								<td>'.lang('General').'</td>
								<td align="center">-</td>
								<td align="center">'.$totalAffiliates.'</td>
							</tr>'.$langList.'</tfoot>
						</table>';
		theme();
		break;
	}

?>