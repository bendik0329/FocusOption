<?php

// Option-Fair Integration - By Hanan ;)

header("Pragma: no-cache");
header("Expires: 0");
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1

require_once('../common/database.php');
require_once('common/global.php');


//$getMarchant = mysql_fetch_assoc(mysql_query("SELECT * FROM sites WHERE id='9'"));
//if (!$getMarchant['valid']) die("Cron Off!");
//updateUnit("sites","cron_lastscan='".dbDate()."',running='1'","id='".$getMarchant['id']."'");


$defaultBtag = 'a500-b1-p';

function getMerchantByType($type){
	$mid = 0;
	switch($type){
		case 'casino': 					$mid=1; 	break;
		case 'sports': 					$mid=17; 	break;
		case 'sportsm': 				$mid=17; 	break;
		case 'games': 					$mid=18; 	break;
		case 'livecasino': 				$mid=1; 	break;
		case 'poker':	 				$mid=3; 	break;
		case 'bingo':	 				$mid=4; 	break;
		case 'winner-co-uk-casino':		$mid=19; 	break;
		case 'winner-co-uk-sports':		$mid=20; 	break;
		case 'winner-co-uk-games':		$mid=21; 	break;
		case '7regal':	 				$mid=22; 	break;
		case '24winner': 				$mid=23; 	break;
		default: 						$mid=0;		break;
	}
	
	return $mid;
}


//$getMarchant['cron_lastscan'] = '2014-07-24 18:01:57';
//die($getMarchant['cron_lastscan'] .' = '.date("dmYH00", strtotime("+1 hour")));

if ($_GET['m_date']) {
	$exp_mdate=explode("-",$_GET['m_date']);
}else{
	$exp_mdate=explode("-",date("Y-m-d"));
}
//$scanDate = date("Y_m_d", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));
//$fromscanDate = date("Y_m_d", strtotime("+2 Days",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));
if(isset($_GET['monthly']) AND $_GET['monthly']){
	$dateFormat = "Y_m";
}else{
	$dateFormat = "Y_m_d";
}

$scanDateNOTIME = date($dateFormat, mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));

if(isset($_GET['monthly']) AND $_GET['monthly']){
	//$scanDateNOTIME = ".*.".$scanDateNOTIME;
	//echo '~^registrations_'.$scanDateNOTIME.'.*\.(csv)$~';
}

//die('~^MR_141591_'.$scanDateNOTIME.'.*\.(csv)$~');
$scanDate = date("dmYH00", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));
$fromscanDate = date("dmYH00", strtotime("+2 Days",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));
$reg_urls = preg_grep('~^MR_141551_'.$scanDateNOTIME.'.*\.(csv)$~', scandir('files/'));
$sales_urls = preg_grep('~^MR_141611_'.$scanDateNOTIME.'.*\.(csv)$~', scandir('files/'));
$sales2_urls = preg_grep('~^MR_141621_'.$scanDateNOTIME.'.*\.(csv)$~', scandir('files/'));
$stats_urls = preg_grep('~^MR_141591_'.$scanDateNOTIME.'.*\.(csv)$~', scandir('files/'));
$stats2_urls = preg_grep('~^MR_141571_'.$scanDateNOTIME.'.*\.(csv)$~', scandir('files/'));

setLog('Manually scanning for '.$_GET['m_date'],9,'yellow');
/*
} else {
	//if ($getMarchant['cron_lastscan'] >= date("Y-m-d H:00") OR date("G") < '3') 
	//	die("Not yet...");
	//updateUnit("sites","cron_lastscan='".date("Y-m-d H:00")."'","id='".$getMarchant['id']."'");
	$scanDate = date("dmYH00");
	$fromscanDate = date("dmYH00", strtotime("-1 day"));
	
	setLog('Daily scanning',9,'green');
}
*/


