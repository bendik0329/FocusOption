<?php
set_time_limit(0);
header("Pragma: no-cache");
header("Expires: 0");
ini_set("memory_limit","128M");
require_once('common/global.php');

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');

$debug = isset($_GET['debug']) ? $_GET['debug'] : 0;
$skipPositions = false;

$forceRunCheckSaleStatus = false;
$siteURL = 'http://partners.plusoption.com/';
$ignoreCampaign = isset($_GET['ic']) ? $_GET['ic'] : 1 ;
$runLDOCheck = false;
$ignoreAccounts = isset($_GET['ignoreAccounts']) ? $_GET['ignoreAccounts'] : false;
$DynamicTrackertActive = false;
$merchantid=isset($_GET['merchantid']) ? $_GET['merchantid'] : "" ;
$isTest=isset($_GET['isTest']) ? $_GET['isTest'] : 0 ;

function concatUrlParts($firstPart, $secondPart) {
	if (strpos($firstPart,'?')>0) {
			return $firstPart . '&'.  $secondPart;
	}
	return $firstPart . '?'.  $secondPart;
}

function shouldIgnoreCampaign ($siteid=0){
	if ($siteid==0) {
		return true;
	}
	else if ($siteid==2 || $siteid==3 || $siteid==4) {
		return false;
	}
	else {
		return true;
	}
	
}

function overrideSiteId ($siteid=0){
	if ($siteid==0) {
		return $siteid;
	}
	else if ($siteid==4) {
		return 3 ;
	}
	else if ($siteid==7) {
		return 6 ;
	}
	else	
		return $siteid;
	
}


	
	

function extractDate($date,$delimiter="") {
	$date = str_replace('pm','',strtolower($date));
	$date = trim(str_replace('am','',strtolower($date)));
	$exp = explode(' ',$date);
	
	$exp_mdate = explode($delimiter,trim($exp[1]));
	// $mdate = date("Y-m-d H:i:s", mktime(0, 0, 0, $exp_mdate[2], $exp_mdate[1], $exp_mdate[0]). ' ' . trim($exp[0]));
	$mdate = '20'.($exp_mdate[2].'-'. $exp_mdate[1].'-'. $exp_mdate[0]). ' ' . trim($exp[0]);
// die ($mdate);	
	return $mdate;
}	
		


$find = Array("\n","\t");
$replace = Array("","");
echo '<br>Starting time' . date("h:i:s") . '<br>';
$countries2isoJson = '{"Bangladesh":"BD","Belgium":"BE","Burkina Faso":"BF","Bulgaria":"BG","Bosnia and Herzegovina":"BA","Barbados":"BB","Wallis and Futuna":"WF","Saint Barthelemy":"BL","Bermuda":"BM","Brunei":"BN","Brunei Darussalam":"BN","Bolivia":"BO","Bahrain":"BH","Burundi":"BI","Benin":"BJ","Bhutan":"BT","Jamaica":"JM","Bouvet Island":"BV","Botswana":"BW","Samoa":"WS","Bonaire, Saint Eustatius and Saba ":"BQ","Brazil":"BR","Bahamas":"BS","Jersey":"JE","Belarus":"BY","Belize":"BZ","Russia":"RU","Rwanda":"RW","Serbia":"RS","East Timor":"TL","Reunion":"RE","Turkmenistan":"TM","Tajikistan":"TJ","Romania":"RO","Tokelau":"TK","Guinea-Bissau":"GW","Guam":"GU","Guatemala":"GT","South Georgia and the South Sandwich Islands":"GS","Greece":"GR","Equatorial Guinea":"GQ","Guadeloupe":"GP","Japan":"JP","Guyana":"GY","Guernsey":"GG","French Guiana":"GF","Georgia":"GE","Grenada":"GD","United Kingdom":"GB","Gabon":"GA","El Salvador":"SV","Guinea":"GN","Gambia":"GM","Greenland":"GL","Gibraltar":"GI","Ghana":"GH","Oman":"OM","Tunisia":"TN","Jordan":"JO","Croatia":"HR","Haiti":"HT","Hungary":"HU","Hong Kong":"HK","Honduras":"HN","Heard Island and McDonald Islands":"HM","Venezuela":"VE","Puerto Rico":"PR","Palestinian Territory":"PS","Palau":"PW","Portugal":"PT","Svalbard and Jan Mayen":"SJ","Paraguay":"PY","Iraq":"IQ","Panama":"PA","French Polynesia":"PF","Papua New Guinea":"PG","Peru":"PE","Pakistan":"PK","Philippines":"PH","Pitcairn":"PN","Poland":"PL","Saint Pierre and Miquelon":"PM","Zambia":"ZM","Western Sahara":"EH","Estonia":"EE","Egypt":"EG","South Africa":"ZA","Ecuador":"EC","Italy":"IT","Vietnam":"VN","viet nam":"VN","Solomon Islands":"SB","Ethiopia":"ET","Somalia":"SO","Zimbabwe":"ZW","Saudi Arabia":"SA","Spain":"ES","Eritrea":"ER","Montenegro":"ME","Moldova, Republic of":"MD","Moldova  Republic of":"MD","Moldova":"MD","Madagascar":"MG","Saint Martin":"MF","Morocco":"MA","Monaco":"MC","Uzbekistan":"UZ","Myanmar":"MM","Mali":"ML","Macao":"MO","Mongolia":"MN","Marshall Islands":"MH","Macedonia  the Former Yugoslav Republic of":"MK","Macedonia":"MK","Mauritius":"MU","Malta":"MT","Malawi":"MW","Maldives":"MV","Falkland Islands (Malvinas)":"MV","Martinique":"MQ","Northern Mariana Islands":"MP","Montserrat":"MS","Mauritania":"MR","Isle of Man":"IM","Uganda":"UG","Tanzania":"TZ","Tanzania  United Republic of":"TZ","Malaysia":"MY","Mexico":"MX","Israel":"IL","France":"FR","British Indian Ocean Territory":"IO","Saint Helena":"SH","Finland":"FI","Fiji":"FJ","Falkland Islands":"FK","Micronesia  Federated States of":"FM","Micronesia":"FM","Faroe Islands":"FO","Nicaragua":"NI","Netherlands Antilles":"NL","Netherlands":"NL","Norway":"NO","Namibia":"NA","Vanuatu":"VU","New Caledonia":"NC","Niger":"NE","Norfolk Island":"NF","Nigeria":"NG","New Zealand":"NZ","Nepal":"NP","Nauru":"NR","Niue":"NU","Cook Islands":"CK","Kosovo":"XK","Cote D\'Ivoire":"CI","Ivory Coast":"CI","Switzerland":"CH","Colombia":"CO","China":"CN","Cameroon":"CM","Chile":"CL","Cocos Islands":"CC","Canada":"CA","Congo":"CG","Republic of the Congo":"CG","Central African Republic":"CF","Democratic Republic of the Congo":"CD","Czech Republic":"CZ","Cyprus":"CY","Christmas Island":"CX","Costa Rica":"CR","Curacao":"CW","Cape Verde":"CV","Cuba":"CU","Swaziland":"SZ","Syrian Arab Republish":"SY","Syria":"SY","Sint Maarten":"SX","Kyrgyzstan":"KG","Kenya":"KE","South Sudan":"SS","Suriname":"SR","Kiribati":"KI","Cambodia":"KH","Saint Kitts and Nevis":"KN","Comoros":"KM","Sao Tome and Principe":"ST","Slovakia":"SK","South Korea":"KR","Slovenia":"SI","North Korea":"KP","Korea  Republic of":"KP","Kuwait":"KW","Senegal":"SN","San Marino":"SM","Sierra Leone":"SL","Seychelles":"SC","Kazakhstan":"KZ","Cayman Islands":"KY","Singapore":"SG","Sweden":"SE","Sudan":"SD","Dominican Republic":"DO","Dominica":"DM","Djibouti":"DJ","Denmark":"DK","virgin islands, british":"VG","Virgin Islands  British":"VG","British Virgin Islands":"VG","Germany":"DE","Yemen":"YE","Algeria":"DZ","United States":"US","Uruguay":"UY","Mayotte":"YT","United States Minor Outlying Islands":"UM","Lebanon":"LB","Saint Lucia":"LC","Lao People\'s Democratic Republic":"LA","Laos":"LA","Tuvalu":"TV","Taiwan  Province of China":"TW","Taiwan":"TW","Taiwan, Province of China":"TW","Trinidad and Tobago":"TT","Turkey":"TR","Sri Lanka":"LK","Liechtenstein":"LI","Latvia":"LV","Tonga":"TO","Lithuania":"LT","Luxembourg":"LU","Liberia":"LR","Lesotho":"LS","Thailand":"TH","French Southern Territories":"TF","Togo":"TG","Chad":"TD","Turks and Caicos Islands":"TC","Libya":"LY","Libyan Arab Jamahiriya":"LY","Vatican":"VA","Saint Vincent and the Grenadines":"VC","United Arab Emirates":"AE","Andorra":"AD","Antigua and Barbuda":"AG","Afghanistan":"AF","Anguilla":"AI","U.S. Virgin Islands":"VI","Virgin Islands  U.s.":"VI","Iceland":"IS","Iran  Islamic Republic of":"IR","Iran":"IR","Armenia":"AM","Albania":"AL","Angola":"AO","Antarctica":"AQ","American Samoa":"AS","Argentina":"AR","Australia":"AU","Austria":"AT","Aruba":"AW","India":"IN","Aland Islands":"AX","Azerbaijan":"AZ","Ireland":"IE","Indonesia":"ID","Ukraine":"UA","Qatar":"QA","Mozambique":"MZ"}';
$countriesArr = json_decode($countries2isoJson);



