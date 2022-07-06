<?php


/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
if (empty($userLevel))
	die('.');

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



$appTable = 'affiliates';
$appNotes = 'affiliates_notes';
$appDeals = 'affiliates_deals';
$appProfiles = 'affiliates_profiles';

	
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
					//below 2 line are commented by SHALINI - to fix the bug https://trello.com/c/izBOB6bf/210
				 /* 	dataval = $("."+e).data("value");
					$("."+e).val(dataval); */
                    $("."+e).css("background","#fff");
             //       console.log(e + " activate 111");
		} 
		
		function deactivate(e){
                    $("."+e).attr("readonly",true);
                    $("."+e).val("");
                    $("."+e).css("background","#e2e3e3");
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
		
		if($set->activateLogs){
		//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($_POST);
			$fields['country'] = '';
			$fields['location'] = 'Edit Affiliate';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'save_API_Access';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		
		$qry = "update affiliates set apiToken='" .$apiToken."' , apiAccessType='" .$apiAccessType . "',apiStaticIP = '" . $apiStaticIP. "' where id=" . $api_affiliate_id;	
		
		function_mysql_query($qry,__FILE__,__FUNCTION__);
		
	
	_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$api_affiliate_id.'&toggleTo=api_access');
			
	break;
	
	case "savePermission": 
	
		if ($userLevel =='admin' || ($userLevel =='manager' && $set->AllowDealChangesByManager==1)) {
				$permissionprofileId = isset($_POST['db']['permissionprofileId']) ? $_POST['db']['permissionprofileId'] : 0 ; 
				$affiliate_id = $_POST['db']['id'];
				
				
				$qry = "update affiliates set profilePermissionID='" .$permissionprofileId. "' where id=" . $affiliate_id;	
				
				function_mysql_query($qry,__FILE__,__FUNCTION__);
				if($set->activateLogs){
					//activity logs
					$fields =array();
					$fields['ip'] = $set->userInfo['ip'];
					$fields['user_id'] = $set->userInfo['id'];
					$fields['theChange'] = json_encode($_POST);
					$fields['country'] = '';
					$fields['location'] = 'Edit Affiliate';
					$fields['userType'] = $set->userInfo['level'];
					$fields['_file_'] = __FILE__;
					$fields['_function_'] = 'savePermission';
					
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
	_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$affiliate_id.'&toggleTo=permissions');
			
	break;
	
	
	case "save_qualification": 
	
	
		
		$qualify_type = isset($_POST['db']['qualify_type']) ? $_POST['db']['qualify_type'] : "" ; 
		$api_affiliate_id = isset($_POST['affiliate_id']) ? $_POST['affiliate_id'] : (isset($id) ? $id :  0) ; 
		$qualify_amount = isset($_POST['db']['qualify_amount']) ? $_POST['db']['qualify_amount'] : 0 ; 
		
		
		$qry = "update affiliates set qualify_amount='" .$qualify_amount."' , qualify_type='" .$qualify_type . "' where id=" . $api_affiliate_id;	
		// die ($qry);
		function_mysql_query($qry,__FILE__,__FUNCTION__);
		
			if($set->activateLogs){
				//activity logs
				$fields =array();
				$fields['ip'] = $set->userInfo['ip'];
				$fields['user_id'] = $set->userInfo['id'];
				$fields['theChange'] = json_encode($_POST);
				$fields['country'] = '';
				$fields['location'] = 'Edit Affiliate';
				$fields['userType'] = $set->userInfo['level'];
				$fields['_file_'] = __FILE__;
				$fields['_function_'] = 'save_qualification';
				
				$ch      = curl_init();					
				$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
				
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
				$result = curl_exec($ch);
				curl_close($ch);
			}
	
	_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$api_affiliate_id.'&toggleTo=qualified_commission');
			
	break;
	


	
	case "deletePixel":
		
		$qry = 'delete from pixel_monitor where id=' . $ids[0];
		function_mysql_query($qry,__FILE__,__FUNCTION__);
			if($set->activateLogs){
				//activity logs
				$fields =array();
				$fields['ip'] = $set->userInfo['ip'];
				$fields['user_id'] = $set->userInfo['id'];
				$fields['theChange'] = json_encode($_POST);
				$fields['country'] = '';
				$fields['location'] = 'Edit Affiliate';
				$fields['userType'] = $set->userInfo['level'];
				$fields['_file_'] = __FILE__;
				$fields['_function_'] = 'deletePixel';
				
				$ch      = curl_init();					
				$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
				
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
				$result = curl_exec($ch);
				curl_close($ch);
			}
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$id."&toggleTo=api_access");
			
	break;
	
	
	case "editPixel":
		
		
		if($pixelType == "products")
			$qry = "update pixel_monitor set product_id=" .$product_id[0]." ,method='" .$method[0]. "',pixelCode = '" . mysql_real_escape_string($pixelCode[0]) ."' , type = '" .strtolower($type[0]). "', banner_id=". $banner_id ." where id=" . $ids[0];	
		else
			$qry = "update pixel_monitor set merchant_id=" .$merchant_id[0]." ,method='" .$method[0]. "',pixelCode = '" . mysql_real_escape_string($pixelCode[0]) ."' , type = '" .strtolower($type[0]). "' , banner_id=". $banner_id ." where id=" . $ids[0];	
		
			if($set->activateLogs){
				//activity logs
				$fields =array();
				$fields['ip'] = $set->userInfo['ip'];
				$fields['user_id'] = $set->userInfo['id'];
				$fields['theChange'] = json_encode($_POST);
				$fields['country'] = '';
				$fields['location'] = 'Edit Affiliate';
				$fields['userType'] = $set->userInfo['level'];
				$fields['_file_'] = __FILE__;
				$fields['_function_'] = 'editPixel' . ($pixelType == "products"?"products":'merchants');
				
				$ch      = curl_init();					
				$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
				
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
				$result = curl_exec($ch);
				curl_close($ch);
			}
		function_mysql_query($qry,__FILE__,__FUNCTION__);
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$id."&toggleTo=api_access");
	break;

	case "testPixel":
		$pixelid = ($_POST['ids'][0]);
		
		$pxl = $pixelCode[0];
		$pxla = preg_replace("/{[-a-zA-Z0-9 _]+}/","1234",$pxl);
		$resp="";
		if (strpos($pxla,'ttp')<5) {
			$resp=doPost ($pxla); 
			// $qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`tracker`,`pixelResponse`) VALUES ('".$pixelCode[0]."',".$_REQUEST['id'].",'".$pxla."','".$resp."')";
			$qry = "INSERT INTO `pixel_logs` (`firedUrl`, `pixelCode`,`tracker`,`pixelResponse`) VALUES ('".$pixelCode[0]."',".$pixelid.",'TEST','".$resp."')";
			function_mysql_query ($qry,__FILE__);	

			if (!empty($_REQUEST['id']) && is_numeric($_REQUEST['id'])){
			$qry = "update `pixel_monitor` set totalFired =totalFired+1 where id = " . $pixelid;
			
			function_mysql_query ($qry,__FILE__);
			}
			if($set->activateLogs){
				//activity logs
				$fields =array();
				$fields['ip'] = $set->userInfo['ip'];
				$fields['user_id'] = $set->userInfo['id'];
				$fields['theChange'] = json_encode($_POST);
				$fields['country'] = '';
				$fields['location'] = 'Edit Affiliate';
				$fields['userType'] = $set->userInfo['level'];
				$fields['_file_'] = __FILE__;
				$fields['_function_'] = 'testPixel';
				
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
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$id."&toggleTo=api_access");//.'&toggleTo=tab_10#tab_10');	
	break;
	
	case "valid":
		
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		
		
		if ($db['id']>0 &&  ($valid==1)) {
			$emq = 'SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowActivated" and valid=1';

			$mailCode = mysql_fetch_assoc(function_mysql_query($emq,__FILE__,__FUNCTION__));
			sendTemplate($mailCode['mailCode'],$db['id']);
		}
		
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		
		$affiliate_id = $id;
		if($set->activateLogs){
		//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($db);
			$fields['country'] = '';
			$fields['location'] = 'Edit Affiliate';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'valid';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
		case "activate":
			$db=dbGet($id,$appTable);
			
			if ($db['valid']=='-1') $valid=1; else $valid='-1';
			
			if ($db['id']>0 &&  ($valid==1)) {
				$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowActivated" and valid=1',__FILE__,__FUNCTION__));
				sendTemplate($mailCode['mailCode'],$db['id']);
			}
			
			
			updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		
			$affiliate_id = $id;
			
			if($set->activateLogs){
				//activity logs
				$fields =array();
				$fields['ip'] = $set->userInfo['ip'];
				$fields['user_id'] = $set->userInfo['id'];
				$fields['theChange'] = json_encode($db);
				$fields['country'] = '';
				$fields['location'] = 'Edit Affiliate';
				$fields['userType'] = $set->userInfo['level'];
				$fields['_file_'] = __FILE__;
				$fields['_function_'] = 'activate';
				
				$ch      = curl_init();					
				$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
				
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				
				$result = curl_exec($ch);
				curl_close($ch);
			}
			_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$id.'&id' . $db['id']);
			die();
		break;

	/* ------------------------------------ [ Manage Languages ] ------------------------------------ */

	case "send_password":
		$getMail = dbGet($id,$appTable);
		$abc= array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"); 
		$abcBig= array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"); 
		$new_password = rand(0,9).$abc[rand(0,25)].$abc[rand(0,25)].$abcBig[rand(0,25)].rand(0,4).rand(0,9).$abcBig[rand(0,25)];
		updateUnit($appTable,"password='".md5($new_password)."'","id='".$getMail['id']."'");
	
		$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="ResetPassword" and valid=1',__FILE__,__FUNCTION__));
		
		$set->sendTo = $getMail['mail'];
		$set->subject = $getMail['first_name'].' - Password Reset';
		
		sendTemplate($mailCode['mailCode'],$id,0,$new_password);
		if($set->activateLogs){
			//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($_POST);
			$fields['country'] = '';
			$fields['location'] = 'Edit Affiliate';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'send_password';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$id.'&ty=1');
		break;
	
	case "send_mail":
		sendTemplate($mailCode,$affiliate_id);
		if($set->activateLogs){
		//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($_POST);
			$fields['country'] = '';
			$fields['location'] = 'Edit Affiliate';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'send_mail';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$affiliate_id.'&sent=1');
		break;
		
	case "delete":
		
		$dth = isset($_GET['deldth']) ? $_GET['deldth'] : "";
		$affiliate_id = isset($_GET['id']) ? $_GET['id'] : "";
		if (!empty($dth)) {
				$q = "delete from affiliates_deals where affiliate_id = ".$affiliate_id." and id = " . $dth;
				function_mysql_query($q,__FILE__,__FUNCTION__);
		}
		if($set->activateLogs){
		//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($_POST);
			$fields['country'] = '';
			$fields['location'] = 'Edit Affiliate';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'delete from affiliate deals';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
		}
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$affiliate_id);
		break;

	case "add":
		
		$user  = trim(mysql_real_escape_string(strtolower($db['username'])));
		$qq = "SELECT id,valid FROM ".$appTable." WHERE lower(username)='".$user."' AND id != '".$db['id']."'";
		// die ($qq);
		$chkUser = mysql_fetch_assoc(function_mysql_query($qq,__FILE__,__FUNCTION__));
		
		if ($chkUser['id']) $errors['username'] = lang('Username already exist');
		if (!$db['username']) $errors['username'] = lang('Username already exist');
		if (!$db['mail']) $errors['mail'] = lang('E-mails not match');
		if (!$db['first_name']) $errors['first_name'] = lang('Please fill out your first name');
		if (!$db['last_name']) $errors['last_name'] = lang('Please fill out your last name');
		if (isMustField('website') AND !$db['website']) $errors['website'] = lang('Please fill out your website');
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
			
			 _goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $idParam. '&error=' . $str_error);
			
			
		} else {
			
			$db['ip'] = $set->userIP;	
	
		$currentAff = "";
		
		/*  if ($_POST['valid']==1)
			$valid =1;
		else 
			$valid=0; */ 
		/* if ($db['valid']==1)
			$valid =1;
		else 
			$valid=0;  */
		
		//$db['valid'] = $valid;
		
		if(!empty($db['id'])){
		

		$qq = "select id,valid from affiliates where id = " . $db['id'] . " limit 1;";			
			$currentAff = mysql_fetch_assoc(function_mysql_query($qq,__FILE__,__FUNCTION__));
		


		if (!is_numeric($db['valid']) && $db['valid']!=''){
			
			if ($db['valid']=='active')
				$db['valid']=1;
			else if ($db['valid']=='inactive')
				$db['valid']=0;
			else if ($db['valid']=='deleted')
				$db['valid']=-1;
			else if ($db['valid']=='rejected')
				$db['valid']=-2;
			else
				$db['valid']=0;
		}


		
		
		/* 
		var_dump($_POST);
		echo '<br><br><Br>';
		var_dump($db);
		echo '<br><br><Br>';
		var_dump($currentAff);
		echo '<br><br><Br>';
			die ('gerger');
		 */	

			if ($currentAff['valid']==0 && $valid==1 && $db['id']>0){
				$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowActivated" and valid=1',__FILE__,__FUNCTION__));
				sendTemplate($mailCode['mailCode'],$db['id']);
			}
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
			$db['show_credit'] = $_POST['show_credit'];
			
			if ($userLevel =='admin' && isset($set->hidePendingProcessHighAmountDeposit) && $set->hidePendingProcessHighAmountDeposit==0)
			$db['pendingDepositExclude'] = $_POST['pendingDepositExclude'];
			
		/* 	if (isset($_POST['show_credit']) && 'on' == $_POST['show_credit']) {
				$db['show_credit'] = 1;
			} else {
				$db['show_credit'] = 0;
			} */
			
			if ($com_alert) {
				$db['com_alert'] = 1; 
			} else {
				$db['com_alert'] = 0;
			}
			
			
			$db['profilePermissionID'] = $set->def_profilePermissionsForAffiliate ;
			$db['qualify_type'] = $set->def_qualify_type_for_affiliates ==0 ? '' : 'default';
			
			
			$db['type']=$utype;
				
	if ($userLevel=='manager')
				$db['group_id'] = $set->userInfo['group_id'];
			
			
			$defaultGroupID_row  =  mysql_fetch_assoc(function_mysql_query("select id from groups where makedefault=1 and valid=1"));
			$defaultGroupID  =  isset($defaultGroupID_row['id']) && !empty($defaultGroupID_row['id']) ? $defaultGroupID_row['id'] : 0;
			$db['group_id'] = !empty($db['group_id'])  ? $db['group_id'] : $defaultGroupID;
			
			$db['username'] = trim($db['username']);
			$db['password'] = trim($db['password']);
			
			$lastID = dbAdd($db, $appTable);
			if(empty($currentAff)){
				$db['id'] = $lastID;
				$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="WelcomeAffiliate" and valid=1',__FILE__,__FUNCTION__));
				sendTemplate($mailCode['mailCode'],$db['id']);
			}
			
			
			if(!empty($db['id'])){
				if($_POST['changeStatus']){
						$sql = function_mysql_query("update affiliates set emailVerification=0 where id = " . $db['id']);
						
						sendTemplate('AffiliateEmailVerification',$db['id'],0,'',0,0);
				}
			}
			
			
			if(empty($currentAff) && $set->autoRelateNewAffiliateToAllMerchants==1){
				autoRelateAllMerchantsToAff($lastID);
			}
			if($set->showProductsPlace == 1 &&  $set->autoRelateNewAffiliateToAllProducts==1){
                            autoRelateAllProductsToAff($lastID);
			}
			
			if (isset($currentAff['id'])){
			chgAffiliateStatus($lastID,$db['valid'],$currentAff['valid']);
				
			
			// if ($lastGroup_id != $db['group_id']) {
			if ($currentAff['group_id'] != $db['group_id'] && $userLevel=='admin') {
				chgGroup($lastID, $db['group_id']);
			}
			
			}
			autoRelateCampToAff();
			
			if($set->activateLogs){
			//activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($_POST);
			$fields['country'] = '';
			$fields['location'] = 'Edit Affiliate';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'Add Affiliate';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
			}
			
			
			_goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $lastID . '&ty=1');
		}
	break;
	
	case "gotoAff":
		
		$affId = retrieveAffiliateId($affiliate_id);	

		_goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $affId);
	
	break;
			
	case "new":
		$set->content .='<style>
			  /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 60px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 25px;
			 width: 50px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 16px;
			  width: 16px;
			  left: 4px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
			
		</style>';
		
		
			if (isset($_GET['error']) && !empty($_GET['error'])) {
			$explodedArray = explode ('|',$_GET['error']);
			$keyName = ($explodedArray[0]);
			$errors[$keyName] = $explodedArray[1];
			$errDesc = ucwords(str_replace('%20',' ',$explodedArray[1]));
		}
		
		
		if ($id) {
			
			$db = dbGet($id, $appTable);
		$set->content .= '<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
		 <!-- jQuery UI Autocomplete css -->
		<style>
		.custom-combobox {
			position: relative;
			display: inline-block;
		  }
		  .custom-combobox-toggle {
			position: absolute;
			top: 0;
			bottom: 0;
			margin-left: -1px;
			background: white;
			border-radius: inherit;
			border-color: #CECECE;
			border-left: 0;
			color: #1F0000;
		  } 
		  .custom-combobox-input {
			margin: 0;
			padding: 5px 10px;
			width: 200px;
			background: white;
			border-radius: inherit;
			border-color: #CECECE;
			color: #1F0000;
			font-weight: inherit;
			font-size: inherit;
		  }
		  .ui-autocomplete { 
			height: 200px; 
			width:  310px;
			overflow-y: scroll; 
			overflow-x: hidden;
		  }
		</style>';
		$set->content  .= '
		<script>
		$(document).ready(function(){
			$(".btnAff").on("click",function(e){
				e.preventDefault();
				id = $(this).data("id");
				type = $(this).data("type");
				$.post("'.$set->SSLprefix.'ajax/quickAffiliateNavigation.php",{ aff_id : id,nav_type : type }, function(res) {
					if(res!=""){
						
						url="'. $set->SSLprefix . $userLevel .'/affiliates.php?act=new&id=" + res;
						window.location.href = url;
						
					}
				});
			});
		
			$("ul.vertical li a").on("click",function(){
				var tabtoopen = $(this).data("tab");
				 $(this).toggleClass("active").parent().siblings().find("a").removeClass("active");
				show_hide_tabs(tabtoopen,"li");

				if(tabtoopen == "performance" || tabtoopen == "all"){
					if($(".performance_div").html() == ""){
							
							var performanceurl = "' . $_SERVER['SERVER_HOST'] . '/common/AffiliatesPerformanceTab.php";
							$.post(performanceurl,{ id : '. $id .',rdate : "'. $db['rdate'] .'"}, function(res) {
								try {
									$(".performance_loader").hide();
									$(".performance_div").html(res);
								} catch (error) {
									console.log("\n\nException: " + error + "\n\n");
								}
							});
						}
						else{
							refreshData2();
							refreshData3();
						}
						
				}
				
			});
			
			
			
			
			function show_hide_tabs(open_tab,type){
				'. 
				($set->ShowGraphOnDashBoards==1 ||  $set->ShowGraphOnDashBoards== null  ? 
				'//reload graph on clicking any list to display in right width
				//refreshData2();
				//refreshData3();':'').'
				$(".config_tabs").hide();
				
				if(type=="search"){
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
						$(".config_tabs").hide();
						$(".config_tabs").each(function(k,tab){
							var search_txt = $(tab).find("div.normalTableTitle").text();
							
							search_txt = search_txt.toLowerCase();
							open_tab = open_tab.toLowerCase();
								console.log(search_txt + " -----" + open_tab);
							if(search_txt.search(open_tab)!==-1){
								$(this).show();
								txt = $(this).data("tab");
								$("ul.vertical li a").each(function(){
								
									if(txt == $(this).data("tab")){
										$(this).css("color","grey");
									}
								});
							}
							else{
								$(this).hide();
								txt = $(this).data("tab");
								$("ul.vertical li a").each(function(){
									if(txt == $(this).data("tab")){
										$(this).css("color","black");
									}
								});
							}
						});
					}
				}
				else{
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
					$(".config_tabs").each(function(k,tab){
						
						if($(tab).data("tab") == open_tab){
							$(this).show();
						}
						else{
						}
					});
					}
					
				}
			}

			$("#config_filter").on("keyup",function(){
				tabtoopen = $(this).val();
				if(tabtoopen == "")
				{
					$(".config_tabs").show();
					$("ul.vertical li a").css("color","black");
				}
				else{
				show_hide_tabs(tabtoopen,"search");
				}
			});


			
			// jQuery expression for case-insensitive filter
			$.extend($.expr[":"], {
				"contains-ci": function (elem, i, match, array) {
					return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
				}
			});
			
			$("#filter_form").submit(function(){
				var tab_text = $("#config_filter").val();
				show_hide_tabs(tab_text,"search");
				return false;
			});
			
			$(".affiliatesNav .trhover").on("mouseover",function(){
				$(this).siblings().show(1000);
				$("ul.vertical").css("minHeight","33vh");
				$("ul.vertical").css("height","33vh");
			});
			
			$(".main").on("mouseover",function(){
				$(".affiliatesNav tr").not(":first").hide(1000);
				$("ul.vertical").css("minHeight","53vh");
				$("ul.vertical").css("height","53vh");
			});
			 
		
		}); 
		</script>';
		$sql = "SELECT id,username,first_name,last_name FROM affiliates where  1=1  " . ($userLevel=='manager' ? " and group_id = " . $set->userInfo['group_id'] : '') ." ORDER BY id ASC";
		
		$qqAff = function_mysql_query($sql,__FILE__);
		while ($affiliateww = mysql_fetch_assoc($qqAff)) {		   
		 		$listOfAffiliates .= '<option value="'.$affiliateww['id'].'" '.(isset($id) && $affiliateww['id'] == $id ? 'selected' : '').'>['.$affiliateww['id'].'] '
					  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
		}
		
		$set->content .='<section>
		<aside>
		<div class="divAffiliatesNavigation">
		<form method="post">
		<input type="hidden" name="act" value="gotoAff">
		<table width="100%" style="padding-bottom:10px" class="affiliatesNav">
		<tr class="trhover">
		<td  class="heading_active normalTableTitle" colspan=4>'. lang('Quick Affiliates Navigation') .' </td>
		</tr>
		<tr style="display:none;overflow:scroll !important;">
		<td align="center" colspan=4 >
		<div class="ui-widget" style="margin-top:10px;margin-left:-15px">'
			. '<!-- name="affiliate_id" -->'
			. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
			. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
			. $listOfAffiliates
			.'</select>
			<span style="margin-left:25px"><input type="submit"  class="button" value="' . lang('Go') . '">
		</td>
		</tr>
		<tr style="display:none">
		<td width="11%"></td>
		<td align="left">
			<input type="button"  class="button btnAff" data-id="'. $id .'"  data-type="first" value="' . lang('First') . '">
			<input type="button" class="button btnAff" data-id="'. $id .'"  data-type="prev" value="' . lang('Previous') . '">
			</td><td align="right">
			<input type="button"  class="button btnAff" data-id="'. $id .'"  data-type="next" value="' . lang('Next') . '">
			<input type="button"  class="button btnAff" data-id="'. $id .'"  data-type="last" value="' . lang('Last') . '">		
		</td>
		<td width="15%"></td>
		</tr>
		</table>
		</form>
		</div>
		<ul class="vertical">
		
  <li class="heading_active"><span style="font-size:14px;float:left;width:80px;padding-top:8px;">'. lang('Sections') .'</span><form onsubmit="return false;" style="display:inline-flex;float:right;">
  <div class="filter">'. lang('Find').':</div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div></li>
  <li><a href="javascript:void(0)" data-tab="all">'. lang('All') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="affiliate_details" class="active">'. lang('Affiliate Details') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="performance">'. lang('Performance') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="profiles">'. lang('Profiles')  .'</a></li>
    <li><a href="javascript:void(0)" data-tab="deal_type">'. lang('Deal Type') .'</a></li>';
	
  $set->content.='<!--<li><a href="javascript:void(0)" data-tab="tier_deal">'. lang('Tier Deal') .'</a></li>-->
  <!--<li><a href="javascript:void(0)" data-tab="qualified_commission">'. lang('Qualified Commission') .'</a></li>-->
  <li><a href="javascript:void(0)" data-tab="manager_notes_crm">'. lang('Manager Notes CRM') .'</a></li>';
  if (!empty($set->showDocumentsModule) || !empty($set->showAgreementsModule) || !empty($set->showInvoiceModule)) {
  $set->content .='<li><a href="javascript:void(0)" data-tab="verification_documents">'.lang('Documents') .'</a></li>';
  }
  $set->content.='<!--<li><a href="javascript:void(0)" data-tab="agreements">'.lang('Agreements') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="invoices">'.lang('Invoices') .'</a></li>-->
  <li><a href="javascript:void(0)" data-tab="permissions">'.lang('Permissions') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="emails_monitor">'.lang('E-mails Monitor') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="api_access">'.lang('Pixel & API') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="affiliate_campaign_relation">'.lang('Related Campaigns') .'</a></li>
  <li><a href= "javascript:void(0)" data-tab="'. ($set->introducingBrokerInterface ? 'sub_introduce_broker':'sub_affiliates').'">'.  ($set->introducingBrokerInterface ? lang('Sub Introduce Broker'):lang('Sub Affiliates')) .'</a></li>
  <li><a href="javascript:void(0)" data-tab="affiliate_traffic_referral">'.lang('Affiliate Traffic Referral') .'</a></li>
  <!--<li><a href="javascript:void(0)" data-tab="pixel_monitor">'.lang('Pixel Monitor') .'</a></li>-->
  <li><a href="javascript:void(0)" data-tab="payment_details">'.lang('Payment Details') .'</a></li></ul>
  </aside>
	</section>
