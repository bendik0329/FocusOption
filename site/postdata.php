<?php

	header("Pragma: no-cache");
	header("Expires: 0");
	set_time_limit(0);
	$debug_level = 1;
	require_once('common/database.php');
	$siteid = 0;
	$btagorg = 0;
	$campID = 0;
	
if($_REQUEST['isTest']){
	$url = (isset($_REQUEST['url']) ? $_REQUEST['url'] : '');
	function_mysql_query('INSERT INTO postback_logs (rdate, flag, merchant_id, text, ip, url) VALUES (NOW(), "green", -1, "Logged into postData", "'.$_SERVER['REMOTE_ADDR'].'", "'.$url.'")',__FILE__); //OR die(mysql_error());
	die('postData loaded!');
}	

if (isset($_REQUEST['error'])){
	if(isset($_REQUEST['url'])){
		$url = $_REQUEST['url'];
	}else{
		$url = '';
	}
	$url = str_replace('|HASHTAG|','#',$url);
	$url = str_replace('|AMP|','&',$url);
	function_mysql_query('INSERT INTO postback_logs (rdate, flag, merchant_id, text, ip, url) VALUES (NOW(), "green", -1, "'.$_REQUEST['error'].'", "'.$_SERVER['REMOTE_ADDR'].'", "'.$url.'")',__FILE__);
	die ('error: ' . $_REQUEST['error']);
}
	echo "
		<html>
		<head>
		<script type=\"text/javascript\">
		var a = (window.location.hash);
		if (a.length>0) {
		a = document.URL;
		a = a.replace(/#/g,'|HASHTAG|').replace(/&/g,'|AMP|');
		
		//console.log('http://network.affiliatebuddies.com/postdata.php?error=hashtagurl&url=' + a);
		window.location = 'http://network.affiliatebuddies.com/postdata.php?error=hashtagurl&url=' + a;
		
		}
		</script>
		</head>
		</html>
	";
	
	

	
	$pageURL = (@$_SERVER["HTTPS"] == "on") ? "https://" : "http://";
if ($_SERVER["SERVER_PORT"] != "80")
{
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
} 
else 
{
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
}


	$url = $pageURL; //'http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];
	
	//function_mysql_query('INSERT INTO');
	if ($debug_level==1) {
	
	function_mysql_query('INSERT INTO postback_logs (rdate, flag, merchant_id, text, ip, url) VALUES (NOW(), "green", -1, "PostBack called by unknown", "'.$_SERVER['REMOTE_ADDR'].'", "'.$url.'")',__FILE__);
	}

	
	
	//die ($url);
	if (false === strpos($url,'#'))  {
	}else{
		die('remove # character and try again');
	}
	

	
	
	function validateParam($str,$type,$isMust,$options=null,$default=null){
		global $siteid,$btagorg,$campID,$debug_level;

		
		if($str=='tags' AND !isset($_REQUEST['tags'])){
			$str = 'btag';
		}
		
		if((!isset($_REQUEST[$str]) OR $_REQUEST[$str]=='') AND $isMust){
			die('Missing parameter: '.$str);
		}else if((!isset($_REQUEST[$str]) OR $_REQUEST[$str]=='') AND !$isMust){
			return ($default!=null ? $default : '');
		}
		
	
		if($options!=null){
			
			$optCount=0;
			
			for($i=0;$i<count($options);$i++){
				
				//echo $_REQUEST[$str].' - '.$options[$i].'<BR>'.$optCount.'<BR><BR>';
				
				if($_REQUEST[$str]==$options[$i]){
					$optCount=1;
					break;
				}
			}
		
			if($optCount==0){
				die('Error 8371: Parameter "'.$str.'" Has wrong value');
			}
			
			
		
		}
		

		if($str=='btag' OR $str=='tags'){
			$btagvalue = $_REQUEST[$str];
			if (strpos($btagvalue,'|')>0) {
				$campID = explode('|campid-',$btagvalue);
				if ($campID==null)
					$campID = explode('|campID-',$btagvalue);
				if ($campID==null)
					$campID = explode('|CampID-',$btagvalue);
				$btagvalue = $campID[0];
				$campID=$campID[1];
			}
			
			if ($debug_level==2) {
				echo 'btag: ' . $btagvalue;
				echo 'campid: ' . $campID;
			}
			
			preg_match_all("/[Aa]+[0-9]*[-+]*b[0-9]*[+-]*p/", $btagvalue, $matches); 	
			//$btag= "";
			$btagcheck= $matches[0][0];
			if (isset($btagcheck) AND $btagcheck!=null AND $btagcheck!='')  {
				$btag=$btagvalue;
			}else{
				$btag = $default;
			}
		
	
	if ($debug_level==2) {echo '<br><br>btag2: ' . $btag . '<br>';	}
			$btag  = str_replace('+','-',$btag);
			$btagorg = $btag;
if ($debug_level==2) {echo '<br><br>btag2.5: ' . $btag . '<br>';	}	
		
		if (strpos($btag,'ampID')) { 
		$btag  = explode('ampID',$btag);
			$btag = substr($btag[0],0,-2);
			}
if ($debug_level==2) {echo '<br><br>btag3: ' . $btag . '<br>';	}				
			
			//SITEID IS BANNER ID
			preg_match_all("/[-+]*b[0-9]*/", $_REQUEST[$str], $matches); 	
			$siteid =  $matches[0][0];
			$siteid = str_replace('+b','',$siteid);
			$siteid = str_replace('-b','',$siteid);
			if ($siteid=='')
				$siteid=0;
			
			if ($debug_level==2) {
			echo '<br><br>btag end of fun: ' . $btag . '<br>';
			}
			return mysql_real_escape_string(filter_var($btag,FILTER_SANITIZE_STRING));
			

		}
			
		
			
			
		switch($type){
			case 'string': 	return mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_SANITIZE_STRING));	break;
			case 'int': 	return mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_VALIDATE_INT));		break;
			case 'float': 	return mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_VALIDATE_FLOAT));		break;
			case 'email': 	return mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_VALIDATE_EMAIL));		break;
			
		}
		
		
	}
	
	// create guid function
	function guid(){
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}else{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
					.substr($charid, 0, 8).$hyphen
					.substr($charid, 8, 4).$hyphen
					.substr($charid,12, 4).$hyphen
					.substr($charid,16, 4).$hyphen
					.substr($charid,20,12)
					.chr(125);// "}"
			return $uuid;
		}
	}
	$guidparam = guid();
	$guidparam = str_replace('{','',str_replace('}','',$guidparam));
