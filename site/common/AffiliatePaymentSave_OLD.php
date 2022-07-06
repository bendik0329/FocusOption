<?php



if (empty($paymentMethod)) {
	$paymentMethod = $_POST['paymentMethod'];
 // die(print_r($_POST, true));
	
}

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
				
				
                $arrPath = explode('/', $_SERVER['PHP_SELF']);
                $boolIsAffiliate = 'affiliate' == $arrPath[1];
                                
                                
		/*if ($errors) {
                    if ($boolIsAffiliate) {
                        _goto($set->basepage . '?act=payment&ty=1');
                    } else {
                        _goto($set->basepage.'?act=new&id=' . $_GET['id']);
                    }
                    
                } else {*/
                    
                    if ($boolIsAffiliate) {
                        unset($db['username']);
			$db['id'] = $set->userInfo['id'];
			$db['ip'] = $set->userIP;  
			$db['paymentMethod'] = $paymentMethod;
			$lastID = dbAdd($db, 'affiliates');
			// die ('$lastID: ' . $lastID);
			_goto($set->basepage . '?act=payment&ty=1&toggleTo=payment_details');
                        
                    } else {
                        unset($db['username']);
                        $db['id'] = $_GET['id'];
                        $db['ip'] = $_SERVER['REMOTE_ADDR'];
                        $db['paymentMethod'] = $paymentMethod;
                        $lastID = dbAdd($db, 'affiliates');
                        _goto($set->basepage.'?act=new&id=' . $_GET['id'] . "&toggleTo=payment_details");
                    }
                //}
                