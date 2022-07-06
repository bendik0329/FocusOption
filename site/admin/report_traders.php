<?php

require_once '../vendor/autoload.php';
use UAParser\Parser;
$parser_ua = Parser::create();

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
ini_set('memory_limit', '5000M');
ini_set('max_execution_time', 1000);



require_once('common/global.php');


$_SESSION['session_id'] = 1;
$result_admin = function_mysql_query("SELECT admins.id, admins.preferedCurrency, admins.rdate, admins.ip, admins.chk_ip, admins.valid, admins.relatedMerchantID, admins.lang, admins.level, admins.username, admins.password, admins.first_name, admins.last_name, admins.email, admins.lastactive, admins.logged, admins.group_id, admins.phone, admins.IMUser, admins.zopimChat, admins.bigPic, admins.userType, merchants.producttype AS productType FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE admins.id='" . $_SESSION['session_id'] . "' AND admins.valid='1'", __FILE__, __FUNCTION__);
$chk_admin = mysql_fetch_assoc($result_admin);
$_SESSION['session_serial'] = $session_serial = md5($chk_admin['username'] . $chk_admin['password'] . $chk_admin['id']);
$set->userInfo = $chk_admin;

require_once('common/subAffiliateData.php');


$report_path = $_SERVER['DOCUMENT_ROOT'];

$lout = !empty($set->SSLprefix) ? $set->SSLprefix : "/admin/";



$merchantsArray = array();
$displayForex = $isCasino = $isSportbet = 0;
$merchantsAr = getMerchants(0, 1);

// $mer_rsc = function_mysql_query($sql,__FILE__);
// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
foreach ($merchantsAr as $arrMerchant) {



    if (strtolower($arrMerchant['producttype']) == 'forex' && $displayForex == 0){
        $displayForex = 1;
    }
    if (strtolower($arrMerchant['producttype']) == 'sportsbetting' && $displayForex == 0){
        $isSportbet = 1;
    }
    if (strtolower($arrMerchant['producttype']) == 'casino' && $displayForex == 0){
        $isCasino = 1;
    }

    $merchantsArray[$arrMerchant['id']] = $arrMerchant;
}

// All Affiliates.
$affiliate_id = "";


$qqAff = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC", __FILE__);
$listOfAffiliates = '';


$showPNL = $set->deal_pnl == 1;
$hideDemoAndLeads = hideDemoAndLeads();

/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();
// var_dump($arrDealTypeDefaults);
// die();

$allCountriesArray = getDBCountries();


$from = date("d/m/Y");
$to = date("d/m/Y") . " 23:59:59";

    
if($_GET['back'] == 1){
    
    $getLastDateTraderReportResult = function_mysql_query("SELECT Date FROM ReportTraders ORDER BY Date ASC LIMIT 1", __FILE__);    
    if(!empty($getLastDateTraderReportResult)){
        $getLastDateTraderReport = mysql_fetch_assoc($getLastDateTraderReportResult);
        if(!empty($getLastDateTraderReport['Date'])){
            $from = date("d/m/Y",strtotime($getLastDateTraderReport['Date'].' -1 days'));
            $to = date("d/m/Y 23:59:59",strtotime($getLastDateTraderReport['Date'].' -1 days'));
        }
    }
}

if(!empty($_GET['my_date'])){
    $from = date("d/m/Y",strtotime($_GET['my_date']));
    $to = date("d/m/Y 23:59:59",strtotime($_GET['my_date']));
}

$from = strTodate($from);
$to = strTodate($to);

$from = sanitizeDate($from);
$to = sanitizeDate($to);

commonGlobalSetTimeRange($from, $to);

$globalWhere = ' 1 = 1 AND ';

//Prevent  direct browse on reports pages under reports directory
define('DirectBrowse', TRUE);


//////////////////////////////// START REPORT
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];

if (!defined('DirectBrowse')) {
    $path = "http" . $set->SSLswitch . "://" . $_SERVER[HTTP_HOST];
    header("Location: " . $path . "/" . $userlevel);
}

if ($userlevel == "manager") {
    $globalWhere = "group_id = '" . $set->userInfo['group_id'] . "' AND ";
} else{
    $globalWhere = '';
}


if ($set->showProductsPlace == 1) {
    $productsArray = array();
    $sql = "select * from products_items";
    $prdcResource = function_mysql_query($sql, __FILE__);
    while ($row = mysql_fetch_assoc($prdcResource)) {
        $productsArray[$row['id']] = $row;
    }
}

$sql = "select id,name from affiliates_profiles where valid =1";
$qqProfiles = function_mysql_query($sql);
$listProfiles = array();
while ($wwProfiles = mysql_fetch_assoc($qqProfiles)) {
    $listProfiles[$wwProfiles['id']] = $wwProfiles['name'];
}

$pageTitle = lang(ptitle('Trader Report'));





$filename = "Trader_data_" . date('YmdHis');

if ($userlevel == 'manager') {
    $group_id = $set->userInfo['group_id'];
}


$arrAllTraders = [];
$merchant_id = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;


// List of wallets.
$arrWallets = [];
$sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
$resourceWallets = function_mysql_query($sql, __FILE__);
while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
    $arrWallets[$arrWallet['wallet_id']] = false;
    unset($arrWallet);
}