function overRideDynamicParameter ($freeTextParam) {
		if (isset($_GET['comment']) && !empty($_GET['comment'])) {
			return $_GET['comment'];
		}
		else
			return $freeTextParam;
}

/*
echo '{';
$counter = 0;
foreach($countriesArr as $key=>$value) {
	if($counter)
		echo ',';
	$counter++;
	echo '"'.$value.'":"'.$key.'"';
}
echo '}';
die();
*/
// if ($_GET['pass'] != "hanan") _goto();


set_time_limit(0);

function doCurlPost($url){
	

	$arrUrl  = explode('?', $url);
	
	$ch      = curl_init();
	
	$tmpparams = $arrUrl[1];
	$exp = explode('&',$tmpparams);
	
	$params = array();
	foreach ($exp as $item) {
		$itm = explode('=',$item);
		$params[$itm[0]] = $itm[1];
	}

	curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
	curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	
	$error = strtolower(getTag('<error>','<\/error>',$result));
	
	if ($error=='noresults') {
		echo '<span style="color:red">' .ucwords(getTag('<error>','<\/error>',$result)) . '</span>';
		return '';
	} else {
		return $result;
	
	}

}


/* 
		protected function overrideCtag(array $arrAffCampaignsRelations, $intDefaultAffId, $strOriginalCtag = '', $intCampaign = 0)
		{
			$strCtag = '';
			
			if (isset($arrAffCampaignsRelations[$intCampaign])) {
				$strCtag .= 'a' . $arrAffCampaignsRelations[$intCampaign];
				
				if ($this->ctagValid($strOriginalCtag)) {
					$arrOriginalCtag  = explode('-', $strOriginalCtag);
					$strCtag         .= implode('-', array_slice($arrOriginalCtag, 1));
				} else {
					$strCtag .= '-b-p';
				}
				
			} else {
				$strCtag .= $this->ctagValid($strOriginalCtag) ? $strOriginalCtag : 'a' . $intDefaultAffId . '-b-p'; 
			}
			
			return $strCtag;
		}
		
		 */
