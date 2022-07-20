<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];

if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

if($userlevel == "manager"){
$globalWhere = "group_id = '".$set->userInfo['group_id']."' AND ";
// die ('greger');
}
else
$globalWhere  = '';



if ($set->showProductsPlace==1){
$productsArray = array();
$sql = "select * from products_items";
$prdcResource=  function_mysql_query($sql,__FILE__);
while ($row = mysql_fetch_assoc($prdcResource)){
	$productsArray[$row['id']] = $row;
}
}

$sql = "select id,name from affiliates_profiles where valid =1";
$qqProfiles = function_mysql_query($sql);
$listProfiles = array();
while($wwProfiles = mysql_fetch_assoc($qqProfiles)){
	$listProfiles[$wwProfiles['id']] = $wwProfiles['name'];
}

$pageTitle = lang(ptitle('Trader Report'));
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
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
		<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
			<script>
			$(document).ready(function(){
				$("#combobox").combobox("autocomplete","'. $_GET['affiliate_id'] .'");
			});
			</script>
		 <!-- jQuery UI Autocomplete css -->
		<style>
		.custom-combobox {
			position: relative;
			display: inline-block;
		  }
		  .custom-combobox-toggle {
			position: absolute;
			top: 0;
			bottom: 0;
			margin-left: -1px;
			background: white;
			border-radius: inherit;
			border-color: #CECECE;
			border-left: 0;
			color: #1F0000;
		  } 
		  .custom-combobox-input {
			margin: 0;
			padding: 5px 10px;
			width: 120px;
			background: white;
			border-radius: inherit;
			border-color: #CECECE;
			color: #1F0000;
			font-weight: inherit;
			font-size: inherit;
		  }
		  .ui-autocomplete { 
			height: 200px; 
			width:  310px;
			overflow-y: scroll; 
			overflow-x: hidden;
		  }
		</style>';
		$filename = "Trader_data_" . date('YmdHis');
	
