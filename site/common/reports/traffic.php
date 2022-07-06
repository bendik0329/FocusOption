<?php

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if (!defined('DirectBrowse')) {
    $path = "http" . $set->SSLswitch . "://" . $_SERVER[HTTP_HOST];
    header("Location: " . $path . "/" . $userlevel);
}

$pageTitle = lang('Referral Report');
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
$filename = "Referral_data_" . date('YmdHis');

if ($userlevel == 'manager') {
    $group_id = $set->userInfo['group_id'];
}

$page = (isset($page) || !empty($page)) ? $page : 1;
$set->page = $page;

$start_limit = $page == 1 ? 0 : $set->rowsNumberAfterSearch * ($page - 1);
$end_limit = $set->rowsNumberAfterSearch; // * $page;

if ($merchant_id) {
    $where = " AND tb1.MerchantID='" . $merchant_id . "'";
}
if ($profile_id) {
    $whereSites = " AND tb1.ProfileID='" . $profile_id . "'";
}
if ($affiliate_id) {
    $whereSites .= " AND tb1.AffiliateID='" . $affiliate_id . "'";
}
//if ($uid){ $whereSites .= " AND tb1.uid='".$uid."'";}

if ($userlevel == 'admin') {
    if ($group_id && isAdmin()) {
        $whereSites .= " AND af.group_id='" . $group_id . "'";
    }
} else {
    if ($group_id) {
        $whereSites .= " AND af.group_id='" . $group_id . "'";
    }
}


$sql = "select id,name from affiliates_profiles where valid =1";
$qqProfiles = function_mysql_query($sql);
$listProfiles = array();
while ($wwProfiles = mysql_fetch_assoc($qqProfiles)) {
    $listProfiles[$wwProfiles['id']] = $wwProfiles['name'];
}


$fromDate = strtotime($from);
$toDate = strtotime($to);

if ($display_type AND $display_type != "monthly") {
    $diff = ($toDate - $fromDate) / 86400; // get the number of days between
} else {
    $diff = 0;
}

if ($display_type == "weekly") {
    $theFromDay = date("w", strtotime($from)) + 1;
    $theToDay = date("w", strtotime($to)) + 1;
    $daysUntilEndWeek = (7 - $theFromDay);
    $endOfWeek = date("Y-m-d", strtotime($from . " +" . $daysUntilEndWeek . " Day"));

    $diff = round($diff / 8);
    if ($theFromDay > $theToDay) {
        $diff = $diff + 1;
    }
}

if ($display_type == "monthly") {
    $endOfthisMonth = date("t", strtotime($from));
    if ($endOfthisMonth < 10) {
        $endOfthisMonth = "0" . $endOfthisMonth;
    }
    $lastDayOfMonth = date("Y-m-" . $endOfthisMonth, strtotime($from));
    $date1 = strtotime($from);
    $date2 = strtotime($to);
    $months = 0;
    while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) {
        $diff++;
    }
    if ($diff == 0) {
        $lastDayOfMonth = $to;
    }
}

//for ($i=0;$i<=$diff;$i++) 
//{
// From Show on weekly
if ($newFrom) {
    $showFrom = $newFrom;
} else {
    $showFrom = $from;
}