function overrideCtagByCamp ($xml_line ='' , $camp=0,$array) { 
global $ww;
$ctag='';
			
			
			if ($array[$camp]['affiliateID']) {
				$profile_id=  $array[$camp]['profileID'];
				$ctag = "a" . $array[$camp]['affiliateID'] . '-b-p' . (!empty($profile_id) ? $profile_id : "");
			}
			else{
				$tracker_param = ($ww['incomingParam']);
				if (!empty($tracker_param)) {
				$ctag = getTag('<'.$tracker_param.'>','<\/'.$tracker_param.'>',$xml_line);
				}else
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

	$parse_url=parse_url($url);

/* 	function doGet($url){
	 $ch = curl_init();
        
	
	$params = array("api_username" => "ot66d8",
			                "api_password" => "J2e57bdXQaa",
			                "api_whiteLabel" => "ot668",
			                "acceptCharset" => "UTF-8");
			
		
        //set the url, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, 'data=' . json_encode($params));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $result = curl_exec($ch);
        $result = json_decode($result);
        curl_close($ch);
	} */
		
	/* 
function doGe5told($url){
	
	$ch = curl_init();  
	
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"api_username: AffiliateכBuddies",
			"api_password: = 53be598עכגעכגעכגעכג1de84f",
			"api_whiteLabel = no1optככגכעions",
			"acceptCharset: UTF-8"
	));
	
    $output=curl_exec($ch);
	
	if($output === false){
		echo 'Curl error: ' . curl_error($ch);
	}
 
    curl_close($ch);
    return $output;
	
	
}
 */
 


 function doPost($url)
{
	$arrUrl  = explode('?', $url);
	$ch      = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	// var_dump($result);
	$error = strtolower(getTag('<error>','<\/error>',$result));
	
	if ($error=='noresults') {
		echo '<span style="color:red">' .ucwords(getTag('<error>','<\/error>',$result)) . '</span>';
		return '';
	} else {
		return $result;
	
	}
}

function dofsockPost($url){
		$parse_url=parse_url($url);
		$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
		if (!$da) {
			echo "$errstr ($errno)<br/>\n";
			echo $da;
			} else {
			$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
			$params .= "Host: ".$parse_url['host']."\r\n";
			$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$params .= "User-Agent: AB Agent\r\n";
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
	

function doPostorigin($url){
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
	
function CreateOverrideDynamicTracker ($affiliate_id=0 , $uid=0, $tracker='') {
	if ($affiliate_id>0 && $uid>0) {
			$dynamicTracker = GetOverrideDynamicTracker($affiliate_id,$uid);
			if (empty($dynamicTracker) && strlen($tracker)>0) {
				$qry = "INSERT INTO `TrackerConversion`(`affiliate_id`, `uid`, `DynamicTracker`) VALUES (". $affiliate_id . ", " . $uid . ",'" . $tracker . "');";
				$resource = mysql_query($qry);
				return $tracker;
			}
			else if ($tracker=='') {
				return -1;
			}
			
			else	{
				return ($dynamicTracker);
			}
	}
	return -1;
}

function GetOverrideDynamicTracker ($affiliate_id=0 , $uid=0) {
	if ($affiliate_id>0 && $uid>0) {
		$qry = "select  DynamicTracker from TrackerConversion where affiliate_id = " . $affiliate_id . " and uid= " . $uid;
		$resource = mysql_query($qry);
		$row = mysql_fetch_assoc($resource);
		if (!empty($row)) {
			return ($row['DynamicTracker']);
		}
	}
			return NULL;
}
	
function checkSaleStatus($mid, $camsID,$urlType='accounts'){
	global $api_url,$api_user,$api_pass,$current_Campaign,$ww,$runLDOCheck;
	$allTradersArray = array();
	$fromDate = date("Y-m-d",strtotime("-3 Months"));
	$countXML=1;
	$page = 1;
	$intProcessed=0;
	while($countXML>0 AND $timesXML<1000){
		if ($urlType=='accounts') 
			$url = concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Customer&COMMAND=view&'.($ignoreCampaign>0 ? '' :'FILTER[campaignid]='.$current_Campaign).'&FILTER[regTime][min]='.$fromDate.'&page='.$page);
		else
			$url = concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Lead&COMMAND=view&'.($ignoreCampaign>0 ? '' :'FILTER[campaignid]='.$current_Campaign).'&FILTER[regTime][min]='.$fromDate.'&page='.$page);
	
	
		$xml_report=doPost($url);
		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		echo '--------------------------------------------------------------------------------<BR>';
		echo 'UPDATING SaleStatus FOR CAMP: '.$current_Campaign.'<BR>';
		echo '--------------------------------------------------------------------------------<BR>';
		$countXML=0;
		
		if ($debug ==1) 		{ echo '<br><br><br>debug: ' ; var_dump($xml_report); }
		foreach($xml[1] AS $xml_line) {

		
			$saleStatus = getTag('<salestatus>','<\/salestatus>',$xml_line);
			if (empty($saleStatus))
				$saleStatus = getTag('<saleStatus>','<\/saleStatus>',$xml_line);
			$traderID = getTag('<id>','<\/id>',$xml_line);
			$allTradersArray[] = $traderID;
			
			
			$rdate = date("Y-m-d H:i:s", strtotime(getTag('<regTime>','<\/regTime>',$xml_line)));
			
			if($saleStatus AND $traderID){
				$exist = mysql_fetch_assoc(mysql_query('SELECT id FROM data_sales WHERE merchant_id = '.$ww['id'] .' and trader_id='.$traderID.' AND (type="deposit" OR type=1) LIMIT 0,1'));
				if(!$exist['id']){
					mysql_query('UPDATE data_reg SET saleStatus="'.$saleStatus.'" WHERE merchant_id = '.$ww['id'] .' and trader_id="'.$traderID.'"') OR die(mysql_error());
					echo 'TraderID: '.$traderID.' | saleStatus: '.$saleStatus.' | rdate: '.$rdate.'<BR>';
				}else{
					$intProcessed++;
					//echo 'TraderID: '.$traderID.' | saleStatus: '.$saleStatus.' | rdate: '.$rdate.' ------------------------ <font color="RED">ALREADY MADE A DEPOST!</font><BR>';
				}
			}
			$countXML++;
		}
		$page++;
		mysql_query('UPDATE merchants SET lastSaleStatusUpdate="'.date("Y-m-d H:i:s").'" WHERE id='.$mid) OR die(mysql_error());
		echo '--------------------------------------------------------------------------------<BR>';
	}

if ($intProcessed>0)
	echo '<span color:blue;font:size:14px;>Total Processed: ' . $intProcessed . '</span><br><br>';
else
	echo 'Total Processed: ' . $intProcessed . '<br><br>';
	
	
	
	
		if ($runLDOCheck) {
		GetLDOpositions($mid,$allTradersArray);
		}
	
}


function GetLDOpositions ($mid,$allTradersArray) {
	global $api_url,$api_user,$api_pass,$current_Campaign,$ww;
	$counter = 0;
	foreach ($allTradersArray as $trader) {
		
			$url .= "&BATCH[".$counter."][MODULE]=LDO&BATCH[".$counter."][COMMAND]=viewPositions&BATCH[".$counter."][FILTER][customerId]=" . $trader;
		
		
		$counter=$counter+1;
	}
	
	
	echo '<hr /><b>Get LDO positions </b><br />';
	$finalurl =  concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.($url));
	echo 'url: ' .$finalurl . ' <Br><Br>';
	$xml_report=doCurlPost($finalurl);
	if (strpos(strtolower('__' . $xml_report),strtolower('successfulfailednoPermissions'))>0) {
		echo 'successfulfailednoPermissions<br>';
	}
	else {
	var_dump($xml_report);
	}
	
	// die();
	
	/* 
	if (strlen($xml_report)<200 || $debug==1) {
		echo '<pre>';
		print_r($xml_report,TRUE);
		echo '</pre><br>';
		die('temporary stopped');
	}
		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		echo '--------------------------------------------------------------------------------<BR>';
		echo 'Loading LDO Positions<BR>';
		echo '--------------------------------------------------------------------------------<BR>';
		$countXML=0;
		foreach($xml[1] AS $xml_line) {

			$saleStatus = getTag('<salestatus>','<\/salestatus>',$xml_line);
			if (empty($saleStatus))
				$saleStatus = getTag('<saleStatus>','<\/saleStatus>',$xml_line);
				
			$traderID = getTag('<id>','<\/id>',$xml_line);
			$allTradersArray[] = $traderID;
			
			
			$rdate = date("Y-m-d", strtotime(getTag('<regTime>','<\/regTime>',$xml_line)));
			
			if($saleStatus AND $traderID){
				$exist = mysql_fetch_assoc(mysql_query('SELECT id FROM data_sales WHERE merchant_id = '.$ww['id'] .' and trader_id='.$traderID.' AND (type="deposit" OR type=1) LIMIT 0,1'));
				if(!$exist['id']){
					mysql_query('UPDATE data_reg SET saleStatus="'.$saleStatus.'" WHERE merchant_id = '.$ww['id'] .' and trader_id="'.$traderID.'"') OR die(mysql_error());
					echo 'TraderID: '.$traderID.' | saleStatus: '.$saleStatus.' | rdate: '.$rdate.'<BR>';
				}else{
					$intProcessed++;
					//echo 'TraderID: '.$traderID.' | saleStatus: '.$saleStatus.' | rdate: '.$rdate.' ------------------------ <font color="RED">ALREADY MADE A DEPOST!</font><BR>';
				}
			}
			$countXML++;
		}
		$page++;
		 */
		
	

}
	
/**
 * Check if given amount of hours has past.
 * 
 * @param  string $strDateTimeGiven
 * @param  int    $intHours
 * @return bool
 */
function isTimeHasPast($strDateTimeGiven, $intHours)
{
	$arrDateTime   = explode(' ', $strDateTimeGiven);
	$arrDate       = explode('-', $arrDateTime[0]);
	$arrTime       = explode('-', $arrDateTime[1]);
	$dateTimeGiven = new \DateTime($arrDate[0] . '-' . $arrDate[1] . '-' . $arrDate[2]);
	$dateTimeGiven->setTime($arrTime[0], $arrTime[1], $arrTime[2]);
	$dateTimeGiven->modify('+' . $intHours . ' hour' . ($intHours > 1 ? 's' : ''));
	$dateTimeNow = new \DateTime();
	return $dateTimeNow > $dateTimeGiven;
}
function checkCurrencies($force=true){
	global $set,$force;
	$currentTime = new DateTime();
	$startTime = new DateTime('01:00');
	$endTime = new DateTime('04:00');
	// if (($currentTime >= $startTime && $currentTime <= $endTime) OR ($_REQUEST['currency'])) {
	if ((($currentTime >= $startTime && $currentTime <= $endTime) OR ($_REQUEST['currency'])) OR $force) {
		$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, "http://".$set->http_host."/getCurrency.php"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch); 
		echo $output;
	}else{
		// die ('force: ' . var_dump($force));
		echo '<BR>Currencies will be update in other time.<BR>';
	}
}



if (!empty($merchantid)) 
	$wwList = mysql_query("select * from merchants where id = " . $merchantid);
else
	$wwList = mysql_query("select * from merchants where not APIurl =''");

while ($ww  =  mysql_fetch_assoc($wwList)) {
	
	
	$ignoreCampaign =  shouldIgnoreCampaign($ww['id']);
	$defaultAffiliateID = $ww['defaultAffiliateID'];

$defaultBtag='a'.$defaultAffiliateID.'-b-p-f'.$ww['id'];


$query = 'select * from affiliates_campaigns_relations where merchantid ='.$ww['id'];
$campsQ = mysql_query($query);
$autoRelate_campaignIDs = Array();

while($row = mysql_fetch_assoc($campsQ)){
		$autoRelate_campaignIDs[$row['campID']]= $row;
		// array_push($campaignIDs, $row);
}

	
	
echo '<span style="color:orange;font-size:18px;">Proccessing the brand: ' . strtoupper($ww['name']) . '   ('.$ww['id'].')</span><br>';
$api_url = $ww['APIurl'] ;
$api_user =  $ww['APIuser'];
$api_pass = $ww['APIpass'];
$api_label =  $ww['name'];
$api_whiteLabel =  $ww['name'];


if (!empty($isTest)) {
$campaignIDs = Array(
	"0" => $_GET[isTest]
);
} else { 
if (true) {
$campaignIDs = explode(',',$ww['campaignid']);
}
/* else  {

// new method - for multiple campaiogns
$excludeCampaignIDs = Array(
"1" => "107",
"2" => "22",
"3" => "40",
"4" => "38",
"5" => "37",
"6" => "36",
"7" => "28",
"8" => "15",
"9" => "23",
"10" => "17"
);

$campaignIDs = Array();

	for ($x = 2; $x <= 250; $x++) {
		if (!in_array($x,$excludeCampaignIDs)) {
			//echo "The number is: $x <br>";
			array_push($campaignIDs,$x);
		}
	} 

} */
}
$ww['id'] = overrideSiteId($ww['id']);


$sql = 'SELECT MAX(`lastUpdate`) AS last_update FROM `exchange_rates`;';
$arrLastUpdate = mysql_fetch_assoc(mysql_query($sql));
if (isTimeHasPast($arrLastUpdate['last_update'], 12)) {
	$force=true;
	checkCurrencies($force);
}




echo '<style type="text/css">html,body { font-size: 11px; font-family: Tahoma; } </style>';


if ($_GET['m_date']) {
	$exp_mdate=explode("-",$_GET['m_date']);
	if ($_GET['monthly']) {
		$scanDateFrom = date("Y-m-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-m-01", strtotime("+1 Month",strtotime($scanDateFrom)));//.' 23:59:59';
		$totalPage = 30000000;
		} else if ($_GET['yearly']) {
		$scanDateFrom = date("Y-01-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-01-01", strtotime("+1 Year",strtotime($scanDateFrom)));//.' 23:59:59';
		$totalPage = 3000000;
		} else {
		$scanDateFrom = date("Y-m-d", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));//.' 23:59:59';
		}
	} else {
	$scanDateFrom = date("Y-m-d", strtotime("-1 Day"));//.' 00:00:00';
	$scanDateTo = date("Y-m-d", strtotime("+1 Day"));//.' 00:00:00';
	//$scanDateTo = date("Y-m-d");//.' 23:59:59';
	}
if (!$totalPage) $totalPage = 200;

if ($forceRunCheckSaleStatus) {
$toCheckSaleStatus = (date("Y-m-d H:i:s",strtotime("+1 Hours"))>$ww['lastSaleStatusUpdate']);
}
else {
$toCheckSaleStatus = (date("Y-m-d H:i:s",strtotime("-8 Hours"))>$ww['lastSaleStatusUpdate']);
}
//die ('tocheck: ' . $ww['lastSaleStatusUpdate']);




echo 'From: <u>'.$scanDateFrom.'</u> To: <u>'.$scanDateTo.'</u>';

$campscount = 0;

//var_dump($campaignIDs);
//die ('done');

	foreach ($campaignIDs as $key => $value) {
	//for ($camsID=0; $camsID < count($campaignIDs); $camsID++) {
		$current_Campaign = $value;
			echo '<span style="color:green"><br>Campaign:  '  .$current_Campaign . '<br></span>';
			
		if (!$current_Campaign) {
			continue;
		}
		
		
		if($toCheckSaleStatus){
			echo '<hr /><b>About to run checkSaleStatus <span style="color:blue">Page: <u>'.$page.'</u>...</span></b><br />';
			checkSaleStatus($ww['id'],$camsID,'accounts');
			checkSaleStatus($ww['id'],$camsID,'lead');
		}
			
		$campscount++;
		 if($campscount % 4 ==0) {
			 usleep(500000);
		 }
		 $totalRowsFoundSoFar = 0;
		 if (!$ignoreAccounts)
		for ($page=1; $page<=$totalPage; $page++)	{
			
			
		$totalRowsFoundThisPage = 0;
		echo '<hr /><b>Connecting to Customers\'s & Database (Campaign ID:'.$current_Campaign.') <span style="color:blue">Page: <u>'.$page.'</u>...</span></b><br />';
		
		
		
		
		// $url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Customer&COMMAND=view&api_whiteLabel='.$api_label.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&FILTER[regTime][min]='.$scanDateFrom.'&FILTER[regTime][max]='.$scanDateTo.'&page='.$page;
		$url = concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Customer&COMMAND=view'.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&FILTER[regTime][min]='.$scanDateFrom.'&FILTER[regTime][max]='.$scanDateTo.'&page='.$page);
		echo 'url : ' . $url . '<br>';
		$intProcessed=0;
		$xml_report=doCurlPost($url);
		// $xml_report=doPost($url);
if (strlen($xml_report)<200 || $debug==1) {
		echo '<pre>';
		print_r($xml_report,TRUE);
		echo '</pre><br>';
	}
	
		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
			if ($debug ==1) 		{ echo '<br>debug: ' ; var_dump($xml_report); }
		foreach($xml[1] AS $xml_line) {
			
		
		$totalRowsFoundThisPage++;
			$arrXmlTest[] = $xml_line;
			unset($db);
			$exist = 0;
			
			$db['email'] = getTag('<email>','<\/email>',$xml_line);
			$db['trader_id'] = getTag('<id>','<\/id>',$xml_line);
			$db['saleStatus'] = getTag('<saleStatus>','<\/saleStatus>',$xml_line);
			$db['firstname'] = str_replace(Array("\\","'","`"),Array("","",""),getTag('<FirstName>','<\/FirstName>',$xml_line));
			$db['lastname'] = str_replace(Array("\\","'","`"),Array("","",""),getTag('<LastName>','<\/LastName>',$xml_line));
			//echo $db['firstname'].' '.$db['lastname'].'<BR>';
			/*
			if($db['country'] AND isset($countriesArr->$db['country'])){
				echo $db['country'].'->'.$countriesArr->$db['country'].'<BR>';
				$db['country'] = $countriesArr->$db['country'];
				//die($db['country']);
				echo 'UPDATE data_reg_no1options SET country="'.$db['country'].'" WHERE trader_id="'.$db['trader_id'].'" AND country="" OR country="Any"<BR><BR>';
				mysql_query('UPDATE data_reg_no1options SET country="'.$db['country'].'" WHERE trader_id="'.$db['trader_id'].'" AND country="" OR country="Any"');
			}
			
			continue;
			*/
			$db['rdate'] = date("Y-m-d H:i:s", strtotime(getTag('<regTime>','<\/regTime>',$xml_line)));
			
			$campaign_id_from_spot = getTag('<campaignId>','<\/campaignId>',$xml_line);
			$db['ctag'] = overrideCtagByCamp($xml_line,$campaign_id_from_spot,$autoRelate_campaignIDs);
			
			if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;
			
			 // echo ($db['ctag']).'<br>';
			
			$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id,type FROM data_reg WHERE merchant_id = " . $ww['id'] . " AND trader_id='".$db['trader_id']."'"));
			
			if ($chkDouble['id']) {
				if ($chkDouble['type'] == "lead") {
					$db['id'] = $chkDouble['id'];
					$db['type'] = "real";
				} else {
					$exist=1;
				}
			}
			
			
			
					$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
				
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					$db['freeParam']= overRideDynamicParameter($db['freeParam']);
			
			$countryFound = str_replace(Array("\\","'","`"),Array("","",""),getTag('<Country>','<\/Country>',$xml_line));
				$db['country'] = $countriesArr->$countryFound;
				if (empty($db['country'])) {
					foreach ($countriesArr as $key=>$cntry) {
							if (strtolower($countryFound)== strtolower($key))
								$db['country']=$cntry;
								break;
					}
				}
			if(strtolower($db['country'])=='any' OR strtolower($db['country'])==''){
				$gc=$ctagArray['country'];
				// $gc = getTag('<Country>','<\/Country>',$xml_line);
				$db['country'] = str_replace(Array("\\","'","`"),Array("","",""),$gc);
				if (!empty($db['country']))
				$foundCountry = $countriesArr->$db['country'];
			
				if (empty($foundCountry)) {
					foreach ($countriesArr as $key=>$cntry) {
							if (strtolower($countryFound)== strtolower($key))
								$db['country']=$cntry;
								break;
					}
				}
				else {
					$db['country'] = $foundCountry;
				}
				
			}
			
			
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				}
			$db['group_id'] = $getAffiliate['group_id'];
			
			$db['trader_alias'] = str_replace(Array("\\","'","`"),Array("","",""),getTag('<FirstName>','<\/FirstName>',$xml_line).' '.getTag('<LastName>','<\/LastName>',$xml_line));
			
			$type = getTag('<isDemo>','<\/isDemo>',$xml_line);
			
			
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
				if (!$exist)
					if (!$db['id']) {
						/* echo '<pre>';
						print_r($db);
						die(); */
				mysql_query("INSERT INTO data_reg(merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,
									country,trader_id,trader_alias,type,freeParam, campaign_id,uid,email,saleStatus) 
									VALUES
							(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
							'".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
							'".$db['freeParam']."', '" . $campaign_id_from_spot . "', '" . $db['uid'] . "', '" . $db['email'] . "', '" . $db['saleStatus'] . "')") or die(mysql_error());
						$status = 'Inserted!';
						$reg_total++;
						
						
						
						/*die("INSERT INTO data_reg(merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,
									country,trader_id,trader_alias,type,freeParam, campaign_id) 
									VALUES
							(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
							'".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
							'".$db['freeParam']."', '" . $current_Campaign . "')");*/
						
					$subid =  $DynamicTrackertActive  ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
					$pixelurl = $siteURL. 'pixel.php?act=account&ctag='.$db['ctag'].'&merchant_id='.$ww['id'].'&trader_id='.$db['trader_id'].'&trader_alias='. str_replace(' ','%20',$db['trader_alias']) . '&subid=' . $subid;
							$pixelContent  = file_get_contents($pixelurl);
							if (strlen($pixelContent)>0) {
								echo 'Firing Account Pixel, Affiliate_ID = ' . $db['affiliate_id'].' -- '.$pixelurl.'<br>';
								echo $pixelContent;
							}
						
						// $getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$db['affiliate_id']."' AND valid='1' AND type='account'"));
						// if ($getPixel['id']) if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) {
							// doPost (str_replace('&#39;','"',$getPixel['pixelCode']));
							// echo 'Pixel Fired (dopost) from the system for affiliate_id ' . $db['affiliate_id'];
						// }
						// else
							// echo (str_replace('&#39;','"',$getPixel['pixelCode']));
							// echo 'Pixel Fired (echo) from the system for affiliate_id ' . $db['affiliate_id'];
						} else {
						mysql_query("UPDATE data_reg SET type='".$db['type']."' WHERE merchant_id = ".$ww['id'] ." and id='".$db['id']."'");
						$status = 'Updated Customer ('.$db['type'].')!';
						}
				if (!$exist) {
				echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') - <b>'.($exist ? 'Exist' : $status).'</b>!<br />';
				}
				$intProcessed++;
				flush();
				}
			}
			
		
			
		//die(print_r($arrXmlTest));
		//die(print_r($arrXmlTest));
		if ($intProcessed>0)
		echo '<span color:blue;font:size:14px;>Total Processed: ' . $intProcessed . '</span><br><br>';
	else
		echo 'Total Processed: ' . $intProcessed . '<br>';

		echo '<hr /><b>Done!</b><br />';
		######################### [ CSV REG ] #########################
		
		######################### [ CSV Leads ] #########################

		// $url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Lead&COMMAND=view&api_whiteLabel='.$api_whiteLabel.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&FILTER[regTime][min]='.$scanDateFrom.'&FILTER[regTime][max]='.$scanDateTo.'&page='.$page;
		$url = concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Lead&COMMAND=view'.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&FILTER[regTime][min]='.$scanDateFrom.'&FILTER[regTime][max]='.$scanDateTo.'&page='.$page);
		echo 'URL: ' . $url .'<br>';
		$xml_report=doPost($url);
	if (strlen($xml_report)<200 || $debug==1) {
		echo '<pre>';
		print_r($xml_report,TRUE);
		echo '</pre><br>';
	}
		$intProcessed=0;
		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);

		echo '<hr /><b>Connecting to <span style="color:blue">Lead\'s</span> Database (Campaign ID:'.$current_Campaign.') Page: <u>'.$page.'</u>...</b><br />';
			if ($debug ==1) 		{ echo '<br>debug: ' ; var_dump($xml_report); }
		foreach($xml[1] AS $xml_line) {
			$totalRowsFoundThisPage++;
			unset($db);
			$exist = 0;
			$db['trader_id'] = getTag('<id>','<\/id>',$xml_line);
			
			//$TransactionDate = explode(" ",getTag('<regTime>','<\/regTime>',$xml_line));
			//$TransactionDate = explode("/",$TransactionDate[2]);
			//$db['rdate'] = '20'.$TransactionDate[2].'-'.$TransactionDate[1].'-'.$TransactionDate[0];
			$rdate = (getTag('<regTime>','<\/regTime>',$xml_line));
			$rdate = extractDate($rdate,'/');
			
			$db['rdate'] = ($rdate);
			
// 			$db['rdate'] = date("Y-m-d", strtotime(getTag('<regTime>','<\/regTime>',$xml_line)));
//			if (!$TransactionDate OR $TransactionDate == "1970-01-01") $TransactionDate = dbDate();



			$campaign_id_from_spot = getTag('<campaignId>','<\/campaignId>',$xml_line);

			
			$db['ctag'] = overrideCtagByCamp($xml_line,$campaign_id_from_spot,$autoRelate_campaignIDs);
			
			
			if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;

			$db['trader_alias'] = getTag('<FirstName>','<\/FirstName>',$xml_line).' '.getTag('<LastName>','<\/LastName>',$xml_line);
			
			
			$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id FROM data_reg WHERE merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."' AND type='lead'"));
			
			
			
			if ($chkDouble['id']) $exist = 1;
			
					$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					$db['freeParam']= overRideDynamicParameter($db['freeParam']);
			
			
			$countryFound = str_replace(Array("\\","'","`"),Array("","",""),getTag('<Country>','<\/Country>',$xml_line));
				$db['country'] = $countriesArr->$countryFound;
				if (empty($db['country'])) {
					foreach ($countriesArr as $key=>$cntry) {
							if (strtolower($countryFound)== strtolower($key))
								$db['country']=$cntry;
								break;
					}
				}
				
				
						$countryFound = str_replace(Array("\\","'","`"),Array("","",""),getTag('<Country>','<\/Country>',$xml_line));
				$db['country'] = $countriesArr->$countryFound;
				if (empty($db['country'])) {
					foreach ($countriesArr as $key=>$cntry) {
							if (strtolower($countryFound)== strtolower($key))
								$db['country']=$cntry;
								break;
					}
				}
			if(strtolower($db['country'])=='any' OR strtolower($db['country'])==''){
				$gc=$ctagArray['country'];
				// $gc = getTag('<Country>','<\/Country>',$xml_line);
				$db['country'] = str_replace(Array("\\","'","`"),Array("","",""),$gc);
				if (!empty($db['country']))
				$foundCountry = $countriesArr->$db['country'];
			
				if (empty($foundCountry)) {
					foreach ($countriesArr as $key=>$cntry) {
							if (strtolower($countryFound)== strtolower($key))
								$db['country']=$cntry;
								break;
					}
				}
				else {
					$db['country'] = $foundCountry;
				}
			}
				
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				}
			$db['group_id'] = $getAffiliate['group_id'];

			$db['type'] = 'lead';
			if (!$exist) 
				{
			//	if (count($db) > 1) 
			{ 
				
				$qry = "INSERT INTO data_reg (merchant_id , rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,
							trader_id,trader_alias,type,freeParam, campaign_id) 
							VALUES
							(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
							'".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".mysql_real_escape_string($db['trader_alias'])."','".$db['type']."',
							'".$db['freeParam']."', '" . $campaign_id_from_spot . "')";
				// echo $qry . '<br>';
				mysql_query($qry) or die(mysql_error());
				
					$subid =  $DynamicTrackertActive  ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
					$pixelurl = $siteURL. 'pixel.php?act=lead&ctag='.$db['ctag'].'&merchant_id='.$ww['id'].'&trader_id='.$db['trader_id'].'&trader_alias='. str_replace(' ','%20',$db['trader_alias']) . '&subid=' . $subid;
					if ($debug==1)
						echo '<br>'.$pixelurl.'<br>';
					
							$pixelContent  = file_get_contents($pixelurl);
							if (strlen($pixelContent)>0) {
								echo 'Firing Account Pixel, Affiliate_ID = ' . $db['affiliate_id'].' -- '.$pixelurl.'<br>';
								echo $pixelContent;
							}
				}
							
							
				
				$reg_total++;
				
				// $getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$db['affiliate_id']."' AND valid='1' AND type='account'"));
				// if ($getPixel['id']) if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) {
					// doPost (str_replace('&#39;','"',$getPixel['pixelCode']));
					// echo 'Pixel Fired (post) from the system for affiliate_id ' . $db['affiliate_id'];
				// }
				// else {
					// echo (str_replace('&#39;','"',$getPixel['pixelCode']));
				// echo 'Pixel Fired (echo) from the system for affiliate_id ' . $db['affiliate_id'];
				// }
				}
			if (!$exist)
			echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') - <b>'.($exist ? 'Exist' : 'Inserted').'</b>!<br />';
			$intProcessed++;
			flush();
		
		// var_dump($xml_line);
		// die();
		
			
			
			}
			
			
			
			if ($totalRowsFoundThisPage==0)
				break;
				
				
				/* if ($totalRowsFoundThisPage>$totalRowsFoundSoFar)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else
				break;
			 */
			
		}
		echo 'Total Processed: ' . $intProcessed . '<br>';
		echo '<hr /><b>Done!</b><br />';
		######################### [ CSV Leads ] #########################
		
		######################### [ CSV PNL ] #########################
		/* if (!$_GET['m_date']) {
			$url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Customer&COMMAND=view&FILTER[campaignid]='.$current_Campaign.'&page='.$page;
			$xml_report=doPost($url);
			preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
			foreach($xml[1] AS $xml_line) {
				unset($takeGet);
				$TraderID = getTag('<id>','<\/id>',$xml_line);
				$getReg = mysql_fetch_assoc(mysql_query("SELECT id FROM reg_gtoptions WHERE trader_id='".$TraderID."'"));
				if (!$getReg['id']) continue;
				$TransactionDate = getTag('<regTime>','<\/regTime>',$xml_line);
				$subCamp = getTag('<subCampaignId>','<\/subCampaignId>',$xml_line);
				$url2 = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=SubCampaign&COMMAND=view&FILTER[id]='.$subCamp;
				$campID=doPost($url2);
				$bTag = getTag('<param>','<\/param>',$campID);
				$pnl = round(getTag('<pnl>','<\/pnl>',$xml_line),2);
				$currency = getTag('<currency>','<\/currency>',$xml_line);

				// Check the Revenue Share
				$takeGet = mysql_fetch_assoc(mysql_query("SELECT id,value,oldPNL FROM sales_gtoptions WHERE trader_id='".$TraderID."' AND type='revenue' ORDER BY id DESC LIMIT 1"));
				if ($takeGet['id'] AND $takeGet['oldPNL'] == $pnl) continue;
					else $newPnl = round($pnl-($takeGet['oldPNL']),2);
				$newPnl = round($newPnl*(-1),2);
				// Check the Revenue Share
				if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) $bTag = $defaultBtag;
				// BTag Validator
				if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) {
					setLog('BTag not valid[PNL|'.$bTag.'|'.$TransactionDate.'|'.$TraderID.']',9,'red');
					continue;
					}
				// BTag Validator
				
				if ($pnl > 0 OR $pnl < 0) {
					$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id FROM sales_gtoptions WHERE rdate='".date("Y-m-d",strtotime("-1 Day"))."' AND trader_id='".$TraderID."' AND type='revenue'"));
					if (!$chkDouble['id']) {
						mysql_query("INSERT INTO sales_gtoptions (rdate,tranz_id,btag,trader_id,type,value,oldPNL,coin,status,siteID) VALUES ('".date("Y-m-d",strtotime("-1 Day"))."','0','".$bTag."','".$TraderID."','revenue','".$newPnl."','".$pnl."','".$currency."','approved','".getSiteID($bTag)."')");
						$realStatus = 'inserted';
						} else $realStatus = 'exist ['.$chkDouble['id'].']';
					echo 'Trader ID: <b>'.$TraderID.'</b> | BTag: <b>'.$bTag.'</b> | Type: <b>PNL</b> | Value: <b>'.$pnl.$currency.'</b> - '.$realStatus.'!<br />';
					}
				
				flush();
				}
			echo '<hr /><b>Done!</b><br />';
			} */
		######################### [ CSV PNL ] #########################
		
	