function checkCurrencies(){
	global $set;
	$currentTime = new DateTime();
	$startTime = new DateTime('01:00');
	$endTime = new DateTime('04:00');
	if (($currentTime >= $startTime && $currentTime <= $endTime) OR ($_REQUEST['currency'])) {
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://partners.rightcommission.com/getCurrency.php"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch); 
		echo $output;
	}else{
		echo '<BR>Currencies will be update in other time.<BR>';
	}
}
checkCurrencies();

	
echo '<style type="text/css">body,html { font-family: Tahoma; font-size: 11px; } </style>';


######################### [ Accounts ] #########################


function goReg($url){
	//echo 'goReg is not active...<BR>';
	//return;
	global $scanDate,$defaultBtag; 
	//$url = 'ftp://dev@winneroptions.affiliatebuddies.com:afdev@184.107.206.210/efi/registrations_'.$scanDate.'.csv';//.$scanDate.'.xml';
	//$url = 'http://184.107.206.210/~winneroptionsaff/efi/registrations_'.$scanDate.'.csv';
	if (!file_exists($url)) die('Reg Not Exist: '.$url);
	//die($url);
	$xml_report = file_get_contents($url);// or die("Feed not working");
	//var_dump($xml_report);
	if(!$xml_report){
		echo "Feed not working<BR>";
		return;
	}
	//$xml_report = file_get_contents($url) or die("Feed not working");
	$xml_report = rtrim($xml_report,"\n");
	$xml = explode("\n",$xml_report);

	//$find = Array("\n","\t"," ");
	//$replace = Array("","","");
	//$xml_report = str_replace($find,$replace,$xml_report);
	//preg_match_all("/<Sale>(.*?)<\/Sale>/",$xml_report,$xml);
	$titleAdded=0;
	$titles = array();
	$values = array();


	foreach($xml AS $xml_line){
		
		$xl = explode(",",$xml_line);
		
		if(!$titleAdded){
				
			$titles = $xl;
				
			$titleAdded++;
			
			continue;
			
		}else{
			
			for($i=0;$i<count($xl);$i++){
				
				$values[trim($titles[$i])] = $xl[$i];
				
			}
		
		}
		//echo '111';
		$traderID = 		rtrim(trim($values['Playercode']));
		$transactionDate = 	str_replace('"','',rtrim(trim($values['Signup_date'])));
		$bTag = 			rtrim(trim($values['var1']));
		$traderAlias = 		rtrim(trim($values['Username']));
		$fname = 			'';
		$lname =			'';
		$frozen =			rtrim(trim($values['Frozen']));
		$clientType =		rtrim(trim($values['Client_type']));
		$campName	 =		rtrim(trim($values['Affiliate']));
		$platform	 =		rtrim(trim($values['Platform']));
		$merchantID = 1;
		
		
		$merchantID = getMerchantByType(rtrim(trim($values['Client_type'])));
		
		echo ('merchantID: '.$merchantID.'<BR><BR>');
		
		/*
		if($clientType!='casino'){
			echo 'not casino - continue';
			
			$merchantID = 17;
		}
		*/
		// BTag Validator
		//echo '<BR><BR>bTag: '.$bTag;
		
		$isDefault = 0;
		if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) {
			//echo ' --- GO TO DEFAULT BTAG: '.$defaultBtag.' ---';
			$bTag = $defaultBtag;
			$isDefault = 1;
		}
		if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) {
			//echo ' --- GO TO DEFAULT CTAG: '.$defaultBtag.' ---';
			setLog('BTag not valid[Sales|'.$bTag.'|'.$TransactionDate.'|'.$TraderID.']',9,'red');
			echo 'not valid btag - continue';
			continue;
		}
		
		$btagElements = explodeBTAG($bTag);
		
		
		if($bTag == $defaultBtag){
			$campInfo = mysql_fetch_assoc(mysql_query('SELECT affiliateID FROM affiliates_campaigns_relations WHERE campID="'.$campName.'" AND merchantid='.$merchantID.' AND affiliateID>0'));
			if($campInfo['affiliateID']){
				$btagElements['affiliate_id'] = $campInfo['affiliateID'];
				$group = mysql_fetch_assoc(mysql_query('SELECT group_id AS g FROM affiliates WHERE id='.$btagElements['affiliate_id']));
				$btagElements['group_id'] = $group['g'];
			}
		}
		
		
		$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id, lastUpdate, status FROM data_reg WHERE trader_id='".$traderID."'"));
		
		//if(!$frozen){
			
			if (!$chkDouble['id']) {
				$sql = "INSERT INTO data_reg (rdate,ctag,trader_id,trader_alias, status, affiliate_id, group_id, banner_id, profile_id, market_id, country, freeParam, type, merchant_id,platform) VALUES ('".date("Y-m-d H:i:s", strtotime($transactionDate))."','".$bTag."','".$traderID."','".$traderAlias."','".($frozen==0 ? '' : 'frozen')."', '".$btagElements['affiliate_id']."', '".$btagElements['group_id']."', '".$btagElements['banner_id']."', '".$btagElements['profile_id']."', '".$btagElements['market_id']."', '".$btagElements['country']."', '".$btagElements['freeParam']."', 3,".$merchantID.",'".$platform."')";
				//echo '<BR><BR>'.$sql.'<BR><BR>';
				mysql_query($sql) OR die(mysql_error());
			}else if(!$frozen){
				mysql_query('UPDATE data_reg SET status="unfrozed", lastUpdate=NOW() WHERE status="frozen" AND trader_id='.$traderID);
			}
				
			//echo '<BR><BR>';
			echo '<BR>Trader ID: <b>'.$traderID.'</b> | BTag: <b>'.$bTag.'</b> ['.$transactionDate.'] - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br />';
		
		//}else{
			
		//	if ($chkDouble['id'] AND $chkDouble['status']!='frozen') {
			
		//		mysql_query('UPDATE data_reg SET status="frozen", lastUpdate=NOW() WHERE id="'.$traderID.'"');
			
		//	}
			
		//}
		
		flush();
		
	}
}