if($userlevel == 'manager') {
 $group_id = $set->userInfo['group_id'];
 // die ('d: ' . $group_id);
}


                $arrAllTraders = [];
                $merchant_id = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;
                
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                $resourceWallets = function_mysql_query($sql,__FILE__);
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
				

				
				output_memory('','A1','');
				
                
				$merchantsArray = array();
				$merchantsAr = getMerchants($merchant_id,1);
				foreach ($merchantsAr as $arrMerchant) {
				
				$allTranz = array();
			/* 	echo '<pre>';
				var_dump($arrMerchant);
				echo '</pre>';
				die(); */
				
				
				 $loopedMerchant_ID = $arrMerchant['id'];
					
					
					
                    $where = '';
                    
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($group_id) $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($set->showDynamicFilters==1 && $dynamic_filter) $where .= " AND dynamic_filter='".$dynamic_filter."' ";
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
                    $ftd = $totalTraders = $depositAmount = $total_deposits= $microPaymentsCount = $microPaymentsAmount = $ftdAmount = $volumeAmount = 0;
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
			else {
				$typeFilter = " and 1 =2 ";
			}
				
			$typeFilter .= $typeDateFilter;
			
			
			$traders = implode(",",$arrAllTraders);
			$data_reg_from_fields = "
							data_reg.uid,
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
data_reg.product_id,
data_reg.isSelfDeposit,
data_reg.freeParam,
data_reg.freeParam2,
data_reg.freeParam3,
data_reg.freeParam4,
data_reg.freeParam5,
data_reg.banner_id,
". ($set->showDynamicFilters==1 ? "data_reg.dynamic_filter," : ""). " 
data_reg.status,
data_reg.type,
data_reg.trader_alias,
data_reg.email,
data_reg.phone,
data_reg.country,
data_reg.saleStatus,
data_reg.rdate,
data_reg.lastSaleNote,
data_reg.lastSaleNoteDate,
data_reg.campaign_id
";
			$qry = "select ".$data_reg_from_fields.($displayForex==1? ", (select count(*) from data_reg dg where dg.merchant_id>0 and dg.trader_id=data_reg.trader_id group by dg.trader_id) as sub_trader_count":"") . " from data_reg where merchant_id = " . $loopedMerchant_ID 
																.(!empty($traders) ?  " and trader_id in (" . $traders . " ) " : "")
																. $typeFilter
																.$where
																
																. " GROUP BY merchant_id, trader_id ; " 
																;
			
			
																
																
			
/* $new_Qry = "
CREATE TEMPORARY TABLE temp1 ENGINE=MEMORY 
as (" .$qry . " ) " ;  */

			
				// die ($new_Qry);
				
				output_memory('','C1','');

				
					 
					
					if ($set->deal_pnl == 1) {
						$BigMainqry = "select initialftddate,isSelfDeposit,affiliate_id, group_id,merchant_id , trader_id from data_reg where 1=1 " . $typeFilter .$where;   // for pnl Join fix
							$DataArray= array(
								  "affiliate_id"=> $affiliate_id,
								  "fromdate"=> $from,
								  "todate"=> $to,
								  "group_id"=> $group_id,
								  "banner_id"=> $banner_id,
								  "profile_id"=> $profile_id,
								  "country_id"=> $country_id,
								  "trader_id"=> $trader_id,
								  "merchant_id"=> $merchant_id);
								// die ($BigMainqry);  
							$pnlsql = generatePNLqueryForTraderReport($DataArray,false,$BigMainqry);
							$pnlResultsArray = array();
							$pnlsqlRsc = function_mysql_query($pnlsql);
							if(!empty($pnlsqlRsc)){
    							while ($pnlRow = mysql_fetch_assoc($pnlsqlRsc)){
    							
    								$pnlResultsArray[$pnlRow['merchant_id']][$pnlRow['trader_id']][] = $pnlRow;
    							}
							}
							
					}
			
					$tradersProccessedForLots= array();
					$tradersProccessedForPNL= array();
					$PNLPerformanceAgregationArray= array();
               

					$netDepositTransactions = array();


					$acountries = getLongCountries('sales');

					output_memory('','C2','');

					$trader_report_resource = function_mysql_query($qry);
					while ($traderInfo = mysql_fetch_assoc($trader_report_resource)){
					
					$arrRes = $traderInfo;
					
					$traderIssue = "";

					
					//if ($type=='ftd' || $type=='activeTrader'){	
					// if ($type=='ftd' || true){	
					// var_dump($traderInfo);
					if ($traderInfo['initialftddate']> "0000-00-00 00:00:00"){	

									
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
				
                            
					$traderInfo['traderIssue'] =($traderIssue);
					$arrFtd['traderIssue'] =($traderIssue);
		//									continue;
										}else {
											
                            
											
										}
										unset ($arrFtd);
											// commission part is calculated below that part is only to skip the calculation of RAW non qualified ftds when filter FTD.
					}
										
											
											
                    
						$totalCom=0;
						
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
				
							$qry = "select * from ( select dr.FTDqualificationDate , dr.banner_id, dr.profile_id, dr.country,dr.initialftddate , ds.id,dr.affiliate_id,ds.tranz_id,ds.trader_id,ds.merchant_id,ds.amount,ds.rdate,ds.status,ds.type as data_sales_type from 
									(select data_reg.initialftddate,data_reg.affiliate_id,data_reg.trader_id, data_reg.country, data_reg.merchant_id,data_reg.banner_id, data_reg.profile_id,data_reg.FTDqualificationDate  from 
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
						$microPaymentsCount=0;
						$microPaymentsAmount = 0 ;



						
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
								
								if ($set->showMicroPaymentsOnReports==1  && processMicroPaymentRecord($arrAmount)){
										$microPaymentsCount++;
										$microPaymentsAmount += $arrAmount['amount'];
								}
								
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
                                         WHERE merchant_id>0 and merchant_id = "' . $arrMerchant['id'] . '" and  trader_id = "' . $arrRes['trader_id'] . '" ' ;
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
                                            'trader_id'    => $ts['trader_id'],
											'initialftddate'    => $traderInfo['initialftddate'],
											'FTDqualificationDate'    => $traderInfo['FTDqualificationDate'],
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
								
								
				
									// $pnlRecordArray=array();
									// $pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
									// $pnlRecordArray['merchant_id']  = $arrMerchant['id'];
									// $pnlRecordArray['group_id']  = $group_id;
									// $pnlRecordArray['trader_id']  = $arrRes['trader_id'];
									//$pnlRecordArray['searchInSql']  = $searchInSql;
									//$pnlRecordArray['fromdate']  = $from;
									//$pnlRecordArray['todate']  = $to;
//							$sql = generatePNLqueryForTraderReport($pnlRecordArray,false,$BigMainqry);
							
							
							$currentPnlArray = $pnlResultsArray[$arrMerchant['id']][$arrRes['trader_id']];
									 // var_dump($currentPnlArray);
									 // die();
								 $pnlCom = 0;
							
								 // $traderStatsQ = function_mysql_query($sql,__FILE__);
								 // while($ts = mysql_fetch_assoc($traderStatsQ)){
									if (!empty($currentPnlArray))
							foreach ($currentPnlArray as $currentPnlItem){
									 // $pnlRes[] = $ts;
									 $pnlRes[] = $currentPnlItem;
									 
								 }
									
									
								}
							else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
									
									
									
										if (empty($PNLPerformanceAgregationArray[$arrMerchant['id']])){
									
									
												$recordTypes = $isCasino==1 ? '"static","bets","wins","jackpot", "bonuses","removed_bonuses"' : "'PNL'";
									
		
															 $sql = 'SELECT  sum(pnltable.amount) as amount,max(pnltable.rdate) as rdate ,max(pnltable.trader_id) as trader_id ,max(pnltable.merchant_id) as merchant_id,max(pnltable.banner_id) as banner_id,max(pnltable.profile_id) as profile_id,max(pnltable.affiliate_id) as affiliate_id FROM '.$set->pnlTable.' pnltable
													 inner join data_reg on pnltable.merchant_id = data_reg.merchant_id and pnltable.trader_id  = data_reg.trader_id and data_reg.type<>"demo"
													 WHERE pnltable.type in('.$recordTypes.') and pnltable.merchant_id>0 and pnltable.amount<>0   '
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
												 'amount'       =>  ($isCasino==1 ?  calculateCasinoRevenue($pnlamount,$ts['type']) : $pnlamount) ,
												 'initialftddate'       =>  $ts['initialftddate']
												 ];
												 
										// var_dump($row);	
												$totalPNL = $totalPNL + $pnlamount;
												
															 
										
											if (!empty($dealsForAffiliate['pnl'])){
												$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
												
												
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after pnl " . $tmpCom . "<br/>";
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
							 
							 
							
							
					/* 		if (hasValidDate($arrRes['initialftddate'])) {
								
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
										// $arrFtd['rdate'] = $arrFtd['FTDqualificationDate'];
										//	// $arrTradersPerMerchants[$arrMerchant['id'] . '-' . $arrFtd['trader_id']] = $arrFtd['trader_id'];
										//   // if (!in_array($arrFtd['trader_id'], $arrTradersPerMerchants)) {
										$ftdComFromLoop = getCommission($arrFtd['rdate'], $arrFtd['rdate'], 0, $arrFtd['group_id'], $arrDealTypeDefaults, $arrFtd);
										$totalCom += $ftdComFromLoop;
										if($_GET['com'] && $traderIDForCheck)   {
											   echo " Commission ftd loop " . $ftdComFromLoop . "<br/>";
										   } 
									}
								}
							}
                         */	

						 if (hasValidDate($arrRes['FTDqualificationDate'])) {
								
								// var_dump($traderInfo);
								// die();
								$traderInfo['rdate'] = $traderInfo['FTDqualificationDate'];
								$traderInfo['amount'] = $traderInfo['ftdamount'];
								$FtdCount = $traderInfo['amount'];
								$traderInfo['isFTD'] = true;
								$traderInfo['trades'] = $totalTraders;
								$traderInfo['traderHasFTD'] = $traderInfo['initialftddate']=='0000-00-00 00:00:00' ? false : true;
								$ftdComFromLoop = getCommission($traderInfo['rdate'], $traderInfo['rdate'], 0, $traderInfo['group_id'], $arrDealTypeDefaults, $traderInfo);
								$totalCom += $ftdComFromLoop;
									if($_GET['com'] && $traderIDForCheck)   {
										   echo " Commission ftd loop " . $ftdComFromLoop . "<br/>";
								   } 
						}
				
                        
						$traderInfo['rdate'] =  $arrRes['rdate'];
						$traderInfo['initialftddate'] =  $arrRes['initialftddate'];
						$traderInfo['FTDqualificationDate'] =  $arrRes['FTDqualificationDate'];
						
						
					
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

					$ClickFrom = changeDate($traderInfo['rdate'],-4);
					$ClickTo = changeDate($traderInfo['rdate'],+4);
					
                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].'</td>';
							if($set->showCampaignOnTraderReport){
								$listReport .= '<td>'.$arrRes['campaign_id'].'</td>';
							}
							if( $displayForex==1)
								$listReport .= ($arrRes['sub_trader_count']>0  ? '<td>
<a href="/'. $userlevel .'/reports.php?act=subtraders&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$loopedMerchant_ID.'&trader_id='.$arrRes['trader_id'].'">'.$arrRes['sub_trader_count'].'</a>
</td>' : '<td/>');
							
							$hidePendingReason = hasValidDate($arrRes['FTDqualificationDate']) || ($affInfo['qualify_type']=='' || ($affInfo['qualify_type']=='default' && $merchantsAr['qualify_type']==''  ));
							
							
							 $foundcountry = $acountries[$arrRes['country']];
							 $ftdAmount = $arrRes['ftdamount'];
							
						
							// die ($arrRes['initialftddate']);
							if ($set->showDynamicFilters==1){
								listDynamicFilters(0,0);
								$dynamicName = empty($dynamicFilters[$arrRes['dynamic_filter']]['caption']) ? $dynamicFilters[$arrRes['dynamic_filter']]['name'] : $dynamicFilters[$arrRes['dynamic_filter']]['caption'];


							// $a = listDynamicFilters($dynamic_filter,1,true)			;					
							// die ($a);
								
							}
							
                            $listReport .='<td>'.$arrRes['trader_alias'].'</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.$arrRes['email'].'</td>' : '' ) . '
                            ' . ($set->ShowPhonesOnTraderReportForAdmin ? '<td>'.$arrRes['phone'].'</td>' : '' ) . '
                            <td title="'.(date("d/m/Y H:i:s", strtotime($traderInfo['rdate']))).'">'.(date("d/m/Y H:i:s", strtotime($traderInfo['rdate']))).'</td>
                            <td>
<span style="color: '.$color.';">'.$arrRes['type'].'</span>
</td>
                            <td>'.$foundcountry.'</td>
							
							'. ($set->showDynamicFilters==1 ? 
                            '<td>'.$dynamicName.'</td>': '' ).'
							
                            <td>'.$arrRes['affiliate_id'].'</td>
                            <td>
<a href="/'. $userlevel .'/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a>
</td>
                            '. ($set->showProductsPlace==1 ? '
							<td>'.($arrRes['product_id']).'</td>
                            <td>'.ucwords($productsArray[$arrRes['product_id']]['title']).'</td>' : '' )
							.'
							<td>'.$arrMerchant['id'].'</td>
                            <td>'.strtoupper($arrMerchant['name']).'</td>
                            <td style="text-align: left;">'.$bannerInfo['id'].'</td>
                            <td style="text-align: left;">'.$bannerInfo['title'].'</td>
                            <td>'.$bannerInfo['type'].'</td>
                            <td>'.$bannerInfo['language_name'].'</td>
                            <td>'.$arrRes['profile_id'].'</td>
                            <td>'.$listProfiles[$arrRes['profile_id']].'</td>
                            '.($isCasino==1 ? ( empty($arrRes['status'])?"<td>" . lang('real'). "</td>": "<td>" . $arrRes['status'] .'</td>') : '').'
                            <td>'.$arrRes['freeParam'].'</td>
                            <td>'.$arrRes['freeParam2'].'</td>
                            <td>'.$arrRes['freeParam3'].'</td>
                            <td>'.$arrRes['freeParam4'].'</td>
                            <td>'.$arrRes['freeParam5'].'</td>
                            <td>' . (isset($arrRes['tranz_id']) ? $arrRes['tranz_id'] : '') . '</td>
                            <td title="'.(empty($arrRes['tranz_id']) ? "" :  date("d/m/Y H:i:s", strtotime($arrRes['initialftddate'])) ) .'">'.(!empty($arrRes['tranz_id']) ?  date("d/m/Y H:i:s", strtotime($arrRes['initialftddate'])): "").'</td>
							
                            <td>'.(!empty($arrRes['tranz_id']) ? price($ftdAmount) : "" ).'</td>
							
                            <td>'.($arrRes['isSelfDeposit']==1 ? lang('Yes') : "" ).'</td>
							'.	($set->ShowNextDepositsColumn ==1  ? '
							
                            <td>'.($depositAmount>0 && $ftdAmount>0 && $depositAmount > $ftdAmount ?  price($depositAmount-$ftdAmount) : "" ).'</td>
                            <td>'.($total_deposits>1 ? $total_deposits-1 : "" ).'</td>
							' : '' ).'

							'.	($set->showMicroPaymentsOnReports ==1  ? '
							
                            <td>'.($microPaymentsCount ).'</td>
                            <td>'.($microPaymentsAmount ).'</td>
							' : '' ).'
							
							
							
							
							
                            <td>
<a href="/'. $userlevel .'/reports.php?act=transactions&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$loopedMerchant_ID.'&trader_id='.$arrRes['trader_id'].'&type=deposit">'.$total_deposits.'</a>
</td>
                            <td>'.price($depositAmount).'</td>
                            <td>'.price($volumeAmount).'</td>
                            <td>'.price($bonusAmount).'</td>
                            <td>'.price($withdrawalAmount).'</td>
                            <td>'.price($chargebackAmount).'</td>
                            <td>'.price($netRevenue).'</td>
                            <td>'.$totalTraders.'</td>
							'. ( $displayForex==1 ? 
                            '<td>'.($totalLots).'</td>' : '' ).'
							<td>'.(hasValidDate($arrRes['FTDqualificationDate']) ? $arrRes['FTDqualificationDate'] : "" ).'</td>
							'. ($set->deal_pnl==1  ? 
							'<td id="pnl">'.price($totalPNL).'</td>' : '').'
                            <td>'.$arrRes['saleStatus'].'</td>
							'.($set->displayLastMessageFieldsOnReports ==1 ? '
                            <td>'.$arrRes['lastSaleNoteDate'].'</td>
                            <td>'.$arrRes['lastSaleNote'].'</td>':'').'
                            <td>'.($arrRes['lastTimeActive']=='1969-12-31 23:00:00' || $arrRes['lastTimeActive'] == '0000-00-00 00:00:00' ? '-' : $arrRes['lastTimeActive']).'</td>
                            <td>'.price($totalCom) 
					.'</td>
                            <td>'.(!empty($reason) ? $reason : ($hidePendingReason ? '' : lang('Pending'))). (!empty($traderInfo['traderIssue']) ? 
						'<br><span style="color:red;">'. strtoupper($traderInfo['traderIssue']) .'</span>' : "").'</td>
                            '.((!empty($arrRes['uid']) && $arrRes['uid']!='')?'<td><a title="deTails" href="/'. $userlevel .'/reports.php?act=clicks&from='.$ClickFrom.'&to='.$ClickTo.'&merchant_id='.$loopedMerchant_ID.'&unique_id='.$arrRes['uid'].'">'. lang('View') .'</a></td>':'<td></td>').'
                        </tr>';
                    
			
					
                    //if (!in_array($arrRes['merchant_id'] . '-' . $arrRes['trader_id'],   $arrTradersPerMerchants)) 
					{
                        $arrTradersPerMerchants[] = $arrRes['merchant_id'] . '-' . $arrRes['trader_id']; //$arrRes['trader_id'];
                        $totalTotalCom += $totalCom;
                        $totalFTD += $ftdAmount;
                        $totalNetRevenue += $netRevenue;
					

					if ($_GET['deb']==1) {
					var_dump($arrTradersPerMerchants);
					die('totalcom: ' . $totalCom);
					}
					
					
                    }

                    $totalDepositAmount += $depositAmount;
                    $totalVolumeAmount += $volumeAmount;
                    $totalBonusAmount += $bonusAmount;
                    $totalTotalDeposit += $total_deposits;
					$totalmicroPaymentsCount += $microPaymentsCount;
					$totalmicroPaymentsAmount += $microPaymentsAmount;
                    $totalTrades += $totalTraders;
                    $totalLotsamount += $totalLots;
                    $totalPNLamount += $totalPNL;
                    $totalWithdrawalAmount += $withdrawalAmount;
                    $totalChargeBackAmount += $chargebackAmount;
                    $ftdExist[] = $firstDeposit['trader_id'];
                    
					
					$l++;
					
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$microPaymentsCount=$microPaymentsAmount= $totalTraders=$totalLots=0;
                }
							}
     	if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form id="frmRepo" action="'.$set->SSLprefix.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
				<input type="hidden" name="act" value="trader_old" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						<td width=160>'.lang('Affiliate ID').'</td>
						<td style="padding-left:20px">'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						<td>'.lang(ptitle('Trader Alias')).'</td>
						'.($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.lang(ptitle('Email')).'</td>':'').'
						'.($set->ShowPhonesOnTraderReportForAdmin ? '<td>'.lang(ptitle('Phone')).'</td>':'').'
						<td>'.lang(ptitle('Parameter')).'</td>
						<td>'.lang(ptitle('Parameter2')).'</td>
						'. ($userlevel == 'admin' ? '<td>'.lang('Group').'</td>':'') .'
					'.
						 ($set->showDynamicFilters==1 ? '<td>'.lang($set->dynamicFilterTitle).'</td>':'') .'
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td style="padding-right:20px"><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /-->
						<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
						</td>
						<td style="padding-left:20px"><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" onblur="validateMerchant(this)" /></td-->
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;"  /></td>
						<td><input type="text" name="trader_alias" value="'.$trader_alias.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						'.($set->ShowEmailsOnTraderReportForAdmin ? '<td><input type="text" name="email" value="'.$email.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>':'').'
						<td><input type="text" name="param" value="'.$param.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="'.$param2.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td-->
						'.($userlevel == 'admin'?'
						<td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                                                </td>':'').'
												
						'.($set->showDynamicFilters==1 ? 
						'<td>
                                                    <select name="dynamic_filter" style="width: 100px;">
                                                        <option value="">'.lang('All '.$set->dynamicFilterTitle).'</option>'
                                                        . listDynamicFilters($dynamic_filter,1,true) 
                                                . '</select>
                                                </td>':'').'
						<td>
							<select name="type" style="width: 110px;">
								<option value="allaccounts" '.($type == "allaccounts" ? 'selected' : '').'>'.lang(ptitle('All Accounts')).'</option>
								<option value="real" '.($type == "real" ? 'selected' : '').'>'.lang(ptitle('Accounts')).'</option>
								'.($hideDemoAndLeads? "": '<option value="lead" '.($type == "lead" ? 'selected' : '').'>'.lang(ptitle('Lead')).'</option>
								<option value="demo" '.($type == "demo" ? 'selected' : '').'>'.lang(ptitle('Demo')).'</option>').'
								<!--option value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option-->
								<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>
								<!--option value="revenue" '.($type == "revenue" ? 'selected' : '').'>'.lang('Revenue').'</option-->
                                                                <option value="totalftd" '.($type == "totalftd" ? 'selected' : '').'>'.lang('RAW FTD').'</option>
                                                                <option value="activeTrader" '.($type == "activeTrader" ? 'selected' : '').'>'.lang('Active Trader').'</option>
                                                                '.(!$hideDemoAndLeads? "": '<option value="frozen" '.($type == "frozen" ? 'selected' : '').'>'.lang('Frozen').'</option>').'
							</select>
						</td>
						<!--td><input type="button" value="'.lang('View').'" onClick="validateForm()"/></td-->
						<td><input type="submit" value="'.lang('View').'" /></td>
						
					</tr>
				</table>
				</form>
