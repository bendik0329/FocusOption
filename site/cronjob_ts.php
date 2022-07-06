<?php
$cronStartTime = date("Y-m-d H:i:s");

set_time_limit(0);

header("Pragma: no-cache");
header("Expires: 0");
$debug = isset($_GET['debug'])? $_GET['debug']: 0;
ini_set("memory_limit","2048M");
require_once('common/database.php');
require_once('common/global.php');
include ('pixel.php');

checkCurrencies();

/*********************/
function getCurrencyByCurrencyIdMap($id){
    $arrayCurrencyMap = [
        1 => 'usd',
        2 => 'eur',
        3 => 'aud',
        4 => 'cny',
        5 => 'gbp',
        6 => 'jpy',
        7 => 'rub',f
    ];
    
    if(!empty($arrayCurrencyMap[$id])){
        return $arrayCurrencyMap[$id];
    }else{
        return false;
    }
    
}
/*********************/

//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');
$pagesize = 1000;

$m_date = isset($_GET['m_date']) ? $_GET['m_date'] : date('Y-m-d');
$lastmonth = isset($_GET['lastmonth']) && $_GET['lastmonth']==1  ? true : false;

$forceCheckSaleStatus = isset($_GET['forceCheckSaleStatus']) ? $_GET['forceCheckSaleStatus'] : false;
$shouldRunPos = isset($_GET['shouldRunPos']) ? $_GET['shouldRunPos'] : true;
$ignoreSaleStatus = isset($_GET['ignoreSaleStatus']) ? $_GET['ignoreSaleStatus'] : false;

$monthly = isset($_GET['monthly']) ? 1 : "";

$cron_merchant_id = isset($_GET['merchant_id']) ? (int)$_GET['merchant_id'] : die('No merchant ID');

$pendingDespositArrayLoaded= false;
$pendingDepositExcludeAffiliatesArray=array();
$pendingDepositIncludeAffiliatesArray=array();

function populatePDEA(){
    global $pendingDepositExcludeAffiliatesArray;
    global $pendingDepositIncludeAffiliatesArray;
    
    $qq = function_mysql_query("select id,username  from affiliates where pendingDepositExclude=1 and valid=1");
    while($ww = mysql_fetch_assoc($qq)){
        $pendingDepositExcludeAffiliatesArray[$ww['id']]  = $ww['username'];
    }

    $qq = function_mysql_query("select id,username  from affiliates where pendingDepositInclude=1 and valid=1");
    while($ww = mysql_fetch_assoc($qq)){
        $pendingDepositIncludeAffiliatesArray[$ww['id']]  = $ww['username'];
    }

}
if (!$pendingDespositArrayLoaded){
	populatePDEA();
	$pendingDespositArrayLoaded=true;
}



if ($lastmonth ) {
	$exp_mdate=explode("-",date('Y-m-d'));
		$scanDateFrom = date("Y-m-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
	$m_date =  date("Y-m-01", strtotime("-1 Month",strtotime($scanDateFrom)));//.' 23:59:59';
}

if (!empty($isTest)) {
$campaignIDs = Array(
	"0" => $_GET[isTest]
);
} else { 
	$campaignIDs = Array(
		"0" => "1"
	);
}

$ww = dbGet($cron_merchant_id,"merchants");
$sxOption_MerchantId = 24;
$defaultAffiliateID = $ww['defaultAffiliateID'];
$defaultBtag='a'.$defaultAffiliateID.'-b-p';
$api_url = $ww['APIurl'] ;  
$api_user =  $ww['APIuser'];
$api_pass = $ww['APIpass'];
$api_label =  $ww['name'];
$api_whiteLabel =  $ww['name'];

$query = 'select * from affiliates_campaigns_relations where merchantid ='.$ww['id'];
$campsQ = mysql_query($query);
$autoRelate_campaignIDs = Array();

while($row = mysql_fetch_assoc($campsQ)){
		$autoRelate_campaignIDs[$row['campID']]= $row;
		// array_push($campaignIDs, $row);
}

$DynamicTrackertActive = true;
$isTest=isset($_GET['isTest']) ? $_GET['isTest'] : 0 ;

$find = Array("\n","\t");
$replace = Array("","");
echo '<br>Starting time' . date("h:i:s") . '<br>';
$countries2isoJson = '{"Bangladesh":"BD","Belgium":"BE","Burkina Faso":"BF","Bulgaria":"BG","Bosnia and Herzegovina":"BA","Barbados":"BB","Wallis and Futuna":"WF","Saint Barthelemy":"BL","Bermuda":"BM","Brunei":"BN","Bolivia":"BO","Bahrain":"BH","Burundi":"BI","Benin":"BJ","Bhutan":"BT","Jamaica":"JM","Bouvet Island":"BV","Botswana":"BW","Samoa":"WS","Bonaire, Saint Eustatius and Saba ":"BQ","Brazil":"BR","Bahamas":"BS","Jersey":"JE","Belarus":"BY","Belize":"BZ","Russia":"RU","Rwanda":"RW","Serbia":"RS","East Timor":"TL","Reunion":"RE","Turkmenistan":"TM","Tajikistan":"TJ","Romania":"RO","Tokelau":"TK","Guinea-Bissau":"GW","Guam":"GU","Guatemala":"GT","South Georgia and the South Sandwich Islands":"GS","Greece":"GR","Equatorial Guinea":"GQ","Guadeloupe":"GP","Japan":"JP","Guyana":"GY","Guernsey":"GG","French Guiana":"GF","Georgia":"GE","Grenada":"GD","United Kingdom":"GB","Gabon":"GA","El Salvador":"SV","Guinea":"GN","Gambia":"GM","Greenland":"GL","Gibraltar":"GI","Ghana":"GH","Oman":"OM","Tunisia":"TN","Jordan":"JO","Croatia":"HR","Haiti":"HT","Hungary":"HU","Hong Kong":"HK","Honduras":"HN","Heard Island and McDonald Islands":"HM","Venezuela":"VE","Puerto Rico":"PR","Palestinian Territory":"PS","Palau":"PW","Portugal":"PT","Svalbard and Jan Mayen":"SJ","Paraguay":"PY","Iraq":"IQ","Panama":"PA","French Polynesia":"PF","Papua New Guinea":"PG","Peru":"PE","Pakistan":"PK","Philippines":"PH","Pitcairn":"PN","Poland":"PL","Saint Pierre and Miquelon":"PM","Zambia":"ZM","Western Sahara":"EH","Estonia":"EE","Egypt":"EG","South Africa":"ZA","Ecuador":"EC","Italy":"IT","Vietnam":"VN","Solomon Islands":"SB","Ethiopia":"ET","Somalia":"SO","Zimbabwe":"ZW","Saudi Arabia":"SA","Spain":"ES","Eritrea":"ER","Montenegro":"ME","Moldova":"MD","Madagascar":"MG","Saint Martin":"MF","Morocco":"MA","Monaco":"MC","Uzbekistan":"UZ","Myanmar":"MM","Mali":"ML","Macao":"MO","Mongolia":"MN","Marshall Islands":"MH","Macedonia":"MK","Mauritius":"MU","Malta":"MT","Malawi":"MW","Maldives":"MV","Martinique":"MQ","Northern Mariana Islands":"MP","Montserrat":"MS","Mauritania":"MR","Isle of Man":"IM","Uganda":"UG","Tanzania":"TZ","Malaysia":"MY","Mexico":"MX","Israel":"IL","France":"FR","British Indian Ocean Territory":"IO","Saint Helena":"SH","Finland":"FI","Fiji":"FJ","Falkland Islands":"FK","Micronesia":"FM","Faroe Islands":"FO","Nicaragua":"NI","Netherlands":"NL","Norway":"NO","Namibia":"NA","Vanuatu":"VU","New Caledonia":"NC","Niger":"NE","Norfolk Island":"NF","Nigeria":"NG","New Zealand":"NZ","Nepal":"NP","Nauru":"NR","Niue":"NU","Cook Islands":"CK","Kosovo":"XK","Ivory Coast":"CI","Switzerland":"CH","Colombia":"CO","China":"CN","Cameroon":"CM","Chile":"CL","Cocos Islands":"CC","Canada":"CA","Republic of the Congo":"CG","Central African Republic":"CF","Democratic Republic of the Congo":"CD","Czech Republic":"CZ","Cyprus":"CY","Christmas Island":"CX","Costa Rica":"CR","Curacao":"CW","Cape Verde":"CV","Cuba":"CU","Swaziland":"SZ","Syria":"SY","Sint Maarten":"SX","Kyrgyzstan":"KG","Kenya":"KE","South Sudan":"SS","Suriname":"SR","Kiribati":"KI","Cambodia":"KH","Saint Kitts and Nevis":"KN","Comoros":"KM","Sao Tome and Principe":"ST","Slovakia":"SK","South Korea":"KR","Slovenia":"SI","North Korea":"KP","Kuwait":"KW","Senegal":"SN","San Marino":"SM","Sierra Leone":"SL","Seychelles":"SC","Kazakhstan":"KZ","Cayman Islands":"KY","Singapore":"SG","Sweden":"SE","Sudan":"SD","Dominican Republic":"DO","Dominica":"DM","Djibouti":"DJ","Denmark":"DK","British Virgin Islands":"VG","Germany":"DE","Yemen":"YE","Algeria":"DZ","United States":"US","Uruguay":"UY","Mayotte":"YT","United States Minor Outlying Islands":"UM","Lebanon":"LB","Saint Lucia":"LC","Laos":"LA","Tuvalu":"TV","Taiwan":"TW","Trinidad and Tobago":"TT","Turkey":"TR","Sri Lanka":"LK","Liechtenstein":"LI","Latvia":"LV","Tonga":"TO","Lithuania":"LT","Luxembourg":"LU","Liberia":"LR","Lesotho":"LS","Thailand":"TH","French Southern Territories":"TF","Togo":"TG","Chad":"TD","Turks and Caicos Islands":"TC","Libya":"LY","Vatican":"VA","Saint Vincent and the Grenadines":"VC","United Arab Emirates":"AE","Andorra":"AD","Antigua and Barbuda":"AG","Afghanistan":"AF","Anguilla":"AI","U.S. Virgin Islands":"VI","Iceland":"IS","Iran":"IR","Armenia":"AM","Albania":"AL","Angola":"AO","Antarctica":"AQ","American Samoa":"AS","Argentina":"AR","Australia":"AU","Austria":"AT","Aruba":"AW","India":"IN","Aland Islands":"AX","Azerbaijan":"AZ","Ireland":"IE","Indonesia":"ID","Ukraine":"UA","Qatar":"QA","Mozambique":"MZ"}';
$countriesArr = json_decode($countries2isoJson);
$siteURL = 'http://affiliate.wow-partners.com/';
	$parse_url=parse_url($url);
	
	



/*********************/
$tradersMonthlyPnlArray = [];
$AffiliateMonthlyPnlArray = [];
$AffiliatePreMonthlyPnlArray = [];
$AffiliatePnlLimitArray = [];

function getPNLBalanceOfAmountMonth($date, $affiliateId, $merchantId, $limit)
{
    global $AffiliatePreMonthlyPnlArray;

    
   // $dateOlder = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "-1 month"));
    $monthFrom = date('Y-m-01',strtotime($date. "-1 month"));
    $monthTo = date('Y-m-t',strtotime($date. "-1 month"));

    if(empty($AffiliatePreMonthlyPnlArray[$affiliateId])){
        $pnlSum = 0;
        $pnlSumQueryResult = function_mysql_query("SELECT SUM(amount) AS pnl_amount FROM `data_sales` WHERE rdate >= '".$monthFrom."' AND rdate <= '".$monthTo." 23:59:59' AND type = 'PNL' AND affiliate_id = '".$affiliateId."' AND merchant_id = '".$merchantId."'");
        $pnlSum = mysql_fetch_assoc($pnlSumQueryResult);
        $AffiliatePreMonthlyPnlArray[$affiliateId] = $limit - (($pnlSum['pnl_amount'])?($pnlSum['pnl_amount'] * (-1)):0);

    }
    return  $AffiliatePreMonthlyPnlArray[$affiliateId];
}

function getMaxPnlMonthlyLimit($id){
    $max_pnl_monthly_amount = 0;
    $max_pnl_monthly_limit_result = function_mysql_query("SELECT * FROM `traders_deals` WHERE rdate <= '".date('Y-m-d H:i:s')."' AND affiliate_id = ".(int)$id." AND trader_id = 0 AND dealType = 'max_pnl_monthly' AND valid = 1 ORDER BY rdate DESC LIMIT 1",__FILE__);
    $max_pnl_monthly_limit_data = mysql_fetch_assoc($max_pnl_monthly_limit_result);
    if(!empty($max_pnl_monthly_limit_data['amount'])){
        $max_pnl_monthly_amount = $max_pnl_monthly_limit_data['amount'];
    }
    
    return $max_pnl_monthly_amount;
}

function getTraderDealsValue($id,$type){
    $amount = 0;
    $result = function_mysql_query("SELECT * FROM `traders_deals` WHERE rdate <= '".date('Y-m-d H:i:s')."' AND affiliate_id = ".(int)$id." AND trader_id = 0 AND dealType = '".$type."' AND valid = 1 ORDER BY rdate DESC LIMIT 1",__FILE__);
    $data = mysql_fetch_assoc($result);
    if(!empty($data['amount'])){
        $amount = $data['amount'];
    }
    
    return $amount;
}

function IsPnlExceededByAffiliate($affiliate_id, $pnl, $max_affiliate_pnl, $merchant, $date){

	global $AffiliateMonthlyPnlArray, $AffiliatePreMonthlyPnlArray;

	$currentMonthFrom = date('Y-m-01',strtotime($date));
	$currentMonthTo = date('Y-m-t',strtotime($date));

    /*
    if (!empty($AffiliatePreMonthlyPnlArray[$affiliate_id])) {
        $max_affiliate_pnl = $max_affiliate_pnl + $AffiliatePreMonthlyPnlArray[$affiliate_id];
    } else {
        $max_affiliate_pnl = $max_affiliate_pnl + getPNLBalanceOfAmountMonth($date, (int)$affiliate_id, $merchant['id'], $max_affiliate_pnl);
    }
    echo 'SUM(A_PRE:'.$AffiliatePreMonthlyPnlArray[$affiliate_id].') - ';
    */
    
    echo 'SUM(A_PRE_M:'.$max_affiliate_pnl.') - ';

	if($max_affiliate_pnl > 0){

		if(empty($AffiliateMonthlyPnlArray[$affiliate_id])){
			$pnlSum = 0;
			$pnlSumQueryResult = function_mysql_query("SELECT SUM(amount) AS pnl_amount FROM `data_sales` WHERE rdate >= '".$currentMonthFrom."' AND rdate <= '".$currentMonthTo." 23:59:59' AND type = 'PNL' AND affiliate_id = '".$affiliate_id."' AND merchant_id = '".$merchant['id']."'");
			$pnlSum = mysql_fetch_assoc($pnlSumQueryResult);
			$AffiliateMonthlyPnlArray[$affiliate_id] = (($pnlSum['pnl_amount'])?($pnlSum['pnl_amount'] * (-1)):0);

		}

		if($pnl > 0){
			if( $AffiliateMonthlyPnlArray[$affiliate_id] >= $max_affiliate_pnl ){
				echo 'SUM(A:'.$AffiliateMonthlyPnlArray[$affiliate_id].') - '; 
				return true;
			}
		}

		echo 'SUM(A:'.$AffiliateMonthlyPnlArray[$affiliate_id].') - '; 
		$AffiliateMonthlyPnlArray[$affiliate_id] += $pnl;
	}

	return false;
}

function IsPnlExceeded($trader_data, $pnl_original, $merchant, $date){
	
	global $tradersMonthlyPnlArray, $AffiliatePnlLimitArray;

	$currentMonthFrom = date('Y-m-01',strtotime($date));
	$currentMonthTo = date('Y-m-t',strtotime($date));

	$trader_id = $trader_data['trader_id'];
	$affiliate_id = $trader_data['affiliate_id'];


	$pnl = ($pnl_original * (-1));
	
	// Default Merchant Limit
	$max_pnl = (int)$merchant['max_pnl_monthly_amount'];
	$max_affiliate_pnl = (int)$merchant['max_pnl_monthly_amount_affiliate'];

	if(empty($AffiliatePnlLimitArray[$affiliate_id])){
		$AffiliatePnlLimitArray[$affiliate_id] = getMaxPnlMonthlyLimit($affiliate_id);
	}

	if($AffiliatePnlLimitArray[$affiliate_id] > 0){
		$max_affiliate_pnl = $AffiliatePnlLimitArray[$affiliate_id];
	}

	echo 'MAX(M:'.$max_pnl.'|A:'.$max_affiliate_pnl.') - ';

	
	if((int)$max_pnl <= 0){
		return IsPnlExceededByAffiliate($affiliate_id, $pnl, $max_affiliate_pnl, $merchant, $date);
	}
	
	// If PNL is negative
	if($pnl < 0){

		if(!empty($tradersMonthlyPnlArray[$trader_id])){
			echo 'SUM('.$tradersMonthlyPnlArray[$trader_id].') - ';
			$tradersMonthlyPnlArray[$trader_id] += $pnl;
			echo 'SUM+('.$tradersMonthlyPnlArray[$trader_id].') - ';
		}
		return IsPnlExceededByAffiliate($affiliate_id, $pnl, $max_affiliate_pnl, $merchant, $date);
	}


	if(empty($tradersMonthlyPnlArray[$trader_id])){
		$pnlSum = 0;
		$pnlSumQueryResult = function_mysql_query("SELECT SUM(amount) AS pnl_amount FROM `data_sales` WHERE rdate >= '".$currentMonthFrom."' AND rdate <= '".$currentMonthTo." 23:59:59' AND type = 'PNL' AND trader_id = '".$trader_id."' AND merchant_id = '".$merchant['id']."'");
		$pnlSum = mysql_fetch_assoc($pnlSumQueryResult);
		$tradersMonthlyPnlArray[$trader_id] = (($pnlSum['pnl_amount'])?($pnlSum['pnl_amount'] * (-1)):0);

	}

	if($tradersMonthlyPnlArray[$trader_id] > $max_pnl || ($max_affiliate_pnl > 0 AND $tradersMonthlyPnlArray[$trader_id] > $max_affiliate_pnl)){
		echo 'SUM('.$tradersMonthlyPnlArray[$trader_id].') - ';
		return true;
	}else{
		$tradersMonthlyPnlArray[$trader_id] += $pnl;
	}

	echo 'SUM('.$tradersMonthlyPnlArray[$trader_id].') - ';
	return IsPnlExceededByAffiliate($affiliate_id, $pnl, $max_affiliate_pnl, $merchant, $date);

}
/*********************/




function overrideMerchantidBySerial($loop_merchant_id=0 , $merchant_id , $serial ="",$ctag="") {
	global $merchant_id,$sxOption_MerchantId;	
	$a = array();
	if (strtolower($serial)=='sxoption') {
				$a['merchant_id'] = $sxOption_MerchantId;
				$a['ctag']= "a501-b-p";  // as ctag
			}
			else  {
				$a['merchant_id'] = $loop_merchant_id;
				$a['ctag']= $ctag;
			}
			
return $a;
}
			
			
	
	




function allowedMerchants($merchant_id = "") {
	if ($merchant_id=="" || $merchant_id=="-1")
		return false;
	return true;
}


	
function chineseToUnicode($str){
    //split word
    preg_match_all('/./u',$str,$matches);

    $c = "";
    foreach($matches[0] as $m){
            $c .= "&#".base_convert(bin2hex(iconv('UTF-8',"UCS-4",$m)),16,10);
    }
    return $c;
}
	

function tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) {

//  /filterBy/registration/from/2016-03-06/to/2016-03-07
echo $url.'<br>from: '.$scanDateFrom.'<br>to: '.$scanDateTo.'<br>page: '.$page.'<br>';
// Init cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

// Set HTTP Auth credentials
// curl_setopt($ch, CURLOPT_USERPWD, 'gg-buddies-api:a2a86b89');
curl_setopt($ch, CURLOPT_USERPWD, $api_user.':'.$api_pass);

// Report Criteria
$criteria_data = array (
	'filterBy' => 'registration',
    'from' => $scanDateFrom,
    'to' => $scanDateTo,
    'page' => $page
);
//    'affiliate' => 'f7030a4e',

// var_dump ($criteria_data);
// die();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $criteria_data);
$result = curl_exec($ch);
  curl_close($ch);
return  $result ; 

}
	
	
function overRideDynamicParameter ($freeTextParam) {
		if (isset($_GET['comment']) && !empty($_GET['comment'])) {
			return $_GET['comment'];
		}
		else
			return $freeTextParam;
}


