<?php

chdir('../');
require_once('common/global.php');
$debug =2 ;
$ip = $_SERVER['REMOTE_ADDR'];
if (!($ip == '212.199.106.82') and !($ip=='212.143.60.30') ){ 
	
	die ('IP ' .$ip. ' is not approved');
}


	function validateParam($str,$type,$isMust,$options=null,$default=null){
		
		if((!isset($_REQUEST[$str]) OR $_REQUEST[$str]=='') AND $isMust){
			die('Missing parameter: '.$str);
		}else if((!isset($_REQUEST[$str]) OR $_REQUEST[$str]=='') AND !$isMust){
			return ($default!=null ? $default : '');
		}
		
		
		if($options!=null){
			$optCount=0;
			for($i=0;$i<count($options);$i++){
				if($_REQUEST[$str]==$options[$i]){
					$optCount=1;
					break;
				}
			}
			if($optCount==0){
				die('Error 8371: Parameter "'.$str.'" Has wrong value');
			}
		}
		

		
		if($str=='btag' OR $str=='tags' OR $str=='subCampaign'){
			
			preg_match_all("/[Aa]+[0-9]*[-+]*b[0-9]*[+-]*p/", $_REQUEST[$str], $matches); 	
			//$btag= "";
			 $btagcheck= $matches[0][0];
			
			if (isset($btagcheck) AND $btagcheck!=null AND $btagcheck!='')  {
				$btag=$_REQUEST[$str];
			}else{
				$btag = $default;
			}
			$t =  mysql_real_escape_string(filter_var($btag,FILTER_SANITIZE_STRING));
			//die ($t);
			return $t;
		}
		
		
		
		
		if($str=='country'){
			$str = mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_SANITIZE_STRING));
			$str = mysql_fetch_assoc(mysql_query('SELECT spotCode FROM countries WHERE valid=1 AND (code="'.$str.'" OR title="'.$str.'" OR spotCode="'.$str.'")'));
			
			if(!$str){
				die('Error 8372: Country parameter is invalid');
			}else{
				return $str['spotCode'];
			}
		}
			
		
		
		
		switch($type){
			case 'string': 	return mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_SANITIZE_STRING));	break;
			case 'int': 	return mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_VALIDATE_INT));		break;
			case 'float': 	return mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_VALIDATE_FLOAT));		break;
			case 'email': 	return mysql_real_escape_string(filter_var($_REQUEST[$str],FILTER_VALIDATE_EMAIL));		break;
		}

	}
	
	
	
	
	function setSubParam($arr, $title, $val){
		$obj = new stdClass();
		$obj->title = $title;
		$obj->val = $val;
		array_push($arr,$obj);
		return $arr;
	}
	
	
	function setSubParamStr($sb){
		$str = '';

		foreach($sb AS $row){
			
			$title = $row->title.'';
			$val = $row->val.'';
			if ($title=='currency') {
			$str=$title.'='.$val. $str;
			
			}
			else
			$str.='&'.$title.'='.$val;
		}
		
		return $str;
	}
	
	
	// validateParam	(	paramName,	type[string,int,float,email],	isMust[1,0],	options[Array],		defaultValue)
	
	$url =					'http://api-spotplatform.no1options.com/Api';
	
	//$api_username = 		validateParam('api_username','string',1);
	//$api_password = 		validateParam('api_password','string',1);	
	$api_username = 		'24winner2no1';
	$api_password = 		'5496ceee2cb2c';
	$campaignID = 			validateParam('campaignId','string',1);
	
	
	
	//$action = 				validateParam('action','string',1,['view','validate','addLead','getAuth','checkDeposits']);
	$subParams = Array();
	
	
	switch($action){
	
		case 'addCustomer':

		
			$subParams = setSubParam($subParams,'api_username',$api_username);
			$subParams = setSubParam($subParams,'api_password',$api_password);
			$subParams = setSubParam($subParams,'campaignId',$campaignID);
			
			
			$subParams = setSubParam($subParams,'MODULE','Customer');
			$subParams = setSubParam($subParams,'COMMAND','add');
			$subParams = setSubParam($subParams,'FirstName',validateParam('firstName','string',1));
			$subParams = setSubParam($subParams,'LastName',	validateParam('lastName','string',1));
			$subParams = setSubParam($subParams,'email',	validateParam('email','email',1));
			$subParams = setSubParam($subParams,'password',	validateParam('password','string',1));
			$subParams = setSubParam($subParams,'Country',	validateParam('country','string',1));
			$subParams = setSubParam($subParams,'LastName',	validateParam('lastName','string',1));
			$subParams = setSubParam($subParams,'subCampaign',	validateParam('subCampaign','string',1));
			$subParams = setSubParam($subParams,'currency',	validateParam('currency','string',1));
			$subParams = setSubParam($subParams,'a_id',	'4c1784efdcccb');
			$subParams = setSubParam($subParams,'birthday',	validateParam('birthday','string',1));
			$subParams = setSubParam($subParams,'Phone',	validateParam('phone','string',1));
			
			//MODULE=Customer&COMMAND=add&FirstName=Test&LastName=Customer&email=test1@SpotOption.com&password=123456&Country=226&campaignId=7&subCampaignId=45&currency=USD&a_aid=4c1784efdcccb&birthday=1980-07-21

			
			//$gotoDeposit = validateParam('gotoDeposit','int',0,null,0);
			//$url.= 'api_username='.$api_username.'&api_password='.$api_password.'&campaignId='.$campaignID.'&' ;
			//$url.=setSubParamStr($subParams);
			
			//die ($url);
			
			if ($debug==1)
			{
				echo '<br>'.$url . '<br>';
				die('debug!');
			}
//			die ($url);
 
			$xml_report = doPostCurl($url,$subParams);
			
			//var_dump($xml_report);
			//die('done');
			
			
			$status = getTag('<operation_status>','<\/operation_status>',$xml_report);
			$status2 = getTag('<connection_status>','<\/connection_status>',$xml_report);
			
			if($status=='failed' OR $status2=='failed'){
				$error = getTag('<error>','<\/error>',$xml_report);

				echo 'Error: '.$error;
			//	header('Content-type: text/xml');
				echo $xml_report;
			}else{
				
				if(!$gotoDeposit){
	
					header('Content-type: text/xml');
					echo $xml_report;
					die();
				
				}else{
		
					$redirectStr = '
						<form action="http://no1options.com/no1optionssignin.php" method="post" name="frm">
							<input type="hidden" name="email" value="'.getTag('<email>','<\/email>',$xml_report).'"/>
							<input type="hidden" name="password" value="'.validateParam('password','string',1).'"/>
						</form>
						<script language="JavaScript">
						document.frm.submit();
						</script>
						';
					
					echo $redirectStr;
				}
			}
		
		break;
		
		
		case 'addLead':

		$url.= '?'; 
		//  MODULE=Lead&COMMAND=add&FirstName=Lior&LastName=Chen&email=lior@iokun.com&campaignId=7&subCampaignId=45
		
		
			$subParams = setSubParam($subParams,'MODULE','Lead');
			$subParams = setSubParam($subParams,'COMMAND','add');
			$subParams = setSubParam($subParams,'FirstName',validateParam('firstName','string',1));
			$subParams = setSubParam($subParams,'LastName',	validateParam('lastName','string',1));
			$subParams = setSubParam($subParams,'email',	validateParam('email','email',1));
			$subParams = setSubParam($subParams,'subCampaign',	$_GET['subCampaign']);
			$subParams = setSubParam($subParams,'Phone',	$_GET['Phone']);
			
			
			//$gotoDeposit = validateParam('gotoDeposit','int',0,null,0);
			
			
			
			$url.= 'api_username='.$api_username.'&api_password='.$api_password.'&campaignId='.$campaignID ;
			$url.=setSubParamStr($subParams);
			//$url.='&1=1&param=1&btag=2&bTag=3&ctag=4&cTag=5&subcampaign=6&subCampaignId=7&parameter=8&Btag=9sub-campaign=10&subCampaign=11';
			//$url.='&phone=1&Phone=2';
			if ($debug==1)
			{
				echo '<br>'.$url . '<br>';
				die('debug!');
			}
			$xml_report = doPost($url);
			var_dump($xml_report);
			//die('done');
			
			
			$status = getTag('<operation_status>','<\/operation_status>',$xml_report);
			$status2 = getTag('<connection_status>','<\/connection_status>',$xml_report);
			
			if($status=='failed' OR $status2=='failed'){
				$error = getTag('<error>','<\/error>',$xml_report);
				echo 'Error: '.$error;
				header('Content-type: text/xml');
				echo $xml_report;
			}else{
				
				if(!$gotoDeposit){

					//header('Content-type: text/xml');
					echo $xml_report;
					die('not gotodeposit');
				
				}else{
				
					$redirectStr = '
						<form action="http://no1options.com/no1optionssignin.php" method="post" name="frm">
							<input type="hidden" name="email" value="'.getTag('<email>','<\/email>',$xml_report).'"/>
							<input type="hidden" name="password" value="'.validateParam('password','string',1).'"/>
						</form>
						<script language="JavaScript">
						document.frm.submit();
						</script>
						';
					
					echo $redirectStr;
				}
			}
		
		break;
		
		case 'login':
			
			$subParams = setSubParam($subParams,'MODULE','Customer');
			$subParams = setSubParam($subParams,'COMMAND','view');
			$subParams = setSubParam($subParams,'FILTER[email]',validateParam('email','email',1));
			$subParams = setSubParam($subParams,'FILTER[password]',	validateParam('password','string',1));
			
			$redirectToDeposit = validateParam('redirectToDeposit','int',0,null,0);
			
			$url.=setSubParamStr($subParams);
			$xml_report = doPost($url);

			$status = getTag('<operation_status>','<\/operation_status>',$xml_report);
			$status2 = getTag('<connection_status>','<\/connection_status>',$xml_report);
			
			if($status=='failed' OR $status2=='failed'){
				header('Content-type: text/xml');
				echo $xml_report;
			}else{
				$redirectStr = '
					<form action="http://no1options.com/no1optionssignin.php" method="post" name="frm">
						<input type="hidden" name="email" value="'.getTag('<email>','<\/email>',$xml_report).'"/>
						<input type="hidden" name="password" value="'.validateParam('password','string',1).'"/>
					</form>
					<script language="JavaScript">
					document.frm.submit();
					</script>
					';
				
				echo $redirectStr;
			}
			
		break;
		
		case 'view':
			
			$subParams = setSubParam($subParams,'MODULE','Customer');
			$subParams = setSubParam($subParams,'COMMAND','view');
			$subParams = setSubParam($subParams,'FILTER[email]',validateParam('email','email',1));
			$subParams = setSubParam($subParams,'FILTER[password]',	validateParam('password','string',1));
			
			$url.=setSubParamStr($subParams);
			$xml_report = doPost($url);
			
			var_dump($xml_report);
			
		break;
		
		case 'checkDeposits':
			
			$subParams = setSubParam($subParams,'MODULE','CustomerDeposits');
			$subParams = setSubParam($subParams,'COMMAND','view');
			$subParams = setSubParam($subParams,'FILTER[customerId]',validateParam('customerID','string',1));
			
			echo $url.setSubParamStr($subParams);
			
		break;
		
		
	}

?> 