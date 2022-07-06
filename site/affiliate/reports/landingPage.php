<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

//$globalWhere = " and t.affiliate_id = " . $set->userInfo['id'];

$affiliate_id = $set->userInfo['id'];


$merchantIDsQ = function_mysql_query('SELECT merchants FROM affiliates WHERE id='.$set->userInfo['id'],__FILE__); //OR die(mysql_error());
		$merchantIDs = mysql_fetch_assoc($merchantIDsQ);
		$merchantIDs = implode(',',explode('|',$merchantIDs['merchants']));
		if (empty($merchantIDs))
			$merchantIDs = 0;
		else 
			$merchantIDs=ltrim($merchantIDs,',');
		
		
		$sql         = "SELECT * FROM merchants WHERE valid='1' ".$where." AND id IN (".$merchantIDs.") ORDER BY pos";
		
		if (isset($merchant_id) && !empty($merchant_id)) {
			if (in_array($merchant_id, explode(',', $merchantIDs))) {
				$sql = "SELECT * FROM merchants WHERE valid = '1' " . $where . " AND id = '" . $merchant_id . "' ORDER BY pos";
			}
		}
		
		
		
		$allbrabdrsc = function_mysql_query($sql,__FILE__);
		$LowestLevelDeal = 'ALL';
	$tradersProccessedForLots= array();
		$displayForex= 0;
		while ($brandsRow = mysql_fetch_assoc($allbrabdrsc)) {
				
	if (strtolower($brandsRow['producttype'])=='forex')
				$displayForex = 1;
				foreach ($dealsArray as $dealItem=>$value) {
				// var_dump($dealItem);
					if ($brandsRow['id']==$dealItem) {
						
						$LowestLevelDeal = getLowestLevelDeal($LowestLevelDeal, $value);
						break;
					}
				}
		}
	$deal = $LowestLevelDeal;


