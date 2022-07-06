<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
$id = isset($_POST['id']) ? $_POST['id'] : (isset($_GET['id']) ? $_GET['id'] : "");
if (isset($id) && !empty($id) && is_numeric($id)) {
    require '../common/database.php';
    $rsc = function_mysql_query("select * from affiliates_deals where id = ". $id,__FILE__,__FUNCTION__);
	$row = mysql_fetch_assoc($rsc);

/* $sql = 	
	"INSERT INTO  `affiliates_deals` (

`rdate` ,
`admin_id` ,
`merchant_id` ,
`affiliate_id` ,
`dealType` ,
`amount` ,
`tier_amount` ,
`tier_pcpa` ,
`tier_type`
)
VALUES (
 '".date('Y-m-d H:i:s')."',  ".$row['admin_id'].",  '".$row['merchant_id']."',  '".$row['affiliate_id']."',  'tier',  '0',  '0-0',  '0',  '".$row['tier_type']."'
);";
 */

	$sql = 	
									"INSERT INTO  `affiliates_deals` (`valid` ,`rdate` ,`admin_id` ,`merchant_id` ,`affiliate_id` ,`dealType` ,`amount` ,`tier_amount` ,`tier_pcpa` ,`tier_type`)VALUES (
									1, '".date('Y-m-d H:i:s',strtotime("-1 second"))."',  ".$row['admin_id'].",  '".$row['merchant_id']."',  '".$row['affiliate_id']."',  'tier',  '0',  '".$row['tier_amount']."',  '".$row['tier_pcpa']."',  '".$row['tier_type']."');";
							// function_mysql_query($sql);
							
							
// die ($sql);

	// $sql = 'DELETE FROM `affiliates_deals` WHERE `id` = ' . mysql_real_escape_string($id) . ';';
    if ( function_mysql_query($sql,__FILE__,__FUNCTION__) ) {
// mysql_query("update `affiliates_deals` set valid = 0 WHERE `id` = ". $id);
echo 	'1' ;
	}
else { 'Failed to delete the database record';
}
    
} else {
    echo 'Invalid parameters';
}

exit;