'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle"   class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="../images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
			//table 2600
				$tableStr ='<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="traderTbl">
					<thead><tr   class="table-row">
						<th  class="table-cell">'.lang(ptitle('Trader ID')).'</th>'
						.($set->showCampaignOnTraderReport?
								'<th  class="table-cell">'.lang('Campaign Id').'</th>':'')
							
						.( $displayForex==1 ? '
						<th  class="table-cell">'.lang(ptitle('Sub Traders')).'</th>' : '' ).'
						<th  class="table-cell">'.lang(ptitle('Trader Alias')).'</th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th  class="table-cell">'.lang(ptitle('Email')).'</th>' : '' ) . '
						' . ($set->ShowPhonesOnTraderReportForAdmin ? '<th  class="table-cell">'.lang(ptitle('Phone')).'</th>' : '' ) . '
						<th  class="table-cell">'.lang('Registration Date').'</th>
						<th  class="table-cell">'.lang(ptitle('Trader Status')).'</th>
						<th  class="table-cell">'.lang('Country').'</th>
						'. ($set->showDynamicFilters==1 ? 
						'<th  class="table-cell">'.lang($set->dynamicFilterTitle).'</th>':'').'
						
						<th  class="table-cell">'.lang('Affiliate ID').'</th>
						<th  class="table-cell">'.lang('Affiliate Username').'</th>
						'. ($set->showProductsPlace==1 ? '
						<th  class="table-cell">'.lang('Product ID').'</th>
						<th  class="table-cell">'.lang('Product Name').'</th>' : '' ) . '
						<th  class="table-cell">'.lang('Merchant ID').'</th>
						<th  class="table-cell">'.lang('Merchant Name').'</th>
						<th   class="table-cell"style="text-align: left;">'.lang('Creative ID').'</th>
						<th   class="table-cell"style="text-align: left;">'.lang('Creative Name').'</th>
						<th  class="table-cell">'.lang('Type').'</th>
						<th  class="table-cell">'.lang('Creative Language').'</th>
						<th  class="table-cell">'.lang('Profile ID').'</th>
						<th  class="table-cell">'.lang('Profile Name').'</th>
						'.($isCasino==1   ? '<th  class="table-cell">'.lang('Status').'</th>' : '' ).'
						<th  class="table-cell">'.lang('Param').'</th>
						<th  class="table-cell">'.lang('Param2').'</th>
						<th  class="table-cell">'.lang('Param3').'</th>
						<th  class="table-cell">'.lang('Param4').'</th>
						<th  class="table-cell">'.lang('Param5').'</th>
                        <th  class="table-cell">' . lang('Transaction ID') . '</th>
						<th  class="table-cell">'.($type == "deposit" ? lang('Deposit Date') : lang('First Deposit')).'</th>
						<th  class="table-cell">'.lang('FTD Amount').'</th>
						<th  class="table-cell">'.lang('Self Deposit').'</th>
						'. 
						($set->ShowNextDepositsColumn ==1  ? '
						<th  class="table-cell">'.lang('Total Next Deposits').'</th>
						<th  class="table-cell">'.lang('Next Deposits').'</th>' : '' ) . ' '.
						($set->showMicroPaymentsOnReports ==1  ? '
						<th  class="table-cell">'.lang('Total MicroPayments').'</th>
						<th  class="table-cell">'.lang('MicroPayments Amount').'</th>' : '' ) . ' 
						<th  class="table-cell">'.lang('Total Deposits').'</th>
						<th  class="table-cell">'.($type == "deposit" ? lang('Deposit Amount') : lang('Deposit Amount')).'</th>
						<th  class="table-cell">'.lang('Volume').'</th>
						<th  class="table-cell">'.lang('Bonus Amount').'</th>
						<th  class="table-cell">'.lang('Withdrawal Amount').'</th>
						<th  class="table-cell">'.lang('ChargeBack Amount').'</th>
						<th  class="table-cell">'.lang(ptitle('Net Deposit')).'</th>
						<th  class="table-cell">'.lang(ptitle('Trades')).'</th> '
						. ($displayForex==1  ? 
						'<th  class="table-cell">'.lang(ptitle('Lifetime Lots')).'</th>' : '' ) . '
						<th  class="table-cell">'.lang('Qualification Date').'</th>
						'. ($set->deal_pnl==1  ? 
						'<th  class="table-cell">'.lang(ptitle('PNL')).'</th>' : '' ) . '
						<th  class="table-cell">'.lang('Sale Status').'</th>
						'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th  class="table-cell">'.lang('Last Sale Note Date').'</th>
						<th  class="table-cell">'.lang('Last Sale Note').'</th>' : '' ).'
						<th  class="table-cell">'.lang('Last Time Active').'</th>
						<th  class="table-cell">'.lang('Commission').'</th>
						<th  class="table-cell">'.lang('Admin Notes').'</th>
						<th  class="table-cell">'.lang('Click Details').'</th>
					</tr></thead>
					<tfoot>
						<th></th>'
						.($set->showCampaignOnTraderReport?
								'<th></th>':'')
						.( $displayForex==1 ? '
						<th></th>' : '').'
						<th></th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th></th>' : '' ) . '
						' . ($set->ShowPhonesOnTraderReportForAdmin ? '<th></th>' : '' ) . '
						<th></th>
							'. ($set->showDynamicFilters==1 ? 
						'<th></th>':'').'
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						'. ($set->showProductsPlace==1 ? '
						<th></th>
						<th></th>' : '' ).'
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						'.($isCasino==1   ? '<th></th>' : '' ).'
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						
						<th style="text-align: left;">'.price($totalFTD).'</th>
						<th></th>
						'.	($set->ShowNextDepositsColumn ==1  ? '
						<th></th>
						<th></th>
						' : '' ).'
						'.	($set->showMicroPaymentsOnReports ==1  ? '
						<th></th>
						<th></th>
						' : '' ).'
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
						'. ($set->deal_pnl==1  ? 
						'
						
						<th style="text-align: left;">'.price($totalPNLamount).'</th>
						' : '' ).'
						<th></th>
						'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th></th>
						<th></th>' : '' ).'
						<th></th>
						<th style="text-align: left;">'.price($totalTotalCom).'</th>
						<th></th>
						<th></th>
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>
				';
				$tableStr .= getSingleSelectedMerchant();
				$tableStr .='<script>
				$(document).ready(function(){
					
					try{
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'traderData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					i = 0;
					$($("#traderTbl")[0].config.rowsCopy).each(function() {
						// console.log(i);
						i++;
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
											
											saveReportToMyFav(name, \'Trader\',user,level,type);
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
				';
				// $set->content .= getValidateTraderMerchantScript();
				$set->content.=$tableStr.'
			</div>'.getPager();
			
			//MODAL
		$myReport = lang("Trader");
		include "common/ReportFieldsModal.php";
		
//excelExporter($tableStr,'Trader');		
		theme();

?>