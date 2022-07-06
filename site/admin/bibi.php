<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

if (!isAdmin()) _goto('/admin/');

$appTable = 'payments_details';

switch ($act) {
	default:
		$set->pageTitle = lang('Billing');
		if ($month) $where = " AND month='".$month."'";
		if ($year) $where .= " AND year='".$year."'";
		
		if ($affiliate_id ) $where .= " AND affiliate_id='".$affiliate_id."'";
		
		$qq=function_mysql_query("SELECT paymentID,affiliate_id,month,year,reason FROM ".$appTable." WHERE 1 ".$where." GROUP BY paymentID",__FILE__); 
		$l=1;
		while ($ww=mysql_fetch_assoc($qq)) {
			unset($payAddress);
			$affiliateInfo=dbGet($ww['affiliate_id'],"affiliates");
			$amount=mysql_fetch_assoc(function_mysql_query("SELECT SUM(amount) AS amount FROM ".$appTable." WHERE paymentID='".$ww['paymentID']."' AND status='approved'",__FILE__));
			$totalFTDs=mysql_fetch_assoc(function_mysql_query("SELECT COUNT(id) AS totalFTD FROM ".$appTable." WHERE paymentID='".$ww['paymentID']."' AND status='approved' AND reportType != 'bonus'",__FILE__));
			$paid=mysql_fetch_assoc(function_mysql_query("SELECT id,total,paid,sentMail FROM payments_paid WHERE paymentID='".$ww['paymentID']."'",__FILE__));
			if ($status == "paid" AND !$paid['paid']) continue;
				else if ($status == "pending" AND $paid['paid']) continue;
			$lineAmount = ($paid['id'] ? $paid['total'] : $amount['amount']);
			
			if ($affiliateInfo['paymentMethod'] == "paypal") $payAddress = $affiliateInfo['pay_account'];
				else if ($affiliateInfo['paymentMethod'] == "moneyBookers") $payAddress = $affiliateInfo['pay_email'];
			
			if ($paymentMethod AND $paymentMethod != $affiliateInfo['paymentMethod']) continue; // Filting Payment Method
			
			$listReport .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
					<td>'.$l.'</td>
					<td width="140" style="text-align: left;"><a href="'.$set->basepage.'?act=view&paymentID='.$ww['paymentID'].'" target="_blank">'.lang('View').'</a>'.($paid['paid'] ? ($paid['sentMail'] ? ' | <span style="color: green;">'.lang('Sent').'!</span> | <a href="'.$set->basepage.'?act=sendInvoice&paymentID='.$ww['paymentID'].'&affiliate_id='.$affiliateInfo['id'].'" style="font-size: 10px;">Send Again</a>' : ' | <a href="'.$set->basepage.'?act=sendInvoice&paymentID='.$ww['paymentID'].'&affiliate_id='.$affiliateInfo['id'].'">'.lang('Send Invoice').'</a>') : '').' </td>
					<td style="text-align: left;">'.$ww['paymentID'].'</td>
					<td style="text-align: left;"><a href="/admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'">'.$affiliateInfo['username'].'</a></td>
					<td style="text-align: left;"><a href="/admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'">'.$affiliateInfo['first_name'].' '.$affiliateInfo['last_name'].'</a></td>
					<td><a href="/admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'">'.$affiliateInfo['id'].'</a></td>
					<td>'.$ww['month'].'/'.$ww['year'].'</td>
					<td>'.$totalFTDs['totalFTD'].'</td>
					<td>'.price($lineAmount).'</td>
					<td style="text-align: left;">'.strtoupper($affiliateInfo['paymentMethod']).'</td>
					<td style="text-align: left;">'.$payAddress.'</td>
					<td>'.$ww['reason'].'</td>
					<td>'.($paid['paid'] ? lang('Paid') : lang('Pending...')).'</td>
				</tr>';
			$totalFTD += $totalFTDs['totalFTD'];
			$totalAmount += $lineAmount;
			$l++;
			}
		for ($i=1; $i<=12; $i++) $listMonths .= '<option value="'.$i.'" '.($i == $month ? 'selected' : '').'>'.$i.'</option>';
		for ($i=2012; $i<=date("Y"); $i++) $listYears .= '<option value="'.$i.'" '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
		if ($ty) {
			$set->content .= '
				<script type="text/javascript">
					$(document).ready(function() {
						alert(\''.lang('Invoice E-mail has Sent').'\');
						});
				</script>';
			}
		$set->content .= '
			<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Generate Payments').'</div>
			<div id="tab_1" align="left" style="background: #EFEFEF; display: none; padding: 10px;">
				<form action="'.$set->basepage.'" method="get">
					<input type="hidden" name="act" value="generate" />
					'.lang('Generate Payments').': <select name="month" style="width: 100px;">'.$listMonths.'</select> / <select name="year" style="width: 100px;">'.$listYears.'</select> '.lang('Affiliate ID').': <input type="text" name="affiliate_id" value="0" style="width: 100px; text-align: center;" /> <input type="submit" value="'.lang('Generate').'" />
				</form>
			</div>
			<br />
			<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Generate New Invoice').'</div>
			<div id="tab_3" align="left" style="background: #EFEFEF; padding: 10px;">
				<form action="'.$set->basepage.'" method="get">
					<input type="hidden" name="act" value="create" />
					'.lang('Monthly Invoice').': <select name="month" style="width: 100px;">'.$listMonths.'</select> / <select name="year" style="width: 100px;">'.$listYears.'</select> 
					'.lang('Affiliate ID').': <input type="text" name="affiliate_id" value="'.$affiliate_id.'" style="width: 100px;" />
					<input type="submit" value="'.lang('Create').'" />
				</form>
			</div>
			<br />
			<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_2\').slideToggle(\'fast\');">'.lang('Search Payments').'</div>
			<div id="tab_2" align="left" style="background: #EFEFEF; padding: 10px;">
				<form action="'.$set->basepage.'" method="get">
					<select name="month" style="width: 100px;">'.$listMonths.'</select> / <select name="year" style="width: 100px;">'.$listYears.'</select> 
					'.lang('Status').': <select name="status" style="width: 100px;">
						<option value="">'.lang('All').'</option>
						<option value="pending" '.($status == "pending" ? 'selected="selected"' : '').'>'.lang('Pending...').'</option>
						<option value="paid" '.($status == "paid" ? 'selected="selected"' : '').'>'.lang('Paid').'</option>
					</select>
					'.lang('Payment Method').': <select name="paymentMethod" style="width: 140px;">
						<option value="">'.lang('All').'</option>
						<option value="bank" '.($paymentMethod == "bank" ? 'selected="selected"' : '').'>'.lang('Wire Transfer').'</option>
						<option value="moneyBookers" '.($paymentMethod == "moneyBookers" ? 'selected="selected"' : '').'>'.lang('MoneyBookers').'</option>
						<option value="paypal" '.($paymentMethod == "paypal" ? 'selected="selected"' : '').'>'.lang('PayPal').'</option>
					</select>
					'.lang('Affiliate ID').': <input type="text" name="affiliate_id" value="'.$affiliate_id.'" style="width: 100px;" />
					<input type="submit" value="'.lang('View').'" />
				</form>
			</div>
			<br />
			<div class="normalTableTitle">'.lang('Billing Reports').'</div>
			<div style="background: #F8F8F8;">
				<table width="100%" class="normal" border="0" cellpadding="3" cellspacing="0">
					<thead><tr>
						<td>#</td>
						<td style="text-align: left;">'.lang('Actions').'</td>
						<td style="text-align: left;">'.lang('Payment ID').'</td>
						<td style="text-align: left;">'.lang('Username').'</td>
						<td style="text-align: left;">'.lang('Full Name').'</td>
						<td>'.lang('Affiliate ID').'</td>
						<td>'.lang('Month').' / '.lang('Year').'</td>
						<td>'.lang('Total FTD').'</td>
						<td width="100">'.lang('Total').'</td>
						<td style="text-align: left;">'.lang('Payment Method').'</td>
						<td style="text-align: left;">'.lang('Payment Address').'</td>
						<td>'.lang('Reason').'</td>
						<td>'.lang('Payment Status').'</td>
					</tr></thead><tfoot>'.$listReport.'
					<tr style="background: #F8F8F8;">
						<td colspan="4" style="text-align: left;">'.lang('Total').': <b>'.($l-1).'</b></td>
						<td></td>
						<td></td>
						<td></td>
						<td>'.$totalFTD.'</td>
						<td>'.price($totalAmount).'</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
				</table>
			</div>';
		
		theme();
		break;
	
	case "create":
		$getAffiliate=dbGet($affiliate_id,"affiliates");
		if (!$getAffiliate['id']) _goto($set->basepage);
		$paymentID = 'BF'.$year.($month < 10 ? '0'.$month : $month).$affiliate_id.rand('5','9').rand('1','5').'Pm';
		function_mysql_query("INSERT INTO payments_details (rdate,status,month,year,paymentID,merchant_id,affiliate_id) VALUES ('".dbDate()."','approved','".$month."','".$year."','".$paymentID."','0','".$affiliate_id."')",__FILE__);
		_goto($set->basepage.'?act=view&paymentID='.$paymentID);
		break;
	
	case "sendInvoice":
		$getInvoice = mysql_fetch_assoc(function_mysql_query("SELECT * FROM payments_details WHERE paymentID='".$paymentID."'",__FILE__));
		if (!$getInvoice['id']) _goto($set->basepage);
		$affiliateInfo=dbGet($affiliate_id,"affiliates");
		$set->sendTo = $affiliateInfo['mail'];
		$set->subject = $affiliateInfo['username'].' - Invoice #'.$getInvoice['paymentID'].' '.$getInvoice['month'].'/'.$getInvoice['year'].' - '.$set->webTitle;
		$set->body = getPayment($paymentID,1);
		sendMail(0,0);
		updateUnit("payments_paid","sentMail='1'","paymentID='".$getInvoice['paymentID']."'");
		// die($set->refe);
		_goto($set->refe.($set->refe == $set->webAddress."admin/billing.php" ? '?' : '&').'ty=1');
		break;
	
	case "view":
		$set->content .= getPayment($paymentID);
		theme(1);
		break;
	
	case "paid":
		$db['rdate'] = dbDate();
		$db['paid'] = ($paid ? '1' : '0');
		
		for ($i=0; $i<=count($extra['deal'])-1; $i++) {
			unset($extra_db);
			if (!isset($extra['merchant_id'][$i]) AND !$extra['deal'][$i]) continue;
			$extra_db[] = $extra['merchant_id'][$i];
			$extra_db[] = $extra['deal'][$i];
			$extra_db[] = $extra['unit_price'][$i];
			$extra_db[] = $extra['quantity'][$i];
			if (count($extra_db) > 0 AND $extra['unit_price'][$i] AND $extra['quantity'][$i]) $allExtra[] = implode("|",$extra_db);
			}
			
		if (count($allExtra) > 0) $db['extras'] = implode('[var]',$allExtra);
			else $db['extras'] = '';
		dbAdd($db,"payments_paid");
		function_mysql_query("UPDATE affiliates SET credit='".$db['creditLeft']."' WHERE id='".$db['affiliate_id']."'",__FILE__);
		_goto('/admin/billing.php?act=view&paymentID='.$db[paymentID]);
		break;
	
	// --------------------------------------------------------------------------------------------------------------------
		
	case "generate":
		if ($affiliate_id > 0) {
			$affWhere = " AND id='".$affiliate_id."'";
			function_mysql_query("DELETE FROM ".$appTable." WHERE affiliate_id='".$affiliate_id."' AND month='".$month."' AND year='".$year."'",__FILE__);
			function_mysql_query("DELETE FROM payments_paid WHERE affiliate_id='".$affiliate_id."' AND month='".$month."' AND year='".$year."'",__FILE__);
			}
		$set->pageTitle = lang('Generating Payments...');
		$invoiceDate = $year.'-'.($month < 10 ? '0'.$month : $month).'-01';
		$currentDate = date("Y-m-01",strtotime("+1 Month",strtotime($invoiceDate)));
		if (date("Y-m-d") < $currentDate) _goto($set->basepage);
		$getInvoices=mysql_result(function_mysql_query("SELECT COUNT(id) FROM ".$appTable." WHERE month='".$month."' AND year='".$year."'",__FILE__),0);
		// if ($getInvoices > 0) _goto($set->basepage);
		$set->content .= '<script type="text/javascript">
				var affiliate_ids = new Array();
				var affiliateNames = new Array();
				';
			$affNum=0;
			$sql = "SELECT * FROM affiliates WHERE valid='1' ".$affWhere." ORDER BY id ASC";
			// die($sql);
			$qq=function_mysql_query($sql,__FILE__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$chkExist = mysql_fetch_assoc(function_mysql_query("SELECT id FROM ".$appTable." WHERE month='".$month."' AND year='".$year."' AND affiliate_id='".$ww['id']."'",__FILE__));
				if ($chkExist['id']) continue;
				$set->content .= 'affiliate_ids['.$affNum.'] = \''.$ww['id'].'\';';
				$set->content .= 'affiliateNames['.$affNum.'] = \''.$ww['username'].'\';';
				$affNum++;
				}
			$set->content .= '
				var currectNum = 0;
				function generatePayments() {
					ajax(\''.$set->basepage.'?act=generatePayment&month='.$month.'&year='.$year.'&affiliate_id=\'+affiliate_ids[currectNum],\'generateTo\');
					currectNum = currectNum + 1;
					gid(\'nowMail\').innerText = currectNum;
					gid(\'perC\').innerText = Math.round((currectNum / '.$affNum.') * 100)+\'%\';
					gid(\'statusGrapth\').style.width = Math.round((currectNum / '.$affNum.') * 396)+\'px\';
					if (currectNum < '.$affNum.') setTimeout(\'generatePayments()\',800);
						 else gid(\'compl\').innerHTML = \'Payments Completed!<br /><a href="'.$set->basepage.'?month='.$month.'&year='.$year.'">Payments List</a>\';
					}
					window.onload = function() {
						generatePayments();
						}
				</script>';
		$set->content .= '<table align="center" width="400">
					<tr>
						<td align="left" style="font-family: Arial;"><b>Generate Payments...</b></td>
						<td align="right"><span id="generateTo" style="font-family: Arial;"></span></td>
					</tr><tr>
						<td colspan="2" height="16" background="/admin/images/payment_block.gif"><div id="statusGrapth" style="width: 1px; height: 16px; background: url(/admin/images/payment_block_bg.gif);"></div></td>
					</tr><tr>
						<td align="left" style="font-family: Arial;"><span id="nowMail">0</span>/'.$affNum.'</td>
						<td align="right"><span id="perC" style="font-weight: bold; font-family: Arial;">0%</span></td>
					</tr><tr>
						<td colspan="2" height="5"></td>
					</tr><tr>
						<td colspan="2" align="center"><div id="compl" style="color: green; font-weight: bold;"></div></td>
					</tr>
				</table>';
		theme();
		break;
		
	case "generatePayment":
		unset($whereSub);
		unset($chartAffiliates);
		// $affiliate_id='1999'; // Remove
		$getAffiliate = dbGet($affiliate_id,"affiliates");
		$paymentID = 'DE'.rand('1','5').$year.($month < 10 ? '0'.$month : $month).$affiliate_id.rand('5','9').'MO';
		$num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$from = $year.'-'.($month < 10 ? '0'.$month : $month).'-01';
		$to = $year.'-'.($month < 10 ? '0'.$month : $month).'-'.$num;
		
		$chkExist = mysql_fetch_assoc(function_mysql_query("SELECT id FROM ".$appTable." WHERE month='".$month."' AND year='".$year."' AND affiliate_id='".$getAffiliate['id']."'",__FILE__));
		if ($chkExist['id']) {
			print '<b>'.$getAffiliate['username'].' </b> ['.$getAffiliate['id'].'] - <b>Exist!</b>';
			exit;
			}
		
		// Sub Affiliates
		$affiliateqq=function_mysql_query("SELECT id FROM affiliates WHERE valid='1' AND refer_id='".$getAffiliate['id']."' ORDER BY id DESC",__FILE__);
		while ($affiliateww=mysql_fetch_assoc($affiliateqq)) $chartAffiliates[] = "affiliate_id='".$affiliateww['id']."'";
		if (count($chartAffiliates) > 0) $whereSub = "(".implode(" OR ",$chartAffiliates).") AND";
		
		// Sub Affiliates
		$totalSubFtds=0;
		$myReportType=0;
		$qq=function_mysql_query("SELECT id,name,cpa_amount,dcpa_amount FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$merchantName = strtolower($ww['name']);
			
			$getDeal=mysql_fetch_assoc(function_mysql_query("SELECT amount FROM affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$ww['id']."' AND dealType='min_cpa' LIMIT 1",__FILE__));
			$getDealCpa=mysql_fetch_assoc(function_mysql_query("SELECT id,amount FROM affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$ww['id']."' AND dealType='cpa' LIMIT 1",__FILE__));
			$getDealDCpa=mysql_fetch_assoc(function_mysql_query("SELECT id,amount FROM affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$ww['id']."' AND dealType='dcpa' LIMIT 1",__FILE__));
			$getTier=mysql_fetch_assoc(function_mysql_query("SELECT id,amount FROM affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$ww['id']."' AND dealType!='tier'",__FILE__));

			if ($getDealCpa['id']) $cpaAmount = $getDealCpa['amount'];
				// else $cpaAmount = $ww['cpa_amount'];
				
				
			// Total Sub FTD's
			if (count($chartAffiliates) > 0) {
				$sqlSub="SELECT DISTINCT trader_id,amount,affiliate_id
						FROM data_sales_".$merchantName." 
						WHERE ".$whereSub." rdate BETWEEN '".$from."' AND '".$to."' AND type='deposit' AND amount >= '".$getDeal['amount']."' AND trader_id NOT IN 
							(SELECT trader_id FROM data_sales_".$merchantName." WHERE ".$whereSub." rdate < '".$from."' AND type='deposit' GROUP BY trader_id) 
						GROUP BY trader_id";
				// die ($sqlSub);
				$ftd_sub_qq=function_mysql_query($sqlSub,__FILE__);
				while ($subftd=mysql_fetch_assoc($ftd_sub_qq)) {
					$status = 'approved';
					$reason = '';
					$bonus=mysql_fetch_assoc(function_mysql_query("SELECT SUM(amount) AS amount FROM data_sales_".$merchantName." WHERE trader_id='".$subftd['trader_id']."' AND type='bonus' AND rdate >= '".$from."'",__FILE__));
					$withdrawal=mysql_fetch_assoc(function_mysql_query("SELECT SUM(amount) AS amount FROM data_sales_".$merchantName." WHERE trader_id='".$subftd['trader_id']."' AND type='withdrawal' AND rdate >= '".$from."'",__FILE__));
					$bonusDeposit = round($subftd['amount']+$bonus['amount']);
					// if (($withdrawal['amount'] >= $subftd['amount'] AND !$bonus['amount']) OR ($withdrawal['amount'] >= $bonusDeposit)) $status = 'canceled';
					
					// Get the CPA of the affiliate
					$getDealSubCpa=mysql_fetch_assoc(function_mysql_query("SELECT id,amount FROM affiliates_deals WHERE affiliate_id='".$subftd['affiliate_id']."' AND merchant_id='".$ww['id']."' AND dealType='cpa' LIMIT 1",__FILE__));
					$subCPAAmount = $getDealSubCpa['amount'];
					
					$getDealDCpa=mysql_fetch_assoc(function_mysql_query("SELECT id,amount FROM affiliates_deals WHERE affiliate_id='".$subftd['affiliate_id']."' AND merchant_id='".$ww['id']."' AND dealType='dcpa' LIMIT 1",__FILE__));
					if ($getDealDCpa['id'] AND $getDealDCpa['amount'] > 0) $subCPAAmount = round((($subftd['amount'] >= $getDealCpa['amount'] ? $getDealCpa['amount'] : $subftd['amount'])*$getDealDCpa['amount'])/100,2);
					if (!$getTier['id']) { // This affiliate has Tier Deal!
						$tierqq=function_mysql_query("SELECT * FROM affiliates_deals WHERE affiliate_id='".$subftd['affiliate_id']."' AND merchant_id='".$ww['id']."' AND dealType='tier' ORDER BY amount ASC",__FILE__);
						while ($tierww=mysql_fetch_assoc($tierqq)) {
							$tierRange = explode("-",$tierww['tier_amount']);
							if ($subftd['amount'] >= $tierRange[0] AND $subftd['amount'] <= $tierRange[1]) $subCPAAmount = $tierww['amount'];
							}
						}
					
					// Get the CPA of the affiliate
					
					if ($status == "approved") {
						$tag=mysql_fetch_assoc(function_mysql_query("SELECT status,notes FROM traders_tag WHERE merchant_id='".$ww['id']."' AND trader_id='".$subftd['trader_id']."'",__FILE__));
						if ($tag['status'] == "fraud" OR $tag['status'] == "other") $status = 'canceled';
						if ($tag['status'] == "other" AND (strtolower($tag['notes']) == "chargeback" OR strtolower($tag['notes']) == "duplicates")) $status = 'canceled';
						if ($tag['status'] == "chargeback" OR $tag['status'] == "duplicates") $status = 'canceled';
						// if ($tag['status'] == "withdrawal" OR $tag['status'] == "pending") $status = 'pending';
						$reason = ($tag['notes'] ? $tag['notes'] : $tag['status']);
						}
					if ($status == "approved") {
						function_mysql_query("INSERT INTO ".$appTable." (rdate,status,reportType,month,year,paymentID,merchant_id,affiliate_id,trader_id,amount,deposit,withdrawal,reason) 
							VALUES ('".dbDate()."','".$status."','sub','".$month."','".$year."','".$paymentID."','".$ww['id']."','".$subftd['affiliate_id']."','".$subftd['trader_id']."','".round(($subCPAAmount*$getAffiliate['sub_com'])/100,2)."','".$subftd['amount']."','".$withdrawal['amount']."','".$reason."')",__FILE__);
						$totalSubFtds++;
						}
					}
				}
			// Total Sub FTD's
			
			/* Affiliate Invoice */
			
			$ftd_amountqq=function_mysql_query("
			SELECT tb1.rdate, tb1.amount, tb1.trader_id
			FROM data_sales_".$merchantName." AS tb1
			WHERE tb1.affiliate_id='".$affiliate_id."' AND tb1.rdate BETWEEN '".$from."' AND '".$to."' AND tb1.type='deposit' AND tb1.amount >= '".$getDeal['amount']."' AND tb1.trader_id NOT IN 
				(SELECT trader_id FROM data_sales_".$merchantName." WHERE affiliate_id='".$affiliate_id."' AND rdate < tb1.rdate AND type='deposit')
				GROUP BY trader_id",__FILE__);
			
			while ($totalftd=mysql_fetch_assoc($ftd_amountqq)) {
				if ($dealType == "cpc" OR $dealType == "cpm") continue;
				$status = 'approved';
				$reason = '';
				$getCom = explode("|",getCom($affiliate_id,$ww['id'],$totalftd['trader_id'],$from,$to,'deal','',1));
				if ($getCom == 0) continue;
				$dealType = $getCom[0];
				$cpaAmount = $getCom[1];
				if (!$dealType) continue;
				
				if ($status == "approved") {
					$tag=mysql_fetch_assoc(function_mysql_query("SELECT id,status,notes FROM traders_tag WHERE merchant_id='".$ww['id']."' AND trader_id='".trim($totalftd['trader_id'])."'",__FILE__));
					if ($tag['status'] == "fraud" OR $tag['status'] == "withdrawal" OR $tag['status'] == "chargeback" OR $tag['status'] == "duplicates") $status = 'canceled';
					if ($tag['status'] == "pending" OR $tag['status'] == "other") $status = 'pending';
					$reason = ($tag['notes'] ? $tag['notes'] : $tag['status']);
					function_mysql_query("UPDATE traders_tag SET calReport='1' WHERE id='".$tag['id']."'",__FILE__);
					}
				
				function_mysql_query("INSERT INTO ".$appTable." (rdate,status,reportType,month,year,paymentID,merchant_id,affiliate_id,trader_id,amount,deposit,withdrawal,reason) 
					VALUES ('".dbDate()."','".$status."','".$dealType."','".$month."','".$year."','".$paymentID."','".$ww['id']."','".$affiliate_id."','".$totalftd['trader_id']."','".$cpaAmount."','".$totalftd['amount']."','".$withdrawal['amount']."','".$reason."')",__FILE__);
				if ($status == "approved") $myReportType++;
				}
			}
		// Add Network Bonus to the Affiliate that reach the goal!
		if ($myReportType > 0) {
			$getBonus=mysql_fetch_assoc(function_mysql_query("SELECT * FROM network_bonus WHERE min_ftd <= '".$myReportType."' AND (group_id='99999' OR group_id='".$getAffiliate['group_id']."') ORDER BY min_ftd DESC LIMIT 1",__FILE__));
			if ($getBonus['id']) {
				function_mysql_query("INSERT INTO ".$appTable." (rdate,status,reportType,month,year,paymentID,affiliate_id,amount,reason) 
					VALUES ('".dbDate()."','approved','bonus','".$month."','".$year."','".$paymentID."','".$affiliate_id."','".$getBonus['bonus_amount']."','".$getBonus['title']."')",__FILE__);
				}
			}
		
		print '<b>'.$getAffiliate['username'].' </b> ['.$getAffiliate['id'].']';
		exit;
		break;
	
	// ----------------------------------------------------------------------------------------------------------------------------
	
	case "details":
		$payInfo = mysql_fetch_assoc(function_mysql_query("SELECT * FROM payments_details WHERE paymentID='".$invoice."' ORDER BY id DESC LIMIT 1",__FILE__));
		if (!$payInfo['id']) _goto($set->basepage);
		if ($reportType) $where = " AND reportType='".$reportType."'";
		if ($status) $where .= " AND status='".$status."'";
		$traderqq=function_mysql_query("SELECT id,trader_id,status,amount FROM payments_details WHERE paymentID='".$invoice."'".$where,__FILE__);
		while ($traderww=mysql_fetch_assoc($traderqq)) {
			$implodeAll[] = "trader_id='".$traderww['trader_id']."'";
			$traderStatus[$traderww['trader_id']] = $traderww['status'];
			$detailID[$traderww['trader_id']] = $traderww['id'];
			$comAmount[$traderww['trader_id']] = $traderww['amount'];
			}
		if (!$payInfo['id']) _goto($set->basepage);
		$set->pageTitle = 'Invoice #'.$invoice.' Report';
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
			
			$qq=function_mysql_query("SELECT * FROM data_sales_".strtolower($broker['name'])." WHERE ".implode(" OR ", $implodeAll)." GROUP BY trader_id ORDER BY id DESC",__FILE__);
			
			while ($ww=mysql_fetch_assoc($qq)) {
				$ftdAmount = $depositAmount = $bonusAmount = $withdrawalAmount = $revenueAmount = 0;
				$merchantInfo = dbGet($ww['merchant_id'],"merchants");
				// Get Trader Info Because he's FTD
				if ($type == "ftd" || $type == "deposit" || $type == "real" || $type == "revenue") {
					$traderInfo = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate,trader_alias,type,trader_id,country FROM data_reg_".strtolower($broker['name'])." WHERE trader_id='".$ww['trader_id']."'",__FILE__));
					$ww['trader_alias'] = $traderInfo['trader_alias'];
					$ww['rdate'] = $traderInfo['rdate'];
					$ww['type'] = $traderInfo['type'];
					$ww['country'] = $traderInfo['country'];
					}
				
				$bannerInfo = dbGet($ww['banner_id'],"merchants_creative");
				$firstDeposit = mysql_fetch_assoc(function_mysql_query("SELECT id,rdate FROM data_sales_".strtolower($broker['name'])." WHERE trader_id='".$ww['trader_id']."' AND type='deposit' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY id ASC LIMIT 1",__FILE__));
				if ($type == "ftd" AND !$firstDeposit['id']) continue;
				$totalTrades=0;
				$amountqq = function_mysql_query("SELECT type,amount FROM data_sales_".strtolower($broker['name'])." WHERE trader_id='".$ww['trader_id']."' AND rdate BETWEEN '".$from."' AND '".$to."' ORDER BY rdate ASC",__FILE__);
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
				$affInfo = dbGet($ww['affiliate_id'],"affiliates");
				$netRevenue = (($revenueAmount+$bonusAmount-$withdrawalAmount)*(-1));
				
				$total_deposits=mysql_result(function_mysql_query("SELECT COUNT(id) FROM data_sales_".strtolower($broker['name'])." WHERE trader_id='".$ww['trader_id']."' AND type='deposit'",__FILE__),0);
				
				$listReport .= '<tr>
						<td>'.$ww['trader_id'].'</td>
						<td>'.date("d/m/Y", strtotime($ww['rdate'])).'</td>
						<td>'.$ww['affiliate_id'].'</td>
						<td><a href="/admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
						<td>'.longCountry($ww['country']).'</td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td>'.($type == "deposit" ? date("d/m/Y", strtotime($ww['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y", strtotime($firstDeposit['rdate'])) : '')).'</td>
						<td>'.price($ftdAmount).'</td>
						<td><a href="/admin/reports.php?act=trader&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$merchant_id.'&trader_id='.$ww['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
						<td>'.price(($type == "deposit" ? $ww['amount'] : $depositAmount)).'</td>
						<td>'.price($bonusAmount).'</td>
						<td>'.price($withdrawalAmount).'</td>
						<td>'.price($netRevenue).'</td>
						<td>'.$totalTrades.'</td>
						<td>'.price($comAmount[$ww['trader_id']]).'</td>
						<td>
							<select style="width: 120px;" onchange="if (ajax(\''.$set->basepage.'?act=changeStatus&details_id='.$detailID[$ww['trader_id']].'&status=\'+this.value) == \'changed\') alert(\'Status Changed\'); else alert(\'Error\');">
								<option value="approved" '.($traderStatus[$ww['trader_id']] == "approved" ? 'selected="selected"' : '').'>Approved</option>
								<option value="pending" '.($traderStatus[$ww['trader_id']] == "pending" ? 'selected="selected"' : '').'>Pending</option>
								<option value="canceled" '.($traderStatus[$ww['trader_id']] == "canceled" ? 'selected="selected"' : '').'>Canceled</option>
							</select>
						</td>
						<td><input type="text" value="'.$chkTrader['notes'].'" onfocusout="ajax(\''.$set->basepage.'?act=changeNotes&trader_id='.$ww['trader_id'].'&merchant_id='.$merchant_id.'&id='.$chkTrader['id'].'&text=\'+this.value);" /></td>
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
						<th>'.lang('Trader ID').'</th>
						<th>'.lang('Registration Date').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Affiliate Username').'</th>
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
						<th>'.lang('Trades').'</th>
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
	
	case "changeStatus":
		function_mysql_query("UPDATE payments_details SET status='".$_GET['status']."' WHERE id='".$_GET['details_id']."'",__FILE__); //or die("error");
		die('changed');
		break;
		
	case "changeNotes":
		$getNotes = mysql_fetch_assoc(function_mysql_query("SELECT id FROM traders_tag WHERE id='".$_GET['id']."'",__FILE__));
		if ($getNotes['id']) function_mysql_query("UPDATE traders_tag SET notes='".mysql_escape_string($_GET['text'])."' WHERE id='".$_GET['id']."'",__FILE__); //or die("error");
			else function_mysql_query("INSERT INTO traders_tag (valid,trader_id,rdate,merchant_id,status,notes) VALUES ('1','".$_GET['trader_id']."','".dbDate()."','".$_GET['merchant_id']."','other','".$_GET['text']."')",__FILE__); //or die("error");
		die('changed');
		break;
	
	}

?>