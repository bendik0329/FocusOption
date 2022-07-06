<?php
// cron job v2.0 without table foreach merchant.

ini_set("memory_limit","128M");
require_once('common/global.php');


/**
 * I've successfuly run following script!
 * You can find the "print_r'ed" result (for "rightcommission") under the script.
 */
/*$arrResult = array();
$sql       = "SELECT IFNULL(amount, '-') AS min_cpa,
  	        (SELECT IFNULL(amount, '-') FROM `affiliates_deals` WHERE affiliate_id = 500 AND dealType = 'cpa' GROUP BY rdate ORDER BY rdate DESC) AS cpa,
	        (SELECT IFNULL(amount, '-') FROM `affiliates_deals` WHERE affiliate_id = 500 AND dealType = 'dcpa' GROUP BY rdate ORDER BY rdate DESC) AS dcpa,
	        (SELECT IFNULL(amount, '-') FROM `affiliates_deals` WHERE affiliate_id = 500 AND dealType = 'revenue' GROUP BY rdate ORDER BY rdate DESC) AS revenue,
	        (SELECT IFNULL(amount, '-') FROM `affiliates_deals` WHERE affiliate_id = 500 AND dealType = 'cpl' GROUP BY rdate ORDER BY rdate DESC) AS cpl,
	        (SELECT IFNULL(amount, '-') FROM `affiliates_deals` WHERE affiliate_id = 500 AND dealType = 'cpm' GROUP BY rdate ORDER BY rdate DESC) AS cpm,
	        (SELECT IFNULL(amount, '-') FROM `affiliates_deals` WHERE affiliate_id = 500 AND dealType = 'cpc' GROUP BY rdate ORDER BY rdate DESC) AS cpc,
	        (SELECT IFNULL(amount, '-') FROM `affiliates_deals` WHERE affiliate_id = 500 AND dealType = 'revenue_spread' GROUP BY rdate ORDER BY rdate DESC) AS revenue_spread 
FROM `affiliates_deals` 
WHERE affiliate_id = 500 AND dealType = 'min_cpa' 
GROUP BY rdate 
ORDER BY rdate DESC;";

$resource = mysql_query($sql);

while ($arrRow = mysql_fetch_assoc($resource)) {
	$arrResult[] = $arrRow;
}

print_r($arrResult);
die;*/



$find = Array("\n","\t");
$replace = Array("","");
$sentAffiliates = Array();
// if ($_GET['pass'] != "hanan") _goto();

set_time_limit(0);

function overrideCtagByCamp ($xml_line ='' , $camp=0) { 
$ctag='';
			if($camp=="21"){
				$ctag = 'a4020-b528-p';
			}else if($camp=="2"){
				$ctag = 'a4021-b527-p';
			}else if($camp=="26"){
				$ctag = 'a4038-b549-p';
			}else if($camp=="25"){
				$ctag = 'a4033-b548-p';
			}else if($camp=="27"){
				$ctag = 'a4039-b558-p';
			}else if($camp=="29"){
				$ctag = 'a4046-b563-p';
			}else if($camp=="30"){
				$ctag = 'a4046-b562-p';
			}else if($camp=="1"){
				$ctag = 'a4045-b561-p';
			}else if($camp=="31"){
				$ctag = 'a4046-b564-p';
			}else if($camp=="33"){
				$ctag = 'a4049-b564-p';
			}else if($camp=="42"){
				$ctag = 'a4075-b619-p';
			}else{
				$ctag = getTag('<subCampaignParam>','<\/subCampaignParam>',$xml_line);
			}
	return $ctag;
}


function ctagValid($tag='') { // a20-b100-p
	if (!$tag) return false;
	$exp=explode("-",$tag);
	if (substr($exp[0],0,1) == "a" AND substr($exp[1],0,1) == "b") return true;
	return false;
	}

function getTag($tag, $endtag, $xml) {
	if (!$endtag) $endtag=$tag;
	preg_match_all("/".$tag."(.*?)".$endtag."/", $xml, $matches);
	if (isset($matches[1][0])) return $matches[1][0];
	}

	
function fire_pixel ($type , $affiliate_id) {
	
	
	if ($api_type =='account') {
	echo 'about to get to getPixel...<BR>';
							$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND valid='1' AND type='".$type."')"));
							if ($getPixel['id']) {
								echo 'In getPixel1...<BR>';
								if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) {
									echo 'In getPixel2...<BR>';
									doPost($getPixel['pixelCode']);
								}else {
									echo 'In getPixel3...<BR>';
									echo $getPixel['pixelCode'];
								}
								echo 'In getPixel --> About to update DB...<BR>';
								mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
								}
		} 
		else if ($type =='lead') {
		$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND valid='1' AND type='account'"));
						if ($getPixel['id']) {
							if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) doPost($getPixel['pixelCode']);
								else echo $getPixel['pixelCode'];
							mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
							}
		} 
		else if ($type =='sale') {
		$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$affiliate_id."' AND valid='1' AND type='sale'"));
				if ($getPixel['id']) {
					if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) doPost($getPixel['pixelCode']);
						else echo $getPixel['pixelCode'];
					mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
					}
		}
		else {
		
		}
}

function doPost($url){
	$parse_url=parse_url($url);
	$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
	if (!$da) {
		echo "$errstr ($errno)<br/>\n";
		echo $da;
		} else {
		$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
		$params .= "Host: ".$parse_url['host']."\r\n";
		$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$params .= "User-Agent: ".$set->webTitle." Agent\r\n";
		$params .= "Content-Length: ".strlen($parse_url['query'])."\r\n";
		$params .= "Connection: close\r\n\r\n";
		$params .= $parse_url['query'];
		fputs($da, $params);
		while (!feof($da)) $response .= fgets($da);
		fclose($da);
		
		// split the result header from the content
		$result = explode("\r\n\r\n", $response, 2);
		$content = isset($result[1]) ? $result[1] : '';
		return $content;
		}
	}



function doPost2($url){
	
	$ch = curl_init();  
	
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	/*
	curl_setopt($ch,CURLOPT_HEADER, array(
		"Authorization" => "Basic dGVzdHVzZXIsdGVzdHBhc3N3b3Jk",
        "Content-Type" => "application/json; charset=utf-8"
	)); 
	*/
	/*
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization" => "Basic dGVzdHVzZXIsdGVzdHBhc3N3b3Jk",
        "Content-Type" => "application/json; charset=utf-8"
	));
	*/
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	  //'X-abc-AUTH: 123456789' // you can replace this with your $auth variable
	  "Authorization: Basic dGVzdHVzZXIsdGVzdHBhc3N3b3Jk",
	  "Content-Type: application/json; charset=utf-8"
	));
	
    $output=curl_exec($ch);
	
	if($output === false){
		echo 'Curl error: ' . curl_error($ch);
	}
 
    curl_close($ch);
    return $output;
	
	
}




