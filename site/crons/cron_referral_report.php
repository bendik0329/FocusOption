<?php
$mem_start = memory_get_usage();

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 900);

require(__DIR__ . '/../common/database.php');
require(__DIR__ . '/../func/func_debug.php');

define('limit_transactions', 100000);
define('limit_commissions', 100000);
define('limit_traders', 10000);
define('limit_ftd', 10000);

$trafficUIDArray = [];

$dataCounter = [];

function setCronjobVariables($name, $value) {
    $date = date('Y-m-d H:i:s');
    $sql = "INSERT INTO CronjobVariables (Name,Date,Value) VALUES ('" . $name . "','" . $date . "','" . $value . "') ON DUPLICATE KEY UPDATE Date='" . $date . "', Value='" . $value . "'";
    $sql_result = function_mysql_query($sql);

    if ($sql_result) {
        $cronjobVariables[$name] = $value;
        return true;
    } else {
        die('Add value ' . $name . ' into CronjobVariables error<br>');
    }
}

function getTrafficByUID($uid) {
    $trafficRes = function_mysql_query("SELECT * FROM traffic where uid = '" . $uid . "' AND refer_url != '' LIMIT 1");
    return mysql_fetch_assoc($trafficRes);
}

$cronjobVariablesResult = function_mysql_query('SELECT * FROM CronjobVariables');
while ($item = mysql_fetch_assoc($cronjobVariablesResult)) {
    $cronjobVariables[$item['Name']] = $item['Value'];
}




/**
 * Transactions
 */
if (empty($cronjobVariables['referralReportLastTransactionId'])) {
    setCronjobVariables('referralReportLastTransactionId', 0);
    $cronjobVariables['referralReportLastTransactionId'] = 0;
}

$transactionsQuery = "SELECT ds.id, ds.rdate, ds.type, ds.amount,dr.affiliate_id, dr.merchant_id, dr.uid FROM data_sales ds 
INNER JOIN data_reg dr ON dr.trader_id = ds.trader_id AND dr.uid != '' AND dr.uid != 0 AND dr.uid IS NOT NULL AND dr.affiliate_id != 0 AND dr.merchant_id != 0
WHERE ds.id > " . $cronjobVariables['referralReportLastTransactionId'] . " ORDER BY ds.id ASC LIMIT " . limit_transactions;

$transactionsRes = function_mysql_query($transactionsQuery);
while ($item = mysql_fetch_assoc($transactionsRes)) {
    
    $dataCounter['Transactions'] += 1;
    
    $transactionValidateStatus = false;

    if ($trafficUIDArray[$item['uid']] && $trafficUIDArray[$item['uid']] != -1) {
        $item['referral'] = $trafficUIDArray[$item['uid']]['refer_url'];
        $transactionValidateStatus = true;
    } else {
        $traffic = getTrafficByUID($item['uid']);
        if ($traffic) {
            $trafficUIDArray[$item['uid']] = $traffic;

            $item['referral'] = $traffic['refer_url'];

            $transactionValidateStatus = true;
        } else {
            $trafficUIDArray[$item['uid']] = -1;
        }
    }

    if ($transactionValidateStatus) {
        if (!empty($item['referral'])) {
            $updateReferralStat = '';
            switch ($item['type']) {
                case 'PNL' :
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,PNL)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','" . $item['amount'] . "') ON DUPLICATE KEY UPDATE PNL=(PNL+" . $item['amount'] . ")";
                    break;
                case 'volume' :
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,Volume)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','" . $item['amount'] . "') ON DUPLICATE KEY UPDATE Volume=(Volume+" . $item['amount'] . ")";
                    break;
                case 'deposit' :
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,TotalDeposits,DepositsAmount)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','1','" . $item['amount'] . "') ON DUPLICATE KEY UPDATE TotalDeposits=(TotalDeposits+1), DepositsAmount=(DepositsAmount+" . $item['amount'] . ")";
                    break;
                case 'withdrawal' :
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,WithdrawalAmount)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','" . $item['amount'] . "') ON DUPLICATE KEY UPDATE WithdrawalAmount=(WithdrawalAmount+" . $item['amount'] . ")";
                    break;
                    break;
                case 'bonus' :
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,BonusAmount)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','" . $item['amount'] . "') ON DUPLICATE KEY UPDATE BonusAmount=(BonusAmount+" . $item['amount'] . ")";
                    break;
                case 'chargeback' :
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,ChargebackAmount)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','" . $item['amount'] . "') ON DUPLICATE KEY UPDATE ChargebackAmount=(ChargebackAmount+" . $item['amount'] . ")";
                    break;
            }

            try {
                function_mysql_query('BEGIN');

                if (!empty($updateReferralStat)) {
                    function_mysql_query($updateReferralStat);
                }
                setCronjobVariables('referralReportLastTransactionId', $item['id']);
                function_mysql_query('COMMIT');
                echo 'Add ' . $item['type'] . ' ID:' . $item['id'] . '<br>';
            } catch (Exception $e) {
                function_mysql_query('ROLLBACK');
                die('Add ' . $item['type'] . ' ID:' . $item['id'] . ' ERROR');
            }
        }
    }else{
        setCronjobVariables('referralReportLastTransactionId', $item['id']);
    }
}

