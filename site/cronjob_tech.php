<?php
set_time_limit(0);
header("Pragma: no-cache");
header("Expires: 0");
$debug = 1;
ini_set("memory_limit","64M");
require_once('common/global.php');
//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');


/* 
//////////////////////////////////////////////////////////////////////////////////
require 'api/cron_job/TFAdapter.php';
$cron = new TFAdapter([
	'$_GET' => $_GET, 
	'db'    => get_object_vars($ss), // $ss taken from common/database.php
]);
$cron->run();
exit;
///////////////////////////////////////////////////////////////////////////////////

 */
$pagesize = 1000;
 
 function getCoin ($xml_line="") {
	 
	 if ($xml_line['banking_currencyId']==1) {
		$coin ='USD';
	}
	else if ($xml_line['banking_currencyId']==15) {
		$coin ='CNY';
	}
	else {
		echo '<span style="color:red;font-size:18px;">No match for currency id '.$xml_line['banking_currencyId'].'</span><br>';
		$coin = '';
	}
	return $coin;
	 
 }
 
 function curlPostlocal($url, $arrFields = [], $useGet = false)
		{
			$fieldsString = '';
			$result       = null;
			
			foreach ($arrFields as $key => $value) { 
				$fieldsString .= $key . '=' . $value . '&'; 
			}
			
			if ($useGet) {
				$url    .= (empty($fieldsString) ? '' : '?' . $fieldsString);
				$result  = file_get_contents($url);
			} else {
				rtrim($fieldsString, '&');
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_POST, count($arrFields));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsString);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				curl_close($ch);
			}
			
			return $result;
}

		
 
 function isJson($string) {
 json_decode($string);
 return (json_last_error() == JSON_ERROR_NONE);
}

 
if (!empty($isTest)) {
$campaignIDs = Array(
	"0" => $_GET[isTest]
);
} else { 

//OLD METHOD

$campaignIDs = Array(
	"0" => "1"
	
	);

}

$ww = dbGet('2',"merchants");

// var_dump($ww);
// die();
$defaultAffiliateID = $ww['defaultAffiliateID'];
$defaultBtag='a'.$defaultAffiliateID.'-b-p';
$api_url = $ww['APIurl'] ;  //   http://202.56.13.181/api/marketeer/customer/findAccounts?affiliateUserName=BizDevBuddies&affiliatePassword=Nir123&pageIndex=0&pageSize=100&FromDate=2015-05-11 11:47:43.0&accountID=58647
$api_user =  $ww['APIuser'];
$api_pass = $ww['APIpass'];
$api_label =  $ww['name'];
$api_whiteLabel =  $ww['name'];

$DynamicTrackertActive = false;

$isTest=isset($_GET['isTest']) ? $_GET['isTest'] : 0 ;