$searchInSql = "";
if ($display_type == "daily") {
    $searchInSql = "= '" . date("Y-m-d", strtotime($from . " +" . $i . " day")) . "'";
} else if ($display_type == "weekly") {
    if ($i == 0) { // Weekly First Loop
        $searchInSql = "BETWEEN '" . $from . "' AND '" . $endOfWeek . "'"; // First Loop - first week
        $newFrom = date("Y-m-d", strtotime($endOfWeek . "+1 Day"));
    } else if ($i == $diff) { // Last Loop - Last week
        $searchInSql = "BETWEEN '" . $newFrom . "' AND '" . $to . "'";
        $newFrom = date("Y-m-d", strtotime($newTo . "+1 Day"));
    } else { // Else Loops
        $newTo = date("Y-m-d", strtotime($newFrom . "+6 Day"));
        $searchInSql = "BETWEEN '" . $newFrom . "' AND '" . $newTo . "'";
        $newFrom = date("Y-m-d", strtotime($newTo . "+1 Day"));
    }
} else if ($display_type == "weekly") {
    if ($i == 0) { // Weekly First Loop
        $searchInSql = "BETWEEN '" . $from . "' AND '" . date("Y-m-d", strtotime()) . "'"; // First Loop - first week
        $newFrom = date("Y-m-d", strtotime($endOfWeek . "+1 Day"));
    } else if ($i == $diff) { // Last Loop - Last week
        $searchInSql = "BETWEEN '" . $newFrom . "' AND '" . $to . "'";
        $newFrom = date("Y-m-d", strtotime($newTo . "+1 Day"));
    } else { // Else Loops
        $newTo = date("Y-m-d", strtotime($newFrom . "+6 Day"));
        $searchInSql = "BETWEEN '" . $newFrom . "' AND '" . $newTo . "'";
        $newFrom = date("Y-m-d", strtotime($newTo . "+1 Day"));
    }
} else if ($display_type == "monthly") {
    if ($i == 0) { // Monthly First Loop
        $searchInSql = "BETWEEN '" . $from . "' AND '" . $lastDayOfMonth . "'";
        $newFrom = date("Y-m-d", strtotime($lastDayOfMonth . " +1 Day"));
    } else if ($i == $diff) { // Last Loop - Last week
        $searchInSql = "BETWEEN '" . $newFrom . "' AND '" . $to . "'";
        $newFrom = date("Y-m-d", strtotime($newTo . "+1 Day"));
    } else { // Else Loops
        $numOfDaysThisMonth = date("t", strtotime($newFrom)) - 1;
        $newTo = date("Y-m-d", strtotime($newFrom . "+" . $numOfDaysThisMonth . " day"));
        $searchInSql = "BETWEEN '" . $newFrom . "' AND '" . $newTo . "'";
        $newFrom = date("Y-m-d", strtotime($newTo . "+1 Day"));
    }
} else { // If no Display_type 
    $searchInSql = "BETWEEN '" . $from . " 00:00:00' AND '" . $to . "'";
}

// To Show on weekly
if ($display_type == "weekly"){
    if ($newTo) {
        $showTo = $newTo;
    } else {
        $showTo = $endOfWeek;
    }
}
// To Show on monthly
if ($display_type == "monthly") {
    if ($newTo) {
        $showTo = $newTo;
    } else {
        $showTo = $lastDayOfMonth;
    }
}
if ($i == $diff) {
    $showTo = $to;
}
$l++;

$merchantName = strtolower($ww['name']);
$merchantID = $ww['id'];



$filterFrom = ($display_type == "daily" ? strtotime($from . ' +' . $i . ' Day') : strtotime($showFrom));
$filterTo = ($display_type == "daily" ? strtotime($from . ' +' . $i . ' Day') : strtotime($showTo));




$refferelArray = array();
$listGroups = affiliateGroupsArray();
if ($userlevel == "admin") {
    $is_admin = isAdmin();
} else {
    $is_admin = 0;
}

$qSelectString = "t.ReferUrl as refer_url,
    t.MerchantID as merchant_id,
    m.name as merchant_name,
    t.AffiliateID as affiliate_id,
    t.LastClickIp as ip,
    af.group_id as 'group',
    SUM(t.AllTimeClicks) as clicks, 
    SUM(t.AllTimeViews) as views,
    SUM(t.Leads) as leads,
    SUM(t.Demo) as demo,
    SUM(t.Accounts) as 'real',
    SUM(t.FTD) as ftd,
    SUM(t.FTDAmount) as ftd_amount,
    SUM(t.RawFTD) as real_ftd,
    SUM(t.RawFTDAmount) as real_ftd_amount,
    SUM(t.TotalDeposits) as depositingAccounts,
    SUM(t.DepositsAmount) as sumDeposits,
    SUM(t.Volume) as volume,
    SUM(t.BonusAmount) as bonus,
    SUM(t.WithdrawalAmount) as withdrawal,
    SUM(t.ChargebackAmount) as chargeback,
    SUM(t.PNL) as pnl,
    SUM(t.Commissions) as totalCom,
    af.username as username
    ";