<div class="main">';
		
		
		
		
                        
                        
                        if (empty($db['accounts_pixel_params_replacing'])) {
                            $strAccountParamsDefault = '{"ctag":{"value":0,"caption":"Campaign Parameter"},'
                                                     . '"trader_id":{"value":0,"caption":"Trader ID"},'
                                                     . '"trader_alias":{"value":0,"caption":"Username"},'
                                                     . '"type":{"value":0,"caption":"Type of the account"},'
                                                     . '"affiliate_id":{"value":0,"caption":"Affiliate ID"},'
                                                     . '"uid":{"value":0,"caption":"Unique ID"},'
                                                     . '"dynamic_parameter":{"value":0,"caption":"Dynamic Parameter"}'
                                                     . '"dynamic_parameter2":{"value":0,"caption":"Dynamic Parameter2"}}';
                            
                            $sql = "UPDATE `affiliates` "
                                .  "SET `accounts_pixel_params_replacing` = '" . mysql_real_escape_string($strAccountParamsDefault) . "' "
                                .  "WHERE `id` = " . mysql_real_escape_string($id) . ";";
                            
                            function_mysql_query($sql,__FILE__,__FUNCTION__);
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
                                                  . '"dynamic_parameter":{"value":0,"caption":"Dynamic Parameter"}'
                                                  . '"dynamic_parameter2":{"value":0,"caption":"Dynamic Parameter2"}}';
                            
                            $sql = "UPDATE `affiliates` "
                                .  "SET `sales_pixel_params_replacing` = '" . mysql_real_escape_string($strSaleParamsDefault) . "' "
                                .  "WHERE `id` = " . mysql_real_escape_string($id) . ";";
                            
                            function_mysql_query($sql,__FILE__,__FUNCTION__);
                            unset($sql, $strSaleParamsDefault);
                        }
                        
                        
			$merchantList = explode("|",$db['merchants']);
			$productList = explode("|",$db['products']);
			
			if ($userLevel=='manager' && $db['group_id'] != $set->userInfo['group_id'])  _goto($set->SSLprefix.$userLevel.'/affiliates_list.php?act=search');
			
			$networkWhereid = '';
			$networkWheremid='';
			if ($set->isNetwork==1) {
				$networkWhereid=' AND id='.aesDec($_COOKIE['mid']) . ' ';
				$networkWheremid=' AND merchant_id='.aesDec($_COOKIE['mid']) . ' ';
			}
			$keyName = '';
			$errDesc = '';
			$errors = [];
			
			
			if(isset($toggleTo)){
				$set->content.="
					<script type='text/javascript'>
					$(document).ready(function(){
					var hash = '". $toggleTo ."';
					
					 if(hash != '')
					 {
						
						$('a[data-tab=".$toggleTo."]').click();

					 }
					 
					
					});
 					</script>";
			}
			else{
					$set->content.="
					<script type='text/javascript'>
					$(document).ready(function(){
					var hash = 'affiliate_details';
					if(hash != '')
					 {
						$('a[data-tab=\'affiliate_details\']').click();
					 }
					});
 					</script>";
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
		<li><a href="'.$set->SSLprefix.$userLevel.'/">'.lang('Dashboard').'</a></li>
		<li><a href="'.$set->SSLprefix.$userLevel.'/affiliates_list.php?act=search">'.lang('Affiliates List').'</a></li>
		<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
		<li><a style="background:none !Important;"></a></li>
	</ul>';
			
			if ($note_id) $edit_note=dbGet($note_id,$appNotes);
			// Tickets List
			$adminInfoArray = array();
			$noteqq=function_mysql_query("SELECT * FROM ".$appNotes." WHERE affiliate_id='".$id."' AND valid='1' ORDER BY id DESC",__FILE__,__FUNCTION__);
			while ($noteww=mysql_fetch_assoc($noteqq)) {
				$l++;
				if(!isset($adminInfoArray[$noteww['admin_id']])){
					$adminInfo=mysql_fetch_assoc(function_mysql_query("SELECT first_name,last_name FROM admins WHERE id='".$noteww['admin_id']."' LIMIT 1",__FILE__,__FUNCTION__));
					$adminInfoArray[$noteww['admin_id']] = $adminInfo;
				}
				else{
					$adminInfo = $adminInfoArray[$noteww['admin_id']];
				}
				$ticketsList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$noteww['id'].'</td>
								<td>' . ($userLevel=='admin' || ($userLevel=='manager' && $set->allowDeleteCRMnoteForManager==1) ? '<a onclick="editCRMNotes('. $noteww['id'] .')" href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$id.'&note_id='.$noteww['id'].'&toggleTo=manager_notes_crm">'.lang('Edit').'</a>'.( $set->userInfo['level'] == $userLevel ? ' | <a href="'.$set->basepage.'?act=remove_note&affiliate_id='.$id.'&note_id='.$noteww['id'].'">'.lang('Delete').'</a>' : '') : '' ).' </td>
								<td>'.dbDate($noteww['rdate']).'</td>
								<td>'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</td>
								<td>'.dbDate($noteww['issue_date']).'</td>
								<td>'.strtoupper($noteww['status']).'</td>
								<td align="center">'.round(floor((strtotime($noteww['closed_date'] == "0000-00-00 00:00:00" ? dbDate() : $noteww['closed_date'])-strtotime($noteww['rdate']))/(60*60*24))+1).' '.lang('Day(s)').'</td>
								<td align="left">'.nl2br($noteww['notes']).'</td>
							</tr>';
			}
			
			// List Profiles
			$profileqq=function_mysql_query("SELECT * FROM ".$appProfiles." WHERE affiliate_id='".$id."' ORDER BY id DESC",__FILE__,__FUNCTION__);
			while ($profileww=mysql_fetch_assoc($profileqq)) {
				$l++;
				$listProfiles .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$profileww['id'].'</td>
								<td align="left"><a data-name="'.$profileww['name'].'" data-source_traffic="'.$profileww['source_traffic'].'" data-description="'.$profileww['description'].'" data-url="'.$profileww['url'].'" data-id="'.$profileww['id'].'" href="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=save_profile&id='.$profileww['id'].'">'.lang('Edit').'</a></td>
								<td align="center">'.$profileww['name'].'</td>
								<td>'.$profileww['url'].'</td>
								<td>'.$profileww['description'].'</td>
								<td>'.$profileww['source_traffic'].'</td>
								<td id="profile_'.$profileww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=profile_valid&id='.$profileww['id'].'\',\'profile_'.$profileww['id'].'\');" style="cursor: pointer;">'.xvPic($profileww['valid']).'</a></td>
							</tr>';
			}
			} else {
							$set->content .= '
							<script>
							$(document).ready(function(){
								$(".config_tabs").show();	
							})
							</script>
							';
                            $pageTitle = lang('NEW AFFILIATE ACCOUNT');
							$set->breadcrumb_title =  lang($pageTitle);
							$set->pageTitle = '
							<style>
							.pageTitle{
								padding-left:0px !important;
							}
							</style>
							<ul class="breadcrumb">
								<li><a href="'.$set->SSLprefix.$userLevel.'/">'.lang('Dashboard').'</a></li>
								<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
								<li><a style="background:none !Important;"></a></li>
							</ul>';
                            /**
                             * Determine whether to show credits or not.
                             * Begin.
                             */
                            $strSql   = 'SELECT show_credit_as_default_for_new_affiliates FROM settings;';
                            $resource = function_mysql_query($strSql,__FILE__,__FUNCTION__);
                            
                            while ($arrRow = mysql_fetch_assoc($resource)) {
                                    $db['show_credit'] = $arrRow['show_credit_as_default_for_new_affiliates'] == 1 ? 1 : 0;
                            }
                            /**
                             * Determine whether to show credits or not.
                             * End.
                             */
			}
		
		$errors = [];
		if (isset($_GET['error']) && !empty($_GET['error'])) {
			$explodedArray = explode ('|',$_GET['error']);
			$keyName = ($explodedArray[0]);
			$errors[$keyName] = $explodedArray[1];
			$errDesc = ucwords(str_replace('%20',' ',$explodedArray[1]));
		}
			

			// $etqry = "SELECT id,mailCode FROM mail_templates WHERE 1=1 and " . ($id==500 ? "" :  " id>0 and "  ) . " valid='1' ORDER BY id ASC";
			
			getDefaultAffiliateID();
		$etqry = 'SELECT mt.mailCode,mt.title,l.title as langTitle FROM mail_templates mt left join languages l on mt.language_id = l.id WHERE  ' . ($id==$set->defaultAffiliateID ? "" :  " mt.id>0 and "  ) . ' mt.valid=1 order by mt.id desc';
