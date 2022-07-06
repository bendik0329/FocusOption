<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('global.php');


/**
 * Check if this file was "included" in "affiliate" directory.
 * 
 * @param  void
 * @return bool
 */
function isAffiliate()
{
    $arrPath = explode('/', $_SERVER['PHP_SELF']);
    return 'affiliate' == $arrPath[1];
}


function isLogin2() {
   
	global $set,$_SESSION;
	$resulta=function_mysql_query("SELECT id,username,password FROM affiliates WHERE id='".$_SESSION['aff_session_id']."' AND valid='1'",__FILE__,__FUNCTION__);
	$chk=mysql_fetch_assoc($resulta);
        
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($_SESSION['aff_session_id'] == $chk['id'] AND $session_serial == $_SESSION['aff_session_serial']) return true;
	return false;
	}

        $is__loggedin = isAffiliate();
        $boolIsLogen2 = !isLogin2() && $is__loggedin;
        
if ($boolIsLogen2) {
    _goto($set->SSLprefix.'affiliate/');
}


if ($is__loggedin){
$requestID = $set->userInfo['id'];
$requestMerchants = $arrMerchants['merchants'];
}

if ((isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) || (!empty($requestID) && is_numeric($requestID))) {
    $requestID= $_GET['id'];
    
    
	
	$sql = 'select merchants from affiliates where id = ' . $requestID . ' limit 0, 1';
	// die ($sql);
    $arrMerchants = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
    
    if (!empty($arrMerchants) && !empty($arrMerchants['merchants'])) {
        $requestMerchants = $arrMerchants['merchants'];
    }
}else {
	return "";
}

$merchantIDs = $requestMerchants;
$merchantIDs = str_replace('|',",",$merchantIDs);
$merchantsArr = explode(',',$merchantIDs);



$showRev = 0;
$showFtdAmount = 0;
$merchantsArr2 = Array();
if (!empty($merchantIDs))
for($i=0;$i<count($merchantsArr);$i++){
	
	$merID = $merchantsArr[$i];
	$merchantsArr2[$merID] = new stdClass();
	
	if ($set->hideNetRevenueForNonRevDeals){
		$qry = "SELECT amount FROM `affiliates_deals` WHERE affiliate_id='".$requestID."' and dealType='revenue' and amount>0 and merchant_id=".$merID;
                //die($qry);
		$revrslt=mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
		if ($revrslt['amount']>0) {
			$merchantsArr2[$merID]->showRev = 1;
			$showRev = 1;
			//$showRev = 0;
		}else{
			
			$merchantsArr2[$merID]->showRev = 0;
		}
	}else{
		$merchantsArr2[$merID]->showRev = 1;
		$showRev = 1;
	}



	if ($set->hideFTDamountForCPADeals){
		$qry = "SELECT amount FROM `affiliates_deals` WHERE affiliate_id='".$requestID."' and dealType='cpa' and amount>0 and merchant_id=".$merID;
		$cpaslt=mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
		if ($cpaslt['amount']>0) {
			$showFtdAmount=1;
			$merchantsArr2[$merID]->showFtdAmount = 1;
		}else{
			$merchantsArr2[$merID]->showFtdAmount = 0;

		}

	} else {
		$showFtdAmount= 1;
		$merchantsArr2[$merID]->showFtdAmount = 1;
	}

}


$act = 'new' == $act ? 'payment' : $act;