$find = Array("\n","\t");
$replace = Array("","");
echo '<br>Starting time' . date("h:i:s") . '<br>';
$countries2isoJson = '{"Bangladesh":"BD","Belgium":"BE","Burkina Faso":"BF","Bulgaria":"BG","Bosnia and Herzegovina":"BA","Barbados":"BB","Wallis and Futuna":"WF","Saint Barthelemy":"BL","Bermuda":"BM","Brunei":"BN","Bolivia":"BO","Bahrain":"BH","Burundi":"BI","Benin":"BJ","Bhutan":"BT","Jamaica":"JM","Bouvet Island":"BV","Botswana":"BW","Samoa":"WS","Bonaire, Saint Eustatius and Saba ":"BQ","Brazil":"BR","Bahamas":"BS","Jersey":"JE","Belarus":"BY","Belize":"BZ","Russia":"RU","Rwanda":"RW","Serbia":"RS","East Timor":"TL","Reunion":"RE","Turkmenistan":"TM","Tajikistan":"TJ","Romania":"RO","Tokelau":"TK","Guinea-Bissau":"GW","Guam":"GU","Guatemala":"GT","South Georgia and the South Sandwich Islands":"GS","Greece":"GR","Equatorial Guinea":"GQ","Guadeloupe":"GP","Japan":"JP","Guyana":"GY","Guernsey":"GG","French Guiana":"GF","Georgia":"GE","Grenada":"GD","United Kingdom":"GB","Gabon":"GA","El Salvador":"SV","Guinea":"GN","Gambia":"GM","Greenland":"GL","Gibraltar":"GI","Ghana":"GH","Oman":"OM","Tunisia":"TN","Jordan":"JO","Croatia":"HR","Haiti":"HT","Hungary":"HU","Hong Kong":"HK","Honduras":"HN","Heard Island and McDonald Islands":"HM","Venezuela":"VE","Puerto Rico":"PR","Palestinian Territory":"PS","Palau":"PW","Portugal":"PT","Svalbard and Jan Mayen":"SJ","Paraguay":"PY","Iraq":"IQ","Panama":"PA","French Polynesia":"PF","Papua New Guinea":"PG","Peru":"PE","Pakistan":"PK","Philippines":"PH","Pitcairn":"PN","Poland":"PL","Saint Pierre and Miquelon":"PM","Zambia":"ZM","Western Sahara":"EH","Estonia":"EE","Egypt":"EG","South Africa":"ZA","Ecuador":"EC","Italy":"IT","Vietnam":"VN","Solomon Islands":"SB","Ethiopia":"ET","Somalia":"SO","Zimbabwe":"ZW","Saudi Arabia":"SA","Spain":"ES","Eritrea":"ER","Montenegro":"ME","Moldova":"MD","Madagascar":"MG","Saint Martin":"MF","Morocco":"MA","Monaco":"MC","Uzbekistan":"UZ","Myanmar":"MM","Mali":"ML","Macao":"MO","Mongolia":"MN","Marshall Islands":"MH","Macedonia":"MK","Mauritius":"MU","Malta":"MT","Malawi":"MW","Maldives":"MV","Martinique":"MQ","Northern Mariana Islands":"MP","Montserrat":"MS","Mauritania":"MR","Isle of Man":"IM","Uganda":"UG","Tanzania":"TZ","Malaysia":"MY","Mexico":"MX","Israel":"IL","France":"FR","British Indian Ocean Territory":"IO","Saint Helena":"SH","Finland":"FI","Fiji":"FJ","Falkland Islands":"FK","Micronesia":"FM","Faroe Islands":"FO","Nicaragua":"NI","Netherlands":"NL","Norway":"NO","Namibia":"NA","Vanuatu":"VU","New Caledonia":"NC","Niger":"NE","Norfolk Island":"NF","Nigeria":"NG","New Zealand":"NZ","Nepal":"NP","Nauru":"NR","Niue":"NU","Cook Islands":"CK","Kosovo":"XK","Ivory Coast":"CI","Switzerland":"CH","Colombia":"CO","China":"CN","Cameroon":"CM","Chile":"CL","Cocos Islands":"CC","Canada":"CA","Republic of the Congo":"CG","Central African Republic":"CF","Democratic Republic of the Congo":"CD","Czech Republic":"CZ","Cyprus":"CY","Christmas Island":"CX","Costa Rica":"CR","Curacao":"CW","Cape Verde":"CV","Cuba":"CU","Swaziland":"SZ","Syria":"SY","Sint Maarten":"SX","Kyrgyzstan":"KG","Kenya":"KE","South Sudan":"SS","Suriname":"SR","Kiribati":"KI","Cambodia":"KH","Saint Kitts and Nevis":"KN","Comoros":"KM","Sao Tome and Principe":"ST","Slovakia":"SK","South Korea":"KR","Slovenia":"SI","North Korea":"KP","Kuwait":"KW","Senegal":"SN","San Marino":"SM","Sierra Leone":"SL","Seychelles":"SC","Kazakhstan":"KZ","Cayman Islands":"KY","Singapore":"SG","Sweden":"SE","Sudan":"SD","Dominican Republic":"DO","Dominica":"DM","Djibouti":"DJ","Denmark":"DK","British Virgin Islands":"VG","Germany":"DE","Yemen":"YE","Algeria":"DZ","United States":"US","Uruguay":"UY","Mayotte":"YT","United States Minor Outlying Islands":"UM","Lebanon":"LB","Saint Lucia":"LC","Laos":"LA","Tuvalu":"TV","Taiwan":"TW","Trinidad and Tobago":"TT","Turkey":"TR","Sri Lanka":"LK","Liechtenstein":"LI","Latvia":"LV","Tonga":"TO","Lithuania":"LT","Luxembourg":"LU","Liberia":"LR","Lesotho":"LS","Thailand":"TH","French Southern Territories":"TF","Togo":"TG","Chad":"TD","Turks and Caicos Islands":"TC","Libya":"LY","Vatican":"VA","Saint Vincent and the Grenadines":"VC","United Arab Emirates":"AE","Andorra":"AD","Antigua and Barbuda":"AG","Afghanistan":"AF","Anguilla":"AI","U.S. Virgin Islands":"VI","Iceland":"IS","Iran":"IR","Armenia":"AM","Albania":"AL","Angola":"AO","Antarctica":"AQ","American Samoa":"AS","Argentina":"AR","Australia":"AU","Austria":"AT","Aruba":"AW","India":"IN","Aland Islands":"AX","Azerbaijan":"AZ","Ireland":"IE","Indonesia":"ID","Ukraine":"UA","Qatar":"QA","Mozambique":"MZ"}';
$countriesArr = json_decode($countries2isoJson);
$siteURL = 'http://affiliate.fx77.com/';


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

