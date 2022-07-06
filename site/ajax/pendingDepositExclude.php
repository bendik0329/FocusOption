<?php

@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
require_once '../common/database.php';
require_once '../func/func_string.php';
require_once '../func/func_global.php';

if(isset($_POST["affiliate_id"]) && isset($_POST['affiliate_exclude'])){
	
	$affiliate_id = retrieveAffiliateId($_POST["affiliate_id"]);
	
    $sql = "update affiliates set pendingDepositExclude = 1 where id =" . (int)$affiliate_id;
	function_mysql_query($sql);
	
	echo 1;
	die;
} else {

    $sql = "update affiliates set pendingDepositExclude = 0 where id =" . (int)$_POST['affiliate_id'];
	function_mysql_query($sql);
	
	echo 1;
	die;
}
echo 0;
die;
?>
