<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

/**
 * This function will be called from the "save_deal" case.
 */
require_once('common/global.php');
$userLevel = "manager";
if (!isManager()) _goto('/'.$userLevel.'/');

$appTable = 'affiliates';
$appNotes = 'affiliates_notes';
$appDeals = 'affiliates_deals';
$appProfiles = 'affiliates_profiles';

function doPost($url){
	$parse_url=parse_url($url);
	$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
	if (!$da) {
		echo "$errstr ($errno)<br/>\n";
		echo $da;
		} else {
		$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
		$params .= "Host: ".$parse_url['host']."\r\n";
		$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$params .= "User-Agent: ".$set->webTitle." Agent\r\n";
		$params .= "Content-Length: ".strlen($parse_url['query'])."\r\n";
		$params .= "Connection: close\r\n\r\n";
		$params .= $parse_url['query'];
		fputs($da, $params);
		while (!feof($da)) $response .= fgets($da);
		fclose($da);
		
		// split the result header from the content
		$result = explode("\r\n\r\n", $response, 2);
		$content = isset($result[1]) ? $result[1] : '';
		return $content;
		}
	}

	
function listFields($field='',$memberField='') {
	$arr = Array(
		"id" => lang("Affiliate ID"),
		"username" => lang("Username"),
		"mail" => lang("E-Mail"),
		"website" => lang("Website"),
		"profile" => lang("Profiles"),
		"first_name" => lang("First Name"),
		"last_name" => lang("Last Name")
		);
		
		if (strlen($memberField)>0) {
			$arr["member"] = lang(ucwords($memberField));
		}
	foreach ($arr AS $k => $v) $html .= '<option value="'.$k.'" '.($k == $field ? 'selected' : '').'>'.$v.'</option>';
	return $html;
	}


	
if($_REQUEST['editPixel']){

	$act = 'editPixel';

}else if($_REQUEST['deletePixel']){

	$act = 'deletePixel';

}else if($_REQUEST['testPixel']){

	$act = 'testPixel';

}

$set->content.='

	<script type="text/javascript">
		function activate(e){
                    $("."+e).attr("readonly",false);
                    $("."+e).css("background","#fff");
             //       console.log(e + " activate 111");
		} 
		
		function deactivate(e){
                    $("."+e).attr("readonly",true);
                    $("."+e).val("");
                    $("."+e).css("background","#e2e3e3");
            //        console.log(e + " activate 222");
		}
		
		function isEmpty(e){
                    return !($("."+e).val().length>0 && $("."+e).val()!=0 && $("."+e).val()!="0" && $("."+e).val()!="");
		}
		
	</script>';