unset($transactionsRes);


/**
 * Commissions
 */
if (empty($cronjobVariables['referralReportLastCommissionDate'])) {
    setCronjobVariables('referralReportLastCommissionDate', 0);
    $cronjobVariables['referralReportLastCommissionDate'] = 0;
}

$commissionsQuery = "SELECT cm.Date, cm.Amount, cm.affiliateID, cm.merchantID, dr.uid FROM commissions cm 
INNER JOIN data_reg dr ON dr.trader_id = cm.traderID AND dr.uid != '' AND dr.uid != 0 AND dr.uid IS NOT NULL AND dr.affiliate_id != 0 AND dr.merchant_id != 0
WHERE cm.Date > '" . $cronjobVariables['referralReportLastCommissionDate'] . "' ORDER BY cm.Date ASC LIMIT " . limit_commissions;

$commissionsRes = function_mysql_query($commissionsQuery);
while ($item = mysql_fetch_assoc($commissionsRes)) {
    
    $dataCounter['Commissions'] += 1;
    
    $transactionValidateStatus = false;

    if ($trafficUIDArray[$item['uid']] && $trafficUIDArray[$item['uid']] != -1) {
        $item['referral'] = $trafficUIDArray[$item['uid']]['refer_url'];
        $transactionValidateStatus = true;
    } else {
        $traffic = getTrafficByUID($item['uid']);
        if ($traffic) {
            $trafficUIDArray[$item['uid']] = $traffic;

            $item['referral'] = $traffic['refer_url'];

            $transactionValidateStatus = true;
        } else {
            $trafficUIDArray[$item['uid']] = -1;
        }
    }

    if ($transactionValidateStatus) {
        if (!empty($item['referral'])) {

            $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,Commissions)
                    VALUES('" . date('Y-m-d', strtotime($item['Date'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliateID'] . "','" . $item['merchantID'] . "','" . $item['Amount'] . "') ON DUPLICATE KEY UPDATE Commissions=(Commissions+" . $item['Amount'] . ")";

            try {
                function_mysql_query('BEGIN');

                if (!empty($updateReferralStat)) {
                    function_mysql_query($updateReferralStat);
                }
                setCronjobVariables('referralReportLastCommissionDate', $item['Date']);
                function_mysql_query('COMMIT');
                echo 'Add commission ID:' . $item['Date'] . '-' . $item['affiliateID'] . '-' . $item['merchantID'] . '<br>';
            } catch (Exception $e) {
                function_mysql_query('ROLLBACK');
                die('Add commission ID:' . $item['Date'] . '-' . $item['affiliateID'] . '-' . $item['merchantID'] . ' ERROR');
            }
        }
    }else{
        setCronjobVariables('referralReportLastCommissionDate', $item['Date']);
    }
}
unset($commissionsRes);

/**
 * Traders
 */
if (empty($cronjobVariables['referralReportTraderId'])) {
    setCronjobVariables('referralReportTraderId', 0);
    $cronjobVariables['referralReportTraderId'] = 0;
}

$tradersQuery = "select dr.id, dr.rdate, dr.trader_id, dr.type, dr.affiliate_id, dr.merchant_id, dr.uid 
from data_reg dr 
where dr.uid != '' AND dr.uid != 0 AND dr.uid IS NOT NULL AND dr.affiliate_id != 0 AND dr.merchant_id != 0 AND dr.id > " . $cronjobVariables['referralReportTraderId'] . "  AND dr.type !='' 
ORDER BY dr.id ASC LIMIT " . limit_traders;