$merchantsArray = array();
$merchantsAr = getMerchants($merchant_id, 1);
foreach ($merchantsAr as $arrMerchant) {

    $allTranz = array();



    $loopedMerchant_ID = $arrMerchant['id'];



    $where = '';

    if ($affiliate_id){
        $where .= " AND affiliate_id='" . $affiliate_id . "' ";
    }
    if ($group_id){
        $where .= " AND group_id='" . $group_id . "' ";
    }
    if ($banner_id){
        $where .= " AND banner_id='" . $banner_id . "' ";
    }
    if ($profile_id){
        $where .= " AND profile_id='" . $profile_id . "' ";
    }
    if ($set->showDynamicFilters == 1 && $dynamic_filter){
        $where .= " AND dynamic_filter='" . $dynamic_filter . "' ";
    }
    if ($trader_id){
        $where .= " AND trader_id='" . $trader_id . "' ";
    }
    if ($param) {
        $param = trim($param);
        $where .= " AND freeParam='" . $param . "' ";
    }

    if ($param2){
        $where .= " AND freeParam2='" . $param2 . "' ";
    }
    if ($email && $set->ShowEmailsOnTraderReportForAdmin) {
        $email = trim($email);
        $where .= " AND lower(email) like '%" . strtolower($email) . "%' ";
    }



    if (($trader_alias || ($email && $set->ShowEmailsOnTraderReportForAdmin))) {
        if ($trader_alias) {

            if (preg_match('/^[-a-zA-Z0-9 ._]+$/', $trader_alias)) {

                $qry = "select trader_id from data_reg  where  merchant_id=" . $arrMerchant['id'] . " and 
                            
                            (lower(trader_alias) like ('%" . mysql_real_escape_string(strtolower($trader_alias)) . "%') )";
            } else {
                $qry = "select trader_id from (select  convert(group_concat(name separator '') using 'utf8') as alias,trader_id,rdate,merchant_id from(SELECT trader_id,rdate,merchant_id,ID,SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1) value, char(SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1) USING 'utf32') name
  FROM data_reg t CROSS JOIN 
  (
   SELECT a.N + b.N * 10 + 1 n
     FROM 
    (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
   ,(SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
    ORDER BY n 
   ) n
 WHERE  n.n <= 1 + (LENGTH(trim(leading ',' from replace(t.trader_alias,'&#',','))) - LENGTH(REPLACE(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', '')))
                                   ) as t1 group by id) final where  alias = '" . strtolower(trim($trader_alias)) . "' " . $where . "  AND rdate between '" . $from . "' and '" . $to . "' ";
            }
        } else if ($email && $set->ShowEmailsOnTraderReportForAdmin) {
            $qry = "select trader_id from data_reg  where  merchant_id=" . $arrMerchant['id'] . " AND lower(email) like '%" . mysql_real_escape_string(strtolower($email)) . "%' ";
        }

        $row = function_mysql_query($qry, __FILE__);

        while ($arrTraders = mysql_fetch_assoc($row)) {
            $arrAllTraders[] = $arrTraders['trader_id'];
            unset($arrTraders);
        }
    }




    if ($country_id) {
        $where .= " AND country='" . $country_id . "' ";
    }

    $ftdAmount = 0;
    $ftd = $totalTraders = $depositAmount = $total_deposits = $microPaymentsCount = $microPaymentsAmount = $ftdAmount = $volumeAmount = 0;
    $totalCom = $bonusAmount = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
    $spreadAmount = $pnl = 0;
    $ftdUsers = '';



    output_memory('', 'A3', '');

    $traderIDForCheck = true;
    $typeFilter = "";
    $typeDateFilter = "";
    $dateFilterFieldName = "rdate";
    $addInitialFTDPart = " and initialftddate>'" . $from . "'  ";
    if (empty($type) || $type == 'allaccounts') {
        $typeDateFilter = " and rdate between '" . $from . "' and '" . $to . "' ";
    
    } else if ($type == 'real') {
        $typeDateFilter = " and rdate between '" . $from . "' and '" . $to . "' ";
        $typeFilter = " and type='real' ";
    } else if ($type == 'lead') {
        $typeDateFilter = " and rdate between '" . $from . "' and '" . $to . "' ";
        $typeFilter = " and type='lead' ";
    } else if ($type == 'demo') {
        $typeDateFilter = " and rdate between '" . $from . "' and '" . $to . "' ";
        $typeFilter = " and type='demo' ";
    } else if ($type == 'frozen') {
        $typeDateFilter = " and rdate between '" . $from . "' and '" . $to . "' ";
        $typeFilter = " and status='frozen' ";
    } else if ($type == 'ftd' || $type == 'totalftd') {
        // $typeDateFilter  = " and ((initialftddate between '" . $from . "' and '" . $to . "'  ) || (FTDqualificationDate between '" . $from . "' and '" . $to . "' ))";
        $typeDateFilter = " and ((initialftddate between '" . $from . "' and '" . $to . "'  ) )";
        $typeFilter .= " and status<>'frozen' and type<>'demo' ";
    } else if ($type == 'activeTrader') {
        $addInitialFTDPart = "";
        $typeDateFilter = " and ((FTDqualificationDate between '" . $from . "' and '" . $to . "'  ) )";
        $typeFilter .= " and status<>'frozen' and type<>'demo' ";
    } else {
        $typeFilter = " and 1 =2 ";
    }

    $typeFilter .= $typeDateFilter;


    $traders = implode(",", $arrAllTraders);
    $data_reg_from_fields = "
                            data_reg.uid,
                            data_reg.merchant_id,
data_reg.trader_id,
data_reg.ftdamount,
data_reg.id,
data_reg.lastTimeActive,
data_reg.FTDqualificationDate,
data_reg.initialftddate,
data_reg.initialftdtranzid as tranz_id,
data_reg.group_id,
data_reg.affiliate_id,
data_reg.profile_id,
data_reg.product_id,
data_reg.isSelfDeposit,
data_reg.freeParam,
data_reg.freeParam2,
data_reg.freeParam3,
data_reg.freeParam4,
data_reg.freeParam5,
data_reg.banner_id,
" . ($set->showDynamicFilters == 1 ? "data_reg.dynamic_filter," : "") . " 
data_reg.status,
data_reg.type,
data_reg.trader_alias,
data_reg.email,
data_reg.phone,
data_reg.country,
data_reg.saleStatus,
data_reg.rdate,
data_reg.lastSaleNote,
data_reg.lastSaleNoteDate,
data_reg.campaign_id
";

/*
    $qry = "select " . $data_reg_from_fields . ($displayForex == 1 ? ", (select count(*) from data_reg dg where dg.merchant_id>0 and dg.trader_id=data_reg.trader_id group by dg.trader_id) as sub_trader_count" : "") . " from data_reg where merchant_id = " . $loopedMerchant_ID
            . (!empty($traders) ? " and trader_id in (" . $traders . " ) " : "")
            . $typeFilter
            . $where
            . " GROUP BY merchant_id, trader_id;";
*/            

    $qry = "SELECT dr1.* FROM data_reg dr1 WHERE dr1.merchant_id = " . $arrMerchant['id'] . " and dr1.rdate>='".$from."' and dr1.rdate<='".$to."' GROUP BY dr1.merchant_id, dr1.trader_id 
        union all 
    SELECT dr2.* FROM data_reg dr2 inner join data_sales ds on ds.trader_id=dr2.trader_id WHERE dr2.merchant_id = " . $arrMerchant['id'] . " and dr2.rdate<'".$from."' and ds.rdate>='".$from."' and ds.rdate<='".$to."' GROUP BY dr2.merchant_id, dr2.trader_id";


    echo $qry."<br>---------------<br>";







    if ($set->deal_pnl == 1) {
        //$BigMainqry = "select initialftddate,isSelfDeposit,affiliate_id, group_id,merchant_id , trader_id from data_reg where 1=1 " . $typeFilter . $where;   // for pnl Join fix
        $BigMainqry = "SELECT
            dr1.initialftddate,
            dr1.isSelfDeposit,
            dr1.affiliate_id,
            dr1.group_id,
            dr1.merchant_id,
            dr1.trader_id
        FROM
            data_reg dr1
        WHERE
            dr1.merchant_id = " . $arrMerchant['id'] . " AND dr1.rdate >= '".$from."' AND dr1.rdate <= '".$to."'
        GROUP BY
            dr1.merchant_id,
            dr1.trader_id
        UNION ALL
    SELECT
        dr2.initialftddate,
            dr2.isSelfDeposit,
            dr2.affiliate_id,
            dr2.group_id,
            dr2.merchant_id,
            dr2.trader_id
    FROM
        data_reg dr2
    INNER JOIN data_sales ds1 ON
        ds1.trader_id = dr2.trader_id
    WHERE
        dr2.merchant_id = " . $arrMerchant['id'] . " AND dr2.rdate < '".$from."' AND ds1.rdate >= '".$from."' AND ds1.rdate <= '".$to."'
    GROUP BY
        dr2.merchant_id,
        dr2.trader_id";
        
        $DataArray = array(
            "affiliate_id" => $affiliate_id,
            //"fromdate" => $from,
            //"todate" => $to,
            "group_id" => $group_id,
            "banner_id" => $banner_id,
            "profile_id" => $profile_id,
            "country_id" => $country_id,
            "trader_id" => $trader_id,
            "merchant_id" => $merchant_id);
        // die ($BigMainqry);  
        $pnlsql = generatePNLqueryForTraderReport($DataArray, false, $BigMainqry);
        
        echo $pnlsql."<br>---------------<br>";
 
        
        $pnlResultsArray = array();
        $pnlsqlRsc = function_mysql_query($pnlsql);
        if (!empty($pnlsqlRsc)) {
            while ($pnlRow = mysql_fetch_assoc($pnlsqlRsc)) {

                $pnlResultsArray[$pnlRow['merchant_id']][$pnlRow['trader_id']][] = $pnlRow;
            }
        }
        
        /*
        echo "<br>---------------<br>";
        echo "<pre>";
        print_r($pnlResultsArray['1']['4420105']);
        echo "</pre>";
        */
    }

    $tradersProccessedForLots = array();
    $tradersProccessedForPNL = array();
    $PNLPerformanceAgregationArray = array();


    $netDepositTransactions = array();


    $acountries = getLongCountries('sales');



    $trader_report_resource = function_mysql_query($qry);
    while ($traderInfo = mysql_fetch_assoc($trader_report_resource)) {

        $arrRes = $traderInfo;

        $traderIssue = "";



        if ($traderInfo['initialftddate'] > "0000-00-00 00:00:00") {


            $arrFtd = $traderInfo;
            $arrFtd['rdate'] = $traderInfo['initialftddate'];

            $arrFtd['FTDqualificationDate'] = $traderInfo['FTDqualificationDate'];

            if ($type == 'activeTrader') {
                $arrFtd['rdate'] = $traderInfo['FTDqualificationDate'];
            }


            $arrFtd['amount'] = $traderInfo['ftdamount'];
            $beforeNewFTD = $ftd;

            getFtdByDealType($traderInfo['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $arrFtd['amount'], $ftd);

            if ($beforeNewFTD == $ftd) {


                $traderInfo['traderIssue'] = ($traderIssue);
                $arrFtd['traderIssue'] = ($traderIssue);
            } else {
                
            }
            unset($arrFtd);
            // commission part is calculated below that part is only to skip the calculation of RAW non qualified ftds when filter FTD.
        }




        $totalCom = 0;

        $bannerInfo = getCreativeInfo($arrRes['banner_id'], 1);



        if (empty($allTranz)) {

            $qry = "select * from ( select dr.FTDqualificationDate , dr.banner_id, dr.profile_id, dr.country,dr.initialftddate , ds.id,dr.affiliate_id,ds.tranz_id,ds.trader_id,ds.merchant_id,ds.amount,ds.rdate,ds.status,ds.type as data_sales_type from 
                                    (
                                    SELECT
            dr1.initialftddate,
            dr1.affiliate_id,
            dr1.trader_id,
            dr1.country,
            dr1.merchant_id,
            dr1.banner_id,
            dr1.profile_id,
            dr1.FTDqualificationDate
        FROM
            data_reg dr1
        WHERE
            dr1.merchant_id = ".$arrMerchant['id']." AND dr1.rdate >= '".$from."' AND dr1.rdate < '".$to."' AND dr1.initialftddate > dr1.rdate - INTERVAL 1 DAY
        GROUP BY
            dr1.merchant_id,
            dr1.trader_id
        UNION ALL
    SELECT
        dr2.initialftddate,
        dr2.affiliate_id,
        dr2.trader_id,
        dr2.country,
        dr2.merchant_id,
        dr2.banner_id,
        dr2.profile_id,
        dr2.FTDqualificationDate
    FROM
        data_reg dr2
    INNER JOIN data_sales ds1 ON
        ds1.trader_id = dr2.trader_id
    WHERE
        dr2.merchant_id = ".$arrMerchant['id']." AND dr2.rdate < '".$from."' AND ds1.rdate >= '".$from."' AND ds1.rdate < '".$to."' AND dr2.initialftddate > dr2.rdate - INTERVAL 1 DAY
    GROUP BY
        dr2.merchant_id,
        dr2.trader_id
                                    ) dr 
                                            inner join data_sales ds on dr.merchant_id = ds.merchant_id and ds.trader_id = dr.trader_id  
                                            
                                                                where ds.type <>'PNL'
                                                                order by ds.merchant_id, ds.trader_id
                                                                )a
                                                                group by merchant_id , tranz_id , data_sales_type
                                                                ";
            
            echo $qry."<br>---------------<br>";
            //die ();
            $resource = function_mysql_query($qry, __FILE__);
            while ($arrAmount = mysql_fetch_assoc($resource)) {
                $allTranz[$arrAmount['merchant_id']][$arrAmount['trader_id']][] = $arrAmount;
            }
        }



        $total_deposits = 0;
        $microPaymentsCount = 0;
        $microPaymentsAmount = 0;




        $traderRows = $allTranz[$arrRes['merchant_id']][$arrRes['trader_id']];
        
        /*
        if($arrRes['trader_id'] == 4419900){
            print_r($traderRows);
            die();
        }
        */
        
        $traderInfo['traderHasFTD'] = $traderInfo['initialftddate'] == '0000-00-00 00:00:00' ? false : true;

        if (!empty($traderRows) && $traderInfo['traderHasFTD']) {



            foreach ($traderRows as $arrAmount) {




                if ($arrAmount['data_sales_type'] == "chargeback" ||
                        $arrAmount['data_sales_type'] == "withdrawal" ||
                        $arrAmount['data_sales_type'] == "deposit" ||
                        $arrAmount['data_sales_type'] == "bonus") {

                    $tranrow['id'] = $arrAmount['id'];
                    $tranrow['affiliate_id'] = $arrAmount['affiliate_id'];
                    $tranrow['tranz_id'] = $arrAmount['tranz_id'];
                    $tranrow['trader_id'] = $arrAmount['trader_id'];
                    $tranrow['merchant_id'] = $arrAmount['merchant_id'];
                    $tranrow['amount'] = $arrAmount['amount'];
                    $tranrow['rdate'] = $arrAmount['rdate'];
                    $tranrow['type'] = $arrAmount['data_sales_type'];
                    $tranrow['status'] = $arrAmount['status'];
                    if (isset($arrRes['initialftddate'])) {
                        $tranrow['initialftddate'] = $arrRes['initialftddate'];
                    }

                    $tranrow['traderHasFTD'] = $traderInfo['traderHasFTD'];
                    $tranrow['FTDqualificationDate'] = $traderInfo['FTDqualificationDate'];


                    if (empty($netDepositTransactions[$tranrow['merchant_id'] . '-' . $tranrow['tranz_id']])) {
                        $netDepositTransactions[$tranrow['merchant_id'] . '-' . $tranrow['tranz_id']] = array($tranrow);
                    }
                }




                if ($arrAmount['data_sales_type'] == 'deposit') {
                    $depositAmount += $arrAmount['amount'];
                    $total_deposits++;

                    if ($set->showMicroPaymentsOnReports == 1 && processMicroPaymentRecord($arrAmount)) {
                        $microPaymentsCount++;
                        $microPaymentsAmount += $arrAmount['amount'];
                    }
                } elseif ($arrAmount['data_sales_type'] == 'bonus') {
                    $bonusAmount += $arrAmount['amount'];
                } elseif ($arrAmount['data_sales_type'] == 'withdrawal') {
                    $withdrawalAmount += $arrAmount['amount'];
                } elseif ($arrAmount['data_sales_type'] == 'chargeback') {
                    $chargebackAmount += $arrAmount['amount'];
                } elseif ($arrAmount['data_sales_type'] == 'volume') {
                    $volumeAmount += $arrAmount['amount'];
                    $totalTraders++;

                    $arrTmp = [
                        'merchant_id' => $arrAmount['merchant_id'],
                        'affiliate_id' => $arrAmount['affiliate_id'],
                        'rdate' => $arrAmount['rdate'],
                        'banner_id' => $arrAmount['banner_id'],
                        'trader_id' => $arrAmount['trader_id'],
                        'FTDqualificationDate' => $arrAmount['FTDqualificationDate'],
                        'profile_id' => $arrAmount['profile_id'],
                        'type' => 'volume',
                        'amount' => $arrAmount['amount'],
                    ];





                    $volumeCom = getCommission(
                            $arrAmount['rdate'], $arrAmount['rdate'], 0, (isset($group_id) && $group_id != '' ? $group_id : -1), $arrDealTypeDefaults, $arrTmp
                    );
                    $totalCom += $volumeCom;
                }
                unset($arrAmount);
            }
        }







        //lots 


        if (strtolower($arrMerchant['producttype']) == 'forex') {


            $totalLots = 0;


            if (!in_array($arrMerchant['id'] . '-' . $arrRes['trader_id'], $tradersProccessedForLots)) {
                $tradersProccessedForLots[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];


                $sql = 'SELECT  * FROM data_stats 
                                         WHERE merchant_id>0 and merchant_id = "' . $arrMerchant['id'] . '" and  trader_id = "' . $arrRes['trader_id'] . '" ';

                $traderStatsQ = function_mysql_query($sql, __FILE__);
                $earliestTimeForLot = date('Y-m-d H:i:s');
                while ($ts = mysql_fetch_assoc($traderStatsQ)) {


                    if ($earliestTimeForLot > $ts['rdate']) {
                        $earliestTimeForLot = $ts['rdate'];
                    }
                    $this_running_lots_amount = $ts['amount'];
                    $totalLots += $this_running_lots_amount;



                    $row = [
                        'merchant_id' => $ts['merchant_id'],
                        'affiliate_id' => $ts['affiliate_id'],
                        'rdate' => $ts['rdate'],
                        'banner_id' => $ts['banner_id'],
                        'trader_id' => $ts['trader_id'],
                        'initialftddate' => $traderInfo['initialftddate'],
                        'FTDqualificationDate' => $traderInfo['FTDqualificationDate'],
                        'profile_id' => $ts['profile_id'],
                        'type' => 'lots',
                        'amount' => $this_running_lots_amount,
                    ];




                    $b = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);



                    $totalCom += $b;
                }
            }
        }

        output_memory('', 'G2', '');


        $totalPNL = 0;



        if ($set->deal_pnl == 1) {


            $dealsForAffiliate = getExistingDealTypesAllMerchantsForAffiliateArray($arrRes['affiliate_id'], $arrDealTypeDefaults);



            if (!in_array($arrMerchant['id'] . '-' . $arrRes['trader_id'], $tradersProccessedForPNL)) {
                $tradersProccessedForPNL[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];






                if (!empty($dealsForAffiliate['pnl'])) {
                    $pnlRes = array();






                    $currentPnlArray = $pnlResultsArray[$arrMerchant['id']][$arrRes['trader_id']];

                    $pnlCom = 0;


                    if (!empty($currentPnlArray)) {
                        foreach ($currentPnlArray as $currentPnlItem) {
                            // $pnlRes[] = $ts;
                            $pnlRes[] = $currentPnlItem;
                        }
                    }
                } else {  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
                    if (empty($PNLPerformanceAgregationArray[$arrMerchant['id']])) {


                        $recordTypes = $isCasino == 1 ? '"static","bets","wins","jackpot", "bonuses","removed_bonuses"' : "'PNL'";


                        $sql = 'SELECT  sum(pnltable.amount) as amount,max(pnltable.rdate) as rdate ,max(pnltable.trader_id) as trader_id ,max(pnltable.merchant_id) as merchant_id,max(pnltable.banner_id) as banner_id,max(pnltable.profile_id) as profile_id,max(pnltable.affiliate_id) as affiliate_id FROM ' . $set->pnlTable . ' pnltable
                                                     inner join data_reg on pnltable.merchant_id = data_reg.merchant_id and pnltable.trader_id  = data_reg.trader_id and data_reg.type<>"demo"
                                                     WHERE pnltable.type in(' . $recordTypes . ') and pnltable.merchant_id>0 and pnltable.amount<>0   '
                                . (!empty($affiliate_id) ? '  and  pnltable.affiliate_id = ' . $affiliate_id : "")
                                . (!empty($group_id) ? '  and  pnltable.group_id = ' . $group_id : "")
                                . (!empty($trader_id) ? '  and  pnltable.trader_id = ' . $trader_id : "")
                                . (!empty($country_id) ? '  and  pnltable.country_id = ' . $country_id : "")
                                . (!empty($banner_id) ? '  and  pnltable.banner_id = ' . $banner_id : "")
                                . (!empty($profile_id) ? '  and  pnltable.profile_id = ' . $profile_id : "")
                                . (!empty($arrMerchant['id']) ? '  and  pnltable.merchant_id = ' . $arrMerchant['id'] : "")
                                // . ' AND pnltable.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql)
                                . " group by pnltable.trader_id;";

                        echo $sql."<br>---------------<br>";
                        // die ($sql);
                        $traderStatsQ = function_mysql_query($sql, __FILE__);
                        while ($ts = mysql_fetch_assoc($traderStatsQ)) {

                            $PNLPerformanceAgregationArray[$ts['merchant_id']][$ts['trader_id']] = $ts;
                        }
                    }
                    $pnlRes[] = $PNLPerformanceAgregationArray[$arrMerchant['id']][$arrRes['trader_id']];
                }


                foreach ($pnlRes as $ts) {

                    $pnlamount = ($ts['amount'] * -1);
                    $row = [
                        'merchant_id' => $ts['merchant_id'],
                        'affiliate_id' => $ts['affiliate_id'],
                        'rdate' => $ts['rdate'],
                        'banner_id' => $ts['banner_id'],
                        'trader_id' => $ts['trader_id'],
                        'profile_id' => $ts['profile_id'],
                        'type' => 'pnl',
                        'amount' => ($isCasino == 1 ? calculateCasinoRevenue($pnlamount, $ts['type']) : $pnlamount),
                        'initialftddate' => $ts['initialftddate']
                    ];


                    $totalPNL = $totalPNL + $pnlamount;



                    if (!empty($dealsForAffiliate['pnl'])) {
                        $tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);


                        $totalCom += $tmpCom;
                    }
                }
                unset($pnlRes);
            }
        }










        if (!empty($netDepositTransactions)) {

            foreach ($netDepositTransactions as $trans) {


                $a = 0;

                $revDepAmount = 0;
                $revBonAmount = 0;
                $revWithAmount = 0;
                $revChBAmount = 0;

                $amount = $trans[0]['amount'];
                $recordDate = $trans[0]['rdate'];

                // echo $recordDate.'<br>';

                if (!empty($amount) && !empty($recordDate)) {



                    if ($trans[0]['type'] == 'deposit') {
                        $revDepAmount = $amount;
                    }
                    if ($trans[0]['type'] == 'bonus') {
                        $revBonAmount = $amount;
                    }
                    if ($trans[0]['type'] == 'withdrawal') {
                        $revWithAmount = $amount;
                    }
                    if ($trans[0]['type'] == 'chargeback') {
                        $revChBAmount = $amount;
                    }

                    $ThisTransactionRecordNetRevenue = round(getRevenue("data_sales.rdate  BETWEEN . '" . $recordDate . "' AND '" . $recordDate . " 23:59:59' ", $arrMerchant['producttype'], $revDepAmount, $revBonAmount, $revWithAmount, 0, 0, 0, $arrMerchant['rev_formula'], null, $revChBAmount), 2);




                    $comrow = array();
                    $comrow['merchant_id'] = $trans[0]['merchant_id'];
                    $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
                    $comrow['banner_id'] = 0;
                    $comrow['initialftddate'] = $trans[0]['initialftddate'];
                    $comrow['rdate'] = $trans[0]['rdate'];
                    $comrow['amount'] = $ThisTransactionRecordNetRevenue;
                    $comrow['trader_id'] = $trans[0]['trader_id'];
                    $comrow['trades'] = $totalTraders;
                    $comrow['isFTD'] = false;
                    $comrow['traderHasFTD'] = $comrow['initialftddate'] == '0000-00-00 00:00:00' ? false : true;

                    if (isset($trans[0]['initialftddate'])) {
                        $comrow['initialftddate'] = $trans[0]['initialftddate'];
                    }

                    $com = getCommission($recordDate, $recordDate, 1, (isset($group_id) && $group_id != '' ? $group_id : -1), $arrDealTypeDefaults, $comrow, true);




                    $totalCom += $com;





                    $netRevenue += $ThisTransactionRecordNetRevenue;
                }
            }
            unset($netDepositTransactions);
        } else {
            $netRevenue = round(getRevenue(" data_sales.rdate BETWEEN '" . $from . "' AND '" . $to . "' ", $arrMerchant['producttype'], $depositAmount, $bonusAmount, $withdrawalAmount, 0, 0, 0, $arrMerchant['rev_formula'], null, $chargebackAmount), 2);





            $row = [];
            $row['merchant_id'] = $arrRes['merchant_id'];
            $row['affiliate_id'] = $arrRes['affiliate_id'];
            $row['banner_id'] = $arrRes['banner_id'];
            $row['rdate'] = $earliestTimeForNetRev;
            $row['initialftddate'] = $arrRes['initialftddate'];
            $row['amount'] = $netRevenue;
            $row['trader_id'] = $arrRes['trader_id'];
            $row['isFTD'] = false;
            $row['traderHasFTD'] = $row['initialftddate'] == '0000-00-00 00:00:00' ? false : true;



            $externalCom = getCommission($earliestTimeForNetRev, $to, 1, (isset($group_id) && $group_id != '' ? $group_id : -1), $arrDealTypeDefaults, $row);


            $totalCom += $externalCom;
        }




        // AFFILIATE info retrieval.


        $affInfo = getAffiliateRow($arrRes['affiliate_id'], 1);





        if ($arrRes['type'] == 'real') {
            $color = 'green';
        } elseif ($arrRes['type'] == 'demo') {
            $color = 'red';
        } elseif ($arrRes['type'] == 'lead') {
            $color = 'black';
        }





        if (hasValidDate($arrRes['FTDqualificationDate'])) {

            $traderInfo['rdate'] = $traderInfo['FTDqualificationDate'];
            $traderInfo['amount'] = $traderInfo['ftdamount'];
            $FtdCount = $traderInfo['amount'];
            $traderInfo['isFTD'] = true;
            $traderInfo['trades'] = $totalTraders;
            $traderInfo['traderHasFTD'] = $traderInfo['initialftddate'] == '0000-00-00 00:00:00' ? false : true;
            $ftdComFromLoop = getCommission($traderInfo['rdate'], $traderInfo['rdate'], 0, $traderInfo['group_id'], $arrDealTypeDefaults, $traderInfo);
            $totalCom += $ftdComFromLoop;
        }


        $traderInfo['rdate'] = $arrRes['rdate'];
        $traderInfo['initialftddate'] = $arrRes['initialftddate'];
        $traderInfo['FTDqualificationDate'] = $arrRes['FTDqualificationDate'];



        $reason = "";


        

        if (empty($notesArray)) {
            $notesArray = array();
            
            $sql = "select tt.trader_id, tt.notes,pd.status as pd_status,tt.status,pd.reason,tt.merchant_id from traders_tag tt left join  payments_details pd on tt.merchant_id = pd.merchant_id and tt.trader_id = pd.trader_id where tt.merchant_id = " . $arrRes['merchant_id'] . " ; ";

            $results = mysql_query($sql);

            while (($row = mysql_fetch_assoc($results))) {
                $notesArray[$row['merchant_id']][$row['trader_id']] = $row;
            }
        }
        $chkTrader = $notesArray[$arrRes['merchant_id']][$arrRes['trader_id']];


        if (!empty($chkTrader)) {

            $reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
        }

        if (empty($chkTrader)) {

            $reason = $chkTrader['reason'];
        }


        $ClickFrom = changeDate($traderInfo['rdate'], -4);
        $ClickTo = changeDate($traderInfo['rdate'], +4);

        $listReport .= '
                        <tr>
                            <td>' . $arrRes['trader_id'] . '</td>';
        if ($set->showCampaignOnTraderReport) {
            $listReport .= '<td>' . $arrRes['campaign_id'] . '</td>';
        }

        if ($displayForex == 1) {
            $listReport .= ($arrRes['sub_trader_count'] > 0 ? '<td><a href="/' . $userlevel . '/reports.php?act=subtraders&from=' . date("Y/m/d", strtotime($from)) . '&to=' . date("Y/m/d", strtotime($to)) . '&merchant_id=' . $loopedMerchant_ID . '&trader_id=' . $arrRes['trader_id'] . '">' . $arrRes['sub_trader_count'] . '</a></td>' : '<td/>');
        }

        $hidePendingReason = hasValidDate($arrRes['FTDqualificationDate']) || ($affInfo['qualify_type'] == '' || ($affInfo['qualify_type'] == 'default' && $merchantsAr['qualify_type'] == '' ));


        $foundcountry = $acountries[$arrRes['country']];
        $ftdAmount = $arrRes['ftdamount'];


        if ($set->showDynamicFilters == 1) {
            listDynamicFilters(0, 0);
            $dynamicName = empty($dynamicFilters[$arrRes['dynamic_filter']]['caption']) ? $dynamicFilters[$arrRes['dynamic_filter']]['name'] : $dynamicFilters[$arrRes['dynamic_filter']]['caption'];
        }

        $TotalNextDeposits = ($depositAmount > 0 && $ftdAmount > 0 && $depositAmount > $ftdAmount ? price($depositAmount - $ftdAmount) : "" );
        $NextDeposits = ($total_deposits > 1 ? $total_deposits - 1 : "" );
        $FTDqualificationDate = (hasValidDate($arrRes['FTDqualificationDate']) ? $arrRes['FTDqualificationDate'] : "" );
        $lastTimeActive = ($arrRes['lastTimeActive'] == '1969-12-31 23:00:00' || $arrRes['lastTimeActive'] == '0000-00-00 00:00:00' ? '-' : $arrRes['lastTimeActive']);
        $AdminNotes = (!empty($reason) ? $reason : ($hidePendingReason ? '' : lang('Pending'))) . (!empty($traderInfo['traderIssue']) ? '<br><span style="color:red;">' . strtoupper($traderInfo['traderIssue']) . '</span>' : "");
        

        $listReport .= '<td>' . $arrRes['trader_alias'] . '</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>' . $arrRes['email'] . '</td>' : '' ) . '
                            ' . ($set->ShowPhonesOnTraderReportForAdmin ? '<td>' . $arrRes['phone'] . '</td>' : '' ) . '
                            <td title="' . (date("d/m/Y H:i:s", strtotime($traderInfo['rdate']))) . '">' . (date("d/m/Y H:i:s", strtotime($traderInfo['rdate']))) . '</td>
                            <td><span style="color: ' . $color . ';">' . $arrRes['type'] . '</span></td>
                            <td>' . $foundcountry . '</td>
                            
                            ' . ($set->showDynamicFilters == 1 ?
                '<td>' . $dynamicName . '</td>' : '' ) . '
                            
                            <td>' . $arrRes['affiliate_id'] . '</td>
                            <td><a href="/' . $userlevel . '/affiliates.php?act=new&id=' . $arrRes['affiliate_id'] . '" target="_blank">' . $affInfo['username'] . '</a></td>
                            ' . ($set->showProductsPlace == 1 ? '
                            <td>' . ($arrRes['product_id']) . '</td>
                            <td>' . ucwords($productsArray[$arrRes['product_id']]['title']) . '</td>' : '' )
                . '
                            <td>' . $arrMerchant['id'] . '</td>
                            <td>' . strtoupper($arrMerchant['name']) . '</td>
                            <td style="text-align: left;">' . $bannerInfo['id'] . '</td>
                            <td style="text-align: left;">' . $bannerInfo['title'] . '</td>
                            <td>' . $bannerInfo['type'] . '</td>
                            <td>' . $bannerInfo['language_name'] . '</td>
                            <td>' . $arrRes['profile_id'] . '</td>
                            <td>' . $listProfiles[$arrRes['profile_id']] . '</td>
                            ' . ($isCasino == 1 ? ( empty($arrRes['status']) ? "<td>" . lang('real') . "</td>" : "<td>" . $arrRes['status'] . '</td>') : '') . '
                            <td>' . $arrRes['freeParam'] . '</td>
                            <td>' . $arrRes['freeParam2'] . '</td>
                            <td>' . $arrRes['freeParam3'] . '</td>
                            <td>' . $arrRes['freeParam4'] . '</td>
                            <td>' . $arrRes['freeParam5'] . '</td>
                            <td>' . (isset($arrRes['tranz_id']) ? $arrRes['tranz_id'] : '') . '</td>
                            <td title="' . (empty($arrRes['tranz_id']) ? "" : date("d/m/Y H:i:s", strtotime($arrRes['initialftddate'])) ) . '">' . (!empty($arrRes['tranz_id']) ? date("d/m/Y H:i:s", strtotime($arrRes['initialftddate'])) : "") . '</td>
                            
                            <td>' . (!empty($arrRes['tranz_id']) ? price($ftdAmount) : "" ) . '</td>
                            
                            <td>' . ($arrRes['isSelfDeposit'] == 1 ? lang('Yes') : "" ) . '</td>
                            ' . ($set->ShowNextDepositsColumn == 1 ? '
                            
                            <td>' . $TotalNextDeposits . '</td>
                            <td>' . $NextDeposits . '</td>
                            ' : '' ) . '

                            ' . ($set->showMicroPaymentsOnReports == 1 ? '
                            
                            <td>' . ($microPaymentsCount ) . '</td>
                            <td>' . ($microPaymentsAmount ) . '</td>
                            ' : '' ) . '
                            
                            
                            
                            
                            
                            <td><a href="/' . $userlevel . '/reports.php?act=transactions&from=' . date("d/m/Y", strtotime("-3 Years")) . '&to=' . date("d/m/Y") . '&merchant_id=' . $loopedMerchant_ID . '&trader_id=' . $arrRes['trader_id'] . '&type=deposit">' . $total_deposits . '</a></td>
                            <td>' . price($depositAmount) . '</td>
                            <td>' . price($volumeAmount) . '</td>
                            <td>' . price($bonusAmount) . '</td>
                            <td>' . price($withdrawalAmount) . '</td>
                            <td>' . price($chargebackAmount) . '</td>
                            <td>' . price($netRevenue) . '</td>
                            <td>' . $totalTraders . '</td>
                            ' . ( $displayForex == 1 ?
                '<td>' . ($totalLots) . '</td>' : '' ) . '
                            <td>' . $FTDqualificationDate . '</td>
                            ' . ($set->deal_pnl == 1 ?
                '<td id="pnl">' . price($totalPNL) . '</td>' : '') . '
                            <td>' . $arrRes['saleStatus'] . '</td>
                            ' . ($set->displayLastMessageFieldsOnReports == 1 ? '
                            <td>' . $arrRes['lastSaleNoteDate'] . '</td>
                            <td>' . $arrRes['lastSaleNote'] . '</td>' : '') . '
                            <td>' . $lastTimeActive . '</td>
                            <td>' . price($totalCom) . '</td>
                            <td>' . $AdminNotes . '</td>
                            ' . ((!empty($arrRes['uid']) && $arrRes['uid'] != '') ? '<td><a title="deTails" href="/' . $userlevel . '/reports.php?act=clicks&from=' . $ClickFrom . '&to=' . $ClickTo . '&merchant_id=' . $loopedMerchant_ID . '&unique_id=' . $arrRes['uid'] . '">' . lang('View') . '</a></td>' : '<td></td>') . '
                        </tr>';




