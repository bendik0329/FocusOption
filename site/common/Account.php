<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('global.php');


function isLogin2() {
	global $set,$_SESSION;
	$resulta=function_mysql_query("SELECT id,username,password FROM affiliates WHERE id='".$_SESSION['aff_session_id']."' AND valid='1'",__FILE__,__FUNCTION__);
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($_SESSION['aff_session_id'] == $chk['id'] AND $session_serial == $_SESSION['aff_session_serial']) return true;
	return false;
	}

if (isLogin2()) {
    _goto($set->SSLprefix.'affiliate/');
}



    $requestID = $set->userInfo['id'];
if (isset($_GET['id']) && !empty($_GET['id']) && is_numeric($_GET['id'])) {
    $requestID = $_GET['id'];
    
    $sql = 'select merchants from affiliates where id = ' . $requestID . ' limit 0, 1';
    $arrMerchants = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
    
    if (!empty($arrMerchants) && !empty($arrMerchants['merchants'])) {
        $set->userInfo['merchants'] = $arrMerchants['merchants'];
    }
}

$merchantIDs = $set->userInfo['merchants'];
$merchantIDs = str_replace('|',",",$merchantIDs);
$merchantsArr = explode(',',$merchantIDs);




/*
$showRev = 0;

if ($set->hideNetRevenueForNonRevDeals){
	$qry = "SELECT amount FROM `affiliates_deals` WHERE affiliate_id='".$set->userInfo['id']."' and dealType='revenue' and amount>0 and merchant_id in (". $merchantIDs . ")";
	$revrslt=mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
	if ($revrslt['amount']>0) {
		$showRev = 1;
	}else{
		$showRev = 0;
	}
}else{
	$showRev = 1;
}


$showFtdAmount = 0;
*/




$showRev = 0;
$showFtdAmount = 0;
$merchantsArr2 = Array();

