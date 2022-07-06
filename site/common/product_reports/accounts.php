<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}
$pageTitle = lang('Accounts Report');
$set->breadcrumb_title = lang($pageTitle);
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href= "'. (empty($userlevel)?'affiliate':$userlevel).'/">'.lang('Dashboard').'</a></li>
			<li><a href="'. (empty($userlevel)?'affiliate':$userlevel).'/products.php?act=products">'.lang('Products Place').'</a></li>
			<li><a href="'. $set->uri .'" class="arrow-left">'.lang($pageTitle).'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		$set->content .= '<script type="text/javascript" src="../../js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="../../js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="../../js/tableExport/jquery.base64.js"></script>';
		$filename = "Accounts_data_" . date('YmdHis');


		$listReport = '';
                
          $countryList = getLongCountries();
		  $searchInSql = " BETWEEN '" . $from . "' AND '" . $to . "' ";
		  
		  
				$productsArray = array();
				$qry = "select * from products_items where valid=1 " . (!empty($product_id) ? " and id in ( " . $product_id . ")": "");
				$rsc = mysql_query($qry);
		while ($ww = mysql_fetch_assoc($rsc)) {
			$productsArray[$ww['id']] = $ww;
			$arrDealTypeDefaults = getProductsDealTypeDefaults($ww['id']);
			$productsArray[$ww['defaultDeal']] = $arrDealTypeDefaults;
		}
		
                
		$l = 0;
					
	if ($type=='sale') {
	
	$sql = "select l.title as creativelang,mc.title as creativename,mc.type as creativetype, pi.title as name , af.username as aff_username, dr.* from data_reg  dr 
		
		inner join products_items pi on dr.product_id = pi.id 
		left join merchants_creative mc on dr.banner_id = mc.id 
		left join languages l on l.id = mc.language_id
		inner join affiliates af on dr.affiliate_id = af.id 
		inner join data_sales ds on ds.product_id= dr.product_id and ds.trader_id = dr.trader_id		 
		where " .( $globalWhere=='affiliate' ?  " dr.affiliate_id = ". $set->userInfo['id'] ." AND ":"")." pi.valid=1 and dr.product_id>0 ".
		(isset($product_id) && !empty($product_id) ? ' AND dr.product_id in( ' . $product_id.") " : '').
		(isset($group_id) && $group_id != "" ? ' AND group_id = ' . $group_id : '').
		(isset($type) && $type != "" ? ' AND ds.type = "deposit"' : '');
		" and ds.rdate " . $searchInSql;
		
		// die($sql);

	}
else	
		$sql = "select l.title as creativelang,mc.title as creativename,mc.type as creativetype, pi.title as name , af.username as aff_username, dr.product_id as product_id,dr.uid as uid, dr.* from data_reg  dr 
		
		inner join products_items pi on dr.product_id = pi.id 
		left join merchants_creative mc on dr.banner_id = mc.id 
		left join languages l on l.id = mc.language_id
		inner join affiliates af on dr.affiliate_id = af.id 
				
		where " .( $globalWhere=='affiliate' ?  " dr.affiliate_id = ". $set->userInfo['id'] ." AND ":"")." pi.valid=1 and dr.product_id>0 ".
		(isset($product_id) && !empty($product_id) ? ' AND dr.product_id in( ' . $product_id.") " : '').
		(isset($group_id) && $group_id != "" ? ' AND dr.group_id = ' . $group_id : '').
		(isset($type) && $type != "" ? ' AND dr.type = "'. $type .'"' : '')
		. " and dr.rdate " . $searchInSql;
		 // die($sql);
		
		$qq = function_mysql_query($sql,__FILE__);
