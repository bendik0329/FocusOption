<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse') || empty($userlevel)) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$set->content .= '<script type="text/javascript" src="../../js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="../../js/tableExport/filesaver.js"></script>
<script type="text/javascript" src="../../js/tableExport/jquery.base64.js"></script>';
$pageTitle   = lang('Affiliate Report');

$filename = "affiliates_data_" . date('YmdHis');

$set->breadcrumb_title = lang($pageTitle);
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="admin/">'.lang('Dashboard').'</a></li>
			<li><a href="admin/products.php?act=products">'.lang('Products Place').'</a></li>
			<li><a href="'. $set->uri .'" class="arrow-left">'.lang($pageTitle).'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';


		$listReport = '';
                
                // List of wallets.

                // $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1;";
				$merchant_id = isset($merchant_id) && $merchant_id>0 ? $merchant_id : 0;
				


                
		$l = 0;
					



					$productsList = array();
					$sql = "select id from products_items where valid=1" . ($product_id ? " and id in ( " . $product_id . ") "  : "" );
					$rscftd =mysql_query($sql);
					while ($products = mysql_fetch_assoc($rscftd)) {
								   $productsList[$products['id']] = $products;
					}
							// die ($sql);	   
					
		$displayForex = 0;
		$tradersProccessedForLots= array();
		
		// $sql = "SELECT * FROM merchants WHERE valid='1' ORDER BY type, pos";
		// $qq = function_mysql_query($sql,__FILE__);
		$qry = "
		select username,id from affiliates  where valid=1 and id in (
		(select distinct(affiliate_id) from data_reg where product_id>0 " 
		. (!empty($product_id) ? " and product_id in( " . $product_id . ")" : "")
		. (!empty($group_id) ? " and group_id = " . $group_id : "")
		. " and data_reg.rdate between '" . $from . "'  and '". $to . "'".
		"
		union
		select distinct(affiliate_id) as id  from traffic where product_id>0 " 
		. (!empty($product_id) ? " and product_id in( " . $product_id . ")" : "")
		. (!empty($group_id) ? " and group_id = " . $group_id : "")
		. " and traffic.rdate between '" . $from . "'  and '". $to . "'".
		"
		union
		select distinct(affiliate_id) as id  from data_sales where product_id>0 " 
		. (!empty($product_id) ? " and product_id in( " . $product_id . ")" : "")
		. (!empty($group_id) ? " and group_id = " . $group_id : "")
		. " and data_sales.rdate between '" . $from . "'  and '". $to . "'".
		")) ";
		
		
		
		/* echo $qry . '<br><br>'; */
		$rsc = mysql_query($qry);
		while ($ww = mysql_fetch_assoc($rsc)) {
// var_dump($ww);
// die();
                    $fromDate = $from;
                    $toDate   = $to;
                    
                    $l++;
                    $ftdUsers = '';
                    $netRevenue = 0;
                    $totalCom=0;
                    $ftd=0;
                    $totalLeads = 0;
                    $totalInstallation = 0;
                    $totalDemo = 0;
                    $totalReal = 0;
                    $ftd_amount['amount']=0;
                    $real_ftd = 0;
                    $real_ftd_amount = 0;
                    $bonus = 0;
                    $withdrawal = 0;
                    $chargeback = 0;
                    $depositingAccounts = 0;
                    $sumDeposits = 0;
                    $totalLots = 0;
                    $volume = 0;
                    
							unset ($totalTraffic);
                    
                    
                    
                 
                    
                  
			
                        
						{
                            $searchInSql = " BETWEEN '" . $from . "' AND '" . $to . "' ";
                            
                            $formula  = $ww['rev_formula'];
                            $fromDate = $from;
                            $toDate   = $to;
                            
							
                            $ftdUsers = '';
                            $netRevenue = 0;
                            $totalCom=0;
                            $ftd=0;
                            $totalLeads = 0;
                            $totalInstallation = 0;
                            $totalDemo = 0;
                            $totalReal = 0;
                            $ftd_amount['amount']=0;
                            $real_ftd = 0;
                            $real_ftd_amount = 0;
                            $bonus = 0;
                            $lots = 0;
                            $withdrawal = 0;
                            $chargeback = 0;
                            $depositingAccounts = 0;
                            $sumDeposits = 0;
                            $volume = 0;
							$depositsAmount=0;
                            // $merchantName = strtolower($ww['name']);
                            
                            
                           

								   
                            $totalTraffic = [];
								  foreach ($productsList as $products)
						   {				

						   
                            $arrClicksAndImpressions = getClicksAndImpressions(
                                $from, 
                                $to, 
                                $products['id'], 
                                $ww['id'], 
                                (isset($group_id) && $group_id != '' ? $group_id : null)
								,null,  null, null, null,true)
                            ;
							
                            $a = $arrClicksAndImpressions['impressions'];
                            $b= $arrClicksAndImpressions['clicks'];

							
							$totalTraffic['totalViews'] +=(int)$a;
							$totalTraffic['totalClicks']+=(int)$b;
							

						     $arrTmp = [
                                                'count'  => $totalTraffic['totalClicks'],
                                                'product_id'  => $products['id'],
                                                'dealtype'   => 'cpc',
                                            ];
                                            
									
                                            $a= getProductCommission(
											
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
											
											$totalCom +=$a;
                                            unset($arrTmp);
						}
                                        
						
							/*   foreach ($productsList as $products)
						   {
                                 */
			
							   
                            $sql = "SELECT * FROM data_reg "
                                    . "where product_id>0 and affiliate_id = '" . $ww['id'] . "' AND "
                                    . " rdate " . $searchInSql 
                                    . (isset($product_id) && $product_id != '' ? ' AND product_id in( ' . $product_id . ') ' : '')
                                    . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                            // die ($sql);
                            $regqq = function_mysql_query($sql,__FILE__);
                            
                            
                            
                            while ($regww = mysql_fetch_assoc($regqq)) {
								
								
                                
                                    if ($regww['type'] == "lead") {
										
										
							     $arrTmp = [
                                                'product_id'  => $regww['product_id'],
                                                'affiliate_id' => $regww['affiliate_id'],
                                                'rdate'        => $regww['rdate'],
                                                'banner_id'    => $regww['banner_id'],
                                                'trader_id'    => $regww['trader_id'],
                                                'profile_id'   => $regww['profile_id'],
                                                'dealtype'   => 'cpllead',
                                            ];
                                            
											
											$a = getProductCommission(
											
                                                $arrDealTypeDefaults, 
                                                $arrTmp,
												false,
												true
                                            );
                                            
											
											
											$totalCom +=$a;
											
										$totalLeads++;
											
										
									}
                                    if ($regww['type'] == "real") {
                                        
                                            $arrTmp = [
                                                'product_id'  => $regww['product_id'],
                                                'affiliate_id' => $regww['affiliate_id'],
                                                'rdate'        => $regww['rdate'],
                                                'banner_id'    => $regww['banner_id'],
                                                'trader_id'    => $regww['trader_id'],
                                                'profile_id'   => $regww['profile_id'],
                                                'dealtype'   => 'cplaccount',
                                            ];
                                            
									
                                            $a= getProductCommission(
											
											
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
											
											$totalCom +=$a;
                                        $totalReal++;
                                            
                                    }
									  if ($regww['type'] == "installation") {
                                        
                                            $arrTmp = [
                                                'product_id'  => $regww['product_id'],
                                                'affiliate_id' => $regww['affiliate_id'],
                                                'rdate'        => $regww['rdate'],
                                                'banner_id'    => $regww['banner_id'],
                                                'trader_id'    => $regww['trader_id'],
                                                'profile_id'   => $regww['profile_id'],
                                                'dealtype'   => 'cpi',
                                            ];
                                            
									
                                            $a= getProductCommission(
											
											
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
											
											$totalCom +=$a;
                                        $totalInstallation++;
                                            
                                    }
                                            unset($arrTmp);
							}
                            
                                $strSql = "SELECT *, data_sales.type AS data_sales_type  FROM data_sales AS data_sales "
                                        . "INNER JOIN data_reg AS data_reg ON data_sales.product_id = data_reg.product_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                        . " where data_sales.product_id>0 and data_sales.affiliate_id = '" . $ww['id'] . "' AND  data_sales.type='deposit' AND data_sales.rdate "
                                        . $searchInSql . " "
                                        . (isset($group_id) && $group_id != '' ? ' AND data_sales.group_id = ' . $group_id . ' ' : '');
                                
								$salesqq = function_mysql_query($strSql,__FILE__);
								$netDepositTransactions = array();
								while ($salesww = mysql_fetch_assoc($salesqq)) {
                                    //if ($salesww['type'] == 'deposit') { // OLD.
									
										$tranrow['id'] = $salesww['id'];
										$tranrow['affiliate_id'] = $salesww['affiliate_id'];
										$tranrow['tranz_id'] = $salesww['tranz_id'];
										$tranrow['trader_id'] = $salesww['trader_id'];
										$tranrow['product_id'] = $salesww['product_id'];
										$tranrow['rdate'] =$salesww['rdate'];
										$tranrow['amount'] = $salesww['amount'];
										$tranrow['type'] = $salesww['data_sales_type'];
										$tranrow['status'] = $salesww['status'];
										$netDepositTransactions[] = array($tranrow);
                                        $sumDeposits += $salesww['amount'];
										$depositsAmount+=$salesww['amount'];
                                        $depositingAccounts++;
                                    
                                    
									}
                          
						  
						  foreach ($productsList as $products) {
                                
                                $arrFtds  = getTotalFtds(
                                    $from, 
                                    $to, 
                                    $ww['id'], 
                                    $products['id'], 
                                    0, 
                                    (isset($group_id) && $group_id != '' ? $group_id : 0), 
                                    0, 
                                    0,
                                    $searchInSql,
									0,"",
									true
                                );
                                
                                if (!$needToSkipMerchant) {
                                    
						
									
									foreach ($arrFtds as $arrFtd) {
                                        $real_ftd++;
                                        $real_ftd_amount += $arrFtd['amount'];
                                        
                                        $beforeNewFTD = $ftd;
                                        getFtdByDealType($products['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $ftd,false);
                                        
                                        if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
                                            $arrFtd['dealtype'] = 'cpa';
                                            
											
											 $totalCom += getProductCommission(
                                            
                                                
                                                $arrDealTypeDefaults, 
                                                $arrFtd
											
												
												
                                            ); 
                                        }
                                        unset($arrFtd);
                                    }
                                }
									}
									
                                
                   
						
				$filterFrom = $from;
				$filterTo   = $to;
				$boxaName   = "admin-prd-affiliates-report-1";
                     
				                
				$tableArr = array(
						
				
					(object) array(
					  'id' => 'id',
					  'str' => '<td style="text-align: left;">'.$ww['id'].'</td>'
					),(object) array(
					  'id' => 'name',
					  'str' => '<td style="text-align: left;">'.$ww['username'].'</td>'
					),
					(object) array(
					  'id' => 'totalViews',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=traffic&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.@number_format($totalTraffic['totalViews'],0).'</a></td>'
					),
					(object) array(
					  'id' => 'totalClicks',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=traffic&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.@number_format($totalTraffic['totalClicks'],0).'</a></td>'
					),
					
					
					(object) array(
					  'id' => 'total_leads',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&product_id='.$ww['id'].'&type=lead">'.$totalLeads.'</a></td>'
					),
			
					(object) array(
					  'id' => 'total_real',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&product_id='.$ww['id'].'&type=signup">'.$totalReal.'</a></td>'
					),
					(object) array(
					  'id' => 'totalInstallation',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&product_id='.$ww['id'].'&type=installation">'.$totalInstallation.'</a></td>'
					),
					(object) array(
					  'id' => 'ftd',
					  'str' => '<td style="text-align: center;"><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.$arrRange['from'].'&to='.$arrRange['to'].'&product_id='.$ww['id'].'&type=sale">'.$ftd.'</a></td>'
					),
				
					(object) array(
					  'id' => 'Commission',
					  'str' => '<td style="text-align: center;">'.price($totalCom).'</td>'
					)	,			
					(object) array(
					  'id' => 'click_to_signup',
					  'str' => '<td style="text-align: center;">'.@number_format(($totalReal/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
					(object) array(
					  'id' => 'commission_traffic',
					  'str' => '<td style="text-align: center;">'.@number_format(($ftd/$totalTraffic['totalClicks'])*100,2).' %</td>'
					),
				);
				
				$listReport .= '<tr>'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'</tr>';
				
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalLeadsAccounts += $totalLeads;
				
				$totalRealAccounts += $totalReal;
				$totalTotalInstallation += $totalInstallation;
				$totalFTD += $ftd;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount['amount'];
				$totalDepositAmount += $sumDeposits;
				
				$totalComs += $totalCom;
				
				
                                
                            unset($arrRange); // Clear up the memory.
			} // End of time-periods loop.
                        
                    // Mark given wallet as processed.
                
		}
                
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		
		
		$tableArr = Array(
						
			
			(object) array(
			  'id' => 'id',
			  'str' => '<th style="text-align: left;">'.lang('ID').'</th>'
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th style="text-align: left;">'.lang('UserName').'</th>'
			),
			(object) array(
			  'id' => 'totalViews',
			  'str' => '<th>'.lang('Impressions').'</th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th>'.lang('Clicks').'</th>'
			),
			
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th>'.lang(ptitle('Lead')).'</th>'
			),
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th>'.lang(ptitle('Signups')).'</th>'
			),
			(object) array(
			  'id' => 'totalInstallation',
			  'str' => '<th>'.lang(ptitle('Installation')).'</th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th>'.lang('Sale').'</th>'
			),
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.lang('Commission').'</th>'
			),
			(object) array(
			  'id' => 'click_to_signup',
			  'str' => '<th>'.lang(ptitle('Click to Signup')).'</th>'
			),
			(object) array(
			  'id' => 'commission_traffic',
			  'str' => '<th>'.lang(ptitle('Click to Sale')).'</th>'
			)				
		);
		
		
		
		
		$tableArr2 = Array(
						
		
			(object) array(
			  'id' => 'id',
			  'str' => '<th style="text-align: left;"><b>'.lang('Total').':</b></th>'
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th style="text-align: left;"><b></b></th>'
			),
			(object) array(
			  'id' => 'totalViews',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=traffic&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressions.'</a></th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=traffic&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicks.'</a></th>'
			),

	
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>'
			),
		
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>'
			),
			(object) array(
			  'id' => 'totalInstallation',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=installation">'.$totalTotalInstallation.'</a></th>'
			),
			(object) array(
			  'id' => 'ftd',
			  'str' => '<th><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>'
			),
			
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.price($totalComs).'</th>'
			)		,		
			(object) array(
			  'id' => 'click_to_signup',
			  'str' => '<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>'
			),
			(object) array(
			  'id' => 'commission_traffic',
			  'str' => '<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>'
			)
		);
		
		
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form method="get">
			<input type="hidden" name="act" value="affiliates"/>
				<table><tr>
						<td>'.lang('Period').'</td>
						
						'.($userlevel == "admin"? '<td>'.lang('Group ID').'</td>':'').'
						<td>'.lang('Products List').'</td>
						<td></td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					
					<!--td><input type="text" name="group_id" value="'.$group_id.'" id="group_id" style="width: 60px; text-align: center;" /></td-->
                                        '.($userlevel == 'admin'?'<td width="100">
                                            <select name="group_id" style="width: 100px;">
                                                <option value="">'.lang('All Groups').'</option>'
                                                . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                    . lang('General') 
                                                . '</option>' 
                                                . listGroups($group_id) 
                                            . '</select>
                                        </td>':'').'
					<td>
					<select name="product_id" style="width: 150px;"><option value="0">'.lang('Choose Product').'</option>'. listProducts($product_id) . '</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#affiliatesData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#affiliatesData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>' : '').'
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" >'.lang('Affiliates Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="../images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
			$tableStr = '
			<table width="100%" class="tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="affiliates">
				<thead><tr>
					'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').
				'</tr></thead><tfoot><tr>
					'.setTable($tableArr2, $boxaName, $set->userInfo['productType'], '').'
					
				</tr></tfoot>
				<tbody>
				'.$listReport.'
			</table>
			<script type="text/javascript" src="../js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="../js/impromptu/dist/jquery-impromptu.min.css"/>              
			<script>
				$(document).ready(function(){
					try{
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'affiliatesData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#affiliates")[0].config.rowsCopy).each(function() {
						txt += "<tr>" + $(this).html()+"</tr>";
					});
					txt += "</tbody>";
					txt += "<tfoot>" + tfoot + "</tfoot>";
					txt += "</table>";
					$("body").append("<div style=\'display:none\'>"+ txt +"</div>");
					}
					catch(e){
						//exception
					}
					$(".saveReport").on("click",function(){
						$.prompt("<label>'. lang("Provide name for report") .': <br/><input type=\'text\' name=\'report_name\' value=\'\' style=\'width:80wh\' required></label><div class=\'err_message\' style=\'color:red\'></div>", {
								top:200,
								title: "'. lang('Add to Favorites') .'",
								buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
								submit: function(e,v,m,f){
									if(v){
										name = $("[name=report_name]").val();
										if(name != ""){
											
											url = window.location.href;
											user = "'. $set->userInfo['id'] .'";
											level = "'. $userlevel .'";
											type = "add";
											
											saveReportToMyFav(name, \'products - affiliates\',user,level,type);
										}
										else{
											$(".err_message").html("'. lang("Enter Report name.") .'");
											return false;
										}
									}
									else{
										//
									}
								}
							});
					});
			
				});
				</script>
			';
		
		excelExporter($tableStr, 'Affiliates');
		$set->content .= $tableStr . '</div>' . getPager();
		$myReport = lang("Product Affiliates");
		include "common/ReportFieldsModal.php";
		theme();
		
?>