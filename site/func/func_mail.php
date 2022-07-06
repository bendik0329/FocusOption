<?php

// NXCS2 [Encode in UTF-8 Without BOM] [☺]
$adminsGotMsg=array();
	
	function sendMail($type = 1, $copy = 1, $isTest = 0,$affiliate_id = -1) {
	global $set;

	if ($affiliate_id == -1){
		 getDefaultAffiliateID();
		$affiliate_id = $set->defaultAffiliateID;
	}


	
if ($set->isSmtpDebugMode==true){	
getDefaultAffiliateID();
$debugMode = $affiliate_id == $set->defaultAffiliateID  ? 1 : 0 ;
}
	
	if (!class_exists("phpmailer")) {
			// echo 'curdir: ' . getcwd();
			require_once('smtp/PHPMailerAutoload.php');
	}
	
	
	if (!$set->mail_server OR $set->mail_server == '') {
		return;
	}
		
		//die($set->mail_Port);
		
		$mail = new PHPMailer();
	//	$mail->SetLanguage("en", 'smtp/language/');
		
		if (1 == $set->isOffice365) {
			$mail->IsMail();  // send via mail() in case of office 365
		} else {
			$mail->IsSMTP();  // send via SMTP
		}
		
		$mail->IsHTML(true);					// send as HTML
	    $mail->SMTPDebug = $debugMode;
		$mail->SMTPAuth = $set->SMTPAuth == 1 ? true : false;
		$mail->Host = $set->mail_server;		// SMTP servers
		$mail->Hostname = $set->mail_server;		// SMTP servers
		if ($mail->SMTPAuth) {
		    $mail->Username = $set->mail_username;	// SMTP username
		    $mail->Password = $set->mail_password;				// SMTP password
		}
		$mail->CharSet = "UTF-8";
		$mail->Encoding ="8bit";
		$mail->Port = $set->mail_Port;
		
		//$mail->SMTPAuth   = true;
		//$mail->SMTPSecure = 'tls';
		
		if ($set->SMTPSecure=='' or $set->SMTPSecure=='None') {
			;
		} else {
		    $mail->SMTPSecure = strtolower($set->SMTPSecure);
		}
		// SET VARS
		//echo '<br>from: ' . $set->sendFrom . '<br>';
		
		if ($set->sendFrom) $from = $set->sendFrom;
			else {
			$exp_from = explode(",", str_replace(", ", ",", $set->webMail));
			$from = $exp_from[0];
			}
		
		$to = $set->sendTo;
		// $mail->to = $to;
		$nameto=$set->sendName;
		if ($set->fromName) $namefrom = $set->fromName;
			else $namefrom = $set->webTitle;

		
			
		$subject = ($set->subject ? $set->subject : 'No E-Mail Subject From: '.$set->webTitle);
		if ($type)
		{
			/* $message = '<div align="center">
				<table width="600" border="0" cellpadding="0" cellspacing="0">
					<tr>
						' .
						(!empty($set->emailHeaderImageURL) ? 
						'<td width="580"   style="padding: 10px;"><a href="'.$set->webAddress.'"><img border="0" src="'.$set->emailHeaderImageURL .'" alt="" /></a></td>' :
						'<td width="580" align="left" bgcolor="#1F82B2" style="padding: 10px;"><a href="'.$set->webAddress.'"><img border="0" src="'.$set->webAddress.'images/header/logo.png" alt="" /></a></td>'
						) . '
					</tr><tr>
						<td width="580" align="left" bgcolor="#FFF" style="padding: 10px; ' . (!empty($set->emailFooterImageURL) ? '' :  'border-bottom: 2px #1F82B2 solid;' ) . '">'.$set->body.'
					</tr><tr>' .
						(!empty($set->emailFooterImageURL) ? 
						'<td width="580"  style="padding: 10px;"><a href="'.$set->webAddress.'"><img border="0" src="'.$set->emailFooterImageURL .'" alt="" /></a></td>' :
						'<td width="580" align="left" bgcolor="#1F82B2" style="padding: 10px;"><a href="'.$set->webAddress.'"><img border="0" src="'.$set->webAddress.'images/header/logo.png" alt="" /></a></td>'
						) . '
					</tr><tr><td width="580">' .
						(!empty($set->emailSignature) ? $set->emailSignature : "" ). '
					</td>
					</tr>
				</table>
				</div>
				';
			else $message = $set->body; */
			
					$emailHeaderImageHTML= $set->emailHeaderImageURL;
		if (!empty($set->emailHeaderImageURL)) {
			if (strpos($emailHeaderImageHTML, 'http')==0)
			$emailHeaderImageHTML = '<td width="580"   style="padding: 10px;"><a href="'.$set->webAddress.'"><img border="0" src="'.$emailHeaderImageHTML .'" alt="" /></a></td>' ;
		}
		
		$emailFooterImageHTML= $set->emailFooterImageURL;
		if (!empty($set->emailFooterImageURL)) {
			if (strpos($emailFooterImageHTML, 'http')==0)
			$emailFooterImageHTML = '<td width="580"   style="padding: 10px;"><a href="'.$set->webAddress.'"><img border="0" src="'.$emailFooterImageHTML .'" alt="" /></a></td>' ;
		}
		
/* 						(!empty($set->emailHeaderImageURL) ? 

						'<td width="580"   style="padding: 10px;"><a href="'.$set->webAddress.'"><img border="0" src="'.$set->emailHeaderImageURL .'" alt="" /></a></td>' :
						'<td width="580" align="left" bgcolor="#1F82B2" style="padding: 10px;"><a href="'.$set->webAddress.'"><img border="0" src="'.$set->webAddress.'images/header/logo.png" alt="" /></a></td>'
						) . '
 */		
 
		 // (!empty($emailFooterImageHTML) ? '' :  'border-bottom: 2px #1F82B2 solid;' )
		 
		 if (!empty($emailHeaderImageHTML) && !empty($emailFooterImageHTML)) {
			$message = '<div align="center">
				<table width="600" border="0" cellpadding="0" cellspacing="0">
					<tr>
						' .
						$emailHeaderImageHTML . '
					</tr><tr>
						<td width="580" align="left" bgcolor="#FFF" style="padding: 10px; ' ."" . '">'.$set->body.'
					</tr><tr><td width="580">' .
						(!empty($set->emailSignature) ? $set->emailSignature : "" ). '
					</tr><tr>' .
						$emailFooterImageHTML . '
					</td>
					</tr>
				</table>
				</div>
				';
		 }
			else 
				$message = $set->body.	(!empty($set->emailSignature) ? $set->emailSignature : "" ) ;

			
		}

		 $mail->From = $set->webMail;
		 
		 //die ($mail->From);
		//die($set->mail_Port);
		
		// $mail->setFrom = $from;	
		$mail->setFrom = $mail->Username ;
		$mail->FromName = $namefrom;
		$mail->AddReplyTo = $namefrom;
		$mail->Subject = $subject;
		$mail->WordWrap = 50; 
		$mail->SMTP_PORT = $set->mail_Port;
		$mail->msgHTML= $message;
		$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
// var_dump($mail);
// die();		
		//die('SMTP_PORT: ' . $mail->SMTP_PORT);
		
		$mail->Body = $message;
			// var_dump($set);
			// die();
		
		$sendTo_exp = explode(";", str_replace(" ", "", $set->sendTo));
		if (count($sendTo_exp) < 1) {
			$mail->AddAddress($set->sendTo);
		} else {
			for ($i = 0; $i <= count($sendTo_exp); $i++){
				if (trim($sendTo_exp[$i])) {

					$mail->AddCC(trim($sendTo_exp[$i]));
				}
			}
		}
		if ($copy) {
			$exp = explode(",", str_replace(", ", ",", $set->webMail));
			if ($set->mailCopy) {
				if (count($exp) < 1) $mail->AddAddress($set->webMail);
				else {
					for ($i = 0; $i <= count($exp); $i++) {
						if (trim($exp[$i]))
							$mail->AddCC(trim($exp[$i]));
					}
				}
			}

			$copy_exp = explode(",", str_replace(", ", ",", $set->copyMail));
			if (count($copy_exp) < 1)
				$mail->AddAddress($set->copyMail);
			else {
				for ($i = 0; $i <= count($copy_exp); $i++)
					if (trim($copy_exp[$i]))
						$mail->AddCC(trim($copy_exp[$i]));
			}
		}
		
// var_dump($mail);
// die();
		
	/* 	
		//return $mail->Send();
		// var_dump($mail);
		// die();
		if (!$mail->Send()) {
			//var_dump($mail);
			if ($debugMode==1)
				function_mysql_query("insert into logs (`title`,`description`) values ('emailFailed','". $mail->ErrorInfo  . "');" ,__FILE__)   ;
			// if ($_GET['debug'])
			// var_dump($mail);
		}
		 */
		 
if ($debugMode==1 && $affiliate_id==$set->defaultAffiliateID) {		
echo '<br><br>--';
		var_dump($mail);
echo '<br><br>';
}
		
		
		$a =$mail->Send();
		
		file_put_contents('mail_send.log', "\n ***************** \n".$mail->ErrorInfo."\n ***************** \n".print_r($a,1)."\n --- \n". print_r($mail,1));
		
		
		if(!$a) {

if ($debugMode==1 && $affiliate_id==$set->defaultAffiliateID) {
				function_mysql_query("insert into logs (`title`,`description`) values ('emailFailed','". $mail->ErrorInfo  . "');" ,__FILE__,__FUNCTION__)   ;
				echo '<pre>';
			
				var_dump($a);
				echo '</pre>';
				
}

} else {

if ($debugMode==1)
				function_mysql_query("insert into logs (`title`,`description`) values ('success','". $mail->ErrorInfo  . "');" ,__FILE__,__FUNCTION__)   ;
}


		
		
		return 0;
		
	}

