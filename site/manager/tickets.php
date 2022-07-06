<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$loginType = "manager";
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/".$loginType."/";
if (!isManager()  ) _goto($lout);


$appTable = 'affiliates_tickets';

switch ($act) {
	
	case "add":
	//	if(!$set->isNetwork){
	//		die();
	//	}
		if (!$db['subject']) $errors['subject'] = 1;
		
		if (!$db['text']) $errors['text'] = 1;
		if ($errors) {
			
		} else {
			
			
			
			$db['last_update'] = $db['rdate'] = dbDate();
			$db['ticket_id'] = 0;
			//$db['affiliate_id'] = $set->userInfo['id'];
			
			$qry  = 'SELECT * FROM affiliates WHERE id='.$db['affiliate_id'] . ($loginType=='manager' ? " and group_id='".$set->userInfo['group_id']."'" : "") ;
			
			// die ($qry);
			$affiliate = mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
			if (!($loginType=='manager' && isset($affiliate['id']) &&  $affiliate['group_id'] = $set->userInfo['group_id']))
				_goto($set->basepage.'?act=add');
			
			
			// echo '$aff_grp: ' . $affiliate['group_id'];
			// echo '$set->userinfo: ' . $set->userInfo['group_id'];
			// die();
			
				$db['group_id'] = $affiliate['group_id'];
			
			$db['merchantID'] = aesDec($_COOKIE['mid']);
			$db['aff_readed'] = 0;
			$db['status'] = 'open';
			$db['text'] = htmlentities($db['text']);
			$db['subject'] = strip_tags($db['subject']);
			$lastID=dbAdd($db,$appTable);
			
			//$mailqq=function_mysql_query("SELECT email FROM admins WHERE valid='1' AND affiliate_id=  group_id='".$set->userInfo['group_id']."'",__FILE__);
			$mailqq=function_mysql_query("SELECT mail FROM affiliates WHERE valid='1' AND id='".$db['affiliate_id']."' ". ($loginType=='manager' ? " and group_id='".$set->userInfo['group_id']."'" : "" ),__FILE__);
			$allEmails = Array();
			while ($mailww=mysql_fetch_assoc($mailqq)) $allEmails[] = $mailww['mail'];
			
			 $set->sendTo = implode(';',$allEmails);
			 $set->subject = lang('New Ticket').' ID: TKT-'.$lastID;
			// $set->body = 'Hello,<br />
// <br />
// The affiliate <b>'.$set->userInfo['username'].'</b> has been created a new ticket #'.$lastID.'.<br />
// <br />
// Best Regards,<br />
// '.$set->webTitle.'<br />
// ===================<br />
// Ticket ID: <b>TKT-'.$lastID.'</b>';




			
			//sendMail();
			//sendTemplate('m99761587',$db['affiliate_id'],0,'',$lastID,$db['merchantID']);
			sendTemplate('NewTicketFromAffiliate',$db['affiliate_id'],0,'',$lastID,$db['merchantID']);
			
			_goto($set->SSLprefix.$set->basepage.'?act=view&id='.$lastID);
			}
		
	case "new":
		//if(!$set->isNetwork){
		//	_goto($set->basepage);
		//}
		$pageTitle = lang('Open New Ticket');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$affiliates = function_mysql_query('SELECT id,username FROM affiliates WHERE valid=1 '. ($loginType=='manager' ? " and group_id='".$set->userInfo['group_id']."'" : "").' ORDER BY LOWER(username) ASC',__FILE__);
		$affiliates2 = function_mysql_query('SELECT id,username FROM affiliates WHERE valid=1 '. ($loginType=='manager' ? " and group_id='".$set->userInfo['group_id']."'" : "").' ORDER BY id ASC',__FILE__);
		$affiliateOptions = '<option value=-1>'.lang('Select Affiliate').'</option>';
		
		
		$affiliateOptions2 = '<option value=-1>'.lang('Select Affiliate ID').'</option>';
		while($row = mysql_fetch_assoc($affiliates)){
			$affiliateOptions.='<option '.($row['id']==$affiliate_id ? 'selected' : '').' value="'.$row['id'].'">'.$row['username'].'</option>';
		}
		while($row = mysql_fetch_assoc($affiliates2)){
		//if ($affiliate_id>0)
			$affiliateOptions2.='<option '.($row['id']==$affiliate_id ? 'selected' : '').' value="'.$row['id'].'">'.$row['id'].'</option>';
			//else
			//$affiliateOptions2.='<option value="'.$row['id'].'">'.$row['id'].'</option>';
		}
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Open New Ticket').'</div>
			<div style="background: #F8F8F8; padding: 10px;">
				<form action="'.$set->basepage.'" method="post" '.($set->isNetwork ? 'onSubmit="return checkMerchant()"' : '').'>
				<input type="hidden" name="act" value="add" />
				<table>
					'.($set->isNetwork  || 1>0 ? '
					<tr>
						<td '.err('subject').'>'.lang('Affiliate').':</td>
						<td><select id="affiliateIDSBBN" name="affiliate_id_byName" style="width: 360px;" >'.$affiliateOptions.'</select>&nbsp&nbsp ID: <select id="affiliateIDSB" name="db[affiliate_id]" style="width: 220px;" >'.$affiliateOptions2.'</select></td>
						<script type="text/javascript">
							$(document).ready(function(){
								$("#affiliateIDSBBN").change(function(e){
									$("#affiliateIDSB").val($(this).val());
								});
								$("#affiliateIDSB").change(function(e){
									$("#affiliateIDSBBN").val($(this).val());
								});
							});
						</script>
					</tr>' : '').'
					<tr>
						
						<td '.err('subject').'>'.lang('Ticket Subject').':</td>
						<td><input type="text" name="db[subject]" value="" style="width: 600px;" maxlength="254" /></td>
					</tr><tr>
						<td valign="top" '.err('text').'>'.lang('Ticket Content').':</td>
						<td><textarea name="db[text]" cols="60" rows="8" style="width: 600px; height: 200px;"></textarea></td>
					</tr><tr>
						<td valign="top">'.lang('E-mail').':</td>
						<td><input type="text" name="db[reply_email]" value="'.$set->userInfo['mail'].'" style="width: 200px;" /> '.lang('Please verify your current e-mail address in order to get notifications').'</td>
					</tr><tr>
						<td colspan="2" align="right"><input type="submit" value="'.lang('Send').'" /></td>
					</tr>
				</table>
				</form>
			</div>
			<script>
			
			function checkMerchant(){
				if($("#affiliateIDSB").val()==-1){ 
					
					 $.fancybox({ 
					 closeBtn:false, 
					  minWidth:"250", 
					  minHeight:"180", 
					  autoCenter: true, 
					  afterClose:function(){
						  key.focus();
					  },			  
					  content: "<h1><div style=\'float:left;\'><img src=\''.$set->SSLprefix.'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Please select an affiliate.'). '</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
					  });

						return false; 
					} 
						
			}
			</script>';
		theme();
		break;
		
	case "del":	
		$ww = dbGet($id,$appTable);
		if (!$ww['id'] OR $ww['group_id'] != $set->userInfo['group_id'] OR $ww['ticket_id'] > 0) _goto($set->basepage);
		function_mysql_query("DELETE FROM ".$appTable." WHERE id='".$id."' AND ticket_id > '0'",__FILE__);
		_goto($set->SSLprefix.$set->basepage.'?act=view&id='.$goBack);	
		break;
		
		
	case "resend":	
		$ww = dbGet($id,$appTable);
		
		sendTemplate('m99761587',$ww['affiliate_id'],0,'',$id,$ww['merchantID']);

		_goto($set->SSLprefix.$set->basepage.'?act=view&id='.$goBack);	
		break;
		
	
	case "update":
		$ww = dbGet($db['ticket_id'],$appTable);

		if (!$ww['id'] OR !$db['ticket_id'] OR $ww['group_id'] != $set->userInfo['group_id'] OR $ww['ticket_id'] > 0) _goto($set->basepage);
		if ($errors) {
			} else {
			$db['last_update'] = dbDate();
			$db['admin_id'] = $set->userInfo['id'];
			$db['text'] = htmlentities($db['text']);
			updateUnit($appTable,"status='".($status == "close" && strip_tags($db['text']) ? 'open' : $status)."'","id='".$db['ticket_id']."'");
			if (strip_tags($db['text'])) {
				$lastID=dbAdd($db,$appTable);
				
				$set->sendTo = $ww['reply_email'];
				$set->subject = lang('Update Ticket ID').': TKT-'.$db['ticket_id'];
				$set->body = 'Hello,<br />

	'.lang('Thank you for contacting').' '.$set->webTitle.'.<br />
	'.lang('This email confirms that we replied to your ticket').' TKT-'.$db['ticket_id'].'.<br />
	<br />
	'.lang('Best Regards').',<br />
	'.$set->webTitle.'<br />
	===================<br />
	'.lang('Ticket ID').': <b>TKT-'.$db['ticket_id'].'</b>';
				sendMail();
				
				}
			_goto($set->SSLprefix.$set->basepage.'?act=view&id='.$db['ticket_id']);
			}
	
	case "view":
		$ww = dbGet($id,$appTable);
		if (!$ww['id'] OR $ww['group_id'] != $set->userInfo['group_id'] OR $ww['ticket_id'] > 0) _goto($set->basepage);
		updateUnit($appTable,"readed='1'","id='".$ww['id']."'");
		$affiliateInfo = dbGet($ww['affiliate_id'],"affiliates");
		$pageTitle = 'Support Center';
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$i=1;
		$ticketqq=function_mysql_query("SELECT at.id,at.last_update,at.admin_id,at.affiliate_id,at.text,ad.level FROM ".$appTable." at LEFT  JOIN admins ad on ad.id=at.admin_id WHERE at.ticket_id='".$ww['id']."' ORDER BY at.rdate ASC",__FILE__);
		while ($ticketww=mysql_fetch_assoc($ticketqq)) {
			if ($ticketww['admin_id']) $adminInfo = dbGet($ticketww['admin_id'],"admins");
				else $adminInfo = dbGet($ticketww['affiliate_id'],"affiliates");
			$listTickets .= '<tr style="background: #'.($i % 2 ? 'FFFFFF' : 'EFEFEF').';">
							<td align="left" valign="top">'.date("d/m/Y H:i:s",strtotime($ticketww['last_update'])).'</td>
							<td align="left" valign="top">'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</td>
							<td align="left" valign="top" width="6%">'. ($adminInfo['level']==""?'Affiliate':ucwords($adminInfo['level'])) .'</td>
							<td width="70%" align="left" valign="top">'.nl2br($ticketww['text']).'</td>
							<td width="8%" align="center" valign="top"><a href="'.$set->SSLprefix.$set->basepage.'?act=del&id='.$ticketww['id'].'&goBack='.$id.'">'.lang('Delete').'</a></td>
						</tr>';
			$i++;
			}
		
		$set->content .= '
			<div class="normalTableTitle" style="width: 100%;">Ticket #'.$ww['id'].'</div>
			<div style="background: #F8F8F8; padding: 10px;">
				<table width="100%" class="normal" border="0" cellpadding="6" cellspacing="1" style="background: #DDDDDD;">
					<thead><tr>
						<th>'.lang('Ticket ID').'</th>
						<th>'.lang('Date').'</th>
						<th>'.lang('Time').'</th>
						<th style="text-align: left;">'.lang('Ticket Subject').'</th>
						<th>'.lang('Affiliate').'</th>
						<th>'.lang('Current Status').'</th>
					</tr></thead><tbody>
						<tr style="background: #FFFFFF;">
							<td align="center">'.$ww['id'].'</td>
							<td align="center">'.date("d/m/Y",strtotime($ww['rdate'])).'</td>
							<td align="center">'.date("H:i:s",strtotime($ww['rdate'])).'</td>
							<td>'.$ww['subject'].'</td>
							<td align="center"><a href="'.$set->SSLprefix.'manager/affiliates.php?act=new&id='.$affiliateInfo['id'].'" target="_blank">'.$affiliateInfo['username'].'</a></td>
							<td align="center">'.strtoupper($ww['status']).'</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ticket">
				<b>'.lang('Description').':</b><br />
				'.nl2br($ww['text']).'<br />
				<br />';
				if(!$set->isNetwork){
				$set->content .= '
				<b>E-mail:</b> '.$ww['reply_email'];
				}
				$set->content .= '
				<hr />
				<table width="100%" class="normal" border="0" cellpadding="6" cellspacing="1" style="background: #DDDDDD;">
				'.($listTickets ? '<thead>
					<th>'. lang('Date') .'</th>
					<th>'. lang('Name') .'</th>
					<th>'. lang('User Type') .'</th>
					<th>'. lang('Reply') .'</th>
					<th>'. lang('Action') .'</th>
					</thead>':'').'
					<tbody>
						'.($listTickets ? $listTickets : '<div align="center" style="font-weight: bold; padding: 10px;">'.lang('No new tickets').'</div>').'
					</tbody>
				</table>
			
			<div class="normalTableTitle" style="width: 100%; margin-top: 20px;">'.lang('Update Ticket').'</div>
			<div style="background: #F8F8F8; padding: 10px;">
				<form action="'.$set->SSLprefix.$set->basepage.'" method="post">
				<input type="hidden" name="act" value="update" />
				<input type="hidden" name="db[ticket_id]" value="'.$ww['id'].'" />
				<table>
					<tr>
						<td valign="top" '.err('text').'><b>'.lang('Reply').':</b></td>
						<td><textarea name="db[text]" cols="60" rows="8" style="width: 600px; height: 200px;"></textarea></td>
					</tr><tr>
						<td><b>'.lang('Status').':</b></td>
						<td>
							<select name="status">
								<option value="open" '.($ww['status'] == "open" ? 'selected="selected"' : '').'>'.lang('Open').'</option>
								<option value="proccess" '.($ww['status'] == "proccess" ? 'selected="selected"' : '').'>'.lang('In Process').'</option>
								<option value="waiting" '.($ww['status'] == "waiting" ? 'selected="selected"' : '').'>'.lang('Waiting for Affiliate Response').'</option>
								<option value="close" '.($ww['status'] == "close" ? 'selected="selected"' : '').'>'.lang('Closed').'</option>
							</select>
						</td>
					</tr><tr>
						<td colspan="2" align="right"><input type="submit" value="'.lang('Send Update').'" /></td>
					</tr>
				</table>
				</form>
			</div>
			<style type="text/css">
				table,tr,td,div.ticket {
					font-family: arial;
					}
			</style>';
		theme();
		break;
	
	default:
		$pageTitle = lang('My Group Tickets');
		
		$i=0;
		if ($status) {
			$where = " AND status='".$status."'";
			$pageTitle .= ' ('.strtoupper($status).')';
			}
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$sql = "SELECT * FROM ".$appTable." WHERE ticket_id='0' " . ($set->isNetwork ==1 ? " AND merchantID='".aesDec($_COOKIE['mid'])."' " : "")." AND group_id='".$set->userInfo['group_id']."' ".$where." ORDER BY id DESC";
		//$sql = "SELECT * FROM ".$appTable." WHERE ticket_id='0' AND group_id='".$set->userInfo['group_id']."' ".$where." ORDER BY id DESC";
		
		$ticketqq=function_mysql_query($sql,__FILE__);
		while ($ticketww=mysql_fetch_assoc($ticketqq)) {
			$adminInfo = dbGet($ticketww['affiliate_id'],"affiliates");
			$last_update=mysql_fetch_assoc(function_mysql_query("SELECT last_update FROM ".$appTable." WHERE ticket_id='".$ticketww['id']."' ORDER BY id DESC",__FILE__));
			if ($i % 2) $bg = 'EFEFEF';
				else $bg = 'FFFFFF';
			$allTickets .= '<tr style="background: #'.$bg.';">
							<td align="center">'.strtoupper($ticketww['status']).'</td>
							<td align="center">'.$ticketww['id'].'</td>
							<td align="center">'.date("d/m/Y",strtotime($ticketww['rdate'])).'</td>
							<td align="center">'.date("H:i:s",strtotime($ticketww['rdate'])).'</td>
							<td>'.$ticketww['subject'].'</td>
							<td align="center">'.($last_update['last_update'] != "" ? date("d/m/Y H:i:s",strtotime($last_update['last_update'])) : '-').'</td>
							<td align="center"><a href="'.$set->SSLprefix.'manager/affiliates.php?act=new&id='.$adminInfo['id'].'" target="_blank">'.$adminInfo['username'].'</a></td>
							<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?act=view&id='.$ticketww['id'].'">'.lang('View').' / '.lang('Update').'</a></td>
							<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?act=resend&id='.$ticketww['id'].'">'.lang('Resend Notification').'</a></td>
						</tr>';
			$i++;
			}
			
//		if($set->isNetwork){
			$newTicketBtn = '<input type="button" style="position:absolute; margin-left:20px; margin-top:-5px; padding:10px; background:LIGHT GREY; color:#000; border:0px" value="New ticket" onClick="window.location=\''.$set->SSLprefix.'manager/tickets.php?act=new\';"/>';
	//	}else{
		//	$newTicketBtn = '';
		//}
		$set->content .= $newTicketBtn.'
			
			<div align="right" style="margin-bottom: 10px;"><b>'.lang('Filtering By Status').':</b>
				<select name="status" onchange="location.href=\''.$set->SSLprefix.$set->basepage.'?status=\'+this.value;">
					<option value="" '.(!$status ? 'selected="selected"' : '').'>'.lang('All').'</option>
					<option value="open" '.($status == "open" ? 'selected="selected"' : '').'>'.lang('Open').'</option>
					<option value="proccess" '.($status == "proccess" ? 'selected="selected"' : '').'>'.lang('In Process').'</option>
					<option value="waiting" '.($status == "waiting" ? 'selected="selected"' : '').'>'.lang('Waiting for Affiliate Response').'</option>
					<option value="close" '.($status == "close" ? 'selected="selected"' : '').'>'.lang('Closed').'</option>
				</select>
			</div>
			<div class="normalTableTitle" style="width: 100%;">'.lang('My Tickets').'</div>
			<div style="background: #F8F8F8; padding: 10px;">
				<table width="100%" class="normal" border="0" cellpadding="6" cellspacing="1" style="background: #DDDDDD;">
					<thead><tr>
						<th>'.lang('Current Status').'</th>
						<th>'.lang('Ticket ID').'</th>
						<th>'.lang('Date').'</th>
						<th>'.lang('Time').'</th>
						<th style="text-align: left;">'.lang('Ticket Subject').'</th>
						<th>'.lang('Last Response').'</th>
						<th>'.lang('Affiliate').'</th>
						<th></th>
						<th></th>
					</tr></thead><tbody>
						'.($allTickets ? $allTickets : '<tr><td align="center" colspan="8"><b>'.lang('No Tickets').'</b></td></tr>').'
					</tbody>
				</table>
			</div>
			<style type="text/css">
				table,tr,td,div.ticket {
					font-family: arial;
					}
			</style>';
		theme();
		break;
	}

?>