function overrideCtagByCamp ($xml_line ='' , $camp=0) { 
$ctag='';
			if($camp=="25555551"){
				$ctag = 'a4020-b528-p';
			}else if($camp=="2555555555555555"){
				$ctag = 'a4021-b527-p';
						
			}else{
				$ctag = $xml_line['apiaccountview_param1'];
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
        
	
	$params = array("affiliateUserName" => "ot66d8",
			                "affiliatePassword" => "J2e57bdXQaa",
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
			"affiliateUserName: AffiliateכBuddies",
			"affiliatePassword: = 53be598עכגעכגעכגעכג1de84f",
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
	
function checkSaleStatus($mid, $camsID){
	global $api_url,$api_user,$api_pass,$current_Campaign,$ww;
	$fromDate = date("Y-m-d",strtotime("-3 Months"));
	$countXML=1;
	$page = 0;
	$intProcessed=0;
	while($countXML>0 AND $timesXML<1000){
		$url = $api_url.'?affiliateUserName='.$api_user.'&affiliatePassword='.$api_pass.'&MODULE=Customer&COMMAND=view&FILTER[campaignid]='.$current_Campaign.'&FILTER[regTime][min]='.$fromDate.'&page='.$page;
		$xml_report=doPost($url);
		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		echo '--------------------------------------------------------------------------------<BR>';
		echo 'UPDATING SaleStatus FOR CAMP: '.$current_Campaign.'<BR>';
		echo '--------------------------------------------------------------------------------<BR>';
		$countXML=0;
		if ($debug >0) 		{ echo '<br>debug: ' ; var_dump($xml_report); }
		foreach($xml[1] AS $xml_line) {

		
			$saleStatus = getTag('<salestatus>','<\/salestatus>',$xml_line);
			if (empty($saleStatus))
				$saleStatus = getTag('<saleStatus>','<\/saleStatus>',$xml_line);
				
			$traderID = getTag('<id>','<\/id>',$xml_line);
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
		mysql_query('UPDATE merchants SET lastSaleStatusUpdate="'.date("Y-m-d H:i:s").'" WHERE id='.$mid) OR die(mysql_error());
		echo '--------------------------------------------------------------------------------<BR>';
	}
	echo 'Total Processed: ' . $intProcessed . '<br>';
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
		$scanDateFrom = date("Y-m-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-m-01", strtotime("+1 Month",strtotime($scanDateFrom)));//.' 23:59:59';
		$totalPage = 1000;
		} else {
		$scanDateFrom = date("Y-m-d", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));//.' 23:59:59';
		}
	} else {
	$scanDateFrom = date("Y-m-d", strtotime("-1 Day"));//.' 00:00:00';
	$scanDateTo = date("Y-m-d", strtotime("+1 Day"));//.' 00:00:00';
	//$scanDateTo = date("Y-m-d");//.' 23:59:59';
	}
if (!$totalPage) $totalPage = 3;



$toCheckSaleStatus = (date("Y-m-d H:i:s",strtotime("-8 Hours"))>$ww['lastSaleStatusUpdate']);
//die ('tocheck: ' . $ww['lastSaleStatusUpdate']);




echo 'From: <u>'.$scanDateFrom.'</u> To: <u>'.$scanDateTo.'</u>';

$campscount = 0;

//var_dump($campaignIDs);
//die ('done');

	foreach ($campaignIDs as $key => $value) {
	//for ($camsID=0; $camsID < count($campaignIDs); $camsID++) {
		$current_Campaign = $value;
			echo '<span style="color:green"><br>Campaign:  '  .$current_Campaign . '<br></span>';
			
			
		$campscount++;
		 if($campscount % 4 ==0) {
			 usleep(500000);
		 }
		 $totalRowsFoundSoFar = 0;
		for ($page=0; $page<=$totalPage; $page++)	{
		$shouldRunOnNextPageAsWell = false;
		echo '<hr /><b>Connecting to Customers\'s & Database (Campaign ID:'.$current_Campaign.') <span style="color:blue">Page: <u>'.$page.'</u>...</span></b><br />';
		
		
		if (!$current_Campaign) {
			continue;
		}
		
		
		if($toCheckSaleStatus){
			echo '<BR>About to run checkSaleStatus';
			checkSaleStatus($ww['id'],$camsID);
		}
		
		$url = $api_url.'customer/findAccounts?affiliateUserName='.$api_user.'&affiliatePassword='.$api_pass.'&pageIndex='.$page.'&pageSize='.$pagesize.'&accountID=&FromDate='.$scanDateFrom.'%2000:00:00&toDate='.$scanDateTo.'%2000:00:00';
		echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		// $xml_report=doPost($url);
		$xml_report = curlPostlocal($url);
		
		
		$a = substr($xml_report, 0, strrpos( $xml_report, '}'));
		$a = substr($a, 0, strrpos( $a, ']'));
		$exp = explode('[', $a);
		$a= $exp[1];
		$xml_report = mb_convert_encoding($xml_report,'UTF-8','UTF-8'); 
		$xml_report = json_decode($xml_report,true);
	
		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug>1) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
		
		$xml = $xml_report['result'];
		
		foreach($xml AS $xml_line) {
			
		$shouldRunOnNextPageAsWell=true;
		$totalRowsFoundThisPage++;
			$arrXmlTest[] = $xml_line;
			unset($db);
			$exist = 0;
			// var_dump($xml_line);
			// die();
			$db['email'] = $xml_line['apiaccountview_email'];
			$db['trader_id'] = $xml_line['apiaccountview_id'];
			
			$db['firstname'] = str_replace(Array("\\","'","`"),Array("","",""),$xml_line['apiaccountview_firstName']);
			$db['lastname'] = str_replace(Array("\\","'","`"),Array("","",""),$xml_line['apiaccountview_lastName']);
			//$db['country'] = str_replace(Array("\\","'","`"),Array("","",""),$xml_line['apiaccountview_countryId']);

			$db['rdate'] = date("Y-m-d h:i:s", strtotime($xml_line['apiaccountview_registrationDate']));
			// die ($db['rdate']);
			$db['ctag'] = overrideCtagByCamp($xml_line,$current_Campaign);
			
			if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;
			
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
					$db['country']=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					$db['freeParam']= overRideDynamicParameter($db['freeParam']);
			
			if(strtolower($db['country'])=='any' OR strtolower($db['country'])==''){
				$gc = str_replace(Array("\\","'","`"),Array("","",""),$xml_line['apiaccountview_countryId']);
				$db['country'] = str_replace(Array("\\","'","`"),Array("","",""),$gc);
				$db['country'] = $countriesArr->$db['country'];
			}
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				}
			$db['group_id'] = $getAffiliate['group_id'];
			
			$db['trader_alias'] = str_replace(Array("\\","'","`"),Array("","",""),$db['firstname'].' '.$db['lastname']);
			
			$type = $xml_line['apiaccountview_isDemo'];
			// die ('demo: ' . $type. '<br>');
			if (strtolower($type)=='true') {
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
						$qry = "INSERT INTO data_reg(merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,
									country,trader_id,trader_alias,type,freeParam, campaign_id,uid,email) 
									VALUES
							(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
							'".$db['profile_id']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
							'".$db['freeParam']."', '" . $current_Campaign . "', '" . $db['uid'] . "', '" . $db['email'] . "')";
							
							// die ($qry);
						mysql_query($qry) ;
						
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
			
		/* 	if ($totalRowsFoundThisPage>$totalRowsFoundSoFar)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else
				break; */
			
		//die(print_r($arrXmlTest));
		//die(print_r($arrXmlTest));
		echo 'Total Processed: ' . $intProcessed . '<br>';
		echo '<hr /><b>Done!</b><br />';
		######################### [ CSV REG ] #########################
		
######################### [ Deposits ] #########################
$totalRowsFoundSoFar=0;
//for ($page=0; $page<=$totalPage; $page++) 
{
echo '<hr /><b>Connecting to <span style="color:blue">Transactions</span> Database Page: <u>'.$page.'</u>...</b><br />';
$url = $api_url.'banking/findTransactions?affiliateUserName='.$api_user.'&affiliatePassword='.$api_pass.'&pageIndex='.$page.'&type=102&pageSize='.$pagesize.'&accountID=&FromDate='.$scanDateFrom.'%2000:00:00&toDate='.$scanDateTo.'%2000:00:00';
	echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		// $xml_report=doPost($url);
		$xml_report = curlPostlocal($url);
		
		// var_dump($xml_report);
		// die();
		$a = substr($xml_report, 0, strrpos( $xml_report, '}'));
		$a = substr($a, 0, strrpos( $a, ']'));
		$exp = explode('[', $a);
		$a= $exp[1];
		$xml_report = mb_convert_encoding($xml_report,'UTF-8','UTF-8'); 
		$xml_report = json_decode($xml_report,true);
	
		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug >1) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
		
		$xml = $xml_report['result'];
	
if ($xml_report['result']==null) {
	echo 'No Result..<br>';
}
	else
		foreach($xml AS $xml_line) {	
			
			$shouldRunOnNextPageAsWell=true;

		
if ($debug>0) {
	var_dump($xml_line);
	echo '<br>';
}
	

	
	$tranzExist=0;
	$db['trader_id'] = $xml_line['banking_accountId'];
	$db['amount'] = $xml_line['banking_amount']/100;
	// die ('f: ' . $db['amount']);
	$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag,trader_id,merchant_id FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and  trader_id='".$db['trader_id']."' LIMIT 1"));
	//var_dump($chkTrader);
	
	if (!$chkTrader['id']) continue;
	$db['tranz_id'] = $xml_line['banking_id'];
	$db['type'] = 'deposit';
	
	//$paymentMethod = getTag('<paymentMethod>','<\/paymentMethod>',$xml_line);
	//if (strtolower($paymentMethod) == "bonus") $db['type'] = 'bonus';
	
	if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") continue;
	
	$db['rdate'] = $xml_line['banking_updateTime'];
	
	$coin = getCoin($xml_line);
	
	$db['amount'] = getUSD($db['amount'],$coin);
	// if ($db['amount']==0)
		// die('----11');
	
	
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
		mysql_query(
			"INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,
			trader_id,trader_alias,type,amount,freeParam, campaign_id) 
			VALUES
			(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
			'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
			'".$db['amount']."','".$db['freeParam']."', '" . $current_Campaign . "')") or die(mysql_error());
			
		
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
		if ($isTest >0) 
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
	}
	$intProcessed++;
	if ($intProcessed % 100 == 0) {
		echo '... , ';
	}
	$totalRowsFoundThisPage++;
	}
	
	
	/* if ($totalRowsFoundThisPage>$totalRowsFoundSoFar)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else
				break; */
	
if ($intProcessed>0)
	echo 'Total processed so far: ' . $intProcessed . '<br>';
	}
######################### [ Deposits ] #########################
######################### [ Withdrawal ] #########################
$totalRowsFoundSoFar=0;
{
echo '<hr /><b>Connecting to <span style="color:blue">Withdrawals</span> Database Page: <u>'.$page.'</u>...</b><br />';
$url = $api_url.'banking/findTransactions?affiliateUserName='.$api_user.'&affiliatePassword='.$api_pass.'&pageIndex='.$page.'&type=103&pageSize='.$pagesize.'&accountID=&FromDate='.$scanDateFrom.'%2000:00:00&toDate='.$scanDateTo.'%2000:00:00';
	echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		// $xml_report=doPost($url);
		$xml_report = curlPostlocal($url);
		
		// var_dump($xml_report);
		// die();
		$a = substr($xml_report, 0, strrpos( $xml_report, '}'));
		$a = substr($a, 0, strrpos( $a, ']'));
		$exp = explode('[', $a);
		$a= $exp[1];
		$xml_report = mb_convert_encoding($xml_report,'UTF-8','UTF-8'); 
		$xml_report = json_decode($xml_report,true);
	
		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug >1) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
		
		$xml = $xml_report['result'];
	
if ($xml_report['result']==null) {
	echo 'No Result..<br>';
}
	else
		foreach($xml AS $xml_line) {	
			
			
$shouldRunOnNextPageAsWell=true;
		
if ($debug>0) {
	var_dump($xml_line);
	echo '<br>';
}
	

	
	$tranzExist=0;
	$db['trader_id'] = $xml_line['banking_accountId'];
	$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag,trader_id,merchant_id FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and  trader_id='".$db['trader_id']."' LIMIT 1"));
	//var_dump($chkTrader);
	
	if (!$chkTrader['id']) continue;
	$db['tranz_id'] = $xml_line['banking_id'];
	$db['type'] = 'withdrawal';
	
	//$paymentMethod = getTag('<paymentMethod>','<\/paymentMethod>',$xml_line);
	//if (strtolower($paymentMethod) == "bonus") $db['type'] = 'bonus';
	
	if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") continue;
	
	$db['rdate'] = $xml_line['banking_updateTime'];
	$db['amount'] = $xml_line['banking_amount']/100;
	
	$coin = getCoin($xml_line);
	
	$db['amount'] = getUSD($db['amount'],$coin);
	
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
		mysql_query(
			"INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,
			trader_id,trader_alias,type,amount,freeParam, campaign_id) 
			VALUES
			(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
			'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
			'".$db['amount']."','".$db['freeParam']."', '" . $current_Campaign . "')") or die(mysql_error());
			
		
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
		if ($isTest >0) 
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
	}
	$intProcessed++;
	if ($intProcessed % 100 == 0) {
		echo '... , ';
	}
	$totalRowsFoundThisPage++;
	}
	
	
