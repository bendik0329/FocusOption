<?php

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if (!defined('DirectBrowse')) {
    $path = "http" . $set->SSLswitch . "://" . $_SERVER[HTTP_HOST];
    header("Location: " . $path . "/" . $userlevel);
}

$pageTitle = lang('Quick Summary Report');
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
$filename = "QuickSummary_data_" . date('YmdHis');


if ($userlevel == 'manager')
    $group_id = $set->userInfo['group_id'];

$listReport = '';

$merchant_id = isset($merchant_id) && $merchant_id > 0 ? $merchant_id : 0;
$merchantsA = getMerchants($merchant_id, 1);


$l = -1;



$merchants_sql_all = function_mysql_query("select *,id as merchant_id from merchants",__FILE__,__FUNCTION__);
while ($row = mysql_fetch_assoc($merchants_sql_all)) {
        $merchants_array[$row['id']] = $row;
}



/* * ******************************* */
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

if(!empty($group_id)){
    $dasboardSQLwhere = ' AND aff.group_id = '.(int)$group_id;
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
        $period_string = date('F', strtotime(date('Y-'.$dasboardData['Month'].'-01')));
        break;
    case 'weekly':
        $period_string = date('Y-m-d',strtotime($dasboardData['Date'])).'<br>'.date('Y-m-d',strtotime($dasboardData['Date'].' +'.(7 - date('N',strtotime($dasboardData['Date']))).' days'));
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
    
    
    $tableArr = array(
        (object) array(
            'id' => 'daily',
            'str' => ($display_type == "daily" ? '<td style="text-align: center;" nowrap>' . $period_string . '</td>' : '')
        ),
        (object) array(
            'id' => 'weekly',
            'str' => ($display_type == "weekly" ? '<td style="text-align: center;" nowrap>' . $period_string . '</td>' : '')
        ),
        (object) array(
            'id' => 'monthly',
            'str' => ($display_type == "monthly" ? '<td style="text-align: center;" nowrap>' . $period_string . '</td>' : '')
        ),
        (object) array(
            'id' => 'name',
            'str' => '<td style="text-align: left;">' . $merchants_array[$dasboardData['MerchantId']]['name'] . '</td>'
        ),
        (object) array(
            'id' => 'totalViews',
            'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=clicks' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '">' . @number_format($dasboardData['Impressions'], 0) . '</a></td>'
        ),
        (object) array(
            'id' => 'totalClicks',
            'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=clicks' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '">' . @number_format($dasboardData['Clicks'], 0) . '</a></td>'
    ));
    if ($set->deal_cpi) {
        array_push($tableArr, (object) array(
                    'id' => 'totalCPI',
                    'str' => '<td style="text-align: center;">' . @number_format($dasboardData['Install'], 0) . '</td>'
                )
        );
    }

    array_push($tableArr, (object) array(
                'id' => 'totalClicks_totalViews',
                'str' => '<td style="text-align: center;">' . @number_format(($dasboardData['Clicks'] / $dasboardData['Impressions']) * 100, 2) . ' %</td>'
            ), (object) array(
                'id' => 'ftd_total_traffic',
                'str' => '<td style="text-align: center;">' . @number_format(($dasboardData['RealAccount'] / $dasboardData['Clicks']) * 100, 2) . ' %</td>'
            ), (object) array(
                'id' => 'commission_traffic',
                'str' => '<td style="text-align: center;">' . @number_format(($dasboardData['FTD'] / $dasboardData['Clicks']) * 100, 2) . ' %</td>'
            ), (object) array(
                'id' => 'EPC',
                'str' => '<td style="text-align: center;">' . @price($dasboardData['Commission'] / $dasboardData['Clicks']) . '</td>'
            ), (object) array(
                'id' => 'total_leads',
                'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=lead">' . $dasboardData['Leads'] . '</a></td>'
            ), (object) array(
                'id' => 'total_demo',
                'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=demo">' . $dasboardData['Demo'] . '</a></td>'
            ), (object) array(
                'id' => 'total_real',
                'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=real">' . $dasboardData['RealAccount'] . '</a></td>'
            ), (object) array(
                'id' => 'ftd',
                'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=ftd">' . $dasboardData['FTD'] . '</a></td>'
            ), (object) array(
                'id' => 'ftd_amount',
                'str' => '<td style="text-align: center;">' . price($dasboardData['FTDAmount']) . '</td>'
            ), (object) array(
                'id' => 'real_ftd',
                'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=totalftd">' . $dasboardData['RawFTD'] . '</a></td>'
            ), (object) array(
                'id' => 'real_ftd_amount',
                'str' => '<td style="text-align: center;">' . price($dasboardData['RawFTDAmount']) . '</td>'
            ), (object) array(
                'id' => 'depositAccount',
                'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=transactions' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=deposit">' . $dasboardData['Deposits'] . '</a></td>'
            ), (object) array(
                'id' => 'sumDeposits',
                'str' => '<td style="text-align: center;">' . price($dasboardData['DepositsAmount']) . '</td>'
            ), (object) array(
                'id' => 'volume',
                'str' => '<td style="text-align: center;">' . price($dasboardData['Volume']) . '</td>'
            ), (object) array(
                'id' => 'bonus',
                'str' => '<td style="text-align: center;">' . price($dasboardData['Bonus']) . '</td>'
            ), (object) array(
                'id' => 'Withdrawal',
                'str' => '<td style="text-align: center;">' . price($dasboardData['Withdrawal']) . '</td>'
            ), (object) array(
                'id' => 'ChargeBack',
                'str' => '<td style="text-align: center;">' . price($dasboardData['ChargeBack']) . '</td>'
            ), (object) array(
                'id' => 'NetRevenue',
                'str' => '<td style="text-align: center;">' . price($dasboardData['NetDeposit']) . '</td>'
            ), (object) array(
                'id' => 'PNL',
                'str' => '<td style="text-align: center;">' . price($dasboardData['PNL']) . '</td>'
            ), (object) array(
                'id' => 'activeTraders',
                'str' => '<td style="text-align: center;"><a href="/' . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . $arrRange['from'] . '&to=' . $arrRange['to'] . '&merchant_id=' . $dasboardData['MerchantId'] . '&type=activeTrader">' . $dasboardData['ActiveTrader'] . '</a></td>'
            ), (object) array(
                'id' => 'Commission',
                'str' => '<td style="text-align: center;">' . price($dasboardData['Commission']) . '</td>'
            )
    );

    $listReport .= '<tr>' . setTable($tableArr, $boxaName, $set->userInfo['productType'], '') . '</tr>';
}



