<?php

@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}


require_once '../func/func_debug.php';
require_once '../func/func_global.php';
require_once '../common/database.php';
require_once '../func/func_string.php';
require_once '../admin/func/func_admins.php';

function isManagerCheck() {
	global $set,$_SESSION;
	$resulta=function_mysql_query("SELECT id,username,password FROM admins WHERE id='".$_SESSION['session_id']."' AND valid='1' AND level='manager'",__FILE__,__FUNCTION__);
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($_SESSION['session_id'] == $chk['id'] AND $session_serial == $_SESSION['session_serial']) {
		// if(((time() - $_SESSION['loggedin_time']) > $set->login_session_duration )){ 
		if(!isset($_SESSION['loggedin_time']) || (abs(time() - $_SESSION['loggedin_time']) < $set->login_session_duration *60)){
			$_SESSION['loggedin_time'] = time(); 
			return true; 
		} 
	}
	return false;
}

if (!isAdmin() && !isManagerCheck())
    die('Access Denied');


if ($_POST['action'] == 'add') {

    $error = false;

    if (!empty($_POST['merchant_id'])) {
        $merchant_id = $_POST['merchant_id'];
    }

    if (!empty($_POST['name'])) {
        $name = $_POST['name'];
    }

    if (!empty($_POST['value']) && $_POST['value'] > 0) {
        $value = $_POST['value'];
    }

    if (!empty($_POST['countries']) && count($_POST['countries']) > 0) {
        $countries_array = $_POST['countries'];
    }


    if (empty($name)) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill Name fields')
        ]);

        die();
    }

    if (empty($value)) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill Value fields')
        ]);

        die();
    }

    if (empty($countries_array)) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill Countries fields')
        ]);

        die();
    }

    if (empty($merchant_id)) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill MerchantID fields')
        ]);

        die();
    }


    foreach ($countries_array as $country) {

        $sql_find = 'SELECT countries FROM cpa_countries_groups WHERE countries LIKE "%' . mysql_real_escape_string($country) . '%" AND merchant_id = "' . (int) $merchant_id . '" LIMIT 1';
        $resource = function_mysql_query($sql_find, __FILE__, __FUNCTION__);
        if (mysql_fetch_assoc($resource)) {
            echo json_encode([
                'status' => 'error',
                'message' => lang('Country ' . $country . ' already exist.')
            ]);

            die();
        }

        $countries[] = $country;
    }

    if (empty($countries)) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill Countries fields')
        ]);

        die();
    }


    $sql = "INSERT INTO `cpa_countries_groups` (`admin_id`, `merchant_id`, `name`, `value`, `countries`) VALUES ('" . (int) $_SESSION['session_id'] . "', '" . (int) $merchant_id . "', '" . mysql_real_escape_string($name) . "', '" . abs((int) $value) . "', '" . implode('|', $countries) . "');";
    if (function_mysql_query($sql, __FILE__, __FUNCTION__)) {
        echo json_encode([
            'status' => 'ok',
            'data' => [
                'id' => mysql_insert_id(),
                'name' => $name,
                'currency' => $set->currency,
                'value' => $value,
                'countries' => implode(', ', $countries)
            ]
        ]);

        die();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => lang('General Error')
        ]);

        die();
    }
}

if ($_POST['action'] == 'delete') {

    if (empty($_POST['id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill Name fields')
        ]);

        die();
    }

    $sql = "DELETE FROM `cpa_countries_groups` WHERE  `id`='" . (int) $_POST['id'] . "' LIMIT 1";
    if (function_mysql_query($sql, __FILE__, __FUNCTION__)) {
        echo json_encode([
            'status' => 'ok',
            'message' => lang('Item has been deleted')
        ]);

        die();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => lang('General Error')
        ]);

        die();
    }
}

