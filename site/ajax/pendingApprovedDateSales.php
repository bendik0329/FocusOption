<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../common/database.php';
require_once '../func/func_debug.php';

$rowid = $_GET['rowid'];
$type = $_GET['data_sale_type'];

require_once '../common/database.php';
require_once '../func/func_string.php';

if($type == 'pending')
{
	try{
		$sql = "select * from data_sales where id =$rowid limit 1";
		$res = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
		$record_merchand_id = $res['merchant_id'];
		$record_trader_id = $res['trader_id'];
		if (empty($res['id']))
				return false;
			
			
		$res['pendingRelationRecord'] =0;
		$res['created_by_admin_id'] = $_GET['admin_id'];
		
		if($res['original_amount'] == 0){
			$res['original_amount'] = $res['amount'];
		}
		
		$res['amount'] = $_GET['amount'];
		
		
		unset($res['id']);
		$fields = implode(',',array_keys($res));
		$values = implode(',', array_map(function($value) {
			if(!is_numeric($value)) {
				return '"' . $value . '"';
			} else {
				return $value;
			}
		}, array_values($res)));
		
		 $sql = "insert into data_sales_pending ($fields) values($values)";
		 // die ($sql);
		 $res = function_mysql_query($sql,__FILE__,__FUNCTION__);
		 // $lastIdRes = mysql_fetch_assoc(mysql_query("select max(id) as id from data_sales"));
		 $lastId = mysql_insert_id();
		 if (!empty($lastId)){
			 
			$res = function_mysql_query("update data_reg set 
`initialftddate` = '0000-00-00 00:00:00',
`initialftdtranzid`= '',
`lastProcessFTDDate`= '0000-00-00 00:00:00',
`ftdamount`= 0,
`FTDqualificationDate`= '0000-00-00 00:00:00',
`traderValue`= 0,
`lastTransactionRecordDate`= '0000-00-00 00:00:00' 
			where merchant_id = " . $record_merchand_id . " and trader_id = '" . $record_trader_id . "' ",__FILE__,__FUNCTION__);
			 
		 }
		 
// die('r1: ' . $lastId);

		 
		 
		 if(!empty($lastId)){
			 $sql = 'delete from data_sales where id = '.$rowid;
			 $res = function_mysql_query($sql,__FILE__,__FUNCTION__);
		 }
		 echo true;
	}
	catch(Exception $e){
		echo false;
	}
}	
else
{
	try{
            
			$sql = "select * from data_sales_pending where id =$rowid limit 1";
			$res = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
			if (empty($res['id']))
				return false;
			
			$res['pendingRelationRecord'] = 1;
			$res['created_by_admin_id'] = $_GET['admin_id'];
			
			if($res['original_amount'] == 0){
				$res['original_amount'] = $res['amount'];
			}
			
			$res['amount'] = $_GET['amount'];
			
                        $res_pixel = $res;
			
			
			unset($res['id']);
			$fields = implode(',',array_keys($res));
			$values = implode(',', array_map(function($value) {
                                if(is_null($value)){
                                    return 0;
                                }
				if(!is_numeric($value)) {
					return '"' . $value . '"';
				} else {
					return $value;
				}
			}, array_values($res)));
			
			 $sql = "insert into data_sales ($fields) values($values)";
			  
			 $res = function_mysql_query($sql,__FILE__,__FUNCTION__);
			 $lastId = mysql_insert_id();
			 
			 if(!empty($lastId)){
				 $sql = 'delete from data_sales_pending where id = '.$rowid;
				 $res = function_mysql_query($sql,__FILE__,__FUNCTION__);
                          
                            
                            try{
                                // Send pixel
                                include_once ('../pixel.php');
                                // transaction
                                $pixelurl = 'http://' . $_SERVER['HTTP_HOST'] . '/pixel.php?act=deposit&ctag=' . $res_pixel['ctag'] . '&trader_id=' . $res_pixel['trader_id'] . '&merchant_id=' . $res_pixel['merchant_id'] . '&tranz=' . $res_pixel['tranz_id'] . '&type=' . $res_pixel['type'] . '&currency=' . $res_pixel['currency'] . '&amount=' . $res_pixel['amount'] . '&subid=&debug=1';
                                $pixelContent = firePixel($pixelurl);
                                echo $pixelContent;
                            }catch(Exception $e ){
                                
                            }
                                 
			 }
			 echo '1';
	}
	catch(Exception $e){
	    echo false;
	}
}
die;

?>
