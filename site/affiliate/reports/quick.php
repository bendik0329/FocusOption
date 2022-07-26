<?php

if (!defined('DirectBrowse')) {
    $path = "http" . $set->SSLswitch . "://" . $_SERVER[HTTP_HOST];
    header("Location: " . $path . "/affiliate");
}


$userlevel = "affiliate";

$affiliate_id = $set->userInfo['id'];


$globalWhere = " tb1.affiliate_id = " . $set->userInfo['id'] . " and ";
$pageTitle = lang('Quick Summary Report');
$set->breadcrumb_title = lang($pageTitle);
$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="' . $set->SSLprefix . 'affiliate/">' . lang('Dashboard') . '</a></li>
				<li><a href="' . $set->SSLprefix . $set->uri . '">Reports - ' . lang($pageTitle) . '</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
$isCasinoOrSportBets = false;
// $set->hideNetRevenueForNonRevDeals,  $set->hideFTDamountForCPADeals


if (strpos($set->reportsToHide, 'quick') > 0) {
    _goto('/affiliate/');
}
$earliestTimeForNetRev = date('Y-m-d H:i:s');

// List of wallets.
$arrWallets = array();
$sql = "SELECT DISTINCT wallet_id AS wallet_id ,id FROM merchants;";
$resourceWallets = function_mysql_query($sql, __FILE__);

while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
    $arrWallets[$arrWallet['wallet_id']] = false;
    unset($arrWallet);
}

$set->content .= '<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/jquery.base64.js"></script>';
$filename = "QuickSummary_data_" . date('YmdHis');


$merchants_sql_all = function_mysql_query("select *,id as merchant_id from merchants",__FILE__,__FUNCTION__);
while ($row = mysql_fetch_assoc($merchants_sql_all)) {
        $merchants_array[$row['id']] = $row;
}

/**********************************************/

$totalImpressions = 0;
$totalClicks = 0;
$totalCPIM = 0;
$totalLeadsAccounts = 0;
$totalDemoAccounts = 0;
$totalRealAccounts = 0;
$totalFTD = 0;
$totalFTDAmount = 0;
$totalRealFtd = 0;
$totalRealFtdAmount = 0;
$totalDeposits = 0;
$totalDepositAmount = 0;
$totalVolume = 0;
$totalBonus = 0;
$totalWithdrawal = 0;
$totalChargeback = 0;
$totalNetRevenue = 0;
$totalFooterPNL = 0;
$totalActiveTraders = 0;
$totalComs = 0;




switch ($display_type) {
    case 'monthly':
        $dasboardSQLperiod = 'GROUP BY d.MerchantId, YEAR(d.Date), MONTH(d.Date) ORDER BY YEAR(d.Date) ASC, MONTH(d.Date) ASC, d.MerchantId ASC';
        break;
    case 'weekly':
        $dasboardSQLperiod = 'GROUP BY d.MerchantId, YEAR(d.Date), WEEK(d.Date,1) ORDER BY YEAR(d.Date) ASC, WEEK(d.Date,1) ASC, d.MerchantId ASC';
        break;
    case 'daily':
        $dasboardSQLperiod = 'GROUP BY d.MerchantId, d.Date ORDER BY d.Date ASC, d.MerchantId ASC';
        break;
    default:
        $dasboardSQLperiod = 'GROUP BY d.MerchantId ORDER BY d.MerchantId ASC';
        break;
}


        
        $dasboardSQLwhere = '';

if(!empty($merchant_id)){
    $dasboardSQLwhere = ' AND d.MerchantId = '.(int)$merchant_id;
}

