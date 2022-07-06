<?php
/**
 * cronjob_calc_revenue.php
 */
require 'common/global.php';
$isRunAllTime = isset($_GET['all']) ? $_GET['all'] : 0;
$cntInserted = 0;
$cntDeleted  = 0;
$arrDates    = array(
    array(
        'from' => date('Y-m-d',       strtotime('-4 day')), 
        'to'   => date('Y-m-d H:i:s', strtotime('-4 day')),
    ),
    array(
        'from' => date('Y-m-d',       strtotime('-3 day')), 
        'to'   => date('Y-m-d H:i:s', strtotime('-3 day')),
    ),
    array(
        'from' => date('Y-m-d',       strtotime('-2 day')), 
        'to'   => date('Y-m-d H:i:s', strtotime('-2 day')),
    ),
);
if ($isRunAllTime==1) {
	// Run the "all time" update.
	$arrDates     = array();
	$from         = new DateTime('2014-01-01');
	$to           = new DateTime('2015-02-23');
	$amountOfDays = $to->diff($from)->format('%a');

	for ($i = 1; $i <= $amountOfDays; $i++) {
		$arrDates[] = array(
			'from' => date('Y-m-d',       strtotime('-' . $i . ' day')), 
			'to'   => date('Y-m-d H:i:s', strtotime('-' . $i . ' day')),
		);	
	}

}
// Retrieve merchants.
$sql = "SELECT id AS id, type AS type FROM merchants WHERE valid = 1;";
$resourceMerchants = mysql_query($sql);

while ($arrMerchant = mysql_fetch_assoc($resourceMerchants)) {

    // Retrieve affiliates.
    $sql = "SELECT id AS id FROM affiliates WHERE valid = 1 AND merchants LIKE '%" . $arrMerchant['id'] . "%' ";
    $resourceAffiliates = mysql_query($sql);

    while ($arrAffiliate = mysql_fetch_assoc($resourceAffiliates)) {

        foreach ($arrDates as $arrDate) {
            $where = "WHERE merchant_id = " . $arrMerchant['id'] . " AND rdate BETWEEN '" . $arrDate['from'] . "' AND '" . $arrDate['to'] . "' AND affiliate_id = " . $arrAffiliate['id'];

            $date = new DateTime($arrDate['to']);
            //$date->modify('-1 day');
            
            $whereForDelete = "WHERE merchant_id = " . $arrMerchant['id'] 
                            . " AND rdate BETWEEN '" . $date->format('Y-m-d') . "' AND '" . $date->format('Y-m-d H:i:s')
                            . "' AND affiliate_id = " . $arrAffiliate['id'];
            
            // Retrieve traders.
            $sql = "SELECT DISTINCT data_stats.trader_id AS trader_id, data_stats.* FROM `data_stats` AS data_stats " . $where;
            $resourceTraders = mysql_query($sql);

            while ($arrTrader = mysql_fetch_assoc($resourceTraders)) {
                $where       .= ' AND trader_id = ' . $arrTrader['trader_id'];
                $sql          = "SELECT amount AS amount, rdate AS rdate FROM `data_stats` " . $where . " AND type = 'static'";
                $arrDataStat  = mysql_fetch_assoc(mysql_query($sql));
                
                if (empty($arrDataStat) || is_null($arrDataStat['amount'])) {
                    $revenue = getRevenue($where, $arrMerchant['type']);
                    
                    if (!empty($revenue)) {
                        $sql = "INSERT INTO `data_stats`(`rdate`, `ctag`, `affiliate_id`, `group_id`, `banner_id`, `profile_id`, `country`, `tranz_id`, `trader_id`, 
                                            `trader_alias`, `type`, `amount`, `freeParam`, `merchant_id`, `uid`) 
                                VALUES ('" . $arrTrader['rdate'] . "', '" . $arrTrader['ctag'] . "', '" . $arrTrader['affiliate_id'] . "', '" . $arrTrader['group_id'] . "', 
                                        '" . $arrTrader['banner_id'] . "', '" . $arrTrader['profile_id'] . "', '" . $arrTrader['country'] . "', 
                                        '" . $arrTrader['tranz_id'] . "', '" . $arrTrader['trader_id'] . "', '" . $arrTrader['trader_alias'] . "', 
                                        'static', " . $revenue . ", '" . $arrTrader['freeParam'] . "', '" . $arrTrader['merchant_id'] . "', '" . $arrTrader['uid'] . "');";
                        
                        if (!mysql_query($sql)) {
                            echo $sql, '<hr>';
                        } else {
                            // In case of successful insertion
                            // delete corresponding records for following types: 'bets', 'wins', 'jackpot', 'bonuses', 'removed_bonuses'.
                            $cntInserted++;
                            $sql = "DELETE FROM `data_stats` " . $whereForDelete . " AND type IN ('bets', 'wins', 'jackpot', 'bonuses', 'removed_bonuses');";
                            
                            if (!mysql_query($sql)) {
                                echo $sql, '<hr>';
                            } else {
                                $cntDeleted += mysql_affected_rows();
                            }
                        }
                    }
                }
            }			
        }
    }
}

echo '<hr><hr>Inserted: ', $cntInserted, '<br />Deleted: ', $cntDeleted;
exit;



 
 
 
 
 
 
 
 
 
 
 