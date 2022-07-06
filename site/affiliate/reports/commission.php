<?php
//Prevent direct browsing of report
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/affiliate" );
}



$pageTitle = lang('Commission Report');
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
		$filename = "CommissionSummary_data_" . date('YmdHis');
	
	
	if(!isset($commission_type)) $commission_type = "All";
	
	
	
$affiliate_id = $set->userInfo['id'];

		$listReport = '';
                
                // List of wallets.
                $arrWallets = [];
                // $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE 1 = 1 AND valid = 1;";
				$merchant_id = isset($merchant_id) && $merchant_id>0 ? $merchant_id : 0;
				$merchantsA  = getMerchants($merchant_id,1);
                // while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
				foreach ($arrWallets as $arrWallet){
                    $arrWallets[$arrWallet['wallet_id']] = false;
                    unset($arrWallet);
                }
                
		$l = -1;
					
					
		$displayForex = 0;
		$tradersProccessedForLots= array();
		$tradersProccessedForPNL= array();
		
		$commissionArray = array();
		

		
                $exclude_amount_column = false;
                
		// $sql = "SELECT * FROM merchants WHERE valid='1' ORDER BY type, pos";
		// $qq = function_mysql_query($sql,__FILE__);
		foreach ($merchantsA as $ww){
		
		// while ($ww = mysql_fetch_assoc($qq)) {
		
		if (strtolower($ww['producttype'])=='forex')
							$displayForex = 1;			
						
						
                    // Check if this is a first itaration on given wallet.
                    if ($set->multiMerchantsPerTrader==1)
						$needToSkipMerchant = $arrWallets[$ww['wallet_id']];
				else 
					$needToSkipMerchant= false;
				
                    
                    $formula  = $ww['rev_formula'];
                    $fromDate = $from;
                    $toDate   = $to;
                    
                    $l++;
                    $ftdUsers = '';
                    $netRevenue = 0;
                    $totalCom=0;
                    $ftd=0;
                    $totalLeads = 0;
                    $totalDemo = 0;
                    $totalReal = 0;
                    $ftd_amount['amount']=0;
                    $real_ftd = 0;
                    $real_ftd_amount = 0;
                    $bonus = 0;
                    $withdrawal = 0;
                    $chargeback = 0;
                    $depositingAccounts = 0;
                    $sumDeposits = 0;
                    $totalLots = 0;
                    $volume = 0;
                    $merchantName = strtolower($ww['name']);
                    $merchantID = $ww['id'];
                    
                    
                    $arrRanges = [];
                    
                    switch ($display_type) {
                        case 'monthly':
                            $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_MONTHLY);
                            break;
                        case 'weekly':
                            $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_WEEKLY);
                            break;
                        case 'daily':
                            $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_DAILY_RANGE);
                            break;
                        default:
                            $arrRanges = DatesRange::create($from, $to, DatesRange::MODE_TYPE_NONE);
                            break;
                    }
                    
                  
                            $formula  = $ww['rev_formula'];
                            $fromDate = $from;
                            $toDate   = $to;
                            
                            $ftdUsers = '';
                            $netRevenue = 0;
                            $totalCom=0;
                            $ftd=0;
                            $totalLeads = 0;
                            $totalDemo = 0;
                            $totalReal = 0;
                            $ftd_amount['amount']=0;
                            $real_ftd = 0;
                            $real_ftd_amount = 0;
                            $bonus = 0;
                            $lots = 0;
                            $withdrawal = 0;
                            $chargeback = 0;
                            $depositingAccounts = 0;
                            $sumDeposits = 0;
                            $volume = 0;
							$depositsAmount=0;
                            $merchantName = strtolower($ww['name']);
                            $merchantID = $ww['id'];
                            
                            
                            
                            /**********************************************************/
                    
                            $searchInSql = " BETWEEN '" . $from . "' AND '" . $to . "' ";
                            
                            switch($commission_type){
                                case "CPA":
                                    $searchInSql .= " AND c.DealType = 'CPA'";
                                    break;
                                    
                                case "NetDeposit":
                                    $searchInSql .= " AND c.DealType = 'NetDeposit'";
                                    break;
                                    
                                case "PNLRevShare":
                                    $searchInSql .= " AND c.DealType = 'PNL RevShare'";
                                    break;
                            }
                            
                            $sql_commissions = "SELECT c.*, aff.username, m.name as merchant_name FROM commissions c "
                                            . " INNER JOIN affiliates aff ON c.affiliateID = aff.id "
                                            . " INNER JOIN merchants m ON c.merchantID = m.id "
                                            . " LEFT JOIN traders_tag as tg ON tg.trader_id = c.traderID"
                                            . " WHERE tg.trader_id IS NULL AND c.merchantID = '" . $ww['id'] . "' AND "
                                            . " Date " . $searchInSql 
                                            . (isset($affiliate_id) && $affiliate_id != '' ? ' AND c.affiliateID = ' . $affiliate_id . ' ' : '')
                                            . (isset($trader_id) && $trader_id != '' ? ' AND c.traderID = ' . $trader_id . ' ' : '')
        									. (isset($group_id) && $group_id != '' ? ' AND aff.group_id = ' . $group_id . ' ' : '')
        									. " ORDER BY c.Date DESC"
        									;
        									
                            
                            $commissionArrayResult = function_mysql_query($sql_commissions);
                            while ($commissionResultItem = mysql_fetch_assoc($commissionArrayResult)) {
                                $commissionArray[] = $commissionResultItem;
                                if($commissionResultItem['DealType'] == 'NetDeposit' || $commissionResultItem['Type'] == 'PNL'){
                                   $exclude_amount_column = true; 
                                }
                            }
			
                            /**********************************************************/
				                

								


				

                                


                        
                    // Mark given wallet as processed.
                    $arrWallets[$ww['wallet_id']] = true;
		}
                
		
		//echo "<pre>";print_r($commissionArray);die;

		
		
		
		
		$totalCom = 0;
		$totalAmt = 0;
		
		
		function cmp($a, $b)
		{
			return strcmp($a["rdate"], $b["rdate"]);
		}

		usort($commissionArray, "cmp");
		
		$l=0;
		// $commissionTypes = array();
		
		foreach($commissionArray as $key=>$com){
			
		/* 	if (!isset($commissionTypes[strtolower($com['location'])]))
				$commissionTypes[strtolower($com['location'])]=1; */
			
			
			$listReport .= '
				<tr>
				<td style="text-align: left;">'.$com['merchant_name'].'</td>
				<td style="text-align: left;">'.$com['merchantID'].'</td>
				<td style="text-align: left;">'. $com['traderID'] .'</a></td>
				<td style="text-align: left;">'.$com['transactionID'].'</td>
				'./*.'<td style="text-align: left;">'.$com['Date'].'</a></td>'.*/'
				<td style="text-align: left;">'. lang(ucwords($com['Type'])) .'</td>
				'.((!$exclude_amount_column)?'<td style="text-align: left;">'. price($com['Amount']) .'</td>':'').'
				<td style="text-align: left;">'. $com['DealType'] .($com['level']>0?' (Level '. $com['level'].')':'').'</td>
				<td style="text-align: left;">'. price($com['Commission']) .'</td>
			</tr>';
			
			$totalCom  += $com['Commission'];
			$totalAmt += $com['Amount'];
			$l++;
		}
		
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
		}
                
		$set->sortTable = 1;
		$set->totalRows = $l;
		
		
			
		
		 
		 $affDeals=array();
		 foreach ($merchantsArr as $mer) {
			
			
		$dealls =  getExistingDealTypesForAffiliateArray($mer,$set->userInfo['id']);
		foreach ($dealls as $deall){
		if (!empty($deall)){
		
			$affDeals[strtolower($deall)] = $deall;
		}
		}
		 }
		

		
		$set->content .= '
		<div class="normalTableTitle" style="width: 99.5%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form id="frmRepo" method="get">
			<input type="hidden" name="act" value="commission" />
				<table><tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						'.($userlevel == "admin"? '<td>'.lang('Group ID').'</td>':'').'
						<td>'. lang('Trader ID') .'</td>
						<td>'. lang('Commission') .'</td>
						<td></td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<!--td><input type="text" name="group_id" value="'.$group_id.'" id="group_id" style="width: 60px; text-align: center;" /></td-->
                                        '.($userlevel == 'admin'?'<td width="100">
                                            <select name="group_id" style="width: 100px;">
                                                <option value="">'.lang('All Groups').'</option>'
                                                . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                    . lang('General') 
                                                . '</option>' 
                                                . listGroups($group_id) 
                                            . '</select>
                                        </td>':'').'
					<td><input type="text" name="trader_id" value="'.$trader_id.'" id="trader_id" style="width: 60px; text-align: center;" onblur="validateMerchant(this)"/></td>
					<td><select name="commission_type" style="width: 150px;">
					 <option '.($commission_type=='' ? ' selected ' : '').' value="All">'.lang('All').'</option>' .
					 (isset($affDeals['cpl']) ? '<option '.($commission_type=='CPL' ? ' selected ' : '').' value="CPL">'.lang('CPL').'</option>' : '') .
					(isset($affDeals['cpa']) || isset($affDeals['dcpa']) ? '<option '.($commission_type=='CPA' ? ' selected ' : '').' value="CPA">'.lang('CPA / TierCPA / DCPA').'</option>' : '') .
					(isset($affDeals['tiercpl']) ? '<option '.($commission_type=='TierCPL' ? ' selected ' : '').' value="TierCPL">'.lang('Tier CPL').'</option>' : '') .
					(isset($affDeals['revenue']) ? '<option '.($commission_type=='NetDeposit' ? ' selected ' : '').' value="NetDeposit">'.lang('NetDeposit').'</option>' : '') .
					(isset($affDeals['lots']) && $displayForex==1 ? '<option '.($commission_type=='Lots' ? ' selected ' : '').' value="Lots">'.lang('Lots').'</option>' : '') .
					(isset($affDeals['pnl']) && $set->deal_pnl==1 ? '<option '.($commission_type=='PNLRevShare' ? ' selected ' : '').' value="PNLRevShare">'.lang('PNL RevShare').'</option>' : '') .
					(isset($affDeals['SubAffiliateCom']) && $set->deal_pnl==1 ? '<option '.($commission_type=='SubAffiliateCom' ? ' selected ' : '').' value="SubAffiliateCom">'.lang('Sub Affiliate Commission').'</option>' : '') .
