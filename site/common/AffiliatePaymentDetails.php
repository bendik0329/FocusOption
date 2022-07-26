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
		
		$arrayOptions = array('bank','chinaunionpay','epayments','qiwi');
		$paymentMethodStyle = in_array($paymentmethodparam,$arrayOptions) ? 'full':'basic';
		

		if ($act != "payment_save") $paymentMethod = $db['paymentMethod'];
		
		$set->content .= '<form id="frmPaymentDetails"  method="post">
							<input type="hidden" name="affiliate" value="'. $requestID .'">
						<table width="980" border="0" cellpadding="0" cellspacing="0" class="tblDetails">
							'.($ty ? '<tr><td align="left" style="color: green; font-size: 14px;"><img border="0" width="30" src="images/alert/alert_v.png" alt="" align="absmiddle" /> '.lang('Your payment method details has updated').'!</td></tr><tr height="30"></td></tr>' : '').'
							<tr>
								<td align="left">'. 
								(!empty($paymentmethodparam) ? '<div class="payment-header"><p class="payment-method">'.lang('Payment method selected ').': </p><span style="color: green; font-size: 15px;text-transform:capitalize;" class="methodSelected"> ' . lang(ucwords($paymentmethodparam)). "</span> </div>" : ""). 
								
									"<p>".lang('Click to change the primary payment method').':</p> <select class="select-payment" id="paymentMethod" name="paymentMethod" onchange="chooseTab(this.value);">
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
																
								$matches = explode('|',$set->availableCurrencies);
								$selectoptions="";
								foreach ($matches as $val) {
								if (strpos('*'. $val,'-')>0)
									continue;									
								$selectoptions .= '<option ' . ( $db['preferredCurrency']==$val || $bankdb['preferredCurrency']==$val ? ' selected ' : '' ) . '>'.$val.'</option>';
									
								}
								/* 
								<option ' . ( $db['preferredCurrency']=='GBP' || $bankdb['preferredCurrency']=='GBP' ? ' selected ' : '' ) . '>GBP</option>
								<option ' . ( $db['preferredCurrency']=='EUR' || $bankdb['preferredCurrency']=='EUR' ? ' selected ' : '' ) . '>EUR</option>
								<option ' . ( $db['preferredCurrency']=='AUD' || $bankdb['preferredCurrency']=='AUD' ? ' selected ' : '' ) . '>AUD</option>
								<option ' . ( $db['preferredCurrency']=='CNY' || $bankdb['preferredCurrency']=='CNY' ? ' selected ' : '' ) . '>CNY</option>
								 */
								
									// $set->content .= $options.'
									
									 $set->content .=
									
										'
									</select>
								</td>
							</tr><tr>
								<td height="20"></td>
							</tr>
						</table>
						<div id="basic">
						<table width="980" id="basic" cellspacing="0" cellpadding="0">
						<tr>
						<td><u>'.lang('Basic Information').'</u></td>
						</tr><tr>
								<td colspan="5" height="20"></td>
						</tr>
							<tr>
								<td align="left" width="110" '.err('pay_firstname').'>'.lang('First Name').':</td>
								<td align="left"><input type="text" id="pay_firstname" name="bankdb[pay_firstname]" value="'.($bankdb['pay_firstname'] ? $bankdb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_lastname').'>'.lang('Last Name').':</td>
								<td align="left"><input type="text" id="pay_lastname" name="bankdb[pay_lastname]" value="'.($bankdb['pay_lastname'] ? $bankdb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td align="left" width="110" '.err('pay_address1').'>'.lang('Address').' 1:</td>
								<td align="left"><input type="text" id="pay_address1" name="bankdb[pay_address1]" value="'.($bankdb['pay_address1'] ? $bankdb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> <span class="required">*</span></td>
								

								<td width="80"></td>
								
								<td align="left" width="110" '.err('pay_address2').'>'.lang('Address').' 2:</td>
								<td align="left"><input type="text" name="bankdb[pay_address2]" value="'.($bankdb['pay_address2'] ? $bankdb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" /></td>
								
								
								
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								
								<td align="left" width="110" '.err('pay_city').'>'.lang('City').':</td>
								<td align="left"><input type="text" id="pay_city" name="bankdb[pay_city]" value="'.($bankdb['pay_city'] ? $bankdb['pay_city'] : $db['pay_city']).'" style="width: 248px;" /> <span class="required">*</span></td>
								
								
								
								
								
								<td width="80"></td>
								
								<td align="left" width="110" '.err('pay_zip').'>'.lang('Zip Code').':</td>
								<td align="left"><input type="text" name="bankdb[pay_zip]" value="'.($bankdb['pay_zip'] ? $bankdb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" /></td>
								
								
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" valign="top" '.err('pay_country').'>'.lang('Country').':</td>
								<td align="left" valign="top"><select id="pay_country" name="bankdb[pay_country]" style="width: 259px;">'.getCountry(($bankdb['pay_country'] ? $bankdb['pay_country'] : $db['pay_country'])).'</select> <span class="required">*</span></td>
								<td width="80"></td>
							<td align="left" width="110" '.err('pay_company').'>'.lang('Company').':</td>
								<td align="left"><input type="text" name="bankdb[pay_company]" value="'.($bankdb['pay_company'] ? $bankdb['pay_company'] : $db['pay_company']).'" style="width: 248px;" /></td>
								
								
							</tr><tr>
								<td colspan="5" height="40"></td>
							</tr>
							</table>
							
						<table width="980" id="transfer1" cellspacing="0" cellpadding="0">
						<tr>
						<td><u>'.lang('Transfer Details').'</u></td>
						</tr><tr>
								<td colspan="5" height="20"></td>
							</tr>
							<tr>
								
									<td align="left" width="110" '.err('preferredCurrency').'>'.lang('Preferred Currency').':</td>
								<td align="left" style="width: 260px;"><select  style="width: 260px;" name="bankdb[preferredCurrency]"  >
								'.$selectoptions.'
								</select>
								</td>
								
								
									
								<td align="left" width="110" valign="top">'.lang('More Information').':</td>
								<td style="width: 248px;" align="left"><textarea name="bankdb[pay_info]" cols="26" rows="4" >'.($bankdb['pay_info'] ? $bankdb['pay_info'] : $db['pay_info']).'</textarea></td>

								
							</tr><tr>
								<td colspan="5" height="20"></td>
								
								</tr><tr>
								<td align="left" width="110" '.err('pay_email').'>'.lang('Payment Method').' ' .lang('Email').':</td>
								<td align="left"><input type="text" id="pay_email" name="bankdb[pay_email]" value="'.($bankdb['pay_email'] ? $bankdb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> <span class="required">*</span></td>

								
								<td width="80"></td>
								<td></td>
								
						</tr>
						</table>
						<table width="980" id="fullTransfer" cellspacing="0" cellpadding="0" style="display: '.($paymentMethodStyle == "full" ? 'block' : 'none').';">
						<tr>
							<tr>
								<td colspan="5" height="20"></td>
								
								</tr><tr>
								<td align="left" width="110" '.err('pay_account').'>'.lang('Payment Method').' ' .('Account').':</td>
								<td align="right"><input type="text" id="pay_account" name="bankdb[pay_account]" value="'.($bankdb['pay_account'] ? $bankdb['pay_account'] : $db['pay_account']).'" style="width: 248px;" /> <span class="required">*</span></td>
								
								<td width="80"></td>
								<td></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_bank').'>'.lang('Payment Method').' ' .lang('Name').':</td>
								<td align="right"><input type="text" id="pay_bank" name="bankdb[pay_bank]" value="'.($bankdb['pay_bank'] ? $bankdb['pay_bank'] : $db['pay_bank']).'" style="width: 248px;" /> <span class="required">*</span></td>								

								
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_iban').'>'.lang('IBAN').':</td>
								<td align="right"><input type="text" id="pay_iban" name="bankdb[pay_iban]" value="'.($bankdb['pay_iban'] ? $bankdb['pay_iban'] : $db['pay_iban']).'" style="width: 248px;" /> <span class="required">*</span></td>
							</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('pay_branch').'>'.lang('Payment Method').' ' .lang('Branch').':</td>
								<td align="right"><input type="text" id="pay_branch" name="bankdb[pay_branch]" value="'.($bankdb['pay_branch'] ? $bankdb['pay_branch'] : $db['pay_branch']).'" style="width: 248px;" /> <span class="required">*</span></td>
								<td width="80"></td>
								<td align="left" width="110" '.err('pay_swift').'>'.lang('Swift').':</td>
								<td align="right"><input type="text" id="pay_swift" name="bankdb[pay_swift]" value="'.($bankdb['pay_swift'] ? $bankdb['pay_swift'] : $db['pay_swift']).'" style="width: 248px;" /> <span class="required">*</span></td>
							
							
								</tr><tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td align="left" width="110" '.err('account_name').'>'.lang('Account Name').':</td>
								<td align="right"><input type="text" name="bankdb[account_name]" value="'.($bankdb['account_name'] ? $bankdb['account_name'] : $db['account_name']).'" style="width: 248px;" /> </td>
									<td width="80"></td>
									<td align="left" width="110" '.err('account_number').'>'.lang('Account Number').':</td>
								<td align="right"><input type="text" name="bankdb[account_number]" value="'.($bankdb['account_number'] ? $bankdb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
								</tr>
								</table>
								



								<!--basic transfer-->
								<table width="980" id="basicTransfer" cellspacing="0" cellpadding="0" style="display: '.($paymentMethodStyle == "basic" ? 'block' : 'none').';">
					<tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
							

									<td align="left" width="110" '.err('account_number').'>'.lang('Account ID').':</td>
								<td align="right"><input type="text" name="bankdb[account_number]" value="'.($bankdb['account_number'] ? $bankdb['account_number'] : $db['account_number']).'" style="width: 248px;" /> </td>
									<td width="80"></td>
								<td></td>
								<td></td>
							
								</tr>
								</table>
								
								<!--end basic transfer-->
								
								<table width="980" id="save" cellspacing="0" cellpadding="0">
								<tr>
								<td colspan="5" height="20"></td>
							</tr><tr>
								<td colspan="5" class="save-details-button"><input type="submit" value="'.lang('Save Details').'" /></td>
							</tr>
						</table>
						</div>
							
						<div class="payment-page">
								<div class="row">
									<div class="col-lg-6 col-md-6 col-sm-12 col-12">
										<div class="basic-im">
											<div class="basic-heading">
												<h3>'.lang('Basic Information').'</h3>
											</div>
											<div class="basic-section ac-details-tab">
												<div class="basic-section-details">
													<h4'.err('pay_firstname').'>'.lang('First Name').':</h4>
													<input type="text" id="pay_firstname" name="bankdb[pay_firstname]" value="'.($bankdb['pay_firstname'] ? $bankdb['pay_firstname'] : $db['pay_firstname']).'" style="width: 248px;" /> 
												</div>
												<div class="basic-section-details">
													<h4 '.err('pay_lastname').'>'.lang('Last Name').':</h4>
													<input type="text" id="pay_lastname" name="bankdb[pay_lastname]" value="'.($bankdb['pay_lastname'] ? $bankdb['pay_lastname'] : $db['pay_lastname']).'" style="width: 248px;" /> 
												</div>
												<div class="basic-section-details">
													<h4>Email</h4>
													<input type="text" id="" name="bankdb[]" value="'.($bankdb[''] ? $bankdb[''] : $db['']).'" style="width: 248px;" /> 
												</div>
												<div class="basic-section-details">
													<h4'.err('pay_address1').'>'.lang('Address').' 1:</h4>
													<input type="text" id="pay_address1" name="bankdb[pay_address1]" value="'.($bankdb['pay_address1'] ? $bankdb['pay_address1'] : $db['pay_address1']).'" style="width: 248px;" /> 
												</div>
												<div class="basic-section-details">
													<h4'.err('pay_address2').'>'.lang('Address').' 2:</h4>
													<input type="text" name="bankdb[pay_address2]" value="'.($bankdb['pay_address2'] ? $bankdb['pay_address2'] : $db['pay_address2']).'" style="width: 248px;" />
												</div>
												<div class="basic-section-details">
													<h4 '.err('pay_city').'>'.lang('City').':</h4>
													<input type="text" id="pay_city" name="bankdb[pay_city]" value="'.($bankdb['pay_city'] ? $bankdb['pay_city'] : $db['pay_city']).'" style="width: 248px;" />
												</div>
												<div class="basic-section-details">
													<h4'.err('pay_zip').'>'.lang('Zip Code').':</h4>
													<input type="text" name="bankdb[pay_zip]" value="'.($bankdb['pay_zip'] ? $bankdb['pay_zip'] : $db['pay_zip']).'" style="width: 248px;" />
												</div>
												<div class="basic-section-details">
													<h4'.err('pay_country').'>'.lang('Country').':</h4>
													<select id="pay_country" name="bankdb[pay_country]" style="width: 259px;">'.getCountry(($bankdb['pay_country'] ? $bankdb['pay_country'] : $db['pay_country'])).'</select> 
												</div>
												<div class="basic-section-details">
													<h4'.err('pay_company').'>'.lang('Company').':</h4>
													<input type="text" name="bankdb[pay_company]" value="'.($bankdb['pay_company'] ? $bankdb['pay_company'] : $db['pay_company']).'" style="width: 248px;" />
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-6 col-md-6 col-sm-12 col-12">
										<div class="basic-im">
											<div class="basic-heading">
												<h3>'.lang('Transfer Details').'</h3>
											</div>
											<div class="basic-section ac-details-tab">
												<div class="basic-section-details">
													<h4 '.err('preferredCurrency').'>'.lang('Preferred Currency').':</h4>
													<select  style="width: 260px;" name="bankdb[preferredCurrency]"  >
													'.$selectoptions.'
													</select>
												</div>
												<div class="basic-section-details">
													<h4>'.lang('More Information').':</h4>
													<textarea name="bankdb[pay_info]" cols="26" rows="4" >'.($bankdb['pay_info'] ? $bankdb['pay_info'] : $db['pay_info']).'</textarea>
												</div>
												<div class="basic-section-details">
												<h4'.err('pay_email').'>'.lang('Payment Method').' ' .lang('Email').':</h4>
												<input type="text" id="pay_email" name="bankdb[pay_email]" value="'.($bankdb['pay_email'] ? $bankdb['pay_email'] : $db['pay_email']).'" style="width: 248px;" /> 
												</div>
												<div class="basic-section-details">
												<h4 '.err('account_number').'>'.lang('Account ID').':</h4>
												<input type="text" name="bankdb[account_number]" value="'.($bankdb['account_number'] ? $bankdb['account_number'] : $db['account_number']).'" style="width: 248px;" />
												</div>
											</div>
										</div>
									</div>
									<div class="col-lg-12">
										<div class="save-button-p">
											<div colspan="5" class="save-details-button"><input type="submit" value="'.lang('Save Details').'" /></div>
										</div>
									</div>
								</div>
						</div>
						
						</form>
					<script type="text/javascript">
						function chooseTab(value) {
							
							// alert(value);
							
							if (value == "bank" || value== "chinaunionpay" || value =="qiwi" || value=="epayments") {
								gid(\'fullTransfer\').style.display=\'block\';
								gid(\'basicTransfer\').style.display=\'none\';
								} else  {
								gid(\'fullTransfer\').style.display=\'none\';
								gid(\'basicTransfer\').style.display=\'block\';
								} 
							}
							
							$(document).ready(function(){
								$("#frmPaymentDetails").on("submit",function(e){
									e.preventDefault();
									// alert ($("#paymentMethod").val());
									if($("#paymentMethod").val()=="bank" || $("#paymentMethod").val()=="chinaunionpay" || $("#paymentMethod").val()=="qiwi" || $("#paymentMethod").val()=="epayments"){
											var check_ids = new Array("pay_firstname","pay_lastname","pay_address1","pay_city","pay_country","pay_bank","pay_account","pay_branch","pay_email","pay_iban","pay_swift");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Bank Name').'","'.lang('Bank Account').'","'.lang('Bank Branch').'","'.lang('Email').'","'.lang('IBAN').'","'.lang('Swift').'");
											var email = $("#pay_email");
									}
									else{
											var check_ids = new Array("pay_firstname","pay_lastname","pay_address1","pay_city","pay_country","pay_email");
											var names = new Array("'.lang('First Name').'","'.lang('Last Name').'","'.lang('Address').'","'.lang('City').'","'.lang('Country').'","'.lang('Email').'");
											var email = $("#pay_email");
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
		
		
