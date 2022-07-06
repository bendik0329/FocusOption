<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

if(isset($_POST['base64'])){
	$base64 =  $_POST['base64'];
	
	list($type, $data) = explode(';', $base64);
	list(, $data)      = explode(',', $data);
	$data = base64_decode($data);
	$timestamp = time();
	$url = $_POST['url'];
	
		 if (!is_dir('../files/registrations')) {
			 mkdir('../files/registrations');
		 }
	$file =  '../files/registrations/'. $timestamp.'.png';
	file_put_contents($file, $data);
	echo "success";
	exit;
}
echo "error";
die;
?>