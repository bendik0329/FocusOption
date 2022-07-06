<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);

require_once('common/global.php');

if (!isAdmin()) { 
    _goto('/admin/');
}

$hideDemoAndLeads = hideDemoAndLeads();
	
/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();
$allCountriesArray = getDBCountries();

$from = strTodate($from);
$to   = strTodate($to);

$from = sanitizeDate($from);
$to   = sanitizeDate($to);

commonGlobalSetTimeRange($from, $to);

$globalWhere = ' 1 = 1 AND ';


switch ($act) {
		default:
		$set->pageTitle = lang('Quick Summary Report');
		$listReport = '';
                
                // List of wallets.
                $arrWallets = [];
                // $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1;";
				$merchantsA  = getMerchants(0,1);
                // while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
				foreach ($arrWallets as $arrWallet){
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
		$l = -1;
					
					
		$displayForex = 0;
		$tradersProccessedForLots= array();
		
		// $sql = "SELECT * FROM merchants WHERE valid='1' ORDER BY type, pos";
		// $qq = function_mysql_query($sql,__FILE__);
		foreach ($merchantsA as $ww){
		
		// while ($ww = mysql_fetch_assoc($qq)) {
		
		if (strtolower($ww['producttype'])=='forex')
							$displayForex = 1;			
						
						
                    // Check if this is a first itaration on given wallet.
                    if ($set->multiMerchantsPerTrader==1)
						$needToSkipMerchant = $arrWallets[$ww['wallet_id']];
				else 
					$needToSkipMerchant= false;
				
                    
                    $formula  = $ww['rev_formula'];
                    $fromDate = $from;
                    $toDate   = $to;
                    
                    $l++;
                    $ftdUsers = '';
                    $netRevenue = 0;
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
                    $volume = 0;
                    $merchantName = strtolower($ww['name']);
                    $merchantID = $ww['id'];
                    
                    
                    $arrRanges = [];
                    
                    switch ($display_type) {
                        case 'monthly':
                            $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_MONTHLY);
                            break;
                        case 'weekly':
                            $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_WEEKLY);
                            break;
                        case 'daily':
                            $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_DAILY_RANGE);
                            break;
                        default:
                            $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_NONE);
                            break;
                    }
                    
                    
			// Time-periods loop.
                        foreach ($arrRanges as $arrRange) {
                            $searchInSql = " BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";
                            
                            $formula  = $ww['rev_formula'];
                            $fromDate = $from;
                            $toDate   = $to;
                            
                            $ftdUsers = '';
                            $netRevenue = 0;
                            $totalCom=0;
                            $ftd=0;
                            $totalLeads = 0;
                            $totalDemo = 0;
                            $totalReal = 0;
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
                            $merchantID = $ww['id'];
                            
                            
                            $totalTraffic = [];
                            $arrClicksAndImpressions = getClicksAndImpressions(
                                $arrRange['from'], 
                                $arrRange['to'], 
                                $ww['id'], 
                                null, 
                                (isset($group_id) && $group_id != '' ? $group_id : null)
                            );
                            
                            $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                            
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE merchant_id = '" . $ww['id'] . "' AND "
                                    . " rdate " . $searchInSql 
                                    . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                            
                            $regqq = function_mysql_query($sql,__FILE__);
                            
                            $arrTierCplCountCommissionParams = [];
                            
                            while ($regww = mysql_fetch_assoc($regqq)) {
								
								
								
							$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
                                
                                $boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
                                
                                    if ($regww['type'] == "lead") $totalLeads++;
                                    if ($regww['type'] == "demo") $totalDemo++;
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
                                            
                                            $totalCom += getCommission(
                                                $arrRange['from'], 
                                                $arrRange['to'], 
                                                0, 
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
                                            unset($arrTmp);
                                            
                                        } else {
                                            // TIER CPL.
                                            if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
                                                $arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
                                            } else {
                                                $arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
                                                    $arrRange['from'], 
                                                    $arrRange['to'], 
                                                    0, 
                                                    (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                    $arrDealTypeDefaults, 
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
                                        
                                        unset($arrTmp);
                                        $totalReal++;
                                    }
				}
                                
                                // TIER CPL.
                                foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
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
                                
                                
                                
                                $strSql = "SELECT *, data_sales.type AS data_sales_type  FROM data_sales AS data_sales "
                                        . "INNER JOIN data_reg AS data_reg ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                        . "WHERE data_sales.merchant_id = '" . $ww['id'] . "' AND data_sales.rdate "
                                        . $searchInSql . " "
                                        . (isset($group_id) && $group_id != '' ? ' AND data_sales.group_id = ' . $group_id . ' ' : '');
                                
								$salesqq = function_mysql_query($strSql,__FILE__);
								$netDepositTransactions = array();
								while ($salesww = mysql_fetch_assoc($salesqq)) {
                                    //if ($salesww['type'] == 'deposit') { // OLD.
									if ($salesww['data_sales_type'] == 'deposit') {   // NEW.
										$tranrow['id'] = $salesww['id'];
										$tranrow['affiliate_id'] = $salesww['affiliate_id'];
										$tranrow['tranz_id'] = $salesww['tranz_id'];
										$tranrow['trader_id'] = $salesww['trader_id'];
										$tranrow['merchant_id'] = $salesww['merchant_id'];
										$tranrow['rdate'] =$salesww['rdate'];
										$tranrow['amount'] = $salesww['amount'];
										$tranrow['type'] = $salesww['data_sales_type'];
										$tranrow['status'] = $salesww['status'];
										$netDepositTransactions[] = array($tranrow);
                                        $sumDeposits += $salesww['amount'];
										$depositsAmount+=$salesww['amount'];
                                        $depositingAccounts++;
                                    }
                                    
										
                                    if ($salesww['data_sales_type'] == "bonus" || $salesww['data_sales_type'] == "withdrawal" || $salesww['data_sales_type'] == "chargeback"){
										$tranrow['id'] = $salesww['id'];
										$tranrow['affiliate_id'] = $salesww['affiliate_id'];
										$tranrow['tranz_id'] = $salesww['tranz_id'];
										$tranrow['trader_id'] = $salesww['trader_id'];
										$tranrow['merchant_id'] = $salesww['merchant_id'];
										$tranrow['rdate'] =$salesww['rdate'];
										$tranrow['amount'] = $salesww['amount'];
										$tranrow['type'] = $salesww['data_sales_type'];
										$tranrow['status'] = $salesww['status'];
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
                                            'rdate'        => $salesww['rdate'],
                                            'banner_id'    => $salesww['banner_id'],
                                            'trader_id'    => $salesww['trader_id'],
                                            'profile_id'   => $salesww['profile_id'],
                                            'type'       => 'volume',
                                         'amount'       => $salesww['amount'],
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
				
                                
                                $arrFtds  = getTotalFtds(
                                    $arrRange['from'], 
                                    $arrRange['to'], 
                                    0, 
                                    $ww['id'], 
                                    $ww['wallet_id'], 
                                    (isset($group_id) && $group_id != '' ? $group_id : 0), 
                                    0, 
                                    0,
                                    $searchInSql
                                );
                                
                                if (!$needToSkipMerchant) {
                                    
									/* $key = array_keys($arrFtds);
					$size = sizeOf($key);
					for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
						$arrFtd = $arrFtds[$key[$ftdCount]] ;
						
						 */


/* 								$size = sizeOf($arrFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrFtd = $arrFtds[$ftdCount] ;
 */
									
									foreach ($arrFtds as $arrFtd) {
                                        $real_ftd++;
                                        $real_ftd_amount += $arrFtd['amount'];
                                        
                                        $beforeNewFTD = $ftd;
                                        getFtdByDealType($ww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $ftd);
                                        
                                        if ($beforeNewFTD != $ftd) {
                                            $arrFtd['isFTD'] = true;
                                            $totalCom += getCommission(
                                                $arrRange['from'], 
                                                $arrRange['to'], 
                                                0,
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrFtd
                                            );
                                        }
                                        unset($arrFtd);
                                    }
                                }
                                
                                if (strtolower($ww['producttype']) == 'sportsbetting' || strtolower($ww['producttype']) == 'casino') {
                                    // Run through a list of affiliates.
                                    $sql = 'SELECT id AS id, merchants AS merchants FROM affiliates '
                                            . 'WHERE valid = 1 '
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                                    
                                    $resourceAffiliates = function_mysql_query($sql,__FILE__);
                                    
                                    while ($arrAff = mysql_fetch_assoc($resourceAffiliates)) {
                                        $arrMerchantsAffiliate = explode('|', $arrAff['merchants']);
                                        if (!in_array($ww['id'], $arrMerchantsAffiliate)) {
                                            continue;
                                        }
                                        
                                        $arrRevenueRanges = getRevenueDealTypeByRange($arrRange['from'], $arrRange['to'], $ww['id'], $arrAff['id'], $arrDealTypeDefaults);
                                        $intTotalRevenue  = 0;
                                        
                                        foreach ($arrRevenueRanges as $arrRange2) {
											
                                            $strRevWhere = 'WHERE merchant_id = ' . $ww['id'] . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                                         . '" AND affiliate_id = "' . $arrAff['id'] . '" ';
                                            
                                            $intCurrentRevenue = getRevenue($strRevWhere, $ww['producttype']);
                                            
                                            $intTotalRevenue    += $intCurrentRevenue;
                                            $row                 = array();
                                            $row['merchant_id']  = $ww['id'];
                                            $row['affiliate_id'] = $arrAff['id'];
                                            $row['banner_id']    = 0;
                                            $row['rdate']        = $arrRange2['from'];
                                            $row['amount']       = $intCurrentRevenue;
                                            $row['isFTD']        = false;
                                            $totalCom           += getCommission(
                                                $arrRange2['from'], 
                                                $arrRange2['to'], 
                                                1, 
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $row
                                            );
                                            unset($arrRange2, $strRevWhere);
                                        }
                                        
                                        $netRevenue += $intTotalRevenue;
                                        unset($arrAff);
                                    }

                                } else {
                                    //$netRevenue =  round(getRevenue($searchInSql,$ww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$ww['rev_formula'],null,$chargeback),2);
									
									foreach($netDepositTransactions as $trans){
									 	$revDepAmount = 0;
							$revBonAmount = 0;
							$revWithAmount = 0;
							$revChBAmount = 0;
							
							$amount = $trans[0]['amount'];
							
							
						if (floatval($amount>0)  && !empty($trans[0]['rdate'])) {
							
							// var_dump($trans[0]);
							// echo '<Br>';
							
								if ($trans[0]['type']=='deposit')
									$revDepAmount = $amount;
								if ($trans[0]['type']=='bonus')
									$revBonAmount = $amount;
								if ($trans[0]['type']=='withdrawal')
									$revWithAmount = $amount;
								if ($trans[0]['type']=='chargeback')
									$revChBAmount = $amount;
								
									$intNetRevenue =  round(getRevenue($searchInSql,$ww['producttype'],$revDepAmount,$revBonAmount,$revWithAmount,0,0,0,$ww['rev_formula'],null,$revChBAmount),2);
									$netRevenue += $intNetRevenue;
											$comrow                 = array();
										   $comrow['merchant_id']  = $trans[0]['merchant_id'];
										   $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
											$comrow['banner_id']    = 0;
											$comrow['rdate']        = $arrRange2['from'];
											$comrow['amount']       = $intNetRevenue;
											 //$comrow['trader_id']  =  $trans[0]['trader_id'];
											$comrow['isFTD']        = false;

												
														$com = getCommission($from, $to, 1, -1, $arrDealTypeDefaults, $comrow);
														
														
														$totalCom           += $com;
									
									}
									}
									// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
                                }
                                
                                
									//,SUM(turnover) AS totalTO '
                  if (strtolower($ww['producttype']) == 'forex') {
                                    $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl '
                                            . 'FROM data_stats '
                                            . 'WHERE merchant_id = "' . $merchantID . '" AND rdate ' . $searchInSql
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                                    
                                    $traderStatsQ = function_mysql_query($sql,__FILE__);
                                    
                                    while($ts = mysql_fetch_assoc($traderStatsQ)){
                                        $spreadAmount  = $ts['totalSpread'];
                                        //$volume       += $ts['totalTO'];
                                        $pnl           = $ts['totalPnl'];
                                    }
									
									
									
								// lots
					
						$totalLots  = 0;
						
						
							
							
						$sql = 'SELECT turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
                                         // . 'WHERE merchant_id = "' . $merchantID . '" AND rdate ' . $searchInSql 
										 . 'WHERE merchant_id = "' .$merchantID . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
											
											// die($sql);
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d');
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											
											if($ts['affiliate_id']==null) {
													continue;
											}
							
											// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
													$totalLots  = $ts['totalTurnOver'];
													// echo $totalLots
														$tradersProccessedForLots[$merchantID . '-' . $ts['trader_id']] = $merchantID . '-' . $ts['trader_id'];
														$lotdate = $ts['rdate'];
														$ex = explode(' ' , $lotdate);
														$lotdate = $ex[0];
															if ($earliestTimeForLot>$lotdate)
															$earliestTimeForLot = $lotdate;
														
														$row = [
																	'merchant_id'  => $merchantID,
																	'affiliate_id' => $ts['affiliate_id'],
																	'rdate'        => $earliestTimeForLot,
																	'banner_id'    => $ts['banner_id'],
																	'trader_id'    => $ts['trader_id'],
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
													$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
													// echo 'com: ' . $a .'<br>';
													$totalCom += $a;
											// }
										}
				  }
						
				$filterFrom = $arrRange['from'];
				$filterTo   = $arrRange['to'];
				$boxaName   = "admin-quick-report-1";
                                
                                
				$tableArr = array(
						
					(object) array(
					  'id' => 'daily',
					  'str' => ($display_type == "daily" ? '<td style="text-align: center;">' . $arrRange['date'] . '</td>' : '')
					),
					(object) array(
					  'id' => 'weekly',
					  'str' => ($display_type == "weekly" ? '<td style="text-align: center;">'.$arrRange['from'].' - '.$arrRange['to'].'</td>' : '')
					),
					(object) array(
					  'id' => 'monthly',
					  'str' => ($display_type == "monthly" ? '<td style="text-align: center;">'.$arrRange['from'].' - '.$arrRange['to'].'</td>' : '')
					),
					(object) array(
					  'id' => 'name',
					  'str' => '<td style="text-align: left;">'.$ww['name'].'</td>'
					),
					(object) array(
					  'id' => 'totalViews',
					  'str' => '<td style="text-align: center;">'.@number_format($totalTraffic['totalViews'],0).'</td>'
					),
					(object) array(
					  'id' => 'totalClicks',
					  'str' => '<td style="text-align: center;">'.@number_format($totalTraffic['totalClicks'],0).'</td>'
					),
					(object) array(
					  'id' => 'totalClicks_totalViews',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'ftd_total_traffic',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'commission_traffic',
					  'str' => '<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'EPC',
					  'str' => '<td style="text-align: center;">'.@price($totalCom/$totalTraffic['totalClicks']).'</td>'
					),
					(object) array(
					  'id' => 'total_leads',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>'
					),
					(object) array(
					  'id' => 'total_demo',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>'
					),
					(object) array(
					  'id' => 'total_real',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($ftd_amount['amount']).'</td>'
					),
					
					(object) array(
					  'id' => 'real_ftd',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=totalftd">'.$real_ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'real_ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($real_ftd_amount).'</td>'
					),
					
					(object) array(
					  'id' => 'depositAccount',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=transactions&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>'
					),
					(object) array(
					  'id' => 'sumDeposits',
					  'str' => '<td style="text-align: center;">'.price($sumDeposits).'</td>'
					),
					(object) array(
					  'id' => 'volume',
					  'str' => '<td style="text-align: center;">'.price($volume).'</td>'
					),
					(object) array(
					  'id' => 'bonus',
					  'str' => '<td style="text-align: center;">'.price($bonus).'</td>'
					),
					(object) array(
					  'id' => 'Withdrawal',
					  'str' => '<td style="text-align: center;">'.price($withdrawal).'</td>'
					),
					(object) array(
					  'id' => 'ChargeBack',
					  'str' => '<td style="text-align: center;">'.price($chargeback).'</td>'
					),
					(object) array(
					  'id' => 'NetRevenue',
					  'str' => '<td style="text-align: center;">'.price($netRevenue).'</td>'
					),
					(object) array(
					  'id' => 'Commission',
					  'str' => '<td style="text-align: center;">'.price($totalCom).'</td>'
					)				
				);
				
				$listReport .= '<tr>'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'</tr>';
				
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalLeadsAccounts += $totalLeads;
				$totalDemoAccounts += $totalDemo;
				$totalRealAccounts += $totalReal;
				$totalFTD += $ftd;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount['amount'];
				$totalDepositAmount += $sumDeposits;
				$totalVolume += $volume;
				$totalBonus += $bonus;
				$totalWithdrawal += $withdrawal;
				$totalChargeback += $chargeback;
				$totalNetRevenue += $netRevenue;
				$totalComs += $totalCom;
				$totalRealFtd+=$real_ftd;
				$totalRealFtdAmount+=$real_ftd_amount;
                                
                            unset($arrRange); // Clear up the memory.
			} // End of time-periods loop.
                        
                    // Mark given wallet as processed.
                    $arrWallets[$ww['wallet_id']] = true;
		}
                
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		
		
		$tableArr = Array(
						
			(object) array(
			  'id' => 'daily',
			  'str' => ($display_type == "daily" ? '<th>'.lang('Period').'</th>' : '')
			),
			(object) array(
			  'id' => 'weekly',
			  'str' => ($display_type == "weekly" ? '<th>'.lang('Period').'</th>' : '')
			),
			(object) array(
			  'id' => 'monthly',
			  'str' => ($display_type == "monthly" ? '<th>'.lang('Period').'</th>' : '')
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th style="text-align: left;">'.lang('Merchant').'</th>'
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
			  'id' => 'totalClicks_totalViews',
			  'str' => '<th>'.lang('Click Through Ratio (CTR)').'</th>'
			),
			(object) array(
			  'id' => 'ftd_total_traffic',
			  'str' => '<th>'.lang(ptitle('Click to Account')).'</th>'
			),
			(object) array(
			  'id' => 'commission_traffic',
			  'str' => '<th>'.lang(ptitle('Click to Sale')).'</th>'
			),
			(object) array(
			  'id' => 'EPC',
			  'str' => '<th>'.lang(ptitle('EPC')).'</th>'
			),
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th>'.lang(ptitle('Lead')).'</th>'
			),
			(object) array(
			  'id' => 'total_demo',
			  'str' => '<th>'.lang(ptitle('Demo')).'</th>'
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th>'.lang(ptitle('Accounts')).'</th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th>'.lang('FTD').'</th>'
			),
			(object) array(
			  'id' => 'ftd_amount',
			  'str' => '<th>'.lang('FTD Amount').'</th>'
			),
			
			(object) array(
			  'id' => 'real_ftd',
			  'str' => '<th>'.lang('Total FTD').'</th>'
			),
			(object) array(
			  'id' => 'real_ftd_amount',
			  'str' => '<th>'.lang('Total FTD Amount').'</th>'
			),
			
			(object) array(
			  'id' => 'depositAccount',
			  'str' => '<th>'.lang('Total Deposits').'</th>'
			),
			(object) array(
			  'id' => 'sumDeposits',
			  'str' => '<th>'.lang('Deposits Amount').'</th>'
			),
			(object) array(
			  'id' => 'volume',
			  'str' => '<th>'.lang('Volume').'</th>'
			),
			(object) array(
			  'id' => 'bonus',
			  'str' => '<th>'.lang('Bonus Amount').'</th>'
			),
			(object) array(
			  'id' => 'Withdrawal',
			  'str' => '<th>'.lang('Withdrawal Amount').'</th>'
			),
			(object) array(
			  'id' => 'ChargeBack',
			  'str' => '<th>'.lang('ChargeBack Amount').'</th>'
			),
			(object) array(
			  'id' => 'NetRevenue',
			  'str' => '<th>'.lang(ptitle('Net Revenue')).'</th>'
			),
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.lang('Commission').'</th>'
			)				
		);
		
		
		
		
		$tableArr2 = Array(
						
			(object) array(
			  'id' => 'daily',
			  'str' => ($display_type == "daily" ? ($display_type ? '<th></th>' : '') : '')
			),
			(object) array(
			  'id' => 'weekly',
			  'str' => ($display_type == "weekly" ? ($display_type ? '<th></th>' : '') : '')
			),
			(object) array(
			  'id' => 'monthly',
			  'str' => ($display_type == "monthly" ? ($display_type ? '<th></th>' : '') : '')
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th style="text-align: left;"><b>'.lang('Total').':</b></th>'
			),
			(object) array(
			  'id' => 'totalViews',
			  'str' => '<th>'.$totalImpressions.'</th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th>'.$totalClicks.'</th>'
			),
			(object) array(
			  'id' => 'totalClicks_totalViews',
			  'str' => '<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'ftd_total_traffic',
			  'str' => '<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'commission_traffic',
			  'str' => '<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'EPC',
			  'str' => '<th>'.@price($totalComs/$totalClicks).'</th>'
			),
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'total_demo',
			  'str' => '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd_amount',
			  'str' => '<th>'.price($totalFTDAmount).'</th>'
			),
			
			(object) array(
			  'id' => 'real_ftd',
			  'str' => '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>'
			),
			(object) array(
			  'id' => 'real_ftd_amount',
			  'str' => '<th>'.price($totalRealFtdAmount).'</th>'
			),
			
			(object) array(
			  'id' => 'depositAccount',
			  'str' => '<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>'
			),
			(object) array(
			  'id' => 'sumDeposits',
			  'str' => '<th>'.price($totalDepositAmount).'</th>'
			),
			(object) array(
			  'id' => 'volume',
			  'str' => '<th>'.price($totalVolume).'</th>'
			),
			(object) array(
			  'id' => 'bonus',
			  'str' => '<th>'.price($totalBonus).'</th>'
			),
			(object) array(
			  'id' => 'Withdrawal',
			  'str' => '<th>'.price($totalWithdrawal).'</th>'
			),
			(object) array(
			  'id' => 'ChargeBack',
			  'str' => '<th>'.price($totalChargeback).'</th>'
			),
			(object) array(
			  'id' => 'NetRevenue',
			  'str' => '<th>'.price($totalNetRevenue).'</th>'
			),
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.price($totalComs).'</th>'
			)				
		);
		
		
		
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form method="get">
				<table><tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Group ID').'</td>
						<td>'.lang('Search Type').'</td>
						<td></td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<!--td><input type="text" name="group_id" value="'.$group_id.'" id="group_id" style="width: 60px; text-align: center;" /></td-->
                                        <td width="100">
                                            <select name="group_id" style="width: 100px;">
                                                <option value="">'.lang('All Groups').'</option>'
                                                . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                    . lang('General') 
                                                . '</option>' 
                                                . listGroups($group_id) 
                                            . '</select>
                                        </td>
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 2000px;">'.lang('Quick Summary Report').'</div>
		<div style="background: #F8F8F8;">';
			$tableStr = '
			<table width="2000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
				<thead><tr>
					'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'<!--
					<th style="text-align: left;">'.lang('Merchant').'</th>
					<th>'.lang('Impressions').'</th>
					<th>'.lang('Clicks').'</th>
					<th>'.lang('Click Through Ratio (CTR)').'</th>
					<th>'.lang(ptitle('Click to Account')).'</th>
					<th>'.lang(ptitle('Click to Sale')).'</th>
					<th>EPC</th>
					<th>'.lang(ptitle('Lead')).'</th>
					<th>'.lang(ptitle('Demo')).'</th>
					<th>'.lang(ptitle('Accounts')).'</th>
					<th>'.lang('FTD').'</th>
					<th>'.lang('FTD Amount').'</th>
					<th>'.lang('Total Deposits').'</th>
					<th>'.lang('Deposits Amount').'</th>
					<th>'.lang('Volume').'</th>
					<th>'.lang('Bonus Amount').'</th>
					<th>'.lang('Withdrawal Amount').'</th>
					<th>'.lang('ChargeBack Amount').'</th>
					<th>'.lang(ptitle('Net Revenue')).'</th>
					<th>'.lang('Commission').'</th>-->
				</tr></thead><tfoot><tr>
					'.setTable($tableArr2, $boxaName, $set->userInfo['productType'], '').'<!--
					'.($display_type ? '<th></th>' : '').'
					<th style="text-align: left;"><b>'.lang('Total').':</b></th>
					<th>'.$totalImpressions.'</th>
					<th>'.$totalClicks.'</th>
					<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
					<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
					<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
					<th>'.@price($totalComs/$totalClicks).'</th>
					<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
					<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
					<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
					<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
					<th>'.price($totalFTDAmount).'</th>
					<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
					<th>'.price($totalDepositAmount).'</th>
					<th>'.price($totalVolume).'</th>
					<th>'.price($totalBonus).'</th>
					<th>'.price($totalWithdrawal).'</th>
					<th>'.price($totalChargeback).'</th>
					<th>'.price($totalNetRevenue).'</th>
					<th>'.price($totalComs).'</th>-->
				</tr></tfoot>
				<tbody>
				'.$listReport.'
			</table>';
		
		excelExporter($tableStr, 'Quick');
		$set->content .= $tableStr . '</div>' . getPager();
		theme();
		break;
		
		
                
	case "traffic":
	
		$set->pageTitle = lang('Referral Report');
		
		$page = (isset($page) || !empty($page))?$page:1;
		$set->page = $page;
		
		$start_limit = $page==1?0:$set->rowsNumberAfterSearch * ($page -1);
		$end_limit = $set->rowsNumberAfterSearch * $page;
		
		if ($merchant_id) $where = " AND tb1.merchant_id='".$merchant_id."'";
		if ($profile_id) $whereSites = " AND tb1.profile_id='".$profile_id."'";
		// if ($banner_id) $whereSites .= " AND tb1.banner_id='".$banner_id."'";
		if ($affiliate_id) $whereSites .= " AND tb1.affiliate_id='".$affiliate_id."'";
		if ($group_id && isAdmin()) $whereSites .= " AND af.group_id='".$group_id."'";
	
	/* $sql = "SELECT * FROM merchants WHERE valid='1' ".$where." ORDER BY pos";
		$qq=function_mysql_query($sql,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) 
		 */
		
			
			$formula = $ww['rev_formula'];
			
			$fromDate = strtotime($from);
			$toDate = strtotime($to);
			
			if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
				else $diff = 0;
			
			if($display_type == "weekly") {
				$theFromDay = date("w",strtotime($from))+1;
				$theToDay = date("w",strtotime($to))+1;
				$daysUntilEndWeek = (7-$theFromDay);
				$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
				
				$diff = round($diff/8);
				if($theFromDay > $theToDay) $diff = $diff+1;
				}
			
			if ($display_type == "monthly") {
				$endOfthisMonth = date("t",strtotime($from));
				if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
				$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
				$date1 = strtotime($from);
				$date2 = strtotime($to);
				$months = 0;
				while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
				if($diff == 0) $lastDayOfMonth = $to;
				}
			
			//for ($i=0;$i<=$diff;$i++) 
			//{
				
				// From Show on weekly
				if($newFrom) $showFrom = $newFrom; else $showFrom = $from;
				
				$searchInSql = "";
				if ($display_type == "daily") {
					$searchInSql = "= '".date("Y-m-d", strtotime($from." +".$i." day"))."'";
				} else if ($display_type == "weekly"){
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "weekly"){ 
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "monthly"){ 
					if($i==0){ // Monthly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
						$newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
						$newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} 
				} else { // If no Display_type 
					$searchInSql = "BETWEEN '".$from." 00:00:00' AND '".$to."'";
				}
				
				// To Show on weekly
				if($display_type == "weekly")  if($newTo) $showTo = $newTo; else $showTo = $endOfWeek;
				// To Show on monthly
				if($display_type == "monthly") if($newTo) $showTo = $newTo; else $showTo = $lastDayOfMonth;
				
				if($i==$diff) $showTo = $to;
				
				$l++;
				
				$merchantName = strtolower($ww['name']);
				$merchantID = $ww['id'];
				
				
				
				$filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showFrom));
				$filterTo = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showTo));
				
				
		$refferelArray = array();	
		$listGroups = affiliateGroupsArray();
		$is_admin = isAdmin();
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		
					$qry = ("SELECT sum(clicks) as total_clicks, sum(views) as total_visits, af.group_id as group_id ,tb1.*,mr.name as merchant_name,af.username FROM traffic tb1 
									inner join affiliates af on tb1.affiliate_id = af.id 
									inner join merchants mr on mr.id = tb1.merchant_id 
									WHERE tb1.type='traffic'  and tb1.refer_url !='' and 1=1  ".$whereSites." AND tb1.rdate ".$searchInSql." group by tb1.refer_url order by tb1.id desc limit " . $start_limit. ", " . $end_limit);
				
				$listReport="";		
				$resc = function_mysql_query($qry,__FILE__);
				$i = 0;
				$totalVisits = 0;
				while ($arrRes = mysql_fetch_assoc($resc)) {
				if($arrRes['uid'] !=0){
				
				$country='';
				$countryArry = getIPCountry($arrRes['ip']);
				if ($countryArry['countryLONG']=='')
					$country = lang('Unknown');
				else
					$country = $countryArry['countryLONG'];
				
				
				$refferelArray[$arrRes['refer_url']]['refer_url'] = $arrRes['refer_url'];
				$refferelArray[$arrRes['refer_url']]['id'] = $arrRes['id'];
				$refferelArray[$arrRes['refer_url']]['merchant_id'] = $arrRes['merchant_id'];
				$refferelArray[$arrRes['refer_url']]['merchant_name'] = $arrRes['merchant_name'];
				$refferelArray[$arrRes['refer_url']]['ip'] = $arrRes['ip'];
				$refferelArray[$arrRes['refer_url']]['country'] = $arrRes['country'];
				$refferelArray[$arrRes['refer_url']]['group'] = $listGroups[$arrRes['group_id']];
				$refferelArray[$arrRes['refer_url']]['views'] = $arrRes['total_visits'];
				$refferelArray[$arrRes['refer_url']]['clicks'] = $arrRes['total_clicks'];
				$refferelArray[$arrRes['refer_url']]['affiliate_id'] = $arrRes['affiliate_id'];
				$refferelArray[$arrRes['refer_url']]['username'] = $arrRes['username'];
				$refferelArray[$arrRes['refer_url']]['profile_id'] = $arrRes['profile_id'];
				$refferelArray[$arrRes['refer_url']]['uid'] = $arrRes['uid'];
			// var_dump($country);
		
				
				
		  if(!empty($refferelArray)){           
		  
				$ftd=0;
				$ftd_amount=0;
				$totalCom=0;
				$bonus=0;
				$withdrawal=0;
				$chargeback=0;
				$volume=0;
				$totalLeads=0;
				$sumDeposits=0;
				$depositingAccounts=0;
				$totalDemo=0;
				$totalReal=0;
				
				// registration (leads + demo + real)
				$where = "1=1";
				if ($merchant_id) $where .= " AND dg.merchant_id='".$merchant_id."'";
				if ($affiliate_id) $where .= " AND dg.affiliate_id='".$affiliate_id."'";
				  
				$sql = "SELECT dg.* FROM data_reg dg"
							." WHERE " . $where
							. " AND dg.uid = " . $arrRes['uid'];
				//echo $sql;die;
				$regqq = function_mysql_query($sql,__FILE__);
				
				$arrTierCplCountCommissionParams = [];
					// die ($sql);
				$regArray = array();
				while ($regww = mysql_fetch_assoc($regqq)) {
					
					
					if(!empty($regww['trader_id'])){
						$tranrow['id'] = $regww['id'];
						$tranrow['affiliate_id'] = $regww['affiliate_id'];
						$tranrow['trader_id'] = $regww['trader_id'];
						$tranrow['merchant_id'] = $regww['merchant_id'];
						$regArray[] = array($tranrow);

						$refferelArray[$arrRes['refer_url']]['reg_date'] = $regww['reg_date'];
						$refferelArray[$arrRes['refer_url']]['trader_id'] = $regww['trader_id'];
						$refferelArray[$arrRes['refer_url']]['trader_name'] = $regww['trader_alias'];
						
						$refferelArray[$arrRes['refer_url']]['sale_status'] = $regww['saleStatus'];
						
						$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
						$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
						
						if ($regww['type'] == "lead"){
							//$totalLeads++;
								$refferelArray[$arrRes['refer_url']]['leads'] += 1;
						}
						if ($regww['type'] == "demo"){
								$refferelArray[$arrRes['refer_url']]['demo'] += 1;
						} 
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
								
								$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
								$refferelArray[$arrRes['refer_url']]['total_com'] += $totalCom;
								
							} else {
								// TIER CPL.
								if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
								} else {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
										'from'                => $from,
										'to'                  => $to,
										'onlyRevShare'        => 0,
										'groupId'             => (is_null($group_id ? -1 : $group_id)),
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
							
							unset($arrTmp);
							//$totalReal++;
							$refferelArray[$arrRes['refer_url']]['real'] += 1;
						}
					}
				}
				 
				 
				 if(!isset($refferelArray[$arrRes['refer_url']]['trader_id'])){
					// TIER CPL.
					foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
						$totalCom = getCommission(
							$arrParams['from'], 
							$arrParams['to'], 
							$arrParams['onlyRevShare'], 
							$arrParams['groupId'], 
							$arrParams['arrDealTypeDefaults'], 
							$arrParams['arrTmp']
						);
						$refferelArray[$arrRes['refer_url']]['totalCom'] += 1;
						unset($intAffId, $arrParams);
					}
				 }
				
					foreach($regArray as $key=>$params){
						$trader_id = $params[0]['trader_id'];
						if(!is_null($trader_id)){
						$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id),0,0,0,$trader_id);
						foreach ($arrFtds as $arrFtd) {
								$real_ftd++;
								$refferelArray[$arrRes['refer_url']]['real_ftd'] += 1;
								
								$real_ftd_amount = $arrFtd['amount'];
								$refferelArray[$arrRes['refer_url']]['real_ftd_amount'] += $real_ftd_amount;
								
								$beforeNewFTD = $ftd;
								getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
							
								if ($beforeNewFTD != $ftd) {
									
									$arrFtd['isFTD'] = true;
									$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
									
									$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
								}
								$refferelArray[$arrRes['refer_url']]['ftd'] = $ftd;
										
								$refferelArray[$arrRes['refer_url']]['ftd_amount'] = $ftd_amount;
								unset($arrFtd);
						
						}
					
			
				
				
				
					//Sales
					$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
								 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
								 . "WHERE  tb1.trader_id = " .  $trader_id
							//	 . ' and tb1.rdate between "' . $from . '" AND "' . $to . '"' 
								. (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
								 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
								 . (isset($banner_id) && !empty($banner_id) ? ' AND data_reg.banner_id = "'.$banner_id.'"' :'') 
								 .(!empty($unique_id) ? ' and data_reg.uid = ' . $unique_id :'');
					
					$salesqq = function_mysql_query($sql,__FILE__);
								
					while ($salesww = mysql_fetch_assoc($salesqq)) {
							
							if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
								$depositingAccounts++;
								$refferelArray[$arrRes['refer_url']]['depositingAccounts'] += 1;
								
								$sumDeposits = $salesww['amount'];
								$refferelArray[$arrRes['refer_url']]['sumDeposits'] += $salesww['amount'];
								
								// $depositsAmount+=$salesww['amount'];
							}
							
							if ($salesww['data_sales_type'] == "bonus") {
									$bonus = $salesww['amount'];
									$refferelArray[$arrRes['refer_url']]['bonus'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == "withdrawal"){ 
									$withdrawal = $salesww['amount'];
									$refferelArray[$arrRes['refer_url']]['withdrawal'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == "chargeback"){
									$chargeback = $salesww['amount'];
									$refferelArray[$arrRes['refer_url']]['chargeback'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == 'volume') {
								$volume = $salesww['amount'];
								$refferelArray[$arrRes['refer_url']]['volume'] += $salesww['amount'];
								$arrTmp = [
									'merchant_id'  => $salesww['merchant_id'],
									'affiliate_id' => $salesww['affiliate_id'],
									'rdate'        => $salesww['rdate'],
									'banner_id'    => $salesww['banner_id'],
									'trader_id'    => $salesww['trader_id'],
									'profile_id'   => $salesww['profile_id'],
									'type'       => 'volume',
									'amount'       => $salesww['amount'],
								];
								
								$totalCom = getCommission(
									$from, 
									$to, 
									0, 
									(isset($group_id) && $group_id != '' ? $group_id : -1), 
									$arrDealTypeDefaults, 
									$arrTmp
								);

								$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
							}
						
						
							//REVENUE   						// loop on merchants    								// loop on affiliates
							// start of data_stats (revenue) loop
							$merchantww = 	getMerchants($salesww['merchant_id'],0);
							
							if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {
								
								// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
								$netRevenue =  round(getRevenue("data_sales.rdate  BETWEEN . '".$from."' AND '".$to."' ",$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$salesww['data_sales_type'] == "withdrawal"?$withdrawal:0,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
								//$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
								//echo $salesww['id'] . "----" . $netRevenue . "<br/>";
									
								$refferelArray[$arrRes['refer_url']]['netRevenue'] += $netRevenue;
								
								if($netRevenue<>0){
									$row                 = array();
									$row['merchant_id']  = $merchantww['id'];
									$row['affiliate_id'] = $salesww['affiliate_id'];
									$row['banner_id']    = 0;
									$row['rdate']        = $from;
									$row['amount']       = $netRevenue;
									$row['isFTD']        = false;
									$row['trader_id']        = $trader_id;
									  
									$totalCom           = getCommission($from, $to, 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
								}
							} 
							// end of data_stats (revenue) loop
						
							// end of data_sales loop
					}
					
					$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype FROM data_stats ds  "
								. " INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
								 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" 
								 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
								 . " and ds.trader_id=" . $trader_id;
								 
					$revqq  = function_mysql_query($sql,__FILE__); 					 
					while ($revww = mysql_fetch_assoc($revqq)) {
								$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
								$intTotalRevenue  = 0;
								
								foreach ($arrRevenueRanges as $arrRange2) {
									$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
												 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
												 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
									
									$intCurrentRevenue = getRevenue($strRevWhere, $revww['producttype']);
									$intTotalRevenue    += $intCurrentRevenue;
									$row                 = array();
									$row['merchant_id']  = $revww['merchant_id'];
									$row['affiliate_id'] = $revww['affiliate_id'];
									$row['banner_id']    = 0;
									$row['rdate']        = $arrRange2['from'];
									$row['amount']       = $intCurrentRevenue;
									$row['isFTD']        = false;
								  
									$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									
									$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
									
									unset($arrRange2, $strRevWhere);
								}
								
								$netRevenue = $intTotalRevenue;
								$refferelArray[$arrRes['refer_url']]['netRevenue'] += $netRevenue;
								
								
					}
					
						
					$sql = "select * from merchants where producttype = 'Forex' and valid =1";
					$totalqq = function_mysql_query($sql,__FILE__);
					
					while ($merchantww  = mysql_fetch_assoc($totalqq)) {
							$sql = 'SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO FROM data_stats ds '
									. 'WHERE ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
									. ' and ds.trader_id=' . $trader_id
									. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
									. " and ds.merchant_id = " . $arrRes['merchant_id']
									 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
									  .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'');
							
							$traderStatsQ = function_mysql_query($sql,__FILE__);
							
							while($ts = mysql_fetch_assoc($traderStatsQ)){
									$spreadAmount = $ts['totalSpread'];
									$volume += $ts['totalTO'];
									
									$refferelArray[$arrRes['refer_url']]['volume'] += $ts['totalTO'];
									
									$pnl = $ts['totalPnl'];
							}
									
									
							$totalLots  = 0;
														
						
								
							$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds '
							 . 'WHERE  ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
							 . ' and ds.trader_id=' . $trader_id
							 . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
							 . (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
							 . " and ds.merchant_id = " . $arrRes['merchant_id']
							 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
							   .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'');
							
							$traderStatsQ = function_mysql_query($sql,__FILE__);
							$earliestTimeForLot = date('Y-m-d');
							while($ts = mysql_fetch_assoc($traderStatsQ)){
								
								if($ts['affiliate_id']==null) {
										continue;
								}
				
								// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
										$totalLots  = $ts['totalTurnOver'];
										// echo $totalLots
											$tradersProccessedForLots[$arrRes['merchant_id'] . '-' . $ts['trader_id']] = $arrRes['id'] . '-' . $ts['trader_id'];
											$lotdate = $ts['rdate'];
											$ex = explode(' ' , $lotdate);
											$lotdate = $ex[0];
												if ($earliestTimeForLot>$lotdate)
												$earliestTimeForLot = $lotdate;
											$row = [
														'merchant_id'  => $arrRes['merchant_id'],
														'affiliate_id' => $ts['affiliate_id'],
														'rdate'        => $earliestTimeForLot,
														'banner_id'    => $ts['banner_id'],
														'trader_id'    => $ts['trader_id'],
														'profile_id'   => $ts['profile_id'],
														'type'       => 'lots',
														'amount'       =>  $totalLots,
											];
										$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
										//echo 'com: ' . $a .'<br>';
										$totalCom = $a;
										$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
								// }
								
							}
						}
						}						
					}// trader id empty loop end
				 }
				}//uid 0 loop end	 
			 }
			
				foreach($refferelArray as $key=>$data){
					$refer_url = $data['refer_url'];
					if(strlen($data['refer_url'])>50)
						$refer_url = substr($data['refer_url'],0,49). "...";
					$listReport .= '
                        <tr>
                            <td><a href="'.$data['refer_url'].'" target="_blank">'.$refer_url.'</a></td>
                            <td>'.$data['merchant_id'].'</td>
                            <td>'.$data['merchant_name'].'</td>
                            <td>'.$data['ip'].'</td>
                            <td>'.$data['country'].'</td>
                            <td>'.$data['affiliate_id'].'</td>
							<td><a href="/admin/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['username'].'</a></td>
                            <td>'.$data['profile_id'].'</td>
                            ' . ($is_admin ? '<td>'.$data['group'].'</td>' : '' ) . '
                            <td>'.$data['clicks'].'</td>
							<td>'.$data['views'].'</td>
							<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=lead&country_id='.$data['country'].'">'.$data['leads'].'</a></td>
							<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=demo&country_id='.$data['country'].'">'.$data['demo'].'</a></td>
							<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=real&country_id='.$data['country'].'">'.$data['real'].'</a></td>
							<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=ftd&country_id='.$data['country'].'">'.$data['ftd'].'</a></td>
							<td>'.price($data['ftd_amount']).'</td>
							<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'type=totalftd&country_id='.$data['country'].'">'.$data['real_ftd'].'</a></td>
							<td>'.price($data['real_ftd_amount']).'</td>
							<td><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=deposit&country_id='.$data['country'].'">'.$data['depositingAccounts'].'</a></td>
							<td>'.price($data['sumDeposits']).'</td>
							<td style="text-align: center;">'.price($data['volume']).'</td>
							<td>'.price($data['bonus']).'</td>
							<td>'.price($data['withdrawal']).'</td>
							<td>'.price($data['chargeback']).'</td>
							<td style="text-align: center;">'.price($data['netRevenue']).'</td>
							<td>'.price($data['totalCom']).'</td>';
							
							$i++;
							
							$totalViews +=$data['views'];
							$totalClicks +=$data['clicks'];
							$totalLeadsAccounts += $data['leads'];
							$totalDemoAccounts += $data['demo'];
							$totalRealAccounts += $data['real'];
							$totalFTD += $data['ftd'];
							$totalDeposits += $data['depositingAccounts'];
							$totalFTDAmount += $data['ftd_amount'];
							$totalDepositAmount += $data['sumDeposits'];
							$totalVolume += $data['volume'];
							$totalBonusAmount += $data['bonus'];
							$totalWithdrawalAmount += $data['withdrawal'];
							$totalChargeBackAmount += $data['chargeback'];
							$totalNetRevenue += $data['netRevenue'];
							$totalComs += $data['totalCom'];
							$totalRealFtd += $data['real_ftd'];
							$totalRealFtdAmount += $data['real_ftd_amount'];
				}
		//}	
		
		$set->totalRows = $l;
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="traffic" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					'.(isAdmin() ? '<td>'.lang('Group').'</td>' : '').'
					<td>'.lang('Merchant').'</td>
					<!--td>'.lang('Search Type').'</td-->
					
					<td>'.lang('Affiliate ID').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					
					
					'.(isAdmin() ? '
					<td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                    </td>' : '').'
												
					
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<!--td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td-->
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" style="width: 80px;" /></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			
			</div>
			
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2200px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2200" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
					
						
						<th>'.lang('Refer URL').'</th>
						<th style="text-align: center;">'.lang('Merchant ID').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Last Click IP').'</th>
						<th>'.lang('Last Click Country').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Affiliate Username').'</th>
						<th>'.lang('Profile ID').'</th>
						'. (isAdmin() ? '<th>'.lang('Group ID').'</th>' : ''). '
						<th style="text-align: center;">'.lang('All Time Clicks').'</th>
						<th style="text-align: center;">'.lang('All Time Views').'</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
					
						
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						'. ($is_admin ? '<th></th>' : ''). '
						<th></th>
						<th></th>
						<th>'.($totalClicks).'</th>
						<th>'.($totalViews).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr,'Traffic');
		$set->content.=$tableStr.'</div>'.getURLPager();
		theme();
		break;
	
	
	
                
	case "banner":
		$set->pageTitle = lang('Creative Report');
                $where = ' 1 = 1 ';
            
                /**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		if ($banner_id) {
                    $where .= " AND banner_id='".$banner_id."' ";
                }
				
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
                }
                
		if ($profile_id) {
                    $where  .= " AND profile_id='".$profile_id."' ";
                }
              

		
		$merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
		foreach ($merchantsAr as $arrMerchant) {
						
				if (strtolower($arrMerchant['producttype'])=='forex')
						$displayForex = 1;
					$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}

					
					
			  
		$sql = "SELECT merchant_id,id,affiliate_id FROM merchants_creative where " . str_replace('banner_id=','id=', $where) . " and valid=1 ";
		/* 
                     . "WHERE type='traffic' " . $where . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                     . " GROUP BY banner_id"; */
		// die ($sql);
		$qq = function_mysql_query($sql,__FILE__);
		
		// $ww['totalViews']=0;
		
		while ($ww = mysql_fetch_assoc($qq)) {
			// var_dump($ww);
			// die();
                    $ww['banner_id'] = $ww['id'];
                    $arrClicksAndImpressions = getClicksAndImpressions($from, $to, $ww['merchant_id'], $affiliate_id, $group_id,$ww['banner_id']);
					 
					 /* if ($ww['banner_id']==21) {
						var_dump($ww);
						var_dump($arrClicksAndImpressions);
						die;
					}  */
					
					$ww['totalViews']        = $arrClicksAndImpressions['impressions'];
                    $ww['totalClicks']       = $arrClicksAndImpressions['clicks'];
                    
                    $sql = "SELECT l.title as language , mc.id, mc.title, mc.type,mc.width,mc.height FROM merchants_creative mc left join languages l on l.id = mc.language_id WHERE mc.id = '" . $ww['banner_id'] . "' ";
                    $bannerInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		    
                    if ($type && $bannerInfo['type'] != $type) {
                        continue;
                    }
                    
                    $l = 0;
                    $totalLeads=0;
                    $totalDemo=0;
                    $totalReal=0;
                    $ftd=0;
                    $ftd_amount=0;
                    $real_ftd = 0;
                    $real_ftd_amount = 0;
                    $netRevenue = 0;
                    $depositingAccounts=0;
                    $sumDeposits=0;
                    $bonus=0;
                    $chargeback = 0;
                    $cpaAmount=0;
                    $withdrawal=0;
                    $volume=0;
                    $lots=0;
					$depositsAmount=0;
                    $totalCom=0;
                    
                    // $sql = 'SELECT * FROM merchants WHERE valid = 1 AND id = ' . $ww['merchant_id'];
					$merchantww = getMerchants($ww['merchant_id'],1);
                    // $merchantww = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					
                    
                    $formula = $merchantww['rev_formula'];
                    $merchantID = $merchantww['id'];
                    $merchantName = strtolower($merchantww['name']);
                    
                    $sql = "SELECT * FROM data_reg "
                         . "WHERE " . $where . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "' and banner_id = " . $ww['banner_id'] . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
                    
                    $regqq = function_mysql_query($sql,__FILE__);
                    
                    $arrTierCplCountCommissionParams = [];
						// die ($sql);
                    while ($regww = mysql_fetch_assoc($regqq)) {
                        
						
						
						
						$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
						
						
						/* 
						
						$sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                             . "WHERE affiliate_id = '" . $regww['affiliate_id'] . "' "
                             . "AND merchant_id = '" . $regww['merchant_id'] . "' AND dealType = 'tier' "
                             . "ORDER BY id DESC "
                             . "LIMIT 0, 1;";
                        
                        $strAffDealType   = mysql_fetch_assoc(function_mysql_query($sql,__FILE__)); */
                        $boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
                        
                        if ($regww['type'] == "lead") $totalLeads++;
                        if ($regww['type'] == "demo") $totalDemo++;
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
                                
                                $totalCom += getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
                            } else {
                                // TIER CPL.
                                if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
                                    $arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
                                } else {
                                    $arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
                                        'from'                => $from,
                                        'to'                  => $to,
                                        'onlyRevShare'        => 0,
                                        'groupId'             => (is_null($group_id ? -1 : $group_id)),
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
                            
                            unset($arrTmp);
                            $totalReal++;
                        }
                    }
                    
                    
                    // TIER CPL.
                    foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
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
		    
                    
                    $arrFtds  = getTotalFtds($from, $to, $ww['affiliate_id'], $ww['merchant_id'], 0, (is_null($group_id) ? 0 : $group_id),$ww['banner_id']);
                    
