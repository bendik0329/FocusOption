<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
require_once '../func/func_global.php';
require '../common/database.php';
$sql = "";
if($_GET['userLevel']=="all")
	$sql = "truncate table reports_fields";
else if(!empty($_GET['id']))
	$sql = "delete from reports_fields where userLevel ='" . $_GET['userLevel'] . "' and user_id=". $_GET['id'];

if(!empty($sql))
mysql_query($sql);

echo 1;

die;
