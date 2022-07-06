<?php
require('common/global.php');
require('publisher/func/func_publisher.php');



if($set->activateLogs==1){
	$ip = getClientIp();

	function checkIPBlocked($ip){
		
		$sql = "select count(*) as cntIps from users_firewall where IPs like '%" . $ip. "%' and type='login' and valid=1";
		$ww = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__) );
		
		if($ww['cntIps'] > 0){
			return true;
		}
		return false;
		
	}
	
	if(checkIPBlocked($ip)){
			$activityLogUrl = "http://".$_SERVER['SERVER_NAME']. "/ajax/saveLogActivity.php?user_id=".$getAffiliate['id']."&ip=".$ip."&country=" . $country_id."&location=tracking&userType=publisher&theChange=Blocked user trying to enter the system&_function_=".__FUNCTION__ . "&_file_=". __FILE__ . "&queryString=" . urlencode($queryString);
			 //die($activityLogUrl);
			doPost($activityLogUrl);
			
			$url = 'http://'.$_SERVER['SERVER_NAME']."/404.php";
				// die ($url);
				header("Location: ".$url);
			die('--');
		}

	}
	
	
	function doPost($url){
		$parse_url=parse_url($url);
		$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
		if (!$da) {
			
			echo "$errstr ($errno)<br/>\n";
			echo $da;
			} else {
			//die($url);
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
			
	//var_dump($response);
	//die();
			// split the result header from the content
			$result = explode("\r\n\r\n", $response, 2);
			
			$content = isset($result[1]) ? $result[1] : '';
			//die ($content);
			return $content;
			
		}
	}


$refCookie = 'AB_refe_cookie';
$isIB = isset($_GET['IB']) ? $_GET['IB'] :  0;
$ibCookie = 'isIB';

$isDown = $set->blockAffiliateLogin;

if ($isDown==1 && $act!="")
	_goto($set->basepage);

if (!$_COOKIE[$refCookie] AND $_GET['ctag']) {
	setcookie($refCookie,$_GET['ctag'],time()+7776000);
}

if (!$_COOKIE[$isIB] AND $isIB) {
	setcookie($ibCookie,$isIB,time()+7776000);
}


$defaultGroupID_row  =  mysql_fetch_assoc(function_mysql_query("select id from groups where makedefault=1 and valid=1"));
$defaultGroupID  =  isset($defaultGroupID_row['id']) && !empty($defaultGroupID_row['id']) ? $defaultGroupID_row['id'] : 0;

// var_dump($_POST);
// die();

if(!$set->showPublisherModule){
	_goto('/');
}
function updateLang() {
	$lang='';
			if (isset($_GET['lang']) && !empty($_GET['lang'])) {
				$lang = strtoupper($_GET['lang']);
				updateUnit('admins',"lang='".($lang ? $lang : 'ENG')."',ip='".$set->userIP."',logged='1',lastvisit='".dbDate()."'","id='".$chk['id']."'");  //by nir 
				setcookie('setLang',$lang,time()+(24*60*60*30));
				//_goto($refeURL. '?lang='. $lang);
			}
}

$loginEventArray= array();

if ($chk['id'])
	$loginEventArray['error'] = false;
else 
	$loginEventArray['error'] = true;

	$loginEventArray['username'] = $username;
	$loginEventArray['password'] = $password;
	$loginEventArray['type'] = 'publisher';
	$loginEventArray['affiliate_id'] = $chk['id'];
	$loginEventArray['affiliate_valid'] = $chk['valid'];
	$loginEventArray['ip'] = $set->userIP;
	$loginEventArray['refe'] = $_SESSION['refe'];
	$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
	$lastOutCount = LoginEvent($loginEventArray,true,$set->numberOfFailureLoginsAttempts);
	
	if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
		$errors['errPass'] = 'block';
	}
	
	
$registerEventArray= array();

	$registerEventArray['error'] = false;

	$registerEventArray['username'] = $username;
	$registerEventArray['password'] = $password;
	$registerEventArray['type'] = 'publisher';
	$registerEventArray['affiliate_id'] = $chk['id'];
	$registerEventArray['affiliate_valid'] = $chk['valid'];
	$registerEventArray['ip'] = $set->userIP;
	$registerEventArray['refe'] = $_SESSION['refe'];
	$registerEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	$registerEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
	$lastRegOutCount = RegisterEvent($registerEventArray,true,4);
	if ($lastRegOutCount==4) {
		$errors_reg['errRegPass'] = 'block';
	}
	
