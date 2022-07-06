<?php

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 900);

require(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');

$tradersQuery = 'SELECT COUNT(dr.affiliate_id) as aff_count , tr.TraderID as trader_id, tr.AffiliateID as tr_affid, dr.affiliate_id as dr_affid, dr.merchant_id as dr_merchant_id FROM ReportTraders tr INNER JOIN data_reg dr ON dr.trader_id = tr.TraderID WHERE dr.affiliate_id <> tr.AffiliateID GROUP BY dr.trader_id ORDER BY aff_count DESC';

$tradersData = function_mysql_query($tradersQuery, __FILE__);
while ($traderItem = mysql_fetch_assoc($tradersData)) {
    if($traderItem['aff_count'] == 1){
    
        if($traderItem['dr_affid'] > 0 && $traderItem['trader_id'] > 0 && $traderItem['tr_affid'] > 0 && $traderItem['dr_merchant_id'] > 0){
    
            $update_query = "UPDATE `ReportTraders` SET `AffiliateID` = '".$traderItem['dr_affid']."' WHERE `ReportTraders`.`TraderID` = '".$traderItem['trader_id']."' AND `ReportTraders`.`AffiliateID` = '".$traderItem['tr_affid']."' AND `ReportTraders`.`MerchantID` = ".$traderItem['dr_merchant_id'];
            //function_mysql_query($update_query, __FILE__);
            echo $update_query."<br>";
        }
    
    }
    
}