function overrideCtagByCamp ($xml_line ='' , $camp=0,$array) { 
global $ww;
$ctag='';
			if($camp=="25555551"){
				$ctag = 'a4020-b528-p';
			}else if($camp=="2555555555555555"){
				$ctag = 'a4021-b527-p';
						
			}else{
				$ctag = $xml_line['btag'];
			}
			
			$ctagArray = array();
			$ctagArray = getBtag($ctag);
			$uid = $ctagArray['uid'];
			$banner_id = $ctagArray['banner_id'];
			$freeParam=$ctagArray['freeParam'];
			
			// echo ' : ' . $ctag . '<Br>';
			
			
			
			if ($array[$camp]['affiliateID'] && !$alternativeCtag) {
				$profile_id=  $array[$camp]['profileID'];
				$ctag = "a" . $array[$camp]['affiliateID'] . '-b'.(!empty($banner_id)?  $banner_id : "").''.(!empty($uid)? "-u" . $uid : "").'-p' . (!empty($profile_id) ? $profile_id : ""). (!empty($freeParam) ? '-f' . $freeParam : "");
			}
			
			
	return $ctag;
	
	/* 

			if ($array[$camp]['affiliateID']) {
				$profile_id=  $array[$camp]['profileID'];
				$ctag = "a" . $array[$camp]['affiliateID'] . '-b'.(!empty($banner_id)?  $banner_id : "").''.(!empty($uid)? "-u" . $uid : "").'-p' . (!empty($profile_id) ? $profile_id : ""). (!empty($freeParam) ? '-f' . $freeParam : "");

			if ($array[$camp]['affiliateID'] == 1332){
				var_dump($xml_line);
				
				var_dump($array);
				
				die ($ctag);
			}
	}
			
			
	return $ctag; */
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


function checkSaleStatus($mid, $camsID){
	
	global $api_url,$api_user,$api_pass,$current_Campaign,$ww,$pagesize,$debug;
	
	echo '<hr /><b>Getting Sale Status </b><br />';
	
	
	$fromDate = date("Y-m-d",strtotime("-3 Months"));
	$scanDateTo = date("Y-m-d",strtotime("+1 Months"));
	
	$scanDateFrom = $fromDate;
$url = $api_url.'users';
		echo 'Sale Status URL: ' . $url . '<br>';
		$page =0 ;
		$intProcessed=0;
$totalRowsFoundThisPage = 1;
		$emptyResCounter=0;
		while ($totalRowsFoundThisPage>0) {
		$totalRowsFoundThisPage=0;
				
				$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page,true) ;
				// var_dump($res);
				
				// die();
				
				
				$res = json_decode($res,TRUE);
				$xml_report = ($res['data']);
				$xml = $xml_report;
				
		$arrXmlTest = array();

		if ($debug==1) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
		if (!empty($xml))
			foreach($xml AS $xml_line) {

			
				$shouldRunOnNextPageAsWell=true;
				$totalRowsFoundThisPage++;
					$arrXmlTest[] = $xml_line;
					unset($db);
					$exist = 0;
					// var_dump($xml_line);
					// die();
					$db['email'] = "";
					$db['trader_id'] = $xml_line['userID'];
					$db['rdate'] = date("Y-m-d h:i:s", strtotime($xml_line['registrationDate']));
					$db['rdate'] = switchRdateAccordingToTradeingPlatformTime($db['rdate']);
					
					$db['saleStatus'] = $xml_line['saleStatus'] .(!empty($xml_line['userTags']) ?  ' - ' .$xml_line['userTags'] : '');
					$db['lastSaleNote'] = $xml_line['lastMessage'];
					$db['lastSaleNoteDate'] = $xml_line['lastMessageDate'] ;
					
					
					$isDemo = $xml_line['tester'];
				if ($isDemo == 1) {
				$db['type'] = 'demo'; 
				}
				
				else 
				
			/* // die ('demo: ' . $type. '<br>');
			if (strtolower($type)=='true') {
				$db['type'] = 'demo'; 
			} else */
				{
				$db['type'] = 'real';
			}
			
			
					
					if ($db['trader_id']==null) {
						echo 'Trader id is null !!!!!!<br>';
						continue;
					}
					
					$saleStatus = $db['saleStatus'];
					$traderID = $db['trader_id'];
					
					
					$traderName = (!empty($xml_line['firstName'])?$xml_line['firstName'] : '') . ' ' . (!empty($xml_line['lastName'])?$xml_line['lastName'] : '');
					
					// echo $tradeID.'<br>';
					
					
					if((!empty($saleStatus) || !empty($db['lastSaleNote'])) AND $traderID ){ //|| $db['type']=='demo'){
					
					//	$exist = mysql_fetch_assoc(mysql_query('SELECT id FROM data_sales WHERE merchant_id = '.$ww['id'] .' and trader_id='.$traderID.' AND (type="deposit" OR type=1) LIMIT 0,1'));
						//if(!$exist['id'])
						{
							// $pp = 'UPDATE data_reg SET type= "' . $db['type']  .'" , lastSaleNote ="' .mysql_real_escape_string($db['lastSaleNote']) .'",lastSaleNoteDate ="' .$db['lastSaleNoteDate'] .'", saleStatus="'.mysql_real_escape_string($saleStatus).'" WHERE merchant_id = '.$ww['id'] .' and trader_id="'.$traderID.'"';
							$pp = 'UPDATE data_reg SET  lastSaleNote ="' .mysql_real_escape_string($db['lastSaleNote']) .'",lastSaleNoteDate ="' .$db['lastSaleNoteDate'] .'", saleStatus="'.mysql_real_escape_string($saleStatus).'", trader_alias="'.mysql_real_escape_string($traderName).'" WHERE merchant_id = '.$ww['id'] .' and trader_id="'.$traderID.'"';
							if ($debug>1)
								echo $pp.'<br>';
							/* if ($db['trader_id']==2168745)
								die($pp); */
							
							mysql_query($pp) OR die(mysql_error());
							
							echo 'TraderID: '.$traderID.' | saleStatus: '.$saleStatus.' | rdate: '.$db['rdate'].'<BR>';
						//}else{
							$intProcessed++;
							//echo 'TraderID: '.$traderID.' | saleStatus: '.$saleStatus.' | rdate: '.$rdate.' ------------------------ <font color="RED">ALREADY MADE A DEPOST!</font><BR>';
						}
					}
					if (strtolower($db['type'])=='demo' && !empty($traderID) && !empty($mid)) {
								mysql_query("UPDATE data_reg SET type='". $db['type'] ."' WHERE merchant_id = ".$mid ." and type='real' and trader_id='".$traderID."'");
					}
					
					$countXML++;
				}
				$page++;
			
			echo 'Total Processed: ' . $intProcessed . '<br>';
			
			if ($emptyResCounter<2){
				if ($totalRowsFoundThisPage==0)
					$emptyResCounter++;
				$totalRowsFoundThisPage=1;
			}else
				$totalRowsFoundThisPage=0;
			
		}
				mysql_query('UPDATE merchants SET lastSaleStatusUpdate="'.date("Y-m-d H:i:s").'" WHERE id='.$mid) OR die(mysql_error());
				echo '--------------------------------------------------------------------------------<BR>';
}


	
function checkCurrencies(){
	global $set;
	$currentTime = new DateTime();
	$startTime = new DateTime('01:00');
	$endTime = new DateTime('04:00');
	if (($currentTime >= $startTime && $currentTime <= $endTime) OR ($_REQUEST['currency'])) {
		$ch = curl_init(); 
        $url = "http://".$set->http_host."/getCurrency.php";
		// die ($url);
		curl_setopt($ch, CURLOPT_URL,$url ); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch); 
		echo $output;
	}else{
		echo '<BR>Currencies will be update in other time.<BR>';
	}
}

