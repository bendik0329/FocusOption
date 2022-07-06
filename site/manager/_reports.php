<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */
require_once('common/global.php');

/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();

if (!isManager()) {
    _goto('/manager/');
}

$networkWhereid = '';

$hideDemoAndLeads = hideDemoAndLeads();

if ($set->isNetwork == 1) {
    $networkWhereid  = ' AND id=' . aesDec($_COOKIE['mid']) . ' ';
    $networkWheremid = ' AND merchant_id=' . aesDec($_COOKIE['mid']) . ' ';
}

$from = strTodate($from);
$to   = strTodate($to);

$from = sanitizeDate($from);
$to   = sanitizeDate($to);

commonGlobalSetTimeRange($from, $to);
$globalWhere = "group_id = '".$set->userInfo['group_id']."' AND ";



switch ($act) {
	
        default:
                $set->pageTitle = lang('Quick Summary Report');
		$listReport     = '';
                $group_id       = $set->userInfo['group_id'];
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1;";
                $resourceWallets = function_mysql_query($sql,__FILE__);
		
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
		$l = -1;
		$sql = "SELECT * FROM merchants WHERE valid='1' ORDER BY type, pos";
		$qq = function_mysql_query($sql,__FILE__);
		
		while ($ww = mysql_fetch_assoc($qq)) {
		
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
                            $withdrawal = 0;
                            $chargeback = 0;
                            $depositingAccounts = 0;
                            $sumDeposits = 0;
                            $volume = 0;
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
                                $sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                                 . "WHERE affiliate_id = '" . $regww['affiliate_id'] . "' "
                                 . "AND merchant_id = '" . $regww['merchant_id'] . "' AND dealType = 'tier' "
                                 . "ORDER BY id DESC "
                                 . "LIMIT 0, 1;";
                            
                                $strAffDealType   = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                                $boolTierCplCount = !is_null($strAffDealType['tier_type']) && 'cpl_count' == $strAffDealType['tier_type'];
                                
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

                                        $totalCom += getCommission($arrRange['from'], $arrRange['to'], 0, (isset($group_id) && $group_id != '' ? $group_id : -1), $arrDealTypeDefaults, $arrTmp);
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
                                                'groupId'             => (isset($group_id) && $group_id != '' ? $group_id : -1),
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
                            
                            

                                $strSql = "SELECT * , tb1.type as data_sales_type FROM data_sales as tb1 "
                                        . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo' "
                                        . "WHERE tb1.merchant_id = '" . $ww['id'] . "' AND tb1.rdate "
                                        . $searchInSql . " "
                                        . (isset($group_id) && $group_id != '' ? ' AND tb1.group_id = ' . $group_id . ' ' : '');
                                
				$salesqq = function_mysql_query($strSql,__FILE__);
				
				while ($salesww = mysql_fetch_assoc($salesqq)) {
                                    if ($salesww['data_sales_type'] == 'deposit') {
                                        $sumDeposits += $salesww['amount'];
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
                                            'amount'   => $salesww['amount'],
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
                                    // $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
									$netRevenue =  round(getRevenue("",$ww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$ww['rev_formula'],null,$chargeback),2);
                                }
                                
                                
                                if (strtolower($ww['producttype']) == 'forex') {
                                    $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO '
                                            . 'FROM data_stats '
                                            . 'WHERE merchant_id = "' . $merchantID . '" AND rdate ' . $searchInSql
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                                    
                                    $traderStatsQ = function_mysql_query($sql,__FILE__);
                                    
                                    while($ts = mysql_fetch_assoc($traderStatsQ)){
                                        $spreadAmount  = $ts['totalSpread'];
                                        $volume       += $ts['totalTO'];
                                        $pnl           = $ts['totalPnl'];
                                    }
				}
                                
				$filterFrom = $arrRange['from'];
				$filterTo   = $arrRange['to'];
				$boxaName   = "manager-quick-report-1";
                                
                                
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
					  'str' => '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>'
					),
					(object) array(
					  'id' => 'total_demo',
					  'str' => '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>'
					),
					(object) array(
					  'id' => 'total_real',
					  'str' => '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd',
					  'str' => '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($ftd_amount['amount']).'</td>'
					),
					
					(object) array(
					  'id' => 'real_ftd',
					  'str' => '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=totalftd">'.$real_ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'real_ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($real_ftd_amount).'</td>'
					),
					
					(object) array(
					  'id' => 'depositAccount',
					  'str' => '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>'
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
			  'str' => '<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'total_demo',
			  'str' => '<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd_amount',
			  'str' => '<th>'.price($totalFTDAmount).'</th>'
			),
			
			(object) array(
			  'id' => 'real_ftd',
			  'str' => '<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>'
			),
			(object) array(
			  'id' => 'real_ftd_amount',
			  'str' => '<th>'.price($totalRealFtdAmount).'</th>'
			),
			
			(object) array(
			  'id' => 'depositAccount',
			  'str' => '<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>'
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
						<td>'.lang('Search Type').'</td>
						<td></td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
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
					<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
					<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
					<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
					<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
					<th>'.price($totalFTDAmount).'</th>
					<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
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
		
		if ($merchant_id) $where = " AND tb1.merchant_id='".$merchant_id."'";
		if ($profile_id) $whereSites = " AND tb1.profile_id='".$profile_id."'";
		// if ($banner_id) $whereSites .= " AND tb1.banner_id='".$banner_id."'";
		if ($affiliate_id) $whereSites .= " AND tb1.affiliate_id='".$affiliate_id."'";
		
	
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
			{
				
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
				$merchantName = strtolower($ww['name']);
				$merchantID = $ww['id'];
				
				
				
				$filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showFrom));
				$filterTo = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showTo));
				
				
				
			
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		
					$qry = ("SELECT af.group_id as group_id ,tb1.*,mr.name as merchant_name,af.username FROM traffic tb1 
									inner join affiliates af on tb1.affiliate_id = af.id 
									inner join merchants mr on mr.id = tb1.merchant_id 
									WHERE type='traffic' and  ".str_replace('group_id','af.group_id', $globalWhere). '  1=1 ' . $whereSites." AND tb1.rdate ".$searchInSql." order by tb1.id desc");
// die($qry);				
				$listReport="";		
				$resc = function_mysql_query($qry,__FILE__);
				$i = 0;
				$totalVisits = 0;
				while ($arrRes = mysql_fetch_assoc($resc)) {
				
				
				$country='';
			$countryArry = getIPCountry($arrRes['ip']);
			if ($countryArry['countryLONG']=='')
				$country = lang('Unknown');
			else
				$country = $countryArry['countryLONG'];
			
			// var_dump($country);
		
				$listReport .= '
                        <tr>
                            <td>'.$arrRes['rdate'].'</td>
                            <td>'.$arrRes['id'].'</td>
                            <td>'.$arrRes['merchant_id'].'</td>
                            <td>'.$arrRes['merchant_name'].'</td>
                            <td><a href="'.$arrRes['refer_url'].'" target="_blank">'.$arrRes['refer_url'].'</a></td>
                            <td>'.$arrRes['ip'].'</td>
                            <td>'.$country.'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
							<td><a href="/manager/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$arrRes['username'].'</a></td>
                            <td>'.$arrRes['profile_id'].'</td>
                            
                            <td>'.$arrRes['visits'].'</td>';
							
							$i++;
							
							$totalVisits +=$arrRes['visits'];
							
				}		
				
		}	
		
		$set->totalRows = $l;
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="traffic" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					
					<td>'.lang('Merchant').'</td>
					<!--td>'.lang('Search Type').'</td-->
					
					<td>'.lang('Affiliate ID').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					
					
					
												
					
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
					
						
						<th>'.lang('Last Click Date').'</th>
						<th style="text-align: left;">'.lang('ID').'</th>
						<th style="text-align: center;">'.lang('Merchant ID').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Refer URL').'</th>
						<th>'.lang('Last Click IP').'</th>
						<th>'.lang('Last Click Country').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Affiliate Username').'</th>
						<th>'.lang('Profile ID').'</th>
						
						<th style="text-align: center;">'.lang('All Time Clicks').'</th>
					</tr></thead><tfoot><tr>
					
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
						<th>'.($totalVisits).'</th>
					
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr,'Traffic');
		$set->content.=$tableStr.'</div>'.getPager();
		theme();
		break;
	
	
	
                
        case "banner":
		$set->pageTitle = lang('Creative Report');
                $where = ' 1 = 1 ';
                
                $group_id  = $set->userInfo['group_id'];
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		if ($banner_id) {
                    $where .= " AND banner_id='".$banner_id."' ";
                }
                
		if ($profile_id) {
                    $where  .= " AND profile_id='".$profile_id."' ";
                }
                
		$sql = "SELECT * FROM stats_banners "
                     . "WHERE " . $where . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                     . " GROUP BY banner_id";
		
		$qq = function_mysql_query($sql,__FILE__);
		
		while ($ww = mysql_fetch_assoc($qq)) {
                    $arrClicksAndImpressions = getClicksAndImpressions($from, $to, $ww['merchant_id'], $ww['affiliate_id'], $group_id);
                    $ww['totalViews']        = $arrClicksAndImpressions['impressions'];
                    $ww['totalClicks']       = $arrClicksAndImpressions['clicks'];
                    
                    $sql = "SELECT id, title, type FROM merchants_creative WHERE id = '" . $ww['banner_id'] . "' ";
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
                    $totalCom=0;
                    
                    $sql = 'SELECT * FROM merchants WHERE valid = 1 AND id = ' . $ww['merchant_id'];
                    $merchantww = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                    
                    $formula = $merchantww['rev_formula'];
                    $merchantID = $merchantww['id'];
                    $merchantName = strtolower($merchantww['name']);
                    
                    $sql = "SELECT * FROM data_reg "
                         . "WHERE " . $where . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "' ";
                    
                    $regqq = function_mysql_query($sql,__FILE__);
                    
                    $arrTierCplCountCommissionParams = [];
                    
                    while ($regww = mysql_fetch_assoc($regqq)) {
                        $sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                             . "WHERE affiliate_id = '" . $regww['affiliate_id'] . "' "
                             . "AND merchant_id = '" . $regww['merchant_id'] . "' AND dealType = 'tier' "
                             . "ORDER BY id DESC "
                             . "LIMIT 0, 1;";
                        
                        $strAffDealType   = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                        $boolTierCplCount = !is_null($strAffDealType['tier_type']) && 'cpl_count' == $strAffDealType['tier_type'];
                        
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

                                $totalCom += getCommission($from, $to, 0, (isset($group_id) && $group_id != '' ? $group_id : -1), $arrDealTypeDefaults, $arrTmp);
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
                                        'groupId'             => (isset($group_id) && $group_id != '' ? $group_id : -1),
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
                    
		    
                    
                    $arrFtds  = getTotalFtds($from, $to, $ww['affiliate_id'], $ww['merchant_id'], 0, (is_null($group_id) ? 0 : $group_id));
                    
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
                    
                    
                    if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                        $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantww['id'], $ww['id'], $arrDealTypeDefaults);
                        $intTotalRevenue  = 0;
                        
                        foreach ($arrRevenueRanges as $arrRange2) {
                            $strRevWhere = 'WHERE merchant_id = ' . $merchantww['id'] . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                         . '" AND affiliate_id = "' . $ww['id'] . '" ' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '));
                            
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
						$netRevenue =  round(getRevenue("",$ww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$ww['rev_formula'],null,$chargeback),2);
                    }
                    
                    
                    $sql = "SELECT *,tb1.type as data_sales_type FROM data_sales as tb1 "
                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo' "
                            . "WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND tb1.banner_id='".$bannerInfo['id'] 
                            . "' AND tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '));
		    
                    $salesqq = function_mysql_query($sql,__FILE__);
                    
                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                        if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {
                            $depositingAccounts++;
                            $sumDeposits += $salesww['amount'];
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
                    
                    
                    if(strtolower($merchantww['producttype']) == 'forex') {
                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                . 'WHERE merchant_id="' . $merchantww['id'] . '" AND banner_id="'.$bannerInfo['id'] 
                                . '" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '));
                        
                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                        
                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                $spreadAmount = $ts['totalSpread'];
                                $volume += $ts['totalTO'];
                                $pnl = $ts['totalPnl'];
                        }
                    }
                    
                    
                    
                    
                    if (strtolower($merchantww['producttype']) != 'binary') {
                        $sql = "SELECT DISTINCT affiliate_id AS id FROM data_stats "
                             . "WHERE merchant_id = " . $merchantww['id'] . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "' " 
                             . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' '); 
                        
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
						$netRevenue =  round(getRevenue("",$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
                    }
                    
		    
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$bannerInfo['id'].'</td>
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="NewWin(\'/manager/creative.php?act=edit_banner&id='.$bannerInfo['id'].'\',\'editbanner_'.$bannerInfo['id'].'\',\'1000\',\'800\',\'1\');">'.lang('Edit').'</a></td>
				<td style="text-align: left;">'.($bannerInfo['id'] ? $bannerInfo['title'] : lang('BANNER REMOVED')).'</td>
				<td style="text-align: left;">'.$merchantww['name'].'</td>
				<td style="text-align: left;">'.$bannerInfo['type'].'</td>
				<td>'.@number_format($ww['totalViews'],0).'</td>
				<td>'.@number_format($ww['totalClicks'],0).'</td>
				<td>'.@number_format(($ww['totalClicks']/$ww['totalViews'])*100,2).' %</td>
				<td>'.@number_format(($totalReal/$ww['totalClicks'])*100,2).' %</td>
				<td>'.@number_format(($ftd/$ww['totalClicks'])*100,2).' %</td>
				<td>'.@price($totalCom/$ww['totalClicks']).'</td>
				<td><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=lead">'.$totalLeads.'</a></td>
				<td><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=demo">'.$totalDemo.'</a></td>
				<td><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=real">'.$totalReal.'</a></td>
				<td><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=ftd">'.$ftd.'</a></td>
				<td>'.price($ftd_amount).'</td>
				<td><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=totalftd">'.$real_ftd.'</a></td>
				<td>'.price($real_ftd_amount).'</td>
				<td><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
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
					<td>'.lang('Creative Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" /></td>
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
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Type').'</th>
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
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
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
		
		
                
        case "stats":
		$set->pageTitle = lang(ptitle('Trader Stats Report'));
                $group_id = $set->userInfo['group_id'];
                $where = '';
		$l = 0;
		
		if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."'";
		if ($group_id) $where .= " AND group_id='".$group_id."'";
		if ($banner_id) $where .= " AND banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND profile_id='".$profile_id."'";
		if ($trader_id) $where .= " AND trader_id='".$trader_id."'";
		if ($type != "" AND $type != "deposit") $where .= " AND type='".$type."'";
		
		if ($merchant_id) {
			$ww = dbGet($merchant_id,"merchants");
			if (!$ww['id']) _goto();
			$brokers_ids[] = $ww['id'];  
			$brokers[] = $ww['name'];
			$brokers_formula = $ww['rev_formula'];
		} else {
			$qq=function_mysql_query("SELECT id,name,rev_formula FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$brokers_ids[] = $ww['id'];
				$brokers[] = $ww['name'];
				$brokers_formula = $ww['rev_formula'];
				}
		}
		
		
		
			$filterhtml = '';
				
			$sql = "
				SELECT distinct type FROM data_stats order by type";
				$qq=function_mysql_query($sql,__FILE__);
				$filterhtml .='<option value="" '.($type =='' ? 'selected' : '').'>'.lang('All Types').'</option>';
				while ($ww=mysql_fetch_assoc($qq)) {
					//$filterhtml .='<option value="'.$ww['type'].'">'.lang($ww['type']).'</option>';
					$filterhtml .='<option value="'.$ww['type'].'" '.($type == $ww['type'] ? 'selected' : '').'>'.ucwords(str_replace('_',' ',$ww['type'])).'</option>';
					
				}
				
		for ($i=0; $i<=count($brokers)-1; $i++) {
			
			$formula = $brokers_formula[$i];
			$broker['name'] = $brokers[$i];
		
		
				$sql = "
				SELECT ds.*"."
				FROM data_stats AS ds
				WHERE ds.merchant_id = '" . $brokers_ids[$i]."' and rdate BETWEEN '".$from."' AND '".$to."' ".$where;
				$sql.=" ORDER BY ds.trader_id ASC";
				
				
				$qq=function_mysql_query($sql,__FILE__);
			
			while ($ww=mysql_fetch_assoc($qq)) {
				$merchantInfo = dbGet($ww['merchant_id'],"merchants");
				
				$productType = $merchantInfo['producttype'];
				
				$listReport .= '<tr>
						<td>'.$ww['trader_id'].'</td>
						<td>'.$ww['trader_alias'].'</td>
						<td>'.$ww['tranz_id'].'</td>
						<td>'. date("Y/m/d H:i:s", strtotime($ww['rdate'])).'</td>
						<td>'.longCountry($ww['country']).'</td>
						<td><a href="/manager/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$ww['affiliate_id'].'</a></td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td style="text-align: left;"><a href ="/manager/reports.php?act=banner&banner_id='.$ww['banner_id'].'" target="_blank">'.$ww['banner_id'].'</a></td>
						<td>'.$ww['profile_id'].'</td>
						<td>'.$ww['group_id'].'</td>
						<td>'.$ww['ctag'].'</td>
						<td>'.$ww['freeParam'].'</td>
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
						<td style="display: none;">'.lang('Group ID').'</td>
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from,$to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td width="100" style="display: none;">
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
						<th></th>
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
                
               
                
        case "trader":
                $set->pageTitle = lang(ptitle('Trader Report'));
                $group_id = $set->userInfo['group_id'];
                
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
                    $int_merchant_id = empty($intTmpMerchantId) ? $arrMerchant['id'] : $intTmpMerchantId;
                    $where = ' AND merchant_id = ' . $int_merchant_id;
                    
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                     $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
                    if ($param) $where .= " AND freeParam='".$param."' ";
                    if ($param2) $where .= " AND freeParam2='".$param2."' ";
                    
                    if ($trader_alias) {
						$qry = "select trader_id from data_reg  where  lower(trader_alias) like lower('%". mysql_real_escape_string($trader_alias)."%')";
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
				
				
			
               foreach ($arrResultSet as $arrRes) {
					
					/* $key = array_keys($arrResultSet);
					$size = sizeOf($key);
					for ($i=0; $i<$size; $i++) {
						$arrRes = $arrResultSet[$key[$i]] ;
	 */
					// echo $i . '<br>';
                    // var_dump($arrRes);
					$sql = 'SELECT * FROM merchants WHERE id = ' . $arrRes['merchant_id'] . ' AND valid = 1 LIMIT 0, 1;';
                    // die ('arrres: ' . var_dump($arrRes));
					$arrMerchant = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                    
					
					
					//HACK FIX - TEMPORARY - NEED TO BE TEST!!!
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
                        unset($intTmpTraderId, $intTmpGroupId, $strTmpWhere, $intTmpMerchantId);
                    }
                    
                    
                    $depositAmount = 0;
					
					
					
					  // BANNER info retrieval.
                    $bannerInfo = ['id' => '', 'type' => '', 'title' => '',];
                    if (isset($arrRes['banner_id']) && !empty($arrRes['banner_id'])) {
                        $sql = "SELECT l.title as language_name, mc.* FROM merchants_creative mc  left join languages l on l.id = mc.language_id "
                                . "WHERE 1 = 1 AND mc.id = " . $arrRes['banner_id'] . " "
                                . "LIMIT 0, 1;";

                        $bannerInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                    }
                    
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
							
							if (in_array($arrAmount['trader_id'], $frozenTraders))
								continue;
							
							
							
								
								
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
                        }
                        
                    } 
				/* 	else {

                        if (
                            $type == 'ftd' || 
                            $type == 'totalftd' || 
                            $type == 'deposit' || 
                            $type == 'revenue'
                        ) {
							
							// var_dump($arrRes);
							// die();
							// die('greger');
                            if ($arrRes['salesType'] == 'deposit') {
                                $depositAmount += $arrRes['amount'];
                                $total_deposits++;
                            } elseif ($arrRes['salesType'] == 'bonus') {
                                $bonusAmount += $arrRes['amount'];
                            } elseif ($arrRes['salesType'] == 'withdrawal') {
                                $withdrawalAmount += $arrRes['amount'];
                            } elseif ($arrRes['salesType'] == 'chargeback') {
                                $chargebackAmount += $arrRes['amount'];
                            } elseif ($arrRes['salesType'] == 'volume') {
                                $volumeAmount += $arrRes['amount'];
                                $totalTraders++;
								
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
                            
                        } else {
                            if ($arrRes['type'] == 'deposit') {
                                $depositAmount += $arrRes['amount'];
                                $total_deposits++;
                            } elseif ($arrRes['type'] == 'bonus') {
                                $bonusAmount += $arrRes['amount'];
                            } elseif ($arrRes['type'] == 'withdrawal') {
                                $withdrawalAmount += $arrRes['amount'];
                            } elseif ($arrRes['type'] == 'chargeback') {
                                $chargebackAmount += $arrRes['amount'];
                            } elseif ($arrRes['type'] == 'volume') {
                                $volumeAmount += $arrRes['amount'];
                                $totalTraders++;
								
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
                        }
                    } */
                    
					
					
                    
                    if ($type != 'ftd' && $type != 'totalftd') {
                        //$ftd = $totalTraders = $depositAmount = $total_deposits = $volumeAmount = 0;
                       // $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                       // $spreadAmount = $pnl = $ftdAmount = 0;
                        $ftdUsers = '';
                        
                        $arrTotalFtds = getTotalFtds(
                            $from, $to, $arrRes['affiliate_id'], $arrRes['merchant_id'], $arrMerchant['wallet_id'], 
                            $arrRes['group_id'], $arrRes['banner_id'], $arrRes['profile_id'], '', $arrRes['trader_id']
                        );
						
						
						
								
                        
						/* $key = array_keys($arrTotalFtds);
					$size = sizeOf($key);
					for ($k=0; $k<$size; $k++) {
						$arrResLocal = $arrTotalFtds[$key[$k]] ;
						
	 */
                        foreach ($arrTotalFtds as $arrResLocal) {
                            $beforeNewFTD = $ftd;
                            getFtdByDealType($arrRes['merchant_id'], $arrResLocal, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);

                            if ($beforeNewFTD != $ftd) {
                                $firstDeposit = $arrResLocal;
								
                                $ftdAmount = $arrResLocal['amount'];
								// var_dump($ftdAmount);
								// die('ggg');
                                $arrResLocal['isFTD'] = true;
                                
                                // Old version.
                                //$totalCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrResLocal);
                                
								
								
                                // New version.
                                if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
                                    $totalCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrResLocal);
                                }
                            }
                            unset($arrResLocal);
                        }
                    }



                    if (strtolower($arrMerchant['producttype']) == 'sportsbetting' || strtolower($arrMerchant['producttype']) == 'casino') {
                        $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $arrRes['merchant_id'], $arrRes['affiliate_id'], $arrDealTypeDefaults);
                        $intTotalRevenue  = 0;
                        
                        foreach ($arrRevenueRanges as $arrRange) {
                            $sql = 'SELECT * FROM affiliates WHERE valid = 1 AND id = ' . $arrRes['affiliate_id'] . ' LIMIT 0, 1;';
                            $arrAffiliate = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
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
						
						
						
$netRevenue =  round(getRevenue("",$arrMerchant['producttype'],$depositAmount,$bonusAmount,$withdrawalAmount,0,0,0,$arrMerchant['rev_formula'],null,$chargebackAmount),2);
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
                            $row['rdate']        = $from;
                            $row['amount']       = $netRevenue;
                            $row['trader_id']    = $arrRes['trader_id'];
                            $row['isFTD']        = false;
							   	
								
								// if ($set->userIP=='80.178.187.235') {
							    $totalCom           += getCommission($from, $to, 1, $group_id, $arrDealTypeDefaults, $row);
	// var_dump($arrRange);
					// die('totalCom: ' . $totalCom);
	// }
						
                    }
					
                 //   $netRevenue = $depositAmount; // NEW.
					
					
                    // AFFILIATE info retrieval.
                    $sql = "SELECT * FROM affiliates AS aff "
                            . " WHERE aff.valid = 1 AND id = " . $arrRes['affiliate_id']
                            . " LIMIT 0, 1;";

                    $affInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));

                    if ($arrRes['type'] == 'real') {
                        $color = 'green';
                    } elseif ($arrRes['type'] == 'demo') {
                        $color = 'red';
                    } elseif ($arrRes['type'] == 'lead') {
                        $color = 'black';
                    }

					
					
					$reason="";
					
                    // Check trader.
                                   $sql = 'SELECT * FROM payments_details '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrMerchant['id'] 
                            . '  LIMIT 0, 1;';
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (empty($chkTrader)) {
						$sql = 'SELECT * FROM traders_tag '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrMerchant['id'] 
                            . '  LIMIT 0, 1;';
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (!empty($chkTrader)) {
						$reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
					}
					
					}else {
					$reason = chkTrader['reason'];
						
					}
					
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
                            <td><a href="/manager/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
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
                            <td><a href="/manager/reports.php?act=trader&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
                            <td>'.price($depositAmount).'</td>
                            <td>'.price($volumeAmount).'</td>
                            <td>'.price($bonusAmount).'</td>
                            <td>'.price($withdrawalAmount).'</td>
                            <td>'.price($chargebackAmount).'</td>
                            <td>'.price($netRevenue).'</td>
                            <td>'.$totalTraders.'</td>
                            <td>'.$arrRes['saleStatus'].'</td>
                            <td>'.price($totalCom).'</td>
                            <td>'.$reason.'</td>
                        </tr>';
                    
                    if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
                        $arrTradersPerMerchants[] = $arrRes['trader_id'];
                        $totalFTD += $ftdAmount;
                        $totalNetRevenue += $netRevenue;
                        $totalTotalCom += $totalCom;
                    }

                    $totalDepositAmount += $depositAmount;
                    $totalVolumeAmount += $volumeAmount;
                    $totalBonusAmount += $bonusAmount;
                    $totalTotalDeposit += $total_deposits;
                    $totalTrades += $totalTraders;
                    $totalWithdrawalAmount += $withdrawalAmount;
                    $totalChargeBackAmount += $chargebackAmount;
                    $ftdExist[] = $firstDeposit['trader_id'];
                    $l++;
					
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$totalTraders=0;
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
						<th>'.lang('Total Deposits').'</th>
						<th>'.($type == "deposit" ? lang('Deposit Amount') : lang('Deposits Amount')).'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang(ptitle('Trades')).'</th>
						<th>'.lang('Sale Status').'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('manager Notes').'</th>
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
						<th style="text-align: left;">'.$totalTotalDeposit.'</th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th style="text-align: left;">'.price($totalVolumeAmount).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th style="text-align: left;">'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.$totalTrades.'</th>
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
                
		
		
                
                
		
        case "profile":
                $set->pageTitle   = lang('Profile Report');
                $showLeadsAndDemo = false;
                $where            = '';
                $sql              = 'SELECT extraMemberParamName AS title FROM merchants';
		$campID           = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		
                
                $sql = "SELECT COUNT(id) AS count FROM merchants WHERE valid = 1;";
                $arrMerchantCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                $intMerchantCount = $arrMerchantCount['count'];
                
                
                $group_id = $set->userInfo['group_id'];
                
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
                        
                        $sql = 'SELECT id,name,type,producttype,rev_formula, wallet_id  FROM merchants WHERE valid = 1 ' 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        
                        $merchantqq = function_mysql_query($sql,__FILE__);
                        
                        while ($merchantww = mysql_fetch_assoc($merchantqq)) {
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
                                $sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                                 . "WHERE affiliate_id = '" . $regww['affiliate_id'] . "' "
                                 . "AND merchant_id = '" . $regww['merchant_id'] . "' AND dealType = 'tier' "
                                 . "ORDER BY id DESC "
                                 . "LIMIT 0, 1;";
                                
                                $strAffDealType   = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                                $boolTierCplCount = !is_null($strAffDealType['tier_type']) && 'cpl_count' == $strAffDealType['tier_type'];
                                
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

                                        $totalCom += getCommission($arrRange['from'], $arrRange['to'], 0, (isset($group_id) && $group_id != '' ? $group_id : -1), $arrDealTypeDefaults, $arrTmp);
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
                                                'groupId'             => (isset($group_id) && $group_id != '' ? $group_id : -1),
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
								$netRevenue =  round(getRevenue("",$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
                            }
                            
                            
                                    $sql = "SELECT *,tb1.type as data_sales_type FROM data_sales as tb1 "
                                    . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo' "
                                    . "WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND tb1.affiliate_id='".$ww['id']."' "
                                    . "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' "
                                    . (is_null($group_id) ? '' : ' AND tb1.group_id = ' . $group_id . ' ')
                                    . ' AND tb1.profile_id = ' . $ww['ProfileId'];
                                    
                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    $volume = 0;
                                    
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                                        if ($salesww['data_sales_type'] == 'deposit') {
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
                                    
                                    
                                    $sql = "SELECT type,amount FROM data_stats "
                                            . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id='".$ww['id'] 
                                            . "' AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' "
                                            . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
                                            . ' AND profile_id = ' . $ww['ProfileId'];
                                    
                                    $statsqq = function_mysql_query($sql,__FILE__);
                                    
                                    while ($statsww = mysql_fetch_assoc($statsqq)) {
                                        $stats = 1;
                                    }
                                    
                                    $displayForex = 0;
                                    
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
                                        
                                        $displayForex = 1;
                                    }
                                    
                                    
                                    if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {
                                        if (
                                            (int) $stats <= 0 && (int) $totalLeads <= 0 && (int) $totalDemo <= 0 && 
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
                                    <td><a href="/manager/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
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
                                    ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                    
                                    '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                    '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                    <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                    <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                    <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                    
                                    <td style="text-align: center;">' . price($totalBonus) . '</td>
                                    <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                    <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                    <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                    <td style="text-align: center;">' . price($totalVolume) . '</td>
                                    '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                    '.(($displayForex && $showPNL) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
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
                                <td><a href="/manager/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
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
                                ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                
                                '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                
                                <td style="text-align: center;">' . price($totalBonus) . '</td>
                                <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                <td style="text-align: center;">' . price($totalVolume) . '</td>
                                '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                '.(($displayForex && $showPNL) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                <td style="text-align: center;"><a href="/manager/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
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
                            <td style="display: none;">'.lang('Groups').'</td>
                            <td>'.lang('Merchant').'</td>
                            <td>'.lang('Show Affiliates').'</td>
                            <td>'.lang('Search Type').'</td>'
                            . ($intMerchantCount > 1 ? '<td>' . lang('Group Merchants') . '</td> ': '') .
                            '<td></td>
				</tr><tr>
				<td>'.timeFrame($from,$to).'</td>
				<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="alert_date" style="width: 100px;" /></td>
                                <td width="100" style="display: none;"><select name="group_id" style="width: 100px;"><option value="">'.lang('All Groups').'</option><option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listGroups($group_id).'</select></td>
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
                                                ($hideDemoAndLeads ? '' : '<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">' . $totalLeadsAccountsM . '</a></th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">' . $totalDemoAccountsM . '</a></th>') .
						
						'<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">' . $totalRealAccountsM . '</a></th>
						'.($showCasinoFields ? '<th>' . $totalFrozensM . '</th>' : '<th>|FROZEN|</th>').'
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTDM.'</a></th>
						
						<th>' . price($totalFTDAmountM) . '</th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">' . $totalRealFtdM . '</a></th>
						
						<th>' . price($totalRealFtdAmountM) . '</th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">' . $totalDepositsM . '</a></th>
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
            
            
            case "affiliate":
                $set->pageTitle   = lang('Affiliate Report');
                $showLeadsAndDemo = false;
                $where            = '';
                $sql              = 'SELECT extraMemberParamName AS title FROM merchants';
		$campID           = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		
                
                $sql = "SELECT COUNT(id) AS count FROM merchants WHERE valid = 1;";
                $arrMerchantCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                $intMerchantCount = $arrMerchantCount['count'];
                
                
                $group_id = $set->userInfo['group_id'];
                
                if (isset($group_id) && $group_id != '') {
                    $where .= ' AND group_id = ' . $group_id . ' ';
                }
                
                
                if (isset($groupByAff) && $groupByAff == 1) {
                    $groupMerchantsPerAffiliate = 1;
                } else {
                    $groupMerchantsPerAffiliate = 0;
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
                    
                    $sql = "SELECT affiliates.*, acr.campID FROM affiliates "
                        . "LEFT JOIN affiliates_campaigns_relations acr ON affiliates.id = acr.affiliateID "
                        . "WHERE valid = 1 " . $where . " "
                        . "ORDER BY affiliates.id DESC;";
                    
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
                        
                        $sql = 'SELECT id,name,type,producttype,rev_formula, wallet_id  FROM merchants WHERE valid = 1 ' 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        
                        $merchantqq = function_mysql_query($sql,__FILE__);
                        
                        while ($merchantww = mysql_fetch_assoc($merchantqq)) {
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
                                $sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                                 . "WHERE affiliate_id = '" . $regww['affiliate_id'] . "' "
                                 . "AND merchant_id = '" . $regww['merchant_id'] . "' AND dealType = 'tier' "
                                 . "ORDER BY id DESC "
                                 . "LIMIT 0, 1;";
                            
                                $strAffDealType   = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                                $boolTierCplCount = !is_null($strAffDealType['tier_type']) && 'cpl_count' == $strAffDealType['tier_type'];
                                
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

                                        $totalCom += getCommission($arrRange['from'], $arrRange['to'], 0, (isset($group_id) && $group_id != '' ? $group_id : -1), $arrDealTypeDefaults, $arrTmp);
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
                                                'groupId'             => (isset($group_id) && $group_id != '' ? $group_id : -1),
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
                                $arrFtds = getTotalFtds($arrRange['from'], $arrRange['to'], $ww['id'], $merchantww['id'], $merchantww['wallet_id']);
                                
                                foreach ($arrFtds as $arrFtd) {
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
                                // $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
								$netRevenue =  round(getRevenue("",$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
                            }
                            
                            
                                    $sql = "SELECT *,tb1.type as data_sales_type FROM data_sales as tb1 "
                                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo' "
                                            . "WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND tb1.affiliate_id = '".$ww['id']."' "
                                            . "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";
                                    
                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    $volume = 0;
                                    
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                                        if ($salesww['data_sales_type'] == 'deposit') {
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
												'amount'  => $salesww['amount'],
												
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
                                    
                                    
                                    $sql = "SELECT type,amount FROM data_stats "
                                            . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id='".$ww['id'] 
                                            . "' AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";
                                    
                                    $statsqq = function_mysql_query($sql,__FILE__);
                                    
                                    while ($statsww = mysql_fetch_assoc($statsqq)) {
                                        $stats = 1;
                                    }
                                    
                                    $displayForex = 0;
                                    
                                    if(strtolower($merchantww['producttype']) == 'forex') {
                                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                                . 'WHERE affiliate_id = "' . $ww['id'] 
                                                . '" GROUP BY affiliate_id';
                                        
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                            $spreadAmount  = $ts['totalSpread'];
                                            $volume       += $ts['totalTO'];
                                            $pnl           = $ts['totalPnl'];
                                        }
                                        
                                        $displayForex = 1;
                                    }
                                    
                                    
                                    if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {
                                        if (
                                            (int) $stats <= 0 && (int) $totalLeads <= 0 && (int) $totalDemo <= 0 && 
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
                                    <td>'.$ww['id'].'</td>
                                    <td><a href="/manager/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
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
                                    ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                    
                                    '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                    '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                    <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                    <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                    <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                    
                                    <td style="text-align: center;">' . price($totalBonus) . '</td>
                                    <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                    <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                    <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                    <td style="text-align: center;">' . price($totalVolume) . '</td>
                                    '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                    '.(($displayForex && $showPNL) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                    <td style="text-align: center;"><a href="/manager/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
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
                                <td>'.$ww['id'].'</td>
                                <td><a href="/manager/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
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
                                ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                
                                '<td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                <td style="text-align: center;"><a href="/manager/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                
                                <td style="text-align: center;">' . price($totalBonus) . '</td>
                                <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                <td style="text-align: center;">' . price($totalVolume) . '</td>
                                '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                '.(($displayForex && $showPNL) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                <td style="text-align: center;"><a href="/manager/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
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
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="affiliate" />
			<input type="hidden" name="search" value="1" />
			<table><tr>
                            <td>'.lang('Period').'</td>
                            <td>'.lang('Affiliate ID').'</td>
                            <td style="display: none;">'.lang('Groups').'</td>
                            <td>'.lang('Merchant').'</td>
                            <td>'.lang('Show Affiliates').'</td>
                            <td>'.lang('Search Type').'</td>'
                            . ($intMerchantCount > 1 ? '<td>' . lang('Group Merchants') . '</td> ': '') .
                            '<td></td>
				</tr><tr>
				<td>'.timeFrame($from,$to).'</td>
				<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="alert_date" style="width: 100px;" /></td>
                                <td width="100" style="display: none;"><select name="group_id" style="width: 100px;"><option value="">'.lang('All Groups').'</option><option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listGroups($group_id).'</select></td>
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
						'.($campID['title'] ? '<th></th>' : '') .'
                                                <th></th>
						<th></th>'. ($groupMerchantsPerAffiliate == 0 ? '<th></th>' : '') . '
						<th>'.$totalImpressionsM.'</th>
						<th>'.$totalClicksM.'</th>
						<th>' . @number_format(($totalClicksM / $totalImpressionsM) * 100, 2) . ' %</th>
						<th>' . @number_format(($totalRealAccountsM / $totalClicksM) * 100, 2) . ' %</th>
						<th>' . @number_format(($totalFTDM / $totalClicksM) * 100, 2) . ' %</th>
						<th>' . @price($totalComsM / $totalClicksM) . '</th>' . 
                                                ($hideDemoAndLeads ? '' : '<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">' . $totalLeadsAccountsM . '</a></th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">' . $totalDemoAccountsM . '</a></th>') .
						
						'<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">' . $totalRealAccountsM . '</a></th>
						'.($showCasinoFields ? '<th>' . $totalFrozensM . '</th>' : '<th>|FROZEN|</th>').'
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTDM.'</a></th>
						
						<th>' . price($totalFTDAmountM) . '</th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">' . $totalRealFtdM . '</a></th>
						
						<th>' . price($totalRealFtdAmountM) . '</th>
						<th><a href="/manager/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">' . $totalDepositsM . '</a></th>
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
	}
	
	
$fileName = "manager/csv/report.csv";
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