if(!empty($affiliate_id)){
    $dasboardSQLwhere = ' AND d.AffiliateID = '.(int)$affiliate_id;
}


        
$dasboardDataResult = "select 
        d.Date,
        d.MerchantId, 
        YEAR(d.Date) AS Year, 
        MONTH(d.Date) AS Month , 
        WEEK(d.Date) AS Week,
        sum(d.Impressions) as Impressions, 
        sum(d.Clicks) as Clicks,  
        sum(d.Install) as Install, 
        sum(d.Leads) as Leads,  
        sum(d.Demo) as Demo,  
        sum(d.RealAccount) as RealAccount,  
        sum(d.FTD) as FTD,  
        sum(d.FTDAmount) as FTDAmount,  
        sum(d.RawFTD) as RawFTD,  
        sum(d.RawFTDAmount) as RawFTDAmount,  
        sum(d.Deposits) as Deposits,  
        sum(d.DepositsAmount) as DepositsAmount, 
        sum(d.Bonus) as Bonus, 
        sum(d.Withdrawal) as Withdrawal, 
        sum(d.ChargeBack) as ChargeBack, 
        sum(d.NetDeposit) as NetDeposit, 
        sum(d.PNL) as PNL, 
        sum(d.Volume) as Volume, 
        sum(d.ActiveTrader) as ActiveTrader, 
        sum(d.Commission) as Commission, 
        sum(d.PendingDeposits) as PendingDeposits, 
        sum(d.PendingDepositsAmount) as PendingDepositsAmount 
        from Dashboard d
        INNER JOIN affiliates aff ON d.AffiliateID = aff.id
        WHERE 
        d.Date >= '" . $from . "' 
        AND d.Date < '" . $to . "' 
        ".$dasboardSQLwhere."
        " . $dasboardSQLperiod;


$dasboardDataResult = function_mysql_query($dasboardDataResult);

$l = 0;

function getStartAndEndDate($week, $year) {
  $dto = new DateTime();
  $ret['week_start'] = $dto->setISODate($year, $week)->format('Y-m-d');
  $ret['week_end'] = $dto->modify('+6 days')->format('Y-m-d');
  return $ret['week_start'].'<br>'.$ret['week_end'];
}

