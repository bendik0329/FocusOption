<?php

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
//$ver = "_new";
$ver = "";

if (!defined('DirectBrowse')) {
    $path = "http" . $set->SSLswitch . "://" . $_SERVER[HTTP_HOST];
    header("Location: " . $path . "/" . $userlevel);
}
$set->content .= '<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/tableExport.js"></script>

<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/filesaver.js"></script>
<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/jquery.base64.js"></script>
<script type="text/javascript" src="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.js"></script>
<link rel="stylesheet" href="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.css"/>';

$pageTitle = lang('Affiliate Report');


$filename = "affiliates_data_" . date('YmdHis');

$set->breadcrumb_title = lang($pageTitle);
$set->pageTitle = '
			
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="' . $set->SSLprefix . $userlevel . '/">' . lang('Dashboard') . '</a></li>
				<li><a href="' . $set->SSLprefix . $set->uri . '">' . lang($pageTitle) . '</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

$set->content .= '<style>
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
		<script src="' . $set->SSLprefix . 'js/autocomplete.js"></script>
			<script>
			$(document).ready(function(){
				$("#combobox").combobox("autocomplete","' . $_GET['affiliate_id'] . '");
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



if ($userlevel == 'manager') {
    $group_id = $set->userInfo['group_id'];
}

$showLeadsAndDemo = false;
$where = '';

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

$merchant_id = (isset($_GET['merchant_id']) && $_GET['merchant_id'] > 0) ? $_GET['merchant_id'] : 0;
// $merchant_id= 0;
$merchantsAr = getMerchants($merchant_id, 1);
// var_dump($merchantsAr);
// die();
$merchantsArray = $merchantsAr;

foreach ($merchantsAr as $arrMerchant) {


    // var_dump($arrMerchant);
    // die();
    // while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
    if (empty($campID)) {
        $campID['title'] = $arrMerchant['extraMemberParamName'];
    }

    if (strtolower($arrMerchant['producttype']) == 'forex') {
        $displayForex = 1;
    }

    $mer = $arrMerchant['id'];

    // List of wallets.
    $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1 "
            . (isset($mer) && !empty($mer) ? ' AND id = ' . $mer : '');

    $resourceWallets = function_mysql_query($sql, __FILE__);

    while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
        $arrWallets[$arrWallet['wallet_id']] = false;
        unset($arrWallet);
    }
}

$intMerchantCount = count($merchantsArray);


if (isset($group_id) && $group_id != '') {
    $where .= ' AND a.group_id = ' . $group_id . ' ';
}

if (isset($groupByAff)) {

    if ($groupByAff == 1) {
        $groupMerchantsPerAffiliate = 1;
    } else {
        $groupMerchantsPerAffiliate = 0;
    }
} else {
    $groupMerchantsPerAffiliate = 1;
}


if (isset($affiliate_id) && !empty($affiliate_id) && is_numeric($affiliate_id)) {
    $where .= ' AND a.id = ' . $affiliate_id . ' ';
} elseif (isset($affiliate_id) && !empty($affiliate_id)) {
    $where .= " AND (lower(a.username) LIKE '%" . trim(strtolower($affiliate_id)) . "%' ";
    if (!empty($campID['title'])) {
        $where .= " or a.id in (SELECT affiliateID FROM  `affiliates_campaigns_relations` WHERE  `campID` LIKE  '%" . strtolower($affiliate_id) . "%' )";
    }
    $where .= " ) ";
}


// Initialize total counters for all affiliates.
$totalImpressionsM = 0;
$totalClicksM = 0;
$totalCPIM = 0;
$totalLeadsAccountsM = 0;
$totalDemoAccountsM = 0;
$totalRealAccountsM = 0;
$totalFTDM = 0;
$totalQFTDM = 0;
$totalDepositsM = 0;
$totalFTDAmountM = 0;
$totalmicroPaymentsAmountM = 0;
$totalmicroPaymentsCountM = 0;
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




$totalCPIMA = 0;


$depositsAmount = 0;

$l = 0;

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


$sql = 'SELECT COUNT(id) AS count FROM merchants WHERE valid = 1 ' . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');

$arrMerchantsCount = mysql_fetch_assoc(function_mysql_query($sql, __FILE__));
$intMerchantsCount = (int) $arrMerchantsCount['count'];

// List of wallets.
$arrWallets = array();
$sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1 "
        . (isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id : '');

$resourceWallets = function_mysql_query($sql, __FILE__);

while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
    $arrWallets[$arrWallet['wallet_id']] = false;
    unset($arrWallet);
}