/*                     $size = sizeOf($arrFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrFtd = $arrFtds[$ftdCount] ;
 */				
 
					foreach ($arrFtds as $arrFtd) {
					
						
						
                        $real_ftd++;
                        $real_ftd_amount += $arrFtd['amount'];
                        
                        $beforeNewFTD = $ftd;
                        getFtdByDealType($ww['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
                        
                        if ($beforeNewFTD != $ftd) {
                            $arrFtd['isFTD'] = true;
                            $totalCom += getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
                        }
                        unset($arrFtd);
                    }
                    // echo $real_ftd_amount .'<br>';
                    
                    
                    $sql = "SELECT *, tb1.type AS data_sales_type  FROM data_sales as tb1 "
                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                            . "WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND tb1.banner_id='".$bannerInfo['id'] 
                            . "' AND tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
							. (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'');
		    
                    $salesqq = function_mysql_query($sql,__FILE__);
                    
                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                        //if ($salesww['type'] == 1 || $salesww['type'] == 'deposit') { // OLD.
						if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
                            $depositingAccounts++;
                            $sumDeposits += $salesww['amount'];
							$depositsAmount+=$salesww['amount'];
                        }
                        
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
                                'amount'       => $salesww['amount'],
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
					
					
					if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                        $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantww['id'], $ww['id'], $arrDealTypeDefaults);
                        $intTotalRevenue  = 0;
                        
                        foreach ($arrRevenueRanges as $arrRange2) {
                            $strRevWhere = 'WHERE merchant_id = ' . $merchantww['id'] . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                         . '" AND affiliate_id = "' . $ww['id'] . '" ' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
										 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
                            
                            $intCurrentRevenue = getRevenue($strRevWhere, $merchantww['producttype']);
                            
                            $intTotalRevenue    += $intCurrentRevenue;
                            $row                 = array();
                            $row['merchant_id']  = $merchantww['id'];
                            $row['affiliate_id'] = $ww['id'];
                            $row['banner_id']    = 0;
                            $row['rdate']        = $arrRange2['from'];
                            $row['amount']       = $intCurrentRevenue;
                            $row['isFTD']        = false;
                            $totalCom           += getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
                            unset($arrRange2, $strRevWhere);
                        }
                        
                        $netRevenue += $intTotalRevenue;
                        
                    } else {
                        // $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
						$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
                    }
					
                    
                    
                    if(strtolower($merchantww['producttype']) == 'forex') {
                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                . 'WHERE merchant_id="' . $merchantww['id'] . '" AND banner_id="'.$bannerInfo['id'] 
                                . '" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
								. (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
                        
                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                        
                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                $spreadAmount = $ts['totalSpread'];
                                $volume += $ts['totalTO'];
                                $pnl = $ts['totalPnl'];
                        }
						
						
	$totalLots  = 0;
											
							
							
						$sql = 'SELECT turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
                                         
										 . 'WHERE merchant_id = "' . $merchantww['id'] . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '')
											. (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'')
											. (!empty($banner_id) ? ' and banner_id = ' . $bannerInfo['id'] :'');
											
											// die($sql);
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d');
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											
											if($ts['affiliate_id']==null) {
													continue;
											}
							
											// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
													$totalLots  = $ts['totalTurnOver'];
													// echo $totalLots
														$tradersProccessedForLots[$merchantww['id'] . '-' . $ts['trader_id']] = $ww['id'] . '-' . $ts['trader_id'];
														$lotdate = $ts['rdate'];
														$ex = explode(' ' , $lotdate);
														$lotdate = $ex[0];
															if ($earliestTimeForLot>$lotdate)
															$earliestTimeForLot = $lotdate;
														$row = [
																	'merchant_id'  => $merchantww['id'],
																	'affiliate_id' => $ts['affiliate_id'],
																	'rdate'        => $earliestTimeForLot,
																	'banner_id'    => $ts['banner_id'],
																	'trader_id'    => $ts['trader_id'],
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
													$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
													// echo 'com: ' . $a .'<br>';
													$totalCom += $a;
											// }
										}
				     }
                    
                    
                    
                    if (strtolower($merchantww['producttype']) != 'binary') {
                        $sql = "SELECT DISTINCT affiliate_id AS id FROM data_stats "
                             . "WHERE merchant_id = " . $merchantww['id'] . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "' " 
                             . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
							 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
                        
                        $resource = function_mysql_query($sql,__FILE__);
                        
                        while ($arrRow = mysql_fetch_assoc($resource)) {
                            $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantww['id'], $arrRow['id'], $arrDealTypeDefaults);
                            $intTotalRevenue  = 0;
                            
                            foreach ($arrRevenueRanges as $arrRange) {
                                $strWhere = ' WHERE merchant_id=' . $merchantww['id'].' AND banner_id='.$ww['banner_id'] 
                                          . ' AND rdate BETWEEN "'.$arrRange['from'].'" AND "'.$arrRange['to'].'" ';
                                
                                $intCurrentRevenue = getRevenue($strWhere, $merchantww['producttype']);
                                
                                $intTotalRevenue    += $intCurrentRevenue;
                                $row                 = array();
                                $row['merchant_id']  = $merchantww['id'];
                                $row['affiliate_id'] = $arrRow['id'];
                                $row['banner_id']    = $ww['banner_id'];
                                $row['rdate']        = $arrRange['from'];
                                $row['amount']       = $intCurrentRevenue;
                                $row['isFTD']        = false;
                                $totalCom           += getCommission($arrRange['from'], $arrRange['to'], 1, -1, $arrDealTypeDefaults, $row);
                                unset($strWhere, $row, $arrRange);
                            }
                            unset($arrRow);
                        }
                        
                        $netRevenue += $intTotalRevenue;
                        
                    } else {
                        // $netRevenue = round($sumDeposits - ($bonus + $withdrawal + $chargeback));
						$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
                    }
                    
		    
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$bannerInfo['id'].'</td>
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="NewWin(\'/admin/creative.php?act=edit_banner&id='.$bannerInfo['id'].'\',\'editbanner_'.$bannerInfo['id'].'\',\'1000\',\'800\',\'1\');">'.lang('Edit').'</a></td>
				<td style="text-align: left;">'.($bannerInfo['id'] ? $bannerInfo['title'] : lang('BANNER REMOVED')).'</td>
				<td style="text-align: left;">'.$bannerInfo['language'].'</td>
				<td style="text-align: left;">'.($bannerInfo['width']>0 ? $bannerInfo['width'] : "").'</td>
				<td style="text-align: left;">'.($bannerInfo['height']>0 ? $bannerInfo['height'] : "").'</td>
				<td style="text-align: left;">'.ucwords($bannerInfo['type']).'</td>
				<td style="text-align: left;">'.$merchantww['name'].'</td>
				<td>'.@number_format($ww['totalViews'],0).'</td>
				<td>'.@number_format($ww['totalClicks'],0).'</td>
				<td>'.@number_format(($ww['totalClicks']/$ww['totalViews'])*100,2).' %</td>
				<td>'.@number_format(($totalReal/$ww['totalClicks'])*100,2).' %</td>
				<td>'.@number_format(($ftd/$ww['totalClicks'])*100,2).' %</td>
				<td>'.@price($totalCom/$ww['totalClicks']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=lead">'.$totalLeads.'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=demo">'.$totalDemo.'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=real">'.$totalReal.'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=ftd">'.$ftd.'</a></td>
				<td>'.price($ftd_amount).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=totalftd">'.$real_ftd.'</a></td>
				<td>'.price($real_ftd_amount).'</td>
				<td><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
				<td>'.price($sumDeposits).'</td>
				<td style="text-align: center;">'.price($volume).'</td>
				<td>'.price($bonus).'</td>
				<td>'.price($withdrawal).'</td>
				<td>'.price($chargeback).'</td>
				<td style="text-align: center;">'.price($netRevenue).'</td>
				<td>'.price($totalCom).'</td>
			</tr>';
			
			$totalImpressions += $ww['totalViews'];
			$totalClicks += $ww['totalClicks'];
			$totalLeadsAccounts += $totalLeads;
			$totalDemoAccounts += $totalDemo;
			$totalRealAccounts += $totalReal;
			$totalFTD += $ftd;
			$totalDeposits += $depositingAccounts;
			$totalFTDAmount += $ftd_amount;
			$totalDepositAmount += $sumDeposits;
			$totalVolume += $volume;
			$totalBonusAmount += $bonus;
			$totalWithdrawalAmount += $withdrawal;
			$totalChargeBackAmount += $chargeback;
			$totalNetRevenue += $netRevenue;
			$totalComs += $totalCom;
			$totalRealFtd += $real_ftd;
			$totalRealFtdAmount += $real_ftd_amount;
                        $l++;
		// echo $ftd_amount.'<br>';
		$ftd_amount = $real_ftd_amount = 0;
		// $totalLeads = $totalDemo = $totalReal = $ftd = $depositingAccounts = $ftd_amount = $sumDeposits = $volume = $bonus = $withdrawal = $chargeback= $netRevenue= $totalCom= $real_ftd= $real_ftd_amount = 0;
						
		}
                
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->totalRows  = $l;
		$set->sortTable  = 1;
		$set->content   .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="banner" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Banner ID').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td>'.lang('Creative Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" /></td>
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td>
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
						<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>
						<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>
						<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
						<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>
						<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
						<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>
						<option value="coupon" '.($type == "coupon" ? 'selected' : '').'>'.lang('Coupon').'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" style="width: 2400px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2400" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Actions').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th style="text-align: left;">'.lang('Language').'</th>
						<th style="text-align: left;">'.lang('Width').'</th>
						<th style="text-align: left;">'.lang('Height').'</th>
						<th>'.lang('Type').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr, 'Banner');
		$set->content.=$tableStr.'</div>'.getPager();
		theme();
		break;
	


                
        case "trader":
                $set->pageTitle = lang(ptitle('Trader Report'));
                $l = 0;
                $arrResultSet = [];
                $ftdExist = [];
                $totalFTD = 0;
                $totalTotalDeposit = 0;
                $totalDepositAmount = 0;
                $totalVolumeAmount = 0;
                $totalBonusAmount = 0;
                $totalWithdrawalAmount = 0;
                $totalChargeBackAmount = 0;
                $totalNetRevenue = 0;
                $totalTrades = 0;
                $totalTotalCom  = 0;
                $arrTradersPerMerchants = [];
                
                $intTmpMerchantId = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;
                $strWhereMerchantId = isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id . ' ' : '';
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
				 
				
                $resourceWallets = function_mysql_query($sql,__FILE__);
                
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
				
				
				
				
                $sql = "SELECT * FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                $resourceMerchanrs = function_mysql_query($sql,__FILE__);
                
                while ($arrMerchant = mysql_fetch_assoc($resourceMerchanrs)) {
					
					$earliestTimeForNetRev = date('Y-m-d H:i:s');
					
					
                    $int_merchant_id = empty($intTmpMerchantId) ? $arrMerchant['id'] : $intTmpMerchantId;
                    $where = ' AND merchant_id = ' . $int_merchant_id;
                    
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($group_id) $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
                    if ($param) $where .= " AND freeParam='".$param."' ";
                    if ($param2) $where .= " AND freeParam2='".$param2."' ";
                    
                    if ($trader_alias) {
                        $qry = "select trader_id from data_reg  where  lower(trader_alias) like ('%". mysql_real_escape_string(strtolower($trader_alias))."%')";
                        $row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
                        
                        if (!empty($row['trader_id'])) {
                            $trader_id = $row['trader_id'];
                            
                            if ($trader_id) {
                                $where .= " AND trader_id='".$trader_id."' ";
                            }
                            
                            if (empty($trader_id)) {
                                $trader_id = $row['trader_id'];
                            }
                        }
						else {
                              $where .= " AND trader_alias='".$trader_alias."' ";
							
							
						}
                    }
		    	
                    if ($country_id) {
                        $where .= " AND country='".$country_id."' ";
                    }
                    
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
			
			// all demos and frozen accounts.
			$qry = "select id,status from data_reg where merchant_id = " . $int_merchant_id . " AND (status = 'frozen' or type= 'demo') ";
			$invalidResources = function_mysql_query($qry,__FILE__);
			$invalidTraders = array();
			$frozenTraders = array();
			while ($invalidRow = mysql_fetch_assoc($invalidResources)) {
				$invalidTraders[] = $invalidRow['id'];
				if ($invalidRow['status']=='frozen') {
					$frozenTraders[] = $invalidRow['id'];
				}
			}
		
			
			if ($type == 'ftd' || $type == 'totalftd') {
                            $arrTotalFtds = getTotalFtds(
                                $from, $to, $affiliate_id, $arrMerchant['id'], $arrMerchant['wallet_id'], 
                                $group_id, $banner_id, $profile_id, '', $trader_id
                            );
                            
                            if ($type == 'ftd') {
                                foreach ($arrTotalFtds as $arrRes) {
									/* $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ; */
									
									if ($earliestTimeForNetRev>$arrRes['rdate'])
								$earliestTimeForNetRev = $arrRes['rdate'];
							
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($int_merchant_id, $arrRes, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);
                                    
                                    if ($beforeNewFTD != $ftd) {
                                        $firstDeposit           = $arrRes;
                                        $ftdAmount              = $firstDeposit['amount'];
                                        $arrRes['isFTD']        = true;
                                        $totalCom               = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $arrRes);
                                        $arrRes['firstDeposit'] = $firstDeposit;
                                        $arrRes['ftdAmount']    = $ftdAmount;
                                        $arrRes['totalCom']     = $totalCom;
                                        $arrResultSet[]         = $arrRes;
                                    }
                                    unset($arrRes);
                                }
                                
                            } elseif ($type == 'totalftd') {
                                foreach ($arrTotalFtds as $arrRes) {
										/* $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ;
									 */
									
									
									if ($earliestTimeForNetRev>$arrRes['rdate'])
								$earliestTimeForNetRev = $arrRes['rdate'];
							
							
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($int_merchant_id, $arrRes, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);
                                    
                                    $firstDeposit           = $arrRes;
                                    $ftdAmount              = $firstDeposit['amount'];
                                    $arrRes['isFTD']        = true;
                                    $totalCom               = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $arrRes);
                                    $arrRes['firstDeposit'] = $firstDeposit;
                                    $arrRes['ftdAmount']    = $ftdAmount;
                                    $arrRes['totalCom']     = $totalCom;
                                    $arrResultSet[]         = $arrRes;
                                    unset($arrRes);
                                }
                            }
			    
			} elseif ($type == 'deposit') {
                            $where = str_replace('merchant_id', 'ds.merchant_id', $where);
                            $where = str_replace('trader_id', 'ds.trader_id', $where);
                            $where = str_replace('group_id', 'ds.group_id', $where);
                            $where = str_replace('affiliate_id', 'ds.affiliate_id', $where);
                            $where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            
                        /*     $sql = "SELECT ds.* FROM data_sales AS ds
                                    INNER JOIN data_reg AS dr ON dr.trader_id = ds.trader_id AND dr.merchant_id = ds.merchant_id AND dr.status <> 'frozen' AND dr.type <> 'demo' 
                                    WHERE " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'deposit' "
                                            . $where
                                    . " ORDER BY ds.rdate ASC;";
                             */
							$sql = "SELECT ds.* FROM data_sales AS ds
                                  
                                    WHERE 2=2 and " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'deposit' "
                                            . $where
                                    . " ORDER BY ds.rdate ASC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrDeposit = mysql_fetch_assoc($resource)) {
								
								
								if (!in_array($arrDeposit['trader_id'], $invalidTraders)) {
                                $arrResultSet[] = $arrDeposit;
                                unset($arrDeposit);
								}
                            }
                            
                        } elseif ($type == 'revenue') {
                            $where = str_replace('merchant_id', 'ds.merchant_id', $where);
                            $where = str_replace('trader_id', 'ds.trader_id', $where);
                            $where = str_replace('group_id', 'ds.group_id', $where);
                            $where = str_replace('affiliate_id', 'ds.affiliate_id', $where);
							$where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            
/*                             $sql = "SELECT ds.* FROM data_sales AS ds
                                    INNER JOIN data_reg AS dr ON dr.trader_id = ds.trader_id AND dr.merchant_id = ds.merchant_id AND dr.status <> 'frozen' AND dr.type <> 'demo' 
                                    WHERE " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'revenue' "
                                            . $where
                                    . " GROUP BY ds.trader_id "
                                    . " ORDER BY ds.rdate ASC;";
 */                            
							$sql = "SELECT ds.* FROM data_sales AS ds
                                    WHERE 3=3 and " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'revenue' "
                                            . $where
                                    . " GROUP BY ds.trader_id "
                                    . " ORDER BY ds.rdate ASC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrRevenue = mysql_fetch_assoc($resource)) {
								if (!in_array($arrDeposit['trader_id'], $invalidTraders)) {
                                $arrResultSet[] = $arrRevenue;
                                unset($arrRevenue);
								}
                            }
                            
                        } elseif ($type == 'frozen') {
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status = 'frozen' "
                                    . "GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                            
                        } elseif ($type == 'demo') {
							// die ('gerger');
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status <> 'frozen'  AND type ='demo' "
                                    . "GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                        } elseif ($type == 'lead') {
							// die ('gerger');
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status <> 'frozen'  AND type ='lead' "
                                    . "GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                            // die ($sql);
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                        
						} elseif ($type == 'allaccounts') {
							// die ('gerger');
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "'  "
                                    . "GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                            // die ($sql);
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                        
						
						} else {
							// die ('gerger');
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status <> 'frozen' and type='real' "
                                    . "GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                        }
                        
                        
                        unset($arrMerchant);
                } // END of "merchants" loop.
				
				
					// $sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
					
                    $merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
					foreach ($merchantsAr as $arrMerchant) {
						// var_dump($arrMerchant);
						// echo '<br>';
						
						
						if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1;
					
						$merchantsArray[$arrMerchant['id']] = $arrMerchant;
					}
					
			// echo $sql . '<Br>';
			// die('dis: ' . $displayForex);
					$tradersProccessedForLots= array();
               
			   /* $size = sizeOf($arrResultSet);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrResultSet[$ftdCount] ;
									 */
			   foreach ($arrResultSet as $arrRes) {
					
					// var_dump($arrRes);
					// die();
					//old nethod
/* 
					$sql = 'SELECT * FROM merchants WHERE id = ' . $arrRes['merchant_id'] . ' AND valid = 1 LIMIT 0, 1;';
					$arrMerchant = mysql_fetch_assoc(function_mysql_query($sql,__FILE__)); 
					*/
                    
					$arrMerchant = $merchantsArray[$arrRes['merchant_id']];
					// var_dump($arrMerchant);
					// die();
					
					
					
					//HACK FIX - TEMPORARY - NEED TO BE TESTED	!!!
					// $ftdAmount = empty($arrRes['ftdAmount']) ? $ftdAmount : $arrRes['ftdAmount'];
					$ftdAmount = 0;
					// $volumeAmount = 0;
					// $withdrawalAmount = 0;
					
					
                    $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    $totalCom = 0;
                    $int_merchant_id = $arrRes['merchant_id'];
                    
                    
                    if ($type == 'ftd' || $type == 'totalftd') {
                        $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    } else {
                        $firstDeposit = [];
                    }
                    
                    
                    if (
                        $type == 'ftd' || 
                        $type == 'totalftd' || 
                        $type == 'deposit' || 
                        $type == 'revenue'
                    ) {
                        $ftd = $totalTraders = $depositAmount = $total_deposits = $volumeAmount = 0;
                        $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                        $spreadAmount = $pnl = 0;
			
                        $strTmpWhere = '';
                        $intTmpTraderId = empty($trader_id) ? $arrRes['trader_id'] : $trader_id;  
                        $intTmpGroupId = empty($group_id) ? $arrRes['group_id'] : $group_id;
                        $intTmpMerchantId = empty($int_merchant_id) ? $arrRes['merchant_id'] : $int_merchant_id;
                        $strTmpWhere .= empty($intTmpTraderId) ? '' : ' AND trader_id = ' . $intTmpTraderId . ' ';
                        $strTmpWhere .= empty($intTmpGroupId) ? '' : ' AND group_id = ' . $intTmpGroupId . ' ';
                        $strTmpWhere .= empty($intTmpMerchantId) ? '' : ' AND merchant_id = ' . $intTmpMerchantId . ' ';
                        $totalCom = empty($arrRes['totalCom']) ? $totalCom : $arrRes['totalCom'];
                        
                        $sql = "SELECT * FROM data_reg WHERE 1 = 1 AND status <> 'frozen' " . $strTmpWhere . " LIMIT 0, 1;";
                        
                        $traderInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                        $arrRes['trader_alias'] = $traderInfo['trader_alias'];
                        $arrRes['email'] = $traderInfo['email'];
                        $arrRes['country']      = $traderInfo['country'];
                        $arrRes['rdate']        = $type != 'deposit' ? $traderInfo['rdate'] : $arrRes['rdate'];
                        $arrRes['orgType']      = $arrRes['type'];
                        $arrRes['salesType']    = $arrRes['type'];
                        $arrRes['type']         = $traderInfo['type'];
                        $arrRes['banner_id']    = $traderInfo['banner_id'];
                        $arrRes['status']       = $traderInfo['status'];
                        $arrRes['profile_id']   = $traderInfo['profile_id'];
                        $arrRes['freeParam']    = $traderInfo['freeParam'];
                        $arrRes['freeParam2']    = $traderInfo['freeParam2'];
                        $arrRes['saleStatus']   = $traderInfo['saleStatus'];
                        $arrRes['lastTimeActive']   = $traderInfo['lastTimeActive'];
                        unset($intTmpTraderId, $intTmpGroupId, $strTmpWhere, $intTmpMerchantId);
                    }
                    
                    
                    $depositAmount = 0;
					
					
					
					  // BANNER info retrieval.
					$bannerInfo = getCreativeInfo($arrRes['banner_id']);
								
								
                    if ($type != 'deposit' || true) { //hack by nir
                    /*     $sql = "SELECT ds.* FROM data_sales AS ds
                                INNER JOIN data_reg AS dr ON dr.trader_id = ds.trader_id AND dr.merchant_id = ds.merchant_id AND dr.status <> 'frozen' AND (1=1 or dr.type <> 'demo' )
                                WHERE " . $globalWhere . " ds.trader_id = " . $arrRes['trader_id'] 
                                        //. " AND ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                        . " AND ds.merchant_id = " . $int_merchant_id
                                . " ORDER BY ds.rdate ASC;";
								 */ 
						 $sql = "SELECT ds.* FROM data_sales AS ds
                                
                                WHERE 4=4 and " . $globalWhere . " ds.trader_id = " . $arrRes['trader_id'] 
                                        //. " AND ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                        . " AND ds.merchant_id = " . $int_merchant_id
                                . " ORDER BY ds.rdate ASC;";
								
						//if (isset($qa)) echo 'qa: ', $sql, '<hr>';/////////////////////////////////////////////
						// die ($sql);
                        $resource = function_mysql_query($sql,__FILE__);
                        $total_deposits = 0;

                        while ($arrAmount = mysql_fetch_assoc($resource)) {
							
							if (in_array($arrAmount['trader_id'], $frozenTraders)) {
								// die ('greger222');
								continue;
								
							}
							
							
							
								
								
								if (strtolower($arrAmount['type'])=='deposit')								
                            $arrRes['tranz_id'] = $arrAmount['tranz_id'];
						
						// var_dump($ftdAmount);
						// die();
							if ((strtolower($type)=='ftd' || strtolower($type)=='totalftd')){// && isset($ftdAmount['amount']) && $ftdAmount['amount']>0){
								
								//$firstDeposit['rdate'] = $ftdAmount['rdate'];
						//		$firstDeposit['id'] = $arrAmftdAmountount['id'];
									// var_dump($ftdAmount);
									// die();
									$ftdAmount = $firstDeposit['amount'];
							}
							else 	if (($arrRes['tranz_id'] !=''  && $ftdAmount==0 && strtolower($arrAmount['type'])=='deposit'))  {
							// else 	if (($arrRes['tranz_id'] !=''  && $ftdAmount==0))  {
								$ftdAmount = $arrAmount['amount'];
								
								$firstDeposit['rdate'] = $arrAmount['rdate'];
								$firstDeposit['id'] = $arrAmount['id'];
							}
							

							if ($earliestTimeForNetRev>$arrAmount['rdate'])
								$earliestTimeForNetRev = $arrAmount['rdate'];
						
                            
                            if ($arrAmount['type'] == 'deposit') {
                                $depositAmount += $arrAmount['amount'];
                                $total_deposits++;
                            } elseif ($arrAmount['type'] == 'bonus') {
                                $bonusAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'withdrawal') {
                                $withdrawalAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'chargeback') {
                                $chargebackAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'volume') {
                                $volumeAmount += $arrAmount['amount'];
                                $totalTraders++;
								
								/* 
								$volume += $salesww['amount']; */
                                            // die ('gerg');
											$arrTmp = [
                                                'merchant_id'  => $arrRes['merchant_id'],
                                                'affiliate_id' => $arrRes['affiliate_id'],
                                                'rdate'        => $arrRes['rdate'],
                                                'banner_id'    => $arrRes['banner_id'],
                                                'trader_id'    => $arrRes['trader_id'],
                                                'profile_id'   => $arrRes['profile_id'],
                                                'type'       => 'volume',
                                                'amount'       => $arrAmount['amount'],
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
                            unset($arrAmount);
							
                        }
					

					//lots 
					
					
						if (strtolower($arrMerchant['producttype']) == 'forex') {
							
					
						$totalLots  = 0;
						
						
						if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$tradersProccessedForLots)) {
							$tradersProccessedForLots[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
							
						$sql = 'SELECT  * FROM data_stats 
                                         WHERE merchant_id = "' . $arrMerchant['id'] . '" and  trader_id = "' . $arrRes['trader_id'] . '" ' ;
                           // die ($sql);
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d H:i:s');
										while($ts = mysql_fetch_assoc($traderStatsQ)){
													
													
													if ($earliestTimeForLot>$ts['rdate'])
															$earliestTimeForLot = $ts['rdate'];
                                            $totalLots  += $ts['amount'];
                                        }

							
							
							$row = [
                                            'merchant_id'  => $arrMerchant['id'],
                                            'affiliate_id' => $arrRes['affiliate_id'],
                                            'rdate'        => $earliestTimeForLot,
                                            'banner_id'    => $arrRes['banner_id'],
                                            'trader_id'    => $arrRes['trader_id'],
                                            'profile_id'   => $arrRes['profile_id'],
                                            'type'       => 'lots',
                                         'amount'       =>  $totalLots,
										 ];
										 
						//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
						// die ('getcom: ' .$a );
						
						// die();
							$totalCom += getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
							
						}
						
						
						
						
						
						}
                        
                    } 
		                    
                    if ($type != 'ftd' && $type != 'totalftd') {
                        //$ftd = $totalTraders = $depositAmount = $total_deposits = $volumeAmount = 0;
                       // $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                       // $spreadAmount = $pnl = $ftdAmount = 0;
                        $ftdUsers = '';
                        
                        $arrTotalFtds = getTotalFtds(
                            $from, $to, $arrRes['affiliate_id'], $arrRes['merchant_id'], $arrMerchant['wallet_id'], 
                            $arrRes['group_id'], $arrRes['banner_id'], $arrRes['profile_id'], '', $arrRes['trader_id']
                        );
						
						
						
	
                        foreach ($arrTotalFtds as $arrResLocal) {
				/* 			
									   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrResLocal = $arrTotalFtds[$ftdCount] ;
				 */					
									
                            $beforeNewFTD = $ftd;
                            getFtdByDealType($arrRes['merchant_id'], $arrResLocal, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);

                            if ($beforeNewFTD != $ftd) {
                                $firstDeposit = $arrResLocal;
								
                                $ftdAmount = $arrResLocal['amount'];
						
                                $arrResLocal['isFTD'] = true;


							if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$arrTradersPerMerchants)) {
							// $arrTradersPerMerchants[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
                                
                                // if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {

                                    $totalCom += getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrResLocal);
                                }
                            }
                            unset($arrResLocal);
                        }
                    }



                    if (strtolower($arrMerchant['producttype']) == 'sportsbetting' || strtolower($arrMerchant['producttype']) == 'casino') {
                        $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $arrRes['merchant_id'], $arrRes['affiliate_id'], $arrDealTypeDefaults);
                        $intTotalRevenue  = 0;
                        
                        foreach ($arrRevenueRanges as $arrRange) {
                            
							$arrAffiliate = getAffiliateRow($arrRes['affiliate_id']);
							
						
                            $arrMerchantsAffiliate = explode('|', $arrAffiliate['merchants']);
                            
                            if (!in_array($arrRes['merchant_id'], $arrMerchantsAffiliate)) {
                                continue;
                            }
                            
							
                            $intCurrentRevenue = getRevenue(
                                'WHERE merchant_id = ' . $arrRes['merchant_id'] . ' AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . 
                                '" AND affiliate_id = "' . $arrRes['affiliate_id'] . '" AND trader_id = ' . $arrRes['trader_id'] . ' ',
                                
								$arrMerchant['producttype'],0,0,0,0,0,0,$arrMerchant['rev_formula']
                            );
                            
                            $intTotalRevenue    += $intCurrentRevenue;
                            $row                 = [];
                            $row['merchant_id']  = $arrRes['merchant_id'];
                            $row['affiliate_id'] = $arrRes['affiliate_id'];
                            $row['banner_id']    = $arrRes['banner_id'];
                            $row['rdate']        = $arrRange['from'];
                            $row['amount']       = $intCurrentRevenue;
                            $row['trader_id']    = $arrRes['trader_id'];
                            $row['isFTD']        = false;
							
							
                            $totalCom           += getCommission($arrRange['from'], $arrRange['to'], 1, $group_id, $arrDealTypeDefaults, $row);
                            unset($arrRange);
                        }
                        
                        $netRevenue = $intTotalRevenue;
                        
                    } else {
						
						
						
$netRevenue =  round(getRevenue($where,$arrMerchant['producttype'],$depositAmount,$bonusAmount,$withdrawalAmount,0,0,0,$arrMerchant['rev_formula'],null,$chargebackAmount),2);
/* 0,0,0,0,0,0,$arrMerchant['rev_formula']
    $merchantType        = 'casino',
    $sumDeposits         = 0,
    $bonus               = 0,
    $withdrawal          = 0,
    $pnl                 = 0,
    $turnoverAmount      = 0,
    $spreadAmount        = 0,
    $formula             = 0,
    $intProfileId        = null */
	
	
                        // $netRevenue = round($depositAmount - ($withdrawalAmount + $bonusAmount + $chargebackAmount), 2);
						
               
					$row                 = [];
				  $row['merchant_id']  = $arrRes['merchant_id'];
                       $row['affiliate_id'] = $arrRes['affiliate_id'];
                            $row['banner_id']    = $arrRes['banner_id'];
                            $row['rdate']        = $earliestTimeForNetRev;
                            $row['amount']       = $netRevenue;
                            $row['trader_id']    = $arrRes['trader_id'];
                            $row['isFTD']        = false;
							   	
								
								
							    $totalCom           += getCommission($earliestTimeForNetRev, $to, 1, $group_id, $arrDealTypeDefaults, $row);

                    }
					
           
					
                    // AFFILIATE info retrieval.
                    
					$affInfo = getAffiliateRow($arrRes['affiliate_id']);
							
						
				/* 	$sql = "SELECT * FROM affiliates AS aff "
                            . " WHERE aff.valid = 1 AND id = " . $arrRes['affiliate_id']
                            . " LIMIT 0, 1;";

                    $affInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__)); */

                    if ($arrRes['type'] == 'real') {
                        $color = 'green';
                    } elseif ($arrRes['type'] == 'demo') {
                        $color = 'red';
                    } elseif ($arrRes['type'] == 'lead') {
                        $color = 'black';
                    }


                    // Check trader.
					$reason="";
                   
				   $sql = 'SELECT * FROM payments_details '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['merchant_id'] 
                            . '  LIMIT 0, 1;';
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (empty($chkTrader)) {
						$sql = 'SELECT * FROM traders_tag '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['merchant_id'] 
                            . '  LIMIT 0, 1;';
					
					// die ($sql);
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (!empty($chkTrader)) {
						
						$reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
					}
					
					}else {
					$reason = $chkTrader['reason'];
						
					}