while ($dasboardData = mysql_fetch_assoc($dasboardDataResult)) {

    $l++;

    $arrRange['from'] = $dasboardData['Date'];
    $arrRange['to'] = $dasboardData['Date'];

    $dasboardData['NetDeposit'] = ($dasboardData['DepositsAmount'] - ($dasboardData['Withdrawal'] + $dasboardData['ChargeBack']));

    switch ($display_type) {
        case 'monthly':
            $period_string = date('F', strtotime(date('Y-' . $dasboardData['Month'] . '-01')));
            break;
        case 'weekly':
            $period_string = date('Y-m-d', strtotime($dasboardData['Date'])) . '<br>' . date('Y-m-d', strtotime($dasboardData['Date'] . ' +' . (7 - date('N', strtotime($dasboardData['Date']))) . ' days'));
            break;
        case 'daily':
            $period_string = $dasboardData['Date'];
            break;
    }

    $totalImpressions += $dasboardData['Impressions'];
    $totalClicks += $dasboardData['Clicks'];
    $totalCPIM += $dasboardData['Install'];
    $totalLeadsAccounts += $dasboardData['Leads'];
    $totalDemoAccounts += $dasboardData['Demo'];
    $totalRealAccounts += $dasboardData['RealAccount'];
    $totalFTD += $dasboardData['FTD'];
    $totalFTDAmount += $dasboardData['FTDAmount'];
    $totalRealFtd += $dasboardData['RawFTD'];
    $totalRealFtdAmount += $dasboardData['RawFTDAmount'];
    $totalDeposits += $dasboardData['Deposits'];
    $totalDepositAmount += $dasboardData['DepositsAmount'];
    $totalVolume += $dasboardData['Volume'];
    $totalBonus += $dasboardData['Bonus'];
    $totalWithdrawal += $dasboardData['Withdrawal'];
    $totalChargeback += $dasboardData['ChargeBack'];
    $totalNetRevenue += $dasboardData['NetDeposit'];
    $totalFooterPNL += $dasboardData['PNL'];
    $totalActiveTraders += $dasboardData['ActiveTraders'];
    $totalComs += $dasboardData['Commission'];







    $listReport .= '<tr>
                                        ' . ($display_type == "daily" ? '<td style="text-align: center;">' . $period_string . '</td>' : '') . '
                                        ' . ($display_type == "weekly" ? '<td style="text-align: center;">' . $period_string . '</td>' : '') . '
                                        ' . ($display_type == "monthly" ? '<td style="text-align: center;">' . $period_string . '</td>' : '') . '
                                        <td  class="r-first border-left-none-r">' . $merchants_array[$dasboardData['MerchantId']]['name'] . '</td>
                                        ' . (allowView('af-impr', $deal, 'fields') ? '
                                        <td class="border-left-none">' . @number_format($dasboardData['Impressions'], 0) . '</td>
                                        ' : '') . '
                                        ' . (allowView('af-clck', $deal, 'fields') ? '
                                        <td>' . @number_format($dasboardData['Clicks'], 0) . '</td>
                                        ' : '') . '
                                        ' . (allowView('af-instl', $deal, 'fields') && $set->deal_cpi ? '
                                        <td>' . @number_format($dasboardData['Install'], 0) . '</td>
										' : '') . '
                                        <td>' . @number_format(($dasboardData['Clicks'] / $dasboardData['Impressions']) * 100, 2) . ' %</td>
                                        <td>' . @number_format(($dasboardData['RealAccount'] / $dasboardData['Clicks']) * 100, 2) . ' %</td>
                                        <td>' . @number_format(($dasboardData['FTD'] / $dasboardData['Clicks']) * 100, 2) . ' %</td>' .
            (!$hideDemoAndLeads ?
            (allowView('af-lead', $deal, 'fields') ? '	
										<td><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=lead">' . $dasboardData['Leads'] . '</a></td>
										' : '') . '
										' . (allowView('af-demo', $deal, 'fields') ? '			
                                           <td><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=demo">' . $dasboardData['Demo'] . '</a></td>
										' : '') : '') .
            (allowView('af-real', $deal, 'fields') ?
            '<td><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=real">' . $dasboardData['RealAccount'] . '</a></td>' : '')
            . (allowView('af-ftd', $deal, 'fields') ? '
                                        <td><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=ftd">' . $dasboardData['FTD'] . '</a></td>
										' : '') . '

                                        ' . (allowView('af-ftda', $deal, 'fields') ? '<td>' . price($dasboardData['FTDAmount']) . '</td>' : '') . '
                                        ' . (allowView('af-tftd', $deal, 'fields') ? '<td><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=ftd">' . $dasboardData['RawFTD'] . '</a></td>' : '') . '
										
                                        ' . (allowView('af-tftda', $deal, 'fields') ? '<td>' . price($dasboardData['RawFTDAmount']) . '</td>' : '') . '	
                                        
										' . ( (allowView('af-depo', $deal, 'fields')) ?
            '<td><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to']
            . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=deposit">' . $dasboardData['Deposits'] . '</a></td>' : '')
            . ( (allowView('af-depoam', $deal, 'fields')) ? '<td>' . price($dasboardData['DepositsAmount']) . '</td>' : ''
            )
            . (allowView('af-vlm', $deal, 'fields') ?
            '<td>' . price($dasboardData['Volume']) . '</td>' : '')
            .
            ( (allowView('af-bns', $deal, 'fields')) ? '<td>' . price($dasboardData['Bonus']) . '</td>' : '' ) .
            ( (allowView('af-withd', $deal, 'fields')) ?
            '<td>' . price($dasboardData['Withdrawal']) . '</td>' : '' ) .
            ( (allowView('af-chrgb', $deal, 'fields')) ?
            '<td>' . price($dasboardData['ChargeBack']) . '</td>' : '')
            .
            (allowView('af-ntrv', $deal, 'fields') ? '<td>' . price($dasboardData['NetDeposit']) . '</td>' : '') . '
                                       ' . ($set->deal_pnl == 1 && allowView('af-pnl', $deal, 'fields') ?
            '<td>' . price($dasboardData['PNL']) . '</td>' : '') . '                               

										 ' . ( allowView('af-qftd', $deal, 'fields') ?
            '<td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=activeTrader">' . $dasboardData['ActiveTrader'] . '</a></td>' : '') . '
										
                                        
                                        <td>' . price($dasboardData['Commission']) . '</td>
                                </tr>';
}
/**********************************************/

$set->sortTable = 1;
if ($l > 0)
    $set->sortTableScript = 1;
$set->totalRows = $l;


