<?php

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];

if (!defined('DirectBrowse')) {
    $path = "http" . $set->SSLswitch . "://" . $_SERVER[HTTP_HOST];
    header("Location: " . $path . "/" . $userlevel);
}

if ($userlevel == "manager") {
    $globalWhere = "group_id = '" . $set->userInfo['group_id'] . "' AND ";
// die ('greger');
} else{
    $globalWhere = '';
}




$pageTitle = lang(ptitle('Trader Report'));
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


$set->content .= '<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/jquery.base64.js"></script>
		<script type="text/javascript" src="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.css"/>              
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
		</style>';
$filename = "Trader_data_" . date('YmdHis');

if ($userlevel == 'manager') {
    $group_id = $set->userInfo['group_id'];
}


$arrAllTraders = [];
$merchant_id = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;

/*
// List of wallets.
$arrWallets = [];
$sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
$resourceWallets = function_mysql_query($sql, __FILE__);
while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
    $arrWallets[$arrWallet['wallet_id']] = false;
    unset($arrWallet);
}
*/

$acountries = getLongCountries('sales');

$loopedMerchant_ID = $merchant_id;


// CREATE QUERY ------------------//
$where = '';

    if ($affiliate_id){
        $where .= " AND rt.AffiliateID='" . $affiliate_id . "' ";
    }
    if ($group_id){
        $where .= " AND aff.group_id='" . $group_id . "' ";
    }
    if ($banner_id){
        $where .= " AND rt.CreativeID='" . $banner_id . "' ";
    }

    if ($trader_id){
        $where .= " AND rt.TraderID='" . $trader_id . "' ";
    }
    if ($param) {
        $param = trim($param);
        $where .= " AND rt.Param='" . $param . "' ";
    }

    if ($param2){
        $where .= " AND rt.Param2='" . $param2 . "' ";
    }
    
    if ($email && $set->ShowEmailsOnTraderReportForAdmin) {
        $email = trim($email);
        $where .= " AND lower(rt.Email) like '%" . strtolower($email) . "%' ";
    }
    
    if ($trader_alias && $set->ShowEmailsOnTraderReportForAdmin) {
        $trader_alias = trim($trader_alias);
        $where .= " AND lower(trader_alias) like '%" . mysql_real_escape_string(strtolower($trader_alias)) . "%' ";
    }
    
    if ($country_id) {
        $where .= " AND rt.Country='" . $country_id . "' ";
    }

    
    if ($type == 'real') {
        $where .= " AND rt.TraderStatus='real' ";
    } else if ($type == 'lead') {
        $where .= " AND rt.TraderStatus='lead' ";
    } else if ($type == 'demo') {
        $where .= " AND rt.TraderStatus='demo' ";
    } else if ($type == 'frozen') {
        $where .= " AND rt.TraderStatus='frozen' ";
    } else if ($type == 'ftd' || $type == 'totalftd') {
        $where .= " and (rt.FirstDeposit BETWEEN '" . $from . "' and '" . $to . "'  ) AND rt.TraderStatus<>'frozen' and rt.TraderStatus<>'demo' ";
    } else if ($type == 'activeTrader') {
        $where .= " AND (rt.QualificationDate BETWEEN '" . $from . "' and '" . $to . "'  ) AND rt.TraderStatus<>'frozen' AND rt.TraderStatus<>'demo' ";
    }
    
    
    
    
if($type == 'ftd' || $type == 'totalftd'){
    $tradersQuery = "SELECT rt.*, aff.group_id as GroupID FROM ReportTraders rt INNER JOIN affiliates aff ON rt.AffiliateID = aff.id WHERE 1=1 ".$where." ORDER BY RegistrationDate DESC";
}else{
    $tradersQuery = "SELECT rt.*, aff.group_id as GroupID FROM ReportTraders rt INNER JOIN affiliates aff ON rt.AffiliateID = aff.id WHERE RegistrationDate BETWEEN '".$from."' AND '".$to."' ".$where." ORDER BY RegistrationDate DESC";
}


echo "<!-- ".$tradersQuery." -->";

// -------------------------------//


