<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$globalWhere = " and t.affiliate_id = " . $set->userInfo['id'];

$pageTitle = lang('Referral Report');
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
		
		$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "Referral_data_" . date('YmdHis');
		
		$sql = "select id,name from affiliates_profiles where valid =1";
		$qqProfiles = function_mysql_query($sql);
		$listProfiles = array();
		while($wwProfiles = mysql_fetch_assoc($qqProfiles)){
			$listProfiles[$wwProfiles['id']] = $wwProfiles['name'];
		}
		
		
		if($userlevel == 'manager')
			 $group_id = $set->userInfo['group_id'];
		
		
		$page = (isset($page) || !empty($page))?$page:1;
		$set->page = $page;
		
		$start_limit = $page==1?0:$set->rowsNumberAfterSearch * ($page -1);
		$end_limit = $set->rowsNumberAfterSearch;// * $page;
		
		if ($merchant_id) $where = " AND tb1.merchant_id='".$merchant_id."'";
		if ($profile_id) $whereSites = " AND tb1.profile_id='".$profile_id."'";
		// if ($banner_id) $whereSites .= " AND tb1.banner_id='".$banner_id."'";
		if ($uid) $whereSites .= " AND tb1.uid='".$uid."'";
		if($userlevel == 'admin')
			if ($group_id && isAdmin()) $whereSites .= " AND af.group_id='".$group_id."'";
		else
			if ($group_id) $whereSites .= " AND af.group_id='".$group_id."'";
	
		$affiliate_id = $set->userInfo['id'];
	
	/* $sql = "SELECT * FROM merchants WHERE valid='1' ".$where." ORDER BY pos";
		$qq=function_mysql_query($sql,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) 
		 */
		
			
			$formula = $ww['rev_formula'];
			
			$fromDate = strtotime($from);
			$toDate = strtotime($to);
			
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
		
		
					if(isset($filter) && !empty($filter)){
						/* $qry = ("SELECT sum(clicks) as total_clicks, sum(views) as total_visits, af.group_id as group_id ,tb1.*,mr.name as merchant_name,af.username FROM traffic tb1 
									inner join affiliates af on tb1.affiliate_id = af.id 
									inner join merchants mr on mr.id = tb1.merchant_id 
									WHERE tb1.type='traffic'  and tb1.refer_url !='' and tb1.uid>0 and 1=1  ".$whereSites." AND tb1.rdate ".$searchInSql." group by tb1.refer_url order by tb1.id desc limit " . $start_limit. ", " . $end_limit); */
						if($filter == "active_account")
						{
							$where = str_replace("tb1","t",$whereSites);
							$globalAffWhere = str_replace("t.affiliate_id","tb1.affiliate_id",$globalWhere);
							$sql = "SELECT count(*) as total_records from (	select dg.id FROM  `data_reg` dg
							INNER JOIN traffic t ON t.uid = dg.uid
							INNER JOIN affiliates af ON dg.affiliate_id = af.id
							INNER JOIN merchants mr ON mr.id = dg.merchant_id
							WHERE dg.merchant_id > 0 and 1=1 AND dg.uid >0
							".$where. $globalAffWhere ."
							AND t.rdate ".$searchInSql."
							AND t.refer_url !=  ''
							GROUP BY t.refer_url,t.uid) t";
		
							$totalRec = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
							
							$total_records = $totalRec['total_records'];
							$set->total_records = $total_records; 
							
							
							$qry = "
							SELECT SUM( t.clicks ) , SUM( t.views ) , t.*, af.group_id AS group_id, mr.name AS merchant_name, af.username
							FROM  `data_reg` dg
							INNER JOIN traffic t ON t.uid = dg.uid
							INNER JOIN affiliates af ON dg.affiliate_id = af.id
							INNER JOIN merchants mr ON mr.id = dg.merchant_id
							WHERE 1=1 AND dg.uid >0
							".$where. $globalAffWhere ."
							AND t.rdate ".$searchInSql."
							and dg.merchant_id > 0 
							AND t.refer_url !=  ''
							GROUP BY t.refer_url,t.uid
							ORDER BY t.id desc";
							
							$ajaxSql = $qry;
							
							$qry .= " limit " . $start_limit. ", " . $end_limit;
						}	
						else if($filter == "real_account"){
							$where = str_replace("tb1","t",$whereSites);
							$globalAffWhere = str_replace("t.affiliate_id","tb1.affiliate_id",$globalWhere);
							 $sql = "SELECT count(*) as total_records from (	select dg.id FROM  `data_reg` dg
							INNER JOIN traffic t ON t.uid = dg.uid
							INNER JOIN affiliates af ON dg.affiliate_id = af.id
							INNER JOIN merchants mr ON mr.id = dg.merchant_id
							WHERE 1=1 AND dg.uid >0
							and dg.merchant_id > 0 
							AND dg.type =  'real'
							".$where. $globalAffWhere ."
							AND t.rdate ".$searchInSql."
							AND t.refer_url !=  ''
							GROUP BY t.refer_url,t.uid) t";
		
							$totalRec = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
							
							$total_records = $totalRec['total_records'];
							$set->total_records = $total_records; 
		
		
							
							$qry = "
							SELECT SUM( t.clicks ) , SUM( t.views ) , t.*, af.group_id AS group_id, mr.name AS merchant_name, af.username
							FROM  `data_reg` dg
							INNER JOIN traffic t ON t.uid = dg.uid
							INNER JOIN affiliates af ON dg.affiliate_id = af.id
							INNER JOIN merchants mr ON mr.id = dg.merchant_id
							WHERE 1=1 AND dg.uid >0
							and dg.merchant_id > 0 
							AND dg.type =  'real'
							".$where.  $globalAffWhere ."
							AND t.rdate ".$searchInSql."
							AND t.refer_url !=  ''
							GROUP BY t.refer_url,t.uid
							ORDER BY t.id desc";
							
							$ajaxSql = $qry;
							
							$qry .= " limit " . $start_limit. ", " . $end_limit;
							
						}
						else if($filter == "ftd"){
							
								$where = str_replace("tb1","t",$whereSites);
							$globalAffWhere = str_replace("t.affiliate_id","tb1.affiliate_id",$globalWhere);
								 $sql = "SELECT count(*) as total_records from (	select dg.id FROM  `data_reg` dg
								INNER JOIN traffic t ON t.uid = dg.uid
								INNER JOIN affiliates af ON dg.affiliate_id = af.id
								INNER JOIN merchants mr ON mr.id = dg.merchant_id
								WHERE 1=1 AND dg.uid >0
								and dg.merchant_id > 0 
								AND dg.ftdamount > 0
								".$where.  $globalAffWhere ."
								AND t.rdate ".$searchInSql."
								AND t.refer_url !=  ''
								GROUP BY t.refer_url,t.uid) t";
			
								$totalRec = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
								
								$total_records = $totalRec['total_records'];
								$set->total_records = $total_records; 
								
								$qry = "
								SELECT SUM( t.clicks ) , SUM( t.views ) , t.*, af.group_id AS group_id, mr.name AS merchant_name, af.username
								FROM  `data_reg` dg
								INNER JOIN traffic t ON t.uid = dg.uid
								INNER JOIN affiliates af ON dg.affiliate_id = af.id
								INNER JOIN merchants mr ON mr.id = dg.merchant_id
								WHERE 1=1 AND dg.uid >0
								and dg.merchant_id > 0 
								AND dg.ftdamount > 0
								".$where. $globalAffWhere ."
								AND t.rdate >= '". $from ."'
								AND t.refer_url !=  ''
								GROUP BY t.refer_url
								ORDER BY t.id desc";
								$ajaxSql = $qry;
								$qry .=" limit " . $start_limit. ", " . $end_limit;
								
						}
						else{
							//case all
							
						}
					}
					else{
						$globalAffWhere = str_replace("t.affiliate_id","tb1.affiliate_id",$globalWhere);
						$qry = "SELECT sum(clicks) as total_clicks, sum(views) as total_visits, af.group_id as group_id ,tb1.*,mr.name as merchant_name,af.username FROM traffic tb1 
									inner join affiliates af on tb1.affiliate_id = af.id 
									inner join merchants mr on mr.id = tb1.merchant_id 
									WHERE tb1.merchant_id > 0 and tb1.type='traffic'  and tb1.refer_url !='' and tb1.uid>0 and 1=1  ".$whereSites. $globalAffWhere ." AND tb1.rdate ".$searchInSql." group by tb1.refer_url order by tb1.id desc";

						$ajaxSql = $qry;
						
						$qry .= " limit " . $start_limit. ", " . $end_limit;
					}
				
				$listReport="";		
				$resc = function_mysql_query($qry,__FILE__);
				$i = 0;
				$totalVisits = 0;
				while ($arrRes = mysql_fetch_assoc($resc)) {
				if($arrRes['uid'] !=0){
				
				$country='';
				$countryArry = getIPCountry($arrRes['ip']);
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
			// var_dump($country);
		
				
				
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
				if ($uid) $where .= " AND dg.uid='".$uid."'";
				
				$globalAffWhere = str_replace("t.affiliate_id","dg.affiliate_id",$globalWhere);
				$sql = "SELECT dg.* FROM data_reg dg"
							." WHERE " . $where . $globalAffWhere
							. " AND dg.uid = " . $arrRes['uid']
							. " AND dg.uid>0 "
							. " AND dg.merchant_id>0 "
							. "  AND dg.rdate >= '" . $arrRes['rdate'] . "'";
				//echo $sql;die;
			
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
						$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id),0,0,0,$trader_id   , "" , false , 1);
						
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
					$globalAffWhere = str_replace("t.affiliate_id","tb1.affiliate_id",$globalWhere);
					$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
								 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
								 . "WHERE  tb1.merchant_id>0 and  tb1.trader_id = " .  $trader_id
								 . ' and tb1.rdate >= "' . $regDate . '"' 
								. (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
								 . (isset($banner_id) && !empty($banner_id) ? ' AND data_reg.banner_id = "'.$banner_id.'"' :'') 
								 .(!empty($unique_id) ? ' and data_reg.uid = ' . $unique_id :'') . $globalAffWhere;
					
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
							if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino' || true) {
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
									$row['initialftddate']        = $salesww['initialftddate'];
									  
									$totalCom           = getCommission($from, $to, 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									$refferelArray[$arrRes['refer_url']]['totalCom'] += $totalCom;
								}
							} 
							// end of data_stats (revenue) loop
						
							// end of data_sales loop
					}
					//die;
				//echo "<pre>";print_r($refferelArray[$arrRes['refer_url']]);
					$globalAffWhere = str_replace("t.affiliate_id","ds.affiliate_id",$globalWhere);
					$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype FROM data_stats ds  "
								. " INNER JOIN merchants m where ds.merchant_id > 0 and  ds.rdate >= '" . $regDate 
								 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" 
								 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
								 . " and ds.trader_id=" . $trader_id . $globalAffWhere;
								 
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
					$globalAffWhere = str_replace("t.affiliate_id","ds.affiliate_id",$globalWhere);
					while ($merchantww  = mysql_fetch_assoc($totalqq)) {
							$sql = "SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO FROM data_stats ds "
									. "WHERE ds.rdate >= '".$regDate."'"  . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
									. ' and ds.trader_id=' . $trader_id
									. (!empty($uid) ? ' and ds.uid = ' . $uid :'')
									. " and ds.merchant_id> 0 and ds.merchant_id = " . $arrRes['merchant_id']
									 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
									  .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'') . $globalAffWhere;
							
							$traderStatsQ = function_mysql_query($sql,__FILE__);
							
							while($ts = mysql_fetch_assoc($traderStatsQ)){
									$spreadAmount = $ts['totalSpread'];
									$volume += $ts['totalTO'];
									
									$refferelArray[$arrRes['refer_url']]['volume'] += $ts['totalTO'];
									
									$pnl = $ts['totalPnl'];
							}
									
									
							$totalLots  = 0;
														
						
							$globalAffWhere = str_replace("t.affiliate_id","ds.affiliate_id",$globalWhere);
							$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds '
							 . "WHERE  ds.rdate >= '" . $regDate . "'  and ds.trader_id=" . $trader_id
							 . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
							 . (!empty($uid) ? ' and ds.uid = ' . $uid :'')
							 . " and ds.merchant_id > 0 and ds.merchant_id = " . $arrRes['merchant_id']
							 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
							   .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'') . $globalAffWhere;
							
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
				 
				 
				  if ($set->deal_pnl == 1 && allowView('af-pnl',$deal,'fields')  ) {
						
								$totalPNL  = 0;
								
									$dealsForAffiliate = getExistingDealTypesAllMerchantsForAffiliateArray($set->userInfo['id'],$arrDealTypeDefaults);
									
									
									$pnlRecordArray=array();
									
									$pnlRecordArray['affiliate_id']  = $set->userInfo['id'];
									$pnlRecordArray['merchant_id']  =  $arrRes['merchant_id'];
									$pnlRecordArray['trader_id']  =  $arrRes['trader_id'];
									$pnlRecordArray['group_id']  = $group_id;
									$pnlRecordArray['searchInSql']  = $searchInSql;
									$pnlRecordArray['fromdate']  = $from;
									$pnlRecordArray['todate']  = $to;
									
									
									if ($dealsForAffiliate['pnl']>0){
										$sql = generatePNLquery($pnlRecordArray,false);
									}
									else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
										$sql = generatePNLquery($pnlRecordArray,true);
									}
								
								
								
								$traderStatsQ = function_mysql_query($sql,__FILE__);
								while($ts = mysql_fetch_assoc($traderStatsQ)){
												$pnlamount = ($ts['amount']*-1);
												$row = [
													'merchant_id'  => $ts['merchant_id'],
													'affiliate_id' => $ts['affiliate_id'],
													'rdate'        => $ts['rdate'],
													'banner_id'    => $ts['banner_id'],
													'trader_id'    => $ts['trader_id'],
													'profile_id'   => $ts['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  ($showCasinoFields==1 ?  calculateCasinoRevenue($pnlamount,$ts['type']) : $pnlamount) ,
												 'initialftddate'       =>  $ts['initialftddate']
												 ];

												 
												 $refferelArray[$arrRes['refer_url']]['pnl']+= $pnlamount;
											
										if ($dealsForAffiliate['pnl']>0){
											$tmpCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											$refferelArray[$arrRes['refer_url']]['totalCom']+= $tmpCom;
										}
								}
						}
				 
				}//uid 0 loop end	 
			 }
			$l=0;
				foreach($refferelArray as $key=>$data){
					$refer_url = $data['refer_url'];
					if(strlen($data['refer_url'])>50)
						$refer_url = substr($data['refer_url'],0,49). "...";
					$listReport .= '
                        <tr>
                            <td><a href="'.$data['refer_url'].'" target="_blank">'.$refer_url.'</a></td>
                            <td>'.$data['merchant_id'].'</td>
                            <td>'.$data['merchant_name'].'</td>
                            <td>'.$data['ip'].'</td>
                            <td>'.$data['country'].'</td>
                            <td>'.$data['affiliate_id'].'</td>
							<td><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['username'].'</a></td>
                            <td>'.$data['profile_id'].'</td>
                            <td>'.$listProfiles[$data['profile_id']].'</td>
                            ' . ($is_admin ? '<td>'.$data['group'].'</td>' : '' ) . '
                            
							'.(allowView('af-impr',$deal,'fields') ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'">'.$data['clicks'].'</a></td>
							' : '').
							(allowView('af-clck',$deal,'fields') ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'">'.$data['views'].'</td>
							' : '').
							
					(!$hideDemoAndLeads 
					?
					(allowView('af-lead',$deal,'fields') ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=lead&country_id='.$data['country'].'">'.$data['leads'].'</a></td>
						' : '').
						(allowView('af-demo',$deal,'fields') ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=demo&country_id='.$data['country'].'">'.$data['demo'].'</a></td>
						' : '')
						: '') . 
						(allowView('af-real',$deal,'fields') ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=real&country_id='.$data['country'].'">'.$data['real'].'</a></td>
							': '' ) .
							(allowView('af-ftd',$deal,'fields') ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=ftd&country_id='.$data['country'].'">'.$data['ftd'].'</a></td>
							' : '' ) .
							(allowView('af-ftda',$deal,'fields') ? '
							<td>'.price($data['ftd_amount']).'</td>
							' : '')
							.	(allowView('af-tftd',$deal,'fields') ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'type=totalftd&country_id='.$data['country'].'">'.$data['real_ftd'].'</a></td>
							' : '')
							.	(allowView('af-tftda',$deal,'fields') ? '
							<td>'.price($data['real_ftd_amount']).'</td>
							' : '')	
							. (allowView('af-depo',$deal,'fields') && (true) ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=deposit&country_id='.$data['country'].'">'.$data['depositingAccounts'].'</a></td>
							' : '' )
							. (allowView('af-depoam',$deal,'fields') && (true) ? '
							<td>'.price($data['sumDeposits']).'</td>
							':'')
							. (allowView('af-vlm',$deal,'fields')  ? '						
							<td style="text-align: center;">'.price($data['volume']).'</td>
							':'')	
							. (allowView('af-bns',$deal,'fields')  ? '
							<td>'.price($data['bonus']).'</td>
							':'')
							. (allowView('af-withd',$deal,'fields')  ? '
							<td>'.price($data['withdrawal']).'</td>
							':'')
							. (allowView('af-chrgb',$deal,'fields')  ? '
							<td>'.price($data['chargeback']).'</td>
							':'')
							. (allowView('af-ntrv',$deal,'fields')  ? '
							<td style="text-align: center;">'.price($data['netRevenue']).'</td>
							':'')
							.
							($set->deal_pnl==1 ? '
							<td style="text-align: center;">'.price($data['pnl']).'</td>':'').'
							<td>'.price($data['totalCom']).'</td>';
							
							$i++;
							
							$totalViews +=$data['views'];
							$totalClicks +=$data['clicks'];
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
							$totalPNL += $data['pnl'];
							$l++;
				}
		//}	
		$set->sortTable = 1;

		//if ($l > 0) $set->sortTableScript = 1;
		$set->totalRows = $l;
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="traffic" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					'.($is_admin ? '<td>'.lang('Group').'</td>' : '').'
					<td>'.lang('Merchant').'</td>
					<!--td>'.lang('Search Type').'</td-->
					
					<!--td>'.lang('Affiliate ID').'</td-->
					<td>'.lang('UID').'</td>
					<td>'.lang('Filter').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					
					
					'.($is_admin ? '
					<td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                    </td>' : '').'
												
					
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<!--td><select name="display_type" style="width: 150px;"><option value="0">'.lang('Search Type').'</option>'.listDisplayTypes($display_type).'</select></td-->
					<!--td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" style="width: 80px;" /></td-->
					<td><input type="text" name="uid" value="'.$uid.'" style="width: 80px;" /></td>
					<td><select name="filter">
						<option value="">'. lang("All") .'</option>
						<option value="active_account" '. ($filter=='active_account'?'selected':'') .'>'. lang("Active Accounts") .'</option>
						<option value="real_account" '. ($filter=='real_account'?'selected':'') .'>'. lang("Real Accounts") .'</option>
						<option value="ftd" '. ($filter=='ftd'?'selected':'') .'>'. lang("FTDs") .'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="javascript:void(0);" class="testcsv"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a href="javascript:void(0);" class="testexcel"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			
			</div>
			
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2200px;">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
			//width 2200
				$tableStr='<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="trafficTbl">
					<thead><tr  class="table-row">
					
						
						<th  class="table-cell">'.lang('Refer URL').'</th>
						<th  class="table-cell"style="text-align: center;">'.lang('Merchant ID').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Merchant').'</th>
						<th class="table-cell">'.lang('Last Click IP').'</th>
						<th class="table-cell">'.lang('Last Click Country').'</th>
						<th class="table-cell">'.lang('Affiliate ID').'</th>
						<th class="table-cell header">'.lang('Affiliate Username').'</th>
						<th class="table-cell">'.lang('Profile ID').'</th>
						<th class="table-cell">'.lang('Profile Name').'</th>
						'. ($is_admin ? '<th class="table-cell">'.lang('Group ID').'</th>' : ''). '
						'. (allowView('af-impr',$deal,'fields') ? '
						<th class="table-cell" style="text-align: center;">'.lang('All Time Clicks').'</th>
						' : '' ) .'
						'. (allowView('af-clck',$deal,'fields') ? '			
						<th class="table-cell" style="text-align: center;">'.lang('All Time Views').'</th>
						' : '' ) .'
						'. (!$hideDemoAndLeads ? 
						(allowView('af-lead',$deal,'fields') ? 					
						'<th class="table-cell">'.lang(ptitle('Lead')).'</th>
						' : '').
						(allowView('af-demo',$deal,'fields') ? '					
						<th class="table-cell">'.lang(ptitle('Demo')).'</th>
						' : '' ): '') .
						(allowView('af-real',$deal,'fields') ? '
						<th class="table-cell">'.lang(ptitle('Accounts')).'</th>
						' : '' ) .
						(allowView('af-ftd',$deal,'fields') ? '
						<th class="table-cell">'.lang('FTD').'</th>
						' : '')
						.(allowView('af-ftda',$deal,'fields') ?'
						<th class="table-cell">'.lang('FTD Amount').'</th>
						' : '') 
						.(allowView('af-tftd',$deal,'fields') ? 
						'<th class="table-cell">'.lang('RAW FTD').'</th>
						' : '').
						(allowView('af-tftda',$deal,'fields') ? '
						<th class="table-cell">'.lang('RAW FTD Amount').'</th>
						' : '').
						( (allowView('af-depo',$deal,'fields')) ? '
						<th class="table-cell">'.lang('Total Deposits').'</th>
						':'').
						( (allowView('af-depoam',$deal,'fields')) ? '
						<th class="table-cell">'.lang('Deposit Amount').'</th>
						': '').
						(allowView('af-vlm',$deal,'fields') ?  '<th class="table-cell">'.lang('Volume').'</th>' : '').
						(allowView('af-bns',$deal,'fields') ?  '<th class="table-cell">'.lang('Bonus Amount').'</th>' : '').
						(allowView('af-withd',$deal,'fields') ?  '<th class="table-cell">'.lang('Withdrawal Amount').'</th>' : '').
						(allowView('af-chrgb',$deal,'fields') ?  '<th class="table-cell">'.lang('ChargeBack Amount').'</th>' : '').
						(allowView('af-ntrv',$deal,'fields') ?  '<th class="table-cell">'.lang(ptitle('Net Deposit')).'</th>' : '').
						($set->deal_pnl==1 ? '
						<th class="table-cell">'.lang(ptitle('PNL')).'</th> ' : '').'
						<th class="table-cell">'.lang('Commission').'</th>
					</tr></thead><!--<tfoot><tr>
					
						
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						'. ($is_admin ? '<th></th>' : ''). '
						<th></th>
						<th></th>
						'. (allowView('af-impr',$deal,'fields') ? '			
						<th>'.($totalClicks).'</th>
						' : '' ) .'
						'. (allowView('af-clck',$deal,'fields') ? '			
						<th>'.($totalViews).'</th>
						' : '' ) .
						(!$hideDemoAndLeads ? 
						(allowView('af-lead',$deal,'fields') ? 					
						'
						<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						': '').
						(allowView('af-demo',$deal,'fields') ? '
						<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						 ': '' )
						 : '' )
						 .(allowView('af-real',$deal,'fields') ? '
						<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						' : '' ).
						(allowView('af-ftd',$deal,'fields') ? '
						<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						' : '' ).
						(allowView('af-ftda',$deal,'fields') ?'
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						' : '') .
						(allowView('af-tftd',$deal,'fields') ? '
							<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						' : '').	
						(allowView('af-tftda',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						' : '').
						( (allowView('af-depo',$deal,'fields')) ? '
						<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						':'').
						( (allowView('af-depoam',$deal,'fields')) ? '
							<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						': '').
						(allowView('af-vlm',$deal,'fields') ?  '<th>'.price($totalVolume).'</th>' : '').
						(allowView('af-bns',$deal,'fields') ?  '<th>'.price($totalBonusAmount).'</th>' : '').
						(allowView('af-withd',$deal,'fields') ?  '<th>'.price($totalWithdrawalAmount).'</th>' : '').
						(allowView('af-chrgb',$deal,'fields') ?  '<th>'.price($totalChargeBackAmount).'</th>' : '').
						(allowView('af-ntrv',$deal,'fields') ?  '<th>'.price($totalNetRevenue).'</th>' : '').
						'<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>-->
					<tbody>
					'.$listReport.'
				</table>
				<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
				<script>
					$(document).ready(function(){
						
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
											level = "affiliate";
											type = "add";
											
											saveReportToMyFav(name, \'traffic\',user,level,type);
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
						
				$(".testcsv").on("click",function(){
						$.ajax({
							  method: "POST",
							  url: "'.$set->SSLprefix.'ajax/clicksReportExport.php",
							  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'"}
							})
							  .done(function( msg ) {
								$("body").append("<div style=\'display:none\'>"+ msg +"</div>");
									$("#clicksAjaxTbl").tableExport({type:"csvbig",escape:"false",tableName:"'.  $filename .'"});
							  });
					});
					
					$(".testexcel").on("click",function(){
						$.ajax({
							  method: "POST",
							  url: "'.$set->SSLprefix.'ajax/clicksReportExport.php",
							  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'"}
							})
							  .done(function( msg ) {
								$("body").append("<div style=\'display:none\'>"+ msg +"</div>");
									$("#clicksAjaxTbl").tableExport({type:"excelbig",escape:"false",tableName:"'.  $filename .'"});
							  });
					});
					});
					</script>
				';
			
		excelExporter($tableStr,'Traffic');
		
		
		$set->content.=$tableStr.'</div>'.getURLPager();
		
		
			//MODAL
		$myReport = lang("Referral");
		include "common/ReportFieldsModal.php";
		
		
		theme();

?>