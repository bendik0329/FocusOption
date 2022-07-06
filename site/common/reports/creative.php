<?php

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/".$userlevel );
}


$pageTitle = lang('Creative Report');
	$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.$userlevel.'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';	
			
		 $set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
		<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
			<script>
			$(document).ready(function(){
				$("#combobox").combobox("autocomplete","'. $_GET['affiliate_id'] .'");
			});
			</script>
		 <!-- jQuery UI Autocomplete css -->
		<style>
		.custom-combobox {
			position: relative;
			display: inline-block;
		  }
		  .custom-combobox-toggle {
			position: absolute;
			top: 0;
			bottom: 0;
			margin-left: -1px;
			background: white;
			border-radius: inherit;
			border-color: #CECECE;
			border-left: 0;
			color: #1F0000;
		  } 
		  .custom-combobox-input {
			margin: 0;
			padding: 5px 10px;
			width: 120px;
			background: white;
			border-radius: inherit;
			border-color: #CECECE;
			color: #1F0000;
			font-weight: inherit;
			font-size: inherit;
		  }
		  .ui-autocomplete { 
			height: 200px; 
			width:  310px;
			overflow-y: scroll; 
			overflow-x: hidden;
		  }
		</style>';
		$filename = "creatives_data_" . date('YmdHis');

		$creativeArray = [];
		
                $where = ' 1 = 1 ';
            
         
            
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		if ($banner_id) {
                    $where .= " AND banner_id='".$banner_id."' ";
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
		 //$where_main =  str_replace('affiliate_id','mc.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','mc.merchant_id', $where_main) ;
		 $where_main =  str_replace('profile_id','mc.profile_id', $where_main) ;
		$where_main =  str_replace('banner_id','mc.id', $where_main) ;
		$sql = "SELECT mc.*,m.name as merchant_name,l.title as language FROM merchants_creative mc left join languages l on l.id = mc.language_id INNER JOIN merchants m on mc.merchant_id = m.id where " 
		. $where_main . " and mc.valid=1 ";
		
	// die($sql);	
		$bannersqq = function_mysql_query($sql,__FILE__);
		
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
		}
		
		/**
		 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
		 */
		 if($userlevel == 'manager')
			 $group_id       = $set->userInfo['group_id'];
		 else
			$group_id  = null;
		
		$where    .= empty($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';

		
		
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
		$creativeArray["0"]['banner_title'] = lang("Non LP Related");
	
		// clicks and impressions
		// $arrClicksAndImpressions = getClicksAndImpressions($from, $to, $merchant_id, $affiliate_id, $group_id,$banner_id);
		
		//$sql = "select banner_id, SUM(clicks) as total_clicks,sum(views) as total_views from traffic where " . $where . " AND merchant_id>0 and rdate BETWEEN '" . $from . "' AND '" . $to ."' GROUP BY banner_id";
		// die ($sql);
		/* select banner_id, SUM(clicks) as total_clicks,sum(views) as total_views from traffic where 1 = 1 AND group_id = 5 AND merchant_id>0 and rdate BETWEEN '2016-09-06' AND '2016-09-06 23:59:59' GROUP BY banner_id */
		
		
		$creatives_stats_where = '';
		$creatives_stats_join = '';
		if ($affiliate_id) {
            $creatives_stats_where .= " AND AffiliateID='".$affiliate_id."' ";
		}
		if ($merchant_id) {
            $creatives_stats_where .= " AND MerchantID='".$merchant_id."' ";
        }
                
		if ($banner_id) {
            $creatives_stats_where .= " AND BannerID='".$banner_id."' ";
        }
		
		if ($group_id) {
            $creatives_stats_join .= " INNER JOIN affiliates aff ON merchants_creative_stats.AffiliateID = aff.id AND aff.group_id = '".(int)$group_id."' ";
        }
		
        $sql = "SELECT SUM(Impressions) AS total_views, SUM(Clicks) AS total_clicks, BannerID FROM merchants_creative_stats ".$creatives_stats_join." WHERE (Date BETWEEN '" . $from . "' AND '" . $to ."') ".$creatives_stats_where." GROUP BY BannerID";

		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
				if(!isset($creativeArray[$trafficRow['BannerID']])){
						continue;
				}
				$creativeArray[$trafficRow['BannerID']]['clicks'] = $trafficRow['total_clicks'];
				$creativeArray[$trafficRow['BannerID']]['views'] = $trafficRow['total_views'];
		}
			
		
		$l = 0;
		$totalLeads=0;
		$totalDemo=0;
		$totalReal=0;
		$totalCPI=0;
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
		 $where_reg =  str_replace('group_id','dg.group_id', $where_reg) ;
		 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
		 
		$sql = "select * from (SELECT dg.*, m.name as merchant_name FROM data_reg dg"
					." INNER JOIN merchants m on m.id = dg.merchant_id "
					."WHERE " . $where . " AND dg.merchant_id>0 and dg.rdate BETWEEN '" . $from . "' AND '" . $to . "'" 
					. (isset($banner_id) && !empty($banner_id) ? ' AND dg.banner_id = "'.$banner_id.'"' :'')
					. (isset($group_id) && !empty($group_id) ? ' AND dg.group_id = '.$group_id :'')
					. " ) a group by merchant_id , trader_id ";
		// die ($sql);
		$regqq = function_mysql_query($sql,__FILE__);
		
		$arrTierCplCountCommissionParams = [];
			// die ($sql);
		while ($regww = mysql_fetch_assoc($regqq)) {
			if($regww['banner_id'] == ""  || $regww['banner_id'] == 0 || !isset($creativeArray[$regww['banner_id']])){
				continue;
			}
			
			$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
			$boolTierCplCount = !empty($strAffDealType) && 'cpl_count' == $strAffDealType;
			
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
						'group_id'    => $regww['group_id'],
						'banner_id'    => $regww['banner_id'],
						'initialftddate'    => $regww['initialftddate'],
						'trader_id'    => $regww['trader_id'],
						'profile_id'   => $regww['profile_id'],
					];
					
					$totalCom = getCommission($from, $to, 0, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
					
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
							'groupId'             => (empty($group_id ? -1 : $group_id)),
							'arrDealTypeDefaults' => $arrDealTypeDefaults,
							'arrTmp'              => [
								'merchant_id'  => $regww['merchant_id'],
								'affiliate_id' => $regww['affiliate_id'],
								'rdate'        => $regww['rdate'],
								'initialftddate'    => $regww['initialftddate'],
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
		
		
		
		$arrFtds  = getTotalFtds($from, $to, (!empty($affiliate_id)?$affiliate_id:0), (!empty($merchant_id)?$merchant_id:0), 0, (empty($group_id) ? -1 : $group_id));
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
			
				if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
				// die ('gergerge');	
					$ftd_amount = $real_ftd_amount;
					$arrFtd['isFTD'] = true;
					$totalCom = getCommission($from, $to, 0, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
					
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
					 . "WHERE  tb1.rdate BETWEEN '".$from."' AND '".$to."' " . (empty($group_id ? '' : ' AND tb1.group_id = ' . $group_id . ' '))
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($banner_id) && !empty($banner_id) ? ' AND tb1.banner_id = "'.$banner_id.'"' :''); */

					 
			/* 
					  $sql = "SELECT *, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND tb1.banner_id = data_reg.banner_id AND data_reg.type <> 'demo'  "
					 ." INNER JOIN merchants_creative mc on mc.id= tb1.banner_id "
					 . "WHERE tb1.type<>'pnl' and tb1.merchant_id>0 and mc.valid=1 and tb1.rdate BETWEEN '".$from."' AND '".$to."' " 
					 . (empty($group_id) ? '' : ' AND tb1.group_id = ' . $group_id . ' ')
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($banner_id) && !empty($banner_id) ? ' AND tb1.banner_id = "'.$banner_id.'"' :'');
					  */
					 			$sql = "SELECT data_reg.merchant_id,data_reg.affiliate_id,data_reg.initialftddate,tb1.rdate,data_reg.banner_id,data_reg.trader_id,data_reg.group_id,data_reg.profile_id,tb1.amount, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "
					 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND tb1.banner_id = data_reg.banner_id AND data_reg.type <> 'demo'  "
					 ." INNER JOIN merchants_creative mc on mc.id= tb1.banner_id "
					 . "WHERE tb1.type<>'pnl' and tb1.merchant_id>0 and mc.valid=1 and tb1.rdate BETWEEN '".$from."' AND '".$to."' " 
					 . (empty($group_id) ? '' : ' AND tb1.group_id = ' . $group_id . ' ')
					 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :'')
					 . (isset($banner_id) && !empty($banner_id) ? ' AND tb1.banner_id = "'.$banner_id.'"' :'');
					 
					 
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
						'initialftddate'        => $salesww['initialftddate'],
						'rdate'        => $salesww['rdate'],
						'banner_id'    => $salesww['banner_id'],
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
						$row['rdate']        = $from;
						$row['group_id']     =$group_id;
						$row['amount']       = $netRevenue;
						$row['isFTD']        = false;
						$row['initialftddate']        = 			$salesww['initialftddate'];
									  
						$totalCom           = getCommission($from, $to, 1, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									  
						$creativeArray[$salesww['banner_id']]['totalCom'] += $totalCom;
						}
				}
				// end of data_stats (revenue) loop

				// end of data_sales loop
		}
		
	
		$sql ="SELECT DISTINCT  ds.affiliate_id,ds.banner_id, ds.merchant_id,m.producttype as producttype, dg.country as country FROM data_stats ds INNER JOIN data_reg dg ON dg.trader_id = ds.trader_id INNER JOIN merchants m where ds.rdate BETWEEN '" . $from . "' AND '" . $to 
					 . "' AND (m.producttype = 'casino' or m.producttype ='sportsbetting') and m.valid=1" 
					 . (isset($banner_id) && !empty($banner_id) ? ' AND  ds.merchant_id>0 and dg.banner_id = "'.$banner_id.'"' :'')
					 . (isset($group_id) && !empty($group_id) ? ' AND   ds.group_id = "'.$group_id.'"' :'')
					 ;
		
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
					  
						$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
						
						$creativeArray[$revww['banner_id']]['totalCom'] += $totalCom;
						
						unset($arrRange2, $strRevWhere);
					}
					
					$netRevenue = $intTotalRevenue;
					$creativeArray[$revww['banner_id']]['netRevenue'] = $netRevenue;
					
		}
					
		
		
		
		$merchantsA  = getMerchants(0,1);
		foreach  ($merchantsA as $merchantww) {
			
			if (strtolower($merchantww['producttype'])!='forex')
				continue;
/* 		$sql = "select * from merchants where producttype = 'Forex' and valid =1";
		$totalqq = function_mysql_query($sql,__FILE__);
		while ($merchantww  = mysql_fetch_assoc($totalqq)) {
 */			
				$sql = 'SELECT SUM(ds.spread) AS totalSpread,  SUM(ds.turnover) AS totalTO, dg.banner_id as banner_id FROM data_stats ds '
						. ' INNER JOIN data_reg dg on ds.merchant_id = dg.merchant_id and ds.trader_id = dg.trader_id  '
						. 'WHERE ds.rdate BETWEEN "'.$from.'" AND "'.$to.'" ' 
						. (empty($group_id) ? '' : ' AND ds.group_id = ' . $group_id . ' ')
						. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
						. " and ds.merchant_id = " . $merchantww['id']
						. " and ds.merchant_id >0 "
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
						
						// $pnl = $ts['totalPnl'];
				}
						
				$totalLots  = 0;
											
							
							
				$sql = 'SELECT dg.initialftddate , ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id,dg.country as country FROM data_stats ds '
				 . ' INNER JOIN data_reg dg on ds.trader_id = dg.trader_id '
				 . 'WHERE  ds.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql) 
					. (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
					. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
					. " and ds.merchant_id >0 "
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
											'initialftddate'    => $toww['initialftddate'],
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
									
								/* 	if ($dealsForAffiliate['pnl']>0){
										
											$sql = 'SELECT  dr.initialftddate, pnltable.rdate,pnltable.amount as amount,pnltable.trader_id,pnltable.merchant_id,pnltable.banner_id,pnltable.profile_id,pnltable.affiliate_id FROM '.$set->pnlTable.' pnltable
														inner join data_reg dr on pnltable.merchant_id = dr.merchant_id and pnltable.trader_id = dr.trader_id 
														WHERE pnltable.type="PNL" '.($merchantww['id']>0 ? 'and pnltable.merchant_id>0   and pnltable.merchant_id = ' . $merchantww['id']: '')
														.(!empty($affiliate_id) ?  '  and  pnltable.affiliate_id = ' . $affiliate_id : "")
														.(!empty($group_id) ?  '  and  pnltable.group_id = ' . $group_id : "")
														. ' AND pnltable.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql);
									}
									else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
									
									$sql = 'SELECT  max(dr.initialftddate), sum(pnltable.amount) as amount,max(pnltable.rdate) ,max(pnltable.trader_id),max(pnltable.merchant_id),max(pnltable.banner_id),max(pnltable.profile_id),max(pnltable.affiliate_id) FROM '.$set->pnlTable.'
												inner join data_reg dr on pnltable.merchant_id = dr.merchant_id and pnltable.trader_id = dr.trader_id 
												 WHERE pnltable.type="PNL" and pnltable.merchant_id>0   '
												 .(!empty($affiliate_id) ?  '  and  pnltable.affiliate_id = ' . $affiliate_id : "")
												 .(!empty($group_id) ?  '  and  pnltable.group_id = ' . $group_id : "")
												 .(!empty($merchantww['id']) ?  '  and  pnltable.merchant_id = ' . $merchantww['id'] : "")
												 . ' AND pnltable.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql);
								} */
								
												// . ' AND ' . $globalWhere ;
									   
						   
											 //echo ($sql).'<Br>';
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
												 
											
												// $totalPNL = $totalPNL + $pnlamount;
												$creativeArray[$ts['banner_id']]['pnl'] += $pnlamount;
												
															 
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
										if ($dealsForAffiliate['pnl']>0){
											
											$tmpCom = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// echo 'com: ' . $tmpCom.'<br>';
												
												$creativeArray[$toww['banner_id']]['totalCom'] += $tmpCom;
										}
								}
						}
						
		// die;		
		
			if ($set->deal_cpi==1){
						// installation
					$merchantsA  = getMerchants(0,1);
					foreach  ($merchantsA as $merchantww) {
						$array = array();			
						$array['from']  	= 	$from ;
						$array['to'] = $to;
						$array['merchant_id'] = $merchantww['id'];
						$array['type'] = 'install' ;
						$array['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
						$array['searchInSql']  = $searchInSql;
						$array['group_id'] = $group_id ;
					
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
		
		foreach($creativeArray as $data){
			
			if ($data['views']>0 || $data['clicks']>0 || $data['leads'] >0 || $data['demo'] >0 || $data['real'] >0 || $data['cpi'] >0 
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
			 || $data['pnl'] >0 
			 || $data['volume'] >0 
		){
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$data['banner_id'].'</td>
				<td style="text-align: center;"><a href="javascript:void(0);" onclick="NewWin(\'/'. $userlevel .'/creative.php?act=edit_banner&id='.$data['banner_id'].'\',\'editbanner_'.$data['banner_id'].'\',\'1000\',\'800\',\'1\');">'.lang('Edit').'</a></td>
				<td style="text-align: left;">'.($data['banner_id'] ? $data['banner_title'] : lang('BANNER REMOVED')).'</td>
				<td style="text-align: left;">'.$data['language'].'</td>
				<td style="text-align: left;">'.($data['width']>0 ? $data['width'] : "").'</td>
				<td style="text-align: left;">'.($data['height']>0 ? $data['height'] : "").'</td>
				<td style="text-align: left;">'.ucwords($data['type']).'</td>
				<td style="text-align: left;">'.$data['merchant'].'</td>
				<td><a href="/'.$userlevel.'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'">'.@number_format($data['views'],0).'</a></td>
				<td><a href="/'.$userlevel.'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'">'.@number_format($data['clicks'],0).'</a></td>
				'.($set->deal_cpi?'<td>'.@number_format($data['cpi']).'</td>':'').'
				<td>'.@number_format(($data['clicks']/$data['views'])*100,2).' %</td>
				<td>'.@number_format(($data['real']/$data['clicks'])*100,2).' %</td>
				<td>'.@number_format(($data['ftd']/$data['clicks'])*100,2).' %</td>
				<td>'.@price($data['totalCom']/$data['clicks']).'</td>
				<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=lead">'.$data['leads'].'</a></td>
				<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=demo">'.$data['demo'].'</a></td>
				<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=real">'.$data['real'].'</a></td>
				<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=ftd">'.$data['ftd'].'</a></td>
				<td>'.price($data['ftd_amount']).'</td>
				<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=totalftd">'.$data['real_ftd'].'</a></td>
				<td>'.price($data['real_ftd_amount']).'</td>
				<td><a href="/'.$userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$data['merchant_id'].'&banner_id='.$data['banner_id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>
				<td>'.price($data['sumDeposits']).'</td>
				<td style="text-align: center;">'.price($data['volume']).'</td>
				<td>'.price($data['bonus']).'</td>
				<td>'.price($data['withdrawal']).'</td>
				<td>'.price($data['chargeback']).'</td>
				<td style="text-align: center;">'.price($data['netRevenue']).'</td>'.
				($set->deal_pnl==1 ? '<td style="text-align: center;">'.price($data['pnl']).'</td>':'').
				'<td>'.price($data['totalCom']).'</td>
			</tr>';
			
			$totalImpressions += $data['views'];
			$totalClicks += $data['clicks'];
			$totalCPI += $data['cpi'];
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
			$totalPNL += $data['pnl'];
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
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="creative" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Banner ID').'</td>
					<td width=160>'.lang('Affiliate ID').'</td>
					<td style="padding-left:20px">'.lang('Creative Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" /></td>
					<td><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" /-->
					<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
					</td>
					<td style="padding-left:20px"><select name="type" style="width: 150px;">
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
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#creativeData\').tableExport({type:\'csvbig\',ignoreColumn:[1],escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#creativeData\').tableExport({type:\'excelbig\',ignoreColumn:[1],escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle"  class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
			//width 2400
				$tableStr='<table class=" table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="creativeTbl">
					<thead><tr  class="table-row">
						<th  class="table-cell">'.lang('Creative ID').'</th>
						<th class="table-cell">'.lang('Actions').'</th>
						<th class="table-cell">'.lang('Creative Name').'</th>
						<th class="table-cell">'.lang('Language').'</th>
						<th class="table-cell">'.lang('Width').'</th>
						<th class="table-cell">'.lang('Height').'</th>
						<th class="table-cell">'.lang('Type').'</th>
						<th class="table-cell">'.lang('Merchant').'</th>
						<th class="table-cell">'.lang('Impressions').'</th>
						<th class="table-cell">'.lang('Clicks').'</th>
						'.($set->deal_cpi?'<th>'.lang('Installation').'</th>':'').'
						<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
						<th class="table-cell">'.lang(ptitle('Click to Account')).'</th>
						<th class="table-cell">'.lang(ptitle('Click to Sale')).'</th>
						<th class="table-cell">EPC</th>
						<th class="table-cell">'.lang(ptitle('Lead')).'</th>
						<th class="table-cell">'.lang(ptitle('Demo')).'</th>
						<th class="table-cell">'.lang(ptitle('Accounts')).'</th>
						<th class="table-cell">'.lang('FTD').'</th>
						<th class="table-cell">'.lang('FTD Amount').'</th>
						<th class="table-cell">'.lang('RAW FTD').'</th>
						<th class="table-cell">'.lang('RAW FTD Amount').'</th>
						<th class="table-cell">'.lang('Total Deposits').'</th>
						<th class="table-cell">'.lang('Deposit Amount').'</th>
						<th class="table-cell">'.lang('Volume').'</th>
						<th class="table-cell">'.lang('Bonus Amount').'</th>
						<th class="table-cell">'.lang('Withdrawal Amount').'</th>
						<th class="table-cell">'.lang('ChargeBack Amount').'</th>
						<th class="table-cell">'.lang(ptitle('Net Revenue')).'</th>
						'.($set->deal_pnl?'<th class="table-cell">'.lang(ptitle('PNL')).'</th>':'').'
						<th class="table-cell">'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th><b>'.lang('Total').':</b></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalImpressions.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=clicks&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'">'.$totalClicks.'</a></th>
						'.($set->deal_cpi?'<th>'.@number_format($totalCPI).'</th>':'').'
						<th>'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th>'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th>'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>
						<th>'.@price($totalComs/$totalClicks).'</th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th>'.price($totalFTDAmount).'</th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th>'.price($totalRealFtdAmount).'</th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th>'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th>'.price($totalBonusAmount).'</th>
						<th>'.price($totalWithdrawalAmount).'</th>
						<th>'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						'.($set->deal_pnl?'<th>'.price($totalPNL).'</th>':'').'
						<th>'.price($totalComs).'</th>
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>
				<script>
				$(document).ready(function(){
					try{
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'creativeData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#creativeTbl")[0].config.rowsCopy).each(function() {
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
											
											saveReportToMyFav(name, \'creative\',user,level,type);
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
			
		//excelExporter($tableStr, 'Creative');
		$set->content.=$tableStr.'</div>'.getPager();
		
			//MODAL
		$myReport = lang("Creatives");
		include "common/ReportFieldsModal.php";
		
		theme();

?>>