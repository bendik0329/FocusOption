<?php


if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLprefix."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/affiliate" );
}


$userlevel = 'affiliate';
		$pageTitle = lang(ptitle('Trader Report'));
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
			
		if (strpos($set->reportsToHide, 'trader') > 0) {
			
			if ($set->SSLprefix = ltrim(ltrim($set->SSLprefix,'affiliate'),'/'))
                            _goto($set->SSLprefix.'affiliate/');
						else
                            _goto($set->SSLprefix.'/');
        }
		
		$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "Trader_data_" . date('YmdHis');
		
		// echo 'rth: ' . ($set->reportsToHide). '------';
		
		
                //$group_id     = $set->userInfo['group_id']; // '$group_id' MUST be commented in affiliate/reports.
                $affiliate_id   = $set->userInfo['id'];
                
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
				
               
                // List of wallets.
                $arrAllTraders = [];
                $merchant_id = isset($merchant_id) && !empty($merchant_id) ? $merchant_id : 0;
                
				
				//profile names
				$sql = "select id,name from affiliates_profiles where valid=1";
				$qqProfiles = function_mysql_query($sql);
				$listProfiles = array();
				while($wwProfiles = mysql_fetch_assoc($qqProfiles)){
					$listProfiles[$wwProfiles['id']] = $wwProfiles['name'];
				}
                
                // List of wallets.
                $arrWallets = [];
                $sql = "SELECT DISTINCT wallet_id AS wallet_id,id FROM merchants WHERE valid = 1 " . $strWhereMerchantId;
                $resourceWallets = function_mysql_query($sql,__FILE__);
                while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
				
				$affiliatesRowsArray = array();
				$creativesRowsArray = array();
				
                
				
				
				$allbrabdrsc = function_mysql_query($sql,__FILE__);
				$LowestLevelDeal = 'ALL';
				while ($brandsRow = mysql_fetch_assoc($allbrabdrsc)) {

				    foreach ($dealsArray as $dealItem=>$value) {
    					if ($brandsRow['id']==$dealItem) {
    						$LowestLevelDeal = getLowestLevelDeal($LowestLevelDeal, $value);
    						break;
    					}
    				}
				}
			   $deal = $LowestLevelDeal;
		

					
				$allTranz = array();
				$loopedMerchant_ID = $int_merchant_id = $merchant_id;
						 
						 
						 
					
                    $where = '';
                    
                    if ($affiliate_id) {
						$where .= " AND rt.AffiliateID='".$affiliate_id."' ";
					}

                    if ($group_id) {
						$where .= " AND aff.group_id='".$group_id."' ";
					}

                    if ($banner_id) {
						$where .= " AND rt.CreativeID='".$banner_id."' ";
					}

					//if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
					
					if ($trader_id) {
						$where .= " AND rt.TraderID='".$trader_id."' ";
					}

					if ($merchant_id) {
						$where .= " AND rt.MerchantID='" . $merchant_id . "' ";
					}

                    if ($param) {
						$param =  trim($param);
					    $where .= " AND rt.Param='".$param."' ";
					}
                    
                    if ($param2) {
						$where .= " AND rt.Param2='".$param2."' ";
					}
                    
					if ($email && $set->ShowEmailsOnTraderReportForAdmin ){
						$email = trim($email);
						$where .= " AND lower(rt.Email) like '%".strtolower($email)."%' ";
					}
					


                    if ($country_id) {
                        $where .= " AND rt.Country='".$country_id."' ";
                    }
                    
					$ftdAmount = 0;
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
			
			
			output_memory('','A3','');
			
			$traderIDForCheck = true;
			 $typeFilter = "";	
			 $typeDateFilter = "";	
			 $dateFilterFieldName = "rdate";
			 $addInitialFTDPart =  " and initialftddate>'". $from . "'  ";
			 
			 
			if ($type == 'real') {
                $where .= " AND rt.TraderStatus='real' ";
            } else if ($type == 'lead') {
                $where .= " AND rt.TraderStatus='lead' ";
            } else if ($type == 'demo') {
                $where .= " AND rt.TraderStatus='demo' ";
            } else if ($type == 'frozen') {
                $where .= " AND rt.TraderStatus='frozen' ";
            } else if ($type == 'ftd' || $type == 'totalftd') {
                $where .= " and (rt.FirstDeposit BETWEEN '" . $from . "' and '" . $to . "'  ) AND rt.TraderStatus<>'frozen' and rt.TraderStatus<>'demo' ";
            } else if ($type == 'activeTrader') {
                $where .= " AND (rt.QualificationDate BETWEEN '" . $from . "' and '" . $to . "'  ) AND rt.TraderStatus<>'frozen' AND rt.TraderStatus<>'demo' ";
            }
				
			$typeFilter .= $typeDateFilter;
			
			
			$traders = implode(",",$arrAllTraders);
			
			
                    if($type == 'ftd' || $type == 'totalftd'){
                        $tradersQuery = "SELECT rt.*, dr.saleStatus as SaleStatusOriginal, dr.freeParam5 as freeParam5, aff.group_id as GroupID FROM ReportTraders rt INNER JOIN affiliates aff ON rt.AffiliateID = aff.id INNER JOIN data_reg dr ON dr.trader_id = rt.TraderID  WHERE 1=1 ".$where." ORDER BY RegistrationDate DESC, rt.TraderID ASC";
                    }else{
                        $tradersQuery = "SELECT rt.*, dr.saleStatus as SaleStatusOriginal, dr.freeParam5 as freeParam5, aff.group_id as GroupID FROM ReportTraders rt INNER JOIN affiliates aff ON rt.AffiliateID = aff.id INNER JOIN data_reg dr ON dr.trader_id = rt.TraderID  WHERE RegistrationDate BETWEEN '".$from."' AND '".$to."' ".$where." ORDER BY RegistrationDate DESC, rt.TraderID ASC";
                    }
			


					$tradersProccessedForLots= array();
					$tradersProccessedForPNL= array();
					$PNLPerformanceAgregationArray= array();
               

					$netDepositTransactions = array();


					$acountries = getLongCountries('sales');


					$trader_report_resource = mysql_query($tradersQuery);
					while ($traderItem = mysql_fetch_assoc($trader_report_resource)){

                        $arrRes = [];

			        $landing_params_array = json_decode($traderItem['freeParam5']);
                    if(!empty($landing_params_array)){
                        $landing_params_array_data = [];
                        
                        if(!empty($landing_params_array->serial)){
                            $traderItem['Param4'] = $landing_params_array->serial; 
                        }
                        
                        foreach($landing_params_array as $k => $v) { 
                            $landing_params_array_data[] = "$k = $v"; 
                        }
                        $landing_params = implode('; ', $landing_params_array_data);
                    }else{
                        $landing_params = $traderItem['freeParam5'];
                    }

						$arrRes['trader_id'] = $traderItem['TraderID'];
						$arrRes['sub_trader_count'] = '';
                        $arrRes['trader_alias'] = $traderItem['TraderAlias'];
                        $arrRes['email'] = $traderItem['Email'];
                        $arrRes['rdate'] = $traderItem['RegistrationDate'];
                        $arrRes['type'] = $traderItem['TraderStatus'];
                        $foundcountry = $acountries[$traderItem['Country']];
                        $arrRes['affiliate_id'] = $traderItem['AffiliateID'];
                        $arrRes['username'] = $traderItem['AffiliateUsername'];
                        $arrRes['m_id'] = $traderItem['MerchantID'];
                        $arrRes['m_name'] = $traderItem['MerchantName'];
                        $arrRes['banner_id'] = $traderItem['CreativeID'];
                        $arrRes['banner_title'] = $traderItem['CreativeName'];
                        $arrRes['banner_type'] = $traderItem['Type'];
                        $arrRes['banner_language_name'] = $traderItem['CreativeLanguage'];
                        $arrRes['profile_id'] = $traderItem['ProfileID'];
                        $arrRes['profile_name'] = $traderItem['ProfileName'];
                        $arrRes['freeParam'] = $traderItem['Param'];
                        $arrRes['freeParam2'] = $traderItem['Param2'];
                        $arrRes['freeParam3'] = $traderItem['Param3'];
                        $arrRes['freeParam4'] = $traderItem['Param4'];
                        $arrRes['freeParam5'] = $landing_params;
                        $arrRes['tranzactionId'] = $traderItem['TransactionID'];
                        $arrRes['initialftddate'] = $traderItem['FirstDeposit'];
                        $arrRes['ftdamount'] = $traderItem['FTDAmount'];
                        $total_deposits = $traderItem['TotalDeposits'];
                        $depositAmount = $traderItem['DepositAmount'];
                        $volumeAmount = $traderItem['Volume'];
                        $bonusAmount = $traderItem['BonusAmount'];
                        $withdrawalAmount = $traderItem['WithdrawalAmount'];
                        $chargebackAmount = $traderItem['ChargeBackAmount'];
                        $netRevenue = $traderItem['NetDeposit'];
                        $totalTraders = $traderItem['Trades'];
                        $totalLots = 0;
                        $totalPNL = $traderItem['PNL'];
                        $arrRes['saleStatus'] = $traderItem['SaleStatusOriginal'];
                        $arrRes['lastSaleNote'] = '';
                        $arrRes['lastSaleNoteDate'] = '';
                        $arrRes['FTDqualificationDate'] = $traderItem['QualificationDate'];
                        $totalCom = $traderItem['Commission'];
                        $reason = $traderItem['AdminNotes'];
                        $arrRes['uid'] = $traderItem['ClickDetails'];

                        
						
						
						if($arrRes['type'] == 'real'){
						    $color = 'green';
						}else{
						    $color = 'red';
						}
					

					$cnt=1;
					

					
                    $listReport .= '
                        <tr>
                            <td>'.$arrRes['trader_id'].''.($debugshowhide ? "1":'').'</td>';
								if($displayForex==1)
								$listReport .='<td><a href="'. $set->SSLprefix.$userlevel .'/reports.php?act=subtraders&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'">'.$arrRes['sub_trader_count'].'</a></td>';
							$listReport .= '

                            ' . ( allowView('af-mail',$deal,'fields') ? '<td>'.$arrRes['email'].''.($debugshowhide ? "3":'').'</td>' : '' ) . '
							<td>'.date("d/m/Y", strtotime($arrRes['rdate'])).''.($debugshowhide ? "4":'').'</td>
                            <td><span style="color: '.$color.';">'.$arrRes['type'].'</span>'.($debugshowhide ? "5":'').'</td>
                            <td>'.$foundcountry.''.($debugshowhide ? "6":'').'</td>
                            <td>'.$arrRes['affiliate_id'].''.($debugshowhide ? "7":'').'</td>
                            <td><a href="affiliate/account.php" target="_blank">'.$arrRes['username'].'</a>'.($debugshowhide ? "8":'').'</td>
                            <td>'.$arrRes['m_id'].''.($debugshowhide ? "9":'').'</td>
                            <td>'.strtoupper($arrRes['m_name']).''.($debugshowhide ? "10":'').'</td>
                            <td style="text-align: left;">'.$arrRes['banner_id'].''.($debugshowhide ? "11":'').'</td>
                            <td style="text-align: left;">'.$arrRes['banner_title'].''.($debugshowhide ? "12":'').'</td>
                            <td>'.$arrRes['banner_type'].''.($debugshowhide ? "13":'').'</td>
							<td>'.$arrRes['banner_language_name'].''.($debugshowhide ? "14":'').'</td>
                            <td>'.$arrRes['profile_id'].''.($debugshowhide ? "15":'').'</td>
                            <td>'.$arrRes['profile_name'].''.($debugshowhide ? "15":'').'</td>
							<td>'.(empty($arrRes['status']) ? lang('real'): $arrRes['status']) .''.($debugshowhide ? "16":'').'</td>
                            <td>'.$arrRes['freeParam'].''.($debugshowhide ? "17":'').'</td>
                            <td>'.$arrRes['freeParam2'].''.($debugshowhide ? "18":'').'</td>
                            <td>'.$arrRes['freeParam3'].''.($debugshowhide ? "18":'').'</td>
                            <td>'.$arrRes['freeParam4'].''.($debugshowhide ? "18":'').'</td>
                            <td>'.$arrRes['freeParam5'].''.($debugshowhide ? "18":'').'</td>
                             '. (allowView('af-trnz',$deal,'fields') ?
							'<td>' . $arrRes['tranzactionId'] . ''.($debugshowhide ? "19":'').'</td>' : '').'
							'. (allowView('af-ftd',$deal,'fields')  ?
							'<td>' .((strtotime($arrRes['initialftddate']) > 0)?date("d/m/Y H:i:s", strtotime($arrRes['initialftddate'])):'&mdash;').($debugshowhide ? "20":'').'</td>'
							: '').'
							'. (allowView('af-ftda',$deal,'fields') ? '
                            <td>'.price($arrRes['ftdamount']).''.($debugshowhide ? "21":'').'</td>
							': '' ).'
							'. (allowView('af-depo',$deal,'fields') ? '
                            <td><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("d/m/Y", strtotime("-3 Years")).'&to='.date("d/m/Y").'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'&type=deposit">'.$total_deposits.'</a>'.($debugshowhide ? "22":'').'</td>
							': '' ).'
							'. (allowView('af-depoam',$deal,'fields') ? '
                            <td>'.price($depositAmount).''.($debugshowhide ? "23":'').'</td>
							': '' ).'
							'. (allowView('af-vlm',$deal,'fields') ? '
                            <td>'.price($volumeAmount).''.($debugshowhide ? "24":'').'</td>
							': '' ).'
							'. (allowView('af-bns',$deal,'fields') ? '
                            <td>'.price($bonusAmount).''.($debugshowhide ? "25":'').'</td>
							': '' ).'
							'. (allowView('af-withd',$deal,'fields') ? '
                            <td>'.price($withdrawalAmount).''.($debugshowhide ? "26":'').'</td>
							': '' ).'
							'. (allowView('af-chrgb',$deal,'fields') ? '
                            <td>'.price($chargebackAmount).''.($debugshowhide ? "27":'').'</td>
							': '' ).'
							'. (allowView('af-ntrv',$deal,'fields') ? '
                            <td>'.price($netRevenue).''.($debugshowhide ? "28":'').'</td>
							': '' ).'
							'. (allowView('af-trades',$deal,'fields') ? '
                            <td>'.$totalTraders.''.($debugshowhide ? "29":'').'</td>
							': '' ).'
		'. ( allowView('af-vlm',$deal,'fields')  && $displayForex==1 ? 
                            '<td>'.$totalLots.''.($debugshowhide ? "30":'').'</td>' : '' ).'
							
							'. ($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields') ? 
							'<td>'.price($totalPNL).'</td>' : '').'
							
							'.(allowView('af-salests',$deal,'fields') ? '
                            <td>'.$arrRes['saleStatus'].''.($debugshowhide ? "31":'').'</td>
							':'').'
							
							'. ( allowView('af-slsnt',$deal,'fields')   ? 
							
							
                            '<td>'.$arrRes['lastSaleNote'].''.($debugshowhide ? $cnt++:'').'</td>
                            <td>'.($arrRes['lastSaleNoteDate']=='0000-00-00 00:00:00' ? '' : $arrRes['lastSaleNoteDate']).''.($debugshowhide ? $cnt++:'').'</td>' : '' ).
													
							 /*( allowView('af-qftd',$deal,'fields')   ? 
							'<td>'.(hasValidDate($arrRes['FTDqualificationDate']) ? $arrRes['FTDqualificationDate'] : "" ).'</td>' : '' ).*/'
							
							'. (($affiliate_id == 1025)?(($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 && $affiliate_id == 1025) ? '' : '<td>'.price($totalCom).''.($debugshowhide ? $cnt++:'').'</td>'):'')./*'
							<td>'. $reason . ($debugshowhide ? $cnt++:'').'</td>
							'.( allowView('af-clckdtls',$deal,'fields')   ? 
							'<td>'. (($arrRes['uid']!=0 || $arrRes['uid']!="")?'<a href="'.$set->SSLprefix.'affiliate/reports.php?act=clicks&from='.date("d/m/Y", strtotime($from)).'&to='.date("d/m/Y", strtotime($to)).'&merchant_id='.$int_merchant_id.'&trader_id='.$arrRes['trader_id'].'">'. lang('View') .'</a>':'') .'</td>':'')
							.*/'
                        </tr>';
						               
					
					

                    $arrTradersPerMerchants[] = $arrRes['merchant_id'] . '-' . $arrRes['trader_id']; //$arrRes['trader_id'];
                    $totalFTD += $arrRes['ftdamount'];
                    $totalNetRevenue += $netRevenue;
                    $totalTotalCom += $totalCom;

                    
                    $totalDepositAmount += $depositAmount;
                    $totalVolumeAmount += $volumeAmount;
                    $totalBonusAmount += $bonusAmount;
                    $totalTotalDeposit += $total_deposits;
                    $total_Traders +=           $totalTraders  ;
                    
					$totalLotsamount += $totalLots;
					 $totalPNLamount += $totalPNL;
					 
                    $totalWithdrawalAmount += $withdrawalAmount;
                    $totalChargeBackAmount += $chargebackAmount;
                    $ftdExist[] = $firstDeposit['trader_id'];
                    $l++;
					
					$volumeAmount =$totalLots = $totalTraders= $bonusAmount=$withdrawalAmount=$netRevenue=$chargebackAmount= $total_deposits=0;
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=0;					
					
                }
                        
			
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
		$set->totalRows = $l;
		
		
		$set->content .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form id="frmRepo" action="'.$set->SSLprefix.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="trader" />
				<table border="0" cellpadding="3" cellspacing="2">
					<tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						
						<td>'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>

						<td>'.lang(ptitle('Parameter')).'</td>
						<td>'.lang(ptitle('Parameter2')).'</td>
						
						<td>'.lang('Filter').'</td>
						<td></td>
					</tr><tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value=""'.($merchant_id == 0 ? ' selected="selected"' : 'selected=null').'>'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<!--td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" onblur="validateMerchant(this)"/></td-->
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>

						<td><input type="text" name="param" value="'.$param.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="'.$param2.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
                                               
						<td>
							<select name="type" style="width: 110px;">
								<option value="real" '.($type == "real" ? 'selected' : '').'>'.lang(ptitle('Accounts')).'</option>
								'.($hideDemoAndLeads? "": '<option value="lead" '.($type == "lead" ? 'selected' : '').'>'.lang(ptitle('Lead')).'</option>
								<option value="demo" '.($type == "demo" ? 'selected' : '').'>'.lang(ptitle('Demo')).'</option>').'
								 '. (allowView('af-ftd',$deal,'fields') ? '<option value="ftd" '.($type == "ftd" ? 'selected' : '').'>'.lang('FTD').'</option>' : '').'
								 '. (allowView('af-tftd',$deal,'fields') ? '<option value="totalftd" '.($type == "totalftd" ? 'selected' : '').'>'.lang('RAW FTD').'</option>' : '').'
								 '. (allowView('af-qftd',$deal,'fields') ? '<option value="activeTrader" '.($type == "activeTrader" ? 'selected' : '').'>'.lang('Active Trader').'</option>' : '').'
								 '.(allowView('af-frzn',$deal,'fields') ? '<option value="frozen" '.($type == "frozen" ? 'selected' : '').'>'.lang('Frozen').'</option>' : '').'
							</select>
						</td>
						
						<!--td><input type="button" value="'.lang('View').'" onClick="validateForm()"/></td-->
						<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
				</table>
				</form>
				'.(
                                    $set->export 
                                    ? //'<div class="exportCSV"><a href="'.$set->basepage.'?act=trader_xml&'.str_replace("act=".$act,"",$_SERVER['QUERY_STRING']).'">
                                       //     <img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'
                                        //        .lang('Export to CSV').'</b></a></div>'
                                       '<div class="exportCSV" style="float:left">
                                            <a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});">
                                                <img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'
                                                    .lang('Export to CSV').'</b>
                                            </a>
                                        </div>':'').'
                                        <div class="exportCSV" style="float:left">
                                            <a style="cursor:pointer" onclick="$(\'#traderData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});">
                                                <img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'
                                                    .lang('Export to XLS').'</b>
                                            </a>
                                        </div>
										'. getFavoritesHTML() .'
                                        <div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle" style="width: 2600px;">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">
				<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="traderTbl">
					<thead><tr  class="table-row">
						<th  class="table-cell">'.lang(ptitle('Trader ID')).''.($debugshowhide ? "a0":'').'</th>
						'. ($displayForex==1  ? '
						<th class="table-cell">'.lang(ptitle('Trader Sub Accounts')).''.($debugshowhide ? "a2":'').'</th>':'').'
						' . (allowView('af-mail',$deal,'fields')  ? '<th>'.lang(ptitle('Email')).''.($debugshowhide ? "a3":'').'</th>' : '' ) . '
						<th class="table-cell">'.lang('Registration Date').''.($debugshowhide ? "a4":'').'</th>
						<th class="table-cell">'.lang(ptitle('Trader Status')).''.($debugshowhide ? "a5":'').'</th>
						<th class="table-cell">'.lang('Country').''.($debugshowhide ? "a6":'').'</th>
						<th class="table-cell">'.lang('Affiliate ID').''.($debugshowhide ? "a7":'').'</th>
						<th class="table-cell">'.lang('Affiliate Username').''.($debugshowhide ? "a8":'').'</th>
						<th class="table-cell">'.lang('Merchant ID').''.($debugshowhide ? "a9":'').'</th>
						<th class="table-cell">'.lang('Merchant Name').''.($debugshowhide ? "a10":'').'</th>
						<th  class="table-cell"style="text-align: left;">'.lang('Creative ID').''.($debugshowhide ? "a11":'').'</th>
						<th class="table-cell" style="text-align: left;">'.lang('Creative Name').''.($debugshowhide ? "a12":'').'</th>
						<th class="table-cell">'.lang('Type').''.($debugshowhide ? "a13":'').'</th>
						<th class="table-cell">'.lang('Creative Language').''.($debugshowhide ? "a14":'').'</th>
						<th class="table-cell">'.lang('Profile ID').''.($debugshowhide ? "a15":'').'</th>
						<th class="table-cell">'.lang('Profile Name').''.($debugshowhide ? "a15":'').'</th>
						<th class="table-cell">'.lang('Status').''.($debugshowhide ? "a16":'').'</th>
						<th class="table-cell">'.lang('Param').''.($debugshowhide ? "a17":'').'</th>
						<th class="table-cell">'.lang('Param2').''.($debugshowhide ? "a18":'').'</th>
						<th class="table-cell">'.lang('Param3').''.($debugshowhide ? "a18":'').'</th>
						<th class="table-cell">'.lang('Param4').''.($debugshowhide ? "a18":'').'</th>
						<th class="table-cell">'.lang('Param5').''.($debugshowhide ? "a18":'').'</th>'
                        
						. (allowView('af-trnz',$deal,'fields') ?
						'<th class="table-cell">' . lang('Transaction ID') . ''.($debugshowhide ? "a19":'').'</th>' : '').'
						'. (allowView('af-ftd',$deal,'fields') ?
						'<th class="table-cell">'.lang(($type == "deposit" ? 'Deposit Date' : 'First Deposit')).''.($debugshowhide ? "a20":'').'</th>' :'').
						(allowView('af-ftda',$deal,'fields') ? 
						'<th class="table-cell">'.lang('FTD Amount').''.($debugshowhide ? "a21":'').'</th>' : '').
						(allowView('af-depo',$deal,'fields')  ? '<th class="table-cell">'.lang('Total Deposits').''.($debugshowhide ? "a22":'').'</th>' : '' ) .
						(allowView('af-depoam',$deal,'fields') ? '<th class="table-cell">'.lang(($type == "deposit" ? 'Deposit Amount' : 'Deposit Amount')).''.($debugshowhide ? "a23":'').'</th>' : '' ).
						(allowView('af-vlm',$deal,'fields') ? '<th class="table-cell">'.lang('Volume').''.($debugshowhide ? "a24":'').'</th>' : '').
						(allowView('af-bns',$deal,'fields') ? '<th class="table-cell">'.lang('Bonus  Amount').''.($debugshowhide ? "1":'').'</th>' : '').
						(allowView('af-withd',$deal,'fields') ? '<th class="table-cell">'.lang('Withdrawal Amount').''.($debugshowhide ? "1":'').'</th>' : '').
						(allowView('af-chrgb',$deal,'fields') ? '<th class="table-cell">'.lang('ChargeBack Amount').''.($debugshowhide ? "1":'').'</th>' : '').
						(allowView('af-ntrv',$deal,'fields') ? '<th class="table-cell">'.lang(ptitle('Net Deposit')).''.($debugshowhide ? "1":'').'</th>':'').
						(allowView('af-trades',$deal,'fields') ? '
						<th class="table-cell">'.lang(ptitle('Trades')).''.($debugshowhide ? "1":'').'</th>' : '' ). '
	'. (allowView('af-vlm',$deal,'fields') && $displayForex==1  ? 
						'<th class="table-cell">'.lang(ptitle('Lots')).''.($debugshowhide ? "1":'').'</th>' : '' ) . '
						'. ($set->deal_pnl==1  && allowView('af-pnl',$deal,'fields')? 
						'<th class="table-cell">'.lang(ptitle('PNL')).'</th>' : '' ) . '
						'.(allowView('af-salests',$deal,'fields') ? '
						<th class="table-cell">'.lang('Sale Status').''.($debugshowhide ? "1":'').'</th>
						' :'').'
						'. ( allowView('af-slsnt',$deal,'fields')   ? 
							
							
                            '<th class="table-cell">'.lang('Last Sale Note').''.($debugshowhide ? "1":'').'</th>
                            <th class="table-cell">'.lang('Last Sale Note Date').''.($debugshowhide ? "1":'').'</th>
							' : '' ).'
							
							'. /*( allowView('af-qftd',$deal,'fields')   ? 
                            '<th class="table-cell">'.lang('Qualification Date').''.($debugshowhide ? "1":'').'</th>
							' : '' ).*/'
                            
						'. (($affiliate_id == 1025)?(($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 && $affiliate_id == 1025)? '' : '<th>'.lang('Commission').''.($debugshowhide ? "1":'').'</th>' ):'')./*'
						
						<th class="table-cell">'.lang('Admin Notes').''.($debugshowhide ? "1":'').'</th>'
						.( allowView('af-clckdtls',$deal,'fields')   ? '<th class="table-cell">' . lang('Click Details') . '</th>':'')
						.*/'
					</tr></thead>
					<tfoot>
						<th>'.($debugshowhide ? "b-1":'').'</th>
						'. ($displayForex==1  ? '<th>'.($debugshowhide ? "b1":'').'</th>':'').'
						
						' . (allowView('af-mail',$deal,'fields') ?  '<th id="3">'.($debugshowhide ? "b3":'').'</th>' : '' ) . '
						<th>'.($debugshowhide ? "b4":'').'</th>
						<th>'.($debugshowhide ? "b5":'').'</th>
						<th>'.($debugshowhide ? "b6":'').'</th>
						<th>'.($debugshowhide ? "b7":'').'</th>
						<th>'.($debugshowhide ? "b8":'').'</th>
						<th>'.($debugshowhide ? "b9":'').'</th>
						<th>'.($debugshowhide ? "b10":'').'</th>
						<th>'.($debugshowhide ? "b11":'').'</th>
						<th>'.($debugshowhide ? "b12":'').'</th>
						<th>'.($debugshowhide ? "b13":'').'</th>
						<th>'.($debugshowhide ? "b14":'').'</th>
						<th>'.($debugshowhide ? "b15":'').'</th>
						<th>'.($debugshowhide ? "b15":'').'</th>
						<th>'.($debugshowhide ? "b16":'').'</th>
						<th>'.($debugshowhide ? "b17":'').'</th>
						<th>'.($debugshowhide ? "b18":'').'</th>
						<th>'.($debugshowhide ? "b18":'').'</th>
						<th>'.($debugshowhide ? "b18":'').'</th>
						<th>'.($debugshowhide ? "b18":'').'</th>'
						
						. (allowView('af-trnz',$deal,'fields') ?
						'<th>'.($debugshowhide ? "b19":'').'</th>
						' : '').'
						'. (allowView('af-ftd',$deal,'fields') ?
						
						'<th>'.($debugshowhide ? "b20":'').'</th>':'').'
                              
						' . (allowView('af-ftda',$deal,'fields') ? '
						<th  style="text-align: left;">'.price($totalFTD).''.($debugshowhide ? "b21":'').'</th>
						' : '' ).'
						' . (allowView('af-depo',$deal,'fields') ? '
						<th style="text-align: left;">'.$totalTotalDeposit.''.($debugshowhide ? "b22":'').'</th>
						' : '' ).'
						' . (allowView('af-depoam',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalDepositAmount).''.($debugshowhide ? "b23":'').'</th>
						' : '' ).'
						' . (allowView('af-vlm',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalVolumeAmount).''.($debugshowhide ? "b24":'').'</th>
						' : '' ).'
						' . (allowView('af-bns',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalBonusAmount).''.($debugshowhide ? "b25":'').'</th>
						' : '' ).'
						' . (allowView('af-withd',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalWithdrawalAmount).''.($debugshowhide ? "b26":'').'</th>
						' : '' ).'
						' . (allowView('af-chrgb',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalChargeBackAmount).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-ntrv',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalNetRevenue).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-trades',$deal,'fields') ? '
						<th style="text-align: left;">'.$total_Traders.''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
	                    '. (allowView('af-vlm',$deal,'fields') && $displayForex==1 ? 
						'<th style="text-align: left;">'.$totalLotsamount.''.($debugshowhide ? "1":'').'</th>' : '' ).'
						'. ($set->deal_pnl==1  && allowView('af-pnl',$deal,'fields')? 
						'<th>'.$totalPNLamount.'</th>' : '' ).'
						'.(allowView('af-salests',$deal,'fields') ? '
						<th>'.($debugshowhide ? "1":'').'</th>
						':'').'
						'. ( allowView('af-slsnt',$deal,'fields')   ? 
							
							
                            '
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
                            
							' : '' ).'
							
							'. /*( allowView('af-qftd',$deal,'fields')   ? 
						'<th>'.($debugshowhide ? "1":'').'</th>
							' : '' ).*/'
							
						'. (($affiliate_id == 1025)?(($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1)? '' : '<th style="text-align: left;">'.price($totalTotalCom).''.($debugshowhide ? "1":'').'</th>'):'')./*'
						<th>'.($debugshowhide ? "1":'').'</th>'
						.( allowView('af-clckdtls',$deal,'fields')   ? '<th></th>':'')
						.*/'
						
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>
				<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
				<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>   
				<script>
				$(document).ready(function(){
					/* thead = $("thead").html();
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
					$("body").append("<div style=\'display:none\'>"+ txt +"</div>"); */
					
					$("input[name=trader_id]").on("keyup",function(){
						if($(this).val()!=""){
							$("#date_from").val("'. date("Y/m/d",strtotime('-100 year')) .'");
							$("#date_to").val("'. date("Y/m/d",strtotime('+100 year')) .'");
						}
						else{
							$("#date_from").val("'. date("Y/m/d") .'");
							$("#date_to").val("'. date("Y/m/d") .'");
						}
					});
				});
				</script>
			</div>'.getPager();
				$tableStr .= getSingleSelectedMerchant();
				// $tableStr .= getValidateTraderMerchantScript();
                $tableStr = '
                    <table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
					<thead><tr>
						<th>'.lang(ptitle('Trader ID')).''.($debugshowhide ? "1":'').'</th>
						'.($displayForex==1 ? '
						<th>'.lang(ptitle('Sub Traders')).''.($debugshowhide ? "1":'').'</th>':'').'
						
						
						' . (allowView('af-mail',$deal,'fields') ? '
					<th>'.lang(ptitle('Email')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						
						<th>'.lang('Registration Date').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang(ptitle('Trader Status')).''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Country').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Affiliate ID').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Affiliate Username').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Merchant ID').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Merchant Name').''.($debugshowhide ? "1":'').'</th>
						<th style="text-align: left;">'.lang('Creative ID').''.($debugshowhide ? "1":'').'</th>
						<th style="text-align: left;">'.lang('Creative Name').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Type').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Language').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Profile ID').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Profile Name').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Status').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Param').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Param2').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Param3').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Param4').''.($debugshowhide ? "1":'').'</th>
						<th>'.lang('Param5').''.($debugshowhide ? "1":'').'</th>
						' . (allowView('af-trnz',$deal,'fields') ? '
						<th>' . lang('Transaction ID') . ''.($debugshowhide ? "1":'').'</th>
                        ' : '' ).'
						' . (allowView('af-ftd',$deal,'fields') ? '
						<th>'.lang(($type == "deposit" ? 'Deposit Date' : 'First Deposit')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-ftda',$deal,'fields') ? '
						<th>'.lang('FTD Amount').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-depo',$deal,'fields') ? '
						<th>'.lang('Total Deposits').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-depoam',$deal,'fields') ? '
						<th>'.lang(($type == "deposit" ? 'Deposit Amount' : 'Deposits Amount')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-vlm',$deal,'fields') ? '
						<th>'.lang('Volume').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-bns',$deal,'fields') ? '
						<th>'.lang('Bonus  Amount').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-withd',$deal,'fields') ? '
						<th>'.lang('Withdrawal Amount').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-chrgb',$deal,'fields') ? '
						<th>'.lang('ChargeBack Amount').''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-ntrv',$deal,'fields') ? '
						<th>'.lang(ptitle('Net Revenue')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						' . (allowView('af-trades',$deal,'fields') ? '
						<th>'.lang(ptitle('Trades')).''.($debugshowhide ? "1":'').'</th>
						' : '' ).'
						
						<th>'.lang('Sale Status').''.($debugshowhide ? "1":'').'</th>
						'. ( allowView('af-slsnt',$deal,'fields')   ? 
							
							
                            '<th>'.lang('Last Sale Note').''.($debugshowhide ? "1":'').'</th>
                            <th>'.lang('Last Sale Note Date').''.($debugshowhide ? "1":'').'</th>
							' : '' ).'
							
						'. ($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 ? '' : 	
						'<th>'.lang('Commission').''.($debugshowhide ? "1":'').'</th>').'
						<th>'.lang('Admin Notes').''.($debugshowhide ? "1":'').'</th>
					</tr></thead>
					<tfoot>
						<th>'.($debugshowhide ? "1":'').'</th>
						'.($displayForex==1 ? '
						<th>'.($debugshowhide ? "1":'').'</th>
						':'').'
						
						' . (allowView('af-mail',$deal,'fields') ? '
						<th>'.($debugshowhide ? "1":'').'</th>' : '' ) . '
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>'
						.(allowView('af-ftd',$deal,'fields') ? '<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
						' : '').'
                                                <th>'.($debugshowhide ? "1":'').'</th>
						' . (allowView('af-ftda',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalFTD).''.($debugshowhide ? "1":'').'</th>
						' : '').'
						' . (allowView('af-depo',$deal,'fields') ? '
						<th style="text-align: left;">'.$totalTotalDeposit.''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-depoam',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalDepositAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-vlm',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalVolumeAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-bns',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalBonusAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-withd',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalWithdrawalAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-chrgb',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalChargeBackAmount).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-ntrv',$deal,'fields') ? '
						<th style="text-align: left;">'.price($totalNetRevenue).''.($debugshowhide ? "1":'').'</th>
												' : '').'
						' . (allowView('af-trades',$deal,'fields') ? '
						<th style="text-align: left;">'.$total_Traders.''.($debugshowhide ? "1":'').'</th>
						' : '').'
						<th>'.($debugshowhide ? "1":'').'</th>
						
						'. ( allowView('af-slsnt',$deal,'fields')   ? 
						'<th>'.($debugshowhide ? "1":'').'</th>
						<th>'.($debugshowhide ? "1":'').'</th>
							' : '' ).'
							'. ($set->userInfo['hasRevDeal'] && $set->hideCommissionOnTraderReportForRevDeal==1 ? '' : 
						'<th style="text-align: left;">'.price($totalTotalCom).''.($debugshowhide ? "1":'').'</th>').'
						<th></th>
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
											level = "affiliate";
											type = "add";
											
											saveReportToMyFav(name, \'trader\',user,level,type);
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
					//MODAL
		// $set->content .= getValidateTraderMerchantScript();
		$myReport = lang("Trader");
		include "common/ReportFieldsModal.php";
		
                //excelExporter($tableStr,'Trader');
		theme();
		
?>
