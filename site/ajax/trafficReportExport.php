<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

$l = 1;
ini_set('memory_limit', '1024M');
//$basepath = $_SERVER['DOCUMENT_ROOT'];
$where = $_POST['where'];

//require_once($basepath . '/common/global.php');

	require '../common/database.php';
	require '../common/Excel.php';
	require '../func/func_string.php';
	require '../func/func_global.php';
	require '../func/func_form.php';

$xls = new Excel('Report');
	
$fromDate = strtotime($_POST['from']);
$toDate = strtotime($_POST['to']);
$display_type = $_POST['display_type']	;		
$merchant_id = empty($_POST['merchant_id'])?"":$_POST['merchant_id'];
$affiliate_id = empty($_POST['affiliate_id'])?"":$_POST['affiliate_id'];
$group_id = empty($_POST['group_id'])?"":$_POST['group_id'];
$banner_id = empty($_POST['banner_id'])?"":$_POST['banner_id'];
$unique_id = empty($_POST['unique_id'])?"":$_POST['unique_id'];
$uid = empty($_POST['uid'])?"":$_POST['uid'];

			if ($display_type AND $display_type != "monthly") $diff = ($toDate - $fromDate) / 86400; // get the number of days between
				else $diff = 0;
			
			if($display_type == "weekly") {
				$theFromDay = date("w",strtotime($from))+1;
				$theToDay = date("w",strtotime($to))+1;
				$daysUntilEndWeek = (7-$theFromDay);
				$endOfWeek = date("Y-m-d",strtotime($from." +".$daysUntilEndWeek." Day"));
				
				$diff = round($diff/8);
				if($theFromDay > $theToDay) $diff = $diff+1;
				}
			
			if ($display_type == "monthly") {
				$endOfthisMonth = date("t",strtotime($from));
				if($endOfthisMonth<10) $endOfthisMonth = "0".$endOfthisMonth;
				$lastDayOfMonth = date("Y-m-".$endOfthisMonth, strtotime($from));
				$date1 = strtotime($from);
				$date2 = strtotime($to);
				$months = 0;
				while (($date1 = strtotime('+1 MONTH', $date1)) <= $date2) $diff++;
				if($diff == 0) $lastDayOfMonth = $to;
				}
			
			//for ($i=0;$i<=$diff;$i++) 
			//{
				
				// From Show on weekly
				if($newFrom) $showFrom = $newFrom; else $showFrom = $from;
				
				$searchInSql = "";
				if ($display_type == "daily") {
					$searchInSql = "= '".date("Y-m-d", strtotime($from." +".$i." day"))."'";
				} else if ($display_type == "weekly"){
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$endOfWeek."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "weekly"){ 
					if($i==0){ // Weekly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".date("Y-m-d", strtotime())."'"; // First Loop - first week
						$newFrom = date("Y-m-d", strtotime($endOfWeek. "+1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$newTo = date("Y-m-d", strtotime($newFrom. "+6 Day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					}
				} else if ($display_type == "monthly"){ 
					if($i==0){ // Monthly First Loop
						$searchInSql = "BETWEEN '".$from."' AND '".$lastDayOfMonth."'";
						$newFrom = date("Y-m-d", strtotime($lastDayOfMonth." +1 Day"));
					} else if ($i == $diff) { // Last Loop - Last week
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$to."'"; 
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} else { // Else Loops
						$numOfDaysThisMonth = date("t",strtotime($newFrom))-1;
						$newTo = date("Y-m-d", strtotime($newFrom. "+".$numOfDaysThisMonth." day"));
						$searchInSql = "BETWEEN '".$newFrom."' AND '".$newTo."'";
						$newFrom = date("Y-m-d", strtotime($newTo. "+1 Day"));
					} 
				} else { // If no Display_type 
					$searchInSql = "BETWEEN '".$from." 00:00:00' AND '".$to."'";
				}
				
				// To Show on weekly
				if($display_type == "weekly")  if($newTo) $showTo = $newTo; else $showTo = $endOfWeek;
				// To Show on monthly
				if($display_type == "monthly") if($newTo) $showTo = $newTo; else $showTo = $lastDayOfMonth;
				
				if($i==$diff) $showTo = $to;
				
				$l++;
				
				$merchantName = strtolower($ww['name']);
				$merchantID = $ww['id'];
				
				
				
				$filterFrom = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showFrom));
				$filterTo = ($display_type == "daily" ? strtotime($from.' +'.$i.' Day') : strtotime($showTo));
				
		
		
		
		$refferelArray = array();	
		$listGroups = affiliateGroupsArray();
		if($userlevel == "admin")
			$is_admin = isAdmin();
		else
			$is_admin = 0;
				

		
				$qry = $_POST['sql'];
				$listReport="";		
				$resc = mysql_query($qry);
				$i = 0;
				$totalVisits = 0;
				while ($arrRes = mysql_fetch_assoc($resc)) {
					
				if($arrRes['uid'] !=0){
				
				$country='';
				$countryArry = getIPCountry_new($arrRes['ip']);
				if ($countryArry['countryLONG']=='')
					$country = lang('Unknown');
				else
					$country = $countryArry['countryLONG'];
				
				
				$refferelArray[$arrRes['refer_url']]['refer_url'] = $arrRes['refer_url'];
				$refferelArray[$arrRes['refer_url']]['id'] = $arrRes['id'];
				$refferelArray[$arrRes['refer_url']]['merchant_id'] = $arrRes['merchant_id'];
				$refferelArray[$arrRes['refer_url']]['merchant_name'] = $arrRes['merchant_name'];
				$refferelArray[$arrRes['refer_url']]['ip'] = $arrRes['ip'];
				$refferelArray[$arrRes['refer_url']]['country'] = $arrRes['country'];
				$refferelArray[$arrRes['refer_url']]['group'] = $listGroups[$arrRes['group_id']];
				$refferelArray[$arrRes['refer_url']]['views'] = $arrRes['total_visits'];
				$refferelArray[$arrRes['refer_url']]['clicks'] = $arrRes['total_clicks'];
				$refferelArray[$arrRes['refer_url']]['affiliate_id'] = $arrRes['affiliate_id'];
				$refferelArray[$arrRes['refer_url']]['username'] = $arrRes['username'];
				$refferelArray[$arrRes['refer_url']]['profile_id'] = $arrRes['profile_id'];
				$refferelArray[$arrRes['refer_url']]['uid'] = $arrRes['uid'];
			
			
		  if(!empty($refferelArray)){           
		  
				$ftd=0;
				$ftd_amount=0;
				$totalCom=0;
				$bonus=0;
				$withdrawal=0;
				$chargeback=0;
				$volume=0;
				$totalLeads=0;
				$sumDeposits=0;
				$depositingAccounts=0;
				$totalDemo=0;
				$totalReal=0;
				
				// registration (leads + demo + real)
				$where = "1=1";
				if ($merchant_id) $where .= " AND dg.merchant_id='".$merchant_id."'";
				if ($affiliate_id) $where .= " AND dg.affiliate_id='".$affiliate_id."'";
				if ($uid) $where .= " AND dg.uid='".$uid."'";
				  
				$sql = "SELECT dg.* FROM data_reg dg"
							." WHERE " . $where
							. " AND dg.uid = " . $arrRes['uid']
							. " AND dg.uid>0 "
							. " AND dg.merchant_id>0 "
							. "  AND dg.rdate >= '" . $arrRes['rdate'] . "'";
			
				$regqq = function_mysql_query($sql,__FILE__);
				$arrTierCplCountCommissionParams = [];
					// die ($sql);
				$regArray = array();
				while ($regww = mysql_fetch_assoc($regqq)) {
					
					if(!empty($regww['trader_id'])){
						$tranrow['id'] = $regww['id'];
						$tranrow['rdate'] = $regww['rdate'];
						$tranrow['affiliate_id'] = $regww['affiliate_id'];
						$tranrow['trader_id'] = $regww['trader_id'];
						$tranrow['merchant_id'] = $regww['merchant_id'];
						$regArray[] = array($tranrow);

						$refferelArray[$arrRes['refer_url']]['reg_date'] = $regww['reg_date'];
						$refferelArray[$arrRes['refer_url']]['trader_id'] = $regww['trader_id'];
						$refferelArray[$arrRes['refer_url']]['trader_name'] = $regww['trader_alias'];
						
						$refferelArray[$arrRes['refer_url']]['sale_status'] = $regww['saleStatus'];
						
						$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
						$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
						
						if ($regww['type'] == "lead"){
							//$totalLeads++;
								$refferelArray[$arrRes['refer_url']]['leads'] += 1;
						}
						if ($regww['type'] == "demo"){
								$refferelArray[$arrRes['refer_url']]['demo'] += 1;
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
								$refferelArray[$arrRes['refer_url']]['total_com'] += $totalCom;
								
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
							$refferelArray[$arrRes['refer_url']]['real'] += 1;
							
						}
					}
				}
				
				 if(isset($refferelArray[$arrRes['refer_url']]['trader_id'])){
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
						$refferelArray[$arrRes['refer_url']]['totalCom'] += 1;
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
								$refferelArray[$arrRes['refer_url']]['real_ftd'] += 1;
								
								$real_ftd_amount = $arrFtd['amount'];
								$refferelArray[$arrRes['refer_url']]['real_ftd_amount'] += $real_ftd_amount;
								
								$beforeNewFTD = $ftd;
								getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
							
								if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
									$arrFtd['isFTD'] = true;
									$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
									
									$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
								}
								$refferelArray[$arrRes['refer_url']]['ftd'] = $ftd;
										
								$refferelArray[$arrRes['refer_url']]['ftd_amount'] = $ftd_amount;
								unset($arrFtd);
						
						}
					
			
			
				
					//Sales
					$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
								 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
								 . "WHERE  tb1.merchant_id>0 and  tb1.trader_id = " .  $trader_id
								. (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
								 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
								 . (isset($banner_id) && !empty($banner_id) ? ' AND data_reg.banner_id = "'.$banner_id.'"' :'') 
								 .(!empty($unique_id) ? ' and data_reg.uid = ' . $unique_id :'')
								 . ' and tb1.rdate >= "' . $regDate . '"';
			
					$salesqq = function_mysql_query($sql,__FILE__);
			
					while ($salesww = mysql_fetch_assoc($salesqq)) {
							
							if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
								$depositingAccounts++;
								$refferelArray[$arrRes['refer_url']]['depositingAccounts'] += 1;
								
								$sumDeposits = $salesww['amount'];
								$refferelArray[$arrRes['refer_url']]['sumDeposits'] += $salesww['amount'];
								
								// $depositsAmount+=$salesww['amount'];
							}
							
							if ($salesww['data_sales_type'] == "bonus") {
									$bonus = $salesww['amount'];
									$refferelArray[$arrRes['refer_url']]['bonus'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == "withdrawal"){ 
									$withdrawal = $salesww['amount'];
									$refferelArray[$arrRes['refer_url']]['withdrawal'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == "chargeback"){
									$chargeback = $salesww['amount'];
									$refferelArray[$arrRes['refer_url']]['chargeback'] += $salesww['amount'];
							}
							if ($salesww['data_sales_type'] == 'volume') {
							
								$volume = $salesww['amount'];
								$refferelArray[$arrRes['refer_url']]['volume'] += $salesww['amount'];
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

								$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
							}
						
						
							//REVENUE   						// loop on merchants    								// loop on affiliates
							// start of data_stats (revenue) loop
							
							$merchantww = 	getMerchants($salesww['merchant_id'],0);
							$merchantww = (isset($merchantww[0])?$merchantww[0]:$merchantww);
							if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {
								// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
								$netRevenue =  round(getRevenue("data_sales.rdate  BETWEEN . '".$from."' AND '".$to."' ",$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$salesww['data_sales_type'] == "withdrawal"?$withdrawal:0,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
								//$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$sumDeposits,$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
								//echo $salesww['id'] . "----" . $netRevenue . "<br/>";
									
								$refferelArray[$arrRes['refer_url']]['netRevenue'] += $netRevenue;
								
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
									$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
								}
							} 
							// end of data_stats (revenue) loop
						
							// end of data_sales loop
					}
					//die;
				//echo "<pre>";print_r($refferelArray[$arrRes['refer_url']]);
					
					$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype FROM data_stats ds  "
								. " INNER JOIN merchants m where ds.merchant_id > 0 AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" 
								 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
								 . " and ds.trader_id=" . $trader_id 
								 . " and  ds.rdate >= '" . $regDate . "'";
					
					$revqq  = function_mysql_query($sql,__FILE__); 					 
					while ($revww = mysql_fetch_assoc($revqq)) {
								$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
								$intTotalRevenue  = 0;
								
								foreach ($arrRevenueRanges as $arrRange2) {
									$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
												 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
												 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'')
												 . (!empty($uid) ? ' and uid = ' . $uid :'');
									
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
									
									$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
									
									unset($arrRange2, $strRevWhere);
								}
								
								$netRevenue = $intTotalRevenue;
								$refferelArray[$arrRes['refer_url']]['netRevenue'] += $netRevenue;
								
								
					}
					
						
					$sql = "select * from merchants where producttype = 'Forex' and valid =1";
					$totalqq = function_mysql_query($sql,__FILE__);
					
					while ($merchantww  = mysql_fetch_assoc($totalqq)) {
							$sql = "SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO FROM data_stats ds "
									. "WHERE 1=1 " . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
									. ' and ds.trader_id=' . $trader_id
									. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
									. (!empty($uid) ? ' and ds.uid = ' . $uid :'')
									. " and ds.merchant_id> 0 and ds.merchant_id = " . $arrRes['merchant_id']
									 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
									  .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'')
									  ." ds.rdate >= '".$regDate."'";
							
							$traderStatsQ = function_mysql_query($sql,__FILE__);
							
							while($ts = mysql_fetch_assoc($traderStatsQ)){
									$spreadAmount = $ts['totalSpread'];
									$volume += $ts['totalTO'];
									
									$refferelArray[$arrRes['refer_url']]['volume'] += $ts['totalTO'];
									
									$pnl = $ts['totalPnl'];
							}
									
									
							$totalLots  = 0;
														
						
								
							$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds '
							 . "WHERE  1=1  and ds.trader_id=" . $trader_id
							 . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
							 . (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
							 . (!empty($uid) ? ' and ds.uid = ' . $uid :'')
							 . " and ds.merchant_id > 0 and ds.merchant_id = " . $arrRes['merchant_id']
							 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
							   .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'')
							   ." ds.rdate >= '" . $regDate . "'";
							
							$traderStatsQ = function_mysql_query($sql,__FILE__);
							$earliestTimeForLot = date('Y-m-d');
							while($ts = mysql_fetch_assoc($traderStatsQ)){
								
								if($ts['affiliate_id']==null) {
										continue;
								}
				
								// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
										$totalLots  = $ts['totalTurnOver'];
										// echo $totalLots
											$tradersProccessedForLots[$arrRes['merchant_id'] . '-' . $ts['trader_id']] = $arrRes['id'] . '-' . $ts['trader_id'];
											$lotdate = $ts['rdate'];
											$ex = explode(' ' , $lotdate);
											$lotdate = $ex[0];
												if ($earliestTimeForLot>$lotdate)
												$earliestTimeForLot = $lotdate;
											if($totalLots <> 0 ){
												$row = [
															'merchant_id'  => $arrRes['merchant_id'],
															'affiliate_id' => $ts['affiliate_id'],
															'rdate'        => $earliestTimeForLot,
															'banner_id'    => $ts['banner_id'],
															'trader_id'    => $ts['trader_id'],
															'profile_id'   => $ts['profile_id'],
															'type'       => 'lots',
															'amount'       =>  $totalLots,
												];
											}
										$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
										//echo 'com: ' . $a .'<br>';
										$totalCom = $a;
										$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
								// }
								
							}
						}
						}						
					}// trader id empty loop end
				 }
				}//uid 0 loop end	 
			 }
			
			
			ob_start();
				
		//header
		$xls->home();
		 $xls->label(lang('Refer URL'));
		  $xls->right();
		  $xls->label(lang('Merchant ID'));
		  $xls->right();
		  $xls->label(lang('Merchant'));
		  $xls->right();
		  $xls->label(lang('Last Click IP'));
		  $xls->right();
		  $xls->label(lang('Last Click Country'));
		  $xls->right();
		  $xls->label(lang('Affiliate ID'));
		  $xls->right();
		  $xls->label(lang('Affiliate Username'));
		  $xls->right();
		  $xls->label(lang('Profile ID'));
		  $xls->right();
		  $xls->label(lang('Group ID'));
		  $xls->right();
		  $xls->label(lang('All Time Clicks'));
		  $xls->right();
		  $xls->label(lang('All Time Views'));
		  $xls->right();
		  $xls->label(lang('Lead'));
		  $xls->right();
		  $xls->label(lang('Demo'));
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
		

	
		foreach($refferelArray as $key=>$data){
				
					$xls->home();
		 $xls->label($data['refer_url']);
		  $xls->right();
		  $xls->label($data['merchant_id']);
		  $xls->right();
		  $xls->label($data['merchant_name']);
		  $xls->right();
		  $xls->label($data['ip']);
		  $xls->right();
		  $xls->label($data['country']);
		  $xls->right();
		  $xls->label($data['affiliate_id']);
		  $xls->right();
		  $xls->label($data['username']);
		  $xls->right();
		  $xls->label($data['profile_id']);
		  $xls->right();
		  $xls->label($data['group']);
		  $xls->right();
		  $xls->label($data['clicks']);
		  $xls->right();
		  $xls->label($data['views']);
		  $xls->right();
		  $xls->label($data['leads']);
		  $xls->right();
		  $xls->label($data['demo']);
		  $xls->right();
		  $xls->label($data['real']);
		  $xls->right();
		  $xls->label($data['ftd']);
		  $xls->right();
		  $xls->label($data['ftd_amount']);
		  $xls->right();
		  $xls->label($data['real_ftd']);
		  $xls->right();
		  $xls->label(price_new($data['real_ftd_amount']));
		  $xls->right();
		  $xls->label(price_new($data['depositingAccounts']));
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
		
		if($l>=65536)
		{
			echo json_encode(array('file'=>$file,'status'=>'big'));exit;
		}
		else{
			echo json_encode(array('file'=>$file,'status'=>'ok'));exit;
		}
		
		
		

function getIPCountry_new($ip=-1) {
//die ('$IPaddr' . $ip);

	global $_SERVER;
	if ($ip==-1)
	$IPaddr = getClientIp();
	else
	$IPaddr = $ip;
	if ($IPaddr == "") return false;
		else {
			$ips = explode(".", $IPaddr);
			$ipno = ($ips[3] + $ips[2] * 256 + $ips[1] * 256 * 256 + $ips[0] * 256 * 256 * 256);
			}
	$qy = "SELECT * FROM ip2country WHERE 4=4 and ipFROM <= '".$ipno."' AND ipTo >= '".$ipno."' LIMIT 1";
	//die ($qy);
	$sql = mysql_query($qy);
	$ww=mysql_fetch_assoc($sql);
	return $ww;
	}

	function price_new($price=0) {
		$num = @number_format($price,2);
		return $set->currency .' '.$num;
	}			
?>