function processClicksData(){
	global $set,$siteURL;
	$currentTime = date('Y-m-d');
	$exp_mdate=explode("-",$currentTime);
	$currentTime = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));
		
	if ($set->lastClicksCronJobRunningDate<$currentTime) {
		$ch = curl_init(); 
		// var_dump($set);
		// die();
        curl_setopt($ch, CURLOPT_URL, "http://".$set->sitebaseurl."/crons/cron_clicks.php?runthis=2"); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch); 
		echo $output;
		mysql_query("update settings set lastClicksCronJobRunningDate = '" . date('Y-m-d') . "' where id = 1;");
	}else{
		echo '<BR>Clicks CronJob will be update in other time.<BR>';
	}
}



processClicksData();
	
	
echo '<style type="text/css">html,body { font-size: 11px; font-family: Tahoma; } </style>';


if ($m_date) {
	$exp_mdate=explode("-",$m_date);
	if ($_GET['yearly']) {
		$scanDateFrom = date("Y-01-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-01-01", strtotime("+1 Year",strtotime($scanDateFrom)));//.' 23:59:59';
		$totalPage = 1000;
	} elseif ($monthly) {
		$scanDateFrom = date("Y-m-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		
		if ($lastmonth)
			$scanDateTo = date("Y-m-01", strtotime("+2 Month",strtotime($scanDateFrom)));//.' 23:59:59';
		else
			$scanDateTo = date("Y-m-01", strtotime("+1 Month",strtotime($scanDateFrom)));//.' 23:59:59';
		$totalPage = 1000;
	} else {
		// $scanDateFrom = date("Y-m-d", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateFrom = date("Y-m-d", strtotime("-1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));
		$scanDateTo = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));//.' 23:59:59';
		}
	} else {
		$date= date("Y-m-d");
		$exp_mdate=explode("-",$date);
		$scanDateFrom = date("Y-m-d", strtotime("-1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));
		$scanDateTo = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));
		
			// $scanDateFrom = date("Y-m-d", strtotime("-1 Day"));//.' 00:00:00';
	// $scanDateTo = date("Y-m-d", strtotime("+1 Day"));//.' 00:00:00';
	//$scanDateTo = date("Y-m-d");//.' 23:59:59';
	}
if (!$totalPage) $totalPage = 3000;


// die ('gg: ' . $forceCheckSaleStatus);
if ($forceCheckSaleStatus)  {
	
$toCheckSaleStatus =   true;
}
else {
	
$toCheckSaleStatus =   (date("Y-m-d H:i:s",strtotime("15 Minutes"))>$ww['lastSaleStatusUpdate']) ;
}


//die ('tocheck: ' . $ww['lastSaleStatusUpdate']);
echo 'From: <u>'.$scanDateFrom.'</u> To: <u>'.$scanDateTo.'</u>';

$campscount = 0;

//var_dump($campaignIDs);
//die ('done');

	
	foreach ($campaignIDs as $key => $value) {
		
		
		if($toCheckSaleStatus && !$ignoreSaleStatus){
		//	echo '<BR>About to run checkSaleStatus';
		// die ('gerg');
			checkSaleStatus($ww['id'],$camsID);
		}
		
		
	//for ($camsID=0; $camsID < count($campaignIDs); $camsID++) {
		$current_Campaign = $value;
//=========================================lead ==================================================
	echo '<span style="color:green"><br>Campaign:  '  .$current_Campaign . '<br></span>';
		$campscount++;
		 if($campscount % 4 ==0) {
			 usleep(500000);
		 }
		 $totalRowsFoundSoFar = 0;
		for ($page=1; $page<=$totalPage; $page++)	{
		$shouldRunOnNextPageAsWell = false;
		echo '<hr /><b>Connecting to Lead\'s & Database (Campaign ID:'.$current_Campaign.') <span style="color:blue">Page: <u>'.$page.'</u>...</span></b><br />';
		
		
		/* if (!$current_Campaign) {
			
			continue;
		}
		 */
		

	
		
		
		$url = $api_url.'leads';
		echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) ;
		$res = json_decode($res,TRUE);
		$xml_report = ($res['data']);
		$xml = $xml_report;
		
		
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug>1) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
			
		if (!empty($xml))
			foreach($xml AS $xml_line) {
			
			
			
		$shouldRunOnNextPageAsWell=true;
		$totalRowsFoundThisPage++;
			$arrXmlTest[] = $xml_line;
			unset($db);
			$exist = 0;
			// die();
			$db['email'] = "";
			$db['trader_id'] = $xml_line['userID'];
			
		
			
			if ($db['trader_id']==null) {
				echo 'Trader id is null !!!!!!<br>';
				continue;
			}
			
			$thedate = $xml_line['registrationDate'];
			if (empty($thedate))
				$thedate = $xml_line['lastModifiedDate'];
				
			$db['rdate'] = date("Y-m-d h:i:s", strtotime($thedate));
			$db['rdate'] = switchRdateAccordingToTradeingPlatformTime($db['rdate']);
			
			$userTag = (!empty($xml_line['userTags']) ?  ' - ' .$xml_line['userTags'] : '');
			$db['saleStatus'] = $xml_line['saleStatus'] .$userTag;
			

			
			
			//$exp = explode ('.',str_replace('"','',$xml_line['initials']));
			
			$db['firstname'] = $xml_line['firstName'];
			$db['lastname'] = $xml_line['lastName'];

			
			
			$landingParams = json_decode($xml_line['landingParams']);
			$outParam = $landingParams->btag;
			if (empty($outParam)) {
				$outParam = $landingParams->serial;
			}
			$ctagRaw['btag'] = $outParam;
			
			
			$campaign_id_from_CRM = $landingParams->a_aid;
			
			
			$db['ctag'] = overrideCtagByCamp($ctagRaw,$campaign_id_from_CRM,$autoRelate_campaignIDs);
			$db['ctag'] = mysql_real_escape_string($db['ctag']);
			$db['serial'] = $landingParams->serial;
			
			
			$a=overrideMerchantidBySerial($ww['id'],$merchant_id,$db['serial'],$db['ctag']);
			$merchant_id = $a['merchant_id'];
			$db['ctag'] = $a['ctag'];

			
			if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;
			
			$dupQry = "SELECT id,type FROM data_reg WHERE merchant_id = " . $merchant_id . " AND trader_id='".$db['trader_id']."' LIMIT 1";
			// die($dupQry);
			$chkDouble=mysql_fetch_assoc(mysql_query($dupQry));
			
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
					$ctagCountry=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					$db['freeParam']= overRideDynamicParameter($db['freeParam']);
                                        $db['freeParam5'] = '';
                                        if(!empty($landingParams)){
                                            $db['freeParam5']= json_encode($landingParams);
                                        }

				
				if(!empty($xml_line['affiliateID'])){
				    $db['affiliate_id']=$xml_line['affiliateID'];
				}
				
				$country = strtoupper($xml_line['country']);
				if (!empty($country)) {
					if (strlen($country)>2) {
						$db['country'] = $countriesArr->$country;
					}
				else {
						$db['country'] = $country;
				}
				}
				
				
				
			if(strtolower($db['country'])=='any' OR empty($db['country'])){
				$gc = str_replace(Array("\\","'","`"),Array("","",""),strtoupper($ctagCountry));
				$db['country'] = str_replace(Array("\\","'","`"),Array("","",""),$gc);
					if (!empty($db['country']) &&  strlen($db['country'])>2)
					$db['country'] = $countriesArr->$db['country'];
			}
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				}
			$db['group_id'] = $getAffiliate['group_id'];
			
			$db['trader_alias'] = str_replace(Array("\\","'","`"),Array("","",""),$db['firstname'].' '.$db['lastname']);
			
		/* 	$type = $xml_line['apiaccountview_isDemo'];
			 */
			
			$isDemo = $xml_line['tester'];
				if ($isDemo == 1) {
				$db['type'] = 'demo'; 
				}
				
				else 
				
			/* // die ('demo: ' . $type. '<br>');
			if (strtolower($type)=='true') {
				$db['type'] = 'demo'; 
			} else */
				{
				$db['type'] = 'real';
			}
			
			if ($chkDouble['type'] != $db['type']) {
				$db['id'] = $chkDouble['id'];
				$exist = 0;
			}
		


		
			if (count($db) > 1) {
				if (!$exist)
					if (!$chkDouble['id']) {
		
		
		
		
		if ($DynamicTrackertActive){
						$DTresp = GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']);
						if (is_array($DTresp)){
							$p2 = $DTresp['p2'];
							$p3 = $DTresp['p3'];
							$p4 = $DTresp['p4'];
							$p5 = $DTresp['p5'];
						}
						else
							$p2 = $DTresp;
					}
					
		$subid =  $p2;
						$db['freeParam2'] = $subid;
						
						/* echo '<pre>';
						print_r($db);
						die(); */
						$qry = "INSERT INTO data_reg(merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,saleStatus,
									country,trader_id,trader_alias,type,freeParam,freeParam2,freeParam5, campaign_id,uid,email) 
									VALUES
							(" . $merchant_id . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
							'".$db['profile_id']."','".$db['saleStatus']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
							'".$db['freeParam']."','".$db['freeParam2']."','".$db['freeParam5']."', '" . $campaign_id_from_CRM . "', '" . $db['uid'] . "', '" . $db['email'] . "')";
							
							// die ($qry);
						mysql_query($qry) ;
						
						$status = 'Inserted!';
						$reg_total++;
						
			
			// continue;
 
// var_dump($db);
// die();
			 

						
						
						
					// $subid =  $DynamicTrackertActive  ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
					$pixelurl = $siteURL. 'pixel.php?act=account&ctag='.$db['ctag'].'&merchant_id='.$merchant_id.'&trader_id='.$db['trader_id'].'&trader_alias='. str_replace(' ','%20',$db['trader_alias']) . '&subid=' . $subid;
							$pixelContent  = firePixel($pixelurl);
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
							if (strtolower($chkDouble['type'])=='real' && strtolower($db['type'])=='demo') {
								mysql_query("UPDATE data_reg SET type='".$db['type']."' WHERE merchant_id = ".$merchant_id ." and id='".$db['id']."'");
								$status = 'Updated Customer ('.$db['type'].')!';
							}
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
			
			if ($shouldRunOnNextPageAsWell == false)
				break;
		}	
		//die(print_r($arrXmlTest));
		//die(print_r($arrXmlTest));
		echo 'Total Processed: ' . $intProcessed . '<br>';
		echo '<hr /><b>Done!</b><br />';
		