$pageTitle = lang('Landing Pages Report');
	$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';	
			
			$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "LandingPage_data_" . date('YmdHis');
		
		$creativeArray = [];
		
                $where = ' 1 = 1 ';
            
                $group_id  = null;
                $where    .= is_null($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		/* if ($url) {
                    $where .= " AND url like '%".$url."%' ";
                } */
				
	
                
		if ($profile_id) {
                    $where  .= " AND profile_id='".$profile_id."' ";
                }
              

		
		$merchantsArray = array();
					$displayForex = 0;
					$merchantsAr = getMerchants(0,1);
					
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
		 //$where_main =  str_replace('affiliate_id','mc.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','mc.merchant_id', $where_main) ;
		  // $where_main =  str_replace('group_id','mc.group_id', $where_main) ;
		 $where_main =  str_replace('profile_id','mc.profile_id', $where_main) ;
		 $where_main =  str_replace('banner_id','mc.id', $where_main) ;
		$sql = "SELECT mc.*,m.name as merchant_name,l.title as language FROM merchants_creative mc left join languages l on l.id = mc.language_id INNER JOIN merchants m on mc.merchant_id = m.id where " 
		. $where_main . " and mc.valid=1 ";

	
	
	  $where    .= empty($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
	  
	  
	// die ($sql);
	
		$bannersqq = function_mysql_query($sql,__FILE__);
		while ($bannersww = mysql_fetch_assoc($bannersqq)) {
					
					if ($type && $bannersww['type'] != $type) {
                        continue;
                    }
					// var_dump($bannersww);
					// die();
					$creativeArray[$bannersww['id']]['banner_id'] = $bannersww['id'];
					$creativeArray[$bannersww['id']]['url'] = $bannersww['url'];
				    $creativeArray[$bannersww['id']]['banner_title'] = $bannersww['title'];
					$creativeArray[$bannersww['id']]['type'] = $bannersww['type'];
					$creativeArray[$bannersww['id']]['merchant'] = $bannersww['merchant_name'];
					$creativeArray[$bannersww['id']]['language'] = $bannersww['language'];
					$creativeArray[$bannersww['id']]['width'] = $bannersww['width'];
					$creativeArray[$bannersww['id']]['height'] = $bannersww['height'];
					$creativeArray[$bannersww['id']]['merchant_id'] = $bannersww['merchant_id'];
		}
		
		$creativeArray["0"]['banner_title'] = lang("Non LP Related");
		
		
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
        }
		// clicks and impressions
		
		$sql = "select banner_id, SUM(clicks) as total_clicks,sum(views) as total_views from traffic where " . $where . " AND merchant_id > 0 and rdate BETWEEN '" . $from . "' AND '" . $to . "' GROUP BY banner_id";

// die ($sql);		
		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
				if(!isset($creativeArray[$trafficRow['banner_id']])){
						continue;
				}
				$creativeArray[$trafficRow['banner_id']]['clicks'] = $trafficRow['total_clicks'];
				$creativeArray[$trafficRow['banner_id']]['views'] = $trafficRow['total_views'];
		}
		
		/* $sql = "select * from traffic  where " . $where . " AND uid>0 AND rdate BETWEEN '" . $from . "' AND '" . $to . "'";
		
		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
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
		$cpi=0;
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
		$totalPNL=0;
		$depositsAmount=0;
		$totalCom=0;
		
		
		// registration (leads + demo + real)
		$where_reg = $where;
		$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
		 $where_reg =  str_replace('group_id','dg.group_id', $where_reg) ;
		 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
		 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
		 
		$sql = "SELECT dg.*,m.name as merchant_name FROM data_reg dg"
					." INNER JOIN merchants m on m.id = dg.merchant_id "
					."WHERE " . $where . " AND dg.merchant_id>0 and  dg.rdate BETWEEN '" . $from . "' AND '" . $to . "'" 
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
						'group_id'    => $regww['group_id'],
						'trader_id'    => $regww['trader_id'],
						'profile_id'   => $regww['profile_id'],
					];
					
					$totalCom = getCommission($from, $to, 0, (empty($group_id) ? -1 : $group_id), $arrDealTypeDefaults, $arrTmp);
					
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
								'group_id'    => $regww['group_id'],
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
		
		
		
		$arrFtds  = getTotalFtds($from, $to, (!empty($affiliate_id)?$affiliate_id:0), (!empty($merchant_id)?$merchant_id:0), 0, (empty($group_id) ? -1 : $group_id));
		// var_dump(count($arrFtds));
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
			
				
				if ($beforeNewFTD != $ftd ) {
				
				// die ('gergerge');	
					$ftd_amount = $real_ftd_amount;
					$arrFtd['isFTD'] = true;

					
				//	$totalCom = getCommission($from, $to, 0, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
					
				//	$creativeArray[$arrFtd['banner_id']]['totalCom'] += $totalCom;
				$creativeArray[$arrFtd['banner_id']]['ftd'] += 1;
				$creativeArray[$arrFtd['banner_id']]['ftd_amount'] += $ftd_amount;
				
				}
				unset($arrFtd);
		}
	
	
	
	// Qualified
		$ftdUsersQualified="";
		$FILTERbyTrader = !empty($trader_id)? $trader_id : 0;
        $selected_group_id = ($gorup_id<>"")? $group_id : -1;
		
		 $qftdQuery  = "SELECT * FROM `data_reg` where type<>'demo' and FTDqualificationDate>'0000-00-00 00:00:00' and FTDqualificationDate>'". $from ." 00:00:00' and FTDqualificationDate <'". $to ."'  " . ($affiliate_id?" and affiliate_id = " . $affiliate_id  : '') . " " . ($merchant_id?" and merchant_id = " . $merchant_id  : '') 
		 .(!empty($selected_group_id) && $selected_group_id>0 ? ' and group_id= '. $selected_group_id : '')  
		 .(!empty($FILTERbyTrader) ? ' and trader_id= '. $FILTERbyTrader : '') ;
		 $qftdQQ = function_mysql_query($qftdQuery,__FILE__);
		
		while ($arrFtd = mysql_fetch_assoc($qftdQQ)) {
				
								
					
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
		

				
				
				// die ('gergerge');	
					$ftd_amount = $real_ftd_amount;
					$arrFtd['isFTD'] = true;
					
					$totalCom = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, $selected_group_id, $arrDealTypeDefaults, $arrFtd);
					// $totalCom = getCommission($from, $to, 0, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
					
					$creativeArray[$arrFtd['banner_id']]['totalCom'] += $totalCom;
					$creativeArray[$arrFtd['banner_id']]['Qftd'] += 1;
				
				unset($arrFtd);
		}
	
	
		
		// $sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
		//Sales
 			$sql = "
			select * from (
			SELECT data_reg.merchant_id,data_reg.affiliate_id,data_reg.initialftddate,tb1.rdate,tb1.tranz_id,data_reg.banner_id,data_reg.trader_id,data_reg.group_id,data_reg.profile_id,tb1.amount, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 ." INNER JOIN merchants_creative mc on mc.id= tb1.banner_id "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
					 . "WHERE tb1.merchant_id> 0 and mc.valid=1 and tb1.rdate BETWEEN '".$from."' AND '".$to."' "
					 . (empty($group_id) ? '' : ' AND tb1.group_id = ' . $group_id . ' ')
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($banner_id) && !empty($banner_id) ? ' AND tb1.banner_id = "'.$banner_id.'"' :'').
					 " ) a group by merchant_id , tranz_id , data_sales_type; ";

		// die ($sql);
		
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
						'initialftddate'    => $salesww['initialftddate'],
						'trader_id'    => $salesww['trader_id'],
						'group_id'    => $salesww['group_id'],
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
				
					if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino' || true) {

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
							$row['group_id']    = $group_id;
							$row['rdate']        = $arrRange2['from'];
							$row['amount']       = $netRevenue;
							$row['isFTD']        = false;
								$row['initialftddate']        = $salesww['initialftddate'];
							  
							$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (is_null($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
							$creativeArray[$salesww['banner_id']]['totalCom'] += $totalCom;
						}
				}
				// end of data_stats (revenue) loop

				// end of data_sales loop
		}
		
	
	
		$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype, dg.country as country FROM data_stats ds INNER JOIN data_reg dg ON dg.trader_id = ds.trader_id INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
					 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" . (isset($banner_id) && !empty($banner_id) ? ' AND dg.merchant_id>0 and dg.banner_id = "'.$banner_id.'"' :'');
		
		$revqq  = function_mysql_query($sql,__FILE__); 					 
		while ($revww = mysql_fetch_assoc($revqq)) {
					$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
					$intTotalRevenue  = 0;
					
					foreach ($arrRevenueRanges as $arrRange2) {
						$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
									 . '"' . (empty($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
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
					  
						$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (empty($group_id) ? -1 : $group_id), $arrDealTypeDefaults, $row);
						
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
						. 'WHERE ds.merchant_id>0 and  ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' . (empty($group_id) ? '' : ' AND ds.group_id = ' . $group_id . ' ')
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
					. " and ds.merchant_id >0 and  ds.merchant_id = " . $merchantww['id']
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
											'group_id'    => $toww['group_id'],
											'profile_id'   => $toww['profile_id'],
											'type'       => 'lots',
											'amount'       =>  $totalLots,
								];
							$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row);
							// echo 'com: ' . $a .'<br>';
							$totalCom = $a;
							$creativeArray[$toww['banner_id']]['totalCom'] += $totalCom;
					// }
				}
		 }	
		 

		 
		 if ($set->deal_pnl == 1) {
						
								$totalPNL  = 0;
								$dealsForAffiliate['pnl'] = 1;
									
									
									$pnlRecordArray=array();
									
									$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
									$pnlRecordArray['merchant_id']  = $merchantww['id'];
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
													'initialftddate'        => $ts['initialftddate'],
													'banner_id'    => $ts['banner_id'],
													'trader_id'    => $ts['trader_id'],
													'profile_id'   => $ts['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  ($showCasinoFields==1 ?  calculateCasinoRevenue($pnlamount,$ts['type']) : $pnlamount) ,
												 'initialftddate'       =>  $ts['initialftddate']
												 ];
												 
											
												// $totalPNL = $totalPNL + $pnlamount;
												$creativeArray[$ts['banner_id']]['pnl'] += $pnlamount;
												
															 
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
										if ($dealsForAffiliate['pnl']>0){
											
											$tmpCom = getCommission($from, $to, 0, $ts['group_id'], $arrDealTypeDefaults, $row);
											// echo 'com: ' . $tmpCom.'<br>';
												
												$creativeArray[$toww['banner_id']]['totalCom'] += $tmpCom;
										}
								}
						}
		 
		 if ($set->deal_cpi==1){
					$merchantsA  = getMerchants(0,1);
					foreach  ($merchantsA as $merchantww) {
						$array = array();			
						$array['from']  	= 	$from ;
						$array['to'] = $to;
						$array['merchant_id'] = $merchantww['id'];
						$array['type'] = 'install' ;
						$array['affiliate_id']  =  $set->userInfo['id'];
						$array['searchInSql']  = $searchInSql;
						$array['banner_id']  =  $ww['banner_id'];
						
						$installs = generateInstallations($array);
						if (!empty($installs)){
						foreach ($installs as $install_item){
								
								$creativeArray[$install_item['banner_id']]['cpi'] += 1;
								$a= getCommission($install_item['rdate'], $install_item['rdate'], 0, -1, $arrDealTypeDefaults, $install_item);
                              
									$cpiCom += $a;
									
								       	 if ($_GET['ddd']==1) {
										 echo '<br><br>';
										 var_dump($a);
										 
										 echo '<br><br>';
										 echo '<br><br>';
										 var_dump($install_item);
										 echo '<br><br>';
											echo '00: ' . $a . '<br>';
											 echo '$totalCom: ' . $totalCom. '<br>';
										}
									$totalCom +=$a;
									$creativeArray[$install_item['banner_id']]['totalCom'] += $a;
									// unset($arrTmp);
									
									
							// var_dump($install_item);
							// echo '<Br><Br>';
							// die('--');
							unset($a);
						}
						}
						// end of install
				}
			}
		
		//echo "<pre>";print_r($creativeArray);die;
		$newUrlArray = array();		 
			foreach($creativeArray as $data){
					if (empty($newUrlArray[$data['url']])) {
						
						$newUrlArray[$data['url']]['url'] = $data['url'];
						$newUrlArray[$data['url']]['merchant'] = $data['merchant'];
						$newUrlArray[$data['url']]['clicks'] = 0;
						$newUrlArray[$data['url']]['cpi'] = 0;

						$newUrlArray[$data['url']]['views'] = 0;
						$newUrlArray[$data['url']]['leads'] = 0;
						$newUrlArray[$data['url']]['demo'] = 0;
						$newUrlArray[$data['url']]['real'] = 0;
						$newUrlArray[$data['url']]['depositingAccounts'] = 0;
						$newUrlArray[$data['url']]['real_ftd'] =0;
						$newUrlArray[$data['url']]['ftd'] =0;

						$newUrlArray[$data['url']]['real_ftd_amount'] =0;
						$newUrlArray[$data['url']]['ftd_amount'] =0;
						$newUrlArray[$data['url']]['withdrawal'] =0;
						$newUrlArray[$data['url']]['bonus'] =0;
						$newUrlArray[$data['url']]['totalCom'] =0;
						$newUrlArray[$data['url']]['netRevenue'] =0;
						$newUrlArray[$data['url']]['volume'] =0;

						$newUrlArray[$data['url']]['sumDeposits'] =0;
						if($set->deal_pnl){
							$newUrlArray[$data['url']]['pnl'] =0;
						}
							$newUrlArray[$data['url']]['qFTD'] =0;
						
						
					}
					
					
						$newUrlArray[$data['url']]['clicks'] += $data['clicks'];
						$newUrlArray[$data['url']]['cpi'] += $data['cpi'];
						$newUrlArray[$data['url']]['views'] += $data['views'];
						$newUrlArray[$data['url']]['leads'] += $data['leads'];
						$newUrlArray[$data['url']]['demo'] += $data['demo'];
						$newUrlArray[$data['url']]['real'] += $data['real'];
						$newUrlArray[$data['url']]['depositingAccounts'] += $data['depositingAccounts'];
						$newUrlArray[$data['url']]['real_ftd'] += $data['real_ftd'];
						$newUrlArray[$data['url']]['ftd'] += $data['ftd'];
						$newUrlArray[$data['url']]['real_ftd_amount'] += $data['real_ftd_amount'];
						$newUrlArray[$data['url']]['ftd_amount'] += $data['ftd_amount'];
						$newUrlArray[$data['url']]['withdrawal'] += $data['withdrawal'];
						$newUrlArray[$data['url']]['bonus'] += $data['bonus'];
						$newUrlArray[$data['url']]['totalCom'] += $data['totalCom'];
						$newUrlArray[$data['url']]['netRevenue'] += $data['netRevenue'];
						$newUrlArray[$data['url']]['volume'] += $data['volume'];
						$newUrlArray[$data['url']]['sumDeposits'] += $data['sumDeposits'];

						if($set->deal_pnl){
							$newUrlArray[$data['url']]['pnl'] +=$data['pnl'];
						}
						$newUrlArray[$data['url']]['Qftd'] += $data['Qftd'];

						
			}
		


		
		
		foreach($newUrlArray as $data){
			
			if ($data['views']>0 || $data['clicks']>0 || $data['leads'] >0 || $data['demo'] >0 || $data['real'] >0 || $data['cpi'] >0
			 || $data['depositingAccounts'] >0 
			 || $data['real_ftd'] >0 
			 || $data['Qftd'] >0 
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
				<td style="text-align: left;">'.$data['url'].'</td>
				<td style="text-align: left;">'.$data['merchant'].'</td>
				'.(allowView('af-impr',$deal,'fields') ?'
										<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'">'.@number_format($data['views'],0).'</a></td>
										' : '').'
				'.(allowView('af-clck',$deal,'fields') ? '					
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'">'.@number_format($data['clicks'],0).'</a></td>
				' : '').'
				'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi? '					
				<td>'.@number_format($data['cpi'],0).'</td>
				' : '').'
				
				
				<td>'.@number_format(($data['clicks']/$data['views'])*100,2).' %</td>
				<td>'.@number_format(($data['real']/$data['clicks'])*100,2).' %</td>
				<td>'.@number_format(($data['ftd']/$data['clicks'])*100,2).' %</td>
				<td>'.@price($data['totalCom']/$data['clicks']).'</td>'.

				(!$hideDemoAndLeads  ? 
					(allowView('af-lead',$deal,'fields') ? '
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=lead">'.$data['leads'].'</a></td>
				' : '').'
				'. (allowView('af-demo',$deal,'fields') ? '	
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=demo">'.$data['demo'].'</a></td>
				' : '')
				: '') . 
				(allowView('af-real',$deal,'fields') ? 
				'<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=real">'.$data['real'].'</a></td>
				' :'')
				. (allowView('af-ftd',$deal,'fields') ? '
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=ftd">'.$data['ftd'].'</a></td>
				' : '') .'
				'.(allowView('af-ftda',$deal,'fields') ? '<td>'.price($data['ftd_amount']).'</td>' :  '').'
				'.(allowView('af-tftd',$deal,'fields') ? '<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=totalftd">'.$data['real_ftd'].'</a></td>
				' : '').'
				'.(allowView('af-tftda',$deal,'fields') ? '<td>'.price($data['real_ftd_amount']).'</td>' : '').'	
				'.( (allowView('af-depo',$deal,'fields')) ? '
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>
				' : '')
				.( (allowView('af-depoam',$deal,'fields'))?
				'<td>'.price($data['sumDeposits']).'</td>
				' : ''
                      )
				.(allowView('af-vlm',$deal,'fields') ? 
				'<td style="text-align: center;">'.price($data['volume']).'</td>
				' : '')
				.
				( (allowView('af-bns',$deal,'fields'))?'<td>'.price($data['bonus']).'</td>': '' ).
				
				/*( (allowView('af-withd',$deal,'fields')) ? '<td>'.price($data['withdrawal']).'</td>' : '' ) .*/
				
				( (allowView('af-chrgb',$deal,'fields')) ? '
				<td>'.price($data['chargeback']).'</td>
				' : '')
				.
				(allowView('af-ntrv',$deal,'fields') ?  '
				<td style="text-align: center;">'.price($data['netRevenue']).'</td>
				' : '').'
				
				'.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')?'<td style="text-align: center;">'.price($data['pnl']).'</td>':'').'
				'.(allowView('af-qftd',$deal,'fields') ?  '
				<td style="text-align: center;">'.($data['Qftd']).'</td>
				' : '').'
				'./*.'<td>'.price($data['totalCom']).'</td>'.*/'
			</tr>';
			
			$totalImpressions += $data['views'];
			$totalClicks += $data['clicks'];
			$totalCPI += $data['cpi'];
			$totalLeadsAccounts += $data['leads'];
			$totalDemoAccounts += $data['demo'];
			$totalRealAccounts += $data['real'];
			$totalFTD += $data['ftd'];
			$totalQFTD += $data['Qftd'];
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
			if($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields'))
			$totalSumPnl += $data['pnl'];
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
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="landingPage" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('URL').'</td>
					<!---td>'.lang('Affiliate ID').'</td-->
					<td>'.lang('Creative Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="url" value="'.$URL.'" /></td>
					<!--td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td-->
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
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#landingPageTbl\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#landingPageTbl\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle" class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
			//width 2400
				$tableStr='<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="landingPageTbl">
					<thead><tr class="table-row">
						<th  class="table-cell" style="text-align: left;">'.lang('URL').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Merchant').'</th>
						<th class="table-cell">'.lang('Impressions').'</th>
						<th class="table-cell">'.lang('Clicks').'</th>
						'. (allowView('af-instl',$deal,'fields') && $set->deal_cpi? '	
						<th class="table-cell">'.lang('Installation').'</th>':'').'
						<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
						<th class="table-cell">'.lang(ptitle('Click to Account')).'</th>
						<th class="table-cell">'.lang(ptitle('Click to Sale')).'</th>
						<th class="table-cell">EPC</th>'.
						(!$hideDemoAndLeads  ? ('
						'. (allowView('af-lead',$deal,'fields') ? '	
						<th class="table-cell">'.lang(ptitle('Lead')).'</th>
						' : '' ).'
						'. (allowView('af-demo',$deal,'fields') ? '	
						<th class="table-cell">'.lang(ptitle('Demo')).'</th>
						': '' ) ) : '' ).'
						'.(allowView('af-real',$deal,'fields') ? '<th class="table-cell">'.lang(ptitle('Accounts')).'</th>':'').'
						'. (allowView('af-ftd',$deal,'fields') ? '<th class="table-cell">'.lang('FTD').'</th>':'').'
						'. (allowView('af-ftda',$deal,'fields') ? '<th class="table-cell">'.lang('FTD Amount').'</th>':'')
						.	(allowView('af-tftd',$deal,'fields') ? '<th class="table-cell">'.lang('RAW FTD').'</th>' : '')
						.	(allowView('af-tftda',$deal,'fields') ? '<th class="table-cell">'.lang('RAW FTD Amount').'</th>' : '')
						. (allowView('af-depo',$deal,'fields') && (true) ? '<th class="table-cell">'.lang('Total Deposits').'</th>' : '' )
					. (allowView('af-depoam',$deal,'fields') && (true) ? '<th class="table-cell">'.lang('Deposit Amount').'</th>':'')
					. (allowView('af-vlm',$deal,'fields')  ? '<th class="table-cell">'.lang('Volume').'</th>':'')
					. (allowView('af-bns',$deal,'fields')  ? '<th class="table-cell">'.lang('Bonus Amount').'</th>':'')
					/*. (allowView('af-withd',$deal,'fields')  ? '<th class="table-cell">'.lang('Withdrawal Amount').'</th>':'')*/
					. (allowView('af-chrgb',$deal,'fields')  ? '<th class="table-cell">'.lang('ChargeBack Amount').'</th>':'')
					. (allowView('af-ntrv',$deal,'fields')  ? '<th class="table-cell">'.lang(ptitle('Net Deposit')).'</th>':'').'
						'.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')?'<th class="table-cell">'.lang(ptitle('PNL')).'</th>':'').'
						
						
						'.( allowView('af-qftd',$deal,'fields')?'<th class="table-cell">'.lang(ptitle('Active Traders')).'</th>':'').'
						'./*.'<th class="table-cell">'.lang('Commission').'</th>'.*/'
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th><b>'.lang('Total').':</b></th>
						<th></th>
						
						
						'. (allowView('af-impr',$deal,'fields') ? '			
							<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressions.'</a></th>' : '' ) .'
						'. (allowView('af-clck',$deal,'fields') ? '			
							<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicks.'</a></th>' : '' ).'
						'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi? '<th>'.@number_format($totalCPI).'</th>':'').'
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>'.
						
						(!$hideDemoAndLeads ? 
						(allowView('af-lead',$deal,'fields') ? 					
					'<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>' : '').
					(allowView('af-demo',$deal,'fields') ? 
					   '<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>' : '' )
					: '') .
						(allowView('af-real',$deal,'fields') ? 
					'<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>' : '' ) .
					(allowView('af-ftd',$deal,'fields') ? '
					<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>' : '')	
						.(allowView('af-ftda',$deal,'fields') ?
					'<th>'.price($totalFTDAmount).'</th>' : '') 
				.	
					(allowView('af-tftd',$deal,'fields') ? 
					'<th><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.$arrRange['from'].'&to='.$arrRange['to'].'&type=ftd">'.$totalRealFtd.'</a></th>' : '').
					(allowView('af-tftda',$deal,'fields') ? 
					 '<th>'.price($totalRealFtdAmount).'</th>' : '')
					 .
					( (allowView('af-depo',$deal,'fields')) ? 
					 '<th><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>':'').
					 
					 ( (allowView('af-depoam',$deal,'fields')) ? 
                                         '<th>'.price($totalDepositAmount).'</th>': '')
										 .
					(allowView('af-vlm',$deal,'fields') ?  '<th>'.price($totalVolume).'</th>' : '')
					.
					(allowView('af-bns',$deal,'fields') ?  '<th>'.price($totalBonusAmount).'</th>' : '').
					/*(allowView('af-withd',$deal,'fields') ?  '<th>'.price($totalWithdrawalAmount).'</th>' : '').*/
					(allowView('af-chrgb',$deal,'fields') ?  '<th>'.price($totalChargeBackAmount).'</th>' : '').
					(allowView('af-ntrv',$deal,'fields') ?  '<th>'.price($totalNetRevenue).'</th>' : '').
					
					($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')?'<th>'.price($totalSumPnl).'</th>':'').
							
					(allowView('af-qftd',$deal,'fields') ?  '<th>'.($totalQFTD).'</th>' : '').'

						'./*.'<th>'.price($totalComs).'</th>'.*/'
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>
				<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
	<script>
				$(document).ready(function(){
					try{
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'landingPageData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#landingPageTbl")[0].config.rowsCopy).each(function() {
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
											level = "affiliate";
											type = "add";
											
											saveReportToMyFav(name, \'landingPage\',user,level,type);
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
			
		//excelExporter($tableStr, 'LandingPage');
		$set->content.=$tableStr.'</div>'.getPager();
		
		//MODAL
		$myReport = lang("Landing Pages");
		include "common/ReportFieldsModal.php";
		
		theme();

?>