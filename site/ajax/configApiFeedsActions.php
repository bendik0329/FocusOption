<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}
require_once '../func/func_debug.php';

require '../common/database.php';
if($_POST['status']=="on")
	$status = 1;
else
	$status = 0;

if(isset($_POST['id'])    && $_POST['id'] == 0 && $_POST['type']=="save"){
	
	$sql = "select count(*) as countDup from config_api_n_feeds where apiAccessType = '" . $_POST['apiAccessType']. "'";
	$checkww = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
	
	if(isset($checkww['countDup']) && $checkww['countDup'] ==0)
	{
		
		$sql = "insert into config_api_n_feeds (apiAccessType,apiToken,apiStaticIP,createdByUserId,status,outputType) values ('". $_POST['apiAccessType'] ."','". $_POST['apiToken'] ."','". $_POST['apiStaticIP'] ."','". $_POST['createdByUserID'] ."',". $status .",'". $_POST['outputType'] ."')";
		function_mysql_query($sql,__FILE__,__FUNCTION__);
		
		$lastid =  mysql_insert_id();
		if($lastid){
				echo json_encode(array('type'=>'Save','apiAccessType'=>$_POST['apiAccessType'],'apiToken'=>$_POST['apiToken'],'apiStaticIP'=>$_POST['apiStaticIP'],'id'=>$lastid));
		}
	}
	else{
		echo 2;
	}
	
}
elseif($_POST['type']=="update"){
	$strSql = 'UPDATE `config_api_n_feeds` 
              SET `apiAccessType` = "'. $_POST['apiAccessType'] .'",
				`apiStaticIP` = "'. $_POST['apiStaticIP'] .'",
				`apiToken`="'. $_POST['apiToken'] .'",
				`createdByUserId` = "'. $_POST['createdByUserID'].'",
				`outputType` = "'. $_POST['outputType'].'",
				`status` = "'. $status .'"
              WHERE `id` = ' . mysql_real_escape_string($_POST['id']) . ';';
	$d = function_mysql_query($strSql,__FILE__,__FUNCTION__)?1:0;
	if($d==1){
	echo json_encode(array('type'=>'update','apiAccessType'=>$_POST['apiAccessType'],'apiToken'=>$_POST['apiToken'],'apiStaticIP'=>$_POST['apiStaticIP'],'id'=>$id));    
	}
	else{
		echo 0;
	}
}
else if($_POST['type']=="delete"){
	$strSql = 'delete from `config_api_n_feeds` 
              WHERE `id` = ' . mysql_real_escape_string($_POST['id']) . ';';
	echo function_mysql_query($strSql,__FILE__,__FUNCTION__)?"deleted":0;
}

exit;
