<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}
ini_set('zlib.output_compression', 'On');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
ini_set('request_terminate_timeout', 500); //300 seconds = 5 minutes
ini_set('fastcgi_read_timeout', 500); //300 seconds = 5 minutes
ini_set('client_body_timeout', 5000); //300 seconds = 5 minutes
ini_set('client_header_timeout', 5000); //300 seconds = 5 minutes
ini_set('memory_limit', '-1');
set_time_limit(0);
//set_time_limit(1200);
//ini_set('memory_limit', '4048M');


/* client_header_timeout 3000;
client_body_timeout 3000;
fastcgi_read_timeout 3000;
client_max_body_size 32m;
fastcgi_buffers 8 128k;
fastcgi_buffer_size 128k;
 */
// set_time_limit(0);
$l = 1;
$i = 1;
//$basepath = $_SERVER['DOCUMENT_ROOT'];
$where = $_POST['where'];
//require_once($basepath . '/common/global.php');

$exportAdvancedReport  = isset($_POST['adv']) ? $_POST['adv'] : false;

require '../common/database.php';
require '../common/Excel.php';
require '../func/func_string.php';
// require '../func/func_debug.php';
require '../func/func_global.php';
require '../func/func_form.php';




$userlevel = $set->userInfo['level'];
if($userlevel == "affiliate"){
	
	$globalWhere = $_POST['globalWhere'];

	$globalWhereSales = str_replace('affiliate_id', 'tb1.affiliate_id', $globalWhere);
	$globalWhereSales2 = str_replace('t.affiliate_id','ds.affiliate_id', $globalWhere) ;
	$globalWhereReg = str_replace('t.affiliate_id','dg.affiliate_id', $globalWhere) ;
	
}
else{
	$globalWhere = "";
	$globalWhereSales = "";
	$globalWhereSales2 = "";
	$globalWhereReg = "";
}

$xls = new Excel('Report');


$sql = $_POST['sql'];

$sql =   (encrypt_decrypt('decrypt' ,$sql));

$ajaxSqlArray = json_decode($sql,true);

/* 
	$_GET['debug']=true;
	$_GET['dbg1']=true; */
	
/*
$partSQL = $ajaxSqlArray['select'] .  $ajaxSqlArray['midpart'] . $ajaxSqlArray['wherePart'] . $ajaxSqlArray['wherePart2']; // .  $ajaxSqlArray['order'];

$sqlPart1 = "SELECT t.*,mc.url as mc_url,mc.title as mc_title,lang.title as language, m.name as merchant_name,af.username as affiliate_username
			  from  (";
$sqlPart2 = ") t INNER JOIN merchants m on m.id = t.merchant_id INNER JOIN affiliates af on af.id = t.affiliate_id LEFT JOIN languages lang on lang.id = t.language_id LEFT JOIN merchants_creative mc on t.banner_id = mc.id WHERE 2=2 and 1 = 1 ORDER BY t.unixRdate";



// changed in 26 of Feb old (deprecated)
$sqlPart1 = "SELECT t.*,mc.url as mc_url,mc.title as mc_title,lang.title as language, m.name as merchant_name,af.username as affiliate_username
			  from  ";
$sqlPart2 = "traffic t INNER JOIN merchants m on m.id = t.merchant_id INNER JOIN affiliates af on af.id = t.affiliate_id LEFT JOIN languages lang on lang.id = t.language_id LEFT JOIN merchants_creative mc on t.banner_id = mc.id WHERE 2=2 and 1 = 1 ORDER BY t.unixRdate";

*/





// $sqlPart2 = ") t INNER JOIN merchants m on m.id = t.merchant_id INNER JOIN affiliates af on af.id = t.affiliate_id LEFT JOIN languages lang on lang.id = t.language_id LEFT JOIN merchants_creative mc on t.banner_id = mc.id WHERE 2=2 and 1 = 1 ORDER BY t.rdate,t.i

//old
//$sql = $sqlPart1 . $partSQL . $sqlPart2;

