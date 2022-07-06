<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
require_once '../func/func_global.php';
/**
 * check trader (trader tag page)
 */
 
///if (isset($_GET['trader_id']) && !empty($_GET['trader_id'])) {
	require '../common/database.php';
	
	$affiliate_id = retrieveAffiliateId($_GET['affiliate_id']);	
	
	$where = " FTDqualificationDate !='0000-00-00 00:00:00'";
	
	if(!empty($affiliate_id))
		$where .= " and affiliate_id = " . $affiliate_id;
	
	if(!empty($_GET['merchant_id']))
		$where .= " and merchant_id = " . $_GET['merchant_id'];
	
	if(!empty($_GET['trader_id']) && empty($_GET['date_from']) && empty($_GET['date_to'])){
		$trader_id = $_GET['trader_id'];
		$where .= " and trader_id = " . $trader_id;
	}
	
	if(!empty($_GET['date_from']) && !empty($_GET['date_to']) && empty($_GET['trader_id'])){
		$datefrom = str_replace('/', '-', $_GET['date_from']);
		$dateto = str_replace('/', '-', $_GET['date_to']);
		$datefrom = date("Y-m-d",strtotime($datefrom));
		$dateto = date("Y-m-d",strtotime($dateto));
		$where .= " and FTDqualificationDate between '" . $datefrom . "' and '" . $dateto . "'";
	}
	
	if(!empty($_GET['trader_id']) && !empty($_GET['date_from']) && !empty($_GET['date_to'])){
		$datefrom = str_replace('/', '-', $_GET['date_from']);
		$dateto = str_replace('/', '-', $_GET['date_to']);
		$datefrom = date("Y-m-d",strtotime($datefrom));
		$dateto = date("Y-m-d",strtotime($dateto));
		$where .= " and trader_id =  " . $_GET['trader_id'] ." and  FTDqualificationDate between '" . $datefrom . "' and '" . $dateto . "'";
	}
	if($_GET['type'] == "getCount"){
	$sql    = 'SELECT count(*) as valuesToUpdate  FROM data_reg WHERE ' . $where;
	$result = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
	
	echo $result['valuesToUpdate'];
	}
	else{
		if(isset($_GET['dateType']) && $_GET['dateType'] == 'automatic')
			$dateChange = '0000-00-00 00:00:00';
		else
			$dateChange = $_GET['date_change'];
		
		$sql    = 'update data_reg set FTDqualificationDate = "'. $dateChange .'" WHERE ' . $where;
		$result = function_mysql_query($sql,__FILE__,__FUNCTION__);
	
		echo "true";
	}
	die;
/* } else {
	echo json_encode(array('error' => 'Missing parameter'));
	exit;
} */