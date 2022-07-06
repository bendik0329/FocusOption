<?php

if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/affiliate" );
}

	
$userlevel = 	"affiliate";

$affiliate_id = $set->userInfo['id'];

		
	$globalWhere = " tb1.affiliate_id = " . $set->userInfo['id'] . " and ";
	$pageTitle = lang('Quick Summary Report');
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
		$isCasinoOrSportBets = false;
		// $set->hideNetRevenueForNonRevDeals,  $set->hideFTDamountForCPADeals
		
	
                    if (strpos($set->reportsToHide, 'quick') > 0) {
                            _goto('/affiliate/');
                    }
			$earliestTimeForNetRev = date('Y-m-d H:i:s');
                
                // List of wallets.
                $arrWallets = array();
                $sql = "SELECT DISTINCT wallet_id AS wallet_id ,id FROM merchants;";
                $resourceWallets = function_mysql_query($sql,__FILE__);
		
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
         
		 $set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "QuickSummary_data_" . date('YmdHis');
                
		$l   = 0;
		
		
		$merchantIDsQ = function_mysql_query('SELECT merchants FROM affiliates WHERE id='.$set->userInfo['id'],__FILE__); //OR die(mysql_error());
		$merchantIDs = mysql_fetch_assoc($merchantIDsQ);
		$merchantIDs = implode(',',explode('|',$merchantIDs['merchants']));
		if (empty($merchantIDs))
			$merchantIDs = 0;
		else 
			$merchantIDs=ltrim($merchantIDs,',');
		
		
		$sql         = "SELECT * FROM merchants WHERE valid='1' ".$where." AND id IN (".$merchantIDs.") ORDER BY pos";
		
		if (isset($merchant_id) && !empty($merchant_id)) {
			if (in_array($merchant_id, explode(',', $merchantIDs))) {
				$sql = "SELECT * FROM merchants WHERE valid = '1' " . $where . " AND id = '" . $merchant_id . "' ORDER BY pos";
			}
		}
		
		
		
		$allbrabdrsc = function_mysql_query($sql,__FILE__);
		$LowestLevelDeal = 'ALL';
	$tradersProccessedForLots= array();
		$displayForex= 0;
		while ($brandsRow = mysql_fetch_assoc($allbrabdrsc)) {
				
	if (strtolower($brandsRow['producttype'])=='forex')
				$displayForex = 1;
				foreach ($dealsArray as $dealItem=>$value) {
				// var_dump($dealItem);
					if ($brandsRow['id']==$dealItem) {
						
						$LowestLevelDeal = getLowestLevelDeal($LowestLevelDeal, $value);
						break;
					}
				}
		}
		
		
			$tradersProccessedForLots= array();
		$tradersProccessedForPNL= array();
		
		
		$qq = function_mysql_query($sql,__FILE__);
		
		while ($ww = mysql_fetch_assoc($qq)) {
                    // Check if this is a first itaration on given wallet.
					$deal = $LowestLevelDeal;
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
                    $totalCPIM=0;
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
                            $totalCPI=0;
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
                            $arrClicksAndImpressions     = getTotalClicksAndImpressions(
                                $arrRange['from'], 
                                $arrRange['to'], 
                                $ww['id'], 
                                (isset($affiliate_id) && $affiliate_id != '' ? $affiliate_id  : null),
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
                            $regCom = 0;
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
                                                'initialftddate'   => $regww['initialftddate'],
                                                'profile_id'   => $regww['profile_id'],
                                            ];
                                            
                                            $a = getCommission(
                                                $regww['rdate'], 
                                                $regww['rdate'], 
                                                0, 
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
											$regCom += $a;
											$totalCom += $a;
											
                                            unset($arrTmp);
                                            
                                        } else {
                                            // TIER CPL.
                                            if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
                                                $arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
                                            } else {
                                                $arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
                                                    $regww['rdate'], 
                                                    $regww['rdate'], 
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
				
				  if($_GET['com'])
				   {
					   echo " Commission after Reg " . $regCom . "<br/>";
				   } 
                                
                                // TIER CPL.
								$tierCom =0;
                                foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
                                    $a = getCommission(
                                        $arrParams['from'], 
                                        $arrParams['to'], 
                                        $arrParams['onlyRevShare'], 
                                        $arrParams['groupId'], 
                                        $arrParams['arrDealTypeDefaults'], 
                                        $arrParams['arrTmp']
                                    );
									$totalCom += $a;
									$tierCom += $a;
                                    
                                    unset($intAffId, $arrParams);
                                }
								
								  if($_GET['com'])
						   {
							   echo " Commission after Tier " . $tierCom . "<br/>";
						   } 
                                
                                
                                
                                $strSql = "select * from (SELECT data_sales.status ,data_sales.amount ,data_sales.tranz_id , data_sales.id, data_reg.group_id,data_reg.trader_id,data_reg.affiliate_id,data_reg.merchant_id,data_reg.banner_id,data_reg.profile_id, data_reg.initialftddate, data_sales.type AS data_sales_type, data_sales.rdate AS data_sales_rdate  FROM data_sales AS data_sales "
                                        . "INNER JOIN data_reg AS data_reg ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                        . "WHERE data_sales.merchant_id>0 and data_sales.type<>'pnl' and data_sales.merchant_id = '" . $ww['id'] . "' AND data_sales.rdate "
                                        . $searchInSql . " "
                                        . (isset($affiliate_id) && $affiliate_id != '' ? ' AND data_sales.affiliate_id = ' . $affiliate_id . ' ' : '')
										. (isset($group_id) && $group_id != '' ? ' AND data_sales.group_id = ' . $group_id . ' ' : '')
										
										. " ) a group by merchant_id , tranz_id , data_sales_type "
										;
										
										
										
										// die ($strSql);
										
								$salesqq = function_mysql_query($strSql,__FILE__);
								$netDepositTransactions = array();
								$salesCom =0;
								while ($salesww = mysql_fetch_assoc($salesqq)) {
                                    //if ($salesww['type'] == 'deposit') { // OLD.
									if ($salesww['data_sales_type'] == 'deposit') {   // NEW.
										$tranrow['id'] = $salesww['id'];
										$tranrow['affiliate_id'] = $salesww['affiliate_id'];
										$tranrow['tranz_id'] = $salesww['tranz_id'];
										$tranrow['group_id'] = $salesww['group_id'];



										$tranrow['trader_id'] = $salesww['trader_id'];
										$tranrow['merchant_id'] = $salesww['merchant_id'];
										$tranrow['rdate'] =$salesww['data_sales_rdate'];
										$tranrow['amount'] = $salesww['amount'];
										$tranrow['type'] = $salesww['data_sales_type'];
										$tranrow['status'] = $salesww['status'];
										$tranrow['initialftddate'] = $salesww['initialftddate'];
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
										$tranrow['rdate'] =$salesww['data_sales_rdate'];
										$tranrow['amount'] = $salesww['amount'];
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
                                            'initialftddate'    => $salesww['initialftddate'],
                                            'banner_id'    => $salesww['banner_id'],
                                            'trader_id'    => $salesww['trader_id'],
                                            'profile_id'   => $salesww['profile_id'],
                                            'type'       => 'volume',
                                         'amount'       => $salesww['amount'],
                                        ];
                                        
                                        $a = getCommission(
                                            $salesww['data_sales_rdate'], 
                                            $salesww['data_sales_rdate'], 
                                            0, 
                                            (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                            $arrDealTypeDefaults, 
                                            $arrTmp
                                        );
										$totalCom += $a;
										$salesCom += $a;
                                    }
									
								
                                }
				
								  if($_GET['com'])
								   {
									   echo " Commission after Sales " . $salesCom . "<br/>";
								   } 
                                
                                $arrFtds  = getTotalFtds(
                                    $arrRange['from'], 
                                    $arrRange['to'], 
                                    (!empty($affiliate_id) ? $affiliate_id : 0), 
                                    $ww['id'], 
                                    $ww['wallet_id'], 
                                    (isset($group_id) && $group_id != '' ? $group_id : -1), 
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
									$ftdCom =0;
									foreach ($arrFtds as $arrFtd) {
                                        $real_ftd++;
                                        $real_ftd_amount += $arrFtd['amount'];
                                        
                                        $beforeNewFTD = $ftd;
                                        getFtdByDealType($ww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $ftd);
                                        
										// if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
											if ($beforeNewFTD != $ftd ) {
                                            $arrFtd['isFTD'] = true;
                                            
										/* 	$b = getCommission(
                                                $arrFtd['rdate'], 
                                                $arrFtd['rdate'], 
                                                0,
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrFtd
                                            );
											// echo 'new com: ' . $b .'<br>';	
											$ftdCom +=$b;
											$totalCom+=$b; */
                                        }
                                        unset($arrFtd);
                                    }
                                }
                                if ($_GET['com'])
								echo 'Commission after ftd: ' . $ftdCom .'<Br>';
								
								
								
										//******* qualification ftds
							   $ftdUsersQualified = '';
                         $FILTERbyTrader = !empty($trader_id)? $trader_id : 0;
                         $selected_group_id = ($gorup_id<>"")? $group_id : -1;
						 $arrFtds  = getTotalFtds($arrRange['from'], $arrRange['to'], (!empty($affiliate_id) ? $affiliate_id : 0), $merchantww['id'], $merchantww['wallet_id'],$selected_group_id,0,0,"",$FILTERbyTrader,"",false,1);
							
							// echo ('--: ' . $from . '   |   '   .  $to. '   |   '   .   "0". '   |   '   .   $merchantww['id']. '   |   '   .   $merchantww['wallet_id']. '   |   '   .   ($InitManagerID==0? -1 : $InitManagerID).'<br>');
							
								
                        
                        if (!$needToSkipMerchant) {
							$ftdCom = 0;
							$new_qualified_ftd=0;
							foreach ($arrFtds as $arrFtd) {
                              
								$arrFtd['initialftddate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['rdate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['runningType'] = 'qualification';
                                $before_qualified_NewFTD = $new_qualified_ftd;
                                getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsersQualified, $irrelevant['amount'], $new_qualified_ftd,true);
                                if ($before_qualified_NewFTD != $new_qualified_ftd){// || count($arrFtds)==1) {
								
									$activeTrader++;  
                                    $arrFtd['isFTD'] = true;
									$b = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, -1, $arrDealTypeDefaults, $arrFtd);
									
									// echo 'qualified com: ' . $arrFtd['trader_id'] . '    -   ' . $b .'<br>';
									
										/* $b = getCommission(
                                                $arrFtd['rdate'], 
                                                $arrFtd['rdate'], 
                                                0,
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrFtd
                                            ); */
											// echo 'new com: ' . $b .'<br>';	
											$ftdCom +=$b;
											$totalCom+=$b;
											if(isset($commission_type) && $commission_type == "All" || $commission_type == 'CPA'){
												if(!empty($b)){
													$arrFtd['id'] = $arrFtd['id'];
													$arrFtd['rdate'] = $arrFtd['rdate'];
													$arrFtd['location'] = lang("CPA");
													$arrFtd['commission'] += $b;
													$arrFtd['trader_id'] = $arrFtd['trader_id'];
													$arrFtd['amount'] = $arrFtd['amount'];
													$arrFtd['type'] = "FTD";
													$arrFtd['merchant_id'] = $ww['id'];
													$arrFtd['merchant_name'] = $ww['name'];
													$arrFtd['affiliate_id'] = $arrFtd['affiliate_id'];
													$arrFtd['affiliate_name'] = $aff_data['username'];
													
													$commissionArray[] = $arrFtd;
												}
											}
											
											
									
									$totalCom += $a;
									$qftdCom += $a;
                                }
                            }
							
							}
							
								
                                /* if (strtolower($ww['producttype']) == 'sportsbetting' || strtolower($ww['producttype']) == 'casino') {
                                    // Run through a list of affiliates.
                                    $sql = 'SELECT id AS id, merchants AS merchants FROM affiliates '
                                            . 'WHERE valid = 1 '
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '').
											  (isset($affiliate_id) && $affiliate_id != '' ? ' and id = ' . $affiliate_id : '');
                                            
                                    
                                    $resourceAffiliates = function_mysql_query($sql,__FILE__);
                                    $revCom = 0;
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
                                            $a           = getCommission(
                                                $arrRange2['from'], 
                                                $arrRange2['to'], 
                                                1, 
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $row
                                            );
											$totalCom += $a;
											$revCom += $a;
                                            unset($arrRange2, $strRevWhere);
                                        }
                                        
                                        $netRevenue += $intTotalRevenue;
                                        unset($arrAff);
                                    }
									  if($_GET['com'])
									   {
										   echo " Commission after Rev " . $revCom . "<br/>";
									   } 

                                } else */ {
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
											$comrow['rdate']        = $trans[0]['rdate'];//arrRange2['from'];
											$comrow['amount']       = $intNetRevenue;
											 //$comrow['trader_id']  =  $trans[0]['trader_id'];
											$comrow['isFTD']        = false;
											$comrow['initialftddate']        = $trans[0]['initialftddate'];

												
														$com = getCommission($trans[0]['rdate'], $trans[0]['rdate'], 1, (isset($group_id) && $group_id != '' ? $group_id : -1),  $arrDealTypeDefaults, $comrow);
														// echo 'com : ' .$com . '         --  date:    ' . $trans[0]['rdate'].'<br>';
														$trans_revshare +=$com;
														$totalCom           += $com;
									
									}
									}
									// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
									
									if ($_GET['com'])
								echo 'Commission after trans_revshare: ' . $trans_revshare .'<Br>';
							
                                }
                                
                                
									//,SUM(turnover) AS totalTO '
                  if (strtolower($ww['producttype']) == 'forex') {
                                    $sql = 'SELECT SUM(spread) AS totalSpread '
                                            . 'FROM data_stats '
                                            . 'WHERE merchant_id>0 and merchant_id = "' . $merchantID . '" AND rdate ' . $searchInSql
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '')
                                            . (isset($affiliate_id) && $affiliate_id != '' ? ' AND affiliate_id = ' . $affiliate_id . ' ' : '');
                                    
                                    $traderStatsQ = function_mysql_query($sql,__FILE__);
                                    
                                    while($ts = mysql_fetch_assoc($traderStatsQ)){
                                        $spreadAmount  = $ts['totalSpread'];
                                        //$volume       += $ts['totalTO'];
                                        // $pnl           = $ts['totalPnl'];
                                    }
									
									
									
								// lots
					
						$totalLots  = 0;
						
						
							
							
						$sql = 'SELECT dr.initialftddate, ds.turnover AS totalTurnOver,ds.trader_id,ds.merchant_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds  
									inner join data_reg dr on ds.merchant_id = dr.merchant_id and ds.trader_id = dr.trader_id ' 
									 . 'WHERE dr.merchant_id>0 and dr.merchant_id = "' .$merchantID . '" AND dr.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                      . (isset($group_id) && $group_id != '' ? ' AND dr.group_id = ' . $group_id . ' ' : ''). (isset($affiliate_id) && $affiliate_id != '' ? ' AND dr.affiliate_id = ' . $affiliate_id . ' ' : '');
											
