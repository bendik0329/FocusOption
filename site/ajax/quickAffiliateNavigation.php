<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';

$id = $_POST['aff_id'];
$type = $_POST['nav_type'];

$html = "";

if($type == "first")
	$sql = "SELECT id as aff_id from affiliates  order by id asc limit 1";
else if($type == "next")
	$sql = "SELECT id as aff_id from affiliates where id > ". $id ."  order by id asc limit 1";
else if($type == "prev")
	$sql = "SELECT max(id) as aff_id from affiliates where id < ". $id ."  order by id desc limit 1";
else if($type == "last")
	$sql = "SELECT max(id) as aff_id from affiliates order by id desc limit 1";

$qq = mysql_query($sql);
$data=mysql_fetch_assoc($qq); 
echo $data['aff_id'];
die;