echo '<hr>';
echo '<b>Next: ACCOUNTS ***********************************************************</b>';
echo '<hr>';


if(isset($_REQUEST['sepReg']) OR 1==1){
	
	goReg('files/'.$_REQUEST['sepReg'].'.csv');
	
}else{


	if($reg_urls){
		foreach($reg_urls AS $url){
			echo '<BR><BR><BR><div style="font-size:14px; font-weight:bold; color:BLUE">Checking file: '.$url.'</div><BR>';
			goReg('files/'.$url);
		}
	}else{
		goReg('files/reportPlayer.csv');
	}

}
echo '<hr /><b>Done!</b><br />';



######################### [ Accounts ] #########################








// if ($_GET['m_date'] AND !$_GET['monthly']) die("<hr />Script Die!");

######################### [ Transaction ] #########################

function goSales($url){
	//echo 'goSales is not active...<BR>';
	//return;
	global $scanDate,$defaultBtag;
	//$url = 'ftp://dev@winneroptions.affiliatebuddies.com:afdev@184.107.206.210/efi/transactions_'.$scanDate.'.csv';//'csv/transactions_25012014.csv';//.$scanDate.'.xml';
	if (!file_exists($url)) die('Transaction Not Exist');
	$xml_report = file_get_contents($url) or $noReport=1;
	if($noReport){
		return;
	}
	$xml_report = rtrim($xml_report,"\n");
	$xml = explode("\n",$xml_report);
	$titleAdded=0;
	$titles = array();
	$values = array();
	foreach($xml AS $xml_line){
		//echo '<BR><BR><BR>XML LINE: ';
		//var_dump($xml_line);
		//echo '<BR><BR><BR>';
		
		$xl = explode(",",$xml_line);
		
		if(!$titleAdded){
				
			$titles = $xl;
				
			$titleAdded++;
			
			continue;
			
		}else{
		
			for($i=0;$i<count($xl);$i++){
				
				$values[trim($titles[$i])] = $xl[$i];
				
			}
		
		}
		
		
		$traderID = 		rtrim(trim($values['Playercode']));
		$traderAlias = 		rtrim(trim($values['Username']));
		$transactionDate = 	str_replace('"','',rtrim(trim($values['Accept_date'])));
		$transactionID =	rtrim(trim($values['Code']));
		$actionName = 		rtrim(trim($values['Type']));
		$amount =			rtrim(trim($values['Amount_PC']));
		$originalAmount =	rtrim(trim($values['Amount']));
		$coin =				rtrim(trim($values['Currency']));
		$clientType = 		rtrim(trim($values['Client_type']));
		$merchantID = 1;
		
		//echo ('transDate: ');
		//var_dump($values);
		//echo '<BR><BR>';
		//continue;
		/*
		if($clientType!='casino'){
			echo 'not casino - continue';
			$merchantID = 17;
		}
		*/
		$merchantID = getMerchantByType(rtrim(trim($values['Client_type'])));
		
		
		/*
		if ($_GET['m_date'] OR $_GET['monthly']) 
			if ($actionName != "deposit") continue;
		*/
		if ($actionName == "volume") 
			$amount = str_replace("-",'',$amount);
		else if ($actionName == "Revenue") {
			$amount = $amount;
			if ($amount > 0) 
				$amount = '-'.$amount;
			else $amount = str_replace('-','',$amount);
		} else 
			$amount = $amount;
		
		
		
		
		
		switch (strtolower($actionName)){
			case "deposit":							$actionName = 1; break;
			case "revenue":							$actionName = 2; break;
			case "bonus":							$actionName = 3; break;
			case "withdraw": 						$actionName = 4; break;
			case "volume": 							$actionName = 5; break;
			case "chargeback": 						$actionName = 6; break;
			case "chargebackreverse": 				$actionName = 7; break;
			case "credit": 							$actionName = 8; break;
			case "creditreverse": 					$actionName = 9; break;
			case "return": 							$actionName = 10; break;
			case "returnreverse": 					$actionName = 11; break;
		}
		
		
			
		$ww=mysql_fetch_assoc(mysql_query("SELECT ctag, affiliate_id FROM data_reg WHERE trader_id='".$traderID."'"));
		
		$bTag = $ww['ctag'];
		$isDefault = 0;
		// BTag Validator
		if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)){
			$bTag = $defaultBtag;
			$isDefault = 1;
		}
		if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) {
			setLog('BTag not valid[Tranz|'.$btag.'|'.$transactionDate.'|'.$traderID.']',9,'red');
			continue;
		}
		
			
		$btagElements = explodeBTAG($bTag);
		// BTag Validator
		
		if(($isDefault OR $bTag==$defaultBtag) AND $ww['affiliate_id']){
			$btagElements['affiliate_id'] = $ww['affiliate_id'];
			$group = mysql_fetch_assoc(mysql_query('SELECT group_id AS g FROM affiliates WHERE id='.$btagElements['affiliate_id']));
			$btagElements['group_id'] = $group['g'];
		}

		$sql = "SELECT id FROM data_sales WHERE tranz_id='".$transactionID."' AND type=".$actionName;

		$query = mysql_query($sql) OR die(mysql_error());
		$chkDouble=mysql_fetch_assoc($query);
		
		
		if (!$chkDouble['id']) {
			$sql = "INSERT INTO data_sales (rdate,tranz_id,ctag,trader_id, trader_alias, type,amount,affiliate_id, group_id, banner_id, market_id, profile_id, country, merchant_id) VALUES ('".date("Y-m-d H:i:s", strtotime($transactionDate))."','".$transactionID."','".$bTag."','".$traderID."','".$traderAlias."','".strtolower($actionName)."','".$amount."','".$btagElements['affiliate_id']."','".$btagElements['group_id']."','".$btagElements['banner_id']."','".$btagElements['market_id']."','".$btagElements['profile_id']."','".$btagElements['country']."',".$merchantID.")";			//echo '<BR><BR>'.$sql.'<BR><BR>';
			mysql_query($sql) OR die(mysql_error());
			
			echo 'Trader ID: <b>'.$traderID.'</b> | Transaction: <b>('.$actionName.') '.$transactionID.'</b> ['.$transactionDate.'] | Amount: <b>'.$amount.$coin.'</b> | BTag: <b>'.$bTag.'</b> - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br />';
		}else{
			echo 'Trader ID: <b>'.$traderID.'</b> | Transaction: <b>('.$actionName.') '.$transactionID.'</b> ['.$transactionDate.'] | Amount: <b>'.$amount.$coin.'</b> | BTag: <b>'.$bTag.'</b> - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br />';
		}
		
		flush();
		
	}
}


