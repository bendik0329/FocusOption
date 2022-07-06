<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';

if (
    isset($_POST['action']) && 'pixel_activation' == $_POST['action'] && 
    isset($_POST['pixel_id']) && !empty($_POST['pixel_id']) && 
    is_numeric($_POST['pixel_id'])
) {
    require '../common/database.php';
    
    $strSql = 'UPDATE `pixel_monitor` 
              SET `valid` = (CASE WHEN `valid` = 1 THEN 0 ELSE 1 END)  
              WHERE `id` = ' . mysql_real_escape_string($_POST['pixel_id']) . ';';
    
    echo function_mysql_query($strSql,__FILE__,__FUNCTION__) ? '1' : '0';
    unset($strSql);
    
} elseif (
    isset($_POST['action']) && 
    'account_pixel_params_update' == $_POST['action'] && 
    isset($_POST['affiliate_id']) && !empty($_POST['affiliate_id']) && 
    is_numeric($_POST['affiliate_id']) && 
    isset($_POST['param']) && !empty($_POST['param'])
) {
    require '../common/database.php';
    
    $sql = 'SELECT `accounts_pixel_params_replacing` AS accounts_pixel_params_replacing '
            . 'FROM `affiliates` '
            . 'WHERE `id` = ' . mysql_real_escape_string($_POST['affiliate_id']) . ' '
            . 'LIMIT 0, 1;';
    
    $arrAffiliatePixelParams = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
	if(!empty($arrAffiliatePixelParams['accounts_pixel_params_replacing']))
    $arrAccountPixelParams   = explode(",",$arrAffiliatePixelParams['accounts_pixel_params_replacing']);
    
    unset($sql, $arrAffiliatePixelParams);
    
    if (isset($arrAccountPixelParams[$_POST['param']])) {
        //$arrAccountPixelParams[$_POST['param']]['value'] = empty($arrAccountPixelParams[$_POST['param']]['value']) ? 1 : 0;
    }
	else
		$arrAccountPixelParams[] = $_POST['param'];
    
    $sql = "UPDATE `affiliates` "
            . "SET `accounts_pixel_params_replacing` = '" . mysql_real_escape_string(implode(",",$arrAccountPixelParams)) . "' "
            . "WHERE `id` = " . mysql_real_escape_string($_POST['affiliate_id']) . ";";
    
    echo function_mysql_query($sql,__FILE__,__FUNCTION__) ? '1' : '0';
    unset($sql, $arrAccountPixelParams);
    
} elseif (
    isset($_POST['action']) && 
    'sale_pixel_params_update' == $_POST['action'] && 
    isset($_POST['affiliate_id']) && !empty($_POST['affiliate_id']) && 
    is_numeric($_POST['affiliate_id']) && 
    isset($_POST['param']) && !empty($_POST['param'])
) {
    require '../common/database.php';
    
    $sql = 'SELECT `sales_pixel_params_replacing` AS sales_pixel_params_replacing '
            . 'FROM `affiliates` '
            . 'WHERE `id` = ' . mysql_real_escape_string($_POST['affiliate_id']) . ' '
            . 'LIMIT 0, 1;';
    
    $arrAffiliatePixelParams = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
    if(!empty($arrAffiliatePixelParams['sales_pixel_params_replacing']))
	$arrSalePixelParams      = explode(",",$arrAffiliatePixelParams['sales_pixel_params_replacing']);
    
    unset($sql, $arrAffiliatePixelParams);
    
    if (isset($arrSalePixelParams[$_POST['param']])) {
        //$arrSalePixelParams[$_POST['param']]['value'] = empty($arrSalePixelParams[$_POST['param']]['value']) ? 1 : 0;
    }
	else
		$arrSalePixelParams[] = $_POST['param'];
    
	
    $sql = "UPDATE `affiliates` "
            . "SET `sales_pixel_params_replacing` = '" . mysql_real_escape_string(implode(",",$arrSalePixelParams)) . "' "
            . "WHERE `id` = " . mysql_real_escape_string($_POST['affiliate_id']) . ";";
    
    echo function_mysql_query($sql,__FILE__,__FUNCTION__) ? '1' : '0';
    unset($sql, $arrSalePixelParams);
    
} else {
    echo '0';
}

exit;