if ($_POST['action'] == 'update') {

    if (empty($_POST['id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill Name fields')
        ]);

        die();
    }

    if (!empty($_POST['name'])) {
        $name = $_POST['name'];
    }

    if (!empty($_POST['value']) && $_POST['value'] > 0) {
        $value = (int) $_POST['value'];
    }

    if (empty($name)) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill Name fields')
        ]);

        die();
    }

    if (empty($value)) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Please, fill Value fields')
        ]);

        die();
    }

    $sql = "UPDATE `cpa_countries_groups` SET `name`='" . mysql_real_escape_string($name) . "', `value` = '" . abs((int) $value) . "' WHERE  `id`='" . (int) $_POST['id'] . "' LIMIT 1";
    if (function_mysql_query($sql, __FILE__, __FUNCTION__)) {
        echo json_encode([
            'status' => 'ok',
            'message' => lang('Item has been deleted')
        ]);

        die();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => lang('General Error')
        ]);

        die();
    }
}


if ($_POST['action'] == 'affiliate-groups') {

    if (empty($_POST['affiliate_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Affiliate not found')
        ]);

        die();
    }

    if (empty($_POST['merchant_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Merchant not found')
        ]);

        die();
    }

    $affiliate_id = (int) $_POST['affiliate_id'];
    $merchant_id = (int) $_POST['merchant_id'];

    $my_country_list = [];
    $sql_find = 'SELECT * FROM cpa_countries_groups WHERE merchant_id = "' . $merchant_id . '"';
    $resource = function_mysql_query($sql_find, __FILE__, __FUNCTION__);
    while ($my_country = mysql_fetch_assoc($resource)) {
        
        $sql_find_my = 'SELECT * FROM cpa_group_delas WHERE group_id = "' . $my_country['id'] . '" AND merchant_id = "' . $merchant_id . '" AND affiliate_id = "' . $affiliate_id . '" ORDER BY rdate DESC LIMIT 1';
        $resource_my = function_mysql_query($sql_find_my, __FILE__, __FUNCTION__);
        $my_last_value = mysql_fetch_assoc($resource_my);
        
        $my_country_list[] = [
            'id' => $my_country['id'],
            'name' => $my_country['name'],
            'value' => !empty($my_last_value['value'])?$my_last_value['value']:$my_country['value'],
            'countries' => implode(', ', explode('|', $my_country['countries'])),
            'status' => (empty($my_last_value['value'])?0:1),
        ];
    }

    echo json_encode([
        'status' => 'ok',
        'data' => [
            'currency' => $set->currency,
            'groups' => $my_country_list,
        ],
    ]);
}

if ($_POST['action'] == 'affiliate-update-cpa-group') {

    if (empty($_POST['affiliate_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Affiliate not found')
        ]);

        die();
    }

    if (empty($_POST['merchant_id']) && $_POST['merchant_id'] != 0) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Merchant not found')
        ]);

        die();
    }

    if (empty($_POST['group_id'])) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Group not found')
        ]);

        die();
    }

    if (!isset($_POST['value']) || !is_numeric($_POST['value'])) {
        echo json_encode([
            'status' => 'error',
            'message' => lang('Comission must be more or equal 0')
        ]);

        die();
    }

    $affiliate_id = (int) $_POST['affiliate_id'];
    $merchant_id = (int) $_POST['merchant_id'];
    $group_id = (int) $_POST['group_id'];
    $value = abs((int) $_POST['value']);


    $sql_find = 'SELECT * FROM cpa_group_delas WHERE group_id = "' . $group_id . '" AND merchant_id = "' . $merchant_id . '" AND affiliate_id = "' . $affiliate_id . '" ORDER BY rdate DESC LIMIT 1';
    $resource = function_mysql_query($sql_find, __FILE__, __FUNCTION__);
    $my_last_value = mysql_fetch_assoc($resource);

    if (!empty($my_last_value)) {
        if ($my_last_value['value'] == $value) {

            echo json_encode([
                'status' => 'ok',
                'message' => lang('The value has not changed.')
            ]);

            die();
        }
    }

    $sql = "INSERT INTO `cpa_group_delas` (`admin_id`, `group_id`, `merchant_id`, `affiliate_id`, `value`) VALUES ('" . (int) $_SESSION['session_id'] . "', '" . (int) $group_id . "', '" . (int) $merchant_id . "', '" . (int) $affiliate_id . "', '" . abs((int) $value) . "')";
    if (function_mysql_query($sql, __FILE__, __FUNCTION__)) {
        echo json_encode([
            'status' => 'ok',
            'message' => lang('Comission has been updated')
        ]);

        die();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => lang('General Error')
        ]);

        die();
    }
}
