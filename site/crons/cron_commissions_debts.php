<?php

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 900);

require(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');

// parse params
$fromDate = (strtotime($_GET['fromDate'])) ? date('Y-m-d', strtotime($_GET['fromDate'])) : date('Y-m-01');
$toDate = (strtotime($_GET['toDate'])) ? date('Y-m-d', strtotime($_GET['toDate'])) : date('Y-m-d');

if(!empty($_GET['last_month'])){
    $fromDate = date('Y-m-01', strtotime('-1 month'));
    $toDate = date('Y-m-t', strtotime('-1 month'));
}

// calculate data for next years
$processYears = prepareDates($fromDate, $toDate);

// run main process
main($processYears);


// functions

// main process function
function main($data)
{
    foreach ($data as $year => $months) {
        foreach ($months as $month) {
            calcMonth($year, $month);
        }
    }
}

// prepare dates interval to process
function prepareDates($fromDate, $toDate)
{
    $fromDateInfo = date_parse($fromDate);
    $toDateInfo = date_parse($toDate);

    $processYears = array();
    for ($y = $fromDateInfo['year']; $y <= $toDateInfo['year']; $y++) {
        $monthArray = array();
        if ($y == $fromDateInfo['year']) {
            for ($m = $fromDateInfo['month']; $m <= 12; $m++) {
                $monthArray[] = $m;
            }
        } elseif ($y == $toDateInfo['year']) {
            for ($m = 1; $m <= $toDateInfo['month']; $m++) {
                $monthArray[] = $m;
            }
        } else {
            for ($m = 1; $m <= 12; $m++) {
                $monthArray[] = $m;
            }
        }
        $processYears[$y] = $monthArray;
    }

    return $processYears;
}

// calculate debts for all affiliates for year and month
function calcMonth($year, $month)
{
    /*
    $query = "SELECT tc.affiliateID, tc.merchantID, SUM(tc.Commission) as total_commission FROM commissions tc 
              WHERE tc.Date BETWEEN '".date("$year-$month-01 00:00:00")."' AND '".date("$year-$month-t 23:59:59")."' 
              GROUP BY tc.affiliateID, tc.merchantID 
              ORDER BY tc.affiliateID, tc.merchantID ASC;";
    */
    
    $query = "SELECT c.affiliateID, c.merchantID, SUM(c.Commission) as total_commission FROM commissions c  INNER JOIN affiliates aff ON c.affiliateID = aff.id LEFT JOIN traders_tag as tg ON tg.trader_id = c.traderID WHERE  tg.trader_id IS NULL AND  Date  BETWEEN '".date("$year-$month-01 00:00:00")."' AND '".date("$year-$month-t 23:59:59")."' GROUP BY c.affiliateID, c.merchantID ORDER BY c.affiliateID, c.merchantID ASC";
    

    //die($query); // debug

    $result = function_mysql_query($query);

    while ($item = mysql_fetch_assoc($result)) {
        
        //die(var_dump($item)); // debug

        $commissions = isset($item['total_commission']) ? $item['total_commission'] : 0;
        if (isset($item['affiliateID']) && isset($item['merchantID'])) {
            $affiliate_id = $item['affiliateID'];
            $merchant_id = $item['merchantID'];
            $prevCommissions = calcAffiliatePrevMonth($affiliate_id, $merchant_id, $year, $month);
            if ((int)$prevCommissions < 0) {
                $commissions += $prevCommissions;
            }
            $replaceQuery = "REPLACE INTO commissions_debts VALUES ($affiliate_id, $merchant_id, $year, $month, $commissions);";

            //die($replaceQuery); // debug

            function_mysql_query($replaceQuery);

        }/* else {
            die('Error. Missing affiliateID or merchantID in record.');
        }*/
    }
    unset($result);
}

// return commissions for one affiliate in prev month for year-month
function calcAffiliatePrevMonth($affiliate_id, $merchant_id, $year, $month)
{
    if ($month == 1) {
        $month = 12;
        $year = $year - 1;
    } else {
        $month = $month - 1;
    }
    $query = "SELECT tcd.* FROM commissions_debts tcd 
              WHERE tcd.year = $year AND tcd.month = $month AND tcd.affiliate_id = $affiliate_id AND tcd.merchant_id = $merchant_id 
              LIMIT 1;";

    //die($query); // debug

    $result = function_mysql_query($query);
    $result = mysql_fetch_assoc($result);
    if (isset($result) && isset($result['commissions']) && !empty($result['commissions'])) {
        return $result['commissions'];
    }

    return 0;
}

?>
