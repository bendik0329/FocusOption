<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';
if(isset($_POST['affiliate_id'])){
	$sql = "update affiliates set emailVerification=1 where id=" . $_POST['affiliate_id'] ;
	mysql_query($sql);
	echo "success";
	exit;	
}
echo "error";
exit;
?>