$tradersRes = function_mysql_query($tradersQuery);
while ($item = mysql_fetch_assoc($tradersRes)) {
    
    $dataCounter['Traders'] += 1;
    
    $transactionValidateStatus = false;

    if ($trafficUIDArray[$item['uid']] && $trafficUIDArray[$item['uid']] != -1) {
        $item['referral'] = $trafficUIDArray[$item['uid']]['refer_url'];
        $transactionValidateStatus = true;
    } else {
        $traffic = getTrafficByUID($item['uid']);
        if ($traffic) {
            $trafficUIDArray[$item['uid']] = $traffic;

            $item['referral'] = $traffic['refer_url'];

            $transactionValidateStatus = true;
        } else {
            $trafficUIDArray[$item['uid']] = -1;
        }
    }

    if ($transactionValidateStatus) {
        if (!empty($item['referral'])) {

            $updateReferralStat = '';
            switch ($item['type']) {
                case 'demo':
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,Demo)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','1') ON DUPLICATE KEY UPDATE Demo=(Demo+1)";
                    break;
                case 'lead':
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,Leads)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','1') ON DUPLICATE KEY UPDATE Leads=(Leads+1)";
                    break;
                case 'real':
                    $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,Accounts)
                    VALUES('" . date('Y-m-d', strtotime($item['rdate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','1') ON DUPLICATE KEY UPDATE Accounts=(Accounts+1)";
                    break;
                default: continue;
            }

            try {
                function_mysql_query('BEGIN');

                if (!empty($updateReferralStat)) {
                    function_mysql_query($updateReferralStat);
                }
                setCronjobVariables('referralReportTraderId', $item['id']);
                function_mysql_query('COMMIT');
                echo 'Add trader ID:' . $item['trader_id'] . '(' . $item['type'] . ')<br>';
            } catch (Exception $e) {
                function_mysql_query('ROLLBACK');
                die('Add trader ID:' . $item['trader_id'] . ' ERROR');
            }
        }
    }else{
        setCronjobVariables('referralReportTraderId', $item['id']);
    }
}
unset($tradersRes);


/**
 * RAW FTDs
 */

if (empty($cronjobVariables['referralReportLastRawFtdDate'])) {
    setCronjobVariables('referralReportLastRawFtdDate', '0000-00-00 00:00:00');
    $cronjobVariables['referralReportLastRawFtdDate'] = '0000-00-00 00:00:00';
}

$rawFtdQuery = "select dr.id, dr.initialftddate, dr.ftdamount, dr.trader_id, dr.affiliate_id, dr.merchant_id, dr.uid 
from data_reg dr 
where dr.uid != '' AND dr.uid != 0 AND dr.uid IS NOT NULL AND dr.affiliate_id != 0 AND dr.merchant_id != 0 AND dr.initialftddate > '" . $cronjobVariables['referralReportLastRawFtdDate'] . "'  AND dr.type ='real' AND dr.FTDqualificationDate = '0000-00-00 00:00:00' AND dr.ftdamount > 0
ORDER BY dr.initialftddate ASC LIMIT " . limit_ftd;

$rawFtdRes = function_mysql_query($rawFtdQuery);
while ($item = mysql_fetch_assoc($rawFtdRes)) {

    $dataCounter['RAWFTD'] += 1;
    
    $transactionValidateStatus = false;

    if ($trafficUIDArray[$item['uid']] && $trafficUIDArray[$item['uid']] != -1) {
        $item['referral'] = $trafficUIDArray[$item['uid']]['refer_url'];
        $transactionValidateStatus = true;
    } else {
        $traffic = getTrafficByUID($item['uid']);
        if ($traffic) {
            $trafficUIDArray[$item['uid']] = $traffic;

            $item['referral'] = $traffic['refer_url'];

            $transactionValidateStatus = true;
        } else {
            $trafficUIDArray[$item['uid']] = -1;
        }
    }

    if ($transactionValidateStatus) {
        if (!empty($item['referral'])) {

            $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,RawFTD,RawFTDAmount)
                    VALUES('" . date('Y-m-d', strtotime($item['initialftddate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','1','" . $item['ftdamount'] . "') ON DUPLICATE KEY UPDATE RawFTD=(RawFTD+1), RawFTDAmount=(RawFTDAmount+" . $item['ftdamount'] . ")";

            try {
                function_mysql_query('BEGIN');

                if (!empty($updateReferralStat)) {
                    function_mysql_query($updateReferralStat);
                }
                setCronjobVariables('referralReportLastRawFtdDate', $item['initialftddate']);
                function_mysql_query('COMMIT');
                echo 'Add commission ID:' . $item['initialftddate'] . '-' . $item['affiliate_id'] . '-' . $item['merchant_id'] . '<br>';
            } catch (Exception $e) {
                function_mysql_query('ROLLBACK');
                die('Add commission ID:' . $item['initialftddate'] . '-' . $item['affiliate_id'] . '-' . $item['merchant_id'] . ' ERROR');
            }
        }
    }else{
        setCronjobVariables('referralReportLastRawFtdDate', $item['initialftddate']);
    }
}
unset($rawFtdRes);


