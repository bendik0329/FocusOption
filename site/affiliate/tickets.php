<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);

$appTable = 'affiliates_tickets';

switch ($act) {
	case "add":
		if (!$db['subject']) $errors['subject'] = 1;
		if (!$db['text']) $errors['text'] = 1;
		if ($errors) {
			} else {
			$db['last_update'] = $db['rdate'] = dbDate();
			$db['ticket_id'] = 	0;
			$db['affiliate_id'] = $set->userInfo['id'];
			$db['group_id'] = $set->userInfo['group_id'];
			$db['status'] = 'open';
			$db['text'] = htmlentities($db['text']);
			$db['subject'] = strip_tags($db['subject']);
			$lastID=dbAdd($db,$appTable);

			if(!$set->isNetwork)
				$sql = "SELECT email FROM admins WHERE valid='1' AND  group_id='".$set->userInfo['group_id']."'";
			else
				$sql = "SELECT email FROM admins WHERE valid='1' AND relatedMerchantID='".$db["merchantID"]."' AND group_id='".$set->userInfo['group_id']."'";
		
			$mailqq=function_mysql_query($sql,__FILE__);
			$allEmails = Array();
			while ($mailww=mysql_fetch_assoc($mailqq)) $allEmails[] = $mailww['email'];
			
			$set->sendTo = implode(';',$allEmails);
			$set->subject = 'New Ticket ID: TKT-'.$lastID;
			 $set->body = 'Hello,<br />
				<br />
			 The affiliate <b>'.$set->userInfo['username'].'</b> has been created a new ticket #'.$lastID.'.<br />
			 <br />
			 Best Regards,<br />
			 '.$set->webTitle.'<br />
			 ===================<br />
			 Ticket ID: <b>TKT-'.$lastID.'</b>';
			sendMail();
			
			//send email to affiliate
			sendTemplate('NewTicketFromAffiliate',$db['affiliate_id'],0,'',$lastID);

			
			_goto($set->SSLprefix.$set->basepage.'?act=view&id='.$lastID);
			}
		
	case "new":
		$set->pageTitle = lang('Open New Ticket');
		$userMercants = $set->userInfo['merchants'];
		
		$userMercants = str_replace("|",",",$userMercants);		

		$sql = 'SELECT id,name FROM merchants WHERE id in ('. $userMercants .') and valid=1';

		$merchants = function_mysql_query($sql,__FILE__);
		$merchantOptions = '<option value=-1>Select Merchant</option>';
		while($row = mysql_fetch_assoc($merchants)){
			$merchantOptions.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
		}
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Open New Ticket').'</div>
			<div style="background: #F8F8F8; padding: 10px;">
				<form action="'.$set->SSLprefix.$set->basepage.'" method="post" '.($set->isNetwork ? 'onSubmit="if($(\'#merchantIDSB\').val()==-1){ alert(\'Please select a merchant\'); return false; } return true;"' : '').'>
				<input type="hidden" name="act" value="add" />
				<table>
					'.($set->isNetwork ? '
					<tr>
						<td '.err('subject').'>'.lang('Merchant:').':</td>
						<td><select id="merchantIDSB" name="db[merchantID]" style="width: 610px;" >'.$merchantOptions.'</select></td>
					</tr>' : '').'
					<tr>
						<td '.err('subject').'>'.lang('Ticket Subject').':</td>
						<td><input type="text" name="db[subject]" value="" style="width: 600px;" maxlength="254" /></td>
					</tr><tr>
						<td valign="top" '.err('text').'>'.lang('Ticket Content').':</td>
						<td><textarea name="db[text]" cols="60" rows="8" style="width: 600px; height: 200px;"></textarea></td>
					</tr>';
					if ($_SESSION['isam']==0) {
						$set->content .= '
						<tr><td valign="top">'.lang('Your E-mail').':</td>
						<td><input type="text" name="db[reply_email]" value="'.$set->userInfo['mail'].'" style="width: 200px;" /> '.lang('Please verify your current e-mail address in order to get notifications').'</td>
					</tr>'; 
					}$set->content .= '
					<tr>
						<td colspan="2" align="right"><input type="submit" value="'.lang('Send').'" /></td>
					</tr>
				</table>
				</form>
			</div>';
		theme();
		break;
	
	case "update":
		$ww = dbGet($db['ticket_id'],$appTable);
		if (!$ww['id'] OR !$db['ticket_id'] OR $ww['affiliate_id'] != $set->userInfo['id'] OR $ww['ticket_id'] > 0) _goto($set->basepage);
		if ($errors) {
			} else {
			$db['last_update'] = dbDate();
			$db['affiliate_id'] = $set->userInfo['id'];
			$db['text'] = htmlentities($db['text']);
			updateUnit($appTable,"status='".$status."'","id='".$db['ticket_id']."'");
			if (strip_tags($db['text'])) {
				$lastID=dbAdd($db,$appTable);

				$mailqq=function_mysql_query("SELECT email FROM admins WHERE valid='1' AND group_id='".$set->userInfo['group_id']."'",__FILE__);
				$allEmails = Array();
				while ($mailww=mysql_fetch_assoc($mailqq)) $allEmails[] = $mailww['email'];
				
				$set->sendTo = implode(',',$allEmails);
				$set->subject = 'Update Ticket ID: TKT-'.$db['ticket_id'];
				$set->body = 'Hello,

	The Affiliate <b>'.$set->userInfo['username'].'</b> has updated the ticket #'.$db['ticket_id'].'.<br />
	<br />
	Best Regards,<br />
	'.$set->webTitle.'<br />
	===================<br />
	Ticket ID: <b>TKT-'.$db['ticket_id'].'</b>';
				sendMail();
				}
			_goto($set->SSLprefix.$set->basepage.'?act=view&id='.$db['ticket_id']);
			}
	
	case "view":
		$ww = dbGet($id,$appTable);
		if (!$ww['id'] OR $ww['affiliate_id'] != $set->userInfo['id'] OR $ww['ticket_id'] > 0) _goto($set->basepage);
		updateUnit($appTable,"readed='1'","id='".$ww['id']."'");
		$set->pageTitle = 'Support Center';
		$i=1;
		$ticketqq=function_mysql_query("SELECT at.id,at.last_update,at.admin_id,at.affiliate_id,at.text,merchants.name,ad.level AS merchantName FROM ".$appTable." at LEFT  JOIN admins ad on ad.id=at.admin_id LEFT JOIN merchants ON at.merchantID=merchants.id WHERE at.ticket_id='".$ww['id']."' ORDER BY at.rdate ASC",__FILE__);
		//die("SELECT at.id,at.last_update,at.admin_id,at.affiliate_id,at.text,merchants.name AS merchantName FROM ".$appTable." at LEFT JOIN merchants ON at.merchantID=merchants.id WHERE at.id='".$ww['id']."' ORDER BY at.rdate ASC");
		while ($ticketww=mysql_fetch_assoc($ticketqq)) {
			$merchantName = $ticketww['merchantName'];
			if ($ticketww['admin_id']) $adminInfo = dbGet($ticketww['admin_id'],"admins");
				else $adminInfo = dbGet($ticketww['affiliate_id'],"affiliates");
			updateUnit($appTable,"readed='1'","id='".$ticketww['id']."'");
			$listTickets .= '<tr style="background: #'.($i % 2 ? 'FFFFFF' : 'EFEFEF').';">
							<td align="left" valign="top">'.date("d/m/Y H:i:s",strtotime($ticketww['last_update'])).'</td>
							<td align="left" valign="top">'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</td>
							<td align="left" valign="top" width="6%">'. ($adminInfo['level']==""?'Affiliate':ucwords($adminInfo['level'])) .'</td>
							<td width="70%" align="left" valign="top">'.nl2br($ticketww['text']).'</td>
						</tr>
						
						
						';
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
						'.($set->isNetwork ? '<th>'.lang('Merchant').'</th>' : '').'
						<th>'.lang('Current Status').'</th>
					</tr></thead><tbody>
						<tr style="background: #FFFFFF;">
							<td align="center">'.$ww['id'].'</td>
							<td align="center">'.date("d/m/Y",strtotime($ww['rdate'])).'</td>
							<td align="center">'.date("H:i:s",strtotime($ww['rdate'])).'</td>
							<td>'.$ww['subject'].'</td>
							<td align="center">'.$set->userInfo['username'].'</td>
							'.($set->isNetwork ? '<td align="center">'.$merchantName.'</td>' : '').'
							<td align="center">'.strtoupper($ww['status']).'</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="ticket" style="padding: 10px;">
				<b>'.lang('Description').':</b><br />
				'.nl2br($ww['text']).'<br />
				<br />
				'.(!$set->isNetwork ? '<b>'.lang('E-mail').':</b> '.$ww['reply_email'] : '').'
				<hr />
				<table width="100%" class="normal" border="0" cellpadding="6" cellspacing="1" style="background: #DDDDDD;">
				'.($listTickets ?'<thead>
					<th>'. lang('Date') .'</th>
					<th>'. lang('Name') .'</th>
					<th>'. lang('User Type') .'</th>
					<th>'. lang('Reply') .'</th>
					<th>'. lang('Action') .'</th>
					</thead>':'').'
					<tbody>
						'.($listTickets ? $listTickets : '<div align="center" style="font-weight: bold; padding: 10px;">'.lang('No new messages').'</div>').'
					</tbody>
				</table>
			</div>
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
						<td colspan="2" align="right"><input type="submit" value="'.lang('Send').'" /></td>
					</tr>
				</table>
				</form>
			
			<style type="text/css">
				table,tr,td,div.ticket {
					font-family: arial;
					}
			</style>
			
			
			
			';
		theme();
		break;
	
	default:
		
		$set->pageTitle = lang('Support');
		
		$pageTitle = lang('Support');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		// $set->content .= '<div class="btn"><a href="affiliate/tickets.php?act=new">'.lang('Add New').'</a></div>';
		$i=0;
		if ($status) {
			$where = " AND at.status='".$status."'";
			$set->pageTitle .= ' ('.strtoupper($status).')
			
			

			';
			}
			
		$q = "SELECT at.*, merchants.name AS merchantName FROM ".$appTable." at LEFT JOIN merchants ON at.merchantID=merchants.id WHERE at.ticket_id='0' AND at.affiliate_id='".$set->userInfo['id']."' ".$where." ORDER BY at.id DESC";
		$ticketqq=function_mysql_query($q,__FILE__);
//		die ($q);

		while ($ticketww=mysql_fetch_assoc($ticketqq)) {
			if ($ticketww['admin_id']) $adminInfo = dbGet($ticketww['admin_id'],"admins");
				else $adminInfo = dbGet($ticketww['affiliate_id'],"affiliates");
			$last_update=mysql_fetch_assoc(function_mysql_query("SELECT last_update FROM ".$appTable." WHERE ticket_id='".$ticketww['id']."' ORDER BY id DESC",__FILE__));
			$readed=mysql_result(function_mysql_query("SELECT COUNT(id) FROM ".$appTable." WHERE ticket_id='".$ticketww['id']."' AND readed='0'",__FILE__),0);
			if ($i % 2) $bg = 'EFEFEF';
				else $bg = 'FFFFFF';
			if ($readed > 0) $bg = 'EDFFF5';
			$allTickets .= '<tr style="background: #'.$bg.';">
							<td align="center">'.$ticketww['id'].'</td>
							<td align="center">'.date("d/m/Y",strtotime($ticketww['rdate'])).'</td>
							<td align="center">'.date("H:i:s",strtotime($ticketww['rdate'])).'</td>
							<td>'.$ticketww['subject'].'</td>
							'.($set->isNetwork ? '<td>'.$ticketww['merchantName'].'</td>' : '').'
							<td align="center">'.($last_update['last_update'] != "" ? date("d/m/Y H:i:s",strtotime($last_update['last_update'])) : '-').'</td>
							<td align="center">'.strtoupper($ticketww['status']).'</td>
							<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?act=view&id='.$ticketww['id'].'">'.lang('View / Update').'</a></td>
						</tr> 
						
						
						
						
						';
			$i++;
			}

			$userMercants = $set->userInfo['merchants'];
			
			$userMercants = str_replace("|",",",$userMercants);		

			$sql = 'SELECT id,name FROM merchants WHERE id in ('. $userMercants .') and valid=1';

			$merchants = function_mysql_query($sql,__FILE__);
			$merchantOptions = '<option value=-1>Select Merchant</option>';
			while($row = mysql_fetch_assoc($merchants)){
				$merchantOptions.='<option value="'.$row['id'].'">'.$row['name'].'</option>';
			}

			$set->content .= '
			<div class="Suport-page">
				<div class="suportpage-top">
					<div class="suport-search">
						<div class="billing-page-id pb-0">
							<div class="search-payment">
								<label>Search Payment ID<label>
							</div>
							<div class="SearchPayment-input">
								<input type="text">
								<p><i class="fa fa-search"></i></p>
							</div>
						</div>
					</div>
					<div class="faq-section">
						<div class="faq-page">
							<div class="faq-link">
								<a href="'.$set->SSLprefix.'affiliate/faq.php"> F A Q </a>
							</div>
							<div class="add-new-ticket">
								<button type="button" class="btn" data-toggle="modal" data-target="#exampleModalCenter">
								Add new ticket
							</button>
							
						  <!-- Modal -->
						  <div class="modal fade HtmlCode-modal" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
						  	<div class="modal-dialog modal-dialog-centered" role="document">
								<div class="modal-content html-modal-content">
							  		<div class="modal-header html-model-header">
										<h5 class="modal-title" id="exampleModalLongTitle">Open New Ticket</h5>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								  			<span aria-hidden="true">&times;</span>
										</button>
							  		</div>
								  	<div class="modal-body html-model-body">
								  	<form action="'.$set->SSLprefix.$set->basepage.'" method="post" '.($set->isNetwork ? 'onSubmit="if($(\'#merchantIDSB\').val()==-1){ alert(\'Please select a merchant\'); return false; } return true;"' : '').'>

										<div class="html-code-body">
										'.($set->isNetwork ? '
											<div>
												<label '.err('subject').'>'.lang('Merchant:').':</label>
												<select id="merchantIDSB" name="db[merchantID]" style="width: 610px;" >'.$merchantOptions.'</select>
											</div>' : '').'
										  	<div class="ticket-modal">
												<div class="TicketSubject">
													<label '.err('subject').'>Ticket Subject</label>
													<input type="text"></input>
												</div> ';
												if ($_SESSION['isam']==0) {
														$set->content .= '
														<div class="Youremail">
															<label class="label-email">Your email</label>
															<input type="text" name="db[reply_email]" value="'.$set->userInfo['mail'].'" /> <span class="modal-span">'.lang('Please verify your current e-mail address in order to get notifications').'</span>'.
														'</div>'
														; 
												}
											$set->content .= '
										  	</div>
										  	<div class="text-area-div">
												<label '.err('text').'>Ticket Subject</label>
											  	<textarea></textarea>
										  	</div>
										</div>
							  		</div>
							  	<div class="modal-footer html-model-footer">
									<div class="html-code-footer-button support-modal-button">
								  		<button type="submit" name="new_ticket" class="new_ticket">Send Ticket</button>
									</div>
							  	</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
				<div class="support-table">
				<div class="row">
				<div class="col-md-12">
					<div class="top-performing-creative h-full">
						<h2 class="specialTableTitle"></h2>
							<div class="performing-creative-table">
								<div class="table-responsive">
									<table class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
										<thead>
											<tr>
											<th scope="col">#</th>
											<th scope="col">Ticket ID</th>
											<th scope="col">Date</th>
											<th scope="col">Time</th>
											<th scope="col">Ticket Subject</th>
											'.($set->isNetwork ? '<th style="text-align:">'.lang('Merchant').'</th>' : '').'
											<th scope="col">Last Response</th>
											<th scope="col">Current status</th>
											<th scope="col">Action</th>
											<th scope="col"></th>
											</tr>
										</thead>
										<tbody>
										'.($allTickets ? $allTickets : '<tr><td colspan="7" style="text-align: center;"><b>'.lang('No Tickets').'</b></td></tr>').'
											
									
										</tbody>
									</table>
								</div>
							</div>
					</div>
				</div>
			</div>
				</div>
			</div>
';
		theme();
		break;
	}

?>