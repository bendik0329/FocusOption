<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$globalWhere = " and t.affiliate_id = " . $set->userInfo['id'];
$affiliate_id =  $set->userInfo['id'];

$globalWhereSales = str_replace('t.affiliate_id', 'tb1.affiliate_id', $globalWhere);

$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
<!--script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/blob.js"></script-->
<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
$filename = "clicks_data_" . date('YmdHis');

$pageTitle = lang('Clicks Report');
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
		$page = (isset($page) || !empty($page))?$page:1;
		$set->page = $page;
		
		$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
				<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "clicks_data_" . date('YmdHis');
		$start_limit = $page==1?0:$set->rowsNumberAfterSearch * ($page -1);
		$end_limit = $set->rowsNumberAfterSearch;// * $page;

$sql = "select id,name from affiliates_profiles where valid =1";
$qqProfiles = function_mysql_query($sql);
$listProfiles = array();
while($wwProfiles = mysql_fetch_assoc($qqProfiles)){
	$listProfiles[$wwProfiles['id']] = $wwProfiles['name'];
}
if (!empty($trader_id)){
	$rsc_trdr = mysql_query("select uid from data_reg where 1=1 ".(!empty($merchant_id) ? " and merchant_id =" . $merchant_id : "" )." and trader_id =  '" . $trader_id  .  "'  limit 1 ;");
	$uidrow = mysql_fetch_assoc($rsc_trdr);
	// var_dump($uidrow);
	// die();
	if (!empty($uidrow))
		$unique_id = $uidrow['uid'];
	
}
		
		
		
		$clickArray = [];
		
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
		
		$orderBy = "";
		if(isset($sortBy) && $sortBy!=""){
			
			if($sortBy == "affiliate_username"){
				$sortBy_new = "af.username";
			}	
			else if($sortBy == "merchant_name"){
				$sortBy_new = "m.name";
			}
			else if($sortBy == "trader_id" || $sortBy == "trader_alias"){
				$sortBy_new  = "";
			}
			else{
				$sortBy_new = "t." . $sortBy;
			}
			
			if(isset($sortOrder) &&$sortOrder!="")
			{
				if($sortBy_new !="")
				$orderBy = " order by " . $sortBy_new . " " . $sortOrder;
			}
			else{
				if($sortBy_new !="")
				$orderBy = " order by " . $sortBy_new . " ASC";
			}
		}
		else{
			$orderBy = "ORDER BY t.id DESC";
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
		
		$where_main = $where;
$where_main =  str_replace('affiliate_id','t.affiliate_id', $where_main) ;
		 $where_main =  str_replace('merchant_id','t.merchant_id', $where_main) ;
		 $where_main =  str_replace('profile_id','t.profile_id', $where_main) ;
		 $where_main =  str_replace('banner_id','t.banner_id', $where_main) ;
		 
		 $type_filter = "";
		 if($type == 'clicks')
		 $type_filter = ' and t.clicks > 0';
		 else if($type == "views")
		 $type_filter = ' and t.views > 0';
		 
	/* 	 $sql = "SELECT count(*) as total_records FROM traffic  t"
					. " WHERE " . $where . $type_filter .
					(!empty($unique_id) ? ' and t.uid = ' . $unique_id :'') 
					." and t.rdate BETWEEN '".$from."' AND '".$to. "' ";// and t.uid >0 "; */
		/* 		$sql = "SELECT count(t.id) as total_records from traffic t"
					. " INNER JOIN merchants m on m.id = t.merchant_id "
					. " left JOIN affiliates af on af.id = t.affiliate_id "
					. " LEFT JOIN languages lang on lang.id = t.language_id" 
					. " WHERE " . $where_main . $type_filter
					  . "  and t.uid > 0"
					 . "  and t.merchant_id > 0"
					. (!empty($unique_id) ? ' and t.uid = ' . $unique_id :'')
					." and t.rdate BETWEEN '".$from."' AND '".$to. "' ORDER BY t.id DESC";
					 */
					
						$sql = "SELECT count(t.id) as total_records from traffic t"
					
					. " left JOIN affiliates af on af.id = t.affiliate_id "
				
					. " WHERE " . $where_main . $type_filter
					  . "  and t.uid > 0"
					 . "  and t.merchant_id > 0"
					. (!empty($unique_id) ? ' and t.uid = ' . $unique_id :'')
					." and t.unixRdate BETWEEN '".convertTimeStampToUnixTimeStamp($from)."' AND '".convertTimeStampToUnixTimeStamp($to). "' "
					// ."ORDER BY t.id DESC"
					;
					
					// ." and t.rdate BETWEEN '".$from."' AND '".$to. "' "
					
					



		$totalRec = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		
		$total_records = $totalRec['total_records'];
		$set->total_records = $total_records; 
					
		$sql = "SELECT t.*,mc.url as mc_url,mc.title as mc_title,lang.title as language, m.name as merchant_name,af.username as affiliate_username from traffic t"
					. " INNER JOIN merchants m on m.id = t.merchant_id "
					. " left JOIN affiliates af on af.id = t.affiliate_id "
					. " LEFT JOIN languages lang on lang.id = t.language_id" 
					. " LEFT JOIN merchants_creative mc on t.banner_id = mc.id "
					. " WHERE 1=1 and " . $where_main . $type_filter
					  . "  and t.uid > 0"
					 . "  and t.merchant_id > 0" 
					. (!empty($unique_id) ? ' and t.uid = ' . $unique_id :'')
." and t.unixRdate BETWEEN '".convertTimeStampToUnixTimeStamp($from)."' AND '".convertTimeStampToUnixTimeStamp($to). "'". $orderBy ."  limit " . $start_limit. ", " . $end_limit;
					// ." and t.rdate BETWEEN '".$from."' AND '".$to. "'". $orderBy ."  limit " . $start_limit. ", " . $end_limit;
					
		
	/* 	$ajaxSqlArray = "SELECT t.*,mc.url as mc_url,mc.title as mc_title,lang.title as language, m.name as merchant_name,af.username as affiliate_username
											from  (
											select t.* from traffic t where ".$where_main . $type_filter." AND t.merchant_id > 0  and t.rdate BETWEEN '". $from ."' AND '". $to ."'"
											. (!empty($unique_id) ? ' and t.uid = ' . $unique_id :'') . " limit 970000, 1000 ) t 

											INNER JOIN merchants m on m.id = t.merchant_id INNER JOIN affiliates af on af.id = t.affiliate_id LEFT JOIN languages lang on lang.id = t.language_id LEFT JOIN merchants_creative mc on t.banner_id = mc.id WHERE 2=2 and 1 = 1 ORDER BY t.id DESC"; */
		
		$sqlArr = array("select"=>"select t.* ",
		"midpart" => "from traffic t where ",
		"wherePart" =>$where_main . $type_filter ." AND t.merchant_id > 0". (!empty($unique_id) ? ' and t.uid = ' . $unique_id :''),
		"wherePart2" => " and t.unixRdate BETWEEN '".convertTimeStampToUnixTimeStamp($from)."' AND '".convertTimeStampToUnixTimeStamp($to). "'"
		);
		
		$ajaxSqlArray = $sqlArr;
		
		// "wherePart2" => " and t.rdate BETWEEN '".$from."' AND '".$to. "'"
		
		
		
		/* $ajaxSqlArray = array('select'=>	 "SELECT t.*,mc.url as mc_url,mc.title as mc_title,lang.title as language, m.name as merchant_name,af.username as affiliate_username",
										'midpart' => "		from traffic t"
					. " INNER JOIN merchants m on m.id = t.merchant_id "
					. " INNER JOIN affiliates af on af.id = t.affiliate_id "
					. " LEFT JOIN languages lang on lang.id = t.language_id" 
					. " LEFT JOIN merchants_creative mc on t.banner_id = mc.id "
					. " WHERE 2=2 and ",
					 "wherePart" => $where_main . $type_filter
					. "  and t.uid > 0"
					. "  and t.merchant_id > 0"
					

					"wherePart2" => " and t.rdate BETWEEN '".$from."' AND '".$to. "'",
					'order'=>  $orderBy); */
			$ajaxSql = json_encode($ajaxSqlArray);
			
			$ajaxSql =  (encrypt_decrypt('encrypt' ,$ajaxSql));
			//die($ajaxSql);
			
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
				 $clickArray[$clickww['id']]['banner_title'] = $clickww['mc_title'];
				 $clickArray[$clickww['id']]['banner_url'] = $clickww['mc_url'];
				 $clickArray[$clickww['id']]['profile_id'] = $clickww['profile_id'];
				 $clickArray[$clickww['id']]['param'] = $clickww['param'];
				 $clickArray[$clickww['id']]['param2'] = $clickww['param2'];
				 $clickArray[$clickww['id']]['param3'] = $clickww['param3'];
				 $clickArray[$clickww['id']]['param4'] = $clickww['param4'];
				 $clickArray[$clickww['id']]['param5'] = $clickww['param5'];
				 $clickArray[$clickww['id']]['refer_url'] = $clickww['refer_url'];
				 $clickArray[$clickww['id']]['language'] = $clickww['language'];
				 $clickArray[$clickww['id']]['country'] = $clickww['country_id'];
				 $clickArray[$clickww['id']]['ip'] = $clickww['ip'];
				 $clickArray[$clickww['id']]['merchant_name'] = $clickww['merchant_name'];
				 $clickArray[$clickww['id']]['affiliate_username'] = $clickww['affiliate_username'];
				 $clickArray[$clickww['id']]['affiliate_id'] = $clickww['affiliate_id'];
				 
				 $clickArray[$clickww['id']]['platform'] = $clickww['platform'];
				 
				 if(empty($clickww['os']))
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
				$Qftd=0;
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
				 if(!empty($clickArray)){           
				   // registration (leads + demo + real)
				$where_reg = $where;
				$where_reg =  str_replace('affiliate_id','dg.affiliate_id', $where_reg) ;
				 $where_reg =  str_replace('merchant_id','dg.merchant_id', $where_reg) ;
				 $where_reg =  str_replace('profile_id','dg.profile_id', $where_reg) ;
				  $where_reg =  str_replace('banner_id','dg.banner_id', $where_reg) ;
				  
				  
				  
				  
				  
				  
				  
				  

				  if ($clickww['uid']>0) {
				$sql = "SELECT dg.* FROM data_reg dg"
							." WHERE " . $where_reg 
							." and dg.uid > 0"
							." and dg.merchant_id > 0"
							. " and dg.rdate >= '" . $clickww['rdate'] . "'"
							. " AND dg.uid = " . $clickww['uid'] 
							. ($sortBy == "trader_id" || $sortBy == "trader_alias"? " ORDER BY dg." . $sortBy . " " . $sortOrder : "");


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
						$tranrow['merchant_id'] = $regww['merchant_id'];
						$regArray[] = array($tranrow);

						$clickArray[$clickww['id']]['reg_date'] = $regww['reg_date'];
						$clickArray[$clickww['id']]['trader_id'] = $regww['trader_id'];
						$clickArray[$clickww['id']]['trader_name'] = $regww['trader_alias'];
						
						$clickArray[$clickww['id']]['sale_status'] = $regww['saleStatus'];
						
						$strAffDealType = getAffiliateTierDeal($regww['merchant_id'],$regww['affiliate_id']);
						$boolTierCplCount = !empty($strAffDealType) && 'cpl_count' == $strAffDealType;
						
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
									'initialftddate'    => $regww['initialftddate'],
									'trader_id'    => $regww['trader_id'],
									'profile_id'   => $regww['profile_id'],
								];
								
								$totalCom = getCommission($from, $to, 0, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrTmp);
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
										'groupId'             => (empty($group_id ? -1 : $group_id)),
										'arrDealTypeDefaults' => $arrDealTypeDefaults,
										'arrTmp'              => [
											'merchant_id'  => $regww['merchant_id'],
											'affiliate_id' => $regww['affiliate_id'],
											'initialftddate'        => $regww['initialftddate'],
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
						// var_dump($params);
						$looped_trader_id = $params[0]['trader_id'];
						$regDate = $params[0]['rdate'];
						if(!empty($looped_trader_id)){
						$arrFtds  = getTotalFtds($from, $to, (!empty($affiliate_id)?$affiliate_id:0), (!empty($merchant_id)?$merchant_id:0), 0, (empty($group_id) ? 0 : $group_id),0,0,0,$looped_trader_id);


						foreach ($arrFtds as $arrFtd) {
								$real_ftd++;
								$clickArray[$clickww['id']]['real_ftd'] += 1;
								
								$real_ftd_amount = $arrFtd['amount'];
								$clickArray[$clickww['id']]['real_ftd_amount'] += $real_ftd_amount;
								
								$beforeNewFTD = $ftd;
								getFtdByDealType($arrFtd['merchant_id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount, $ftd);
							
								if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
									
									$arrFtd['isFTD'] = true;
							//		$totalCom = getCommission($from, $to, 0, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $arrFtd);
									
								//	$clickArray[$clickww['id']]['totalCom'] += $totalCom;
								}
								$clickArray[$clickww['id']]['ftd'] = $ftd;
										
								$clickArray[$clickww['id']]['ftd_amount'] = $ftd_amount;
								unset($arrFtd);
						
						}
						
						
						
						
						$looped_trader_id = $params[0]['trader_id'];
						$regDate = $params[0]['rdate'];
						if(!empty($looped_trader_id)){
							
									 $qftdQuery  = "SELECT * FROM `data_reg` where type<>'demo' and FTDqualificationDate>'0000-00-00 00:00:00' and FTDqualificationDate>'". $from ." 00:00:00' and FTDqualificationDate <'". $to ."' " . ($affiliate_id?" and affiliate_id = " . $affiliate_id  : '') . " and merchant_id = ". $regDate = $params[0]['merchant_id']  
							 .(!empty($selected_group_id) && $selected_group_id>0 ? ' and group_id= '. $selected_group_id : '')  
							 .(!empty($FILTERbyTrader) ? ' and trader_id= '. $FILTERbyTrader : '') ;
							
							 $qftdQQ = function_mysql_query($qftdQuery,__FILE__);
								//$arrFtds  = getTotalFtds($from, $to, (!empty($affiliate_id)?$affiliate_id:0), (!empty($merchant_id)?$merchant_id:0), 0, (empty($group_id) ? -1 : $group_id),0,0,"",$FILTERbyTrader,"",false,1);
								while ($arrFtd = mysql_fetch_assoc($qftdQQ)) {
										
										
										// die ('gergerge');	
											$ftd_amount = $real_ftd_amount;
											$arrFtd['isFTD'] = true;
											
											$totalCom = getCommission($arrFtd['FTDqualificationDate'], $arrFtd['FTDqualificationDate'], 0, $selected_group_id, $arrDealTypeDefaults, $arrFtd);
											
									
											$clickArray[$clickww['id']]['totalCom'] += $totalCom;
								
								
											
											
										$clickArray[$clickww['id']]['totalCom'] += $totalCom;
										$clickArray[$clickww['id']]['Qftd'] += 1;
									
										unset($arrFtd);
								}
					
						}

				
				

									//Sales
					$sql = "SELECT data_reg.affiliate_id,data_reg.merchant_id,data_reg.initialftddate,tb1.rdate,data_reg.banner_id,data_reg.trader_id,data_reg.profile_id,tb1.amount, tb1.type AS data_sales_type  ,data_reg.country as country FROM data_sales as tb1 "

								 . "INNER JOIN data_reg AS data_reg ON tb1.merchant_id = data_reg.merchant_id AND tb1.trader_id = data_reg.trader_id AND data_reg.type <> 'demo'  "
								 . "WHERE  tb1.trader_id = " .  $looped_trader_id
							//	 . ' and tb1.rdate between "' . $from . '" AND "' . $to . '"' 
								. " and tb1.rdate >= '" . $regDate . "'"
								. " and tb1.merchant_id >0 "
								. (empty($group_id) ? '' : ' AND tb1.group_id = ' . $group_id . ' ')
								 . (!empty($affiliate_id) ? ' and tb1.affiliate_id = ' . $affiliate_id :' ')
								 . (isset($banner_id) && !empty($banner_id) ? ' AND data_reg.banner_id = "'.$banner_id.'"' :' ') 
								 .(!empty($unique_id) ? ' and data_reg.uid = ' . $unique_id :' ');
					

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
									'merchant_id'  => $salesww['merchant_id'],
									'affiliate_id' => $salesww['affiliate_id'],
									'initialftddate'        => $salesww['initialftddate'],
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
							
							if (strtolower($merchantww['producttype']) != 'sportsbetting' && strtolower($merchantww['producttype']) != 'casino' || true) {
								
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
									$row['initialftddate']        =  $salesww['initialftddate'];
									  
									$totalCom           = getCommission($from, $to, 1, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
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
								 . " and ds.trader_id=" . $looped_trader_id
								 . " and ds.merchant_id>0 ";
								 
					$revqq  = function_mysql_query($sql,__FILE__); 					 
					while ($revww = mysql_fetch_assoc($revqq)) {
								$arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $revww['merchant_id'], $revww['affiliate_id'], $arrDealTypeDefaults);
								$intTotalRevenue  = 0;
								
								foreach ($arrRevenueRanges as $arrRange2) {
									$strRevWhere = 'WHERE rdate BETWEEN "' . $arrRange2['from'] . '" AND "' . $arrRange2['to'] 
												 . '"' . (empty($group_id ? '' : ' AND group_id = ' . $group_id . ' '))
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
								  
									$totalCom           = getCommission($arrRange2['from'], $arrRange2['to'], 1, (empty($group_id ? -1 : $group_id)), $arrDealTypeDefaults, $row);
									
									$clickArray[$clickww['id']]['totalCom'] += $totalCom;
									
									unset($arrRange2, $strRevWhere);
								}
								
								$netRevenue = $intTotalRevenue;
								$clickArray[$clickww['id']]['netRevenue'] += $netRevenue;
								
								
					}
					
						
					$sql = "select * from merchants where producttype = 'Forex' and valid =1";
					$totalqq = function_mysql_query($sql,__FILE__);
					
					while ($merchantww  = mysql_fetch_assoc($totalqq)) {
							$sql = 'SELECT dr.initialftddate,SUM(ds.spread) AS totalSpread, SUM(ds.pnl) AS totalPnl, SUM(ds.turnover) AS totalTO FROM data_stats ds '
									.	' inner join data_reg dr inner join ds.merchant_id = dr.merchant_id and ds.trader_id = dr.merchant_id ' 
									. 'WHERE ds.rdate >= "'.$regDate.'"' . (empty($group_id ? '' : ' AND ds.group_id = ' . $group_id . ' '))
									. ' and ds.trader_id=' . $looped_trader_id
									. (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
									. " and ds.merchant_id = " . $clickww['merchant_id']
									. " and ds.merchant_id >0 "
									 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
									  .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'');
						
							$traderStatsQ = function_mysql_query($sql,__FILE__);
							
							while($ts = mysql_fetch_assoc($traderStatsQ)){
									$spreadAmount = $ts['totalSpread'];
									$volume += $ts['totalTO'];
									
									$clickArray[$clickww['id']]['volume'] += $ts['totalTO'];
									
									$pnl = $ts['totalPnl'];
							}
									
									
							$totalLots  = 0;
														
						
								
							$sql = 'SELECT dr.initialftddate,ds.turnover AS totalTurnOver,ds.trader_id,ds.rdate,ds.affiliate_id,ds.profile_id,ds.banner_id FROM data_stats ds '
							.	' inner join data_reg dr inner join ds.merchant_id = dr.merchant_id and ds.trader_id = dr.merchant_id ' 
							 . 'WHERE  ds.rdate >= "' . $regDate . '"'
							 . ' and ds.trader_id=' . $looped_trader_id
							 . (isset($group_id) && $group_id != '' ? ' AND ds.group_id = ' . $group_id . ' ' : '')
							 . (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')

							 . " and ds.merchant_id >0 "
							 . " and ds.merchant_id = " . $clickww['merchant_id']
							 . (isset($banner_id) && !empty($banner_id) ? ' AND ds.banner_id = "'.$banner_id.'"' :'') 
							   .(!empty($unique_id) ? ' and ds.uid = ' . $unique_id :'');
							
							$traderStatsQ = function_mysql_query($sql,__FILE__);
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
															'initialftddate'    => $ts['initialftddate'],
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
						
						



						 if ($set->deal_pnl == 1) {
						

								$totalPNL  = 0;


								$dealsForAffiliate['pnl'] = 1;
									

									
								$pnlRecordArray=array();
							
								$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
								$pnlRecordArray['merchant_id']  = $clickww['merchant_id'];
								$pnlRecordArray['trader_id']  = (isset($trader_id) ? $trader_id: "");
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

												 
											

												 //$totalPNL = $totalPNL + $pnlamount;
												 $clickArray[$clickww['id']]['pnl'] += $pnlamount;
												
															 
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
										if ($dealsForAffiliate['pnl']>0){

											

											$tmpCom = getCommission($from, $to, 0, $ts['group_id'], $arrDealTypeDefaults, $row);
											// echo 'com: ' . $tmpCom.'<br>';
												
												$clickArray[$clickww['id']]['totalCom'] += $tmpCom;

										}
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
				'.(allowView('af-impr',$deal,'fields') ? '
				<td style="text-align: center;">'.@number_format($data['views'],0).'</td>
				' : '').
				(allowView('af-clck',$deal,'fields') ? '
				<td style="text-align: center;">'.@number_format($data['clicks'],0).'</td>
				' : '').'
				<!--td style="text-align: left;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_id'] .'</a></td>
				 <td style="text-align: left;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_username'] .'</a></td-->
				<td style="text-align: left;">'.$data['traffic_date'] .'</td>
				<td style="text-align: left;">'.ucwords($data['type']).'</td>
				<td style="text-align: left;">'. $data['merchant_name'] .'</td>
				<td style="text-align: left;"><a href="'.$data['banner_url'] . '" target="_blank">'. $data['banner_title'] . ' ('.$data['banner_id'] .')</a></td>
				<td style="text-align: left;">'. $data['profile_id'] .'</td>
				<td style="text-align: left;">'. $listProfiles[$data['profile_id']] .'</td>
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
				'.
									(!$hideDemoAndLeads 
					?
					(allowView('af-lead',$deal,'fields') ? '
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=lead">'.$data['leads'].'</a></td>
				' : '').
				(allowView('af-demo',$deal,'fields') ? '
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=demo">'.$data['demo'].'</a></td>
				' : '')
				: '') . 
				'
				<td style="text-align: left;">'. $data['sale_status'] .'</td>'.
				(allowView('af-real',$deal,'fields') ? '
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=real">'.$data['real'].'</a></td>'
				: '' ) .
				(allowView('af-ftd',$deal,'fields') ? '
				<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=ftd&trader_id='. $data['trader_id'] .'">'.$data['ftd'].'</a></td>
				' : '' ) .
				(allowView('af-ftda',$deal,'fields') ? '<td>'.price($data['ftd_amount']).'</td>' : '')
			.	(allowView('af-tftd',$deal,'fields') ? '<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=totalftd&trader_id='. $data['trader_id'] .'">'.$data['real_ftd'].'</a></td>' : '')
			.	(allowView('af-tftda',$deal,'fields') ? '<td>'.price($data['real_ftd_amount']).'</td>' : '')	
			  . (allowView('af-depo',$deal,'fields') && (true) ? '<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>' : '' )
			  . (allowView('af-depoam',$deal,'fields') && (true) ? '<td>'.price($data['sumDeposits']).'</td>':'')
			. (allowView('af-vlm',$deal,'fields')  ? '<td style="text-align: center;">'.price($data['volume']).'</td>':'')	
				. (allowView('af-bns',$deal,'fields')  ? '<td>'.price($data['bonus']).'</td>':'')
				. (allowView('af-withd',$deal,'fields')  ? '<td>'.price($data['withdrawal']).'</td>':'')
				. (allowView('af-chrgb',$deal,'fields')  ? '<td>'.price($data['chargeback']).'</td>':'')
				. (allowView('af-ntrv',$deal,'fields')  ? '<td style="text-align: center;">'.price($data['netRevenue']).'</td>':'').
				'
				'.($set->deal_pnl==1 ? 
								(allowView('af-pnl',$deal,'fields')  ? '<td style="text-align: center;">'.price($data['pnl']).'</td>':'')
							
							:'')

					. (allowView('af-qftd',$deal,'fields')  ? '
							<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=activeTrader&trader_id='. $data['trader_id'] .'">'.$data['Qftd'].'</a></td>	'
							:'').'
							
							
										
				'./*.'<td>'.price($data['totalCom']).'</td>'.*/'
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
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get" id="testForm">
			<input type="hidden" name="sortBy" id="sortBy" value="" />
			<input type="hidden" name="sortOrder" id="sortOrder" value="" />
			<input type="hidden" name="act" value="clicks" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Unique ID').'</td>
					<td>'.lang('Trader ID').'</td>
					<!--td>'.lang('Affiliate ID').'</td-->
					<td>'.lang('Type').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="unique_id" value="'.$unique_id.'" /></td>
					<td><input type="text" name="trader_id" value="'.$trader_id.'"  onblur="validateMerchant(this)"/></td>
					<!--td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" /></td-->
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="clicks" '.($type == "clicks" ? 'selected' : '').'>'.lang('Clicks').'</option>
						<option value="views" '.($type == "views" ? 'selected' : '').'>'.lang('Views').'</option>
					</select></td>
					<td><input type="button" value="'.lang('View').'" onclick="validateForm()"/></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="javascript:void(0);" class="testcsv"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a href="javascript:void(0);" class="testexcel"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div class="ajaxloader" style="display:none;padding:3px 30px;"><img style="margin-left:10px" src="'.$set->SSLprefix.'images/ajax-loader.gif"></div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle"  class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
			//width 2400
				$tableStr='<table  class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="clicksTbl">
					<thead><tr>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="id">'.lang('ID').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="uid"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="uid">'.lang('UID').'</th>
						
						'. (allowView('af-impr',$deal,'fields') ? '			
						<th style="text-align: center;" class="table-cell header ' . ($sortBy =="views"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"")  .'" data-sort="views">'.lang('Impression').'</th>
						' : '' ) .'
						'. (allowView('af-clck',$deal,'fields') ? '			
						<th style="text-align: center;" class="table-cell header '. ($sortBy =="clicks"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="clicks">'.lang('Click').'</th>
						' : '' ) .'
						<th class="table-cell header  '. ($sortBy =="rdate"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="rdate">'.lang('Date').'</th>
						<th class="table-cell header  '. ($sortBy =="type"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="type">'.lang('Type').'</th>
						<th style="text-align: left;" class="table-cell header  '. ($sortBy =="merchant_name"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="merchant_name">'.lang('Merchant').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="banner_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="banner_id">'.lang('Banner ID').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="profile_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="profile_id">'.lang('Profile ID').'</th>
						<th style="text-align: left;">'.lang('Profile Name').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="param"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="param">'.lang('Param').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="param2"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="param2">'.lang('Param2').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="refer_url"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="refer_url">'.lang('Refer URL').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="country_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="country_id">'.lang('Country').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="ip"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="ip">'.lang('IP').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="platform"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="platform">'.lang('Platform').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="os"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="os">'.lang('Operating System').'</th>
						<th style="text-align: left;"class="table-cell header '. ($sortBy =="osVersion"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="osVersion">'.lang('OS Version').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="browser"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="browser">'.lang('Browser').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="broswerVersion"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="broswerVersion">'.lang('Broswer Version').'</th>
						
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="trader_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="trader_id">'.lang('Trader Id').'</th>
						<th style="text-align: left;" class="table-cell header '. ($sortBy =="trader_alias"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="trader_alias">'.lang('Trader Alias').'</th>
						'.
											(!$hideDemoAndLeads ? 
						(allowView('af-lead',$deal,'fields') ? 					
						'
						<th class="table-cell header">'.lang(ptitle('Lead')).'</th>
						' : '').
						(allowView('af-demo',$deal,'fields') ? '
						<th class="table-cell header">'.lang(ptitle('Demo')).'</th>
						' : '' )
						: '') .
						'
						<th class="table-cell header">'.lang('Sale Status').'</th>
						'.(allowView('af-real',$deal,'fields') ? 
						'<th class="table-cell header">'.lang(ptitle('Accounts')).'</th>
						' : '' ) .
						(allowView('af-ftd',$deal,'fields') ? '
						<th class="table-cell header">'.lang('FTD').'</th>
						' : '')
						.(allowView('af-ftda',$deal,'fields') ?
						'<th class="table-cell header">'.lang('FTD Amount').'</th>
						' : '') 
						.(allowView('af-tftd',$deal,'fields') ? 
						'<th class="table-cell">'.lang('RAW FTD').'</th>
						' : '').
						(allowView('af-tftda',$deal,'fields') ? '
						<th class="table-cell">'.lang('RAW FTD Amount').'</th>
						' : '').
						( (allowView('af-depo',$deal,'fields')) ? '
						<th class="table-cell">'.lang('Total Deposits').'</th>
						':'')
						. ( (allowView('af-depoam',$deal,'fields')) ? '
						<th>'.lang('Deposit Amount').'</th>
						': '').
						(allowView('af-vlm',$deal,'fields') ?  '<th class="table-cell">'.lang('Volume').'</th>' : '').
						(allowView('af-bns',$deal,'fields') ?  '<th class="table-cell">'.lang('Bonus Amount').'</th>' : '').
						(allowView('af-withd',$deal,'fields') ?  '<th class="table-cell">'.lang('Withdrawal Amount').'</th>' : '').
						(allowView('af-chrgb',$deal,'fields') ?  '<th class="table-cell">'.lang('ChargeBack Amount').'</th>' : '').
						(allowView('af-ntrv',$deal,'fields') ?  '<th class="table-cell">'.lang(ptitle('Net Revenue')).'</th>' : '').
						($set->deal_pnl==1 ? 
						(allowView('af-pnl',$deal,'fields')  ? '<th  class="table-cell"style="text-align: center;">'.lang(ptitle('PNL')).'</th>':'')
						:'').'
						'.(allowView('af-qftd',$deal,'fields')  ? '
						<th class="table-cell">'.lang('Active Traders').'</th>':'').'
						
						'./*.'<th class="table-cell">'.lang('Commission').'</th>'.*/'
					</tr></thead><!--<tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						'. (allowView('af-impr',$deal,'fields') ? '			
						<th style="text-align: center;">'.$totalImpressions.'</th>
						' : '' ) .'
						'. (allowView('af-clck',$deal,'fields') ? '			
						<th style="text-align: center;">'.$totalClicks.'</th>
						' : '' ) .'
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
						<th></th>'.
						(!$hideDemoAndLeads ? 
						(allowView('af-lead',$deal,'fields') ? 					
						'
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalLeadsAccounts.'</a></th>
						': '').
						(allowView('af-demo',$deal,'fields') ? '
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						 ': '' )
						 : '' )
						 
						.(allowView('af-real',$deal,'fields') ? '
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						' : '' ).
						'<th></th>'.
						(allowView('af-ftd',$deal,'fields') ? '
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						' : '' ).
						(allowView('af-ftda',$deal,'fields') ?
						'<th style="text-align: left;">'.price($totalFTDAmount).'</th>'
						: '') .
						(allowView('af-tftd',$deal,'fields') ? '
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						' : '').
						(allowView('af-tftda',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						' : '').
						( (allowView('af-depo',$deal,'fields')) ? '
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						':'').
						( (allowView('af-depoam',$deal,'fields')) ? '
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						': '').
						(allowView('af-vlm',$deal,'fields') ?  '<th>'.price($totalVolume).'</th>' : '').
						(allowView('af-bns',$deal,'fields') ?  '<th>'.price($totalBonusAmount).'</th>' : '').
						(allowView('af-withd',$deal,'fields') ?  '<th>'.price($totalWithdrawalAmount).'</th>' : '').
						(allowView('af-chrgb',$deal,'fields') ?  '<th>'.price($totalChargeBackAmount).'</th>' : '').
						(allowView('af-ntrv',$deal,'fields') ?  '<th>'.price($totalNetRevenue).'</th>' : '').
						
						($set->deal_pnl==1 ? 
							(allowView('af-pnl',$deal,'fields')  ? '<th style="text-align: center;">'.price($totalTotalPnl).'</th>':'')
							:'').'
						'./*.'<th style="text-align: left;">'.price($totalComs).'</th>'.*/'
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
											
											saveReportToMyFav(name, \'clicks\',user,level,type);
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
						
						recs = "'. $set->total_records.'";
						if(recs > 1000){
								
								$.prompt("'.lang('There are so many records. It will take sometime to export the data. Do you still want to export?').'", {
														top:200,
														title: "'. lang('Export CSV?') .'",
														buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
														submit: function(e,v,m,f){
															$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"'.$set->SSLprefix.'images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>'.lang('Please be patient. Exporting data can take few minutes...') .'</span>");
															if(v){
																	$.ajax({
																		  method: "POST",
																		  url: "'.$set->SSLprefix.'ajax/clicksReportExport.php",
																		  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'",format:"csv",globalWhere:"'. $globalWhere .'"}
																		})
																		  .done(function( filepath ) {
																				filedata = $.parseJSON(filepath);
																				window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
																				if(filedata.status == \'big\')
																				{
																						$.prompt("'.lang('File has been downloaded successfully. Because of limitation of excel, downloaded file contains only 65536 rows.').'", {
																								top:200,
																								title: "Export CSV",
																								buttons: { "'.lang('OK').'": true}
																						});
																				}
																			  	$(".ajaxloader").hide();
																		  }).fail(function() { 
																					window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename='. $_SERVER['DOCUMENT_ROOT'] ."/ajax/" . $filename.'.csv&unlinkfile=1";
																					$(".ajaxloader").hide();
																		  });
															}
															else{
																	$(".ajaxloader").hide();
															}
														}
								});
						}
						else{
								$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"'.$set->SSLprefix.'images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>'.lang('Please be patient. Exporting data can take few minutes...') .'</span>");
								$.ajax({
									  method: "POST",
									  url: "'.$set->SSLprefix.'ajax/clicksReportExport.php",
									  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'",format:"csv",globalWhere:"'. $globalWhere .'"}
									})
									  .done(function( filepath ) {
												filedata = $.parseJSON(filepath);
												window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
												$(".ajaxloader").hide();
										  
									  }).fail(function() { 
												window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename='. $set->webAddress ."ajax/" . $filename.'.csv&unlinkfile=1";
												$(".ajaxloader").hide();
									  });
							}
					});
					
					$(".testexcel").on("click",function(){
						recs = "'. $set->total_records.'";
						if(recs > 1000){
								$.prompt("'.lang('There are so many records. It will take sometime to export the data. Do you still want to export?').'", {
														top:200,
														title: "'. lang('Export Excel?') .'",
														buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
														submit: function(e,v,m,f){
															if(v){
																$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"'.$set->SSLprefix.'images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>'.lang('Please be patient. Exporting data can take few minutes...') .'</span>");
																$.ajax({
																	  method: "POST",
																	  url: "'.$set->SSLprefix.'ajax/clicksReportExport.php",
																	  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'",format:"xlsx",globalWhere:"'. $globalWhere .'"}
																	})
																	  .done(function( filepath ) {
																				filedata = $.parseJSON(filepath);
																				
																				if(filedata.status == "big")
																				{
																						$.prompt("'.lang('File has been downloaded successfully. Because of limitation of excel, downloaded file contains only 65536 rows.').'", {
																								top:200,
																								title: "Export Excel",
																								buttons: { "'.lang('OK').'": true}
																						});
																				}
																				window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
																			$(".ajaxloader").hide();
																		  
																	  }).fail(function() { 
																					window.location.href = "common/downloadFile.php?filename='. $_SERVER['DOCUMENT_ROOT'] ."/ajax/" . $filename.'.xls&unlinkfile=1";
																					$(".ajaxloader").hide();
																		  });
															}
															else{
																$(".ajaxloader").hide();
															}
														}
								});
						}
						else{
								$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"'.$set->SSLprefix.'images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>'.lang('Please be patient. Exporting data can take few minutes...') .'</span>");
								$.ajax({
									  method: "POST",
									  url: "'.$set->SSLprefix.'ajax/clicksReportExport.php",
									  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'",format:"xlsx",globalWhere:"'. $globalWhere .'"}
									})
									  .done(function( filepath ) {
												filedata = $.parseJSON(filepath);
												window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
												$(".ajaxloader").hide();
										  
									  }).fail(function() { 
												window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename='. $set->webAddress ."ajax/" . $filename.'.csv&unlinkfile=1";
												$(".ajaxloader").hide();
									  });
						}
					});
					
					$(".header").on("click",function(){
					var sortBy = $(this).data("sort");
					$("#sortBy").val(sortBy);
					
					if($(this).hasClass("headerSortDown"))
					{
						$(this).removeClass("headerSortDown").addClass("headerSortUp");
						$("#sortOrder").val("ASC");
					}
					else if($(this).hasClass("headerSortUp"))
					{
						$(this).removeClass("headerSortUp").addClass("headerSortDown");
						$("#sortOrder").val("DESC");
					}
					else
					{
						$(this).addClass("headerSortDown");
						$("#sortOrder").val("ASC");
					}
					$("#testForm").submit();
				});	
				
			
				
					});
				
					</script>
					
				';
		$tableStr .= getSingleSelectedMerchant();
		$tableStr .= getValidateTraderMerchantScript('testForm');	
		//excelExporter($tableStr, 'Clicks');
		$set->content.=$tableStr.'</div>'.getURLPager();
		
		//MODAL
		$myReport = lang("Clicks");
		include "common/ReportFieldsModal.php";
		
		theme();


?>