// die ($sql);                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d');
										$lotsCom =0;
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
														
																	// 'rdate'        => $earliestTimeForLot,
														$row = [
																	'merchant_id'  => $merchantID,
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
													$lotsCom += $a;
													// echo 'com: ' . $a .'<br>';
													$totalCom += $a;
											// }
										}
										
										  if($_GET['com'])
									   {
										   echo " Commission after Lots " . $lotsCom . "<br/>";
									   } 
				  }
				  
				  if ($set->deal_pnl == 1) {
						$dealsForAffiliate['pnl']=1; 
								$totalPNL  = 0;
								
								
								$pnlRecordArray=array();
								
								$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
								$pnlRecordArray['merchant_id']  = $merchantID;
								$pnlRecordArray['group_id']  = $group_id;
								$pnlRecordArray['searchInSql']  = $searchInSql;
								$pnlRecordArray['fromdate']  = $arrRange['from'];
								$pnlRecordArray['todate']  = $arrRange['to'];
								
								
								if ($dealsForAffiliate['pnl']>0){
									$sql = generatePNLquery($pnlRecordArray,false);
								}
								else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
									$sql = generatePNLquery($pnlRecordArray,true);
								}
								
								
								 $pnlCom = 0;
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
											
											$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// echo 'com: ' . $tmpCom.'<br>';
												$pnlCom +=$tmpCom;
												$totalCom += $tmpCom;
										}
								}
						}
								if ($_GET['com'])
								echo 'Commission after pnl: ' . $pnlCom .'<Br>';
						
					
					if ($set->deal_cpi==1){
					
						// installation
						$array = array();			
						$array['from']  	= 	$from ;
						$array['to'] = $to;
						$array['merchant_id'] = $merchantID;
						$array['type'] = 'install' ;
						$array['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
						$array['searchInSql']  = $searchInSql;
						$array['group_id'] = $group_id;
						
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
					
					
						//Sub Affiliate Commission
						
			$subcomm= 0;
			
						
						$groupPart = $userlevel=='manager' || ($group_id!="" &&  ($group_id)>-1) ? " and group_id = " .$group_id ." ": " " ;
						$qry = "select id,refer_id from affiliates where id in ( select distinct (id) as id from affiliates where 1=1  " . ($userlevel=='affiliate' ? " and id = " . $set->userInfo['id'] . " "  : "" ) . " ) and valid = 1 and sub_com>0" . $groupPart;
						// die ($qry);
						$rsc = function_mysql_query($qry,__FILE__);
							
						$allAffiliates = "";
						
						while ($row = mysql_fetch_assoc($rsc)) {
								
								
								// $sql = "SELECT id,username FROM affiliates WHERE valid = 1 AND refer_id = " . $row['id'];
								
								// $affiliateqq = function_mysql_query($sql,__FILE__);
							 
								 $hasResults = false;
								 if ($row['id']>0)  {
   										$affiliateww = getAffiliateRow($row['id']);
										// while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {
											$comData = getSubAffiliateData($arrRange['from'],$arrRange['to'],$affiliateww['id'],$affiliateww['refer_id'],'commission',$userlevel,$affiliateww['group_id']);
											$subcomm += $comData['commission'];
										}
						}
						
						$totalCom+= $subcomm;
						if($_GET['com'])	{
									echo " Commission after sub com " . $subcomm . "<br/>";
							} 
							
							
								
						
						
				$filterFrom = $arrRange['from'];
				$filterTo   = $arrRange['to'];

				
                            
                            $listReport .= '<tr>
                                        '.($display_type == "daily" ? '<td style="text-align: center;">'.$arrRange['from'].'</td>' : '').'
                                        '.($display_type == "weekly" ? '<td style="text-align: center;">'.$arrRange['from'].' - '.$arrRange['to'].'</td>' : '').'
                                        '.($display_type == "monthly" ? '<td style="text-align: center;">'.$arrRange['from'].' - '.$arrRange['to'].'</td>' : '').'
                                        <td style="text-align: left;">'.$ww['name'].'</td>
										'.(allowView('af-impr',$deal,'fields') ?'
										<td style="text-align: center;">'.@number_format($totalTraffic['totalViews'],0).'</td>
										' : '').'
                                        '.(allowView('af-clck',$deal,'fields') ? '
                                        <td style="text-align: center;">'.@number_format($totalTraffic['totalClicks'],0).'</td>
										' : '').'
										'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi ? '
                                        <td style="text-align: center;">'.@number_format($totalCPI,0).'</td>
										' : '').'
                                        <td style="text-align: center;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>
                                        <td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>
                                        <td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>' .
                                        (!$hideDemoAndLeads
                                        ? 
										 (allowView('af-lead',$deal,'fields') ? '	
										<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>
										' : '').'
										'. (allowView('af-demo',$deal,'fields') ? '			
                                           <td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>
										' : '')
										
                                        : '') . 
                                        
										 (allowView('af-real',$deal,'fields') ? 
										'<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>' :'')
										
										. (allowView('af-ftd',$deal,'fields') ? '
                                        <td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>
										' : '') .'

                                        '.(allowView('af-ftda',$deal,'fields') ? '<td style="text-align: center;">'.price($ftd_amount['amount']).'</td>' :  '').'
                                        '.(allowView('af-tftd',$deal,'fields') ? '<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=ftd">'.$real_ftd.'</a></td>' : '').'
										
                                        '.(allowView('af-tftda',$deal,'fields') ? '<td style="text-align: center;">'.price($real_ftd_amount['amount']).'</td>' : '').'	
                                        
										'.( (allowView('af-depo',$deal,'fields')) ? 
										'<td style="text-align: center;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to']
                                                  . '&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>' : '')
												  
                                         .( (allowView('af-depoam',$deal,'fields'))
                                                ? '<td style="text-align: center;">'.price($sumDeposits).'</td>' : ''
                                            )
                                            
										.(allowView('af-vlm',$deal,'fields') ? 
										'<td style="text-align: center;">'.price($volume).'</td>' : '')
										.
                                        ( (allowView('af-bns',$deal,'fields'))
										?  '<td style="text-align: center;">'.price($bonus).'</td>' : '' ).
                                            
                                        ( (allowView('af-withd',$deal,'fields')) ? 
                                             '<td style="text-align: center;">'.price($withdrawal).'</td>' : '' ) .
										( (allowView('af-chrgb',$deal,'fields')) ? 
                                                '<td style="text-align: center;">'.price($chargeback).'</td>' : '')
                                          .
                                        (allowView('af-ntrv',$deal,'fields') ?  '<td style="text-align: center;">'.price($netRevenue).'</td>' : '').'
                                       '. ($set->deal_pnl==1  && allowView('af-pnl',$deal,'fields') ? 
										 '<td>'.price($totalPNL).'</td>':'') .'                               

										 '. ( allowView('af-qftd',$deal,'fields') ? 
										 '<td style="text-align: center;"><a href="'.$set->SSLprefix.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=activeTrader">'.$new_qualified_ftd.'</a></td>':'') .'
										
                                        
                                        <td style="text-align: center;">'.price($totalCom).'</td>
                                </tr>';
				// var_dump($set);
				// die();
				
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalCPIM += $totalCPI;
				$totalLeadsAccounts += $totalLeads;
				$totalDemoAccounts += $totalDemo;
				$totalRealAccounts += $totalReal;
				$totalFTD += $ftd;
				$totalActiveTraders += $new_qualified_ftd;
				$totalRealFtd += $real_ftd;
				$totalRealFtdAmount += $real_ftd_amount;
				$totalDeposits += $depositingAccounts;
                                
                                $totalFTDAmount += $ftd_amount['amount'];

				$totalFooterPNL += $totalPNL;        
				$totalDepositAmount += $sumDeposits;
				$totalVolume += $volume;
				$totalBonus += $bonus;
				$totalWithdrawal += $withdrawal;
				$totalChargeback += $chargeback;
                                
				// if ($merchantsArr2[$ww['id']]->showRev) {
                                    $totalNetRevenue += $netRevenue;
				// }
                                
				$totalComs += $totalCom;	
                        }
                                
                    // Mark given wallet as processed.
                    $arrWallets[$ww['wallet_id']] = true;
                }
			
                
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $l;
		
		
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form method="get">
				<table><tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Search Type').'</td>';
						if ($platformParam!='') {
							$set->content .= '<td>'.lang('Platform').'</td>';
						}
						$set->content .= '<td></td>
						
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					'.$platformParam .'
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickTbl\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickTbl\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" class="table">'.lang('Quick Summary Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
		
	
			//width 2000
			$tableStr = '
			<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="quickTbl">
				<thead><tr  class="table-row">
					'.($display_type ? '<th  class="table-cell">'.lang('Period').'</th>' : '').'
					<th  class="table-cell" style="text-align: left;">'.lang('Merchant').'</th>
					'.(allowView('af-impr',$deal,'fields') ? '
					<th class="table-cell">'.lang('Impressions').'</th>
					' : '').
					(allowView('af-clck',$deal,'fields') ? '
					<th class="table-cell">'.lang('Clicks').'</th>
					' : '').'
					'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi ? '
					<th class="table-cell">'.lang('Installation').'</th>
					' : '').
					'<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
					<th class="table-cell">'.lang('Click to Account').'</th>
					<th class="table-cell">'.lang(ptitle('Click to Sale')).'</th>' . 
					(!$hideDemoAndLeads 
					?
					(allowView('af-lead',$deal,'fields') ? 
					'<th class="table-cell">'.lang(ptitle('Lead')).'</th>
					' : '').
					(allowView('af-demo',$deal,'fields') ? 
					   '<th class="table-cell">'.lang(ptitle('Demo')).'</th>
					' : '')
										
					: '') . 
					(allowView('af-real',$deal,'fields') ? 
					'<th class="table-cell">'.lang('Accounts').'</th>' 
					: '' ) .
					(allowView('af-ftd',$deal,'fields') ? '
					<th class="table-cell">'.lang('FTD').'</th>' 
					: '' ) .
					(allowView('af-ftda',$deal,'fields') ? '<th class="table-cell">'.lang('FTD Amount').'</th>' : '')
				.	(allowView('af-tftd',$deal,'fields') ? '<th class="table-cell">'.lang('FAW FTD').'</th>' : '')
				.	(allowView('af-tftda',$deal,'fields') ? '<th class="table-cell">'.lang('RAW FTD Amount').'</th>' : '')
                            
					. (allowView('af-depo',$deal,'fields') && (true) ? '<th class="table-cell">'.lang('Total Deposits').'</th>' : '' )
					. (allowView('af-depoam',$deal,'fields') && (true) ? '<th class="table-cell">'.lang('Deposit Amount').'</th>':'')
					. (allowView('af-vlm',$deal,'fields')  ? '<th class="table-cell">'.lang('Volume').'</th>':'')
					. (allowView('af-bns',$deal,'fields')  ? '<th class="table-cell">'.lang('Bonus Amount').'</th>':'')
					. (allowView('af-withd',$deal,'fields')  ? '<th class="table-cell">'.lang('Withdrawal Amount').'</th>':'')
					. (allowView('af-chrgb',$deal,'fields')  ? '<th class="table-cell">'.lang('ChargeBack Amount').'</th>':'')
					. (allowView('af-ntrv',$deal,'fields')  ? '<th class="table-cell">'.lang(ptitle('Net Deposit')).'</th>':'').
					 ($set->deal_pnl==1  && allowView('af-pnl',$deal,'fields') ?   '<th class="table-cell">'.lang(ptitle('PNL')).'</th>':'').
					 (allowView('af-qftd',$deal,'fields') ?   '<th class="table-cell">'.lang(ptitle('Active Traders')).'</th>':'')
						 
					.'<th class="table-cell">'.lang('Commission').'</th>
				</tr></thead><tfoot><tr>
					'.($display_type ? '<th></th>' : '').'
					<th style="text-align: left;"><b>'.lang('Total').':</b></th>
					'. (allowView('af-impr',$deal,'fields') ? '			
					<th>'.$totalImpressions.'</th>' : '' ) .'
					'. (allowView('af-clck',$deal,'fields') ? '			
					<th>'.$totalClicks.'</th>' : '' ).'
					'. (allowView('af-instl',$deal,'fields') && $set->deal_cpi ? '			
					<th>'.$totalCPIM.'</th>' : '' ).'
					<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
					<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
					<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>' . 
					(!$hideDemoAndLeads ? 
						(allowView('af-lead',$deal,'fields') ? 					
					'<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&type=lead">'.$totalLeadsAccounts.'</a></th>' : '').
					(allowView('af-demo',$deal,'fields') ? 
					   '<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&type=demo">'.$totalDemoAccounts.'</a></th>' : '' )
					: '') .
					
					(allowView('af-real',$deal,'fields') ? 
					'<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&type=real">'.$totalRealAccounts.'</a></th>' : '' ) .
					(allowView('af-ftd',$deal,'fields') ? '
					<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&type=ftd">'.$totalFTD.'</a></th>' : '')
					.(allowView('af-ftda',$deal,'fields') ?
					'<th>'.price($totalFTDAmount).'</th>' : '') 
					.
					(allowView('af-tftd',$deal,'fields') ? 
					'<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&type=ftd">'.$totalRealFtd.'</a></th>' : '').
					(allowView('af-tftda',$deal,'fields') ? 
					 '<th>'.price($totalRealFtdAmount).'</th>' : '')
					 .
					( (allowView('af-depo',$deal,'fields')) ? 
					 '<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&type=deposit">'.$totalDeposits.'</a></th>':'')
					 
					 .
					( (allowView('af-depoam',$deal,'fields')) ? 
                                         '<th>'.price($totalDepositAmount).'</th>': '')
										 .
					(allowView('af-vlm',$deal,'fields') ?  '<th>'.price($totalVolume).'</th>' : '')
					.
					(allowView('af-bns',$deal,'fields') ?  '<th>'.price($totalBonus).'</th>' : '').
					(allowView('af-withd',$deal,'fields') ?  '<th>'.price($totalWithdrawal).'</th>' : '').
					(allowView('af-chrgb',$deal,'fields') ?  '<th>'.price($totalChargeback).'</th>' : '').
					(allowView('af-ntrv',$deal,'fields') ?  '<th>'.price($totalNetRevenue).'</th>' : '').
					($set->deal_pnl==1  && allowView('af-pnl',$deal,'fields')? 
						 '<th>'.price($totalFooterPNL).'</th>':'').
						
						(allowView('af-qftd',$deal,'fields')? 
						 '<th>'.($totalActiveTraders).'</th>':'').
						
						
                                         '<th>'.price($totalComs).'</th>
				</tr></tfoot>
				<tbody>
				'.$listReport.'
			</table>
			<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
			<script>
			$(document).ready(function(){
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
											
											saveReportToMyFav(name, \'quick\',user,level,type);
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
			
			//excelExporter($tableStr,'Quick');
		
		$set->content.=$tableStr.'
		</div>'.getPager();
		
			//MODAL
		$myReport = lang("Quick Summary");
		include "common/ReportFieldsModal.php";
		
		
		theme();
?>