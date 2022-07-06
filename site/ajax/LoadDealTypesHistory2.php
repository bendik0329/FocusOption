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
	$arrDealType_cpl                 = array();
	$arrDealType_cpm                 = array();
	$arrDealType_cpc                 = array();
	$arrDealType_lots      = array();
	$arrDealType_tier      = array();
	$arrDealType_revenue_spread      = array();
	$arrDealType_positions_rev_share = array();
	
	$arrTimeLine = array();
	$rdate = $_GET['rdate'];//affiliate deal
	
	$regTimelineDt = date("Y-m-d" , strtotime($rdate));
	
	$boolPrevDeal = false;
	
	$tooltip = array();
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
	        WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'min_cpa' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$mc = 0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$mc = 0;
			}
			
			$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'min_cpa');
			if($mc==0)
				$arrDealType_min_cpa[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'current'=>1);
			else
				$arrDealType_min_cpa[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//if($mc == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('Minimum Deposit');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('Minimum Deposit') . "-<span class='red'>" . lang('INACTIVE') ."</span>";
				}
			//}
			$mc++;
			

		//}
		unset($arrRow);
	}
	
	$intLargestResultSetCount = count($arrDealType_min_cpa);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals`  
	        WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'cpa' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$ifMerchantDefault = false;
	$cnt  = 0;
	
	$cntMerchantDefault = 0;
	$cp = 0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		/* if($arrRow['amount'] == lang('Merchant Default')){
			$cntMerchantDefault  ++;
		} */
		//if (is_numeric($arrRow['amount'])) {
			
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$cp = 0;
			}
			
			$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'cpa');//,'merchantDefaultCnt' => $cntMerchantDefault);
			
			//$arrDealType_cpa[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);//,'merchantDefaultCnt' => $cntMerchantDefault);
			if($cp == 0)
				$arrDealType_cpa[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);//,'merchantDefaultCnt' => $cntMerchantDefault);
			else
				$arrDealType_cpa[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);//,'merchantDefaultCnt' => $cntMerchantDefault);
			
			
			//if($cp == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('CPA');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('CPA') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$cp++;
			
		//}
		unset($arrRow);
	}
	
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_cpa) ? $intLargestResultSetCount : count($arrDealType_cpa);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
	       WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'dcpa' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
		   GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$dcp = 0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$dcp = 0;
			}
			
			$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'d_cpa');
			
			if($dcpa ==0)
				$arrDealType_d_cpa[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
			else
				$arrDealType_d_cpa[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//if($dcp == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('DCPA');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('DCPA') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
		//	}
			$dcp++;
			
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_d_cpa) ? $intLargestResultSetCount : count($arrDealType_d_cpa);
	
	
		
	
	
	
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'revenue' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$rv = 0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$rv = 0;
			}
			
			$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'revenue');
			//$arrDealType_revenue[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			if($rv==0)
				$arrDealType_revenue[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
			else
				$arrDealType_revenue[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			
			//if($rv == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('Revenue');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('Revenue') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$rv++;
		//}
		unset($arrRow);
	}

	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_revenue) ? $intLargestResultSetCount : count($arrDealType_revenue);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'cpl'  AND merchant_id = " . $_GET['merchant_id'] . "  and  amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$cl = 0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$cl = 0;
			}
			$arrTimeLine[$timelineDt][] = array('amount' =>$arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'cpl');
			//$arrDealType_cpl[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			if($cl==0)
				$arrDealType_cpl[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
			else
				$arrDealType_cpl[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//if($cl == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('CPL');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('CPL') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$cl++;
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_cpl) ? $intLargestResultSetCount : count($arrDealType_cpl);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'cpm'  AND merchant_id = " . $_GET['merchant_id'] . "  and  amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$cm = 0 ;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
					$cm = 0 ;
			}
			$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'cpm');
			//$arrDealType_cpm[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			if($cm==0)
			$arrDealType_cpm[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
			else
			$arrDealType_cpm[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//if($cm == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('CPM');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('CPM') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$cm++;
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_cpm) ? $intLargestResultSetCount : count($arrDealType_cpm);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
	       WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'pnl' AND merchant_id = " . $_GET['merchant_id'] . "  and  amount>-1
		   GROUP BY rdate ORDER BY rdate DESC;";
		   // die ($sql);
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$pn = 0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$pn = 0;
			}
			$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'pnl');
			//$arrDealType_pnl[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			if($pn == 0)
			$arrDealType_pnl[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
			else
			$arrDealType_pnl[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//if($pn == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('PNL');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('PNL') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$pn++;
		//}
		unset($arrRow);
	}
		$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_pnl) ? $intLargestResultSetCount : count($arrDealType_pnl);
	
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'cpc' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$cc = 0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$cc = 0;
			}
			$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'cpc');
			//$arrDealType_cpc[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			if($cc==0)
			$arrDealType_cpc[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
			else
			$arrDealType_cpc[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//if($cc == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('CPC');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('CPC') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$cc++;
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_cpc) ? $intLargestResultSetCount : count($arrDealType_cpc);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'revenue_spread' AND merchant_id = " . $_GET['merchant_id'] . "  and  amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$rs = 0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$rs = 0;
			}
			$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'rev_spread');
			//$arrDealType_revenue_spread[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			if($rs==0)
			$arrDealType_revenue_spread[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
			else
			$arrDealType_revenue_spread[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//if($rs == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('Revenue Spread');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('Revenue Spread') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$rs++;
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_revenue_spread) ? $intLargestResultSetCount : count($arrDealType_revenue_spread);


	$sql = "SELECT tier_amount,CONVERT(SUBSTRING_INDEX( tier_amount,  '-', 1 ),UNSIGNED INTEGER) as tierorder,IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, 
			rdate AS rdate, id AS id,valid FROM `affiliates_deals` 
			WHERE  affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'tier' AND merchant_id = " . $_GET['merchant_id'] . " and tier_amount !='0-0' and amount>-1
			 ORDER BY tierorder DESC";//tierorder;";
			 
	// die ($sql);
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	$tr =0;
	while ($arrRow = mysql_fetch_assoc($resource)) {
	
		//if (is_numeric($arrRow['amount'])) {
			$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$tr =0;
			}
			$arrTimeLine[$timelineDt][] =  array('amount' =>$arrRow['valid']?$arrRow['amount']:0, 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], 'tier_amount' => $arrRow['tier_amount'],'deal_type'=>'tier');
			//$arrDealType_tier[] = array('amount' => $arrRow['valid']?$arrRow['amount']:0, 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], 'tier_amount' => $arrRow['tier_amount']);
			if($tr == 0)
			$arrDealType_tier[$timelineDt][] = array('amount' => $arrRow['valid']?$arrRow['amount']:0, 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], 'tier_amount' => $arrRow['tier_amount'], "current"=>1);
			else
			$arrDealType_tier[$timelineDt][] = array('amount' => $arrRow['valid']?$arrRow['amount']:0, 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], 'tier_amount' => $arrRow['tier_amount']);
				
				if($arrRow['amount']  > 0 &&  $arrRow['pcpa']>0){
					$tooltip[$timelineDt][]= lang('Tier');
				}
				else if($arrRow['amount'] == 0 ||   $arrRow['pcpa']==0){
					$tooltip[$timelineDt][]= lang('Tier') . "-<span class='red'>" . lang('Inactive')."</span>";
				}
			
			$tr++;
		//}
		unset($arrRow);
	}
	
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_tier) ? $intLargestResultSetCount : count($arrDealType_tier);

	
	if ($isForexOrBinary) {
		$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
				WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'positions_rev_share' AND merchant_id = " . $_GET['merchant_id'] . " 
				GROUP BY rdate ORDER BY rdate DESC;";
		$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
		$prs = 0;
		while ($arrRow = mysql_fetch_assoc($resource)) {
			//if (is_numeric($arrRow['amount'])) {
				$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
					$prs = 0;
			}
				$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'rev_share');
				//$arrDealType_positions_rev_share[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
				if($prs == 0)
				$arrDealType_positions_rev_share[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
				else
				$arrDealType_positions_rev_share[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
				//if($prs == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('Position Revenue Share');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('Position Revenue Share') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$prs++;
			//}
			unset($arrRow);
		}
		$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_positions_rev_share) ? $intLargestResultSetCount : count($arrDealType_positions_rev_share);
		
		
			$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
				WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'lots' AND merchant_id = " . $_GET['merchant_id'] . " 
				GROUP BY rdate ORDER BY rdate DESC;";
		$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
		$lt = 0;
		while ($arrRow = mysql_fetch_assoc($resource)) {
			//if (is_numeric($arrRow['amount'])) {
				$timelineDt = date("Y-m-d" , strtotime($arrRow['rdate']));
			if($regTimelineDt >= $timelineDt){
				$timelineDt = $regTimelineDt;
				$boolPrevDeal = true;
				$lt = 0;
			}
				$arrTimeLine[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'],'deal_type'=>'lots');
				//$arrDealType_lots[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
				if($lt==0)
				$arrDealType_lots[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], "current"=>1);
				else
				$arrDealType_lots[$timelineDt][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
				//if($lt == 0){
				if($arrRow['amount'] > 0){
					$tooltip[$timelineDt][]= lang('Lots');
				}
				else if($arrRow['amount'] == 0){
					$tooltip[$timelineDt][]= lang('Lots') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			//}
			$lt++;
			//}
			unset($arrRow);
		}
		$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_lots) ? $intLargestResultSetCount : count($arrDealType_lots);
		
		
	}
	
	if(!$boolPrevDeal){
		
	$arrDealTypeDefaults = getMerchantDealTypeDefaults();
	
	$timelineDt = $regTimelineDt;
			if(!empty($arrDealTypeDefaults['min_cpa_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['min_cpa_amount'], 'rdate' => $arrRow['rdate']);
				$arrDealType_min_cpa[$timelineDt][] = array('amount' => $arrDealTypeDefaults['min_cpa_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['min_cpa_amount']> 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('Minimum Deposit');
				}
				else if($arrDealTypeDefaults['min_cpa_amount'] == 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('Minimum Deposit') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['cpa_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['cpa_amount'], 'rdate' => $rdate);
				$arrDealType_cpa[$timelineDt][] = array('amount' => $arrDealTypeDefaults['cpa_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['cpa_amount'] > 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('CPA');
				}
				else if($arrDealTypeDefaults['cpa_amount'] == 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('CPA') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['dcpa_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['dcpa_amount'], 'rdate' => $rdate);
				$arrDealType_d_cpa[$timelineDt][] = array('amount' => $arrDealTypeDefaults['dcpa_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['dcpa_amount']> 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('DCPA');
				}
				else if($arrDealTypeDefaults['dcpa_amount'] == 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('DCPA') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['revenue_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['revenue_amount'], 'rdate' => $rdate);
				$arrDealType_revenue[$timelineDt][] = array('amount' => $arrDealTypeDefaults['revenue_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['revenue_amount']> 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('Revenue');
				}
				else if($arrDealTypeDefaults['revenue_amount'] == 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('Revenue') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['lots_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['lots_amount'], 'rdate' => $rdate);
				$arrDealType_lots[$timelineDt][] = array('amount' => $arrDealTypeDefaults['lots_amount'], 'rdate' => $rdate,'default'=>true);
				if( $arrDealTypeDefaults['lots_amount']> 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('Lots');
				}
				else if( $arrDealTypeDefaults['lots_amount'] == 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('Lots') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['pnl_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['pnl_amount'], 'rdate' => $rdate);
				$arrDealType_pnl[$timelineDt][] = array('amount' => $arrDealTypeDefaults['pnl_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['pnl_amount']> 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('PNL');
				}
				else if( $arrDealTypeDefaults['pnl_amount'] == 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('PNL') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['revenue_spread_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['revenue_spread_amount'], 'rdate' => $rdate);
				$arrDealType_revenue_spread[$timelineDt][] = array('amount' => $arrDealTypeDefaults['revenue_spread_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['revenue_spread_amount']> 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('Revenue Spread');
				}
				else if( $arrDealTypeDefaults['revenue_spread_amount'] == 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('Revenue Spread') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['cpl_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['cpl_amount'], 'rdate' => $rdate);
				$arrDealType_cpl[$timelineDt][] = array('amount' => $arrDealTypeDefaults['cpl_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['cpl_amount']> 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('CPL');
				}
				else if( $arrDealTypeDefaults['cpl_amount'] == 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('CPL') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['cpc_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['cpc_amount'], 'rdate' => $rdate);
				$arrDealType_cpc[$timelineDt][] = array('amount' => $arrDealTypeDefaults['cpc_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['cpc_amount']> 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('CPC');
				}
				else if( $arrDealTypeDefaults['cpc_amount'] == 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('CPC') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['cpm_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['cpm_amount'], 'rdate' => $rdate);
				$arrDealType_cpm[$timelineDt][] = array('amount' => $arrDealTypeDefaults['cpm_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['cpm_amount']> 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('CPM');
				}
				else if( $arrDealTypeDefaults['cpm_amount'] == 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('CPM') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(!empty($arrDealTypeDefaults['positions_rev_share_amount'])){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['positions_rev_share_amount'], 'rdate' => $rdate);
				$arrDealType_positions_rev_share[$timelineDt][] = array('amount' => $arrDealTypeDefaults['positions_rev_share_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['positions_rev_share_amount']> 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('Position Revenue Share');
				}
				else if( $arrDealTypeDefaults['positions_rev_share_amount'] == 0){
					$tooltip[$timelineDt][]=lang('Merchant Default') . " - ". lang('Position Revenue Share') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
			if(isset($arrDealTypeDefaults['tier_amount']) && $arrDealTypeDefaults['tier_amount'] != '0-0'){
				$arrTimeLine[$timelineDt][] = array('amount' => $arrDealTypeDefaults['tier_amount'], 'rdate' => $rdate);
				$arrDealType_tier[$timelineDt][] = array('amount' => $arrDealTypeDefaults['tier_amount'], 'rdate' => $rdate,'default'=>true);
				if($arrDealTypeDefaults['tier_amount']> 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('TIER');
				}
				else if( $arrDealTypeDefaults['tier_amount'] == 0){
					$tooltip[$timelineDt][]= lang('Merchant Default') . " - ".lang('TIER') . "-<span class='red'>" . lang('INACTIVE')."</span>";
				}
			}
	}
	
	
	$boolAtLeastOneRowExists  = false;
	
	
			$timelineTxt .= '
			<style>
			a.showDelete {
   position: absolute;
    margin-left: -24px;
    margin-top: 5px;
	
			}
			.mainTD{
				min-width:300px;
				background-color: lightgray;
			}
			.red{
				color:red;
				font-weight:bold;
			}
			.signup_txt{
				margin-top: 10px;
				font-weight: bold;
				font-style: italic;
			}
			.subTable{
				width:100%;
			}
			.subTable thead td {
				padding:5px;
			}
			.subTable .heading {
				font-size:14px;
				text-align:center;
				color:white;
			}
			.deals_rdate{
				padding:4px;
			}
			.defaultDeal td{
				padding:10px 0;
				text-align:center;
			}
			.events-content input{
				width:140px !important;
			}
			</style>
			
	<script>
			
	
			

			
			$( "#deal_types_history tr td input[type=text]" ).click(function() {
				
				
				
				
		 		$( "#deal_types_history tr td a" ).remove(".showDelete");
				// $( "input[name="data-rdate_id"]" ).remove(".showDelete");
				$( "#deal_types_history tr td a" ).remove(".showDelete");
				
				
				 
			  var a = ($(this).attr(\'data-amount_id\'));
				  if (!a) {
					  a = ($(this).attr(\'data-rdate_id\'));
				  }
				// $(this).addClass("showDelete");
				
					$(this).after("<a onclick = \"if (! confirm(\''.lang('Are you sure you want to delete').'?\')) { return false; }\" href=\"/admin/affiliates.php?act=delete&id='.$_GET['affiliate_id'].'\" class=\"showDelete\"><img src=\"images/x.png\"/></a>");
					
					var _href = $("a.showDelete").attr("href");
					$("a.showDelete").attr("href", _href + "&deldth=" + a);
				
				//$("div[data-rdate_id==\"a\"]").remove();
				//$("div[data-amount_id==\"a\"]").remove();
				//$( "#deal_types_history tr td" ).remove(".showDelete");
				//$( "#deal_types_history tr td" ).remove(".showDelete");
				
				
				
				});
	</script>';
			
		
		ksort($arrTimeLine);
		ksort($tooltip);
		
	
	$timelineTxt .= '
		 <link rel="stylesheet" href="js/horizontal-timeline/css/reset.css"> <!-- CSS reset -->
		<link rel="stylesheet" href="js/horizontal-timeline/css/style.css"> <!-- Resource style -->
		<script src="js/horizontal-timeline/js/modernizr.js"></script>
		 <link rel="stylesheet" type="text/css" href="js/tooltipster/css/tooltipster.bundle.min.css" />
		 <link rel="stylesheet" type="text/css" href="js/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
		<script type="text/javascript" src="js/tooltipster/js/tooltipster.bundle.min.js"></script>
		<section class="cd-horizontal-timeline">
	<div class="timeline">
		<div class="events-wrapper">
			<div class="events">
				<ol>
				';
				$i=0;
				foreach($arrTimeLine as $key=>$timeline){
					if(isset($tooltip[$key]))
						$tooltiptxt = implode("<br/> ",array_unique($tooltip[$key]));
					else
						$tooltiptxt ="";
				
					$timelineTxt .= '<li><a '. ($tooltiptxt!=''?'title="'. $tooltiptxt .'"':'') .' href="#0" data-date="'. date('d/m/Y',strtotime($key)) .'" '  . ($i==0?' class="selected"':'') .'>'. date('d M Y',strtotime($key)) .'</a>
					'. ($i==0?'<div class=\'signup_txt\'>'. lang('Sign Up Date') .'</div>':'') .'</li>';
					$i++;
				}
			/* 	
				if($i<9){
					for($a = $i;$a<9;$a++){
							$timelineTxt .= '<li><a  href="#0" data-date="'. date('d/m/Y',strtotime('+1 Day')) .'" '  . ($i==0?' class="selected"':'') .' style="visibility:visible">'. date('d M Y',strtotime('+1 Day')) .'</a></li>';
					}
				}
			 */
				$timelineTxt .='</ol>

				<span class="filling-line" aria-hidden="true"></span>
			</div> <!-- .events -->
		</div> <!-- .events-wrapper -->
			
		<!--ul class="cd-timeline-navigation">
			<li><a href="#0" class="prev inactive">Prev</a></li>
			<li><a href="#0" class="next">Next</a></li>
		</ul--> <!-- .cd-timeline-navigation -->
	</div> <!-- .timeline -->

	<div class="events-content">
		<ol>';
		$i=0;
		
		
		foreach($arrTimeLine as $key=>$timeline){
			$boolCurrentDeal = false;
			$boolDefaultDeal  = false;
			$timelineTxt .='<li '.  ($i==0?' class="selected"':'') .' data-date="'. date('d/m/Y',strtotime($key)) .'">';
					if ($isAdmin) {
						
						if($i===0){
							$timelineTxt .= '<tr class="first">';
							$cls = ' class = "amt" ';
						}
						else{
							$timelineTxt .= '<tr>';
							$cls = "";
						}
						$timelineTxt .=  '<table id="deal_types_history">
								<tbody><tr>';
						$mincpa = 0;
						if(isset($arrDealType_min_cpa[$key])){
								
								$isRowMarkedAsActive      = $mincpa === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_min_cpa[$key] as $k=>$row)
								{
									if($mincpa==0){
										if(!empty($arrDealType_min_cpa[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('MINIMUM DEPOSIT')) .'</td></tr>';
												}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('MINIMUM DEPOSIT')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
										if(isset($row['default']) && $row['default']==true){
											$boolDefaultDeal = true;
											$timelineTxt.= '<tr><td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
												<td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
										}
										else{
											$timelineTxt.= '<tr><td><input class = "deals_rdate" type="text" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
												<td><input type="text" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>$</td></tr>';
												if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
										}
										
									$mincpa++;
								}
								if($mincpa > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						$cpa = 0;
						if(isset($arrDealType_cpa[$key])){
								/* if(!empty($arrDealType_cpa[$key])){
									$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('CPA')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */

								foreach($arrDealType_cpa[$key] as $k=>$row)
								{
									$isRowMarkedAsActive      =  ($cpa == 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"');
									if($cpa==0){
										if(!empty($arrDealType_cpa[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('CPA')) .'</td></tr>';
												}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('CPA')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;  ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>$</td></tr>';
										if(isset($row['current'])){
													$boolCurrentDeal = true;
												}	
										}
									$cpa++;
								}
								if($cpa > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$dcpa = 0;
						if(isset($arrDealType_d_cpa[$key])){
								/* if(!empty($arrDealType_d_cpa[$key])){
									$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('DCPA')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $dcpa === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_d_cpa[$key] as $k=>$row)
								{
									if($dcpa==0){
										if(!empty($arrDealType_d_cpa[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('DCPA')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('DCPA')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;" ' .(isset($row['current'])?$isRowMarkedAsActive:''). '>' . $row['amount'] . '%</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>%</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
									}
									$dcpa++;
								}
								if($dcpa > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$revenue= 0;
						if(isset($arrDealType_revenue[$key])){
								$isRowMarkedAsActive      =  $revenue === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_revenue[$key] as $k=>$row)
								{
									if($revenue==0){
										if(!empty($arrDealType_revenue[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('REVENUE')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('REVENUE')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;"' .(isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['amount'] . '%</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>%</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
										}
									$revenue++;
								}
								if($revenue > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$cpl= 0;
						if(isset($arrDealType_cpl[$key])){
								/* if(!empty($arrDealType_cpl[$key])){
									$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('CPL')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $cpl === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_cpl[$key] as $k=>$row)
								{
									if($cpl==0){
										if(!empty($arrDealType_cpl[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('CPL')) .'</td></tr>';
												}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('CPL')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr ><td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px; "' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>$</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
									}
									$cpl++;
								}
								if($cpl > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$pnl= 0;
						if(isset($arrDealType_pnl[$key])){
								$isRowMarkedAsActive      =  $pnl=== 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_pnl[$key] as $k=>$row)
								{
									if($pnl==0){
										if(!empty($arrDealType_pnl[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('PNL')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('PNL')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 140px;" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['amount'] . '%</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>%</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
									}
									$pnl++;
								}
								if($pnl > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$cpm= 0;
						if(isset($arrDealType_cpm[$key])){
								$isRowMarkedAsActive      =  $cpm === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_cpm[$key] as $k=>$row)
								{
									if($cpm==0){
										if(!empty($arrDealType_cpm[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('CPM')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('CPM')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>$</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
											
									}
									$cpm++;
								}
								if($cpm > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$cpc= 0;
						if(isset($arrDealType_cpc[$key])){
								$isRowMarkedAsActive      =  $cpl === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_cpc[$key] as $k=>$row)
								{
									if($cpc==0){
										if(!empty($arrDealType_cpc[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('CPC')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('CPC')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 140px;"  ' .(isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>$</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
									}
									$cpc++;
								}
								if($cpc > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$revenue_spread= 0;
						if(isset($arrDealType_revenue_spread[$key])){
								$isRowMarkedAsActive      =  $revenue_spread === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_revenue_spread[$key] as $k=>$row)
								{
									if($revenue_spread==0){
										if(!empty($arrDealType_revenue_spread[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('REVENUE SPREAD')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('REVENUE SPREAD')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';	
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 155px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 155px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['amount'] . '%</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>%</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
									}
									$revenue_spread++;
								}
								if($revenue_spread > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$lots= 0;
						if(isset($arrDealType_lots[$key])){
								$isRowMarkedAsActive      =  $lots === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_lots[$key] as $k=>$row)
								{
									if($lots==0){
										if(!empty($arrDealType_lots[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('LOTS')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('LOTS')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 140px;" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>$</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
									}
									$lots++;
								}
								if($lots > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$rev_share= 0;
						if(isset($arrDealType_positions_rev_share[$key])){
								/* if(!empty($arrDealType_positions_rev_share[$key])){
									$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('POSITIONS REV SHARE')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $rev_share === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_positions_rev_share[$key] as $k=>$row)
								{
									if($rev_share==0){
										if(!empty($arrDealType_positions_rev_share[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('POSITIONS REVENUE SHARE')) .'</td></tr>';
												}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('POSITIONS REVENUE SHARE')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['amount'] . '%</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '" '. $cls .'/>%</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
												}
									}
									$rev_share++;
								}
								if($rev_share > 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$tier= 0;
						if(isset($arrDealType_tier[$key])){
								/* if(!empty($arrDealType_tier[$key])){
									$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>
									<tr><td colspan="3" class="heading">'. strtoupper(lang('TIER')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('TIER AMOUNTS')  . '</td><td style="color:white;">' . lang('TIER')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $tier === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_tier[$key] as $k=>$row)
								{
									if($tier==0){
										if(!empty($arrDealType_tier[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="3" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('TIER')) .'</td></tr>';
												}
											else{
												$timelineTxt.='<tr><td colspan="3" class="heading">'. strtoupper(lang('TIER')) .'</td></tr>';
											}
												$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('TIER AMOUNTS')  . '</td><td style="color:white;">' . lang('TIER')  . '</td></tr></thead><tbody>';
										}
									}
									if(isset($row['default']) && $row['default']==true){
										$boolDefaultDeal = true;
										$timelineTxt.= '<tr><td  style="width: 140px;" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td style="width: 140px;" '.  (isset($row['current'])?$isRowMarkedAsActive:'') .'>$' . $row['amount'] . '</td></tr>';
									}
									else{
									$timelineTxt .= '<tr><td><input class = "deals_rdate" type="text" ' .(isset($row['current'])?$isRowMarkedAsActive:'') . ' data-rdate_id="' . $row['id'] . '" value="' . $row['rdate'] . '" readonly/></td>
											<td><input type="text" ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' data-tier_amount_id="' . $row['id'] . '" value="' . $row['tier_amount'] . '" '. $cls .'/>$</td>
											<td><input type="text" '. (isset($row['current'])?$isRowMarkedAsActive:'') .' data-amount_id="' . $row['id'] . '" value="' . $row['amount'] . '"'. $cls .' />$</td></tr>';
											if(isset($row['current'])){
													$boolCurrentDeal = true;
											}
									}
									$tier++;
								}
								if($tier> 0){
									$boolAtLeastOneRowExists = true;
										$timelineTxt .= "</tbody></table></td>";
								}
						}							
				}
				else{
					
					if($i===0){
							$timelineTxt .= '<tr class="first">';
							$cls = ' class = "amt" ';
						}
						else{
							$timelineTxt .= '<tr>';
							$cls = "";
						}
						$timelineTxt .=  '<table id="deal_types_history">
								<tbody><tr>';
						$mincpa = 0;
						if(isset($arrDealType_min_cpa[$key])){
								/* if(!empty($arrDealType_cpa[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('MINIMUM DEPOSIT')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      = $mincpa === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_min_cpa[$key] as $k=>$row)
								{
									if($mincpa==0){
										if(!empty($arrDealType_min_cpa[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('MINIMUM DEPOSIT')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('MINIMUM DEPOSIT')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt.= '<tr><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>'. $row['rdate'] . '</td><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									$mincpa++;
								}
								if($mincpa > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
								
						}
						$cpa = 0;
						if(isset($arrDealType_cpa[$key])){
								/* if(!empty($arrDealType_cpa[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('CPA')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $cpa === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_cpa[$key] as $k=>$row)
								{
									if($cpa==0){
										if(!empty($arrDealType_cpa[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('CPA')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('MINIMUM DEPOSIT')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									$cpa++;
								}
								if($cpa > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$dcpa = 0;
						if(isset($arrDealType_d_cpa[$key])){
								/* if(!empty($arrDealType_d_cpa[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('DCPA')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $dcpa === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_d_cpa[$key] as $k=>$row)
								{
									if($dcpa==0){
										if(!empty($arrDealType_d_cpa[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('DCPA')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('DCPA')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['amount'] . '%</td></tr>';
									$dcpa++;
								}
								if($dcpa > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$revenue= 0;
						if(isset($arrDealType_revenue[$key])){
								/* if(!empty($arrDealType_revenue[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('REVENUE')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $revenue === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_revenue[$key] as $k=>$row)
								{
									if($revenue==0){
										if(!empty($arrDealType_revenue[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('REVENUE')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('REVENUE')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' .(isset($row['current'])?$isRowMarkedAsActive:''). '>' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' >' . $row['amount'] . '%</td></tr>';
									$revenue++;
								}
								if($revenue > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$cpl= 0;
						if(isset($arrDealType_cpl[$key])){
								/* if(!empty($arrDealType_cpl[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('CPL')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $cpl === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_cpl[$key] as $k=>$row)
								{
									if($cpl==0){
										if(!empty($arrDealType_cpl[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('CPL')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('CPL')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									$cpl++;
								}
								if($cpl > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$pnl= 0;
						if(isset($arrDealType_pnl[$key])){
								/* if(!empty($arrDealType_pnl[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('PNL')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $pnl=== 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_pnl[$key] as $k=>$row)
								{
									if($pnl==0){
										if(!empty($arrDealType_pnl[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('PNL')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('PNL')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' .(isset($row['current'])?$isRowMarkedAsActive:''). '>' . $row['rdate'] . '></td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['amount'] . '%</td></tr>';
									$pnl++;
								}
								if($pnl > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$cpm= 0;
						if(isset($arrDealType_cpm[$key])){
								/* if(!empty($arrDealType_cpm[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('CPM')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $cpm === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_cpm[$key] as $k=>$row)
								{
									if($cpm==0){
										if(!empty($arrDealType_cpl[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('CPM')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('CPM')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									$cpm++;
								}
								if($cpm > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$cpc= 0;
						if(isset($arrDealType_cpc[$key])){
								/* if(!empty($arrDealType_cpc[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('CPC')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $cpl === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_cpc[$key] as $k=>$row)
								{
									if($cpc==0){
										if(!empty($arrDealType_cpc[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('CPC')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('CPC')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' .(isset($row['current'])?$isRowMarkedAsActive:''). ' >' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' >$' . $row['amount'] . '</td></tr>';
									$cpc++;
								}
								if($cpc > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$revenue_spread= 0;
						if(isset($arrDealType_revenue_spread[$key])){
								/* if(!empty($arrDealType_revenue_spread[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('REVENUE SPREAD')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $revenue_spread === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_revenue_spread[$key] as $k=>$row)
								{
									if($revenue_spread==0){
										if(!empty($arrDealType_revenue_spread[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('REVENUE SPREAD')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('REVENUE SPREAD')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' .(isset($row['current'])?$isRowMarkedAsActive:'') . ' >' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' >' . $row['amount'] . '%</td></tr>';
									$revenue_spread++;
								}
								if($revenue_spread > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$lots= 0;
						if(isset($arrDealType_lots[$key])){
								/* if(!empty($arrDealType_lots[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('LOTS')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $lots === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_lots[$key] as $k=>$row)
								{
									if($lots==0){
										if(!empty($arrDealType_lots[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('LOTS')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('LOTS')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['amount'] . '</td></tr>';
									$lots++;
								}
								if($lots > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$rev_share= 0;
						if(isset($arrDealType_positions_rev_share[$key])){
								/* if(!empty($arrDealType_positions_rev_share[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="2" class="heading">'. strtoupper(lang('POSITIONS REV SHARE')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('DEPOSIT')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $rev_share === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_positions_rev_share[$key] as $k=>$row)
								{
									if($rev_share==0){
										if(!empty($arrDealType_positions_rev_share[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('POSITIONS REV SHARE')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('POSITIONS REV SHARE')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' >' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' >' . $row['amount'] . '%</td></tr>';
									$rev_share++;
								}
								if($rev_share > 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}
						
						$tier= 0;
						if(isset($arrDealType_tier[$key])){
								/* if(!empty($arrDealType_tier[$key])){
									$timelineTxt .='<td valign="top"><table class="subTable"><thead>
									<tr><td colspan="3" class="heading">'. strtoupper(lang('TIER')) .'</td></tr>
									<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('TIER AMOUNTS')  . '</td><td style="color:white;">' . lang('TIER')  . '</td></tr></thead><tbody>';
								} */
								$isRowMarkedAsActive      =  $tier === 0 ? 'style="color: green; font-weight: bold; width: 140px;"' : 'style="width: 140px;"';
								foreach($arrDealType_tier[$key] as $k=>$row)
								{
									if($tier==0){
										if(!empty($arrDealType_tier[$key])){
											$timelineTxt .='<td valign="top" class="mainTD"><table class="subTable"><thead>';
											if(isset($row['default']) && $row['default']==true){
												$timelineTxt.='<tr><td colspan="2" class="heading">'. lang('Merchant Default Deal'). " - " . strtoupper(lang('TIER')) .'</td></tr>';
											}
											else{
												$timelineTxt.='<tr><td colspan="2" class="heading">'. strtoupper(lang('TIER')) .'</td></tr>';
											}
											$timelineTxt .= '<tr><td style="color:white;">' . strtoupper(lang('DATE')) . '</td><td style="color:white;">' . lang('COMMISSION')  . '</td></tr></thead><tbody>';
										}
									}
									$timelineTxt .= '<tr><td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . ' >' . $row['rdate'] . '</td>
											<td ' . (isset($row['current'])?$isRowMarkedAsActive:'') . '>$' . $row['tier_amount'] . '</td>
											<td '. (isset($row['current'])?$isRowMarkedAsActive:'') .' >$' . $row['amount'] . '</td></tr>';
									$tier++;
								}
								if($tier> 0){
										$timelineTxt .= "</tbody></table></td>";
								}
						}							
					
				}
			
			$timelineTxt .= $boolCurrentDeal?'<tr><td colspan="3"><label style="color: green; font-weight: bold;">* ' . lang('Currently active record marked by green') . '</label></td></tr>':'';
			$timelineTxt .= $boolDefaultDeal  ? '' : ($boolAtLeastOneRowExists
									  ? '<tr><td><input type="submit" onclick="return false;" value="' . lang('Save changes') . '"/></td></tr>'
									  : '');
			
			$timelineTxt .= '</tr></tbody></table></center>'; 
								
			$timelineTxt .='</li>';
			
			$i++;
		}	
		
	/* 	if($i<9){
			for($a = $i;$a<9;$a++){
					$timelineTxt .= '<li  data-date="'. date('d/m/Y',strtotime('+1 day')) .'"></li>';
			}
		} */
	
		$timelineTxt .='</ol>
	</div> <!-- .events-content -->
</section>
<script>

$(".events ol li").find("a").addClass("tooltip1");
$(document).ready(function(){
	$(".tooltip1").tooltipster({
		  theme: \'tooltipster-punk\',
		  contentAsHTML : true
	});
});
</script>
		<script src="js/horizontal-timeline/js/jquery.mobile.custom.min.js"></script>
		<script src="js/horizontal-timeline/js/main.js"></script> <!-- Resource jQuery -->';

	$timelineTxt .= 	'
			<link href="../css/redmond/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" />
			<link href="../css/jquery-ui-timepicker-addon.css" media="screen" rel="stylesheet" type="text/css" />
			<script type="text/javascript" src="../js/jquery-ui-timepicker-addon.js"></script>
			<script>
			$("document").ready(function(){
						$(".deals_rdate").datetimepicker({
							timeFormat: "HH:mm:ss",
							dateFormat:"yy-mm-dd",
							onClose:function(dt,inst){
								
								var id    = $.trim($(this).data("rdate_id"));
								var rdate = dt;
								return updateDealTypesHistory("rdate", id, rdate);
								
							}
						});
					
			});
			</script>
			
			';
	$timelineTxt .= !$isAdmin ? '' : '<script>
										/**
										 * Performs an ajax call in order to update chosen deal-type record.
										 *
										 * @param  string     subject
										 * @param  int        id
										 * @param  int|string value
										 * @return bool
										 */
										function updateDealTypesHistory(subject, id, value) {
											$.post("ajax/UpdateDealTypesHistory.php", 
											       {
													   subject: subject,
													   id     : id,
													   value  : value
												   },
												   function(res) {
													   try {
														    res = JSON.parse(res);
															
															if (res["success"] && subject === "rdate") {
																/* var intMerchantId = $("#select_merchant").val();
																
																$.get("ajax/LoadDealTypesHistory1.php?is_admin=1&affiliate_id=' . $_GET['affiliate_id'] . '&rdate='. $rdate .'&merchant_id=" + intMerchantId, function(res) {
																	try {
																		res = JSON.parse(res);
																		if (res["success"]) {
                                                                                                                                                    $("#div_deal_type_history").html(res["success"]);
																																					var amtMerchantDefault = false;
																																					var amtNumeric = false;
																																					$(".amt").each(function(key,ele){
																																						if(false === isNaN(parseInt($(this).val())))
																																						{
																																							amtNumeric = true;
																																						}
																																						else if($(this).val() == "'. lang('Merchant Default') .'"){
																																							amtMerchantDefault = true;
																																						}
																																					});
																																					if(amtMerchantDefault == true && amtNumeric == true){
																																						$(".amt").each(function(key,ele){
																																							if($(this).val() == "'. lang('Merchant Default') .'"){
																																								$(this).val(0);
																																							}
																																						});
																																					}
																																				
																																				
																		}
																		
																	} catch (error) {
																		console.log(error);
																	}
																}) */;
																//alert( res["success"]);
																	$.fancybox({ 
																 closeBtn:false, 
																  minWidth:"250", 
																  minHeight:"180", 
																  autoCenter: true, 
																  afterClose:function(){
																	  
																  },			  
																  content: "<div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +res["success"] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
																  });
																
															} else if (res["success"] && subject === "amount") {
																
																/* var intMerchantId = $("#select_merchant").val();
																
																$.get("ajax/LoadDealTypesHistory1.php?is_admin=1&affiliate_id=' . $_GET['affiliate_id'] . '&rdate='. $rdate .'&merchant_id=" + intMerchantId, function(res) {
																	try {
																		res = JSON.parse(res);
																		if (res["success"]) {
                                                                                                                                                    $("#div_deal_type_history").html(res["success"]);
																																					var amtMerchantDefault = false;
																																					var amtNumeric = false;
																																					$(".amt").each(function(key,ele){
																																						if(false === isNaN(parseInt($(this).val())))
																																						{
																																							amtNumeric = true;
																																						}
																																						else if($(this).val() == "'. lang('Merchant Default') .'"){
																																							amtMerchantDefault = true;
																																						}
																																					});
																																					if(amtMerchantDefault == true && amtNumeric == true){
																																						$(".amt").each(function(key,ele){
																																							if($(this).val() == "'. lang('Merchant Default') .'"){
																																								$(this).val(0);
																																							}
																																						});
																																					}
																																				
																																				
																		}
																		
																	} catch (error) {
																		console.log(error);
																	}
																}); */
																//alert(res["success"]);
																$.fancybox({ 
																 closeBtn:false, 
																  minWidth:"250", 
																  minHeight:"180", 
																  autoCenter: true, 
																  afterClose:function(){
																	 
																  },			  
																  content: "<div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +res["success"] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
																  });
																                                                                
															}
															else if (res["success"] && subject === "tier_amount") {
																
															/* 	var intMerchantId = $("#select_merchant").val();
																
																$.get("ajax/LoadDealTypesHistory1.php?is_admin=1&affiliate_id=' . $_GET['affiliate_id'] . '&rdate='. $rdate .'&merchant_id=" + intMerchantId, function(res) {
																	try {
																		res = JSON.parse(res);
																		if (res["success"]) {
                                                                                                                                                    $("#div_deal_type_history").html(res["success"]);
																																					var amtMerchantDefault = false;
																																					var amtNumeric = false;
																																					$(".amt").each(function(key,ele){
																																						if(false === isNaN(parseInt($(this).val())))
																																						{
																																							amtNumeric = true;
																																						}
																																						else if($(this).val() == "'. lang('Merchant Default') .'"){
																																							amtMerchantDefault = true;
																																						}
																																					});
																																					if(amtMerchantDefault == true && amtNumeric == true){
																																						$(".amt").each(function(key,ele){
																																							if($(this).val() == "'. lang('Merchant Default') .'"){
																																								$(this).val(0);
																																							}
																																						});
																																					}
																																				
																																				
																		}
																		
																	} catch (error) {
																		console.log(error);
																	}
																}); */
																//alert(res["success"]);
																	$.fancybox({ 
																 closeBtn:false, 
																  minWidth:"250", 
																  minHeight:"180", 
																  autoCenter: true, 
																  afterClose:function(){
																	  
																  },			  
																  content: "<div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +res["success"] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
																  });
																	
                                                                                                                                
															}
															else {
																console.log(res["error"]);
															}
														    
													   } catch (error) {
														   console.log(error);
													   }
												   });
											return false;
										}
										
										/* $("[data-rdate_id]").blur(function() {
											var id    = $.trim($(this).attr("data-rdate_id"));
											var rdate = $.trim($(this).val());
											return updateDealTypesHistory("rdate", id, rdate);
										}); */
										
										$("[data-amount_id]").change(function() {
											var id     = $.trim($(this).attr("data-amount_id"));
                                                                                        var amount = $(this).val();
                                                                                        
											if (isNaN(amount) || amount === "") {
                                                                                            amount = "NULL";
											}
											return updateDealTypesHistory("amount", id, amount);
										});
										
										$("[data-tier_amount_id]").change(function() {
											var id     = $.trim($(this).attr("data-tier_amount_id"));
											var amount = $(this).val();
                                          
											/* if (isNaN(amount) || amount === "") {
                                                                                            amount = "NULL";
											} */
											return updateDealTypesHistory("tier_amount", id, amount);
										});
								  </script>';
	
							  
	//$strDealTypeHistoryDiv .= $strDealTypeHistoryTable . $strDealTypeHistoryScript;
	//$timelineTxt .= $timelineTxt . $strDealTypeHistoryScript;
	
	
	$data = json_encode($arrDealType_tier);
	
	?>
	
	
	<?php
	
	$test .= '
		<style>
	* {
      margin: 0;
      padding: 0;
  }
  body {
      background: #fff;
      font-family: \'Open-Sans\',sans-serif;

  }

#container{
  margin: 0 auto;
  position: relative;
  width:800px;
  overflow: visible;
}


  .svg {
    width:800px;
    height:400px;
    overflow: visible;
    position:absolute;
}

.grid .tick {
    stroke: lightgrey;
    opacity: 0.3;
    shape-rendering: crispEdges;
}
.grid path {
      stroke-width: 0;
}


#tag {
  color: white;
  background: #FA283D;
  width: 150px;
  position: absolute;
  display: none;
  padding:3px 6px;
  margin-left: -80px;
  font-size: 11px;
}



#tag:before {
  border: solid transparent;
  content: \' \';
  height: 0;
  left: 50%;
  margin-left: -5px;
  position: absolute;
  width: 0;
  border-width: 10px;
  border-bottom-color: #FA283D;
  top: -20px;
}
</style>
';

	
	
	//echo json_encode(array('success' => $timelineTxt));
	echo json_encode(array('success' => $test));
	exit;
	
} else {
	echo json_encode(array('error' => 'Missing some parameter(s)'));
	exit;
}