//=========================================lead ==================================================
//=========================================reg ==================================================
			echo '<span style="color:green"><br>Campaign:  '  .$current_Campaign . '<br></span>';
			
			
		$campscount++;
		 if($campscount % 4 ==0) {
			 usleep(500000);
		 }
		 $totalRowsFoundSoFar = 0;
		for ($page=1; $page<=$totalPage; $page++)	{
		$shouldRunOnNextPageAsWell = false;
		echo '<hr /><b>Connecting to Customers\'s & Database (Campaign ID:'.$current_Campaign.') <span style="color:blue">Page: <u>'.$page.'</u>...</span></b><br />';
		
		
		if (!$current_Campaign) {
			continue;
		}
		
		
		/* if($toCheckSaleStatus){
		//	echo '<BR>About to run checkSaleStatus';
			checkSaleStatus($ww['id'],$camsID);
		} */
		
		// $url = $api_url.'registrations';
		$url = $api_url.'users';
		echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page,true) ;
		// var_dump($res);
		
		
		// die();
		
		
		$res = json_decode($res,TRUE);
		$xml_report = ($res['data']);
		$xml = $xml_report;
		
		
		
/* 		
		$a = substr($xml_report, 0, strrpos( $xml_report, '}'));
		$a = substr($a, 0, strrpos( $a, ']'));
		$exp = explode('[', $a);
		$a= $exp[1];
		$xml_report = mb_convert_encoding($xml_report,'UTF-8','UTF-8'); 
		$xml_report = json_decode($xml_report,true); */
	
	
	// die();
	
	
		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug>1) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
			
		
		// $xml = $xml_report['result'];
		// var_dump($xml);
		// die();
		
				
		if (!empty($xml))
		foreach($xml AS $xml_line) {


	
		
	
		$shouldRunOnNextPageAsWell=true;
		$totalRowsFoundThisPage++;
			$arrXmlTest[] = $xml_line;
			unset($db);
			$exist = 0;
			// var_dump($xml_line);
			// die();
			$db['email'] = "";
			$db['trader_id'] = $xml_line['userID'];
			$db['rdate'] = date("Y-m-d h:i:s", strtotime($xml_line['registrationDate']));
			$db['rdate'] = switchRdateAccordingToTradeingPlatformTime($db['rdate']);
			
			$db['saleStatus'] = $xml_line['saleStatus'] .(!empty($xml_line['userTags']) ?  ' - ' .$xml_line['userTags'] : '');
			$db['lastSaleNote'] = mysql_real_escape_string($xml_line['lastMessage']);
			$db['lastSaleNoteDate'] = $xml_line['lastMessageDate'] ;
			
			
			if ($db['trader_id']==null) {
				echo 'Trader id is null !!!!!!<br>';
				continue;
			}
			
			$db['firstname'] = $xml_line['firstName'];
			$db['lastname'] = $xml_line['lastName'];
			
			
			$landingParams = json_decode($xml_line['landingParams']);
			
			/* if ($db['trader_id']=='1619862'){
				echo '<br><Br>';
				echo '<br><Br>';
					var_dump($landingParams);
					die();
			} */
			$outParam = $landingParams->btag;
			if (empty($outParam)) {
				$outParam = $landingParams->serial;
			}
			$ctagRaw['btag'] = $outParam;
			$campaign_id_from_CRM = $landingParams->a_aid;
			
			$db['ctag'] = overrideCtagByCamp($ctagRaw,$campaign_id_from_CRM,$autoRelate_campaignIDs);
			$db['serial'] = $landingParams->serial;
			
			
			$a=overrideMerchantidBySerial($ww['id'],$merchant_id,$db['serial'],$db['ctag']);
			$merchant_id = $a['merchant_id'];
			$db['ctag'] = $a['ctag'];
			
			
			if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;
			
			$dupQry = "SELECT id,type FROM data_reg WHERE merchant_id = " . $merchant_id . " AND trader_id='".$db['trader_id']."' LIMIT 1";
			
			$chkDouble=mysql_fetch_assoc(mysql_query($dupQry));
			
			if ($chkDouble['id']) {
				if ($chkDouble['type'] == "lead") {
					$db['id'] = $chkDouble['id'];
					$db['type'] = "real";
					$exist=1;
				} else {
				}
			}
			
			
			
					$db['ctag'] = str_replace("--","-",$db['ctag']);
					$ctag= $db['ctag'];
					$ctagArray = array();
					$ctagArray = getBtag($ctag);
					$db['affiliate_id']=$ctagArray['affiliate_id'];
					$db['banner_id']=$ctagArray['banner_id'];
					$db['profile_id']=$ctagArray['profile_id'];
					// $db['country']=$ctagArray['country'];
					$ctagCountry=$ctagArray['country'];
					$db['uid']=$ctagArray['uid'];
					$db['freeParam']=$ctagArray['freeParam'];
					$db['freeParam']= overRideDynamicParameter($db['freeParam']);
                                        $db['freeParam5'] = '';
                                        if(!empty($xml_line['landingParams'])){
                                            $db['freeParam5'] = $xml_line['landingParams'];
                                        }
                                        
			
				if(!empty($xml_line['affiliateID'])){
				    $db['affiliate_id']=$xml_line['affiliateID'];
				}
			
				$country = strtoupper($xml_line['country']);
				
				
				if (!empty($country)) {
					if (strlen($country)>2) {
						$db['country'] = $countriesArr->$country;
					}
				else {
						$db['country'] = $country;
				}
				}
					
				
			/* if ($db['trader_id'] == '3255648'){
				var_dump	($xml_line);
				echo '<br><Br>';
				var_dump	($countriesArr);
				echo '<br><Br>';
				var_dump	($db['country']);
				die();
				} */
				
			if(strtolower($db['country'])=='any' OR empty($db['country'])){
				$gc = str_replace(Array("\\","'","`"),Array("","",""),strtoupper($ctagCountry));
				$db['country'] = str_replace(Array("\\","'","`"),Array("","",""),$gc);
				if (!empty($db['country']) &&  strlen($db['country'])>2)
					$db['country'] = $countriesArr->$db['country'];
			}
			
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
				$db['affiliate_id'] = $defaultAffiliateID;
				$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
				}
			$db['group_id'] = $getAffiliate['group_id'];
			
			$db['trader_alias'] = str_replace(Array("\\","'","`"),Array("","",""),$db['firstname'].' '.$db['lastname']);
			
		/* 	$type = $xml_line['apiaccountview_isDemo'];
			 */
			
			$isDemo = $xml_line['tester'];
				if ($isDemo == 1) {
				$db['type'] = 'demo'; 
				}
				
				else 
				
			/* // die ('demo: ' . $type. '<br>');
			if (strtolower($type)=='true') {
				$db['type'] = 'demo'; 
			} else */
				{
				$db['type'] = 'real';
			}
			
			if ($chkDouble['type'] != $db['type']) {
				$db['id'] = $chkDouble['id'];
				$exist = 0;
			}
		

		/* if ($db['trader_id']=="3241886"){
			
	$subid =  $DynamicTrackertActive  ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
	var_dump($db);
	echo '<br>';
	echo '<br>';
	var_dump($subid);
	die();
	
			
		} */
			

		
			if (count($db) > 1) {
				

				if (!$exist)
				{
					if (!$chkDouble['id']) {
						/* echo '<pre>';
						print_r($db);
						die(); */

		if ($DynamicTrackertActive){
						$DTresp = GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']);
						if (is_array($DTresp)){
							$p2 = $DTresp['p2'];
							$p3 = $DTresp['p3'];
							$p4 = $DTresp['p4'];
							$p5 = $DTresp['p5'];
						}
						else
							$p2 = $DTresp;
					}
					
		$subid =  $p2;
						$db['freeParam2'] = $subid;
						
			
						$qry = "INSERT INTO data_reg(merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,saleStatus,lastSaleNote,lastSaleNoteDate,
									country,trader_id,trader_alias,type,freeParam,freeParam2,freeParam5, campaign_id,uid,email) 
									VALUES
							(" . $merchant_id . ",'".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
							'".$db['profile_id']."','".$db['saleStatus']."','".$db['lastSaleNote']."','".$db['lastSaleNoteDate']."','".$db['country']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
							'".$db['freeParam']."','".$db['freeParam2']."','".$db['freeParam5']."', '" . $campaign_id_from_CRM . "', '" . $db['uid'] . "', '" . $db['email'] . "')";
						
							// die ($qry);
						mysql_query($qry) ;
						
						$status = 'Inserted!';
						$reg_total++;
						
			
			// continue;
 
// var_dump($db);
// die();
			 

						
						
						
			//		$subid =  $DynamicTrackertActive  ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
					$pixelurl = $siteURL. 'pixel.php?act=account&ctag='.$db['ctag'].'&merchant_id='.$merchant_id.'&trader_id='.$db['trader_id'].'&trader_alias='. str_replace(' ','%20',$db['trader_alias']) . '&subid=' . $subid;
							$pixelContent  = firePixel($pixelurl);
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
						mysql_query("UPDATE data_reg SET type='".$db['type']."' WHERE merchant_id = ".$merchant_id ." and id='".$db['id']."'");
						$status = 'Updated Customer ('.$db['type'].')!';
						}
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
			
			if ($shouldRunOnNextPageAsWell == false)
				break;
		}	
		//die(print_r($arrXmlTest));
		//die(print_r($arrXmlTest));
		echo 'Total Processed: ' . $intProcessed . '<br>';
		echo '<hr /><b>Done!</b><br />';
		######################### [ CSV REG ] #########################

		
    ######################### [ Deposits + Withdrawals ] #########################
    for ($page=1; $page<=$totalPage; $page++)	{
    	$shouldRunOnNextPageAsWell = false;
    $totalRowsFoundSoFar=0;
    //for ($page=0; $page<=$totalPage; $page++) 
    
    echo '<hr /><b>Connecting to <span style="color:blue">Transactions + Withdrawals</span> Database Page: <u>'.$page.'</u>...</b><br />';
    $url = $api_url.'transactions';
    	echo 'url : ' . $url . '<br>';
    		
    		$intProcessed=0;
    		// $xml_report=doPost($url);
    	$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) ;
    	
    		if ($debug >0){ 
    			echo '<br>res: '; echo $res;
    		}
    	
    		$res = json_decode($res,TRUE);
    		$xml_report = ($res['data']);
    		$xml = $xml_report;
    		
    		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
    		$arrXmlTest = array();
    		
    		$totalRowsFoundThisPage = 0;
    		if ($debug >0){ 
    			echo '<br>debug: ' ; var_dump($xml_report); 
    		}
    		
    	
    	
    if (isset($xml_report['result']) && $xml_report['result']==null) {
    	echo 'No Result..<br>';
    }
    	else
    		if (count($xml)>0)
    		foreach($xml AS $xml_line) {	
    			
    						
    						$tranzExist=0;
    						$shouldRunOnNextPageAsWell=true;
    						if ($debug>1) {
    							var_dump($xml_line);
    							echo '<br>';
    						}
    						$db['trader_id'] = $xml_line['userID'];
    						$db['tranz_id'] = $xml_line['recordID'];
    						$db['rdate'] = $xml_line['date'];
    						// $chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag,trader_id,merchant_id FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and  trader_id='".$db['trader_id']."' LIMIT 1"));
    						$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,merchant_id,ctag,trader_id,merchant_id FROM data_reg WHERE  1=1  and  trader_id='".$db['trader_id']."' AND merchant_id = ".$ww['id'] ." LIMIT 1"));
    						//var_dump($chkTrader);
    						if (!$chkTrader['id']) {
    							echo 'Error:: Trader not found for this trader: ' . $db ['trader_id'] . '<br>';
    							continue;
    						}
    						$merchant_id = $chkTrader['merchant_id'];
    						if (!allowedMerchants($merchant_id)) {
    							echo 'Error:: Record Not Allowed<Br>';
    							continue;
    						}
    						$db['type'] = $xml_line['type'];//'deposit';
    						if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") {
    								echo 'Error:: Problematic Type: ' . $db['type'] . '<br>';
    								continue;
    						}
    						
    						if(!empty($xml_line['convert_to_usd'])){
    							$db['amount'] = abs($xml_line['convert_to_usd']);
    						}else{
    							$db['amount'] = $xml_line['amount'];
        						$coin = strtoupper($xml_line['currency']);
							$db['amount'] = abs(getUSD($db['amount'],$coin));
    						}
    						
    						
							$traderInfo = mysql_fetch_assoc(mysql_query("SELECT freeParam,freeParam2, ctag,ftdamount,initialftdtranzid FROM data_reg WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."'"));
							if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
								else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
							if (!ctagValid($db['ctag'])) {
								$db['ctag'] = $defaultBtag;
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
    						
    						
    						
    						$db_table_name = "data_sales";
    						if (
                                $db['type']=='deposit' && $set->hidePendingProcessHighAmountDeposit == 0 &&
                                (
                                    (
                                        $db['amount'] >= $set->pendingDepositsAmountLimit &&
                                        ($pendingDespositArrayLoaded && empty($pendingDepositExcludeAffiliatesArray[$db['affiliate_id']]))
                                    ) ||
                                    ($pendingDespositArrayLoaded && !empty($pendingDepositIncludeAffiliatesArray[$db['affiliate_id']]))
                                )
    						){
                                $db_table_name = "data_sales_pending";
                            }
    						
    						// check if record exists on data_sales or data_sales_pending
    						if ($db['type']=='deposit' && $set->hidePendingProcessHighAmountDeposit==0) {
    							$qrTranz = "SELECT id,type,tranz_id FROM data_sales_pending WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' 	AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'
    											union
    											SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' 	AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'
    								";
    						}else {
    								$qrTranz = "SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."' LIMIT 1";
    						}
    						
    						
    							if ($db['type']=='deposit' && $debug==1){
    								echo $qrTranz.'<Br>';
    							}
    							
    							$chkExistTranz=mysql_fetch_assoc(mysql_query($qrTranz));
    							if ($chkExistTranz['id']) {
    								$tranzExist = 1;
    							}
    							if ($debug==1)
    								echo 'Trader id: ' .$db['trader_id']  .'   |      Type: ' . $db['type'] . '<br>';
    							

    							$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
    							if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
    								$db['affiliate_id'] = $defaultAffiliateID;
    								$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
    								}
    							$db['group_id'] = $getAffiliate['group_id'];
    							
    							if (count($db) > 1 AND !$tranzExist) {
    										$q = "INSERT INTO ".$db_table_name." (dummySource,currentDate,merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,
    										trader_id,trader_alias,type,amount,freeParam,freeParam2, campaign_id) 
    										VALUES
    										(33, '".date('Y-m-d H:i:s')."',".$merchant_id.", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
    										'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
    										'".$db['amount']."','".$traderInfo['freeParam']."','".$traderInfo['freeParam2']."', '" . $current_Campaign . "')";
    										
    										echo $q .'<br>';
    										
    										mysql_query($q	) or die(mysql_error());
     										if ($traderInfo['ftdamount']==0 && $db['type']=='deposit' && $traderInfo['initialftdtranzid']=='' && $set->hidePendingProcessHighAmountDeposit==0 && $db['amount']<=$set->pendingDepositsAmountLimit) {
    											$GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from ".$db_table_name." where 
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
    							//	$subid =  $DynamicTrackertActive ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
    								$pixelurl = $siteURL. 'pixel.php?act=deposit&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&merchant_id='.$merchant_id.'&tranz='.$db['tranz_id'].'&type='.$db['type'].'&currency='.$db['coin'].'&amount='.$db['amount'].'&subid='.$subid;
    								$pixelContent  = firePixel($pixelurl);
    								if (strlen($pixelContent)>0) {
    									echo 'Firing Sale Pixel, Affiliate_ID = ' . $db['affiliate_id'].' -- '.$pixelurl.'<br>';
    									echo $pixelContent;
    								}
    								flush();
    							} else {
    								if ($debug >0) 
    									echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
    							}
    							$intProcessed++;
    							if ($intProcessed % 100 == 0) {
    								echo '... , ';
    							}
    							$totalRowsFoundThisPage++;
    			}
    		if ($shouldRunOnNextPageAsWell == false)
    				break;
    	
            if ($intProcessed>0)
    	        echo 'Total processed so far: ' . $intProcessed . '<br>';
    	}
    ######################### [ Deposits + Withdrawals ] #########################	

        
    ######################### [ Deposits + Withdrawals CFD ] #########################
    for ($page=1; $page<=$totalPage; $page++)	{
    	$shouldRunOnNextPageAsWell = false;
    $totalRowsFoundSoFar=0;
    //for ($page=0; $page<=$totalPage; $page++) 
    
    echo '<hr /><b>Connecting to <span style="color:blue">Transactions + Withdrawals CFD</span> Database Page: <u>'.$page.'</u>...</b><br />';
    $url = $api_url.'transactions-fx';
    	echo 'url : ' . $url . '<br>';
    		
    		$intProcessed=0;
    		// $xml_report=doPost($url);
    	$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) ;
    	
    	
    		$res = json_decode($res,TRUE);
    		$xml_report = ($res['data']);
    		$xml = $xml_report;
    		
    		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
    		$arrXmlTest = array();
    		
    		$totalRowsFoundThisPage = 0;
    		if ($debug >0) 		{ 
    			echo '<br>debug: ' ; var_dump($xml_report); 
    		}
    		
    	
    	
    if (isset($xml_report['result']) && $xml_report['result']==null) {
    	echo 'No Result..<br>';
    }
    	else
    		if (count($xml)>0)
    		foreach($xml AS $xml_line) {	
    			
    						
    						$tranzExist=0;
    						$shouldRunOnNextPageAsWell=true;
    						if ($debug>1) {
    							var_dump($xml_line);
    							echo '<br>';
    						}
    						$db['trader_id'] = $xml_line['userID'];
    						$db['rdate'] = $xml_line['date'];
    						$db['tranz_id'] = date("Ymd",strtotime($db['rdate'])).$xml_line['recordID'];
    						// $chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag,trader_id,merchant_id FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and  trader_id='".$db['trader_id']."' LIMIT 1"));
    						$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,merchant_id,ctag,trader_id,merchant_id FROM data_reg WHERE  1=1  and  trader_id='".$db['trader_id']."' AND merchant_id = ".$ww['id'] ." LIMIT 1"));
    						//var_dump($chkTrader);
    						if (!$chkTrader['id']) {
    							echo 'Error:: Trader not found for this trader: ' . $db ['trader_id'] . '<br>';
    							continue;
    						}
    						$merchant_id = $chkTrader['merchant_id'];
    						if (!allowedMerchants($merchant_id)) {
    							echo 'Error:: Record Not Allowed<Br>';
    							continue;
    						}
    						$db['type'] = $xml_line['type'];//'deposit';
    						if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") {
    								echo 'Error:: Problematic Type: ' . $db['type'] . '<br>';
    								continue;
    						}
    						$db['amount'] = $xml_line['amount'];
    						$coin = strtoupper($xml_line['currency']);

    						if(!empty($xml_line['convert_to_usd'])){
								$coin = 'USD';
								$db['amount'] = abs($xml_line['convert_to_usd']);
    						}

    						$db['amount'] = abs(getUSD($db['amount'],$coin));
    						
    						
							$traderInfo = mysql_fetch_assoc(mysql_query("SELECT freeParam,freeParam2, ctag,ftdamount,initialftdtranzid FROM data_reg WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."'"));
							if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
								else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
							if (!ctagValid($db['ctag'])) {
								$db['ctag'] = $defaultBtag;
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
    						
    						
    						
    						$db_table_name = "data_sales";
    						if (
                                $db['type']=='deposit' && $set->hidePendingProcessHighAmountDeposit == 0 &&
                                (
                                    (
                                        $db['amount'] >= $set->pendingDepositsAmountLimit &&
                                        ($pendingDespositArrayLoaded && empty($pendingDepositExcludeAffiliatesArray[$db['affiliate_id']]))
                                    ) ||
                                    ($pendingDespositArrayLoaded && !empty($pendingDepositIncludeAffiliatesArray[$db['affiliate_id']]))
                                )
    						){
                                $db_table_name = "data_sales_pending";
                            }
    						
    						// check if record exists on data_sales or data_sales_pending
    						if ($db['type']=='deposit' && $set->hidePendingProcessHighAmountDeposit==0) {
    							$qrTranz = "SELECT id,type,tranz_id FROM data_sales_pending WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' 	AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'
    											union
    											SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' 	AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'
    								";
    						}else {
    								$qrTranz = "SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."' LIMIT 1";
    						}
    						
    						
    							if ($db['type']=='deposit' && $debug==1){
    								echo $qrTranz.'<Br>';
    							}
    							
    							$chkExistTranz=mysql_fetch_assoc(mysql_query($qrTranz));
    							if ($chkExistTranz['id']) {
    								$tranzExist = 1;
    							}
    							if ($debug==1)
    								echo 'Trader id: ' .$db['trader_id']  .'   |      Type: ' . $db['type'] . '<br>';
    							
    
    
    
    							$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
    							if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
    								$db['affiliate_id'] = $defaultAffiliateID;
    								$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
    								}
    							$db['group_id'] = $getAffiliate['group_id'];
    							
    							if (count($db) > 1 AND !$tranzExist) {
    										$q = "INSERT INTO ".$db_table_name." (dummySource,currentDate,merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,
    										trader_id,trader_alias,type,amount,freeParam,freeParam2, campaign_id) 
    										VALUES
    										(33, '".date('Y-m-d H:i:s')."',".$merchant_id.", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
    										'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
    										'".$db['amount']."','".$traderInfo['freeParam']."','".$traderInfo['freeParam2']."', '" . $current_Campaign . "')";
    										
    										echo $q .'<br>';
    										
    										mysql_query($q	) or die(mysql_error());
     										if ($traderInfo['ftdamount']==0 && $db['type']=='deposit' && $traderInfo['initialftdtranzid']=='' && $set->hidePendingProcessHighAmountDeposit==0 && $db['amount']<=$set->pendingDepositsAmountLimit) {
    											$GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from ".$db_table_name." where 
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
    							//	$subid =  $DynamicTrackertActive ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
    								$pixelurl = $siteURL. 'pixel.php?act=deposit&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&merchant_id='.$merchant_id.'&tranz='.$db['tranz_id'].'&type='.$db['type'].'&currency='.$db['coin'].'&amount='.$db['amount'].'&subid='.$subid;
    								$pixelContent  = firePixel($pixelurl);
    								if (strlen($pixelContent)>0) {
    									echo 'Firing Sale Pixel, Affiliate_ID = ' . $db['affiliate_id'].' -- '.$pixelurl.'<br>';
    									echo $pixelContent;
    								}
    								flush();
    							} else {
    								if ($debug >0) 
    									echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
    							}
    							$intProcessed++;
    							if ($intProcessed % 100 == 0) {
    								echo '... , ';
    							}
    							$totalRowsFoundThisPage++;
    			}
    		if ($shouldRunOnNextPageAsWell == false)
    				break;
    	
            if ($intProcessed>0)
    	        echo 'Total processed so far: ' . $intProcessed . '<br>';
    	}
    ######################### [ Deposits + Withdrawals CFD ] #########################
	

    ######################### [ MT5 Deposits + Withdrawals ] #########################
    for ($page=1; $page<=$totalPage; $page++)	{
    	$shouldRunOnNextPageAsWell = false;
    $totalRowsFoundSoFar=0;
    //for ($page=0; $page<=$totalPage; $page++) 
    
    echo '<hr /><b>Connecting to <span style="color:blue">Transactions + Withdrawals</span> Database Page: <u>'.$page.'</u>...</b><br />';
    $url = $api_url.'transactions-forex';
    	echo 'url : ' . $url . '<br>';
    		
    		$intProcessed=0;
    		// $xml_report=doPost($url);
    	$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) ;
    	
    		if ($debug >0){ 
    			echo '<br>res: '; echo $res;
    		}
    	
    		$res = json_decode($res,TRUE);
    		$xml_report = ($res['data']);
    		$xml = $xml_report;
    		
    		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
    		$arrXmlTest = array();
    		
    		$totalRowsFoundThisPage = 0;
    		if ($debug >0){ 
    			echo '<br>debug: ' ; var_dump($xml_report); 
    		}
    		
    	
    	
    if (isset($xml_report['result']) && $xml_report['result']==null) {
    	echo 'No Result..<br>';
    }
    	else
    		if (count($xml)>0)
    		foreach($xml AS $xml_line) {	
    			
    						
    						$tranzExist=0;
    						$shouldRunOnNextPageAsWell=true;
    						if ($debug>1) {
    							var_dump($xml_line);
    							echo '<br>';
    						}
    						$db['trader_id'] = $xml_line['userID'];
    						$db['tranz_id'] = 'mt'.$xml_line['recordID'];
    						$db['rdate'] = $xml_line['date'];
    						// $chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag,trader_id,merchant_id FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and  trader_id='".$db['trader_id']."' LIMIT 1"));
    						$chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,merchant_id,ctag,trader_id,merchant_id FROM data_reg WHERE  1=1  and  trader_id='".$db['trader_id']."' AND merchant_id = ".$ww['id'] ." LIMIT 1"));
    						//var_dump($chkTrader);
    						if (!$chkTrader['id']) {
    							echo 'Error:: Trader not found for this trader: ' . $db ['trader_id'] . '<br>';
    							continue;
    						}
    						$merchant_id = $chkTrader['merchant_id'];
    						if (!allowedMerchants($merchant_id)) {
    							echo 'Error:: Record Not Allowed<Br>';
    							continue;
    						}
    						$db['type'] = $xml_line['type'];//'deposit';
    						if ($db['type'] != "deposit" AND $db['type'] != "revenue" AND $db['type'] != "volume" AND $db['type'] != "bonus" AND $db['type'] != "withdrawal") {
    								echo 'Error:: Problematic Type: ' . $db['type'] . '<br>';
    								continue;
    						}
    						
    						if(!empty($xml_line['convert_to_usd'])){
    							$db['amount'] = abs($xml_line['convert_to_usd']);
    						}else{
    							$db['amount'] = $xml_line['amount'];
        						$coin = strtoupper($xml_line['currency']);
							$db['amount'] = abs(getUSD($db['amount'],$coin));
    						}
    						
    						
							$traderInfo = mysql_fetch_assoc(mysql_query("SELECT freeParam,freeParam2, ctag,ftdamount,initialftdtranzid FROM data_reg WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."'"));
							if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
								else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
							if (!ctagValid($db['ctag'])) {
								$db['ctag'] = $defaultBtag;
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
    						
    						
    						
    						$db_table_name = "data_sales";
    						if (
                                $db['type']=='deposit' && $set->hidePendingProcessHighAmountDeposit == 0 &&
                                (
                                    (
                                        $db['amount'] >= $set->pendingDepositsAmountLimit &&
                                        ($pendingDespositArrayLoaded && empty($pendingDepositExcludeAffiliatesArray[$db['affiliate_id']]))
                                    ) ||
                                    ($pendingDespositArrayLoaded && !empty($pendingDepositIncludeAffiliatesArray[$db['affiliate_id']]))
                                )
    						){
                                $db_table_name = "data_sales_pending";
                            }
    						
    						// check if record exists on data_sales or data_sales_pending
    						if ($db['type']=='deposit' && $set->hidePendingProcessHighAmountDeposit==0) {
    							$qrTranz = "SELECT id,type,tranz_id FROM data_sales_pending WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' 	AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'
    											union
    											SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' 	AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'
    								";
    						}else {
    								$qrTranz = "SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$merchant_id ." and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."' LIMIT 1";
    						}
    						
    						
    							if ($db['type']=='deposit' && $debug==1){
    								echo $qrTranz.'<Br>';
    							}
    							
    							$chkExistTranz=mysql_fetch_assoc(mysql_query($qrTranz));
    							if ($chkExistTranz['id']) {
    								$tranzExist = 1;
    							}
    							if ($debug==1)
    								echo 'Trader id: ' .$db['trader_id']  .'   |      Type: ' . $db['type'] . '<br>';
    							

    							$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
    							if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
    								$db['affiliate_id'] = $defaultAffiliateID;
    								$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
    								}
    							$db['group_id'] = $getAffiliate['group_id'];
    							
    							if (count($db) > 1 AND !$tranzExist) {
    										$q = "INSERT INTO ".$db_table_name." (dummySource,currentDate,merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,
    										trader_id,trader_alias,type,amount,freeParam,freeParam2, campaign_id) 
    										VALUES
    										(33, '".date('Y-m-d H:i:s')."',".$merchant_id.", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
    										'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
    										'".$db['amount']."','".$traderInfo['freeParam']."','".$traderInfo['freeParam2']."', '" . $current_Campaign . "')";
    										
    										echo $q .'<br>';
    										
    										mysql_query($q	) or die(mysql_error());
     										if ($traderInfo['ftdamount']==0 && $db['type']=='deposit' && $traderInfo['initialftdtranzid']=='' && $set->hidePendingProcessHighAmountDeposit==0 && $db['amount']<=$set->pendingDepositsAmountLimit) {
    											$GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from ".$db_table_name." where 
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
    							//	$subid =  $DynamicTrackertActive ? GetOverrideDynamicTracker ($db['affiliate_id'], $db['uid']) : "";
    								$pixelurl = $siteURL. 'pixel.php?act=deposit&ctag='.$db['ctag'].'&trader_id='.$db['trader_id'].'&merchant_id='.$merchant_id.'&tranz='.$db['tranz_id'].'&type='.$db['type'].'&currency='.$db['coin'].'&amount='.$db['amount'].'&subid='.$subid;
    								$pixelContent  = firePixel($pixelurl);
    								if (strlen($pixelContent)>0) {
    									echo 'Firing Sale Pixel, Affiliate_ID = ' . $db['affiliate_id'].' -- '.$pixelurl.'<br>';
    									echo $pixelContent;
    								}
    								flush();
    							} else {
    								if ($debug >0) 
    									echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
    							}
    							$intProcessed++;
    							if ($intProcessed % 100 == 0) {
    								echo '... , ';
    							}
    							$totalRowsFoundThisPage++;
    			}
    		if ($shouldRunOnNextPageAsWell == false)
    				break;
    	
            if ($intProcessed>0)
    	        echo 'Total processed so far: ' . $intProcessed . '<br>';
    	}
    ######################### [ MT5 Deposits + Withdrawals ] #########################		
	
}