/* * ******************************* */






if ($l > 0) {
    $set->sortTableScript = 1;
}

$set->sortTable = 1;
$set->totalRows = $l;


$tableArr = Array(
    (object) array(
        'id' => 'daily',
        'str' => ($display_type == "daily" ? '<th class="table-cell">' . lang('Period') . '</th>' : '')
    ),
    (object) array(
        'id' => 'weekly',
        'str' => ($display_type == "weekly" ? '<th class="table-cell">' . lang('Period') . '</th>' : '')
    ),
    (object) array(
        'id' => 'monthly',
        'str' => ($display_type == "monthly" ? '<th class="table-cell">' . lang('Period') . '</th>' : '')
    ),
    (object) array(
        'id' => 'name',
        'str' => '<th  class="table-cell" style="text-align: left;">' . lang('Merchant') . '</th>'
    ),
    (object) array(
        'id' => 'totalViews',
        'str' => '<th class="table-cell">' . lang('Impressions') . '</th>'
    ),
    (object) array(
        'id' => 'totalClicks',
        'str' => '<th class="table-cell">' . lang('Clicks') . '</th>'
        ));
if ($set->deal_cpi) {
    array_push($tableArr, (object) array(
                'id' => 'totalCPI',
                'str' => '<th class="table-cell">' . lang('Installation') . '</th>'
            )
    );
}
array_push($tableArr, (object) array(
            'id' => 'totalClicks_totalViews',
            'str' => '<th class="table-cell">' . lang('Click Through Ratio (CTR)') . '</th>'
        ), (object) array(
            'id' => 'ftd_total_traffic',
            'str' => '<th class="table-cell">' . lang(ptitle('Click to Account')) . '</th>'
        ), (object) array(
            'id' => 'commission_traffic',
            'str' => '<th class="table-cell">' . lang(ptitle('Click to Sale')) . '</th>'
        ), (object) array(
            'id' => 'EPC',
            'str' => '<th class="table-cell">' . lang(ptitle('EPC')) . '</th>'
        ), (object) array(
            'id' => 'total_leads',
            'str' => '<th  class="table-cell">' . lang(ptitle('Lead')) . '</th>'
        ), (object) array(
            'id' => 'total_demo',
            'str' => '<th class="table-cell">' . lang(ptitle('Demo')) . '</th>'
        ), (object) array(
            'id' => 'total_real',
            'str' => '<th class="table-cell">' . lang(ptitle('Accounts')) . '</th>'
        ), (object) array(
            'id' => 'ftd',
            'str' => '<th class="table-cell">' . lang('FTD') . '</th>'
        ), (object) array(
            'id' => 'ftd_amount',
            'str' => '<th class="table-cell">' . lang('FTD Amount') . '</th>'
        ), (object) array(
            'id' => 'real_ftd',
            'str' => '<th class="table-cell">' . lang('RAW FTD') . '</th>'
        ), (object) array(
            'id' => 'real_ftd_amount',
            'str' => '<th class="table-cell">' . lang('RAW FTD Amount') . '</th>'
        ), (object) array(
            'id' => 'depositAccount',
            'str' => '<th class="table-cell">' . lang('Total Deposits') . '</th>'
        ), (object) array(
            'id' => 'sumDeposits',
            'str' => '<th class="table-cell">' . lang('Deposit Amount') . '</th>'
        ), (object) array(
            'id' => 'volume',
            'str' => '<th class="table-cell">' . lang('Volume') . '</th>'
        ), (object) array(
            'id' => 'bonus',
            'str' => '<th class="table-cell">' . lang('Bonus Amount') . '</th>'
        ), (object) array(
            'id' => 'Withdrawal',
            'str' => '<th class="table-cell">' . lang('Withdrawal Amount') . '</th>'
        ), (object) array(
            'id' => 'ChargeBack',
            'str' => '<th class="table-cell">' . lang('ChargeBack Amount') . '</th>'
        ), (object) array(
            'id' => 'NetRevenue',
            'str' => '<th class="table-cell">' . lang(ptitle('Net Revenue')) . '</th>'
        ), (object) array(
            'id' => 'PNL',
            'str' => '<th class="table-cell">' . lang(ptitle('PNL')) . '</th>'
        ), (object) array(
            'id' => 'activeTraders',
            'str' => '<th class="table-cell">' . lang(ptitle('Active Traders')) . '</th>'
        ), (object) array(
            'id' => 'Commission',
            'str' => '<th class="table-cell">' . lang('Commission') . '</th>'
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
        'str' => '<th style="text-align: left;"><b>' . lang('Total') . ':</b></th>'
    ),
    (object) array(
        'id' => 'totalViews',
        'str' => '<th><a href="/' . $userlevel . '/reports.php?act=clicks' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '">' . $totalImpressions . '</a></th>'
    ),
    (object) array(
        'id' => 'totalClicks',
        'str' => '<th><a href="/' . $userlevel . '/reports.php?act=clicks' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '">' . $totalClicks . '</a></th>'
        ));