if(!empty($arrRes['uid']) AND empty($arrRes['ip']) AND empty($arrRes['device_name']) AND empty($arrRes['os_ver']) AND empty($arrRes['os_name']) AND empty($arrRes['browser_ver']) AND empty($arrRes['browser_name'])){
    
    $trader_ua = false;
    
    $sql_ua = "SELECT ip, userAgent FROM traffic WHERE uid='" . $arrRes['uid'] . "' AND affiliate_id = '" . $arrRes['affiliate_id'] . "' AND merchant_id = '". $arrMerchant['id'] ."'  limit 1";
    $result_ua = function_mysql_query($sql_ua, __FILE__, __FUNCTION__);
    $trader_ua = mysql_fetch_assoc($result_ua);
    $arrRes['ip'] = $trader_ua['ip'];
    if($trader_ua['userAgent']){
        $result_parser_ua = $parser_ua->parse($trader_ua['userAgent']);

        $arrRes['device_name'] = $result_parser_ua->device->toString();
        $arrRes['os_ver'] = $result_parser_ua->os->toString();
        $arrRes['os_name'] = $result_parser_ua->os->family;
        $arrRes['browser_ver'] = $result_parser_ua->ua->toString();
        $arrRes['browser_name'] = $result_parser_ua->ua->family;


        $sql_ua_update_trader = "UPDATE data_reg SET ip='". $arrRes['ip'] ."',device_name='". $arrRes['device_name'] ."',os_ver='". $arrRes['os_ver'] ."',os_name='". $arrRes['os_name'] ."',browser_ver='". $arrRes['browser_ver'] ."',browser_name='". $arrRes['browser_name'] ."' WHERE  id=".$arrRes['id'];
        function_mysql_query($sql_ua_update_trader,__FILE__,__FUNCTION__);
    }else{
        $sql_ua_update_trader = "UPDATE data_reg SET ip='". $arrRes['ip'] ."' WHERE  id=".$arrRes['id'];
        function_mysql_query($sql_ua_update_trader,__FILE__,__FUNCTION__);        
    }
}
        
