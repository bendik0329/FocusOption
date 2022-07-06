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
if (isset($_GET['affiliate_id']) && !empty($_GET['affiliate_id'])) {
	require '../common/database.php';
	
	$sql    = 'SELECT merchants AS merchants FROM affiliates WHERE id = ' . $_GET['affiliate_id'];
	$result = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
	unset($sql);
	
	$merchs = str_replace('|', ',', $result['merchants']);
	$merchs = ltrim($merchs,',');
	if (empty($merchs))
		$merchs=0;
	$sql       = 'SELECT id AS id, name AS name FROM merchants WHERE id IN(' . $merchs . ')';
 
	$resource  = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$arrResult = array();
	if (empty($result['merchants'])) {
		$arrResult[] = array(
			'id'   => '0',
			'name' => ''
			);
	}
	while ($arrRow = mysql_fetch_assoc($resource)) {
		$arrResult[] = array(
			'id'   => $arrRow['id'],
			'name' => $arrRow['name'],
		);
		unset($arrRow);
	}
	
	echo json_encode(array('success' => $arrResult));
	exit;
	
} else {
	echo json_encode(array('error' => 'Missing parameter'));
	exit;
}