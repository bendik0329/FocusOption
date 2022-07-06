<?php 
$masterAffCommission = array();
$masterPNLrequests = array();

function getSubAffiliateData($from, $to, $affiliate_id,$master_affiliate_id,$mode ="full",$userlevel="",$group_id=-1){
	
	global $set,$masterAffCommission;
	$arrResult = array();

	$arrDealTypeDefaults = getMerchantDealTypeDefaults();

if ($set->hideSubAffiliation==1){
	$arrResult['commission'] = 0;
	return $arrResult['commission'];
}

	   // Sub commission.
		if (!isset($masterAffCommission[$master_affiliate_id])){
		
		$sql = 'SELECT sub_com AS sub_com FROM affiliates WHERE valid = 1 AND id = ' . $master_affiliate_id . ' LIMIT 0, 1;';
		$arrAffSubCom = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		$floatAffSubCom = (float) $arrAffSubCom['sub_com'];
		$masterAffCommission[$master_affiliate_id] = $floatAffSubCom;
		
		unset($arrAffSubCom);
		}
		else {
			$floatAffSubCom = $masterAffCommission[$master_affiliate_id];
		}
	
	
	$merchantsArray = getMerchants(0,1);
					$displayForex = 0;

foreach($merchantsArray as $arrMerchant){
						if (strtolower($arrMerchant['producttype'])=='forex') {
							$displayForex = 1;
							break;
						}
		}
	
	 $total_leads =  $total_demo=  $total_real= $new_ftd= $totalDeposits=  $depositsAmount= $bonus=  $withdrawal= $chargeback= $thisComis = $totalDeposits = $depositsAmount =  0;

			 
	$hasResults=true;
			 
			
				if($mode == 'full'){
                        // List of wallets.
                        $arrWallets = [];
                        $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1;";
                        $resourceWallets = function_mysql_query($sql,__FILE__);
                        
                        while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                            $arrWallets[$arrWallet['wallet_id']] = false;
                            unset($arrWallet);
                        }
				}
                        
             
				if($userlevel == ""){
								$dealsArray = $set->userInfo['dealsArray'];
								 $merQry = "SELECT * FROM merchants WHERE valid='1' ORDER BY id;";
								$merchantqq = function_mysql_query($merQry,__FILE__);
											
											$allbrabdrsc = function_mysql_query($merQry,__FILE__);
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
					
				}
			 
			// $merchantqq = function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY id;",__FILE__);
                        
			// while ($merchantww = mysql_fetch_assoc($merchantqq)) {
			foreach ($merchantsArray as $merc) {
				$line_views = 0;
				$line_clicks = 0;
				$line_leads = 0;
				$line_demo = 0;
				$line_real = 0;
				$line_ftd = 0;
				$line_lots = 0;
				$line_cpi = 0;
				$line_ftd_amount = 0;
				$line_deposits = 0;
				$line_deposits_amount = 0;
				$line_bonus = 0;
				$line_withdrawal = 0;
				$line_comission = 0;
				$line_pnl = 0;
				// var_dump($merc);
				// die();
				
				$merchantww  = $merc;
				$formula = $merchantww['rev_formula'];
                                // Check if this is a first itaration on given wallet.
                                $needToSkipMerchant = $arrWallets[$merchantww['wallet_id']];
                                
				$l++;
				$ftd_amount['amount'] = 0;
                                $ftd = 0;
                                
                                $total                   = [];
				if($mode == "full"){
                                //$arrClicksAndImpressions = getClicksAndImpressions($from, $to, $merchantww['id'], $affiliate_id);
								$arrClicksAndImpressions = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS impressions, SUM(clicks) AS clicks FROM sub_stats WHERE affiliate_id='".$affiliate_id."'",__FILE__));
                                $total['viewsSum']       = $arrClicksAndImpressions['impressions'];
                                $total['clicksSum']      = $arrClicksAndImpressions['clicks'];
                                
                                
								$regqq=function_mysql_query("SELECT id,type,initialftddate FROM data_reg where merchant_id ='". $merchantww['id'] ."' and affiliate_id='".$affiliate_id."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
								while ($regww=mysql_fetch_assoc($regqq)) {
									if ($regww['type'] == "lead") $total_leads++;
									if ($regww['type'] == "demo") $total_demo++;
									if ($regww['type'] == "real") $total_real++;
								}
								/* $sql = "SELECT type,amount FROM data_sales WHERE merchant_id = '".$merchantww['id'] . "' and  affiliate_id='".$affiliate_id."' AND rdate BETWEEN '".$from."' AND '".$to."'";

								$salesqq=function_mysql_query($sql,__FILE__);
								while ($salesww=mysql_fetch_assoc($salesqq)) {
													if ($salesww['type'] == "deposit") {
														$depositsAmount += $salesww['amount'];
														$totalDeposits++;
														}
													if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
													if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
													if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
													if ($salesww['type'] == "volume") $volume += $salesww['volume'];
									} */
									
				
					}        
                                $ftdUsers = '';
                                
                                if (!$needToSkipMerchant) {
                                    $arrFtds = getTotalFtds($from, $to, $affiliate_id, $merchantww['id'], $merchantww['wallet_id']);
                                    
                                    foreach ($arrFtds as $arrFtd) {
                                        $new_ftd++;
                                        // $ftd_amount['amount'] += $totalftd['amount'];
                                        $ftd_amount['amount'] += $arrFtd['amount'];
                                        $beforeNewFTD = $ftd;
                                        getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $ftd);
                                        
										// var_dump($arrFtd);
										// die();
                                        if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
                                            $arrFtd['isFTD'] = true;
                                            // $thisComis += (getCommission($from, $to,$arrFtd['affiliate_id'] ,$merchantww['id'], $arrDealTypeDefaults, $arrFtd) ) * $floatAffSubCom/ 100;
											// if($userlevel == "admin")
											// $thisComis += (getCommission($from, $to,$arrFtd['affiliate_id'] ,$group_id, $arrDealTypeDefaults, $arrFtd) ) * $floatAffSubCom/ 100;
											// else
//											$thisComis += (getCommission($from, $to, $arrFtd['affiliate_id'], $group_id, $arrDealTypeDefaults, $arrFtd) / 100) * $floatAffSubCom;
											// $thisComis += (getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $arrFtd) / 100) * $floatAffSubCom;
											
                                        }
                                        unset($arrFtd);
                                    }
                                }
                  
				  
				  			//******* qualification ftds
							   $ftdUsersQualified = '';
                         $FILTERbyTrader = !empty($trader_id)? $trader_id : 0;
                         $selected_group_id = ($group_id<>"")? $group_id : -1;
						 //$arrFtds  = getTotalFtds($from, $to, (!empty($affiliate_id) ? $affiliate_id : 0), $merchantww['id'], $merchantww['wallet_id'],$selected_group_id,0,0,"",$FILTERbyTrader,"",false,1);
							
							// echo ('--: ' . $from . '   |   '   .  $to. '   |   '   .   "0". '   |   '   .   $merchantww['id']. '   |   '   .   $merchantww['wallet_id']. '   |   '   .   ($InitManagerID==0? -1 : $InitManagerID).'<br>');
							
							 $qftdQuery  = "SELECT * FROM `data_reg` where type<>'demo' and FTDqualificationDate>'0000-00-00 00:00:00' and FTDqualificationDate>'". $from ." 00:00:00' and FTDqualificationDate <'". $to ."' " . ($affiliate_id?" and affiliate_id = " . $affiliate_id  : '') . " and merchant_id = ". $merchantww['id']  
							 .(!empty($selected_group_id) && $selected_group_id>0 ? ' and group_id= '. $selected_group_id : '')  
							 .(!empty($FILTERbyTrader) ? ' and trader_id= '. $FILTERbyTrader : '') ;
							
							 $qftdQQ = function_mysql_query($qftdQuery,__FILE__);
								
                        
                        if (!$needToSkipMerchant) {
							$ftdCom = 0;
							while ($arrFtd = mysql_fetch_assoc($qftdQQ)) {
								$aff_data = getAffiliateRow($arrFtd['affiliate_id']);
								$arrFtd['initialftddate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['rdate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['runningType'] = 'qualification';
                               
									$activeTrader++;  
                                    $arrFtd['isFTD'] = true;
									$b = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, $selected_group_id, $arrDealTypeDefaults, $arrFtd);
									
									
									$ftdCom +=$b;
									$thisComis+=$b;
											
									if($_GET['com']==2){
										echo "thisComis = " .  $thisComis . "<br/>";
									}
								
                            }
							
							}
				
				//data_sales
				$sql = "select * from (SELECT tb1.status ,tb1.amount ,tb1.tranz_id , tb1.id, data_reg.group_id,data_reg.trader_id,data_reg.affiliate_id,data_reg.merchant_id,data_reg.banner_id,data_reg.profile_id, data_reg.initialftddate, tb1.type AS data_sales_type, tb1.rdate AS data_sales_rdate  FROM data_sales AS tb1 "
											. "INNER JOIN (select merchant_id,trader_id,affiliate_id,group_id,profile_id,country,banner_id,type,initialftddate from data_reg where   merchant_id = " . $merchantww['id'] . " AND affiliate_id=".$affiliate_id."  and  initialftddate >'0000-00-00 00:00:00' and data_reg.type <> 'demo'  ) AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id "
											
											. "WHERE tb1.merchant_id >0 and  tb1.merchant_id = " . $merchantww['id'] . " AND tb1.affiliate_id=".$affiliate_id." "
											. "AND tb1.rdate BETWEEN '" . $from . "' AND '" . $to . "' 
											) a 
											group by merchant_id , tranz_id , data_sales_type"; 
									
									 //die ($sql);		
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
											$thisComis += $a;
											$salesCom += $a;
											
											if ($_GET['com']==2) 	echo 'com4: ' . $thisComis.'<br>';
											
                                        }
                                    }
				if($mode=='full'){
				/* if (strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') {
                                $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantww['id'], $affiliate_id, $arrDealTypeDefaults);
                                $intTotalRevenue  = 0;
                                $revCom = 0;
                                foreach ($arrRevenueRanges as $arrRange2) {
                                    $strWhere = 'WHERE merchant_id = ' . $merchantww['id'] 
                                              . ' AND rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
                                              . '" AND affiliate_id = "' . $affiliate_id . '"';
                                    
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
										$row['affiliate_id'] = $affiliate_id;
										$row['banner_id']    = 0;
										$row['rdate']        = $arrRange2['from'];
										$row['amount']       = $intCurrentRevenue;
										$row['isFTD']        = false;
										$a           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $row);
										$thisComis += $a;
										$revCom += $a;
										unset($arrRange2, $strWhere);
										if ($_GET['com']==2) 	echo 'com5: ' . $thisComis.'<br>';
										
									}
                                }
								
								  if($_GET['com'])
								   {
									   echo " Commission after Rev " . $revCom . "<br/>";
								   } 
                                $netRevenue = $intTotalRevenue;
								
                                
                            } else  */{
								
										$revElseCom = 0;
										if(!empty($netDepositTransactions)){
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
										   $comrow['merchant_id']  = $merchantww['id'];
										   $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
											$comrow['banner_id']    = 0;
											$comrow['rdate']        = $trans[0]['rdate'];//$arrRange2['from'];
											$comrow['amount']       = $intNetRevenue;
											 $comrow['trader_id']  =  $trans[0]['trader_id'];
											$comrow['isFTD']        = false;
											$comrow['initialftddate']        = $trans[0]['initialftddate'];
											
											$com = getCommission($trans[0]['rdate'], $trans[0]['rdate'], 1, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $comrow);
											
													
											
														$revElseCom += $com;
														
														$thisComis           += $com;
														
														if ($_GET['com']==2) 	echo 'com6  :  ' . $affiliate_id . " : "  . $thisComis.'<br>';
									
									}
									}
										}	
									  if($_GET['com'])
									   {
										   echo " Commission after Rev Else " . $revElseCom . "<br/>";
									   } 
									 
									 
							}	

				
				}
				  if ($merchantww['producttype']=='forex' ) {
						
						//lots 
						$sql = 'SELECT turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
										 . 'WHERE affiliate_id = ' . $affiliate_id
                                         . ' and merchant_id = "' . $merchantww['id'] . '" AND rdate BETWEEN "' . $from . '" AND "' . $to . '" '
							 ;
											
					
					$traderStatsQ = function_mysql_query($sql,__FILE__);
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
									// var_dump($row);
									// die();
								$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row); 
								
								
								if($_GET['com']==2){
										echo "thisComis LOTS = " .  $thisComis . "<br/>";
									}
								// echo 'com: ' . $a .'<br>';
								$thisComis += $a;
						// }
					$line_lots +=$totalLots;
					}
					
			}
			
			
				 if ($set->deal_pnl == 1) {
						$dealsForAffiliate['pnl']=1; 
								$line_pnl  = 0;
								
								
								// {
								/* if (!in_array($merchantID . '-' .  $arrRes['trader_id'],$tradersProccessedForPNL)) {
									$tradersProccessedForPNL[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id']; */
									// {	
									
									// die ($where);
									
									
										$pnlRecordArray=array();
									
										$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
										$pnlRecordArray['merchant_id']  = $merchantww['id'];
										$pnlRecordArray['trader_id']  = (isset($trader_id) ? $trader_id: "");
										$pnlRecordArray['banner_id']  = $banner_id;
										$pnlRecordArray['profile_id']  = $profile_id;
										$pnlRecordArray['group_id']  = $group_id;
										$pnlRecordArray['searchInSql']  = $searchInSql;
										$pnlRecordArray['fromdate']  = $from;
										$pnlRecordArray['todate']  = $to;
									
									if (!isset($masterPNLrequests[$pnlRecordArray['affiliate_id']])){
									
														
														if ($dealsForAffiliate['pnl']>0){
															$sql = generatePNLquery($pnlRecordArray,false);
														}
														else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
															$sql = generatePNLquery($pnlRecordArray,true);
														}

											   
																 //echo ($sql).'<Br>';
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
																	
																	$line_pnl = $line_pnl + $pnlamount;
									 
																//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
																// die ('getcom: ' .$a );
															if ($dealsForAffiliate['pnl']>0){

																$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $group_id, $arrDealTypeDefaults, $row);
																	
																// echo 'com: ' . $tmpCom.'<br>';
																	$pnlCom +=$tmpCom;
																	$thisComis += $tmpCom;
															}
													}
										$masterPNLrequests[$pnlRecordArray['affiliate_id']]['com'] = 		$pnlCom	;
										$masterPNLrequests[$pnlRecordArray['affiliate_id']]['pnlamount'] = 		$line_pnl	;
													
									}
									else {
										
								//		$line_pnl = $masterPNLrequests[$pnlRecordArray['affiliate_id']]['pnlamount'] 	;
								//		$pnlCom =  $masterPNLrequests[$pnlRecordArray['affiliate_id']]['com'] 	;
								//		$thisComis +=  $masterPNLrequests[$pnlRecordArray['affiliate_id']]['com'] 	;
										
									}
									if($_GET['com']==2){
										echo "thisComis PNL = " .  $thisComis . "<br/>";
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
						$array['group_id'] = $group_id ;
						
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
											 echo '$totalCom: ' . $thisComis. '<br>';
										}
									$thisComis +=$a;
									
									// unset($arrTmp);
									
									
							// var_dump($install_item);
							// echo '<Br><Br>';
							// die('--');
							unset($a);
						}
						if($_GET['com']==2){
										echo "thisComis INSTALL = " .  $thisComis . "<br/>";
									}
						}
						// end of install
	   
					}


				  
				$line_views += $total['viewsSum'];
				$line_clicks += $total['clicksSum'];
				$line_leads += $total_leads;
				
				$line_cpi += $totalCPI;
				
				$line_demo += $total_demo;
				$line_real += $total_real;
				$line_ftd += $new_ftd;
				$line_ftd_amount += $ftd_amount['amount'];
				$line_deposits += $totalDeposits;
				$line_deposits_amount += $depositsAmount;
				$line_bonus += $bonus;
				$line_withdrawal += $withdrawal;
				$line_chargeback += $chargeback;
				$line_comission += $thisComis / 100 * $floatAffSubCom;
			
			if($_GET['com']==2){
					echo "line Commission = " .  $line_comission . "<br/>";
			}
				
                                
                            // Mark given wallet as processed.
                            $arrWallets[$merchantww['wallet_id']] = true;
			}
	
				if($mode == "full" || $mode == "default"){
				$arrResult['views'] = $line_views;
				$arrResult['clicks'] = $line_clicks;
				$arrResult['leads'] = $line_leads;
				$arrResult['demo'] = $line_demo;
				$arrResult['real'] = $line_real;
				$arrResult['ftd'] = $line_ftd;
				$arrResult['ftd_amount'] = $line_ftd_amount;
				$arrResult['deposits'] = $line_deposits;
				$arrResult['deposits_amount'] = $line_deposits_amount;
				$arrResult['bonus'] = $bonus;
				$arrResult['withdrawal'] = $line_withdrawal;
				$arrResult['chargeback'] = $line_chargeback;
				$arrResult['commission'] = $line_comission;
				$arrResult['pnl'] = $line_pnl;
				$arrResult['cpi'] = $line_cpi;
				}
				else{
					$arrResult['commission'] = $line_comission;
				}
	return $arrResult;
	
}



