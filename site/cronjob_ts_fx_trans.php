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


/*********************/

function getTraderDealsValue($id,$type){
    $amount = 0;
    $result = function_mysql_query("SELECT * FROM `traders_deals` WHERE rdate <= '".date('Y-m-d H:i:s')."' AND affiliate_id = ".(int)$id." AND trader_id = 0 AND dealType = '".$type."' AND valid = 1 ORDER BY rdate DESC LIMIT 1",__FILE__);
    $data = mysql_fetch_assoc($result);
    if(!empty($data['amount'])){
        $amount = $data['amount'];
    }
    
    return $amount;
}

function tools4brokersRequest($request){
    
    $server = "78.46.33.214";
    $port = 80;
    
    $socketPtr = fsockopen($server, $port, $errno, $errstr, 0.4);
    
    fputs($socketPtr, $request);
    $size = fgets($socketPtr, 64);
    $answer = "";
    $readed = 0;
    while ($readed < $size) {
        $part = fread($socketPtr, $size - $readed);
        $readed += strlen($part);
        $answer .= $part;
    }
    
    //echo $request."<br>--------------------------<br>".$answer."<br>--------------------------<br>";
    return $answer;
}

function getDealsHistoryToolsForBrokers($date_from, $date_to, $login) {

    $all_trades = [];

    $server = "78.46.33.214";
    $port = 80;

    $socketPtr = fsockopen($server, $port, $errno, $errstr, 0.4);
//$request = "action=getaccounts";
//$request = "action=getgroups";
//$request = "action=getaccounts&group=real\real";
//$request = "action=getopentrades";

    $request = "action=getdealshistory&from=" . $date_from . "&to=" . $date_to . "&logins=".$login;
    
    $answer = tools4brokersRequest($request);
    
    if (!empty($answer)) {

        parse_str($answer, $params_array);

        if (!empty($params_array['answer'])) {
            $trades = explode("\n", $params_array['answer']);

            foreach ($trades as $value) {
                $item = [];
                
                list($item['login'], $item['deal'], $item['order'], $item['symbol'], $item['price'], $item['profit'], $item['commission'], $item['dealer'], $item['entry'], $item['volume'], $item['create_time'], $item['setup_time'], $item['done_time'], $item['cmd'], $item['contract_size'], $item['digits'], $item['external_ID'], $item['position_ID'], $item['gateway'], $item['price_gateway'], $item['rate_margin'], $item['profit_raw'], $item['rate_profit'], $item['reason'], $item['swaps'], $item['comment'], $item['tick_size'], $item['tick_vlue']) = explode(';', $value);
                
                if(empty($item['login'])){
                    continue;
                }
                
                if(!empty($item['comment']) && (trim(strtolower($item['comment'])) == 'deposit' || trim(strtolower($item['comment'])) == 'withdraw')){
                    
                    $trader_group_result = tools4brokersRequest("action=getaccountinfo&login=".$login);
                    parse_str($trader_group_result, $trader_group);
                    if(!empty($trader_group['group'])){
                        $trader_currency_result = tools4brokersRequest("action=getgroup&name=".$trader_group['group']);
                        parse_str($trader_currency_result, $trader_currency);
                        if($trader_currency['currency']){
                            $item['currency'] = $trader_currency['currency'];
                        }   
                    }
                    unset($trader_group);
                    unset($trader_currency);
                }

                $all_trades[] = $item;

                unset($item);
            }
        }
    }
    
    return $all_trades;
    
}


