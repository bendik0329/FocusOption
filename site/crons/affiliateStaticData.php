<?php
chdir("../");
require('common/global.php');
ini_set('memory_limit', '-1');
set_time_limit(0);


$where = "";
$queryStringAffiliate_id = isset($_GET['affiliate_id']) ? $_GET['affiliate_id'] : 0;
$m_date = isset($_GET['m_date']) ? $_GET['m_date'] : "";
$from = isset($_GET['from']) ? $_GET['from'] : "";


$where =  !empty($queryStringAffiliate_id) ? " and affiliates.id = " .$queryStringAffiliate_id ." " : " ";
$where_static =  !empty($queryStringAffiliate_id) ? " and affiliate_id = " .$queryStringAffiliate_id ." " : " ";

/* $from = date("2016-01-01 00:00:00");
$to = date("2016-12-31 23:59:59");
 */
 $to = date("Y-m-d" ,strtotime("-". $set->affiliateStaticReportMonths ." months"));	
 if(isset($m_date) && !empty($m_date) ){
	if(strtotime($m_date) >strtotime($to)){
		die("You cannot run the report for this date");
	}
	$from =  $m_date . " 00:00:00";
	$to =  $m_date . " 23:59:59";
 }
 else{
	
	if (empty($from)){
		
		
		$sql = "	select min(rdate) as rdate from (
					(select rdate from traffic limit 1 )
					UNION all
					(select rdate from data_reg limit 1)
					UNION all
					(select rdate from data_install limit 1)) hg";
		$minDate = mysql_fetch_assoc(function_mysql_query($sql, __FILE__));
		$from = $minDate['rdate'];
		if (empty($from))
			$from = date("2013-01-01 00:00:00");
		
	}
	else 
		$from = date($from." 00:00:00");


	//check for last run date
	$sql = "select * from affiliates_static_data affiliates where 1=1 ".$where_static." order by id DESC limit 1";
	$res = mysql_fetch_assoc(function_mysql_query($sql, __FILE__));
	 
	if($res){
		$lastRunDate = $res['rdate'];
		$from = $lastRunDate;
	}
	echo "From : " . $from . "   <br/>   To : " . $to . "<br/>";
	if (strtotime($from)>strtotime($to))
		die('nothing to do');
 }
// $to = date("Y-m-d" ,strtotime("-". $set->affiliateStaticReportMonths ." months"));	

$arrDealTypeDefaults = getMerchantDealTypeDefaults();
$l = 0;

$arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_DAILY_RANGE);


$merchantsArray = array();
                    $strAffDealTypeArray = array();
					$displayForex = 0;
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					$campID = "";
					$arrWallets = array();
					
					$merchant_id = (isset($_GET['merchant_id']) && $_GET['merchant_id']>0) ? $_GET['merchant_id'] : 0;
					// $merchant_id= 0;
					$merchantsAr = getMerchants($merchant_id,1);
					// var_dump($merchantsAr);
					// die();
					$merchantsArray=$merchantsAr;
					$data = array();