if (isset($filter) && !empty($filter)) {

    if ($filter == "active_account") {
        $where = str_replace("tb1", "t", $whereSites);
        $sql = "SELECT ".$qSelectString." FROM ReportReferral t INNER JOIN affiliates af ON t.AffiliateID = af.id LEFT JOIN merchants m ON t.MerchantID = m.id WHERE t.Date " . $searchInSql . " AND (t.TotalDeposits > 0 OR t.PNL > 0 OR t.Volume > 0) " . $where . " GROUP BY t.ReferUrlHash, t.AffiliateID";
        $totalRec = mysql_num_rows (function_mysql_query($sql, __FILE__));
    } else if ($filter == "real_account") {
        $where = str_replace("tb1", "t", $whereSites);
        $sql = "SELECT ".$qSelectString." FROM ReportReferral t INNER JOIN affiliates af ON t.AffiliateID = af.id LEFT JOIN merchants m ON t.MerchantID = m.id WHERE t.Date " . $searchInSql . " AND t.Accounts > 0 " . $where . " GROUP BY t.ReferUrlHash, t.AffiliateID";
        $totalRec = mysql_num_rows (function_mysql_query($sql, __FILE__));
    } else if ($filter == "ftd") {
        $where = str_replace("tb1", "t", $whereSites);
        $sql = "SELECT ".$qSelectString." FROM ReportReferral t INNER JOIN affiliates af ON t.AffiliateID = af.id LEFT JOIN merchants m ON t.MerchantID = m.id WHERE t.Date " . $searchInSql . " AND t.FTD > 0 " . $where . " GROUP BY t.ReferUrlHash, t.AffiliateID";
        $totalRec = mysql_num_rows (function_mysql_query($sql, __FILE__));
    } else {
        //case all
    }
} else {
    $where = str_replace("tb1", "t", $whereSites);

    $sql = "SELECT ".$qSelectString." FROM ReportReferral t INNER JOIN affiliates af ON t.AffiliateID = af.id LEFT JOIN merchants m ON t.MerchantID = m.id WHERE t.Date " . $searchInSql . " " . $where . " GROUP BY t.ReferUrlHash";
    if (empty($affiliate_id)) {
        $sql.= ', t.AffiliateID';
    }
    $totalRec = mysql_num_rows (function_mysql_query($sql, __FILE__));
}



$groups_result = function_mysql_query("SELECT id, title FROM groups",__FILE__);

while ($group = mysql_fetch_assoc($groups_result)) {
    $arrGroups[$group['id']] = $group;
}
unset($group);

$listReport = "";
$set->total_records = $totalRec;

$ajaxSql = $sql . " limit 0,65536";
$sql .= " limit " . $start_limit . ", " . $end_limit;

$resc = function_mysql_query($sql, __FILE__);
$i = 0;
$l = 0;
$totalVisits = 0;
while ($data = mysql_fetch_assoc($resc)) {
    
    $i++;
    $l++;

    $country = '';
    $countryArry = getIPCountry($data['ip']);
    if ($countryArry['countryLONG'] == '') {
        $country = lang('Unknown');
    } else {
        $country = $countryArry['countryLONG'];
    }
    
    $data['country'] = $country;
    
    if(empty($data['ip'])){
        $data['ip'] = '&mdash;';
    }
    
    $data['refer_url'] =htmlentities($data['refer_url']);
    $refer_url = $data['refer_url'];
    if (strlen($data['refer_url']) > 50) {
        $refer_url = substr($data['refer_url'], 0, 49) . "...";
    }

    

    $totalViews += $data['views'];
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
    $totalPNL += $data['pnl'];
    $totalComs += $data['totalCom'];
    $totalRealFtd += $data['real_ftd'];
    $totalRealFtdAmount += $data['real_ftd_amount'];
    

    $listReport .= '
                        <tr>
                            <td><a href="' . $data['refer_url'] . '" target="_blank">' . $refer_url . '</a></td>
                            <td>' . $data['merchant_id'] . '</td>
                            <td>' . $data['merchant_name'] . '</td>
                            <td>' . $data['ip'] . '</td>
                            <td>' . $data['country'] . '</td>
                            <td>' . $data['affiliate_id'] . '</td>
                            <td><a href="' . $set->SSLprefix . $userlevel . '/affiliates.php?act=new&id=' . $data['affiliate_id'] . '" target="_blank">' . $data['username'] . '</a></td>
                            
                            <!--td>' . $data['profile_id'] . '</td>
                            <td>' . $listProfiles[$data['profile_id']] . '</td-->
                                
                            ' . ($is_admin ? '<td>' . $arrGroups[$data['group']]['title'] . '</td>' : '' ) . '
                            <td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=clicks&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . (!is_null($merchant) ? $merchant : 0) . '">' . $data['clicks'] . '</a></td>
                            <td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=clicks&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . (!is_null($merchant) ? $merchant : 0) . '">' . $data['views'] . '</td>
                            <td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . (!is_null($merchant) ? $merchant : 0) . '&type=lead&country_id=' . $data['country'] . '">' . $data['leads'] . '</a></td>
                            <td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . (!is_null($merchant) ? $merchant : 0) . '&type=demo&country_id=' . $data['country'] . '">' . $data['demo'] . '</a></td>
                            <td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . (!is_null($merchant) ? $merchant : 0) . '&type=real&country_id=' . $data['country'] . '">' . $data['real'] . '</a></td>
                            <td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . (!is_null($merchant) ? $merchant : 0) . '&type=ftd&country_id=' . $data['country'] . '">' . $data['ftd'] . '</a></td>
                            <td>' . price($data['ftd_amount']) . '</td>
                            <td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=trader&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . (!is_null($merchant) ? $merchant : 0) . 'type=totalftd&country_id=' . $data['country'] . '">' . $data['real_ftd'] . '</a></td>
                            <td>' . price($data['real_ftd_amount']) . '</td>
                            <td><a href="' . $set->SSLprefix . $userlevel . '/reports.php?act=transactions&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . (!is_null($merchant) ? $merchant : 0) . '&type=deposit&country_id=' . $data['country'] . '">' . $data['depositingAccounts'] . '</a></td>
                            <td>' . price($data['sumDeposits']) . '</td>
                            <td style="text-align: center;">' . price($data['volume']) . '</td>
                            <td>' . price($data['bonus']) . '</td>
                            <td>' . price($data['withdrawal']) . '</td>
                            <td>' . price($data['chargeback']) . '</td>
                            <td style="text-align: center;">' . price($data['netRevenue']) . '</td>
                            ' . ($set->deal_pnl == 1 ? '
                            <td style="text-align: center;">' . price($data['pnl']) . '</td>' : '') . '
                            <td>' . price($data['totalCom']) . '</td>';
}




