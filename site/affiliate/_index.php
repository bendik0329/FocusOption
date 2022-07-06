<?php
ini_set('memory_limit', '1024M');


/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
chdir('../');

require_once('common/global.php');
require_once('common/subAffiliateData.php');

	$ip = getClientIp();

	if(checkUserFirewallIP($ip)){

			$activityLogUrl = "http".$set->SSLswitch."://".$_SERVER['SERVER_NAME']. "/ajax/saveLogActivity.php?user_id=".$set->userInfo['id']."&ip=".$ip."&country=" . $country_id."&location=login&userType=affiliate&theChange=Blocked user trying to enter the system&_function_=".__FUNCTION__ . "&_file_=". __FILE__ . "&queryString=" . urlencode($queryString);
			doPost($activityLogUrl);
			$url = 'http'.$set->SSLswitch.'://'.$_SERVER['SERVER_NAME']."/404.php";
				header("Location: ".$url);
			die('--');
	}
	
	
$showInnerTableForMerchantMoreThan = 1;


if (!isLogin()) {
	if(isset($_GET['v'])){
		_goto($set->SSLprefix.'?v=1');
	}
	else{
		
	_goto($set->SSLprefix);
	}
	
}



$arrDealTypeDefaults = getMerchantDealTypeDefaults();


getPermissions();



$hideDemoAndLeads = hideDemoAndLeads();