// ADD TO DB

$replace_query = "REPLACE INTO `ReportTraders` 
(`Date`, `TraderID`, `CampaignID`, `TraderAlias`, `Email`, `RegistrationDate`, `TraderStatus`, 
`Country`, `AffiliateID`, `AffiliateUsername`, `MerchantID`, `MerchantName`, `CreativeID`, `CreativeName`, 
`Type`, `CreativeLanguage`, `ProfileID`, `ProfileName`, `Param`, `Param2`, `Param3`, `Param4`, `Param5`, 
`TransactionID`, `FirstDeposit`, `FTDAmount`, `SelfDeposit`, `TotalNextDeposits`, `NextDeposits`, 
`TotalMicroPayments`, `MicroPaymentsAmount`, `TotalDeposits`, `DepositAmount`, `Volume`, `BonusAmount`, 
`WithdrawalAmount`, `ChargeBackAmount`, `NetDeposit`, `Trades`, `QualificationDate`, `PNL`, `SaleStatus`, 
`LastTimeActive`, `Commission`, `AdminNotes`, `ClickDetails`) 
VALUES 
('".$from."', '".$arrRes['trader_id']."', '".$arrRes['campaign_id']."', '".$arrRes['trader_alias']."', '".$arrRes['email']."', '".(date("Y-m-d H:i:s", strtotime($traderInfo['rdate'])))."', '".$arrRes['type']."', 
'".$arrRes['country']."', '".$arrRes['affiliate_id']."', '".$affInfo['username']."', '".$arrMerchant['id']."', '".$arrMerchant['name']."', '".$bannerInfo['id']."', '".$bannerInfo['title']."', 
'".$bannerInfo['type']."', '".$bannerInfo['language_name']."', '".$arrRes['profile_id']."', '".$listProfiles[$arrRes['profile_id']]."', '".$arrRes['freeParam']."', '".$arrRes['freeParam2']."', '".$arrRes['freeParam3']."', '".$arrRes['freeParam4']."', '".$arrRes['freeParam5']."', 
'".$arrRes['tranz_id']."', '".$arrRes['initialftddate']."', '".$ftdAmount."', '".$arrRes['isSelfDeposit']."', '".$TotalNextDeposits."', '".$NextDeposits."', 
'".$microPaymentsCount."', '".$microPaymentsAmount."', '".$total_deposits."', '".$depositAmount."', '".$volumeAmount."', '".$bonusAmount."', 
'".$withdrawalAmount."', '".$chargebackAmount."', '".$netRevenue."', '".$totalTraders."', '".$FTDqualificationDate."', '".$totalPNL."', '".$arrRes['saleStatus']."', 
'".$lastTimeActive."', '".$totalCom."', '".htmlspecialchars($AdminNotes)."', '".$arrRes['uid']."');";


