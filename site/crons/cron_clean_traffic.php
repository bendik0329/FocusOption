<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
// require_once('common/global.php');
$runthis = $_GET['runthis']==2 ? true : false;
if (!$runthis)
	die('.');

$removeClickRecordsPerStep = 50;

require(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');

$cronjobVariablesResult = function_mysql_query('SELECT * FROM CronjobVariables');
while ($item = mysql_fetch_assoc($cronjobVariablesResult)) {
    $cronjobVariables[$item['Name']] = $item['Value'];
}

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

if (!empty($cronjobVariables['lastCleanupTrafficRegID'])){
    $setupConditionReg = "id > ".$cronjobVariables['lastCleanupTrafficRegID']."";
} else {
    $setupConditionReg = "1 = 1";
}

if (!empty($cronjobVariables['lastCleanupTrafficID'])){
    $setupCondition = "id > ".$cronjobVariables['lastCleanupTrafficID']."";
} else {
    $setupCondition = "1 = 1";
}


$dateOlder = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "-31 days"));
$dateOlderView = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "-3 days"));
$dateOlderPlusOne = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "-30 days"));


$sql = "DELETE FROM traffic where rdate < '".$dateOlderView."' AND views = 1  LIMIT 1000000";
$result = function_mysql_query($sql,__FILE__);

if($result){
    echo "Old traffic 'views' rows has been successfully removed";
} else {
    echo "Removing 'views' rows is not complete";
}



$dataRegSql = "SELECT id, uid FROM data_reg WHERE rdate < '".$dateOlderPlusOne."' AND uid > 0 AND ".$setupConditionReg." ";
$dataReg = function_mysql_query($dataRegSql,__FILE__);

$dataRegIds = [];

while ($item = mysql_fetch_assoc($dataReg)) {
    $dataRegIds[$item['id']] = $item['uid'];
}

$sqlGetId = "SELECT id, uid FROM traffic WHERE clicks = 1 AND  rdate < '".$dateOlderPlusOne."' AND ".$setupCondition." ORDER BY id ASC  ";
$resGetId = function_mysql_query($sqlGetId,__FILE__);
$lastClickId = 0;

$stepCounter = 0;
$removeBuffer = [];
$lastRegId = 0;
while ($item = mysql_fetch_assoc($resGetId)) {

    $find = false;
    foreach ($dataRegIds as $key => $value) {
        if ($value == $item['uid']) {
            $lastRegId = $key;
            $find = true;
        }
    }

    if (!$find) {
        $removeBuffer[] = $item['id'];
    }

    if ($stepCounter > $removeClickRecordsPerStep) {

        $removeBuffer = implode(',', $removeBuffer);

        $sql = "DELETE FROM traffic where id IN (".$removeBuffer.");";
        $result = function_mysql_query($sql,__FILE__);

        if($result){

            echo "LAST REG: ".$lastRegId;

            if ($lastRegId > 0) {
                setCronjobVariables("lastCleanupTrafficRegID", $lastRegId);
            }
            if ($item['id'] > 0) {
                setCronjobVariables("lastCleanupTrafficID", $item['id']);
            }

            echo "Old traffic 'clicks' ".$removeClickRecordsPerStep." rows has been successfully removed";
            $stepCounter = 0;
            $removeBuffer = [];
        } else {
            echo "Removing 'clicks' is not complete"; die;
        }
    }
    $stepCounter++;
    $lastClickId = $item['id'];
}

?>