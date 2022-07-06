<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'groups';
$pageTitle = lang('Groups');
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
	/* ------------------------------------ [ Manage Groups ] ------------------------------------ */
	
	case "makedefault":
		$db=dbGet($id,$appTable);
		//set all groups "makedefault" to zero
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
	
	
	
	case "add":
	
	// var_dump($_POST);
	// die();
		if (!$db['title']) $errors['title'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
		if (isset($group_id)) {
			$db['id'] = $group_id;
		} else
			$db[valid] = 0;
			dbAdd($db,$appTable);
			_goto($set->SSLprefix.$set->basepage);
			}
	
	default:
$row = '';
		$langsOptions = "";
		
		if(isset($group_id)){
			$db = mysql_fetch_assoc(function_mysql_query("SELECT * from groups where id= ". $group_id ." order by lower(title)",__FILE__));
		}
		
		$ll=function_mysql_query("SELECT * from languages order by lower(title)",__FILE__);
		$allLangsCount = 0;
		while ($wwl=mysql_fetch_assoc($ll)) {
			$langsOptions.='<option value="'. $wwl['id'] . '" '.(isset($language_id)&& $language_id>0 && $language_id==$wwl['id'] ? " selected " : "" ).'>'. lang($wwl['title']) .'</option>';
			$allLangsCount++;
		}
			//$sql = "SELECT g.*,l.title as language_title,l.id as language_id FROM ".$appTable." g inner join languages l on g.language_id = l.id ORDER BY g.id ASC";
			$sql = "SELECT g.*,l.title as language_title,l.id as language_id,g.language_id as group_lang_id FROM ".$appTable." g inner join languages l on g.language_id = l.id ORDER BY g.id ASC";
			$qq=function_mysql_query($sql,__FILE__);
		
		
		$resArray =array();
		$foundDefault = false;
		while ($ww=mysql_fetch_assoc($qq)) {
			
			$res =array();
		if (!empty($group_id) && $group_id == $ww['id'])
				$row = $ww;
			$sql = "SELECT COUNT(id) FROM affiliates WHERE group_id='".$ww['id']."'";
			$totalAffiliates=mysql_result(function_mysql_query($sql,__FILE__),0);
			
			$allRecLang = $ww['group_lang_id'];
			
			if(strpos($allRecLang ,",")){
				$allRecLang = explode ("," , $ww['group_lang_id']);;
				$allRecLangTitle = array();
				foreach($allRecLang as $k=>$lang_id){
					$myLangs = mysql_fetch_assoc(function_mysql_query("SELECT title from languages where id = " . $lang_id, __FILE__));
					$allRecLangTitle[] = $myLangs['title'];
				}
				$allRecLangTitle = implode(",",$allRecLangTitle);
				$allRecLang = count($allRecLang) ." ". lang('of') ." "  . $allLangsCount . " " . lang("selected");
			}
			else{
				$allRecLangTitle = "";
				$myLangs = mysql_fetch_assoc(function_mysql_query("SELECT title from languages where id = " . $ww['group_lang_id'], __FILE__));
				$allRecLang = $myLangs['title'];
			}
			$res['id'] = $ww['id'];
			$res['language_id'] = $ww['language_id'];
			$res['language_title'] = $allRecLang;
			$res['all_language_title'] = $allRecLangTitle;
			$res['title'] = $ww['title'];
			$res['rdate'] = $ww['rdate'];
			$res['totalAffiliates'] = $totalAffiliates;
			$res['valid'] = $ww['valid'];
			$res['makedefault'] = $ww['makedefault'];
			if ( $ww['makedefault']==1)
				$foundDefault=true;

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
						<td align="center"><a href="'.$set->SSLprefix.'admin/groups.php?act=add&group_id='.$ww['id'].'&language_id='.$ww['language_id'].'">'.lang('Edit').'</a></td>
						<td>'.$ww['title'].'</td>
						'. (empty($ww['all_language_title'])?'<td>'.$ww['language_title'].'</td>':'
						<td class="tooltip1" title=\''. $ww['all_language_title'] .'\'>'.$ww['language_title'].'</td>').'
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/affiliates_list.php?group_id='.$ww['id'].'">'.$ww['totalAffiliates'].'</a></td>
						<td align="center" id="lng_'.$ww['id'].'">'.$currentDefValue.'</td>
						<td align="center" id="lng_valid_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_valid_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
					</tr>';
					
			}
			
		$totalAffiliates=mysql_result(function_mysql_query("SELECT COUNT(id) FROM affiliates WHERE group_id='0'",__FILE__),0);
		$set->content = '<form method="post">
						<input type="hidden" name="act" value="add" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Group').'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1" style="">
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
							<td align="left">'.lang('Group Name').':</td><td><input type="text" name="db[title]" value="'. (!empty($row['title']) ? ($row['title']) :   $db['title']) .'" '.($errors['title'] ? 'style="border: 1px red solid;"' : '').' /></td>
							<td align="left">'.lang('Language').':</td><td><!--select name="db[language_id]">'.$langsOptions . '</select--> 
							<input type="hidden" name="db[language_id]" id="multi_languages"/>
						<select name ="multipleLangs" id = "multipleLangs" multiple="multiple">
						'. $langsOptions .'
						</select>
							'.($errors['language'] ? 'style="border: 1px red solid;"' : '').' </td>
							<td align="left"><input type="submit" value="'.lang('Save').'" /></td>
							</tr>
							<tr><td></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Group List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Action').'</td>
								<td>'.lang('Group Name').'</td>
								<td>'.lang('Language').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Total Affiliates').'</td>
								<td align="center">'.lang('Default Group').'</td>
								<td align="center">'.lang('Available').'</td>
							</tr></thead><tfoot><tr>
								<td>0</td>
								<td></td>
								<td>'.lang('General').'</td>
								<td align="center">-</td>
								<td align="center">-</td>
								<td align="center">'.$totalAffiliates.'</td>
								<td align="center">'.( !$foundDefault ?  xvPic(1) :'<a onclick="window.location.href=\''.$set->SSLprefix.$set->basepage.'?act=makedefault&id=0\'" style="cursor: pointer;">' . xvPic(0).'</a>').'</td>
							</tr>'.$langList.'</tfoot>
						</table>';
		$set->content .= '
		<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
		<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
		<link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'js/tooltipster/css/tooltipster.bundle.min.css" />
		 <link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'js/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tooltipster/js/tooltipster.bundle.min.js"></script>
		<script>
						$(document).ready(function(){
							$("#multipleLangs option[value=\'\']").remove();
								$("#multipleLangs").multipleSelect({
											width: 200,
											placeholder: "'.lang('Select Languages').'"
										});
								$("#multipleLangs").change(function(){
									$("#multi_languages").val($(this).val());
								}); 
								
								var selects = "'. $db['language_id'] .'";
								console.log(selects);
								selects = selects.split(",");
						
								$("#multipleLangs").multipleSelect("setSelects",selects);
								$("#multipleLangs").multipleSelect("refresh");
				
								$(".tooltip1").tooltipster({
								  theme: \'tooltipster-punk\',
								  maxWidth:200
								});
						});
		</script>';
		theme();
		break;
	}

?>