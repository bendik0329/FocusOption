<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$pageTitle = lang('Commission Report');
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
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
		</style>
		';
		$filename = "CommissionSummary_data_" . date('YmdHis');
	
	
	if(!isset($commission_type)) $commission_type = "All";
	
	
	if($userlevel == 'manager')
	$group_id       = $set->userInfo['group_id'];


if (!empty($trader_id) && !empty($merchant_id)){
	$r = mysql_fetch_assoc(function_mysql_query("select affiliate_id from data_reg where merchant_id = " . $merchant_id . " and trader_id = '" . $trader_id . "' limit 1;"));
	$affiliate_id = $r['affiliate_id'];
}


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
		
		$commissionArray = array();
		
		
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
                            
                            
                            $totalTraffic = [];
                            $arrClicksAndImpressions = getClicksAndImpressions(
                                $arrRange['from'], 
                                $arrRange['to'], 
                                $ww['id'], 
                                null, 
                                (isset($group_id) && $group_id != '' ? $group_id : null)
                            );
                            
                            $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
                            
                            $sql = "SELECT aff.id,aff.username, dg.* FROM data_reg dg LEFT JOIN affiliates aff on aff.id=dg.affiliate_id "
                                    . "WHERE dg.merchant_id>0 and dg.merchant_id = '" . $ww['id'] . "' AND "
                                    . " dg.rdate " . $searchInSql 
                                    . (isset($affiliate_id) && $affiliate_id != '' ? ' AND dg.affiliate_id = ' . $affiliate_id . ' ' : '')
                                    . (isset($trader_id) && $trader_id != '' ? ' AND dg.trader_id = ' . $trader_id . ' ' : '')
									. (isset($group_id) && $group_id != '' ? ' AND dg.group_id = ' . $group_id . ' ' : '')
									. " ORDER BY dg.rdate"
									;
                            // die ($sql);
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
											if(isset($commission_type) && $commission_type == "All" || $commission_type == 'CPL'){
												if(!empty($a)){
													$regww['id'] = $regww['id'];
													$regww['rdate'] = $regww['rdate'];
													$regww['location'] = lang("CPL");
													$regww['commission'] += $a;
													$regww['trader_id'] = $regww['trader_id'];
													$regww['amount'] = "-";
													$regww['type'] = $regww['type']==""?"-":$regww['type'];
													$regww['merchant_id'] = $ww['id'];
													$regww['merchant_name'] = $ww['name'];
													$regww['affiliate_id'] = $regww['id'];
													$regww['affiliate_name'] = $regww['username'];
													
													$commissionArray[] = $regww;
												}
											}
											
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
														'id'    => $regww['id'],
														'type'    => $regww['type'],
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
				   
				                
                                // TIER CPL.
								$tierCom =0;
                                foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
                                    $a = getCommission(
                                        $arrParams['from'], 
                                        $arrParams['to'], 
                                        $arrParams['onlyNetDeposit'], 
                                        $arrParams['groupId'], 
                                        $arrParams['arrDealTypeDefaults'], 
                                        $arrParams['arrTmp']
                                    );
									$totalCom += $a;
									$tierCom += $a;
									if(isset($commission_type) && $commission_type == "All" || $commission_type == 'TierCPL'){
										if(!empty($a)){
												$aff_data = getAffiliateRow($arrParams['arrTmp']['affiliate_id']);
												$arrParams['arrTmp']['id'] = $arrParams['arrTmp']['id'];
												$arrParams['arrTmp']['rdate'] = $arrParams['from'];
												$arrParams['arrTmp']['location'] = lang("Tier CPL");
												$arrParams['arrTmp']['commission'] += $a;
												$arrParams['arrTmp']['trader_id'] = $arrParams['arrTmp']['trader_id'];
												$arrParams['arrTmp']['amount'] = $arrParams['arrTmp']['amount'];
												$arrParams['arrTmp']['type'] = $arrParams['arrTmp']['type']==""?"-":$arrParams['arrTmp']['type'];
												$arrParams['arrTmp']['merchant_id'] = $ww['id'];
												$arrParams['arrTmp']['merchant_name'] = $ww['name'];
												$arrParams['arrTmp']['affiliate_id'] = $arrParams['arrTmp']['affiliate_id'];
												$arrParams['arrTmp']['affiliate_name'] = $aff_data['username'];
												
												$commissionArray[] = $arrParams['arrTmp'];
										}
									}
								
									unset($intAffId, $arrParams);
                                }
								
								  if($_GET['com'])
						   {
							   echo " Commission after Tier " . $tierCom . "<br/>";
						   } 
                                
                                
                                
								
								
                                $strSql = "SELECT *, data_sales.type AS data_sales_type, data_sales.rdate AS data_sales_rdate  FROM data_sales AS data_sales "
                                        . "INNER JOIN data_reg AS data_reg ON data_sales.merchant_id = data_reg.merchant_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                        . "WHERE data_sales.merchant_id>0 and data_sales.type<>'pnl' and data_sales.merchant_id = '" . $ww['id'] . "' AND data_sales.rdate "
                                        . $searchInSql . " "
                                        . (isset($affiliate_id) && $affiliate_id != '' ? ' AND data_sales.affiliate_id = ' . $affiliate_id . ' ' : '')
										. (isset($trader_id) && $trader_id != '' ? ' AND data_sales.trader_id = ' . $trader_id . ' ' : '')
										. (isset($group_id) && $group_id != '' ? ' AND data_sales.group_id = ' . $group_id . ' ' : '')
										. " ORDER BY data_sales.rdate";
										
										if(!empty($com)){
										    echo $strSql;
										}
										
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
										
										if($_GET['com']){
										        echo $a."\n";
										}
										
									} // nir fix..
										$totalCom += $a;
										$salesCom += $a;
										
										if(isset($commission_type) && $commission_type == "All"  || $commission_type == 'NetDeposit'){
											if(!empty($a)){
												$aff_data = getAffiliateRow($salesww['affiliate_id']);
												$salesww['id'] = $salesww['id'];
												$salesww['rdate'] = $salesww['rdate'];
												$salesww['location'] = lang("NetDeposit");
												$salesww['commission'] += $a;
												$salesww['trader_id'] = $salesww['trader_id'];
												$salesww['amount'] = $salesww['amount'];
												$salesww['type'] = $salesww['data_sales_type']==""?"-":$salesww['data_sales_type'];
												$salesww['merchant_id'] = $ww['id'];
												$salesww['merchant_name'] = $ww['name'];
												$salesww['affiliate_id'] = $salesww['affiliate_id'];
												$salesww['affiliate_name'] = $aff_data['username'];
												
												$commissionArray[] = $salesww;
												
											}
										}
                                    // } // nir fix..
                                }
				
								  if($_GET['com'])
								   {
									   echo " Commission after Sales " . $salesCom . "<br/>";
								   } 
                                
								// die((isset($group_id) && $group_id != '' ? $group_id : -1));
								
                                $arrFtds  = getTotalFtds(
                                    $arrRange['from'], 
                                    $arrRange['to'], 
                                    (!empty($affiliate_id) ? $affiliate_id : 0), 
                                    $ww['id'], 
                                    $ww['wallet_id'], 
                                    (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                    0, 
                                    0,
                                    $searchInSql,
									!empty($trader_id) ? $trader_id : 0
                                );
                                // var_dump($arrFtds);
								// die();
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
										$aff_data = getAffiliateRow($arrFtd['affiliate_id']);
                                        $real_ftd++;
                                        $real_ftd_amount += $arrFtd['amount'];
                                        
                                        $beforeNewFTD = $ftd;
                                        getFtdByDealType($ww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $ftd);
                                        
										// if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
											if ($beforeNewFTD != $ftd ) {
                                            $arrFtd['isFTD'] = true;
                                            
											
											/*
											 $b = getCommission(
                                                $arrFtd['rdate'], 
                                                $arrFtd['rdate'], 
                                                0,
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrFtd
                                            ); 
											// echo 'new com: ' . $b .'<br>';	
											$ftdCom +=$b;
											$totalCom+=$b;
											if(isset($commission_type) && $commission_type == "All" || $commission_type == 'CPA'){
												if(!empty($b)){
													$arrFtd['id'] = $arrFtd['id'];
													$arrFtd['rdate'] = $arrFtd['rdate'];
													$arrFtd['location'] = lang("CPA");
													$arrFtd['commission'] += $b;
													$arrFtd['trader_id'] = $arrFtd['trader_id'];
													$arrFtd['amount'] = $arrFtd['amount'];
													$arrFtd['type'] = "FTD";
													$arrFtd['merchant_id'] = $ww['id'];
													$arrFtd['merchant_name'] = $ww['name'];
													$arrFtd['affiliate_id'] = $arrFtd['affiliate_id'];
													$arrFtd['affiliate_name'] = $aff_data['username'];
													
													$commissionArray[] = $arrFtd;
												}
											}
											
											*/
                                        }
                                        unset($arrFtd);
                                    }
                                }
							
                                if ($_GET['com'])
								echo 'Commission after ftd: ' . $ftdCom .'<Br>';
								
								
								
								
									//******* qualification ftds
							   $ftdUsersQualified = '';
                         $FILTERbyTrader = !empty($trader_id)? $trader_id : 0;
						 
                         $selected_group_id = (isset($group_id) && $group_id != '' ? $group_id : -1);
						
						$qftdQuery  = "SELECT * FROM `data_reg` where type<>'demo' and FTDqualificationDate>'0000-00-00 00:00:00' and FTDqualificationDate>'". $arrRange['from'] ." 00:00:00' and FTDqualificationDate <'". $arrRange['to'] ."' " . ($affiliate_id?" and affiliate_id = " . $affiliate_id  : '') .  " and merchant_id = ". $ww['id']  
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
								
								$arrFtd['amount'] = $arrFtd['ftdamount'] ;
								$arrFtd['trades'] = 0;
								$arrFtd['traderHasFTD'] = $arrFtd['initialftddate']=='0000-00-00 00:00:00' ? false : true;
                                
									$activeTrader++;  
                                    $arrFtd['isFTD'] = true;

									$b = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, $selected_group_id, $arrDealTypeDefaults, $arrFtd);

											$ftdCom +=$b;
											$totalCom+=$b;
											if(isset($commission_type) && $commission_type == "All" || $commission_type == 'CPA'){
												if(!empty($b)){
													$arrFtd['id'] = $arrFtd['id'];
													$arrFtd['rdate'] = $arrFtd['rdate'];
													$arrFtd['location'] = lang("CPA");
													$arrFtd['commission'] += $b;
													$arrFtd['trader_id'] = $arrFtd['trader_id'];
													$arrFtd['amount'] = $arrFtd['amount'];
													$arrFtd['type'] = "FTD";
													$arrFtd['merchant_id'] = $ww['id'];
													$arrFtd['merchant_name'] = $ww['name'];
													$arrFtd['affiliate_id'] = $arrFtd['affiliate_id'];
													$arrFtd['affiliate_name'] = $aff_data['username'];
													
													
                        												
													
													$commissionArray[] = $arrFtd;
												}
											}
											
											
									
									$totalCom += $a;
									$qftdCom += $a;
                                
                            }
							
							if ($_GET['com']){
								echo 'Commission after qualification ftds: ' . $ftdCom .'<Br>';
							}
						}
							
							
								
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
													$arrAff['merchant_id'] = $ww['id'];
													$arrAff['merchant_name'] = $ww['name'];
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
								
									$intNetRevenue =  round(getRevenue($searchInSql,$ww['producttype'],$revDepAmount,$revBonAmount,$revWithAmount,0,0,0,$ww['rev_formula'],null,$revChBAmount),2);
									$netRevenue += $intNetRevenue;
											$comrow                 = array();
										   $comrow['merchant_id']  = $trans[0]['merchant_id'];
										   $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
											$comrow['banner_id']    = 0;
											$comrow['rdate']        = $trans[0]['rdate'];//arrRange2['from'];
											$comrow['amount']       = $intNetRevenue;
											 $comrow['trader_id']  =  $trans[0]['trader_id'];
											$comrow['isFTD']        = false;
											$comrow['initialftddate']        = $trans[0]['initialftddate'];

												
											$com = getCommission($trans[0]['rdate'], $trans[0]['rdate'], 1, (isset($group_id) && $group_id != '' ? $group_id : -1),  $arrDealTypeDefaults, $comrow);
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
													$trans[0]['merchant_id'] = $ww['id'];
													$trans[0]['merchant_name'] = $ww['name'];
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
                                
                                
									//,SUM(turnover) AS totalTO '
                  if (strtolower($ww['producttype']) == 'forex') {
                                    $sql = 'SELECT SUM(spread) AS totalSpread '
                                            . 'FROM data_stats '
                                            . 'WHERE merchant_id>0 and merchant_id = "' . $merchantID . '" AND rdate ' . $searchInSql
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '')
                                            . (isset($trader_id) && $trader_id != '' ? ' AND trader_id = ' . $trader_id . ' ' : '')
											. (isset($affiliate_id) && $affiliate_id != '' ? ' AND affiliate_id = ' . $affiliate_id . ' ' : '').
											' ORDER BY rdate ';
                                    
                                    $traderStatsQ = function_mysql_query($sql,__FILE__);
                                    
                                    while($ts = mysql_fetch_assoc($traderStatsQ)){
                                        $spreadAmount  = $ts['totalSpread'];
                                        //$volume       += $ts['totalTO'];
                                        // $pnl           = $ts['totalPnl'];
                                    }
									
									
									
								// lots
					
						$totalLots  = 0;
						
						
			/* 			$sql = 'SELECT dr.initialftddate, ds.turnover AS totalTurnOver,ds.trader_id,ds.merchant_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds  
									inner join data_reg dr inner join ds.merchant_id = dr.merchant_id and ds.trader_id = dr.merchant_id ' 
										 . 'WHERE ds.merchant_id = "' . $merchantID . '" AND ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) .
										 (isset($affiliate_id) && $affiliate_id != '' ? ' AND ds.affiliate_id = ' . $affiliate_id . ' ' : '').
											(isset($trader_id) && $trader_id != '' ? ' AND ds.trader_id = ' . $trader_id . ' ' : '').
											' ORDER BY ds.rdate';
										 ;
										 
										  */
										 							
								$sql = '
									select data_stats.* , data_reg.FTDqualificationDate , data_reg.initialftddate from 
									(SELECT turnover AS totalTurnOver,trader_id,id,merchant_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
										 . 'WHERE merchant_id = "' . $merchantID . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql)  . 
										 
                                            (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : ''). 
											(isset($affiliate_id) && $affiliate_id != '' ? ' AND affiliate_id = ' . $affiliate_id . ' ' : '').
											(isset($trader_id) && $trader_id != '' ? ' AND trader_id = ' . $trader_id . ' ' : '').
										   ' and turnover>0 
										 
										 
										 
										 ) data_stats
