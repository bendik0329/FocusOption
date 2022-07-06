<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];

if(!defined('DirectBrowse')) {
	$path = "http://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

if($userlevel == "manager")
$globalWhere = "group_id = '".$set->userInfo['group_id']."' AND ";
else
$globalWhere  = '';


$pageTitle = lang(ptitle('Trader Report'));
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$userlevel.'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
		$set->content .= '<script type="text/javascript" src="../../js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="../../js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="../../js/tableExport/jquery.base64.js"></script>';
		$filename = "Trader_data_" . date('YmdHis');
		
		
if($userlevel == 'manager')
 $group_id = $set->userInfo['group_id'];

                $l = 0;
                $arrResultSet = [];
                $ftdExist = [];
                $totalFTD = 0;
                $totalTotalDeposit = 0;
                $totalDepositAmount = 0;
                $totalVolumeAmount = 0;
                $totalBonusAmount = 0;
                $totalWithdrawalAmount = 0;
                $totalChargeBackAmount = 0;
                $totalNetRevenue = 0;
                $totalTrades = 0;
                $totalTotalCom  = 0;
                $arrTradersPerMerchants = [];
                $arrAllTraders = [];
                
                $intTmpMerchantId = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;
                $strWhereMerchantId = isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id . ' ' : '';
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
				 
				
                $resourceWallets = function_mysql_query($sql,__FILE__);
                
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
				
	$affiliatesRowsArray = array();
				$creativesRowsArray = array();
				
				
				
				output_memory('','A1','');
				
                $sql = "SELECT * FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                $resourceMerchanrs = function_mysql_query($sql,__FILE__);
                
                while ($arrMerchant = mysql_fetch_assoc($resourceMerchanrs)) {
					
					
                    $int_merchant_id = empty($intTmpMerchantId) ? $arrMerchant['id'] : $intTmpMerchantId;
                    $where = ' AND merchant_id = ' . $int_merchant_id;
                    
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($group_id) $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
                    if ($param){
						$param =  trim($param);
					$where .= " AND freeParam='".$param."' ";
					}
                    
                    if ($param2) $where .= " AND freeParam2='".$param2."' ";
                    
					if ($email && $set->ShowEmailsOnTraderReportForAdmin ){
						$email = trim($email);
						$where .= " AND lower(email) like '%".strtolower($email)."%' ";
					}
                    
					output_memory('','A2','');
					
					if ( ($trader_alias || ($email && $set->ShowEmailsOnTraderReportForAdmin))) {
						if ($trader_alias){
							
							if(preg_match('/^[-a-zA-Z0-9 ._]+$/', $trader_alias)){
							// die ('2');
							$qry = "select trader_id from data_reg  where  merchant_id=".$arrMerchant['id']." and 
							
							(lower(trader_alias) like ('%". mysql_real_escape_string(strtolower($trader_alias))."%') )";
							
// echo ($qry).'<br>';
							}
							else
							$qry = "
						
							 select trader_id from (select  convert(group_concat(name separator '') using 'utf8') as alias,trader_id,rdate,merchant_id from(SELECT trader_id,rdate,merchant_id,ID,SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1) value, char(SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1) USING 'utf32') name
  FROM data_reg t CROSS JOIN 
  (
   SELECT a.N + b.N * 10 + 1 n
     FROM 
    (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
   ,(SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
    ORDER BY n 
   ) n
 WHERE  n.n <= 1 + (LENGTH(trim(leading ',' from replace(t.trader_alias,'&#',','))) - LENGTH(REPLACE(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', '')))
                                   ) as t1 group by id) final where  alias = '".strtolower(trim($trader_alias))."' " . $where . "  AND rdate between '" . $from . "' and '" . $to    . "' 
							
							";
							
							/* // shalini please fix this query to work properly instead of the query ^^^ */
						/* 	$qry = "select trader_id from data_reg  where  merchant_id=".$arrMerchant['id']." and 
							(
							(lower(trader_alias) like ('%". mysql_real_escape_string(strtolower($trader_alias))."%') )
							||
							(
							
							select trader_id from (select  convert(group_concat(name separator '') using 'utf8') as alias,trader_id,rdate,merchant_id from(SELECT trader_id,rdate,merchant_id,ID,SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1) value, char(SUBSTRING_INDEX(SUBSTRING_INDEX(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', n.n), ',', -1)) name
  FROM data_reg t CROSS JOIN 
  (
   SELECT a.N + b.N * 10 + 1 n
     FROM 
    (SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a
   ,(SELECT 0 AS N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b
    ORDER BY n 
   ) n
 WHERE  n.n <= 1 + (LENGTH(trim(leading ',' from replace(t.trader_alias,'&#',','))) - LENGTH(REPLACE(trim(leading ',' from replace(t.trader_alias,'&#',',')), ',', '')))
                                   ) as t1 group by id) final where  rdate between '" . $from . "' and '" . $to    . "' and alias = '".strtolower(trim($trader_alias))."' " . $where . " 
 

 

							
							)
							)
							";
							 */
							 
					
							 
							
						}
						else if ($email && $set->ShowEmailsOnTraderReportForAdmin)
							$qry = "select trader_id from data_reg  where  merchant_id=".$arrMerchant['id']." AND lower(email) like '%".mysql_real_escape_string(strtolower($email))."%' ";
					

                        $row = function_mysql_query($qry,__FILE__);
						
						while ($arrTraders = mysql_fetch_assoc($row)) {
								$arrAllTraders[] = $arrTraders['trader_id'];
                                unset($arrTraders);
						}
					
					
							
							
						/* 
                         if (!empty($row['trader_id'])) {
                            $trader_id = $row['trader_id']; 
                            
                            if ($trader_id) {
                                $where .= " AND trader_id='".$trader_id."' ";
                            }
                            
                            if (empty($trader_id)) {
                                $trader_id = $row['trader_id'];
                            }
                        }
						else {
                              // $where .= " AND trader_alias='".$trader_alias."' ";
                              // $where .= " AND email='".$email."' ";
							
							
						} */
                    }
		    		
					if (empty($arrAllTraders) && !empty($trader_alias)){
						
						$arrAllTraders[]=0;	
						
					}
				
				
                    if ($country_id) {
                        $where .= " AND country='".$country_id."' ";
                    }
                    
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
			
			
			output_memory('','A3','');
			
			// all demos and frozen accounts.
			$qry = "select id,status from data_reg where merchant_id>0 and merchant_id = " . $int_merchant_id . " AND (status = 'frozen' or type= 'demo') ";
			$invalidResources = function_mysql_query($qry,__FILE__);
			$invalidTraders = array();
			$frozenTraders = array();
			while ($invalidRow = mysql_fetch_assoc($invalidResources)) {
				$invalidTraders[] = $invalidRow['id'];
				if ($invalidRow['status']=='frozen') {
					$frozenTraders[] = $invalidRow['id'];
				}
			}
		
			$traders = implode(",",$arrAllTraders);
			
			$this_group_id = $group_id && $group_id>-1 ? $group_id : -1;
			
			
			if ($type == 'ftd' || $type == 'totalftd') {
				output_memory('','B1','');
				// foreach($arrAllTraders as $key=>$traderID)
				{
				
				            $arrTotalFtds = getTotalFtds(
                                $from, $to, $affiliate_id, $arrMerchant['id'], $arrMerchant['wallet_id'], 
                                $this_group_id, $banner_id, $profile_id, '', $traderID
                            );
						 	
					 	  if ($traders!='') {
						// var_dump($traders);
							 $arrTotalFtds= array();
							 $exp = explode(',',$traders);
							 foreach ($exp as $ex) {
								 if ($ex==0)
									 continue;
								 // echo $ex.'<br>';
								 
								 $tmp_arrTotalFtds = getTotalFtds(
                                $from, $to, $affiliate_id, $arrMerchant['id'], $arrMerchant['wallet_id'], 
                                $this_group_id, $banner_id, $profile_id, '', $ex
                            );
								
								 $arrTotalFtds = array_merge($arrTotalFtds,$tmp_arrTotalFtds);
							 }
							 
						 }
						 else  
							   $arrTotalFtds = getTotalFtds(
                                $from, $to, $affiliate_id, $arrMerchant['id'], $arrMerchant['wallet_id'], 
                                $this_group_id, $banner_id, $profile_id, '', $trader_id
                            );
                           
                            if ($type == 'ftd') {
                                foreach ($arrTotalFtds as $arrRes) {

						
								/* $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ; */
									
									if ($earliestTimeForNetRev>$arrRes['rdate'])
								$earliestTimeForNetRev = $arrRes['rdate'];
							
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($int_merchant_id, $arrRes, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);
				// var_dump($arrRes);
				// die ('mi44');
                                    if ($beforeNewFTD != $ftd ) {
                                    // die ('gregr');
                                        $firstDeposit           = $arrRes;
                                        $ftdAmount              = $firstDeposit['amount'];
                                        $arrRes['isFTD']        = true;
                                        $totalCom               = getCommission($arrRes['rdate'], $arrRes['rdate'], 0, $group_id, $arrDealTypeDefaults, $arrRes);
										
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after FTD " . $totalCom . "<br/>";
									   } 
									   
			   
                                        $arrRes['firstDeposit'] = $firstDeposit;
                                        $arrRes['ftdAmount']    = $ftdAmount;
                                        $arrRes['totalCom']     = $totalCom;
                                        $arrResultSet[]         = $arrRes;
                                    }
                                    unset($arrRes);
                                }
                             
                            } elseif ($type == 'totalftd') {
								

                                foreach ($arrTotalFtds as $arrRes) {
										/* $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrTotalFtds[$ftdCount] ;
									 */
									
									
									if ($earliestTimeForNetRev>$arrRes['rdate'])
								$earliestTimeForNetRev = $arrRes['rdate'];
							
							
                                    $beforeNewFTD = $ftd;
                                    getFtdByDealType($int_merchant_id, $arrRes, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);
                                    
                                    $firstDeposit           = $arrRes;
                                    $ftdAmount              = $firstDeposit['amount'];
                                    $arrRes['isFTD']        = true;
                                    $totalCom               = getCommission($arrRes['rdate'], $arrRes['rdate'], 0, $group_id, $arrDealTypeDefaults, $arrRes);
									
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after total FTD " . $totalCom . "<br/>";
									   } 
									   
                                    $arrRes['firstDeposit'] = $firstDeposit;
                                    $arrRes['ftdAmount']    = $ftdAmount;
                                    $arrRes['totalCom']     = $totalCom;
                                    $arrResultSet[]         = $arrRes;
                                    unset($arrRes);
                                }
                            }
					}
			} elseif ($type == 'deposit') {
				
				output_memory('','B2','');
                            $where = str_replace('merchant_id', 'ds.merchant_id', $where);
                            $where = str_replace('trader_id', 'ds.trader_id', $where);
                            $where = str_replace('group_id', 'ds.group_id', $where);
                            $where = str_replace('affiliate_id', 'ds.affiliate_id', $where);
                            $where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            $where = str_replace('email', 'dr.email', $where);
                            
                        /*     $sql = "SELECT ds.* FROM data_sales AS ds
                                    INNER JOIN data_reg AS dr ON dr.trader_id = ds.trader_id AND dr.merchant_id = ds.merchant_id AND dr.status <> 'frozen' AND dr.type <> 'demo' 
                                    WHERE " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'deposit' "
                                            . $where
                                    . " ORDER BY ds.rdate ASC;";
                             */
							 
							$sql = "SELECT ds.* FROM data_sales AS ds
                                  
                                    WHERE ds.merchant_id and 2=2 and " . $globalWhere 
                                            . "  ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'deposit' "
                                            . $where
											. ($traders!=''? " AND trader_id in (". $traders .")  " : "" ) 
											 . " and ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                    . "  ORDER BY ds.rdate ASC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrDeposit = mysql_fetch_assoc($resource)) {
								
								
								if (!in_array($arrDeposit['trader_id'], $invalidTraders)) {
                                $arrResultSet[] = $arrDeposit;
                                unset($arrDeposit);
								}
                            }
                            
                        } elseif ($type == 'revenue') {
							
							output_memory('','B3','');
							
                            $where = str_replace('merchant_id', 'ds.merchant_id', $where);
                            $where = str_replace('trader_id', 'ds.trader_id', $where);
                            $where = str_replace('group_id', 'ds.group_id', $where);
                            $where = str_replace('affiliate_id', 'ds.affiliate_id', $where);
							$where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            
/*                             $sql = "SELECT ds.* FROM data_sales AS ds
                                    INNER JOIN data_reg AS dr ON dr.trader_id = ds.trader_id AND dr.merchant_id = ds.merchant_id AND dr.status <> 'frozen' AND dr.type <> 'demo' 
                                    WHERE " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'revenue' "
                                            . $where
                                    . " GROUP BY ds.trader_id "
                                    . " ORDER BY ds.rdate ASC;";
 */                            
							
							$sql = "SELECT ds.* FROM data_sales AS ds
                                    WHERE ds.merchant_id>0 and 3=3 and " . $globalWhere 
                                            . "  ds.merchant_id = " . $int_merchant_id
                                            . " AND ds.type = 'revenue' "
                                            . $where
											. ($traders!=''? " AND trader_id in (". $traders .")  " : "" ) 
											. " and ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                    . "  GROUP BY ds.trader_id "
                                    . " ORDER BY ds.rdate ASC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrRevenue = mysql_fetch_assoc($resource)) {
								if (!in_array($arrDeposit['trader_id'], $invalidTraders)) {
                                $arrResultSet[] = $arrRevenue;
                                unset($arrRevenue);
								}
                            }
                            
                        } elseif ($type == 'frozen') {
							
							output_memory('','B4','');
							                            							$data_reg_from_fields = "
							data_reg.merchant_id,
data_reg.trader_id,
data_reg.id,
data_reg.lastTimeActive,
data_reg.initialftddate,
data_reg.group_id,
data_reg.affiliate_id,
data_reg.profile_id,
data_reg.freeParam,
data_reg.freeParam2,
data_reg.banner_id,
data_reg.status,
data_reg.type,
data_reg.trader_alias,
data_reg.email,
data_reg.country,
data_reg.saleStatus,
data_reg.rdate,
data_reg.lastSaleNote,
data_reg.lastSaleNoteDate
";


							
                            $sql = "SELECT " . $data_reg_from_fields ."  ". ($displayForex==1? ", (select count(*) from data_reg dg where dg.merchant_id>0 and dg.trader_id=data_reg.trader_id group by dg.trader_id) as sub_trader_count":"")." FROM data_reg "
                                    . "WHERE merchant_id>0 and 1 = 1 " . $where . " AND " . $globalWhere  . "  status = 'frozen' "
									. ($traders!=''? " AND trader_id in (". $traders .")  " : "" ) 
									. " and rdate BETWEEN '" . $from . "' AND '" . $to . "' " 
                                    . " GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                            
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                            
                        } elseif ($type == 'demo') {
							
							output_memory('','B5','');
							
							// die ('gerger');
							
                            							$data_reg_from_fields = "
							data_reg.merchant_id,
data_reg.trader_id,
data_reg.id,
data_reg.lastTimeActive,
data_reg.initialftddate,
data_reg.group_id,
data_reg.affiliate_id,
data_reg.profile_id,
data_reg.freeParam,
data_reg.freeParam2,
data_reg.banner_id,
data_reg.status,
data_reg.type,
data_reg.trader_alias,
data_reg.email,
data_reg.country,
data_reg.saleStatus,
data_reg.rdate,
data_reg.lastSaleNote,
data_reg.lastSaleNoteDate
";


							
                            $sql = "SELECT " . $data_reg_from_fields ." ". ($displayForex==1? ", (select count(*) from data_reg dg where dg.merchant_id>0 and dg.trader_id=data_reg.trader_id group by dg.trader_id) as sub_trader_count":"")." FROM data_reg "
                                    . "WHERE merchant_id>0 and 1 = 1 " . $where . " AND " . $globalWhere . "  status <> 'frozen'  AND type ='demo' "
									. ($traders!=''? " AND trader_id in (". $traders .")  " : "" ) 
									. " and rdate BETWEEN '" . $from . "' AND '" . $to . "'" 
                                    . " GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                        // die ($sql);    
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                        } elseif ($type == 'lead') {
							// die ('gerger');
							output_memory('','B6','');
							
							$data_reg_from_fields = "
							data_reg.merchant_id,
data_reg.trader_id,
data_reg.id,
data_reg.lastTimeActive,
data_reg.initialftddate,
data_reg.group_id,
data_reg.affiliate_id,
data_reg.profile_id,
data_reg.freeParam,
data_reg.freeParam2,
data_reg.banner_id,
data_reg.status,
data_reg.type,
data_reg.trader_alias,
data_reg.email,
data_reg.country,
data_reg.saleStatus,
data_reg.rdate,
data_reg.lastSaleNote,
data_reg.lastSaleNoteDate
";


							
                            $sql = "SELECT " . $data_reg_from_fields ."
									". ($displayForex==1? ", (select count(*) from data_reg dg where dg.merchant_id>0 and dg.trader_id=data_reg.trader_id group by dg.trader_id) as sub_trader_count":"")." FROM data_reg "
                                    . "WHERE merchant_id>0 and 1 = 1 " . $where . " AND " . $globalWhere . "  status <> 'frozen'  AND type ='lead' "
									. ($traders!=''? " AND trader_id in (". $traders .")  " : "" ) 
									. " and rdate BETWEEN '" . $from . "' AND '" . $to . "'"
                                    . " GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                            // die ($sql);
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                        
						} elseif ($type == 'allaccounts') {

						output_memory('','B7','');
                            							$data_reg_from_fields = "
							data_reg.merchant_id,
data_reg.trader_id,
data_reg.id,
data_reg.lastTimeActive,
data_reg.initialftddate,
data_reg.group_id,
data_reg.affiliate_id,
data_reg.profile_id,
data_reg.freeParam,
data_reg.freeParam2,
data_reg.banner_id,
data_reg.status,
data_reg.type,
data_reg.trader_alias,
data_reg.email,
data_reg.country,
data_reg.saleStatus,
data_reg.rdate,
data_reg.lastSaleNote,
data_reg.lastSaleNoteDate
";


							
                            $sql = "SELECT " . $data_reg_from_fields ." 
							". ($displayForex==1? ", (select count(*) from data_reg dg where dg.merchant_id>0 and dg.trader_id=data_reg.trader_id group by dg.trader_id) as sub_trader_count":"")." FROM data_reg "
                                    . "WHERE merchant_id>0 and 1 = 1 " . $where . " AND " . $globalWhere 
									. (!empty($traders)? " trader_id in (". $traders .") AND " : "" ) 
									. " rdate BETWEEN '" . $from . "' AND '" . $to . "'"
									. ($traders!=''? " AND trader_id in (". $traders .")  " : "" ) 
                                    . " GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
                             // die ($sql);
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                     
						
						} else {
							
							output_memory('','B8','');
							                            							$data_reg_from_fields = "
							data_reg.merchant_id,
data_reg.trader_id,
data_reg.id,
data_reg.lastTimeActive,
data_reg.initialftddate,
data_reg.group_id,
data_reg.affiliate_id,
data_reg.profile_id,
data_reg.freeParam,
data_reg.freeParam2,
data_reg.banner_id,
data_reg.status,
data_reg.type,
data_reg.trader_alias,
data_reg.email,
data_reg.country,
data_reg.saleStatus,
data_reg.rdate,
data_reg.lastSaleNote,
data_reg.lastSaleNoteDate
";


							
                            $sql = "SELECT " . $data_reg_from_fields ."  ". ($displayForex==1? ", (select count(*) from data_reg dg where dg.merchant_id>0 and dg.trader_id=data_reg.trader_id group by dg.trader_id) as sub_trader_count":"")." FROM data_reg  "
                                    . "WHERE merchant_id>0 and 1 = 1 " . $where . " AND " . $globalWhere . "  status <> 'frozen' and type='real' "
									. (!empty($traders)? " AND trader_id in (". $traders .") AND " : "" ) 
									. " and rdate BETWEEN '" . $from . "' AND '" . $to . "'" 
                                    . " GROUP BY trader_id "
                                    . "ORDER BY id DESC;";
									// die($sql);
                            $resource = function_mysql_query($sql,__FILE__);
                            
                            while ($arrReg = mysql_fetch_assoc($resource)) {
                                $arrResultSet[] = $arrReg;
                                unset($arrReg);
                            }
                        }
                        
                        
                        unset($arrMerchant);
                } // END of "merchants" loop.
				
				
				
				output_memory('','C1','');
					// $sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
					
                    $merchantsArray = array();
					/* $displayForex = 0; */
					$merchantsAr = getMerchants(0,1);
					
					// $mer_rsc = function_mysql_query($sql,__FILE__);
					// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
					foreach ($merchantsAr as $arrMerchant) {
						// var_dump($arrMerchant);
						// echo '<br>';
						
						
						/* if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1; */
					
						$merchantsArray[$arrMerchant['id']] = $arrMerchant;
					}
					
			// echo $sql . '<Br>';
			// die('dis: ' . $displayForex);
					$tradersProccessedForLots= array();
					$tradersProccessedForPNL= array();
					$PNLPerformanceAgregationArray= array();
               
			   /* $size = sizeOf($arrResultSet);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrRes = $arrResultSet[$ftdCount] ;
									 */