function checkCurrencies(){
	global $set;
	$currentTime = new DateTime();
	$startTime = new DateTime('01:00');
	$endTime = new DateTime('04:00');
	if (($currentTime >= $startTime && $currentTime <= $endTime) OR ($_REQUEST['currency'])) {
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://".$set->http_host."/getCurrency.php"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch); 
		echo $output;
	}else{
		echo '<BR>Currencies will be update in other time.<BR>';
	}
}
checkCurrencies();
	
		
echo '<style type="text/css">html,body { font-size: 11px; font-family: Tahoma; } </style>';


if ($_GET['m_date']) {
	$exp_mdate=explode("-",$_GET['m_date']);
	if ($_GET['monthly']) {
		$scanDateFrom = date("Y-m-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])).' 00:00:00';
		$scanDateTo = date("Y-m-01", strtotime("+1 Month",strtotime($scanDateFrom))).' 23:59:59';
		$totalPage = 30;
		} else {
		$scanDateFrom = date("Y-m-d", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])).' 00:00:00';
		$scanDateTo = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]))).' 23:59:59';
		}
	} else {
	$scanDateFrom = date("Y-m-d", strtotime("-1 Day")).' 00:00:00';
	$scanDateTo = date("Y-m-d").' 23:59:59';
	}
if (!$totalPage) $totalPage = 2;

	

	
$merchants = mysql_query('SELECT * FROM merchants WHERE valid=1');