/* 	if ($totalRowsFoundThisPage>$totalRowsFoundSoFar)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else
				break; */
	
if ($intProcessed>0)
	echo 'Total processed so far: ' . $intProcessed . '<br>';
	}
######################### [ Withdrawal ] #########################
	
######################### [ ChargeBack ] #########################
$totalRowsFoundSoFar=0;
//for ($page=0; $page<=$totalPage; $page++) 
{
echo '<hr /><b>Connecting to <span style="color:blue">ChargeBacks</span> Database Page: <u>'.$page.'</u>...</b><br />';
$url = $api_url.'banking/findTransactions?affiliateUserName='.$api_user.'&affiliatePassword='.$api_pass.'&pageIndex='.$page.'&type=195&pageSize='.$pagesize.'&accountID=&FromDate='.$scanDateFrom.'%2000:00:00&toDate='.$scanDateTo.'%2000:00:00';
	echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		// $xml_report=doPost($url);
		$xml_report = curlPostlocal($url);
		
		// var_dump($xml_report);
		// die();
		$a = substr($xml_report, 0, strrpos( $xml_report, '}'));
		$a = substr($a, 0, strrpos( $a, ']'));
		$exp = explode('[', $a);
		$a= $exp[1];
		$xml_report = mb_convert_encoding($xml_report,'UTF-8','UTF-8'); 
		$xml_report = json_decode($xml_report,true);
	
		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug >1) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
		
		$xml = $xml_report['result'];
	