echo '<hr>';
echo '<b>Next: TRANSACTIONS ***********************************************************</b>';
echo '<hr>';

if(isset($_REQUEST['sepSales']) OR 1==1){
	
	//goSales('files/'.$_REQUEST['sepSales'].'1.csv');
	//goSales('files/'.$_REQUEST['sepSales'].'2.csv');
	goSales('files/Tranz01-11.csv');
	
}else{

	if($sales_urls){
		foreach($sales_urls AS $url){
			echo '<div style="font-size:14px; font-weight:bold; color:BLUE">Checking file: '.$url.'</div>';
			goSales('files/'.$url);
		}
		
		foreach($sales2_urls AS $url){
			echo '<div style="font-size:14px; font-weight:bold; color:BLUE">Checking file: '.$url.'</div>';
			goSales('files/'.$url);
		}
	}else{
		goSales('files/'.$scanDate.'.csv');
	}

}




echo '<hr /><b>Done!</b><br />';
######################### [ Transaction ] #########################



















// if ($_GET['m_date'] AND !$_GET['monthly']) die("<hr />Script Die!");

######################### [ Stats ] #########################

function goOtherActions($url){

	global $scanDate,$defaultBtag;
	if (!file_exists($url)) die('Transaction Not Exist');
	
	$xml_report = file_get_contents($url) ;
	if (!$xml_report){
		echo "Feed not working<BR>";
		return;
	}
	
	
	$xml_report = rtrim($xml_report,"\n");
	$xml = explode("\n",$xml_report);
	$titleAdded=0;
	$titles = array();
	$values = array();
	foreach($xml AS $xml_line){
		//echo '<BR><BR><BR>XML LINE: ';
		//var_dump($xml_line);
		//echo '<BR><BR><BR>';
		
		$xl = explode(",",$xml_line);
		
		if(!$titleAdded){
				
			$titles = $xl;
				
			$titleAdded++;
			
			continue;
			
		}else{
		
			for($i=0;$i<count($xl);$i++){
				
				$values[trim($titles[$i])] = $xl[$i];
				
			}
		
		}
		
		
		$traderID = 		rtrim(trim($values['Playercode']));
		$traderAlias = 		rtrim(trim($values['Username']));
		$statsDate = 		str_replace('"','',rtrim(trim($values['Stats_date']))).':00';
		$merchantID = 		getMerchantByType(str_replace('"','',rtrim(trim($values['"Client type"']))));

		//$actionName = 		rtrim(trim($values['Type']));
		
		
		
		$actions = Array();
		
		$bets =				intval(rtrim(trim($values['Bets'])));
		$bets>0 ? $actions[]=(object) array('type' => 'bets','val' => $bets, 'enum' => 1) : null;

		$wins =				intval(rtrim(trim($values['Wins'])));
		$wins>0 ? $actions[]=(object) array('type' => 'wins','val' => $wins, 'enum' => 2) : null;
		
		$jackpot =			intval(rtrim(trim($values['Jackpot_bets'])));
		$jackpot>0 ? $actions[]=(object) array('type' => 'jackpot','val' => $jackpot, 'enum' => 3) : null;
		
		$Bonuses =			intval(rtrim(trim($values['Bonuses'])));
		$Bonuses>0 ? $actions[]=(object) array('type' => 'bonuses','val' => $Bonuses, 'enum' => 4) : null;
		
		$rb =				intval(rtrim(trim($values['Removed_bonuses'])));
		$rb>0 ? $actions[]=(object) array('type' => 'removed_bonuses','val' => $rb, 'enum' => 5) : null;
		
		$bc =				intval(rtrim(trim($values['Bonuses_count'])));
		$bc>0 ? $actions[]=(object) array('type' => 'bonuses_count','val' => $bc, 'enum' => 6) : null;
		
		$rbc =				intval(rtrim(trim($values['Removed_bonuses_count'])));
		$rbc>0 ? $actions[]=(object) array('type' => 'removed_bonuses_count','val' => $rbc, 'enum' => 7) : null;
		
		$pl =				intval(rtrim(trim($values['Player_loss'])));
		$pl>0 ? $actions[]=(object) array('type' => 'Player_loss','val' => $pl, 'enum' => 8) : null;
		
		$pp =				intval(rtrim(trim($values['Player_profit'])));
		$pp>0 ? $actions[]=(object) array('type' => 'Player_profit','val' => $pp, 'enum' => 9) : null;
		
		$gi =				intval(rtrim(trim($values['Gross_income'])));
		$gi>0 ? $actions[]=(object) array('type' => 'Gross_income','val' => $gi, 'enum' => 10) : null;
		
		$rdb =				intval(rtrim(trim($values['Redeemed_bonuses'])));
		$rdb>0 ? $actions[]=(object) array('type' => 'Redeemed_bonuses','val' => $rdb, 'enum' => 11) : null;
		
		$hcr =				intval(rtrim(trim($values['House_correction_revenue'])));
		$hcr>0 ? $actions[]=(object) array('type' => 'House_correction_revenue','val' => $hcr, 'enum' => 12) : null;
		
		$hcl =				intval(rtrim(trim($values['House_correction_loss'])));
		$hcl>0 ? $actions[]=(object) array('type' => 'House_correction_loss','val' => $hcl, 'enum' => 13) : null;
		
		$sbcb =				intval(rtrim(trim($values['sportsbookcancelledbets'])));
		$sbcb>0 ? $actions[]=(object) array('type' => 'sportsbookcancelledbets','val' => $sbcb, 'enum' => 14) : null;
		
		
		
		
		
		
		
		
		for($j=0;$j<count($actions);$j++){
			
			
			$transactionID = $traderID.strtotime($statsDate).$j;
			$actionName = $actions[$j]->type;
			$amount = $actions[$j]->val;
			$enum = $actions[$j]->enum;
			
			/*
			if ($_GET['m_date'] OR $_GET['monthly']) 
				if ($actionName != "deposit") continue;
			
			if ($actionName == "volume") 
				$amount = str_replace("-",'',$amount);
			else if ($actionName == "Revenue") {
				$amount = $amount;
				if ($amount > 0) 
					$amount = '-'.$amount;
				else $amount = str_replace('-','',$amount);
			} else 
				$amount = $amount;
			*/
			
				
			$ww=mysql_fetch_assoc(mysql_query("SELECT ctag,affiliate_id FROM data_reg WHERE trader_id='".$traderID."'"));
			$bTag = $ww['ctag'];

			// BTag Validator
			if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) {
				$bTag = $defaultBtag;
			}
			if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) {
				setLog('BTag not valid[Tranz|'.$btag.'|'.$transactionDate.'|'.$traderID.']',9,'red');
				continue;
			}
			
			$btagElements = explodeBTAG($bTag);
			// BTag Validator
			if($bTag==$defaultBtag AND $ww['affiliate_id']){
				$btagElements['affiliate_id'] = $ww['affiliate_id'];
				$group = mysql_fetch_assoc(mysql_query('SELECT group_id AS g FROM affiliates WHERE id='.$btagElements['affiliate_id']));
				$btagElements['group_id'] = $group['g'];
			}
			
			$sql = "SELECT id FROM data_stats WHERE tranz_id='".$transactionID."' AND type=".$enum;
			//die($sql);
			$query = mysql_query($sql) OR die(mysql_error());
			$chkDouble=mysql_fetch_assoc($query);
			
			
			
			if (!$chkDouble['id']) {
				$sql = "INSERT INTO data_stats (rdate,tranz_id,ctag, trader_id, trader_alias, type,amount,affiliate_id, group_id, banner_id, market_id, profile_id, country, merchant_id) VALUES ('".date("Y-m-d H:i:s", strtotime($statsDate))."','".$transactionID."','".$bTag."','".$traderID."','".$traderAlias."',".$enum.",'".$amount."','".$btagElements['affiliate_id']."','".$btagElements['group_id']."','".$btagElements['banner_id']."','".$btagElements['market_id']."','".$btagElements['profile_id']."','".$btagElements['country']."',".$merchantID.")";			//echo '<BR><BR>'.$sql.'<BR><BR>';
				mysql_query($sql) OR die(mysql_error());
				echo 'Trader ID: <b>'.$traderID.'</b> | Transaction: <b>('.$actionName.') '.$transactionID.'</b> ['.$statsDate.'] | Amount: <b>'.$amount.$coin.'</b> | BTag: <b>'.$bTag.'</b> - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br />';
			}else{
				echo 'Trader ID: <b>'.$traderID.'</b> | Transaction: <b>('.$actionName.') '.$transactionID.'</b> ['.$statsDate.'] | Amount: <b>'.$amount.$coin.'</b> | BTag: <b>'.$bTag.'</b> - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br />';
			}
		
		}
		
		flush();
		
	}
}