// ---------------------------------------------------------------------------------------------------------

######################### [ Deposits ] #########################
for ($page=1; $page<=$totalPage; $page++) {
	$totalRowsFoundThisPage=0;
				
echo '<hr /><b>Connecting to <span style="color:blue">Transactions</span> Database Page: <u>'.$page.'</u>...</b><br />';
// $url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=CustomerDeposits&COMMAND=view'.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&api_whiteLabel='.$api_whiteLabel.'&FILTER[requestTime][min]='.$scanDateFrom.'&FILTER[requestTime][max]='.$scanDateTo.'&page='.$page;
$url = concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=CustomerDeposits&COMMAND=view'.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&FILTER[requestTime][min]='.$scanDateFrom.'&FILTER[requestTime][max]='.$scanDateTo.'&page='.$page);
echo $url.'<br>';
// $url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=CustomerDeposits&COMMAND=view&api_whiteLabel='.$api_whiteLabel.'&FILTER[requestTime][min]='.$scanDateFrom.'&FILTER[requestTime][max]='.$scanDateTo.'&page='.$page;
$intProcessed=0;
$xml_report=doPost($url);
if (strlen($xml_report)<200 || $debug==1) {
		echo '<pre>';
		print_r($xml_report,TRUE);

		echo '</pre><br>';
	}
	

preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
if ($debug ==1) 		{ echo '<br>debug: ' ; var_dump($xml_report); }

foreach($xml[1] AS $xml_line) {
$totalRowsFoundThisPage++;
		
if ($isTest>0 || $debug==1) {
	var_dump($xml_line);
	echo '<br>';
}
	
	$tranzExist=0;
	$db['trader_id'] = getTag('<customerId>','<\/customerId>',$xml_line);
	$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag,trader_id,merchant_id FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and  trader_id='".$db['trader_id']."' LIMIT 1"));

	
	if (!$chkTrader['id']) continue;
	$db['tranz_id'] = getTag('<transactionID>','<\/transactionID>',$xml_line);
	if (strlen( $db['tranz_id'])>80) {
		echo '<span style="color:red;font-size:22px;">Is this a real tranz id or what ....  <br>' . $db['tranz_id'] . '</span>';
	}
	$db['type'] = strtolower(getTag('<type>','<\/type>',$xml_line));
	
	$paymentMethod = getTag('<paymentMethod>','<\/paymentMethod>',$xml_line);
	if (strtolower($paymentMethod) == "bonus") $db['type'] = 'bonus';
	
	if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") continue;
	
	$db['rdate'] = getTag('<requestTime>','<\/requestTime>',$xml_line);
	$coin = getTag('<currency>','<\/currency>',$xml_line);
		
		$db['amount'] = getTag('<amountUSD>','<\/amountUSD>',$xml_line);
		// var_dump($xml_line);
			// die($db['amount']);
			
	// echo 'amountusd: ' . $db['amount'] .'<br>';		
	if (empty($db['amount'])) {
		$db['amount'] = getTag('<amount>','<\/amount>',$xml_line);
		$db['amount'] = getUSD($db['amount'],$coin);
	}
	// echo '<br>';
	// var_dump($xml_line);
	// echo '<br>';
	// echo 'amount: ' . $db['amount'] .'<br><br>';		
	
	
	
	$status = getTag('<status>','<\/status>',$xml_line);
	if ($debug==1) {
	echo 'status: ' . $status . '<br>';
	echo strtolower($db['type']) . '<br><br>';
	
	}
	
	if (strtolower($db['type'])=='bonus') {
		/* if (strtolower($status)=='deleted') {
			$db['amount']=$db['amount']*-1;
		}
		else */
			if (strtolower($status)=='approved') {
			
		}
		else {
			continue;
		}
	}
	else {
		if (strtolower($status)!='approved') {
			continue;
		}
	}
	
	
	/* if ($db['trader_id']=='66502') {
			die('greger');
	}	
	 */
	
	
	$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
	if ($chkExist['id']) {
		$tranzExist = 1;
		}
	
	// Check cTag From Trader
	$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag,ftdamount,initialftdtranzid FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."'"));
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
					$db['freeParam']= overRideDynamicParameter($db['freeParam']);
	
	$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
	if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
		$db['affiliate_id'] = $defaultAffiliateID;
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		}
	$db['group_id'] = $getAffiliate['group_id'];
	
	if (count($db) > 1 AND !$tranzExist) {
		// dbAdd($db,"data_sales_".strtolower($ww['name']));
		$insertQry = "INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,
			trader_id,trader_alias,type,amount,freeParam, campaign_id) 
			VALUES
			(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
			'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
			'".$db['amount']."','".$db['freeParam']."', '" . $current_Campaign . "')";
			/* echo $insertQry . '<br>';
			var_dump($xml_line);
			die(); */
		mysql_query($insertQry) or die(mysql_error());
			
		
		if ($traderInfo['ftdamount']==0 && $db['type']=='deposit' && $traderInfo['initialftdtranzid']=='') {
		
				$GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from data_sales where 
										trader_id = " . $chkTrader['trader_id'] .  " and merchant_id = " . $chkTrader['merchant_id'] . " and type='deposit'  order by rdate limit 0,1";
									
						$GetFTDforTrader =mysql_fetch_assoc( mysql_query($GetFTDforTraderQuery));
						if (!empty($GetFTDforTrader)) {
								$UpdateFTDforTrader = "update data_reg set  ftdamount = " .$GetFTDforTrader['amount']." , initialftdtranzid = '"
								.$GetFTDforTrader['tranz_id']."' , initialftddate = '" .$GetFTDforTrader['rdate'] ."' where trader_id= ".$chkTrader['trader_id']. "  
								and merchant_id = " . $chkTrader['merchant_id'];
								
								echo 'New FTD record added to Data_Reg, TraderID: ' . $chkTrader['trader_id'].'<br>';
								mysql_query($UpdateFTDforTrader);
						}
		
		}
		
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
		$sales_total++;
		
		// $getPixel = mysql_fetch_assoc(mysql_query("SELECT * FROM pixel_monitor WHERE affiliate_id='".$db['affiliate_id']."' AND valid='1' AND type='sale'"));
		// if ($getPixel['id']) if (filter_var($getPixel['pixelCode'], FILTER_VALIDATE_URL)) {
			// doPost (str_replace('&#39;','"',$getPixel['pixelCode']));
			// echo 'Pixel Fired (post) from the system for affiliate_id ' . $db['affiliate_id'];
		// }
		// else {
			// echo (str_replace('&#39;','"',$getPixel['pixelCode']));
			// echo 'Pixel Fired (echo) from the system for affiliate_id ' . $db['affiliate_id'];
		// }
			$subid =  $DynamicTrackertActive ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
			$pixelurl = $siteURL. 'pixel.php?act=deposit&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&merchant_id='.$ww['id'].'&tranz='.$db['tranz_id'].'&type='.$db['type'].'&currency='.$db['coin'].'&amount='.$db['amount'].'&subid='.$subid;
			$pixelContent  = file_get_contents($pixelurl);
			if (strlen($pixelContent)>0) {
				echo 'Firing Sale Pixel, Affiliate_ID = ' . $db['affiliate_id'].' -- '.$pixelurl.'<br>';
				echo $pixelContent;
			}
							
							
		
		flush();
	} else {
		if ($isTest >0 || $debug==1) 
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
	}
	$intProcessed++;
	if ($intProcessed % 100 == 0) {
		echo '... , ';
	}
	// $totalRowsFoundThisPage++;
	}
	
	
	
		if ($totalRowsFoundThisPage==0)
				break;
			
	/* if ($totalRowsFoundThisPage>$totalRowsFoundSoFar)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else
				break; */
	
