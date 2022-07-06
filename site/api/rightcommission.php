<?php


ini_set("memory_limit","128M");
chdir('../');
require_once('common/global.php');


//////////////////////// rightCommission

$isTest = $_REQUEST['isTest'];

function getParam(){
	if(isset($_REQUEST['isTest'])){
		return $_REQUEST[p];
	}else{
		return $_POST[p];
	}
}

function getAction($d,$w,$b){
	$arr = Array();
	$arr['deposit'] = $d;
	$arr['withdrawal'] = $w;
	$arr['bonus'] = $b;
	return $arr;
}


$defaultBtag = 'a1524-b565-p';


if(!$isTest){
	$api_user = getParam('api_user');
	$api_password = getParam('api_pass');
	$scanDateFrom = getParam('scanDateFrom');
	$scanDateTo = getParam('scanDateTo');
	$merchantID = getParam('merchant');
	
}else{

	$api_user = 'magicred'; 					//static api_user
	$api_password = '51sehadfa4'; 				//static api_password
	$scanDateFrom = '2014-09-10';
	$scanDateTo = '2014-09-12';
	$merchantID = 1;
}

//$url = 'https://' . $api_user . ':' . $api_password . '@portal.winneraffiliates.com/portal/outer/csv.jhtm?reportId=3993&reportBy1=date&startDate='.$scanDateFrom.'&endDate='.$scanDateTo.'&reportBy2=player&reportBy3=affiliate&reportBy4=var1';
$url = 'https://' . $api_user . ':' . $api_password . '@portal.winneraffiliates.com/portal/outer/csv.jhtm?reportId=3993&reportBy1=date&startDate='.$scanDateFrom.'&endDate='.$scanDateTo.'&reportBy2=player&reportBy3=affiliate&reportBy4=var1';
echo 'URL : ' . $url . '<br>';
//die();


//https://magicred:51sehadfa4@portal.winneraffiliates.com/portal/outer/csv.jhtm?reportId=3993&reportBy1=date&startDate=2014-08-01&endDate=2014-09-01

$xml_report = file_get_contents($url) or die("Feed not working");
$xml_report = rtrim($xml_report,"\n");
$xml = explode("\n",$xml_report);


$titleAdded=0;
$titles = array();
$values = array();


foreach($xml AS $xml_line){
	
	$xl = explode(",",$xml_line);
		
	if(!$titleAdded){
			
		$titles = $xl;
			
		$titleAdded++;
		
		continue;
		
	}else{
		
		for($i=0;$i<count($xl);$i++){
			
			$values[$titles[$i]] = $xl[$i];
			
		}
	
	}
	//var_dump(rtrim(trim($values['"UserName"'],'"'),'"'));
	
	
	$traderID = 		rtrim(trim($values['"UserName"'],'"'),'"');
	$traderAlias = 		rtrim(trim($values['"UserName"'],'"'),'"');
	$bTag =			 	rtrim(trim($values['"Var 1"'],'"'),'"');
	//echo $bTag.'<BR><BR>';
	//continue;
	$rdate = 			rtrim(trim($values['"Date"'],'"'),'"');
	$isDemo =	 		rtrim(trim($values['"Downloads"'],'"'),'"');
	$netRevenue = 		rtrim(trim($values['"Casino Net Gaming"'],'"'),'"');
	$amount =			getAction(rtrim(trim($values['"Total Deposit amt"'],'"'),'"'),rtrim(trim($values['"Withdrawal amt"'],'"'),'"'),rtrim(trim($values['"Bonus amt"'],'"'),'"'));
	
	if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) {
		//echo ' --- GO TO DEFAULT BTAG: '.$defaultBtag.' ---';
		$bTag = $defaultBtag;
	}
	if (!btagValid($bTag) AND !ctagValid($bTag) AND !ctagMarketValid($bTag)) {
		//echo ' --- GO TO DEFAULT CTAG: '.$defaultBtag.' ---';
		setLog('BTag not valid[Sales|'.$bTag.'|'.$TransactionDate.'|'.$TraderID.']',9,'red');
		continue;
	}
	
	$bTagEX = explode("-", $bTag);
	//a-b-p-c-f
	$affiliateID = substr($bTagEx[0],1);
	$bannerID = substr($bTagEx[0],1);
	$profileID = substr($bTagEx[0],1);
	$country = substr($bTagEx[0],1);
	
	$affiliate = mysql_fetch_assoc(mysql_query("SELECT * FROM affiliates WHERE id='".$affiliateID."'"));
	$groupID = $affiliate['group_id'];
	
	//if ($isDemo>0) {
	$typeparam = 3; //all real account
	//} else {
	//}
	
	$chkDouble=mysql_fetch_assoc(mysql_query("SELECT id FROM data_reg WHERE merchant_id=".$merchantID." AND trader_id='".$traderID."'"));
	if(!$chkDouble['id']){
		$query = "INSERT INTO data_reg (type, merchant_id, rdate,ctag,trader_id,trader_alias,country, affiliate_id, banner_id, profile_id, group_id) VALUES (".$typeparam.", ".$merchantID.",'".date("Y-m-d", strtotime($rdate))."','".$bTag."','".$traderID."','".$traderAlias."','".$country."','".$affilaiteID."','".$bannerID."','".$profileID."','".$groupID."')";
		mysql_query($query) OR die(mysql_error());
		//echo '<BR><BR>'.$query.'<BR><BR>';
		echo '<span style="font-family:ARIAL; font-size:11px; font-weight:bold; color:BLUE">Trader ID: <b>'.$traderID.'</b> | <b>'.$bTag.'</b> - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br /></span>';
	}else{
		echo '<span style="font-family:ARIAL; font-size:11px; font-weight:bold; color:RED">Trader ID: <b>'.$traderID.'</b> | <b>'.$bTag.'</b> - '.($chkDouble['id'] ? 'exist' : 'inserted').'!<br /></span>';
	}
	
	//var_dump($values);
	echo '<BR>';

}





?>