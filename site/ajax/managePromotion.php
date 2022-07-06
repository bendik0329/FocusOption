<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
require '../common/database.php';	
if(isset($_GET['act']) && $_GET['act'] == "switchToGeneral"){
		if(!empty($_GET['id'])){
			$sql = "update merchants_creative set promotion_id = 0 where promotion_id = " . $_GET['id'];
			function_mysql_query($sql,__FILE__,__FUNCTION__);
			echo 1;
		}
}
else{
	if(!empty($_GET['id'])){
	
		$sql = "select count(id) as cnt from merchants_creative where promotion_id = " . $_GET['id'];
		
		$val = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
		echo $val['cnt'];die;
	}
}
echo false;die;
?>