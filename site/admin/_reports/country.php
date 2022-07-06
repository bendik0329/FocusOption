<?php

//Prevent direct browsing of report
if(!defined('DirectBrowse')) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/admin" );
}


$countryArray = [];
		$set->pageTitle = lang('Country Report');
                $where = ' 1 = 1 ';
            
                /**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
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
		
		//general merchants information
		$merchantww = getMerchants($merchant_id ? $merchant_id:0,1);
		
		$formula = $merchantww['rev_formula'];
		$merchantID = $merchantww['id'];
		$merchantName = strtolower($merchantww['name']);
		
		
		// clicks and impressions
		$where_main = $where;
		 $where_main =  str_replace('affiliate_id','t.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','t.merchant_id', $where_main) ;
		 $where_main =  str_replace('profile_id','t.profile_id', $where_main) ;
		 
		 
		 $sql = "select sum(t.clicks) as total_clicks, sum(t.views) as total_views, max(t.country_id) as country_id,max(t.type) as type, max(m.name) as merchant_name from traffic t "
		     ." INNER JOIN merchants m on m.id = t.merchant_id where " . $where_main . " AND t.rdate BETWEEN '" . $from . "' AND '" . $to . "'
		 " . (isset($country_id) && !empty($country_id) ? ' AND t.country_id = "'.$country_id.'"' :'')
			.	" GROUP BY t.country_id"	 ;
		 
		
		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
			$trafficRow['country_id'] = $trafficRow['country_id']=='' ? '-' : $trafficRow['country_id'];
			
					 if (!isset($countryArray[$trafficRow['country_id']]))  {
							$countryArray[$trafficRow['country_id']]['clicks'] = $trafficRow['total_clicks'];
							$countryArray[$trafficRow['country_id']]['views'] = $trafficRow['total_views'];
					}
					else{
							$countryArray[$trafficRow['country_id']]['clicks'] =  $trafficRow['total_clicks'];
							$countryArray[$trafficRow['country_id']]['views'] = $trafficRow['total_views'];
					}
					
					$countryArray[$trafficRow['country_id']]['country'] = $trafficRow['country_id'];
					$countryArray[$trafficRow['country_id']]['type'] = $trafficRow['type'];
					$countryArray[$trafficRow['country_id']]['merchant'] = $trafficRow['merchant_name'];					
		}
		
		// registration (leads + demo + real)
		$where_reg = $where;
		$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
		 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
		 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
		
				   $sql = "SELECT dg.*,m.name as merchant_name FROM data_reg dg"
								." INNER JOIN merchants m on m.id = dg.merchant_id "
                         . "WHERE " . $where . " AND dg.rdate BETWEEN '" . $from . "' AND '" . $to . "'
						 " . (isset($country_id) && !empty($country_id) ? ' AND dg.country = "'.$country_id.'"' :'');
					
					$regqq = function_mysql_query($sql,__FILE__);
					
					$arrTierCplCountCommissionParams = [];
						
					while ($regww = mysql_fetch_assoc($regqq)) {
						$regww['country'] = $regww['country']=='' ? '-' : $regww['country'];
						
						$countryArray[$regww['country']]['country'] = $regww['country'];
						$countryArray[$regww['country']]['type'] = $regww['type'];
					    $countryArray[$regww['country']]['merchant'] = $regww['merchant_name'];				
						
						$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
						$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
						if ($regww['type'] == "lead"){
								//$totalLeads++; 
								$countryArray[$regww['country']]['leads'] += 1 ;
							}
						if ($regww['type'] == "demo"){
								// $totalDemo++;
								$countryArray[$regww['country']]['demo'] += 1;
						}
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
								
								$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
								$countryArray[$regww['country']]['totalCom'] += $totalCom;
							} else {
								// TIER CPL.
								if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
								} else {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
										'from'                => $from,
										'to'                  => $to,
										'onlyRevShare'        => 0,
										'groupId'             => (is_null($group_id ? -1 : $group_id)),
										'arrDealTypeDefaults' => $arrDealTypeDefaults,
										'country'=> $regww['country'],
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
							// $totalReal++;
							$countryArray[$regww['country']]['real'] += 1;
						}
				   }
				  
					/* echo "<pre>";
					print_r($countryArray);
					echo "</pre>";
					die; */
					
				     // TIER CPL.
                    foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
                        $totalCom = getCommission(
                            $arrParams['from'], 
                            $arrParams['to'], 
                            $arrParams['onlyRevShare'], 
                            $arrParams['groupId'], 
                            $arrParams['arrDealTypeDefaults'], 
                            $arrParams['arrTmp']
                        );
                      $countryArray[$arrParams['country']]['totalCom'] += $totalCom;
					    unset($intAffId, $arrParams);
                    }
					
			
					
					//FTDs
					$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id),0,0,0,0,((isset($country_id) && !empty($country_id)?$country_id:'')));
				
                    foreach ($arrFtds as $arrFtd) {
				
				
						// if($arrFtd['country'] == "" || $arrFtd['country'] == 'Any' || $arrFtd['country'] == 0)
								// continue;
						$arrFtd['country'] = $arrFtd['country']=='' ? '-' : $arrFtd['country'];
						
						
						$real_ftd++;
						$countryArray[$arrFtd['country']]['real_ftd'] += 1;
                        
						$real_ftd_amount = $arrFtd['amount'];
                        $countryArray[$arrFtd['country']]['real_ftd_amount'] += $arrFtd['amount'];
                        
						$beforeNewFTD = $ftd;
                        getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
                      
                        if ($beforeNewFTD != $ftd) {
							$ftd_amount = $real_ftd_amount;
                            $arrFtd['isFTD'] = true;
                            $totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
							$countryArray[$arrFtd['country']]['totalCom'] += $totalCom;
							$countryArray[$arrFtd['country']]['ftd'] +=1;
							$countryArray[$arrFtd['country']]['ftd_amount'] += $ftd_amount;
                        }
						unset($arrFtd);
                    }
			
					
					//SALES
					$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 ." INNER JOIN merchants_creative mc on mc.id= tb1.banner_id "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
					 . "WHERE  mc.valid=1 and tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($country_id) && !empty($country_id) ? ' AND data_reg.country = "'.$country_id.'"' :'') ;
				
                    $salesqq = function_mysql_query($sql,__FILE__);
                    
                    while ($salesww = mysql_fetch_assoc($salesqq)) {
					
						$salesww['country']=='' ? '-' : $salesww['country'];
						
                        //if ($salesww['type'] == 1 || $salesww['type'] == 'deposit') { // OLD.
						if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
                            $depositingAccounts++;
							$countryArray[$salesww['country']]['depositingAccounts'] += 1;
							
                            $sumDeposits = $salesww['amount'];
							$countryArray[$salesww['country']]['sumDeposits'] += $salesww['amount'];
							
							// $depositsAmount+=$salesww['amount'];
                        }
                        
                        if ($salesww['data_sales_type'] == "bonus") {
								$bonus = $salesww['amount'];
								$countryArray[$salesww['country']]['bonus'] += $salesww['amount'];
						}
                        if ($salesww['data_sales_type'] == "withdrawal"){ 
								$withdrawal = $salesww['amount'];
								$countryArray[$salesww['country']]['withdrawal'] += $salesww['amount'];
						}
                        if ($salesww['data_sales_type'] == "chargeback"){
								$chargeback = $salesww['amount'];
								$countryArray[$salesww['country']]['chargeback'] += $salesww['amount'];
						}
                        if ($salesww['data_sales_type'] == 'volume') {
                            $volume = $salesww['amount'];
							
							$countryArray[$salesww['country']]['volume'] += $volume;
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
                            
                            $totalCom = getCommission(
                                $from, 
                                $to, 
                                0, 
                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                $arrDealTypeDefaults, 
                                $arrTmp
                            );

							$countryArray[$salesww['country']]['totalCom'] += $totalCom;
                        }
						
					
			
								
							//REVENUE   						// loop on merchants    								// loop on affiliates
								// start of data_stats (revenue) loop
								$merchantww = 	getMerchants($salesww['merchant_id'],0);
								if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {
									
								
									// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
									$withd = $salesww['data_sales_type'] == "withdrawal"?$withdrawal:0;
									$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$withd,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
									
									//$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
									$countryArray[$salesww['country']]['netRevenue'] += $netRevenue;
									
									
									
										$row                 = array();
										$row['merchant_id']  = $merchantww['id'];
										$row['affiliate_id'] = $salesww['affiliate_id'];
										$row['banner_id']    = 0;
										$row['rdate']        = $salesww['rdate'];
										$row['amount']       = ($netRevenue);
										$row['isFTD']        = false;
									  
									
									  // $totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									  $totalCom           = getCommission($salesww['rdate'],$salesww['rdate'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									
										if ($withd<>0) {
										// echo '<br>'. ($totalCom).'<br>';
										// var_dump($salesww);
										}
								
									  
									
										// echo ($totalCom.'<br>');
									  
									  // die();
									  
									$countryArray[$salesww['country']]['totalCom'] += $totalCom;
									  
								}
							// end of data_stats (revenue) loop
					
					
						// end of data_sales loop
                    }
					
						
						$sql ="SELECT DISTINCT  ds.affiliate_id, ds.merchant_id,m.producttype as producttype, dg.country as country FROM data_stats ds INNER JOIN data_reg dg ON dg.trader_id = ds.trader_id INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
													 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" . (isset($country_id) && !empty($country_id) ? ' AND dg.country = "'.$country_id.'"' :'');
													 
						$revqq  = function_mysql_query($sql,__FILE__); 					 
				
						while ($revww = mysql_fetch_assoc($revqq)) {
									
									$revww['country'] = $revww['country']=='' ? '-' : $revww['country'];
									
									
									$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
									$intTotalRevenue  = 0;
									
									foreach ($arrRevenueRanges as $arrRange2) {
										$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
													 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
													 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
										
										$intCurrentRevenue = getRevenue($strRevWhere, $revww['producttype']);
										
										$intTotalRevenue    += $intCurrentRevenue;
										$row                 = array();
										$row['merchant_id']  = $revww['merchant_id'];
										$row['affiliate_id'] = $revww['affiliate_id'];
										$row['banner_id']    = 0;
										$row['rdate']        = $arrRange2['from'];
										$row['amount']       = $intCurrentRevenue;
										$row['isFTD']        = false;
									  
									  $totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
										
										$countryArray[$revww['country']]['totalCom'] += $totalCom;
										
										unset($arrRange2, $strRevWhere);
									}
									
									$netRevenue = $intTotalRevenue;
									$countryArray[$revww['country']]['netRevenue'] += $netRevenue;
									
						}
					
					
					$sql = "select * from merchants where producttype = 'Forex' and valid =1";
					$totalqq = function_mysql_query($sql,__FILE__);
					
					while ($merchantww  = mysql_fetch_assoc($totalqq)) {
						
						
						
                        $sql = 'SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO, dg.country as country FROM data_stats ds '
								. ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
                                . 'WHERE ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
								. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
								. " and ds.merchant_id = " . $merchantww['id']
								 . (isset($country_id) && !empty($country_id) ? ' AND dg.country = "'.$country_id.'"' :'');
                        
                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                        
                        while($ts = mysql_fetch_assoc($traderStatsQ)){
						
                                $spreadAmount = $ts['totalSpread'];
								$countryArray[$ts['country']]['totalSpread'] += $ts['totalSpread'];
                                $volume = $ts['totalTO'];
								$ts['country'] = $ts['country']=='' ? '-' : $ts['country'];
								
								$countryArray[$ts['country']]['volume'] += $ts['totalTO'];
								
                                $pnl = $ts['totalPnl'];
								$countryArray[$ts['country']]['pnl'] += $ts['totalPnl'];
                        }
						
						
	$totalLots  = 0;
											
							
							
						$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id,dg.country as country FROM data_stats ds '
                                         . ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
										 . 'WHERE  ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
                                            . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
											. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
											. " and ds.merchant_id = " . $merchantww['id']
												 . (isset($country_id) && !empty($country_id) ? ' AND dg.country = "'.$country_id.'"' :'');
											// die($sql);
                           
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d');
                                        while($ts = mysql_fetch_assoc($traderStatsQ)){
											if($ts['affiliate_id']==null) {
													continue;
											}
											$ts['country'] = $ts['country']=='' ? '-' : $ts['country'];
							
											// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
													$totalLots  = $ts['totalTurnOver'];
													// echo $totalLots
														$tradersProccessedForLots[$merchantww['id'] . '-' . $ts['trader_id']] = $trafficRow['id'] . '-' . $ts['trader_id'];
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
													$totalCom = $a;
													$countryArray[$ts['country']]['totalCom'] += $totalCom;
											// }
										}
				     }
					
				/*  echo '<pre>';
				var_dump($countryArray);
				echo '</pre>';
				die();  */
		
			
					//DISPLAY Report
					foreach($countryArray as $data){
						if ($data['views']>0 || $data['clicks']>0 || $data['leads'] >0 || $data['demo'] >0 || $data['real'] >0 
						 || $data['depositingAccounts'] >0 
						 || $data['real_ftd'] >0 
						 || $data['ftd'] >0 
						 || $data['ftd_amount'] >0 
						 || $data['real_ftd_amount'] >0 
						 || $data['chargeback'] >0 
						 || $data['withdrawal'] >0 
						 || $data['bonus'] >0 
						 || $data['totalCom'] >0 
						 || $data['netRevenue'] >0 
						 || $data['volume'] >0 
						){
							$country = $allCountriesArray[$data['country']];
							// if($country == "Any") $country = "";
										$listReport .= '
								<tr>
									<td style="text-align: left;" title="'.$country.'">'.$country.'</td>
								
								
									<td>'.@number_format($data['views'],0).'</td>
									<td>'.@number_format($data['clicks'],0).'</td>
									<td>'.@number_format(($data['clicks']/$data['views'])*100,2).' %</td>
									<td>'.@number_format(($data['real']/$data['clicks'])*100,2).' %</td>
									<td>'.@number_format(($data['ftd']/$data['clicks'])*100,2).' %</td>
									<td>'.@price($data['totalCom']/$data['clicks']).'</td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=lead&country_id='.$data['country'].'">'.$data['leads'].'</a></td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=demo&country_id='.$data['country'].'">'.$data['demo'].'</a></td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=real&country_id='.$data['country'].'">'.$data['real'].'</a></td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=ftd&country_id='.$data['country'].'">'.$data['ftd'].'</a></td>
									<td>'.price($data['ftd_amount']).'</td>
									<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'type=totalftd&country_id='.$data['country'].'">'.$data['real_ftd'].'</a></td>
									<td>'.price($data['real_ftd_amount']).'</td>
									<td><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=deposit&country_id='.$data['country'].'">'.$data['depositingAccounts'].'</a></td>
									<td>'.price($data['sumDeposits']).'</td>
									<td style="text-align: center;">'.price($data['volume']).'</td>
									<td>'.price($data['bonus']).'</td>
									<td>'.price($data['withdrawal']).'</td>
									<td>'.price($data['chargeback']).'</td>
									<td style="text-align: center;">'.price($data['netRevenue']).'</td>
									<td>'.price($data['totalCom']).'</td>
								</tr>';
								
								$totalImpressions += $data['views'];
								$totalClicks += $data['clicks'];
								$totalLeadsAccounts += $data['leads'];
								$totalDemoAccounts += $data['demo'];
								$totalRealAccounts += $data['real'];
								$totalFTD += $data['ftd'];
								$totalDeposits += $data['depositingAccounts'];
								$totalFTDAmount += $data['ftd_amount'];
								$totalDepositAmount += $data['sumDeposits'];
								$totalVolume += $data['volume'];
								$totalBonusAmount += $data['bonus'];
								$totalWithdrawalAmount += $data['withdrawal'];
								$totalChargeBackAmount += $data['chargeback'];
								$totalNetRevenue += $data['netRevenue'];
								$totalComs += $data['totalCom'];
								$totalRealFtd += $data['real_ftd'];
								$totalRealFtdAmount += $data['real_ftd_amount'];
								
								$l++;
							// echo $ftd_amount.'<br>';
							$ftd_amount = $real_ftd_amount = 0;
							// $totalLeads = $totalDemo = $totalReal = $ftd = $depositingAccounts = $ftd_amount = $sumDeposits = $volume = $bonus = $withdrawal = $chargeback= $netRevenue= $totalCom= $real_ftd= $real_ftd_amount = 0;
						}
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
			<input type="hidden" name="act" value="country" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Country').'</td>
					<td>'.lang('Affiliate ID').'</td>
					
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td>
					
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
						
						<th style="text-align: left;">'.lang('Country').'</th>
						
						
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
					
						<th>'.$totalViews.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalViews)*100,2).' %</th>
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
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
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
			
		excelExporter($tableStr, 'Country');
		$set->content.=$tableStr.'</div>'.getPager();
		theme();

?>