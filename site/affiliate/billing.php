<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);


$appTable = 'payments_paid';

switch ($act) {
	default:
		$pageTitle = lang('Billing (Payment List)');
		$set->breadcrumb_title =  lang($pageTitle);
		$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$qq=function_mysql_query("SELECT * FROM ".$appTable." WHERE affiliate_id='".$set->userInfo['id']."' ORDER BY id DESC",__FILE__);
		$listPayments = '';
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$affiliateInfo=dbGet($ww['affiliate_id'],"affiliates");
			$amount=mysql_fetch_assoc(function_mysql_query("SELECT COUNT(id) AS totalFTD, SUM(amount) AS amount FROM payments_details WHERE paymentID='".$ww['paymentID']."'",__FILE__));
			$paid=mysql_fetch_assoc(function_mysql_query("SELECT id,total,paid FROM payments_paid WHERE paymentID='".$ww['paymentID']."'",__FILE__));
			if (!$paid['paid']) continue;
			$listPayments .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
					<td>'.$l.'</td>
					<td>'.$ww['paymentID'].'</td>
					<td>'.$ww['month'].'/'.$ww['year'].'</td>
					<td>'.$amount['totalFTD'].'</td>
					<td>'.price($paid['total']).'</td>
					<td>'.($paid['paid'] ? lang('Paid') : lang('Pending...')).'</td>
					<td><a href="'.$set->SSLprefix.$set->basepage.'?act=view&paymentID='.$ww['paymentID'].'" target="_blank">'.lang('View').'</a></td>
				</tr>';
			}
			if($listPayments==''){
				$listPayments = '<tr><td colspan="7"><h6 style="text-align:center">No data found.</h6> </td></tr>';	
			}
			$set->content .= '<div class="billing-page">
				<div class="billing-page-id">
					<div class="search-payment">
						<label>Search Payment ID<label>
					</div>
					<div class="SearchPayment-input">
						<input type="text">
						<p><i class="fa fa-search"></i></p>
					</div>
				</div>

				<div class="billing-page-table">
					<div class="row">
						<div class="col-md-12">
							<div class="top-performing-creative h-full">
								<h2 class="specialTableTitle"></h2>
									<div class="performing-creative-table">
										<div class="table-responsive">
											<table class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
												<thead>
													<tr>
													<th scope="col">#</th>
													<th scope="col">Payment ID</th>
													<th scope="col">Month</th>
													<th scope="col">Total FTD</th>
													<th scope="col">Amount</th>
													<th scope="col">Status</th>
													<th scope="col">Action</th>
													</tr>
												</thead>
												<tbody>
													'.$listPayments .'
												</tbody>
											</table>
										</div>
									</div>
							</div>
						</div>
					</div>
				</div>


			</div>

			';
		theme();
		break;
	
	case "view":
		$set->content .= getPayment($paymentID);
		theme(1);
		break;
	
	case "details":
		$payInfo = mysql_fetch_assoc(function_mysql_query("SELECT * FROM payments_details WHERE paymentID='".$invoice."' ORDER BY id DESC LIMIT 1",__FILE__));
		if (!$payInfo['id'] OR $payInfo['affiliate_id'] != $set->userInfo['id']) _goto($set->SSLprefix.$set->basepage);
		if ($reportType) $where = " AND reportType='".$reportType."'";
		if ($status) $where .= " AND status='".$status."'";
		$traderqq=function_mysql_query("SELECT id,trader_id,status,amount FROM payments_details WHERE paymentID='".$invoice."'".$where,__FILE__);
		while ($traderww=mysql_fetch_assoc($traderqq)) {
			$implodeAll[] = "trader_id='".$traderww['trader_id']."'";
			$traderStatus[$traderww['trader_id']] = $traderww['status'];
			$detailID[$traderww['trader_id']] = $traderww['id'];
			$comAmount[$traderww['trader_id']] = $traderww['amount'];
			}
		if (!$payInfo['id']) _goto($set->SSLprefix.$set->basepage);
		$set->pageTitle = lang('Invoice').' #'.$invoice.' '.lang('Report');
		$l=0;
		$brokerInfo = dbGet($merchant_id,"merchants");
		$brokers_ids[] = $brokerInfo['id'];
		$brokers[] = $brokerInfo['name'];
		
		$from = '01/'.$payInfo['month'].'/'.$payInfo['year'];
		$to = '31/'.$payInfo['month'].'/'.$payInfo['year'];
		$from = strTodate($from);
		$to = strTodate($to);
		for ($i=0; $i<=count($brokers)-1; $i++) {
			$broker['name'] = $brokers[$i];
			
			$qq=function_mysql_query("SELECT * FROM data_sales WHERE mechant_id = '" . $brokers_ids[$i] . "' and ".implode(" OR ", $implodeAll)." GROUP BY trader_id ORDER BY id DESC",__FILE__);
			
			while ($ww=mysql_fetch_assoc($qq)) {
				$ftdAmount = $depositAmount = $bonusAmount = $withdrawalAmount = $revenueAmount = 0;
				$merchantInfo = dbGet($ww['merchant_id'],"merchants");
				// Get Trader Info Because he's FTD
				if ($type == "ftd" || $type == "deposit" || $type == "real" || $type == "revenue") {
					$traderInfo = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate,trader_alias,type,trader_id,country FROM data_reg WHERE mechant_id = '" . $brokers_ids[$i] . "' and trader_id='".$ww['trader_id']."'",__FILE__));
					$ww['trader_alias'] = $traderInfo['trader_alias'];
					$ww['rdate'] = $traderInfo['rdate'];
					$ww['type'] = $traderInfo['type'];
					$ww['country'] = $traderInfo['country'];
					}
				
				$bannerInfo = dbGet($ww['banner_id'],"merchants_creative");
				$firstDeposit = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate FROM data_sales WHERE mechant_id = '" . $brokers_ids[$i] . "' and trader_id='".$ww['trader_id']."' AND type='deposit' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY id ASC LIMIT 1",__FILE__));
				if ($type == "ftd" AND !$firstDeposit['id']) continue;
				$totalTrades=0;
				$amountqq = function_mysql_query("SELECT type,amount FROM data_sales WHERE mechant_id = '" . $brokers_ids[$i] . "' and trader_id='".$ww['trader_id']."' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY rdate ASC",__FILE__);
				while ($amountww=mysql_fetch_assoc($amountqq)) {
					if ($amountww['type'] == "deposit") {
						$depositAmount += $amountww['amount'];
						if (!$ftdAmount) $ftdAmount = $amountww['amount'];
						} else if ($amountww['type'] == "bonus") $bonusAmount += $amountww['amount'];
						else if ($amountww['type'] == "withdrawal") $withdrawalAmount += $amountww['amount'];
						else if ($amountww['type'] == "revenue") {
						$revenueAmount += $amountww['amount'];
						$totalTrades++;
						}
					}
				$netRevenue = (($revenueAmount+$bonusAmount-$withdrawalAmount)*(-1));
				
				$total_deposits=mysql_result(function_mysql_query("SELECT COUNT(id) FROM data_sales WHERE mechant_id = '" . $brokers_ids[$i] . "' and trader_id='".$ww['trader_id']."' AND type='deposit'",__FILE__),0);
				
				$listReport .= '<tr>
						<td>'.$ww['trader_id'].'</td>
						<td>'.date("d/m/Y", strtotime($ww['rdate'])).'</td>
						<td>'.longCountry($ww['country']).'</td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td>'.($type == "deposit" ? date("d/m/Y", strtotime($ww['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y", strtotime($firstDeposit['rdate'])) : '')).'</td>
						<td>'.price($ftdAmount).'</td>
						<td><a href="/affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchant_id.'&trader_id='.$ww['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
						<td>'.price(($type == "deposit" ? $ww['amount'] : $depositAmount)).'</td>
						<td>'.price($bonusAmount).'</td>
						<td>'.price($withdrawalAmount).'</td>
						<td>'.price($netRevenue).'</td>
						<td>'.$totalTrades.'</td>
						<td>'.price($comAmount[$ww['trader_id']]).'</td>
						<td>'.lang(strtoupper($traderStatus[$ww['trader_id']])).'</td>
						<td>'.$chkTrader['notes'].'</td>
					</tr>';
				$l++;
				}
			}
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $l;
		$set->content .= '<div class="normalTableTitle" style="width: 1995px;">'.$set->pageTitle.'</div>
			<div style="background: #F8F8F8;">
				<table width="2000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).'</th>
						<th>'.lang('Registration Date').'</th>
						<th>'.lang('Country').'</th>
						<th>'.lang('Merchant ID').'</th>
						<th>'.lang('Merchant Name').'</th>
						<th>'.($type == "deposit" ? lang('Deposit Date') : lang('First Deposit')).'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.($type == "deposit" ? lang('Deposit Amount') : lang('Deposits Amount')).'</th>
						<th>'.lang('Bonus  Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('Net Revenue').'</th>
						<th>'.lang(ptitle('Trades')).'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Status').'</th>
						<th>'.lang('Notes').'</th>
					</tr></thead><tfoot></tfoot>
					<tbody>
					'.$listReport.'
				</table>
			</div>'.getPager();
		
		theme();
		break;
	
	}

?>