foreach ($merchantsAr as $arrMerchant) {
						
					
						// var_dump($arrMerchant);
						// die();
						
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
						if (empty($campID))
								$campID['title']= $arrMerchant['extraMemberParamName'];
							
						if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1;
					
						// $merchantsArray[$arrMerchant['id']] = $arrMerchant;
					
			
					$mer = $arrMerchant['id'];
					
					   // List of wallets.
                        $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1 " 
                              . (isset($mer) && !empty($mer) ? ' AND id = ' . $mer : '');
                        
                        $resourceWallets = function_mysql_query($sql,__FILE__);
                        
                        while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                            $arrWallets[$arrWallet['wallet_id']] = false;
                            unset($arrWallet);
                        }
						
					}
					
					$allData = array();
			
			$sql = "SELECT affiliates.*, acr.campID FROM affiliates LEFT JOIN"
                        . "(select distinct(affiliateID),campID from affiliates_campaigns_relations where 1 =1 " 
						. (isset($merchant_id) && !empty($merchant_id) ? ' AND merchantid = ' . $merchant_id : ''). " group by affiliateID) acr ON affiliates.id = acr.affiliateID"
                        . " WHERE valid = 1 " . $where 
                        . "ORDER BY affiliates.id DESC";
			
			$qq = function_mysql_query($sql,__FILE__);
			
			foreach ($arrRanges as $arrRange) {
				
					$arrRange["from"] .=  " 00:00:00";
				
					mysql_data_seek($qq, 0);
                    
                    $intAffiliatesCombinedCount = 0;
                    $showCasinoFields           = 0;
                    $totalRealFtd               = 0;
                    $totalRealFtdAmount         = 0;
                    $isRedTag = true;
                    while ($ww = mysql_fetch_assoc($qq)) {
						
						
						
						
                        // Initialize total counters per affiliate.
                        $totalImpressions = 0;
                        $totalClicks = 0;
                        $totalCPIGroup = 0;
                        $totalLeadsAccounts = 0;
                        $totalDemoAccounts = 0;
                        $totalRealAccounts = 0;
                        $totalFTD = 0;
                        $totalQFTD = 0;
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
                        $totalCom = 0;
                        $totalSpreadAmount = 0;
                        $totalTurnoverAmount = 0;
                        $totalpnl = 0;
                        $totalFrozens = 0;
                        $totalRealFtd = 0;
                        $totalRealFtdAmount = 0;
                        
						$pnl=0;
                        
						
						//$affiliate_id = $ww['id'];
						 
						/* 
						$sql = 'SELECT id,name,type,producttype,rev_formula, wallet_id  FROM merchants WHERE valid = 1 ' 
                              . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
                        // die ($sql);
                        $merchantqq = function_mysql_query($sql,__FILE__); */
                        $counterrr=0;
						
						$tradersProccessedForLots= array();
						$tradersProccessedForPNL= array();
						
						
                        // while ($merchantww = mysql_fetch_assoc($merchantqq)) {
							foreach ($merchantsArray as $merchantww) {
						
						
							// $counterrr++;
							// echo 'counter: ' . $counterrr.'<br>';
                            if (!$isRedTag){
								$arrMerchantsAffiliate = explode('|', $ww['merchants']);
								// if (!in_array($merchantww['id'], $arrMerchantsAffiliate) && $_GET['showAllRecords']==1) {
								if (!in_array($merchantww['id'], $arrMerchantsAffiliate) ) {
									
									// die ('grgerQ');
									$isRedTag=true;
								}
							}
                            
                            // Check if this is a first itaration on given wallet.
							if ($set->multiMerchantsPerTrader==1)
									$needToSkipMerchant  = $arrWallets[$merchantww['wallet_id']];
							else 
									$needToSkipMerchant= false;
							
							
                            $merchantww['count'] = $intMerchantsCount;
                            
                            $showLeadsAndDemo = strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino';
                            
                            if (strtolower($merchantww['producttype']) == 'casino') {
                                $showCasinoFields = 1;
                            }
                            
                            // Initialize total counters per affiliate-merchant.
                            $formula = $merchantww['rev_formula'];
                            $activeTrader=0;
                            $totalTraffic=0;
                            $totalLeads=0;
                            $totalDemo=0;
                            $totalReal=0;
                            $totalCPI=0;
                            $ftd=0;
                            $cpi=0;
                            $pnl = 0;
                            $volume=0;
                            $bonus=0;
                            $spreadAmount = 0;
							$frozens=0;
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
                            
                            //Clicks and Impressions
							$totalTraffic                = [];
                        
							$arrClicksAndImpressions     = getClicksAndImpressions($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id']);
							
						    $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                             
                            $merchantName = strtolower($merchantww['name']);
                          
                            
							if ($_GET['com']==2) 	echo 'com1: ' . $totalCom.'<br>';
							
                            $sql = "select * from ( SELECT * FROM data_reg "
                                    . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id = " . $ww['id'] . " "
                                    . "AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' 
									
								) a group by merchant_id , trader_id";

									
                            $regqq = function_mysql_query($sql,__FILE__);
                            
                            $arrTierCplCountCommissionParams = [];
                            $regCom = 0;
                            while ($regww = mysql_fetch_assoc($regqq)) {


							
							$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
									
								
                                $boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
                                
								if ($regww['status'] == 'frozen')  $frozens++;
								
                                if ($regww['type'] == 'lead') $totalLeads++;
                                if ($regww['type'] == 'demo') $totalDemo++;
                                if ($regww['type'] == 'real') {
                                    if (!$boolTierCplCount) {
                                        $arrTmp = [
                                            'merchant_id'  => $regww['merchant_id'],
                                            'affiliate_id' => $regww['affiliate_id'],
                                            'rdate'        => $regww['rdate'],
                                            'banner_id'    => $regww['banner_id'],
                                            'initialftddate'    => $regww['initialftddate'],
                                            'trader_id'    => $regww['trader_id'],
                                            'profile_id'   => $regww['profile_id'],
                                        ];

                                        $a = getCommission($regww['rdate'], $regww['rdate'], 0, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $arrTmp);
										$totalCom +=  $a;
										$regCom += $a;
                                        unset($arrTmp);
                                        if ($_GET['com']==2) 	echo 'com2: ' . $totalCom.'<br>';
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
                                                    'initialftddate'    => $regww['initialftddate'],
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
						   if($_GET['com'])
						   {
							   echo " Commission after Reg " . $regCom . "<br/>";
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
                            
                            
							$netDepositTransactions = array();
							
                            $ftdUsers = '';
                            $ftdCom = 0;
								if (!$needToSkipMerchant) {
									
										$arrFtds = getTotalFtds($arrRange['from'], $arrRange['to'], $ww['id'], $merchantww['id'], $merchantww['wallet_id'],(!empty($group_id)? $group_id : -1));
										// var_dump($arrFtds);
										// echo $arrRange['from'].'   -    ' .  $arrRange['to'].'   -    ' .  $ww['id'].'   -    ' .  $merchantww['id'].'   -    ' .  $merchantww['wallet_id'].'   -    ' . (!empty($group_id)? $group_id : -1).'<br>';
										// echo 'cnt: ' . count($arrFtds).'<Br>';
										// die();

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
												
												if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
												/* 	$arrFtd['isFTD'] = true;
													$a = getCommission($arrFtd['rdate'], $arrFtd['rdate'], 0, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $arrFtd);
													$totalCom += $a;
													$ftdCom += $a; */
												}
												unset($arrFtd);
										}
								}
                            
								  if($_GET['com'])
							   {
								   echo " Commission after FTD " . $ftdCom . "<br/>";
							   } 
							   
							   
							   
							   
							  //******* qualification ftds
							   $ftdUsersQualified = '';
                         $FILTERbyTrader = !empty($trader_id)? $trader_id : 0;
                         $selected_group_id = ($gorup_id<>"")? $group_id : -1;
                         
						 $selected_affiliate_id = $ww['id'];//(!empty($affiliate_id) ? $affiliate_id : 0);
						 $qftdQuery  = "SELECT * FROM `data_reg` where type<>'demo' and FTDqualificationDate>'0000-00-00 00:00:00' and FTDqualificationDate>'". $arrRange['from'] ." 00:00:00' and FTDqualificationDate <'". $arrRange['to'] ."' and affiliate_id = " . $selected_affiliate_id . " and merchant_id = ". $merchantww['id']  
						 .(!empty($selected_group_id) && $selected_group_id>0 ? ' and group_id= '. $selected_group_id : '')  
						 .(!empty($FILTERbyTrader) ? ' and trader_id= '. $FILTERbyTrader : '') ;
						 $qftdQQ = function_mysql_query($qftdQuery,__FILE__);
			
						 
						 
						 // $arrFtds  = getTotalFtds($arrRange['from'], $arrRange['to'], $selected_affiliate_id, $merchantww['id'], $merchantww['wallet_id'],$selected_group_id,0,0,"",$FILTERbyTrader,"",false,1);
							
							// echo ('--: ' . $from . '   |   '   .  $to. '   |   '   .   "0". '   |   '   .   $merchantww['id']. '   |   '   .   $merchantww['wallet_id']. '   |   '   .   ($InitManagerID==0? -1 : $InitManagerID).'<br>');
							
								
                        
                        if (!$needToSkipMerchant) {
							$qftdCom = 0;
							$new_qualified_ftd = 0;
							
							//foreach ($arrFtds as $arrFtd) {
							while ($arrFtd = mysql_fetch_assoc($qftdQQ)) {
                              
								$arrFtd['initialftddate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['rdate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['runningType'] = 'qualification';
								$arrFtd['amount'] = $arrFtd['ftdamount'] ;
							  	$arrFtd['isFTD'] = true;
								// $arrFtd['trades'] = $totalTraders;
								$arrFtd['traderHasFTD'] = $arrFtd['initialftddate']=='0000-00-00 00:00:00' ? false : true;
									
									
									
								$arrFtd['trades'] = 0;
								
								
									$activeTrader++;  
									 
									  
                                    $arrFtd['isFTD'] = true;
								
									$b = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $arrFtd);
									
								
											$qftdCom +=$b;
											$totalCom+=$b;
															
															
									if ($_GET['com']==2) 	echo 'com3: ' . $totalCom.'<br>';
                                
                            }
							
							}
							
                            /* 
									$sql = "SELECT *, tb1.type AS data_sales_type  FROM data_sales as tb1 "
											. "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
											. "WHERE tb1.merchant_id = " . $merchantww['id'] . " AND tb1.affiliate_id=".$ww['id']." "
											. "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";  */
									
									
									//$sql = "SELECT *, tb1.type AS data_sales_type, tb1.rdate as data_sales_rdate  FROM data_sales as tb1 "
									$sql = "select * from (SELECT tb1.status ,tb1.amount ,tb1.tranz_id , tb1.id, data_reg.group_id,data_reg.trader_id,data_reg.affiliate_id,data_reg.merchant_id,data_reg.banner_id,data_reg.profile_id, data_reg.initialftddate, tb1.type AS data_sales_type, tb1.rdate AS data_sales_rdate  FROM data_sales AS tb1 "
											. "INNER JOIN (select merchant_id,trader_id,affiliate_id,group_id,profile_id,country,banner_id,type,initialftddate from data_reg where   merchant_id = " . $merchantww['id'] . " AND affiliate_id=".$ww['id']."  and  initialftddate >'0000-00-00 00:00:00' and data_reg.type <> 'demo'  ) AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id "
											
											. "WHERE tb1.merchant_id >0 and  tb1.merchant_id = " . $merchantww['id'] . " AND tb1.affiliate_id=".$ww['id']." "
											. "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' 
											) a 
											group by merchant_id , tranz_id , data_sales_type"; 
									// die ($sql);		
											//if (isset($_GET['test'])) echo '<br />', $sql, '<br />';
											
									$salesqq = function_mysql_query($sql,__FILE__);
									$volume = 0;
                                    $salesCom = 0;
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
										
										if ($salesww['data_sales_type'] == 'deposit') {   // NEW.
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
									
									
										//if ($salesww['type'] == 'deposit') { // OLD.
									/* 	if ($salesww['data_sales_type'] == 'deposit') { // NEW.
                                            $depositingAccounts++;
                                            $sumDeposits    += $salesww['amount'];
											$depositsAmount += $salesww['amount'];
                                        } */
                                        
                                        if ($salesww['data_sales_type'] == 'bonus') $bonus += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'revenue') $revenue += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'withdrawal') $withdrawal += $salesww['amount'];
                                        if ($salesww['data_sales_type'] == 'chargeback') $chargeback += $salesww['amount'];
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
											
											if ($_GET['com']==2) 	echo 'com4: ' . $totalCom.'<br>';
											
                                        }
                                    }
									
						  if($_GET['com'])
						   {
							   echo " Commission after Sales " . $salesCom . "<br/>";
						   } 
									
							/* if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                                $arrRevenueRanges = getRevenueDealTypeByRange($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id'], $arrDealTypeDefaults);
                                $intTotalRevenue  = 0;
                                $revCom = 0;
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
                                    if($intCurrentRevenue!=0){
										$row                 = array();
										$row['merchant_id']  = $merchantww['id'];
										$row['affiliate_id'] = $ww['id'];
										$row['banner_id']    = 0;
										$row['rdate']        = $arrRange2['from'];
										$row['amount']       = $intCurrentRevenue;
										$row['isFTD']        = false;
										$a           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $row);
										$totalCom += $a;
										$revCom += $a;
										unset($arrRange2, $strWhere);
										if ($_GET['com']==2) 	echo 'com5: ' . $totalCom.'<br>';
										
									}
                                }
								
								  if($_GET['com'])
								   {
									   echo " Commission after Rev " . $revCom . "<br/>";
								   } 
                                $netRevenue = $intTotalRevenue;
								
                                
                            } else */ {
								
										$revElseCom = 0;
										
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
								
									$intNetRevenue =  round(getRevenue($where,$merchantww['producttype'],$revDepAmount,$revBonAmount,$revWithAmount,0,0,0,$merchantww['rev_formula'],null,$revChBAmount),2);
									
									$netRevenue += $intNetRevenue;
											$comrow                 = array();
										   $comrow['merchant_id']  = $trans[0]['merchant_id'];
										   $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
											$comrow['banner_id']    = 0;
											$comrow['rdate']        = $trans[0]['rdate'];//$arrRange2['from'];
											$comrow['amount']       = $intNetRevenue;
											 $comrow['trader_id']  =  $trans[0]['trader_id'];
											$comrow['isFTD']        = false;
											$comrow['initialftddate']        = $trans[0]['initialftddate'];
											
											$com = getCommission($trans[0]['rdate'], $trans[0]['rdate'], 1, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $comrow);
											
											
											$revElseCom += $com;
														
											$totalCom           += $com;
											if ($_GET['com']==2) 	echo 'com6: ' . $totalCom.'<br>';
									
									}
									}
									
									  if($_GET['com'])
									   {
										   echo " Commission after Rev Else " . $revElseCom . "<br/>";
									   } 
								
								
							/* 	
								
								
								
                                // $netRevenue = round($sumDeposits - ($withdrawal + $bonus + $chargeback), 2);
								$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
								
								// die ($netRevenue);
								
								 if($netRevenue!=0){      
										$row                 = [];
										$row['merchant_id']  = $merchantww['id'];
										$row['affiliate_id'] = $ww['id'];
										$row['banner_id']    = 0;
										$row['rdate']        = $from;
										$row['amount']       = $netRevenue;
										// $row['trader_id']    = 0;
										$row['isFTD']        = false;
										
									
										$totalCom           += getCommission($from, $to, 1, -1, $arrDealTypeDefaults, $row);
								 }
								 */
								
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
                                        $sql = 'SELECT SUM(spread) AS totalSpread, SUM(turnover) AS totalTO FROM data_stats '
                                                . 'WHERE merchant_id> 0 and affiliate_id = "' . $ww['id']  . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                                . ' GROUP BY affiliate_id';
                                        // die($sql);
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
                                            $spreadAmount  = $ts['totalSpread'];
                                            $volume       += $ts['totalTO'];
                                            // $pnl           = $ts['totalPnl'];
                                        }
                                        
                                        // $displayForex = 1;
								

								//Lots
														
									$totalLots  = 0;
									$sql = 'SELECT dr.initialftddate, ds.turnover AS totalTurnOver,ds.trader_id,ds.merchant_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds  
									inner join 
