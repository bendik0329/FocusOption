<?php

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
$ver = "_new";
$ver = "";

if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}
 $set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>

<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>
<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>';

$pageTitle   = lang('Affiliate Report');


$filename = "affiliates_data_" . date('YmdHis');

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
			
							$set->content .='<style>
			  /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 60px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 20px;
			 width: 43px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 12px;
			  width: 12px;
			  left: 3px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
			
		</style>
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
		

 
if($userlevel == 'manager')
	$group_id = $set->userInfo['group_id'];

$showLeadsAndDemo = false;
$where            = '';
// $sql              = 'SELECT extraMemberParamName AS title FROM merchants';// WHERE id = ' . aesDec($_COOKIE['mid']);
// $campID           = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));

$listGroups = affiliateGroupsArray();
$isMasterAffiliatesArrayLoaded = false;

// $sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
$masterAffiliatesArray = array();
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

	

// $sql = "SELECT COUNT(id) AS count FROM merchants WHERE valid = 1;";
// $arrMerchantCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));

$intMerchantCount =count($merchantsArray);


if (isset($group_id) && $group_id != '') {
	$where .= ' AND group_id = ' . $group_id . ' ';
}

if (isset($groupByAff)) {
	
	if ( $groupByAff == 1) {
	$groupMerchantsPerAffiliate = 1;
} else {
	$groupMerchantsPerAffiliate = 0;
}
	
} else {
	$groupMerchantsPerAffiliate = 1;
}


if (isset($affiliate_id) && !empty($affiliate_id) && is_numeric($affiliate_id)) {
	$where .= ' AND affiliates.id = ' . $affiliate_id . ' ';
} elseif (isset($affiliate_id) && !empty($affiliate_id)) {
	$where .= " AND (lower(username) LIKE '%" . trim(strtolower($affiliate_id)) . "%' ";
		if (!empty($campID['title'])) {
			$where .= " or affiliates.id in (SELECT affiliateID FROM  `affiliates_campaigns_relations` WHERE  `campID` LIKE  '%".strtolower($affiliate_id)."%' )";
		}
		
	$where .= " ) ";
}



// Initialize total counters for all affiliates.
$totalImpressionsM = 0;
//echo "0. Static Text : " .  $totalClicksM . "<br/>";
$totalClicksM = 0;
$totalCPIM = 0;
$totalLeadsAccountsM = 0;
$totalDemoAccountsM = 0;
$totalRealAccountsM = 0;
$totalFTDM = 0;
$totalQFTDM = 0;
$totalDepositsM = 0;
$totalFTDAmountM = 0;
$totalmicroPaymentsAmountM = 0 ;
$totalmicroPaymentsCountM = 0 ;
$totalDepositAmountM = 0;
$totalVolumeM = 0;
$totalBonusM = 0;
$totalWithdrawalM = 0;
$totalChargeBackM = 0;
$totalNetRevenueM = 0;
$totalComsM = 0;
$netRevenueM = 0;
$totalFruadM = 0;
$totalFrozensM = 0;
$totalRealFtdM = 0;
$totalRealFtdAmountM = 0;




$totalCPIMA= 0;


$depositsAmount = 0;

$l = 0;
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
	
	/* echo '<pre>';
	var_dump($arrRanges);	
	echo '</pre>';
	die; */
	
	
	
	




//Affiliate Static Data

/*** TODO :: 
1. fetching data from last months
2. Data from decemer 2005
3. one week before two months ago adn 1 week after 2 months ago

Current Date : 04 Jun 2017
2 months ago : 04 Apr 2014
time frame : 25th Mar 2017 to 10th Apr 2017

check if timeframe is after 2 months ago

*/////

	
$sql = 'SELECT COUNT(id) AS count FROM merchants WHERE valid = 1 ' 
			  . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');
		
$arrMerchantsCount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
$intMerchantsCount = (int) $arrMerchantsCount['count'];

// List of wallets.
$arrWallets = array();
$sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1 " 
	  . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');

$resourceWallets = function_mysql_query($sql,__FILE__);

while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
	$arrWallets[$arrWallet['wallet_id']] = false;
	unset($arrWallet);
}


$newArrRanges = array();
$paramFromSettings =getStaticReportMonths();

$DateBeforeMonthsAgo =	getXmonthsAgoDate($paramFromSettings);
$DateBeforeMonthsAgo =	decreaseDayFromDate($DateBeforeMonthsAgo);

/* 
foreach ($arrRanges as $arrRange) {

	
	// die ('dpt: ' . $display_type);

	if ((isDateConsiderStatic($arrRange['from']) && isDateConsiderStatic($arrRange['to']))|| ($display_type<>'' && $display_type<>0 )  ){
		$newArrRanges[] = $arrRange;
	}	
	else {
	

	$newRange =  array();
	
	$newRange['from'] = $arrRange['from'];
	
	
	$newRange['to'] = $DateBeforeMonthsAgo . ' 23:59:59';
	$newArrRanges[] = $newRange;
	
	
	$DateBeforeMonthsAgoPlusDay = addDayToDate($DateBeforeMonthsAgo);
	// echo '<br>'.$DateBeforeMonthsAgo.'<br>';
	// die ($DateBeforeMonthsAgoPlusDay);
	$newRange['from'] = $DateBeforeMonthsAgoPlusDay. ' 00:00:00';
	$newRange['to'] = $arrRange['to'];
	$newArrRanges[] = $newRange;
	
	unset($newRange);
	unset($arrRange);
	
	
		
		
	}
} */



foreach ($arrRanges as $arrRange) {

	// if (($display_type=='' || $display_type==0 ) && (!isDateConsiderStatic($arrRange['from']) && !isDateConsiderStatic($arrRange['to']))){
	if ((isDateConsiderStatic($arrRange['from']) && isDateConsiderStatic($arrRange['to'])) 
	||
		(!isDateConsiderStatic($arrRange['from']) && !isDateConsiderStatic($arrRange['to']))
	)
	{
		
		$newArrRanges[] = $arrRange;
	}	
	else {
		
		
	$newRange =  array();
	$newRange['from'] = $arrRange['from'];
	$newRange['to'] = $DateBeforeMonthsAgo . ' 23:59:59';
	$newArrRanges[] = $newRange;
	$DateBeforeMonthsAgoPlusDay = addDayToDate($DateBeforeMonthsAgo);
	$newRange['from'] = $DateBeforeMonthsAgoPlusDay. ' 00:00:00';
	$newRange['to'] = $arrRange['to']; 
	
	$newArrRanges[] = $newRange;
	unset($newRange);
	unset($arrRange);
	}
}

$arrRanges = $newArrRanges;


// var_dump($arrRanges);
// die('zzzzzzzzzzzzzzzzzzz');

$arrayOfMergedAffiliatesResults = array();

