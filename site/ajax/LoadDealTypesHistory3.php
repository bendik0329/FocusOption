<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';

/**
 * Load Deal-Types history via ajax call.
 */
if (
	isset($_GET['affiliate_id'])      && !empty($_GET['affiliate_id']) && 
	isset($_GET['merchant_id'])       && !empty($_GET['merchant_id'])  && 
	is_numeric($_GET['affiliate_id']) && is_numeric($_GET['merchant_id'])
 ) {
	
	require '../common/database.php';
	require '../func/func_string.php';
	require '../func/func_global.php';
	
	$sql = 'SELECT LOWER(type) AS type, LOWER(producttype) AS producttype FROM merchants ' 
	     . 'WHERE id = ' . $_GET['merchant_id']
		 . ' LIMIT 0, 1;';
		 
	$arrMerchantData = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
	$isForexOrBinary = 'binary' == $arrMerchantData['producttype'] || 
					   'forex'  == $arrMerchantData['producttype'] || 
					   'binary' == $arrMerchantData['type'] || 
					   'forex'  == $arrMerchantData['type'];
	
	$isAdmin                         = isset($_GET['is_admin'])  && !empty($_GET['is_admin']);
	$strDealTypeHistoryDiv           = '';
	$strDealTypeHistoryTable         = '';
	$strDealTypeHistoryScript        = '';
	$intLargestResultSetCount        = 0;
	$arrDealType_min_cpa             = array();
	$arrDealType_cpa                 = array();
	$arrDealType_d_cpa               = array();
	$arrDealType_revenue             = array();
	$arrDealType_pnl                 = array();
	$arrDealType_cpi                 = array();
	$arrDealType_cpl                 = array();
	$arrDealType_cpm                 = array();
	$arrDealType_cpc                 = array();
	$arrDealType_lots      = array();
	$arrDealType_tier      = array();
	$arrDealType_revenue_spread      = array();
	$arrDealType_positions_rev_share = array();
	
	
	
	$numOfTicks=10;
	
	$where = "";
	if(isset($_GET['startdate']) && !empty($_GET['startdate']) && $_GET['startdate']!='undefined') $where .= " and rdate >= '" . $_GET['startdate'] ."'";
	if(isset($_GET['enddate']) && !empty($_GET['enddate']) && $_GET['enddate']!='undefined') $where .= " and rdate <= '" . $_GET['enddate'] ."'";
	
	
	$arrTimeLine = array();
	$rdate = $_GET['rdate'];//affiliate deal
	
	$regTimelineDt = date("Y-m-d" , strtotime($rdate));
	
	$boolPrevDeal = false;
	
	$tooltip = array();
	
	$dealsArray= array('min_cpa'=>'Min CPA','cpa'=>'CPA','dcpa'=>"DCPA",'revenue'=>"Revenue",'pnl'=>"PNL",'cpi'=>"CPI",'cpl'=>"CPL",'cpm'=>"CPM",'cpc'=>"CPC",'lots'=>"LOTS",'revenue_spread'=>"Revenue Spread",'positions_rev_share'=>"Positions Rev Share");
	$lastRunEndDate = date('Y-m-d H:i:s');
	$most_min_date = date('Y-m-d H:i:s');
	$most_max_date = date('Y-m-d H:i:s');
	$info = "";
	
	foreach ($dealsArray as $deal=>$dealname) {
		
		
		
		if(($deal=="lots" || $deal =="positions_rev_share") && !$isForexOrBinary) continue;
		$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, valid, id AS id FROM `affiliates_deals` 
				WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = '".$deal."' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
				". $where ." GROUP BY rdate ORDER BY rdate DESC";
		
		$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
		
		while ($arrRow = mysql_fetch_assoc($resource)) {
				//if (is_numeric($arrRow['amount'])) {
					$deal = $dealname;
					if(strtolower($deal) == "revenue"){
						$deal = "Net Deposit";
					}
					elseif(strtolower($deal) == "positions rev share"){
						$deal = "Position Rev Share";
					}
					elseif(strtolower($deal) == "min cpa"){
						$deal = "Minimum Deposit";
					}
					$fetchedArrDealType[$deal][] = array('deal'=>$deal,'amount' => $arrRow['valid']?$arrRow['amount']:0, 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], 'tier_amount' => $arrRow['tier_amount']);
				//}
				unset($arrRow);
		}
	}
	
	//TIER
	$sql = "SELECT valid,tier_amount,tier_pcpa,CONVERT(SUBSTRING_INDEX( tier_amount,  '-', 1 ),UNSIGNED INTEGER) as tierorder,IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, 
			rdate AS rdate, id AS id,valid,tier_pcpa as pcpa FROM `affiliates_deals` 
			WHERE  affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'tier' AND merchant_id = " . $_GET['merchant_id'] . " and tier_amount !='0-0' and amount>-1 and valid=1
			 ". $where ." ORDER BY tierorder DESC";//tierorder;";
	
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	
	$tr =0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		$deal = "Tier";
		$fetchedArrDealType[$deal][] = array('deal'=>$deal,'amount' => $arrRow['valid']?$arrRow['amount']:0, 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], 'tier_amount' => $arrRow['tier_amount'],'tier_pcpa' => $arrRow['tier_pcpa']);
	}

	$intLargestResultSetCount = $intLargestResultSetCount > count($fetchedArrDealType[$deal]) ? $intLargestResultSetCount : count($fetchedArrDealType[$deal]);
	
	$colors = array('black','yellow','blue','green','red','purple','orange','brown','lightblue','gray');
	
	$most_min_date = date('Y-m-d H:i:s');
	$most_max_date = date('Y-m-d H:i:s');
	$processedDealArray = array();
	
	if(!empty($fetchedArrDealType)){
	foreach ($fetchedArrDealType as $dealTypeArr=>$arr) {
	
				$lastRunEndDate = date('Y-m-d H:i:s');
				$times = array();
			
			foreach ($arr as $deal){
	
				$deal['ending_time'] = strtotime($lastRunEndDate)*1000;
				$deal['starting_time'] = strtotime($deal['rdate'])*1000;
				$deal['end_date'] = ($lastRunEndDate);
				$deal['deal'] = $deal['deal'];
			
				
				if ($deal['amount']==0)
					$deal['color'] = "lightgrey";
				else
					$deal['color'] = "rgb(31, 119, 180)";
				
			
				$times['times'][] = $deal;
			
				$times['label'] = $dealTypeArr;
				$lastRunEndDate = $deal['rdate'];
				$most_min_date = $deal['rdate']<$most_min_date ? $deal['rdate'] : $most_min_date;
			// }
		}
		$processedDealArray[] =  $times;
		
	}
	
	}
	
	if(!empty($processedDealArray)){
		if(isset($_GET['startdate']) && !empty($_GET['startdate']) && $_GET['startdate']!='undefined')
			$info['startingTime'] = strtotime($_GET['startdate']);
		else
			$info['startingTime'] = strtotime($most_min_date);
		
		if(isset($_GET['enddate']) && !empty($_GET['enddate']) && $_GET['enddate']!='undefined')
			$info['CurrentTime'] = strtotime($_GET['enddate']);
		else
			$info['CurrentTime'] = strtotime($most_max_date);
	}
	
	
	$jsonData = $processedDealArray;
	$info = $info;
	if(empty($fetchedArrDealType))
		echo json_encode(array("noData" => 1));
	else
		echo json_encode(array("jsonData" => $jsonData ,"info"=>$info));
	exit;
 }

?>