$newArrRanges = array();
$paramFromSettings = getStaticReportMonths();

$DateBeforeMonthsAgo = getXmonthsAgoDate($paramFromSettings);
$DateBeforeMonthsAgo = decreaseDayFromDate($DateBeforeMonthsAgo);

/*
foreach ($arrRanges as $arrRange) {

    if ((isDateConsiderStatic($arrRange['from']) && isDateConsiderStatic($arrRange['to'])) ||
            (!isDateConsiderStatic($arrRange['from']) && !isDateConsiderStatic($arrRange['to']))
    ) {

        $newArrRanges[] = $arrRange;
    } else {


        $newRange = array();
        $newRange['from'] = $arrRange['from'];
        $newRange['to'] = $DateBeforeMonthsAgo . ' 23:59:59';
        $newArrRanges[] = $newRange;
        $DateBeforeMonthsAgoPlusDay = addDayToDate($DateBeforeMonthsAgo);
        $newRange['from'] = $DateBeforeMonthsAgoPlusDay . ' 00:00:00';
        $newRange['to'] = $arrRange['to'];

        $newArrRanges[] = $newRange;
        unset($newRange);
        unset($arrRange);
    }
}

$arrRanges = $newArrRanges;
*/


if ((isset($_GET['showAllRecords']) && $_GET['showAllRecords'] == 1) || isset($_GET['showAllRecords']) == false) {
    $where .= " AND (d.Leads > 0  OR d.Demo > 0 OR d.RealAccount > 0 OR d.FTD > 0 OR d.Deposits OR d.Commission != 0 OR d.Clicks > 0 OR d.Impressions > 0) ";
}

