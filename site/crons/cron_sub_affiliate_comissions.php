<?php

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 900);

require(__DIR__ . '/../common/database.php');
require(__DIR__ . '/../func/func_debug.php');

/**
 * Start Calculation Date
 */
$commissionStartCalculationDate = '2019-04-11';


/**
 * Commissions Depth
 */
$commissionsDepth = $set->sub_com_level;

// Level now can be not set (using value by default from settings)
/*if (empty($commissionsDepth)) {
    die('Sub Affiliate Commissions Depth is 0!');
}*/

/**
 * Affiliates Array
 */
$affiliates_array = [];
$affiliatesRes = function_mysql_query('SELECT * FROM affiliates WHERE valid = 1', __FILE__);
while ($item = mysql_fetch_assoc($affiliatesRes)) {
    $affiliates_array[$item['id']] = $item;
}
unset($affiliatesRes);

/**
 * Sub Affiliates Commissions by Levels
 */
//$merchant_sub_aff_levels = function_mysql_query("SELECT lv1.*  FROM merchants_affiliate_level lv1 INNER JOIN ( SELECT max(rdate) MaxDate, level FROM merchants_affiliate_level WHERE affiliate_id = 0 GROUP BY merchant_id, level ) lv2 ON lv1.level = lv2.level AND lv1.rdate = lv2.MaxDate WHERE lv1.affiliate_id = 0 order by lv1.rdate desc", __FILE__);
//$merchant_sub_aff_levels_array = [];
//while ($row_level = mysql_fetch_assoc($merchant_sub_aff_levels)) {
//    $merchant_sub_aff_levels_array[$row_level['merchant_id']][$row_level['level']] = $row_level['amount'];
//}

/*$merchant_sub_aff_levels = function_mysql_query("SELECT * FROM merchants_affiliate_level WHERE affiliate_id = 0", __FILE__);
$merchant_sub_aff_levels_array = [];
while ($row_level = mysql_fetch_assoc($merchant_sub_aff_levels)) {
    $merchant_sub_aff_levels_array[$row_level['merchant_id']][$row_level['level']][strtotime($row_level['rdate'])] = $row_level['amount'];
}*/


$merchant_sub_aff_levels = function_mysql_query("SELECT * FROM merchants_affiliate_level", __FILE__);
$merchant_sub_aff_levels_array = [];
while ($row_level = mysql_fetch_assoc($merchant_sub_aff_levels)) {
    $merchant_sub_aff_levels_array[$row_level['merchant_id']][$row_level['affiliate_id']][$row_level['level']][strtotime($row_level['rdate'])] = $row_level['amount'];
}

/**
 * Calculate commision by level or default value for affiliate or system
 * @param $date
 * @param int $merchant_id
 * @param int $affiliate_id
 * @param int $level
 * @param int $commissions_levels
 * @param $affiliates_array
 * @param $defaultCommission
 * @return int
 */
function getCommissionsLevelByDate($date, $merchant_id, $affiliate_id, $level, $commissions_levels, $affiliates_array = [], $defaultCommission)
{
    $current_date = '';
    if (!empty($commissions_levels[0][$affiliate_id][$level])) {
        foreach ($commissions_levels[0][$affiliate_id][$level] as $level_date => $level_amount) {
            if (empty($current_date)) {
                $current_date = $level_date;
            }

            if ($current_date <= $level_date) {
                $current_date = $level_date;
            }
        }

        if (!empty($commissions_levels[0][$affiliate_id][$level][$current_date]) && $commissions_levels[0][$affiliate_id][$level][$current_date] > 0) {
            return $commissions_levels[0][$affiliate_id][$level][$current_date];
        }
    }

    if ($affiliates_array[$affiliate_id]['sub_com'] > 0) {
        return $affiliates_array[$affiliate_id]['sub_com'];
    }

    if (!empty($commissions_levels[$merchant_id][0][$level])) {
        foreach ($commissions_levels[$merchant_id][0][$level] as $level_date => $level_amount) {
            if (empty($current_date)) {
                $current_date = $level_date;
            }

            if ($current_date <= $level_date) {
                $current_date = $level_date;
            }
        }

        if (!empty($commissions_levels[$merchant_id][0][$level][$current_date])) {
            return $commissions_levels[$merchant_id][0][$level][$current_date];
        }
    }
    return $defaultCommission;

}

/**
 * Commissions
 */