if ($xml_report['result']==null) {
	echo 'No Result..<br>';
}
	else
		foreach($xml AS $xml_line) {	
			
			$shouldRunOnNextPageAsWell=true;

		
if ($debug>0) {
	var_dump($xml_line);
	echo '<br>';
}
	

	
	$tranzExist=0;
	$db['trader_id'] = $xml_line['banking_accountId'];
	$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag,trader_id,merchant_id FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and  trader_id='".$db['trader_id']."' LIMIT 1"));
	//var_dump($chkTrader);
	
	if (!$chkTrader['id']) continue;
	$db['tranz_id'] = $xml_line['banking_id'];
	$db['type'] = 'chargeback';
	
	//$paymentMethod = getTag('<paymentMethod>','<\/paymentMethod>',$xml_line);
	//if (strtolower($paymentMethod) == "bonus") $db['type'] = 'bonus';
	
	if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal" AND $db['type'] != "chargeback") continue;
	
	$db['rdate'] = $xml_line['banking_updateTime'];
	$db['amount'] = $xml_line['banking_amount']/100;
	
	$coin = getCoin($xml_line);
	
	$db['amount'] = getUSD($db['amount'],$coin);
	
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
		mysql_query(
			"INSERT INTO data_sales (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,
			trader_id,trader_alias,type,amount,freeParam, campaign_id) 
			VALUES
			(" . $ww['id'] . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
			'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
			'".$db['amount']."','".$db['freeParam']."', '" . $current_Campaign . "')") or die(mysql_error());
			
			
		
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
		if ($isTest >0) 
		echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
	}
	$intProcessed++;
	if ($intProcessed % 100 == 0) {
		echo '... , ';
	}
	$totalRowsFoundThisPage++;
	}
	
	
	/* if ($totalRowsFoundThisPage>$totalRowsFoundSoFar)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else
				break; */
	