switch ($act) {
	case "login":
		// var_dump($set);
		
		// $errors='';
		//echo '<br>', $username, '<br>', $password, '<br>';sleep(3);
		//coachkf
		//7862388236fd452ee56cd3414499c011
		// if ( isset($_GET['password']))
			// $pass = $_GET['password'];
		// else 
			// $pass = $password;
		
		
		
		if (!$username) $errors['username'] = 1;
		if (!$password) $errors['password'] = 1;
		
		// die ($password);
		
			
			
	if (empty($errors) && $lastOutCount<$set->numberOfFailureLoginsAttempts) {
			
$username = mysql_real_escape_string($username);
		$password = mysql_real_escape_string(str_replace(' ','',$password));
			
			//$resulta=function_mysql_query("SELECT id,username,password FROM affiliates WHERE lower(username)='".strtolower($username)."' AND password='".($admin ? $password : md5($password))."' AND valid='1'",__FILE__);
			//$chk=mysql_fetch_assoc($resulta);
								// $strSql = "SELECT id, username, password FROM affiliates  WHERE LOWER(username) = LOWER('" . strtolower(str_replace(' ',' ',$username)) . "') AND LOWER(password) = '" . (($admin>0 || $manager>0) ? strtolower($password):  strtolower(md5($password))) . "' AND valid = '1'";
								$strSql = "SELECT id, username, password,valid FROM admins  WHERE LOWER(username) = LOWER('" . strtolower($username) . "') AND (password) = '" . (($admin>0 || $manager>0) ? strtolower($password):  strtolower(md5($password))) . "' ";
				   // die ($strSql);
			$resulta = function_mysql_query($strSql,__FILE__);
			$chk     = mysql_fetch_assoc($resulta);
			

		if ($chk['id'] && $chk['valid']==0)
			$notValidUser = 1;
	
				$loginEventArray= array();
		if ($chk['id'] && $chk['valid']==1)
			$loginEventArray['error'] = false;
		else 
			$loginEventArray['error'] = true;
			$loginEventArray['username'] = $username;
			$loginEventArray['password'] = $password;
			$loginEventArray['affiliate_id'] = $chk['id'];
			$loginEventArray['type'] = 'publisher';
			$loginEventArray['affiliate_valid'] = $chk['valid'];
			$loginEventArray['ip'] = $set->userIP;
			$loginEventArray['refe'] = $_SESSION['refe'];
			$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
			$lastOutCount = LoginEvent($loginEventArray,false,$set->numberOfFailureLoginsAttempts);
			
			if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
				$errors['errPass'] = 'block';
			}
			
			
			
			
				
				// echo '0session_id: ' . $_SESSION['session_id'] . '<br>';
	// echo '0$chkid: ' . $chk['id'] . '<br><br>';
	
	// echo '0session_serial: ' . $_SESSION['session_serial'] . '<br>';
	// echo '0$session_serial: ' . $session_serial . '<br><br>';
	
	
	
	
	if ($admin && $lastOutCount<$set->numberOfFailureLoginsAttempts) {
				$_SESSION['session_id']     = $chk['id'];
				// $_SESSION['loggedin_time'] = trim(' ' . time()); 
				// $_SESSION['loggedin_time'] = ( time()); 
				
				// var_dump($_SESSION);
				// die();
				$_SESSION['session_serial'] = md5($chk['username'].$chk['password'].$chk['id']);
				_goto('/publisher/');
			}

			else if ( $chk['id'] && $lastOutCount<$set->numberOfFailureLoginsAttempts)  {
				$_SESSION['session_id']     = $chk['id'];
				// $_SESSION['loggedin_time'] = ( time()); 
				$_SESSION['session_serial'] = md5($chk['username'].$chk['password'].$chk['id']);
				
				
	
	
				if (!$admin) {
					updateUnit('admins',"lang='".($lang ? $lang : 'ENG')."',ip='".$set->userIP."',logged='1',lastvisit='".dbDate()."'","id='".$chk['id']."'");
					
					setcookie('setLang',$lang,time()+(24*60*60*30));
					 // die ($lang);
				}
				
		
				if ($_SESSION['refe']) {
					$refeURL = $_SESSION['refe'];
					unset($_SESSION['refe']);
					_goto($refeURL  );
				} else 
				{
					if($notValidUser)
					_goto('/index-pub.php/?v=1');
					else
						_goto('/publisher/');
				}
			} else {
				if ($lastOutCount<$set->numberOfFailureLoginsAttempts)
				$errors['errPass'] = 1;
			}
			
			
			
			
			
		} else {
			
			$loginEventArray= array();
			$loginEventArray['error'] = true;
			$loginEventArray['username'] = $username;
			$loginEventArray['password'] = $password;
			$loginEventArray['type'] = 'publisher';
			$loginEventArray['affiliate_id'] = $chk['id'];
			$loginEventArray['affiliate_valid'] = $chk['valid'];
			$loginEventArray['ip'] = $set->userIP;
			$loginEventArray['refe'] = $_SESSION['refe'];
			$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
			$lastOutCount = LoginEvent($loginEventArray,false,$set->numberOfFailureLoginsAttempts);
			if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
				$errors['errPass'] = 'block';
			}
			else
			$errors['errPass'] = 1;
		}
	default:
		
		// var_dump($_SESSION);
		// die();
		if (isPublisher()) {
			// die ('grgre');
			if ($_GET['refe']) _goto($_GET['refe']);
			}
		if ($_GET['refe']) $_SESSION['refe'] = $_GET['refe'];
		if ($h) $_SESSION['hideCom'] = 1;
		

		
		updateLang();
		/* 
		
		if (isset($_GET['lang'])) {
			// die ('lang: ' . $lang);
		// setcookie('setLang',$lang,time()+(24*60*60*30));
		
		
		$domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
		updateUnit('affiliates',"lang='".($lang ? $lang : 'ENG')."',ip='".$set->userIP."',lastvisit='".dbDate()."'","id='".$chk['id']."'");
		setcookie('setLang', $lang, time()+(24*60*60*30), '/', $domain, false);
		}

		 */

					
		if ($isDown==1) 
			$set->pageTitle = translateInnerWord(lang($set->webTitle),"Affiliate Program");
		else	 {
			
			if ($set->showTitleOnLoginPage){
				$set->pageTitle = lang('Join our affiliate network as a publisher and promote your creatives')	;
			}
		else  {
			$set->pageTitle = "";
		}
		}
		
	
	
		$set->content .='	<script>
  $(document).ready(function() {
$( "body" ).addClass( "affiliateLoginBody" );
$( "html" ).addClass( "affiliateLoginHtml" );

	});
