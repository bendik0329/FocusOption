<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);



switch ($act) {
	
	case "send":
		
		
		if (!$db['subject']) $errors['subject'] = 1;
		if (!$db['description']) $errors['description'] = 1;
		if ($errors) {
		
		} else {
			
			$db['last_update'] = $db['rdate'] = dbDate();
			$db['ticket_id'] = 0;
			
			
			//$affiliate = mysql_fetch_assoc(function_mysql_query('SELECT * FROM affiliates WHERE id='.$db['affiliate_id']),__FILE__);
			//$db['group_id'] = $set->userInfo['group_id'];
			
			$db['merchantID'] = aesDec($_COOKIE['mid']);
			$db['aff_readed'] = 0;
			$db['status'] = 'open';
			$db['description'] = htmlentities($db['description']);
			
			
			$allEmails = Array();
			
			if(isset($_REQUEST['valid'])){
				array_push($allEmails,$set->userInfo['email']);
			}
			array_push($allEmails,"support@affiliatets.com");
			
			



$pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 }
 
 
$Identifier  =  'UserID: ' . $set->userInfo['id'] . ' <br>' . 
					'username: ' . $set->userInfo['username'] . ' <br>' . 
					'Level: ' . $set->userInfo['level'] . ' <br>'.
					'FirstName: ' . $set->userInfo['first_name'] . ' <br>'.
					'email: ' . $set->userInfo['email'] . ' <br>'.
					'pageUrl: ' . $pageURL . ' <br>'.
					'product Type: ' . $set->userInfo['productType'] . ' <br>' .
					
					

					
			$set->sendTo = implode(',',$allEmails);
			$set->subject = 'New Ticket:'.$db['subject'];
			$set->body = $Identifier . '<br>------------<br>' . $db['description'] ;

		

			sendMail();
			
			
			_goto($set->SSLprefix.$set->basepage);
			}
		
default:
		$set->pageTitle = lang('Affiliate Buddies Support Tickets').' - '. lang('Open New Ticket');
		
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Open New Ticket').'</div>
		<div class="text" style="padding:15px;font-size:14px; width: 90%;">'.lang('Please feel free to contact us if you have any questions or comments').'.</div>
			<div style="background: #F8F8F8; padding: 10px;">
				<form action="'.$set->basepage.'" method="post">
				<input type="hidden" name="act" value="send" />
				<table>

					<tr>
						<td '.err('subject').'>'.lang('Subject').':</td>
						<td><input type="text" name="db[subject]" value="" style="width: 600px;" maxlength="254" /></td>
					</tr><tr>
						<td valign="top" '.err('description').'>'.lang('Description').':</td>
						<td><textarea name="db[description]" cols="60" rows="8" style="width: 600px; height: 200px;"></textarea></td>
					</tr><tr>
						<!--<td valign="top">'.lang('E-mail').':</td>
						<td><input type="text" name="db[reply_email]" value="'.$set->userInfo['mail'].'" style="width: 200px;" /> '.lang('Send a copy to my e-mail').'</td>-->
						<td style="align:right" ><input type="checkbox" name="valid" /> '.lang('Send a copy to my e-mail').'</td>
					</tr><tr>
						<td colspan="2" align="right"><input type="submit" value="'.lang('Send').'" /></td>
					</tr>
				</table>
				</form>
			</div>';
		
		 theme();
		 break;
	 
}
?>