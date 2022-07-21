<?php


if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLprefix."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/affiliate" );
}

$userlevel = 'affiliate';
		$pageTitle = lang(ptitle('Trader Report'));
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
			
		if (strpos($set->reportsToHide, 'trader') > 0) {
			
			if ($set->SSLprefix = ltrim(ltrim($set->SSLprefix,'affiliate'),'/'))
                            _goto($set->SSLprefix.'affiliate/');
						else
                            _goto($set->SSLprefix.'/');
        }
		
		$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "Trader_data_" . date('YmdHis');
		
		// echo 'rth: ' . ($set->reportsToHide). '------';
		
		
                //$group_id     = $set->userInfo['group_id']; // '$group_id' MUST be commented in affiliate/reports.
                $affiliate_id   = $set->userInfo['id'];
                
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
				
               
                // List of wallets.
                $arrAllTraders = [];
                $merchant_id = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;
                
				
				//profile names
				$sql = "select id,name from affiliates_profiles where valid =1";
				$qqProfiles = function_mysql_query($sql);
				$listProfiles = array();
				while($wwProfiles = mysql_fetch_assoc($qqProfiles)){
					$listProfiles[$wwProfiles['id']] = $wwProfiles['name'];
				}
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id,id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                $resourceWallets = function_mysql_query($sql,__FILE__);
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
				
				$affiliatesRowsArray = array();
				$creativesRowsArray = array();
				
                
				
				
				$allbrabdrsc = function_mysql_query($sql,__FILE__);
				$LowestLevelDeal = 'ALL';
				while ($brandsRow = mysql_fetch_assoc($allbrabdrsc)) {
				/* var_dump($dealsArray);
				echo '<Br><br>';
				var_dump($brandsRow);
				die(); */
				foreach ($dealsArray as $dealItem=>$value) {
					// echo $brandsRow['id'].'<br>';
					// echo $dealItem.'<br>';
					// echo $value.'<br>';
					// die();
					if ($brandsRow['id']==$dealItem) {
						
						$LowestLevelDeal = getLowestLevelDeal($LowestLevelDeal, $value);
						// die ($LowestLevelDeal);
						break;
					}
					}
				}
			   $deal = $LowestLevelDeal;
		
                