(select merchant_id,affiliate_id,trader_id,initialftddate,banner_id,profile_id,country,group_id from data_reg where
									
									initialftddate>"0000-00-00 00:00:00" and merchant_id = "'. $merchantww['id'] . '" AND affiliate_id = ' . $ww['id'] . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '')
									.'
									)
									dr on ds.merchant_id = dr.merchant_id and ds.trader_id = dr.trader_id ' 
                                         . 'WHERE  ds.merchant_id> 0 and  ds.merchant_id = "' . $merchantww['id'] . '" AND ds.affiliate_id = ' . $ww['id'] . ' AND ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                            . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '');
											
											// die($sql);
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        
                                        $earliestTimeForLot = date('Y-m-d H:i:s');
										$lotsCom = 0;
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
														
														if($totalLots!=0){
																$row = [
																			'merchant_id'  => $merchantww['id'],
																			'affiliate_id' => $ts['affiliate_id'],
																			'rdate'        => $earliestTimeForLot,
																			'banner_id'    => $ts['banner_id'],
																			'initialftddate'    => $ts['initialftddate'],
																			'trader_id'    => $ts['trader_id'],
																			'profile_id'   => $ts['profile_id'],
																			'type'       => 'lots',
																			'amount'       =>  $totalLots,
																];
																
																// var_dump($row);
																// die();
															$a = getCommission($from, $to, 0, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $row);
															// echo 'com: ' . $a .'<br>';
															$lotsCom += $a;
															$totalCom += $a;
															
															if ($_GET['com']==2) 	echo 'com7: ' . $totalCom.'<br>';
													}
											// }
										}
										
										  if($_GET['com'])
										   {
											   echo " Commission after Lots " . $lotsCom . "<br/>";
										   } 
						   
                                    }
						
						$pnl=0;
						$totalPNL  = 0;
						$pnlCom = 0;
						if ($set->deal_pnl == 1) {
						
								$dealsForAffiliate = getExistingDealTypesAllMerchantsForAffiliateArray($ww['id'],$arrDealTypeDefaults);
								$totalPNL  = 0;
								
								
								
								// {
								/* if (!in_array($merchantID . '-' .  $arrRes['trader_id'],$tradersProccessedForPNL)) {
									$tradersProccessedForPNL[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id']; */
									// {	
									
									// die ($where);
									
									$pnlRecordArray=array();
									
									$pnlRecordArray['affiliate_id']  = (!empty($ww['id']) ? $ww['id']: "");
									$pnlRecordArray['merchant_id']  = $merchantww['id'];
									$pnlRecordArray['group_id']  = $group_id;
									$pnlRecordArray['searchInSql']  = $searchInSql;
									$pnlRecordArray['fromdate']  = $arrRange['from'];
									$pnlRecordArray['todate']  = $arrRange['to'];
									
									
									
									if ($dealsForAffiliate['pnl']>0 || isset($dealsForAffiliate['pnl'])){
									// if ($dealsForAffiliate['pnl']>0){
										$sql = generatePNLquery($pnlRecordArray,false);
									// var_dump($dealsForAffiliate);
									// die();
									}
									else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
										$sql = generatePNLquery($pnlRecordArray,true);
									}
									
								
								
											 // echo ($sql).'<Br>';
								$traderStatsQ = function_mysql_query($sql,__FILE__);
								while($ts = mysql_fetch_assoc($traderStatsQ)){
												$pnlamount = ($ts['amount']*-1);
												$row = [
													'merchant_id'  => $ts['merchant_id'],
													'affiliate_id' => $ts['affiliate_id'],
													'rdate'        => $ts['rdate'],
													'banner_id'    => $ts['banner_id'],
													'method'    => $ts['method'],
													'trader_id'    => $ts['trader_id'],
													'profile_id'   => $ts['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  ($showCasinoFields==1 ?  calculateCasinoRevenue($pnlamount,$ts['type']) : $pnlamount) ,
												 'initialftddate'       =>  $ts['initialftddate']
												 ];
											
											$pnl = $pnl + $pnlamount;
												
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
											
											if ($dealsForAffiliate['pnl']>0 || isset($dealsForAffiliate['pnl'])){  // no need to calculate commission if the affiliate has no pnl 
												$tmpCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
												$pnlCom += $tmpCom;
												// echo 'com: ' . $tmpCom.'<br>';
												$totalCom += $tmpCom;
												
												if ($_GET['com']==2) 	echo 'com8: ' . $totalCom.'<br>';
											}
								}
							}
						  if($_GET['com'])
						   {
							   echo " Commission after PNL " . $pnlCom . "<br/>";
						   } 
						
						
					if ($set->deal_cpi==1){
						// installation
						$array = array();			
						$array['from']  	= 	$arrRange['from'] . " 00:00:00" ;
						$array['to'] = $arrRange['to'];
						$array['merchant_id'] = $merchantww['id'];
						$array['type'] = 'install' ;
						$array['affiliate_id']  = (!empty($ww['id']) ? $ww['id']: "");
						$array['searchInSql']  = $searchInSql;
						$array['group_id'] = $group_id ;
					
						
					
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
						
						
						//Sub Affiliate Commission
						
					
								if (!$isMasterAffiliatesArrayLoaded){
									// $sql = "SELECT (id) FROM affiliates WHERE valid = 1 AND refer_id>0 and refer_id = " . $ww['id']  .  ($userlevel=='manager' ? " AND  group_id = " . $group_id : "");
									// $sql = "SELECT id,refer_id FROM affiliates WHERE valid = 1 and sub_com>0 AND refer_id>0 "  .  ($userlevel=='manager' ? " AND  group_id = " . $group_id : "");
									
									$groupPart = ($group_id<>'' && ($userlevel=='manager' || $group_id>-1)) ? " and group_id = " .$group_id ." ": " " ;
									// $qry = "select id,refer_id from affiliates where id in ( select distinct (id) as id from affiliates where refer_id = " . $ww['id'] . ") and valid = 1 and sub_com>0" . $groupPart;
									$qry = "select id,refer_id from affiliates where refer_id >0 and valid = 1 and sub_com>0" . $groupPart;
									$rsc = function_mysql_query($qry,__FILE__);
									while ($row = mysql_fetch_assoc($rsc)) {

									
									$masterAffiliatesArray[$row['refer_id']][] = $row;
									
									
									$isMasterAffiliatesArrayLoaded= true;
								}
								}
						
								
							$subcomm= 0;
							if (($isMasterAffiliatesArrayLoaded) && !empty($masterAffiliatesArray))
							foreach ($masterAffiliatesArray as $row){
								
								
								 $hasResults = false;
								 if ($row['id']>0)  {
   										$affiliateww = getAffiliateRow($row['id']);
										// while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {
											$comData = getSubAffiliateData($arrRange['from'],$arrRange['to'],$affiliateww['id'],$affiliateww['refer_id'],'commission',$userlevel,$affiliateww['group_id']);
											$subcomm += $comData['commission'];
										}
						}
						
						$totalCom+= $subcomm;
						
						if ($_GET['com']==2) 	echo 'com9: ' . $totalCom.'<br>';
						
						
						if($_GET['com'])	{
									echo " Commission after sub com " . $subcomm . "<br/>";
							}
							
							
								
						
									
                                    
                                    if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {
                                        if (
                                            (int) $stats <= 0 && (int) $totalLeads <= 0 && (int) $totalDemo <= 0 && 
                                            (int) $totalReal <= 0 && (int) $ftd <= 0 && 
                                            (int) $depositingAccounts <= 0 && 
											(int) $totalCom <= 0 && 
											(int) $activeTrader <= 0 && 
											(int) $totalCPI <= 0 && 
                                            (int) $totalTraffic['totalViews'] <= 0 && (int) $totalTraffic['totalClicks'] <= 0 
											&& (int) $ftd_amount <= 0
										&& (int) $sumDeposits <= 0
										&& (int) $volume <= 0
										&& (int) $bonus <= 0
										&& (int) $withdrawal <= 0
										&& (int) $chargeback <= 0
										&& (int) $netRevenue <= 0
										&& (int) $spreadAmount <= 0
										&& (int) $turnoverAmount <= 0
										&& (int) $pnl <= 0
										&& (int) $frozens <= 0
										&& (int) $real_ftd <= 0
										&& (int) $real_ftd_amount <= 0
                                        ) {
                                            continue;
                                        }
                                    }
                                                           
                                                        
                                  /*   $sql = "SELECT id FROM payments_details WHERE status = 'canceled' AND affiliate_id = '" . $ww['id'] . "' ";
                                    $totalFruad = mysql_num_rows(function_mysql_query($sql,__FILE__));
                                    $totalFruadM += $totalFruad; */
                                 
										
										
                                   
                                        $totalImpressions = $totalTraffic['totalViews'];
                                        $totalClicks = $totalTraffic['totalClicks'];
                                        $totalCPI = $totalCPI;
                                        $totalLeadsAccounts = $totalLeads;
                                        $totalDemoAccounts = $totalDemo;
                                        $totalRealAccounts = $totalReal;
                                        $totalFTD = $ftd;
                                        $totalQFTD = $activeTrader;
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
										
										$data['affiliate_id'] = $ww['id'];
										$data['merchant_id'] = $merchantww['id'];
										$data['rdate'] = $arrRange['from'] ;
										$data['views'] =$totalTraffic['totalViews'];
										$data['clicks'] =$totalTraffic['totalClicks'];
										$data['cpi'] =$totalCPI;
										$data['leads'] =$totalLeads;
										$data['demo'] =$totalDemo;
										$data['real'] =$totalReal;
										$data['ftd'] =$ftd;
										$data['qftd'] =$activeTrader;
										$data['deposits'] =$depositingAccounts;
										$data['ftd_amount'] =$ftd_amount;
										$data['deposits_amount'] =$sumDeposits;
										$data['volume'] =$volume;
										$data['bonus'] =$bonus;
										$data['withdrawal'] =$withdrawal;
										$data['chargeback'] =$chargeBack;
										$data['netRevenue'] =$netRevenue;
										$data['commission'] =$totalCom;
										$data['spread'] =$spreadAmount;
										$data['turnover'] =$turnoverAmount;
										$data['pnl'] =$pnl;
										$data['frozons'] =$frozens;
										$data['real_ftd'] =$real_ftd;
										$data['real_ftd_amount'] =$real_ftd_amount;
									
									
									foreach($data as $key=>$val)
									{
										if($key != 'affiliate_id' && $key !="merchant_id" && $key !="rdate"){
											
											$sql = "select count(id) as chkExist from affiliates_static_data where affiliate_id = " . $data['affiliate_id'] . " and merchant_id=" . $data['merchant_id'] . " and rdate = '" . $data['rdate'] . "' and key_name='" . $key . "'" ; 
											$rowExist = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
											if(empty($rowExist['chkExist']) || !$rowExist['chkExist'] && $val!="0"){
											echo $k  .". INSERT ROW for Affiliate - <b>" . $data["affiliate_id"] . "</b> and MERCHANT - <b>" . $data['merchant_id'] . "</b> and RDATE - <b>" . $data['rdate'] . "</b> and Key Name - <b>" . $key . "</b> and VALUE - <b>" . $val . "</b><br/>";
											$sql = "insert into affiliates_static_data (affiliate_id, merchant_id, rdate, key_name, key_value ) values (". $data['affiliate_id'] .",". $data['merchant_id'] .",'". $data['rdate'] ."','". $key ."',". $val .")";
										
											function_mysql_query($sql,__FILE__,__FUNCTION__);
											}
										}
									}
									
                                  
									
									$pnl=0;
									
                                    $l++;        
                                    
                                // Mark given wallet as processed.
                                $arrWallets[$merchantww['wallet_id']] = true;
                            
                                
                           $affiliate_id_row = "<td ".(false && $isRedTag ? ' style="color:red;" '  : '' )."   >".$ww['id'].'</td>';
                            
                           
                        }
						
						
						
						
						
								
						
                        
                        // Loop through affiliates, aggregate the per-merchant info.
                       
                        
                        $intAffiliatesCombinedCount++;
						$totalVolume = 0;
                    }
                    
                    unset($arrRange); // Clear up the memory.
                } // End of time-periods loop.
                
                       

?>