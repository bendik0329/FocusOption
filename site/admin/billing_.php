<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

if (!isAdmin()) _goto('/admin/');

/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();


$appTable = 'payments_details';

if($_REQUEST['save']){

	$act = 'save';

}else if($_REQUEST['delete']){
//die('loo');
	$act = 'delete';
}


switch ($act) {

case "delete":
		
		//echo $ids[0].'<BR>';
		//echo $pixelCode[0];
		$qry = 'delete from ' .$appTable .' where month=' . $month[0] . ' and year = ' .$year;
if ($affiliate_id>0)
			$qry .= ' and affiliate_id = ' . $affiliate_id;
		
		mysql_query($qry);
		//echo 'id: ';
		
		//die ($db['id']);
		//_goto($set->basepage.'?act=new&id='.$id.'#tab_10');
		
		_goto($set->basepage.'/admin/billing.php');
			
	break;
	
/*
	case "save":
	
		//echo $ids.'<BR>';
		//echo $pixelCode[0];
		$qry = 'update pixel_monitor set merchant_id=' .$merchant_id[0].' ,pixelCode = "' .$pixelCode[0] .'" , type = "' .$type[0]. '" where id=' . $ids[0];	
		//die ($qry);
		mysql_query($qry);
		_goto($set->basepage.'?act=new&id='.$id.'&toggleTo=tab_10#tab_10');
//		_goto($set->basepage.'?act=new&id='.$id.'#tab_10');
		//die('JUST EDIT PIXEL...');
	
	break;
*/	
	default:
		$set->pageTitle = lang('Billing');
		if ($month) $where = " AND month='".$month."'";
		if ($year) $where .= " AND year='".$year."'";
		
		if ($affiliate_id ) $where .= " AND affiliate_id='".$affiliate_id."'";
		
		$qq=mysql_query("SELECT paymentID,affiliate_id,month,year,reason FROM ".$appTable." WHERE 1 ".$where." GROUP BY paymentID");
		$l=1;
		
		$mid = $_COOKIE['mid'];
		 $mid = (aesDec($mid));

		$clrs=mysql_fetch_assoc(mysql_query("SELECT PaymentColors FROM merchants where id = " . $mid));
		$pendingC='';
		$paidC = '';
		if ($clrs) {
		$pc =  ($clrs['PaymentColors']);
		$pcArr = explode("|",$pc);
		$pendingC = explode(":",$pcArr[0]);
		$pendingC=$pendingC[1];
		$paidC = explode(":",$pcArr[1]);
		$paidC = $paidC[1];
		}
		
		while ($ww=mysql_fetch_assoc($qq)) {
			unset($payAddress);
			$affiliateInfo=dbGet($ww['affiliate_id'],"affiliates");
			$amount=mysql_fetch_assoc(mysql_query("SELECT SUM(amount) AS amount FROM ".$appTable." WHERE paymentID='".$ww['paymentID']."' AND status='approved'"));
			$totalFTDs=mysql_fetch_assoc(mysql_query("SELECT COUNT(id) AS totalFTD FROM ".$appTable." WHERE paymentID='".$ww['paymentID']."' AND status='approved' AND reportType != 'bonus'"));
			$paid=mysql_fetch_assoc(mysql_query("SELECT id,total,paid,sentMail FROM payments_paid WHERE paymentID='".$ww['paymentID']."'"));
			if ($status == "paid" AND !$paid['paid']) continue;
				else if ($status == "pending" AND $paid['paid']) continue;
			$lineAmount = ($paid['id'] ? $paid['total'] : $amount['amount']);
			
			if ($affiliateInfo['paymentMethod'] == "paypal") $payAddress = $affiliateInfo['pay_account'];
				else if ($affiliateInfo['paymentMethod'] == "moneyBookers") $payAddress = $affiliateInfo['pay_email'];
			
			if ($paymentMethod AND $paymentMethod != $affiliateInfo['paymentMethod']) continue; // Filting Payment Method
			
			$paidStatus = ($paid['paid'] ? ('Paid') : ('Pending') . '...');
			
			$bckColor = '';
			if ($paidStatus=='Paid') {
					
					if ($paidC=='') 
					$paidC =( $l % 2 ? '#EFEFEF;"' : '');
					$bckColor = $paidC;
					
				}
				if ($paidStatus=='Pending...') {
					if ($pendingC=='') 
					$pendingC =( $l % 2 ? '#EFEFEF;"' : '');
					
					$bckColor = $pendingC;
					
				}
			
			if ($valid==0) {
			 if (price($lineAmount)<>0 && $totalFTDs['totalFTD']<>0) 
				 continue;
			}
			//$block= '<tr '.($l % 2 ? 'class="trLine" style: "background='. $bckColor  . '">
			$block= '<tr '.'class="trLine" style= "background:'. $bckColor .'">
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
					<td style="text-align: left;">'.strtoupper($affiliateInfo['preferredCurrency']).'</td>
					<td style="text-align: left;">'.$payAddress.'</td>
					<td>'.$ww['reason'].'</td>
					<td>'.lang($paidStatus).'</td>
					</tr>';
			
			$listReport .=$block;
			$totalFTD += $totalFTDs['totalFTD'];
			$totalAmount += $lineAmount;
			$l++;
			}
		for ($i=1; $i<=12; $i++) $listMonths .= '<option value="'.$i.'" '.($i == $month ? 'selected' : '').'>'.$i.'</option>';
		for ($i=date("Y"); $i>=2012; $i--) $listYears .= '<option value="'.$i.'" '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
		//for ($i=2012; $i<=date("Y"); $i++) $listYears .= '<option value="'.$i.'" '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
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
			<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Generate New Empty Invoice').'</div>
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
				<form id="form1" action="'.$set->basepage.'" method="get">
					<select id="monthSelect" name="month" style="width: 100px;">'.$listMonths.'</select> / <select id="yearSelect" name="year" style="width: 100px;">'.$listYears.'</select> 
					'.lang('Status').': <select name="status" style="width: 100px;">
						<option value="">'.lang('All').'</option>
						<option value="pending" '.($status == "pending" ? 'selected="selected"' : '').'>'.lang('Pending').'...</option>
						<option value="paid" '.($status == "paid" ? 'selected="selected"' : '').'>'.lang('Paid').'</option>
					</select>
					'.lang('Payment Method').': <select name="paymentMethod" style="width: 140px;">
						<option value="">'.lang('All').'</option>
						<option value="bank" '.($paymentMethod == "bank" ? 'selected="selected"' : '').'>'.lang('Wire Transfer').'</option>
						<option value="Skrill" '.($paymentMethod == "Skrill" ? 'selected="selected"' : '').'>'.lang('Skrill').'</option>
						<option value="paypal" '.($paymentMethod == "paypal" ? 'selected="selected"' : '').'>'.lang('PayPal').'</option>
						<option value="webmoney" '.($paymentMethod == "webmoney" ? 'selected="selected"' : '').'>'.lang('Web Money').'</option>
						<option value="neteller" '.($paymentMethod == "neteller" ? 'selected="selected"' : '').'>'.lang('Neteller').'</option>
					</select>
					'.lang('Affiliate ID').': <input type="text" id="affiliate_id" name="affiliate_id" value="'.$affiliate_id.'" style="width: 100px;" />
					<input  type="submit" value="'.lang('View').'" />';
					if(!$set->isNetwork) {
					$set->content .= '
					<input name="delete" type="submit" value="'.lang('Delete').'" 
							onClick="return confirmation()"/>';
							
					}
					$set->content .= '
				</form>
			</div>
					<script>
						function confirmation() {
							var aff = isNaN(document.getElementById("affiliate_id").value) || !document.getElementById("affiliate_id").value ? " '.lang('ALL').' " : " '.lang('affiliate').' " + document.getElementById("affiliate_id").value;
							
							var msg = "'.lang('Are you sure you want to delete').' " + aff + " '.lang('payments for').' " 
							            + document.getElementById("yearSelect").value + "-" + document.getElementById("monthSelect").value + "?";
							
							return confirm(msg);
						}
					</script>
			<br />
			<div class="normalTableTitle">'.lang('Billing Reports').'</div>
			<form id="form1" action="'.$set->basepage.'" method="get" style="margin-top:8px;margin-bottom:8px;background: #EFEFEF;padding:2px;">
			&nbsp;&nbsp;<td>'.lang('Show rows').':</td><td align="left"><select name="valid" style="width: 180px;">
			<option '. ($valid==0 ? 'selected' :'') .' value="0">'.lang('Amount is not').' 0 </option><option '. ($valid==1 ? 'selected' :'') .' value="1">'.lang('All').'</option></select></td>
			<td><input  type="submit" value="'.lang('Filter').'" /><td>
			</form>
			
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
						<td style="text-align: left;">'.lang('Preferred Currency').'</td>
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
                
                $sql = "INSERT INTO payments_details (rdate,status,month,year,paymentID,merchant_id,affiliate_id) "
                        . "VALUES ('".dbDate()."','approved','".$month."','".$year."','".$paymentID."','0','".$affiliate_id."')";
                
		mysql_query($sql);
		_goto($set->basepage.'?act=view&paymentID='.$paymentID);
		break;
	
                
	case "sendInvoice":
            $sql = "SELECT * FROM payments_details WHERE paymentID='".$paymentID."'";
            $getInvoice = mysql_fetch_assoc(mysql_query($sql));
            
            if (!$getInvoice['id']) {
                _goto($set->basepage);
            }
            
            $affiliateInfo = dbGet($affiliate_id, "affiliates");
            $set->sendTo   = $affiliateInfo['mail'];
            $set->subject  = $affiliateInfo['username'] . ' - Invoice #' . $getInvoice['paymentID'] . ' ' . $getInvoice['month'] . '/' . $getInvoice['year'] . ' - ' . $set->webTitle;
            $set->body     = getPayment($paymentID, 1);
            sendMail(0, 0);
            updateUnit("payments_paid", "sentMail='1'", "paymentID='" . $getInvoice['paymentID'] . "'");
            _goto($set->refe . ($set->refe == $set->webAddress . "admin/billing.php" ? '?' : '&') . 'ty=1');
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
		mysql_query("UPDATE affiliates SET credit='".$db['creditLeft']."' WHERE id='".$db['affiliate_id']."'");
		_goto('/admin/billing.php?act=view&paymentID='.$db[paymentID]);
		break;
	
	// --------------------------------------------------------------------------------------------------------------------
		
	case "generate":
		if ($affiliate_id > 0) {
			$affWhere = " AND id='".$affiliate_id."'";
			mysql_query("DELETE FROM ".$appTable." WHERE affiliate_id='".$affiliate_id."' AND month='".$month."' AND year='".$year."'");
			mysql_query("DELETE FROM payments_paid WHERE affiliate_id='".$affiliate_id."' AND month='".$month."' AND year='".$year."'");
			}
		$set->pageTitle = lang('Generating Payments...');
		$invoiceDate = $year.'-'.($month < 10 ? '0'.$month : $month).'-01';
		$currentDate = date("Y-m-01",strtotime("+1 Month",strtotime($invoiceDate)));
		if (date("Y-m-d") < $currentDate) _goto($set->basepage);
		$getInvoices=mysql_result(mysql_query("SELECT COUNT(id) FROM ".$appTable." WHERE month='".$month."' AND year='".$year."'"),0);
		// if ($getInvoices > 0) _goto($set->basepage);
		$set->content .= '<script type="text/javascript">
				var affiliate_ids = new Array();
				var affiliateNames = new Array();
				';
			$affNum=0;
			$sql = "SELECT * FROM affiliates WHERE valid='1' ".$affWhere." ORDER BY id ASC";
			// die($sql);
			$qq=mysql_query($sql);
			while ($ww=mysql_fetch_assoc($qq)) {
				$chkExist = mysql_fetch_assoc(mysql_query("SELECT id FROM ".$appTable." WHERE month='".$month."' AND year='".$year."' AND affiliate_id='".$ww['id']."'"));
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
                // NEW.
                $cpaAmount           = 0;
                $chartAffiliates     = [];
                $arrSubAffiliatesIds = [];
                
		$getAffiliate = dbGet($affiliate_id, 'affiliates');
		$paymentID    = 'DE'.rand('1','5').$year.($month < 10 ? '0'.$month : $month).$affiliate_id.rand('5','9').'MO';
		$num          = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$from         = $year . '-' . ($month < 10 ? '0' . $month : $month) . '-01';
		$to           = $year . '-' . ($month < 10 ? '0' . $month : $month) . '-' . $num . ' 23:59:59'; // NEW.
		
                $sql = "SELECT id FROM " . $appTable 
                        . " WHERE month = '" . $month . "' AND year = '" . $year 
                        . "' AND affiliate_id = '" . $getAffiliate['id'] . "';";
                
		$chkExist = mysql_fetch_assoc(mysql_query($sql));
                
		if ($chkExist['id']) {
                    echo '<b>', $getAffiliate['username'], ' </b> [', $getAffiliate['id'], '] - <b>Exist!</b>';
                    exit;
                }
		
		// Sub Affiliates.
                // Check whether current affiliate is a "sub-affiliate" (of 618).
                $sql = "SELECT id FROM affiliates "
                        . "WHERE valid = '1' AND refer_id = '" . $getAffiliate['id'] 
                        . "' ORDER BY id DESC;";
                
		$affiliateqq = mysql_query($sql);
                
		while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {
                    $chartAffiliates[]     = "affiliate_id='" . $affiliateww['id'] . "'";
                    $arrSubAffiliatesIds[] = $affiliateww['id'];
                    unset($affiliateww);
                }
                
                
		$totalSubFtds = 0;
		$myReportType = 0;
		$qq = mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY pos");
                
		while ($ww = mysql_fetch_assoc($qq)) {
			$merchantName = strtolower($ww['name']);
			$merchantID   = $ww['id'];
                        
			$getDeal=mysql_fetch_assoc(mysql_query("SELECT amount FROM affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$ww['id']."' AND dealType='min_cpa' LIMIT 1"));
			$getDealCpa=mysql_fetch_assoc(mysql_query("SELECT id,amount FROM affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$ww['id']."' AND dealType='cpa' LIMIT 1"));
			$getDealDCpa=mysql_fetch_assoc(mysql_query("SELECT id,amount FROM affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$ww['id']."' AND dealType='dcpa' LIMIT 1"));
			$getTier=mysql_fetch_assoc(mysql_query("SELECT id,amount FROM affiliates_deals WHERE affiliate_id='".$affiliate_id."' AND merchant_id='".$ww['id']."' AND dealType!='tier'"));

			if ($getDealCpa['id']) {
                            $cpaAmount = $getDealCpa['amount'];
                        }
                        
			// Total Sub FTD's.  NEW.
                        foreach ($arrSubAffiliatesIds as $intSubAffId) {
                            $arrCommissions       = [];
                            $ftd_amountqq         = getTotalFtds($from, $to, $intSubAffId, $merchantID, $ww['wallet_id']);
                            $ftdUsers             = '';
                            $new_real_ftd         = 0;
                            $real_ftd_amount      = 0;
                            $new_ftd              = 0;
                            $ftd_amount['amount'] = 0;
                            
                            foreach ($ftd_amountqq as $totalftd) {
                                if (isset($dealType) && !empty($dealType)) {
                                    if ($dealType == 'cpc' || $dealType == 'cpm') {
                                        continue;
                                    }
                                }
                                
                                $real_ftd_amount += $totalftd['amount'];
                                $status           = 'approved';
                                $reason           = '';
                                $beforeNewFTD     = $new_ftd;
                                $new_real_ftd++;
                                
                                getFtdByDealType($merchantID, $totalftd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $new_ftd);
                                
                                if ($beforeNewFTD != $new_ftd) {
                                    $totalftd['isFTD'] = true;
                                    $arrCommissions[]  = getCommission($from, $to, 0, -1, $arrDealTypeDefaults, $totalftd, false, true);
                                }
                            }
                            
                            // Revenue share.
                            if (strtolower($ww['producttype']) == 'sportsbetting' || strtolower($ww['producttype']) == 'casino') {
                                $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantID, $intSubAffId, $arrDealTypeDefaults);
                                
                                foreach ($arrRevenueRanges as $arrRange) {
                                    $intCurrentRevenue = getRevenue(
                                            'WHERE merchant_id = ' . $merchantID . ' AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . 
                                            '" AND affiliate_id = "' . $intSubAffId . '"',
                                            $ww['producttype']
                                    );
                                    
                                    $row                 = array();
                                    $row['merchant_id']  = $merchantID;
                                    $row['affiliate_id'] = $intSubAffId;
                                    $row['banner_id']    = 0;
                                    $row['rdate']        = $arrRange['from'];
                                    $row['amount']       = $intCurrentRevenue;
                                    $row['isFTD']        = false;
                                    $arrCommissions[]    = getCommission($arrRange['from'], $arrRange['to'], 1, -1, $arrDealTypeDefaults, $row, false, true);
                                }    
                            }
                            
                            if (empty($arrCommissions)) {
                                continue;
                            }
                            
                            foreach ($arrCommissions as $arrCommission) {
                                foreach ($arrCommission as $strDealType => $arrQuantityCommission) {
                                    $intQuantity     = $arrQuantityCommission['quantity'];
                                    $floatCommission = $arrQuantityCommission['commission'];
                                    $strTraderId     = $arrQuantityCommission['trader_id'];
                                    
                                    if ($status == 'approved') {
                                        $sql = "SELECT id, status, notes FROM traders_tag "
                                             . "WHERE merchant_id = " . $ww['id'] . (empty($strTraderId) ? '' : " AND trader_id = '" . $strTraderId . "' ");
                                        
                                        $tag = mysql_fetch_assoc(mysql_query($sql));
                                        
                                        if (
                                            $tag['status'] == 'fraud'      || $tag['status'] == 'withdrawal' || 
                                            $tag['status'] == 'chargeback' || $tag['status'] == 'duplicates'
                                        ) {
                                            $status = 'canceled';
                                        }
                                        
                                        if ($tag['status'] == 'pending' || $tag['status'] == 'other') {
                                            $status = 'pending';
                                        }
                                        
                                        $reason = isset($tag['notes']) && !empty($tag['notes']) ? $tag['notes'] : $tag['status'];
                                        $sql    = "UPDATE traders_tag SET calReport = '1' WHERE id = '" . $tag['id'] . "';";
                                        mysql_query($sql);
                                    }
                                    
                                    $sql = "INSERT INTO " . $appTable 
                                            . " (rdate, status, reportType, month, year, paymentID, merchant_id, affiliate_id, 
                                                trader_id, amount, deposit, withdrawal,reason) 
                                            VALUES ('" . dbDate() . "', '" . $status . "', '" . $strDealType . "', '" . $month . "', '" . $year . "', '" 
                                                   . $paymentID . "', '" . $ww['id'] . "', '" . $intSubAffId . "', '" . $strTraderId . "', '" 
                                                   . $floatCommission . "', '" . $cpaAmount . "', '" . $withdrawal['amount'] . "', '" . $reason . "');";
                                    
                                    mysql_query($sql);
                                }
                            }
                            
                            unset($intSubAffId, $arrCommission, $new_real_ftd, $real_ftd_amount, $ftd_amount['amount']);
                        }
			// Total Sub FTD's
			
                    
                    // NEW FLOW.
                    $arrCommissions       = [];
                    $ftd_amountqq         = getTotalFtds($from, $to, $affiliate_id, $merchantID, $ww['wallet_id']);
                    $ftdUsers             = '';
                    $new_real_ftd         = 0;
                    $real_ftd_amount      = 0;
                    $new_ftd              = 0;
                    $ftd_amount['amount'] = 0;
                    
                    foreach ($ftd_amountqq as $totalftd) {
                        if (isset($dealType) && !empty($dealType)) {
                            if ($dealType == 'cpc' || $dealType == 'cpm') {
                                continue;
                            }
                        }
                        
                        $real_ftd_amount += $totalftd['amount'];
                        $status           = 'approved';
                        $reason           = '';
                        $beforeNewFTD     = $new_ftd;
                        $new_real_ftd++;
                        
                        getFtdByDealType($merchantID, $totalftd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $new_ftd);
                        
                        if ($beforeNewFTD != $new_ftd) {
                            $totalftd['isFTD'] = true;
                            $arrCommissions[]  = getCommission($from, $to, 0, -1, $arrDealTypeDefaults, $totalftd, false, true);
                        }
                    }
                    
                    // Revenue share. 
                    if (strtolower($ww['producttype']) == 'sportsbetting' || strtolower($ww['producttype']) == 'casino') {
                        $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $merchantID, $affiliate_id, $arrDealTypeDefaults);

                        foreach ($arrRevenueRanges as $arrRange) {
                            $intCurrentRevenue = getRevenue(
                                    'WHERE merchant_id = ' . $merchantID . ' AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . 
                                    '" AND affiliate_id = "' . $affiliate_id . '"',
                                    $ww['producttype']
                            );
                            
                            $row                 = array();
                            $row['merchant_id']  = $merchantID;
                            $row['affiliate_id'] = $affiliate_id;
                            $row['banner_id']    = 0;
                            $row['rdate']        = $arrRange['from'];
                            $row['amount']       = $intCurrentRevenue;
                            $row['isFTD']        = false;
                            $arrCommissions[]    = getCommission($arrRange['from'], $arrRange['to'], 1, -1, $arrDealTypeDefaults, $row, false, true);
                        }    
                    }
                    
                    if (empty($arrCommissions)) {
                        continue;
                    }
                    
                    foreach ($arrCommissions as $arrCommission) {
                        foreach ($arrCommission as $strDealType => $arrQuantityCommission) {
                            $intQuantity     = $arrQuantityCommission['quantity'];
                            $floatCommission = $arrQuantityCommission['commission'];
                            $strTraderId     = $arrQuantityCommission['trader_id'];
                            
                            if ($status == 'approved') {
                                $sql = "SELECT id, status, notes FROM traders_tag "
                                     . "WHERE merchant_id = " . $ww['id'] . (empty($strTraderId) ? '' : " AND trader_id = '" . $strTraderId . "' ");
                                
                                $tag = mysql_fetch_assoc(mysql_query($sql));
                                
                                if (
                                    $tag['status'] == 'fraud'      || $tag['status'] == 'withdrawal' || 
                                    $tag['status'] == 'chargeback' || $tag['status'] == 'duplicates'
                                ) {
                                    $status = 'canceled';
                                }
                                
                                if ($tag['status'] == 'pending' || $tag['status'] == 'other') {
                                    $status = 'pending';
                                }
                                
                                $reason = isset($tag['notes']) && !empty($tag['notes']) ? $tag['notes'] : $tag['status'];
                                $sql    = "UPDATE traders_tag SET calReport = '1' WHERE id = '" . $tag['id'] . "';";
                                mysql_query($sql);
                            }
                            
                            $sql = "INSERT INTO " . $appTable 
                                    . " (rdate, status, reportType, month, year, paymentID, merchant_id, affiliate_id, 
                                        trader_id, amount, deposit, withdrawal,reason) 
                                    VALUES ('" . dbDate() . "', '" . $status . "', '" . $strDealType . "', '" . $month . "', '" . $year . "', '" 
                                           . $paymentID . "', '" . $ww['id'] . "', '" . $affiliate_id . "', '" . $strTraderId . "', '" 
                                           . $floatCommission . "', '" . $cpaAmount . "', '" . $withdrawal['amount'] . "', '" . $reason . "');";
                            
                            if (mysql_query($sql)) {
                                $myReportType = $status == 'approved' ? $myReportType + 1 : $myReportType;
                            }
                        }
                    }
                } // End of merchants loop.
                
		// Add Network Bonus to the Affiliate that reach the goal!
		if ($myReportType > 0) {
                    $sql = "SELECT * FROM network_bonus "
                         . " WHERE min_ftd <= '" . $myReportType . "' AND (group_id = '99999' OR group_id = '" . $getAffiliate['group_id'] . "') "
                         . " ORDER BY min_ftd DESC LIMIT 0, 1;";
                    
                    $getBonus = mysql_fetch_assoc(mysql_query($sql));
                    
                    if ($getBonus['id']) {
                        $sql = "INSERT INTO " . $appTable . " (rdate, status, reportType, month, year, paymentID, affiliate_id, amount, reason)  
                                VALUES ('" . dbDate() . "', 'approved', 'bonus', '" . $month . "', '" . $year . "', '" 
                                           . $paymentID . "', '" . $affiliate_id . "', '" . $getBonus['bonus_amount'] . "', '" . $getBonus['title'] . "');";
                        
                        mysql_query($sql);
                    }
                }
		
		echo '<b>' , $getAffiliate['username'] , ' </b> [' , $getAffiliate['id'] , ']';
		break;
	
	
                
                
	case "details":
            $reportType  = isset($reportType)  && !empty($reportType)  ? " AND pd.reportType = '" . $reportType . "' " : '';
            $status      = isset($status)      && !empty($status)      ? " AND pd.status = '" . $status . "' " : '';
            $invoice     = isset($invoice)     && !empty($invoice)     ? " AND pd.paymentID = '" . $invoice . "' " : '';
            $merchant_id = isset($merchant_id) && !empty($merchant_id) ? ' AND pd.merchant_id = ' . $merchant_id . ' ' : '';
            $strReport   = '';
            $i           = 0;
            
            $sql = "SELECT pd.*, aff.username AS aff_username, dr.country AS trader_country, m.name AS merchant_name, "
                    . "("
                    .   "SELECT MIN(rdate) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id"
                    . ") AS deposit_date, "
                    . "("
                    . " SELECT amount FROM data_sales "
                    . " WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'deposit' ORDER BY rdate DESC LIMIT 0, 1"
                    . ") AS ftd_amount, "
                    . "("
                    . " SELECT COUNT(id) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'deposit'"
                    . " ) AS total_deposits, "
                    . "("
                    . " SELECT SUM(amount) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'deposit'"
                    . " ) AS deposits_amount, "
                    . "("
                    . " SELECT IFNULL(SUM(amount), 0) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'bonus'"
                    . " ) AS bonus_amount, "
                    . "("
                    . " SELECT IFNULL(SUM(amount), 0) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'volume'"
                    . " ) AS volume_amount, "
                    . "("
                    . " SELECT IFNULL(SUM(amount), 0) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'chargeback'"
                    . " ) AS chargeback_amount, "
                    . "("
                    . " SELECT IFNULL(SUM(amount), 0) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'withdrawal'"
                    . " ) AS withdrawal_amount, m.producttype AS producttype, "
                    . "(CASE 
                            WHEN (LOWER(m.producttype) = 'casino' OR LOWER(m.producttype) = 'sportsbetting') THEN 
                                (SELECT COUNT(id) FROM data_stats WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'bets') 
                            ELSE 
                                (SELECT COUNT(id) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'positions') 
                        END) AS trades "
                    . "FROM payments_details AS pd "
                    . "INNER JOIN affiliates AS aff ON aff.id = pd.affiliate_id "
                    . "INNER JOIN data_reg AS dr ON dr.trader_id = pd.trader_id AND  pd.merchant_id = dr.merchant_id "
                    . "INNER JOIN merchants AS m ON m.id = pd.merchant_id "
                    . "WHERE 1 = 1 " . $reportType . $status . $invoice . $merchant_id
                    . 'ORDER BY rdate DESC;';
            
            $resource = mysql_query($sql);
            
            while ($arrRow = mysql_fetch_assoc($resource)) {
                $strWhere = 'WHERE 1 = 1 ' 
                          . (isset($_GET['merchant_id']) && !empty($_GET['merchant_id']) ? ' AND merchant_id = ' . $_GET['merchant_id'] . ' ' : '') 
                          . (isset($arrRow['trader_id']) && !empty($arrRow['trader_id']) ? ' AND trader_id = ' . $arrRow['trader_id'] . ' ' : '');
                
                $floatRevenue  = getRevenue($strWhere, $arrRow['producttype']);
                $strReport    .= '<tr>
                                    <td>' . $arrRow['trader_id'] . '</td>
                                    <td>' . date('d/m/Y', strtotime($arrRow['rdate'])) . '</td>
                                    <td><a href="admin/affiliates.php?act=new&id=' . $arrRow['affiliate_id'] . '">' . $arrRow['affiliate_id'] . '</a></td>
                                    <td><a href="admin/affiliates.php?act=new&id=' . $arrRow['affiliate_id'] . '">' . $arrRow['aff_username'] . '</a></td>
                                    <td>' . longCountry($arrRow['trader_country']) . '</td>
                                    <td>' . $arrRow['merchant_id'] . '</td>
                                    <td>' . $arrRow['merchant_name'] . '</td>
                                    <td>' . $arrRow['deposit_date'] . '</td>
                                    <td>' . round($arrRow['ftd_amount'], 2) . '</td>
                                    <td>' . $arrRow['total_deposits'] . '</td>
                                    <td>' . round($arrRow['deposits_amount'], 2)  . '</td>
                                    <td>' . round($arrRow['bonus_amount'], 2) . '</td>
                                    <td>' . round($arrRow['volume_amount'], 2) . '</td>
                                    <td>' . round($arrRow['chargeback_amount'], 2) . '</td>
                                    <td>' . round($arrRow['withdrawal_amount'], 2) . '</td>
                                    <td>' . round($floatRevenue, 2) . '</td>
                                    <td>' . $arrRow['trades'] . '</td>
                                    <td>' . round($arrRow['amount'], 2) . '</td>
                                    <td>' . ucwords($arrRow['status']) . '</td>
                                    <td>' . $arrRow['reason'] . '</td>
                              </tr>';
                
                unset($arrRow, $strWhere, $floatRevenue);
                $i++;
            }
            
            if ($i > 0) {
                $set->sortTableScript = 1;
            }
            
            $set->pageTitle  = lang('Invoice') . ' #' . $invoice . ' ' . lang('Report');
            $set->sortTable  = 1;
            $set->totalRows  = $i;
            $set->content   .= '<div class="normalTableTitle" style="width: 1995px;">'.$set->pageTitle.'</div>
                    <div style="background: #F8F8F8;">
                        <table width="2000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
                            <thead><tr>
                                    <th>'.lang(ptitle('Trader ID')).'</th>
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
                                    <th>'.lang('Volume').'</th>
                                    <th>'.lang('Chargeback Amount').'</th>
                                    <th>'.lang('Withdrawal Amount').'</th>
                                    <th>'.lang('Net Revenue').'</th>
                                    <th>'.lang(ptitle('Trades')).'</th>
                                    <th>'.lang('Commission').'</th>
                                    <th>'.lang('Status').'</th>
                                    <th>'.lang('Notes').'</th>
                            </tr></thead><tfoot></tfoot>
                            <tbody>' . $strReport . '
                        </table>
                    </div>' . getPager();
            theme();
            break;
	
                
	case "changeStatus":
		mysql_query("UPDATE payments_details SET status='".$_GET['status']."' WHERE id='".$_GET['details_id']."'") or die("error");
		die('changed');
		break;
		
            
	case "changeNotes":
		$getNotes = mysql_fetch_assoc(mysql_query("SELECT id FROM traders_tag WHERE id='".$_GET['id']."'"));
		if ($getNotes['id']) mysql_query("UPDATE traders_tag SET notes='".mysql_escape_string($_GET['text'])."' WHERE id='".$_GET['id']."'") or die("error");
			else mysql_query("INSERT INTO traders_tag (valid,trader_id,rdate,merchant_id,status,notes) VALUES ('1','".$_GET['trader_id']."','".dbDate()."','".$_GET['merchant_id']."','other','".$_GET['text']."')") or die("error");
		die('changed');
		break;
	
}

exit;
?>