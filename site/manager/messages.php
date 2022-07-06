<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
require_once('func/func_manager.php');

$userLevel = $set->userInfo['level'];
if ($userLevel=='admin'){
//if (!isAdmin()) _goto($set->SSLprefix.$userLevel.'/');
	$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/".$userLevel."/";
	if (!isAdmin ) _goto($lout);
}
if ($userLevel=='manager'){
	//if (!isManager()) _goto($set->SSLprefix.$userLevel.'/');
	$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/".$userLevel."/";
	if (!isManager()) _goto($lout);
}

$appTable = 'affiliates_msgs';


switch ($act) {
	/* ------------------------------------ [ Manage Messages ] ------------------------------------ */
	
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "delete":
		$sql = "delete  from ". $appTable . " where id=" . $id;
		mysql_query($sql);
		_goto($set->SSLprefix.$set->basepage);
		break;
		
	case "add":
	global $set;
	if (!$db['title']) $errors['title'] = 1;
		if (!$db['text']) $errors['text'] = 1;
		if (empty($errors)) {
			
			if ($userLevel=='manager')
			$db['group_id'] = $set->userInfo['group_id'];
			$db[rdate] = dbDate();
			$db[valid] = 0;
			$db['admin_id'] = $set->userInfo['id'];
			
			// Send to Affiliates
			if ($sendMail) {
				if ($db['group_id']) { // Send to Group
											$qq=function_mysql_query("SELECT * FROM affiliates af inner join WHERE valid='1' AND group_id='".$db['group_id']."' ORDER BY id ASC",__FILE__);
				} else if ($db['affiliate_id']) {
											$q = "SELECT * FROM affiliates WHERE valid='1' AND id='".$db['affiliate_id']."' " . ($userLevel=='manager' ? " and group_id = " . $set->userInfo['group_id'] : "" );
											// die($q);
											$qq=function_mysql_query($q,__FILE__);
					} else if ($db['status_id']) {
											$qq=function_mysql_query("SELECT * FROM affiliates WHERE valid='1' AND id='".$db['status_id']."' " . ($userLevel=='manager' ? " and group_id = " . $set->userInfo['group_id'] : "" ),__FILE__);
					} else if (!$db['group_id'] AND !$db['affiliate_id'] AND !$db['status_id']) {
											$qq=function_mysql_query("SELECT * FROM affiliates WHERE valid='1' ".  ($userLevel=='manager' ? " and group_id ="  .$set->userInfo['group_id'] : "" ) ." ORDER BY id ASC",__FILE__);
					}
					while ($ww=mysql_fetch_assoc($qq)) {
						$set->sendFrom = $set->webMail;
						$set->sendTo = $ww['mail'];

							

						// $find = Array("{custom_field}","{affiliate_username}","{affiliate_name}","{affiliate_email}","{accountManager_name}","{accountManager_email}","{accountManager_IM}","{brand_name}","{affiliate_password}", "{web_address}", "{web_mail}", "{aff_uname}", "{aff_fname}", "{merchant_name}", "{ticket_id}", "{base_url}", "{first_name}", "{last_name}");
						// $replace = Array($set->custom_field,$ww['username'],$ww['first_name'].' '.$ww['last_name'],$ww['mail'],$set->userInfo['first_name'].' '.$set->userInfo['last_name'],$set->userInfo['email'],$set->userInfo['IMUser'],$set->webTitle, $ww, $set->webAddress, $set->webMail, $set->aff_uname, $set->aff_fname, $merchantName, $ticketID, $set->webAddress, $FirstName, $LastName);
		
						//$set->subject = $db['title'];
						// $set->subject = str_replace($find,$replace,$db['title']);
						//$db['title'] = mysql_real_escape_string($db['title']);
						$db['title'] = $db['title'];
						$set->subject = messagesSearchNReplace($ww,$db['title']);
						$set->body = messagesSearchNReplace($ww,$db['text']);
						//$set->body = ($db['text']);
						// $set->body = str_replace($find,$replace,$db['text']);

						
						sendMail();
					}
			}
					/* while ($ww=mysql_fetch_assoc($qq)) {
						$set->sendFrom = $set->webMail;
						$set->sendTo = $ww['mail'];
						$set->subject = $db['title'];
						$set->body = $db['text'];
						sendMail();
						}
					while ($ww=mysql_fetch_assoc($qq)) {
						$set->sendFrom = $set->webMail;
						$set->sendTo = $ww['mail'];
						$set->subject = $db['title'];
						$set->body = $db['text'];
						sendMail();
						}
					while ($ww=mysql_fetch_assoc($qq)) {
						$set->sendFrom = $set->webMail;
						$set->sendTo = $ww['mail'];
						$set->subject = $db['title'];
						$set->body = $db['text'];
						sendMail();
						}
					} */

				
			// Send to Affiliates
			
			$a = dbAdd($db,$appTable);
			// var_dump($a);
			// die();
			_goto($set->SSLprefix.$set->basepage);
			}
	
	default:
		$pageTitle = lang('Messages');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'. $set->SSLprefix.$userLevel.  '/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		if ($id){
			$db=dbGet($id,$appTable);
			//$db['title'] = mysql_real_escape_string($db['title']);
		}
		//$q = "SELECT am.* FROM ".$appTable." am inner join affiliates on am.affiliate_id = affiliates.id  ".($userLevel=='manager' ? ' where affiliates.group_id = ' . $set->userInfo['group_id'] : '' ). " ORDER BY am.id ASC";
		$q = "SELECT am.* FROM ".$appTable." am where am.group_id = " . $set->userInfo['group_id'] . " ORDER BY am.id ASC";
		
		$qq=function_mysql_query($q	,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$adminww = dbGet($ww['admin_id'],"admins");
			
			// $msgTitle =  messagesSearchNReplace($adminww,$ww['title']);
			$msgTitle =  $ww['title'];
			$msgText = $ww['text'];
			
			$delete = ($ww['admin_id'] == $set->userInfo['id']?'<a href="'.$set->SSLprefix.$set->basepage.'?id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="'.$set->basepage.'?act=delete&id='.$ww['id'].'">'.lang('Delete').'</a>':'-');
			
			$group = ($ww['group_id'] ? listGroups($ww['group_id'],1):"");
			$status = ($ww['status_id'] ? listStatus($ww['status_id'],1) : '');
			$affiliate = ($ww['affiliate_id'] ? '<a href="'.$set->SSLprefix.'manager/affiliates.php?act=new&id='.$ww['affiliate_id'].'">'.listAffiliates($ww['affiliate_id'],1): "");
			$all = [];
			if($group != "")
			$all[] = $group;
			if($status != "")
			$all[] = $status; 
			if($affiliate != "")
			$all[] = $affiliate; 
			
			if($group == "" && $status == "" && $affiliate =="")
				$val = lang('All');
			else{
				$val = implode(" / ",$all);
			}
			
			$messagesList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td>'. $delete .'</td>
						<td align="left">'.nl2br($msgTitle).'</td>
						<td align="center">'.$adminww['first_name'].' '.$adminww['last_name'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center">'. $val .'</a></td>
						<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
					</tr>';
			}
		$set->content = '<form method="post">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						<div class="normalTableTitle">'.lang('Add New Message').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table border="0" cellpadding="0" cellspacing="5" id="tab_1">
							<tr>
								<!--td align="left">'.lang('Choose Group').':</td>
								<td><select name="db[group_id]" style="width: 140px;"><option value="">'.lang('All Groups').'</option>'.listGroups($db['group_id']).'</select></td-->
								
								<td align="left">'.lang('Choose Category').':</td><td><select id="category" name="db[status_id]" style="width: 140px;"><option value="">'.lang('All Categories').'</option>'.listStatus($db['status_id']).'</select></td>
								
								<td align="left">'.lang('Specific Affiliate').': <select name="db[affiliate_id]" id="affiliates" style="width: 140px;"><option value="">'.lang('All Affiliate').'</option>'.listManagerAffiliates($db['affiliate_id']).'</select></td>
							</tr><tr>
								<td align="left" valign="top">'.lang('Subject').':</td>
								<td colspan="3" align="left"><!--input type="text" name="db[title]" value="'.$db['title'].'" style="width: 98.5%;" id="fieldClear" /-->
								<textarea name="db[title]" cols="119" rows="1">'.$db['title'].'</textarea>
								</td>
							</tr><tr>
								<td align="left" valign="top">'.lang('Content').':</td>
								<td colspan="3" align="left">
									<textarea name="db[text]" id="contentMail" cols="119" rows="12" style="font-family: Arial; background: #FFF;">'.$db['text'].'</textarea>
								</td>
							</tr>
							<tr>
								<td></td>
								<td align="left"><input type="submit" value="'.lang('Save').'" /></td>
								<td colspan="3" align="right"><input type="checkbox" name="sendMail" /> '.lang('Send by e-mail to the affiliates').'</td>
							</tr>
						</table>
						<link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'css/jquery.cleditor.css" />
						<script type="text/javascript" src="'.$set->SSLprefix.'js/jquery.cleditor.js"></script>
						<script type="text/javascript">
							$(document).ready(function () { 
							
							$("#contentMail").cleditor(); 
							
							$("#category").on("change", function(){
								var cat = $(this).val();
								$.get( "' . $_SERVER['SERVER_HOST'] . '/ajax/getCategoryAffiliates.php?category="+cat+"&group="+'. $set->userInfo['group_id'] .', function(res) {
									try {
										$("#affiliates").html(res);
									} catch (error) {
										console.log(error);
									}
								});
								
							});
							
							});
						</script>
						</div>
						</form>
						';
						$set->content .= '<br>					<b><u>'.lang('Variables List').'</u></b><br />
					<ul type="*" style="line-height: 18px;">
						<li><b>{Brand Name}</b> - '.lang('Brand Name').'</li>
						<li><b>{affiliate_username}</b> - '.lang('Affiliate Username').'</li>
						<li><b>{affiliate_name}</b> - '.lang('Affiliate Full Name').'</li>
						<li><b>{affiliate_email}</b> - '.lang('Affiliate E-mail').'</li>
						<!--li><b>{affiliate_password}</b> - '.lang('Affiliate Password').'</li-->
						<li><b>{accountManager_name}</b> - '.lang('Account Manager Name').'</li>
						<li><b>{accountManager_email}</b> - '.lang('Account Manager E-mail').'</li>
						<li><b>{accountManager_IM}</b> - '.lang('Account Manager IM').'</li>
						<!--li><b>{ticket_id}</b> - '.lang('Ticket ID').'</li-->
						<li><b>{custom_field}</b> - '.lang('Custom Variable').'</li>
					</ul><hr />';
					
							$set->content .= '<br><br>		
						<div class="normalTableTitle">'.lang('Message List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td style="text-align: left;">'.lang('Subject').'</td>
								<td align="center">'.lang('Added By').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Related To').'</td>
								<td align="center">'.lang('Action').'</td>
							</tr></thead><tfoot><tr>
							</tr>'.$messagesList.'</tfoot>
						</table>';
						
			
		theme();
		break;
	}

?>