if ($shouldRunPos==true){
######################### [ Positions Volume ] #########################
for ($page=1; $page<=$totalPage; $page++) {
		
		
        $shouldRunOnNextPageAsWell = false;
        $totalRowsFoundSoFar=0;
        //break; //////////////////////////////////////////////////////////////

        $positioncount++;
         if($positioncount % 4 ==0) {
                 usleep(1000000);
         }
			 
	echo '<hr /><b>Connecting to Revenue <span style="color:blue">Positions</span> Database Page: <u>'.$page.'</u>...</b><br />';
	// $scanDateFrom = "2015-08-01";
	$url = $api_url.'trades';
	echo 'url : ' . $url . '<br>';
		
        $intProcessed=0;
        // $xml_report=doPost($url);
	$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) ;
	
	
        $res = json_decode($res,TRUE);
        $xml_report = ($res['data']);
        $xml = $xml_report;

        //preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
        $arrXmlTest = array();

        $totalRowsFoundThisPage = 0;
        if ($debug >0) 		{ 
                echo '<br>debug: ' ; var_dump($xml_report); 
        }
		
	
	
        if (isset($xml_report['result']) && $xml_report['result']==null) {
                echo 'No Result..<br>';
        }
	else
		if (!empty($xml)){
                    foreach($xml AS $xml_line) {	
			
                        $shouldRunOnNextPageAsWell=true;


                        if ($debug>1) {
                                var_dump($xml_line);
                                echo '<br>';
                        }




	
                        $existVolume = 0;
                        $db['rdate'] = $xml_line['date'];
                        $db['trader_id'] = $xml_line['userID'];
                        $db['tranz_id'] = $xml_line['tradeID'];

                        $qry = "SELECT id,ctag,merchant_id FROM data_reg WHERE 1=1 and trader_id='".$db['trader_id']."' AND merchant_id = ".$ww['id'] ." LIMIT 1";

                        // var_dump($xml_line);
                        // var_dump($qry);
                        // die();
                        $chkTrader=mysql_fetch_assoc(mysql_query(	$qry));
                        if (!$chkTrader['id']) { 
                            echo 'Trader not found ' . $db['trader_id'].'<br>';
                            continue;
                        }
		
                        $merchant_id = $chkTrader['merchant_id'];
                        if (!allowedMerchants($merchant_id)) {
                                echo 'Record Not Allowed<Br>';
                                continue;
                        }
	
	
		
		
                        $db['amount'] = $xml_line['volume'];
                        $coin = strtoupper($xml_line['currency']);
                        $db['amount'] = abs(getUSD($db['amount'],$coin));

                        $db['type'] = 'volume';
	
	
                        $status = $xml_line['status'] ;//'closed';//getTag('<status>','<\/status>',$xml_line);
				
	
                        //if ($status == "open") continue;
                        if ($status != "processed") {
                                echo 'Status not found for trader: '.$db['trader_id'].':  ' . $status . '<br>';
                                continue;
                        }
		
		
		$Posqry = "SELECT id,type,tranz_id FROM data_sales WHERE merchant_id =" .$merchant_id. " and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."' LIMIT 1";
		// die ($qry);
		$chkExist=mysql_fetch_assoc(mysql_query($Posqry));
		if ($chkExist['id']) $existVolume=1;
		
		// Check cTag From Trader
		$traderInfo = mysql_fetch_assoc(mysql_query("SELECT freeParam2,ctag FROM data_reg WHERE merchant_id =" .$merchant_id. " and  trader_id='".$db['trader_id']."'"));
		if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
			else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
		// Check cTag From Trader
			
		if (!ctagValid($db['ctag'])) continue;
		
		$db['ctag'] = str_replace("--","-",$db['ctag']);
		$exp=explode("-",$db['ctag']);
		
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
		/*
		$db['affiliate_id']=substr($exp[0],1); // a
		$db['banner_id']=substr($exp[1],1); // b
		$db['profile_id']=substr($exp[2],1); // p
		$db['freeParam']=substr($exp[3],1); // f
		$db['country']=substr($exp[count($exp)-1],1); // c
		$db['freeParam']= overRideDynamicParameter($db['freeParam']);
		*/
		
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
			$db['affiliate_id'] = $defaultAffiliateID;
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			}
		$db['group_id'] = $getAffiliate['group_id'];
		
		if (count($db) > 1 AND !$existVolume) {
			// dbAdd($db,"data_sales_".strtolower($ww['name']));
			$PosInsQry = "INSERT INTO data_sales  (merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,trader_id,
						trader_alias,type,amount,freeParam,freeParam2) 
						VALUES
						(".$merchant_id.", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
						'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
						'".$db['amount']."','".$db['freeParam']."','".$traderInfo['freeParam2']."')";
			// die ($qry);
			mysql_query($PosInsQry) ;
			
			echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
			$sales_total++;
			flush();
			} else {
			if ($isTest>0) 
			echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br />';
			}
			 $intProcessed++;
                    }
                }
		if ($intProcessed>0){
                    echo 'Total processed so far: ' . $intProcessed . '<br>';
                }
	
	
	
	
	if ($shouldRunOnNextPageAsWell==false)
		break;
	}		
			
	echo 'Total Processed: ' . $intProcessed . '<br>';
	echo '<hr /><b>Done!</b><br />';