if ($intProcessed>0)
	echo 'Total processed so far: ' . $intProcessed . '<br>';
	}
	
echo '<hr /><b>Done!</b><br />';
######################### [ Deposits ] #########################

######################### [ Withdrawal ] #########################

for ($page=1; $page<=$totalPage; $page++) {
	
		$totalRowsFoundThisPage=0;
				
echo '<hr /><b>Connecting to <span style="color:blue">Withdrawal</span> Database Page: <u>'.$page.'</u>...</b><br />';
// $url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Withdrawal&COMMAND=view'.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&api_whiteLabel='.$api_whiteLabel.'&FILTER[confirmTime][min]='.$scanDateFrom.'&FILTER[confirmTime][max]='.$scanDateTo.'&page='.$page;
$url = concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Withdrawal&COMMAND=view'.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&FILTER[confirmTime][min]='.$scanDateFrom.'&FILTER[confirmTime][max]='.$scanDateTo.'&page='.$page);
echo 'URL: ' . $url .'<br>';
$intProcessed=0;
$xml_report=doPost($url);
if (strlen($xml_report)<200 || $debug==1) {
		echo '<pre>';
		print_r($xml_report,TRUE);
		echo '</pre><br>';
	}
	

preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
	if ($debug ==1) 		{ echo '<br>debug: ' ; var_dump($xml_report); }

