<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';

require('../common/database.php');

if (isset($_GET['affiliate_id']) && !empty ($_GET['affiliate_id'])) {
    $affiliate_id = $_GET['affiliate_id'];
    $profileqq    = function_mysql_query('SELECT id, name, url FROM affiliates_profiles WHERE affiliate_id = '.$affiliate_id.' AND valid = 1 ORDER BY id ASC',__FILE__,__FUNCTION__);
    $arrResult    = array(
        array(
            'value' => '',
            'text'  => 'Choose profile',
        )
    );
    
    while ($profileww = mysql_fetch_assoc($profileqq)) {
        $arrResult[] = array(
            'value' => $profileww['id'],
            'text'  => $profileww['id'] . ' ' . $profileww['name'].' - '.$profileww['url'],
        );
    }					 
    
    echo json_encode(array('success' => $arrResult));
}