function validMail($email) {
	if (!filter_var($email,FILTER_VALIDATE_EMAIL)) $result = false;
		 else return true;
}



function sendTemplate($mailCode='',$affiliate_id='',$test=0, $newpass='', $ticketID=0, $merchant_id=0) {
	try{
		
		
		
		
		
		global $set;
		
		if (!$mailCode) return false;
		
		if ($test) {
			
			
			/* $sendTo = $set->userInfo['email'];
			$fromName = 'Test';
			$sendFrom = $set->webMail;
			 */
		} else {
			if (!$mailCode OR (!$affiliate_id AND !$merchant_id AND $mailCode!='NewAffiliateNotification')) return false;
			
			if($affiliate_id){
				$qry = "select ifnull(g.language_id,1) as group_language_id,af.* from affiliates af left join groups g on af.group_id = g.id where af.id = " .$affiliate_id;
				$getInfo = mysql_fetch_assoc(mysql_query($qry));
				// $getInfo = dbGet($affiliate_id,"affiliates");
				$FirstName = $getInfo['first_name'];
				$LastName = $getInfo['last_name'];
				$OptinGuid = $getInfo['optinGuid'];
				if($mailCode!='NewAffiliateNotification'){
					$sendTo = $getInfo['mail'];
					if (!$sendTo OR !$FirstName) return false;
					$fromName = $set->userInfo['first_name'].' '.$set->userInfo['last_name'];
					$sendFrom = $set->userInfo['email'];
				}
			}
			
			if($merchant_id){
				$merchantInfo = dbGet($merchant_id,"merchants");
				$merchantName = $merchantInfo['name'];
			}
			
			if($mailCode=='NewAffiliateNotification'){
				
				$grourow= mysql_fetch_assoc(function_mysql_query("select id from (select 1 as priority,id,title from groups where valid = 1 and makedefault=1
union
select 0 as priority, 0 as id , 'General' as title ) a limit 1;"));

				$groupqq = 'SELECT level,notifyOnAffReg,email FROM admins where valid =1 and group_id in ('.$grourow['id'].');';
				$adminQ = function_mysql_query($groupqq,__FILE__,__FUNCTION__);
				$allEmails = Array();
				
				while($row=mysql_fetch_assoc($adminQ)){
					
					if ($row['level']=='admin' ){ //AND $row['notifyOnAffReg']==1) {
						
						$adminsGotMsg[$row['id']]=$row['id'];
						
						  array_push($allEmails, $row['email']);
					}
					
				}
				
				$sendTo = implode(';',$allEmails);
					
			}
		}
		
		if (!$sendTo){
			return false;
		}

		// $qry = "SELECT * FROM mail_templates WHERE mailCode='".$mailCode."' LIMIT 1";
		$language_id = isset($getInfo['language_id']) ? $getInfo['language_id'] :( isset($getInfo['group_language_id']) ? $getInfo['group_language_id'] : 0);

		$qry = "
		select * from (
		(SELECT * FROM mail_templates WHERE valid=1 and mailCode='".$mailCode."'  and language_id in( ".$language_id.") limit 1)
		union
		(SELECT * FROM mail_templates WHERE valid=1 and  mailCode='".$mailCode."' limit 1) ) a limit 1;
		";

		$ww=mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
		if ($ww['valid']==0){
			return 0;
		}
		
		$set->sendTo = $sendTo;
		if($mailCode!='NewAffiliateNotification'){
			$set->sendFrom = $sendFrom;
			$set->fromName = $fromName;
		}
		
		$find = Array("{custom_field}","{affiliate_id}","{affiliate_username}","{affiliate_name}","{affiliate_email}","{accountManager_name}","{accountManager_email}","{accountManager_IM}","{brand_name}","{affiliate_password}", "{web_address}", "{web_mail}", "{aff_uname}", "{aff_fname}", "{merchant_name}", "{ticket_id}", "{base_url}", "{first_name}", "{last_name}","{optinGuid}");
		$replace = Array($set->custom_field,$getInfo['id'],$getInfo['username'],$FirstName.' '.$LastName,$sendTo,$set->userInfo['first_name'].' '.$set->userInfo['last_name'],$set->userInfo['email'],$set->userInfo['IMUser'],$set->webTitle, $newpass, $set->webAddress, $set->webMail, $set->aff_uname, $set->aff_fname, $merchantName, $ticketID, $set->webAddress, $FirstName, $LastName,$OptinGuid);
		$set->subject = str_replace($find,$replace,$ww['title']);
		$trackingCode = md5(time());
		$set->sendFrom = ($set->userInfo['email'] ? $set->userInfo['email'] : $set->webMail);
		$set->fromName = $set->webTitle;
		$signature .= '<br /><div style="font-size: 9px; color: #999999; line-height: 13px;">This message is intended only for use by the addressee(s) named above and may contain information that is confidential, private or privileged in nature. If you are not the intended recipient, you may not peruse, use, disseminate, distribute or copy this message or any file, which is attached to this message. If you have received this message in error, please delete it from your computer system, and notify us at the telephone number or e-mail address appearing above. Your co-operation and assistance is appreciated.</div>';
		
						//'.str_replace($find,$replace,nl2br($ww['text'])).'
		$set->body = '
		<div align="center">
			<table width="650" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td height="11"></td>
				</tr><tr>
					<td align="left" style="font-family: Verdana; font-size: 12px; line-height: 18px;">
						'.str_replace($find,$replace,htmlspecialchars_decode($ww['text'])).'
					</td>
				</tr><tr>
					<td height="30"></td>
				</tr><tr>
					<td align="left" style="font-family: Verdana; font-size: 11px; line-height: 18px;">
						'. (empty($set->emailSignature) ? str_replace($find,$replace,$signature)  : "" ).'
					</td>
				</tr>
			</table>
		</div>
		<img border="0" src="'.$set->http_host.'/opened/'.$trackingCode.'.gif" width="1" height="1" style="display: block;" alt="" />
		';
		
	if ($test) {
			var_dump($set);
			die ('ffefe');
	}
	
		if($mailCode=='NewAffiliateNotification'){
			
			sendMail(1,1,1,$affiliate_id);
		}else{
			sendMail(1,1,0,$affiliate_id);
		}
	
		
		if (!$test) {
				if($mailCode=='NewAffiliateNotification'){
						 
						foreach ($adminsGotMsg as $adminGotMsg=>$the_id){
							function_mysql_query("INSERT INTO mail_sent (rdate,trackingCode,valid,affiliate_id,admin_id,mail_id,mailCode) VALUES ('".dbDate()."','".$trackingCode."','1','".$affiliate_id."','".$adminGotMsg."','".$ww['id']."','".$ww['mailCode']."')",__FILE__,__FUNCTION__);
						}
				}
					else 
			function_mysql_query("INSERT INTO mail_sent (rdate,trackingCode,valid,affiliate_id,admin_id,mail_id,mailCode) VALUES ('".dbDate()."','".$trackingCode."','1','".$affiliate_id."','".$set->userInfo['id']."','".$ww['id']."','".$ww['mailCode']."')",__FILE__,__FUNCTION__);
		}
		return true;
		
	} catch(phpmailerException $e){
		echo '<br>PhpMailerException:<br>', $e->errorMessage(), '<br>';
		return 0;
		
	} catch(Exception $e){
		echo '<br>Exception:<br>', $e->getMessage(), '<br>';
		return 0;
	}
}

?>