######################### [ Positions Volume ] #########################
        
######################### [ Positions Volume CFD ] #########################
for ($page=1; $page<=$totalPage; $page++) {
		
		
        $shouldRunOnNextPageAsWell = false;
        $totalRowsFoundSoFar=0;
        //break; //////////////////////////////////////////////////////////////

        $positioncount++;
         if($positioncount % 4 ==0) {
                 usleep(1000000);
         }
			 
	echo '<hr /><b>Connecting to Revenue <span style="color:blue">Positions CFD</span> Database Page: <u>'.$page.'</u>...</b><br />';
	// $scanDateFrom = "2015-08-01";
	$url = $api_url.'trades-fx';
	echo 'url : ' . $url . '<br>';
		
        $intProcessed=0;
        // $xml_report=doPost($url);
	$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) ;
	
	
        $res = json_decode($res,TRUE);
        $xml_report = ($res['data']);
        $xml = $xml_report;

        //preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
        $arrXmlTest = array();

        $totalRowsFoundThisPage = 0;
        if ($debug >0) 		{ 
                echo '<br>debug: ' ; var_dump($xml_report); 
        }
		
	
	
        if (isset($xml_report['result']) && $xml_report['result']==null) {
                echo 'No Result..<br>';
        }
	else
		if (!empty($xml)){
                    foreach($xml AS $xml_line) {	
			
                        $shouldRunOnNextPageAsWell=true;


                        if ($debug>1) {
                                var_dump($xml_line);
                                echo '<br>';
                        }




	
                        $existVolume = 0;
                        $db['rdate'] = $xml_line['tradeTime'];
                        $db['trader_id'] = $xml_line['userID'];
                        $db['tranz_id'] = date("Ymd",strtotime($xml_line['closeTime'])).$xml_line['tradeID'];

                        $qry = "SELECT id,ctag,merchant_id FROM data_reg WHERE 1=1 and trader_id='".$db['trader_id']."' AND merchant_id = ".$ww['id'] ." LIMIT 1";

                        // var_dump($xml_line);
                        // var_dump($qry);
                        // die();
                        $chkTrader=mysql_fetch_assoc(mysql_query(	$qry));
                        if (!$chkTrader['id']) { 
                            echo 'Trader not found ' . $db['trader_id'].'<br>';
                            continue;
                        }
		
                        $merchant_id = $chkTrader['merchant_id'];
                        if (!allowedMerchants($merchant_id)) {
                                echo 'Record Not Allowed<Br>';
                                continue;
                        }
	
	
		
		
                        $db['amount'] = $xml_line['Trade_volume'];
                        $coin = strtoupper(getCurrencyByCurrencyIdMap($xml_line['currency']));
                        $db['amount'] = abs(getUSD($db['amount'],$coin));

                        $db['type'] = 'volume';
	
	
                        $status = $xml_line['status'] ;//'closed';//getTag('<status>','<\/status>',$xml_line);
				
	
                        //if ($status == "open") continue;
                        if ($status != "3") {
                                echo 'Status not found for trader: '.$db['trader_id'].':  ' . $status . '<br>';
                                continue;
                        }
		
		
		$Posqry = "SELECT id,type,tranz_id FROM data_sales WHERE merchant_id =" .$merchant_id. " and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."' LIMIT 1";
		// die ($qry);
		$chkExist=mysql_fetch_assoc(mysql_query($Posqry));
		if ($chkExist['id']) $existVolume=1;
		
		// Check cTag From Trader
		$traderInfo = mysql_fetch_assoc(mysql_query("SELECT freeParam2,ctag FROM data_reg WHERE merchant_id =" .$merchant_id. " and  trader_id='".$db['trader_id']."'"));
		if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
			else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
		// Check cTag From Trader
			
		if (!ctagValid($db['ctag'])) continue;
		
		$db['ctag'] = str_replace("--","-",$db['ctag']);
		$exp=explode("-",$db['ctag']);
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
		/*
		$db['affiliate_id']=substr($exp[0],1); // a
		$db['banner_id']=substr($exp[1],1); // b
		$db['profile_id']=substr($exp[2],1); // p
		$db['freeParam']=substr($exp[3],1); // f
		$db['country']=substr($exp[count($exp)-1],1); // c
		$db['freeParam']= overRideDynamicParameter($db['freeParam']);
		*/
		
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
			$db['affiliate_id'] = $defaultAffiliateID;
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			}
		$db['group_id'] = $getAffiliate['group_id'];
		
		if (count($db) > 1 AND !$existVolume) {
			// dbAdd($db,"data_sales_".strtolower($ww['name']));
			$PosInsQry = "INSERT INTO data_sales  (merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,trader_id,
						trader_alias,type,amount,freeParam,freeParam2) 
						VALUES
						(".$merchant_id.", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
						'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
						'".$db['amount']."','".$db['freeParam']."','".$traderInfo['freeParam2']."')";
			// die ($qry);
			mysql_query($PosInsQry) ;
			
			echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
			$sales_total++;
			flush();
			} else {
			if ($isTest>0) 
			echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br />';
			}
			 $intProcessed++;
                    }
                }
		if ($intProcessed>0){
                    echo 'Total processed so far: ' . $intProcessed . '<br>';
                }
	
	
	
	
	if ($shouldRunOnNextPageAsWell==false)
		break;
	}		
			
	echo 'Total Processed CFD: ' . $intProcessed . '<br>';
	echo '<hr /><b>Done!</b><br />';
