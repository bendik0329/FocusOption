<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';

require('../common/database.php');

if (isset($_GET['profile_id']) && $_GET['profile_id']!="") {
    $profile_id = $_GET['profile_id'];
	$sql = 'SELECT * FROM permissionProfile WHERE id = '.$profile_id;
    $profileqq    = function_mysql_query('SELECT * FROM permissionProfile WHERE id = '.$profile_id,__FILE__,__FUNCTION__);
    
    $profileww = mysql_fetch_assoc($profileqq);
		if(!empty($profileww))
			echo json_encode($profileww);
		else{
			$arr['failed'] = 'failed';
			echo json_encode($arr);
		}
}
else{
		$arr['failed'] = 'failed';
		echo json_encode($arr);
}

