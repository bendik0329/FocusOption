<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$pageTitle = lang('Creative Report');
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

                $where = ' 1 = 1 ';
            
                
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
		
		
				/**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
				 if($userlevel == 'manager')
					 $group_id       = $set->userInfo['group_id'];
				 else
					$group_id  = null;
                
				$where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		
		
		// $ww['totalViews']=0;
		
		while ($ww = mysql_fetch_assoc($qq)) {
			 
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
                                
                                $totalCom += getCommission($from, $to, 0, (is_null($group_id) ? -1 : $group_id), $arrDealTypeDefaults, $arrTmp);
                            } else {
                                // TIER CPL.
                                if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
                                    $arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
                                } else {
                                    $arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
                                        'from'                => $from,
                                        'to'                  => $to,
                                        'onlyRevShare'        => 0,
                                        'groupId'             => (is_null($group_id) ? -1 : $group_id),
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
                            $totalCom += getCommission($from, $to, 0, (is_null($group_id) ? -1 : $group_id), $arrDealTypeDefaults, $arrFtd);
                        }
                        unset($arrFtd);
                    }
                    // echo $real_ftd_amount .'<br>';
                    
                    
                    $sql = "SELECT *, tb1.type AS data_sales_type  FROM data_sales as tb1 "
                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                            . "WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND tb1.banner_id='".$bannerInfo['id'] 
                            . "' AND tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id) ? '' : ' AND tb1.group_id = ' . $group_id . ' ')
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
					
					
					/* if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                        $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantww['id'], $ww['id'], $arrDealTypeDefaults);
                        $intTotalRevenue  = 0;
                        
                        foreach ($arrRevenueRanges as $arrRange2) {
                            $strRevWhere = 'WHERE merchant_id = ' . $merchantww['id'] . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                         . '" AND affiliate_id = "' . $ww['id'] . '" ' . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
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
                            $totalCom           += getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id) ? -1 : $group_id), $arrDealTypeDefaults, $row);
                            unset($arrRange2, $strRevWhere);
                        }
                        
                        $netRevenue += $intTotalRevenue;
                        
                    } else */ {
                        // $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
						$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$depositsAmount,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
                    }
					
                    
                    
                    if(strtolower($merchantww['producttype']) == 'forex') {
                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                . 'WHERE merchant_id="' . $merchantww['id'] . '" AND banner_id="'.$bannerInfo['id'] 
                                . '" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
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
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="NewWin(\'/'. $userlevel  .'/creative.php?act=edit_banner&id='.$bannerInfo['id'].'\',\'editbanner_'.$bannerInfo['id'].'\',\'1000\',\'800\',\'1\');">'.lang('Edit').'</a></td>
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
				<td><a href="/'. $userlevel  .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=lead">'.$totalLeads.'</a></td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=demo">'.$totalDemo.'</a></td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=real">'.$totalReal.'</a></td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=ftd">'.$ftd.'</a></td>
				<td>'.price($ftd_amount).'</td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=totalftd">'.$real_ftd.'</a></td>
				<td>'.price($real_ftd_amount).'</td>
				<td><a href="/'. $userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
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
			<form action="'.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
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
			
			<div class="normalTableTitle" class="table">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">';
		// width = 2400
				$tableStr='<table class="table tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr class="table-row">
						<th class="table-cell" style="text-align: left;">'.lang('Creative ID').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Actions').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Creative Name').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Language').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Width').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Height').'</th>
						<th class="table-cell">'.lang('Type').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Merchant').'</th>
						<th class="table-cell">'.lang('Impressions').'</th>
						<th class="table-cell">'.lang('Clicks').'</th>
						<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
						<th class="table-cell">'.lang(ptitle('Click to Account')).'</th>
						<th class="table-cell">'.lang(ptitle('Click to Sale')).'</th>
						<th class="table-cell">EPC</th>
						<th class="table-cell">'.lang(ptitle('Lead')).'</th>
						<th class="table-cell">'.lang(ptitle('Demo')).'</th>
						<th class="table-cell">'.lang(ptitle('Accounts')).'</th>
						<th class="table-cell">'.lang('FTD').'</th>
						<th class="table-cell">'.lang('FTD Amount').'</th>
						<th class="table-cell">'.lang('Total FTD').'</th>
						<th class="table-cell">'.lang('Total FTD Amount').'</th>
						<th class="table-cell">'.lang('Total Deposits').'</th>
						<th class="table-cell">'.lang('Deposits Amount').'</th>
						<th class="table-cell">'.lang('Volume').'</th>
						<th class="table-cell">'.lang('Bonus Amount').'</th>
						<th class="table-cell">'.lang('Withdrawal Amount').'</th>
						<th class="table-cell">'.lang('ChargeBack Amount').'</th>
						<th class="table-cell">'.lang(ptitle('Net Revenue')).'</th>
						<th class="table-cell">'.lang('Commission').'</th>
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
						<th><a href="/'. $userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/' . $userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/'.$userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
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
		
?>