/**
 * RAW FTDs
 */

if (empty($cronjobVariables['referralReportLastFtdDate'])) {
    setCronjobVariables('referralReportLastFtdDate', '0000-00-00 00:00:01');
    $cronjobVariables['referralReportLastFtdDate'] = '0000-00-00 00:00:01';
}

$ftdQuery = "select dr.id, dr.FTDqualificationDate, dr.ftdamount, dr.trader_id, dr.affiliate_id, dr.merchant_id, dr.uid 
from data_reg dr 
where dr.uid != '' AND dr.uid != 0 AND dr.uid IS NOT NULL AND dr.affiliate_id != 0 AND dr.merchant_id != 0 AND dr.initialftddate != '0000-00-00 00:00:00' AND dr.type ='real' AND dr.FTDqualificationDate >= dr.initialftddate AND dr.FTDqualificationDate > '" . $cronjobVariables['referralReportLastFtdDate'] . "' AND dr.ftdamount > 0
ORDER BY dr.FTDqualificationDate ASC LIMIT " . limit_ftd;

$ftdRes = function_mysql_query($ftdQuery);
while ($item = mysql_fetch_assoc($ftdRes)) {

    $dataCounter['FTD'] += 1;
    
    $transactionValidateStatus = false;

    if ($trafficUIDArray[$item['uid']] && $trafficUIDArray[$item['uid']] != -1) {
        $item['referral'] = $trafficUIDArray[$item['uid']]['refer_url'];
        $transactionValidateStatus = true;
    } else {
        $traffic = getTrafficByUID($item['uid']);
        if ($traffic) {
            $trafficUIDArray[$item['uid']] = $traffic;

            $item['referral'] = $traffic['refer_url'];

            $transactionValidateStatus = true;
        } else {
            $trafficUIDArray[$item['uid']] = -1;
        }
    }

    if ($transactionValidateStatus) {
        if (!empty($item['referral'])) {

            $updateReferralStat = "INSERT INTO ReportReferral(Date,ReferUrlHash,ReferUrl,AffiliateID,MerchantID,FTD,FTDAmount)
                    VALUES('" . date('Y-m-d', strtotime($item['FTDqualificationDate'])) . "','" . md5($item['referral']) . "','" . mysql_real_escape_string($item['referral']) . "','" . $item['affiliate_id'] . "','" . $item['merchant_id'] . "','1','" . $item['ftdamount'] . "') ON DUPLICATE KEY UPDATE FTD=(FTD+1), FTDAmount=(FTDAmount+" . $item['ftdamount'] . ")";

            try {
                function_mysql_query('BEGIN');

                if (!empty($updateReferralStat)) {
                    function_mysql_query($updateReferralStat);
                }
                setCronjobVariables('referralReportLastFtdDate', $item['FTDqualificationDate']);
                function_mysql_query('COMMIT');
                echo 'Add commission ID:' . $item['FTDqualificationDate'] . '-' . $item['affiliate_id'] . '-' . $item['merchant_id'] . '<br>';
            } catch (Exception $e) {
                function_mysql_query('ROLLBACK');
                die('Add commission ID:' . $item['FTDqualificationDate'] . '-' . $item['affiliate_id'] . '-' . $item['merchant_id'] . ' ERROR');
            }
        }
    }else{
        setCronjobVariables('referralReportLastFtdDate', $item['FTDqualificationDate']);
    }
}
unset($rawFtdRes);


echo "<pre>";
echo "Data Counter<br>";
print_r($dataCounter);
echo "Variables<br>";
print_r($cronjobVariables);
echo 'Memory: '.(memory_get_usage() - $mem_start);
echo "</pre>";