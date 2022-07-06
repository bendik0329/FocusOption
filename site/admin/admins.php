<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto( $lout);
// if (adminPermissionCheck('admins')) _goto($lout);

//$enc = aesEnc('I\'m testing the network');

//die(aesDec($enc));


$appTable = 'admins';
$appNotes = 'admins_notes';

$theType = 	($type == "manager" ? ($set->introducingBrokerInterface ? lang('INTRODUCING BROKER') : lang('MANAGER')) : ($type=='advertiser' ? lang('Advertiser') : lang('ADMIN')));
if (!adminPermissionCheck('admins') && $type <> "manager") _goto($lout);

			
			// if ($_GET['ddd']==1)
				// die ($theType);
			
			

$switch = isset($_GET['switch']) ? $_GET['switch'] : 0;
			if ($switch==1) {

			$userid = isset($_GET['id']) ? $_GET['id'] : 0;
			if ($userid>0) {
				if($_GET['type'] == 'manager')
					$sqlpart = ",userType='default'";
				else
					$sqlpart ="";
				$qry = "UPDATE ".$appTable ." SET `level` = (CASE WHEN `level` = 'admin' THEN 'manager' WHEN `level` = 'manager' THEN 'admin' END) ". $sqlpart ."  WHERE id = ".$userid.";";
				// die ($qry);
				function_mysql_query($qry,__FILE__,__FUNCTION__);
				if($set->activateLogs){
					//activity logs
					$fields =array();
					$fields['ip'] = $set->userInfo['ip'];
					$fields['user_id'] = $set->userInfo['id'];
					$fields['theChange'] = json_encode($_GET);
					$fields['country'] = '';
					$fields['location'] = 'Admins - Switch User';
					$fields['userType'] = $set->userInfo['level'];
					$fields['_file_'] = __FILE__;
					$fields['_function_'] = 'Switch User (Admin/Manager)';
					
					$ch      = curl_init();					
					$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
					
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					
					$result = curl_exec($ch);
					curl_close($ch);
				}
			}
			}
			