if ($boolIsLogen2) {
    $set->pageTitle = lang('Payment Method Details');
}

    
    
    
		$db=dbGet($requestID,"affiliates");
		if ($db['id'] != $requestID) _goto($set->SSLprefix.'affiliate/');
		
		// die ('gtrgrt');
		$paymentmethodparam = empty($db['paymentMethod']) ? $_POST['paymentMethod'] : $db['paymentMethod'];

		if ($act != "payment_save") $paymentMethod = $db['paymentMethod'];
		$set->content .= '<form id="frmPaymentDetails"  method="post">
							<input type="hidden" name="affiliate" value="'. $requestID .'">
						<table width="980" border="0" cellpadding="0" cellspacing="0" class="tblDetails">
							'.($ty ? '<tr><td align="left" style="color: green; font-size: 14px;"><img border="0" width="30" src="images/alert/alert_v.png" alt="" align="absmiddle" /> '.lang('Your payment method details has updated').'!</td></tr><tr height="30"></td></tr>' : '').'
							<tr>
								<td align="left">'. 
								(!empty($paymentmethodparam) ? '<b>'.lang('Payment method selected ').': </b><span style="color: green; font-size: 15px;text-transform:capitalize;" class="methodSelected"> ' . lang(ucwords($paymentmethodparam)). "</span><br><br>" : ""). 
								
									"<b>".lang('Click to change the primary payment method').':</b> <select id="paymentMethod" name="paymentMethod" onchange="chooseTab(this.value);">
										<option value="">'.lang('Please Select Payment Method').'</option>';
										
										
						$options = '';
						$paymentmethodparam = empty($db['paymentMethod']) ? $_POST['paymentMethod'] : $db['paymentMethod'];
						$paymentmethodparam = strtolower($paymentmethodparam);
						
						$avl = strtolower('*'.$set->availablePayments);
						// *paypal|skrill|-Skrill
									 if (strpos($avl,'-wire')>0 ) {} else { 
									 $options .= ('<option value="bank" '.($paymentmethodparam == "bank" ? 'selected' : '').'>'.lang('Wire Transfer').'</option>');
									 }
									 if (strpos($avl,'-paypal')>0 ) {} else { 
									 $options .= ('<option value="paypal" '.($paymentmethodparam == "paypal" ? 'selected' : '').'>'.lang('Paypal').'</option>');
									 }
									 if (strpos($avl,'-skrill')>0 ) {} else { 
									 $options .= ('<option value="skrill" '.($paymentmethodparam == "skrill" ? 'selected' : '').'>'.lang('Skrill').'</option>');
									 }
									 if (strpos($avl,'-webmoney')>0 ) {} else { 
									 $options .= ('<option value="webmoney" '.($paymentmethodparam == "webmoney" ? 'selected' : '').'>'.lang('WebMoney').'</option>');
									 }
									 if (strpos($avl,'-neteller')>0 ) {} else { 
									 $options .= ('<option value="neteller" '.( $paymentmethodparam == "neteller" ? 'selected' : '').'>'.lang('Neteller').'</option>');
									 }
									  if (strpos($avl,'-China UnionPay')>0 || strpos($avl,'-china unionpay')>0 || strpos($avl,'-chinaunio')>0 ) {} else { 
									 $options .= ('<option value="chinaunionpay" '.( $paymentmethodparam == "chinaunionpay" ? 'selected' : '').'>'.lang('China Union Pay').'</option>');
									 }
									 if (strpos($avl,'-epayments')>0 ) {} else { 
									 $options .= ('<option value="epayments" '.( $paymentmethodparam == "epayments" ? 'selected' : '').'>'.lang('ePayments').'</option>');
									 }									 if (strpos($avl,'-qiwi')>0 ) {} else { 
									 $options .= ('<option value="qiwi" '.( $paymentmethodparam == "qiwi" ? 'selected' : '').'>'.lang('QiWi').'</option>');
									 }									 if (strpos($avl,'-yandexmoney')>0 ) {} else { 
									 $options .= ('<option value="yandexmoney" '.( $paymentmethodparam == "yandexmoney" ? 'selected' : '').'>'.lang('YandexMoney').'</option>');
									 }									 if (strpos($avl,'-bitcoin')>0 ) {} else { 
									 $options .= ('<option value="bitcoin" '.( $paymentmethodparam == "bitcoin" ? 'selected' : '').'>'.lang('Bitcoins').'</option>');
									 }

								
										$set->content .= $options.
										'
									</select>
								</td>
							</tr><tr>
								<td height="20"></td>
							</tr>
						</table>
						<div id="bank" style="display: '.($paymentMethod == "bank" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="pay_firstname" name="bankdb[pay_firstname]" value="'.($bankdb['pay_firstname'] ? $bankdb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_bank').'>'.lang('Bank Name').':</td>
								<td align="right"><input type="text" id="pay_bank" name="bankdb[pay_bank]" value="'.($bankdb['pay_bank'] ? $bankdb['pay_bank'] : $db['pay_bank']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="pay_lastname" name="bankdb[pay_lastname]" value="'.($bankdb['pay_lastname'] ? $bankdb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_account').'>'.lang('Bank Account').':</td>
								<td align="right"><input type="text" id="pay_account" name="bankdb[pay_account]" value="'.($bankdb['pay_account'] ? $bankdb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="pay_address1" name="bankdb[pay_address1]" value="'.($bankdb['pay_address1'] ? $bankdb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_branch').'>'.lang('Bank Branch').':</td>
								<td align="right"><input type="text" id="pay_branch" name="bankdb[pay_branch]" value="'.($bankdb['pay_branch'] ? $bankdb['pay_branch'] : $db['pay_branch']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="bankdb[pay_address2]" value="'.($bankdb['pay_address2'] ? $bankdb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_email').'>'.lang('Bank Email').':</td>
								<td align="right"><input type="text" id="pay_email" name="bankdb[pay_email]" value="'.($bankdb['pay_email'] ? $bankdb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="pay_city" name="bankdb[pay_city]" value="'.($bankdb['pay_city'] ? $bankdb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_iban').'>'.lang('IBAN').':</td>
								<td align="right"><input type="text" id="pay_iban" name="bankdb[pay_iban]" value="'.($bankdb['pay_iban'] ? $bankdb['pay_iban'] : $db['pay_iban']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="bankdb[pay_zip]" value="'.($bankdb['pay_zip'] ? $bankdb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_swift').'>'.lang('Swift').':</td>
								<td align="right"><input type="text" id="pay_swift" name="bankdb[pay_swift]" value="'.($bankdb['pay_swift'] ? $bankdb['pay_swift'] : $db['pay_swift']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
							
								
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" valign="top" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left" valign="top"><select id="pay_country" name="bankdb[pay_country]" style="width: 259px;">'.getCountry(($bankdb['pay_country'] ? $bankdb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('account_name').'>'.lang('Account Name').':</td>
								<td align="right"><input type="text" name="bankdb[account_name]" value="'.($bankdb['account_name'] ? $bankdb['account_name'] : $db['account_name']).'" style="width: 248px;" /> </td>
								
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="bankdb[pay_company]" value="'.($bankdb['pay_company'] ? $bankdb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('account_number').'>'.lang('Account Number').':</td>
								<td align="right"><input type="text" name="bankdb[account_number]" value="'.($bankdb['account_number'] ? $bankdb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td align="left" width="110" valign="top">'.lang('More Information').':</td>
								<td align="left"><textarea name="bankdb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($bankdb['pay_info'] ? $bankdb['pay_info'] : $db['pay_info']).'</textarea></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select name="bankdb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;									
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $bankdb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
								/* 
								<option ' . ( $db['preferredCurrency']=='GBP' || $bankdb['preferredCurrency']=='GBP' ? ' selected ' : '' ) . '>GBP</option>
								<option ' . ( $db['preferredCurrency']=='EUR' || $bankdb['preferredCurrency']=='EUR' ? ' selected ' : '' ) . '>EUR</option>
								<option ' . ( $db['preferredCurrency']=='AUD' || $bankdb['preferredCurrency']=='AUD' ? ' selected ' : '' ) . '>AUD</option>
								<option ' . ( $db['preferredCurrency']=='CNY' || $bankdb['preferredCurrency']=='CNY' ? ' selected ' : '' ) . '>CNY</option>
								 */
								
									$set->content .= $options.'
								</select>
								</td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						<div id="paypal" style="display: '.($paymentMethod == "paypal" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text"  id="paypal_firstname" name="paypaldb[pay_firstname]" value="'.($paypaldb['pay_firstname'] ? $paypaldb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160" '.err('pay_account').'>'.lang('E-Mail Paypal Address').':</td>
								<td align="right"><input type="text" id="paypal_email" name="paypaldb[pay_account]" value="'.($paypaldb['pay_account'] ? $paypaldb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="paypal_lastname" name="paypaldb[pay_lastname]" value="'.($paypaldb['pay_lastname'] ? $paypaldb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="paypaldb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($paypaldb['pay_info'] ? $paypaldb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="paypal_address1" name="paypaldb[pay_address1]" value="'.($paypaldb['pay_address1'] ? $paypaldb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td width="160"></td>
							</tr><tr>
								<td colspan="4" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="paypaldb[pay_address2]" value="'.($paypaldb['pay_address2'] ? $paypaldb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="paypal_city" name="paypaldb[pay_city]" value="'.($paypaldb['pay_city']  ? $paypaldb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="paypaldb[pay_zip]" value="'.($paypaldb['pay_zip'] ? $paypaldb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left"><select id="paypal_country" name="paypaldb[pay_country]" style="width: 257px;">'.getCountry(($paypaldb['pay_country'] ? $paypaldb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="paypaldb[pay_company]" value="'.($paypaldb['pay_company'] ? $paypaldb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								
								<td align="right"><select name="paypaldb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;														
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $paypaldb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
								/* 
								<option ' . ( $db['preferredCurrency']=='GBP' || $bankdb['preferredCurrency']=='GBP' ? ' selected ' : '' ) . '>GBP</option>
								<option ' . ( $db['preferredCurrency']=='EUR' || $bankdb['preferredCurrency']=='EUR' ? ' selected ' : '' ) . '>EUR</option>
								<option ' . ( $db['preferredCurrency']=='AUD' || $bankdb['preferredCurrency']=='AUD' ? ' selected ' : '' ) . '>AUD</option>
								<option ' . ( $db['preferredCurrency']=='CNY' || $bankdb['preferredCurrency']=='CNY' ? ' selected ' : '' ) . '>CNY</option>
								 */
								
									$set->content .= $options.'
								</select>
								
								
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
							<div id="skrill" style="display: '.($paymentMethod == "skrill" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="skrill_firstname" name="Skrilldb[pay_firstname]" value="'.($Skrilldb['pay_firstname'] ? $Skrilldb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
								
								

								<td width="80"></td>
								<td align="left" width="160" '.err('pay_email').'>'.lang('Skrill E-mail Address').':</td>
								<td align="right"><input type="text" id="skrill_email" name="Skrilldb[pay_email]" value="'.($Skrilldb['pay_email'] ? $Skrilldb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="skrill_lastname" name="Skrilldb[pay_lastname]" value="'.($Skrilldb['pay_lastname'] ? $Skrilldb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="Skrilldb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($Skrilldb['pay_info'] ? $Skrilldb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="skrill_address1" name="Skrilldb[pay_address1]" value="'.($Skrilldb['pay_address1'] ? $Skrilldb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="4" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text"  name="Skrilldb[pay_address2]" value="'.($Skrilldb['pay_address2'] ? $Skrilldb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="skrill_city" name="Skrilldb[pay_city]" value="'.($Skrilldb['pay_city'] ? $Skrilldb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="Skrilldb[pay_zip]" value="'.($Skrilldb['pay_zip'] ? $Skrilldb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select id="skrill_country" name="Skrilldb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;														
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $Skrilldb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
							
								
									$set->content .= $options.'
								</select>
								
								
							
							
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left"><select name="Skrilldb[pay_country]" style="width: 257px;">'.getCountry(($Skrilldb['pay_country'] ? $Skrilldb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="Skrilldb[pay_company]" value="'.($Skrilldb['pay_company'] ? $Skrilldb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>	
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
						
						
						
						
						
						<div id="yandexmoney" style="display: '.($paymentMethod == "yandexmoney" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="ya_firstname" name="yandexmoneydb[pay_firstname]" value="'.($yandexmoneydb['pay_firstname'] ? $yandexmoneydb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160" '.err('pay_email').'>'.lang('yandexmoney E-mail Address').':</td>
								<td align="right"><input type="text" id="ya_email" name="yandexmoneydb[pay_email]" value="'.($yandexmoneydb['pay_email'] ? $yandexmoneydb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="ya_lastname" name="yandexmoneydb[pay_lastname]" value="'.($yandexmoneydb['pay_lastname'] ? $yandexmoneydb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="yandexmoneydb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($yandexmoneydb['pay_info'] ? $yandexmoneydb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="nt_address1" name="yandexmoneydb[pay_address1]" value="'.($yandexmoneydb['pay_address1'] ? $yandexmoneydb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="4" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="yandexmoneydb[pay_address2]" value="'.($yandexmoneydb['pay_address2'] ? $yandexmoneydb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
				
				
				 
									<td align="left" width="110" '.err('account_number').'>'.lang('Customer ID').':</td>
								<td align="right"><input type="text" name="yandexmoneydb[account_number]" value="'.($yandexmoneydb['account_number'] ? $yandexmoneydb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr><tr>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="nt_city" name="yandexmoneydb[pay_city]" value="'.($yandexmoneydb['pay_city'] ? $yandexmoneydb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select name="yandexmoneydb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;														
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $yandexmoneydb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
							
								
									$set->content .= $options.'
								</select>
								
								
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="yandexmoneydb[pay_zip]" value="'.($yandexmoneydb['pay_zip'] ? $yandexmoneydb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left"><select id="nt_country" name="yandexmoneydb[pay_country]" style="width: 257px;">'.getCountry(($yandexmoneydb['pay_country'] ? $yandexmoneydb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="yandexmoneydb[pay_company]" value="'.($yandexmoneydb['pay_company'] ? $yandexmoneydb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
						
						
						
						<div id="chinaunionpay" style="display: '.($paymentMethod == "chinaunionpay" ? 'block' : 'none').';">
						<table width="980" id="chinaunionpay" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="cu_firstname" name="Chinaunionpaydb[pay_firstname]" value="'.($Chinaunionpaydb['pay_firstname'] ? $Chinaunionpaydb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_bank').'>'.lang('China Union Pay Name').':</td>
								<td align="right"><input type="text" id="cu_bank" name="Chinaunionpaydb[pay_bank]" value="'.($Chinaunionpaydb['pay_bank'] ? $Chinaunionpaydb['pay_bank'] : $db['pay_bank']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="cu_lastname" name="Chinaunionpaydb[pay_lastname]" value="'.($Chinaunionpaydb['pay_lastname'] ? $Chinaunionpaydb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_account').'>'.lang('China Union Pay Account').':</td>
								<td align="right"><input type="text" id="cu_account" name="Chinaunionpaydb[pay_account]" value="'.($Chinaunionpaydb['pay_account'] ? $Chinaunionpaydb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="cu_address1" name="Chinaunionpaydb[pay_address1]" value="'.($Chinaunionpaydb['pay_address1'] ? $Chinaunionpaydb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_branch').'>'.lang('China Union Pay Branch').':</td>
								<td align="right"><input type="text" id="cu_branch" name="Chinaunionpaydb[pay_branch]" value="'.($Chinaunionpaydb['pay_branch'] ? $Chinaunionpaydb['pay_branch'] : $db['pay_branch']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" id="cu_address2" name="Chinaunionpaydb[pay_address2]" value="'.($Chinaunionpaydb['pay_address2'] ? $Chinaunionpaydb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_email').'>'.lang('Email').':</td>
								<td align="right"><input type="text" id="cu_email" name="Chinaunionpaydb[pay_email]" value="'.($Chinaunionpaydb['pay_email'] ? $Chinaunionpaydb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="cu_city" name="Chinaunionpaydb[pay_city]" value="'.($Chinaunionpaydb['pay_city'] ? $Chinaunionpaydb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_iban').'>'.lang('IBAN').':</td>
								<td align="right"><input type="text" id="cu_iban" name="Chinaunionpaydb[pay_iban]" value="'.($Chinaunionpaydb['pay_iban'] ? $Chinaunionpaydb['pay_iban'] : $db['pay_iban']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" id="cu_zip" name="Chinaunionpaydb[pay_zip]" value="'.($Chinaunionpaydb['pay_zip'] ? $Chinaunionpaydb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_swift').'>'.lang('Swift').':</td>
								<td align="right"><input type="text" id="cu_swift" name="Chinaunionpaydb[pay_swift]" value="'.($Chinaunionpaydb['pay_swift'] ? $Chinaunionpaydb['pay_swift'] : $db['pay_swift']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
							
								
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" valign="top" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left" valign="top"><select id="cu_country" name="Chinaunionpaydb[pay_country]" style="width: 259px;">'.getCountry(($Chinaunionpaydb['pay_country'] ? $Chinaunionpaydb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('account_name').'>'.lang('Account Name').':</td>
								<td align="right"><input type="text" id="cu_account_name" name="Chinaunionpaydb[account_name]" value="'.($Chinaunionpaydb['account_name'] ? $Chinaunionpaydb['account_name'] : $db['account_name']).'" style="width: 248px;" /> </td>
								
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" id="cu_company" name="Chinaunionpaydb[pay_company]" value="'.($Chinaunionpaydb['pay_company'] ? $Chinaunionpaydb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('account_number').'>'.lang('Account Number').':</td>
								<td align="right"><input type="text" id="cu_account_number" name="Chinaunionpaydb[account_number]" value="'.($Chinaunionpaydb['account_number'] ? $Chinaunionpaydb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td align="left" width="110" valign="top">'.lang('More Information').':</td>
								<td align="left"><textarea id="cu_info" name="Chinaunionpaydb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($Chinaunionpaydb['pay_info'] ? $Chinaunionpaydb['pay_info'] : $db['pay_info']).'</textarea></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select id="cu_preferredCurrency" name="Chinaunionpaydb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;									
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $Chinaunionpaydb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
								/* 
								<option ' . ( $db['preferredCurrency']=='GBP' || $Chinaunionpaydb['preferredCurrency']=='GBP' ? ' selected ' : '' ) . '>GBP</option>
								<option ' . ( $db['preferredCurrency']=='EUR' || $Chinaunionpaydb['preferredCurrency']=='EUR' ? ' selected ' : '' ) . '>EUR</option>
								<option ' . ( $db['preferredCurrency']=='AUD' || $Chinaunionpaydb['preferredCurrency']=='AUD' ? ' selected ' : '' ) . '>AUD</option>
								<option ' . ( $db['preferredCurrency']=='CNY' || $Chinaunionpaydb['preferredCurrency']=='CNY' ? ' selected ' : '' ) . '>CNY</option>
								 */
								
									$set->content .= $options.'
								</select>
								</td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
						
						
						
						<div id="epayments" style="display: '.(strtolower($paymentMethod) == "epayments" ? 'block' : 'none').';">
						<table width="980" id="epayments" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="epay_firstname" name="epaymentsdb[pay_firstname]" value="'.($epaymentsdb['pay_firstname'] ? $epaymentsdb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_bank').'>'.lang('ePayments Name').':</td>
								<td align="right"><input type="text" id="epay_bank" name="epaymentsdb[pay_bank]" value="'.($epaymentsdb['pay_bank'] ? $epaymentsdb['pay_bank'] : $db['pay_bank']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="epay_lastname" name="epaymentsdb[pay_lastname]" value="'.($epaymentsdb['pay_lastname'] ? $epaymentsdb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_account').'>'.lang('ePayments Account').':</td>
								<td align="right"><input type="text" id="epay_account" name="epaymentsdb[pay_account]" value="'.($epaymentsdb['pay_account'] ? $epaymentsdb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="epay_address1" name="epaymentsdb[pay_address1]" value="'.($epaymentsdb['pay_address1'] ? $epaymentsdb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_branch').'>'.lang('ePayments Branch').':</td>
								<td align="right"><input type="text" id="epay_branch" name="epaymentsdb[pay_branch]" value="'.($epaymentsdb['pay_branch'] ? $epaymentsdb['pay_branch'] : $db['pay_branch']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" id="cu_address2" name="epaymentsdb[pay_address2]" value="'.($epaymentsdb['pay_address2'] ? $epaymentsdb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_email').'>'.lang('Email').':</td>
								<td align="right"><input type="text" id="epay_email" name="epaymentsdb[pay_email]" value="'.($epaymentsdb['pay_email'] ? $epaymentsdb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="cu_city" name="epaymentsdb[pay_city]" value="'.($epaymentsdb['pay_city'] ? $epaymentsdb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_iban').'>'.lang('IBAN').':</td>
								<td align="right"><input type="text" id="epay_iban" name="epaymentsdb[pay_iban]" value="'.($epaymentsdb['pay_iban'] ? $epaymentsdb['pay_iban'] : $db['pay_iban']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" id="epay_zip" name="epaymentsdb[pay_zip]" value="'.($epaymentsdb['pay_zip'] ? $epaymentsdb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_swift').'>'.lang('Swift').':</td>
								<td align="right"><input type="text" id="epay_swift" name="epaymentsdb[pay_swift]" value="'.($epaymentsdb['pay_swift'] ? $epaymentsdb['pay_swift'] : $db['pay_swift']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
							
								
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" valign="top" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left" valign="top"><select id="epay_country" name="epaymentsdb[pay_country]" style="width: 259px;">'.getCountry(($epaymentsdb['pay_country'] ? $epaymentsdb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('account_name').'>'.lang('Account Name').':</td>
								<td align="right"><input type="text" id="epay_account_name" name="epaymentsdb[account_name]" value="'.($epaymentsdb['account_name'] ? $epaymentsdb['account_name'] : $db['account_name']).'" style="width: 248px;" /> </td>
								
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" id="epay_company" name="epaymentsdb[pay_company]" value="'.($epaymentsdb['pay_company'] ? $epaymentsdb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('account_number').'>'.lang('Account Number').':</td>
								<td align="right"><input type="text" id="epay_account_number" name="epaymentsdb[account_number]" value="'.($epaymentsdb['account_number'] ? $epaymentsdb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td align="left" width="110" valign="top">'.lang('More Information').':</td>
								<td align="left"><textarea id="epay_info" name="epaymentsdb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($epaymentsdb['pay_info'] ? $epaymentsdb['pay_info'] : $db['pay_info']).'</textarea></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select id="epay_preferredCurrency" name="epaymentsdb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;									
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $epaymentsdb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
								/* 
								<option ' . ( $db['preferredCurrency']=='GBP' || $epaymentsdb['preferredCurrency']=='GBP' ? ' selected ' : '' ) . '>GBP</option>
								<option ' . ( $db['preferredCurrency']=='EUR' || $epaymentsdb['preferredCurrency']=='EUR' ? ' selected ' : '' ) . '>EUR</option>
								<option ' . ( $db['preferredCurrency']=='AUD' || $epaymentsdb['preferredCurrency']=='AUD' ? ' selected ' : '' ) . '>AUD</option>
								<option ' . ( $db['preferredCurrency']=='CNY' || $epaymentsdb['preferredCurrency']=='CNY' ? ' selected ' : '' ) . '>CNY</option>
								 */
								
									$set->content .= $options.'
								</select>
								</td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
						
						
						<div id="qiwi" style="display: '.(strtolower($paymentMethod) == "qiwi" ? 'block' : 'none').';">
						<table width="980" id="qiwi" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="epay_firstname" name="qiwidb[pay_firstname]" value="'.($qiwidb['pay_firstname'] ? $qiwidb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_bank').'>'.lang('QiWi Name').':</td>
								<td align="right"><input type="text" id="qiwi_bank" name="qiwidb[pay_bank]" value="'.($qiwidb['pay_bank'] ? $qiwidb['pay_bank'] : $db['pay_bank']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="qiwi_lastname" name="qiwidb[pay_lastname]" value="'.($qiwidb['pay_lastname'] ? $qiwidb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_account').'>'.lang('QiWi Account').':</td>
								<td align="right"><input type="text" id="qiwi_account" name="qiwidb[pay_account]" value="'.($qiwidb['pay_account'] ? $qiwidb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="qiwi_address1" name="qiwidb[pay_address1]" value="'.($qiwidb['pay_address1'] ? $qiwidb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_branch').'>'.lang('QiWi Branch').':</td>
								<td align="right"><input type="text" id="qiwi_branch" name="qiwidb[pay_branch]" value="'.($qiwidb['pay_branch'] ? $qiwidb['pay_branch'] : $db['pay_branch']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" id="cu_address2" name="qiwidb[pay_address2]" value="'.($qiwidb['pay_address2'] ? $qiwidb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_email').'>'.lang('Email').':</td>
								<td align="right"><input type="text" id="qiwi_email" name="qiwidb[pay_email]" value="'.($qiwidb['pay_email'] ? $qiwidb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="cu_city" name="qiwidb[pay_city]" value="'.($qiwidb['pay_city'] ? $qiwidb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_iban').'>'.lang('IBAN').':</td>
								<td align="right"><input type="text" id="epay_iban" name="qiwidb[pay_iban]" value="'.($qiwidb['pay_iban'] ? $qiwidb['pay_iban'] : $db['pay_iban']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" id="epay_zip" name="qiwidb[pay_zip]" value="'.($qiwidb['pay_zip'] ? $qiwidb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_swift').'>'.lang('Swift').':</td>
								<td align="right"><input type="text" id="qiwi_swift" name="qiwidb[pay_swift]" value="'.($qiwidb['pay_swift'] ? $qiwidb['pay_swift'] : $db['pay_swift']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
							
								
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" valign="top" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left" valign="top"><select id="qiwi_country" name="qiwidb[pay_country]" style="width: 259px;">'.getCountry(($qiwidb['pay_country'] ? $qiwidb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('account_name').'>'.lang('Account Name').':</td>
								<td align="right"><input type="text" id="qiwi_account_name" name="qiwidb[account_name]" value="'.($qiwidb['account_name'] ? $qiwidb['account_name'] : $db['account_name']).'" style="width: 248px;" /> </td>
								
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" id="qiwi_company" name="qiwidb[pay_company]" value="'.($qiwidb['pay_company'] ? $qiwidb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('account_number').'>'.lang('Account Number').':</td>
								<td align="right"><input type="text" id="qiwi_account_number" name="qiwidb[account_number]" value="'.($qiwidb['account_number'] ? $qiwidb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td align="left" width="110" valign="top">'.lang('More Information').':</td>
								<td align="left"><textarea id="qiwi_info" name="qiwidb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($qiwidb['pay_info'] ? $qiwidb['pay_info'] : $db['pay_info']).'</textarea></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select id="qiwi_preferredCurrency" name="qiwidb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;									
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $qiwidb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
							
									$set->content .= $options.'
								</select>
								</td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
						
						
						
						
						
						
						<div id="neteller" style="display: '.($paymentMethod == "neteller" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="ya_firstname" name="Netellerdb[pay_firstname]" value="'.($Netellerdb['pay_firstname'] ? $Netellerdb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160" '.err('pay_email').'>'.lang('Neteller E-mail Address').':</td>
								<td align="right"><input type="text" id="nt_email" name="Netellerdb[pay_email]" value="'.($Netellerdb['pay_email'] ? $Netellerdb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="nt_lastname" name="Netellerdb[pay_lastname]" value="'.($Netellerdb['pay_lastname'] ? $Netellerdb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="Netellerdb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($Netellerdb['pay_info'] ? $Netellerdb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="nt_address1" name="Netellerdb[pay_address1]" value="'.($Netellerdb['pay_address1'] ? $Netellerdb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="4" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="Netellerdb[pay_address2]" value="'.($Netellerdb['pay_address2'] ? $Netellerdb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
				
				
				 
									<td align="left" width="110" '.err('account_number').'>'.lang('Customer ID').':</td>
								<td align="right"><input type="text" name="Netellerdb[account_number]" value="'.($Netellerdb['account_number'] ? $Netellerdb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr><tr>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="nt_city" name="Netellerdb[pay_city]" value="'.($Netellerdb['pay_city'] ? $Netellerdb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select name="Netellerdb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;														
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $Netellerdb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
							
								
									$set->content .= $options.'
								</select>
								
								
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="Netellerdb[pay_zip]" value="'.($Netellerdb['pay_zip'] ? $Netellerdb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left"><select id="nt_country" name="Netellerdb[pay_country]" style="width: 257px;">'.getCountry(($Netellerdb['pay_country'] ? $Netellerdb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="Netellerdb[pay_company]" value="'.($Netellerdb['pay_company'] ? $Netellerdb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
						
						
						
						<div id="bitcoin" style="display: '.($paymentMethod == "bitcoin" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="bt_firstname" name="bitcoindb[pay_firstname]" value="'.($bitcoindb['pay_firstname'] ? $bitcoindb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160" '.err('pay_email').'>'.lang('bitcoin E-mail Address').':</td>
								<td align="right"><input type="text" id="bt_email" name="bitcoindb[pay_email]" value="'.($bitcoindb['pay_email'] ? $bitcoindb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="bt_lastname" name="bitcoindb[pay_lastname]" value="'.($bitcoindb['pay_lastname'] ? $bitcoindb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="bitcoindb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($bitcoindb['pay_info'] ? $bitcoindb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="bt_address1" name="bitcoindb[pay_address1]" value="'.($bitcoindb['pay_address1'] ? $bitcoindb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="4" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="bitcoindb[pay_address2]" value="'.($bitcoindb['pay_address2'] ? $bitcoindb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
				
				
				 
									<td align="left" width="110" '.err('account_number').'>'.lang('Customer ID').':</td>
								<td align="right"><input type="text" name="bitcoindb[account_number]" value="'.($bitcoindb['account_number'] ? $bitcoindb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr><tr>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="nt_city" name="bitcoindb[pay_city]" value="'.($bitcoindb['pay_city'] ? $bitcoindb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select name="bitcoindb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;														
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $bitcoindb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
							
								
									$set->content .= $options.'
								</select>
								
								
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="bitcoindb[pay_zip]" value="'.($bitcoindb['pay_zip'] ? $bitcoindb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left"><select id="nt_country" name="bitcoindb[pay_country]" style="width: 257px;">'.getCountry(($bitcoindb['pay_country'] ? $bitcoindb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="bitcoindb[pay_company]" value="'.($bitcoindb['pay_company'] ? $bitcoindb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
						<div id="webmoney" style="display: '.($paymentMethod == "webmoney" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="wm_firstname" name="webmoneyldb[pay_firstname]" value="'.($webmoneyldb['pay_firstname'] ? $webmoneyldb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160" '.err('pay_email').'>'.lang('Web Money Account ID').':</td>
								<td align="right"><input type="text" id="wm_email" name="webmoneyldb[pay_email]" value="'.($webmoneyldb['pay_email'] ? $webmoneyldb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="wm_lastname" name="webmoneyldb[pay_lastname]" value="'.($webmoneyldb['pay_lastname'] ? $webmoneyldb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="webmoneyldb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($webmoneyldb['pay_info'] ? $webmoneyldb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="wm_address1" name="webmoneyldb[pay_address1]" value="'.($webmoneyldb['pay_address1'] ? $webmoneyldb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="4" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="webmoneyldb[pay_address2]" value="'.($webmoneyldb['pay_address2'] ? $webmoneyldb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="wm_city" name="webmoneyldb[pay_city]" value="'.($webmoneyldb['pay_city'] ? $webmoneyldb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select name="webmoneyldb[preferredCurrency]"  style="width: 248px;">';
								
								$matches = explode('|',$set->availableCurrencies);
								$options="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;
								$options .= '<option ' . ( $db['preferredCurrency']==$val || $webmoneyldb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
							
								
									$set->content .= $options.'
								</select>
								
								
								
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="webmoneyldb[pay_zip]" value="'.($webmoneyldb['pay_zip'] ? $webmoneyldb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left"><select id="wm_country" name="webmoneyldb[pay_country]" style="width: 257px;">'.getCountry(($webmoneyldb['pay_country'] ? $webmoneyldb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
								<td align="right"></td>

							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="webmoneyldb[pay_company]" value="'.($webmoneyldb['pay_company'] ? $webmoneyldb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
								</tr><tr>
								
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
						
						
						</form>
					<script type="text/javascript">
						function chooseTab(value) {
							if (value == "bank") {
								gid(\'bank\').style.display=\'block\';
								gid(\'paypal\').style.display=\'none\';
								gid(\'skrill\').style.display=\'none\';
								gid(\'webmoney\').style.display=\'none\';
								gid(\'neteller\').style.display=\'none\';
								gid(\'chinaunionpay\').style.display=\'none\';
								gid(\'epayments\').style.display=\'none\';
								gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'none\';
								
								} else if (value == "paypal") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'block\';
								gid(\'skrill\').style.display=\'none\';
								gid(\'webmoney\').style.display=\'none\';
								gid(\'neteller\').style.display=\'none\';
								gid(\'chinaunionpay\').style.display=\'none\';
								gid(\'epayments\').style.display=\'none\';
																gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'none\';
								
								} else if (value == "skrill") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'none\';
								gid(\'skrill\').style.display=\'block\';
								gid(\'webmoney\').style.display=\'none\';
								gid(\'neteller\').style.display=\'none\';
								gid(\'chinaunionpay\').style.display=\'none\';
								gid(\'epayments\').style.display=\'none\';
																gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'none\';
								}
								 else if (value == "neteller") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'none\';
								gid(\'skrill\').style.display=\'none\';
								gid(\'webmoney\').style.display=\'none\';
								gid(\'neteller\').style.display=\'block\';
								gid(\'chinaunionpay\').style.display=\'none\';
																gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'none\';
								gid(\'epayments\').style.display=\'none\';
								}
								 else if (value == "webmoney") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'none\';
								gid(\'skrill\').style.display=\'none\';
								gid(\'webmoney\').style.display=\'block\';
								gid(\'neteller\').style.display=\'none\';
								gid(\'chinaunionpay\').style.display=\'none\';
																gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'none\';
								gid(\'epayments\').style.display=\'none\';
								}
								else if (value == "chinaunionpay") {
									gid(\'bank\').style.display=\'none\';
									gid(\'paypal\').style.display=\'none\';
									gid(\'skrill\').style.display=\'none\';
									gid(\'chinaunionpay\').style.display=\'block\';
									gid(\'webmoney\').style.display=\'none\';
									gid(\'neteller\').style.display=\'none\';
									gid(\'epayments\').style.display=\'none\';
																	gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'none\';
								}
								else if (value == "epayments") {
									gid(\'bank\').style.display=\'none\';
									gid(\'paypal\').style.display=\'none\';
									gid(\'skrill\').style.display=\'none\';
									gid(\'chinaunionpay\').style.display=\'none\';
									gid(\'webmoney\').style.display=\'none\';
									gid(\'neteller\').style.display=\'none\';
									gid(\'epayments\').style.display=\'block\';
																	gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'none\';
								}
								
																else if (value == "qiwi") {
									gid(\'bank\').style.display=\'none\';
									gid(\'paypal\').style.display=\'none\';
									gid(\'skrill\').style.display=\'none\';
									gid(\'chinaunionpay\').style.display=\'none\';
									gid(\'webmoney\').style.display=\'none\';
									gid(\'neteller\').style.display=\'none\';
									gid(\'epayments\').style.display=\'none\';
																	gid(\'qiwi\').style.display=\'block\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'none\';
								}
																else if (value == "yandexmoney") {
									gid(\'bank\').style.display=\'none\';
									gid(\'paypal\').style.display=\'none\';
									gid(\'skrill\').style.display=\'none\';
									gid(\'chinaunionpay\').style.display=\'none\';
									gid(\'webmoney\').style.display=\'none\';
									gid(\'neteller\').style.display=\'none\';
									gid(\'epayments\').style.display=\'none\';
																	gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'none\';
								gid(\'yandexmoney\').style.display=\'block\';
								}
																else if (value == "bitcoin") {
									gid(\'bank\').style.display=\'none\';
									gid(\'paypal\').style.display=\'none\';
									gid(\'skrill\').style.display=\'none\';
									gid(\'chinaunionpay\').style.display=\'none\';
									gid(\'webmoney\').style.display=\'none\';
									gid(\'neteller\').style.display=\'none\';
									gid(\'epayments\').style.display=\'none\';
																	gid(\'qiwi\').style.display=\'none\';
								gid(\'bitcoin\').style.display=\'block\';
								gid(\'yandexmoney\').style.display=\'none\';
								}
								
							}
							
							$(document).ready(function(){
								$("#frmPaymentDetails").on("submit",function(e){
									e.preventDefault();
									// alert ($("#paymentMethod").val());
									if($("#paymentMethod").val()=="bank"){
											var check_ids = new Array("pay_firstname","pay_lastname","pay_address1","pay_city","pay_country","pay_bank","pay_account","pay_branch","pay_email","pay_iban","pay_swift");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Bank Name').'","'.lang('Bank Account').'","'.lang('Bank Branch').'","'.lang('Email').'","'.lang('IBAN').'","'.lang('Swift').'");
											var email = $("#pay_email");
									}
									else if($("#paymentMethod").val()=="paypal"){
											var check_ids = new Array("paypal_firstname","paypal_lastname","paypal_address1","paypal_city","paypal_country","paypal_email");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'");
											var email = $("#paypal_email");
									}
									else if($("#paymentMethod").val()=="skrill"){
											var check_ids = new Array("skrill_firstname","skrill_lastname","skrill_address1","skrill_city","skrill_country","skrill_email");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'");
											var email = $("#skrill_email");
									}
									else if($("#paymentMethod").val()=="webmoney"){
											var check_ids = new Array("wm_firstname","wm_lastname","wm_address1","wm_city","wm_country","wm_email");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'");
											var email = $("#wm_email");
									}
									else if($("#paymentMethod").val()=="neteller"){
											var check_ids = new Array("nt_firstname","nt_lastname","nt_address1","nt_city","nt_country","nt_email");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'");
											var email = $("#nt_email");
									}
									else if($("#paymentMethod").val()=="chinaunionpay"){
											var check_ids = new Array("cu_firstname","cu_lastname","cu_address1","cu_city","cu_country","cu_email","cu_bank","cu_branch","cu_iban","cu_swift");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'","'.lang('Bank').'","'.lang('Branch').'","'.lang('IBAN').'","'.lang('Swift').'");
											var email = $("#cu_email");
									}
									
											else if($("#paymentMethod").val()=="qiwi"){
											var check_ids = new Array("pay_firstname","pay_lastname","pay_address1","pay_city","pay_country","pay_email","pay_bank","pay_branch","pay_iban","pay_swift");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'","'.lang('Bank').'","'.lang('Branch').'","'.lang('IBAN').'","'.lang('Swift').'");
											var email = $("#pay_email");
									}
											else if($("#paymentMethod").val()=="yandexmoney"){
											var check_ids = new Array("ya_firstname","ya_lastname","ya_address1","pay_city","pay_country","pay_email","pay_bank","pay_branch","pay_iban","pay_swift");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'","'.lang('Bank').'","'.lang('Branch').'","'.lang('IBAN').'","'.lang('Swift').'");
											var email = $("#pay_email");
									}
											else if($("#paymentMethod").val()=="bitcoin"){
											var check_ids = new Array("bt_firstname","bt_lastname","bt_address1","pay_city","pay_country","pay_email","pay_bank","pay_branch","pay_iban","pay_swift");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'","'.lang('Bank').'","'.lang('Branch').'","'.lang('IBAN').'","'.lang('Swift').'");
											var email = $("#pay_email");
									}
									else if($("#paymentMethod").val()=="epayments"){
											var check_ids = new Array("epay_firstname","epay_lastname","epay_address1","epay_city","epay_country","epay_email","epay_bank","epay_branch","epay_iban","epay_swift");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'","'.lang('Bank').'","'.lang('Branch').'","'.lang('IBAN').'","'.lang('Swift').'");
											var email = $("#epay_email");
									}
									
									
									
									var check_ids_length = check_ids.length;
									for (i = 0; i < check_ids_length; i++) {
										key = $("#"+check_ids[i]);
										
										if (key.val() == "") {
											// alert("Please fill out "+names[i]);
											  $.fancybox({ 
											 closeBtn:false, 
											  minWidth:"250", 
											  minHeight:"180", 
											  autoCenter: true, 
											  afterClose:function(){
												  key.focus();
											  },			  
											  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Please fill in&nbsp;'). '" + names[i] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
											  });
											  key.focus();
											return false;
										}
										else{
												if(names[i] != "Country"){
													val =key.val(); 
													//if (val.match(/^[\w@].+$/) == null)
													if(/^[-a-zA-Z0-9@+._:// \u00C0-\u1FFF\u2C00-\uD7FF]+$/.test(val) == false)
													{
														msg = "";
														if(val[0] == ".")
															msg = "'. lang('Username should start with \'@\' or any alphanumeric character') .'";
														else
															msg = "'. lang('Special characters are not allowed.') .'";
															$.fancybox({ 
													 closeBtn:false, 
													  minWidth:"250", 
													  minHeight:"180", 
													  autoCenter: true, 
													  afterClose:function(){
														  key.focus();
													  },			  
													  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +msg + " "+ names[i] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
													  });
													  key.focus();
													return false;
												}
												}
											 
										}
									}

									if (($("#paymentMethod").val()!="webmoney" && !(pattern.test(email.val()))) || !(email.val())) {
										//alert("Your e-mail is not valid");
										 $.fancybox({ 
										 closeBtn:false, 
										  minWidth:"250", 
											  minHeight:"180", 
										  autoCenter: true, 
										  afterClose:function(){
											  email.focus();
										  },			  
										  content: "<h1><div style=\'float:left;\'><img src=\'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'.lang('Your e-mail is not valid.').'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' value=\''.lang('Continue').'\'  onClick=\'$.fancybox.close()\'></div></div>" 
										  });
										
										return false;
										}
										
										
											var obj = $(this).serializeArray();
											$.post("'.$set->SSLprefix.'common/AffiliatePaymentSave.php", obj, function(res) {
											if(res){
												res = res.split("-");
												$(".methodSelected").html(res[1]);
												  $.fancybox({ 
												 closeBtn:false, 
												  minWidth:"250", 
												  minHeight:"180", 
												  autoCenter: true, 
												  afterLoad:function(){
													  setTimeout(function () { $.fancybox.close(); }, 3000); 
												  },
												  content: "<div align=\'center\' style=\'margin-top:40px;\'><div id=\'alert1\' style=\'display:block;margin:0px auto;\'><h2>'.lang('Payment details saved.').'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\'Continue\' onClick=\'$.fancybox.close()\'></div></div>" 
												  });
											}
										});
										return false;
								
									
								
								});
							});
					</script>';
		
		