foreach ($arrRanges as $arrRange) {

    $intAffiliatesCombinedCount = 0;
    $showCasinoFields = 0;
    $totalRealFtd = 0;
    $totalRealFtdAmount = 0;
    $isRedTag = true;

    /*
    $sql = "SELECT affiliates.*, acr.campID FROM affiliates LEFT JOIN"
            . "(select distinct(affiliateID),campID from affiliates_campaigns_relations where 1 =1 "
            . (isset($merchant_id) && !empty($merchant_id) ? ' AND merchantid = ' . $merchant_id : '') . " group by affiliateID) acr ON affiliates.id = acr.affiliateID"
            . " WHERE valid = 1 " . $where
            . "ORDER BY affiliates.id DESC";
    */
    
    
/*
    $sql = "select a.*, 
        SUM(d.Clicks) as totalClicks, 
        SUM(d.Impressions) as totalImpressions, 
        SUM(d.Install) AS totalCPIGroup,
        SUM(d.RealAccount) as totalRealAccounts,
        SUM(d.Leads) as totalLeadsAccounts,
        SUM(d.Demo) as totalDemoAccounts,
        SUM(d.FTD) as totalFTD,
        SUM(d.FTDAmount) as totalFTDAmount,
        SUM(d.RawFTD) as totalRealFtd,
        SUM(d.RawFTDAmount) as totalRealFtdAmount,
        SUM(d.Deposits) AS totalDeposits,
        SUM(d.DepositsAmount) AS totalDepositAmount,
        SUM(d.Bonus) AS totalBonus,
        SUM(d.Withdrawal) AS totalWithdrawal,
        SUM(d.ChargeBack) AS totalChargeBack,
        SUM(d.PNL) AS totalpnl,
        SUM(d.NetDeposit) AS totalNetRevenue,
        SUM(d.ActiveTrader) as totalQFTD,
        
        SUM(d.Commission) AS totalComs,
 
        SUM(d.TotalMicroPayments) as totalmicroPaymentsCount, 
        SUM(d.MicroPaymentsAmount) as totalmicroPaymentsAmount, 
        SUM(d.Volume) as totalVolume
 
    FROM affiliates a 
inner join Dashboard d ON a.id=d.AffiliateID AND d.Date>='".$arrRange['from']."' AND d.Date<='".$arrRange['to']."' ".(isset($merchant_id) && !empty($merchant_id) ? ' AND d.MerchantID = ' . $merchant_id : '')."
WHERE a.valid =1 ".$where."
group by a.id ORDER BY a.id DESC";
*/


$sql = "
Select 
    aff1.*,
    IF(bc.badc IS NULL,aff1.totalComsAll,aff1.totalComsAll-bc.badc) as totalComs
from (
    select a.*, 
        SUM(d.Clicks) as totalClicks, 
        SUM(d.Impressions) as totalImpressions, 
        SUM(d.Install) AS totalCPIGroup,
        SUM(d.RealAccount) as totalRealAccounts,
        SUM(d.Leads) as totalLeadsAccounts,
        SUM(d.Demo) as totalDemoAccounts,
        SUM(d.FTD) as totalFTD,
        SUM(d.FTDAmount) as totalFTDAmount,
        SUM(d.RawFTD) as totalRealFtd,
        SUM(d.RawFTDAmount) as totalRealFtdAmount,
        SUM(d.Deposits) AS totalDeposits,
        SUM(d.DepositsAmount) AS totalDepositAmount,
        SUM(d.Bonus) AS totalBonus,
        SUM(d.Withdrawal) AS totalWithdrawal,
        SUM(d.ChargeBack) AS totalChargeBack,
        SUM(d.PNL) AS totalpnl,
        SUM(d.NetDeposit) AS totalNetRevenue,
        SUM(d.ActiveTrader) as totalQFTD,
        
        SUM(d.Commission) AS totalComsAll,
 
        SUM(d.TotalMicroPayments) as totalmicroPaymentsCount, 
        SUM(d.MicroPaymentsAmount) as totalmicroPaymentsAmount, 
        SUM(d.Volume) as totalVolume
     
    FROM affiliates a 
    inner join Dashboard d ON a.id=d.AffiliateID AND d.Date>='".$arrRange['from']."' AND d.Date<='".$arrRange['to']."' ".(isset($merchant_id) && !empty($merchant_id) ? ' AND d.MerchantID = ' . $merchant_id : '')."
    WHERE a.valid =1 ".$where."
    group by a.id ORDER BY a.id DESC
) aff1 
left join (
    SELECT c.affiliateID,sum(c.Commission)  as badc
    FROM commissions c 
    inner join traders_tag tg on c.traderID=tg.trader_id AND c.Date>='".$arrRange['from']."' AND c.Date<='".$arrRange['to']."' 
    group by c.affiliateID    
) as bc on aff1.id=bc.affiliateID";



    $qq = function_mysql_query($sql, __FILE__);
    while ($affiliateItem = mysql_fetch_assoc($qq)) {

        
        
        //--- Dsts Row -------------------------------------------------------//
        $affiliateRowConfig = [
            'display_type' => $display_type,
            'isRedTag' => $isRedTag,
            'userlevel' => $userlevel,
            'hideDemoAndLeads' => $hideDemoAndLeads,
            'showCasinoFields' => $showCasinoFields,
            'displayForex' => $displayForex,
            'groupMerchantsPerAffiliate' => $groupMerchantsPerAffiliate,
        ];
        $affiliateRowData = [
            'from' => $arrRange['from'],
            'to' => $arrRange['to'],
            'affiliate_id' => $affiliateItem['id'],
            'affiliate_username' => $affiliateItem['username'],
            'affiliate_firstname' => $affiliateItem['first_name'],
            'affiliate_lastname' => $affiliateItem['last_name'],
            'website' => $affiliateItem['website'],
            'mail' => $affiliateItem['mail'],
            'totalImpressions' => $affiliateItem['totalImpressions'],
            'totalClicks' => $affiliateItem['totalClicks'],
            'totalCPIGroup' => $affiliateItem['totalCPIGroup'],
            'totalRealAccounts' => $affiliateItem['totalRealAccounts'],
            'totalLeadsAccounts' => $affiliateItem['totalLeadsAccounts'],
            'totalDemoAccounts' => $affiliateItem['totalDemoAccounts'],
            'totalFTD' => $affiliateItem['totalFTD'],
            'totalFTDAmount' => $affiliateItem['totalFTDAmount'],
            'totalRealFtd' => $affiliateItem['totalRealFtd'],
            'totalRealFtdAmount' => $affiliateItem['totalRealFtdAmount'],
            'totalmicroPaymentsCount' => $affiliateItem['totalmicroPaymentsCount'],
            'totalmicroPaymentsAmount' => $affiliateItem['totalmicroPaymentsAmount'],
            'totalDeposits' => $affiliateItem['totalDeposits'],
            'totalDepositAmount' => $affiliateItem['totalDepositAmount'],
            'totalBonus' => $affiliateItem['totalBonus'],
            'totalWithdrawal' => $affiliateItem['totalWithdrawal'],
            'totalChargeBack' => $affiliateItem['totalChargeBack'],
            'totalVolume' => $affiliateItem['totalVolume'],
            'totalpnl' => $affiliateItem['totalpnl'],
            'totalNetRevenue' => ($affiliateItem['totalDepositAmount'] - ($affiliateItem['totalBonus'] + $affiliateItem['totalWithdrawal'] + $affiliateItem['totalChargeBack'])),//$affiliateItem['totalNetRevenue'],
            'totalQFTD' => $affiliateItem['totalQFTD'],
            'totalComs' => $affiliateItem['totalComs'],
            'group_id' => $affiliateItem['group_id'],
            
        ];


        

        $listReport .= generateRow($affiliateRowData, $affiliateRowConfig);
        
        // Total
        $totalImpressionsM += $affiliateRowData['totalImpressions'];
        $totalClicksM += $affiliateRowData['totalClicks'];
        $totalCPIM += $affiliateRowData['totalCPIGroup'];
        $totalLeadsAccountsM += $affiliateRowData['totalLeadsAccounts'];
        $totalDemoAccountsM += $affiliateRowData['totalDemoAccounts'];
        $totalRealAccountsM += $affiliateRowData['totalRealAccounts'];
        $totalFTDM += $affiliateRowData['totalFTD'];
        $totalFTDAmountM += $affiliateRowData['totalFTDAmount'];
        $totalRealFtdM += $affiliateRowData['totalRealFtd'];
        $totalRealFtdAmountM += $affiliateRowData['totalRealFtdAmount'];
        $totalDepositsM += $affiliateRowData['totalDeposits'];
        
        $totalDepositAmountM += $affiliateRowData['totalDepositAmount'];
        $totalBonusM += $affiliateRowData['totalBonus'];
        $totalWithdrawalM += $affiliateRowData['totalWithdrawal'];
        $totalChargeBackM += $affiliateRowData['totalChargeBack'];
        $totalVolumeM += $affiliateRowData['totalVolume'];
        $totalSpreadAmountM += 0;
        $totalpnlM += $affiliateRowData['totalpnl'];
        $totalNetRevenueM += $affiliateRowData['totalNetRevenue'];
        $totalQFTDM += $affiliateRowData['totalQFTD'];
        $totalComsM += $affiliateRowData['totalComs'];
        
        $l++;
        
        unset($affiliateRowConfig);
        unset($affiliateRowData);
        //--------------------------------------------------------------------//
    }

    unset($arrRange); // Clear up the memory.
} // End of time-periods loop.