// var_dump($chkTrader);
// die();
					
					// var_dump($arrRes);
					// die();

                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].'</td>
                            <td>'.$arrRes['trader_alias'].'</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.$arrRes['email'].'</td>' : '' ) . '
                            <td>'.($type == "deposit" ? date("d/m/Y", strtotime($traderInfo['rdate'])) : date("d/m/Y", strtotime($arrRes['rdate']))).'</td>
                            <td><span style="color: '.$color.';">'.$arrRes['type'].'</span></td>
                            <td>'.longCountry($arrRes['country']).'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
                            <td><a href="/admin/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
                            <td>'.$arrMerchant['id'].'</td>
                            <td>'.strtoupper($arrMerchant['name']).'</td>
                            <td style="text-align: left;">'.$bannerInfo['id'].'</td>
                            <td style="text-align: left;">'.$bannerInfo['title'].'</td>
                            <td>'.$bannerInfo['type'].'</td>
                            <td>'.$bannerInfo['language_name'].'</td>
                            <td>'.$arrRes['profile_id'].'</td>
                            <td>'.$arrRes['status'].'</td>
                            <td>'.$arrRes['freeParam'].'</td>
                            <td>'.$arrRes['freeParam2'].'</td>
                            <td>' . (isset($arrRes['tranz_id']) ? $arrRes['tranz_id'] : '') . '</td>
                            <td>'.($type == "deposit" ? date("d/m/Y", strtotime($arrRes['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y", strtotime($firstDeposit['rdate'])) : '')).'</td>
                            <td>'.price($ftdAmount).'</td>
							
                            <td>'.($depositAmount>0 && $ftdAmount>0 && $depositAmount > $ftdAmount ?  price($depositAmount-$ftdAmount) : "" ).'</td>
                            <td>'.($total_deposits>1 ? $total_deposits-1 : "" ).'</td>
                            <td><a href="/admin/reports.php?act=transactions&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
                            <td>'.price($depositAmount).'</td>
                            <td>'.price($volumeAmount).'</td>
                            <td>'.price($bonusAmount).'</td>
                            <td>'.price($withdrawalAmount).'</td>
                            <td>'.price($chargebackAmount).'</td>
                            <td>'.price($netRevenue).'</td>
                            <td>'.$totalTraders.'</td>
							'. ( $displayForex==1 ? 
                            '<td>'.$totalLots.'</td>' : '' ).'
                            <td>'.$arrRes['saleStatus'].'</td>
							'.($set->displayLastMessageFieldsOnReports ==1 ? '
                            <td>'.$arrRes['lastSaleNoteDate'].'</td>
                            <td>'.$arrRes['lastSaleNote'].'</td>':'').'
                            <td>'.($arrRes['lastTimeActive']=='1969-12-31 23:00:00' || $arrRes['lastTimeActive'] == '0000-00-00 00:00:00' ? '-' : $arrRes['lastTimeActive']).'</td>
                            <td>'.price($totalCom).'</td>
                            <td>'.$reason.'</td>
                        </tr>';
                    
					
					
                    if (!in_array($arrRes['merchant_id'] . '-' . $arrRes['trader_id'],   $arrTradersPerMerchants)) {
                        $arrTradersPerMerchants[] = $arrRes['merchant_id'] . '-' . $arrRes['trader_id']; //$arrRes['trader_id'];
                        $totalTotalCom += $totalCom;
                        $totalFTD += $ftdAmount;
                        $totalNetRevenue += $netRevenue;
					

					// die ($totalTotalCom);
					if ($_GET['deb']==1) {
					var_dump($arrTradersPerMerchants);
					die('totalcom: ' . $totalCom);
					}
					
					
                    }

                    $totalDepositAmount += $depositAmount;
                    $totalVolumeAmount += $volumeAmount;
                    $totalBonusAmount += $bonusAmount;
                    $totalTotalDeposit += $total_deposits;
                    $totalTrades += $totalTraders;
                    $totalLotsamount += $totalLots;
                    $totalWithdrawalAmount += $withdrawalAmount;
                    $totalChargeBackAmount += $chargebackAmount;
                    $ftdExist[] = $firstDeposit['trader_id'];
                    $l++;
					
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$totalTraders=$totalLots=0;
                }
                        
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form action="'.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="trader" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						<td>'.lang('Affiliate ID').'</td>
						<td>'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						<td>'.lang(ptitle('Trader Alias')).'</td>
						<td>'.lang(ptitle('Parameter')).'</td>
						<td>'.lang(ptitle('Parameter2')).'</td>
						<td>'.lang('Group').'</td>
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_alias" value="'.$trader_alias.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param" value="'.$param.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="'.$param2.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td-->
                                                <td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                                                </td>
						<td>
							<select name="type" style="width: 100px;">
								<option value="allaccounts" '.($type == "allaccounts" ? 'selected' : '').'>'.lang(ptitle('All Accounts')).'</option>
								<option value="real" '.($type == "real" ? 'selected' : '').'>'.lang(ptitle('Accounts')).'</option>
								'.($hideDemoAndLeads? "": '<option value="lead" '.($type == "lead" ? 'selected' : '').'>'.lang(ptitle('Lead')).'</option>
								<option value="demo" '.($type == "demo" ? 'selected' : '').'>'.lang(ptitle('Demo')).'</option>').'
								<!--option value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option-->
								<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>
								<!--option value="revenue" '.($type == "revenue" ? 'selected' : '').'>'.lang('Revenue').'</option-->
                                                                <option value="totalftd" '.($type == "totalftd" ? 'selected' : '').'>'.lang('Total FTD').'</option>
                                                                '.(!$hideDemoAndLeads? "": '<option value="frozen" '.($type == "frozen" ? 'selected' : '').'>'.lang('Frozen').'</option>').'
							</select>
						</td>
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2600px;">'.lang('Report Results').'</div>
			<div style="background: #F8F8F8;">';
				$tableStr ='<table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).'</th>
						<th>'.lang(ptitle('Trader Alias')).'</th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th>'.lang(ptitle('Email')).'</th>' : '' ) . '
						<th>'.lang('Registration Date').'</th>
						<th>'.lang(ptitle('Trader Status')).'</th>
						<th>'.lang('Country').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Affiliate Username').'</th>
						<th>'.lang('Merchant ID').'</th>
						<th>'.lang('Merchant Name').'</th>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th>'.lang('Type').'</th>
						<th>'.lang('Creative Language').'</th>
						<th>'.lang('Profile ID').'</th>
						<th>'.lang('Status').'</th>
						<th>'.lang('Param').'</th>
						<th>'.lang('Param2').'</th>
                        <th>' . lang('Transaction ID') . '</th>
						<th>'.($type == "deposit" ? lang('Deposit Date') : lang('First Deposit')).'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Next Deposits').'</th>
						<th>'.lang('Next Deposits').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.($type == "deposit" ? lang('Deposit Amount') : lang('Deposits Amount')).'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang(ptitle('Trades')).'</th> '
						. ($displayForex==1  ? 
						'<th>'.lang(ptitle('Lots')).'</th>' : '' ) . '
						<th>'.lang('Sale Status').'</th>
						'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th>'.lang('Last Sale Note Date').'</th>
						<th>'.lang('Last Sale Note').'</th>' : '' ).'
						<th>'.lang('Last Time Active').'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Admin Notes').'</th>
					</tr></thead>
					<tfoot>
						<th></th>
						<th></th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th></th>' : '' ) . '
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th style="text-align: left;">'.price($totalFTD).'</th>
						<th></th>
						<th></th>
						<th style="text-align: left;">'.$totalTotalDeposit.'</th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th style="text-align: left;">'.price($totalVolumeAmount).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th style="text-align: left;">'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.$totalTrades.'</th>
						'. ($displayForex==1 ? 
						'<th style="text-align: left;">'.$totalLotsamount.'</th>' : '' ).
						'
						<th></th>
						'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th></th>
						<th></th>' : '' ).'
						<th></th>
						<th style="text-align: left;">'.price($totalTotalCom).'</th>
						<th></th>
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>';
				
				$set->content.=$tableStr.'
			</div>'.getPager();
			
			
excelExporter($tableStr,'Trader');		
		theme();
		break;
                
		
		
	
        case "transactions":
                $set->pageTitle = lang(ptitle('Transaction Report'));
                $l = 0;
                $arrResultSet = [];
                $ftdExist = [];
                $totalFTD = 0;
                $totalTotalDeposit = 0;
                $totalDepositAmount = 0;
                $totalVolumeAmount = 0;
                $totalBonusAmount = 0;
                $totalWithdrawalAmount = 0;
                $totalChargeBackAmount = 0;
                $totalNetRevenue = 0;
                $totalTrades = 0;
                $lots = 0;
                $totalTotalCom  = 0;
                $arrTradersPerMerchants = [];
                $intTmpMerchantId = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;
                $strWhereMerchantId = isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id . ' ' : '';
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                $resourceWallets = function_mysql_query($sql,__FILE__);
                
           
                
                
                $sql = "SELECT * FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                // die ($sql);
				$resourceMerchanrs = function_mysql_query($sql,__FILE__);
		
		// $resourceMerchants = $resourceMerchanrs;
				// $arrMerchant = mysql_fetch_assoc($resourceMerchants);
				 $arrMerchant = array();
				
                while ($arrMerchantRow = mysql_fetch_assoc($resourceMerchanrs)) {
					
					$arrMerchant = $arrMerchantRow;
					
                // var_dump($arrMerchantRow);
				// die();
                    $int_merchant_id = empty($intTmpMerchantId) ? $arrMerchantRow['id'] : $intTmpMerchantId;
                
                    $where = ' AND merchant_id = ' . $int_merchant_id;
                    
					
					// die();
						$hidedemo= true;
						
				if ($showdemo=='on' ) {
						$hidedemo =false;
						
					}	

					
					// die ('hide: ' . $ignoredemo);
					
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($group_id) $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
					if ($param) $where .= " AND freeParam='".$param."' ";
					if ($param2) $where .= " AND freeParam2='".$param2."' ";
					if ($hidedemo) $where .= " AND not type='demo' ";
                    
                    if ($trader_alias) {
                        $qry = "select trader_id from data_reg  where  lower(trader_alias) like ('%". mysql_real_escape_string(strtolower($trader_alias))."%')";
                        $row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
                        
                        if (!empty($row['trader_id'])) {
                            $trader_id = $row['trader_id'];
                            
                            if ($trader_id) {
                                $where .= " AND trader_id='".$trader_id."' ";
                            }
                            
                            if (empty($trader_id)) {
                                $trader_id = $row['trader_id'];
                            }
                        }
						else {
                              $where .= " AND trader_alias='".$trader_alias."' ";
							
							
						}
                    }
		    	
                    if ($country_id) {
                        $where .= " AND country='".$country_id."' ";
                    }
                    
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
			
			// all demos and frozen accounts.
			$qry = "select id,status from data_reg where merchant_id = " . $int_merchant_id . " AND (status = 'frozen' or type= 'demo') ";
			$invalidResources = function_mysql_query($qry,__FILE__);
			$invalidTraders = array();
			$frozenTraders = array();
			while ($invalidRow = mysql_fetch_assoc($invalidResources)) {
				$invalidTraders[] = $invalidRow['id'];
				if ($invalidRow['status']=='frozen') {
					$frozenTraders[] = $invalidRow['id'];
				}
			}
		
			
			if ($type == 'ftd' || $type == 'totalftd') {
                            $arrTotalFtds = getTotalFtds(
                                $from, $to, $affiliate_id, $arrMerchantRow['id'], $arrMerchantRow['wallet_id'], 
                                $group_id, $banner_id, $profile_id, '', $trader_id
                            );
                            
                            if ($type == 'ftd') {
                                foreach ($arrTotalFtds as $arrRes) {
									
				/* 						
									   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ;
						
				 */		
						
									
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($int_merchant_id, $arrRes, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);
                                    
                                    if ($beforeNewFTD != $ftd) {
                                        $firstDeposit           = $arrRes;
                                        $ftdAmount              = $firstDeposit['amount'];
                                        $arrRes['isFTD']        = true;
                                        $totalCom               = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $arrRes);
                                        $arrRes['firstDeposit'] = $firstDeposit;
                                        $arrRes['ftdAmount']    = $ftdAmount;
                                        $arrRes['totalCom']     = $totalCom;
                                        $arrResultSet[]         = $arrRes;
                                    }
                                    unset($arrRes);
                                }
                                
                            } elseif ($type == 'totalftd') {
                                foreach ($arrTotalFtds as $arrRes) {
									
				/* 							
									   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ;
				 */		
						
						
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($int_merchant_id, $arrRes, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);
                                    
                                    $firstDeposit           = $arrRes;
                                    $ftdAmount              = $firstDeposit['amount'];
                                    $arrRes['isFTD']        = true;
                                    $totalCom               = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $arrRes);
                                    $arrRes['firstDeposit'] = $firstDeposit;
                                    $arrRes['ftdAmount']    = $ftdAmount;
                                    $arrRes['totalCom']     = $totalCom;
                                    $arrResultSet[]         = $arrRes;
                                    unset($arrRes);
                                }
                            }
							
							$ftdsTraderIds = "0";
							 foreach($arrTotalFtds as $arrRes) {
				/* 				   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ;
				 */					
									
								 
								if (empty($ftdsTraderIds)){
									$ftdsTraderIds = $arrRes['trader_id'];
								 }
								$ftdsTraderIds .= ",".$arrRes['trader_id'];
							 }
							
							
							
								$where = str_replace('merchant_id', 'dr.merchant_id', $where);
                            $where = str_replace('trader_id', 'dr.trader_id', $where);
                            $where = str_replace('group_id', 'dr.group_id', $where);
                            $where = str_replace('affiliate_id', 'dr.affiliate_id', $where);
                            $where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            $where = str_replace('freeParam', 'dr.freeParam', $where);
                            $where = str_replace('freeParam2', 'dr.freeParam2', $where);
                            $where = str_replace('type', 'dr.type', $where);
                            
                   
							$sql = "SELECT 
							ds.id,
							ds.rdate,
							ds.trader_id,
							trim(ds.tranz_id) as tranz_id,
							ds.type,
							ds.amount,
							ds.merchant_id,
							ds.currency , 
							dr.rdate as registration_date,
							dr.ctag,
							dr.affiliate_id,
							dr.banner_id,
							dr.group_id,
							dr.profile_id,
							dr.country,
							dr.phone,
							dr.trader_alias,
							dr.type as reg_type,
							dr.freeParam,
							dr.freeParam2,
							dr.uid,
							dr.saleStatus,
							dr.lastTimeActive,
							dr.lastSaleNoteDate,
							dr.lastSaleNote,
							dr.status,
							dr.email,
							dr.campaign_id,
							dr.couponName
							
							FROM data_sales AS ds
                                  inner join data_reg dr on ds.trader_id = dr.trader_id
								  and ds.merchant_id = dr.merchant_id
                                    WHERE 2=2 and " . $globalWhere . " 
											 ds.trader_id in (".  $ftdsTraderIds . ") "
                                            . " AND ds.type = 'deposit'  "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " and ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . $where
											
									. " group by ds.tranz_id , ds.merchant_id "
                                    . " ORDER BY ds.rdate ASC;";
									
								// die ($sql);	
			    
			// } elseif ($type == 'deposit' || $type == 'withdrawal' || $type == 'bonus' || $type == 'alltransactions') {
			} else{
                            
							$where = str_replace('merchant_id', 'dr.merchant_id', $where);
                            $where = str_replace('trader_id', 'dr.trader_id', $where);
                            $where = str_replace('group_id', 'dr.group_id', $where);
                            $where = str_replace('affiliate_id', 'dr.affiliate_id', $where);
                            $where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            $where = str_replace('type', 'dr.type', $where);
                            
                   
							$sql = "SELECT 
							ds.id,
							ds.rdate,
							ds.trader_id,
							ds.tranz_id,
							ds.type,
							ds.amount,
							ds.merchant_id,
							ds.type as salesType,
							ds.currency , 
							dr.rdate as registration_date,
							dr.ctag,
							dr.affiliate_id,
							dr.banner_id,
							dr.group_id,
							dr.profile_id,
							dr.country,
							dr.phone,
							dr.trader_alias,
							dr.type as reg_type,
							dr.freeParam,
							dr.freeParam2,
							dr.uid,
							dr.saleStatus,
							dr.lastTimeActive,
							dr.lastSaleNoteDate,
							dr.lastSaleNote,
							dr.status,
							dr.email,
							dr.campaign_id,
							dr.couponName
							
							FROM data_sales AS ds
                                  inner join data_reg dr on 
								  ds.trader_id = dr.trader_id
								  and ds.merchant_id = dr.merchant_id
								  
                                    WHERE 2=2 and " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            .($type == 'alltransactions' || $type == '' ? "" : " AND ds.type = '".$type."' " )
											. " AND ds.type != 'volume'  "
                                            . $where
											
									. " group by ds.tranz_id , ds.merchant_id "
                                    . " ORDER BY ds.rdate ASC;";
							}
		
									
                             
							
							
		/* 
		else {
							// die ('gerger');
                            $sql = "SELECT * FROM data_sales "
                                    . "WHERE 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status <> 'frozen' and type='real' "
                                    . " "
                                    . "ORDER BY id DESC;";

						die ($sql);
						} */
						// die ($sql);
                            $resource = function_mysql_query($sql,__FILE__);
               while ($arrRes = mysql_fetch_assoc($resource)) {
				   
				   // var_dump($arrRes);
				   // die();
				   
               // foreach ($arrResultSet as $arrRes) {
					
			
					$ftdAmount = 0;
			
				// if ($type=="alltransactions")
						// $type =  $arrRes['salesType'];
					
					
                    $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    $totalCom = 0;
                    $int_merchant_id = $arrRes['merchant_id'];
                    
                    if ($type == 'ftd' || $type == 'totalftd') {
                        $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    } else {
                        $firstDeposit = [];
                    }
                    
                    if (
                        $type == 'ftd' || 
                        $type == 'totalftd' || 
                        $type == 'deposit' || 
                        $type == 'bonus' || 
                        $type == 'alltransactions' || 
                        $type == 'chargeback' || 
                        $type == 'withdrawal'
                    ) {
                        $ftd = $totalTraders = $depositAmount = $total_deposits = $volumeAmount = 0;
                        $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                        $spreadAmount = $pnl = 0;
			
                        $strTmpWhere = '';
                        $intTmpTraderId = empty($trader_id) ? $arrRes['trader_id'] : $trader_id;  
                        $intTmpGroupId = empty($group_id) ? $arrRes['group_id'] : $group_id;
                        $intTmpMerchantId = empty($int_merchant_id) ? $arrRes['merchant_id'] : $int_merchant_id;
                        $strTmpWhere .= empty($intTmpTraderId) ? '' : ' AND trader_id = ' . $intTmpTraderId . ' ';
                        $strTmpWhere .= empty($intTmpGroupId) ? '' : ' AND group_id = ' . $intTmpGroupId . ' ';
                        $strTmpWhere .= empty($intTmpMerchantId) ? '' : ' AND merchant_id = ' . $intTmpMerchantId . ' ';
                        // $totalCom = empty($arrRes['totalCom']) ? $totalCom : $arrRes['totalCom'];
                        
                 
                        $arrRes['trader_alias'] = $arrRes['trader_alias'];
                        $arrRes['email'] = $arrRes['email'];
                        $arrRes['country']      = $arrRes['country'];
                        $arrRes['registration_date']        =  $arrRes['registration_date'];
                        $arrRes['rdate']        = $arrRes['rdate'];
                        $arrRes['salesType']    = $arrRes['type'];
                        $arrRes['regtype']         = $arrRes['reg_type'];
                        $arrRes['banner_id']    = $arrRes['banner_id'];
                        $arrRes['status']       = $arrRes['status'];
                        $arrRes['profile_id']   = $arrRes['profile_id'];
                        $arrRes['freeParam']    = $arrRes['freeParam'];
                        $arrRes['freeParam2']    = $arrRes['freeParam2'];
                        $arrRes['saleStatus']   = $arrRes['saleStatus'];
                        $arrRes['lastTimeActive']   = $arrRes['lastTimeActive'];
                        unset($intTmpTraderId, $intTmpGroupId, $strTmpWhere, $intTmpMerchantId);
                    }
                    
                    // var_dump ($arrRes);
					// die();
					
					   if (($type=='totalftd' || $type=='ftd' ) and   in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
                        continue;
						// $arrTradersPerMerchants[] = $arrRes['trader_id'];
					   }
						
                    $depositAmount = 0;
					
				
                    $bannerInfo = getCreativeInfo($arrRes['banner_id']);
					
                            $arrRes['tranz_id'] = $arrRes['tranz_id'];
							if ($arrRes['tranz_id'] !=''  && $ftdAmount==0) {
								$ftdAmount = $arrRes['amount'];
								
								$firstDeposit['rdate'] = $arrRes['rdate'];
								$firstDeposit['id'] = $arrAmount['id'];
							}
                            
                            if ($arrAmount['type'] == 'deposit') {
                             //   $depositAmount += $arrAmount['amount'];
                               $total_deposits++;
                            } elseif ($arrAmount['type'] == 'bonus') {
                              //  $bonusAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'withdrawal') {
                              //  $withdrawalAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'chargeback') {
                            //    $chargebackAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'volume') {
                                //$volumeAmount += $arrAmount['amount'];
                          //      $totalTraders++;
								
								/* 
								$volume += $salesww['amount']; */
                                            // die ('gerg');
											$arrTmp = [
                                                'merchant_id'  => $arrRes['merchant_id'],
                                                'affiliate_id' => $arrRes['affiliate_id'],
                                                'rdate'        => $arrRes['rdate'],
                                                'banner_id'    => $arrRes['banner_id'],
                                                'trader_id'    => $arrRes['trader_id'],
                                                'profile_id'   => $arrRes['profile_id'],
                                                'type'       => 'volume',
                                                'amount'       => $arrRes['amount'],
                                            ];
                                            // var_dump($arrRes);
											// die('--');
                                            $totalCom += getCommission(
                                                $from, 
                                                $to, 
                                                0, 
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
					
                            }
                            unset($arrAmount);
                   
                    if ($type == 'ftd' || $type == 'totalftd') {
                     
                        $ftdUsers = '';
                        
                        $arrTotalFtds = getTotalFtds(
                            $from, $to, $arrRes['affiliate_id'], $arrRes['merchant_id'], $arrMerchant['wallet_id'], 
                            $arrRes['group_id'], $arrRes['banner_id'], $arrRes['profile_id'], '', $arrRes['trader_id']
                        );
                        
					/* }
					if (false) */
                        foreach ($arrTotalFtds as $arrResLocal) {
				/* 				   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrResLocal = $arrTotalFtds[$ftdCount] ;
				 */					
									
							
                            $beforeNewFTD = $ftd;
                            getFtdByDealType($arrRes['merchant_id'], $arrResLocal, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);

                            if ($beforeNewFTD != $ftd) {
                                $firstDeposit = $arrResLocal;
								
                                $ftdAmount = $arrResLocal['amount'];
                                $arrResLocal['isFTD'] = true;
                                
                                // Old version.
                                //$totalCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrResLocal);
                                
								
								
                                // New version.
                                //if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
									$totalCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrResLocal);
                                    
									// var($totalCom);
                                //}
                            }
                            unset($arrResLocal);
                        }
                    
			   }

                    if ($arrRes['reg_type'] == 'real') {
                        $color = 'green';
                    } elseif ($arrRes['reg_type'] == 'demo') {
                        $color = 'red';
                    } elseif ($arrRes['reg_type'] == 'lead') {
                        $color = 'black';
                    }
                  

                    // AFFILIATE info retrieval.
                    /* $sql = "SELECT id,group_id,username FROM affiliates AS aff "
                            . " WHERE  id = " . $arrRes['affiliate_id']
                            . " LIMIT 0, 1;";
// die ($sql);
                    $affInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__)); */
					$affInfo = getAffiliateRow($arrRes['affiliate_id']);

                    // Check trader.
					$reason="";
                    $sql = 'SELECT * FROM payments_details '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['id'] 
                            . '  LIMIT 0, 1;';
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (empty($chkTrader)) {
						$sql = 'SELECT * FROM traders_tag '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['id'] 
                            . '  LIMIT 0, 1;';
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (!empty($chkTrader)) {
						$reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
					}
					
					}else {
					$reason = chkTrader['reason'];
						
					}
							
							
                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].'</td>
                            <td>'.$arrRes['trader_alias'].'</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.$arrRes['email'].'</td>' : '' ) . '
                            <td>'.date("d/m/Y", strtotime($arrRes['registration_date'])) .'</td>
                            <td><span style="color: '.$color.';">'.$arrRes['reg_type'].'</span></td>
                            <td>'.longCountry($arrRes['country']).'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
                            <td><a href="/admin/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
                            <td>'.$arrMerchant['id'].'</td>
                            <td>'.strtoupper($arrMerchant['name']).'</td>
                            <td style="text-align: left;">'.$bannerInfo['id'].'</td>
                            <td style="text-align: left;">'.$bannerInfo['title'].'</td>
                            <td>'.$bannerInfo['type'].'</td>
                            <td>'.$bannerInfo['language_name'].'</td>
                            <td>'.$arrRes['profile_id'].'</td>
                            <td><span>'.($type=='totalftd' || $type=='ftd' ? strtoupper($type) :  ucwords($arrRes['salesType'])).'</span></td>
                            <td>'.$arrRes['status'].'</td>
                            <td>'.$arrRes['freeParam'].'</td>
                            <td>'.$arrRes['freeParam2'].'</td>
                            <td>' .  $arrRes['tranz_id'] . '</td>
                            <td>'.date("d/m/Y", strtotime($arrRes['rdate'])) .'</td>
                            <td>'.price($arrRes['amount']).'</td>
                            <td>'.ucwords($arrRes['saleStatus']).'</td>
							'.($set->displayLastMessageFieldsOnReports ==1 ? '
                            <td>'.$arrRes['lastSaleNoteDate'].'</td>
                            <td>'.$arrRes['lastSaleNote'].'</td>':'').'
							
							<td>'.($arrRes['lastTimeActive']=='1969-12-31 23:00:00' || $arrRes['lastTimeActive'] == '0000-00-00 00:00:00' ? '-' : $arrRes['lastTimeActive']).'</td>
                            <!--td>'.price($totalCom).'</td-->
                            <td>'.$chkTrader['reason'].'</td>
                        </tr>';
                    
                    if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$arrTradersPerMerchants)) {
							$arrTradersPerMerchants[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
							
					// if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
                        // $arrTradersPerMerchants[] = $arrRes['trader_id'];
                        // $totalFTD += $ftdAmount;
                        // $totalNetRevenue += $netRevenue;
                        $totalTotalCom += $totalCom;
                    }
				$totalAmounts += $arrRes['amount'];
                
                    $l++;
					
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$totalTraders=0;
                }
                        
			   }
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form action="'.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="transactions" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						<td>'.lang('Affiliate ID').'</td>
						<td>'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						<td>'.lang(ptitle('Trader Alias')).'</td>
						<td>'.lang(ptitle('Parameter')).'</td>
						<td>'.lang(ptitle('Parameter2')).'</td>
						<td>'.lang('Group').'</td>
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_alias" value="'.$trader_alias.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param" value="'.$param.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="'.$param2.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td-->
                                                <td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                                                </td>
						<td>
							<select name="type" style="width: 110px;">
								<option value="alltransactions" '.($type == "alltransactions" ? 'selected' : '').'>'.lang(ptitle('All Transactions')).'</option>
                                <option value="bonus" '.($type == "bonus" ? 'selected' : '').'>'.lang('Bonus').'</option>
                                <option value="chargeback" '.($type == "chargeback" ? 'selected' : '').'>'.lang('Chargeback').'</option>
								<option value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option>
								<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>
                                <option value="totalftd" '.($type == "totalftd" ? 'selected' : '').'>'.lang('Total FTD').'</option>
                                <option value="withdrawal" '.($type == "withdrawal" ? 'selected' : '').'>'.lang('Withdrawal').'</option>
							</select>
						</td>
						<td><input type="checkbox" name="showdemo"  '.($showdemo ? 'checked="checked"' : '').'  />'.lang('Show Demo Traders').'</td>
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2600px;">'.lang('Report Results').'</div>
			<div style="background: #F8F8F8;">';
				$tableStr ='<table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).'</th>
						<th>'.lang(ptitle('Trader Alias')).'</th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th>'.lang(ptitle('Email')).'</th>' : '' ) . '
						<th>'.lang('Registration Date').'</th>
						<th>'.lang(ptitle('Trader Status')).'</th>
						<th>'.lang('Country').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Affiliate Username').'</th>
						<th>'.lang('Merchant ID').'</th>
						<th>'.lang('Merchant Name').'</th>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th>'.lang('Type').'</th>
						<th>'.lang('Creative Language').'</th>
						<th>'.lang('Profile ID').'</th>
						<!--th>'.lang('FTD Date').'</th>
						<th>'.lang('FTD Amount').'</th-->
						<th>'.lang('Transaction Type').'</th>
						<th>'.lang('Status').'</th>
						<th>'.lang('Param').'</th>
						<th>'.lang('Param2').'</th>
                        <th>' . lang('Transaction ID') . '</th>
						<th>'. lang('Transaction Date') .'</th>
						<th>'.lang('Amount').'</th>
						<th>'.lang('Sale Status').'</th>
					'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th>'.lang('Last Sale Note Date').'</th>
						<th>'.lang('Last Sale Note').'</th>' : '' ).'
						<th>'.lang('Last Time Active').'</th>
						<!--th>'.lang('Commission').'</th-->
						<th>'.lang('Admin Notes').'</th>
					</tr></thead>
					<tfoot>
						<th></th>
						<th></th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th></th>' : '' ) . '
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						
					
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						
						<th style="text-align: left;">'.price($totalAmounts).'</th>
						<th></th>
				'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th></th>
						<th></th>' : '' ).'
						<th></th>
						<!--th style="text-align: left;">'.price($totalTotalCom).'</th-->
						<th></th>
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>';
				
				$set->content.=$tableStr.'
			</div>'.getPager();
			
			
