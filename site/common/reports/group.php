<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

 $arrGroups = [
                    ['id' => 0, 'title' => 'General'],
                ];
                
		$pageTitle = lang('Groups Report');
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
		$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "Groups_data_" . date('YmdHis');
		
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


		$intMerchantCount =count($merchantsArray);
		
		if (isset($groupByAff)) {
					
			if ( $groupByAff == 1) {
			$groupMerchantsPerAffiliate = 1;
		} else {
			$groupMerchantsPerAffiliate = 0;
		}
			
		} else {
			$groupMerchantsPerAffiliate = 0;
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
		
		$depositsAmount = 0;
		
		$sql = "SELECT id, title FROM groups WHERE valid = 1 ".(isset($group_id) && $group_id>0 ? " and id = " . $group_id : "" ). " ORDER BY id DESC;";
		// die ($sql);
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
					
					 // Initialize total counters per group.
                        $totalImpressions = 0;
                        $totalClicks = 0;
                        $totalCPIGroup = 0;
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
                        $totalPNLAmount = 0;
					
                    if ($ww['id'] != $group_id && isset($group_id) && $group_id!= "") {
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
                                    $totalCPI=0;
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
                                    $totalPNL = 0;
									
									  // Initialize total counters per affiliate-merchant.
                        
                                    
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
                                            
                                            if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
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
                                            . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                            . "WHERE tb1.merchant_id = '" . $merchantID . "' "
                                            . "and tb1.group_id='".($ww['id'] ? $ww['id'] : '0')."' AND tb1.rdate ".$searchInSql."";
                                    // die ($sql);
                                    $salesqq = function_mysql_query($sql,__FILE__);
                                    	$netDepositTransactions = array();
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
										
										// var_dump($salesww);
										// echo '<br>';
                                        //if ($salesww['type'] == "deposit") { // OLD.
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
                                                $ww['id'], 
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
                                        }
                                    }
									
									
								   /* if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                                    // Run through a list of affiliates.
                                    $sql = 'SELECT id AS id, merchants AS merchants FROM affiliates '
                                            . 'WHERE group_id = '.$ww['id']. ' and valid = 1 '
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '').
											  (isset($affiliate_id) && $affiliate_id != '' ? ' and id = ' . $affiliate_id : '');
                                            
                                    
                                    $resourceAffiliates = function_mysql_query($sql,__FILE__);
                                    $revCom = 0;
                                    while ($arrAff = mysql_fetch_assoc($resourceAffiliates)) {
                                        $arrMerchantsAffiliate = explode('|', $arrAff['merchants']);
                                        if (!in_array($merchantww['id'], $arrMerchantsAffiliate)) {
                                            continue;
                                        }
                                        
                                        $arrRevenueRanges = getRevenueDealTypeByRange($arrRange['from'], $arrRange['to'], $merchantww['id'], $arrAff['id'], $arrDealTypeDefaults);
                                        $intTotalRevenue  = 0;
                                        
                                        foreach ($arrRevenueRanges as $arrRange2) {
											
                                            $strRevWhere = 'WHERE merchant_id = ' . $merchantww['id'] . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                                         . '" and group_id = '.$ww['id']. ' AND affiliate_id = "' . $arrAff['id'] . '" ';
                                            
                                            $intCurrentRevenue = getRevenue($strRevWhere, $merchantww['producttype']);
                                            
                                            $intTotalRevenue    += $intCurrentRevenue;
                                            $row                 = array();
                                            $row['merchant_id']  = $merchantww['id'];
                                            $row['group_id']  = $ww['id'];
                                            $row['affiliate_id'] = $arrAff['id'];
                                            $row['banner_id']    = 0;
                                            $row['rdate']        = $arrRange2['from'];
                                            $row['amount']       = $intCurrentRevenue;
                                            $row['isFTD']        = false;
                                            $a           = getCommission(
                                                $arrRange2['from'], 
                                                $arrRange2['to'], 
                                                1, 
                                                $ww['id'], 
                                                $arrDealTypeDefaults, 
                                                $row
                                            );
											$totalCom += $a;
											$revCom += $a;
											if(isset($commission_type) && $commission_type == "All" || $commission_type == 'NetDeposit'){
												if(!empty($a)){
													$aff_data = getAffiliateRow($arrAff['id']);
													$arrAff['id'] = $arrAff['id'];
													$arrAff['rdate'] = $arrRange2['from'];
													$arrAff['location'] = lang("NetDeposit");
													$arrAff['commission'] += $a;
													$arrAff['trader_id'] = "NetDeposit";
													$arrAff['amount'] = $intCurrentRevenue;
													$arrAff['type'] = "-";
													$arrAff['merchant_id'] = $merchantww['id'];
													$arrAff['merchant_name'] = $merchantww['name'];
													$arrAff['affiliate_id'] = $arrAff['id'];
													$arrAff['affiliate_name'] = $aff_data['username'];
													
													$commissionArray[] = $arrAff;
												}
											}
											
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
							
							
						if (floatval($amount<>0)  && !empty($trans[0]['rdate'])) {
							
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
								
									$intNetRevenue =  round(getRevenue($searchInSql,$merchantww['producttype'],$revDepAmount,$revBonAmount,$revWithAmount,0,0,0,$merchantww['rev_formula'],null,$revChBAmount),2);
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

												
											$com = getCommission($trans[0]['rdate'], $trans[0]['rdate'], 1, $ww['id'],  $arrDealTypeDefaults, $comrow);
											// echo 'com : ' .$com . '         --  date:    ' . $trans[0]['rdate'].'<br>';
											$trans_NetDeposit +=$com;
											$totalCom           += $com;
											
											
											// $nirSum +=$intNetRevenue;
											// echo $nirSum.'     ||  com: '.$com.'     ||     rdate:    '.$trans[0]['rdate'].'<Br>';
											if(isset($commission_type) && $commission_type == "All" || $commission_type == 'NetDeposit'){
												if(!empty($com)){
													$aff_data = getAffiliateRow($trans[0]['affiliate_id']);
													$trans[0]['id'] = $trans[0]['id'];
													$trans[0]['rdate'] = $trans[0]['rdate'];
													$trans[0]['location'] = lang("NetDeposit");
													$trans[0]['commission'] += $com;
													$trans[0]['trader_id'] = $trans[0]['trader_id'];
													$trans[0]['amount'] = $intNetRevenue;
													$trans[0]['type'] = $trans[0]['type'];
													$trans[0]['merchant_id'] = $merchantww['id'];
													$trans[0]['merchant_name'] = $merchantww['name'];
													$trans[0]['affiliate_id'] = $trans[0]['affiliate_id'];
													$trans[0]['affiliate_name'] = $aff_data['username'];
													
													
													$commissionArray[] = $trans[0];
													
													
													
												}
											}
									
									}
									}
									// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
									
									if ($_GET['com'])
								echo 'Commission after trans_NetDeposit: ' . $trans_NetDeposit .'<Br>';
							
                                }
										
										
										
										
						
          if (strtolower($merchantww['producttype']) == 'forex') {
                                        /* $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                                . 'WHERE affiliate_id = ' . $ww['id'] . ' AND profile_id = ' . $ww['ProfileId']
                                                . (is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
                                                . ' GROUP BY affiliate_id';
												 */
										          if (strtolower($merchantww['producttype']) == 'forex') {
                                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(pnl) AS totalPnl, SUM(turnover) AS totalTO FROM data_stats '
                                                . 'WHERE merchant_id > 0 and group_id = ' . $ww['id'] 
                                                . (empty($group_id) ? '' : ' AND group_id = ' . $group_id . ' ')
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
                                         . 'WHERE merchant_id> 0 and merchant_id = "' . $merchantww['id'] . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
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
										
										
		  }
		  
		  
		    if ($set->deal_pnl == 1) {
						
								$totalPNL  = 0;
								$dealsForAffiliate['pnl'] = 1;
									
									
									$pnlRecordArray=array();
									
									$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
									$pnlRecordArray['merchant_id']  = $merchantww['id'];
									$pnlRecordArray['group_id']  = $ww['id'];
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
						
						
				if ($set->deal_cpi==1){
					
						// installation
						$array = array();			
						$array['from']  	= 	$from ;
						$array['to'] = $to;
						$array['merchant_id'] = $merchantww['id'];
						$array['type'] = 'install' ;
						$array['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
						$array['searchInSql']  = $searchInSql;
						$array['group_id'] = $ww['id'] ;
						
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
                                    
                                    $filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$n.' Day') : strtotime($showFrom));
                                    $filterTo   = ($display_type == "daily" ? strtotime($from.' +'.$n.' Day') : strtotime($showTo));
				    
                                    
									/* 
								 if (
                                            (int) $totalLeads <= 0 && (int) $totalDemo <= 0 && 
                                            (int) $totalReal <= 0 && (int) $ftd <= 0 && 
                                            (int) $depositingAccounts <= 0 && 
											(int) $totalCom <= 0 && 
                                            (int) $totalTraffic['totalViews'] <= 0 && (int) $totalTraffic['totalClicks'] <= 0
                                        ) {
                                            continue;
                                        } */
										
										
										 if ($groupMerchantsPerAffiliate == 0) {
                                        $totalImpressions = $totalTraffic['totalViews'];
                                        $totalClicks = $totalTraffic['totalClicks'];
                                        $totalCPI = $totalCPI;
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
                                        $totalPNLAmount = $totalPNL;
                                        
                                    } else {
                                        $totalImpressions += $totalTraffic['totalViews'];
                                        $totalClicks += $totalTraffic['totalClicks'];
                                        $totalCPIGroup += $totalCPI;
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
                                        $totalPNLAmount += $totalPNL;
                                    }
                                    
                                    $totalImpressionsM += $totalTraffic['totalViews'];
                                    $totalClicksM += $totalTraffic['totalClicks'];
                                    $totalCPIM += $totalCPI;
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
                                    $totalPNLAmountM += $totalPNL;
									
							 if ($groupMerchantsPerAffiliate == 0) {
								
							   
                               $listReport .= '<tr>
                                            '.($display_type == "daily" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($from.' +'.$n.' Day')).'</td>' : '').'
                                            '.($display_type == "weekly" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($showFrom)).' - '.date("Y/m/d", strtotime($showTo)).'</td>' : '').'
                                            '.($display_type == "monthly" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($showFrom)).' - '.date("Y/m/d", strtotime($showTo)).'</td>' : '').'
                                            <td>'.($ww['id'] ? $ww['id'] : '0').'</td>
                                            <td>'.($ww['title'] ? $ww['title'] : lang('General')).'</td>
                                            <td mid="'.$merchantww['id'].'">'.($merchantww['name'] ? $merchantww['name'] : '-').'</td>
                                            <td><a href="/'.$userlevel.'/reports.php?act=clicks&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'">'.@number_format($totalTraffic['totalViews'],0).'</a></td>
                                            <td><a href="/'.$userlevel.'/reports.php?act=clicks&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'">'.@number_format($totalTraffic['totalClicks'],0).'</a></td>
                                            '.($set->deal_cpi?'<td>'.@number_format($totalCPI).'</td>':'').'
                                            <td>'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>
                                            <td>'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>
                                            <td>'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>
                                            <td>'.@price($totalCom/$totalTraffic['totalClicks']).'</td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=lead">'.$totalLeads.'</a></td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=demo">'.$totalDemo.'</a></td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=real">'.$totalReal.'</a></td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=ftd">'.$ftd.'</a></td>
                                            <td>'.price($ftd_amount).'</td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=totalftd">'.$real_ftd.'</a></td>
                                            <td>'.price($real_ftd_amount).'</td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=transactions&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=deposit">'.$depositingAccounts.'</a></td>
                                            <td>'.price($sumDeposits).'</td>
                                            <td>'.price($volume).'</td>
                                            <td>'.price($bonus).'</td>
                                            <td>'.price($withdrawal).'</td>
                                            <td>'.price($chargeback).'</td>
                                            <td>'.price($netRevenue).'</td>
                                            '.($set->deal_pnl?'<td>'.price($totalPNL).'</td>':'').'
                                            <td>'.price($totalCom).'</td>
                                        </tr>';
                            }
                                    
                                    $l++;
			//	}
                            
                            // Mark given wallet as processed.
                            $arrWallets[$merchantww['wallet_id']] = true;
			}
		}
		
								 if ($groupMerchantsPerAffiliate == 1) {
                                    $listReport .= '<tr>
                                            '.($display_type == "daily" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($from.' +'.$n.' Day')).'</td>' : '').'
                                            '.($display_type == "weekly" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($showFrom)).' - '.date("Y/m/d", strtotime($showTo)).'</td>' : '').'
                                            '.($display_type == "monthly" ? '<td style="text-align: center;">'.date("Y/m/d",strtotime($showFrom)).' - '.date("Y/m/d", strtotime($showTo)).'</td>' : '').'
                                            <td>'.($ww['id'] ? $ww['id'] : '0').'</td>
                                            <td>'.($ww['title'] ? $ww['title'] : lang('General')).'</td>
                                           <!-- <td mid="'.$merchantww['id'].'">'.($merchantww['name'] ? $merchantww['name'] : '-').'</td>-->
                                            <td><a href="/'.$userlevel.'/reports.php?act=clicks&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'">'.@number_format($totalImpressions,0).'</a></td>
                                            <td><a href="/'.$userlevel.'/reports.php?act=clicks&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'">'.@number_format($totalClicks,0).'</a></td>
                                            '.($set->deal_cpi?'<td>'.@number_format($totalCPIGroup).'</td>':'').'
                                            <td>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</td>
                                            <td>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</td>
                                            <td>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</td>
                                            <td>'.@price($totalComs/$totalClicks).'</td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=lead">'.$totalLeadsAccounts.'</a></td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=demo">'.$totalDemoAccounts.'</a></td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=real">'.$totalRealAccounts.'</a></td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=ftd">'.$totalFTD.'</a></td>
                                            <td>'.price($totalFTDAmount).'</td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=trader&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=totalftd">'.$totalRealFtd.'</a></td>
                                            <td>'.price($totalRealFtdAmount).'</td>
                                            <td style="text-align: center;"><a href="/'.$userlevel.'/reports.php?act=transactions&from='.($display_type ? date("Y/m/d", $filterFrom) : date("Y/m/d", strtotime($from))).'&to='.($display_type ? date("Y/m/d", $filterTo) : date("Y/m/d", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=deposit">'.$totalDeposits.'</a></td>
                                            <td>'.price($totalDepositAmount).'</td>
                                            <td>'.price($totalVolume).'</td>
                                            <td>'.price($totalBonus).'</td>
                                            <td>'.price($totalWithdrawal).'</td>
                                            <td>'.price($totalChargeBack).'</td>
                                            <td>'.price($totalNetRevenue).'</td>
                                            '.($set->deal_pnl?'<td>'.price($totalPNLAmount).'</td>':'').'
                                            <td>'.price($totalComs).'</td>
                                        </tr>';
									 }
					
									}
		
		if ($l > 0) {
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
		
		$set->content   .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="group" />
			<table>
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Group').'</td>
					<td>'.lang('Search Type').'</td>'
					 . ($intMerchantCount > 1  ? '<td>' . lang('Group Merchants') . '</td> ': '') .
					
					'<td></td>
				</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="group_id" style="width: 150px;"><option value="">'.lang('All Groups').'</option><option value="0"' . ($group_id=="0"?"selected" : "") .'>'.lang('General').'</option>'.listGroups($group_id).'</select></td>
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					'. ($intMerchantCount > 1  ? '	<td colspan=2><div><label class="switch"><input type="checkbox" id="groupByAff" name="groupByAff" value=1 '.($groupMerchantsPerAffiliate==1 ? 'checked' : '').' ><div class="slider round">
					
					
					</div></label></div></td>':'').'
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#groupsData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#groupsData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle"  class="table">'.lang('Groups Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
			//width 2000px
				$tableStr='<table class=" table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="groupsTbl">
					<thead><tr  class="table-row">
						'.($display_type ? '<th  class="table-cell">'.lang('Period').'</th>' : '').'
						<th>'.lang('Group ID').'</th>
						<th>'.lang('Group Name').'</th>
						'.($groupMerchantsPerAffiliate==0?'<th>'.lang('Merchant').'</th>':'')
						.'<th  class="table-cell">'.lang('Impressions').'</th>
						<th class="table-cell">'.lang('Clicks').'</th>
						'.($set->deal_cpi?'<th class="table-cell">'.lang('Installation').'</th>':'').'
						<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
						<th class="table-cell">'.lang(ptitle('Click to Account')).'</th>
						<th class="table-cell">'.lang(ptitle('Click to Sale')).'</th>
						<th class="table-cell">EPC</th>
						<th class="table-cell">'.lang(ptitle('Lead')).'</th>
						<th class="table-cell">'.lang(ptitle('Demo')).'</th>
						<th class="table-cell">'.lang(ptitle('Accounts')).'</th>
						<th class="table-cell">'.lang('FTD').'</th>
						<th class="table-cell">'.lang('FTD Amount').'</th>
						<th class="table-cell">'.lang('RAW FTD').'</th>
						<th class="table-cell">'.lang('RAW FTD Amount').'</th>
						<th class="table-cell">'.lang('Total Deposits').'</th>
						<th class="table-cell">'.lang('Deposit Amount').'</th>
						<th class="table-cell">'.lang('Volume').'</th>
						<th class="table-cell">'.lang('Bonus Amount').'</th>
						<th class="table-cell">'.lang('Withdrawal Amount').'</th>
						<th class="table-cell">'.lang('ChargeBack Amount').'</th>
						<th class="table-cell">'.lang(ptitle('Net Revenue')).'</th>
						'.($set->deal_pnl?'<th class="table-cell">'.lang(ptitle('PNL')).'</th>':'').'
						<th class="table-cell">'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th><b>'.lang('Total').':</b></th>
						<th></th>
						'.($groupMerchantsPerAffiliate==0 ? '<th></th>':'')
						.'<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressionsM.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicksM.'</a></th>
						'.($set->deal_cpi?'<th>'.@number_format($totalCPIM).'</th>':'').'
						<th>'.@number_format(($totalClicksM/$totalImpressionsM)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccountsM/$totalClicksM)*100,2).' %</th>
						<th>'.@number_format(($totalFTDM/$totalClicksM)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicksM).'</th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccountsM.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccountsM.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccountsM.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTDM.'</a></th>
						<th>'.price($totalFTDAmountM).'</th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtdM.'</a></th>
						<th>'.price($totalRealFtdAmountM).'</th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDepositsM.'</a></th>
						<th>'.price($totalDepositAmountM).'</th>
						<th>'.price($totalVolumeM).'</th>
						<th>'.price($totalBonusM).'</th>
						<th>'.price($totalWithdrawalM).'</th>
						<th>'.price($totalChargeBackM).'</th>
						<th>'.price($totalNetRevenueM).'</th>
						'.($set->deal_pnl?'<th>'.price($totalPNLAmountM).'</th>':'').'
						<th>'.price($totalComsM).'</th>
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
						txt = "<table id=\'groupsData\' class=\'mdlReportFieldsData\'>";
						txt += "<thead>" + thead + "</thead>";
						txt += "<tbody>";
						$($("#groupsTbl")[0].config.rowsCopy).each(function() {
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
											level = "'. $userlevel .'";
											type = "add";
											
											saveReportToMyFav(name, \'group\',user,level,type);
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
		
		//excelExporter($tableStr, 'Group');
		$set->content .= $tableStr . '</div>' . getPager();
		
		//MODAL
		$myReport = lang("Group");
		include "common/ReportFieldsModal.php";
		
		theme();

?>