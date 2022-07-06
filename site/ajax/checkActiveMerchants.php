<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';


$merWW = mysql_query("select id from merchants where valid = 1");
if(mysql_num_rows($merWW) == 1){
	$data = mysql_fetch_assoc($merWW);
	echo $data['id'];
	die;
}	

echo 0;
die;