excelExporter($tableStr,'Transactions');		
		theme();
		break;
                
		
		
		
	case "stats":
		$set->pageTitle = lang(ptitle('Trader Stats Report'));
		$l=0;
		
		if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."'";
		if ($group_id) $where .= " AND group_id='".$group_id."'";
		if ($banner_id) $where .= " AND banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND profile_id='".$profile_id."'";
		if ($trader_id) $where .= " AND trader_id='".$trader_id."'";
		if ($type != "" AND $type != "deposit") $where .= " AND type='".$type."'";
		
		if ($merchant_id) {
			// $ww = dbGet($merchant_id,"merchants");
			$ww = getMerchants($merchant_id,0);
			if (!$ww['id']) _goto();
			$brokers_ids[] = $ww['id'];  
			$brokers[] = $ww['name'];
			$brokers_formula = $ww['rev_formula'];
			$productType = $ww['productType'];
			$statsTable = $ww['productType']=='binaryoption' ? "sales" : "stats";
			
		} else {
			// $qq=function_mysql_query("SELECT id,name,rev_formula,productType FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
			// while ($ww=mysql_fetch_assoc($qq)) {
		$merchantsArr = getMerchants(0,1);
		foreach ($merchantsArr as $ww) {
				$brokers_ids[] = $ww['id'];
				$brokers[] = $ww['name'];
				$brokers_formula = $ww['rev_formula'];
				$productType[] = $ww['productType'];
				$statsTable = $ww['productType']=='binaryoption' ? "sales" : "stats";
				}
		}
		
		
		
		
		
		
			$filterhtml = '';
			

			if ($statsTable =='sales') {
				
			$sql = "	SELECT distinct type FROM data_".$statsTable." where type ='volume' order by type";
			}
			else {
			$sql = "	SELECT distinct type FROM data_".$statsTable." order by type";
			}
				// die ($sql);
				
				
				$qq=function_mysql_query($sql,__FILE__);
				$filterhtml .='<option value="" '.($type =='' ? 'selected' : '').'>'.lang('All Types').'</option>';
				while ($ww=mysql_fetch_assoc($qq)) {
					//$filterhtml .='<option value="'.$ww['type'].'">'.lang($ww['type']).'</option>';
					$filterhtml .='<option value="'.$ww['type'].'" '.($type == $ww['type'] ? 'selected' : '').'>'.ucwords(str_replace('_',' ',$ww['type'])).'</option>';
					
				}
				
		for ($i=0; $i<=count($brokers)-1; $i++) {
			
			$formula = $brokers_formula[$i];
			$broker['name'] = $brokers[$i];
		$productType = $productType[$i];
		// die($productType);
		
		$statsTable = $productType=='binaryoption' ? "sales" : "stats";
		
				$sql = "	SELECT ds.*"."	FROM data_".$statsTable." AS ds ";
				
			
			if ($statsTable =='sales') {
						$sql .= "WHERE 5=5 and 1=1 and ds.type='volume' and ds.merchant_id = '" . $brokers_ids[$i]."' and ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ".$where;//." GROUP BY ds.trader_id";
			}
			else {
						$sql .= "WHERE 5=5 and ds.merchant_id = '" . $brokers_ids[$i]."' and ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ".$where;//." GROUP BY ds.trader_id";
			}
			
		
				
				$sql.=" ORDER BY ds.trader_id ASC";
				
				// die ($sql);
				$qq=function_mysql_query($sql,__FILE__);
			
			while ($ww=mysql_fetch_assoc($qq)) {
				// $merchantInfo = dbGet($ww['merchant_id'],"merchants");
				$merchantInfo = getMerchants($merchant_id,0);
				
				$productType = $merchantInfo['producttype'];
				
				$listReport .= '<tr>
						<td>'.$ww['trader_id'].'</td>
						<td>'.$ww['trader_alias'].'</td>
						<td>'.$ww['tranz_id'].'</td>
						<td>'. date("Y/m/d H:i:s", strtotime($ww['rdate'])).'</td>
						<td>'.longCountry($ww['country']).'</td>
						<td><a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$ww['affiliate_id'].'</a></td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td style="text-align: left;"><a href ="/admin/reports.php?act=banner&banner_id='.$ww['banner_id'].'" target="_blank">'.$ww['banner_id'].'</a></td>
						<td>'.$ww['profile_id'].'</td>
						<td>'.$ww['group_id'].'</td>
						<td>'.$ww['ctag'].'</td>
						<td>'.$ww['freeParam'].'</td>
						<td>'.$ww['freeParam2'].'</td>
						<td>'.ucwords(str_replace('_',' ',$ww['type'])).'</td>
						<td>'.price($ww['amount']).'</td>
						'.($productType=='forex' ? '<td>'.price($ww['turnover']).'</td>' : '').'
						'.($productType=='forex' ? '<td>'.price($ww['spread']).'</td>' : '').'
						'.($productType=='forex' ? '<td>'.price($ww['pnl']).'</td>' : '').'
					</tr>';
				$l++;
				
				$totalAmount += $ww['amount'];
				$totalTurnover += $ww['turnover'];
				$totalSpread += $ww['spread'];
				$totalPnl += $ww['pnl'];
				
			}
				

				
		}
	
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $l;
		

		//die($filterhtml);
		
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form action="'.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="stats" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						<td>'.lang('Affiliate ID').'</td>
						<td>'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						<td>'.lang('Group ID').'</td>
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from,$to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td width="100">
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                            . lang('General') 
                                                        . '</option>' 
                                                        . listGroups($group_id) 
                                                    . '</select>
                                                </td>
						<td>
							<select name="type" style="width: 100px;">'
								.$filterhtml.'
							</select>
						</td>
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
				'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2600px;">'.($merchant_id ? strtoupper($broker['name']) : '').' '.lang('Report Results').'</div>
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="1700" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).'</th>
						<th>'.lang(ptitle('Trader Alias')).'</th>
						<th>'.lang(ptitle('Transaction ID')).'</th>
						<th title="mm/dd/yyyy">'.lang('Date').'</th>
						
						<th>'.lang('Country').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Merchant ID').'</th>
						<th>'.lang('Merchant Name').'</th>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th>'.lang('Profile ID').'</th>
						<th>'.lang('Group ID').'</th>
						<th>'.lang(ptitle('cTag')).'</th>
						<th>'.lang('Free Parameter').'</th>
						<th>'.lang('Free Parameter2').'</th>
						<th>'.lang('Type').'</th>
						<th>'.lang(ptitle('Amount')).'</th>
						'.($productType=='forex' ? '<th>'.lang(ptitle('Turnover')).'</th>' : '').'
						'.($productType=='forex' ? '<th>'.lang(ptitle('Spread')).'</th>' : '').'
						'.($productType=='forex' ? '<th>'.lang(ptitle('PNL')).'</th>' : '').'
						
					</tr></thead>
					
					<tbody>
					'.$listReport.($productType=='forex' ? '
					<tfoot><tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></tth>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>'.price($totalAmount).'</th>
						'.($productType=='forex' ? '<th>'.price($totalTurnover).'</th>' : '').'
						'.($productType=='forex' ? '<th>'.price($totalSpread).'</th>' : '').'
						'.($productType=='forex' ? '<th>'.price($totalPnl).'</th>' : '').'
						
					</tr></tfoot>' : '').'
				</table>';
			
		excelExporter($tableStr,'stats');
			
		$set->content.=$tableStr.'
			</div>'.getPager();
		
		theme();
		break;
		
	
        
	
	case "affiliate":
				$set->pageTitle   = lang('Affiliate Report');
                $showLeadsAndDemo = false;
                $where            = '';
                // $sql              = 'SELECT extraMemberParamName AS title FROM merchants';// WHERE id = ' . aesDec($_COOKIE['mid']);
				// $campID           = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));

				
				// $sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
                    $merchantsArray = array();
                    $strAffDealTypeArray = array();
					$displayForex = 0;
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					$campID = "";
					$arrWallets = array();
					
					$merchantsAr = getMerchants(0,1);
					foreach ($merchantsAr as $arrMerchant) {
						
						
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
						if (empty($campID))
								$campID['title']= $arrMerchant['extraMemberParamName'];
							
						if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1;
					
						$merchantsArray[$arrMerchant['id']] = $arrMerchant;
					
			
					$merchant_id = $arrMerchant['id'];
					
					   // List of wallets.
                        $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1 " 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        
                        $resourceWallets = function_mysql_query($sql,__FILE__);
                        
                        while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                            $arrWallets[$arrWallet['wallet_id']] = false;
                            unset($arrWallet);
                        }
						
					}
			
					
				
                // $sql = "SELECT COUNT(id) AS count FROM merchants WHERE valid = 1;";
                // $arrMerchantCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));

                $intMerchantCount =count($merchantsArray);
                
                
                if (isset($group_id) && $group_id != '') {
                    $where .= ' AND group_id = ' . $group_id . ' ';
                }
                
                
                if (isset($groupByAff)) {
					
					if ( $groupByAff == 1) {
                    $groupMerchantsPerAffiliate = 1;
                } else {
                    $groupMerchantsPerAffiliate = 0;
                }
					
				} else {
					$groupMerchantsPerAffiliate = 1;
				}
				

                if (isset($affiliate_id) && !empty($affiliate_id) && is_numeric($affiliate_id)) {
                    $where .= ' AND affiliates.id = ' . $affiliate_id . ' ';
                } elseif (isset($affiliate_id) && !empty($affiliate_id)) {
                    $where .= " AND (lower(username) LIKE '%" . trim(strtolower($affiliate_id)) . "%' ";
                        if (!empty($campID['title'])) {
                            $where .= " or affiliates.id in (SELECT affiliateID FROM  `affiliates_campaigns_relations` WHERE  `campID` LIKE  '%".strtolower($affiliate_id)."%' )";
                        }
                        
                    $where .= " ) ";
                }
                
                
                // Initialize total counters for all affiliates.
                $totalImpressionsM = 0;
                $totalClicksM = 0;
                $totalLeadsAccountsM = 0;
                $totalDemoAccountsM = 0;
                $totalRealAccountsM = 0;
                $totalFTDM = 0;
                $totalDepositsM = 0;
                $totalFTDAmountM = 0;
                $totalDepositAmountM = 0;
                $totalVolumeM = 0;
                $totalBonusM = 0;
                $totalWithdrawalM = 0;
                $totalChargeBackM = 0;
                $totalNetRevenueM = 0;
                $totalComsM = 0;
                $netRevenueM = 0;
                $totalFruadM = 0;
                $totalFrozensM = 0;
                $totalRealFtdM = 0;
                $totalRealFtdAmountM = 0;
                
				$depositsAmount = 0;
				
                $l = 0;
                $arrRanges = [];
                
                switch ($display_type) {
                    case 'monthly':
                        $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_MONTHLY);
                        break;
                    case 'weekly':
                        $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_WEEKLY);
                        break;
                    case 'daily':
                        $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_DAILY_RANGE);
                        break;
                    default:
                        $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_NONE);
                        break;
                }
                
                // Time-periods loop.
