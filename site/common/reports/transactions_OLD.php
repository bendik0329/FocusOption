<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}
$countriesLong = getLongCountries();
 $set->pageTitle = lang(ptitle('Transaction Report'));
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
                $lots = 0;
                $totalTotalCom  = 0;
                $arrTradersPerMerchants = [];
                $intTmpMerchantId = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;
                $strWhereMerchantId = isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id . ' ' : '';
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                $resourceWallets = function_mysql_query($sql,__FILE__);
                
           
                
                
                $sql = "SELECT * FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                // die ($sql);
				$resourceMerchanrs = function_mysql_query($sql,__FILE__);
		
		// $resourceMerchants = $resourceMerchanrs;
				// $arrMerchant = mysql_fetch_assoc($resourceMerchants);
				 $arrMerchant = array();
				
                while ($arrMerchantRow = mysql_fetch_assoc($resourceMerchanrs)) {
					
					$arrMerchant = $arrMerchantRow;
					
                // var_dump($arrMerchantRow);
				// die();
                    $int_merchant_id = empty($intTmpMerchantId) ? $arrMerchantRow['id'] : $intTmpMerchantId;
                
                    $where = ' AND merchant_id = ' . $int_merchant_id;
                    
					
					// die();
						$hidedemo= true;
						
				if ($showdemo=='on' ) {
						$hidedemo =false;
						
					}	

					
					// die ('hide: ' . $ignoredemo);
					
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($group_id) $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
					if ($param) $where .= " AND freeParam='".$param."' ";
					if ($param2) $where .= " AND freeParam2='".$param2."' ";
					if ($hidedemo) $where .= " AND not type='demo' ";
                    
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
                                $from, $to, $affiliate_id, $arrMerchantRow['id'], $arrMerchantRow['wallet_id'], 
                                $group_id, $banner_id, $profile_id, '', $trader_id
                            );
                            
                            if ($type == 'ftd') {
                                foreach ($arrTotalFtds as $arrRes) {
									
				/* 						
									   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ;
						
				 */		
						
									
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
									
				/* 							
									   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ;
				 */		
						
						
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
							
							$ftdsTraderIds = "0";
							 foreach($arrTotalFtds as $arrRes) {
				/* 				   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ;
				 */					
									
								 
								if (empty($ftdsTraderIds)){
									$ftdsTraderIds = $arrRes['trader_id'];
								 }
								$ftdsTraderIds .= ",".$arrRes['trader_id'];
							 }
							
							
							
								$where = str_replace('merchant_id', 'dr.merchant_id', $where);
                            $where = str_replace('trader_id', 'dr.trader_id', $where);
                            $where = str_replace('group_id', 'dr.group_id', $where);
                            $where = str_replace('affiliate_id', 'dr.affiliate_id', $where);
                            $where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            $where = str_replace('banner_id', 'dr.banner_id', $where);
                            $where = str_replace('freeParam', 'dr.freeParam', $where);
                            $where = str_replace('freeParam2', 'dr.freeParam2', $where);
                            $where = str_replace('type', 'dr.type', $where);
                            
                   
							$sql = "SELECT 
							ds.id,
							ds.rdate,
							ds.trader_id,
							trim(ds.tranz_id) as tranz_id,
							ds.type,
							ds.amount,
							ds.merchant_id,
							ds.currency , 
							dr.rdate as registration_date,
							dr.ctag,
							dr.affiliate_id,
							dr.banner_id,
							dr.group_id,
							dr.profile_id,
							dr.country,
							dr.phone,
							dr.trader_alias,
							dr.type as reg_type,
							dr.freeParam,
							dr.freeParam2,
							dr.uid,
							dr.saleStatus,
							dr.lastTimeActive,
							dr.lastSaleNoteDate,
							dr.lastSaleNote,
							dr.status,
							dr.email,
							dr.campaign_id,
							dr.couponName
							
							FROM data_sales AS ds
                                  inner join data_reg dr on ds.trader_id = dr.trader_id
								  and ds.merchant_id = dr.merchant_id
                                    WHERE 2=2 and " . $globalWhere . " 
											 ds.trader_id in (".  $ftdsTraderIds . ") "
                                            . " AND ds.type = 'deposit'  "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " and ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . $where
											
									. " group by ds.tranz_id , ds.merchant_id "
                                    . " ORDER BY ds.rdate ASC;";
									
								// die ($sql);	
			    
			// } elseif ($type == 'deposit' || $type == 'withdrawal' || $type == 'bonus' || $type == 'alltransactions') {
			} else{
                            
							$where = str_replace('merchant_id', 'dr.merchant_id', $where);
                            $where = str_replace('trader_id', 'dr.trader_id', $where);
                            $where = str_replace('group_id', 'dr.group_id', $where);
                            $where = str_replace('affiliate_id', 'dr.affiliate_id', $where);
                            $where = str_replace('banner_id', 'dr.banner_id', $where);
                            $where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            $where = str_replace('type', 'dr.type', $where);
                            $where = str_replace('country', 'dr.country', $where);
                   
							$sql = "SELECT 
							ds.id,
							ds.rdate,
							ds.trader_id,
							ds.tranz_id,
							ds.type,
							ds.amount,
							ds.merchant_id,
							ds.type as salesType,
							ds.currency , 
							dr.rdate as registration_date,
							dr.ctag,
							dr.affiliate_id,
							dr.banner_id,
							dr.group_id,
							dr.profile_id,
							dr.country,
							dr.phone,
							dr.trader_alias,
							dr.type as reg_type,
							dr.freeParam,
							dr.freeParam2,
							dr.uid,
							dr.saleStatus,
							dr.lastTimeActive,
							dr.lastSaleNoteDate,
							dr.lastSaleNote,
							dr.status,
							dr.email,
							dr.campaign_id,
							dr.couponName
							
							FROM data_sales AS ds
                                  inner join data_reg dr on 
								  ds.trader_id = dr.trader_id
								  and ds.merchant_id = dr.merchant_id
								  
                                    WHERE 2=2 and " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            .($type == 'alltransactions' || $type == '' ? "" : " AND ds.type = '".$type."' " )
											. " AND ds.type != 'volume'  ". " AND ds.type != 'PNL'  "
                                            . $where
											
									. " group by ds.tranz_id , ds.merchant_id "
                                    . " ORDER BY ds.rdate ASC;";
							}
		
						
							
							
		/* 
		else {
							// die ('gerger');
                            $sql = "SELECT * FROM data_sales "
                                    . "WHERE 1 = 1 " . $where . " AND " . $globalWhere . " rdate BETWEEN '" . $from . "' AND '" . $to . "' AND status <> 'frozen' and type='real' "
                                    . " "
                                    . "ORDER BY id DESC;";

						die ($sql);
						} */
						// die ($sql);
                            $resource = function_mysql_query($sql,__FILE__);
               while ($arrRes = mysql_fetch_assoc($resource)) {
				   
				   // var_dump($arrRes);
				   // die();
				   
               // foreach ($arrResultSet as $arrRes) {
					
			
					$ftdAmount = 0;
			
				// if ($type=="alltransactions")
						// $type =  $arrRes['salesType'];
					
					
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
                        $type == 'bonus' || 
                        $type == 'alltransactions' || 
                        $type == 'chargeback' || 
                        $type == 'withdrawal'
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
                        // $totalCom = empty($arrRes['totalCom']) ? $totalCom : $arrRes['totalCom'];
                        
                 
                        $arrRes['trader_alias'] = $arrRes['trader_alias'];
                        $arrRes['email'] = $arrRes['email'];
                        $arrRes['country']      = $arrRes['country'];
                        $arrRes['registration_date']        =  $arrRes['registration_date'];
                        $arrRes['rdate']        = $arrRes['rdate'];
                        $arrRes['salesType']    = $arrRes['type'];
                        $arrRes['regtype']         = $arrRes['reg_type'];
                        $arrRes['banner_id']    = $arrRes['banner_id'];
                        $arrRes['status']       = $arrRes['status'];
                        $arrRes['profile_id']   = $arrRes['profile_id'];
                        $arrRes['freeParam']    = $arrRes['freeParam'];
                        $arrRes['freeParam2']    = $arrRes['freeParam2'];
                        $arrRes['saleStatus']   = $arrRes['saleStatus'];
                        $arrRes['lastTimeActive']   = $arrRes['lastTimeActive'];
                        unset($intTmpTraderId, $intTmpGroupId, $strTmpWhere, $intTmpMerchantId);
                    }
                    
                    // var_dump ($arrRes);
					// die();
					
					   if (($type=='totalftd' || $type=='ftd' ) and   in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
                        continue;
						// $arrTradersPerMerchants[] = $arrRes['trader_id'];
					   }
						
                    $depositAmount = 0;
					
				
                    $bannerInfo = getCreativeInfo($arrRes['banner_id']);
					
                            $arrRes['tranz_id'] = $arrRes['tranz_id'];
							if ($arrRes['tranz_id'] !=''  && $ftdAmount==0) {
								$ftdAmount = $arrRes['amount'];
								
								$firstDeposit['rdate'] = $arrRes['rdate'];
								$firstDeposit['id'] = $arrAmount['id'];
							}
                            
                            if ($arrAmount['type'] == 'deposit') {
                             //   $depositAmount += $arrAmount['amount'];
                               $total_deposits++;
                            } elseif ($arrAmount['type'] == 'bonus') {
                              //  $bonusAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'withdrawal') {
                              //  $withdrawalAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'chargeback') {
                            //    $chargebackAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'volume') {
                                //$volumeAmount += $arrAmount['amount'];
                          //      $totalTraders++;
								
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
                            unset($arrAmount);
                   
                    if ($type == 'ftd' || $type == 'totalftd') {
                     
                        $ftdUsers = '';
                        
                        $arrTotalFtds = getTotalFtds(
                            $from, $to, $arrRes['affiliate_id'], $arrRes['merchant_id'], $arrMerchant['wallet_id'], 
                            $arrRes['group_id'], $arrRes['banner_id'], $arrRes['profile_id'], '', $arrRes['trader_id']
                        );
                        
					/* }
					if (false) */
                        foreach ($arrTotalFtds as $arrResLocal) {
				/* 				   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrResLocal = $arrTotalFtds[$ftdCount] ;
				 */					
									
							
                            $beforeNewFTD = $ftd;
                            getFtdByDealType($arrRes['merchant_id'], $arrResLocal, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);

                            if ($beforeNewFTD != $ftd) {
                                $firstDeposit = $arrResLocal;
								
                                $ftdAmount = $arrResLocal['amount'];
                                $arrResLocal['isFTD'] = true;
                                
                                // Old version.
                                //$totalCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrResLocal);
                                
								
								
                                // New version.
                                //if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
									$totalCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrResLocal);
                                    
									// var($totalCom);
                                //}
                            }
                            unset($arrResLocal);
                        }
                    
			   }

                    if ($arrRes['reg_type'] == 'real') {
                        $color = 'green';
                    } elseif ($arrRes['reg_type'] == 'demo') {
                        $color = 'red';
                    } elseif ($arrRes['reg_type'] == 'lead') {
                        $color = 'black';
                    }
                  

                    // AFFILIATE info retrieval.
                    /* $sql = "SELECT id,group_id,username FROM affiliates AS aff "
                            . " WHERE  id = " . $arrRes['affiliate_id']
                            . " LIMIT 0, 1;";
// die ($sql);
                    $affInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__)); */
					$affInfo = getAffiliateRow($arrRes['affiliate_id']);

                    // Check trader.
					$reason="";
                    $sql = 'SELECT * FROM payments_details '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['id'] 
                            . '  LIMIT 0, 1;';
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (empty($chkTrader)) {
						$sql = 'SELECT * FROM traders_tag '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['id'] 
                            . '  LIMIT 0, 1;';
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (!empty($chkTrader)) {
						$reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
					}
					
					}else {
					$reason = chkTrader['reason'];
						
					}
						

 $arrRes['amount'] =  strtolower($arrRes['salesType'])=='deposit' ? $arrRes['amount'] : $arrRes['amount']*-1;
							
                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].'</td>
                            <td>'.$arrRes['trader_alias'].'</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.$arrRes['email'].'</td>' : '' ) . '
                            <td>'.date("d/m/Y", strtotime($arrRes['registration_date'])) .'</td>
                            <td><span style="color: '.$color.';">'.$arrRes['reg_type'].'</span></td>
                            <td>'.$countriesLong[$arrRes['country']].'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
                            <td><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
                            <td>'.$arrMerchant['id'].'</td>
                            <td>'.strtoupper($arrMerchant['name']).'</td>
                            <td style="text-align: left;">'.$bannerInfo['id'].'</td>
                            <td style="text-align: left;">'.$bannerInfo['title'].'</td>
                            <td>'.$bannerInfo['type'].'</td>
                            <td>'.$bannerInfo['language_name'].'</td>
                            <td>'.$arrRes['profile_id'].'</td>
                            <td><span>'.($type=='totalftd' || $type=='ftd' ? strtoupper($type) :  ucwords($arrRes['salesType'])).'</span></td>
                            <td>'.$arrRes['status'].'</td>
                            <td>'.$arrRes['freeParam'].'</td>
                            <td>'.$arrRes['freeParam2'].'</td>
                            <td>' .  $arrRes['tranz_id'] . '</td>
                            <td>'.date("d/m/Y", strtotime($arrRes['rdate'])) .'</td>
                            <td>'.price($arrRes['amount']).'</td>
                            <td>'.ucwords($arrRes['saleStatus']).'</td>
							'.($set->displayLastMessageFieldsOnReports ==1 ? '
                            <td>'.$arrRes['lastSaleNoteDate'].'</td>
                            <td>'.$arrRes['lastSaleNote'].'</td>':'').'
							
							<td>'.($arrRes['lastTimeActive']=='1969-12-31 23:00:00' || $arrRes['lastTimeActive'] == '0000-00-00 00:00:00' ? '-' : $arrRes['lastTimeActive']).'</td>
                            <!--td>'.price($totalCom).'</td-->
                            <td>'.$chkTrader['reason'].'</td>
                        </tr>';
                    
                    if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$arrTradersPerMerchants)) {
							$arrTradersPerMerchants[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
							
					// if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
                        // $arrTradersPerMerchants[] = $arrRes['trader_id'];
                        // $totalFTD += $ftdAmount;
                        // $totalNetRevenue += $netRevenue;
                        $totalTotalCom += $totalCom;
                    }
				$totalAmounts += $arrRes['amount'];
                
                    $l++;
					
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$totalTraders=0;
                }
                        
			   }
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form action="'.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="transactions" />
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
							<select name="type" style="width: 110px;">
								<option value="alltransactions" '.($type == "alltransactions" ? 'selected' : '').'>'.lang(ptitle('All Transactions')).'</option>
                                <option value="bonus" '.($type == "bonus" ? 'selected' : '').'>'.lang('Bonus').'</option>
                                <option value="chargeback" '.($type == "chargeback" ? 'selected' : '').'>'.lang('Chargeback').'</option>
								<option value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option>
								<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>
                                <option value="totalftd" '.($type == "totalftd" ? 'selected' : '').'>'.lang('Total FTD').'</option>
                                <option value="withdrawal" '.($type == "withdrawal" ? 'selected' : '').'>'.lang('Withdrawal').'</option>
							</select>
						</td>
						<td><input type="checkbox" name="showdemo"  '.($showdemo ? 'checked="checked"' : '').'  />'.lang('Show Demo Traders').'</td>
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
						<!--th>'.lang('FTD Date').'</th>
						<th>'.lang('FTD Amount').'</th-->
						<th>'.lang('Transaction Type').'</th>
						<th>'.lang('Status').'</th>
						<th>'.lang('Param').'</th>
						<th>'.lang('Param2').'</th>
                        <th>' . lang('Transaction ID') . '</th>
						<th>'. lang('Transaction Date') .'</th>
						<th>'.lang('Amount').'</th>
						<th>'.lang('Sale Status').'</th>
					'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th>'.lang('Last Sale Note Date').'</th>
						<th>'.lang('Last Sale Note').'</th>' : '' ).'
						<th>'.lang('Last Time Active').'</th>
						<!--th>'.lang('Commission').'</th-->
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
						<th></th>
						
						<th style="text-align: left;">'.price($totalAmounts).'</th>
						<th></th>
				'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th></th>
						<th></th>' : '' ).'
						<th></th>
						<!--th style="text-align: left;">'.price($totalTotalCom).'</th-->
						<th></th>
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>';
				
				$set->content.=$tableStr.'
			</div>'.getPager();
			
			
excelExporter($tableStr,'Transactions');		
		theme();

?>