<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';


if(isset($_GET['id'])){
	
	$blockTraffic = $_GET['blockTraffic'];
	
	
	$sql = "update affiliates set blockNewTraffic=" . $blockTraffic . " where id=" . $_GET['id'];

	mysql_query($sql);
	
	echo 1;
	die;
	
	
}
else{
	echo "Missing required values";
}