</script>';

if(isset($_GET['v']) && $_GET['v'] == 1){
	$set->content .='	<script>
	$(document).ready(function() {
		 $.fancybox({ 
			 closeBtn:false, 
			  width:"250", 
			  height:"180", 
			  autoCenter: true, 
			  afterClose:function(){
				  key.focus();
			  },			  
			  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Dear Affiliate').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('This account is pending. <br/>Our account manager will contact you soon.'). '</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
			  });
	});
</script>';
}
			
		$set->content .= '
			<style type="text/css">

' . ($set->showTitleOnLoginPage==0 ? '
.titleOnPage {
	
	height:85px!important;

}


': '').'



			</style>
			<div class="affiliate_image" style="'.($set->affiliateLoginImage ?  "background-image:url('". $set->affiliateLoginImage. "');" : ''). ' ">';
							if ($isDown==1) {
								
															$set->content .= '
								<style>
							.affiliate_image {
									height: 293px!important;
									color: black!important;    
									font-size: 35px!important;
							}
							</style>';
								
							}
			
			
							if ($isDown==0) {
							$set->content .= '

<script>
function langRedirect(x) {
	window.location.href = "?lang=" +x;
	
	
}


	function checkUsername(){
			username = $("#username").val();
			var re = /^\w+$/;
			//if(/^[-a-zA-Z0-9@._:// \u0400-\u04FF]+$/.test(username) == false){
				if(/^[-a-zA-Z0-9@._:// \u00C0-\u1FFF\u2C00-\uD7FF]+$/.test(username) == false){
				if(username[0] == ".")
				{
					$(".loginerrors").text("'. lang("Username should start with '@' or any alphanumeric character.") .'");
				}
				else{
				$(".loginerrors").text("'. lang("Special characters are not allowed for Username.") .'");
				}
				return false;
			}
			else{
				$(".loginerrors").text("");
			}
			return true;
	}

</script>
							<div align="center" style="width: 989px; height: 336px;    margin-left: auto;    margin-right: auto; " class="topDivOnAffRegFrame">
					<table class="affiliateLoginFrame"   width="945" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="50%" align="left" valign="top" style="font-family: Arial; color: #333333; padding-top: 30px;">
							'. ($set->disableAutoCompleteOnLogin? '
							<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px;">'.lang('Sign In').'</div>
									<div style="color:red;padding-bottom:10px;" class="loginerrors"></div>
							<div style="font-size: 14px; color: '.($errors['username'] ? 'red' : '#616161').'; height: 20px;">'.lang('Username').':</div>
									<input type="text" name="username_new" id="username_new" required  value="" style="width: 342px; height: 35px; line-height: 35px; background: url(images/main/input_bg.png); border: 0; font-size: 16px;" />
									<div style="font-size: 14px; color: '.($errors['password'] ? 'red' : '#616161').'; padding-top: 5px; height: 20px;">'.lang('Password').':</div>
									<input type="password" required   name="password_new" id="password_new" value="" style="width: 342px; height: 35px; line-height: 35px; background: url(images/main/input_bg.png); border: 0; font-size: 16px;" />':'') .'
							'.($lastOutCount >= $set->numberOfFailureLoginsAttempts ? '<div style="color:red;padding-bottom:10px;" >'.lang('You have exceeded the number of allowed login attempts. Please try again in an hour.').'</div>' : '') . '
								<form id="affiliateLoginForm" action="index-pub.php/?act=login" method="post">
									'. ($set->disableAutoCompleteOnLogin? '
									<input type="hidden" id="hiddenUsername" name="username"/>
									<input type="hidden" id="hiddenPassword" name="password"/>' :'<div style="color:red;padding-bottom:10px;" class="loginerrors"></div>
							<div style="font-size: 14px; color: '.($errors['username'] ? 'red' : '#616161').'; height: 20px;">'.lang('Username').':</div>
									<input type="text" name="username" id="username" required  value="" style="width: 342px; height: 35px; line-height: 35px; background: url(images/main/input_bg.png); border: 0; font-size: 16px;" />
									<div style="font-size: 14px; color: '.($errors['password'] ? 'red' : '#616161').'; padding-top: 5px; height: 20px;">'.lang('Password').':</div>
									<input type="password" required   name="password" id="password" value="" style="width: 342px; height: 35px; line-height: 35px; background: url(images/main/input_bg.png); border: 0; font-size: 16px;" />').'
									
									
									'.($set->multi ? '<select onchange="langRedirect(this.value)" name="lang" style="width: 352px; margin-top: 10px;"><option value="">'.lang('Choose your language').'</option>'.listMulti($lang).'</select>' : '').'
									<div style="font-size: 12px;   padding-top: 4px;">'.lang('Forgot your').' <a href="index-pub.php?act=forgot_password'.($lang ? '&lang='.$lang:'').'" style=color: #0d599a;">'.lang('Username').'</a> '.lang('or').' <a href="index-pub.php?act=forgot_password'.($lang ? '&lang='.$lang:'').'" style="color: #0d599a;">'.lang('Password').'</a>?</div>
									<div style="padding: 5px 0 10px 0;"><input type="checkbox" /> <span style="">'.lang('Stay signed in').'</span> <span style="color: #918F8F;">('.lang("Uncheck if you're on a shared computer").')</span></div>
									<input type="submit"onclick= " return checkUsername()"  value="'.lang('Sign In').'" />
								</form>
								'.(strlen($errors['errPass'])>0 ? '<span style="color: red; font-weight: bold;">'.lang('Login Incorrect!'). '  ' . ($errors['errPass']=='block' ? '' : "" ).'</span>' : '').'
							</td>
							<td width="50%" align="left" valign="top" style="font-family: Arial; color: #333333; padding-top: 30px;">
								<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px; padding-left: 20px;">'.lang('Open an Account').'</div>
								<div align="center" style="padding-top: 40px;" class="regRight">
									<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px;">'.lang("Don't have an account yet?").'</div>
									<div style="padding-bottom: 10px;">'.lang('Get started now. it\'s fast and easy!').'</div>
									<input style="margin-left: auto; margin-right: auto;" type="submit" value="'.lang('Register').'" onclick="location.href=\'index-pub.php/?act=new_account'.($lang ? '&lang='.$lang:'').'\';" />
								</div>
							</td>
						</tr>
					</table>
				</div>
				'. ($set->disableAutoCompleteOnLogin? '
				<script>
				 $("#affiliateLoginForm").submit(function() {
					$("#hiddenUsername").val($("#username_new").val());
					$("#hiddenPassword").val($("#password_new").val());
				  });
				  $("#username_new,#password_new").keypress(function(e) {
					if (e.which == 13) {
					  $("#affiliateLoginForm").submit();
					}
				  });
				</script>':'');
						}	else{
							

							
							
						$set->content .= '
							<div class="mainMsg">
						<div style="font-size:30px;">'.lang('The site is currently down for maintenance').'</div>
						<div style="font-size:25px;">'.lang('Tracking is working as usual.').'</div>
						<div style="font-size:25px;">'.lang('We expect to be back soon. Thanks for your patience.').'</div>
						</div>
					
						</tr>
					</table>
				</div>
					';
						}
							$set->content .= '
			</div>
			<div class="loginPageFooter" style="' . (!empty($set->affiliateLoginImage) ? "" : " margin-top: 0px;" ) . '">
				<table width="989" border="0" cellpadding="0" cellspacing="0" style="    margin-left: auto;    margin-right: auto;">
				<tr>
				'. ($set->hideBrandsDescriptionfromAffiliateFooter==1 ? '':'
					<td align="left" style="font-size: 11px; color: #746d6d; font-family: Arial; text-align: justify;width:50%;">
						<b>'.$set->webTitle.'</b> - The official '.$set->webTitle.' Affiliation - place you in the perfect position to claim your share of one of the most lucrative industries online. Over 3 trillion dollars is traded every day in the financial markets and '.$set->webTitle.' offers you the most respected and rewarding brands to help you convert your web traffic into an unlimited source of revenue.
					</td>
					')
					. ($set->hidePoweredByABLogo==0 ? '<td style="padding-left:15px;" align="right"><a href="http://www.affiliatebuddies.com/" target="_blank"><img border="0" src="images/main/powered.png" alt="Powered by AffiliateBuddies" /></a></td>' : '') 
					. (!empty($set->secondaryPoweredByLogo) ? '<td  align="center"><a href="'.$set->secondaryPoweredByLogoHrefUrl.'" target="_blank"><img border="0" src="'. $set->secondaryPoweredByLogo .'" alt="Powered by AffiliateBuddies" /></a></td>' : '') 
					. (!empty($set->brandsPoweredbyText) ? '<td  align="left" style="padding-left:15px;">'.$set->brandsPoweredbyText.'</td>' : '') . '
				</tr></table>
			</div>
			';
		
		theme(0,'mainHomePage');
		break;

	case "new_account":
		
		$str_error = '';
			
		if(isset($_POST['db'])){

		$chkUser = @mysql_fetch_assoc(function_mysql_query("SELECT id,valid FROM merchants WHERE lower(APIuser)='".strtolower($db['username'])."'",__FILE__));
		if ($chkUser['id']) $errors['username'] = lang('Username already exist');
		if (!$db['username']) $errors['username'] = lang('Username already exist');
		if ($password != $repassword) $errors['password'] = lang('Passwords not match');
		if (!$password OR !$repassword) $errors['password'] = lang('Please fill out your password');
		$chkMail = mysql_fetch_assoc(function_mysql_query("SELECT id FROM merchants WHERE lower(email)='".strtolower($db['mail'])."'",__FILE__));
		if ($chkMail[id]) $errors['mail'] = lang('This e-mail already exist in our records');
		if (!validMail($db[mail])) $errors['mail'] = lang('Invalid E-mail');
		if (!$db[mail]) $errors['mail'] = lang('Please fill your E-mail');
		if (!$db[first_name]) $errors['first_name'] = lang('Please fill out your first name');
		if (!$db[last_name]) $errors['last_name'] = lang('Please fill out your last name');
		if (!$db[phone]) $errors['phone'] = lang('Please fill out your phone');
		if (!$db[company]) $errors['company'] = lang('Please fill out your company');
		if (!$db[website]) $errors['website'] = lang('Please fill out your website');
		if (!$approve) $errors['approve'] = lang('Please approve the Terms & Conditions');
		
		if ($set->allowCapthaOnReg && !chkSecure($code)) $errors['secureCode'] = lang('Please type the capcha currently');
		
		if (!empty($errors)) {
			foreach ($errors as $errItem => $errMessage) {
				$str_error .= $errItem.'|' . $errMessage ;
				break;
			}
		}
		else if(empty($errors)  && $lastRegOutCount<4){
			$registerEventArray= array();
			$registerEventArray['error'] = false;

			$registerEventArray['username'] = $db['username'];
			$registerEventArray['password'] = $db['password'];
			$registerEventArray['type'] = 'publisher';
			$registerEventArray['affiliate_id'] = $chkUser['id'];
			$registerEventArray['affiliate_valid'] = $chkUser['valid'];
			$registerEventArray['ip'] = $set->userIP;
			$registerEventArray['refe'] = $_SESSION['refe'];
			$registerEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			$registerEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
			$lastRegOutCount = RegisterEvent($registerEventArray,false,4);
			
			if ($lastRegOutCount==4) {
				$errors_reg['errRegPass'] = 'block';
			}
			
			
			$db['APIpass'] = md5($password);
			$db['APIuser'] = $db['username'];
			$db['rdate'] = dbDate();
			$db['valid'] = '0';
			
			
			
			// Strip Tags
			$db['first_name'] = strip_tags($db['first_name']);
			$db['last_name'] = strip_tags($db['last_name']);
			
			$db['company'] = strip_tags($db['company']);
			$db['website'] = strip_tags($db['website']);
			$db['email'] = strip_tags($db['mail']);
			
			
			$db['name'] = $db['first_name'] . " " . $db['last_name'];
			$db['isSelfManaged'] = 1;
			
			
			
			$admin['rdate'] = dbDate();
			$admin['valid'] = 0;
			$admin['level'] = 'publisher';
			$admin['username'] = $db['username'];
			$admin['password'] = $db['APIpass'];
			$admin['first_name'] = $db['first_name'];
			$admin['last_name'] = $db['last_name'];
			$admin['email'] = $db['email'];
			$admin['group_id'] = -1;
			$admin['phone'] = $db['phone'];
			$admin['ip'] = $set->userIP;
			
			
			unset($db['username']);
			unset($db['password']);
			unset($db['first_name']);
			unset($db['last_name']);
			unset($db['mail']);
			
			//no company & phone field in database
			unset($db['company']);
			unset($db['phone']);
			
			$lastID = dbAdd($db, 'merchants');
            
			$admin['relatedMerchantID'] = $lastID;
			
			$lastID = dbAdd($admin, 'admins');
			
            
			sendTemplate('MerchantAccountReview',$lastID,0,'',0,0);
					 echo '
			'.lang('Please Wait...').'
			<meta http-equiv="Refresh" content="2; URL="index-pub.php?act=new_account&ty=1"/>';
			break;

			echo $set->affiliateRegistrationPixel;
		}
		else{		
	
			if ($lastRegOutCount<=4)
				$errors_reg['errRegPass'] = 1;
		}
			

				
			}
			
		
		if ($group_id AND !$_SESSION['group_id_aff']) $_SESSION['group_id_aff'] = $group_id;
		$set->pageTitle = lang('Open a New Affiliate Account');
		
		$hideMarketingField = $set->hideInvoiceSectionOnAffiliateRegPage==0 || $set->hideMarketingSectionOnAffiliateRegPage==0;
		

		if(!isset($errors_reg['errRegPass'])){
		
		
		if (count($errors) > 0) $set->content .= '<div align="left" style="width: 970px; color: red;">
			<b>'.lang('Please check one or more of the following fields:').'</b><br />
			<ul type="*"><li />'.implode('<li />',$errors).'</ul>
		</div>'
		;
		
		
		
		$refer = $_SERVER['HTTP_REFERER'];
		
		
		$set->content .= '
			<style>
			
td{
	
	text-transform: capitalize;
}


		.titleOnPage {
			margin-top: -42px!important;
		}		
		.pageTitle {
    background: url(\'\')!important;
	/* background-color: black!important; */
	color:black;
    
}
			
		.title {
			color: white!important;
		}


	.fancybox-skin{
		border:2px solid grey;
	}

		</style>';
		
		$set->content .= '<form id="frmAffReg"action="index-pub.php/?act=new_account'.($_GET['debug']==1 ? "&debug=1" : "").'" method="post" onsubmit="return checkReg();" autocomplete="off" class="regform" style="'. 
		($set->affiliateLoginImage ?  "background-image:url('". $set->affiliateLoginImage. "');
		
		 no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
		
		" : '') . '">
		
		<table width="975" border="0" cellpadding="0" cellspacing="0" style="background-color: white;    opacity: 0.999;    border-radius: 13px;    padding: 5px;">
			<!--<tr>
				<td colspan="3" height="10">'.$str_error.'</td>
			</tr>--><tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr>
					<td style="text-transform:uppercase;padding:10px;background-color:#f5f5f5;font-size:14px;text-align:center;font-weight:bold;">
					'. lang('Publishers Registration Page') .'
					</td>
					</tr>
					<tr>
					<td style="background-color:#000;color:white;text-transform:uppercase;padding:10px;">
					'. lang('Account Information') .'
					</td>
					</tr>
						<tr>
							<td height="225" valign="top" >
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tr>
									<td colspan="2" height="10"></td>
									</tr>
								
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('First Name').':</td>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[first_name]" id="first_name" value="'.$db['first_name'].'" style="width: 280px;" /></td>
									</tr>
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Last name').':</td>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[last_name]" id="last_name" value="'.$db['last_name'].'" style="width: 280px;" /></td>
									</tr>
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('E-mail').':</td>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[mail]" value="'.$db['mail'].'" id="mail" style="width: 280px;" /></td>
									</tr>
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Website').':</td>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website]" id="website" value="'.($db['website'] ? $db['website'] : 'http://').'" style="width: 280px;" /></td>
									</tr>
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Company Name').':</td>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[company]" id="company" value="'.$db['company'].'" style="width: 280px;" /></td>
									</tr>
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span   class="required">*</span> '.lang('Phone number').':</td>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[phone]" value="'.$db['phone'].'" id="phone" style="width: 280px;" /></td>
									</tr>
										<tr>
									<td colspan="2" height="10"></td>
									</tr>
										<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Username').':</td>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[username]" id="username" style="width: 280px;" readonly/><div class="question tooltip"><span class="tooltiptext">'.lang('The username will be set automatically by the website url.').'</span></div></td>
									</tr>
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Password').':</td>
										<td align="left" style="padding-left: 10px;"><input type="password" name="password" id="password" value="" style="width: 280px;" /></td>
									</tr>
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Repeat Password').':</td>
										<td align="left" style="padding-left: 10px;"><input type="password" name="repassword" id="repassword" value="" style="width: 280px;" /></td>
									</tr>
								
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
									<td></td>
										' .
										(!$set->allowCapthaOnReg? "" : '
									<td  align="left" style="padding-left: 10px;">'.secureCode().'</td>'  ) . '
									</tr>
									<tr>
									<td colspan="2" height="10"></td>
									</tr>
									<tr>
									<td colspan="2" align="left">
										<input type="checkbox" name="approve" id="approve" /> '.lang('I have read and accepted the').' <a class="inline" href="#termsPop">'.lang('Terms & Conditions').'</a> '. ((!empty($set->extraAgreement2Name) && !empty($set->extraAgreement2Link)) ? lang('&') .' ' . '<a href="'.$set->extraAgreement2Link.'" target="_blank">'.lang($set->extraAgreement2Name).'</a> '  :  '') . '  '. ((!empty($set->extraAgreement3Name) && !empty($set->extraAgreement3Link)) ? lang('&') .' ' . '<a href="'.$set->extraAgreement3Link.'" target="_blank">'.lang($set->extraAgreement3Name).'</a> '  :  '') .'.<br />
									</td>
									</tr>
									<tr>
									<td></td>
									<td align="right" colspan=2 height=30>
										<input class="register_now_btn" type="submit" value="'.lang('Register Now').'" style=" margin-left: -202px !important;"/>
									</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td>
			</tr><tr>
				<td colspan="3" height="10"></td>
			</tr>
				
		</table>
		<script>
		$("input").on("blur",function(){
			
			if($("#website").val()){
				url = toLocation($("#website").val());
				url = url.hostname;
				if(url.indexOf("www") >= 0){
					arrURL = url.split(".");
					url = arrURL[1] + "."+arrURL[2];
				}
				$("#username").val(url);
			}
			
		});
		function toLocation(url) {
				var a = document.createElement("a");
				a.href = url;
				return a;
			};
        $(".inline").colorbox({inline:true,border: "1px black solid" ,height: "400px", width:"50%"});

        $(".inline").click(function() {
            $("#termsPop").show();
        });

        $(document).bind("cbox_closed", function() {
            $("#termsPop").hide();
        });
		
    </script>
		';

		
			
			
		$set->content .='<script>
		function checkReg() {
	
	var check_ids = new Array("username","password","repassword","first_name","last_name","mail", "website","company","phone","code");
	var names = new Array("'.lang('Username').'","'.lang('Password').' ","'.lang('Repeat Password').'","'.lang('Last Name').'","'.lang('First Name').'","'.lang('E-mail').'","'. lang('Website') . '","'. lang('Company') . '","'.lang("Phone") . '","'.lang('Code').'");
	var check_ids_length = check_ids.length;
	for (i = 0; i < check_ids_length; i++) {
		key = $("#"+check_ids[i]);
		keyval = key.val();
		if(check_ids[i] == "website")
		{
			if(key.val() === "http://" || key.val() === "https://")
			{
				keyval ="";
			}
		}
		if (keyval=="") {
			//alert("Please fill out "+names[i]);
			  $.fancybox({ 
			 closeBtn:false, 
			  minWidth:"250", 
			  minHeight:"180", 
			  autoCenter: true, 
			  afterClose:function(){
				  key.focus();
			  },			  
			  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Please fill in&nbsp;'). '" + names[i] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
			  });
			  key.focus();
			return false;
		}
		else{
			if(check_ids[i] != "password" || check_ids[i] != "repassword"){
					val =key.val(); 
					//if (val.match(/^[\w@].+$/) == null)
					//if(/^[a-zA-Z0-9@._:// \u00C0-\u1FFF\u2C00-\uD7FF]+$/.test(val) == false)
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
					  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +msg + " "+ names[i] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
					  });
					  key.focus();
					return false;
				}
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
		  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Your e-mail is not valid.').'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' value=\''.lang('Continue').'\'  onClick=\'$.fancybox.close()\'></div></div>" 
		  });
		
		return false;
		}
	if ($("#password").val() != $("#repassword").val()) {
//		alert("Password does not match");
		$.fancybox({ 
		 closeBtn:false, 
		minWidth:"250", 
			  minHeight:"180", 
		  autoCenter: true, 
		  afterClose:function(){
			 $("#password").focus();
		  },			  
		  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Password does not match.').'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' value=\''.lang('Continue').'\'  onClick=\'$.fancybox.close()\'></div></div>" 
		  });
		$("#password").focus();
		return false;
		}
	
	if (!($("#approve").is(":checked"))) {
		//alert("Your have to approve the terms & conditions");
		$.fancybox({ 
		 closeBtn:false, 
		  minWidth:"250", 
			  minHeight:"180", 
		  autoCenter: true, 
		  afterClose:function(){
			 $("#approve").focus();
		  },
		 
		  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Your have to approve the terms & conditions.').'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' value=\''.lang('Continue').'\'  onClick=\'$.fancybox.close()\'></div></div>" 
		  });
		$("#approve").focus();
		return false;
		}
	}
	</script>
		';
		$set->content .='<script>
		$(document).ready(function(){
	$("form#frmAffReg").on("submit",function(){
	$("form#frmAffReg input[type=\"text\"]").each(function(){
        
        if (this.value.match(/[a-z0-9]+/i))
		{
			this.value.focus();
			return false;
		}
	});
		return true;
	});
});
		</script>';
			$set->content .= "<script type=\"text/javascript\">
			function closemodal() {
			
				
				$('#colorbox').hide();
			$('#cboxOverlay').hide();
				$('#termsPop').hide();
			}
		</script>
		<style>
		.question{
			background: url('../images/question.png');
			background-repeat:no-repeat;
			width:16px;
			height:16px;
			top:4px;
			left:5px;
		}
		
		.tooltip {
			position: relative;
			display: inline-block;
		}
		
		.tooltip .tooltiptext {
			visibility: hidden;
			width: 200px;
			bottom: 100%;
			left: 50%; 
			margin-left: -100px;
			background-color: black;
			color: #fff;
			text-align: center;
			padding: 5px;
			border-radius: 6px;
			position: absolute;
			z-index: 1;
			opacity:0.8;
		}

		.tooltip:hover .tooltiptext {
			visibility: visible;
		}
		</style>
		";
		
		
		$set->content .= '<div id="termsPop" style="display: none; border: "1px black solid" ,height: "220px", width:"250px;">
		<!--a href="javascript:void(0)"><img src="images/x_btn.png" style="    float: right;    margin-top: -17px;    z-index: 10000;"/></a-->
            <center>
            <h2><u>' . lang('Terms and conditions') . '</u></h2>
            
									<iframe style="width:95%   ; height: 272px!important;" src="'.$set->merchants_terms_link.'"></iframe>
							
			
            </center>
    </div>';
		
		
		if ($ty) $set->content = '
		<div align="left" style="width: 940px; margin-left: -10px; color: green; font-size: 16px; font-weight: bold; border-radius: 3px; color: #FFF; background: #000 url(/images/approved.png) no-repeat left; border: 1px #000 solid; padding: 30px;">
			<div style="padding-left: 130px;">
				'.lang('Thank you for registering with').' '.$set->webTitle.'<br />
				<br />
				'.lang('Your account be will activated as soon as it is approved').'
			</div>
		</div>';
		}
		else{
			
			$set->content = '<h2 width="975">'. lang('Sorry!!!! You are blocked.') .'</h2>';
			
		}
		theme();
		break;
		
		
	// --------------------------------- [ Forgot Password ] --------------------------------- //
	
	case "send_password":
		$username = clearInjection($username);
		$mail = clearInjection($mail);
		
		$getMail=mysql_fetch_assoc(function_mysql_query("SELECT id,mail,username,first_name FROM admins WHERE lower(username)='".strtolower($username)."' OR lower(mail)='".strtolower($mail)."'",__FILE__));
		if (!$getMail['id'] AND $username) $errors['err'] = lang('Username no exist!');
		if (!$getMail['id'] AND $mail) $errors['err'] = lang('E-mail no exist!');
		if (!$username AND !$mail) $errors['err'] = lang('Please fill out username or e-mail');
		if ($set->allowCapthaOnReset && !chkSecure($code)) $errors['err'] = lang('Please type the capcha currently');
		if ($errors) {
			} else {
			$abc= array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"); 
			$abcBig= array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"); 
			$new_password = rand(0,9).$abc[rand(0,25)].$abc[rand(0,25)].$abcBig[rand(0,25)].rand(0,4).rand(0,9).$abcBig[rand(0,25)];
			updateUnit("admins","password='".md5($new_password)."'","id='".$getMail['id']."'");
			$set->sendTo = $getMail['mail'];
			$set->subject = $getMail['first_name'].' - '.lang('Password Reset');
			// $mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode FROM mail_templates WHERE id=-1',__FILE__));
			$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="ResetPassword" and valid=1',__FILE__));
			sendTemplate($mailCode['mailCode'],$getMail['id'],0,$new_password);
			
			/*
			$set->body .= 'Dear '.$getMail['first_name'].',<br />
				This email has been sent automatically by '.$set->webTitle.' in response to your request to recover your password.<br />
				<br />
				<u>Your username is:</u> <b>'.$getMail['username'].'</b><br />
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
			_goto($set->basepage.'index_pub.php?act=forgot_password&ty=1'.($lang ? '&lang='.$lang:''));
			}
			
	case "forgot_password":
		$set->pageTitle = lang('Change Your Password');


	
		$set->content .='	<script>
  $(document).ready(function() {
// $( "div.loginPageFooter" ).addClass( "loginPageFooter_forgot" );
$(".loginPageFooter").attr("id", "loginPageFooter_forgot");

	});
</script>';


		
		$set->content .= '
			<style type="text/css">
				html,body {
					background: #f5f5f5 !important;
					}
				.smallBar {
					height:0px!important;
				}	
				.titleOnPage {
					height: 157px!important;
				}

form#resetform {
    margin-left: 80px;
    margin-top: -50px;
}


			</style>
			
			
			
			<div class="coverImageBlock" style="'. ($set->affiliateLoginImage ?  "background-image:url('". $set->affiliateLoginImage. "');" : '"') .'>
				<div align="center" style="width: 989px; height: 336px; background-color: url(images/main/bg.png);">
					<table width="945" border="0" cellpadding="0" cellspacing="0" style="background-color: white;      margin-top: 6%;  opacity: 0.999;    padding: 12px 7px 12px 7px;    border-radius: 8px;">
						<tr>
							<td width="50%" align="left" style="font-family: Arial; color: #333333; padding-top: 30px;">
								
								
								
								<form id="resetform" action="'.$set->basepage.'" method="post">
								
								<input type="hidden" name="act" value="send_password" />
									<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px;">'.lang('Forgot your password?').'</div>
									<span class="required">'.$errors['err'].'</span>
									'.($ty ? '<b>'.lang('Password has sent to your inbox!').'</b><br /><a href="/">'.lang('Click here to login').'</a>' : '
									<div style="font-size: 14px; color: #616161; height: 20px;">'.lang('Username').':</div>
									<input type="text" name="username" value="" style="width: 342px; height: 35px; line-height: 35px; background: url(images/main/input_bg.png); border: 0; font-size: 16px;" />
									
									
									
									<div align="left" style="font-weight: bold; font-size: 20px; padding: 15px 0 0 160px;">'.lang('OR').'</div>
									
									
									<div style="font-size: 14px; color: #616161; padding-top: 5px; height: 20px;">'.lang('E-Mail').':</div>
									
									<input type="text" name="mail" value="" style="width: 342px; height: 35px; line-height: 35px; background: url(images/main/input_bg.png); border: 0; font-size: 16px;" />
									<div style="padding-top: 5px;">'. (!$set->allowCapthaOnReset ? "" : '
									'.secureCode().''  ) . '
									<div style="padding-top: 10px;">
										<input class="resetb" type="submit"  value="'.lang('Reset Password').'" />
	
									</div>

									
								</form>').'
								
								
								
								
								<div style="padding-top: 3px;"><a href="index-pub.php" style="color: #0D599A;">« '.lang('Back to login').'</a></div>
							</td>
							<td width="50%" valign="middle" align="center" style="font-family: Arial; color: #333333;">
								<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px;">'.lang("Don't have an account yet?").'</div>
								<div style="padding-bottom: 10px;">'.lang("Get started now. it's fast and easy!").'</div>
								<a href="index-pub.php?act=new_account'.($lang ? '&lang='.$lang:'').'">
								<input class="registerbtn"  type="button" value="'.lang('Register').'" />
								
								</a>
								
							</td>
						</tr>
					</table>
				</div>';
	
		
		
			$set->content .= '
			<div class="loginPageFooter" style="' . (!empty($set->affiliateLoginImage) ? "" : " margin-top: 0px;" ) . '">
				<table width="989" border="0" cellpadding="0" cellspacing="0">
				<tr>
				'. ($set->hideBrandsDescriptionfromAffiliateFooter==1 ? '':'
					<td align="left" style="font-size: 11px; color: #746d6d; font-family: Arial; text-align: justify;">
						<b>'.$set->webTitle.'</b> - The official '.$set->webTitle.' Affiliation - place you in the perfect position to claim your share of one of the most lucrative industries online. Over 3 trillion dollars is traded every day in the financial markets and '.$set->webTitle.' offers you the most respected and rewarding brands to help you convert your web traffic into an unlimited source of revenue.
					</td>'
					 ) 
					. ($set->hidePoweredByABLogo==0 ? '<td width="300" align="right"><a href="http://www.affiliatebuddies.com/" target="_blank"><img border="0" src="images/main/powered.png" alt="Powered by AffiliateBuddies" /></a></td>' : '') 
					. (!empty($set->secondaryPoweredByLogo) ? '<td width="300" align="right"><a href="'.$set->secondaryPoweredByLogoHrefUrl.'" target="_blank"><img border="0" src="'. $set->secondaryPoweredByLogo .'" alt="Powered by AffiliateBuddies" /></a></td>' : '') . '
				</tr></table>
			</div>
			</div>';
			
			
		theme();
		break; 
	
	
	
	
	}

function get_domain($url)
{
  $pieces = parse_url($url);
  $domain = isset($pieces['host']) ? $pieces['host'] : '';
  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
	  return $regs['domain'];
  }
  return false;
}	
	
?>

