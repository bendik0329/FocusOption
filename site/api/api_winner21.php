<?php

//progressplay 
ini_set('memory_limit', '-1');
ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL ^ E_NOTICE);

$siteURL = "http://partners.rightcommission.com/";	
	
echo '---- 21 Jackpots Integration ----	<br><br>';
	$merchant_id=22;
 $defaultBtag = 'a500-b-p';
	$debug=0;
	
require_once('common/global.php');

function overrideCtagBytracker ($originalCtag='' , $tracker=0) { 
$ctag='';

			if($tracker=="580666"){
				$ctag = 'a549-b1-p';
			}else if($tracker=="2hjj"){
				$ctag = 'a021-b527-p';
			}else{
				$ctag = $originalCtag;
			}
	return $ctag;
}



if ($_GET['m_date']) {
	$exp_mdate=explode("-",$_GET['m_date']);
	if ($_GET['monthly']) {
		$scanDateFrom = date("Y-m-01", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-m-01", strtotime("+1 Month",strtotime($scanDateFrom)));//.' 23:59:59';
		$totalPage = 30;
		} else {
		$scanDateFrom = date("Y-m-d", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
		$scanDateTo = date("Y-m-d", strtotime("+1 day",mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0])));//.' 23:59:59';
		}
	} else {
	$scanDateFrom = date("Y-m-d", strtotime("-1 Day"));//.' 00:00:00';
	$scanDateTo = date("Y-m-d");//.' 23:59:59';
	}
	
	

	
	// if(!$day)
		// $day=30;
$csvDate=$scanDateFrom;// date("Y-m-d",strtotime("-{$day} Day"));
$dir    = '/home/rightcommission/public_html/api/21/';
$files1 = scandir($dir);
$count=0;
$fileName='PlayersDaily-'.$csvDate.'.csv';

