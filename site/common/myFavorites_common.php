<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
//require_once('common/global.php');


	$level =$set->userInfo['level'];
	$array  = array();
	if (empty($level)){
			$level = $_SERVER['REQUEST_URI'];
			$level = ltrim($level,'/');
			$exp = explode('/',$level);
			$level = $exp[0];
	}
	$user_id =$set->userInfo['id'];
	
if ($level=='admin'){
	if (!isAdmin()) _goto($set->SSLprefix.'admin/');
}
else if ($level=='manager'){
	
	if (!isManager()) _goto($set->SSLprefix.$level.'/');
}
else if ($level=='affiliate'){
	
	if (!isLogin()) _goto($set->SSLprefix.$level.'/');
}
else 
	_goto($set->SSLprefix.$level . '/');

$appTable = 'users_reports';
$pageTitle = lang('My Favourites');

$set->breadcrumb_title =  lang($pageTitle);
$set->pageTitle = '
<style>
.pageTitle{
	padding-left:0px !important;
}
</style>
<ul class="breadcrumb">
	<li><a href="'.$set->SSLprefix.$level.'/">'.lang('Dashboard').'</a></li>
	<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
	<li><a style="background:none !Important;"></a></li>
</ul>';


switch ($act) {
	/* ------------------------------------ [ Manage Groups ] ------------------------------------ */
	
	case "del_fav":
	
		$sql = "delete from " . $appTable . " where user_id= " . $user_id . " and level = '" .$level . "' and id = " . $id;
		function_mysql_query($sql);
		_goto($set->basepage);
		
	case "edit":
		
		$sql = "update " . $appTable . " set report_name = '". $report_name ."', url = '". $report_url ."'  where user_id= " . $user_id . " and level = '" .$level . "' and id = " . $report_id;
		function_mysql_query($sql);
		_goto($set->basepage);
	
	default:
		
			if (!empty($level) && !empty($user_id))
			$qry = "select * from users_reports where user_id = " . $user_id   . " and level='" .$level . "'  ORDER BY lower(report_name) desc ";
				else
			$qry = "select * from users_reports where id = 0 ;";
		
		
			$qq=function_mysql_query($qry,__FILE__);
			
			while ($ww=mysql_fetch_assoc($qq)) {
			$user_data = "";
			
			if($ww['user_id'] != "0"){
				
				if($ww['level'] == "admin" || $ww['level'] == "manager" || $ww['level'] == "advertiser")
					$sql = "select username from admins where id=" . $ww['user_id'];
				else
					$sql = "select username from affiliates where id=" . $ww['user_id'];
				
				$user_data = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
			}
	
			$l++;
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
					
						<td>'.$ww['id'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td><a href="'. $ww['url'] .'" target="_blank">'.($ww['report_name']).'</a></td>
						<td>'.$ww['level'].'</td>
						<!--td>'.$ww['url'].'</td-->
						<td>'.$ww['report'].'</td>
						<td>'.$user_data['username'].'</td>
						<td><a href="'. $set->SSLprefix.$set->basepage .'?id='. $ww['id'] .'">'  . lang('Edit') . '</a> | <a href="javascript:void(0)" class="deleteFav" data-id='. $ww['id'] .'>' . lang('Delete') . '</a></td>
					</tr>';
			}
		
		
		
		if(isset($id)){
			$db = mysql_fetch_assoc(function_mysql_query("select * from users_reports where user_id= " . $user_id . " and level = '" .$level . "' and id = " . $id));
			
			$set->content .= '
			
			<div class="normalTableTitle" style="cursor: pointer;">'.lang('Edit Favorites Record').'</div>
			<form method="get">
			<input type="hidden" name="act" value="edit">
			<input type="hidden" name="report_id" value="'. $id .'">
				<table><tr>
						<td>'.lang('Report Name').'</td><td><input type="text" name="report_name" value="'.$db['report_name'].'" id="report_name" style="width: 300px;"></td></tr>
						<tr><td>'.lang('Report URL').'</td><td>
						<textarea name = "report_url" rows=5 cols=42>'. $db['url'] .'</textarea>
						</td>
					</tr>
					<tr>
					<td colspan=2><input type="submit" value="'. lang('Save') .'"></td>
					</tr>
					</table>
			</form>
			</div>
			';
		}
		

		
		$set->content .= '
					<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('My Favorites').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								
								<td>'.lang('ID').'</td>
								<td>'.lang('Date').'</td>
								<td align="center">'.lang('Report Name').'</td>
								<td align="center">'.lang('User Level').'</td>
								<!--td align="center">'.lang('URL').'</td-->
								<td align="center">'.lang('Report').'</td>
								<td align="center">'.lang('User Name').'</td>
								<td align="center">'.lang('Actions').'</td>
								</tr></thead><tfoot><tr>
							
								
								
							'.$langList.'</tfoot>
						</table>
						
		<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
		<script>
		
		$(document).ready(function(){
				$(".deleteFav").on("click",function(){
					id = $(this).data("id");
						$.prompt("'. lang("Are you sure you want to delete this record?") .'", {
								top:200,
								title: "'. lang('My Favorites') .'",
								buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
								submit: function(e,v,m,f){
									if(v){
										var url = "'.$set->SSLprefix.$level.'/myFavorites.php?act=del_fav&id="+ id;
										window.location.href= url;
									}
									else{
										//
									}
								}
							})
				});
		})
		</script>';
		theme();
		break;
	}

?>