if ($set->deal_cpi) {
    array_push($tableArr2, (object) array(
                'id' => 'totalCPI',
                'str' => '<th>' . $totalCPIM . '</th>'
            )
    );
}

array_push($tableArr2, (object) array(
            'id' => 'totalClicks_totalViews',
            'str' => '<th>' . @number_format(($totalClicks / $totalImpressions) * 100, 2) . ' %</th>'
        ), (object) array(
            'id' => 'ftd_total_traffic',
            'str' => '<th>' . @number_format(($totalRealAccounts / $totalClicks) * 100, 2) . ' %</th>'
        ), (object) array(
            'id' => 'commission_traffic',
            'str' => '<th>' . @number_format(($totalFTD / $totalClicks) * 100, 2) . ' %</th>'
        ), (object) array(
            'id' => 'EPC',
            'str' => '<th>' . @price($totalComs / $totalClicks) . '</th>'
        ), (object) array(
            'id' => 'total_leads',
            'str' => '<th><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=lead">' . $totalLeadsAccounts . '</a></th>'
        ), (object) array(
            'id' => 'total_demo',
            'str' => '<th><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=demo">' . $totalDemoAccounts . '</a></th>'
        ), (object) array(
            'id' => 'total_real',
            'str' => '<th><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=real">' . $totalRealAccounts . '</a></th>'
        ), (object) array(
            'id' => 'ftd',
            'str' => '<th><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=ftd">' . $totalFTD . '</a></th>'
        ), (object) array(
            'id' => 'ftd_amount',
            'str' => '<th>' . price($totalFTDAmount) . '</th>'
        ), (object) array(
            'id' => 'real_ftd',
            'str' => '<th><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader&' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . 'from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=ftd">' . $totalRealFtd . '</a></th>'
        ), (object) array(
            'id' => 'real_ftd_amount',
            'str' => '<th>' . price($totalRealFtdAmount) . '</th>'
        ), (object) array(
            'id' => 'depositAccount',
            'str' => '<th><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=transactions' . ($affiliate_id ? '&affiliate_id=' . $affiliate_id : "") . '&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&type=deposit">' . $totalDeposits . '</a></th>'
        ), (object) array(
            'id' => 'sumDeposits',
            'str' => '<th>' . price($totalDepositAmount) . '</th>'
        ), (object) array(
            'id' => 'volume',
            'str' => '<th>' . price($totalVolume) . '</th>'
        ), (object) array(
            'id' => 'bonus',
            'str' => '<th>' . price($totalBonus) . '</th>'
        ), (object) array(
            'id' => 'Withdrawal',
            'str' => '<th>' . price($totalWithdrawal) . '</th>'
        ), (object) array(
            'id' => 'ChargeBack',
            'str' => '<th>' . price($totalChargeback) . '</th>'
        ), (object) array(
            'id' => 'NetRevenue',
            'str' => '<th>' . price($totalNetRevenue) . '</th>'
        ), (object) array(
            'id' => 'PNL',
            'str' => '<th>' . price($totalFooterPNL) . '</th>'
        ), (object) array(
            'id' => 'activeTraders',
            'str' => '<th>' . ($totalActiveTraders) . '</th>'
        ), (object) array(
            'id' => 'Commission',
            'str' => '<th>' . price($totalComs) . '</th>'
        )
);