/*                 		$key = array_keys($arrRanges);
					$size = sizeOf($key);
					for ($k=0; $k<$size; $k++) {
						$arrRange = $arrRanges[$key[$k]] ;
						
						
						// echo 'aff: ' .$k.'<br>'; */
						
				
				foreach ($arrRanges as $arrRange) {
                    
                    $sql = "SELECT affiliates.*, acr.campID FROM affiliates "
                        . "LEFT JOIN affiliates_campaigns_relations acr ON affiliates.id = acr.affiliateID "
                        . "WHERE valid = 1 " . $where . " 
						group by id "
                        . "ORDER BY affiliates.id DESC;";
                    
					// die ($sql);
                    $qq = function_mysql_query($sql,__FILE__);
                    
                    $intAffiliatesCombinedCount = 0;
                    $showCasinoFields           = 0;
                    $totalRealFtd               = 0;
                    $totalRealFtdAmount         = 0;
                    
                    while ($ww = mysql_fetch_assoc($qq)) {
						
                        // List of wallets.
                        $arrWallets = array();
                        $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1 " 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        
                        $resourceWallets = function_mysql_query($sql,__FILE__);
                        
                        while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                            $arrWallets[$arrWallet['wallet_id']] = false;
                            unset($arrWallet);
                        }
                        
                        
                        $sql = 'SELECT COUNT(id) AS count FROM merchants WHERE valid = 1 ' 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        
                        $arrMerchantsCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                        $intMerchantsCount = (int) $arrMerchantsCount['count'];
                        
                        
                        // Initialize total counters per affiliate.
                        $totalImpressions = 0;
                        $totalClicks = 0;
                        $totalLeadsAccounts = 0;
                        $totalDemoAccounts = 0;
                        $totalRealAccounts = 0;
                        $totalFTD = 0;
                        $totalDeposits = 0;
                        $totalFTDAmount = 0;
                        $totalDepositAmount = 0;
                        $totalVolume = 0;
                        $totalLots = 0;
                        $totalBonus = 0;
                        $totalWithdrawal = 0;
                        $totalChargeBack = 0;
                        $totalNetRevenue = 0;
                        $totalComs = 0;
                        $totalSpreadAmount = 0;
                        $totalTurnoverAmount = 0;
                        $totalpnl = 0;
                        $totalFrozens = 0;
                        $totalRealFtd = 0;
                        $totalRealFtdAmount = 0;
                        
                        
						 
						/* 
						$sql = 'SELECT id,name,type,producttype,rev_formula, wallet_id  FROM merchants WHERE valid = 1 ' 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        // die ($sql);
                        $merchantqq = function_mysql_query($sql,__FILE__); */
                        $counterrr=0;
						
						$tradersProccessedForLots= array();
						
						
						
                        // while ($merchantww = mysql_fetch_assoc($merchantqq)) {
							foreach ($merchantsArray as $merchantww) {
							
							
							// $counterrr++;
							// echo 'counter: ' . $counterrr.'<br>';
                            $arrMerchantsAffiliate = explode('|', $ww['merchants']);
                            if (!in_array($merchantww['id'], $arrMerchantsAffiliate)) {
                                continue;
                            }
                            
                            // Check if this is a first itaration on given wallet.
							                    if ($set->multiMerchantsPerTrader==1)
						
                            $needToSkipMerchant  = $arrWallets[$merchantww['wallet_id']];
				else 
					$needToSkipMerchant= false;
							
							
                            $merchantww['count'] = $intMerchantsCount;
                            
                            $showLeadsAndDemo = strtolower($merchantww['producttype']) == 'sportsbetting' 
                                             || strtolower($merchantww['producttype']) == 'casino';
                            
                            if (strtolower($merchantww['producttype']) == 'casino') {
                                $showCasinoFields = 1;
                            }
                            
                            // Initialize total counters per affiliate-merchant.
                            $formula = $merchantww['rev_formula'];
                            $totalTraffic=0;
                            $totalLeads=0;
                            $totalDemo=0;
                            $totalReal=0;
                            $ftd=0;
                            $pnl = 0;
                            $volume=0;
                            $bonus=0;
                            $spreadAmount = 0;
                            $turnoverAmount = 0;
                            $withdrawal=0;
                            $chargeback=0;
                            $revenue=0;
                            $ftd_amount=0;
                            $depositingAccounts=0;
                            $sumDeposits=0;
                            $netRevenue=0;
                            $lots=0;
                            $totalCom=0;
                            $real_ftd = 0;
                            $real_ftd_amount = 0;
                            
                            
                            $totalTraffic                = [];
                            $arrClicksAndImpressions     = getClicksAndImpressions($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id']);
                            $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                            
                            
                            $merchantName = strtolower($merchantww['name']);
                            $sql = 'SELECT SUM(IF(status="frozen",1,0)) AS total FROM data_reg '
                                    . 'WHERE affiliate_id="'.$ww['id'].'" AND merchant_id="'.$merchantww['id'] 
                                    . '" AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . '"';
                            
                            $frozensQ = function_mysql_query($sql,__FILE__);
                            $frozens  = mysql_fetch_assoc($frozensQ);
                            $frozens  = $frozens['total'] ? $frozens['total'] : 0;
                            
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id = " . $ww['id'] . " "
                                    . "AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";
                            
                            $regqq = function_mysql_query($sql,__FILE__);
                            
                            $arrTierCplCountCommissionParams = [];
                            
                            while ($regww = mysql_fetch_assoc($regqq)) {


							
							$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
									
								
                                $boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
                                
                                if ($regww['type'] == 'lead') $totalLeads++;
                                if ($regww['type'] == 'demo') $totalDemo++;
                                if ($regww['type'] == 'real') {
                                    if (!$boolTierCplCount) {
                                        $arrTmp = [
                                            'merchant_id'  => $regww['merchant_id'],
                                            'affiliate_id' => $regww['affiliate_id'],
                                            'rdate'        => $regww['rdate'],
                                            'banner_id'    => $regww['banner_id'],
                                            'trader_id'    => $regww['trader_id'],
                                            'profile_id'   => $regww['profile_id'],
                                        ];

                                        $totalCom += getCommission($arrRange['from'], $arrRange['to'], 0, -1, $arrDealTypeDefaults, $arrTmp);
                                        unset($arrTmp);
                                        
                                    } else {
                                        // TIER CPL.
                                        if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
                                            $arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
                                        } else {
                                            $arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
                                                'from'                => $arrRange['from'],
                                                'to'                  => $arrRange['to'],
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
                                    
                                    $totalReal++;
                                }
                            }
                            
                   /*          
                            // TIER CPL.
                            foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
                                $totalCom += getCommission(
                                    $arrParams['from'], 
                                    $arrParams['to'], 
                                    $arrParams['onlyRevShare'], 
                                    $arrParams['groupId'], 
                                    $arrParams['arrDealTypeDefaults'], 
                                    $arrParams['arrTmp']
                                );
                                
                                unset($intAffId, $arrParams);
                            } */
                            
                            
                            $ftdUsers = '';
                            
                            if (!$needToSkipMerchant) {
                                $arrFtds = getTotalFtds($arrRange['from'], $arrRange['to'], $ww['id'], $merchantww['id'], $merchantww['wallet_id']);
                                
								
								
								///   CHECKTHIS	
							foreach ($arrFtds as $arrFtd) {
				/* 				
								$size = sizeOf($arrFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrFtd = $arrFtds[$ftdCount] ;
				 */			   
                                    $real_ftd++;
                                    $real_ftd_amount += $arrFtd['amount'];
                                    
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
                                    
                                    if ($beforeNewFTD != $ftd) {
                                        $arrFtd['isFTD'] = true;
                                        $totalCom += getCommission($arrRange['from'], $arrRange['to'], 0, -1, $arrDealTypeDefaults, $arrFtd);
                                    }
                                    unset($arrFtd);
                                }
                            }
                            
							
                            
                                    $sql = "SELECT *, tb1.type AS data_sales_type  FROM data_sales as tb1 "
                                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                            . "WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND tb1.affiliate_id='".$ww['id']."' "
                                            . "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";
                                    
									//if (isset($_GET['test'])) echo '<br />', $sql, '<br />';
									
                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    $volume = 0;
                                    
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                                        //if ($salesww['type'] == 'deposit') { // OLD.
										if ($salesww['data_sales_type'] == 'deposit') { // NEW.
                                            $depositingAccounts++;
                                            $sumDeposits    += $salesww['amount'];
											$depositsAmount += $salesww['amount'];
                                        }
                                        
                                        if ($salesww['data_sales_type'] == 'bonus') $bonus += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'revenue') $revenue += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'withdrawal') $withdrawal += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'chargeback') $chargeback += $salesww['amount'];
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
                                                'amount'       => $salesww['amount'],
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
									
									
							if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                                $arrRevenueRanges = getRevenueDealTypeByRange($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id'], $arrDealTypeDefaults);
                                $intTotalRevenue  = 0;
                                
                                foreach ($arrRevenueRanges as $arrRange2) {
                                    $strWhere = 'WHERE merchant_id = ' . $merchantww['id'] 
                                              . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                              . '" AND affiliate_id = "' . $ww['id'] . '"';
                                    
                                    $intCurrentRevenue = getRevenue(
                                        $strWhere,
                                        $merchantww['producttype'],
                                        $sumDeposits,
                                        $bonus,
                                        $withdrawal,
                                        $pnl,
                                        $turnoverAmount,
                                        $spreadAmount,
                                        $formula
                                    );
                                    
                                    $intTotalRevenue    += $intCurrentRevenue;
                                    $row                 = array();
                                    $row['merchant_id']  = $merchantww['id'];
                                    $row['affiliate_id'] = $ww['id'];
                                    $row['banner_id']    = 0;
                                    $row['rdate']        = $arrRange2['from'];
                                    $row['amount']       = $intCurrentRevenue;
                                    $row['isFTD']        = false;
                                    $totalCom           += getCommission($arrRange2['from'], $arrRange2['to'], 1, -1, $arrDealTypeDefaults, $row);

                                    unset($arrRange2, $strWhere);
                                }
                                
                                $netRevenue = $intTotalRevenue;
                                
                            } else {
								
								
                                // $netRevenue = round($sumDeposits - ($withdrawal + $bonus + $chargeback), 2);
								$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
								
								// die ($netRevenue);
								
								               
								$row                 = [];
								$row['merchant_id']  = $merchantww['id'];
								$row['affiliate_id'] = $ww['id'];
								$row['banner_id']    = 0;
								$row['rdate']        = $from;
								$row['amount']       = $netRevenue;
								// $row['trader_id']    = 0;
								$row['isFTD']        = false;
							   	
							
							    $totalCom           += getCommission($from, $to, 1, -1, $arrDealTypeDefaults, $row);
								
								
								
								//if (isset($_GET['test'])) echo print_r([$depositsAmount, $withdrawal, $bonus, $chargeback, 'test-depositsAmount' => gettype($depositsAmount)], true), '<br />';
                            }
									
								
                                    
                                    
									/* 
                                    $sql = "SELECT type,amount FROM data_stats "
                                            . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id='".$ww['id'] 
                                            . "' AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";
                                    
                                    $statsqq = function_mysql_query($sql,__FILE__);
                                    
                                    while ($statsww = mysql_fetch_assoc($statsqq)) {
                                    } */
                                    
                                //    $displayForex = 0;
                                    
                                    if(strtolower($merchantww['producttype']) == 'forex') {
                                        $stats = 1;
                                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                                . 'WHERE affiliate_id = "' . $ww['id']  . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                                . ' GROUP BY affiliate_id';
                                        // die($sql);
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                            $spreadAmount  = $ts['totalSpread'];
                                            $volume       += $ts['totalTO'];
                                            $pnl           = $ts['totalPnl'];
                                        }
                                        
                                        // $displayForex = 1;
								

								//Lots
														
						$totalLots  = 0;
						
						
							
										
						$sql = 'SELECT turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
                                         . 'WHERE merchant_id = "' . $merchantww['id'] . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
											
											// die($sql);
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
                                        $earliestTimeForLot = date('Y-m-d H:i:s');
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											
											if($ts['affiliate_id']==null) {
													continue;
											}
							
											// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
													$totalLots  = $ts['totalTurnOver'];
													
														
													// echo $totalLots
														$tradersProccessedForLots[$merchantww['id'] . '-' . $ts['trader_id']] = $merchantww['id'] . '-' . $ts['trader_id'];
														$lotdate = $ts['rdate'];
														$ex = explode(' ' , $lotdate);
														$lotdate = $ex[0];
															
															
															
															if ($earliestTimeForLot>$lotdate)
															$earliestTimeForLot = $lotdate;
														
														
														$row = [
																	'merchant_id'  => $merchantww['id'],
																	'affiliate_id' => $ts['affiliate_id'],
																	'rdate'        => $earliestTimeForLot,
																	'banner_id'    => $ts['banner_id'],
																	'trader_id'    => $ts['trader_id'],
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
														
														// var_dump($row);
														// die();
													$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
													// echo 'com: ' . $a .'<br>';
													$totalCom += $a;
													
											// }
										}
										
										 
										
                                    }
                                    
                                    if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {
                                        if (
                                            (int) $stats <= 0 && (int) $totalLeads <= 0 && (int) $totalDemo <= 0 && 
                                            (int) $totalReal <= 0 && (int) $ftd <= 0 && 
                                            (int) $depositingAccounts <= 0 && 
											(int) $totalCom <= 0 && 
                                            (int) $totalTraffic['totalViews'] <= 0 && (int) $totalTraffic['totalClicks'] <= 0
                                        ) {
                                            continue;
                                        }
                                    }
                                                           
                                                        
                                    $sql = "SELECT id FROM payments_details WHERE status = 'canceled' AND affiliate_id = '" . $ww['id'] . "' ";
                                    $totalFruad = mysql_num_rows(function_mysql_query($sql,__FILE__));
                                    $totalFruadM += $totalFruad;
                                    
                                    if ($groupMerchantsPerAffiliate == 0) {
                                        $totalImpressions = $totalTraffic['totalViews'];
                                        $totalClicks = $totalTraffic['totalClicks'];
                                        $totalLeadsAccounts = $totalLeads;
                                        $totalDemoAccounts = $totalDemo;
                                        $totalRealAccounts = $totalReal;
                                        $totalFTD = $ftd;
                                        $totalDeposits = $depositingAccounts;
                                        $totalFTDAmount = $ftd_amount;
                                        $totalDepositAmount = $sumDeposits;
                                        $totalVolume = $volume;
                                        $totalBonus = $bonus;
                                        $totalWithdrawal = $withdrawal;
                                        $totalChargeBack = $chargeback;
                                        $totalNetRevenue = $netRevenue;
                                        $totalComs = $totalCom;
                                        $totalSpreadAmount = $spreadAmount;
                                        $totalTurnoverAmount = $turnoverAmount;
                                        $totalpnl = $pnl;
                                        $totalFrozens = $frozens;
                                        $totalRealFtd = $real_ftd;
                                        $totalRealFtdAmount = $real_ftd_amount;
                                        
                                    } else {
                                        $totalImpressions += $totalTraffic['totalViews'];
                                        $totalClicks += $totalTraffic['totalClicks'];
                                        $totalLeadsAccounts += $totalLeads;
                                        $totalDemoAccounts += $totalDemo;
                                        $totalRealAccounts += $totalReal;
                                        $totalFTD += $ftd;
                                        $totalDeposits += $depositingAccounts;
                                        $totalFTDAmount += $ftd_amount;
                                        $totalDepositAmount += $sumDeposits;
                                        $totalVolume += $volume;
                                        $totalBonus += $bonus;
                                        $totalWithdrawal += $withdrawal;
                                        $totalChargeBack += $chargeback;
                                        $totalNetRevenue += $netRevenue;
                                        $totalComs += $totalCom;
                                        $totalSpreadAmount += $spreadAmount;
                                        $totalTurnoverAmount += $turnoverAmount;
                                        $totalpnl += $pnl;
                                        $totalFrozens += $frozens;
                                        $totalRealFtd += $real_ftd;
                                        $totalRealFtdAmount += $real_ftd_amount;
                                    }
                                    
                                    $totalImpressionsM += $totalTraffic['totalViews'];
                                    $totalClicksM += $totalTraffic['totalClicks'];
                                    $totalLeadsAccountsM += $totalLeads;
                                    $totalDemoAccountsM += $totalDemo;
                                    $totalRealAccountsM += $totalReal;
                                    $totalFTDM += $ftd;
                                    $totalDepositsM += $depositingAccounts;
                                    $totalFTDAmountM += $ftd_amount;
                                    $totalDepositAmountM += $sumDeposits;
                                    $totalVolumeM += $volume;
                                    $totalBonusM += $bonus;
                                    $totalWithdrawalM += $withdrawal;
                                    $totalChargeBackM += $chargeback;
                                    $totalNetRevenueM += $netRevenue;
                                    $totalComsM += $totalCom;
                                    $totalSpreadAmountM += $spreadAmount;
                                    $totalTurnoverAmountM += $turnoverAmount;
                                    $totalFrozensM += $frozens;
                                    $totalpnlM += $pnl;
                                    $totalRealFtdM += $real_ftd;
                                    $totalRealFtdAmountM += $real_ftd_amount;
                                    
                                    $l++;        
                                    
                                // Mark given wallet as processed.
                                $arrWallets[$merchantww['wallet_id']] = true;
                            
                                
                            $listReport .= '<tr>';
                            
                            if ($groupMerchantsPerAffiliate == 0) {
                                if (isset($display_type) && !empty($display_type)) {
                                    $listReport .= '<td>' . $arrRange['from'] . ' - ' . $arrRange['to'] . '</td>';
                                }
                                
                                $listReport .= '
                                    <td>'.$ww['id'].'</td>
                                    <td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
                                    <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';
                                
                                if ($campID['title']) {
                                    if ($ww['campID']) {
                                        $listReport .= '<td align="left">' . $ww['campID'] . '</td>';
                                    } else {
                                        $listReport .= '<td align="left"></td>';
                                    }	
                                }
                               
							   
							   
							   
							   
                                $listReport .=  
                                    (!$set->isNetwork ? '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>' : '') . '
                                    <td>' . $merchantww['name'] . '</td>
                                    '.($set->ShowIMUserOnAffiliatesList ? '<td align="left">'.$ww['IMUser'].'</td>' : ''). '
									<td><a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>
                                    <td style="text-align: center;">' . @number_format($totalImpressions, 0) . '</td>
                                    <td style="text-align: center;">' . @number_format($totalClicks, 0) . '</td>
                                    <td style="text-align: center;">' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</td>
                                    <td style="text-align: center;">' . @number_format(($totalRealAccounts / $totalClicks) * 100, 2) . ' %</td>
                                    <td style="text-align: center;">' . @number_format(($totalFTD / $totalClicks) * 100, 2) . ' %</td>
                                    <td style="text-align: center;">' . @price($totalComs / $totalClicks) . '</td>' . 
                                    ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                    
                                    '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                    '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                    <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                    <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=transactions&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                    <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                    
                                    <td style="text-align: center;">' . price($totalBonus) . '</td>
                                    <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                    <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                    <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                    <td style="text-align: center;">' . price($totalVolume) . '</td>
                                    '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                    '.(($displayForex && $showPNL) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
                                    <td style="text-align: center;">' . price($totalComs) . '</td>
                                    <td style="text-align: center;">' . listGroups($ww['group_id'], 1) . '</td>
                                </tr>';
                            }
                        }
                        
                        // Loop through affiliates, aggregate the per-merchant info.
                        if ($groupMerchantsPerAffiliate == 1) {
								if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {


									
						if (
								
                                            (int) $stats <= 0 && (int) $totalLeadsAccounts <= 0 && (int) $totalDemoAccounts <= 0 && 
                                            (int) $totalRealAccounts <= 0 && (int) $totalFTD <= 0 && 
											(int) $totalComs <= 0 && 
                                            (int) $totalDeposits <= 0 && 
                                            (int) $totalRealAccounts <= 0 && (int) $totalFTD <= 0 && 
                                            (int) $totalImpressions <= 0 && (int) $totalClicks <= 0
											
											
                                        ) {
                                            continue;
                                        }
                                    }
							
									
									
                            if (isset($display_type) && !empty($display_type)) {
                                $listReport .= '<td>' . $arrRange['from'] . ' - ' . $arrRange['to'] . '</td>';
                            }
                            
                            $listReport .= '
                                <td>'.$ww['id'].'</td>
                                <td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
                                <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';
                            
                            if ($campID['title']) {
                                if ($ww['campID']) {
                                    $listReport .= '<td align="left">' . $ww['campID'] . '</td>';
                                } else {
                                    $listReport .= '<td align="left"></td>';
                                }	
                            }
                            
                            
													// echo 'isset($_GET:  ' . isset($_GET['showAllRecords']).'<Br>';
							// echo 'showAllRecords:  '  .($_GET['showAllRecords']).'<Br>';
							// die();
					
									
									
						
                            $listReport .= 
                                (!$set->isNetwork ? '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>' : '') . '
                                '.($set->ShowIMUserOnAffiliatesList ? '<td align="left">'.$ww['IMUser'].'</td>' : ''). '
								<td><a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>
                                <td style="text-align: center;">' . @number_format($totalImpressions, 0) . '</td>
                                <td style="text-align: center;">' . @number_format($totalClicks, 0) . '</td>
                                <td style="text-align: center;">' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format(($totalRealAccounts / $totalClicks) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format(($totalFTD / $totalClicks) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @price($totalComs / $totalClicks) . '</td>' . 
                                ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                
                                '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=transactions&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                
                                <td style="text-align: center;">' . price($totalBonus) . '</td>
                                <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                <td style="text-align: center;">' . price($totalVolume) . '</td>
                                '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                '.(($displayForex && $showPNL) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                <td style="text-align: center;"><a href="/admin/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
                                <td style="text-align: center;">' . price($totalComs) . '</td>
                                <td style="text-align: center;">' . listGroups($ww['group_id'], 1) . '</td>
                            </tr>';
                        }
                        
                        $intAffiliatesCombinedCount++;
						$totalVolume = 0;
                    }
                    
                    unset($arrRange); // Clear up the memory.
                } // End of time-periods loop.
                
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable  = 1;
		$set->totalRows  = isset($groupByAff) && 1 ==$groupByAff ? $intAffiliatesCombinedCount : $l;
		$set->content   .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
		<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="affiliate" />
			<input type="hidden" name="search" value="1" />
			<table><tr>
                            <td>'.lang('Period').'</td>
                            <td>'.lang('Affiliate ID').'</td>
                            <td>'.lang('Groups').'</td>
                            <td>'.lang('Merchant').'</td>
                            <td>'.lang('Show Affiliates').'</td>
                            <td>'.lang('Search Type').'</td>'
                            . ($intMerchantCount > 1 ? '<td>' . lang('Group Merchants') . '</td> ': '') .
                            '<td></td>
				</tr><tr>
				<td>'.timeFrame($from,$to).'</td>
				<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="alert_date" style="width: 100px;" /></td>
                                <td width="100"><select name="group_id" style="width: 100px;"><option value="">'.lang('All Groups').'</option><option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listGroups($group_id).'</select></td>
				<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
				<td><select name="showAllRecords" style="width: 150px;"><option id=1 value="1">'.lang('Only Active Affiliates').'</option><option id=0 value="0" '.(isset($showAllRecords) && $showAllRecords==0 ? ' selected ' : '' ).'>'.lang('All Affiliates').'</option></select></td>
				<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>'
				. ($intMerchantCount > 1 ? '<td><input type="checkbox" id="groupByAff" name="groupByAff" '.($groupMerchantsPerAffiliate==1 ? 'checked' : '').'/>
				<input type="hidden" id="groupByAffVal" value="'.($groupMerchantsPerAffiliate).'" name="groupByAff" /></td>' : '') .
				
				'<script type="text/javascript">
					$(document).ready(function(){
						$("#groupByAff").change(function(e){
							if($("#groupByAff").is(":checked")){
								$("#groupByAffVal").val("1");
							}else{
								$("#groupByAffVal").val("0");
							}
						});
					});
				</script>
				
				<td><input type="submit" value="'.lang('View').'" /></td>
			</tr></table>
			</form>
			
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 3000px;">'.lang('Affiliate Report').'</div>
			<div style="background: #F8F8F8;">';
			
				$tableStr='<table width="3000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>'
                                                . (isset($display_type) && !empty($display_type) ? '<th>' . lang('Period') . '</th>' : '') . '
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Username').'</th>
						<th>'.lang('Full Name').'</th>
						'.($campID['title'] ? '<th>'.lang($campID['title']).'</th>' : '') .
						(!$set->isNetwork ? '<th>'.lang('E-Mail').'</th>' : '') . 
						($groupMerchantsPerAffiliate==0 ? '<th>'.lang('Merchant').'</th>':'').'
						'.($set->ShowIMUserOnAffiliatesList ? '<th>'.lang('IM-User').'</th>' : ''). '
						<th>'.lang('Website').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>' . 
                                                ($hideDemoAndLeads ? '' : '<th>'.lang(ptitle('Lead')).'</th>
                                                                           <th>'.lang(ptitle('Demo')).'</th>') . 
						
						'<th>'.lang(ptitle('Accounts')).'</th>
						'.($showCasinoFields ? '<th>'.lang(ptitle('Frozens')).'</th>' : '<th>|FROZEN|</th>').'
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang('Affiliate Risk').'</th>
						<th>'.lang(ptitle('Volume')).'</th>
						'.(($displayForex) ? '<th>'.lang('Spread Amount').'</th>' : '').'
						'.(($displayForex && $showPNL) ? '<th>'.lang('PNL').'</th>' : '').'
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Group').'</th>
					</tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
						<th><b>'.lang('Total').':</b></th>'
                                                . (isset($display_type) && !empty($display_type) ? '<th></th>' : '') . '
						<th></th>
						<th></th>
						<th></th>
						'.($campID['title'] ? '<th></th>' : '') .'
                                                <th></th>
                                                ' . ($groupMerchantsPerAffiliate == 0 ? '<th></th>' : '') . '
                                                ' . ($set->ShowIMUserOnAffiliatesList ? '<th></th>' : '') . '
						<th>'.$totalImpressionsM.'</th>
						<th>'.$totalClicksM.'</th>
						<th>' . @number_format(($totalClicksM / $totalImpressionsM) * 100, 2) . ' %</th>
						<th>' . @number_format(($totalRealAccountsM / $totalClicksM) * 100, 2) . ' %</th>
						<th>' . @number_format(($totalFTDM / $totalClicksM) * 100, 2) . ' %</th>
						<th>' . @price($totalComsM / $totalClicksM) . '</th>' . 
                                                ($hideDemoAndLeads ? '' : '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">' . $totalLeadsAccountsM . '</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">' . $totalDemoAccountsM . '</a></th>') .
						
						'<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">' . $totalRealAccountsM . '</a></th>
						'.($showCasinoFields ? '<th>' . $totalFrozensM . '</th>' : '<th>|FROZEN|</th>').'
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTDM.'</a></th>
						
						<th>' . price($totalFTDAmountM) . '</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">' . $totalRealFtdM . '</a></th>
						
						<th>' . price($totalRealFtdAmountM) . '</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">' . $totalDepositsM . '</a></th>
						<th>' . price($totalDepositAmountM) . '</th>
						
						<th>' . price($totalBonusM) . '</th>
						<th>' . price($totalWithdrawalM) . '</th>
						<th>' . price($totalChargeBackM) . '</th>
						<th></th>
						<th>' . price($totalVolumeM) . '</th>
						' . (($displayForex) ? '<th>' . price($totalSpreadAmountM) . '</th>' : '') . '
						' . (($displayForex && $showPNL) ? '<th>' . price($totalpnlM) . '</th>' : '') . '
						<th>' . price($totalNetRevenueM) . '</th>
						<th>' . price($totalComsM) . '</th>
						<th></th>
                                            </tr>
                                        </tfoot>
					<tbody>
					' . $listReport . '
                                        </tbody>
				</table>';
                                
		if ($showCasinoFields) {
                    $tableStr = str_replace('<td>|FROZEN|</td>', '<td></td>', $tableStr);
                    $tableStr = str_replace('<th>|FROZEN|</th>', '<th></th>', $tableStr);
		} else {
                    $tableStr = str_replace('<td>|FROZEN|</td>', '', $tableStr);
                    $tableStr = str_replace('<th>|FROZEN|</th>', '', $tableStr);
		}
              
		$set->content .= $tableStr . '</div>' . getPager();
		excelExporter($tableStr, 'Affiliate');
		theme();
		break;
                
                
                 
                
                
                
	
	case "group":
                $arrGroups = [
                    ['id' => 0, 'title' => 'General'],
                ];
                
		$set->pageTitle = lang('Groups Report');
		
		
		
		/* $sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
                    $merchantsArray = array();
					$displayForex = 0;
					$mer_rsc = function_mysql_query($sql,__FILE__);
					while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
						 */
						$merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
					foreach ($merchantsAr as $arrMerchant) {
						
						if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1;
					
						$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}


					
		
		
		
		$sql = "SELECT id, title FROM groups WHERE valid = 1 ORDER BY id DESC;";
		$qq = function_mysql_query($sql,__FILE__);
                
                while ($ww = mysql_fetch_assoc($qq)) {
                    $arrGroups[] = $ww;
                    unset($ww);
                }
                
                foreach ($arrGroups as $ww) {
					/* 
					$key = array_keys($arrGroups);
					$size = sizeOf($key);
					for ($grpCount=0; $grpCount<$size; $grpCount++) {
						$ww = $arrGroups[$key[$grpCount]] ;
						
						 */
						
						
                    if ($ww['id'] != $group_id && isset($group_id) && !empty($group_id)) {
                        continue;
                    }
                    
                    $l++;
                    
                    // List of wallets.
                    $arrWallets = array();
                    $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1;";
                    $resourceWallets = function_mysql_query($sql,__FILE__);
                    
                    while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                        $arrWallets[$arrWallet['wallet_id']] = false;
                        unset($arrWallet);
                    }
                    
                    $mers = getMerchants();
                    
					// $merchantqq = function_mysql_query("SELECT id,name,producttype,rev_formula,wallet_id FROM merchants WHERE valid='1'",__FILE__);
                    // while ($merchantww = mysql_fetch_assoc($merchantqq)) {
						foreach($mers as $merchantww) {
						
			    // Check if this is a first itaration on given wallet.
							
							
								                    if ($set->multiMerchantsPerTrader==1)
						
                            $needToSkipMerchant = $arrWallets[$merchantww['wallet_id']];
                            
				else 
					$needToSkipMerchant= false;
				
				
                            
                            $formula = $merchantww['rev_formula'];
                            $fromDate = strtotime($from);
                            $toDate = strtotime($to);
			    
				if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
					else $diff = 0;
				
				if($display_type == "weekly") {
					$theFromDay = date("w",strtotime($from))+1;
					$theToDay = date("w",strtotime($to))+1;
					$daysUntilEndWeek = (7-$theFromDay);
					$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
					
					$diff = round($diff/8);
					if($theFromDay > $theToDay) $diff = $diff+1;
				}
				
				if ($display_type == "monthly") {
					$endOfthisMonth = date("t",strtotime($from));
					if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
					$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
					$date1 = strtotime($from);
					$date2 = strtotime($to);
					$months = 0;
					while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
					if($diff == 0) $lastDayOfMonth = $to;
				}
				
				for ($n = 0; $n <= $diff; $n++) {
                                    $netRevenuePerTimePeriod = 0;
                                    $totalTraffic=0;
                                    $totalLeads=0;
                                    $totalDemo=0;
                                    $totalReal=0;
                                    $ftd=0;
                                    $ftd_amount=0;
                                    $depositingAccounts=0;
                                    $sumDeposits=0;
                                    $revenue=0;
                                    $volume=0;
                                    $bonus=0;
                                    $withdrawal=0;
                                    $chargeback=0;
                                    $netRevenue=0;
									$depositsAmount=0;
                                    $totalCom=0;
                                    $real_ftd = 0;
                                    $real_ftd_amount = 0;
                                    
                                    // From Show on weekly
                                    if ($newFrom) {
                                        $showFrom = $newFrom;
                                    } else {
                                        $showFrom = $from;
                                    }
                                    
                                    $searchInSql = "";
                                    
                                    if ($display_type == "daily") {
                                        $searchInSql = "= '".date("Y-m-d", strtotime($from." +".$n." day"))."'";
                                            
                                    } elseif ($display_type == "weekly") {
                                        if ($n == 0) { // Weekly First Loop
                                            $searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
                                            $newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
                                        } elseif ($n == $diff) { // Last Loop - Last week
                                            $searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
                                            $newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
                                        } else { // Else Loops
                                            $newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
                                            $searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
                                            $newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
                                        }
                                        
                                    } elseif ($display_type == "weekly") { 
                                            if($n==0){ // Weekly First Loop
                                                    $searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
                                                    $newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
                                            } else if ($n == $diff) { // Last Loop - Last week
                                                    $searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
                                                    $newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
                                            } else { // Else Loops
                                                    $newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
                                                    $searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
                                                    $newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
                                            }
                                            
                                    } elseif ($display_type == "monthly"){
                                            if($n==0){ // Monthly First Loop
                                                    $searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
                                                    $newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
                                            } else if ($n == $diff) { // Last Loop - Last week
                                                    $searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
                                                    $newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
                                            } else { // Else Loops
                                                    $numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
                                                    $newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
                                                    $searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
                                                    $newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
                                            }
                                        
                                    } else { // If no Display_type 
                                        $searchInSql = "BETWEEN '".$from."' AND '".$to."'";
                                    }
                                    
                                    
                                    // To Show on weekly
                                    if ($display_type == "weekly") {
                                        if ($newTo) {
                                            $showTo = $newTo; 
                                        } else {
                                            $showTo = $endOfWeek;
                                        }
                                    }
                                    
                                    // To Show on monthly
                                    if ($display_type == "monthly") {
                                        if ($newTo) {
                                            $showTo = $newTo; 
                                        } else {
                                            $showTo = $lastDayOfMonth;
                                        }
                                    }
                                    
                                    if ($n == $diff) {
                                        $showTo = $to;
                                    }
                                    
                                    
                                    // Time-range calculation.
                                    $arrTmpTime  = ['from' => $from, 'to' => $to,];
                                    if (!empty($searchInSql)) {
                                        $arrTmpTime  = getDateRangeFromSoCalledSearchType($searchInSql);
                                        $searchInSql = " BETWEEN '" . $arrTmpTime['from'] . "' AND '" . $arrTmpTime['to'] . "' ";
                                    }
                                    
                                    
                                    $totalTraffic                = array();
                                    $arrClicksAndImpressions     = getClicksAndImpressions($arrTmpTime['from'], $arrTmpTime['to'], $merchantww['id'], null, $ww['id']);
                                    $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                                    $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                                    
                                    $merchantName = strtolower($merchantww['name']);
                                    $merchantID = $merchantww['id'];
				    
                                    $sql = "SELECT * FROM data_reg WHERE merchant_id = '" . $merchantID . "' and group_id='".$ww['id']."' AND rdate ".$searchInSql;
                                    $regqq = function_mysql_query($sql,__FILE__);
                                    
                                    $arrTierCplCountCommissionParams = [];
                                    
                                    while ($regww = mysql_fetch_assoc($regqq)) {
                                        
										$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
										
                                        $boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
                                        
                                        if ($regww['type'] == "lead") $totalLeads++;
                                        if ($regww['type'] == "demo") $totalDemo++;
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
                                                
                                                $totalCom += getCommission($arrTmpTime['from'], $arrTmpTime['to'], 0, $ww['id'], $arrDealTypeDefaults, $arrTmp);
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
                                            
                                            $totalReal++;
                                        }
                                    }
                                    
                                    
                                    // TIER CPL.
                                    foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
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
				    
                                    
                                    $ftdUsers = '';
                                    
                                    if (!$needToSkipMerchant) {
                                        // IMPORTANT in case of "per-group" calculation.
                                        // DO NOT modify even a single character at the following line!!!
                                        $intTmpGroupId = 0 == $ww['id'] ? ' 0 ' : $ww['id'];
                                        $arrFtds = getTotalFtds($arrTmpTime['from'], $arrTmpTime['to'], 0, $merchantww['id'], $merchantww['wallet_id'], $intTmpGroupId);
                                        unset($intTmpGroupId);
                                        
                                        foreach ($arrFtds as $arrFtd) {
										/* 	
											$key = array_keys($arrFtds);
					$size = sizeOf($key);
					for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
						$arrFtd = $arrFtds[$key[$ftdCount]] ; */
						
						
											
                                            $real_ftd++;
                                            $real_ftd_amount += $arrFtd['amount'];
                                            
                                            $beforeNewFTD = $ftd;
                                            getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
                                            
                                            if ($beforeNewFTD != $ftd) {
                                                $arrFtd['isFTD'] = true;
                                                $totalCom += getCommission($arrTmpTime['from'], $arrTmpTime['to'], 0, $ww['id'], $arrDealTypeDefaults, $arrFtd);
                                            }
                                            unset($arrFtd); 
                                        }
                                    }
                                    
                                    
                                    $sql = "SELECT id AS id, merchants AS merchants FROM affiliates "
                                         . "WHERE valid = 1 AND group_id = " . $ww['id'];
                                    
                                    $resourceAffiliates = function_mysql_query($sql,__FILE__);
                                    
                                    while ($arrAff = mysql_fetch_assoc($resourceAffiliates)) {
                                        $arrMerchantsAffiliate = explode('|', $arrAff['merchants']);
                                        if (!in_array($merchantww['id'], $arrMerchantsAffiliate)) {
                                            continue;
                                        }
                                        
                                        unset($arrAff);
                                    }
                                    
                                    if (isset($display_type) && !empty($display_type)) {
                                        $netRevenue = $netRevenuePerTimePeriod;
                                    }
                                    
                                    
                                    $sql = "SELECT *, tb1.type AS data_sales_type  FROM data_sales as tb1 "
                                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo' "
                                            . "WHERE tb1.merchant_id = '" . $merchantID . "' "
                                            . "and tb1.group_id='".($ww['id'] ? $ww['id'] : '0')."' AND tb1.rdate ".$searchInSql."";
                                    
                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                                        //if ($salesww['type'] == "deposit") { // OLD.
										if ($salesww['data_sales_type'] == "deposit") {   // NEW.
                                            $sumDeposits += $salesww['amount'];
											$depositsAmount +=$salesww['amount'];
                                            $depositingAccounts++;
                                        }
                                        
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
                                                'amount'       => $salesww['amount'],
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
									
									
									if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
										
											while ($arrAff = mysql_fetch_assoc($resourceAffiliates)) {
											//foreach($arrMerchantsAffiliate as $affiliateInArray) {
												
												
												
													$arrRevenueRanges = getRevenueDealTypeByRange($arrTmpTime['from'], $arrTmpTime['to'], $merchantww['id'], $arrAff['id'], $arrDealTypeDefaults, $searchInSql);
													$intTotalRevenue = 0;
													
													foreach ($arrRevenueRanges as $arrRange) {
														$strWhere = 'WHERE merchant_id = ' . $merchantww['id'] . ' AND rdate BETWEEN "' . $arrRange['from'] 
																  . '" AND "' . $arrRange['to'] 
																  . '" AND affiliate_id = "' . $arrAff['id'] . '" ';

														$intCurrentRevenue = getRevenue($strWhere, $merchantww['producttype']);
														
														$intTotalRevenue    += $intCurrentRevenue;
														$row                 = array();
														$row['merchant_id']  = $merchantww['id'];
														$row['affiliate_id'] = $arrAff['id'];
														$row['banner_id']    = 0;
														$row['rdate']        = $arrRange['from'];
														$row['amount']       = $intCurrentRevenue;
														$row['isFTD']        = false;
														$totalCom           += getCommission($arrRange['from'], $arrRange['to'], 1, $ww['id'], $arrDealTypeDefaults, $row);
														unset($arrRange, $strWhere, $row, $intCurrentRevenue);
													}
													
													$netRevenuePerTimePeriod += $intTotalRevenue;
													$netRevenue              += $intTotalRevenue;
											}
                                        } else {
                                            // $netRevenuePerTimePeriod += round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
											$netRevenuePerTimePeriod =  round(getRevenue($searchInSql,$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
											$netRevenue =  round(getRevenue($searchInSql,$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
											
                                            // $netRevenue              += round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
                                        }
										
										
										
										
						
          if (strtolower($merchantww['producttype']) == 'forex') {
                                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                                . 'WHERE affiliate_id = ' . $ww['id'] . ' AND profile_id = ' . $ww['ProfileId']
                                                . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
                                                . ' GROUP BY affiliate_id';
                                        
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                            $spreadAmount  = $ts['totalSpread'];
                                            $volume       += $ts['totalTO'];
                                            $pnl           = $ts['totalPnl'];
                                        }
                                        
                                        // $displayForex = 1;
										
															
						$totalLots  = 0;
						
						
							
							//lots 

							
						
						
						
							
							
						$sql = 'SELECT turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
                                         . 'WHERE merchant_id = "' . $merchantww['id'] . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                             . ' and group_id = ' . $ww['id'] ;

                           // die ($sql);
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
										
										$earliestTimeForLot = date('Y-m-d H:i:s');
										
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											
											if($ts['affiliate_id']==null) {
													continue;
											}
							
											// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
													$totalLots  = $ts['totalTurnOver'];
													// echo $totalLots
														$tradersProccessedForLots[$merchantww['id'] . '-' . $ts['trader_id']] = $merchantww['id'] . '-' . $ts['trader_id'];
														$lotdate = $ts['rdate'];
														$ex = explode(' ' , $lotdate);
														$lotdate = $ex[0];
															if ($earliestTimeForLot>$lotdate)
															$earliestTimeForLot = $lotdate;
														
														
														
														$row = [
																	'merchant_id'  => $merchantww['id'],
																	'affiliate_id' => $ts['affiliate_id'],
																	'rdate'        => $earliestTimeForLot,
																	'banner_id'    => $ts['banner_id'],
																	'trader_id'    => $ts['trader_id'],
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
													$a = getCommission($from, $to, 0, $ww['id'], $arrDealTypeDefaults, $row);
													// echo 'com: ' . $a .'<br>';
													$totalCom += $a;
											// }
										}
										
		  }	
										
									
									
                                    
                                    $filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$n.' Day') : strtotime($showFrom));
                                    $filterTo   = ($display_type == "daily" ? strtotime($from.' +'.$n.' Day') : strtotime($showTo));
				    
                                    
									
											
				
									
									
									
									
                                    $listReport .= '<tr>
                                            '.($display_type == "daily" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($from.' +'.$n.' Day')).'</td>' : '').'
                                            '.($display_type == "weekly" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($showFrom)).' - '.date("Y/m/d", strtotime($showTo)).'</td>' : '').'
                                            '.($display_type == "monthly" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($showFrom)).' - '.date("Y/m/d", strtotime($showTo)).'</td>' : '').'
                                            <td>'.($ww['id'] ? $ww['id'] : '0').'</td>
                                            <td>'.($ww['title'] ? $ww['title'] : lang('General')).'</td>
                                            <td mid="'.$merchantww['id'].'">'.($merchantww['name'] ? $merchantww['name'] : '-').'</td>
                                            <td>'.@number_format($totalTraffic['totalViews'],0).'</td>
                                            <td>'.@number_format($totalTraffic['totalClicks'],0).'</td>
                                            <td>'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>
                                            <td>'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>
                                            <td>'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>
                                            <td>'.@price($totalCom/$totalTraffic['totalClicks']).'</td>
                                            <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=lead">'.$totalLeads.'</a></td>
                                            <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=demo">'.$totalDemo.'</a></td>
                                            <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=real">'.$totalReal.'</a></td>
                                            <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=ftd">'.$ftd.'</a></td>
                                            <td>'.price($ftd_amount).'</td>
                                            <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=totalftd">'.$real_ftd.'</a></td>
                                            <td>'.price($real_ftd_amount).'</td>
                                            <td style="text-align: center;"><a href="/admin/reports.php?act=transactions&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=deposit">'.$depositingAccounts.'</a></td>
                                            <td>'.price($sumDeposits).'</td>
                                            <td>'.price($volume).'</td>
                                            <td>'.price($bonus).'</td>
                                            <td>'.price($withdrawal).'</td>
                                            <td>'.price($chargeback).'</td>
                                            <td>'.price($netRevenue).'</td>
                                            <td>'.price($totalCom).'</td>
                                        </tr>';
                                    
                                    $totalImpressions += $totalTraffic['totalViews'];
                                    $totalClicks += $totalTraffic['totalClicks'];
                                    $totalLeadsAccounts += $totalLeads;
                                    $totalDemoAccounts += $totalDemo;
                                    $totalRealAccounts += $totalReal;
                                    $totalFTD += $ftd;
                                    $totalDeposits += $depositingAccounts;
                                    $totalFTDAmount += $ftd_amount;
                                    $totalDepositAmount += $sumDeposits;
                                    $totalVolume += $volume;
                                    $totalBonus += $bonus;
                                    $totalWithdrawal += $withdrawal;
                                    $totalChargeBack += $chargeback;
                                    $totalNetRevenue += $netRevenue;
                                    $totalComs += $totalCom;
                                    $totalRealFtd += $real_ftd;
                                    $totalRealFtdAmount += $real_ftd_amount;
                                    
                                    $l++;
				}
                            
                            // Mark given wallet as processed.
                            $arrWallets[$merchantww['wallet_id']] = true;
			}
		}
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
		$set->sortTable  = 1;
		$set->content   .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="group" />
			<table>
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Group').'</td>
					<td>'.lang('Search Type').'</td>
					<td></td>
				</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="group_id" style="width: 150px;"><option value="">'.lang('General').'</option>'.listGroups($group_id).'</select></td>
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 2000px;">'.lang('Affiliate Report').'</div>
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						'.($display_type ? '<th>'.lang('Period').'</th>' : '').'
						<th>'.lang('Group ID').'</th>
						<th>'.lang('Group Name').'</th>
						<th>'.lang('Merchant').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th><b>'.lang('Total').':</b></th>
						<th></th>
						<th></th>
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th>'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th>'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th>'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th>'.price($totalBonus).'</th>
						<th>'.price($totalWithdrawal).'</th>
						<th>'.price($totalChargeBack).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th>'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
		
		excelExporter($tableStr, 'Group');
		$set->content .= $tableStr . '</div>' . getPager();
		theme();
		break;
		
		
                
                
                
                
        case "profile":
                $set->pageTitle   = lang('Profile Report');
                $showLeadsAndDemo = false;
                $where            = '';
                
				$sql              = 'SELECT extraMemberParamName AS title FROM merchants';// WHERE id = ' . aesDec($_COOKIE['mid']);
				$campID           = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
				
				
                    $merchantsArray = array();
					$displayForex = 0;
					
					$merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
					foreach ($merchantsAr as $arrMerchant) {
						
                // $sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
						
						if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1;
						
					
						$merchantsArray[$arrMerchant['id']] = $arrMerchant;
					}
			    // $sql = "SELECT COUNT(id) AS count FROM merchants WHERE valid = 1;";
                // $arrMerchantCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));

                $intMerchantCount =count($merchantsArray);
				
				
                
                /**
                 * In "manager" report, 
                 * switch the following line to an explicit assignment of group-id.
                 */
                $group_id = isset($group_id) && $group_id != '' ? $group_id : null;
                
                if (isset($group_id) && $group_id != '') {
                    $where .= ' AND aff.group_id = ' . $group_id . ' ';
                }
                
                
                if (isset($groupByAff) && $groupByAff == 1) {
                    $groupMerchantsPerAffiliate = 1;
                } else {
                    $groupMerchantsPerAffiliate = 0;
                }
                
                if (isset($affiliate_id) && !empty($affiliate_id) && is_numeric($affiliate_id)) {
                    $where .= ' AND aff.id = ' . $affiliate_id . ' ';
                } elseif (isset($affiliate_id) && !empty($affiliate_id)) {
                    $where .= " AND (lower(username) LIKE '%" . trim(strtolower($affiliate_id)) . "%' ";
                        if (!empty($campID['title'])) {
                            $where .= " or affiliates.id in (SELECT affiliateID FROM  `affiliates_campaigns_relations` WHERE  `campID` LIKE  '%".strtolower($affiliate_id)."%' )";
                        }
                        
                    $where .= " ) ";
                }
                
                
                // Initialize total counters for all affiliates.
                $totalImpressionsM = 0;
                $totalClicksM = 0;
                $totalLeadsAccountsM = 0;
                $totalDemoAccountsM = 0;
                $totalRealAccountsM = 0;
                $totalFTDM = 0;
                $totalDepositsM = 0;
                $totalFTDAmountM = 0;
                $totalDepositAmountM = 0;
                $totalVolumeM = 0;
                $totalBonusM = 0;
                $totalWithdrawalM = 0;
                $totalChargeBackM = 0;
                $totalNetRevenueM = 0;
                $totalComsM = 0;
                $netRevenueM = 0;
                $totalFruadM = 0;
                $totalFrozensM = 0;
                $totalRealFtdM = 0;
                $totalRealFtdAmountM = 0;
                
                $l = 0;
                $arrRanges = [];
                
                switch ($display_type) {
                    case 'monthly':
                        $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_MONTHLY);
                        break;
                    case 'weekly':
                        $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_WEEKLY);
                        break;
                    case 'daily':
                        $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_DAILY_RANGE);
                        break;
                    default:
                        $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_NONE);
                        break;
                }
                	
                // Time-periods loop.
                foreach ($arrRanges as $arrRange) {
					/* 
					$key = array_keys($arrRanges);
					$size = sizeOf($key);
					for ($timePeriodsCount=0; $timePeriodsCount<$size; $timePeriodsCount++) {
						$arrRange = $arrRanges[$key[$timePeriodsCount]] ;
						
						 */
						
						
                    
                    $sql = "SELECT aff_profiles.id AS ProfileId, aff_profiles.name AS ProfileName, aff_profiles.url AS URL, acr.campID, aff.* 
                            FROM affiliates_profiles AS aff_profiles 
                            INNER JOIN  affiliates AS aff ON aff_profiles.affiliate_id = aff.id AND aff.valid = 1 AND aff_profiles.valid = 1  
                            LEFT JOIN affiliates_campaigns_relations acr ON aff.id = acr.affiliateID AND aff.valid = 1  
                            WHERE aff.valid = 1 AND aff_profiles.valid = 1 " . $where . " 
                            ORDER BY aff.id DESC ";
					
                    $qq = function_mysql_query($sql,__FILE__);
                    
                    $intAffiliatesCombinedCount = 0;
                    $showCasinoFields           = 0;
                    $totalRealFtd               = 0;
                    $totalRealFtdAmount         = 0;
                    
                    while ($ww = mysql_fetch_assoc($qq)) {
                        // List of wallets.
                        $arrWallets = array();
                        $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1 " 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        
                        $resourceWallets = function_mysql_query($sql,__FILE__);
                        
                        while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                            $arrWallets[$arrWallet['wallet_id']] = false;
                            unset($arrWallet);
                        }
                        
                        
                        $sql = 'SELECT COUNT(id) AS count FROM merchants WHERE valid = 1 ' 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        
                        $arrMerchantsCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                        $intMerchantsCount = (int) $arrMerchantsCount['count'];
                        
                        
                        // Initialize total counters per affiliate.
                        $totalImpressions = 0;
                        $totalClicks = 0;
                        $totalLeadsAccounts = 0;
                        $totalDemoAccounts = 0;
                        $totalRealAccounts = 0;
                        $totalFTD = 0;
                        $totalDeposits = 0;
                        $totalFTDAmount = 0;
                        $totalDepositAmount = 0;
                        $totalVolume = 0;
                        $totalBonus = 0;
                        $totalWithdrawal = 0;
                        $totalChargeBack = 0;
                        $totalNetRevenue = 0;
                        $totalComs = 0;
                        $totalSpreadAmount = 0;
                        $totalTurnoverAmount = 0;
                        $totalpnl = 0;
                        $totalFrozens = 0;
                        $totalRealFtd = 0;
                        $totalRealFtdAmount = 0;
                        
                        // $sql = 'SELECT id,name,type,producttype,rev_formula, wallet_id  FROM merchants WHERE valid = 1 ' 
						$customMerchant_id = isset($merchant_id) && !empty($merchant_id) ?  $merchant_id : 0;
						
						$merchantsA = getMerchants($customMerchant_id,1);
                        
						
                        // $merchantqq = function_mysql_query($sql,__FILE__);
                        // while ($merchantww = mysql_fetch_assoc($merchantqq)) {
							
							foreach ($merchantsA as $merchantww) {
                        
                            $arrMerchantsAffiliate = explode('|', $ww['merchants']);
                            if (!in_array($merchantww['id'], $arrMerchantsAffiliate)) {
                                continue;
                            }
                            
                            // Check if this is a first itaration on given wallet.
								                    if ($set->multiMerchantsPerTrader==1)
						
                            $needToSkipMerchant  = $arrWallets[$merchantww['wallet_id']];
                            
                            
				else 
					$needToSkipMerchant= false;
				
				
							
							
                            $merchantww['count'] = $intMerchantsCount;
                            
                            $showLeadsAndDemo = strtolower($merchantww['producttype']) == 'sportsbetting' 
                                             || strtolower($merchantww['producttype']) == 'casino';
                            
                            if (strtolower($merchantww['producttype']) == 'casino') {
                                $showCasinoFields = 1;
                            }
                            
                            // Initialize total counters per affiliate-merchant.
                            $formula = $merchantww['rev_formula'];
                            $totalTraffic=0;
                            $totalLeads=0;
                            $totalDemo=0;
                            $totalReal=0;
                            $ftd=0;
                            $pnl = 0;
                            $volume=0;
                            $bonus=0;
                            $spreadAmount = 0;
                            $turnoverAmount = 0;
                            $withdrawal=0;
                            $chargeback=0;
                            $revenue=0;
                            $ftd_amount=0;
                            $depositingAccounts=0;
                            $sumDeposits=0;
                            $netRevenue=0;
                            $totalCom=0;
                            $real_ftd = 0;
                            $real_ftd_amount = 0;
                            
                            
                            $totalTraffic                = [];
                            $arrClicksAndImpressions     = getClicksAndImpressions($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id'], $group_id, $ww['ProfileId']);
                            $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                            
                            
                            $merchantName = strtolower($merchantww['name']);
                            $sql = 'SELECT SUM(IF(status="frozen",1,0)) AS total FROM data_reg '
                                    . 'WHERE affiliate_id="'.$ww['id'].'" AND merchant_id="'.$merchantww['id'] 
                                    . '" AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . '" '
                                    . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
                                    . ' AND profile_id = ' . $ww['ProfileId'];
                            
                            $frozensQ = function_mysql_query($sql,__FILE__);
                            $frozens  = mysql_fetch_assoc($frozensQ);
                            $frozens  = $frozens['total'] ? $frozens['total'] : 0;
                            
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id = " . $ww['id'] . " "
                                    . "AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' "
                                    . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
                                    . ' AND profile_id = ' . $ww['ProfileId'];
                            
                            $regqq = function_mysql_query($sql,__FILE__);
                            
                            $arrTierCplCountCommissionParams = [];
                            
                            while ($regww = mysql_fetch_assoc($regqq)) {
                                $strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
								
                                $boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
                                
                                if ($regww['type'] == 'lead') $totalLeads++;
                                if ($regww['type'] == 'demo') $totalDemo++;
                                if ($regww['type'] == 'real') {
                                    if (!$boolTierCplCount) {
                                        $arrTmp = [
                                            'merchant_id'  => $regww['merchant_id'],
                                            'affiliate_id' => $regww['affiliate_id'],
                                            'rdate'        => $regww['rdate'],
                                            'banner_id'    => $regww['banner_id'],
                                            'trader_id'    => $regww['trader_id'],
                                            'profile_id'   => $regww['profile_id'],
                                        ];
                                        
                                         $totalCom += getCommission($arrRange['from'], $arrRange['to'], 0, (is_null($group_id) ? -1 : $group_id), $arrDealTypeDefaults, $arrTmp);
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
                                    
                                    $totalReal++;
                                }
                            }
                            
                            
                            // TIER CPL.
                            foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
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
                            
                            
                            
                            $ftdUsers = '';
                            
                            if (!$needToSkipMerchant) {
                                $arrFtds = getTotalFtds(
                                    $arrRange['from'], 
                                    $arrRange['to'], 
                                    $ww['id'], 
                                    $merchantww['id'], 
                                    $merchantww['wallet_id'], 
                                    (is_null($group_id) ? 0 : $group_id),
                                    0,
                                    $ww['ProfileId']
                                );
                                
								
						/* 					
											$key = array_keys($arrFtds);
					$size = sizeOf($key);
					for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
						$arrFtd = $arrFtds[$key[$ftdCount]] ;
						
						 */
						
						
/* 						   $size = sizeOf($arrFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrFtd = $arrFtds[$ftdCount] ;
									
 */									
									
                                foreach ($arrFtds as $arrFtd) {
                                    $real_ftd++;
                                    $real_ftd_amount += $arrFtd['amount'];
                                    
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
                                    
                                    if ($beforeNewFTD != $ftd) {
                                        $arrFtd['isFTD'] = true;
                                        $totalCom += getCommission($arrRange['from'], $arrRange['to'], 0, (is_null($group_id) ? -1 : $group_id), $arrDealTypeDefaults, $arrFtd);
                                    }
                                    unset($arrFtd);
                                }
                            }
                            
                            if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                                $arrRevenueRanges = getRevenueDealTypeByRange($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id'], $arrDealTypeDefaults);
                                $intTotalRevenue  = 0;
                                
                                foreach ($arrRevenueRanges as $arrRange2) {
                                    $strWhere = 'WHERE merchant_id = ' . $merchantww['id'] 
                                              . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                              . '" AND affiliate_id = "' . $ww['id'] . '" AND profile_id = ' . $ww['ProfileId']
                                              . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ');
                                    
                                    $intCurrentRevenue = getRevenue(
                                        $strWhere,
                                        $merchantww['producttype'],
                                        $sumDeposits,
                                        $bonus,
                                        $withdrawal,
                                        $pnl,
                                        $turnoverAmount,
                                        $spreadAmount,
                                        $formula
                                    );
                                    
                                    $intTotalRevenue    += $intCurrentRevenue;
                                    $row                 = array();
                                    $row['merchant_id']  = $merchantww['id'];
                                    $row['affiliate_id'] = $ww['id'];
                                    $row['banner_id']    = 0;
                                    $row['rdate']        = $arrRange2['from'];
                                    $row['amount']       = $intCurrentRevenue;
                                    $row['isFTD']        = false;
                                    $totalCom           += getCommission(
                                        $arrRange2['from'], 
                                        $arrRange2['to'], 
                                        1, 
                                        (is_null($group_id) ? -1 : $group_id), 
                                        $arrDealTypeDefaults, 
                                        $row
                                    );
                                    
                                    unset($arrRange2, $strWhere);
                                }
                                
                                $netRevenue = $intTotalRevenue;
                                
                            } else {
                                // $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
								$netRevenue =  round(getRevenue($searchInSql,$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
                            }
                            
                            
                                    $sql = "SELECT * FROM data_sales as tb1 "
                                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo' "
                                            . "WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND tb1.affiliate_id='".$ww['id']."' "
                                            . "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' "
                                            . (is_null($group_id) ? '' : ' AND tb1.group_id = ' . $group_id . ' ')
                                            . ' AND tb1.profile_id = ' . $ww['ProfileId'];
                                    
                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    $volume = 0;
                                    
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                                        if ($salesww['type'] == 'deposit') {
                                            $depositingAccounts++;
                                            $sumDeposits += $salesww['amount'];
                                        }
                                        
                                        if ($salesww['data_sales_type'] == 'bonus') $bonus += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'revenue') $revenue += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'withdrawal') $withdrawal += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'chargeback') $chargeback += $salesww['amount'];
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
                                                'amount'       => $salesww['amount'],
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
                                    
                              /*       
                                    $sql = "SELECT type,amount FROM data_stats "
                                            . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id='".$ww['id'] 
                                            . "' AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' "
                                            . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
                                            . ' AND profile_id = ' . $ww['ProfileId'];
                                    
                                    $statsqq = function_mysql_query($sql,__FILE__);
                                    
                                    while ($statsww = mysql_fetch_assoc($statsqq)) {
                                        $stats = 1;
                                    } */
                                    
                                    // $displayForex = 0;
                                    
                                    if (strtolower($merchantww['producttype']) == 'forex') {
                                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                                . 'WHERE affiliate_id = ' . $ww['id'] . ' AND profile_id = ' . $ww['ProfileId']
                                                . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
                                                . ' GROUP BY affiliate_id';
                                        
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                            $spreadAmount  = $ts['totalSpread'];
                                            $volume       += $ts['totalTO'];
                                            $pnl           = $ts['totalPnl'];
                                        }
                                        
                                        // $displayForex = 1;
										
															
						$totalLots  = 0;
						
						
							
							//lots 
							
							
						$sql = 'SELECT turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
										 . 'WHERE affiliate_id = ' . $ww['id'] . ' AND profile_id = ' . $ww['ProfileId']
                                         . ' and merchant_id = "' . $merchantww['id'] . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                                       . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
                                            
											
											
										 ;
											
											// die($sql);
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d');
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											
											if($ts['affiliate_id']==null) {
													continue;
											}
							
											// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
													$totalLots  = $ts['totalTurnOver'];
													// echo $totalLots
														$tradersProccessedForLots[$merchantww['id'] . '-' . $ts['trader_id']] = $merchantww['id'] . '-' . $ts['trader_id'];
														$lotdate = $ts['rdate'];
														$ex = explode(' ' , $lotdate);
														$lotdate = $ex[0];
															if ($earliestTimeForLot>$lotdate)
															$earliestTimeForLot = $lotdate;
														
														
														$row = [
																	'merchant_id'  => $merchantww['id'],
																	'affiliate_id' => $ts['affiliate_id'],
																	'rdate'        => $earliestTimeForLot,
																	'banner_id'    => $ts['banner_id'],
																	'trader_id'    => $ts['trader_id'],
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
														// var_dump($row);
														// die();
													$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
													// echo 'com: ' . $a .'<br>';
													$totalCom += $a;
											// }
										}
										
										
										
                                    }
                                    
                                    
                                    if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {
                                        if (
                                            (int) $stats <= 0 && (int) $totalLeads <= 0 && (int) $totalDemo <= 0 && 
										
									 (int) $totalCom <= 0 &&
									 (int) $totalReal <= 0 && (int) $ftd <= 0 && 
                                            (int) $totalTraffic['totalViews'] <= 0 && (int) $totalTraffic['totalClicks'] <= 0
                                        ) {
                                            continue;
                                        }
                                    }
                                    
                                    
                                    $sql = "SELECT id FROM payments_details WHERE status = 'canceled' AND affiliate_id = '" . $ww['id'] . "' ";
                                    $totalFruad = mysql_num_rows(function_mysql_query($sql,__FILE__));
                                    $totalFruadM += $totalFruad;
                                    
                                    if ($groupMerchantsPerAffiliate == 0) {
                                        $totalImpressions = $totalTraffic['totalViews'];
                                        $totalClicks = $totalTraffic['totalClicks'];
                                        $totalLeadsAccounts = $totalLeads;
                                        $totalDemoAccounts = $totalDemo;
                                        $totalRealAccounts = $totalReal;
                                        $totalFTD = $ftd;
                                        $totalDeposits = $depositingAccounts;
                                        $totalFTDAmount = $ftd_amount;
                                        $totalDepositAmount = $sumDeposits;
                                        $totalVolume = $volume;
                                        $totalBonus = $bonus;
                                        $totalWithdrawal = $withdrawal;
                                        $totalChargeBack = $chargeback;
                                        $totalNetRevenue = $netRevenue;
                                        $totalComs = $totalCom;
                                        $totalSpreadAmount = $spreadAmount;
                                        $totalTurnoverAmount = $turnoverAmount;
                                        $totalpnl = $pnl;
                                        $totalFrozens = $frozens;
                                        $totalRealFtd = $real_ftd;
                                        $totalRealFtdAmount = $real_ftd_amount;
                                        
                                    } else {
                                        $totalImpressions += $totalTraffic['totalViews'];
                                        $totalClicks += $totalTraffic['totalClicks'];
                                        $totalLeadsAccounts += $totalLeads;
                                        $totalDemoAccounts += $totalDemo;
                                        $totalRealAccounts += $totalReal;
                                        $totalFTD += $ftd;
                                        $totalDeposits += $depositingAccounts;
                                        $totalFTDAmount += $ftd_amount;
                                        $totalDepositAmount += $sumDeposits;
                                        $totalVolume += $volume;
                                        $totalBonus += $bonus;
                                        $totalWithdrawal += $withdrawal;
                                        $totalChargeBack += $chargeback;
                                        $totalNetRevenue += $netRevenue;
                                        $totalComs += $totalCom;
                                        $totalSpreadAmount += $spreadAmount;
                                        $totalTurnoverAmount += $turnoverAmount;
                                        $totalpnl += $pnl;
                                        $totalFrozens += $frozens;
                                        $totalRealFtd += $real_ftd;
                                        $totalRealFtdAmount += $real_ftd_amount;
                                    }
                                    
                                    $totalImpressionsM += $totalTraffic['totalViews'];
                                    $totalClicksM += $totalTraffic['totalClicks'];
                                    $totalLeadsAccountsM += $totalLeads;
                                    $totalDemoAccountsM += $totalDemo;
                                    $totalRealAccountsM += $totalReal;
                                    $totalFTDM += $ftd;
                                    $totalDepositsM += $depositingAccounts;
                                    $totalFTDAmountM += $ftd_amount;
                                    $totalDepositAmountM += $sumDeposits;
                                    $totalVolumeM += $volume;
                                    $totalBonusM += $bonus;
                                    $totalWithdrawalM += $withdrawal;
                                    $totalChargeBackM += $chargeback;
                                    $totalNetRevenueM += $netRevenue;
                                    $totalComsM += $totalCom;
                                    $totalSpreadAmountM += $spreadAmount;
                                    $totalTurnoverAmountM += $turnoverAmount;
                                    $totalFrozensM += $frozens;
                                    $totalpnlM += $pnl;
                                    $totalRealFtdM += $real_ftd;
                                    $totalRealFtdAmountM += $real_ftd_amount;
                                    
                                    $l++;        
                                    
                                // Mark given wallet as processed.
                                $arrWallets[$merchantww['wallet_id']] = true;
                            
                            
                            $listReport .= '<tr>';
                            
                            if ($groupMerchantsPerAffiliate == 0) {
                                if (isset($display_type) && !empty($display_type)) {
                                    $listReport .= '<td>' . $arrRange['from'] . ' - ' . $arrRange['to'] . '</td>';
                                }
                                
                                $listReport .= '
                                    <td>' . $ww['ProfileId'] . '</td>'
                                    .  '<td>' . $ww['ProfileName'] . '</td>'
                                    .  '<td>' . $ww['URL'] . '</td>
                                    <td>'.$ww['id'].'</td>
                                    <td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
                                    <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';
                                
                                if ($campID['title']) {
                                    if ($ww['campID']) {
                                        $listReport .= '<td align="left">' . $ww['campID'] . '</td>';
                                    } else {
                                        $listReport .= '<td align="left"></td>';
                                    }	
                                }
                                
                                $listReport .=  
                                    (!$set->isNetwork ? '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>' : '') . '
                                    <td>' . $merchantww['name'] . '</td>
                                    <td><a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>
                                    <td style="text-align: center;">' . @number_format($totalImpressions, 0) . '</td>
                                    <td style="text-align: center;">' . @number_format($totalClicks, 0) . '</td>
                                    <td style="text-align: center;">' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</td>
                                    <td style="text-align: center;">' . @number_format(($totalRealAccounts / $totalClicks) * 100, 2) . ' %</td>
                                    <td style="text-align: center;">' . @number_format(($totalFTD / $totalClicks) * 100, 2) . ' %</td>
                                    <td style="text-align: center;">' . @price($totalComs / $totalClicks) . '</td>' . 
                                    ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                    
                                    '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                    '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                    <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                    <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=transactions&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                    <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                    
                                    <td style="text-align: center;">' . price($totalBonus) . '</td>
                                    <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                    <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                    <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                    <td style="text-align: center;">' . price($totalVolume) . '</td>
                                    '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                    '.(($displayForex && $showPNL) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
                                    <td style="text-align: center;">' . price($totalComs) . '</td>
                                    <td style="text-align: center;">' . listGroups($ww['group_id'], 1) . '</td>
                                </tr>';
                            }
                        }
                        
                        
                        // Loop through affiliates, aggregate the per-merchant info.
                        if ($groupMerchantsPerAffiliate == 1) {
                            if (isset($display_type) && !empty($display_type)) {
                                $listReport .= '<td>' . $arrRange['from'] . ' - ' . $arrRange['to'] . '</td>';
                            }
                            
                            $listReport .= '
                                <td>' . $ww['ProfileId'] . '</td>'
                                .  '<td>' . $ww['ProfileName'] . '</td>'
                                .  '<td>' . $ww['URL'] . '</td>
                                <td>'.$ww['id'].'</td>
                                <td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
                                <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';
                            
                            if ($campID['title']) {
                                if ($ww['campID']) {
                                    $listReport .= '<td align="left">' . $ww['campID'] . '</td>';
                                } else {
                                    $listReport .= '<td align="left"></td>';
                                }	
                            }
                            
							
							
                            
                            $listReport .= 
                                (!$set->isNetwork ? '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>' : '') . '
                                <td><a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>
                                <td style="text-align: center;">' . @number_format($totalImpressions, 0) . '</td>
                                <td style="text-align: center;">' . @number_format($totalClicks, 0) . '</td>
                                <td style="text-align: center;">' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format(($totalRealAccounts / $totalClicks) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format(($totalFTD / $totalClicks) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @price($totalComs / $totalClicks) . '</td>' . 
                                ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                
                                '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=transactions&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                
                                <td style="text-align: center;">' . price($totalBonus) . '</td>
                                <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                <td style="text-align: center;">' . price($totalVolume) . '</td>
                                '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                '.(($displayForex && $showPNL) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                <td style="text-align: center;"><a href="/admin/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
                                <td style="text-align: center;">' . price($totalComs) . '</td>
                                <td style="text-align: center;">' . listGroups($ww['group_id'], 1) . '</td>
                            </tr>';
                        }
                        
                        $intAffiliatesCombinedCount++;
                    }
                    
                    unset($arrRange); // Clear up the memory.
                } // End of time-periods loop.
                
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable  = 1;
		$set->totalRows  = isset($groupByAff) && 1 ==$groupByAff ? $intAffiliatesCombinedCount : $l;
		$set->content   .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
		<div style="background: #F8F8F8;">
			<form action="" method="get">
			<input type="hidden" name="act" value="profile" />
			<input type="hidden" name="search" value="1" />
			<table><tr>
                            <td>'.lang('Period').'</td>
                            <td>'.lang('Affiliate ID').'</td>
                            <td>'.lang('Groups').'</td>
                            <td>'.lang('Merchant').'</td>
                            <td>'.lang('Show Affiliates').'</td>
                            <td>'.lang('Search Type').'</td>'
                            . ($intMerchantCount > 1 ? '<td>' . lang('Group Merchants') . '</td> ': '') .
                            '<td></td>
				</tr><tr>
				<td>'.timeFrame($from,$to).'</td>
				<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="alert_date" style="width: 100px;" /></td>
                                <td width="100"><select name="group_id" style="width: 100px;"><option value="">'.lang('All Groups').'</option><option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listGroups($group_id).'</select></td>
				<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
				<td><select name="showAllRecords" style="width: 150px;"><option id=1 value="1">'.lang('Only Active Affiliates').'</option><option id=0 value="0" '.(isset($showAllRecords) && $showAllRecords==0 ? ' selected ' : '' ).'>'.lang('All Affiliates').'</option></select></td>
				<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>'
				. ($intMerchantCount > 1 ? '<td><input type="checkbox" id="groupByAff" name="groupByAff" '.($groupMerchantsPerAffiliate==1 ? 'checked' : '').'/>
				<input type="hidden" id="groupByAffVal" value="'.($groupMerchantsPerAffiliate).'" name="groupByAff" /></td>' : '') .
				
				'<script type="text/javascript">
					$(document).ready(function(){
						$("#groupByAff").change(function(e){
							if($("#groupByAff").is(":checked")){
								$("#groupByAffVal").val("1");
							}else{
								$("#groupByAffVal").val("0");
							}
						});
					});
				</script>
				
				<td><input type="submit" value="'.lang('View').'" /></td>
			</tr></table>
			</form>
			
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 3000px;">'.lang('Affiliate Report').'</div>
			<div style="background: #F8F8F8;">';
			
				$tableStr='<table width="3000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>'
                                                . (isset($display_type) && !empty($display_type) ? '<th>' . lang('Period') . '</th>' : '') . '
                                                <th>'.lang('Profile ID').'</th>
						<th>'.lang(ptitle('Profile Name')).'</th>
						<th>'.lang(ptitle('Profile URL')).'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Username').'</th>
						<th>'.lang('Full Name').'</th>
						'.($campID['title'] ? '<th>'.lang($campID['title']).'</th>' : '') .
						(!$set->isNetwork ? '<th>'.lang('E-Mail').'</th>' : '') . 
						($groupMerchantsPerAffiliate==0 ? '<th>'.lang('Merchant').'</th>':'').'
						<th>'.lang('Website').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>' . 
                                                ($hideDemoAndLeads ? '' : '<th>'.lang(ptitle('Lead')).'</th>
                                                                           <th>'.lang(ptitle('Demo')).'</th>') . 
						
						'<th>'.lang(ptitle('Accounts')).'</th>
						'.($showCasinoFields ? '<th>'.lang(ptitle('Frozens')).'</th>' : '<th>|FROZEN|</th>').'
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang('Affiliate Risk').'</th>
						<th>'.lang(ptitle('Volume')).'</th>
						'.(($displayForex) ? '<th>'.lang('Spread Amount').'</th>' : '').'
						'.(($displayForex && $showPNL) ? '<th>'.lang('PNL').'</th>' : '').'
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Group').'</th>
					</tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
						<th><b>'.lang('Total').':</b></th>'
                                                . (isset($display_type) && !empty($display_type) ? '<th></th>' : '') . '
						<th></th>
						<th></th>
                                                <th></th>
						<th></th>
                                                <th></th>
						'.($campID['title'] ? '<th></th>' : '') .'
                                                <th></th>
						<th></th>'. ($groupMerchantsPerAffiliate == 0 ? '<th></th>' : '') . '
						<th>'.$totalImpressionsM.'</th>
						<th>'.$totalClicksM.'</th>
						<th>' . @number_format(($totalClicksM / $totalImpressionsM) * 100, 2) . ' %</th>
						<th>' . @number_format(($totalRealAccountsM / $totalClicksM) * 100, 2) . ' %</th>
						<th>' . @number_format(($totalFTDM / $totalClicksM) * 100, 2) . ' %</th>
						<th>' . @price($totalComsM / $totalClicksM) . '</th>' . 
                                                ($hideDemoAndLeads ? '' : '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">' . $totalLeadsAccountsM . '</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">' . $totalDemoAccountsM . '</a></th>') .
						
						'<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">' . $totalRealAccountsM . '</a></th>
						'.($showCasinoFields ? '<th>' . $totalFrozensM . '</th>' : '<th>|FROZEN|</th>').'
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTDM.'</a></th>
						
						<th>' . price($totalFTDAmountM) . '</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">' . $totalRealFtdM . '</a></th>
						
						<th>' . price($totalRealFtdAmountM) . '</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">' . $totalDepositsM . '</a></th>
						<th>' . price($totalDepositAmountM) . '</th>
						
						<th>' . price($totalBonusM) . '</th>
						<th>' . price($totalWithdrawalM) . '</th>
						<th>' . price($totalChargeBackM) . '</th>
						<th></th>
						<th>' . price($totalVolumeM) . '</th>
						' . (($displayForex) ? '<th>' . price($totalSpreadAmountM) . '</th>' : '') . '
						' . (($displayForex && $showPNL) ? '<th>' . price($totalpnlM) . '</th>' : '') . '
						<th>' . price($totalNetRevenueM) . '</th>
						<th>' . price($totalComsM) . '</th>
						<th></th>
                                            </tr>
                                        </tfoot>
					<tbody>
					' . $listReport . '
                                        </tbody>
				</table>';
                                
		if ($showCasinoFields) {
                    $tableStr = str_replace('<td>|FROZEN|</td>', '<td></td>', $tableStr);
                    $tableStr = str_replace('<th>|FROZEN|</th>', '<th></th>', $tableStr);
		} else {
                    $tableStr = str_replace('<td>|FROZEN|</td>', '', $tableStr);
                    $tableStr = str_replace('<th>|FROZEN|</th>', '', $tableStr);
		}
                
		$set->content .= $tableStr . '</div>' . getPager();
		excelExporter($tableStr, 'profile');
		theme();
            break;
                
    
case "country":
		$countryArray = [];
		$set->pageTitle = lang('Country Report');
                $where = ' 1 = 1 ';
            
                /**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
                }
                
		if ($profile_id) {
                    $where  .= " AND profile_id='".$profile_id."' ";
                }
              

		
		$merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
		foreach ($merchantsAr as $arrMerchant) {
						
				if (strtolower($arrMerchant['producttype'])=='forex')
						$displayForex = 1;
					$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}

		
		$l = 0;
		 $totalLeads=0;
		$totalDemo=0;
		$totalReal=0;
		$ftd=0;
		$ftd_amount=0;
		$real_ftd = 0;
		$real_ftd_amount = 0;
		$netRevenue = 0;
		$depositingAccounts=0;
		$sumDeposits=0;
		$bonus=0;
		$chargeback = 0;
		$cpaAmount=0;
		$withdrawal=0;
		$volume=0;
		$lots=0;
		$depositsAmount=0;
		$totalCom=0;
		
		//general merchants information
		$merchantww = getMerchants($merchant_id ? $merchant_id:0,1);
		
		$formula = $merchantww['rev_formula'];
		$merchantID = $merchantww['id'];
		$merchantName = strtolower($merchantww['name']);
		
		
		// clicks and impressions
		$where_main = $where;
		 $where_main =  str_replace('affiliate_id','t.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','t.merchant_id', $where_main) ;
		 $where_main =  str_replace('profile_id','t.profile_id', $where_main) ;
		 $sql = "select t.*, m.name as merchant_name from traffic t INNER JOIN merchants m on m.id = t.merchant_id where " . $where_main . " AND t.rdate BETWEEN '" . $from . "' AND '" . $to . "'
		 " . (isset($country_id) && !empty($country_id) ? ' AND t.country_id = "'.$country_id.'"' :'') ;
		 
	
		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
			$trafficRow['country_id'] = $trafficRow['country_id']=='' ? '-' : $trafficRow['country_id'];
			
					 if (!isset($countryArray[$trafficRow['country_id']]))  {
							$countryArray[$trafficRow['country_id']]['clicks'] = $trafficRow['clicks'];
							$countryArray[$trafficRow['country_id']]['views'] = $trafficRow['views'];
					}
					else{
							$countryArray[$trafficRow['country_id']]['clicks'] = $countryArray[$trafficRow['country_id']]['clicks'] + $trafficRow['clicks'];
							$countryArray[$trafficRow['country_id']]['views'] = $countryArray[$trafficRow['country_id']]['views'] + $trafficRow['views'];
					}
					$countryArray[$trafficRow['country_id']]['country'] = $trafficRow['country_id'];
					$countryArray[$trafficRow['country_id']]['type'] = $trafficRow['type'];
					$countryArray[$trafficRow['country_id']]['merchant'] = $trafficRow['merchant_name'];					
		}
		
		// registration (leads + demo + real)
		$where_reg = $where;
		$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
		 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
		 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
		
				   $sql = "SELECT dg.*,m.name as merchant_name FROM data_reg dg"
								." INNER JOIN merchants m on m.id = dg.merchant_id "
                         . "WHERE " . $where . " AND dg.rdate BETWEEN '" . $from . "' AND '" . $to . "'
						 " . (isset($country_id) && !empty($country_id) ? ' AND dg.country = "'.$country_id.'"' :'');
					
					$regqq = function_mysql_query($sql,__FILE__);
					
					$arrTierCplCountCommissionParams = [];
						
					while ($regww = mysql_fetch_assoc($regqq)) {
						$regww['country'] = $regww['country']=='' ? '-' : $regww['country'];
						
						$countryArray[$regww['country']]['country'] = $regww['country'];
						$countryArray[$regww['country']]['type'] = $regww['type'];
					    $countryArray[$regww['country']]['merchant'] = $regww['merchant_name'];				
						
						$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
						$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
						if ($regww['type'] == "lead"){
								//$totalLeads++; 
								$countryArray[$regww['country']]['leads'] += 1 ;
							}
						if ($regww['type'] == "demo"){
								// $totalDemo++;
								$countryArray[$regww['country']]['demo'] += 1;
						}
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
								
								$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
								$countryArray[$regww['country']]['totalCom'] += $totalCom;
							} else {
								// TIER CPL.
								if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
								} else {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
										'from'                => $from,
										'to'                  => $to,
										'onlyRevShare'        => 0,
										'groupId'             => (is_null($group_id ? -1 : $group_id)),
										'arrDealTypeDefaults' => $arrDealTypeDefaults,
										'country'=> $regww['country'],
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
							
							unset($arrTmp);
							// $totalReal++;
							$countryArray[$regww['country']]['real'] += 1;
						}
				   }
				  
					/* echo "<pre>";
					print_r($countryArray);
					echo "</pre>";
					die; */
					
				     // TIER CPL.
                    foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
                        $totalCom = getCommission(
                            $arrParams['from'], 
                            $arrParams['to'], 
                            $arrParams['onlyRevShare'], 
                            $arrParams['groupId'], 
                            $arrParams['arrDealTypeDefaults'], 
                            $arrParams['arrTmp']
                        );
                      $countryArray[$arrParams['country']]['totalCom'] += $totalCom;
					    unset($intAffId, $arrParams);
                    }
					
			
					
					//FTDs
					$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id),0,0,0,0,((isset($country_id) && !empty($country_id)?$country_id:'')));
					
                    foreach ($arrFtds as $arrFtd) {
				
				
						// if($arrFtd['country'] == "" || $arrFtd['country'] == 'Any' || $arrFtd['country'] == 0)
								// continue;
						$arrFtd['country'] = $arrFtd['country']=='' ? '-' : $arrFtd['country'];
						
						
						$real_ftd++;
						$countryArray[$arrFtd['country']]['real_ftd'] += 1;
                        
						$real_ftd_amount = $arrFtd['amount'];
                        $countryArray[$arrFtd['country']]['real_ftd_amount'] += $arrFtd['amount'];
                        
						$beforeNewFTD = $ftd;
                        getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
                      
                        if ($beforeNewFTD != $ftd) {
							$ftd_amount = $real_ftd_amount;
                            $arrFtd['isFTD'] = true;
                            $totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
							$countryArray[$arrFtd['country']]['totalCom'] += $totalCom;
							$countryArray[$arrFtd['country']]['ftd'] +=1;
							$countryArray[$arrFtd['country']]['ftd_amount'] += $ftd_amount;
                        }
						unset($arrFtd);
                    }
			
					
					//SALES
					$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 ." INNER JOIN merchants_creative mc on mc.id= tb1.banner_id "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
					 . "WHERE  mc.valid=1 and tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($country_id) && !empty($country_id) ? ' AND data_reg.country = "'.$country_id.'"' :'') ;
				
                    $salesqq = function_mysql_query($sql,__FILE__);
                    
                    while ($salesww = mysql_fetch_assoc($salesqq)) {
					
						$salesww['country']=='' ? '-' : $salesww['country'];
						
                        //if ($salesww['type'] == 1 || $salesww['type'] == 'deposit') { // OLD.
						if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
                            $depositingAccounts++;
							$countryArray[$salesww['country']]['depositingAccounts'] += 1;
							
                            $sumDeposits = $salesww['amount'];
							$countryArray[$salesww['country']]['sumDeposits'] += $salesww['amount'];
							
							// $depositsAmount+=$salesww['amount'];
                        }
                        
                        if ($salesww['data_sales_type'] == "bonus") {
								$bonus = $salesww['amount'];
								$countryArray[$salesww['country']]['bonus'] += $salesww['amount'];
						}
                        if ($salesww['data_sales_type'] == "withdrawal"){ 
								$withdrawal = $salesww['amount'];
								$countryArray[$salesww['country']]['withdrawal'] += $salesww['amount'];
						}
                        if ($salesww['data_sales_type'] == "chargeback"){
								$chargeback = $salesww['amount'];
								$countryArray[$salesww['country']]['chargeback'] += $salesww['amount'];
						}
                        if ($salesww['data_sales_type'] == 'volume') {
                            $volume = $salesww['amount'];
							
							$countryArray[$salesww['country']]['volume'] += $volume;
                            $arrTmp = [
                                'merchant_id'  => $salesww['merchant_id'],
                                'affiliate_id' => $salesww['affiliate_id'],
                                'rdate'        => $salesww['rdate'],
                                'banner_id'    => $salesww['banner_id'],
                                'trader_id'    => $salesww['trader_id'],
                                'profile_id'   => $salesww['profile_id'],
                                'type'       => 'volume',
                                'amount'       => $salesww['amount'],
                            ];
                            
                            $totalCom = getCommission(
                                $from, 
                                $to, 
                                0, 
                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                $arrDealTypeDefaults, 
                                $arrTmp
                            );

							$countryArray[$salesww['country']]['totalCom'] += $totalCom;
                        }
						
					
			
								
							//REVENUE   						// loop on merchants    								// loop on affiliates
								// start of data_stats (revenue) loop
								$merchantww = 	getMerchants($salesww['merchant_id'],0);
								if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {
									
								
									// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
									$withd = $salesww['data_sales_type'] == "withdrawal"?$withdrawal:0;
									$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$withd,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
									
									//$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
									$countryArray[$salesww['country']]['netRevenue'] += $netRevenue;
									
									
									
										$row                 = array();
										$row['merchant_id']  = $merchantww['id'];
										$row['affiliate_id'] = $salesww['affiliate_id'];
										$row['banner_id']    = 0;
										$row['rdate']        = $salesww['rdate'];
										$row['amount']       = ($netRevenue);
										$row['isFTD']        = false;
									  
									
									  // $totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									  $totalCom           = getCommission($salesww['rdate'],$salesww['rdate'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									
										if ($withd<>0) {
										// echo '<br>'. ($totalCom).'<br>';
										// var_dump($salesww);
										}
								
									  
									
										// echo ($totalCom.'<br>');
									  
									  // die();
									  
									$countryArray[$salesww['country']]['totalCom'] += $totalCom;
									  
								}
							// end of data_stats (revenue) loop
					
					
						// end of data_sales loop
                    }
					
						
						$sql ="SELECT DISTINCT  ds.affiliate_id, ds.merchant_id,m.producttype as producttype, dg.country as country FROM data_stats ds INNER JOIN data_reg dg ON dg.trader_id = ds.trader_id INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
													 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" . (isset($country_id) && !empty($country_id) ? ' AND dg.country = "'.$country_id.'"' :'');
													 
						$revqq  = function_mysql_query($sql,__FILE__); 					 
				
						while ($revww = mysql_fetch_assoc($revqq)) {
									
									$revww['country'] = $revww['country']=='' ? '-' : $revww['country'];
									
									
									$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
									$intTotalRevenue  = 0;
									
									foreach ($arrRevenueRanges as $arrRange2) {
										$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
													 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
													 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
										
										$intCurrentRevenue = getRevenue($strRevWhere, $revww['producttype']);
										
										$intTotalRevenue    += $intCurrentRevenue;
										$row                 = array();
										$row['merchant_id']  = $revww['merchant_id'];
										$row['affiliate_id'] = $revww['affiliate_id'];
										$row['banner_id']    = 0;
										$row['rdate']        = $arrRange2['from'];
										$row['amount']       = $intCurrentRevenue;
										$row['isFTD']        = false;
									  
									  $totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
										
										$countryArray[$revww['country']]['totalCom'] += $totalCom;
										
										unset($arrRange2, $strRevWhere);
									}
									
									$netRevenue = $intTotalRevenue;
									$countryArray[$revww['country']]['netRevenue'] += $netRevenue;
									
						}
					
					
					$sql = "select * from merchants where producttype = 'Forex' and valid =1";
					$totalqq = function_mysql_query($sql,__FILE__);
					
					while ($merchantww  = mysql_fetch_assoc($totalqq)) {
						
						
						
                        $sql = 'SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO, dg.country as country FROM data_stats ds '
								. ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
                                . 'WHERE ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
								. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
								. " and ds.merchant_id = " . $merchantww['id']
								 . (isset($country_id) && !empty($country_id) ? ' AND dg.country = "'.$country_id.'"' :'');
                        
                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                        
                        while($ts = mysql_fetch_assoc($traderStatsQ)){
						
                                $spreadAmount = $ts['totalSpread'];
								$countryArray[$ts['country']]['totalSpread'] += $ts['totalSpread'];
                                $volume = $ts['totalTO'];
								$ts['country'] = $ts['country']=='' ? '-' : $ts['country'];
								
								$countryArray[$ts['country']]['volume'] += $ts['totalTO'];
								
                                $pnl = $ts['totalPnl'];
								$countryArray[$ts['country']]['pnl'] += $ts['totalPnl'];
                        }
						
						
	$totalLots  = 0;
											
							
							
						$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id,dg.country as country FROM data_stats ds '
                                         . ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
										 . 'WHERE  ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
                                            . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
											. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
											. " and ds.merchant_id = " . $merchantww['id']
												 . (isset($country_id) && !empty($country_id) ? ' AND dg.country = "'.$country_id.'"' :'');
											// die($sql);
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d');
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											if($ts['affiliate_id']==null) {
													continue;
											}
											$ts['country'] = $ts['country']=='' ? '-' : $ts['country'];
							
											// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
													$totalLots  = $ts['totalTurnOver'];
													// echo $totalLots
														$tradersProccessedForLots[$merchantww['id'] . '-' . $ts['trader_id']] = $trafficRow['id'] . '-' . $ts['trader_id'];
														$lotdate = $ts['rdate'];
														$ex = explode(' ' , $lotdate);
														$lotdate = $ex[0];
															if ($earliestTimeForLot>$lotdate)
															$earliestTimeForLot = $lotdate;
														$row = [
																	'merchant_id'  => $merchantww['id'],
																	'affiliate_id' => $ts['affiliate_id'],
																	'rdate'        => $earliestTimeForLot,
																	'banner_id'    => $ts['banner_id'],
																	'trader_id'    => $ts['trader_id'],
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
													$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
													// echo 'com: ' . $a .'<br>';
													$totalCom = $a;
													$countryArray[$ts['country']]['totalCom'] += $totalCom;
											// }
										}
				     }
					
				/*  echo '<pre>';
				var_dump($countryArray);
				echo '</pre>';
				die();  */
		
			
					//DISPLAY Report
					foreach($countryArray as $data){
						if ($data['views']>0 || $data['clicks']>0 || $data['leads'] >0 || $data['demo'] >0 || $data['real'] >0 
						 || $data['depositingAccounts'] >0 
						 || $data['real_ftd'] >0 
						 || $data['ftd'] >0 
						 || $data['ftd_amount'] >0 
						 || $data['real_ftd_amount'] >0 
						 || $data['chargeback'] >0 
						 || $data['withdrawal'] >0 
						 || $data['bonus'] >0 
						 || $data['totalCom'] >0 
						 || $data['netRevenue'] >0 
						 || $data['volume'] >0 
						){
										$listReport .= '
								<tr>
									<td style="text-align: left;" title="'.$data['country'].'">'.$allCountriesArray[$data['country']].'</td>
								
								
									<td>'.@number_format($data['views'],0).'</td>
									<td>'.@number_format($data['clicks'],0).'</td>
									<td>'.@number_format(($data['clicks']/$data['views'])*100,2).' %</td>
									<td>'.@number_format(($data['real']/$data['clicks'])*100,2).' %</td>
									<td>'.@number_format(($data['ftd']/$data['clicks'])*100,2).' %</td>
									<td>'.@price($data['totalCom']/$data['clicks']).'</td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=lead&country_id='.$data['country'].'">'.$data['leads'].'</a></td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=demo&country_id='.$data['country'].'">'.$data['demo'].'</a></td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=real&country_id='.$data['country'].'">'.$data['real'].'</a></td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=ftd&country_id='.$data['country'].'">'.$data['ftd'].'</a></td>
									<td>'.price($data['ftd_amount']).'</td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'type=totalftd&country_id='.$data['country'].'">'.$data['real_ftd'].'</a></td>
									<td>'.price($data['real_ftd_amount']).'</td>
									<td><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=deposit&country_id='.$data['country'].'">'.$data['depositingAccounts'].'</a></td>
									<td>'.price($data['sumDeposits']).'</td>
									<td style="text-align: center;">'.price($data['volume']).'</td>
									<td>'.price($data['bonus']).'</td>
									<td>'.price($data['withdrawal']).'</td>
									<td>'.price($data['chargeback']).'</td>
									<td style="text-align: center;">'.price($data['netRevenue']).'</td>
									<td>'.price($data['totalCom']).'</td>
								</tr>';
								
								$totalImpressions += $data['views'];
								$totalClicks += $data['clicks'];
								$totalLeadsAccounts += $data['leads'];
								$totalDemoAccounts += $data['demo'];
								$totalRealAccounts += $data['real'];
								$totalFTD += $data['ftd'];
								$totalDeposits += $data['depositingAccounts'];
								$totalFTDAmount += $data['ftd_amount'];
								$totalDepositAmount += $data['sumDeposits'];
								$totalVolume += $data['volume'];
								$totalBonusAmount += $data['bonus'];
								$totalWithdrawalAmount += $data['withdrawal'];
								$totalChargeBackAmount += $data['chargeback'];
								$totalNetRevenue += $data['netRevenue'];
								$totalComs += $data['totalCom'];
								$totalRealFtd += $data['real_ftd'];
								$totalRealFtdAmount += $data['real_ftd_amount'];
								
								$l++;
							// echo $ftd_amount.'<br>';
							$ftd_amount = $real_ftd_amount = 0;
							// $totalLeads = $totalDemo = $totalReal = $ftd = $depositingAccounts = $ftd_amount = $sumDeposits = $volume = $bonus = $withdrawal = $chargeback= $netRevenue= $totalCom= $real_ftd= $real_ftd_amount = 0;
						}
				} 
		
		
		        if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->totalRows  = $l;
		$set->sortTable  = 1;
		$set->content   .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="country" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Country').'</td>
					<td>'.lang('Affiliate ID').'</td>
					
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td>
					
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" style="width: 2400px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2400" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						
						<th style="text-align: left;">'.lang('Country').'</th>
						
						
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
					
						<th>'.$totalViews.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalViews)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr, 'Country');
		$set->content.=$tableStr.'</div>'.getPager();
		theme();
		break;
	