switch ($act) {


	case "save_API_Access": 
		
		$apiToken = isset($_POST['apiToken']) ? $_POST['apiToken'] : "" ; 
		$api_affiliate_id = isset($_POST['affiliate_id']) ? $_POST['affiliate_id'] : (isset($id) ? $id :  0) ; 
		$apiAccessType = isset($_POST['apiAccessType']) ? $_POST['apiAccessType'] : "none" ; 
		$apiStaticIP = isset($_POST['apiStaticIP']) ? $_POST['apiStaticIP'] : "" ; 
		
		
		// var_dump($_POST);
		$qry = "update affiliates set apiToken='" .$apiToken."' , apiAccessType='" .$apiAccessType . "',apiStaticIP = '" . $apiStaticIP. "' where id=" . $api_affiliate_id;	
		// die ($qry);
		function_mysql_query($qry,__FILE__);
		
	
	_goto($set->basepage.'?act=new&id='.$api_affiliate_id.'');
			
	break;
	
	
	case "save_qualification": 
	
	// var_dump($_POST);
	// die();
		
		$qualify_type = isset($_POST['db']['qualify_type']) ? $_POST['db']['qualify_type'] : "" ; 
		$api_affiliate_id = isset($_POST['affiliate_id']) ? $_POST['affiliate_id'] : (isset($id) ? $id :  0) ; 
		$qualify_amount = isset($_POST['db']['qualify_amount']) ? $_POST['db']['qualify_amount'] : 0 ; 
		
		
		$qry = "update affiliates set qualify_amount='" .$qualify_amount."' , qualify_type='" .$qualify_type . "' where id=" . $api_affiliate_id;	
		// die ($qry);
		function_mysql_query($qry,__FILE__);
		
	
	_goto($set->basepage.'?act=new&id='.$api_affiliate_id.'');
			
	break;
	


	
	case "deletePixel":
		
		//echo $ids[0].'<BR>';
		//echo $pixelCode[0];
		$qry = 'delete from pixel_monitor where id=' . $ids[0];
		//die ($qry);
		function_mysql_query($qry,__FILE__);
		//echo 'id: ';
		
		//die ($db['id']);
		//_goto($set->basepage.'?act=new&id='.$id.'#tab_10');
		_goto($set->basepage.'?act=new&id='.$id.'&toggleTo=tab_10#tab_10');
			
	break;
	
	
	case "editPixel":
	
		//echo $ids.'<BR>';
		//echo $pixelCode[0];
		//$qry = "UPDATE pixel_monitor SET merchant_id='".$merchant_id[$i]."',method='" .$method[$i]. "', pixelCode='".mysql_escape_string($pixelCode[$i])."',type='".$type[$i]."',total='".$total[$i]."' WHERE id='".$ids[$i]."'";
		$qry = "update pixel_monitor set merchant_id=" .$merchant_id[0]." ,method='" .$method[0]. "',pixelCode = '" . mysql_escape_string($pixelCode[0]) ."' , type = '" .strtolower($type[0]). "' where id=" . $ids[0];	
		// $qry = "update pixel_monitor set merchant_id=" .$merchant_id[0]." ,method='" .$method[0]. "',pixelCode = '" . mysql_escape_string($pixelCode[0]) ."' , type = '" .$type[0]. "' where id=" . $ids[0];	
		// die ($qry);
		function_mysql_query($qry,__FILE__);
		_goto($set->basepage.'?act=new&id='.$id.'&toggleTo=tab_10#tab_10');
//		_goto($set->basepage.'?act=new&id='.$id.'#tab_10');
		//die('JUST EDIT PIXEL...');
	
	break;

	case "testPixel":
		$pxl = $pixelCode[0];
		$pxla = preg_replace("/{[-a-zA-Z0-9 _]+}/","1234",$pxl);
		$a="";
		if (strpos($pxla,'ttp')<3) {
			$a=doPost ($pxla); 
		}
		_goto($set->basepage.'?act=new&id='.$id.'&toggleTo=tab_10#tab_10');	
	//_goto($set->basepage.'?act=new&id='.$id.'#tab_10');
	break;
	
	case "valid":
		$db=dbGet($id,$appTable);
		// var_dump($db);
		// die();
		if ($db['valid']) $valid='0'; else $valid='1';
		if ($db['id']>0 &&  $valid==1) {
			$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE id=-9 and valid=1',__FILE__));
			sendTemplate($mailCode['mailCode'],$db['id']);
		}
		
		
		
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		
/* 		if ($valid==1) {
		$mailCode = 'AffiliateAccountIsNowActivated';
		$affiliate_id = $id;
		
		sendTemplate($mailCode,$affiliate_id);
		} */
			
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
		$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode FROM mail_templates WHERE id=-1',__FILE__));
		
		$set->sendTo = $getMail['mail'];
		$set->subject = $getMail['first_name'].' - Password Reset';
		
		sendTemplate($mailCode['mailCode'],$id,0,$new_password);
		
		
		/*
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
		*/
		_goto($set->basepage.'?act=new&id='.$id.'&ty=1');
		break;
	
	case "send_mail":
		sendTemplate($mailCode,$affiliate_id);
		_goto($set->basepage.'?act=new&id='.$affiliate_id.'&sent=1');
		break;
		
	
	
	case "delete":
		
		$dth = isset($_GET['deldth']) ? $_GET['deldth'] : "";
		$affiliate_id = isset($_GET['id']) ? $_GET['id'] : "";
		if (!empty($dth)) {
				$q = "delete from affiliates_deals where affiliate_id = ".$affiliate_id." and id = " . $dth;
				// die ($q);
				function_mysql_query($q,__FILE__);
		}
		_goto($set->basepage.'?act=new&id='.$affiliate_id);
		break;

	case "add":
		
		$chkUser = mysql_fetch_assoc(function_mysql_query("SELECT id FROM ".$appTable." WHERE lower(username)='".strtolower($db['username'])."' AND id != '".$db['id']."'",__FILE__));
		if ($chkUser['id']) $errors['username'] = lang('Username already exist');
		if (!$db['username']) $errors['username'] = lang('Username already exist');
		if (!$db['mail']) $errors['mail'] = lang('E-mails not match');
		if (!$db['first_name']) $errors['first_name'] = lang('Please fill out your first name');
		if (!$db['last_name']) $errors['last_name'] = lang('Please fill out your last name');
		if (!$db['website']) $errors['website'] = lang('Please fill out your website');
		if (isMustField('country') AND !$db['country']) $errors['country'] = lang('Please fill out your country');
		if (isMustField('phone') AND !$db['phone']) $errors['phone'] = lang('Please fill out your phone');
		
		if ($errors) {
			$idParam  = $lastID;
			if (isset($db['id']) && !empty($db['id']))
				$idParam = $db['id'];
			
			$str_error = '';
			foreach ($errors as $errItem => $errMessage) {
				$str_error .= $errItem.'|' . $errMessage ;
				break;
			}
			
			 _goto($set->basepage . '?act=new&id=' . $idParam. '&error=' . $str_error);
			
			
		} else {
			$db['ip'] = $set->userIP;
			
			if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
			if ($db['id']>0 &&  $valid==1) {
				
		
			$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE id=-9 and valid=1',__FILE__));
			
			
			sendTemplate($mailCode['mailCode'],$db['id']);
		}
		
		
			if ($password) $db['password'] = md5($password);
			
			if ($showDeposit) {
				$db['showDeposit'] = 1; 
			} else {
				$db['showDeposit'] = 0;
			}
		
			if ($newsletter) {
				$db['newsletter'] = 1; 
			} else {
				$db['newsletter'] = 0;
			}
			
			if (isset($_POST['show_credit']) && 'on' == $_POST['show_credit']) {
				$db['show_credit'] = 1;
			} else {
				$db['show_credit'] = 0;
			}
			
			if ($com_alert) {
				$db['com_alert'] = 1; 
			} else {
				$db['com_alert'] = 0;
			}
			
			$db['qualify_type'] = $set->def_qualify_type_for_affiliates ==0 ? '' : 'default';
		
			$lastID = dbAdd($db, $appTable);


			if($set->autoRelateNewAffiliateToAllMerchants==1){
				autoRelateAllMerchantsToAff($lastID);
			}

			
			if ($lastGroup_id != $db['group_id']) {
				chgGroup($lastID, $db['group_id']);
			}
			
			autoRelateCampToAff();
			_goto($set->basepage . '?act=new&id=' . $lastID . '&ty=1');
		}
	break;
	
			
	case "new":
		
		if ($id) {
			$db = dbGet($id, $appTable);
                        
                        
                        if (empty($db['accounts_pixel_params_replacing'])) {
                            $strAccountParamsDefault = '{"ctag":{"value":0,"caption":"Campaign Parameter"},'
                                                     . '"trader_id":{"value":0,"caption":"Trader ID"},'
                                                     . '"trader_alias":{"value":0,"caption":"Username"},'
                                                     . '"type":{"value":0,"caption":"Type of the account"},'
                                                     . '"affiliate_id":{"value":0,"caption":"Affiliate ID"},'
                                                     . '"uid":{"value":0,"caption":"Unique ID"},'
                                                     . '"dynamic_parameter":{"value":0,"caption":"Dynamic Parameter"}}';
                            
                            $sql = "UPDATE `affiliates` "
                                .  "SET `accounts_pixel_params_replacing` = '" . mysql_real_escape_string($strAccountParamsDefault) . "' "
                                .  "WHERE `id` = " . mysql_real_escape_string($id) . ";";
                            
                            function_mysql_query($sql,__FILE__);
                            unset($sql, $strAccountParamsDefault);
                        }
                        
                        if (empty($db['sales_pixel_params_replacing'])) {
                            $strSaleParamsDefault = '{"ctag":{"value":0,"caption":"Campaign Parameter"},'
                                                  . '"trader_id":{"value":0,"caption":"Trader ID"},'
                                                  . '"tranz":{"value":0,"caption":"Transaction ID"},'
                                                  . '"type":{"value":0,"caption":"Type of the account"},'
                                                  . '"currency":{"value":0,"caption":"Account Currency"},'
                                                  . '"amount":{"value":0,"caption":"Amount of the transaction"},'
                                                  . '"affiliate_id":{"value":0,"caption":"Affiliate ID"},'
                                                  . '"uid":{"value":0,"caption":"Unique ID"},'
                                                  . '"dynamic_parameter":{"value":0,"caption":"Dynamic Parameter"}}';
                            
                            $sql = "UPDATE `affiliates` "
                                .  "SET `sales_pixel_params_replacing` = '" . mysql_real_escape_string($strSaleParamsDefault) . "' "
                                .  "WHERE `id` = " . mysql_real_escape_string($id) . ";";
                            
                            function_mysql_query($sql,__FILE__);
                            unset($sql, $strSaleParamsDefault);
                        }
                        
                        
			$merchantList = explode("|",$db['merchants']);

			
			// die ('gergerg');
			if ($userLevel=='manager' && $db['group_id'] != $set->userInfo['group_id']) _goto($set->basepage);
			
			$networkWhereid = '';
			$networkWheremid='';
			if ($set->isNetwork==1) {
				$networkWhereid=' AND id='.aesDec($_COOKIE['mid']) . ' ';
				$networkWheremid=' AND merchant_id='.aesDec($_COOKIE['mid']) . ' ';
			}
			$keyName = '';
			$errDesc = '';
			$errors = [];
			if (isset($_GET['error']) && !empty($_GET['error'])) {
				$explodedArray = explode ('|',$_GET['error']);
				$keyName = ($explodedArray[0]);
				$errors[$keyName] = $explodedArray[1];
				$errDesc = ucwords(str_replace('%20',' ',$explodedArray[1]));
			}
			
			if(isset($toggleTo)){
				$set->content.='
					<script type="text/javascript">
						$(document).ready(function(){
							$("#'.$toggleTo.'").toggle("fast");
						});
					</script>
				';
			}
			// 1899-11-30 00:00:00
			$lastvisit = dbDate($db['lastvisit']) =='' ? lang('Not yet login') : dbDate($db['lastvisit']) ;
			$pageTitle = lang('EDIT AFFILIATE ACCOUNT').' #'.$db['id'].' ('.$db['first_name'].' '.$db['last_name'].')';
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
			if ($note_id) $edit_note=dbGet($note_id,$appNotes);
			// Tickets List
			$noteqq=function_mysql_query("SELECT * FROM ".$appNotes." WHERE affiliate_id='".$id."' AND valid='1' ORDER BY id DESC",__FILE__);
			while ($noteww=mysql_fetch_assoc($noteqq)) {
				$l++;
				$adminInfo=mysql_fetch_assoc(function_mysql_query("SELECT first_name,last_name FROM admins WHERE id='".$noteww['admin_id']."' LIMIT 1",__FILE__));
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
			$profileqq=function_mysql_query("SELECT * FROM ".$appProfiles." WHERE affiliate_id='".$id."' ORDER BY id DESC",__FILE__);
			while ($profileww=mysql_fetch_assoc($profileqq)) {
				$l++;
				$listProfiles .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$profileww['id'].'</td>
								<td align="left"><a data-name="'.$profileww['name'].'" data-source_traffic="'.$profileww['source_traffic'].'" data-description="'.$profileww['description'].'" data-url="'.$profileww['url'].'" data-id="'.$profileww['id'].'" href="/'.$userLevel.'/affiliates.php?act=save_profile&id='.$profileww['id'].'">'.lang('Edit').'</a></td>
								<td align="center">'.$profileww['name'].'</td>
								<td>'.$profileww['url'].'</td>
								<td>'.$profileww['description'].'</td>
								<td>'.$profileww['source_traffic'].'</td>
								<td id="profile_'.$profileww['id'].'"><a onclick="ajax(\''.$set->basepage.'?act=profile_valid&id='.$profileww['id'].'\',\'profile_'.$profileww['id'].'\');" style="cursor: pointer;">'.xvPic($profileww['valid']).'</a></td>
							</tr>';
			}
			} else {
                            $pageTitle = lang('NEW AFFILIATE ACCOUNT');
							$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
                            /**
                             * Determine whether to show credits or not.
                             * Begin.
                             */
                            $strSql   = 'SELECT show_credit_as_default_for_new_affiliates FROM settings;';
                            $resource = function_mysql_query($strSql,__FILE__);
                            
                            while ($arrRow = mysql_fetch_assoc($resource)) {
                                    $db['show_credit'] = $arrRow['show_credit_as_default_for_new_affiliates'] == 1 ? 1 : 0;
                            }
                            /**
                             * Determine whether to show credits or not.
                             * End.
                             */
			}
		
		$etqry = "SELECT id,mailCode FROM mail_templates WHERE 1=1 and " . ($id==500 ? "" :  " id>0 and "  ) . " valid='1' ORDER BY id ASC";
		$tempqq=function_mysql_query($etqry,__FILE__);
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
			$affiliateqq=function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
				while ($affiliateww=mysql_fetch_assoc($affiliateqq)) {
					$allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($db['refer_id'] == $affiliateww['id'] ? 'selected' : '').'>['.$affiliateww['id'].'] '.$affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
				}
			}
		
		$set->content .= '<div style="border: 1px #DDDDDD solid; margin: 10px 0 10px 0;">'. ($set->ShowGraphOnDashBoards==1 ||  $set->ShowGraphOnDashBoards== null ? chart('0','affiliate',$db['id'],1) : "" ) .'</div>
						<form action="/'.$userLevel.'/affiliates.php?act=add" method="post">
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						<input type="hidden" name="lastGroup_id" value="'.$db['group_id'].'" />
						'.($ty ? '<div class="Confirm">- '.lang('The page is up to date').' ('.dbDate().')</div><br />' : '').'
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Affiliate Details').'</div>
						<div id="tab_1" style="width: 100%; padding: 10px; background: #F8F8F8;">
						<table width="98%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td width="48%" align="left" valign="top">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
										'.($errors ? '<td colspan="2" align="left" style="color: red;"><b>'.lang('Please check one or more of the following fields').':</b><br /><ul type="*"><li />'.ucwords($keyName). '  -  '.$errDesc. '</ul></td>' : '').'<tr>
											<td align="left" width="200" class="blueText" '.err('username').'>'.lang('Username').':</td>
											<td align="left"><input type="text" name="db[username]" value="'.$db['username'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('password').'>'.lang('Password').':</td>
											<td align="left"><input type="password" name="password" placeholder="***********"  value="" style="width: 280px;" /></td>
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
										</tr><tr>';
										
										if($userLevel=='admin' || ($userLevel=='manager' && !$set->isNetwork)){
										$set->content .='
											<td align="left" class="blueText" '.err('mail').'>'.lang('E-Mail').':</td>
											<td align="left"><input type="text" name="db[mail]" value="'.$db['mail'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('phone').'>'.lang('Phone').':</td>
											<td align="left"><input type="text" name="db[phone]" value="'.$db['phone'].'" style="width: 280px;" />'.(isMustField('phone') ? ' <span class="required">*</span>' : '').'</td>
										</tr><tr>';
										}
	$set->content .='
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('country').'>'.lang('Country').':</td>
											<td align="left"><select name="db[country]" style="width: 292px;"><option value="">'.lang('Choose Your Country').'</option>'.getCountry($db['country']).'</select>'.(isMustField('country') ? ' <span class="required">*</span>' : '').'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('website').'>'.lang('Website URL').':</td>
											<td align="left"><input type="text" name="db[website]" value="'.$db['website'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
										<td colspan="2" height="5"></td>
										</tr>
										
										
										' .
										($db['website2'] != '' && $db['website2'] != 'http://' 
											? 
											' <tr>  <td align="left" class="blueText" '.err('website2').'>Website URL2:</td>
										        <td align="left"><input type="text" name="db[website2]" value="'.$db['website2'].'" style="width: 280px;" /></td>
										    </tr>
											<tr>
										        <td colspan="2" height="5"></td>
										    </tr>
											'
										    : '') . ' ' 
											
										. ($db['website3'] != '' && $db['website3'] != 'http://' ? 
											'<tr>
											     <td align="left" class="blueText" '.err('website3').'>Website URL3:</td>
												 <td align="left"><input type="text" name="db[website3]" value="'.$db['website3'].'" style="width: 280px;" /></td>
										    </tr>' : '')

										
										
										. '<tr> <!-- NEW -->'
											. '<td colspan="2" height="5"></td>
										</tr><tr>
';
										
										if($userLevel=='admin' || ($userLevel=='manager' && !$set->isNetwork)){
										$set->content .='
											<td align="left" class="blueText">'.lang('I.M. Type').':</td>
											<td align="left">
												<select name="db[IMUserType]" style="width: 292px;">
													<option value="">'.lang('Choose I.M. Type').'</option>
													<option value="Skype" '.($db['IMUserType'] == "Skype" ? 'selected="selected"' : '').'>'.lang('Skype').'</option>
													<option value="MSN" '.($db['IMUserType'] == "MSN" ? 'selected="selected"' : '').'>'.lang('MSN').'</option>
													<option value="Google Talk" '.($db['IMUserType'] == "Google Talk" ? 'selected="selected"' : '').'>'.lang('Google Talk').'</option>
													<option value="QQ" '.($db['IMUserType'] == "QQ" ? 'selected="selected"' : '').'>'.lang('QQ').'</option>
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
										</tr><tr>';
										}
			
										$set->content .='
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
											<td colspan="2" height="5"></td>'

											. ($set->AllowDealChangesByManager==1 ? '
										</tr>'.($set->introducingBrokerInterface ? '' : '<tr>
											<td align="left" class="blueText">'.lang('Sub Affiliate Commission').':</td>
											<td align="left">% <input type="text" name="db[sub_com]" value="'.$db['sub_com'].'" style="width: 100px; text-align: center;" /></td>
										</tr>').'<tr>
											<td colspan="2" height="5"></td>
										</tr>': '' ) . '
											
											<tr><td></td>
											<td align="left" class="blueText"><input type="checkbox" name="newsletter" '.($db['newsletter'] || $newsletter  ? 'checked' : '').' /> '.lang('Asked for Newsletter').'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td></td>
											<td align="left" class="blueText"><input type="checkbox" name="valid" '.($db['valid'] || $valid ? 'checked' : '').' /> '.lang('Active Account').'</td>
										</tr><tr>
										</tr>
										<tr>
											<td></td>
											<td align="left" class="blueText"><input type="checkbox" name="showDeposit" '.($db['showDeposit'] ? 'checked' : '').' /> '.lang('Show Deposits & Withdrawals').'</td>
										</tr>
										<tr>
											<td></td>
											<td align="left" class="blueText"><input type="checkbox" name="show_credit" ' . ($db['show_credit'] ? 'checked' : '') . ' /> ' . lang('Show credit') . '</td>
										</tr>
										<tr>
											<td colspan="2" height="5"></td>
										</tr><!--tr>
											<td></td>
											<td align="left" class="blueText"><input type="checkbox" name="com_alert" '.($db['com_alert'] ? 'checked' : '').' /> '.lang('Stop Commission Alerts').'</td>
										</tr--><tr>
											<td colspan="2" height="10"></td>
	</tr><tr>
											<td align="left" class="blueText">'.lang('Manager Private Note').':</td>
											<td align="left"><textarea  cols="80" rows="10" name="db[manager_private_note]"  style="width: 400px;height:100px; text-align: left;">'.$db['manager_private_note'].'</textarea></td>
											<!--td align="left"><input type="text" name="db[manager_private_note]" value="'.$db['manager_private_note'].'" style="width: 300px;height:25px; text-align: left;" /></td-->
										</tr><tr>
										<td colspan="2" height="5"></td>
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
											<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a href="'.$set->basepage.'../../tickets.php?act=new&affiliate_id='.$db['id']  .'">'.lang('Open a ticket').'</a></div></td>

										</tr><tr>
											<td colspan="2" height="5"></td>
											
										</tr><tr>
											<td align="left" width="160" height="20" class="blueText">'.lang('Sign up Date').':</td>
											<td align="left" class="greenText">'.dbDate($db['rdate']).'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="160" height="20" class="blueText">'.lang('Last Login').':</td>
											<td align="left" class="greenText">'.$lastvisit.'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										'.($set->introducingBrokerInterface ? '</tr><tr>
											<td align="left" width="160" class="blueText">'.lang('Affiliate Type').':</td>
											<td align="left">
												<select style="width: 242px;" name="db[isIB]">
													<option value="0" '.($db['isIB']==0 ? ' selected ' : '').'>'.lang('Affiliate').'</option>
													<option value="1" '.($db['isIB']==1 ? ' selected ' : '').'>'.lang('IB').'</option>
												</select>
											</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr>' : '').'
										<tr>
											<td align="left" width="160" class="blueText">'.lang('Status').':</td>
											<td align="left"><select name="db[status_id]" style="width: 242px;"><option value="0">'.lang('General').'</option>'.listStatus($db['status_id']).'</select></td>
										</tr><tr>
										<td colspan="2" height="5"></td>
										</tr><!--tr>
											<td align="left" width="160" class="blueText">'.lang('Group').':</td>
											<td align="left"><select name="db[group_id]" style="width: 242px;"><option value="0">'.lang('General').'</option>'.listGroups($db['group_id']).'</select></td>
											
										</tr-->'.($db['id'] ? '<tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="160" class="blueText">'.lang('Auto E-Mail').':</td>
											<td align="left">
												<select style="width: 242px;" onchange="confirmation(\''.lang('Are you sure you want to send this mail?').'\',\''.$set->basepage.'?act=send_mail&affiliate_id='.$db['id'].'&mailCode=\'+this.value);">
													<option value="">'.lang('Select E-Mail Template').'</option>
													'.$allTemplates.'
												</select>
											</td>
										
										</tr><tr>
										
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="160" class="blueText">'.($set->introducingBrokerInterface ? lang('Introduce Broker Parent') : lang('Sub Affiliate Of')).':</td>
											<td align="left">
												<select name="db[refer_id]" style="width: 242px;">
													<option value="">'.($set->introducingBrokerInterface ? lang('Parent IB Account') : lang('Main Affiliate Account')).'</option>
													'.$allAffiliates.'
												</select>
											</td>
										</tr>' : '').'
									</table>
									<hr />';
									
                                                        
                                                        $merchantName = strtolower($ww['name']);
                                                        $merchantID   = strtolower($ww['id']);
                                                        
													if ($db['id']) {
                                                        $sql = "SELECT COUNT(data_reg.id) AS count FROM data_reg AS data_reg "
                                                             . "INNER JOIN merchants AS mer ON mer.id = data_reg.merchant_id AND mer.valid = 1 "
                                                             . "WHERE data_reg.affiliate_id = " . $id;
                                                        
                                                        $arrTotalTraders  = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                                                        $totalTraders    += $arrTotalTraders['count'];
                                                        
                                                        
                                                        $ftd = count(getTotalFtds('', '', $id));
                                                        
                                                        
                                                        $sql = "SELECT data_sales.type, data_sales.amount FROM data_sales AS data_sales "
                                                             . "INNER JOIN merchants AS mer ON mer.id = data_sales.merchant_id AND mer.valid = 1 "
                                                             . "WHERE data_sales.affiliate_id = " . $id;
                                                        
                                                        $salesqq = function_mysql_query($sql,__FILE__);
                                                        
                                                        while ($salesww = mysql_fetch_assoc($salesqq)) {
                                                            if ($salesww['type'] == "deposit") $totalAmount += $salesww['amount'];
                                                            if ($salesww['type'] == "withdrawal") $totalWithdrawal += $salesww['amount'];
                                                        }
                                                        
                                                        
                                                        $sql = "SELECT IFNULL(SUM(total), 0) AS totalPaid FROM payments_paid WHERE affiliate_id = " . $id;
							$total = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                                                        
                                                        $sql = "SELECT id FROM payments_details WHERE affiliate_id = " . $id;
							$accounts = mysql_num_rows(function_mysql_query($sql,__FILE__));
                                                        
                                                        $sql = "SELECT id FROM payments_details WHERE status = 'canceled' AND affiliate_id = " . $id;
							$totalFruad = mysql_num_rows(function_mysql_query($sql,__FILE__));
                                                        
                                                        $sql = "SELECT id FROM payments_details WHERE status = 'pending' AND affiliate_id = " . $id;
							$totalPending = mysql_num_rows(function_mysql_query($sql,__FILE__));
                                                        
							$totalTraffic                = [];
                                                        $arrClicksAndImpressions     = getClicksAndImpressions(null, null, null, $id);
                                                        $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                                                        $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
							
													}
							$boxaName = $userLevel."-affiliates-1";
		
							$tableArr = Array(
									
								(object) array(
								  'id' => 'impressions',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Impressions').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraffic['totalViews'],0).'</td></tr>'
								),
																
								(object) array(
								  'id' => 'Clicks',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Clicks').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraffic['totalClicks'],0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Click Through Ratio (CTR)',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Click Through Ratio (CTR)').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).'%</td></tr>'
								),
								
								(object) array(
								  'id' => 'Click to Account',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang(ptitle('Click to Account')).':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format(($totalTraders/$totalTraffic['totalClicks'])*100,0).'%</td></tr>'
								),
								
								(object) array(
								  'id' => 'Click to Sale',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang(ptitle('Click to Sale')).':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,0).'%</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Paid',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Total Paid').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">$ '.@number_format($total['totalPaid'],2).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Traders',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang(ptitle('Total Traders')).':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraders,0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total FTDs',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('FTDs count').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($ftd,0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Deposit',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Total Deposit').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">$ '.@number_format($totalAmount,2).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Withdrawal',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Total Withdrawal').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">$ '.@number_format($totalWithdrawal,2).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Chargeback',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang(ptitle('Total Chargeback / Refund / Fraud')).':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalFruad,0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Pending',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Pending/Un Paid Traders').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($totalPending+$totalFruad,0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Affiliate Risk',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Affiliate Risk').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($totalFruad/$ftd)*100,2).'%</td></tr>'
								),
								
								(object) array(
								  'id' => 'Trader LTV',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;" title="Total Deposit Amount  / Total FTD Count">'.lang(ptitle('Trader LTV')).':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">$ '.@number_format($totalAmount/$ftd,2).'</td></tr>'
								)				
							);
							
					
							
							
							$set->content .= '<table width="100%" class="tablesorter" border="0" cellpadding="4" cellspacing="1" style="background: #DDDDDD;"><tbody>
										'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '</tr><tr>').'
									</tbody></table>
								</td>
							</tr><tr>
								<td colspan="3" height="10"></td>
							</tr><tr>
								<td colspan="3" align="right"><input type="submit" value="'.lang('Save').'" /></td>
							</tr></table>
						</form>
						</div>';
			
			if ($id) {
				
				$l=0;
				$merchantqq = function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
				$counter = mysql_fetch_assoc( function_mysql_query("SELECT count(id) as count FROM merchants WHERE valid='1' ORDER BY pos",__FILE__));
				$isOneOfMerchantsForex = false;
				
				$isForexOrBinary = false;
				
				
				while ($merchantww = mysql_fetch_assoc($merchantqq)) {
					$l++;
					
					if (
						'forex'  == strtolower($merchantww['producttype']) ||
						'binary' == strtolower($merchantww['producttype']) ||
						'forex'  == strtolower($merchantww['type']) ||
						'binary' == strtolower($merchantww['type'])
					) {
						$isForexOrBinary = true;
					}
					
					if (strtolower($merchantww['producttype']) == 'forex' && $isOneOfMerchantsForex == false) {
						$isOneOfMerchantsForex = true;
					}
					
					$IsMoreThanOneBrand = $counter['count'] > 1 ? true : false;
					
					//$IsMoreThanOneBrand = false;
					
					unset($min_cpaAmount, $cpaAmount, $dcpaAmount, $revenueAmount,$revenueSpreadAmount,$lotsAmount, $cplAmount, $cpcAmount, $cpmAmount, $positionsRevShareAmount);
					
					
					// nirs fix 06/1/2015
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='min_cpa' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "min_cpa") $min_cpaAmount = $takeww['amount'];
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='cpa' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpa") $cpaAmount = $takeww['amount'];
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='dcpa' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "dcpa") $dcpaAmount = $takeww['amount'];
						
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='revenue' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "revenue") $revenueAmount = $takeww['amount'];
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='revenue_spread' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "revenue_spread") $revenueSpreadAmount = $takeww['amount'];
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='lots' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					// die ($afDealsQuery);
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "lots"){ $lotsAmount = $takeww['amount'];   }
				
						$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='cpl' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpl") $cplAmount = $takeww['amount'];
				
	             	$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='cpc' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpc") $cpcAmount = $takeww['amount'];				

	             	$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='cpm' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpm") $cpmAmount = $takeww['amount'];		
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='positions_rev_share' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "positions_rev_share") $positionsRevShareAmount = $takeww['amount'];	
					
					
					/* original 
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by id desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__);
					while ($takeww = mysql_fetch_assoc($takeqq)) {
						
						if ($takeww['dealType'] == "min_cpa") $min_cpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpa") $cpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "dcpa") $dcpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "revenue") $revenueAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "revenue_spread") $revenueSpreadAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpl") $cplAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpc") $cpcAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpm") $cpmAmount = $takeww['amount'];
					}
					*/ 
					
					// if (!$min_cpaAmount) $min_cpaAmount = $merchantww['min_cpa_amount'];
					// if (!$cpaAmount) $cpaAmount = $merchantww['cpa_amount'];
					// if (!$revenueAmount) $revenueAmount = $merchantww['revenue_amount'];
					// if (!$cplAmount) $cplAmount = $merchantww['cpl_amount'];
					// if (!$cpcAmount) $cpcAmount = $merchantww['cpc_amount'];
					
					/*$min_cpaAmount = is_null($min_cpaAmount) ? ' ' : $min_cpaAmount;
					$cpaAmount = is_null($cpaAmount) ? ' ' : $cpaAmount;
					$dcpaAmount = is_null($dcpaAmount) ? ' ' : $dcpaAmount;
					$revenueSpreadAmount = is_null($revenueSpreadAmount) ? ' ' : $revenueSpreadAmount;
					$cpcAmount = is_null($cpcAmount) ? ' ' : $cpcAmount;
					$revenueAmount = is_null($revenueAmount) ? ' ' : $revenueAmount;
					$cpmAmount = is_null($cpmAmount) ? ' ' : $cpmAmount;*/
					
					// var_dump($merchantList);
					// die ('lots:' . $lotsAmount);
					// echo 'lot: ' . $lotsAmount . '<Br>';
					
					$listDealType .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
									<td align="center"><input type="checkbox" name="activeMerchants[]" value="'.$merchantww['id'].'" '.(@in_array($merchantww['id'],$merchantList) ? 'checked' : '').' /></td>
									<td align="left"><input type="hidden" name="deal_merchant[]" value="'.$merchantww['id'].'" /><b>'.$merchantww['name'].'</b></td>
									<td>$ <input class="dealType1'.$l.' deal_min_cpa'.$l.' minCpa'.$l.'" type="text" name="deal_min_cpa[]" value="'.$min_cpaAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
									<td>$ <input class="dealType1'.$l.' cpa'.$l.'" type="text" name="deal_cpa[]" value="'.$cpaAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
									<td>% <input class="dealType1'.$l.' dcpa'.$l.'" type="text" name="deal_dcpa[]" value="'.$dcpaAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
									<td>% <input class="dealType1'.$l.' rev'.$l.'" type="text" name="deal_revenue[]" value="'.$revenueAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' .
									($set->showPositionsRevShareDeal && $isForexOrBinary ? '<td>% <input class="dealType1'.$l.' positions_rev_share'.$l.'" type="text" name="deal_positions_rev_share[]" value="'.$positionsRevShareAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '<td style="display:none;">% <input class="dealType1'.$l.' positions_rev_share'.$l.'" type="text" /></td>') .
									($isOneOfMerchantsForex ? '<td>% <input class="dealType1'.$l.' rev_spread'.$l.'" type="text" name="deal_revenue_spread[]" value="'.$revenueSpreadAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '<td style="display:none;"><input style="width: 80px; text-align: center;" class="dealType1'.$l.' rev_spread'.$l.'" type="text" name="deal_revenue_spread[]" id="fieldClear"  /></td>').
									($isOneOfMerchantsForex ? '<td>$ <input class="dealType1'.$l.' lots'.$l.'" type="text" name="deal_lots[]" value="'.$lotsAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '<td style="display:none;"> <input class="dealType1'.$l.' lots'.$l.'" type="text" name="deal_lots[]"  id="fieldClear" style=width: 80px; text-align: center;" /></td>')
									.($set->deal_cpl ? '<td>$ <input class="dealType2'.$l.' cpl'.$l.'" type="text" name="deal_cpl[]" value="'.$cplAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
									'.($set->deal_cpc ? '<td>$ <input class="dealType2'.$l.' cpc'.$l.'" type="text" name="deal_cpc[]" value="'.$cpcAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
									'.($set->deal_cpm ? '<td>$ <input class="dealType2'.$l.' cpm'.$l.'" type="text" name="deal_cpm[]" value="'.$cpmAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
								</tr>
								
								<script type="text/javascript">
									$(document).ready(function(){
										
										$(".dealType1'.$l.'").keyup(function(){
																						
											if(!(isEmpty("rev'.$l.'") && isEmpty("dcpa'.$l.'") && isEmpty("cpa'.$l.'") && isEmpty("minCpa'.$l.'"))){
												deactivate("cpl'.$l.'");
												deactivate("cpc'.$l.'");
												deactivate("cpm'.$l.'");
												activate("cpa'.$l.'");
												activate("dcpa'.$l.'");
												activate("rev'.$l.'");
												activate("minCpa'.$l.'");
											}else{
												activate("cpl'.$l.'");
												activate("cpc'.$l.'");
												activate("cpm'.$l.'");
											}
										});
										
										
										$(".cpl'.$l.'").keyup(function(){
																						
											if(!(isEmpty("cpl'.$l.'"))){
												activate("cpl'.$l.'");
												deactivate("cpc'.$l.'");
												deactivate("cpm'.$l.'");
												deactivate("cpa'.$l.'");
												deactivate("dcpa'.$l.'");
												deactivate("rev'.$l.'");
												deactivate("minCpa'.$l.'");
											}else{
												activate("cpl'.$l.'");
												activate("cpc'.$l.'");
												activate("cpm'.$l.'");
												activate("cpa'.$l.'");
												activate("dcpa'.$l.'");
												activate("rev'.$l.'");
												activate("minCpa'.$l.'");
											}
										
										});
										
										
										$(".cpc'.$l.'").keyup(function(){
																						
											if(!(isEmpty("cpc'.$l.'"))){
												activate("cpc'.$l.'");
												deactivate("cpl'.$l.'");
												deactivate("cpm'.$l.'");
												deactivate("cpa'.$l.'");
												deactivate("dcpa'.$l.'");
												deactivate("rev'.$l.'");
												deactivate("minCpa'.$l.'");
											}else{
												activate("cpl'.$l.'");
												activate("cpc'.$l.'");
												activate("cpm'.$l.'");
												activate("cpa'.$l.'");
												activate("dcpa'.$l.'");
												activate("rev'.$l.'");
												activate("minCpa'.$l.'");
											}
										
										});
										
										
										$(".cpm'.$l.'").keyup(function(){
																						
											if(!(isEmpty("cpm'.$l.'"))){
												activate("cpm'.$l.'");
												deactivate("cpc'.$l.'");
												deactivate("cpl'.$l.'");
												deactivate("cpa'.$l.'");
												deactivate("dcpa'.$l.'");
												deactivate("rev'.$l.'");
											}else{
												activate("cpl'.$l.'");
												activate("cpc'.$l.'");
												activate("cpm'.$l.'");
												activate("cpa'.$l.'");
												activate("dcpa'.$l.'");
												activate("rev'.$l.'");
											}
										
										});
										
										/*
										$(".dealType'.$l.'").keyup(function() {
											if($(this).val().length>0 && $(this).val()!=0 && $(this).val()!="0" && $(this).val()!=""){
												var val = $(this).val();
												
												$(".dealType'.$l.'").attr("readonly",true);
												$(".dealType'.$l.'").val("");
												$(".dealType'.$l.'").css("background","#e2e3e3");
												$(this).val(val);
												$(this).attr("readonly",false);
												$(this).css("background","#fff");
											}else{
												$(".dealType'.$l.'").attr("readonly",false);
												$(".dealType'.$l.'").css("background","#fff");
											}
											
										});
										*/
										
										($(".cpa'.$l.'") && $(".cpa'.$l.'").val().length>0) 	? 	$(".cpa'.$l.'").trigger("keyup") 	: 		null;
										($(".dcpa'.$l.'") && $(".dcpa'.$l.'").val().length>0) 	? 	$(".dcpa'.$l.'").trigger("keyup") 	:	 	null;
										($(".rev'.$l.'") && $(".rev'.$l.'").val().length>0) 	? 	$(".rev'.$l.'").trigger("keyup") 	: 		null;
										'.($set->deal_cpl ? '($(".cpl'.$l.'") && $(".cpl'.$l.'").val().length>0) 	? 	$(".cpl'.$l.'").trigger("keyup") 	:	 	null;' : '').'
										'.($set->deal_cpc ? '($(".cpc'.$l.'") && $(".cpc'.$l.'").val().length>0) 	? 	$(".cpc'.$l.'").trigger("keyup") 	:	 	null;' : '').'
										'.($set->deal_cpm ? '($(".cpm'.$l.'") && $(".cpm'.$l.'").val().length>0) 	? 	$(".cpm'.$l.'").trigger("keyup") 	:	 	null;' : '').'
								
									});
								</script>
								
								';
					}
					
					
					$qry = "SELECT id,tier_amount,CONVERT(SUBSTRING_INDEX( tier_amount,  '-', 1 ),UNSIGNED INTEGER) as tierorder,"
                                                     . "tier_pcpa,amount,merchant_id , tier_type  "
                                                . "FROM ".$appDeals." "
                                                . "WHERE 1=1 "   .$networkWheremid. " and affiliate_id='".$id."' AND dealType='tier' ORDER BY tierorder ASC";
                                        
					$takeqq = function_mysql_query($qry,__FILE__);
					$strCurrentTierDealType = 'ftd_amount';
                                        $strTierTypePrefix = '$';
                                        $strTierTypeCaption = lang('Deposit Range').' '.'(ex. 100-200)';
                                        
					while ($takeww = mysql_fetch_assoc($takeqq)) {
                                            $merchantww = mysql_fetch_assoc(function_mysql_query("SELECT name FROM merchants WHERE id='".$takeww['merchant_id']."' ".$networkWhereid,__FILE__));
                                            $strCurrentTierDealType = $takeww['tier_type'];
                                            
                                            switch ($takeww['tier_type']) {
                                                case 'ftd_amount':
                                                    $strTierTypePrefix = '$';
                                                    $strTierTypeCaption =lang('Deposit Range').' (ex. 100-200)';
                                                    break;
                                                case 'rev_share':
                                                    $strTierTypePrefix = '%';
                                                    $strTierTypeCaption = lang('Precent Range').' (ex. 50-60)';
                                                    break;
                                                default:
                                                    $strTierTypePrefix = '';
                                                    $strTierTypeCaption = lang('Amount Range').' (ex. 10-20)';
                                                    break;
                                            }
                                            
                                            $listTier .= '<tr id="tr_tier_deal_' .  $takeww['id'] . '" data-existing_tier_deals="1" '.($ll % 2 ? 'class="trLine"' : '').'>
                                                            <td align="left"><input type="hidden" name="deal_ids[]" value="'.$takeww['id'].'" /><b>'.$merchantww['name'].'</b></td>
                                                            <td>
                                                                <select name="tier_deal_type">
                                                                    ' . ('ftd_amount' == $strCurrentTierDealType ? '<option value="ftd_amount" selected>' . lang('FTD Amount') . '</option>' : '') . '
                                                                    ' . ('ftd_count' == $strCurrentTierDealType ? '<option value="ftd_count" selected>' . lang('FTD Count') . '</option>' : '') . '
                                                                    ' . ('cpl_count' == $strCurrentTierDealType ? '<option value="cpl_count" selected>' . lang('CPL Count') . '</option>' : '') . '
                                                                    ' . ('rev_share' == $strCurrentTierDealType ? '<option value="rev_share" selected>' . lang('Rev. Share') . '</option>' : '') . '
                                                                </select>
                                                            </td>
                                                            <td>' . $strTierTypePrefix . ' <input type="text" name="deal_tier_amount[]" value="'.$takeww['tier_amount'].'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                            <td>$ <input type="text" name="deal_cpa[]" value="'.$takeww['amount'].'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                            <td>% <input type="text" name="deal_pcpa[]" value="'.$takeww['tier_pcpa'].'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                            <td style="float:left !important;"><input type="submit" name="delete" value="'.lang('Delete').'" onclick="return deleteTierDeal(' . $takeww['id'] . ');" /></td>
                                                            <td><input type="hidden" name="current_tier_type[]" value="' . $strCurrentTierDealType . '" /></td>
                                                        </tr>';
                                            $ll++;
					}
					
					
					if ($db['id']) $set->content .= '
							<br />
							<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_2\').slideToggle(\'fast\');">'.lang('Profiles').'</div>
							<div id="tab_2" style="width: 100%; background: #F8F8F8;">
							
							
							
							<form action="'.$set->basepage.'" method="post">
						<input type="hidden" name="act" value="save_profile" />
						<input type="hidden" name="db[affiliate_id]" value="'.$id.'" />
							<div align="left" style="padding: 10px;">
								<table><tr>
								<td align="left" width="100" class="blueText">'.lang('Profile Name').':</td>
								<td align="left"><input id="db_name" type="text" name="db[name]" value="" style="width: 250px;" /></td>
								<td width="80"></td>
								<td align="left" class="blueText">'.lang('URL').':</td>
								<td align="left"><input id="db_url" type="text" name="db[url]" value="http://" style="width: 250px;" /></td>
								<td width="80"></td>
								<td></td>
							</tr><tr>
								<td align="left" class="blueText">'.lang('Description').':</td>
								<td align="left"><input id="db_description" type="text" name="db[description]" value="" style="width: 250px;" /></td>
								<td></td>
								<td align="left" class="blueText">'.lang('Traffic Source').':</td>
								<td align="left"><input id="db_source_traffic" type="text" name="db[source_traffic]" value="" style="width: 250px;" /></td>
								<td><input type="hidden" id="db_id" name="db[id]" /></td>
								<td align="right"><input type="submit" value="'.lang('Save').'" /></td>
							</tr></table>
							</form>
							</div>
							</div>
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td>#</td>
									<td style="text-align: left;">'.lang('Action').'</td>
									<td style="text-align: center;">'.lang('Profile Name').'</td>
									<td>'.lang('URL').'</td>
									<td>'.lang('Description').'</td>
									<td>'.lang('Traffic Source').'</td>
									<td>'.lang('Available').'</td>
								</tr></thead><tfoot><tr style="background: #D9D9D9;">
									<td align="left"></td>
									<td align="left"></td>
									<td align="center"><b>'.lang('Default').'</b></td>
									<td>'.$set->userInfo['website'].'</td>
									<td></td>
									<td></td>
									<td></td>
								</tr>'.$listProfiles.'</tfoot>
							</table>
								<script>
						$("[data-id]").click(function() {
							$("#db_id").val($(this).data("id"));
							$("#db_name").val($(this).data("name"));
							$("#db_source_traffic").val($(this).data("source_traffic"));
							$("#db_description").val($(this).data("description"));
							$("#db_url").val($(this).data("url"));
							$("#id_hidden").val($(this).data("id"));
							return false;
						});
						</script>'
						.
							
                                                
							'</div>
							<br />
                                                        
                                                        
                                                        
							<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Deal Type').'</div>
							<div id="tab_3" style="width: 100%; background: #F8F8F8;">
								<form action="'.$set->basepage.'" method="post">
								<input type="hidden" name="act" value="' . ($userLevel =='admin' || ($userLevel =='manager' && $set->AllowDealChangesByManager==1) ? 'save_deal' : 'disable'  ) .'" />
								<input type="hidden" name="affiliate_id" value="'.$id.'" />
								<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
									<thead><tr>
										<td align="center">'.lang('Active').'</td>
										<td style="text-align: left;">'.lang('Merchant').'</td>
										<td>'.lang(ptitle('Min. Deposit')).'</td>
										<td>'.lang('CPA').'</td>
										<td>'.lang('DCPA').'</td>
										<td>'.lang('Revenue Share').'</td>'. 
										($set->showPositionsRevShareDeal && $isForexOrBinary ? '<td>' . lang(ptitle('Positions Rev. Share')) . '</td>' : '') . 
										($isOneOfMerchantsForex ? '<td>'.lang('Revenue Share Spread').'</td>' : '') . 
										($isOneOfMerchantsForex ? '<td>'.lang('Lots').'</td>' : '') . '
										'.($set->deal_cpl ? '<td>'.lang('CPL').'</td>' : '').'
										'.($set->deal_cpc ? '<td>'.lang('CPC').'</td>' : '').'
										'.($set->deal_cpm ? '<td>'.lang('CPM').'</td>' : '').'
									</tr></thead>
									
									<tfoot><tr style="background: #D9D9D9;'.($IsMoreThanOneBrand==1 ? '' : 'display:none;').'">
									<td></td>
									<td align="left"><b>'.lang('Global To All Merchants').'</b></td>
									<td>$ <input type="text" name="min_cpa_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
									<td>$ <input type="text" name="cpa_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
									<td>% <input type="text" name="dcpa_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
									<td>% <input type="text" name="revenue_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>'.
									($set->showPositionsRevShareDeal && $isForexOrBinary ? '<td>% <input type="text" name="positions_rev_share_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '') . 
									 
									($isOneOfMerchantsForex ? '<td>% <input type="text" name="revenue_spread_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>':'').
									($isOneOfMerchantsForex ? '<td>$ <input type="text" name="lots_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>':'').'
									'.($set->deal_cpl ? '<td>$ <input type="text" name="cpl_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
									'.($set->deal_cpc ? '<td>$ <input type="text" name="cpc_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
									'.($set->deal_cpm ? '<td>$ <input type="text" name="cpm_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
									</tr>'.$listDealType.'</tfoot>
								</table>'.
								($userLevel =='admin' || ($userLevel =='manager' && $set->AllowDealChangesByManager==1) ?
															'
																<div align="right" style="padding-top: 20px;">
                                                                        <input type="submit" value="'.lang('Save').'" />' :'').'
                                                                        <br />
                                                                </div>
								<div style="padding:5px; color:GREEN; font-weight:bold">* '.lang('Empty values on all fields will be automatically converted to system default commission values').'</div>
								<!--div style="padding:5px; color:GREEN; font-weight:bold">* '.lang('0 values will override default merchant values').', '.lang('make sure that the lower Minimum Deposit value is 1').'.</div-->
								</form>
								<br />';
								
								// var_dump($set);
								// die();
								if ($userLevel =='admin' || ($userLevel =='manager' && $set->AllowDealChangesByManager==1 
								&& !empty($set->showDealTypeHistoryToAM )
								)) {
								 $set->content .='
								<!-- Deal-types history -->
								<div class="normalTableTitle" id="notesPlace" style="cursor: pointer;" onclick="$(\'#tab_history\').slideToggle(\'fast\');">'.lang('Deal Types History').'</div>
								<div id="tab_history" style="width: 100%; display: '.($note_id ? 'block' : 'none').'">
									<div>
										<table>
											<tr>
												<td>' . lang('Choose a merchant') . ':</td>
												<td>
													<select id="select_merchant">
														<!-- Content will be loaded via ajax call. -->
													</select>
												</td>
												<td>
													<input type="submit" id="load_deal_types_history" value="' . lang('Load') . '" />
												</td>
											</tr>
										</table>
									</div>
									<div id="div_deal_type_history">
										<!-- Content will be loaded via ajax call. -->
									</div>
									<script>
										(function() {
                                                                                    $.get("ajax/LoadMerchants.php?affiliate_id=' . $_GET['id'] . '", function(res) {
                                                                                        try {
                                                                                            res = JSON.parse(res);
                                                                                            
                                                                                            if (res["success"]) {
                                                                                                for (var i = 0; i < res["success"].length; i++) {
                                                                                                    $("<option>").attr("value", res["success"][i]["id"])
                                                                                                                 .text(res["success"][i]["name"])
                                                                                                                 .appendTo("#select_merchant");
                                                                                                }
                                                                                            }
                                                                                            
                                                                                        } catch (error) {
                                                                                            console.log(error);
                                                                                        }
                                                                                    });
										})();
										
										$("#load_deal_types_history").click(function() {
											var intMerchantId = $("#select_merchant").val();
											$.get("ajax/LoadDealTypesHistory.php?is_admin=1&affiliate_id=' . $_GET['id'] . '&merchant_id=" + intMerchantId, function(res) {
												try {
												    res = JSON.parse(res);
												    
													if (res["success"]) {
														$("#div_deal_type_history").html(res["success"]);
													}
													
												} catch (error) {
													console.log(error);
												}
											});
											return false;
										});
									</script>
								</div>
								<br />
								' ;
								}
								
							
								if ($set->isBasicVer ==1 ) {
								$set->content .= '
								<style>
								div.floatFeatures {
    float: right;
    height: 115px;
    position: fixed;
    right: 0px;
    background: whitesmoke;
    border-bottom: 1px solid gray;
    border-top: solid gray 1px;
    border-left: 1px solid gray;
	    top: 80%;
    padding: 8px;
    border-radius: 10px 10px 0px 0px;
    font-size: 12px;
    font-weight: bold;
}
.animation-examplesone {
  outline: 1px dashed #E0E4CC;
  /* color: #69D2E7; */
  box-shadow: 0 0 0 3px #69D2E7;
  animation: 2s animateBorderOne ease infinite;
}

@keyframes animateBorderOne {
  to {
    outline-color: #69D2E7;
    box-shadow: 0 0 0 4px #E0E4CC;
  
}
div.floatFeatures div {
	font-size:16px;
	PADDING-BOTTOM: 10PX;
}

								</style>
								<div class="floatFeatures animation-examplesone ">
								<div>'.lang('Upgrade now to get the following features and many other more').'</div>
								<li>'.lang('Additional deal types: CPC, Revenue Share, Positions Revenue Share, Lots, CPL').'</li>
								<li>'.lang('Qualified commission by trades and volume and Total Minimum Deposit').'</li>
								<li>'.lang('Manager Note CRM').'</li>
								<li>'.lang('API Integration').'</li>
								<li>'.lang('Sub affiliates').'</li>
								<li>'.lang('Traffic Sources').'</li>
								</div>
							
								';
									
								}
								 
								 
								 if (($set->isBasicVer ==0 && $userLevel =='admin') || ($userLevel =='manager')){
								
								if ($set->AllowDealChangesByManager==1) {
								$set->content .= '
								'.($set->deal_tier ? '<div class="normalTableTitle">'.lang('Tier Deal').'</div>
								<div style="font-size: 10px; padding: 5px;">
                                                                    * '.lang('Tier deal will erase all previous deals for this affiliate').'
                                                                </div>
								
                                                                
                                                                <script type="text/javascript">
                                                                    function deleteTierDeal(intTierDealId) {
                                                                        if (confirm("Tier deal will be deleted")) {
                                                                            var strAjaxAddr = "' . $_SERVER['SERVER_HOST'] . '/ajax/DeleteTierDealType.php";
                                                                            $.post(strAjaxAddr, { id : intTierDealId }, function(res) {
                                                                                try {
                                                                                    if ("1" != res) {
                                                                                        console.log("\n\nChosen deal was not deleted due to an error\n\n");
                                                                                    } else {
                                                                                        $("#tr_tier_deal_" + intTierDealId).remove();
                                                                                    }
                                                                                    
                                                                                } catch (error) {
                                                                                    console.log("\n\nException: " + error + "\n\n");
                                                                                }
                                                                            });
                                                                        }
                                                                        return false;
                                                                    }
                                                                </script>
                                                                
                                                                <form id="form_tier_deal_type" action="'.$set->basepage.'" method="post">
                                                                    <input type="hidden" name="act" value="save_deal_tier" />
                                                                    <input type="hidden" name="affiliate_id" value="'.$id.'" />
                                                                    <table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
                                                                            <thead><tr>
                                                                                    <td style="text-align: left;">'.lang('Merchant').'</td>
                                                                                    <td>' . lang('Tier Deal Type') . '</td>
                                                                                    <td>' . lang($strTierTypeCaption) . '</td>
                                                                                    <td>' . lang('CPA').'</td>
                                                                                    <td>' . lang('PCPA').'</td>
                                                                                    <td width="35%"></td>
                                                                            </tr></thead><tfoot>'.$listTier.'<tr '.($ll % 2 ? 'class="trLine"' : '').'>
                                                                            <td style="text-align: left;"><select name="deal_merchant">'.listMerchants($merchantww['id']).'</select></td>
                                                                            <td>
                                                                                <select name="tier_deal_type_new">
                                                                                    <!-- Content will loaded via ajax. -->
                                                                                </select>
                                                                            </td>
                                                                            <td><span>' . $strTierTypePrefix . '</span> <input type="text" name="tier_amount" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                                            <td><span>$</span> <input type="text" name="cpa" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                                            <td><span>%</span> <input type="text" name="pcpa" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                                            <td></td>
                                                                    </tr></tfoot>
                                                                    </table>
                                                                    <div align="right" style="padding-top: 20px;"><input type="submit" value="'.lang('Save').'" /></div>
								</form>
                                                                
                                                                <script type="text/javascript">
                                                                    var intCurrentMerchant  = $("#form_tier_deal_type select[name=deal_merchant]").val();
                                                                    var intCurrentAffiliate = "' . $_GET['id'] . '";
                                                                    
                                                                    (function() {
                                                                        var strAjaxAddr = "' . $_SERVER['SERVER_HOST'] . '/ajax/PopulateTierDealTypeSelect.php?merchant_id=" 
                                                                                        + intCurrentMerchant 
                                                                                        + "&affiliate_id=" 
                                                                                        + intCurrentAffiliate;
                                                                        
                                                                        $.get(strAjaxAddr, function(res) {
                                                                            try {
                                                                                $("#form_tier_deal_type select[name=tier_deal_type_new]").html(res);
                                                                            } catch (error) {
                                                                                console.log(error);
                                                                            }
                                                                        });
                                                                    })();
                                                                    
                                                                    $("#form_tier_deal_type select[name=deal_merchant]").change(function() {
                                                                        var strAjaxAddr = "' . $_SERVER['SERVER_HOST'] . '/ajax/PopulateTierDealTypeSelect.php?merchant_id=" 
                                                                                        + $(this).val() 
                                                                                        + "&affiliate_id=" 
                                                                                        + intCurrentAffiliate;
                                                                        
                                                                        $.get(strAjaxAddr, function(res) {
                                                                            try {
                                                                                $("#form_tier_deal_type select[name=tier_deal_type_new]").html(res);
                                                                            } catch (error) {
                                                                                console.log(error);
                                                                            }
                                                                        });
                                                                    });
                                                                    
                                                                    /*var strOriginalTierType = $("#form_tier_deal_type [name=tier_deal_type]").val();
                                                                    
                                                                    $("#form_tier_deal_type [name=tier_deal_type]").change(function() {
                                                                        if ($(this).val() != strOriginalTierType) {
                                                                            $("[data-existing_tier_deals]").hide();
                                                                        } else {
                                                                            $("[data-existing_tier_deals]").show();
                                                                        }
                                                                    });*/
                                                                </script>
                                                                
                                                                
								
							</div>
							<br />' : '').'
							<form action="'.$set->basepage.'" method="post">
							<input type="hidden" name="act" value="add_note" />
							<input type="hidden" name="affiliate_id" value="'.$id.'" />
							<input type="hidden" name="note_id" value="'.$note_id.'" />';
						
// var_dump($db);
// die();

						$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_4\').slideToggle(\'fast\');">'.lang('Qualified Commission').'</div>
						<div id="tab_4" style="width: 100%; background: #F8F8F8; display: none;">
							
							
							
							<table>
							<tr>
							<td width="50%" align="left" valign="top">
								<form id="save_qualification" action="'.$set->basepage.'" method="post">
                                                                    <input type="hidden" name="act" value="save_qualification" />
                                                                    <input type="hidden" name="affiliate_id" value="'.$id.'" />
																	
								<table border="0" cellpadding="0" cellspacing="5">
									<tr><td>'.lang('Choose the prefered option').':
									</td>
									</tr>
									
							<tr>
									<td colspan="2" height="5"></td>
								
								</tr>
								
									<tr  id="empn" style="margin-top:20px;">
										<td align="left">'.lang('Qualify Commission').':</td>
										<td align="left"><select  id="empnt" name="db[qualify_type]" style="width: 292px;">
											<option value=""  '.($db['qualify_type'] == "" ? "selected" : ''). '>'.lang('None').'</option>
											<option value="default" '.($db['qualify_type'] == "default" ? 'selected' : ''). '>'.lang('Merchant Default').'</option>
											<option value="trades" '.($db['qualify_type'] == "trades" ? 'selected' : ''). '>'.lang('Number Of Trades').'</option>
											<option value="totalmd" '.($db['qualify_type'] == "totalmd" ? 'selected' : ''). '>'.lang('Total Minimum Deposit').'</option>
											<option value="volume" '.($db['qualify_type'] == "volume" ? 'selected' : ''). '>'.lang('Amount Of Volume').'</option>
											
										</td>
										</tr>
										<tr  id="empv" style="'.($db['qualify_type'] == "totalmd" || $db['qualify_type'] == "trades" || $db['qualify_type'] == "volume" ? '' : 'display:none').' ;margin-top:20px;">
										<td align="left">
										'.lang('Limitation').': <input type="text" name="db[qualify_amount]" value="'.$db['qualify_amount'].'" style="width: 100px; text-align: center;" maxlength="5" /></td>
									</tr>
								<tr>
									<td colspan="2" align="right"><input type="submit" value="'.lang('Save').'" />
						</form>
									</td>
								</tr>
								</table>
							</td>
						</tr>
						</table>
						
						</div>
							<script type="text/javascript">
								$(document).ready(function(){
									$("#empn").change(function(e){
										if($("#empnt").val().length>0 && $("#empnt").val()!="default" ){
											$("#empv").show();
										}else{
											$("#empv").hide();
										}
									});
								});
							</script>
						<br />
						';
								}
						
							$set->content .= '
							<div class="normalTableTitle" id="notesPlace" style="cursor: pointer;" onclick="$(\'#tab_5\').slideToggle(\'fast\');">'.lang('Manager Notes CRM').'</div>
							<div id="tab_5" style="width: 100%; display: '.($note_id ? 'block' : 'none').'">
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
			
			$qq=function_mysql_query("SELECT * FROM mail_sent WHERE affiliate_id='".$db['id']."' ORDER BY id DESC",__FILE__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$adminInfo=mysql_fetch_assoc(function_mysql_query("SELECT first_name,last_name FROM admins WHERE id='".$ww['admin_id']."'",__FILE__));
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
                        
			/* $set->content .= '
			<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_10\').slideToggle(\'fast\');">'.lang('Documents').'</div>
                                <div id="tab_10" style="width: 100%; background: #F8F8F8; display: none;">
                                </div>';*/		    
                    
                    
                    
                    
                    //////////////////////////// showDocumentsModule ////////////////////////////////////////////
                    include 'common/DocumentsPanel.php';
                    //////////////////////////// showDocumentsModule ///////////////////////////////////////////
                    
                        
                        
			

				$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_6\').slideToggle(\'fast\');">'.lang('E-mails Monitor').'</div>
						<div id="tab_6" style="width: 100%; background: #F8F8F8; display: none;">
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
			
			$qq=function_mysql_query("SELECT * FROM affiliates WHERE valid='1' AND refer_id='".$db['id']."' ORDER BY id DESC",__FILE__);
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

			
                  

		
				$affiliateapiurl = "";
				if (!empty($db['apiStaticIP']) && !empty($db['apiToken'])) {
					// $affiliateapiurl = $set->webAddress .'/affiliate/apifeed.php?apiToken='. $db['apiToken'].'&apiStaticIP='. $db['apiStaticIP'].'&affiliate_id='. $id  .'&fromdate=' . date("Y-m-01",strtotime("-1 Months"));
					$affiliateapiurl = $set->webAddress .'/affiliate/apifeed.php?apiToken='. $db['apiToken'].'&affiliate_id='. $id  .'&fromdate=' . date("Y-m-01",strtotime("-1 Months"));
					
				}
				

				
				
				$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_7\').slideToggle(\'fast\');">'.lang('API Access').'</div>
						<div id="tab_7" style="width: 100%; background: #F8F8F8; display: none;">
						     <form id="form_api_access" action="'.$set->basepage.'" method="post">
                                                                 
                                                                    <input type="hidden" name="act" value="save_API_Access" />
                                                                    <input type="hidden" name="affiliate_id" value="'.$id.'" />
								
																	
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td align="center">'.lang('Access Type').'</td>
									<td align="center">'.lang('Affiliate\'s Static IP').'</td>
									<td align="center">'.lang('Token').'</td>
									<td align="center">'.lang('FeedUrl').'</td>
									<td align="center">'.lang('Status').'</td>
									<td align="center">'.lang('Action').'</td>
								</tr></thead><tfoot>
								<tr>
									<td align="center"><select name="apiAccessType">
                                                                        <option '.($db['apiAccessType']=='' ? " selected " : "").' value="" selected>' . lang('None') . '</option>
                                                                        <option '.($db['apiAccessType']=='accounts' ? " selected " : "").'value="accounts">' . lang('Accounts') . '</option>
                                                                        <option '.($db['apiAccessType']=='transactions' ? " selected " : "").'value="transactions">' . lang('Transactions') . '</option>
                                                                        <option '.($db['apiAccessType']=='all' ? " selected " : "").' value="all">' . lang('Accounts + Transactions') . '</option>
                                                                    </select></td>
									
									<td align="center"><input type="text" name="apiStaticIP" value="'.$db['apiStaticIP'].'" /></td>
									<td align="center"><span><input style="width: 240px;" id="apiToken" type="text" name="apiToken" value="'.$db['apiToken'].'" /></span><span>&nbsp;</span><span><button id="putDefaultText">'.lang('Generate').'</button></span></td>
									<td align="center"><textarea style="width:400px;height:50px;" type="text"  readonly >'.$affiliateapiurl .'</textarea></td>
									<td align="center">'.(empty($affiliateapiurl) || $db['apiAccessType']=='None' || $db['apiAccessType']=='' ?lang('Inactive') : lang('Active')).'</td>
									<td align="center"><input type="submit" value="'.lang('Update').'" /></td>
								</tr>
								</tfoot>
							</table>
							<div style="margin-top:20px;margin-left:15px;">* '.lang('Update empty values to delete active permission').'.</div>
						</form>
						</div>
						<br />
						';
			
				

			$set->content .='<script>
				
				function S4() {
				return (((1+Math.random())*0x10000)|0).toString(16).substring(1); 
				}

				$("#putDefaultText").click(function()	{
					guid = (S4() + S4() + "-" + S4() + "-4" + S4().substr(0,3) + "-" + S4() + "-" + S4() + S4() + S4()).toLowerCase();
							$("#apiToken").val(guid);
							return false;
						}); 
						
				</script>';

				
			$set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_8\').slideToggle(\'fast\');">'.($set->introducingBrokerInterface ? lang('Sub Introduce Broker') : lang('Sub Affiliates')).'</div>
						<div id="tab_8" style="width: 100%; background: #F8F8F8; display: none;">
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td align="center">'.($set->introducingBrokerInterface ? lang('IB ID') : lang('Affiliate ID')).'</td>
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
			
                            $qq=function_mysql_query("SELECT * FROM traffic WHERE affiliate_id='".$db['id']."' ORDER BY rdate DESC LIMIT 100",__FILE__);
                            while ($ww=mysql_fetch_assoc($qq)) {
                                    $merchantName=dbGet($ww['merchant_id'],"merchants");
                                    $listTraffic .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
                                                            <td align="left"><a href="'.$ww['refer_url'].'" target="_blank">'.$ww['refer_url'].'</a></td>
                                                            <td align="center">'.$ww['ip'].'</td>
                                                            <td align="center">'.date("d/m/y H:i", strtotime($ww['rdate'])).'</td>
                                                            <td align="center">'.$merchantName['name'].'</td>
                                                            <!--td align="center">'.$ww['visits'].'</td-->
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
									<!--td align="center">'.lang('Visits').'</td-->
								</tr></thead><tfoot>'.$listTraffic.'</tfoot>
							</table>
						</div>
						<br />
						';
						
								}
                            
                            //////////////////////////// PIXEL MONITOR begin //////////////////////////////////////////
                            include 'common/PixelMonitor.php';
                            //////////////////////////// PIXEL MONITOR end ///////////////////////////////////////////
                            
                            $set->content .= '<br />';
                            $set->content .= '<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_11\').slideToggle(\'fast\');">'.lang('Payment Details').'</div>
						<div id="tab_11" style="width: 100%; background: #F8F8F8; display: none;">';
                            
                            //////////////////////////// ACCOUNT begin //////////////////////////////////////////
                            //include 'common/Account.php';
                            include 'common/AffiliatePaymentDetails.php';
                            //////////////////////////// ACCOUNT end ////////////////////////////////////////////
                            
                            $set->content .= '</div><br />';
			}
		theme(); 
		break;
	
                
        case 'payment_save':
            //////////////////  AFFILIATE PAYMENT SAVE begin ////////////////////////////////////////////
            include 'common/AffiliatePaymentSave.php';
            //////////////////  AFFILIATE PAYMENT SAVE end //////////////////////////////////////////////
            break;
                
                
	
        
	case "save_pixel":
            for ($i = 0; $i <= count($ids); $i++) {
                $qry = "UPDATE pixel_monitor "
                        . "SET merchant_id='".$merchant_id[$i]."',method='" .$method[$i]. "', "
                        . "    pixelCode='".mysql_escape_string($pixelCode[$i])."',type='".$type[$i]."',total='".$total[$i]."' "
                        . " WHERE id='".$ids[$i]."'";
                
                if ($pixelCode[$i]) {
                    function_mysql_query($qry,__FILE__);
                } elseif ($ids[$i]) { 
                    function_mysql_query("DELETE FROM pixel_monitor WHERE id='".$ids[$i]."'",__FILE__);
                }
            }
            
            if ($db['pixelCode'])  {
                $db['pixelCode'] = str_replace("'", '"', $db['pixelCode']);
                
                $qry = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `merchant_id`, `pixelCode`,`method`, `totalFired`) VALUES
                       ('".$db['type']."',1,".$db['affiliate_id'].",".$db['merchant_id'].",'". mysql_real_escape_string($db['pixelCode'])."','". ($db['method'])."',0)";
                
                function_mysql_query($qry,__FILE__);
            }
            
            _goto($set->basepage.'?act=new&id='.$db['affiliate_id']);
            break;
	
	
	
	case "save_deal": 

	
// var_dump($_POST);
// die();
	
	
			
		//	array(13) { ["act"]=> string(9) "save_deal" ["affiliate_id"]=> string(3) "694" ["min_cpa_global"]=> string(0) "" ["cpa_global"]=> string(0) "" ["dcpa_global"]=> string(0) "" ["revenue_global"]=> string(0) "" ["cpl_global"]=> string(0) "" ["deal_merchant"]=> array(1) { [0]=> string(1) "1" } ["deal_min_cpa"]=> array(1) { [0]=> string(3) "100" } ["deal_cpa"]=> array(1) { [0]=> string(3) "400" } ["deal_dcpa"]=> array(1) { [0]=> string(0) "" } ["deal_revenue"]=> array(1) { [0]=> string(0) "" } ["deal_cpl"]=> array(1) { [0]=> string(0) "" } }
		//  array(13) { ["act"]=> string(9) "save_deal" ["affiliate_id"]=> string(3) "694" ["min_cpa_global"]=> string(0) "" ["cpa_global"]=> string(0) "" ["dcpa_global"]=> string(0) "" ["revenue_global"]=> string(0) "" ["cpl_global"]=> string(0) "" ["deal_merchant"]=> array(1) { [0]=> string(1) "1" } ["deal_min_cpa"]=> array(1) { [0]=> string(3) "100" } ["deal_cpa"]=> array(1) { [0]=> string(3) "400" } ["deal_dcpa"]=> array(1) { [0]=> string(0) "" } ["deal_revenue"]=> array(1) { [0]=> string(0) "" } ["deal_cpl"]=> array(1) { [0]=> string(0) "" } }
		//  array(13) { ["act"]=> string(9) "save_deal" ["affiliate_id"]=> string(3) "696" ["min_cpa_global"]=> string(0) "" ["cpa_global"]=> string(0) "" ["dcpa_global"]=> string(0) "" ["revenue_global"]=> string(0) "" ["cpl_global"]=> string(0) "" ["deal_type_merchant"]=> array(1) { [0]=> string(1) "1" } ["deal_min_cpa"]=> array(1) { [0]=> string(3) "249" } ["deal_cpa"]=> array(1) { [0]=> string(3) "300" } ["deal_dcpa"]=> array(1) { [0]=> string(0) "" } ["deal_revenue"]=> array(1) { [0]=> string(0) "" } ["deal_cpl"]=> array(1) { [0]=> string(0) "" } }
			
			
			if ($activeMerchants || $activeMerchants=="") {
                if ($activeMerchants=="")
					updateUnit("affiliates", "merchants = ''" , "id = '" . $affiliate_id . "'");
				else
					updateUnit("affiliates", "merchants = '" . implode("|", $activeMerchants) . "'", "id = '" . $affiliate_id . "'");
            }
            
            // The query below is probably wrong.
            //function_mysql_query("DELETE FROM " . $appDeals . " WHERE (amount < '1' OR dealType = 'tier') AND affiliate_id = '" . $affiliate_id . "'",__FILE__);

            for ($i = 0; $i < count($deal_merchant); $i++) {
                unset($min_cpa_db, $cpa_db, $dcpa_db, $revenue_db, $revenue_spread_db,$lots_db, $cpl_db, $cpc_db, $cpm_db);
				
                $chkDealMinCPA = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='min_cpa' ORDER BY rdate DESC",__FILE__));
                $chkDealCPA = mysql_fetch_assoc(function_mysql_query("SELECT id,merchant_id, amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpa' ORDER BY rdate DESC",__FILE__));
                $chkDealDCPA = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='dcpa' ORDER BY rdate DESC",__FILE__));
                $chkDealRevenue = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='revenue' ORDER BY rdate DESC",__FILE__));
                $chkDealRevenueSpread = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='revenue_spread' ORDER BY rdate DESC",__FILE__));
                $chkDealLots = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='lots' ORDER BY rdate DESC",__FILE__));
                $chkDealCPL = mysql_fetch_assoc(function_mysql_query("SELECT id,merchant_id, amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpl' ORDER BY rdate DESC",__FILE__));
                $chkDealCPC = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpc' ORDER BY rdate DESC",__FILE__));
                $chkDealCPM = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpm' ORDER BY rdate DESC",__FILE__));
                $chkDealPositionsRevShare = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='positions_rev_share' ORDER BY rdate DESC",__FILE__));
                
                // Min CPA.
                processDealType($appDeals, $chkDealMinCPA, $set, $affiliate_id, $deal_merchant[$i], 'min_cpa', $min_cpa_global, $deal_min_cpa[$i], 'deal_min_cpa');

                // CPA.
                processDealType($appDeals, $chkDealCPA, $set, $affiliate_id, $deal_merchant[$i], 'cpa', $cpa_global, $deal_cpa[$i], 'deal_cpa');

                // DCPA.
                processDealType($appDeals, $chkDealDCPA, $set, $affiliate_id, $deal_merchant[$i], 'dcpa', $dcpa_global, $deal_dcpa[$i], 'deal_dcpa');

                // Revenue.
                processDealType($appDeals, $chkDealRevenue, $set, $affiliate_id, $deal_merchant[$i], 'revenue', $revenue_global, $deal_revenue[$i], 'deal_revenue');

                // Revenue Spread.
                processDealType($appDeals, $chkDealRevenueSpread, $set, $affiliate_id, $deal_merchant[$i], 'revenue_spread', $revenue_spread_global, $deal_revenue_spread[$i], 'deal_revenue_spread');
				
				// lots.
                processDealType($appDeals, $chkDealLots, $set, $affiliate_id, $deal_merchant[$i], 'lots', $lots_global, $deal_lots[$i], 'deal_lots');

                // CPL.
                processDealType($appDeals, $chkDealCPL, $set, $affiliate_id, $deal_merchant[$i], 'cpl', $cpl_global, $deal_cpl[$i], 'deal_cpl');

                // CPC.
                processDealType($appDeals, $chkDealCPC, $set, $affiliate_id, $deal_merchant[$i], 'cpc', $cpc_global, $deal_cpc[$i], 'deal_cpc');

                // CPM.
                processDealType($appDeals, $chkDealCPM, $set, $affiliate_id, $deal_merchant[$i], 'cpm', $cpm_global, $deal_cpm[$i], 'deal_cpm');
				
				// Positions Revenue Share.
				processDealType($appDeals, $chkDealPositionsRevShare, $set, $affiliate_id, $deal_merchant[$i], 'positions_rev_share', $positions_rev_share_global, $deal_positions_rev_share[$i], 'deal_positions_rev_share');
            }
			
            _goto($set->basepage . '?act=new&id=' . $affiliate_id . '&ty=1');
            break;
	
	
	case "save_deal_tier":
            //var_dump($_POST);exit;
		function_mysql_query("DELETE FROM `affiliates_deals`  WHERE dealType <> 'tier' AND affiliate_id = '" . $affiliate_id . "'",__FILE__);
		function_mysql_query("DELETE FROM `affiliates_deals`  WHERE dealType = 'tier' AND affiliate_id = '" . $affiliate_id . "' AND tier_amount=''",__FILE__);
                
                $sql = "DELETE FROM `affiliates_deals` "
                        . "WHERE 1 = 1 AND dealType = 'tier' "
                        . "AND merchant_id = " . $deal_merchant . " "
                        . "AND affiliate_id = '" . $affiliate_id . "' "
                        . "AND tier_type <> '" . $tier_deal_type_new . "';"; // $tier_deal_type
                
                function_mysql_query($sql,__FILE__);
                
		for ($i=0; $i<count($deal_ids); $i++) {
                    unset($db);
                    
                    if ($current_tier_type[$i] != $tier_deal_type) {
                        continue;
                    }
                    
                    if (!$deal_tier_amount[$i] AND $deal_cpa[$i] <= 0) {
                        function_mysql_query("DELETE FROM `affiliates_deals`  WHERE id='".$deal_ids[$i]."'",__FILE__);
                        continue;
                    }
                    
                    $db['id'] = $deal_ids[$i];
                    $db['rdate'] = dbDate();
                    $db['admin_id'] = $set->userInfo['id'];
                    $db['affiliate_id'] = $affiliate_id;
                    $db['dealType'] = 'tier';
                    $db['tier_amount'] = str_replace(' ','',$deal_tier_amount[$i]);
                    $db['tier_pcpa'] = str_replace(' ' ,'',$deal_pcpa[$i]);
                    $db['amount'] = str_replace(' ','',$deal_cpa[$i]);
                    $db['tier_type'] = $tier_deal_type;
                    dbAdd($db, 'affiliates_deals');
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
                    $db['tier_type'] = $tier_deal_type_new;
                    dbAdd($db, 'affiliates_deals');
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
		$db['notes'] = addslashes($text);
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
		$pageTitle = lang('Affiliates List');
           $set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';     
		if ($act == "pending") {
			$search = 1;
			$where .= " AND valid='0'";
			$pageTitle = lang('Pending Affiliates');
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			}
		if ($logged) $where .= " AND logged='1'";
		updateUnit($appTable,"logged='0'","lastactive <= '".date("Y-m-d H:i:s",strtotime("-20 Minutes"))."'");
		
		if ($q AND $field) {
			if($field=='id'){
				$field='affiliates.id';
			}
		if ($field == "id") $where .= " AND lower(".$field.")='".$q."'";
				else 
				{
					if (strtolower($field)=='website') { 
						$where .= " AND (website LIKE '%".strtolower($q)."%' or website2 LIKE '%".strtolower($q)."%' or website3 LIKE '%".strtolower($q)."%'  )";
					}
					elseif (strtolower($field)=='member') { 
					   $where .= " AND affiliates.id in (SELECT affiliateID FROM  `affiliates_campaigns_relations` WHERE  `campID` LIKE  '%".strtolower($q)."%' )";
					}
					elseif (strtolower($field)=='profile') { 
					   $where .= " AND affiliates.id in (select affiliate_id as id from affiliates_profiles where url like '%".strtolower($q)."%' )";
					} else {
					   $where .= " AND lower(".$field.") LIKE '%".strtolower($q)."%'";
					}
			}
			
		}
		
		if ($userLevel=='manager') {
			$where .= " AND group_id='".$set->userInfo['group_id']."'";
		}
		else		{
			if ($group_id >= "0") $where .= " AND group_id='".$group_id."'";
		}
		
		if ($status_id >= "0") $where .= " AND status_id='".$status_id."'";
		
		$getPos = $set->itemsLimit;
		
		// var_dump($set);
		// die();
                
                $pgg = $pg * $getPos;
                $sql = '';
                
                
                $boolShowDocs          = !empty($set->showDocumentsModule);
                $intAskDocTypeCompany  = empty($set->AskDocTypeCompany) ? 0 : 1;
                $intAskDocTypeAddress  = empty($set->AskDocTypeAddress) ? 0 : 1;
                $intAskDocTypePassport = empty($set->AskDocTypePassport) ? 0 : 1;
                $intDocsToIssue        = $intAskDocTypeCompany + $intAskDocTypeAddress + $intAskDocTypePassport;
                
                if ($boolShowDocs) {
                    $sql = "SELECT affiliates.*, acr.campID, acr.affiliateID,    
                                CONCAT(
                                    (IF(0 < (SELECT COUNT(*) AS count FROM `documents` WHERE `affiliate_id` = affiliates.id AND `type` = 'Passport_Driving_Licence') , 1, 0)) + 
                                    (IF(0 < (SELECT COUNT(*) AS count FROM `documents` WHERE `affiliate_id` = affiliates.id AND `type` = 'Company_Verification') , 1, 0)) + 
                                    (IF(0 < (SELECT COUNT(*) AS count FROM `documents` WHERE `affiliate_id` = affiliates.id AND `type` = 'Address_Verification') , 1, 0))
                                    , ' / " . $intDocsToIssue . "'
                                ) AS docs_fracture    
                            FROM affiliates AS affiliates 
                            LEFT JOIN affiliates_campaigns_relations AS acr ON affiliates.id = acr.affiliateID  
                            WHERE 1 = 1 " . $where . " 
							group by affiliates.id 
                            ORDER BY affiliates.id DESC;";
                    
                } else {
                    $sql = "SELECT affiliates.*, acr.campID, acr.affiliateID FROM affiliates "
                        . "LEFT JOIN affiliates_campaigns_relations acr ON affiliates.id=acr.affiliateID "
                        . "WHERE 1 = 1 " . $where . " ".
						"group by affiliates.id "
                        . "ORDER BY affiliates.id DESC;";
                }
                
                
		$qq = function_mysql_query($sql,__FILE__); //  LIMIT $pgg,$getPos
		$query = 'SELECT extraMemberParamName AS title FROM merchants' ; // WHERE id='.aesDec($_COOKIE['mid'];
		$campID = mysql_fetch_assoc(function_mysql_query($query,__FILE__));
		
		$memberField = '';
		
			if($campID['title']){
				//if($ww['campID']){
					$memberField = $campID['title'];
					
				//////////////////////////////////////////////////////////////////////////	$affList .= '<td align="left">'.$ww['campID'].'</td>';
					
					//echo 'SELECT extraMemberParamName AS title FROM merchants WHERE merchant_id='.aesDec($_COOKIE['mid']);
					//die();
				}else{
				//////////////////////////////////////////////////////////////////////	$affList .= '<td align="left"></td>';
				}	
			//}
			
                        
		while ($ww = mysql_fetch_assoc($qq)) {
                        
                        $intShowRed    = 0;
                        $intShowYellow = 0;
                        $intShowGreen  = 0;
                        
                        if (!empty($intAskDocTypeCompany)) {
                            $sql = "SELECT doc_status AS doc_status FROM `documents`  
                                    WHERE affiliate_id = " . $ww['id'] . " AND `type` = 'Company_Verification' 
                                    ORDER BY id DESC
                                    LIMIT 0, 1;";
                            
                            $arrRes = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                            
                            if (empty($arrRes) || 'disapproved' == $arrRes['doc_status']) {
                                $intShowRed++;
                            } elseif (!empty($arrRes) && 'not_reviewed' == $arrRes['doc_status']) {
                                $intShowYellow++;
                            } elseif (!empty($arrRes) && 'approved' == $arrRes['doc_status']) {
                                $intShowGreen++;
                            }
                        }
                        
                        if (!empty($intAskDocTypeAddress)) {
                            $sql = "SELECT doc_status AS doc_status FROM `documents`  
                                    WHERE affiliate_id = " . $ww['id'] . " AND `type` = 'Address_Verification' 
                                    ORDER BY id DESC
                                    LIMIT 0, 1;";
                            
                            $arrRes = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                            
                            if (empty($arrRes) || 'disapproved' == $arrRes['doc_status']) {
                                $intShowRed++;
                            } elseif (!empty($arrRes) && 'not_reviewed' == $arrRes['doc_status']) {
                                $intShowYellow++;
                            } elseif (!empty($arrRes) && 'approved' == $arrRes['doc_status']) {
                                $intShowGreen++;
                            }
                        }
                        
                        if (!empty($intAskDocTypePassport)) {
                            $sql = "SELECT doc_status AS doc_status FROM `documents`  
                                    WHERE affiliate_id = " . $ww['id'] . " AND `type` = 'Passport_Driving_Licence' 
                                    ORDER BY id DESC
                                    LIMIT 0, 1;";
                            
                            $arrRes = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                            
                            if (empty($arrRes) || 'disapproved' == $arrRes['doc_status']) {
                                $intShowRed++;
                            } elseif (!empty($arrRes) && 'not_reviewed' == $arrRes['doc_status']) {
                                $intShowYellow++;
                            } elseif (!empty($arrRes) && 'approved' == $arrRes['doc_status']) {
                                $intShowGreen++;
                            }
                        }
                        
                        
                        
                        if (!empty($intShowRed)) {
                            $ww['doc_status_img'] = 'images/docs_red.png';
                            $ww['doc_status_alt'] = 'Some documents are missing, or some documents has been disapproved';
                        } elseif (!empty($intShowYellow)) {
                            $ww['doc_status_img'] = 'images/docs_yellow.png';
                            $ww['doc_status_alt'] = 'Issued documents has not been reviewed';
                        } else {
                            $ww['doc_status_img'] = 'images/docs_green.png';
                            $ww['doc_status_alt'] = 'All the documents has been issued and approved';
                        }
                        
                        
			$l++;
			$affList .= '<tr>
					<td>'.$ww['id'].'</td>
					
					'.($excel ? '' : '<td><a href="'.$set->basepage.'?act=new&id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="/?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank">'.lang('Login').'</a></td>').'
					<td align="left">'.$ww['username'].'</td>
					
					

					
					
					';
				


			if($campID['title']){
				if($ww['campID']){
					
					$affList .= '<td align="left">'.$ww['campID'].'</td>';
					
					//echo 'SELECT extraMemberParamName AS title FROM merchants WHERE merchant_id='.aesDec($_COOKIE['mid']);
					//die();
				}else{
					$affList .= '<td align="left"></td>';
				}	
			}
			
			
			$affList .= '<td align="center"><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
					<td align="left">'.$ww['first_name'].'</td>
					<td align="left">'.$ww['last_name'].'</td>
					'.($set->ShowIMUserOnAffiliatesList ? '<td align="left">'.$ww['IMUser'].'</td>' : ''). '
					<td align="center">'.getCountry($ww['country'],1).'</td>
					<td align="left">'.(trim($ww['website'])=='http://' ? '' : '<a href="'.addHttpIfNeeded($ww['website']).'" target="_blank">'. ($excel ? addHttpIfNeeded($ww['website']) :  charLimit($ww['website'],30)).'</a>').'</td>
					<td align="left" style="display:none">'.(trim($ww['website2'])=='http://' ? '' : '<a href="'.addHttpIfNeeded($ww['website2']).'" target="_blank">'.addHttpIfNeeded($ww['website2']).'</a>').'</td>
					<td align="left" style="display:none">'.(trim($ww['website3'])=='http://' ? '' : '<a href="'.addHttpIfNeeded($ww['website3']).'" target="_blank">'.addHttpIfNeeded($ww['website3']).'</a>').'</td>
					<td align="center">'.listGroups($ww['group_id'],1).'</td>
					<td align="center">'.listStatus($ww['status_id'],1).'</td>
					<td align="center">'.dbDate($ww['rdate']).'</td>
					<td align="center">'.dbDate($ww['lastvisit']).'</td>
					'.($excel ?  '<td align="center">'.$ww['newsletter'].'</td>' : '').'
                                        ' . ($boolShowDocs ? '<td align="center"><img title="' . ucwords(lang($ww['doc_status_alt'])) . '" alt="' . ucwords(lang($ww['doc_status_alt'])) . '" src="' . $ww['doc_status_img'] . '" style="width:15px;height:15px;" />&nbsp;' . $ww['docs_fracture'] . '</td>' : '') . '
					<td align="center">'.($excel ? ($ww['logged']=='logged_1' ? 'Yes' : 'No') : '<img border="0" src="/'.$userLevel.'/images/logged_'.$ww['logged'].'.png" alt="'.dbDate($ww['lastactive']).'" title="'.dbDate($ww['lastactive']).'" />').'</td>
					<td align="center" id="lng_'.$ww['id'].'">'.($excel ? ($ww['valid'] ? 'Yes' : 'No') : '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a>').'</td>
				</tr>';
			}
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
				$online=mysql_result(function_mysql_query("SELECT COUNT(id) FROM ".$appTable." WHERE logged='1' " . $where,__FILE__),0);
		$qry = "SELECT COUNT(id) FROM ".$appTable . " where 1=1 " . $where;
		$totalAffiliates=mysql_result(function_mysql_query($qry,__FILE__),0);
		
		$set->content = '<form method="get">
					<input type="hidden" name="search" value="1" />
					<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Search Affiliate').'</div>
					<div id="tab_1" style="width: 100%; background: #F8F8F8;">
					<table width="98%" border="0" cellpadding="0" cellspacing="5">
						<tr><td colspan="3" height="5"></td></tr>
						<tr>
							<td width="160" align="left">'.lang('Search').':</td><td><input type="text" name="q" value="'.$q.'" /></td>
							<td width="100"><select name="field" style="width: 120px;"> '.lang('In').' <option id="0" value="">'.lang('Choose Filter').'</option>'.listFields($field, $memberField).'</select></td>
							<!--td width="100"><select name="group_id" style="width: 100px;"><option value="">'.lang('All Groups').'</option><option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listGroups($group_id).'</select></td-->
							<td width="100"><select name="status_id" style="width: 100px;"><option value="">'.lang('All Status').'</option><option value="0" '.($status_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listStatus($status_id).'</select></td>
							<td align="left"><input type="submit" value="'.lang('Search').'" /></td>
							<td align="left" width="60%">
								'.lang('Total Online').': <a href="'.$set->basepage.'?act=search&logged=1"><b>'.$online.'</b></a> / '.lang('Total Affiliates').': <b>'.$totalAffiliates.'</b>
							</td>
						</tr>
					</table>
					
					
					'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
					<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
					</div><div style="clear:both"></div>' : '').'
					
					</div>
					</form>
					<hr />
					<div class="normalTableTitle">'.$pageTitle.'</div>
					<script>
						
							
							$("[name=q]").keyup(function() {
							var inputValue = $("[name=q]").val();
								
								if (isNaN(inputValue)) {
									if (inputValue == 0) {
										//$("[name=field]").val("");
									} else if (inputValue.indexOf("@") != -1) {
										$("[name=field]").val("mail");
									} else {
								//		$("[name=field]").val("");
									}
								} else if (inputValue < 1) {
									$("[name=field]").val("");
								} else {
									$("[name=field]").val("id");
								}
							});
							
					</script>';
					
					$tableStr = '<table class="tablesorter" width="100%" border="0" cellpadding="0" cellspacing="0">
						<thead>
						<tr>
							<th>'.lang('Affiliate ID').'</th>
							'.($excel ? '' : '<th>'.lang('Actions').'</th>').'
							<th align="left">'.lang('Username').'</th>
							'.($campID['title'] ? '<th align="left">'.ucwords(lang($campID['title'])).'</th>' : '').'
							<th align="center">'.lang('E-Mail').'</th>
							<th align="left">'.lang('First Name').'</th>
							<th align="left">'.lang('Last Name').'</th>
							'.($set->ShowIMUserOnAffiliatesList ? '<th align="center">'.lang('IMUser').'</th>' : ''). '
							<th align="center">'.lang('Country').'</th>
							<th align="left">'.lang('Website URL').'</th>
							<th align="left" style="display:none">'.lang('Website').' 2</th>
							<th align="left" style="display:none">'.lang('Website').' 3'.'</th>
							<th align="center">'.lang('Group').'</th>
							<th align="center">'.lang('Status').'</th>
							<th align="center">'.lang('Registration Date').'</th>
							<th align="center">'.lang('Last Visit').'</th>
							'.($excel ?  '<th align="center">'.lang('Newsletter').'</th>' : '').'
                                                        ' . ($boolShowDocs ? '<th align="center">' . lang('Docs') . '</th>' : '') . '
							<th align="center">'.lang('Logged').'</th>
							<th align="center">'.lang('Active').'</th>
						</tr></thead><tfoot></tfoot>
						<tbody>
						'.$affList.'
					</table>';
					
					excelExporter($tableStr,'Affiliate_list');
					
					$set->content.=$tableStr.'
					</div>'.getPager();
		theme();
		break;
		
	
	// --------------------------------------------------------- [ Profiles ] --------------------------------------------------------- //
	
	case "xml":
		if ($act == "pending") {
			$search = 1;
			$where .= " AND valid='0'";
			$pageTitle = 'Pending Affiliates';
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
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
		$qq=function_mysql_query($sql,__FILE__); //  LIMIT $pgg,$getPos
		
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
		$fileName = $userLevel."/csv/report.csv";
		$openFile = fopen($fileName, 'w'); 
		// fwrite($openFile, $csvContent); 
		fclose($openFile); 
		header("Expires: 0");
		header("Pragma: no-cache");
		header("Content-type: application/ofx");
		header("Content-Disposition: attachment; filename=".date('Ymd').'-'.$fileName);
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