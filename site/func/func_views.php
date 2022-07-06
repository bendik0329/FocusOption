<?php

// NXCS2 [Encode in UTF-8 Without BOM] [☺]
$debug =  isset($_GET['debug']) && $_GET['debug']==1 ? true : false;
function generatePNLquery($arr , $genSum=false,$getDistinct=false){
	global $set,$debug,$showCasinoFields;
	$affiliate_id = isset($arr['affiliate_id'])? $arr['affiliate_id'] : "";
	$merchant_id = isset($arr['merchant_id'])? $arr['merchant_id'] : "";
	$group_id = isset($arr['group_id'])? $arr['group_id'] : "";
	$trader_id = isset($arr['trader_id'])? $arr['trader_id'] : "";
	$country_id = isset($arr['country_id'])? $arr['country_id'] : "";
	$banner_id = isset($arr['banner_id'])? $arr['banner_id'] : "";
	$profile_id = isset($arr['profile_id'])? $arr['profile_id'] : "";
	$from = isset($arr['fromdate'])? $arr['fromdate'] : "";
	$to = isset($arr['todate'])? $arr['todate'] : "";
	$searchInSql = isset($arr['searchInSql'])? $arr['searchInSql'] : "";
	
		$filterByDate = (!empty($from) && !empty($to)) || !empty($searchInSql) ? true : false;
		
	 {
					
				if (!$genSum){
					$sql = '  select  ' . ($getDistinct ? ' distinct(affiliate_id) as affiliate_id ' : ' "detailed" as method ,dr.initialftddate,dr.isSelfDeposit,a.* ') . ' from 
					
					(SELECT pnltable.id,pnltable.rdate,pnltable.amount as amount,pnltable.trader_id,pnltable.merchant_id,pnltable.banner_id,pnltable.profile_id,pnltable.affiliate_id FROM '.$set->pnlTable.' pnltable

																	WHERE 
																	' .($showCasinoFields==1 ? 
																	' lower(pnltable.type) in ("static","bets","wins","jackpot", "bonuses","removed_bonuses") ' 
																	: 
																	' pnltable.type="PNL"  ' ).'
																	
																	and pnltable.merchant_id>0   	and pnltable.amount<>0 '
																	.(!empty($affiliate_id) ?  '  and  pnltable.affiliate_id = ' . $affiliate_id : "")
																	.(!empty($merchant_id) ?  '  and  pnltable.merchant_id = ' . $merchant_id : "")
																	
																	.(!empty($trader_id) ?  '  and  pnltable.trader_id = ' . $trader_id : "")
																	.(!empty($banner_id) ?  '  and  pnltable.banner_id = ' . $banner_id : "")
																	.(!empty($country_id) ?  '  and  pnltable.country = "' . $country_id .'"' : "")
																	.(!empty($profile_id) ?  '  and  pnltable.profile_id = ' . $profile_id : "")
																	.(!empty($group_id) ?  '  and  pnltable.group_id = ' . $group_id : "")
																	.($filterByDate?   ' AND pnltable.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) : "" )
																	.' ) a 
																	inner join 
																	(select merchant_id,trader_id,type,profile_id,banner_id,initialftddate,isSelfDeposit from data_reg 
																		where
																		initialftddate>"0000-00-00 00:00:00"  '
																			.(!empty($affiliate_id) ?  '  and  affiliate_id = ' . $affiliate_id : "")
																	.(!empty($merchant_id) ?  '  and  merchant_id = ' . $merchant_id : "")
																	.(!empty($trader_id) ?  '  and  trader_id = ' . $trader_id : "")
																	.(!empty($banner_id) ?  '  and  banner_id = ' . $banner_id : "")
																	.(!empty($country_id) ?  '  and  country = "' . $country_id .'"' : "")
																	.(!empty($profile_id) ?  '  and  profile_id = ' . $profile_id : "")
																	.(!empty($group_id) ?  '  and  group_id = ' . $group_id : "")
																	. '
																	)
																	dr on a.merchant_id = dr.merchant_id and a.trader_id  = dr.trader_id and dr.type<>"demo" '
																	. ($getDistinct ? ' ' : '	ORDER BY a.rdate' );
																	// .' ORDER BY pnltable.rdate';
					
					
					
				}
				else {
					

			$sql = 'SELECT  "summed" as method ,dr.initialftddate,dr.isSelfDeposit, pnltable.id,sum(pnltable.amount) as amount,max(pnltable.rdate) as rdate ,max(pnltable.trader_id) as trader_id,max(pnltable.merchant_id) as merchant_id,max(pnltable.banner_id) as banner_id,max(pnltable.profile_id) as profile_id,max(pnltable.affiliate_id) as affiliate_id FROM '.$set->pnlTable.' pnltable
															 inner join 
															 (select merchant_id,trader_id,type,profile_id,banner_id,initialftddate,isSelfDeposit from data_reg 
																		where
																		initialftddate>"0000-00-00 00:00:00"  '
																			.(!empty($affiliate_id) ?  '  and  affiliate_id = ' . $affiliate_id : "")
																	.(!empty($merchant_id) ?  '  and  merchant_id = ' . $merchant_id : "")
																	.(!empty($trader_id) ?  '  and  trader_id = ' . $trader_id : "")
																	.(!empty($banner_id) ?  '  and  banner_id = ' . $banner_id : "")
																	.(!empty($country_id) ?  '  and  country = "' . $country_id .'"' : "")
																	.(!empty($profile_id) ?  '  and  profile_id = ' . $profile_id : "")
																	.(!empty($group_id) ?  '  and  group_id = ' . $group_id : "")
																	. '
																	)
																	
															 dr on 
															 
															 pnltable.merchant_id = dr.merchant_id and pnltable.trader_id  = dr.trader_id and dr.type<>"demo"
															 WHERE 
															 
															 ' .($showCasinoFields==1 ? 
																	' lower(pnltable.type) in ("static","bets","wins","jackpot", "bonuses","removed_bonuses") ' 
																	: 
																	' pnltable.type="PNL"  ' ).'

																	and pnltable.merchant_id>0 and pnltable.amount<>0   '
															 .(!empty($affiliate_id) ?  '  and  pnltable.affiliate_id = ' . $affiliate_id : "")
															 .(!empty($group_id) ?  '  and  pnltable.group_id = ' . $group_id : "")
															 .(!empty($trader_id) ?  '  and  pnltable.trader_id = ' . $trader_id : "")
															 .(!empty($country_id) ?  '  and  pnltable.country_id = ' . $country_id : "")
															 .(!empty($banner_id) ?  '  and  pnltable.banner_id = ' . $banner_id : "")
															 .(!empty($profile_id) ?  '  and  pnltable.profile_id = ' . $profile_id : "")
															 .(!empty($merchant_id) ?  '  and  pnltable.merchant_id = ' . $merchant_id : "")
															 .($filterByDate?   ' AND pnltable.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) : "" )
															 .' ORDER BY pnltable.rdate';		
					
				}
		}
	return $sql;
	
	
}


function generatePNLqueryForTraderReport($arr , $genSum=false,$bigMainQueryFromTopTraderReportFile=""){
	if (empty($bigMainQueryFromTopTraderReportFile))
		return "";
	
	
	global $set;
	$affiliate_id = isset($arr['affiliate_id'])? $arr['affiliate_id'] : "";
	$merchant_id = isset($arr['merchant_id'])? $arr['merchant_id'] : "";
	$group_id = isset($arr['group_id'])? $arr['group_id'] : "";
	$trader_id = isset($arr['trader_id'])? $arr['trader_id'] : "";
	$country_id = isset($arr['country_id'])? $arr['country_id'] : "";
	$banner_id = isset($arr['banner_id'])? $arr['banner_id'] : "";
	$profile_id = isset($arr['profile_id'])? $arr['profile_id'] : "";
	$from = isset($arr['fromdate'])? $arr['fromdate'] : "";
	$to = isset($arr['todate'])? $arr['todate'] : "";
	$searchInSql = isset($arr['searchInSql'])? $arr['searchInSql'] : "";
	
		$filterByDate = (!empty($from) && !empty($to)) || !empty($searchInSql) ? true : false;
	
	
	$type = getMerchantType($merchant_id);
	
	if ($type=='casino')
		$type='static';
	else	
		$type='PNL';
	
	
	
		$sql = 'select "detailed" as method ,mainBigQry.affiliate_id, mainBigQry.group_id,mainBigQry.initialftddate,mainBigQry.isSelfDeposit,a.* from (
		
		
		SELECT pnltable.id,pnltable.rdate,pnltable.amount as amount,pnltable.trader_id,pnltable.merchant_id,pnltable.banner_id,pnltable.profile_id,pnltable.affiliate_id FROM '.$set->pnlTable.' pnltable

														WHERE 1=1 ' 
														
														.(!empty($merchant_id) ?  '  and  pnltable.merchant_id = ' . $merchant_id : "")
														.(!empty($affiliate_id) ?  '  and  pnltable.affiliate_id = ' . $affiliate_id : "")
														
														
														.(!empty($trader_id) ?  '  and  pnltable.trader_id = ' . $trader_id : "")
														.(!empty($banner_id) ?  '  and  pnltable.banner_id = ' . $banner_id : "")
														.(!empty($country_id) ?  '  and  pnltable.country_id = ' . $country_id : "")
														.(!empty($profile_id) ?  '  and  pnltable.profile_id = ' . $profile_id : "")
														.(!empty($group_id) ?  '  and  pnltable.group_id = ' . $group_id : "")
														.  '  and pnltable.type="'.$type.'" and pnltable.merchant_id>0   	and pnltable.amount<>0 '
														// .($filterByDate?   ' AND pnltable.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) : "" )
														.($filterByDate?   ' AND pnltable.rdate >"' . $from . '" ' : "" )
														.' ) a 
														
														inner join 
														
														('.
															$bigMainQueryFromTopTraderReportFile
														.')
														
														mainBigQry on mainBigQry.merchant_id = a.merchant_id and mainBigQry.trader_id = a.trader_id 
														
														ORDER BY a.rdate';
														// .' ORDER BY pnltable.rdate';
		
		// die ("<br>". "<br>". $sql."<br>". "<br>");
		
	return $sql;
	
	
}

function getNonQualifiedFTD($justToday=1,$fromThisMonth="",$trader_id=""){

		 global $debug;
		 
	$merchants  = getMerchants(0,0); 
	foreach ($merchants as $merchantRow){
	

/* 		$q = "
		SELECT max(data_reg.affiliate_id) as affiliate_id ,max(data_reg.profile_id) as profile_id , MAX( data_sales.trader_id ) as trader_id , MAX( data_sales.merchant_id ) as merchant_id , COUNT( data_sales.tranz_id ) as count , SUM( data_sales.amount ) as amount , MAX( data_sales.rdate ) as rdate
		, MAX( data_reg.initialftddate ) as initialftddate, MAX( data_reg.isSelfDeposit ) as isSelfDeposit
		FROM data_reg
		LEFT JOIN 
		
		(select * from data_sales where merchant_id = " . $merchantRow['id'] . 
		(!empty($trader_id) ?  " AND trader_id = '" . $trader_id . "' " : "").
		" and type = 'volume' )
		
		
		data_sales ON data_reg.merchant_id = data_sales.merchant_id
		AND data_reg.trader_id = data_sales.trader_id 
		WHERE data_reg.initialftddate <= data_sales.rdate  and data_reg.FTDqualificationDate =  '0000-00-00 00:00:00'
		AND not data_reg.initialftddate =  '0000-00-00 00:00:00' " .
		(!empty($fromThisMonth) ?  " AND data_reg.initialftddate > '" . $fromThisMonth . "' " : "").
		(!empty($trader_id) ?  " AND data_reg.trader_id = '" . $trader_id . "' " : "").
		"
		and data_reg.merchant_id = " . $merchantRow['id'] . 
		" GROUP BY data_sales.merchant_id, data_sales.trader_id"; */
		
		
		$q = "
		SELECT max(data_reg.affiliate_id) as affiliate_id ,max(data_reg.profile_id) as profile_id ,max(data_reg.banner_id) as banner_id , MAX( data_reg.trader_id ) as trader_id , MAX( data_reg.merchant_id ) as merchant_id , COUNT( data_sales.tranz_id ) as count , SUM( data_sales.amount ) as amount , MAX( data_sales.rdate ) as rdate
		, MAX( data_reg.initialftddate ) as initialftddate, MAX( data_reg.isSelfDeposit ) as isSelfDeposit
		FROM data_reg
		LEFT JOIN 
		
		(select * from data_sales where merchant_id = " . $merchantRow['id'] . 
		(!empty($trader_id) ?  " AND trader_id = '" . $trader_id . "' " : "").
		" and type = 'volume' )
		
		
		data_sales ON data_reg.merchant_id = data_sales.merchant_id
		AND data_reg.trader_id = data_sales.trader_id 
		and data_reg.initialftddate <= data_sales.rdate
		WHERE data_reg.FTDqualificationDate =  '0000-00-00 00:00:00'
		AND not data_reg.initialftddate =  '0000-00-00 00:00:00' " .
		(!empty($fromThisMonth) ?  " AND data_reg.initialftddate > '" . $fromThisMonth . "' " : "").
		(!empty($trader_id) ?  " AND data_reg.trader_id = '" . $trader_id . "' " : "").
		"
		and data_reg.merchant_id = " . $merchantRow['id'] . 
		" GROUP BY data_reg.merchant_id, data_reg.trader_id";
		
if ($_GET['debug']==1){
		echo  ('  ' . $q.'<br>');
		echo '<br>';
		// var_dump($merchantRow);
		echo '<br>';
}
		
		$qualifiedArray['merchant_type']=	$merchantRow['producttype']; 
		$qualifiedArray['merchant_qualify_type']=		 $merchantRow['qualify_type'];
		$qualifiedArray['merchant_qualify_amount'] =		 $merchantRow['qualify_amount'];
		$onlyRevenueShare= 0;
		
		$rsc = function_mysql_query($q);
		while ($row = mysql_fetch_assoc($rsc)){

			if ($row['profile_id']>0)
					 $qualifiedArray['strSqlProfileId'] =		 " and profile_id= " . $row['profile_id'] . " ";
			
			$transactionRow['rdate'] = $row['rdate'];
			$transactionRow['affiliate_id'] = $row['affiliate_id'];
			$transactionRow['merchant_id'] = $merchantRow['merchant_id'];
			$transactionRow['traderHasFTD']=true;
			$transactionRow['isFTD']=true;
			$transactionRow['trader_id']=$row['trader_id'];
			$transactionRow['trades'] = $row['count'];
			
			$traderTrades = $row['count'];
 			$traderVolume  = $row['amount'];
/*			function_mysql_query("update data_reg set traderTrades = ". $traderTrades ." , traderValue = " . $traderValue . " where merchant_id = " . $row['merchant_id'] . " and trader_id = '". $row['trader_id'] . "' ;"); */
			
			
			$affiliateRow = getAffiliateRow($row['affiliate_id']);
			
			$a = checkQualification($qualifiedArray,$affiliateRow,$transactionRow,$onlyRevenueShare) ;
			// var_dump($transactionRow);
			// var_dump($a);
			// die();
			if ($_GET['debug']==1){
				echo 'Qualification status: ' . $a .'<br><br>';
			}
			if ($a==1){
			
			
			if (($affiliateRow['qualify_type']=='' || $affiliateRow['qualify_type']=='none') ||($affiliateRow['qualify_type']=='default' && ($qualifiedArray['merchant_qualify_type']=='none' || $qualifiedArray['merchant_qualify_type']=='' )))
			{ // no qualification
				$row['rdate'] = $row['initialftddate'];
			}
			
			$qualifiedDate = !isset($row['rdate']) ? date('Y-m-d H-i-s') :$row['rdate'];
			$traderVolume = !isset($row['amount']) ? 0 :$row['amount'];
			$traderTrades = !isset($row['count']) ? 0 :$row['count'];
			
			$updateq = "update data_reg set isSelfDeposit = '" . $row['isSelfDeposit'] . "' ,FTDqualificationDate = '" . $qualifiedDate . "' ,traderTrades = ". $traderTrades .",traderVolume = ". $traderVolume ." where merchant_id = " . $row['merchant_id'] . " and trader_id = '". $row['trader_id'] . "' ;";
			if ($_GET['debug']==1)
				echo $updateq.'<Br>';
			function_mysql_query($updateq);
			
			include_once('../pixel.php');
			$ctag = 'a'.$row['affiliate_id'].'-b'.$row['banner_id'].'-p'.$row['profile_id'];
			$siteURL = $set->webAddress;
			$pixelurl = $siteURL  . 'pixel.php?act=qftd&affiliate_id='.$row['affiliate_id'].'&product_id='.$row['product_id'].'&merchant_id='.$transactionRow['merchant_id'].'&trader_id='.$transactionRow['trader_id'].'&ctag=' . $ctag ;
				if ($debug)
					echo $pixelurl.'<br>';
				// die ('pixelurl: '  . $pixelurl);
				$pixelContent  = firePixel($pixelurl);

				
			
			// var_dump($row);
			// die();
			}
			
			
		}
	
	}
	
	echo 'Done with FTD qualification process!<br>';
}	

function updateInitialFTD($fromThisMonth=0,$trader_id=""){

	$scanDateFrom="";
	if ($fromThisMonth ==1){
			$exp_mdate=explode("-",date('Y-m-d H:i:s'));
			$scanDateFrom = date("Y-m-01 00:00:00", mktime(0,0,0,$exp_mdate[1],$exp_mdate[2],$exp_mdate[0]));//.' 00:00:00';
	}

		$merchants  = getMerchants(0,0); 
		foreach ($merchants as $merchantRow){

				$q  = "
						 SELECT ds.tranz_id ,ds.isSelfDeposit , ds.amount, ds.trader_id, ds.merchant_id, MIN( ds.rdate ) AS rdate
				FROM data_sales ds
				INNER JOIN data_reg dr ON ds.merchant_id = dr.merchant_id
				AND ds.trader_id = dr.trader_id
				WHERE ds.type =  'deposit'
				AND dr.initialftddate =  '0000-00-00 00:00:00' " .
				(!empty($scanDateFrom) ? " and ds.rdate> '" . $scanDateFrom . "' " : "" ).
				(!empty($trader_id) ? " and ds.trader_id = '" . $trader_id . "' " : "" ).
				" and ds.merchant_id = " . $merchantRow['id'] . 
				" GROUP BY ds.merchant_id, ds.trader_id";
				// die ($q) ;
				$rsc = function_mysql_query($q);
				while ($chkTrader = mysql_fetch_assoc($rsc)) {

					
					
				$UpdateFTDforTrader = "update data_reg set  isSelfDeposit = " . $chkTrader['isSelfDeposit'] . ", ftdamount = " .$chkTrader['amount']." , initialftdtranzid = '"
				.$chkTrader['tranz_id']."' , initialftddate = '" .$chkTrader['rdate'] ."' where trader_id= '".$chkTrader['trader_id']. "'  
				and merchant_id = " . $chkTrader['merchant_id'];

				
				// die ($UpdateFTDforTrader);

				function_mysql_query($UpdateFTDforTrader);
															
				}
					echo 'Done with FTD initialftddate process!<br>';
		}
		
		
		
		
		echo 'Starting running over products<br><br>';
		
		$resProd = mysql_query("select * from products_items where valid!=0");
		while ($productRow = mysql_fetch_assoc($resProd)){
			// foreach ($merchants as $merchantRow){

				$q  = "
						 SELECT dr.product_id,ds.tranz_id ,ds.isSelfDeposit , ds.amount, ds.trader_id, ds.merchant_id, MIN( ds.rdate ) AS rdate
				FROM data_sales ds
				INNER JOIN data_reg dr ON ds.merchant_id = dr.merchant_id
				AND ds.trader_id = dr.trader_id
				WHERE ds.type =  'deposit'
				AND dr.initialftddate =  '0000-00-00 00:00:00' " .
				(!empty($scanDateFrom) ? " and ds.rdate> '" . $scanDateFrom . "' " : "" ).
				(!empty($trader_id) ? " and ds.trader_id = '" . $trader_id . "' " : "" ).
				" and ds.product_id = " . $productRow['id'] . 
				" GROUP BY ds.product_id, ds.trader_id";
				// die ($q) ;
				$rsc = function_mysql_query($q);
				while ($chkTrader = mysql_fetch_assoc($rsc)) {

					
					
				$UpdateFTDforTrader = "update data_reg set  isSelfDeposit = " . $chkTrader['isSelfDeposit'] . ", ftdamount = " .$chkTrader['amount']." , initialftdtranzid = '"
				.$chkTrader['tranz_id']."' , initialftddate = '" .$chkTrader['rdate'] ."' where trader_id= ".$chkTrader['trader_id']. "  
				and product_id = " . $chkTrader['product_id'];

				
				// die ($UpdateFTDforTrader);

				function_mysql_query($UpdateFTDforTrader);
															
				}
					echo 'Done with FTD initialftddate process!<br>';
		}
}




function updateTraderValue($justToday=1){

		 global $debug;
	$merchants  = getMerchants(0,0); 
	foreach ($merchants as $merchantRow){
	

		$q = "
		SELECT merchant_id,trader_id, affiliate_id,

SUM( IF( TYPE =  'deposit', amount, 0 ) ) - SUM( IF( 
TYPE =  'withdrawal', amount, 0 ) ) - SUM( IF( 
TYPE =  'chargeback', amount, 0 ) ) AS tradervalue,
max(rdate) as lastRowDate
FROM data_sales
where merchant_id = " . $merchantRow['id'] . "
 " . ($justToday==1 ? " and trader_id in (select distinct trader_id from data_sales where merchant_id = " . $merchantRow['id'] . " and rdate  > DATE_SUB(NOW(), INTERVAL 1 DAY) and (type='deposit' or type='withdrawal' or type='chargeback')) " : "") .
" GROUP BY merchant_id, trader_id ";
// die ($q);
		$qualifiedArray['merchant_type']=	$merchantRow['producttype']; 
		$qualifiedArray['merchant_qualify_type']=		 $merchantRow['qualify_type'];
		$qualifiedArray['merchant_qualify_amount'] =		 $merchantRow['qualify_amount'];
		$onlyRevenueShare= 0;
		
		$rsc = function_mysql_query($q);
		while ($row = mysql_fetch_assoc($rsc)){

			
			
			$transactionRow['rdate'] = $row['lastRowDate'];
			$transactionRow['affiliate_id'] = $row['affiliate_id'];
			$transactionRow['merchant_id'] = $merchantRow['id'];
			$transactionRow['traderHasFTD']=true;
			$transactionRow['isFTD']=true;
			$transactionRow['trader_id']=$row['trader_id'];
			// $transactionRow['trades'] = $row['count'];
			
			$tradervalue = $row['tradervalue'];
 			// $traderVolume  = $row['amount'];
			// mysql_query("update data_reg set traderTrades = ". $traderTrades ." , traderVolume = " . $traderVolume . " where merchant_id = " . $row['merchant_id'] . " and trader_id = '". $row['trader_id'] . "' ;"); 
			
						
			
			
			$affiliateRow = getAffiliateRow($row['affiliate_id']);
			
			// $a = checkQualification($qualifiedArray,$affiliateRow,$transactionRow,$onlyRevenueShare) ;
			// if ($a==1){
			
			function_mysql_query("update data_reg set lastTransactionRecordDate = '" . $transactionRow['rdate'] . "' ,tradervalue = ". $tradervalue ." where merchant_id = " . $row['merchant_id'] . " and trader_id = '". $row['trader_id'] . "' ;");
			
			// var_dump($row);
			// die();
			// }
			
			
		}
	
	}
	echo 'Done with Trader Value process!<br>';
}


function normalizeAffiliatesGroupId(){
	//update data reg group id with affiliates group id
	echo 'Start processing groups fix<br>';
	
	$sql = function_mysql_query("update data_reg dg left join affiliates aff on dg.affiliate_id = aff.id set dg.ctag = 	CONCAT(  'a', dg.affiliate_id,  '-b', SUBSTRING_INDEX( dg.ctag,  '-b' , -1 ) ) , 	dg.group_id = aff.group_id where dg.ctag not like concat('%',dg.affiliate_id , '%')");
	
	//update data sales group id with affiliates group id
	$sql = function_mysql_query("update data_sales ds left join affiliates aff on ds.affiliate_id = aff.id set ds.ctag = 	CONCAT(  'a', ds.affiliate_id,  '-b', SUBSTRING_INDEX( ds.ctag,  '-b' , -1 ) ) ,ds.group_id = aff.group_id where ds.ctag not like concat('%',ds.affiliate_id , '%')");
	
	//update data stats group id with affiliates group id
	$sql = function_mysql_query("update data_stats dt left join affiliates aff on dt.affiliate_id = aff.id set dt.ctag = 	CONCAT(  'a', dt.affiliate_id,  '-b', SUBSTRING_INDEX( dt.ctag,  '-b' , -1 ) ) ,dt.group_id = aff.group_id where dt.ctag not like concat('%',dt.affiliate_id , '%')");
	
	//update data stats group id with affiliates group id
	$sql = function_mysql_query("update data_sales_pending dp left join affiliates aff on dp.affiliate_id = aff.id set dp.ctag = 	CONCAT(  'a', dp.affiliate_id,  '-b', SUBSTRING_INDEX( dp.ctag,  '-b' , -1 ) ) , dp.group_id = aff.group_id where dp.ctag not like concat('%',dp.affiliate_id , '%')");
	
}	

function generateInstallations($array,$generateDistinct=false){



	$from = $array['from'];
	$to = $array['to'];
	$affiliate_id = $array['affiliate_id'];
	$merchant_id = $array['merchant_id'];
	$group_id = $array['group_id'];
	$profile_id = $array['profile_id'];
	$banner_id = $array['banner_id'];
	$country_id = $array['country_id'];
	$type = $array['type'];
	
		$sql = "select " . ($generateDistinct ? " distinct(di.affiliate_id) as id  " : " di.* " ).' from data_install di
														WHERE 1=1 ' 
														
														.(!empty($merchant_id) ?  '  and  di.merchant_id = ' . $merchant_id : "")
														.(!empty($affiliate_id) ?  '  and  di.affiliate_id = ' . $affiliate_id : "")
														
														
														.(!empty($trader_id) ?  '  and  di.trader_id = ' . $trader_id : "")
														.(!empty($banner_id) ?  '  and  di.banner_id = ' . $banner_id : "")
														.(!empty($country_id) ?  '  and  di.country_id = ' . $country_id : "")
														.(!empty($profile_id) ?  '  and  di.profile_id = ' . $profile_id : "")
														.(!empty($group_id) ?  '  and  di.group_id = ' . $group_id : "")
														.(!empty($type) ?  '  and  di.type = "' . $type.'" ' : "")
														.(!empty($from) ?   ' AND di.rdate >"' . $from . '" ' : "" )
														.(!empty($to) ?   ' AND di.rdate <"' . $to . '" ' : "" )
														.' 
														
														';

$rsc=function_mysql_query($sql,__FILE__,__FUNCTION__) ;
$aa =array();
while ($row = mysql_fetch_assoc($rsc)){
	$aa[$row['id']] = $row;
}

		
	return $aa;
	
	
	
	
}
function getQualifications($selectedType,$isMerchant=0) {
global $set;

	$l=0;
				$isOneOfMerchantsForex = false;
					$isForexOrBinary = false;
					$counter['count'] = 0;
				$merchants = getMerchants(0,0);
				// var_dump($merchants);
				// die();
				foreach ($merchants as $merchantww)  {
					$l++;
				$counter['count'] ++ ;
					if (strtolower($merchantww['producttype']) == 'forex' && $isOneOfMerchantsForex == false) {
						$isOneOfMerchantsForex = true;
					}
					
					$IsMoreThanOneBrand = $counter['count'] > 1 ? true : false;
					
						if (
						'forex'  == strtolower($merchantww['producttype']) ||
						'binary' == strtolower($merchantww['producttype']) ||
						'binaryoption' == strtolower($merchantww['producttype']) ||
						'forex'  == strtolower($merchantww['type']) ||
						'binary' == strtolower($merchantww['type'])
					) {
						$isForexOrBinary = true;
					}
				}
				
					$qoptions = strtolower($set->availableQualifications);
	return '
										<select  id="empnt" name="db[qualify_type]" style="width: 292px;"> ' .
							 (strpos($qoptions,'-wire')>0  ? '' : ' <option value=""  '.($selectedType == "" ? "selected" : ''). '>'.lang('None').'</option> ' ) .
							 (strpos($qoptions,'-wire')>0  ? '' : ($isMerchant==0 ? '<option value="default" '.($selectedType == "default" ? 'selected' : ''). '>'.lang('Merchant Default').'</option>' : '' ) ) .
							 (strpos($qoptions,'-trades')>0  ? '' : ' <option value="trades"  '.($selectedType == "trades" ? "selected" : ''). '>'.lang('Trades').'</option> ' ) .
							 (strpos($qoptions,'-totalminimumdeposit')>0  ? '' : ' <option value="totalmd"  '.($selectedType == "totalmd" ? "selected" : ''). '>'.lang('Total Minimum Deposit').'</option> ' ) .
							 (strpos($qoptions,'-volume')>0  ? '' : ' <option value="volume"  '.($selectedType == "volume" ? "selected" : ''). '>'.lang('Volume').'</option> ' ) .
							 (strpos($qoptions,'-lots')>0  && $isOneOfMerchantsForex ? '' : ' <option value="lots"  '.($selectedType == "lots" ? "selected" : ''). '>'.lang('Lots').'</option> ' ) .
											
									'		
									
											</select>';
}
?>