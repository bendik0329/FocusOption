<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
require '../common/database.php';
require '../common/config.php';
require '../func/func_string.php';
require '../func/func_file.php';
if ( 
    isset($_POST['pic']) && $_POST["avatar"] == 1
) {
   
		$sql = "select bigPic from admins where id=" . $_POST['user_id'];
		$data = mysql_fetch_assoc(mysql_query($sql));
		
		if($data){
			if(!empty($data["bigPic"]) && strpos($data["bigPic"],"files")){
				unlink($data["bigPic"]);
			}
		}
    
	$bigPic = "../images/avatars/" . $_POST['pic'];
	
	$sql = "update admins set bigPic = '" . $bigPic . "' where id=" . $_POST['user_id'];
	mysql_query($sql);
	echo $bigPic;
	exit;
	
    
}
else if( isset($_FILES['profile_img']['name'])){
	
	
	if (chkUpload('profile_img')) {
		
			$randomFolder =mt_rand(10000000, 99999999);
			$folder = '../files/managers/tmp/' . $randomFolder ."/";
			 if (!is_dir('../files/managers/tmp')) {
				 mkdir('../files/managers/tmp');
			 }
			 if (!is_dir($folder)) {
				 mkdir($folder);
			 }
			
			$bigPic = UploadFile('profile_img', '5120000', 'jpg,gif,swf,jpeg,png', '', $folder);
			if($bigPic== ""){
				echo "2";
				die;
			}
			//$bigPic = UploadFile('profile_img','5120000','jpg,gif,png','','../files/managers/');
			fixPic($bigPic,'100','170','',1);
		
			$sql = "update admins set bigPic = '" . $bigPic . "' where id=" . $_POST['user_id'];
			mysql_query($sql);
			//echo $bigPic;
			echo "../images/wheel.gif";
			exit;
		
	}
	
}
//remove image
else{
		
		$sql = "select bigPic from admins where id=" . $_POST['user_id'];
		$data = mysql_fetch_assoc(mysql_query($sql));
		
		if($data){
			if(!empty($data["bigPic"]) && strpos($data["bigPic"],"files")){
			unlink($data["bigPic"]);
			}
		}
		
		$sql = "update admins set bigPic = '' where id=" . $_POST['user_id'];
		mysql_query($sql);
		echo 1;
		exit;
}

echo json_encode(['error' => 'Validation failed']);
exit;
