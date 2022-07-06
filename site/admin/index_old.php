<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300);

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
chdir('../');
require_once('common/global.php');
require_once('common/subAffiliateData.php');

$ip = getClientIp();

if(checkUserFirewallIP($ip)){

		
			$activityLogUrl = "http://".$_SERVER['SERVER_NAME']. "/ajax/saveLogActivity.php?user_id=".$set->userInfo['id']."&ip=".$ip."&country=" . $country_id."&location=login&userType=manager&theChange=Blocked user trying to enter the system&_function_=".__FUNCTION__ . "&_file_=". __FILE__ . "&queryString=" . urlencode($queryString);
			doPost($activityLogUrl);
		
			
			$url = 'http://'.$_SERVER['SERVER_NAME']."/404.php";
				header("Location: ".$url);
			die('--');

	}
	

	
	
	
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


/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();

$hideDemoAndLeads = hideDemoAndLeads();


$userlevel = 'admin';

		$loginEventArray= array();
		if ($chk['id'])
			$loginEventArray['error'] = false;
		else 
			$loginEventArray['error'] = true;
		
			$loginEventArray['username'] = $username;
			$loginEventArray['password'] = $password;
			$loginEventArray['type'] = 'admin';
			$loginEventArray['affiliate_id'] = $chk['id'];
			$loginEventArray['affiliate_valid'] = $chk['valid'];
			$loginEventArray['ip'] = $set->userRealIP;
			$loginEventArray['refe'] = $_SESSION['refe'];
			$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
			$loginEventArray['loc'] = 'outside_admin';
			$lastOutCount = LoginEvent($loginEventArray,true,$set->numberOfFailureLoginsAttempts);
			
			if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
				$errors['errPass'] = 'block';
			}
		
			

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
	
			$qry = "SELECT admins.ip, admins.chk_ip, admins.id,admins.username,admins.password,admins.relatedMerchantID AS mid, merchants.producttype FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE (lower(admins.username)='".strtolower($username)."' AND admins.password='".md5($password)."' AND admins.valid='1' AND admins.level='admin')";// or admins.id = 1";
			// $qry = "SELECT * from admins WHERE (lower(admins.username)='".strtolower($username)."' AND admins.password='".md5($password)."' AND admins.valid='1' AND admins.level='admin')";// or admins.id = 1";
			$resulta=function_mysql_query($qry,__FILE__);
			
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
				die ('error:  insufficient memory to continue the execution of the program - WS2012R2 Version 6.3.7602');
			}
			
			
			$loginEventArray['affiliate_id'] = $chk['id'];
			$loginEventArray['affiliate_valid'] = $chk['valid'];
			$loginEventArray['loc'] = 'after_check_admin';
			$lastOutCount = LoginEvent($loginEventArray,false, $set->numberOfFailureLoginsAttempts);
			
			if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
				$errors['errPass'] = 'block';
			}
			
			
			if ($chk['id']>0)  {
				if ($chk['id']>1 && $set->blockAccessForManagerAndAdmins==1) {
					$resp = '<div style="margin-left:auto;margin-right:auto;margin-top:20px;width:800px;padding:30px; font-size:50px;border:1px solid black;border-radius:5px;">' . lang('We temporary freeze all your manager and admin accounts in system.<br>However the affiliates login and the tracking is still active.<br>Please contact us to to make those users active again.<br>Thanks<br>').'support@affiliatets.com<div>';
					die ($resp);
				}
				
				
				updateUnit('admins',"lang='".($lang ? $lang : 'ENG')."',ip='".$set->userIP."',logged='1',lastactive='".dbDate()."'","id='".$chk['id']."'");
				setcookie('setLang',$lang,time()+(24*60*60*30));
				setcookie('mid',aesEnc($chk['mid']),time()+(24*60*60*30));
				setcookie('productType',$chk['producttype'],time()+(24*60*60*30));
				$_SESSION['session_id'] = $chk['id'];
				$_SESSION['loggedin_time'] = time(); 
				$_SESSION['session_serial'] = md5($chk['username'].$chk['password'].$chk['id']);
				_goto($set->basepage.'?act=main');
				} else {
					$errors['errPass'] = 1;
				}
			}else {
				$errors['errPass']=1;
			}
				
				

	default:
		
		if (isAdmin()) _goto('?act=main');
		$set->pageTitle = lang('Affiliate Administrator Login');
		$set->relatedMerchantID = $chk['mid'];
		$set->content = '
		<style type="text/css">
			body { background: #F5F5F5 !important; }
		
			.affiliate_image {
				height: 540px!important;
				
			}
			</style>
		
			<script>
			function checkUsername(){
					username = $("#username").val();
					
					var re = "/^\w+$/[\w@]";
				//if (username.match(/^[\w@].+$/) == null) {
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
					<div style="text-align: left; width: 450px; background: #FFF; border: 1px #DDD solid; font-family: Arial; padding: 20px;">
						'.lang('Welcome back admin, please log in').':<br /><br />
						'. ($set->disableAutoCompleteOnLogin? '
							<table width="100%">
							<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.($errors['err'] ? 'color: red;' : '').'">'.lang('Username').':</td><td align="right"><input type="text" name="username_new" id="username_new" value="'.$username.'" style="width: 300px;" /></td></tr>
								<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.(strlen($errors['errPass'])==1 ? 'color: red;' : '').'">'.lang('Password').':</td><td align="right"><input type="password" name="password_new" id="password_new" style="width: 300px;" /></td></tr>
								</table>':'').
						
							($lastOutCount >= $set->numberOfFailureLoginsAttempts ? '<div style="color:red;padding-bottom:10px;" >'.lang('You have exceeded the number of allowed login attempts. Please try again in an hour.').'</div>' : '') . '
						<form method="post" id="adminLoginForm">
						<input type="hidden" name="act" value="login" />
						<div style="color:red;padding-bottom:10px;" class="loginerrors"></div>
							<table width="100%">
								'. ($set->disableAutoCompleteOnLogin? '
								<input type="hidden" id="hiddenUsername" name="username"/>
								<input type="hidden" id="hiddenPassword" name="password"/>':'
								<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.($errors['err'] ? 'color: red;' : '').'">'.lang('Username').':</td><td align="right"><input type="text" name="username" id="username" value="'.$username.'" style="width: 300px;" /></td></tr>
								<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.(strlen($errors['errPass'])==1 ? 'color: red;' : '').'">'.lang('Password').':</td><td align="right"><input type="password" name="password" id="password_new" style="width: 300px;" /></td></tr>
								').'
									
								'.($set->multi ? '<tr><td></td><td align="right"><select name="lang" style="width: 312px;"><option value="">'.lang('Choose your language').'</option>'.listMulti($_COOKIE['setLang']).'</select></td></tr>' : '').'
								<tr><td></td><td align="right" style="padding-top: 10px;"><input type="submit" onClick = "return checkUsername();"value="'.lang('Login').'" /></td></tr>
							
							</table>
						</form>
					</div>
				</div>
			<div class="admin_footerpart" style="border-top: 1px #dddddd solid; padding: 25px 0 25px 0; background: #FFFFFF; margin-top: 40px;">
				<table width="989" border="0" cellpadding="0" cellspacing="0"><tr>
					<td align="left" style="font-size: 11px; color: #746d6d; font-family: Arial; text-align: justify;">
						<b>'.$set->webTitle.'</b> - The official '.$set->webTitle.' Affiliation - place you in the perfect position to claim your share of one of the most lucrative industries online. Over 3 trillion dollars is traded every day in the financial markets and '.$set->webTitle.' offers you the most respected and rewarding brands to help you convert your web traffic into an unlimited source of revenue.
					</td>
					<td width="300" align="right"><a href="http://www.affiliatets.com/" target="_blank"><img border="0" src="images/main/powered.png" alt="Powered By Afffiliate Buddies" /></a></td>
				</tr></table>
			</div>
			</div>
			'. ($set->disableAutoCompleteOnLogin? '
				<script>
				 $("#adminLoginForm").submit(function() {
					$("#hiddenUsername").val($("#username_new").val());
					$("#hiddenPassword").val($("#password_new").val());
				  });
				  $("#username_new,#password_new").keypress(function(e) {
					if (e.which == 13) {
					  $("#adminLoginForm").submit();
					}
				  });
				</script>':'');
			
		theme();
		break;
	
	case "main":
	
		$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
		if (!isAdmin()) _goto($lout);
		
		$set->noFilter = 1;
		$set->pageTitle = lang('Home Screen').' - '.lang('Dashboard');
		
		if(!$from)	$from = date("d/m/Y 00:00:00");
		if(!$to)	$to = date("d/m/Y 23:59:59");

		$from = strTodate($from);
		$to   = strTodate($to);

		$from = sanitizeDate($from);
		$to   = sanitizeDate($to);

		commonGlobalSetTimeRange($from, $to);
                
    		
		// List Merchants
		$viewsSum               = 0;
		$totalCPI               = 0;
		$clicksSum              = 0;
		$totalLeads             = 0;
		$totalDemo              = 0;
		$totalReal              = 0;
		$newFTD                 = 0;
		$ftdAmount              = 0;
		$totalLots             = 0;
		$activeTraders          = 0;
		$totalBonus             = 0;
		$totalWithdrawal        = 0;
		$mType                  = 0;
		$totalFrozens           = 0;
		
		$totalCredits           = 0;
                $strCurrentMerchantType = '';
		
			
			
			output_memory('','A1','');
                
                // List of wallets.
                $arrWallets = array();
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1;";
                $resourceWallets = function_mysql_query($sql,__FILE__);
		
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
         
		 
		$merchantqq = function_mysql_query("SELECT * FROM merchants WHERE valid = '1' ORDER BY producttype, pos",__FILE__);
		
		
		$strQuery = 'SELECT sum(credit) as credit FROM affiliates WHERE valid = 1';
                $resource = function_mysql_query($strQuery,__FILE__);
                $arrRow = mysql_fetch_assoc($resource);
                $totalCredits = $arrRow['credit'];
		
		output_memory('','A2','');
		while ($merchantww = mysql_fetch_assoc($merchantqq)) {
			$netDepositTransactions = array();
                     // Check if this is a first itaration on given wallet.
                    if ($set->multiMerchantsPerTrader==1)
					$needToSkipMerchant = $arrWallets[$merchantww['wallet_id']];
				else 
					$needToSkipMerchant= false;
				
                    
                    $showCasinoFields = 0;
                   
				   
				   output_memory('','B1','');
				   
                    $totalTraffic                = [];
                    
					$arrClicksAndImpressions     = getTotalClicksAndImpressions($from, $to, $merchantww['id']);
                    $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                    $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
					
					
					
                    if (strtolower($merchantww['producttype']) == 'casino' || strtolower($merchantww['producttype']) == 'sportsbetting') {
                        $showCasinoFields = 1;
                    }
                   
					output_memory('','B2','');
				   
                    $frozens = mysql_result(function_mysql_query('SELECT COUNT(id) FROM data_reg WHERE merchant_id="'.$merchantww['id'].'" AND status="frozen" and  rdate BETWEEN "'.$from.'" AND "'.$to.'"',__FILE__),0,0);
                    
					output_memory('','B3','');                    
					
                    if ($merchantww['producttype'] != $currentType) {
						
						
				
				$mType++;
				$currentType = $merchantww['producttype'];
				if ($mType > 1) {
                                    $listMerchants    .= '</tbody><tfoot>
                                            <tr>
                                                <th><b>'.lang('Total').':</b></th>
                                                <th align="center">'.$mviewsSum.'</th>
                                                <th align="center">'.$mclicksSum.'</th>'
                                                 . ($set->deal_cpi ?  '<th align="center"><a href="/admin/reports.php?act=install&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.$mviewCPI.'</a></th>':'')
                                                 . ($hideDemoAndLeads ? '' : '<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.number_format($mtotalLeads,0).'</a></th>
                                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.number_format($mtotalDemo,0).'</a></th>') . '
                                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.number_format($mtotalReal,0).'</a></th>
                                                ' . ($strCurrentMerchantType == 'casino' || $strCurrentMerchantType == 'sportsbetting' ? '<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=frozen">'.number_format($mtotalFrozens, 0).'</a></th>' : '') . '
                                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$mnewFTD.'</a></th>
                                                <th align="center">'.price(($mftdAmount)).'</th>
                                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=totalftd">'.($mtotalRealFtd).'</a></th>
                                                <th align="center">'.price(($mtotalRealFtdAmount)).'</th>
                                                <th align="center"><a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.($mtotal_deposits).'</a></th>
                                                <th align="center">'.price(($mtotal_depositsAmount)).'</th>
                                                <th align="center">'.price(@round(($mtotal_depositsAmount/$mnewFTD),2)).'</th>
                                                <th align="center"><a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=bonus">'.price($mtotalBonus).'</a></th>
												<th align="center"><a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=withdrawal">'.price($mtotalWithdrawal).'</a></th>
												<th align="center"><a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=chargeback">'.price($mtotalChargeback).'</a></th>
												'.($productType == 'forex' ? '
                                                <th align="center">'.price(($mtotalLots)).'</th>
												' :'').'
                                                <th align="center"><a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&netdeposit=1">'
												
                                                . price(($mtotalnetDeposit)).'</a></th>'
												
												.($set->deal_pnl==1 ? '<th align="center">'.price(($mtotalPnl)).'</th>':'')
												.'<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=activeTraders">'.($mactiveTraders).'</a></th>'.
                                                '<th align="center">'.price(($mtotalComs)).'</th>
                                            </tr>
                                        </tfoot></table>' . getPager();
                                        
                                        $mviewsSum = 0;
                                        $mTotalCPI = 0;
                                        $mclicksSum = 0;
                                        $mtotalLeads = 0;
                                        $mtotalDemo = 0;
                                        $mtotalReal = 0;
                                        $mnewFTD = 0;
                                        $mtotal_deposits = 0;
                                        $mtotal_depositsAmount = 0;
                                        $mftdAmount = 0;
                                        $mtotalBonus = 0;
                                        $mtotalLots = 0;
                                        $mtotalWithdrawal = 0;
                                        $mtotalChargeback = 0;
                                        $mtotalLots = 0;
                                        $mtotalnetDeposit = 0;
                                        $mactiveTraders = 0;
                                        $mtotalFrozens = 0;
                                        $mtotalRealFtd = 0;
                                        $mtotalRealFtdAmount = 0;
                                        $strCurrentMerchantType = '';
                                        $mtotalComs = 0;
                                        $mtotalPnl = 0;
                                }
					$productType = strtolower($merchantww['producttype']);
				
				
					output_memory('','B3','');
				
				
				
				if ($set->dashBoardMainTitle<>'')
				$listMerchants .= '<div class="space">&nbsp;</div><div class="specialTableTitle">'.$set->dashBoardMainTitle .'</div>';
				else
				$listMerchants .= '<div class="space">&nbsp;</div><div class="specialTableTitle">'.lang(typeName($merchantww['producttype'])).' '.(lang(ptitle('Brokers',ucwords($merchantww['producttype'])))).'</div>';
				
				$listMerchants .= '<table class="tablesorter mdlReportFields_'. $merchantww['producttype'] .'" width="100%" border="0" cellpadding="4" cellspacing="0">
						<thead>
							<tr>
								<th width="8%">'.lang(ptitle('Merchant')).'</th>
								<th align="center">'.lang('Impression').'</th>
								<th align="center">'.lang('Clicks').'</th>' .
								($set->deal_cpi?'<th align="center">'.lang('Installation').'</th>':'') 
								
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
								'.(strtolower($merchantww['producttype']) == 'forex' ? '
								<th align="center">'.lang(('Lots')).'</th>
								':'').'
								
								<th align="center">'.lang(ptitle('Net Deposit')).'</th>
								'
								.($set->deal_pnl==1 ? 
								'<th>'.lang(ptitle('PNL')).'</th> ' : '').
								'<th align="center">'.lang(ptitle('Active Traders')).'</th>'.
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
			$bonus=0;
			$lots=0;
			$totalPNL=0;
			$withdrawal=0;
			$chargeback=0;
			$netDeposit=0;
			$activeTrader=0;
			$real_ftd_amount=0;
			$new_real_ftd = 0;
			$totalCom=0;
			$totalCPI=0;
			
                        
					output_memory('','B4','');        
					
                        $sql = "SELECT * FROM data_reg WHERE  merchant_id='".$merchantww['id']."' AND  rdate BETWEEN '".$from."' AND '".$to."'";   
                        $regqq = function_mysql_query($sql,__FILE__);
                        
                        $arrTierCplCountCommissionParams = [];
						
						$regCom =0;
                        while ($regww = mysql_fetch_assoc($regqq)) {
							
							$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
							
                            
                            $boolTierCplCount = !is_null($strAffDealType['tier_type']) && 'cpl_count' == $strAffDealType['tier_type'];
                            
                            if ($regww['type'] == "lead") $total_leads++;
                            if ($regww['type'] == "demo") $total_demo++;
                            if ($regww['type'] == "real") {
                               
                                if (!$boolTierCplCount) {
                                    $arrTmp = [
                                        'merchant_id'  => $regww['merchant_id'],
                                        'affiliate_id' => $regww['affiliate_id'],
                                        'rdate'        => $regww['rdate'],
                                        'banner_id'    => $regww['banner_id'],
                                        'initialftddate'    => $regww['initialftddate'],
                                        'trader_id'    => $regww['trader_id'],
                                        'profile_id'   => $regww['profile_id'],
                                    ];
									
									$a = getCommission($from, $to, 0, (isset($group_id) && $group_id != '' ? $group_id : -1),  $arrDealTypeDefaults, $arrTmp);
									
									$regCom += $a;
									
                                    $totalCom += $a;
                                    unset($arrTmp);
                                    
                                } else {
                                    // TIER CPL.
                                    if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
                                        $arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
                                    } else {
                                        $arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
                                            'from'                => $from,
                                            'to'                  => $to,
                                            'onlyRevShare'        => 0,
                                            'groupId'             => -1,
                                            'arrDealTypeDefaults' => $arrDealTypeDefaults,
                                            'arrTmp'              => [
                                                'merchant_id'  => $regww['merchant_id'],
                                                'affiliate_id' => $regww['affiliate_id'],
                                                'rdate'        => $regww['rdate'],
                                                'banner_id'    => $regww['banner_id'],
                                                'trader_id'    => $regww['trader_id'],
                                                'profile_id'   => $regww['profile_id'],
                                                'amount'       => 1,
                                                'tier_type'    => 'cpl_count',
                                            ],
                                        ];
                                    }
                                }
                                
                                $total_real++;
                            }
                            unset($regww);
                        }
                       output_memory('','B5','');
					   
					   if($_GET['com'])
					   {
						   echo " Commission after Reg " . $regCom . "<br/>";
					   } 
                        // TIER CPL.
           
                        
                        
                            
                            $volume = 0;

									
					        $sql = "SELECT 
							data_reg.initialftddate,
							data_reg.affiliate_id,
							data_reg.banner_id,
							data_reg.profile_id,
							data_reg.group_id,
							data_reg.trader_id,
							data_reg.merchant_id,
							data_sales.amount,
							data_sales.tranz_id,
							data_sales.status,
							data_sales.id,
							data_sales.type as data_sales_type, data_sales.rdate as data_sales_rdate FROM  
												(select 
												amount,
												tranz_id,
												merchant_id,
												status,
												id,
												type, 
												rdate,
												trader_id
							 from data_sales where
							merchant_id = '" . $merchantww['id'] . "' and rdate BETWEEN '".$from."' AND '".$to."' and type <> 'deposit'  and type<>'PNL' ) data_sales
																INNER JOIN data_reg AS data_reg 
																		ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id "
                                    . "WHERE  data_reg.type <> 'demo'   ";
								

									
                            $salesqq=function_mysql_query($sql,__FILE__); //OR die(mysql_error());
							$salesCom = 0;
                            while ($salesww=mysql_fetch_assoc($salesqq)) {
								
								
								if ($salesww['data_sales_type'] == "chargeback" ||
								$salesww['data_sales_type'] == "withdrawal" ||
								$salesww['data_sales_type'] == "bonus" ) {
								
								$tranrow['id'] = $salesww['id'];
								$tranrow['affiliate_id'] = $salesww['affiliate_id'];
								$tranrow['tranz_id'] = $salesww['tranz_id'];
								$tranrow['trader_id'] = $salesww['trader_id'];
								$tranrow['merchant_id'] = $salesww['merchant_id'];
								$tranrow['amount'] = $salesww['amount'];
								$tranrow['rdate'] = $salesww['data_sales_rdate'];
								$tranrow['type'] = $salesww['data_sales_type'];
								$tranrow['status'] = $salesww['status'];
								$tranrow['initialftddate'] = $salesww['initialftddate'];
								$netDepositTransactions[] = array($tranrow);
								}
								
                                if ($salesww['data_sales_type'] == "bonus") $bonus += $salesww['amount'];
                                if ($salesww['data_sales_type'] == "withdrawal") $withdrawal += $salesww['amount'];
                                if ($salesww['data_sales_type'] == "chargeback") $chargeback += $salesww['amount'];
                                if ($salesww['data_sales_type'] == 'volume') {
                                    $volume += $salesww['amount'];
                                    $arrTmp = [
                                        'merchant_id'  => $salesww['merchant_id'],
                                        'affiliate_id' => $salesww['affiliate_id'],
                                        'rdate'        => $salesww['data_sales_rdate'],
                                        'initialftddate'        => $salesww['initialftddate'],
                                        'banner_id'    => $salesww['banner_id'],
                                        'trader_id'    => $salesww['trader_id'],
                                        'profile_id'   => $salesww['profile_id'],
                                        'type'       => 'volume',
										'amount' => $salesww['amount'],
                                    ];
                                    
                                    
									$a = getCommission(
                                        $salesww['data_sales_rdate'], 
                                        $salesww['data_sales_rdate'], 
                                        0, 
                                        (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                        $arrDealTypeDefaults, 
                                        $arrTmp
                                    );
									$salesCom += $a;
									$totalCom += $a;
                                }
                            }
							
							if($_GET['com'])
						   {
							   echo " Commission after Sales " . $salesCom . "<br/>";
						   } 
                       output_memory('','B6','');
						   
						   
                            $depositsqq = function_mysql_query("
                            SELECT distinct(data_sales.tranz_id),data_sales.* , data_reg.initialftddate
                            FROM data_sales  AS data_sales 
                            INNER JOIN data_reg AS data_reg ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  
                            where data_sales.merchant_id = '" . ($merchantww['id']). "' and  
                             data_sales.rdate BETWEEN '".$from."' AND '".$to."' AND data_sales.type='deposit'",__FILE__);

                            while ($depositww = mysql_fetch_assoc($depositsqq)) {
								
								$tranrow['id'] = $depositww['id'];
								$tranrow['affiliate_id'] = $depositww['affiliate_id'];
								$tranrow['tranz_id'] = $depositww['tranz_id'];
								$tranrow['trader_id'] = $depositww['trader_id'];
								$tranrow['merchant_id'] = $depositww['merchant_id'];
								$tranrow['rdate'] = $depositww['rdate'];
								$tranrow['amount'] = $depositww['amount'];
								$tranrow['type'] = 'deposit';
								$tranrow['status'] = $depositww['status'];
								$tranrow['initialftddate'] = $depositww['initialftddate'];
								$netDepositTransactions[] = array($tranrow);
								
								
                                $depositsAmount += $depositww['amount'];
                                $totalDeposits++;
                            }
                          output_memory('','B7','');                         
                            
                            $ftdUsers = '';
                            $arrFtds  = getTotalFtds($from, $to, 0, $merchantww['id'], $merchantww['wallet_id']);
							
                       output_memory('','B8','');
							// echo ('--: ' . $from . '   |   '   .  $to. '   |   '   .   "0". '   |   '   .   $merchantww['id']. '   |   '   .   $merchantww['wallet_id']. '   |   '   .   ($InitManagerID==0? -1 : $InitManagerID).'<br>');
							
                        
                        if (!$needToSkipMerchant) {
							$ftdCom = 0;
							foreach ($arrFtds as $arrFtd) {
                                $new_real_ftd++;
                                $real_ftd_amount += $arrFtd['amount'];
                                $beforeNewFTD = $new_ftd;
                                getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $new_ftd);
                                if ($beforeNewFTD != $new_ftd){// || count($arrFtds)==1) {
                             
                                }
                            }
							
							
                       output_memory('','B8','');
							//******* qualification ftds
							   $ftdUsersQualified = '';
							   $selected_group_id = ($gorup_id<>"")? $group_id : -1;
							   $FILTERbyTrader = !empty($trader_id)? $trader_id : 0;
							
							 $qftdQuery  = "SELECT * FROM `data_reg` where type<>'demo' and FTDqualificationDate>'0000-00-00 00:00:00' and FTDqualificationDate>'". $from ." 00:00:00' and FTDqualificationDate <'". $to ."'  and merchant_id = ". $merchantww['id']  
						 .(!empty($selected_group_id) && $selected_group_id>0 ? ' and group_id= '. $selected_group_id : '')  
						 .(!empty($FILTERbyTrader) ? ' and trader_id= '. $FILTERbyTrader : '') ;
						 $qftdQQ = function_mysql_query($qftdQuery,__FILE__);
							
                        if (!$needToSkipMerchant) {
							$ftdCom = 0;
								while ($arrFtd = mysql_fetch_assoc($qftdQQ)) {
                              
								$arrFtd['initialftddate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['rdate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['runningType'] = 'qualification';
                                
									$activeTrader++;  
                                    $arrFtd['isFTD'] = true;
									$a = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, -1, $arrDealTypeDefaults, $arrFtd);
									$totalCom += $a;
									$qftdCom += $a;
                                
                            }
							
							}
                                    
                        
                       output_memory('','B9','');         
							if($_GET['com'])
						   {
							   echo " Commission after qualified " . $qftdCom . "<br/>";
						    } 
							
                        }
                        
						
						
						
						
                        $revCom = 0;
	                       output_memory('','B10','');
							
							if (!empty($netDepositTransactions))
							foreach($netDepositTransactions as $trans){
									
									
									
									 	$revDepAmount = 0;
							$revBonAmount = 0;
							$revWithAmount = 0;
							$revChBAmount = 0;
							
							$amount = $trans[0]['amount'];
							
						if (floatval($amount<>0)  && !empty($trans[0]['rdate'])) {
		
								if ($trans[0]['type']=='deposit')
									$revDepAmount = $amount;
								if ($trans[0]['type']=='bonus')
									$revBonAmount = $amount;
								if ($trans[0]['type']=='withdrawal')
									$revWithAmount = $amount;
								if ($trans[0]['type']=='chargeback')
									$revChBAmount = $amount;
								
									$intnetDeposit =  round(getRevenue($searchInSql,$merchantww['producttype'],$revDepAmount,$revBonAmount,$revWithAmount,0,0,0,$merchantww['rev_formula'],null,$revChBAmount),2);
									
									$netDeposit = $intnetDeposit;
									
						
								
									
											$comrow                 = array();
										   $comrow['merchant_id']  = $trans[0]['merchant_id'];
										   $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
											$comrow['banner_id']    = 0;
											$comrow['rdate']        = $trans[0]['rdate'];//arrRange2['from'];
											$comrow['amount']       = $intnetDeposit;
											 //$comrow['trader_id']  =  $trans[0]['trader_id'];
											$comrow['isFTD']        = false;
											$comrow['initialftddate']        = $trans[0]['initialftddate'];

												
														$com = getCommission($trans[0]['rdate'], $trans[0]['rdate'], 1, -1, $arrDealTypeDefaults, $comrow);
														if ($_GET['debug']==1){
															echo 'com : ' .$com . '         --  date:    ' . $trans[0]['rdate'].'<br>';
														}
														$revCom +=$com;
														$totalCom           += $com;
									
									}
									}
									
                      output_memory('','B11','');	
					$netDeposit =  round(getRevenue(" data_sales.rdate BETWEEN '".$from."' AND '".$to."' ",$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);								
                      output_memory('','B12','');	
							
						
						
						
						if($_GET['com'])
					   {
						   echo " Commission Rev " . $revCom . "<br/>";
					   } 
							
							
							$lotCom = 0;
							output_memory('Before Forex','C1','');
							if (strtolower($merchantww['productType'])=='forex') {
									//lots 
									$sql = 'SELECT dr.initialftddate, ds.turnover AS totalTurnOver,ds.trader_id,ds.merchant_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds  
									inner join data_reg dr inner join ds.merchant_id = dr.merchant_id and ds.trader_id = dr.merchant_id ' 
										 . 'WHERE ds.merchant_id = "' . $merchantww['id'] . '" AND ds.rdate BETWEEN "' . $from . '" AND "' . $to . '" '
										 ;
											
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $lots= 0;
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											
											if($ts['affiliate_id']==null) {
													continue;
											}
				
													$totalLots  = $ts['totalTurnOver'];
														$tradersProccessedForLots[$merchantww['id'] . '-' . $ts['trader_id']] = $merchantww['id'] . '-' . $ts['trader_id'];
														$lotdate = $ts['rdate'];
														$ex = explode(' ' , $lotdate);
														$lotdate = $ex[0];
														$row = [
																	'merchant_id'  => $merchantww['id'],
																	'affiliate_id' => $ts['affiliate_id'],
																	'rdate'        => $lotdate,
																	'banner_id'    => $ts['banner_id'],
																	'trader_id'    => $ts['trader_id'],
																	'initialftddate'    => $ts['initialftddate'],
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
												
													$a = getCommission($lotdate, $lotdate, 0, $group_id, $arrDealTypeDefaults, $row);
													$lotCom += $a;
													$totalCom += $a;
													$lots += $totalLots; 
													
											
										}
								
										}
output_memory('After Forex','C2','');
										if($_GET['com'])
									   {
										   echo " Commission LOTS " . $lotCom . "<br/>";
									   } 
										
								
								$pnlCom = 0;
								

								if ($set->deal_pnl == 1) {
						
								$totalPNL  = 0;
								$dealsForAffiliate['pnl'] = 1;
								
									
									$pnlRecordArray=array();
									
										$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
										$pnlRecordArray['merchant_id']  = $merchantww['id'];
										$pnlRecordArray['trader_id']  = (isset($trader_id) ? $trader_id: "");
										$pnlRecordArray['banner_id']  = $banner_id;
										$pnlRecordArray['profile_id']  = $profile_id;
										$pnlRecordArray['group_id']  = $group_id;
										$pnlRecordArray['searchInSql']  = $searchInSql;
										$pnlRecordArray['fromdate']  = $from;
										$pnlRecordArray['todate']  = $to;
									
									
									
									
									if ($dealsForAffiliate['pnl']>0){
										$sql = generatePNLquery($pnlRecordArray,false);
									}
									else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
										$sql = generatePNLquery($pnlRecordArray,true);
									}
									
								output_memory('PNL','C3','');
								$traderStatsQ = function_mysql_query($sql,__FILE__);
								while($ts = mysql_fetch_assoc($traderStatsQ)){
												$pnlamount = ($ts['amount']*-1);
												$row = [
													'merchant_id'  => $ts['merchant_id'],
													'affiliate_id' => $ts['affiliate_id'],
													'rdate'        => $ts['rdate'],
													'banner_id'    => $ts['banner_id'],
													'trader_id'    => $ts['trader_id'],
													'initialftddate'    => $ts['initialftddate'],
													'profile_id'   => $ts['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  ($showCasinoFields==1 ?  calculateCasinoRevenue($pnlamount,$ts['type']) : $pnlamount) ,
												 'initialftddate'=>$ts['initialftddate']
												 ];
												 
											
												 $totalPNL = $totalPNL + $pnlamount;

										if ($dealsForAffiliate['pnl']>0){
											
											$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $group_id, $arrDealTypeDefaults, $row);
												$pnlCom += $tmpCom;
												$totalCom += $tmpCom;
										}
								}
		output_memory('Aftr PNL','C4','');
						}
						
		
				if($_GET['com'])
			   {
				   echo " Commission after PNL " . $pnlCom . "<br/>";
			   } 
			   
						


if ($set->deal_cpi==1){
			   output_memory('Before install','D0','');
	
	   // installation
						$array = array();			
						$array['from']  	= 	$from ;
						$array['to'] = $to;
						$array['merchant_id'] = $merchantww['id'] ;
						$array['type'] = 'install' ;
						$array['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
						$array['searchInSql']  = $searchInSql;
						$array['group_id'] = $group_id ;
	
						$installs = generateInstallations($array);
						if (!empty($installs)){
						$totalCPI  = 0;
						foreach ($installs as $install_item){
						
								$totalCPI++;
				
				
								$a= getCommission($install_item['rdate'], $install_item['rdate'], 0, -1, $arrDealTypeDefaults, $install_item);
                              
									$cpiCom += $a;
									
								       	 if ($_GET['ddd']==1) {
										 echo '<br><br>';
										 var_dump($a);
										 
										 echo '<br><br>';
										 echo '<br><br>';
										 var_dump($install_item);
										 echo '<br><br>';
											echo '00: ' . $a . '<br>';
											 echo '$totalCom: ' . $totalCom. '<br>';
										}
									$totalCom +=$a;
								 
									// unset($arrTmp);
									

							unset($a);
						}
						}
						// end of install
	   
	}

		
			   
			   output_memory('Before Sub','D1','');
			   //Sub Affiliate Commission
						$subcomm= 0;
						$qry = "select id from affiliates where id in ( select distinct (id) as id from affiliates where refer_id>0) and valid = 1 and sub_com>0";
						$rsc = function_mysql_query($qry,__FILE__);
							
						$allAffiliates = "";
						
						while ($row = mysql_fetch_assoc($rsc)) {
								
								 $hasResults = false;
								 if ($row['id']>0)  {
   										$affiliateww = getAffiliateRow($row['id']);
											$comData = getSubAffiliateData($from,$to,$affiliateww['id'],$affiliateww['refer_id'],'commission','admin');
											$subcomm += $comData['commission'];
										}
						}
						
						$totalCom+= $subcomm;
						if($_GET['com'])	{
									echo " Commission after sub com " . $subcomm . "<br/>";
							} 
						
			output_memory('After Sub','D2','');
		
			$listMerchants .= '<tr>
                            <td style="color: #646464;" id="'.($merchantww['name']). '"><b>'.lang($merchantww['name']).'</b></td>
                            <td align="center"><a href="/admin/reports.php?act=clicks&type=views&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($totalTraffic['totalViews'] ? $totalTraffic['totalViews'] : '0').'</a></td>
                            <td align="center"><a href="/admin/reports.php?act=clicks&type=clicks&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($totalTraffic['totalClicks'] ? $totalTraffic['totalClicks'] : '0').'</a></td>
                            ' . ($set->deal_cpi?'<td align="center"><a href="/admin/reports.php?act=install&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($totalCPI ? $totalCPI : '0').'</a></td>':'').'
                            ' . ($hideDemoAndLeads ? '' : '<td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=lead">'.number_format($total_leads,0).'</a></td>
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=demo">'.number_format($total_demo,0).'</a></td>') . '
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=real">'.number_format($total_real,0).'</a></td>
                            '.($showCasinoFields ? '<td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=frozen">'.$frozens.'</a></td>' : '').'
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=ftd">'.$new_ftd.'</a></td>
                            <td align="center">'.price($ftd_amount['amount']).'</td>
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=totalftd">'.($new_real_ftd).'</a></td>
                            <td align="center">'.price(($real_ftd_amount)).'</td>
                            <td align="center"><a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=deposit">'.($totalDeposits).'</a></td>
                            <td align="center">'.price(($depositsAmount)).'</td>
                            <td align="center">'.price(@round(($depositsAmount/$new_ftd),2)).'</td>
                            <td align="center">'.price(($bonus)).'</td>
                            <td align="center">'.price(($withdrawal)).'</td>
                            <td align="center">'.price(($chargeback)).'</td>
                            '.(strtolower($merchantww['producttype']) == 'forex' ?
							'<td align="center">'.price($lots).'</td>' : ''							
							).'
                            <td align="center"><a href="/admin/reports.php?act=stats&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.price(($netDeposit)).'</a></td>'
							.($set->deal_pnl==1 ? '
							<td align="center">'.price($totalPNL).'</td> ' : '').
							'<td align="center">'.($activeTrader).'</td>'.
                            '<td align="center">'.price(($totalCom)).'</td>
                        </tr>';
                        
                        $mviewsSum+=$totalTraffic['totalViews'];
                        $mTotalCPI+=$totalCPI;
			$mclicksSum+=$totalTraffic['totalClicks'];
			$mtotalLeads+=$total_leads;
			$mtotalDemo+=$total_demo;
			$mtotalReal+=$total_real;
			$mnewFTD+=$new_ftd;
			$mtotal_deposits+=$totalDeposits;
			$mtotal_depositsAmount+=$depositsAmount;
			$mftdAmount+=$ftd_amount['amount'];
			$mtotalBonus+=$bonus;
			$mtotalWithdrawal+=$withdrawal;
			$mtotalChargeback+=$chargeback;
			$mtotalLots+=$lots;
			$mtotalPnl+=$totalPNL;
			$mtotalnetDeposit+=$netDeposit;
			$mactiveTraders+=$activeTrader;
			$mtotalComs+=$totalCom;
			$mtotalFrozens+=$frozens;
			$mtotalRealFtd+=$new_real_ftd;
			$mtotalRealFtdAmount+=$real_ftd_amount;
                        $strCurrentMerchantType = strtolower($merchantww['producttype']);
                        
			$viewsSum+=$totalTraffic['totalViews'];
			$viewCPI+=0;
			$clicksSum+=$totalTraffic['totalClicks'];
			$totalLeads+=$total_leads;
			$totalDemo+=$total_demo;
			$totalReal+=$total_real;
			$newFTD+=$new_ftd;
			$total_deposits+=$totalDeposits;
			$total_depositsAmount+=$depositsAmount;
			$ftdAmount+=$ftd_amount['amount'];
			$totalBonus+=$bonus;
			$activeTraders+=$activeTrader;
			$totalWithdrawal+=$withdrawal;
			$totalChargeback+=$mtotalChargeback;
			$totalLots+=$mtotalLots;
			$totalTotalPnls+=$mtotalPnl;


			$totalnetDeposit+=$netDeposit;
			$totalComs+=$totalCom;
			$totalFrozens+=$frozens;
			$totalRealFtd+=$new_real_ftd;
			$totalRealFtdAmount+=$real_ftd_amount;
                        
                    // Mark given wallet as processed.
                    $arrWallets[$merchantww['wallet_id']] = true;
					
					
			$productType = strtolower($merchantww['producttype']);
			
		}
		// List Merchants
		
			if ($set->hidePendingProcessHighAmountDeposit ==0  && adminPermissionCheck('pendingdeposit')) {
			//pending deposits
		$sql = "select count(dsp.id) as totalPendingDeposits,sum(dsp.amount)  as sumPendingDeposits from data_sales_pending dsp inner join data_reg dr on dsp.trader_id = dr.trader_id where lower(dr.status)<>'demo' and dsp.rdate between '". $from . "' and '" . $to . "' ";

		$pdeposits = @mysql_fetch_assoc(function_mysql_query($sql,__FILE__) );
		$pendingDeposits = 0;
		$sumPendingDeposits = 0;
		if(!empty($pdeposits)){
			$pendingDeposits = $pdeposits['totalPendingDeposits'];
			$sumPendingDeposits = $pdeposits['sumPendingDeposits'];
		}
			}
                
		$set->rightBar = '<form action="'.$set->basepage.'" method="get">
						<input type="hidden" name="act" value="main" />
						<table><tr>
							<td>'.timeFrame($from,$to).'</td>
							<td><input class="search" type="submit" value="'.lang('View').'" /></td>
						</tr></table>
						</form>';
		
		$boxaName = "admin-index-dashboard-1";
		
		$tableArr = Array(
				
			(object) array(
			  'id' => 'impressions',
			  'str' => '<td class="dashStat '.lang('Impressions').'">
							'.lang('Impressions').'<br />
							<span style="font-size: 18px; font-weight: bold;"><a href="/admin/reports.php?act=clicks&type=views&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($viewsSum ? number_format($viewsSum) : '0').'</a></span>
						</td>'
			),
			
			(object) array(
			  'id' => 'clicks',
			  'str' => '<td class="dashStat '.lang('Clicks').'">
							'.lang('Clicks').'<br />
							<span style="font-size: 18px; font-weight: bold;"><a href="/admin/reports.php?act=clicks&type=clicks&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($clicksSum ? number_format($clicksSum) : '0').'</a></span>
						</td>'
			)
			);
			if($set->deal_cpi){
			array_push($tableArr,
				
				(object) array(
				  'id' => 'cpi',
				  'str' => '<td class="dashStat '.lang('Install').'">
								'.lang('Install').'<br />								
								<span style="font-size: 18px; font-weight: bold;"><a href="/admin/reports.php?act=install&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($mTotalCPI ? number_format($mTotalCPI) : '0').'</a></span>
							</td>'
				)
			);
			}
			
	
                
                if (!$hideDemoAndLeads) {
                    array_push($tableArr,
                        (object) array(
                            'id'  => 'leads',
                            'str' => '<td class="dashStat  '.lang(ptitle('Leads')).'">
                                         '.lang(ptitle('Leads')).'<br />
                                         <a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to))
                                         .'&type=lead" style="font-size: 18px; font-weight: bold;">'.number_format($totalLeads+$marketTotal_leads).'</a>
                                     </td>',
                        ),
                        
                        (object) array(
                          'id'  => 'demo',
                          'str' => '<td class="dashStat '.lang(ptitle('Demo')).'">
                                        '.lang(ptitle('Demo')).'<br />
                                        <a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to))
                                        .'&type=demo" style="font-size: 18px; font-weight: bold;">'.number_format($totalDemo+$marketTotal_demo).'</a>
                                    </td>',
                        )
                    );
                }
		
		if($showCasinoFields){
			array_push($tableArr,(object) array(
				  'id' => 'frozens',
				  'str' => '<td class="dashStat '.lang('Frozens').'">
								'.lang('Frozens').'<br />
								<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=frozen" style="font-size: 18px; font-weight: bold;"><span style="font-size: 18px; font-weight: bold;">'.($totalFrozens).'</span></a>
							</td>',
				));
		}
 
 array_push($tableArr,
				
				(object) array(
				   'id' => 'realAccount',
			  'str' => '<td class="dashStat '.lang(ptitle('Real Account')).'">
							'.lang(ptitle('Real Account')).'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real" style="font-size: 18px; font-weight: bold;">'.number_format($totalReal+$marketTotal_real).'</a>
						</td>'
				)
			);
			array_push($tableArr,
			
			(object) array(
			  'id' => 'ctr',
			  'str' => '<td class="dashStat '.lang('CTR').'">
							'.lang('CTR').'<br />
							<span style="font-size: 17px; font-weight: bold;">'.($viewsSum && $clicksSum ? @number_format((($clicksSum)/$viewsSum)*100,2).' %' : '-').'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'epc',
			  'str' => '<td class="dashStat '.lang('EPC').'" title="Total Commision / total clicks">
							'.lang('EPC').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.($totalComs && $clicksSum ? price((($totalComs)/$clicksSum),2) : '-').'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'ftd',
			  'str' => '<td class="dashStat '.lang('FTD').'">
							'.lang('FTD').'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd" style="font-size: 18px; font-weight: bold;">'.number_format($newFTD+$marketTotal_FTDs,0).'</a>
						</td>'
			),
			
			(object) array(
			  'id' => 'ftdAmount',
			  'str' => '<td class="dashStat '.lang('FTD Amount').'">
							'.lang('FTD Amount').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($ftdAmount+$marketTotal_FTDAmount),2).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'realFtd',
			  'str' => '<td class="dashStat '.lang('RAW FTD').'">
							'.lang('RAW FTD').'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=totalftd" style="font-size: 18px; font-weight: bold;">'.number_format($totalRealFtd,0).'</a>
						</td>'
			),
			
			(object) array(
			  'id' => 'realFtdAmount',
			  'str' => '<td class="dashStat '.lang('RAW FTD Amount').'">
							'.lang('RAW FTD Amount').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalRealFtdAmount)).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'deposits',
			  'str' => '<td class="dashStat '.lang('Deposits').'">
							'.lang('Deposits').'<br />
							<a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit"><span style="font-size: 18px; font-weight: bold;">'.number_format($total_deposits,0).'</span></a>
						</td>'
			),
			
			(object) array(
			  'id' => 'depositAmount',
			  'str' => '<td class="dashStat '.lang('Deposits Amount').'">
							'.lang('Deposits Amount').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($total_depositsAmount+$marketTotal_depositAmount),2).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'bonus',
			  'str' => '<td class="dashStat '.lang('Bonus').'">
							'.lang('Bonus').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalBonus+$marketTotal_Bonus),2).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'Withdrawal',
			  'str' => '<td class="dashStat '.lang('Withdrawal').'">
							'.lang('Withdrawal').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalWithdrawal+$marketTotal_withdrawal),2).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'ChargeBack',
			  'str' => '<td class="dashStat '.lang('ChargeBack').'">
							'.lang('ChargeBack').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalChargeback),2).'</span>
						</td>'
			),
			
			
			
			
			
			(object) array(
			  'id' => 'netDeposit',
			  'str' => '<td class="dashStat '.lang(ptitle('Net Deposit')).'">
							'.lang(ptitle('Net Deposit')).'<br />
							<a href="/admin/reports.php?act=transactions&netdeposit=1&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'"><span style="font-size: 18px; font-weight: bold;">'.
							price(($totalnetDeposit),2).'</span></a>
						</td>'
			));
			if($set->deal_pnl){
			array_push($tableArr,(object) array(
			  'id' => 'Pnls',
			  'str' => '<td class="dashStat '.lang(ptitle('PNL')).'">
							'.lang(ptitle('PNL')).'<br />
							<a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'"><span style="font-size: 18px; font-weight: bold;">'.
							price(($totalTotalPnls),2).'</span></a>
						</td>'
			));
			}
			
			array_push($tableArr,
			(object) array(
			  'id' => 'activeTrader',
			  'str' => '<td class="dashStat '.lang('Active Trader').'">
							'.lang(ptitle('Active Trader')).'<br />
							<a href="/admin/reports.php?act=trader&type=activeTrader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'"><span style="font-size: 18px; font-weight: bold;">'.(($activeTraders)).'</span></a>
						</td>'
			),
			
			
			(object) array(
			  'id' => 'Commission',
			  'str' => '<td class="dashStat '.lang('Commission').'">
							'.lang('Commission').'<br />
							<a href="/admin/reports.php?act=commission&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'"><span style="font-size: 18px; font-weight: bold;">'.price(($totalComs),2).'</span></a>
						</td>'
			));
			
			
			($set->hidePendingProcessHighAmountDeposit==0 && adminPermissionCheck('pendingdeposit') ? 
			array_push($tableArr,(object) array(
			  'id' => 'PendingDeposits',
			  'str' => '<td class="dashStat '.lang('Pending Deposits').'">
							'.lang('Pending Deposits').'<br />
							<a href="/admin/pendingDepositsApproval.php?main=1&status=pending&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'"><span style="font-size: 18px; font-weight: bold;">'.$pendingDeposits.'</span></a>
						</td>'			
			),
			(object) array(
			  'id' => 'sumPendingDeposits',
			  'str' => '<td class="dashStat '.lang('Pending Deposits Amount').'">
							'.lang('Pending Deposits Amount').'<br />
							<a href="/admin/pendingDepositsApproval.php?main=1&status=pending&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'"><span style="font-size: 18px; font-weight: bold;">'.price($sumPendingDeposits,2).'</span></a>
						</td>'			
			)):"")
			;

	if(strtolower($merchantww['producttype']) == 'forex'){
			array_push($tableArr,
				(object) array(
			  'id' => 'Lots',
			  'str' => '<td class="dashStat">
							'.lang('Lots').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalLots),2).'</span>
						</td>'
			));
			
			
		}
	
		$set->content .='  <link rel="stylesheet" href="'.$set->SSLprefix.'js/unslider/dist/css/unslider.css">
			<!--link rel="stylesheet" href="'.$set->SSLprefix.'js/unslider/dist/css/unslider-dots.css"-->
			<script src="'.$set->SSLprefix.'js/unslider/dist/js/unslider.js"></script>';
		//$set->content .= ($set->ShowGraphOnDashBoards==1 ||  $set->ShowGraphOnDashBoards== null  ? chart('0') : ""  ).'
		$set->content .= ($set->ShowGraphOnDashBoards==1  ? 
		'		
		<div class="my-slider" style="display:none">
			<div style="float:right;margin-right:10px;margin-top:5px;position:absolute;left:95%;z-index:99999;" class="refresh1"><a class="refresha tooltip" onclick="refreshChart2();" style="font-size: 11px; cursor: pointer;display:none" >'. ($set->chartTheme=='dark_unica'?'<img src='.$set->SSLprefix.'\'images/refresh_white.png\' width=20 />':'<img src=\''.$set->SSLprefix.'images/refresh_black.png\' width=30 />') . '</a></div>
			<ul>
				<li data-slide="0" data-name="Performance Chart" class="unslider-active">  '.highchart('0','admin',$set->userInfo['id']) .'</li>
				<li data-slide="1" data-name="Conversion Chart">'. conversionHighchart('0','admin',$set->userInfo['id']).'</li>
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
' :'') .'
		<div class="normalTableTitle" >'.lang('Merchants Performance').'<span class="imgGear" style="float:right;"><img class="imgReportFieldsSettings_dashstat" style="padding-top:6px;width:55%;cursor:pointer;" src="'.(!empty($set->SSLprefix) ? $set->SSLprefix : '../').'images/settings.png"/></span></div>	
		
			<div class="dashStat mainDashStat">
			<table width="100%" border="0" cellpadding="4" cellspacing="5" class="dashStatFields"><tr>
				'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '</tr><tr>',1,$hideDemoAndLeads).'
			</tr></table>
			</div>
		
			';
		
		
		
		
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
		$set->sortTable = 1;
		$set->noFilter = 1;
         
		 
		if (mysql_num_rows($merchantqq) > 1) {
		
                        $set->content .= $listMerchants . '</tbody><tfoot>
                            <tr>
                                <th><b>'.lang('Total').':</b></th>
                                <th align="center">'.$mviewsSum.'</th>
                                <th align="center">'.$mclicksSum.'</th>'.
                                ($set->deal_cpi?'<th align="center">'.$mTotalCPI.'</th>':'').'
                                ' . ($hideDemoAndLeads ? '' : '<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.number_format($mtotalLeads,0).'</a></th>
                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.number_format($mtotalDemo,0).'</a></th>') . '
                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.number_format($mtotalReal,0).'</a></th>
                                ' . ($showCasinoFields ? '<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=frozen">'.number_format($mtotalFrozens, 0).'</a></th>' : '') . '
                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.($mnewFTD).'</a></th>
                                <th align="center">'.price(($mftdAmount)).'</th>
                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=totalftd">'.($mtotalRealFtd).'</a></th>
                                <th align="center">'.price(($mtotalRealFtdAmount)).'</th>
                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.($mtotal_deposits).'</a></th>
                                <th align="center">'.price(($mtotal_depositsAmount)).'</th>
                                <th align="center">'.price(@round(($mtotal_depositsAmount/$mnewFTD),2)).'</th>
                                <th align="center">'.price(($mtotalBonus)).'</th>
                                <th align="center">'.price(($mtotalWithdrawal)).'</th>
                                <th align="center">'.price(($mtotalChargeback)).'</th>
                                '.($productType == 'forex' ? '
								<th align="center">'.price($mtotalLots).'</th>
								' : '' ).'
                                <th align="center"><a href="/admin/reports.php?act=stats&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'
                                . price(($mtotalnetDeposit)).'</a></th>'
								.($set->deal_pnl==1 ? '<th align="center">'.price(($mtotalPnl)).'</th>':'').
                                '<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=activeTrader">'.($mactiveTraders).'</a></th>'.
                                '<th align="center">'.price(($mtotalComs)).'</th>
                            </tr>
                        </tfoot></table>' . getPager();
						
						
                }
		
		//modal
			$fields = getReportsHiddenCols("dashStatCols","admin",$set->userInfo['id']);
			if($fields){
				$set->DashboardDashStatHiddenCols = $fields;
			}
		$set->content .= '<div class="space">&nbsp;</div>';
		$qq = function_mysql_query("SELECT * FROM affiliates WHERE rdate >= '" . date("Y-m-d") . " 00:00:00' AND rdate <= '" . date("Y-m-d") . " 23:59:59' and valid!='-1' ORDER BY id DESC",__FILE__);
		
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$listAffiliates .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>';
							if($ww['valid']==1):
								$listAffiliates .= '<td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="/?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'">'.lang('Login').'</a></td>';
							else:
								$listAffiliates .= '<td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'">'.lang('Edit').'</a> | <div class="tooltip"><a href="javascript:void(0);">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please activate the account to login.") .'</span></div></td>';
							endif;
							$listAffiliates .='<td align="left">'.$ww['username'].'</td>
							<td>'.$ww['first_name'].'</td>
						'.($set-ShowIMUserOnAffiliatesList==1 ? '<td>'.$ww['IMUser'].'</td>' : '') .'
							<td>'.getCountry($ww['country'],1).'</td>
							<td>'.listGroups($ww['group_id'],1).'</td>
							<td><img border="0" src="/admin/images/logged_'.$ww['logged'].'.png" alt="'.dbDate($ww['lastactive']).'" title="'.dbDate($ww['lastactive']).'" /></td>
							<!--<td><img border="0" src="images/'.($ww['valid'] ? 'v.png" alt="'.lang('Approved').'" title="'.lang('Approved').'" />' : 'x.png" alt="'.lang('Pending').'..." title="'.lang('Pending').'..." />').'</td>-->
							<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
						</tr>';
			}
		
		$l=0;
		

		$qry = ("SELECT * FROM traffic ORDER BY rdate DESC LIMIT 10");
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
		
			$country='';
			$countryArry = getIPCountry($ww['ip']);
			if ($countryArry['countryLONG']=='')
				$country = lang('Unknown');
			else
				$country = $countryArry['countryLONG'];
			
			$l++;
			
			$affiliateName = getAffiliateRow($ww['affiliate_id'],1);
			$brokerName = getMerchants($ww['merchant_id']);
			
			$brandname = !empty($brokerName[0]['name']) ? $brokerName[0]['name'] : $brokerName['name'];
			
			
			$lastVisits .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td><a href="/admin/affiliates.php?act=new&id='.$affiliateName['id'].'">'.$affiliateName['username'].'</a></td>
							<td>'.$countryArry['countryLONG'].'</td>
							<td>'.date("d/m/Y H:i",strtotime($ww['rdate'])).'</td>
							<td>'.$brandname.'</td>
							<td>'.(empty($ww['refer_url']) ? '' : '<a href="/out.php?refe='.urlencode($ww['refer_url']).'" title="'.urldecode($ww['refer_url']).'" target="_blank">'.lang('Click Here').'</a>').'</td>
							<!--td>'.$ww['visits'].'</td-->
						</tr>';
			}

			//products
		if($set->showProductsPlace == 1){
	
		
		$qry = "select *,0 as wallet_id from products_items where valid!=-1";
		$rsc = mysql_query($qry);
		$totalComs=0;
		while ($ww = mysql_fetch_assoc($rsc)) {
	
                            
					$arrDealTypeDefaults = getProductsDealTypeDefaults($ww['id']);
					$fromDate = $from;
                    $toDate   = $to;
                    
                    $l++;
                    $ftdUsers = '';
                    $netDeposit = 0;
                    $totalCom=0;
					
                    $ftd=0;
                    $totalLeads = 0;
                    $totalDemo = 0;
                    $totalReal = 0;
                    $ftd_amount['amount']=0;
                    $real_ftd = 0;
                    $real_ftd_amount = 0;
                    $bonus = 0;
                    $withdrawal = 0;
                    $chargeback = 0;
                    $depositingAccounts = 0;
                    $sumDeposits = 0;
                    $totalLots = 0;
                    $totalPNLs = 0;
                    $volume = 0;
                    
						{
                            $searchInSql = " BETWEEN '" . $from . "' AND '" . $to . "' ";
                            
                            $formula  = $ww['rev_formula'];
                            $fromDate = $from;
                            $toDate   = $to;
                            
                            $ftdUsers = '';
                            $netDeposit = 0;
                            $totalCom=0;
                            $ftd=0;
                            $totalLeads = 0;
                            $totalDemo = 0;
                            $totalReal = 0;
							 $totalInstallation = 0;
                            $ftd_amount['amount']=0;
                            $real_ftd = 0;
                            $real_ftd_amount = 0;
                            $bonus = 0;
                            $lots = 0;
                            $totalPNL = 0;
                            $withdrawal = 0;
                            $chargeback = 0;
                            $depositingAccounts = 0;
                            $sumDeposits = 0;
                            $volume = 0;
							$depositsAmount=0;
							$totalComs=0;
                            $merchantName = strtolower($ww['name']);
                            
                            
                            
                            $totalTraffic = [];
                            $arrClicksAndImpressions = getClicksAndImpressions(
                                $from, 
                                $to, 
                                $ww['id'], 
                                null, 
                                (isset($group_id) && $group_id != '' ? $group_id : null)
								,null,  null, null, null,true)
                            ;
							
							  if ($_GET['ddd']==1) {
								  
								  
							  }
                            
                            $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                            
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE product_id = '" . $ww['id'] . "' AND "
                                    . " rdate " . $searchInSql 
                                    . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                            // die ($sql);
                            $regqq = function_mysql_query($sql,__FILE__);
                            
                            
                            while ($regww = mysql_fetch_assoc($regqq)) {
								
								
                                
                                
                                    if ($regww['type'] == "lead") {
										$totalLeads++;
										
										
							     $arrTmp = [
                                                'product_id'  => $regww['product_id'],
                                                'affiliate_id' => $regww['affiliate_id'],
                                                'rdate'        => $regww['rdate'],
                                                'banner_id'    => $regww['banner_id'],
                                                'trader_id'    => $regww['trader_id'],
                                                'profile_id'   => $regww['profile_id'],
                                                'dealtype'   => 'cpllead',
                                            ];
                                            
											
											$a = getProductCommission(
											
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
                                            
											$totalCom +=$a;
											
											
										
									}
                                    if ($regww['type'] == "real") {
                                        
                                            $arrTmp = [
                                                'product_id'  => $regww['product_id'],
                                                'affiliate_id' => $regww['affiliate_id'],
                                                'rdate'        => $regww['rdate'],
                                                'banner_id'    => $regww['banner_id'],
                                                'trader_id'    => $regww['trader_id'],
                                                'profile_id'   => $regww['profile_id'],
                                                'dealtype'   => 'cplaccount',
                                            ];
                                            
									
                                            $a= getProductCommission(
											
											
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
											
											
											$totalCom +=$a;
                                            unset($arrTmp);
                                        $totalReal++;
                                            
                                    }
									
									if ($regww['type'] == "installation") {
                                    
                                            $arrTmp = [
                                                'product_id'  => $regww['product_id'],
                                                'affiliate_id' => $regww['affiliate_id'],
                                                'rdate'        => $regww['rdate'],
                                                'banner_id'    => $regww['banner_id'],
                                                'trader_id'    => $regww['trader_id'],
                                                'profile_id'   => $regww['profile_id'],
                                                'dealtype'   => 'cpi',
                                            ];
                                            
									
                                            $a= getProductCommission(
											
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
											
											$totalCom +=$a;
                                            unset($arrTmp);
                                        $totalInstallation++;
                                            
                                    }
									
							}
			

								
                                $strSql = "SELECT data_reg.FTDqualificationDate, data_reg.initialftddate, data_sales.id,data_sales.affiliate_id,data_sales.tranz_id,data_sales.trader_id,data_sales.merchant_id,data_sales.amount,data_sales.rdate,data_sales.status, data_sales.type AS data_sales_type  FROM data_sales AS data_sales "
                                        . "INNER JOIN data_reg AS data_reg ON data_sales.product_id = data_reg.product_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                        . "WHERE data_sales.type='deposit' and data_sales.product_id = '" . $ww['id'] . "' AND data_sales.rdate "
                                        . $searchInSql . " "
                                        . (isset($group_id) && $group_id != '' ? ' AND data_sales.group_id = ' . $group_id . ' ' : '');
                                
								$salesqq = function_mysql_query($strSql,__FILE__);
								$netDepositTransactions = array();
								while ($salesww = mysql_fetch_assoc($salesqq)) {
                                    //if ($salesww['type'] == 'deposit') { // OLD.
									
										$tranrow['id'] = $salesww['id'];
										$tranrow['affiliate_id'] = $salesww['affiliate_id'];
										$tranrow['tranz_id'] = $salesww['tranz_id'];
										$tranrow['trader_id'] = $salesww['trader_id'];
										$tranrow['product_id'] = $salesww['product_id'];
										$tranrow['FTDqualificationDate'] =$salesww['FTDqualificationDate'];
										$tranrow['initialftddate'] =$salesww['initialftddate'];
										$tranrow['rdate'] =$salesww['rdate'];
										$tranrow['amount'] = $salesww['amount'];
										$tranrow['type'] = $salesww['data_sales_type'];
										$tranrow['status'] = $salesww['status'];
										$netDepositTransactions[] = array($tranrow);
                                        $sumDeposits += $salesww['amount'];
										$depositsAmount+=$salesww['amount'];
                                        $depositingAccounts++;
                                    
                                    
									
                               
                                
                                $arrFtds  = getTotalFtds(
                                    $from, 
                                    $to, 
                                    0, 
                                    $ww['id'], 
                                    $ww['wallet_id'], 
                                    (isset($group_id) && $group_id != '' ? $group_id : 0), 
                                    0, 
                                    0,
                                    $searchInSql,
									0,"",
									true
                                );
                                
						
									
									
                                if (!$needToSkipMerchant) {
                                    
									
									
									foreach ($arrFtds as $arrFtd) {
										
										
										
										
                                        $real_ftd++;
                                        $real_ftd_amount += $arrFtd['amount'];
                                        
                                        $beforeNewFTD = $ftd;
	
										
                                        getFtdByDealType($ww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $ftd,false);
                        
											
                                        if ($beforeNewFTD != $ftd ) {
                                            $arrFtd['dealtype'] = 'cpa';
                                            
											 $totalCom += getProductCommission(
                                                
                                                $arrDealTypeDefaults, 
                                                $arrFtd
											
												
												
                                            ); 
                                            
                                        }
                                        unset($arrFtd);
                                    }
                                }
									}
                            
                                
                   
						
				$filterFrom = $arrRange['from'];
				$filterTo   = $arrRange['to'];
				$boxaName   = "admin-prd-quick-report-1";
                     
				
					{
				 
				$tableArr = array(
						
				
					(object) array(
					  'id' => 'id',
					  
					  'str' => '<td style="text-align: left;"><a href="/'.$userlevel .'/edit_products.php?act=products&id='.$ww['id'].'">'.$ww['id'].'</a></td>'
					  
					),(object) array(
					  'id' => 'name',
					  'str' => '<td style="text-align: left;"><a href="/'.$userlevel .'/edit_products.php?act=products&id='.$ww['id'].'">'.$ww['title'].'</a></td>'
					  
					),
					(object) array(
					  'id' => 'totalViews',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=traffic&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.@number_format($totalTraffic['totalViews'],0).'</a></td>'
					),
					(object) array(
					  'id' => 'totalClicks',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=traffic&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.@number_format($totalTraffic['totalClicks'],0).'</a></td>'
					),
					
					(object) array(
					  'id' => 'total_installation',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=installation">'.$totalInstallation.'</a></td>'
					),
					
					(object) array(
					  'id' => 'total_leads',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>'
					),
			
					(object) array(
					  'id' => 'realAccount',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=sale">'.$ftd.'</a></td>'
					),
				
					(object) array(
					  'id' => 'Commission',
					  'str' => '<td style="text-align: center;">'.price($totalCom).'</td>'
					),			
					(object) array(
					  'id' => 'click_to_signup',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'Click_to_Sale',
					  'str' => '<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
				);
				
				$listReport .= '<tr>'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'</tr>';
				
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalLeadsAccounts += $totalLeads;
				
				$totalRealAccounts += $totalReal;
				$totalFTD += $ftd;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount['amount'];
				$totalDepositAmount += $sumDeposits;
				$totalTotalInstallation += $totalInstallation;
				
				$totalComs += $totalCom;
				
				}
                                
                            unset($arrRange); // Clear up the memory.
			} // End of time-periods loop.
                    // Mark given wallet as processed.
                    $arrWallets[$ww['wallet_id']] = true;
		}
               		
                
		
		
		$tableArr = Array(
						
			
			(object) array(
			  'id' => 'id',
			  'str' => '<th style="text-align: left;">'.lang('ID').'</th>'
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th style="text-align: left;">'.lang('Product').'</th>'
			),
			(object) array(
			  'id' => 'totalViews',
			  'str' => '<th>'.lang('Impressions').'</th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th>'.lang('Clicks').'</th>'
			),
			(object) array(
			  'id' => 'total_installation',
			  'str' => '<th>'.lang(ptitle('Installation')).'</th>'
			),
		
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th>'.lang(ptitle('Lead')).'</th>'
			),
			(object) array(
			  'id' => 'realAccount',
			  'str' => '<th>'.lang(ptitle('Signups')).'</th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th>'.lang('Sale').'</th>'
			),
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.lang('Commission').'</th>'
			)	,
			(object) array(
			  'id' => 'click_to_signup',
			  'str' => '<th>'.lang(ptitle('Click to Signup')).'</th>'
			),
			(object) array(
			  'id' => 'Click_to_Sale',
			  'str' => '<th>'.lang(ptitle('Click to Sale')).'</th>'
			),
		);
		
		
		
		
		$tableArr2 = Array(
						
		
			(object) array(
			  'id' => 'id',
			  'str' => '<th style="text-align: left;"><b>'.lang('Total').':</b></th>'
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th style="text-align: left;"><b></b></th>'
			),
			(object) array(
			  'id' => 'totalViews',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=traffic&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressions.'</a></th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=traffic&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicks.'</a></th>'
			),
			
			(object) array(
			  'id' => 'total_installation',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=install&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalTotalInstallation.'</a></th>'
			),
	
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>'
			),
		
			(object) array(
			  'id' => 'realAccount',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=signup">'.$totalRealAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=sale">'.$totalFTD.'</a></th>'
			),
			
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.price($totalComs).'</th>'
			)	,			
			(object) array(
			  'id' => 'click_to_signup',
			  'str' => '<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'Click_to_Sale',
			  'str' => '<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>'
			),
		);
		
		
		$set->rowsNumberAfterSearch = 100;
		$set->content .= '
		<div class="normalTableTitle" >'.lang('Products Place').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.(!empty($set->SSLprefix) ? $set->SSLprefix : '../').'images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
			$tableStr = '
			<table width="100%" ' . (!empty($listReport) ? 'class="tablesorter mdlReportFields_prd"':'class="normal mdlReportFields_prd"') . 'border="0" cellpadding="0" cellspacing="0">
				<thead><tr>
					'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').
				'</tr></thead><tfoot><tr>
					'.setTable($tableArr2, $boxaName, $set->userInfo['productType'], '').'
					
				</tr></tfoot>
				<tbody>
				'.$listReport.'
			</table>';
			$set->content .= $tableStr ." <br/>";
			// die ($set->content
			//modal
			$fields = getReportsHiddenCols("productsPlaceReport","admin",$set->userInfo['id']);
			if($fields){
				$set->DashboardProductsPlaceHiddenCols = $fields;
			}
			$dashboardReport = lang("Products Place");
			
	}
	
		$set->content .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="49%" align="center" valign="top">
								<div class="specialTableTitle">'.lang('Today Registered Affiliates').'</div>
								<table class="normal" width="100%" border="0" cellpadding="1" cellspacing="0">
									<thead>
									<tr>
										<td>'.lang('Affiliate ID').'</td>
										<td>'.lang('Actions').'</td>
										<td style="text-align: left;">'.lang('Username').'</td>
										<td>'.lang('First Name').'</td>
								'.($set-ShowIMUserOnAffiliatesList==1 ? '<td>'.lang('IM User').'</td>' : '') .'
										<td>'.lang('Country').'</td>
										<td>'.lang('Group').'</td>
										<td>'.lang('Logged').'</td>
										<td>'.lang('Status').'</td>
									</tr>
									</thead>
									<tfoot>
									'.($listAffiliates ? $listAffiliates : '<tr><td colspan="8" align="center">'.lang('No new registration today').'</td></tr>').'
									</tfoot>
								</table>
							</td>
							<td width="2%"></td>
							<td width="49%" align="center" valign="top">
								<div class="specialTableTitle">'.lang('Last Traffic Clicks').'</div>
								<table class="normal" width="100%" border="0" cellpadding="1" cellspacing="0">
									<thead>
									<tr>
										<td>'.lang('Affiliate').'</td>
										<td>'.lang('Country').'</td>
										<td>'.lang('Last Visit').'</td>
										<td>'.lang('Merchant').'</td>
										<td>'.lang('Refer URL').'</td>
										<!--td>'.lang('Total Visits').'</td-->
									</tr>
									</thead>
									<tfoot>
									'.$lastVisits.'
									</tfoot>
								</table>
							</td>
						</tr>
					</table>';
		$qq=function_mysql_query("SELECT * FROM affiliates_notes WHERE status != 'closed' ORDER BY issue_date DESC LIMIT 10",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			unset($bgColor);
			$adminInfo = dbGet($ww['admin_id'],"admins");
			$affiliateInfo = getAffiliateRow($ww['affiliate_id'],1);
			
			$groupInfo = dbGet($affiliateInfo['group_id'],"groups");
			if ($ww['status'] == "inprocess") $bgColor = 'style="background: #d4deff;"';
				else if ($ww['status'] == "closed") $bgColor = 'style="background: #d4ffdd;"';
			if ($ww['issue_date'] < dbDate() AND $ww['status'] != "closed") $bgColor = 'style="background: #ffd4d4;"';
			$noteList .= '<tr '.($l % 2 ? 'class="trLine"' : '').' '.$bgColor.'>
						<td>'.$ww['id'].'</td>
						<td><a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'&note_id='.$ww['id'].'#notesPlace" target="_blank">'.lang('Edit').'</a></td>
						<td>'.dbDate($ww['rdate']).'</td>
						<td align="center"><a href="/admin/admins.php?act=new&id='.$adminInfo['id'].'" target="_blank">'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</a></td>
						<td>'.dbDate($ww['issue_date']).'</td>
						<td align="center"><a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$ww['affiliate_id'].'</a></td>
						<td align="center"><a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$affiliateInfo['username'].'</a></td>
						<td align="center">'.($groupInfo['id'] ? $groupInfo['title'] : lang('General')).'</td>
						<td align="center">'.round(floor((strtotime($ww['closed_date'] == "0000-00-00 00:00:00" ? dbDate() : $ww['closed_date'])-strtotime($ww['rdate']))/(60*60*24))+1).' '.lang('Day(s)').'</td>
						<td align="left">'.$ww['notes'].'</td>
						<td align="center"><b>'.lang(strtoupper($ww['status'])).'</b></td>
					</tr>';
			$l++;
			}
	$set->content .= '<br /><div class="specialTableTitle">'.lang('Managers Notes').' '.lang('CRM').'</div>
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
			</table>';
			
				include "common/ReportFieldsModal.php";
			
			if($showCasinoFields){
				$set->content=str_replace('<td>|FROZEN|</td>','<td></td>',$set->content);
				$set->content=str_replace('<th>|FROZEN|</th>','<th></th>',$set->content);
			}else{
				$set->content=str_replace('<td>|FROZEN|</td>','',$set->content);
				$set->content=str_replace('<th>|FROZEN|</th>','',$set->content);
				$set->content=str_replace('<th>FROZEN</th>','',$set->content);
			}
		
		theme();
		break;
	
    case "valid":
		$appTable = 'affiliates';
		
		$db = getAffiliateRow($id,1);

		if ($db['valid']) $valid='0'; else $valid='1';
		
		
		if ($db['id']>0 &&  ($valid==1)) {
				
		
			$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowActivated" and valid=1',__FILE__));
			sendTemplate($mailCode['mailCode'],$db['id']);
		}
		
		
		
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		
		
		
		/* 
		if ($valid==1) {
		$mailCode = 'AffiliateAccountIsNowActivated'; */
		$affiliate_id = $id;
		/* 
		sendTemplate($mailCode,$affiliate_id);
		} */
			
		echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	 
	case "logout":
		unset($_SESSION['session_id']);
		unset($_SESSION['session_serial']);
		unset($_SESSION['loggedin_time']);
		setcookie('mid','',time()+(24*60*60*30));
		$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
		if (!isAdmin()) _goto($lout);
		break;
	
	}

?>