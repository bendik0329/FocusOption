<?php
	if(!(isset($_REQUEST['username']) AND isset($_REQUEST['password']) AND $_REQUEST['username']=='winner' AND $_REQUEST['password']=='wnr123!')){
		die('Access denied');
	}
		
	require_once('common/database.php');
	
	set_time_limit(0);
	header('Content-Type: text/xml');

	if ($_GET['sdate']) $where .= " AND rdate >= '".$_GET['sdate']."'";
	if ($_GET['edate']) $where .= " AND rdate <= '".$_GET['edate']."'";
	if ($_GET['mode']) $whereActivity = $where;
	
	$searchBtag = "a4033";
	
	echo '<?xml version="1.0" encoding="UTF-8" ?>'."\n";
	echo '<Data>'."\n";
	if ($_GET['label'] == "no1options" or 1==1) {
		
		if($_REQUEST['type']=='reg'){
			$sql = "SELECT * FROM data_reg_no1options WHERE rdate>'2014-07-31'  ".$where." ORDER BY rdate ASC";
//die ($sql);
			$query = function_mysql_query($sql,__FILE__);
			while($ww = mysql_fetch_assoc($query)){
				
				$btag = $ww['ctag'];
				$country = explode('-c',$btag);
				$country = explode('-',$country[1]);
				$country = $country[0];
				
				
				echo '<TraderInfo>'."\n";
				
				echo '<CUSTOMERCODE>'.$ww['trader_id'].'</CUSTOMERCODE>'."\n";
				echo '<BRAND>no1options</BRAND>'."\n";
				echo '<PRODUCTTYPE>Binary</PRODUCTTYPE>'."\n";
				echo '<PLATFORM>Website</PLATFORM>'."\n";
				echo '<SIGNUPDATE>'.$ww['rdate'].'</SIGNUPDATE>'."\n";
				echo '<ADVERTISER>AffiliateBuddies</ADVERTISER>'."\n";
				echo '<COUNTRY>'.$country.'</COUNTRY>'."\n";
				echo '<LANGUAGE></LANGUAGE>'."\n";
				echo '<CURRENCY></CURRENCY>'."\n";
				echo '<STATUS></STATUS>'."\n";
				echo '<REGISTRATION_STATUS></REGISTRATION_STATUS>'."\n";
				echo '<FIRSTNAME>'.$ww['fname'].'</FIRSTNAME>'."\n";
				echo '<LASTNAME>'.$ww['lname'].'</LASTNAME>'."\n";
				echo '<EMAIL>'.$ww['email'].'</EMAIL>'."\n";
				echo '<ADMAP>'.$ww['btag'].'</ADMAP>'."\n";
				
				echo '</TraderInfo>'."\n";
				flush();
			}
		
		}else if($_REQUEST['type']=='sales'){
			//$sql = "SELECT data_sales_no1options.*,j1.ftd  FROM data_sales_no1options WHERE 1=1 inner join (select min(trader_id),min(date_) as ftd from data_sales_no1options where type='deposit' group by trader_id) j1 on j1.traderid = data_sales_no1options.traderid ".$where." ORDER BY rdate ASC";
			$sql = "SELECT data_sales_no1options.*,j1.ftd as ftd FROM data_sales_no1options inner join (select min(trader_id) as trader_id,min(rdate) as ftd from data_sales_no1options where type='deposit' group by trader_id) j1 on j1.trader_id = data_sales_no1options.trader_id where 1=1 and  rdate>'2014-07-31' ".$where." ORDER BY `data_sales_no1options`.`rdate` asc";
			//die ($sql);
						$query = function_mysql_query($sql,__FILE__);
			while($row = mysql_fetch_assoc($query)){
				//var_dump ($row);
				//die();
				echo '<TraderInfo>'."\n";
				echo '<CUSTOMERCODE>'.$row['trader_id'].'</CUSTOMERCODE>'."\n";
				echo '<ADVERTISER>AffiliateBuddies</ADVERTISER>'."\n";
				echo '<BRAND>no1options</BRAND>'."\n";
				echo '<PRODUCTTYPE>Binary</PRODUCTTYPE>'."\n";
				echo '<PLATFORM>Website</PLATFORM>'."\n";
				echo '<TRXDATE>'.$row['rdate'].'</TRXDATE>'."\n";
				echo '<TYPE>'.$row['type'].'</TYPE>'."\n";
				echo '<CODE>'.$row['tranz_id'].'</CODE>'."\n";
				echo '<AMOUNT>'.$row['amount'].'</AMOUNT>'."\n";
				//echo '<CURRENCY>'.$row['currency'].'</CURRENCY>'."\n";
				echo '<CURRENCY>USD</CURRENCY>'."\n";
				echo '<AMOUNTBC>'.$row['amount'].'</AMOUNTBC>'."\n";
				echo '<FD_Date>'.$row['ftd'].'</FD_Date>'."\n";

				echo '</TraderInfo>'."\n";
				flush();
			}
		}
	}
		
	echo '</Data>'."\n";
	mysql_close();
	die();
?>