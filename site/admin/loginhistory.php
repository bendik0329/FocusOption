<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$adminsArr= array();
function getAdminName($adminid= 0 ){
	global $adminsArr;
	if (empty($adminid))
		return $adminid;
	
	if (empty($adminsArr)){
		$q = "select id,level,username from admins";
		$rsc = function_mysql_query($q);
		while ($row = mysql_fetch_assoc($rsc)){
			$adminsArr[$row['id']] = $row;
		}
		
	}
	
	return $adminsArr[$adminid];
}


$appTable = 'loginHistory';
$pageTitle = lang('Login History');
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
                    $where .= " AND lh.ip='".$ip."' ";
                }
                
			if ($login_type) {
                    $where .= " AND lh.type='".$login_type."' ";
			}
			
			if ($affiliate_id) {
                    $where .= " AND lh.affiliate_id='".$affiliate_id."' ";
			}
			$row = '';
			if ($showlastweek==1)
				$qry = "SELECT af.username as af_username,af.valid as af_valid,lh.* FROM loginHistory lh left join affiliates af on lh.affiliate_id = af.id where lh.rdate >= curdate() - INTERVAL DAYOFWEEK(curdate())+6 DAY ". $where ." ORDER BY lh.id desc ";
			else
				$qry =("SELECT af.username as af_username,af.valid as af_valid, lh.* FROM loginHistory lh  left join affiliates af on lh.affiliate_id = af.id  where lh.rdate >= DATE_SUB(NOW(),INTERVAL 2 HOUR) ". $where ." ORDER BY lh.id desc ");
			
			
			// die ($qry);
			$qq=function_mysql_query($qry,__FILE__);
			
		while ($ww=mysql_fetch_assoc($qq)) {
			// var_dump($ww);
			// die();
		
	 $login_as_affiliate_by_user_id =  empty($ww['login_as_affiliate_by_user_id']) ? "-" : getAdminName($ww['login_as_affiliate_by_user_id']);
	 if (!empty($login_as_affiliate_by_user_id) && $login_as_affiliate_by_user_id<>'-'){
	 // var_dump($login_as_affiliate_by_user_id);
	 $login_as_affiliate_by_user_id = $login_as_affiliate_by_user_id['username'] . ' ( ' . $login_as_affiliate_by_user_id['level'] . ' ) ';
	 }
	 
			$l++;
			// var_dump($ww);
			// die();
			$country = getIPCountry($ww['ip']);
			
			$countryShort = $country['countrySHORT'];
		
			 $country = $country['countryLONG'];
			 
			 if(isset($country_id) && $country_id != "" && $country_id!="-")
			if($countryShort != $country_id) continue;
		
			if($ww['affiliate_id'] == -1)
				$error = 0;
			else
				$error = 1;
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$l.'</td>
						<td>'.$ww['id'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td>'.($ww['username']).'</td>
						<td>'.($ww['password']).'</td>
						<td>'.ucwords($ww['type']).'</td>
						<td>'.($ww['affiliate_id']==-1 ? "" : '<a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.($ww['affiliate_id']==-1 ? "" : $ww['affiliate_id']).'" target="_blank">'.($ww['affiliate_id']==-1 ? "" : $ww['affiliate_id']).'</a>').'</td>
						
						<td>'.($ww['af_valid']==1 ? lang("Active") : ($ww['af_valid']==null ? "" : lang("Pending") )).'</td>
						<td>'.($ww['ip']).'</td>
						<td>'.$country.'</td>
						<td>'.($ww['HTTP_USER_AGENT']).'</td>
						<td>'.($login_as_affiliate_by_user_id).'</td>
						<td align="center">'.xvPic($error).'</td>
						<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.($error==0 && $ww['admin_id_force_allow'] ==0? lang('Release'):'').'</a></td>
					</tr>';
			}
		
		
		
		
		if ($showlastweek==0) { 
			$set->content .= '<div class="btn"><a href="'.$set->SSLprefix.'admin/loginhistory.php?weekly=1">'.lang('Last week records').'</a></div>';
			
			}
			else {
			$set->content .= '<div class="btn"><a href="'.$set->SSLprefix.'admin/loginhistory.php">'.lang('Last two hours records').'</a></div>';
				
			}
		
		$login_types = "
		<option value=''>". lang('Select') ."</option>
		<option value='admin'". (isset($_GET['login_type'])&&($_GET['login_type']=='admin')?' selected':'') .">". lang('Admin') ."</option>
		<option value='manager' ". (isset($_GET['login_type'])&&($_GET['login_type']=='manager')?' selected':'') .">". lang('Manager') ."</option>
		<option value='affiliate'". (isset($_GET['login_type'])&&($_GET['login_type']=='affiliate')?' selected':'') .">". lang('Affiliate') ."</option>
		";
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Filter').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">';
			if($showlastweek){
			$set->content .= '<input type="hidden" name="weekly" value=1 />';
			}
			$set->content .= '<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Affiliate Id').'</td>
					<td>'.lang('IP').'</td>
					<td>'.lang('Login type').'</td>
					<td>'.lang('Country').'</td>
					<td></td>
				</tr><tr>
					<td><input type="text" id="affiliate_id" name="affiliate_id" value="'. $_GET['affiliate_id'] .'"/></td>
					<td><input type="text" id="ip" name="ip" value="'. $_GET['ip'] .'"/></td>
					<td><select name="login_type" id="login_type" style="width: 150px;">'. $login_types .'</select></td>
					<td><select name="country_id" id="country_id" style="width: 150px;">'. getCountries($country_id) .'</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			</div>';
			
		$set->content .= '
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Login History Log').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('ID').'</td>
								<td>'.lang('Date').'</td>
								<td align="center">'.lang('Username').'</td>
								<td align="center">'.lang('Password Length').'</td>
								<td align="center">'.lang('Login Type').'</td>
								<td align="center">'.lang('User ID').'</td>

								<td align="center">'.lang('User State').'</td>
								<td align="center">'.lang('IP').'</td>
								<td align="center">'.lang('Country').'</td>
								<td align="center">'.lang('HTTP_USER_AGENT').'</td>
								<td align="center">'.lang('Logged in As Affiliate by').'</td>
								<td align="center">'.lang('Login State').'</td>
								<td align="center">'.lang('Force Release').'</td>
							</tr></thead><tfoot><tr>
							
								
								
							'.$langList.'</tfoot>
						</table>';
		theme();
		break;
	}

?>