switch ($act) {
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['id'] == "1") _goto($set->SSLprefix.$set->basepage);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		$password =1;
		if($db['password'] == "")
			$password = 0;
		if($set->activateLogs){
			 //activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($db);
			$fields['country'] = '';
			$fields['location'] = 'Admins - Activate/Dactivate';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'Valid Case';
			
			$ch      = curl_init();					
			$url  = 'http'. $set->SSLswitch .'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');setStatus('. $db['id'] .','. $valid .','. $password .')" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;

	/* ------------------------------------ [ Manage Languages ] ------------------------------------ */


		
		case "addAdditionalLink":
		
		
		if ($db['id'] == "1") {
			unset($password);
			$valid = 1;
			}
		
		
		{
		
			$db['id'] = $_POST['db']['id'];
			
		if ($_POST['showAdditionalLink']=="on") 
				
				{
				$db['showAdditionalLink']="1";
				
				}
else  {
				$db['showAdditionalLink']="0";
				
}
			
			
		$db['additionalLinkUrl'] = $_POST['db']['additionalLinkUrl'];
		$db['additionalLinkText'] = $_POST['db']['additionalLinkText'];

		// var_dump($_POST);
		// die();
			$q = "update admins set 	additionalLinkUrl= '" .$db['additionalLinkUrl'] . "' ,	additionalLinkText = '" . $db['additionalLinkText'] . "' , showAdditionalLink= " . $db['showAdditionalLink'] . "  where id =  " . $db['id'];
		
		// die ($q);
		
		if($set->activateLogs){
			 //activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($_POST);
			$fields['country'] = '';
			$fields['location'] = 'Admins - AddAdditionalLink';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'addAdditionalLink';
			
			$ch      = curl_init();					
			$url  = 'http'. $set->SSLswitch .'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		
		function_mysql_query($q,__FILE__,__FUNCTION__);
			
			_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$db['id'].'&ty=1');
		}
	
	case "add":
	
		if ($db['id'] == "1") {
			unset($password);
			$valid = 1;
			}
			$sql = "SELECT id FROM ".$appTable." WHERE lower(username)='".strtolower($db['username'])."' AND id != '".$db['id']."'";
		$chkUser = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
		if ($chkUser['id']) $errors['username'] = lang('Username already exist');
		if (!$db['username']) $errors['username'] = lang('Username already exist');
		if (!$db[email]) $errors['email'] = lang('E-mails not match');
		if (!$db[first_name]) $errors['first_name'] = lang('Please fill out your first name');
		if (!$db[last_name]) $errors['last_name'] = lang('Please fill out your last name');
		
		// if (!$db[relatedMerchantID] OR $db[relatedMerchantID]==-1 OR $db[relatedMerchantID]=='Choose Merchant') $errors['relatedMerchantID'] = lang('Please fill out your Merchant');
		// if (!$db[phone]) $errors['phone'] = 'Please fill out your phone';
		
		if (!empty($errors)) {
			// TODO ???
			 var_dump($errors);
			// die();
		} else {
			if (!$db['id']) $db['rdate'] = dbDate();
			$db['ip'] = $set->userIP;
			
			if(isset($db["bigPic"])){
			if (chkUpload('bigPic')) {
				$db['bigPic'] = UploadFile('bigPic','5120000','jpg,gif,png','','files/managers/');
				fixPic($db['bigPic'],'100','170','',1);
			}
			}
			
			if ($password) {
				$db['password'] = md5($password);	
			}
			
			$db['valid'] = $_POST['valid'];
			// die();
			
			 $valid  =		$db['valid'] ;
			
			if ($showAdditionalLink)
				$db['showAdditionalLink']==1;
			else
				$db['showAdditionalLink']==0;
			
			$db['group_id'] = isset($_POST['db']['group_id']) && $_POST['db']['group_id']>-2 ? $_POST['db']['group_id'] : 0 ; 
			
			unset($db['relatedMerchantID']);
			
			$db['relatedMerchantID'] = $_POST['selected_merchants'];
			
			
			$lastID = dbAdd($db,$appTable);
			if($set->activateLogs){
				
				//activity logs
				$fields =array();
				$fields['ip'] = $set->userInfo['ip'];
				$fields['user_id'] = $set->userInfo['id'];
				$fields['theChange'] = json_encode($db);
				$fields['country'] = '';
				$fields['location'] = 'Admins - Add';
				$fields['userType'] = $set->userInfo['level'];
				$fields['_file_'] = __FILE__;
				$fields['_function_'] = 'add';
				
				$ch      = curl_init();					
				$url  = 'http'. $set->SSLswitch .'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
				
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
				$result = curl_exec($ch);
				curl_close($ch);
			}
			if(empty($errors))
			_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$lastID.'&ty=1');
		}	
	case "new":
		
			// $db['group_id'] = isset($_GET['id']) && $_GET['id']>-2 ? $_GET['id'] : 0 ; 
			
			
		$isSelfManaged = false;
		function getMerchantList($db){
			global $isSelfManaged;
			$merchantsListQ = function_mysql_query('SELECT name,id,isSelfManaged FROM merchants WHERE 1=1  ORDER BY lower(name) ASC',__FILE__,__FUNCTION__);
				
			//$merchantListStr='<option id=-1>'.lang('Choose Merchant').'</option>';
			$merchantListStr='';
			while($row = mysql_fetch_assoc($merchantsListQ)){
				$selectedMerchant = '';
				if($row['id']==$db['relatedMerchantID']){
					$isSelfManaged = true;
					$selectedMerchant = 'selected';
				}
				$merchantListStr.='<option value='.$row['id'].' '.$selectedMerchant.'>'.$row['name'].'</option>';
			}
			
			return $merchantListStr;
		}
			
		if ($id) {
			
			
			
			
			if ($id == "1") _goto($set->SSLprefix.$set->basepage.'?act=list');
			$db = dbGet($id,$appTable);
			$type = $db['level'];
			$userType = $db['userType'];
			
			$merchantList = explode("|",$db['merchants']);
			$pageTitle = lang('EDIT').' '. $theType 	.' '.lang('ACCOUNT').' #'.$db['id'].' ('.$db['first_name'].' '.$db['last_name'].')';
			
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'. $set->SSLprefix .'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			$merchantListStr = getMerchantList($db);
			
			if ($note_id) $edit_note=dbGet($note_id,$appNotes);
			// Tickets List
			$noteqq=function_mysql_query("SELECT * FROM ".$appNotes." WHERE admins_id='".$id."' AND valid='1' ORDER BY id DESC",__FILE__,__FUNCTION__);
			while ($noteww=mysql_fetch_assoc($noteqq)) {
				$l++;
				$adminInfo=mysql_fetch_assoc(function_mysql_query("SELECT first_name,last_name FROM admins WHERE id='".$noteww['admin_id']."' LIMIT 1",__FILE__,__FUNCTION__));
				$ticketsList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$noteww['id'].'</td>
								<td><a href="'.$set->SSLprefix .$set->basepage.'?act=new&id='.$id.'&note_id='.$noteww['id'].'">'.lang('Edit').'</a>'.($set->userInfo['level'] == "admin" ? ' | <a href="'.$set->SSLprefix .$set->basepage.'?act=remove_note&admins_id='.$id.'&note_id='.$noteww['id'].'">'.lang('Delete').'</a>' : '').'</td>
								<td>'.dbDate($noteww['rdate']).'</td>
								<td >'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</td>
								<td align="left">'.nl2br($noteww['notes']).'</td>
							</tr>';
				}
			} else {
				$merchantListStr = getMerchantList($db);
				// $pageTitle = lang('NEW') .' '.($type == "manager" ? ($set->introducingBrokerInterface ? lang('INTRODUCING BROKER') : lang('MANAGER')) : lang(strtoupper($type)).' ' .lang('ACCOUNT'));
				$pageTitle = lang('NEW').' '.$theType.' '.lang('ACCOUNT');
				$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'. $set->SSLprefix .'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			}
		

		$set->content .= '<table><tr><td>';
			if (strtolower($type)=='manager') { 
				$set->content .= '<div class="btn"><a href="'. $set->SSLprefix .'admin/admins.php?type=manager">'.lang('Back To Managers List').'</a></div>';
			
			}
			else if (strtolower($type)=='advertiser') { 
				$set->content .= '<div class="btn"><a href="'. $set->SSLprefix .'admin/admins.php?type=advertiser">'.lang('Back To Advertisers List').'</a></div>';
			
			}
			else {
				$set->content .= '<div class="btn"><a href="'. $set->SSLprefix .'admin/admins.php">'.lang('Back To Admins List').'</a></div>';
			}
			
		if(empty($type)) $type = "admin";
		
		$set->content .= '</td>';
		if(strtolower($userType)!=='sys'){
				$set->content .='<td>';	
				
					
				if ($id) {
					if (strtolower($type)=='manager' && (adminPermissionCheck('admins')) ) { 
						$set->content .= '<div class="btn"><a href="'. $set->SSLprefix .'admin/admins.php?switch=1&type=admin&id='.$id.'">'.lang('Switch User To Admin').'</a></div>';
					
					}
					
					else {
				
					$set->content .= '<div class="btn"><a href="'. $set->SSLprefix .'admin/admins.php?switch=1&type=manager&id='.$id.'">'.lang('Switch User To Manager').'</a></div>';
						
					}
					$set->content .= '</td>';
				}	
			}
			$set->content .= '</tr></table>';
						$set->content.= '</br></br>';
		
		
		$set->content .= '<form action="'. $set->SSLprefix .'admin/admins.php?act=add" method="post" enctype="multipart/form-data">
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						<input type="hidden" name="db[level]" value="'.(strtolower($type)).'" />
						<input type="hidden" id="selected_merchants" name="selected_merchants">
						'.($ty ? '<div class="Must">- '.lang('Row Updated').' ('.dbDate().')</div><br />' : '').'
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.($type == "manager" ? ($set->introducingBrokerInterface ? lang('INTRODUCING BROKER') : lang('MANAGER')) : lang(strtoupper($type))).' '. lang('Details').'</div>
						<div id="tab_1" style="width: 68%; padding: 10px; background: #F8F8F8;float:left;">
						<table width="100%" border="0" cellpadding="0" cellspacing="0" class="float:left;">
							<tr>
								<td width="475" align="left" valign="top">
									<table width="475" border="0" cellpadding="0" cellspacing="0">
									<tr>
									<td style="font-weight:600;" colspan=2>
									'. lang('These details will be display on affiliate side.') .'
									</td>
									</tr>
									<tr>
										<td colspan="2" height="10"></td>
									</tr>
										'.($errors ? '<td colspan="2" align="left" style="color: red;"><b>'.lang('Please check one or more of the following fields').':</b><br /><ul type="*"><li />'.implode('<li />',$errors).'</ul></td>' : '').'<tr>
											<td align="left" width="110" class="blueText" '.err('username').'>'.lang('Username').':</td>
											<td align="left"><input requierd type="text" name="db[username]" value="'.$db['username'].'" style="width: 250px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="110" class="blueText" '.err('first_name').'>'.lang('First Name').':</td>
											<td align="left"><input type="text" name="db[first_name]" value="'.$db['first_name'].'" style="width: 250px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('last_name').'>'.lang('Last Name').':</td>
											<td align="left"><input type="text" name="db[last_name]" value="'.$db['last_name'].'" style="width: 250px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('password').'>'.lang('Password').':</td>
											<td align="left"><input requierd  type="password" name="password" value="" style="width: 250px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('mail').'>'.lang('E-Mail').':</td>
											<td align="left"><input type="text" name="db[email]" value="'.$db['email'].'" style="width: 250px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('phone').'>'.lang('Phone').':</td>
											<td align="left"><input type="text" name="db[phone]" value="'.$db['phone'].'" style="width: 250px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText">'.lang('IM Type').':</td>
											<td align="left">
												<select name="db[IMUserType]" style="width: 263px;">
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
										</tr>
										<tr>
											<td colspan="2" height="5"></td>
										</tr>
										<tr>
											<td align="left" class="blueText">'.lang('IM').':</td>
											<td align="left"><input type="text" name="db[IMUser]" value="'.$db['IMUser'].'" style="width: 250px;" /></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText">'.lang('Language').':</td>
											<td align="left"><select name="db[lang]" style="width: 263px;">'.listMulti($db['lang']).'</select></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											
											<td align="left" class="blueText">'.
											lang('Status') .'
											</td>
											<td align="left">
												<select name="valid">'.
													'<option value="1" '.($db['valid']=="1" ? "selected" : "" ) .'>'.lang('Active').'</option>'.
													'<option value="0" '.($db['valid']=="0" ? "selected" : "" ) .'>'.lang('Inactive').'</option>'.
													'<option value="-1" '.($db['valid']=="-1" ? "selected" : "" ) .'>'.lang('Deleted').'</option>'.
												'</select>'
											.'
											</td>
										</tr>
										'.(adminPermissionCheck('admins') ? 
										'
										<tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											
											<td align="left" class="blueText">'.
											lang('Permission Type') .'
											</td>
											<td align="left">
												<select name="db[userType]">'.
													'<option value="default" '.($db['userType']=="default" ? "selected" : "" ) .'>'.lang('Default').'</option>'.
													'<option value="restricted" '.($db['userType']=="restricted" ? "selected" : "" ) .'>'.lang('Restricted').'</option>'.
                                                                                                        '<option value="teamlead" '.($db['userType']=="teamlead" ? "selected" : "" ) .'>'.lang('Team Leader').'</option>'.
												'</select>'
											.'
											</td>
										</tr>':'').'<tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText" '.err('relatedMerchantID').'>'.lang('Merchant').':</td>
											<td align="left"><select name="db[relatedMerchantID]" id="merchant_id" style="width: 263px;" multiple="multiple">'.$merchantListStr.'</select></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr>
									</table>
								</td>
								<td width="50"></td>
								<td width="475" align="left" valign="top">
									<table width="475" border="0" cellpadding="0" cellspacing="0">
										'.($db['id'] ? '<tr>
											<td align="left" width="110" class="blueText">'.lang('Created Date').':</td>
											<td align="left" class="greenText">'.dbDate($db['rdate']).'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="110" class="blueText">'.lang('Last Login').':</td>
											<td align="left" class="greenText">'.dbDate($db['lastvisit']).'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr>' : '').'<tr>
											<td align="left" width="110" class="blueText">'.lang('Group').':</td>
											<td align="left"><select name="db[group_id]" style="width: 193px;"><option value="">'.lang('General').'</option><option value="-1" '. ($db['group_id']==-1 ? " selected " : "" ).'>'.lang('None').'</option>'.listGroups($db['group_id']).'</select></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText">'.lang('Connect From IP').':</td>
											<td align="left"><input type="text" name="db[chk_ip]" value="'.$db['chk_ip'].'" maxlength="15" style="width: 180px;" /></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td colspan="2" align="left" class="blueText">'.lang('Online Chat Code').':</td>
										</tr><tr>
											<td colspan="2" align="left"><textarea name="db[zopimChat]" cols="40" rows="6">'.$db['zopimChat'].'</textarea></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><!--tr>
											<td colspan="2" align="left" class="blueText">'.lang('Image').':</td>
										</tr><tr>
											<td colspan="2" align="left">'.fileField('bigPic',$db['bigPic']).' (100x170)</td>
										</tr-->
									</table>
								</td>
							</tr><tr>
								<td colspan="3" height="10"></td>
							</tr>
							<!--tr>
								<td align="center"><img width = "100px" height = "100px" border="0" src="'.$set->SSLprefix.$db['bigPic'].'" /></td>
							</tr-->
							<tr>
								<td colspan="3" height="10"></td>
							</tr><tr>
								<td colspan="3" align="right"><input type="submit" value="'.lang('Save').'" style="position: relative;right: 30px;"/></td>
								
							</tr></table>
						</div>
						</form>
						<div  style="width:30%;float:right;background: #F8F8F8;text-align:center;height:460px;">
						<div style="padding:10px 0;">'.(strpos($db['bigPic'],'/tmp')?"<img src='".$set->SSLprefix."images/wheel.gif'>":(isset($db['bigPic']) && !empty($db["bigPic"])?"<img src='".$set->SSLprefix. $db['bigPic'] ."' class='mainProfilePic' width='150px' style='border-radius: 50%;'>":"<img src='".$set->SSLprefix."images/profile_img.png' class='mainProfilePic' width='150px' style='border-radius: 50%;'>")).'</div>
						<div style="padding:10px 0;">
						<input type="radio" name="profileImgChoose" id="none" value="None"> '. lang("None") .'
						<input type="radio" name="profileImgChoose" id="custom" value="custom"> '. lang("Upload Custom Image") .'
						<input type="radio" name="profileImgChoose" id="avatars" value="avatars"> '. lang("Choose From Avatars") .'
						</div>
						<div class="customImage" style="padding:10px 0;display:none">
							<form id="frmB" enctype="multipart/form-data" method="POST">
							<input type="hidden" name="user_id" value='. $id .'>
							<input type="file" name="profile_img" id="profile_img"> <input type="submit" name="uploadCustomImage" id="uploadCustomImage" value="'. lang("Upload") .'"></form>
						</div>
						<div class="fromavatars" style="padding:10px 0;display:none">
						<input type="button" name="SelectFromAvatars" id="SelectFromAvatars" value="'. lang("Select from Avatars") .'">
						</div>
						</div>
						<div style="clear:both;"></div>
						
						
						<div id="myModal" class="modal">

						  <!-- Modal content -->
						  <div class="modal-content" style="width:50%">
							<div class="modal-header">
							  <span class="close">&times;</span>
							  <h2>'. lang('Select from Avatars') .  '</span></h2>
							</div>
							<div class="modal-body">
								<p class="err"></p>
							  <p>'. lang('Please click on the image you want as your profile picture:') .'</p>
							  <p class="cc-selector">
							  ';
							  
							  $dir = 'images/avatars';
							$files = scandir($dir, 0);
							for($i = 2; $i < count($files); $i++){
								$set->content .= "
								 <input id='". $files[$i] ."' type='radio' name='avatarImg' value='" .  $files[$i] . "' />
								<label class='avatarImgCls' style='background-image:url(". $set->webAddress . $set->SSLswitch . $dir . "/" . $files[$i]. ");' for='" . $files[$i] . "'></label>
								";
								if($i==6)
									$set->content.= "<br/><br/>";
							}
							  
							  $set->content .= '</p>
							  <p>
							  <input style="float:right" type="button" id="saveImage" name="saveImage" value="'. lang('Save') . '">&nbsp;&nbsp;
							  </p>
							</div>
							<!--div class="modal-footer">
							  <h3>Modal Footer</h3>
							</div-->
						  </div>

						</div>
						<style>
						.cc-selector{
							text-align:center;
						}
						.cc-selector input{
							margin:0;padding:0;
							-webkit-appearance:none;
							   -moz-appearance:none;
									appearance:none;
						}
						
						.cc-selector input:active +.avatarImgCls{opacity: .9;}
						.cc-selector input:checked +.avatarImgCls{
							-webkit-filter: none;
							   -moz-filter: none;
									filter: none;
						}
						.avatarImgCls{
							cursor:pointer;
							background-size:contain;
							background-repeat:no-repeat;
							display:inline-block;
							width:120px;height:120px;
							-webkit-transition: all 100ms ease-in;
							   -moz-transition: all 100ms ease-in;
									transition: all 100ms ease-in;
							-webkit-filter: brightness(1) grayscale(1) opacity(.7);
							   -moz-filter: brightness(1) grayscale(1) opacity(.7);
									filter: brightness(1) grayscale(1) opacity(.7);
						}
						.avatarImgCls:hover{
							-webkit-filter: brightness(1.2) grayscale(.5) opacity(.9);
							   -moz-filter: brightness(1.2) grayscale(.5) opacity(.9);
									filter: brightness(1.2) grayscale(.5) opacity(.9);
						}
						
						</style>
						<script type="text/javascript">

						//Code for displaying the modal on all reports to display/hide fields
							
							// Get the modal
						var modal = document.getElementById("myModal");

						// Get the <span> element that closes the modal
						var span = document.getElementsByClassName("close")[0];

						// When the user clicks on <span> (x), close the modal
						if(typeof span != "undefined"){
							span.onclick = function() {
								modal.style.display = "none";
							}
						}
						// When the user clicks anywhere outside of the modal, close it
						window.onclick = function(event) {
							if (event.target == modal) {
								modal.style.display = "none";
							}
						}
						
						$("#SelectFromAvatars").on("click",function(){
								modal.style.display = "flex";
						});
						</script>
						<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
						<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
						<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
						<script src="'.$set->SSLprefix.'js/ajax_file_upload.js"></script>
						<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
						<script type="text/javascript">
						$(document).ready(function(){
							
							$("#saveImage").on("click",function(){
								var profileImage = $("[name=avatarImg]:checked").val();
								
								var objParams = { 
									pic  :profileImage,
									level:"manager",
									avatar :1,
									user_id:'. $id .'
								};
								
								$.post("'.$set->SSLprefix.'ajax/UploadProfileImage.php", objParams, function(res) {
										$(".mainProfilePic").attr("src",res);
										modal.style.display = "none";
								});
			
							});
							
							$( "#frmB" )
							  .submit( function( e ) {
								$.ajax( {
								  url: "'.$set->SSLprefix.'ajax/UploadProfileImage.php",
								  type: "POST",
								  data: new FormData( this ),
								  processData: false,
								  contentType: false,
								  success:function(res){
									  if(res==2){
										$.prompt("'.lang('Wrong file uploaded. Only GIF, JPG and PNG imager are allowed for Profile Image').'", {
											top:200,
											title: "'.lang('Profile Image Upload').'",
											buttons: { "'.lang('OK').'": true}
										});
									  }
									  else{
										  $(".mainProfilePic").attr("width","auto");
										  $(".mainProfilePic").attr("src",res);
										  $(".mainProfilePic").after("<div>'. lang('System is checking for Virus. Please refresh after a minute.') .'</div>");
									  }
								  }
								} );
								e.preventDefault();
							  } );
							
					
							$("[name=profileImgChoose]").on("change",function(){
								if($(this).is(":checked") && $(this).val()== "custom"){
									$("div.customImage").show()
								}
								else{
									$("div.customImage").hide()
								}
								
								if($(this).is(":checked") && $(this).val()== "avatars"){
									$("div.fromavatars").show()
								}
								else{
									$("div.fromavatars").hide()
								}
								
								if($(this).is(":checked") && $(this).val()== "None"){
										var objParams = { 
											pic  :"",
											user_id:'. $id .'
										};
										
										$.post("'.$set->SSLprefix.'ajax/UploadProfileImage.php", objParams, function(res) {
												$(".mainProfilePic").attr("src","'.$set->SSLprefix.'images/profile_img.png");
										});
								}
									
							});
						});
						$("#merchant_id option[value=\'\']").remove();
						$("#merchant_id").multipleSelect({
									width: 200,
									placeholder: "'.lang('Select Merchant') .'"
								});
						$("#merchant_id").change(function(){
							$("#selected_merchants").val($(this).val());
						});
						var selects = "'. $db['relatedMerchantID'] .'";
						console.log(selects);
						$("#merchant_id").multipleSelect("setSelects",[  '. $db['relatedMerchantID'] .'  ]);
						$("form#frmA").submit(function() {
							this.merchant_id.disable = true;
							return true; 
						});
						
						
					</script>
						
						';
						
				$group_id = $db['group_id'];
			
			// var_dump($db);
			// die();
				if ($isSelfManaged && ($type=='advertiser'))
					$type = 'advertiser';
				
				$set->content .= ' ' . lang('Login URL for').' ' 
				.($type == "manager" ? ($set->introducingBrokerInterface ? lang('INTRODUCING BROKER') : lang('MANAGER')) : lang(strtoupper($type)))
				. ': <a href="' . ($set->isHttps?$set->webAddressHttps:$set->webAddress) . $type .'" title="' 
				. ($set->introducingBrokerInterface ? lang('Introducing Broker') :  ucwords(lang($type))) . ' ' . lang('login'). '">' . ($set->isHttps?$set->webAddressHttps:$set->webAddress) . $type .'</a>';
				
				
				if ($type!='advertiser'){
				$set->content .= '<div class="normalTableTitle">'.lang('Desk Affiliate Sign Up Link').'</div>
		<div align="left" style="border: 1px #DDDDDD solid; padding: 10px; font-family: Arial;"><a href="'.($set->isHttps?$set->webAddressHttps:$set->webAddress).'?act=new_account'.($group_id == -1 ? "" : '&group_id='.$group_id).'" target="_blank">'.($set->isHttps?$set->webAddressHttps:$set->webAddress).'?act=new_account'.($group_id == -1 ? "" : '&group_id='.$group_id).'</a><br /><span style="font-size: 11px;">Use this link to associate the affiliates directly under this group.</span></div>';
		
		
				// $set->content .= ' ' . lang('Registration to that group').': '  $set->webAddress . $type .'</a>';
				
				$set->content .= '</br></br>';
				
				
				$set->content .= '<div class="normalTableTitle">'.lang("Additional link in manger box on affiliate dashboard").'</div>
				
				<form id= "frmA" action="'. $set->SSLprefix .'admin/admins.php?act=addAdditionalLink" method="post" enctype="multipart/form-data">
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						<input type="hidden" name="db[level]" value="'.(strtolower($type)=='manager' ? 'manager' : 'admin').'" />
						
		
		<table>

<tr>
		
		<td align="left" class="blueText"><input type="checkbox" name="showAdditionalLink" '.($db['showAdditionalLink']==1 ? 'checked' : '').' /> '.lang('Show Additional Link on Affiliate Dashboard').'</td>
		</tr>
		
		<tr>
		<td align="left" class="blueText" '.err('additionalLinkText').'>'.lang('Link Text').':</td> 
		<td align="left"><input type="text" name="db[additionalLinkText]" value="'.$db['additionalLinkText'].'" style="width: 250px;" /></td>
		</tr>
		
		
		<tr>
		<td align="left" class="blueText" '.err('additionalLinkUrl').'>'.lang('Link URL').':</td> 
		<td align="left"><input type="text" name="db[additionalLinkUrl]" value="'.$db['additionalLinkUrl'].'" style="width: 250px;" /></td>
		
							</tr><tr>
		<td colspan="3" height="10"></td>
							</tr><tr>
								<td colspan="3" align="right"><input type="submit" value="'.lang('Save').'" /></td>
		</tr>
		</table>	
</form>';		
		
				$set->content .= '</br></br>';
				
				}
				

						
			if ($db['id']) $set->content .= '<form action="'.$set->SSLprefix.$set->basepage.'" method="post">
							<input type="hidden" name="act" value="add_note" />
							<input type="hidden" name="admins_id" value="'.$id.'" />
							<input type="hidden" name="note_id" value="'.$note_id.'" />
							<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_2\').slideToggle(\'fast\');">'.($theType.' '.lang('Notes')).'  '.lang('for').' #'.$db['id'].' ('.$db['first_name'].' '.$db['last_name'].')' .'</div>
							<div id="tab_2" style="width: 1000px;">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td height="10"></td>
								</tr><tr>
									<td align="left">'. ($set->introducingBrokerInterface ? lang('Account Introducing Broker Note') : lang(ucwords($type).' Note')).':</td>
								</tr><tr>
									<td height="5"></td>
								</tr><tr>
									<td align="left"><textarea name="text" cols="1" rows="1" id="notes" class="aff_textArea">'.$edit_note['notes'].'</textarea></td>
								</tr><tr>
									<td height="5"></td>
								</tr><tr>
									<td align="right"><input type="submit" value="'.lang('Save').'" /></td>
								</tr><tr>
									<td height="10"></td>
								</tr>
							</table>
						</form>
						<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
							<thead><tr>
								<td width="50">'.lang('ID').'</td>
								<td width="100">'.lang('Actions').'</td>
								<td width="100">'.lang('Last Edited').'</td>
								<td width="100" >'.lang('Added By').'</td>
								<td style="text-align: left;">'.lang('Notes').'</td>
							</tr>
							</thead><tfoot>'.$ticketsList.'</tfoot>
						</table>
						</div>
						<br />';
						
				
						
		theme();
		break;

	case "add_note":
	
		if ($note_id) $db['id'] = $note_id;
		$db['rdate'] = dbDate();
		$db['valid'] = 1;
		$db['admin_id'] = $set->userInfo['id'];
		$db['edited_by'] = $set->userInfo['id'];
		$db['admins_id'] = $admins_id;
		$db['notes'] = $text;
		
		if($set->activateLogs){
			//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($db);
			$fields['country'] = '';
			$fields['location'] = 'Admin - Add Notes';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'Add Admin Notes';
			
			$ch      = curl_init();					
			$url  = 'http'. $set->SSLswitch .'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		dbAdd($db,$appNotes);
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$admins_id);
		break;
		
	case "remove_note":
		updateUnit($appNotes,"valid='0'","id='".$_GET['note_id']."'");
		if($set->activateLogs){
		//activity logs
		$fields =array();
		$fields['ip'] = $set->userInfo['ip'];
		$fields['user_id'] = $set->userInfo['id'];
		$fields['theChange'] = json_encode($_GET);
		$fields['country'] = '';
		$fields['location'] = 'Admins - Remove Notes';
		$fields['userType'] = $set->userInfo['level'];
		$fields['_file_'] = __FILE__;
		$fields['_function_'] = 'Remove Notes';
		
		$ch      = curl_init();					
		$url  = 'http'. $set->SSLswitch .'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		curl_close($ch);
		}
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$_GET['admins_id']);
		break;
	
	case "delete":
		$sql = "update admins set valid = -1 where id=" . $id;
		mysql_query($sql);
		if($set->activateLogs){
		//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($_GET);
			$fields['country'] = '';
			$fields['location'] = 'Admins - Delete';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'Delete Affiliate';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		if($_GET["type"])
		_goto($set->SSLprefix.$set->basepage.'?type=' . $_GET["type"]);
		else
		_goto($set->SSLprefix.$set->basepage);
	default:
		updateUnit($appTable,"logged='0'","lastactive <= '".date("Y-m-d H:i:s",strtotime("-20 Minutes"))."'");
		$atype = isset($_POST['atype'])?$_POST['atype']:1;
			
			//$type = rtrim($_GET['act'],'s');
			// $pageTitle = ($set->introducingBrokerInterface ? lang('Introducing Broker List') :   lang(strtolower($type) . 's List') );
			
			
			
			
			$pageTitle = $theType.' '.lang('ACCOUNT').' #'.$db['id'].' ('.$db['first_name'].' '.$db['last_name'].')';
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'. $set->SSLprefix .'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
		if ($type == "manager") {
			
			$where .= " AND level='manager'";
		
			} else 	if ($type == "advertiser") {
			
			$where .= " AND level='advertiser'";
		
			} else {
			// $type='admin';
			
			$where .= " AND level='admin'";
			}
		
			if($atype !=""){
				
				$where .= " AND valid " . ($atype==1? ">-1" : "=" . $atype);
			}

		/* 	$pageTitle = lang(ucwords($type).'s List');
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			 */
			
		
		// $loginType =  isset($_GET['act']) ? $_GET['act'] : 'admin';
		
		
		$getPos = $set->itemsLimit;
		$pgg=$pg * $getPos;
		$sql = "SELECT * FROM ".$appTable." WHERE id > '1' ".$where." ORDER BY id DESC LIMIT $pgg,$getPos";
		$qq=function_mysql_query($sql,__FILE__,__FUNCTION__);
		$bottomNav = GetPages($appTable,"WHERE 1 ".$where,$pg,$getPos);
		
		while ($ww=mysql_fetch_assoc($qq)) {
		
	
	
		$chosegroup = listGroups($ww['group_id'],1);
		$chosegroup = empty($chosegroup) ? lang('None') : $chosegroup;
		
			$l++;
			$password  = 1;
			
			if($ww['password'] == "")
				$password = 0;
			$affList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
					<td>'.$ww['id'].'</td>';
					if($ww['valid']==1):
						if($ww['password'] == ""):
							if($set->userInfo['id'] == $ww['id']):
								$affList .='<td width="140px"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].'" style="float:left;padding:0 6px;">'.lang('Edit').'</a><span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><div class="tooltip"><a href="javascript:void(0);" oncontextmenu="return false">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please set password to login.") .'</span></div></td>';
							else:
								$affList .='<td width="140px"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].'" style="float:left;padding:0 6px;">'.lang('Edit').'</a><span style="float:left;"> | </span><a href="'. $set->SSLprefix.$set->basepage.strtolower($theType).'?act=delete&id='.$ww['id'].(isset($type)?'&type='. $type:'') .'" style="float:left;padding:0 6px;">'.lang('Delete').'</a><span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><div class="tooltip"><a href="javascript:void(0);" oncontextmenu="return false">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please set password to login.") .'</span></div></td>';
							endif;
						else:
							if($set->userInfo['id'] == $ww['id']):
								$affList .='<td width="140px"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].'" style="float:left;padding:0 6px;">'.lang('Edit').'</a><span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><a href="/'.$type.strtolower($theType).'/?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank" oncontextmenu="return false">'.lang('Login').'</a></div></td>';
							else:
								$affList .='<td width="140px"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].($type == "manager" ? "&type=manager":"").'" style="float:left;padding:0 6px;">'.lang('Edit').'</a><span style="float:left;"> | </span><a href="'.$set->SSLprefix.$set->basepage.'?act=delete&id='.$ww['id'].(isset($type)?'&type='. $type:'') .'" style="float:left;padding:0 6px;">'.lang('Delete').'</a><span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><a href="'.$set->SSLprefix.strtolower($theType).'/?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank" oncontextmenu="return false">'.lang('Login').'</a></div></td>';
							endif;
						endif;
					else:
						if($set->userInfo['id'] == $ww['id']):
							$affList .='<td width="140px"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].'" style="float:left;padding:0 6px;">'.lang('Edit').'</a><span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><div class="tooltip"><a href="javascript:void(0);" oncontextmenu="return false">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please activate the account to login.") .'</span></div></td>';
						else:
							$affList .='<td width="140px"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].'" style="float:left;padding:0 6px;">'.lang('Edit').'</a><span style="float:left;"> | </span><a href="'.$set->basepage.'?act=delete&id='.$ww['id'].(isset($type)?'&type='. $type:'') .'" style="float:left;padding:0 6px;">'.lang('Delete').'</a><span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><div class="tooltip"><a href="javascript:void(0);" oncontextmenu="return false">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please activate the account to login.") .'</span></div></td>';
						endif;
					endif;
					$affList .= '<td align="center">'.$ww['first_name'].'</td>
					<td align="center">'.$ww['last_name'].'</td>
					<td align="center">'.$ww['username'].'</td>
					<td align="center"><a href="mailto:'.$ww['email'].'">'.$ww['email'].'</a></td>
					<td align="center">'.$chosegroup.'</td>
					<td align="center">'.dbDate($ww['rdate']).'</td>
					<td align="center">'.dbDate($ww['lastactive']).'</td>
					<td align="center"><img border="0" src="'.$set->SSLprefix.'admin/images/logged_'.$ww['logged'].'.png" alt="'.dbDate($ww['lastactive']).'" title="'.dbDate($ww['lastactive']).'" /></td>
					<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');setStatus(' . $ww['id'] .','. $ww['valid'] .','. $password .')" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
				</tr>';
				
				
				$affList .='<div class="deactive_text'. $ww['id'] .'" style="display:none"><div class="tooltip"><a href="javascript:void(0);" oncontextmenu="return false">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please activate the account to login.") .'</span></div>';
				$affList .='<div class="no_password_text'. $ww['id'] .'" style="display:none"><div class="tooltip"><a href="javascript:void(0);" oncontextmenu="return false">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please set password to login.") .'</span></div>';
				$affList .='<div class="active_text'. $ww['id'] .'" style="display:none"><a href="/'.$type.strtolower($theType).'/?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank" oncontextmenu="return false">'.lang('Login').'</a></div>';

				
			}
		$set->content = '
					<div class="btn" style="float:left"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&type='.$type.'">'.lang('Add new').' ' .  (strtolower($type)=='manager'  ? strtolower(lang('Manager')) : strtolower(lang($type))).'</a></div>
					<form method="post"><div style="text-align:right;margin-right:200px;margin-bottom:20px;">'. lang('Show') .': <select name="atype" style="width:150px;" onchange="form.submit();">
					<option value="" '. ($_POST['atype']==1?'selected':'') .'>'. lang('All') .'</option>
					<option value=1 '. (!isset($_POST['atype'])?' selected':$_POST['atype']==1?' selected':'') .'>'. lang('Active') .'</option>
					<option value=-1 '. ($_POST['atype']==-1?'selected':'') .'>'. lang('Deleted') .'</option>
					</select></div></form>
					<div class="normalTableTitle">'.$pageTitle.'</div>
					<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
						<thead>
						<tr>
							<td>'.lang('ID').'</td>
							<td>'.lang('Actions').'</td>
							<td align="left">'.lang('First Name').'</td>
							<td align="left">'.lang('Last Name').'</td>
							<td align="left">'.lang('Username').'</td>
							<td align="center">'.lang('E-Mail').'</td>
							<td align="center">'.lang('Group').'</td>
							<td align="center">'.lang('Registration Date').'</td>
							<td align="center">'.lang('Last Visit').'</td>
							<td align="center">'.lang('Logged').'</td>
							<td align="center">'.lang('Active').'</td>
						</tr></thead><tfoot>'.$affList.'</tfoot>
					</table><br />
					<div align="left">'.$bottomNav.'</div>';
					$set->content .= '<style>
						.tooltip .tooltiptext {
							width: 200px !important;
							margin-left: -72px !important;
						}
						
	</style>
	<script>
	function setStatus(id, valid, password){
		if(password == 0)
		{
			$(".test" + id).html($(".no_password_text"+id).html());
		}
		else if(valid == 0)
		{
			$(".test"+id).html($(".active_text"+id).html());
		}
		else{
			$(".test"+id).html($(".deactive_text"+id).html());
		}
	}
	</script>
	';
		theme();
		break;
		
	}

?>
