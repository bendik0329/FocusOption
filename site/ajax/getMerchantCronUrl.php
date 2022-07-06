<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
/**
 * Load merchants info dynamically via ajax call.
 */
 
 // var_dump($_GET);
if (isset($_GET['merchant_id']) && !empty($_GET['merchant_id'])) {
	require '../common/database.php';
	
	$sql    = 'SELECT cronjoburl  FROM merchants WHERE id = ' . $_GET['merchant_id'];
	$result = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
	
	echo $result['cronjoburl'];
	exit;
	
} else {
	echo json_encode(array('error' => 'Missing parameter'));
	exit;
}