$tradersData = function_mysql_query($tradersQuery, __FILE__);
while ($traderItem = mysql_fetch_assoc($tradersData)) {


    $ftd = $totalTraders = $depositAmount = $total_deposits = $microPaymentsCount = $microPaymentsAmount = $ftdAmount = $volumeAmount = 0;
    $totalCom = $bonusAmount = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
    $spreadAmount = $pnl = 0;
    $ftdUsers = '';


        $arrRes = $traderInfo;
        $total_deposits = 0;
        $microPaymentsCount = 0;
        $microPaymentsAmount = 0;
        $totalLots = 0;
        $totalPNL = 0;


        $ClickFrom = changeDate($traderItem['Date'], -4);
        $ClickTo = changeDate($traderItem['Date'], +4);

        $listReport .= '<tr>';
        
        $listReport .= '<td>' . $traderItem['TraderID'] . '</td>';
        
        if ($set->showCampaignOnTraderReport) {
            $listReport .= '<td>' . $traderItem['CampaignID'] . '</td>';
        }
        if ($displayForex == 1){
            //$listReport .= ($arrRes['sub_trader_count'] > 0 ? '<td><a href="/' . $userlevel . '/reports.php?act=subtraders&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . $loopedMerchant_ID . '&trader_id=' . $arrRes['trader_id'] . '">' . $arrRes['sub_trader_count'] . '</a></td>' : '<td/>');
            $listReport .= '<td></td>';
        }


        $ftdAmount = $traderItem['FTDAmount'];
        $depositAmount = $traderItem['DepositAmount'];


        $listReport .= '<td>' . $traderItem['TraderAlias'] . '</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>' . $traderItem['Email'] . '</td>' : '' ) . '
                            ' . ($set->ShowPhonesOnTraderReportForAdmin ? '<td>' . $traderItem['Phone'] . '</td>' : '' ) . '
                            <td title="' . (date("d/m/Y H:i:s", strtotime($traderItem['RegistrationDate']))) . '">' . (date("d/m/Y H:i:s", strtotime($traderItem['RegistrationDate']))) . '</td>
                            <td><span style="color: ' . (($traderItem['TraderStatus'] == 'real')?'green':'black') . ';">' . $traderItem['TraderStatus'] . '</span></td>
                            <td>' . $acountries[$traderItem['Country']] . '</td>
                            ' . ($set->showDynamicFilters == 1 ?'<td></td>' : '' ) . '
                            <td>' . $traderItem['AffiliateID'] . '</td>
                            <td><a href="/' . $userlevel . '/affiliates.php?act=new&id=' . $traderItem['AffiliateID'] . '" target="_blank">' . $traderItem['AffiliateUsername'] . '</a></td>
                            ' . ($set->showProductsPlace == 1 ? '<td></td><td></td>' : '' )                
                            . '<td>' . $traderItem['MerchantID'] . '</td>
                            <td>' . strtoupper($traderItem['MerchantName']) . '</td>
                            <td style="text-align: left;">' . $traderItem['CreativeID'] . '</td>
                            <td style="text-align: left;">' . $traderItem['CreativeName'] . '</td>
                            <td>' . $traderItem['Type'] . '</td>
                            <td>' . $traderItem['CreativeLanguage'] . '</td>
                            <td>' . $traderItem['ProfileID'] . '</td>
                            <td>' . $traderItem['ProfileName'] . '</td>
                            ' . ($isCasino == 1 ? ( empty($traderItem['status']) ? "<td></td>" : "<td></td>") : '') . '
                            <td>' . $traderItem['Param'] . '</td>
                            <td>' . $traderItem['Param2'] . '</td>
                            <td>' . $traderItem['Param3'] . '</td>
                            <td>' . $traderItem['Param4'] . '</td>
                            <td>' . $traderItem['Param5'] . '</td>
                            <td>' . $traderItem['TransactionID'] . '</td>
                            <td title="' . (empty($traderItem['TransactionID']) ? "" : date("d/m/Y H:i:s", strtotime($traderItem['FirstDeposit'])) ) . '">' . (!empty($traderItem['TransactionID']) ? date("d/m/Y H:i:s", strtotime($traderItem['FirstDeposit'])) : "") . '</td>
                            <td>' . (!empty($traderItem['TransactionID']) ? price($ftdAmount) : "" ) . '</td>
                            <td>' . ($traderItem['SelfDeposit'] == 1 ? lang('Yes') : "" ) . '</td>
                            ' . ($set->ShowNextDepositsColumn == 1 ? '
                                    <td>' . $traderItem['TotalNextDeposits'] . '</td>
                                    <td>' . $traderItem['NextDeposits'] . '</td>
                            ' : '' ) . '

                            ' . ($set->showMicroPaymentsOnReports == 1 ? '
                                <td>' . $traderItem['TotalMicroPayments'] . '</td>
                                <td>' . $traderItem['MicroPaymentsAmount'] . '</td>
                            ' : '' ) . '
							
                            <td><a href="/' . $userlevel . '/reports.php?act=transactions&from=' . date("d/m/Y", strtotime("-3 Years")) . '&to=' . date("d/m/Y") . '&merchant_id=' . $traderItem['MerchantID'] . '&trader_id=' . $traderItem['TraderID'] . '&type=deposit">' . $traderItem['TotalDeposits'] . '</a></td>
                            <td>' . price($traderItem['DepositAmount']) . '</td>
                            <td>' . price($traderItem['Volume']) . '</td>
                            <td>' . price($traderItem['BonusAmount']) . '</td>
                            <td>' . price($traderItem['WithdrawalAmount']) . '</td>
                            <td>' . price($traderItem['ChargeBackAmount']) . '</td>
                            <td>' . price($traderItem['NetDeposit']) . '</td>
                            <td>' . $traderItem['Trades'] . '</td>
                            ' . ( $displayForex == 1 ?'<td></td>' : '' ) . '
                            <td>' . $traderItem['QualificationDate'] . '</td>
                            ' . ($set->deal_pnl == 1 ?'<td id="pnl">' . price($traderItem['PNL']) . '</td>' : '') . '
                            <td>' . $traderItem['SaleStatus'] . '</td>
                            ' . ($set->displayLastMessageFieldsOnReports == 1 ? '<td></td><td></td>' : '') . '
                            <td>' . $traderItem['LastTimeActive'] . '</td>
                            <td>' . price($traderItem['Commission']). '</td>
                            <td>' . $traderItem['AdminNotes'] . '</td>
                            ' . ((!empty($traderItem['ClickDetails']) && $traderItem['ClickDetails'] != '') ? '<td><a title="deTails" href="/' . $userlevel . '/reports.php?act=clicks&from=' . $ClickFrom . '&to=' . $ClickTo . '&merchant_id=' . $traderItem['MerchantID'] . '&unique_id=' . $traderItem['ClickDetails'] . '">' . lang('View') . '</a></td>' : '<td></td>') . '
                        </tr>';




        
        $totalTotalCom += floatval($traderItem['Commission']);
        $totalFTD += floatval($ftdAmount);
        $totalNetRevenue += floatval($traderItem['NetDeposit']);

        

        $totalDepositAmount += floatval($traderItem['DepositAmount']);
        $totalVolumeAmount += floatval($traderItem['Volume']);
        $totalBonusAmount += floatval($traderItem['BonusAmount']);
        $totalTotalDeposit += intval($traderItem['TotalDeposits']);
        $totalmicroPaymentsCount += intval($traderItem['TotalMicroPayments']);
        $totalmicroPaymentsAmount += floatval($traderItem['MicroPaymentsAmount']);
        $totalTrades += intval($traderItem['Trades']);
        $totalLotsamount += $totalLots;
        $totalPNLamount += floatval($traderItem['PNL']);
        $totalWithdrawalAmount += floatval($traderItem['WithdrawalAmount']);
        $totalChargeBackAmount += floatval($traderItem['ChargeBackAmount']);


        $l++;

        $volumeAmount = $netRevenue = $chargebackAmount = $withdrawalAmount = $depositAmount = $bonusAmount = $total_deposits = $microPaymentsCount = $microPaymentsAmount = $totalTraders = $totalLots = 0;
    
}