/* $uidTraderIDQuery = "select t.uid " . $ajaxSqlArray['midpart'].$ajaxSqlArray['wherePart'].$ajaxSqlArray['wherePart2'];  // select all uid from traffic table

 $data_reg_sql = "SELECT t.* FROM data_reg t"
							." WHERE " . $ajaxSqlArray['wherePart'];

							
	$sqlUIDnTraderID = "select traffic.uid , data_reg.* from ( " . $uidTraderIDQuery . " ) traffic 
						inner join 
									( " .$data_reg_sql . " ) data_reg on traffic.uid = data_reg.uid ";
									
	$UIDnTraderIDarray = array();
	
	$rsc = function_mysql_query($sqlUIDnTraderID);
	while ($uidRow = mysql_fetch_assoc($rsc)){
		$UIDnTraderIDarray[$uidRow['uid']] = $uidRow;
	}
	die("ENDTIME - "  .time()); */
	
	
/*
$allClicks = array();
if($_POST['format'] == 'xlsx'){
	$newSQL = $partSQL . " limit 0,65536";
    //// changed in 26 of Feb
//	$sql = $sqlPart1 . $newSQL. $sqlPart2;
	$sql = $sqlPart1 . $sqlPart2 . $newSQL;
	$clickqq = function_mysql_query($sql);
	$allClicks[] = $clickqq;
}
else{
	
	$i = 0;
	do{
		$blnHasRecords = false;
		$newSQL = $partSQL;
		$newSQL .= " limit " . $i . ", 10000";

		//// changed in 26 of Feb
	//	$sql = $sqlPart1 . $newSQL. $sqlPart2;
		$sql = $sqlPart1 . $sqlPart2 . $newSQL;


		$clickqq = function_mysql_query($sql);
		
		
				
		$allClicks[] = $clickqq;
		$i += 10000;
	}
	while(mysql_num_rows($clickqq));	
}
*/

$sql = "SELECT id, name FROM affiliates_profiles WHERE valid =1";
$qqProfiles = function_mysql_query($sql);
$listProfiles = array();
while($wwProfiles = mysql_fetch_assoc($qqProfiles)){
    $listProfiles[$wwProfiles['id']] = $wwProfiles['name'];
}

if (!empty($trader_id)){
    $rsc_trdr = mysql_query("select uid from data_reg where 1=1 ".(!empty($merchant_id) ? " and merchant_id =" . $merchant_id : "" )." and trader_id =  '" . $trader_id  .  "'  limit 1 ;");
    $uidrow = mysql_fetch_assoc($rsc_trdr);
    // var_dump($uidrow);
    // die();
    if (!empty($uidrow))
        $unique_id = $uidrow['uid'];

}

$clickArray = [];
$merchant_id = $_POST['merchant_id'];
$group_id = $_POST['group_id'];
$affiliate_id = $_POST['affiliate_id'];
$profile_id = $_POST['profile_id'];
$from = $_POST['from'];
$to = $_POST['to'];

$where = ' 1 = 1 ';

/**
 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
 */
if ($userlevel=='manager'){
    $group_id  = $set->userInfo['group_id'];

}
else
    $group_id  = null;


// $where    .= empty($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';



if ($merchant_id) {
    $where .= " AND merchant_id='".$merchant_id."' ";
}



if (!empty($group_id)) {
    $where .= " AND group_id='".$group_id."' ";
}
if ($banner_id) {
    $where .= " AND banner_id='".$banner_id."' ";
}

if ($affiliate_id) {
    $where .= " AND affiliate_id='".$affiliate_id."' ";
}

if ($profile_id) {
    $where  .= " AND profile_id='".$profile_id."' ";
}

