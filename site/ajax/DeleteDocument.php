<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
if (
    isset($_POST['id']) && !empty($_POST['id']) && 
    is_numeric($_POST['id']) && 
    isset($_POST['path']) && !empty($_POST['path'])
) { 
	try{
		if (!unlink($_POST['path'])) {
			echo 'Failed to delete the file';
			exit;
		}
	}
	catch(Exception $e){
		// In case file is not there.
	}
    
    require '../common/database.php';
    $sql = 'DELETE FROM `documents` WHERE `id` = ' . mysql_real_escape_string($_POST['id']) . ';';
    echo function_mysql_query($sql,__FILE__,__FUNCTION__) ? '1' : 'Failed to delete the database record';
    
} else {
    echo 'Invalid parameters';
}

exit;
