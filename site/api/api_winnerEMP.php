<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL ^ E_NOTICE);
	
// if (isset($_GET['monthly']))

$siteURL = "http://partners.rightcommission.com/";	
$ignoreStats= 0;				
$justStats =  isset($_GET['justStats']) ? $_GET['justStats'] : 0;
$debug              = isset($_GET['debug']);
$defaultAffiliateID = (isset($merchant['defaultAffiliateID']) AND $merchant['defaultAffiliateID'] > 0) ? $merchant['defaultAffiliateID'] : 500;
$defaultCtag        = 'a' . $defaultAffiliateID . '-b-p';
$api_label          = $merchant['name'];
$casinoName 		= "euromaxplay";
$defualtStartLimit = "&start=0&limit=200";
$forceAddNonExistingAccount = true;

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
		
		$btagElements = getBtag($bTag);
		
		$sql = "INSERT INTO data_sales (rdate,tranz_id,ctag,trader_id,type,amount, affiliate_id, group_id, banner_id, market_id, profile_id,uid, country,trader_alias, merchant_id) VALUES (NOW(),CONCAT('".$traderID."',NOW()),'".$bTag."','".$traderID."',6,'".$sumAmount."', '".$btagElements['affiliate_id']."', '".$btagElements['group_id']."', '".$btagElements['banner_id']."', '".$btagElements['market_id']."', '".$btagElements['profile_id']."', '".$btagElements['uid']."', '".$btagElements['country']."','".$traderAlias."',1)";
		
		
		mysql_query($sql);
		
		mysql_query('UPDATE data_reg SET status="frozen charged" WHERE trader_id='.$traderID);
		echo '<div style="color:GREEN; font-size:12px; font-weight:bold">A new Chargeback ['.$sumAmount.'] was added to trader '.$traderID.'</div>';
		
		
	}

}
//handleFrozens();


function ForceAddAccount (  $xml_line = '' , $merchantid=0,$frozen=0) {

global $defaultCtag;
		$db['platform'] = getTag('<platform>','<\/platform>',$xml_line);
		$db['trader_alias'] = getTag('<username>','<\/username>',$xml_line);
		$db['trader_id'] = getTag('<playercode>','<\/playercode>',$xml_line);
		$db['rdate']     = date("Y-m-d-1", strtotime(getTag('<requestdate>','<\/requestdate>',$xml_line)));
		$db['frozen']      = getTag('<frozen>','<\/frozen>',$xml_line);
		$db['ctag']      = getTag('<var1>','<\/var1>',$xml_line);
			if (!ctagValid($db['ctag'])) {
				$db['ctag'] = $defaultCtag;
		}
		populateDBwithCtag($db);
		$db['type'] = 'real';	
			
	$sql = 
						"INSERT INTO data_reg (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,trader_id,trader_alias,type,freeParam,platform,status) 
						VALUES
						('".$merchantid."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','-1','".$db['banner_id']."',
						'".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['freeParam']."','".$db['platform']."',
						'".(($frozen==0 || $frozen=='') ? '' : 'frozen')."')";
						
						

						mysql_query($sql) or die(mysql_error());
					
					
					
}


function getMerchantByType($type)
{
	$mid = 0;
	
	//die ('type: ' .$type);
	
	switch($type) {
		case 'casino': 					$mid=19; 	break;  // EMP
		default: 						$mid=0;		break;
	}
	return $mid;
}


