<?php
ini_set('memory_limit', '2048M');
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
chdir('../');
require_once('common/global.php');
require_once('common/subAffiliateData.php');
$userlevel = 'manager';
//checked blocked IPs

$set->webAddress = $set->isHttps?$set->webAddressHttps:$set->webAddress;

if($set->activateLogs==1){
$ip = getClientIp();

if(checkUserFirewallIP($ip)){

		
			$activityLogUrl = "http".$set->SSLswitch."://".$_SERVER['SERVER_NAME']. "/ajax/saveLogActivity.php?user_id=".$set->userInfo['id']."&ip=".$ip."&country=" . $country_id."&location=login&userType=manager&theChange=Blocked user trying to enter the system&_function_=".__FUNCTION__ . "&_file_=". __FILE__ . "&queryString=" . urlencode($queryString);
			 //die($activityLogUrl);
			doPost($activityLogUrl);
		
			
			$url = 'http'.$set->SSLswitch.'://'.$_SERVER['SERVER_NAME']."/404.php";
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


/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();

$hideDemoAndLeads = hideDemoAndLeads();

$networkWhere = '';


$group_id = $set->userInfo['group_id'] ;



if ($set->userInfo['level']!='admin' AND $set->isNetwork) {
    $networkWhere.=' AND id='.aesDec($_COOKIE['mid']);
}




	$loginEventArray= array();
			if ($chk['id'])
			$loginEventArray['error'] = false;
		else 
			$loginEventArray['error'] = true;
		
			$loginEventArray['username'] = $username;
			$loginEventArray['password'] = $password;
			$loginEventArray['type'] = 'manager';
			$loginEventArray['affiliate_id'] = $chk['id'];
			$loginEventArray['affiliate_valid'] = $chk['valid'];
			$loginEventArray['ip'] = $set->userRealIP;
			$loginEventArray['refe'] = $_SESSION['refe'];
			$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			$loginEventArray['login_as_affiliate_by_user_id'] = isset($_GET['admin']) ? $_GET['admin'] : (isset($_GET['manager']) ? $_GET['manager'] : 0);
			$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
			$lastOutCount = LoginEvent($loginEventArray,true,$set->numberOfFailureLoginsAttempts);
			
				if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
				$errors['errPass'] = 'block';
			}
			// var_dump($lastOutCount);
switch ($act) {
	case "login":
		
		$errors='';
		if (!$username) $errors['err'] = 1;
		if (!$password) $errors['errPass'] = 1;
		if (empty($errors) && $lastOutCount<$set->numberOfFailureLoginsAttempts) {
			$username = mysql_real_escape_string(str_replace(' ','',$username));
			$password = mysql_real_escape_string(str_replace(' ','',$password));
$username = strip_tags($username);
$password = strip_tags($password);
		
		
			//$resulta=function_mysql_query("SELECT id,username,chk_ip,password FROM admins WHERE lower(username)='".strtolower($username)."' AND password='".($admin ? $password : md5($password))."' AND valid='1' AND level='manager'",__FILE__);
			//die("SELECT admins.id,admins.username,admins.chk_ip,admins.password,admins.relatedMerchantID AS mid, merchants.producttype FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE lower(admins.username)='".strtolower($username)."' AND admins.password='".($admin ? $password : md5($password))."' AND admins.valid='1' AND admins.level='manager'");
			$qry = "SELECT admins.ip, admins.chk_ip, admins.id,admins.username,admins.chk_ip,admins.password,admins.relatedMerchantID AS mid, merchants.producttype FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE lower(admins.username)='".strtolower($username)."' AND admins.password='".($admin ? $password : md5($password))."' AND admins.valid='1' AND admins.level='manager'";
			$resulta=function_mysql_query($qry,__FILE__);
			
			//$resulta=function_mysql_query("SELECT admins.id,admins.username,admins.password,admins.relatedMerchantID, merchants.producttype AS mid FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE lower(username)='".strtolower($username)."' AND password='".md5($password)."' AND valid='1' AND level='admin'",__FILE__);
			$chk=mysql_fetch_assoc($resulta);
			
		
				if ($chk['chk_ip']) {
				$myip = getClientIP();
				$bool = false;
				$exp =  explode('|',$chk['chk_ip']);
				foreach ($exp as $ex) {
					if ($ex==$myip) {
						$bool=true;
						break;
					}
				}
			}
			if ($chk['chk_ip'] && !$bool) {
				die ('error:  insufficient memory to continue the execution of the program - WS2012R2 Version 6.3.7603');
			}
			
			$loginEventArray['affiliate_id'] = $chk['id'];
			$loginEventArray['affiliate_valid'] = $chk['valid'];
			$loginEventArray['loc'] = 'after_check_admin';
			$lastOutCount = LoginEvent($loginEventArray,false, $set->numberOfFailureLoginsAttempts);
			
			if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
				$errors['errPass'] = 'block';
			}
			
			if ($chk['id'])  {
				
				if ($chk['id']>1 && $set->blockAccessForManagerAndAdmins==1) {
					$resp = '<div style="margin-left:auto;margin-right:auto;margin-top:20px;width:800px;padding:30px; font-size:50px;border:1px solid black;border-radius:5px;">' . lang('We temporary freeze all your manager and admin accounts in system.<br>However the affiliates login and the tracking is still active.<br>Please contact us to to make those users active again.<br>Thanks<br>').'support@affiliatets.com<div>';
					die ($resp);
				}
				
				if ($chk['chk_ip'] AND $chk['chk_ip'] != $_SERVER['REMOTE_ADDR'] AND !$admin) {
					mail($set->webMail,$chk['username'].' trying to login into '.$set->webTitle.' From: '.$_SERVER['REMOTE_ADDR'].'!',print_r($_SERVER,1));
					die('<div align="center" style="padding-top: 3%; font-size: 16px; font-family: Tahoma;"><span style="color: red; font-weight: bold;">'.lang('ACCESS DENIED').'!</span><hr />IP Reported: <b>'.$_SERVER['REMOTE_ADDR'].'</b> to Administrators.</div>');
					}
				updateUnit('admins',"lang='".($lang ? $lang : 'ENG')."',ip='".$set->userIP."',logged='1',lastactive='".dbDate()."'","id='".$chk['id']."'");
				setcookie('setLang',$lang,time()+(24*60*60*30));
				setcookie('mid', aesEnc($chk['mid']), time() + (24 * 60 * 60 * 30));
				
				@session_start();
				$_SESSION['mid'] = aesEnc($chk['mid']);
				
				setcookie('productType',$chk['producttype'],time()+(24*60*60*30));
				//die($chk['producttype']);
				$_SESSION['session_id'] = $chk['id'];
				$_SESSION['session_serial'] = md5($chk['username'].$chk['password'].$chk['id']);
				$_SESSION['loggedin_time'] = time(); 
				$_SESSION['isam'] = 1;
                                
                                if (!empty($_POST['ajax'])) {
                                    echo json_encode(['result' => 'success', 'message' => 'ok']);
                                    die();
				}
                                
				_goto($set->SSLprefix.$set->basepage.'?act=main');
				
			} else {
				$errors['errPass'] = 1;
			}
			}else {
				$errors['errPass']=1;
			}
	
                        if (!empty($_POST['ajax'])) {
				echo json_encode(['result' => 'error', 'message' => 'Login incorrect']);
				die();
			}
	

	default:
            
            include('./manager/signin.php'); die();
            
		//if (isManager()) _goto('?act=main');
		//$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/manager/index.php?act=main";
		$lout = "/manager/index.php?act=main";
		if (isManager()) _goto($lout);

		$set->pageTitle = lang('Account Manager Login');
			
		$set->content = '
			<style type="text/css">
				html,body {
					background: #f5f5f5 !important;
					}
			</style>
			<script>
			function checkUsername(){
					username = $("#username").val();
					//if (username.match(/^[\w@].+$/) ==null) {
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
			<div class="admin_image" style="padding: 40px 0 40px 0; margin-top: -10px; border-top: 1px #FFFFFF solid;'.($set->adminLoginImage && strpos($set->adminLoginImage,"/tmp")===false?  "background-image:url('". $set->adminLoginImage. "');
			    background-size: cover;
    background-position: 0px -425px;
    height: 575px;
			" : ''). ' >
				<div align="center" style="width: 989px; height: 220px;">
					<div style="text-align: left; width: 400px; background: #FFFFFF; border: 1px #dddddd solid; font-family: Arial; padding: 20px;">
						'.lang('Welcome back account manager').', '.lang('please log in').':<br /><br />
						'. ($set->disableAutoCompleteOnLogin? '
						<table width="400">
							<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.($errors['err'] ? 'color: red;' : '').'">'.lang('Username').':</td><td align="right"><input type="text" name="username_new" id="username_new" value="'.$username.'" style="width: 250px;" /></td></tr>
							<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.($errors['errPass'] ? 'color: red;' : '').'">'.lang('Password').':</td><td align="right"><input type="password" name="password_new" id="password_new" style="width: 250px;" /></td></tr>
						</table>':'').
						
						($lastOutCount >= $set->numberOfFailureLoginsAttempts ? '<div style="color:red;padding-bottom:10px;" >'.lang('You have exceeded the number of allowed login attempts. Please try again in an hour.').'</div>' : '') . '
						<form method="post" id="managerLoginForm">
						<input type="hidden" name="act" value="login" />
						<div style="color:red;padding-bottom:10px;" class="loginerrors"></div>
							<table width="400">
								'. ($set->disableAutoCompleteOnLogin? '<input type="hidden" id="hiddenUsername" name="username"/>
								<input type="hidden" id="hiddenPassword" name="password"/>':'<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.($errors['err'] ? 'color: red;' : '').'">'.lang('Username').':</td><td align="right"><input type="text" name="username" id="username" value="'.$username.'" style="width: 250px;" /></td></tr>
								<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.($errors['errPass'] ? 'color: red;' : '').'">'.lang('Password').':</td><td align="right"><input type="password" name="password" style="width: 250px;" /></td></tr>').'
								
								<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal;">'.lang('Language').':</td><td align="right"><select name="lang" style="width: 262px;">'.listMulti().'</select></td></tr>
								<tr><td></td><td align="right"><input type="submit" onclick="return checkUsername()"value="'.lang('Login').'" /></td></tr>
							</table>
						</form>
					</div>
				</div>
			
			<div style="border-top: 1px #dddddd solid; padding: 25px 0 25px 0; background: #FFFFFF; margin-top: 40px;">
				<table width="989" border="0" cellpadding="0" cellspacing="0"><tr>
					<td align="left" style="font-size: 11px; color: #746d6d; font-family: Arial; text-align: justify;">
						<b>'.$set->webTitle.'</b> - The official '.$set->webTitle.' Affiliation - place you in the perfect position to claim your share of one of the most lucrative industries online. Over 3 trillion dollars is '.ptitle('traded').' every day in the financial markets and '.$set->webTitle.' offers you the most respected and rewarding brands to help you convert your web traffic into an unlimited source of revenue.
					</td>
					<td width="300" align="right"><a href="http://www.affiliatets.com/" target="_blank"><img border="0" src="images/main/powered.png" alt="Affiliate Buddies Logo" /></a></td>
				</tr></table>
			</div>
			</div>
			'. ($set->disableAutoCompleteOnLogin? '
				<script>
				 $("#managerLoginForm").submit(function() {
					$("#hiddenUsername").val($("#username_new").val());
					$("#hiddenPassword").val($("#password_new").val());
				  });
				  $("#username_new,#password_new").keypress(function(e) {
					if (e.which == 13) {
					  $("#managerLoginForm").submit();
					}
				  });
				</script>':'');
			
		theme();
		break;
	
	case "main":
		//if (!isManager()) _goto($set->SSLprefix.'manager/');
		$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/manager/";
		if (!isManager()) _goto($lout);

		$set->noFilter = 1;
		
		if(!$from)	$from = date("d/m/Y 00:00:00");
		if(!$to)	$to = date("d/m/Y 23:59:59");
		$from = strTodate($from);
		$to = strTodate($to);
		
		$from = sanitizeDate($from);
		$to   = sanitizeDate($to);
		
		commonGlobalSetTimeRange($from, $to);
		
		// List Merchants
		$viewsSum               = 0;
		$clicksSum              = 0;
		$totalLeads             = 0;
		$totalCPI             = 0;
		$totalDemo              = 0;
		$totalReal              = 0;
		$newFTD                 = 0;
		$ftdAmount              = 0;
		$totalBonus             = 0;
		$totalWithdrawal        = 0;
		$mType                  = 0;
		$totalFrozens           = 0;
		$totalCredits           = 0;
                $strCurrentMerchantType = '';
                
		$qry = "SELECT * FROM merchants WHERE valid = 1 " . $networkWhere. " ORDER BY producttype, pos";
		$merchantqq=function_mysql_query($qry,__FILE__);
                
		if($set->isNetwork){
			$merchantName = mysql_fetch_assoc($merchantqq);
			$merchantName = $merchantName['name'];
		}else{
			$merchantName = '';
		}
		
		$pageTitle = ($set->isNetwork ? $merchantName . ' ': '' ).  lang('Home Screen - Dashboard');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<!--li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li-->
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
		$totalFrozens     = 0;
		$showCasinoFields = 0;


                $strQuery = "SELECT sum(credit) as credit FROM affiliates WHERE valid = 1 AND group_id = '" . $set->userInfo['group_id'] . "'";
                $resource = function_mysql_query($strQuery,__FILE__);
                $arrRow = mysql_fetch_assoc($resource);
                $totalCredits = $arrRow['credit'];
                
                
                // List of wallets.
                $arrWallets = array();
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1;";
                $resourceWallets = function_mysql_query($sql,__FILE__);
		
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
                
	while ($merchantww = mysql_fetch_assoc($merchantqq)) {
			$netDepositTransactions = array();
                     // Check if this is a first itaration on given wallet.
                    if ($set->multiMerchantsPerTrader==1)
					$needToSkipMerchant = $arrWallets[$merchantww['wallet_id']];
				else 
					$needToSkipMerchant= false;
				
                    
                    $showCasinoFields = 0;
                    
                    $totalTraffic                = [];



                    if (strtolower($merchantww['producttype']) == 'casino' || strtolower($merchantww['producttype']) == 'sportsbetting') {
                        $showCasinoFields = 1;
                    }
                    

                    
                    
                    if ($merchantww['producttype'] != $currentType) {
						
						
				
				$mType++;
				$currentType = $merchantww['producttype'];
				if ($mType > 1) {
                                    $listMerchants    .= '</tbody><tfoot>
                                            <tr>
                                                <th><b>'.lang('Total').':</b></th>
                                                <th align="center">'.$mviewsSum.'</th>
                                                <th align="center">'.$mclicksSum.'</th>
                                                '.($set->deal_cpi?'<th align="center">'.$mtotalCPI.'</th>':'').'
                                                ' . ($hideDemoAndLeads ? '' : '<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.number_format($mtotalLeads,0).'</a></th>
                                                <th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.number_format($mtotalDemo,0).'</a></th>') . '
                                                <th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.number_format($mtotalReal,0).'</a></th>
                                                ' . ($strCurrentMerchantType == 'casino' || $strCurrentMerchantType == 'sportsbetting' ? '<th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=frozen">'.number_format($mtotalFrozens, 0).'</a></th>' : '') . '
                                                <th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$mnewFTD.'</a></th>
                                                <th align="center">'.price(($mftdAmount)).'</th>
                                                <th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=totalftd">'.($mtotalRealFtd).'</a></th>
                                                <th align="center">'.price(($mtotalRealFtdAmount)).'</th>
                                                <th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.($mtotal_deposits).'</a></th>
                                                <th align="center">'.price(($mtotal_depositsAmount)).'</th>
                                                <th align="center">'.price(@round(($mtotal_depositsAmount/$mnewFTD),2)).'</th>
                                                <th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.price($mtotalBonus).'</a></th>
												<th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=withdrawal">'.price($mtotalWithdrawal).'</a></th>
												<th align="center"><a href="'.$set->SSLprefix.'admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=chargeback">'.price($mtotalChargeback).'</a></th>
												'.($productType == 'forex' ? '
                                                <th align="center">'.price(($mtotalLots)).'</th>
												' :'').'
                                                <th align="center"><a href="/admin/reports.php?act=transactions&netdeposit=1&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'
                                                . price(($mtotalNetRevenue)).'</a></th>'
												.($set->deal_pnl==1 ? '<th align="center">'.price(($mtotalPnl)).'</th>':'').
                                                '<th align="center">'.price(($mtotalComs)).'</th>
                                            </tr>
                                        </tfoot></table>' . getPager();
                                        
                                        $mviewsSum = 0;
                                        $mclicksSum = 0;
                                        $mtotalLeads = 0;
                                        $mtotalDemo = 0;
                                        $mtotalReal = 0;
                                        $mTotalCPI = 0;
                                        $mnewFTD = 0;
                                        $mtotal_deposits = 0;
                                        $mtotal_depositsAmount = 0;
                                        $mftdAmount = 0;
                                        $mtotalBonus = 0;
                                        $mtotalLots = 0;
                                        $mtotalWithdrawal = 0;
                                        $mtotalChargeback = 0;
                                        $mtotalLots = 0;
                                        $mtotalNetRevenue = 0;
                                        $mtotalFrozens = 0;
                                        $mtotalRealFtd = 0;
                                        $mtotalRealFtdAmount = 0;
                                        $strCurrentMerchantType = '';
                                        $mtotalComs = 0;
                                        $mtotalPnl = 0;
                                }
					$productType = strtolower($merchantww['producttype']);
				
				
				
				
				
				
				if ($set->dashBoardMainTitle<>'')
				$listMerchants .= '<div class="space">&nbsp;</div><div class="specialTableTitle">'.$set->dashBoardMainTitle .'</div>';
				else
				$listMerchants .= '<div class="space">&nbsp;</div><div class="specialTableTitle">'.lang(typeName($merchantww['producttype'])).' '.(lang(ptitle('Brokers',ucwords($merchantww['producttype'])))).'</div>';
				
				$listMerchants .= '<table class="tablesorter" width="100%" border="0" cellpadding="4" cellspacing="0">
						<thead>
							<tr>
								<th width="8%">'.lang(ptitle('Merchant')).'</th>
								<th align="center">'.lang('Impression').'</th>
								<th align="center">'.lang('Clicks').'</th>' .
								($set->deal_cpi?'<th align="center">'.lang('CPI').'</th>' :'')
								. ($hideDemoAndLeads ? '' : '
                                                                <th align="center">'.lang(ptitle('Leads',ucwords($merchantww['producttype']))).'</th>
								<th align="center">'.lang(ptitle('Demo')).'</th>') . '
								<th align="center">'.lang(ptitle('Accounts',ucwords($merchantww['producttype']))).'</th>
								'.($showCasinoFields ? '<th align="center">'.lang(ptitle(('Frozens'))).'</th>' : '').'
								<th align="center">'.lang(('FTD')).'</th>
								
								<th align="center">'.lang(('FTD Amount')).'</th>
								<th align="center">'.lang(('RAW FTD')).'</th>
								<th align="center">'.lang(('RAW FTD Amount')).'</th>
								<th align="center">'.lang(('Deposits')).'</th>
								<th align="center">'.lang(('Deposit Amount')).'</th>
								<th align="center">'.lang(ptitle('Player Value')).'</th>
								<th align="center">'.lang('Bonus').'</th>
								<th align="center">'.lang(('Withdrawal')).'</th>
								<th align="center">'.lang(('ChargeBack')).'</th>
								
								'.(strtolower($merchantww['producttype']) == 'forex' ? '<!--<th align="center">'.lang(('Lots')).'</th>-->':'').'
								
								<th align="center">'.lang(ptitle('Net Revenue')).'</th>'
								.($set->deal_pnl==1 ? '
								<th>'.lang(ptitle('PNL')).'</th> ' : '').
								'<th align="center">'.lang(('Commission')).'</th>
							</tr>
						</thead>
						<tbody>';
                    }
             
                               
			$l++;
			$ftd_amount['amount']=0;
			$mftd_amount['amount']=0;
			$new_ftd=0;
			$depositsAmount=0;
			$totalDeposits=0;
			$total_leads=0;
			$total_demo=0;
			$total_real=0;
			$totalCPI=0;
			$bonus=0;
			$lots=0;
			$totalPNL=0;
			$withdrawal=0;
			$chargeback=0;
			$netRevenue=0;
			$real_ftd_amount=0;
			$new_real_ftd = 0;
			$totalCom=0;
			
                        
                    /*******************************************/                        
                    
                    
                    
                        $dasboardQuery = "select sum(db.Impressions) as Impressions, 
            		    sum(db.Clicks) as Clicks,  
            		    sum(db.Install) as Install, 
            		    sum(db.Leads) as Leads,  
            		    sum(db.Demo) as Demo,  
            		    sum(db.RealAccount) as RealAccount,  
            		    sum(db.FTD) as FTD,  
            		    sum(db.FTDAmount) as FTDAmount,  
            		    sum(db.RawFTD) as RawFTD,  
            		    sum(db.RawFTDAmount) as RawFTDAmount,  
            		    sum(db.Deposits) as Deposits,  
            		    sum(db.DepositsAmount) as DepositsAmount,  
            		    sum(db.Bonus) as Bonus,  
            		    sum(db.Withdrawal) as Withdrawal,  
            		    sum(db.ChargeBack) as ChargeBack,  
            		    sum(db.NetDeposit) as NetDeposit,  
            		    sum(db.PNL) as PNL,  
            		    sum(db.ActiveTrader) as ActiveTrader,  
            		    sum(db.Commission) as Commission,  
            		    sum(db.PendingDeposits) as PendingDeposits,  
            		    sum(db.PendingDepositsAmount) as PendingDepositsAmount 
            		    FROM 
            		    Dashboard db
            		    INNER JOIN affiliates aff ON db.affiliateID = aff.id AND aff.group_id = ".$set->userInfo['group_id']."
            		    WHERE db.MerchantID = ".$merchantww['id']." AND db.Date>='".$from."' and Date<'".$to."' ";

            		    $dasboardDataResult = function_mysql_query($dasboardQuery);

		                
		                $dasboardData = mysql_fetch_assoc($dasboardDataResult);
                    

                    $totalTraffic['totalViews'] = $dasboardData['Impressions'];
                    $totalTraffic['totalClicks'] = $dasboardData['Clicks'];
                    $totalCPI = $dasboardData['Install'];
                    $total_leads = $dasboardData['Leads'];
                    $total_demo = $dasboardData['Demo'];
                    $total_real = $dasboardData['RealAccount'];
                    $frozens = 0;
                    $new_ftd = $dasboardData['FTD'];
                    $ftd_amount['amount'] = $dasboardData['FTDAmount'];
                    $new_real_ftd = $dasboardData['RawFTD'];
                    $real_ftd_amount = $dasboardData['RawFTDAmount'];
                    $totalDeposits = $dasboardData['Deposits'];
                    $depositsAmount = $dasboardData['DepositsAmount'];
                    $bonus = $dasboardData['Bonus'];
                    $withdrawal = $dasboardData['Withdrawal'];
                    $chargeback = $dasboardData['ChargeBack'];
                    $netRevenue = ($dasboardData['DepositsAmount'] - ($dasboardData['Withdrawal'] + $dasboardData['ChargeBack']));
                    $totalPNL = $dasboardData['PNL'];
                    $totalCom = $dasboardData['Commission'];
                    
                    
                    
                    /*******************************************/

			$listMerchants .= '<tr>
								<td style="color: #646464;"><b>'.$merchantww['name'].'</b></td>
								<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($totalTraffic['totalViews'] ? $totalTraffic['totalViews'] : '0').'</a></td>
								<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($totalTraffic['totalClicks'] ? $totalTraffic['totalClicks'] : '0').'</a></td>
								'.($set->deal_cpi?'<td align="center">'.($totalCPI ? $totalCPI : '0').'</td>':'')
								 . ($hideDemoAndLeads ? '' : '<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=lead">'.number_format($total_leads,0).'</a></td>
								<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=demo">'.number_format($total_demo,0).'</a></td>') . '
								<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=real">'.number_format($total_real,0).'</a></td>
								'.($showCasinoFields ? '<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=frozen">'.$frozens.'</a></td>' : '').'
								<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=ftd">'.$new_ftd.'</a></td>
								<td align="center">'.price($ftd_amount['amount']).'</td>
								<td align="center">'.$new_real_ftd.'</td>
								<td align="center">'.price($real_ftd_amount).'</td>
								<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=deposit">'.$totalDeposits.'</a></td>
								<td align="center">'.price($depositsAmount).'</td>
								<td align="center">'.price(@round($depositsAmount/$new_ftd,2)).'</td>
								<td align="center">'.price($bonus).'</td>
								<td align="center">'.price($withdrawal).'</td>
								<td align="center">'.price($chargeback).'</td>
								<td align="center">'.price($netRevenue).'</td>'
								.($set->deal_pnl==1 ? '
									<td>'.price($totalPNL).'</td> ' : '').
								'<td align="center">'.price($totalCom).'</td>
							</tr>';
			
            // $mviewsSum+=$total['viewsSum'];
            $mviewsSum+=$totalTraffic['totalViews'];
			// $mclicksSum+=$total['clicksSum'];
			$mclicksSum+=$totalTraffic['totalClicks'];
			$mTotalCPI+=$totalCPI;
			$mtotalLeads+=$total_leads;
			$mtotalDemo+=$total_demo;
			$mtotalReal+=$total_real;
			$mnewFTD+=$new_ftd;
			$mtotal_deposits+=$totalDeposits;
			$mtotal_depositsAmount+=$depositsAmount;
			$mftdAmount+=$ftd_amount['amount'];
			$mtotalBonus+=$bonus;
			$mtotalWithdrawal+=$withdrawal;
			$mtotalChargeBack+=$chargeback;
			$mtotalNetRevenue+=$netRevenue;
			$mtotalComs+=$totalCom;
			$mtotalPnl+=$totalPNL;
			$mtotalFrozens+=$frozens;
			$mtotalRealFtd+=$new_real_ftd;
			$mtotalRealFtdAmount+=$real_ftd_amount;
                        $strCurrentMerchantType = strtolower($merchantww['producttype']);
                        
			// $viewsSum+=$total['viewsSum'];
			$viewsSum+=$totalTraffic['totalViews'];
			// $clicksSum+=$total['clicksSum'];
			$clicksSum+=$totalTraffic['totalClicks'];
			$totalLeads+=$total_leads;
			$totalDemo+=$total_demo;
			$totalReal+=$total_real;
			$newFTD+=$new_ftd;
			$total_deposits+=$totalDeposits;
			$total_depositsAmount+=$depositsAmount;
			$ftdAmount+=$ftd_amount['amount'];
			$totalBonus+=$bonus;
			$totalWithdrawal+=$withdrawal;
			$totalChargeBack+=$chargeback;
			$totalNetRevenue+=$netRevenue;
			$totalComs+=$totalCom;
			$totalPNLs+=$totalPNL;
			$totalFrozens+=$frozens;
			$totalRealFtd+=$new_real_ftd;
			$totalRealFtdAmount+=$real_ftd_amount;
			
                        
                    // Mark given wallet as processed.
                    $arrWallets[$merchantww['wallet_id']] = true;
                }
		// List Merchants
	
		$set->rightBar = '<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
						<input type="hidden" name="act" value="main" />
						<table><tr>
							<td>'.timeFrame($from,$to).'</td>
							<td><input type="submit" value="'.lang('View').'" /></td>
						</tr></table>
						</form>';
		
		

		//$set->content .= chart('0','manager',$set->userInfo['group_id']).'
		//$set->content .= chart('0','manager').'
		// echo 'group: ' . ($set->userInfo['group_id']).'<Br><br>';
		
		$set->content .=' <link rel="stylesheet" href="'.$set->SSLprefix.'js/unslider/dist/css/unslider.css">
		<script src="'.$set->SSLprefix.'js/unslider/dist/js/unslider.js"></script>
		<div class="my-slider" style="display:none">
			<div style="float:right;margin-right:10px;margin-top:5px;position:absolute;left:95%;z-index:99999;" class="refresh1"><a class="refresha tooltip" onclick="refreshChart2();" style="font-size: 11px; cursor: pointer;display:none" >'. ($set->chartTheme=='dark_unica'?'<img src=\''.$set->SSLprefix.'images/refresh_white.png\' width=20 />':'<img src=\''.$set->SSLprefix.'images/refresh_black.png\' width=30 />') . '</a></div>
			<ul>
				<li data-slide="0" data-name="Performance Chart" class="unslider-active">  '.highchart('0','manager',$set->userInfo['group_id']) .'</li>
				<li data-slide="1" data-name="Conversion Chart" >'. conversionHighchart('0','manager',$set->userInfo['group_id']).'</li>
			</ul>
		</div>		
<script>
window.onload = function() {
		$(".my-slider").show();
		refreshData2();
		refreshData3();
}
$(document).ready(function(){
	$(".my-slider").mouseover(function(){
		$(".refresha").show();
	});
	$(".my-slider").mouseout(function(){
		$(".refresha").hide();
	});
	$(".my-slider").unslider({
		arrows: false,
		dots:true
	});
	
	$(".unslider-nav li").on("click",function(){
		slide = $(this).data("slide");
		if(slide == 0){
			$(".refresh1").html("<a class=\"refresha tooltip\" id=\'refreshData2\' onclick=\"refreshChart2();\" style=\"font-size: 11px; cursor: pointer;display:none\">'. ($set->chartTheme=='dark_unica'?'<img src=\''.$set->SSLprefix.'images/refresh_white.png\' width=20 />':'<img src=\''.$set->SSLprefix.'images/refresh_black.png\' width=30 />') . '</a>")
		}
		else{
			$(".refresh1").html("<a class=\"refresha tooltip\" id=\'refreshData3\' onclick=\"refreshChart3();\" style=\"font-size: 11px; cursor: pointer;display:none\">'. ($set->chartTheme=='dark_unica'?'<img src=\''.$set->SSLprefix.'images/refresh_white.png\' width=20 />':'<img src=\''.$set->SSLprefix.'images/refresh_black.png\' width=30 />') . '</a>")
		}
	});
	
	
	
});
</script>
<style>
.unslider-nav ol{
	padding:10px;
	text-align:center;
}
/* .unslider-nav ol li:first-child:after{
	content : "   |   ";
	padding: 0 10px;
} */
.unslider-nav ol li{
	display:inline;
	cursor:pointer;
}
</style>
<div class="normalTableTitle" >'.lang('Merchants Performance').'<span class="imgGear" style="float:right;"><img class="imgReportFieldsSettings_dashstat" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>	
			<div class="dashStat mainDashStat">
			<table width="100%" border="0" cellpadding="4" cellspacing="5" class="dashStatFields"><tr>
				<td class="dashStat '.lang('Impressions').'">
					'.lang('Impressions').'<br />
					<span style="font-size: 18px; font-weight: bold;"><a href="'.$set->SSLprefix.'manager/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($viewsSum ? number_format($viewsSum) : '0').'</a></span>
				</td>
				<td class="dashStat '.lang('Clicks').'">
					'.lang('Clicks').'<br />
					<span style="font-size: 18px; font-weight: bold;"><a href="'.$set->SSLprefix.'manager/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($clicksSum ? number_format($clicksSum) : '0').'</a></span>
				</td>
				'.($set->deal_cpi?'<td class="dashStat '.lang('CPI').'">
					'.lang('CPI').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.($mTotalCPI ? number_format($mTotalCPI) : '0').'</span>
				</td>':'').'
				' . ($hideDemoAndLeads ? '' : '<td class="dashStat '.lang(ptitle('Leads')).'">
					'.lang(ptitle('Leads')).'<br />
					<a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead" style="font-size: 18px; font-weight: bold;">'.number_format($totalLeads+$marketTotal_leads).'</a>
				</td>
				<td class="dashStat '.lang(ptitle('Demo')).'">
					'.lang(ptitle('Demo')).'<br />
					<a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo" style="font-size: 18px; font-weight: bold;">'.number_format($totalDemo+$marketTotal_demo).'</a>
				</td>') . '
				<td class="dashStat '.lang(ptitle('Real Account')).'">
					'.lang(ptitle('Real Account')).'<br />
					<a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real" style="font-size: 18px; font-weight: bold;">'.number_format($totalReal+$marketTotal_real).'</a>
				</td>
				'.($showCasinoFields ? '<td class="dashStat '.lang('Frozens').'">
					'.lang('Frozens').'<br />
					<a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=frozen" style="font-size: 18px; font-weight: bold;">'.number_format($totalFrozens,0).'</a>
				</td>' : '').'
				<td class="dashStat '.lang('FTD').'">
					'.lang('FTD').'<br />
					<a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd" style="font-size: 18px; font-weight: bold;">'.number_format($newFTD+$marketTotal_FTDs,0).'</a>
				</td>
				<td class="dashStat '.lang('FTD Amount').'">
					'.lang('FTD Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($ftdAmount+$marketTotal_FTDAmount,2).'</span>
				</td>
				<td class="dashStat '.lang('RAW FTD').'">
					'.lang('RAW FTD').'<br />
					<a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=totalftd" style="font-size: 18px; font-weight: bold;">'.number_format($totalRealFtd,0).'</a>
				</td>
				
				<td class="dashStat '.lang('RAW FTD Amount').'">
					'.lang('RAW FTD Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalRealFtdAmount).'</span>
				</td>
				</tr><tr>
				<td class="dashStat '.lang('Deposits').'">
					'.lang('Deposits').'<br />
					<a href="'.$set->SSLprefix.'manager/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit" style="font-size: 18px; font-weight: bold;">'.number_format($total_deposits,0).'</a>
				</td>
				<td class="dashStat '.lang('Deposits Amount').'">
					'.lang('Deposits Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($total_depositsAmount+$marketTotal_depositAmount,2).'</span>
				</td>
				<td class="dashStat '.lang('Bonus').'">
					'.lang('Bonus').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalBonus+$marketTotal_Bonus,2).'</span>
				</td>
				<td class="dashStat '.lang('Withdrawal').'">
					'.lang('Withdrawal').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalWithdrawal+$marketTotal_withdrawal,2).'</span>
				</td>
				<td class="dashStat '.lang('ChargeBack').'">
					'.lang('ChargeBack').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalChargeback,2).'</span>
				</td>
				<td class="dashStat '.lang(ptitle('Net Revenue')).'">
					'.lang(ptitle('Net Revenue')).'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalNetRevenue,2).'</span>
				</td>'
				.($set->deal_pnl==1 ? '
					<td class="dashStat '.lang(ptitle('PNL')).'">
					'.lang(ptitle('PNL')).'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalPNLs,2).'</span>
					</td>':'').
				'<td class="dashStat '.lang('Commission').'">
					'.lang('Commission').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalComs,2).'</span>
					</td>'
				. ($set->showCreditForAM == 1 ? '<td class="dashStat '.lang('Credit').'">
						'.lang('Credit').'<br />
						<span style="font-size: 18px; font-weight: bold;">' . price($totalCredits, 2) . '</span>
					</td>' : '') . 
			'</tr></table>
			</div>
			';
		
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
		$set->sortTable = 1;
		$set->noFilter = 1;
		
		if (mysql_num_rows($merchantqq) > 1) {
			$set->content .= $listMerchants.'</tbody><tfoot>
                            <tr>
                                    <th><b>'.lang('Total').':</b></th>
                                    <th align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.$mviewsSum.'</a></th>
                                    <th align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.$mclicksSum.'</a></th>
                                    '.($set->deal_cpi?'<th align="center">'.$mTotalCPI.'</th>':'').'
                                    ' . ($hideDemoAndLeads ? '' : '<th align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.number_format($mtotalLeads,0).'</a></th>
                                    <th align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.number_format($mtotalDemo,0).'</a></th>') . '
                                    <th align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.number_format($mtotalReal,0).'</a></th>
									'.($showCasinoFields ? '<td align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=frozen">'.$mtotalFrozens.'</a></td>' : '').'
									
									
                                    <th align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$mnewFTD.'</a></th>
                                    <th align="center">'.price($mftdAmount).'</th>
                                    <th align="center">'.$mtotalRealFtd.'</th>
                                    <th align="center">'.price($mtotalRealFtdAmount).'</th>
                                    <th align="center"><a href="'.$set->SSLprefix.'manager/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.$mtotal_deposits.'</a></th>
                                    <th align="center">'.price($mtotal_depositsAmount).'</th>
                                    <th align="center">'.price(@round($mtotal_depositsAmount/$mnewFTD,2)).'</th>
                                    <th align="center">'.price($mtotalBonus).'</th>
                                    <th align="center">'.price($mtotalWithdrawal).'</th>
                                    <th align="center">'.price($mtotalChargeback).'</th>
                                    <th align="center">'.price($mtotalNetRevenue).'</th>'
                                    .($set->deal_pnl==1 ? '<th align="center">'.price($mtotalPnl).'</th>':'').'
                                    <th align="center">'.price($mtotalComs).'</th>
                            </tr>
                        </tfoot></table>' . getPager();
                }
                
		$set->content .= '<div class="space">&nbsp;</div>';
		//modal
		$fields = getReportsHiddenCols("dashStatCols","manager",$set->userInfo['id']);
		if($fields){
			$set->DashboardDashStatHiddenCols = $fields;
		}
		
			
		$sql = "SELECT * FROM affiliates WHERE valid='1' AND group_id='".$set->userInfo['group_id']."' AND logged='1' ORDER BY id DESC LIMIT 10";
		$qq=function_mysql_query($sql,__FILE__); //  LIMIT $pgg,$getPos
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$affList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
					<td align="center">'.$ww['id'].'</td>
					<td align="center"><a href="'.$set->SSLprefix.'manager/affiliates.php?act=new&id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="'.$set->SSLprefix.'?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank">'.lang('Login').'</a></td>
					<td align="left">'.$ww['username'].'</td>
					<td align="left">'.$ww['first_name'].'</td>
					<td align="left">'.$ww['last_name'].'</td>
					<td align="center">'.getCountry($ww['country'],1).'</td>
					<td align="center">'.dbDate($ww['lastvisit']).'</td>
					<td align="center"><img border="0" src="'.$set->SSLprefix.'manager/images/logged_'.$ww['logged'].'.png" alt="'.dbDate($ww['lastactive']).'" title="'.dbDate($ww['lastactive']).'" /></td>
				</tr>';
			}
		
		$set->content .= '<div class="normalTableTitle">'.lang('Desk Affiliate Sign Up Link').'</div>
		<div align="left" style="border: 1px #DDDDDD solid; padding: 10px; font-family: Arial;"><a href="'.$set->SSLprefix.'?act=new_account&group_id='.$set->userInfo['group_id'].'" target="_blank">'.$set->webAddress.'?act=new_account&group_id='.$set->userInfo['group_id'].'</a><br /><span style="font-size: 11px;">Use this link to associate your affiliates directly under your desk.</span></div>';
		
		
		$set->content .= '<div class="normalTableTitle">'.lang('My Online Affiliates').'</div>
			<table class="normal" width="100%" border="0" cellpadding="0" cellspacing="0">
				<thead>
				<tr>
					<th align="center">'.lang('Affiliate ID').'</th>
					<th align="center">'.lang('Actions').'</th>
					<th align="left">'.lang('Username').'</th>
					<th align="left">'.lang('First Name').'</th>
					<th align="left">'.lang('Last Name').'</th>
					<th align="center">'.lang('Country').'</th>
					<th align="center">'.lang('Last Visit').'</th>
					<th align="center">'.lang('Logged').'</th>
				</tr></thead><tfoot></tfoot>
				<tbody>
				'.$affList.'
			</table>
			
			';
		$qq=function_mysql_query("SELECT * FROM affiliates_notes WHERE group_id='".$set->userInfo['group_id']."' AND status != 'closed' ORDER BY issue_date DESC LIMIT 10",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			unset($bgColor);
			$adminInfo = dbGet($ww['admin_id'],"admins");
			$affiliateInfo = dbGet($ww['affiliate_id'],"affiliates");
			$groupInfo = dbGet($affiliateInfo['group_id'],"groups");
			if ($ww['status'] == "inprocess") $bgColor = 'style="background: #d4deff;"';
				else if ($ww['status'] == "closed") $bgColor = 'style="background: #d4ffdd;"';
			if ($ww['issue_date'] < dbDate() AND $ww['status'] != "closed") $bgColor = 'style="background: #ffd4d4;"';
			$noteList .= '<tr '.($l % 2 ? 'class="trLine"' : '').' '.$bgColor.'>
						<td>'.$ww['id'].'</td>
						<td><a href="'.$set->SSLprefix.'manager/affiliates.php?act=new&id='.$ww['affiliate_id'].'&note_id='.$ww['id'].'&toggleTo=manager_notes_crm" target="_blank">'.lang('Edit').'</a></td>
						<td>'.dbDate($ww['rdate']).'</td>
						'. ($ww['admin_id']==-1?'<td>'. lang('SYSTEM') .'</td>':'<td align="center"><a href="'.$set->SSLprefix.'manager/admins.php?act=new&id='.$adminInfo['id'].'" target="_blank">'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</a></td>').'
						<td>'.dbDate($ww['issue_date']).'</td>
						<td align="center"><a href="'.$set->SSLprefix.'manager/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$ww['affiliate_id'].'</a></td>
						<td align="center"><a href="'.$set->SSLprefix.'manager/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$affiliateInfo['username'].'</a></td>
						<td align="center">'.($groupInfo['id'] ? $groupInfo['title'] : lang('General')).'</td>
						<td align="center">'.round(floor((strtotime($ww['closed_date'] == "0000-00-00 00:00:00" ? dbDate() : $ww['closed_date'])-strtotime($ww['rdate']))/(60*60*24))+1).' '.lang('Day(s)').'</td>
						<td align="left">'.$ww['notes'].'</td>
						<td align="center"><b>'.lang(strtoupper($ww['status'])).'</b></td>
					</tr>';
			$l++;
			}
	$set->content .= '<br /><div class="specialTableTitle">'.lang('Manager Notes').' '.lang('CRM').'</div>
			<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
				<thead>
				<tr>
					<td>#</td>
					<td align="center">'.lang('Actions').'</td>
					<td align="center">'.lang('Created Date').'</td>
					<td align="center">'.lang('Added By').'</td>
					<td align="center">'.lang('Issue Date').'</td>
					<td align="center">'.lang('Affiliate ID').'</td>
					<td align="center">'.lang('Affiliate Username').'</td>
					<td align="center">'.lang('Affiliate Group').'</td>
					<td align="center">'.lang('Processing Time').'</td>
					<td style="text-align: left;">'.lang('Notes').'</td>
					<td align="center">'.lang('Status').'</td>
				</tr></thead><tfoot>'.$noteList.'</tfoot>
			</table>
			
			<br />
			';
	//echo $set->DashboardProductsPlaceHiddenCols;die;
	include "common/ReportFieldsModal.php";
			
	if($showCasinoFields){
			$set->content=str_replace('<td>|FROZEN|</td>','<td></td>',$set->content);
			$set->content=str_replace('<th>|FROZEN|</th>','<th></th>',$set->content);
		}else{
			$set->content=str_replace('<td>|FROZEN|</td>','',$set->content);
			$set->content=str_replace('<th>|FROZEN|</th>','',$set->content);
		}
		
		theme();
		break;
	
	case "logout":
		unset($_SESSION['session_id']);
		unset($_SESSION['session_serial']);
		unset($_SESSION['isam']);
		unset($_SESSION['loggedin_time']);
		//_goto($set->SSLprefix.'manager/');
		$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/manager/";
		if (!isManager()) _goto($lout);
		break;
	
	}

?>