// var_dump($deal);
// die();				
				
				$merchantsArray = array();
				$merchantsAr = getMerchants($merchant_id,1);
				foreach ($merchantsAr as $arrMerchant) {
					
						$allTranz = array();
						 $loopedMerchant_ID = $arrMerchant['id'];
						 
						 
						 
					
                    $where = '';
                    
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($group_id) $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
                    if ($param){
						$param =  trim($param);
					$where .= " AND freeParam='".$param."' ";
					}
                    
                    if ($param2) $where .= " AND freeParam2='".$param2."' ";
                    
					if ($email && $set->ShowEmailsOnTraderReportForAdmin ){
						$email = trim($email);
						$where .= " AND lower(email) like '%".strtolower($email)."%' ";
					}
                    
					output_memory('','A2','');
					
					if ( ($trader_alias || ($email && $set->ShowEmailsOnTraderReportForAdmin))) {
						if ($trader_alias){
							
							if(preg_match('/^[-a-zA-Z0-9 ._]+$/', $trader_alias)){
							// die ('2');
							$qry = "select trader_id from data_reg  where  merchant_id=".$arrMerchant['id']." and 
							
							(lower(trader_alias) like ('%". mysql_real_escape_string(strtolower($trader_alias))."%') )";
							
// echo ($qry).'<br>';
							}
							else
							$qry = "
						
							 select trader_id from (select  convert(group_concat(name separator '') using 'utf8') as alias,trader_id,rdate,merchant_id from(SELECT trader_id,rdate,merchant_id,ID,SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1) value, char(SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1) USING 'utf32') name
  FROM data_reg t CROSS JOIN 
  (
   SELECT a.N + b.N * 10 + 1 n
     FROM 
    (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
   ,(SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
    ORDER BY n 
   ) n
 WHERE  n.n <= 1 + (LENGTH(trim(leading ',' from replace(t.trader_alias,'&#',','))) - LENGTH(REPLACE(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', '')))
                                   ) as t1 group by id) final where  alias = '".strtolower(trim($trader_alias))."' " . $where . "  AND rdate between '" . $from . "' and '" . $to    . "' 
							
							";
							
							/* // shalini please fix this query to work properly instead of the query ^^^ */
						/* 	$qry = "select trader_id from data_reg  where  merchant_id=".$arrMerchant['id']." and 
							(
							(lower(trader_alias) like ('%". mysql_real_escape_string(strtolower($trader_alias))."%') )
							||
							(
							
							select trader_id from (select  convert(group_concat(name separator '') using 'utf8') as alias,trader_id,rdate,merchant_id from(SELECT trader_id,rdate,merchant_id,ID,SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1) value, char(SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1)) name
  FROM data_reg t CROSS JOIN 
  (
   SELECT a.N + b.N * 10 + 1 n
     FROM 
    (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
   ,(SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
    ORDER BY n 
   ) n
 WHERE  n.n <= 1 + (LENGTH(trim(leading ',' from replace(t.trader_alias,'&#',','))) - LENGTH(REPLACE(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', '')))
                                   ) as t1 group by id) final where  rdate between '" . $from . "' and '" . $to    . "' and alias = '".strtolower(trim($trader_alias))."' " . $where . " 
 

 

							
							)
							)
							";
							 */
							 
					
							 
							
						}
						else if ($email && $set->ShowEmailsOnTraderReportForAdmin)
							$qry = "select trader_id from data_reg  where  merchant_id=".$arrMerchant['id']." AND lower(email) like '%".mysql_real_escape_string(strtolower($email))."%' ";
					

                        $row = function_mysql_query($qry,__FILE__);
						
						while ($arrTraders = mysql_fetch_assoc($row)) {
								$arrAllTraders[] = $arrTraders['trader_id'];
                                unset($arrTraders);
						}
					
                    }
		    		
				/* 	if (empty($arrAllTraders) && !empty($trader_alias)){
						
						$arrAllTraders[]=0;	
						
					} */
				
				
                    if ($country_id) {
                        $where .= " AND country='".$country_id."' ";
                    }
                    
					$ftdAmount = 0;
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
			
			
			output_memory('','A3','');
			
			$traderIDForCheck = true;
			 $typeFilter = "";	
			 $typeDateFilter = "";	
			 $dateFilterFieldName = "rdate";
			 $addInitialFTDPart =  " and initialftddate>'". $from . "'  ";
			if (empty($type) || $type=='allaccounts'){
				$typeDateFilter  = " and rdate between '" . $from . "' and '" . $to . "' ";
			}
			else if ($type=='real'){
				$typeDateFilter  = " and rdate between '" . $from . "' and '" . $to . "' ";
				$typeFilter  = " and type='real' ";
			}
			else if ($type=='lead'){
				$typeDateFilter  = " and rdate between '" . $from . "' and '" . $to . "' ";
				$typeFilter  = " and type='lead' ";
			}
			else if ($type=='demo'){
				$typeDateFilter  = " and rdate between '" . $from . "' and '" . $to . "' ";
				$typeFilter  = " and type='demo' ";
			
			}else if ($type=='frozen'){
				$typeDateFilter  = " and rdate between '" . $from . "' and '" . $to . "' ";
				$typeFilter  = " and status='frozen' ";
			}
			
			else if ($type=='ftd' || $type=='totalftd'){
				// $typeDateFilter  = " and ((initialftddate between '" . $from . "' and '" . $to . "'  ) || (FTDqualificationDate between '" . $from . "' and '" . $to . "' ))";
				$typeDateFilter  = " and ((initialftddate between '" . $from . "' and '" . $to . "'  ) )";
				$typeFilter  .= " and status<>'frozen' and type<>'demo' ";
			}	
			else if ($type=='activeTrader'){
				$addInitialFTDPart="";
				$typeDateFilter  = " and ((FTDqualificationDate between '" . $from . "' and '" . $to . "'  ) )";
				$typeFilter  .= " and status<>'frozen' and type<>'demo' ";
			}	
				
			$typeFilter .= $typeDateFilter;
			
			
			$traders = implode(",",$arrAllTraders);
			
							                            							$data_reg_from_fields = "
							data_reg.merchant_id,
data_reg.trader_id,
data_reg.ftdamount,
data_reg.id,
data_reg.lastTimeActive,
data_reg.FTDqualificationDate,
data_reg.initialftddate,
data_reg.initialftdtranzid as tranz_id,
data_reg.group_id,
data_reg.affiliate_id,
data_reg.profile_id,
data_reg.freeParam,
data_reg.freeParam2,
data_reg.banner_id,
data_reg.status,
data_reg.type,
data_reg.trader_alias,
data_reg.email,
data_reg.country,
data_reg.saleStatus,
data_reg.rdate,
data_reg.lastSaleNote,
data_reg.lastSaleNoteDate
";
			$qry = "select ".$data_reg_from_fields.($displayForex==1? ", (select count(*) from data_reg dg where dg.merchant_id>0 and dg.trader_id=data_reg.trader_id group by dg.trader_id) as sub_trader_count":"") . " from data_reg where merchant_id = " . $loopedMerchant_ID 
																.(!empty($traders) ?  " and trader_id in (" . $traders . " ) " : "")
																. $typeFilter
																.$where
																. " GROUP BY merchant_id, trader_id ; " 
																;
			
			
			
				// die ($qry);
				
				output_memory('','C1','');


					$tradersProccessedForLots= array();
					$tradersProccessedForPNL= array();
					$PNLPerformanceAgregationArray= array();
               

					$netDepositTransactions = array();


					$acountries = getLongCountries('sales');

					output_memory('','C2','');

					$trader_report_resource = mysql_query($qry);
					while ($traderInfo = mysql_fetch_assoc($trader_report_resource)){
					


					
					//if ($type=='ftd' || $type=='activeTrader'){	
					if ($type=='ftd'){	

					
										$arrFtd = $traderInfo;
										$arrFtd['rdate'] = $traderInfo['initialftddate'];
										
										$arrFtd['FTDqualificationDate'] = $traderInfo['FTDqualificationDate'];
										
										if ($type=='activeTrader')
											$arrFtd['rdate'] = $traderInfo['FTDqualificationDate'];	
										
										
										$arrFtd['amount'] = $traderInfo['ftdamount'];
										$beforeNewFTD = $ftd;
										// var_dump($arrFtd);
										// die();
                                        getFtdByDealType($traderInfo['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $arrFtd['amount'], $ftd);
										
                                        if ($beforeNewFTD == $ftd ) {
										
											continue;
										}
										unset ($arrFtd);
											// commission part is calculated below that part is only to skip the calculation of RAW non qualified ftds when filter FTD.
					}
										
                                            
											
											
                    
						$totalCom=0;
						$arrRes = $traderInfo;
						$bannerInfo = getCreativeInfo($arrRes['banner_id'],1);
								
						//the problem						
						/* 	
								$sql = "SELECT ds.id,ds.affiliate_id,ds.tranz_id,ds.trader_id,ds.merchant_id,ds.amount,ds.rdate,ds.status,ds.type as data_sales_type FROM data_sales AS ds
                                WHERE 4=4 
								and ds.merchant_id = " . $loopedMerchant_ID . " 
								
                                    AND ds.affiliate_id =" .  $arrRes['affiliate_id'] . " and ds.type<>'PNL' 
								and ds.merchant_id>0 and 4=4 and " . $globalWhere . " ds.trader_id = " . $arrRes['trader_id'] 
                                        ;
						 */
			
						if (empty($allTranz))
						{
				
						$qry = "select * from ( select dr.FTDqualificationDate , dr.banner_id, dr.profile_id, dr.initialftddate , ds.id,dr.affiliate_id,ds.tranz_id,ds.trader_id,ds.merchant_id,ds.amount,ds.rdate,ds.status,ds.type as data_sales_type from 
									(select data_reg.initialftddate,data_reg.affiliate_id,data_reg.trader_id, data_reg.merchant_id,data_reg.banner_id, data_reg.profile_id,data_reg.FTDqualificationDate  from 
															data_reg where 1=1 " . $addInitialFTDPart 
																.(!empty($traders) ?  " and trader_id in (" . $traders . " ) " : "")
																 . $typeFilter
																.$where
																. " ) dr 
											inner join data_sales ds on dr.merchant_id = ds.merchant_id and ds.trader_id = dr.trader_id  
											
																where ds.type <>'PNL'
																order by ds.merchant_id, ds.trader_id
																)a
																group by merchant_id , tranz_id , data_sales_type
																"
																;
															// die ($qry);
                        $resource = function_mysql_query($qry,__FILE__);
                        while ($arrAmount = mysql_fetch_assoc($resource)) {
						$allTranz[$arrAmount['merchant_id']][$arrAmount['trader_id']][] = $arrAmount;
						
						}
					}
						
						
						
						$total_deposits = 0;



						
				$traderRows = $allTranz[$arrRes['merchant_id']][$arrRes['trader_id']];
                        $traderInfo['traderHasFTD'] = $traderInfo['initialftddate']=='0000-00-00 00:00:00' ? false : true;
						// var_dump($traderRows);
						// die ('gregerger');
				if (!empty($traderRows) && $traderInfo['traderHasFTD']) {
				
						
						
				foreach ($traderRows  as $arrAmount){

		
										
					
							if ($arrAmount['data_sales_type'] == "chargeback" ||
								$arrAmount['data_sales_type'] == "withdrawal" ||
								$arrAmount['data_sales_type'] == "deposit" ||
								$arrAmount['data_sales_type'] == "bonus" ) {
								
								$tranrow['id'] = $arrAmount['id'];
								$tranrow['affiliate_id'] = $arrAmount['affiliate_id'];
								$tranrow['tranz_id'] = $arrAmount['tranz_id'];
								$tranrow['trader_id'] = $arrAmount['trader_id'];
								$tranrow['merchant_id'] = $arrAmount['merchant_id'];
								$tranrow['amount'] = $arrAmount['amount'];
								$tranrow['rdate'] = $arrAmount['rdate'];
								$tranrow['type'] = $arrAmount['data_sales_type'];
								$tranrow['status'] = $arrAmount['status'];
								if(isset($arrRes['initialftddate'])) 
								$tranrow['initialftddate'] = $arrRes['initialftddate'];
							
								$tranrow['traderHasFTD'] = $traderInfo['traderHasFTD'];
								$tranrow['FTDqualificationDate'] = $traderInfo['FTDqualificationDate'];
								
							
								if (empty($netDepositTransactions[$tranrow['merchant_id'].'-'.$tranrow['tranz_id']]))
								$netDepositTransactions[$tranrow['merchant_id'].'-'.$tranrow['tranz_id']] = array($tranrow);
						
								}
							
							
/* 
								if (strtolower($arrAmount['data_sales_type'])=='deposit')	{
                            $arrRes['tranz_id'] = $arrAmount['tranz_id'];

							
								} */
                            
                            if ($arrAmount['data_sales_type'] == 'deposit') {
                                $depositAmount += $arrAmount['amount'];
                                $total_deposits++;
                            } elseif ($arrAmount['data_sales_type'] == 'bonus') {
                                $bonusAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['data_sales_type'] == 'withdrawal') {
                                $withdrawalAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['data_sales_type'] == 'chargeback') {
                                $chargebackAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['data_sales_type'] == 'volume') {
                                $volumeAmount += $arrAmount['amount'];
                                $totalTraders++;
								
											$arrTmp = [
                                                'merchant_id'  => $arrAmount['merchant_id'],
                                                'affiliate_id' => $arrAmount['affiliate_id'],
                                                'rdate'        => $arrAmount['rdate'],
                                                'banner_id'    => $arrAmount['banner_id'],
                                                'trader_id'    => $arrAmount['trader_id'],
                                                'FTDqualificationDate'    => $arrAmount['FTDqualificationDate'],
                                                'profile_id'   => $arrAmount['profile_id'],
                                                'type'       => 'volume',
                                                'amount'       => $arrAmount['amount'],
                                            ];
                                
								
								
								
								
											$volumeCom = getCommission(
                                                $arrAmount['rdate'], 
                                                $arrAmount['rdate'], 
                                                0, 
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
                                            $totalCom += $volumeCom;
											
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after volume " . $volumeCom . "<br/>";
									   } 
											
									
								
                            }
                            unset($arrAmount);
							
                        }
				}
			
            			
		


                    

					output_memory('','G1','');
					
					
						//lots 
					
					
						if (strtolower($arrMerchant['producttype']) == 'forex') {
							
					
						$totalLots  = 0;
						
						
						if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$tradersProccessedForLots)) {
							$tradersProccessedForLots[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
							
						$sql = 'SELECT  * FROM data_stats 
                                         WHERE merchant_id>0 and merchant_id = "' . $arrMerchant['id'] . '" and  trader_id = "' . $arrRes['trader_id'] . '" and turnover>0 ' ;
                           // die ($sql);
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d H:i:s');
										while($ts = mysql_fetch_assoc($traderStatsQ)){
													
													
													if ($earliestTimeForLot>$ts['rdate'])
															$earliestTimeForLot = $ts['rdate'];
                                            $this_running_lots_amount  = $ts['amount'];
											$totalLots  += $this_running_lots_amount;

							
							
							$row = [
                                            'merchant_id'  => $ts['merchant_id'],
                                            'affiliate_id' => $ts['affiliate_id'],
                                            'rdate'        => $ts['rdate'],
                                            'banner_id'    => $ts['banner_id'],
											'initialftddate'    => $traderInfo['initialftddate'],
											'FTDqualificationDate'    => $traderInfo['FTDqualificationDate'],
                                            'trader_id'    => $ts['trader_id'],
                                            'profile_id'   => $ts['profile_id'],
                                            'type'       => 'lots',
                                         'amount'       =>  $this_running_lots_amount,
										 ];
										 
						//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
						// die ('getcom: ' .$a );
						
						// die();
							$b = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
							
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after lots " . $b . "<br/>";
									   } 
									   
									   
							$totalCom +=$b;
										}
										
						
						}
						
						
						
						
						
						}
						
						output_memory('','G2','');
						
						
					   $totalPNL  = 0;
					   
							
							
						if ($set->deal_pnl == 1) {
						
						// echo $totalPNL.'<br>';
								$dealsForAffiliate = getExistingDealTypesAllMerchantsForAffiliateArray($arrRes['affiliate_id'],$arrDealTypeDefaults);								
								
								// {
								if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$tradersProccessedForPNL)) {
									$tradersProccessedForPNL[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
									
									
							
									// {	
									
									// die ($where);
								
						if (!empty($dealsForAffiliate['pnl'])){
								$pnlRes=array();	
								
								
				
									$pnlRecordArray=array();
									$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
									$pnlRecordArray['merchant_id']  = $arrMerchant['id'];
									$pnlRecordArray['group_id']  = $group_id;
									$pnlRecordArray['trader_id']  = $arrRes['trader_id'];
									//$pnlRecordArray['searchInSql']  = $searchInSql;
									//$pnlRecordArray['fromdate']  = $from;
									//$pnlRecordArray['todate']  = $to;
							$sql = generatePNLquery($pnlRecordArray,false);
							
									   
									   		// echo ($sql).'<Br>';
								 $traderStatsQ = function_mysql_query($sql,__FILE__);
								 $pnlCom = 0;
								 while($ts = mysql_fetch_assoc($traderStatsQ)){
									 $pnlRes[] = $ts;
									 
								 }
									
									
								}
							else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
									
									
									
										if (empty($PNLPerformanceAgregationArray[$arrMerchant['id']])){
									
		
															 $sql = 'SELECT  sum(pnltable.amount) as amount,max(pnltable.rdate) as rdate ,max(pnltable.trader_id) as trader_id ,max(pnltable.merchant_id) as merchant_id,max(pnltable.banner_id) as banner_id,max(pnltable.profile_id) as profile_id,max(pnltable.affiliate_id) as affiliate_id FROM '.$set->pnlTable.' pnltable
													 inner join data_reg on pnltable.merchant_id = data_reg.merchant_id and pnltable.trader_id  = data_reg.trader_id and data_reg.type<>"demo"
													 WHERE pnltable.type="PNL" and pnltable.merchant_id>0 and pnltable.amount<>0   '
													 .(!empty($affiliate_id) ?  '  and  pnltable.affiliate_id = ' . $affiliate_id : "")
													 .(!empty($group_id) ?  '  and  pnltable.group_id = ' . $group_id : "")
													 .(!empty($trader_id) ?  '  and  pnltable.trader_id = ' . $trader_id : "")
													 .(!empty($country_id) ?  '  and  pnltable.country_id = ' . $country_id : "")
													 .(!empty($banner_id) ?  '  and  pnltable.banner_id = ' . $banner_id : "")
													 .(!empty($profile_id) ?  '  and  pnltable.profile_id = ' . $profile_id : "")
													 .(!empty($arrMerchant['id']) ?  '  and  pnltable.merchant_id = ' . $arrMerchant['id'] : "")
													 // . ' AND pnltable.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql)
													 .	 " group by pnltable.trader_id;";
												
												// die ($sql);
												$traderStatsQ = function_mysql_query($sql,__FILE__);
												while($ts = mysql_fetch_assoc($traderStatsQ)){
												
												$PNLPerformanceAgregationArray[$ts['merchant_id']][$ts['trader_id']]=$ts;
													
													
												}
										}
										$pnlRes[] = $PNLPerformanceAgregationArray[$arrMerchant['id']][$arrRes['trader_id']];
										
								}
									
									
									foreach ($pnlRes as $ts)
									{
										
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
												
															 
										
											if (!empty($dealsForAffiliate['pnl'])){
												$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
												
												
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after pnl " . $b . "<br/>";
									   } 
												
													$totalCom += $tmpCom;
											}
									}
									unset ($pnlRes);
								
									}
								}
								
						output_memory('','G3','');
						

					
						

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
                                '" AND affiliate_id = "' . $arrRes['affiliate_id'] . '" AND trader_id = ' . $arrRes['trader_id'] . ' ',
                                
								$arrMerchant['producttype'],0,0,0,0,0,0,$arrMerchant['rev_formula']
                            );
                            
                            $intTotalRevenue    += $intCurrentRevenue;
                            $row                 = [];
                            $row['merchant_id']  = $arrRes['merchant_id'];
                            $row['affiliate_id'] = $arrRes['affiliate_id'];
                            $row['banner_id']    = $arrRes['banner_id'];
                            $row['rdate']        = $arrRange['from'];
                            $row['initialftddate']  = $arrRes['initialftddate'];
                            $row['amount']       = $intCurrentRevenue;
                            $row['trader_id']    = $arrRes['trader_id'];
                            $row['isFTD']        = false;
							$row['traderHasFTD'] = $row['initialftddate']=='0000-00-00 00:00:00' ? false : true;
							
							
                            $netCommission           = getCommission($arrRange['from'], $arrRange['to'], 1, (isset($group_id) && $group_id != '' ? $group_id : -1),  $arrDealTypeDefaults, $row);
							
							if($_GET['com'] && $traderIDForCheck)
								 {
								  echo " Commission after netcommission " . $netCommission . "<br/>";
								  } 
							
                            $totalCom           += $netCommission;
							
							
                            unset($arrRange);
                        }
                        
                        $netRevenue = $intTotalRevenue;
                        
                    } else */ {
						
						
						
					if (!empty($netDepositTransactions)){
						
						foreach($netDepositTransactions as $trans){

							
							$a=0;
							
							$revDepAmount = 0;
							$revBonAmount = 0;
							$revWithAmount = 0;
							$revChBAmount = 0;
							
							$amount = $trans[0]['amount'];
							$recordDate = $trans[0]['rdate'];
						
							// echo $recordDate.'<br>';
							
						if (!empty($amount)  && !empty($recordDate)) {


							
								if ($trans[0]['type']=='deposit')
									$revDepAmount = $amount;
								if ($trans[0]['type']=='bonus')
									$revBonAmount = $amount;
								if ($trans[0]['type']=='withdrawal')
									$revWithAmount = $amount;
								if ($trans[0]['type']=='chargeback')
									$revChBAmount = $amount;
								
							$ThisTransactionRecordNetRevenue =  round(getRevenue("data_sales.rdate  BETWEEN . '".$recordDate."' AND '".$recordDate." 23:59:59' ",$arrMerchant['producttype'],$revDepAmount,$revBonAmount,$revWithAmount,0,0,0,$arrMerchant['rev_formula'],null,$revChBAmount),2);
							
							
							
/* 
						 	echo 'transamount: ' . $trans[0]['amount']. '<br>';
							echo 'netRevenue: ' . $netRevenue. '<br>';
							
							echo '$recordDate: ' . $recordDate. '<br>'. '<br>';
							// die ($netRevenue); 
 */							 
							
							// $regDateRow = mysql_fetch_assoc(mysql_query("select rdate from data_reg where merchant_id = " . $trans[0]['merchant_id'] . " and trader_id = " . $trans[0]['trader_id']));
							// $regDate = $regDateRow['rdate'];
							// var_dump($regDate);
							// die();
								$comrow                 = array();
				           $comrow['merchant_id']  = $trans[0]['merchant_id'];
                           $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
                            $comrow['banner_id']    = 0;
                            $comrow['initialftddate']        = $trans[0]['initialftddate'];
                            $comrow['rdate']        = $trans[0]['rdate'];
                            $comrow['amount']       = $ThisTransactionRecordNetRevenue;
                             $comrow['trader_id']  =  $trans[0]['trader_id'];
							$comrow['trades'] = $totalTraders;
                            $comrow['isFTD']        = false;
							$comrow['traderHasFTD'] = $comrow['initialftddate']=='0000-00-00 00:00:00' ? false : true;
							
							if(isset($trans[0]['initialftddate']))
                            $comrow['initialftddate']        = $trans[0]['initialftddate'];
							  
							  
						$com = getCommission($recordDate,$recordDate, 1, (isset($group_id) && $group_id != '' ? $group_id : -1),  $arrDealTypeDefaults, $comrow,true);
							  // var_dump($comrow);
							  // die();
						
								if($_GET['com'] && $traderIDForCheck)
								 {
								  echo " Commission after net com inside tranz loop " . $com . "<br/>";
								  } 
								  
								  
						
										$totalCom           += $com;
										
								 if ($_GET['ggg']>0) {
									 echo '6: ' . $a . '<br>';
														 var_dump($com);
														 echo '<Br>';
														 var_dump($comrow);
														 echo '<Br>';
														 echo '$totalCom: ' . $totalCom. '<br>';
														 echo '<Br>';
														 echo '<Br>';
														 echo '<Br>';
								 }
								 
										// $totalCom        +=0;//   += $com;
										
						$netRevenue +=$ThisTransactionRecordNetRevenue;
						}
								

								
				}
				unset ($netDepositTransactions);
				
				
		}
		else{
					$netRevenue =  round(getRevenue(" data_sales.rdate BETWEEN '".$from."' AND '".$to."' ",$arrMerchant['producttype'],$depositAmount,$bonusAmount,$withdrawalAmount,0,0,0,$arrMerchant['rev_formula'],null,$chargebackAmount),2);								
					
					
/*		
 $netRevenue =  round(getRevenue($where,$arrMerchant['producttype'],$depositAmount,$bonusAmount,$withdrawalAmount,0,0,0,$arrMerchant['rev_formula'],null,$chargebackAmount),2);

 0,0,0,0,0,0,$arrMerchant['rev_formula']
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
                            $row['initialftddate']        = $arrRes['initialftddate'];
                            $row['amount']       = $netRevenue;
                            $row['trader_id']    = $arrRes['trader_id'];
                            $row['isFTD']        = false;
							$row['traderHasFTD'] = $row['initialftddate']=='0000-00-00 00:00:00' ? false : true;
							   	
								
								
							    $externalCom           = getCommission($earliestTimeForNetRev, $to, 1, (isset($group_id) && $group_id != '' ? $group_id : -1),  $arrDealTypeDefaults, $row);
								if($_GET['com'] && $traderIDForCheck)
								 {
								  echo " Commission after net externalCom " . $externalCom . "<br/>";
								  } 
								  
								  
							    $totalCom           += $externalCom;

                    }
					}
					output_memory('','G4','');
           
					
                    // AFFILIATE info retrieval.
           
		   
					$affInfo = getAffiliateRow($arrRes['affiliate_id'],1);
					
							
						
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


					
					
					
					

/* 
tb1.merchant_id,tb1.banner_id, tb1.amount, tb1.rdate, tb1.affiliate_id, tb1.trader_id, tb1.id as id ,reg.country AS country 
									".(strtolower($mer['producttype'])=='forex' ? " , (select count(*) from data_reg dg1 where dg1.trader_id=data_reg.trader_id group by dg1.trader_id) as sub_trader_count " : ""). " 
									


							 */
							 
							 
							
							
							if (hasValidDate($arrRes['initialftddate'])) {
								
								$arrFtd = $traderInfo;
								$arrFtd['rdate'] = $traderInfo['initialftddate'];
								$arrFtd['FTDqualificationDate'] = $traderInfo['FTDqualificationDate'];
								$arrFtd['amount'] = $traderInfo['ftdamount'];
										
                            $beforeNewFTD = $ftd;
							
							
							
                            getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $FtdCount, $ftd);

								
							
                            
							// echo 'before ' . $beforeNewFTD . '<Br>';
							// echo 'ftd ' . $ftd . '<Br>';
							if ($beforeNewFTD != $ftd ) {
								
                                $FtdCount = $arrFtd['amount'];

                                $arrFtd['isFTD'] = true;
								$arrFtd['trades'] = $totalTraders;
								$arrFtd['traderHasFTD'] = $arrFtd['initialftddate']=='0000-00-00 00:00:00' ? false : true;
								
								// echo $arrFtd['initialftddate'] . '<br>';
								///		if (!in_array($arrMerchant['id'] . '-' .  $arrFtd['trader_id'],$arrTradersPerMerchants)) 
							if (hasValidDate($arrFtd['FTDqualificationDate']))
							{
								//	// $arrTradersPerMerchants[$arrMerchant['id'] . '-' . $arrFtd['trader_id']] = $arrFtd['trader_id'];
								//   // if (!in_array($arrFtd['trader_id'], $arrTradersPerMerchants)) {

                                    $ftdComFromLoop = getCommission($arrFtd['rdate'], $arrFtd['rdate'], 0, $arrFtd['group_id'], $arrDealTypeDefaults, $arrFtd);
                                    $totalCom += $ftdComFromLoop;
									
									if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission ftd loop " . $ftdComFromLoop . "<br/>";
									   } 
                                }
                            }
                            
							}
                        
						
						
						
					
					$reason="";
                   
				   
					$notesArray = array();
					
					if (empty($notesArray)){
				   /* $sql = "select pd.trader_id, tt.notes,pd.status,pd.reason from payments_details pd 
								left join traders_tag tt on tt.merchant_id = pd.merchant_id and tt.trader_id = pd.trader_id where pd.merchant_id = " . $arrRes['merchant_id'] . " and pd.trader_id in (" . implode(',',$allTradersIDs) .") ; " ;
								 */
								$sql = "select tt.trader_id, tt.notes,pd.status as pd_status,tt.status,pd.reason,tt.merchant_id from traders_tag tt left join  payments_details pd on tt.merchant_id = pd.merchant_id and tt.trader_id = pd.trader_id where tt.merchant_id = " . $arrRes['merchant_id'] . " ; " ;
								
								
						// die ($sql);
								// left join traders_tag tt on tt.merchant_id = pd.merchant_id and tt.trader_id = pd.trader_id where pd.merchant_id = " . $arrRes['merchant_id'] . " and pd.trader_id = '" . $arrRes['trader_id'] ."' limit 1; " ;
						$results=mysql_query($sql);
						
						while(($row =  mysql_fetch_assoc($results))) {
							$notesArray[$row['merchant_id']][$row['trader_id']] = $row;
						}
					}
                    $chkTrader = $notesArray[$arrRes['merchant_id']][$arrRes['trader_id']];
								
			
					if (!empty($chkTrader)) {
						
						$reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
					}
					
					// }else 
						
					if (empty($chkTrader)) {
					
					$reason = $chkTrader['reason'];
						
					}
					
				
					output_memory('','G5','');

					// var_dump($arrRes);
					// die();
					
								
					 $foundcountry = $acountries[$arrRes['country']];
					
					$tranzactionId  = $arrRes['tranz_id']; // $tranrow['tranz_id']
					$hidePendingReason = hasValidDate($arrRes['FTDqualificationDate']) || ($affInfo['qualify_type']=='' || ($affInfo['qualify_type']=='default' && $merchantsAr['qualify_type']==''  ));					
					$cnt=1;
                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].''.($debugshowhide ? "1":'').'</td>';
								if($displayForex==1)
								$listReport .='<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=subtraders&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'">'.$arrRes['sub_trader_count'].'</a></td>';
							$listReport .= '
                            <td>'.$arrRes['trader_alias'].''.($debugshowhide ? "2":'').'</td>
                            ' . ( allowView('af-mail',$deal,'fields') ? '<td>'.$arrRes['email'].''.($debugshowhide ? "3":'').'</td>' : '' ) . '
							<td>'.($type == "deposit" ? date("d/m/Y", strtotime($traderInfo['rdate'])) : date("d/m/Y", strtotime($arrRes['rdate']))).''.($debugshowhide ? "4":'').'</td>
                            <td><span style="color: '.$color.';">'.$arrRes['type'].'</span>'.($debugshowhide ? "5":'').'</td>
                            <td>'.$foundcountry.''.($debugshowhide ? "6":'').'</td>
                            <td>'.$arrRes['affiliate_id'].''.($debugshowhide ? "7":'').'</td>
                            <td><a href="affiliate/account.php" target="_blank">'.$affInfo['username'].'</a>'.($debugshowhide ? "8":'').'</td>
                            <td>'.$arrMerchant['id'].''.($debugshowhide ? "9":'').'</td>
                            <td>'.strtoupper($arrMerchant['name']).''.($debugshowhide ? "10":'').'</td>
                            <td style="text-align: left;">'.$bannerInfo['id'].''.($debugshowhide ? "11":'').'</td>
                            <td style="text-align: left;">'.$bannerInfo['title'].''.($debugshowhide ? "12":'').'</td>
                            <td>'.$bannerInfo['type'].''.($debugshowhide ? "13":'').'</td>
							<td>'.$bannerInfo['language_name'].''.($debugshowhide ? "14":'').'</td>
                            <td>'.$arrRes['profile_id'].''.($debugshowhide ? "15":'').'</td>
                            <td>'.$listProfiles[$arrRes['profile_id']].''.($debugshowhide ? "15":'').'</td>
							<td>'.(empty($arrRes['status']) ? lang('real'): $arrRes['status']) .''.($debugshowhide ? "16":'').'</td>
                            <td>'.$arrRes['freeParam'].''.($debugshowhide ? "17":'').'</td>
                            <td>'.$arrRes['freeParam2'].''.($debugshowhide ? "18":'').'</td>
                             '. (allowView('af-trnz',$deal,'fields') ?
							'<td>' . ($tranzactionId) . ''.($debugshowhide ? "19":'').'</td>' : '').'
							'. (allowView('af-ftd',$deal,'fields')  ?
							'<td>' .
							(hasValidDate($arrRes['initialftddate']) ?   date("d/m/Y H:i:s", strtotime($arrRes['initialftddate'])) : '' )
							.($debugshowhide ? "20":'').'</td>'
							: '').'
							'. (allowView('af-ftda',$deal,'fields') ? '
                            <td>'.price($arrRes['ftdamount']).''.($debugshowhide ? "21":'').'</td>
							': '' ).'
							'. (allowView('af-depo',$deal,'fields') ? '
                            <td><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'&type=deposit">'.$total_deposits.'</a>'.($debugshowhide ? "22":'').'</td>
							': '' ).'
							'. (allowView('af-depoam',$deal,'fields') ? '
                            <td>'.price($depositAmount).''.($debugshowhide ? "23":'').'</td>
							': '' ).'
							'. (allowView('af-vlm',$deal,'fields') ? '
                            <td>'.price($volumeAmount).''.($debugshowhide ? "24":'').'</td>
							': '' ).'
							'. (allowView('af-bns',$deal,'fields') ? '
                            <td>'.price($bonusAmount).''.($debugshowhide ? "25":'').'</td>
							': '' ).'
							'. (allowView('af-withd',$deal,'fields') ? '
                            <td>'.price($withdrawalAmount).''.($debugshowhide ? "26":'').'</td>
							': '' ).'
							'. (allowView('af-chrgb',$deal,'fields') ? '
                            <td>'.price($chargebackAmount).''.($debugshowhide ? "27":'').'</td>
							': '' ).'
							'. (allowView('af-ntrv',$deal,'fields') ? '
                            <td>'.price($netRevenue).''.($debugshowhide ? "28":'').'</td>
							': '' ).'
							'. (allowView('af-trades',$deal,'fields') ? '
                            <td>'.$totalTraders.''.($debugshowhide ? "29":'').'</td>
							': '' ).'
		'. ( allowView('af-vlm',$deal,'fields')  && $displayForex==1 ? 
                            '<td>'.$totalLots.''.($debugshowhide ? "30":'').'</td>' : '' ).'
							
							'. ($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields') ? 
							'<td>'.price($totalPNL).'</td>' : '').'
							
							'.(allowView('af-salests',$deal,'fields') ? '
                            <td>'.$arrRes['saleStatus'].''.($debugshowhide ? "31":'').'</td>
							':'').'
							
							'. ( allowView('af-slsnt',$deal,'fields')   ? 
							
							
                            '<td>'.$arrRes['lastSaleNote'].''.($debugshowhide ? $cnt++:'').'</td>
                            <td>'.($arrRes['lastSaleNoteDate']=='0000-00-00 00:00:00' ? '' : $arrRes['lastSaleNoteDate']).''.($debugshowhide ? $cnt++:'').'</td>' : '' ).
													
							 ( allowView('af-qftd',$deal,'fields')   ? 
							'<td>'.(hasValidDate($arrRes['FTDqualificationDate']) ? $arrRes['FTDqualificationDate'] : "" ).'</td>' : '' ).'
							
							'. ($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 ? '' : 
                            '<td>'.price($totalCom).''.($debugshowhide ? $cnt++:'').'</td>').'
							<td>'.(!empty($reason) ? $reason : ($hidePendingReason ? '' : lang('Pending'))).($debugshowhide ? $cnt++:'').'</td>
							'.( allowView('af-clckdtls',$deal,'fields')   ? 
							'<td>'. (($arrRes['uid']!=0 || $arrRes['uid']!="")?'<a href="'.$set->SSLprefix.'affiliate/reports.php?act=clicks&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'">'. lang('View') .'</a>':'') .'</td>':'').'
                        </tr>';
						               
					
					
                    if (!in_array($arrRes['merchant_id'] . '-' . $arrRes['trader_id'],   $arrTradersPerMerchants)) {	
                        $arrTradersPerMerchants[] = $arrRes['merchant_id'] . '-' . $arrRes['trader_id']; //$arrRes['trader_id'];
                        $totalFTD += $arrRes['ftdamount'];
                        $totalNetRevenue += $netRevenue;
                        $totalTotalCom += $totalCom;
                    }
                    
                    $totalDepositAmount += $depositAmount;
                    $totalVolumeAmount += $volumeAmount;
                    $totalBonusAmount += $bonusAmount;
                    $totalTotalDeposit += $total_deposits;
                    $total_Traders +=           $totalTraders  ;
                    
					$totalLotsamount += $totalLots;
					 $totalPNLamount += $totalPNL;
					 
                    $totalWithdrawalAmount += $withdrawalAmount;
                    $totalChargeBackAmount += $chargebackAmount;
                    $ftdExist[] = $firstDeposit['trader_id'];
                    $l++;
					
					$volumeAmount =$totalLots = $totalTraders= $bonusAmount=$withdrawalAmount=$netRevenue=$chargebackAmount= $total_deposits=0;
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=0;					
					
                }
                        
						}
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		
		
		
		
		
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form id="frmRepo" action="'.$set->SSLprefix.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="trader" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						
						<td>'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						<td>'.lang(ptitle('Trader Alias')).'</td>
						<td>'.lang(ptitle('Parameter')).'</td>
						<td>'.lang(ptitle('Parameter2')).'</td>
						
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" onblur="validateMerchant(this)"/></td-->
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_alias" value="'.$trader_alias.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param" value="'.$param.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="'.$param2.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
                                               
						<td>
							<select name="type" style="width: 110px;">
								<option value="real" '.($type == "real" ? 'selected' : '').'>'.lang(ptitle('Accounts')).'</option>
								'.($hideDemoAndLeads? "": '<option value="lead" '.($type == "lead" ? 'selected' : '').'>'.lang(ptitle('Lead')).'</option>
								<option value="demo" '.($type == "demo" ? 'selected' : '').'>'.lang(ptitle('Demo')).'</option>').'
								 '. (allowView('af-ftd',$deal,'fields') ? '<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>' : '').'
								 '. (allowView('af-tftd',$deal,'fields') ? '<option value="totalftd" '.($type == "totalftd" ? 'selected' : '').'>'.lang('RAW FTD').'</option>' : '').'
								 '. (allowView('af-qftd',$deal,'fields') ? '<option value="activeTrader" '.($type == "activeTrader" ? 'selected' : '').'>'.lang('Active Trader').'</option>' : '').'
								 '.(allowView('af-frzn',$deal,'fields') ? '<option value="frozen" '.($type == "frozen" ? 'selected' : '').'>'.lang('Frozen').'</option>' : '').'
							</select>
						</td>
						
						<!--td><input type="button" value="'.lang('View').'" onClick="validateForm()"/></td-->
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
				'.(
                                    $set->export 
                                    ? //'<div class="exportCSV"><a href="'.$set->basepage.'?act=trader_xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'">
                                       //     <img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'
                                        //        .lang('Export to CSV').'</b></a></div>'
                                       '<div class="exportCSV" style="float:left">
                                            <a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});">
                                                <img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'
                                                    .lang('Export to CSV').'</b>
                                            </a>
                                        </div>':'').'
                                        <div class="exportCSV" style="float:left">
                                            <a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});">
                                                <img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'
                                                    .lang('Export to XLS').'</b>
                                            </a>
                                        </div>
										'. getFavoritesHTML() .'
                                        <div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2600px;">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">
				<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="traderTbl">
					<thead><tr  class="table-row">
						<th  class="table-cell">'.lang(ptitle('Trader ID')).''.($debugshowhide ? "a0":'').'</th>
						'. ($displayForex==1  ? '
						<th class="table-cell">'.lang(ptitle('Trader Sub Accounts')).''.($debugshowhide ? "a2":'').'</th>':'').'
						<th class="table-cell">'.lang(ptitle('Trader Alias')).''.($debugshowhide ? "a1":'').'</th>
						' . (allowView('af-mail',$deal,'fields')  ? '<th>'.lang(ptitle('Email')).''.($debugshowhide ? "a3":'').'</th>' : '' ) . '
						<th class="table-cell">'.lang('Registration Date').''.($debugshowhide ? "a4":'').'</th>
						<th class="table-cell">'.lang(ptitle('Trader Status')).''.($debugshowhide ? "a5":'').'</th>
						<th class="table-cell">'.lang('Country').''.($debugshowhide ? "a6":'').'</th>
						<th class="table-cell">'.lang('Affiliate ID').''.($debugshowhide ? "a7":'').'</th>
						<th class="table-cell">'.lang('Affiliate Username').''.($debugshowhide ? "a8":'').'</th>
						<th class="table-cell">'.lang('Merchant ID').''.($debugshowhide ? "a9":'').'</th>
						<th class="table-cell">'.lang('Merchant Name').''.($debugshowhide ? "a10":'').'</th>
						<th  class="table-cell"style="text-align: left;">'.lang('Creative ID').''.($debugshowhide ? "a11":'').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Creative Name').''.($debugshowhide ? "a12":'').'</th>
						<th class="table-cell">'.lang('Type').''.($debugshowhide ? "a13":'').'</th>
						<th class="table-cell">'.lang('Creative Language').''.($debugshowhide ? "a14":'').'</th>
						<th class="table-cell">'.lang('Profile ID').''.($debugshowhide ? "a15":'').'</th>
						<th class="table-cell">'.lang('Profile Name').''.($debugshowhide ? "a15":'').'</th>
						<th class="table-cell">'.lang('Status').''.($debugshowhide ? "a16":'').'</th>
						<th class="table-cell">'.lang('Param').''.($debugshowhide ? "a17":'').'</th>
						<th class="table-cell">'.lang('Param2').''.($debugshowhide ? "a18":'').'</th>'
                        
						. (allowView('af-trnz',$deal,'fields') ?
						'<th class="table-cell">' . lang('Transaction ID') . ''.($debugshowhide ? "a19":'').'</th>' : '').'
						'. (allowView('af-ftd',$deal,'fields') ?
						'<th class="table-cell">'.lang(($type == "deposit" ? 'Deposit Date' : 'First Deposit')).''.($debugshowhide ? "a20":'').'</th>' :'').
						(allowView('af-ftda',$deal,'fields') ? 
						'<th class="table-cell">'.lang('FTD Amount').''.($debugshowhide ? "a21":'').'</th>' : '').
						(allowView('af-depo',$deal,'fields')  ? '<th class="table-cell">'.lang('Total Deposits').''.($debugshowhide ? "a22":'').'</th>' : '' ) .
						(allowView('af-depoam',$deal,'fields') ? '<th class="table-cell">'.lang(($type == "deposit" ? 'Deposit Amount' : 'Deposit Amount')).''.($debugshowhide ? "a23":'').'</th>' : '' ).
						(allowView('af-vlm',$deal,'fields') ? '<th class="table-cell">'.lang('Volume').''.($debugshowhide ? "a24":'').'</th>' : '').
						(allowView('af-bns',$deal,'fields') ? '<th class="table-cell">'.lang('Bonus  Amount').''.($debugshowhide ? "1":'').'</th>' : '').
						(allowView('af-withd',$deal,'fields') ? '<th class="table-cell">'.lang('Withdrawal Amount').''.($debugshowhide ? "1":'').'</th>' : '').
						(allowView('af-chrgb',$deal,'fields') ? '<th class="table-cell">'.lang('ChargeBack Amount').''.($debugshowhide ? "1":'').'</th>' : '').
						(allowView('af-ntrv',$deal,'fields') ? '<th class="table-cell">'.lang(ptitle('Net Deposit')).''.($debugshowhide ? "1":'').'</th>':'').
						(allowView('af-trades',$deal,'fields') ? '
						<th class="table-cell">'.lang(ptitle('Trades')).''.($debugshowhide ? "1":'').'</th>' : '' ). '
	'. (allowView('af-vlm',$deal,'fields') && $displayForex==1  ? 
						'<th class="table-cell">'.lang(ptitle('Lots')).''.($debugshowhide ? "1":'').'</th>' : '' ) . '
						'. ($set->deal_pnl==1  && allowView('af-pnl',$deal,'fields')? 
						'<th class="table-cell">'.lang(ptitle('PNL')).'</th>' : '' ) . '
						'.(allowView('af-salests',$deal,'fields') ? '
						<th class="table-cell">'.lang('Sale Status').''.($debugshowhide ? "1":'').'</th>
						' :'').'
						'. ( allowView('af-slsnt',$deal,'fields')   ? 
							
							
                            '<th class="table-cell">'.lang('Last Sale Note').''.($debugshowhide ? "1":'').'</th>
                            <th class="table-cell">'.lang('Last Sale Note Date').''.($debugshowhide ? "1":'').'</th>
							' : '' ).'
							
							'. ( allowView('af-qftd',$deal,'fields')   ? 
                            '<th class="table-cell">'.lang('Qualification Date').''.($debugshowhide ? "1":'').'</th>
							' : '' ).'
                            
						'. ($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 ? '' : 
						'<th>'.lang('Commission').''.($debugshowhide ? "1":'').'</th>' ).'
						
						<th class="table-cell">'.lang('Admin Notes').''.($debugshowhide ? "1":'').'</th>'
						.( allowView('af-clckdtls',$deal,'fields')   ? '<th class="table-cell">' . lang('Click Details') . '</th>':'').'
					</tr></thead>
					<tfoot>
						<th>'.($debugshowhide ? "b-1":'').'</th>
						'. ($displayForex==1  ? 
						'<th>'.($debugshowhide ? "b1":'').'</th>':'').'
						<th>'.($debugshowhide ? "b2":'').'</th>
						' . (allowView('af-mail',$deal,'fields') ?  '<th id="3">'.($debugshowhide ? "b3":'').'</th>' : '' ) . '
						<th>'.($debugshowhide ? "b4":'').'</th>
						<th>'.($debugshowhide ? "b5":'').'</th>
						<th>'.($debugshowhide ? "b6":'').'</th>
						<th>'.($debugshowhide ? "b7":'').'</th>
						<th>'.($debugshowhide ? "b8":'').'</th>
						<th>'.($debugshowhide ? "b9":'').'</th>
						<th>'.($debugshowhide ? "b10":'').'</th>
						<th>'.($debugshowhide ? "b11":'').'</th>
						<th>'.($debugshowhide ? "b12":'').'</th>
						<th>'.($debugshowhide ? "b13":'').'</th>
						<th>'.($debugshowhide ? "b14":'').'</th>
						<th>'.($debugshowhide ? "b15":'').'</th>
						<th>'.($debugshowhide ? "b15":'').'</th>
						<th>'.($debugshowhide ? "b16":'').'</th>
						<th>'.($debugshowhide ? "b17":'').'</th>
						<th>'.($debugshowhide ? "b18":'').'</th>'
						
						. (allowView('af-trnz',$deal,'fields') ?
						'<th>'.($debugshowhide ? "b19":'').'</th>
						' : '').'
						'. (allowView('af-ftd',$deal,'fields') ?
						
						'<th>'.($debugshowhide ? "b20":'').'</th>':'').'
                              
						' . (allowView('af-ftda',$deal,'fields') ? '
						<th  style="text-align: left;">'.price($totalFTD).''.($debugshowhide ? "b21":'').'</th>
						' : '' ).'
						' . (allowView('af-depo',$deal,'fields') ? '
						<th style="text-align: left;">'.$totalTotalDeposit.''.($debugshowhide ? "b22":'').'</th>
						' : '' ).'
						' . (allowView('af-depoam',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalDepositAmount).''.($debugshowhide ? "b23":'').'</th>
						' : '' ).'
						' . (allowView('af-vlm',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalVolumeAmount).''.($debugshowhide ? "b24":'').'</th>
						' : '' ).'
						' . (allowView('af-bns',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalBonusAmount).''.($debugshowhide ? "b25":'').'</th>
						' : '' ).'
						' . (allowView('af-withd',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalWithdrawalAmount).''.($debugshowhide ? "b26":'').'</th>
						' : '' ).'
						' . (allowView('af-chrgb',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalChargeBackAmount).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-ntrv',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalNetRevenue).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-trades',$deal,'fields') ? '
						<th style="text-align: left;">'.$total_Traders.''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
	'. (allowView('af-vlm',$deal,'fields') && $displayForex==1 ? 
						'<th style="text-align: left;">'.$totalLotsamount.''.($debugshowhide ? "1":'').'</th>' : '' ).'
						'. ($set->deal_pnl==1  && allowView('af-pnl',$deal,'fields')? 
						'<th>'.$totalPNLamount.'</th>' : '' ).'
						'.(allowView('af-salests',$deal,'fields') ? '
						<th>'.($debugshowhide ? "1":'').'</th>
						':'').'
						'. ( allowView('af-slsnt',$deal,'fields')   ? 
							
							
                            '
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
                            
							' : '' ).'
							
							'. ( allowView('af-qftd',$deal,'fields')   ? 
						'<th>'.($debugshowhide ? "1":'').'</th>
							' : '' ).'
							
						'. ($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 ? '' : 
						'<th style="text-align: left;">'.price($totalTotalCom).''.($debugshowhide ? "1":'').'</th>').'
						<th>'.($debugshowhide ? "1":'').'</th>'
						.( allowView('af-clckdtls',$deal,'fields')   ? '<th></th>':'').'
						
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>
				<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
				<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>   
				<script>
				$(document).ready(function(){
					/* thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'traderData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					i = 0;
					$($("#traderTbl")[0].config.rowsCopy).each(function() {
						console.log(i);
						i++;
						txt += "<tr>" + $(this).html()+"</tr>";
					});
					txt += "</tbody>";
					txt += "<tfoot>" + tfoot + "</tfoot>";
					txt += "</table>";
					$("body").append("<div style=\'display:none\'>"+ txt +"</div>"); */
					
					$("input[name=trader_id]").on("keyup",function(){
						if($(this).val()!=""){
							$("#date_from").val("'. date("Y/m/d",strtotime('-100 year')) .'");
							$("#date_to").val("'. date("Y/m/d",strtotime('+100 year')) .'");
						}
						else{
							$("#date_from").val("'. date("Y/m/d") .'");
							$("#date_to").val("'. date("Y/m/d") .'");
						}
					});
				});
				</script>
			</div>'.getPager();
				$tableStr .= getSingleSelectedMerchant();
				// $tableStr .= getValidateTraderMerchantScript();
                $tableStr = '
                    <table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).''.($debugshowhide ? "1":'').'</th>
						'.($displayForex==1 ? '
						<th>'.lang(ptitle('Sub Traders')).''.($debugshowhide ? "1":'').'</th>':'').'
						<th>'.lang(ptitle('Trader Alias')).''.($debugshowhide ? "1":'').'</th>
						
						' . (allowView('af-mail',$deal,'fields') ? '
					<th>'.lang(ptitle('Email')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						
						<th>'.lang('Registration Date').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang(ptitle('Trader Status')).''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Country').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Affiliate ID').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Affiliate Username').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Merchant ID').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Merchant Name').''.($debugshowhide ? "1":'').'</th>
						<th style="text-align: left;">'.lang('Creative ID').''.($debugshowhide ? "1":'').'</th>
						<th style="text-align: left;">'.lang('Creative Name').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Type').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Language').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Profile ID').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Profile Name').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Status').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Param').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Param2').''.($debugshowhide ? "1":'').'</th>
						' . (allowView('af-trnz',$deal,'fields') ? '
						<th>' . lang('Transaction ID') . ''.($debugshowhide ? "1":'').'</th>
                        ' : '' ).'
						' . (allowView('af-ftd',$deal,'fields') ? '
						<th>'.lang(($type == "deposit" ? 'Deposit Date' : 'First Deposit')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-ftda',$deal,'fields') ? '
						<th>'.lang('FTD Amount').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-depo',$deal,'fields') ? '
						<th>'.lang('Total Deposits').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-depoam',$deal,'fields') ? '
						<th>'.lang(($type == "deposit" ? 'Deposit Amount' : 'Deposits Amount')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-vlm',$deal,'fields') ? '
						<th>'.lang('Volume').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-bns',$deal,'fields') ? '
						<th>'.lang('Bonus  Amount').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-withd',$deal,'fields') ? '
						<th>'.lang('Withdrawal Amount').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-chrgb',$deal,'fields') ? '
						<th>'.lang('ChargeBack Amount').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-ntrv',$deal,'fields') ? '
						<th>'.lang(ptitle('Net Revenue')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-trades',$deal,'fields') ? '
						<th>'.lang(ptitle('Trades')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						
						<th>'.lang('Sale Status').''.($debugshowhide ? "1":'').'</th>
						'. ( allowView('af-slsnt',$deal,'fields')   ? 
							
							
                            '<th>'.lang('Last Sale Note').''.($debugshowhide ? "1":'').'</th>
                            <th>'.lang('Last Sale Note Date').''.($debugshowhide ? "1":'').'</th>
							' : '' ).'
							
						'. ($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 ? '' : 	
						'<th>'.lang('Commission').''.($debugshowhide ? "1":'').'</th>').'
						<th>'.lang('Admin Notes').''.($debugshowhide ? "1":'').'</th>
					</tr></thead>
					<tfoot>
						<th>'.($debugshowhide ? "1":'').'</th>
						'.($displayForex==1 ? '
						<th>'.($debugshowhide ? "1":'').'</th>
						':'').'
						<th>'.($debugshowhide ? "1":'').'</th>
						' . (allowView('af-mail',$deal,'fields') ? '
						<th>'.($debugshowhide ? "1":'').'</th>' : '' ) . '
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>'
						.(allowView('af-ftd',$deal,'fields') ? '<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						' : '').'
                                                <th>'.($debugshowhide ? "1":'').'</th>
						' . (allowView('af-ftda',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalFTD).''.($debugshowhide ? "1":'').'</th>
						' : '').'
						' . (allowView('af-depo',$deal,'fields') ? '
						<th style="text-align: left;">'.$totalTotalDeposit.''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-depoam',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalDepositAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-vlm',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalVolumeAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-bns',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalBonusAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-withd',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalWithdrawalAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-chrgb',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalChargeBackAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-ntrv',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalNetRevenue).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-trades',$deal,'fields') ? '
						<th style="text-align: left;">'.$total_Traders.''.($debugshowhide ? "1":'').'</th>
						' : '').'
						<th>'.($debugshowhide ? "1":'').'</th>
						
						'. ( allowView('af-slsnt',$deal,'fields')   ? 
						'<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
							' : '' ).'
							'. ($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 ? '' : 
						'<th style="text-align: left;">'.price($totalTotalCom).''.($debugshowhide ? "1":'').'</th>').'
						<th></th>
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>
				           
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
											
											saveReportToMyFav(name, \'trader\',user,level,type);
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
					//MODAL
		// $set->content .= getValidateTraderMerchantScript();
		$myReport = lang("Trader");
		include "common/ReportFieldsModal.php";
		
                //excelExporter($tableStr,'Trader');
		theme();
		
?>