//}	
$set->sortTable = 1;

//if ($l > 0) $set->sortTableScript = 1;
$set->totalRows = $l;
$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">' . lang('Report Search') . '</div>
			<div style="background: #F8F8F8;">
			<form action="' . $set->SSLprefix . $set->basepage . '" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="traffic" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>' . lang('Period') . '</td>
					' . ($is_admin ? '<td>' . lang('Group') . '</td>' : '') . '
					<td>' . lang('Merchant') . '</td>
					
					<td width=160>' . lang('Affiliate ID') . '</td>
					<td style="padding-left:20px">' . lang('UID') . '</td>
					<td>' . lang('Filter') . '</td>
					<td></td>
				</tr><tr>
					<td>
						' . timeFrame($from, $to) . '
					</td>
					
					
					' . ($is_admin ? '
					<td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">' . lang('All Groups') . '</option>'
        . '<option value="0" ' . ($group_id == "0" ? 'selected="selected"' : '') . '>' . lang('General') . '</option>'
        . listGroups($group_id)
        . '</select>
                    </td>' : '') . '
												
					
					<td><select name="merchant_id" style="width: 150px;"><option value="">' . lang('All') . '</option>' . listMerchants($merchant_id) . '</select></td>
					<td><!--input type="text" name="affiliate_id" value="' . $affiliate_id . '" style="width: 80px;" /-->
					<div class="ui-widget">'
        . '<!-- name="affiliate_id" -->'
        . '<select id="combobox" ' . ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') . '>'
        . '<!--option value="">' . lang('Choose Affiliate') . '</option-->'
        . $listOfAffiliates
        . '</select>
								</div>
					</td>
					<td style="padding-left:20px"><input type="text" name="uid" value="' . $uid . '" style="width: 80px;" /></td>
					<td><select name="filter">
						<option value="">' . lang("All") . '</option>
						<option value="active_account" ' . ($filter == 'active_account' ? 'selected' : '') . '>' . lang("Active Accounts") . '</option>
						<option value="real_account" ' . ($filter == 'real_account' ? 'selected' : '') . '>' . lang("Real Accounts") . '</option>
						<option value="ftd" ' . ($filter == 'ftd' ? 'selected' : '') . '>' . lang("FTDs") . '</option>
					</select></td>
					<td><input type="submit" value="' . lang('View') . '" /></td>
				</tr>
			</table>
			</form>
			
			' . ($set->export ? '<div class="exportCSV" style="float:left"><a href="javascript:void(0);" class="testcsv"><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to CSV') . '" title="' . lang('Export to CSV') . '" align="absmiddle" /> <b>' . lang('Export to CSV') . '</b></a></div>' : '') . '
				<div class="exportCSV" style="float:left"><a href="javascript:void(0);" class="testexcel"><img border="0" src="' . $set->SSLprefix . 'images/excel.png" alt="' . lang('Export to XLS') . '" title="' . lang('Export to XLS') . '" align="absmiddle" /> <b>' . lang('Export to XLS') . '</b></a>
				</div><div class="ajaxloader" style="display:none;padding:3px 30px;"><img style="margin-left:10px" src="' . $set->SSLprefix . 'images/ajax-loader.gif"></div>
				' . getFavoritesHTML() . '
				<div style="clear:both"></div>
			
			</div>
			
			<div style="height: 20px;"></div>
			<div class="normalTableTitle"  class="table">' . lang('Report Results') . '<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="' . $set->SSLprefix . 'images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
