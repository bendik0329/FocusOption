<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
/**
 * Load merchants info dynamically via ajax call.
 */

$advertiser_id = isset($_GET['advertiser']) ? $_GET['advertiser_id'] : 0;
$affiliate_id = isset($_GET['affiliate_id']) ? $_GET['affiliate_id'] : 0;
 
 // var_dump($_GET);
if (!empty($affiliate_id) || !empty($advertiser_id)) {
	require '../common/database.php';
	$arrResult = array();
		$sql = "SELECT * FROM affiliates_msgs WHERE valid='1' AND status_id='".$_GET['status_id']."' AND ( " . 
		(!empty($advertiser_id) ?  " (advertiser_id='".$advertiser_id."' OR (advertiser_id='0' and affiliate_id = -1)) " : "" ). 
		(!empty($affiliate_id) ?  " (affiliate_id='".$affiliate_id."' OR (affiliate_id='0' and advertiser_id = -1)) " : "" ).
		" AND (group_id='0' OR group_id='".$_GET['group_id']."') ) AND display_approval_popup = 1";
		$msgqq = function_mysql_query($sql); 
		while ($msgww=mysql_fetch_assoc($msgqq)) {
			$sql="select count(*) as approved from affiliate_messages_approval where ".
			(!empty($advertiser_id) ?  " advertiser_id='".$advertiser_id."' " : "" ). 
			(!empty($affiliate_id) ?  " affiliate_id='".$affiliate_id."' " : "" ). 
			" and message_id=".$msgww['id'];
			 $data = mysql_fetch_assoc(function_mysql_query($sql));
			if(isset($data['approved']) && $data['approved'] == 0){
				$arrResult[] = $msgww;
			}
		}
	
	echo json_encode(array('success' => $arrResult));
	exit;
	
} else {
	echo json_encode(array('error' => 'Missing parameter'));
	exit;
}