for($i=0;$i<count($merchantsArr);$i++){
	
	$merID = $merchantsArr[$i];
	$merchantsArr2[$merID] = new stdClass();
	
	if ($set->hideNetRevenueForNonRevDeals){
		$qry = "SELECT amount FROM `affiliates_deals` WHERE affiliate_id='".$set->$requestID."' and dealType='revenue' and amount>0 and merchant_id=".$merID;
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

switch ($act) {
	case "payment_save":
		if ($paymentMethod == "bank") {
				if (!$bankdb['pay_firstname']) $errors['pay_firstname'] = 1;
				if (!$bankdb['pay_lastname']) $errors['pay_lastname'] = 1;
				if (!$bankdb['pay_address1']) $errors['pay_address1'] = 1;
				// if (!$bankdb['pay_address2']) $errors['pay_address2'] = 1;
				if (!$bankdb['pay_city']) $errors['pay_city'] = 1;
				// if (!$bankdb['pay_zip']) $errors['pay_zip'] = 1;
				if (!$bankdb['pay_country']) $errors['pay_country'] = 1;
				if (!$bankdb['pay_email']) $errors['pay_email'] = 1;
				if (!$bankdb['pay_account']) $errors['pay_account'] = 1;
			
				if (!$bankdb['pay_bank']) $errors['pay_bank'] = 1;
				if (!$bankdb['pay_branch']) $errors['pay_branch'] = 1;
				if (!$bankdb['pay_swift']) $errors['pay_swift'] = 1;
				// if (!$bankdb['account_number']) $errors['account_number'] = 1;
				// if (!$bankdb['account_name']) $errors['account_name'] = 1;
				if (!$bankdb['preferredCurrency']) $errors['preferredCurrency'] = 1;
				if (!$bankdb['pay_iban']) $errors['pay_iban'] = 1;
				$db = $bankdb;
			} else if ($paymentMethod == "paypal") {
				if (!$paypaldb['pay_firstname']) $errors['pay_firstname'] = 1;
				if (!$paypaldb['pay_lastname']) $errors['pay_lastname'] = 1;
				if (!$paypaldb['pay_address1']) $errors['pay_address1'] = 1;
				// if (!$paypaldb['pay_address2']) $errors['pay_address2'] = 1;
				if (!$paypaldb['pay_city']) $errors['pay_city'] = 1;
				// if (!$paypaldb['pay_zip']) $errors['pay_zip'] = 1;
				if (!$paypaldb['pay_country']) $errors['pay_country'] = 1;
				// if (!$paypaldb['pay_email']) $errors['pay_email'] = 1;
				if (!$paypaldb['preferredCurrency']) $errors['preferredCurrency'] = 1;
				if (!$paypaldb['pay_account']) $errors['pay_account'] = 1;
				$db = $paypaldb;
				} else if ($paymentMethod == "webmoney") {
				if (!$webmoneyldb['pay_firstname']) $errors['pay_firstname'] = 1;
				if (!$webmoneyldb['pay_lastname']) $errors['pay_lastname'] = 1;
				if (!$webmoneyldb['pay_address1']) $errors['pay_address1'] = 1;
				// if (!$webmoneyldb['pay_address2']) $errors['pay_address2'] = 1;
				if (!$webmoneyldb['pay_city']) $errors['pay_city'] = 1;
				// if (!$webmoneyldb['pay_zip']) $errors['pay_zip'] = 1;
				if (!$webmoneyldb['pay_country']) $errors['pay_country'] = 1;
				 if (!$webmoneyldb['preferredCurrency']) $errors['preferredCurrency'] = 1;
				 if (!$webmoneyldb['pay_email']) $errors['pay_email'] = 1;
				//if (!$webmoneyldb['pay_account']) $errors['pay_account'] = 1;
				$db = $webmoneyldb;
				} else if ($paymentMethod == "neteller") {
				if (!$Netellerdb['pay_firstname']) $errors['pay_firstname'] = 1;
				if (!$Netellerdb['pay_lastname']) $errors['pay_lastname'] = 1;
				if (!$Netellerdb['pay_address1']) $errors['pay_address1'] = 1;
				// if (!$Netellerdb['pay_address2']) $errors['pay_address2'] = 1;
				if (!$Netellerdb['pay_city']) $errors['pay_city'] = 1;
				// if (!$Netellerdb['pay_zip']) $errors['pay_zip'] = 1;
				if (!$Netellerdb['pay_country']) $errors['pay_country'] = 1;
				 if (!$Netellerdb['pay_email']) $errors['pay_email'] = 1;
				 if (!$Netellerdb['preferredCurrency']) $errors['preferredCurrency'] = 1;
				 if (!$Netellerdb['account_number']) $errors['account_number'] = 1;
				 
				 
				//if (!$Netellerdb['pay_account']) $errors['pay_account'] = 1;
				$db = $Netellerdb;
			} else if ($paymentMethod == "skrill") {
				if (!$Skrilldb['pay_firstname']) $errors['pay_firstname'] = 1;
				if (!$Skrilldb['pay_lastname']) $errors['pay_lastname'] = 1;
				if (!$Skrilldb['pay_address1']) $errors['pay_address1'] = 1;
				// if (!$Skrilldb['pay_address2']) $errors['pay_address2'] = 1;
				if (!$Skrilldb['pay_city']) $errors['pay_city'] = 1;
				// if (!$Skrilldb['pay_zip']) $errors['pay_zip'] = 1;
				if (!$Skrilldb['pay_country']) $errors['pay_country'] = 1;
				if (!$Skrilldb['pay_email']) $errors['pay_email'] = 1;
				if (!$Skrilldb['preferredCurrency']) $errors['preferredCurrency'] = 1;
				// if (!$Skrilldb['pay_account']) $errors['pay_account'] = 1;
				$db = $Skrilldb;
				
			} else if (strtolower($paymentMethod) == "chinaunionpay") {
				if (!$Chinaunionpaydb['pay_firstname']) $errors['pay_firstname'] = 1;
				if (!$Chinaunionpaydb['pay_lastname']) $errors['pay_lastname'] = 1;
				if (!$Chinaunionpaydb['pay_address1']) $errors['pay_address1'] = 1;
				// if (!$Skrilldb['pay_address2']) $errors['pay_address2'] = 1;
				if (!$Chinaunionpaydb['pay_city']) $errors['pay_city'] = 1;
				// if (!$Skrilldb['pay_zip']) $errors['pay_zip'] = 1;
				if (!$Chinaunionpaydb['pay_country']) $errors['pay_country'] = 1;
				if (!$Chinaunionpaydb['pay_email']) $errors['pay_email'] = 1;
				if (!$Chinaunionpaydb['preferredCurrency']) $errors['preferredCurrency'] = 1;
				// if (!$Skrilldb['pay_account']) $errors['pay_account'] = 1;
				$db = $Chinaunionpaydb;
				}
				
				
		if ($errors) {
		
			//	var_dump($errors);
				//die ('error');
			} else {
			unset($db['username']);
			$db['id'] = $set->userInfo['id'];
			$db['ip'] = $set->userIP;  
			
			$db['paymentMethod'] = $paymentMethod;
			
			$lastID=dbAdd($db,"affiliates");
			
			_goto($set->SSLprefix.$set->basepage.'?act=payment&ty=1');
			}
	
	case "payment":
		$set->pageTitle = lang('Payment Method Details');
		$db=dbGet($set->userInfo['id'],"affiliates");
		if ($db['id'] != $set->userInfo['id']) _goto($set->SSLprefix.'affiliate/');
		$paymentmethodparam = empty($db['paymentMethod']) ? $_POST['paymentMethod'] : $db['paymentMethod'];
		if ($act != "payment_save") $paymentMethod = $db['paymentMethod'];
		$set->content .= '<form action="'.$set->SSLprefix.$set->basepage.'?act=payment_save&id=' . $set->userInfo['id'] . '" method="post">
						<table width="980" border="0" cellpadding="0" cellspacing="0">
							'.($ty ? '<tr><td align="left" style="color: green; font-size: 14px;"><img border="0" width="30" src="'.$set->SSLprefix.'images/alert/alert_v.png" alt="" align="absmiddle" /> '.lang('Your payment method details has updated').'!</td></tr><tr height="30"></td></tr>' : '').'
							<tr>
								<td align="left">'. 
								(!empty($paymentmethodparam) ? '<b>'.lang('Payment method selected').': </b><span style="color: green; font-size: 15px;"> ' . lang(ucwords($paymentmethodparam)). "</span><br><br>" : ""). 
								
									"<b>".lang('Click to change the primary payment method').':</b> <select name="paymentMethod" onchange="chooseTab(this.value);">
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
									  if (strpos($avl,'-chinaunionpay')>0 ) {} else { 
									 $options .= ('<option value="chinaunionpay" '.( $paymentmethodparam == "chinaunionpay" ? 'selected' : '').'>'.lang('China Union Pay').'</option>');
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
								<td align="left"><input type="text" name="bankdb[pay_firstname]" value="'.($bankdb['pay_firstname'] ? $bankdb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_bank').'>'.lang('Bank Name').':</td>
								<td align="right"><input type="text" name="bankdb[pay_bank]" value="'.($bankdb['pay_bank'] ? $bankdb['pay_bank'] : $db['pay_bank']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" name="bankdb[pay_lastname]" value="'.($bankdb['pay_lastname'] ? $bankdb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_account').'>'.lang('Bank Account').':</td>
								<td align="right"><input type="text" name="bankdb[pay_account]" value="'.($bankdb['pay_account'] ? $bankdb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" name="bankdb[pay_address1]" value="'.($bankdb['pay_address1'] ? $bankdb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_branch').'>'.lang('Bank Branch').':</td>
								<td align="right"><input type="text" name="bankdb[pay_branch]" value="'.($bankdb['pay_branch'] ? $bankdb['pay_branch'] : $db['pay_branch']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="bankdb[pay_address2]" value="'.($bankdb['pay_address2'] ? $bankdb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_email').'>'.lang('Bank Email').':</td>
								<td align="right"><input type="text" name="bankdb[pay_email]" value="'.($bankdb['pay_email'] ? $bankdb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" name="bankdb[pay_city]" value="'.($bankdb['pay_city'] ? $bankdb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_iban').'>'.lang('IBAN').':</td>
								<td align="right"><input type="text" name="bankdb[pay_iban]" value="'.($bankdb['pay_iban'] ? $bankdb['pay_iban'] : $db['pay_iban']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="bankdb[pay_zip]" value="'.($bankdb['pay_zip'] ? $bankdb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_swift').'>'.lang('Swift').':</td>
								<td align="right"><input type="text" name="bankdb[pay_swift]" value="'.($bankdb['pay_swift'] ? $bankdb['pay_swift'] : $db['pay_swift']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
							
								
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" valign="top" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left" valign="top"><select name="bankdb[pay_country]" style="width: 259px;">'.getCountry(($bankdb['pay_country'] ? $bankdb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
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
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save').'" /></td>
							</tr>
						</table>
						</div>
						<div id="paypal" style="display: '.($paymentMethod == "paypal" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" name="paypaldb[pay_firstname]" value="'.($paypaldb['pay_firstname'] ? $paypaldb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160" '.err('pay_account').'>'.lang('E-Mail Paypal Address').':</td>
								<td align="right"><input type="text" name="paypaldb[pay_account]" value="'.($paypaldb['pay_account'] ? $paypaldb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" name="paypaldb[pay_lastname]" value="'.($paypaldb['pay_lastname'] ? $paypaldb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="paypaldb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($paypaldb['pay_info'] ? $paypaldb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" name="paypaldb[pay_address1]" value="'.($paypaldb['pay_address1'] ? $paypaldb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
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
								<td align="left"><input type="text" name="paypaldb[pay_city]" value="'.($paypaldb['pay_city']  ? $paypaldb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
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
								<td align="left"><select name="paypaldb[pay_country]" style="width: 257px;">'.getCountry(($paypaldb['pay_country'] ? $paypaldb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
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
								<td align="left"><input type="text" name="Skrilldb[pay_firstname]" value="'.($Skrilldb['pay_firstname'] ? $Skrilldb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
								
								

								<td width="80"></td>
								<td align="left" width="160" '.err('pay_email').'>'.lang('Skrill E-mail Address').':</td>
								<td align="right"><input type="text" name="Skrilldb[pay_email]" value="'.($Skrilldb['pay_email'] ? $Skrilldb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" name="Skrilldb[pay_lastname]" value="'.($Skrilldb['pay_lastname'] ? $Skrilldb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="Skrilldb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($Skrilldb['pay_info'] ? $Skrilldb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" name="Skrilldb[pay_address1]" value="'.($Skrilldb['pay_address1'] ? $Skrilldb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="4" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="Skrilldb[pay_address2]" value="'.($Skrilldb['pay_address2'] ? $Skrilldb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" name="Skrilldb[pay_city]" value="'.($Skrilldb['pay_city'] ? $Skrilldb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160"></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="Skrilldb[pay_zip]" value="'.($Skrilldb['pay_zip'] ? $Skrilldb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select name="Skrilldb[preferredCurrency]"  style="width: 248px;">';
								
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
						
						
						
						
						
						
						
						
						<div id="chinaunionpay" style="display: '.($paymentMethod == "chinaunionpay" ? 'block' : 'none').';">
						<table width="980" id="chinaunionpay" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" name="Chinaunionpaydb[pay_firstname]" value="'.($Chinaunionpaydb['pay_firstname'] ? $Chinaunionpaydb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_bank').'>'.lang('China Union Pay Name').':</td>
								<td align="right"><input type="text" name="Chinaunionpaydb[pay_bank]" value="'.($Chinaunionpaydb['pay_bank'] ? $Chinaunionpaydb['pay_bank'] : $db['pay_bank']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" name="Chinaunionpaydb[pay_lastname]" value="'.($Chinaunionpaydb['pay_lastname'] ? $Chinaunionpaydb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_account').'>'.lang('China Union Pay Account').':</td>
								<td align="right"><input type="text" name="Chinaunionpaydb[pay_account]" value="'.($Chinaunionpaydb['pay_account'] ? $Chinaunionpaydb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" name="Chinaunionpaydb[pay_address1]" value="'.($Chinaunionpaydb['pay_address1'] ? $Chinaunionpaydb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_branch').'>'.lang('China Union Pay Branch').':</td>
								<td align="right"><input type="text" name="Chinaunionpaydb[pay_branch]" value="'.($Chinaunionpaydb['pay_branch'] ? $Chinaunionpaydb['pay_branch'] : $db['pay_branch']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="Chinaunionpaydb[pay_address2]" value="'.($Chinaunionpaydb['pay_address2'] ? $Chinaunionpaydb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_email').'>'.lang('Email').':</td>
								<td align="right"><input type="text" name="Chinaunionpaydb[pay_email]" value="'.($Chinaunionpaydb['pay_email'] ? $Chinaunionpaydb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" name="Chinaunionpaydb[pay_city]" value="'.($Chinaunionpaydb['pay_city'] ? $Chinaunionpaydb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_iban').'>'.lang('IBAN').':</td>
								<td align="right"><input type="text" name="Chinaunionpaydb[pay_iban]" value="'.($Chinaunionpaydb['pay_iban'] ? $Chinaunionpaydb['pay_iban'] : $db['pay_iban']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="Chinaunionpaydb[pay_zip]" value="'.($Chinaunionpaydb['pay_zip'] ? $Chinaunionpaydb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_swift').'>'.lang('Swift').':</td>
								<td align="right"><input type="text" name="Chinaunionpaydb[pay_swift]" value="'.($Chinaunionpaydb['pay_swift'] ? $Chinaunionpaydb['pay_swift'] : $db['pay_swift']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
							
								
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" valign="top" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left" valign="top"><select name="Chinaunionpaydb[pay_country]" style="width: 259px;">'.getCountry(($Chinaunionpaydb['pay_country'] ? $Chinaunionpaydb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('account_name').'>'.lang('Account Name').':</td>
								<td align="right"><input type="text" name="Chinaunionpaydb[account_name]" value="'.($Chinaunionpaydb['account_name'] ? $Chinaunionpaydb['account_name'] : $db['account_name']).'" style="width: 248px;" /> </td>
								
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="Chinaunionpaydb[pay_company]" value="'.($Chinaunionpaydb['pay_company'] ? $Chinaunionpaydb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('account_number').'>'.lang('Account Number').':</td>
								<td align="right"><input type="text" name="Chinaunionpaydb[account_number]" value="'.($Chinaunionpaydb['account_number'] ? $Chinaunionpaydb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td align="left" width="110" valign="top">'.lang('More Information').':</td>
								<td align="left"><textarea name="Chinaunionpaydb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($Chinaunionpaydb['pay_info'] ? $Chinaunionpaydb['pay_info'] : $db['pay_info']).'</textarea></td>
									<td width="80"></td>
									<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="right"><select name="Chinaunionpaydb[preferredCurrency]"  style="width: 248px;">';
								
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
								<td colspan="5" height="20" align="right"><input type="submit" value="'.lang('Save').'" /></td>
							</tr>
						</table>
						</div>
						
						
						
						<div id="neteller" style="display: '.($paymentMethod == "neteller" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" name="Netellerdb[pay_firstname]" value="'.($Netellerdb['pay_firstname'] ? $Netellerdb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160" '.err('pay_email').'>'.lang('Neteller E-mail Address').':</td>
								<td align="right"><input type="text" name="Netellerdb[pay_email]" value="'.($Netellerdb['pay_email'] ? $Netellerdb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" name="Netellerdb[pay_lastname]" value="'.($Netellerdb['pay_lastname'] ? $Netellerdb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="Netellerdb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($Netellerdb['pay_info'] ? $Netellerdb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" name="Netellerdb[pay_address1]" value="'.($Netellerdb['pay_address1'] ? $Netellerdb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
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
								<td align="left"><input type="text" name="Netellerdb[pay_city]" value="'.($Netellerdb['pay_city'] ? $Netellerdb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
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
								<td align="left"><select name="Netellerdb[pay_country]" style="width: 257px;">'.getCountry(($Netellerdb['pay_country'] ? $Netellerdb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
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
						
						
						
						<div id="webmoney" style="display: '.($paymentMethod == "webmoney" ? 'block' : 'none').';">
						<table width="980" id="bank" cellspacing="0" cellpadding="0">
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" name="webmoneyldb[pay_firstname]" value="'.($webmoneyldb['pay_firstname'] ? $webmoneyldb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160" '.err('pay_email').'>'.lang('Web Money Account ID').':</td>
								<td align="right"><input type="text" name="webmoneyldb[pay_email]" value="'.($webmoneyldb['pay_email'] ? $webmoneyldb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" name="webmoneyldb[pay_lastname]" value="'.($webmoneyldb['pay_lastname'] ? $webmoneyldb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="160">'.lang('More Information').':</td>
								<td align="right" rowspan="3"><textarea name="webmoneyldb[pay_info]" cols="26" rows="4" style="width: 257px;">'.($webmoneyldb['pay_info'] ? $webmoneyldb['pay_info'] : $db['pay_info']).'</textarea></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" name="webmoneyldb[pay_address1]" value="'.($webmoneyldb['pay_address1'] ? $webmoneyldb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
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
								<td align="left"><input type="text" name="webmoneyldb[pay_city]" value="'.($webmoneyldb['pay_city'] ? $webmoneyldb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
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
								<td align="left"><select name="webmoneyldb[pay_country]" style="width: 257px;">'.getCountry(($webmoneyldb['pay_country'] ? $webmoneyldb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
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
								} else if (value == "paypal") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'block\';
								gid(\'skrill\').style.display=\'none\';
								gid(\'webmoney\').style.display=\'none\';
								gid(\'weteller\').style.display=\'none\';
								gid(\'chinaunionpay\').style.display=\'none\';
								
								} else if (value == "skrill") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'none\';
								gid(\'skrill\').style.display=\'block\';
								gid(\'webmoney\').style.display=\'none\';
								gid(\'neteller\').style.display=\'none\';
								gid(\'chinaunionpay\').style.display=\'none\';
								}
								 else if (value == "neteller") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'none\';
								gid(\'skrill\').style.display=\'none\';
								gid(\'webmoney\').style.display=\'none\';
								gid(\'neteller\').style.display=\'block\';
								gid(\'chinaunionpay\').style.display=\'none\';
								}
								 else if (value == "webmoney") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'none\';
								gid(\'skrill\').style.display=\'none\';
								gid(\'webmoney\').style.display=\'block\';
								gid(\'neteller\').style.display=\'none\';
								gid(\'chinaunionpay\').style.display=\'none\';
								}
									 else if (value == "chinaunionpay") {
								gid(\'bank\').style.display=\'none\';
								gid(\'paypal\').style.display=\'none\';
								gid(\'skrill\').style.display=\'none\';
								gid(\'chinaunionpay\').style.display=\'block\';
								gid(\'webmoney\').style.display=\'none\';
								gid(\'neteller\').style.display=\'none\';
								}
							}
					</script>';
		
		theme();
		break;
	
	case "save":
		if ($errors) {
			} else {
			unset($db['username']);
			$db['id'] = $set->userInfo['id'];
			$db['ip'] = $set->userIP;
			if ($password) $db['password'] = md5($password);
			$db['udate'] = dbDate();
			$db['valid'] = 1;
			if ($newsletter) $db['newsletter'] = 1; else $db['newsletter'] = 0;
			// Strip Tags
			$db['first_name'] = strip_tags($db['first_name']);
			$db['last_name'] = strip_tags($db['last_name']);
			$db['phone'] = strip_tags($db['phone']);
			$db['country'] = strip_tags($db['country']);
			$db['website'] = strip_tags($db['website']);
			$db['mail'] = strip_tags($db['mail']);
			// Strip Tags
			$lastID=dbAdd($db,"affiliates");
			_goto($set->SSLprefix.$set->basepage.'?ty=1');
			}
	
	default:
		$set->pageTitle = lang('Account Details');
		$db=dbGet($set->userInfo['id'],"affiliates");
		if ($db['id'] != $set->userInfo['id']) _goto('/affiliate/');
		$arr = Array("1" => "Africa",
			"2" => "Afro Eurasia",
			"3" => "Americas",
			"4" => "Asia",
			"5" => "Australia",
			"6" => "Eurasia",
			"7" => "Europe",
			"8" => "North America",
			"9" => "South America",
			"10" => "United Kingdom",
			"11" => "World Wide");
		$expMarketInfo=explode(",",$db['marketInfo']);
		for ($i=1; $i<=11; $i++) {
			if (@in_array($i,$expMarketInfo)) $selectedItems .= '<option value="'.$i.'">'.$arr[$i].'</option>';
				else $unselectedItems .= '<option value="'.$i.'">'.$arr[$i].'</option>';
			}
		$set->content .= '
		<div align="center">
		<form action="'.$set->SSLprefix.$set->basepage.'?act=save" method="post" onsubmit="return checkUpdate();" autocomplete="off">
		<table width="975" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" height="10"></td>
			</tr><tr>
				<td width="325" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('ACCOUNT INFORMATION').'</td>
						</tr><tr>
							<td height="225" valign="top" style="background: url(images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Username').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" value="'.$db['username'].'" disabled="disabled" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Password').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="password" name="password" id="password" value="" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Repeat Password').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="password" name="repassword" id="repassword" value="" style="width: 283px;" /></td>
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr><tr>
							<td height="10"></td>
						</tr><tr>
							<td align="left" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('WEBSITE INFORMATION').'</td>
						</tr><tr>
							<td height="195" valign="top" style="background: url(images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Website').' 1:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website]" id="website" value="'.($db['website'] ? $db['website'] : 'http://').'" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Website').' 2:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website2]" value="'.($db['website2'] ? $db['website2'] : 'http://').'" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Website').' 3:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website3]" value="'.($db['website3'] ? $db['website3'] : 'http://').'" style="width: 283px;" /></td>
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td>
				<td width="325" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('INVOICE INFORMATION').'</td>
						</tr><tr>
							<td height="226" valign="top" style="background: url(images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Street').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[street]" value="'.$db['street'].'" id="street" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Postal / Zip Code').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[postalCode]" value="'.$db['postalCode'].'" id="postalCode" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('City').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[city]" value="'.$db['city'].'" id="city" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Country').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><select name="db[country]" style="width: 295px;" id="country"><option value="">'.lang('Choose Your Country...').'</option>'.getCountry($db['country']).'</select></td>
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr><tr>
							<td height="10"></td>
						</tr><tr>
							<td align="left" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('MARKET INFORMATION').'</td>
						</tr><tr>
							<td height="184" valign="top" style="background: url(images/reg/reg_box_bg.jpg); padding-left: 10px; padding-top: 10px;">
								<table><tr>
									<td>
										<select id="q1" size="10" multiple="true" style="width: 130px; height: 150px; overflow: auto; border: 1px #CECECE solid;">'.$unselectedItems.'</select>
									</td>
									<td>
										<table border="0" cellpadding="0" cellspacing="0">
										<tr><td><img border="0" src="'.$set->SSLprefix.'images/reg/right.jpg" alt="" onclick="moveMultiple(\'q1\',\'q2\',\'q2\',\'update\'); return false;" style="cursor: pointer;" /></td></tr>
										<tr><td height="3"></td></tr>
										<tr><td><img border="0" src="'.$set->SSLprefix.'images/reg/left.jpg" alt="" onclick="moveMultiple(\'q2\',\'q1\',\'q2\',\'update\'); return false;" style="cursor: pointer;" /></td></tr>
										</table>
									</td>
									<td><select id="q2" multiple="true" style="width: 130px; height: 150px; overflow: auto; border: 1px #CECECE solid;">'.$selectedItems.'</select></td>
								</tr></table>
								<input type="hidden" name="db[marketInfo]" id="update" value="'.$db['marketInfo'].'" />
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td>
				<td width="325" rowspan="1" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" height="38" style="background: url(images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('CONTACT INFORMATION').'</td>
						</tr><tr>
							<td height="480" valign="top" style="background: url(images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Company Name').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[company]" value="'.$db['company'].'" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Salutation').':</td>
										<td align="right" style="padding-right: 20px;"><input type="radio" name="db[gender]" value="male" '.($db['gender'] == "male" || !$db['gender'] ? 'checked="checked"' : '').' /> '.lang('Mr').'. <input type="radio" name="db[gender]" value="female" '.($db['gender'] == "female" ? 'checked="checked"' : '').' /> '.lang('Ms').'.</td>
									</tr><tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('First Name').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[first_name]" id="first_name" value="'.$db['first_name'].'" style="width: 283px;" /></td>
									</tr><tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Last name').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[last_name]" id="last_name" value="'.$db['last_name'].'" style="width: 283px;" /></td>
									</tr><tr>';
										if ($_SESSION['isam']==0) {
										$set->content .= '
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('E-mail').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[mail]" value="'.$db['mail'].'" id="mail" style="width: 283px;" /></td>
									</tr><tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Phone number').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[phone]" value="'.$db['phone'].'" id="phone" style="width: 283px;" /></td>
									</tr><tr>
									
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('I.M. type').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;">
											<select name="db[IMUserType]" style="width: 295px;">
												<option value="">'.lang('Choose I.M. Type').'</option>
												<option value="Skype" '.($db['IMUserType'] == "Skype" ? 'selected="selected"' : '').'>'.lang('Skype').'</option>
												<option value="MSN" '.($db['IMUserType'] == "MSN" ? 'selected="selected"' : '').'>'.lang('MSN').'</option>
												<option value="Google Talk" '.($db['IMUserType'] == "Google Talk" ? 'selected="selected"' : '').'>'.lang('Google Talk').'</option>
												<option value="QQ" '.($db['IMUserType'] == "QQ" ? 'selected="selected"' : '').'>'.lang('QQ').'</option>
												<option value="ICQ" '.($db['IMUserType'] == "ICQ" ? 'selected="selected"' : '').'>'.lang('ICQ').'</option>
												<option value="Yahoo" '.($db['IMUserType'] == "Yahoo" ? 'selected="selected"' : '').'>'.lang('Yahoo').'</option>
												<option value="AIM" '.($db['IMUserType'] == "AIM" ? 'selected="selected"' : '').'>'.lang('AIM').'</option>
											</select>
										</td>
									</tr><tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('I.M. account').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[IMUser]" value="'.$db['IMUser'].'" style="width: 283px;" /></td>
									</tr>';
									}
									$set->content .= '
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url(images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td>
			</tr><tr>
				<td colspan="3" height="10"></td>
			</tr><tr>
				<td colspan="2" align="left">
					<input type="checkbox" name="newsletter" '.($db['newsletter'] ? 'checked="checked"' : '').' /> '.lang('Yes, I would like to receive the Affiliate newsletter').'
				</td>
				<td align="right" style="padding-right: 10px;">
					<input type="submit" value="'.lang('Save').'" />
				</td>
			</tr>
		</table>
		</form>
		</div>';
		
		theme();
		break;
	
	
	case "commission":
		$set->pageTitle = lang('Your Commission Structure');
		
		$l=0;
		$merchantqq=function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY id ASC",__FILE__,__FUNCTION__);
		while ($merchantww=mysql_fetch_assoc($merchantqq)) {
			// die('frr');
			if (!chkMerchant($merchantww['id'])) continue;
			unset($min_cpaAmount); unset($cpaAmount); unset($revenueAmount); unset($cplAmount); unset($cpcAmount);
			
			$sql = "SELECT * FROM affiliates_deals WHERE merchant_id='".$merchantww['id']."' AND affiliate_id='".$set->userInfo['id']."'  AND dealType='tier'";
			
			$takeqq=function_mysql_query($sql,__FILE__,__FUNCTION__);
			if (mysql_num_rows($takeqq) > 0) {
				while ($takeww=mysql_fetch_assoc($takeqq)) {
					$tierDealType .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td align="left"><b>'.$merchantww['name'].'</b></td>
							<td>'. $set->currency .' '.$takeww['tier_amount'].'</td>
							<td>'. $set->currency . ' '.number_format($takeww['amount'],2).'</td>
							<td>% '.number_format($takeww['tier_pcpa'],2).'</td>
						</tr>';
						$l++;
					}
					
				} else {
				
				$dealTypes = array();
				$row = array();
				$row['rdate'] =  date('Y-m-d');
				$row['affiliate_id'] = $set->userInfo['id'];
				$row['merchant_id'] = $merchantww['id'];
				
				$dealTypes = extractDealTypes($row, getMerchantDealTypeDefaults());
				if ($_GET['qa']) {
					var_dump($dealTypes);
					die();
				}
				$min_cpaAmount = $dealTypes['min_cpa']['amount'];
				$cpaAmount = $dealTypes['cpa']['amount'];
				$dcpaAmount = $dealTypes['dcpa']['amount'];
				$revenueAmount = $dealTypes['revenue']['amount'];
				$cplAmount = $dealTypes['cpl']['amount'];
				$cpcAmount = $dealTypes['cpc']['amount'];
				$cpmAmount = $dealTypes['cpm']['amount'];
				$revenueSpreadAmount = $dealTypes['revenue_spread']['amount'];
				$positions_rev_shareAmount = $dealTypes['positions_rev_share']['amount'];
				
				/*
				$takeqq=function_mysql_query("SELECT * FROM affiliates_deals WHERE merchant_id='".$merchantww['id']."' AND affiliate_id='".$set->userInfo['id']."' AND dealType!='tier'",__FILE__);
				while ($takeww=mysql_fetch_assoc($takeqq)) {
					if ($takeww['dealType'] == "min_cpa") $min_cpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpa") $cpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "dcpa") $dcpaAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "revenue") $revenueAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpl") $cplAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpc") $cpcAmount = $takeww['amount'];
						else if ($takeww['dealType'] == "cpm") $cpmAmount = $takeww['amount'];
					}
					if (!$min_cpaAmount) $min_cpaAmount = $merchantww['min_cpa_amount'];
					if (!$cpaAmount) $cpaAmount = $merchantww['cpa_amount'];
					if (!$dcpaAmount) $dcpaAmount = $merchantww['dcpa_amount'];
					if ($revenueAmount=="") $revenueAmount = $merchantww['revenue_amount'];
					if (!$cplAmount) $cplAmount = $merchantww['cpl_amount'];
					if (!$cpcAmount) $cpcAmount = $merchantww['cpc_amount'];
					if (!$cpmAmount) $cpmAmount = $merchantww['cpm_amount'];
				*/
					$listDealType .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td align="left"><b>'.$merchantww['name'].'</b></td>'.
							($set->showMiminumDepositOnAffAccount ==1 ?  '<td>'. $set->currency .' '.number_format($min_cpaAmount,0).'</td>' : '') .'
							'.($merchantsArr2[$merchantww['id']]->showFtdAmount ? '<td>'. $set->currency .' '.number_format($cpaAmount,0).'</td>' : ($showFtdAmount ? '<td></td>' : '<td></td>')).'
							'.($set->showDCPAonAffiliateComStruc ==1 ? '<td>% '.number_format($dcpaAmount,0).'</td>' : '').'
							'.($merchantsArr2[$merchantww['id']]->showRev ? '<td>% '.number_format($revenueAmount,0).'</td>' : ($showRev ? '<td></td>' : '<td></td>')).'
							'.($set->showPositionsRevShareDeal ? '<td>% '.number_format($positions_rev_shareAmount, 2).'</td>' : '').'
							'.($set->deal_cpl ? '<td>'. $set->currency .' '.number_format($cplAmount,0).'</td>' : '').'
							'.($set->deal_cpc ? '<td>'. $set->currency .' '.number_format($cpcAmount,0).'</td>' : '').'
							'.($set->deal_cpm ? '<td>'. $set->currency .' '.number_format($cpmAmount,0).'</td>' : '').'
						</tr>';
					$l++;
				}
			}
			
			$set->content .= '<div class="normalTableTitle">'.lang('Your Account Commission Structure').'</div>
						<div align="center">
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td style="text-align: left;">'.lang('Merchant').'</td>
									'.($tierDealType ? '
									<td>'.lang('Deposit Range').'</td>
									<td>'.lang('CPA').'</td>
									<td>'.lang('PCPA').'</td>
									' : ($set->showMiminumDepositOnAffAccount ==1 ? '<td>'.lang(ptitle('Minimum Deposit')).'</td>' : '').'
									'.($showFtdAmount ? '<td>'.lang('CPA').'</td>' : '<td></td>').'
									'.($set->showDCPAonAffiliateComStruc==1 ? '<td>'.lang('DCPA').'</td>' : '').'
									'.($showRev ? '<td>'.lang('Revenue').'</td>' : '<td></td>').'
									'.($set->showPositionsRevShareDeal ? '<td>'.lang('Positions Rev. Share').'</td>' : '').'
									'.($set->deal_cpl ? '<td>'.lang('CPL').'</td>' : '').'
									'.($set->deal_cpc ? '<td>'.lang('CPC').'</td>' : '').'
									'.($set->deal_cpm ? '<td>'.lang('CPM').'</td>' : '')).'
								</tr></thead><tfoot>'.($tierDealType ? $tierDealType : $listDealType).'</tfoot>
							</table>
						</div>';
			theme();
		break;
	
	}

