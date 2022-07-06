<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';

	$type = isset($_POST['type'])?$_POST['type']:$_GET['type'];

	if($type == "update" || $type == "delete")
		$id = isset($_POST['id'])?$_POST['id']:$_GET['id'];
	else
		$id = 0; 
	
	$rdate = date('Y-m-d h:i:s');
	$url = isset($_POST['report_url'])?$_POST['report_url']:$_GET['report_url'];
	$level = isset($_POST['level'])?$_POST['level']:$_GET['level'];
	$name = isset($_POST['report_name'])?$_POST['report_name']:$_GET['report_name'];
	$user_id = isset($_POST['user_id'])?$_POST['user_id']:$_GET['user_id'];
	$report = isset($_POST['report'])?$_POST['report']:$_GET['report'];
	
	if(empty($id))
		$sql = "insert into users_reports (rdate,url,level,report_name,user_id,report) value ('$rdate','$url','$level','$name','$user_id','$report')";
	else
	{
		if($type =="delete")
			$sql = "delete from users_reports where id=$id";
		else if($type =="update")
			$sql = "update users_reports set url = '$url' ,report_name='$name' where id=$id";
	}
	
	mysql_query($sql);
	
	echo 1;
	die;
	