foreach($xml[1] AS $xml_line) {
	
			$totalRowsFoundThisPage++;
				
	$existWithdrawal = 0;
	$db['trader_id'] = getTag('<customerId>','<\/customerId>',$xml_line);
	$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag FROM data_reg WHERE merchant_id =" .$ww['id']. " and trader_id='".$db['trader_id']."' LIMIT 1"));
	if (!$chkTrader['id']) continue;
	$db['tranz_id'] = getTag('<id>','<\/id>',$xml_line);
	$db['type'] = 'withdrawal';
	
	$db['rdate'] = getTag('<confirmTime>','<\/confirmTime>',$xml_line);



	
	$status = getTag('<status>','<\/status>',$xml_line);
	$paymentMethod = getTag('<paymentMethod>','<\/paymentMethod>',$xml_line);
	if (strtolower($paymentMethod) == "chargeback") $db['type'] = 'chargeback';
	if (strtolower($paymentMethod) == "bonus") $db['type'] = 'bonus';
	
	if ($status != "approved") continue;
	

	 		$coin = getTag('<currency>','<\/currency>',$xml_line);
		$db['amount'] = getTag('<amountUSD>','<\/amountUSD>',$xml_line);
			if (empty($db['amount'])) {
		$db['amount'] = getTag('<amount>','<\/amount>',$xml_line);
		$db['amount'] = getUSD($db['amount'],$coin);
	}
		
		
		if (strtolower($paymentMethod) == "bonus") {
			$db['amount']= $db['amount']*-1;
		}
		
		
		
		
	$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales WHERE merchant_id =" .$ww['id']. " and  trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
	if ($chkExist['id']) $existWithdrawal=1;
	
	// Check cTag From Trader
	$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id =" .$ww['id']. " and trader_id='".$db['trader_id']."'"));
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
					$db['freeParam']= overRideDynamicParameter($db['freeParam']);
	
	$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
	if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
		$db['affiliate_id'] = $defaultAffiliateID;
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		}
	$db['group_id'] = $getAffiliate['group_id'];
	
	if (count($db) > 1 AND !$existWithdrawal) {
		// dbAdd($db,"data_sales_".strtolower($ww['name']));
		mysql_query("INSERT INTO data_sales (merchant_id, rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,
					trader_id,trader_alias,type,amount,freeParam, campaign_id) 
					VALUES
					(" . $ww['id'] . ", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
					'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
					'".$db['amount']."','".$db['freeParam']."', '" . $current_Campaign . "')") or die(mysql_error());
					
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
		$sales_total++;
		flush();
		} else {
		if ($isTest>0 || $debug==1) 
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br />';
		}
		 $intProcessed++;
		 
		 $totalRowsFoundThisPage++;
	}
	