if ($l > 0) {
    $set->sortTableScript = 1;
}

$set->sortTable = 1;
$set->totalRows = isset($groupByAff) && 1 == $groupByAff ? $intAffiliatesCombinedCount : $l;


$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">' . lang('Report Search') . '</div>
		<div style="background: #F8F8F8;">
			<form action="' . $set->SSLprefix . $set->basepage . '" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="affiliate" />
			<input type="hidden" name="search" value="1" />
			<table><tr>
                            <td>' . lang('Period') . '</td>
                            <td width=160>' . lang('Affiliate ID') . '</td>
                            ' . ($userlevel == 'admin' ? '<td style="padding-left:10px">' . lang('Groups') . '</td>' : '') . '
                            <td style="padding-left:10px">' . lang('Merchant') . '</td>
                            <td>' . lang('Show Affiliates') . '</td>
                            <td>' . lang('Search Type') . '</td>'
        . ($intMerchantCount > 1 ? '<td>' . lang('Group Merchants') . '</td> ' : '') .
        '<td></td>
				</tr><tr>
				<td>' . timeFrame((isset($static_from) ? $static_from : $from), $to) . '</td>
				<td><!--input type="text" name="affiliate_id" value="' . $affiliate_id . '" id="affiliate_id" style="width: 100px;" /-->
				<div class="ui-widget">'
        . '<!-- name="affiliate_id" -->'
        . '<select id="combobox" ' . ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') . '>'
        . '<!--option value="">' . lang('Choose Affiliate') . '</option-->'
        . $listOfAffiliates
        . '</select>
								</div>
				</td>
				' . ($userlevel == 'admin' ? '<td width="100" style="padding-left:10px"><select name="group_id" style="width: 100px;"><option value="">' . lang('All Groups') . '</option><option value="0" ' . ($group_id == "0" ? 'selected="selected"' : '') . '>' . lang('General') . '</option>' . listGroups($group_id) . '</select></td>' : '') . '
				<td style="padding-left:10px"><select name="merchant_id" style="width: 150px;"><option value="">' . lang('All Merchants') . '</option>' . listMerchants($merchant_id) . '</select></td>
				<td><select name="showAllRecords" style="width: 150px;"><option id=1 value="1">' . lang('Only Active Affiliates') . '</option><option id=0 value="0" ' . (isset($showAllRecords) && $showAllRecords == 0 ? ' selected ' : '' ) . '>' . lang('All Affiliates') . '</option></select></td>
				<td class="tooltip"><select name="display_type" id="display_type" style="width: 150px;" disabled><option value="0">' . lang('Search Type') . '</option>' . listDisplayTypes($display_type) . '</select><span class="tooltiptext">' . lang("Available only if affiliate is selected.") . '</span></td>'
        . ($intMerchantCount > 1 ? '<!--td><input type="checkbox" id="groupByAff" name="groupByAff" ' . ($groupMerchantsPerAffiliate == 1 ? 'checked' : '') . '/-->
				<td colspan=2><div><label class="switch"><input type="checkbox" id="groupByAff" name="groupByAff"  ' . ($groupMerchantsPerAffiliate == 1 ? 'checked' : '') . ' ><div class="slider round"></div></label></div>
				<input type="hidden" id="groupByAffVal" value="' . ($groupMerchantsPerAffiliate) . '" name="groupByAff" /></td>' : '') .
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
				<td><input type="submit" value="' . lang('View') . '" /></td>
			</tr></table>
			</form>
			
			' . ($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#affiliatesData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\'' . $filename . '\'});" ><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to CSV') . '" title="' . lang('Export to CSV') . '" align="absmiddle" /> <b>' . lang('Export to CSV') . '</b></a></div>' : '') . '
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#affiliatesData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\'' . $filename . '\'});" ><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to XLS') . '" title="' . lang('Export to XLS') . '" align="absmiddle" /> <b>' . lang('Export to XLS') . '</b></a>
				</div>
				' . getFavoritesHTML() . '
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		<!--href="' . $set->uri . (strpos($set->uri, '?') ? '&' : '?') . 'excel=xls"--> 
		<div class="normalTableTitle" class="table">' . lang('Affiliate Report') . '<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="' . $set->SSLprefix . 'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';

