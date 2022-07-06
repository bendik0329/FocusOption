<?php
require('common/global.php');
require('affiliate/func/login.php');


$selectedTextDirection = "LTR";
if(isset($_GET['lang'])){
	$selectedLang = $_GET['lang'];
	$selectedDirection = mysql_fetch_assoc(function_mysql_query("select textDirection from languages where lngCode ='". $_GET['lang'] ."'",__FILE__,__FUNCTION__));
	$selectedTextDirection = $selectedDirection['textDirection'];
		
}

if($set->activateLogs==1){
	
		$ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
if (empty($ip))
$ip	= getRealIP();
if (empty($ip))
	$ip = getClientIp();

	if(checkUserFirewallIP($ip)){

		
			$activityLogUrl = "http://".$_SERVER['SERVER_NAME']. "/ajax/saveLogActivity.php?user_id=000&ip=".$ip."&country=" . $country_id."&location=loginAff&userType=affiliate&theChange=Blocked user trying to enter the system&_function_=".__FUNCTION__ . "&_file_=". __FILE__ . "&queryString=" . urlencode($queryString);
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

function updateLang() {
	$lang='';
			if (isset($_GET['lang']) && !empty($_GET['lang'])) {
				$lang = strtoupper($_GET['lang']);
				updateUnit('affiliates',"lang='".($lang ? $lang : 'ENG')."',ip='".$set->userIP."',logged='1',lastvisit='".dbDate()."'","id='".$chk['id']."'");  //by nir 
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
	$loginEventArray['type'] = 'affiliate';
	$loginEventArray['affiliate_id'] = $chk['id'];
	$loginEventArray['affiliate_valid'] = $chk['valid'];
	$loginEventArray['ip'] = $set->userRealIP;
	$loginEventArray['login_as_affiliate_by_user_id'] = isset($_GET['admin']) ? $_GET['admin'] : (isset($_GET['manager']) ? $_GET['manager'] : 0);
	$loginEventArray['refe'] = $_SESSION['refe'];
	$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
	$lastOutCount = LoginEvent($loginEventArray,true,$set->numberOfFailureLoginsAttempts);
	
	if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
		$errors_log['errPass'] = 'block';
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
	$loginEventArray['login_as_affiliate_by_user_id'] = isset($_GET['admin']) ? $_GET['admin'] : (isset($_GET['manager']) ? $_GET['manager'] : 0);
	$registerEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	$registerEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
	$lastRegOutCount = RegisterEvent($registerEventArray,true,4);
	if ($lastRegOutCount==4) {
		$errors_reg['errRegPass'] = 'block';
	}

switch ($act) {
    case 'signin':
        include('signin.php');
        break;
	case "login":
		
        if (!$username)
            $errors['username'] = 1;
        if (!$password)
            $errors['password'] = 1;
			
			
			
	if (empty($errors) && $lastOutCount<$set->numberOfFailureLoginsAttempts) {
			
$username = mysql_real_escape_string($username);

		$password = mysql_real_escape_string(str_replace(' ','',$password));
$username = strip_tags($username);
$password = strip_tags($password);
			


								$strSql = "SELECT id, username, password,valid,emailVerification FROM affiliates  WHERE LOWER(username) = LOWER('" . strtolower($username) . "') AND (password) = '" . (($admin>0 || $manager>0) ? strtolower($password):  strtolower(md5($password))) . "' ";
			$resulta = function_mysql_query($strSql,__FILE__);
			$chk     = mysql_fetch_assoc($resulta);
			
				if($set->BlockLoginUntillEmailVerification==1 && $chk['emailVerification']==0){
					$errors_log['errEmailVerify'] = 1;
				}else{
				
                if ($chk['id'] && $chk['valid'] == 0) {
			$notValidUser = 1;
                }
								$loginEventArray= array();
                if ($chk['id'] && $chk['valid'] == 1) {
			$loginEventArray['error'] = false;
                } else {
			$loginEventArray['error'] = true;
                }
							$loginEventArray['username'] = $username;
							$loginEventArray['password'] = $password;
							$loginEventArray['affiliate_id'] = $chk['id'];
							$loginEventArray['type'] = 'affiliate';
							$loginEventArray['affiliate_valid'] = $chk['valid'];
							$loginEventArray['ip'] = $set->userRealIP;
							$loginEventArray['refe'] = $_SESSION['refe'];
							$loginEventArray['login_as_affiliate_by_user_id'] = isset($_GET['admin']) ? $_GET['admin'] : (isset($_GET['manager']) ? $_GET['manager'] : 0);
							$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
							$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
							$lastOutCount = LoginEvent($loginEventArray,false,$set->numberOfFailureLoginsAttempts);
							
							if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
								$errors_log['errPass'] = 'block';
							}
					
					
					if ($admin && $lastOutCount<$set->numberOfFailureLoginsAttempts) {
								$_SESSION['aff_session_id']     = $chk['id'];
								// $_SESSION['loggedin_time'] = trim(' ' . time()); 
								// $_SESSION['loggedin_time'] = ( time()); 
								// var_dump($_SESSION);
								// die();
								$_SESSION['aff_session_serial'] = md5($chk['username'].$chk['password'].$chk['id']);

                    if (!empty($_POST['ajax'])) {
                        echo json_encode(['result' => 'success', 'message' => 'ok']);
                        die();
							}

                    _goto('/affiliate/');
                } else if ($chk['id'] && $lastOutCount < $set->numberOfFailureLoginsAttempts) {
								$_SESSION['aff_session_id']     = $chk['id'];
								// $_SESSION['loggedin_time'] = ( time()); 
								$_SESSION['aff_session_serial'] = md5($chk['username'].$chk['password'].$chk['id']);
								
								
					
					
								if (!$admin) {
									updateUnit('affiliates',"lang='".($lang ? $lang : 'ENG')."',ip='".$set->userIP."',logged='1',lastvisit='".dbDate()."'","id='".$chk['id']."'");
									
									setcookie('setLang',$lang,time()+(24*60*60*30));
									 // die ($lang);
								}
								
						
								if ($_SESSION['refe']) {
									$refeURL = $_SESSION['refe'];
									unset($_SESSION['refe']);
                        if (!empty($_POST['ajax'])) {
                            echo json_encode(['result' => 'redirect', 'message' => $refeURL]);
                            die();
                        }
									_goto($refeURL  );
								} else {
if (!empty($_POST['ajax'])) {
                            echo json_encode(['result' => 'success', 'message' => 'ok']);
                            die();
                        }
                        if ($notValidUser) {
									_goto('/affiliate/?v=1');
                        } else {
										_goto('/affiliate/');
                        }
								}
							} else {
								if ($lastOutCount<$set->numberOfFailureLoginsAttempts)
								$errors_log['errPass'] = 1;
							}
				}
		} else {
			
			$loginEventArray= array();
			$loginEventArray['error'] = true;
			$loginEventArray['username'] = $username;
			$loginEventArray['password'] = $password;
			$loginEventArray['type'] = 'affiliate';
			$loginEventArray['affiliate_id'] = $chk['id'];
			$loginEventArray['affiliate_valid'] = $chk['valid'];
			$loginEventArray['login_as_affiliate_by_user_id'] = isset($_GET['admin']) ? $_GET['admin'] : (isset($_GET['manager']) ? $_GET['manager'] : 0);
			$loginEventArray['ip'] = $set->userIP;
			$loginEventArray['refe'] = $_SESSION['refe'];
			$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
			$lastOutCount = LoginEvent($loginEventArray,false,$set->numberOfFailureLoginsAttempts);
			if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
				$errors_log['errPass'] = 'block';
			}else
			$errors_log['errPass'] = 1;
		}

		if (!empty($_POST['ajax'])) {

		    if ($lastOutCount >= $set->numberOfFailureLoginsAttempts) {
		        $json_login_answer = [
		            'result' => 'error',
		            'message' => lang('You have exceeded the number of allowed login attempts. Please try again in an hour.')
		        ];
		    } elseif (!empty($errors_log['errPass'])) {
		        $json_login_answer = [
		            'result' => 'error',
		            'message' => lang('Login Incorrect!')
		        ];
		    } elseif (!empty($errors_log['errEmailVerify'])) {
		        $json_login_answer = [
		            'result' => 'error',
		            'message' => lang('Please verify your email before you login!')
		        ];
		    }
		
            echo json_encode($json_login_answer);
            die();
        }

    default:
		updateLang();
        include('signin.php');
        break;
		
    case 'old_login':
        if (isLogin()) {
            if ($_GET['refe'])
                _goto($_GET['refe']);
        }
        if ($_GET['refe'])
            $_SESSION['refe'] = $_GET['refe'];
        if ($h)
            $_SESSION['hideCom'] = 1;



        updateLang();
					
		if ($isDown==1) 
			$set->pageTitle = translateInnerWord(lang($set->webTitle),"Affiliate Program");
		else	 {
			
			if ($set->showTitleOnLoginPage){
				$set->pageTitle = lang('Join').' '.translateInnerWord(lang($set->webTitle),"Affiliate Program")	;
			} else  {
				$set->pageTitle = "";
			}
		}
		
	
	
		$set->content .='	<script>
  $(document).ready(function() {
$( "body" ).addClass( "affiliateLoginBody" );
$( "html" ).addClass( "affiliateLoginHtml" );

	});
</script>';

if($selectedTextDirection == "RTL")
	$set->content .= '<link rel="stylesheet" href="css/login.css">';

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
			<div class="affiliate_image" style="'.($set->affiliateLoginImage && strpos($set->affiliateLoginImage,"/tmp")===false?  "background-image:url('". $set->affiliateLoginImage. "');" : ''). ' ">';
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
					<table class="affiliateLoginFrame" width="945" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="50%" valign="top" style="font-family: Arial; color: #333333; padding-top: 30px;">
							'.($lastOutCount >= $set->numberOfFailureLoginsAttempts ? '<div style="color:red;padding-bottom:10px;" >'.lang('You have exceeded the number of allowed login attempts. Please try again in an hour.').'</div>' : '') . '
								<form id="affiliateLoginForm" action="/?act=login" method="post">
									<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px;">'.lang('Sign In').'</div>
									<div style="color:red;padding-bottom:10px;" class="loginerrors"></div>
									'.(strlen($errors_log['errEmailVerify'])>0 ? '<span style="color: red; font-weight: bold;">'.lang('Please verify your email before you login!'). '  ' . ($errors_log['errEmailVerify']=='block' ? '' : "" ).'</span>' : '').'
									<div style="font-size: 14px; color: '.($errors['username'] ? 'red' : '#616161').'; height: 20px;">'.lang('Username').':</div>
									<input type="text" name="username" id="username" required  value="" style="width: 342px; height: 35px; line-height: 35px; background: url(images/main/input_bg.png); border: 0; font-size: 16px;" />
									<div style="font-size: 14px; color: '.($errors['password'] ? 'red' : '#616161').'; padding-top: 5px; height: 20px;">'.lang('Password').':</div>
									<input type="password" required   name="password" value="" style="width: 342px; height: 35px; line-height: 35px; background: url(images/main/input_bg.png); border: 0; font-size: 16px;" />
									'.($set->multi ? '<select onchange="langRedirect(this.value)" name="lang" style="width: 352px; margin-top: 10px;"><option value="">'.lang('Choose your language').'</option>'.listMulti($lang).'</select>' : '').'
									<div style="font-size: 12px;   padding-top: 4px;">'.lang('Forgot your').' <a href="/?act=forgot_password'.($lang ? '&lang='.$lang:'').'" style=color: #0d599a;">'.lang('Username').'</a> '.lang('or').' <a href="/?act=forgot_password'.($lang ? '&lang='.$lang:'').'" style="color: #0d599a;">'.lang('Password').'</a>?</div>
									<div style="padding: 5px 0 10px 0;"><input type="checkbox" /> <span style="">'.lang('Stay signed in').'</span> <span style="color: #918F8F;">('.lang("Uncheck if you're on a shared computer").')</span></div>
									<input type="submit"onclick= " return checkUsername()"  value="'.lang('Sign In').'" />
								</form>
								'.(strlen($errors_log['errPass'])>0 ? '<span style="color: red; font-weight: bold;">'.lang('Login Incorrect!'). '  ' . ($errors_log['errPass']=='block' ? '' : "" ).'</span>' : '').'
								
							</td>
							<td width="50%"  valign="top" style="font-family: Arial; color: #333333; padding-top: 30px;'. ($selectedTextDirection == "RTL"?'padding-right:30px;':'') .'">
								<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px; padding-left: 20px;">'.lang('Open an Account').'</div>
								<div align="center" style="padding-top: 40px;" class="regRight">
									<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px;">'.lang("Don't have an account yet?").'</div>
									<div style="padding-bottom: 10px;">'.lang('Get started now. it\'s fast and easy!').'</div>
									<input style="margin-left: auto; margin-right: auto;" type="submit" value="'.lang('Register').'" onclick="location.href=\'/?act=new_account'.($lang ? '&lang='.$lang:'').'\';" />
								</div>
							</td>
						</tr>
					</table>
				</div>';
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
					. ($set->hidePoweredByABLogo==0 ? '<td style="padding-left:15px;" align="right"><a href="http://www.affiliatets.com/" target="_blank"><img border="0" src="images/main/powered.png" alt="Powered by AffiliateBuddies" /></a></td>' : '') 
					. (!empty($set->secondaryPoweredByLogo) &&  strpos($set->secondaryPoweredByLogo,"/tmp")===false? '<td  align="center"><a href="'.$set->secondaryPoweredByLogoHrefUrl.'" target="_blank"><img style="    max-height: 85px;" border="0" src="'. $set->secondaryPoweredByLogo .'" alt="Powered by AffiliateBuddies" /></a></td>' : '') 
					. (!empty($set->brandsPoweredbyText) ? '<td  align="left" style="padding-left:15px;">'.$set->brandsPoweredbyText.'</td>' : '') . '
				</tr></table>
			</div>
			';
		
		theme(0,'mainHomePage');
		break;
	
	// --------------------------------- [ New Affiliate Account ] --------------------------------- //
	case "create":
	
		$chkUser = @mysql_fetch_assoc(function_mysql_query("SELECT id FROM affiliates WHERE lower(username)='".strtolower($db['username'])."'",__FILE__));
		if ($chkUser['id']) $errors['username'] = lang('Username already exist');
		if (!$db['username']) $errors['username'] = lang('Username already exist');
		if ($password != $repassword) $errors['password'] = lang('Passwords not match');
		if (!$password OR !$repassword) $errors['password'] = lang('Please fill out your password');
		$chkMail = mysql_fetch_assoc(function_mysql_query("SELECT id FROM affiliates WHERE lower(mail)='".strtolower($db['mail'])."'",__FILE__));
		if ($chkMail[id]) $errors['mail'] = lang('This e-mail already exist in our records');
		if (!validMail($db[mail])) $errors['mail'] = lang('Invalid E-mail');
		if (!$db[mail]) $errors['mail'] = lang('Please fill your E-mail');
		if (!$db[first_name]) $errors['first_name'] = lang('Please fill out your first name');
		if (!$db[last_name]) $errors['last_name'] = lang('Please fill out your last name');
		if (isMustField('phone') and  (!$db[phone])) $errors['phone'] = lang('Please fill out your phone');
		if (isMustField('IMUserType') and  (!$db['IMUserType'])) $errors['IMUserType'] = lang('Please fill out your IM user type');
		
		
		if ($set->hideInvoiceSectionOnAffiliateRegPage==0) {
			if (isMustField('country') and !$db[country]) $errors['country'] = lang('Please fill out your country');
		}
		
		if (isMustField('website') and   (!$db[website])) $errors['website'] = lang('Please fill out your website');
		if (!$approve) $errors['approve'] = lang('Please approve the Terms & Conditions');
		
		if ($set->allowCapthaOnReg && !chkSecure($code)) $errors['secureCode'] = lang('Please type the capcha currently');
		
		if (!empty($errors)) {
			
			
		// $db = $_POST;
			
			$str_error = '';
			foreach ($errors as $errItem => $errMessage) {
				$str_error .= $errItem.'|' . $errMessage ;
				break;
			}
			
			// _goto($set->basepage . '?act=new_account&error=' . $str_error);
			header("Location: javascript:window.history.go(-1)");
			
			
		} else {
			
			$db['ip'] = $set->userIP;
			$db['password'] = md5($password);
			$db['rdate'] = dbDate();
			$db['valid'] = ($set->pending ? '0' : '1');
			
			
			
            if ($newsletter || $set->affiliateNewsletterCheckboxValue == 1)
                $db['newsletter'] = 1;
            else
                $db['newsletter'] = 0;
			// Strip Tags
			$db['first_name'] = strip_tags($db['first_name']);
			$db['last_name'] = strip_tags($db['last_name']);
			$db['phone'] = strip_tags($db['phone']);
			$db['country'] = strip_tags($db['country']);
			$db['website'] = trim(strip_tags($db['website']));
			$db['mail'] = trim(strip_tags($db['mail']));
			$db['sub_com'] = $set->sub_com;
			$db['showDeposit'] = $set->show_deposit;
			$db['type'] = $utype;
			// if (!empty($set->IBpushLeadOnRegistrationUrl) && strtolower($utype)!='affiliate' && $set->ShowAffiliateTypes)
			}
			
		
			// Strip Tags
			
			/**
			 * Determine whether to show credits or not.
			 * Begin.
			 */
				$db['show_credit'] = $set->show_credit_as_default_for_new_affiliates;
			/**
			 * Determine whether to show credits or not.
			 * End.
			 */
				// $settings = mysql_fetch_assoc(function_mysql_query('SELECT * FROM settings WHERE id=1 LIMIT 0,1',__FILE__));
			if ($_COOKIE[$refCookie]) {
				$refe_exp = explode("-",$_COOKIE[$refCookie]);
				$db['refer_id'] = substr($refe_exp[0],1);
				setcookie($refCookie,'',time()-1000);
				if ($db['refer_id'])
				$parent = mysql_fetch_assoc(function_mysql_query('SELECT * FROM affiliates WHERE id='.$db['refer_id'],__FILE__));
			}
				if ($_COOKIE[$ibCookie]) {
				$isIB = explode("-",$_COOKIE[$ibCookie]);
				$db['refer_id'] = substr($refe_exp[0],1);
				setcookie($ibCookie,'',time()-1000);
				$db['isIB'] = 1 ; 
			}
				
			
			
			$merchantqq=function_mysql_query("SELECT * FROM merchants WHERE valid='1'  ORDER BY pos",__FILE__);
        while ($merchantww = mysql_fetch_assoc($merchantqq))
            $activeMerchants[] = $merchantww['id'];
        if (count($activeMerchants) > 0)
            $db['merchants'] = implode("|", $activeMerchants);
			if ($_SESSION['group_id_aff']) {
				$db['group_id'] = $_SESSION['group_id_aff'];
				unset($_SESSION['group_id_aff']);
				}

			if (empty($group_id))
			$db['group_id'] = isset($_POST['group_id']) ? $_POST['group_id'] : (isset($_GET['group_id']) ? $_GET['group_id'] :$defaultGroupID);
			
			
			if($parent['id'] AND $set->autoRelateSubAffiliate){
				$db['group_id'] = $parent['group_id'];
			}
			
			$db['sub_com'] = $set->sub_com;
			$db['qualify_type'] = $set->def_qualify_type_for_affiliates ==0 ? '' : 'default';                        
			$db['profilePermissionID'] = $set->def_profilePermissionsForAffiliate ;
			
			
			$lastID = dbAdd($db, 'affiliates');
			
			
				if (!empty($set->IBpushLeadOnRegistrationUrl) &&  $set->ShowAffiliateTypes){
				
				//http://api-dev.megaflex.me/leads/create?phone_number={phone}&language=en&first_name={first_name}&last_name={last_name}&email={email}&ctag=&ip={ip}
												
					$url = $set->IBpushLeadOnRegistrationUrl;
					$url = str_replace('{email}',$db['mail'],$url);
					$url = str_replace('{phone}',str_replace(' ','',$db['phone']),$url);
			     	$url = str_replace('{first_name}',str_replace(' ','',$db['first_name']),$url);
			     	$url = str_replace('{last_name}',str_replace(' ','',$db['last_name']),$url);
					$url = str_replace('{password}',$password,$url);
			     	$url = str_replace('{country}',str_replace(' ','',$db['country']),$url);
					$url = str_replace('{ip}',str_replace(' ','',$db['ip']),$url);
					$url .= "ctag=" .$lastID;

					if ($_GET['debug'])
					var_dump($url);
				
				
					$arrUrl  = explode('?', $url);	
					$ch      = curl_init();
					
					curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					
					$result = curl_exec($ch);
					
					curl_close($ch);
			}
			
                        
			if($set->autoRelateNewAffiliateToAllMerchants==1){
                            autoRelateAllMerchantsToAff($lastID);
			}
                        
			autoRelateCampToAff();
                        
			if (!$set->pending) {
				$_SESSION['aff_session_id'] = $lastID;
				$_SESSION['aff_session_serial'] = md5($db['username'].$db['password'].$lastID);
				updateUnit('affiliates',"ip='".$set->userIP."',logged='1',lastvisit='".dbDate()."'","id='".$lastID."'");
				}
			
			sendTemplate('NewAffiliateNotification',$lastID,0,'',0,0);
			sendTemplate('WelcomeAffiliate',$lastID,0,$password,0,0);
			
			echo '
'.lang('Please Wait...').'
<meta http-equiv="Refresh" content="2; URL='.($set->pending ? '/?act=new_account&ty=1' : '/affiliate/').'" />';


echo $set->affiliateRegistrationPixel;
			
			die();
		
		
			
	case "new_account":
		
		$str_error = '';
		if(isset($_POST['db'])){
		$chkUser = @mysql_fetch_assoc(function_mysql_query("SELECT id FROM affiliates WHERE lower(username)='".strtolower($db['username'])."'",__FILE__));
		if ($chkUser['id']) $errors['username'] = lang('Username already exist');
		if (!$db['username']) $errors['username'] = lang('Username already exist');
		if ($password != $repassword) $errors['password'] = lang('Passwords not match');
		if (!$password OR !$repassword) $errors['password'] = lang('Please fill out your password');
		$chkMail = mysql_fetch_assoc(function_mysql_query("SELECT id FROM affiliates WHERE lower(mail)='".strtolower($db['mail'])."'",__FILE__));
		if ($chkMail[id]) $errors['mail'] = lang('This e-mail already exist in our records');
		if (!validMail($db[mail])) $errors['mail'] = lang('Invalid E-mail');
		if (!$db[mail]) $errors['mail'] = lang('Please fill your E-mail');
		if (!$db[first_name]) $errors['first_name'] = lang('Please fill out your first name');
		if (!$db[last_name]) $errors['last_name'] = lang('Please fill out your last name');
		if (isMustField('phone') and  (!$db[phone])) $errors['phone'] = lang('Please fill out your phone');
		
		
		if ($set->hideInvoiceSectionOnAffiliateRegPage==0) {
			if (isMustField('country') and !$db[country]) $errors['country'] = lang('Please fill out your country');
		}
		
		if (isMustField('website') and   (!$db[website])) $errors['website'] = lang('Please fill out your website');
		if (!$approve) $errors['approve'] = lang('Please approve the Terms & Conditions');
		
		if ($set->allowCapthaOnReg && !chkSecure($code)) $errors['secureCode'] = lang('Please type the capcha currently');
		
		if (!empty($errors)) {
			
		        if(!empty($_POST['ajax'])){
		             $json_login_answer = [
		                'result' => 'error',
		                'message' => 'Please, fill all required fields.',
		                'errors' => $errors
		            ];
		            echo json_encode($json_login_answer);
		            
		            die();
		        }
			
			foreach ($errors as $errItem => $errMessage) {
				$str_error .= $errItem.'|' . $errMessage ;
				break;
			}
		}else if(empty($errors)  && $lastRegOutCount<4){
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
			
			
			$db['ip'] = $set->userIP;
			$db['password'] = md5($password);
			$db['rdate'] = dbDate();
			$db['valid'] = ($set->pending ? '0' : '1');
			
			
			
                if ($newsletter || $set->affiliateNewsletterCheckboxValue == 1)
                    $db['newsletter'] = 1;
                else
                    $db['newsletter'] = 0;
			// Strip Tags
			$db['first_name'] = strip_tags($db['first_name']);
			$db['last_name'] = strip_tags($db['last_name']);
			$db['phone'] = strip_tags($db['phone']);
                        
                        if(!empty($_POST['__phone_prefix'])){
                            $db['phone'] = strip_tags($_POST['__phone_prefix'].$db['phone']);
                        }
                        
			$db['country'] = strip_tags($db['country']);
			$db['website'] = strip_tags($db['website']);
			$db['mail'] = strip_tags($db['mail']);
			$db['sub_com'] = $set->sub_com;
			$db['showDeposit'] = $set->show_deposit;
			
			if(isset($groups))
				$db['group_id'] = strip_tags($groups);
			if(isset($language))
				$db['group_id'] = strip_tags($language);

					
			if (empty($group_id))
			$db['group_id'] = empty($db['group_id'])  && isset($_POST['group_id']) ? $_POST['group_id'] :(isset($_GET['group_id']) ? $_GET['group_id'] : $defaultGroupID);

		
		
			
			
			
			$db['type'] = $utype;
			
			// if (!empty($set->IBpushLeadOnRegistrationUrl) && strtolower($utype)!='affiliate' && $set->ShowAffiliateTypes)
			if (!empty($set->IBpushLeadOnRegistrationUrl)  && $set->ShowAffiliateTypes)
			{
				
				//http://api-dev.megaflex.me/leads/create?phone_number={phone}&language=en&first_name={first_name}&last_name={last_name}&email={email}&ctag=&ip={ip}
												
					$url = $set->IBpushLeadOnRegistrationUrl;
					$url = str_replace('{email}',$db['mail'],$url);
					$url = str_replace('{phone}',str_replace(' ','',$db['phone']),$url);
			     	$url = str_replace('{first_name}',str_replace(' ','',$db['first_name']),$url);
			     	$url = str_replace('{last_name}',str_replace(' ','',$db['last_name']),$url);
					$url = str_replace('{password}',$password,$url);
			     	$url = str_replace('{country}',str_replace(' ','',$db['country']),$url);
					$url = str_replace('{ip}',str_replace(' ','',$db['ip']),$url);

					if ($_GET['debug'])
					var_dump($url);
				
				
					$arrUrl  = explode('?', $url);	
					$ch      = curl_init();
					
					curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					
					$result = curl_exec($ch);
					
					curl_close($ch);

			}
			
			
		
			// Strip Tags
			
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
			
			
				// $settings = mysql_fetch_assoc(function_mysql_query('SELECT * FROM settings WHERE id=1 LIMIT 0,1',__FILE__));
			if ($_COOKIE[$refCookie]) {
				$refe_exp = explode("-",$_COOKIE[$refCookie]);
				$db['refer_id'] = substr($refe_exp[0],1);
				setcookie($refCookie,'',time()-1000);
				if ($db['refer_id'])
				$parent = mysql_fetch_assoc(function_mysql_query('SELECT * FROM affiliates WHERE id='.$db['refer_id'],__FILE__));
			}
				if ($_COOKIE[$ibCookie]) {
				$isIB = explode("-",$_COOKIE[$ibCookie]);
				$db['refer_id'] = substr($refe_exp[0],1);
				setcookie($ibCookie,'',time()-1000);
				$db['isIB'] = 1 ; 
				
			}
				
			
			
			/* $merchantqq=function_mysql_query("SELECT * FROM merchants WHERE valid='1'  ORDER BY pos",__FILE__);
			while ($merchantww=mysql_fetch_assoc($merchantqq)) $activeMerchants[] = $merchantww['id'];
			if (count($activeMerchants) > 0) $db['merchants']=implode("|",$activeMerchants); */
			if ($_SESSION['group_id_aff']) {
				$db['group_id'] = $_SESSION['group_id_aff'];
				unset($_SESSION['group_id_aff']);
				}
			
			if($parent['id'] AND $set->autoRelateSubAffiliate==1){
				$db['group_id'] = $parent['group_id'];
			}
			
			$db['sub_com'] = $set->sub_com;
			$db['qualify_type'] = $set->def_qualify_type_for_affiliates ==0 ? '' : 'default';                        
			$db['profilePermissionID'] = $set->def_profilePermissionsForAffiliate ;
			
			
			//email verification code
			$db['optinGuid'] = md5($db['username'] . microtime()); 
			if(isset($db['selected_language_id']) && !empty($db['selected_language_id'])){
			$db['language_id'] = $db['selected_language_id'];
			unset($db['selected_language_id']);
			}
			
			$lastID = dbAdd($db, 'affiliates');
                        
			
		
			
			if($set->BlockLoginUntillEmailVerification == 1){
				sendTemplate('AffiliateEmailVerification',$lastID,0,'',0,0);
                    if(empty($_POST['ajax'])){
                        echo lang('Please Wait...') . '<meta http-equiv="Refresh" content="2; URL="/?act=new_account&ty=1"/>';
                    }else{
                        $json_login_answer = [
                            'result' => 'success',
                            'message' => '/affiliate'
                        ];
                        echo json_encode($json_login_answer);
                        die();
			}
                } else {
				if($set->autoRelateNewAffiliateToAllMerchants==1){
								autoRelateAllMerchantsToAff($lastID);
				}
				
							
				autoRelateCampToAff();
							
					if (!$set->pending) {
					$_SESSION['aff_session_id'] = $lastID;
					$_SESSION['aff_session_serial'] = md5($db['username'].$db['password'].$lastID);
					updateUnit('affiliates',"ip='".$set->userIP."',logged='1',lastvisit='".dbDate()."'","id='".$lastID."'");
					}
			
				sendTemplate('NewAffiliateNotification',$lastID,0,'',0,0);
				sendTemplate('WelcomeAffiliate',$lastID,0,$password,0,0);
                    if(empty($_POST['ajax'])){
                        echo lang('Please Wait...') . '<meta http-equiv="Refresh" content="2; URL=' . ($set->pending ? '/?act=new_account&ty=1' : '/affiliate/') . '" />';
                    }else{
                        $json_login_answer = [
                            'result' => ($set->pending?'pending':'success'),
                            'message' => ($set->pending?'Account is pending':'/affiliate')
                        ];
                        echo json_encode($json_login_answer);
                        die();
			}
                }
		
break;

echo $set->affiliateRegistrationPixel;
            } else {
	
			if ($lastRegOutCount<=4)
				$errors_reg['errRegPass'] = 1;
		}
		}
        if ($group_id AND ! $_SESSION['group_id_aff'])
            $_SESSION['group_id_aff'] = $group_id;
		$set->pageTitle = lang('Open a New Affiliate Account');
		
		$hideMarketingField = $set->hideInvoiceSectionOnAffiliateRegPage==0 || $set->hideMarketingSectionOnAffiliateRegPage==0;
		
		if(!isset($errors_reg['errRegPass'])){
			
            if (count($errors) > 0)
                $set->content .= '<div align="left" style="width: 970px; color: red;">
			<b>'.lang('Please check one or more of the following fields:').'</b><br />
			<ul type="*"><li />'.implode('<li />',$errors).'</ul>
		</div>'
		;
		
		
		
		$refer = $_SERVER['HTTP_REFERER'];
		
		if($selectedTextDirection == "RTL"){
			$set->content .= '<link rel="stylesheet" href="css/login.css">
			<style>
			.rtltd{
				padding-right:22px;
			}
			</style>
			';
		}
		$set->content .= '
		<script type="text/javascript" src="../js/html2canvas.js"></script>
<script type="text/javascript" src="../js/jquery.plugin.html2canvas.js"></script>
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

		</style>
		<div id="target">
		<form id="frmAffReg"action="/?act=new_account'.($_GET['debug']==1 ? "&debug=1" : "").'" method="post" onsubmit="return checkReg()" autocomplete="off" class="regform" style="'. 
		($set->affiliateLoginImage ?  "background-image:url('". $set->affiliateLoginImage. "');
		
		 no-repeat center center fixed; 
  -webkit-background-size: cover;
  -moz-background-size: cover;
  -o-background-size: cover;
  background-size: cover;
		
		" : '') . '">
		
		<table width="975" border="0" cellpadding="0" cellspacing="0" style="background-color: white;    opacity: 0.999;    border-radius: 13px;    padding: 5px;
}">

			<!--<tr>
				<td colspan="3" height="10">'.$str_error.'</td>
			</tr>--><tr>
				<td style="'.($set->hideMarketingSectionOnAffiliateRegPage==1 ? "padding-left:12%;":"").'width:435px" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="reg_field_title rtltd" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('ACCOUNT INFORMATION').'</td>
						</tr><tr>
							<td height="225" valign="top" style="background: url(images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Username').':</td>
									</tr><tr>
										<td align="left"  style="padding-left: 10px;"><input type="text" name="db[username]" id="username" value="'.$db['username'].'" style="width: 280px;" /></td>
									</tr><tr>
										<td class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Password').':</td>
									</tr><tr>
										<td align="left"  style="padding-left: 10px;"><input type="password" name="password" id="password" value="" style="width: 280px;" /></td>
									</tr><tr>
										<td class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Repeat Password').':</td>
									</tr><tr>
										<td  align="left" style="padding-left: 10px;"><input type="password" name="repassword" id="repassword" value="" style="width: 280px;" /></td>
									'.($set->ShowAffiliateTypes ? '
									</tr><tr>
										<td class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Type').':</td>
									</tr><tr>
										<td align="left"  style="padding-left: 10px;"><select name="utype" style="width: 295px;" id="utype"><option selected=1 value = "Affiliate">'. lang('Affiliate') .'</option><option value = "IB">'. lang('IB') .'</option><option value = "WhileLabel">'. lang('WhiteLabel') .'</option><option value = "PortfolioManager">'. lang('Porfolio Manager') .'</option></select></td>'
										: '' ).'
									</tr>';
									
								if($set->showGroupValuesOnAffReg){
									
									
									if($set->showGroupsLanguages == "group_name"){
										$qry ="select * from groups where valid=1 order by lower(title)";
										if ($_GET['group_id'] && $_GET['group_id']>0)
											$qry = $qry .  " and id =" . $_GET['group_id'];
											
										$groups = function_mysql_query($qry,__FILE__);
										$numRows =mysql_num_rows($groups) ;
										if($numRows > 0){
											$set->content.='<tr>
										<td class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Group').':</td>
									</tr><tr>
										<td  align="left" style="padding-left: 10px;"><select name="group_id" style="width: 295px;" id="groups" onchange="setLanguageId(this)"><option  value = "">'. lang('Select') .'</option>';
											while($group = mysql_fetch_assoc($groups)){
												$set->content.='<option '.($numRows==1 ? ' selected ' : '').' value=' . $group['id'] . '>'. $group['title'] .'</option>';
											}
											$set->content .='</select><input type="hidden" name="db[selected_language_id]" value=0></td></tr>';
										}
                } elseif ($set->showGroupsLanguages == "language_name") {
										
										$qs_group_id = isset ($_GET['group_id']) && $_GET['group_id']>0 ? $_GET['group_id'] : "";
										
											$qry ="SELECT groups.id, groups.language_id

												FROM groups

												WHERE groups.valid =1  ".(!empty($qs_group_id) ? " and groups.id =" . $_GET['group_id']. " "  : " "). "
										";
										
											$groupsArray=array();
											$langsList="";
											$groups = mysql_query($qry);
                    while ($group = mysql_fetch_assoc($groups)) {
												$exp = explode(',',$group['language_id']);
												foreach ($exp as $ex){
													
													if (!isset($groupsArray[$ex])){
													$groupsArray[$ex] = $group['id'];
													$langsList .= ",".$ex;
													}
												}
											}

											$rsc = mysql_query("select id,title from languages where id in (-1". $langsList. ") ");
											$langsArray = array();
											while ($lrow = mysql_fetch_assoc($rsc)){
												$langsArray[$lrow['id']] = $lrow['title'];
											}
										
											$numRows = count(	$groupsArray);
											

											
								
								/* 		$langs = function_mysql_query($qry,__FILE__);
										$numRows =mysql_num_rows($langs) ; */
										if($numRows > 0){
											$set->content.='<tr>
										<td class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Languages').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><select name="group_id" style="width: 295px;" id="language" onchange="setLanguageId(this)"><option  value = "">'. lang('Select') .'</option>';
											foreach ($groupsArray as $lang_id=>$gr_id){
										
												$set->content.='<option '.($numRows==1 ? ' selected ' : '').' value=' . $gr_id . '>'. $langsArray[$lang_id] .'</option>';
											}
											
											
											/* while($lang = mysql_fetch_assoc($langs)){
												$set->content.='<option '.($numRows==1 ? ' selected ' : '').' value=' . $lang['id'] . '>'. $lang['title'] .'</option>';
											} */
											$set->content .='</select><input type="hidden" name="db[selected_language_id]" value=0></td></tr>';
										}
                } elseif ($set->showGroupsLanguages == "display_lname") {
										
									 	if ($_GET['group_id'] && $_GET['group_id']>0){
											$qry ="SELECT groups.id, languages.title,languages.displayText
												FROM groups
												LEFT JOIN languages ON languages.id = groups.language_id
												WHERE groups.valid =1  and groups.id =" . $_GET['group_id'] . "
												GROUP BY languages.title
												ORDER BY LOWER(languages.displayText ) ";
                    } else {
										$qry ="SELECT groups.id,languages.title, languages.displayText
												FROM groups
												LEFT JOIN languages ON languages.id = groups.language_id
												WHERE groups.valid =1
												GROUP BY languages.title
												ORDER BY LOWER( languages.displayText ) ";	
										} 
										
										
										
										$langs = function_mysql_query($qry,__FILE__);
										$numRows =mysql_num_rows($langs) ;
										if($numRows > 0){
											$set->content.='<tr>
										<td class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Languages').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><select name="group_id" style="width: 295px;" id="language" onchange="setLanguageId(this)"><option  value = "">'. lang('Select') .'</option>';
											while($lang = mysql_fetch_assoc($langs)){
												$set->content.='<option '.($numRows==1 ? ' selected ' : '').' value=' . $lang['id'] . '>'. $lang['displayText'] .'</option>';
											}
											$set->content .='</select>
											<input type="hidden" name="db[selected_language_id]" value=0>
											</td></tr>';
										}
									}
								}
									
								$set->content .= '</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr><tr>
							<td height="10"></td>
						</tr><tr>
						
							<td  class="reg_field_title rtltd" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('WEBSITE INFORMATION').'</td>
						</tr><tr>
							<td  height="195" valign="top" style="background: url(images/reg/reg_box_bg.jpg);  padding-bottom: 15px;">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span ' .  (isMustField('website') ? '  class="required">*' : '>').'</span> '.lang('Website').' 1:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website]" id="website" value="'.($db['website'] ? $db['website'] : 'http://').'" style="width: 280px;" /></td>
									</tr><tr>
										<td  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Website').' 2:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website2]" value="'.($db['website2'] ? $db['website2'] : 'http://').'" style="width: 280px;" /></td>
									</tr><tr>
										<td  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Website').' 3:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website3]" value="'.($db['website3'] ? $db['website3'] : 'http://').'" style="width: 280px;" /></td>
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td>
				'.($set->hideInvoiceSectionOnAffiliateRegPage==1 && $set->hideMarketingSectionOnAffiliateRegPage==1 ? "" : 
				'<td width="325" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						'.($set->hideInvoiceSectionOnAffiliateRegPage ? "" : '<tr>
							<td class="reg_field_title rtltd" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('INVOICE INFORMATION').'</td>
						</tr><tr>
							<td height="226" valign="top" style="background: url(images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span ' .  (isMustField('street') ? '  class="required">*' : '>').'</span> '.lang('Street').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[street]" value="'.$db['street'].'" id="street" style="width: 280px;" /></td>
									</tr><tr>
										<td   class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span ' .  (isMustField('postalCode') ? '  class="required">*' : '>').'</span> '.lang('Postal / Zip Code').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[postalCode]" value="'.$db['postalCode'].'" id="postalCode" style="width: 280px;" /></td>
									</tr><tr>
										<td  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span ' .  (isMustField('city') ? '  class="required">*' : '>').'</span> '.lang('City').':</td>
									</tr><tr>
										<td  align="left" style="padding-left: 10px;"><input type="text" name="db[city]" value="'.$db['city'].'" id="city" style="width: 280px;" /></td>
									' . ($hideMarketingField ? '
									</tr><tr>
										<td height="24" class="rtltd" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span ' .  (isMustField('country') ? '  class="required">*' : '>').'</span> '.lang('Country').':</td>
									</tr><tr>
										<td  class="rtltd" style="padding-left: 10px;"><select name="db[country]" style="width: 295px;" id="country"><option value="">'.lang('Choose Your Country...').'</option>'.getCountry($db['country']).'</select></td>
										': '').'
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr><tr>').
							 ($set->hideMarketingSectionOnAffiliateRegPage ? "" : ' <td height="10"></td>
						</tr><tr>
							<td  class="reg_field_title rtltd" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('MARKET INFORMATION').'</td>
						</tr><tr>
										<td  class="rtltd" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('What are your traffic sources?').'</td>
						</tr><tr>
							<td align="left" height="184" valign="top" style="background: url(images/reg/reg_box_bg.jpg); padding-left: 10px; padding-top: 10px;">
								<table><tr>
									<td>
										<select id="q1" size="10" multiple="true" style="width: 130px; height: 150px; overflow: auto; border: 1px #CECECE solid;">
												<option value="1">'.lang('Africa').'</option>
											<option value="2">'.lang('Afro Eurasia').'</option>
											<option value="3">'.lang('Americas').'</option>
											<option value="4">'.lang('Asia').'</option>
											<option value="5">'.lang('Australia').'</option>
											<option value="6">'.lang('Eurasia').'</option>
											<option value="7">'.lang('Europe').'</option>
											<option value="8">'.lang('North America').'</option>
											<option value="9">'.lang('South America').'</option>
											<option value="10">'.lang('United Kingdom').'</option>
											<option value="11">'.lang('World Wide').'</option>
										</select>
									</td>
									<td>
										<table border="0" cellpadding="0" cellspacing="0">
										<tr><td><img border="0" src="images/reg/right.jpg" alt="" onclick="moveMultiple(\'q1\',\'q2\',\'q2\',\'update\'); return false;" style="cursor: pointer;" /></td></tr>
										<tr><td height="3"></td></tr>
										<tr><td><img border="0" src="images/reg/left.jpg" alt="" onclick="moveMultiple(\'q2\',\'q1\',\'q2\',\'update\'); return false;" style="cursor: pointer;" /></td></tr>
										</table>
									</td>
									<td><select id="q2" multiple="true" style="width: 130px; height: 150px; overflow: auto; border: 1px #CECECE solid;">'.$selectedItems.'</select></td>
								</tr></table>
								<input type="hidden" name="db[marketInfo]" id="update" value="" />
								
							</td>
						</tr>').'<tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td> ' ) . '
				<td width="325" rowspan="1" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td class="reg_field_title rtltd" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('CONTACT INFORMATION').'</td>
						</tr><tr>
							<td height="480" valign="top" style="  padding-bottom: 15px;background: url(images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2"  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Company Name').':</td>
									</tr><tr>
										<td colspan="2" align="left"  style="padding-left: 10px;"><input type="text" name="db[company]" value="'.$db['company'].'" style="width: 280px;" /></td>
									</tr><tr>
										<td  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Salutation').':</td>
									</tr><tr>
										<td  style="padding-right: 20px;"><input type="radio" name="db[gender]" value="male" '.($db['gender'] == "male" || !$db['gender'] ? 'checked="checked"' : '').' /> '.lang('Mr').'. <input type="radio" name="db[gender]" value="female" '.($db['gender'] == "female" ? 'checked="checked"' : '').' /> '.lang('Ms').'.</td>
									</tr><tr>
										<td colspan="2"  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('First Name').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[first_name]" id="first_name" value="'.$db['first_name'].'" style="width: 280px;" /></td>
									</tr><tr>
										<td colspan="2"  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Last name').':</td>
									</tr><tr>
										<td colspan="2"  align="left" style="padding-left: 10px;"><input type="text" name="db[last_name]" id="last_name" value="'.$db['last_name'].'" style="width: 280px;" /></td>
									</tr><tr>
										<td colspan="2"  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('E-mail').':</td>
									</tr><tr>
										<td colspan="2"  align="left"style="padding-left: 10px;"><input type="text" name="db[mail]" value="'.$db['mail'].'" id="mail" style="width: 280px;" /></td>
									</tr><tr>
										<td colspan="2"  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span ' .  (isMustField('phone') ? '  class="required">*' : '>').'</span> '.lang('Phone number').':</td>
									</tr><tr>
										<td colspan="2"  align="left" style="padding-left: 10px;"><input type="text" name="db[phone]" value="'.$db['phone'].'" id="phone" style="width: 280px;" /></td>
									'.(!$hideMarketingField ? '
													</tr><tr>
										<td  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span ' .  (isMustField('country') ? '  class="required">*' : '>').'</span> '.lang('Country').':</td>
									</tr><tr>
										<td  style="padding-left: 10px;" align="left"><select name="db[country]" style="width: 295px;" id="country"><option value="">'.lang('Choose Your Country...').'</option>'.getCountry($db['country']).'</select></td>
										' :'').'
									</tr><tr>
										<td colspan="2"  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('I.M. type').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;">
											<select name="db[IMUserType]" style="width: 295px;">
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
										<td colspan="2"  class="rtltd" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('I.M. account').':</td>
									</tr><tr>
										<td colspan="2" align="left"  style="padding-left: 10px;"><input type="text" name="db[IMUser]" value="'.$db['IMUser'].'" style="width: 280px;" /></td>
									</tr><tr>
										<td colspan="2" height="10"></td>
									</tr><tr>
										
										' .
										(!$set->allowCapthaOnReg? "" : '
									<td colspan="2"  class="rtltd" style="padding-left: 10px;">'.secureCode().'</td>'  ) . '
									
									
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td>
			</tr><tr>
				<td colspan="3" height="10"></td>
			</tr><tr>
				<td '.($selectedTextDirection=='RTL'?'colspan="2"':'colspan="3"').' >
					<!--input type="checkbox" name="approve" id="approve" /> '.lang('I have read and accepted the').' <a href="'.$set->terms_link.'" target="_blank">'.lang('Terms & Conditions').'</a> '. ((!empty($set->extraAgreement2Name) && !empty($set->extraAgreement2Link)) ? lang('&') .' ' . '<a href="'.$set->extraAgreement2Link.'" target="_blank">'.lang($set->extraAgreement2Name).'</a> '  :  '') . '  '. ((!empty($set->extraAgreement3Name) && !empty($set->extraAgreement3Link)) ? lang('&') .' ' . '<a href="'.$set->extraAgreement3Link.'" target="_blank">'.lang($set->extraAgreement3Name).'</a> '  :  '') .'.<br /-->
					<input type="checkbox" name="approve" id="approve" /> '.lang('I have read and accepted the').' <a class="inline" href="#termsPop">'.lang('Terms & Conditions').'</a> '. ((!empty($set->extraAgreement2Name) && !empty($set->extraAgreement2Link)) ? lang('&') .' ' . '<a href="'.$set->extraAgreement2Link.'" target="_blank">'.lang($set->extraAgreement2Name).'</a> '  :  '') . '  '. ((!empty($set->extraAgreement3Name) && !empty($set->extraAgreement3Link)) ? lang('&') .' ' . '<a href="'.$set->extraAgreement3Link.'" target="_blank">'.lang($set->extraAgreement3Name).'</a> '  :  '') .'.<br />
					<input type="checkbox" name="newsletter" '.(($newsletter || $set->affiliateNewsletterCheckboxValue==1) ? 'checked="checked"' : '').' /> '.lang($set->newsletterCheckboxCaption).'
				</td>
				<td colspan="1" align="right" style="'.($selectedTextDirection=='RTL'?'padding-right: 130px;':'padding-right: 10px;').'">
					<input class="register_now_btn" type="submit" value="'.lang('Register Now').'" style="   '.($set->hideInvoiceSectionOnAffiliateRegPage==1 && $set->hideMarketingSectionOnAffiliateRegPage==1 ?  'position: relative;' : '    margin-left: -202px;').'px!important; "/>
				</td>
			</tr><tr>
				<td colspan="3" height="15"></td>
			</tr>
		</table>
		'.($set->captureAffiliatesRegistration?'
		<input type="hidden" name="img_val" id="img_val" value="" />':'').'
		</form>
		
		</div><!--Target Div closed-->
		<input type="hidden" name="db[regReferUrl]" value="'.$refer.'" />
		  <script>
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
		var pattern = new RegExp( /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
		function checkReg() {
	capture();
	var check_ids = new Array("username","password","repassword",'. (isMustField('website')?'"website",':"") . (isMustField('street')?'"street",':""). (isMustField('postalCode')?'"postalCode",':"") .(isMustField('city')?'"city",':"") .(isMustField('IMUserType')?'"IMUserType",':"").(isMustField('country')?'"country",':"") .'"last_name","first_name","mail",'. (isMustField('phone')?'"phone",':"")  .'"code");
	var names = new Array("'.lang('Username').'","'.lang('Password').' ","'.lang('Repeat Password').'","'.(isMustField('website')?lang("Website") . '","':"").(isMustField('street')?lang("Street") . '","':"").(isMustField('postalCode')?lang("postal Code") . '","':"").(isMustField('city')?lang("city") . '","':"").(isMustField('IMUserType')?lang("IMUserType") . '","':"").(isMustField('country')?lang("Country") . '","':"").lang('Last Name').'","'.lang('First Name').'","'.lang('E-mail').'","'.(isMustField('phone')?lang("Phone") . '","':"").lang('Code').'");
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
			if(check_ids[i] != "password" || check_ids[i] != "repassword" || check_ids[i] != "country" ){
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

function setLanguageId(e){
	$("[name=\'db[selected_language_id]\']").val($(e).val());
}
		</script>';
		
		$set->content .= "
		<script>
		function capture() {
			
			". ($set->captureAffiliatesRegistration==1?"

			$('#target').html2canvas({
				onrendered: function (canvas) {
					//Set hidden field's value to image data (base-64 string)
					imgData = canvas.toDataURL('image/png');
					$('#img_val').val(imgData);
					path  = '". $set->webAddress ."';
					$.post('ajax/AffiliateRegistrationScreenCapture.php',{base64:imgData,url:path},function(res){
						
					}); 
				}
			});
			":"")."
		}
		</script>
		";
			$set->content .= "<script type=\"text/javascript\">
			function closemodal() {
			
				
				$('#colorbox').hide();
			$('#cboxOverlay').hide();
				$('#termsPop').hide();
			}
		</script>";
		
		
		$set->content .= '<div id="termsPop" style="display: none; border: "1px black solid" ,height: "220px", width:"250px;">
		<!--a href="javascript:void(0)"><img src="images/x_btn.png" style="    float: right;    margin-top: -17px;    z-index: 10000;"/></a-->
            <center>
            <h2><u>' . lang('Terms and conditions') . '</u></h2>
            
									<iframe style="width:95%   ; height: 272px!important;" src="'.$set->terms_link.'"></iframe>
							
			
            </center>
    </div>';
		
		
            if ($ty)
                $set->content = '
		<div align="left" style="width: 940px; margin-left: -10px; color: green; font-size: 16px; font-weight: bold; border-radius: 3px; color: #FFF; background: #000 url(/images/approved.png) no-repeat left; border: 1px #000 solid; padding: 30px;">
			<div style="padding-left: 130px;">
				'.lang('Thank you for registering with').' '.$set->webTitle.'<br />
				<br />
				'.lang('Your account be will activated as soon as it is approved').'
			</div>
		</div>';
							}
							else{
								
								$set->content = '<h2 width="975">'. lang('Sorry, you are temporary blocked because of multiple wrong attempts made from your end.') .'<br>'.lang('Please contact you account manager').'.</h2>';
							}
		theme(0,'',$selectedTextDirection);
		break;
		
		
	// --------------------------------- [ Forgot Password ] --------------------------------- //
	
	case "send_password":
		$username = clearInjection($username);
		$mail = clearInjection($mail);
		
		$getMail=mysql_fetch_assoc(function_mysql_query("SELECT id,mail,username,first_name FROM affiliates WHERE lower(username)='".strtolower($username)."' OR lower(mail)='".strtolower($mail)."'",__FILE__));
        if (!$getMail['id'] AND $username)
            $errors['err'] = lang('Username no exist!');
        if (!$getMail['id'] AND $mail)
            $errors['err'] = lang('E-mail no exist!');
        if (!$username AND ! $mail)
            $errors['err'] = lang('Please fill out username or e-mail');
        if ($set->allowCapthaOnReset && !chkSecure($code))
            $errors['err'] = lang('Please type the capcha currently');
		if ($errors) {
                        if(!empty($_POST['ajax'])){
                            $json_forgot_answer = [
                                'result' => 'error',
                                'message' => $errors['err']
                            ];
                            echo json_encode($json_forgot_answer);
                            die();
                        }
			} else {
			$abc= array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"); 
			$abcBig= array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"); 
			$new_password = rand(0,9).$abc[rand(0,25)].$abc[rand(0,25)].$abcBig[rand(0,25)].rand(0,4).rand(0,9).$abcBig[rand(0,25)];
			updateUnit("affiliates","password='".md5($new_password)."'","id='".$getMail['id']."'");
			$set->sendTo = $getMail['mail'];
			$set->subject = $getMail['first_name'].' - '.lang('Password Reset');
			// $mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode FROM mail_templates WHERE id=-1',__FILE__));
			$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="ResetPassword" and valid=1',__FILE__));
			sendTemplate($mailCode['mailCode'],$getMail['id'],0,$new_password);
			
                            if(!empty($_POST['ajax'])){
                                $json_forgot_answer = [
                                    'result' => 'success',
                                    'message' => 'ok'
                                ];
                                echo json_encode($json_forgot_answer);
                                die();
                            }
                            
			_goto($set->basepage.'?act=forgot_password&ty=1'.($lang ? '&lang='.$lang:''));
			}
			
	case "forgot_password":
		$set->pageTitle = lang('Change Your Password');
		
		if($selectedTextDirection == "RTL")
	$set->content .= '<link rel="stylesheet" href="css/login.css">';

	
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
							<td width="50%" style="font-family: Arial; color: #333333; padding-top: 30px;">
								
								
								
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
								
								
								
								
								<div style="padding-top: 3px;"><a href="/" style="color: #0D599A;">« '.lang('Back to login').'</a></div>
							</td>
							<td width="50%" valign="middle" align="center" style="font-family: Arial; color: #333333;">
								<div style="font-size: 20px; font-weight: bold; padding-bottom: 20px;">'.lang("Don't have an account yet?").'</div>
								<div style="padding-bottom: 10px;">'.lang("Get started now. it's fast and easy!").'</div>
								<a href="/?act=new_account'.($lang ? '&lang='.$lang:'').'">
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
					. ($set->hidePoweredByABLogo==0 ? '<td width="300" align="right"><a href="http://www.affiliatets.com/" target="_blank"><img border="0" src="images/main/powered.png" alt="Powered by AffiliateBuddies" /></a></td>' : '') 
					. (!empty($set->secondaryPoweredByLogo) ? '<td width="300" align="right"><a href="'.$set->secondaryPoweredByLogoHrefUrl.'" target="_blank"><img border="0" src="'. $set->secondaryPoweredByLogo .'" alt="Powered by AffiliateBuddies" /></a></td>' : '') . '
				</tr></table>
			</div>
			</div>';
			
			
		theme();
		break; 
	
	
	
	
	}
	
	
?>

