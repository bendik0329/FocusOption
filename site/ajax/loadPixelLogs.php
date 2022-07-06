<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
	
    if (isset($_GET['pixel_id'])) {
        require '../common/database.php';
        
        $arrResult = array();
       /*  $strQuery  = "SELECT firedUrl AS URL, dateTime AS Date, pixelResponse AS pixelResponse "
                   . "FROM pixel_logs AS pl "
                   . "WHERE pixelCode = " . (int) $_GET['pixel_id'] . " "
                   . "ORDER BY dateTime DESC "
                   . "LIMIT 0, 40;"; */
				   
		$strQuery  = "SELECT pl.id, firedUrl AS URL, dateTime AS Date, pixelResponse AS pixelResponse "
                   . "FROM pixel_logs AS pl LEFT JOIN pixel_monitor AS pm on pm.id=pl.pixelCode"
                   . " WHERE pl.pixelCode = " . (int) $_GET['pixel_id'] . " "
                   . "ORDER BY dateTime DESC "
                   . "LIMIT 0, 40;"; 
		
        
        $resource  = function_mysql_query($strQuery,__FILE__,__FUNCTION__);
        
        while ($arrRow = mysql_fetch_assoc($resource)) {
            $arrResult[] = $arrRow;
            unset($arrRow);
        }
        
        echo json_encode(array('success' => $arrResult));
        
    } else {
        echo json_encode(array('error' => 'Missing pixel ID'));
    }
    
    