$tableStr = '<table class="table ' . (empty($listReport) ? 'normal' : 'tablesorter') . ' mdlReportFields" border="0" cellpadding="0" cellspacing="0"  id="affiliates">
					<thead><tr class="table-row">'
        . (isset($display_type) && !empty($display_type) ? '<th style="padding: 0 80px;">' . lang('Period') . '</th>' : '') . '
						<th class="table-cell">' . lang('Affiliate ID') . '</th>
						<th class="table-cell">' . lang('Username') . '</th>
						<th class="table-cell">' . lang('Full Name') . '</th>
						' . (!empty($campID['title']) ? '<th class="table-cell">' . lang($campID['title']) . '</th>' : '') .
        (!$set->isNetwork ? '<th class="table-cell">' . lang('E-Mail') . '</th>' : '') .
        ($groupMerchantsPerAffiliate == 0 ? '<th class="table-cell">' . lang('Merchant') . '</th>' : '') . '
						' . ($set->ShowIMUserOnAffiliatesList ? '<th class="table-cell">' . lang('IM-User') . '</th>' : '') . '
						<th class="table-cell">' . lang('Website') . '</th>
						<th class="table-cell">' . lang('Impressions') . '</th>
						<th class="table-cell">' . lang('Clicks') . '</th>
						' . ($set->deal_cpi ? '<th class="table-cell">' . lang('Installation') . '</th>' : '') . '
						<th class="table-cell">' . lang('Click Through Ratio (CTR)') . '</th>
						<th class="table-cell">' . lang(ptitle('Click to Account')) . '</th>
						<th  class="table-cell">' . lang(ptitle('Click to Sale')) . '</th>
						<th class="table-cell">EPC</th>' .
        ($hideDemoAndLeads ? '' : '<th class="table-cell">' . lang(ptitle('Lead')) . '</th>
                                                                           <th class="table-cell">' . lang(ptitle('Demo')) . '</th>') .
        '<th class="table-cell">' . lang(ptitle('Accounts')) . '</th>
						' . ($showCasinoFields ? '<th class="table-cell">' . lang(ptitle('Frozens')) . '</th>' : '<th>|FROZEN|</th>') . '
						<th class="table-cell">' . lang('FTD') . '</th>
						<th class="table-cell">' . lang('FTD Amount') . '</th>
						<th class="table-cell">' . lang('RAW FTD') . '</th>
						<th class="table-cell">' . lang('RAW FTD Amount') . '</th>
						' . ($set->showMicroPaymentsOnReports == 1 ? '
						<th  class="table-cell">' . lang('Total MicroPayments') . '</th>
						<th  class="table-cell">' . lang('MicroPayments Amount') . '</th>' : '' ) . ' 
						
						<th class="table-cell">' . lang('Total Deposits') . '</th>
						<th class="table-cell">' . lang('Deposit Amount') . '</th>
						<!--th>' . lang('Trader Value') . '</th-->
						
						<th class="table-cell">' . lang('Bonus Amount') . '</th>
						<th class="table-cell">' . lang('Withdrawal Amount') . '</th>
						<th class="table-cell">' . lang('ChargeBack Amount') . '</th>
						<th class="table-cell">' . lang('Affiliate Risk') . '</th>
						<th class="table-cell">' . lang(ptitle('Volume')) . '</th>
						' . (($displayForex) ? '<th class="table-cell">' . lang('Spread Amount') . '</th>' : '') . '
						' . (($set->deal_pnl) ? '<th class="table-cell">' . lang('PNL') . '</th>' : '') . '
						<th class="table-cell">' . lang(ptitle('Net Revenue')) . '</th>
						<th class="table-cell">' . lang(ptitle('Active Traders')) . '</th>
						<th class="table-cell">' . lang('Commission') . '</th>
						<th class="table-cell">' . lang('Group') . '</th>
					</tr>
                                        </thead>
                                        <tfoot>
                                            <tr class="table-row">
						<th><b>' . lang('Total') . ':</b></th>'
        . (isset($display_type) && !empty($display_type) ? '<th></th>' : '') . '
						<th></th>
						<th></th>
						<th></th>
						' . (!empty($campID['title']) ? '<th></th>' : '') . '
                                                <th></th>
                                                ' . ($groupMerchantsPerAffiliate == 0 ? '<th></th>' : '') . '
                                                ' . ($set->ShowIMUserOnAffiliatesList ? '<th></th>' : '') . '
						<th><a href="/' . $userlevel . '/reports.php?act=clicks&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '">' . $totalImpressionsM . '</a></th>
						<th><a href="/' . $userlevel . '/reports.php?act=clicks&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '">' . $totalClicksM . '</a></th>
						' . ($set->deal_cpi ? '<th>' . @number_format($totalCPIM) . '</th>' : '') . '
						<th>' . @number_format((div($totalClicksM, $totalImpressionsM)) * 100, 2) . ' %</th>
						<th>' . @number_format((div($totalRealAccountsM, $totalClicksM)) * 100, 2) . ' %</th>
						<th>' . @number_format((div($totalFTDM, $totalClicksM)) * 100, 2) . ' %</th>
						<th>' . @price(div($totalComsM, $totalClicksM)) . '</th>' .
        ($hideDemoAndLeads ? '' : '<th><a href="/' . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=lead">' . $totalLeadsAccountsM . '</a></th>
						<th><a href="/' . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=demo">' . $totalDemoAccountsM . '</a></th>') .
        '<th><a href="/' . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=real">' . $totalRealAccountsM . '</a></th>
						' . ($showCasinoFields ? '<th>' . $totalFrozensM . '</th>' : '<th>|FROZEN|</th>') . '
						<th><a href="/' . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=ftd">' . $totalFTDM . '</a></th>
						
						<th>' . price($totalFTDAmountM) . '</th>
						<th><a href="/' . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=ftd">' . $totalRealFtdM . '</a></th>
						
						<th>' . price($totalRealFtdAmountM) . '</th>
						
						' . ($set->showMicroPaymentsOnReports == 1 ? '
						<th></th>
						<th></th>
						' : '' ) . '
						
						<th><a href="/' . $userlevel . '/reports.php?act=transactions&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=deposit">' . $totalDepositsM . '</a></th>
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
						<th>' . number_format($totalQFTDM, 0) . '</th>
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
						$.prompt("<label>' . lang("Provide name for report") . ': <br/><input type=\'text\' name=\'report_name\' value=\'\' style=\'width:80wh\' required></label><div class=\'err_message\' style=\'color:red\'></div>", {
								top:200,
								title: "' . lang('Add to Favorites') . '",
								buttons: { "' . lang('Yes') . '": true, "' . lang('Cancel') . '": false },
								submit: function(e,v,m,f){
									if(v){
										name = $("[name=report_name]").val();
										if(name != ""){
											
											url = window.location.href;
											user = "' . $set->userInfo['id'] . '";
											level = "' . $userlevel . '";
											type = "add";
											
											saveReportToMyFav(name, \'affiliates\',user,level,type);
										}
										else{
											$(".err_message").html("' . lang("Enter Report name.") . '");
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

function generateRow($data, $config) {

    global $set, $listGroups;

    $listReport .= '<tr>';

    if (isset($config['display_type']) && !empty($config['display_type'])) {
        $listReport .= '<td>' . $data['from'] . ' - ' . $data['to'] . '</td>';
    }
    $affiliate_id_row = "<td " . (false && $config['isRedTag'] ? ' style="color:red;" ' : '' ) . "   >" . $data['affiliate_id'] . '</td>';

    $listReport .= $affiliate_id_row . '
						<td><a href="/' . $config['userlevel'] . '/affiliates' . $ver . '.php?act=new&id=' . $data['affiliate_id'] . '" target="_blank">' . $data['affiliate_username'] . '</a></td>
						<td>' . $data['affiliate_firstname'] . ' ' . $data['affiliate_lastname'] . '</td>';

    if (!empty($data['campId_title'])) {
        if ($data['affiliate_campID']) {
            $listReport .= '<td align="left">' . $data['affiliate_campID'] . '</td>';
        } else {
            $listReport .= '<td align="left"></td>';
        }
    }



    $showWebsite = !empty($data['website']) && $data['website'] != 'http://' && $data['website'] != 'http://www.' ? true : false;

    $listReport .= (!$set->isNetwork ? '<td><a href="mailto:' . $data['mail'] . '">' . $data['mail'] . '</a></td>' : '') . '
								' . ($config['groupMerchantsPerAffiliate'] == 0 ? '<td>' . $data['merchant_name'] . '</td>' : '') . '
                                ' . ($set->ShowIMUserOnAffiliatesList ? '<td align="left">' . $data['IMUser'] . '</td>' : '') . '
								<td>' . ($showWebsite ? '<a href="/out.php?refe=' . urlencode($data['website']) . '" target="_blank">' . $data['website'] . '</a>' : '') . '</td>
                                <td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=clicks&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id=' . $data['affiliate_id'] . '">' . @number_format($data['totalImpressions'], 0) . '</a></td>
                                <td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=clicks&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&affiliate_id=' . $ww['affiliate_id'] . '">' . @number_format($data['totalClicks'], 0) . '</a></td>
                                ' . ($set->deal_cpi ? '<td style="text-align: center;">' . @number_format($data['totalCPIGroup']) . '</td>' : '') . '
                                <td style="text-align: center;">' . @number_format((div($data['totalClicks'], $data['totalImpressions'])) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format((div($data['totalRealAccounts'], $data['totalClicks'])) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @number_format((div($data['totalFTD'], $data['totalClicks'])) * 100, 2) . ' %</td>
                                <td style="text-align: center;">' . @price(div($data['totalComs'], $data['totalClicks'])) . '</td>' .
            ($config['hideDemoAndLeads'] ? '' : '<td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id=' . $data['affiliate_id'] . '&type=lead">' . $data['totalLeadsAccounts'] . '</a></td>
                                <td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id=' . $data['affiliate_id'] . '&type=demo">' . $data['totalDemoAccounts'] . '</a></td>') .
            '<td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id=' . $data['affiliate_id'] . '&type=real">' . $data['totalRealAccounts'] . '</a></td>
                                ' . ($config['showCasinoFields'] ? '<td style="text-align: center;">' . $totalFrozens . '</td>' : '<td>|FROZEN|</td>') . '
                                <td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id=' . $data['affiliate_id'] . '&type=ftd">' . $data['totalFTD'] . '</a></td>
                                <td style="text-align: center;">' . price($data['totalFTDAmount']) . '</td>
                                <td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id=' . $data['affiliate_id'] . '&type=totalftd">' . $data['totalRealFtd'] . '</a></td>
                                <td style="text-align: center;">' . price($data['totalRealFtdAmount']) . '</td>
								' . ($set->showMicroPaymentsOnReports == 1 ? '
                                <td style="text-align: center;">' . (int)($data['totalmicroPaymentsCount']) . '</td>
                                <td style="text-align: center;">' . price($data['totalmicroPaymentsAmount']) . '</td> ' : '' ) . '
								
								<td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=transactions&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id=' . $data['affiliate_id'] . '&type=deposit">' . $data['totalDeposits'] . '</a></td>
                                <td style="text-align: center;">' . price($data['totalDepositAmount']) . '</td>
                                <!--td style="text-align: center;">' . ($data['totalDeposits'] > 0 ? price($data['totalDepositAmount'] / $data['totalDeposits']) : 0) . '</td-->
                                <td style="text-align: center;">' . price($data['totalBonus']) . '</td>
                                <td style="text-align: center;">' . price($data['totalWithdrawal']) . '</td>
                                <td style="text-align: center;">' . price($data['totalChargeBack']) . '</td>
                                <td style="text-align: center;">' . @number_format((div($data['totalFruad'], $data['totalFTD'])) * 100, 2) . '%</td>
                                <td style="text-align: center;">' . price($data['totalVolume']) . '</td>
                                ' . (($config['displayForex']) ? '<td style="text-align: center;">' . price($data['totalSpreadAmount']) . '</td>' : '') . '
                                ' . (($set->deal_pnl) ? '<td style="text-align: center;">' . price($data['totalpnl']) . '</td>' : '') . '
                                <td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=transactions&type=volume&from=' . $data['from'] . '&to=' . $data['to'] . '&merchant_id=' . $data['merchant_id'] . '">' . price($data['totalNetRevenue']) . '</a></td>
                                <td style="text-align: center;"><a href="/' . $config['userlevel'] . '/reports.php?act=trader&from=' . $data['from'] . '&to=' . $data['to'] . '&affiliate_id=' . $data['affiliate_id'] . '&type=activeTrader">' . number_format($data['totalQFTD'], 0) . '</a></td>
								<td style="text-align: center;">' . price($data['totalComs']) . '</td>
                                <td style="text-align: center;">' . $listGroups[$data['group_id']] . '</td>
                            </tr>';
    return $listReport;
}

?>