######################### [ Positions Volume CFD END ] #########################
        
}
	
	
######################### [ PNL ] #########################
$runPNL =  isset($_GET['runPNL']) ? $_GET['runPNL'] : false;
  if ($set->cronPnlRunAday==0 || empty($set->cronPnlRunAday)){
	  $runPNL = true;
  }else{
  
		$hoursToRun =   (24/ abs($set->cronPnlRunAday));
		$LastRunTime = mysql_fetch_assoc(mysql_query("select cronPnlRunLastTime from settings"));
		if ($LastRunTime['cronPnlRunLastTime']<>'0000-00-00 00:00:00'){
				$q = "select TIMESTAMPDIFF(hour,'".$LastRunTime['cronPnlRunLastTime']."',CURRENT_TIMESTAMP)  as hoursint ;";
				 // echo ($q.'<Br>');
				 $timeRow = mysql_fetch_assoc(mysql_query($q));
				 $hoursDif  =  $timeRow['hoursint'];
				 echo 'hoursToRun: ' . $hoursToRun. '<Br>';
				  echo 'hoursDif: ' . $hoursDif. '<Br>';
				  // die();
		}
		if ($hoursDif>$hoursToRun || $LastRunTime['cronPnlRunLastTime']=='0000-00-00 00:00:00'){
			mysql_query("update settings set cronPnlRunLastTime = '" . date('Y-m-d H:i:s'). "' ");
			$runPNL = true;
		}
	}


	if ($runPNL)
				
	for ($page=1; $page<=$totalPage; $page++) {
		
		
		$shouldRunOnNextPageAsWell = false;
$totalRowsFoundSoFar=0;
//		break; //////////////////////////////////////////////////////////////
		
		$positioncount++;
		 if($positioncount % 4 ==0) {
			 usleep(1000000);
		 }
			 
	echo '<hr /><b>Connecting to <span style="color:blue">PNL</span> Database Page: <u>'.$page.'</u>...</b><br />';
	// $scanDateFrom = "2015-08-01";
	$url = $api_url.'trades';
	echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		// $xml_report=doPost($url);
	$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) ;
	
	
	if(!empty($_GET['debug_trades'])){
	    echo $res;
	    die();
	}
	
		$res = json_decode($res,TRUE);
		$xml_report = ($res['data']);
		$xml = $xml_report;
		
		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug >0) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
		
	
	
