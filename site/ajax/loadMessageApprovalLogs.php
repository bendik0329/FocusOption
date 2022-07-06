<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';

$advertiser_id = isset($_GET['advertiser']) ? $_GET['advertiser_id'] : 0;
$affiliate_id = isset($_GET['affiliate_id']) ? $_GET['affiliate_id'] : 0;
	
    if (isset($_GET['msg_id'])) {
        require '../common/database.php';
        
        $arrResult = array();
       /*  $strQuery  = "SELECT firedUrl AS URL, dateTime AS Date, pixelResponse AS pixelResponse "
                   . "FROM pixel_logs AS pl "
                   . "WHERE pixelCode = " . (int) $_GET['pixel_id'] . " "
                   . "ORDER BY dateTime DESC "
                   . "LIMIT 0, 40;"; */
				   
		$strQuery  = "select m.*, aff.username from affiliate_messages_approval m inner join affiliates aff  on m.affiliate_id = aff.id "
                   . " where " . (!empty($affiliate_id) ?  " m.affiliate_id = ". $affiliate_id : "") . (!empty($advertiser_id) ?  " m.advertiser_id = ". $advertiser_id : "") . " and  m.message_id=". $_GET['msg_id']  ." ORDER BY m.approval_date DESC "
                   . "LIMIT 0, 200;"; 

        
        $resource  = function_mysql_query($strQuery,__FILE__,__FUNCTION__);
        
        while ($arrRow = mysql_fetch_assoc($resource)) {
            $arrResult[] = $arrRow;
            unset($arrRow);
        }
        
        echo json_encode(array('success' => $arrResult));
        
    } else {
        echo json_encode(array('error' => lang('Missing Message ID')));
    }
    
    