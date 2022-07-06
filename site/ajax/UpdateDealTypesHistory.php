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
    isset($_POST['subject']) && !empty($_POST['subject']) &&
    isset($_POST['id'])      && !empty($_POST['id']) &&
    isset($_POST['value'])
 ) {
    
    if ($_POST['subject'] != 'rdate' && $_POST['subject'] != 'amount' && $_POST['subject'] != 'tier_pcpa'  && $_POST['subject'] != 'tier_amount')  {
        echo json_encode(array('error' => 'Cannot update ' . $_POST['subject']));
        exit;
    } elseif (!is_numeric($_POST['id'])) {
        echo json_encode(array('error' => 'Given ID (' . $_POST['id'] . ') not found'));
        exit;
    } elseif (strlen($_POST['value']) > 19) {
        echo json_encode(array('error' => 'Given value (' . $_POST['value'] . ') is not valid'));
        exit;
    }
    
    require '../common/database.php';
    $sql = "UPDATE affiliates_deals 
            SET " . $_POST['subject'] . ($_POST['value'] == 'NULL' ? ' = NULL' : " = '" . $_POST['value'] . "'") . " 
            WHERE id = " . $_POST['id'] . ";";

    if (!function_mysql_query($sql,__FILE__,__FUNCTION__)) {
        echo json_encode(array('error' => 'Server error took place'));
        exit;
    }
    
	if($_POST['subject'] == "tier_pcpa"){
		$subject = "PCPA";
	}
	else if($_POST['subject']=="tier_amount"){
		$subject = "Tier Amount";
	}
	else{
		$subject = $_POST['subject'];
	}
    echo json_encode(array(
        'success'   => 'Success, ' . ($_POST['subject'] == 'rdate' ? 'date' : $subject) . ' has been updated!',
        'new_value' => $_POST['value'] == 'NULL' ? 'Merchant Default' : $_POST['value'],
    ));
    exit;
    
 } else {
    echo json_encode(array('error' => 'Missing parameters'));
    exit;
 }
