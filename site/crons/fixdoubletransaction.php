<?php
require(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');


$loop = mysql_query("SELECT rdate, affiliate_id, trader_id, tranz_id, COUNT(*) c FROM data_sales where type = 'PNL' AND rdate >= '2019-04-01' GROUP BY tranz_id HAVING c > 1 LIMIT 20");
while ($transaction = mysql_fetch_assoc($loop)){

    echo "<pre>";
    echo "-------------------------------------------------<br>";
    
    $flag_first = false;
    
    $loop_double_trans = mysql_query("SELECT id, rdate, affiliate_id, trader_id, tranz_id FROM `data_sales` WHERE tranz_id = ".$transaction['tranz_id']." AND trader_id = ".$transaction['trader_id']." AND affiliate_id = ".$transaction['affiliate_id'].' LIMIT 100');
    while ($item_trans = mysql_fetch_assoc($loop_double_trans)){
        
        print_r($item_trans);
        echo "<br>";
        
        
        $loop_double = mysql_query("SELECT * FROM `commissions` WHERE transactionID = ".$item_trans['id']." AND traderID = ".$item_trans['trader_id']." AND affiliateID = ".$item_trans['affiliate_id'].' ORDER BY Date ASC LIMIT 100');
        while ($item = mysql_fetch_assoc($loop_double)){
            echo "Double:<br>";
            print_r($item);
    
            $sql = "DELETE FROM `commissions` WHERE `commissions`.`merchantID` = ".$item['merchantID']." AND `commissions`.`affiliateID` = ".$item['affiliateID']." AND `commissions`.`traderID` = '".$item['traderID']."' AND `commissions`.`transactionID` = '".$item['transactionID']."' AND `commissions`.`Date` = '".$item['Date']."' AND `commissions`.`Type` = '".$item['Type']."' AND CONCAT(`commissions`.`Amount`) = '".$item['Amount']."'";
            mysql_query($sql);
            echo "<br>Comm del";
            
        }
        
        if($flag_first){
            $sql = "DELETE FROM `data_sales` WHERE `data_sales`.`id` = ".$item_trans['id']." LIMIT 1";
            mysql_query($sql);
            echo "<br> trans del";
        }
            
        $flag_first = true;
        
    }


    

    
    
    echo "</pre>";
    
    flush();


}