//width 2200
$tableStr = '<table  class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="trafficTbl">
					<thead><tr  class="table-row">
					
						
						<th class="table-cell">' . lang('Refer URL') . '</th>
						<th  class="table-cell" style="text-align: center;">' . lang('Merchant ID') . '</th>
						<th class="table-cell" style="text-align: left;">' . lang('Merchant') . '</th>
						<th class="table-cell">' . lang('Last Click IP') . '</th>
						<th class="table-cell">' . lang('Last Click Country') . '</th>
						<th class="table-cell">' . lang('Affiliate ID') . '</th>
						<th class="table-cell header">' . lang('Affiliate Username') . '</th>
						<!--th class="table-cell">' . lang('Profile ID') . '</th>
						<th class="table-cell">' . lang('Profile Name') . '</th-->
						' . ($is_admin ? '<th class="table-cell">' . lang('Group ID') . '</th>' : '') . '
						<th  class="table-cell"style="text-align: center;">' . lang('All Time Clicks') . '</th>
						<th class="table-cell" style="text-align: center;">' . lang('All Time Views') . '</th>
						<th class="table-cell">' . lang(ptitle('Lead')) . '</th>
						<th class="table-cell">' . lang(ptitle('Demo')) . '</th>
						<th class="table-cell">' . lang(ptitle('Accounts')) . '</th>
						<th class="table-cell">' . lang('FTD') . '</th>
						<th class="table-cell">' . lang('FTD Amount') . '</th>
						<th class="table-cell">' . lang('RAW FTD') . '</th>
						<th class="table-cell">' . lang('RAW FTD Amount') . '</th>
						<th class="table-cell">' . lang('Total Deposits') . '</th>
						<th class="table-cell">' . lang('Deposit Amount') . '</th>
						<th class="table-cell">' . lang('Volume') . '</th>
						<th class="table-cell">' . lang('Bonus Amount') . '</th>
						<th class="table-cell">' . lang('Withdrawal Amount') . '</th>
						<th class="table-cell">' . lang('ChargeBack Amount') . '</th>
						<th class="table-cell">' . lang(ptitle('Net Revenue')) . '</th>
						' . ($set->deal_pnl == 1 ? '
						<th class="table-cell">' . lang(ptitle('PNL')) . '</th> ' : '') . '
						<th class="table-cell">' . lang('Commission') . '</th>
					</tr></thead>
					<tbody>
					' . $listReport . '
					</tbody>
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
											level = "' . $userlevel . '";
											type = "add";
											
											saveReportToMyFav(name, \'traffic\',user,level,type);
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
					
					$(".testcsv").on("click",function(){
						
						recs = "' . $set->total_records . '";
						if(recs > 1000){
								
								$.prompt("' . lang('There are so many records. It will take sometime to export the data. Do you still want to export?') . '", {
														top:200,
														title: "Export CSV?",
														buttons: { "' . lang('Yes') . '": true, "' . lang('Cancel') . '": false },
														submit: function(e,v,m,f){
															$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"' . $set->SSLprefix . 'images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>' . lang('Please be patient. Exporting data can take few minutes...') . '</span>");
															if(v){
																	$.ajax({
																		  method: "POST",
																		 url: "' . $set->SSLprefix . 'ajax/trafficReportExport.php",
																		data: { sql: "' . $ajaxSql . '" , where : "' . $where . '", merchant_id : "' . $merchant_id . '",affiliate_id : "' . $affiliate_id . '",uid : "' . $uid . '",banner_id:"' . $banner_id . '",unique_id:"' . $unique_id . '",from:"' . $from . '",to:"' . $to . '",display_type : "' . $display_type . '",filename:"' . $filename . '",format:"csv"}
																		})
																		  .done(function( filepath ) {
																				filedata = $.parseJSON(filepath);
																				window.location.href = "' . $set->SSLprefix . 'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
																				if(filedata.status == \'big\')
																				{
																						$.prompt("' . lang('File has been downloaded successfully. Because of limitation of excel, downloaded file contains only 65536 rows.') . '", {
																								top:200,
																								title: "Export CSV",
																								buttons: { "' . lang('OK') . '": true}
																						});
																				}
																			  	$(".ajaxloader").hide();
																		  });
															}
															else{
																	$(".ajaxloader").hide();
															}
														}
								});
						}
						else{
							$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"' . $set->SSLprefix . 'images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>' . lang('Please be patient. Exporting data can take few minutes...') . '</span>");
								$.ajax({
									  method: "POST",
									  url: "' . $set->SSLprefix . 'ajax/trafficReportExport.php",
									  data: { sql: "' . $ajaxSql . '" , where : "' . $where . '", merchant_id : "' . $merchant_id . '",affiliate_id : "' . $affiliate_id . '",uid : "' . $uid . '",banner_id:"' . $banner_id . '",unique_id:"' . $unique_id . '",from:"' . $from . '",to:"' . $to . '",display_type : "' . $display_type . '",filename:"' . $filename . '",format:"csv"}
									})
									  .done(function( filepath ) {
												filedata = $.parseJSON(filepath);
												window.location.href = "' . $set->SSLprefix . 'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
												$(".ajaxloader").hide();
										  
									  });
						}
					});
					
					$(".testexcel").on("click",function(){
						recs = "' . $set->total_records . '";
						if(recs > 1000){
								$.prompt("' . lang('There are so many records. It will take sometime to export the data. Do you still want to export?') . '", {
														top:200,
														title: "Are you Ready?",
														buttons: { "' . lang('Yes') . '": true, "' . lang('Cancel') . '": false },
														submit: function(e,v,m,f){
															if(v){
																$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"' . $set->SSLprefix . 'images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>' . lang('Please be patient. Exporting data can take few minutes...') . '</span>");
																$.ajax({
																	  method: "POST",
																	  url: "' . $set->SSLprefix . 'ajax/trafficReportExport.php",
																		data: { sql: "' . $ajaxSql . '" , where : "' . $where . '", merchant_id : "' . $merchant_id . '",affiliate_id : "' . $affiliate_id . '",uid : "' . $uid . '",banner_id:"' . $banner_id . '",unique_id:"' . $unique_id . '",from:"' . $from . '",to:"' . $to . '",display_type : "' . $display_type . '",filename:"' . $filename . '",format:"xlsx"}
																	})
																	  .done(function( filepath ) {
																				filedata = $.parseJSON(filepath);
																				
																				if(filedata.status == \'big\')
																				{
																						$.prompt("' . lang('File has been downloaded successfully. Because of limitation of excel, downloaded file contains only 65536 rows.') . '", {
																								top:200,
																								title: "Export Excel",
																								buttons: { "' . lang('OK') . '": true}
																						});
																				}
																				window.location.href = "' . $set->SSLprefix . 'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
																				$(".ajaxloader").hide();
																		  
																	  });
															}
															else{
																$(".ajaxloader").hide();
															}
														}
								});
						}
						else{
							$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"' . $set->SSLprefix . 'images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>' . lang('Please be patient. Exporting data can take few minutes...') . '</span>");
								$.ajax({
									  method: "POST",
									  url: "' . $set->SSLprefix . 'ajax/trafficReportExport.php",
									  data: { sql: "' . $ajaxSql . '" , where : "' . $where . '", merchant_id : "' . $merchant_id . '",affiliate_id : "' . $affiliate_id . '",uid : "' . $uid . '",banner_id:"' . $banner_id . '",unique_id:"' . $unique_id . '",from:"' . $from . '",to:"' . $to . '",display_type : "' . $display_type . '",filename:"' . $filename . '",format:"xlsx"}
									})
									  .done(function( filepath ) {
												filedata = $.parseJSON(filepath);
												window.location.href = "' . $set->SSLprefix . 'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
												$(".ajaxloader").hide();
										  
									  });
						}
					});
					
			
					
				
				});
				</script>
				';

//excelExporter($tableStr,'Traffic');
$set->content .= $tableStr . '</div>' . getURLPager();

//MODAL
$myReport = lang("Referral");
include "common/ReportFieldsModal.php";

theme();
?>