$orderBy = "";
if(isset($sortBy) && $sortBy!=""){

    if($sortBy == "affiliate_username"){
        $sortBy_new = "af.username";
    }
    else if($sortBy == "merchant_name"){
        $sortBy_new = "m.name";
    }
    else if($sortBy == "trader_id" || $sortBy == "trader_alias"){
        $sortBy_new  = "";
    }
    else{
        $sortBy_new = "t." . $sortBy;
    }

    if(isset($sortOrder) &&$sortOrder!="")
    {
        if($sortBy_new !="")
            $orderBy = " order by " . $sortBy_new . " " . $sortOrder;
    }
    else{
        if($sortBy_new !="")
            $orderBy = " order by " . $sortBy_new . " ASC";
    }
}
else{
    $orderBy = "ORDER BY traffic.id DESC";
}

$merchantsArray = array();
$displayForex = 0;
$merchantsAr = getMerchants(0,1);


// $mer_rsc = function_mysql_query($sql,__FILE__);
// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
foreach ($merchantsAr as $arrMerchant) {

    if (strtolower($arrMerchant['producttype'])=='forex')
        $displayForex = 1;
    $merchantsArray[$arrMerchant['id']] = $arrMerchant;
}

//general merchants information
$merchantww = getMerchants($merchant_id ? $merchant_id:0,1);

$formula = $merchantww['rev_formula'];
$merchantID = $merchantww['id'];
$merchantName = strtolower($merchantww['name']);

$where_main = $where;
$where_main =  str_replace('affiliate_id','t.affiliate_id', $where_main) ;
$where_main =  str_replace('merchant_id','t.merchant_id', $where_main) ;
$where_main =  str_replace('group_id','t.group_id', $where_main) ;
$where_main =  str_replace('profile_id','t.profile_id', $where_main) ;
$where_main =  str_replace('banner_id','t.banner_id', $where_main) ;

$type_filter = "";
if($type == 'clicks' || empty($type))
    $type_filter = ' and traffic.clicks > 0';
else if($type == "views")
    $type_filter = ' and traffic.views > 0';

$sql = "SELECT * from traffic WHERE ".$where . $type_filter ." AND traffic.merchant_id > 0". (!empty($unique_id) ? ' and traffic.uid = ' . $unique_id :'')." and traffic.rdate >= '".$from."' AND traffic.rdate <='".$to. "' ". $orderBy;

$uids = [];
$data = [];

$clickArray = [];
$traficData = function_mysql_query($sql,__FILE__);
$traficDataFull = mysql_fetch_assoc($traficData);


while($item = mysql_fetch_assoc($traficData)){
    if (!empty($item['uid'])) {
        $uids[] = $item['uid'];
    }
    $data[$item['id']] = $item;
}


$uidString = implode(',', $uids);

// TODO: In current table is not used (commented for lesser request to database
/*  $sqlLanguages = "SELECT * from languages";
  $languagesData = function_mysql_query($sqlLanguages,__FILE__);*/

$sqlMerchants = "SELECT * from merchants";
$MerchantsData = function_mysql_query($sqlMerchants,__FILE__);
$MerchantsDataItems = [];
while($item = mysql_fetch_assoc($MerchantsData)){
    $MerchantsDataItems[$item['id']]['id'] = $item['id'];
    $MerchantsDataItems[$item['id']]['name'] = $item['name'];
}

$sqlMerchantsCreative = "SELECT * from merchants_creative";
$MerchantsCreativeData = function_mysql_query($sqlMerchantsCreative,__FILE__);
$MerchantsCreativeDataItems = [];
while($item = mysql_fetch_assoc($MerchantsCreativeData)){
    $MerchantsCreativeDataItems[$item['id']]['id'] = $item['id'];
    $MerchantsCreativeDataItems[$item['id']]['title'] = $item['title'];
    $MerchantsCreativeDataItems[$item['id']]['url'] = $item['url'];
}

$sqlAffiliates = "SELECT * from affiliates";
$AffiliatesData = function_mysql_query($sqlAffiliates,__FILE__);
$AffiliatesDataItems = [];
while($item = mysql_fetch_assoc($AffiliatesData)){
    $AffiliatesDataItems[$item['id']]['id'] = $item['id'];
    $AffiliatesDataItems[$item['id']]['username'] = $item['username'];
}