if ($intProcessed>0)
	echo 'Total processed so far: ' . $intProcessed . '<br>';

	}
######################### [ ChargeBack ] #########################
	
echo '<hr /><b>Done!</b><br />';


echo 'Total Processed: ' . $intProcessed . '<br>';
echo '<hr /><b>Done!</b><br />';


if ($shouldRunOnNextPageAsWell==false) {
	break;
}
	}
######################### [ Positions Volume ] #########################
$totalRowsFoundSoFar=0;
				
	for ($page=0; $page<=$totalPage; $page++) {
//		break; //////////////////////////////////////////////////////////////
		
		$positioncount++;
		 if($positioncount % 4 ==0) {
			 usleep(1000000);
		 }
			 
	echo '<hr /><b>Connecting to Revenue <span style="color:blue">Positions</span> Database Page: <u>'.$page.'</u>...</b><br />';
	$scanDateFrom = "2015-08-01";
	$url = $api_url.'trading/getTrades?affiliateUserName='.$api_user.'&affiliatePassword='.$api_pass.'&status=20&pageIndex='.$page.'&pageSize='.$pagesize.'&accountID=&FromDate='.$scanDateFrom.'%2000:00:00&toDate='.$scanDateTo.'%2000:00:00';
	echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		// $xml_report=doPost($url);
		$xml_report = curlPostlocal($url);
		
		
		
		$a = substr($xml_report, 0, strrpos( $xml_report, '}'));
		$a = substr($a, 0, strrpos( $a, ']'));
		$exp = explode('[', $a);
		$a= $exp[1];
		$xml_report = mb_convert_encoding($xml_report,'UTF-8','UTF-8'); 
		$xml_report = json_decode($xml_report,true);
	
	
		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug >1) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
		
		$xml = $xml_report['result'];
		
		
		// var_dump($xml);
		
		// die();
	
		
