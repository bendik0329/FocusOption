<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

require_once('common/global.php');

if (!isAdmin()) 
	_goto('/admin/');

	
/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();

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
                    $needToSkipMerchant = $arrWallets[$ww['wallet_id']];
                    
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
                            
                            while ($regww = mysql_fetch_assoc($regqq)) {
                                    if ($regww['type'] == "lead") $totalLeads++;
                                    if ($regww['type'] == "demo") $totalDemo++;
                                    if ($regww['type'] == "real") {
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
                                        $totalReal++;
                                    }
				}
				
                                $strSql = "SELECT type, amount FROM data_sales "
                                        . "WHERE merchant_id = '" . $ww['id'] . "' AND rdate "
                                        . $searchInSql . " "
                                        . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                                
				$salesqq = function_mysql_query($strSql,__FILE__);
				
				while ($salesww = mysql_fetch_assoc($salesqq)) {
                                    if ($salesww['type'] == 'deposit') {
                                        $sumDeposits += $salesww['amount'];
                                        $depositingAccounts++;
                                    }
                                    
                                    if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
                                    if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
                                    if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
                                    if ($salesww['type'] == "volume") $volume += $salesww['volume'];
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
                                    $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
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
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>'
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
			  'str' => '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>'
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
					<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
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
		$set->pageTitle = lang('Traffic Report');
		
		if ($merchant_id) $where = " AND id='".$merchant_id."'";
		if ($profile_id) $whereSites = " AND profile_id='".$profile_id."'";
		if ($banner_id) $whereSites .= " AND banner_id='".$banner_id."'";
		if ($affiliate_id) $whereSites .= " AND affiliate_id='".$affiliate_id."'";
		$sql = "SELECT * FROM merchants WHERE valid='1' ".$where." ORDER BY pos";
		$qq=function_mysql_query($sql,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			
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
			
			for ($i=0;$i<=$diff;$i++) {
				unset($totalCom);
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
					$searchInSql = "BETWEEN '".$from."' AND '".$to."'";
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
				$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." merchant_id='".$ww['id']."' ".$whereSites." AND rdate ".$searchInSql."",__FILE__));
				
				$regqq = function_mysql_query("SELECT * FROM data_reg WHERE merchant_id = '" . $merchantID . "' and ".$globalWhere." rdate ".$searchInSql." ".$whereSites,__FILE__);
                                
				while ($regww = mysql_fetch_assoc($regqq)) {
                                    if ($regww['type'] == "lead") $totalLeads++;
                                    if ($regww['type'] == "demo") $totalDemo++;
                                    if ($regww['type'] == "real") {
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
                                        $totalReal++;
                                    }
                                }
				
				$ftdUsers = '';
				
				$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT trader_id, tb1.rdate,amount,affiliate_id
				FROM data_sales AS tb1
				WHERE tb1.merchant_id = '" . $merchantID . "' and ".$globalWhere." rdate ".$searchInSql." AND type=1 ".$whereSites." AND trader_id NOT IN 
					(SELECT trader_id FROM data_sales LEFT JOIN merchants ON data_sales.merchant_id=merchants.id WHERE ".(!$set->multiMerchants ? " merchant_id='".$ww['id']."' AND " : " merchants.wallet_id='".$ww['wallet_id']."' AND ")." data_sales.rdate < tb1.rdate AND data_sales.type=1 GROUP BY trader_id) 
				GROUP BY trader_id",__FILE__);
				
				while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
					getFtdByDealType($ww['id'], $totalftd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
					$totalCom += getCom($totalftd['affiliate_id'], $ww['id'], $totalftd['trader_id'], $from, $to, 'deal', 1, 0, 0, 0, -1, $arrDealTypeDefaults);
				}
				
				$impression = @number_format($totalTraffic['totalViews'],0);
				$clicks = @number_format($totalTraffic['totalClicks'],0);
				$salesqq=function_mysql_query("SELECT type,amount FROM data_sales WHERE  merchant_id=" . $merchantID. " and " .$globalWhere." rdate ".$searchInSql." ".$whereSites,__FILE__);
								
				
				$qqry20_notFTD = "
				SELECT affiliate_id, merchant_id, trader_id FROM data_stats tb1 WHERE tb1.merchant_id = '" . $merchantID . "' and  ".str_replace('affiliate_id','tb1.affiliate_id',$globalWhere)." tb1.rdate ".$searchInSql."  ".$whereSites." AND tb1.market_id='0' ".($ftdUsers ? "AND tb1.trader_id NOT IN (".$ftdUsers.") " : "" )." GROUP BY trader_id
				";

				$notFtd_amountqq=function_mysql_query($qqry20_notFTD,__FILE__) OR die(mysql_error());
					
				while ($totalftd=mysql_fetch_assoc($notFtd_amountqq)) {
					// OLD VERSION
					//$totalCom += getCom($totalftd['affiliate_id'],$ww['id'],$totalftd['trader_id'],$from,$to,'deal',1,0,1);
					
					$totalCom += getCom($totalftd['affiliate_id'], $ww['id'], $totalftd['trader_id'], $from, $to, 'deal', 1, 0, 1, 0, -1, $arrDealTypeDefaults);
				}
				
				//die ("SELECT type,amount FROM data_sales WHERE  merchant_id=" . $merchantID . " and " .$globalWhere." rdate ".$searchInSql." ".$whereSites);
				//$salesqq=function_mysql_query("SELECT type,amount FROM data_sales_".$merchantName." WHERE ".$globalWhere." rdate ".$searchInSql." ".$whereSites,__FILE__);
				while ($salesww=mysql_fetch_assoc($salesqq)) {
					
					if ($salesww['type'] == 'deposit') {
						$sumDeposits += $salesww['amount'];
						$depositingAccounts++;
						}
					if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
					if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
					if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
					if ($salesww['type'] == "volume") $volume += $salesww['volume'];
					}
				
				
				
				if(strtolower($ww['producttype'])=='forex'){
					$traderStatsQ = function_mysql_query('SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats WHERE merchant_id="'.$merchantID.'" AND '.$globalWhere.' rdate '.$searchInSql,__FILE__); //OR die(mysql_error());
					while($ts = mysql_fetch_assoc($traderStatsQ)){
						$spreadAmount = $ts['totalSpread'];
						$volume += $ts['totalTO'];
						$pnl = $ts['totalPnl'];
					}
				}
				
				$mww = mysql_fetch_assoc(function_mysql_query('SELECT * FROM merchants WHERE id='.$merchantID,__FILE__));
				
				$revWhere = ' WHERE merchant_id='.$merchantID.' '.$whereSites.' AND rdate BETWEEN "'.$from.'" AND "'.$to.'" ';
				$netRevenue = getRevenue($revWhere,$mww['producttype'],$sumDeposits,$bonus,$withdrawal,$pnl,$volume,$spreadAmount,$formula);
				
				
				
				$filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showFrom));
				$filterTo = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showTo));
				
				
				
				$boxaName = "admin-traffic-report-1";
		
				$tableArr = Array(
						
					(object) array(
					  'id' => 'daily',
					  'str' => ($display_type == "daily" ? '<td style="text-align: center;">'.date("Y/m/d H:i:s",strtotime($from.' +'.$i.' Day')).'</td>' : '')
					),
					(object) array(
					  'id' => 'weekly',
					  'str' => ($display_type == "weekly" ? '<td style="text-align: center;">'.date("Y/m/d H:i:s",strtotime($showFrom)).' - '.date("Y/m/d H:i:s", strtotime($showTo)).'</td>' : '')
					),
					(object) array(
					  'id' => 'monthly',
					  'str' => ($display_type == "monthly" ? '<td style="text-align: center;">'.date("Y/m/d H:i:s",strtotime($showFrom)).' - '.date("Y/m/d H:i:s", strtotime($showTo)).'</td>' : '')
					),
					(object) array(
					  'id' => 'name',
					  'str' => '<td style="text-align: left;">'.$ww['name'].'</td>'
					),
					(object) array(
					  'id' => 'impression',
					  'str' => '<td style="text-align: center;">'.$impression.'</td>'
					),
					(object) array(
					  'id' => 'clicks',
					  'str' => '<td style="text-align: center;">'.$clicks.'</td>'
					),
					(object) array(
					  'id' => 'CTR',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'Click_to_Account',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'Click_to_Sale',
					  'str' => '<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'EPC',
					  'str' => '<td style="text-align: center;">'.@price($totalCom/$totalTraffic['totalClicks']).'</td>'
					),
					(object) array(
					  'id' => 'total_leads',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>'
					),
					(object) array(
					  'id' => 'total_demo',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&merchant_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>'
					),
					(object) array(
					  'id' => 'total_real',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&merchant_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'Deposits',
					  'str' => '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>'
					),
					(object) array(
					  'id' => 'ftdAmount',
					  'str' => '<td style="text-align: center;">'.price($ftd_amount).'</td>'
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
				
				$listReport .= '<tr>
						'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '',1).'
					</tr>';
				
				$totalImpressions += $impression;
				$totalClicks += $clicks;
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
			}
		}
		
		
		$tableArr = Array(
				
			(object) array(
			  'id' => 'daily',
			  'str' => Array(
					($display_type ? '<th>'.lang('Period').'</th>' : ''),
					($display_type ? '<th></th>' : '')
				)
			),
			(object) array(
			  'id' => 'weekly',
			  'str' => Array(
					($display_type ? '<th>'.lang('Period').'</th>' : ''),
					($display_type ? '<th></th>' : '')
				)
			),
			(object) array(
			  'id' => 'monthly',
			  'str' => Array(
					($display_type ? '<th>'.lang('Period').'</th>' : ''),
					($display_type ? '<th></th>' : '')
				)
			),
			(object) array(
			  'id' => 'name',
			  'str' => Array(
					'<th style="text-align: left;">'.lang('Merchant').'</th>',
					'<th style="text-align: left;"><b>'.lang('Total').':</b></th>'
				)
			),
			(object) array(
			  'id' => 'impression',
			  'str' => Array(
					'<th>'.lang('Impressions').'</th>',
					'<th>'.$totalImpressions.'</th>'
				)
			),
			(object) array(
			  'id' => 'clicks',
			  'str' => Array(
					'<th>'.lang('Clicks').'</th>',
					'<th>'.$totalClicks.'</th>'
				)
			),
			(object) array(
			  'id' => 'CTR',
			  'str' => Array(
					'<th>'.lang('Click Through Ratio (CTR)').'</th>',
					'<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>'
				)
			),
			(object) array(
			  'id' => 'Click_to_Account',
			  'str' => array(
					'<th>'.lang(ptitle('Click to Account')).'</th>',
					'<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>'
				)
			),
			(object) array(
			  'id' => 'Click_to_Sale',
			  'str' => array(
					'<th>'.lang(ptitle('Click to Sale')).'</th>',
					'<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>'
				)
			),
			(object) array(
			  'id' => 'EPC',
			  'str' => array(
					'<th>EPC</th>',
					'<th>'.@price($totalComs/$totalClicks).'</th>'
				)
			),
			(object) array(
			  'id' => 'total_leads',
			  'str' => array(
					'<th>'.lang(ptitle('Lead')).'</th>',
					'<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>'
				)
			),
			(object) array(
			  'id' => 'total_demo',
			  'str' => array(
					'<th>'.lang(ptitle('Demo')).'</th>',
					'<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>'
				)
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => array(
					'<th>'.lang(ptitle('Accounts')).'</th>',
					'<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>'
				)
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => array(
					'<th>'.lang('FTD').'</th>',
					'<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>'
				)
			),
			(object) array(
			  'id' => 'Deposits',
			  'str' => array(
					'<th>'.lang('Deposits').'</th>',
					'<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>'
				)
			),
			(object) array(
			  'id' => 'ftdAmount',
			  'str' => array(
					'<th>'.lang('FTD Amount').'</th>',
					'<th>'.price($totalFTDAmount).'</th>'
				)
			),
			(object) array(
			  'id' => 'sumDeposits',
			  'str' => array(
					'<th>'.lang('Deposits Amount').'</th>',
					'<th>'.price($totalDepositAmount).'</th>'
				)
			),
			(object) array(
			  'id' => 'volume',
			  'str' => array(
					'<th>'.lang('Volume').'</th>',
					'<th>'.price($totalVolume).'</th>'
				)
			),
			(object) array(
			  'id' => 'bonus',
			  'str' => array(
					'<th>'.lang('Bonus Amount').'</th>',
					'<th>'.price($totalBonusAmount).'</th>'
				)
			),
			(object) array(
			  'id' => 'Withdrawal',
			  'str' => array(
					'<th>'.lang('Withdrawal Amount').'</th>',
					'<th>'.price($totalWithdrawalAmount).'</th>'
				)
			),
			(object) array(
			  'id' => 'ChargeBack',
			  'str' => array(
					'<th>'.lang('ChargeBack Amount').'</th>',
					'<th>'.price($totalChargeBackAmount).'</th>'
				)
			),
			(object) array(
			  'id' => 'NetRevenue',
			  'str' => array(
					'<th>'.lang(ptitle('Net Revenue')).'</th>',
					'<th>'.price($totalNetRevenue).'</th>'
				)
			),
			(object) array(
			  'id' => 'Commission',
			  'str' => array(
					'<th>'.lang('Commission').'</th>',
					'<th>'.price($totalComs).'</th>'
				)
			)				
		);

		
		$tableCells = setTable($tableArr, $boxaName, $set->userInfo['productType'], '',2);
		
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
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
					<td>'.lang('Search Type').'</td>
					<td>'.lang('Banner ID').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" style="width: 80px;" /></td>
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
					'.$tableCells[0].'
					<!--
						'.($display_type ? '<th>'.lang('Period').'</th>' : '').'
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
						<th>'.lang('Deposits').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>-->
					</tr></thead><tfoot><tr>
						'.$tableCells[1].'
					<!--
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
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th>'.price($totalFTDAmount).'</th>
						<th>'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th>'.price($totalBonusAmount).'</th>
						<th>'.price($totalWithdrawalAmount).'</th>
						<th>'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th>'.price($totalComs).'</th>
					-->
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr,'Traffic');
			
		$set->content.=$tableStr.'
			</div>'.getPager();
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
                    
                    while ($regww = mysql_fetch_assoc($regqq)) {
                        if ($regww['type'] == "lead") $totalLeads++;
                        if ($regww['type'] == "demo") $totalDemo++;
                        if ($regww['type'] == "real") {
                            $arrTmp = [
                                'merchant_id'  => $regww['merchant_id'],
                                'affiliate_id' => $regww['affiliate_id'],
                                'rdate'        => $regww['rdate'],
                                'banner_id'    => $regww['banner_id'],
                                'trader_id'    => $regww['trader_id'],
                                'profile_id'   => $regww['profile_id'],
                            ];
                            
                            $totalCom += getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
                            unset($arrTmp);
                            $totalReal++;
                        }
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
                        $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
                    }
                    
                    
                    $sql = "SELECT type, amount FROM data_sales "
                            . "WHERE merchant_id = '" . $merchantww['id'] . "' AND banner_id='".$bannerInfo['id'] 
                            . "' AND rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '));
		    
                    $salesqq = function_mysql_query($sql,__FILE__);
                    
                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                        if ($salesww['type'] == 1 || $salesww['type'] == 'deposit') {
                            $depositingAccounts++;
                            $sumDeposits += $salesww['amount'];
                        }
                        
                        if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
                        if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
                        if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
                        if ($salesww['type'] == "volume") $volume += $salesww['volume'];
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
                        $netRevenue = round($sumDeposits - ($bonus + $withdrawal + $chargeback));
                    }
                    
		    
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$bannerInfo['id'].'</td>
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="NewWin(\'/admin/creative.php?act=edit_banner&id='.$bannerInfo['id'].'\',\'editbanner_'.$bannerInfo['id'].'\',\'1000\',\'800\',\'1\');">'.lang('Edit').'</a></td>
				<td style="text-align: left;">'.($bannerInfo['id'] ? $bannerInfo['title'] : lang('BANNER REMOVED')).'</td>
				<td style="text-align: left;">'.$merchantww['name'].'</td>
				<td style="text-align: left;">'.$bannerInfo['type'].'</td>
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
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
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
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
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
			
           
		
	case "full":
		$set->pageTitle = lang(ptitle('Full Report'));
		$l=0;
		
		if ($affiliate_id) $where .= " AND reg1.affiliate_id='".$affiliate_id."'";
		if ($group_id) $where .= " AND reg1.group_id='".$group_id."'";
		if ($banner_id) $where .= " AND reg1.banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND reg1.profile_id='".$profile_id."'";
		if ($trader_id) $where .= " AND reg1.trader_id='".$trader_id."'";
		if ($country_id) $where .= " AND reg1.country='".$country_id."'";
		
		$whereSales = str_replace('reg1','sales1',$where);
		$whereSales2 = str_replace('reg1','sales2',$where);
		$whereStats = str_replace('reg1','stats1',$where);
		
		if ($merchant_id) {
			$ww = dbGet($merchant_id,"merchants");
			if (!$ww['id']) _goto();
			$brokers_ids[] = $ww['id'];  
			$brokers[] = $ww['name'];
		} else {
			$qq=function_mysql_query("SELECT id,name FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$brokers_ids[] = $ww['id'];
				$brokers[] = $ww['name'];
				}
		}
		
		if(isset($_REQUEST['steps'])){
			$steps = $_REQUEST['steps'];
		}else{
			$steps = 20;
		}
		
		if(isset($_REQUEST['currPage'])){
			$currPage = $_REQUEST['currPage']-1;
			if($currPage<0) 
				$currPage=0;
			$startFrom = $currPage*$steps;
		}else{
			$startFrom = 0;
		}
		
		$sql2 = 'SELECT * FROM data_reg reg1';
		$sql = '
		SELECT tbl.* FROM
			
			(SELECT 

			reg1.trader_id, 
			reg1.trader_alias, 
			reg1.rdate, 
			reg1.type, 
			countries.title AS country, 
			countries.code AS countryCode, 
			reg1.affiliate_id, 
			affiliates.username AS affiliate_un, 
			affiliates.id, 
			reg1.merchant_id AS merchant_id, 
			"TODO" AS merchant_name, 
			reg1.banner_id, 
			merchants_creative.title AS banner_title, 
			merchants_creative.type AS banner_type, 
			reg1.profile_id, 
			reg1.freeParam, 
			sales2.rdate AS ftd_date,
			sales2.amount AS ftd_amount,
			SUM(IF(sales1.type="deposit", 1, 0)) AS totalDeposits, 
			SUM(IF(sales1.type="deposit", sales1.amount, 0)) AS totalDepositsAmount,
			SUM(IF(sales1.type="volume", sales1.amount, 0)) AS totalVolumeAmount,
			SUM(IF(sales1.type="bonus", sales1.amount, 0)) AS totalBonusAmount,
			SUM(IF(sales1.type="withdrawal", sales1.amount, 0)) AS totalWithdrawalAmount,
			SUM(IF(sales1.type="chargeback", sales1.amount, 0)) AS totalChargebackAmount,

			ROUND(SUM(IF(sales1.type="deposit", sales1.amount, 0))-(SUM(IF(sales1.type="bonus", sales1.amount, 0))+SUM(IF(sales1.type="withdrawal", sales1.amount, 0))+SUM(IF(sales1.type="chargeback", sales1.amount, 0)))) AS netRevenue,

			SUM(IF(sales1.type="volume", 1, 0)) AS totalTrades,

			payments_details.reason,'.(!isset($_REQUEST['isAjax']) ? '
			
			(SELECT SUM(amount) FROM (SELECT * FROM (SELECT sales2.trader_id, sales2.rdate, sales2.amount FROM data_sales sales2 WHERE type="deposit" '.$whereSales2.' ORDER BY rdate ASC)t1 GROUP BY t1.trader_id)t2 WHERE rdate BETWEEN "'.$from.'" AND "'.$to.'") AS sumFtd,
			(SELECT COUNT(amount) FROM (SELECT sales2.trader_id, sales2.rdate, sales2.amount FROM data_sales sales2 WHERE type="deposit" '.$whereSales2.' ORDER BY rdate ASC)t2 WHERE rdate BETWEEN "'.$from.'" AND "'.$to.'") AS sumDeposits,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="deposit" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$whereSales2.') AS sumDepositsAmount,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="volume" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$whereSales2.') AS sumVolumeAmount,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="bonus" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$whereSales2.') AS sumBonusAmount,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="withdrawal" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$whereSales2.') AS sumWithdrawalAmount,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="chargeback" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$whereSales2.') AS sumChargebackAmount,
			(SELECT COUNT(amount) FROM data_sales sales2 WHERE type="volume" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$whereSales2.') AS sumTrades
			
			' : ' 
			
			0 AS sumFtd,
			0 AS sumDeposits,
			0 AS sumDepositsAmount,
			0 AS sumVolumeAmount,
			0 AS sumBonusAmount,
			0 AS sumWithdrawalAmount,
			0 AS sumChargebackAmount,
			0 AS sumTrades
			
			').'
			
			
			FROM 

			data_reg reg1
			LEFT JOIN data_sales sales1 ON reg1.trader_id=sales1.trader_id AND sales1.rdate BETWEEN "'.$from.'" AND "'.$to.'"
			LEFT JOIN merchants_creative ON reg1.banner_id=merchants_creative.id
			LEFT JOIN affiliates ON reg1.affiliate_id=affiliates.id

			LEFT JOIN (SELECT * FROM (SELECT sales2.trader_id, sales2.rdate, sales2.amount FROM data_sales sales2 WHERE type="deposit" '.$whereSales2.' ORDER BY rdate ASC)t1 GROUP BY t1.trader_id)sales2 ON sales1.trader_id=sales2.trader_id
			LEFT JOIN payments_details ON payments_details.trader_id=reg1.trader_id AND payments_details.merchant_id=1
			LEFT JOIN countries ON reg1.country=countries.code

			GROUP BY reg1.trader_id
			ORDER BY CAST(reg1.trader_id AS SIGNED) DESC
			
			)tbl 
			
			WHERE rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where.' '.$whereType1.'
			
			LIMIT '.$startFrom.','.$steps.'
			
			
		';
		
		//die($sql);
		
		
		$testCount = 0;
		$revCount = Array();
		for ($i=0; $i<=count($brokers)-1; $i++) {
			
			$broker['name'] = $brokers[$i];
			if ($type AND $type != "ftd" AND $type != "real") $where .= " AND type='".$type."'";
			if ($type == "ftd") {
				
				$sql = "
				SELECT DISTINCT *
				FROM data_sales AS tb1
				WHERE tb1.merchant_id = '" . $brokers_ids[$i]."' and ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' AND type=1 ".$where." AND trader_id NOT IN 
					(SELECT trader_id FROM data_sales WHERE merchant_id = '". $brokers_ids[$i] . "' and type=1 AND rdate < tb1.rdate ".$where." GROUP BY trader_id) 
				GROUP BY trader_id";
				
				$qq=function_mysql_query($sql,__FILE__);
								
			} else if ($type == "deposit") {
				$sql = "SELECT * FROM data_sales where merchant_id = '" . $brokers_ids[$i]."' ".$where." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ORDER BY trader_id DESC"; //  ".($trader_id ? '' : 'GROUP BY trader_id')."
				// die($sql);
				$qq=function_mysql_query($sql,__FILE__);
			} else if ($type == "revenue") {
				$sql = "SELECT * FROM data_sales where merchant_id = '" . $brokers_ids[$i]."' ".$where." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' AND type='revenue' GROUP BY trader_id ORDER BY trader_id DESC";
				$qq=function_mysql_query($sql,__FILE__);
			} else {
				$sql = "SELECT * FROM data_reg WHERE merchant_id = '" . $brokers_ids[$i] . "' ".$where."  ".$whereReg." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC";
				$qq=function_mysql_query($sql,__FILE__);
				// die("SELECT * FROM data_reg WHERE merchant_id = '" . $brokers_ids[$i] . "' ".$where." AND rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC");
			}
			
			// die("SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC");
			
			while ($ww=mysql_fetch_assoc($qq)) {
				
				unset($marketInfo);
				$totalTraders = $total_deposits = $depositAmount = $ftdAmount = $volumeAmount = $bonusAmount = $withdrawalAmount = $chargebackAmount = $totalTraders = $revenueAmount = 0;
				$merchantInfo = dbGet($ww['merchant_id'],"merchants");
				// Get Trader Info Because he's FTD
				if ($type == "ftd" || $type == "deposit" || $type == "real" || $type == "revenue") {
					$traderInfo = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate,trader_alias,type,trader_id FROM data_reg WHERE ".(!$set->multiMerchants ? " merchant_id = '" . $brokers_ids[$i] . "' and " : "")." trader_id='".$ww['trader_id']."'",__FILE__));

					$ww['trader_alias'] = $traderInfo['trader_alias'];
					if ($type != "deposit") $ww['rdate'] = $traderInfo['rdate'];
					$ww['orgType'] = $ww['type'];
					$ww['type'] = $traderInfo['type'];
					
					
				}
				
				$bannerInfo = dbGet($ww['banner_id'],"merchants_creative");
				
				if ($ww['market_id'] > 0) {
					$marketInfo = dbGet($ww['market_id'],"market_items");
				}
				
				$firstDeposit = mysql_fetch_assoc(function_mysql_query(
					"SELECT id,rdate,amount,trader_id FROM data_sales 
					WHERE merchant_id = '". $brokers_ids[$i] . "' and trader_id='".$ww['trader_id']."' AND type='deposit' 
						AND rdate BETWEEN '".$from."' AND '".$to."' 
					ORDER BY rdate ASC LIMIT 1",__FILE__
				));
				
				
				if ($type == "ftd" AND !$firstDeposit['id']) {
					continue;
				}
				
				$ftdAmount = $firstDeposit['amount'];
				
				
				if ($type != "ftd" AND $type != "deposit" AND $type != "revenue") {
					$strSql = "SELECT type,amount FROM data_sales where merchant_id = '" . $brokers_ids[$i]."' and ".$globalWhere." trader_id='".$ww['trader_id']."' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY rdate ASC";
					
					$amountqq = function_mysql_query($strSql,__FILE__);
					//echo "SELECT type,amount FROM data_sales where merchant_id = '" . $brokers_ids[$i]."' and ".$globalWhere." trader_id='".$ww['trader_id']."' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY rdate ASC<BR>union all<BR>";
					
					while ($amountww=mysql_fetch_assoc($amountqq)) {
						
						if ($amountww['type'] == "deposit") {
							$testCount++;
							$depositAmount += $amountww['amount'];
							$total_deposits++;
							
						} else if ($amountww['type'] == "bonus") 
							$bonusAmount += $amountww['amount'];
						else if ($amountww['type'] == "withdrawal") 
							$withdrawalAmount += $amountww['amount'];
						else if ($amountww['type'] == "chargeback") 
							$chargebackAmount += $amountww['amount'];
						else if ($amountww['type'] == "volume") {
								$volumeAmount += $amountww['amount'];
								$totalTraders++;
						}
					}
				}else{
					
					//echo $ww['type'].' -------- '.$ww['amount'].'<BR>';
					if ($ww['orgType'] == "deposit") {
						
						$depositAmount = $ww['amount'];
						$total_deposits++;
					} else if ($ww['type'] == "bonus") 
						$bonusAmount = $ww['amount'];
					else if ($ww['type'] == "withdrawal") 
						$withdrawalAmount = $ww['amount'];
					else if ($ww['type'] == "chargeback") 
						$chargebackAmount = $ww['amount'];
					else if ($ww['type'] == "volume") {
							$volumeAmount = $ww['amount'];
							$totalTraders++;
					}
					
				}
				$affInfo = dbGet($ww['affiliate_id'],"affiliates");
				if ($ww['type'] == "real") $color = 'green';
					else if ($ww['type'] == "demo") $color = 'red';
					else if ($ww['type'] == "lead") $color = 'black';
				
				
				if($set->userInfo['productType']!='Casino'){
					$netRevenue = round($depositAmount-($bonusAmount+$withdrawalAmount+$chargebackAmount));
				}else{
					$netRevenue = getRevenue('WHERE merchant_id='.$brokers_ids[$i].' AND trader_id='.$ww['trader_id'].' AND rdate BETWEEN "'.$from.'" AND "'.$to.'"');				
				}
				
				
				// OLD VERSION
				//$totalCom = getCom($ww['affiliate_id'],$brokers_ids[$i],$ww['trader_id'],$from,$to,'deal',1);
				
				$totalCom = getCom($ww['affiliate_id'], $brokers_ids[$i], $ww['trader_id'], $from, $to, 'deal', 1, 0, 0, 0, -1, $arrDealTypeDefaults);

				$chkTrader = mysql_fetch_assoc(function_mysql_query("SELECT * FROM payments_details WHERE trader_id='".$ww['trader_id']."' AND merchant_id='".$brokers_ids[$i]."'",__FILE__));
				$listReport .= '<tr>
						<td>'.$ww['trader_id'].'</td>
						<td>'.$ww['trader_alias'].'</td>
						<td>'.($traderInfo ? ($type == "deposit" ? date("Y/m/d H:i:s", strtotime($traderInfo['rdate'])) : date("Y/m/d H:i:s", strtotime($ww['rdate']))) : '').'</td>
						<td><span style="color: '.$color.';">'.$ww['type'].'</span></td>
						<td>'.longCountry($ww['country']).'</td>
						<td>'.$ww['affiliate_id'].'</td>
						<td><a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td style="text-align: left;">'.$bannerInfo['id'].'</td>
						<td style="text-align: left;">'.$bannerInfo['title'].'</td>
						<td>'.$bannerInfo['type'].'</td>
						<td>'.$ww['profile_id'].'</td>
						<td>'.$ww['freeParam'].'</td>
						<td>'.($type == "deposit" ? date("Y/m/d H:i:s", strtotime($ww['rdate'])) : ($firstDeposit['id'] ? date("Y/m/d H:i:s", strtotime($firstDeposit['rdate'])) : '')).'</td>
						<td>'.price($ftdAmount).'</td>
						<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime("-3 Years")).'&to='.date("Y/m/d").'&merchant_id='.$merchant_id.'&trader_id='.$ww['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
						<td>'.price($depositAmount).'</td>
						<td>'.price($volumeAmount).'</td>
						<td>'.price($bonusAmount).'</td>
						<td>'.price($withdrawalAmount).'</td>
						<td>'.price($chargebackAmount).'</td>
				        <td><a href="/admin/reports.php?act=stats&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&trader_id='.$ww['trader_id'].'">'.price($netRevenue).'</a></td>
						<td>'.$totalTraders.'</td>
						<td>'.price($totalCom).'</td>
						<td>'.$chkTrader['reason'].'</td>
					</tr>';
					
					if (@!in_array($firstDeposit['trader_id'],$ftdExist)) $totalFTD += $ftdAmount;
					$ftdExist[] = $firstDeposit['trader_id'];
					$totalTotalDeposit += $total_deposits;
					$totalDepositAmount += $depositAmount;
					$totalVolumeAmount += $volumeAmount;
					if($brokersType[$i]=='forex')
						$totalSpreadAmount += $spreadAmount;
					$totalBonusAmount += $bonusAmount;
					$totalWithdrawalAmount += $withdrawalAmount;
					$totalChargeBackAmount += $chargebackAmount;
					if(!in_array($ww['trader_id'],$revCount)){
						$totalNetRevenue += $netRevenue;
						array_push($revCount,$ww['trader_id']);
					}
					$totalTrades += $totalTraders;
					$totalTotalCom += $totalCom;
					
				$l++;
				}
			}
		
                        
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
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
						<td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td>
							<select name="type" style="width: 100px;">
								<option value="real" '.($type == "real" ? 'selected' : '').'>'.lang(ptitle('Accounts')).'</option>
								<option value="lead" '.($type == "lead" ? 'selected' : '').'>'.lang(ptitle('Lead')).'</option>
								<option value="demo" '.($type == "demo" ? 'selected' : '').'>'.lang(ptitle('Demo')).'</option>
								<option value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option>
								<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>
								<option value="revenue" '.($type == "revenue" ? 'selected' : '').'>'.lang('Revenue').'</option>
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
				$tableStr='<table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).'</th>
						<th>'.lang(ptitle('Trader Alias')).'</th>
						<th title="mm/dd/yyyy">'.lang('Registration Date').'</th>
						<th>'.lang(ptitle('Trader Status')).'</th>
						<th>'.lang('Country').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Affiliate Username').'</th>
						<th>'.lang('Merchant ID').'</th>
						<th>'.lang('Merchant Name').'</th>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th>'.lang('Type').'</th>
						<th>'.lang('Profile ID').'</th>
						<th>'.lang('Param').'</th>
						<th title="mm/dd/yyyy">'.lang(($type == "deposit" ? 'Deposit Date' : 'First Deposit')).'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang(($type == "deposit" ? 'Deposit Amount' : 'Deposits Amount')).'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus  Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang(ptitle('Trades')).'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Admin Notes').'</th>
					</tr></thead>
					<tfoot>
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
						<th style="text-align: left;">'.price($totalTotalCom).'</th>
						<th></th>
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr,'Trader');
			
		$set->content.=$tableStr.'
			</div>'.getPager();
		
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
                    $int_merchant_id = empty($intTmpMerchantId) ? $arrMerchant['id'] : $intTmpMerchantId;
                    $where = ' AND merchant_id = ' . $int_merchant_id;
                    
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($group_id) $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
                    
						if ($trader_alias) {
						$qry = "select trader_id from data_reg  where  lower(trader_alias) like '".$trader_alias."'";
						
						$row= mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
						if (!empty($row['trader_id'])) {
							$trader_id = $row['trader_id'];
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
						if (empty($trader_id))
							$trader_id = $row['trader_id'];
						}
					}
					
                    if ($country_id) $where .= " AND country='".$country_id."' ";
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
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
                            
                            $sql = "SELECT ds.* FROM data_sales AS ds
                                    INNER JOIN data_reg AS dr ON dr.trader_id = ds.trader_id AND dr.merchant_id = ds.merchant_id AND dr.status <> 'frozen' 
                                    WHERE " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'deposit' "
                                            . $where
                                    . " ORDER BY ds.rdate ASC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrDeposit = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrDeposit;
                                unset($arrDeposit);
                            }
                            
                        } elseif ($type == 'revenue') {
                            $where = str_replace('merchant_id', 'ds.merchant_id', $where);
                            $where = str_replace('trader_id', 'ds.trader_id', $where);
                            $where = str_replace('group_id', 'ds.group_id', $where);
                            $where = str_replace('affiliate_id', 'ds.affiliate_id', $where);
							$where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            
                            $sql = "SELECT ds.* FROM data_sales AS ds
                                    INNER JOIN data_reg AS dr ON dr.trader_id = ds.trader_id AND dr.merchant_id = ds.merchant_id AND dr.status <> 'frozen' 
                                    WHERE " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'revenue' "
                                            . $where
                                    . " GROUP BY ds.trader_id "
                                    . " ORDER BY ds.rdate ASC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrRevenue = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrRevenue;
                                unset($arrRevenue);
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
                            
                        } else {
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status <> 'frozen' "
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
                    $sql = 'SELECT * FROM merchants WHERE id = ' . $arrRes['merchant_id'] . ' AND valid = 1 LIMIT 0, 1;';
                    $arrMerchant = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                    
                    $ftdAmount = empty($arrRes['ftdAmount']) ? $ftdAmount : $arrRes['ftdAmount'];
                    $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    //$totalCom = empty($arrRes['totalCom']) ? $totalCom : $arrRes['totalCom'];
                    $totalCom = 0;
                    $int_merchant_id = $arrRes['merchant_id'];
                    
                    if (
                        $type == 'ftd' || 
                        $type == 'totalftd' || 
                        $type == 'deposit' || 
                        $type == 'revenue'
                    ) {
                        $strTmpWhere = '';
                        $intTmpTraderId = empty($trader_id) ? $arrRes['trader_id'] : $trader_id;  
                        $intTmpGroupId = empty($group_id) ? $arrRes['group_id'] : $group_id;
                        $intTmpMerchantId = empty($int_merchant_id) ? $arrRes['merchant_id'] : $int_merchant_id;
                        $strTmpWhere .= empty($intTmpTraderId) ? '' : ' AND trader_id = ' . $intTmpTraderId . ' ';
                        $strTmpWhere .= empty($intTmpGroupId) ? '' : ' AND group_id = ' . $intTmpGroupId . ' ';
                        $strTmpWhere .= empty($intTmpMerchantId) ? '' : ' AND merchant_id = ' . $intTmpMerchantId . ' ';

                        $sql = "SELECT * FROM data_reg WHERE 1 = 1 AND status <> 'frozen' " . $strTmpWhere . " LIMIT 0, 1;";

                        $traderInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                        $arrRes['trader_alias'] = $traderInfo['trader_alias'];
                        $arrRes['rdate']        = $type != 'deposit' ? $traderInfo['rdate'] : $arrRes['rdate'];
                        $arrRes['orgType']      = $arrRes['type'];
                        $arrRes['salesType']    = $arrRes['type'];
                        $arrRes['type']         = $traderInfo['type'];
                        $arrRes['banner_id']    = $traderInfo['banner_id'];
                        $arrRes['status']       = $traderInfo['status'];
                        $arrRes['profile_id']   = $traderInfo['profile_id'];
                        $arrRes['freeParam']    = $traderInfo['freeParam'];
                        unset($intTmpTraderId, $intTmpGroupId, $strTmpWhere, $intTmpMerchantId);
                    }
                    
                    
                    if ($type != 'ftd' && $type != 'totalftd') {
                        $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                        $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                        $spreadAmount = $pnl = 0;
                        $ftdUsers = '';

                        $arrTotalFtds = getTotalFtds(
                            $from, $to, $arrRes['affiliate_id'], $arrRes['merchant_id'], $arrMerchant['wallet_id'], 
                            $arrRes['group_id'], $arrRes['banner_id'], $arrRes['profile_id'], '', $arrRes['trader_id']
                        );

                        foreach ($arrTotalFtds as $arrResLocal) {
                            $beforeNewFTD = $ftd;
                            getFtdByDealType($arrRes['merchant_id'], $arrResLocal, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);

                            if ($beforeNewFTD != $ftd) {
                                $firstDeposit = $arrResLocal;
                                $ftdAmount = $arrResLocal['amount'];
                                $arrResLocal['isFTD'] = true;
                                $totalCom = getCommission($from, $to, 0, $arrResLocal['group_id'], $arrDealTypeDefaults, $arrResLocal);
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
                                $arrMerchant['producttype']
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
                        $netRevenue = round($depositsAmount - ($withdrawalAmount + $bonusAmount + $chargebackAmount), 2);
                    }
                    
                    
                    // BANNER info retrieval.
                    $bannerInfo = ['id' => '', 'type' => '', 'title' => '',];
                    if (isset($arrRes['banner_id']) && !empty($arrRes['banner_id'])) {
                        $sql = "SELECT * FROM merchants_creative "
                                . "WHERE 1 = 1 AND id = " . $arrRes['banner_id'] . " "
                                . "LIMIT 0, 1;";

                        $bannerInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                    }
                    
                    if ($type != 'deposit') {
                        $sql = "SELECT ds.* FROM data_sales AS ds
                                INNER JOIN data_reg AS dr ON dr.trader_id = ds.trader_id AND dr.merchant_id = ds.merchant_id AND dr.status <> 'frozen' 
                                WHERE " . $globalWhere . " ds.trader_id = " . $arrRes['trader_id'] 
                                        . " AND ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                        . " AND ds.merchant_id = " . $int_merchant_id
                                . " ORDER BY ds.rdate ASC;";

                        $resource = function_mysql_query($sql,__FILE__);
                        $total_deposits = 0;

                        while ($arrAmount = mysql_fetch_assoc($resource)) {
                            $arrRes['tranz_id'] = $arrAmount['tranz_id'];
                            
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
                            }
                            unset($arrAmount);
                        }
                        
                    } else {

                        if (
                            $type == 'ftd' || 
                            $type == 'totalftd' || 
                            $type == 'deposit' || 
                            $type == 'revenue'
                        ) {
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
                            }
                        }
                    }
                    
                    
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


                    // Check trader.
                    $sql = 'SELECT * FROM payments_details '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrMerchant['id'] 
                            . '  LIMIT 0, 1;';

                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));


                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].'</td>
                            <td>'.$arrRes['trader_alias'].'</td>
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
                            <td>'.$arrRes['profile_id'].'</td>
                            <td>'.$arrRes['status'].'</td>
                            <td>'.$arrRes['freeParam'].'</td>
                            <td>' . (isset($arrRes['tranz_id']) ? $arrRes['tranz_id'] : '') . '</td>
                            <td>'.($type == "deposit" ? date("d/m/Y", strtotime($arrRes['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y", strtotime($firstDeposit['rdate'])) : '')).'</td>
                            <td>'.price($ftdAmount).'</td>
                            <td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
                            <td>'.price($depositAmount).'</td>
                            <td>'.price($volumeAmount).'</td>
                            <td>'.price($bonusAmount).'</td>
                            <td>'.price($withdrawalAmount).'</td>
                            <td>'.price($chargebackAmount).'</td>
                            <td>'.price($netRevenue).'</td>
                            <td>'.$totalTraders.'</td>
                            <td>'.(isset($arrRes['salesType']) ? $arrRes['type'] : $arrRes['salesType']).'</td>
                            <td>'.price($totalCom).'</td>
                            <td>'.$chkTrader['reason'].'</td>
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
						<td>'.lang('Group ID').'</td>
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
								<option value="real" '.($type == "real" ? 'selected' : '').'>'.lang(ptitle('Accounts')).'</option>
								<option value="lead" '.($type == "lead" ? 'selected' : '').'>'.lang(ptitle('Lead')).'</option>
								<option value="demo" '.($type == "demo" ? 'selected' : '').'>'.lang(ptitle('Demo')).'</option>
								<option value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option>
								<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>
								<option value="revenue" '.($type == "revenue" ? 'selected' : '').'>'.lang('Revenue').'</option>
                                                                <option value="totalftd" '.($type == "totalftd" ? 'selected' : '').'>'.lang('Total FTD').'</option>
                                                                <option value="frozen" '.($type == "frozen" ? 'selected' : '').'>'.lang('Frozen').'</option>
							</select>
						</td>
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
				'.($set->export ? '<div class="exportCSV"><a href="'.$set->basepage.'?act=trader_xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2600px;">'.lang('Report Results').'</div>
			<div style="background: #F8F8F8;">
				<table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).'</th>
						<th>'.lang(ptitle('Trader Alias')).'</th>
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
						<th>'.lang('Profile ID').'</th>
						<th>'.lang('Status').'</th>
						<th>'.lang('Param').'</th>
                        <th>' . lang('Transaction ID') . '</th>
						<th>'.lang(($type == "deposit" ? 'Deposit Date' : 'First Deposit')).'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang(($type == "deposit" ? 'Deposit Amount' : 'Deposits Amount')).'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus  Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang(ptitle('Trades')).'</th>
						<th>'.lang('Sale Status').'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Admin Notes').'</th>
					</tr></thead>
					<tfoot>
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
				</table>
			</div>'.getPager();
		
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
				WHERE ds.merchant_id = '" . $brokers_ids[$i]."' and ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ".$where;//." GROUP BY ds.trader_id";
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
						<td><a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$ww['affiliate_id'].'</a></td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td style="text-align: left;"><a href ="/admin/reports.php?act=banner&banner_id='.$ww['banner_id'].'" target="_blank">'.$ww['banner_id'].'</a></td>
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
						<td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
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
		
	
        

            case "affiliate":
		$set->pageTitle   = lang('Affiliate Report');
                $showLeadsAndDemo = false;
                $where            = '';
                $sql              = 'SELECT extraMemberParamName AS title FROM merchants';// WHERE id = ' . aesDec($_COOKIE['mid']);
		$campID           = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		
                
                $sql = "SELECT COUNT(id) AS count FROM merchants WHERE valid = 1;";
                $arrMerchantCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                $intMerchantCount = $arrMerchantCount['count'];
                
                
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
                            $needToSkipMerchant  = $arrWallets[$merchantww['wallet_id']];
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
                            
                            while ($regww = mysql_fetch_assoc($regqq)) {
                                if ($regww['type'] == 'lead') $totalLeads++;
                                if ($regww['type'] == 'demo') $totalDemo++;
                                if ($regww['type'] == 'real') {
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
                                    $totalReal++;
                                }
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
                                $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
                            }
                            
                            
                                    $sql = "SELECT type,amount FROM data_sales "
                                            . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id='".$ww['id']."' "
                                            . "AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";
                                    
                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    $volume = 0;
                                    
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                                        if ($salesww['type'] == 'deposit') {
                                            $depositingAccounts++;
                                            $sumDeposits += $salesww['amount'];
                                        }
                                        
                                        if ($salesww['type'] == 'bonus') $bonus += $salesww['amount'];
                                        if ($salesww['type'] == 'revenue') $revenue += $salesww['amount'];
                                        if ($salesww['type'] == 'withdrawal') $withdrawal += $salesww['amount'];
                                        if ($salesww['type'] == 'chargeback') $chargeback += $salesww['amount'];
                                        if ($salesww['type'] == 'volume') $volume += $salesww['amount'];
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
                                    ($showLeadsAndDemo ? '' : '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                    
                                    '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                    '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                    <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                    <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                    <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
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
                                ($showLeadsAndDemo ? '' : '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                
                                '<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
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
						<th>'.lang('Website').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>' . 
                                                ($showLeadsAndDemo ? '' : '<th>'.lang(ptitle('Lead')).'</th>
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
                                                ($showLeadsAndDemo ? '' : '<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">' . $totalLeadsAccountsM . '</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">' . $totalDemoAccountsM . '</a></th>') .
						
						'<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">' . $totalRealAccountsM . '</a></th>
						'.($showCasinoFields ? '<th>' . $totalFrozensM . '</th>' : '<th>|FROZEN|</th>').'
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTDM.'</a></th>
						
						<th>' . price($totalFTDAmountM) . '</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">' . $totalRealFtdM . '</a></th>
						
						<th>' . price($totalRealFtdAmountM) . '</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">' . $totalDepositsM . '</a></th>
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
		$sql = "SELECT id, title FROM groups WHERE valid = 1 ORDER BY id DESC;";
		$qq = function_mysql_query($sql,__FILE__);
                
                while ($ww = mysql_fetch_assoc($qq)) {
                    $arrGroups[] = $ww;
                    unset($ww);
                }
                
                foreach ($arrGroups as $ww) {
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
                    
                    $merchantqq = function_mysql_query("SELECT id,name,producttype,rev_formula,wallet_id FROM merchants WHERE valid='1'",__FILE__);
                    
                    while ($merchantww = mysql_fetch_assoc($merchantqq)) {
			    // Check if this is a first itaration on given wallet.
                            $needToSkipMerchant = $arrWallets[$merchantww['wallet_id']];
                            
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
                                    
                                    while ($regww = mysql_fetch_assoc($regqq)) {
                                        if ($regww['type'] == "lead") $totalLeads++;
                                        if ($regww['type'] == "demo") $totalDemo++;
                                        if ($regww['type'] == "real") {
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
                                            $totalReal++;
                                        }
                                    }
				    
                                    
                                    $ftdUsers = '';
                                    
                                    if (!$needToSkipMerchant) {
                                        // IMPORTANT in case of "per-group" calculation.
                                        // DO NOT modify even a single character at the following line!!!
                                        $intTmpGroupId = 0 == $ww['id'] ? ' 0 ' : $ww['id'];
                                        $arrFtds = getTotalFtds($arrTmpTime['from'], $arrTmpTime['to'], 0, $merchantww['id'], $merchantww['wallet_id'], $intTmpGroupId);
                                        unset($intTmpGroupId);
                                        
                                        foreach ($arrFtds as $arrFtd) {
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
                                        
                                        if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
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
                                            
                                        } else {
                                            $netRevenuePerTimePeriod += round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
                                            $netRevenue              += round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
                                        }
                                        unset($arrAff);
                                    }
                                    
                                    if (isset($display_type) && !empty($display_type)) {
                                        $netRevenue = $netRevenuePerTimePeriod;
                                    }
                                    
                                    
                                    $sql = "SELECT type,amount,affiliate_id,trader_id FROM data_sales "
                                            . "WHERE merchant_id = '" . $merchantID . "' "
                                            . "and group_id='".($ww['id'] ? $ww['id'] : '0')."' AND rdate ".$searchInSql."";

                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
                                        if ($salesww['type'] == "deposit") {
                                            $sumDeposits += $salesww['amount'];
                                            $depositingAccounts++;
                                        }
                                        
                                        if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
                                        if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
                                        if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
                                        if ($salesww['type'] == "volume") $volume += $salesww['amount'];
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
                                            <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=deposit">'.$depositingAccounts.'</a></td>
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
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
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
		$set->pageTitle = lang('Profile Report');
                
                /**
                 * In the "manager" report, 
                 * following line MUST be replaced with an explicit assignment of group-id. 
                 */
                $group_id = isset($group_id) && $group_id != '' ? $group_id : null;
                        
                $where = ' 1 = 1 ';
                $limit = '';
		$sql = 'SELECT extraMemberParamName AS title FROM merchants WHERE id='.aesDec($_COOKIE['mid']);
		$campID = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		
                if (is_numeric($affiliate_id)) {
                    $where .= " AND aff.id = '" . $affiliate_id . "' ";
                } elseif ($affiliate_id) {
                    $where .= " AND lower(username) LIKE '%" . trim(strtolower($affiliate_id)) . "%' ";
                }
                
                if (!is_null($group_id)) {
                    $where .= " AND aff.group_id = '" . $group_id . "' ";
                }
                
                $sql = "SELECT aff_profiles.id AS ProfileId, aff_profiles.name AS ProfileName, aff_profiles.url AS URL, aff.* FROM affiliates_profiles AS aff_profiles 
                        LEFT JOIN  affiliates AS aff ON aff_profiles.affiliate_id = aff.id AND aff.valid = '1'  
                        LEFT JOIN affiliates_campaigns_relations acr ON aff.id = acr.affiliateID AND aff.valid = '1' 
                        WHERE " . $where . "
                        ORDER BY aff.id DESC " . $limit;
                
                $qq                 = function_mysql_query($sql,__FILE__);
                $l                  = 0;
                $showCasinoFields   = 0;
                $totalRealFtd       = 0;
                $totalRealFtdAmount = 0;
                
                while ($ww = mysql_fetch_assoc($qq)) {
                        $totalImpressionsM   = 0;
                        $totalClicksM        = 0;
                        $totalLeadsAccountsM = 0;
                        $totalDemoAccountsM  = 0;
                        $totalRealAccountsM  = 0;
                        $totalFTDM           = 0;
                        $totalDepositsM      = 0;
                        $totalFTDAmountM     = 0;
                        $totalDepositAmountM = 0;
                        $totalVolumeM        = 0;
                        $totalBonusM         = 0;
                        $totalWithdrawalM    = 0;
                        $totalChargeBackM    = 0;
                        $totalNetRevenueM    = 0;
                        $totalComsM          = 0;
                        $netRevenueM         = 0;
                        $totalFruadM         = 0;
                        $totalFrozensM       = 0;
                        $totalRealFtdM       = 0;
                        $totalRealFtdAmountM = 0;
                        
                        $sql = 'SELECT COUNT(id) AS count FROM merchants '
                             . 'WHERE valid = 1 ' . (isset($merchant_id) && $merchant_id != '' ? ' AND id = ' . $merchant_id . ' ' : '');
                        
                        $arrMerCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                        $intMerCount = $arrMerCount['count'];
                        unset($arrMerCount, $sql);
                        
                        $sql = 'SELECT * FROM merchants WHERE valid = 1 ' . (isset($merchant_id) && $merchant_id != '' ? ' AND id = ' . $merchant_id . ' ' : '');
                        $merchantqq = function_mysql_query($sql,__FILE__);
                        
                        while ($merchantww = mysql_fetch_assoc($merchantqq)) {
                                if (strtolower($merchantww['producttype']) == 'casino') {
                                        $showCasinoFields = 1;
                                }
                                
                                $merchantww['count'] = $intMerCount;
                                $formula = $merchantww['rev_formula'];
                                $totalTraffic=0;
                                $totalLeads=0;
                                $totalDemo=0;
                                $totalReal=0;
                                $ftd=0;
                                $volume=0;
                                $bonus=0;
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

                                $totalTraffic = getClicksAndImpressions(
                                    $from, 
                                    $to, 
                                    (isset($merchantww['id']) && $merchantww['id'] != '' ? $merchantww['id'] : null), 
                                    $ww['id'], 
                                    (isset($group_id) && $group_id != '' ? $group_id : null),
                                    $ww['ProfileId']
                                );
                                
                                $merchantName = strtolower($merchantww['name']);

                                $sql = 'SELECT SUM(IF(status="frozen",1,0)) AS total FROM data_reg WHERE affiliate_id="'.$ww['id'].'" 
                                                AND '.$globalWhere.' merchant_id="'.$merchantww['id'].'" AND rdate BETWEEN "'.$from.'" AND "'.$to.'"';

                                $frozensQ = function_mysql_query($sql,__FILE__); //OR die(mysql_error());
                                $frozens  = mysql_fetch_assoc($frozensQ);

                                if ($frozens['total']) {
                                        $frozens = $frozens['total'];
                                } else {
                                        $frozens = 0;
                                }

                                $regqq=function_mysql_query("SELECT * FROM data_reg WHERE merchant_id = '" . $merchantww['id'] . "' and ".$globalWhere." affiliate_id='".$ww['id']."' AND type=3 AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);

                                while ($regww = mysql_fetch_assoc($regqq)) {
                                    if ($regww['type'] == 'lead') $totalLeads++;
                                    if ($regww['type'] == 'demo') $totalDemo++;
                                    if ($regww['type'] == 'real') {
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
                                        $totalReal++;
                                    }
                                }

                                $ftdUsers = '';

                                $strQuery = 
                                "SELECT DISTINCT tb1.trader_id, tb1.rdate,tb1.amount, tb1.affiliate_id FROM data_sales AS tb1																			
                                WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND " . $globalWhere . " tb1.affiliate_id = '" . $ww['id'] . "' 
                                                                                                                                        AND tb1.rdate BETWEEN '" . $from . "' AND '" . $to . "' 
                                                                                                                                        AND tb1.type = 'deposit' 
                                                                                                                                        AND tb1.trader_id NOT IN 
                                        (SELECT trader_id FROM data_sales 
                                        LEFT JOIN merchants ON data_sales.merchant_id = merchants.id 
                                        WHERE " . (!$set->multiMerchants ? " merchant_id = '" . $merchantww['id'] . "' AND " : " merchants.wallet_id = '" . $merchantww['wallet_id'] . "' AND ") 
                                                        . " data_sales.rdate < tb1.rdate AND data_sales.type = 1 AND data_sales.affiliate_id = '" . $ww['id'] . "' 
                                        GROUP BY trader_id) 
                                GROUP BY trader_id";



                                $query_ftd_amountqq = function_mysql_query($strQuery,__FILE__);

                                while ($totalftd = mysql_fetch_assoc($query_ftd_amountqq)) {
                                    getFtdByDealType($merchantww['id'], $totalftd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
                                    $totalCom += getCom($ww['id'], $merchantww['id'], $totalftd['trader_id'], $from, $to, 'deal', 1, 0, 0, 0, -1, $arrDealTypeDefaults);
                                }

                                $real_ftd_amountqq = function_mysql_query("
                                        SELECT DISTINCT trader_id, tb1.rdate,amount
                                        FROM data_sales AS tb1
                                        WHERE tb1.merchant_id = '" . $merchantww['id']."' and ".$globalWhere." affiliate_id='".$ww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' AND trader_id NOT IN 
                                                (SELECT trader_id FROM data_sales WHERE merchant_id = '" . $merchantww['id'] . "' and affiliate_id='".$ww['id']."' AND type='deposit'  AND rdate < tb1.rdate ".(!$set->multiMerchants ? " AND merchant_id='".$merchantww['id']."'" : "")." GROUP BY trader_id) 
                                        GROUP BY trader_id",__FILE__);



                                while ($totalftd=mysql_fetch_assoc($real_ftd_amountqq)) {
                                    $real_ftd++;
                                    $real_ftd_amount+= $totalftd['amount'];
                                }

                                $qqry20_notFTD = "
                                SELECT affiliate_id, merchant_id, trader_id FROM data_stats tb1 
                                WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND ".str_replace('affiliate_id', 'tb1.affiliate_id', $globalWhere) . " tb1.affiliate_id = '" . $ww['id'] . "' 
                                        AND tb1.rdate " . $searchInSql . " AND tb1.market_id = '0' " . ($ftdUsers ? "AND tb1.trader_id NOT IN (" . $ftdUsers . ") " : "" ) . " 
                                GROUP BY trader_id";

                                $notFtd_amountqq = function_mysql_query($qqry20_notFTD,__FILE__) OR die(mysql_error());

                                while ($totalftd = mysql_fetch_assoc($notFtd_amountqq)) {
                                        $totalCom += getCom($ww['id'], $merchantww['id'], $totalftd['trader_id'], $from, $to, 'deal', 1, 0, 1, 0, -1, $arrDealTypeDefaults);
                                }


                                $salesqq = function_mysql_query("SELECT type,amount FROM data_sales WHERE merchant_id = '" . $merchantww['id'] . "' and ".$globalWhere." affiliate_id='".$ww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
                                $volume = 0;

                                while ($salesww=mysql_fetch_assoc($salesqq)) {
                                        if ($salesww['type'] == 'deposit') {
                                                $depositingAccounts++;
                                                $sumDeposits += $salesww['amount'];
                                                }
                                        if ($salesww['type'] == 'bonus') $bonus += $salesww['amount'];
                                        if ($salesww['type'] == 'revenue') $revenue += $salesww['amount'];
                                        if ($salesww['type'] == 'withdrawal') $withdrawal += $salesww['amount'];
                                        if ($salesww['type'] == 'chargeback') $chargeback += $salesww['amount'];
                                        if ($salesww['type'] == 'volume') $volume += $salesww['amount'];
                                }


                                $statsqq=function_mysql_query("SELECT type,amount FROM data_stats WHERE merchant_id = '" . $merchantww['id'] . "' and ".$globalWhere." affiliate_id='".$ww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);

                                while ($statsww=mysql_fetch_assoc($statsqq)) {
                                        $stats=1;
                                }

                                $displayForex = 0;
                                //$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
                                if(strtolower($merchantww['producttype'])=='forex'){
                                        $traderStatsQ = function_mysql_query('SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats WHERE affiliate_id="'.$ww['id'].'" GROUP BY affiliate_id',__FILE__);
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                                $spreadAmount = $ts['totalSpread'];
                                                $volume += $ts['totalTO'];
                                                $pnl = $ts['totalPnl'];
                                        }
                                        $displayForex = 1;
                                }


                                $mwwQ = function_mysql_query('SELECT * FROM merchants WHERE id='.$merchantww['id'],__FILE__); //OR die(mysql_error());
                                $mww = mysql_fetch_assoc($mwwQ);


                                //if(strtolower($mww['producttype'])=='sportsbetting' OR strtolower($mww['producttype'])=='casino'){

                                $netRevenue = getRevenue(
                                        ' WHERE '.($merchantww['id'] ? 'merchant_id='.$merchantww['id'].' AND ' : '').
                                        ' rdate BETWEEN "'.$from.'" AND "'.$to.'" AND affiliate_id='.$ww['id'],
                                        $mww['producttype'],
                                        $sumDeposits,
                                        $bonus,
                                        $withdrawal,
                                        $pnl,
                                        $turnoverAmount,
                                        $spreadAmount,
                                        $formula
                                );

                                $netRevenueM += $netRevenue; 

                                if ($stats<=0 AND $totalLeads <= 0 AND $totalDemo <= 0 AND $totalReal <= 0 AND $ftd <= 0 AND $totalTraffic['impressions'] <= 0 AND $totalTraffic['clicks'] <= 0) 
                                        continue; // Skip is affiliate data empty

                                $totalFruad   = mysql_num_rows(function_mysql_query("SELECT id FROM payments_details WHERE status='canceled' AND affiliate_id='".$ww['id']."'",__FILE__));
                                $totalFruadM += $totalFruad;


                                if($groupByAff=='0'){
                                        $listReport .= '<tr>
                                                        <td>'.$ww['id'].'</td>
                                                        <td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
                                                        <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';

                                                        if($campID['title']){
                                                                if($ww['campID']){
                                                                        $listReport .= '<td align="left">'.$ww['campID'].'</td>';

                                                                        //echo 'SELECT extraMemberParamName AS title FROM merchants WHERE id='.aesDec($_COOKIE['mid']);
                                                                        //die();
                                                                }else{
                                                                        $listReport .= '<td align="left"></td>';
                                                                }	
                                                        }


                                        $listReport .= '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
                                                        <td style="text-align: center;">'.$merchantww['name'].'</td>
                                                        <td><a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>
                                                        <td style="text-align: center;">'.@number_format($totalTraffic['impressions'],0).'</td>
                                                        <td style="text-align: center;">'.@number_format($totalTraffic['clicks'],0).'</td>
                                                        <td style="text-align: center;">'.@number_format(($totalTraffic['clicks']/$totalTraffic['impressions'])*100,2).' %</td>
                                                        <td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['clicks'])*100,2).' %</td>
                                                        <td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['clicks'])*100,2).' %</td>
                                                        <td style="text-align: center;">'.@price($totalCom/$totalTraffic['clicks']).'</td>
                                                        <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>
                                                        <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>
                                                        <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>
                                                        '.($showCasinoFields ? '<td>'.$frozens.'</td>' : '<td>|FROZEN|</td>').'
                                                        <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>
                                                        <td>'.price($ftd_amount).'</td>
                                                        <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=totalftd">'.$real_ftd.'</a></td>
                                                        <td>'.price($real_ftd_amount).'</td>
                                                        <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
                                                        <td>'.price($sumDeposits).'</td>

                                                        <td>'.price($bonus).'</td>
                                                        <td>'.price($withdrawal).'</td>
                                                        <td>'.price($chargeback).'</td>
                                                        <td>'.@number_format(($totalFruad/$ftd)*100,2).'%</td>
                                                        <td>'.price($volume).'</td>
                                                        '.((strtolower($merchantww['producttype'])=='forex') ? '<td>'.price($spreadAmount).'</td>' : '').'
                                                        '.((strtolower($merchantww['producttype'])=='forex') ? '<td>'.price($pnl).'</td>' : '').'
                                                        <td><a href="/admin/reports.php?act=stats&from='.date("Y/m/d H:i:s", strtotime($from)).'&to='.date("Y/m/d H:i:s", strtotime($to)).'&merchant_id='.$merchant_id.'">'.price($netRevenue).'</a></td>
                                                        <td>'.price($totalCom).'</td>
                                                        <td>'.listGroups($ww['group_id'],1).'</td>
                                                </tr>';
                                }


                                        $totalImpressions += $totalTraffic['impressions'];
                                        $totalClicks += $totalTraffic['clicks'];
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

                                        if(isset($_REQUEST['test'])){
                                                echo $totalNetRevenue.'<BR>';
                                        }

                                        $totalImpressionsM += $totalTraffic['impressions'];
                                        $totalClicksM += $totalTraffic['clicks'];
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
                                        $totalNetRevenueM += $netRevenueM;
                                        $totalComsM += $totalCom;
                                        $totalSpreadAmountM += $spreadAmount;
                                        $totalTurnoverAmountM += $turnoverAmount;
                                        $totalFrozensM += $frozens;
                                        $totalpnlM += $pnl;
                                        $totalRealFtdM += $real_ftd;
                                        $totalRealFtdAmountM += $real_ftd_amount;

                                        $l++;
                        }

                        if($groupByAff!='0'){
                                $listReport .= '<tr>
                                            <td>'.$ww['ProfileId'].'</td>
                                                        <td>'.$ww['ProfileName'].'</td>
                                                        <td>'.$ww['URL'].'</td>

                                                        <td>'.$ww['id'].'</td>
                                                        <td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
                                                        <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';

                                                        if($campID['title']){
                                                                if($ww['campID']){
                                                                        $listReport .= '<td align="left">'.$ww['campID'].'</td>';

                                                                        //echo 'SELECT extraMemberParamName AS title FROM merchants WHERE id='.aesDec($_COOKIE['mid']);
                                                                        //die();
                                                                }else{
                                                                        $listReport .= '<td align="left"></td>';
                                                                }	
                                                        }

                                $listReport .= '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
                                                <td><a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>
                                                <td style="text-align: center;">'.@number_format($totalImpressionsM,0).'</td>
                                                <td style="text-align: center;">'.@number_format($totalClicksM,0).'</td>
                                                <td style="text-align: center;">' . @number_format(($totalClicksM / $totalImpressionsM) * 100, 2) . ' %</td>
                                                <td style="text-align: center;">'.@number_format(($totalRealAccountsM/$totalClicksM)*100,2).' %</td>
                                                <td style="text-align: center;">'.@number_format(($totalFTDM/$totalClicksM)*100,2).' %</td>
                                                <td style="text-align: center;">' . @price($totalComsM / $totalClicksM) . '</td>
                                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=lead">'.$totalLeadsAccountsM.'</a></td>
                                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=demo">'.$totalDemoAccountsM.'</a></td>
                                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=real">'.$totalRealAccountsM.'</a></td>
                                                '.($showCasinoFields ? '<td style="text-align: center;">'.$totalFrozensM.'</td>' : '<td>|FROZEN|</td>').'
                                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=ftd">'.$totalFTDM.'</a></td>
                                                <td style="text-align: center;">'.price($totalFTDAmountM).'</td>
                                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=ftd">'.$totalRealFtdM.'</a></td>
                                                <td style="text-align: center;">'.price($totalRealFtdAmountM).'</td>
                                                <td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=deposit">'.$totalDepositsM.'</a></td>
                                                <td style="text-align: center;">'.price($totalDepositAmountM).'</td>

                                                <td style="text-align: center;">'.price($totalBonusM).'</td>
                                                <td style="text-align: center;">'.price($totalWithdrawalM).'</td>
                                                <td style="text-align: center;">'.price($totalChargeBackM).'</td>
                                                <td style="text-align: center;">'.@number_format(($totalFruadM/$totalFTDM)*100,2).'%</td>
                                                <td style="text-align: center;">'.price($totalVolumeM).'</td>
                                                '.(($displayForex) ? '<td style="text-align: center;">'.price($totalSpreadAmountM).'</td>' : '').'
                                                '.(($displayForex AND $showPNL) ? '<td style="text-align: center;">'.price($totalpnlM).'</td>' : '').'
                                                <td style="text-align: center;"><a href="/admin/reports.php?act=stats&from='.date("Y/m/d H:i:s", strtotime($from)).'&to='.date("Y/m/d H:i:s", strtotime($to)).'&merchant_id='.$merchant_id.'">'.price($netRevenueM).'</a></td>
                                                <td style="text-align: center;">'.price($totalComsM).'</td>
                                                <td style="text-align: center;">'.listGroups($ww['group_id'],1).'</td>
                                        </tr>';
                                }
                }
		
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
		$set->sortTable  = 1;
		$set->totalRows  = $l;
		$set->content   .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
		<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="profile" />
			<input type="hidden" name="search" value="1" />
			<table><tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td>'.lang('Group').'</td>
					<td>'.lang('Merchant').'</td>'
					. ($merchantww['count'] > 1 ? '<td>' . lang('Group Merchants') . '</td> ': '') .
					'<td></td>
				</tr><tr>
				<td>'.timeFrame($from,$to).'</td>
				<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="alert_date" style="width: 100px;" /></td>
				<td><select name="group_id" style="width: 150px;"><option value="">'.lang('All Groups').'</option>'.listGroups($group_id).'</select></td>
				<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>'
				
				. ($merchantww['count'] > 1 ? '<td><input type="checkbox" id="groupByAff" name="groupByAff" '.($groupByAff!='0' ? 'checked' : '').'/>
				<input type="hidden" id="groupByAffVal" value="'.($groupByAff).'" name="groupByAff" /></td>' : '') .
				
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
		
		<div class="normalTableTitle" style="width: 3000px;">'.lang('Profile Report').'</div>
			<div style="background: #F8F8F8;">';
				
				$tableStr='<table width="3000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang('Profile ID').'</th>
						<th>'.lang(ptitle('Profile Name')).'</th>
						<th>'.lang(ptitle('Profile URL')).'</th>
						
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Username').'</th>
						<th>'.lang('Full Name').'</th>
						'.($campID['title'] ? '<th>'.lang($campID['title']).'</th>' : '').'
						<th>'.lang('E-Mail').'</th>'. ($groupByAff=='0' ? '<th>'.lang('Merchant').'</th>':'').'
						<th>'.lang('Website').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
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
						'.(($displayForex AND $showPNL) ? '<th>'.lang('PNL').'</th>' : '').'
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Group').'</th>
					</tr></thead><tfoot><tr>
						<th><b>'.lang('Total').':</b></th>
						<th colspan="6"></th>
						<th></th>
						<th></th>'. ($groupByAff=='0' ? '<th></th>':'').'
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>' . @price($totalComs / $totalClicks) . '</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						'.($showCasinoFields ? '<th>'.$totalFrozens.'</th>' : '<th>|FROZEN|</th>').'
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						
						<th>'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						
						<th>'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th>'.price($totalDepositAmount).'</th>
						
						<th>'.price($totalBonus).'</th>
						<th>'.price($totalWithdrawal).'</th>
						<th>'.price($totalChargeBack).'</th>
						<th></th>
						<th>'.price($totalVolume).'</th>
						'.(($displayForex) ? '<th>'.price($totalSpreadAmount).'</th>' : '').'
						'.(($displayForex AND $showPNL) ? '<th>'.price($totalpnl).'</th>' : '').'
						<th>'.price($totalNetRevenue).'</th>
						<th>'.price($totalComs).'</th>
						<th></th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
				
				
			// die(print_r(array( $totalComs, $totalClicks )));
			
		
		if($showCasinoFields){
			$tableStr=str_replace('<td>|FROZEN|</td>','<td></td>',$tableStr);
			$tableStr=str_replace('<th>|FROZEN|</th>','<th></th>',$tableStr);
		}else{
			$tableStr=str_replace('<td>|FROZEN|</td>','',$tableStr);
			$tableStr=str_replace('<th>|FROZEN|</th>','',$tableStr);
		}
		$set->content.=$tableStr.'
			</div>'.getPager();
		
		excelExporter($tableStr,'Affiliate');
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