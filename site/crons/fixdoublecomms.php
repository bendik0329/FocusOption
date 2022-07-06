<?php
require(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');


$loop = mysql_query("SELECT affiliateID, traderID, transactionID, COUNT(*) c FROM commissions where Type = 'FTD' GROUP BY transactionID HAVING c > 1 LIMIT 50");
while ($comission = mysql_fetch_assoc($loop)){

    echo "<pre>";
    echo "-------------------------------------------------<br>";
    print_r($comission);

    $loop_double = mysql_query("SELECT * FROM `commissions` WHERE transactionID = ".$comission['transactionID']." AND traderID = ".$comission['traderID']." AND affiliateID = ".$comission['affiliateID'].' ORDER BY Date ASC LIMIT 1,10');

    while ($item = mysql_fetch_assoc($loop_double)){
        echo "Double:<br>";
        print_r($item);

        $sql = "DELETE FROM `commissions` WHERE `commissions`.`merchantID` = ".$item['merchantID']." AND `commissions`.`affiliateID` = ".$item['affiliateID']." AND `commissions`.`traderID` = '".$item['traderID']."' AND `commissions`.`transactionID` = '".$item['transactionID']."' AND `commissions`.`Date` = '".$item['Date']."' AND `commissions`.`Type` = '".$item['Type']."' AND CONCAT(`commissions`.`Amount`) = '".$item['Amount']."'";
        mysql_query($sql);
        
    }
    
    
    
    echo "</pre>";
    
    flush();


}