// end create guid function
	
	

	$username = 	validateParam('username','string',1);
	$password = 	validateParam('password','string',1);
	$action = 		validateParam('action','string',1,['lead','reg','sale']);

	//echo 'SELECT sites.merchant_id as id merchants.name as name FROM sites inner join merchants on metchants.id = sites.merchant_id  WHERE sites.username="'.$username.'" AND sites.password="'.$password.'" AND valid=1';
	$merchant = mysql_fetch_assoc(function_mysql_query('SELECT sites.merchant_id as id, merchants.name as name FROM sites inner join merchants on merchants.id = sites.merchant_id WHERE sites.username="'.$username.'" AND sites.password="'.$password.'" AND sites.valid=1',__FILE__));
//	SELECT sites.merchant_id as id merchants.name as name FROM sites inner join merchants on metchants.id = sites.merchant_id  WHERE sites.username="'.$username.'" AND sites.password="'.$password.'" AND valid=1'));
	
	//$merchant['name'] = mysql_fetch_assoc(function_mysql_query('SELECT name FROM merchants WHERE id="'.$merchant['id'],__FILE__));
	
	if ($debug_level>1) {
	echo 'SELECT id,site AS name FROM sites WHERE username="'.$username.'" AND password="'.$password.'" AND valid=1';
	}
	
	if(!$merchant)
		die('Error 1108: Username or password incorrect.');
	//$merchant = Array();
	//$merchant['name'] = 'test';
	//$merchant['id'] = 0;
	
	
	
	if($action=='reg'){
		$memberStatusOptions = ['active','demo'];
	}else{
		$memberStatusOptions = ['lead','active','demo'];
	}
	

	if ($merchant['id']=='2ghrfg9') { //plus500 integration
		
		// $memberStatus = validateParam('memberStatus','string',0,null,'active');
		// $btag = 		validateParam('tags','string',1,null,'a1524-b-p');
		// $oid = 		validateParam('oid','string',0,null,null);
		// if ($campID!=0) {
			// validateParam('campID','string',0,null,null);
		// }
		// if($campID==null){
	
	//	_CampID-BFP_P5-pidguid
			// $btagEx = explode('ampID-',($btagorg));
			// $btagExFinal = explode('-',($btagEx[1]));
			// $campID = $btagExFinal[0];
		// }
		// if($oid==null){
	
	//	-CampID-BFP_P5-pidguid
			// $btagEx = explode('ampID-',($btag));
			// $btagExFinal = explode('-',($btagEx[1]));
			// $oid = $btagExFinal[1];
		// }
		
		// $rdate = 		validateParam('rdate','string',0,null,date("Y-m-d"));
		// $gmt = 			validateParam('gmt','string',0);
		// $traderID = 	validateParam('oid','string',0,null,$oid);
		// $traderAlias = 	validateParam('trader_alias','string',0,null,$traderID);
		// $tranzID = 		validateParam('tranzID','string', 0,null,guid());
		// $type = 		validateParam('type','string', 0,['bonus','deposit','withdrawal','revenue']);
		// $value = 		validateParam('value','string',0,null,100);		
		// $pnl = 			validateParam('pnl','string',0);
		// $coin = 		validateParam('currency','string',0);
		// $status = 		validateParam('status','string',0,['approved','cancelled','pending']);
		// $phone = 		validateParam('phone','string',0);
		// $mail = 		validateParam('mail','email',0);
		// $country = 	validateParam('country','string',0);
		

	} else { 
		$merchant_id =  $merchant['id'];
		$memberStatus = validateParam('memberStatus','string',1,$memberStatusOptions);
		$btag = 		validateParam('btag','string',($action=='reg' ? 1 : 0),null,'a1524-b-p');
		if ($campID!=0) {
			validateParam('campID','string',1,null,null);
		}
		$rdate = 		validateParam('rdate','string', 0,null,date("Y-m-d"));
		$gmt = 			validateParam('gmt','string',0);
		$traderID = 	validateParam('trader_id','string',($action!='lead' ? 1 : 0));
		if ($debug_level==2) { 
		echo '1'. ($traderID). '<br>';}
		//$traderAlias = 	validateParam('trader_alias','string',($action!='lead' ? 0 : 0));
		$traderAlias = 	validateParam('trader_alias','string',0,null,$traderID);
		if ($debug_level==2) { 
		echo '2'. ($traderID). '<br>';}
		$tranzID = 		validateParam('tranzID','string',($action=='sale' ? 1 : 0));
		$type = 		validateParam('type','string',($action=='sale' ? 1 : 0),['bonus','deposit','withdrawal','revenue']);
		$value = 		validateParam('value','string',($action=='sale' ? 1 : 0));
		/*$currency = 	validateParam('currency','string',($action=='sale' ? 1 : 0));*/
		$pnl = 			validateParam('pnl','string',0);
		$coin = 		validateParam('currency','string',($action=='sale' ? 1 : 0));
		$status = 		validateParam('status','string',($action=='sale' ? 1 : 0),['approved','cancelled','pending']);
		$phone = 		validateParam('phone','string',0);
		$mail = 		validateParam('mail','email',0);
		$country = 		validateParam('country','string',0);
	}
	
	echo '<style>
		body {
			font-size:14px;
			font-family:Tahoma;
			font-weight:solid;
			color:#000
		}
	</style>';
	
	
	if($coin!='' AND $coin!=null AND $value>0){
		if(strtoupper($coin)=='USD'){
			$usdAmount = $value;
		}else{
			$curr = mysql_fetch_assoc(function_mysql_query('SELECT val FROM exchange_rates WHERE fromCurr="'.strtoupper($coin).'" AND toCurr="USD"',__FILE__));
			if($curr['val'] AND $curr['val']>0){
				$usdAmount = number_format((float)($value*$curr['val']), 2, '.', '');
				//die($usdAmount);
			}else{
				$usdAmount = 0;
				echo '<div style="font-weight:bold; padding:10px; background:RED; color:#fff">WARNING 1923: NO CURRENCY WAS FOUND, USD AMOUNT WILL COUNT AS 0.</div>';
			}
		}
	}else{
		$usdAmount = 0;
	}
	
	
	
	if ($debug_level==2) {	
	//function_mysql_query('INSERT INTO');
	function_mysql_query('INSERT INTO postback_logs (rdate, flag, merchant_id, text, ip, url) VALUES (NOW(), "green", '.$merchant['id'].', "PostBack called by '.$merchant['name'].'", "'.$_SERVER['REMOTE_ADDR'].'", "http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI].'")',__FILE__);
	}

	//$rdate = strtotime($_REQUEST[$str]);
	
	
	try{
		$rdate = new DateTime($rdate);
		$rdate = date_format($rdate,'Y-m-d');
	}catch(Exception $e){
		die('Error 1109: Date format is not currect');
	}
	
	$exist = 0;
	if($action=='lead' OR $action=='reg'){
		
		$chk = mysql_fetch_assoc(function_mysql_query('SELECT id FROM datascan_reg WHERE trader_id='.$traderID.' AND merchant_id='.$merchant_id,__FILE__));
		if($chk['id']){
		
			$exist = 1;
			echo '<BR><BR>Trader exist! [traderID: '.$traderID.']<BR><BR>'. date(DATE_RFC2822);
			
		}
	}else if($action=='sale'){
		
		$chk = mysql_fetch_assoc(function_mysql_query('SELECT id FROM datascan_sales WHERE trader_id="'.$traderID.'" AND tranz_id="'.$tranzID.'" AND merchant_id='.$merchant_id,__FILE__));
			
		
		if($chk['id']){
			$exist = 1;
			echo '<BR><BR>Tranz exist! -- traderID: '.$traderID.'  |  tranzid: '.$tranzID.'<BR><BR>'. date(DATE_RFC2822);
			
		}
	}
	if($exist!=1){
		if($action=='lead'){
			//echo 'INSERT INTO reg_'.$merchant['name'].' (rdate, btag, trader_id, TraderAlias, MemberStatus, email, phone, country) VALUES ("'.$rdate.'","'.$btag.'","'.$traderID.'","'.$traderAlias.'","'.$memberStatus.'","'.$email.'","'.$phone.'","'.$country.'")';
			function_mysql_query('INSERT INTO datascan_reg (rdate, btag, trader_id, TraderAlias, MemberStatus, email, phone, country,merchant_id) VALUES ("'.$rdate.'","'.$btag.'","'.$traderID.'","'.$traderAlias.'","'.($action=='lead' ? 'lead' : 'active').'","'.$email.'","'.$phone.'","'.$country.'",'.$merchant['id'].')',__FILE__);
			echo '<BR><BR>Done - Lead inserted! [traderID: '.$traderID.']<BR><BR>'. date(DATE_RFC2822);
		}
		if ($debug_level==2) { 
		echo '3'. ($traderID) . '<br>';}
		if($action=='reg'){
		
		if ($debug_level==2) { 	
			echo '4'. ($traderID). '<br>';}
			//die('INSERT INTO reg_'.$merchant['name'].' (rdate, btag, trader_id, TraderAlias, MemberStatus, email, phone, country'.($merchant['id']==29 ? ',campID' : '').',siteID) VALUES ("'.$rdate.'","'.$btag.'","'.$traderID.'","'.$traderAlias.'","'.($action=='lead' ? 'lead' : 'active').'","'.$email.'","'.$phone.'","'.$country.'"'.($merchant['id']==29 ? ',"'.$campID.'"' : '').','.$siteid.')');
			function_mysql_query('INSERT INTO datascan_reg (rdate, btag, trader_id, TraderAlias, MemberStatus, email, phone, country'.($merchant['id']==29 ? ',campID' : '').',siteID,merchant_id) VALUES ("'.$rdate.'","'.$btag.'","'.$traderID.'","'.$traderAlias.'","'.($action=='lead' ? 'lead' : 'active').'","'.$email.'","'.$phone.'","'.$country.'"'.($merchant['id']==29 ? ',"'.$campID.'"' : '').','.$siteid.','.$merchant['id'].')',__FILE__);
			echo '<BR><BR>Done - registrar inserted! [traderID: '.$traderID.']<BR><BR>'. date(DATE_RFC2822);
			if ($debug_level==2) {  echo '5'. ($traderID). '<br>';} 
		}
		
		if($action=='sale'){
			//echo 'INSERT INTO sales_'.$merchant['name'].' (rdate,tranz_id,btag,trader_id,type,value,usdAmount,oldPNL,coin,status,siteID,sent_ia) VALUES ("'.$rdate.'","'.$transactionID.'","'.$btag.'","'.$traderID.'","'.$type.'","'.$value.'","'.$usdAmount.'","'.$pnl.'","'.$coin.'","'.$status.'",'.$siteid.',0)';
			function_mysql_query('INSERT INTO datascan_sales (rdate,tranz_id,btag,trader_id,type,value,oldPNL,coin,status,siteID,sent_ia'.($merchant['id']==29 ? ',campID' : '').',merchant_id) VALUES ("'.$rdate.'","'.$tranzID.'","'.$btag.'","'.$traderID.'","'.$type.'","'.$value.'","'.$pnl.'","'.$coin.'","'.$status.'",'.$siteid.',0'.($merchant['id']==29 ? ',"'.$campID.'"' : '').','.$merchant['id'].')',__FILE__); //OR die(mysql_error());
			// with "usdAmount
			//function_mysql_query('INSERT INTO sales_'.$merchant['name'].' (rdate,tranz_id,btag,trader_id,type,value,usdAmount,oldPNL,coin,status,sent_ia) VALUES ("'.$rdate.'","'.$transactionID.'","'.$btag.'","'.$traderID.'","'.$type.'","'.$value.'","'.$usdAmount.'","'.$pnl.'","'.$coin.'","'.$status.'",0)',__FILE__) OR die(mysql_error());
			echo '<BR><BR>Done - sale inserted! [transactionID: '.$tranzID.']<BR><BR>' . date(DATE_RFC2822);
		}
	}
	
	die('<BR><BR><BR><BR><div class="ver1.1" style="background:BLUE; color:#fff; font-weight:bold; padding:10px; font-size:18px">Process is done!</div><BR><BR><BR><BR><BR><BR><BR>');
	
?>