left join data_reg on data_stats.merchant_id = data_reg.merchant_id and  data_stats.trader_id = data_reg.trader_id
										 '
										 ;

										 
										 
// die ($sql);											
											
							/* 
						$sql = 'SELECT id,turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
                                         // . 'WHERE merchant_id = "' . $merchantID . '" AND rdate ' . $searchInSql 
										 . 'WHERE merchant_id>0 and merchant_id = "' .$merchantID . '" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' " : $searchInSql) 
                                            . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : ''). 
											(isset($affiliate_id) && $affiliate_id != '' ? ' AND affiliate_id = ' . $affiliate_id . ' ' : '').
											(isset($trader_id) && $trader_id != '' ? ' AND trader_id = ' . $trader_id . ' ' : '').
											' ORDER BY rdate';
											 */
											// die($sql);
                           
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
																	'initialftddate'    => $ts['initialftddate'],
																	'FTDqualificationDate'    => $ts['FTDqualificationDate'],
																	'rdate'        => $lotdate,
																	'banner_id'    => $ts['banner_id'],
																	'trader_id'    => $ts['trader_id'],
																	'profile_id'   => $ts['profile_id'],
																	'type'       => 'lots',
																	'amount'       =>  $totalLots,
														];
													$a = getCommission($lotdate, $lotdate, 0, $group_id, $arrDealTypeDefaults, $row);
													$lotsCom += $a;
													
													if(isset($commission_type) && $commission_type == "All" || $commission_type == 'Lots'){
														if(!empty($a)){
															$aff_data = getAffiliateRow($ts['affiliate_id']);
															$ts['id'] = $ts['id'];
															$ts['rdate'] =$lotdate;
															$ts['location'] = lang("Lots");
															$ts['commission'] += $a;
															$ts['trader_id'] = $ts['trader_id'];
															$ts['amount'] = $totalLots;
															$ts['type'] = $ts['type']==""?"-":$ts['type'];
															$ts['merchant_id'] = $ww['id'];
															$ts['merchant_name'] = $ww['name'];
															$ts['affiliate_id'] = $ts['affiliate_id'];
															$ts['affiliate_name'] = $aff_data['username'];
															
															$commissionArray[] = $ts;
														}
													}
													
													
													// echo 'com: ' . $a .'<br>';
													$totalCom += $a;
											// }
										}
										
										  if($_GET['com'])
									   {
										   echo " Commission after Lots " . $lotsCom . "<br/>";
									   } 
				  }
				  
				  if ($set->deal_pnl == 1) {
						$dealsForAffiliate['pnl']=1; 
								$totalPNL  = 0;


								
								// {
								/* if (!in_array($merchantID . '-' .  $arrRes['trader_id'],$tradersProccessedForPNL)) {
									$tradersProccessedForPNL[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id']; */
									// {	
									
									// die ($where);
									
									
										$pnlRecordArray=array();
									
										$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
										$pnlRecordArray['merchant_id']  = $merchantID;
										$pnlRecordArray['trader_id']  = (isset($trader_id) ? $trader_id: "");
										$pnlRecordArray['banner_id']  = $banner_id;
										$pnlRecordArray['profile_id']  = $profile_id;
										$pnlRecordArray['group_id']  = $group_id;
										$pnlRecordArray['searchInSql']  = $searchInSql;
										$pnlRecordArray['fromdate']  = $arrRange['from'];
										$pnlRecordArray['todate']  = $arrRange['to'];
										
										
									
									
									if ($dealsForAffiliate['pnl']>0 || isset($dealsForAffiliate['pnl'])){
										
										
										$sql = generatePNLquery($pnlRecordArray,false);
										
										
									}
									else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
										// die ('2');
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
												 
												
											
												$totalPNL = $totalPNL + $pnlamount;
												
															 
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
										if ($dealsForAffiliate['pnl']>0){
											// echo 'com: ' . $tmpCom.'<br>'; 
											$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
										 	/* if ($tmpCom>0){
												var_dump($row);
												echo '<br>';
												echo '<br>';
											echo 'com: ' . $tmpCom.'<br>'; 
												// die();
											} */
											
											
												$pnlCom +=$tmpCom;
												$totalCom += $tmpCom;
												
												if(isset($commission_type) && $commission_type == "All" || $commission_type == 'PNL RevShare'){
													if(!empty($tmpCom)){
														$aff_data = getAffiliateRow($ts['affiliate_id']);
														$ts['id'] = $ts['id'];
														$ts['rdate'] =$ts['rdate'];
														$ts['location'] = lang("PNL RevShare");
														$ts['commission'] += $tmpCom;
														$ts['trader_id'] = $ts['trader_id'];
														$ts['amount'] = $pnlamount;
														$ts['type'] = lang("PNL");
														$ts['merchant_id'] = $ww['id'];
														$ts['merchant_name'] = $ww['name'];
														$ts['affiliate_id'] = $ts['affiliate_id'];
														$ts['affiliate_name'] = $aff_data['username'];
														
														$commissionArray[] = $ts;
													}
												}
												
										}
								}
						}
								if ($_GET['com'])
								echo 'Commission after pnl: ' . $pnlCom .'<Br>';
						
						
						//Sub Affiliate Commission
						if(isset($commission_type) && $commission_type == "All" || $commission_type == 'SubAffiliateCom'){
					//	$qry = "select id from affiliates where id in ( select distinct (refer_id) as id from affiliates) and sub_com>0 and valid = 1". ($userlevel=='manager' ? " and group_id = " . $group_id : "");
						
						
						$groupPart = ($group_id<>'' && ($userlevel=='manager' || $group_id>-1)) ? " and group_id = " .$group_id ." ": " " ;
						$qry = "select id,refer_id from affiliates where id in ( select distinct (id) as id from affiliates where 1=1 " . (!empty($affiliate_id) ? "
						and refer_id = " . $affiliate_id : "") . ") and valid = 1 and sub_com>0" . $groupPart;
						
						// die ($qry);
						
						
						$rsc = function_mysql_query($qry,__FILE__);
							
						$allAffiliates = "";
						
						while ($row = mysql_fetch_assoc($rsc)) {
								
								
								// $sql = "SELECT id,username FROM affiliates WHERE valid = 1 AND refer_id = " . $row['id']  .  ($userlevel=='manager'  || $group_id>-1 ? " AND  group_id = " . $group_id : "");
								
								// $affiliateqq = function_mysql_query($sql,__FILE__);
							 
								 $hasResults = false;
								 if ($row['id']>0)  {
											
											$affiliateww = getAffiliateRow($row['id']);
										// while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {
											{
											$comData = getSubAffiliateData($arrRange['from'],$arrRange['to'],$affiliateww['id'],$affiliateww['refer_id'],'commission',$userlevel,($userlevel=='manager' ?  $group_id : -1));
											if ($comData['commission']<>0){
											$ts['id'] = $affiliateww['id'];
											$ts['rdate'] =$arrRange['from'];
											$ts['location'] = lang("Sub Affiliate Commission");
											$ts['commission'] = $comData['commission'];
											$ts['trader_id'] = '';
											$ts['amount'] = 0;
											$ts['type'] = lang("Sub Affiliate Commission");
											$ts['merchant_id'] = $ww['id'];
											$ts['merchant_name'] = $ww['name'];
											$ts['affiliate_id'] = $affiliateww['id'];
											$ts['affiliate_name'] = $affiliateww['username'];
											
											$commissionArray[] = $ts;
											}
										}
								 }
								
						}
						}
						
						
						
						
			} // End of time-periods loop.
                        
                    // Mark given wallet as processed.
                    $arrWallets[$ww['wallet_id']] = true;
		}
                
		
		//echo "<pre>";print_r($commissionArray);die;

		
		
		
		
		$totalCom = 0;
		$totalAmt = 0;
		
		
		function cmp($a, $b)
		{
			return strcmp($a["rdate"], $b["rdate"]);
		}

		usort($commissionArray, "cmp");
		
		$l=0;
	// $commissionTypes = array();
