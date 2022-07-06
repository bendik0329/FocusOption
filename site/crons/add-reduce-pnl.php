<?php

require(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');


$loopAffiliates = mysql_query("SELECT aff.id FROM `affiliates` aff LEFT JOIN `traders_deals` td ON aff.id = td.affiliate_id and td.dealType = 'pnl_lower' WHERE td.affiliate_id IS NULL ORDER BY aff.id");
while ($affiliate = mysql_fetch_assoc($loopAffiliates)){

    $sql = "INSERT INTO `traders_deals` "
                                . "(`rdate`, `admin_id`,`merchant_id`,`affiliate_id`,`trader_id`,`dealType`,`amount`,`valid`) VALUES "
                                . "('".date('Y-m-d H:i:s')."', '31','0','".$affiliate['id']."','0','pnl_lower','30','1')";
    echo $sql;
    function_mysql_query ($sql,__FILE__);
    die();
    
}