if ($l > 0) {
    $set->sortTableScript = 1;
}

$set->sortTable = 1;
$set->totalRows = $l;

$set->content .= '<div class="normalTableTitle" style="width: 100%;">' . lang('Report Search') . '</div>
			<div style="background: #F8F8F8;">
				<form id="frmRepo" action="' . $set->SSLprefix . $set->basepage . '" method="get" onsubmit = "return submitReportsForm(this)">
				<input type="hidden" name="act" value="trader_test" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>' . lang('Period') . '</td>
						<td>' . lang('Merchant') . '</td>
						<td>' . lang('Country') . '</td>
						<td width=160>' . lang('Affiliate ID') . '</td>
						<td style="padding-left:20px">' . lang('Banner ID') . '</td>
						<td>' . lang(ptitle('Trader ID')) . '</td>
						<td>' . lang(ptitle('Trader Alias')) . '</td>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>' . lang(ptitle('Email')) . '</td>' : '') . '
						' . ($set->ShowPhonesOnTraderReportForAdmin ? '<td>' . lang(ptitle('Phone')) . '</td>' : '') . '
						<td>' . lang(ptitle('Parameter')) . '</td>
						<td>' . lang(ptitle('Parameter2')) . '</td>
						' . ($userlevel == 'admin' ? '<td>' . lang('Group') . '</td>' : '') . '
					' .
        ($set->showDynamicFilters == 1 ? '<td>' . lang($set->dynamicFilterTitle) . '</td>' : '') . '
						<td>' . lang('Filter') . '</td>
						<td></td>
					</tr><tr>
						<td>' . timeFrame($from, $to) . '</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">' . lang('All Merchants') . '</option>' . listMerchants($merchant_id) . '</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">' . lang('All') . '</option>' . getCountries($country_id) . '</select></td>
						<td style="padding-right:20px"><!--input type="text" name="affiliate_id" value="' . $affiliate_id . '" id="fieldClear" style="width: 60px; text-align: center;" /-->
						<div class="ui-widget">'
        . '<!-- name="affiliate_id" -->'
        . '<select id="combobox" ' . ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') . '>'
        . '<!--option value="">' . lang('Choose Affiliate') . '</option-->'
        . $listOfAffiliates
        . '</select>
								</div>
						</td>
						<td style="padding-left:20px"><input type="text" name="banner_id" value="' . $banner_id . '" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="trader_id" value="' . $trader_id . '" id="fieldClear" style="width: 60px; text-align: center;" onblur="validateMerchant(this)" /></td-->
						<td><input type="text" name="trader_id" value="' . $trader_id . '" id="fieldClear" style="width: 60px; text-align: center;"  /></td>
						<td><input type="text" name="trader_alias" value="' . $trader_alias . '" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td><input type="text" name="email" value="' . $email . '" id="fieldClear" style="width: 60px; text-align: center;" /></td>' : '') . '
						<td><input type="text" name="param" value="' . $param . '" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="' . $param2 . '" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="group_id" value="' . $group_id . '" id="fieldClear" style="width: 60px; text-align: center;" /></td-->
						' . ($userlevel == 'admin' ? '
						<td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">' . lang('All Groups') . '</option>'
        . '<option value="0" ' . ($group_id == "0" ? 'selected="selected"' : '') . '>' . lang('General') . '</option>'
        . listGroups($group_id)
        . '</select>
                                                </td>' : '') . '
												
						' . ($set->showDynamicFilters == 1 ?
        '<td>
                                                    <select name="dynamic_filter" style="width: 100px;">
                                                        <option value="">' . lang('All ' . $set->dynamicFilterTitle) . '</option>'
        . listDynamicFilters($dynamic_filter, 1, true)
        . '</select>
                                                </td>' : '') . '
						<td>
							<select name="type" style="width: 110px;">
								<option value="allaccounts" ' . ($type == "allaccounts" ? 'selected' : '') . '>' . lang(ptitle('All Accounts')) . '</option>
								<option value="real" ' . ($type == "real" ? 'selected' : '') . '>' . lang(ptitle('Accounts')) . '</option>
								' . ($hideDemoAndLeads ? "" : '<option value="lead" ' . ($type == "lead" ? 'selected' : '') . '>' . lang(ptitle('Lead')) . '</option>
								<option value="demo" ' . ($type == "demo" ? 'selected' : '') . '>' . lang(ptitle('Demo')) . '</option>') . '
								<!--option value="deposit" ' . ($type == "deposit" ? 'selected' : '') . '>' . lang('Deposits') . '</option-->
								<option value="ftd" ' . ($type == "ftd" ? 'selected' : '') . '>' . lang('FTD') . '</option>
								<!--option value="revenue" ' . ($type == "revenue" ? 'selected' : '') . '>' . lang('Revenue') . '</option-->
                                                                <option value="totalftd" ' . ($type == "totalftd" ? 'selected' : '') . '>' . lang('RAW FTD') . '</option>
                                                                <option value="activeTrader" ' . ($type == "activeTrader" ? 'selected' : '') . '>' . lang('Active Trader') . '</option>
                                                                ' . (!$hideDemoAndLeads ? "" : '<option value="frozen" ' . ($type == "frozen" ? 'selected' : '') . '>' . lang('Frozen') . '</option>') . '
							</select>
						</td>
						<!--td><input type="button" value="' . lang('View') . '" onClick="validateForm()"/></td-->
						<td><input type="submit" value="' . lang('View') . '" /></td>
						
					</tr>
				</table>
				</form>
