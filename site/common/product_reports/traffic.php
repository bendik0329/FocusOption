<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$set->content .= '<script type="text/javascript" src="../../js/tableExport/tableExport.js"></script>
<script type="text/javascript" src="../../js/tableExport/filesaver.js"></script>
<script type="text/javascript" src="../../js/tableExport/jquery.base64.js"></script>';
$filename = "traffic_data_" . date('YmdHis');

$pageTitle = lang('Traffic Report');
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

if($userlevel == 'manager')
	$group_id       = $set->userInfo['group_id'];




		$page = (isset($page) || !empty($page))?$page:1;
		$set->page = $page;
		
		$start_limit = $page==1?0:$set->rowsNumberAfterSearch * ($page -1);
		$end_limit = $set->rowsNumberAfterSearch * $page;
		
		
		$clickArray = [];
		
                $where = ' 1 = 1 ';
            
                /**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($product_id) {
                    $where .= " AND product_id='".$product_id."' ";
                }
                
		if ($banner_id) {
                    $where .= " AND banner_id='".$banner_id."' ";
                }
				
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
                }
                
		if ($profile_id) {
                    $where  .= " AND profile_id='".$profile_id."' ";
                }
		
		
         
		
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
		

		//general merchants information
		
		
		
		
		
		
		$where_main = $where;
		$where_main =  str_replace('affiliate_id','t.affiliate_id', $where_main) ;
		 $where_main =  str_replace('product_id','t.product_id', $where_main) ;
		 $where_main =  str_replace('profile_id','t.profile_id', $where_main) ;
		 $where_main =  str_replace('banner_id','t.banner_id', $where_main) ;
		 
		 $type_filter = "";
		 if($type == 'clicks')
		 $type_filter = ' and t.clicks > 0';
		 else if($type == "views")
		 $type_filter = ' and t.views > 0';
		 
		 $sql = "SELECT count(*) as total_records FROM traffic  t"
					. " WHERE " . $where . $type_filter .
					(!empty($unique_id) ? ' and t.uid = ' . $unique_id :'') 
					." and t.product_id>0 and  t.rdate BETWEEN '".$from."' AND '".$to. "' ";// and t.uid >0 ";
		// die ($sql);
		$totalRec = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		
		$total_records = $totalRec['total_records'];
		$set->total_records = $total_records; 
					
		$sql = "SELECT t.*,lang.title as language, m.title as product_name,af.username as affiliate_username from traffic t"
					. " INNER JOIN products_items m on m.id = t.product_id "
					. " INNER JOIN affiliates af on af.id = t.affiliate_id "
					. " LEFT JOIN languages lang on lang.id = t.language_id" 
					. " WHERE " . $where_main . $type_filter
					 . "  and product_id > 0 and t.uid > 0"
					. (!empty($unique_id) ? ' and t.uid = ' . $unique_id :'')
					." and t.rdate BETWEEN '".$from."' AND '".$to. "' ORDER BY id DESC limit " . $start_limit. ", " . $end_limit;
		
		
		// die ($sql);
		
	/*  $sql = "SELECT t.*from traffic t "
					. "WHERE  " . $where_main . " and t.rdate BETWEEN '".$from."' AND '".$to. "'";*/
			
		$clickqq = function_mysql_query($sql,__FILE__);
		while($clickww = mysql_fetch_assoc($clickqq)){
			// if($clickww['uid'] !=0)
			{
				$clickArray[$clickww['id']]['traffic_id'] = $clickww['id'];
				 $clickArray[$clickww['id']]['uid'] = $clickww['uid'];
				 $clickArray[$clickww['id']]['clicks'] = $clickww['clicks'];
				 $clickArray[$clickww['id']]['views'] = $clickww['views'];
				 $clickArray[$clickww['id']]['traffic_date'] = $clickww['rdate'];
				
				 $clickArray[$clickww['id']]['type'] = $clickww['type'];
				 
				 $clickArray[$clickww['id']]['banner_id'] = $clickww['banner_id'];
				 $clickArray[$clickww['id']]['profile_id'] = $clickww['profile_id'];
				 $clickArray[$clickww['id']]['param'] = $clickww['param'];
				 $clickArray[$clickww['id']]['param2'] = $clickww['param2'];
				 $clickArray[$clickww['id']]['refer_url'] = $clickww['refer_url'];
				 $clickArray[$clickww['id']]['language'] = $clickww['language'];
				 $clickArray[$clickww['id']]['country'] = $clickww['country_id'];
				 $clickArray[$clickww['id']]['ip'] = $clickww['ip'];
				 $clickArray[$clickww['id']]['product_id'] = $clickww['product_id'];
				 $clickArray[$clickww['id']]['product_name'] = $clickww['product_name'];
				 $clickArray[$clickww['id']]['affiliate_username'] = $clickww['affiliate_username'];
				 $clickArray[$clickww['id']]['affiliate_id'] = $clickww['affiliate_id'];
				 
				 $clickArray[$clickww['id']]['platform'] = $clickww['platform'];
				 
				 if(is_null($clickww['os']))
					$clickArray[$clickww['id']]['platform'] = "";
			 
				 
				 $clickArray[$clickww['id']]['os'] = $clickww['os'];
				 $clickArray[$clickww['id']]['osVersion'] = $clickww['osVersion'];
				 
				 $clickArray[$clickww['id']]['browser'] = $clickww['browser'];
				 $clickArray[$clickww['id']]['browserVersion'] = $clickww['broswerVersion'];
				
				$l = 0;
				$totalLeads=0;
				$totalDemo=0;
				$totalReal=0;
				$ftd=0;
				$ftd_amount=0;
				$real_ftd = 0;
				$real_ftd_amount = 0;
				$netRevenue = 0;
				$depositingAccounts=0;
				$sumDeposits=0;
				$bonus=0;
				$chargeback = 0;
				$cpaAmount=0;
				$withdrawal=0;
				$volume=0;
				$lots=0;
				$depositsAmount=0;
				$totalCom=0;
				 if(!empty($clickArray)){           
				   // registration (leads + demo + real)
				$where_reg = $where;
				$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
				 $where_reg =  str_replace('product_id','dg.product_id', $where_reg) ;
				 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
				  $where_reg =  str_replace('banner_id','dg.banner_id', $where_reg) ;
				  
				  
				  
				  
				  
				  
				  
				  
				  if ($clickww['uid']>0) {
				$sql = "SELECT dg.* FROM data_reg dg"
							." WHERE " . $where_reg 
							." and dg.product_id>0 and  dg.uid > 0"
							. " and dg.rdate >= '" . $clickww['rdate'] . "'"
							. " AND dg.uid = " . $clickww['uid'];
				// echo $sql;
				$regqq = function_mysql_query($sql,__FILE__);
				
				$arrTierCplCountCommissionParams = [];
					// die ($sql);
				$regArray = array();
				while ($regww = mysql_fetch_assoc($regqq)) {
					
					//if(!empty($regww['trader_id'])){
						$tranrow['id'] = $regww['id'];
						$tranrow['rdate'] = $regww['rdate'];
						$tranrow['affiliate_id'] = $regww['affiliate_id'];
						$tranrow['trader_id'] = $regww['trader_id'];
						$tranrow['product_id'] = $regww['product_id'];
						$regArray[] = array($tranrow);

						$clickArray[$clickww['id']]['reg_date'] = $regww['reg_date'];
						$clickArray[$clickww['id']]['trader_id'] = $regww['trader_id'];
						$clickArray[$clickww['id']]['trader_name'] = $regww['trader_alias'];
						
						$clickArray[$clickww['id']]['sale_status'] = $regww['saleStatus'];
						
						$strAffDealType = getAffiliateTierDeal($regww['product_id'],$regww['affiliate_id']);
						$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
						
						if ($regww['type'] == "lead"){
							//$totalLeads++;
								$clickArray[$clickww['id']]['leads'] += 1;
						}
						if ($regww['type'] == "demo"){
								$clickArray[$clickww['id']]['demo'] += 1;
						} 
						if ($regww['type'] == "real") {
							if (!$boolTierCplCount) {
								$arrTmp = [
									'product_id'  => $regww['product_id'],
									'affiliate_id' => $regww['affiliate_id'],
									'rdate'        => $regww['rdate'],
									'banner_id'    => $regww['banner_id'],
									'merchant_id'    => $regww['merchant_id'],
									'trader_id'    => $regww['trader_id'],
									'profile_id'   => $regww['profile_id'],
								];
								
								$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
								// $totalCom = 1;
								$clickArray[$clickww['id']]['total_com'] += $totalCom;
								
							} else {
								// TIER CPL.
								if (array_key_exists($regww['affiliate_id'], $arrTierCplCountCommissionParams)) {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']]['arrTmp']['amount']++;
								} else {
									$arrTierCplCountCommissionParams[$regww['affiliate_id']] = [
										'from'                => $from,
										'to'                  => $to,
										'onlyRevShare'        => 0,
										'groupId'             => (is_null($group_id ? -1 : $group_id)),
										'arrDealTypeDefaults' => $arrDealTypeDefaults,
										'arrTmp'              => [
											'product_id'  => $regww['product_id'],
											'affiliate_id' => $regww['affiliate_id'],
											'rdate'        => $regww['rdate'],
											'banner_id'    => $regww['banner_id'],
											'trader_id'    => $regww['trader_id'],
											'profile_id'   => $regww['profile_id'],
											'amount'       => 1,
											'tier_type'    => 'cpl_count',
										],
									];
								}
							}
							
							unset($arrTmp);
							//$totalReal++;
							$clickArray[$clickww['id']]['real'] += 1;
						}
					//}
				}
				 
				 
				 if(!isset($clickArray[$clickww['id']]['trader_id'])){
					// TIER CPL.
					foreach ($arrTierCplCountCommissionParams as $intAffId => $arrParams) {
						$totalCom = getCommission(
							$arrParams['from'], 
							$arrParams['to'], 
							$arrParams['onlyRevShare'], 
							$arrParams['groupId'], 
							$arrParams['arrDealTypeDefaults'], 
							$arrParams['arrTmp']
						);
						$clickArray[$clickww['id']]['totalCom'] += 1;
						unset($intAffId, $arrParams);
					}
				 }
				
					foreach($regArray as $key=>$params){
						$trader_id = $params[0]['trader_id'];
						$regDate = $params[0]['rdate'];
						if(!is_null($trader_id)){
						$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($product_id)?$product_id:0), 0, (is_null($group_id) ? 0 : $group_id),0,0,0,$trader_id);
						foreach ($arrFtds as $arrFtd) {
								$real_ftd++;
								$clickArray[$clickww['id']]['real_ftd'] += 1;
								
								$real_ftd_amount = $arrFtd['amount'];
								$clickArray[$clickww['id']]['real_ftd_amount'] += $real_ftd_amount;
								
								$beforeNewFTD = $ftd;
								getFtdByDealType($arrFtd['product_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd,false);
							
								if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
									
									$arrFtd['isFTD'] = true;
									$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
									
									$clickArray[$clickww['id']]['totalCom'] += $totalCom;
								}
								$clickArray[$clickww['id']]['ftd'] = $ftd;
										
								$clickArray[$clickww['id']]['ftd_amount'] = $ftd_amount;
								unset($arrFtd);
						
						}
					
			
				
				
				
					//Sales
					$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
								 . "INNER JOIN data_reg AS data_reg ON tb1.product_id = data_reg.product_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
								 . "WHERE  tb1.trader_id = " .  $trader_id
							//	 . ' and tb1.rdate between "' . $from . '" AND "' . $to . '"' 
								. " and tb1.product_id>0 and tb1.rdate >= '" . $regDate . "'"
								. (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
								 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
								 . (isset($banner_id) && !empty($banner_id) ? ' AND data_reg.banner_id = "'.$banner_id.'"' :'') 
								 .(!empty($unique_id) ? ' and data_reg.uid = ' . $unique_id :'');
					
					$salesqq = function_mysql_query($sql,__FILE__);
								
					while ($salesww = mysql_fetch_assoc($salesqq)) {
							
							if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
								$depositingAccounts++;
								$clickArray[$clickww['id']]['depositingAccounts'] += 1;
								
								$sumDeposits = $salesww['amount'];
								$clickArray[$clickww['id']]['sumDeposits'] += $salesww['amount'];
								
								// $depositsAmount+=$salesww['amount'];
							}
							
							if ($salesww['data_sales_type'] == "bonus") {
									$bonus = $salesww['amount'];
									$clickArray[$clickww['id']]['bonus'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == "withdrawal"){ 
									$withdrawal = $salesww['amount'];
									$clickArray[$clickww['id']]['withdrawal'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == "chargeback"){
									$chargeback = $salesww['amount'];
									$clickArray[$clickww['id']]['chargeback'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == 'volume') {
								$volume = $salesww['amount'];
								$clickArray[$clickww['id']]['volume'] += $salesww['amount'];
								$arrTmp = [
									'product_id'  => $salesww['product_id'],
									'affiliate_id' => $salesww['affiliate_id'],
									'rdate'        => $salesww['rdate'],
									'banner_id'    => $salesww['banner_id'],
									'trader_id'    => $salesww['trader_id'],
									'profile_id'   => $salesww['profile_id'],
									'type'       => 'volume',
									'amount'       => $salesww['amount'],
								];
								
								$totalCom = getCommission(
									$from, 
									$to, 
									0, 
									(isset($group_id) && $group_id != '' ? $group_id : -1), 
									$arrDealTypeDefaults, 
									$arrTmp
								);

								$clickArray[$clickww['id']]['totalCom'] += $totalCom;
							}
						
						
							// end of data_sales loop
					
						}
						}						
						
					
				 
				 // trader id empty loop end
				 } // if uid >0
			 
				  }
				 }
			}
			
			}//uid 0 loop end	
			 
		
		//}
		foreach($clickArray as $data){
			$refer_url = $data['refer_url'];
		    if(strlen($data['refer_url'])>50)
				$refer_url = substr($data['refer_url'],0,49). "...";
			
			$country_name = $allCountriesArray[$data['country'] ];
			if(strtolower($country)=='any'){
				$country_name = "";
			}
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$data['traffic_id'].'</td>
				<td style="text-align: left;">'.$data['uid'] .'</td>
				<td style="text-align: center;">'.@number_format($data['views'],0).'</td>
				<td style="text-align: center;">'.@number_format($data['clicks'],0).'</td>
				<td style="text-align: left;"><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_id'] .'</a></td>
				 <td style="text-align: left;"><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_username'] .'</a></td>
				<td style="text-align: left;">'.$data['traffic_date'] .'</td>
				<td style="text-align: left;">'.ucwords($data['type']).'</td>
				<td style="text-align: left;">'. $data['product_id'] .'</td>
				<td style="text-align: left;">'. $data['product_name'] .'</td>
				<td style="text-align: left;">'. $data['banner_id'] .'</td>
				<td style="text-align: left;">'. $data['profile_id'] .'</td>
				<td style="text-align: left;">'. $data['param'] .'</td>
				<td style="text-align: left;">'. $data['param2'] .'</td>
				<td style="text-align: left;"><a href="'. $data['refer_url'] .'" target="_blank">'.$refer_url.'</td>
				<td style="text-align: left;">'. $country_name .'</td>
				<td style="text-align: left;">'. $data['ip'] .'</td>
				<td style="text-align: left;">'. ucwords($data['platform']) .'</td>
				<td style="text-align: left;">'. $data['os'] .'</td>
				<td style="text-align: left;">'. $data['osVersion'] .'</td>
				<td style="text-align: left;">'. $data['browser'] .'</td>
				<td style="text-align: left;">'. $data['browserVersion'] .'</td>
				<td style="text-align: left;">'. $data['trader_id'] .'</td>
				<td style="text-align: left;">'. $data['trader_name'] .'</td>
				<td><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&product_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=lead">'.$data['leads'].'</a></td>
				<td><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&product_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=signup">'.$data['real'].'</a></td>
				<td><a href="/'.$userlevel .'/product_reports.php?act=accounts&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&product_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=sale&trader_id='. $data['trader_id'] .'">'.$data['ftd'].'</a></td>
				<td>'.price($data['ftd_amount']).'</td>
	
			</tr>';
			
			/*$totalImpressions += $data['views'];
			$totalClicks += $data['clicks'];
			$totalLeadsAccounts += $data['leads'];
			$totalDemoAccounts += $data['demo'];
			$totalRealAccounts += $data['real'];
			$totalFTD += $data['ftd'];
			$totalDeposits += $data['depositingAccounts'];
			$totalFTDAmount += $data['ftd_amount'];
			$totalDepositAmount += $data['sumDeposits'];
			$totalVolume += $data['volume'];
			$totalBonusAmount += $data['bonus'];
			$totalWithdrawalAmount += $data['withdrawal'];
			$totalChargeBackAmount += $data['chargeback'];
			$totalNetRevenue += $data['netRevenue'];
			$totalComs += $data['totalCom'];
			$totalRealFtd += $data['real_ftd'];
			$totalRealFtdAmount += $data['real_ftd_amount'];
                        $l++;
		// echo $ftd_amount.'<br>';
		$ftd_amount = $real_ftd_amount = 0;
		// $totalLeads = $totalDemo = $totalReal = $ftd = $depositingAccounts = $ftd_amount = $sumDeposits = $volume = $bonus = $withdrawal = $chargeback= $netRevenue= $totalCom= $real_ftd= $real_ftd_amount = 0;
				*/		
		}
                
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->totalRows  = $l;
		$set->sortTable  = 1;
		
		$set->content   .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="traffic" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Products List').'</td>
					<td>'.lang('Unique ID').'</td>
					<td width=160>'.lang('Affiliate ID').'</td>
					<td>'.lang('Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					
					<td><select name="product_id" style="width: 150px;"><option value="0">'.lang('Choose Product').'</option>'. listProducts($product_id) . '</select></td>
					<td><input type="text" name="unique_id" value="'.$unique_id.'" /></td>
					<td><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" /-->
					<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
					</td>
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="clicks" '.($type == "clicks" ? 'selected' : '').'>'.lang('Clicks').'</option>
						<option value="views" '.($type == "views" ? 'selected' : '').'>'.lang('Views').'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#trafficData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#trafficData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>' : '').'
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" style="width: 2400px;">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="../images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2400" class="tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="trafficTbl">
					<thead><tr>
						<th style="text-align: left;">'.lang('ID').'</th>
						<th style="text-align: left;">'.lang('UID').'</th>
						
						<th style="text-align: center;">'.lang('Impression').'</th>
						<th style="text-align: center;">'.lang('Click').'</th>
						
						<th style="text-align: left;">'.lang('Affiliate ID').'</th>
						<th style="text-align: left;">'.lang('Affiliate Username').'</th>
						<th>'.lang('Date').'</th>
						<th>'.lang('Type').'</th>
						<th style="text-align: left;">'.lang('Product ID').'</th>
						<th style="text-align: left;">'.lang('Product').'</th>
						<th style="text-align: left;">'.lang('Banner ID').'</th>
						<th style="text-align: left;">'.lang('Profile ID').'</th>
						<th style="text-align: left;">'.lang('Param').'</th>
						<th style="text-align: left;">'.lang('Param2').'</th>
						<th style="text-align: left;">'.lang('Refer URL').'</th>
						<th style="text-align: left;">'.lang('Country').'</th>
						<th style="text-align: left;">'.lang('IP').'</th>
						<th style="text-align: left;">'.lang('Platform').'</th>
						<th style="text-align: left;">'.lang('Operating System').'</th>
						<th style="text-align: left;">'.lang('OS Version').'</th>
						<th style="text-align: left;">'.lang('Browser').'</th>
						<th style="text-align: left;">'.lang('Broswer Version').'</th>
						
						<th style="text-align: left;">'.lang('Trader Id').'</th>
						<th style="text-align: left;">'.lang('Trader Name').'</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Signup')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<!--th>'.lang('Total FTD').'</th-->

					</tr></thead>
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
					txt = "<table id=\'trafficData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#trafficTbl")[0].config.rowsCopy).each(function() {
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
											
											saveReportToMyFav(name, \'products - traffic\',user,level,type);
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
			
		excelExporter($tableStr, 'Clicks');
		$set->content.=$tableStr.'</div>'.getURLPager();
		
		$myReport = lang("Product Traffic");
		include "common/ReportFieldsModal.php";
		
		theme();


?>