if ($_GET['aaaa']==1){
	var_dump($commissionArray);
		die();
}
		
		foreach($commissionArray as $key=>$com){
			
			/* if (!isset($commissionTypes[strtolower($com['location'])])) {
				$commissionTypes[strtolower($com['location'])]=1;
			} */
			
			$listReport .= '
				<tr>
				<td style="text-align: left;">'.$com['merchant_name'].'</td>
				<td style="text-align: left;">'.$com['merchant_id'].'</td>
				<td style="text-align: left;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$com['affiliate_id'].'" target="_blank">'.$com['affiliate_id'].'</a></td>
				<td style="text-align: left;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$com['affiliate_id'].'" target="_blank">'.$com['affiliate_name'].'</a></td>
				<td style="text-align: left;">'. $com['trader_id'] .'</a></td>
				<td style="text-align: left;">'.$com['id'].'</td>
				<td style="text-align: left;">'.date("Y-m-d h:i:s", strtotime($com['rdate'])) .'</a></td>
				<td style="text-align: left;">'. lang(ucwords($com['type'])) .'</td>
				<td style="text-align: left;">'. price($com['amount']) .'</td>
				<td style="text-align: left;">'. $com['location'] .'</td>
				<td style="text-align: left;">'. price($com['commission']) .'</td>
			</tr>';
			
			$totalCom  += $com['commission'];
			$totalAmt += $com['amount'];
			$l++;
		}
		
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
		}
                
		$set->sortTable = 1;
		$set->totalRows = $l;
		
		
		$set->content .= '
		<div class="normalTableTitle" style="width: 99.5%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form id="frmRepo" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="commission" />
				<table><tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						'.($userlevel == "admin" || $userlevel == "manager"? '<td width=160>'.lang('Affiliate ID').'</td>':'').'
						'.($userlevel == "admin"? '<td style="padding-left:10px">'.lang('Group ID').'</td>':'').'
						<td style="padding-left:10px">'. lang('Trader ID') .'</td>
						<td>'. lang('Commission') .'</td>
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
                                        '.($userlevel == 'admin'?'<td width="100" style="padding-left:10px">
                                            <select name="group_id" style="width: 100px;">
                                                <option value="">'.lang('All Groups').'</option>'
                                                . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                    . lang('General') 
                                                . '</option>' 
                                                . listGroups($group_id) 
                                            . '</select>
                                        </td>':'').'
										<td style="padding-left:10px"><input type="text" name="trader_id" value="'.$trader_id.'" id="trader_id" style="width: 60px; text-align: center;" onblur="validateMerchant(this)"/></td>
										<td><select name="commission_type" style="width: 150px;">
					<option '.($commission_type=='' ? ' selected ' : '').' value="All">'.lang('All').'</option>' .
					 (true ? '<option '.($commission_type=='CPL' ? ' selected ' : '').' value="CPL">'.lang('CPL').'</option>' : '') .
					 (true ? '<option '.($commission_type=='CPA' ? ' selected ' : '').' value="CPA">'.lang('CPA / TierCPA / DCPA').'</option>' : '') .
					 (false ? '<option '.($commission_type=='TierCPL' ? ' selected ' : '').' value="TierCPL">'.lang('Tier CPL').'</option>' : '') .
					 (true ? '<option '.($commission_type=='NetDeposit' ? ' selected ' : '').' value="NetDeposit">'.lang('NetDeposit').'</option>' : '') .
					 ( $displayForex==1 ? '<option '.($commission_type=='Lots' ? ' selected ' : '').' value="Lots">'.lang('Lots').'</option>' : '') .
					 ($set->deal_pnl==1 ? '<option '.($commission_type=='PNLRevShare' ? ' selected ' : '').' value="PNLRevShare">'.lang('PNL RevShare').'</option>' : '') .
					 (true ? '<option '.($commission_type=='SubAffiliateCom' ? ' selected ' : '').' value="SubAffiliateCom">'.lang('Sub Affiliate Commission').'</option>' : '') .