echo '<span style="color:blue">Working on ' . $fileName . '</span><br>';

 if(in_array($fileName, $files1)){

$fileArray=parse_csv_file($dir.$fileName);
$insert=array();

for($i=0;$i<count($fileArray);$i++){
if(!$fileArray[$i]['Date']){

continue;

}

 

 
	$ctag        = str_replace("--", "-", $fileArray[$i]['Dynamic Tracker']);
	$ctagArray          = array();
if (!ctagValid($ctag)) $ctag = $defaultBtag;	 
 	
	$ctagArray          = getBtag($ctag);
	if($ctagArray['affiliate_id'])
		$group = mysql_fetch_assoc(mysql_query('SELECT group_id AS g FROM affiliates WHERE id='."'".mysql_real_escape_string($ctagArray['affiliate_id'])."'"));

		$tranz_id=mysql_real_escape_string(str2date($fileArray[$i]["Date"]).$fileArray[$i]["Player ID"]);
	$tranz_id= str_replace(array(' ','-',':'), array('','',''), $tranz_id);
	
$db=array();
$db['merchant_id']=$merchant_id;														
$db['rdate']=str2date($fileArray[$i]["Date"]);							
$db['ctag']=mysql_real_escape_string($ctag);							
$db['affiliate_id']=mysql_real_escape_string($ctagArray["affiliate_id"]);	
if(!$db['affiliate_id'])
$db['affiliate_id']=500;	
$db['group_id']=mysql_real_escape_string($group["g"]);			
$db['banner_id']=mysql_real_escape_string($ctagArray["banner_id"]);		
$db['market_id']=0;//mysql_real_escape_string($ctagArray["affiliate_id"]);		
$db['profile_id']=mysql_real_escape_string($ctagArray["profile_id"]);	
$db['country']=mysql_real_escape_string($ctagArray["country"]?$ctagArray["country"]:$fileArray[$i]["Country"]);		
$db['trader_id']=intval($fileArray[$i]["Player ID"]);
$db['trader_alias']=mysql_real_escape_string($fileArray[$i]["Last Name"]);	
$db['type']='static';													
$db['freeParam']=mysql_real_escape_string($ctagArray["freeParam"]);		
$db['uid']=mysql_real_escape_string($ctagArray["uid"]);				
// $db['frozen']=mysql_real_escape_string($fileArray[$i]["Status"]);	

// if ($db['frozen']=='Blocked')
	// $db['frozen'] = 'frozen';
// else
	// $db['frozen'] = '';


$dbReg=$db;
$dbReg['type']='real';


$tracker=mysql_real_escape_string($fileArray[$i]["tracker"]);	
$db['ctag'] = overrideCtagBytracker($ctag,$tracker);

 if (mysql_real_escape_string($fileArray[$i]["Status"]) == 'Blocked' && $db['trader_id']!='' && $db['merchant_id_id']!='') {
 $qry = "update data_reg set status = 'frozen' where trader_id = ".$db['trader_id']."  and merchant_id = " . $db['merchant_id'];
 // die ($qry);
 mysql_query($qry);
// die('die');
}

 
$db['tranz_id']=$tranz_id;	
$db['amount']=mysql_real_escape_string($fileArray[$i]["FinalRev"]);		


if (!empty($dbReg['trader_id']))
$qry = "SELECT * FROM data_reg WHERE trader_id='{$dbReg['trader_id']}' and merchant_id = '{$dbReg['merchant_id']}'";
else
$qry = "SELECT * FROM data_reg WHERE trader_id='{$db['trader_id']}' and merchant_id = '{$db['merchant_id']}'";

$data_reg = mysql_fetch_assoc(mysql_query($qry));
if($data_reg['id']){
	$dbReg['id']=$data_reg['id'];

		$db['ctag']=$data_reg['ctag'];
		$ctagArray          = array();
		if (!ctagValid($db['ctag'])) $db['ctag'] = $defaultBtag;	
			$ctagArray          = getBtag($db['ctag']);
		if($ctagArray['affiliate_id'])
			$group = mysql_fetch_assoc(mysql_query('SELECT group_id AS g FROM affiliates WHERE id='."'".mysql_real_escape_string($ctagArray['affiliate_id'])."'"));

			if($ctagArray['affiliate_id']){
				$db['affiliate_id']=mysql_real_escape_string($ctagArray["affiliate_id"]);
				$db['banner_id']=mysql_real_escape_string($ctagArray["banner_id"]);		
				$db['market_id']=0;// mysql_real_escape_string($ctagArray["affiliate_id"]);		
				$db['profile_id']=mysql_real_escape_string($ctagArray["profile_id"]);	
				$db['country']=mysql_real_escape_string($ctagArray["country"]?$ctagArray["country"]:$fileArray[$i]["Country"]);	
				$db['group_id']=mysql_real_escape_string($group["g"]);	
		}	
}else{


	
dbAdd($dbReg,'data_reg');
	echo 'Processing Reg_: ' . $dbReg['id'] . '<br>';
}



$data_stats = mysql_fetch_assoc(mysql_query("SELECT * FROM data_stats WHERE tranz_id='{$db['tranz_id']}' and merchant_id = '{$db['merchant_id']}'"));
if($data_stats['id'])
$db['id']=$data_stats['id'];


$ctag=$data_reg['ctag'];
if (!ctagValid($ctag)) {
			$ctag = $defaultBtag;	
			$db['ctag'] = $ctag;
}

	
	
	
if ($debug==1)
{
	var_dump ($db);
	echo '<br>';
}

if ($db['amount']!=0)
	dbAdd($db,'data_stats');
	echo 'Processing Stats:  ' . $db['id'] . '<br>';


//$data_sales = mysql_fetch_assoc(mysql_query("SELECT * FROM data_sales WHERE tranz_id='{$db['tranz_id']}'"));
//if($data_sales['id'])
//$db['id']=$data_sales['id'];
	
/* $db['type']='revenue';	
$db['amount']=mysql_real_escape_string($fileArray[$i]["FinalRev"]);
dbAdd($db,'data_sales'); */

//print_r($db);

}



}



