<?php 
		require_once('database.php');
		require_once('../func/charts.php');
		require_once('../func/func_string.php');
		require_once('../func/func_debug.php');
		require_once('../func/func_global.php');
		require_once('../func/func_db.php');
		
		require_once('../affiliate/func/login.php');
		
		$db = $_POST;

		
		//$set->content .= ($set->ShowGraphOnDashBoards==1 ||  $set->ShowGraphOnDashBoards== null  ? chart('0') : ""  ).'
		$set->content .= ($set->ShowGraphOnDashBoards==1 ||  $set->ShowGraphOnDashBoards== null  ? 
		'		
		<div class="my-slider" style="width:100%">
			<div style="float:right;margin-right:10px;margin-top:5px;position:absolute;left:95%;z-index:99999;" class="refresh1"><a class="refresha tooltip" onclick="refreshChart2();" style="font-size: 11px; cursor: pointer;display:none" >'. ($set->chartTheme=='dark_unica'?'<img src=\'images/refresh_white.png\' width=20 />':'<img src=\'images/refresh_black.png\' width=30 />') . '</a></div>
			<ul>
				<li data-slide="0" data-name="Performance Chart" class="unslider-active">  '.highchart('0','affiliate',$db['id'],1) .'</li>
				<li data-slide="1" data-name="Conversion Chart" >'. conversionHighchart('0','affiliate',$db['id'],1).'</li>
			</ul>
		</div>		
<script>

