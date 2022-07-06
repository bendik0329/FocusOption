<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';


if(isset($_POST['id'])){
	$sql = "select merchants from affiliates where id=" . $_POST['affiliate_id'];
	$merchants = mysql_fetch_assoc(mysql_query($sql));
	$arr_merchants = array();
	$merchants = $merchants["merchants"];
	
	if($_POST['checked'] == 1){				
				
				if($merchants!==""){
					$arr_merchants = explode("|",$merchants);
					if(!in_array($_POST['id'],$arr_merchants)){
						array_push($arr_merchants,$_POST['id']);
					}
				}
				else{
					array_push($arr_merchants,$_POST['id']);
				}
	}
	else if($_POST['checked'] == 0){
			$arr_merchants = explode("|",$merchants);
			if(in_array($_POST['id'],$arr_merchants)){
				$arr_merchants = array_flip($arr_merchants);			
				unset($arr_merchants[$_POST['id']]);
			}
	}
	
	$merchants = implode("|",$arr_merchants);
	$sql = mysql_query("update affiliates set merchants='" . $merchants . "' where id = " . $_POST['affiliate_id']);
	echo 1;
	die;
	
	
}
