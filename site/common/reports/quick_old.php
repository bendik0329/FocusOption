<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$pageTitle = lang('Quick Summary Report');
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
		</style>
		';
		$filename = "QuickSummary_data_" . date('YmdHis');
	
	
	if($userlevel == 'manager')
	$group_id       = $set->userInfo['group_id'];

		$listReport = '';
                
                // List of wallets.
                $arrWallets = [];
                // $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1;";
				$merchant_id = isset($merchant_id) && $merchant_id>0 ? $merchant_id : 0;
				$merchantsA  = getMerchants($merchant_id,1);
                // while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
				foreach ($arrWallets as $arrWallet){
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
		$l = -1;
					
					
		$displayForex = 0;
			$tradersProccessedForLots= array();
			$tradersProccessedForPNL= array();
		
		// $sql = "SELECT * FROM merchants WHERE valid='1' ORDER BY type, pos";
		// $qq = function_mysql_query($sql,__FILE__);
		foreach ($merchantsA as $ww){
		
		// while ($ww = mysql_fetch_assoc($qq)) {
		
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
							$totalCPI  = 0;
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


if($_GET['com']) 	echo '1: ' . $totalCom.'<Br>';	                            
                            
                            $totalTraffic = [];
                            $arrClicksAndImpressions     = getTotalClicksAndImpressions(
                                $arrRange['from'], 
                                $arrRange['to'], 
                                $ww['id'], 
                                (isset($affiliate_id) && $affiliate_id != '' ? $affiliate_id : null),
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
				   
				   if($_GET['com']) 	echo '2: ' . $totalCom.'<Br>';	
                                
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
                                
                            if($_GET['com']) 	echo '3: ' . $totalCom.'<Br>';	    
                                
                                $strSql = "
								select * from (
								SELECT data_sales.status ,data_sales.amount ,data_sales.tranz_id , data_sales.id, data_reg.group_id,data_reg.trader_id,data_reg.affiliate_id,data_reg.merchant_id,data_reg.banner_id,data_reg.profile_id, data_reg.initialftddate, data_sales.type AS data_sales_type, data_sales.rdate AS data_sales_rdate  FROM data_sales AS data_sales "
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
                                if($_GET['com']) 	echo '4: ' . $totalCom.'<Br>';	
								
								
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
								if($_GET['com']) 	echo '5: ' . $totalCom.'<Br>';	
								
								
										//******* qualification ftds
							   $ftdUsersQualified = '';
                         $FILTERbyTrader = !empty($trader_id)? $trader_id : 0;
                         $selected_group_id = ($gorup_id<>"")? $group_id : -1;
							 $qftdQuery  = "SELECT * FROM `data_reg` where type<>'demo' and FTDqualificationDate>'0000-00-00 00:00:00' and FTDqualificationDate>'". $arrRange['from'] ." 00:00:00' and FTDqualificationDate <'". $arrRange['to'] ."' " . ($affiliate_id?" and affiliate_id = " . $affiliate_id  : '') . " and merchant_id = ". $ww['id']  
						 .(!empty($selected_group_id) && $selected_group_id>0 ? ' and group_id= '. $selected_group_id : '')  
						 .(!empty($FILTERbyTrader) ? ' and trader_id= '. $FILTERbyTrader : '') ;
						 $qftdQQ = function_mysql_query($qftdQuery,__FILE__);
						 
                        
							$new_qualified_ftd=0;
							$activeTrader=0;
							$qftdCom = 0;
                        if (!$needToSkipMerchant) {
							while ($arrFtd = mysql_fetch_assoc($qftdQQ)) {
                              
								$arrFtd['initialftddate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['rdate'] = $arrFtd['FTDqualificationDate'];
								$arrFtd['runningType'] = 'qualification';
                               
								
									$activeTrader++;  
                                    $arrFtd['isFTD'] = true;
									$b = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, -1, $arrDealTypeDefaults, $arrFtd);
									
								
									$qftdCom +=$b;
									$totalCom+=$b;
									
                                }
                            }
							
							
							      if ($_GET['com'])
								echo 'Commission after Qftd: ' . $qftdCom .'<Br>';
							if($_GET['com']) 	echo '6: ' . $totalCom.'<Br>';	
								
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
                                if($_GET['com']) 	echo '7: ' . $totalCom.'<Br>';	
                                
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
				  if($_GET['com']) 	echo '8: ' . $totalCom.'<Br>';	
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
						if($_GET['com']) 	echo '9: ' . $totalCom.'<Br>';	
			
			
			
			if ($set->deal_cpi==1){
						// installation
						$array = array();			
						$array['from']  	= 	$arrRange['from']; ;
						$array['to'] =$arrRange['to'];;
						$array['merchant_id'] = $merchantID;
						$array['type'] = 'install' ;
						$array['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
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
						
			$subcomm= 0;
			
						$selected_affiliate_id = ($userlevel=='affiliate' ? " and id = " . $set->userInfo['id'] . " "  : ( !empty($affiliate_id) ? " and id = " .$affiliate_id : "" ) );
						$groupPart = ($userlevel=='manager' || ($group_id!="" &&  ($group_id)>-1)) ? " and group_id = " .$group_id ." ": " " ;
						
						$qry = "select id,refer_id from affiliates where id in ( select distinct (id) as id from affiliates where 1=1  " . $selected_affiliate_id . " ) and valid = 1 and sub_com>0" . $groupPart;
						
						
						$qry = "select id,refer_id from affiliates where id in ( select distinct (id) as id from affiliates where 1=1  " . $selected_affiliate_id . " and refer_id>0 ) and valid = 1 and sub_com>0" . $groupPart;
						
						
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
							
							
								
						if($_GET['com']) 	echo '10: ' . $totalCom.'<Br>';	
						
				$filterFrom = $arrRange['from'];
				$filterTo   = $arrRange['to'];
				$boxaName   = "admin-quick-report-1";
                     
				                
				$tableArr = array(
						
					(object) array(
					  'id' => 'daily',
					  'str' => ($display_type == "daily" ? '<td style="text-align: center;">' . $arrRange['from'].' - '.$arrRange['to'] . '</td>' : '')
					),
					(object) array(
					  'id' => 'weekly',
					  'str' => ($display_type == "weekly" ? '<td style="text-align: center;">'.$arrRange['from'].' - '.$arrRange['to'].'</td>' : '')
					),
					(object) array(
					  'id' => 'monthly',
					  'str' => ($display_type == "monthly" ? '<td style="text-align: center;">'.$arrRange['from'].' - '.$arrRange['to'].'</td>' : '')
					),
					(object) array(
					  'id' => 'name',
					  'str' => '<td style="text-align: left;">'.$ww['name'].'</td>'
					),
					(object) array(
					  'id' => 'totalViews',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=clicks'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.@number_format($totalTraffic['totalViews'],0).'</a></td>'
					),
					(object) array(
					  'id' => 'totalClicks',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=clicks'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.@number_format($totalTraffic['totalClicks'],0).'</a></td>'
					));
					if($set->deal_cpi){
						array_push($tableArr,
						(object) array(
						  'id' => 'totalCPI',
						  'str' => '<td style="text-align: center;">'.@number_format($totalCPI,0).'</td>'
						)
						);
					}
					
					array_push($tableArr,
					(object) array(
					  'id' => 'totalClicks_totalViews',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'ftd_total_traffic',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'commission_traffic',
					  'str' => '<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'EPC',
					  'str' => '<td style="text-align: center;">'.@price($totalCom/$totalTraffic['totalClicks']).'</td>'
					),
					(object) array(
					  'id' => 'total_leads',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>'
					),
					(object) array(
					  'id' => 'total_demo',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>'
					),
					(object) array(
					  'id' => 'total_real',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($ftd_amount['amount']).'</td>'
					),
					
					(object) array(
					  'id' => 'real_ftd',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=totalftd">'.$real_ftd.'</a></td>'
					),
					(object) array(
					  'id' => 'real_ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($real_ftd_amount).'</td>'
					),
					
					(object) array(
					  'id' => 'depositAccount',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=transactions'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>'
					),
					(object) array(
					  'id' => 'sumDeposits',
					  'str' => '<td style="text-align: center;">'.price($sumDeposits).'</td>'
					),
					(object) array(
					  'id' => 'volume',
					  'str' => '<td style="text-align: center;">'.price($volume).'</td>'
					),
					(object) array(
					  'id' => 'bonus',
					  'str' => '<td style="text-align: center;">'.price($bonus).'</td>'
					),
					(object) array(
					  'id' => 'Withdrawal',
					  'str' => '<td style="text-align: center;">'.price($withdrawal).'</td>'
					),
					(object) array(
					  'id' => 'ChargeBack',
					  'str' => '<td style="text-align: center;">'.price($chargeback).'</td>'
					),
					(object) array(
					  'id' => 'NetRevenue',
					  'str' => '<td style="text-align: center;">'.price($netRevenue).'</td>'
					),
					
					(object) array(
					  'id' => 'PNL',
					  'str' => '<td style="text-align: center;">'.price($totalPNL).'</td>'
					),
					
					(object) array(
					  'id' => 'activeTraders',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.$arrRange['from'].'&to='.$arrRange['to'].'&merchant_id='.$ww['id'].'&type=activeTrader">'.$activeTrader.'</a></td>'
					),
										
					(object) array(
					  'id' => 'Commission',
					  'str' => '<td style="text-align: center;">'.price($totalCom).'</td>'
					)				
				);
				
				$listReport .= '<tr>'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'</tr>';
				
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalCPIM += $totalCPI;
				$totalLeadsAccounts += $totalLeads;
				$totalDemoAccounts += $totalDemo;
				$totalRealAccounts += $totalReal;
				$totalFTD += $ftd;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount['amount'];
				$totalDepositAmount += $sumDeposits;
				$totalVolume += $volume;
				$totalBonus += $bonus;
				$totalWithdrawal += $withdrawal;
				$totalChargeback += $chargeback;
				$totalNetRevenue += $netRevenue;
				$totalActiveTraders += $activeTrader;
				$totalFooterPNL += $totalPNL;
				$totalComs += $totalCom;
				$totalRealFtd+=$real_ftd;
				$totalRealFtdAmount+=$real_ftd_amount;
                                
                            unset($arrRange); // Clear up the memory.
			} // End of time-periods loop.
                        
                    // Mark given wallet as processed.
                    $arrWallets[$ww['wallet_id']] = true;
		}
                
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		
		
		$tableArr = Array(
						
			(object) array(
			  'id' => 'daily',
			  'str' => ($display_type == "daily" ? '<th class="table-cell">'.lang('Period').'</th>' : '')
			),
			(object) array(
			  'id' => 'weekly',
			  'str' => ($display_type == "weekly" ? '<th class="table-cell">'.lang('Period').'</th>' : '')
			),
			(object) array(
			  'id' => 'monthly',
			  'str' => ($display_type == "monthly" ? '<th class="table-cell">'.lang('Period').'</th>' : '')
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th  class="table-cell" style="text-align: left;">'.lang('Merchant').'</th>'
			),
			(object) array(
			  'id' => 'totalViews',
			  'str' => '<th class="table-cell">'.lang('Impressions').'</th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th class="table-cell">'.lang('Clicks').'</th>'
			));
			if($set->deal_cpi){
			array_push($tableArr,
					(object) array(
					  'id' => 'totalCPI',
					  'str' => '<th class="table-cell">'.lang('Installation').'</th>'
					)
				);
			}
			array_push($tableArr,
			(object) array(
			  'id' => 'totalClicks_totalViews',
			  'str' => '<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>'
			),
			(object) array(
			  'id' => 'ftd_total_traffic',
			  'str' => '<th class="table-cell">'.lang(ptitle('Click to Account')).'</th>'
			),
			(object) array(
			  'id' => 'commission_traffic',
			  'str' => '<th class="table-cell">'.lang(ptitle('Click to Sale')).'</th>'
			),
			(object) array(
			  'id' => 'EPC',
			  'str' => '<th class="table-cell">'.lang(ptitle('EPC')).'</th>'
			),
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th  class="table-cell">'.lang(ptitle('Lead')).'</th>'
			),
			(object) array(
			  'id' => 'total_demo',
			  'str' => '<th class="table-cell">'.lang(ptitle('Demo')).'</th>'
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th class="table-cell">'.lang(ptitle('Accounts')).'</th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th class="table-cell">'.lang('FTD').'</th>'
			),
			(object) array(
			  'id' => 'ftd_amount',
			  'str' => '<th class="table-cell">'.lang('FTD Amount').'</th>'
			),
			
			(object) array(
			  'id' => 'real_ftd',
			  'str' => '<th class="table-cell">'.lang('RAW FTD').'</th>'
			),
			(object) array(
			  'id' => 'real_ftd_amount',
			  'str' => '<th class="table-cell">'.lang('RAW FTD Amount').'</th>'
			),
			
			(object) array(
			  'id' => 'depositAccount',
			  'str' => '<th class="table-cell">'.lang('Total Deposits').'</th>'
			),
			(object) array(
			  'id' => 'sumDeposits',
			  'str' => '<th class="table-cell">'.lang('Deposit Amount').'</th>'
			),
			(object) array(
			  'id' => 'volume',
			  'str' => '<th class="table-cell">'.lang('Volume').'</th>'
			),
			(object) array(
			  'id' => 'bonus',
			  'str' => '<th class="table-cell">'.lang('Bonus Amount').'</th>'
			),
			(object) array(
			  'id' => 'Withdrawal',
			  'str' => '<th class="table-cell">'.lang('Withdrawal Amount').'</th>'
			),
			(object) array(
			  'id' => 'ChargeBack',
			  'str' => '<th class="table-cell">'.lang('ChargeBack Amount').'</th>'
			),
			(object) array(
			  'id' => 'NetRevenue',
			  'str' => '<th class="table-cell">'.lang(ptitle('Net Revenue')).'</th>'
			),
			(object) array(
			  'id' => 'PNL',
			  'str' => '<th class="table-cell">'.lang(ptitle('PNL')).'</th>'
			),
			(object) array(
			  'id' => 'activeTraders',
			  'str' => '<th class="table-cell">'.lang(ptitle('Active Traders')).'</th>'
			),
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th class="table-cell">'.lang('Commission').'</th>'
			)				
		);
		
		
		
		
		$tableArr2 = Array(
						
			(object) array(
			  'id' => 'daily',
			  'str' => ($display_type == "daily" ? ($display_type ? '<th></th>' : '') : '')
			),
			(object) array(
			  'id' => 'weekly',
			  'str' => ($display_type == "weekly" ? ($display_type ? '<th></th>' : '') : '')
			),
			(object) array(
			  'id' => 'monthly',
			  'str' => ($display_type == "monthly" ? ($display_type ? '<th></th>' : '') : '')
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th style="text-align: left;"><b>'.lang('Total').':</b></th>'
			),
			(object) array(
			  'id' => 'totalViews',
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=clicks'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressions.'</a></th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th><a href="/'.$userlevel .'/reports.php?act=clicks'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicks.'</a></th>'
			));
			
			if($set->deal_cpi){
				array_push($tableArr2,
				(object) array(
				  'id' => 'totalCPI',
				  'str' => '<th>'.$totalCPIM.'</th>'
				)
				);
			}
			
			array_push($tableArr2,
			(object) array(
			  'id' => 'totalClicks_totalViews',
			  'str' => '<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'ftd_total_traffic',
			  'str' => '<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'commission_traffic',
			  'str' => '<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'EPC',
			  'str' => '<th>'.@price($totalComs/$totalClicks).'</th>'
			),
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th><a href="'.$set->SSLprefix.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'total_demo',
			  'str' => '<th><a href="'.$set->SSLprefix.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th><a href="'.$set->SSLprefix.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th><a href="'.$set->SSLprefix.$userlevel .'/reports.php?act=trader'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd_amount',
			  'str' => '<th>'.price($totalFTDAmount).'</th>'
			),
			
			(object) array(
			  'id' => 'real_ftd',
			  'str' => '<th><a href="'.$set->SSLprefix.$userlevel .'/reports.php?act=trader&'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>'
			),
			(object) array(
			  'id' => 'real_ftd_amount',
			  'str' => '<th>'.price($totalRealFtdAmount).'</th>'
			),
			
			(object) array(
			  'id' => 'depositAccount',
			  'str' => '<th><a href="'.$set->SSLprefix.$userlevel .'/reports.php?act=transactions'.($affiliate_id ? '&affiliate_id='.$affiliate_id : "").'&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>'
			),
			(object) array(
			  'id' => 'sumDeposits',
			  'str' => '<th>'.price($totalDepositAmount).'</th>'
			),
			(object) array(
			  'id' => 'volume',
			  'str' => '<th>'.price($totalVolume).'</th>'
			),
			(object) array(
			  'id' => 'bonus',
			  'str' => '<th>'.price($totalBonus).'</th>'
			),
			(object) array(
			  'id' => 'Withdrawal',
			  'str' => '<th>'.price($totalWithdrawal).'</th>'
			),
			(object) array(
			  'id' => 'ChargeBack',
			  'str' => '<th>'.price($totalChargeback).'</th>'
			),
			(object) array(
			  'id' => 'NetRevenue',
			  'str' => '<th>'.price($totalNetRevenue).'</th>'
			),
				(object) array(
			  'id' => 'PNL',
			  'str' => '<th>'.price($totalFooterPNL).'</th>'
			),
			(object) array(
			  'id' => 'activeTraders',
			  'str' => '<th>'.($totalActiveTraders).'</th>'
			),
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.price($totalComs).'</th>'
			)				
		);
	
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form method="get" onsubmit = "return submitReportsForm(this)">
				<table><tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td width=160>'.lang('Affiliate ID').'</td>
						'.($userlevel == "admin"? '<td style="padding-left:20px">'.lang('Group ID').'</td>':'').'
						<td style="padding-left:20px">'.lang('Search Type').'</td>
						<td></td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="affiliate_id" style="width: 60px; text-align: center;" /-->
					<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
					</td>
					<!--td><input type="text" name="group_id" value="'.$group_id.'" id="group_id" style="width: 60px; text-align: center;" /></td-->
                                        '.($userlevel == 'admin'?'<td width="100" style="padding-left:20px">
                                            <select name="group_id" style="width: 100px;">
                                                <option value="">'.lang('All Groups').'</option>'
                                                . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                    . lang('General') 
                                                . '</option>' 
                                                . listGroups($group_id) 
                                            . '</select>
                                        </td>':'').'
					<td style="padding-left:20px"><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle"  class="table">'.lang('Quick Summary Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
		//width 2000
			$tableStr = '
			<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="quickTbl">
				<thead><tr  class="table-row">
					'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'
				</tr></thead><tfoot><tr>
					'.setTable($tableArr2, $boxaName, $set->userInfo['productType'], '').'
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
						txt = "<table id=\'quickData\' class=\'mdlReportFieldsData\'>";
						txt += "<thead>" + thead + "</thead>";
						txt += "<tbody>";
						$($("#quickTbl")[0].config.rowsCopy).each(function() {
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
		//MODAL
		$myReport = lang("Quick Summary");
		include "common/ReportFieldsModal.php";
		//excelExporter($tableStr, 'Quick');
		$set->content .= $tableStr . '</div>' . getPager();
		theme();
		
?>