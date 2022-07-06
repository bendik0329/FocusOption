<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

if(!empty($_POST['affiliate'])){
	
	
	$sql = "update affiliates set pendingDepositExclude = 1 where id= ".  $_POST['affiliate'];
	
	mysql_query($sql);
	
	echo 1;
	die;
	
	
}

echo 0;
die;

?>