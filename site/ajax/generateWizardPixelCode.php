<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
require_once '../func/func_global.php';
require_once '../common/database.php';
require_once '../func/func_string.php';

if(isset($_POST['pixel_url']) && !empty($_POST['pixel_url'])){
	
		$url = explode("?",$_POST['pixel_url']);
		$params = array();
		if(isset($url[1]))
		$params = explode("&",$url[1]); 
		
		$arrftdParams=array(
		'{p1}'=>lang('Dynamic Parameter (p1)') ,
		'{p2}'=> lang('Dynamic Parameter2 (p2)'),
		'{ctag}' =>lang('Campaign Parameter'),
		'{trader_id}'=>lang('Trader ID'),
		'{tranz}'=>lang('Transaction ID'),
		'{type}'=>lang('Type of the account'),
		"{currency}"=>lang('Account Currency'),
		"{amount}"=>lang('Amount of the transaction'),
		"{affiliate_id}"=>lang('Affiliate ID'),
		"{ip}"=>lang("IP address of traders click"),
		"{uid}"=>lang('Unique ID')
		);
		
		if(isset($_POST['affiliate_id'])){
			$sql = 'SELECT `accounts_pixel_params_replacing` AS accounts_pixel_params_replacing, '
        . '`sales_pixel_params_replacing` AS sales_pixel_params_replacing '
        . 'FROM `affiliates` '
        . 'WHERE `id` = ' . mysql_real_escape_string($_POST['affiliate_id']) . ' '
        . 'LIMIT 0, 1;';
			$arrAffiliatePixelParams = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
			$activeAccountPixels ="";
			$activeSalesPixels ="";
			if (!empty($arrAffiliatePixelParams)) { 
				$activeAccountPixels =  explode(",",$arrAffiliatePixelParams['accounts_pixel_params_replacing']);
				$activeSalesPixels =  explode(",",$arrAffiliatePixelParams['sales_pixel_params_replacing']);
			}
			
			$accountPixels = array(
			"ctag"=>"Trader ID from the platform",
			"trader_id"=>"Campaign Parameter",
			"trader_alias"=>"Username",
			"type"=>"Type of the account",
			"affiliate_id"=>"Affiliate ID",
			"ip"=>"IP address of traders click",
			"uid"=>"AffiliateTS user internal click id",
			"dynamic_parameter"=>"Dynamic Parameter"
			);
			$salesPixels = array(
			"ctag"=>"Campaign Parameter",
			"trader_id"=>"Trader ID from the platform",
			"tranz"=>"Transaction ID",
			"type"=>"Type of the account",
			"currency"=>"Account Currency",
			"amount"=>"Amount of the transaction",
			"affiliate_id"=>"Affiliate ID",
			"uid"=>"AffiliateTS unique user internal click id",
			"dynamic_parameter"=>"Dynamic Parameter"
			);
	
			$arrAccountPixelParams     = $accountPixels;
			$arrSalePixelParams        =  $salesPixels;

				foreach ($arrAccountPixelParams as $k => $v) {
						if (!empty($v)) {
							$key = "{". $k ."}";
							$arrftdParams[$key] = lang($v);
						}
					}
				
				
				if ($arrSalePixelParams)
				foreach ($arrSalePixelParams as $k => $v) {
						if (!empty($v)) {
							$key = "{". $k ."}";
							if(!array_key_exists($key,$arrftdParams)){
							$arrftdParams[$k] = lang($v);
							}
						}
					}
		}
	
		
		//replace {} with value
		preg_match_all('/{(.*?)}/', $url[0], $amatches);
		preg_match_all('/\[[^\]]*\]/', $url[0], $bmatches);
		$params2 = array();
		if(!empty($amatches[0])){
			foreach($amatches[0] as $ke=>$p){
			$params2[] = $p;
			}
		}
		if(!empty($bmatches[0])){
			foreach($bmatches[0] as $ke=>$p){
			$params2[] = $p;
			}
		}
		
	
		
		$param_len = count($params) + count($params2);
		
		if(empty($params))
		{
			$params = $params2;
		}
		if(!empty($params)){
			
			$params = array_merge($params2,$params);
			
			
			$str = "<table style='margin:0 auto'>";
			
			foreach($params as $key => $obj){
					$param = explode("=",$obj);
					
					if(count($param) > 1){
						$cnt= $key+1;
						$str .= "<tr data-count=".  $cnt ."><td><b>". $param[0] ."</b></td><td>
						<select name='opts' onchange='replaceSelectedParam(this)' data-val='". $param[1] ."'>";
						if(!array_key_exists($param[1],$arrftdParams)){
							$str .= "<option value='". $param[1] ."' selected>". $param[1] ."</option>";
						}
						foreach($arrftdParams as $k=>$val){
							if($k == $param[1]){
								$str .= "<option value='". $k ."' selected>". $val ."</option>";
							}
							else{
								$str .= "<option value='". $k ."'>". $val ."</option>";
							}
						}
						$str .="</select>
						
						
						
						</td></tr>";
				}
				else{
						$cnt= $key+1;
						$str .= "<tr data-count=".  $cnt ."><td><b>". $obj ."</b></td><td>
						<select name='opts' onchange='replaceSelectedParam(this)' data-val='". $obj ."'>";
						if(!array_key_exists($obj,$arrftdParams)){
							$str .= "<option value='". $obj ."' selected>". $obj ."</option>";
						}
						foreach($arrftdParams as $k=>$val){
							if($k == $obj){
								$str .= "<option value='". $k ."' selected>". $val ."</option>";
							}
							else
								$str .= "<option value='". $k ."'>". $val ."</option>";
						}
						$str .="</select>
						</td></tr>";
					
				}
			}
			
			$str .= "</table>";
			
			echo $param_len . "~~" . $str;die;
			
		}
		
}

?>