/* 	if ($totalRowsFoundThisPage>$totalRowsFoundSoFar)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else
				break; */
			
				if ($totalRowsFoundThisPage==0)
				break;
			
			
	
	if ($intProcessed>0)
	echo 'Total processed so far: ' . $intProcessed . '<br>';
}
echo 'Total Processed: ' . $intProcessed . '<br>';
echo '<hr /><b>Done!</b><br />';

	}

	
	
		
	######################### [ Positions Volume ] #########################
$totalRowsFoundSoFar=0;
$emptyPagesCounter=0;
	if (!$skipPositions)
	for ($page=1; $page<=($totalPage+50); $page++) {
		
		$totalRowsFoundThisPage=0;
		
//		break; //////////////////////////////////////////////////////////////
		
		$positioncount++;
		 if($positioncount % 4 ==0) {
			 usleep(1000000);
		 }
			 
			 
			 
			 
	echo '<hr /><b>Connecting to Revenue <span style="color:blue">Positions</span> Database Page: <u>'.$page.'</u>...</b><br />';
	// $url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Positions&COMMAND=view&api_whiteLabel='.$api_whiteLabel.'&FILTER[date][min]='.$scanDateFrom.'&FILTER[date][max]='.$scanDateTo.'&page='.$page; // &FILTER[confirmTime][min]='.$scanDateFrom.'&FILTER[confirmTime][max]='.$scanDateTo.'
	$url = concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Positions&COMMAND=view&FILTER[date][min]='.$scanDateFrom.'&FILTER[date][max]='.$scanDateTo.'&page='.$page); // &FILTER[confirmTime][min]='.$scanDateFrom.'&FILTER[confirmTime][max]='.$scanDateTo.)'
	// $url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Positions&COMMAND=view&FILTER[campaignid]='.$current_Campaign.'&api_whiteLabel='.$api_whiteLabel.'&FILTER[date][min]='.$scanDateFrom.'&FILTER[date][max]='.$scanDateTo.'&page='.$page; // &FILTER[confirmTime][min]='.$scanDateFrom.'&FILTER[confirmTime][max]='.$scanDateTo.'
	echo 'URL: ' . $url .'<br>';
	
	
	$xml_report=doPost($url);
	preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
	$intProcessed =0;
		if ($debug ==1) 		{ echo '<br>debug: ' ; var_dump($xml_report) . '<br>'; }
		
	foreach($xml[1] AS $xml_line) {
		$totalRowsFoundThisPage++;
		
		$existVolume = 0;
		$db['trader_id'] = getTag('<customerId>','<\/customerId>',$xml_line);
	/* 	if ($db['trader_id']=='13130') {
			var_dump($xml_line);
			die();
		} */
		$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag FROM data_reg WHERE merchant_id =" .$ww['id']. " and trader_id='".$db['trader_id']."' LIMIT 1"));
		if (!$chkTrader['id']) continue;
		$db['tranz_id'] = getTag('<id>','<\/id>',$xml_line);
		$db['type'] = 'volume';
		
		$db['rdate'] = getTag('<date>','<\/date>',$xml_line);
		$status = getTag('<status>','<\/status>',$xml_line);
	
		if ($status == "open" || $status == "canceled") continue;
		$db['amount'] = getTag('<amountUSD>','<\/amountUSD>',$xml_line);
		if (empty($db['amount'])) {
			$db['amount'] = getTag('<amount>','<\/amount>',$xml_line);
			$coin = getTag('<currency>','<\/currency>',$xml_line);
			$db['amount'] = getUSD($db['amount'],$coin);
		}
		/*
		if ($coin == "EUR") $db['amount'] = round($db['amount']*1.3,2);
			else if ($coin == "JPY") $db['amount'] = round($db['amount']/102,2);
			else if ($coin == "GBP") $db['amount'] = round($db['amount']*1.6,2);
		*/
		$chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales WHERE merchant_id =" .$ww['id']. " and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'"));
		if ($chkExist['id']) $existVolume=1;
		
		// Check cTag From Trader
		$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id =" .$ww['id']. " and  trader_id='".$db['trader_id']."'"));
		if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
			else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
		// Check cTag From Trader
			
		// if (!ctagValid($db['ctag'])) continue;
		
		$db['ctag'] = str_replace("--","-",$db['ctag']);
		$exp=explode("-",$db['ctag']);
		
		$db['affiliate_id']=substr($exp[0],1); // a
		$db['banner_id']=substr($exp[1],1); // b
		$db['profile_id']=substr($exp[2],1); // p
		$db['freeParam']=substr($exp[3],1); // f
		$db['country']=substr($exp[count($exp)-1],1); // c
		$db['freeParam']= overRideDynamicParameter($db['freeParam']);
		
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
			$db['affiliate_id'] = $defaultAffiliateID;
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			}
		$db['group_id'] = $getAffiliate['group_id'];
		
		if (count($db) > 1 AND !$existVolume) {
			// dbAdd($db,"data_sales_".strtolower($ww['name']));
			mysql_query("INSERT INTO data_sales  (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,
						trader_alias,type,amount,freeParam, campaign_id) 
						VALUES
						(".$ww['id'].", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
						'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
						'".$db['amount']."','".$db['freeParam']."', '" . $current_Campaign . "')") or die(mysql_error());
			
			echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
			$sales_total++;
			flush();
			} else {
			if ($isTest>0) 
			echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br />';
			}
			 $intProcessed++;
			
		}
		if ($intProcessed>0)
		echo 'Total processed so far: ' . $intProcessed . '<br>';
	
	 if ($totalRowsFoundThisPage>0)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else {
				if ($emptyPagesCounter==3)
				break; 
				else
					$emptyPagesCounter++;
			}
	}
	
			
	echo 'Total Processed: ' . $intProcessed . '<br>';
	echo '<hr /><b>Done!</b><br />';

	

$takeMonth = @mysql_fetch_assoc(mysql_query("SELECT id FROM cron_logs WHERE month='".date("n")."' AND year='".date("Y")."' AND merchant_id='".$ww['id']."'"));
if ($takeMonth['id']) @mysql_query("UPDATE cron_logs SET lastscan='".dbDate()."',reg_total=reg_total+".$reg_total.",sales_total=sales_total+".$sales_total." WHERE id='".$takeMonth['id']."'");
	else @mysql_query("INSERT INTO cron_logs (lastscan,month,year,merchant_id,merchant_name,success,reg_total,sales_total) VALUES ('".dbDate()."','".date("n")."','".date("Y")."','".$ww['id']."','".strtolower($ww['name'])."','1','".$reg_total."','".$sales_total."')");

	}
	echo '<br>Ending time' . date('h:i:s') . '<br>';
	echo 'Cron is done!';
die();
	exit;

?>
