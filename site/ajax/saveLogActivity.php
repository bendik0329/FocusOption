<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';


if(isset($_POST['user_id']) || isset($_GET['user_id'])){
	
	$id = isset($_POST['user_id'])?$_POST['user_id']:$_GET['user_id'];
	$rdate = date('Y-m-d h:i:s');
	$ip = isset($_POST['ip'])?$_POST['ip']:$_GET['ip'];
	$country = isset($_POST['country'])?$_POST['country']:$_GET['country'];
	$location = isset($_POST['location'])?$_POST['location']:$_GET['location'];
	$user_type = isset($_POST['userType'])?$_POST['userType']:$_GET['userType'];
	if((is_array($_POST['theChange']) || is_object($_POST['theChange'])) || (is_array($_GET['theChange']) || is_object($_GET['theChange']))){
		$theChange = json_encode(isset($_POST['theChange'])?$_POST['theChange']:$_GET['theChange']);
	}
	else{
		$theChange = isset($_POST['theChange'])?$_POST['theChange']:$_GET['theChange'];
	}
	$file_ = isset($_POST['_file_'])?$_POST['_file_']:$_GET['_file_'];
	
	if(!empty($file_)){
		
		$arrFile = explode("public_html",$file_);
		$file_ = $arrFile[1];
	}
	
	
	$function_ = isset($_POST['_function_'])?$_POST['_function_']:$_GET['_function_'];
	if(!isset($_POST['queryString']) || !isset($_GET['queryString']))
		$queryString = "";
	else
	$queryString = isset($_POST['queryString'])?$_POST['queryString']:$_GET['queryString'];
	
	
	$sql = "insert into logs_activity (rdate, ip,country,location,userType,user_id,theChange,_file_,_function_,queryString) value ('$rdate','$ip','$country','$location','$user_type','$id','$theChange','$file_','$function_','$queryString')";

	mysql_query($sql);
	
	echo 1;
	die;
	
	
}
else{
	echo "Missing required values";
}