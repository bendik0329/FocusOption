<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}
$l = 1;
ini_set('memory_limit', '-1');
//$basepath = $_SERVER['DOCUMENT_ROOT'];
$where = $_POST['where'];
//require_once($basepath . '/common/global.php');

require '../common/database.php';
require '../common/Excel.php';
require '../func/func_string.php';
require '../func/func_global.php';
require '../func/func_form.php';


$userlevel = $set->userInfo['level'];
if($userlevel == "affiliate"){
	
	$globalWhere = $_POST['globalWhere'];

	$globalWhereSales = str_replace('affiliate_id', 'tb1.affiliate_id', $globalWhere);
	$globalWhereSales2 = str_replace('t.affiliate_id','ds.affiliate_id', $globalWhere) ;
	$globalWhereReg = str_replace('t.affiliate_id','dg.affiliate_id', $globalWhere) ;
	
}
else{
	$globalWhere = "";
	$globalWhereSales = "";
	$globalWhereSales2 = "";
	$globalWhereReg = "";
}

$xls = new Excel('Report');


$sql = $_POST['sql'];

if($_POST['format'] == 'xlsx'){
	$sql .= " limit 0,65536";
}


$clickqq = mysql_query($sql);
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
				 $clickArray[$clickww['id']]['merchant_name'] = $clickww['merchant_name'];
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
				 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
				 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
				  $where_reg =  str_replace('banner_id','dg.banner_id', $where_reg) ;
				  
				  
				  
				  
				  
				  
				  
				  
				  if ($clickww['uid']>0) {
				$sql = "SELECT dg.* FROM data_reg dg"
							." WHERE " . $where_reg . $globalWhereReg
							." and dg.uid > 0"
							." and dg.merchant_id > 0"
							. " and dg.rdate >= '" . $clickww['rdate'] . "'"
							. " AND dg.uid = " . $clickww['uid'];
				// echo $sql;
				$regqq = mysql_query($sql);
				
				$arrTierCplCountCommissionParams = [];
					// die ($sql);
				$regArray = array();
				while ($regww = mysql_fetch_assoc($regqq)) {
					
					//if(!empty($regww['trader_id'])){
						$tranrow['id'] = $regww['id'];
						$tranrow['rdate'] = $regww['rdate'];
						$tranrow['affiliate_id'] = $regww['affiliate_id'];
						$tranrow['trader_id'] = $regww['trader_id'];
						$tranrow['merchant_id'] = $regww['merchant_id'];
						$regArray[] = array($tranrow);

						$clickArray[$clickww['id']]['reg_date'] = $regww['reg_date'];
						$clickArray[$clickww['id']]['trader_id'] = $regww['trader_id'];
						$clickArray[$clickww['id']]['trader_name'] = $regww['trader_alias'];
						
						$clickArray[$clickww['id']]['sale_status'] = $regww['saleStatus'];
						
						$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
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
									'merchant_id'  => $regww['merchant_id'],
									'affiliate_id' => $regww['affiliate_id'],
									'rdate'        => $regww['rdate'],
									'banner_id'    => $regww['banner_id'],
									'trader_id'    => $regww['trader_id'],
									'profile_id'   => $regww['profile_id'],
								];
								
								$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
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
											'merchant_id'  => $regww['merchant_id'],
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
						$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id),0,0,0,$trader_id);
						foreach ($arrFtds as $arrFtd) {
								$real_ftd++;
								$clickArray[$clickww['id']]['real_ftd'] += 1;
								
								$real_ftd_amount = $arrFtd['amount'];
								$clickArray[$clickww['id']]['real_ftd_amount'] += $real_ftd_amount;
								
								$beforeNewFTD = $ftd;
								getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
							
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
								 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
								 . "WHERE  tb1.trader_id = " .  $trader_id
							//	 . ' and tb1.rdate between "' . $from . '" AND "' . $to . '"' 
								. " and tb1.rdate >= '" . $regDate . "'"
								. " and tb1.merchant_id >0 "
								. (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
								 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
								 . (isset($banner_id) && !empty($banner_id) ? ' AND data_reg.banner_id = "'.$banner_id.'"' :'') 
								 .(!empty($unique_id) ? ' and data_reg.uid = ' . $unique_id :'') . $globalWhereSales;
					
					$salesqq = mysql_query($sql);
								
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
									'merchant_id'  => $salesww['merchant_id'],
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
						
						
							//REVENUE   						// loop on merchants    								// loop on affiliates
							// start of data_stats (revenue) loop
							$merchantww = 	getMerchants($salesww['merchant_id'],0);
							
							if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {
								
								$netRevenue =  round(getRevenue("data_sales.rdate  BETWEEN . '".$from."' AND '".$to."' ",$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$salesww['data_sales_type'] == "withdrawal"?$withdrawal:0,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
								//$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
								//echo $salesww['id'] . "----" . $netRevenue . "<br/>";
									
								$clickArray[$clickww['id']]['netRevenue'] += $netRevenue;
								
								if($netRevenue<>0){
									
									$row                 = array();
									$row['merchant_id']  = $merchantww['id'];
									$row['affiliate_id'] = $salesww['affiliate_id'];
									$row['banner_id']    = 0;
									$row['rdate']        = $regDate;
									$row['amount']       = $netRevenue;
									$row['isFTD']        = false;
									$row['trader_id']        = $trader_id;
									  
									$totalCom           = getCommission($from, $to, 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									$clickArray[$clickww['id']]['totalCom'] += $totalCom;
									
								}
							} 
							// end of data_stats (revenue) loop
						
							// end of data_sales loop
					}
					
					$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype FROM data_stats ds  "
								. " INNER JOIN merchants m where ds.rdate >= '" . $regDate . "'"
								 . ' AND (m.producttype = "casino" or m.producttype ="sportsbetting") and m.valid=1'
								 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
								 . " and ds.trader_id=" . $trader_id
								 . " and ds.merchant_id>0 " . $globalWhereSales2;
								 
					$revqq  = mysql_query($sql); 					 
					while ($revww = mysql_fetch_assoc($revqq)) {
								$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
								$intTotalRevenue  = 0;
								
								foreach ($arrRevenueRanges as $arrRange2) {
									$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
												 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
												 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
									
									$intCurrentRevenue = getRevenue($strRevWhere, $revww['producttype']);
									$intTotalRevenue    += $intCurrentRevenue;
									$row                 = array();
									$row['merchant_id']  = $revww['merchant_id'];
									$row['affiliate_id'] = $revww['affiliate_id'];
									$row['banner_id']    = 0;
									$row['rdate']        = $arrRange2['from'];
									$row['amount']       = $intCurrentRevenue;
									$row['isFTD']        = false;
								  
									$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									
									$clickArray[$clickww['id']]['totalCom'] += $totalCom;
									
									unset($arrRange2, $strRevWhere);
								}
								
								$netRevenue = $intTotalRevenue;
								$clickArray[$clickww['id']]['netRevenue'] += $netRevenue;
								
								
					}
					
						
					$sql = "select * from merchants where producttype = 'Forex' and valid =1";
					$totalqq = mysql_query($sql);
					
					while ($merchantww  = mysql_fetch_assoc($totalqq)) {
							$sql = 'SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO FROM data_stats ds '
									. 'WHERE ds.rdate >= "'.$regDate.'"' . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
									. ' and ds.trader_id=' . $trader_id
									. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
									. " and ds.merchant_id = " . $clickww['merchant_id']
									. " and ds.merchant_id >0 "
									 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
									  .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'');
						
							$traderStatsQ = mysql_query($sql);
							
							while($ts = mysql_fetch_assoc($traderStatsQ)){
									$spreadAmount = $ts['totalSpread'];
									$volume += $ts['totalTO'];
									
									$clickArray[$clickww['id']]['volume'] += $ts['totalTO'];
									
									$pnl = $ts['totalPnl'];
							}
									
									
							$totalLots  = 0;
														
						
								
							$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds '
							 . 'WHERE  ds.rdate >= "' . $regDate . '"'
							 . ' and ds.trader_id=' . $trader_id
							 . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
							 . (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
							 . " and ds.merchant_id >0 "
							 . " and ds.merchant_id = " . $clickww['merchant_id']
							 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
							   .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'');
							
							$traderStatsQ = mysql_query($sql);
							$earliestTimeForLot = date('Y-m-d');
							while($ts = mysql_fetch_assoc($traderStatsQ)){
								
								if($ts['affiliate_id']==null) {
										continue;
								}
				
								// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
										$totalLots  = $ts['totalTurnOver'];
										// echo $totalLots
											$tradersProccessedForLots[$clickww['merchant_id'] . '-' . $ts['trader_id']] = $clickww['id'] . '-' . $ts['trader_id'];
											$lotdate = $ts['rdate'];
											$ex = explode(' ' , $lotdate);
											$lotdate = $ex[0];
												if ($earliestTimeForLot>$lotdate)
												$earliestTimeForLot = $lotdate;
											if($totalLots != 0){
												$row = [
															'merchant_id'  => $clickww['merchant_id'],
															'affiliate_id' => $ts['affiliate_id'],
															'rdate'        => $earliestTimeForLot,
															'banner_id'    => $ts['banner_id'],
															'trader_id'    => $ts['trader_id'],
															'profile_id'   => $ts['profile_id'],
															'type'       => 'lots',
															'amount'       =>  $totalLots,
												];
												
											$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
											//echo 'com: ' . $a .'<br>';
											$totalCom = $a;
											$clickArray[$clickww['id']]['totalCom'] += $totalCom;
										}
								// }
								
							}
						}
						}						
					}
				 }
				 // trader id empty loop end
				 } // if uid >0
			 }//uid 0 loop end	
			 } 
		
		//}
		ob_start();
		
		//header
		$xls->home();
		 $xls->label(lang('ID'));
		  $xls->right();
		  $xls->label(lang('ID'));
		  $xls->right();
		  $xls->label(lang('Impression'));
		  $xls->right();
		  $xls->label(lang('Click'));
		  $xls->right();
		  $xls->label(lang('Affiliate ID'));
		  $xls->right();
		  $xls->label(lang('Affiliate Username'));
		  $xls->right();
		  $xls->label(lang('Date'));
		  $xls->right();
		  $xls->label(lang('Type'));
		  $xls->right();
		  $xls->label(lang('Merchant'));
		  $xls->right();
		  $xls->label(lang('Banner ID'));
		  $xls->right();
		  $xls->label(lang('Profile ID'));
		  $xls->right();
		  $xls->label(lang('Param'));
		  $xls->right();
		  $xls->label(lang('Param2'));
		  $xls->right();
		  $xls->label(lang('Refer URL'));
		  $xls->right();
		  $xls->label(lang('Country'));
		  $xls->right();
		  $xls->label(lang('IP'));
		  $xls->right();
		  $xls->label(lang('Platform'));
		  $xls->right();
		  $xls->label(lang('Operating System'));
		  $xls->right();
		  $xls->label(lang('OS Version'));
		  $xls->right();
		  $xls->label(lang('Browser'));
		  $xls->right();
		  $xls->label(lang('Browser Version'));
		  $xls->right();
		  $xls->label(lang('Trader ID'));
		  $xls->right();
		  $xls->label(lang('Trader Alias'));
		  $xls->right();
		  $xls->label(lang('Lead'));
		  $xls->right();
		  $xls->label(lang('Demo'));
		  $xls->right();
		  $xls->label(lang('Sale Status'));
		  $xls->right();
		  $xls->label(lang('Accounts'));
		  $xls->right();
		  $xls->label(lang('FTD'));
		  $xls->right();
		  $xls->label(lang('FTD Amount'));
		  $xls->right();
		  $xls->label(lang('Total FTD'));
		  $xls->right();
		  $xls->label(lang('Total FTD Amount'));
		  $xls->right();
		  $xls->label(lang('Total Deposits'));
		  $xls->right();
		  $xls->label(lang('Deposits Amount'));
		  $xls->right();
		  $xls->label(lang('Volume'));
		  $xls->right();
		  $xls->label(lang('Bonus Amount'));
		  $xls->right();
		  $xls->label(lang('Withdrawal Amount'));
		  $xls->right();
		  $xls->label(lang('ChargeBack Amount'));
		  $xls->right();
		  $xls->label(lang('Net Deposits'));
		  $xls->right();
		  $xls->label(lang('Commission'));
		  $xls->down();
		
		
		foreach($clickArray as $data){
		//	if($l == 65536) break;
			$refer_url = $data['refer_url'];
		    if(strlen($data['refer_url'])>50)
				$refer_url = substr($data['refer_url'],0,49). "...";
			
			$country_name = $allCountriesArray[$data['country'] ];
			if(strtolower($country)=='any'){
				$country_name = "";
			}
		/* 	$listReport .= '
			<tr>
				<td style="text-align: left;">'.$data['traffic_id'].'</td>
				<td style="text-align: left;">'.$data['uid'] .'</td>
				<td style="text-align: center;">'.@number_format($data['views'],0).'</td>
				<td style="text-align: center;">'.@number_format($data['clicks'],0).'</td>
				<td style="text-align: left;"><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_id'] .'</a></td>
				 <td style="text-align: left;"><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_username'] .'</a></td>
				<td style="text-align: left;">'.$data['traffic_date'] .'</td>
				<td style="text-align: left;">'.ucwords($data['type']).'</td>
				<td style="text-align: left;">'. $data['merchant_name'] .'</td>
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
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=lead">'.$data['leads'].'</a></td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=demo">'.$data['demo'].'</a></td>
				<td style="text-align: left;">'. $data['sale_status'] .'</td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=real">'.$data['real'].'</a></td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=ftd&trader_id='. $data['trader_id'] .'">'.$data['ftd'].'</a></td>
				<td>'.price_new($data['ftd_amount']).'</td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=totalftd&trader_id='. $data['trader_id'] .'">'.$data['real_ftd'].'</a></td>
				<td>'.price_new($data['real_ftd_amount']).'</td>
				<td><a href="/'. $userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>
				<td>'.price_new($data['sumDeposits']).'</td>
				<td style="text-align: center;">'.price_new($data['volume']).'</td>
				<td>'.price_new($data['bonus']).'</td>
				<td>'.price_new($data['withdrawal']).'</td>
				<td>'.price_new($data['chargeback']).'</td>
				<td style="text-align: center;">'.price_new($data['netRevenue']).'</td>
				<td>'.price_new($data['totalCom']).'</td>
			</tr>'; */
			
		  $xls->home();
		  $xls->label($data['traffic_id']);
		  $xls->right();
		  $xls->label($data['uid']);
		  $xls->right();
		  $xls->label(@number_format($data['views'],0));
		  $xls->right();
		  $xls->label(@number_format($data['clicks'],0));
		  $xls->right();
		  $xls->label($data['affiliate_id']);
		  $xls->right();
		  $xls->label($data['affiliate_username']);
		  $xls->right();
		  $xls->label($data['traffic_date']);
		  $xls->right();
		  $xls->label(ucwords($data['type']));
		  $xls->right();
		  $xls->label($data['merchant_name']);
		  $xls->right();
		  $xls->label($data['banner_id']);
		  $xls->right();
		  $xls->label($data['profile_id']);
		  $xls->right();
		  $xls->label($data['param']);
		  $xls->right();
		  $xls->label($data['param2']);
		  $xls->right();
		  $xls->label($refer_url);
		  $xls->right();
		  $xls->label($country_name);
		  $xls->right();
		  $xls->label($data['ip']);
		  $xls->right();
		  $xls->label(ucwords($data['platform']));
		  $xls->right();
		  $xls->label($data['os'] );
		  $xls->right();
		  $xls->label($data['osVersion'] );
		  $xls->right();
		  $xls->label($data['browser']);
		  $xls->right();
		  $xls->label($data['browserVersion'] );
		  $xls->right();
		  $xls->label($data['trader_id'] );
		  $xls->right();
		  $xls->label($data['trader_name']);
		  $xls->right();
		  $xls->label($data['leads']);
		  $xls->right();
		  $xls->label($data['demo']);
		  $xls->right();
		  $xls->label($data['sale_status'] );
		  $xls->right();
		  $xls->label($data['real']);
		  $xls->right();
		  $xls->label($data['ftd']);
		  $xls->right();
		  $xls->label(price_new($data['ftd_amount']));
		  $xls->right();
		  $xls->label($data['real_ftd']);
		  $xls->right();
		  $xls->label(price_new($data['real_ftd_amount']));
		  $xls->right();
		  $xls->label($data['depositingAccounts']);
		  $xls->right();
		  $xls->label(price_new($data['sumDeposits']));
		  $xls->right();
		  $xls->label(price_new($data['volume']));
		  $xls->right();
		  $xls->label(price_new($data['bonus']));
		  $xls->right();
		  $xls->label(price_new($data['withdrawal']));
		  $xls->right();
		  $xls->label(price_new($data['chargeback']));
		  $xls->right();
		  $xls->label(price_new($data['netRevenue']));
		  $xls->right();
		  $xls->label(price_new($data['totalCom']));
		  $xls->down();
			$l++;
		}
      
		/* $tableStr='<table width="2400" class="tablesorter" border="0" cellpadding="0" cellspacing="0" id="clicksAjaxTbl">
					<thead><tr>
						<th style="text-align: left;">'.lang('ID').'</th>
						<th style="text-align: left;">'.lang('UID').'</th>
						
						<th style="text-align: center;">'.lang('Impression').'</th>
						<th style="text-align: center;">'.lang('Click').'</th>
						
						<th style="text-align: left;">'.lang('Affiliate ID').'</th>
						<th style="text-align: left;">'.lang('Affiliate Username').'</th>
						<th>'.lang('Date').'</th>
						<th>'.lang('Type').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
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
						<th style="text-align: left;">'.lang('Trader Alias').'</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
						<th>'.lang('Sale Status').'</th>
						<th>'.lang(ptitle('Accounts')).'</th>
						<th>'.lang('FTD').'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total FTD').'</th>
						<th>'.lang('Total FTD Amount').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.lang('Deposits Amount').'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang('Commission').'</th>
					</tr></thead><!--<tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						<th style="text-align: center;">'.$totalImpressions.'</th>
						<th style="text-align: center;">'.$totalClicks.'</th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th></th>
						<th><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price_new($totalFTDAmount).'</th>
						<th><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price_new($totalRealFtdAmount).'</th>
						<th><a href="/'.$userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price_new($totalDepositAmount).'</th>
						<th>'.price_new($totalVolume).'</th>
						<th style="text-align: left;">'.price_new($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price_new($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price_new($totalChargeBackAmount).'</th>
						<th>'.price_new($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price_new($totalComs).'</th>
					</tr></tfoot>-->
					<tbody>
					'.$listReport.'
				</table>
				'; */
				
				
				

				$xls->send();
				$test = ob_get_clean();
			
				if($_POST['format'] == 'csv'){
					file_put_contents(__DIR__ .'/'. $_POST['filename'].'.csv', $test);
					$file = __DIR__ .'/'. $_POST['filename'].'.csv';
				}
				else{
					file_put_contents(__DIR__ .'/'. $_POST['filename'].'.xls', $test);
					$file = __DIR__ .'/'. $_POST['filename'].'.xls';
				}
				
				if($_POST['format'] == 'xlsx'){
					if($l>=65536)
					{
						echo json_encode(array('file'=>$file,'status'=>'big'));exit;
					}
					else{
						echo json_encode(array('file'=>$file,'status'=>'ok'));exit;
					}
				}
				else{
					echo json_encode(array('file'=>$file,'status'=>'ok'));exit;
				}				
			
				
function price_new($price=0) {
$num = @number_format($price,2);
return $set->currency .' '.$num;
}			
?>