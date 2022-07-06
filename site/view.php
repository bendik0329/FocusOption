<?php
require('common/global.php');


if ($set->activateLogs == 1) {
    $ip = getClientIp();

    if (checkUserFirewallIP($ip)) {


        $activityLogUrl = "http://" . $_SERVER['SERVER_NAME'] . "/ajax/saveLogActivity.php?user_id=" . $getAffiliate['id'] . "&ip=" . $ip . "&country=" . $country_id . "&location=traffic&userType=affiliate&theChange=Blocked-user_trying_to_enter_the_system&_function_=" . __FUNCTION__ . "&_file_=" . __FILE__ . "&queryString=" . urlencode($queryString);
        //die($activityLogUrl);
        doPost($activityLogUrl);


        $url = 'http://' . $_SERVER['SERVER_NAME'] . "/404.php";
        // die ($url);
        header("Location: " . $url);
        die('--');
    }
}

function doPost($url) {
    $parse_url = parse_url($url);
    $da = fsockopen($parse_url['host'], 80, $errno, $errstr);
    if (!$da) {

        echo "$errstr ($errno)<br/>\n";
        echo $da;
    } else {
        //die($url);
        $params = "POST " . $parse_url['path'] . " HTTP/1.1\r\n";
        $params .= "Host: " . $parse_url['host'] . "\r\n";
        $params .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $params .= "User-Agent: " . $set->webTitle . " Agent\r\n";
        $params .= "Content-Length: " . strlen($parse_url['query']) . "\r\n";
        $params .= "Connection: close\r\n\r\n";
        $params .= $parse_url['query'];
        fputs($da, $params);
        while (!feof($da))
            $response .= fgets($da);
        fclose($da);

        //var_dump($response);
        //die();
        // split the result header from the content
        $result = explode("\r\n\r\n", $response, 2);

        $content = isset($result[1]) ? $result[1] : '';
        //die ($content);
        return $content;
    }
}

$debug = isset($_GET['debug']) ? $_GET['debug'] : 0;
$appTable = "traffic";

//$ctag=strip_tags(str_replace("=","",$_GET['ctag']));
//$exp=explode("-",$ctag);

$ctagArray = array();
$ctagArray = getBtag($ctag);



$userCountry = $_SERVER['HTTP_CF_IPCOUNTRY'];
$country_id = $userCountry;
if (empty($userCountry)){
	$userCountry = getIPCountry();
	if ($userCountry['countrySHORT']) {
		$country_id = $userCountry['countrySHORT'];
	}
}
if (trim($country_id)=='-'){
	$country_id="";
}


$affiliate_id = (int) $ctagArray['affiliate_id']; //substr($exp[0],1);
$banner_id = (int) $ctagArray['banner_id'];
$profile_id = (int) $ctagArray['profile_id'];

$country = $ctagArray['country'];
$uid = $ctagArray['uid'];
$freeParam = $ctagArray['freeParam'];



$cookieName = 'affClickCtag_' . $affiliate_id . $banner_id . $profile_id;

$getAffiliate = dbGet($affiliate_id, "affiliates");
$getProfile = dbGet($profile_id, "affiliates_profiles");
$getBanner = dbGet($banner_id, "merchants_creative");


if ($getAffiliate['valid'] < 0) {
    _goto($set->webAddressHttps . 'images/1px.gif');
}


$isProductSession = false;
if ($getBanner['merchant_id'] == 0 && ($getBanner['product_id'] > 0)) {
    $q = "select *,param as params ,id as product_id from products_items where id = " . $getBanner['product_id'];
    // die ($q);
    $getMerchant = mysql_fetch_assoc(mysql_query($q));
    $isProductSession = true;
} else
    $getMerchant = dbGet($getBanner['merchant_id'], "merchants");


if (!$getMerchant['id'] OR ! $getMerchant['valid'])
    $miss = 1;


$querystringurl = $_SERVER['REQUEST_URI'];
$extraMemberParam = '';

// var_dump($getMerchant);
// die();
if ($getMerchant['campaignparamname'] != '') {


    if ($affiliate_id) {
        $qqcamp = 'SELECT campID AS id FROM affiliates_campaigns_relations WHERE affiliateID=' . $affiliate_id . ' and merchantid = ' . $getMerchant['id'];
        // die ($qqcamp);
        if ($debug == 1) {
            echo $qqcampa . '<br>';
        }
        $camp = mysql_fetch_assoc(function_mysql_query($qqcamp, __FILE__));
    }
    if ($affiliate_id AND $camp['id'] != '' AND $camp['id'] != NULL) {
        $extraMemberParam .= $camp['id'];
    } else {
        $extraMemberParam .= $affiliate_id;
    }

    if (!empty($extraMemberParam))
        $querystringurl .= '&' . $getMerchant['campaignparamname'] . '=' . $extraMemberParam;
}



$queryStringParamsArray = array();
$ex = explode('?', $querystringurl);
$params = "";
if (isset($ex[1]) && !empty($ex[1]))
    $params = $ex[1];
unset($ex);
$queryString = $params;
$exs = explode('&', $queryString);
foreach ($exs as $ex) {

    $pair = explode('=', $ex);
    $valid = 1;
    if ($pair[0] == 'ctag')
        $valid = 0;
    $pair['valid'] = $valid;
    $queryStringParamsArray[] = $pair;
}