'	
					</select></td>
					<td><input type="button" value="'.lang('View').'" onclick="validateForm()"/></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#commissionData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#commissionData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		<div class="normalTableTitle" class="table">'.lang('Commission Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
		//style="width: 99.5%;"
			$tableStr = '
			<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="commissionTbl">
				<thead><tr  class="table-row">
				<th  class="table-cell">'. lang('Merchant Name') .'</th>
				<th class="table-cell">'. lang('Merchant ID') .'</th>
				<th class="table-cell">'. lang('Affiliate ID') .'</th>
				<th class="table-cell">'. lang('Affiliate Name') .'</th>
				<th class="table-cell">'. lang('Trader ID') .'</th>
				<th class="table-cell">'. lang('Transaction ID') .'</th>
				<th class="table-cell">'. lang('Date') .'</th>
				<th class="table-cell">'. lang('Type') .'</th>
				<th class="table-cell">'. lang('Amount') .'</th>
				<th class="table-cell">'. lang('Location') .'</th>
				<th class="table-cell">'. lang('Commission') .'</th>
				</tr></thead><tfoot><tr>
				<th>'. lang('Total') .'</th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>'. price($totalAmt) .'</th>
				<th></th>
				<th>'. price($totalCom) .'</th>
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
					txt = "<table id=\'commissionData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#commissionTbl")[0].config.rowsCopy).each(function() {
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
											
											saveReportToMyFav(name, \'commission\',user,level,type);
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
		$tableStr .= getSingleSelectedMerchant();
		$tableStr .= getValidateTraderMerchantScript();
		//excelExporter($tableStr, 'Quick');
		$set->content .= $tableStr . '</div>' . getPager();
		
		//MODAL
		$myReport = lang("Commission");
		include "common/ReportFieldsModal.php";


		theme();
		
?>