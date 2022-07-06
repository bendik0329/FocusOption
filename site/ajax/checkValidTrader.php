<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
/**
 * check trader (trader tag page)
 */
 
if (isset($_GET['trader_id']) && !empty($_GET['trader_id'])) {
	require '../common/database.php';
	
	$sql    = 'SELECT count(*) as traders  FROM data_reg WHERE trader_id = "' . $_GET['trader_id'] .'"';
	$result = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
	
	if (empty($result['traders'])) {
		echo false;
		exit;
	}
	else{
		echo true;
		exit;
	}
	
} else {
	echo json_encode(array('error' => 'Missing parameter'));
	exit;
}