$set->content .= '
		<div class="normalTableTitle quick-report-text" style="width: 100%;">' . lang('
           <button type="button" class="btn report-display imgReportFieldsSettings">
            <i class="fa fa-cog"></i> Report Display  
        </button>

            ') . '</div>
			<div style="background: #F8F8F8;">
			<form method="get">
				
                
                            <div class="quick-summary-reports">
                                <div class="row align-items-flex-end">
                                    <div class="col-lg-8">
                                        <div class="row reports-summary">
                                            <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                                                <div class="q-summary-r">
                                                    <h3>' . lang('Period') . '</h3>
                                                    <div class="q-s-option">
                                                    '.timeFramequick($from, $to).'
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-2 col-md-6 col-sm-12 col-12">
                                                <div class="q-summary-r">
                                                    <h3>' . lang('Merchant') . '</h3>
                                                    <div class="q-s-option">
                                                    <select name="merchant_id" style="width: 150px;"><option value="">' . lang('All') . '</option>' . listMerchants($merchant_id) . '</select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6 col-sm-12 col-12">
                                                <div class="q-summary-r">
                                                    <h3>' . lang('Search Type') . '</h3>
                                                    <div class="q-s-option">
                                                    <select name="display_type" style="width: 150px;"><option value="0">' . lang('Search Type') . '</option>' . listDisplayTypes($display_type) . '</select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="q-four-buttons">
                                            <div class="view-button">
                                                <input type="submit" value="' . lang('Show Reports') . '" />
                                                <input type="submit" value="Reset Search" />
                                            </div>
                                            <div class="expert-cc-button">
                                            ' . ($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickTbl\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\'' . $filename . '\'});"><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to CSV') . '" title="' . lang('Export to CSV') . '" align="absmiddle" /> <b>' . lang('Export to CSV') . '</b></a></div>' : '') . '
                                            <div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickTbl\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\'' . $filename . '\'});"><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to XLS') . '" title="' . lang('Export to XLS') . '" align="absmiddle" /> <b>' . lang('Export to XLS') . '</b></a>
                                            </div>
                                            ' . getFavoritesHTML() . '
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
             
			</form>
			
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" class="table" style="display:none;"></div>
		<div class="table-r" style="background: #F8F8F8;">';