$fileName='DailyTransactions-'.$csvDate.'.csv';
echo '<span style="color:blue">Working on ' . $fileName . '</span><br>';



 if(in_array($fileName, $files1)){

$fileArray=parse_csv_file($dir.$fileName);
$insert=array();

for($i=0;$i<count($fileArray);$i++){
if(!$fileArray[$i]['Date'])
continue;


	
	$ctagArray          = array();

	$data_reg = mysql_fetch_assoc(mysql_query("SELECT * FROM data_reg WHERE trader_id='".intval($fileArray[$i]["Player ID"])."' and merchant_id = '{$merchant_id}'"));
	


$ctag=$data_reg['ctag'];
	if (!ctagValid($ctag)) {
		$ctag        = str_replace("--", "-", $fileArray[$i]['Dynamic']);
		if (!ctagValid($ctag)) {
			$ctag = $defaultBtag;	
		}
	}
	
	//if (!ctagValid($ctag)) $ctag = $defaultBtag;
	$ctagArray          = getBtag($ctag);
	if($ctagArray['affiliate_id'])
		$group = mysql_fetch_assoc(mysql_query('SELECT group_id AS g FROM affiliates WHERE id='."'".mysql_real_escape_string($ctagArray['affiliate_id'])."'"));

$db=array();
$db['rdate']=str2date($fileArray[$i]["Date"]);							
$db['ctag']=mysql_real_escape_string($ctag);							

$db['affiliate_id']=mysql_real_escape_string($ctagArray["affiliate_id"]);	
if(!$db['affiliate_id'])
$db['affiliate_id']=500;	
$db['group_id']=mysql_real_escape_string($group["g"]);			
$db['banner_id']=mysql_real_escape_string($ctagArray["banner_id"]);		
$db['market_id']=0;// mysql_real_escape_string($ctagArray["affiliate_id"]);		
$db['profile_id']=mysql_real_escape_string($ctagArray["profile_id"]);	
$db['country']=mysql_real_escape_string($ctagArray["country"]?$ctagArray["country"]:$fileArray[$i]["Country"]);		
$db['tranz_id']=mysql_real_escape_string($fileArray[$i]["Transaction ID"]);										
$db['trader_id']=intval($fileArray[$i]["Player ID"]);
$db['trader_alias']=mysql_real_escape_string($fileArray[$i]["Last Name"]);	
$db['freeParam']=mysql_real_escape_string($ctagArray["freeParam"]);		
$db['merchant_id']=$merchant_id;														
$db['uid']=mysql_real_escape_string($ctagArray["uid"]);				
// $db['status']=mysql_real_escape_string($fileArray[$i]["Status"]);	

// if ($db['status']=='Blocked')
	// $db['status'] = 'frozen';
// else
	// $db['status'] = '';


$tracker=mysql_real_escape_string($fileArray[$i]["tracker"]);	
$db['ctag'] = overrideCtagBytracker($ctag,$tracker);


if (mysql_real_escape_string($fileArray[$i]["Status"]) == 'Blocked' && $db['trader_id']!='' && $db['merchant_id_id']!='') {
 $qry = "update data_reg set status = 'frozen' where trader_id = ".$db['trader_id']."  and merchant_id = " . $db['merchant_id'];
 // die ($qry);
 mysql_query($qry);
// die('die');
}

		if (!$data_reg['id']){
			$dbReg=$db;
			$dbReg['type']='real';
			
			echo 'Processing Reg: Trader_ID-' . $dbReg['trader_id'] . '<br>';
			dbAdd($dbReg,'data_reg');
		
		}

if($fileArray[$i]["Deposit"]>0){
	$db['amount']=mysql_real_escape_string($fileArray[$i]["Deposit"]);
	$db['type']='deposit';	
}elseif($fileArray[$i]["Cashout"]>0){
	$db['amount']=mysql_real_escape_string($fileArray[$i]["Cashout"]);
	$db['type']='withdrawal';
}elseif($fileArray[$i]["Chargeback"]>0){
	$db['amount']=mysql_real_escape_string($fileArray[$i]["Chargeback"]);
	$db['type']='chargeback';	
}



$data_sales = mysql_fetch_assoc(mysql_query("SELECT * FROM data_sales WHERE tranz_id='{$db['tranz_id']}'"));
if($data_sales['id'])
$db['id']=$data_sales['id'];

if($db['type']){

echo 'Processing Sales:  ' . $db['id'] . '<br>';
dbAdd($db,'data_sales');

}



}



}

	

	
	
	function parse_csv_file($csvfile) {
    $csv = Array();
    $rowcount = 0;
    if (($handle = fopen($csvfile, "r")) !== FALSE) {
        $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
        $header = fgetcsv($handle, $max_line_length);
        $header_colcount = count($header);
        while (($row = fgetcsv($handle, $max_line_length)) !== FALSE) {
            $row_colcount = count($row);
            if ($row_colcount == $header_colcount) {
                $entry = array_combine($header, $row);
                $csv[] = $entry;
            }
            else {
                error_log("csvreader: Invalid number of columns at line " . ($rowcount + 2) . " (row " . ($rowcount + 1) . "). Expected=$header_colcount Got=$row_colcount");
                return null;
            }
            $rowcount++;
        }
        //echo "Totally $rowcount rows found\n";
        fclose($handle);
    }
    else {
        error_log("csvreader: Could not read CSV \"$csvfile\"");
        return null;
    }
    return $csv;
}



	
function str2date($str){
return date("Y-m-d H:i:s", strtotime($str));
}

die ('Done');
/*
<!--head>
  <meta http-equiv="refresh" content="1;URL='http://partners.rightcommission.com/api/api_winner21.php?day=<? echo($day-1); ?>'" />   
</head-->
*/
?>