$netDepositTransactions = array();


				$allTradersIDs=array();
			   foreach ($arrResultSet as $arrRes) {
				   $allTradersIDs[]=$arrRes['trader_id'];
				}
			   
			   
// var_dump($arrResultSet);
// die('22222222222222222222222222222222222222222222');
$acountries = getLongCountries('sales');

output_memory('','C2','');

			   foreach ($arrResultSet as $arrRes) {
				
				
				output_memory('','D1','');
				
					$traderIDForCheck = $arrRes['trader_id']=="2367676" ? true : false;
				
					// die();
					//old nethod
/* 
					$sql = 'SELECT * FROM merchants WHERE id = ' . $arrRes['merchant_id'] . ' AND valid = 1 LIMIT 0, 1;';
					$arrMerchant = mysql_fetch_assoc(function_mysql_query($sql,__FILE__)); 
					*/
                    
					$arrMerchant = $merchantsArray[$arrRes['merchant_id']];
					// var_dump($arrMerchant);
					// die();
					
					
					
					//HACK FIX - TEMPORARY - NEED TO BE TESTED	!!!
					// $ftdAmount = empty($arrRes['ftdAmount']) ? $ftdAmount : $arrRes['ftdAmount'];
					$ftdAmount = 0;
					// $volumeAmount = 0;
					// $withdrawalAmount = 0;
					
					
                    $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    $totalCom = 0;
                    $int_merchant_id = $arrRes['merchant_id'];
                    
                    
                    if ($type == 'ftd' || $type == 'totalftd') {
                        $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    } else {
                        $firstDeposit = [];
                    }
                    
                    if (
                        $type == 'ftd' || 
                        $type == 'totalftd' || 
                        $type == 'deposit' || 
                        $type == 'revenue'
                    ) {
						
						output_memory('','E1','');
                        $ftd = $totalTraders = $depositAmount = $total_deposits = $volumeAmount = 0;
                        $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                        $spreadAmount = $pnl = 0;
			
                        $strTmpWhere = '';
                        $intTmpTraderId = empty($trader_id) ? $arrRes['trader_id'] : $trader_id;  
                        $intTmpGroupId = empty($group_id) ? $arrRes['group_id'] : $group_id;
                        $intTmpMerchantId = empty($int_merchant_id) ? $arrRes['merchant_id'] : $int_merchant_id;
                        $strTmpWhere .= empty($intTmpTraderId) ? '' : ' AND trader_id = ' . $intTmpTraderId . ' ';
                        $strTmpWhere .= empty($intTmpGroupId) ? '' : ' AND group_id = ' . $intTmpGroupId . ' ';
                        $strTmpWhere .= empty($intTmpMerchantId) ? '' : ' AND merchant_id = ' . $intTmpMerchantId . ' ';
                        $totalCom = empty($arrRes['totalCom']) ? $totalCom : $arrRes['totalCom'];
                        
                        $sql = "SELECT * FROM data_reg WHERE 1 = 1 AND merchant_id>0 and status <> 'frozen' " . $strTmpWhere . " LIMIT 0, 1;";
                      // die($sql);
                        $traderInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
						
                    
						
						
                        $arrRes['trader_alias'] = $traderInfo['trader_alias'];
                        $arrRes['email'] = $traderInfo['email'];
                        $arrRes['country']      = $traderInfo['country'];
                        $arrRes['rdate']        = $type != 'deposit' ? $traderInfo['rdate'] : $arrRes['rdate'];
                        $arrRes['orgType']      = $arrRes['type'];
                        $arrRes['salesType']    = $arrRes['type'];
                        $arrRes['type']         = $traderInfo['type'];
                        $arrRes['banner_id']    = $traderInfo['banner_id'];
                        $arrRes['status']       = $traderInfo['status'];
                        $arrRes['profile_id']   = $traderInfo['profile_id'];
                        $arrRes['freeParam']    = $traderInfo['freeParam'];
                        $arrRes['freeParam2']    = $traderInfo['freeParam2'];
                        $arrRes['saleStatus']   = $traderInfo['saleStatus'];
                        $arrRes['initialftddate']   = $traderInfo['initialftddate'];
                        $arrRes['lastTimeActive']   = $traderInfo['lastTimeActive'];
                        unset($intTmpTraderId, $intTmpGroupId, $strTmpWhere, $intTmpMerchantId);
                    }
                    
                    
                    $depositAmount = 0;
					
					output_memory('','F1','');
					
					  // BANNER info retrieval.
					$bannerInfo = getCreativeInfo($arrRes['banner_id']);
								
								
                    if ($type != 'deposit' || true) { //hack by nir
					
					
					
                   			$sql = "SELECT ds.id,ds.affiliate_id,ds.tranz_id,ds.trader_id,ds.merchant_id,ds.amount,ds.rdate,ds.status,ds.type as data_sales_type FROM data_sales AS ds
                                WHERE 4=4 
								and ds.merchant_id = " . $int_merchant_id . " 
                                    AND ds.affiliate_id =" .  $arrRes['affiliate_id'] . " and ds.type<>'pnl' 
								and ds.merchant_id>0 and 4=4 and " . $globalWhere . " ds.trader_id = " . $arrRes['trader_id'] 
                                        ;
								

						//if (isset($qa)) echo 'qa: ', $sql, '<hr>';/////////////////////////////////////////////
						// die ($sql);
                        $resource = function_mysql_query($sql,__FILE__);
                        $total_deposits = 0;

                        while ($arrAmount = mysql_fetch_assoc($resource)) {
							
							if (in_array($arrAmount['trader_id'], $frozenTraders)) {
								// die ('greger222');
								continue;
								
							}

					
	if ($arrAmount['data_sales_type'] == "chargeback" ||
								$arrAmount['data_sales_type'] == "withdrawal" ||
								$arrAmount['data_sales_type'] == "deposit" ||
								$arrAmount['data_sales_type'] == "bonus" ) {
								
								$tranrow['id'] = $arrAmount['id'];
								$tranrow['affiliate_id'] = $arrAmount['affiliate_id'];
								$tranrow['tranz_id'] = $arrAmount['tranz_id'];
								$tranrow['trader_id'] = $arrAmount['trader_id'];
								$tranrow['merchant_id'] = $arrAmount['merchant_id'];
								$tranrow['amount'] = $arrAmount['amount'];
								$tranrow['rdate'] = $arrAmount['rdate'];
								$tranrow['type'] = $arrAmount['data_sales_type'];
								$tranrow['status'] = $arrAmount['status'];
								if(isset($arrRes['initialftddate'])) 
								$tranrow['initialftddate'] = $arrRes['initialftddate'];
								
							
								if (empty($netDepositTransactions[$tranrow['merchant_id'].'-'.$tranrow['tranz_id']]))
								$netDepositTransactions[$tranrow['merchant_id'].'-'.$tranrow['tranz_id']] = array($tranrow);
						
								}
							
							
								
								
								if (strtolower($arrAmount['type'])=='deposit')								
                            $arrRes['tranz_id'] = $arrAmount['tranz_id'];
						
						// var_dump($ftdAmount);
						// die();
							if ((strtolower($type)=='ftd' || strtolower($type)=='totalftd')){// && isset($ftdAmount['amount']) && $ftdAmount['amount']>0){
								
								//$firstDeposit['rdate'] = $ftdAmount['rdate'];
						//		$firstDeposit['id'] = $arrAmftdAmountount['id'];
									// var_dump($ftdAmount);
									// die();
									$ftdAmount = $firstDeposit['amount'];
							}
							else 	if (($arrRes['tranz_id'] !=''  && $ftdAmount==0 && strtolower($arrAmount['type'])=='deposit'))  {
							// else 	if (($arrRes['tranz_id'] !=''  && $ftdAmount==0))  {
								$ftdAmount = $arrAmount['amount'];
								
								$firstDeposit['rdate'] = $arrAmount['rdate'];
								$firstDeposit['initialftddate'] = $arrAmount['rdate'];
									
							
								$firstDeposit['rdate'] = $arrAmount['rdate'];
								$firstDeposit['id'] = $arrAmount['id'];
							}
							

							if ($earliestTimeForNetRev>$arrAmount['rdate'])
								$earliestTimeForNetRev = $arrAmount['rdate'];
						
                            
                            if ($arrAmount['type'] == 'deposit') {
                                $depositAmount += $arrAmount['amount'];
                                $total_deposits++;
                            } elseif ($arrAmount['type'] == 'bonus') {
                                $bonusAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'withdrawal') {
                                $withdrawalAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'chargeback') {
                                $chargebackAmount += $arrAmount['amount'];
                            } elseif ($arrAmount['type'] == 'volume') {
                                $volumeAmount += $arrAmount['amount'];
                                $totalTraders++;
								
								/* 
								$volume += $salesww['amount']; */
                                            // die ('gerg');
											$arrTmp = [
                                                'merchant_id'  => $arrAmount['merchant_id'],
                                                'affiliate_id' => $arrAmount['affiliate_id'],
                                                'rdate'        => $arrAmount['rdate'],
                                                'banner_id'    => $arrAmount['banner_id'],
                                                'trader_id'    => $arrAmount['trader_id'],
                                                'profile_id'   => $arrAmount['profile_id'],
                                                'type'       => 'volume',
                                                'amount'       => $arrAmount['amount'],
                                            ];
                                  
											$volumeCom = getCommission(
                                                $arrAmount['rdate'], 
                                                $arrAmount['rdate'], 
                                                0, 
                                                (isset($group_id) && $group_id != '' ? $group_id : -1), 
                                                $arrDealTypeDefaults, 
                                                $arrTmp
                                            );
                                            $totalCom += $volumeCom;
											
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after volume " . $volumeCom . "<br/>";
									   } 
											
									
								
                            }
                            unset($arrAmount);
							
                        }
					
			
                        
                    } 
		                    
                    if ($type != 'ftd' && $type != 'totalftd') {
                        //$ftd = $totalTraders = $depositAmount = $total_deposits = $volumeAmount = 0;
                       // $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                       // $spreadAmount = $pnl = $ftdAmount = 0;
                        $ftdUsers = '';
                        $this_group_id = $arrRes['group_id'] && $arrRes['group_id']>-1 ? $arrRes['group_id'] : -1;
						
						
                        $arrTotalFtds = getTotalFtds(
                            $from, $to, $arrRes['affiliate_id'], $arrRes['merchant_id'], $arrMerchant['wallet_id'], 
                            $this_group_id, $arrRes['banner_id'], $arrRes['profile_id'], '', $arrRes['trader_id']
                        );
						
					
						
	
                        foreach ($arrTotalFtds as $arrResLocal) {
				/* 			
									   $size = sizeOf($arrTotalFtds);
								for ($ftdCount=0; $ftdCount<$size; $ftdCount++) {
									$arrResLocal = $arrTotalFtds[$ftdCount] ;
				 */					
									
                            $beforeNewFTD = $ftd;
                            getFtdByDealType($arrRes['merchant_id'], $arrResLocal, $arrDealTypeDefaults, $ftdUsers, $ftdAmount, $ftd);

                            
							if ($beforeNewFTD != $ftd ) {
                                $firstDeposit = $arrResLocal;
								
                                $ftdAmount = $arrResLocal['amount'];
						
                                $arrResLocal['isFTD'] = true;


							if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$arrTradersPerMerchants)) {
							// $arrTradersPerMerchants[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
                                
                                // if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {

                                    $ftdComFromLoop = getCommission($arrResLocal['rdate'], $arrResLocal['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $arrResLocal);
                                    $totalCom += $ftdComFromLoop;
									
									if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission ftd loop " . $ftdComFromLoop . "<br/>";
									   } 
									   
									   
                                }
                            }
                            unset($arrResLocal);
                        }
                    }

					output_memory('','G1','');
					
					
						//lots 
					
					
						if (strtolower($arrMerchant['producttype']) == 'forex') {
							
					
						$totalLots  = 0;
						
						
						if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$tradersProccessedForLots)) {
							$tradersProccessedForLots[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
							
						$sql = 'SELECT  * FROM data_stats 
                                         WHERE merchant_id>0 and merchant_id = "' . $arrMerchant['id'] . '" and  trader_id = "' . $arrRes['trader_id'] . '" ' ;
                           // die ($sql);
                                        $traderStatsQ = function_mysql_query($sql,__FILE__);
                                        $earliestTimeForLot = date('Y-m-d H:i:s');
										while($ts = mysql_fetch_assoc($traderStatsQ)){
													
													
													if ($earliestTimeForLot>$ts['rdate'])
															$earliestTimeForLot = $ts['rdate'];
                                            $this_running_lots_amount  = $ts['amount'];
											$totalLots  += $this_running_lots_amount;

							
							
							$row = [
                                            'merchant_id'  => $ts['merchant_id'],
                                            'affiliate_id' => $ts['affiliate_id'],
                                            'rdate'        => $ts['rdate'],
                                            'banner_id'    => $ts['banner_id'],
                                            'trader_id'    => $ts['trader_id'],
                                            'profile_id'   => $ts['profile_id'],
                                            'type'       => 'lots',
                                         'amount'       =>  $this_running_lots_amount,
										 ];
										 
						//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
						// die ('getcom: ' .$a );
						
						// die();
							$b = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
							
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after lots " . $b . "<br/>";
									   } 
									   
									   
							$totalCom +=$b;
										}
										
						
						}
						
						
						
						
						
						}
						
						output_memory('','G2','');
						
						
						if ($set->deal_pnl == 1) {
						
								$totalPNL  = 0;
								$dealsForAffiliate = getExistingDealTypesAllMerchantsForAffiliateArray($arrRes['affiliate_id'],$arrDealTypeDefaults);								
								
								// {
								if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$tradersProccessedForPNL)) {
									$tradersProccessedForPNL[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
									// {	
									
									// die ($where);
								
						if ($dealsForAffiliate['pnl']>0){
								$pnlRes=array();	
								
								
								/* $sql = 'SELECT  rdate,amount as amount FROM data_sales
												 WHERE merchant_id>0   and merchant_id = "' . $arrMerchant['id'] . '" and  trader_id = "' . $arrRes['trader_id'] . '" AND ' . $globalWhere . '
									   type="PNL" and rdate BETWEEN "' . $from . '" AND "' . $to . '"  '.
										(!empty($affiliate_id) ? 'and affiliate_id = ' . $affiliate_id : '');

 */
										
									$pnlRecordArray=array();
									$pnlRecordArray['affiliate_id']  = (!empty($affiliate_id) ? $affiliate_id: "");
									$pnlRecordArray['merchant_id']  = $arrMerchant['id'];
									$pnlRecordArray['group_id']  = $group_id;
									$pnlRecordArray['trader_id']  = $arrRes['trader_id'];
									$pnlRecordArray['searchInSql']  = $searchInSql;
									$pnlRecordArray['fromdate']  = $from;
									$pnlRecordArray['todate']  = $to;
							$sql = generatePNLquery($pnlRecordArray,false);
							
									   
									   		// echo ($sql).'<Br>';
								 $traderStatsQ = function_mysql_query($sql,__FILE__);
								 $pnlCom = 0;
								 while($ts = mysql_fetch_assoc($traderStatsQ)){
									 $pnlRes[] = $ts;
									 
								 }
									
									
								}
							else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
									
									
										if (empty($PNLPerformanceAgregationArray[$arrMerchant['id']])){
									
			/* 									$sql = 'SELECT  sum(amount) as amount,max(rdate) ,max(trader_id),max(merchant_id),max(banner_id),max(profile_id),max(affiliate_id) FROM '.$set->pnlTable.'
															 WHERE merchant_id>0   '
															 .(!empty($affiliate_id) ?  '  and  affiliate_id = ' . $affiliate_id : "")
															 .(!empty($arrRes['trader_id']) ?  '  and  trader_id = ' . $arrRes['trader_id'] : "")
															 .(!empty($group_id) ?  '  and  group_id = ' . $group_id : "")
															 .(!empty($arrMerchant['id']) ?  '  and  merchant_id = ' . $arrMerchant['id'] : "")
															 . ' and type="PNL" AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql); */
															 
															 $sql = 'SELECT  sum(pnltable.amount) as amount,max(pnltable.rdate) as rdate ,max(pnltable.trader_id) as trader_id ,max(pnltable.merchant_id) as merchant_id,max(pnltable.banner_id) as banner_id,max(pnltable.profile_id) as profile_id,max(pnltable.affiliate_id) as affiliate_id FROM '.$set->pnlTable.' pnltable
													 inner join data_reg on pnltable.merchant_id = data_reg.merchant_id and pnltable.trader_id  = data_reg.trader_id and data_reg.type<>"demo"
													 WHERE pnltable.type="PNL" and pnltable.merchant_id>0 and pnltable.amount<>0   '
													 .(!empty($affiliate_id) ?  '  and  pnltable.affiliate_id = ' . $affiliate_id : "")
													 .(!empty($group_id) ?  '  and  pnltable.group_id = ' . $group_id : "")
													 .(!empty($trader_id) ?  '  and  pnltable.trader_id = ' . $trader_id : "")
													 .(!empty($country_id) ?  '  and  pnltable.country_id = ' . $country_id : "")
													 .(!empty($banner_id) ?  '  and  pnltable.banner_id = ' . $banner_id : "")
													 .(!empty($profile_id) ?  '  and  pnltable.profile_id = ' . $profile_id : "")
													 .(!empty($arrMerchant['id']) ?  '  and  pnltable.merchant_id = ' . $arrMerchant['id'] : "")
													 . ' AND pnltable.rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql)
													 .	 " group by pnltable.trader_id;";
													 
												$traderStatsQ = function_mysql_query($sql,__FILE__);
												while($ts = mysql_fetch_assoc($traderStatsQ)){
												
												$PNLPerformanceAgregationArray[$ts['merchant_id']][$ts['trader_id']]=$ts;
													
													
												}
										}
										$pnlRes[] = $PNLPerformanceAgregationArray[$arrMerchant['id']][$arrRes['trader_id']];
										
								}
											/* $traderStatsQ = function_mysql_query($sql,__FILE__);
										while($ts = mysql_fetch_assoc($traderStatsQ)){
										
										$PNLPerformanceAgregationArray[$ts['merchant_id']][$ts['trader_id']]=$ts;
											
											
										}
									
										$pnlRes[] = $PNLPerformanceAgregationArray[$arrMerchant['id']][$arrRes['trader_id']]; */
										
								//}
								
							/* else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
									
									$sql = 'SELECT  sum(amount) as amount,max(rdate) ,max(trader_id),max(merchant_id),max(banner_id),max(profile_id),max(affiliate_id) FROM '.$set->pnlTable.'
												 WHERE type="PNL" and merchant_id>0   '
												 .(!empty($affiliate_id) ?  '  and  affiliate_id = ' . $affiliate_id : "")
												 .(!empty($arrRes['trader_id']) ?  '  and  trader_id = ' . $arrRes['trader_id'] : "")
												 .(!empty($group_id) ?  '  and  group_id = ' . $group_id : "")
												 .(!empty($arrMerchant['id']) ?  '  and  merchant_id = ' . $arrMerchant['id'] : "")
												 . ' AND rdate ' . (empty($searchInSql) ? "BETWEEN '" . $from . "' AND '" . $to . "' " : $searchInSql);
									
								} */
									foreach ($pnlRes as $ts)
									{
												$pnlamount = ($ts['amount']*-1);
												$row = [
													'merchant_id'  => $ts['merchant_id'],
													'affiliate_id' => $ts['affiliate_id'],
													'rdate'        => $ts['rdate'],
													'banner_id'    => $ts['banner_id'],
													'trader_id'    => $ts['trader_id'],
													'profile_id'   => $ts['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  $pnlamount,
												 'initialftddate'       =>  $ts['initialftddate']
												 ];
												 
											
												$totalPNL = $totalPNL + $pnlamount;
												
															 
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
											if ($dealsForAffiliate['pnl']>0){
												$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
												// echo 'com: ' . $tmpCom.'<br>';
												
										if($_GET['com'] && $traderIDForCheck)
									   {
										   echo " Commission after pnl " . $b . "<br/>";
									   } 
												
													$totalCom += $tmpCom;
											}
								}
								
								
								
								
						   
											// echo ($sql).'<Br>';
								/* $traderStatsQ = function_mysql_query($sql,__FILE__);
								while($ts = mysql_fetch_assoc($traderStatsQ)){
												$pnlamount = ($ts['amount']*-1);
												$row = [
													'merchant_id'  => $arrMerchant['id'],
													'affiliate_id' => $ts['affiliate_id'],
													'rdate'        => $ts['rdate'],
													'banner_id'    => $ts['banner_id'],
													'trader_id'    => $ts['trader_id'],
													'profile_id'   => $ts['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  $pnlamount,
												 ];
												 
											
												$totalPNL = $totalPNL + $pnlamount;
												
															 
											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
											if ($dealsForAffiliate['pnl']>0){
												$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
												
												if($_GET['com'] && $traderIDForCheck)
												{
													echo " Commission after pnl2 " . $tmpCom . "<br/>";
												} 
												// echo 'com: ' . $tmpCom.'<br>';
													$totalCom += $tmpCom;
											}
								} */
						}
						
						
						}
						
						output_memory('','G3','');
						


                    if (strtolower($arrMerchant['producttype']) == 'sportsbetting' || strtolower($arrMerchant['producttype']) == 'casino') {
                        $arrRevenueRanges = getRevenueDealTypeByRange($from, $to, $arrRes['merchant_id'], $arrRes['affiliate_id'], $arrDealTypeDefaults);
                        $intTotalRevenue  = 0;
                        
                        foreach ($arrRevenueRanges as $arrRange) {
                            
							$arrAffiliate = getAffiliateRow($arrRes['affiliate_id']);
							
						
                            $arrMerchantsAffiliate = explode('|', $arrAffiliate['merchants']);
                            
                            if (!in_array($arrRes['merchant_id'], $arrMerchantsAffiliate)) {
                                continue;
                            }
                            
							
                            $intCurrentRevenue = getRevenue(
                                'WHERE merchant_id = ' . $arrRes['merchant_id'] . ' AND rdate BETWEEN "' . $arrRange['from'] . '" AND "' . $arrRange['to'] . 
                                '" AND affiliate_id = "' . $arrRes['affiliate_id'] . '" AND trader_id = ' . $arrRes['trader_id'] . ' ',
                                
								$arrMerchant['producttype'],0,0,0,0,0,0,$arrMerchant['rev_formula']
                            );
                            
                            $intTotalRevenue    += $intCurrentRevenue;
                            $row                 = [];
                            $row['merchant_id']  = $arrRes['merchant_id'];
                            $row['affiliate_id'] = $arrRes['affiliate_id'];
                            $row['banner_id']    = $arrRes['banner_id'];
                            $row['rdate']        = $arrRange['from'];
                            $row['initialftddate']  = $arrRes['initialftddate'];
                            $row['amount']       = $intCurrentRevenue;
                            $row['trader_id']    = $arrRes['trader_id'];
                            $row['isFTD']        = false;
							
							
                            $netCommission           = getCommission($arrRange['from'], $arrRange['to'], 1, $group_id, $arrDealTypeDefaults, $row);
							
							if($_GET['com'] && $traderIDForCheck)
								 {
								  echo " Commission after netcommission " . $netCommission . "<br/>";
								  } 
							
                            $totalCom           += $netCommission;
							
							
                            unset($arrRange);
                        }
                        
                        $netRevenue = $intTotalRevenue;
                        
                    } else {
						
						
		if (!empty($netDepositTransactions)){
						foreach($netDepositTransactions as $trans){

							
							$a=0;
							
							$revDepAmount = 0;
							$revBonAmount = 0;
							$revWithAmount = 0;
							$revChBAmount = 0;
							
							$amount = $trans[0]['amount'];
							$recordDate = $trans[0]['rdate'];
							
							
							
						if (floatval($amount>0)  && !empty($trans[0]['rdate'])) {


							
								if ($trans[0]['type']=='deposit')
									$revDepAmount = $amount;
								if ($trans[0]['type']=='bonus')
									$revBonAmount = $amount;
								if ($trans[0]['type']=='withdrawal')
									$revWithAmount = $amount;
								if ($trans[0]['type']=='chargeback')
									$revChBAmount = $amount;
								
							$netRevenue =  round(getRevenue("data_sales.rdate  BETWEEN . '".$recordDate."' AND '".$recordDate." 23:59:59' ",$arrMerchant['producttype'],$revDepAmount,$revBonAmount,$revWithAmount,0,0,0,$arrMerchant['rev_formula'],null,$revChBAmount),2);
							
							
							
/* 
						 	echo 'transamount: ' . $trans[0]['amount']. '<br>';
							echo 'netRevenue: ' . $netRevenue. '<br>';
							
							echo '$recordDate: ' . $recordDate. '<br>'. '<br>';
							// die ($netRevenue); 
 */							 
							
							// $regDateRow = mysql_fetch_assoc(mysql_query("select rdate from data_reg where merchant_id = " . $trans[0]['merchant_id'] . " and trader_id = " . $trans[0]['trader_id']));
							// $regDate = $regDateRow['rdate'];
							// var_dump($regDate);
							// die();
								$comrow                 = array();
				           $comrow['merchant_id']  = $trans[0]['merchant_id'];
                           $comrow['affiliate_id'] = $trans[0]['affiliate_id'];
                            $comrow['banner_id']    = 0;
                            $comrow['initialftddate']        = $trans[0]['initialftddate'];
                            $comrow['rdate']        = $trans[0]['rdate'];
                            $comrow['amount']       = $netRevenue;
                             $comrow['trader_id']  =  $trans[0]['trader_id'];
                            $comrow['isFTD']        = false;
							if(isset($trans[0]['initialftddate']))
                            $comrow['initialftddate']        = $trans[0]['initialftddate'];
							  
						$com = getCommission($recordDate,$recordDate, 1, -1, $arrDealTypeDefaults, $comrow);
						
													if($_GET['com'] && $traderIDForCheck)
								 {
								  echo " Commission after net com inside tranz loop " . $com . "<br/>";
								  } 
								  
								  
						
										$totalCom           += $com;
										
								 if ($_GET['ggg']>0) {
									 echo '6: ' . $a . '<br>';
														 var_dump($com);
														 echo '<Br>';
														 var_dump($comrow);
														 echo '<Br>';
														 echo '$totalCom: ' . $totalCom. '<br>';
														 echo '<Br>';
														 echo '<Br>';
														 echo '<Br>';
								 }
								 
										// $totalCom        +=0;//   += $com;
										
						}
								

								
				}
				unset ($netDepositTransactions);
		}
		else{
					$netRevenue =  round(getRevenue(" data_sales.rdate BETWEEN '".$from."' AND '".$to."' ",$arrMerchant['producttype'],$depositAmount,$bonusAmount,$withdrawalAmount,0,0,0,$arrMerchant['rev_formula'],null,$chargebackAmount),2);								
					
					
	/*	
$netRevenue =  round(getRevenue($where,$arrMerchant['producttype'],$depositAmount,$bonusAmount,$withdrawalAmount,0,0,0,$arrMerchant['rev_formula'],null,$chargebackAmount),2);

 0,0,0,0,0,0,$arrMerchant['rev_formula']
    $merchantType        = 'casino',
    $sumDeposits         = 0,
    $bonus               = 0,
    $withdrawal          = 0,
    $pnl                 = 0,
    $turnoverAmount      = 0,
    $spreadAmount        = 0,
    $formula             = 0,
    $intProfileId        = null */
	
	
                        // $netRevenue = round($depositAmount - ($withdrawalAmount + $bonusAmount + $chargebackAmount), 2);
						
               
					$row                 = [];
				  $row['merchant_id']  = $arrRes['merchant_id'];
                       $row['affiliate_id'] = $arrRes['affiliate_id'];
                            $row['banner_id']    = $arrRes['banner_id'];
                            $row['rdate']        = $earliestTimeForNetRev;
                            $row['initialftddate']        = $arrRes['initialftddate'];
                            $row['amount']       = $netRevenue;
                            $row['trader_id']    = $arrRes['trader_id'];
                            $row['isFTD']        = false;
							   	
								
								
							    $externalCom           = getCommission($earliestTimeForNetRev, $to, 1, $group_id, $arrDealTypeDefaults, $row);
								if($_GET['com'] && $traderIDForCheck)
								 {
								  echo " Commission after net externalCom " . $externalCom . "<br/>";
								  } 
								  
								  
							    $totalCom           += $externalCom;

                    }
					}
					output_memory('','G4','');
           
					
                    // AFFILIATE info retrieval.
                    
					$affInfo = getAffiliateRow($arrRes['affiliate_id'],1);
							
						
				/* 	$sql = "SELECT * FROM affiliates AS aff "
                            . " WHERE aff.valid = 1 AND id = " . $arrRes['affiliate_id']
                            . " LIMIT 0, 1;";

                    $affInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__)); */

                    if ($arrRes['type'] == 'real') {
                        $color = 'green';
                    } elseif ($arrRes['type'] == 'demo') {
                        $color = 'red';
                    } elseif ($arrRes['type'] == 'lead') {
                        $color = 'black';
                    }


					
					// have inner join with where rdate of reg >   ///   NIR
                    /* // Check trader.
					$reason="";
                   
				   $sql = 'SELECT * FROM payments_details '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['merchant_id'] 
                            . '  LIMIT 0, 1;';
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (empty($chkTrader)) {
						$sql = 'SELECT * FROM traders_tag '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['merchant_id'] 
                            . '  LIMIT 0, 1;';
					
					// die ($sql);
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					// var_dump($chkTrader);
					if (!empty($chkTrader)) {
						
						$reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
					}
					
					}else {
					$reason = $chkTrader['reason'];
						
					} */
					
					$reason="";
                   
				   
					$notesArray = array();
					
					if (empty($notesArray)){
				   $sql = "select pd.trader_id, tt.notes,pd.status,pd.reason from payments_details pd 
								left join traders_tag tt on tt.merchant_id = pd.merchant_id and tt.trader_id = pd.trader_id where pd.merchant_id = " . $arrRes['merchant_id'] . " and pd.trader_id in (" . implode(',',$allTradersIDs) .") ; " ;
						// die ($sql);
								// left join traders_tag tt on tt.merchant_id = pd.merchant_id and tt.trader_id = pd.trader_id where pd.merchant_id = " . $arrRes['merchant_id'] . " and pd.trader_id = '" . $arrRes['trader_id'] ."' limit 1; " ;
						$results=mysql_query($sql);
						
						while(($row =  mysql_fetch_assoc($results))) {
							$notesArray[$row['trader_id']] = $row;
						}
					}
                    $chkTrader = $notesArray[$arrRes['trader_id']];
								
				   /* 
				   $sql = 'SELECT * FROM payments_details '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['merchant_id'] 
                            . '  LIMIT 0, 1;'; */
                    // $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					/* if (empty($chkTrader)) {
						$sql = 'SELECT * FROM traders_tag '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['merchant_id'] 
                            . '  LIMIT 0, 1;';
					
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__)); */
					if (!empty($chkTrader)) {
						
						$reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
					}
					
					// }else 
						
					if (empty($chkTrader)) {
					
					$reason = $chkTrader['reason'];
						
					}
					
					
					
					
					output_memory('','G5','');
