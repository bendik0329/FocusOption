<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../common/database.php';
require_once '../func/func_mail.php';
require_once '../func/func_debug.php';
require_once '../smtp/PHPMailerAutoload.php';
require_once '../func/func_db.php';
/**
 * send verification email to affiliate
 */
 
if (isset($_POST['affiliate_id']) && !empty($_POST['affiliate_id'])) {
	
	sendTemplate('AffiliateEmailVerification',$_POST['affiliate_id'],0,'',0,0);
	
	echo 1;
	exit;
} else {
	echo 0;
	exit;
}