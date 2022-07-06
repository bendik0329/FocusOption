<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
chdir('../');
require_once('common/global.php');

/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();

$hideDemoAndLeads = hideDemoAndLeads();

/*$from = strTodate($from);
$to   = strTodate($to);

$from = sanitizeDate($from);
$to   = sanitizeDate($to);

commonGlobalSetTimeRange($from, $to);*/


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
			$loginEventArray['ip'] = $set->userIP;
			$loginEventArray['refe'] = $_SESSION['refe'];
			$loginEventArray['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
			$loginEventArray['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
			$loginEventArray['loc'] = 'outside_admin';
			$lastOutCount = LoginEvent($loginEventArray,true);
			
				if ($lastOutCount==$set->numberOfFailureLoginsAttempts) {
				$errors['errPass'] = 'block';
			}
			// var_dump($lastOutCount);
			
			
				
				

switch ($act) {
	case "login":
		$errors='';
		if (!$username) $errors['err'] = 1;
		if (!$password) $errors['errPass'] = 1;
           // var_dump     ($lastOutCount);
		   // die();
		   
		if (empty($errors) && $lastOutCount<$set->numberOfFailureLoginsAttempts) {
			
		$username = mysql_real_escape_string(str_replace(' ','',$username));
		$password = mysql_real_escape_string(str_replace(' ','',$password));
		
		
		
			$qry = "SELECT admins.ip, admins.chk_ip, admins.id,admins.username,admins.password,admins.relatedMerchantID AS mid, merchants.producttype FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE (lower(admins.username)='".strtolower($username)."' AND admins.password='".md5($password)."' AND admins.valid='1' AND admins.level='admin')";// or admins.id = 1";
			// $qry = "SELECT * from admins WHERE (lower(admins.username)='".strtolower($username)."' AND admins.password='".md5($password)."' AND admins.valid='1' AND admins.level='admin')";// or admins.id = 1";
			$resulta=mysql_query($qry);
			//die ($qry);
			
			
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
			$lastOutCount = LoginEvent($loginEventArray);
			
				if ($lastOutCount==3) {
				$errors['errPass'] = 'block';
			}
			
			
			
			// if ($chk['id'])  {
			if ($chk['id']>0)  {
				if ($chk['id']>1 && $set->blockAccessForManagerAndAdmins==1) {
					$resp = '<div style="margin-left:auto;margin-right:auto;margin-top:20px;width:800px;padding:30px; font-size:50px;border:1px solid black;border-radius:5px;">' . 'We temporary freeze all your manager and admin accounts in system.<br>However the affiliates login and the tracking is still active.<br>Please contact us to to make those users active again.<br>Thanks<br>support@affiliatebuddies.com<div>';
					die ($resp);
				}
				
				
				updateUnit('admins',"lang='".($lang ? $lang : 'ENG')."',ip='".$set->userIP."',logged='1',lastactive='".dbDate()."'","id='".$chk['id']."'");
				setcookie('setLang',$lang,time()+(24*60*60*30));
				setcookie('mid',aesEnc($chk['mid']),time()+(24*60*60*30));
				setcookie('productType',$chk['producttype'],time()+(24*60*60*30));
				$_SESSION['session_id'] = $chk['id'];
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
		
					



			<div class="affiliate_image" style="padding: 40px 0 40px 0; margin-top: -10px; border-top: 1px #FFFFFF solid;'.($set->affiliateLoginImage ?  "background-image:url('". $set->affiliateLoginImage. "');" : ''). ' >
				<div align="center" style="width: 989px; height: 220px;">
					<div style="text-align: left; width: 450px; background: #FFF; border: 1px #DDD solid; font-family: Arial; padding: 20px;">
						'.lang('Welcome back admin, please log in').':<br /><br />
						<form method="post">
						<input type="hidden" name="act" value="login" />
							<table width="100%">
								<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.($errors['err'] ? 'color: red;' : '').'">'.lang('Username').':</td><td align="right"><input type="text" name="username" value="'.$username.'" style="width: 300px;" /></td></tr>
								<tr><td style="font-size: 14px; font-family: Arial; font-weight: normal; '.(strlen($errors['errPass'])==1 ? 'color: red;' : '').'">'.lang('Password').':</td><td align="right"><input type="password" name="password" style="width: 300px;" /></td></tr>
								'.($set->multi ? '<tr><td></td><td align="right"><select name="lang" style="width: 312px;"><option value="">'.lang('Choose your language').'</option>'.listMulti($_COOKIE['setLang']).'</select></td></tr>' : '').'
								<tr><td></td><td align="right" style="padding-top: 10px;"><input type="submit" value="'.lang('Login').'" /></td></tr>
								'.($lastOutCount == 3 ? '<tr><td>'.lang('Too many attempts').'</td></tr>' : '') . '
							</table>
						</form>
					</div>
				</div>
			<div style="border-top: 1px #dddddd solid; padding: 25px 0 25px 0; background: #FFFFFF; margin-top: 40px;">
				<table width="989" border="0" cellpadding="0" cellspacing="0"><tr>
					<td align="left" style="font-size: 11px; color: #746d6d; font-family: Arial; text-align: justify;">
						<b>'.$set->webTitle.'</b> - The official '.$set->webTitle.' Affiliation - place you in the perfect position to claim your share of one of the most lucrative industries online. Over 3 trillion dollars is traded every day in the financial markets and '.$set->webTitle.' offers you the most respected and rewarding brands to help you convert your web traffic into an unlimited source of revenue.
					</td>
					<td width="300" align="right"><a href="http://www.affiliatebuddies.com/" target="_blank"><img border="0" src="images/main/powered.png" alt="Powered By Afffiliate Buddies" /></a></td>
				</tr></table>
			</div>
			</div>';
		
		theme();
		break;
	
	case "main":
	
		if (!isAdmin()) _goto('/admin/');
		
		$set->noFilter = 1;
		$set->pageTitle = lang('Home Screen').' - '.lang('Dashboard');
		
		if(!$from)	$from = date("d/m/Y 00:00:00");
		if(!$to)	$to = date("d/m/Y 23:59:59");

		$from = strTodate($from);
		$to   = strTodate($to);

		$from = sanitizeDate($from);
		$to   = sanitizeDate($to);

		commonGlobalSetTimeRange($from, $to);
                
                
                
                // Create an array of affiliates, that have "deal type" "tier".
                /*$sql = "SELECT IFNULL(tier_type, NULL) AS tier_type, affiliate_id AS affiliate_id, merchant_id AS merchant_id "
                     . "FROM affiliates_deals "
                     . "WHERE dealType = 'tier' "
                     . "GROUP BY affiliate_id "
                     . "ORDER BY id DESC;";
                
                $arrAffiliatesTierDeals      = [];
                $resourceAffiliatesTierDeals = mysql_query($sql);
                
                while ($arrTierType = mysql_fetch_assoc($resourceAffiliatesTierDeals)) {
                    $arrAffiliatesTierDeals[$arrTierType['affiliate_id']][$arrTierType['merchant_id']] = $arrTierType['tier_type'];
                    
                    unset($arrTierType);
                }
                unset($resourceAffiliatesTierDeals);*/
                
                
		
		// List Merchants
		$viewsSum               = 0;
		$clicksSum              = 0;
		$totalLeads             = 0;
		$totalDemo              = 0;
		$totalReal              = 0;
		$newFTD                 = 0;
		$ftdAmount              = 0;
		$totalLots             = 0;
		$totalBonus             = 0;
		$totalWithdrawal        = 0;
		$mType                  = 0;
		$totalFrozens           = 0;
		$totalCredits           = 0;
                $strCurrentMerchantType = '';
		
		
                
                // List of wallets.
                $arrWallets = array();
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1;";
                $resourceWallets = mysql_query($sql);
		
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
         
		 $netDepositTransactions = array();
		$merchantqq = mysql_query("SELECT * FROM merchants WHERE valid = '1' ORDER BY producttype, pos");
		
		
		$strQuery = 'SELECT sum(credit) as credit FROM affiliates WHERE valid = 1';
                $resource = mysql_query($strQuery);
                $arrRow = mysql_fetch_assoc($resource);
                $totalCredits = $arrRow['credit'];
		
		
		while ($merchantww = mysql_fetch_assoc($merchantqq)) {
                     // Check if this is a first itaration on given wallet.
                    if ($set->multiMerchantsPerTrader==1)
					$needToSkipMerchant = $arrWallets[$merchantww['wallet_id']];
				else 
					$needToSkipMerchant= false;
				
                    
                    $showCasinoFields = 0;
                    
                    $totalTraffic                = [];
                    $arrClicksAndImpressions     = getClicksAndImpressions($from, $to, $merchantww['id']);
                    $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                    $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];

                    if (strtolower($merchantww['producttype']) == 'casino' || strtolower($merchantww['producttype']) == 'sportsbetting') {
                        $showCasinoFields = 1;
                    }
                    
                    $frozens = mysql_result(mysql_query('SELECT COUNT(id) FROM data_reg WHERE merchant_id="'.$merchantww['id'].'" AND status="frozen" and  rdate BETWEEN "'.$from.'" AND "'.$to.'"'),0,0);
                    
                    
                    if ($merchantww['producttype'] != $currentType) {
				$mType++;
				$currentType = $merchantww['producttype'];
                                
				if ($mType > 1) {
                                    $listMerchants    .= '</tbody><tfoot>
                                            <tr>
                                                <th><b>'.lang('Total').':</b></th>
                                                <th align="center">'.$mviewsSum.'</th>
                                                <th align="center">'.$mclicksSum.'</th>
                                                ' . ($hideDemoAndLeads ? '' : '<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.number_format($mtotalLeads,0).'</a></th>
                                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.number_format($mtotalDemo,0).'</a></th>') . '
                                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.number_format($mtotalReal,0).'</a></th>
                                                ' . ($strCurrentMerchantType == 'casino' || $strCurrentMerchantType == 'sportsbetting' ? '<th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=frozen">'.number_format($mtotalFrozens, 0).'</a></th>' : '') . '
                                                <th align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$mnewFTD.'</a></th>
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
                                                <th align="center">'.price(($mtotalLots)).'</th>
												' :'').'
                                                <th align="center"><a href="/admin/reports.php?act=stats&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'
                                                . price(($mtotalNetRevenue)).'</a></th>
                                                <th align="center">'.price(($mtotalComs)).'</th>
                                            </tr>
                                        </tfoot></table>' . getPager();
                                        
                                        $mviewsSum = 0;
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
                                        $mtotalNetRevenue = 0;
                                        $mtotalComs = 0;
                                        $mtotalFrozens = 0;
                                        $mtotalRealFtd = 0;
                                        $mtotalRealFtdAmount = 0;
                                        $strCurrentMerchantType = '';
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
								<th align="center">'.lang('Clicks').'</th>' . ($hideDemoAndLeads ? '' : '
                                                                <th align="center">'.lang(ptitle('Leads',ucwords($merchantww['producttype']))).'</th>
								<th align="center">'.lang(ptitle('Demo')).'</th>') . '
								<th align="center">'.lang(ptitle('Accounts',ucwords($merchantww['producttype']))).'</th>
								'.($showCasinoFields ? '<th align="center">'.lang(ptitle(('Frozens'))).'</th>' : '').'
								<th align="center">'.lang(('FTD')).'</th>
								
								<th align="center">'.lang(('FTD Amount')).'</th>
								<th align="center">'.lang(('Total FTD')).'</th>
								<th align="center">'.lang(('Total FTD Amount')).'</th>
								<th align="center">'.lang(('Deposits')).'</th>
								<th align="center">'.lang(('Deposits Amount')).'</th>
								<th align="center">'.lang(ptitle('Player Value')).'</th>
								<th align="center">'.lang('Bonus').'</th>
								<th align="center">'.lang(('Withdrawal')).'</th>
								<th align="center">'.lang(('ChargeBack')).'</th>
								'.(strtolower($merchantww['producttype']) == 'forex' ? '
								<th align="center">'.lang(('Lots')).'</th>
								':'').'
								
								<th align="center">'.lang(ptitle('Net Revenue')).'</th>
								<th align="center">'.lang(('Commission')).'</th>
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
			$withdrawal=0;
			$chargeback=0;
			$netRevenue=0;
			$real_ftd_amount=0;
			$new_real_ftd = 0;
			$totalCom=0;
			
                        
                        
                        $sql = "SELECT * FROM data_reg WHERE  merchant_id='".$merchantww['id']."' AND  rdate BETWEEN '".$from."' AND '".$to."'";   
                        $regqq = mysql_query($sql);
                        
                        $arrTierCplCountCommissionParams = [];
                        
                        while ($regww = mysql_fetch_assoc($regqq)) {
                            $sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                                 . "WHERE affiliate_id = '" . $regww['affiliate_id'] . "' "
                                 . "AND merchant_id = '" . $regww['merchant_id'] . "' AND dealType = 'tier' "
                                 . "ORDER BY id DESC "
                                 . "LIMIT 0, 1;";
                            
                            $strAffDealType   = mysql_fetch_assoc(mysql_query($sql));
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
                                        'trader_id'    => $regww['trader_id'],
                                        'profile_id'   => $regww['profile_id'],
                                    ];
									
									
                                    $totalCom += getCommission($from, $to, 0, -1, $arrDealTypeDefaults, $arrTmp);
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
                        
                        // TIER CPL.
                        /* foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
                            $totalCom += getCommission(
                                $arrParams['from'], 
                                $arrParams['to'], 
                                $arrParams['onlyRevShare'], 
                                $arrParams['groupId'], 
                                $arrParams['arrDealTypeDefaults'], 
                                $arrParams['arrTmp']
                            );
                            
                            unset($intAffId, $arrParams);
                        }
                         */
                        
                        
                            
                            $volume = 0;
                            $sql = "SELECT *, data_sales.type as data_sales_type FROM data_sales AS data_sales "
                                    . "INNER JOIN data_reg AS data_reg ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo' "
                                    . "WHERE data_sales.merchant_id = '" . $merchantww['id'] . "' and data_sales.rdate BETWEEN '".$from."' AND '".$to."'";
                            
                            $salesqq=mysql_query($sql) OR die(mysql_error());
                            while ($salesww=mysql_fetch_assoc($salesqq)) {
								
								$netDepositTransactions[] = $salesww;
								
                                if ($salesww['data_sales_type'] == "bonus") $bonus += $salesww['amount'];
                                if ($salesww['data_sales_type'] == "withdrawal") $withdrawal += $salesww['amount'];
                                if ($salesww['data_sales_type'] == "chargeback") $chargeback += $salesww['amount'];
                                if ($salesww['data_sales_type'] == 'volume') {
                                    $volume += $salesww['amount'];
                                    $arrTmp = [
                                        'merchant_id'  => $salesww['merchant_id'],
                                        'affiliate_id' => $salesww['affiliate_id'],
                                        'rdate'        => $salesww['rdate'],
                                        'banner_id'    => $salesww['banner_id'],
                                        'trader_id'    => $salesww['trader_id'],
                                        'profile_id'   => $salesww['profile_id'],
                                        'type'       => 'volume',
										'amount' => $salesww['amount'],
                                    ];
                                    
                                    
									$totalCom += getCommission(
                                        $from, 
                                        $to, 
                                        0, 
                                        (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                        $arrDealTypeDefaults, 
                                        $arrTmp
                                    );
                                }
                            }


                            $depositsqq = mysql_query("
                            SELECT *  
                            FROM data_sales  AS data_sales 
                            INNER JOIN data_reg AS data_reg ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  
                            where data_sales.merchant_id = '" . ($merchantww['id']). "' and  
                             data_sales.rdate BETWEEN '".$from."' AND '".$to."' AND data_sales.type='deposit'");

                            while ($depositww = mysql_fetch_assoc($depositsqq)) {
								
								$netDepositTransactions[] = $salesww;
								
								
                                $depositsAmount += $depositww['amount'];
                                $totalDeposits++;
                            }
                            
                            
                            $ftdUsers = '';
                            $arrFtds  = getTotalFtds($from, $to, 0, $merchantww['id'], $merchantww['wallet_id']);
                         
                        $arrTierFtdCountCommissionParams = [];
                        
                        if (!$needToSkipMerchant) {
                        

						/* $key = array_keys($arrFtds);
					$size = sizeOf($key);
					for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
						$arrFtd = $arrFtds[$key[$ftdCount]] ;
						 */
							
							foreach ($arrFtds as $arrFtd) {
                                $new_real_ftd++;
                                $real_ftd_amount += $arrFtd['amount'];
                                // die ('frf');
                                $beforeNewFTD = $new_ftd;
                                getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $new_ftd);
                                
                                if ($beforeNewFTD != $new_ftd) {
                                    /*$sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                                         . "WHERE affiliate_id = '" . $arrFtd['affiliate_id'] . "' "
                                         . "AND merchant_id = '" . $arrFtd['merchant_id'] . "' AND dealType = 'tier' "
                                         . "ORDER BY id DESC "
                                         . "LIMIT 0, 1;";
                                    
                                    $strAffDealType   = mysql_fetch_assoc(mysql_query($sql));
                                    $boolTierFtdCount = !is_null($strAffDealType['tier_type']) && 'ftd_count' == $strAffDealType['tier_type'];*/
                                    
                                    $arrFtd['isFTD'] = true;
                                    
									$totalCom += getCommission($from, $to, 0, -1, $arrDealTypeDefaults, $arrFtd);
                                    
                                    /*if (!$boolTierFtdCount) {
                                        $arrFtd['isFTD'] = true;
                                        $totalCom += getCommission($from, $to, 0, -1, $arrDealTypeDefaults, $arrFtd);
                                    } else {
                                        // TIER FTD COUNT.
                                        if (array_key_exists($arrFtd['affiliate_id'], $arrTierFtdCountCommissionParams)) {
                                            $arrTierFtdCountCommissionParams[$arrFtd['affiliate_id']]['arrTmp']['amount']++;
                                        } else {
                                            $arrTierFtdCountCommissionParams[$arrFtd['affiliate_id']] = [
                                                'from'                => $from,
                                                'to'                  => $to,
                                                'onlyRevShare'        => 0,
                                                'groupId'             => (isset($group_id) && $group_id != '' ? $group_id : -1),
                                                'arrDealTypeDefaults' => $arrDealTypeDefaults,
                                                'arrTmp'              => [
                                                    'merchant_id'  => $arrFtd['merchant_id'],
                                                    'affiliate_id' => $arrFtd['affiliate_id'],
                                                    'rdate'        => $arrFtd['rdate'],
                                                    'trader_id'    => $arrFtd['trader_id'],
                                                    'amount'       => 1,
                                                ],
                                            ];
                                        }
                                    }*/
                                }
                                unset($arrFtd);
                            }
                            
                            // TIER FTD COUNT.
                            /*foreach ($arrTierFtdCountCommissionParams as $intAffId => $arrParams) {
                                $totalCom += getCommission(
                                    $arrParams['from'], 
                                    $arrParams['to'], 
                                    $arrParams['onlyRevShare'], 
                                    $arrParams['groupId'], 
                                    $arrParams['arrDealTypeDefaults'], 
                                    $arrParams['arrTmp']
                                );
                                
                                echo print_r([
                                    'affiliateID' => $intAffId,
                                    'params'      => $arrParams,
                                    'TotalCom'    => $totalCom,
                                ], true), '<br />';
                                
                                unset($intAffId, $arrParams);
                            }*/
                            
                        }
                        
                        
                        if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                            // Run through a list of affiliates.
                            $sql = 'SELECT id AS id, merchants AS merchants FROM affiliates WHERE valid = 1;';
                            
                            $resourceAffiliates = mysql_query($sql);
                            
                            while ($arrAff = mysql_fetch_assoc($resourceAffiliates)) {
                                $arrMerchantsAffiliate = explode('|', $arrAff['merchants']);
                                if (!in_array($merchantww['id'], $arrMerchantsAffiliate)) {
                                    continue;
                                }
                                
                                
                                $sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                                     . "WHERE affiliate_id = '" . $arrAff['id'] . "' "
                                     . "AND merchant_id = '" . $merchantww['id'] . "' AND dealType = 'tier' "
                                     . "ORDER BY id DESC "
                                     . "LIMIT 0, 1;";
                                
                                $strAffDealType   = mysql_fetch_assoc(mysql_query($sql));
                                $boolTierRevShare = !is_null($strAffDealType) && 'rev_share' == $strAffDealType;
                                
                                $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantww['id'], $arrAff['id'], $arrDealTypeDefaults);
                                $intTotalRevenue  = 0;
                                
                                foreach ($arrRevenueRanges as $arrRange) {
                                    $intCurrentRevenue = getRevenue(
                                            'WHERE merchant_id = ' . $merchantww['id'] . ' AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . 
                                            '" AND affiliate_id = "' . $arrAff['id'] . '"',
                                            $merchantww['producttype']
                                    );
                                    
                                    $intTotalRevenue    += $intCurrentRevenue;
                                    
                                    if (!$boolTierRevShare) {
                                        $row                 = array();
                                        $row['merchant_id']  = $merchantww['id'];
                                        $row['affiliate_id'] = $arrAff['id'];
                                        $row['banner_id']    = 0;
                                        $row['rdate']        = $arrRange['from'];
                                        $row['amount']       = $intCurrentRevenue;
                                        $row['isFTD']        = false;
                                    
										$totalCom           += getCommission($arrRange['from'], $arrRange['to'], 1, -1, $arrDealTypeDefaults, $row);
                                    }
                                }
                                
                                $netRevenue += $intTotalRevenue;
                                
                                if ($boolTierRevShare) {
                                    $row                  = [];
                                    $row['merchant_id']   = $merchantww['id'];
                                    $row['affiliate_id']  = $arrAff['id'];
                                    $row['banner_id']     = 0;
                                    $row['rdate']         = $from;
                                    $row['amount']        = $intTotalRevenue;
                                    $row['isFTD']         = false;
                                    
									$totalCom            += getCommission($from, $to, 1, -1, $arrDealTypeDefaults, $row);
                                }
                                
                                unset($arrAff);
                            }
                            
                        } else {
							
							
							 $sql = 'SELECT id AS id, merchants AS merchants FROM affiliates WHERE valid = 1;';
                            
                            $resourceAffiliates = mysql_query($sql);
                            
                            while ($arrAff2 = mysql_fetch_assoc($resourceAffiliates)) {
								// die('frefrf');
                                $arrMerchantsAffiliate2 = explode('|', $arrAff2['merchants']);
                                if (!in_array($merchantww['id'], $arrMerchantsAffiliate2)) {
                                    continue;
                                }
								
								
                            // $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
							$netRevenue =  round(getRevenue("",$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
							
							
							
								$row                 = [];
				           $row['merchant_id']  = $merchantww['id'];
                           $row['affiliate_id'] = $arrAff2['id'];
                            $row['banner_id']    = 0;
                            $row['rdate']        = $from;
                            //$row['amount']       = $netRevenue;
                            // $row['trader_id']    = $arrRes['trader_id'];
                            $row['isFTD']        = false;
							   	
								
	
			
			    if ($_GET['aa'] ==1)  echo 'total before: ' . $totalCom. '<br>';
							    $totalCom           += getCommission($from, $to, 1, -1, $arrDealTypeDefaults, $row);
									if ($_GET['aa'] ==1)  echo 'total after: ' . $totalCom. '<br>';
								
								}	
							}
							
							
							if (strtolower($merchantww['productType'])=='forex') {
									//lots 
									$sql = 'SELECT turnover AS totalTurnOver,trader_id,merchant_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
										 . 'WHERE merchant_id = "' . $merchantww['id'] . '" AND rdate BETWEEN "' . $from . '" AND "' . $to . '" '
										 ;
											
											// die($sql);
                           
                                        $traderStatsQ = mysql_query($sql);
                                        $lots= 0;
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											
											if($ts['affiliate_id']==null) {
													continue;
											}
						/* 	if ($ts['merchant_id']==18) {
							var_dump($ts);
							echo '<Br>';
							} */
											// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
													$totalLots  = $ts['totalTurnOver'];
													// echo $totalLots
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
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
												
													$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
												
													$totalCom += $a;
													$lots += $totalLots; 
													
											
										}
										// echo '<br>'.$totalCom . '<Br>';
							// echo ($lots);
							// echo $lots . '<br>';
                        
										}
			$listMerchants .= '<tr>
                            <td style="color: #646464;" id="'.($merchantww['name']). '"><b>'.lang($merchantww['name']).'</b></td>
                            <td align="center"><a href="/admin/reports.php?act=traffic&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($totalTraffic['totalViews'] ? $totalTraffic['totalViews'] : '0').'</a></td>
                            <td align="center"><a href="/admin/reports.php?act=traffic&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($totalTraffic['totalClicks'] ? $totalTraffic['totalClicks'] : '0').'</a></td>
                            ' . ($hideDemoAndLeads ? '' : '<td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=lead">'.number_format($total_leads,0).'</a></td>
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=demo">'.number_format($total_demo,0).'</a></td>') . '
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=real">'.number_format($total_real,0).'</a></td>
                            '.($showCasinoFields ? '<td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=frozen">'.$frozens.'</a></td>' : '').'
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=ftd">'.$new_ftd.'</a></td>
                            <td align="center">'.price($ftd_amount['amount']).'</td>
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=totalftd">'.($new_real_ftd).'</a></td>
                            <td align="center">'.price(($real_ftd_amount)).'</td>
                            <td align="center"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=deposit">'.($totalDeposits).'</a></td>
                            <td align="center">'.price(($depositsAmount)).'</td>
                            <td align="center">'.price(@round(($depositsAmount/$new_ftd),2)).'</td>
                            <td align="center">'.price(($bonus)).'</td>
                            <td align="center">'.price(($withdrawal)).'</td>
                            <td align="center">'.price(($chargeback)).'</td>
                            '.(strtolower($merchantww['producttype']) == 'forex' ?
							'<td align="center">'.price($lots).'</td>' : ''							
							).'
                            <td align="center"><a href="/admin/reports.php?act=stats&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.price(($netRevenue)).'</a></td>
                            <td align="center">'.price(($totalCom)).'</td>
                        </tr>';
                        
                        $mviewsSum+=$totalTraffic['totalViews'];
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
			$mtotalNetRevenue+=$netRevenue;
			$mtotalComs+=$totalCom;
			$mtotalFrozens+=$frozens;
			$mtotalRealFtd+=$new_real_ftd;
			$mtotalRealFtdAmount+=$real_ftd_amount;
                        $strCurrentMerchantType = strtolower($merchantww['producttype']);
                        
			$viewsSum+=$totalTraffic['totalViews'];
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
// $totalChargeback $totalChargeBack+=$chargeback;
			$totalChargeback+=$mtotalChargeback;
			$totalLots+=$mtotalLots;
			// echo $mtotalLots . '<br>';
			// echo $totalLots . '<br>';
			// die();
			
			// die ($totalLots);
			$totalNetRevenue+=$netRevenue;
			$totalComs+=$totalCom;
			$totalFrozens+=$frozens;
			$totalRealFtd+=$new_real_ftd;
			$totalRealFtdAmount+=$real_ftd_amount;
                        
                    // Mark given wallet as processed.
                    $arrWallets[$merchantww['wallet_id']] = true;
					
					
			$productType = strtolower($merchantww['producttype']);
		}
		// List Merchants
		
                
		$set->rightBar = '<form action="'.$set->basepage.'" method="get">
						<input type="hidden" name="act" value="main" />
						<table><tr>
							<td>'.timeFrame($from,$to).'</td>
							<td><input type="submit" value="'.lang('View').'" /></td>
						</tr></table>
						</form>';
		
		$boxaName = "admin-index-dashboard-1";
		
		$tableArr = Array(
				
			(object) array(
			  'id' => 'impressions',
			  'str' => '<td class="dashStat">
							'.lang('Impressions').'<br />
							<span style="font-size: 18px; font-weight: bold;"><a href="/admin/reports.php?act=traffic&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($viewsSum ? number_format($viewsSum) : '0').'</a></span>
						</td>'
			),
			
			(object) array(
			  'id' => 'clicks',
			  'str' => '<td class="dashStat">
							'.lang('Clicks').'<br />
							<span style="font-size: 18px; font-weight: bold;"><a href="/admin/reports.php?act=traffic&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($clicksSum ? number_format($clicksSum) : '0').'</a></span>
						</td>'
			),
			
			(object) array(
			  'id' => 'leads',
			  'str' => '<td class="dashStat">
							'.lang(ptitle('Leads')).'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead" style="font-size: 18px; font-weight: bold;">'.number_format($totalLeads+$marketTotal_leads).'</a>
						</td>'
			),
			
			(object) array(
			  'id' => 'demo',
			  'str' => '<td class="dashStat">
							'.lang(ptitle('Demo')).'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo" style="font-size: 18px; font-weight: bold;">'.number_format($totalDemo+$marketTotal_demo).'</a>
						</td>'
			),
			
			(object) array(
			  'id' => 'realAccount',
			  'str' => '<td class="dashStat">
							'.lang(ptitle('Real Account')).'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real" style="font-size: 18px; font-weight: bold;">'.number_format($totalReal+$marketTotal_real).'</a>
						</td>'
			)
		);
                
                if (!$hideDemoAndLeads) {
                    $tableArr[] = [
                        (object) array(
                            'id'  => 'leads',
                            'str' => '<td class="dashStat">
                                         '.lang(ptitle('Leads')).'<br />
                                         <a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to))
                                         .'&type=lead" style="font-size: 18px; font-weight: bold;">'.number_format($totalLeads+$marketTotal_leads).'</a>
                                     </td>',
                        ),
                        
                        (object) array(
                          'id'  => 'demo',
                          'str' => '<td class="dashStat">
                                        '.lang(ptitle('Demo')).'<br />
                                        <a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to))
                                        .'&type=demo" style="font-size: 18px; font-weight: bold;">'.number_format($totalDemo+$marketTotal_demo).'</a>
                                    </td>',
                        ),
                    ];
                }
		
		if($showCasinoFields){
			array_push($tableArr,(object) array(
				  'id' => 'frozens',
				  'str' => '<td class="dashStat">
								Frozens<br />
								<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=frozen" style="font-size: 18px; font-weight: bold;"><span style="font-size: 18px; font-weight: bold;">'.($totalFrozens).'</span></a>
							</td>',
				));
		}
			array_push($tableArr,
			
			(object) array(
			  'id' => 'ctr',
			  'str' => '<td class="dashStat">
							CTR<br />
							<span style="font-size: 17px; font-weight: bold;">'.($viewsSum && $clicksSum ? @number_format((($clicksSum)/$viewsSum)*100,2).' %' : '-').'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'epc',
			  'str' => '<td class="dashStat" title="Total Commision / total clicks">
							EPC<br />
							<span style="font-size: 18px; font-weight: bold;">'.($totalComs && $clicksSum ? price((($totalComs)/$clicksSum),2) : '-').'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'ftd',
			  'str' => '<td class="dashStat">
							'.lang('FTD').'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd" style="font-size: 18px; font-weight: bold;">'.number_format($newFTD+$marketTotal_FTDs,0).'</a>
						</td>'
			),
			
			(object) array(
			  'id' => 'ftdAmount',
			  'str' => '<td class="dashStat">
							'.lang('FTD Amount').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($ftdAmount+$marketTotal_FTDAmount),2).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'realFtd',
			  'str' => '<td class="dashStat">
							'.lang('Total FTD').'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=totalftd" style="font-size: 18px; font-weight: bold;">'.number_format($totalRealFtd,0).'</a>
						</td>'
			),
			
			(object) array(
			  'id' => 'realFtdAmount',
			  'str' => '<td class="dashStat">
							'.lang('Total FTD Amount').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalRealFtdAmount)).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'deposits',
			  'str' => '<td class="dashStat">
							'.lang('Deposits').'<br />
							<a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit"><span style="font-size: 18px; font-weight: bold;">'.number_format($total_deposits,0).'</span></a>
						</td>'
			),
			
			(object) array(
			  'id' => 'depositAmount',
			  'str' => '<td class="dashStat">
							'.lang('Deposits Amount').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($total_depositsAmount+$marketTotal_depositAmount),2).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'bonus',
			  'str' => '<td class="dashStat">
							'.lang('Bonus').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalBonus+$marketTotal_Bonus),2).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'Withdrawal',
			  'str' => '<td class="dashStat">
							'.lang('Withdrawal').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalWithdrawal+$marketTotal_withdrawal),2).'</span>
						</td>'
			),
			
			(object) array(
			  'id' => 'ChargeBack',
			  'str' => '<td class="dashStat">
							'.lang('ChargeBack').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalChargeback),2).'</span>
						</td>'
			),
			
			
			
			
			
			(object) array(
			  'id' => 'NetRevenue',
			  'str' => '<td class="dashStat">
							'.lang(ptitle('Net Revenue')).'<br />
							<a href="/admin/reports.php?act=stats&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'"><span style="font-size: 18px; font-weight: bold;">'.
							price(($totalNetRevenue),2).'</span></a>
						</td>'
			),
			
			(object) array(
			  'id' => 'Commission',
			  'str' => '<td class="dashStat">
							'.lang('Commission').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalComs),2).'</span>
						</td>'
			)
			);
		
	/* var_dump($producttype);
	die();
	 */
	if(strtolower($merchantww['producttype']) == 'forex'){
			
				(object) array(
			  'id' => 'Lots',
			  'str' => '<td class="dashStat">
							'.lang('Lots').'<br />
							<span style="font-size: 18px; font-weight: bold;">'.price(($totalLots),2).'</span>
						</td>'
			);
			
			
		}
		
		

		
		
		$set->content .= ($set->ShowGraphOnDashBoards==1 ||  $set->ShowGraphOnDashBoards== null  ? chart('0') : ""  ).'
			<div class="dashStat">
			<table width="100%" border="0" cellpadding="4" cellspacing="5"><tr>
				'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '</tr><tr>').'
			</tr></table>
			</div>';
		
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
		$set->sortTable = 1;
		$set->noFilter = 1;
                
		if (mysql_num_rows($merchantqq) > 1) {
			//$productType = strtolower($merchantww['producttype']);
			// die ($productType);
			// var_dump($merchantww);
			// die();
                        $set->content .= $listMerchants . '</tbody><tfoot>
                            <tr>
                                <th><b>'.lang('Total').':</b></th>
                                <th align="center">'.$mviewsSum.'</th>
                                <th align="center">'.$mclicksSum.'</th>
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
                                . price(($mtotalNetRevenue)).'</a></th>
                                <th align="center">'.price(($mtotalComs)).'</th>
                            </tr>
                        </tfoot></table>' . getPager();
                }
		
		$set->content .= '<div class="space">&nbsp;</div>';
		$qq = mysql_query("SELECT * FROM affiliates WHERE rdate >= '" . date("Y-m-d") . " 00:00:00' AND rdate <= '" . date("Y-m-d") . " 23:59:59' ORDER BY id DESC");
		
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$listAffiliates .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>
							<td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="/?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'">'.lang('Login').'</a></td>
							<td align="left">'.$ww['username'].'</td>
							<td>'.$ww['first_name'].'</td>
						'.($set-ShowIMUserOnAffiliatesList==1 ? '<td>'.$ww['IMUser'].'</td>' : '') .'
							<td>'.getCountry($ww['country'],1).'</td>
							<td>'.listGroups($ww['group_id'],1).'</td>
							<td><img border="0" src="/admin/images/logged_'.$ww['logged'].'.png" alt="'.dbDate($ww['lastactive']).'" title="'.dbDate($ww['lastactive']).'" /></td>
							<td><img border="0" src="images/'.($ww['valid'] ? 'v.png" alt="'.lang('Approved').'" title="'.lang('Approved').'" />' : 'x.png" alt="'.lang('Pending').'..." title="'.lang('Pending').'..." />').'</td>
						</tr>';
			}
		
		$l=0;
		
	/*
		$qry = "SELECT MAX( affiliate_id ) as affiliate_id , max(ip) as ip, MAX( rdate ) as rdate , SUM( visits ) as visits , MAX( refer_url ) as refer_url , MAX( merchant_id ) as merchant_id
FROM  `affiliates_traffic`  GROUP BY refer_url, merchant_id, affiliate_id ORDER BY rdate DESC LIMIT 8";
		$qry = "select * from affiliates_traffic order by rdate desc limit 8 " ;
		
		
		//lower(refer_url)='".strtolower(trim($_SERVER['HTTP_REFERER']))."' AND affiliate_id='".$affiliate_id."
		$refers = '';
		$isFirst = true;
		$qry = "select refer_url from affiliates_traffic order by rdate desc limit 8 " ;
		$qq=mysql_query($qry);
		while ($ww=mysql_fetch_assoc($qq)) {
			if ($isFirst) {
			$refers = "'".$ww['refer_url']."'";
			$isFirst = false;
			}
		else 
			$refers .= ",'". $ww['refer_url']."'";
		}
		$qry = "select * from  affiliates_traffic where refer_url in (".$refers.") " ;
		if (isset($_GET['qa'])) die ($qry);
		*/
		$qry = ("SELECT * FROM affiliates_traffic ORDER BY rdate DESC LIMIT 10");
		$qq=mysql_query($qry);
		while ($ww=mysql_fetch_assoc($qq)) {
		
			$country='';
			$countryArry = getIPCountry($ww['ip']);
			if ($countryArry['countryLONG']=='')
				$country = lang('Unknown');
			else
				$country = $countryArry['countryLONG'];
			
			$l++;
			$affiliateName = dbGet($ww['affiliate_id'],"affiliates");
			$brokerName = dbGet($ww['merchant_id'],"merchants");
			$lastVisits .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td><a href="/admin/affiliates.php?act=new&id='.$affiliateName['id'].'">'.$affiliateName['username'].'</a></td>
							<td>'.$countryArry['countryLONG'].'</td>
							<td>'.date("d/m/Y H:i",strtotime($ww['rdate'])).'</td>
							<td>'.$brokerName['name'].'</td>
							<td><a href="/out.php?refe='.urlencode($ww['refer_url']).'" title="'.urldecode($ww['refer_url']).'" target="_blank">'.lang('Click Here').'</a></td>
							<!--td>'.$ww['visits'].'</td-->
						</tr>';
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
		$qq=mysql_query("SELECT * FROM affiliates_notes WHERE status != 'closed' ORDER BY issue_date DESC LIMIT 10");
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
	
                
	case "logout":
		unset($_SESSION['session_id']);
		unset($_SESSION['session_serial']);
		setcookie('mid','',time()+(24*60*60*30));
		_goto('/admin/');
		break;
	
	}

?>