//width 2000
$tableStr = '
            <table class="report-quick">
                            <thead>
                                <tr>
                                <th class="r-first">Merchant</th>
                                ' . ($display_type ? '<th>' . lang('Period') . '</th>' : '') . '
                                ' . (allowView('af-impr', $deal, 'fields') ? '
                                    <th class="border-left-none">' . lang('Impressions') . '</th>
                                    ' : '') .
                        (allowView('af-clck', $deal, 'fields') ? '
                                    <th>' . lang('Clicks') . '</th>
                                    ' : '') . '
                                    ' . (allowView('af-instl', $deal, 'fields') && $set->deal_cpi ? '
                                    <th>' . lang('Installation') . '</th>
                                    ' : '') .
                        '<th>' . lang('Click Through Ratio (CTR)') . '</th>
                                    <th>' . lang('Click to Account') . '</th>
                                    <th>' . lang(ptitle('Click to Sale')) . '</th>' .
                        (!$hideDemoAndLeads ?
                        (allowView('af-lead', $deal, 'fields') ?
                        '<th>' . lang(ptitle('Lead')) . '</th>
                                    ' : '') .
                        (allowView('af-demo', $deal, 'fields') ?
                        '<th>' . lang(ptitle('Demo')) . '</th>
                                    ' : '') : '') .
                        (allowView('af-real', $deal, 'fields') ?
                        '<th>' . lang('Accounts') . '</th>' : '' ) .
                        (allowView('af-ftd', $deal, 'fields') ? '
                                    <th>' . lang('FTD') . '</th>' : '' ) .
                        (allowView('af-ftda', $deal, 'fields') ? '<th>' . lang('FTD Amount') . '</th>' : '')
                        . (allowView('af-tftd', $deal, 'fields') ? '<th>' . lang('FAW FTD') . '</th>' : '')
                        . (allowView('af-tftda', $deal, 'fields') ? '<th>' . lang('RAW FTD Amount') . '</th>' : '')
                        . (allowView('af-depo', $deal, 'fields') && (true) ? '<th>' . lang('Total Deposits') . '</th>' : '' )
                        . (allowView('af-depoam', $deal, 'fields') && (true) ? '<th>' . lang('Deposit Amount') . '</th>' : '')
                        . (allowView('af-vlm', $deal, 'fields') ? '<th>' . lang('Volume') . '</th>' : '')
                        . (allowView('af-bns', $deal, 'fields') ? '<th>' . lang('Bonus Amount') . '</th>' : '')
                        . (allowView('af-withd', $deal, 'fields') ? '<th>' . lang('Withdrawal Amount') . '</th>' : '')
                        . (allowView('af-chrgb', $deal, 'fields') ? '<th>' . lang('ChargeBack Amount') . '</th>' : '')
                        . (allowView('af-ntrv', $deal, 'fields') ? '<th>' . lang(ptitle('Net Deposit')) . '</th>' : '') .
                        ($set->deal_pnl == 1 && allowView('af-pnl', $deal, 'fields') ? '<th>' . lang(ptitle('PNL')) . '</th>' : '') .
                        (allowView('af-qftd', $deal, 'fields') ? '<th>' . lang(ptitle('Active Traders')) . '</th>' : '')
                        . '<th>' . lang('Commission') . '</th>
                                </tr>
                            </thead>
                            <tbody>
                ' . $listReport . '
                <tfoot><tr>
                    ' . ($display_type ? '<th></th>' : '') . '
                    <th class="r-first border-left-none-r"><b>' . lang('Total') . ':</b></th>
                    ' . (allowView('af-impr', $deal, 'fields') ? '          
                    <th class="border-left-none">' . $totalImpressions . '</th>' : '' ) . '
                    ' . (allowView('af-clck', $deal, 'fields') ? '          
                    <th>' . $totalClicks . '</th>' : '' ) . '
                    ' . (allowView('af-instl', $deal, 'fields') && $set->deal_cpi ? '           
                    <th>' . $totalCPIM . '</th>' : '' ) . '
                    <th>' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</th>
                    <th>' . @number_format(($totalRealAccounts / $totalClicks) * 100, 2) . ' %</th>
                    <th>' . @number_format(($totalFTD / $totalClicks) * 100, 2) . ' %</th>' .
                        (!$hideDemoAndLeads ?
                        (allowView('af-lead', $deal, 'fields') ?
                        '<th><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&type=lead">' . $totalLeadsAccounts . '</a></th>' : '') .
                        (allowView('af-demo', $deal, 'fields') ?
                        '<th><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&type=demo">' . $totalDemoAccounts . '</a></th>' : '' ) : '') .
                        (allowView('af-real', $deal, 'fields') ?
                        '<th><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&type=real">' . $totalRealAccounts . '</a></th>' : '' ) .
                        (allowView('af-ftd', $deal, 'fields') ? '
                                    <th><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&type=ftd">' . $totalFTD . '</a></th>' : '')
                        . (allowView('af-ftda', $deal, 'fields') ?
                        '<th>' . price($totalFTDAmount) . '</th>' : '')
                        .
                        (allowView('af-tftd', $deal, 'fields') ?
                        '<th><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&type=ftd">' . $totalRealFtd . '</a></th>' : '') .
                        (allowView('af-tftda', $deal, 'fields') ?
                        '<th>' . price($totalRealFtdAmount) . '</th>' : '')
                        .
                        ( (allowView('af-depo', $deal, 'fields')) ?
                        '<th><a href="' . $set->SSLprefix . 'affiliate/reports.php?act=trader&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&type=deposit">' . $totalDeposits . '</a></th>' : '')
                        .
                        ( (allowView('af-depoam', $deal, 'fields')) ?
                        '<th>' . price($totalDepositAmount) . '</th>' : '')
                        .
                        (allowView('af-vlm', $deal, 'fields') ? '<th>' . price($totalVolume) . '</th>' : '')
                        .
                        (allowView('af-bns', $deal, 'fields') ? '<th>' . price($totalBonus) . '</th>' : '') .
                        (allowView('af-withd', $deal, 'fields') ? '<th>' . price($totalWithdrawal) . '</th>' : '') .
                        (allowView('af-chrgb', $deal, 'fields') ? '<th>' . price($totalChargeback) . '</th>' : '') .
                        (allowView('af-ntrv', $deal, 'fields') ? '<th>' . price($totalNetRevenue) . '</th>' : '') .
                        ($set->deal_pnl == 1 && allowView('af-pnl', $deal, 'fields') ?
                        '<th>' . price($totalFooterPNL) . '</th>' : '') .
                        (allowView('af-qftd', $deal, 'fields') ?
                        '<th>' . ($totalActiveTraders) . '</th>' : '') .
                        '<th>' . price($totalComs) . '</th>
                                </tr></tfoot>
            </table>

			<script type="text/javascript" src="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.css"/>              
			<script>
			$(document).ready(function(){
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
											level = "affiliate";
											type = "add";
											
											saveReportToMyFav(name, \'quick\',user,level,type);
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

//excelExporter($tableStr,'Quick');

$set->content .= $tableStr . '
		</div>' . getPager();

//MODAL
$myReport = lang("Quick Summary");
include "common/ReportFieldsModal.php";


theme();
?>