foreach ($arrRanges as $arrRange) {


if ($_GET['debug']==1){	
	echo '<br><br>';	
var_dump($arrRange);
echo '<br><br>';
}


$affiliateWithInstallationsArray = array();
$affiliatesWithQualificationArray = array();
$affiliatesWithRegArray = array();
$affiliatesWithPNLArray = array();
$allAffiliatesTraffic = array();

				
				
				$sql = "SELECT affiliates.*, acr.campID FROM affiliates LEFT JOIN"
                        . "(select distinct(affiliateID),campID from affiliates_campaigns_relations where 1 =1 " 
						. (isset($merchant_id) && !empty($merchant_id) ? ' AND merchantid = ' . $merchant_id : ''). " group by affiliateID) acr ON affiliates.id = acr.affiliateID"
                        . " WHERE valid = 1 " . $where 
                        . "ORDER BY affiliates.id DESC";
				// die ($sql);
				
				//echo "<!-- ".$sql." -->";
				
				    $qq = function_mysql_query($sql,__FILE__);
                    
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
                        $totalmicroPaymentsAmount = 0;
                        $totalmicroPaymentsCount = 0;
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
								  // Initialize total counters per affiliate-merchant.
                            $formula = $merchantww['rev_formula'];
                            $activeTrader = 0;
                            $totalTraffic=[];
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
                            $microPaymentsAmount=0;
                            $microPaymentsCount=0;
                            $sumDeposits=0;
                            $netRevenue=0;
                            $lots=0;
                            $totalCom=0;
                            $real_ftd = 0;
                            $real_ftd_amount = 0;
							$microPaymentsCount=0;
							$microPaymentsAmount=0;

							$totalTrafficA=[];
                            $totalLeadsA=0;
							$totalDemoA=0;
                            $totalRealA=0;
                            $totalCPIA=0;
                            $ftdA=0;
                            $cpiA=0;
                            $pnlA = 0;
                            $volumeA=0;
                            $bonusA=0;
                            $spreadAmountA = 0;
                            $turnoverAmountA = 0;
                            $withdrawalA=0;
                            $chargebackA=0;
                            $revenueA=0;
                            $ftd_amountA=0;
                            $depositingAccountsA=0;
							$microPaymentsAmountA=0;
							$microPaymentsCountA=0;
                            $sumDepositsA=0;
                            $netRevenueA=0;
                            $lotsA=0;
                            $totalComA=0;
                            $real_ftdA = 0;
                            $real_ftd_amountA = 0;
							$microPaymentsAmountA=0;
							$microPaymentsCountA=0;
							
							
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
                            
							
							$config['display_type'] = $display_type;
							$config['isRedTag'] = $isRedTag;
							$config['userlevel'] = $userlevel;
							$config['hideDemoAndLeads'] = $hideDemoAndLeads;
							$config['showCasinoFields'] = $showCasinoFields;
							$config['displayForex'] = $displayForex;
							$config['groupMerchantsPerAffiliate'] = $groupMerchantsPerAffiliate;
							
							$data['from'] = $arrRange['from'];
							$data['to'] = $arrRange['to'];
							$data['merchant_id'] = $merchant_id;
							$data['affiliate_id'] = $ww['id'];
							$data['affiliate_username'] = $ww['username'];
							$data['affiliate_firstname'] = $ww['first_name'];
							$data['affiliate_lastname'] = $ww['last_name'];
							$data['affiliate_campId'] = $ww['campID'];
							$data['website'] = $ww['website'];
							$data['mail'] = $ww['mail'];
							$data['IMUser'] = $ww['IMUser'];
							$data['group_id'] = $ww['group_id'];
							$data['campId_title'] = $campID['title'];
							
							if ($_GET['debug']==1){
								echo '<Br>';
								var_dump($arrRange);
								echo '<Br>';
								echo '<Br>';
								
							}
							
							if (isDateConsiderStatic($arrRange['from']) && isDateConsiderStatic($arrRange['to']) && $ww['id'] != 2227){

							if ($_GET['com']==1){
								echo '/************************/';
								
							}

								if ($_GET['debug']==1)
									echo $arrRange['from'].'  in <br>';
								include_once('common/AffiliatesStaticData.php');
								$listData = getAllAffiliateStaticData($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id']);
							
							
							if ($_GET['com']==1){
								echo "\n\n";
								var_dump($listData);
								echo "\n\n";

							}
							
								if(!empty($listData))
								{
									// echo $arrRange['from'].'<br>';
									foreach($listData as $key=>$row){
										if ($groupMerchantsPerAffiliate == 0) {
											$totalTrafficA['totalViews'] += $row['views'];
											$totalTrafficA['totalClicks'] += $row['clicks'];
											$totalCPIA+= $row['cpi'];
											$totalLeadsA += $row['leads'];
											$totalDemoA += $row['demo'];
											$totalRealA += $row['real'];
											$ftdA += $row['ftd'];
											$activeTraderA = $row['qftd'];
											$depositingAccountsA += $row['deposits'];
											$microPaymentsAmountA +=$microPaymentsAmount;
											$microPaymentsCountA +=$microPaymentsCount;
											$ftd_amountA += $row['ftd_amount'];
											$sumDepositsA += $row['deposits_amount'];
											$volumeA += $row['volume'];
											$bonusA += $row['bonus'];
											$withdrawalA += $row['withdrawal'];
											$chargeBackA += $row['chargeback'];
											$netRevenueA += $row['netRevenue'];
											$totalComA += $row['commission'];
											$spreadAmountA += $row['spread'];
											$turnoverAmountA += $row['turnover'];
											$pnlA += $row['pnl'];
											$frozensA += $row['frozens'];
											$real_ftdA += $row['real_ftd'];
											$real_ftd_amountA += $row['real_ftd_amount'];
											
											$fruadA += $row['fruad'];
											
										} else {
											$totalImpressions += $row['views'];
											$totalClicks += $row['clicks'];
											$totalCPIGroup += $row['cpi'];
											$totalLeadsAccounts += $row['leads'];
											$totalDemoAccounts += $row['demo'];
											$totalRealAccounts += $row['real'];
											$totalFTD += $row['ftd'];
											$totalQFTD += $row['qftd'];
											$totalDeposits += $row['deposits'];
											$totalFTDAmount += $row['ftd_amount'];
											$totalDepositAmount += $row['deposits_amount'];
											$totalmicroPaymentsAmount +=$microPaymentsAmount;
											$totalmicroPaymentsCount +=$microPaymentsCount;
											$totalVolume += $row['volume'];
											$totalBonus += $row['bonus'];
											$totalWithdrawal += $row['withdrawal'];
											$totalChargeBack += $row['chargeback'];
											$totalNetRevenue += $row['netRevenue'];
											$totalComs += $row['commission'];
											$totalSpreadAmount += $row['spread'];
											$totalTurnoverAmount += $row['turnover'];
											$totalpnl += $row['pnl'];
											$totalFrozens += $row['frozens'];
											$totalRealFtd += $row['real_ftd'];
											$totalRealFtdAmount += $row['real_ftd_amount'];
										}
									
										$totalImpressionsM += $row['views'];
										$totalClicksM += $row['clicks'];
										$totalCPIM += $row['cpi'];
										$totalLeadsAccountsM += $row['leads'];
										$totalDemoAccountsM += $row['demo'];
										$totalRealAccountsM += $row['real'];
										$totalFTDM += $row['ftd'];
										$totalQFTDM += $row['qftd'];
										$totalDepositsM += $row['deposits'];
										$totalFTDAmountM += $row['ftd_amount'];
										$totalmicroPaymentsAmountM += $row['microPaymentsAmount'];
										$totalmicroPaymentsCountM += $row['microPaymentsCount'];
										$totalDepositAmountM += $row['deposits_amount'];
										$totalVolumeM += $row['volume'];
										$totalBonusM += $row['bonus'];
										$totalWithdrawalM += $row['withdrawal'];
										$totalChargeBackM += $row['chargeback'];
										$totalNetRevenueM += $row['netRevenue'];
										$totalComsM += $row['commission'];
										$totalSpreadAmountM += $row['spread'];
										$totalTurnoverAmountM += $row['turnover'];
										$totalFrozensM += $row['frozens'];
										$totalpnlM += $row['pnl'];
										$totalRealFtdM += $row['real_ftd'];
										$totalRealFtdAmountM += $row['real_ftd_amount'];
										
										}
										
										if($groupMerchantsPerAffiliate == 0){
											
											$data['merchant_name'] = $merchantww['name'];
											$data['totalImpressions'] = $totalTrafficA['totalViews'];
											$data['totalClicks'] = $totalTrafficA['totalClicks'];
											$data['totalCPIGroup'] = $totalCPIA;
											$data['totalRealAccounts'] = $totalRealA;
											$data['totalFTD'] = $ftdA;
											$data['totalComs'] = $totalComA;
											$data['totalLeadsAccounts'] = $totalLeadsA;
											$data['totalDemoAccounts'] = $totalDemoA;
											$data['totalFrozens'] = $frozensA;
											$data['totalFTDAmount'] = $ftd_amountA;
											$data['totalRealFtd'] = $real_ftdA;
											$data['totalRealFtdAmount'] = $real_ftd_amountA;
											$data['totalDeposits'] = $depositingAccountsA;
											$data['totalmicroPaymentsCount'] = $totalmicroPaymentsCountA;
											$data['totalmicroPaymentsAmount'] = $totalmicroPaymentsAmountA;
											$data['totalDepositAmount'] = $sumDepositsA;
											$data['totalBonus'] = $bonusA;
											$data['totalWithdrawal'] = $withdrawalA;
											$data['totalChargeBack'] = $chargeBackA;
											$data['totalFruad'] = $fruadA;
											$data['totalVolume'] = $volumeA;
											$data['totalSpreadAmount'] = $spreadAmountA;
											$data['totalTurnoverAmount'] = $turnoverAmountA;
											$data['totalpnl'] = $pnlA;
											$data['totalNetRevenue'] = $netRevenueA;
											$data['totalQFTD'] = $activeTraderA;
											
											
											$listReport .= generateRow($data,$config);
										}
										
										
										
										
									}
								//	}//if condition to check the date range
								} else {
						
						
						if ($_GET['debug']==1)
									echo $arrRange['from'].'  out <br>';
                          
                            
                            //Clicks and Impressions
                            if (!empty($affiliate_id)){
							
                            $arrClicksAndImpressions     = getClicksAndImpressions($arrRange['from'], $arrRange['to'], $merchantww['id'], $ww['id'],(!empty($group_id)? $group_id : -1));
							
							}
							else { //load all affiliates data
	
								if (!isset($allAffiliatesTraffic[$merchantww['id']]))
								$allAffiliatesTraffic[$merchantww['id']]     = getClicksAndImpressions($arrRange['from'], $arrRange['to'], $merchantww['id'], (!empty($group_id)? $group_id : -1));
								
								$arrClicksAndImpressions = $allAffiliatesTraffic[$merchantww['id']][$ww['id']];
								
								
								
//								SELECT sb.affiliate_id, IFNULL(SUM(sb.views), 0) AS impressions, IFNULL(SUM(sb.clicks), 0) AS clicks FROM traffic AS sb INNER JOIN affiliates AS aff ON aff.id = sb.affiliate_id AND aff.valid = 1 INNER JOIN merchants AS merchants ON merchants.id = sb.merchant_id AND merchants.valid = 1 WHERE sb.type="traffic"  AND sb.merchant_id = 1 AND sb.rdate BETWEEN '2017-01-01' AND '2017-04-03 23:59:59' group by sb.affiliate_id
								
							}
							
							
							$totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
							$totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
							
                            $merchantName = strtolower($merchantww['name']);
                           /*  $sql = 'SELECT SUM(IF(status="frozen",1,0)) AS total FROM data_reg '
                                    . 'WHERE affiliate_id="'.$ww['id'].'" AND merchant_id="'.$merchantww['id'] 
                                    . '" AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . '"';
                            
							
							
                            $frozensQ = function_mysql_query($sql,__FILE__);
                            $frozens  = mysql_fetch_assoc($frozensQ);
                            $frozens  = $frozens['total'] ? $frozens['total'] : 0; */
                            
							if ($_GET['com']==2) 	echo 'com1: ' . $totalCom.'<br>';
							



							
				if (empty( $affiliatesWithRegArray[$merchantww['id']] ) && empty($affiliate_id)){
									
							 	$sqlArray = "SELECT distinct(affiliate_id) as affiliate_id  FROM data_reg "
                                    . "WHERE merchant_id = '" . $merchantww['id'] . "'  "
                                    . "AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' 
								";
								
							 
							 $affiliatesWithRegQ = function_mysql_query($sqlArray,__FILE__);
							 while ($affiliatesWithRegRow = mysql_fetch_assoc($affiliatesWithRegQ)){
								 // var_dump($affiliatesWithRegRow);
								 $affiliatesWithRegArray[$merchantww['id']][$affiliatesWithRegRow['affiliate_id']] = $affiliatesWithRegRow['affiliate_id'];
							 }
							 if (empty($affiliatesWithRegArray[$merchantww['id']]))
								 $affiliatesWithRegArray[$merchantww['id']][-1]=-1;
						 }

						 
							
                            $regCom = 0;
						if (!empty($affiliate_id) || isset($affiliatesWithRegArray[$merchantww['id']][$ww['id']])){
								
								
							
                            $sql = "select * from ( SELECT merchant_id,initialftddate,status,trader_id,rdate,type,profile_id,affiliate_id,banner_id,country,FTDqualificationDate FROM data_reg "
                                    . "WHERE merchant_id = '" . $merchantww['id'] . "' AND affiliate_id = " . $ww['id'] . " "
                                    . "AND rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' 
									
								) a group by merchant_id , trader_id";

						
									
                            $regqq = function_mysql_query($sql,__FILE__);
                            
                            $arrTierCplCountCommissionParams = [];
                            $boolTierCplCount = false;
                            while ($regww = mysql_fetch_assoc($regqq)) {


							
							// $strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
                            // $boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
									
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
						 
						 if (empty( $affiliatesWithQualificationArray[$merchantww['id']] ) && empty($affiliate_id)){
							 $qftdQuery  = "SELECT distinct(affiliate_id) as affiliate_id FROM `data_reg` where type<>'demo' and FTDqualificationDate>'0000-00-00 00:00:00' and FTDqualificationDate>'". $arrRange['from'] ." 00:00:00' and FTDqualificationDate <'". $arrRange['to'] ."' "
							 .(!empty($selected_group_id) && $selected_group_id>0 ? ' and group_id= '. $selected_group_id : '')  
							 .(!empty($FILTERbyTrader) ? ' and trader_id= '. $FILTERbyTrader : '') ;
							 $affiliatesWithQualificationQ = function_mysql_query($qftdQuery,__FILE__);
							 while ($affiliatesWithQualificationRow = mysql_fetch_assoc($affiliatesWithQualificationQ)){
								 // var_dump($affiliatesWithQualificationRow);
								 $affiliatesWithQualificationArray[$merchantww['id']][$affiliatesWithQualificationRow['affiliate_id']] = $affiliatesWithQualificationRow['affiliate_id'];
							 }
							 if (empty($affiliatesWithQualificationArray[$merchantww['id']]))
								 $affiliatesWithQualificationArray[$merchantww['id']][-1]=-1;
						 }
						 
						 if (!empty($affiliate_id) || isset($affiliatesWithQualificationArray[$merchantww['id']][$ww['id']])){
						 
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
								$arrFtd['trades'] = 0;
								$arrFtd['traderHasFTD'] = $arrFtd['initialftddate']=='0000-00-00 00:00:00' ? false : true;
								
							  // die ('gergee');
									$activeTrader++;  
									
                                    $arrFtd['isFTD'] = true;
								
									$b = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $arrFtd);
									
								 if ($_GET['com']==2){
									 echo 'qualification<br><Br>';
								 var_dump($b);
								 echo '<br>';
								 echo '<br>';
								 // die();
								 }
								
								
											$qftdCom +=$b;
											$totalCom+=$b;
															
															
									if ($_GET['com']==2) 	echo 'com3: ' . $totalCom.'<br>';
                                
                            }
							
							}
						}
							   
							   
								
                            /* 
									$sql = "SELECT *, tb1.type AS data_sales_type  FROM data_sales as tb1 "
											. "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
											. "WHERE tb1.merchant_id = " . $merchantww['id'] . " AND tb1.affiliate_id=".$ww['id']." "
											. "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' ";  */
									
									
									//$sql = "SELECT *, tb1.type AS data_sales_type, tb1.rdate as data_sales_rdate  FROM data_sales as tb1 "
									$sql = "select * from (SELECT tb1.status ,tb1.amount ,tb1.tranz_id , tb1.id, data_reg.group_id,data_reg.trader_id,data_reg.country,data_reg.affiliate_id,data_reg.merchant_id,data_reg.banner_id,data_reg.profile_id, data_reg.initialftddate, tb1.type AS data_sales_type, tb1.rdate AS data_sales_rdate  FROM data_sales AS tb1 "
											. "INNER JOIN (select merchant_id,trader_id,affiliate_id,group_id,profile_id,country,banner_id,type,initialftddate from data_reg where   merchant_id = " . $merchantww['id'] . " AND affiliate_id=".$ww['id']."  and  initialftddate >'0000-00-00 00:00:00' and data_reg.type <> 'demo'  ) AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id "
											. "WHERE tb1.merchant_id >0 and  tb1.merchant_id = " . $merchantww['id'] . " AND tb1.affiliate_id=".$ww['id']." "
											. "AND tb1.rdate BETWEEN '" . $arrRange['from'] . "' AND '" . $arrRange['to'] . "' 
											) a 
											group by merchant_id , tranz_id , data_sales_type"; 
											
											if(!empty($_GET['com'])){
    										    echo $sql;
    										}
											
									// die ($sql);		
											//if (isset($_GET['test'])) echo '<br />', $sql, '<br />';
											
									$salesqq = function_mysql_query($sql,__FILE__);
									$volume = 0;
                                    $salesCom = 0;
                                    while ($salesww = mysql_fetch_assoc($salesqq)) {
										
										if ($salesww['data_sales_type'] == 'deposit') {   // NEW.
										
										
										if ($set->showMicroPaymentsOnReports==1  && processMicroPaymentRecord($salesww)){
											$microPaymentsCount++;
											$microPaymentsAmount += $salesww['amount'];
										}
								
										
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
                                            
                                            if($_GET['com']){
										        echo $a."\n";
										    }
                                            
											$totalCom += $a;
											$salesCom += $a;
											
											if ($_GET['com']==2) 	echo 'com4: ' . $totalCom.'('.$a.')<br>';
											
                                        }
                                    }
									
						  if($_GET['com'])
						   {
						       print_r($arrDealTypeDefaults);
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
									$pnlRecordArray['merchant_id']  = $merchant_id;
									$pnlRecordArray['group_id']  = $group_id;
									$pnlRecordArray['searchInSql']  = $searchInSql;
									$pnlRecordArray['fromdate']  = $arrRange['from'];
									$pnlRecordArray['todate']  = $arrRange['to'];
							if (empty( $affiliatesWithPNLArray[$merchantww['id']] ) && empty($affiliate_id)){
								$pnlArray = $pnlRecordArray;
								// unset ($pnlArray['merchant_id']);
								unset ($pnlArray['affiliate_id']);
									
							 $sql = generatePNLquery($pnlArray,false,true);
							 // die ($sql);
							 $affiliatesWithPNLQ = function_mysql_query($sql,__FILE__);
							 while ($affiliatesWithPNLRow = mysql_fetch_assoc($affiliatesWithPNLQ)){
								 // var_dump($affiliatesWithPNLRow);
								 $affiliatesWithPNLArray[$merchantww['id']][$affiliatesWithPNLRow['affiliate_id']] = $affiliatesWithPNLRow['affiliate_id'];
							 }
							 if (empty($affiliatesWithPNLArray[$merchantww['id']]))
								 $affiliatesWithPNLArray[$merchantww['id']][-1]=-1;
						 }
						 
						 
								
								
								if (!empty($affiliate_id)  || isset($affiliatesWithPNLArray[$merchantww['id']][$ww['id']])){
									
									
									if ($dealsForAffiliate['pnl']>0 || isset($dealsForAffiliate['pnl'])){
													// if ($dealsForAffiliate['pnl']>0){
														$sql = generatePNLquery($pnlRecordArray,false);
													// var_dump($dealsForAffiliate);
													// die();
													}
													else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
														$sql = generatePNLquery($pnlRecordArray,true);
													}
													
												    if($_GET['com'])
        										   {
        											   echo $sql;
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
											
										  if($_GET['com'])
										   {
											   echo " Commission after PNL " . $pnlCom . "<br/>";
										   } 
									}
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
						$array['group_id'] = (!empty($group_id)? $group_id : "" ) ;
					
					if (empty($affiliate_id)){
						
						if (empty($affiliateWithInstallationsArray[$merchantww['id']])){
							
							$arrayDistinct = $array;
							unset($arrayDistinct['merchant_id']);
							unset($arrayDistinct['affiliate_id']);
							$affiliateWithInstallationsArray[$merchantww['id']] = generateInstallations($arrayDistinct,true);
							unset($arrayDistinct);
						}
						 if (empty($affiliateWithInstallationsArray[$merchantww['id']]))
								 $affiliateWithInstallationsArray[$merchantww['id']][-1]=-1;
						
					}
					
					
					if (!empty($affiliate_id) || isset($affiliateWithInstallationsArray[$merchantww['id']][$ww['id']])){
					
						$installs = generateInstallations($array);
					
						if (!empty($installs)){
						$totalCPI  = 0;
						
						foreach ($installs as $install_item){
						
								$totalCPI++;
							
								$a= getCommission($install_item['rdate'], $install_item['rdate'], 0, (!empty($group_id)? $group_id : -1), $arrDealTypeDefaults, $install_item);
                              
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
					} // end of processing install for affiliate and check if we should process it anyway...
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
									
									/* 
									$qry = "select refer_id,id from affiliates where id in ( select distinct (refer_id) as id from affiliates) and valid = 1 and sub_com>0" .  ($userlevel=='manager' ? " AND  group_id = " . $group_id : "");
									$affiliateqq = function_mysql_query($sql,__FILE__);
									while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {
										$masterAffiliatesArray[$affiliateww['refer_id']][$affiliateww['id']]=$affiliateww['id'];
									} */
									$isMasterAffiliatesArrayLoaded= true;
								}
								}
						
								 /*
									if (isset($masterAffiliatesArray[$ww['id']])){
												foreach ($masterAffiliatesArray[$ww['id']] as $subID){
													// echo $subID.'<br>';
													// die();
													$comData = getSubAffiliateData($arrRange['from'],$arrRange['to'],$subID,$ww['id'],'commission',$userlevel);
													$totalCom += $comData['commission'];
												}
									} */
									
			//Sub Affiliate Commission
						
						
						/*
						$groupPart = ($group_id<>'' && ($userlevel=='manager' || $group_id>-1)) ? " and group_id = " .$group_id ." ": " " ;
						$qry = "select id,refer_id from affiliates where id in ( select distinct (id) as id from affiliates where refer_id = " . $ww['id'] . ") and valid = 1 and sub_com>0" . $groupPart;
						$rsc = function_mysql_query($qry,__FILE__);
						while ($row = mysql_fetch_assoc($rsc)) {
							*/
							$subcomm= 0;
							if (($isMasterAffiliatesArrayLoaded) && !empty($masterAffiliatesArray))
							foreach ($masterAffiliatesArray as $row){
								
								
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
                                            (int) $totalTraffic['totalViews'] <= 0 && (int) $totalTraffic['totalClicks'] <= 0
                                        ) {
                                            continue;
                                        }
                                    }
							
								 if ($groupMerchantsPerAffiliate == 0) {
									 
                                        $totalImpressions = $totalTraffic['totalViews'] + $totalTrafficA['totalViews'];
                                        $totalClicks = $totalTraffic['totalClicks'] + $totalTrafficA['totalClicks'];
                                        $totalCPI = $totalCPI + $totalCPIA;
                                        $totalLeadsAccounts = $totalLeads+$totalLeadsA;
										
                                        $totalDemoAccounts = $totalDemo+$totalDemoA;
                                        $totalRealAccounts = $totalReal+$totalRealA;
                                        $totalFTD = $ftd+$ftdA;
										//echo $new_qualified_ftdA . "<br/>";
                                        //$totalQFTD = $new_qualified_ftdA + $new_qualified_ftd;
                                        $totalQFTD = $activeTraderA + $activeTrader;
                                        $totalDeposits = $depositingAccountsA+$depositingAccounts;
										$totalmicroPaymentsCount = $microPaymentsCountA+ $microPaymentsCount;
										$totalmicroPaymentsAmount = $microPaymentsAmountA+ $microPaymentsAmount;
                                        $totalFTDAmount = $ftd_amountA+$ftd_amount;
                                        $totalDepositAmount = $sumDepositsA+$sumDeposits;
                                        $totalVolume = $volumeA+$volume;
                                        $totalBonus = $bonusA+$bonus;
                                        $totalWithdrawal = $withdrawalA+$withdrawal;
                                        $totalChargeBack = $chargebackA+$chargeback;
                                        $totalNetRevenue = $netRevenueA+$netRevenue;
                                        $totalComs = $totalComA+$totalCom;
                                        $totalSpreadAmount = $spreadAmountA+$spreadAmount;
                                        $totalTurnoverAmount = $turnoverAmountA+$turnoverAmount;
                                        $totalpnl = $pnlA+$pnl;
                                        $totalFrozens = $frozensA+$frozens;
                                        $totalRealFtd = $real_ftdA+$real_ftd;
                                        $totalRealFtdAmount = $real_ftd_amountA+$real_ftd_amount;
										
										
										
										
										$data['merchant_name'] = $merchantww['name'];
										$data['totalImpressions'] = $totalImpressions;
										$data['totalClicks'] = $totalClicks;
										$data['totalCPIGroup'] = $totalCPI;
										$data['totalRealAccounts'] = $totalRealAccounts;
										$data['totalFTD'] = $totalFTD;
										$data['totalComs'] = $totalComs;
										$data['totalLeadsAccounts'] = $totalLeadsAccounts;
										$data['totalDemoAccounts'] = $totalDemoAccounts;
										$data['totalFrozens'] = $totalFrozens;
										$data['totalFTDAmount'] = $totalFTDAmountA;
										$data['totalRealFtd'] = $totalRealFtd;
										$data['totalRealFtdAmount'] = $totalRealFtdAmountA;
										$data['totalDeposits'] = $totalDepositsA;
										$data['totalmicroPaymentsAmount'] = $totalmicroPaymentsAmountA;
										$data['totalmicroPaymentsCount'] = $totalmicroPaymentsCountA;
										$data['totalDepositAmount'] = $totalDepositAmountA;
										$data['totalBonus'] = $totalBonus;
										$data['totalWithdrawal'] = $totalWithdrawal;
										$data['totalChargeBack'] = $totalChargeBack;
										$data['totalFruad'] = $totalFruad;
										$data['totalVolume'] = $totalVolume;
										$data['totalSpreadAmount'] = $totalSpreadAmount;
										$data['totalpnl'] = $totalpnl;
										$data['totalNetRevenue'] = $totalNetRevenue;
										$data['totalQFTD'] = $totalQFTD;
										
										
										/* $affiliate_id_row = "<td ".(false && $isRedTag ? ' style="color:red;" '  : '' )."   >".$ww['id'].'</td>';
						
										$listReport .= '<tr>';
										
										if (isset($display_type) && !empty($display_type)) {
											   $listReport .= '<td>' . $arrRange['from'] . ' - ' . $arrRange['to'] . '</td>';
										}
                                
								
										   
											$listReport .= 
											$affiliate_id_row .'
												<td><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
												<td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';
											
											if ($campID['title']) {
												if ($ww['campID']) {
													$listReport .= '<td align="left">' . $ww['campID'] . '</td>';
												} else {
													$listReport .= '<td align="left"></td>';
												}	
											}
										   
										   
										   
										   $showWebsite = !empty($ww['website']) && $ww['website']!='http://' && $ww['website']!='http://www.' ? true : false;
										   
											$listReport .=  
												(!$set->isNetwork ? '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>' : '') . '
												<td>' . $merchantww['name'] . '</td>
												'.($set->ShowIMUserOnAffiliatesList ? '<td align="left">'.$ww['IMUser'].'</td>' : ''). '
												<td>'.($showWebsite ? '<a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a>':'').'</td>
												<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=clicks&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'">' . @number_format($totalImpressions, 0) . '</a></td>
												<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=clicks&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'">' . @number_format($totalClicks, 0) . '</a></td>
												'.($set->deal_cpi?'<td style="text-align: center;">' . @number_format($totalCPI) . '</td>':'').'
												<td style="text-align: center;">' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</td>
												<td style="text-align: center;">' . @number_format(($totalRealAccounts / $totalClicks) * 100, 2) . ' %</td>
												<td style="text-align: center;">' . @number_format(($totalFTD / $totalClicks) * 100, 2) . ' %</td>
												<td style="text-align: center;">' . @price($totalComs / $totalClicks) . '</td>' . 
												($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
												<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
												
												'<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
												'.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
												<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
												<td style="text-align: center;">' . price($totalFTDAmountA) . '</td>
												<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
												<td style="text-align: center;">' . price($totalRealFtdAmountA) . '</td>
												<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=transactions&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
												<td style="text-align: center;">' . price($totalDepositAmount) . '</td>
												<!--td style="text-align: center;">' . ($totalDepositsA >0 ? price($totalDepositAmountA/$totalDeposits) : 0) . '</td-->
												
												<td style="text-align: center;">' . price($totalBonus) . '</td>
												<td style="text-align: center;">' . price($totalWithdrawal) . '</td>
												<td style="text-align: center;">' . price($totalChargeBack) . '</td>
												<td style="text-align: center;">' . @number_format(($totalFruadA / $totalFTD) * 100, 2) . '%</td>
												<td style="text-align: center;">' . price($totalVolume) . '</td>
												'.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
												'.(($set->deal_pnl) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
												<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=stats&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
												<td style="text-align: center;">' . number_format($totalQFTD,0) . '</td>
												<td style="text-align: center;">' . price($totalComs) . '</td>
												<td style="text-align: center;">' . $listGroups[$ww['group_id']] . '</td>
											</tr>';
											
											
											 */
											 
											 $listReport .= generateRow($data,$config);
                                        
                                    } else {
											
                                        $totalImpressions += $totalTraffic['totalViews'];
                                        $totalClicks += $totalTraffic['totalClicks'];
                                        $totalCPIGroup += $totalCPI;
                                        $totalLeadsAccounts += $totalLeads;
                                        $totalDemoAccounts += $totalDemo;
                                        $totalRealAccounts += $totalReal;
                                        $totalFTD += $ftd;
                                        //$totalQFTD += $new_qualified_ftd;
										
                                        $totalQFTD += $activeTrader;
                                        $totalDeposits += $depositingAccounts;
                                        $totalmicroPaymentsAmount += $microPaymentsAmount;
                                        $totalmicroPaymentsCount += $microPaymentsCount;
                                        $totalFTDAmount += $ftd_amount;
                                        $totalDepositAmount += $sumDeposits;
                                        $totalVolume += $volume;
                                        $totalBonus += $bonus;
                                        $totalWithdrawal += $withdrawal;
                                        $totalChargeBack += $chargeback;
                                        $totalNetRevenue += $netRevenue;
                                        $totalComs += $totalCom;
                                        $totalSpreadAmount += $spreadAmount;
                                        $totalTurnoverAmount += $turnoverAmount;
                                        $totalpnl += $pnl;
                                        $totalFrozens += $frozens;
                                        $totalRealFtd += $real_ftd;
                                        $totalRealFtdAmount += $real_ftd_amount;
                                    }  
							
								$totalImpressionsM += $totalTraffic['totalViews'];
								$totalClicksM +=$totalTraffic['totalClicks'];
								//echo " Group total CPI  : " . $totalCPIM  . "<br/>";
								//echo " total CPI  : " . $totalCPI  . "<br/>";
								$totalCPIM += $totalCPI;
								//echo " Group total CPI  : " . $totalCPIM  . "<br/>";
								$totalLeadsAccountsM += $totalLeads;
								$totalDemoAccountsM += $totalDemo;
								$totalRealAccountsM += $totalReal;
								$totalFTDM += $ftd;
								$totalQFTDM += $activeTrader;
								$totalDepositsM += $depositingAccounts;
								$totalmicroPaymentsAmountM += $totalmicroPaymentsAmount;
								$totalmicroPaymentsCountM += $totalmicroPaymentsCount;
								$totalFTDAmountM += $ftd_amount;
								$totalDepositAmountM += $sumDeposits;
								$totalVolumeM += $volume;
								$totalBonusM += $bonus;
								$totalWithdrawalM += $withdrawal;
								$totalChargeBackM += $chargeback;
								$totalNetRevenueM += $netRevenue;
								$totalComsM += $totalCom;
								$totalSpreadAmountM += $spreadAmount;
								$totalTurnoverAmountM += $turnoverAmount;
								$totalFrozensM += $frozens;
								$totalpnlM += $pnl;
								$totalRealFtdM += $real_ftd;
								$totalRealFtdAmountM += $real_ftd_amount;
										
								/* if($groupMerchantsPerAffiliate==1){
									$totalImpressions += $totalTraffic['totalViews']; 
									$totalClicks += $totalTraffic['totalClicks'] ;
								} */
							
								$l++;        
							   
                        }
						
						}
                        // end of else
							
                        // Loop through affiliates, aggregate the per-merchant info.
                        if ($groupMerchantsPerAffiliate == 1) {
								if (isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 0) {
                                        ;
                                    } else {


									
						if (
								
                                            (int) $stats <= 0 && (int) $totalLeadsAccounts <= 0 && (int) $totalDemoAccounts <= 0 && 
                                            (int) $totalRealAccounts <= 0 && (int) $totalFTD <= 0 && 
											(int) $totalComs <= 0 && 
                                            (int) $totalDeposits <= 0 && 
                                            (int) $totalBonus <= 0 && 
                                            (int) $totalCPI <= 0 && 
                                            (int) $totalWithdrawal <= 0 && 
                                            (int) $totalRealAccounts <= 0 && (int) $totalFTD <= 0 && 
                                            (int) $totalImpressions <= 0 && (int) $totalClicks <= 0
											
											
                                        ) {
                                            continue;
                                        }
                                    }
							
							
							
							$data['totalImpressions'] = $totalImpressions;
							$data['totalClicks'] = $totalClicks;
							$data['totalCPIGroup'] = $totalCPIGroup;
							$data['totalRealAccounts'] = $totalRealAccounts;
							$data['totalFTD'] = $totalFTD;
							$data['totalComs'] = $totalComs;
							$data['totalLeadsAccounts'] = $totalLeadsAccounts;
							$data['totalDemoAccounts'] = $totalDemoAccounts;
							$data['totalFrozens'] = $totalFrozens;
							$data['totalFTDAmount'] = $totalFTDAmount;
							$data['totalRealFtd'] = $totalRealFtd;
							$data['totalRealFtdAmount'] = $totalRealFtdAmount;
							$data['totalmicroPaymentsAmount'] = $totalmicroPaymentsAmount;
							$data['totalmicroPaymentsCount'] = $totalmicroPaymentsCount;
							$data['totalDeposits'] = $totalDeposits;
							$data['totalDepositAmount'] = $totalDepositAmount;
							$data['totalBonus'] = $totalBonus;
							$data['totalWithdrawal'] = $totalWithdrawal;
							$data['totalChargeBack'] = $totalChargeBack;
							$data['totalFruad'] = $totalFruad;
							$data['totalVolume'] = $totalVolume;
							$data['totalSpreadAmount'] = $totalSpreadAmount;
							$data['totalpnl'] = $totalpnl;
							$data['totalNetRevenue'] = $totalNetRevenue;
							$data['totalQFTD'] = $totalQFTD;
							
							
	// echo '111: ' . $totalComs .'<br>';
							$tmpArr = array();
							$tmpArr['config'] = $config;
							if (isset($arrayOfMergedAffiliatesResults[$ww['id']]['data'])){
							$currentData = $arrayOfMergedAffiliatesResults[$ww['id']]['data'];
							$data['totalImpressions'] = $data['totalImpressions'] + $currentData['totalImpressions'];
							$data['totalClicks'] = $data['totalClicks'] + $currentData['totalClicks'];
							$data['totalCPIGroup'] = $data['totalCPIGroup'] + $currentData['totalCPIGroup'];
							$data['totalRealAccounts'] = $data['totalRealAccounts'] + $currentData['totalRealAccounts'];
							$data['totalFTD'] = $data['totalFTD'] + $currentData['totalFTD'];
							$data['totalComs'] = $data['totalComs'] + $currentData['totalComs'];
							$data['totalLeadsAccounts'] = $data['totalLeadsAccounts'] + $currentData['totalLeadsAccounts'];
							$data['totalFrozens'] = $data['totalFrozens'] + $currentData['totalFrozens'];
							$data['totalFTDAmount'] = $data['totalFTDAmount'] + $currentData['totalFTDAmount'];
							$data['totalRealFtd'] = $data['totalRealFtd'] + $currentData['totalRealFtd'];
							$data['totalRealFtdAmount'] = $data['totalRealFtdAmount'] + $currentData['totalRealFtdAmount'];
							$data['totalmicroPaymentsAmount'] = $data['totalmicroPaymentsAmount'] + $currentData['totalmicroPaymentsAmount'];
							$data['totalmicroPaymentsCount'] = $data['totalmicroPaymentsCount'] + $currentData['totalmicroPaymentsCount'];
							$data['totalDeposits'] = $data['totalDeposits'] + $currentData['totalDeposits'];
							$data['totalDepositAmount'] = $data['totalDepositAmount'] + $currentData['totalDepositAmount'];
							$data['totalBonus'] = $data['totalBonus'] + $currentData['totalBonus'];
							$data['totalWithdrawal'] = $data['totalWithdrawal'] + $currentData['totalWithdrawal'];
							$data['totalChargeBack'] = $data['totalChargeBack'] + $currentData['totalChargeBack'];
							$data['totalVolume'] = $data['totalVolume'] + $currentData['totalVolume'];
							$data['totalFruad'] = $data['totalFruad'] + $currentData['totalFruad'];
							$data['totalSpreadAmount'] = $data['totalSpreadAmount'] + $currentData['totalSpreadAmount'];
							$data['totalpnl'] = $data['totalpnl'] + $currentData['totalpnl'];
							$data['totalNetRevenue'] = $data['totalNetRevenue'] + $currentData['totalNetRevenue'];
							$data['totalQFTD'] = $data['totalQFTD'] + $currentData['totalQFTD'];
							

							}
 
							$tmpArr['data'] = $data;
							$arrayOfMergedAffiliatesResults[$ww['id']]=$tmpArr;
							
							// $listReport .= generateRow($data,$config);
								/*  $listReport .= '<tr>';	
									
                            if (isset($display_type) && !empty($display_type)) {
                                $listReport .= '<td>' . $arrRange['from'] . ' - ' . $arrRange['to'] . '</td>';
                            }
						   $affiliate_id_row = "<td ".(false && $isRedTag ? ' style="color:red;" '  : '' )."   >".$ww['id'].'</td>';
							
                            $listReport .= 
								$affiliate_id_row .'
                                <td><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
                                <td>'.$ww['first_name'].' '.$ww['last_name'].'</td>';
                            
                            if (!empty($campID['title'])) {
                                if ($ww['campID']) {
                                    $listReport .= '<td align="left">' . $ww['campID'] . '</td>';
                                } else {
                                    $listReport .= '<td align="left"></td>';
                                }	
                            }
                            
                            
													// echo 'isset($_GET:  ' . isset($_GET['showAllRecords']).'<Br>';
							// echo 'showAllRecords:  '  .($_GET['showAllRecords']).'<Br>';
							// die();
					
									
							$showWebsite = !empty($ww['website']) && $ww['website']!='http://' && $ww['website']!='http://www.' ? true : false;
							

							
							
							
							
                            $listReport .= 
                                (!$set->isNetwork ? '<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>' : '') . '
                                '.($set->ShowIMUserOnAffiliatesList ? '<td align="left">'.$ww['IMUser'].'</td>' : ''). '
								<td>'.($showWebsite ? '<a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a>':'').'</td>
                                <td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=clicks&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'">' . @number_format($totalImpressions, 0) . '</a></td>
                                <td style="text-align: center;"><a href="/'. $userlevel.'/reports.php?act=clicks&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'">' . @number_format($totalClicks, 0) . '</a></td>
                                '.($set->deal_cpi?'<td style="text-align: center;">' . @number_format($totalCPIGroup) . '</td>':'').'
                                <td style="text-align: center;">' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format(($totalRealAccounts / $totalClicks) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format(($totalFTD / $totalClicks) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @price($totalComs / $totalClicks) . '</td>' . 
                                ($hideDemoAndLeads ? '' : '<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=lead">' . $totalLeadsAccounts . '</a></td>
                                <td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=demo">' . $totalDemoAccounts . '</a></td>') . 
                                
                                '<td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=real">' . $totalRealAccounts . '</a></td>
                                '.($showCasinoFields ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                <td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=ftd">' . $totalFTD . '</a></td>
                                <td style="text-align: center;">' . price($totalFTDAmount) . '</td>
                                <td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=totalftd">' . $totalRealFtd . '</a></td>
                                <td style="text-align: center;">' . price($totalRealFtdAmount) . '</td>
                                <td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=transactions&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=deposit">' . $totalDeposits . '</a></td>
                                <td style="text-align: center;">' . price($totalDepositAmount) . '</td>
                                <!--td style="text-align: center;">' . ($totalDeposits>0?price($totalDepositAmount/$totalDeposits):0) . '</td-->
                                <td style="text-align: center;">' . price($totalBonus) . '</td>
                                <td style="text-align: center;">' . price($totalWithdrawal) . '</td>
                                <td style="text-align: center;">' . price($totalChargeBack) . '</td>
                                <td style="text-align: center;">' . @number_format(($totalFruad / $totalFTD) * 100, 2) . '%</td>
                                <td style="text-align: center;">' . price($totalVolume) . '</td>
                                '.(($displayForex) ? '<td style="text-align: center;">' . price($totalSpreadAmount) . '</td>' : '') . '
                                '.(($set->deal_pnl) ? '<td style="text-align: center;">' . price($totalpnl) . '</td>' : '') . '
                                <td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=transactions&type=volume&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id='.$merchant_id.'">' . price($totalNetRevenue) . '</a></td>
                                <td style="text-align: center;"><a href="/'. $userlevel .'/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['id'].'&type=activeTrader">' .  number_format($totalQFTD,0) . '</a></td>
								<td style="text-align: center;">' . price($totalComs) . '</td>
                                <td style="text-align: center;">' . $listGroups[$ww['group_id']] . '</td>
                            </tr>';
						 */
						
						if($_GET['com'])
							   {
								   echo " Commission 2: totalComs " . $totalComs . "<br/>";
							   } 
							   
						$totalpnl=0;						
						$pnl=0;
						$totalComs=0;
						$totalCom=0;
						$totalQFTD=0;
						
							   
                        }
                        
                        $intAffiliatesCombinedCount++;
						$totalVolume = 0;
                    }
                    
                    unset($arrRange); // Clear up the memory.
                } // End of time-periods loop.
				foreach ($arrayOfMergedAffiliatesResults as $mergedAff){
					
					$listReport .= generateRow($mergedAff['data'],$mergedAff['config']);
				}
               
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable  = 1;
		$set->totalRows  = isset($groupByAff) && 1 ==$groupByAff ? $intAffiliatesCombinedCount : $l;
	
		
		$set->content   .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
		<div style="background: #F8F8F8;">
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="affiliate_old" />
			<input type="hidden" name="search" value="1" />
			<table><tr>
                            <td>'.lang('Period').'</td>
                            <td width=160>'.lang('Affiliate ID').'</td>
                            '.($userlevel == 'admin'?'<td style="padding-left:10px">'.lang('Groups').'</td>':'').'
                            <td style="padding-left:10px">'.lang('Merchant').'</td>
                            <td>'.lang('Show Affiliates').'</td>
                            <td>'.lang('Search Type').'</td>'
                            . ($intMerchantCount > 1 ? '<td>' . lang('Group Merchants') . '</td> ': '') .
                            '<td></td>
				</tr><tr>
				<td>'.timeFrame((isset($static_from)?$static_from:$from),$to).'</td>
				<td><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="affiliate_id" style="width: 100px;" /-->
				<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
				</td>
				'.($userlevel == 'admin'?'<td width="100" style="padding-left:10px"><select name="group_id" style="width: 100px;"><option value="">'.lang('All Groups').'</option><option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listGroups($group_id).'</select></td>':'').'
				<td style="padding-left:10px"><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
				<td><select name="showAllRecords" style="width: 150px;"><option id=1 value="1">'.lang('Only Active Affiliates').'</option><option id=0 value="0" '.(isset($showAllRecords) && $showAllRecords==0 ? ' selected ' : '' ).'>'.lang('All Affiliates').'</option></select></td>
				<td class="tooltip"><select name="display_type" id="display_type" style="width: 150px;" disabled><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select><span class="tooltiptext">'. lang("Available only if affiliate is selected.") .'</span></td>'
				. ($intMerchantCount > 1 ? '<!--td><input type="checkbox" id="groupByAff" name="groupByAff" '.($groupMerchantsPerAffiliate==1 ? 'checked' : '').'/-->
				<td colspan=2><div><label class="switch"><input type="checkbox" id="groupByAff" name="groupByAff"  '.($groupMerchantsPerAffiliate==1 ? 'checked' : '').' ><div class="slider round"></div></label></div>
				<input type="hidden" id="groupByAffVal" value="'.($groupMerchantsPerAffiliate).'" name="groupByAff" /></td>' : '') .
				
				'<script type="text/javascript">
					$(document).ready(function(){
						if($("#affiliate_id").val()!=""){
							$(".tooltiptext").hide();
							$("#display_type").attr("disabled",false);
						}
						$("#groupByAff").change(function(e){
							if($("#groupByAff").is(":checked")){
								$("#groupByAffVal").val("1");
							}else{
								$("#groupByAffVal").val("0");
							}
						});
						
						$("#affiliate_id").keyup(function(){
							var affid = $(this).val();
							
							if( affid !="" && isNumeric(affid)){
								$(".tooltiptext").hide();
								$("#display_type").attr("disabled",false);
							}
							else{
								$(".tooltiptext").show();
								$("#display_type").attr("disabled",true);
							}
						});
						function isNumeric(n) {
						  return !isNaN(parseFloat(n)) && isFinite(n);
						}

					});
				</script>
				<td><input type="submit" value="'.lang('View').'" /></td>
			</tr></table>
			</form>
			
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#affiliatesData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});" ><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#affiliatesData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});" ><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		<!--href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"--> 
		<div class="normalTableTitle" class="table">'.lang('Affiliate Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
			
				$tableStr='<table class="table '.(empty($listReport)?'normal':'tablesorter').' mdlReportFields" border="0" cellpadding="0" cellspacing="0"  id="affiliates">
					<thead><tr class="table-row">'
                      . (isset($display_type) && !empty($display_type) ? '<th style="padding: 0 80px;">' . lang('Period') . '</th>' : '') . '
						<th class="table-cell">'.lang('Affiliate ID').'</th>
						<th class="table-cell">'.lang('Username').'</th>
						<th class="table-cell">'.lang('Full Name').'</th>
						'.(!empty($campID['title']) ? '<th class="table-cell">'.lang($campID['title']).'</th>' : '') .
						(!$set->isNetwork ? '<th class="table-cell">'.lang('E-Mail').'</th>' : '') . 
						($groupMerchantsPerAffiliate==0 ? '<th class="table-cell">'.lang('Merchant').'</th>':'').'
						'.($set->ShowIMUserOnAffiliatesList ? '<th class="table-cell">'.lang('IM-User').'</th>' : ''). '
						<th class="table-cell">'.lang('Website').'</th>
						<th class="table-cell">'.lang('Impressions').'</th>
						<th class="table-cell">'.lang('Clicks').'</th>
						'.($set->deal_cpi?'<th class="table-cell">'.lang('Installation').'</th>':'').'
						<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
						<th class="table-cell">'.lang(ptitle('Click to Account')).'</th>
						<th  class="table-cell">'.lang(ptitle('Click to Sale')).'</th>
						<th class="table-cell">EPC</th>' . 
                                                ($hideDemoAndLeads ? '' : '<th class="table-cell">'.lang(ptitle('Lead')).'</th>
                                                                           <th class="table-cell">'.lang(ptitle('Demo')).'</th>') . 
						
						'<th class="table-cell">'.lang(ptitle('Accounts')).'</th>
						'.($showCasinoFields ? '<th class="table-cell">'.lang(ptitle('Frozens')).'</th>' : '<th>|FROZEN|</th>').'
						<th class="table-cell">'.lang('FTD').'</th>
						<th class="table-cell">'.lang('FTD Amount').'</th>
						<th class="table-cell">'.lang('RAW FTD').'</th>
						<th class="table-cell">'.lang('RAW FTD Amount').'</th>
						'.($set->showMicroPaymentsOnReports ==1  ? '
						<th  class="table-cell">'.lang('Total MicroPayments').'</th>
						<th  class="table-cell">'.lang('MicroPayments Amount').'</th>' : '' ) . ' 
						
						<th class="table-cell">'.lang('Total Deposits').'</th>
						<th class="table-cell">'.lang('Deposit Amount').'</th>
						<!--th>'.lang('Trader Value').'</th-->
						
						<th class="table-cell">'.lang('Bonus Amount').'</th>
						<th class="table-cell">'.lang('Withdrawal Amount').'</th>
						<th class="table-cell">'.lang('ChargeBack Amount').'</th>
						<th class="table-cell">'.lang('Affiliate Risk').'</th>
						<th class="table-cell">'.lang(ptitle('Volume')).'</th>
						'.(($displayForex) ? '<th class="table-cell">'.lang('Spread Amount').'</th>' : '').'
						'.(($set->deal_pnl) ? '<th class="table-cell">'.lang('PNL').'</th>' : '').'
						<th class="table-cell">'.lang(ptitle('Net Revenue')).'</th>
						<th class="table-cell">'.lang(ptitle('Active Traders')).'</th>
						<th class="table-cell">'.lang('Commission').'</th>
						<th class="table-cell">'.lang('Group').'</th>
					</tr>
                                        </thead>
                                        <tfoot>
                                            <tr class="table-row">
						<th><b>'.lang('Total').':</b></th>'
                                                . (isset($display_type) && !empty($display_type) ? '<th></th>' : '') . '
						<th></th>
						<th></th>
						<th></th>
						'.(!empty($campID['title']) ? '<th></th>' : '') .'
                                                <th></th>
                                                ' . ($groupMerchantsPerAffiliate == 0 ? '<th></th>' : '') . '
                                                ' . ($set->ShowIMUserOnAffiliatesList ? '<th></th>' : '') . '
						<th><a href="/'. $userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressionsM.'</a></th>
						<th><a href="/'. $userlevel.'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicksM.'</a></th>
						'.($set->deal_cpi?'<th>' . @number_format($totalCPIM) . '</th>':'').'
						<th>' . @number_format((div($totalClicksM,$totalImpressionsM)) * 100, 2) . ' %</th>
						<th>' . @number_format((div($totalRealAccountsM,$totalClicksM)) * 100, 2) . ' %</th>
						<th>' . @number_format((div($totalFTDM,$totalClicksM)) * 100, 2) . ' %</th>
						<th>' . @price(div($totalComsM,$totalClicksM)) . '</th>' . 
                                                ($hideDemoAndLeads ? '' : '<th><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">' . $totalLeadsAccountsM . '</a></th>
						<th><a href="/'.  $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">' . $totalDemoAccountsM . '</a></th>') .
						
						'<th><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">' . $totalRealAccountsM . '</a></th>
						'.($showCasinoFields ? '<th>' . $totalFrozensM . '</th>' : '<th>|FROZEN|</th>').'
						<th><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTDM.'</a></th>
						
						<th>' . price($totalFTDAmountM) . '</th>
						<th><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">' . $totalRealFtdM . '</a></th>
						
						<th>' . price($totalRealFtdAmountM) . '</th>
						
						'.	($set->showMicroPaymentsOnReports ==1  ? '
						<th></th>
						<th></th>
						' : '' ).'
						
						<th><a href="/'. $userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">' . $totalDepositsM . '</a></th>
						<th>' . price($totalDepositAmountM) . '</th>
						
						<!--th></th-->
						<th>' . price($totalBonusM) . '</th>
						<th>' . price($totalWithdrawalM) . '</th>
						<th>' . price($totalChargeBackM) . '</th>
						<th></th>
						<th>' . price($totalVolumeM) . '</th>
						' . (($displayForex) ? '<th>' . price($totalSpreadAmountM) . '</th>' : '') . '
						' . (($set->deal_pnl) ? '<th>' . price($totalpnlM) . '</th>' : '') . '
						<th>' . price($totalNetRevenueM) . '</th>
						<th>' . number_format($totalQFTDM,0) . '</th>
						<th>' . price($totalComsM) . '</th>
						<th></th>
                                            </tr>
                                        </tfoot>
					<tbody>
					' . $listReport . '
                                        </tbody>
				</table>
				<script>
				$(document).ready(function(){
					try{
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'affiliatesData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#affiliates")[0].config.rowsCopy).each(function() {
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
											
											saveReportToMyFav(name, \'affiliates\',user,level,type);
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
                                
		if ($showCasinoFields) {
                    $tableStr = str_replace('<td>|FROZEN|</td>', '<td></td>', $tableStr);
                    $tableStr = str_replace('<th>|FROZEN|</th>', '<th></th>', $tableStr);
		} else {
                    $tableStr = str_replace('<td>|FROZEN|</td>', '', $tableStr);
                    $tableStr = str_replace('<th>|FROZEN|</th>', '', $tableStr);
		}
         
		$set->content .= $tableStr_export . "</div>";
		
		$set->content .= $tableStr . '</div>' . getPager();
		
		//MODAL
		$myReport = lang("Affiliates");
		include "common/ReportFieldsModal.php";
		
		excelExporter($tableStr, 'Affiliate');
		theme();

		
		
		
		function generateRow($data, $config){
			
			global $set,$listGroups ;

					$listReport .= '<tr>';	
									
					if (isset($config['display_type']) && !empty($config['display_type'])) {
						$listReport .= '<td>' . $data['from'] . ' - ' . $data['to'] . '</td>';
					}
					$affiliate_id_row = "<td ".(false && $config['isRedTag'] ? ' style="color:red;" '  : '' )."   >".$data['affiliate_id'].'</td>';
							
					$listReport .= 
						$affiliate_id_row .'
						<td><a href="/'. $config['userlevel'] .'/affiliates'.$ver.'.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_username'].'</a></td>
						<td>'.$data['affiliate_firstname'].' '.$data['affiliate_lastname'].'</td>';
                            
                            if (!empty($data['campId_title'])) {
                                if ($data['affiliate_campID']) {
                                    $listReport .= '<td align="left">' . $data['affiliate_campID'] . '</td>';
                                } else {
                                    $listReport .= '<td align="left"></td>';
                                }	
                            }
                            
                            
													// echo 'isset($_GET:  ' . isset($_GET['showAllRecords']).'<Br>';
							// echo 'showAllRecords:  '  .($_GET['showAllRecords']).'<Br>';
							// die();
					
									
							$showWebsite = !empty($data['website']) && $data['website']!='http://' && $data['website']!='http://www.' ? true : false;
						
                            $listReport .= 
                                (!$set->isNetwork ? '<td><a href="mailto:'.$data['mail'].'">'.$data['mail'].'</a></td>' : '') . '
								'.($config['groupMerchantsPerAffiliate']==0?'<td>' . $data['merchant_name'] . '</td>':'').'
                                '.($set->ShowIMUserOnAffiliatesList ? '<td align="left">'.$data['IMUser'].'</td>' : ''). '
								<td>'.($showWebsite ? '<a href="/out.php?refe='.urlencode($data['website']).'" target="_blank">'.$data['website'].'</a>':'').'</td>
                                <td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=clicks&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id='.$data['affiliate_id'].'">' . @number_format($data['totalImpressions'], 0) . '</a></td>
                                <td style="text-align: center;"><a href="/'. $config['userlevel'].'/reports.php?act=clicks&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id='.$ww['affiliate_id'].'">' . @number_format($data['totalClicks'], 0) . '</a></td>
                                '.($set->deal_cpi?'<td style="text-align: center;">' . @number_format($data['totalCPIGroup']) . '</td>':'').'
                                <td style="text-align: center;">' . @number_format((div($data['totalClicks'],$data['totalImpressions'])) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format((div($data['totalRealAccounts'],$data['totalClicks'])) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format((div($data['totalFTD'],$data['totalClicks'])) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @price(div($data['totalComs'],$data['totalClicks'])) . '</td>' . 
                                ($config['hideDemoAndLeads'] ? '' : '<td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id='.$data['affiliate_id'].'&type=lead">' . $data['totalLeadsAccounts']. '</a></td>
                                <td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id='.$data['affiliate_id'].'&type=demo">' . $data['totalDemoAccounts'] . '</a></td>') . 
                                
                                '<td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id='.$data['affiliate_id'].'&type=real">' . $data['totalRealAccounts'] . '</a></td>
                                '.($config['showCasinoFields'] ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                <td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id='.$data['affiliate_id'].'&type=ftd">' . $data['totalFTD'] . '</a></td>
                                <td style="text-align: center;">' . price($data['totalFTDAmount']) . '</td>
                                <td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id='.$data['affiliate_id'].'&type=totalftd">' . $data['totalRealFtd'] . '</a></td>
                                <td style="text-align: center;">' . price($data['totalRealFtdAmount']) . '</td>
								'. ($set->showMicroPaymentsOnReports==1  ?  '
                                <td style="text-align: center;">' . ($data['totalmicroPaymentsCount']) . '</td>
                                <td style="text-align: center;">' . price($data['totalmicroPaymentsAmount']) . '</td> ' : '' ).'
								
								<td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=transactions&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id='.$data['affiliate_id'].'&type=deposit">' . $data['totalDeposits'] . '</a></td>
                                <td style="text-align: center;">' . price($data['totalDepositAmount']) . '</td>
                                <!--td style="text-align: center;">' . ($data['totalDeposits']>0?price($data['totalDepositAmount']/$data['totalDeposits']):0) . '</td-->
                                <td style="text-align: center;">' . price($data['totalBonus']) . '</td>
                                <td style="text-align: center;">' . price($data['totalWithdrawal']) . '</td>
                                <td style="text-align: center;">' . price($data['totalChargeBack']) . '</td>
                                <td style="text-align: center;">' . @number_format((div($data['totalFruad'],$data['totalFTD'])) * 100, 2) . '%</td>
                                <td style="text-align: center;">' . price($data['totalVolume']) . '</td>
                                '.(($config['displayForex']) ? '<td style="text-align: center;">' . price($data['totalSpreadAmount']) . '</td>' : '') . '
                                '.(($set->deal_pnl) ? '<td style="text-align: center;">' . price($data['totalpnl']) . '</td>' : '') . '
                                <td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=transactions&type=volume&from=' . $data['from'] . '&to=' . $data['to'] . '&merchant_id='.$data['merchant_id'].'">' . price($data['totalNetRevenue']) . '</a></td>
                                <td style="text-align: center;"><a href="/'. $config['userlevel'] .'/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id='.$data['affiliate_id'].'&type=activeTrader">' .  number_format($data['totalQFTD'],0) . '</a></td>
								<td style="text-align: center;">' . price($data['totalComs']) . '</td>
                                <td style="text-align: center;">' . $listGroups[$data['group_id']] . '</td>
                            </tr>';
				return $listReport;
		}
		
?>