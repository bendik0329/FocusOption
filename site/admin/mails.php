<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'mail_templates';
// var_dump($_POST);
// die ('<br>'.$act);
switch ($act) {
	/* ------------------------------------ [ Manage Mails ] ------------------------------------ */
	
	case "delete":
		
		// $qr = "DELETE from ".  $appTable ." where mailCode= '".$mailCode . "' and language_id = '". $language_id ."' and admin_id >0";
		$qr = "DELETE from ".  $appTable ." where id = '". $id ."' and admin_id >0";
		// die($qr); 
		function_mysql_query($qr ,__FILE__);
		_goto($set->SSLprefix.$set->basepage);
		break;
	case "valid":
		
		//$db=dbGet("'".$mailCode ."'",$appTable);
		$qqValid= function_mysql_query('select * from ' . $appTable . ' where mailCode = "'. $mailCode .'"',__FILE__);
		while($dbValid = mysql_fetch_assoc($qqValid)){
			if ($dbValid['valid']) $valid='0'; else $valid='1';
			updateUnit($appTable,"valid='".$valid."'","id='".$dbValid['id']."'");
		}
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&mailCode='. $mailCode .'&id='.$id.'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	case "add":

		
		$errors="";
		if (!$db['id'])
		$db['id'] = $_POST['db']['id'];
	
	if (!$db['mailCode'])
		$db['mailCode'] = ($_POST['db']['mailCode']);
		
	
		if (!$db['title']) $errors['title'] = 1;
		if (!$db['mailCode'] && !$_POST['db']['mailCode']) $errors['mailCode'] = 1;
		// var_dump($db);
		// die();
		
		$getCode=mysql_fetch_assoc(function_mysql_query("SELECT id FROM mail_templates WHERE mailCode='".$db['mailCode']."' and language_id = ".$db['language_id']. " AND id = '".$db['id']."'",__FILE__));
		// var_dump($getCode);
		// die();
		// if ($getCode['id']) $errors['mailCode'] = 1;
		if (!$db['text']) $errors['text'] = 1;
			
		if (empty($errors)) {
			
			
			$db['title'] = htmlspecialchars($db['title']);
			$db['text'] = htmlspecialchars($db['text']);
			$db['rdate'] = dbDate();
			if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
			$db['admin_id'] = $set->userInfo['id'];
			$db['ip'] = $_SERVER['REMOTE_ADDR'];
			
			$lastID=dbAdd($db,$appTable);
			
			
			_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$lastID.'&language_id='.$db['language_id']);
			}
		else {
		
				$str_error = '';
			foreach ($errors as $errItem => $errMessage) {
				$str_error .= $errItem.'|' . $errMessage ;
				break;
			}
			
				 _goto($set->SSLprefix.$set->basepage . '?id='.$db['id'].'&error=' . $str_error);
			
		}
		break;
	case "addnewlang":
	// var_dump($_POST);
	// die();
		$errors="";
		if (!$db['title']) $errors['title'] = 1;
		if (!$db['mailCode']) $errors['mailCode'] = 1;
		if (!$db['language_id'] || empty($db['language_id'])) $errors['language'] = 1;
		if(!empty($db['language_id'])){
			$getCode=mysql_fetch_assoc(function_mysql_query("SELECT id FROM mail_templates WHERE mailCode='".$db['mailCode']."' AND id != '".$db['id']."' AND language_id=".  $db['language_id'],__FILE__));
			if ($getCode['id']) $errors['language'] = 1;
		}
		if (!$db['text']) $errors['text'] = 1;
		
		if (empty($errors)) {
			
			
			$db['title'] = htmlspecialchars($db['title']);
			$db['text'] = htmlspecialchars($db['text']);
			$db[rdate] = dbDate();
			if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
			$db[admin_id] = $set->userInfo['id'];
			$db[ip] = $_SERVER['REMOTE_ADDR'];
			if($original_id<0){
					$newid= mysql_fetch_assoc(function_mysql_query("select min(id)-1 as id from mail_templates",__FILE__));
					$db['id'] = $newid['id'];
			}
			
			/* echo "<pre>";var_dump($db);
			die(); */
			$fields =  implode(',',array_keys($db));
			$language_id = $db['language_id'];
			$db = array_map(create_function('$e', 'return mysql_real_escape_string(((get_magic_quotes_gpc()) ? stripslashes($e) : $e));'), array_values($db));	
			$values = implode(',', array_map(function($value) {
				return "'" . $value . "'";
			}, array_values($db)));
			
			$result =function_mysql_query("INSERT INTO ". $appTable . " (". $fields .") values (".  $values .")",__FILE__);
			//$lastID=dbAdd($db,$appTable);
			$lastID = mysql_insert_id();
			_goto($set->SSLprefix.$set->basepage.'?id='.$lastID.'&language_id='.$language_id);
		}
	default:
		$pageTitle = 'Manage E-Mail Templates';
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
		
		if ($id) 
			$db=dbGet($id,$appTable);
		else if(isset($mailCode)  && isset($language_id)){
			$sql = "SELECT * from " . $appTable . " WHERE mailCode = '". $mailCode ."' AND language_id = ".$language_id;
			$db = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		}
			
		
		$set->content .= '
		<div class="btn"><a href="'.$set->SSLprefix.$set->basepage.'">'.lang('Add New Template').'</a></div>

		';
		
		
		$langsForTemplateArray=array();
		
		$qqNew=function_mysql_query("SELECT * FROM ".$appTable."  ORDER BY id ASC",__FILE__);
		while ($wwNew=mysql_fetch_assoc($qqNew)) {
			$langsForTemplateArray[$wwNew['mailCode']][]=$wwNew['language_id'];
		}
		
		$sql = "SELECT * FROM ( SELECT * FROM `mail_templates` ORDER BY rdate) t1 GROUP BY mailCode order by t1.id ASC";
		$qq=function_mysql_query($sql,__FILE__);
		
		$mailcode = "";
		while ($ww=mysql_fetch_assoc($qq)) {
			
			if ($set->disableAutoCompleteOnLogin==1 && $ww['is_advertiser_related']==1)
				continue;
				
			
			$sql = 'select count(*) as totalLang from ' . $appTable . ' where mailCode="' . $ww['mailCode'] . '" group by mailCode';
			$countSameLangMailCode = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
			$countSameLangMailCode = !empty($countSameLangMailCode)?$countSameLangMailCode['totalLang']:0;
			
			//$langsForTemplateArray[$ww['mailCode']][]=$ww['language_id'];
			
			$l++;
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td>'.$ww['title'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center">'. ($ww['trigger_name'] == ""?getMailCode($ww['mailCode']):$ww['trigger_name']).'</td>
						<td align="center">'.$ww['mailCode'].'</td>
						<td align="center">'.$countSameLangMailCode.'</td>
						<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'&mailCode='.$ww['mailCode'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
						<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?id='.$ww['id'].'&language_id='.$ww['language_id'].'">'.lang('Edit').  '</a></td>
					</tr>';
			}

			
				
		
		$qry = "SELECT l.title , (g.language_id) as id from groups g inner join languages l on l.id = g.language_id group by g.language_id ";
		// die ($qry);
		$ll=function_mysql_query($qry ,__FILE__);
		while ($wwl=mysql_fetch_assoc($ll)) {
				$ActiveLanguages[$wwl['id']] = $wwl['title'];
				$ActiveLanguagesIDs[] = $wwl['id'];
		}

		
		
		
		$thisTemplateLangsOption = "";
		$countThisTemplateLangsOption = 0;
		// foreach ($ActiveLanguages as $key => $value) {
			$mailcode = isset($db['mailCode']) ? $db['mailCode'] : "";
			if (!empty($mailcode)) {
				foreach ($langsForTemplateArray[$db['mailCode']] as $value) {
					$wwl['id'] = $value;
					$wwl['title'] = $ActiveLanguages[$value];
					$thisTemplateLangsOption.='<option value="'. $wwl['id'] . '" '.(isset($language_id)&& $language_id>0 && $language_id==$wwl['id'] ? " selected " : "" ).'>'. lang($wwl['title']) .'</option>';
					$countThisTemplateLangsOption ++;
				}
			}
			//add disable property to switch to another language of this template selectbox
			$disableThisTemplateLangsDropdown = "";
			if($countThisTemplateLangsOption<=1){
					$disableThisTemplateLangsDropdown = " disabled";
			}
			
		// die (($thisTemplateLangsOption));
		
		$newActiveLangs = array();
		if(isset($db['mailCode']))
		$newActiveLangs =  array_flip(array_diff($ActiveLanguagesIDs,$langsForTemplateArray[$db['mailCode']]));
		
			
		
		
		// ($langsForTemplateArray[$db['mailCode']]);
		
		$languagesList = "";
		$countGroupLangs = 0;
		if($act == 'newlang' || $act == 'addnewlang'){
				$languagesList ='<option value="">'. lang("-Select-") .'</option>';
		}
			foreach ($ActiveLanguages as $key => $value) {
				
				$wwl['id'] = $key;
				$wwl['title'] = $value;
				
				
				/* if ($newlang) {
					$isit = isset($language_id)&& $language_id>0 && $language_id==$wwl['id'] ? true : false;
					
					if (!$isit)
						$languagesList.='<option value="'. $wwl['id'] . '" '.(isset($language_id)&& $language_id>0 && $language_id==$wwl['id'] ? " selected " : "" ).'>'. lang($wwl['title']) .'</option>';
				}
				else { */			
				if($act == 'newlang' || $act=='addnewlang'){
					if(array_key_exists($wwl['id'],$newActiveLangs)){
						$languagesList.='<option value="'. $wwl['id'] . '" >'. lang($wwl['title']) .'</option>';
					}
				}
				else{
						$languagesList.='<option value="'. $wwl['id'] . '" '.(isset($language_id)&& $language_id>0 && $language_id==$wwl['id'] ? " selected " : "" ).'>'. lang($wwl['title']) .'</option>';
				}
				/* } */
				$countGroupLangs ++;
		}
		$set->content .= '
					<br>
					<div class="normalTableTitle" onclick="$(\'#add_new\').slideToggle();" style="cursor: pointer;">'.lang('Template Configuration').'</div>
					<br>
					
		
		<table><tr>';
		if((isset($_GET['id']) || isset($_GET['mailCode'])) && $countGroupLangs>1){
			$set->content .= '<td><div class="btn"><a href="'.$set->SSLprefix.$set->basepage.'?act=newlang&id='.$_GET['id'].'&language_id='.$_GET['language_id'].'">'.lang('Add New Language').'</a></div></td>';
		}
		
		if((isset($_GET['id']) || isset($_GET['mailCode']))  && $db['admin_id'] > 0)
		{
			$set->content .= '<td><div class="btn"><a href="'.$set->SSLprefix.$set->basepage.'?act=delete&id='.$_GET['id'].'">'.lang('Delete This Language').  '</a></div></td>';
		}
		
		$set->content .=' <script type="text/javascript">
		function submitPage(sel){
			location.href="' . $set->basepage . '?mailCode='.$db['mailCode'].'&language_id="+sel.value;
		}
		</script>';
		if((isset($_GET['id']) || isset($_GET['mailCode']))  || isset($act)) {
		$set->content .='<td style="padding-left:30px;">
		<span>'.lang('Switch to another language of this template') . ':  </span>
		<select onchange="submitPage(this)" '. $disableThisTemplateLangsDropdown .'>
		'.$thisTemplateLangsOption . '
		</select>
		</td>';
		}
		$set->content .='</tr>
		</table><br>
		
		
					<div id="add_new">
					<form method="post">';
					
					if($act=='newlang' || $act =='addnewlang'){
						$set->content .='<input type="hidden" name="act" value="addnewlang" /><input type="hidden" name="db[id]"/><input type="hidden" name="original_id" value="'.$db['id'].'" />';
					}
					else{
						
						$set->content .='<input type="hidden" name="act" value="add" /><input type="hidden" name="db[id]" value="'.$db['id'].'" />';
					}
						$set->content .='<table width="100%">
							<tr><td width="150" '.err('title').'>'.lang('Mail Subject').':</td><td><input type="text" name="db[title]" value="'.$db[title].'" autocomplete="off" style="width: 250px;" /></td></tr>
							<tr><td>'.lang('Trigger').':</td><td><input type="text" name="db[trigger_name]" value="'.$db['trigger_name'].'" autocomplete="off" style="width: 250px;" /></td></tr>
							<tr><td '.err('mailCode').'>'.lang('Mail Code').':</td><td><input type="text" name="db[mailCode]" value="'.$db['mailCode'].'" autocomplete="off" '.($db['id'] ? 'readOnly' : '').' style="width: 250px;" /></td></tr>
							<tr><td width="150" '.err('language').'>'.lang('Group Languages').':</td><td><select name="db[language_id]" style="width: 250px;">'.$languagesList  . '</select></td></tr>
							<tr><td></td><td><input type="checkbox" name="valid" '.($db['valid'] ? 'checked' : '').' /> '.lang('Active').'</td></tr>
							<tr><td valign="top" '.err('text').'>'.lang('Content').':</td><td valign="top"><textarea name="db[text]" id="contentMail" cols="80" rows="40">'.$db['text'].'</textarea></td></tr>
							<tr><td></td><td align="right"><input type="submit" value="'.lang('Save').'" /></td></tr>
						</table>
						<link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'css/jquery.cleditor.css" />
						<script type="text/javascript" src="'.$set->SSLprefix.'js/jquery.cleditor.js"></script>
						<script type="text/javascript">
							$(document).ready(function () {
								$("#contentMail").cleditor({
									width:        800,
									height:       400
									});
								});
						</script>
					</form>
					<b><u>'.lang('Variables List').'</u></b><br />
					<ul type="*" style="line-height: 18px;">
						<li><b>{Brand Name}</b> - '.lang('Brand Name').'</li>
						<li><b>{affiliate_id}</b> - '.lang('Affiliate ID').'</li>
						<li><b>{affiliate_username}</b> - '.lang('Affiliate Username').'</li>
						<li><b>{affiliate_name}</b> - '.lang('Affiliate Full Name').'</li>
						<li><b>{affiliate_email}</b> - '.lang('Affiliate E-mail').'</li>
						<!--li><b>{affiliate_password}</b> - '.lang('Affiliate Password').'</li-->
						<li><b>{accountManager_name}</b> - '.lang('Account Manager Name').'</li>
						<li><b>{accountManager_email}</b> - '.lang('Account Manager E-mail').'</li>
						<li><b>{accountManager_IM}</b> - '.lang('Account Manager IM').'</li>
						<li><b>{ticket_id}</b> - '.lang('Ticket ID').'</li>
						<li><b>{custom_field}</b> - '.lang('Custom Variable').'</li>
					</ul>
					</div>
					<hr />
					<div class="normalTableTitle">'.lang('E-mails List').'</div>
					<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
						<thead>
						<tr>
							<td align="center">#</td>
							<td>'.lang('Subject').'</td>
							<td align="center">'.lang('Added Date').'</td>
							<td align="center">'.lang('Trigger').'</td>
							<td align="center">'.lang('Mail Code').'</td>
							<td align="center">'.lang('No. of Languages').'</td>
							<td align="center">'.lang('E-Mail Active').'</td>
							<td align="center">'.lang('Actions').'</td>
						</tr></thead><tfoot>'.$langList.'</tfoot>
					</table>
					<hr />';
		$qq=function_mysql_query("SELECT * FROM mail_sent ORDER BY id DESC LIMIT 50",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$adminInfo=mysql_fetch_assoc(function_mysql_query("SELECT id,first_name,last_name FROM admins WHERE id='".$ww['admin_id']."'",__FILE__));
			$affiliateInfo=mysql_fetch_assoc(function_mysql_query("SELECT id,first_name,last_name FROM affiliates WHERE id='".$ww['admin_id']."'",__FILE__));
			$listEmails .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td align="center">'.$ww['id'].'</td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'">'.$affiliateInfo['first_name'].' '.$affiliateInfo['last_name'].'</a></td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'">'.$affiliateInfo['first_name'].' '.$affiliateInfo['last_name'].'</a></td>
						<td align="center">'.date("d/m/y H:i", strtotime($ww['rdate'])).'</td>
						<td align="center">'.$ww['mailCode'].'</td>
						<td align="center">'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</td>
						<td align="center">'.$ww['opened'].'</td>
						<td align="center">'.($ww['opened_time'] != "0000-00-00 00:00:00" ? xvPic($ww['opened'],1).' '.date("d/m/y H:i", strtotime($ww['opened_time'])) : '-').'</td>
					</tr>';
			$i++;
			}
		$set->content .= '<div class="normalTableTitle" onclick="$(\'#tab_3\').slideToggle();" style="cursor: pointer;">'.lang('Last 50 E-mails Monitor').'</div>
					<div id="tab_3" style="width: 100%; display: none;">
					<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
						<thead>
						<tr>
							<td align="center">#</td>
							<td align="center">'.lang('Sent To').'</td>
							<td align="center">'.lang('Sent At').'</td>
							<td align="center">'.lang('E-Mail Code').'</td>
							<td align="center">'.lang('Manager').'</td>
							<td align="center">'.lang('Viewed').'</td>
							<td align="center">'.lang('Readed').'</td>
						</tr></thead><tfoot>'.$listEmails.'</tfoot>
					</table>
					</div>';
					
			// die ('frfr');
		theme();
		break;
	}

	
	function getMailCode($code){
		
		$code = preg_split('/(?=[A-Z])/',$code);
		$code = str_replace("_"," ",$code);
		return implode(" ",$code);
	}
?>