' . ($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\'' . $filename . '\'});"><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to CSV') . '" title="' . lang('Export to CSV') . '" align="absmiddle" /> <b>' . lang('Export to CSV') . '</b></a></div>' : '') . '
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\'' . $filename . '\'});"><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to XLS') . '" title="' . lang('Export to XLS') . '" align="absmiddle" /> <b>' . lang('Export to XLS') . '</b></a>
				</div>
				' . getFavoritesHTML() . '
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle"   class="table">' . lang('Report Results') . '<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="../images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
//table 2600
$tableStr = '<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="traderTbl">
					<thead><tr   class="table-row">
						<th  class="table-cell">' . lang(ptitle('Trader ID')) . '</th>'
        . ($set->showCampaignOnTraderReport ?
        '<th  class="table-cell">' . lang('Campaign Id') . '</th>' : '')
        . ( $displayForex == 1 ? '
						<th  class="table-cell">' . lang(ptitle('Sub Traders')) . '</th>' : '' ) . '
						<th  class="table-cell">' . lang(ptitle('Trader Alias')) . '</th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th  class="table-cell">' . lang(ptitle('Email')) . '</th>' : '' ) . '
						' . ($set->ShowPhonesOnTraderReportForAdmin ? '<th  class="table-cell">' . lang(ptitle('Phone')) . '</th>' : '' ) . '
						<th  class="table-cell">' . lang('Registration Date') . '</th>
						<th  class="table-cell">' . lang(ptitle('Trader Status')) . '</th>
						<th  class="table-cell">' . lang('Country') . '</th>
						' . ($set->showDynamicFilters == 1 ?
        '<th  class="table-cell">' . lang($set->dynamicFilterTitle) . '</th>' : '') . '
						
						<th  class="table-cell">' . lang('Affiliate ID') . '</th>
						<th  class="table-cell">' . lang('Affiliate Username') . '</th>
						' . ($set->showProductsPlace == 1 ? '
						<th  class="table-cell">' . lang('Product ID') . '</th>
						<th  class="table-cell">' . lang('Product Name') . '</th>' : '' ) . '
						<th  class="table-cell">' . lang('Merchant ID') . '</th>
						<th  class="table-cell">' . lang('Merchant Name') . '</th>
						<th   class="table-cell"style="text-align: left;">' . lang('Creative ID') . '</th>
						<th   class="table-cell"style="text-align: left;">' . lang('Creative Name') . '</th>
						<th  class="table-cell">' . lang('Type') . '</th>
						<th  class="table-cell">' . lang('Creative Language') . '</th>
						<th  class="table-cell">' . lang('Profile ID') . '</th>
						<th  class="table-cell">' . lang('Profile Name') . '</th>
						' . ($isCasino == 1 ? '<th  class="table-cell">' . lang('Status') . '</th>' : '' ) . '
						<th  class="table-cell">' . lang('Param') . '</th>
						<th  class="table-cell">' . lang('Param2') . '</th>
						<th  class="table-cell">' . lang('Param3') . '</th>
						<th  class="table-cell">' . lang('Param4') . '</th>
						<th  class="table-cell">' . lang('Param5') . '</th>
                        <th  class="table-cell">' . lang('Transaction ID') . '</th>
						<th  class="table-cell">' . ($type == "deposit" ? lang('Deposit Date') : lang('First Deposit')) . '</th>
						<th  class="table-cell">' . lang('FTD Amount') . '</th>
						<th  class="table-cell">' . lang('Self Deposit') . '</th>
						' .
        ($set->ShowNextDepositsColumn == 1 ? '
						<th  class="table-cell">' . lang('Total Next Deposits') . '</th>
						<th  class="table-cell">' . lang('Next Deposits') . '</th>' : '' ) . ' ' .
        ($set->showMicroPaymentsOnReports == 1 ? '
						<th  class="table-cell">' . lang('Total MicroPayments') . '</th>
						<th  class="table-cell">' . lang('MicroPayments Amount') . '</th>' : '' ) . ' 
						<th  class="table-cell">' . lang('Total Deposits') . '</th>
						<th  class="table-cell">' . ($type == "deposit" ? lang('Deposit Amount') : lang('Deposit Amount')) . '</th>
						<th  class="table-cell">' . lang('Volume') . '</th>
						<th  class="table-cell">' . lang('Bonus Amount') . '</th>
						<th  class="table-cell">' . lang('Withdrawal Amount') . '</th>
						<th  class="table-cell">' . lang('ChargeBack Amount') . '</th>
						<th  class="table-cell">' . lang(ptitle('Net Deposit')) . '</th>
						<th  class="table-cell">' . lang(ptitle('Trades')) . '</th> '
        . ($displayForex == 1 ?
        '<th  class="table-cell">' . lang(ptitle('Lifetime Lots')) . '</th>' : '' ) . '
						<th  class="table-cell">' . lang('Qualification Date') . '</th>
						' . ($set->deal_pnl == 1 ?
        '<th  class="table-cell">' . lang(ptitle('PNL')) . '</th>' : '' ) . '
						<th  class="table-cell">' . lang('Sale Status') . '</th>
						' . ($set->displayLastMessageFieldsOnReports == 1 ? '
						<th  class="table-cell">' . lang('Last Sale Note Date') . '</th>
						<th  class="table-cell">' . lang('Last Sale Note') . '</th>' : '' ) . '
						<th  class="table-cell">' . lang('Last Time Active') . '</th>
						<th  class="table-cell">' . lang('Commission') . '</th>
						<th  class="table-cell">' . lang('Admin Notes') . '</th>
						<th  class="table-cell">' . lang('Click Details') . '</th>
					</tr></thead>
					<tfoot>
						<th></th>'
        . ($set->showCampaignOnTraderReport ?
        '<th></th>' : '')
        . ( $displayForex == 1 ? '
						<th></th>' : '') . '
						<th></th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th></th>' : '' ) . '
						' . ($set->ShowPhonesOnTraderReportForAdmin ? '<th></th>' : '' ) . '
						<th></th>
							' . ($set->showDynamicFilters == 1 ?
        '<th></th>' : '') . '
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						' . ($set->showProductsPlace == 1 ? '
						<th></th>
						<th></th>' : '' ) . '
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						' . ($isCasino == 1 ? '<th></th>' : '' ) . '
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						
						<th style="text-align: left;">' . price($totalFTD) . '</th>
						<th></th>
						' . ($set->ShowNextDepositsColumn == 1 ? '
						<th></th>
						<th></th>
						' : '' ) . '
						' . ($set->showMicroPaymentsOnReports == 1 ? '
						<th>' . $totalmicroPaymentsCount . '</th>
						<th></th>
						' : '' ) . '
						<th style="text-align: left;">' . $totalTotalDeposit . '</th>
						<th style="text-align: left;">' . price($totalDepositAmount) . '</th>
						<th style="text-align: left;">' . price($totalVolumeAmount) . '</th>
						<th style="text-align: left;">' . price($totalBonusAmount) . '</th>
						<th style="text-align: left;">' . price($totalWithdrawalAmount) . '</th>
						<th style="text-align: left;">' . price($totalChargeBackAmount) . '</th>
						<th style="text-align: left;">' . price($totalNetRevenue) . '</th>
						<th style="text-align: left;">' . $totalTrades . '</th>
						' . ($displayForex == 1 ?
        '<th style="text-align: left;">' . $totalLotsamount . '</th>' : '' ) .
        '
						<th></th>
						' . ($set->deal_pnl == 1 ?
        '
						
						<th style="text-align: left;">' . price($totalPNLamount) . '</th>
						' : '' ) . '
						<th></th>
						' . ($set->displayLastMessageFieldsOnReports == 1 ? '
						<th></th>
						<th></th>' : '' ) . '
						<th></th>
						<th style="text-align: left;">' . price($totalTotalCom) . '</th>
						<th></th>
						<th></th>
					</tfoot>
					<tbody>
					' . $listReport . '
				</table>
				';
$tableStr .= getSingleSelectedMerchant();
$tableStr .= '<script>
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
											
											saveReportToMyFav(name, \'Trader\',user,level,type);
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
					
					$("input[name=trader_id]").on("keyup",function(){
						if($(this).val()!=""){
							$("#date_from").val("' . date("Y/m/d", strtotime('-100 year')) . '");
							$("#date_to").val("' . date("Y/m/d", strtotime('+100 year')) . '");
						}
						else{
							$("#date_from").val("' . date("Y/m/d") . '");
							$("#date_to").val("' . date("Y/m/d") . '");
						}
					});
					
					
					
					
				});
				</script>
				';

$set->content .= $tableStr . '
			</div>' . getPager();

//MODAL
$myReport = lang("Trader");
include "common/ReportFieldsModal.php";


theme();
?>