if (isset($xml_report['result']) && $xml_report['result']==null) {
	echo 'No Result..<br>';
}
	else
		foreach($xml AS $xml_line) {	
			
			$shouldRunOnNextPageAsWell=true;

		
			if ($debug>1) {
				var_dump($xml_line);
				echo '<br>';
			}


	
		$existVolume = 0;
		$db['rdate'] = $xml_line['date'];
		$db['trader_id'] = $xml_line['userID'];
		$db['tranz_id'] = $xml_line['tradeID'];
	
		$qry = "SELECT id,ctag,merchant_id FROM data_reg WHERE 1=1 and trader_id='".$db['trader_id']."' AND merchant_id = ".$ww['id'] ." LIMIT 1";
		
		// var_dump($xml_line);
		// var_dump($qry);
		// die();
		$chkTrader=mysql_fetch_assoc(mysql_query(	$qry));
		if (!$chkTrader['id']) { 
		echo 'Trader not found ' . $db['trader_id'].'<br>';
		continue;
		}
		
		$merchant_id = $chkTrader['merchant_id'];
	if (!allowedMerchants($merchant_id)) {
		echo 'Record Not Allowed<Br>';
		continue;
	}
	
	$db['amount'] = $xml_line['pnl'] - $xml_line['bonusPnl'];
	$coin = strtoupper($xml_line['currency']);
	$db['amount'] = (getUSD($db['amount'],$coin));
	$db['type'] = 'PNL';
	$status = $xml_line['status'] ;//'closed';//getTag('<status>','<\/status>',$xml_line);

	
		//if ($status == "open") continue;
		if ($status != "processed") {
			echo 'Status not found: ' . $status . '<br>';
			continue;
		}
		
		
		$qry = "SELECT id,type,tranz_id FROM data_sales WHERE merchant_id =" .$merchant_id. " and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'";
		
		
		// die ($qry);
		$chkExist=mysql_fetch_assoc(mysql_query($qry));
		if ($chkExist['id']) $existVolume=1;
		
		// Check cTag From Trader
		$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id =" .$merchant_id. " and  trader_id='".$db['trader_id']."'"));
		if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
			else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
		// Check cTag From Trader
			
		if (!ctagValid($db['ctag'])) continue;
		
		$db['ctag'] = str_replace("--","-",$db['ctag']);
		$exp=explode("-",$db['ctag']);
		
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
		
		/*
		$db['affiliate_id']=substr($exp[0],1); // a
		$db['banner_id']=substr($exp[1],1); // b
		$db['profile_id']=substr($exp[2],1); // p
		$db['freeParam']=substr($exp[3],1); // f
		$db['country']=substr($exp[count($exp)-1],1); // c
		$db['freeParam']= overRideDynamicParameter($db['freeParam']);
		*/
		
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
			$db['affiliate_id'] = $defaultAffiliateID;
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			}
		$db['group_id'] = $getAffiliate['group_id'];
		
		if (count($db) > 1 AND !$existVolume) {

			// Check PNL Limitation
			if(IsPnlExceeded($db,$db['amount'],$ww,$db['rdate'])){
				echo 'PNL Limitation Exceeded: ' . $db['trader_id'].' - PNL $'.$db['amount'].'<br>';
				continue;
			}

			$AffiliatePnlReduceValue = getTraderDealsValue($db['affiliate_id'],'pnl_lower');
			if($AffiliatePnlReduceValue > 0 && $AffiliatePnlReduceValue < 100){
				$db['amount'] =  $db['amount'] * (1 - $AffiliatePnlReduceValue / 100);
			}


			// dbAdd($db,"data_sales_".strtolower($ww['name']));
			$qryPNL = "INSERT INTO data_sales  (merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,trader_id,currency,
						trader_alias,type,amount,freeParam,freeParam2) 
						VALUES
						(".$merchant_id.", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
						'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$coin."','".$db['trader_alias']."','".$db['type']."',
						'".$db['amount']."','".$db['freeParam']."','".$db['freeParam2']."')";
			// die ($qry);
			
			
			mysql_query($qryPNL) OR die(mysql_error());
			
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
	
	
	
	
	if ($shouldRunOnNextPageAsWell==false)
		break;
	}		
			
	echo 'Total Processed: ' . $intProcessed . '<br>';
	echo '<hr /><b>Done!</b><br />';
######################### [ PNL END ] #########################
	
	
	
######################### [ PNL CFD ] #########################
$runPNL =  isset($_GET['runPNL']) ? $_GET['runPNL'] : false;
  if ($set->cronPnlRunAday==0 || empty($set->cronPnlRunAday)){
	  $runPNL = true;
  }else{
  
		$hoursToRun =   (24/ abs($set->cronPnlRunAday));
		$LastRunTime = mysql_fetch_assoc(mysql_query("select cronPnlRunLastTime from settings"));
		if ($LastRunTime['cronPnlRunLastTime']<>'0000-00-00 00:00:00'){
				$q = "select TIMESTAMPDIFF(hour,'".$LastRunTime['cronPnlRunLastTime']."',CURRENT_TIMESTAMP)  as hoursint ;";
				 // echo ($q.'<Br>');
				 $timeRow = mysql_fetch_assoc(mysql_query($q));
				 $hoursDif  =  $timeRow['hoursint'];
				 echo 'hoursToRun: ' . $hoursToRun. '<Br>';
				  echo 'hoursDif: ' . $hoursDif. '<Br>';
				  // die();
		}
		if ($hoursDif>$hoursToRun || $LastRunTime['cronPnlRunLastTime']=='0000-00-00 00:00:00'){
			mysql_query("update settings set cronPnlRunLastTime = '" . date('Y-m-d H:i:s'). "' ");
			$runPNL = true;
		}
	}


	if ($runPNL)
				
	for ($page=1; $page<=$totalPage; $page++) {
		
		
		$shouldRunOnNextPageAsWell = false;
$totalRowsFoundSoFar=0;
//		break; //////////////////////////////////////////////////////////////
		
		$positioncount++;
		 if($positioncount % 4 ==0) {
			 usleep(1000000);
		 }
			 
	echo '<hr /><b>Connecting to <span style="color:blue">PNL</span> Database Page: <u>'.$page.'</u>...</b><br />';
	// $scanDateFrom = "2015-08-01";
	$url = $api_url.'trades-fx';
	echo 'url : ' . $url . '<br>';
		
		$intProcessed=0;
		// $xml_report=doPost($url);
	$res=tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page) ;
	
	
		$res = json_decode($res,TRUE);
		$xml_report = ($res['data']);
		$xml = $xml_report;
		
		//preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
		$totalRowsFoundThisPage = 0;
		if ($debug >0) 		{ 
			echo '<br>debug: ' ; var_dump($xml_report); 
		}
		
	
	
if (isset($xml_report['result']) && $xml_report['result']==null) {
	echo 'No Result..<br>';
}
	else
		foreach($xml AS $xml_line) {	
			
			$shouldRunOnNextPageAsWell=true;

		
if ($debug>1) {
	var_dump($xml_line);
	echo '<br>';
}


	
		$existVolume = 0;
		$db['rdate'] = $xml_line['tradeTime'];
		$db['trader_id'] = $xml_line['userID'];
		$db['tranz_id'] = date("Ymd",strtotime($db['rdate'])).$xml_line['tradeID'];
	
		$qry = "SELECT id,ctag,merchant_id FROM data_reg WHERE 1=1 and trader_id='".$db['trader_id']."' AND merchant_id = ".$ww['id'] ." LIMIT 1";
		
		// var_dump($xml_line);
		// var_dump($qry);
		// die();
		$chkTrader=mysql_fetch_assoc(mysql_query(	$qry));
		if (!$chkTrader['id']) { 
		echo 'Trader not found ' . $db['trader_id'].'<br>';
		continue;
		}
		
		$merchant_id = $chkTrader['merchant_id'];
	if (!allowedMerchants($merchant_id)) {
		echo 'Record Not Allowed<Br>';
		continue;
	}
	
	
	$db['amount'] = $xml_line['realPnl'];
	$coin = strtoupper(getCurrencyByCurrencyIdMap($xml_line['currency']));
	$db['amount'] = (getUSD($db['amount'],$coin));
	$db['type'] = 'PNL';
	$status = $xml_line['status'] ;//'closed';//getTag('<status>','<\/status>',$xml_line);
				
	
		//if ($status == "open") continue;
		if ($status != "3") {
			echo 'Status not found: ' . $status . '<br>';
			continue;
		}

		
		$qry = "SELECT id,type,tranz_id FROM data_sales WHERE merchant_id =" .$merchant_id. " and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'";
		
		if ( $db['trader_id'] =="3600519" )
		echo $qry .'<br>';
		
		// die ($qry);
		$chkExist=mysql_fetch_assoc(mysql_query($qry));
		if ($chkExist['id']) $existVolume=1;
		
		// Check cTag From Trader
		$traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id =" .$merchant_id. " and  trader_id='".$db['trader_id']."'"));
		if ($traderInfo['ctag']) $db['ctag'] = $traderInfo['ctag'];
			else $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
		// Check cTag From Trader
			
		if (!ctagValid($db['ctag'])) continue;
		
		$db['ctag'] = str_replace("--","-",$db['ctag']);
		$exp=explode("-",$db['ctag']);
		
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
		
		/*
		$db['affiliate_id']=substr($exp[0],1); // a
		$db['banner_id']=substr($exp[1],1); // b
		$db['profile_id']=substr($exp[2],1); // p
		$db['freeParam']=substr($exp[3],1); // f
		$db['country']=substr($exp[count($exp)-1],1); // c
		$db['freeParam']= overRideDynamicParameter($db['freeParam']);
		*/
		
		$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
		if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
			$db['affiliate_id'] = $defaultAffiliateID;
			$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."'"));
			}
		$db['group_id'] = $getAffiliate['group_id'];
		
		if (count($db) > 1 AND !$existVolume) {

			// Check PNL Limitation
			if(IsPnlExceeded($db,$db['amount'],$ww,$db['rdate'])){
				echo 'PNL Limitation Exceeded: ' . $db['trader_id'].' - PNL $'.$db['amount'].'<br>';
				continue;
			}

			$AffiliatePnlReduceValue = getTraderDealsValue($db['affiliate_id'],'pnl_lower');
			if($AffiliatePnlReduceValue > 0 && $AffiliatePnlReduceValue < 100){
				$db['amount'] =  $db['amount'] * (1 - $AffiliatePnlReduceValue / 100);
			}

			// dbAdd($db,"data_sales_".strtolower($ww['name']));
			$qryPNL = "INSERT INTO data_sales  (merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,trader_id,currency,
						trader_alias,type,amount,freeParam,freeParam2) 
						VALUES
						(".$merchant_id.", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
						'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$coin."','".$db['trader_alias']."','".$db['type']."',
						'".$db['amount']."','".$db['freeParam']."','".$db['freeParam2']."')";
			// die ($qry);

	
			mysql_query($qryPNL) OR die(mysql_error());
			
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
	
	
	
	
	if ($shouldRunOnNextPageAsWell==false)
		break;
	}		
			
	echo 'Total Processed: ' . $intProcessed . '<br>';
	echo '<hr /><b>Done!</b><br />';
######################### [ PNL CFD END ] #########################
	
	
	

//getNonQualifiedFTD($fromThisMonth);
//getNonQualifiedFTD($fromThisMonth);
updateInitialFTD(1);
updateTraderValue(0);
getNonQualifiedFTD(0,'2016-12-01 00:00:00');
normalizeAffiliatesGroupId();

// updateTraderValue(1); // just for records came today

/*
$takeMonth = @mysql_fetch_assoc(mysql_query("SELECT id FROM cron_logs WHERE month='".date("n")."' AND year='".date("Y")."' AND merchant_id='".$ww['id']."'"));
if ($takeMonth['id']) @mysql_query("UPDATE cron_logs SET lastscan='".dbDate()."',reg_total=reg_total+".$reg_total.",sales_total=sales_total+".$sales_total." WHERE id='".$takeMonth['id']."'");
	else @mysql_query("INSERT INTO cron_logs (lastscan,month,year,merchant_id,merchant_name,success,reg_total,sales_total) VALUES ('".dbDate()."','".date("n")."','".date("Y")."','".$ww['id']."','".strtolower($ww['name'])."','1','".$reg_total."','".$sales_total."')");
*/

@mysql_query("INSERT INTO cron_logs (startscan,lastscan,month,year,merchant_id,merchant_name,success,reg_total,sales_total) VALUES ('".$cronStartTime."','".dbDate()."','".date("n")."','".date("Y")."','".$ww['id']."','".strtolower($ww['name'])."','1','".$reg_total."','".$sales_total."')");


	echo '<br>Ending time' . date('H:i:s') . '<br>';
	echo 'Cron is done!.';
exit;

?>