switch ($act) {
	
	case "approveMessage":
				if($flag ==1){
					$sql  ="insert into affiliate_messages_approval (affiliate_id,message_id) values (". $set->userInfo['id'] .",". $msg_id .")";
					function_mysql_query($sql,__FILE__);
					echo true;
					die;
				}
				break;
	default:
		$set->noFilter = 1;
		$showCasinoFields = 0;
		$merchantIDs = ($set->userInfo['merchants']);
		$noMerchants = 0;
		if ($merchantIDs=='' || empty($merchantIDs)){
			$noMerchants = 1;
		}
		
		
		$merchantIDs = str_replace('|',",",$merchantIDs);
		$merchantIDs = ltrim($merchantIDs,',');
		$affwhere = $merchantIDs;
		
		
				if (!empty($affwhere)) {
                $sql = 'SELECT * FROM merchants '
                     . 'WHERE valid = 1 AND id IN (' . $affwhere . ') '
                     . 'ORDER BY producttype, pos';
            
		$merchantqq = function_mysql_query($sql,__FILE__);
		$merchantsArray= array();
		$showForex = 0 ;
		$hasActiveMerchants = 0;
		while ($merchantww = mysql_fetch_assoc($merchantqq)) {
			$merchantsArray[$merchantww['id']] = $merchantww;
			$hasActiveMerchants=1;
			if (strtolower($merchantww['producttype']) == 'forex')
				$showForex=1;
		}
		}
		
		if (!$hasActiveMerchants)
			$noMerchants=1;
		
		
		$pageTitle = lang('Home Screen - Dashboard');
		$set->pageTitle = $pageTitle;

		if(!$from)	$from = date("d/m/Y 00:00:00");
		if(!$to)	$to = date("d/m/Y 23:59:59");
		
		$from = strTodate($from);
		$to = strTodate($to);
		
		$from = sanitizeDate($from);
		$to   = sanitizeDate($to);
		
		$set->rightBar = '<form action="'.$set->basepage.'" method="get">
						<input type="hidden" name="act" value="main" />
						<table><tr>
							<td>'.timeFrame($from,$to).'</td>
							<td><input type="submit" value="'.lang('View').'" /></td>
						</tr></table>
						</form>';
		
		commonGlobalSetTimeRange($from, $to);
                
                // List of wallets.
                $arrWallets = array();
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1;";
                $resourceWallets = function_mysql_query($sql,__FILE__);
                
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
                
		// List Merchants
		$viewsSum=0;
		$clicksSum=0;
		$totalLeads=0;
		$totalDemo=0;
		$totalReal=0;
		$newFTD=0;
		$ftdAmount=0;
		$totalBonus=0;
		$totalWithdrawal=0;
		$mType=0;
		$totalFrozens = 0;
		$totalCPI = 0;

		
		$products_list="";
		if($set->showProductsPlace == 1 && !empty($set->userInfo['products']) ){
			$products_list = ltrim(str_replace ("|","," , $set->userInfo['products']),',');
		}
		
		

		$displayRevTotal = 0;
		$displayFtdAmountTotal = 0;
		$displayTraderValue = 0;
               
		if ($noMerchants == 1 ) {
			if (empty($products_list))
			$set->content .= '<center><div style="font-size: 26px;">'.($set->introducingBrokerInterface ? lang('You have no activated brands, please contact your Introducing Broker') :  lang('You have no activated brands, please contact your account manager') ) .'</div></center><br><br><br><br><br><br>';
		} else  {
					$brandsCounter=1;
					$brandsDealsArray = array();
                    // while ($merchantww = mysql_fetch_assoc($merchantqq)) {
						foreach ($merchantsArray as $merchantww){
						
						
								
			$deal = AffiliateDealType($merchantww['id'],$arrDealTypeDefaults);
			if (!$deal)
	continue;

						$brandsCounter++;

						$brandsDealsArray[$brandsCounter] = $deal;
						// var_dump($brandsDealsArray);
						
						
						
                        // Check if this is a first itaration on given wallet.
						if ($set->multiMerchantsPerTrader==1)
                        $needToSkipMerchant = $arrWallets[$merchantww['wallet_id']];
	
                				else 
					$needToSkipMerchant= false;
				
				$ealiestTimeForNetRev = date('Y-m-d H:i:s');
				
				
			$frozens = mysql_result(function_mysql_query('SELECT COUNT(id) FROM data_reg WHERE merchant_id="'.$merchantww['id'].'" AND affiliate_id="'.$set->userInfo['id'].'" AND status="frozen" and  rdate BETWEEN "'.$from.'" AND "'.$to.'"',__FILE__),0,0);
			$showCasinoFields = 0;
                        $hideFrozens = 0;
                        
                        // $hideFrozens = $set->hideFrozenOnCPAdeals;
                        
			if (strtolower($merchantww['producttype']) == 'casino' || strtolower($merchantww['producttype']) == 'sportsbetting') {
                            $showCasinoFields = 1;
			}

			if ($merchantww['producttype'] != $currentType) {
				
				$netDepositTransactions = array();
				
					// die ($deal);
					
                            $mType++;
                            $currentType = $merchantww['producttype'];

							
                            if ($mType > 1) {

					$deal = $brandsDealsArray[$brandsCounter-1];
					// echo $deal . '<br>';
					
					
                                $listMerchants .= '</tbody><tfoot id="top">
                                <tr>
                                 <th><b>'.lang('Total').':</b></th>
                            '.(allowView('af-impr',$deal,'fields') ?'
							<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.$mviewsSum.'</a></th>
							' : '' ) .'
                            '.(allowView('af-clck',$deal,'fields') ? '
                            <th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.$mclicksSum.'</a></th>
							' : '' ). 
							
							(allowView('af-instl',$deal,'fields') && $set->deal_cpi==1 ? '
                            <th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($mInstallSum ? number_format($mInstallSum) : '0').'</a></th>
							' : '' ) ;
							
													
							
								
                                if (!$hideDemoAndLeads) {
                                    $listMerchants .= '
										'. (allowView('af-lead',$deal,'fields') ? '	
									<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.number_format($mtotalLeads,0).'</a></th>
									 ' : '' ).'
											'. (allowView('af-demo',$deal,'fields') ? '			
                                                    <th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.number_format($mtotalDemo,0).'</a></th>' : '');
                                }
								
								
								 
                                
                                $listMerchants .= '
                                    '. (allowView('af-real',$deal,'fields') ? '<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.number_format($mtotalReal,0).'</a></th>':'').'
                                    '.((allowView('af-frzn',$deal,'fields') && $showCasinoFields) ? '<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=frozen">'.$mtotalFrozens.'</a></th>' : '').'
                                    '. (allowView('af-ftd',$deal,'fields') ? '<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$mnewFTD.'</a></th>':'').'
                                    '.(allowView('af-ftda',$deal,'fields') ? '<th align="center">'.price($mftdAmount).'</th>' : '').'
                                    
									
                        '.(allowView('af-tftd',$deal,'fields') ? '<th align="center" id="tftd-f">'.$mtotalRealFtd.'</th>' : '').'

                         
                        '.(allowView('af-tftda',$deal,'fields') ? '<th align="center" id="tftda">'.price($mtotalRealFtdAmount).'</th>' : '').'
						
						
									'.
                                    
                                    ((allowView('af-depo',$deal,'fields'))
                                        ? '<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to))
                                            . '&type=deposit">'.$mtotal_deposits.'</a></th>' :''
                                    ) .     
                                    ((allowView('af-depoam',$deal,'fields'))
                                        ?  '<th align="center">'.price($mtotal_depositsAmount).'</th>' :''
                                    ) . 
                                    (allowView('af-trdvlu',$deal,'fields') ? 
										'<th align="center">'.price(@round($mtotal_depositsAmount/$mnewFTD,2)).'</th>' 
										: '') . 
                                    ((allowView('af-bns',$deal,'fields'))
                                        ?  '<th align="center">'.price($mtotalBonus).'</th>' :''
                                    ) . 
                                    ( (allowView('af-withd',$deal,'fields'))
                                        ?  '<th align="center">'.price($mtotalWithdrawal).'</th>': '').'
									'.( (allowView('af-chrgb',$deal,'fields')) ?
                                             '<th align="center">'.price($mtotalChargeback).'</th>': '').
                                    
									(allowView('af-vlm',$deal,'fields') && strtolower($productType)=='forex' ?
                                             '<th align="center">'.price($mtotalLots).'</th>': '').

									(allowView('af-ntrv',$deal,'fields') ?  '<th align="center">'.price($mtotalNetRevenue).'</th>' : '')
									.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields') ? '
										<th>'.price($mtotalPnl).'</th> ' : '')
									.(allowView('af-qftd',$deal,'fields')? '
							<th>'.($mnewQFTD).'</th> ' : '')
							.'
                                    <th align="center">'.price($mtotalComs).'</th>
                                    </tr>
                                 </tfoot></table>' . getPager() . '
                                <div class="space">&nbsp;</div>';
                                         
										 
                                
                                $mviewsSum = 0;
                                $mclicksSum = 0;
								$mInstallSum = 0 ;
                                $mtotalLeads = 0;
                                $mtotalDemo = 0;
                                $mtotalReal = 0;
                                $mnewQFTD = 0;
                                $mnewFTD = 0;
                                $mtotal_deposits = 0;
                                $mtotal_depositsAmount = 0;
                                $mftdAmount = 0;
                                $mtotalBonus = 0;
                                $mtotalWithdrawal = 0;
                                $mtotalChargeBack = 0;
                                $mtotalComs = 0;
                                $mtotalLots = 0;
                                $mtotalPnl = 0;
                                $mtotalRealFtd = 0;
                                $mtotalRealFtdAmount = 0;
                                $mtotalFrozens = 0;
                                $mtotalNetRevenue = 0;
                            }
							$productType = strtolower($merchantww['producttype']);
							
                            if ($set->dashBoardMainTitle != '') {
                                $listMerchants .= '<div class="space">&nbsp;</div><div class="specialTableTitle">'.$set->dashBoardMainTitle .'</div>';
                            } else {
                                $listMerchants .= '<div class="space">&nbsp;</div><div class="specialTableTitle">'.typeName($merchantww['producttype']).' '.(lang(ptitle('Brokers'))).'</div>';
                            }
                            
                            $listMerchants .= '|HEADER|';
			}
			$deal = $brandsDealsArray[$brandsCounter];
			
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
			$withdrawal=0;
			$chargeback=0;
			$netRevenue=0;
			$totalCom=0;
			$lots=$totalPNL = 0;
			$real_ftd_amount=0;
			$new_real_ftd = 0;
                        
			$showDataForAffiliateSince = $merchantww['showDataForAffiliateSince'];
			$showDataForAffiliateSinceWhere = '';
                        
                        
			if ($showDataForAffiliateSince > '2000-11-30 00:00:00') { 
                            $showDataForAffiliateSinceWhere = " AND rdate > '" . $showDataForAffiliateSince . "' ";
                        }
                        
                        
                        $total                   = [];
                        // $arrClicksAndImpressions = getClicksAndImpressions($from, $to, $merchantww['id'], $set->userInfo['id']);
						$arrClicksAndImpressions     = getTotalClicksAndImpressions($from, $to, $merchantww['id'], $set->userInfo['id']);
                        $total['viewsSum']       = $arrClicksAndImpressions['impressions'];
                        $total['clicksSum']      = $arrClicksAndImpressions['clicks'];
                        
						
						
						
						
						if ($set->deal_cpi==1){
						// installation
						$array = array();			
						$array['from']  	= 	$from ;
						 $array['to'] = $to;
						 $array['affiliate_id'] = $set->userInfo['id'] ;
						$array['merchant_id'] = $merchantww['id'] ;
						//$array['group_id'] = $set->userInfo['group_id'];
						 // $array['profile_id'] = $profile_id;
						 // $array['banner_id'] = $banner_id ;
						 // $array['country_id'] = $country_id ;
						$array['type'] = 'install' ;
	
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
									
									
							// var_dump($install_item);
							// echo '<Br><Br>';
							// die('--');
							unset($a);
						}
						}
						// end of install
						}
						
						
						
                        $qqry = "SELECT * FROM data_reg "
                              . "WHERE merchant_id = '" . $merchantww['id'] . "' and affiliate_id='".$set->userInfo['id'] 
                              . "' AND rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ";
                        // if($_GET['hhd']) echo ($qqry).'<br>';
                        $regqq = function_mysql_query($qqry,__FILE__);
                        $arrTierCplCountCommissionParams = [];
                        $regCom = 0;
                        while ($regww = mysql_fetch_assoc($regqq)) {
                            $sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                                 . "WHERE affiliate_id = '" . $regww['affiliate_id'] . "' "
                                 . "AND merchant_id = '" . $regww['merchant_id'] . "' AND dealType = 'tier' "
                                 . "ORDER BY id DESC "
                                 . "LIMIT 0, 1;";
                            // die($sql);
                            // $strAffDealType   = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
							
                            // $boolTierCplCount = !is_null($strAffDealType['tier_type']) && 'cpl_count' == $strAffDealType['tier_type'];
                            $boolTierCplCount = false;
                            
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
                                        'profile_id'   => $regww['profile_id']
                                    ];
									
                                    $a= getCommission($from, $to, 0, -1, $arrDealTypeDefaults, $arrTmp);
                              
									$regCom += $a;
									
								       	 if ($_GET['ddd']==1) {
											echo '1: ' . $a . '<br>';
											 echo '$totalCom: ' . $totalCom. '<br>';
								 }
								 
									$totalCom +=$a;
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
                        }
                        
					   if($_GET['com'])
					   {
						   echo " Commission after Reg - " . $regCom . "<br/>";
					   } 
						
                        // TIER CPL.
						$tierCom = 0;
                        foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
                            $a = getCommission(
                                $arrParams['from'], 
                                $arrParams['to'], 
                                $arrParams['onlyRevShare'], 
                                $arrParams['groupId'], 
                                $arrParams['arrDealTypeDefaults'], 
                                $arrParams['arrTmp']
                            );
							$tierCom += $a;
								if ($_GET['ddd']==1) {
									 echo '2: ' . $a . '<br>';
									echo '$totalCom: ' . $totalCom. '<br>';
								 }							
                            $totalCom += $a;
                            
                            unset($intAffId, $arrParams);
                        }
                        
					   if($_GET['com'])
					   {
						   echo " Commission after TIER - " . $tierCom . "<br/>";
					   } 
                        
                        $volume = 0;
                        $qqry2 = "SELECT distinct(data_sales.tranz_id),data_sales.*,data_sales.type as data_sales_type,data_sales.rdate as data_sales_rdate,data_reg.initialftddate as initialftddate FROM data_sales  as data_sales "
                               . "INNER JOIN data_reg AS data_reg ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                               . "where 1=1 
                                AND data_sales.rdate BETWEEN '".$from."' AND '".$to."'
							   and data_reg.merchant_id= " . $merchantww['id'] . " and data_reg.affiliate_id=".$set->userInfo['id'] . " AND data_sales.type<>'deposit'" . " AND data_sales.type<>'PNL'"; 
							   
					
                        $salesqq = function_mysql_query($qqry2,__FILE__);
                        $salesCom = 0;
                        while ($salesww = mysql_fetch_assoc($salesqq)) {
							
							
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
								
								// var_dump($salesww);
								// echo '<Br>';
								}
								
								
						if ($ealiestTimeForNetRev>$salesww['data_sales_rdate'])
								$ealiestTimeForNetRev = $salesww['data_sales_rdate'];
                            if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
                            if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
                            if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
                            if ($salesww['type'] == 'volume') {
                                $volume += $salesww['amount'];
                                $arrTmp = [
                                    'merchant_id'  => $salesww['merchant_id'],
                                    'affiliate_id' => $salesww['affiliate_id'],
                                    'rdate'        => $salesww['data_sales_rdate'],
                                    'banner_id'    => $salesww['banner_id'],
                                    'trader_id'    => $salesww['trader_id'],
                                    'profile_id'   => $salesww['profile_id'],
                                      'type'       => 'volume',
                                    'amount'       => $salesww['amount'],
                                ];
                                
                                $a =  getCommission(
                                    $from, 
                                    $to, 
                                    0, 
                                    (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                    $arrDealTypeDefaults, 
                                    $arrTmp
								 );
								$salesCom += $a;
								
								if ($_GET['ddd']==1) {
									 echo '3: ' . $a . '<br>';
									 echo '$totalCom: ' . $totalCom. '<br>';
								 }
								 
								$totalCom +=$a;
                            }
                        }
                        
					   if($_GET['com'])
					   {
						   echo " Commission after Sales -  " . $salesCom . "<br/>";
					   } 
                        
                        $sql = "SELECT  distinct(data_sales.tranz_id), data_sales.* , data_reg.initialftddate FROM data_sales as data_sales "
                             . "INNER JOIN data_reg AS data_reg ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                             . "WHERE data_reg.merchant_id= '" . $merchantww['id'] . "' and  data_reg.affiliate_id='".$set->userInfo['id'] 
                             . "' AND data_sales.rdate BETWEEN '".$from."' AND '".$to."' AND data_sales.type='deposit'";


		 
                        $depositsqq = function_mysql_query($sql,__FILE__);
                       
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
			
			$showDataForAffiliateSinceTB1='';
			if ($showDataForAffiliateSince>'2000-11-30 00:00:00') {
				$showDataForAffiliateSinceTB1 = ' and tb1.rdate >"'.$showDataForAffiliateSince  .'"'; 
			}
				
			
			
			
			
			$arrFtds = getTotalFtds($from, $to, $set->userInfo['id'], $merchantww['id'], $merchantww['wallet_id'],   -1,0,0,$searchInSql);

                      
                                if (!$needToSkipMerchant) {
                                    

									$ftdCom =0;
									foreach ($arrFtds as $arrFtd) {
                                        $real_ftd++;
                                        $real_ftd_amount += $arrFtd['amount'];
                                        
                                        $beforeNewFTD = $new_ftd;
                                        getFtdByDealType($ww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $new_ftd);
                                        
										// if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
											if ($beforeNewFTD != $new_ftd ) {
                                            $arrFtd['isFTD'] = true;

                                        }
                                        unset($arrFtd);
                                    }
                                }
                                
								
								if ($_GET['com'])
								echo 'Commission after ftd: ' . $ftdCom .'<Br>';
								
								
								
			
			$ftdUsers = '';