'

					</select></td>
					<td><input type="button" value="'.lang('View').'" onclick="validateForm()"/></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#commissionData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#commissionData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle"  class="table">'.lang('Commission Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
			$tableStr = '
			<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="commissionTbl">
				<thead><tr  class="table-row">
				<th class="table-cell">'. lang('Merchant Name') .'</th>
				<th  class="table-cell">'. lang('Merchant ID') .'</th>
				<th class="table-cell">'. lang('Trader ID') .'</th>
				<th class="table-cell">'. lang('Transaction ID') .'</th>
				'./*.'<th class="table-cell">'. lang('Date') .'</th>'..*/'
				<th class="table-cell">'. lang('Type') .'</th>
                                '.((!$exclude_amount_column)?'<th class="table-cell">'. lang('Amount') .'</th>':'').'
				<th class="table-cell">'. lang('Location') .'</th>
				<th class="table-cell">'. lang('Commission') .'</th>
				</tr></thead><tfoot><tr>
				<th>'. lang('Total') .'</th>
				<th></th>
				<th></th>
				<th></th>
				<!--<th></th>-->
				<th></th>
                                '.((!$exclude_amount_column)?'<th>'. price($totalAmt) .'</th>':'').'
				<th></th>
				<th>'. price($totalCom) .'</th>
				</tr></tfoot>
				<tbody>
				'.$listReport.'
			</table>
			<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
			';
			
			if(!empty($listReport)){
				$set->content .='<script>
				$(document).ready(function(){
					
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'commissionData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#commissionTbl")[0].config.rowsCopy).each(function() {
						txt += "<tr>" + $(this).html()+"</tr>";
					});
					txt += "</tbody>";
					txt += "<tfoot>" + tfoot + "</tfoot>";
					txt += "</table>";
					$("body").append("<div style=\'display:none\'>"+ txt +"</div>");
				});
					</script>';
				}
				$set->content .= '<script>
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
											
											saveReportToMyFav(name, \'commission\',user,level,type);
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
		//excelExporter($tableStr, 'Quick');
		$set->content .= $tableStr . '</div>' . getPager();
		
			//MODAL
		$myReport = lang("Commission");
		include "common/ReportFieldsModal.php";		
		
		theme();
		
?>
