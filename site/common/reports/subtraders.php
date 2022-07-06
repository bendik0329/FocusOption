<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

if($userlevel == "manager")
	$globalWhere = "group_id = '".$set->userInfo['group_id']."' AND ";
else
	$globalWhere = "";

$pageTitle = lang(ptitle('Sub Trader Report'));
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.$userlevel.'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
	$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "SubTraders_data_" . date('YmdHis');
			
if($userlevel == 'manager')
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
                        $qry = "select trader_id from data_reg  where merchant_id>0 and  lower(trader_alias) like ('%". mysql_real_escape_string(strtolower($trader_alias))."%')";
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
			$qry = "select id,status from data_reg where merchant_id >0 and  merchant_id = " . $int_merchant_id . " AND (status = 'frozen' or type= 'demo') ";
			$invalidResources = function_mysql_query($qry,__FILE__);
			$invalidTraders = array();
			$frozenTraders = array();
			while ($invalidRow = mysql_fetch_assoc($invalidResources)) {
				$invalidTraders[] = $invalidRow['id'];
				if ($invalidRow['status']=='frozen') {
					$frozenTraders[] = $invalidRow['id'];
				}
			}
							
							//echo $where; die;
							if(isset($trader_id) && $trader_id != ""){
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE merchant_id>0 and 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status <> 'frozen' and type='real' "
                                    //. "GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                            }
							else{
								
								$sql = "SELECT * FROM data_reg "
                                    . "WHERE merchant_id>0 and 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status <> 'frozen' and type='real' and sub_trader_id>0 "
                                    . "GROUP BY trader_id, sub_trader_id "
                                    . "ORDER BY id DESC;";
							}
							
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                        
                        
                        
                        unset($arrMerchant);
                } // END of "merchants" loop.
				
				
                    $merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
				
					foreach ($merchantsAr as $arrMerchant) {
					
						
						
						if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1;
					
						$merchantsArray[$arrMerchant['id']] = $arrMerchant;
					}
					
		
					$tradersProccessedForLots= array();
               
			
			// HERE WHERE ALL THE FUN PART BEGIN       **********************************************************************
			$total_ftd_amount = 0;
			   foreach ($arrResultSet as $arrRes) {
                    
					$arrMerchant = $merchantsArray[$arrRes['merchant_id']];
					$ftdAmount = 0;
				
					
                    $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    $totalCom = 0;
                    $int_merchant_id = $arrRes['merchant_id'];
                    
                    
                    if ($type == 'ftd' || $type == 'totalftd') {
                        $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    } else {
                        $firstDeposit = [];
                    }
                    
                    
                    
                    $depositAmount = 0;
					
					
					
					  // BANNER info retrieval.
					$bannerInfo = getCreativeInfo($arrRes['banner_id']);
								
								
						
						 $sql = "SELECT ds.* FROM data_sales AS ds
                                
                                WHERE merchant_id > 0 and 4=4 and " . $globalWhere . " ds.trader_id = " . $arrRes['trader_id']  
                                        . " AND ds.sub_trader_id= ". $arrRes['sub_trader_id'] ." AND ds.merchant_id = " . $int_merchant_id
										
                                . " ORDER BY ds.rdate ASC;";
						
					
                        $resource = function_mysql_query($sql,__FILE__);
                        $total_deposits = 0;

                        while ($arrAmount = mysql_fetch_assoc($resource)) {
							
							if (in_array($arrAmount['trader_id'], $frozenTraders)) {
								// die ('greger222');
								continue;
								
							}
							
							
                            $arrRes['tranz_id'] = $arrAmount['tranz_id'];
						
							

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
											   'sub_trader_id'    => $arrRes['sub_trader_id'],
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
						
						
						//if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$tradersProccessedForLots)) {
							$tradersProccessedForLots[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
							
						$sql = 'SELECT  * FROM data_stats 
                                         WHERE  merchant_id >0 and  merchant_id = "' . $arrMerchant['id'] . '" and  trader_id = "' . $arrRes['trader_id'] . '" and sub_trader_id =  ' . $arrRes['sub_trader_id'] ;
                           
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
											'sub_trader_id'    => $arrRes['sub_trader_id'],
                                            'profile_id'   => $arrRes['profile_id'],
                                            'type'       => 'lots',
                                         'amount'       =>  $totalLots,
										 ];
										 
					
							$totalCom += getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
							
						//}
						
						
						
						
						
						}
                        
                     
		                    
                    
                        $ftdUsers = '';
                        
                        /* $arrTotalFtds = getTotalFtds(
                            $from, $to, $arrRes['affiliate_id'], $arrRes['merchant_id'], $arrMerchant['wallet_id'], 
                            $arrRes['group_id'], $arrRes['banner_id'], $arrRes['profile_id'], '', $arrRes['trader_id']
                        ); */
						
						$qry = "SELECT tb1.merchant_id,tb1.banner_id, tb1.amount, tb1.rdate, tb1.affiliate_id, tb1.trader_id, tb1.id as id,tb1.sub_trader_id FROM data_sales  AS tb1
                                WHERE 1 = 1  AND tb1.merchant_id>0 AND tb1.merchant_id = " . $arrRes['merchant_id'] . " AND tb1.rdate between '" . $from ."' and '". $to ."'
								AND tb1.type = 'deposit' 
								AND tb1.affiliate_id = " . $arrRes['affiliate_id'] . 
								' AND tb1.banner_id = ' . $arrRes['banner_id'] . 
								 ' AND tb1.trader_id = '.$arrRes['trader_id'] . 
								' AND tb1.profile_id = ' . $arrRes['profile_id'] . 
								' AND tb1.sub_trader_id = ' . $arrRes['sub_trader_id'] . 
								' AND tb1.group_id = '.$arrRes['group_id'];
						
							$arrTotalFtds = mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
	
						
							if(!empty($arrTotalFtds)){
                            $beforeNewFTD = $ftd;
                            getFtdByDealType($arrRes['merchant_id'], $arrTotalFtds, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);

                            if ($beforeNewFTD != $ftd || count($arrTotalFtds)==1) {
                                $firstDeposit = $arrTotalFtds;
								
                                $ftdAmount = $arrTotalFtds['amount'];
								
								$total_ftd_amount += $ftdAmount;
								
                                $arrTotalFtds['isFTD'] = true;


							if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$arrTradersPerMerchants)) {
                                    $totalCom += getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrTotalFtds);
                                }
                            }
                            //unset($arrTotalFtds);
							}
               ///     }
                    
					
                    /* if (strtolower($arrMerchant['producttype']) == 'sportsbetting' || strtolower($arrMerchant['producttype']) == 'casino') {
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
                                '" AND affiliate_id = "' . $arrRes['affiliate_id'] . '" AND trader_id = ' . $arrRes['trader_id'] . ' AND sub_trader_id = ' . $arrRes['sub_trader_id'],
                                
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
							$row['sub_trader_id']    = $arrRes['sub_trader_id'];
                            $row['isFTD']        = false;
							
							
                            $totalCom           += getCommission($arrRange['from'], $arrRange['to'], 1, $group_id, $arrDealTypeDefaults, $row);
                            unset($arrRange);
                        }
                        
                        $netRevenue = $intTotalRevenue;
                        
                    } else */ {
						
						$where .= " AND sub_trader_id=" . $arrRes['sub_trader_id'];
						
						
$netRevenue =  round(getRevenue($where,$arrMerchant['producttype'],$depositAmount,$bonusAmount,$withdrawalAmount,0,0,0,$arrMerchant['rev_formula'],null,$chargebackAmount),2);
					
               
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
					
           
					
                    
                    
					$affInfo = getAffiliateRow($arrRes['affiliate_id']);
							
						

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

					
					
					if ($set->deal_pnl == 1) {
						
								$totalPNL  = 0;
								$dealsForAffiliate = getExistingDealTypesAllMerchantsForAffiliateArray($arrRes['affiliate_id'],$arrDealTypeDefaults);								
								
								// {
								if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$tradersProccessedForPNL)) {
									$tradersProccessedForPNL[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
									// {	
									
									// die ($where);
								
								$pnlRecordArray=array();
								
								$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
								$pnlRecordArray['merchant_id']  =  $arrMerchant['id'];
								$pnlRecordArray['group_id']  = $group_id;
								$pnlRecordArray['trader_id']  = $arrRes['trader_id'];
								$pnlRecordArray['searchInSql']  = $searchInSql;
								$pnlRecordArray['fromdate']  = $from;
								$pnlRecordArray['todate']  = $to;
								
								
								if ($dealsForAffiliate['pnl']>0){
									$sql = generatePNLquery($pnlRecordArray,false);
								}
								else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
									$sql = generatePNLquery($pnlRecordArray,true);
								}
								
						
											// echo ($sql).'<Br>';
								$traderStatsQ = function_mysql_query($sql,__FILE__);
								while($ts = mysql_fetch_assoc($traderStatsQ)){
												$pnlamount = ($ts['amount']*-1);
												$row = [
													'merchant_id'  => $arrMerchant['id'],
													'affiliate_id' => $arrRes['affiliate_id'],
													'rdate'        => $ts['rdate'],
													'banner_id'    => $arrRes['banner_id'],
													'trader_id'    => $arrRes['trader_id'],
													'profile_id'   => $arrRes['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  $pnlamount,
												 'initialftddate'       =>  $ts['initialftddate']
												 ];
												 
											
												$totalPNL = $totalPNL + $pnlamount;
												
															 
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
											if ($dealsForAffiliate['pnl']>0){
												$tmpCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
												// echo 'com: ' . $tmpCom.'<br>';
													$totalCom += $tmpCom;
											}
								}
						}
						
						
						}
						
						

                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].'</td>
                            '.($displayForex==1? '
							<td>'.$arrRes['sub_trader_id'].'</td>
							':'').'
                            <td>'.$arrRes['trader_alias'].'</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.$arrRes['email'].'</td>' : '' ) . '
                            <td title="'.($type == "deposit" ? date("d/m/Y H:i:s", strtotime($traderInfo['rdate'])) : date("d/m/Y H:i:s", strtotime($arrRes['rdate']))).'">'.($type == "deposit" ? date("d/m/Y", strtotime($traderInfo['rdate'])) : date("d/m/Y", strtotime($arrRes['rdate']))).'</td>
                            <td><span style="color: '.$color.';">'.$arrRes['type'].'</span></td>
                            <td>'.longCountry($arrRes['country']).'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
                            <td><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
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
                            <td><a href="/'. $userlevel .'/reports.php?act=transactions&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
                            <td>'.price($depositAmount).'</td>
                            <td>'.price($volumeAmount).'</td>
                            <td>'.price($bonusAmount).'</td>
                            <td>'.price($withdrawalAmount).'</td>
                            <td>'.price($chargebackAmount).'</td>
                            <td>'.price($netRevenue).'</td>
							'.($set->deal_pnl==1 ? '
                            <td>'.price($totalPNL).'</td>' : '').'
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
                    
					
					//if (!in_array($arrRes['merchant_id'] . '-' . $arrRes['trader_id'],   $arrTradersPerMerchants)) {
                        $arrTradersPerMerchants[] = $arrRes['merchant_id'] . '-' . $arrRes['trader_id']; //$arrRes['trader_id'];
                        $totalTotalCom += $totalCom;
                        $totalFTD += $ftdAmount;
                        $totalNetRevenue += $netRevenue;
					

					// die ($totalTotalCom);
					if ($_GET['deb']==1) {
					var_dump($arrTradersPerMerchants);
					die('totalcom: ' . $totalCom);
					}
					
					
                    //}

                    $totalDepositAmount += $depositAmount;
                    $totalVolumeAmount += $volumeAmount;
                    $totalBonusAmount += $bonusAmount;
                    $totalTotalDeposit += $total_deposits;
                    $totalTotalPNL += $totalPNL;
                    $totalTrades += $totalTraders;
                    $totalLotsamount += $totalLots;
                    $totalWithdrawalAmount += $withdrawalAmount;
                    $totalChargeBackAmount += $chargebackAmount;
                    $ftdExist[] = $firstDeposit['trader_id'];
                    $l++;
					
					$volumeAmount=$totalTotalPNL=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$totalTraders=$totalLots=0;
                }
                        
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="subtraders" />
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
						'. ($userlevel == 'admin' ? '<td>'.lang('Group').'</td>':'').'
						
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
						'. ($userlevel == 'admin'?'
                                                <td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                                                </td>':'').'
						
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer;" onclick="$(\'#subTradersData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer;" onclick="$(\'#subTradersData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle"  class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
			//width 2600
				$tableStr ='<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="subTradersTbl">
					<thead><tr  class="table-row">
						<th   class="table-cell">'.lang(ptitle('Trader ID')).'</th>
						'.($displayForex==1? '
						<th  class="table-cell">'.lang(ptitle('Sub Traders')).'</th>
						':'').'
						<th  class="table-cell">'.lang(ptitle('Trader Alias')).'</th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th  class="table-cell">'.lang(ptitle('Email')).'</th>' : '' ) . '
						<th  class="table-cell">'.lang('Registration Date').'</th>
						<th  class="table-cell">'.lang(ptitle('Trader Status')).'</th>
						<th  class="table-cell">'.lang('Country').'</th>
						<th  class="table-cell">'.lang('Affiliate ID').'</th>
						<th  class="table-cell">'.lang('Affiliate Username').'</th>
						<th  class="table-cell">'.lang('Merchant ID').'</th>
						<th  class="table-cell">'.lang('Merchant Name').'</th>
						<th   class="table-cell" style="text-align: left;">'.lang('Creative ID').'</th>
						<th  class="table-cell" style="text-align: left;">'.lang('Creative Name').'</th>
						<th  class="table-cell">'.lang('Type').'</th>
						<th  class="table-cell">'.lang('Creative Language').'</th>
						<th  class="table-cell">'.lang('Profile ID').'</th>
						<th  class="table-cell">'.lang('Status').'</th>
						<th  class="table-cell">'.lang('Param').'</th>
						<th  class="table-cell">'.lang('Param2').'</th>
                        <th  class="table-cell">' . lang('Transaction ID') . '</th>
						<th  class="table-cell">'.($type == "deposit" ? lang('Deposit Date') : lang('First Deposit')).'</th>
						<th  class="table-cell">'.lang('FTD Amount').'</th>
						<th  class="table-cell">'.lang('Total Next Deposits').'</th>
						<th  class="table-cell">'.lang('Next Deposits').'</th>
						<th  class="table-cell">'.lang('Total Deposits').'</th>
						<th  class="table-cell">'.($type == "deposit" ? lang('Deposit Amount') : lang('Deposit Amount')).'</th>
						<th  class="table-cell">'.lang('Volume').'</th>
						<th  class="table-cell">'.lang('Bonus Amount').'</th>
						<th  class="table-cell">'.lang('Withdrawal Amount').'</th>
						<th  class="table-cell">'.lang('ChargeBack Amount').'</th>
						<th  class="table-cell">'.lang(ptitle('Net Revenue')).'</th>
						'.($set->deal_pnl==1 ? '
						<th  class="table-cell">'.lang(ptitle('PNL')).'</th>':'').'
						<th  class="table-cell">'.lang(ptitle('Trades')).'</th> '
						. ($displayForex==1  ? 
						'<th  class="table-cell">'.lang(ptitle('Lots')).'</th>' : '' ) . '
						<th  class="table-cell">'.lang('Sale Status').'</th>
						'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th   class="table-cell">'.lang('Last Sale Note Date').'</th>
						<th  class="table-cell">'.lang('Last Sale Note').'</th>' : '' ).'
						<th  class="table-cell">'.lang('Last Time Active').'</th>
						<th  class="table-cell">'.lang('Commission').'</th>
						<th  class="table-cell">'.lang('Admin Notes').'</th>
					</tr></thead>
					<tfoot>
						<th></th>
						<th></th>
						'.($displayForex==1? '
						<th></th>':'').'
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
						'.($set->deal_pnl==1 ? '
						<th style="text-align: left;">'.price($totalTotalPNL).'</th>':'').'
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
				</table>
				<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
				<script>
				$(document).ready(function(){
					try{
						thead = $("thead").html();
						tfoot = $("tfoot").html();
						txt = "<table id=\'subTradersData\' class=\'mdlReportFieldsData\'>";
						txt += "<thead>" + thead + "</thead>";
						txt += "<tbody>";
						$($("#subTradersTbl")[0].config.rowsCopy).each(function() {
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
											
											saveReportToMyFav(name, \'Sub Trader\',user,level,type);
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
				
				$set->content.=$tableStr.'
			</div>'.getPager();
			
	//MODAL
		$myReport = lang("Sub Traders");
		include "common/ReportFieldsModal.php";		
			
//excelExporter($tableStr,'SubTraders');		
		theme();

?>