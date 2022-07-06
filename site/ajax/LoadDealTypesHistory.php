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
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
	        WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'min_cpa' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_min_cpa[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
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
	while ($arrRow = mysql_fetch_assoc($resource)) {
		/* if($arrRow['amount'] == lang('Merchant Default')){
			$cntMerchantDefault  ++;
		} */
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_cpa[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);//,'merchantDefaultCnt' => $cntMerchantDefault);
		//}
		unset($arrRow);
	}
	
	/* $ifMerchantDefault = false;
	$amount = 0;
	if(count($arrDealType_cpa)>1){
			foreach($arrDealType_cpa as $key => $deal){
				//echo $key;
				if($key == 0){
					if($deal['merchantDefaultCnt'] > 0){
							$ifMerchantDefault = true;
					}
				}
				
				foreach($arrDealType_cpa as $k=>$d)
				{ 
						if($k == 0) continue;
						// echo $d['merchantDefaultCnt'] . "<br/>";
						if($d['amount'] == lang('Merchant Default')){
							$ifMerchantDefault = false;
							break;
						}
				}
				if($ifMerchantDefault == true){
					$arrDealType_cpa[0]['amount']= 0;
					break;
				}
			}
	}
	foreach($arrDealType_cpa as $key => $deal){
		unset($deal['merchantDefaultCnt']);
	} */
	
	
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_cpa) ? $intLargestResultSetCount : count($arrDealType_cpa);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
	       WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'dcpa' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
		   GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_d_cpa[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_d_cpa) ? $intLargestResultSetCount : count($arrDealType_d_cpa);
	
	
		
	
	
	
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'revenue' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_revenue[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_revenue) ? $intLargestResultSetCount : count($arrDealType_revenue);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'cpl'  AND merchant_id = " . $_GET['merchant_id'] . "  and  amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_cpl[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_cpl) ? $intLargestResultSetCount : count($arrDealType_cpl);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'cpm'  AND merchant_id = " . $_GET['merchant_id'] . "  and  amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_cpm[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_cpm) ? $intLargestResultSetCount : count($arrDealType_cpm);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
	       WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'pnl' AND merchant_id = " . $_GET['merchant_id'] . "  and  amount>-1
		   GROUP BY rdate ORDER BY rdate DESC;";
		   // die ($sql);
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_pnl[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
		//}
		unset($arrRow);
	}
		$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_pnl) ? $intLargestResultSetCount : count($arrDealType_pnl);
	
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'cpc' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_cpc[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_cpc) ? $intLargestResultSetCount : count($arrDealType_cpc);
	
	$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'revenue_spread' AND merchant_id = " . $_GET['merchant_id'] . "  and  amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_revenue_spread[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_revenue_spread) ? $intLargestResultSetCount : count($arrDealType_revenue_spread);


	$sql = "SELECT tier_amount,CONVERT(SUBSTRING_INDEX( tier_amount,  '-', 1 ),UNSIGNED INTEGER) as tierorder,IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, 
			rdate AS rdate, id AS id,valid FROM `affiliates_deals` 
			WHERE  affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'tier' AND merchant_id = " . $_GET['merchant_id'] . " and tier_amount !='0-0' and amount>-1
			 ORDER BY rdate DESC";//tierorder;";
			 
	// die ($sql);
	$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
	while ($arrRow = mysql_fetch_assoc($resource)) {
		//if (is_numeric($arrRow['amount'])) {
			$arrDealType_tier[] = array('amount' => $arrRow['valid']?$arrRow['amount']:0, 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], 'tier_amount' => $arrRow['tier_amount']);
		//}
		unset($arrRow);
	}
	$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_tier) ? $intLargestResultSetCount : count($arrDealType_tier);

	
	if ($isForexOrBinary) {
		$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
				WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'positions_rev_share' AND merchant_id = " . $_GET['merchant_id'] . " 
				GROUP BY rdate ORDER BY rdate DESC;";
		$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
		while ($arrRow = mysql_fetch_assoc($resource)) {
			//if (is_numeric($arrRow['amount'])) {
				$arrDealType_positions_rev_share[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//}
			unset($arrRow);
		}
		$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_positions_rev_share) ? $intLargestResultSetCount : count($arrDealType_positions_rev_share);
		
		
			$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
				WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'lots' AND merchant_id = " . $_GET['merchant_id'] . " 
				GROUP BY rdate ORDER BY rdate DESC;";
		$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
		while ($arrRow = mysql_fetch_assoc($resource)) {
			//if (is_numeric($arrRow['amount'])) {
				$arrDealType_lots[] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//}
			unset($arrRow);
		}
		$intLargestResultSetCount = $intLargestResultSetCount > count($arrDealType_lots) ? $intLargestResultSetCount : count($arrDealType_lots);
		
		
	}
	
	$boolAtLeastOneRowExists  = false;
	$strDealTypeHistoryTable .= '<center><table id="deal_types_history">
									<thead>
										<tr>' 
										. (empty($arrDealType_min_cpa)        ? '' : '<td style="color:white;">' . lang('MINIMUM DEPOSIT DATE')        . '</td>
																		        	  <td style="color:white;">' . lang('MINIMUM DEPOSIT')             . '</td>') 
								        . (empty($arrDealType_cpa)            ? '' : '<td style="color:white;">' . lang('CPA DATE')            . '</td>
																		              <td style="color:white;">' . lang('CPA')                 . '</td>') 
									    . (empty($arrDealType_d_cpa)          ? '' : '<td style="color:white;">' . lang('DCPA DATE')           . '</td>
																			          <td style="color:white;">' . lang('DCPA')                . '</td>') 
										. (empty($arrDealType_revenue)        ? '' : '<td style="color:white;">' . lang('REVENUE DATE')        . '</td>
																			          <td style="color:white;">' . lang('REVENUE')             . '</td>') 
										. (empty($arrDealType_cpl)            ? '' : '<td style="color:white;">' . lang('CPL DATE')            . '</td>
																		              <td style="color:white;">' . lang('CPL')                 . '</td>') 
										. (empty($arrDealType_pnl)            ? '' : '<td style="color:white;">' . lang('PNL DATE')            . '</td>
																						<td style="color:white;">' . lang('PNL')                 . '</td>') 
										. (empty($arrDealType_cpm)            ? '' : '<td style="color:white;">' . lang('CPM DATE')            . '</td>
																		              <td style="color:white;">' . lang('CPM')                 . '</td>')
										. (empty($arrDealType_cpc)            ? '' : '<td style="color:white;">' . lang('CPC DATE')            . '</td>
																		              <td style="color:white;">' . lang('CPC')                 . '</td>')
										. (empty($arrDealType_revenue_spread) ? '' : '<td style="color:white;">' . lang('REVENUE_SPREAD DATE') . '</td>
																					  <td style="color:white;">' . lang('REVENUE_SPREAD')      . '</td>') 
										. (empty($arrDealType_lots) ? '' : '<td style="color:white;">' . lang('LOTS DATE') . '</td>
																					  <td style="color:white;">' . lang('LOTS')      . '</td>') 
									    . (empty($arrDealType_positions_rev_share) ? '' : '<td style="color:white;">' . lang('POSITIONS_REV_SHARE DATE') . '</td>
																					  <td style="color:white;">' . lang('POSITIONS_REV_SHARE')      . '</td>') 
																					  
										. (empty($arrDealType_tier) ? '' : '<td style="color:white;">' . lang('Tier DATE') . '</td>
																					<td style="color:white;">' . lang('TIER AMOUNTS')      . '</td>
																					<td style="color:white;">' . lang('TIER')      . '</td>') 
										. '</tr>
									</thead>
									<tbody>';
	
			$strDealTypeHistoryTable .= '
			<style>
			a.showDelete {
   position: absolute;
    margin-left: -24px;
    margin-top: 5px;
	
			}
			</style>
			
	<script>
			
	
			

			
			$( "#deal_types_history tr td input" ).click(function() {
				
				
				
				
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
			
			
			
	for ($i = 0; $i < $intLargestResultSetCount; $i++) {
		if ($isAdmin) {
			$isRowMarkedAsActive      = $i === 0 ? 'style="color: green; font-weight: bold; width: 146px;"' : 'style="width: 146px;"';
			
			
			if($i===0){
				$strDealTypeHistoryTable .= '<tr class="first">';
				$cls = ' class = "amt" ';
			}
			else{
				$strDealTypeHistoryTable .= '<tr>';
				$cls = "";
			}
			$strDealTypeHistoryTable .= count($arrDealType_min_cpa) > 0 ? ($i < count($arrDealType_min_cpa) 
                            ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_min_cpa[$i]['id'] . '" value="' . $arrDealType_min_cpa[$i]['rdate'] . '" readonly/></td>
                                <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_min_cpa[$i]['id'] . '" value="' . $arrDealType_min_cpa[$i]['amount'] . '" '. $cls .'/></td>' 
                            : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) : '';
			  
			$strDealTypeHistoryTable .= count($arrDealType_cpa) > 0 
                                                  ? ($i <  count($arrDealType_cpa) 
                                                        ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_cpa[$i]['id'] . '" value="' . $arrDealType_cpa[$i]['rdate'] . '" readonly/></td>
                                                               <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_cpa[$i]['id'] . '" value="' . $arrDealType_cpa[$i]['amount'] . '"'. $cls .' /></td>' 
                                                        : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) 
                                                  : '' ;
			
			
			$strDealTypeHistoryTable .= count($arrDealType_d_cpa)>0 ? ( $i < count($arrDealType_d_cpa) 
									  ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_d_cpa[$i]['id'] . '" value="' . $arrDealType_d_cpa[$i]['rdate'] . '" readonly/></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_d_cpa[$i]['id'] . '" value="' . $arrDealType_d_cpa[$i]['amount'] . '"'. $cls .' /></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>') : '';
			
			$strDealTypeHistoryTable .= count($arrDealType_revenue) > 0  ? ($i < count($arrDealType_revenue) 
									  ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_revenue[$i]['id'] . '" value="' . $arrDealType_revenue[$i]['rdate'] . '" readonly/></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_revenue[$i]['id'] . '" value="' . $arrDealType_revenue[$i]['amount'] . '"'. $cls .' /></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>'): '';
			
			$strDealTypeHistoryTable .= count($arrDealType_cpl) > 0  ? ($i < count($arrDealType_cpl)
									  ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_cpl[$i]['id'] . '" value="' . $arrDealType_cpl[$i]['rdate'] . '" readonly/></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_cpl[$i]['id'] . '" value="' . $arrDealType_cpl[$i]['amount'] . '" '. $cls .'/></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) : '';
			$strDealTypeHistoryTable .= count($arrDealType_pnl) > 0 
                                                  ? ($i <  count($arrDealType_pnl) 
                                                        ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_pnl[$i]['id'] . '" value="' . $arrDealType_pnl[$i]['rdate'] . '" readonly/></td>
                                                               <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_pnl[$i]['id'] . '" value="' . $arrDealType_pnl[$i]['amount'] . '"'. $cls .' /></td>' 
                                                        : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) 
                                                  : '' ;
			
			
			$strDealTypeHistoryTable .= count($arrDealType_cpm) > 0 ? ($i < count($arrDealType_cpm)
									  ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_cpm[$i]['id'] . '" value="' . $arrDealType_cpm[$i]['rdate'] . '" readonly/></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_cpm[$i]['id'] . '" value="' . $arrDealType_cpm[$i]['amount'] . '"'. $cls .' /></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) : '';
			
			$strDealTypeHistoryTable .= count($arrDealType_cpc) > 0 ? ( $i < count($arrDealType_cpc) 
									  ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_cpc[$i]['id'] . '" value="' . $arrDealType_cpc[$i]['rdate'] . '" readonly/></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_cpc[$i]['id'] . '" value="' . $arrDealType_cpc[$i]['amount'] . '"'. $cls .' /></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) : '';
			
			
			$strDealTypeHistoryTable .=  count($arrDealType_revenue_spread) > 0 ? ($i < count($arrDealType_revenue_spread)
									  ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_revenue_spread[$i]['id'] . '" value="' . $arrDealType_revenue_spread[$i]['rdate'] . '" readonly/></td>
										 <td><input  type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_revenue_spread[$i]['id'] . '" value="' . $arrDealType_revenue_spread[$i]['amount'] . '"'. $cls .' /></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) : '';
									  
		    $strDealTypeHistoryTable .=  count($arrDealType_positions_rev_share) > 0 ? ($i < count($arrDealType_positions_rev_share)
									  ? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_positions_rev_share[$i]['id'] . '" value="' . $arrDealType_positions_rev_share[$i]['rdate'] . '" readonly/></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_positions_rev_share[$i]['id'] . '" value="' . $arrDealType_positions_rev_share[$i]['amount'] . '" '. $cls .'/></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) : '';
									  
			$strDealTypeHistoryTable .=  count($arrDealType_lots) > 0 ? ($i < count($arrDealType_lots)
									? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_lots[$i]['id'] . '" value="' . $arrDealType_lots[$i]['rdate'] . '" readonly/></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_lots[$i]['id'] . '" value="' . $arrDealType_lots[$i]['amount'] . '"'. $cls .' /></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) : '';
			
			$strDealTypeHistoryTable .=  count($arrDealType_tier) > 0 ? ($i < count($arrDealType_tier)
									? '<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $arrDealType_tier[$i]['id'] . '" value="' . $arrDealType_tier[$i]['rdate'] . '" readonly/></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_tier[$i]['id'] . '" value="' . $arrDealType_tier[$i]['tier_amount'] . '" /></td>
										 <td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $arrDealType_tier[$i]['id'] . '" value="' . $arrDealType_tier[$i]['amount'] . '"'. $cls .' /></td>' 
									  : '<td style="width: 146px;"></td><td style="width: 146px;"></td>' ) : '';
			
			$strDealTypeHistoryTable .= '</tr>';
			$boolAtLeastOneRowExists  = true;
			unset($isRowMarkedAsActive);
			
			
		} else {
			$isRowMarkedAsActive      = $i === 0 ? 'style="color: green; font-weight: bold; width: 146px;"' : '';
			$strDealTypeHistoryTable .= '<tr>';
			
			$strDealTypeHistoryTable .= $i < count($arrDealType_min_cpa) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_min_cpa[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_min_cpa[$i]['amount'] . '</label></td>' 
									  : '';
									  
			$strDealTypeHistoryTable .= $i < count($arrDealType_cpa) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_cpa[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_cpa[$i]['amount'] . '</label></td>' 
									  : '';
			
			$strDealTypeHistoryTable .= $i < count($arrDealType_d_cpa) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_d_cpa[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_d_cpa[$i]['amount'] . '</label></td>' 
									  : '';
			
			$strDealTypeHistoryTable .= $i < count($arrDealType_revenue) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_revenue[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_revenue[$i]['amount'] . '</label></td>' 
									  : '';
			$strDealTypeHistoryTable .= $i < count($arrDealType_pnl) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_pnl[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_pnl[$i]['amount'] . '</label></td>' 
									  : '';
			
			$strDealTypeHistoryTable .= $i < count($arrDealType_cpl) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_cpl[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_cpl[$i]['amount'] . '</label></td>' 
									  : '';
			
			$strDealTypeHistoryTable .= $i < count($arrDealType_cpm) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_cpm[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_cpm[$i]['amount'] . '</label></td>' 
									  : '';
			
			$strDealTypeHistoryTable .= $i < count($arrDealType_cpc) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_cpc[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_cpc[$i]['amount'] . '</label></td>' 
									  : '';
			
			$strDealTypeHistoryTable .= $i < count($arrDealType_revenue_spread) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_revenue_spread[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_revenue_spread[$i]['amount'] . '</label></td>' 
									  : '';
									  
			$strDealTypeHistoryTable .= $i < count($arrDealType_positions_rev_share) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_positions_rev_share[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_positions_rev_share[$i]['amount'] . '</label></td>' 
									  : '';
									  
		$strDealTypeHistoryTable .= $i < count($arrDealType_lots) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_lots[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_lots[$i]['amount'] . '</label></td>' 
									  : '';									  
		$strDealTypeHistoryTable .= $i < count($arrDealType_tier) 
									  ? '<td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_tier[$i]['rdate'] . '</label></td>
										 <td><label ' . $isRowMarkedAsActive . '>' . $arrDealType_tier[$i]['amount'] . '</label></td>' 
									  : '';																 
			
			$strDealTypeHistoryTable .= '</tr>';
			$boolAtLeastOneRowExists  = true;
			unset($isRowMarkedAsActive);
		}
	}
	
	$strDealTypeHistoryTable .= $boolAtLeastOneRowExists 
	                          ? '<tr><td><input type="submit" onclick="return false;" value="' . lang('Save changes') . '" /></td></tr>
								 <tr><td colspan="3"><label style="color: green; font-weight: bold;">* ' . lang('Currently active record marked by green') . '</label></td></tr>' 
							  : '';
	
	$strDealTypeHistoryTable .= '</tbody></table></center>';
	$strDealTypeHistoryScript .= 	'
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
	$strDealTypeHistoryScript .= !$isAdmin ? '' : '<script>
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
																var intMerchantId = $("#select_merchant").val();
																
																$.get("ajax/LoadDealTypesHistory.php?is_admin=1&affiliate_id=' . $_GET['affiliate_id'] . '&merchant_id=" + intMerchantId, function(res) {
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
																});
																alert(res["success"]);
																
															} else if (res["success"] && subject === "amount") {
																
																var intMerchantId = $("#select_merchant").val();
																
																$.get("ajax/LoadDealTypesHistory.php?is_admin=1&affiliate_id=' . $_GET['affiliate_id'] . '&merchant_id=" + intMerchantId, function(res) {
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
																});
																alert(res["success"]);
                                                                                                                                
															} else {
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
								  </script>';
	
							  
	$strDealTypeHistoryDiv .= $strDealTypeHistoryTable . $strDealTypeHistoryScript;
	
	echo json_encode(array('success' => $strDealTypeHistoryDiv));
	exit;
	
} else {
	echo json_encode(array('error' => 'Missing some parameter(s)'));
	exit;
}



