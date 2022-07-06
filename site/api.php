<?php
require('common/global.php');
$resulta=mysql_query("SELECT id,username,password FROM affiliates WHERE id='".$account_id."' AND lower(username)='".strtolower($username)."' AND password='".md5($password)."' AND valid='1'");
$chk=mysql_fetch_assoc($resulta);

if ($chk['id']) {
	$Merchant = mysql_fetch_assoc(mysql_query("SELECT id,name FROM merchants WHERE lower(name)='".strtolower($merchant)."'"));
	if (!$Merchant['id']) {
		$xmlResponse .= '	<Error>No Merchant Found</Error>';
		} else {
		$Trader = mysql_fetch_assoc(mysql_query("SELECT * FROM data_reg WHERE '". $Merchant['id'] ."' and trader_id='".$trader_id."'"));
		if (!$Trader['id']) {
			$xmlResponse .= '	<Error>No Trader Found</Error>';
			} else {
			$TraderSales = mysql_fetch_assoc(mysql_query("SELECT * FROM data_sales WHERE '". $Merchant['id'] ."' and trader_id='".$trader_id."' ORDER BY id ASC"));
			$xmlResponse .= '<Trader>
		<TraderID>'.$Trader['trader_id'].'</TraderID>
		<Merchant>'.strtoupper($Merchant['name']).'</Merchant>
		<TraderAlias>'.$Trader['trader_alias'].'</TraderAlias>
		<RegistrationTime>'.$Trader['rdate'].'</RegistrationTime>
		<FTD>'.($TraderSales['id'] ? '1' : '0').'</FTD>
		<FTD_Date>'.$TraderSales['rdate'].'</FTD_Date>
		<FTD_Amount>'.$TraderSales['amount'].'</FTD_Amount>
		<FTD_Currency>USD</FTD_Currency>
	</Trader>';
			}
		}

	} else {
	
	$xmlResponse .= '<Error>Bad Username OR Password</Error>';
	
	}

print '<?xml version="1.0" encoding="UTF-8"?>
<Traders>
	'.$xmlResponse.'
</Traders>';
mysql_close();
die();
	
?>