<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';
require_once '../func/func_debug.php';
require_once '../func/func_string.php';


if (
    isset($_GET['merchant_id']) && !empty($_GET['merchant_id']) && is_numeric($_GET['merchant_id']) && 
    isset($_GET['affiliate_id']) && !empty($_GET['affiliate_id']) && is_numeric($_GET['affiliate_id'])
) {
    /* require '../common/database.php';
    require '../func/func_string.php'; */
    
    $sql = "SELECT IFNULL(`tier_type`, NULL) AS tier_type FROM `affiliates_deals` "
            . " WHERE 1 = 1 "
            . " AND `affiliate_id` = " . mysql_real_escape_string($_GET['affiliate_id']) 
            . " AND `merchant_id` = " . mysql_real_escape_string($_GET['merchant_id']) 
            . " ORDER BY `id` DESC "
            . " LIMIT 0, 1;";
    
    $arrResult = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
    
    if (is_array($arrResult) && !is_null($arrResult['tier_type'])) {
        $strCaption = '';
        
        switch ($arrResult['tier_type']) {
            case 'rev_share':
                $strCaption = 'Rev. Share';
                break;
            case 'cpl_count':
                $strCaption = 'CPL Count';
                break;
            case 'ftd_count':
                $strCaption = 'FTD Count';
                break;
            case 'ftd_amount':
            default:
                $strCaption = 'FTD Amount';
                break;
        }
        
        echo '<option value="' . $arrResult['tier_type'] . '" selected>' . lang($strCaption) . '</option>';
    
    } else {
        echo '<option value="ftd_amount" selected>' . lang('FTD Amount') . '</option>
            <!--option value="ftd_count">' . lang('FTD Count') . '</option>
            <option value="cpl_count">' . lang('CPL Count') . '</option>
            <option value="rev_share">' . lang('Rev. Share') . '</option-->';
    }
    
} else {
    echo '<option value="ftd_amount" selected>' . lang('FTD Amount') . '</option>
        <!--option value="ftd_count">' . lang('FTD Count') . '</option>
        <option value="cpl_count">' . lang('CPL Count') . '</option>
        <option value="rev_share">' . lang('Rev. Share') . '</option-->';
}

exit;

