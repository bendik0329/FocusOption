<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
if (
    isset($_POST['param'])   && !empty($_POST['param']) && 
    isset($_POST['id'])      && !empty($_POST['id'])    && 
    is_numeric($_POST['id']) && 
    isset($_POST['value'])   && !empty($_POST['value'])
) {
    require '../common/database.php';
    
    switch ($_POST['param']) {
        case 'doc_status':
            $sql = "UPDATE `documents` 
                    SET `doc_status` = '" . mysql_real_escape_string($_POST['value']) . "' 
                    WHERE `id` = " . $_POST['id'] . ";";
            
            echo @function_mysql_query($sql,__FILE__,__FUNCTION__) ? '1' : mysql_error();
            unset($sql);
            break;
    }
    
} else {
    echo 'Invalid parameters';
}

exit;