if (!$miss) {
    $getStat = mysql_fetch_assoc(function_mysql_query("SELECT id FROM " . $appTable . " WHERE ctag='" . $ctag . "' AND rdate='" . date("Y-m-d") . "' LIMIT 1", __FILE__));
    if ($getStat['id']) {
        if (!$_COOKIE[$cookieName]) {
            //updateUnit($appTable,"views=views+1","id='".$getStat['id']."'");
            $informationArray = array();

            $informationArray['ip'] = $_SERVER['REMOTE_ADDR'];
            $informationArray['ctag'] = $ctag;
            $informationArray['uid'] = $uid;
            $informationArray['banner_id'] = $banner_id;
            $informationArray['group_id'] = $group_id;
            $informationArray['profile_id'] = $profile_id;
            $informationArray['affiliate_id'] = $affiliate_id;
            $informationArray['country_id'] = $country_id;

            if ($isProductSession) {
                $informationArray['merchant_id'] = 0;
                $informationArray['product_id'] = $getMerchant['id'];
            } else {
                $informationArray['product_id'] = 0;
                $informationArray['merchant_id'] = $getMerchant['id'];
            }

            $date = new DateTime();
            $informationArray['unixRdate'] = $date->getTimestamp();


            $informationArray['refer_url'] = trim($_SERVER['HTTP_REFERER']);
            $informationArray['views'] = 1;
            $informationArray['userAgent'] = substr($_SERVER['HTTP_USER_AGENT'], 0, 253);
            $informationArray['type'] = 'traffic';
            $informationArray['param'] = $freeParam;

            insert_new_traffic_record($informationArray);

            @setcookie($cookieName, md5(time()), time() + 3600);
        }
    } else {
        $db['rdate'] = date("Y-m-d");
        $db['ctag'] = $ctag;
        if ($getAffiliate['id'] AND $getAffiliate['valid'])
            $db['affiliate_id'] = $affiliate_id;
        else
            $db['affiliate_id'] = '1';
        $db['group_id'] = $getAffiliate['group_id'];
        $db['merchant_id'] = $getBanner['merchant_id'];
        $db['product_id'] = $getBanner['product_id'];
        $db['banner_id'] = $banner_id;
        if ($getProfile['id'] AND $getProfile['valid'])
            $db['profile_id'] = $profile_id;
        $db['views'] = 1;
        dbAdd($db, $appTable);
        setcookie($cookieName, md5(time()), time() + 3600);
    }

    //-------------------------------
    // START STATS
    //-------------------------------
    // Merchant Creative Statistics
    $updateImpressionStat = "INSERT INTO merchants_creative_stats(Date,AffiliateID,MerchantID,BannerID,Impressions,Clicks,CountryID)VALUES('" . date('Y-m-d') . "','" . $affiliate_id . "','" . $getBanner['merchant_id'] . "','" . $banner_id . "','1','0', '".$country_id."') ON DUPLICATE KEY UPDATE Impressions=(Impressions+1)";
    function_mysql_query($updateImpressionStat, __FILE__, __FUNCTION__);

    // Referral Report
    if(!empty($_SERVER['HTTP_REFERER'])){
        $updateImpressionStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,ProfileID,AllTimeViews)
                VALUES('" . date('Y-m-d') . "','" . md5($_SERVER['HTTP_REFERER']) . "','" . mysql_real_escape_string($_SERVER['HTTP_REFERER']) . "','" . $affiliate_id . "','" . $getBanner['merchant_id'] . "','".$profile_id."','1') ON DUPLICATE KEY UPDATE AllTimeViews=(AllTimeViews+1)";
        function_mysql_query($updateImpressionStat, __FILE__, __FUNCTION__);
    }
    
    //-------------------------------
    // END STATS
    //-------------------------------
}
if ($getBanner['type'] == "widget") {

    $pexp = explode(",", $getMerchant['params']);
    $parse_url = parse_url($getBanner['url']);
    for ($i = 0; $i <= count($pexp) - 1; $i++) {
        if ($i == "0") {
            if (!$parse_url['query'])
                $moreParams .= '&' . $pexp[$i] . '=' . $ctag;
            else
                $moreParams .= '&' . $pexp[$i] . '=' . $ctag;
        } else
            $moreParams .= '&' . $pexp[$i] . '=' . $ctag;
    }

    $parseURL = parse_url($getBanner['iframe_url']);
    $string = 'width=' . ($_GET['width'] ? $_GET['width'] : $getBanner['width']);
    $string .= '&height=' . ($_GET['height'] ? $_GET['height'] : $getBanner['height']);
    $string .= '&landing_url=' . urlencode($getBanner['url']);
    $string .= '&ctag=' . $_GET['ctag'];
    $string .= $moreParams;

    $addionalParams = "";
    foreach ($queryStringParamsArray as $queryStringParamsItem) {

        if ($queryStringParamsItem['valid'] == 1) {
            $addionalParams .= "&" . $queryStringParamsItem[0] . "=" . $queryStringParamsItem[1];
            ;

            // var_dump($queryStringParamsItem);
            // die();
        }
    }


    $finalurl = $getBanner['iframe_url'] . ($parseURL['query'] ? '&' : '?') . $string . $addionalParams;
    if ($debug)
        die($finalurl);
    _goto($finalurl);
}
else {
    _goto($set->webAddress . $getBanner['file']);
}

mysql_close();
die();
?>