<?php
//Prevent direct browsing of report
$userlevel = 'affiliate';
// $userlevel = $set->userInfo['level'];

if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}



$countriesLong = getLongCountries();
 //$set->pageTitle = lang(ptitle('Transaction Report'));
	$set->breadcrumb_title =  lang(ptitle('Install Report'));
	

	$affiliate_id = $set->userInfo['id'];
	
	$set->pageTitle = '
	<style>
	.pageTitle{
		padding-left:0px !important;
	}
	</style>
	<ul class="breadcrumb">
		<li><a href="'.$set->SSLprefix.$userlevel.'/">'.lang('Dashboard').'</a></li>
		<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang(ptitle('Install Report')).'</a></li>
		<li><a style="background:none !Important;"></a></li>
	</ul>';
		$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "Transactions_data_" . date('YmdHis');
		
			$filterhtml = '';
			$tbl =isset($table) && $table!=""?$table:'data_sales' ;
			if($isCasino || $isSportbet || $displayForex){
				
				$sql = "	SELECT distinct type FROM data_install where merchant_id>0  order by lower(type)";
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
                $totalPNL = 0;
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
					
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($group_id) $where .= " AND group_id='".$group_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
					if ($param) $where .= " AND freeParam='".$param."' ";
					if ($param2) $where .= " AND freeParam2='".$param2."' ";
					if ($hidedemo) $where .= " AND not type='demo' ";
				
                    if ($country_id) {
                        $where .= " AND country='".$country_id."' ";
                    }
                    
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
			
			
							$where = str_replace('merchant_id', 'dr.merchant_id', $where);
                            $where = str_replace('trader_id', 'dr.trader_id', $where);
                            $where = str_replace('group_id', 'dr.group_id', $where);
                            $where = str_replace('affiliate_id', 'dr.affiliate_id', $where);
                            $where = str_replace('banner_id', 'dr.banner_id', $where);
                            
                            $where = str_replace('type', 'dr.type', $where);
                            $where = str_replace('country', 'dr.country', $where);
							
							$sql = "SELECT 
							dr.* from data_install dr
							
                                
								  
								  left join payments_details pd on 
								  pd.trader_id = dr.trader_id
								  and pd.merchant_id = dr.merchant_id
								  
							
                                    WHERE 2=2 and " . $globalWhere 
                                            . " dr.rdate BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND dr.merchant_id >0 "
                                            . " AND dr.merchant_id = " . $int_merchant_id
                                            .($type == 'all' || $type == '' ? "" : " AND dr.type = '".$type."' " )
											//. " AND ds.type != 'volume'  ". " AND ds.type != 'PNL'  "
                                            . $where
											
								//	. " group by dr.trader_id , dr.merchant_id "
                                    . " ORDER BY dr.rdate ASC;";
									
							
						
			
                            $resource = function_mysql_query($sql,__FILE__);
               while ($arrRes = mysql_fetch_assoc($resource)) {
				   
				
					$ftdAmount = 0;
	
					
                 
                    $totalCom = 0;
                    $int_merchant_id = $arrRes['merchant_id'];
                    
                 
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
                        
						
						
						$totalCom = getCommission($arrRes['rdate'], $arrRes['rdate'], 0, -1, $arrDealTypeDefaults, $arrRes);
                        
						
                        
                        
                        $arrRes['country']      = $arrRes['country'];
                        
                        $arrRes['rdate']        = $arrRes['rdate'];
                        $arrRes['salesType']    = $arrRes['type'];
                        
                        $arrRes['banner_id']    = $arrRes['banner_id'];
                        $arrRes['status']       = $arrRes['status'];
                        $arrRes['profile_id']   = $arrRes['profile_id'];
                        $arrRes['freeParam']    = $arrRes['freeParam'];
                        $arrRes['freeParam2']    = $arrRes['freeParam2'];
                        
                        
                        unset($intTmpTraderId, $intTmpGroupId, $strTmpWhere, $intTmpMerchantId);
                //    }
                    
                 
                    $depositAmount = 0;
					
				
                    $bannerInfo = getCreativeInfo($arrRes['banner_id']);
					
					
                  
					$affInfo = getAffiliateRow($arrRes['affiliate_id']);

					
					 
						 $reason="";
					
					if (!empty($arrRes['pd_reason'])) {
						
						$reason = $arrRes['pd_reason'];
					}
					else
					{
						
						
						$reason = empty($arrRes['dt_notes']) ? ucwords($arrRes['dt_status']) : ucwords($arrRes['dt_status']) . ' - ' . $arrRes['dt_notes'];
						
						}
					
						

					$arrRes['amount'] =  strtolower($arrRes['salesType'])=='deposit' || strtolower($arrRes['salesType'])=='position' || strtolower($arrRes['salesType'])=='volumn' ? $arrRes['amount'] : $arrRes['amount']*-1;
							
                    $listReport .= '
                        <tr>
                            <td>'.ucwords($arrRes['type']) .'</td>
                            <td>'.date("d/m/Y H:i:s", strtotime($arrRes['rdate'])) .'</td>
                            <td>'.$arrRes['trader_id'].'</td>
                            <td>'.$arrRes['trader_alias'].'</td>
                            
							' . ( allowView('af-mail',$deal,'fields') ? '<td>'.$arrRes['email'].''.($debugshowhide ? "3":'').'</td>' : '' ) . '
                            <td><span style="color: '.$color.';">'.$arrRes['reg_type'].'</span></td>
                            <td>'.$countriesLong[$arrRes['country']].'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
                            <td><a href="/'. $userlevel .'/account.php" target="_blank">'.$affInfo['username'].'</a></td>
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

                            <td>'.$chkTrader['reason'].'</td>
                            
                            './*.'<td>'.price($totalCom).'</td>'.*/'

					'.((!empty($arrRes['uid']) && $arrRes['uid']!='')?'<td><a title="deTails" href="/'. $userlevel .'/reports.php?act=clicks&from='.$from.'&to='.$to.'&unique_id='.$arrRes['uid'].'">'. lang('View') .'</a></td>':'<td></td>')
						.'</tr>';					
                    
               
                
                    $l++;
					
					$volumeAmount=$netRevenue=$chargebackAmount = $withdrawalAmount=$depositAmount = $bonusAmount=$total_deposits=$totalTraders=0;
					
					
					$totalTotalCom+=$totalCom;
					
					
					
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
				<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="install" />
				<input type="hidden" name="table"  id="table" '.($tbl!=""? 'value=' . $tbl  :'').'>
				<table border="0" cellpadding="1" cellspacing="1">
					<tr>
						<td style="min-width:360px;">'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						
						<td>'.lang('Banner ID').'</td>
						<td>'.lang(ptitle('Trader ID')).'</td>
						<td>'.lang(ptitle('Parameter')).'</td>
						<td>'.lang(ptitle('Parameter2')).'</td>
						
						<td>'.lang('Filter').'</td> 
						<td></td>
					</tr>
					
					<tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						
						<td><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						
						<td><input type="text" name="trader_id" value="'.$trader_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param" value="'.$param.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						<td><input type="text" name="param2" value="'.$param2.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						
												<td>
							<select name="type" id="table_type" style="width: 130px;">
								<option data-table="data_sales" value="all" '.($type == "all" ? 'selected' : '').'>'.lang(ptitle('All')).'</option>
                                <option data-table="data_sales" value="install" '.($type == "install" ? 'selected' : '').'>'.lang('Install').'</option>
                                <option data-table="data_sales" value="uninstall" '.($type == "uninstall" ? 'selected' : '').'>'.lang('uninstall').'</option>
								
								'. ($filterhtml != "" ? '<option disabled>...............</option>':'') .'
								' . $filterhtml .'
							</select>
						</td>
							<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
					
					<tr>
						<!--td><input type="text" name="group_id" value="'.$group_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td-->
							<td>'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#transactionData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#transactionData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				</td><td colspan=8></td>

						
					</tr>
				</table>
				</form>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle"  class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
			//width 2600
				$tableStr ='<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="transactionTbl" style="width:100%">
					<thead><tr  class="table-row">
						<th class="table-cell">'.lang('Event Type').'</th>
						<th class="table-cell">'.lang('Event Date').'</th>
						<th  class="table-cell">'.lang(ptitle('Trader ID')).'</th>
						<th  class="table-cell">'.lang(ptitle('Trader Alias')).'</th>
						
						' . ( allowView('af-mail',$deal,'fields') ? '<th class="table-cell">'.lang(ptitle('Email')).'</th>' : '' ) . '
						<th class="table-cell">'.lang(ptitle('Trader Status')).'</th>
						<th class="table-cell">'.lang('Country').'</th>
						<th class="table-cell">'.lang('Affiliate ID').'</th>
						<th class="table-cell">'.lang('Affiliate Username').'</th>
						<th class="table-cell">'.lang('Merchant ID').'</th>
						<th class="table-cell">'.lang('Merchant Name').'</th>
						<th style="text-align: left;" class="table-cell">'.lang('Creative ID').'</th>
						<th  class="table-cell"style="text-align: left;">'.lang('Creative Name').'</th>
						<th class="table-cell">'.lang('Type').'</th>
						<th class="table-cell">'.lang('Creative Language').'</th>
						<th class="table-cell">'.lang('Profile ID').'</th>
					
						<th class="table-cell">'.lang('Status').'</th>
						<th class="table-cell">'.lang('Param').'</th>
						<th class="table-cell">'.lang('Param2').'</th>
                     
						<th class="table-cell">'.lang('Admin Notes').'</th>
						'./*.'<th class="table-cell">'.lang('Commission').'</th>'.*/'
					<th  class="table-cell">'.lang('Click Details').'</th>
					</tr></thead>
					<tfoot>
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						' . ( allowView('af-mail',$deal,'fields') ? '<th></th>' : '' ) . '
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
				'.($set->displayLastMessageFieldsOnReports ==1 ? '
						<th></th>
						<th></th>' : '' ).'
						<th></th>
						'./*.'<th style="text-align: left;">'.price($totalTotalCom).'</th>'.*/'
						<th></th>
						
					</tfoot>
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
											level = "affiliate";
											type = "add";
											
											saveReportToMyFav(name, \'install\',user,level,type);
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
		$myReport = lang("Install");
		include "common/ReportFieldsModal.php";
excelExporter($tableStr,'Install');		
		theme();

?>