echo '<hr>';
echo '<b>Next: STATS ***********************************************************</b>';
echo '<hr>';

if(isset($_REQUEST['sepStats']) OR 1==1){

	//goOtherActions('files/'.$_REQUEST['sepStats'].'.csv');
	goOtherActions('files/statsPoker01-11.csv');
	
}else{

	if($stats_urls){
		foreach($stats_urls AS $url){
			echo '<div style="font-size:14px; font-weight:bold; color:BLUE">Checking file: '.$url.'</div>';
			goOtherActions('files/'.$url);
		}
	}else{
		goOtherActions('files/'.$scanDate.'.csv');
	}
	
	if($stats2_urls){
		foreach($stats2_urls AS $url){
			echo '<div style="font-size:14px; font-weight:bold; color:BLUE">Checking file: '.$url.'</div>';
			goOtherActions('files/'.$url);
		}
	}else{
		goOtherActions('files/'.$scanDate.'.csv');
	}

}



echo '<hr /><b>Done!</b><br />';
######################### [ Transaction ] #########################
















function handleFrozens(){
	
	$rows = mysql_query('select trader_id, ctag, trader_alias FROM data_reg WHERE status="frozen" AND lastUpdate+INTERVAL 3 DAY <= NOW()');
	
	while($row = mysql_fetch_assoc($rows)){
		$traderID = $row['trader_id'];
		$traderAlias = $row['trader_alias'];
		$bTag = $row['ctag'];
		$traderSum = mysql_query('SELECT amount,type FROM data_sales WHERE trader_id="'.$traderID.'"') OR die(mysql_error());
		$sumAmount = 0;

		while($row = mysql_fetch_assoc($traderSum)){
			
			if($row['type']=='deposit'){
				$sumAmount+=$row['amount'];
			}else if($row['type']=='withdrawal' OR $row['type']=='chargeback'){
				$sumAmount-=$row['amount'];
			}
		}
		
		$btagElements = explodeBTAG($bTag);
		
		$sql = "INSERT INTO data_sales (rdate,tranz_id,ctag,trader_id,type,amount, affiliate_id, group_id, banner_id, market_id, profile_id, country,trader_alias, merchant_id) VALUES (NOW(),CONCAT('".$traderID."',NOW()),'".$bTag."','".$traderID."',6,'".$sumAmount."', '".$btagElements['affiliate_id']."', '".$btagElements['group_id']."', '".$btagElements['banner_id']."', '".$btagElements['market_id']."', '".$btagElements['profile_id']."', '".$btagElements['country']."','".$traderAlias."',1)";
		
		mysql_query($sql) OR die(mysql_error());
		
		mysql_query('UPDATE data_reg SET status="frozen charged" WHERE trader_id='.$traderID);
		echo '<div style="color:GREEN; font-size:12px; font-weight:bold">A new Chargeback ['.$sumAmount.'] was added to trader '.$traderID.'</div>';
		
		
	}

}


//handleFrozens();


updateUnit("sites","cron_lastscan='".dbDate()."',running='0'","id='".$getMarchant['id']."'");
?>