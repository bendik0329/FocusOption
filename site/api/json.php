<?php

if(!isset($_REQUEST['username']) AND !isset($_REQUEST['password'])){
	die('No permission');
}else{
	$un = $_REQUEST['username'];
	$pass = $_REQUEST['password'];
	if(!($un=='user193' AND $pass=='919apb7')){
		die('No permission');
	}
}

chdir('../');
require_once('common/global.php');

$today = date("Y-m-d");
$amonthago = date("Y-m-d",strtotime( '-1 month'));


$from = isset($_REQUEST['from']) ? $_REQUEST['from'] : $amonthago;
$to = isset($_REQUEST['to']) ? $_REQUEST['to'] : $today;
$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1;

$whereReg = ' AND reg.rdate between "'.$from.'" AND  "'.$to.'"';
$whereSales = ' AND sales.rdate between "'.$from.'" AND  "'.$to.'"';
$page = ($page<=0 ? 0 : ($page-1)*200);

$traderSQL ='
SELECT 
reg.trader_id AS traderID, 
"No1Options" AS brand, 
reg.trader_alias AS username, 
"Binary Options" AS productType, 
"WEB" AS platform, 
reg.rdate AS signupDate,
reg.trader_id AS serial,
reg.affiliate_id AS advertiser,
countries.title AS country,
"-" AS language,
"USD" AS currency,
reg.saleStatus AS status,
"-" AS registrationStatus,
reg.firstname AS firstName,
reg.lastname AS lastName,
reg.email AS email,
"-" AS betsCount,
"-" AS betsSum,
IFNULL(s1.depositSum,0) AS depositSum,
IFNULL(s1.depositCount,0) AS depositCount,
IFNULL(s1.bonusSum,0) AS bonusSum,
IFNULL(s1.bonusCount,0) AS bonusCount,
IFNULL(s1.withdrawalSum,0) AS withdrawalSum,
IFNULL(s2.amount,0) AS ftdAmount,
s2.rdate AS firstDepositDate


FROM 

data_reg_no1options reg

LEFT JOIN countries ON UPPER(reg.country)=UPPER(countries.code)

LEFT JOIN (SELECT trader_id,
	
	SUM(IF(sales.type="deposit", sales.amount, 0)) AS depositSum, 
	SUM(IF(sales.type="deposit", 1, 0)) AS depositCount, 
	
	SUM(IF(sales.type="bonus", sales.amount, 0)) AS bonusSum, 
	SUM(IF(sales.type="bonus", 1, 0)) AS bonusCount, 
	
	SUM(IF(sales.type="withdrawal", sales.amount, 0)) AS withdrawalSum, 
	SUM(IF(sales.type="withdrawal", 1, 0)) AS withdrawalCount
	
	FROM data_sales_no1options sales GROUP BY sales.trader_id)s1 ON s1.trader_id=reg.trader_id
	

LEFT JOIN (SELECT * FROM (SELECT trader_id,rdate,amount
	
	FROM data_sales_no1options sales WHERE type="deposit" ORDER BY rdate ASC)sales GROUP BY sales.trader_id)s2 ON s2.trader_id=reg.trader_id	
	
	
WHERE 1=1 '.$whereReg.' ORDER BY reg.id DESC LIMIT '.$page.',200';




$transSQL = '
	SELECT 
	
	reg.trader_alias AS username,
	"No1Options" AS brand,
	"Binary Options" AS productType,
	"Web" AS platform,
	sales.rdate AS trxDate,
	sales.type AS type,
	sales.tranz_id AS code,
	sales.amount AS amount,
	"USD" AS currency,
	sales.amount AS amountBC
	
	FROM data_sales_no1options sales LEFT JOIN data_reg_no1options reg ON sales.trader_id=reg.trader_id
	
	WHERE 1=1 '.$whereSales.' ORDER BY sales.id DESC LIMIT '.$page.',200
';




if($_REQUEST['type']){
	if($_REQUEST['type']=='traders')
		$sql = $traderSQL;
	else if($_REQUEST['type']=='transactions'){
		$sql = $transSQL;
	}
}else{
	$sql = $traderSQL;
}

$rowsQ = mysql_query($sql) OR die(mysql_error());
$results = Array();
while($row = mysql_fetch_assoc($rowsQ)){
	$results[] = $row;
	/*
	echo '<BR>';
	echo 'Brand: '.					$trader['brand'].'<BR>';
	echo 'Username: '.				$trader['username'].'<BR>';
	echo 'productType: '.			$trader['productType'].'<BR>';
	echo 'platform: '.				$trader['platform'].'<BR>';
	echo 'signupDate: '.			$trader['signupDate'].'<BR>';
	echo 'serial: '.				$trader['serial'].'<BR>';
	echo 'advertiser: '.			$trader['advertiser'].'<BR>';
	echo 'country: '.				$trader['country'].'<BR>';
	echo 'language: '.				$trader['language'].'<BR>';
	echo 'currency: '.				$trader['currency'].'<BR>';
	echo 'status: '.				$trader['status'].'<BR>';
	echo 'registrationStatus: '.	$trader['registrationStatus'].'<BR>';
	echo 'firstName: '.				$trader['firstName'].'<BR>';
	echo 'lastName: '.				$trader['lastName'].'<BR>';
	echo 'phone: '.					$trader['phone'].'<BR>';
	echo 'email: '.					$trader['email'].'<BR>';
	echo 'betsCount: '.				$trader['betsCount'].'<BR>';
	echo 'betsSum: '.				$trader['betsSum'].'<BR>';
	echo 'depositSum: '.			$trader['depositSum'].'<BR>';
	echo 'depositCount: '.			$trader['depositCount'].'<BR>';
	echo 'bonusSum: '.				$trader['bonusSum'].'<BR>';
	echo 'bonusCount: '.			$trader['bonusCount'].'<BR>';
	echo 'withdrawalSum: '.			$trader['withdrawalSum'].'<BR>';
	echo 'withdrawalCount: '.		$trader['withdrawalCount'].'<BR>';
	echo 'FTD Amount: '.			$trader['ftdAmount'].'<BR>';
	echo 'FTD Date: '.				$trader['ftdDate'].'<BR>';
	echo '<BR>';
	*/
}

echo json_encode($results);


?>