$comissionRes = function_mysql_query('SELECT * FROM commissions WHERE Type != "AFF" AND Commission > 0 AND (status = 0 OR (status = 1 AND updated <= "' . date('Y-m-d H:i:s', strtotime('-2 hours')) . '") ) AND Date >= "'.$commissionStartCalculationDate.'"', __FILE__);
while ($item = mysql_fetch_assoc($comissionRes)) {
    if ($item['status'] == 1) {
        // Delete all sub commission for this item
        $sql_del_comms = "DELETE FROM `commissions` WHERE `commissions`.`merchantID` = " . $item['merchantID'] . " AND `commissions`.`traderID` = '" . $item['traderID'] . "' AND `commissions`.`transactionID` = '" . $item['transactionID'] . "' AND `commissions`.`Date` = '" . $item['Date'] . "' AND `commissions`.`Type` = 'AFF'";
        function_mysql_query($sql_del_comms, __FILE__);
    }

    // If commission item no refer_id - continue
    if (empty($affiliates_array[$item['affiliateID']]['refer_id'])) {
        function_mysql_query("UPDATE `commissions` SET `status` = '2' WHERE `commissions`.`merchantID` = " . $item['merchantID'] . " AND `commissions`.`affiliateID` = " . $item['affiliateID'] . " AND `commissions`.`traderID` = '" . $item['traderID'] . "' AND `commissions`.`transactionID` = '" . $item['transactionID'] . "' AND `commissions`.`Type` = '" . $item['Type'] . "' AND `commissions`.`Date` = '" . $item['Date'] . "' AND CONCAT(`commissions`.`Amount`) = '" . $item['Amount'] . "' LIMIT 1", __FILE__);
        continue;
    }

    function_mysql_query("UPDATE `commissions` SET `updated` = '" . date('Y-m-d H:i:s') . "',`status` = '1' WHERE `commissions`.`merchantID` = " . $item['merchantID'] . " AND `commissions`.`affiliateID` = " . $item['affiliateID'] . " AND `commissions`.`traderID` = '" . $item['traderID'] . "' AND `commissions`.`transactionID` = '" . $item['transactionID'] . "' AND `commissions`.`Type` = '" . $item['Type'] . "' AND `commissions`.`Date` = '" . $item['Date'] . "' AND CONCAT(`commissions`.`Amount`) = '" . $item['Amount'] . "' LIMIT 1", __FILE__);



    // Add commission each refferers
    $refer_id = $affiliates_array[$item['affiliateID']]['refer_id'];

    $new_sub_commissions_array = [];
    for ($sub_com_level = 1; $sub_com_level <= $commissionsDepth; $sub_com_level++) {

        $sub_com_level_amount = getCommissionsLevelByDate($item['Date'], $item['merchantID'], $affiliates_array[$item['affiliateID']]['refer_id'], $sub_com_level, $merchant_sub_aff_levels_array, $affiliates_array, $set->sub_com);

        if (empty($sub_com_level_amount)) {
            break;
        }

        $sub_aff_com_amount = ($item['Commission'] * ($sub_com_level_amount / 100));

        /*
          echo "<br><br>LEVEL " . $sub_com_level . "<br>";
          echo "refer_id: " . $refer_id."<br>";
          echo "Comm amount: " . $item['Commission']."<br>";
          echo "Comm %: " . $sub_com_level_amount."<br>";
          echo "Comm AFF Amount: " . $sub_aff_com_amount."<br>";
         */

        if ($sub_aff_com_amount > 0) {
            // Add commissions
            $new_sub_commissions_array[] = [
                'merchantID' => $item['merchantID'],
                'affiliateID' => $refer_id,
                'traderID' => $item['traderID'],
                'transactionID' => $item['transactionID'],
                'Date' => $item['Date'],
                'Type' => 'AFF',
                'Amount' => $item['Commission'],
                'DealType' => 'Sub AFF',
                'Commission' => $sub_aff_com_amount,
                'DealTypeCondition' => $item['DealTypeCondition'],
                'level' => $sub_com_level,
                'subAffiliateID' => $item['affiliateID'],
                'status' => '',
                'updated' => ''
            ];
        }


        if (empty($affiliates_array[$refer_id]['refer_id'])) {
            break;
        } else {
            $refer_id = $affiliates_array[$refer_id]['refer_id'];
        }
    }

    // Insert commissions
    if (!empty($new_sub_commissions_array)) {

        $sql_add_sub_comm_values = [];
        foreach ($new_sub_commissions_array as $new_item_com_array) {
            $sql_add_sub_comm_values[] = "('" . $new_item_com_array['merchantID'] . "','" . $new_item_com_array['affiliateID'] . "','" . $new_item_com_array['traderID'] . "','" . $new_item_com_array['transactionID'] . "','" . $new_item_com_array['Date'] . "','" . $new_item_com_array['Type'] . "','" . $new_item_com_array['Amount'] . "','" . $new_item_com_array['DealType'] . "','" . $new_item_com_array['Commission'] . "','" . $new_item_com_array['DealTypeCondition'] . "','" . $new_item_com_array['level'] . "','" . $new_item_com_array['subAffiliateID'] . "','" . $new_item_com_array['status'] . "','" . $new_item_com_array['updated'] . "')";
        }
        $sql_add_sub_comm = "INSERT INTO commissions (merchantID,affiliateID,traderID,transactionID,Date,Type,Amount,DealType,Commission,DealTypeCondition,level,subAffiliateID,status,updated) VALUES" . implode(',', $sql_add_sub_comm_values);

        //echo $sql_add_sub_comm;

        $sql_add_sub_comm_result = function_mysql_query($sql_add_sub_comm, __FILE__);

        if ($sql_add_sub_comm_result) {
            echo $item['merchantID'] . ' - ' . $item['affiliateID'] . ' - ' . $item['traderID'] . ' - ' . $item['transactionID'] . ' - ' . ' OK <br>';
            function_mysql_query("UPDATE `commissions` SET `status` = '2' WHERE `commissions`.`merchantID` = " . $item['merchantID'] . " AND `commissions`.`affiliateID` = " . $item['affiliateID'] . " AND `commissions`.`traderID` = '" . $item['traderID'] . "' AND `commissions`.`transactionID` = '" . $item['transactionID'] . "' AND `commissions`.`Type` = '" . $item['Type'] . "' AND `commissions`.`Date` = '" . $item['Date'] . "' AND CONCAT(`commissions`.`Amount`) = '" . $item['Amount'] . "' LIMIT 1", __FILE__);
        } else {
            echo 'Add Commission Failed';
            echo mysql_error();
            echo '<pre>';
            print_r($new_sub_commissions_array);
            echo '</pre>';
            echo "<br>--------------------<br>";
        }
    }
    unset($new_sub_commissions_array);


    flush();
}
?>
