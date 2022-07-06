<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

	$pageTitle = lang(ptitle('Trader Stats Report'));
	$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
	if($userlevel == "manager")
		$group_id = $set->userInfo['group_id'];
	
		$l=0;
		
		
		if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."'";
		if ($group_id) $where .= " AND group_id='".$group_id."'";
		if ($banner_id) $where .= " AND banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND profile_id='".$profile_id."'";
		if ($trader_id) $where .= " AND trader_id='".$trader_id."'";
		if ($type != "" AND $type != "deposit") $where .= " AND type='".$type."'";
		
		if ($merchant_id) {
			// $ww = dbGet($merchant_id,"merchants");
			$ww = getMerchants($merchant_id,0);
			if (!$ww['id']) _goto();
			$brokers_ids[] = $ww['id'];  
			$brokers[] = $ww['name'];
			$brokers_formula = $ww['rev_formula'];
			$productType = $ww['productType'];
			$statsTable = $ww['productType']=='binaryoption' ? "sales" : "stats";
			
		} else {
			// $qq=function_mysql_query("SELECT id,name,rev_formula,productType FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
			// while ($ww=mysql_fetch_assoc($qq)) {
		$merchantsArr = getMerchants(0,1);
		foreach ($merchantsArr as $ww) {
				$brokers_ids[] = $ww['id'];
				$brokers[] = $ww['name'];
				$brokers_formula = $ww['rev_formula'];
				$productType[] = $ww['productType'];
				$statsTable = $ww['productType']=='binaryoption' ? "sales" : "stats";
				}
		}
		
		
		
		
		
		
			$filterhtml = '';
			

			if ($statsTable =='sales') {
				
			$sql = "	SELECT distinct type FROM data_".$statsTable." where type ='volume' order by type";
			}
			else {
			$sql = "	SELECT distinct type FROM data_".$statsTable." order by type";
			}
				// die ($sql);
				
				
				$qq=function_mysql_query($sql,__FILE__);
				$filterhtml .='<option value="" '.($type =='' ? 'selected' : '').'>'.lang('All Types').'</option>';
				while ($ww=mysql_fetch_assoc($qq)) {
					//$filterhtml .='<option value="'.$ww['type'].'">'.lang($ww['type']).'</option>';
					$filterhtml .='<option value="'.$ww['type'].'" '.($type == $ww['type'] ? 'selected' : '').'>'.ucwords(str_replace('_',' ',$ww['type'])).'</option>';
					
				}
				
		for ($i=0; $i<=count($brokers)-1; $i++) {
			
			$formula = $brokers_formula[$i];
			$broker['name'] = $brokers[$i];
		$productType = $productType[$i];
		// die($productType);
		
		$statsTable = $productType=='binaryoption' ? "sales" : "stats";
		
				$sql = "	SELECT ds.*"."	FROM data_".$statsTable." AS ds ";
				
			
			if ($statsTable =='sales') {
						$countriesLong = getLongCountries('sales');
						$sql .= "WHERE 5=5 and 1=1 and ds.type='volume' and ds.merchant_id = '" . $brokers_ids[$i]."' and ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ".$where;//." GROUP BY ds.trader_id";
			}
			else {
						$countriesLong = getLongCountries('stats');
						$sql .= "WHERE 5=5 and ds.merchant_id = '" . $brokers_ids[$i]."' and ".$globalWhere." rdate BETWEEN '".$from."' AND '".$to."' ".$where;//." GROUP BY ds.trader_id";
			}
			
		
				
				$sql.=" ORDER BY ds.trader_id ASC";
				
				// die ($sql);
				$qq=function_mysql_query($sql,__FILE__);
			
			while ($ww=mysql_fetch_assoc($qq)) {
				// $merchantInfo = dbGet($ww['merchant_id'],"merchants");
				$merchantInfo = getMerchants($merchant_id,0);
				
				$productType = $merchantInfo['producttype'];
				
				$listReport .= '<tr>
						<td>'.$ww['trader_id'].'</td>
						<td>'.$ww['trader_alias'].'</td>
						<td>'.$ww['tranz_id'].'</td>
						<td>'. date("Y/m/d H:i:s", strtotime($ww['rdate'])).'</td>
						<td>'.$countriesLong[$ww['country']].'</td>
						<td><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$ww['affiliate_id'].'</a></td>
						<td>'.$brokers_ids[$i].'</td>
						<td>'.strtoupper($brokers[$i]).'</td>
						<td style="text-align: left;"><a href ="'.$set->SSLprefix.$userlevel.'/reports.php?act=banner&banner_id='.$ww['banner_id'].'" target="_blank">'.$ww['banner_id'].'</a></td>
						<td>'.$ww['profile_id'].'</td>
						<td>'.$ww['group_id'].'</td>
						<td>'.$ww['ctag'].'</td>
						<td>'.$ww['freeParam'].'</td>
						<td>'.$ww['freeParam2'].'</td>
						<td>'.ucwords(str_replace('_',' ',$ww['type'])).'</td>
						<td>'.price($ww['amount']).'</td>
						'.($productType=='forex' ? '<td>'.price($ww['turnover']).'</td>' : '').'
						'.($productType=='forex' ? '<td>'.price($ww['spread']).'</td>' : '').'
						'.($productType=='forex' ? '<td>'.price($ww['pnl']).'</td>' : '').'
					</tr>';
				$l++;
				
				$totalAmount += $ww['amount'];
				$totalTurnover += $ww['turnover'];
				$totalSpread += $ww['spread'];
				$totalPnl += $ww['pnl'];
				
			}
				

				
		}
	
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $l;
		

		//die($filterhtml);
		
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form action="'.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="stats" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						<td>'.lang('Affiliate ID').'</td>
						<td>'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						'.($userlevel == 'admin'?'<td>'.lang('Group ID').'</td>':'') .'
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from,$to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
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
							<select name="type" style="width: 100px;">'
								.$filterhtml.'
							</select>
						</td>
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
				'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle"  class="table">'.($merchant_id ? strtoupper($broker['name']) : '').' '.lang('Report Results').'</div>
			<div style="background: #F8F8F8;">';
			//width 1700
				$tableStr='<table  class="table tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr class="table-row">
						<th class="table-cell">'.lang(ptitle('Trader ID')).'</th>
						<th class="table-cell">'.lang(ptitle('Trader Alias')).'</th>
						<th class="table-cell">'.lang(ptitle('Transaction ID')).'</th>
						<th title="mm/dd/yyyy"  class="table-cell">'.lang('Date').'</th>
						
						<th class="table-cell">'.lang('Country').'</th>
						<th class="table-cell">'.lang('Affiliate ID').'</th>
						<th class="table-cell">'.lang('Merchant ID').'</th>
						<th class="table-cell">'.lang('Merchant Name').'</th>
						<th  class="table-cell"style="text-align: left;">'.lang('Creative ID').'</th>
						<th class="table-cell">'.lang('Profile ID').'</th>
						<th class="table-cell">'.lang('Group ID').'</th>
						<th class="table-cell">'.lang(ptitle('cTag')).'</th>
						<th class="table-cell">'.lang('Free Parameter').'</th>
						<th class="table-cell">'.lang('Free Parameter2').'</th>
						<th class="table-cell">'.lang('Type').'</th>
						<th class="table-cell">'.lang(ptitle('Amount')).'</th>
						'.($productType=='forex' ? '<th class="table-cell">'.lang(ptitle('Turnover')).'</th>' : '').'
						'.($productType=='forex' ? '<th class="table-cell">'.lang(ptitle('Spread')).'</th>' : '').'
						'.($productType=='forex' ? '<th class="table-cell">'.lang(ptitle('PNL')).'</th>' : '').'
						
					</tr></thead>
					
					<tbody>
					'.$listReport.($productType=='forex' ? '
					<tfoot><tr>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></tth>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th>'.price($totalAmount).'</th>
						'.($productType=='forex' ? '<th>'.price($totalTurnover).'</th>' : '').'
						'.($productType=='forex' ? '<th>'.price($totalSpread).'</th>' : '').'
						'.($productType=='forex' ? '<th>'.price($totalPnl).'</th>' : '').'
						
					</tr></tfoot>' : '').'
				</table>';
			
		excelExporter($tableStr,'stats');
			
		$set->content.=$tableStr.'
			</div>'.getPager();
		
		theme();

?>