refreshData2();
refreshData3();
/* window.onload = function() {
		refreshData2();
		refreshData3();
} */
$(document).ready(function(){
	$(".my-slider").mouseover(function(){
		$(".refresha").show();
	});
	$(".my-slider").mouseout(function(){
		$(".refresha").hide();
	});
	$(".my-slider").unslider({
		arrows: false,
		dots:true
	});
	
	$(".unslider-nav li").on("click",function(){
		slide = $(this).data("slide");
		if(slide == 0){
			$(".refresh1").html("<a class=\"refresha tooltip\" id=\'refreshData2\' onclick=\"refreshChart2();\" style=\"font-size: 11px; cursor: pointer;display:none\">'. ($set->chartTheme=='dark_unica'?'<img src=\'images/refresh_white.png\' width=20 />':'<img src=\'images/refresh_black.png\' width=30 />') . '</a>")
		}
		else{
			$(".refresh1").html("<a class=\"refresha tooltip\" id=\'refreshData3\' onclick=\"refreshChart3();\" style=\"font-size: 11px; cursor: pointer;display:none\">'. ($set->chartTheme=='dark_unica'?'<img src=\'images/refresh_white.png\' width=20 />':'<img src=\'images/refresh_black.png\' width=30 />') . '</a>")
		}
	});
	
	
	
});
</script>
<style>
.unslider-nav ol{
	padding:10px;
	text-align:center;
}
/* .unslider-nav ol li:first-child:after{
	content : "   |   ";
	padding: 0 10px;
} */
.unslider-nav ol li{
	display:inline;
	cursor:pointer;
}
</style>
' :'') ;
							                            $merchantName = strtolower($ww['name']);
                                                        $merchantID   = strtolower($ww['id']);
                                                        
													if ($db['id']) {
														
														$regdate = date("Y-m-01",strtotime($db['rdate']));
														$start = $month = strtotime($regdate);
														$end = strtotime("-1 month", strtotime(date('Y-m-01')));
												
														while($month < $end)
														{
															 //$end_date =  date("Y-m-d",strtotime("+". $daysInMonth-1 ." days", $month));
															 $month = strtotime("+1 month", $month);
															 
															 $sql = 	"SELECT * FROM chart_data WHERE level='affiliate' AND member_id=".$db['id'] ." AND month='".date("m",$month)."'  AND year='".date("Y",$month)." 23:59:59'";
															
															$chkExist=mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
															 
															if (!$chkExist['id']) {
																
																$qq=function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY pos",__FILE__,__FUNCTION__);
																$tradersFtdArray = array();
																$new_ftd=0;
			
																while ($ww=mysql_fetch_assoc($qq)) {
																
																	$new_ftd_rslt = getTotalFtds(date('Y-m-d',$month),date("Y-m-d",strtotime("-1 Day", strtotime("+1 Month",$month))).' 23:59:59', $db['id'], $ww['id'], $ww['wallet_id'], 0);
																	 foreach ($new_ftd_rslt as $arrFtd) {
																		
																							$beforeNewFTD = $new_ftd;
																							getFtdByDealType($ww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $arrFtd['amount'], $new_ftd);
																							
																							// if ($beforeNewFTD != $new_ftd && !in_array($arrFtd['trader_id'],$tradersFtdArray)){
																							if ($beforeNewFTD != $new_ftd) {

																								$tradersFtdArray[$arrFtd['trader_id']]=$arrFtd['trader_id'];
																							}
																							unset($arrFtd);
																	}
																	$new_ftd =+ count($tradersFtdArray) ;
																	
																	$qry = "";
																	$qry = "SELECT COUNT(id) FROM data_reg WHERE merchant_id = '" . $ww['id'] . "' and  ".$where." type='real' AND rdate BETWEEN '".date('Y-m-d',$month)."' AND '".date("Y-m-d",strtotime("-1 Day", strtotime("+1 Month",$month)))." 23:59:59' ";
																	$qry = $qry . ' and affiliate_id = ' . $db['id'];
																	function_mysql_query("INSERT INTO `logs` ( `title`, `description`, `var1`, `var2`, `var3`, `rdate`, `flag`, `merchant_id`, `text`, `ip`, `url`) VALUES ( '5', '".$qry."', '-', '-', '-', '2015-03-02 00:00:00', '', '0', '0', '', '-');");
																	
																	$total_real += mysql_result(function_mysql_query($qry,__FILE__,__FUNCTION__),0);
																	
																	}
				
																	$sql = "INSERT INTO chart_data (lastUpdate,fulldate,level,member_id,month,year,accounts,ftds) VALUES
																			('".dbDate()."','".date("Y",$month)."-".date("m",$month)."-01','affiliate','".$db['id']."','".date("m",$month)."','".date("Y",$month)."  23:59:59','".$total_real."','".$new_ftd."')";
																	function_mysql_query($sql,__FILE__,__FUNCTION__); 
																	
															}
																
															
														}
														
                                                      /*   $sql = "SELECT COUNT(data_reg.id) AS count FROM data_reg AS data_reg "
                                                             . "INNER JOIN merchants AS mer ON mer.id = data_reg.merchant_id AND mer.valid = 1 "
                                                             . "WHERE data_reg.affiliate_id = " . $id . " and data_reg.type='real'";
                                                        
                                                        $arrTotalTraders  = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                                                        $totalTraders    += $arrTotalTraders['count']; */
                                                        
														  $sql = "SELECT COUNT(data_reg.id) AS count FROM data_reg AS data_reg "
                                                             . "INNER JOIN merchants AS mer ON mer.id = data_reg.merchant_id AND mer.valid = 1 "
                                                             . "WHERE data_reg.affiliate_id = " . $db['id'] . " and data_reg.type='lead'";
                                                        
                                                        $arrTotalLeads  = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                                                        $totalLeads    += $arrTotalLeads['count'];
                                                        
														//$ftd = count(getTotalFtds('', '', $id));
														
														 $sql = "select sum(ftds) as ftd, sum(accounts) as accounts from chart_data where member_id=" .  $db['id'];
														$arrFTDs  = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
														/* if(empty($arrFTDs) || !$arrFTDs)
															$ftd = count(getTotalFtds('', '', $id));
														else */
														$ftd = $arrFTDs['ftd'];
														
														$totalTraders = $arrFTDs['accounts'];
														
                                                        $sql = "SELECT data_sales.type, data_sales.amount FROM data_sales AS data_sales "
                                                             . "INNER JOIN merchants AS mer ON mer.id = data_sales.merchant_id AND mer.valid = 1 "
                                                             . "WHERE data_sales.affiliate_id = " .  $db['id'];
                                                        
                                                        $salesqq = function_mysql_query($sql,__FILE__,__FUNCTION__);
                                                        
                                                        while ($salesww = mysql_fetch_assoc($salesqq)) {
                                                            if ($salesww['type'] == "deposit") $totalAmount += $salesww['amount'];
                                                            if ($salesww['type'] == "withdrawal") $totalWithdrawal += $salesww['amount'];
                                                        }
                                                        
                                                        
                                                        $sql = "SELECT IFNULL(SUM(total), 0) AS totalPaid FROM payments_paid WHERE affiliate_id = " .  $db['id'];
							$total = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                                                        
                                                        $sql = "SELECT id FROM payments_details WHERE affiliate_id = " .  $db['id'];
							$accounts = mysql_num_rows(function_mysql_query($sql,__FILE__,__FUNCTION__));
                                                        
                                                        $sql = "SELECT id FROM payments_details WHERE status = 'canceled' AND affiliate_id = " .  $db['id'];
							$totalFruad = mysql_num_rows(function_mysql_query($sql,__FILE__,__FUNCTION__));
                                                        
                                                        $sql = "SELECT id FROM payments_details WHERE status = 'pending' AND affiliate_id = " .  $db['id'];
							$totalPending = mysql_num_rows(function_mysql_query($sql,__FILE__,__FUNCTION__));
                                                        
							
                                                        $totalTraffic                = [];
                                                        //$arrClicksAndImpressions     = getClicksAndImpressions(null, null, null,  $db['id']);
                                                        $sqlClicksAndImpressions = "SELECT SUM(Clicks) as clicks, SUM(Impressions) as impressions FROM merchants_creative_stats WHERE AffiliateID = ".(int)$db['id'];
                                                        $arrClicksAndImpressions = mysql_fetch_assoc(function_mysql_query($sqlClicksAndImpressions,__FILE__,__FUNCTION__));
                                                        
                                                        $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                                                        $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];
							
													}
							$boxaName = $userLevel."-affiliates-1";
		
							$tableArr = Array(
									
								(object) array(
								  'id' => 'impressions',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Impressions').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraffic['totalViews'],0).'</td></tr>'
								),
																
								(object) array(
								  'id' => 'Clicks',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Clicks').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraffic['totalClicks'],0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Click Through Ratio (CTR)',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Click Through Ratio (CTR)').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($totalTraffic['totalClicks']/$totalTraffic['totalViews'])*100,2).'%</td></tr>'
								),
								
								(object) array(
								  'id' => 'Click to Account',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang(ptitle('Click to Account')).':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format(($totalTraders/$totalTraffic['totalClicks'])*100,2).'%</td></tr>'
								),
						
								(object) array(
								  'id' => 'Click to Sale',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang(ptitle('Click to Sale')).':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).'%</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Paid',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Total Paid').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'. $set->currency .' '.@number_format($total['totalPaid'],2).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Traders',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang(ptitle('Total Accounts')).':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalTraders,0).'</td></tr>'
								),	
								
								(object) array(
								  'id' => 'Total Leads',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang(ptitle('Total Leads')).':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalLeads,0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total FTDs',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('FTDs count').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($ftd,0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Deposit',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Total Deposit').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'. $set->currency .' '.@number_format($totalAmount,2).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Withdrawal',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Total Withdrawal').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'. $set->currency .' '.@number_format($totalWithdrawal,2).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Total Chargeback',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang(ptitle('Total Chargeback / Refund / Fraud')).':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format($totalFruad,0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Pending',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;">'.lang('Pending/Un Paid Traders').':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'.@number_format($totalPending+$totalFruad,0).'</td></tr>'
								),
								
								(object) array(
								  'id' => 'Affiliate Risk',
								  'str' => '<tr><td style="background: #FFFFFF; font-family: Arial;">'.lang('Affiliate Risk').':</td><td style="background: #FFFFFF; font-family: Arial; font-weight: bold;">'.@number_format(($totalFruad/$ftd)*100,2).'%</td></tr>'
								),
								
								(object) array(
								  'id' => 'Trader LTV',
								  'str' => '<tr><td style="background: #EFEFEF; font-family: Arial;" title="Total Deposit Amount  / Total FTD Count">'.lang(ptitle('Trader LTV')).':</td><td style="background: #EFEFEF; font-family: Arial; font-weight: bold;">'. $set->currency .' '.@number_format($totalAmount/$ftd,2).'</td></tr>'
								)				
							);
							
					
							
							
							$set->content .= '<hr/><table width="100%" class="tablesorter" border="0" cellpadding="4" cellspacing="1" style="background: #DDDDDD;"><tbody>
										'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '</tr><tr>').'
									</tbody></table>';
									
									
									
									echo $set->content;die;