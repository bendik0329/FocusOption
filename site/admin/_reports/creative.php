<?php

//Prevent direct browsing of report
if(!defined('DirectBrowse')) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/admin" );
}


$set->pageTitle = lang('Creative Report');
		
		$creativeArray = [];
		
                $where = ' 1 = 1 ';
            
                /**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
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
              

		
		$merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
		foreach ($merchantsAr as $arrMerchant) {
						
				if (strtolower($arrMerchant['producttype'])=='forex')
						$displayForex = 1;
					$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}

		//general merchants information
		$merchantww = getMerchants($merchant_id ? $merchant_id:0,1);
		
		$formula = $merchantww['rev_formula'];
		$merchantID = $merchantww['id'];
		$merchantName = strtolower($merchantww['name']);
		
		//Banners
		 $where_main = $where;
		 $where_main =  str_replace('affiliate_id','mc.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','mc.merchant_id', $where_main) ;
		 $where_main =  str_replace('profile_id','mc.profile_id', $where_main) ;
		$sql = "SELECT mc.*,m.name as merchant_name,l.title as language FROM merchants_creative mc left join languages l on l.id = mc.language_id INNER JOIN merchants m on mc.merchant_id = m.id where " 
		. str_replace('banner_id=','mc.id=', $where) . " and mc.valid=1 ";
		
	// die($sql);	
		$bannersqq = function_mysql_query($sql,__FILE__);
		while ($bannersww = mysql_fetch_assoc($bannersqq)) {		
					
					if ($type && $bannersww['type'] != $type) {
                        continue;
                    }
					
					$creativeArray[$bannersww['id']]['banner_id'] = $bannersww['id'];
				    $creativeArray[$bannersww['id']]['banner_title'] = $bannersww['title'];
					$creativeArray[$bannersww['id']]['type'] = $bannersww['type'];
					$creativeArray[$bannersww['id']]['merchant'] = $bannersww['merchant_name'];
					$creativeArray[$bannersww['id']]['language'] = $bannersww['language'];
					$creativeArray[$bannersww['id']]['width'] = $bannersww['width'];
					$creativeArray[$bannersww['id']]['height'] = $bannersww['height'];
					$creativeArray[$bannersww['id']]['merchant_id'] = $bannersww['merchant_id'];
		}
	
		// clicks and impressions
		// $arrClicksAndImpressions = getClicksAndImpressions($from, $to, $merchant_id, $affiliate_id, $group_id,$banner_id);
		
		$sql = "select banner_id, SUM(clicks) as total_clicks,sum(views) as total_views from traffic where " . $where . " AND rdate BETWEEN '" . $from . "' AND '" . $to . "' GROUP BY banner_id";
		
		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
				if(!isset($creativeArray[$trafficRow['banner_id']])){
						continue;
				}
				$creativeArray[$trafficRow['banner_id']]['clicks'] = $trafficRow['total_clicks'];
				$creativeArray[$trafficRow['banner_id']]['views'] = $trafficRow['total_views'];
		}
		/* while ($trafficRow = mysql_fetch_assoc($qq)) {
					if(!isset($creativeArray[$trafficRow['banner_id']])){
						continue;
					}
					 if (!isset($creativeArray[$trafficRow['banner_id']]))  {
							$creativeArray[$trafficRow['banner_id']]['clicks'] = $trafficRow['clicks'];
							$creativeArray[$trafficRow['banner_id']]['views'] = $trafficRow['views'];
					}
					else{
							$creativeArray[$trafficRow['banner_id']]['clicks'] = $creativeArray[$trafficRow['banner_id']]['clicks'] + $trafficRow['clicks'];
							$creativeArray[$trafficRow['banner_id']]['views'] = $creativeArray[$trafficRow['banner_id']]['views'] + $trafficRow['views'];
					}
		}		 */
		
		
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
		
		
		// registration (leads + demo + real)
		$where_reg = $where;
		$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
		 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
		 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
		 
		$sql = "SELECT dg.*,m.name as merchant_name FROM data_reg dg"
					." INNER JOIN merchants m on m.id = dg.merchant_id "
					."WHERE " . $where . " AND  dg.rdate BETWEEN '" . $from . "' AND '" . $to . "'" 
					. (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
		
		$regqq = function_mysql_query($sql,__FILE__);
		
		$arrTierCplCountCommissionParams = [];
			// die ($sql);
		while ($regww = mysql_fetch_assoc($regqq)) {
			if($regww['banner_id'] == ""  || $regww['banner_id'] == 0 || !isset($creativeArray[$regww['banner_id']])){
				continue;
			}
			
			$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
			$boolTierCplCount = !is_null($strAffDealType) && 'cpl_count' == $strAffDealType;
			
			if ($regww['type'] == "lead"){
				//$totalLeads++;
					$creativeArray[$regww['banner_id']]['leads'] += 1;
			}
			if ($regww['type'] == "demo"){
					$creativeArray[$regww['banner_id']]['demo'] += 1;
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
					
					// var_dump($totalCom);
					// die();
					
					$creativeArray[$regww['banner_id']]['totalCom'] += $totalCom;
					 
					
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
				$creativeArray[$regww['banner_id']]['real'] += 1;
			}
		}
		
			
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
			$creativeArray[$arrParams['arrTmp']['banner_id']]['totalCom'] += $totalCom;
			unset($intAffId, $arrParams);
		}
		
		
		
		$arrFtds  = getTotalFtds($from, $to, (!is_null($affiliate_id)?$affiliate_id:0), (!is_null($merchant_id)?$merchant_id:0), 0, (is_null($group_id) ? 0 : $group_id));
		// var_dump($arrFtds);
		// die();
		

		foreach ($arrFtds as $arrFtd) {
				
								
					
				$isFilteredBanner_id=true;
				 if (!empty($banner_id) && isset($banner_id)){
					
				
						 if ($banner_id==$arrFtd['banner_id']) {
							 $isFilteredBanner_id = true;
							 
						 }
						 else {
							 $isFilteredBanner_id = false;
						 }
						
				}
				 
				if  ($arrFtd['banner_id'] == ""  || $arrFtd['banner_id'] == 0 || !isset($creativeArray[$arrFtd['banner_id']]) ) {
				$isFilteredBanner_id = false;
				}
				 
				if(!$isFilteredBanner_id ){
					continue;
				}
		


		// $real_ftd++;
				$creativeArray[$arrFtd['banner_id']]['real_ftd'] += 1;
			
				$real_ftd_amount = $arrFtd['amount'];
				$creativeArray[$arrFtd['banner_id']]['real_ftd_amount'] += $real_ftd_amount;
				
				$beforeNewFTD = $ftd;
				getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
			
				if ($beforeNewFTD != $ftd) {
				// die ('gergerge');	
					$ftd_amount = $real_ftd_amount;
					$arrFtd['isFTD'] = true;
					$totalCom = getCommission($from, $to, 0, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
					
					$creativeArray[$arrFtd['banner_id']]['totalCom'] += $totalCom;
				$creativeArray[$arrFtd['banner_id']]['ftd'] += 1;
				$creativeArray[$arrFtd['banner_id']]['ftd_amount'] += $ftd_amount;
				
				
		// var_dump($creativeArray);
		// die();
				}
				unset($arrFtd);
		}
	
		
		//Sales
	/* 	$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
					 . "WHERE  tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($banner_id) && !empty($banner_id) ? ' AND tb1.banner_id = "'.$banner_id.'"' :''); */
			$sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 ." INNER JOIN merchants_creative mc on mc.id= tb1.banner_id "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
					 . "WHERE  mc.valid=1 and tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (is_null($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($banner_id) && !empty($banner_id) ? ' AND tb1.banner_id = "'.$banner_id.'"' :'');
		
		$salesqq = function_mysql_query($sql,__FILE__);
	
	    while ($salesww = mysql_fetch_assoc($salesqq)) {
				
				/* if($salesww['banner_id'] == ""  || $salesww['banner_id'] == 0){					
					continue;
				} */
				
				$isFilteredBanner_id=true;
				 if (!empty($banner_id) && isset($banner_id)){
					
				
						 if ($banner_id==$salesww['banner_id']) {
							 $isFilteredBanner_id = true;
							 
						 }
						 else {
							 $isFilteredBanner_id = false;
						 }
						
				}
				 
				if  ($salesww['banner_id'] == ""  || $salesww['banner_id'] == 0 || !isset($creativeArray[$salesww['banner_id']]) ) {
				$isFilteredBanner_id = false;
				}
				 
				if(!$isFilteredBanner_id ){
					continue;
				}
				
				if ($salesww['data_sales_type'] == 1 || $salesww['data_sales_type'] == 'deposit') {   // NEW.
					//$depositingAccounts++;
					$creativeArray[$salesww['banner_id']]['depositingAccounts'] += 1;
					
					$sumDeposits = $salesww['amount'];
					$creativeArray[$salesww['banner_id']]['sumDeposits'] += $salesww['amount'];
					
					// $depositsAmount+=$salesww['amount'];
				}
				
				if ($salesww['data_sales_type'] == "bonus") {
						$bonus = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['bonus'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == "withdrawal"){ 
						$withdrawal = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['withdrawal'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == "chargeback"){
						$chargeback = $salesww['amount'];
						$creativeArray[$salesww['banner_id']]['chargeback'] += $salesww['amount'];
				}
				if ($salesww['data_sales_type'] == 'volume') {
					$volume = $salesww['amount'];
					$creativeArray[$salesww['banner_id']]['volume'] += $salesww['amount'];
					
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

					$creativeArray[$salesww['banner_id']]['totalCom'] += $totalCom;
				}
				
				
				//REVENUE   						// loop on merchants    								// loop on affiliates
				// start of data_stats (revenue) loop
			$merchantww = 	getMerchants($salesww['merchant_id'],0);
				
					if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino') {

						// $netRevenue = round($depositsAmount - ($withdrawal + $bonus + $chargeback), 2);
				
						$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$salesww['data_sales_type'] == 'deposit'?$salesww['amount']:0,$salesww['data_sales_type'] == "bonus"?$bonus:0,$salesww['data_sales_type'] == "withdrawal"?$withdrawal:0,0,0,0,$merchantww['rev_formula'],null,$salesww['data_sales_type'] == "chargeback"?$chargeback:0),2);
					//	$netRevenue =  round(getRevenue($where,$merchantww['producttype'],$salesww['amount'],$bonus,$withdrawal,0,0,0,$merchantww['rev_formula'],null,$chargeback),2);
						 // echo  (':: ' . $netRevenue);
						$creativeArray[$salesww['banner_id']]['netRevenue'] += $netRevenue;
						if($netRevenue <> 0 ){
						$row                 = array();
						$row['merchant_id']  = $merchantww['id'];
						$row['affiliate_id'] = $salesww['affiliate_id'];
						$row['banner_id']    = 0;
						$row['rdate']        = $from;
						$row['amount']       = $netRevenue;
						$row['isFTD']        = false;
									  
						$totalCom           = getCommission($from, $to, 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									  
						$creativeArray[$salesww['banner_id']]['totalCom'] += $totalCom;
						}
				}
				// end of data_stats (revenue) loop

				// end of data_sales loop
		}
		
	
		$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype, dg.country as country FROM data_stats ds INNER JOIN data_reg dg ON dg.trader_id = ds.trader_id INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
					 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" . (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
		
		$revqq  = function_mysql_query($sql,__FILE__); 					 
		while ($revww = mysql_fetch_assoc($revqq)) {
					$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
					$intTotalRevenue  = 0;
					
					foreach ($arrRevenueRanges as $arrRange2) {
						$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
									 . '"' . (is_null($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
									 . (!empty($affiliate_id) ? ' and affiliate_id = ' . $affiliate_id :'');
						
						$intCurrentRevenue = getRevenue($strRevWhere, $revww['producttype']);
						
						$intTotalRevenue   += $intCurrentRevenue;
						$row                 = array();
						$row['merchant_id']  = $revww['merchant_id'];
						$row['affiliate_id'] = $revww['affiliate_id'];
						$row['banner_id']    = 0;
						$row['rdate']        = $arrRange2['from'];
						$row['amount']       = $intCurrentRevenue;
						$row['isFTD']        = false;
					  
						$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
						
						$creativeArray[$revww['banner_id']]['totalCom'] += $totalCom;
						
						unset($arrRange2, $strRevWhere);
					}
					
					$netRevenue = $intTotalRevenue;
					$creativeArray[$revww['banner_id']]['netRevenue'] = $netRevenue;
					
		}
					
		
					
		$sql = "select * from merchants where producttype = 'Forex' and valid =1";
		$totalqq = function_mysql_query($sql,__FILE__);
		while ($merchantww  = mysql_fetch_assoc($totalqq)) {
				$sql = 'SELECT SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO, dg.banner_id as banner_id FROM data_stats ds '
						. ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
						. 'WHERE ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (is_null($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
						. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
						. " and ds.merchant_id = " . $merchantww['id']
						. (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'')
						. " group by dg.banner_id";

				$traderStatsQ = function_mysql_query($sql,__FILE__);
				
				while($ts = mysql_fetch_assoc($traderStatsQ)){
					
						$isFilteredBanner_id=true;
						 if (!empty($banner_id) && isset($banner_id)){
							
						
								 if ($banner_id==$ts['banner_id']) {
									 $isFilteredBanner_id = true;
									 
								 }
								 else {
									 $isFilteredBanner_id = false;
								 }
								
						}
						 
						if  ($ts['banner_id'] == ""  || $ts['banner_id'] == 0 || !isset($creativeArray[$ts['banner_id']]) ) {
						$isFilteredBanner_id = false;
						}
						 
						if(!$isFilteredBanner_id ){
							continue;
						}
			
						$spreadAmount = $ts['totalSpread'];
						$volume += $ts['totalTO'];
						
						$creativeArray[$ts['banner_id']]['volume'] += $ts['totalTO'];
						
						$pnl = $ts['totalPnl'];
				}
						
				$totalLots  = 0;
											
							
							
				$sql = 'SELECT ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id,dg.country as country FROM data_stats ds '
				 . ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
				 . 'WHERE  ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
					. (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
					. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
					. " and ds.merchant_id = " . $merchantww['id']
						 . (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'');
				
   
				$traderStatsQ = function_mysql_query($sql,__FILE__);
				$earliestTimeForLot = date('Y-m-d');
				while($toww = mysql_fetch_assoc($traderStatsQ)){
					
					if($toww['affiliate_id']==null) {
							continue;
					}
	
					// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
							$totalLots  = $toww['totalTurnOver'];
							// echo $totalLots
								$tradersProccessedForLots[$merchantww['id'] . '-' . $toww['trader_id']] = $trafficRow['id'] . '-' . $toww['trader_id'];
								$lotdate = $toww['rdate'];
								$ex = explode(' ' , $lotdate);
								$lotdate = $ex[0];
									if ($earliestTimeForLot>$lotdate)
									$earliestTimeForLot = $lotdate;
								
								$row = [
											'merchant_id'  => $merchantww['id'],
											'affiliate_id' => $toww['affiliate_id'],
											'rdate'        => $earliestTimeForLot,
											'banner_id'    => $toww['banner_id'],
											'trader_id'    => $toww['trader_id'],
											'profile_id'   => $toww['profile_id'],
											'type'       => 'lots',
											'amount'       =>  $totalLots,
								];
							$a = getCommission($from, $toww, 0, $group_id, $arrDealTypeDefaults, $row);
							// echo 'com: ' . $a .'<br>';
							$totalCom = $a;
							$creativeArray[$toww['banner_id']]['totalCom'] += $totalCom;
					// }
				}
		 }		
		// die;		
		
		foreach($creativeArray as $data){
			
			if ($data['views']>0 || $data['clicks']>0 || $data['leads'] >0 || $data['demo'] >0 || $data['real'] >0 
			 || $data['depositingAccounts'] >0 
			 || $data['real_ftd'] >0 
			 || $data['ftd'] >0 
			 || $data['ftd_amount'] >0 
			 || $data['real_ftd_amount'] >0 
			 || $data['chargeback'] >0 
			 || $data['withdrawal'] >0 
			 || $data['bonus'] >0 
			 || $data['totalCom'] >0 
			 || $data['netRevenue'] >0 
			 || $data['volume'] >0 
		){
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$data['banner_id'].'</td>
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="NewWin(\'/admin/creative.php?act=edit_banner&id='.$data['banner_id'].'\',\'editbanner_'.$data['banner_id'].'\',\'1000\',\'800\',\'1\');">'.lang('Edit').'</a></td>
				<td style="text-align: left;">'.($data['banner_id'] ? $data['banner_title'] : lang('BANNER REMOVED')).'</td>
				<td style="text-align: left;">'.$data['language'].'</td>
				<td style="text-align: left;">'.($data['width']>0 ? $data['width'] : "").'</td>
				<td style="text-align: left;">'.($data['height']>0 ? $data['height'] : "").'</td>
				<td style="text-align: left;">'.ucwords($data['type']).'</td>
				<td style="text-align: left;">'.$data['merchant'].'</td>
				<td><a href="/admin/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'">'.@number_format($data['views'],0).'</a></td>
				<td><a href="/admin/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'">'.@number_format($data['clicks'],0).'</a></td>
				<td>'.@number_format(($data['clicks']/$data['views'])*100,2).' %</td>
				<td>'.@number_format(($data['real']/$data['clicks'])*100,2).' %</td>
				<td>'.@number_format(($data['ftd']/$data['clicks'])*100,2).' %</td>
				<td>'.@price($data['totalCom']/$data['clicks']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=lead">'.$data['leads'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=demo">'.$data['demo'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=real">'.$data['real'].'</a></td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=ftd">'.$data['ftd'].'</a></td>
				<td>'.price($data['ftd_amount']).'</td>
				<td><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=totalftd">'.$data['real_ftd'].'</a></td>
				<td>'.price($data['real_ftd_amount']).'</td>
				<td><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>
				<td>'.price($data['sumDeposits']).'</td>
				<td style="text-align: center;">'.price($data['volume']).'</td>
				<td>'.price($data['bonus']).'</td>
				<td>'.price($data['withdrawal']).'</td>
				<td>'.price($data['chargeback']).'</td>
				<td style="text-align: center;">'.price($data['netRevenue']).'</td>
				<td>'.price($data['totalCom']).'</td>
			</tr>';
			
			$totalImpressions += $data['views'];
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
						
		}
		}
                
                
                if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->totalRows  = $l;
		$set->sortTable  = 1;
		$set->content   .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="creative" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Banner ID').'</td>
					<td>'.lang('Affiliate ID').'</td>
					<td>'.lang('Creative Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" /></td>
					<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td>
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
						<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>
						<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>
						<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
						<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>
						<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
						<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>
						<option value="coupon" '.($type == "coupon" ? 'selected' : '').'>'.lang('Coupon').'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" style="width: 2400px;">'.lang('Report Results').'</div>
			
			<div style="background: #F8F8F8;">';
				$tableStr='<table width="2400" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Actions').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th style="text-align: left;">'.lang('Language').'</th>
						<th style="text-align: left;">'.lang('Width').'</th>
						<th style="text-align: left;">'.lang('Height').'</th>
						<th>'.lang('Type').'</th>
						<th style="text-align: left;">'.lang('Merchant').'</th>
						<th>'.lang('Impressions').'</th>
						<th>'.lang('Clicks').'</th>
						<th>'.lang('Click Through Ratio (CTR)').'</th>
						<th>'.lang(ptitle('Click to Account')).'</th>
						<th>'.lang(ptitle('Click to Sale')).'</th>
						<th>EPC</th>
						<th>'.lang(ptitle('Lead')).'</th>
						<th>'.lang(ptitle('Demo')).'</th>
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
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><a href="/admin/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressions.'</a></th>
						<th><a href="/admin/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicks.'</a></th>
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="/admin/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="/admin/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>';
			
		excelExporter($tableStr, 'Creative');
		$set->content.=$tableStr.'</div>'.getPager();
		theme();

?>