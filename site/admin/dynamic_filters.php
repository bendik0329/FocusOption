<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'dynamic_filters';
$pageTitle = lang('Dynamic Filters');
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

switch ($act) {
	/* ------------------------------------ [ Manage dynamic_filters ] ------------------------------------ */
	
	case "makedefault":
		$db=dbGet($id,$appTable);
		//set all dynamic_filters "makedefault" to zero
		function_mysql_query("update " . $appTable . " set makedefault=0",__FILE__);
		if($id>0){
		//make selected group to default group
		$makedefault=1;
		updateUnit($appTable,"makedefault='".$makedefault."'","id='".$db['id']."'");
		}
		_goto($set->SSLprefix.$set->basepage);
		break;
	
	
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_valid_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	
	
	case "delete":

		if (!$dynamic_filter_id) $errors['id'] = 1;
		if (empty($errors)) {
		
			dbDelete($dynamic_filter_id,$appTable);
			_goto($set->SSLprefix.$set->basepage);
	}
			
			
			case "add":
	
	// var_dump($_POST);
	// die();
		if (!$db['name']) $errors['name'] = 1;
		if (empty($errors)) {
			
		if (isset($dynamic_filter_id)) {
			$db['id'] = $dynamic_filter_id;
		} else
			$db[valid] = 0;
			dbAdd($db,$appTable);
			_goto($set->SSLprefix.$set->basepage);
			}
	
	default:
$row = '';
		$langsOptions = "";
		
		if(isset($dynamic_filter_id)){
			$db = mysql_fetch_assoc(function_mysql_query("SELECT * from dynamic_filters where id= ". $dynamic_filter_id ." ",__FILE__));
		}
		
	
			//$sql = "SELECT g.*,l.title as language_title,l.id as language_id FROM ".$appTable." g inner join languages l on g.language_id = l.id ORDER BY g.id ASC";
			$sql = "SELECT g.*,m.name as brandname FROM ".$appTable." g left join merchants m on g.merchant_id = m.id ";
			$qq=function_mysql_query($sql,__FILE__);
		
		
		$resArray =array();
		$foundDefault = false;
		while ($ww=mysql_fetch_assoc($qq)) {
			
			$res =array();
	
			$res['id'] = $ww['id'];
			$res['caption'] = $ww['caption'];
			$res['name'] = $ww['name'];
			$res['rdate'] = $ww['rdate'];
			$res['brandname'] = $ww['brandname'];
			$res['valid'] = $ww['valid'];
		

			$resArray[$ww['id']]= $res;
		}
		
		
		//echo "<pre>";print_r($resArray);die;
		
		$i = 0;
		foreach ($resArray as $ww) {
			
			
				$currentDefValue =   '<a onclick="window.location.href=\''.$set->SSLprefix.$set->basepage.'?act=makedefault&id='.$ww['id'].'\'" style="cursor: pointer;">' . xvPic($ww['makedefault']) . "</a>";
				// $currentDefValue =  $i==0 && !$foundDefault ?  xvPic(1) : xvPic($ww['makedefault']);
				
			$l++;
			
			
			
			
			
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/dynamic_filters.php?act=add&dynamic_filter_id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="'.$set->SSLprefix.'admin/dynamic_filters.php?act=delete&dynamic_filter_id='.$ww['id'].'">'.lang('Delete').'</a></td>
						<td>'.$ww['caption'].'</td>
						<td>'.$ww['name'].'</td>
						
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td>'.$ww['brandname'].'</td>
						<td align="center" id="lng_valid_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_valid_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
					</tr>';
					
			}
			
		
		$set->content = '<form method="post">
						<input type="hidden" name="act" value="add" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Dynamic Filter').'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1" style="">
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
							<td align="left">'.lang('Name').':</td><td><input type="text" name="db[caption]" value="'. (!empty($row['caption']) ? ($row['caption']) :   $db['caption']) .'" '.($errors['caption'] ? 'style="border: 1px red solid;"' : '').' /></td>
							<td align="left">'.lang('Key').':</td><td><td><input type="text" name="db[name]" value="'. (!empty($row['name']) ? ($row['name']) :   $db['name']) .'" '.($errors['name'] ? 'style="border: 1px red solid;"' : '').' /></td>
							
							<td align="left"><input type="submit" value="'.lang('Save').'" /></td>
							</tr>
							<tr><td></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Dynamic Filters List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Action').'</td>
								<td>'.lang('Name').'</td>
								<td>'.lang('Key').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Merchant Name').'</td>
								<td align="center">'.lang('Available').'</td>
							</tr></thead><tfoot>
							'.$langList.'</tfoot>
						</table>';
	
		theme();
		break;
	}

?>