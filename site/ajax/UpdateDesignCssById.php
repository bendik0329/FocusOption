<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
/**
 * Update deal-types history. 
 */
 if (
    isset($_POST['css']) && !empty($_POST['id'])
 ) {
    require '../common/database.php';
    $sql = "UPDATE design_css
            SET attribute_value = '" . $_POST['css'] . "'
            WHERE id = " . $_POST['id'] . ";";
    
    if (!function_mysql_query($sql,__FILE__,__FUNCTION__)) {
        echo json_encode(array('error' => 'Server error took place'));
        exit;
    }
    
    echo json_encode(array(
        'success'   => 'Success, value has been updated!'
    ));
    exit;
    
 } else {
    echo json_encode(array('error' => 'Missing parameters'));
    exit;
 }
