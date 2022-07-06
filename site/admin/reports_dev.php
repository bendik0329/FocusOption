<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

if (!isAdmin()) _goto('/admin/');

$from = strTodate($from);
$to = strTodate($to);
	
switch ($act) {
	default:
		$set->pageTitle = lang('Quick Summary Report');
		
		$l=-1;
		$sql="SELECT * FROM merchants WHERE valid='1' ORDER BY pos";
		$qq=function_mysql_query($sql,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			
			$fromDate = strtotime($from);
			$toDate = strtotime($to);
			
			if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
				else $diff = 0;
			
			if($display_type == "weekly") {
				$theFromDay = date("w",strtotime($from))+1;
				$theToDay = date("w",strtotime($to))+1;
				$daysUntilEndWeek = (7-$theFromDay);
				$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
				
				$diff = round($diff/8);
				if($theFromDay > $theToDay) $diff = $diff+1;
				}
			
			if ($display_type == "monthly") {
				$endOfthisMonth = date("t",strtotime($from));
				if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
				$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
				$date1 = strtotime($from);
				$date2 = strtotime($to);
				$months = 0;
				while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
				if($diff == 0) $lastDayOfMonth = $to;
				}
			
			for ($i=0;$i<=$diff;$i++) {
				unset($totalCom);
				// From Show on weekly
				if($newFrom) $showFrom = $newFrom; else $showFrom = $from;
				
				$searchInSql = "";
				if ($display_type == "daily") {
					$searchInSql = "= '".date("Y-m-d", strtotime($from." +".$i." day"))."'";
				} else if ($display_type == "weekly"){
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "weekly"){ 
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "monthly"){ 
					if($i==0){ // Monthly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
						$newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
						$newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} 
				} else { // If no Display_type 
					$searchInSql = "BETWEEN '".$from."' AND '".$to."'";
				}
				
				// To Show on weekly
				if($display_type == "weekly")  if($newTo) $showTo = $newTo; else $showTo = $endOfWeek;
				// To Show on monthly
				if($display_type == "monthly") if($newTo) $showTo = $newTo; else $showTo = $lastDayOfMonth;
				
				if($i==$diff) $showTo = $to;
			
				$l++;
				$totalCom=0;
				$ftd=0;
				$totalLeads = 0;
				$totalDemo = 0;
				$totalReal = 0;
				$ftd_amount['amount']=0;
				$bonus = 0;
				$withdrawal = 0;
				$chargeback = 0;
				$depositingAccounts = 0;
				$sumDeposits = 0;
				$volume = 0;
				$merchantName = strtolower($ww['name']);
				$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." merchant_id='".$ww['id']."' AND rdate ".$searchInSql."",__FILE__));
				$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE ".$globalWhere." rdate ".$searchInSql,__FILE__);
				while ($regww=mysql_fetch_assoc($regqq)) {
					if ($regww['type'] == "lead") $totalLeads++;
					if ($regww['type'] == "demo") $totalDemo++;
					if ($regww['type'] == "real") $totalReal++;
					}
				$ftd_amountqq=function_mysql_query("
				SELECT tb1.rdate, amount,trader_id,affiliate_id
				FROM data_sales  AS tb1
				WHERE ".$globalWhere." rdate ".$searchInSql." AND type='deposit' AND trader_id NOT IN 
					(SELECT trader_id FROM data_sales_".$merchantName." WHERE rdate < tb1.rdate AND type='deposit')
					GROUP BY trader_id",__FILE__);
				while ($totalftd=mysql_fetch_assoc($ftd_amountqq)) {
					$ftd++;
					$ftd_amount['amount'] += $totalftd['amount'];
					$totalCom += getCom($totalftd['affiliate_id'],$ww['id'],$totalftd['trader_id'],$from,$to,'deal');
					}
				
				$salesqq=function_mysql_query("SELECT type,amount FROM data_sales WHERE ".$globalWhere." rdate ".$searchInSql."",__FILE__);
				while ($salesww=mysql_fetch_assoc($salesqq)) {
					if ($salesww['type'] == "deposit") {
						$sumDeposits += $salesww['amount'];
						$depositingAccounts++;
						}
					if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
					if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
					if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
					if ($salesww['type'] == "volume") $volume += $salesww['volume'];
					}
				$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
				
				$filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showFrom));
				$filterTo = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showTo));
				
				$listReport .= '<tr>
						'.($display_type == "daily" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($from.' +'.$i.' Day')).'</td>' : '').'
						'.($display_type == "weekly" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)).'</td>' : '').'
						'.($display_type == "monthly" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)).'</td>' : '').'
						<td style="text-align: left;">'.$ww['name'].'</td>
						<td style="text-align: center;">'.@number_format($totalTraffic['totalViews'],0).'</td>
						<td style="text-align: center;">'.@number_format($totalTraffic['totalClicks'],0).'</td>
						<td style="text-align: center;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>
						<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>
						<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>
						<td style="text-align: center;">'.@price($totalCom/$totalTraffic['totalClicks']).'</td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>
						<td style="text-align: center;">'.price($ftd_amount['amount']).'</td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
						<td style="text-align: center;">'.price($sumDeposits).'</td>
						<td style="text-align: center;">'.price($volume).'</td>
						<td style="text-align: center;">'.price($bonus).'</td>
						<td style="text-align: center;">'.price($withdrawal).'</td>
						<td style="text-align: center;">'.price($chargeback).'</td>
						<td style="text-align: center;">'.price($netRevenue).'</td>
						<td style="text-align: center;">'.price($totalCom).'</td>
					</tr>';
					
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
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
				$totalComs += $totalCom;
					
				}
			}
			
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $l;
		
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form method="get">
				<table><tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Search Type').'</td>
						<td></td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV"><a href="'.$set->basepage.'?act=xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>' : '').'
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 2000px;">'.lang('Quick Summary Report').'</div>
		<div style="background: #F8F8F8;">
			<table width="2000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
				<thead><tr>
					'.($display_type ? '<th>'.lang('Period').'</th>' : '').'
					<th style="text-align: left;">'.lang('Merchant').'</th>
					<th>'.lang('Impressions').'</th>
					<th>'.lang('Clicks').'</th>
					<th>'.lang('Click Through Ratio (CTR)').'</th>
					<th>'.lang('Click to Account').'</th>
					<th>'.lang('Click to Sale').'</th>
					<th>EPC</th>
					<th>'.lang('Lead').'</th>
					<th>'.lang('Demo').'</th>
					<th>'.lang('Accounts').'</th>
					<th>'.lang('FTD').'</th>
					<th>'.lang('FTD Amount').'</th>
					<th>'.lang('Total Deposits').'</th>
					<th>'.lang('Deposits Amount').'</th>
					<th>'.lang('Volume').'</th>
					<th>'.lang('Bonus Amount').'</th>
					<th>'.lang('Withdrawal Amount').'</th>
					<th>'.lang('ChargeBack Amount').'</th>
					<th>'.lang('Net Revenue').'</th>
					<th>'.lang('Commission').'</th>
				</tr></thead><tfoot><tr>
					'.($display_type ? '<th></th>' : '').'
					<th style="text-align: left;"><b>'.lang('Total').':</b></th>
					<th>'.$totalImpressions.'</th>
					<th>'.$totalClicks.'</th>
					<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
					<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
					<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
					<th>'.@price($totalComs/$totalClicks).'</th>
					<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
					<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
					<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
					<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
					<th>'.price($totalFTDAmount).'</th>
					<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
					<th>'.price($totalDepositAmount).'</th>
					<th>'.price($totalVolume).'</th>
					<th>'.price($totalBonus).'</th>
					<th>'.price($totalWithdrawal).'</th>
					<th>'.price($totalChargeback).'</th>
					<th>'.price($totalNetRevenue).'</th>
					<th>'.price($totalComs).'</th>
				</tr></tfoot>
				<tbody>
				'.$listReport.'
			</table>
		</div>'.getPager();
		
		theme();
		break;
	
	case "traffic":
		$set->pageTitle = lang('Traffic Report');
		
		if ($merchant_id) $where = " AND id='".$merchant_id."'";
		if ($profile_id) $whereSites = " AND profile_id='".$profile_id."'";
		if ($banner_id) $whereSites .= " AND banner_id='".$banner_id."'";
		if ($affiliate_id) $whereSites .= " AND affiliate_id='".$affiliate_id."'";
		$sql = "SELECT * FROM merchants WHERE valid='1' ".$where." ORDER BY pos";
		$qq=function_mysql_query($sql,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			
			$fromDate = strtotime($from);
			$toDate = strtotime($to);
			
			if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
				else $diff = 0;
			
			if($display_type == "weekly") {
				$theFromDay = date("w",strtotime($from))+1;
				$theToDay = date("w",strtotime($to))+1;
				$daysUntilEndWeek = (7-$theFromDay);
				$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
				
				$diff = round($diff/8);
				if($theFromDay > $theToDay) $diff = $diff+1;
				}
			
			if ($display_type == "monthly") {
				$endOfthisMonth = date("t",strtotime($from));
				if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
				$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
				$date1 = strtotime($from);
				$date2 = strtotime($to);
				$months = 0;
				while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
				if($diff == 0) $lastDayOfMonth = $to;
				}
			
			for ($i=0;$i<=$diff;$i++) {
				unset($totalCom);
				// From Show on weekly
				if($newFrom) $showFrom = $newFrom; else $showFrom = $from;
				
				$searchInSql = "";
				if ($display_type == "daily") {
					$searchInSql = "= '".date("Y-m-d", strtotime($from." +".$i." day"))."'";
				} else if ($display_type == "weekly"){
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "weekly"){ 
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "monthly"){ 
					if($i==0){ // Monthly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
						$newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
						$newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} 
				} else { // If no Display_type 
					$searchInSql = "BETWEEN '".$from."' AND '".$to."'";
				}
				
				// To Show on weekly
				if($display_type == "weekly")  if($newTo) $showTo = $newTo; else $showTo = $endOfWeek;
				// To Show on monthly
				if($display_type == "monthly") if($newTo) $showTo = $newTo; else $showTo = $lastDayOfMonth;
				
				if($i==$diff) $showTo = $to;
				
				$l++;
				$ftd=0;
				$ftd_amount=0;
				$totalCom=0;
				$bonus=0;
				$withdrawal=0;
				$chargeback=0;
				$volume=0;
				$totalLeads=0;
				$sumDeposits=0;
				$depositingAccounts=0;
				$totalDemo=0;
				$totalReal=0;
				$merchantName = strtolower($ww['name']);
				$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." merchant_id='".$ww['id']."' ".$whereSites." AND rdate ".$searchInSql."",__FILE__));
				
				$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE ".$globalWhere." rdate ".$searchInSql." ".$whereSites,__FILE__);
				while ($regww=mysql_fetch_assoc($regqq)) {
					if ($regww['type'] == "lead") $totalLeads++;
					if ($regww['type'] == "demo") $totalDemo++;
					if ($regww['type'] == "real") $totalReal++;
					}
				
				$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT trader_id, tb1.rdate,amount,affiliate_id
				FROM data_sales AS tb1
				WHERE ".$globalWhere." rdate ".$searchInSql." AND type='deposit' ".$whereSites." AND trader_id NOT IN 
					(SELECT trader_id FROM data_sales WHERE type='deposit'  AND rdate < tb1.rdate GROUP BY trader_id) 
				GROUP BY trader_id",__FILE__);
				while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
					$ftd++;
					$ftd_amount += $totalftd['amount'];
					$totalCom += getCom($totalftd['affiliate_id'],$ww['id'],$totalftd['trader_id'],$from,$to,'deal');
					}
				$impression = @number_format($totalTraffic['totalViews'],0);
				$clicks = @number_format($totalTraffic['totalClicks'],0);

				$salesqq=function_mysql_query("SELECT type,amount FROM data_sales WHERE ".$globalWhere." rdate ".$searchInSql." ".$whereSites,__FILE__);
				while ($salesww=mysql_fetch_assoc($salesqq)) {
					if ($salesww['type'] == "deposit") {
						$sumDeposits += $salesww['amount'];
						$depositingAccounts++;
						}
					if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
					if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
					if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
					if ($salesww['type'] == "volume") $volume += $salesww['volume'];
					}
				$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
				$filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showFrom));
				$filterTo = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showTo));
				$listReport .= '<tr>
						'.($display_type == "daily" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($from.' +'.$i.' Day')).'</td>' : '').'
						'.($display_type == "weekly" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)).'</td>' : '').'
						'.($display_type == "monthly" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)).'</td>' : '').'
						<td style="text-align: left;">'.$ww['name'].'</td>
						<td style="text-align: center;">'.$impression.'</td>
						<td style="text-align: center;">'.$clicks.'</td>
						<td style="text-align: center;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>
						<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>
						<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>
						<td style="text-align: center;">'.@price($totalCom/$totalTraffic['totalClicks']).'</td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&merchant_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
						<td style="text-align: center;">'.price($ftd_amount).'</td>
						<td style="text-align: center;">'.price($sumDeposits).'</td>
						<td style="text-align: center;">'.price($volume).'</td>
						<td style="text-align: center;">'.price($bonus).'</td>
						<td style="text-align: center;">'.price($withdrawal).'</td>
						<td style="text-align: center;">'.price($chargeback).'</td>
						<td style="text-align: center;">'.price($netRevenue).'</td>
						<td style="text-align: center;">'.price($totalCom).'</td>
					</tr>';
				$totalImpressions += $impression;
				$totalClicks += $clicks;
				$totalLeadsAccounts += $totalLeads;
				$totalDemoAccounts += $totalDemo;
				$totalRealAccounts += $totalReal;
				$totalFTD += $ftd;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount;
				$totalDepositAmount += $sumDeposits;
				$totalVolume += $volume;
				$totalBonusAmount += $bonus;
				$totalWithdrawalAmount += $withdrawal;
				$totalChargeBackAmount += $chargeback;
				$totalNetRevenue += $netRevenue;
				$totalComs += $totalCom;
			}
		}
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $l;
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="traffic" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Search Type').'</td>
					<td>'.lang('Banner ID').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" style="width: 80px;" /></td>
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" style="width: 80px;" /></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			
			'.($set->export ? '<div class="exportCSV"><a href="'.$set->basepage.'?act=traffic_xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div> ' :'').'
			
			</div>
			
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2200px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">
				<table width="2200" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						'.($display_type ? '<th>'.lang('Period').'</th>' : '').'
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang('Click to Account').'</th>
						<th>'.lang('Click to Sale').'</th>
						<th>EPC</th>
						<th>'.lang('Lead').'</th>
						<th>'.lang('Demo').'</th>
						<th>'.lang('Accounts').'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('Deposits').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang('Net Revenue').'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th>'.price($totalFTDAmount).'</th>
						<th>'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th>'.price($totalBonusAmount).'</th>
						<th>'.price($totalWithdrawalAmount).'</th>
						<th>'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th>'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>
			</div>'.getPager();
		theme();
		break;
		
	
	case "banner":
		$set->pageTitle = lang('Creative Report');
		
		if ($merchant_id) $where = " AND merchant_id='".$merchant_id."'";
		if ($banner_id) $where = " AND banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND profile_id='".$profile_id."'";
		$sql = "SELECT *,SUM(views) AS totalViews,SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ".$where." GROUP BY banner_id";
		$qq=function_mysql_query($sql,__FILE__);
		
		while ($ww=mysql_fetch_assoc($qq)) {
			$bannerInfo=mysql_fetch_assoc(function_mysql_query("SELECT id,title,type FROM merchants_creative WHERE id='".$ww['banner_id']."'",__FILE__));
			// if (!$bannerInfo['id']) continue;
			if ($type AND $bannerInfo['type'] != $type) continue;
			$totalLeads=0;
			$totalDemo=0;
			$totalReal=0;
			$ftd=0;
			$ftd_amount=0;
			$depositingAccounts=0;
			$sumDeposits=0;
			$bonus=0;
			$cpaAmount=0;
			$withdrawal=0;
			$volume=0;
			$totalCom=0;
			
			$merchantww=dbGet($ww['merchant_id'],"merchants");
			
			$merchantName = strtolower($merchantww['name']);
			
			$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE ".$globalWhere." banner_id='".$bannerInfo['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
			while ($regww=mysql_fetch_assoc($regqq)) {
				if ($regww['type'] == "lead") $totalLeads++;
				if ($regww['type'] == "demo") $totalDemo++;
				if ($regww['type'] == "real") $totalReal++;
				}
			
			$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT trader_id, tb1.rdate,amount,affiliate_id
			FROM data_sales  AS tb1
			WHERE ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' AND banner_id='".$bannerInfo['id']."' AND trader_id NOT IN 
				(SELECT trader_id FROM data_sales  WHERE type='deposit'  AND rdate < tb1.rdate GROUP BY trader_id) 
			GROUP BY trader_id",__FILE__);
			while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
				$ftd++;
				$ftd_amount += $totalftd['amount'];
				$totalCom += getCom($totalftd['affiliate_id'],$merchantww['id'],$totalftd['trader_id'],$from,$to,'deal');
				}
			
			$salesqq=function_mysql_query("SELECT type,amount FROM data_sales  WHERE ".$globalWhere." banner_id='".$bannerInfo['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
			while ($salesww=mysql_fetch_assoc($salesqq)) {
				if ($salesww['type'] == "deposit") {
					$depositingAccounts++;
					$sumDeposits += $salesww['amount'];
					}
				if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
				if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
				if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
				if ($salesww['type'] == "volume") $volume += $salesww['volume'];
				}
			$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$bannerInfo['id'].'</td>
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="NewWin(\'/admin/creative.php?act=edit_banner&id='.$bannerInfo['id'].'\',\'editbanner_'.$bannerInfo['id'].'\',\'1000\',\'800\',\'1\');">'.lang('Edit').'</a></td>
				<td style="text-align: left;">'.($bannerInfo['id'] ? $bannerInfo['title'] : 'BANNER REMOVED').'</td>
				<td style="text-align: left;">'.$merchantww['name'].'</td>
				<td style="text-align: left;">'.$bannerInfo['type'].'</td>
				<td>'.@number_format($ww['totalViews'],0).'</td>
				<td>'.@number_format($ww['totalClicks'],0).'</td>
				<td>'.@number_format(($ww['totalClicks']/$ww['totalViews'])*100,2).' %</td>
				<td>'.@number_format(($totalReal/$ww['totalClicks'])*100,2).' %</td>
				<td>'.@number_format(($ftd/$ww['totalClicks'])*100,2).' %</td>
				<td>'.@price($totalCom/$ww['totalClicks']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=lead">'.$totalLeads.'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=demo">'.$totalDemo.'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=real">'.$totalReal.'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=ftd">'.$ftd.'</a></td>
				<td>'.price($ftd_amount).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
				<td>'.price($sumDeposits).'</td>
				<td style="text-align: center;">'.price($volume).'</td>
				<td>'.price($bonus).'</td>
				<td>'.price($withdrawal).'</td>
				<td>'.price($chargeback).'</td>
				<td style="text-align: center;">'.price($netRevenue).'</td>
				<td>'.price($totalCom).'</td>
			</tr>';
			
			$totalImpressions += $ww['totalViews'];
			$totalClicks += $ww['totalClicks'];
			$totalLeadsAccounts += $totalLeads;
			$totalDemoAccounts += $totalDemo;
			$totalRealAccounts += $totalReal;
			$totalFTD += $ftd;
			$totalDeposits += $depositingAccounts;
			$totalFTDAmount += $ftd_amount;
			$totalDepositAmount += $sumDeposits;
			$totalVolume += $volume;
			$totalBonusAmount += $bonus;
			$totalWithdrawalAmount += $withdrawal;
			$totalChargeBackAmount += $chargeback;
			$totalNetRevenue += $netRevenue;
			$totalComs += $totalCom;
			
			$l++;
			}
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->content .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="banner" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Banner ID').'</td>
					<td>'.lang('Creative Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" /></td>
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
						<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>
						<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>
						<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
						<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>
						<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
						<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV"><a href="'.$set->basepage.'?act=banner_xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" style="width: 2400px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">
				<table width="2400" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Actions').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Type').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang('Click to Account').'</th>
						<th>'.lang('Click to Sale').'</th>
						<th>EPC</th>
						<th>'.lang('Lead').'</th>
						<th>'.lang('Demo').'</th>
						<th>'.lang('Accounts').'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang('Net Revenue').'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>
			</div>
			'.getPager();
			
		theme();
		break;
		
	
	case "trader":
		$set->pageTitle = lang('Trader Report');
		$l=0;
		
		if ($affiliate_id) $where .= " AND ||affiliate_id='".$affiliate_id."'";
		if ($group_id) $where .= " AND ||group_id='".$group_id."'";
		if ($banner_id) $where .= " AND ||banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND ||profile_id='".$profile_id."'";
		if ($trader_id) $where .= " AND ||trader_id='".$trader_id."'";
		if ($country_id) $where .= " AND ||countryCode='".$country_id."'";
		if ($type != "ftd" AND $type != "deposit") $whereType = " AND ||type='".$type."'";
		
		if ($merchant_id) {
			$ww = dbGet($merchant_id,"merchants");
			if (!$ww['id']) _goto();
			$brokers_ids[] = $ww['id'];  
			$brokers[] = $ww['name'];
			} else {
			$qq=function_mysql_query("SELECT id,name FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$brokers_ids[] = $ww['id'];
				$brokers[] = $ww['name'];
				}
			}
			
			
			
		for ($i=0; $i<=count($brokers)-1; $i++) {
			$broker['name'] = $brokers[$i];
			
			if(isset($_REQUEST['steps'])){
				$steps = $_REQUEST['steps'];
			}else{
				$steps = 20;
			}
			
			if(isset($_REQUEST['currPage'])){
				$currPage = $_REQUEST['currPage']-1;
				if($currPage<0) 
					$currPage=0;
				$startFrom = $currPage*$steps;
			}else{
				$startFrom = 0;
			}
			
			$brName = strtolower($broker['name']);
			$where1 = str_replace('||','',$where);
			$whereType1 = str_replace('||','',$whereType);
			$query = '
			
			SELECT tbl.* FROM
			
			(SELECT 

			reg1.trader_id, 
			reg1.trader_alias, 
			reg1.rdate, 
			reg1.type, 
			countries.title AS country, 
			countries.code AS countryCode, 
			reg1.affiliate_id, 
			affiliates.username AS affiliate_un, 
			affiliates.id, 
			"1" AS merchant_id, 
			"RBOptions" AS merchant_name, 
			reg1.banner_id, 
			merchants_creative.title AS banner_title, 
			merchants_creative.type AS banner_type, 
			reg1.profile_id, 
			reg1.freeParam, 
			sales2.rdate AS ftd_date,
			sales2.amount AS ftd_amount,
			SUM(IF(sales1.type="deposit", 1, 0)) AS totalDeposits, 
			SUM(IF(sales1.type="deposit", sales1.amount, 0)) AS totalDepositsAmount,
			SUM(IF(sales1.type="volume", sales1.amount, 0)) AS totalVolumeAmount,
			SUM(IF(sales1.type="bonus", sales1.amount, 0)) AS totalBonusAmount,
			SUM(IF(sales1.type="withdrawal", sales1.amount, 0)) AS totalWithdrawalAmount,
			SUM(IF(sales1.type="chargeback", sales1.amount, 0)) AS totalChargebackAmount,

			ROUND(SUM(IF(sales1.type="deposit", sales1.amount, 0))-(SUM(IF(sales1.type="bonus", sales1.amount, 0))+SUM(IF(sales1.type="withdrawal", sales1.amount, 0))+SUM(IF(sales1.type="chargeback", sales1.amount, 0)))) AS netRevenue,

			SUM(IF(sales1.type="volume", 1, 0)) AS totalTrades,

			payments_details.reason,'.(!isset($_REQUEST['isAjax']) ? '
			
			(SELECT SUM(amount) FROM (SELECT * FROM (SELECT sales2.trader_id, sales2.rdate, sales2.amount FROM data_sales sales2 WHERE type="deposit" '.$where1.' ORDER BY rdate ASC)t1 GROUP BY t1.trader_id)t2 WHERE rdate BETWEEN "'.$from.'" AND "'.$to.'") AS sumFtd,
			(SELECT COUNT(amount) FROM (SELECT sales2.trader_id, sales2.rdate, sales2.amount FROM data_sales sales2 WHERE type="deposit" '.$where1.' ORDER BY rdate ASC)t2 WHERE rdate BETWEEN "'.$from.'" AND "'.$to.'") AS sumDeposits,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="deposit" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where1.') AS sumDepositsAmount,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="volume" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where1.') AS sumVolumeAmount,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="bonus" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where1.') AS sumBonusAmount,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="withdrawal" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where1.') AS sumWithdrawalAmount,
			(SELECT SUM(amount) FROM data_sales sales2 WHERE type="chargeback" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where1.') AS sumChargebackAmount,
			(SELECT COUNT(amount) FROM data_sales sales2 WHERE type="volume" AND rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where1.') AS sumTrades
			
			' : ' 
			
			0 AS sumFtd,
			0 AS sumDeposits,
			0 AS sumDepositsAmount,
			0 AS sumVolumeAmount,
			0 AS sumBonusAmount,
			0 AS sumWithdrawalAmount,
			0 AS sumChargebackAmount,
			0 AS sumTrades
			
			').'
			
			
			FROM 

			data_reg  reg1
			LEFT JOIN data_sales  sales1 ON reg1.trader_id=sales1.trader_id AND sales1.rdate BETWEEN "'.$from.'" AND "'.$to.'"
			LEFT JOIN merchants_creative ON reg1.banner_id=merchants_creative.id
			LEFT JOIN affiliates ON reg1.affiliate_id=affiliates.id

			LEFT JOIN (SELECT * FROM (SELECT sales2.trader_id, sales2.rdate, sales2.amount FROM data_sales  sales2 WHERE type="deposit" '.$where1.' ORDER BY rdate ASC)t1 GROUP BY t1.trader_id)sales2 ON sales1.trader_id=sales2.trader_id
			LEFT JOIN payments_details ON payments_details.trader_id=reg1.trader_id AND payments_details.merchant_id=1
			LEFT JOIN countries ON reg1.country=countries.code

			GROUP BY reg1.trader_id
			ORDER BY CAST(reg1.trader_id AS SIGNED) DESC
			
			)tbl 
			
			WHERE rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where1.' '.$whereType1.'
			
			LIMIT '.$startFrom.','.$steps.'
			
			
			';
			
			
			//die($query);
			
			
			
			$where2 = str_replace('||','reg1.',$where);
			$whereType2 = str_replace('||','reg1.',$whereType);
			
			
			if(!isset($_REQUEST['isAjax'])){
			
				$totalQuery = '
				
				SELECT COUNT(DISTINCT reg1.trader_id)

				FROM 

				data_reg  reg1
				LEFT JOIN data_sales  sales1 ON reg1.trader_id=sales1.trader_id
				LEFT JOIN merchants_creative ON reg1.banner_id=merchants_creative.id
				LEFT JOIN affiliates ON reg1.affiliate_id=affiliates.id

				LEFT JOIN (SELECT * FROM (SELECT sales2.trader_id, sales2.rdate, sales2.amount FROM data_sales  sales2 ORDER BY rdate ASC)t1 GROUP BY t1.trader_id)sales2 ON sales1.trader_id=sales2.trader_id
				
				WHERE reg1.rdate BETWEEN "'.$from.'" AND "'.$to.'" '.$where2.' '.$whereType2.'
				
				
				';
				
				//die($totalQuery);
				
				$totalResults = mysql_result(function_mysql_query($totalQuery,__FILE__),0,0);
				
			}
			
			$rows = function_mysql_query($query,__FILE__);
			
			$sumFtd = 0;
			
			$rowCount = 0;
			
			while($row = mysql_fetch_assoc($rows)){
				
				switch($row['type']){
					case "real":	$color="green";		break;
					case "red":		$color="demo";		break;
					case "black":	$color="lead";		break;
				}
				
				
				$totalCom = getCom($row['affiliate_id'],$brokers_ids[$i],$row['trader_id'],$from,$to,'deal');
					
					$listReport .= '<tr id="row'.$rowCount.'"><td>'.$row['trader_id'].'</td><td>'.$row['trader_alias'].'</td><td>'.date("d/m/Y", strtotime($row['rdate'])).'</td><td><span style="color: '.$color.';">'.$row['type'].'</span></td><td>'.$row['country'].'</td><td>'.$row['affiliate_id'].'</td><td><a href="/admin/affiliates.php?act=new&id='.$row['affiliate_id'].'" target="_blank">'.$row['affiliate_un'].'</a></td><td>'.$brokers_ids[$i].'</td><td>'.strtoupper($brokers[$i]).'</td><td style="text-align: left;">'.$row['banner_id'].'</td><td style="text-align: left;">'.$row['banner_title'].'</td><td>'.$row['banner_type'].'</td><td>'.$row['profile_id'].'</td><td>'.$row['freeParam'].'</td><td>'.$row['ftd_date'].'</td><td>'.price($row['ftd_amount']).'</td><td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$merchant_id.'&trader_id='.$row['trader_id'].'&type=deposit">'.$row['totalDeposits'].'</a></td><td>'.price($row['totalDepositsAmount']).'</td><td>'.price($row['totalVolumeAmount']).'</td><td>'.price($row['totalBonusAmount']).'</td><td>'.price($row['totalWithdrawalAmount']).'</td><td>'.price($row['totalChargebackAmount']).'</td><td>'.price($row['netRevenue']).'</td><td>'.$row['totalTrades'].'</td><td>'.price($totalCom).'</td><td>'.$row['reason'].'</td></tr>';
					
					/*
					$listReport .= '<tr id="row'.$rowCount.'">
						<td>'.$row['trader_id'].'</td>
						<td>'.$row['trader_alias'].'</td>
						<td>'.date("d/m/Y", strtotime($row['rdate'])).'</td>
						<td><span style="color: '.$color.';">'.$row['type'].'</span></td>
						<td>'.$row['country'].'</td>
						<td>'.$row['affiliate_id'].'</td>
						<td><a href="/admin/affiliates.php?act=new&id='.$row['affiliate_id'].'" target="_blank">'.$row['affiliate_un'].'</a></td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td style="text-align: left;">'.$row['banner_id'].'</td>
						<td style="text-align: left;">'.$row['banner_title'].'</td>
						<td>'.$row['banner_type'].'</td>
						<td>'.$row['profile_id'].'</td>
						<td>'.$row['freeParam'].'</td>
						<td>'.$row['ftd_date'].'</td>
						<td>'.price($row['ftd_amount']).'</td>
						<td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$merchant_id.'&trader_id='.$row['trader_id'].'&type=deposit">'.$row['totalDeposits'].'</a></td>
						<td>'.price($row['totalDepositsAmount']).'</td>
						<td>'.price($row['totalVolumeAmount']).'</td>
						<td>'.price($row['totalBonusAmount']).'</td>
						<td>'.price($row['totalWithdrawalAmount']).'</td>
						<td>'.price($row['totalChargebackAmount']).'</td>
						<td>'.price($row['netRevenue']).'</td>
						<td>'.$row['totalTrades'].'</td>
						<td>'.price($totalCom).'</td>
						<td>'.$row['reason'].'</td>
					</tr>';
					*/
					
					
					
				$rowCount++;
				
				
				$sumFtd 				= 	$row['sumFtd'];
				$sumDeposits 			=	$row['sumDeposits'];
				$sumDepositsAmount		=	$row['sumDepositsAmount'];
				$sumVolumeAmount		=	$row['sumVolumeAmount'];
				$sumBonusAmount			=	$row['sumBonusAmount'];
				$sumWithdrawalAmount	=	$row['sumWithdrawalAmount'];
				$sumChargebackAmount	=	$row['sumChargebackAmount'];
				$sumNetRevenue			=	$sumDepositsAmount-($sumBonusAmount+$sumWithdrawalAmount+$sumChargebackAmount);	//$row['netRevenue'];
				$sumTrades				=	$row['sumTrades'];
				$sumCom					+=	0;

				
				if (@!in_array($firstDeposit['trader_id'],$ftdExist)) $totalFTD += $ftdAmount;
				$ftdExist[] = $firstDeposit['trader_id'];
				$totalTotalDeposit += $total_deposits;
				$totalDepositAmount += $depositAmount;
				$totalVolumeAmount += $volumeAmount;
				$totalBonusAmount += $bonusAmount;
				$totalWithdrawalAmount += $withdrawalAmount;
				$totalChargeBackAmount += $chargebackAmount;
				$totalNetRevenue += $netRevenue;
				$totalTrades += $totalTraders;
				$totalTotalCom += $totalCom;
				
			/*
			die($query);
			
			
			if ($type AND $type != "ftd" AND $type != "real") $where .= " AND type='".$type."'";
			if ($type == "ftd") {
				// $qq=function_mysql_query("SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' GROUP BY trader_id ORDER BY id DESC",__FILE__);
				
				$qq=function_mysql_query("
				SELECT DISTINCT *
				FROM data_sales_".strtolower($broker['name'])." AS tb1
				WHERE ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' ".$where." AND trader_id NOT IN 
					(SELECT trader_id FROM data_sales_".strtolower($broker['name'])." WHERE type='deposit' AND rdate < tb1.rdate ".$where." GROUP BY trader_id) 
				GROUP BY trader_id",__FILE__);
				
				} else if ($type == "deposit") {
				$sql = "SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ORDER BY trader_id DESC"; //  ".($trader_id ? '' : 'GROUP BY trader_id')."
				// die($sql);
				$qq=function_mysql_query($sql,__FILE__);
				} else if ($type == "revenue") {
				$qq=function_mysql_query("SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' AND type='revenue' GROUP BY trader_id ORDER BY trader_id DESC",__FILE__);
				} else {
				$qq=function_mysql_query("SELECT * FROM data_reg_".strtolower($broker['name'])." WHERE 1 ".$where." ".$whereReg." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC",__FILE__);
				// die("SELECT * FROM data_reg_".strtolower($broker['name'])." WHERE 1 ".$where." AND rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC");
				}
			// die("SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC");
			while ($ww=mysql_fetch_assoc($qq)) {
				unset($marketInfo);
				$totalTraders = $total_deposits = $depositAmount = $ftdAmount = $volumeAmount = $bonusAmount = $withdrawalAmount = $chargebackAmount = $totalTraders = $revenueAmount = 0;
				$merchantInfo = dbGet($ww['merchant_id'],"merchants");
				// Get Trader Info Because he's FTD
				if ($type == "ftd" || $type == "deposit" || $type == "real" || $type == "revenue") {
					$traderInfo = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate,trader_alias,type,trader_id FROM data_reg_".strtolower($broker['name'])." WHERE trader_id='".$ww['trader_id']."'",__FILE__));
					$ww['trader_alias'] = $traderInfo['trader_alias'];
					if ($type != "deposit") $ww['rdate'] = $traderInfo['rdate'];
					$ww['type'] = $traderInfo['type'];
					}
				
				$bannerInfo = dbGet($ww['banner_id'],"merchants_creative");
				if ($ww['market_id'] > 0) $marketInfo = dbGet($ww['market_id'],"market_items");
				$firstDeposit = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate,amount,trader_id FROM data_sales_".strtolower($broker['name'])." WHERE trader_id='".$ww['trader_id']."' AND type='deposit' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY rdate ASC LIMIT 1",__FILE__));
				if ($type == "ftd" AND !$firstDeposit['id']) continue;
				$ftdAmount = $firstDeposit['amount'];
				$amountqq = function_mysql_query("SELECT type,amount FROM data_sales_".strtolower($broker['name'])." WHERE ".$globalWhere." trader_id='".$ww['trader_id']."' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY rdate ASC",__FILE__);
				while ($amountww=mysql_fetch_assoc($amountqq)) {
					if ($amountww['type'] == "deposit") {
						$depositAmount += $amountww['amount'];
						$total_deposits++;
						} else if ($amountww['type'] == "bonus") $bonusAmount += $amountww['amount'];
						else if ($amountww['type'] == "withdrawal") $withdrawalAmount += $amountww['amount'];
						else if ($amountww['type'] == "chargeback") $chargebackAmount += $amountww['amount'];
						else if ($amountww['type'] == "volume") {
							$volumeAmount += $amountww['amount'];
							$totalTraders++;
							}
					}
				$affInfo = dbGet($ww['affiliate_id'],"affiliates");
				if ($ww['type'] == "real") $color = 'green';
					else if ($ww['type'] == "demo") $color = 'red';
					else if ($ww['type'] == "lead") $color = 'black';
				$netRevenue = round($depositAmount-($bonusAmount+$withdrawalAmount+$chargebackAmount));
				
				$totalCom = getCom($ww['affiliate_id'],$brokers_ids[$i],$ww['trader_id'],$from,$to,'deal');

				$chkTrader = mysql_fetch_assoc(function_mysql_query("SELECT * FROM payments_details WHERE trader_id='".$ww['trader_id']."' AND merchant_id='".$brokers_ids[$i]."'",__FILE__));
				$listReport .= '<tr>
						<td>'.$ww['trader_id'].'</td>
						<td>'.$ww['trader_alias'].'</td>
						<td>'.($type == "deposit" ? date("d/m/Y", strtotime($traderInfo['rdate'])) : date("d/m/Y", strtotime($ww['rdate']))).'</td>
						<td><span style="color: '.$color.';">'.$ww['type'].'</span></td>
						<td>'.longCountry($ww['country']).'</td>
						<td>'.$ww['affiliate_id'].'</td>
						<td><a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td style="text-align: left;">'.$bannerInfo['id'].'</td>
						<td style="text-align: left;">'.$bannerInfo['title'].'</td>
						<td>'.$bannerInfo['type'].'</td>
						<td>'.$ww['profile_id'].'</td>
						<td>'.$ww['freeParam'].'</td>
						<td>'.($type == "deposit" ? date("d/m/Y", strtotime($ww['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y", strtotime($firstDeposit['rdate'])) : '')).'</td>
						<td>'.price($ftdAmount).'</td>
						<td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$merchant_id.'&trader_id='.$ww['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
						<td>'.price($depositAmount).'</td>
						<td>'.price($volumeAmount).'</td>
						<td>'.price($bonusAmount).'</td>
						<td>'.price($withdrawalAmount).'</td>
						<td>'.price($chargebackAmount).'</td>
						<td>'.price($netRevenue).'</td>
						<td>'.$totalTraders.'</td>
						<td>'.price($totalCom).'</td>
						<td>'.$chkTrader['reason'].'</td>
					</tr>';
					
					if (@!in_array($firstDeposit['trader_id'],$ftdExist)) $totalFTD += $ftdAmount;
					$ftdExist[] = $firstDeposit['trader_id'];
					$totalTotalDeposit += $total_deposits;
					$totalDepositAmount += $depositAmount;
					$totalVolumeAmount += $volumeAmount;
					$totalBonusAmount += $bonusAmount;
					$totalWithdrawalAmount += $withdrawalAmount;
					$totalChargeBackAmount += $chargebackAmount;
					$totalNetRevenue += $netRevenue;
					$totalTrades += $totalTraders;
					$totalTotalCom += $totalCom;
					
				$l++;
				}
				*/
			}
			
			if(isset($_REQUEST['isAjax'])){
				die($listReport);
			}
		}
		
		
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $totalResults;
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form action="'.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="trader" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						<td>'.lang('Affiliate ID').'</td>
						<td>'.lang('Banner ID').'</td>
						<td>'.lang('Trader ID').'</td>
						<td>'.lang('Group ID').'</td>
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from,$to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td>
							<select name="type" style="width: 100px;">
								<option value="real" '.($type == "real" ? 'selected' : '').'>'.lang('Accounts').'</option>
								<option value="lead" '.($type == "lead" ? 'selected' : '').'>'.lang('Lead').'</option>
								<option value="demo" '.($type == "demo" ? 'selected' : '').'>'.lang('Demo').'</option>
								<option value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option>
								<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>
								<option value="revenue" '.($type == "revenue" ? 'selected' : '').'>'.lang('Revenue').'</option>
							</select>
						</td>
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
				'.($set->export ? '<div class="exportCSV"><a href="'.$set->basepage.'?act=trader_xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2600px;">'.($merchant_id ? strtoupper($broker['name']) : '').' '.lang('Report Results').'</div>
			<div style="background: #F8F8F8;">
				<table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang('Trader ID').'</th>
						<th>'.lang('Trader Alias').'</th>
						<th>'.lang('Registration Date').'</th>
						<th>'.lang('Trader Status').'</th>
						<th>'.lang('Country').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Affiliate Username').'</th>
						<th>'.lang('Merchant ID').'</th>
						<th>'.lang('Merchant Name').'</th>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th>'.lang('Type').'</th>
						<th>'.lang('Profile ID').'</th>
						<th>'.lang('Param').'</th>
						<th>'.lang(($type == "deposit" ? 'Deposit Date' : 'First Deposit')).'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang(($type == "deposit" ? 'Deposit Amount' : 'Deposits Amount')).'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus  Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang('Net Revenue').'</th>
						<th>'.lang('Trades').'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Admin Notes').'</th>
					</tr></thead>
					<tfoot>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th style="text-align: left;">'.price($sumFtd).'</th>
						<th style="text-align: left;">'.$sumDeposits.'</th>
						<th style="text-align: left;">'.price($sumDepositsAmount).'</th>
						<th style="text-align: left;">'.price($sumVolumeAmount).'</th>
						<th style="text-align: left;">'.price($sumBonusAmount).'</th>
						<th style="text-align: left;">'.price($sumWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($sumChargebackAmount).'</th>
						<th style="text-align: left;">'.price($sumNetRevenue).'</th>
						<th style="text-align: left;">'.$sumTrades.'</th>
						<th style="text-align: left;">'.price($sumCom).'</th>
						<th></th>
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>
				<div id="ajaxLoading" style="display:none; z-index:500; top:0px; left:0px; position:fixed; width:100%; height:100%; background:#fff; opacity:0">
					<img src="images/ajax6.gif" style="width:85px; height:88px; position:absolute; left:50%; margin-left:-42px; top:50%; margin-top:-44px"/>
				</div>
				
				<script type="text/javascript">
					$( document ).ready(function() {
					
						function gotoPage(curr){
							$("#ajaxLoading").show();
							$("#ajaxLoading").animate({"opacity":"0.7"},200);
							$.ajax({
								url: "http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&isAjax=1&currPage="+curr+"&steps="+$(".pagesize").val(),
								dataType: "HTML",
							}).done(function(response) {
								if(response==""){
									$("#row0").parent().html("<tr id=\'row0\'><td>dfgsdfgsdfgsdfg</td></tr>");
								}
								$("#row0").parent().html(response);
								$("#ajaxLoading").animate({"opacity":"0"},200,function(){
									$("#ajaxLoading").hide();
								});
								$(".pagedisplay").val(currentPage);
							});
						}
						
						var currentPage = 1;
						var pages = Math.ceil('.$totalResults.'/$(".pagesize").val());
						$(".pagedisplay").val(currentPage);
						
						
						$(".pagedisplay").blur(function(e){
							currentPage = parseInt($(".pagedisplay").val());
							gotoPage(currentPage);
						});
						
						
						$(".first").click(function(e){
							currentPage = 1;
							gotoPage(currentPage);
						});
						
						$(".last").click(function(e){
							pages = Math.ceil('.$totalResults.'/$(".pagesize").val());
							currentPage = pages;
							gotoPage(currentPage);
						});
						
						$(".next").click(function(e){
							currentPage++;
							pages = Math.ceil('.$totalResults.'/$(".pagesize").val());
							if(currentPage>pages){
								currentPage-=1;
							}
							gotoPage(currentPage);
						});
						
						$(".prev").click(function(e){
							currentPage--;
							pages = Math.ceil('.$totalResults.'/$(".pagesize").val());
							if(currentPage<0){
								currentPage=0;
							}
							gotoPage(currentPage);
						});
						
						
					});
				</script>
			</div>'.getPager();
		
		theme();
		break;
	
	case "affiliate":
		$set->pageTitle = lang('Affiliate Report');
		if ($search) {
			if (is_numeric($affiliate_id)) $where .= " AND id='".$affiliate_id."'";
				else if ($affiliate_id) $where .= " AND lower(username) LIKE '%".trim(strtolower($affiliate_id))."%'";
			if ($group_id) $where .= " AND group_id='".$group_id."'";
			if ($merchant_id) $whereMer .= " AND id='".$merchant_id."'";
			// if (!$where AND !$affiliate_id) $limit = "LIMIT 50";
			$sql = "SELECT * FROM affiliates WHERE valid='1' ".$where." ORDER BY id DESC ".$limit;
			$qq=function_mysql_query($sql,__FILE__);
			$l=0;
			while ($ww=mysql_fetch_assoc($qq)) {
				$totalTraffic=0;
				$totalLeads=0;
				$totalDemo=0;
				$totalReal=0;
				$ftd=0;
				$volume=0;
				$bonus=0;
				$withdrawal=0;
				$chargeback=0;
				$revenue=0;
				$ftd_amount=0;
				$depositingAccounts=0;
				$sumDeposits=0;
				$netRevenue=0;
				$totalCom=0;
				$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." affiliate_id='".$ww['id']."' ".($merchant_id ? " AND merchant_id='".$merchant_id."'" : "")." AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__));
				$merchantqq=function_mysql_query("SELECT id,name FROM merchants WHERE valid='1' ".$whereMer,__FILE__);
				while ($merchantww=mysql_fetch_assoc($merchantqq)) {
					$merchantName = strtolower($merchantww['name']);
					
					$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE ".$globalWhere." affiliate_id='".$ww['id']."' AND type='real' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
					while ($regww=mysql_fetch_assoc($regqq)) {
						if ($regww['type'] == "lead") $totalLeads++;
						if ($regww['type'] == "demo") $totalDemo++;
						if ($regww['type'] == "real") $totalReal++;
						}
					
					$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT trader_id, tb1.rdate,amount
					FROM data_sales_".$merchantName." AS tb1
					WHERE ".$globalWhere." affiliate_id='".$ww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' AND trader_id NOT IN 
						(SELECT trader_id FROM data_sales_".$merchantName." WHERE affiliate_id='".$ww['id']."'AND type='deposit'  AND rdate < tb1.rdate GROUP BY trader_id) 
					GROUP BY trader_id",__FILE__);
					while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
						$ftd++;
						$ftd_amount += $totalftd['amount'];
						$totalCom += getCom($ww['id'],$merchantww['id'],$totalftd['trader_id'],$from,$to,'deal');
						}
					
					$salesqq=function_mysql_query("SELECT type,amount FROM data_sales_".$merchantName." WHERE ".$globalWhere." affiliate_id='".$ww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
					while ($salesww=mysql_fetch_assoc($salesqq)) {
						if ($salesww['type'] == "deposit") {
							$depositingAccounts++;
							$sumDeposits += $salesww['amount'];
							}
						if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
						if ($salesww['type'] == "revenue") $revenue += $salesww['amount'];
						if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
						if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
						if ($salesww['type'] == "volume") $volume += $salesww['amount'];
						}
					
					}
				if ($totalLeads <= 0 AND $totalDemo <= 0 AND $totalReal <= 0 AND $ftd <= 0 AND $totalTraffic['totalViews'] <= 0 AND $totalTraffic['totalClicks'] <= 0) continue; // Skip is affiliate data empty
				$totalFruad=mysql_num_rows(function_mysql_query("SELECT id FROM payments_details WHERE status='canceled' AND affiliate_id='".$ww['id']."'",__FILE__));
				$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
				$listReport .= '<tr>
						<td>'.$ww['id'].'</td>
						<td><a href="/admin/affiliates.php?act=new&id='.$ww['id'].'" target="_blank">'.$ww['username'].'</a></td>
						<td>'.$ww['first_name'].' '.$ww['last_name'].'</td>
						<td><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
						<td><a href="/out.php?refe='.urlencode($ww['website']).'" target="_blank">'.$ww['website'].'</a></td>
						<td style="text-align: center;">'.@number_format($totalTraffic['totalViews'],0).'</td>
						<td style="text-align: center;">'.@number_format($totalTraffic['totalClicks'],0).'</td>
						<td style="text-align: center;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>
						<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>
						<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>
						<td style="text-align: center;">'.@price($totalCom/$totalTraffic['totalClicks']).'</td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=demo">'.$totalDemo.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=real">'.$totalReal.'</a></td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=ftd">'.$ftd.'</a></td>
						<td>'.price($ftd_amount).'</td>
						<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&affiliate_id='.$ww['id'].'&type=deposit">'.$depositingAccounts.'</a></td>
						<td>'.price($sumDeposits).'</td>
						<td>'.price($volume).'</td>
						<td>'.price($bonus).'</td>
						<td>'.price($withdrawal).'</td>
						<td>'.price($chargeback).'</td>
						<td>'.@number_format(($totalFruad/$ftd)*100,2).'%</td>
						<td>'.price(($netRevenue)*(-1)).'</td>
						<td>'.price($totalCom).'</td>
						<td>'.listGroups($ww['group_id'],1).'</td>
					</tr>';
					
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalLeadsAccounts += $totalLeads;
				$totalDemoAccounts += $totalDemo;
				$totalRealAccounts += $totalReal;
				$totalFTD += $ftd;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount;
				$totalDepositAmount += $sumDeposits;
				$totalVolume += $volume;
				$totalBonus += $bonus;
				$totalWithdrawal += $withdrawal;
				$totalChargeBack += $chargeback;
				$totalNetRevenue += $netRevenue;
				$totalComs += $totalCom;
				$l++;
				}
			}
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $l;
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
		<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="affiliate" />
			<input type="hidden" name="search" value="1" />
			<table><tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td>'.lang('Group').'</td>
					<td>'.lang('Merchant').'</td>
					<td></td>
				</tr><tr>
				<td>'.timeFrame($from,$to).'</td>
				<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="alert_date" style="width: 100px;" /></td>
				<td><select name="group_id" style="width: 150px;"><option value="">'.lang('All Groups').'</option>'.listGroups($group_id).'</select></td>
				<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
				<td><input type="submit" value="'.lang('View').'" /></td>
			</tr></table>
			</form>
			
			'.($set->export ? '<div class="exportCSV"><a href="'.$set->basepage.'?act=affiliate_xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>' : '').'
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 2800px;">'.lang('Affiliate Report').'</div>
			<div style="background: #F8F8F8;">
				<table width="2800" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Username').'</th>
						<th>'.lang('Full Name').'</th>
						<th>'.lang('E-Mail').'</th>
						<th>'.lang('Website').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang('Click to Account').'</th>
						<th>'.lang('Click to Sale').'</th>
						<th>EPC</th>
						<th>'.lang('Lead').'</th>
						<th>'.lang('Demo').'</th>
						<th>'.lang('Accounts').'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang('Affiliate Risk').'</th>
						<th>'.lang('Net Revenue').'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Group').'</th>
					</tr></thead><tfoot><tr>
						<th><b>'.lang('Total').':</b></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th>'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th>'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th>'.price($totalBonus).'</th>
						<th>'.price($totalWithdrawal).'</th>
						<th>'.price($totalChargeBack).'</th>
						<th></th>
						<th>'.price($totalNetRevenue).'</th>
						<th>'.price($totalComs).'</th>
						<th></th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>
			</div>'.getPager();
		
		theme();
		break;
	
	case "group":
		$set->pageTitle = lang('Groups Report');
		$sql="SELECT * FROM groups ORDER BY id DESC";
		$qq=function_mysql_query($sql,__FILE__);
		for ($i = 0; $i <= mysql_num_rows($qq); $i++){
			$ww = mysql_fetch_assoc($qq);
			
			if($ww['id'] != $group_id AND $group_id) continue;
			
			$l++;
			
			$merchantqq=function_mysql_query("SELECT id,name FROM merchants WHERE valid='1'",__FILE__);
			while ($merchantww=mysql_fetch_assoc($merchantqq)) {
				
				$fromDate = strtotime($from);
				$toDate = strtotime($to);
				
				if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
					else $diff = 0;
				
				if($display_type == "weekly") {
					$theFromDay = date("w",strtotime($from))+1;
					$theToDay = date("w",strtotime($to))+1;
					$daysUntilEndWeek = (7-$theFromDay);
					$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
					
					$diff = round($diff/8);
					if($theFromDay > $theToDay) $diff = $diff+1;
					}
				
				if ($display_type == "monthly") {
					$endOfthisMonth = date("t",strtotime($from));
					if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
					$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
					$date1 = strtotime($from);
					$date2 = strtotime($to);
					$months = 0;
					while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
					if($diff == 0) $lastDayOfMonth = $to;
					}
				
				for ($n=0;$n<=$diff;$n++) {
					$totalTraffic=0;
					$totalLeads=0;
					$totalDemo=0;
					$totalReal=0;
					$ftd=0;
					$ftd_amount=0;
					$depositingAccounts=0;
					$sumDeposits=0;
					$revenue=0;
					$volume=0;
					$bonus=0;
					$withdrawal=0;
					$chargeback=0;
					$netRevenue=0;
					$totalCom=0;
					// From Show on weekly
					if($newFrom) $showFrom = $newFrom; else $showFrom = $from;
					
					$searchInSql = "";
					if ($display_type == "daily") {
						$searchInSql = "= '".date("Y-m-d", strtotime($from." +".$n." day"))."'";
					} else if ($display_type == "weekly"){
						if ($n==0) { // Weekly First Loop
							$searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
							$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
						} else if ($n == $diff) { // Last Loop - Last week
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						} else { // Else Loops
							$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						}
					} else if ($display_type == "weekly"){ 
						if($n==0){ // Weekly First Loop
							$searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
							$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
						} else if ($n == $diff) { // Last Loop - Last week
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						} else { // Else Loops
							$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						}
					} else if ($display_type == "monthly"){ 
						if($n==0){ // Monthly First Loop
							$searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
							$newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
						} else if ($n == $diff) { // Last Loop - Last week
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						} else { // Else Loops
							$numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
							$newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						} 
					} else { // If no Display_type 
						$searchInSql = "BETWEEN '".$from."' AND '".$to."'";
					}
					
					// To Show on weekly
					if ($display_type == "weekly")  if($newTo) $showTo = $newTo; else $showTo = $endOfWeek;
					// To Show on monthly
					if ($display_type == "monthly") if($newTo) $showTo = $newTo; else $showTo = $lastDayOfMonth;
					
					if ($n==$diff) $showTo = $to;
					
					$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE group_id='".$ww['id']."' AND rdate ".$searchInSql."",__FILE__));
					$merchantName = strtolower($merchantww['name']);
					
					$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE group_id='".$ww['id']."' AND type='real' AND rdate ".$searchInSql,__FILE__);
					while ($regww=mysql_fetch_assoc($regqq)) {
						if ($regww['type'] == "lead") $totalLeads++;
						if ($regww['type'] == "demo") $totalDemo++;
						if ($regww['type'] == "real") $totalReal++;
						}
						
					$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT tb1.trader_id, tb1.rdate,tb1.amount,tb1.affiliate_id
					FROM data_sales_".$merchantName." AS tb1
					WHERE group_id='".$ww['id']."' AND rdate ".$searchInSql." AND type='deposit' AND trader_id NOT IN 
						(SELECT trader_id FROM data_sales_".$merchantName." WHERE type='deposit' AND rdate < tb1.rdate GROUP BY trader_id) 
					GROUP BY trader_id",__FILE__);
					while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
						$ftd++;
						$ftd_amount += $totalftd['amount'];
						$totalCom += getCom($totalftd['affiliate_id'],$merchantww['id'],$totalftd['trader_id'],$from,$to,'deal');
						}
					
					$salesqq=function_mysql_query("SELECT type,amount,affiliate_id,trader_id FROM data_sales_".$merchantName." WHERE group_id='".($ww['id'] ? $ww['id'] : '0')."' AND rdate ".$searchInSql."",__FILE__);
					while ($salesww=mysql_fetch_assoc($salesqq)) {
						if ($salesww['type'] == "deposit") {
							$sumDeposits += $salesww['amount'];
							$depositingAccounts++;
							$totalCom += getCom($salesww['affiliate_id'],$merchantww['id'],$salesww['trader_id'],$from,$to,'deal');
							}
						if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
						if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
						if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
						if ($salesww['type'] == "volume") $volume += $salesww['amount'];
						}
					
					$filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$n.' Day') : strtotime($showFrom));
					$filterTo = ($display_type == "daily" ? strtotime($from.' +'.$n.' Day') : strtotime($showTo));
					
					$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
					
					$listReport .= '<tr>
					'.($display_type == "daily" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($from.' +'.$n.' Day')).'</td>' : '').'
					'.($display_type == "weekly" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)).'</td>' : '').'
					'.($display_type == "monthly" ? '<td style="text-align: center;">'.date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)).'</td>' : '').'
					<td>'.($ww['id'] ? $ww['id'] : '0').'</td>
					<td>'.($ww['title'] ? $ww['title'] : lang('General')).'</td>
					<td>'.@number_format($totalTraffic['totalViews'],0).'</td>
					<td>'.@number_format($totalTraffic['totalClicks'],0).'</td>
					<td>'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).' %</td>
					<td>'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>
					<td>'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>
					<td>'.@price($totalCom/$totalTraffic['totalClicks']).'</td>
					<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=lead">'.$totalLeads.'</a></td>
					<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=demo">'.$totalDemo.'</a></td>
					<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=real">'.$totalReal.'</a></td>
					<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=ftd">'.$ftd.'</a></td>
					<td>'.price($ftd_amount).'</td>
					<td style="text-align: center;"><a href="/admin/reports.php?act=trader&from='.($display_type ? date("d/m/Y", $filterFrom) : date("d/m/Y", strtotime($from))).'&to='.($display_type ? date("d/m/Y", $filterTo) : date("d/m/Y", strtotime($to))).'&group_id='.(!$ww['id'] ? '0' : $ww['id']).'&type=deposit">'.$depositingAccounts.'</a></td>
					<td>'.price($sumDeposits).'</td>
					<td>'.price($volume).'</td>
					<td>'.price($bonus).'</td>
					<td>'.price($withdrawal).'</td>
					<td>'.price($chargeback).'</td>
					<td>'.price($netRevenue).'</td>
					<td>'.price($totalCom).'</td>
				</tr>';
				
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalLeadsAccounts += $totalLeads;
				$totalDemoAccounts += $totalDemo;
				$totalRealAccounts += $totalReal;
				$totalFTD += $ftd;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount;
				$totalDepositAmount += $sumDeposits;
				$totalVolume += $volume;
				$totalBonus += $bonus;
				$totalWithdrawal += $withdrawal;
				$totalChargeBack += $chargeback;
				$totalNetRevenue += $netRevenue;
				$totalComs += $totalCom;
				$l++;
				
				}
			}
		}
			
			
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="group" />
			<table>
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Group').'</td>
					<td>'.lang('Search Type').'</td>
					<td></td>
				</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="group_id" style="width: 150px;"><option value="">'.lang('General').'</option>'.listGroups($group_id).'</select></td>
					<td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV"><a href="'.$set->basepage.'?act=group_xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>' : '').'
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 2000px;">'.lang('Affiliate Report').'</div>
			<div style="background: #F8F8F8;">
				<table width="2000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						'.($display_type ? '<th>'.lang('Period').'</th>' : '').'
						<th>'.lang('Group ID').'</th>
						<th>'.lang('Group Name').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang('Click to Account').'</th>
						<th>'.lang('Click to Sale').'</th>
						<th>EPC</th>
						<th>'.lang('Lead').'</th>
						<th>'.lang('Demo').'</th>
						<th>'.lang('Accounts').'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang('Net Revenue').'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th><b>'.lang('Total').':</b></th>
						<th></th>
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th>'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th>'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th>'.price($totalBonus).'</th>
						<th>'.price($totalWithdrawal).'</th>
						<th>'.price($totalChargeBack).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th>'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>
			</div>'.getPager();
		
		theme();
		break;
		
	// -----------------------------------------------------------------------------------------------------------------------------------------
	// -----------------------------------------------------------------------------------------------------------------------------------------
		
	case "xml":
		
		if ($display_type) $csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Period'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Merchant'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Impressions'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Clicks'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click Through Ratio (CTR)'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Account'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Sale'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'EPC');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Lead'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Demo'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Accounts'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Total Deposits'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Deposits Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Volume'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Bonus Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Withdrawal Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('ChargeBack Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Net Revenue'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Commission'));
		
		if ($merchant_id) $where = " AND id='".$merchant_id."'";
		if ($profile_id) $whereSites = " AND profile_id='".$profile_id."'";
		if ($banner_id) $whereSites .= " AND banner_id='".$banner_id."'";
		if ($affiliate_id) $whereSites .= " AND affiliate_id='".$affiliate_id."'";
		
		$l=-1;
		$sql="SELECT * FROM merchants WHERE valid='1' ORDER BY pos";
		$qq=function_mysql_query($sql,__FILE__);
		$k=1;
		while ($ww=mysql_fetch_assoc($qq)) {
			
			$fromDate = strtotime($from);
			$toDate = strtotime($to);
			
			if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
				else $diff = 0;
			
			if($display_type == "weekly") {
				$theFromDay = date("w",strtotime($from))+1;
				$theToDay = date("w",strtotime($to))+1;
				$daysUntilEndWeek = (7-$theFromDay);
				$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
				
				$diff = round($diff/8);
				if($theFromDay > $theToDay) $diff = $diff+1;
				}
			
			if ($display_type == "monthly") {
				$endOfthisMonth = date("t",strtotime($from));
				if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
				$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
				$date1 = strtotime($from);
				$date2 = strtotime($to);
				$months = 0;
				while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
				if($diff == 0) $lastDayOfMonth = $to;
				}
			
			for ($i=0;$i<=$diff;$i++) {
				unset($totalCom);
				// From Show on weekly
				if($newFrom) $showFrom = $newFrom; else $showFrom = $from;
				
				$searchInSql = "";
				if ($display_type == "daily") {
					$searchInSql = "= '".date("Y-m-d", strtotime($from." +".$i." day"))."'";
				} else if ($display_type == "weekly"){
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "weekly"){ 
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "monthly"){ 
					if($i==0){ // Monthly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
						$newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
						$newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} 
				} else { // If no Display_type 
					$searchInSql = "BETWEEN '".$from."' AND '".$to."'";
				}
				
				// To Show on weekly
				if($display_type == "weekly")  if($newTo) $showTo = $newTo; else $showTo = $endOfWeek;
				// To Show on monthly
				if($display_type == "monthly") if($newTo) $showTo = $newTo; else $showTo = $lastDayOfMonth;
				
				if($i==$diff) $showTo = $to;
			
				$l++;
				$totalCom=0;
				$ftd=0;
				$totalLeads = 0;
				$totalDemo = 0;
				$totalReal = 0;
				$ftd_amount['amount']=0;
				$volume = 0;
				$bonus = 0;
				$withdrawal = 0;
				$chargeback = 0;
				$netRevenue = 0;
				$depositingAccounts = 0;
				$sumDeposits = 0;
				$merchantName = strtolower($ww['name']);
				$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." merchant_id='".$ww['id']."' AND rdate ".$searchInSql."",__FILE__));
				$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE ".$globalWhere." rdate ".$searchInSql,__FILE__);
				while ($regww=mysql_fetch_assoc($regqq)) {
					if ($regww['type'] == "lead") $totalLeads++;
					if ($regww['type'] == "demo") $totalDemo++;
					if ($regww['type'] == "real") $totalReal++;
					}
				$ftd_amountqq=function_mysql_query("
				SELECT tb1.rdate, amount,trader_id,affiliate_id
				FROM data_sales_".$merchantName." AS tb1
				WHERE ".$globalWhere." rdate ".$searchInSql." AND type='deposit' AND trader_id NOT IN 
					(SELECT trader_id FROM data_sales_".$merchantName." WHERE rdate < tb1.rdate AND type='deposit')
					GROUP BY trader_id",__FILE__);
				while ($totalftd=mysql_fetch_assoc($ftd_amountqq)) {
					$ftd++;
					$ftd_amount['amount'] += $totalftd['amount'];
					$totalCom += getCom($totalftd['affiliate_id'],$ww['id'],$totalftd['trader_id'],$from,$to,'deal');
					}
				
				$salesqq=function_mysql_query("SELECT type,amount FROM data_sales_".$merchantName." WHERE ".$globalWhere." rdate ".$searchInSql."",__FILE__);
				while ($salesww=mysql_fetch_assoc($salesqq)) {
					if ($salesww['type'] == "deposit") {
						$sumDeposits += $salesww['amount'];
						$depositingAccounts++;
						}
					if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
					if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
					if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
					if ($salesww['type'] == "volume") $volume += $salesww['volume'];
					}
				
				$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
				
				if ($display_type == "daily") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($from.' +'.$i.' Day')));
				if ($display_type == "weekly") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)));
				if ($display_type == "monthly") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['name']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalTraffic['totalViews']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalTraffic['totalClicks']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalReal/$totalTraffic['totalClicks'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($ftd/$totalTraffic['totalClicks'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalCom/$totalTraffic['totalClicks']),2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalLeads);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalDemo);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalReal);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd_amount['amount']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $depositingAccounts);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $sumDeposits);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $volume);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bonus);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $withdrawal);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $chargeback);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $netRevenue);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalCom);
				$k++;
				}
			}
		
		break;
	
	case "traffic_xml":
		
		if ($display_type) $csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Period'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Merchant'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Impressions'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Clicks'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click Through Ratio (CTR)'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Account'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Sale'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'EPC');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Lead'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Demo'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Accounts'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Deposits'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Deposits Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Volume'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Bonus Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Withdrawal Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('ChargeBack Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Net Revenue'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Commission'));
		
		if ($merchant_id) $where = " AND id='".$merchant_id."'";
		if ($profile_id) $whereSites = " AND profile_id='".$profile_id."'";
		if ($banner_id) $whereSites .= " AND banner_id='".$banner_id."'";
		if ($affiliate_id) $whereSites .= " AND affiliate_id='".$affiliate_id."'";
		$sql = "SELECT * FROM merchants WHERE valid='1' ".$where." ORDER BY pos";
		$qq=function_mysql_query($sql,__FILE__);
		$k=1;
		while ($ww=mysql_fetch_assoc($qq)) {
			
			$fromDate = strtotime($from);
			$toDate = strtotime($to);
			
			if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
				else $diff = 0;
			
			if($display_type == "weekly") {
				$theFromDay = date("w",strtotime($from))+1;
				$theToDay = date("w",strtotime($to))+1;
				$daysUntilEndWeek = (7-$theFromDay);
				$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
				
				$diff = round($diff/8);
				if($theFromDay > $theToDay) $diff = $diff+1;
				}
			
			if ($display_type == "monthly") {
				$endOfthisMonth = date("t",strtotime($from));
				if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
				$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
				$date1 = strtotime($from);
				$date2 = strtotime($to);
				$months = 0;
				while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
				if($diff == 0) $lastDayOfMonth = $to;
				}
			
			for ($i=0;$i<=$diff;$i++) {
				unset($totalCom);
				// From Show on weekly
				if($newFrom) $showFrom = $newFrom; else $showFrom = $from;
				
				$searchInSql = "";
				if ($display_type == "daily") {
					$searchInSql = "= '".date("Y-m-d", strtotime($from." +".$i." day"))."'";
				} else if ($display_type == "weekly"){
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "weekly"){ 
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "monthly"){ 
					if($i==0){ // Monthly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
						$newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
						$newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} 
				} else { // If no Display_type 
					$searchInSql = "BETWEEN '".$from."' AND '".$to."'";
				}
				
				// To Show on weekly
				if($display_type == "weekly")  if($newTo) $showTo = $newTo; else $showTo = $endOfWeek;
				// To Show on monthly
				if($display_type == "monthly") if($newTo) $showTo = $newTo; else $showTo = $lastDayOfMonth;
				
				if($i==$diff) $showTo = $to;
				
				$l++;
				$ftd=0;
				$ftd_amount=0;
				$totalCom=0;
				$bonus=0;
				$withdrawal=0;
				$chargeback=0;
				$volume=0;
				$totalLeads=0;
				$sumDeposits=0;
				$depositingAccounts=0;
				$totalDemo=0;
				$totalReal=0;
				$merchantName = strtolower($ww['name']);
				$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." merchant_id='".$ww['id']."' ".$whereSites." AND rdate ".$searchInSql."",__FILE__));
				
				$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE ".$globalWhere." rdate ".$searchInSql." ".$whereSites,__FILE__);
				while ($regww=mysql_fetch_assoc($regqq)) {
					if ($regww['type'] == "lead") $totalLeads++;
					if ($regww['type'] == "demo") $totalDemo++;
					if ($regww['type'] == "real") $totalReal++;
					}
				
				$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT trader_id, tb1.rdate,amount,affiliate_id
				FROM data_sales_".$merchantName." AS tb1
				WHERE ".$globalWhere." rdate ".$searchInSql." AND type='deposit' ".$whereSites." AND trader_id NOT IN 
					(SELECT trader_id FROM data_sales_".$merchantName." WHERE type='deposit'  AND rdate < tb1.rdate GROUP BY trader_id) 
				GROUP BY trader_id",__FILE__);
				while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
					$ftd++;
					$ftd_amount += $totalftd['amount'];
					$totalCom += getCom($totalftd['affiliate_id'],$ww['id'],$totalftd['trader_id'],$from,$to,'deal');
					}

				$salesqq=function_mysql_query("SELECT type,amount FROM data_sales_".$merchantName." WHERE ".$globalWhere." rdate ".$searchInSql." ".$whereSites,__FILE__);
				while ($salesww=mysql_fetch_assoc($salesqq)) {
					if ($salesww['type'] == "deposit") {
						$sumDeposits += $salesww['amount'];
						$depositingAccounts++;
						}
					if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
					if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
					if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
					if ($salesww['type'] == "volume") $volume += $salesww['volume'];
					}
				if ($display_type == "daily") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($from.' +'.$i.' Day')));
				if ($display_type == "weekly") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)));
				if ($display_type == "monthly") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['name']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255',number_format($totalTraffic['totalViews'],0));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', number_format($totalTraffic['totalClicks'],0));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @number_format(($totalReal/$totalTraffic['totalClicks'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @number_format(($ftd/$totalTraffic['totalClicks'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @number_format(($totalCom/$totalTraffic['totalClicks']),2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalLeads);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalDemo);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalReal);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $depositingAccounts);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd_amount);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $sumDeposits);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $volume);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bonus);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $withdrawal);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $chargeback);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $netRevenue);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalCom);
				$k++;
				}
			}
		break;
	
	case "banner_xml":
		
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Creative ID'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Creative Name'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Merchant'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Type'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Impressions'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Clicks'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click Through Ratio (CTR)'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Account'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Sale'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'EPC');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Lead'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Demo'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Accounts'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Total Deposits'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Deposits Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Volume'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Bonus Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Withdrawal Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('ChargeBack Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Net Revenue'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Commission'));

		$k=1;
		if ($merchant_id) $where = " AND merchant_id='".$merchant_id."'";
		if ($banner_id) $where = " AND banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND profile_id='".$profile_id."'";
		$sql = "SELECT *,SUM(views) AS totalViews,SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ".$where." GROUP BY banner_id";
		$qq=function_mysql_query($sql,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$bannerInfo=mysql_fetch_assoc(function_mysql_query("SELECT id,title,type FROM merchants_creative WHERE id='".$ww['banner_id']."'",__FILE__));
			// if (!$bannerInfo['id']) continue;
			if ($type AND $bannerInfo['type'] != $type) continue;
			$totalLeads=0;
			$totalDemo=0;
			$totalReal=0;
			$ftd=0;
			$ftd_amount=0;
			$depositingAccounts=0;
			$sumDeposits=0;
			$volume=0;
			$bonus=0;
			$cpaAmount=0;
			$withdrawal=0;
			$netRevenue=0;
			$totalCom=0;
			
			$merchantww=dbGet($ww['merchant_id'],"merchants");
			
			$merchantName = strtolower($merchantww['name']);
			
			$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE ".$globalWhere." banner_id='".$bannerInfo['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
			while ($regww=mysql_fetch_assoc($regqq)) {
				if ($regww['type'] == "lead") $totalLeads++;
				if ($regww['type'] == "demo") $totalDemo++;
				if ($regww['type'] == "real") $totalReal++;
				}
			
			$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT trader_id, tb1.rdate,amount,affiliate_id
			FROM data_sales_".$merchantName." AS tb1
			WHERE ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' AND banner_id='".$bannerInfo['id']."' AND trader_id NOT IN 
				(SELECT trader_id FROM data_sales_".$merchantName." WHERE type='deposit'  AND rdate < tb1.rdate GROUP BY trader_id) 
			GROUP BY trader_id",__FILE__);
			while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
				$ftd++;
				$ftd_amount += $totalftd['amount'];
				$totalCom += getCom($totalftd['affiliate_id'],$merchantww['id'],$totalftd['trader_id'],$from,$to,'deal');
				}
			
			$salesqq=function_mysql_query("SELECT type,amount FROM data_sales_".$merchantName." WHERE ".$globalWhere." banner_id='".$bannerInfo['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
			while ($salesww=mysql_fetch_assoc($salesqq)) {
				if ($salesww['type'] == "deposit") {
					$depositingAccounts++;
					$sumDeposits += $salesww['amount'];
					}
				if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
				if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
				if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
				if ($salesww['type'] == "volume") $volume += $salesww['volume'];
				}
			
			$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
			
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bannerInfo['id']);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', ($bannerInfo['id'] ? $bannerInfo['title'] : 'BANNER REMOVED'));
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $merchantww['name']);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bannerInfo['type']);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round($ww['totalViews'],0));
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round($ww['totalClicks'],0));
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($ww['totalClicks']/$ww['totalViews'])*100,2));
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalReal/$ww['totalClicks'])*100,2));
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($ftd/$ww['totalClicks'])*100,2));
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalCom/$ww['totalClicks']),2));
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalLeads);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalDemo);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalReal);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd_amount);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $depositingAccounts);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $sumDeposits);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $volume);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bonus);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $withdrawal);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $chargeback);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $netRevenue);
			$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalCom);
			$k++;
			}
		
		break;
		
	case "trader_xml":
		
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Trader ID');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Trader Alias');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Registration Date');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Trader Status');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Country');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Affiliate ID');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Affiliate Username');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Merchant ID');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Merchant Name');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Creative ID');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Creative Name');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Type');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Profile ID');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Param');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', ($type == "deposit" ? 'Deposit Date' : 'First Deposit'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'FTD Amount');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Total Deposits');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', ($type == "deposit" ? 'Deposit Amount' : 'Deposits Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Volume');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Bonus  Amount');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Withdrawal Amount');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'ChargeBack Amount');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Net Revenue');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Trades');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Commission');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'Admin Notes');
		
		if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."'";
		if ($group_id) $where .= " AND group_id='".$group_id."'";
		if ($banner_id) $where .= " AND banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND profile_id='".$profile_id."'";
		if ($trader_id) $where .= " AND trader_id='".$trader_id."'";
		if ($country_id) $whereReg = " AND country='".$country_id."'";
		if ($type != "ftd" AND $type != "deposit") $where .= " AND type='".$type."'";
		
		if ($merchant_id) {
			$ww = dbGet($merchant_id,"merchants");
			if (!$ww['id']) _goto();
			$brokers_ids[] = $ww['id'];  
			$brokers[] = $ww['name'];
			} else {
			$qq=function_mysql_query("SELECT id,name FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$brokers_ids[] = $ww['id'];
				$brokers[] = $ww['name'];
				}
			}
		for ($i=0; $i<=count($brokers)-1; $i++) {
			$broker['name'] = $brokers[$i];
			if ($type AND $type != "ftd" AND $type != "real") $where .= " AND type='".$type."'";
			if ($type == "ftd") {
				// $qq=function_mysql_query("SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' GROUP BY trader_id ORDER BY id DESC",__FILE__);
				
				$qq=function_mysql_query("
				SELECT DISTINCT *
				FROM data_sales_".strtolower($broker['name'])." AS tb1
				WHERE ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' ".$where." AND trader_id NOT IN 
					(SELECT trader_id FROM data_sales_".strtolower($broker['name'])." WHERE type='deposit' AND rdate < tb1.rdate ".$where." GROUP BY trader_id) 
				GROUP BY trader_id",__FILE__);
				
				} else if ($type == "deposit") {
				$sql = "SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ORDER BY trader_id DESC"; //  ".($trader_id ? '' : 'GROUP BY trader_id')."
				// die($sql);
				$qq=function_mysql_query($sql,__FILE__);
				} else if ($type == "revenue") {
				$qq=function_mysql_query("SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' AND type='revenue' GROUP BY trader_id ORDER BY trader_id DESC",__FILE__);
				} else {
				$qq=function_mysql_query("SELECT * FROM data_reg_".strtolower($broker['name'])." WHERE 1 ".$where." ".$whereReg." AND ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC",__FILE__);
				// die("SELECT * FROM data_reg_".strtolower($broker['name'])." WHERE 1 ".$where." AND rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC");
				}
			// die("SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE 1 ".$where." AND rdate BETWEEN '".$from."' AND '".$to."' GROUP BY trader_id ORDER BY id DESC");
			$k=1;
			while ($ww=mysql_fetch_assoc($qq)) {
				unset($marketInfo);
				$totalTraders = $total_deposits = $depositAmount = $ftdAmount = $volumeAmount = $bonusAmount = $withdrawalAmount = $chargebackAmount = $totalTraders = $revenueAmount = 0;
				$merchantInfo = dbGet($ww['merchant_id'],"merchants");
				// Get Trader Info Because he's FTD
				if ($type == "ftd" || $type == "deposit" || $type == "real" || $type == "revenue") {
					$traderInfo = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate,trader_alias,type,trader_id FROM data_reg_".strtolower($broker['name'])." WHERE trader_id='".$ww['trader_id']."'",__FILE__));
					$ww['trader_alias'] = $traderInfo['trader_alias'];
					if ($type != "deposit") $ww['rdate'] = $traderInfo['rdate'];
					$ww['type'] = $traderInfo['type'];
					}
				
				$bannerInfo = dbGet($ww['banner_id'],"merchants_creative");
				if ($ww['market_id'] > 0) $marketInfo = dbGet($ww['market_id'],"market_items");
				$firstDeposit = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate,amount,trader_id FROM data_sales_".strtolower($broker['name'])." WHERE trader_id='".$ww['trader_id']."' AND type='deposit' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY rdate ASC LIMIT 1",__FILE__));
				if ($type == "ftd" AND !$firstDeposit['id']) continue;
				$ftdAmount = $firstDeposit['amount'];
				$amountqq = function_mysql_query("SELECT type,amount FROM data_sales_".strtolower($broker['name'])." WHERE ".$globalWhere." trader_id='".$ww['trader_id']."' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY rdate ASC",__FILE__);
				while ($amountww=mysql_fetch_assoc($amountqq)) {
					if ($amountww['type'] == "deposit") {
						$depositAmount += $amountww['amount'];
						$total_deposits++;
						} else if ($amountww['type'] == "bonus") $bonusAmount += $amountww['amount'];
						else if ($amountww['type'] == "withdrawal") $withdrawalAmount += $amountww['amount'];
						else if ($amountww['type'] == "chargeback") $chargebackAmount += $amountww['amount'];
						else if ($amountww['type'] == "volume") {
							$volumeAmount += $amountww['amount'];
							$totalTraders++;
							}
					}
				$affInfo = dbGet($ww['affiliate_id'],"affiliates");

				$netRevenue = round($depositAmount-($bonusAmount+$withdrawalAmount+$chargebackAmount));
				$totalCom = getCom($ww['affiliate_id'],$brokers_ids[$i],$ww['trader_id'],$from,$to,'deal');

				$chkTrader = mysql_fetch_assoc(function_mysql_query("SELECT * FROM payments_details WHERE trader_id='".$ww['trader_id']."' AND merchant_id='".$brokers_ids[$i]."'",__FILE__));
				
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['trader_id']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['trader_alias']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', ($type == "deposit" ? date("d/m/Y", strtotime($traderInfo['rdate'])) : date("d/m/Y", strtotime($ww['rdate']))));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['type']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', str_replace(",", "", longCountry($ww['country'])));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['affiliate_id']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $affInfo['username']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $brokers_ids[$i]);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', strtoupper($brokers[$i]));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bannerInfo['id']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bannerInfo['title']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bannerInfo['type']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['profile_id']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', str_replace(",", "", $ww['freeParam']));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', ($type == "deposit" ? date("d/m/Y", strtotime($ww['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y", strtotime($firstDeposit['rdate'])) : '')));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftdAmount);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $total_deposits);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $depositAmount);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $volumeAmount);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bonusAmount);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $withdrawalAmount);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $chargebackAmount);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $netRevenue);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalTraders);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalCom);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', str_replace(",", "", $chkTrader['reason']));
				
				$k++;
				}
			}
		break;
		
	case "affiliate_xml":
		
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Affiliate ID'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Username'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Full Name'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('E-Mail'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Website'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Impressions'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Clicks'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click Through Ratio (CTR) %'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Account'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Sale'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'EPC');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Lead'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Demo'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Accounts'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Total Deposits'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Deposits Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Volume'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Bonus Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Withdrawal Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('ChargeBack Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Affiliate Risk'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Net Revenue'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Commission'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Group'));
		
		if (is_numeric($affiliate_id)) $where .= " AND id='".$affiliate_id."'";
			else if ($affiliate_id) $where .= " AND lower(username) LIKE '%".trim(strtolower($affiliate_id))."%'";
		if ($group_id) $where .= " AND group_id='".$group_id."'";
		if ($merchant_id) $whereMer .= " AND id='".$merchant_id."'";
		// if (!$where AND !$affiliate_id) $limit = "LIMIT 50";
		$sql = "SELECT * FROM affiliates WHERE valid='1' ".$where." ORDER BY id DESC ".$limit;
		$qq=function_mysql_query($sql,__FILE__);
		$k=1;
			
		while ($ww=mysql_fetch_assoc($qq)) {
				$totalTraffic=0;
				$totalLeads=0;
				$totalDemo=0;
				$totalReal=0;
				$ftd=0;
				$ftd_amount=0;
				$bonus=0;
				$withdrawal=0;
				$chargeback=0;
				$volume=0;
				$revenue=0;
				$depositingAccounts=0;
				$sumDeposits=0;
				$totalCom=0;
				$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE ".$globalWhere." affiliate_id='".$ww['id']."' ".($merchant_id ? " AND merchant_id='".$merchant_id."'" : "")." AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__));
				$merchantqq=function_mysql_query("SELECT id,name FROM merchants WHERE valid='1' ".$whereMer,__FILE__);
				while ($merchantww=mysql_fetch_assoc($merchantqq)) {
					$merchantName = strtolower($merchantww['name']);
					
					$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE ".$globalWhere." affiliate_id='".$ww['id']."' AND type='real' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
					while ($regww=mysql_fetch_assoc($regqq)) {
						if ($regww['type'] == "lead") $totalLeads++;
						if ($regww['type'] == "demo") $totalDemo++;
						if ($regww['type'] == "real") $totalReal++;
						}
					
					$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT trader_id, tb1.rdate,amount
					FROM data_sales_".$merchantName." AS tb1
					WHERE ".$globalWhere." affiliate_id='".$ww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' AND trader_id NOT IN 
						(SELECT trader_id FROM data_sales_".$merchantName." WHERE affiliate_id='".$ww['id']."'AND type='deposit'  AND rdate < tb1.rdate GROUP BY trader_id) 
					GROUP BY trader_id",__FILE__);
					while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
						$ftd++;
						$ftd_amount += $totalftd['amount'];
						$totalCom += getCom($ww['id'],$merchantww['id'],$totalftd['trader_id'],$from,$to,'deal');
						}
					
					$salesqq=function_mysql_query("SELECT type,amount FROM data_sales_".$merchantName." WHERE ".$globalWhere." affiliate_id='".$ww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
					while ($salesww=mysql_fetch_assoc($salesqq)) {
						if ($salesww['type'] == "deposit") {
							$depositingAccounts++;
							$sumDeposits += $salesww['amount'];
							}
						if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
						if ($salesww['type'] == "revenue") $revenue += $salesww['amount'];
						if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
						if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
						if ($salesww['type'] == "volume") $volume += $salesww['amount'];
						}
					
					}
				if ($totalLeads <= 0 AND $totalDemo <= 0 AND $totalReal <= 0 AND $ftd <= 0 AND $totalTraffic['totalViews'] <= 0 AND $totalTraffic['totalClicks'] <= 0) continue; // Skip is affiliate data empty
				$totalFruad=mysql_num_rows(function_mysql_query("SELECT id FROM payments_details WHERE status='canceled' AND affiliate_id='".$ww['id']."'",__FILE__));
				$netRevenue = round($sumDeposits-($bonus+$withdrawal+$chargeback));
				
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['id']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['username']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['first_name'].' '.$ww['last_name']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['mail']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['website']);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round($totalTraffic['totalViews'],0));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round($totalTraffic['totalClicks'],0));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalReal/$totalTraffic['totalClicks'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($ftd/$totalTraffic['totalClicks'])*100,2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalCom/$totalTraffic['totalClicks']),2));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalLeads);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalDemo);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalReal);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd_amount);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $depositingAccounts);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $sumDeposits);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $volume);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bonus);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $withdrawal);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $chargeback);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @number_format(($totalFruad/$ftd)*100,2).'%');
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', round($netRevenue));
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalCom);
				$csvContent[$k][] = iconv('UTF-8', 'windows-1255', listGroups($ww['group_id'],1));
				
				$k++;
				}
		
		break;
	
	case "group_xml":
		
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Group ID'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Group Name'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Impressions'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Clicks'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click Through Ratio (CTR) %'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Account'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Click to Sale'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', 'EPC');
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Lead'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Demo'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Accounts'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('FTD Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Total Deposits'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Deposits Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Bonus Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Withdrawal Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('ChargeBack Amount'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Commission'));
		
		$k=1;
		$sql="SELECT * FROM groups ORDER BY id DESC";
		$qq=function_mysql_query($sql,__FILE__);
		for ($i = 0; $i <= mysql_num_rows($qq); $i++){
			$ww = mysql_fetch_assoc($qq);
			
			if($ww['id'] != $group_id AND $group_id) continue;
			
			$l++;
			$merchantqq=function_mysql_query("SELECT id,name FROM merchants WHERE valid='1'",__FILE__);
			while ($merchantww=mysql_fetch_assoc($merchantqq)) {
				
				$fromDate = strtotime($from);
				$toDate = strtotime($to);
				
				if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
					else $diff = 0;
				
				if($display_type == "weekly") {
					$theFromDay = date("w",strtotime($from))+1;
					$theToDay = date("w",strtotime($to))+1;
					$daysUntilEndWeek = (7-$theFromDay);
					$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
					
					$diff = round($diff/8);
					if($theFromDay > $theToDay) $diff = $diff+1;
					}
				
				if ($display_type == "monthly") {
					$endOfthisMonth = date("t",strtotime($from));
					if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
					$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
					$date1 = strtotime($from);
					$date2 = strtotime($to);
					$months = 0;
					while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
					if($diff == 0) $lastDayOfMonth = $to;
					}
				
				for ($n=0;$n<=$diff;$n++) {
					$totalTraffic=0;
					$totalLeads=0;
					$totalDemo=0;
					$totalReal=0;
					$ftd=0;
					$ftd_amount=0;
					$depositingAccounts=0;
					$sumDeposits=0;
					$revenue=0;
					$volume=0;
					$bonus=0;
					$withdrawal=0;
					$chargeback=0;
					$netRevenue=0;
					$totalCom=0;
					// From Show on weekly
					if($newFrom) $showFrom = $newFrom; else $showFrom = $from;
					
					$searchInSql = "";
					if ($display_type == "daily") {
						$searchInSql = "= '".date("Y-m-d", strtotime($from." +".$n." day"))."'";
					} else if ($display_type == "weekly"){
						if ($n==0) { // Weekly First Loop
							$searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
							$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
						} else if ($n == $diff) { // Last Loop - Last week
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						} else { // Else Loops
							$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						}
					} else if ($display_type == "weekly"){ 
						if($n==0){ // Weekly First Loop
							$searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
							$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
						} else if ($n == $diff) { // Last Loop - Last week
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						} else { // Else Loops
							$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						}
					} else if ($display_type == "monthly"){ 
						if($n==0){ // Monthly First Loop
							$searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
							$newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
						} else if ($n == $diff) { // Last Loop - Last week
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						} else { // Else Loops
							$numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
							$newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
							$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
							$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
						} 
					} else { // If no Display_type 
						$searchInSql = "BETWEEN '".$from."' AND '".$to."'";
					}
					
					// To Show on weekly
					if ($display_type == "weekly")  if($newTo) $showTo = $newTo; else $showTo = $endOfWeek;
					// To Show on monthly
					if ($display_type == "monthly") if($newTo) $showTo = $newTo; else $showTo = $lastDayOfMonth;
					
					if ($n==$diff) $showTo = $to;
					
					$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM stats_banners WHERE group_id='".$ww['id']."' AND rdate ".$searchInSql."",__FILE__));
					$merchantName = strtolower($merchantww['name']);
					
					$regqq=function_mysql_query("SELECT id,type FROM data_reg  WHERE group_id='".$ww['id']."' AND type='real' AND rdate ".$searchInSql,__FILE__);
					while ($regww=mysql_fetch_assoc($regqq)) {
						if ($regww['type'] == "lead") $totalLeads++;
						if ($regww['type'] == "demo") $totalDemo++;
						if ($regww['type'] == "real") $totalReal++;
						}
						
					$query_ftd_amountqq=function_mysql_query("SELECT DISTINCT tb1.trader_id, tb1.rdate,tb1.amount,tb1.affiliate_id
					FROM data_sales_".$merchantName." AS tb1
					WHERE group_id='".$ww['id']."' AND rdate ".$searchInSql." AND type='deposit' AND trader_id NOT IN 
						(SELECT trader_id FROM data_sales_".$merchantName." WHERE type='deposit' AND rdate < tb1.rdate GROUP BY trader_id) 
					GROUP BY trader_id",__FILE__);
					while ($totalftd=mysql_fetch_assoc($query_ftd_amountqq)) {
						$ftd++;
						$ftd_amount += $totalftd['amount'];
						$totalCom += getCom($totalftd['affiliate_id'],$merchantww['id'],$totalftd['trader_id'],$from,$to,'deal');
						}
					
					$salesqq=function_mysql_query("SELECT type,amount FROM data_sales_".$merchantName." WHERE group_id='".$ww['id']."' AND rdate ".$searchInSql."",__FILE__);
					while ($salesww=mysql_fetch_assoc($salesqq)) {
						if ($salesww['type'] == "deposit") {
							$sumDeposits += $salesww['amount'];
							$depositingAccounts++;
							$totalCom += getCom($salesww['affiliate_id'],$merchantww['id'],$salesww['trader_id'],$from,$to,'deal');
							}
						if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
						if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
						if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
						if ($salesww['type'] == "volume") $volume += $salesww['amount'];
						}
					
					if ($display_type == "daily") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($from.' +'.$n.' Day')));
					if ($display_type == "weekly") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)));
					if ($display_type == "monthly") $csvContent[$k][] = iconv('UTF-8', 'windows-1255', date("d/m/y",strtotime($showFrom)).' - '.date("d/m/y", strtotime($showTo)));
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['id']);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ww['title']);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round($totalTraffic['totalViews'],0));
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round($totalTraffic['totalClicks'],0));
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2));
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalReal/$totalTraffic['totalClicks'])*100,2));
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($ftd/$totalTraffic['totalClicks'])*100,2));
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', @round(($totalCom/$totalTraffic['totalClicks']),2));
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalLeads);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalDemo);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalReal);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $ftd_amount);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $depositingAccounts);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $sumDeposits);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $volume);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $bonus);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $withdrawal);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $chargeback);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $netRevenue);
					$csvContent[$k][] = iconv('UTF-8', 'windows-1255', $totalCom);
					$k++;
					}
				}
			}
		
		break;
	
	}

	
$fileName = "admin/csv/report.csv";
$openFile = fopen($fileName, 'w'); 
// fwrite($openFile, $csvContent); 
fclose($openFile); 
header("Expires: 0");
header("Pragma: no-cache");
header("Content-type: application/ofx");
header("Content-Disposition: attachment; filename=".$fileName);
for ($i=0; $i<=count($csvContent)-1; $i++) echo implode(",",$csvContent[$i])."\n";
die();

?>