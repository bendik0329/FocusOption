<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

if (!isAdmin()) _goto('/admin/');

$appTable = 'affiliates';
$appNotes = 'affiliates_notes';
$appDeals = 'affiliates_deals';
$appProfiles = 'affiliates_profiles';

function listFields($field='') {
	$arr = Array(
		"id" => lang("Affiliate ID"),
		"username" => lang("Username"),
		"mail" => lang("E-Mail"),
		"website" => lang("Website"),
		"first_name" => lang("First Name"),
		"last_name" => lang("Last Name"));
	foreach ($arr AS $k => $v) $html .= '<option value="'.$k.'" '.($k == $field ? 'selected' : '').'>'.$v.'</option>';
	return $html;
	}

	
switch ($act) {
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;

	/* ------------------------------------ [ Manage Languages ] ------------------------------------ */

	case "send_password":
		$getMail = dbGet($id,$appTable);
		$abc= array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"); 
		$abcBig= array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"); 
		$new_password = rand(0,9).$abc[rand(0,25)].$abc[rand(0,25)].$abcBig[rand(0,25)].rand(0,4).rand(0,9).$abcBig[rand(0,25)];
		updateUnit($appTable,"password='".md5($new_password)."'","id='".$getMail['id']."'");
		$set->sendTo = $getMail['mail'];
		$set->subject = $getMail['first_name'].' - Password Reset';
		$set->body .= 'Dear '.$getMail['first_name'].',<br />
			This email has been sent automatically by '.$set->webTitle.' in response to your request to recover your password.<br />
			<br />
			<u>Your new password is:</u> <b>'.$new_password.'</b><br />
			<br />
			It is recommended to keep this password in a safe place. To access your account now <a href="'.$set->webAddress.'">Click Here</a>.<br />
			<br />
			If you have problems accessing your account please email us here: <a href="mailto:'.$set->webMail.'">'.$set->webMail.'</a><br />
			<br />
			Best Regards, <br />
			'.$set->webTitle;
			sendMail();
		_goto($set->basepage.'?act=new&id='.$id.'&ty=1');
		break;
	
	case "send_mail":
		sendTemplate($mailCode,$affiliate_id);
		_goto($set->basepage.'?act=new&id='.$affiliate_id.'&sent=1');
		break;

	case "add":
		$chkUser = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appTable." WHERE lower(username)='".strtolower($db['username'])."' AND id != '".$db['id']."'"));
		if ($chkUser['id']) $errors['username'] = lang('Username already exist');
		if (!$db['username']) $errors['username'] = lang('Username already exist');
		if (!$db['mail']) $errors['mail'] = lang('E-mails not match');
		if (!$db['first_name']) $errors['first_name'] = lang('Please fill out your first name');
		if (!$db['last_name']) $errors['last_name'] = lang('Please fill out your last name');
		if (!$db['website']) $errors['website'] = lang('Please fill out your website');
		if ($errors) {
			} else {
			$db['ip'] = $set->userIP;
			if ($password) $db['password'] = md5($password);
			if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
			if ($showDeposit) $db['showDeposit'] = 1; else $db['showDeposit'] = 0;
			$lastID=dbAdd($db,$appTable);
			if ($lastGroup_id != $db['group_id']) chgGroup($lastID,$db['group_id']);
			_goto($set->basepage.'?act=new&id='.$lastID.'&ty=1');
			}
			
	case "new":
		if ($id) {
			$db = dbGet($id,$appTable);
			$merchantList = explode("|",$db['merchants']);
			$set->pageTitle = lang('EDIT AFFILIATE ACCOUNT').' #'.$db['id'].' ('.$db['first_name'].' '.$db['last_name'].')';
			if ($note_id) $edit_note=dbGet($note_id,$appNotes);
			// Tickets List
			$noteqq=mysql_query("SELECT * FROM ".$appNotes." WHERE affiliate_id='".$id."' AND valid='1' ORDER BY id DESC");
			while ($noteww=mysql_fetch_assoc($noteqq)) {
				$l++;
				$adminInfo=mysql_fetch_assoc(mysql_query("SELECT first_name,last_name FROM admins WHERE id='".$noteww['admin_id']."' LIMIT 1"));
				$ticketsList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$noteww['id'].'</td>
								<td><a href="'.$set->basepage.'?act=new&id='.$id.'&note_id='.$noteww['id'].'#notesPlace">'.lang('Edit').'</a>'.($set->userInfo['level'] == "admin" ? ' | <a href="'.$set->basepage.'?act=remove_note&affiliate_id='.$id.'&note_id='.$noteww['id'].'">'.lang('Delete').'</a>' : '').'</td>
								<td>'.dbDate($noteww['rdate']).'</td>
								<td>'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</td>
								<td>'.dbDate($noteww['issue_date']).'</td>
								<td>'.strtoupper($noteww['status']).'</td>
								<td align="center">'.round(floor((strtotime($noteww['closed_date'] == "0000-00-00 00:00:00" ? dbDate() : $noteww['closed_date'])-strtotime($noteww['rdate']))/(60*60*24))+1).' '.lang('Day(s)').'</td>
								<td align="left">'.nl2br($noteww['notes']).'</td>
							</tr>';
				}
				
			// List Profiles
			$profileqq=mysql_query("SELECT * FROM ".$appProfiles." WHERE affiliate_id='".$id."' ORDER BY id DESC");
			while ($profileww=mysql_fetch_assoc($profileqq)) {
				$l++;
				$listProfiles .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$profileww['id'].'</td>
								<td align="left">'.$profileww['name'].'</td>
								<td>'.$profileww['url'].'</td>
								<td>'.$profileww['description'].'</td>
								<td>'.$profileww['source_traffic'].'</td>
								<td id="profile_'.$profileww['id'].'"><a onclick="ajax(\''.$set->basepage.'?act=profile_valid&id='.$profileww['id'].'\',\'profile_'.$profileww['id'].'\');" style="cursor: pointer;">'.xvPic($profileww['valid']).'</a></td>
							</tr>';
				}
			} else {
			$set->pageTitle = lang('NEW AFFILIATE ACCOUNT');
			}
		$tempqq=mysql_query("SELECT id,mailCode FROM mail_templates WHERE valid='1' ORDER BY id ASC");
		while ($tempww=mysql_fetch_assoc($tempqq)) $allTemplates .= '<option value="'.$tempww['mailCode'].'" '.($tempww['mailCode'] == $mailCode ? 'selected' : '').'>'.$tempww['mailCode'].'</option>';
		
		if ($sent) {
			$set->content .= '
			<script type="text/javascript">
				window.onload = function() {
					alert(\''.lang('The E-mail has sent to affiliate').'\');
					}
			</script>
			';
			}
		if ($db['id']) {
			$affiliateqq=mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC");
			while ($affiliateww=mysql_fetch_assoc($affiliateqq)) $allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($db['refer_id'] == $affiliateww['id'] ? 'selected' : '').'>['.$affiliateww['id'].'] '.$affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
			}
		
		$set->content .= '<div style="border: 1px #DDDDDD solid; margin: 10px 0 10px 0;">'.chart('0','affiliate',$db['id'],1).'</div>
						<form action="/admin/affiliates.php?act=add" method="post">
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						<input type="hidden" name="lastGroup_id" value="'.$db['group_id'].'" />
						'.($ty ? '<div class="Must">- '.lang('The page is up to date').' ('.dbDate().')</div><br />' : '').'
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Affiliate Details').'</div>
						<div id="tab_1" style="width: 100%; padding: 10px; background: #F8F8F8;">
						<table width="98%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="48%" align="left" valign="top">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
										'.($errors ? '<td colspan="2" align="left" style="color: red;"><b>'.lang('Please check one or more of the following fields').':</b><br /><ul type="*"><li />'.implode('<li />',$errors).'</ul></td>' : '').'<tr>
											<td align="left" width="200" class="blueText" '.err('username').'>'.lang('Username').':</td>
											<td align="left"><input type="text" name="db[username]" value="'.$db['username'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('password').'>'.lang('Password').':</td>
											<td align="left"><input type="password" name="password" value="" style="width: 280px;" /></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="110" class="blueText" '.err('first_name').'>'.lang('First Name').':</td>
											<td align="left"><input type="text" name="db[first_name]" value="'.$db['first_name'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('last_name').'>'.lang('Last Name').':</td>
											<td align="left"><input type="text" name="db[last_name]" value="'.$db['last_name'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('mail').'>'.lang('E-Mail').':</td>
											<td align="left"><input type="text" name="db[mail]" value="'.$db['mail'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('phone').'>'.lang('Phone').':</td>
											<td align="left"><input type="text" name="db[phone]" value="'.$db['phone'].'" style="width: 280px;" /></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('country').'>'.lang('Country').':</td>
											<td align="left"><select name="db[country]" style="width: 292px;"><option value="">'.lang('Choose Your Country').'</option>'.getCountry($db['country']).'</select></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('website').'>'.lang('Website URL').':</td>
											<td align="left"><input type="text" name="db[website]" value="'.$db['website'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText">'.lang('I.M. Type').':</td>
											<td align="left">
												<select name="db[IMUserType]" style="width: 292px;">
													<option value="">'.lang('Choose I.M. Type').'</option>
													<option value="Skype" '.($db['IMUserType'] == "Skype" ? 'selected="selected"' : '').'>'.lang('Skype').'</option>
													<option value="MSN" '.($db['IMUserType'] == "MSN" ? 'selected="selected"' : '').'>'.lang('MSN').'</option>
													<option value="Google Talk" '.($db['IMUserType'] == "Google Talk" ? 'selected="selected"' : '').'>'.lang('Google Talk').'</option>
													<option value="ICQ" '.($db['IMUserType'] == "ICQ" ? 'selected="selected"' : '').'>'.lang('ICQ').'</option>
													<option value="Yahoo" '.($db['IMUserType'] == "Yahoo" ? 'selected="selected"' : '').'>'.lang('Yahoo').'</option>
													<option value="AIM" '.($db['IMUserType'] == "AIM" ? 'selected="selected"' : '').'>'.lang('AIM').'</option>
												</select>
											</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText">'.lang('I.M').':</td>
											<td align="left"><input type="text" name="db[IMUser]" value="'.$db['IMUser'].'" style="width: 280px;" /></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr>'.($set->multi ? '<tr>
											<td align="left" class="blueText">'.lang('Language').':</td>
											<td align="left"><select name="db[lang]">'.listMulti($db['lang']).'</select></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr>' : '').'<tr>
											<td align="left" class="blueText">'.lang('Credit Amount').':</td>
											<td align="left">$ <input type="text" name="db[credit]" value="'.$db['credit'].'" style="width: 100px; text-align: center;" /></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText">'.lang('Sub Affiliate Commission').':</td>
											<td align="left">% <input type="text" name="db[sub_com]" value="'.$db['sub_com'].'" style="width: 100px; text-align: center;" /></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td></td>
											<td align="left" class="blueText"><input type="checkbox" name="valid" '.($db['valid'] || $valid ? 'checked' : '').' /> '.lang('Active Account').'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td></td>
											<td align="left" class="blueText"><input type="checkbox" name="showDeposit" '.($db['showDeposit'] ? 'checked' : '').' /> '.lang('Show Deposits & Withdrawals').'</td>
										</tr><tr>
											<td colspan="2" height="10"></td>
										</tr>
									</table>
								</td>
								<td width="4%"></td>
								<td width="48%" align="left" valign="top">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
										<tr>
											<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a href="/?act=login&username='.$db['username'].'&password='.$db['password'].'&admin='.$set->userInfo['id'].'" target="_blank">'.lang('Login as affiliate').'</a></div></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a href="javascript:void(0);" onclick="confirmation(\''.lang('Are you sure you want to send this mail?').'\',\''.$set->basepage.'?act=send_password&id='.$db['id'].'\');">'.lang('Reset Password').'</a></div></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a href="mailto:'.$db['mail'].'">'.lang('Send an e-mail').'</a></div></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="110" height="20" class="blueText">'.lang('Sign up Date').':</td>
											<td align="left" class="greenText">'.dbDate($db['rdate']).'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="110" height="20" class="blueText">'.lang('Last Login').':</td>
											<td align="left" class="greenText">'.dbDate($db['lastvisit']).'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="110" class="blueText">'.lang('Group').':</td>
											<td align="left"><select name="db[group_id]" style="width: 292px;"><option value="0">General</option>'.listGroups($db['group_id']).'</select></td>
										</tr>'.($db['id'] ? '<tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="110" class="blueText">'.lang('Auto E-Mail').':</td>
											<td align="left">
												<select style="width: 292px;" onchange="confirmation(\''.lang('Are you sure you want to send this mail?').'\',\''.$set->basepage.'?act=send_mail&affiliate_id='.$db['id'].'&mailCode=\'+this.value);">
													<option value="">'.lang('Select E-Mail Template').'</option>
													'.$allTemplates.'
												</select>
											</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="110" class="blueText">'.lang('Sub Affiliate Of').':</td>
											<td align="left">
												<select name="db[refer_id]" style="width: 292px;">
													<option value="">'.lang('Main Affiliate Account').'</option>
													'.$allAffiliates.'
												</select>
											</td>
										</tr>' : '').'
									</table>
									<hr />';
							$qq=mysql_query("SELECT * FROM merchants");
							while ($ww=mysql_fetch_assoc($qq)) {
								$merchantName = strtolower($ww['name']);
								$totalTraders+=mysql_num_rows(mysql_query("SELECT id FROM data_reg_".$merchantName." WHERE affiliate_id='".$id."'"));
								$ftd+=mysql_num_rows(mysql_query("SELECT id FROM data_sales_".$merchantName." WHERE affiliate_id='".$id."' AND type='deposit' GROUP BY trader_id"));
								$salesqq=mysql_query("SELECT type,amount FROM data_sales_".$merchantName." WHERE affiliate_id='".$id."'");
								while ($salesww=mysql_fetch_assoc($salesqq)) {
									if ($salesww['type'] == "deposit") $totalAmount+=$salesww['amount'];
									if ($salesww['type'] == "withdrawal") $totalWithdrawal+=$salesww['amount'];
									}
								}
							$total=mysql_fetch_assoc(mysql_query("SELECT SUM(total) AS totalPaid FROM payments_paid WHERE affiliate_id='".$id."'"));
							$accounts=mysql_num_rows(mysql_query("SELECT id FROM payments_details WHERE affiliate_id='".$id."'"));
							$totalFruad=mysql_num_rows(mysql_query("SELECT id FROM payments_details WHERE status='canceled' AND affiliate_id='".$id."'"));
							$totalPending=mysql_num_rows(mysql_query("SELECT id FROM payments_details WHERE status='pending' AND affiliate_id='".$id."'"));
							$totalTraffic = mysql_fetch_assoc(mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE affiliate_id='".$id."'"));
							$set->content .= '<table width="100%" class="tablesorter" border="0" cellpadding="4" cellspacing="1" style="background: #DDDDDD;"><tbody>
										<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Impressions').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraffic['totalViews'],0).'</td></tr>
										<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Clicks').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraffic['totalClicks'],0).'</td></tr>
										<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Click Through Ratio (CTR)').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).'%</td></tr>
										<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Click to Account').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format(($totalTraders/$totalTraffic['totalClicks'])*100,0).'%</td></tr>
										<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Click to Sale').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,0).'%</td></tr>
										<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Total Paid').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">$ '.@number_format($total['totalPaid'],2).'</td></tr>
										<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Total Traders').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraders,0).'</td></tr>
										<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Total FTDs').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($ftd,0).'</td></tr>
										<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Total Deposit').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">$ '.@number_format($totalAmount,2).'</td></tr>
										<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Total Withdrawal').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">$ '.@number_format($totalWithdrawal,2).'</td></tr>
										<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Total Chargeback / Refund / Fraud').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalFruad,0).'</td></tr>
										<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Pending/Un Paid Traders').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($totalPending+$totalFruad,0).'</td></tr>
										<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Affiliate Risk').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($totalFruad/$ftd)*100,2).'%</td></tr>
										<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Trader TLV').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">$ '.@number_format($totalAmount/$ftd,2).'</td></tr>
									</tbody></table>
								</td>
							</tr><tr>
								<td colspan="3" height="10"></td>
							</tr><tr>
								<td colspan="3" align="right"><input type="submit" value="'.lang('Save').'" /></td>
							</tr></table>
						</form>
						</div>';
			$l=0;
			$merchantqq=mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY pos");
			while ($merchantww=mysql_fetch_assoc($merchantqq)) {
				$l++;
				unset($min_cpaAmount); unset($cpaAmount); unset($dcpaAmount); unset($revenueAmount); unset($cplAmount); unset($cpcAmount); unset($cpmAmount);
				$takeqq=mysql_query("SELECT id,dealType,amount FROM ".$appDeals." WHERE merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."'");
				while ($takeww=mysql_fetch_assoc($takeqq)) {
					if ($takeww['dealType'] == "min_cpa") $min_cpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpa") $cpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "dcpa") $dcpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "revenue") $revenueAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpl") $cplAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpc") $cpcAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpm") $cpmAmount = $takeww['amount'];
					}
				// if (!$min_cpaAmount) $min_cpaAmount = $merchantww['min_cpa_amount'];
				// if (!$cpaAmount) $cpaAmount = $merchantww['cpa_amount'];
				// if (!$revenueAmount) $revenueAmount = $merchantww['revenue_amount'];
				// if (!$cplAmount) $cplAmount = $merchantww['cpl_amount'];
				// if (!$cpcAmount) $cpcAmount = $merchantww['cpc_amount'];
				$listDealType .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td align="center"><input type="checkbox" name="activeMerchants[]" value="'.$merchantww['id'].'" '.(@in_array($merchantww['id'],$merchantList) ? 'checked' : '').' /></td>
								<td align="left"><input type="hidden" name="deal_merchant[]" value="'.$merchantww['id'].'" /><b>'.$merchantww['name'].'</b></td>
								<td>$ <input type="text" name="deal_min_cpa[]" value="'.$min_cpaAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>$ <input type="text" name="deal_cpa[]" value="'.$cpaAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>% <input type="text" name="deal_dcpa[]" value="'.$dcpaAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>% <input type="text" name="deal_revenue[]" value="'.$revenueAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								'.($set->deal_cpl ? '<td>$ <input type="text" name="deal_cpl[]" value="'.$cplAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
								'.($set->deal_cpc ? '<td>$ <input type="text" name="deal_cpc[]" value="'.$cpcAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
								'.($set->deal_cpm ? '<td>$ <input type="text" name="deal_cpm[]" value="'.$cpmAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
							</tr>';
				}
				
				
				$takeqq=mysql_query("SELECT id,tier_amount,tier_pcpa,amount,merchant_id FROM ".$appDeals." WHERE affiliate_id='".$id."' AND dealType='tier' ORDER BY tier_amount ASC");
				while ($takeww=mysql_fetch_assoc($takeqq)) {
					$merchantww=mysql_fetch_assoc(mysql_query("SELECT name FROM merchants WHERE id='".$takeww['merchant_id']."'"));
					$listTier .= '<tr '.($ll % 2 ? 'class="trLine"' : '').'>
								<td align="left"><input type="hidden" name="deal_ids[]" value="'.$takeww['id'].'" /><b>'.$merchantww['name'].'</b></td>
								<td>$ <input type="text" name="deal_tier_amount[]" value="'.$takeww['tier_amount'].'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>$ <input type="text" name="deal_cpa[]" value="'.$takeww['amount'].'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>% <input type="text" name="deal_pcpa[]" value="'.$takeww['tier_pcpa'].'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td></td>
							</tr>';
					$ll++;
					}
				
			if ($db['id']) $set->content .= '
						<br />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_5\').slideToggle(\'fast\');">'.lang('Profiles').'</div>
						<div id="tab_5" style="width: 100%; background: #F8F8F8;">
						<form action="'.$set->basepage.'" method="post">
						<input type="hidden" name="act" value="save_profile" />
						<input type="hidden" name="db[affiliate_id]" value="'.$id.'" />
							<div align="left" style="padding: 10px;">
								<table><tr>
								<td width="100" class="blueText" style="text-align: left;">'.lang('Profile Name').':</td>
								<td align="left"><input type="text" name="db[name]" value="" style="width: 280px;" /></td>
								<td width="80"></td>
								<td align="left" class="blueText">'.lang('URL').':</td>
								<td align="left"><input type="text" name="db[url]" value="http://" style="width: 280px;" /></td>
								<td width="80"></td>
								<td></td>
							</tr><tr>
								<td align="left" class="blueText">'.lang('Description').':</td>
								<td align="left"><input type="text" name="db[description]" value="" style="width: 280px;" /></td>
								<td></td>
								<td align="left" class="blueText">'.lang('Traffic Source').':</td>
								<td align="left"><input type="text" name="db[source_traffic]" value="" style="width: 280px;" /></td>
								<td></td>
								<td align="right"><input type="submit" value="'.lang('Save').'" /></td>
							</tr></table>
							</form>
							</div>
							
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td>'.lang('ID').'</td>
									<td align="left">'.lang('Profile Name').'</td>
									<td>'.lang('URL').'</td>
									<td>'.lang('Description').'</td>
									<td>'.lang('Traffic Source').'</td>
									<td>'.lang('Available').'</td>
								</tr></thead><tfoot><tr style="background: #D9D9D9;">
									<td>-</td>
									<td align="left"><b>'.lang('Default').'</b></td>
									<td>'.$db['website'].'</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>'.$listProfiles.'</tfoot>
							</table>
						</div>
						<br />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Deal Type').'</div>
						<div id="tab_3" style="width: 100%; background: #F8F8F8;">
							<form action="'.$set->basepage.'" method="post">
							<input type="hidden" name="act" value="save_deal" />
							<input type="hidden" name="affiliate_id" value="'.$id.'" />
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td align="center">'.lang('Active').'</td>
									<td style="text-align: left;">'.lang('Merchant').'</td>
									<td>'.lang('Min. Deposit').'</td>
									<td>'.lang('CPA').'</td>
									<td>'.lang('DCPA').'</td>
									<td>'.lang('Revenue').'</td>
									'.($set->deal_cpl ? '<td>'.lang('CPL').'</td>' : '').'
									'.($set->deal_cpc ? '<td>'.lang('CPC').'</td>' : '').'
									'.($set->deal_cpm ? '<td>'.lang('CPM').'</td>' : '').'
								</tr></thead><tfoot><tr style="background: #D9D9D9;">
								<td></td>
								<td align="left"><b>'.lang('Global To All Merchants').'</b></td>
								<td>$ <input type="text" name="min_cpa_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>$ <input type="text" name="cpa_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>% <input type="text" name="dcpa_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>% <input type="text" name="revenue_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								'.($set->deal_cpl ? '<td>$ <input type="text" name="cpl_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
								'.($set->deal_cpc ? '<td>$ <input type="text" name="cpc_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
								'.($set->deal_cpm ? '<td>$ <input type="text" name="cpm_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
							</tr>'.$listDealType.'</tfoot>
							</table>
							<div align="right" style="padding-top: 20px;"><input type="submit" value="'.lang('Save').'" /></div>
							</form>
							<br />
							'.($set->deal_tier ? '<div class="normalTableTitle">'.lang('Tier Deal').'</div>
							<div style="font-size: 10px; padding: 5px;">* '.lang('Tier deal will erase all previous deals for this affiliate').'</div>
							
							<form action="'.$set->basepage.'" method="post">
							<input type="hidden" name="act" value="save_deal_tier" />
							<input type="hidden" name="affiliate_id" value="'.$id.'" />
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td style="text-align: left;">'.lang('Merchant').'</td>
									<td>'.lang('Deposit Range').' (ex. 100-200)</td>
									<td>'.lang('CPA').'</td>
									<td>'.lang('PCPA').'</td>
									<td width="55%"></td>
								</tr></thead><tfoot>'.$listTier.'<tr '.($ll % 2 ? 'class="trLine"' : '').'>
								<td style="text-align: left;"><select name="deal_merchant">'.listMerchants($merchantww['id']).'</select></td>
								<td>$ <input type="text" name="tier_amount" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>$ <input type="text" name="cpa" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td>% <input type="text" name="pcpa" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
								<td></td>
							</tr></tfoot>
							</table>
							<div align="right" style="padding-top: 20px;"><input type="submit" value="'.lang('Save').'" /></div>
							</form>
							
						</div>
						<br />' : '').'
						<form action="'.$set->basepage.'" method="post">
						<input type="hidden" name="act" value="add_note" />
						<input type="hidden" name="affiliate_id" value="'.$id.'" />
						<input type="hidden" name="note_id" value="'.$note_id.'" />
						<div class="normalTableTitle" id="notesPlace" style="cursor: pointer;" onclick="$(\'#tab_2\').slideToggle(\'fast\');">'.lang('Manager Notes CRM').'</div>
						<div id="tab_2" style="width: 100%; display: '.($note_id ? 'block' : 'none').'">
						<table border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td colspan="2" align="left">'.lang('Account Manager Note').':</td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr><tr>
								<td colspan="2" align="left"><textarea name="text" cols="1" rows="1" id="notes" class="aff_textArea" style="width: 700px; height: 100px;">'.$edit_note['notes'].'</textarea></td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr>';
						if ($edit_note['issue_date'] AND $edit_note['issue_date'] != "0000-00-00 00:00:00") {
							$exp=explode(" ",$edit_note['issue_date']);
							$time=explode(":",$exp[1]);
							} else {
							$time[0] = date("H");
							$time[1] = date("i");
							}
						for ($i=0; $i<=23; $i++) $listHour .= '<option value="'.($i < 10 ? '0'.$i : $i).'" '.($time[0] == $i ? 'selected="selected"' : '').'>'.($i < 10 ? '0'.$i : $i).'</option>';
						for ($i=0; $i<=59; $i++) $listMin .= '<option value="'.($i < 10 ? '0'.$i : $i).'" '.($time[1] == $i ? 'selected="selected"' : '').'>'.($i < 10 ? '0'.$i : $i).'</option>';

						$set->content .= '<tr>
								<td align="left">
									<input type="text" name="issue_date" id="issue_date" value="'.($edit_note['issue_date'] == "0000-00-00 00:00:00" || !$edit_note['id'] ? date("d/m/Y") : date("d/m/Y", strtotime($edit_note['issue_date']))).'" style="width: 100px; padding: 3px;" />
									<select name="hour" style="width: 50px;">'.$listHour.'</select> : <select name="min" style="width: 50px;">'.$listMin.'</select>
								</td>
								<td align="right"><select name="status">
									<option value="open" '.($edit_note['status'] == "open" ? 'selected="selected"' : '').'>'.lang('Open').'</option>
									<option value="inprocess" '.($edit_note['status'] == "inprocess" ? 'selected="selected"' : '').'>'.lang('In Process').'</option>
									<option value="closed" '.($edit_note['status'] == "closed" ? 'selected="selected"' : '').'>'.lang('Closed').'</option>
								</select></td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr><tr>
								<td colspan="2" align="right"><input type="submit" value="'.lang('Save').'" /></td>
							</tr>
						</table>
						<script type="text/javascript">
							$(function() {
								$("#issue_date").datepicker({
									dateFormat: \'dd/mm/yy\'
									});
								});
						</script>
						</script>
					</form>
					<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
						<thead><tr>
							<td width="50">'.lang('ID').'</td>
							<td>'.lang('Actions').'</td>
							<td>'.lang('Last Edited').'</td>
							<td>'.lang('Added By').'</td>
							<td>'.lang('Due Date').'</td>
							<td align="center">'.lang('Processing Time').'</td>
							<td>'.lang('Status').'</td>
							<td style="text-align: left;">'.lang('Notes').'</td>
						</tr>
						</thead><tfoot>'.$ticketsList.'</tfoot>
					</table>
					</div>
					<br />
					';
		
		$qq=mysql_query("SELECT * FROM mail_sent WHERE affiliate_id='".$db['id']."' ORDER BY id DESC");
		while ($ww=mysql_fetch_assoc($qq)) {
			$adminInfo=mysql_fetch_assoc(mysql_query("SELECT first_name,last_name FROM admins WHERE id='".$ww['admin_id']."'"));
			$listEmails .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td align="center">'.$ww['id'].'</td>
						<td align="center">'.date("d/m/y H:i", strtotime($ww['rdate'])).'</td>
						<td align="center">'.$ww['mailCode'].'</td>
						<td align="center">'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</td>
						<td align="center">'.$ww['opened'].'</td>
						<td align="center">'.($ww['opened_time'] != "0000-00-00 00:00:00" ? xvPic($ww['opened'],1).' '.date("d/m/y H:i", strtotime($ww['opened_time'])) : '-').'</td>
					</tr>';
			$i++;
			}
		
		$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_7\').slideToggle(\'fast\');">'.lang('E-mails Monitor').'</div>
					<div id="tab_7" style="width: 100%; background: #F8F8F8; display: none;">
						<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
							<thead><tr>
								<td align="center">#</td>
								<td align="center">'.lang('Sent At').'</td>
								<td align="center">'.lang('E-Mail Code').'</td>
								<td align="center">'.lang('Manager').'</td>
								<td align="center">'.lang('Viewed').'</td>
								<td align="center">'.lang('Readed').'</td>
							</tr></thead><tfoot>'.$listEmails.'</tfoot>
						</table>
					</div>
					<br />
					';
		
		$qq=mysql_query("SELECT * FROM affiliates WHERE valid='1' AND refer_id='".$db['id']."' ORDER BY id DESC");
		while ($ww=mysql_fetch_assoc($qq)) {
			$listSub .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td align="center"><a href="'.$set->basepage.'?act=new&id='.$ww['id'].'">'.$ww['id'].'</a></td>
						<td align="center"><a href="'.$set->basepage.'?act=new&id='.$ww['id'].'">'.$ww['username'].'</a></td>
						<td align="center">'.$ww['first_name'].'</td>
						<td align="center">'.$ww['last_name'].'</td>
						<td align="center"><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
						<td align="center">'.date("d/m/y H:i", strtotime($ww['rdate'])).'</td>
						<td align="center">'.date("d/m/y H:i", strtotime($ww['lastvisit'])).'</td>
					</tr>';
			$i++;
			}
		
		$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_8\').slideToggle(\'fast\');">'.lang('Sub Affiliates').'</div>
					<div id="tab_8" style="width: 100%; background: #F8F8F8; display: none;">
						<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
							<thead><tr>
								<td align="center">'.lang('Affiliate ID').'</td>
								<td align="center">'.lang('Username').'</td>
								<td align="center">'.lang('First Name').'</td>
								<td align="center">'.lang('Last Name').'</td>
								<td align="center">'.lang('E-Mail').'</td>
								<td align="center">'.lang('Registered At').'</td>
								<td align="center">'.lang('Last Login').'</td>
							</tr></thead><tfoot>'.$listSub.'</tfoot>
						</table>
					</div>
					<br />
					';
		
		$qq=mysql_query("SELECT * FROM affiliates_traffic WHERE affiliate_id='".$db['id']."' ORDER BY rdate DESC LIMIT 100");
		while ($ww=mysql_fetch_assoc($qq)) {
			$merchantName=dbGet($ww['merchant_id'],"merchants");
			$listTraffic .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td align="left"><a href="'.$ww['refer_url'].'" target="_blank">'.$ww['refer_url'].'</a></td>
						<td align="center">'.$ww['ip'].'</td>
						<td align="center">'.date("d/m/y H:i", strtotime($ww['rdate'])).'</td>
						<td align="center">'.$merchantName['name'].'</td>
						<td align="center">'.$ww['visits'].'</td>
					</tr>';
			$i++;
			}
		
		$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_9\').slideToggle(\'fast\');">'.lang('Affiliate Traffic Referral').'</div>
					<div id="tab_9" style="width: 100%; background: #F8F8F8; display: none;">
						<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
							<thead><tr>
								<td align="left" style="text-align: left;">'.lang('Referral URL').'</td>
								<td align="center">'.lang('Last IP').'</td>
								<td align="center">'.lang('Last Click').'</td>
								<td align="center">'.lang('Merchant').'</td>
								<td align="center">'.lang('Visits').'</td>
							</tr></thead><tfoot>'.$listTraffic.'</tfoot>
						</table>
					</div>
					<br />
					';
		
		
		$pixelqq=mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$db['id']."' ORDER BY id ASC");
		while ($pixelww=mysql_fetch_assoc($pixelqq)) {
			$listPixels .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td align="center"><input type="hidden" name="ids[]" value="'.$pixelww['id'].'" /><select name="merchant_id[]">'.listMerchants($pixelww['merchant_id']).'</select></td>
						<td align="center"><textarea name="pixelCode[]" cols="40" rows="3">'.$pixelww['pixelCode'].'</textarea></td>
						<td align="center"><select name="type[]"><option value="account" '.($pixelww['type'] == "account" ? 'selected="selected"' : '').'>Account</option><option value="sale" '.($pixelww['type'] == "sale" ? 'selected="selected"' : '').'>Sale</option></select></td>
						<td align="center"><input type="text" name="total[]" value="'.$pixelww['totalFired'].'" style="width: 100px; text-align: center;" /></td>
					</tr>';
			$i++;
			}
		
		$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_10\').slideToggle(\'fast\');">'.lang('Pixel Monitor').'</div>
					<div id="tab_10" style="width: 100%; background: #F8F8F8; display: none;">
						<form action="'.$set->basepage.'" method="post">
						<input type="hidden" name="act" value="save_pixel" />
						<input type="hidden" name="db[affiliate_id]" value="'.$id.'" />
						<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
							<thead><tr>
								<td align="left" style="text-align: left;">'.lang('Merchant').'</td>
								<td align="center">'.lang('Pixel Code').'</td>
								<td align="center">'.lang('Type').'</td>
								<td align="center">'.lang('Total Fired').'</td>
							</tr></thead>'.$listPixels.'<tfoot><tr>
								<td align="center"><select name="db[merchant_id]">'.listMerchants().'</select></td>
								<td align="center"><textarea name="db[pixelCode]" cols="40" rows="3"></textarea></td>
								<td align="center"><select name="db[type]"><option value="account">'.lang('Account').'</option><option value="sale">'.lang('Sale').'</option></select></td>
								<td align="center"><input type="submit" value="'.lang('Add / Update').'" /></td>
							</tr></tfoot>
						</table>
					</form>
					<table><tr>
					<td width="300" valign="top">
						<b><u>'.lang('Parameters Replacing').':</u></b><br />
						<b>{ctag}</b> - '.lang('Campaign Parameter').'<br />
						<b>{trader_id}</b> - '.lang('The Trader ID').'<br />
						<b>{trader_alias}</b> - '.lang('Username').'<br />
						<b>{type}</b> - '.lang('Type of the account').'<br />
					</td><td valign="top">
						<b><u>'.lang('Get Sales Pixel').':</u></b><br />
						<b>{ctag}</b> - '.lang('Campaign Parameter').'<br />
						<b>{trader_id}</b> - '.lang('The Trader ID').'<br />
						<b>{tranz}</b> - '.lang('Transaction ID').'<br />
						<b>{type}</b> - '.lang('Transaction Type').'<br />
						<b>{currency}</b> - '.lang('Account Currency').'<br />
						<b>{amount}</b> - '.lang('Amount of the transaction').'
					</td></tr></table>
					</div>
					';
		
		theme();
		break;
	
	case "save_pixel":
		for ($i=0; $i <= count($ids); $i++) {
			if ($pixelCode[$i]) mysql_query("UPDATE pixel_monitor SET merchant_id='".$merchant_id[$i]."',pixelCode='".mysql_escape_string($pixelCode[$i])."',type='".$type[$i]."',total='".$total[$i]."' WHERE id='".$ids[$i]."'");
				else if ($ids[$i]) mysql_query("DELETE FROM pixel_monitor WHERE id='".$ids[$i]."'");
			}
		if ($db['pixelCode']) dbAdd($db,"pixel_monitor");
		_goto($set->basepage.'?act=new&id='.$db['affiliate_id']);
		break;
	
	case "save_deal":
		if ($activeMerchants) updateUnit("affiliates","merchants='".implode("|",$activeMerchants)."'","id='".$affiliate_id."'");
		// echo "<pre>";
		// print_r($_POST);
		// die();
		
		mysql_query("DELETE FROM ".$appDeals." WHERE amount < '1' OR dealType='tier' AND affiliate_id='".$affiliate_id."'");
		for ($i=0; $i<=count($deal_merchant)-1; $i++) {
			unset($min_cpa_db);
			unset($cpa_db);
			unset($dcpa_db);
			unset($revenue_db);
			unset($cpl_db);
			unset($cpc_db);
			unset($cpm_db);
			$chkDealMinCPA = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='min_cpa'"));
			$chkDealCPA = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpa'"));
			$chkDealDCPA = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='dcpa'"));
			$chkDealRevenue = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='revenue'"));
			$chkDealCPL = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpl'"));
			$chkDealCPC = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpc'"));
			$chkDealCPM = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpm'"));
			
			// Min CPA
			if ($chkDealMinCPA['id']) $min_cpa_db['id'] = $chkDealMinCPA['id'];
			$min_cpa_db['rdate'] = dbDate();
			$min_cpa_db['admin_id'] = $set->userInfo['id'];
			$min_cpa_db['affiliate_id'] = $affiliate_id;
			$min_cpa_db['merchant_id'] = $deal_merchant[$i];
			$min_cpa_db['dealType'] = 'min_cpa';
			if ($min_cpa_global == "0" OR $deal_min_cpa[$i] == 0) {
				if ($min_cpa_db['id']) dbDelete($min_cpa_db['id'],$appDeals);
				} else if ($deal_min_cpa[$i] > 0 OR $min_cpa_global > 0) {
				$min_cpa_db['amount'] = ($min_cpa_global ? $min_cpa_global : $deal_min_cpa[$i]);
				$lastID=dbAdd($min_cpa_db,$appDeals);
				}

			// CPA
			if ($chkDealCPA['id']) $cpa_db['id'] = $chkDealCPA['id'];
			$cpa_db['rdate'] = dbDate();
			$cpa_db['admin_id'] = $set->userInfo['id'];
			$cpa_db['affiliate_id'] = $affiliate_id;
			$cpa_db['merchant_id'] = $deal_merchant[$i];
			$cpa_db['dealType'] = 'cpa';

			if ($cpa_global == "0" OR $deal_cpa[$i] == "0") {
				if ($chkDealCPA['id']) dbDelete($cpa_db['id'],$appDeals);
				} else if ($cpa_global > 0 OR $deal_cpa[$i] > 0) {
				$cpa_db['amount'] = ($cpa_global ? $cpa_global : $deal_cpa[$i]);
				dbAdd($cpa_db,$appDeals);
				}
				
			// DCPA
			if ($chkDealDCPA['id']) $dcpa_db['id'] = $chkDealDCPA['id'];
			$dcpa_db['rdate'] = dbDate();
			$dcpa_db['admin_id'] = $set->userInfo['id'];
			$dcpa_db['affiliate_id'] = $affiliate_id;
			$dcpa_db['merchant_id'] = $deal_merchant[$i];
			$dcpa_db['dealType'] = 'dcpa';

			if ($dcpa_global == "0" OR $deal_dcpa[$i] == "0") {
				if ($chkDealDCPA['id']) dbDelete($dcpa_db['id'],$appDeals);
				} else if ($dcpa_global > 0 OR $deal_dcpa[$i] > 0) {
				$dcpa_db['amount'] = ($dcpa_global ? $dcpa_global : $deal_dcpa[$i]);
				dbAdd($dcpa_db,$appDeals);
				}
				
			// Revenue
			if ($chkDealRevenue['id']) $revenue_db['id'] = $chkDealRevenue['id'];
			$revenue_db['rdate'] = dbDate();
			$revenue_db['admin_id'] = $set->userInfo['id'];
			$revenue_db['affiliate_id'] = $affiliate_id;
			$revenue_db['merchant_id'] = $deal_merchant[$i];
			$revenue_db['dealType'] = 'revenue';
			if ($revenue_global == "0" OR $deal_revenue[$i] == "0") {
				if ($revenue_db['id']) dbDelete($revenue_db['id'],$appDeals);
				} else if ($revenue_global > "0" OR $deal_revenue[$i] > "0") {
				$revenue_db['amount'] = ($revenue_global ? $revenue_global : $deal_revenue[$i]);
				dbAdd($revenue_db,$appDeals);
				}
				
			// CPL
			if ($chkDealCPL['id']) $cpl_db['id'] = $chkDealCPL['id'];
			$cpl_db['rdate'] = dbDate();
			$cpl_db['admin_id'] = $set->userInfo['id'];
			$cpl_db['affiliate_id'] = $affiliate_id;
			$cpl_db['merchant_id'] = $deal_merchant[$i];
			$cpl_db['dealType'] = 'cpl';
			if ($cpl_global == "0" OR $deal_cpl[$i] == "0") {
				if ($cpl_db['id']) dbDelete($cpl_db['id'],$appDeals);
				} else if ($cpl_global > "0" OR $deal_cpl[$i] > "0") {
				$cpl_db['amount'] = ($cpl_global ? $cpl_global : $deal_cpl[$i]);
				dbAdd($cpl_db,$appDeals);
				}
				
			// CPC
			if ($chkDealCPC['id']) $cpc_db['id'] = $chkDealCPC['id'];
			$cpc_db['rdate'] = dbDate();
			$cpc_db['admin_id'] = $set->userInfo['id'];
			$cpc_db['affiliate_id'] = $affiliate_id;
			$cpc_db['merchant_id'] = $deal_merchant[$i];
			$cpc_db['dealType'] = 'cpc';
			if ($cpc_global == "0" OR $deal_cpc[$i] == "0") {
				if ($cpc_db['id']) dbDelete($cpc_db['id'],$appDeals);
				} else if ($cpc_global > "0" OR $deal_cpc[$i] > "0") {
				$cpc_db['amount'] = ($cpc_global ? $cpc_global : $deal_cpc[$i]);
				dbAdd($cpc_db,$appDeals);
				}
			
			// CPM
			if ($chkDealCPM['id']) $cpm_db['id'] = $chkDealCPM['id'];
			$cpm_db['rdate'] = dbDate();
			$cpm_db['admin_id'] = $set->userInfo['id'];
			$cpm_db['affiliate_id'] = $affiliate_id;
			$cpm_db['merchant_id'] = $deal_merchant[$i];
			$cpm_db['dealType'] = 'cpm';
			if ($cpm_global == "0" OR $deal_cpm[$i] == "0") {
				if ($cpm_db['id']) dbDelete($cpm_db['id'],$appDeals);
				} else if ($cpm_global > "0" OR $deal_cpm[$i] > "0") {
				$cpm_db['amount'] = ($cpm_global ? $cpm_global : $deal_cpm[$i]);
				dbAdd($cpm_db,$appDeals);
				}
			}
		_goto($set->basepage.'?act=new&id='.$affiliate_id.'&ty=1');
		break;
	
	case "save_deal_tier":
		mysql_query("DELETE FROM ".$appDeals." WHERE dealType != 'tier' AND affiliate_id='".$affiliate_id."'");
		mysql_query("DELETE FROM ".$appDeals." WHERE dealType='tier' AND affiliate_id='".$affiliate_id."' AND tier_amount=''");

		for ($i=0; $i<count($deal_ids); $i++) {
			unset($db);
			if (!$deal_tier_amount[$i] AND $deal_cpa[$i] <= 0) {
				mysql_query("DELETE FROM ".$appDeals." WHERE id='".$deal_ids[$i]."'");
				continue;
				}
			$db['id'] = $deal_ids[$i];
			$db['rdate'] = dbDate();
			$db['admin_id'] = $set->userInfo['id'];
			$db['affiliate_id'] = $affiliate_id;
			$db['dealType'] = 'tier';
			$db['tier_amount'] = $deal_tier_amount[$i];
			$db['tier_pcpa'] = $deal_pcpa[$i];
			$db['amount'] = $deal_cpa[$i];
			dbAdd($db,$appDeals);
			}
		unset($db);
		if ($tier_amount AND ($cpa > 0 OR $pcpa > 0)) {
			$db['rdate'] = dbDate();
			$db['admin_id'] = $set->userInfo['id'];
			$db['affiliate_id'] = $affiliate_id;
			$db['merchant_id'] = $deal_merchant;
			$db['dealType'] = 'tier';
			$db['tier_amount'] = $tier_amount;
			$db['amount'] = $cpa;
			$db['tier_pcpa'] = $pcpa;
			dbAdd($db,$appDeals);
			}
		_goto($set->basepage.'?act=new&id='.$affiliate_id.'&ty=1');
		break;
	
	case "add_note":
		if ($note_id) $db['id'] = $note_id;
		if (!$db['id']) $db['rdate'] = dbDate();
		$db['valid'] = 1;
		$db['admin_id'] = $set->userInfo['id'];
		$db['edited_by'] = $set->userInfo['id'];
		$aff = dbGet($affiliate_id,"affiliates");
		$noteInfo = dbGet($note_id,"affiliates_notes");
		$db['group_id'] = $aff['group_id'];
		$db['affiliate_id'] = $affiliate_id;
		$db['notes'] = $text;
		$date = explode("/",$issue_date);
		$db['issue_date'] = $date[2].'-'.$date[1].'-'.$date[0].' '.$hour.':'.$min.':00';
		$db['status'] = $status;
		if ($status == "closed" AND $noteInfo['status'] != "closed") $db['closed_date'] = dbDate();
		if ($db['notes']) dbAdd($db,$appNotes);
		_goto($set->basepage.'?act=new&id='.$affiliate_id.'#notesPlace');
		break;
		
	case "remove_note":
		updateUnit($appNotes,"valid='0'","id='".$_GET['note_id']."'");
		_goto($set->basepage.'?act=new&id='.$_GET['affiliate_id']);
		break;
	
	default:
		$set->pageTitle = lang('Affiliates List');
		if ($act == "pending") {
			$search = 1;
			$where .= " AND valid='0'";
			$set->pageTitle = lang('Pending Affiliates');
			}
		if ($logged) $where .= " AND logged='1'";
		updateUnit($appTable,"logged='0'","lastactive <= '".date("Y-m-d H:i:s",strtotime("-20 Minutes"))."'");
		if ($q AND $field) {
			if ($field == "id") $where .= " AND lower(".$field.")='".$q."'";
				else $where .= " AND lower(".$field.") LIKE '%".strtolower($q)."%'";
			}
		
		if ($group_id >= "0") $where .= " AND group_id='".$group_id."'";
		
		$getPos = $set->itemsLimit;
		$pgg=$pg * $getPos;
		$sql = "SELECT * FROM ".$appTable." WHERE 1 ".$where." ORDER BY id DESC";
		$qq=mysql_query($sql); //  LIMIT $pgg,$getPos
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$affList .= '<tr>
					<td>'.$ww['id'].'</td>
					<td><a href="'.$set->basepage.'?act=new&id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="/?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank">'.lang('Login').'</a></td>
					<td align="left">'.$ww['username'].'</td>
					<td align="center"><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
					<td align="left">'.$ww['first_name'].'</td>
					<td align="left">'.$ww['last_name'].'</td>
					<td align="center">'.getCountry($ww['country'],1).'</td>
					<td align="left"><a href="'.$ww['website'].'" target="_blank">'.charLimit($ww['website'],30).'</a></td>
					<td align="center">'.listGroups($ww['group_id'],1).'</td>
					<td align="center">'.dbDate($ww['rdate']).'</td>
					<td align="center">'.dbDate($ww['lastvisit']).'</td>
					<td align="center"><img border="0" src="/admin/images/logged_'.$ww['logged'].'.png" alt="'.dbDate($ww['lastactive']).'" title="'.dbDate($ww['lastactive']).'" /></td>
					<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
				</tr>';
			}
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$online=mysql_result(mysql_query("SELECT COUNT(id) FROM ".$appTable." WHERE logged='1'"),0);
		$totalAffiliates=mysql_result(mysql_query("SELECT COUNT(id) FROM ".$appTable),0);
		$set->content = '<form method="get">
					<input type="hidden" name="search" value="1" />
					<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Search Affiliate').'</div>
					<div id="tab_1" style="width: 100%; background: #F8F8F8;">
					<table width="98%" border="0" cellpadding="0" cellspacing="5">
						<tr><td colspan="3" height="5"></td></tr>
						<tr>
							<td width="160" align="left">'.lang('String').':</td><td><input type="text" name="q" value="'.$q.'" /></td>
							<td width="100"><select name="field" style="width: 100px;"><option value="">'.lang('All Affiliates').'</option>'.listFields($field).'</select></td>
							<td width="100"><select name="group_id" style="width: 100px;"><option value="">'.lang('All Groups').'</option><option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>General</option>'.listGroups($group_id).'</select></td>
							<td align="left"><input type="submit" value="'.lang('Search').'" /></td>
							<td align="left" width="60%">
								'.lang('Total Online').': <a href="'.$set->basepage.'?act=search&logged=1"><b>'.$online.'</b></a> / '.lang('Total Affiliates').': <b>'.$totalAffiliates.'</b>
							</td>
						</tr>
					</table>
					'.($set->export ? '<div class="exportCSV"><a href="'.$set->basepage.'?act=xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>' : '').'
					</div>
					</form>
					<hr />
					<div class="normalTableTitle">'.$set->pageTitle.'</div>
					<table class="tablesorter" width="100%" border="0" cellpadding="0" cellspacing="0">
						<thead>
						<tr>
							<th>'.lang('Affiliate ID').'</th>
							<th>'.lang('Actions').'</th>
							<th align="left">'.lang('Username').'</th>
							<th align="center">'.lang('E-Mail').'</th>
							<th align="left">'.lang('First Name').'</th>
							<th align="left">'.lang('Last Name').'</th>
							<th align="center">'.lang('Country').'</th>
							<th align="left">'.lang('Website URL').'</th>
							<th align="center">'.lang('Group').'</th>
							<th align="center">'.lang('Registration Date').'</th>
							<th align="center">'.lang('Last Visit').'</th>
							<th align="center">'.lang('Logged').'</th>
							<th align="center">'.lang('Active').'</th>
						</tr></thead><tfoot></tfoot>
						<tbody>
						'.$affList.'
					</table>
					</div>'.getPager();
		theme();
		break;
		
	
	// --------------------------------------------------------- [ Profiles ] --------------------------------------------------------- //
	
	case "xml":
		if ($act == "pending") {
			$search = 1;
			$where .= " AND valid='0'";
			$set->pageTitle = 'Pending Affiliates';
			}
		if ($logged) $where .= " AND logged='1'";
		updateUnit($appTable,"logged='0'","lastactive <= '".date("Y-m-d H:i:s",strtotime("-20 Minutes"))."'");
		if ($q AND $field) {
			if ($field == "id") $where .= " AND lower(".$field.")='".$q."'";
				else $where .= " AND lower(".$field.") LIKE '%".strtolower($q)."%'";
			}
		
		if ($group_id >= "0") $where .= " AND group_id='".$group_id."'";
		
		$getPos = $set->itemsLimit;
		$pgg=$pg * $getPos;
		$sql = "SELECT * FROM ".$appTable." WHERE 1 ".$where." ORDER BY id DESC";
		$qq=mysql_query($sql); //  LIMIT $pgg,$getPos
		
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('ID'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Username'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('E-Mail'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('First Name'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Last Name'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Credit'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Country'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Website URL'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Group'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Registration Date'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Last Visit'));
		$i=1;
		while ($ww=mysql_fetch_assoc($qq)) {
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['id']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['username']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['mail']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['first_name']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['last_name']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['credit'].' USD');
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', getCountry($ww['country'],1));
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['website']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', listGroups($ww['group_id'],1));
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', ($ww['rdate'] != "0000-00-00 00:00:00" ? date("d/m/Y", strtotime($ww['rdate'])) : ''));
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', ($ww['lastvisit'] != "0000-00-00 00:00:00" ? date("d/m/Y", strtotime($ww['lastvisit'])) : ''));
			$i++;
			}
		$fileName = "admin/csv/report.csv";
		$openFile = fopen($fileName, 'w'); 
		// fwrite($openFile, $csvContent); 
		fclose($openFile); 
		header("Expires: 0");
		header("Pragma: no-cache");
		header("Content-type: application/ofx");
		header("Content-Disposition: attachment; filename=".$fileName);
		for ($i=0; $i<=count($csvContent)-1; $i++) echo implode(",",$csvContent[$i])."\n";
		die();
		break;
	
	case "profile_valid":
		$db=dbGet($id,$appProfiles);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appProfiles,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->basepage.'?act=profile_valid&id='.$db['id'].'\',\'profile_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "save_profile":
		if (!$db['url'] OR !$db['name']) _goto($set->basepage.'?act=new&id='.$db['affiliate_id']);
		
		$db[rdate] = dbDate();
		$db[valid] = 1;
		dbAdd($db,$appProfiles);
		_goto($set->basepage.'?act=new&id='.$db['affiliate_id'].'&ty=1');
		break;
	
	}

?>