function_mysql_query($replace_query,__FILE__,__FUNCTION__);






        $arrTradersPerMerchants[] = $arrRes['merchant_id'] . '-' . $arrRes['trader_id']; //$arrRes['trader_id'];
        $totalTotalCom += $totalCom;
        $totalFTD += $ftdAmount;
        $totalNetRevenue += $netRevenue;




        $totalDepositAmount += $depositAmount;
        $totalVolumeAmount += $volumeAmount;
        $totalBonusAmount += $bonusAmount;
        $totalTotalDeposit += $total_deposits;
        $totalmicroPaymentsCount += $microPaymentsCount;
        $totalmicroPaymentsAmount += $microPaymentsAmount;
        $totalTrades += $totalTraders;
        $totalLotsamount += $totalLots;
        $totalPNLamount += $totalPNL;
        $totalWithdrawalAmount += $withdrawalAmount;
        $totalChargeBackAmount += $chargebackAmount;
        $ftdExist[] = $firstDeposit['trader_id'];


        $l++;

        $volumeAmount = $netRevenue = $chargebackAmount = $withdrawalAmount = $depositAmount = $bonusAmount = $total_deposits = $microPaymentsCount = $microPaymentsAmount = $totalTraders = $totalLots = 0;
    }
}
if ($l > 0) {
    $set->sortTableScript = 1;
}

