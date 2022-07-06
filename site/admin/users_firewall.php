<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);


		//$set->pageTitle = lang('Maintenance');
		$set->breadcrumb_title = lang('Users Firewall');
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.'admin/users_firewall.php" class="arrow-left">'.lang('Users Firewall').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		
$appTable = 'users_firewall';
// var_dump($_POST);
// die ('<br>'.$act);
switch ($act) {
	/* ------------------------------------ [ Manage Mails ] ------------------------------------ */
	
	case "delete":
		
		$qr = "DELETE from ".  $appTable ." where id = ". $id;
		
		if($set->activateLogs){
		//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = "Deleted ID " . json_encode($id);
			$fields['country'] = '';
			$fields['location'] = 'User Firewall';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'Delete Users Firewall';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		
		function_mysql_query($qr ,__FILE__);
		_goto($set->SSLprefix.$set->basepage);
		break;
		
	case "valid":
		$qqValid= function_mysql_query('select * from ' . $appTable . ' where id = "'. $id .'"',__FILE__);
		$dbValid = mysql_fetch_assoc($qqValid);
		if ($dbValid['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$id."'");
		
		if($set->activateLogs){
		//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($_GET);
			$fields['country'] = '';
			$fields['location'] = 'User Firewall';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'Vaid/Invalid Users Firewall';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		
		
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$id.'\',\'lng_'.$id.'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "add":
		if(isset($db['id']) && !empty($db['id'])){
			//edit case
			//updateUnit($appTable,"valid='".$valid."'","id='".$id."'");
			$db['set_by_user_id'] = $set->userInfo['id'];
			$db['IPs'] = str_replace(",","|",$db['IPs']);
			$lastID=dbAdd($db,$appTable);
		
		}
		else{
			//add case
			unset($db['id']);
			$db['set_by_user_id'] = $set->userInfo['id'];
			$db['IPs'] = str_replace(",","|",$db['IPs']);
			$lastID=dbAdd($db,$appTable);
		}
		
		if($set->activateLogs){
		//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($db);
			$fields['country'] = '';
			$fields['location'] = 'User Firewall';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = (isset($db['id']) && !empty($db['id'])?'Edit' : 'Add') . ' Users Firewall';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		
			_goto($set->SSLprefix.$set->basepage);
		break;
	
	default:
		
		if ($id) 
			$db=dbGet($id,$appTable);
		
		
		$sql = "SELECT uf.*,a.username,a.level FROM ".$appTable." uf left join admins a on uf.set_by_user_id = a.id  ORDER BY id Desc";
		$qq=function_mysql_query($sql,__FILE__);
		
		while ($ww=mysql_fetch_assoc($qq)) {
			
			
			$l++;
			$firewallList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center" style="text-transform:capitalize">'. $ww['username']. ' - '. $ww['set_by_user_id'] .' ('. strtoupper($ww['level']) .  ')' .'</td>
						<td align="center">'.str_replace("|",",",$ww['IPs']).'</td>
						<td align="center" style="text-transform:capitalize">'.$ww['type'].'</td>
						<td align="center">'.$ww['comment'].'</td>
						<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
						<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?id='.$ww['id'].'">'.lang('Edit').  '</a> | <a href="javascript:void(0)" class="deleteFirewall" data-id="'. $ww['id'] .'">'.lang('Delete').  '</a></td>
					</tr>';
			}

			$set->content .= '<div class="btn"><a href="'.$set->SSLprefix.$set->basepage.'">'.lang('Add New Record').'</a></div>';
			
					$set->content .= '
					<div class="normalTableTitle"  style="cursor: pointer;">'.(isset($id)?lang('Edit Record'):lang('Add New Record')).'</div>
					<br>
					<form method="post">
					<input type="hidden" name="act" value="add" /><input type="hidden" name="db[id]" value="'. $db['id'] .'"/>
					<table width="100%">
							<tr>
									<td width="150">'.lang('IPs').':</td>
									<td>'.lang('Type').':</td>
								<td valign="top">'.lang('Comment').':</td>
							</tr>
							<tr>
							<td><textarea name="db[IPs]" cols="80" rows="1" required>'.  str_replace("|",",",$db['IPs']).'</textarea></td>
							
							
							
							
							<td><select name="db[type]" style="width:50px">
							<option value ="" '. (isset($db['type']) && empty($db['type'])?" selected" :"") .' >'. lang('All') . '</option>
							<option value ="traffic" '. (isset($db['type']) && $db['type']=='traffic'?" selected" :"") .' >'. lang('Traffic') . '</option>
							<option value ="login" '. (isset($db['type']) && $db['type']=='login'?" selected" :"") .'>'. lang('Login') . '</option>
							</select></td>
							
							<td valign="top"><textarea name="db[comment]"  cols="80" rows="1">'.$db['comment'].'</textarea></td>
							<td></td><td></td><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
						</table>
					
					</div>
					
					<div class="normalTableTitle">'.lang('Users Firewall').'</div>
					<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
						<thead>
						<tr>
							<td align="center">#</td>
							<td align="center">'.lang('Added Date').'</td>
							<td align="center">'.lang('Set by User').'</td>
							<td align="center">'.lang('IPs').'</td>
							<td align="center">'.lang('Type').'</td>
							<td align="center">'.lang('Comment').'</td>
							<td align="center">'.lang('Blocked').'</td>
							<td align="center">'.lang('Actions').'</td>
						</tr></thead><tfoot>'.$firewallList.'</tfoot>
					</table>
					<hr />
					<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
					<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
					<script>
					$(document).ready(function(){
						
						$(".deleteFirewall").on("click",function(){
							
							var id = $(this).data("id");
							
							$.prompt("'.lang('Are you sure you want to delete this record').'", {
								top:200,
								title: "'. lang('Delete Record') .'",
								buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
								submit: function(e,v,m,f){
									if(v){
										var url = "'.$set->SSLprefix.$set->basepage .'?act=delete&id="+id;
										window.location.href= url;
									}
									else{
										//
									}
								}
							});
							
							
						});
						
					});
					</script>
					';
		
			// die ('frfr');
		theme();
		break;
	}

?>