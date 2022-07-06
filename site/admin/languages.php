<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'languages';

switch ($act) {
	/* ------------------------------------ [ Manage Languages ] ------------------------------------ */
	
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "add":
		
		if (!$db['title']) $errors['title'] = 1;
		if (!$db['lngCode']) $errors['lngCode'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			$db[valid] = 0;
			$db['lngCode'] = strtoupper($db['lngCode']);
			dbAdd($db,$appTable);
			_goto($set->basepage);
			}

			case "edit":
		// var_dump($_POST);
		$qry = "update languages set title = '" . $_POST['db']['title'] . "' , lngCode = '" . $_POST['db']['lngCode']. "' where id = " . $_POST['db']['id'];
		// die ($qry);
		function_mysql_query($qry,__FILE__);
		
			_goto($set->SSLprefix.$set->basepage);
		
	
	default:
	
	if ($id) {
			
			$db = dbGet($id,$appTable);
			$pageTitle = lang('Editing Language').': '.$db['title'];
		} else $pageTitle = lang('Add New Language');
		$qq=function_mysql_query("SELECT * FROM ".$appTable." ORDER BY title ASC",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?id='.$ww['id'].'">'.lang('Edit').'</a></td>
						<td>'.$ww['title'].'</td>
						<td align="center">'.$ww['lngCode'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
					</tr>';
			}
		$set->content = '<form method="post">
						<input type="hidden" name="act" value="'.($id ? 'edit' : 'add' ) .'" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<div class="btn"><a href="'.$set->SSLprefix.$set->basepage.'">'.lang('Add New').'</a></div>
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.($id ? lang('Edit Language') : lang('Add New Language')).'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">'.lang('Language Name').':</td><td><input type="text" name="db[title]" value="'.$db['title'].'" '.($errors['title'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><td align="left">'.lang('Language Code').':</td><td><input type="text" name="db[lngCode]" value="'.$db['lngCode'].'" '.($errors['lngCode'] ? 'style="border: 1px red solid;"' : '').' maxlength="3" style="text-align: center;" /> Write 3 chars for language code</td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />
						<div class="normalTableTitle">'.lang('Languages List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Options').'</td>
								<td>'.lang('Language').'</td>
								<td align="center">'.lang('Language Code').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Available').'</td>
							</tr></thead><tfoot>'.$langList.'</tfoot>
						</table>';
		theme();
		break;
	}

?>