// var_dump($set);
// die();			
		  
                        if (!$needToSkipMerchant) {
                            $arrFtds = getTotalFtds($from, $to, $set->userInfo['id'], $merchantww['id'], $merchantww['wallet_id'],   -1,0,0,"",0,"",false,1);
							
						
							
                         if (!$needToSkipMerchant) {
							$ftdCom = 0;
							$new_Qftd = 0;
								/* 	var_dump($arrFtds);
									echo 'qualified com: ' . $arrFtd['trader_id'] . '    -   ' . $a .'<br>';
									die(); */
							foreach ($arrFtds as $arrFtd) {
									
									
									
                              
								$arrFtd['initialftddate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['rdate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['runningType'] = 'qualification';
                                $before_qualified_NewFTD = $new_qualified_ftd;
                                getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsersQualified, $irrelevant['amount'], $new_qualified_ftd,true);
                                if ($before_qualified_NewFTD != $new_qualified_ftd){// || count($arrFtds)==1) {
								
									$new_Qftd++;   // activeTrader
                                    $arrFtd['isFTD'] = true;
									$a = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, -1, $arrDealTypeDefaults, $arrFtd);
									
									
									
									$totalCom += $a;
									$qftdCom += $a;
                                }
                            }
							
							}
							
							
							if($_GET['com'])
						   {
							   echo " Commission after FTD " . $qftdCom . "<br/>";
						   } 
                        }
					  
			
			$strQuery              = "SELECT credit FROM affiliates WHERE id = '" . $set->userInfo['id'] . "' AND show_credit = 1 LIMIT 0, 1;";
			$arrResult             = function_mysql_query($strQuery,__FILE__);
			$strCredit             = '';
			$boolAllowToShowCredit = false;
			
			while ($arrRow = mysql_fetch_assoc($arrResult)) {
                            $strCredit             = $arrRow['credit'];
                            $boolAllowToShowCredit = true;
                            break;
			}
			
			/* if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                            $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantww['id'], $set->userInfo['id'], $arrDealTypeDefaults);
                            $intTotalRevenue  = 0;
                            $revCom = 0;
                            foreach ($arrRevenueRanges as $arrRange) {   
                                $intCurrentRevenue = getRevenue(
                                    'WHERE merchant_id = ' . $merchantww['id'] . ' AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . 
                                    '" AND affiliate_id = "' . $set->userInfo['id'] . '"',
                                    $merchantww['producttype']
                                );
                                
                                $intTotalRevenue    += $intCurrentRevenue;
                                $row                 = array();
                                $row['merchant_id']  = $merchantww['id'];
                                $row['affiliate_id'] = $set->userInfo['id'];
                                $row['banner_id']    = 0;
                                $row['rdate']        = $arrRange['from'];
                                $row['amount']       = $intCurrentRevenue;
                                $row['isFTD']        = false;
                                $a= getCommission($arrRange['from'], $arrRange['to'], 1, -1, $arrDealTypeDefaults, $row);
                                $revCom += $a;
									       	 if ($_GET['ddd']==1) {
									 echo '5: ' . $a . '<br>';
												 echo '$totalCom: ' . $totalCom. '<br>';
								 }
								 
								$totalCom           += $a;
                            }
                    
                            $netRevenue += $intTotalRevenue;
							
							   if($_GET['com'])
							   {
								   echo " Commission after Revenue - " . $revCom . "<br/>";
							   } 
							
						} else  */{
                          




							$revNewCom = 0;
							foreach($netDepositTransactions as $trans){
							$a=0;
							
							
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
								
							$netRevenue =  round(getRevenue("data_sales.rdate  BETWEEN . '".$trans[0]['rdate']."' AND '".$trans[0]['rdate']."' ",$merchantww['producttype'],$revDepAmount,$revBonAmount,$revWithAmount,0,0,0,$merchantww['rev_formula'],null,$revChBAmount),2);
							$recordDate = $trans[0]['rdate'];


							
							
								$comrow                 = array();
				           $comrow['merchant_id']  = $trans[0]['merchant_id'];
                           $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
                            $comrow['banner_id']    = 0;
                            $comrow['rdate']        = $trans[0]['rdate'];
                            $comrow['amount']       = $netRevenue;
                             $comrow['trader_id']  =  $trans[0]['trader_id'];
                            $comrow['isFTD']        = false;
                            $comrow['initialftddate']        = $trans[0]['initialftddate'];
							  
								// var_dump($comrow);
								// die();
								
								
						$com = getCommission($recordDate,$recordDate, 1, -1, $arrDealTypeDefaults, $comrow);
					
						$revNewCom += $com;

							// echo 'com : ' .$com . '         --  date:    ' . $trans[0]['rdate'].'<br>';
						

										
								 if ($_GET['ddd']>0) {
									 echo '6: ' . $a . '<br>';
														 echo '$totalCom: ' . $totalCom. '<br>';
														 // var_dump($comrow);
														 echo '<Br>';
								 }
								 
										$totalCom           += $com;
										// $totalCom        +=0;//   += $com;
										
						}
								

								
				}
					$netRevenue =  round(getRevenue(" data_sales.rdate BETWEEN '".$from."' AND '".$to."' ",$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);								
					
					


					
					   if($_GET['com'])
					   {
						   echo " Commission after Rev else  " . $revNewCom . "<br/>";
					   } 
			}
			
			
			
			if (strtolower($merchantww['producttype'])=='forex') {
			//lots 
									$sql = '
									select data_stats.* , data_reg.FTDqualificationDate , data_reg.initialftddate from 
									(SELECT turnover AS totalTurnOver,trader_id,id,merchant_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
										 . 'WHERE affiliate_id = '.$set->userInfo['id']. ' and merchant_id = "' . $merchantww['id'] . '" AND rdate BETWEEN "' . $from . '" AND "' . $to . '" and turnover>0
										 
										 ) data_stats
left join data_reg on data_stats.merchant_id = data_reg.merchant_id and  data_stats.trader_id = data_reg.trader_id
										 '
										 ;
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $lots= 0;
										$lotsCom = 0;
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											
									// die ($sql);		
											if($ts['affiliate_id']==null) {
													continue;
											}
		
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
																	'initialftddate'    => $ts['initialftddate'],
																	'FTDqualificationDate'    => $ts['FTDqualificationDate'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
												
													$a = getCommission($lotdate, $lotdate, 0, $group_id, $arrDealTypeDefaults, $row);
													
													$lotsCom += $a;
												
													       	 if ($_GET['ddd']==1) {
									 echo '7: ' . $a . '<br>';
																 echo '$totalCom: ' . $totalCom. '<br>';
								 }
								 
								 
													$totalCom += $a;
													$lots += $totalLots; 
													
											
										}
										// echo '<br>'.$totalCom . '<Br>';
							// echo ($lots);
							// echo $lots . '<br>';
											  if($_GET['com'])
										   {
											   echo " Commission after Reg " . $lotsCom . "<br/>";
										   } 
										}
					 
			if ($set->deal_pnl == 1 ) {
						
								$totalPNL  = 0;
								$dealsForAffiliate['pnl'] = 1;
									// die ('greger');
									
								$pnlRecordArray=array();
								
								$pnlRecordArray['affiliate_id']  = $set->userInfo['id'];
								$pnlRecordArray['merchant_id']  = $merchantww['id'];
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
									
								$traderStatsQ = function_mysql_query($sql,__FILE__);
								$pnlCOm = 0;
								while($ts = mysql_fetch_assoc($traderStatsQ)){
												$pnlamount = ($ts['amount']*-1);
												$row = [
													'merchant_id'  => $ts['merchant_id'],
													'affiliate_id' => $ts['affiliate_id'],
													'rdate'        => $ts['rdate'],
													'banner_id'    => $ts['banner_id'],
													'trader_id'    => $ts['trader_id'],
													'profile_id'   => $ts['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  ($showCasinoFields==1 ?  calculateCasinoRevenue($pnlamount,$ts['type']) : $pnlamount) ,
												 'initialftddate'       =>  $ts['initialftddate']
												 ];
												 
											
												 $totalPNL = $totalPNL + $pnlamount;
												
															 
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
										if ($dealsForAffiliate['pnl']>0){
											
											$tmpCom = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
											// echo 'com: ' . $tmpCom.'<br>';
												$pnlCOm += $tmpCom;
												$totalCom += $tmpCom;
										}
								}
								
								   if($_GET['com'])
								   {
									   echo " Commission after PNL " . $pnlCOm . "<br/>";
								   } 
						}
			
						
				
   //Sub Affiliate Commission
						$subcomm= 0;
						$qry = "select id from affiliates where valid = 1 AND refer_id = " . $set->userInfo['id'] . " and sub_com>0" ;
						$rsc = function_mysql_query($qry,__FILE__);
							
						$allAffiliates = "";
						
						while ($row = mysql_fetch_assoc($rsc)) {
								
								
								// $sql = "SELECT id,username FROM affiliates WHERE valid = 1 AND refer_id = " . $row['id'];
								
								// $affiliateqq = function_mysql_query($sql,__FILE__);
							 
								 $hasResults = false;
								 if ($row['id']>0)  {
   										$affiliateww = getAffiliateRow($row['id']);
										// while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {
											$comData = getSubAffiliateData($from,$to,$affiliateww['id'],$affiliateww['refer_id'],'commission','affiliate');
											$subcomm += $comData['commission'];
										}
						}
						
						
				
						
			$listMerchants .= '<tr>
                            <td style="color: #646464;"><b>'.lang($merchantww['name']).'</b></td>'.
						(allowView('af-impr',$deal,'fields') ? '
                            <td align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($total['viewsSum'] ? $total['viewsSum'] : '0').'</a></td>' : '').
                            (allowView('af-clck',$deal,'fields') ? '
							<td align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'">'.($total['clicksSum'] ? $total['clicksSum'] : '0').'</a></td>':'');
                        
						if(allowView('af-instl',$deal,'fields') && $set->deal_cpi){			
						  $listMerchants .= '<td align="center">
						  <a href="'.$set->SSLprefix.'affiliate/reports.php?act=install&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=install&merchant_id='.$merchantww['id'].'" >'.number_format($totalCPI).'</a>
						  </td>';
						}
                        if (!$hideDemoAndLeads) {
							
                            $listMerchants .=
							(allowView('af-lead',$deal,'fields') ? '
							<td align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=lead">'.number_format($total_leads,0).'</a></td>' : '' ).
							(allowView('af-demo',$deal,'fields') ? '
                                <td align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=demo">'.number_format($total_demo,0).'</a></td>':'');
                        }
                        
						
						//$productType = $merchantww['producttype'];
                        $listMerchants .= 
							(allowView('af-real',$deal,'fields') ? '
                            <td align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=real">'.number_format($total_real,0).'</a></td>' : '' ).

                            ((allowView('af-frzn',$deal,'fields') && $showCasinoFields) ? '<td align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=frozen">'.$frozens.'</a></td>' : '').
                            (allowView('af-ftd',$deal,'fields') ? '<td align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&type=ftd">'.$new_ftd.'</a></td>' : '').
                            (allowView('af-ftda',$deal,'fields') ? '<td align="center">'.price($ftd_amount['amount']).'</td>' : '').
                            (allowView('af-tftd',$deal,'fields') ? '<td align="center">'.$new_real_ftd.'</td>':'').
                            (allowView('af-tftda',$deal,'fields') ? '<td align="center">'.price($real_ftd_amount).'</td>' : '')
							                             
                            . ( !allowView('af-depo',$deal,'fields') ? '' :
                                 '<td align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to))
                                    .'&merchant_id='.$merchantww['id'].'&type=deposit">'.$totalDeposits.'</a></td>' 
                              )
							  
                            . ( !allowView('af-depoam',$deal,'fields') 
                                ? '' : '<td align="center">'.price($depositsAmount).'</td>'
                              )
                            
							. (allowView('af-trdvlu',$deal,'fields') ? 
							'<td align="center">'.price(@round($depositsAmount/$new_ftd,2)).'</td>' : '')
                            . ( !allowView('af-bns',$deal,'fields') 
                                ? '' : '<td align="center">'.price($bonus).'</td>')
                            . ( !allowView('af-withd',$deal,'fields') 
                                ? '' : '<td align="center">'.price($withdrawal).'</td>')
                            . ( !allowView('af-withd',$deal,'fields') 
                                       ? '' : '<td align="center">'.price($chargeback).'</td>'
                                )
                            .
							(allowView('af-vlm',$deal,'fields') && strtolower($productType) == 'forex' ?
                                        '<td align="center">'.price($lots).'</td>'
									
								:'')
							
                            
							
                            . (allowView('af-ntrv',$deal,'fields') ? '<td align="center">'.price($netRevenue).'</td>' : '')
							.($set->deal_pnl==1  && allowView('af-pnl',$deal,'fields')? '
							<td align="center">'.price($totalPNL).'</td> ' : '').
							( allowView('af-qftd',$deal,'fields')? '
							<td align="center">'.($new_Qftd).'</td> ' : '').
                            '<td align="center">'.price($totalCom).'</td>
                        </tr>';
                    
                        
                        $mviewsSum+=$total['viewsSum'];
			$mclicksSum+=$total['clicksSum'];
			
			$mInstallSum += $totalCPI; 
			$mtotalLeads+=$total_leads;
			$mtotalDemo+=$total_demo;
			$mtotalReal+=$total_real;
			$mnewFTD+=$new_ftd;
			$mnewQFTD+=$new_Qftd;
			$mtotal_deposits+=$totalDeposits;
			$mtotal_depositsAmount+=$depositsAmount;
			$mftdAmount+=$ftd_amount['amount'];
			$mtotalBonus+=$bonus;
			$mtotalWithdrawal+=$withdrawal;
			$mtotalChargeBack+=$chargeback;
			$mtotalLots+=$lots;
			$mtotalPnl+=$totalPNL;
			$mtotalComs+=$totalCom;
			$mtotalRealFtd+=$new_real_ftd;
			$mtotalRealFtdAmount+=$real_ftd_amount;
                        
			if ($frozens) {
                            $mtotalFrozens += $frozens;
                        }
                        
			// if ($revrslt['amount'] > 0 || !$set->hideNetRevenueForNonRevDeals) { 
                            $mtotalNetRevenue += $netRevenue;
                        // }
			
			
			$viewsSum+=$total['viewsSum'];
			$clicksSum+=$total['clicksSum'];
			$totalLeads+=$total_leads;
			$totalDemo+=$total_demo;
			$totalReal+=$total_real;
			$newFTD+=$new_ftd;
			$newQFTD+=$new_Qftd;
			$total_deposits+=$totalDeposits;
			$total_depositsAmount+=$depositsAmount;
			$ftdAmount+=$ftd_amount['amount'];
			$totalBonus+=$bonus;
			$totalWithdrawal+=$withdrawal;
			$totalChargeBack+=$chargeback;
			$totalLots+=$lots;
			$totalTotalPnl+=$totalPNL;
			    
			$totalComs+=$totalCom;
			$totalRealFtd+=$new_real_ftd;
			$totalRealFtdAmount+=$real_ftd_amount;
                        
			if ($frozens) {
                            $totalFrozens += $frozens;
                        }
                        
			// if ($revrslt['amount'] > 0 || !$set->hideNetRevenueForNonRevDeals) { 
                            $totalNetRevenue += $netRevenue;
                        // }
                        
                    // Mark given wallet as processed.
                    $arrWallets[$merchantww['wallet_id']] = true;
					
					
					
					
					
					
				

	$listMerchantsHeader = '<table class="tablesorter" width="100%" border="0" cellpadding="4" cellspacing="0">
						<thead>
							<tr>
								
								<th width="8%">'.lang('Merchant').'</th>
								'.(allowView('af-impr',$deal,'fields') ? '
								<th align="center">'.lang('Impression').'</th>' : '').
								(allowView('af-clck',$deal,'fields') ? '
								<th align="center">'.lang('Clicks').'</th>' :'');
								
                if(allowView('af-instl',$deal,'fields') && $set->deal_cpi){			
				  $listMerchantsHeader .= '<th align="center">'.lang('Install').'</th>';
				}
                if (!$hideDemoAndLeads) {	
                    $listMerchantsHeader .=
											(allowView('af-lead',$deal,'fields') ? 
											'<th align="center">'.lang('Leads').'</th>' : '') .
											(allowView('af-demo',$deal,'fields') ? 
											'<th align="center">'.lang(ptitle('Demo')).'</th>' : '') ;
                }
								
							/* 	if ($_GET['ssss']==1){
								echo 'ftda : ' . (allowView('af-ftda',$deal,'fields',true));
								die();
								} */
								
                $listMerchantsHeader .='
                                        '.(allowView('af-real',$deal,'fields') ? '
										<th align="center">'.lang(ptitle('Real Accounts')).'</th>' : '').'
										
										
                                        '.(allowView('af-frzn',$deal,'fields') && ($showCasinoFields ) ? '<th align="center">'.lang('Frozens').'</th>' : '').'
                                         '.(allowView('af-ftd',$deal,'fields') ? '<th align="center">'.lang('FTD').'</th>' : '').'
                                        '.(allowView('af-ftda',$deal,'fields') ? '<th align="center">'.lang('FTD Amount').'</th>' : '').'
                                        '.(allowView('af-tftd',$deal,'fields') ? '<th align="center">'.lang('RAW FTD').'</th>' :'').'
                                        '.(allowView('af-tftda',$deal,'fields') ? '<th align="center">'.lang('RAW FTD amount').'</th>' :'').''
                                        . (!allowView('af-depo',$deal,'fields') 
                                            ? '' : '<th align="center">'.lang('Deposits').'</th>'
                                            )
                                        . (!allowView('af-depoam',$deal,'fields') 
                                            ? '' : '<th align="center">'.lang('Deposit Amount').'</th>'
                                            )
                                        .(allowView('af-trdvlu',$deal,'fields') ? 
											'<th align="center">'.lang(ptitle('Trader Value')).'</th>' : '')
                                        . (!allowView('af-bns',$deal,'fields') 
                                            ? '' : '<th align="center">'.lang('Bonus').'</th>'
                                            )
                                        . (!allowView('af-withd',$deal,'fields')
                                            ? '' : '<th align="center">'.lang('Withdrawal').'</th>')
											
											. (!allowView('af-chrgb',$deal,'fields') ?  '' :
                                                 '<th align="center">'.lang('ChargeBack').'</th>')
										.
										(allowView('af-vlm',$deal,'fields') && strtolower($productType)=='forex' ?
											'<th align="center">'.lang('Lots').'</th>'
                                        : '' )
										
                                        .(allowView('af-ntrv',$deal,'fields') ? '<th align="center">'.lang(ptitle('Net Deposit')).'</th>' : '')
										.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')? '
										<th>'.lang(ptitle('PNL')).'</th> ' : '')
										.(allowView('af-qftd',$deal,'fields')? '
										<th>'.lang(ptitle('Active Traders')).'</th> ' : '')
                                        .'<th align="center">'.lang('Commission').'</th>
                                </tr>
                        </thead>
                        <tbody>';
						
	
						
			$listMerchants = str_replace('|HEADER|',$listMerchantsHeader,$listMerchants);		
					
					
					
		if ($merchantww['producttype'] != $currentType) {
						
						
			
						
						
						
					}
					
		$productType = strtolower($merchantww['producttype']);
		}
		// List Merchants ends here.
		
					
					
					
                
		
		
		
		
		$set->content .='  <link rel="stylesheet" href="'.$set->SSLprefix.'js/unslider/dist/css/unslider.css">
			<script src="'.$set->SSLprefix.'js/unslider/dist/js/unslider.js"></script>';
	
		
		$set->content .= ($noMerchants==0 && ($set->ShowGraphOnDashBoards==1 ||  $set->ShowGraphOnDashBoards== null ) ? 
		'		
		<div class="my-slider" style="display:none">
			<div style="float:right;margin-right:10px;margin-top:5px;position:absolute;left:95%;z-index:99999;" class="refresh1"><a class="refresha tooltip" onclick="refreshChart2();" style="font-size: 11px; cursor: pointer;display:none" >'. ($set->chartTheme=='dark_unica'?'<img src=\''.$set->SSLprefix.'images/refresh_white.png\' width=20 />':'<img src=\''.$set->SSLprefix.'images/refresh_black.png\' width=30 />') . '</a></div>
			<ul>
				<li data-slide="0" data-name="Performance Chart" class="unslider-active">  '.highchart('0','affiliate',$set->userInfo['id']) .'</li>
				<li data-slide="1" data-name="Conversion Chart" >'. conversionHighchart('0','affiliate',$set->userInfo['id']).'</li>
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
' :'') ;



if ($noMerchants ==0){
$set->content .=
'
<div class="normalTableTitle" >'.lang('Merchants Performance').'<span class="imgGear" style="float:right;"><img class="imgReportFieldsSettings_dashstat" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
		
			<div class="dashStat mainDashStat">
			<table width="100%" border="0" cellpadding="4" cellspacing="5" class="dashStatFields"><tr>
				'. (allowView('af-impr',$deal,'fields') ? '			
				<td class="dashStat '.lang('Impressions').'">
					'.lang('Impressions').'<br />
					<span style="font-size: 18px; font-weight: bold;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($viewsSum ? number_format($viewsSum) : '0').'</a></span>
				</td> 
				' : '' ).'
				
				'. (allowView('af-clck',$deal,'fields') ? '			
				<td class="dashStat '.lang('Clicks').'">
					'.lang('Clicks').'<br />
					<span style="font-size: 18px; font-weight: bold;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.($clicksSum ? number_format($clicksSum) : '0').'</a></span>
				</td>': '');
		
				if(allowView('af-instl',$deal,'fields') && $set->deal_cpi){			
				  $set->content .='<td class="dashStat '.lang('Install').'">
					'.lang('Install').'<br />
					<a href="'.$set->SSLprefix.'affiliate/reports.php?act=install&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=install" style="font-size: 18px; font-weight: bold;">'.number_format($mInstallSum).'</a>
					
				</td>';
				}

                if (!$hideDemoAndLeads) {
					
                    $set->content .='
					'. (allowView('af-lead',$deal,'fields') ? '			
                        <td class="dashStat  '.lang('Leads').'">
                                '.lang('Leads').'<br />
                                <a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead" style="font-size: 18px; font-weight: bold;">'.number_format($totalLeads+$marketTotal_leads).'</a>
                        </td> ' : '' ).'
						'. (allowView('af-demo',$deal,'fields') ? '			
                        <td class="dashStat '.lang(ptitle('Demo')).'">
                                '.lang(ptitle('Demo')).'<br />
                                <a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo" style="font-size: 18px; font-weight: bold;">'.number_format($totalDemo+$marketTotal_demo).'</a>
                        </td>' : '' );
                }
                
                $set->content .='
				'. (allowView('af-real',$deal,'fields') ? '			
				<td class="dashStat '.lang(ptitle('Real Account')).'">
					'.lang(ptitle('Real Account')).'<br />
					<a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real" style="font-size: 18px; font-weight: bold;">'.number_format($totalReal+$marketTotal_real).'</a>
				</td> ' : '' ).'
				'.($showCasinoFields  && allowView('af-frzn',$deal,'fields') ? '			
				<td class="dashStat '.lang(ptitle('Frozens')).'">
					'.lang(ptitle('Frozens')).'<br />
					<a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=frozen" style="font-size: 18px; font-weight: bold;">'.number_format($totalFrozens).'</a>
				</td>':'') .'
				'. (allowView('af-ftd',$deal,'fields') ? '			
				<td class="dashStat '.lang('FTD').'">
					'.lang('FTD').'<br />
					<a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd" style="font-size: 18px; font-weight: bold;">'.number_format($newFTD+$marketTotal_FTDs,0).'</a>
				</td>' : '' );
				
		
			
			if($displayFtdAmountTotal){
				
				$set->content .='
				'. (allowView('af-ftda',$deal,'fields') ? '			
				<td class="dashStat '.lang('FTD Amount').'">'
					.lang('FTD Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($ftdAmount+$marketTotal_FTDAmount,2)."</span>
				</td>" : "");
				//$displayFtdAmount=0;
			}
			
				
				$set->content .= 
				
				 (allowView('af-tftd',$deal,'fields') ? '<td class="dashStat '.lang('RAW FTD').'">'.lang('RAW FTD').'<br /><span style="font-size: 18px; font-weight: bold;">'.$totalRealFtd.'</span></td>':'').
				 (allowView('af-tftda',$deal,'fields') ? '<td class="dashStat '.lang('RAW FTD Amount').'">'.lang('RAW FTD Amount').'<br /><span style="font-size: 18px; font-weight: bold;">'.price($totalRealFtdAmount).'</span></td>' : '')
                                    
                                
				.( !allowView('af-depo',$deal,'fields') ? 		
				
				
                                  '' : '<td class="dashStat '.lang('Deposits').'">'.lang('Deposits').'<br />
					<a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to))
                                        .'&type=deposit" style="font-size: 18px; font-weight: bold;">'.number_format($total_deposits,0).'</a></td>' 
                                    )
				
				.( !allowView('af-depoam',$deal,'fields') ?	 '' : '<td class="dashStat '.lang('Deposits Amount').'">
					'.lang('Deposits Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($total_depositsAmount+$marketTotal_depositAmount,2).'</span>
				</td>')
				.( !allowView('af-bns',$deal,'fields') ? 		 '' : '<td class="dashStat '.lang('Bonus').'">
					'.lang('Bonus').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalBonus+$marketTotal_Bonus,2).'</span>
				</td>')
				.( !allowView('af-withd',$deal,'fields') ? 		 '' : '<td class="dashStat '.lang('Withdrawal').'">
					'.lang('Withdrawal').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalWithdrawal+$marketTotal_withdrawal,2).'</span>
				</td>')
				
				.( !allowView('af-chrgb',$deal,'fields') ? 		 '' : '<td class="dashStat '.lang('ChargeBack').'">
					'.lang('ChargeBack').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalChargeback,2).'</span>
				</td>')
                
				
				
                                    
				. ((!$boolAllowToShowCredit  || !allowView('af-crdt',$deal,'fields')) ? '' :
							'<td class="dashStat ' . lang('Credit') . '">
								' . lang('Credit') . '<br />
								<span style="font-size: 18px; font-weight: bold;">' . price($strCredit, 2) . '</span></td>' );				
				
			
			
			if (allowView('af-ntrv',$deal,'fields')){
					$set->content .='<td class="dashStat '.lang(ptitle('Net Revenue')).'">
					'.lang(ptitle('Net Revenue')).'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalNetRevenue,2).'</span>
					</td>';
			}
				
				if($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')){
					$set->content .='<td class="dashStat '.lang(ptitle('PNL')).'">
					'.lang(ptitle('PNL')).'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalTotalPnl,2).'</span>
					</td>';
				}
				if(allowView('af-qftd',$deal,'fields')){
					
					$set->content .='<td class="dashStat '.lang(ptitle('Active Traders')).'">'.lang(ptitle('Active Traders')).'<br />
					<a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to))
                                        .'&type=activeTrader" style="font-size: 18px; font-weight: bold;">'.number_format($newQFTD,0).'</a></td>' ;
				}
				
				$set->content .='<td class="dashStat '.lang('Commission').'">
					'.lang('Commission').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalComs,2).'</span>
				</td>' ;
			
			$set->content .='</tr></table>
			</div>
			<div class="space">&nbsp;</div>';
}


			$set->content .= '
					
					<script>
	$(document).ready(function(){
	$(".inline").colorbox({iframe:true,border: "1px black solid" ,height: "95%", width:"95%",fixed:true});

	});
	</script>
	';

	
		$set->sortTable = 1;
		$set->noFilter = 1;
		//modal
		$fields = getReportsHiddenCols("dashStatCols","affiliate",$set->userInfo['id']);
		if($fields){
			$set->DashboardDashStatHiddenCols = $fields;
		}
	
		
		
			
		if($displayRevTotal)
			$listMerchants = str_replace('<td>REV</td>','<td align="center"></td>',$listMerchants);
		else
			$listMerchants = str_replace('<td>REV</td>','',$listMerchants);
		
		if($displayFtdAmountTotal)
			$listMerchants = str_replace('<td>CPA</td>','<td align="center"></td>',$listMerchants);
		else
			$listMerchants = str_replace('<td>CPA</td>','',$listMerchants);
		
		
	
		
        $set->sortTableScript = 1;
        
                
		if (mysql_num_rows($merchantqq) > $showInnerTableForMerchantMoreThan) {
                    $deal = $brandsDealsArray[$brandsCounter];
					
					
					$set->content .= $listMerchants.'</tbody><tfoot id="seco">
                        <tr>
                            <th><b>'.lang('Total').':</b></th>
                            '.(allowView('af-impr',$deal,'fields') ?'
							<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.$mviewsSum.'</a></th>
							' : '' ) .'
                            '.(allowView('af-clck',$deal,'fields') ? '
                            <th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=banner&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'">'.$mclicksSum.'</a></th>
							' : '' ) ;
                    if(allowView('af-instl',$deal,'fields') && $set->deal_cpi){
						$set->content .= '
                            <th align="center">'.$mInstallSum.'</th>
							';
					}
                    if (!$hideDemoAndLeads) {
                        $set->content .='
						'.(allowView('af-lead',$deal,'fields') ?'<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.number_format($mtotalLeads,0).'</a></th>':'').'
                        '.(allowView('af-demo',$deal,'fields') ?'<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.number_format($mtotalDemo,0).'</a></th>' : '');
                    }
                    
                    $set->content .='
                        '.(allowView('af-real',$deal,'fields') ?'
						<th align="center"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.number_format($mtotalReal,0).'</a></th>' :'').'
                      
					    '.(
						(allowView('af-frzn',$deal,'fields') && $showCasinoFields) 
						? '
						<th>
							<a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='
							.date("d/m/Y", strtotime($from))
							.'&to='.date("d/m/Y", strtotime($to))
							.'&merchant_id='.$merchantww['id']
							.'&type=frozen">'.$mtotalFrozens 
						
							.'</a>
							</th>
						
						' : '' ).'
						
					  
                        '.(allowView('af-ftd',$deal,'fields') ? '<th align="center" id="ftd">'.number_format($mnewFTD,0).'</th>' : '').'
                        '.(allowView('af-ftda',$deal,'fields') ? '<th align="center" id="ftda">'.price($mftdAmount).'</th>' : '').'
                        
						
						'.(allowView('af-tftd',$deal,'fields') ? '<th align="center" id="tftd-f2">'.$mtotalRealFtd.'</th>' : '').'
                        '.(allowView('af-tftda',$deal,'fields') ? '<th align="center" id="tftda">'.price($mtotalRealFtdAmount).'</th>' : '')
                        
						.(!allowView('af-depo',$deal,'fields') 
                            ? '' : '<th align="center" id="depo"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.$mtotal_deposits.'</a></th>'
                           )
                        .(!allowView('af-depoam',$deal,'fields') 
                            ? '' : '<th align="center" id="depoam">'.price($mtotal_depositsAmount).'</th>'
                           )
						   
						   .(allowView('af-trdvlu',$deal,'fields') ?  		
                        '<th align="center">'.price(@round($mtotal_depositsAmount/$mnewFTD,2)).'</th>' 
						   :''  
						)
						
						
                        .( !allowView('af-bns',$deal,'fields') 
                            ? '' : '<th align="center">'.price($mtotalBonus).'</th>'
                           )
                        .( !allowView('af-withd',$deal,'fields') 
                            ? '' : '<th align="center">'.price($mtotalWithdrawal).'</th>')
							
							.(!allowView('af-chrgb',$deal,'fields') 
                            ? '' : '<th align="center">'.price($mtotalChargeback).'</th>')
							
							.(allowView('af-vlm',$deal,'fields') && strtolower($productType)=='forex' 
                            ? '<th align="center">'.price($mtotalLots).'</th>':'')
														
						.
						(allowView('af-ntrv',$deal,'fields') ? 		
                        '<th align="center">'.price($mtotalNetRevenue).'</th>' : '')
						.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')? '
							<th>'.price($mtotalPnl).'</th> ' : '')
						.(allowView('af-qftd',$deal,'fields')? '
							<th align="center">'.($mnewQFTD).'</th> ' : '')
                        .'<th align="center">'.price($mtotalComs).'</th>
                    </tr>
                     </tfoot></table>' . getPager() . '
                    <div class="space">&nbsp;</div>';
                }
		
		}
		// echo 'depom: ' . allowView('af-depoam',$deal,'fields').'<br>';
		// echo 'depom: ' . allowView('af-tdtd',$deal,'fields').'<br>';
		// die();
		
		$groupInfo = dbGet($set->userInfo['group_id'],"groups");
		
		$adminInfo = mysql_fetch_assoc(function_mysql_query("SELECT * FROM admins WHERE valid='1' AND group_id='".($groupInfo['id'] ? $groupInfo['id'] : '0')."' AND id > '1'",__FILE__));
	
		
		$bannersCount =0;
		
		if(!isset($_SESSION['messages_popup'])){
		//POPUP Messages
		$msgsPopup = '
		<style>
		.fancybox-skin{
			padding:6px !important;
		}
		.fancybox-outer{
			background-color:white;
		}
		</style>
		<script type="text/javascript">
		var msgs = [],msgcnt=0;
		$.get("'.$set->SSLprefix.'ajax/LoadAffiliateMessages.php?affiliate_id='. $set->userInfo['id'] .'&status_id='. $set->userInfo['status_id'] .'&group_id='. $set->userInfo['group_id'] .'", function(res) {
				try {
					res = JSON.parse(res);
					res = res.success;
					msgs = res;
					
					loadMessagePopup(0);
					
				} catch (error) {
					console.log(error);
				}
			});
			
			function loadMessagePopup(cnt){
				 $.fancybox({ 
						 closeBtn:false, 
						  minWidth:"600", 
						  minHeight:"350", 
						  autoCenter: true, 
						  modal:true,
						  overlayShow:true,
						  helpers : { 
							   overlay: {
								css: {\'background-color\': \'rgba(0, 0, 0, 0.5)\'} // or your preferred hex color value
							   } // overlay 
						  }, // helpers
						  afterClose:function(){
							 
						  },			  
						  content: "<div align=\'center\'><div id=\'alert1\'  style=\'margin-top:10px\'><h3>" + msgs[cnt].title +"</h3><h5 style=\'height:80px;padding:0 5px;text-align:left\'>" + msgs[cnt].text +"</h5>
						  <div style=\'position: absolute;bottom:5px;font-size:11px;padding-top:5px;\'><input id=\'approval\' type=\'checkbox\' name=\'approval\'>'. lang('Got it, please dont show this popup anymore') .'</div>
						  </div><div style=\'    bottom: 10px;    position: absolute;    left: 44%;margin-top:10px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Ok').'\' onClick=\'changeApproveFlag("+ msgs[cnt].id +")\'></div></div>"
				  });
			}
			
			function changeApproveFlag(msgId){
					if($("#approval").is(":checked"))
						flag =1;
					else
						flag =0;
					if(flag==1){
						$.get("'.$set->SSLprefix. 'affiliate/index.php?act=approveMessage&msg_id="+msgId+"&flag="+flag, function(res) {
							msgcnt++;
							if(typeof msgs[msgcnt] == "undefined"){
								$.fancybox.close();
								return;
							}
							loadMessagePopup(msgcnt);
						});
					}
					else{
						msgcnt++;
						if(typeof msgs[msgcnt] == "undefined"){
							$.fancybox.close();
							return;
						}
					 loadMessagePopup(msgcnt);
					}
			}
			
		</script>';
		$_SESSION['messages_popup'] = true; //just to load messages once per session
		}
		
		$topCreativesCount=0;
		$sql = "SELECT * FROM affiliates_msgs WHERE valid='1' AND status_id='".$set->userInfo['status_id']."' AND ((affiliate_id='".$set->userInfo['id']."' OR affiliate_id='0') AND (group_id='0' OR group_id='".$set->userInfo['group_id']."') )";
		$msgqq=function_mysql_query($sql,__FILE__);
		while ($msgww=mysql_fetch_assoc($msgqq)) {

			$msgTitle = messagesSearchNReplace($adminInfo,strip_tags($msgww['title']));
			
			
			$listMessages .= '<tr class="trLine">
					<td>'.date("d/m/Y", strtotime($msgww['rdate'])).'</td>
					<td align="left"><a href="'.$set->SSLprefix.'affiliate/messages.php?id='.$msgww['id'].'">'.charLimit($msgTitle,100).'</a></td>
				</tr>';
			}



	if($set->showProductsPlace == 1 && !empty($set->userInfo['products']) && !empty($products_list)){
		
		
		if (empty($products_list))
			$products_list= 0;
		
		//check if all products are valid

		$valid_prod = 0;
		$sql = "select * from products_items where valid !=0 and id IN (" . $products_list .  ")";
		$pqq = mysql_query($sql);
		$arrDBValidPrd = array();
		while ($ww = mysql_fetch_assoc($pqq))
		{

					$arrDealTypeDefaults = getProductsDealTypeDefaults($ww['id']);
					$fromDate = $from;
                    $toDate   = $to;
                    
                    $l++;
                    $ftdUsers = '';
                    $netRevenue = 0;
                    $totalCom=0;
                    $ftd=0;
                    $cpi=0;
                    $totalLeads = 0;
                    $totalDemo = 0;
                    $totalReal = 0;
                    $totalInstallation = 0;
                    $ftd_amount['amount']=0;
                    $real_ftd = 0;
                    $real_ftd_amount = 0;
                    $bonus = 0;
                    $withdrawal = 0;
                    $chargeback = 0;
                    $depositingAccounts = 0;
                    $sumDeposits = 0;
                    $totalLots = 0;
                    $volume = 0;
                    
						{
                            $searchInSql = " BETWEEN '" . $from . "' AND '" . $to . "' ";
                            
                            $formula  = $ww['rev_formula'];
                            $fromDate = $from;
                            $toDate   = $to;
                            
                            $ftdUsers = '';
                            $netRevenue = 0;
                            $totalCom=0;
							$totalComs=0;
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
                            $withdrawal = 0;
                            $chargeback = 0;
                            $depositingAccounts = 0;
                            $sumDeposits = 0;
                            $volume = 0;
							$depositsAmount=0;
                            $merchantName = strtolower($ww['name']);
                            
                            
                            
                            $totalTraffic = [];
                            $arrClicksAndImpressions = getClicksAndImpressions(
                                $from, 
                                $to, 
                                $ww['id'], 
                                $set->userInfo['id'], 
                                (isset($group_id) && $group_id != '' ? $group_id : null)
								,null,  null, null, null,true)
                            ;
							
                            $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                            
					
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE affiliate_id = ".$set->userInfo['id']." and product_id = '" . $ww['id'] . "' AND "
                                    . " rdate " . $searchInSql 
                                    . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                
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
											$cpi++;
											
											
										
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
                            
                                $strSql = "SELECT *, data_sales.type AS data_sales_type  FROM data_sales AS data_sales "
                                        . "INNER JOIN data_reg AS data_reg ON data_sales.product_id = data_reg.product_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                        . "WHERE data_sales.type='deposit' and data_sales.product_id = '" . $ww['id'] . "' AND data_sales.rdate "
                                        . $searchInSql . " "
                                        . (isset($group_id) && $group_id != '' ? ' AND data_sales.group_id = ' . $group_id . ' ' : '')
                                        .  ' AND data_sales.affiliate_id = ' . $set->userInfo['id'];
                                
								$salesqq = function_mysql_query($strSql,__FILE__);
								$netDepositTransactions = array();
								while ($salesww = mysql_fetch_assoc($salesqq)) {
                                    //if ($salesww['type'] == 'deposit') { // OLD.
									
										$tranrow['id'] = $salesww['id'];
										$tranrow['affiliate_id'] = $salesww['affiliate_id'];
										$tranrow['tranz_id'] = $salesww['tranz_id'];
										$tranrow['trader_id'] = $salesww['trader_id'];
										$tranrow['product_id'] = $salesww['product_id'];
										$tranrow['rdate'] =$salesww['rdate'];
										$tranrow['amount'] = $salesww['amount'];
										$tranrow['type'] = $salesww['data_sales_type'];
										$tranrow['status'] = $salesww['status'];
										$netDepositTransactions[] = array($tranrow);
                                        $sumDeposits += $salesww['amount'];
										$depositsAmount+=$salesww['amount'];
                                        $depositingAccounts++;
                                    
									} 
									
                               
                                
                                $arrFtds  = getTotalFtds(
                                    $arrRange['from'], 
                                    $arrRange['to'], 
                                    $set->userInfo['id'], 
                                    $ww['id'], 
                                    0, 
                                    (isset($group_id) && $group_id != '' ? $group_id : 0), 
                                    0, 
                                    0,
                                    $searchInSql,
									0,"",
									true,
									1
                                );
                                
                                
                                    if ($_GET['nir']==1){
								echo '2<br>';
								var_dump($arrFtds);
								die();
							}
						
									
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
								
									
                            
                                
                   
						
				$filterFrom = $arrRange['from'];
				$filterTo   = $arrRange['to'];
				$boxaName   = "admin-prd-dashboard-report-1";
                    
				
				
				/* if($totalTraffic['totalClicks'] >0 || $totalTraffic['totalViews'] > 0
								|| $totalLeads > 0 || $totalReal > 0 || $ftd >0  
				) */ {
				 
				$tableArr = array(
						
				
					(object) array(
					  'id' => 'id',
					  'str' => '<td style="text-align: left;">'.$ww['id'].'</td>'
					),(object) array(
					  'id' => 'name',
					  'str' => '<td style="text-align: left;">'.$ww['title'].'</td>'
					),
					(object) array(
					  'id' => 'totalViews',
					  'str' => '<td style="text-align: center;"><!--a href="'.$set->SSLprefix.'affiliate/product_reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'"-->'.@number_format($totalTraffic['totalViews'],0).'<!--/a--></td>'
					),
					(object) array(
					  'id' => 'totalClicks',
					  'str' => '<td style="text-align: center;"><!--a href="'.$set->SSLprefix.'affiliate/product_reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'"-->'.@number_format($totalTraffic['totalClicks'],0).'<!--/a--></td>'
					));
					
					  if(allowView('af-instl',$deal,'fields') && $set->deal_cpi){
						 array_push($tableArr,
						(object) array(
							  'id' => 'totalCPI',
							  'str' => '<td style="text-align: center;">'.@number_format($cpi,0).'</td>'
							)
						);
					}
					 array_push($tableArr,
					(object) array(
					  'id' => 'total_leads',
					  'str' => '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&product_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>'
					),
			
					(object) array(
					  'id' => 'total_real',
					  'str' => '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&product_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>'
					),
					
					(object) array(
					  'id' => 'ftd',
					  'str' => '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&product_id='.$ww['id'].'&type=sale">'.$ftd.'</a></td>'
					),
				
					(object) array(
					  'id' => 'Commission',
					  'str' => '<td style="text-align: center;">'.price($totalCom).'</td>'
					)				,
					(object) array(
					  'id' => 'click_to_signup',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'commission_traffic',
					  'str' => '<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>'
					)
				);
				
				$listReport .= '<tr>'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'</tr>';
				
				
				
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalLeadsAccounts += $totalLeads;
				
				$totalRealAccounts += $totalReal;
				$totalFTD += $ftd;
				$totalCPIP += $cpi;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount['amount'];
				$totalDepositAmount += $sumDeposits;
				$totalTotalInstallation += $totalInstallation;
				$totalComs += $totalCom;
				
				}
                                
                            unset($arrRange); // Clear up the memory.
			} // End of time-periods loop.
                        
                    // Mark given wallet as processed.
                    
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
			));
			
			  if(allowView('af-instl',$deal,'fields') && $set->deal_cpi){
				 array_push($tableArr,
				(object) array(
				  'id' => 'totalCPI',
				  'str' => '<th>'.lang('Installation').'</th>'
				)
				);
			}
			 array_push($tableArr,
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th>'.lang(ptitle('Lead')).'</th>'
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th>'.lang(ptitle('Signups')).'</th>'
			),
			
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th>'.lang('Sale').'</th>'
			),
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.lang('Commission').'</th>'
			)				,
			(object) array(
			  'id' => 'click_to_signup',
			  'str' => '<th>'.lang(ptitle('Click to Signup')).'</th>'
			),
			(object) array(
			  'id' => 'commission_traffic',
			  'str' => '<th>'.lang(ptitle('Click to Sale')).'</th>'
			)
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
			  'str' => '<th><!--a href="'.$set->SSLprefix.'affiliate/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'"-->'.$totalImpressions.'<!--/a--></th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th><!--a href="'.$set->SSLprefix.'affiliate/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'"-->'.$totalClicks.'<!--/a--></th>'
			));
			
			  if(allowView('af-instl',$deal,'fields') && $set->deal_cpi){
				 array_push($tableArr2,
					(object) array(
					  'id' => 'totalCPI',
					  'str' => '<th>'.$totalCPIP.'</th>'
					)
				);
			}
		 array_push($tableArr2,
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th><!--a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead"-->'.$totalLeadsAccounts.'<!--/a--></th>'
			),
		
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th><!--a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real"-->'.$totalRealAccounts.'<!--/a--></th>'
			),

			(object) array(
			  'id' => 'ftd',
			  'str' => '<th><!--a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd"-->'.$totalFTD.'<!--/a--></th>'
			),
			
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.price($totalComs).'</th>'
			)		,		
			(object) array(
			  'id' => 'click_to_signup',
			  'str' => '<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'commission_traffic',
			  'str' => '<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>'
			)
		);
		
		$set->rowsNumberAfterSearch = 100;
		$set->sortTable = 1;
		$set->noFilter = 1;
		$set->content .= '
		<div class="normalTableTitle" >'.lang('Products Place').(empty($listReport)?'':'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span>').'</div>
		<div style="background: #F8F8F8;">';
		if (empty($listReport)){
				$set->content .= '<div class="productAffDash" style="text-align:center;padding-bottom:30px;padding-top:10px;font-size:14px;">'.lang('No data found for this timeframe').'</div>';
		}
		else {
				
			$tableStr = '
			<table  width="100%"  border="0" cellpadding="0" cellspacing="0" class="tablesorter mdlReportFields_prd">
				<thead><tr>
					'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').
				'</tr></thead><tfoot><tr>
					'.setTable($tableArr2, $boxaName, $set->userInfo['productType'], '').'
					
				</tr></tfoot>
				<tbody>
				'.$listReport.'
			</table>';
			$set->content .= $tableStr ." <br/>";
		}
		}
	
				
	$set->content .= getImageGrowerScript();
	$pic = $adminInfo['bigPic'];
	// var_dump($pic);
	// die();
	// $pic = substr($adminInfo['bigPic'],3);
	

		$set->content .= '<table width="100%" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td width="49%" align="center" valign="top">
								<div class="specialTableTitle">'.($set->introducingBrokerInterface ? lang('Your Introducing Broker') :  lang('Your Account Manager') ).'</div>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2" height="10"></td>
									</tr><tr>
										'.(file_exists($pic) ? '<td width="120" align="left" valign="top"><img width="150px" height="150px" border="0" src="'.$pic.'" alt="" style="border: 1px #DDDDDD solid;margin-right:10px" /></td>' : '').'
										<td align="left" valign="top" style="font-family: Arial; line-height: 22px;">
											<b>'.lang('Name').':</b> '.$adminInfo['first_name'].' '.$adminInfo['last_name'].'<br />
											<b>'.lang('E-Mail').':</b> <a href="mailto:'.$adminInfo['email'].'">'.$adminInfo['email'].'</a><br />
											<b>'.lang('Skype').':</b> <a href="skype:'.$adminInfo['IMUser'].'?call">'.$adminInfo['IMUser'].'</a><br />
											'.(!isset($set->showDeskNameOnAffiliateDashboard) || $set->showDeskNameOnAffiliateDashboard==0 ? '' : '<b>'.lang('Desk').':</b> '.($groupInfo['title'] ? $groupInfo['title'] : lang('General')).'<br />').'
											<a href="'.$set->SSLprefix.'affiliate/tickets.php?act=new">'.lang('You have a question? Click Here').'</a><br />
											'.($adminInfo['showAdditionalLink']==1 ? ' <b>'.$adminInfo['additionalLinkText'].':</b> <a target="_blank" href="'. $adminInfo['additionalLinkUrl'] . '">'.$adminInfo['additionalLinkUrl'].'</a><br />' : '' ).'
										</td>
									</tr>
								</table>
								<br />
								<table style="width:100%">
								<tr>
								
								
								<td>
									<div class="specialTableTitle">'.lang('Your Commission').'</div>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2" height="10"></td>
									</tr><tr>
										<td align="left" valign="top" style="font-family: Arial; line-height: 22px;">
											<a href="'.$set->SSLprefix.'affiliate/account.php?act=commission" target="_blank">'.(lang('Click here to review your commission structure')).'</a><br />
										</td>
									</tr>
								</table>
							</td>
								' . (!$set->hideSubAffiliation?(strpos(' '.$set->menuToHide,'subaffiliates')<1 ? '
								<td>
								<div class="specialTableTitle">'.lang('Your Affiliate Link').'  ('.lang('Sub Affiliates').')</div>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2" height="10"></td>
									</tr><tr>
										<td align="left" valign="top" style="font-family: Arial; line-height: 22px;">
											<b>'.lang('URL').':</b> <a href="'.$set->webAddress.'?ctag=a'.$set->userInfo['id'].'-b0-p" target="_blank">'.$set->webAddress.'?ctag=a'.$set->userInfo['id'].'-b0-p</a><br />
										</td>
									</tr>
								</table>
								</td>
								
								' : '' ):'') . '
							</tr>
							</table>
								<br />
								
								
								<div class="specialTableTitle">'.lang('Alerts & Announcements').'</div>
								<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
									<thead>
									<tr>
										<td width="120">'.lang('Date').'</td>
										<td style="text-align: left;">'.lang('Message').'</td>
									</tr>
									</thead>
									<tfoot>
									'.$listMessages.'
									</tfoot>
								</table>
							</td>
							<td width="2%"></td>
							<td width="49%" align="center" valign="top">
								'.($noMerchants!=1 ? '
								<div class="specialTableTitle">'.lang('Top').' <span class="topCreativesCount"></span> ' . lang('Merchant\'s Creatives').'</div>
								<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
									<thead>
									<tr>
										<td>'.lang('Type').'</td>
										<td>'.lang('Preview').'</td>
										<td>'.lang('Merchant').'</td>
										<td>'.lang('Language').'</td>
										<td>'.lang('Size').'</td>
										<td>'.lang('Get Code').'</td>
									</tr>
									</thead>
									<tfoot class="topCreativesCls">
									
									</tfoot>
								</table>' : '' ).'
								'.(!empty($products_list) ? '
								<div class="specialTableTitle">'.lang('Top').' <span class="topPCreativesCount">5</span> ' . lang('Products').'</div>
								<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
									<thead>
									<tr>
										<td>'.lang('Preview').'</td>
										<td>'.lang('Product Name').'</td>
										<td>'.lang('Language').'</td>
										<!--td>'.lang('Size').'</td-->
										<td>'.lang('Type').'</td>
										<td>'.lang('Get Code').'</td>
									</tr>
									</thead>
									<tfoot class="topPCreativesCls">
									
									</tfoot>
								</table>
								' : '' ).'
							</td>
						</tr>
					</table>
					<script class="text/javascript">
					$(document).ready(function(){
						$("tfoot.topCreativesCls").html("<tr><td colspan=10 align=\'center\'><img src=\''.$set->SSLprefix.'images/ajax-loader.gif\'>  <span style=\'position:relative;bottom:3px;left:10px;\'>'. lang('Loading Creatives. Please wait.') .'</span></td></tr>");
						$("tfoot.topPCreativesCls").html("<tr><td colspan=10 align=\'center\'><img src=\''.$set->SSLprefix.'images/ajax-loader.gif\'>  <span style=\'position:relative;bottom:3px;left:10px;\'>'. lang('Loading Creatives. Please wait.') .'</span></td></tr>");
					 	$.post("'.$set->SSLprefix.'ajax/LoadAffiliateTopCreatives.php",{affwhere:"'. $affwhere .'",affiliate_id:'. $set->userInfo['id'] .',affiliate_merchants:"'. $set->userInfo['merchants'] .'",active_products:"'. $set->userInfo['products'] .'"},function(res){
							if(res != ""){
								res = JSON.parse(res);
								console.log(res);
								
								if(res["creatives"]!= undefined){
									$("tfoot.topCreativesCls").html(res["creatives"]);
									$("span.topCreativesCount").html($("tfoot.topCreativesCls tr").length);
								}
								else{
									$("tfoot.topCreativesCls").html("<tr><td colspan=10 align=\'center\'>'. lang('No Creatives') .'</td></tr>");
									$("span.topCreativesCount").html("");
								}
								
								if(res["products"]!= undefined){
									$("tfoot.topPCreativesCls").html(res["products"]);
									$(".topProductsTable").show();
									$("span.topPCreativesCount").html($("tfoot.topPCreativesCls tr").length);
								}
								else{
									$(".topProductsTable").hide();
									$("tfoot.topPCreativesCls").html("");
									$("span.topPCreativesCount").html("");
								}
								
								$(".inline").colorbox({iframe:true,border: "1px black solid" ,height: "95%", width:"95%",fixed:true});
								$( ".img-wrap img" ).each(function( index ) {
									//console.log($(this).attr("src"));
									var dymantionsStr = $(this).closest("tr").find(".dimantion-wrap").text();
									console.log(dymantionsStr);
									var dymantionsArray = dymantionsStr.split("x");
									//console.log(dymantionsArray);
									var dymantionsRate = parseInt(dymantionsArray[0])/parseInt(dymantionsArray[1]);
									if(dymantionsRate < 0.7)
									{
										$(this).addClass("small-scale");
									}
									else if(dymantionsRate > 10) 
									{
										$(this).addClass("horizontal-scale");
									}
									else // if(dymantionsRate > 0.7 && dymantionsRate < 2) 
									{
										$(this).css("transition","scale(12)");
									}
									console.log(dymantionsRate);
								});
								$( ".img-wrap img" ).hover(
									function() {
										var currentImage = $( this );
										currentImage.addClass("animate");
										setTimeout(function(){ 
											currentImage.removeClass("animate");
											//console.log("innn");
										}, 2000);
									}, function() {
										$( this ).removeClass("animate");
									}
								);
							}
							else{
								$("span.topCreativesCount").html("");
								$("span.topPCreativesCount").html("");
								$(".topProductsTable").hide();
								$("tfoot.topCreativesCls").html("<tr><td colspan=10 align=\'center\'>'. lang('No Creatives') .'</td></tr>");
								$("tfoot.topPCreativesCls").html("<tr><td colspan=10 align=\'center\'>'. lang('No Creatives') .'</td></tr>");
							}
						}); 
					});
					</script>
					';

			if ($set->showDocumentsModule==1) {
			if ($set->showRequierdDocsOnAffiliateDash==1)					
				
					
                $strHtmlAskDocTypeCompany  = empty($set->AskDocTypeCompany)  ? '' : '<tr><td><li style="font-size:12.5px;">'.lang('Company Verification').'</li></td></tr>';
                $strHtmlAskDocTypeAddress  = empty($set->AskDocTypeAddress)  ? '' : '<tr><td><li style="font-size:12.5px;margin-top:3px;">'.lang('Address Verification').'</li></td></tr>';
                $strHtmlAskDocTypePassport = empty($set->AskDocTypePassport) ? '' : '<tr><td><li style="font-size:12.5px;margin-top:3px;">'.lang('Passport / Driving Licence').'</li></td></tr>';
                
                $sql = "SELECT (SELECT COUNT(*) AS count FROM `documents` 
                        WHERE `affiliate_id` = " . mysql_real_escape_string($set->userInfo['id']) . " 
                        AND `type` = 'Passport_Driving_Licence') AS Passport_Driving_Licence, 
                        (SELECT COUNT(*) AS count FROM `documents` 
                        WHERE `affiliate_id` = " . mysql_real_escape_string($set->userInfo['id']) . " 
                        AND `type` = 'Company_Verification') AS Company_Verification, 
                        (SELECT COUNT(*) AS count FROM `documents` 
                        WHERE `affiliate_id` = " . mysql_real_escape_string($set->userInfo['id']) . " 
                        AND `type` = 'Address_Verification') AS Address_Verification;";
                
                $arrDocuments = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                
                $strHtmlAskDocTypeCompany  = !empty($strHtmlAskDocTypeCompany)  && empty($arrDocuments['Company_Verification'])     ? $strHtmlAskDocTypeCompany  : '';
                $strHtmlAskDocTypeAddress  = !empty($strHtmlAskDocTypeAddress)  && empty($arrDocuments['Address_Verification'])     ? $strHtmlAskDocTypeAddress  : '';
                $strHtmlAskDocTypePassport = !empty($strHtmlAskDocTypePassport) && empty($arrDocuments['Passport_Driving_Licence']) ? $strHtmlAskDocTypePassport : '';
                
                if (
                    ((!empty($strHtmlAskDocTypeCompany)  && empty($arrDocuments['Company_Verification'])) || 
                    (!empty($strHtmlAskDocTypeAddress)  && empty($arrDocuments['Address_Verification'])) || 
                    (!empty($strHtmlAskDocTypePassport) && empty($arrDocuments['Passport_Driving_Licence'])))
              && $set->showDocumentsModule==1 && $set->showRequierdDocsOnAffiliateDash==1) {
                    $set->content .= '
                        <div id="document_fancybox" style="display:none;min-width:350px;">
                            <div>
                                <table style="min-width:330px">
                                    <tr>
                                        <td><h2 style="font-size:16px;">' . lang($set->AskDocSentence) . '</h2></td>
                                    </tr>' . $strHtmlAskDocTypeCompany . $strHtmlAskDocTypeAddress . $strHtmlAskDocTypePassport . '
                                    <tr><td>&nbsp;</td></tr>
                                    <tr>
                                        <td style="padding-top:5px;  padding-bottom: 10px;"><label style="  background-color: lightblue;font-size:15px;font-weight:bold;padding-top:6px;padding-bottom:6px;padding-left:4px;padding-right:4px;border:1px solid black;"><a href="'.$set->SSLprefix.'affiliate/documents.php">' . lang('Submit Documents') . '</a></label></td>
                                        <td><img style="height: 97px;  margin-left: -55px;  margin-top: -100px;" src="'.$set->SSLprefix.'images/panmark.png"/></td>
                                    </tr>
                                </table>
                            </div>
                        </div>';
						 $set->content .= '
                        <script type="text/javascript">
                            (function() {
                                $.fancybox({href : "#document_fancybox"});
                                $(\'#document_fancybox\').parent().parent().parent().css("border","4px red solid").css("border-radius", "5px");
                            })();
                        </script>
                    ';
			  }
			 
}        

			//modal
			$fields = getReportsHiddenCols("productsPlaceReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->DashboardProductsPlaceHiddenCols = $fields;
			}
			//echo $set->DashboardProductsPlaceHiddenCols;die;
			$dashboardReport = lang("Products Place");
			include "common/ReportFieldsModal.php";        
		
		if ($showCasinoFields && !$hideFrozens) {
			$set->content=str_replace('<td>|FROZEN|</td>','<td></td>',$set->content);
			$set->content=str_replace('<th>|FROZEN|</th>','<th></th>',$set->content);
		} else {
			$set->content=str_replace('<td>|FROZEN|</td>','',$set->content);
			$set->content=str_replace('<th>|FROZEN|</th>','',$set->content);
			$set->content=str_replace('<th>FROZEN</th>','',$set->content);
		}
		$set->content .= $msgsPopup;
		theme();
		break;
	
	case "logout":
		updateUnit("affiliates","logged='0'","id='".$_SESSION['aff_session_id']."'");
		unset($_SESSION['aff_session_id']);
		unset($_SESSION['aff_session_serial']);
		unset($_SESSION['messages_popup']);
		$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
		
		if (!isLogin()) _goto( $lout);
		break;
	
	}

/* ;
		unset($_SESSION['messages_popup']);
		_goto($set->SSLprefix'affiliate/');
		break;
	
	}

 */
?>