function goOtherActionsWinner($url, &$intProcessed, &$intInserted)
{
	
	global $scanDate, $defaultCtag,$debug;
	$intProcessed = 0;
	$intInserted = 0;
	echo '<br>url: ' . $url.'<br>';
	
	//die ($url);
	if ($ignoreStats==1)
	return;
	
	else
	$xml_report = file_get_contents($url);
	
	
	//die ($url);
		
	
	if (!$xml_report){
		echo "Feed not working, OR there is no data to retrieve<br>Change a date-range for a sake of test.<br>";
		return;
	}
    
	$xml_report = rtrim($xml_report,"\n");
	$xml = explode("\n",$xml_report);
	$titleAdded=0;
	$titles = array();
	$values = array();
	
	preg_match_all('/<customer>(.*?)<\/customer>/', $xml_report, $xml);
	
	foreach($xml[1] as $xml_line) {
		
		
		
		//$intProcessed++;
		
		$actions = array();
		
		if ($debug) {
		echo '<br>';		
		var_dump($xml_line);
		echo '<br>';
		}
		
		/*$xl = explode(",",$xml_line);
		
		if (!$titleAdded) {
			$titles = $xl;
			$titleAdded++;
			continue;
		} else {
			for($i=0;$i<count($xl);$i++) {
				$values[trim($titles[$i])] = $xl[$i];
			}
		}
		
		$traderID    = rtrim(trim($values['playercode']));
		$traderAlias = rtrim(trim($values['username']));
		$statsDate   = str_replace('"', '', rtrim(trim($values['statsdate']))) . ':00';
		$clientType  = rtrim(trim($values['clienttype']));
		$merchantID  = getMerchantByType(rtrim(trim($values['clienttype'])));*/
		//$actionName = rtrim(trim($values['Type']));
		
		
		$traderID    =  getTag('<playercode>','<\/playercode>',$xml_line) ; //$arrUsernameMatches[0][0];
		$traderAlias =  getTag('<username>','<\/username>',$xml_line) ; //$arrUsernameMatches[0][0];
		$statsDate   =  getTag('<statsdate>','<\/statsdate>',$xml_line) ; //$arrUsernameMatches[0][0];
		$clientType  =  getTag('<clienttype>','<\/clienttype>',$xml_line) ; //$arrUsernameMatches[0][0];
		
		$merchantID = getMerchantByType($clientType);
		
		/*$x = array(
			'not parsed'   => $xml_line,
			'parsed'       => array( 
				'$traderID'    => $traderID,
				'$traderAlias' => $traderAlias, 
				'$statsDate'   => $statsDate, 
				'$clientType'  => $clientType, 
				'$merchantID'  => $merchantID,  
		));
		die(print_r($x));*/
		
		//preg_match_all('/<bets>(.*?)<\/bets>/', $xml_line, $arrBetsMatches);
		//$bets      = $arrBetsMatches[0][0];
		$bets =  getTag('<bets>','<\/bets>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'bets','val' => $bets, 'enum' => 1);
		
		// OLD VERSION
		/*$bets =				intval(rtrim(trim($values['bets'])));
		$bets>0 ? $actions[]=(object) array('type' => 'bets','val' => $bets, 'enum' => 1) : null;*/
		
		//preg_match_all('/<wins>(.*?)<\/wins>/', $xml_line, $arrWinsMatches);
		//$wins      = $arrWinsMatches[0][0];
		$wins =  getTag('<wins>','<\/wins>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'wins','val' => $wins, 'enum' => 2);
		
		//preg_match_all('/<jackpotbets>(.*?)<\/jackpotbets>/', $xml_line, $arrJackpotbetsMatches);
		//$jackpot   = $arrJackpotbetsMatches[0][0];
		$jackpot =  getTag('<jackpotbets>','<\/jackpotbets>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'jackpot','val' => $jackpot, 'enum' => 3);
		
		// preg_match_all('/<bonuses>(.*?)<\/bonuses>/', $xml_line, $arrBonusesMatches);
		// $bonuses   = $arrBonusesMatches[0][0];
		$bonuses =  getTag('<bonuses>','<\/bonuses>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'bonuses','val' => $bonuses, 'enum' => 4);
		
		// preg_match_all('/<removedbonuses>(.*?)<\/removedbonuses>/', $xml_line, $arrRbMatches);
		// $rb        = $arrRbMatches[0][0];
		$removedbonuses =  getTag('<removedbonuses>','<\/removedbonuses>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'removed_bonuses','val' => $removedbonuses, 'enum' => 5);
		
		// preg_match_all('/<bonusescount>(.*?)<\/bonusescount>/', $xml_line, $arrBcMatches);
		// $bc        = $arrBcMatches[0][0];
		$bonusescount =  getTag('<bonusescount>','<\/bonusescount>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'bonuses_count','val' => $bonusescount, 'enum' => 6);
		
		// preg_match_all('/<removedbonusescount>(.*?)<\/removedbonusescount>/', $xml_line, $arrRbcMatches);
		// $rbc       = $arrRbcMatches[0][0];
		$removedbonusescount =  getTag('<removedbonusescount>','<\/removedbonusescount>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'bonuses_count','val' => $removedbonusescount, 'enum' => 7);
		
		// preg_match_all('/<playerloss>(.*?)<\/playerloss>/', $xml_line, $arrPlMatches);
		// $pl        = $arrPlMatches[0][0];
		$playerloss =  getTag('<playerloss>','<\/playerloss>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'Player_loss','val' => $playerloss, 'enum' => 8);
		
		// preg_match_all('/<playerprofit>(.*?)<\/playerprofit>/', $xml_line, $arrPpMatches);
		// $pp        = $arrPpMatches[0][0];
		$playerprofit =  getTag('<playerprofit>','<\/playerprofit>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'Player_profit','val' => $playerprofit, 'enum' => 9);
		
		// preg_match_all('/<grossincome>(.*?)<\/grossincome>/', $xml_line, $arrGiMatches);
		// $gi        = $arrGiMatches[0][0];
		$grossincome =  getTag('<grossincome>','<\/grossincome>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'Gross_income','val' => $grossincome, 'enum' => 10);
		
		// preg_match_all('/<redeemedbonuses>(.*?)<\/redeemedbonuses>/', $xml_line, $arrRedeemedBonusesMatches);
		// $rdb       = $arrRedeemedBonusesMatches[0][0];
		$redeemedbonuses =  getTag('<redeemedbonuses>','<\/redeemedbonuses>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'Redeemed_bonuses','val' => $redeemedbonuses, 'enum' => 11);
		
		// preg_match_all('/<housecorrectionrevenue>(.*?)<\/housecorrectionrevenue>/', $xml_line, $arrHcrMatches);
		// $hcr       = $arrHcrMatches[0][0];
		$housecorrectionrevenue =  getTag('<housecorrectionrevenue>','<\/housecorrectionrevenue>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'House_correction_revenue','val' => $housecorrectionrevenue, 'enum' => 12);
		
		// preg_match_all('/<housecorrectionloss>(.*?)<\/housecorrectionloss>/', $xml_line, $arrHclMatches);
		// $hcl       = $arrHclMatches[0][0];
		$housecorrectionloss =  getTag('<housecorrectionloss>','<\/housecorrectionloss>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'House_correction_loss','val' => $housecorrectionloss, 'enum' => 13);
		
		// preg_match_all('/<sportsbookcancelledbets>(.*?)<\/sportsbookcancelledbets>/', $xml_line, $arrSbcbMatches);
		// $sbcb      = $arrSbcbMatches[0][0];
		$sportsbookcancelledbets =  getTag('<sportsbookcancelledbets>','<\/sportsbookcancelledbets>',$xml_line) ; //$arrUsernameMatches[0][0];
		$actions[] = (object) array('type' => 'sportsbookcancelledbets','val' => $sportsbookcancelledbets, 'enum' => 14);
		
		//die(print_r($actions));
		
		for ($j = 0; $j < count($actions); $j++) {
			
			$intProcessed++;
/// old method			
				//$statsDatePrc =date("YmdHis",$statsDate);
			//$transactionID = $traderID . $statsDatePrc . $j;
			
			
			$transactionID =  getTag('<id>','<\/id>',$xml_line);
			
			if ($transactionID>0)
			$transactionID .= '_' . $j ;
			

		
//$tmp = date('His', strtotime('-4 hours', $tmp));
//$uid =$tmp.$micro.  date('mYd') 

			
			
			$actionName    = $actions[$j]->type;
			$amount        = round($actions[$j]->val, 2);
			$enum          = $actions[$j]->enum;
			
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
			
			
			$ww   = mysql_fetch_assoc(mysql_query("SELECT ctag,affiliate_id FROM data_reg WHERE trader_id='".$traderID."'"));
			$ctag = $ww['ctag'];
			
			// ctag Validator
			if ( !ctagValid($ctag)) {
				$ctag = $defaultCtag;
			}
			
			//echo '<br>condition: ' .((  !ctagValid($ctag)) ? 'true' : 'false');
			
			if ( !ctagValid($ctag) ) {
				die ('ctag: ' . $ctag);
				echo ('ctag not valid[Tranz|'.$ctag.'|'.$transactionDate.'|'.$traderID.']');
				continue;
			}
			
			$ctagElements = getBtag($ctag);
			
			// ctag Validator
			if ($ctag == $defaultCtag AND $ww['affiliate_id']) {
				$ctagElements['affiliate_id'] = $ww['affiliate_id'];
				$group = mysql_fetch_assoc(mysql_query('SELECT group_id AS g FROM affiliates WHERE id='.$ctagElements['affiliate_id']));
				$ctagElements['group_id'] = $group['g'];
			}
			
			//$sql       = "SELECT id FROM data_stats WHERE tranz_id='".$transactionID."' AND type=".$enum;
			$sql       = "SELECT id FROM data_stats WHERE merchant_id= '". $merchantID . "'  and tranz_id='".$transactionID."' AND type=".$enum;
			$query     = mysql_query($sql) ;
			$chkDouble = mysql_fetch_assoc($query);
		if ($amount!='0' && $amount!=0) {	
			if (!$chkDouble['id']) {
				$sql = "INSERT INTO data_stats (rdate,tranz_id,ctag, trader_id, trader_alias, type,amount,affiliate_id, group_id, banner_id, market_id, profile_id,uid, country, merchant_id) 
						VALUES ('".$statsDate ."','".$transactionID."','".$ctag."','".$traderID."','".$traderAlias."',
						".$enum.",'".$amount."','".$ctagElements['affiliate_id']."','".$ctagElements['group_id']."','".$ctagElements['banner_id']."',
						'".$ctagElements['market_id']."','".$ctagElements['profile_id']."','".$ctagElements['uid']."','".$ctagElements['country']."',".$merchantID.")";
						
						/*
						$sql = "INSERT INTO data_stats (rdate,tranz_id,ctag, trader_id, trader_alias, type,amount,affiliate_id, group_id, banner_id, market_id, profile_id,uid, country, merchant_id) 
						VALUES ('".date("Y-m-d H:i:s", strtotime($statsDate))."','".$transactionID."','".$ctag."','".$traderID."','".$traderAlias."',
						".$enum.",'".$amount."','".$ctagElements['affiliate_id']."','".$ctagElements['group_id']."','".$ctagElements['banner_id']."',
						'".$ctagElements['market_id']."','".$ctagElements['profile_id']."','".$ctagElements['uid']."','".$ctagElements['country']."',".$merchantID.")";
						*/
				
				mysql_query($sql);
				$intInserted++;
				echo 'Trader ID: <b>'.$traderID.'</b> | Transaction: <b>('.$actionName.') '.$transactionID.'</b> ['.$statsDate.'] | Amount: <b>'.$amount.$coin.
					 '</b> | ctag: <b>'.$ctag.'</b> - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br />';
				
			} else {
				/*
			echo 'Trader ID: <b>'.$traderID.'</b> | Transaction: <b>('.$actionName.') '.$transactionID.'</b> ['.$statsDate.'] | Amount: <b>'.$amount.$coin.
					 '</b> | ctag: <b>'.$ctag.'</b> - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br />';
					 */
			}	}
		}
		
		flush();
	}
}



if ($api_url == '') {
	echo ' >>> Error: no api_url was found for: ' . $merchant['name'];
	continue;
} else {
	echo ' >>> API_url found for: ' . $merchant['name'];
}





$ww = dbGet('19', "merchants");

echo 'From: <u>'.$scanDateFrom.'</u> To: <u>'.$scanDateTo.'</u>';


if ($justStats==0) { 

		/**
		 * Signups.
		 */
		echo '<hr>Signups.<hr>';

		if (count($campaignIDs)==0) {
			$campaignIDs[]=1;
		}


		for ($page = 1; $page <= $totalPage; $page++) {  // Irrelevant.
			
			for ($camsID=0; $camsID < count($campaignIDs); $camsID++) {  // Irrelevant.
				echo '<hr /><b>Connecting to Players Signups Database (Campaign ID:'.$campaignIDs[$camsID].') Page: <u>'.$page.'</u>...</b><br />';
				
				if (!$campaignIDs[$camsID]  ) {
					continue;
				}
				
				$url = $api_url . '?api_username=' . $api_user . '&api_password=' . $api_pass . '&module=playerssignups&command=getallbydate&startdate=' 
								. $scanDateFrom . '&enddate=' . $scanDateTo . '&casino='.$casinoName.$defualtStartLimit;
				//die ($url);
				$url 		  = encodeDateTimeWithinUrl($url);
				$url 		  = convertToHttpsPostRequest($url);
			echo '<br>url: ' . $url.'<br>';
				
				
				
				
				$xml_report   = doGet($url);
				$intInserted  = 0;
				$intProcessed = 0;
				
				//preg_match_all('/<data_[0-9]+>(.*?)<\/data_[0-9]+>/', $xml_report, $xml);  // OLD VERSION
				preg_match_all('/<customer>(.*?)<\/customer>/', $xml_report, $xml);
				
				foreach($xml[1] as $xml_line) {
					unset($db);
					$exist           = 0;
					$db['trader_id'] = getTag('<playercode>','<\/playercode>',$xml_line);
					$db['platform'] = getTag('<platform>','<\/platform>',$xml_line);
					$db['trader_alias'] = getTag('<username>','<\/username>',$xml_line);
					$db['couponName']      = getTag('<couponname>','<\/couponname>',$xml_line);
					$db['rdate']     = date("Y-m-d", strtotime(getTag('<signupdate>','<\/signupdate>',$xml_line)));
					$db['frozen']      = getTag('<frozen>','<\/frozen>',$xml_line);
					$db['ctag']      = getTag('<var1>','<\/var1>',$xml_line);
					if (!ctagValid($db['ctag'])) {
						$db['ctag'] = $defaultCtag;
					}
					
					$chkDouble = mysql_fetch_assoc(mysql_query("SELECT id,type FROM data_reg WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."'"));
					
					$intProcessed++;
					
					if ($chkDouble['id']) {
						if ($chkDouble['type'] == "lead") {
							$db['id']   = $chkDouble['id'];
							$db['type'] = "real";
						} else {
							$exist = 1;
						}
					}
					
					/*$db['ctag'] = str_replace("--","-",$db['ctag']);
					$exp=explode("-",$db['ctag']);
					
					$db['affiliate_id']=substr($exp[0],1); // a
					$db['banner_id']=substr($exp[1],1); // b
					$db['profile_id']=substr($exp[2],1); // p
					$db['freeParam']=substr($exp[3],1); // f
					$db['country']=substr($exp[count($exp)-1],1); // c*/
					
					populateDBwithCtag($db);
					
					$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					
					if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
						$db['affiliate_id'] = $defaultAffiliateID;
						$getAffiliate 		= mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					}

					if (!empty($db['couponName'])) {
						$qry = "select id,affiliate_id from merchants_creative where type ='coupon' and valid = 1 and title like '".$db['couponName']."'";
						$couponsRslt		= mysql_fetch_assoc(mysql_query($qry));
						$couponsAffiliateID=$couponsRslt['affiliate_id'];
						$couponsBannerID=$couponsRslt['id'];
						if (!empty($couponsAffiliateID) && $couponsAffiliateID != $defaultAffiliateID) {
						$db['affiliate_id'] = $couponsAffiliateID;
						$db['banner_id'] = $couponsBannerID;
						}
					}
					
					
					$db['group_id']     = $getAffiliate['group_id'];
					//$db['trader_alias'] = str_replace(Array("\\","'","`"),Array("","",""),getTag('<FirstName>','<\/FirstName>',$xml_line).' '.getTag('<LastName>','<\/LastName>',$xml_line));
					$type               = getTag('<isDemo>','<\/isDemo>',$xml_line);
					
					if ($type) {
						$db['type'] = 'demo'; 
					} else {
						$db['type'] = 'real';
					}
					
					if ($chkDouble['type'] != $db['type']) {
						$db['id'] = $chkDouble['id'];
						$exist = 0;
					}
					
					if (count($db) > 1) {
						if (!$exist && !$db['id']) {
							mysql_query(
								"INSERT INTO data_reg (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,trader_id,trader_alias,type,freeParam,platform,status) 
								VALUES
								('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
								'".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['freeParam']."','".$db['platform']."',
								'".($frozen==0 ? '' : 'frozen')."')"
							) or die(mysql_error());
							
							$status = 'Inserted!';
							$reg_total++;
							$intInserted++;
							
							//mysql_query('UPDATE datascan_reg SET sent_ia=1 WHERE merchant_id='.$ww['merchant_id'].' AND trader_id="'.$db['trader_id'].'"');
							//echo file_get_contents('../pixel.php?act=account&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&trader_alias='.$db['trader_alias']);
							$pixelurl = $siteURL. 'pixel.php?act=account&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&trader_alias='.$db['trader_alias'];
							//die ($pixelurl);
							echo file_get_contents($pixelurl);
							
						} else {
						
							if(!$frozen){
								mysql_query('UPDATE data_reg SET status="unfrozed", lastUpdate=NOW() WHERE status="frozen" AND trader_id='.$db['trader_id']);
							}
							mysql_query("UPDATE data_reg SET type='".$db['type']."' WHERE id='".$db['id']."' and merchant_id= '".$ww['id']."' ");
							$status = 'Updated Lead ('.$db['type'].')!';
						}
						
						
						flush();
					}
				}
				
				echo '<hr /><b>Done!</b><br />',
					 '<b>Inserted: ' . $intInserted . '</b><br>',
					 '<b>Processed: ' . $intProcessed . '</b><br>';
			}
		}


		/**
		 * Transactions.
		 */
		echo '<hr>Transactions.<hr>';
		unset($db);

		for ($page=1; $page<=$totalPage; $page++) {

		echo '<hr /><b>Connecting to Player-Transactions Database Page: <u>'.$page.'</u>...</b><br />';

		$url = $api_url . '?api_username=' . $api_user . '&api_password=' . $api_pass . '&module=playertransactions&command=getallbydate&startdate=' 
								. $scanDateFrom . '&enddate=' . $scanDateTo . '&casino='.$casinoName.$defualtStartLimit;


		$url 		  = encodeDateTimeWithinUrl($url);
		$url 		  = convertToHttpsPostRequest($url);
		if ($debug) { 
			echo $url.'<br>';
		}

		echo '<br>url: ' . $url.'<br>';
		$xml_report   = doGet($url);
		$intInserted  = 0;
		$intProcessed = 0;		

		preg_match_all('/<customer>(.*?)<\/customer>/', $xml_report, $xml);
		$i=0;
		foreach ($xml[1] as $xml_line) {
			
			if ($debug==1) {
				var_dump($xml_line);
				echo  '<br>';
				}
				
			
			$tranzExist      = 0;
			$db['trader_id'] = getTag('<playercode>', '<\/playercode>', $xml_line);
			$qqrry = "SELECT id,ctag FROM data_reg WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."' LIMIT 1";
			$chkTrader       = mysql_fetch_assoc(mysql_query($qqrry));
			$intProcessed++;
			
			if (!$chkTrader['id'] && $forceAddNonExistingAccount) {

				ForceAddAccount($xml_line,$ww['id'],$frozen);
			}
			else if (!$chkTrader['id']) {
			 
			 if ($debug==1)
				echo 'trader id not exist..<br>';
				continue;
			}
			
			
			$db['tranz_id'] = getTag('<code>', '<\/code>', $xml_line);
			$db['type']     = strtolower(getTag('<type>', '<\/type>', $xml_line));
			if (strtolower($db['type'])=='withdraw')
				$db['type']= 'withdrawal';
				
			//echo '<br>traderid: '.$db['trader_id']. ' |  type: '.$db['type'].'<br>';
			//$paymentMethod  = getTag('<paymentMethod>', '<\/paymentMethod>', $xml_line);
			
			/*if (strtolower($paymentMethod) == "bonus") {
				$db['type'] = 'bonus';
			}*/
			
			
			if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") {

			if ($debug==1) {
				echo 'type: ' . $db['type']. '.<br>';
				echo 'unknown type..<br>';
			}
				continue;
			}
			
			if ($debug ==1) { 
			$i++;
			echo $i . '<br>';
			}
			$db['rdate']  = getTag('<requestdate>', '<\/requestdate>', $xml_line);
			$db['amount'] = getTag('<amountpc>', '<\/amountpc>', $xml_line);
			//$coin         = getTag('<currency>', '<\/currency>', $xml_line);
				$coin='USD';
			
			$db['amount'] = getUSD($db['amount'], $coin);
			
			// ORIGINAL VERSION
			/*$chkExist = mysql_fetch_assoc(mysql_query(
											"SELECT id,type,tranz_id FROM data_sales 
											WHERE merchant_id= '".$ww['id']."' and  trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"
											));*/
			
			$chkExist = mysql_fetch_assoc(mysql_query(
											"SELECT id,type,tranz_id FROM data_sales 
											WHERE merchant_id= '".$ww['id']."' and  trader_id='".$db['trader_id']."' AND tranz_id='".$db['tranz_id']."'"
											));
				
			if ($chkExist['id']) {
				$tranzExist = 1;
			}
			
			// Check cTag From Trader
			$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag,ftdamount,initialftdtranzid,merchant_id,trader_id FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."'"));
			
			if ($traderInfo['ctag']) {
				$db['ctag'] = $traderInfo['ctag'];
			} else {
				$db['ctag'] = $defaultCtag;
			}
			
			// Check cTag From Trader
			if (!ctagValid($db['ctag'])) {
				continue;
			}
			
			populateDBwithCtag($db);
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate       = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			}
			
			$db['group_id'] = $getAffiliate['group_id'];
			
			
			
			if (count($db) > 1 AND !$tranzExist) {
				//echo '<pre>', print_r($db), '</pre>';
				
				mysql_query("INSERT INTO data_sales (merchant_id, rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam) 
							VALUES
							('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."',
							'".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."',
							'".$db['type']."','".$db['amount']."','".$db['freeParam']."')") 
				or die(mysql_error());

		if ($traderInfo['ftdamount']==0 && $db['type']=='deposit' && $traderInfo['initialftdtranzid']=='') {
				
						$GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from data_sales where 
												trader_id = " . $traderInfo['trader_id'] .  " and merchant_id = " . $traderInfo['merchant_id'] . " and type='deposit'  order by rdate limit 0,1";

								$GetFTDforTrader =mysql_fetch_assoc( mysql_query($GetFTDforTraderQuery));
								if (!empty($GetFTDforTrader)) {
										$UpdateFTDforTrader = "update data_reg set  ftdamount = " .$GetFTDforTrader['amount']." , initialftdtranzid = '"
										.$GetFTDforTrader['tranz_id']."' , initialftddate = '" .$GetFTDforTrader['rdate'] ."' where trader_id= ".$chkTrader['trader_id']. "  
										and merchant_id = " . $chkTrader['merchant_id'];
										
										echo 'New FTD record added to Data_Reg, TraderID: ' . $chkTrader['trader_id'].'<br>';
										mysql_query($UpdateFTDforTrader);
								}
				
				}
				
				
				
				$intInserted++;
				$sales_total++;
				
				//mysql_query('UPDATE datascan_sales SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND tranz_id="'.$row['tranz_id'].'"');
				
				if (!in_array($db['affiliate_id'], $sentAffiliates) && $db['type'] == "deposit") {
					$new_ftd = mysql_num_rows(mysql_query(
									"SELECT id FROM data_sales WHERE merchant_id= '".$ww['id']."' and affiliate_id='".$db['affiliate_id']."' 
									AND trader_id='".$db['trader_id']."' AND type='deposit' AND rdate <= '".date("Y-m-d")."'"
								));
								
					if ($new_ftd == "1") {
						$affiliateInfo = mysql_fetch_assoc(mysql_query("SELECT id,mail,first_name,username FROM affiliates WHERE id='".$db['affiliate_id']."'"));
						
						if ($affiliateInfo['id'] AND $db['affiliate_id'] AND !$affiliateInfo['com_alert']) {
							$set->sendTo = $affiliateInfo['mail'];
							$set->sendFrom = $set->webMail;
							$set->subject = $set->webTitle.' - You\'ve made a commission!';
							$set->aff_uname = $affiliateInfo['username'];
							$set->aff_fname = $affiliateInfo['first_name'];
							
							$mailCode = mysql_fetch_assoc(mysql_query('SELECT mailCode FROM mail_templates WHERE id=-2'));
							
							sendTemplate($mailCode['mailCode'], $db['affiliate_id'], 0);
							$sentAffiliates[] = $db['affiliate_id'];
						}
						
					}
				}
				
				$pixelurl = $siteURL. 'pixel.php?act=deposit&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&tranz='.$db['tranz_id'].'&type='.$db['type'].'&currency='.$db['coin'].'&amount='.$db['amount'];
							//die ($pixelurl);
							echo file_get_contents($pixelurl);
							
							
				//echo file_get_contents('../pixel.php?act=deposit&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&tranz='.$db['tranz_id'].'&type='.$db['type'].'&currency='.$db['coin'].'&amount='.$db['amount']);
				flush();
				
			} else {
				//echo '<div> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br /></div>';
			}
			
			}
		}
		echo '<hr /><b>Done!</b><br />',
					 '<b>Inserted: ' . $intInserted . '</b><br>',
					 '<b>Processed: ' . $intProcessed . '</b><br>';


		/**
		 * Stats.
		 */
}

 echo '<hr>Stats.<hr>';


$RowsPerPage = 100;
$intProcessed = 0;
$lastProcessedNum= 0;
$shouldExit=0;

for ($page=0;  ; $page++) {

echo '<hr /><b>Connecting to PlayerStats Database Page: <u>'.$page.'</u>...</b><br />';

$url = $api_url . '?api_username=' . $api_user . '&api_password=' . $api_pass . '&module=playerstats&command=getallbydate&startdate=' 
	    . $scanDateFrom . '&enddate=' . $scanDateTo . '&casino='.$casinoName.'&start='.($page*$RowsPerPage) . '&limit=' . ($RowsPerPage+ $page*$RowsPerPage);

$url 	      = encodeDateTimeWithinUrl($url);
$url 		  = convertToHttpsPostRequest($url);
$intInserted  = 0;


goOtherActionsWinner($url, $intProcessed, $intInserted);

if ($intProcessed==$lastProcessedNum) {
		break;
}
$lastProcessedNum = $intProcessed;	




////  old one 
// for ($page=1; $page<=$totalPage; $page++) {
// echo '<hr /><b>Connecting to PlayerStats Database Page: <u>'.$page.'</u>...</b><br />';

// $url = $api_url . '?api_username=' . $api_user . '&api_password=' . $api_pass . '&module=playerstats&command=getallbydate&startdate=' 
			    // . $scanDateFrom . '&enddate=' . $scanDateTo . '&casino='.$casinoName;

// $url 	      = encodeDateTimeWithinUrl($url);
// $url 		  = convertToHttpsPostRequest($url);
// $intInserted  = 0;
// $intProcessed = 0;
// goOtherActionsWinner($url, $intProcessed, $intInserted);



/*$url 	      = encodeDateTimeWithinUrl($url);
$url 		  = convertToHttpsPostRequest($url);
$xml_report   = doGet($url);
$intInserted  = 0;
$intProcessed = 0;

die(gettype($xml_report));*/

//preg_match_all('/<customer>(.*?)<\/customer>/', $xml_report, $xml);

/*foreach($xml[1] as $xml_line) {
	$existWithdrawal = 0;
	$db['trader_id'] = getTag('<playercode>', '<\/playercode>', $xml_line);
	$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag FROM data_reg WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."' LIMIT 1"));
	if (!$chkTrader['id']) continue;
	$db['tranz_id'] = getTag('<id>','<\/id>',$xml_line);
	$db['type'] = 'withdrawal';
	
	$db['rdate'] = getTag('<confirmTime>','<\/confirmTime>',$xml_line);
	$db['amount'] = getTag('<amount>','<\/amount>',$xml_line);
	$coin = getTag('<currency>','<\/currency>',$xml_line);
	$status = getTag('<status>','<\/status>',$xml_line);
	$paymentMethod = getTag('<paymentMethod>','<\/paymentMethod>',$xml_line);
	if (strtolower($paymentMethod) == "chargeback") $db['type'] = 'chargeback';
	
	if ($status != "approved") continue;
	
	
	$db['amount'] = getUSD($db['amount'],$coin);
	
	$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
	if ($chkExist['id']) $existWithdrawal=1;
	
	// Check cTag From Trader
	$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id= '".$ww['id']."'  and trader_id='".$db['trader_id']."'"));
	if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
		else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
	// Check cTag From Trader
		
	if (!ctagValid($db['ctag'])) continue;
	
	populateDBwithCtag($db);
	
	//$db['ctag'] = str_replace("--","-",$db['ctag']);
	//$exp=explode("-",$db['ctag']);
	
	//$db['affiliate_id']=substr($exp[0],1); // a
	//$db['banner_id']=substr($exp[1],1); // b
	//$db['profile_id']=substr($exp[2],1); // p
	//$db['freeParam']=substr($exp[3],1); // f
	//$db['country']=substr($exp[count($exp)-1],1); // c
	
	$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
	if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
		$db['affiliate_id'] = $defaultAffiliateID;
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		}
	$db['group_id'] = $getAffiliate['group_id'];
	
	if (count($db) > 1 AND !$existWithdrawal) {
		echo '<pre>', print_r($db), '</pre>';
		
		mysql_query("INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam) VALUES
			('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['amount']."','".$db['freeParam']."')") or die(mysql_error());
		echo '<div>'.$ww['id'] .'['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br /></div>';
		$sales_total++;
		mysql_query('UPDATE datascan_sales SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND tranz_id="'.$row['tranz_id'].'"');
		flush();
	} else {
		echo '<div>'.$ww['id'].'['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br /></div>';
	}
	}*/
}

echo '<hr /><b>Done!</b><br />',
	 '<b>Inserted: ' . $intInserted . '</b><br>',
	 '<b>Processed: ' . $intProcessed . '</b><br>';



if ($justStats ==0) {
			 
		/**
		 * Transactions2.
		 */
		echo '<hr>Transactions2.<hr>';
		 
		for ($page=1; $page<=$totalPage; $page++) {
		echo '<hr /><b>Connecting to Player-Transactions2 Database Page: <u>'.$page.'</u>...</b><br />';

		$url = $api_url . '?api_username=' . $api_user . '&api_password=' . $api_pass . '&module=playertransactions2&command=getallbydate&startdate=' 
						. $scanDateFrom . '&enddate=' . $scanDateTo . '&casino='.$casinoName.$defualtStartLimit;

		$url 	      = encodeDateTimeWithinUrl($url);
		$url 		  = convertToHttpsPostRequest($url);
		echo '<br>url: ' . $url.'<br>';
		$xml_report   = doGet($url);
		$intInserted  = 0;
		$intProcessed = 0;

		preg_match_all('/<customer><\/customer>/', $xml_report, $xml);

		if (!is_array($xml[1])) {
			continue;
		}

		foreach($xml[1] as $xml_line) {
			$existVolume = 0;
			$db['trader_id'] = getTag('<playercode>', '<\/playercode>', $xml_line);
			$intProcessed++;
			$chkTrader = mysql_fetch_assoc(mysql_query("SELECT id,ctag FROM data_reg WHERE merchant_id= '".$ww['id']."'  and trader_id='".$db['trader_id']."' LIMIT 1"));
			
			if (!$chkTrader['id']) {
				continue;
			}
			
			$db['tranz_id'] = getTag('<code>', '<\/code>', $xml_line);
			$db['type']     = 'volume';
			
			$db['rdate']  = getTag('<requestdate>','<\/requestdate>',$xml_line);
			$db['amount'] = getTag('<amountpc>','<\/amountpc>',$xml_line);
			$coin         = 'USD';// getTag('<currency>','<\/currency>',$xml_line);
			//$status       = getTag('<status>','<\/status>',$xml_line);
			
			if ($status == "open") {
				continue;
			}
			
			$db['amount'] = getUSD($db['amount'], $coin);
			$chkExist = mysql_fetch_assoc(mysql_query(
				"SELECT id,type,tranz_id FROM data_sales 
				WHERE merchant_id= '".$ww['id']."'  and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"
			));
			
			if ($chkExist['id']) {
				$existVolume = 1;
			}
			
			// Check cTag From Trader
			$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id= '".$ww['id']."'  and trader_id='".$db['trader_id']."'"));
			
			if ($traderInfo['ctag']) {
				$db['ctag'] = $traderInfo['ctag'];
			} else {
				$db['ctag'] = $defaultCtag;
			}
			
			// Check cTag From Trader	
			if (!ctagValid($db['ctag'])) {
				continue;
			}
			
			populateDBwithCtag($db);
			
			/*$db['ctag'] = str_replace("--","-",$db['ctag']);
			$exp=explode("-",$db['ctag']);
			
			$db['affiliate_id']=substr($exp[0],1); // a
			$db['banner_id']=substr($exp[1],1); // b
			$db['profile_id']=substr($exp[2],1); // p
			$db['freeParam']=substr($exp[3],1); // f
			$db['country']=substr($exp[count($exp)-1],1); // c*/
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			}
			
			$db['group_id'] = $getAffiliate['group_id'];
			
			if (count($db) > 1 && !$existVolume) {
				// echo '<pre>', print_r($db), '</pre>';
				
				mysql_query("INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam) 
						VALUES
						('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."',
						'".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."',
						'".$db['type']."','".$db['amount']."','".$db['freeParam']."')") 
				or die(mysql_error());
					
				$sales_total++;
				mysql_query('UPDATE datascan_sales SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND tranz_id="'.$row['tranz_id'].'"');
				
				$intInserted++;
				flush();
				
			} else {
				//echo '<div> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br /></div>';
			}
			
			}
		}

		echo '<hr /><b>Done!</b><br />',
			 '<b>Inserted: ' . $intInserted . '</b><br>',
			 '<b>Processed: ' . $intProcessed . '</b><br>';
}


echo 'Cron is Done!';