while($merchant=mysql_fetch_assoc($merchants)){

	$campsQ = mysql_query('select campID from affiliates_campaigns_relations where merchantid ='.$merchant['id']);
	$campaignIDs = Array();
	while($row = mysql_fetch_assoc($campsQ)){
		array_push($campaignIDs,$row['campID']);
	}
	/*
	$campaignIDs = Array(
	"0" => "53" // RBO Affiliate Program
	);
	*/
	
	$api_url = $merchant['APIurl'];  //including ftp url's for file type brand
	$api_user = $merchant['APIuser']; //ftp user in case of file type brand
	$api_pass = $merchant['APIpass']; //ftp pass in case of file type brand
	
	$api_type = $merchant['apiType'];
	//$api_type="file";
	
	if($api_type=='currentDesc'){
	
		
		
		(!$from) ? $from=date('Y-m-d') : null;
		(!$to) ? $to=date('Y-m-d',strtotime('+1 Day')) : null;
		
		///////////////////////////////////////////////////////////// LEADS
		echo '<BR> RUNNING LEADS...... <BR>';

		//$api_url = 'http://api.partner.currentdesk.com/api/LeadRegistration?startDate='.$from.'&endDate='.$to;
		//$api_auth = 'dGVzdHVzZXIsdGVzdHBhc3N3b3Jk';
		$api_auth = $api_pass;

		$xml_report=doPost2($api_url.'?startDate='.$from.'&endDate='.$to);

		if($xml_report!='"Might be your inputs are either wrong or not in right format. Please check and try again"'){
			$data = json_decode($xml_report);
			var_dump($data);
			for($i=0;$i<count($data);$i++){
				
				######################### [ CSV Leads ] #########################

					unset($db);
					$exist = 0;
					
					
					$db['trader_id'] = $data[$i]->TraderID;
					$db['rdate'] = date('Y-m-d H:i:s', strtotime($data[$i]->RegistrationDate));
					$db['type'] = 1;//$data[$i]->Status;
					$db['ctag'] = $data[$i]->CTag___SubCampaign;
					$db['trader_alias'] = $data[$i]->TraderName;

					if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;
					
					
					$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id FROM data_reg_".strtolower($ww['name'])." WHERE trader_id='".$db['trader_id']."' AND type='".$db['type']."'"));
					if ($chkDouble['id']) $exist = 1;

					$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];

					

					
					$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
						$db['affiliate_id'] = $defaultAffiliateID;
						$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
						}
					$db['group_id'] = $getAffiliate['group_id'];
					
					//$db['type'] = 'lead';
					if (!$exist) if (count($db) > 1) {
						mysql_query("INSERT INTO data_reg_".strtolower($ww['name'])." (rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,trader_id,trader_alias,type,freeParam) VALUES
							('".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['freeParam']."')") or die(mysql_error());
						$reg_total++;
						
						$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$db['affiliate_id']."' AND valid='1' AND type='account'"));
						$getPixel['pixelCode'] = str_replace('&#34;','"',$getPixel['pixelCode']);
						$getPixel['pixelCode'] = rawurldecode($getPixel['pixelCode']);
						if ($getPixel['id']) {
							if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) doPost($getPixel['pixelCode']);
								else echo $getPixel['pixelCode'];
							mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
						}
						
					}
					echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') - <b>'.($exist ? 'Exist' : 'Inserted').'</b>!<br />';
					flush();
					
			}
			echo '<hr /><b>Done!</b><br />';
			######################### [ CSV Leads ] #########################
		}

		// ---------------------------------------------------------------------------------------------------------
		///////////////////////////////////////////////////////////// DONE LEADS





		////////////////////////////////////////////////////////////// REG
		echo '<BR> RUNNING REG...... <BR>';
		$api_url = 'http://api.partner.currentdesk.com/api/TradeInfo?startDate='.$from.'&endDate='.$to;
		$api_auth = 'dGVzdHVzZXIsdGVzdHBhc3N3b3Jk';

		$xml_report=doPost2($api_url);

		if($xml_report!='"Might be your inputs are either wrong or not in right format. Please check and try again"'){
			$data = json_decode($xml_report);
			
			for($i=0;$i<count($data);$i++){
		//echo 'From: <u>'.$scanDateFrom.'</u> To: <u>'.$scanDateTo.'</u>';
		//for ($page=1; $page<=$totalPage; $page++)
			//for ($camsID=0; $camsID < count($campaignIDs); $camsID++) {
				//echo '<hr /><b>Connecting to Customers\'s & Lead\'s Database (Campaign ID:'.$campaignIDs[$camsID].') Page: <u>'.$page.'</u>...</b><br />';
				//if (!$campaignIDs[$camsID]) continue;
				//$url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Customer&COMMAND=view&FILTER[campaignid]='.$campaignIDs[$camsID].'&FILTER[regTime][min]='.$scanDateFrom.'&FILTER[regTime][max]='.$scanDateTo.'&page='.$page;

				//$xml_report=doPost($url);
				//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
				
				//foreach($xml[1] AS $xml_line) {
					unset($db);
					$exist = 0;
					$db['trader_id'] = $data[$i]->TraderID;
					$db['trader_alias'] = $data[$i]->TraderAlias;
					$db['rdate'] = date('Y-m-d H:i:s', strtotime($data[$i]->CreationDate));
					$db['ctag'] = $data[$i]->cTag;
					$db['type'] = 3;
					//$db['trader_id'] = ;
					
					///////////////TODO: REMOVE NEXT LINE. FOR TESTING ONLY:
					$db['ctag'] = 'a524-b12-p1';
					if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;
					
					$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id,type FROM data_reg_".strtolower($ww['name'])." WHERE trader_id='".$db['trader_id']."'"));
					if ($chkDouble['id']) {
						if ($chkDouble['type'] == 1) {
							$db['id'] = $chkDouble['id'];
							$db['type'] = 3;
						} else 
							$exist=1;
					}
					
					$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					
					
					$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
						$db['affiliate_id'] = $defaultAffiliateID;
						$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					}
					$db['group_id'] = $getAffiliate['group_id'];
					
					//$db['trader_alias'] = str_replace(Array("\\","'","`"),Array("","",""),getTag('<FirstName>','<\/FirstName>',$xml_line).' '.getTag('<LastName>','<\/LastName>',$xml_line));
					
					//$type = getTag('<isDemo>','<\/isDemo>',$xml_line);
					if ($type) $db['type'] = 2; else $db['type'] = 3;
					if ($chkDouble['type'] != $db['type']) {
						$db['id'] = $chkDouble['id'];
						$exist = 0;
					}
					if (count($db) > 1) {
						if (!$exist) if (!$db['id']) {
							/* echo '<pre>';
							print_r($db);
							die(); */
							mysql_query("INSERT INTO data_reg_".strtolower($ww['name'])." (rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,trader_id,trader_alias,type,freeParam) VALUES
								('".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['freeParam']."')") or die(mysql_error());
							$status = 'Inserted!';
							$reg_total++;
							
							$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$db['affiliate_id']."' AND valid='1' AND type='account'"));
							$getPixel['pixelCode'] = str_replace('&#34;','"',$getPixel['pixelCode']);
							$getPixel['pixelCode'] = rawurldecode($getPixel['pixelCode']);
							if ($getPixel['id']) {
								if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) doPost($getPixel['pixelCode']);
									else echo $getPixel['pixelCode'];
								mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
								}
							
						} else {
							mysql_query("UPDATE data_reg_".strtolower($ww['name'])." SET type='".$db['type']."' WHERE id='".$db['id']."'");
							$status = 'Updated Lead ('.$db['type'].')!';
						}
						echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') - <b>'.($exist ? 'Exist' : $status).'</b>!<br />';
						flush();
						}
					//}
				echo '<hr /><b>Done!</b><br />';
				######################### [ CSV REG ] #########################
				
			}
			
		}
		////////////////////////////////////////////////////////////// DONE REG





		//////////////////////////////////////////////////////////// SALES
		echo '<BR> RUNNING SALES...... <BR>';
		$api_url = 'http://api.partner.currentdesk.com/api/TransactionInfo?startDate='.$from.'&endDate='.$to;
		$api_auth = 'dGVzdHVzZXIsdGVzdHBhc3N3b3Jk';

		$xml_report=doPost2($api_url);

		if($xml_report!='"Might be your inputs are either wrong or not in right format. Please check and try again"'){
			$data = json_decode($xml_report);
			//var_dump($data);

			echo '<BR><BR>';
			//var_dump($data[0]->TransactionDate);

			for($i=0;$i<count($data);$i++){
				$db['tranz_id'] = $data[$i]->TransactionID;
				$db['trader_id'] = $data[$i]->TraderID;
				$db['rdate'] = date('Y-m-d H:i:s', strtotime($data[$i]->TransactionDate));
				$db['ctag'] = $data[$i]->cTag;
				$dbtype = explode('-',$data[$i]->ActionType);
				$db['type'] = $dbtype[0];
				if($db['type']=='Incoming Funds'){
					$db['type'] = 'deposit';
				}else if($db['Outgoing Funds']==''){
					$db['type'] = 'withdrawal';
				}else if($db['type']=='Internal Transfers' OR $db['type']=='Conversions Requests'){
					$db['type'] = $dbtype[1];
				}else if($db['type']=='Credit In' OR $db['type']=='Credit Out'){
					continue;
				}
				$db['coin'] = $data[$i]->Coin;
				$db['amount'] = getUSD($data[$i]->Amount,$db['coin']);
				$db['coin'] = 'usd';
				
				$tranzExist=0;
				
				$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag FROM data_reg_".strtolower($ww['name'])." WHERE trader_id='".$db['trader_id']."' LIMIT 1"));
				if (!$chkTrader['id']) continue;
				
				if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") continue;
				
				
				
				$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales_".strtolower($ww['name'])." WHERE trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
				if ($chkExist['id']) {
					$tranzExist = 1;
				}
				
				// Check cTag From Trader
				$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg_".strtolower($ww['name'])." WHERE trader_id='".$db['trader_id']."'"));
				if ($traderInfo['ctag']) 
					$db['ctag'] = $traderInfo['ctag'];
				// Check cTag From Trader
				
				
				///////////////TODO: REMOVE NEXT LINE. FOR TESTING ONLY:
				$db['ctag'] = 'a524-b12-p1';
				
				if (!ctagValid($db['ctag'])) continue;
				
									$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
				
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
					$db['affiliate_id'] = $defaultAffiliateID;
					$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					}
				$db['group_id'] = $getAffiliate['group_id'];
				
				if (count($db) > 1 AND !$tranzExist) {
					// dbAdd($db,"data_sales_".strtolower($ww['name']));
					mysql_query("INSERT INTO data_sales_".strtolower($ww['name'])." (rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam) VALUES
						('".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['amount']."','".$db['freeParam']."')") or die(mysql_error());
					echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
					$sales_total++;
					
					if (!in_array($db['affiliate_id'],$sentAffiliates)) if ($db['type'] == "deposit") {
						$new_ftd=mysql_num_rows(mysql_query("SELECT id FROM data_sales_".strtolower($ww['name'])."  WHERE affiliate_id='".$db['affiliate_id']."' AND trader_id='".$db['trader_id']."' AND type='deposit' AND rdate <= '".date("Y-m-d")."'"));
						if ($new_ftd == "1") {
							$affiliateInfo=mysql_fetch_assoc(mysql_query("SELECT id,mail,first_name,username FROM affiliates WHERE id='".$db['affiliate_id']."'"));
							if ($affiliateInfo['id'] AND $db['affiliate_id'] AND !$affiliateInfo['com_alert']) {
								$set->sendTo = $affiliateInfo['mail'];
								$set->sendFrom = $set->webMail;
								$set->subject = $set->webTitle.' - You\'ve made a commission!';
								$set->body = 'Dear '.$affiliateInfo['first_name'].'!<br />
									<br />
									You\'ve made a commission - come check out your stats at:<br />
									<br />
									<a href="'.$set->webAddress.'/?username='.$affiliateInfo['username'].'" target="_blank">'.$set->webAddress.'</a><br />
									<br />
									<span style="font-size: 11px;"><b>Note:</b> Due to a lag between the '.$set->webTitle.' scanners and the Merchant Report Systems, there could be a delay in the appearance of the FTD date. Please check yesterday\'s FTDs as well.</span><br />
									<br />
									Best Regards,<br />
									'.$set->webTitle;
								sendMail();
								$sentAffiliates[] = $db['affiliate_id'];
								}
							}
						}
					
					$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$db['affiliate_id']."' AND valid='1' AND type='sale'"));
					$getPixel['pixelCode'] = str_replace('&#34;','"',$getPixel['pixelCode']);
					$getPixel['pixelCode'] = rawurldecode($getPixel['pixelCode']);
					if ($getPixel['id']) {
						if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) doPost($getPixel['pixelCode']);
							else echo $getPixel['pixelCode'];
						mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
						}
					
					flush();
					} else {
					echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
					}
				//var_dump($db);
				//echo '<BR><BR>';
			}
		}else{
			echo 'No results.<BR>';
		}


		echo 'Done Transactions';
		//////////////////////////////////////////////////////////////////// DONE SALES















		//////////////////////////////////////////////////////////// STATS
		echo '<BR> RUNNING STATS...... <BR>';
		$api_url = 'http://api.partner.currentdesk.com/api/TraderStatus?startDate='.$from.'&endDate='.$to;
		$api_auth = 'dGVzdHVzZXIsdGVzdHBhc3N3b3Jk';

		$xml_report=doPost2($api_url);

		if($xml_report!='"Might be your inputs are either wrong or not in right format. Please check and try again"'){
			$data = json_decode($xml_report);
			//var_dump($data);

			echo '<BR><BR>';
			//var_dump($data[0]->TransactionDate);

			for($i=0;$i<count($data);$i++){
				$db['tranz_id'] = $data[$i]->TransactionID;
				$db['trader_id'] = $data[$i]->TraderID;
				$db['rdate'] = date('Y-m-d H:i:s', strtotime($data[$i]->TransactionDate));
				$db['ctag'] = $data[$i]->cTag;
				//$dbtype = explode('-',$data[$i]->ActionType);
				$db['type'] = $data[$i]->ActionType;
				$db['coin'] = $data[$i]->Coin;
				$db['turnover'] = $data[$i]->Turnover;
				$db['spreads'] = $data[$i]->Spreads;
				$db['amount'] = getUSD($data[$i]->Amount,$db['coin']);
				
				$tranzExist=0;
				
				$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag FROM data_reg_".strtolower($ww['name'])." WHERE trader_id='".$db['trader_id']."' LIMIT 1"));
				if (!$chkTrader['id']) continue;
				
				if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") continue;
				
				
				
				$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_stats_".strtolower($ww['name'])." WHERE trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
				if ($chkExist['id']) {
					$tranzExist = 1;
				}
				
				// Check cTag From Trader
				$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg_".strtolower($ww['name'])." WHERE trader_id='".$db['trader_id']."'"));
				if ($traderInfo['ctag']) 
					$db['ctag'] = $traderInfo['ctag'];
				// Check cTag From Trader
				
				
				///////////////TODO: REMOVE NEXT LINE. FOR TESTING ONLY:
				$db['ctag'] = 'a524-b12-p1';
				
				if (!ctagValid($db['ctag'])) continue;
				
									$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
				
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
					$db['affiliate_id'] = $defaultAffiliateID;
					$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					}
				$db['group_id'] = $getAffiliate['group_id'];
				
				if (count($db) > 1 AND !$tranzExist) {
					// dbAdd($db,"data_stats_".strtolower($ww['name']));
					mysql_query("INSERT INTO data_stats_".strtolower($ww['name'])." (rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam, spreads, turnover) VALUES
						('".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['amount']."','".$db['freeParam']."','".$db['spreads']."','".$db['turnover']."')") or die(mysql_error());
					echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
					$stats_total++;
					/*
					if (!in_array($db['affiliate_id'],$sentAffiliates)) if ($db['type'] == "deposit") {
						$new_ftd=mysql_num_rows(mysql_query("SELECT id FROM data_stats_".strtolower($ww['name'])."  WHERE affiliate_id='".$db['affiliate_id']."' AND trader_id='".$db['trader_id']."' AND type='deposit' AND rdate <= '".date("Y-m-d")."'"));
						if ($new_ftd == "1") {
							$affiliateInfo=mysql_fetch_assoc(mysql_query("SELECT id,mail,first_name,username FROM affiliates WHERE id='".$db['affiliate_id']."'"));
							if ($affiliateInfo['id'] AND $db['affiliate_id'] AND !$affiliateInfo['com_alert']) {
								$set->sendTo = $affiliateInfo['mail'];
								$set->sendFrom = $set->webMail;
								$set->subject = $set->webTitle.' - You\'ve made a commission!';
								$set->body = 'Dear '.$affiliateInfo['first_name'].'!<br />
									<br />
									You\'ve made a commission - come check out your stats at:<br />
									<br />
									<a href="'.$set->webAddress.'/?username='.$affiliateInfo['username'].'" target="_blank">'.$set->webAddress.'</a><br />
									<br />
									<span style="font-size: 11px;"><b>Note:</b> Due to a lag between the '.$set->webTitle.' scanners and the Merchant Report Systems, there could be a delay in the appearance of the FTD date. Please check yesterday\'s FTDs as well.</span><br />
									<br />
									Best Regards,<br />
									'.$set->webTitle;
								sendMail();
								$sentAffiliates[] = $db['affiliate_id'];
								}
							}
						}
					/*
					$getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$db['affiliate_id']."' AND valid='1' AND type='sale'"));
					$getPixel['pixelCode'] = str_replace('&#34;','"',$getPixel['pixelCode']);
					$getPixel['pixelCode'] = rawurldecode($getPixel['pixelCode']);
					if ($getPixel['id']) {
						if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) doPost($getPixel['pixelCode']);
							else echo $getPixel['pixelCode'];
						mysql_query("UPDATE pixel_monitor SET totalFired=totalFired+1 WHERE id='".$getPixel['id']."'");
						}
					
					flush();
					} else {
					echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
					}*/
				//var_dump($db);
				//echo '<BR><BR>';
				}
			}
		}else{
			echo 'No results.<BR>';
		}
		
		
		
		
		
		
	
	
	}else if($api_type=='file'){
		
		$ch = curl_init();
		/////////////////////////////////////////////////////// TODO: HANDLE PIXEL REQUESTS FROM FILE
		//set the url, number of POST vars, POST data
		
		curl_setopt($ch,CURLOPT_URL, $api_url.'?'.'merchant='.$merchant['id'].'&api_user='.$api_user.'&api_pass='.$api_pass.($_GET['m_date'] ? '&m_date='.$_GET['m_date'] : '').($_GET['monthly'] ? '&monthly='.$_GET['monthly'] : ''));
//		curl_setopt($ch,CURLOPT_GET, true);
//		curl_setopt($ch,CURLOPT_GETFIELDS, );
		echo $api_url.'?merchant='.$merchant['id'].'&api_user='.$api_user.'&api_pass='.$api_pass.($_GET['m_date'] ? '&m_date='.$_GET['m_date'] : '').($_GET['monthly'] ? '&monthly='.$_GET['monthly'] : '');
		//execute post
		$result = curl_exec($ch);

		//close connection
		curl_close($ch);

		  // /api/$merchant nane .php
	
	}else if($api_type=='postback'){
		
		$defaultAffiliateID = (isset($merchant['defaultAffiliateID']) AND $merchant['defaultAffiliateID']>0) ? $merchant['defaultAffiliateID'] : 500;
		$defaultBtag='a'.$defaultAffiliateID.'-b-p';
		
		
		
		//////////////////////////////////////////////////////// POSTBACK REG
		$rowsQ = mysql_query('SELECT * FROM datascan_reg WHERE valid=1 AND sent_ia=0 AND merchant_id='.$merchant['id']);
		while($row = mysql_fetch_assoc($rowsQ)){
			$qry = "SELECT id,type FROM data_reg WHERE merchant_id= '".$row['merchant_id']."' and trader_id='".$row['trader_id']."'";
			//die ($qry);
			$chkDouble=mysql_fetch_assoc(mysql_query($qry));
			$exist = 0;
			if ($chkDouble['id']) {
				$exist = 1;
			}

			
			$row['btag'] = str_replace("--","-",$row['btag']);
			$exp=explode("-",$row['btag']);
			
			$row['affiliate_id']=substr($exp[0],1); // a
			$row['banner_id']=substr($exp[1],1); // b
			$row['profile_id']=substr($exp[2],1); // p
			$row['freeParam']=substr($exp[3],1); // f
			$row['country']=substr($exp[count($exp)-1],1); // c
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$row['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$row['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$row['affiliate_id']."'"));
				}
			$row['group_id'] = $getAffiliate['group_id'];
			$memberStatus =  $row['MemberStatus'] == 'active' ? 'real' : $row['MemberStatus'];
			
			if($exist==1){
				echo '<div>Trader exist! ['.$row['trader_id'].']</div>';
			}else{
				mysql_query("INSERT INTO data_reg (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,trader_id,trader_alias,type,freeParam) VALUES
				('".$row['merchant_id']."','".$row['rdate']."','".$row['btag']."','".$row['affiliate_id']."','".$row['group_id']."','".$row['market_id']."','".$row['banner_id']."','".$row['profile_id']."','".$row['country']."','".$row['trader_id']."','".$row['TraderAlias']."','".$memberStatus."','".$row['freeParam']."')") or die(mysql_error());
				
				//echo("INSERT INTO data_reg (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,trader_id,trader_alias,type,freeParam) VALUES
				//('".$row['merchant_id']."','".$row['rdate']."','".$row['btag']."','".$row['affiliate_id']."','".$row['group_id']."','".$row['market_id']."','".$row['banner_id']."','".$row['profile_id']."','".$row['country']."','".$row['trader_id']."','".$row['TraderAlias']."','".$row['MemberStatus']."','".$row['freeParam']."')");

				mysql_query('UPDATE datascan_reg SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND trader_id="'.$row['trader_id'].'"');
				
				echo '<div>Date: ' .$row['rdate'] .'  , (PostBack)- REG --- Trader Inserted! ['.$row['trader_id'].']</div>';
				
				if ($row['MemberStatus']=='active' OR $row['MemberStatus']=='real'){
					//echo 'about to fire pixel 1:<BR>';
					
					echo file_get_contents('http://demo.affiliatebuddies.com/pixel.php?act=account&ctag='.$row['btag'].'&trader_id='.$row['trader_id'].'&trader_alias='.$row['trader_alias'].'&type='.$row['memberStatus']);
					//fire_pixel('account',$row['affiliate_id']);
				}else if ($row['MemberStatus']=='lead'){
					//echo 'about to fire pixel 2:<BR>';
					echo file_get_contents('http://demo.affiliatebuddies.com/pixel.php?act=account&ctag='.$row['btag'].'&trader_id='.$row['trader_id'].'&trader_alias='.$row['trader_alias'].'&type='.$row['memberStatus']);
					//fire_pixel('lead',$row['affiliate_id']);
				}
				
				/*
				if (filter_var($firedPixel, FILTER_VALIDATE_URL)) {
					echo 'In getPixel2...<BR>';
					doPost($firedPixel);
				}else {
					echo 'In getPixel3...<BR>';
					echo $firedPixel;
				}
				*/
			}
			
			
		}
		echo 'Done Processing PostBack - Registrations for ' . $merchant['name'] . '.<br>';
		
		
		//////////////////////////////////////////////////////// POSTBACK SALES
		$rowsQ = mysql_query('SELECT * FROM datascan_sales WHERE valid=1 AND sent_ia=0 AND merchant_id='.$merchant['id']);
		while($row = mysql_fetch_assoc($rowsQ)){
			$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id,type FROM data_sales WHERE merchant_id= '".$row['merchant_id']."' and tranz_id='".$row['tranz_id']."'"));
			$tranzExist = 0;
			if ($chkDouble['id']) {
				$tranzExist = 1;
			}
			
			$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id= '".$row['merchant_id']."' and trader_id='".$row['trader_id']."'"));
			if ($traderInfo['ctag']) 
				$row['btag'] = $traderInfo['ctag'];
			// Check cTag From Trader
				
			if (!ctagValid($row['btag'])) continue;
			
			$row['btag'] = str_replace("--","-",$row['btag']);
			$exp=explode("-",$row['btag']);
			
			$row['affiliate_id']=substr($exp[0],1); // a
			$row['banner_id']=substr($exp[1],1); // b
			$row['profile_id']=substr($exp[2],1); // p
			$row['freeParam']=substr($exp[3],1); // f
			$row['country']=substr($exp[count($exp)-1],1); // c
			
			
			if($row['coin']!="USD"){
			
				$curr = mysql_fetch_assoc(mysql_query("SELECT val from currencies WHERE fromCurr='".$row['coin']."' AND toCurr='USD'"));
				if($curr['val']){
					echo 'In currency:';
					$row['value'] = (round(round($row['value']*$curr['val'])/10)*10);
				}
			}
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$row['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$row['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$row['affiliate_id']."'"));
			}
			$row['group_id'] = $getAffiliate['group_id'];
			
			if (count($row) > 1 AND !$tranzExist) {
				// dbAdd($db,"data_sales_".strtolower($ww['name']));
				mysql_query("INSERT INTO data_sales (merchant_id, rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam) VALUES
					('".$row['merchant_id']."','".$row['rdate']."','".$row['btag']."','".$row['affiliate_id']."','".$row['group_id']."','".$row['market_id']."','".$row['banner_id']."','".$row['profile_id']."','".$row['country']."','".$row['tranz_id']."','".$row['trader_id']."','".$row['trader_alias']."','".$row['type']."','".$row['value']."','".$row['freeParam']."')") or die(mysql_error());
				echo '<div> ['.$row['rdate'].'] '.$row['trader_id'].' (ctag: '.$row['ctag'].') /'.$row['type'].' Amount: $ '.$row['amount'].'/ - <b>Inserted</b>!<br /></div>';
				$sales_total++;
				mysql_query('UPDATE datascan_sales SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND tranz_id="'.$row['tranz_id'].'"');
				
				
				echo file_get_contents('pixel.php?act=deposit&ctag='.$row['ctag'].'&trader_id='.$row['trader_id'].'&tranz='.$row['tranz_id'].'&type='.$row['type'].'&currency='.$row['coin'].'&amount='.$row['amount']);
				//fire_pixel('sale',$row['affiliate_id']);
				
				if (!in_array($row['affiliate_id'],$sentAffiliates)) if ($row['type'] == "deposit") {
					$new_ftd=mysql_num_rows(mysql_query("SELECT id FROM data_sales WHERE merchant_id= '".$row['merchant_id']."' and affiliate_id='".$row['affiliate_id']."' AND trader_id='".$row['trader_id']."' AND type='deposit' AND rdate <= '".date("Y-m-d")."'"));
					if ($new_ftd == "1") {
						$affiliateInfo=mysql_fetch_assoc(mysql_query("SELECT id,mail,first_name,username FROM affiliates WHERE id='".$row['affiliate_id']."'"));
						if ($affiliateInfo['id'] AND $row['affiliate_id'] AND !$affiliateInfo['com_alert']) {
							$set->sendTo = $affiliateInfo['mail'];
							$set->sendFrom = $set->webMail;
							$set->subject = $set->webTitle.' - You\'ve made a commission!';
							
							$set->aff_uname = $affiliateInfo['username'];
							$set->aff_fname = $affiliateInfo['first_name'];
							
							$mailCode = mysql_fetch_assoc(mysql_query('SELECT mailCode FROM mail_templates WHERE id=-2'));
							
							sendTemplate($mailCode['mailCode'],$row['affiliate_id'],0);
							
							/*
							$set->body = 'Dear '.$affiliateInfo['first_name'].'!<br />
								<br />
								You\'ve made a commission - come check out your stats at:<br />
								<br />
								<a href="'.$set->webAddress.'/?username='.$affiliateInfo['username'].'" target="_blank">'.$set->webAddress.'</a><br />
								<br />
								<span style="font-size: 11px;"><b>Note:</b> Due to a lag between the '.$set->webTitle.' scanners and the Merchant Report Systems, there could be a delay in the appearance of the FTD date. Please check yesterday\'s FTDs as well.</span><br />
								<br />
								Best Regards,<br />
								'.$set->webTitle;
							sendMail();
							*/
							$sentAffiliates[] = $row['affiliate_id'];
						}
					}
				}
			}else{
				echo '<BR>Tranz Exists! ['.$row['tranz_id'].']';
			}
					
					
		}
		
	
	
	}else if($api_type=='spot'){
	

		
		$defaultAffiliateID = (isset($merchant['defaultAffiliateID']) AND $merchant['defaultAffiliateID']>0) ? $merchant['defaultAffiliateID'] : 500;
		$defaultBtag='a'.$defaultAffiliateID.'-b-p';
		
		$api_label = $merchant['name'];
		
		
		if($api_url==''){
			echo ' >>> Error: no api_url was found for: '.$merchant['name'];
			continue;
		}


		$ww = dbGet('1',"merchants");

		echo 'From: <u>'.$scanDateFrom.'</u> To: <u>'.$scanDateTo.'</u>';
		for ($page=1; $page<=$totalPage; $page++)
			for ($camsID=1; $camsID == count($campaignIDs); $camsID++) {
				echo '<hr /><b>Connecting to Customers\'s & Lead\'s Database (Campaign ID:'.$campaignIDs[$camsID].') Page: <u>'.$page.'</u>...</b><br />';
				if (!$campaignIDs[$camsID]) continue;
				$url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Customer&COMMAND=view&FILTER[campaignid]='.$campaignIDs[$camsID].'&FILTER[regTime][min]='.$scanDateFrom.'&FILTER[regTime][max]='.$scanDateTo.'&page='.$page;

				$xml_report=doPost($url);
				preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
				
				foreach($xml[1] AS $xml_line) {
					unset($db);
					$exist = 0;
					$db['trader_id'] = getTag('<id>','<\/id>',$xml_line);
					$db['rdate'] = date("Y-m-d", strtotime(getTag('<regTime>','<\/regTime>',$xml_line)));
					$db['ctag'] = getTag('<subCampaignParam>','<\/subCampaignParam>',$xml_line);
					if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;
					
					$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id,type FROM data_reg WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."'"));
					if ($chkDouble['id']) {
						if ($chkDouble['type'] == "lead") {
							$db['id'] = $chkDouble['id'];
							$db['type'] = "real";
							} else $exist=1;
						}
					
										$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					
					
					$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
						$db['affiliate_id'] = $defaultAffiliateID;
						$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
						}
					$db['group_id'] = $getAffiliate['group_id'];
					
					$db['trader_alias'] = str_replace(Array("\\","'","`"),Array("","",""),getTag('<FirstName>','<\/FirstName>',$xml_line).' '.getTag('<LastName>','<\/LastName>',$xml_line));
					$type = getTag('<isDemo>','<\/isDemo>',$xml_line);
					if ($type) $db['type'] = 'demo'; else $db['type'] = 'real';
					if ($chkDouble['type'] != $db['type']) {
						$db['id'] = $chkDouble['id'];
						$exist = 0;
						}
					if (count($db) > 1) {
						echo 'before if <BR>';
						if (!$exist) if (!$db['id']) {
							/* echo '<pre>';
							print_r($db);
							die(); */
							mysql_query("INSERT INTO data_reg (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,trader_id,trader_alias,type,freeParam) VALUES
								('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['freeParam']."')") or die(mysql_error());
							$status = 'Inserted!';
							$reg_total++;
							mysql_query('UPDATE datascan_reg SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND trader_id="'.$row['trader_id'].'"');
							
							
							echo file_get_contents('pixel.php?act=account&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&trader_alias='.$db['trader_alias'].'&type='.$row['memberStatus']);
							//fire_pixel('account',$row['affiliate_id']);
							
							} else {
							mysql_query("UPDATE data_reg SET type='".$db['type']."' WHERE id='".$db['id']."' and merchant_id= '".$ww['id']."' ");
							$status = 'Updated Lead ('.$db['type'].')!';
							}
						echo '<div> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') - <b>'.($exist ? 'Exist' : $status).'</b>!<br /></div>';
						flush();
						}
					}
				echo '<hr /><b>Done!</b><br />';
				
				######################### [ CSV Leads ] #########################

				$url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Lead&COMMAND=view&FILTER[campaignid]='.$campaignIDs[$camsID].'&FILTER[regTime][min]='.$scanDateFrom.'&FILTER[regTime][max]='.$scanDateTo.'&page='.$page;
				$xml_report=doPost($url);

				preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);

				echo '<hr /><b>Connecting to Lead\'s Database (Campaign ID:'.$campaignIDs[$camsID].') Page: <u>'.$page.'</u>...</b><br />';
				foreach($xml[1] AS $xml_line) {
					unset($db);
					$exist = 0;
					$db['trader_id'] = getTag('<id>','<\/id>',$xml_line);
					
					$TransactionDate = explode(" ",getTag('<regTime>','<\/regTime>',$xml_line));
					$TransactionDate = explode("/",$TransactionDate[2]);
					$db['rdate'] = '20'.$TransactionDate[2].'-'.$TransactionDate[1].'-'.$TransactionDate[0];
					
					if (!$TransactionDate OR $TransactionDate == "1970-01-01") $TransactionDate = dbDate();
					$db['ctag'] = getTag('<subCampaignParam>','<\/subCampaignParam>',$xml_line);
					if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;
					
					$db['trader_alias'] = getTag('<FirstName>','<\/FirstName>',$xml_line).' '.getTag('<LastName>','<\/LastName>',$xml_line);
					$db['country'] = getTag('<Country>','<\/Country>',$xml_line);
					
					$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id FROM data_reg WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."' AND type='lead'"));
					if ($chkDouble['id']) $exist = 1;
					
										$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					
					$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
					if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
						$db['affiliate_id'] = $defaultAffiliateID;
						$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
						}
					$db['group_id'] = $getAffiliate['group_id'];
					
					$db['type'] = 'lead';
					if (!$exist) if (count($db) > 1) {
						mysql_query("INSERT INTO data_reg (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,trader_id,trader_alias,type,freeParam) VALUES
							('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['freeParam']."')") or die(mysql_error());
						$reg_total++;
						
						mysql_query('UPDATE datascan_reg SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND trader_id='.$row['trader_id']);
						
						
						echo file_get_contents('pixel.php?act=account&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&trader_alias='.$db['trader_alias'].'&type='.$row['memberStatus']);
						//fire_pixel('lead',$db['affiliate_id']);
						
						}
					echo '<div> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') - <b>'.($exist ? 'Exist' : 'Inserted').'</b>!<br /></div>';
					flush();
					
					}
				echo '<hr /><b>Done!</b><br />';
				######################### [ CSV Leads ] #########################
			}
		// ---------------------------------------------------------------------------------------------------------

		######################### [ Deposits ] #########################
		for ($page=1; $page<=$totalPage; $page++) {
		echo '<hr /><b>Connecting to Transactions Database Page: <u>'.$page.'</u>...</b><br />';
		$url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=CustomerDeposits&COMMAND=view&FILTER[requestTime][min]='.$scanDateFrom.'&FILTER[requestTime][max]='.$scanDateTo.'&page='.$page;
		$xml_report=doPost($url);

		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);

		foreach($xml[1] AS $xml_line) {
			$tranzExist=0;
			$db['trader_id'] = getTag('<customerId>','<\/customerId>',$xml_line);
			$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag FROM data_reg WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."' LIMIT 1"));
			if (!$chkTrader['id']) continue;
			$db['tranz_id'] = getTag('<transactionID>','<\/transactionID>',$xml_line);
			$db['type'] = strtolower(getTag('<type>','<\/type>',$xml_line));
			
			$paymentMethod = getTag('<paymentMethod>','<\/paymentMethod>',$xml_line);
			if (strtolower($paymentMethod) == "bonus") $db['type'] = 'bonus';
			
			if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") continue;
			
			$db['rdate'] = getTag('<requestTime>','<\/requestTime>',$xml_line);
			$db['amount'] = getTag('<amount>','<\/amount>',$xml_line);
			$coin = getTag('<currency>','<\/currency>',$xml_line);
			/*
			if($coin!="USD"){
				$curr = mysql_fetch_assoc(mysql_query("SELECT val from currencies WHERE fromCurr='".$coin."' AND toCurr='USD'"));
				if($curr['val']){
					echo 'In currency:';
					$db['amount'] = (round(round($db['amount']*$curr['val'])/10)*10);
				}
			}*/
			$db['amount'] = getUSD($db['amount'],$coin);
			
			/*
			if ($coin == "EUR") $db['amount'] = round($db['amount']*1.3,2);
				else if ($coin == "JPY") $db['amount'] = round($db['amount']/102,2);
				else if ($coin == "GBP") $db['amount'] = round($db['amount']*1.6,2);
			*/
			
			$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales WHERE merchant_id= '".$ww['id']."' and  trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
			if ($chkExist['id']) {
				$tranzExist = 1;
				}
			
			// Check cTag From Trader
			$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."'"));
			if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
				else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
			// Check cTag From Trader
				
			if (!ctagValid($db['ctag'])) continue;
			
								$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				}
			$db['group_id'] = $getAffiliate['group_id'];
			
			if (count($db) > 1 AND !$tranzExist) {
				// dbAdd($db,"data_sales_".strtolower($ww['name']));
				mysql_query("INSERT INTO data_sales (merchant_id, rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam) VALUES
					('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['amount']."','".$db['freeParam']."')") or die(mysql_error());
				echo '<div> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br /></div>';
				$sales_total++;
				mysql_query('UPDATE datascan_sales SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND tranz_id="'.$row['tranz_id'].'"');
				if (!in_array($db['affiliate_id'],$sentAffiliates)) if ($db['type'] == "deposit") {
					$new_ftd=mysql_num_rows(mysql_query("SELECT id FROM data_sales WHERE merchant_id= '".$ww['id']."' and affiliate_id='".$db['affiliate_id']."' AND trader_id='".$db['trader_id']."' AND type='deposit' AND rdate <= '".date("Y-m-d")."'"));
					if ($new_ftd == "1") {
						$affiliateInfo=mysql_fetch_assoc(mysql_query("SELECT id,mail,first_name,username FROM affiliates WHERE id='".$db['affiliate_id']."'"));
						if ($affiliateInfo['id'] AND $db['affiliate_id'] AND !$affiliateInfo['com_alert']) {
							
							$set->sendTo = $affiliateInfo['mail'];
							$set->sendFrom = $set->webMail;
							$set->subject = $set->webTitle.' - You\'ve made a commission!';
							$set->aff_uname = $affiliateInfo['username'];
							$set->aff_fname = $affiliateInfo['first_name'];
							
							$mailCode = mysql_fetch_assoc(mysql_query('SELECT mailCode FROM mail_templates WHERE id=-2'));
							
							sendTemplate($mailCode['mailCode'],$db['affiliate_id'],0);
							
							/*
							$set->body = 'Dear '.$affiliateInfo['first_name'].'!<br />
								<br />
								You\'ve made a commission - come check out your stats at:<br />
								<br />
								<a href="'.$set->webAddress.'/?username='.$affiliateInfo['username'].'" target="_blank">'.$set->webAddress.'</a><br />
								<br />
								<span style="font-size: 11px;"><b>Note:</b> Due to a lag between the '.$set->webTitle.' scanners and the Merchant Report Systems, there could be a delay in the appearance of the FTD date. Please check yesterday\'s FTDs as well.</span><br />
								<br />
								Best Regards,<br />
								'.$set->webTitle;
							sendMail();
							*/
							
							
							$sentAffiliates[] = $db['affiliate_id'];
							}
						}
					}
				
				
				echo file_get_contents('pixel.php?act=deposit&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&tranz='.$db['tranz_id'].'&type='.$db['type'].'&currency='.$db['coin'].'&amount='.$db['amount']);
				//fire_pixel('sale',$db['affiliate_id']);
				
				flush();
				} else {
				echo '<div> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br /></div>';
				}
			
			}
		}
		echo '<hr /><b>Done!</b><br />';
		######################### [ Deposits ] #########################

		######################### [ Withdrawal ] #########################

		for ($page=1; $page<=$totalPage; $page++) {
		echo '<hr /><b>Connecting to Withdrawal Database Page: <u>'.$page.'</u>...</b><br />';
		$url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Withdrawal&COMMAND=view&FILTER[confirmTime][min]='.$scanDateFrom.'&FILTER[confirmTime][max]='.$scanDateTo.'&page='.$page;
		$xml_report=doPost($url);

		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);

		foreach($xml[1] AS $xml_line) {
			$existWithdrawal = 0;
			$db['trader_id'] = getTag('<customerId>','<\/customerId>',$xml_line);
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
		/*	
			if ($coin == "EUR") $db['amount'] = round($db['amount']*1.3,2);
				else if ($coin == "JPY") $db['amount'] = round($db['amount']/102,2);
				else if ($coin == "GBP") $db['amount'] = round($db['amount']*1.6,2);
			*/
			/*
			if($coin!="USD"){
				$curr = mysql_fetch_assoc(mysql_query("SELECT val from currencies WHERE fromCurr='".$coin."' AND toCurr='USD'"));
				if($curr['val']){
					echo 'In currency:';
					$db['amount'] = (round(round($db['amount']*$curr['val'])/10)*10);
				}
			}*/
			$db['amount'] = getUSD($db['amount'],$coin);
			
			$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales WHERE merchant_id= '".$ww['id']."' and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
			if ($chkExist['id']) $existWithdrawal=1;
			
			// Check cTag From Trader
			$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id= '".$ww['id']."'  and trader_id='".$db['trader_id']."'"));
			if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
				else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
			// Check cTag From Trader
				
			if (!ctagValid($db['ctag'])) continue;
			
								$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				}
			$db['group_id'] = $getAffiliate['group_id'];
			
			if (count($db) > 1 AND !$existWithdrawal) {
				// dbAdd($db,"data_sales_".strtolower($ww['name']));
				mysql_query("INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam) VALUES
					('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['amount']."','".$db['freeParam']."')") or die(mysql_error());
				echo '<div>'.$ww['id'] .'['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br /></div>';
				$sales_total++;
				mysql_query('UPDATE datascan_sales SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND tranz_id="'.$row['tranz_id'].'"');
				flush();
				} else {
				echo '<div>'.$ww['id'].'['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br /></div>';
				}
			}
		}
		echo '<hr /><b>Done!</b><br />';


		######################### [ Positions Volume ] #########################

		for ($page=1; $page<=$totalPage; $page++) {
		echo '<hr /><b>Connecting to Revenue (Positions) Database Page: <u>'.$page.'</u>...</b><br />';
		$url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Positions&COMMAND=view&FILTER[date][min]='.$scanDateFrom.'&FILTER[date][max]='.$scanDateTo.'&page='.$page; // &FILTER[confirmTime][min]='.$scanDateFrom.'&FILTER[confirmTime][max]='.$scanDateTo.'
		$xml_report=doPost($url);
		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);

		foreach($xml[1] AS $xml_line) {
			$existVolume = 0;
			$db['trader_id'] = getTag('<customerId>','<\/customerId>',$xml_line);
			$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag FROM data_reg WHERE merchant_id= '".$ww['id']."'  and trader_id='".$db['trader_id']."' LIMIT 1"));
			if (!$chkTrader['id']) continue;
			$db['tranz_id'] = getTag('<id>','<\/id>',$xml_line);
			$db['type'] = 'volume';
			
			$db['rdate'] = getTag('<date>','<\/date>',$xml_line);
			$db['amount'] = getTag('<amount>','<\/amount>',$xml_line);
			$coin = getTag('<currency>','<\/currency>',$xml_line);
			$status = getTag('<status>','<\/status>',$xml_line);

			if ($status == "open") continue;
			/*
			if ($coin == "EUR") $db['amount'] = round($db['amount']*1.3,2);
				else if ($coin == "JPY") $db['amount'] = round($db['amount']/102,2);
				else if ($coin == "GBP") $db['amount'] = round($db['amount']*1.6,2);
			*/
			/*
			if($coin!="USD"){
				$curr = mysql_fetch_assoc(mysql_query("SELECT val from currencies WHERE fromCurr='".$coin."' AND toCurr='USD'"));
				if($curr['val']){
					echo 'In currency:';
					$db['amount'] = (round(round($db['amount']*$curr['val'])/10)*10);
				}
			}
			*/
			$db['amount'] = getUSD($db['amount'],$coin);
			$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales WHERE merchant_id= '".$ww['id']."'  and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
			if ($chkExist['id']) $existVolume=1;
			
			// Check cTag From Trader
			$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id= '".$ww['id']."'  and trader_id='".$db['trader_id']."'"));
			if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
				else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
			// Check cTag From Trader
				
			if (!ctagValid($db['ctag'])) continue;
			
								$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				}
			$db['group_id'] = $getAffiliate['group_id'];
			
			if (count($db) > 1 AND !$existVolume) {
				// dbAdd($db,"data_sales_".strtolower($ww['name']));
				mysql_query("INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,trader_alias,type,amount,freeParam) VALUES
					('".$ww['id']."','".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."','".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."','".$db['amount']."','".$db['freeParam']."')") or die(mysql_error());
					
				echo '<div> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br /></div>';
				$sales_total++;
				mysql_query('UPDATE datascan_sales SET sent_ia=1 WHERE merchant_id='.$row['merchant_id'].' AND tranz_id="'.$row['tranz_id'].'"');
				flush();
				} else {
				echo '<div> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br /></div>';
				}
			}
		}
		echo '<hr /><b>Done!</b><br />';
	}
	
	else if ($api_type == 'winner') {
		
		$api_url  = $merchant['APIurl'];   // including ftp url's for file type brand
		$api_user = $merchant['APIuser']; // ftp user in case of file type brand
		$api_pass = $merchant['APIpass']; // ftp pass in case of file type brand
		$api_type = $merchant['apiType'];
		require 'api/api_winnerCasino.php';
		
		/*require 'api/ProcessCasinoData.php';
		$processCasinoData = new ProcessCasinoData('delrio', $defaultAffiliateID, $merchant, $scanDateFrom, $scanDateTo);
		$processCasinoData->processSignups();
		$processCasinoData->processStats();
		$processCasinoData->processTransactions();
		$processCasinoData->processTransactions2();
		unset($processCasinoData);*/
		
	} 
	else{
		echo '<BR><BR>NO API TYPE FOR MERCHANT: '.$merchant['name'].'<BR><BR>';
	}
}

$takeMonth = @mysql_fetch_assoc(mysql_query("SELECT id FROM cron_logs WHERE month='".date("n")."' AND year='".date("Y")."' AND merchant_id='".$ww['id']."'"));
if ($takeMonth['id']) @mysql_query("UPDATE cron_logs SET lastscan='".dbDate()."',reg_total=reg_total+".$reg_total.",sales_total=sales_total+".$sales_total." WHERE id='".$takeMonth['id']."'");
	else @mysql_query("INSERT INTO cron_logs (lastscan,month,year,merchant_id,merchant_name,success,reg_total,sales_total) VALUES ('".dbDate()."','".date("n")."','".date("Y")."','".$ww['id']."','".strtolower($ww['name'])."','1','".$reg_total."','".$sales_total."')");
exit;

?>