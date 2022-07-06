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
	$arrDealType_cpi                 = array();
	$arrDealType_cpm                 = array();
	$arrDealType_cpc                 = array();
	$arrDealType_lots      = array();
	$arrDealType_tier      = array();
	$arrDealType_revenue_spread      = array();
	$arrDealType_positions_rev_share = array();
	
	$dealsArray= array('min_cpa','cpa','dcpa','revenue','pnl','cpi','cpl','cpm','cpc','lots','revenue_spread','positions_rev_share');
	
	$fetchedArrDealType = array();
	foreach ($dealsArray as $deal) {
		$sql = "SELECT IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, rdate AS rdate, id AS id FROM `affiliates_deals` 
			WHERE affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = '".$deal."' AND merchant_id = " . $_GET['merchant_id'] . "  and amount>-1
			GROUP BY rdate ORDER BY rdate DESC;";
		$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
		while ($arrRow = mysql_fetch_assoc($resource)) {
			//if (is_numeric($arrRow['amount'])) {
				$fetchedArrDealType[$deal][] = array('amount' => $arrRow['amount'], 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id']);
			//}
			unset($arrRow);
		}
		$intLargestResultSetCount = $intLargestResultSetCount > count($fetchedArrDealType[$deal]) ? $intLargestResultSetCount : count($fetchedArrDealType[$deal]);
	}	
	
	
	$deal = 'tier';
	$sql = "SELECT tier_amount,CONVERT(SUBSTRING_INDEX( tier_amount,  '-', 1 ),UNSIGNED INTEGER) as tierorder,IFNULL(amount, '" . lang('Merchant Default') . "') AS amount, 
			rdate AS rdate, id AS id,valid FROM `affiliates_deals` 
			WHERE  affiliate_id = " . $_GET['affiliate_id'] . " AND dealType = 'tier' AND merchant_id = " . $_GET['merchant_id'] . " and tier_amount !='0-0' and amount>-1
			 ORDER BY rdate DESC";//tierorder;";
			 
	
		$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
		while ($arrRow = mysql_fetch_assoc($resource)) {
			//if (is_numeric($arrRow['amount'])) {
				$fetchedArrDealType[$deal][] = array('amount' => $arrRow['valid']?$arrRow['amount']:0, 'rdate' => $arrRow['rdate'], 'id' => $arrRow['id'], 'tier_amount' => $arrRow['tier_amount']);
			//}
			unset($arrRow);
		}
		$intLargestResultSetCount = $intLargestResultSetCount > count($fetchedArrDealType[$deal]) ? $intLargestResultSetCount : count($fetchedArrDealType[$deal]);
		
		
	$colors = array('black','yellow','blue','green','red','purple','orange','brown','lightblue','gray');
	
	
	$most_min_date = date('Y-m-d H:i:s');
	$most_max_date = date('Y-m-d H:i:s');
	$processedDealArray = array();
	foreach ($fetchedArrDealType as $dealTypeArr=>$arr) {
		/* 
		if($isForexOrBinary && (strtolower($dealTypeArr)=='lots' || strtolower($dealTypeArr)=='pnl' || strtolower($dealTypeArr)=='revenue_spread' || strtolower($dealTypeArr)=='positions_rev_share'))
			{
			 */
			
			$lastRunEndDate = date('Y-m-d H:i:s');
			$times = array();
			foreach ($arr as $deal){

				$deal['ending_time'] = strtotime($lastRunEndDate)*1000;
				$deal['starting_time'] = strtotime($deal['rdate'])*1000;
				$deal['end_date'] = ($lastRunEndDate);
				// $deal['label'] = $dealTypeArr;
				
			//	$k = array_rand($colors);
			//	$deal['color'] = $colors[$k];
				$times['times'][] = $deal;
				if ($deal['amount']==0)
				$deal['color'] = "grey";
			
			
				$times['label'] = $dealTypeArr;
				$lastRunEndDate = $deal['rdate'];
				$most_min_date = $deal['rdate']<$most_min_date ? $deal['rdate'] : $most_min_date;
			// }
		}
		$processedDealArray[] =  $times;
	}
	$processedDealArray['info']['startingTime'] = strtotime($most_min_date);
	$processedDealArray['info']['CurrentTime'] = strtotime($most_max_date);
	
	
	die(json_encode($processedDealArray));
	// die();
	/* 
	echo '<pre>';
	echo '</pre>';
	die('--');
		 */
/* 	{
	"min_cpa": [
		{
			"amount": "100",
			"rdate": 1478175750,
			"end_date": 1479655901,
			"id": "254"
		},
		{
			"amount": "101",
			"rdate": 1478175746,
			"end_date": 1479655901,
			"id": "253"
		},
		{
			"amount": "102",
			"rdate": 1477278000,
			"end_date": 1479655901,
			"id": "226"
		}
	],
	"CPA": [
		{
			"amount": "200",
			"rdate": 1478131200,
			"end_date": 1479655901,
			"id": "227"
		}
	],
	"revenue": [
		{
			"amount": "0",
			"rdate": 1478762165,
			"end_date": 1479655901,
			"id": "259",
			"deal_type": "revenue"
		},
		{
			"amount": "30",
			"rdate": 1478762002,
			"end_date": 1479655901,
			"id": "258",
			"deal_type": "revenue"
		},
		{
			"amount": "54",
			"rdate": 1478761471,
			"end_date": 1479655901,
			"id": "256",
			"deal_type": "revenue"
		},
		{
			"amount": "30",
			"rdate": 1477267200,
			"end_date": 1479655901,
			"id": "228",
			"deal_type": "revenue"
		}
	]
} */
	

	

	
	
	
	
	$boolAtLeastOneRowExists  = false;
	$strDealTypeHistoryTable .= '<center>
	<style>
	  .deal_types_history tr td {
		  color:white;
	  }
	</style>
	<table id="deal_types_history">
									<thead>
										<tr>' ;
										
										
										
										foreach ($dealsArray as $deal){
										
										$nameOfDeal = trim(str_replace('_',' ' ,strtoupper($deal)));
										$nameOfDeal= $nameOfDeal== 'MIN CPA' ? "MINIMUM DEPOSIT" : trim($nameOfDeal);
										$nameOfDeal= $nameOfDeal== 'REVENUE' ? "NetDeposit" : trim($nameOfDeal);
										
										$strDealTypeHistoryTable .=	 (empty($processedDealArray[$deal])  ? '' : '<td>' . lang($nameOfDeal.' DATE')        . '</td><td>' . lang($nameOfDeal)       . '</td>') ;
										}																					
																					
							$strDealTypeHistoryTable .=
										 '</tr>
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
				
				foreach ($dealsArray as $deal){
											
											
					if (count($processedDealArray[$deal]) > 0) {
						if ($i < count($processedDealArray[$deal])) {
					
											$strDealTypeHistoryTable .='<td><input class = "deals_rdate" type="text" ' . $isRowMarkedAsActive . ' data-rdate_id="' . $processedDealArray[$deal][$i]['id'] . '" value="' . $processedDealArray[$deal][$i]['rdate'] . '" readonly/></td>
											<td><input type="text" ' . $isRowMarkedAsActive . ' data-amount_id="' . $processedDealArray[$deal][$i]['id'] . '" value="' . $processedDealArray[$deal][$i]['amount'] . '" '. $cls .'/></td>';
						}
						else 
						{
											$strDealTypeHistoryTable .='<td style="width: 146px;"></td><td style="width: 146px;"></td>';
						}
				
					}
				}
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
			
			
				foreach ($dealsArray as $deal){
									
										$nameOfDeal = trim(str_replace('_',' ' ,strtoupper($deal)));
										$nameOfDeal= $nameOfDeal== 'MIN CPA' ? "MINIMUM DEPOSIT" : trim($nameOfDeal);
										
										$strDealTypeHistoryTable .=	 $i < count($processedDealArray[$deal])  ? '<td><label ' . $isRowMarkedAsActive . '>' . $processedDealArray[$deal]['rdate'] . '</label></td><td><label ' . $isRowMarkedAsActive . '>' . $processedDealArray[$deal]['amount'] . '</label></td>' : '' ;
				}
										
			
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



