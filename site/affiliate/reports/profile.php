<?php

if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLprefix."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/affiliate" );
}

		$globalWhere = " tb1.affiliate_id = " . $set->userInfo['id'] . " and ";

                $pageTitle      = lang('Profile Report');
				$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
			$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
			<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
			$filename = "Profiles_data_" . date('YmdHis');
		
                $isCasinoOrSportBets = false;
                $isAffiliateLogined  = strpos('__' . strtolower($_SERVER['REQUEST_URI']), '/affiliate/') > 0 ? true : false;
					
					
				$merchantIDs=ltrim($merchantIDs,',');

								
								
                $campID              = mysql_fetch_assoc(function_mysql_query('SELECT extraMemberParamName AS title FROM merchants WHERE id in (' . $merchantIDs . ')',__FILE__));
                $showLeadsAndDemo    = false;
                $where               = '';
                $listReport          = '';
                $sql                 = 'SELECT extraMemberParamName AS title FROM merchants';
		$campID              = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		
                
	$sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
                    $merchantsArray = array();
					$displayForex = 0;
					$mer_rsc = function_mysql_query($sql,__FILE__);
					while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
						
						if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1;
					
						$merchantsArray[$arrMerchant['id']] = $arrMerchant;
					}
			
                $sql = "SELECT COUNT(id) AS count FROM merchants WHERE valid = 1;";
                $arrMerchantCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                $intMerchantCount = $arrMerchantCount['count'];
                
                
                // BEGIN snippet only for AFFILIATE/reports/profile. 
                $sql        = 'SELECT group_id AS group_id FROM affiliates WHERE id = ' . $set->userInfo['id'];
                $arrGroupId = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                $group_id   = $arrGroupId['group_id'];
                unset($arrGroupId);
                
                if (isset($group_id) && $group_id != '') {
                    $where .= ' AND aff.group_id = ' . $group_id . ' ';
                }
                
                $affiliate_id = $set->userInfo['id'];
                // END.
                
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
                $totalCPIM = 0;
                $totalLeadsAccountsM = 0;
                $totalDemoAccountsM = 0;
                $totalRealAccountsM = 0;
                $totalFTDM = 0;
                $totalDepositsM = 0;
                $totalFTDAmountM = 0;
                $totalDepositAmountM = 0;
                $totalVolumeM = 0;
                $totalBonusM = 0;
 $totalLotsM = 0;
                $totalWithdrawalM = 0;
                $totalChargeBackM = 0;
                $totalNetRevenueM = 0;
                $totalComsM = 0;
                $netRevenueM = 0;
                $totalFruadM = 0;
                $totalFrozensM = 0;
                $totalRealFtdM = 0;
                $totalRealFtdAmountM = 0;
                $totalPNLAmountM = 0;
                
                $l = 0;
                $arrRanges = [];
                