$sqlReportTraders = "SELECT * FROM ReportTraders  WHERE Date >= '".$from."' AND ClickDetails IN (".$uidString.")";
$ReportTradersData = function_mysql_query($sqlReportTraders,__FILE__);

$ReportTradersDataItems = [];
while($item = mysql_fetch_assoc($ReportTradersData)){
    $ReportTradersDataItems[$item['ClickDetails']]['volume'] += $item['Volume'];
    $ReportTradersDataItems[$item['ClickDetails']]['trader_id'] = $item['TraderID'];
    $ReportTradersDataItems[$item['ClickDetails']]['trader_name'] = $item['TraderAlias'];

    switch ($item['Type']) {
        case 'lead': $ReportTradersDataItems[$item['ClickDetails']]['leads'] += 1;
            break;
        case 'demo': $ReportTradersDataItems[$item['ClickDetails']]['demo'] += 1;
            break;
        case 'real': $ReportTradersDataItems[$item['ClickDetails']]['real'] += 1;
            break;
    }

    $ReportTradersDataItems[$item['ClickDetails']]['sale_status'] += $item['SaleStatus'];
    $ReportTradersDataItems[$item['ClickDetails']]['ftd'] += $item['FirstDeposit'];
    $ReportTradersDataItems[$item['ClickDetails']]['depositingAccounts'] += $item['TotalDeposits'];
    $ReportTradersDataItems[$item['ClickDetails']]['sumDeposits'] += $item['DepositAmount'];
    $ReportTradersDataItems[$item['ClickDetails']]['bonus'] += $item['BonusAmount'];
    $ReportTradersDataItems[$item['ClickDetails']]['withdrawal'] += $item['WithdrawalAmount'];
    $ReportTradersDataItems[$item['ClickDetails']]['chargeback'] += $item['ChargeBackAmount'];
    $ReportTradersDataItems[$item['ClickDetails']]['netRevenue'] += $item['NetDeposit'];
    $ReportTradersDataItems[$item['ClickDetails']]['pnl'] += $item['PNL'];

    if ($item['TotalDeposits'] > 0 || $item['Volume'] > 0 || $item['PNL'] > 0) {
        $ReportTradersDataItems[$item['ClickDetails']]['Qftd'] ++;
    }

    $ReportTradersDataItems[$item['ClickDetails']]['totalCom'] += $item['Commission'];

}

