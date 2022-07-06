<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';
try{
	$rdate = date('Y-m-d h:i:s');
	$user_id = $_POST['user_id'];
	$level = $_POST['userlevel'];
	$location =$_POST['location'];
	$removed_fields = $_POST['removed_fields'];
	
	$sql = "select * from reports_fields where userlevel = '" .  $level . "' and location='". $location ."' and user_id=". $user_id;
	
	$checkExists = mysql_fetch_assoc(mysql_query($sql));
	if($checkExists){
			$sql = "update reports_fields set removed_fields='$removed_fields' where  userlevel = '$level' and location='$location' and user_id=". $user_id;
			mysql_query($sql);
	}
	else{
		mysql_query("insert into reports_fields (userlevel,user_id,location, removed_fields ) values ('$level','$user_id','$location','$removed_fields')");
	}
	
	echo 1;
	die;
}
catch(Exception $e){
	echo 0;
	die;
}
