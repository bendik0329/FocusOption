<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);

$appTable = 'affiliates_msgs';

switch ($act) {
	default:
		if ($id>0 && $set->userInfo['id']>0 && $set->userInfo['group_id']>-1){
			
			
		$fromAdmin = mysql_fetch_assoc(function_mysql_query("SELECT * FROM admins WHERE valid='1' AND group_id='".$set->userInfo['group_id']."' AND id > '1' limit 1;",__FILE__));
			
		$sql = "SELECT * FROM affiliates_msgs WHERE  id='".$id."' AND ((affiliate_id='".$set->userInfo['id']."' OR (affiliate_id='0' and advertiser_id=-1) AND (group_id='0' OR group_id='".$set->userInfo['group_id']."')))";	
		
		$ww=mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		if (!$ww['id']) _goto($set->SSLprefix.'affiliate/');
		
		
		$msgTitle = messagesSearchNReplace($fromAdmin,$ww['title']);
		$msgBody = messagesSearchNReplace($fromAdmin,$ww['text']);
						
						
		$set->pageTitle = $msgTitle;
		//$fromAdmin = dbGet($ww['admin_id'],"admins");
		$set->content .= '<div class="normalTableTitle">'.$msgTitle.'</div>
						<div align="left" style="background: #EFEFEF; padding: 15px;">
							<b>'.lang('From').':</b> '.$fromAdmin['first_name'].' '.$fromAdmin['last_name'].'<br />
							<b>'.lang('For Group').':</b> '.listGroups($set->userInfo['group_id'],1).'<br />
							'.($ww['affiliate_Id'] ? '<b>'.lang('To Affiliate').':</b> '.listAffiliates($ww['affiliate_Id'],1).'<br />' : '').'
							<b>'.lang('Date Sent').':</b> '.dbDate($ww['rdate']).'<br />
							<hr />
							'.nl2br($msgBody).'
						</div>';
						
		}
		//$q = "SELECT * FROM affiliates_msgs WHERE valid='1' AND ((affiliate_id='".$set->userInfo['id']."' OR affiliate_id='0') AND (group_id='0' OR group_id='".$set->userInfo['group_id']."')) ORDER BY id ASC";
		$q = "SELECT * FROM affiliates_msgs WHERE valid='1' AND status_id='".$set->userInfo['status_id']."' AND ((affiliate_id='".$set->userInfo['id']."' OR affiliate_id='0') AND (group_id='0' OR group_id='".$set->userInfo['group_id']."') )";
		//die ($q);
		$qq=function_mysql_query($q	,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$adminww = dbGet($ww['admin_id'],"admins");
			
			 $msgTitle =  messagesSearchNReplace($adminww,$ww['title']);
		
			//$delete = ($ww['admin_id'] == $set->userInfo['id']?' | <a href="'.$set->basepage.'?act=delete&id='.$ww['id'].'">'.lang('Delete').'</a>':'');
			
			$messagesList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td align="left"><a href="'. $set->basepage.'?id='.$ww['id'] .'">'.nl2br($msgTitle).'</a></td>
						<td align="center">'.$adminww['first_name'].' '.$adminww['last_name'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center">'.($ww['group_id'] ? listGroups($ww['group_id'],1) : 'All').'</td>
						<td align="center">'.($ww['status_id'] ? listStatus($ww['status_id'],1) : 'All').'</td>
						<td align="center">'.($ww['affiliate_id'] ? '<a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'">'.listAffiliates($ww['affiliate_id'],1) : lang('All')).'</a></td>
						</tr>';
			}
			$set->content .= '<br><br>		
				<div class="normalTableTitle">'.lang('Message List').'</div>
				<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
					<thead>
					<tr>
						<td>#</td>
						<td style="text-align: left;">'.lang('Subject').'</td>
						<td align="center">'.lang('Added By').'</td>
						<td align="center">'.lang('Added Date').'</td>
						<td align="center">'.lang('Group').'</td>
						<td align="center">'.lang('Affiliate').'</td>
						<td align="center">'.lang('Available').'</td>
						</tr></thead><tfoot><tr>
					</tr>'.$messagesList.'</tfoot>
				</table>';
		
		
		theme();
		break;
	}

?>