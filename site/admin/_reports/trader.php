<?php
//Prevent direct browsing of report
if(!defined('DirectBrowse')) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/admin" );
}

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
                            <td title="'.($type == "deposit" ? date("d/m/Y H:i:s", strtotime($traderInfo['rdate'])) : date("d/m/Y H:i:s", strtotime($arrRes['rdate']))).'">'.($type == "deposit" ? date("d/m/Y", strtotime($traderInfo['rdate'])) : date("d/m/Y", strtotime($arrRes['rdate']))).'</td>
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
                            <td title="'.($type == "deposit" ? date("d/m/Y H:i:s", strtotime($arrRes['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y H:i:s", strtotime($firstDeposit['rdate'])) : '')).'">'.($type == "deposit" ? date("d/m/Y", strtotime($arrRes['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y", strtotime($firstDeposit['rdate'])) : '')).'</td>
							
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

?>