// echo 		($etqry).'<br>';
		$tempqq=function_mysql_query($etqry,__FILE__,__FUNCTION__);
		while ($tempww=mysql_fetch_assoc($tempqq)) {
			
			$allTemplates .= '<option value="'.$tempww['mailCode'].'" '.($tempww['mailCode'] == $mailCode ? 'selected' : '').'>'.$tempww['mailCode'].' - ' . $tempww['langTitle'].'</option>';
		}

		// die($allTemplates);
		
		if ($sent) {
			$set->content .= '
			<script type="text/javascript">
				window.onload = function() {
					alert(\''.lang('The E-mail has sent to affiliate').'\');
					}
					
					$(document).ready(function(){
						$( ".confirm" ).dialog({
							  resizable: false,
							  height:140,
							  modal: true,
							  buttons: {
								"Delete all items": function() {
								  $( this ).dialog( "close" );
								},
								Cancel: function() {
								  $( this ).dialog( "close" );
								}
							  }
							});
					});
			</script>
			';
			}
			
		if ($db['id']) {
			$affiliateqq=function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__,__FUNCTION__);
				while ($affiliateww=mysql_fetch_assoc($affiliateqq)) {
					if($_GET['id'] == $affiliateww['id']) continue;
					$allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($db['refer_id'] == $affiliateww['id'] ? 'selected' : '').'>['.$affiliateww['id'].'] '.$affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
				}
			}
			
			
			
			/* 
			// var_dump($valid);
			var_dump($_POST);
			echo '<br>';
			echo '<br>';
			echo '<br>';
			var_dump($valid);
			echo '<br>';
			echo '<br>';
			echo '<br>';
			var_dump($db);
			echo '<br>';
			echo '<br>';
			echo '<br>';
			
			die ('greger'); */
			if($db['valid']=="1" || $valid=="1")
				$selectedvalid ="selected";
			else
				$selectedvalid ="";
			
			
			if($db['valid']=="0" || $valid=="0")
				$selectedinvalid ="selected";
			else
				$selectedinvalid ="";
			
		$set->content .='<script>
		
		function confirmAndSubmit(){
					
			
					if($("#mail").val() != $("#email").val() && $("#emailVerification").val() ==1){
							$.prompt("'.lang('Would you like to request another email verification?').'", {
								top:200,
								title: "'. lang('Edit Affiliate') .'",
								buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
								submit: function(e,v,m,f){
									if(v){
										$("#changeStatus").val(1);
										
										$("#frmEditAff").submit();
									}
									else{
										//
									}
								}
							});
							//return false;
							
				}
				else{
					$("#changeStatus").val(0);
										
									$("#frmEditAff").submit();
				}
			}

		
		
		$(document).ready(function(){
				
		$("#frmAddAff").on("submit",function(e) {
	
	var check_ids = new Array("username","password","first_name","last_name","mail");
	var names = new Array("'.lang('Username').'","'.lang('Password').'","'.lang('First Name').'","'.lang('Last Name').'","'.lang('E-mail').'");
	var check_ids_length = check_ids.length;
	for (i = 0; i < check_ids_length; i++) {
		key = $("#"+check_ids[i]);
		if (key.val() == "") {
			//alert("Please fill out "+names[i]);
			  $.fancybox({ 
			 closeBtn:false, 
			  minWidth:"250", 
			  minHeight:"180", 
			  autoCenter: true, 
			  afterClose:function(){
				  key.focus();
			  },			  
			  content: "<h1><div style=\'float:left;\'><img src=\''.$set->SSLprefix.'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Please fill in&nbsp;'). '" + names[i] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
			  });
			  key.focus();
			return false;
		}
		else{
			
					val =key.val(); 
					//if (val.match(/^[\w@].+$/) == null)
					if(/^[-a-zA-Z0-9@+._:// \u00C0-\u1FFF\u2C00-\uD7FF]+$/.test(val) == false)
					{
						msg = "";
						if(val[0] == ".")
							msg = "'. lang('Username should start with \'@\' or any alphanumeric character') .'";
						else
							msg = "'. lang('Special characters are not allowed.') .'";
							$.fancybox({ 
					 closeBtn:false, 
					  minWidth:"250", 
					  minHeight:"180", 
					  autoCenter: true, 
					  afterClose:function(){
						  key.focus();
					  },			  
					  content: "<h1><div style=\'float:left;\'><img src=\''.$set->SSLprefix.'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +msg + " "+ names[i] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
					  });
					  key.focus();
					return false;
				}
			 
		}
	}

	var email = $("#mail");
	if (!(pattern.test(email.val()))) {
		//alert("Your e-mail is not valid");
		 $.fancybox({ 
		 closeBtn:false, 
		  minWidth:"250", 
			  minHeight:"180", 
		  autoCenter: true, 
		  afterClose:function(){
			  email.focus();
		  },			  
		  content: "<h1><div style=\'float:left;\'><img src=\''.$set->SSLprefix.'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Your e-mail is not valid.').'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' value=\''.lang('Continue').'\'  onClick=\'$.fancybox.close()\'></div></div>" 
		  });
		
		return false;
		}
		
	
		$(this).submit();
	});
	});';
	
	if(isset($id)){
	$set->content.='
	//approve manually
	function approveManual(id){
			$.post("'.$set->SSLprefix.'ajax/varifyUserManual.php", { affiliate_id : '. $id .' }, function(res) {
				if(res == "success"){
					window.location.href=window.location.href;
				}
			});
	}';
	}	
	$set->content.='</script>';
	
				
		$set->content .= '<div id="affiliate_details" data-tab="affiliate_details" class="config_tabs" style="padding-bottom:20px;display:none;">';
					
						$set->content .='<div class="normalTableTitle" style="cursor: pointer;">'.lang('Affiliate Details').'</div>
						
						<div id="tab_1"  style="width: 100%; padding: 10px; background: #F8F8F8;">
						<form action="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=add" method="post" '. (!isset($db['id'])? 'id="frmAddAff"' : 'id="frmEditAff"') .'>
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						<input type="hidden" name="lastGroup_id" value="'.$db['group_id'].'" />
						'.($ty ? '<div class="Confirm">- '.lang('The page is up to date').' ('.dbDate().')</div><br />' : '').'
						<table width="98%" border="0" cellpadding="0" cellspacing="0" class="tblDetails">
							<tr>
								<td width="48%" align="left" valign="top">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">
										'.($errors ? '<td colspan="3" align="left" style="color: red;"><b>'.lang('Please check one or more of the following fields').':</b><br /><ul type="*"><li />'.ucwords($keyName). '  -  '.$errDesc. '</ul></td>' : '').'
										<tr>
											<td colspan=2 align="left" class="blueText" '.err('valid').'>
											<div style="float:left;padding-top:5px;padding-right:10px;min-width:113px">'.lang('Account Status').'</div></td>';
											
											
											$statusTxt = "value='" . $db['valid'] ."'";
																				
											// $statusTxt = "value='" . lang('Active') ."'";
											$ulStatus = "User Status";
											if($db['valid'] == 0){
												// $statusTxt = "value='" . lang('Inactive') ."'";
												$ulStatus = lang('Inactive');
											}
											else if($db['valid'] == -1){
												// $statusTxt = "value='" . lang('Deleted') ."'";
												$ulStatus = lang('Deleted');
											}
											else if($db['valid'] == -2){
												// $statusTxt = "value='" . lang('Rejected') ."'";
												$ulStatus = lang('Rejected');
											}
											else{
												$ulStatus = lang('Active');
											}
											$set->content .='<td align="left">
												<div id="dd" class="wrapper-dropdown-3" tabindex="1">
													<span>'. $ulStatus .'</span>
													<ul class="dropdown_new">
														<li><a href="#" data-valid = 1>'. lang('Active') .'</a></li>
														<li><a href="#" data-valid = 0>'. lang('Inactive') .'</a></li>
														<li><a href="#" data-valid = -2>'. lang('Rejected') .'</a></li>
													</ul>
												</div>
												';
												
												
												
										$set->content .='<input type="hidden" id="db_valid" name="db[valid]" '. $statusTxt .' />';
					
				
$set->content .= '				
											<!--label class="switch"><input type="checkbox" name="valid" value=1 '.($db['valid'] ? 'checked' : '').' ><div class="slider round"></div></label-->
											
											
											</td>';
											include ('common/affiliate_status.php');
											$set->content .='
											
											<!--<td align="left" class="blueText">
											
											<input colspan=2  type="radio" name="db[valid]" '.($db['valid'] ? 'value=1 checked' : 'value=1').' />'.lang('Active') .'&nbsp;&nbsp;
											<input type="radio" name="db[valid]" '.($db['valid']==0? 'value=0 checked' : 'value=0').' />'.lang('Inactive').'</td>-->
											
										</tr>
										<tr>
											<td colspan="3" height="20"></td>
										</tr>
										
										<tr>
											<td colspan=2 align="left" width="200" class="blueText" '.err('username').'>'.lang('Username').':</td>
											<td align="left"><input type="text" name="db[username]"  id="username" value="'.$db['username'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr>
										<tr>
											<td colspan="3" height="5"></td>
										</tr><tr>
											<td colspan=2 align="left" class="blueText" '.err('password').'>'.lang('Password').':</td>
											<td align="left"><input type="password" id="password" name="password" placeholder="***********"  value="" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="3" height="5"></td>
										</tr><tr>
											<td colspan=2  align="left" width="110" class="blueText" '.err('first_name').'>'.lang('First Name').':</td>
											<td align="left"><input type="text" name="db[first_name]" id="first_name" value="'.$db['first_name'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="3" height="5"></td>
										</tr><tr>
											<td colspan=2  align="left" class="blueText" '.err('last_name').'>'.lang('Last Name').':</td>
											<td align="left"><input type="text" name="db[last_name]" id="last_name" value="'.$db['last_name'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="3" height="5"></td>
										</tr><tr>';
										
										if($userLevel=='admin' || ($userLevel=='manager' && !$set->isNetwork)){
										$set->content .='
											<td colspan=2 align="left" class="blueText" '.err('mail').'>'.lang('E-Mail').':</td>
											<input type="hidden" name="email" id="email" value="'.$db['mail'].'">
											<td align="left"><input type="text" name="db[mail]" id="mail" value="'.$db['mail'].'" style="width: 280px;" /> <span class="required">*</span></td>
										</tr><tr>
											<td colspan="3" height="5"></td>
										</tr><tr>
											<td colspan=2  align="left" class="blueText" '.err('phone').'>'.lang('Phone').':</td>
											<td align="left"><input type="text" name="db[phone]" value="'.$db['phone'].'" style="width: 280px;" />'.(isMustField('phone') ? ' <span class="required">*</span>' : '').'</td>
										</tr><tr>';
										}
	$set->content .='
											<td colspan="3" height="5"></td>
										</tr><tr>
											<td colspan=2  align="left" class="blueText" '.err('country').'>'.lang('Country') .':</td>
											<td align="left"><select name="db[country]" style="width: 292px;"><option value="">'.lang('Choose Your Country').'</option>'.getCountry($db['country']).'</select>'.(isMustField('country') ? ' <span class="required">*</span>' : '').'</td>
										</tr><tr>
											<td colspan="3" height="5"></td>
										</tr><tr>
											<td colspan=2 align="left" class="blueText" '.err('website').'>'.lang('Website URL').':</td>
											<td align="left"><input type="text" name="db[website]" value="'.$db['website'].'" style="width: 280px;" />'.(isMustField('website') ? ' <span class="required">*</span>' : '').'</td>
										</tr><tr>
										<td colspan="3" height="5"></td>
										</tr>
										
										
										' .
										($db['website2'] != '' && $db['website2'] != 'http'.$set->SSLswitch .'://' 
											? 
											' <tr>  <td colspan=2  align="left" class="blueText" '.err('website2').'>Website URL2:</td>
										        <td align="left"><input type="text" name="db[website2]" value="'.$db['website2'].'" style="width: 280px;" /></td>
										    </tr>
											<tr>
										        <td colspan="3" height="5"></td>
										    </tr>
											'
										    : '') . ' ' 
											
										. ($db['website3'] != '' && $db['website3'] != 'http'.$set->SSLswitch.'://' ? 
											'<tr>
											     <td colspan=2  align="left" class="blueText" '.err('website3').'>Website URL3:</td>
												 <td align="left"><input type="text" name="db[website3]" value="'.$db['website3'].'" style="width: 280px;" /></td>
										    </tr>' : '')

										
										
										. '<tr> <!-- NEW -->'
											. '<td colspan="3" height="5"></td>
										</tr><tr>
';
										
										if($userLevel=='admin' || ($userLevel=='manager' && !$set->isNetwork)){
										$set->content .='
											<td colspan=2 align="left" class="blueText">'.lang('I.M. Type').':</td>
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
											<td colspan="3" height="5"></td>
										</tr><tr>
											<td colspan=2  align="left" class="blueText">'.lang('I.M').':</td>
											<td align="left"><input type="text" name="db[IMUser]" value="'.$db['IMUser'].'" style="width: 280px;" /></td>
										</tr><tr>';
										}
										$set->content .='
											<td colspan="3" height="5"></td>
										</tr>'.($set->multi ? '<tr>
											<td colspan=2 align="left" class="blueText">'.lang('Language').':</td>
											<td align="left"><select name="db[lang]">'.listMulti($db['lang']).'</select></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr>' : '').'<tr>

										
											<td align="left" class="blueText">'.lang('Credit Amount').':</td>
											<td width="5px">'. $set->currency .'</td>
											<td align="left"><input type="text" name="db[credit]" value="'.$db['credit'].'" style="width: 100px; text-align: left;" /></td>
										</tr><tr>
											<td colspan="2" height="5"></td>'

											. (($set->AllowDealChangesByManager==1  && $userLevel =='manager') ||  $userLevel =='admin' ? 
											('
										</tr>'.(!$set->hideSubAffiliation?( $set->introducingBrokerInterface ? '' : '<tr>
											<td align="left" class="blueText">'.lang('Sub Affiliate Commission').':</td>
											<td width="5px">%</td>
											<td align="left"><input type="text" name="db[sub_com]" value="'.$db['sub_com'].'" style="width: 100px; text-align: left;" /></td>
										</tr>'):'').'<tr>
											<td colspan="2" height="5"></td>
										</tr>' ): '' ) 
											.'
										<tr>
											<td colspan="2" height="5"></td>
										</tr><!--tr>
											<td></td>
											<td align="left" class="blueText"><input type="checkbox" name="com_alert" '.($db['com_alert'] ? 'checked' : '').' /> '.lang('Stop Commission Alerts').'</td>
										</tr-->';

										$set->content.='<tr>
										<td colspan="2" height="5"></td>
										</tr>
									</table>
								</td>
								<td width="4%"></td>
								<td width="48%" align="left" valign="top">
									<table width="100%" border="0" cellpadding="0" cellspacing="0">';
									if($id){
										$set->content.='<tr>';
										if($db['valid'] == 1):
											$set->content.='<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a href="/?act=login&username='.$db['username'].'&password='.$db['password'].'&admin='.$set->userInfo['id'].'" target="_blank" oncontextmenu="return false">'.lang('Login as affiliate').'</a></td>';
										else:
											$set->content.='<td colspan="2" align="left" class="tooltip"><div class="exportCSV" style="margin: 0;"><a href="javascript:void(0);" target="_blank" oncontextmenu="return false">'.lang('Login as affiliate').'</a></div><span class="tooltiptext">'. lang("Please activate the account to login.") .'</span></td>';
										endif;
										$set->content.='</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a href="javascript:void(0);" onclick="confirmation(\''.lang('Are you sure you want to reset the pasword?').'\',\''.$set->basepage.'?act=send_password&id='.$db['id'].'\');">'.lang('Reset Password').'</a></div></td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a href="mailto:'.$db['mail'].'">'.lang('Send an e-mail').'</a></div></td>
										</tr><tr>
										  <td colspan="2" height="5"></td>
										</tr><tr>
											<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a href="'.$set->SSLprefix.$set->basepage.'../../tickets.php?act=new&affiliate_id='.$db['id']  .'">'.lang('Open a ticket').'</a></div></td>

										</tr><tr>
											<td colspan="2" height="5"></td>
											
										</tr>';
										
										if($id!="" && ($id != $set->defaultAffiliateID || $db['id']!=$set->defaultAffiliateID)){
										$set->content.='<tr>
											<td colspan="2" align="left"><div class="exportCSV" style="margin: 0;"><a id = "confirm" href="javascript:void(0);" style="cursor: pointer;" data-type="'.  ($db['valid']=='-1'?lang('reactivate'):lang('delete')) .'">' . ($db['valid']=='-1'?lang('Reactivate Account'):lang('Delete Account')).'</a></div></td>

										</tr>';
										}
										$set->content.='<tr><td colspan="2" height="5"></td></tr>';
										
									}
									$set->content.='<tr>
											<td align="left" width="160" height="20" class="blueText">'.lang('Block New Traffic').':</td>
											<td><label class="switch"><input type="checkbox" name="blockNewTraffic" class="blockNewTraffic"  value=1 '.($db['blockNewTraffic'] ? 'checked' : '').' ><div class="slider round"></div></label></td>
										</tr>
										<script>
										$(document).ready(function(){
											$(".blockNewTraffic").on("click",function(){
												if($(this).is(":checked")){
													$.prompt("'.lang('Are you sure you want to block new traffic for this affiliate?').'", {
														top:200,
														title: "'. lang("Block New Traffic") .'",
														buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
														submit: function(e,v,m,f){
															if(v){
																$.get("'.$set->SSLprefix.'ajax/blockTrafficForAffiliate.php?blockTraffic=1&id='. $db['id'] .'",function(){
																		//console.log(res);
																});
															}
															else{
																//
															}
														}
													});
												}
												else{
													$.get("'.$set->SSLprefix.'ajax/blockTrafficForAffiliate.php?blockTraffic=0&id='. $db['id'] .'",function(){
														console.log(res);
													});
												}
											});
										});
										</script>
										';
										$set->content.='<tr>
											<td align="left" width="160" height="20" class="blueText">'.lang('Sign up Date').':</td>
											<td align="left" class="greenText">'.dbDate($db['rdate']).'</td>
										</tr><tr>
											<td colspan="2" height="5"></td>
										</tr>
										<tr>
											<td align="left" width="160" height="20" class="blueText">'.lang('Sign up Referral URL').':</td>
											<td align="left"><a href="'.$db['regReferUrl'].'" target="_blank">'.$db['regReferUrl'].'</a></td>
											</tr>
										<tr>
											<td align="left" width="160" height="20" class="blueText">'.lang('Registered IP').':</td>
											<td align="left"><a href="'.$db['ip'].'" target="_blank">'.$db['ip'].'</a></td>
										</tr>
										<tr>
											<td colspan="2" height="5"></td>
										</tr>
										'. ($set->BlockLoginUntillEmailVerification && $id?'<tr>
											<td align="left" width="160" height="20" class="blueText">'.lang('Email Verification').':</td>
											<td align="left" class="greenText">'.($db['emailVerification']==1?'<span style="color:green">'.lang("True").'</span>':'<a href="javascript:void(0)" class="verifyEmail"><span style="color:red">'.lang('False').'</span></a>').($db['emailVerification']!=1?'<a style="padding-left:20px;" onclick="approveManual('. $id .')" href="javascript:void(0)">'.  lang("Approve Manually") .'</a>':'').'</td>
										</tr>
										<tr>
											<td colspan="2" height="5"></td>
										</tr>':'').'
										<tr>
											<td align="left" width="160" height="20" class="blueText">'.lang('Last Login').':</td>
											<td align="left" class="greenText">'.$lastvisit.'</td>
										</tr>
										<tr>
											<td colspan="2" height="5"></td>
										</tr>
										<tr>
											<td align="left" class="blueText">
											<div style="float:left;padding-top:5px;padding-right:10px;min-width:100px">'.lang('Asked for Newsletter').'</div></td>
											<td><label class="switch"><input type="checkbox" name="newsletter" class="newsletter"  value=1 '.($db['newsletter'] ? 'checked' : '').' ><div class="slider round"></div></label></td>
										</tr>
										<tr>
											<td colspan="2" height="5"></td></tr>
										<tr>
											<td align="left" class="blueText">
											<div style="float:left;padding-top:5px;padding-right:10px;min-width:113px">'.lang('Show credit').'</div></td>
											<td><label class="switch"><input type="checkbox" name="show_credit" class="show_credit"  value=1 '.($db['show_credit'] ? 'checked' : '').' ><div class="slider round"></div></label></td>
										</tr>

										<tr>
											<td colspan="2" height="5"></td></tr>
										'.($userLevel =='admin' &&  isset($set->hidePendingProcessHighAmountDeposit) && $set->hidePendingProcessHighAmountDeposit==0 ? '
										<tr>
											<td align="left" class="blueText">
											<div style="float:left;padding-top:5px;padding-right:10px;min-width:113px">'.lang('Exclude Pending Deposit').'</div></td>
											<td><label class="switch"><input type="checkbox" name="pendingDepositExclude" class="pendingDepositExclude"  value=1 '.($db['pendingDepositExclude'] ? 'checked' : '').' ><div class="slider round"></div></label></td>
										</tr>' : '' ).'
										<tr>
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
											<td align="left" width="160" class="blueText">'.lang('Type').':</td>
											<td align="left"><select name="utype" style="width: 242px;"><option value="Affiliate" '. ($db['type'] == 'Affiliate'?'selected':'') .'>'.lang('Affiliate').'</option>
											<option value="IB" '. ($db['type'] == 'IB'?'selected':'') .'>'.lang('IB').'</option>
											<option value="WhileLabel" '. ($db['type'] == 'WhiteLabel'?'selected':'') .'>'.lang('WhiteLabel').'</option>
											<option value="PortfolioManager" '. ($db['type'] == 'PortfolioManager' ? 'selected':'') .'>'.lang('Portfolio Manager').'</option>
											</select></td>
										</tr>
										<td colspan="2" height="5"></td>
										'.
										($set->isBasicVer?'':
										'<tr>
											<td align="left" width="160" class="blueText">'.lang('Category').':</td>
											<td align="left"><select name="db[status_id]" style="width: 242px;"><option value="0">'.lang('General').'</option>'.listStatus($db['status_id']).'</select>&nbsp;&nbsp;<a href="'.$userLevel.'/affiliatesStatus.php" target="_blank" class="blueText" style="text-decoration:underline;">'. lang("Add New") .'</a></td>
										</tr><tr>
										<td colspan="2" height="5"></td>
										</tr>
										'.($userLevel=='admin' ? '
										<tr>
											<td align="left" width="160" class="blueText">'.lang('Group').':</td>
											<td align="left"><select name="db[group_id]" style="width: 242px;"><option value="0">'.lang('General').'</option>'.listGroups($db['group_id']).'</select>&nbsp;&nbsp;<a href="admin/groups.php" target="_blank" class="blueText" style="text-decoration:underline;">'. lang("Add New") .'</a></td>
											
										</tr>': '' ));
										
										$set->content .= ($db['id'] ? '<tr>
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" width="160" class="blueText">'.lang('Auto E-Mail').':</td>
											<td align="left">
												<select style="width: 242px;" onchange="confirmation(\''.lang('Are you sure you want to send this mail?').'\',\''.$set->SSLprefix.$set->basepage.'?act=send_mail&affiliate_id='.$db['id'].'&mailCode=\'+this.value);">
													<option value="">'.lang('Select E-Mail Template').'</option>
													'.$allTemplates.'
												</select>&nbsp;&nbsp;<a href="'.$set->SSLprefix.$userLevel.'/mails.php" target="_blank" class="blueText" style="text-decoration:underline;">'. lang("Add New") .'</a>
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
										</tr>' : '');
										$set->content .= '<tr>
										
											<td colspan="2" height="5"></td>
										</tr><tr>
											<td align="left" class="blueText">'.lang('Manager Private Note').':</td>
											<td align="left"><textarea  cols="80" rows="10" name="db[manager_private_note]"  style="width:97%;height:100px; text-align: left;">'.$db['manager_private_note'].'</textarea></td>
										
										</tr>
										<tr>
											<td colspan="2" height="10"></td>
										</tr><tr>
											<td colspan="2" align="right"><input'. (!isset($db['id'])?' type="submit"':' onclick = "confirmAndSubmit()" type="button"') .' name="aff_submit" id="aff_submit" value="'.lang('Save').'" /></td>
										</tr>
										</table>';
									
								$set->content .='</td>
							</tr></table>
							<input type="hidden" name="email" id="emailVerification" value="'.$db['emailVerification'].'">
							<input type="hidden" name="changeStatus" id="changeStatus">
							</form>
						</div>
						</div>';
			
			/************* PERFORMANCE******************/ 
			
			if($id){
					
								$set->content .= '<div id="performance" data-tab="performance" class="config_tabs" style="padding-bottom:20px;display:none;">
						<div class="normalTableTitle" style="cursor: pointer;">'.lang('Performance').'</div>
							
							';
								$set->content .='  <link rel="stylesheet" href="'.$set->SSLprefix.'js/unslider/dist/css/unslider.css">
							<!--link rel="stylesheet" href="'.$set->SSLprefix.'js/unslider/dist/css/unslider-dots.css"-->
							<script src="'.$set->SSLprefix.'js/unslider/dist/js/unslider.js"></script>';
									
								$set->content .="<div style='padding:30px;text-align:center;' class='performance_loader'><img src='".$set->SSLprefix."images/ajax-loader_big.gif'></div><div class='performance_div'></div></div>";
								
									$set->content .= '
									<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
									<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
									<!--script src="'.$set->SSLprefix.'js/easytabs/jquery.hashchange.min.js" type="text/javascript"></script-->
									<script src="'.$set->SSLprefix.'js/easytabs/jquery.easytabs.min.js" type="text/javascript"></script>
									<script type="text/javascript">
									$(document).ready(function(){
										$("#confirm").on("click",function(){
											var type = $(this).data("type");
											
											$.prompt("'.lang('Are you sure you want to').' "+ type +" '. lang('this account?').'", {
												top:200,
												title: "Are you Ready?",
												buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
												submit: function(e,v,m,f){
													if(v){
														var url = "'.$set->SSLprefix.$userLevel .'/affiliates.php?act=activate&id='. $db['id'] .'";
														window.location.href= url;
													}
													else{
														//
													}
												}
											});
										});
										
										i=0;
										
										$("#form_tier_deal_type tfoot tr").each(function(){
												if(i==0){
													if($(this).data("existing_tier_deals")){
													
														$("#tab-container").find("a[href=\'#tabs1-tierDeals\']").click();
														$("#tab-container").easytabs({
															defaultTab: "li#tab-2",
														});
														i++;
													}
												}
										});
										$("#tab-container").easytabs();
										$("#tab-container2").easytabs();
										
										$(".verifyEmail").on("click",function(){
											$.post("'.$set->SSLprefix.'ajax/sendEmailVarificationMail.php", { affiliate_id : '. $db['id'] .' }, function(res) {
												try {
													if(res == 1){
														$.prompt("'.lang('Verification email sent on your email id').'", {
																top:200,
																title: "'. lang('Email Verification') .'",
																buttons: { "'.lang('Ok').'": true},
																submit: function(e,v,m,f){
																	if(v){
																		//
																	}
																	else{
																		//
																	}
																}
															});
													}
													else{
														$.prompt("'.lang('There is some problem in sending verification email. Please try again later.').'", {
																top:200,
																title: "'. lang('Email Verification') .'",
																buttons: { "'.lang('Ok').'": true},
																submit: function(e,v,m,f){
																	if(v){
																		//
																	}
																	else{
																		//
																	}
																}
															});
													}
												} catch (error) {
													console.log("\n\nException: " + error + "\n\n");
												}
											});
										});
										
									});
									
									
								</script>
								 <style>
				    .etabs { margin: 0; padding: 0; }
					.tab { display: inline-block; zoom:1; *display:inline; background: #eee; border: solid 1px #999; border-bottom: none; -moz-border-radius: 4px 4px 0 0; -webkit-border-radius: 4px 4px 0 0; }
					.tab a { font-size: 14px; line-height: 2em; display: block; padding: 0 10px; outline: none; }
					.tab a:hover { text-decoration: underline; }
					.tab.active { background: #fff; padding-top: 6px; position: relative; top: 1px; border-color: #666; }
					.tab a.active { font-weight: bold; }
					.tab-container .panel-container { background: #fff; border: solid #666 1px; padding: 10px; -moz-border-radius: 0 4px 4px 4px; -webkit-border-radius: 0 4px 4px 4px; }
				  </style>';
			}
			
			
			if ($id) {
				
				$l=0;
				$merchantqq = function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY pos",__FILE__,__FUNCTION__);
				$counter = mysql_fetch_assoc( function_mysql_query("SELECT count(id) as count FROM merchants WHERE valid='1' ORDER BY pos",__FILE__,__FUNCTION__));
				$isOneOfMerchantsForex = false;
				
					$isForexOrBinary = false;
				$merchants = array();
				while ($merchantww = mysql_fetch_assoc($merchantqq)) {
					$l++;
					
				
					
					if (strtolower($merchantww['producttype']) == 'forex' && $isOneOfMerchantsForex == false) {
						$isOneOfMerchantsForex = true;
					}
					
					$IsMoreThanOneBrand = $counter['count'] > 1 ? true : false;
					$merchants[] = $merchantww;
						if (
						'forex'  == strtolower($merchantww['producttype']) ||
						'binary' == strtolower($merchantww['producttype']) ||
						'binaryoption' == strtolower($merchantww['producttype']) ||
						'forex'  == strtolower($merchantww['type']) ||
						'binary' == strtolower($merchantww['type'])
					) {
						$isForexOrBinary = true;
					}
				}
				
				$l=0;
				foreach ($merchants as $merchantww){
					
					
					$l++;
					//$IsMoreThanOneBrand = false;
					
					unset($min_cpaAmount, $cpaAmount, $dcpaAmount, $revenueAmount,$revenueSpreadAmount,$lotsAmount, $pnlAmount, $cplAmount, $cpcAmount, $cpmAmount, $positionsRevShareAmount);
					
					
					// nirs fix 06/1/2015
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='min_cpa' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "min_cpa") $min_cpaAmount = $takeww['amount'];
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='cpa' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpa") $cpaAmount = $takeww['amount'];
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='dcpa' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "dcpa") $dcpaAmount = $takeww['amount'];
						
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='revenue' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "revenue") $revenueAmount = $takeww['amount'];
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='revenue_spread' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "revenue_spread") $revenueSpreadAmount = $takeww['amount'];
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='lots' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "lots"){ $lotsAmount = $takeww['amount'];   }
				
						$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='cpl' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpl") $cplAmount = $takeww['amount'];

					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='pnl' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "pnl") $pnlAmount = $takeww['amount'];
				
	             	$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='cpc' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpc") $cpcAmount = $takeww['amount'];				

	             	$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='cpm' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpm") $cpmAmount = $takeww['amount'];		
					
					$afDealsQuery = "SELECT id,dealType,amount FROM ".$appDeals." WHERE dealType='positions_rev_share' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "positions_rev_share") $positionsRevShareAmount = $takeww['amount'];	
					
					
					$afDealsQuery = "SELECT id,dealType,amount FROM affiliates_deals WHERE dealType='cpi' and merchant_id='".$merchantww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
					$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
					$takeww = mysql_fetch_assoc($takeqq) ;
					if ($takeww['dealType'] == "cpi") $cpiAmount = $takeww['amount'];
					
					
					$listDealType .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
									<!--td align="center"><input type="checkbox" name="activeMerchants[]" value="'.$merchantww['id'].'" '.(@in_array($merchantww['id'],$merchantList) ? 'checked' : '').' /></td-->
									<td align="center"><label class="switch"><input type="checkbox" name="activeMerchants[]" value="'.$merchantww['id'].'" '.(@in_array($merchantww['id'],$merchantList) ? 'checked' : '').' /><div class="slider round"></div></label></td>
									<td align="left"><input type="hidden" name="deal_merchant[]" value="'.$merchantww['id'].'" /><b>'.$merchantww['name'].'</b></td>
									<td>'. $set->currency .' <input class="dealType1'.$l.' deal_min_cpa'.$l.' minCpa'.$l.'" id="minCpa_'.$l.'" type="text" name="deal_min_cpa[]" data-value="'.$min_cpaAmount.'" value="'.$min_cpaAmount.'"  style="width: 80px; text-align: center;" /></td>
									<td>'. $set->currency .' <input class="dealType1'.$l.' cpa'.$l.'" type="text" name="deal_cpa[]" id="deal_cpa_'.$l.'" data-value="'.$cpaAmount.'" value="'.$cpaAmount.'"  style="width: 80px; text-align: center;" /></td>
									<td>% <input class="dealType1'.$l.' dcpa'.$l.'" type="text" name="deal_dcpa[]" data-value="'.$dcpaAmount.'" value="'.$dcpaAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>'.
									($set->deal_cpi==1 ? '<td>'. $set->currency .' <input class="dealType1'.$l.' cpi'.$l.'" type="text" name="deal_cpi[]" data-value="'.$cpiAmount.'" value="'.$cpiAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' :'').
									($set->deal_revshare==1 ? '<td>% <input class="dealType1'.$l.' rev'.$l.'" type="text" name="deal_revenue[]" data-value="'.$revenueAmount.'" value="'.$revenueAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' :'').
									($set->showPositionsRevShareDeal  ? ($isForexOrBinary ? '<td>% <input class="dealType1'.$l.' positions_rev_share'.$l.'" type="text" name="deal_positions_rev_share[]" data-value="'.$positionsRevShareAmount.'" value="'.$positionsRevShareAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '<td style=\'visibility:hidden\'>% <input class="dealType1'.$l.' positions_rev_share'.$l.'" type="text" name="deal_positions_rev_share[]" data-value="'.$positionsRevShareAmount.'" value="'.$positionsRevShareAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>') : '') .
									($set->deal_pnl==1  ?  '<td>% <input class="dealType1'.$l.' deal_pnl'.$l.'" type="text" name="deal_pnl[]" data-value="'.$pnlAmount.'" value="'.$pnlAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '') .
									($isOneOfMerchantsForex && $set->deal_revshare_spread==1 ? ( 'forex'  == strtolower($merchantww['producttype']) ? '<td>% <input class="dealType1'.$l.' rev_spread'.$l.'" type="text" name="deal_revenue_spread[]" data-value="'.$revenueSpreadAmount.'" value="'.$revenueSpreadAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '<td></td>'):"").
									($isOneOfMerchantsForex ? ( 'forex'  == strtolower($merchantww['producttype']) ? '<td>'. $set->currency .'<input class="dealType1'.$l.' lots'.$l.'" type="text" name="deal_lots[]" data-value="'.$lotsAmount.'" value="'.$lotsAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '<TD></TD>'):'')
									.($set->deal_cpl ? '<td>'. $set->currency .'<input class="dealType2'.$l.' cpl'.$l.'" type="text" name="deal_cpl[]" data-value = "'. $cplAmount . '" value="'.$cplAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
									'.($set->deal_cpc ? '<td>'. $set->currency .' <input class="dealType2'.$l.' cpc'.$l.'" type="text" name="deal_cpc[]" data-value="'.$cpcAmount.'" value="'.$cpcAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
									'.($set->deal_cpm ? '<td>'. $set->currency .' <input class="dealType2'.$l.' cpm'.$l.'" type="text" name="deal_cpm[]" data-value="'.$cpmAmount.'" value="'.$cpmAmount.'" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
								</tr>
								<style>
								.requiredError{
									border:2px solid #ff0000 !important;
								}
								.displayErrs{
									color:#ff0000 !important;
									text-align:left !important;
								}
								</style>
								<script type="text/javascript">
									$(document).ready(function(){
										$(".dealType1'.$l.'").focus(function(event){
												/* dataval = $(this).data("value");
												$(this).val(dataval); */
										});
										$(".dealType1'.$l.'").keyup(function(event){
											
											
											if(($(".rev'.$l.'").length>0 && !(isEmpty("rev'.$l.'"))) && ($(".dcpa'.$l.'").length>0 && isEmpty("dcpa'.$l.'")) && ($(".pnl'.$l.'").length>0 && isEmpty("pnl'.$l.'")) && ($(".cpa'.$l.'").length>0 && isEmpty("cpa'.$l.'")) && ($(".minCpa'.$l.'").length>0 && isEmpty("minCpa'.$l.'"))){
												isNumeric(event,$(this));
												deactivate("cpl'.$l.'");
												deactivate("cpc'.$l.'");
												deactivate("cpm'.$l.'");
												activate("cpa'.$l.'");
												activate("pnl'.$l.'");
												activate("dcpa'.$l.'");
												//activate("rev'.$l.'");
												activate("minCpa'.$l.'");
											}else{
												
														activate("cpl'.$l.'");
														activate("cpc'.$l.'");
														activate("cpm'.$l.'");
												
												isNumeric(event,$(this));
												
											}
											
											if($(".positions_rev_share'.$l.'").length>0 && !(isEmpty("positions_rev_share'.$l.'"))){
												isNumeric(event,$(this));
											}
											
										});
										
										$(".cpl'.$l.'").focus(function(event){
												/* dataval = $(this).data("value");
												$(this).val(dataval); */
										});
										$("input[name=\'dcpa_global\']").keyup(function(){
											if($(this).val() == "")
												$(".dcpa_min, .dcpa_max").hide();
											else
												$(".dcpa_min, .dcpa_max").show();
										});
										$(".dcpa'.$l.'").keyup(function(event){
											
											dcpa_arr = [];
											$("input[name=\'deal_dcpa[]\']").each(function(k,v){
												
												if($(this).val() != ""){
													dcpa_arr.push($(this).val());
												}
												
											});
											
											if(dcpa_arr.length == 0){
												$(".dcpa_min, .dcpa_max").hide();
											}
											else
												$(".dcpa_min, .dcpa_max").show();
											
											
										});
										
										dcpa_arr = [];
										$("input[name=\'deal_dcpa[]\']").each(function(k,v){
											
											if($(this).val() != ""){
												dcpa_arr.push($(this).val());
											}
											
										});
										
										if(dcpa_arr.length == 0){
											$(".dcpa_min, .dcpa_max").hide();
										}
											
										$(".cpl'.$l.'").keyup(function(event){
											
											if(!(isEmpty("cpl'.$l.'"))){
												isNumeric(event,$(this));
												//activate("cpl'.$l.'");
												deactivate("cpc'.$l.'");
												deactivate("cpm'.$l.'");
												deactivate("cpa'.$l.'");
												deactivate("dcpa'.$l.'");
												deactivate("rev'.$l.'");
												deactivate("pnl'.$l.'");
												deactivate("minCpa'.$l.'");
											}else{
												//activate("cpl'.$l.'");
												activate("cpc'.$l.'");
												activate("cpm'.$l.'");
												activate("cpa'.$l.'");
												activate("dcpa'.$l.'");
												activate("pnl'.$l.'");
												activate("rev'.$l.'");
												activate("minCpa'.$l.'");
											}
										
										});
										
										
										$(".cpc'.$l.'").focus(function(event){
												/* dataval = $(this).data("value");
												$(this).val(dataval); */
										});
										
										$(".cpc'.$l.'").keyup(function(event){
											
											if(!(isEmpty("cpc'.$l.'"))){
												isNumeric(event,$(this));
												//activate("cpc'.$l.'");
												deactivate("cpl'.$l.'");
												deactivate("cpm'.$l.'");
												deactivate("cpa'.$l.'");
												deactivate("dcpa'.$l.'");
												deactivate("rev'.$l.'");
												deactivate("pnl'.$l.'");
												deactivate("minCpa'.$l.'");
											}else{
												activate("cpl'.$l.'");
												//activate("cpc'.$l.'");
												activate("cpm'.$l.'");
												activate("cpa'.$l.'");
												activate("dcpa'.$l.'");
												activate("rev'.$l.'");
												activate("pnl'.$l.'");
												activate("minCpa'.$l.'");
											}
										
										});
										
										
										$(".cpm'.$l.'").keyup(function(event){
																
											if(!(isEmpty("cpm'.$l.'"))){
												isNumeric(event,$(this));
												//activate("cpm'.$l.'");
												deactivate("cpc'.$l.'");
												deactivate("cpl'.$l.'");
												deactivate("cpa'.$l.'");
												deactivate("dcpa'.$l.'");
												deactivate("rev'.$l.'");
												deactivate("pnl'.$l.'");
											}else{
												activate("cpl'.$l.'");
												activate("cpc'.$l.'");
												//activate("cpm'.$l.'");
												activate("cpa'.$l.'");
												activate("dcpa'.$l.'");
												activate("pnl'.$l.'");
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
										if($(".rev'.$l.'").length >0)
										($(".rev'.$l.'") && $(".rev'.$l.'").val().length>0) 	? 	$(".rev'.$l.'").trigger("keyup") 	: 		null;
										//(($(".rev'.$l.'") && $(".rev'.$l.'").val().length>0) || ()) 	? 	$(".rev'.$l.'").trigger("keyup") 	: 		null;
									
									if($(".pnl'.$l.'").length >0)
										($(".pnl'.$l.'") && $(".pnl'.$l.'").val().length>0) 	? 	$(".pnl'.$l.'").trigger("keyup") 	: 		null;
										//(($(".pnl'.$l.'") && $(".pnl'.$l.'").val().length>0) || ()) 	? 	$(".pnl'.$l.'").trigger("keyup") 	: 		null;
									
										'.($set->deal_cpl ? '($(".cpl'.$l.'") && $(".cpl'.$l.'").val().length>0) 	? 	$(".cpl'.$l.'").trigger("keyup") 	:	 	null;' : '').'
										'.($set->deal_cpc ? '($(".cpc'.$l.'") && $(".cpc'.$l.'").val().length>0) 	? 	$(".cpc'.$l.'").trigger("keyup") 	:	 	null;' : '').'
										'.($set->deal_cpm ? '($(".cpm'.$l.'") && $(".cpm'.$l.'").val().length>0) 	? 	$(".cpm'.$l.'").trigger("keyup") 	:	 	null;' : '').'
									
									
										function isNumeric(event,v){
												if(isNaN(String.fromCharCode(event.which))){
													newVal = v.val();
													v.val( newVal.replace(/[^0-9\.]/g,""));
													return false;
												}
										}
									});
									
									$(".allproductdeals").keyup(function(e){
										isNumeric(event,$(this));
									});
									
								</script>
								
								';
					}
					
					$a = getMerchants();
					$allTierMerOptions = "";
					$defaultTierMer = isset($tier_merchant)?$tier_merchant:0;
					$rec = 0;
					foreach($a as $t_id=>$t_mer){
						if($defaultTierMer == 0){
							if($rec==0)
								$defaultTierMer = $t_id;
						}
						
						$allTierMerOptions .= "<option value='" . $t_id ."'" .  (isset($tier_merchant)&&$tier_merchant==$t_id?' selected':'') . ">". $t_mer['name'] ."</option>";
						
						$rec++;
					}
					
					
					
					$qry = "SELECT id,tier_amount,CONVERT(SUBSTRING_INDEX( tier_amount,  '-', 1 ),UNSIGNED INTEGER) as tierorder,"
                                                     . "tier_pcpa,amount,merchant_id , tier_type  "
                                                . "FROM ".$appDeals." "
                                                . "WHERE valid=1 and 1=1 "   .$networkWheremid. " and affiliate_id='".$id."' AND dealType='tier' and (amount!=0 or tier_pcpa!=0 ) ". (isset($tier_merchant)&&$tier_merchant!=""?' and merchant_id= ' . $tier_merchant :' and merchant_id= ' . $defaultTierMer) ." ORDER BY tierorder ASC";
                                                //. "WHERE valid=1 and 1=1 "   .$networkWheremid. " and affiliate_id='".$id."' AND dealType='tier' and tier_amount !='0-0' and amount!=0 and tier_pcpa!=0 ". (isset($tier_merchant)&&$tier_merchant!=""?' and merchant_id= ' . $tier_merchant :' and merchant_id= ' . $defaultTierMer) ." ORDER BY tierorder ASC";
                                        
					$takeqq = function_mysql_query($qry,__FILE__,__FUNCTION__);
					$strCurrentTierDealType = 'ftd_amount';
                                        $strTierTypePrefix = '$';
                                        $strTierTypeCaption = lang('Deposit Range').' '.'(ex. 100-200)';
                                        
					while ($takeww = mysql_fetch_assoc($takeqq)) {
                                            $merchantww = mysql_fetch_assoc(function_mysql_query("SELECT name FROM merchants WHERE id='".$takeww['merchant_id']."' ".$networkWhereid,__FILE__,__FUNCTION__));
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
                                                            <td>'. $set->currency .' <input type="text" name="deal_cpa[]" value="'.$takeww['amount'].'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                            <td>% <input type="text" name="deal_pcpa[]" value="'.$takeww['tier_pcpa'].'" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                            <td style="float:left !important;"><input type="submit" name="delete" value="'.lang('Delete').'" onclick="return deleteTierDeal(' . $takeww['id'] . ');"  style="display:none"/></td>
                                                            <td><input type="hidden" name="current_tier_type[]" value="' . $strCurrentTierDealType . '" /></td>
                                                        </tr>';
                                            $ll++;
					}
					
					
					
					if ($db['id']) $set->content .= '
							<div id="profiles" data-tab="profiles" class="config_tabs" style="padding-bottom:20px;;display:none;">
							<div class="normalTableTitle" style="cursor: pointer;">'.lang('Profiles').'</div>
							<div id="tab_2" style="width: 100%; background: #F8F8F8;">
							
							
							
							<form action="'.$set->basepage.'" method="post">
						<input type="hidden" name="act" value="save_profile" />
						<input type="hidden" name="db[affiliate_id]" value="'.$id.'" />
							<div align="left" style="padding: 10px;">
								<table class="tblDetails"><tr>
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
							</div>
							</form>
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
						
						$(document).ready(function(){
						function isNumeric(event,v){
								if(isNaN(String.fromCharCode(event.which))){
									newVal = v.val();
									v.val( newVal.replace(/[^0-9\.]/g,""));
									return false;
								}
						}
					
					
					$(".allproductdeals").keyup(function(e){
						isNumeric(e,$(this));
					});
					});
						</script>'
						.
							
                                                
							' </div>
							<script>
									
									var promptClick = false;
											function checkTierDeal(){
													i=0; checktier = false;
													$("#form_tier_deal_type tfoot tr").each(function(){
														if(i==0){
														
														if($(this).data("existing_tier_deals")){
															
															checktier = true;
																$.prompt("'.lang('Saving new deal type will stop the current tier deal this affiliate has, are you sure?').'", {
																	top:200,
																	title: "'.lang('Deals').'",
																	buttons: { "'. lang('Yes') .'": true, "'. lang('Cancel') .'": false },
																	submit: function(e,v,m,f){
																		var tiererrors = false;
																		e.preventDefault();
																		if($("#min_cpa_global").val() == ""){
																				l = 1; 
																				$("input[name=\'deal_cpa[]\']").each(function(){
																					console.log("#deal_cpa_"+l);
																						if($("#deal_cpa_"+l).val() !=""){
																							console.log($("#minCpa_"+l).val());
																							 if($("#minCpa_"+l).val() == ""){
																								 $(".minCpa"+l).addClass("requiredError");
																								 $(".displayErrs").html("'.lang('Minimum Deposit is required in case of CPA.').'");
																								 tiererrors = true;
																							 }
																						  }
																						  l++;
																				});
																			}
																			if(tiererrors == false && v==false){
																				$.prompt.close();
																				return;
																			}
																			if(tiererrors==true)
																			{
																				v=false;
																			}
																			else{
																				v=true;
																			}
																		if(v == true){
																			$("#formDealType").submit();
																		}
																		else{
																			$.prompt.close();
																			return false;
																		}
																	} 
																});
														}
														else{
															var tiererrors2 = false;
															if($("#min_cpa_global").val() == ""){
																l = 1; 
																$("input[name=\'deal_cpa[]\']").each(function(){
																	console.log("#deal_cpa_"+l);
																		if($("#deal_cpa_"+l).val() !=""){
																			console.log($("#minCpa_"+l).val());
																			 if($("#minCpa_"+l).val() == ""){
																				 $(".minCpa"+l).addClass("requiredError");
																				 $(".displayErrs").html("'.lang('Minimum Deposit is required in case of CPA.').'");
																				 tiererrors2 = true;
																			 }
																		  }
																		  l++;
																});
															}
															if(tiererrors2==true)
															{
																return false;
															}
															else{
																$("#formDealType").submit();
															}
															
														}
														i++;
													}
												});
												
											}
											</script>
							<div id="deal_type" data-tab="deal_type" class="config_tabs" style="padding-bottom:20px;display:none;">
                             <div class="normalTableTitle" style="cursor: pointer;">'.lang('Deal Types').'</div>
							 <div id="tab-container" class="tab-container">
								 <ul class="etabs">
								   <li class="tab" id="tab-1"><a href="#tabs1-regulareDeals">'. lang('Regular Deal types') .'</a></li>
								   '.($set->deal_tier?'<li class="tab" id="tab-2"><a href="#tabs1-tierDeals">'. lang('Tier Deals') .'</a></li>':'').'
								 </ul>
								  <div class="panel-container">
								   <div id="tabs1-regulareDeals">
											<div id="tab_3" style="width: 100%; background: #F8F8F8;">
												<form action="'.$set->SSLprefix.$set->basepage.'" method="post" id="formDealType">
												<input type="hidden" name="act" value="' . ($userLevel =='admin' || ($userLevel =='manager' && $set->AllowDealChangesByManager==1) ? 'save_deal' : 'disable'  ) .'" />
												<input type="hidden" name="affiliate_id" value="'.$id.'" />
												<table class="normal tblDetails" width="100%" border="0" cellpadding="3" cellspacing="0">
													<thead>
													<tr><td align="left"colspan="10" class="displayErrs"></td></tr>
													<tr>
														<td align="center">'.lang('Active').'</td>
														<td style="text-align: left;">'.lang('Merchant').'</td>
														<td>'.lang(ptitle('Min. Deposit')).' <span class="dcpa_min" style="display:none"> / ' . lang('DCPA - Min') .'</span></td>
														<td>'.lang('CPA').' <span class="dcpa_max" style="display:none">/ ' . lang('DCPA - Max') .'</span></td>
														<td>'.lang('DCPA').'</td>' 	. 
														($set->deal_cpi==1 ? '<td>'.lang('CPI').'</td>' : '') . 
														($set->deal_revshare==1 ? '<td>'.lang('NetDeposit').'</td>' : '') . 
														($set->showPositionsRevShareDeal && $isForexOrBinary ? '<td>' . lang(ptitle('Positions Rev. Share')) . '</td>' : '') . 
														($set->deal_pnl==1 ? '<td>' . lang(ptitle('PNL RevShare')) . '</td>' : '') . 
														($set->deal_revshare_spread==1 ? ($isOneOfMerchantsForex ? '<td>'.lang('Revenue Share Spread').'</td>' : '') :''). 
														($isOneOfMerchantsForex ? '<td>'.lang('Lots').'</td>' : '') . '
														'.($set->deal_cpl ? '<td>'.lang('CPL').'</td>' : '').'
														'.($set->deal_cpc ? '<td>'.lang('CPC').'</td>' : '').'
														'.($set->deal_cpm ? '<td>'.lang('CPM').'</td>' : '').'
													</tr></thead>
													
													<tfoot class="alldeals"><tr style="background: #D9D9D9;'.($IsMoreThanOneBrand==1 ? '' : 'display:none;').'">
													<td></td>
													<td align="left"><b>'.lang('Global To All Merchants').'</b></td>
													<td>'. $set->currency .'  <input type="text" name="min_cpa_global" value="" id="min_cpa_global" style="width: 80px; text-align: center;" /></td>
													<td>'. $set->currency .' <input type="text" name="cpa_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
													<td>% <input type="text" name="dcpa_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>'.
													($set->deal_cpi==1 ? '<td>% <input type="text" name="cpi_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '' ).
													($set->deal_revshare==1 ? '<td>% <input type="text" name="revenue_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '' ).
													($set->showPositionsRevShareDeal && $isForexOrBinary ? '<td>% <input type="text" name="positions_rev_share_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '') . 
													 
													($set->deal_pnl==1 ? '<td>% <input type="text" name="pnl_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>':'').
													($set->deal_revshare_spread==1 && $isOneOfMerchantsForex ? '<td>% <input type="text" name="revenue_spread_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>':'').
													($isOneOfMerchantsForex ? '<td>'. $set->currency .' <input type="text" name="lots_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>':'').'
													'.($set->deal_cpl ? '<td>'. $set->currency .' <input type="text" name="cpl_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
													'.($set->deal_cpc ? '<td>'. $set->currency .' <input type="text" name="cpc_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
													'.($set->deal_cpm ? '<td>'. $set->currency .' <input type="text" name="cpm_global" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>' : '').'
													</tr>'.$listDealType.'</tfoot>
												</table>'.
												($userLevel =='admin' || ($userLevel =='manager' && $set->AllowDealChangesByManager==1) ?
																			'
																				<div align="right" style="padding-top: 20px;">
																				'.($set->deal_tier?'<input type="button" value="'.lang('Save').'"  onclick="checkTierDeal();"/>':'<input type="submit" value="'.lang('Save').'""/>') :'').'
																						<br />
																				</div>
												<div style="padding:5px; color:GREEN; font-weight:bold">* '.lang('Empty values on all fields will be automatically converted to system default commission values').'</div>
												<!--div style="padding:5px; color:GREEN; font-weight:bold">* '.lang('0 values will override default merchant values').', '.lang('make sure that the lower Minimum Deposit value is 1').'.</div-->
												</form>
												</div>
								   </div>
								   '.($set->deal_tier?'<div id="tabs1-tierDeals">
												
															  <div style="font-size: 10px; padding: 5px;float:left;">
                                                                    * '.lang('Tier deal will erase all previous deals for this affiliate').'
                                                                </div>
																
																<div style="float:right;padding-bottom:10px;padding-right:50px;">
																<form method="get">
																<input type="hidden" name="act" value="new">
																<input type="hidden" name="id" value="'. $id .'">
																    '.lang('Choose Merchant').' : 
																		<select name="tier_merchant" onChange="form.submit()">
																		<!--option value="">'. lang('All') .'</option>
																		'. listMerchants(isset($tier_merchant)?$tier_merchant:0) .'-->
																		'. $allTierMerOptions .'
																		</select>
																		<input type="hidden" name="toggleTo" value="deal_type">
																	</form>
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
                                                                
                                                                <form id="form_tier_deal_type" action="'.$set->SSLprefix.$set->basepage.'" method="post">
                                                                    <input type="hidden" name="act" value="save_deal_tier" />
                                                                    <input type="hidden" name="affiliate_id" value="'.$id.'" />
                                                                    <input type="hidden" name="deal_count" id="deal_count" value="1" />
                                                                    <table class="normal tblDetails" width="100%" border="0" cellpadding="3" cellspacing="0">
                                                                            <thead><tr>
                                                                                    <td style="text-align: left;">'.lang('Merchant').'</td>
                                                                                    <td>' . lang('Tier Deal Type') . '</td>
                                                                                    <td>' . lang($strTierTypeCaption) . '</td>
                                                                                    <td>' . lang('CPA').'</td>
                                                                                    <td>' . lang('PCPA').'</td>
                                                                                    <td width="35%"></td>
                                                                            </tr></thead><tfoot class="testtieradd">'.$listTier.'<tr '.($ll % 2 ? 'class="trLine"' : '').'>
                                                                            <td style="text-align: left;"><select name="deal_merchant[]"><!--'.listMerchants(isset($tier_merchant)&&$tier_merchant!=""?$tier_merchant:$merchantww['id']).'-->'. $allTierMerOptions .'</select></td>
                                                                            <td>
                                                                                <select name="tier_deal_type_new[]">
                                                                                    <!-- Content will loaded via ajax. -->
                                                                                </select>
                                                                            </td>
                                                                            <td><span>' . $strTierTypePrefix . '</span> <input type="text" name="tier_amount[]" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                                            <td><span>'. $set->currency .'</span> <input type="text" name="cpa[]" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                                            <td><span>%</span> <input type="text" name="pcpa[]" value="" id="fieldClear" style="width: 80px; text-align: center;" /></td>
                                                                            <td style="float:left !important;"><input type=\'button\' name=\'delete_new\' value=\''.lang('Delete').'\' style="display:none"></td>
                                                                    </tr></tfoot>
                                                                    </table>
                                                                    <div align="right" style="padding-top: 20px;"><input type="button" value="'.lang('Add New Tier Level').'" onClick="addNewTierLevel()"/>&nbsp;&nbsp;<input type="submit" value="'.lang('Save').'" /></div>
								</form>
                                                                
                                                                <script type="text/javascript">
                                                                    var intCurrentMerchant  = $("#form_tier_deal_type select[name=deal_merchant]").val();
                                                                    var intCurrentAffiliate = "' . $_GET['id'] . '";
                                                                    
																	
																	/* $("tfoot.testtieradd").on( "click","tr",function(){
																		 if($("tfoot.testtieradd tr").length > 1){
																				$(this).find("input[name=delete_new]").show();
																		}
																	}); */
																	
																	$("tfoot.testtieradd").on( "mouseover","tr",function(){
																		 
																		if($("tfoot.testtieradd input[name^=deal_ids]").length >= 1){
																			$(this).find("input[name=delete]").show();
																		}
																		
																		if($("tfoot.testtieradd select[name^=deal_merchant]").length > 1){
																				$(this).find("input[name=delete_new]").show();
																		}
																		
																	}).on( "mouseout","tr",function(){
																		 //if($("tfoot.testtieradd tr").length > 1){
																				$(this).find("input[name=delete_new]").hide();
																				$(this).find("input[name=delete]").hide();
																		//}
																	});
																	
																	$("tfoot.testtieradd").on("click", \'input[name="delete_new"]\', function(e){
																	   $(this).closest(\'tr\').remove();
																	   $("#deal_count").val(parseInt($("#deal_count").val())-1);
																	});
																	
																																		
																	var row = 0;
																	function addNewTierLevel(){
																		
																		$("#deal_count").val(parseInt($("#deal_count").val())+1);
																		
																		var lastRow = $("tfoot.testtieradd tr:last").html();
																		
																		var brand = tierdealtype = depositRange= cpa = pcpa = "";
																		

																		
																		 $("tfoot.testtieradd tr:last").find("select").each(function(k,val) {
																				if(k==0){
																					brand = this.value;
																				}
																				if(k==1){
																					tierdealtype = this.value;
																				}
																		  });
																		  
																		
																		  $("tfoot.testtieradd").append("<tr>" + lastRow + "</tr>");
																		  
																		  $("tfoot.testtieradd tr:last").find("select").each(function(k,val) {
																				if(k==0){
																					this.value = brand;
																				}
																				if(k==1){
																					this.value = tierdealtype;
																				}
																		  }); 
																		  $("tfoot.testtieradd tr:last").find("input").each(function(k,val) {
																				if(k==3){
																					$(this).hide();
																				}
																		  });
																		  row++;
																	}
																	
                                                                    (function() {
                                                                        var strAjaxAddr = "'.$set->SSLprefix.'ajax/PopulateTierDealTypeSelect.php?merchant_id=" 
                                                                                        + intCurrentMerchant 
                                                                                        + "&affiliate_id=" 
                                                                                        + intCurrentAffiliate;
                                                                        
                                                                        $.get(strAjaxAddr, function(res) {
                                                                            try {
                                                                                $("#form_tier_deal_type select[name^=tier_deal_type_new]").html(res);
                                                                            } catch (error) {
                                                                                console.log(error);
                                                                            }
                                                                        });
                                                                    })();
                                                                    
                                                                    /* $("#form_tier_deal_type select[name=deal_merchant]").change(function() {
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
                                                                    }); */
																	
																	$("tfoot.testtieradd").on("change", "tr select[name^=deal_merchant]",function() {
																		var trCurr = $(this).closest("tr").find("select[name^=tier_deal_type_new]");
																		var strAjaxAddr = "' . $_SERVER['SERVER_HOST'] . '/ajax/PopulateTierDealTypeSelect.php?merchant_id=" 
                                                                                        + $(this).val() 
                                                                                        + "&affiliate_id=" 
                                                                                        + intCurrentAffiliate;
                                                                        
                                                                        $.get(strAjaxAddr, function(res) {
                                                                            try {
																				
																				trCurr.html(res);
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
													<!--/div-->
								  </div>':'').'
							  </div>
							
								';
								
								 if ($set->isBasicVer ==0 && ($userLevel =='admin') || ($userLevel =='manager')){
									if (($set->AllowDealChangesByManager==1 && $userLevel =='manager') ||  $userLevel =='admin') {
								$set->content .= '
								'.($set->deal_tier ? '<div id="tier_deal" data-tab="tier_deal" class="config_tabs" style="padding-bottom:20px;display:none;">
								
							</div>' : '').'
							';
						


						$merDefaultQualification = "";
						$merchants = getMerchants(0,1);
						foreach($merchants as $merchantRow){
							$merDefaultQualification="( ". $merchantRow['qualify_amount'] . '  ' . ucwords($merchantRow['qualify_type']) . " )";
						}

				
				
						$set->content .= '<div style="padding-top:20px;"><div class="normalTableTitle" style="cursor: pointer;">'.lang('Qualified Commission').'</div>
						<div id="tab_4" style="width: 100%; background: #F8F8F8;">
							<table>
							<tr>
							<td width="50%" align="left" valign="top">
								<form id="save_qualification" action="'.$set->SSLprefix.$set->basepage.'" method="post">
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
										<td align="left" style="width:180px">'.lang('Qualify Commission').':</td>
										<td align="left"><select  id="empnt" name="db[qualify_type]" style="width: 292px;">
											<option value=""  '.($db['qualify_type'] == "" ? "selected" : ''). '>'.lang('None').'</option>
											<option value="default" '.($db['qualify_type'] == "default" ? 'selected' : ''). '>'.lang('Merchant Default').'</option>
											<option value="trades" '.($db['qualify_type'] == "trades" ? 'selected' : ''). '>'.lang('Number Of Trades').'</option>
											<option value="totalmd" '.($db['qualify_type'] == "totalmd" ? 'selected' : ''). '>'.lang('Total Minimum Deposit').'</option>
											<option value="volume" '.($db['qualify_type'] == "volume" ? 'selected' : ''). '>'.lang('Amount Of Volume').'</option>
											'.($isOneOfMerchantsForex ? '
											<option value="lots" '.($db['qualify_type'] == "lots" ? 'selected' : ''). '>'.lang('Amount Of Lots').'</option>
											' : '' ).'
											
										</td>
										
										<td align="center" id="default_merchant_qualification" style="font-size: 12px;">'.$merDefaultQualification.'</td>
											
										</td>
										</tr>
										<tr  id="empv" style="'.($db['qualify_type'] == "totalmd" || $db['qualify_type'] == "trades" || $db['qualify_type'] == "volume"|| $db['qualify_type'] == "lots" ? '' : 'display:none').' ;margin-top:20px;">
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
						
						</div></div>
							<script type="text/javascript">
								$(document).ready(function(){
									$("#empn").change(function(e){
										if($("#empnt").val().length>0 && $("#empnt").val()!="default" ){
											$("#empv").show();
										}else{
											$("#empv").hide();
										}
									});
									
									
									
						  	$("#empn").change(function(e){
							if($("#empnt").val().length>0 && $("#empnt").val()=="default" ){
											
									$("#default_merchant_qualification").show();
								}
								else 
								{
									$("#default_merchant_qualification").hide();
								}
							
						}).change();
		
									
								});
							</script>
						';
								}
								 }
							
								
									if ($userLevel =='admin' || ($userLevel =='manager' && $set->AllowDealChangesByManager==1 
								&& !empty($set->showDealTypeHistoryToAM )
								)) {

								$set->content .='
								<!-- Deal-types history -->
								<div style="padding-top:20px;">
								<div class="normalTableTitle" id="dealTypeHistoryTab" style="cursor: pointer;">'.lang('Deal Types History').'</div>';
								
								include ('common/dealTypeHistory.php');
								$set->content .='</div>';
								
								if($set->showProductsPlace){
									$sql = "Select * from products_items where valid>-1";
									$productqq = function_mysql_query($sql,__FILE__,__FUNCTION__);
									$productsList = "";
									$a=0;
									while($productww = mysql_fetch_assoc($productqq)){
										
										unset($cpaAmount, $cplLeadAmount, $cplAccountAmount);
									
										$afDealsQuery = "SELECT id,dealType,amount FROM products_affiliates_deals WHERE dealType='cpa' and product_id='".$productww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
										$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
										$takeww = mysql_fetch_assoc($takeqq) ;
										if ($takeww['dealType'] == "cpa") $cpaAmount = $takeww['amount'];
										
											$afDealsQuery = "SELECT id,dealType,amount FROM products_affiliates_deals WHERE dealType='cpc' and product_id='".$productww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
										$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
										$takeww = mysql_fetch_assoc($takeqq) ;
										if ($takeww['dealType'] == "cpc") $cpcAmount = $takeww['amount'];
										
										$afDealsQuery = "SELECT id,dealType,amount FROM products_affiliates_deals WHERE dealType='cpi' and product_id='".$productww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
										$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
										$takeww = mysql_fetch_assoc($takeqq) ;
										if ($takeww['dealType'] == "cpi") $cpiAmount = $takeww['amount'];
										
										
										
										$afDealsQuery = "SELECT id,dealType,amount FROM products_affiliates_deals WHERE dealType='cpllead' and product_id='".$productww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
										$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
										$takeww = mysql_fetch_assoc($takeqq) ;
										if ($takeww['dealType'] == "cpllead") $cplLeadAmount = $takeww['amount'];
										
										$afDealsQuery = "SELECT id,dealType,amount FROM products_affiliates_deals WHERE dealType='cplaccount' and product_id='".$productww['id']."' AND affiliate_id='".$id."' order by rdate desc limit 0,1";
										$takeqq = function_mysql_query($afDealsQuery,__FILE__,__FUNCTION__);
										$takeww = mysql_fetch_assoc($takeqq) ;
										if ($takeww['dealType'] == "cplaccount") $cplAccountAmount = $takeww['amount'];
										
										$a++;
										$productsList .= "<tr id='productradio'>
										<!--td align='center'><input type='checkbox' name='activeProducts[]' value='".$productww['id']."'".(@in_array($productww['id'],$productList) ? 'checked' : '')."/></td-->
										<td align='center'><label class='switch'><input type='checkbox' name='activeProducts[]' value='".$productww['id']."'"  . (@in_array($productww['id'],$productList) ? 'checked' : '') ."><div class='slider round'></div></label></td>
										<td align='center'><input type='hidden' name='deal_product[]' value='".$productww['id']."' />". $productww['title'] ."</td>
										<td align='center'>
										".(strpos($productww['image'],"/tmp/")?"<img  width=28 src='images/wheel.gif' alt='' />":"
										<img  style='height: 50px;    max-width: 100%;' border='0' src='".$productww['image']."' alt='' />") ."</td>
										<td align='center'>". $set->currency ." <input type='text'  name='deal_cpi[] ' data-id=".  $productww['id'] ." class='product_deals".$a." clp" . $a ." allproductdeals' value=".$cpiAmount."></td>
										<td align='center'>". $set->currency ." <input type='text'  name='deal_cpc[] ' data-id=".  $productww['id'] ." class='product_deals".$a." clp" . $a ." allproductdeals' value=".$cpcAmount."></td>
										<td align='center'>". $set->currency ." <input type='text'  name='deal_cpllead[] ' data-id=".  $productww['id'] ." class='product_deals".$a." clp" . $a ." allproductdeals' value=".$cplLeadAmount."></td>
										<td align='center'>". $set->currency ." <input type='text'  name='deal_cplaccount[]' data-id=".  $productww['id'] ." class='product_deals".$a." cpaf" . $a ." allproductdeals' value=".$cplAccountAmount."></td>
										<td align='center'>". $set->currency ." <input type='text' name='deal_cpa[]' data-id=".  $productww['id'] ." class='product_deals".$a." cps" . $a ." allproductdeals' value=".$cpaAmount."></td>
										</tr>";
									}
									 $set->content .='
								<!-- Product Deal Types -->
								<form id="product_deals" method="post">
								<input type="hidden" name="act" value="' . ($userLevel =='admin' || ($userLevel =='manager' && $set->AllowDealChangesByManager==1) ? 'save_product_deal' : 'disable'  ) .'" />
								<input type="hidden" name="affiliate_id" value="'.$id.'" />
								<div style="padding-top:20px;">
								<div class="normalTableTitle" style="cursor: pointer;">'.lang('Product Deal Types').'</div>
								<div id="tab_product_deals" style="width: 100%;">
								<table class="normal" width="100%">
								<thead><tr><td>'.lang('Active').'</td><td>'.lang('Product Name').'</td><td>Logo</td>
								<td>'. lang('Cost Per Insallation') .'</td>
								<td>'. lang('Cost Per Click') .'</td>
								<td>'. lang('Cost Per Lead') .'</td>
								<td>'. lang('Cost Per Account').'</td><td>'.lang('Cost Per Sale') . '</td></tr></thead>
								'. $productsList.'
							  </table>
							  <div align="right" style="padding-top: 20px;">
									<input type="submit" value="'.lang('Save').'"/>
									<br />
							</div>
								</div></div></form>
								';
								}
								$set->content .='</div>';
								}
								$set->content .='</div>
								';
								
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
								 

								 if ($set->isBasicVer ==0 && ($userLevel =='admin') || ($userLevel =='manager')){
								

							$set->content .= '
							<div data-tab="manager_notes_crm" id="manager_notes_crm" class="config_tabs" style="padding-bottom:20px;display:none;">
							<div class="normalTableTitle"  style="cursor: pointer;">'.lang('Manager Notes CRM').'</div>
							<!--<div id="tab_5" style="width: 100%; display: '.($note_id ? 'block' : 'none').'">-->
							<div id="tab_5" style="width: 100%;">
							<form action="'.$set->SSLprefix.$set->basepage.'" method="post">
							<input type="hidden" name="act" value="add_note" />
							<input type="hidden" name="affiliate_id" value="'.$id.'" />
							<input type="hidden" name="note_id" value="'.$note_id.'" />
							<table border="0" cellpadding="0" cellspacing="0" class="tblDetails">
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
						<script>
							function editCRMNotes(id){
									$.get( 
										"'.$set->SSLprefix.$set->basepage.'?act=edit_note&note_id="+id
									);	
							}
						</script>
						</div></div>
						';
			
			$qq=function_mysql_query("SELECT * FROM mail_sent WHERE affiliate_id='".$db['id']."' ORDER BY id DESC",__FILE__,__FUNCTION__);
			$adminInfoArray = array();
			while ($ww=mysql_fetch_assoc($qq)) {
				if(!isset($adminInfoArray[$ww['admin_id']])){
					$adminInfo=mysql_fetch_assoc(function_mysql_query("SELECT first_name,last_name FROM admins WHERE id='".$ww['admin_id']."'",__FILE__,__FUNCTION__));
					$adminInfoArray[$ww['admin_id']] = $adminInfo;
				}
				else{
					$adminInfo = $adminInfoArray[$ww['admin_id']];
				}
				$listEmails .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td align="center">'.$ww['id'].'</td>
							<td align="center">'.date("d/m/y H:i", strtotime($ww['rdate'])).'</td>
							<td align="center">'.$ww['mailCode'].'</td>
							<td align="center">'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</td>
							<td align="center">'.$ww['opened'].'</td>
							<!--td align="center">'.($ww['opened_time'] != "0000-00-00 00:00:00" ? xvPic($ww['opened'],1).' '.date("d/m/y H:i", strtotime($ww['opened_time'])) : '-').'</td-->
							<td align="center">'.($ww['opened_time'] != "0000-00-00 00:00:00" ? date("d/m/y H:i", strtotime($ww['opened_time'])) : '-').'</td>
						</tr>';
				$i++;
			}
			
			if (!empty($set->showDocumentsModule) || !empty($set->showAgreementsModule) || !empty($set->showInvoiceModule)) {
			 $set->content .= '
			 </a><div data-tab="verification_documents" id="verification_documents" class="config_tabs" style="padding-bottom:20px;display:none;"><div class="normalTableTitle" style="cursor: pointer;">'.lang('Documents').'</div>
			 <table width="100%">
			 <tr>
			 <td>
			 <div id="tab-container2" class="tab-container">
			 <ul class="etabs">';
			   if(!empty($set->showDocumentsModule))  $set->content .= '<li class="tab"><a href="#tabs1-document">'. lang('Verifications') .'</a></li>';
			   if(!empty($set->showAgreementsModule)) $set->content .= '<li class="tab"><a href="#tabs1-agreement">'. lang('Agreements') .'</a></li>';
			   if(!empty($set->showInvoiceModule)) $set->content .= '<li class="tab"><a href="#tabs1-invoice">'.lang('Invoices').'</a></li>';
			  $set->content .= '</ul>
			  <div class="panel-container">';
			  if(!empty($set->showDocumentsModule)){
			  
			 $set->content .= '<div id="tabs1-document">';
			 
					//////////////////////////// showDocumentsModule ////////////////////////////////////////////
                    include 'common/DocumentsPanel.php';
                    //////////////////////////// showDocumentsModule ///////////////////////////////////////////
			 
			$set->content .=' </div>';
			}
			if(!empty($set->showAgreementsModule)){
			$set->content .='<div id="tabs1-agreement">';
					 //////////////////////////// showAgreementsModule ////////////////////////////////////////////
                    include 'common/AgreementsPanel.php';
                    //////////////////////////// showAgreementsModule ///////////////////////////////////////////
			$set->content .='</div>';
			}
			if(!empty($set->showInvoiceModule)){
			$set->content .='<div id="tabs1-invoice">';
						 //////////////////////////// showInvoiceModule ////////////////////////////////////////////
                    include 'common/InvoicePanel.php';
                    //////////////////////////// showInvoiceModule ///////////////////////////////////////////
			$set->content .='</div>';
			}
			
			$set->content.='</div></td></tr></table></div>
			';
			}
			$qq=function_mysql_query("SELECT profilePermissionID FROM affiliates WHERE id='".$id."' ORDER BY id limit 1;",__FILE__,__FUNCTION__);
			$affiliatePermissionID = mysql_fetch_assoc($qq);
			
			$sql = "SELECT * from permissionProfile where affiliate_id in (0,". $id .") and valid=1";
			$resultqq = function_mysql_query($sql,__FILE__,__FUNCTION__);
			$profilelist = [];
			$listProfiles = '<option '.($set->def_profilePermissionsForAffiliate==-1 ?  ' selected ' : '').' value="-1">'.lang('Automatic').'</option>';
			while($resultww = mysql_fetch_assoc($resultqq)){
				if($affiliatePermissionID['profilePermissionID'] == $resultww['id'])
					$listProfiles .= "<option value='".$resultww['id']."' selected>". $resultww['name'] ."</option>";
				else
					$listProfiles .= "<option value='".$resultww['id']."'>". $resultww['name'] ."</option>";
			}
			
		
			$set->content .= '<div data-tab="permissions" id="permissions" class="config_tabs" style="padding-bottom:20px;display:none;"><div class="normalTableTitle" style="cursor: pointer;">'.lang('Permissions').'</div>
						<div id="tab_6" style="width: 100%; background: #F8F8F8;">
							<form action="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=savePermission" method="post">
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						
							<table class="normal tblDetails" border="0" cellpadding="3" cellspacing="0">
								<tr>
									<td colspan="2" height="10"></td>
								</tr>
								<tr>
									<td>'.lang('Select Profile').':</td>
									<td>
													<select id="select_profile" name="db[permissionprofileId]">
														'. $listProfiles .'
													</select>
												</td>
												<td>
													<input type="submit"  value="' . lang('Save') . '" />
												</td>
								</tr>
							</table></form>
							<br/>
							<input type="button" onclick="javascript:goToPermissions()" value = "'. lang('Change') .'"/>
						</div>
						</div>
						<script>
						function goToPermissions(){
							window.location.href="'.$set->SSLprefix.$userLevel.'/permissions.php?affiliate_id='.$id.'";
						}
						</script>
						';

				$set->content .= '<div data-tab="emails_monitor" id="emails_monitor" class="config_tabs" style="padding-bottom:20px;display:none;"><div class="normalTableTitle" style="cursor: pointer;">'.lang('E-mails Monitor').'</div>
						<div id="tab_7" style="width: 100%; background: #F8F8F8;">
							<table class="normal tblDetails" width="100%" border="0" cellpadding="3" cellspacing="0">
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
						</div>
						';
			
			$qq=function_mysql_query("SELECT * FROM affiliates WHERE valid='1' AND refer_id='".$db['id']."' ORDER BY id DESC",__FILE__,__FUNCTION__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$listSub .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].'">'.$ww['id'].'</a></td>
							<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].'">'.$ww['username'].'</a></td>
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
				

				
				
				$set->content .= '<div  data-tab="api_access" id="api_access" class="config_tabs" style="padding-bottom:20px;display:none;"><div class="normalTableTitle" style="cursor: pointer;">'.lang('API Access').'</div>
						<div id="tab_8" style="width: 100%; background: #F8F8F8;">
						     <form id="form_api_access" action="'.$set->SSLprefix.$set->basepage.'" method="post">
                                                                 
                                                                    <input type="hidden" name="act" value="save_API_Access" />
                                                                    <input type="hidden" name="affiliate_id" value="'.$id.'" />
								
																	
							<table class="normal tblDetails" width="100%" border="0" cellpadding="3" cellspacing="0">
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
									<td align="center"><textarea style="width:255px;height:50px;" type="text"  readonly >'.$affiliateapiurl .'</textarea></td>
									<td align="center">'.(empty($affiliateapiurl) || $db['apiAccessType']=='None' || $db['apiAccessType']=='' ?lang('Inactive') : lang('Active')).'</td>
									<td align="center"><input type="submit" value="'.lang('Update').'" /></td>
								</tr>
								</tfoot>
							</table>
							<div style="margin-top:20px;margin-left:15px;">* '.lang('Update empty values to delete active permission').'.</div>
						</form>
						</div>
						';
			
				     //////////////////////////// PIXEL MONITOR begin //////////////////////////////////////////
                            include 'common/PixelMonitor.php';
					//////////////////////////// PIXEL MONITOR end ///////////////////////////////////////////
					$set->content .= "</div>";

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

				$set->content .= '<div  data-tab="affiliate_campaign_relation" id="affiliate_campaign_relation" class="config_tabs" style="padding-bottom:20px;display:none;"><div class="normalTableTitle" style="cursor: pointer;">'.lang('Related Campaigns').'</div>
						<div id="tab_9" style="width: 100%; background: #F8F8F8;"><br>
				<input type="button" onclick="window.location.href=\''.$set->SSLprefix.'admin/affiliateCampaignRelation.php\'" value="'.lang('Add New Relation').'" ><Br>';
							
	$qry = 	'SELECT merchants.name as merchant_name ,  affiliates_campaigns_relations.merchantid as merchant_id ,affiliates_campaigns_relations.profile_id,affiliates_campaigns_relations.isDefaultCamp, affiliates_campaigns_relations.id, affiliates_campaigns_relations.name as name , affiliates_campaigns_relations.affiliateID AS affID, affiliates.username,affiliates_campaigns_relations.campID AS campID 
				FROM affiliates_campaigns_relations INNER JOIN affiliates ON affiliates_campaigns_relations.affiliateID=affiliates.id inner join merchants where merchants.id = affiliates_campaigns_relations.merchantid
				and affiliates_campaigns_relations.affiliateid = ' . $id . '
				ORDER BY CAST(`affiliates_campaigns_relations`.`affiliateID` as SIGNED INTEGER)  DESC';
				
		// die ($qry);
		$relationsQ = function_mysql_query($qry,__FILE__,__FUNCTION__);
		
		$relationsStr = '';
		$l=0;
		
		$lastRowAffiliateID= 0;
		$style = "";
		
		
		
		
		$affiliatesAndProfilesArray = array();
		$counter=0;
		$profilesOptionText = "";
		if(!empty($relationsQ)){
		while($row=mysql_fetch_assoc($relationsQ)){
			$l++;
			
		
			if ($row['isDefaultCamp']==1) 
			$isDefaultCampTest = '<span style="font-weight:bold" >'.lang('Default for this Affiliate').'</span>';
		else
			$isDefaultCampTest = '<button onclick="window.location.href=\''.$set->SSLprefix.'admin/logs.php?act=make_default_campaign&mid='.$row['merchant_id'].'&affiliate_id='.$row['affID'].'&campaign='.$row['campID'].'\'">'.lang('Set As Default').'</button> ';
	

	
		if (!$affiliatesAndProfilesArray[$row['affID']]>0) {
			
		$qq = "SELECT * FROM affiliates_profiles s where affiliate_id = " . $row['affID'] . " and valid=1;";
		
		$prfRsc = function_mysql_query($qq,__FILE__,__FUNCTION__);
			while ($prflRslt = mysql_fetch_assoc($prfRsc)) {
			
			$affiliatesAndProfilesArray[$row['affID']][$counter]['id'] =  $prflRslt['id'];
			$affiliatesAndProfilesArray[$row['affID']][$counter]['name'] =  $prflRslt['name'];
			$counter++;
			}
			unset ($prfRsc);
		}		
	
	
		$profilesArray = $affiliatesAndProfilesArray[$row['affID']];
		
		$profilesOptionText = '';
		
		if (!empty($profilesArray))
		foreach	 ($affiliatesAndProfilesArray[$row['affID']] as $profilesArray) {
			
			if (empty($profilesOptionText))
				$profilesOptionText = '<option id="-1">' . lang('Default'). '</option>';
			$profilesOptionText .='<option id="'.$profilesArray['id'].'" ' .($profilesArray['id']==$row['profile_id'] ? " selected " : "" ). ' >' . $profilesArray['name'] . '</option>';
		}

		if ($lastRowAffiliateID==0 || $lastRowAffiliateID == $row['affID']) {
			$lastRowAffiliateID = $row['affID'];
			$style="";
			$showBorder = false;
		}
		else
		{
			
			$style= ' style="border-top:1px solid black;" ';
			$showBorder= true;
				$lastRowAffiliateID = $row['affID'];
		}
			
		
			
			$relationsStr.= '<tr ' . ' '  .($showBorder ? " class=\"showBorder\" " : '').' ' .($l % 2 ? 'class="trLine"' : '').' rel="'.$row['id'].'">
								<td style="text-align:center;width:10%; padding-top:8px; padding-bottom:8px"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$row['affID'].'" target="_blank">'.$row['affID'].'</a></td>
								<td style="text-align:center;width:20%;" class="affs" rel="'.$row['affID'].'"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$row['affID'].'" target="_blank">'.$row['username'].'</a></td>
								
								'. (empty($profilesOptionText)? '<td style="text-align:center;width:5%;" class="profile_id" rel="'.$row['profile_id'].'">'.(!$row['profile_id'] ? lang("Default") : "" ) .'</td>' : 
								'<td style="text-align:center;width:5%;" class="profile_id" rel="'.$row['profile_id'].'"><select disabled style="width:132px;">'. $profilesOptionText .'</select></td>' ) . '
								
								<td style="text-align:center;width:15%;" class="merchant_name" rel="'.$row['merchant_name'].'">'.$row['merchant_name'].'</td>
								<td style="text-align:center;width:15%;" class="camps" rel="'.$row['campID'].'">'.$row['campID'].'</td>
								<td style="text-align:center;width:20%;" class="campname" rel="'.$row['name'].'">'.$row['name'].'</td>
								<td style="text-align:center;width:10%;" class="isDefaultCamp" rel="'.$row['isDefaultCamp'].'">'.$isDefaultCampTest.'</td>
								<td style="text-align:center;width:10%;" class="isDefaultCamp" rel="'.$row['isDefaultCamp'].'"><a href="admin/affiliateCampaignRelation.php" target="_blank">'.lang('Edit').'</a></td>
								
									<input type="hidden" name="affiliate_id1" value="'.$row['affID'].'"/>
									<input type="hidden" name="campaign_id" value="'.$row['campID'].'"/>
									<input type="hidden" name="merchant_id" value="'.$row['merchant_id'].'"/>
								</td>
							</tr>';
		}
		}
		
		$set->content .='
			<table id="caTable" cellspacing=0 cellpadding=3 border=0 width="100%" class="normal" >
				<thead>
				<tr>
					<td style="width:10%">'.lang('Affilaite ID').'</td>
					<td style="width:15%">'.lang('Affilaite Username').'</td>
					<td style="width:5%">'.lang('Profile').'</td>
					<td style="width:15%">'.($campID['title'] ? ucwords($campID['title']) : lang('Merchant')).'</td>
					<td style="width:10%">'.($campID['title'] ? ucwords($campID['title']) : lang('Campaign ID')).'</td>
					<td style="width:15%">'.(lang('Campaign Name')).'</td>
					<td style="width:10%">'.(lang('Is Default Campaign')).'</td>
					<td style="width:15%">'.lang('Action').'</td>
				</tr>
				</thead>
				'.$relationsStr.'
				
			</table>
			
			
						
						
						</div>
						</div>
						';
				
				
				
			$set->content .= '<div id="'. ($set->introducingBrokerInterface ? 'sub_introduce_broker':'sub_affiliates').'" data-tab="'. ($set->introducingBrokerInterface ? 'sub_introduce_broker':'sub_affiliates').'" class="config_tabs" style="padding-bottom:20px;display:none;"><div class="normalTableTitle" style="cursor: pointer;">'.($set->introducingBrokerInterface ? lang('Sub Introduce Broker') : lang('Sub Affiliates')).'</div>
						<div id="tab_10" style="width: 100%; background: #F8F8F8;">
							<table class="normal tblDetails" width="100%" border="0" cellpadding="3" cellspacing="0">
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
						</div></div>
						';
			
                            $qq=function_mysql_query("SELECT * FROM traffic WHERE refer_url != '' and affiliate_id='".$db['id']."' ORDER BY rdate DESC LIMIT 50",__FILE__,__FUNCTION__);
							$merchantsArray = array();
							$l = 0;
                            while ($ww=mysql_fetch_assoc($qq)) {
									if($merchantsArray[$ww['merchant_id']]){
										$merchantName=dbGet($ww['merchant_id'],"merchants");
										$merchantsArray[$ww['merchant_id']] = $merchantName;
									}
									else{
										$merchantName = $merchantsArray[$ww['merchant_id']];
									}
                                    $listTraffic .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
                                                            <td align="left"><a href="'.$ww['refer_url'].'" target="_blank">'.$ww['refer_url'].'</a></td>
                                                            <td align="center">'.$ww['ip'].'</td>
                                                            <td align="center">'.date("d/m/y H:i", strtotime($ww['rdate'])).'</td>
                                                            <td align="center">'.$merchantName['name'].'</td>
                                                            <!--td align="center">'.$ww['visits'].'</td-->
                                                    </tr>';
                                    $i++;$l++;
                            }
                            
							
					$set->content .= '<div id="affiliate_traffic_referral" data-tab="affiliate_traffic_referral" class="config_tabs" style="padding-bottom:20px;display:none;"><div class="normalTableTitle" style="cursor: pointer;">'.lang('Affiliate Traffic Referral').'</div>
						<div id="tab_11" style="width: 100%; background: #F8F8F8;">
							<table class="normal tblDetails" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td align="left" style="text-align: left;">'.lang('Referral URL').'</td>
									<td align="center">'.lang('Last IP').'</td>
									<td align="center">'.lang('Last Click').'</td>
									<td align="center">'.lang('Merchant').'</td>
									<!--td align="center">'.lang('Visits').'</td-->
								</tr></thead><tfoot>'.($listTraffic==""?lang('No Records'):$listTraffic).'</tfoot>
							</table>
							'. ($l>=50?'<div style="padding:5px; color:GREEN; font-weight:bold">* '.lang('Last 50 records').'</div>':'').'
						</div></div>
						';
			

			
								}
						
						
						$set->content .= '<div id="payment_details" data-tab="payment_details" class="config_tabs" style="padding-bottom:20px;display:none;"><div class="normalTableTitle" style="cursor: pointer;">'.lang('Payment Details').'</div>
						<div id="tab_12" style="width: 100%; background: #F8F8F8;">';
                            
                            //////////////////////////// ACCOUNT begin //////////////////////////////////////////
                            //include 'common/Account.php';
                            include 'common/AffiliatePaymentDetails.php';
                            //////////////////////////// ACCOUNT end ////////////////////////////////////////////
							
                            $set->content .= '</div>';
			}
		
		$set->content .= "</div>";/* sidebar main div closed*/
		theme(); 
		break;
	
                
        case 'payment_save':
            //////////////////  AFFILIATE PAYMENT SAVE begin ////////////////////////////////////////////
            include 'common/AffiliatePaymentSave.php';
            //////////////////  AFFILIATE PAYMENT SAVE end //////////////////////////////////////////////
            break;
                
                
	
        
	case "save_pixel":
		
		    for ($i = 0; $i <= count($ids); $i++) {
				if($pixelType == 'merchants'){
					$qry = "UPDATE pixel_monitor "
                        . "SET merchant_id='".$merchant_id[$i]."',method='" .$method[$i]. "', "
                        . "    pixelCode='".trim(mysql_real_escape_string($pixelCode[$i]))."',type='".$type[$i]."',total='".$total[$i]."' "
                        . " WHERE id='".$ids[$i]."'";
				}
				else{
					$qry = "UPDATE pixel_monitor "
                        . "SET product_id='".$product_id[$i]."',method='" .$method[$i]. "', "
                        . "    pixelCode='".trim(mysql_real_escape_string($pixelCode[$i]))."',type='".$type[$i]."',total='".$total[$i]."' "
                        . " WHERE id='".$ids[$i]."'";
				}
                if ($pixelCode[$i]) {
                    function_mysql_query($qry,__FILE__,__FUNCTION__);
                } elseif ($ids[$i]) { 
                    function_mysql_query("DELETE FROM pixel_monitor WHERE id='".$ids[$i]."'",__FILE__,__FUNCTION__);
                }
            }
			
			
            if ($db['pixelCode'])  {
                $db['pixelCode'] = str_replace("'", '"', $db['pixelCode']);
                
			if($pixelType == 'products'){
                $qry = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `product_id`, `pixelCode`,`method`, `totalFired`,`banner_id`) VALUES
                       ('".$db['type']."',1,".$db['affiliate_id'].",".$db['product_id'].",'". trim(mysql_real_escape_string($db['pixelCode']))."','". ($db['method'])."',0,". $db["banner_id"] .")";
			}
			else
			{
				$qry = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `merchant_id`, `pixelCode`,`method`, `totalFired`,`banner_id`) VALUES
                       ('".$db['type']."',1,".$db['affiliate_id'].",".$db['merchant_id'].",'". trim(mysql_real_escape_string($db['pixelCode']))."','". ($db['method'])."',0,". $db["banner_id"] .")";
			}	
			    function_mysql_query($qry,__FILE__,__FUNCTION__);
            }
            
            _goto($set->SSLprefix.$set->basepage.'?act=new&id='.$db['affiliate_id'] ."&toggleTo=api_access");
            break;
			
			
	case "save_wizard_pixel":
	
			if($wizard_banners == "all_banners"){
				$banner_id = 0;
			}
			else{
				$banner_id = $wizard_selectBanner;
			}
			if(isset($pixel_id) && !empty($pixel_id)){
					if($wizard_pixelType == 'merchants'){
							   $qry = "UPDATE pixel_monitor "
                        . "SET merchant_id='".$wizard_merchantId."',method='" .$wizard_pixelmethod. "', "
                        . "    pixelCode='".trim(mysql_real_escape_string($wizard_pixelCode))."',type='".$wizard_trigger."', banner_id=". $banner_id ." WHERE id=".$pixel_id;
					}
					else{
						  $qry = "UPDATE pixel_monitor "
                        . "SET product_id='".$wizard_productId."',method='" .$wizard_pixelmethod. "', "
                        . "    pixelCode='".trim(mysql_real_escape_string($wizard_pixelCode))."',type='".$wizard_trigger."', banner_id=". $banner_id ." WHERE id=".$pixel_id;
					}
				function_mysql_query($qry,__FILE__,__FUNCTION__);
            }
			else{
				
						if($wizard_pixelType == 'merchants'){
							$qry = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `merchant_id`, `pixelCode`,`method`, `totalFired`,`banner_id`) VALUES
							   ('".$wizard_trigger."', '" . $wizard_pixelValid . "' ,".$affiliate_id.",".$wizard_merchantId.",'". trim(mysql_real_escape_string($wizard_pixelCode))."','". ($wizard_pixelmethod)."',0,". $banner_id.")"; 
						}
						else{
							   $qry = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `product_id`, `pixelCode`,`method`, `totalFired`,`banner_id`) VALUES
							   ('".$wizard_trigger."', '" . $wizard_pixelValid . "',".$affiliate_id.",".$wizard_productId.",'". trim(mysql_real_escape_string($wizard_pixelCode))."','". ($wizard_pixelmethod)."',0,". $banner_id.")";
						}
					 function_mysql_query($qry,__FILE__,__FUNCTION__);

			}
            _goto($set->SSLprefix.$set->basepage.'?act=new&id='.$affiliate_id ."&toggleTo=api_access");
            break;
	
	case "save_deal": 

	
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
				
                $chkDealPNL = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='pnl' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealMinCPA = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='min_cpa' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealCPA = mysql_fetch_assoc(function_mysql_query("SELECT id,merchant_id, amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpa' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealDCPA = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='dcpa' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealRevenue = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='revenue' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealRevenueSpread = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='revenue_spread' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealLots = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='lots' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealCPI = mysql_fetch_assoc(function_mysql_query("SELECT id,merchant_id, amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpi' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealCPL = mysql_fetch_assoc(function_mysql_query("SELECT id,merchant_id, amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpl' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealCPC = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpc' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealCPM = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='cpm' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
                $chkDealPositionsRevShare = mysql_fetch_assoc(function_mysql_query("SELECT id, merchant_id,amount FROM ".$appDeals." WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$deal_merchant[$i]."' AND dealType='positions_rev_share' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
				
				// PNL.
                processDealType($appDeals, $chkDealPNL, $set, $affiliate_id, $deal_merchant[$i], 'pnl', $pnl_global, $deal_pnl[$i], 'deal_pnl');
                
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
			    
				// CPI.
                processDealType($appDeals, $chkDealCPI, $set, $affiliate_id, $deal_merchant[$i], 'cpi', $cpi_global, $deal_cpi[$i], 'deal_cpi');
				
				

                // CPC.
                processDealType($appDeals, $chkDealCPC, $set, $affiliate_id, $deal_merchant[$i], 'cpc', $cpc_global, $deal_cpc[$i], 'deal_cpc');

                // CPM.
                processDealType($appDeals, $chkDealCPM, $set, $affiliate_id, $deal_merchant[$i], 'cpm', $cpm_global, $deal_cpm[$i], 'deal_cpm');
				
				// Positions Revenue Share.
				processDealType($appDeals, $chkDealPositionsRevShare, $set, $affiliate_id, $deal_merchant[$i], 'positions_rev_share', $positions_rev_share_global, $deal_positions_rev_share[$i], 'deal_positions_rev_share');
            }
			
            _goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $affiliate_id . '&ty=1'. "&toggleTo=deal_type");
            break;
	
		case "save_product_deal": 
			 if ($activeProducts || $activeProducts=="") {
                if ($activeProducts=="")
					updateUnit("affiliates", "products = ''" , "id = '" . $affiliate_id . "'");
				else
					updateUnit("affiliates", "products = '" . implode("|", $activeProducts) . "'", "id = '" . $affiliate_id . "'");
            } 

			for ($i = 0; $i < count($deal_product); $i++) {
					
					
					if($deal_cpa[$i] != ""){
					  $chkDealCPA =  mysql_fetch_assoc(function_mysql_query("SELECT id, product_id,amount FROM products_affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND product_id='".$deal_product[$i]."' AND dealType='cpa' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
					  
					  if(!$chkDealCPA){
						  
							$arrParams['rdate']        = dbDate();
							$arrParams['admin_id']     = $set->userInfo['id'];
							$arrParams['affiliate_id'] = $affiliate_id;
							$arrParams['product_id']  = $deal_product[$i];
							$arrParams['dealType']     = "cpa";
							$arrParams['amount']     = $deal_cpa[$i];
							
							$fields = implode(",",array_keys($arrParams));
							$values = implode(',', array_map(function($value) {
							if(!is_numeric($value)) {
								return '"' . $value . '"';
							} else {
								return $value;
							}
							}, array_values($arrParams)));
							$sql = "insert into products_affiliates_deals ($fields) values($values)";
							mysql_query($sql);
					  }
					  else{
						  $sql = "update products_affiliates_deals set rdate='". dbDate() ."' , amount=". $deal_cpa[$i] ."  where id= ". $chkDealCPA['id'];
						
						 mysql_query($sql);
					  }
					}
					  if($deal_cpllead[$i] != ""){
					  $chkDealCPLLead =  mysql_fetch_assoc(function_mysql_query("SELECT id, product_id,amount FROM products_affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND product_id='".$deal_product[$i]."' AND dealType='cpllead' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
					  if(empty($chkDealCPLLead)){
							$arrParams['rdate']        = dbDate();
							$arrParams['admin_id']     = $set->userInfo['id'];
							$arrParams['affiliate_id'] = $affiliate_id;
							$arrParams['product_id']  = $deal_product[$i];
							$arrParams['dealType']     = "cpllead";
							$arrParams['amount']     = $deal_cpllead[$i];
							$fields = implode(",",array_keys($arrParams));
							$values = implode(',', array_map(function($value) {
							if(!is_numeric($value)) {
								return '"' . $value . '"';
							} else {
								return $value;
							}
							}, array_values($arrParams)));
							$sql = "insert into products_affiliates_deals ($fields) values($values)";
							mysql_query($sql);
					  }
					  else{
						  $dt = dbDate();
						 //$sql = "update products_affiliates_deals set rdate='2016-01-01 00:00:00' and amount=". $deal_cpllead[$i] ."  where id= ". $chkDealCPLLead['id'];
						 $sql = "UPDATE products_affiliates_deals SET rdate =  '". $dt ."', amount =". $deal_cpllead[$i] ." WHERE id = ". $chkDealCPLLead['id'];
						
						 mysql_query($sql);
					  }
					  }
					  
					  if($deal_cplaccount[$i] != ""){
					  $chkDealCPLAcc =  mysql_fetch_assoc(function_mysql_query("SELECT id, product_id,amount FROM products_affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND product_id='".$deal_product[$i]."' AND dealType='cplaccount' ORDER BY rdate DESC",__FILE__,__FUNCTION__));
						if(!$chkDealCPLAcc){
						  
							$arrParams['rdate']        = dbDate();
							$arrParams['admin_id']     = $set->userInfo['id'];
							$arrParams['affiliate_id'] = $affiliate_id;
							$arrParams['product_id']  = $deal_product[$i];
							$arrParams['dealType']     = "cplaccount";
							$arrParams['amount']     = $deal_cplaccount[$i];
							
							$fields = implode(",",array_keys($arrParams));
							$values = implode(',', array_map(function($value) {
							if(!is_numeric($value)) {
								return '"' . $value . '"';
							} else {
								return $value;
							}
							}, array_values($arrParams)));
						  $sql = "insert into products_affiliates_deals ($fields) values($values)";
						  mysql_query($sql);
					  }
					  else{
						  $sql = "update products_affiliates_deals set rdate='". dbDate() ."' , amount=". $deal_cplaccount[$i] ."  where id= ". $chkDealCPLAcc['id'];
						 mysql_query($sql);
					  }
					  } 
					    if($deal_cpc[$i] != ""){
					  $q = "SELECT id, product_id,amount FROM products_affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND product_id='".$deal_product[$i]."' AND dealType='cpc' ORDER BY rdate DESC";
					  $chkDealCPC =  mysql_fetch_assoc(mysql_query($q));
					  
						if(!$chkDealCPC){
							$arrParams['rdate']        = dbDate();
							$arrParams['admin_id']     = $set->userInfo['id'];
							$arrParams['affiliate_id'] = $affiliate_id;
							$arrParams['product_id']  = $deal_product[$i];
							$arrParams['dealType']     = "cpc";
							$arrParams['amount']     = $deal_cpc[$i];
							
							$fields = implode(",",array_keys($arrParams));
							$values = implode(',', array_map(function($value) {
							if(!is_numeric($value)) {
						  
								return '"' . $value . '"';
							} else {
								return $value;
							}
							}, array_values($arrParams)));
						  $sql = "insert into products_affiliates_deals ($fields) values($values)";
						  
						  mysql_query($sql);
					  }
					  else{
						  $sql = "update products_affiliates_deals set rdate='". dbDate() ."' , amount=". $deal_cpc[$i] ."  where id= ". $chkDealCPC['id'];
						 mysql_query($sql);
					  }
					  }
					  
					  
					    if($deal_cpi[$i] != ""){
							
					  $chkDealCPI =  mysql_fetch_assoc(mysql_query("SELECT id, product_id,amount FROM products_affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND product_id='".$deal_product[$i]."' AND dealType='cpi' ORDER BY rdate DESC"));
						if(!$chkDealCPI){
						  
							$arrParams['rdate']        = dbDate();
							$arrParams['admin_id']     = $set->userInfo['id'];
							$arrParams['affiliate_id'] = $affiliate_id;
							$arrParams['product_id']  = $deal_product[$i];
							$arrParams['dealType']     = "cpi";
							$arrParams['amount']     = $deal_cpi[$i];
							
							$fields = implode(",",array_keys($arrParams));
							$values = implode(',', array_map(function($value) {
							if(!is_numeric($value)) {
								return '"' . $value . '"';
							} else {
								return $value;
							}
							}, array_values($arrParams)));
						  $sql = "insert into products_affiliates_deals ($fields) values($values)";
						  mysql_query($sql);
					  }
					  else{
						  $sql = "update products_affiliates_deals set rdate='". dbDate() ."' , amount=". $deal_cpi[$i] ."  where id= ". $chkDealCPI['id'];
						 mysql_query($sql);
					  }
					  }
			   }
            _goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $affiliate_id . '&ty=1'. "&toggleTo=deal_type");
            break;
	
	
	case "save_deal_tier":
	for($a=0;$a<$deal_count;$a++){
		$enums = get_enum_values("affiliates_deals",'dealType');
		$deals = getExistingDealTypesForAffiliateArray($deal_merchant[$a],$affiliate_id);
		foreach ($deals as $deal) {
				foreach ($enums as $enum) {
				if ($enum!='tier' && $deal==$enum && count($deals)>0 ){
					
					
					$sql = "select * from affiliates_deals where affiliate_id = " . $affiliate_id . " and merchant_id = " . $deal_merchant[$a] . " and dealType = '" . $deal . "' order by id desc limit 1;";
					$lastDealRow = mysql_fetch_assoc(mysql_query($sql));
					if ($lastDealRow['amount']<>0) {
					$sql = 	
	"INSERT INTO  `affiliates_deals` (`rdate` ,`admin_id` ,`merchant_id` ,`affiliate_id` ,`dealType` ,`amount` ,`tier_amount` ,`tier_pcpa` ,`tier_type`)VALUES (
 '".date('Y-m-d H:i:s')."',  ".$set->userInfo['id'].",  '".$deal_merchant[$a]."',  '".$affiliate_id."',  '".$deal."',  '0',  '',  '0',  '');";
 
					mysql_query($sql);
					}
				}
			}
		}
		        
		for ($i=0; $i<count($deal_ids); $i++) {
		            unset($db);
                    
                    if ($current_tier_type[$i] != $tier_deal_type) {
                        continue;
                    }
                    
                    if (!$deal_tier_amount[$i] AND $deal_cpa[$i] <= 0) {
                        continue;
                    }
                    
                    $db['id'] = $deal_ids[$i];
                    
					
					
					// $db['rdate'] = dbDate();  // caused a bug that deal from month x-1 got an update to x
					
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
                
				
		
				if ($tier_amount[$a] AND ($cpa[$a] > 0 OR $pcpa[$a] > 0)) {  
			
			
						$db['rdate'] = dbDate();
						$db['admin_id'] = $set->userInfo['id'];
						$db['affiliate_id'] = $affiliate_id;
						$db['merchant_id'] = $deal_merchant[$a];
						$db['dealType'] = 'tier';
						$db['tier_amount'] = $tier_amount[$a];
						$db['amount'] = $cpa[$a];
						$db['tier_pcpa'] = $pcpa[$a];
						// $db['tier_type'] = $tier_deal_type_new;
						
						$db['tier_type'] = 'ftd_amount';
						
						dbAdd($db, 'affiliates_deals');
					}
		}
                
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$affiliate_id.'&ty=1' . "&toggleTo=deal_type");
		break;
                
     
	 
	
	case "edit_note":
		$sql= "select * from " . $appNotes . " where id=" . $note_id;
		echo json_encode(mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__)));
		exit;
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
if ($db['notes']) {
			
			if ($userLevel=='admin' ||($set->allowDeleteCRMnoteForManager==1 || !isset($note_id)))
			dbAdd($db,$appNotes);
		}
		
		// if ($db['notes']) dbAdd($db,$appNotes);
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$affiliate_id.'&toggleTo=manager_notes_crm');
		break;
		
		
	case "remove_note":
if ($userLevel=='admin' ||  ($set->allowDeleteCRMnoteForManager==1)) {
		updateUnit($appNotes,"valid='0'","id='".$_GET['note_id']."'");
}
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$_GET['affiliate_id'] . "&toggleTo=manager_notes_crm");
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
				<li><a href="'.$set->SSLprefix.$userLevel.'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
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
		$qq=function_mysql_query($sql,__FILE__,__FUNCTION__); //  LIMIT $pgg,$getPos
		
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
		$fileName = $set->SSLprefix.$userLevel."/csv/report.csv";
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
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=profile_valid&id='.$db['id'].'\',\'profile_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "save_profile":
		if (!$db['url'] OR !$db['name']) _goto($set->SSLprefix.$set->basepage.'?act=new&id='.$db['affiliate_id'] ."&toggleTo=profiles");
		
		$db[rdate] = dbDate();
		$db[valid] = 1;
		dbAdd($db,$appProfiles);
		_goto($set->SSLprefix.$set->basepage.'?act=new&id='.$db['affiliate_id'].'&ty=1&toggleTo=profiles' );
		break;
	
	}

?>