if ($xml_report['result']==null) {
	echo 'No Result..<br>';
}
	else
		foreach($xml AS $xml_line) {	
		
		$existVolume = 0;
		$db['trader_id'] = $xml_line['tradinghistory_accountId'];
		$qry = "SELECT id,ctag FROM data_reg WHERE merchant_id =" .$ww['id']. " and trader_id='".$db['trader_id']."' LIMIT 1";
		// var_dump($xml_line);
		// var_dump($qry);
		// die();
		$chkTrader=mysql_fetch_assoc(mysql_query(	$qry));
		if (!$chkTrader['id']) continue;
		$db['tranz_id'] = $xml_line['tradinghistory_positionId'];
		$db['type'] = 'volume';
		
		$db['rdate'] = $xml_line['tradinghistory_closeTime'];
		$old_time = strtotime($db['rdate']);
		$db['rdate'] =date("Y-m-d h:i:s", $old_time);
		
		$db['amount'] = $xml_line['tradinghistory_amount']/100;
		$coin = $xml_line['tradinghistory_currencyISO'];
		
		
		$status = 'closed';//getTag('<status>','<\/status>',$xml_line);
	
		if ($status == "open") continue;
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
			
		if (!ctagValid($db['ctag'])) continue;
		
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
			$qry = "INSERT INTO data_sales  (merchant_id,rdate,ctag,affiliate_id,group_id,market_id,banner_id,profile_id,country,tranz_id,trader_id,
						trader_alias,type,amount,freeParam) 
						VALUES
						(".$ww['id'].", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['market_id']."','".$db['banner_id']."',
						'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
						'".$db['amount']."','".$db['freeParam']."')";
			// die ($qry);
			mysql_query($qry) ;
			
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
	
	
	$totalRowsFoundThisPage++;
	
	
	if ($totalRowsFoundThisPage>$totalRowsFoundSoFar && $intProcessed>0)
				$totalRowsFoundSoFar += $totalRowsFoundThisPage;
			else
				break;
	}		
			
	echo 'Total Processed: ' . $intProcessed . '<br>';
	echo '<hr /><b>Done!</b><br />';
}


$takeMonth = @mysql_fetch_assoc(mysql_query("SELECT id FROM cron_logs WHERE month='".date("n")."' AND year='".date("Y")."' AND merchant_id='".$ww['id']."'"));
if ($takeMonth['id']) @mysql_query("UPDATE cron_logs SET lastscan='".dbDate()."',reg_total=reg_total+".$reg_total.",sales_total=sales_total+".$sales_total." WHERE id='".$takeMonth['id']."'");
	else @mysql_query("INSERT INTO cron_logs (lastscan,month,year,merchant_id,merchant_name,success,reg_total,sales_total) VALUES ('".dbDate()."','".date("n")."','".date("Y")."','".$ww['id']."','".strtolower($ww['name'])."','1','".$reg_total."','".$sales_total."')");

	echo '<br>Ending time' . date('h:i:s') . '<br>';
	echo 'Cron is done!';
exit;

?>