$earliestTimeForNetRev = date('Y-m-d H:i:s');
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
                        $totalCPI = 0;
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
$totalLots = 0;
                        $totalSpreadAmount = 0;
                        $totalTurnoverAmount = 0;
                        $totalpnl = 0;
                        $totalFrozens = 0;
                        $totalRealFtd = 0;
                        $totalRealFtdAmount = 0;
                        $totalPNLAmount = 0;
                        
                        $sql = 'SELECT id,name,type,producttype,rev_formula, wallet_id  FROM merchants WHERE valid = 1 ' 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        
										
				$allbrabdrsc = function_mysql_query($sql,__FILE__);
				$LowestLevelDeal = 'ALL';
				while ($brandsRow = mysql_fetch_assoc($allbrabdrsc)) {
				foreach ($dealsArray as $dealItem=>$value) {
					if ($brandsRow['id']==$dealItem) {
						
						$LowestLevelDeal = getLowestLevelDeal($LowestLevelDeal, $value);
						break;
					}
					}
				}
			   $deal = $LowestLevelDeal;
		
		
		
						
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
                            
                            
                            if (
                                strtolower($merchantww['producttype']) == 'casino' || 
                                strtolower($merchantww['producttype']) == 'sportbook' || 
                                strtolower($merchantww['producttype']) == 'sportsbetting'
                            ) {
                                $isCasinoOrSportBets = true;
                                $showLeadsAndDemo    = true;
                            }
                            
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
                            $cpi=0;
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
                            $totalPNL = 0;
                            
                            
                            $totalTraffic                = [];
                            $arrClicksAndImpressions     = getClicksAndImpressions($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id'], null, $ww['ProfileId']);
                            $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                            
                            
                            $merchantName = strtolower($merchantww['name']);
                            $sql = 'SELECT SUM(IF(status="frozen",1,0)) AS total FROM data_reg '
                                    . 'WHERE affiliate_id="'.$ww['id'].'" AND merchant_id="'.$merchantww['id'] 
                                    . '" AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . '" '
                                    . ' AND profile_id = ' . $ww['ProfileId'];
                            
                            $frozensQ = function_mysql_query($sql,__FILE__);
                            $frozens  = mysql_fetch_assoc($frozensQ);
                            $frozens  = $frozens['total'] ? $frozens['total'] : 0;
                            
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id = " . $set->userInfo['id'] . " "
                                    . "AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' "
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
                                    0,
                                    0,
                                    $ww['ProfileId']
									
									
									
								,"",0,"",false , 1
                                );
                                
                                foreach ($arrFtds as $arrFtd) {
                                    $real_ftd++;
                                    $real_ftd_amount += $arrFtd['amount'];
                                    
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
                                    
                                    // if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
                                    if ($beforeNewFTD != $ftd ) {
                                        $arrFtd['isFTD'] = true;
                                        $totalCom += getCommission($arrRange['from'], $arrRange['to'], 0, -1, $arrDealTypeDefaults, $arrFtd);
                                    }
                                    unset($arrFtd);
                                }
                            }
                            
							
							
							
                                    $sql = "SELECT tb1.type as data_sales_type, tb1.* FROM data_sales as tb1 "
                                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                            . "WHERE tb1.merchant_id = '" . $merchantww['id'] . "' AND tb1.affiliate_id='".$ww['id']."' "
                                            . "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' "
                                            . ' AND tb1.profile_id = ' . $ww['ProfileId'];
                                    
                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    $volume = 0;
                                    
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
	if ($earliestTimeForNetRev>$salesww['rdate'])
											$earliestTimeForNetRev = $salesww['rdate'];
                                        if ($salesww['type'] == 'deposit') {
                                            $depositingAccounts++;
                                            $sumDeposits += $salesww['amount'];
                                        }
                                        
                                        if ($salesww['data_sales_type'] == 'bonus') $bonus += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'revenue') $revenue += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'withdrawal') $withdrawal += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'chargeback') $chargeback += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'volume') $volume += $salesww['amount'];
                                    }
									
									
                            /* if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                                $arrRevenueRanges = getRevenueDealTypeByRange($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id'], $arrDealTypeDefaults);
                                $intTotalRevenue  = 0;
                                
                                foreach ($arrRevenueRanges as $arrRange2) {
                                    $strWhere = 'WHERE merchant_id = ' . $merchantww['id'] 
                                              . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                              . '" AND affiliate_id = "' . $ww['id'] . '" AND profile_id = ' . $ww['ProfileId'];
                                    
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
                                        -1, 
                                        $arrDealTypeDefaults, 
                                        $row
                                    );
                                    
                                    unset($arrRange2, $strWhere);
                                }
                                
                                $netRevenue = $intTotalRevenue;
                                
                            } else */ {
                                // $netRevenue = round($sumDeposits - ($withdrawal + $bonus + $chargeback), 2);
								$netRevenue =  round(getRevenue("",$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
		if ($netRevenue>0) {
								$row                 = array();
									$row['merchant_id']  = $arrMerchant['id'];
									$row['affiliate_id'] = $set->userInfo['id'];
									$row['banner_id']    = 0;
								// $row['rdate']        = $from;
									$row['rdate']        = $earliestTimeForNetRev;
									$row['amount']       = $netRevenue;
									$row['isFTD']        = false;
									
									
								
									$totalCom            += getCommission($earliestTimeForNetRev, $to, 1, -1, $arrDealTypeDefaults, $row);
								
								}
                            }
                            
                            
                                    
									//forex + lots
                                          if ($displayForex ==1) {    
                              
							  
                                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(turnover) AS totalTO FROM data_stats '
                                                . 'WHERE affiliate_id = ' . $ww['id'] . ' AND profile_id = ' . $ww['ProfileId']
                                                . ' GROUP BY affiliate_id';
                                        
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                            $spreadAmount  = $ts['totalSpread'];
                                            $volume       += $ts['totalTO'];
                                            // $pnl           = $ts['totalPnl'];
                                        }
                                        
   		
							$totalLots  = 0;
											
							// var_dump($ww);
							// die();
							
						$sql = 'SELECT turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
                                         . 'WHERE affiliate_id = ' . $set->userInfo['id'] . ' and profile_id = '. $ww['ProfileId'].' and merchant_id = "' . $merchantww['id'] . '" AND rdate between "'. $from . '" and "' .$to . '"' 
                                            ;
											
											// die($sql);
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
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
													// echo 'com: ' . $a .'<br>';
													$totalCom += $a;
											// }
										
				     
								}
									
									
								}
                                    
                                    
                                    if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {
                                        if (
											(int)$totalCom <=0 &&
                                            (int) $stats <= 0 && (int) $totalLeads <= 0 && (int) $totalDemo <= 0 && 
                                            (int) $totalReal <= 0 && (int) $ftd <= 0 && 
                                            (int) $totalLots <= 0 &&
                                            (int) $totalTraffic['totalViews'] <= 0 && (int) $totalTraffic['totalClicks'] <= 0
                                        ) {
                                            continue;
                                        }
                                    }
                                    
									
									 if ($set->deal_pnl == 1 && allowView('af-pnl',$deal,'fields')) {
								
										$totalPNL  = 0;
										$dealsForAffiliate['pnl'] = 1;
											
										$pnlRecordArray=array();
									
										$pnlRecordArray['affiliate_id']  = $set->userInfo['id'];
										$pnlRecordArray['merchant_id']  = $merchantww['id'];
										$pnlRecordArray['group_id']  = $group_id;
										$pnlRecordArray['profile_id']  = $ww['ProfileId'];
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
													
													$tmpCom = getCommission($from, $to, 0, $ts['group_id'], $arrDealTypeDefaults, $row);
													// echo 'com: ' . $tmpCom.'<br>';
														
													$totalCom += $tmpCom;
												}
										}
								}
								
								
				if ($set->deal_cpi==1 && allowView('af-instl',$deal,'fields')){
						// installation
							
						$array = array();			
						$array['from']  	= 	$from ;
						$array['to'] = $to;
						$array['merchant_id'] = $merchantww['id'];
						$array['type'] = 'install' ;
						$array['affiliate_id']  = $set->userInfo['id'];
						$array['searchInSql']  = $searchInSql;
						$array['group_id'] = $group_id ;
						$array['profile_id']  = $ww['ProfileId'];
					
						
						$installs = generateInstallations($array);
						
						if (!empty($installs)){
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
                                        $totalCPI = $totalCPI;
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
                                        $totalPNLAmount = $totalPNL;
                                        
                                    } else {
                                        $totalImpressions += $totalTraffic['totalViews'];
                                        $totalClicks += $totalTraffic['totalClicks'];
                                        $totalLeadsAccounts += $totalLeads;
                                        $totalDemoAccounts += $totalDemo;
                                        $totalRealAccounts += $totalReal;
                                        $totalFTD += $ftd;
                                        $totalCPIGroup += $totalCPI;
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
                                        $totalPNLAmount += $totalPNL;
                                    }
                                    
                                    $totalImpressionsM += $totalTraffic['totalViews'];
                                    $totalClicksM += $totalTraffic['totalClicks'];
                                    $totalLeadsAccountsM += $totalLeads;
                                    $totalDemoAccountsM += $totalDemo;
                                    $totalRealAccountsM += $totalReal;
                                    $totalFTDM += $ftd;
                                    $totalCPIM += $totalCPI;
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
                                    $totalPNLAmountM += $totalPNL;
                                    
                                    
                            if ($groupMerchantsPerAffiliate == 0) {
                                $listReport .= '<tr>'
                                    . (isset($display_type) && !empty($display_type) ? '<td>' . $arrRange['from'] . ' - ' . $arrRange['to'] . '</td>' : '') . '
                                    <td>'.$ww['ProfileId'].'</td>
                                    <td>'.$ww['ProfileName'].'</td>
                                    <td>'.$ww['URL'].'</td>';
                                
                                if (!$isAffiliateLogined) {
                                    $listReport .='
                                        <td>'.$ww['id'].'</td>
                                        <td><a href="'.$set->SSLprefix.'affiliate/account.php" target="_blank">'.$ww['username'].'</a></td>
                                        <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';
                                    
                                    if ($campID['title']) {
                                        if ($ww['campID']) {
                                            $listReport .= '<td align="left">'.$ww['campID'].'</td>';
                                        } else {
                                            $listReport .= '<td align="left"></td>';
                                        }	
                                    }
                                    
                                    $listReport .= '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
                                                    <td><a href="'.$set->SSLprefix.'out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>';
                                }
                                
								
                                $listReport .=
											'
											'.(allowView('af-impr',$deal,'fields') ?'
											<td style="text-align: center;">'.@number_format($totalTraffic['totalViews'], 0).'</td>
											':'').'
											'.(allowView('af-clck',$deal,'fields') ?'
                                            <td style="text-align: center;">'.@number_format($totalTraffic['totalClicks'], 0).'</td>
											':'').'
											'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi?'
                                            <td style="text-align: center;">'.@number_format($totalCPI).'</td>
											':'').'
                                            <td style="text-align: center;">'.@number_format(($totalTraffic['totalClicks'] / $totalTraffic['totalViews'])*100,2).' %</td>
                                            <td style="text-align: center;">'.@number_format(($totalReal / $totalTraffic['totalClicks'])*100,2).' %</td>
                                            <td style="text-align: center;">'.@number_format(($ftd / $totalTraffic['totalClicks'])*100,2).' %</td>
                                            <td style="text-align: center;">'.@price($totalCom / $totalTraffic['totalClicks']).'</td>' . 
                                            (!$hideDemoAndLeads ? '
											'.(allowView('af-demo',$deal,'fields') ?
                                             '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>
											':'').'
											'.(allowView('af-lead',$deal,'fields') ?
                                               '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>'
                                            : '') 
                                            : '') .

											(allowView('af-real',$deal,'fields') ?	
                                            '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>
											':'').'
											'.($showCasinoFields && allowView('af-frzn',$deal,'fields') ?
                                            '<td>'.$frozens.'</td>' 
											: '')
                                            .(allowView('af-ftd',$deal,'fields') ?	'
                                            <td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>
											' : '' )
                                            
                                            .(allowView('af-ftda',$deal,'fields') ?
											 '<td>'.price($totalFTDAmount).'</td>' : '') . 
                                            (allowView('af-tftd',$deal,'fields') ?
                                             '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=totalftd">'.$real_ftd.'</a></td>' 
											 : '').'
                                            '.(allowView('af-tftd',$deal,'fields') ?
											 '<td>'.price($real_ftd_amount).'</td>' 
											 : '').'
                                            '.(allowView('af-depo',$deal,'fields') ?
                                            '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=deposit">'.$totalDeposits.'</a></td>' 
											 : '').'
                                            '.(allowView('af-depoam',$deal,'fields') ?
												'<td>'.price($sumDeposits).'</td>' : '') .'
                                            '.(allowView('af-bns',$deal,'fields') ? '<td>'.price($bonus).'</td>' : '') .'
                                            '.(allowView('af-withd',$deal,'fields') ? '<td>'.price($withdrawal).'</td>' : '') .'
                                            '.(allowView('af-chrgb',$deal,'fields') ? '<td>'.price($chargeback).'</td>' : '') .'
                                            '.(allowView('af-risk',$deal,'fields') ? 
                                              '<td>'.@number_format(($totalFruad / $ftd) * 100, 2).'%</td>'
                                             : '') . '
                                            '.(allowView('af-vlm',$deal,'fields') 
                                             ? '<td>'.price($volume).'</td>'
                                             : '') . 
                                            
											(allowView('af-ntrv',$deal,'fields') ? 
												($displayForex==1 ? '<td>'.price($spreadAmount).'</td>' : '').'
                                            '.(false && $set->deal_pnl==1 && allowView('af-pnl',$deal,'fields') ? '<td>'.price($pnl).'</td>' : '').'
											
											' : '' ).'
                                            '.(allowView('af-ntrv',$deal,'fields')?
                                                    (!$displayForex? '<td>' .
                                                            '<a href="'.$set->SSLprefix.'affiliate/reports.php?act=stats&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$merchant_id.'">'
                                                                    .price($netRevenue).
                                                            '</a>' .
                                                       '</td>' : '') 
                                                : '')
											.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')?'<td>'.price($totalPNL).'</td>':'')
                                            /*.(!$displayForex? '<td>'.price($totalCom).'</td>':'')*/
                                            .'<td>'.listGroups($ww['group_id'],1).'</td>
                                    </tr>';
                            }
                            
                            // Mark given wallet as processed.
                            $arrWallets[$merchantww['wallet_id']] = true;
                            $l++;

                        }
                        
                        
                        // Loop through affiliates, aggregate the per-merchant info.
                        if ($groupMerchantsPerAffiliate == 1) {
							
							if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {
                                        if (
											(int)$totalComs <=0 &&
                                           (int) $totalLeadsAccounts <= 0 && (int) $totalDemoAccounts <= 0 && 
                                            (int) $totalRealAccounts <= 0 && (int) $totalFTD <= 0 && 
                                            (int) $totalLots <= 0 &&
                                            (int) $totalClicks <= 0 && (int) $totalImpressions <= 0
                                        ) {
                                            continue;
                                        }
                                    }
							
                            $listReport .= '<tr>' 
                                . (isset($display_type) && !empty($display_type) ? '<td>' . $arrRange['from'] . ' - ' . $arrRange['to'] . '</td>' : '') . '
                                <td>'.$ww['ProfileId'].'</td>
                                <td>'.$ww['ProfileName'].'</td>
                                <td>'.$ww['URL'].'</td>';
                            
                            if (!$isAffiliateLogined) {
                                $listReport .='
                                    <td>'.$ww['id'].'</td>
                                    <td><a href="'.$set->SSLprefix.'affiliate/account.php" target="_blank">'.$ww['username'].'</a></td>
                                    <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';
                                
                                if ($campID['title']) {
                                    if ($ww['campID']) {
                                        $listReport .= '<td align="left">'.$ww['campID'].'</td>';
                                    } else {
                                        $listReport .= '<td align="left"></td>';
                                    }	
                                }
                                
                                $listReport .= '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
                                                <td><a href="'.$set->SSLprefix.'out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>';
                            }
                            
                            
                            
                            
                            $listReport .=
							(allowView('af-impr',$deal,'fields')?
							'<td style="text-align: center;">'.@number_format($totalImpressions, 0).'</td>
							' : '').'
							'.(allowView('af-clck',$deal,'fields')?'
                                            <td style="text-align: center;">'.@number_format($totalClicks, 0).'</td>
							' : '').'
							'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi ?'
                                            <td style="text-align: center;">'.@number_format($totalCPIGroup).'</td>
							' : '').'
                                            <td style="text-align: center;">'.@number_format(($totalClicks / $totalImpressions) * 100, 2).' %</td>
                                            <td style="text-align: center;">'.@number_format(($totalRealAccounts / $totalClicks) * 100, 2).' %</td>
                                            <td style="text-align: center;">'.@number_format(($totalFTD / $totalClicks) * 100, 2).' %</td>
                                            <td style="text-align: center;">'.@price($totalComs / $totalClicks).'</td>' . 
                                            (!$hideDemoAndLeads ? '
							'.(allowView('af-lead',$deal,'fields')?'
                                             <td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=lead">'.$totalLeadsAccounts.'</a></td>
							' : '').'
							'.(allowView('af-demo',$deal,'fields')?'
                                               <td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=demo">'.$totalDemoAccounts.'</a></td>
							' : '')
                                            : '') .'
							'.(allowView('af-real',$deal,'fields')?'
                                            <td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=real">'.$totalRealAccounts.'</a></td>'
											:'')
                                            .( $showCasinoFields && allowView('af-frzn',$deal,'fields') ? '<td>'.$totalFrozens.'</td>' : '').'
                                            '.(allowView('af-ftd',$deal,'fields') ? '
                                            <td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=ftd">'.$totalFTD.'</a></td>'
											: '').'
                                            ' . (allowView('af-ftda',$deal,'fields') ? '<td>'.price($totalFTDAmount).'</td>' : '') . 
                                            
                                            (allowView('af-tftd',$deal,'fields') ? '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=totalftd">'.$totalRealFtd.'</a></td>' : '').'
                                            '.(allowView('af-tftda',$deal,'fields') ? '<td>'.price($totalRealFtdAmount).'</td>' : '').'
                                            '.(allowView('af-depo',$deal,'fields') ?  '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&affiliate_id='.$ww['id'].'&type=deposit">'.$totalDeposits.'</a></td>' : '') .'
                                            '.(allowView('af-depoam',$deal,'fields') ? '<td>'.price($totalDepositAmount).'</td>' : '').'
                                            '.(allowView('af-bns',$deal,'fields') ? '<td>'.price($totalBonus).'</td>' : '').'
                                            '.(allowView('af-withd',$deal,'fields') ? '<td>'.price($totalWithdrawal).'</td>' : '').'
                                            '.(allowView('af-chrgb',$deal,'fields') ? '<td>'.price($totalChargeBack).' dsd</td>' : '').'
                                            '.(allowView('af-risk',$deal,'fields') ? '<td>'.@number_format(($totalFruad / $totalFTD) * 100, 2).'%</td>' : '').'
                                            '.(allowView('af-vlm',$deal,'fields') ? '<td>'.price($totalVolume).'</td>' : '').

                                        (allowView('af-ntrv',$deal,'fields') ? (
                                            ($displayForex ? '<td>'.price($totalSpreadAmount).'</td>' : '').'
                                            '.(false && $set->deal_pnl==1 && allowView('af-pnl',$deal,'fields') ? '<td>'.price($totalpnl).'</td>' : '')):'').
                                            
                                        (allowView('af-ntrv',$deal,'fields') 
                                                    ? (!$displayForex ?'<td>' .
                                                            '<a href="'.$set->SSLprefix.'affiliate/reports.php?act=stats&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$merchant_id.'">'
                                                                    .price($totalNetRevenue).
                                                            '</a>' .
                                                       '</td>':'') 
                                                : '')
											.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')?'<td>'.price($totalPNLAmount).'</td>':'')
                                            /*.(!$displayForex?'<td>'.price($totalComs).'</td>':'')*/
                                            .'<td>' . listGroups($ww['group_id'], 1) . '</td>
                                    </tr>';
                        }
                        
                        $intAffiliatesCombinedCount++;
                    }
                    
                    unset($arrRange); // Clear up the memory.
                } // End of time-periods loop.
                
                
                if ($l > 0 || $intAffiliatesCombinedCount > 0) {
                    $set->sortTableScript = 1;
                }
                  $set->content .='<style>
			  /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 60px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 20px;
			 width: 43px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 12px;
			  width: 12px;
			  left: 3px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
			
		</style>';
                $set->sortTable  = 1;
		$set->totalRows  = isset($groupByAff) && 1 == $groupByAff ? $intAffiliatesCombinedCount : $l;
		
		
		$set->content   .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
		<div style="background: #F8F8F8;">
			<form action="" method="get">
			<input type="hidden" name="act" value="profile" />
			<input type="hidden" name="search" value="1" />
			<table><tr>
                            <td>'.lang('Period').'</td>
                            <td>'.lang('Merchant').'</td>
                            <td>'.lang('Search Type').'</td>'
                            . ($intMerchantCount > 1 ? '<td>' . lang('Group Merchants') . '</td> ': '') .
                            '<td></td>
				</tr><tr>
				<td>'.timeFrame($from,$to).'</td>
				<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
				<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>'
				. ($intMerchantCount > 1 ? '<!--td><input type="checkbox" id="groupByAff" name="groupByAff" '.($groupMerchantsPerAffiliate==1 ? 'checked' : '').'/-->
				<td colspan=2><div><label class="switch"><input type="checkbox" id="groupByAff" name="groupByAff" value=1 '.($groupMerchantsPerAffiliate==1 ? 'checked' : '').' ><div class="slider round"></div></label></div>
				<!--<input type="hidden" id="groupByAffVal" value="'.($groupMerchantsPerAffiliate).'" name="groupByAff" /></td>-->' : '') .
				
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
			
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#profileData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#profileData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle"  class="table">'.lang('Profile Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
			
					//width 3000
                $tableStr = '<table  class="table ' . (!empty($listReport)?'tablesorter':'normal').' mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="profileTbl">
                            <thead><tr class="table-row">
                                    ' . (isset($display_type) && !empty($display_type) ? '<th class="table-cell">Time period</th>' : '') . '
                                    <th class="table-cell">'.lang('Profile ID').'</th>
                                    <th class="table-cell">'.lang(ptitle('Profile Name')).'</th>
                                    <th class="table-cell">'.lang(ptitle('Profile URL')).'</th>' . 
                                    ($isAffiliateLogined ? '' : '
                                    <th class="table-cell">'.lang('Affiliate ID').'</th>
                                    <th class="table-cell">'.lang('Username').'</th>
                                    <th class="table-cell">'.lang('Full Name').'</th>
                                    '.($campID['title'] ? '<th class="table-cell">'.lang($campID['title']).'</th>' : '').'
                                    <th class="table-cell">'.lang('E-Mail').'</th>'
                                    . ($groupByAff=='OFF' ? '<th class="table-cell">'.lang('Merchant').'</th>':'').'
                                    <th class="table-cell">'.lang('Website').'</th> ' ) . '
                                    '.(allowView('af-impr',$deal,'fields') ? '
									<th class="table-cell">'.lang('Impressions').'</th>
									':'').'
                                    '.(allowView('af-clck',$deal,'fields') ? '
                                    <th class="table-cell">'.lang('Clicks').'</th>
									':'').'
									'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi? '
                                    <th class="table-cell">'.lang('Installation').'</th>
									':'').'
                                    <th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
                                    <th class="table-cell">'.lang(ptitle('Click to Account')).'</th>
                                    <th class="table-cell">'.lang(ptitle('Click to Sale')).'</th>
                                    <th class="table-cell">EPC</th>' . 
                                    (!$hideDemoAndLeads ? '
                                    '.(allowView('af-lead',$deal,'fields') ? '
                                    <th class="table-cell">'.lang(ptitle('Lead')).'</th>
									':'').'
                                    '.(allowView('af-demo',$deal,'fields') ? '
                                    <th class="table-cell">'.lang(ptitle('Demo')).'</th>
									':'')
                                    : '') . 
									(allowView('af-real',$deal,'fields') ? 
                                    '<th class="table-cell">'.lang(ptitle('Accounts')).'</th>
									':'')
									
                                    .($showCasinoFields && allowView('af-frzn',$deal,'fields')  ? '<th class="table-cell">'.lang(ptitle('Frozens')).'</th>' : '').
                                    (allowView('af-ftd',$deal,'fields') ? '<th class="table-cell">'.lang('FTD').'</th>' : '' ) .
                                    (allowView('af-ftda',$deal,'fields') ? '<th class="table-cell">'.lang('FTD Amount').'</th>' : '' ) .
                                       (allowView('af-tftd',$deal,'fields') ? '<th class="table-cell">'.lang('RAW FTD').'</th>' : '' ) .
                                    (allowView('af-tftda',$deal,'fields') ? '<th class="table-cell">'.lang('RAW FTD Amount').'</th>' : '' ) .
                                    (allowView('af-depo',$deal,'fields') ? '<th class="table-cell">'.lang('Total Deposits').'</th>' : '' ) .
                                    (allowView('af-depoam',$deal,'fields') ? '<th class="table-cell">'.lang('Deposit Amount').'</th>' : '' ) .
                                    (allowView('af-bns',$deal,'fields') ? '<th class="table-cell">'.lang('Bonus Amount').'</th>' : '' ) .
                                    (allowView('af-withd',$deal,'fields') ? '<th class="table-cell">'.lang('Withdrawal Amount').'</th>' : '' ) .
                                    (allowView('af-chrgb',$deal,'fields') ? '<th class="table-cell">'.lang('ChargeBack Amount').'</th>' : '' ) .
                                    (allowView('af-risk',$deal,'fields') ? '<th class="table-cell">'.lang('Affiliate Risk').'</th>' : '' ) .
                                    (allowView('af-vlm',$deal,'fields') ? '<th class="table-cell">'.lang('Volume').'</th>' : '' ) .
       
									
									(allowView('af-ntrv',$deal,'fields') ? (
                                            ($displayForex ? '<th class="table-cell">'.lang('Spread Amount').'</th>' : '').'
                                            '.(false && $set->deal_pnl==1 && allowView('af-pnl',$deal,'fields') ? '<th class="table-cell">'.lang('PNL').'</th>' : '')):'').
                                            
                                        (allowView('af-ntrv',$deal,'fields') 
                                                    ? (!$displayForex ?'<th class="table-cell">'.lang(ptitle('Net Deposit')).'</th>':'') 
                                                : '')
									
									.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')?'<th class="table-cell">'.lang(ptitle('PNL')).'</th>':'')
                                    /*.((!$displayForex)? '<th>'.lang('Commission').'</th>':'')*/
                                    .'<th class="table-cell">'.lang('Group').'</th>
									
                            </tr></thead>
                            <tfoot><tr>
                                    <th><b>'.lang('Total').':</b></th><th></th>
                                    ' . (isset($display_type) && !empty($display_type) ? '<th></th>' : '') . '
                                    <th></th>' . 
                                    ($isAffiliateLogined ? '' : '
                                    <th colspan="4"></th>
                                    <th></th>
                                    <th></th'). ($groupByAff=='OFF' ? ($isAffiliateLogined ? '' : '<th></th>'):'').'
                                    '.(allowView('af-impr',$deal,'fields') ? '
									<th>'.$totalImpressionsM.'</th>
									':'').'
                                    '.(allowView('af-clck',$deal,'fields') ? '
                                    <th>'.$totalClicksM.'</th>
									':'').'
									'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi? '
                                    <th>'.$totalCPIM.'</th>
									':'').'
                                    <th>' . @number_format(($totalClicksM / $totalImpressionsM) * 100, 2) . ' %</th>
                                    <th>'.@number_format(($totalRealAccountsM / $totalClicksM) * 100, 2).' %</th>
                                    <th>'.@number_format(($totalFTDM / $totalClicksM) * 100, 2).' %</th>
                                    <th>' . @price($totalComsM / $totalClicksM) . '</th>' .
                                    (!$hideDemoAndLeads ?
                                    (allowView('af-lead',$deal,'fields') ? 
                                    '<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>':'').
                                    (allowView('af-demo',$deal,'fields') ? 
                                    '<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccountsM.'</a></th>'
									:'')
                                    : '') . 
                                    (allowView('af-real',$deal,'fields') ? 
                                    '<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccountsM.'</a></th>'
									:'').'
									
                                    '.($showCasinoFields && allowView('af-frzn',$deal,'fields') ?  '<th>'.$totalFrozensM.'</th>' : '').
                                    (allowView('af-ftd',$deal,'fields') ? 
                                             '<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTDM.'</a></th>'
                                            : '') . 
                                    (allowView('af-ftda',$deal,'fields') ? 
									'<th>'.price($totalFTDAmountM).'</th>' : '').'
									
                                    '.(allowView('af-tftd',$deal,'fields') ? 
									'<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtdM.'</a></th>' : '').'
                                    '.(allowView('af-tftda',$deal,'fields') ? 
                                    '<th>'.price($totalRealFtdAmountM).'</th>'
									:'').'
                                    '.(allowView('af-depo',$deal,'fields') ? 
                                    '<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDepositsM.'</a></th>' : '') .'
                                    '.(allowView('af-depoam',$deal,'fields') ? 
									 '<th>'.price($totalDepositAmountM).'</th>' :'') . '
                                    '.(allowView('af-bns',$deal,'fields') ? 
                                     '<th>'.price($totalBonusM).'</th>' :'') . '
                                    '.(allowView('af-withd',$deal,'fields') ? 
                                     '<th>'.price($totalWithdrawalM).'</th>' : '') . '
                                    '.(allowView('af-chrgb',$deal,'fields') ? 
                                     '<th>'.price($totalChargeBackM).'</th>' :'') . '
                                    '.(allowView('af-risk',$deal,'fields') ? 
                                     '<th>'.@number_format(($totalFruadM / $totalFTDM) * 100, 2).'%</th>' : '') . '
                                    '.(allowView('af-vlm',$deal,'fields') ? 
                                     '<th>'.price($totalVolumeM).'</th>' : '').
                                    
                                    ($displayForex ? '<th>'.price($totalSpreadAmountM).'</th>' : '').
									(false && allowView('af-pnl',$deal,'fields') && $set->deal_pnl==1 ? '<th>'.price($totalpnlM).'</th>' : '')  . 
									(allowView('af-ntrv',$deal,'fields') ?
									((!$displayForex)? '<th>'.price($totalNetRevenueM).'</th>' : ''):'') .
									($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')?'<th>'.price($totalPNLAmountM).'</th>':'')
									/*.((!$displayForex)? '<th>'.price($totalComsM).'</th>':'')*/
                                    .'<th></th>
                            </tr></tfoot>
                            <tbody>
                            '.$listReport.'
                    </table>
					<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
					<script>
				$(document).ready(function(){
					try{
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'profileData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#profileTbl")[0].config.rowsCopy).each(function() {
						txt += "<tr>" + $(this).html()+"</tr>";
					});
					txt += "</tbody>";
					txt += "<tfoot>" + tfoot + "</tfoot>";
					txt += "</table>";
					$("body").append("<div style=\'display:none\'>"+ txt +"</div>");
					}
					catch(e){
						//exception
					}
						$(".saveReport").on("click",function(){
						$.prompt("<label>'. lang("Provide name for report") .': <br/><input type=\'text\' name=\'report_name\' value=\'\' style=\'width:80wh\' required></label><div class=\'err_message\' style=\'color:red\'></div>", {
								top:200,
								title: "'. lang('Add to Favorites') .'",
								buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
								submit: function(e,v,m,f){
									if(v){
										name = $("[name=report_name]").val();
										if(name != ""){
											
											url = window.location.href;
											user = "'. $set->userInfo['id'] .'";
											level = "affiliate";
											type = "add";
											
											saveReportToMyFav(name, \'profile\',user,level,type);
										}
										else{
											$(".err_message").html("'. lang("Enter Report name.") .'");
											return false;
										}
									}
									else{
										//
									}
								}
							});
					});
					
				});
				</script>
					';
                
		if ($showCasinoFields) {
                    $tableStr = str_replace('<td>|FROZEN|</td>', '<td></td>', $tableStr);
                    $tableStr = str_replace('<th>|FROZEN|</th>', '<th></th>', $tableStr);
		} else {
                    $tableStr = str_replace('<td>|FROZEN|</td>', '', $tableStr);
                    $tableStr = str_replace('<th>|FROZEN|</th>', '', $tableStr);
		}
                
		$set->content .= $tableStr . '</div>' . getPager();
		excelExporter($tableStr, 'Profile');
		
		//MODAL
		$myReport = lang("Profile");
		include "common/ReportFieldsModal.php";
		
		theme();
?>