// while($item = mysql_fetch_assoc($traficData)){
foreach($data as $item){
    $clickArray[$item['id']]['traffic_id'] = $item['id'];
    $clickArray[$item['id']]['uid'] = $item['uid'];
    $clickArray[$item['id']]['clicks'] = $item['clicks'];
    $clickArray[$item['id']]['views'] = $item['views'];
    $clickArray[$item['id']]['traffic_date'] = $item['rdate'];

    $clickArray[$item['id']]['type'] = $item['type'];

    $clickArray[$item['id']]['banner_id'] = $item['banner_id'];
    $clickArray[$item['id']]['banner_title'] = $MerchantsCreativeDataItems[$item['banner_id']]['title'];
    $clickArray[$item['id']]['banner_url'] = $MerchantsCreativeDataItems[$item['banner_id']]['url'];
    $clickArray[$item['id']]['profile_id'] = $item['profile_id'] == 0 ? '' : $item['profile_id'];
    $clickArray[$item['id']]['param'] = $item['param'];
    $clickArray[$item['id']]['param2'] = $item['param2'];
    $clickArray[$item['id']]['param3'] = $item['param3'];
    $clickArray[$item['id']]['param4'] = $item['param4'];
    $clickArray[$item['id']]['param5'] = $item['param5'];
    $clickArray[$item['id']]['refer_url'] = $item['refer_url'];
    //    $clickArray[$item['id']]['language'] = $languagesData[$item['language']]['title'];
    $clickArray[$item['id']]['country'] = $item['country_id'];
    $clickArray[$item['id']]['ip'] = $item['ip'];
    $clickArray[$item['id']]['merchant_name'] = $MerchantsDataItems[$item['merchant_id']]['name'];
    $clickArray[$item['id']]['affiliate_username'] = $AffiliatesDataItems[$item['affiliate_id']]['username'];
    $clickArray[$item['id']]['affiliate_id'] = $item['affiliate_id'];

    $clickArray[$item['id']]['platform'] = $item['platform'];

    $clickArray[$item['id']]['volume'] = $ReportTradersDataItems[$item['uid']]['volume'];
    $clickArray[$item['id']]['trader_id'] = $ReportTradersDataItems[$item['uid']]['trader_id'];
    $clickArray[$item['id']]['trader_name'] = $ReportTradersDataItems[$item['uid']]['trader_name'];
    $clickArray[$item['id']]['leads'] = $ReportTradersDataItems[$item['uid']]['leads'];
    $clickArray[$item['id']]['demo'] = $ReportTradersDataItems[$item['uid']]['demo'];
    $clickArray[$item['id']]['real'] = $ReportTradersDataItems[$item['uid']]['real'];
    $clickArray[$item['id']]['sale_status'] = $ReportTradersDataItems[$item['uid']]['sale_status'];
    $clickArray[$item['id']]['ftd_amount'] = $ReportTradersDataItems[$item['uid']]['ftd_amount'];

    $clickArray[$item['id']]['ftd'] = $ReportTradersDataItems[$item['uid']]['ftd'];
    $clickArray[$item['id']]['depositingAccounts'] = $ReportTradersDataItems[$item['uid']]['depositingAccounts'];
    $clickArray[$item['id']]['sumDeposits'] = $ReportTradersDataItems[$item['uid']]['sumDeposits'];
    $clickArray[$item['id']]['bonus'] = $ReportTradersDataItems[$item['uid']]['bonus'];
    $clickArray[$item['id']]['withdrawal'] = $ReportTradersDataItems[$item['uid']]['withdrawal'];
    $clickArray[$item['id']]['chargeback'] = $ReportTradersDataItems[$item['uid']]['chargeback'];
    $clickArray[$item['id']]['netRevenue'] = $ReportTradersDataItems[$item['uid']]['netRevenue'];
    $clickArray[$item['id']]['pnl'] = $ReportTradersDataItems[$item['uid']]['pnl'];
    $clickArray[$item['id']]['Qftd'] = $ReportTradersDataItems[$item['uid']]['Qftd'];
    $clickArray[$item['id']]['totalCom'] = $ReportTradersDataItems[$item['uid']]['totalCom'];

    if(empty($item['os']))
        $clickArray[$item['id']]['platform'] = "";


    $clickArray[$item['id']]['os'] = $item['os'];
    $clickArray[$item['id']]['osVersion'] = $item['osVersion'];

    $clickArray[$item['id']]['browser'] = $item['browser'];
    $clickArray[$item['id']]['browserVersion'] = $item['broswerVersion'];
}
		
		
		
		if($_POST['format'] == "csv"){
		$mainArry = [];
		
			$mainArry[0][] =lang('ID');
		  $mainArry[0][] =lang('UID');
		  $mainArry[0][] =lang('Impression');
		  $mainArry[0][] =lang('Click');
		  $mainArry[0][] =lang('Affiliate ID');
		  $mainArry[0][] =lang('Affiliate Username');
		  $mainArry[0][] =lang('Date');
		  $mainArry[0][] =lang('Type');
		  $mainArry[0][] =lang('Merchant');
		  $mainArry[0][] =lang('Banner ID');
		  $mainArry[0][] =lang('Profile ID');
		  $mainArry[0][] =lang('Param');
		  $mainArry[0][] =lang('Param2');
		  $mainArry[0][] =lang('Refer URL');
		  $mainArry[0][] =lang('Country');
		  $mainArry[0][] =lang('IP');
		  $mainArry[0][] =lang('Platform');
		  $mainArry[0][] =lang('Operating System');
		  $mainArry[0][] =lang('OS Version');
		  $mainArry[0][] =lang('Browser');
		  $mainArry[0][] =lang('Browser Version');
		  $mainArry[0][] =lang('Trader ID');
		  $mainArry[0][] =lang('Trader Alias');
		  $mainArry[0][] =lang('Lead');
		  $mainArry[0][] =lang('Demo');
		  $mainArry[0][] =lang('Sale Status');
		  $mainArry[0][] =lang('Accounts');
		  $mainArry[0][] =lang('FTD');
		  $mainArry[0][] =lang('FTD Amount');
		  $mainArry[0][] =lang('Total FTD');
		  $mainArry[0][] =lang('Total FTD Amount');
		  $mainArry[0][] =lang('Total Deposits');
		  $mainArry[0][] =lang('Deposits Amount');
		  $mainArry[0][] =lang('Volume');
		  $mainArry[0][] =lang('Bonus Amount');
		  $mainArry[0][] =lang('Withdrawal Amount');
		  $mainArry[0][] =lang('ChargeBack Amount');
		  $mainArry[0][] =lang('Net Deposits');
		  $mainArry[0][] =lang('PNL');
		  $mainArry[0][] =lang('Active Traders');
		  $mainArry[0][] =lang('Commission');
		  
		  
		  	foreach($clickArray as $data){
		//	if($l == 65536) break;
			$refer_url = $data['refer_url'];
		    if(strlen($data['refer_url'])>50)
				$refer_url = substr($data['refer_url'],0,49). "...";
			
			
			
			if(!empty($data['country']) && strlen($data['country'])==2){
				if (empty($allCountriesArray)){
					$allCountriesArray = getDBCountries();
				}
				
				$country_name = $allCountriesArray[$data['country'] ];
			}
			else
				$country_name = "";
			if(strtolower($country)=='any'){
				$country_name = "";
			}
			
			
	
			  $mainArry[$i][] = $data['traffic_id'];
			  $mainArry[$i][] = $data['uid'];
			  $mainArry[$i][] = @number_format($data['views'],0);
			  $mainArry[$i][] = @number_format($data['clicks'],0);
			  $mainArry[$i][] = $data['affiliate_id'];
			  $mainArry[$i][] = $data['affiliate_username'];
			  $mainArry[$i][] = $data['traffic_date'];
			  $mainArry[$i][] = ucwords($data['type']);
			  $mainArry[$i][] = $data['merchant_name'];
			  $mainArry[$i][] = $data['banner_id'];
			  $mainArry[$i][] = $data['profile_id'];
			  $mainArry[$i][] = $data['param'];
			  $mainArry[$i][] = $data['param2'];
			  $mainArry[$i][] = $refer_url;
			  $mainArry[$i][] = $country_name;
			  $mainArry[$i][] = $data['ip'];
			  $mainArry[$i][] = ucwords($data['platform']);
			  $mainArry[$i][] = $data['os'];
			  $mainArry[$i][] = $data['osVersion'];
			  $mainArry[$i][] = $data['browser'];
			  $mainArry[$i][] = $data['browserVersion'];
			  $mainArry[$i][] = $data['trader_id'];
			  $mainArry[$i][] = $data['trader_name'];
			  $mainArry[$i][] = $data['leads'];
			  $mainArry[$i][] = $data['demo'];
			  $mainArry[$i][] = $data['sale_status'];
			  $mainArry[$i][] = $data['real'];
			  $mainArry[$i][] = $data['ftd'];
			  $mainArry[$i][] = price_new($data['ftd_amount']);
			  $mainArry[$i][] = $data['real_ftd'];
			  $mainArry[$i][] = price_new($data['real_ftd_amount']);
			  $mainArry[$i][] = $data['depositingAccounts'];
			  $mainArry[$i][] = price_new($data['sumDeposits']);
			  $mainArry[$i][] = price_new($data['volume']);
			  $mainArry[$i][] = price_new($data['bonus']);
			  $mainArry[$i][] = price_new($data['withdrawal']);
			  $mainArry[$i][] = price_new($data['chargeback']);
			  $mainArry[$i][] = price_new($data['netRevenue']);
			  $mainArry[$i][] = price_new($data['PNL']);
			  $mainArry[$i][] = ($data['Active Traders']);
			  $mainArry[$i][] = price_new($data['totalCom']);
			  
			  $i++;
			}
			
			//CSV
			
			
			$file = "../files/exports/" . $_POST['filename'].'.csv';
			 
			 if (!is_dir('../files/exports')) {
				 mkdir('../files/exports');
			 }
			 
			$file = fopen($file,"w");
			foreach($mainArry as $line) {
				
				fputcsv($file,$line);
			}

			fclose($file); 
			$file = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/files/exports/'. $_POST['filename'].'.csv';
			//$file = __DIR__ .'/../files/exports/'. $_POST['filename'].'.csv';
			echo json_encode(array('file'=>$file,'status'=>'ok'));exit;
			exit;
		}
		
		//EXCEL 
		
		ob_start();
		
		//header
		$xls->home();
		 $xls->label(lang('ID'));
		  $xls->right();
		  $xls->label(lang('ID'));
		  $xls->right();
		  $xls->label(lang('Impression'));
		  $xls->right();
		  $xls->label(lang('Click'));
		  $xls->right();
		  $xls->label(lang('Affiliate ID'));
		  $xls->right();
		  $xls->label(lang('Affiliate Username'));
		  $xls->right();
		  $xls->label(lang('Date'));
		  $xls->right();
		  $xls->label(lang('Type'));
		  $xls->right();
		  $xls->label(lang('Merchant'));
		  $xls->right();
		  $xls->label(lang('Banner ID'));
		  $xls->right();
		  $xls->label(lang('Profile ID'));
		  $xls->right();
		  $xls->label(lang('Param'));
		  $xls->right();
		  $xls->label(lang('Param2'));
		  $xls->right();
		  $xls->label(lang('Refer URL'));
		  $xls->right();
		  $xls->label(lang('Country'));
		  $xls->right();
		  $xls->label(lang('IP'));
		  $xls->right();
		  $xls->label(lang('Platform'));
		  $xls->right();
		  $xls->label(lang('Operating System'));
		  $xls->right();
		  $xls->label(lang('OS Version'));
		  $xls->right();
		  $xls->label(lang('Browser'));
		  $xls->right();
		  $xls->label(lang('Browser Version'));
		  $xls->right();
		  $xls->label(lang('Trader ID'));
		  $xls->right();
		  $xls->label(lang('Trader Alias'));
		  $xls->right();
		  $xls->label(lang('Lead'));
		  $xls->right();
		  $xls->label(lang('Demo'));
		  $xls->right();
		  $xls->label(lang('Sale Status'));
		  $xls->right();
		  $xls->label(lang('Accounts'));
		  $xls->right();
		  $xls->label(lang('FTD'));
		  $xls->right();
		  $xls->label(lang('FTD Amount'));
		  $xls->right();
		  $xls->label(lang('Total FTD'));
		  $xls->right();
		  $xls->label(lang('Total FTD Amount'));
		  $xls->right();
		  $xls->label(lang('Total Deposits'));
		  $xls->right();
		  $xls->label(lang('Deposits Amount'));
		  $xls->right();
		  $xls->label(lang('Volume'));
		  $xls->right();
		  $xls->label(lang('Bonus Amount'));
		  $xls->right();
		  $xls->label(lang('Withdrawal Amount'));
		  $xls->right();
		  $xls->label(lang('ChargeBack Amount'));
		  $xls->right();
		  $xls->label(lang('Net Deposits'));
		  $xls->right();
		  $xls->label(lang('Commission'));
		  $xls->down();
		
		
		foreach($clickArray as $data){
		//	if($l == 65536) break;
			$refer_url = $data['refer_url'];
		    if(strlen($data['refer_url'])>50)
				$refer_url = substr($data['refer_url'],0,49). "...";
			
			$country_name = $allCountriesArray[$data['country'] ];
			if(strtolower($country)=='any'){
				$country_name = "";
			}
	
			  $xls->home();
			  $xls->label($data['traffic_id']);
			  $xls->right();
			  $xls->label($data['uid']);
			  $xls->right();
			  $xls->label(@number_format($data['views'],0));
			  $xls->right();
			  $xls->label(@number_format($data['clicks'],0));
			  $xls->right();
			  $xls->label($data['affiliate_id']);
			  $xls->right();
			  $xls->label($data['affiliate_username']);
			  $xls->right();
			  $xls->label($data['traffic_date']);
			  $xls->right();
			  $xls->label(ucwords($data['type']));
			  $xls->right();
			  $xls->label($data['merchant_name']);
			  $xls->right();
			  $xls->label($data['banner_id']);
			  $xls->right();
			  $xls->label($data['profile_id']);
			  $xls->right();
			  $xls->label($data['param']);
			  $xls->right();
			  $xls->label($data['param2']);
			  $xls->right();
			  $xls->label($refer_url);
			  $xls->right();
			  $xls->label($country_name);
			  $xls->right();
			  $xls->label($data['ip']);
			  $xls->right();
			  $xls->label(ucwords($data['platform']));
			  $xls->right();
			  $xls->label($data['os'] );
			  $xls->right();
			  $xls->label($data['osVersion'] );
			  $xls->right();
			  $xls->label($data['browser']);
			  $xls->right();
			  $xls->label($data['browserVersion'] );
			  $xls->right();
			  $xls->label($data['trader_id'] );
			  $xls->right();
			  $xls->label($data['trader_name']);
			  $xls->right();
			  $xls->label($data['leads']);
			  $xls->right();
			  $xls->label($data['demo']);
			  $xls->right();
			  $xls->label($data['sale_status'] );
			  $xls->right();
			  $xls->label($data['real']);
			  $xls->right();
			  $xls->label($data['ftd']);
			  $xls->right();
			  $xls->label(price_new($data['ftd_amount']));
			  $xls->right();
			  $xls->label($data['real_ftd']);
			  $xls->right();
			  $xls->label(price_new($data['real_ftd_amount']));
			  $xls->right();
			  $xls->label($data['depositingAccounts']);
			  $xls->right();
			  $xls->label(price_new($data['sumDeposits']));
			  $xls->right();
			  $xls->label(price_new($data['volume']));
			  $xls->right();
			  $xls->label(price_new($data['bonus']));
			  $xls->right();
			  $xls->label(price_new($data['withdrawal']));
			  $xls->right();
			  $xls->label(price_new($data['chargeback']));
			  $xls->right();
			  $xls->label(price_new($data['netRevenue']));
			  $xls->right();
			  $xls->label(price_new($data['totalCom']));
			  $xls->down();
				$l++;
		}
      
		

				$xls->send();
				$test = ob_get_clean();
			
				/* if($_POST['format'] == 'csv'){
					file_put_contents(__DIR__ .'/'. $_POST['filename'].'.csv', $test);
					$file = __DIR__ .'/'. $_POST['filename'].'.csv';
				}
				else{ */
					$path = $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/files/exports/'. $_POST['filename'].'.xls';
					file_put_contents(	$path, $test);
					$file =$path;
				//}
				
				if($_POST['format'] == 'xlsx'){
					if($l>=65536)
					{
						echo json_encode(array('file'=>$file,'status'=>'big'));exit;
					}
					else{
						echo json_encode(array('file'=>$file,'status'=>'ok'));exit;
					}
				}
				/* else{
					echo json_encode(array('file'=>$file,'status'=>'ok'));exit;
				}		 */		
			
				
function price_new($price=0) {
$num = @number_format($price,2);
return $set->currency . ' '.$num;
}			
?>