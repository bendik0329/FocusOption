<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$netdeposit = isset($_GET['netdeposit']) ? $_GET['netdeposit'] : 0;

$countriesLong = getLongCountries();
 //$set->pageTitle = lang(ptitle('Transaction Report'));
	$set->breadcrumb_title =  lang(ptitle('Transaction Report'));
	$set->pageTitle = '
	<style>
	.pageTitle{
		padding-left:0px !important;
	}
	</style>
	<ul class="breadcrumb">
		<li><a href="'.$set->SSLprefix.$userlevel.'/">'.lang('Dashboard').'</a></li>
		<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang(ptitle('Transaction Report')).'</a></li>
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
		$filename = "Transactions_data_" . date('YmdHis');
		
			$filterhtml = '';
			$tbl =isset($table) && $table!=""?$table:'data_sales' ;
			if($isCasino || $isSportbet || $displayForex){
				
				$sql = "	SELECT distinct type FROM data_stats where merchant_id>0  order by lower(type)";
				$qq=function_mysql_query($sql,__FILE__);
				while ($ww=mysql_fetch_assoc($qq)) {
					$filterhtml .='<option data-table="data_stats" value="'.$ww['type'].'" '.($type == $ww['type'] ? 'selected' : '').'>'.ucwords(str_replace('_',' ',$ww['type'])).'</option>';
				}
			}
 
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
                $lots = 0;
                $totalTotalCom  = 0;
				$totalSpread = $totalPnl = $totalTurnover =0;
                $arrTradersPerMerchants = [];
                $intTmpMerchantId = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;
                $strWhereMerchantId = isset($merchant_id) && !empty($merchant_id) ? ' AND id = ' . $merchant_id . ' ' : '';
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                $resourceWallets = function_mysql_query($sql,__FILE__);
                
           
                
                
                $sql = "SELECT * FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                // die ($sql);
				$resourceMerchanrs = function_mysql_query($sql,__FILE__);
		
		// $resourceMerchants = $resourceMerchanrs;
				// $arrMerchant = mysql_fetch_assoc($resourceMerchants);
				 $arrMerchant = array();
				
                while ($arrMerchantRow = mysql_fetch_assoc($resourceMerchanrs)) {
					
					$arrMerchant = $arrMerchantRow;
					
                // var_dump($arrMerchantRow);
				// die();
                    $int_merchant_id = empty($intTmpMerchantId) ? $arrMerchantRow['id'] : $intTmpMerchantId;
                
                    $where = ' AND merchant_id = ' . $int_merchant_id;
                    
					
					// die();
						$hidedemo= true;
						
				if ($showdemo=='on' ) {
						$hidedemo =false;
						
					}	

					
					// die ('hide: ' . $ignoredemo);
					
					if ($userlevel=='manager'){
						$group_id = $set->userInfo['group_id'];
						if ($group_id) $where .= " AND group_id='".$group_id."' ";
					}
					else  {
						if ($group_id) $where .= " AND group_id='".$group_id."' ";
					}
						
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
					if ($param) $where .= " AND freeParam='".$param."' ";
					if ($param2) $where .= " AND freeParam2='".$param2."' ";
					if ($hidedemo) $where .= " AND not type='demo' ";
                    
                    if ($trader_alias) {
                        $qry = "select trader_id from data_reg  where merchant_id> 0 and  lower(trader_alias) like ('%". mysql_real_escape_string(strtolower($trader_alias))."%')";
                        $row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
                        
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
                              $where .= " AND trader_alias='".$trader_alias."' ";
							
							
						}
                    }
		    	
                    if ($country_id) {
                        $where .= " AND country='".$country_id."' ";
                    }
                    
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
			
			// all demos and frozen accounts.
			$qry = "select id,status from data_reg where merchant_id > 0 and merchant_id = " . $int_merchant_id . " AND (status = 'frozen' or type= 'demo') ";
			$invalidResources = function_mysql_query($qry,__FILE__);
			$invalidTraders = array();
			$frozenTraders = array();
			while ($invalidRow = mysql_fetch_assoc($invalidResources)) {
				$invalidTraders[] = $invalidRow['id'];
				if ($invalidRow['status']=='frozen') {
					$frozenTraders[] = $invalidRow['id'];
				}
				}
			
			/* if($type == 'position'){
				
				if(strtolower($arrMerchantRow['producttype']) == "casino" || strtolower($arrMerchantRow['producttype']) == "sportbet" || strtolower($arrMerchantRow['producttype']) == "forex")
				{
							$where = str_replace('merchant_id', 'ds.merchant_id', $where);
                            $where = str_replace('trader_id', 'ds.trader_id', $where);
                            $where = str_replace('group_id', 'ds.group_id', $where);
                            $where = str_replace('affiliate_id', 'ds.affiliate_id', $where);
                            $where = str_replace('banner_id', 'ds.banner_id', $where);
                            $where = str_replace('trader_alias', 'ds.trader_alias', $where);
                            $where = str_replace('type', 'ds.type', $where);
                            $where = str_replace('country', 'ds.country', $where);
							
							$sql = "	SELECT ds.id,
							ds.rdate,
							ds.trader_id,
							ds.tranz_id,
							ds.type,
							ds.amount,
							ds.merchant_id,
							ds.type as salesType,
							dr.rdate as registration_date,
							dr.affiliate_id,
							dr.banner_id,
							dr.group_id,
							dr.profile_id,
							dr.country,
							dr.phone,
							dr.trader_alias,
							dr.type as reg_type,
							ds.freeParam,
							ds.freeParam2,
							dr.uid,
							dr.saleStatus,
							dr.lastTimeActive,
							dr.lastSaleNoteDate,
							dr.lastSaleNote,
							dr.status,
							dr.email,
							dr.campaign_id,
							dr.couponName,
							ds.pnl,
							ds.turnover,
							ds.spread
							FROM data_stats AS ds inner join data_reg dr on 
								  ds.trader_id = dr.trader_id
								  and ds.merchant_id = dr.merchant_id  WHERE 5=5 and ds.merchant_id = '" . $int_merchant_id."' and ".$globalWhere." ds.rdate BETWEEN '".$from."' AND '".$to."' ".$where .
							"ORDER BY ds.trader_id ASC";
							
				}
				else{ 
							$where = str_replace('merchant_id', 'ds.merchant_id', $where);
                            $where = str_replace('trader_id', 'ds.trader_id', $where);
                            $where = str_replace('group_id', 'ds.group_id', $where);
                            $where = str_replace('affiliate_id', 'ds.affiliate_id', $where);
                            $where = str_replace('banner_id', 'ds.banner_id', $where);
                            $where = str_replace('trader_alias', 'ds.trader_alias', $where);
                            $where = str_replace('type', 'ds.type', $where);
                            $where = str_replace('country', 'ds.country', $where);
							
							$sql = "	SELECT 
												ds.id,
												ds.rdate,
												ds.trader_id,
												ds.tranz_id,
												ds.type,
												ds.amount,
												ds.merchant_id,
												ds.type as salesType,
												dr.rdate as registration_date,
												dr.affiliate_id,
												dr.banner_id,
												dr.group_id,
												dr.profile_id,
												dr.country,
												dr.phone,
												dr.trader_alias,
												dr.type as reg_type,
												ds.freeParam,
												ds.freeParam2,
												dr.uid,
												dr.saleStatus,
												dr.lastTimeActive,
												dr.lastSaleNoteDate,
												dr.lastSaleNote,
												dr.status,
												dr.email,
												dr.campaign_id,
												dr.couponName
												".
												($tbl == "data_stats"? 'ds.pnl,
							ds.turnover,
							ds.spread' : '')
												."
							FROM ". $tbl ." AS ds inner join data_reg dr on 
								  ds.trader_id = dr.trader_id
								  and ds.merchant_id = dr.merchant_id WHERE 5=5 and 1=1 and ds.type='volume' and ds.merchant_id = '" . $int_merchant_id."' and ".$globalWhere." ds.rdate BETWEEN '".$from."' AND '".$to."' ".$where .
							"ORDER BY ds.trader_id ASC";
				}
				
			}
			else{*/
			if($type=='position'){
					if(strtolower($arrMerchantRow['producttype']) == "casino" || strtolower($arrMerchantRow['producttype']) == "sportbet" || strtolower($arrMerchantRow['producttype']) == "forex")
					{
						$tbl = "data_stats";
					}
					else{
						$tbl = "data_sales";
					}
			}
							$where = str_replace('merchant_id', 'dr.merchant_id', $where);
                            $where = str_replace('trader_id', 'dr.trader_id', $where);
                            $where = str_replace('group_id', 'dr.group_id', $where);
                            $where = str_replace('affiliate_id', 'dr.affiliate_id', $where);
                            $where = str_replace('banner_id', 'dr.banner_id', $where);
                            $where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            $where = str_replace('type', 'dr.type', $where);
                            $where = str_replace('country', 'dr.country', $where);
                   
				   
				   $whereType  =   ($type == 'alltransactions' || $type == '' ? "" : " AND ds.type = '".$type."' " );
				   if ($netdeposit==1)
				   $whereType  = " AND ds.type in ('deposit','withdrawal','chargeback','bonus') ";
				   
							$sql = "SELECT 
							ds.id,
							ds.rdate,
							ds.trader_id,
							ds.tranz_id,
							ds.type,
							ds.amount,
							ds.merchant_id,
							ds.type as salesType,
							".($tbl == 'data_sales' ? 'ds.currency,':'')."
							dr.rdate as registration_date,
							dr.ctag,
							dr.affiliate_id,
							dr.banner_id,
							dr.group_id,
							dr.profile_id,
							dr.country,
							dr.phone,
							dr.trader_alias,
							dr.type as reg_type,
							dr.freeParam,
							dr.freeParam2,
							dr.uid,
							dr.saleStatus,
							dr.lastTimeActive,
							dr.lastSaleNoteDate,
							dr.lastSaleNote,
							dr.status,
							dr.email,
							dr.campaign_id,
							pd.status as pd_status,
							pd.reason as pd_reason,
							dt.status as dt_status,
							dt.notes as dt_notes,
							dr.couponName
							
							".
												($tbl == "data_stats"? ',ds.pnl,
							ds.turnover,
							ds.spread' : '')
												."
							FROM ". $tbl ." AS ds
                                  inner join data_reg dr on 
								  ds.trader_id = dr.trader_id
								  and ds.merchant_id = dr.merchant_id
								  
								  left join traders_tag dt on 
								  dt.trader_id = dr.trader_id
								  and dt.merchant_id = dr.merchant_id
								  
								  left join payments_details pd on 
								  pd.trader_id = dr.trader_id
								  and pd.merchant_id = dr.merchant_id
								  
							
                                    WHERE 2=2 and " . $globalWhere 
                                            . " ds.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND ds.merchant_id >0 "
                                            . " AND ds.merchant_id = " . $int_merchant_id
                                            .$whereType
											//. " AND ds.type != 'volume'  ". " AND ds.type != 'PNL'  "
                                            . $where
											
									. " group by ds.tranz_id , ds.merchant_id "
                                    . " ORDER BY ds.rdate ASC;";
									
							//}
		
						// die ($sql);
			
                            $resource = function_mysql_query($sql,__FILE__);
               while ($arrRes = mysql_fetch_assoc($resource)) {
				   
					// var_dump($arrRes);
					// die();
			
					$ftdAmount = 0;
	
					
                    $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    $totalCom = 0;
                    $int_merchant_id = $arrRes['merchant_id'];
                    
                    if ($type == 'ftd' || $type == 'totalftd') {
                        $firstDeposit = empty($arrRes['firstDeposit']) ? $firstDeposit : $arrRes['firstDeposit'];
                    } else {
                        $firstDeposit = [];
                    }
                    
                    /* if (
                        $type == 'deposit' || 
                        $type == 'bonus' || 
                        $type == 'alltransactions' || 
                        $type == 'chargeback' || 
                        $type == 'withdrawal' || 
						$type == 'position'
                    ) { */
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
                        // $totalCom = empty($arrRes['totalCom']) ? $totalCom : $arrRes['totalCom'];
                        
						
                        $arrRes['trader_alias'] = $arrRes['trader_alias'];
                        $arrRes['email'] = $arrRes['email'];
                        $arrRes['country']      = $arrRes['country'];
                        $arrRes['registration_date']        =  $arrRes['registration_date'];
                        $arrRes['rdate']        = $arrRes['rdate'];
                        $arrRes['salesType']    = $arrRes['type'];
                        $arrRes['regtype']         = $arrRes['reg_type'];
                        $arrRes['banner_id']    = $arrRes['banner_id'];
                        $arrRes['status']       = $arrRes['status'];
                        $arrRes['profile_id']   = $arrRes['profile_id'];
                        $arrRes['freeParam']    = $arrRes['freeParam'];
                        $arrRes['freeParam2']    = $arrRes['freeParam2'];
                        $arrRes['saleStatus']   = $arrRes['saleStatus'];
                        $arrRes['lastTimeActive']   = $arrRes['lastTimeActive'];
                        unset($intTmpTraderId, $intTmpGroupId, $strTmpWhere, $intTmpMerchantId);
                //    }
                    
                 
                    $depositAmount = 0;
					
				
                    $bannerInfo = getCreativeInfo($arrRes['banner_id']);
					
					$arrRes['tranz_id'] = $arrRes['tranz_id'];
					if ($arrRes['tranz_id'] !=''  && $ftdAmount==0) {
						$ftdAmount = $arrRes['amount'];
						
						$firstDeposit['rdate'] = $arrRes['rdate'];
						$firstDeposit['id'] = $arrAmount['id'];
					}
					
					if ($arrAmount['type'] == 'deposit') {
					 //   $depositAmount += $arrAmount['amount'];
					   $total_deposits++;
					} elseif ($arrAmount['type'] == 'bonus') {
					  //  $bonusAmount += $arrAmount['amount'];
					} elseif ($arrAmount['type'] == 'withdrawal') {
					  //  $withdrawalAmount += $arrAmount['amount'];
					} elseif ($arrAmount['type'] == 'chargeback') {
					//    $chargebackAmount += $arrAmount['amount'];
					} elseif ($arrAmount['type'] == 'volume') {
						//$volumeAmount += $arrAmount['amount'];
					}
					unset($arrAmount);
                   
                   

                    if ($arrRes['reg_type'] == 'real') {
                        $color = 'green';
                    } elseif ($arrRes['reg_type'] == 'demo') {
                        $color = 'red';
                    } elseif ($arrRes['reg_type'] == 'lead') {
                        $color = 'black';
                    }
                  
					
					$affInfo = getAffiliateRow($arrRes['affiliate_id'],1);

                    
					
					// Check trader.
					/* $reason="";
                    $sql = 'SELECT * FROM payments_details '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['merchant_id'] 
                            . '  LIMIT 0, 1;';
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (empty($chkTrader)) {
						$sql = 'SELECT * FROM traders_tag '
                            . 'WHERE trader_id = ' . $arrRes['trader_id'] . ' AND merchant_id = ' . $arrRes['merchant_id'] 
                            . '  LIMIT 0, 1;';
					
                    $chkTrader = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
					if (!empty($chkTrader)) {
						$reason = empty($chkTrader['notes']) ? ucwords($chkTrader['status']) : ucwords($chkTrader['status']) . ' - ' . $chkTrader['notes'];
					}
					
					}else {
						$reason = $chkTrader['reason'];
					}
					 */
					 
					 
						 $reason="";
					
					if (!empty($arrRes['pd_reason'])) {
						
						$reason = $arrRes['pd_reason'];
					}
					else
					{
						
						
						$reason = empty($arrRes['dt_notes']) ? ucwords($arrRes['dt_status']) : ucwords($arrRes['dt_status']) . ' - ' . $arrRes['dt_notes'];
						
						}
					
						

					$arrRes['amount'] =  strtolower($arrRes['salesType'])=='deposit' || strtolower($arrRes['salesType'])=='position' || strtolower($arrRes['salesType'])=='volume' ? $arrRes['amount'] : $arrRes['amount']*-1;
							
                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].'</td>
                            <td>'.$arrRes['trader_alias'].'</td>
                            ' . ($set->ShowEmailsOnTraderReportForAdmin ? '<td>'.$arrRes['email'].'</td>' : '' ) . '
                            <td>'.date("d/m/Y", strtotime($arrRes['registration_date'])) .'</td>
                            <td><span style="color: '.$color.';">'.$arrRes['reg_type'].'</span></td>
                            <td>'.$countriesLong[$arrRes['country']].'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
                            <td><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$affInfo['username'].'</a></td>
                            <td>'.$arrMerchant['id'].'</td>
                            <td>'.strtoupper($arrMerchant['name']).'</td>
                            <td style="text-align: left;">'.$bannerInfo['id'].'</td>
                            <td style="text-align: left;">'.$bannerInfo['title'].'</td>
                            <td>'.$bannerInfo['type'].'</td>
                            <td>'.$bannerInfo['language_name'].'</td>
                            <td>'.$arrRes['profile_id'].'</td>
                            <td><span>'.($type=='totalftd' || $type=='ftd' ? strtoupper($type) :  ucwords($arrRes['salesType'])).'</span></td>
                            <td>'.$arrRes['status'].'</td>
                            <td>'.$arrRes['freeParam'].'</td>
                            <td>'.$arrRes['freeParam2'].'</td>
                            <td>' .  $arrRes['tranz_id'] . '</td>
                            <td>'.date("d/m/Y", strtotime($arrRes['rdate'])) .'</td>
                            <td>'.price($arrRes['amount']).'</td>
                            <td>'.ucwords($arrRes['saleStatus']).'</td>
							'.($set->displayLastMessageFieldsOnReports ==1 ? '
                            <td>'.$arrRes['lastSaleNoteDate'].'</td>
                            <td>'.$arrRes['lastSaleNote'].'</td>':'').'
							
							<td>'.($arrRes['lastTimeActive']=='1969-12-31 23:00:00' || $arrRes['lastTimeActive'] == '0000-00-00 00:00:00' ? '-' : $arrRes['lastTimeActive']).'</td>
                            <!--td>'.price($totalCom).'</td-->
                            <td>'.$chkTrader['reason'].'</td>
							'.($displayForex ? '<td>'.price($arrRes['turnover']).'</td>
							<td>'.price($arrRes['spread']).'</td>
							<td>'.price($arrRes['pnl']).'</td>':'').'
                        </tr>';
                    
                    if (!in_array($arrMerchant['id'] . '-' .  $arrRes['trader_id'],$arrTradersPerMerchants)) {
							$arrTradersPerMerchants[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id'];
							
							
					// if (!in_array($arrRes['trader_id'], $arrTradersPerMerchants)) {
                        // $arrTradersPerMerchants[] = $arrRes['trader_id'];
                        // $totalFTD += $ftdAmount;
                        // $totalNetRevenue += $netRevenue;
                        $totalTotalCom += $totalCom;
						$totalPnl += $arrRes['pnl'];
						$totalTurnover += $arrRes['turnover'];
						$totalSpread += $arrRes['spead'];
                    }
				$totalAmounts += $arrRes['amount'];
                
                    $l++;
					
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$totalTraders=0;
                }
                        
			   }
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                $set->content .='<style>
			  /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 60px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 20px;
			 width: 43px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 12px;
			  width: 12px;
			  left: 3px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
			
		</style>';
                $set->sortTable = 1;
		$set->totalRows = $l;
		$set->content .= '<div class="normalTableTitle"style="width: 1600px;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form id="frmRepo" action="'.$set->SSLprefix.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
				<input type="hidden" name="act" value="transactions" />
				<input type="hidden" name="table"  id="table" '.($tbl!=""? 'value=' . $tbl  :'').'>
				<table border="0" cellpadding="1" cellspacing="1">
					<tr>
						<td style="min-width:360px;">'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						<td width=160>'.lang('Affiliate ID').'</td>
						<td style="padding-left:20px">'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						<td>'.lang(ptitle('Trader Alias')).'</td>
						<td>'.lang(ptitle('Parameter')).'</td>
						<td>'.lang(ptitle('Parameter2')).'</td>
						'.($userlevel=='manager' ? '' : '<td>'.lang('Group').'</td>').'
						<td>'.lang('Filter').'</td> 
						<td></td>
					</tr>
					
					<tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td style="padding-right:20px"><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="fieldClear" style="width: 60px; text-align: center;" /-->
						<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
						</td>
						<td style="padding-left:20px"><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" onblur="validateMerchant(this)"/></td>
						<td><input type="text" name="trader_alias" value="'.$trader_alias.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param" value="'.$param.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="'.$param2.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						'.($userlevel=='manager' ? '' : '
						<td><select name="group_id">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                                                </td> ' ).'
												<td>
							<select name="type" id="table_type" style="width: 130px;">
								<option data-table="data_sales" value="alltransactions" '.($type == "alltransactions" ? 'selected' : '').'>'.lang(ptitle('All Transactions')).'</option>
                                <option data-table="data_sales" value="bonus" '.($type == "bonus" ? 'selected' : '').'>'.lang('Bonus').'</option>
                                <option data-table="data_sales" value="chargeback" '.($type == "chargeback" ? 'selected' : '').'>'.lang('Chargeback').'</option>
								<option data-table="data_sales" value="deposit" '.($type == "deposit" ? 'selected' : '').'>'.lang('Deposits').'</option>
                                <option data-table="data_sales" value="withdrawal" '.($type == "withdrawal" ? 'selected' : '').'>'.lang('Withdrawal').'</option>
								<option data-table="data_sales" value="volume" '.($type == "volume" ? 'selected' : '').'>'.lang('Volume').'</option>
								'.($set->deal_pnl==1 ? '<option data-table="data_sales" value="PNL" '.($type == "PNL" ? 'selected' : '').'>'.lang('PNL').'</option>' : '').'
								'. ($filterhtml != "" ? '<option disabled>...............</option>':'') .'
								' . $filterhtml .'
							</select>
						</td>
							<td><input type="button" value="'.lang('View').'" onclick="validateForm()"/></td>
					</tr>
					
					<tr>
						<!--td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td-->
							<td>'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#transactionData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#transactionData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				</td><td colspan=8></td>

						<td align="center"><div style="padding-bottom:5px;">'.lang('Show Demo Traders').'</div></td>
						<td colspan=2><div><label class="switch"><input type="checkbox" name="showdemo" class="showdemo"  '.($showdemo ? 'checked' : '').' ><div class="slider round"></div></label></div></td>
					</tr>
				</table>
				</form>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle"   class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
			//width 2600
				$tableStr ='<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="transactionTbl">
					<thead><tr   class="table-row">
						<th class="table-cell">'.lang(ptitle('Trader ID')).'</th>
						<th class="table-cell">'.lang(ptitle('Trader Alias')).'</th>
						' . ($set->ShowEmailsOnTraderReportForAdmin ? '<th class="table-cell">'.lang(ptitle('Email')).'</th>' : '' ) . '
						<th class="table-cell">'.lang('Registration Date').'</th>
						<th class="table-cell">'.lang(ptitle('Trader Status')).'</th>
						<th class="table-cell">'.lang('Country').'</th>
						<th class="table-cell">'.lang('Affiliate ID').'</th>
						<th class="table-cell">'.lang('Affiliate Username').'</th>
						<th class="table-cell">'.lang('Merchant ID').'</th>
						<th class="table-cell">'.lang('Merchant Name').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Creative ID').'</th>
						<th  class="table-cell"style="text-align: left;">'.lang('Creative Name').'</th>
						<th class="table-cell">'.lang('Type').'</th>
						<th class="table-cell">'.lang('Creative Language').'</th>
						<th class="table-cell">'.lang('Profile ID').'</th>
						<!--th class="table-cell">'.lang('FTD Date').'</th>
						<th class="table-cell">'.lang('FTD Amount').'</th-->
						<th class="table-cell">'.lang('Transaction Type').'</th>
						<th class="table-cell">'.lang('Status').'</th>
						<th class="table-cell">'.lang('Param').'</th>
						<th class="table-cell">'.lang('Param2').'</th>
                        <th class="table-cell">' . lang('Transaction ID') . '</th>
						<th class="table-cell">'. lang('Transaction Date') .'</th>
						<th class="table-cell">'.lang('Amount').'</th>
						<th class="table-cell">'.lang('Sale Status').'</th>
					'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th class="table-cell">'.lang('Last Sale Note Date').'</th>
						<th class="table-cell">'.lang('Last Sale Note').'</th>' : '' ).'
						<th class="table-cell">'.lang('Last Time Active').'</th>
						<!--th class="table-cell">'.lang('Commission').'</th-->
						<th class="table-cell">'.lang('Admin Notes').'</th>
						'.($displayForex ? '<th class="table-cell">'.lang('Turnover').'</th>
						<th class="table-cell">'.lang('Spread').'</th>
						<th class="table-cell">'.lang('Pnl').'</th>' : '') .'
					</tr></thead>
					<tfoot>
						<th></th>
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
						<th></th>
						
						<th style="text-align: left;">'.price($totalAmounts).'</th>
						<th></th>
				'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th></th>
						<th></th>' : '' ).'
						<th></th>
						<!--th style="text-align: left;">'.price($totalTotalCom).'</th-->
						<th></th>'. ($displayForex ? '
						<th style="text-align: left;">'.price($totalTurnover).'</th>
						<th style="text-align: left;">'.price($totalSpread).'</th>
						<th style="text-align: left;">'.price($totalPnl).'</th>' :'').'
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>
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
											level = "'. $userlevel .'";
											type = "add";
											
											saveReportToMyFav(name, \'transactions\',user,level,type);
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
					try{
						thead = $("thead").html();
						tfoot = $("tfoot").html();
						txt = "<table id=\'transactionData\' class=\'mdlReportFieldsData\'>";
						txt += "<thead>" + thead + "</thead>";
						txt += "<tbody>";
						$($("#transactionTbl")[0].config.rowsCopy).each(function() {
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
											
											saveReportToMyFav(name, \'transactions\',user,level,type);
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
				$tableStr .= getSingleSelectedMerchant();
				$tableStr .= getValidateTraderMerchantScript();
				$set->content.=$tableStr.'
			</div>'.getPager();
			
			$set->content .='
			<script>
				$("#table_type").change(function(){
						var tbl = $(this).find(":selected").data("table");
						$("#table").val(tbl);
				});
			</script>
			';
			
			//MODAL
		$myReport = lang("Transactions");
		include "common/ReportFieldsModal.php";
			
//excelExporter($tableStr,'Transactions');		
		theme();

?>