// die ($sql);		
                    $fromDate = $from;
                    $toDate   = $to;
                    
		while ($ww = mysql_fetch_assoc($qq)) {
                    $ftdUsers = '';
                    $l++;
                    $netRevenue = 0;
                    $totalCom=0;
                    $ftd=0;
                    $totalLeads = 0;
                    $totalDemo = 0;
                    $totalReal = 0;
                    $totalInstallation = 0;
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
		unset($ftd_amount);
		unset($totalTraffic);
		
		
		            
                    $productName = strtolower($ww['name']);
                    
                    
                    
                    
                
                    
                            
                        
                       

					$arrDealTypeDefaults = getProductsDealTypeDefaults($ww['product_id']);


			
		
               $totalTraffic = [];
                            $arrClicksAndImpressions = getClicksAndImpressions(
                                $from, 
                                $to, 
                                $ww['product_id'], 
                                ($globalWhere=='affiliate'? $set->userInfo['id']: null), 
                                (isset($group_id) && $group_id != '' ? $group_id : null)
								,null,  null, null, null,true,$ww['uid'])
                            ;

                            
                            $totalTraffic['totalViews']  = $arrClicksAndImpressions['impressions'];
                            $totalTraffic['totalClicks'] = $arrClicksAndImpressions['clicks'];


							
                                        
                                         $arrTmp = [
                                                'count'  => $totalTraffic['totalClicks'],
                                                'product_id'  => $ww['product_id'],
                                                'dealtype'   => 'cpc',
                                            ];
                                            
									
                                            $a= getProductCommission(
											
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
											
											$totalCom +=$a;
                                            unset($arrTmp);
                                        
                                            
                              
							
							
							
                            $sql = "SELECT * FROM data_reg "
                                    . "WHERE trader_id = " . $ww['trader_id'] . " and product_id = '" . $ww['product_id'] . "' AND "
                                    . " rdate " . $searchInSql 
                                    . (isset($group_id) && $group_id != '' ? ' AND group_id = ' . $group_id . ' ' : '');
                            // die ($sql);
                            $regqq = function_mysql_query($sql,__FILE__);
                            
                            
                            
                            while ($regww = mysql_fetch_assoc($regqq)) {
								
								
                                
                                
                                    if ($regww['type'] == "lead") {
										$totalLeads++;
										
										
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
                                                $arrTmp
                                            );
                                            
											$totalCom +=$a;
											
											
										
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
                                            unset($arrTmp);
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
                                            unset($arrTmp);
                                        $totalInstallation++;
                                            
                                    }
							}
                            
                                $strSql = "SELECT *, af.username  as aff_username,data_sales.type AS data_sales_type, data_sales.rdate AS data_sales_rdate  FROM data_sales AS data_sales "
                                        . "INNER JOIN data_reg AS data_reg ON data_sales.product_id = data_reg.product_id AND data_sales.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
                                        . "inner join affiliates af on data_reg.affiliate_id = af.id " 
                                        . "WHERE data_sales.product_id> 0 and  data_sales.type='deposit' and data_sales.product_id = '" . $ww['product_id'] . "' and data_sales.trader_id = '" .$ww['trader_id']. "' AND data_sales.rdate "
                                        . $searchInSql . " "
                                        . (isset($group_id) && $group_id != '' ? ' AND data_sales.group_id = ' . $group_id . ' ' : '');
                                // die ($strSql);
								$salesqq = function_mysql_query($strSql,__FILE__);
								$netDepositTransactions = array();
								while ($salesww = mysql_fetch_assoc($salesqq)) {
                                    
									
									//if ($salesww['type'] == 'deposit') { // OLD.
									
										$tranrow['id'] = $salesww['id'];
										$tranrow['affiliate_id'] = $salesww['affiliate_id'];
										$tranrow['tranz_id'] = $salesww['tranz_id'];
										$tranrow['trader_id'] = $salesww['trader_id'];
										$tranrow['product_id'] = $salesww['product_id'];
										$tranrow['rdate'] =$salesww['data_sales_rdate'];
										$tranrow['amount'] = $salesww['amount'];
										$tranrow['type'] = $salesww['data_sales_type'];
										$tranrow['status'] = $salesww['status'];
										$netDepositTransactions[] = array($tranrow);
                                        $sumDeposits += $salesww['amount'];
										$depositsAmount+=$salesww['amount'];
                                        $depositingAccounts++;
                                    
                                
                                $arrFtds  = getTotalFtds(
                                    $from, 
                                    $to, 
                                    0, 
                                    $ww['product_id'], 
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
                                        getFtdByDealType($ww['product_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $ftd,false);
                                        $ftd_amount['ftdsignupdate'] = $tranrow['rdate'];
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
				$boxaName   = "admin-accounts-report-1";
                     
				                
				$tableArr = array(
						
				
						(object) array(
					  'id' => 'pid',
					  'str' => '<td style="text-align: left;">'.$ww['trader_id'].'</td>'
					),
					(object) array(
					  'id' => 'name',
					  'str' => '<td style="text-align: left;">'.$ww['name'].'</td>'
					),
					(object) array(
					  'id' => 'country',
					  'str' => '<td style="text-align: left;">'.$countryList[$ww['country']].'</td>'
					),
					(object) array(
					  'id' => 'regDate',
					  'str' => '<td style="text-align: left;">'.$ww['rdate'].'</td>'
					),
					(object) array(
					  'id' => 'affiliate_id',
					  'str' => 
					  (empty($userlevel) ?
					  '<td style="text-align: left;">'.$ww['aff_username'].' - '.$ww['affiliate_id'].'</td>'
					  :
					  '<td style="text-align: left;"><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank" title="'.$ww['affiliate_id'].'">'.$ww['aff_username'].' - '.$ww['affiliate_id'].'</a></td>')
					),
					(object) array(
					  'id' => 'creativetype',
					  'str' => '<td style="text-align: left;">'.ucwords($ww['creativetype']).'</td>'
					),
					(object) array(
					  'id' => 'creativename',
					  'str' => '<td style="text-align: left;">'.$ww['creativename'].'</td>'
					),
					(object) array(
					  'id' => 'creativelang',
					  'str' => '<td style="text-align: left;">'.$ww['creativelang'].'</td>'
					),
					(object) array(
					  'id' => 'param',
					  'str' => '<td style="text-align: left;">'.$ww['param'].'</td>'
					),
					(object) array(
					  'id' => 'totalViews',
					  'str' => '<td style="text-align: center;">'.($totalTraffic['totalViews']).'</a></td>'
					),
					(object) array(
					  'id' => 'totalClicks',
					  'str' => '<td style="text-align: center;">'.@number_format($totalTraffic['totalClicks'],0).'</td>'
					),
					
					(object) array(
					  'id' => 'total_leads',
					  'str' => '<td style="text-align: center;">'.$totalLeads.'</td>'
					),

					(object) array(
					  'id' => 'total_real',
					  'str' => '<td style="text-align: center;">'.$totalReal.'</td>'
					),
					(object) array(
					  'id' => 'total_installation',
					  'str' => '<td style="text-align: center;">'.$totalInstallation.'</td>'
					),
					(object) array(
					  'id' => 'ftdsignupdate',
					  'str' => '<td style="text-align: center;">'.$ftd_amount['ftdsignupdate'].'</td>'
					),
					(object) array(
					  'id' => 'ftd_amount',
					  'str' => '<td style="text-align: center;">'.price($ftd_amount['amount']).'</td>'
					),
					
					(object) array(
					  'id' => 'saleStatus',
					  'str' => '<td style="text-align: center;">'.ucwords($ww['saleStatus']).'</td>'
					),
					
				
					(object) array(
					  'id' => 'Commission',
					  'str' => '<td style="text-align: center;">'.price($totalCom).'</td>'
					)				
				);
				
				$listReport .= '<tr>'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'</tr>';
				
				$totalImpressions += $totalTraffic['totalViews'];
				$totalClicks += $totalTraffic['totalClicks'];
				$totalLeadsAccounts += $totalLeads;
				$totalDemoAccounts += $totalDemo;
				$totalRealAccounts += $totalReal;
				$totalfootertotalInstallation += $totalInstallation;
				$totalFTD += $ftd;
				$totalDeposits += $depositingAccounts;
				$totalFTDAmount += $ftd_amount['amount'];
				
				$totalComs += $totalCom;
				
				
                                
                            unset($arrRange); // Clear up the memory.
			} // End of time-periods loop.
                        
          
                
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		
		
		$tableArr = Array(
						
		
			(object) array(
			  'id' => 'pid',
			  'str' => '<th style="text-align: left;">'.lang('Trader ID').'</th>'
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th style="text-align: left;">'.lang('Product').'</th>'
			),
			
			
			(object) array(
			  'id' => 'country',
			  'str' => '<th>'.lang('Country').'</th>'
			),
			
			(object) array(
			  'id' => 'regDate',
			  'str' => '<th>'.lang('Signup Date').'</th>'
			),
			(object) array(
			  'id' => 'affiliate_id',
			  'str' => '<th>'.lang('Affiliate').'</th>'
			),
			
			(object) array(
			  'id' => 'creativetype',
			  'str' => '<th>'.lang('Creative Type').'</th>'
			),
			
			(object) array(
			  'id' => 'creativename',
			  'str' => '<th>'.lang('Creative Name').'</th>'
			),
			
			(object) array(
			  'id' => 'creativelang',
			  'str' => '<th>'.lang('Creative Language').'</th>'
			),
			(object) array(
			  'id' => 'param',
			  'str' => '<th>'.lang('Param').'</th>'
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
			  'str' => '<th>'.lang(('Signup')).'</th>'
			),
			(object) array(
			  'id' => 'total_installation',
			  'str' => '<th>'.lang(('installation')).'</th>'
			),
			(object) array(
			  'id' => 'ftdsignupdate',
			  'str' => '<th>'.lang('Sale Date').'</th>'
			),
			(object) array(
			  'id' => 'ftd_amount',
			  'str' => '<th>'.lang('Sale Amount').'</th>'
			),
			
								(object) array(
					  'id' => 'saleStatus',
					  'str' => '<th>' . lang('Sale Status') . '</th>'
					),
					
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.lang('Commission').'</th>'
			)				
		);
		
		
		
		
		$tableArr2 = Array(
						
		
			(object) array(
			  'id' => 'pid',
			  'str' => '<th style="text-align: left;"><b>'.lang('Total').':</b></th>'
			),
			(object) array(
			  'id' => 'name',
			  'str' => '<th></th>'
			),
			
			
			(object) array(
			  'id' => 'country',
			  'str' => '<th></th>'
			),
			
			(object) array(
			  'id' => 'regDate',
			  'str' => '<th></th>'
			),
			(object) array(
			  'id' => 'affiliate_id',
			  'str' => '<th></th>'
			),
			
			(object) array(
			  'id' => 'creativetype',
			  'str' => '<th></th>'
			),
			
			(object) array(
			  'id' => 'creativename',
			  'str' => '<th></th>'
			),
			
			(object) array(
			  'id' => 'creativelang',
			  'str' => '<th></th>'
			),
			(object) array(
			  'id' => 'param',
			  'str' => '<th></th>'
			),
		
			(object) array(
			  'id' => 'totalViews',
			  'str' => '<th>'.$totalImpressions.'</th>'
			),
			(object) array(
			  'id' => 'totalClicks',
			  'str' => '<th>'.$totalClicks.'</th>'
			),
		
		
			(object) array(
			  'id' => 'total_leads',
			  'str' => '<th>'.$totalLeadsAccounts.'</th>'
			),
		
			(object) array(
			  'id' => 'total_real',
			  'str' => '<th>'.$totalRealAccounts.'</th>'
			),
			(object) array(
			  'id' => 'total_installation',
			  'str' => '<th>'.$totalfootertotalInstallation.'</th>'
			),
			(object) array(
			  'id' => 'ftdsignupdate',
			  'str' => '<th>'.$ftdsignupdate.'</th>'
			),
			(object) array(
			  'id' => 'ftd_amount',
			  'str' => '<th>'.price($totalFTDAmount).'</th>'
			),
			
		(object) array(
					  'id' => 'saleStatus',
		  'str' => '<th></th>'
					),
			
			(object) array(
			  'id' => 'Commission',
			  'str' => '<th>'.price($totalComs).'</th>'
			)				
		);
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form method="get">
				<input type="hidden" name="act" value="accounts"/>
				<table><tr>
						<td>'.lang('Period').'</td>
						
						'.($userlevel == "admin"? '<td>'.lang('Group ID').'</td>':'').'
						<td>'.lang('Products List').'</td>
						<td>'.lang('Type').'</td>
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
					<td><select name="type" style="width: 150px;"><option value="" '.(empty($type)?'selected':'').'>'.lang('All Accounts').'</option>
					<option value="lead" '.($type=='lead'?'selected':'').'>'.lang('Lead').'</option>
					<option value="real" '.($type=='real'?'selected':'').'>'.lang('Signup').'</option>
					<option value="sale" '.($type=='sale'?'selected':'').'>'.lang('Sale').'</option>
					<option value="installation" '.($type=='installation'?'selected':'').'>'.lang('Installation').'</option></select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#accountsData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#accountsData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>' : '').'
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" >'.lang('Accounts Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="../images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
			$tableStr = '
			<table width="100%" class="tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="accountsTbl">
				<thead><tr>
					'.setTable($tableArr, $boxaName, $set->userInfo['productType'], '').'
					
					
				</tr></thead><tfoot><tr>
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
					txt = "<table id=\'accountsData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#accountsTbl")[0].config.rowsCopy).each(function() {
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
											
											saveReportToMyFav(name, \'products - accounts\',user,level,type);
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
		
		excelExporter($tableStr, 'Accounts');
		$set->content .= $tableStr . '</div>' . getPager();
		
		$myReport = lang("Product Accounts");
		include "common/ReportFieldsModal.php";
					
		theme();
		
?>