$set->sortTable = 1;
$set->totalRows = $l;


/*
$tableStr = '<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="traderTbl">
                    <thead><tr   class="table-row">
                        <th  class="table-cell">' . lang(ptitle('Trader ID')) . '</th>'
        . ($set->showCampaignOnTraderReport ? '<th  class="table-cell">' . lang('Campaign Id') . '</th>' : '')
        . ( $displayForex == 1 ? '<th  class="table-cell">' . lang(ptitle('Sub Traders')) . '</th>' : '' ) . '
                        <th  class="table-cell">' . lang(ptitle('Trader Alias')) . '</th>
                        ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th  class="table-cell">' . lang(ptitle('Email')) . '</th>' : '' ) . '
                        ' . ($set->ShowPhonesOnTraderReportForAdmin ? '<th  class="table-cell">' . lang(ptitle('Phone')) . '</th>' : '' ) . '
                        <th  class="table-cell">' . lang('Registration Date') . '</th>
                        <th  class="table-cell">' . lang(ptitle('Trader Status')) . '</th>
                        <th  class="table-cell">' . lang('Country') . '</th>
                        ' . ($set->showDynamicFilters == 1 ?
        '<th  class="table-cell">' . lang($set->dynamicFilterTitle) . '</th>' : '') . '
                        
                        <th  class="table-cell">' . lang('Affiliate ID') . '</th>
                        <th  class="table-cell">' . lang('Affiliate Username') . '</th>
                        ' . ($set->showProductsPlace == 1 ? '
                        <th  class="table-cell">' . lang('Product ID') . '</th>
                        <th  class="table-cell">' . lang('Product Name') . '</th>' : '' ) . '
                        <th  class="table-cell">' . lang('Merchant ID') . '</th>
                        <th  class="table-cell">' . lang('Merchant Name') . '</th>
                        <th   class="table-cell"style="text-align: left;">' . lang('Creative ID') . '</th>
                        <th   class="table-cell"style="text-align: left;">' . lang('Creative Name') . '</th>
                        <th  class="table-cell">' . lang('Type') . '</th>
                        <th  class="table-cell">' . lang('Creative Language') . '</th>
                        <th  class="table-cell">' . lang('Profile ID') . '</th>
                        <th  class="table-cell">' . lang('Profile Name') . '</th>
                        ' . ($isCasino == 1 ? '<th  class="table-cell">' . lang('Status') . '</th>' : '' ) . '
                        <th  class="table-cell">' . lang('Param') . '</th>
                        <th  class="table-cell">' . lang('Param2') . '</th>
                        <th  class="table-cell">' . lang('Param3') . '</th>
                        <th  class="table-cell">' . lang('Param4') . '</th>
                        <th  class="table-cell">' . lang('Param5') . '</th>
                        <th  class="table-cell">' . lang('Transaction ID') . '</th>
                        <th  class="table-cell">' . ($type == "deposit" ? lang('Deposit Date') : lang('First Deposit')) . '</th>
                        <th  class="table-cell">' . lang('FTD Amount') . '</th>
                        <th  class="table-cell">' . lang('Self Deposit') . '</th>
                        ' .
        ($set->ShowNextDepositsColumn == 1 ? '
                        <th  class="table-cell">' . lang('Total Next Deposits') . '</th>
                        <th  class="table-cell">' . lang('Next Deposits') . '</th>' : '' ) . ' ' .
        ($set->showMicroPaymentsOnReports == 1 ? '
                        <th  class="table-cell">' . lang('Total MicroPayments') . '</th>
                        <th  class="table-cell">' . lang('MicroPayments Amount') . '</th>' : '' ) . ' 
                        <th  class="table-cell">' . lang('Total Deposits') . '</th>
                        <th  class="table-cell">' . ($type == "deposit" ? lang('Deposit Amount') : lang('Deposit Amount')) . '</th>
                        <th  class="table-cell">' . lang('Volume') . '</th>
                        <th  class="table-cell">' . lang('Bonus Amount') . '</th>
                        <th  class="table-cell">' . lang('Withdrawal Amount') . '</th>
                        <th  class="table-cell">' . lang('ChargeBack Amount') . '</th>
                        <th  class="table-cell">' . lang(ptitle('Net Deposit')) . '</th>
                        <th  class="table-cell">' . lang(ptitle('Trades')) . '</th> '
        . ($displayForex == 1 ?
        '<th  class="table-cell">' . lang(ptitle('Lifetime Lots')) . '</th>' : '' ) . '
                        <th  class="table-cell">' . lang('Qualification Date') . '</th>
                        ' . ($set->deal_pnl == 1 ?
        '<th  class="table-cell">' . lang(ptitle('PNL')) . '</th>' : '' ) . '
                        <th  class="table-cell">' . lang('Sale Status') . '</th>
                        ' . ($set->displayLastMessageFieldsOnReports == 1 ? '
                        <th  class="table-cell">' . lang('Last Sale Note Date') . '</th>
                        <th  class="table-cell">' . lang('Last Sale Note') . '</th>' : '' ) . '
                        <th  class="table-cell">' . lang('Last Time Active') . '</th>
                        <th  class="table-cell">' . lang('Commission') . '</th>
                        <th  class="table-cell">' . lang('Admin Notes') . '</th>
                        <th  class="table-cell">' . lang('Click Details') . '</th>
                    </tr></thead>
                    <tfoot>
                        <th></th>'
        . ($set->showCampaignOnTraderReport ?
        '<th></th>' : '')
        . ( $displayForex == 1 ? '
                        <th></th>' : '') . '
                        <th></th>
                        ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th></th>' : '' ) . '
                        ' . ($set->ShowPhonesOnTraderReportForAdmin ? '<th></th>' : '' ) . '
                        <th></th>
                            ' . ($set->showDynamicFilters == 1 ?
        '<th></th>' : '') . '
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        ' . ($set->showProductsPlace == 1 ? '
                        <th></th>
                        <th></th>' : '' ) . '
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        ' . ($isCasino == 1 ? '<th></th>' : '' ) . '
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        
                        <th style="text-align: left;">' . price($totalFTD) . '</th>
                        <th></th>
                        ' . ($set->ShowNextDepositsColumn == 1 ? '
                        <th></th>
                        <th></th>
                        ' : '' ) . '
                        ' . ($set->showMicroPaymentsOnReports == 1 ? '
                        <th></th>
                        <th></th>
                        ' : '' ) . '
                        <th style="text-align: left;">' . $totalTotalDeposit . '</th>
                        <th style="text-align: left;">' . price($totalDepositAmount) . '</th>
                        <th style="text-align: left;">' . price($totalVolumeAmount) . '</th>
                        <th style="text-align: left;">' . price($totalBonusAmount) . '</th>
                        <th style="text-align: left;">' . price($totalWithdrawalAmount) . '</th>
                        <th style="text-align: left;">' . price($totalChargeBackAmount) . '</th>
                        <th style="text-align: left;">' . price($totalNetRevenue) . '</th>
                        <th style="text-align: left;">' . $totalTrades . '</th>
                        ' . ($displayForex == 1 ?
        '<th style="text-align: left;">' . $totalLotsamount . '</th>' : '' ) .
        '
                        <th></th>
                        ' . ($set->deal_pnl == 1 ?
        '
                        
                        <th style="text-align: left;">' . price($totalPNLamount) . '</th>
                        ' : '' ) . '
                        <th></th>
                        ' . ($set->displayLastMessageFieldsOnReports == 1 ? '
                        <th></th>
                        <th></th>' : '' ) . '
                        <th></th>
                        <th style="text-align: left;">' . price($totalTotalCom) . '</th>
                        <th></th>
                        <th></th>
                    </tfoot>
                    <tbody>
                    ' . $listReport . '
                </table>
                ';
*/

//echo $tableStr;





