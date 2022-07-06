<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
/**
 * check trader (trader tag page)
 */
 
if (isset($_POST['affiliate_id']) && !empty($_POST['affiliate_id'])) {
	require '../common/database.php';
	
	$sql    = 'update affiliates set apiStaticIP = "" where id = ' . $_POST['affiliate_id'];
	function_mysql_query($sql,__FILE__,__FUNCTION__);
	echo  true;
	exit;
	
} else {
	echo false;
	exit;
}