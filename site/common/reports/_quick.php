<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$pageTitle = lang('Quick Summary Report');
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$userlevel.'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		
		$set->content .= '<script type="text/javascript" src="../../js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="../../js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="../../js/tableExport/jquery.base64.js"></script>';
		$filename = "QuickSummary_data_" . date('YmdHis');
	
	
	if($userlevel == 'manager')
	$group_id       = $set->userInfo['group_id'];

		$listReport = '';
                
                // List of wallets.
                $arrWallets = [];
                // $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1;";
				$merchant_id = isset($merchant_id) && $merchant_id>0 ? $merchant_id : 0;
				$merchantsA  = getMerchants($merchant_id,1);
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
                                    . "WHERE merchant_id>0 and merchant_id = '" . $ww['id'] . "' AND "
                                    . " rdate " . $searchInSql 
                                    . (isset($affiliate_id) && $affiliate_id != '' ? ' AND affiliate_id = ' . $affiliate_id . ' ' : '')
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
                                        . "WHERE data_sales.merchant_id>0 and data_sales.merchant_id = '" . $ww['id'] . "' AND data_sales.rdate "
                                        . $searchInSql . " "
                                        . (isset($affiliate_id) && $affiliate_id != '' ? ' AND data_sales.affiliate_id = ' . $affiliate_id . ' ' : '')
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
                                    (isset($affiliate_id) && $affiliate_id != '' ? $affiliate_id : 0), 
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
                                        
										if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
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
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '').
											  (isset($affiliate_id) && $affiliate_id != '' ? ' and id = ' . $affiliate_id : '');
                                            
                                    
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
                                            . 'WHERE merchant_id>0 and merchant_id = "' . $merchantID . '" AND rdate ' . $searchInSql
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '')
                                            . (isset($affiliate_id) && $affiliate_id != '' ? ' AND affiliate_id = ' . $affiliate_id . ' ' : '');
                                    
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
										 . 'WHERE merchant_id>0 and merchant_id = "' .$merchantID . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : ''). (isset($affiliate_id) && $affiliate_id != '' ? ' AND affiliate_id = ' . $affiliate_id . ' ' : '');
											
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
					  'str' => ($display_type == "daily" ? '<td style="text-align: center;">' . $arrRange['from'].' - '.$arrRange['to'] . '</td>' : '')
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
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.@number_format($totalTraffic['totalViews'],0).'</a></td>'
					),
					(object) array(
					  'id' => 'totalClicks',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.@number_format($totalTraffic['totalClicks'],0).'</a></td>'
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
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>'
					),
					(object) array(
					  'id' => 'total_demo',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>'
					),
					(object) array(
					  'id' => 'total_real',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($ftd_amount['amount']).'</td>'
					),
					
					(object) array(
					  'id' => 'real_ftd',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=totalftd">'.$real_ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'real_ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($real_ftd_amount).'</td>'
					),
					
					(object) array(
					  'id' => 'depositAccount',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=transactions&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>'
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
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressions.'</a></th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicks.'</a></th>'
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
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'total_demo',
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd_amount',
			  'str' => '<th>'.price($totalFTDAmount).'</th>'
			),
			
			(object) array(
			  'id' => 'real_ftd',
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>'
			),
			(object) array(
			  'id' => 'real_ftd_amount',
			  'str' => '<th>'.price($totalRealFtdAmount).'</th>'
			),
			
			(object) array(
			  'id' => 'depositAccount',
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>'
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
						<td>'.lang('Affiliate ID').'</td>
						'.($userlevel == "admin"? '<td>'.lang('Group ID').'</td>':'').'
						<td>'.lang('Search Type').'</td>
						<td></td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="affiliate_id" style="width: 60px; text-align: center;" /></td>
					<!--td><input type="text" name="group_id" value="'.$group_id.'" id="group_id" style="width: 60px; text-align: center;" /></td-->
                                        '.($userlevel == 'admin'?'<td width="100">
                                            <select name="group_id" style="width: 100px;">
                                                <option value="">'.lang('All Groups').'</option>'
                                                . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                    . lang('General') 
                                                . '</option>' 
                                                . listGroups($group_id) 
                                            . '</select>
                                        </td>':'').'
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 2000px;">'.lang('Quick Summary Report').'</div>
		<div style="background: #F8F8F8;">';
			$tableStr = '
			<table width="2000" class="tablesorter" border="0" cellpadding="0" cellspacing="0" id="quickTbl">
				<thead><tr>
					'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'
				</tr></thead><tfoot><tr>
					'.setTable($tableArr2, $boxaName, $set->userInfo['productType'], '').'
				</tr></tfoot>
				<tbody>
				'.$listReport.'
			</table>
			<script>
				$(document).ready(function(){
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'quickData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#quickTbl")[0].config.rowsCopy).each(function() {
						txt += "<tr>" + $(this).html()+"</tr>";
					});
					txt += "</tbody>";
					txt += "<tfoot>" + tfoot + "</tfoot>";
					txt += "</table>";
					$("body").append("<div style=\'display:none\'>"+ txt +"</div>");
				});
				</script>
			';
		
		//excelExporter($tableStr, 'Quick');
		$set->content .= $tableStr . '</div>' . getPager();
		theme();
		
?>