case "creative":
		$set->pageTitle = lang('Creative Report');
		
		$creativeArray = [];
		
                $where = ' 1 = 1 ';
            
                /**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		if ($banner_id) {
                    $where .= " AND banner_id='".$banner_id."' ";
                }
				
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
                }
                
		if ($profile_id) {
                    $where  .= " AND profile_id='".$profile_id."' ";
                }
              

		
		$merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
		foreach ($merchantsAr as $arrMerchant) {
						
				if (strtolower($arrMerchant['producttype'])=='forex')
						$displayForex = 1;
					$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}

		//general merchants information
		$merchantww = getMerchants($merchant_id ? $merchant_id:0,1);
		
		$formula = $merchantww['rev_formula'];
		$merchantID = $merchantww['id'];
		$merchantName = strtolower($merchantww['name']);
		
		//Banners
		 $where_main = $where;
		 $where_main =  str_replace('affiliate_id','mc.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','mc.merchant_id', $where_main) ;
		 $where_main =  str_replace('profile_id','mc.profile_id', $where_main) ;
		$sql = "SELECT mc.*,m.name as merchant_name,l.title as language FROM merchants_creative mc left join languages l on l.id = mc.language_id INNER JOIN merchants m on mc.merchant_id = m.id where " 
		. str_replace('banner_id=','mc.id=', $where) . " and mc.valid=1 ";
		
	// die($sql);	
		$bannersqq = function_mysql_query($sql,__FILE__);
		while ($bannersww = mysql_fetch_assoc($bannersqq)) {		
					
					if ($type && $bannersww['type'] != $type) {
                        continue;
                    }
					
					$creativeArray[$bannersww['id']]['banner_id'] = $bannersww['id'];
				    $creativeArray[$bannersww['id']]['banner_title'] = $bannersww['title'];
					$creativeArray[$bannersww['id']]['type'] = $bannersww['type'];
					$creativeArray[$bannersww['id']]['merchant'] = $bannersww['merchant_name'];
					$creativeArray[$bannersww['id']]['language'] = $bannersww['language'];
					$creativeArray[$bannersww['id']]['width'] = $bannersww['width'];
					$creativeArray[$bannersww['id']]['height'] = $bannersww['height'];
					$creativeArray[$bannersww['id']]['merchant_id'] = $bannersww['merchant_id'];
		}
	
		// clicks and impressions
		$sql = "select * from traffic where " . $where . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "'";
		
		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
					if(!isset($creativeArray[$trafficRow['banner_id']])){
						continue;
					}
					 if (!isset($creativeArray[$trafficRow['banner_id']]))  {
							$creativeArray[$trafficRow['banner_id']]['clicks'] = $trafficRow['clicks'];
							$creativeArray[$trafficRow['banner_id']]['views'] = $trafficRow['views'];
					}
					else{
							$creativeArray[$trafficRow['banner_id']]['clicks'] = $creativeArray[$trafficRow['banner_id']]['clicks'] + $trafficRow['clicks'];
							$creativeArray[$trafficRow['banner_id']]['views'] = $creativeArray[$trafficRow['banner_id']]['views'] + $trafficRow['views'];
					}
		}		
		
		
		$l = 0;
		$totalLeads=0;
		$totalDemo=0;
		$totalReal=0;
		$ftd=0;
		$ftd_amount=0;
		$real_ftd = 0;
		$real_ftd_amount = 0;
		$netRevenue = 0;
		$depositingAccounts=0;
		$sumDeposits=0;
		$bonus=0;
		$chargeback = 0;
		$cpaAmount=0;
		$withdrawal=0;
		$volume=0;
		$lots=0;
		$depositsAmount=0;
		$totalCom=0;
		
		
		// registration (leads + demo + real)
		$where_reg = $where;
		$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
		 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
		 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
		 
		$sql = "SELECT dg.*,m.name as merchant_name FROM data_reg dg"
					." INNER JOIN merchants m on m.id = dg.merchant_id "
					."WHERE " . $where . " AND dg.rdate BETWEEN '" . $from . "' AND '" . $to . "'" 
					. (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
		
		$regqq = function_mysql_query($sql,__FILE__);
		
		$arrTierCplCountCommissionParams = [];
			// die ($sql);
		while ($regww = mysql_fetch_assoc($regqq)) {
			if($regww['banner_id'] == ""  || $regww['banner_id'] == 0 || !isset($creativeArray[$regww['banner_id']])){
				continue;
			}
			
			$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
			$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
			
			if ($regww['type'] == "lead"){
				//$totalLeads++;
					$creativeArray[$regww['banner_id']]['leads'] += 1;
			}
			if ($regww['type'] == "demo"){
					$creativeArray[$regww['banner_id']]['demo'] += 1;
			} 
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
					
					$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
					
					// var_dump($totalCom);
					// die();
					
					$creativeArray[$regww['banner_id']]['totalCom'] += $totalCom;
					 
					
				} else {
					// TIER CPL.
					if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
						$arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
					} else {
						$arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
							'from'                => $from,
							'to'                  => $to,
							'onlyRevShare'        => 0,
							'groupId'             => (is_null($group_id ? -1 : $group_id)),
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
				
				unset($arrTmp);
				//$totalReal++;
				$creativeArray[$regww['banner_id']]['real'] += 1;
			}
		}
		
			
		// TIER CPL.
		foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
			$totalCom = getCommission(
				$arrParams['from'], 
				$arrParams['to'], 
				$arrParams['onlyRevShare'], 
				$arrParams['groupId'], 
				$arrParams['arrDealTypeDefaults'], 
				$arrParams['arrTmp']
			);
			$creativeArray[$arrParams['arrTmp']['banner_id']]['totalCom'] += $totalCom;
			unset($intAffId, $arrParams);
		}
		
		
		
		$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id));
		// var_dump($arrFtds);
		// die();
		

		foreach ($arrFtds as $arrFtd) {
				
								
					
				$isFilteredBanner_id=true;
				 if (!empty($banner_id) && isset($banner_id)){
					
				
						 if ($banner_id==$arrFtd['banner_id']) {
							 $isFilteredBanner_id = true;
							 
						 }
						 else {
							 $isFilteredBanner_id = false;
						 }
						
				}
				 
				if  ($arrFtd['banner_id'] == ""  || $arrFtd['banner_id'] == 0 || !isset($creativeArray[$arrFtd['banner_id']]) ) {
				$isFilteredBanner_id = false;
				}
				 
				if(!$isFilteredBanner_id ){
					continue;
				}
		


		// $real_ftd++;
				$creativeArray[$arrFtd['banner_id']]['real_ftd'] += 1;
			
				$real_ftd_amount = $arrFtd['amount'];
				$creativeArray[$arrFtd['banner_id']]['real_ftd_amount'] += $real_ftd_amount;
				
				$beforeNewFTD = $ftd;
				getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
			
				if ($beforeNewFTD != $ftd) {
				// die ('gergerge');	
					$ftd_amount = $real_ftd_amount;
					$arrFtd['isFTD'] = true;
					$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
					
					$creativeArray[$arrFtd['banner_id']]['totalCom'] += $totalCom;
				$creativeArray[$arrFtd['banner_id']]['ftd'] += 1;
				$creativeArray[$arrFtd['banner_id']]['ftd_amount'] += $ftd_amount;
				
				
		// var_dump($creativeArray);
		// die();
				}
				unset($arrFtd);
		}
	
		
		//Sales
		$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
					 . "WHERE  tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($banner_id) && !empty($banner_id) ? ' AND tb1.banner_id = "'.$banner_id.'"' :'');

		$salesqq = function_mysql_query($sql,__FILE__);
	
	    while ($salesww = mysql_fetch_assoc($salesqq)) {
				
				/* if($salesww['banner_id'] == ""  || $salesww['banner_id'] == 0){					
					continue;
				} */
				
				$isFilteredBanner_id=true;
				 if (!empty($banner_id) && isset($banner_id)){
					
				
						 if ($banner_id==$salesww['banner_id']) {
							 $isFilteredBanner_id = true;
							 
						 }
						 else {
							 $isFilteredBanner_id = false;
						 }
						
				}
				 
				if  ($salesww['banner_id'] == ""  || $salesww['banner_id'] == 0 || !isset($creativeArray[$salesww['banner_id']]) ) {
				$isFilteredBanner_id = false;
				}
				 
				if(!$isFilteredBanner_id ){
					continue;
				}
				
				if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
					//$depositingAccounts++;
					$creativeArray[$salesww['banner_id']]['depositingAccounts'] += 1;
					
					$sumDeposits = $salesww['amount'];
					$creativeArray[$salesww['banner_id']]['sumDeposits'] += $salesww['amount'];
					
					// $depositsAmount+=$salesww['amount'];
				}
				
				if ($salesww['data_sales_type'] == "bonus") {
						$bonus = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['bonus'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == "withdrawal"){ 
						$withdrawal = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['withdrawal'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == "chargeback"){
						$chargeback = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['chargeback'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == 'volume') {
					$volume = $salesww['amount'];
					$creativeArray[$salesww['banner_id']]['volume'] += $salesww['amount'];
					
					$arrTmp = [
						'merchant_id'  => $salesww['merchant_id'],
						'affiliate_id' => $salesww['affiliate_id'],
						'rdate'        => $salesww['rdate'],
						'banner_id'    => $salesww['banner_id'],
						'trader_id'    => $salesww['trader_id'],
						'profile_id'   => $salesww['profile_id'],
						'type'       => 'volume',
						'amount'       => $salesww['amount'],
					];
					
					$totalCom = getCommission(
						$from, 
						$to, 
						0, 
						(isset($group_id) && $group_id != '' ? $group_id : -1), 
						$arrDealTypeDefaults, 
						$arrTmp
					);

					$creativeArray[$salesww['banner_id']]['totalCom'] += $totalCom;
				}
				
				
				//REVENUE   						// loop on merchants    								// loop on affiliates
				// start of data_stats (revenue) loop
			$merchantww = 	getMerchants($salesww['merchant_id'],0);
				
					if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {

						// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
				
						$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$salesww['data_sales_type'] == "withdrawal"?$withdrawal:0,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
					//	$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$salesww['amount'],$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
						 // echo  (':: ' . $netRevenue);
						$creativeArray[$salesww['banner_id']]['netRevenue'] += $netRevenue;
						if($netRevenue <> 0 ){
						$row                 = array();
						$row['merchant_id']  = $merchantww['id'];
						$row['affiliate_id'] = $salesww['affiliate_id'];
						$row['banner_id']    = 0;
						$row['rdate']        = $from;
						$row['amount']       = $netRevenue;
						$row['isFTD']        = false;
									  
						$totalCom           = getCommission($from, $to, 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									  
						$creativeArray[$salesww['banner_id']]['totalCom'] += $totalCom;
						}
				}
				// end of data_stats (revenue) loop

				// end of data_sales loop
		}
		
	
		$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype, dg.country as country FROM data_stats ds INNER JOIN data_reg dg ON dg.trader_id = ds.trader_id INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
					 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" . (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
		
		$revqq  = function_mysql_query($sql,__FILE__); 					 
		while ($revww = mysql_fetch_assoc($revqq)) {
					$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
					$intTotalRevenue  = 0;
					
					foreach ($arrRevenueRanges as $arrRange2) {
						$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
									 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
									 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
						
						$intCurrentRevenue = getRevenue($strRevWhere, $revww['producttype']);
						
						$intTotalRevenue   += $intCurrentRevenue;
						$row                 = array();
						$row['merchant_id']  = $revww['merchant_id'];
						$row['affiliate_id'] = $revww['affiliate_id'];
						$row['banner_id']    = 0;
						$row['rdate']        = $arrRange2['from'];
						$row['amount']       = $intCurrentRevenue;
						$row['isFTD']        = false;
					  
						$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
						
						$creativeArray[$revww['banner_id']]['totalCom'] += $totalCom;
						
						unset($arrRange2, $strRevWhere);
					}
					
					$netRevenue = $intTotalRevenue;
					$creativeArray[$revww['banner_id']]['netRevenue'] = $netRevenue;
					
		}
					
		
					
		$sql = "select * from merchants where producttype = 'Forex' and valid =1";
		$totalqq = function_mysql_query($sql,__FILE__);
		while ($merchantww  = mysql_fetch_assoc($totalqq)) {
				$sql = 'SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO, dg.banner_id as banner_id FROM data_stats ds '
						. ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
						. 'WHERE ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
						. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
						. " and ds.merchant_id = " . $merchantww['id']
						. (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'')
						. " group by dg.banner_id";

				$traderStatsQ = function_mysql_query($sql,__FILE__);
				
				while($ts = mysql_fetch_assoc($traderStatsQ)){
					
						$isFilteredBanner_id=true;
						 if (!empty($banner_id) && isset($banner_id)){
							
						
								 if ($banner_id==$ts['banner_id']) {
									 $isFilteredBanner_id = true;
									 
								 }
								 else {
									 $isFilteredBanner_id = false;
								 }
								
						}
						 
						if  ($ts['banner_id'] == ""  || $ts['banner_id'] == 0 || !isset($creativeArray[$ts['banner_id']]) ) {
						$isFilteredBanner_id = false;
						}
						 
						if(!$isFilteredBanner_id ){
							continue;
						}
			
						$spreadAmount = $ts['totalSpread'];
						$volume += $ts['totalTO'];
						
						$creativeArray[$ts['banner_id']]['volume'] += $ts['totalTO'];
						
						$pnl = $ts['totalPnl'];
				}
						
				$totalLots  = 0;
											
							
							
				$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id,dg.country as country FROM data_stats ds '
				 . ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
				 . 'WHERE  ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
					. (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
					. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
					. " and ds.merchant_id = " . $merchantww['id']
						 . (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
				
   
				$traderStatsQ = function_mysql_query($sql,__FILE__);
				$earliestTimeForLot = date('Y-m-d');
				while($toww = mysql_fetch_assoc($traderStatsQ)){
					
					if($toww['affiliate_id']==null) {
							continue;
					}
	
					// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
							$totalLots  = $toww['totalTurnOver'];
							// echo $totalLots
								$tradersProccessedForLots[$merchantww['id'] . '-' . $toww['trader_id']] = $trafficRow['id'] . '-' . $toww['trader_id'];
								$lotdate = $toww['rdate'];
								$ex = explode(' ' , $lotdate);
								$lotdate = $ex[0];
									if ($earliestTimeForLot>$lotdate)
									$earliestTimeForLot = $lotdate;
								
								$row = [
											'merchant_id'  => $merchantww['id'],
											'affiliate_id' => $toww['affiliate_id'],
											'rdate'        => $earliestTimeForLot,
											'banner_id'    => $toww['banner_id'],
											'trader_id'    => $toww['trader_id'],
											'profile_id'   => $toww['profile_id'],
											'type'       => 'lots',
											'amount'       =>  $totalLots,
								];
							$a = getCommission($from, $toww, 0, $group_id, $arrDealTypeDefaults, $row);
							// echo 'com: ' . $a .'<br>';
							$totalCom = $a;
							$creativeArray[$toww['banner_id']]['totalCom'] += $totalCom;
					// }
				}
		 }		
		// die;		
		
		foreach($creativeArray as $data){
			
			if ($data['views']>0 || $data['clicks']>0 || $data['leads'] >0 || $data['demo'] >0 || $data['real'] >0 
			 || $data['depositingAccounts'] >0 
			 || $data['real_ftd'] >0 
			 || $data['ftd'] >0 
			 || $data['ftd_amount'] >0 
			 || $data['real_ftd_amount'] >0 
			 || $data['chargeback'] >0 
			 || $data['withdrawal'] >0 
			 || $data['bonus'] >0 
			 || $data['totalCom'] >0 
			 || $data['netRevenue'] >0 
			 || $data['volume'] >0 
		){
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$data['banner_id'].'</td>
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="NewWin(\'/admin/creative.php?act=edit_banner&id='.$data['banner_id'].'\',\'editbanner_'.$data['banner_id'].'\',\'1000\',\'800\',\'1\');">'.lang('Edit').'</a></td>
				<td style="text-align: left;">'.($data['banner_id'] ? $data['banner_title'] : lang('BANNER REMOVED')).'</td>
				<td style="text-align: left;">'.$data['language'].'</td>
				<td style="text-align: left;">'.($data['width']>0 ? $data['width'] : "").'</td>
				<td style="text-align: left;">'.($data['height']>0 ? $data['height'] : "").'</td>
				<td style="text-align: left;">'.ucwords($data['type']).'</td>
				<td style="text-align: left;">'.$data['merchant'].'</td>
				<td>'.@number_format($data['views'],0).'</td>
				<td>'.@number_format($data['clicks'],0).'</td>
				<td>'.@number_format(($data['clicks']/$data['views'])*100,2).' %</td>
				<td>'.@number_format(($data['real']/$data['clicks'])*100,2).' %</td>
				<td>'.@number_format(($data['ftd']/$data['clicks'])*100,2).' %</td>
				<td>'.@price($data['totalCom']/$data['clicks']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=lead">'.$data['leads'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=demo">'.$data['demo'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=real">'.$data['real'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=ftd">'.$data['ftd'].'</a></td>
				<td>'.price($data['ftd_amount']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=totalftd">'.$data['real_ftd'].'</a></td>
				<td>'.price($data['real_ftd_amount']).'</td>
				<td><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>
				<td>'.price($data['sumDeposits']).'</td>
				<td style="text-align: center;">'.price($data['volume']).'</td>
				<td>'.price($data['bonus']).'</td>
				<td>'.price($data['withdrawal']).'</td>
				<td>'.price($data['chargeback']).'</td>
				<td style="text-align: center;">'.price($data['netRevenue']).'</td>
				<td>'.price($data['totalCom']).'</td>
			</tr>';
			
			$totalImpressions += $data['views'];
			$totalClicks += $data['clicks'];
			$totalLeadsAccounts += $data['leads'];
			$totalDemoAccounts += $data['demo'];
			$totalRealAccounts += $data['real'];
			$totalFTD += $data['ftd'];
			$totalDeposits += $data['depositingAccounts'];
			$totalFTDAmount += $data['ftd_amount'];
			$totalDepositAmount += $data['sumDeposits'];
			$totalVolume += $data['volume'];
			$totalBonusAmount += $data['bonus'];
			$totalWithdrawalAmount += $data['withdrawal'];
			$totalChargeBackAmount += $data['chargeback'];
			$totalNetRevenue += $data['netRevenue'];
			$totalComs += $data['totalCom'];
			$totalRealFtd += $data['real_ftd'];
			$totalRealFtdAmount += $data['real_ftd_amount'];
                        $l++;
		// echo $ftd_amount.'<br>';
		$ftd_amount = $real_ftd_amount = 0;
		// $totalLeads = $totalDemo = $totalReal = $ftd = $depositingAccounts = $ftd_amount = $sumDeposits = $volume = $bonus = $withdrawal = $chargeback= $netRevenue= $totalCom= $real_ftd= $real_ftd_amount = 0;
						
		}
		}
                
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->totalRows  = $l;
		$set->sortTable  = 1;
		$set->content   .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="creative" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Banner ID').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td>'.lang('Creative Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" /></td>
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td>
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
						<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>
						<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>
						<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
						<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>
						<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
						<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>
						<option value="coupon" '.($type == "coupon" ? 'selected' : '').'>'.lang('Coupon').'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" style="width: 2400px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2400" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Actions').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th style="text-align: left;">'.lang('Language').'</th>
						<th style="text-align: left;">'.lang('Width').'</th>
						<th style="text-align: left;">'.lang('Height').'</th>
						<th>'.lang('Type').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr, 'Creative');
		$set->content.=$tableStr.'</div>'.getPager();
		theme();
		break;	
		
		
		
		
		
		
case "LandingPage":
		$set->pageTitle = lang('Landing Pages Report');
		
		$creativeArray = [];
		
                $where = ' 1 = 1 ';
            
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		/* if ($url) {
                    $where .= " AND url like '%".$url."%' ";
                } */
				
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
                }
                
		if ($profile_id) {
                    $where  .= " AND profile_id='".$profile_id."' ";
                }
              

		
		$merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
		foreach ($merchantsAr as $arrMerchant) {
						
				if (strtolower($arrMerchant['producttype'])=='forex')
						$displayForex = 1;
					$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}

		//general merchants information
		$merchantww = getMerchants($merchant_id ? $merchant_id:0,1);
		
		$formula = $merchantww['rev_formula'];
		$merchantID = $merchantww['id'];
		$merchantName = strtolower($merchantww['name']);
		
		//Banners
		 $where_main = $where;
		 $where_main =  str_replace('affiliate_id','mc.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','mc.merchant_id', $where_main) ;
		 $where_main =  str_replace('profile_id','mc.profile_id', $where_main) ;
		$sql = "SELECT mc.*,m.name as merchant_name,l.title as language FROM merchants_creative mc left join languages l on l.id = mc.language_id INNER JOIN merchants m on mc.merchant_id = m.id where " 
		. str_replace('banner_id=','mc.id=', $where) . " and mc.valid=1 ";
		
	// die($sql);	
		$bannersqq = function_mysql_query($sql,__FILE__);
		while ($bannersww = mysql_fetch_assoc($bannersqq)) {		
					
					if ($type && $bannersww['type'] != $type) {
                        continue;
                    }
					// var_dump($bannersww);
					// die();
					$creativeArray[$bannersww['id']]['banner_id'] = $bannersww['id'];
					$creativeArray[$bannersww['id']]['url'] = $bannersww['url'];
				    $creativeArray[$bannersww['id']]['banner_title'] = $bannersww['title'];
					$creativeArray[$bannersww['id']]['type'] = $bannersww['type'];
					$creativeArray[$bannersww['id']]['merchant'] = $bannersww['merchant_name'];
					$creativeArray[$bannersww['id']]['language'] = $bannersww['language'];
					$creativeArray[$bannersww['id']]['width'] = $bannersww['width'];
					$creativeArray[$bannersww['id']]['height'] = $bannersww['height'];
					$creativeArray[$bannersww['id']]['merchant_id'] = $bannersww['merchant_id'];
		}
	
		// clicks and impressions
		$sql = "select * from traffic where " . $where . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "'";
		
		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
					if(!isset($creativeArray[$trafficRow['banner_id']])){
						continue;
					}
					 if (!isset($creativeArray[$trafficRow['banner_id']]))  {
							$creativeArray[$trafficRow['banner_id']]['clicks'] = $trafficRow['clicks'];
							$creativeArray[$trafficRow['banner_id']]['views'] = $trafficRow['views'];
					}
					else{
							$creativeArray[$trafficRow['banner_id']]['clicks'] = $creativeArray[$trafficRow['banner_id']]['clicks'] + $trafficRow['clicks'];
							$creativeArray[$trafficRow['banner_id']]['views'] = $creativeArray[$trafficRow['banner_id']]['views'] + $trafficRow['views'];
					}
		}		
		
		
		$l = 0;
		$totalLeads=0;
		$totalDemo=0;
		$totalReal=0;
		$ftd=0;
		$ftd_amount=0;
		$real_ftd = 0;
		$real_ftd_amount = 0;
		$netRevenue = 0;
		$depositingAccounts=0;
		$sumDeposits=0;
		$bonus=0;
		$chargeback = 0;
		$cpaAmount=0;
		$withdrawal=0;
		$volume=0;
		$lots=0;
		$depositsAmount=0;
		$totalCom=0;
		
		
		// registration (leads + demo + real)
		$where_reg = $where;
		$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
		 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
		 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
		 
		$sql = "SELECT dg.*,m.name as merchant_name FROM data_reg dg"
					." INNER JOIN merchants m on m.id = dg.merchant_id "
					."WHERE " . $where . " AND dg.rdate BETWEEN '" . $from . "' AND '" . $to . "'" 
					. (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
		
		$regqq = function_mysql_query($sql,__FILE__);
		
		$arrTierCplCountCommissionParams = [];
			// die ($sql);
		while ($regww = mysql_fetch_assoc($regqq)) {
			if($regww['banner_id'] == ""  || $regww['banner_id'] == 0 || !isset($creativeArray[$regww['banner_id']])){
				continue;
			}
			
			$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
			$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
			
			if ($regww['type'] == "lead"){
				//$totalLeads++;
					$creativeArray[$regww['banner_id']]['leads'] += 1;
			}
			if ($regww['type'] == "demo"){
					$creativeArray[$regww['banner_id']]['demo'] += 1;
			} 
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
					
					$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
					
					// var_dump($totalCom);
					// die();
					
					$creativeArray[$regww['banner_id']]['totalCom'] += $totalCom;
					 
					
				} else {
					// TIER CPL.
					if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
						$arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
					} else {
						$arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
							'from'                => $from,
							'to'                  => $to,
							'onlyRevShare'        => 0,
							'groupId'             => (is_null($group_id ? -1 : $group_id)),
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
				
				unset($arrTmp);
				//$totalReal++;
				$creativeArray[$regww['banner_id']]['real'] += 1;
			}
		}
		
			
		// TIER CPL.
		foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
			$totalCom = getCommission(
				$arrParams['from'], 
				$arrParams['to'], 
				$arrParams['onlyRevShare'], 
				$arrParams['groupId'], 
				$arrParams['arrDealTypeDefaults'], 
				$arrParams['arrTmp']
			);
			$creativeArray[$arrParams['arrTmp']['banner_id']]['totalCom'] += $totalCom;
			unset($intAffId, $arrParams);
		}
		
		
		
		$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id));
		// var_dump($arrFtds);
		// die();
		

		foreach ($arrFtds as $arrFtd) {
				
								
					
				$isFilteredBanner_id=true;
				 if (!empty($banner_id) && isset($banner_id)){
					
				
						 if ($banner_id==$arrFtd['banner_id']) {
							 $isFilteredBanner_id = true;
							 
						 }
						 else {
							 $isFilteredBanner_id = false;
						 }
						
				}
				 
				if  ($arrFtd['banner_id'] == ""  || $arrFtd['banner_id'] == 0 || !isset($creativeArray[$arrFtd['banner_id']]) ) {
				$isFilteredBanner_id = false;
				}
				 
				if(!$isFilteredBanner_id ){
					continue;
				}
		


		// $real_ftd++;
				$creativeArray[$arrFtd['banner_id']]['real_ftd'] += 1;
			
				$real_ftd_amount = $arrFtd['amount'];
				$creativeArray[$arrFtd['banner_id']]['real_ftd_amount'] += $real_ftd_amount;
				
				$beforeNewFTD = $ftd;
				getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
			
				if ($beforeNewFTD != $ftd) {
				// die ('gergerge');	
					$ftd_amount = $real_ftd_amount;
					$arrFtd['isFTD'] = true;
					$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
					
					$creativeArray[$arrFtd['banner_id']]['totalCom'] += $totalCom;
				$creativeArray[$arrFtd['banner_id']]['ftd'] += 1;
				$creativeArray[$arrFtd['banner_id']]['ftd_amount'] += $ftd_amount;
				
				
		// var_dump($creativeArray);
		// die();
				}
				unset($arrFtd);
		}
	
		
		//Sales
		$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 ." INNER JOIN merchants_creative mc on mc.id= tb1.banner_id "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
					 . "WHERE  mc.valid=1 and tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($banner_id) && !empty($banner_id) ? ' AND tb1.banner_id = "'.$banner_id.'"' :'');
							
		$salesqq = function_mysql_query($sql,__FILE__);
	
	    while ($salesww = mysql_fetch_assoc($salesqq)) {
				
				/* if($salesww['banner_id'] == ""  || $salesww['banner_id'] == 0){					
					continue;
				} */
				
				$isFilteredBanner_id=true;
				 if (!empty($banner_id) && isset($banner_id)){
					
				
						 if ($banner_id==$salesww['banner_id']) {
							 $isFilteredBanner_id = true;
							 
						 }
						 else {
							 $isFilteredBanner_id = false;
						 }
						
				}
				 
				if  ($salesww['banner_id'] == ""  || $salesww['banner_id'] == 0 || !isset($creativeArray[$salesww['banner_id']]) ) {
				$isFilteredBanner_id = false;
				}
				 
				if(!$isFilteredBanner_id ){
					continue;
				}
				
				if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
					//$depositingAccounts++;
					$creativeArray[$salesww['banner_id']]['depositingAccounts'] += 1;
					
					$sumDeposits = $salesww['amount'];
					$creativeArray[$salesww['banner_id']]['sumDeposits'] += $salesww['amount'];
					
					// $depositsAmount+=$salesww['amount'];
				}
				
				if ($salesww['data_sales_type'] == "bonus") {
						$bonus = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['bonus'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == "withdrawal"){ 
						$withdrawal = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['withdrawal'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == "chargeback"){
						$chargeback = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['chargeback'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == 'volume') {
					$volume = $salesww['amount'];
					$creativeArray[$salesww['banner_id']]['volume'] += $salesww['amount'];
					
					$arrTmp = [
						'merchant_id'  => $salesww['merchant_id'],
						'affiliate_id' => $salesww['affiliate_id'],
						'rdate'        => $salesww['rdate'],
						'banner_id'    => $salesww['banner_id'],
						'trader_id'    => $salesww['trader_id'],
						'profile_id'   => $salesww['profile_id'],
						'type'       => 'volume',
						'amount'       => $salesww['amount'],
					];
					
					$totalCom = getCommission(
						$from, 
						$to, 
						0, 
						(isset($group_id) && $group_id != '' ? $group_id : -1), 
						$arrDealTypeDefaults, 
						$arrTmp
					);

					$creativeArray[$salesww['banner_id']]['totalCom'] += $totalCom;
				}
				
				
				//REVENUE   						// loop on merchants    								// loop on affiliates
				// start of data_stats (revenue) loop
			$merchantww = 	getMerchants($salesww['merchant_id'],0);
				
					if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {

						// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
				
						$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$salesww['data_sales_type'] == "withdrawal"?$withdrawal:0,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
					//	$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$salesww['amount'],$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
						 // echo  (':: ' . $netRevenue);
						$creativeArray[$salesww['banner_id']]['netRevenue'] += $netRevenue;
						if($netRevenue <> 0 ){
							$row                 = array();
							$row['merchant_id']  = $merchantww['id'];
							$row['affiliate_id'] = $salesww['affiliate_id'];
							$row['banner_id']    = 0;
							$row['rdate']        = $arrRange2['from'];
							$row['amount']       = $netRevenue;
							$row['isFTD']        = false;
							  
							$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
							$creativeArray[$salesww['banner_id']]['totalCom'] += $totalCom;
						}
				}
				// end of data_stats (revenue) loop

				// end of data_sales loop
		}
		
	
	
		$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype, dg.country as country FROM data_stats ds INNER JOIN data_reg dg ON dg.trader_id = ds.trader_id INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
					 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" . (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
		
		$revqq  = function_mysql_query($sql,__FILE__); 					 
		while ($revww = mysql_fetch_assoc($revqq)) {
					$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
					$intTotalRevenue  = 0;
					
					foreach ($arrRevenueRanges as $arrRange2) {
						$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
									 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
									 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
						
						$intCurrentRevenue = getRevenue($strRevWhere, $revww['producttype']);
						
						$intTotalRevenue   += $intCurrentRevenue;
						$row                 = array();
						$row['merchant_id']  = $revww['merchant_id'];
						$row['affiliate_id'] = $revww['affiliate_id'];
						$row['banner_id']    = 0;
						$row['rdate']        = $arrRange2['from'];
						$row['amount']       = $intCurrentRevenue;
						$row['isFTD']        = false;
					  
						$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
						
						$creativeArray[$revww['banner_id']]['totalCom'] += $totalCom;
						
						unset($arrRange2, $strRevWhere);
					}
					
					$netRevenue = $intTotalRevenue;
					$creativeArray[$revww['banner_id']]['netRevenue'] = $netRevenue;
					
		}
					
		
					
		$sql = "select * from merchants where producttype = 'Forex' and valid =1";
		$totalqq = function_mysql_query($sql,__FILE__);
		while ($merchantww  = mysql_fetch_assoc($totalqq)) {
				$sql = 'SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO, dg.banner_id as banner_id FROM data_stats ds '
						. ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
						. 'WHERE ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
						. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
						. " and ds.merchant_id = " . $merchantww['id']
						. (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'')
						. " group by dg.banner_id";

				$traderStatsQ = function_mysql_query($sql,__FILE__);
				
				while($ts = mysql_fetch_assoc($traderStatsQ)){
					
						$isFilteredBanner_id=true;
						 if (!empty($banner_id) && isset($banner_id)){
							
						
								 if ($banner_id==$ts['banner_id']) {
									 $isFilteredBanner_id = true;
									 
								 }
								 else {
									 $isFilteredBanner_id = false;
								 }
								
						}
						 
						if  ($ts['banner_id'] == ""  || $ts['banner_id'] == 0 || !isset($creativeArray[$ts['banner_id']]) ) {
						$isFilteredBanner_id = false;
						}
						 
						if(!$isFilteredBanner_id ){
							continue;
						}
			
						$spreadAmount = $ts['totalSpread'];
						$volume += $ts['totalTO'];
						
						$creativeArray[$ts['banner_id']]['volume'] += $ts['totalTO'];
						
						$pnl = $ts['totalPnl'];
				}
						
				$totalLots  = 0;
											
							
							
				$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id,dg.country as country FROM data_stats ds '
				 . ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
				 . 'WHERE  ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
					. (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
					. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
					. " and ds.merchant_id = " . $merchantww['id']
						 . (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
				
   
				$traderStatsQ = function_mysql_query($sql,__FILE__);
				$earliestTimeForLot = date('Y-m-d');
				while($toww = mysql_fetch_assoc($traderStatsQ)){
					
					if($toww['affiliate_id']==null) {
							continue;
					}
	
					// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
							$totalLots  = $toww['totalTurnOver'];
							// echo $totalLots
								$tradersProccessedForLots[$merchantww['id'] . '-' . $toww['trader_id']] = $trafficRow['id'] . '-' . $toww['trader_id'];
								$lotdate = $toww['rdate'];
								$ex = explode(' ' , $lotdate);
								$lotdate = $ex[0];
									if ($earliestTimeForLot>$lotdate)
									$earliestTimeForLot = $lotdate;
								$row = [
											'merchant_id'  => $merchantww['id'],
											'affiliate_id' => $toww['affiliate_id'],
											'rdate'        => $earliestTimeForLot,
											'banner_id'    => $toww['banner_id'],
											'trader_id'    => $toww['trader_id'],
											'profile_id'   => $toww['profile_id'],
											'type'       => 'lots',
											'amount'       =>  $totalLots,
								];
							$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
							// echo 'com: ' . $a .'<br>';
							$totalCom = $a;
							$creativeArray[$to['banner_id']]['totalCom'] += $totalCom;
					// }
				}
		 }	
		
		
		$newUrlArray = array();		 
			foreach($creativeArray as $data){
					if (empty($newUrlArray[$data['url']])) {
						
						$newUrlArray[$data['url']]['url'] = $data['url'];
						$newUrlArray[$data['url']]['clicks'] = 0;

						$newUrlArray[$data['url']]['views'] = 0;
						$newUrlArray[$data['url']]['leads'] = 0;
						$newUrlArray[$data['url']]['demo'] = 0;
						$newUrlArray[$data['url']]['real'] = 0;
						$newUrlArray[$data['url']]['depositingAccounts'] = 0;
						$newUrlArray[$data['url']]['real_ftd'] =0;
						$newUrlArray[$data['url']]['ftd'] =0;
						$newUrlArray[$data['url']]['real_ftd_amount'] =0;
						$newUrlArray[$data['url']]['withdrawal'] =0;
						$newUrlArray[$data['url']]['bonus'] =0;
						$newUrlArray[$data['url']]['totalCom'] =0;
						$newUrlArray[$data['url']]['netRevenue'] =0;
						$newUrlArray[$data['url']]['volume'] =0;
						$newUrlArray[$data['url']]['sumDeposits'] =0;
						
						
					}
					
						$newUrlArray[$data['url']]['clicks'] += $data['clicks'];
						$newUrlArray[$data['url']]['views'] += $data['views'];
						$newUrlArray[$data['url']]['leads'] += $data['leads'];
						$newUrlArray[$data['url']]['demo'] += $data['demo'];
						$newUrlArray[$data['url']]['real'] += $data['real'];
						$newUrlArray[$data['url']]['depositingAccounts'] += $data['depositingAccounts'];
						$newUrlArray[$data['url']]['real_ftd'] += $data['real_ftd'];
						$newUrlArray[$data['url']]['ftd'] += $data['ftd'];
						$newUrlArray[$data['url']]['real_ftd_amount'] += $data['real_ftd_amount'];
						$newUrlArray[$data['url']]['withdrawal'] += $data['withdrawal'];
						$newUrlArray[$data['url']]['bonus'] += $data['bonus'];
						$newUrlArray[$data['url']]['totalCom'] += $data['totalCom'];
						$newUrlArray[$data['url']]['netRevenue'] += $data['netRevenue'];
						$newUrlArray[$data['url']]['volume'] += $data['volume'];
						$newUrlArray[$data['url']]['sumDeposits'] += $data['sumDeposits'];
				
			}
		

		 
		
		foreach($newUrlArray as $data){
			
			if ($data['views']>0 || $data['clicks']>0 || $data['leads'] >0 || $data['demo'] >0 || $data['real'] >0 
			 || $data['depositingAccounts'] >0 
			 || $data['real_ftd'] >0 
			 || $data['ftd'] >0 
			 || $data['ftd_amount'] >0 
			 || $data['real_ftd_amount'] >0 
			 || $data['chargeback'] >0 
			 || $data['withdrawal'] >0 
			 || $data['bonus'] >0 
			 || $data['totalCom'] >0 
			 || $data['netRevenue'] >0 
			 || $data['volume'] >0 
		){
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$data['url'].'</td>
				<td style="text-align: left;">'.$data['merchant'].'</td>
				<td>'.@number_format($data['views'],0).'</td>
				<td>'.@number_format($data['clicks'],0).'</td>
				<td>'.@number_format(($data['clicks']/$data['views'])*100,2).' %</td>
				<td>'.@number_format(($data['real']/$data['clicks'])*100,2).' %</td>
				<td>'.@number_format(($data['ftd']/$data['clicks'])*100,2).' %</td>
				<td>'.@price($data['totalCom']/$data['clicks']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=lead">'.$data['leads'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=demo">'.$data['demo'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=real">'.$data['real'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=ftd">'.$data['ftd'].'</a></td>
				<td>'.price($data['ftd_amount']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=totalftd">'.$data['real_ftd'].'</a></td>
				<td>'.price($data['real_ftd_amount']).'</td>
				<td><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>
				<td>'.price($data['sumDeposits']).'</td>
				<td style="text-align: center;">'.price($data['volume']).'</td>
				<td>'.price($data['bonus']).'</td>
				<td>'.price($data['withdrawal']).'</td>
				<td>'.price($data['chargeback']).'</td>
				<td style="text-align: center;">'.price($data['netRevenue']).'</td>
				<td>'.price($data['totalCom']).'</td>
			</tr>';
			
			$totalImpressions += $data['views'];
			$totalClicks += $data['clicks'];
			$totalLeadsAccounts += $data['leads'];
			$totalDemoAccounts += $data['demo'];
			$totalRealAccounts += $data['real'];
			$totalFTD += $data['ftd'];
			$totalDeposits += $data['depositingAccounts'];
			$totalFTDAmount += $data['ftd_amount'];
			$totalDepositAmount += $data['sumDeposits'];
			$totalVolume += $data['volume'];
			$totalBonusAmount += $data['bonus'];
			$totalWithdrawalAmount += $data['withdrawal'];
			$totalChargeBackAmount += $data['chargeback'];
			$totalNetRevenue += $data['netRevenue'];
			$totalComs += $data['totalCom'];
			$totalRealFtd += $data['real_ftd'];
			$totalRealFtdAmount += $data['real_ftd_amount'];
                        $l++;
		// echo $ftd_amount.'<br>';
		$ftd_amount = $real_ftd_amount = 0;
		// $totalLeads = $totalDemo = $totalReal = $ftd = $depositingAccounts = $ftd_amount = $sumDeposits = $volume = $bonus = $withdrawal = $chargeback= $netRevenue= $totalCom= $real_ftd= $real_ftd_amount = 0;
						
		}
		}
                
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->totalRows  = $l;
		$set->sortTable  = 1;
		$set->content   .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="LandingPage" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('URL').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td>'.lang('Creative Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<!--td><input type="text" name="url" value="'.$URL.'" /></td-->
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td>
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
						<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>
						<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>
						<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
						<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>
						<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
						<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>
						<option value="coupon" '.($type == "coupon" ? 'selected' : '').'>'.lang('Coupon').'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" style="width: 2400px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2400" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th style="text-align: left;">'.lang('URL').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>

						<th></th>
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr, 'LandingPage');
		$set->content.=$tableStr.'</div>'.getPager();
		theme();
		break;	
		
		
		
		case "clicks":
		$set->pageTitle = lang('Clicks Report');
		
		$page = (isset($page) || !empty($page))?$page:1;
		$set->page = $page;
		
		$start_limit = $page==1?0:$set->rowsNumberAfterSearch * ($page -1);
		$end_limit = $set->rowsNumberAfterSearch * $page;
		
		
		$clickArray = [];
		
                $where = ' 1 = 1 ';
            
                /**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		if ($banner_id) {
                    $where .= " AND banner_id='".$banner_id."' ";
                }
				
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
                }
                
		if ($profile_id) {
                    $where  .= " AND profile_id='".$profile_id."' ";
                }
              

		
		$merchantsArray = array();
		$displayForex = 0;
		$merchantsAr = getMerchants(0,1);
		
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
		foreach ($merchantsAr as $arrMerchant) {
						
				if (strtolower($arrMerchant['producttype'])=='forex')
						$displayForex = 1;
					$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}

		//general merchants information
		$merchantww = getMerchants($merchant_id ? $merchant_id:0,1);
		
		$formula = $merchantww['rev_formula'];
		$merchantID = $merchantww['id'];
		$merchantName = strtolower($merchantww['name']);
		
		$where_main = $where;
		$where_main =  str_replace('affiliate_id','t.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','t.merchant_id', $where_main) ;
		 $where_main =  str_replace('profile_id','t.profile_id', $where_main) ;
		 $where_main =  str_replace('banner_id','t.banner_id', $where_main) ;
		 
		/* $sql = "SELECT count(*) as total_records FROM traffic  "
					. " WHERE " . $where .
					(!empty($unique_id) ? ' and uid = ' . $unique_id :'')
					." and rdate BETWEEN '".$from."' AND '".$to. "' and uid !=0 ";
					
		$totalRec = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		
		$total_records = $totalRec['total_records'];
		$set->total_records = $total_records;
					 */
		$sql = "SELECT t.*,lang.title as language, m.name as merchant_name,af.username as affiliate_username from traffic t"
					. " INNER JOIN merchants m on m.id = t.merchant_id "
					. " INNER JOIN affiliates af on af.id = t.affiliate_id "
					. " LEFT JOIN languages lang on lang.id = t.language_id" 
					. " WHERE " . $where_main 
					. " AND t.uid != 0 "
					. (!empty($unique_id) ? ' and t.uid = ' . $unique_id :'')
					." and t.rdate BETWEEN '".$from."' AND '".$to. "' ORDER BY id DESC limit " . $start_limit. ", " . $end_limit;
		
	/*  $sql = "SELECT t.*from traffic t "
					. "WHERE  " . $where_main . " and t.rdate BETWEEN '".$from."' AND '".$to. "'";*/
			
		$clickqq = function_mysql_query($sql,__FILE__);
		while($clickww = mysql_fetch_assoc($clickqq)){
			if($clickww['uid'] !=0){
				$clickArray[$clickww['id']]['traffic_id'] = $clickww['id'];
				 $clickArray[$clickww['id']]['uid'] = $clickww['uid'];
				 $clickArray[$clickww['id']]['clicks'] = $clickww['clicks'];
				 $clickArray[$clickww['id']]['views'] = $clickww['views'];
				 $clickArray[$clickww['id']]['traffic_date'] = $clickww['rdate'];
				
				 $clickArray[$clickww['id']]['type'] = $clickww['type'];
				 
				 $clickArray[$clickww['id']]['banner_id'] = $clickww['banner_id'];
				 $clickArray[$clickww['id']]['profile_id'] = $clickww['profile_id'];
				 $clickArray[$clickww['id']]['param'] = $clickww['param'];
				 $clickArray[$clickww['id']]['param2'] = $clickww['param2'];
				 $clickArray[$clickww['id']]['refer_url'] = $clickww['refer_url'];
				 $clickArray[$clickww['id']]['language'] = $clickww['language'];
				 $clickArray[$clickww['id']]['country'] = $clickww['country_id'];
				 $clickArray[$clickww['id']]['ip'] = $clickww['ip'];
				 $clickArray[$clickww['id']]['merchant_name'] = $clickww['merchant_name'];
				 $clickArray[$clickww['id']]['affiliate_username'] = $clickww['affiliate_username'];
				 $clickArray[$clickww['id']]['affiliate_id'] = $clickww['affiliate_id'];
				 
				 $clickArray[$clickww['id']]['platform'] = $clickww['platform'];
				 $clickArray[$clickww['id']]['os'] = $clickww['os'];
				 $clickArray[$clickww['id']]['osVersion'] = $clickww['osVersion'];
				 
				 $clickArray[$clickww['id']]['browser'] = $clickww['browser'];
				 $clickArray[$clickww['id']]['browserVersion'] = $clickww['broswerVersion'];
				
				$l = 0;
				$totalLeads=0;
				$totalDemo=0;
				$totalReal=0;
				$ftd=0;
				$ftd_amount=0;
				$real_ftd = 0;
				$real_ftd_amount = 0;
				$netRevenue = 0;
				$depositingAccounts=0;
				$sumDeposits=0;
				$bonus=0;
				$chargeback = 0;
				$cpaAmount=0;
				$withdrawal=0;
				$volume=0;
				$lots=0;
				$depositsAmount=0;
				$totalCom=0;
				 if(!empty($clickArray)){           
				   // registration (leads + demo + real)
				$where_reg = $where;
				$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
				 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
				 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
				  $where_reg =  str_replace('banner_id','dg.banner_id', $where_reg) ;
				  
				$sql = "SELECT dg.* FROM data_reg dg"
							." WHERE " . $where_reg 
							. " AND dg.uid = " . $clickww['uid']
							. " AND dg.uid!=0";
				
				$regqq = function_mysql_query($sql,__FILE__);
				
				$arrTierCplCountCommissionParams = [];
					// die ($sql);
				$regArray = array();
				while ($regww = mysql_fetch_assoc($regqq)) {
					
					
					if(!empty($regww['trader_id'])){
						$tranrow['id'] = $regww['id'];
						$tranrow['affiliate_id'] = $regww['affiliate_id'];
						$tranrow['trader_id'] = $regww['trader_id'];
						$tranrow['merchant_id'] = $regww['merchant_id'];
						$regArray[] = array($tranrow);

						$clickArray[$clickww['id']]['reg_date'] = $regww['reg_date'];
						$clickArray[$clickww['id']]['trader_id'] = $regww['trader_id'];
						$clickArray[$clickww['id']]['trader_name'] = $regww['trader_alias'];
						
						$clickArray[$clickww['id']]['sale_status'] = $regww['saleStatus'];
						
						$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
						$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
						
						if ($regww['type'] == "lead"){
							//$totalLeads++;
								$clickArray[$clickww['id']]['leads'] += 1;
						}
						if ($regww['type'] == "demo"){
								$clickArray[$clickww['id']]['demo'] += 1;
						} 
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
								
								$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
								$clickArray[$clickww['id']]['total_com'] += $totalCom;
								
							} else {
								// TIER CPL.
								if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
								} else {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
										'from'                => $from,
										'to'                  => $to,
										'onlyRevShare'        => 0,
										'groupId'             => (is_null($group_id ? -1 : $group_id)),
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
							
							unset($arrTmp);
							//$totalReal++;
							$clickArray[$clickww['id']]['real'] += 1;
						}
					}
				}
				 
				 
				 if(!isset($clickArray[$clickww['id']]['trader_id'])){
					// TIER CPL.
					foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
						$totalCom = getCommission(
							$arrParams['from'], 
							$arrParams['to'], 
							$arrParams['onlyRevShare'], 
							$arrParams['groupId'], 
							$arrParams['arrDealTypeDefaults'], 
							$arrParams['arrTmp']
						);
						$clickArray[$clickww['id']]['totalCom'] += 1;
						unset($intAffId, $arrParams);
					}
				 }
			
					foreach($regArray as $key=>$params){
						$trader_id = $params[0]['trader_id'];
						if(!is_null($trader_id)){
							$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id),0,0,0,$trader_id);
							foreach ($arrFtds as $arrFtd) {
									$real_ftd++;
									$clickArray[$clickww['id']]['real_ftd'] += 1;
									
									$real_ftd_amount = $arrFtd['amount'];
									$clickArray[$clickww['id']]['real_ftd_amount'] += $real_ftd_amount;
									
									$beforeNewFTD = $ftd;
									getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
								
									if ($beforeNewFTD != $ftd) {
										
										$arrFtd['isFTD'] = true;
										$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
										
										$clickArray[$clickww['id']]['totalCom'] += $totalCom;
									}
									$clickArray[$clickww['id']]['ftd'] = $ftd;
											
									$clickArray[$clickww['id']]['ftd_amount'] = $ftd_amount;
									unset($arrFtd);
							
							}
					
			
				
				
				
					//Sales
					$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
								 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
								 . "WHERE  tb1.trader_id = " .  $trader_id
								 . ' data_reg.uid!=0 '
							//	 . ' and tb1.rdate between "' . $from . '" AND "' . $to . '"' 
								. (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
								 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
								 . (isset($banner_id) && !empty($banner_id) ? ' AND data_reg.banner_id = "'.$banner_id.'"' :'') 
								 .(!empty($unique_id) ? ' and data_reg.uid = ' . $unique_id :'');
					
					$salesqq = function_mysql_query($sql,__FILE__);
								
					while ($salesww = mysql_fetch_assoc($salesqq)) {
							
							if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
								$depositingAccounts++;
								$clickArray[$clickww['id']]['depositingAccounts'] += 1;
								
								$sumDeposits = $salesww['amount'];
								$clickArray[$clickww['id']]['sumDeposits'] += $salesww['amount'];
								
								// $depositsAmount+=$salesww['amount'];
							}
							
							if ($salesww['data_sales_type'] == "bonus") {
									$bonus = $salesww['amount'];
									$clickArray[$clickww['id']]['bonus'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == "withdrawal"){ 
									$withdrawal = $salesww['amount'];
									$clickArray[$clickww['id']]['withdrawal'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == "chargeback"){
									$chargeback = $salesww['amount'];
									$clickArray[$clickww['id']]['chargeback'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == 'volume') {
								$volume = $salesww['amount'];
								$clickArray[$clickww['id']]['volume'] += $salesww['amount'];
								$arrTmp = [
									'merchant_id'  => $salesww['merchant_id'],
									'affiliate_id' => $salesww['affiliate_id'],
									'rdate'        => $salesww['rdate'],
									'banner_id'    => $salesww['banner_id'],
									'trader_id'    => $salesww['trader_id'],
									'profile_id'   => $salesww['profile_id'],
									'type'       => 'volume',
									'amount'       => $salesww['amount'],
								];
								
								$totalCom = getCommission(
									$from, 
									$to, 
									0, 
									(isset($group_id) && $group_id != '' ? $group_id : -1), 
									$arrDealTypeDefaults, 
									$arrTmp
								);

								$clickArray[$clickww['id']]['totalCom'] += $totalCom;
							}
						
						
							//REVENUE   						// loop on merchants    								// loop on affiliates
							// start of data_stats (revenue) loop
							$merchantww = 	getMerchants($salesww['merchant_id'],0);
							
							if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {
								
								// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
								$netRevenue =  round(getRevenue("data_sales.rdate  BETWEEN . '".$from."' AND '".$to."' ",$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$salesww['data_sales_type'] == "withdrawal"?$withdrawal:0,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
								//$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
								//echo $salesww['id'] . "----" . $netRevenue . "<br/>";
									
								$clickArray[$clickww['id']]['netRevenue'] += $netRevenue;
								
								if($netRevenue<>0){
									$row                 = array();
									$row['merchant_id']  = $merchantww['id'];
									$row['affiliate_id'] = $salesww['affiliate_id'];
									$row['banner_id']    = 0;
									$row['rdate']        = $from;
									$row['amount']       = $netRevenue;
									$row['isFTD']        = false;
									$row['trader_id']        = $trader_id;
									  
									$totalCom           = getCommission($from, $to, 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									$clickArray[$clickww['id']]['totalCom'] += $totalCom;
								}
							} 
							// end of data_stats (revenue) loop
						
							// end of data_sales loop
					}
					
					$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype FROM data_stats ds  "
								. " INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
								 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" 
								 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
								 . " and ds.trader_id=" . $trader_id;
								 
					$revqq  = function_mysql_query($sql,__FILE__); 					 
					while ($revww = mysql_fetch_assoc($revqq)) {
								$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
								$intTotalRevenue  = 0;
								
								foreach ($arrRevenueRanges as $arrRange2) {
									$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
												 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
												 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
									
									$intCurrentRevenue = getRevenue($strRevWhere, $revww['producttype']);
									$intTotalRevenue    += $intCurrentRevenue;
									$row                 = array();
									$row['merchant_id']  = $revww['merchant_id'];
									$row['affiliate_id'] = $revww['affiliate_id'];
									$row['banner_id']    = 0;
									$row['rdate']        = $arrRange2['from'];
									$row['amount']       = $intCurrentRevenue;
									$row['isFTD']        = false;
								  
									$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									
									$clickArray[$clickww['id']]['totalCom'] += $totalCom;
									
									unset($arrRange2, $strRevWhere);
								}
								
								$netRevenue = $intTotalRevenue;
								$clickArray[$clickww['id']]['netRevenue'] += $netRevenue;
								
								
					}
					
						
					$sql = "select * from merchants where producttype = 'Forex' and valid =1";
					$totalqq = function_mysql_query($sql,__FILE__);
					
					while ($merchantww  = mysql_fetch_assoc($totalqq)) {
							$sql = 'SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO FROM data_stats ds '
									. 'WHERE ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
									. ' and ds.trader_id=' . $trader_id
									. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
									. " and ds.merchant_id = " . $clickww['merchant_id']
									 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
									  .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'');
						
							$traderStatsQ = function_mysql_query($sql,__FILE__);
							
							while($ts = mysql_fetch_assoc($traderStatsQ)){
									$spreadAmount = $ts['totalSpread'];
									$volume += $ts['totalTO'];
									
									$clickArray[$clickww['id']]['volume'] += $ts['totalTO'];
									
									$pnl = $ts['totalPnl'];
							}
									
									
							$totalLots  = 0;
														
						
								
							$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds '
							 . 'WHERE  ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
							 . ' and ds.trader_id=' . $trader_id
							 . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
							 . (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
							 . " and ds.merchant_id = " . $clickww['merchant_id']
							 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
							   .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'');
							
							$traderStatsQ = function_mysql_query($sql,__FILE__);
							$earliestTimeForLot = date('Y-m-d');
							while($ts = mysql_fetch_assoc($traderStatsQ)){
								
								if($ts['affiliate_id']==null) {
										continue;
								}
				
								// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
										$totalLots  = $ts['totalTurnOver'];
										// echo $totalLots
											$tradersProccessedForLots[$clickww['merchant_id'] . '-' . $ts['trader_id']] = $clickww['id'] . '-' . $ts['trader_id'];
											$lotdate = $ts['rdate'];
											$ex = explode(' ' , $lotdate);
											$lotdate = $ex[0];
												if ($earliestTimeForLot>$lotdate)
												$earliestTimeForLot = $lotdate;
											$row = [
														'merchant_id'  => $clickww['merchant_id'],
														'affiliate_id' => $ts['affiliate_id'],
														'rdate'        => $earliestTimeForLot,
														'banner_id'    => $ts['banner_id'],
														'trader_id'    => $ts['trader_id'],
														'profile_id'   => $ts['profile_id'],
														'type'       => 'lots',
														'amount'       =>  $totalLots,
											];
										$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
										//echo 'com: ' . $a .'<br>';
										$totalCom = $a;
										$clickArray[$clickww['id']]['totalCom'] += $totalCom;
								// }
								
							}
						}
						}						
					}
				 }
				 // trader id empty loop end
			 }//uid 0 loop end	
			 } 
		
		//}
		
		foreach($clickArray as $data){
			$refer_url = $data['refer_url'];
		    if(strlen($data['refer_url'])>50)
				$refer_url = substr($data['refer_url'],0,49). "...";
			
			$country_name = $allCountriesArray[$data['country'] ];
			if(strtolower($country)=='any'){
				$country_name = "";
			}
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$data['traffic_id'].'</td>
				<td style="text-align: left;">'.$data['uid'] .'</td>
				<td style="text-align: center;">'.@number_format($data['views'],0).'</td>
				<td style="text-align: center;">'.@number_format($data['clicks'],0).'</td>
				<td style="text-align: left;"><a href="/admin/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_id'] .'</a></td>
				 <td style="text-align: left;"><a href="/admin/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_username'] .'</a></td>
				<td style="text-align: left;">'.$data['traffic_date'] .'</td>
				<td style="text-align: left;">'.ucwords($data['type']).'</td>
				<td style="text-align: left;">'. $data['merchant_name'] .'</td>
				<td style="text-align: left;">'. $data['banner_id'] .'</td>
				<td style="text-align: left;">'. $data['profile_id'] .'</td>
				<td style="text-align: left;">'. $data['param'] .'</td>
				<td style="text-align: left;">'. $data['param2'] .'</td>
				<td style="text-align: left;"><a href="'. $data['refer_url'] .'" target="_blank">'.$refer_url.'</td>
				<td style="text-align: left;">'. $country_name .'</td>
				<td style="text-align: left;">'. $data['ip'] .'</td>
				<td style="text-align: left;">'. ucwords($data['platform']) .'</td>
				<td style="text-align: left;">'. $data['os'] .'</td>
				<td style="text-align: left;">'. $data['osVersion'] .'</td>
				<td style="text-align: left;">'. $data['browser'] .'</td>
				<td style="text-align: left;">'. $data['browserVersion'] .'</td>
				<td style="text-align: left;">'. $data['trader_id'] .'</td>
				<td style="text-align: left;">'. $data['trader_name'] .'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=lead">'.$data['leads'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=demo">'.$data['demo'].'</a></td>
				<td style="text-align: left;">'. $data['sale_status'] .'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=real">'.$data['real'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=ftd&trader_id='. $data['trader_id'] .'">'.$data['ftd'].'</a></td>
				<td>'.price($data['ftd_amount']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=totalftd&trader_id='. $data['trader_id'] .'">'.$data['real_ftd'].'</a></td>
				<td>'.price($data['real_ftd_amount']).'</td>
				<td><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>
				<td>'.price($data['sumDeposits']).'</td>
				<td style="text-align: center;">'.price($data['volume']).'</td>
				<td>'.price($data['bonus']).'</td>
				<td>'.price($data['withdrawal']).'</td>
				<td>'.price($data['chargeback']).'</td>
				<td style="text-align: center;">'.price($data['netRevenue']).'</td>
				<td>'.price($data['totalCom']).'</td>
			</tr>';
			
			$totalImpressions += $data['views'];
			$totalClicks += $data['clicks'];
			$totalLeadsAccounts += $data['leads'];
			$totalDemoAccounts += $data['demo'];
			$totalRealAccounts += $data['real'];
			$totalFTD += $data['ftd'];
			$totalDeposits += $data['depositingAccounts'];
			$totalFTDAmount += $data['ftd_amount'];
			$totalDepositAmount += $data['sumDeposits'];
			$totalVolume += $data['volume'];
			$totalBonusAmount += $data['bonus'];
			$totalWithdrawalAmount += $data['withdrawal'];
			$totalChargeBackAmount += $data['chargeback'];
			$totalNetRevenue += $data['netRevenue'];
			$totalComs += $data['totalCom'];
			$totalRealFtd += $data['real_ftd'];
			$totalRealFtdAmount += $data['real_ftd_amount'];
                        $l++;
		// echo $ftd_amount.'<br>';
		$ftd_amount = $real_ftd_amount = 0;
		// $totalLeads = $totalDemo = $totalReal = $ftd = $depositingAccounts = $ftd_amount = $sumDeposits = $volume = $bonus = $withdrawal = $chargeback= $netRevenue= $totalCom= $real_ftd= $real_ftd_amount = 0;
						
		}
                
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->totalRows  = $l;
		$set->sortTable  = 1;
		$set->content   .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="clicks" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Unique ID').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td>'.lang('Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="unique_id" value="'.$unique_id.'" /></td>
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td>
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="clicks" '.($type == "clicks" ? 'selected' : '').'>'.lang('Clicks').'</option>
						<option value="views" '.($type == "views" ? 'selected' : '').'>'.lang('Views').'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" style="width: 2400px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2400" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th style="text-align: left;">'.lang('ID').'</th>
						<th style="text-align: left;">'.lang('UID').'</th>
						
						<th style="text-align: center;">'.lang('Impression').'</th>
						<th style="text-align: center;">'.lang('Click').'</th>
						
						<th style="text-align: left;">'.lang('Affiliate ID').'</th>
						<th style="text-align: left;">'.lang('Affiliate Username').'</th>
						<th>'.lang('Date').'</th>
						<th>'.lang('Type').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th style="text-align: left;">'.lang('Banner ID').'</th>
						<th style="text-align: left;">'.lang('Profile ID').'</th>
						<th style="text-align: left;">'.lang('Param').'</th>
						<th style="text-align: left;">'.lang('Param2').'</th>
						<th style="text-align: left;">'.lang('Refer URL').'</th>
						<th style="text-align: left;">'.lang('Country').'</th>
						<th style="text-align: left;">'.lang('IP').'</th>
						<th style="text-align: left;">'.lang('Platform').'</th>
						<th style="text-align: left;">'.lang('Operating System').'</th>
						<th style="text-align: left;">'.lang('OS Version').'</th>
						<th style="text-align: left;">'.lang('Browser').'</th>
						<th style="text-align: left;">'.lang('Broswer Version').'</th>
						
						<th style="text-align: left;">'.lang('Trader Id').'</th>
						<th style="text-align: left;">'.lang('Trader Alias').'</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang('Sale Status').'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						<th style="text-align: center;">'.$totalImpressions.'</th>
						<th style="text-align: center;">'.$totalClicks.'</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr, 'Clicks');
		$set->content.=$tableStr.'</div>'.getURLPager();
		theme();
		break;
        
}

	
$fileName =   "admin/csv/report.csv";
$openFile = fopen($fileName, 'w'); 
// fwrite($openFile, $csvContent); 
fclose($openFile); 
header("Expires: 0");
header("Pragma: no-cache");
header("Content-type: application/ofx");
header("Content-Disposition: attachment; filename=".date('Ymd').'-'.$fileName);
for ($i=0; $i<=count($csvContent)-1; $i++) echo implode(",",$csvContent[$i])."\n";
die();

?>