function getCurrencyByCurrencyIdMap($id){
    $arrayCurrencyMap = [
        1 => 'usd',
        2 => 'eur',
        3 => 'aud',
        4 => 'cny',
        5 => 'gbp',
        6 => 'jpy',
        7 => 'rub',
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
	"0" => $_GET['isTest']
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
	

function tradeSmarterCurl($url,$api_user,$api_pass,$scanDateFrom,$scanDateTo,$page,$long = true) {

    //  /filterBy/registration/from/2016-03-06/to/2016-03-07

    if($long){
        $url_full = $url.'/from/'.$scanDateFrom.'/to/'.$scanDateTo.'/page/'.$page;
    }else{
        $url_full = $url;
    }
    
    if(strpos($url_full, 'registrations') || strpos($url_full, 'users') || strpos($url_full, 'leads')){
        $url_full = $url_full.'/filterBy/registration';
    }
    
    if(!empty($_GET['debug'])){
        echo $url_full;
    }
    
    


    // Init cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    // Set HTTP Auth credentials
    curl_setopt($ch, CURLOPT_USERPWD, $api_user.':'.$api_pass);

    curl_setopt($ch, CURLOPT_URL, $url_full);

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



//die ('tocheck: ' . $ww['lastSaleStatusUpdate']);
echo 'From: <u>'.$scanDateFrom.'</u> To: <u>'.$scanDateTo.'</u>';

$campscount = 0;

//var_dump($campaignIDs);
//die ('done');

//------------------------------------------------------
// MT5 tools4broker ------------------------------------
	
	flush();

    $result_traders_mt5 = function_mysql_query("select trader_id from data_reg where merchant_id = ".$ww['id']." and type = 'real'");
    while($traders_mt5 = mysql_fetch_assoc($result_traders_mt5)){
    	$trader_id_array[] = $traders_mt5['trader_id'];	
    }

    $trader_id_array_chunk = array_chunk($trader_id_array,1000);

	foreach($trader_id_array_chunk as $ids){

        $get_wallet_ids = tradeSmarterCurl($api_url.'get-wallets-info?userIDs='.implode(',',$ids),$api_user,$api_pass,false,false,false,false);
        $get_wallet_ids = json_decode($get_wallet_ids);
        
        if(!empty($get_wallet_ids->data)){
        	foreach($get_wallet_ids->data as $tr_id => $tr_wallet){


		        $traders_mt5_wallet_array = $tr_wallet->{5};
		        
		        if(!empty($traders_mt5_wallet_array)){
		            foreach($traders_mt5_wallet_array as $wallet){
		                $trades = getDealsHistoryToolsForBrokers(strtotime($scanDateFrom),strtotime($scanDateTo),$wallet->externalWalletID);
		                if(!empty($trades)){
		                    foreach ($trades AS $xml_line) {
		                        
		                        $xml_line['trader_id'] = $tr_id;

				                $shouldRunOnNextPageAsWell=true;
			
		                        $existVolume = 0;
		                        $db['rdate'] = date('Y-m-d H:i:s',$xml_line['done_time']);
		                        $db['trader_id'] = $xml_line['trader_id'];
		                        $db['tranz_id'] = 'mt'.$xml_line['deal'];

		                        $chkTrader=mysql_fetch_assoc(mysql_query("SELECT id,ctag,merchant_id FROM data_reg WHERE  merchant_id = ".$ww['id']." AND trader_id='".$db['trader_id']."' LIMIT 1"));
		                        if (!$chkTrader['id']) { 
		                            echo 'Trader not found ' . $db['trader_id'].'<br>';
		                            continue;
		                        }
				
		                        $merchant_id = $chkTrader['merchant_id'];
		                        if (!allowedMerchants($merchant_id)) {
		                                echo 'Record Not Allowed<Br>';
		                                continue;
		                        }
			
			
		                        $db['amount'] = $xml_line['profit'];
		                        /*
		                        if($wallet->currency == '1'){
		                            $coin = 'USD';
		                        }else{
		                            $coin = 'EUR';
		                        }
		                        */
                                
                                
                                
		                        if(!empty($xml_line['currency'])){
		                        	$coin = strtoupper($xml_line['currency']);
		                        }else{
		                        	$coin = strtoupper(getCurrencyByCurrencyIdMap($wallet->currency));
		                        }

		                        
		                        $db['amount'] = (getUSD($db['amount'],$coin));
		                        $db['type'] = 'PNL';
		                        $status = 'closed';
		                        
		                        if(empty($db['amount'])){
		                            echo 'Amount is empty: '.$db['amount'].'/'.$coin.'<br>';
		                            continue;
		                        }
		                        
		                        
								// Deposit
								$transaction_type_string = strtolower(trim($xml_line['comment']));
		                        if(empty($xml_line['symbol']) && ($transaction_type_string == 'deposit' || $transaction_type_string == 'withdraw')){
		                            
		                            // No process Deposits (it's on cronjob_ts_fx)
		                            continue;
									break;
		                            
										
		    						$tranzExist=0;
		                            
		                            $db['rdate'] = date('Y-m-d H:i:s',$xml_line['create_time']);
		                            
		    						switch($transaction_type_string){
										case 'deposit': 
											$db['type'] = 'deposit';
											break;
											
										case 'withdraw': 
											$db['type'] = 'withdrawal';
                                            $db['amount'] = abs($db['amount']);
											break;
										
										default:
											echo 'Type is not Deposit or Withdrawal<br>';
											continue;
											break;
									}
									
		    						
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
		    							$qrTranz = "SELECT id,type,tranz_id FROM data_sales_pending WHERE  merchant_id = ".$ww['id']." and trader_id='".$db['trader_id']."' 	AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'
		    											union
		    											SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."' 	AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."'
		    								";
		    						}else {
										$qrTranz = "SELECT id,type,tranz_id FROM data_sales WHERE  merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."' LIMIT 1";
		    						}
		    							
									$chkExistTranz = mysql_fetch_assoc(mysql_query($qrTranz));
									if ($chkExistTranz['id']) {
										$tranzExist = 1;
									}
									
		    							
									$traderInfo = mysql_fetch_assoc(mysql_query("SELECT freeParam,freeParam2, ctag,ftdamount,initialftdtranzid FROM data_reg WHERE  merchant_id = ".$ww['id'] ." and trader_id='".$db['trader_id']."' LIMIT 1"));
									if ($traderInfo['ctag']){
										$db['ctag'] = $traderInfo['ctag'];
									}else {
										$db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
									}
									
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
									$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."' LIMIT 1"));
									if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
										$db['affiliate_id'] = $defaultAffiliateID;
										$getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."' LIMIT 1"));
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
		    										
										mysql_query($q) or die(mysql_error());
										
										if ($traderInfo['ftdamount']==0 && $db['type']=='deposit' && $traderInfo['initialftdtranzid']=='' && $set->hidePendingProcessHighAmountDeposit==0 && $db['amount']<=$set->pendingDepositsAmountLimit) {
											$GetFTDforTraderQuery = "select id, rdate, tranz_id,amount from ".$db_table_name." where trader_id = " . $chkTrader['trader_id'] .  " and merchant_id = " . $chkTrader['merchant_id'] . " and type='deposit'  order by rdate limit 0,1";
											$GetFTDforTrader = mysql_fetch_assoc( mysql_query($GetFTDforTraderQuery));
											if (!empty($GetFTDforTrader)) {
												$UpdateFTDforTrader = "update data_reg set  ftdamount = " .$GetFTDforTrader['amount']." , initialftdtranzid = '".$GetFTDforTrader['tranz_id']."' , initialftddate = '" .$GetFTDforTrader['rdate'] ."' where trader_id= ".$chkTrader['trader_id']. "  and merchant_id = " . $chkTrader['merchant_id'];
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
									} else {
										echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Already Exist</b>!<br />';
									}
									$intProcessed++;
									$totalRowsFoundThisPage++;
										
									flush();
		                        	continue;	
		                        }
		                        
		                        if(empty($xml_line['symbol'])){
		                            echo 'SYMBOL is empty<br>';
		                            continue;
		                        }
		                        
                                        /**************************************/
                                        // PNL - NOT RELEVANT IN THIS CASE
                                        /**************************************/
                                        // IGNORE
                                        continue;
                                        /**************************************/
                                        
		                        // ADD PNL
		                        $qry = "SELECT id,type,tranz_id FROM data_sales WHERE merchant_id =" .$ww['id']. " and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."' LIMIT 1";

		                        $chkExist=mysql_fetch_assoc(mysql_query($qry));
		                        if ($chkExist['id']) $existVolume=1;

		                        // Check cTag From Trader
		                        $traderInfo = mysql_fetch_assoc(mysql_query("SELECT ctag FROM data_reg WHERE merchant_id =" .$ww['id']. " and  trader_id='".$db['trader_id']."' LIMIT 1"));
		                        if ($traderInfo['ctag']){
		                            $db['ctag'] = $traderInfo['ctag'];
		                        }else{
		                            $db['ctag'] = getTag('<ctag>','<\/ctag>',$xml_line);
		                        }
		                        // Check cTag From Trader
					
		                        if (!ctagValid($db['ctag'])) continue;
				
		                        $db['ctag'] = str_replace("--","-",$db['ctag']);
		                        $exp=explode("-",$db['ctag']);

                                        /*
		                        $db['affiliate_id']=substr($exp[0],1); // a
		                        $db['banner_id']=substr($exp[1],1); // b
		                        $db['profile_id']=substr($exp[2],1); // p
		                        $db['freeParam']=substr($exp[3],1); // f
		                        $db['country']=substr($exp[count($exp)-1],1); // c
		                        $db['freeParam']= overRideDynamicParameter($db['freeParam']);
                                        */
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

		                        $getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."' LIMIT 1"));
		                        if (!$getAffiliate['id'] OR !$getAffiliate['valid']) {
		                            $db['affiliate_id'] = $defaultAffiliateID;
		                            $getAffiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$db['affiliate_id']."' LIMIT 1"));
					}
		                        $db['group_id'] = $getAffiliate['group_id'];
				
		                        if (count($db) > 1 AND !$existVolume) {


		                        	if($db['type'] == 'PNL'){
		                        		// Check PNL Limitation
										if(IsPnlExceeded($db,$db['amount'],$ww,$db['rdate'])){
											echo 'PNL Limitation Exceeded: ' . $db['trader_id'].' - PNL $'.$db['amount'].'<br>';
											continue;
										}

                                        $AffiliatePnlReduceValue = getTraderDealsValue($db['affiliate_id'],'pnl_lower');
                                        if($AffiliatePnlReduceValue > 0 && $AffiliatePnlReduceValue < 100){
                                            $db['amount'] =  $db['amount'] * (1 - $AffiliatePnlReduceValue / 100);
                                        }

		                        	}


		                            $qryPNL = "INSERT INTO data_sales  (merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,trader_id,currency,
								trader_alias,type,amount,freeParam,freeParam2) 
								VALUES
								(".$ww['id'].", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
								'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$coin."','".$db['trader_alias']."','".$db['type']."',
								'".$db['amount']."','".$db['freeParam']."','".$db['freeParam2']."')";
					
			
		                            mysql_query($qryPNL) OR die(mysql_error());
					
		                            echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
		                            $sales_total++;
					} else {
		                            if ($isTest>0) 
		                            echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br />';
					}
		                        flush();
		                        
		                        // ADD VOLUME
		                        $db['type'] = 'volume';
		                        $db['amount'] = $xml_line['volume'];
		                        $db['amount'] = (getUSD($db['amount'],$coin));
		                        
		                        $existVolume=0;
		                        $chkExist=mysql_fetch_assoc(mysql_query("SELECT id,type,tranz_id FROM data_sales WHERE merchant_id =" .$ww['id']. " and trader_id='".$db['trader_id']."' AND type='".$db['type']."' AND tranz_id='".$db['tranz_id']."' LIMIT 1"));
		                        if ($chkExist['id']){
		                            $existVolume=1;
		                        }
		                        
		                        if (count($db) > 1 AND !$existVolume) {
		                            $PosInsQry = "INSERT INTO data_sales  (merchant_id,rdate,ctag,affiliate_id,group_id,banner_id,profile_id,country,tranz_id,trader_id,
								trader_alias,type,amount,freeParam,freeParam2) 
								VALUES
								(".$ww['id'].", '".$db['rdate']."','".$db['ctag']."','".$db['affiliate_id']."','".$db['group_id']."','".$db['banner_id']."',
								'".$db['profile_id']."','".$db['country']."','".$db['tranz_id']."','".$db['trader_id']."','".$db['trader_alias']."','".$db['type']."',
								'".$db['amount']."','".$db['freeParam']."','".$traderInfo['freeParam2']."')";
		                            // die ($qry);
		                            mysql_query($PosInsQry) ;
					
		                            echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Inserted</b>!<br />';
		                            $sales_total++;
					} else {
		                            if ($isTest>0) 
		                            echo '<li> ['.$db['rdate'].'] '.$db['trader_id'].' (ctag: '.$db['ctag'].') /'.$db['type'].' Amount: $ '.$db['amount'].'/ - <b>Exist</b>!<br />';
					}
		                        flush();
		                         
					 $intProcessed++;

		                    }
		                    unset($trades);
		                }
		            }
		        }
        	}
    	}
    }

    
// MT5 tools4broker END --------------------------------
//------------------------------------------------------



echo '<br>Ending time' . date('H:i:s') . '<br>';
echo 'Cron is done!.';
die();

?>
