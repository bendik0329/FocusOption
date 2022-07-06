<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$from = isset($_GET['from'])?strToDate($_GET['from'] ):strToDate(date("Y-m-d"));
$to   = isset($_GET['to'])?strToDate($_GET['to'] . " 23:59:59"):strToDate(date("Y-m-d 23:59:59"));

$from = sanitizeDate($from);
$to   = sanitizeDate($to);
$appTable = 'logs_activity';
$pageTitle = lang('Logs Activity');
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

$showlastweek = isset($_GET['weekly']) ? $_GET['weekly'] : 0;
switch ($act) {
	/* ------------------------------------ [ Manage Groups ] ------------------------------------ */
	
	case "valid":

	$db=dbGet($id,$appTable);
		if ($db['admin_id_force_allow']) $valid='0'; else $valid='1';
		updateUnit($appTable,"admin_id_force_allow='".$valid."'","id='".$db['id']."'");
		//echo '<a  style="cursor: pointer;">'.xvPic($valid).'</a>';
		 //echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		 echo '';
		die();
		break;
		
	default:
			if ($ip) {
                    $where .= " AND ip='".$ip."' ";
                }
        	
			if (!$showlastweek){
				if($from !=""  && $to != "") {
                    $where .= " AND rdate between '".$from."' and '" . $to . "'";
				}
				else{
					  $where .= " AND rdate between '".date('Y-m-d')."' and '" . date('Y-m-d 23:59:59') . "'";
				}
			}
		
			$row = '';
			if ($showlastweek==1)
				$qry = "select * from logs_activity where rdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY ". $where ." ORDER BY id desc ";
			else
				$qry =("SELECT * FROM logs_activity   ". ($where==""?'': " where 1=1  " . $where) ." ORDER BY id desc ");
			
		
			//die ($qry);
			$qq=function_mysql_query($qry,__FILE__);
			
		while ($ww=mysql_fetch_assoc($qq)) {
			// var_dump($ww);
			// die();
			$user_data = "";
			if($ww['user_id'] != "0"){
				
				if($ww['userType'] == "admin" || $ww['userType'] == "manager" || $ww['userType'] == "advertiser")
					$sql = "select username from admins where id=" . $ww['user_id'];
				else
					$sql = "select username from affiliates where id=" . $ww['user_id'];
				
				$user_data = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
			}
	
			$l++;
			// var_dump($ww);
			// die();
			$country = getIPCountry($ww['ip']);
			
			 $country = $country['countryLONG'];
			if($ww['affiliate_id'] == -1)
				$error = 0;
			else
				$error = 1;
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
					
						<td>'.$ww['id'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td>'.($ww['ip']).'</td>
						<td>'.$country.'</td>
						<td class="tooltip1" title=\''. $ww['theChange'] .'\'>'.(strlen($ww['theChange']) > 30?substr($ww['theChange'],0,28) ."....":$ww['theChange']) .'</td>
						<td>'.$ww['location'].'</td>
						<td>'.$ww['userType'].'</td>
						<td>'.(empty($user_data)?"-":$user_data['username']. ' ('. $ww['user_id'] .')') . '</td>
						<td>'.$ww['_function_'].'</td>
						<td>'.$ww['_file_'].'</td>
					</tr>';
			}
		
		
		
		
		if ($showlastweek==0) { 
			$set->content .= '<div class="btn"><a href="'.$set->SSLprefix.'admin/logactivity.php?weekly=1">'.lang('Last week records').'</a></div>';
			
			}
			else {
			$set->content .= '<div class="btn"><a href="'.$set->SSLprefix.'admin/loginhistory.php">'.lang('Last two hours records').'</a></div>';
				
			}
		
	
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Filter').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get">';
			if($showlastweek){
			$set->content .= '<input type="hidden" name="weekly" value=1 />';
			}
			$set->content .= '<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('IP').'</td>
					<td></td>
				</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><input type="text" id="ip" name="ip" value="'. $_GET['ip'] .'"/></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			</div>';
			
		$set->content .= '
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Logs Activity').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								
								<td>'.lang('ID').'</td>
								<td>'.lang('Date').'</td>
								<td align="center">'.lang('IP').'</td>
								<td align="center">'.lang('Country').'</td>
								<td align="center">'.lang('Change').'</td>
								<td align="center">'.lang('Location').'</td>
								<td align="center">'.lang('User type').'</td>
								<td align="center">'.lang('User ID').'</td>
								<td align="center">'.lang('Function').'</td>

								<td align="center">'.lang('File').'</td>
							</tr></thead><tfoot><tr>
							
								
								
							'.$langList.'</tfoot>
						</table>
						<link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'js/tooltipster/css/tooltipster.bundle.min.css" />
		 <link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'js/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tooltipster/js/tooltipster.bundle.min.js"></script>
		<script>
		$(".tooltip1").tooltipster({
		  theme: \'tooltipster-punk\',
		  maxWidth:200
		});
		</script>';
		theme();
		break;
	}

?>