// var_dump($chkTrader);
// die();
					
					// var_dump($arrRes);
					// die();

                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].'</td>';
							if( $displayForex==1)
								$listReport .= ($arrRes['sub_trader_count']>0  ? '<td><a href="/'. $userlevel .'/reports.php?act=subtraders&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'">'.$arrRes['sub_trader_count'].'</a></td>' : '<td></td>');
							
							
							/* $foundcountry = (longCountry($arrRes['country']));
							if (is_array($foundcountry))
							$foundcountry = array_values($foundcountry)[0]; 
							 */
							 
							 $foundcountry = $acountries[$arrRes['country']];
							 
							
							
                            $listReport .='<td>'.$arrRes['trader_alias'].'</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.$arrRes['email'].'</td>' : '' ) . '
                            <td title="'.($type == "deposit" ? date("d/m/Y H:i:s", strtotime($traderInfo['rdate'])) : date("d/m/Y H:i:s", strtotime($arrRes['rdate']))).'">'.($type == "deposit" ? date("d/m/Y", strtotime($traderInfo['rdate'])) : date("d/m/Y", strtotime($arrRes['rdate']))).'</td>
                            <td><span style="color: '.$color.';">'.$arrRes['type'].'</span></td>
                            <td>'.$foundcountry.'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
                            <td><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
                            <td>'.$arrMerchant['id'].'</td>
                            <td>'.strtoupper($arrMerchant['name']).'</td>
                            <td style="text-align: left;">'.$bannerInfo['id'].'</td>
                            <td style="text-align: left;">'.$bannerInfo['title'].'</td>
                            <td>'.$bannerInfo['type'].'</td>
                            <td>'.$bannerInfo['language_name'].'</td>
                            <td>'.$arrRes['profile_id'].'</td>
                            <td>'.$arrRes['status'].'</td>
                            <td>'.$arrRes['freeParam'].'</td>
                            <td>'.$arrRes['freeParam2'].'</td>
                            <td>' . (isset($arrRes['tranz_id']) ? $arrRes['tranz_id'] : '') . '</td>
                            <td title="'.($type == "deposit" ? date("d/m/Y H:i:s", strtotime($arrRes['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y H:i:s", strtotime($firstDeposit['rdate'])) : '')).'">'.($type == "deposit" ? date("d/m/Y", strtotime($arrRes['rdate'])) : ($firstDeposit['id'] ? date("d/m/Y", strtotime($firstDeposit['rdate'])) : '')).'</td>
							
                            <td>'.price($ftdAmount).'</td>
							
                            <td>'.($depositAmount>0 && $ftdAmount>0 && $depositAmount > $ftdAmount ?  price($depositAmount-$ftdAmount) : "" ).'</td>
                            <td>'.($total_deposits>1 ? $total_deposits-1 : "" ).'</td>
                            <td><a href="/'. $userlevel .'/reports.php?act=transactions&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'&type=deposit">'.$total_deposits.'</a></td>
                            <td>'.price($depositAmount).'</td>
                            <td>'.price($volumeAmount).'</td>
                            <td>'.price($bonusAmount).'</td>
                            <td>'.price($withdrawalAmount).'</td>
                            <td>'.price($chargebackAmount).'</td>
                            <td>'.price($netRevenue).'</td>
                            <td>'.$totalTraders.'</td>
							'. ( $displayForex==1 ? 
                            '<td>'.price($totalLots).'</td>' : '' ).'
							'. ($set->deal_pnl==1  ? 
							'<td>'.price($totalPNL).'</td>' : '').'
                            <td>'.$arrRes['saleStatus'].'</td>
							'.($set->displayLastMessageFieldsOnReports ==1 ? '
                            <td>'.$arrRes['lastSaleNoteDate'].'</td>
                            <td>'.$arrRes['lastSaleNote'].'</td>':'').'
                            <td>'.($arrRes['lastTimeActive']=='1969-12-31 23:00:00' || $arrRes['lastTimeActive'] == '0000-00-00 00:00:00' ? '-' : $arrRes['lastTimeActive']).'</td>
                            <td>'.price($totalCom).'</td>
                            <td>'.$reason.'</td>
                        </tr>';
                    
					
					
                    if (!in_array($arrRes['merchant_id'] . '-' . $arrRes['trader_id'],   $arrTradersPerMerchants)) {
                        $arrTradersPerMerchants[] = $arrRes['merchant_id'] . '-' . $arrRes['trader_id']; //$arrRes['trader_id'];
                        $totalTotalCom += $totalCom;
                        $totalFTD += $ftdAmount;
                        $totalNetRevenue += $netRevenue;
					

					// die ($totalTotalCom);
					if ($_GET['deb']==1) {
					var_dump($arrTradersPerMerchants);
					die('totalcom: ' . $totalCom);
					}
					
					
                    }

                    $totalDepositAmount += $depositAmount;
                    $totalVolumeAmount += $volumeAmount;
                    $totalBonusAmount += $bonusAmount;
                    $totalTotalDeposit += $total_deposits;
                    $totalTrades += $totalTraders;
                    $totalLotsamount += $totalLots;
                    $totalPNLamount += $totalPNL;
                    $totalWithdrawalAmount += $withdrawalAmount;
                    $totalChargeBackAmount += $chargebackAmount;
                    $ftdExist[] = $firstDeposit['trader_id'];
                    $l++;
					
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$totalTraders=$totalLots=0;
                }
                        
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form action="'.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="trader" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						<td>'.lang('Affiliate ID').'</td>
						<td>'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						<td>'.lang(ptitle('Trader Alias')).'</td>
						'.($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.lang(ptitle('Trader Email')).'</td>':'').'
						<td>'.lang(ptitle('Parameter')).'</td>
						<td>'.lang(ptitle('Parameter2')).'</td>
						'. ($userlevel == 'admin' ? '<td>'.lang('Group').'</td>':'') .'
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_alias" value="'.$trader_alias.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						'.($set->ShowEmailsOnTraderReportForAdmin ? '<td><input type="text" name="email" value="'.$email.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>':'').'
						<td><input type="text" name="param" value="'.$param.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="'.$param2.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td-->
						'.($userlevel == 'admin'?'
						<td>
                                                    <select name="group_id" style="width: 100px;">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                                                </td>':'').'
						<td>
							<select name="type" style="width: 100px;">
								<option value="allaccounts" '.($type == "allaccounts" ? 'selected' : '').'>'.lang(ptitle('All Accounts')).'</option>
								<option value="real" '.($type == "real" ? 'selected' : '').'>'.lang(ptitle('Accounts')).'</option>
								'.($hideDemoAndLeads? "": '<option value="lead" '.($type == "lead" ? 'selected' : '').'>'.lang(ptitle('Lead')).'</option>
								<option value="demo" '.($type == "demo" ? 'selected' : '').'>'.lang(ptitle('Demo')).'</option>').'
								<!--option value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option-->
								<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>
								<!--option value="revenue" '.($type == "revenue" ? 'selected' : '').'>'.lang('Revenue').'</option-->
                                                                <option value="totalftd" '.($type == "totalftd" ? 'selected' : '').'>'.lang('Total FTD').'</option>
                                                                '.(!$hideDemoAndLeads? "": '<option value="frozen" '.($type == "frozen" ? 'selected' : '').'>'.lang('Frozen').'</option>').'
							</select>
						</td>
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2600px;">'.lang('Report Results').'</div>
			<div style="background: #F8F8F8;">';
				$tableStr ='<table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0" id="traderTbl">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).'</th>'
						.( $displayForex==1 ? '
						<th>'.lang(ptitle('Sub Traders')).'</th>' : '' ).'
						<th>'.lang(ptitle('Trader Alias')).'</th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th>'.lang(ptitle('Email')).'</th>' : '' ) . '
						<th>'.lang('Registration Date').'</th>
						<th>'.lang(ptitle('Trader Status')).'</th>
						<th>'.lang('Country').'</th>
						<th>'.lang('Affiliate ID').'</th>
						<th>'.lang('Affiliate Username').'</th>
						<th>'.lang('Merchant ID').'</th>
						<th>'.lang('Merchant Name').'</th>
						<th style="text-align: left;">'.lang('Creative ID').'</th>
						<th style="text-align: left;">'.lang('Creative Name').'</th>
						<th>'.lang('Type').'</th>
						<th>'.lang('Creative Language').'</th>
						<th>'.lang('Profile ID').'</th>
						<th>'.lang('Status').'</th>
						<th>'.lang('Param').'</th>
						<th>'.lang('Param2').'</th>
                        <th>' . lang('Transaction ID') . '</th>
						<th>'.($type == "deposit" ? lang('Deposit Date') : lang('First Deposit')).'</th>
						<th>'.lang('FTD Amount').'</th>
						<th>'.lang('Total Next Deposits').'</th>
						<th>'.lang('Next Deposits').'</th>
						<th>'.lang('Total Deposits').'</th>
						<th>'.($type == "deposit" ? lang('Deposit Amount') : lang('Deposits Amount')).'</th>
						<th>'.lang('Volume').'</th>
						<th>'.lang('Bonus Amount').'</th>
						<th>'.lang('Withdrawal Amount').'</th>
						<th>'.lang('ChargeBack Amount').'</th>
						<th>'.lang(ptitle('Net Revenue')).'</th>
						<th>'.lang(ptitle('Trades')).'</th> '
						. ($displayForex==1  ? 
						'<th>'.lang(ptitle('Lifetime Lots')).'</th>' : '' ) . '
						'. ($set->deal_pnl==1  ? 
						'<th>'.lang(ptitle('PNL')).'</th>' : '' ) . '
						<th>'.lang('Sale Status').'</th>
						'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th>'.lang('Last Sale Note Date').'</th>
						<th>'.lang('Last Sale Note').'</th>' : '' ).'
						<th>'.lang('Last Time Active').'</th>
						<th>'.lang('Commission').'</th>
						<th>'.lang('Admin Notes').'</th>
					</tr></thead>
					<tfoot>
						<th></th>'
						.( $displayForex==1 ? '
						<th></th>' : '').'
						<th></th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th></th>' : '' ) . '
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
						<th style="text-align: left;">'.price($totalFTD).'</th>
						<th></th>
						<th></th>
						<th style="text-align: left;">'.$totalTotalDeposit.'</th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th style="text-align: left;">'.price($totalVolumeAmount).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th style="text-align: left;">'.price($totalNetRevenue).'</th>
						<th style="text-align: left;">'.$totalTrades.'</th>
						'. ($displayForex==1 ? 
						'<th style="text-align: left;">'.$totalLotsamount.'</th>' : '' ).
						'
						'. ($set->deal_pnl==1  ? 
						'
						
						<th style="text-align: left;">'.price($totalPNLamount).'</th>
						' : '' ).'
						<th></th>
						'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th></th>
						<th></th>' : '' ).'
						<th></th>
						<th style="text-align: left;">'.price($totalTotalCom).'</th>
						<th></th>
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>
				<script>
				$(document).ready(function(){
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'traderData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					i = 0;
					$($("#traderTbl")[0].config.rowsCopy).each(function() {
						console.log(i);
						i++;
						txt += "<tr>" + $(this).html()+"</tr>";
					});
					txt += "</tbody>";
					txt += "<tfoot>" + tfoot + "</tfoot>";
					txt += "</table>";
					$("body").append("<div style=\'display:none\'>"+ txt +"</div>");
				});
				</script>
				';
				
				$set->content.=$tableStr.'
			</div>'.getPager();
			
			
//excelExporter($tableStr,'Trader');		
		theme();

?>