$set->content .= '
        <div class="normalTableTitle" style="width: 100%;">' . lang('Report Search') . '</div>
            <div style="background: #F8F8F8;">
            <form method="get" onsubmit = "return submitReportsForm(this)">
                <table><tr>
                        <td>' . lang('Period') . '</td>
                        <td>' . lang('Merchant') . '</td>
                        <td width=160>' . lang('Affiliate ID') . '</td>
                        ' . ($userlevel == "admin" ? '<td style="padding-left:20px">' . lang('Group ID') . '</td>' : '') . '
                        <td style="padding-left:20px">' . lang('Search Type') . '</td>
                        <td></td>
                    </tr><tr>
                    <td>' . timeFrame($from, $to) . '</td>
                    <td><select name="merchant_id" style="width: 150px;"><option value="">' . lang('All') . '</option>' . listMerchants($merchant_id) . '</select></td>
                    <td><!--input type="text" name="affiliate_id" value="' . $affiliate_id . '" id="affiliate_id" style="width: 60px; text-align: center;" /-->
                    <div class="ui-widget">'
        . '<!-- name="affiliate_id" -->'
        . '<select id="combobox" ' . ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') . '>'
        . '<!--option value="">' . lang('Choose Affiliate') . '</option-->'
        . $listOfAffiliates
        . '</select>
                                </div>
                    </td>
                    <!--td><input type="text" name="group_id" value="' . $group_id . '" id="group_id" style="width: 60px; text-align: center;" /></td-->
                                        ' . ($userlevel == 'admin' ? '<td width="100" style="padding-left:20px">
                                            <select name="group_id" style="width: 100px;">
                                                <option value="">' . lang('All Groups') . '</option>'
        . '<option value="0" ' . ($group_id == "0" ? 'selected="selected"' : '') . '>'
        . lang('General')
        . '</option>'
        . listGroups($group_id)
        . '</select>
                                        </td>' : '') . '
                    <td style="padding-left:20px"><select name="display_type" style="width: 150px;"><option value="0">' . lang('Search Type') . '</option>' . listDisplayTypes($display_type) . '</select></td>
                    <td><input type="submit" value="' . lang('View') . '" /></td>
                </tr></table>
            </form>
            ' . ($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\'' . $filename . '\'});"><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to CSV') . '" title="' . lang('Export to CSV') . '" align="absmiddle" /> <b>' . lang('Export to CSV') . '</b></a></div>' : '') . '
                <div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#quickData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\'' . $filename . '\'});"><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to XLS') . '" title="' . lang('Export to XLS') . '" align="absmiddle" /> <b>' . lang('Export to XLS') . '</b></a>
                </div>
                ' . getFavoritesHTML() . '
                <div style="clear:both"></div>
        </div>
        <div style="height:20px;"></div>
        
        <div class="normalTableTitle"  class="table">' . lang('Quick Summary Report') . '<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="' . $set->SSLprefix . 'images/settings.png"/></span></div>
        <div style="background: #F8F8F8;">';
//width 2000
$tableStr = '
            <table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="quickTbl">
                <thead><tr  class="table-row">
                    ' . setTable($tableArr, $boxaName, $set->userInfo['productType'], '') . '
                </tr></thead><tfoot><tr>
                    ' . setTable($tableArr2, $boxaName, $set->userInfo['productType'], '') . '
                </tr></tfoot>
                <tbody>
                ' . $listReport . '
            </table>
            <script type="text/javascript" src="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.js"></script>
            <link rel="stylesheet" href="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.css"/>              
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
//MODAL
$myReport = lang("Quick Summary");
include "common/ReportFieldsModal.php";
//excelExporter($tableStr, 'Quick');
$set->content .= $tableStr . '</div>' . getPager();
theme();
?>