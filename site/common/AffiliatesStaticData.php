<?php 
$arrResult = array();

function getAllAffiliateStaticData($from, $to, $merchant_id, $affiliate_id){
	
	//global $set,$masterAffCommission;
				
				$sql = "select * from affiliates_static_data where rdate between '" . $from . "' and '" . $to ."' and merchant_id = " . $merchant_id . " and affiliate_id = " . $affiliate_id;
				//echo $sql . "<br/>"; 
				$res = function_mysql_query($sql, __FILE__ , __FUNCTION__);
				
				$arr = array();
				while($data = mysql_fetch_assoc($res)){
					$arrResult[$data["rdate"]][$data['key_name']] = $data['key_value'];
				}
	
		return $arrResult;
}


