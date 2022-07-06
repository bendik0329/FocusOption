<?php

ini_set('max_execution_time', 10);

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$pageTitle = lang('Commission Report');
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
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
		</style>
		';
		$filename = "CommissionSummary_data_" . date('YmdHis');
	
	
	if(!isset($commission_type)) $commission_type = "All";
	
        
        
	
	if($userlevel == 'manager')
	$group_id       = $set->userInfo['group_id'];


if (!empty($trader_id) && !empty($merchant_id)){
	$r = mysql_fetch_assoc(function_mysql_query("select affiliate_id from data_reg where merchant_id = " . $merchant_id . " and trader_id = '" . $trader_id . "' limit 1;"));
	$affiliate_id = $r['affiliate_id'];
}


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
                    
                  
                    /**********************************************************/
                    
                    $searchInSql = " BETWEEN '" . $from . "' AND '" . $to . "' ";
                    
                    switch ($commission_type) {
                        case 'all':
                            break;
                        
                        case 'CPL':
                            $searchInSql .= ' AND DealType = "CPL" ';
                            break;
                        
                        case 'CPA':
                            $searchInSql .= ' AND DealType = "CPA" ';
                            break;
                        
                        case 'NetDeposit':
                            $searchInSql .= ' AND DealType = "NetDeposit" ';
                            break;
                        
                        case 'Lots':
                            $searchInSql .= ' AND DealType = "Lots" ';
                            break;
                        
                        case 'PNLRevShare':
                            $searchInSql .= ' AND DealType = "PNL RevShare" ';
                            break;
                        
                        case 'SubAffiliateCom':
                            $searchInSql .= ' AND DealType = "Sub Affiliate Commission" ';
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

		
		foreach($commissionArray as $key=>$com){
			
			
			$listReport .= '
				<tr>
				<td style="text-align: left;">'.$com['merchant_name'].'</td>
				<td style="text-align: left;">'.$com['merchantID'].'</td>
				<td style="text-align: left;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$com['affiliateID'].'" target="_blank">'.$com['affiliateID'].'</a></td>
				<td style="text-align: left;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$com['affiliateID'].'" target="_blank">'.$com['username'].'</a></td>
				<td style="text-align: left;">'. $com['traderID'] .'</a></td>
				<td style="text-align: left;">'.$com['transactionID'].'</td>
				<td style="text-align: left;">'.$com['Date'] .'</a></td>
				<td style="text-align: left;">'. lang(ucwords($com['Type'])) .'</td>
				<td style="text-align: left;">'. price($com['Amount']) .'</td>
				<td style="text-align: left;">'. $com['DealType'] .'</td>
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
		
		
		$set->content .= '
		<div class="normalTableTitle" style="width: 99.5%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form id="frmRepo" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="commission" />
				<table><tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						'.($userlevel == "admin" || $userlevel == "manager"? '<td width=160>'.lang('Affiliate ID').'</td>':'').'
						'.($userlevel == "admin"? '<td style="padding-left:10px">'.lang('Group ID').'</td>':'').'
						<td style="padding-left:10px">'. lang('Trader ID') .'</td>
						<td>'. lang('Commission') .'</td>
						<td></td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="affiliate_id" style="width: 60px; text-align: center;" /-->
					<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
					</td>
					<!--td><input type="text" name="group_id" value="'.$group_id.'" id="group_id" style="width: 60px; text-align: center;" /></td-->
                                        '.($userlevel == 'admin'?'<td width="100" style="padding-left:10px">
                                            <select name="group_id" style="width: 100px;">
                                                <option value="">'.lang('All Groups').'</option>'
                                                . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                    . lang('General') 
                                                . '</option>' 
                                                . listGroups($group_id) 
                                            . '</select>
                                        </td>':'').'
										<td style="padding-left:10px"><input type="text" name="trader_id" value="'.$trader_id.'" id="trader_id" style="width: 60px; text-align: center;" onblur="validateMerchant(this)"/></td>
										<td><select name="commission_type" style="width: 150px;">
					<option '.($commission_type=='' ? ' selected ' : '').' value="All">'.lang('All').'</option>' .
					 (true ? '<option '.($commission_type=='CPL' ? ' selected ' : '').' value="CPL">'.lang('CPL').'</option>' : '') .
					 (true ? '<option '.($commission_type=='CPA' ? ' selected ' : '').' value="CPA">'.lang('CPA / TierCPA / DCPA').'</option>' : '') .
					 (false ? '<option '.($commission_type=='TierCPL' ? ' selected ' : '').' value="TierCPL">'.lang('Tier CPL').'</option>' : '') .
					 (true ? '<option '.($commission_type=='NetDeposit' ? ' selected ' : '').' value="NetDeposit">'.lang('NetDeposit').'</option>' : '') .
					 ( $displayForex==1 ? '<option '.($commission_type=='Lots' ? ' selected ' : '').' value="Lots">'.lang('Lots').'</option>' : '') .
					 ($set->deal_pnl==1 ? '<option '.($commission_type=='PNLRevShare' ? ' selected ' : '').' value="PNLRevShare">'.lang('PNL RevShare').'</option>' : '') .
					 (true ? '<option '.($commission_type=='SubAffiliateCom' ? ' selected ' : '').' value="SubAffiliateCom">'.lang('Sub Affiliate Commission').'</option>' : '') .
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
		<div class="normalTableTitle" class="table">'.lang('Commission Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
		//style="width: 99.5%;"
			$tableStr = '
			<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="commissionTbl">
				<thead><tr  class="table-row">
				<th  class="table-cell">'. lang('Merchant Name') .'</th>
				<th class="table-cell">'. lang('Merchant ID') .'</th>
				<th class="table-cell">'. lang('Affiliate ID') .'</th>
				<th class="table-cell">'. lang('Affiliate Name') .'</th>
				<th class="table-cell">'. lang('Trader ID') .'</th>
				<th class="table-cell">'. lang('Transaction ID') .'</th>
				<th class="table-cell">'. lang('Date') .'</th>
				<th class="table-cell">'. lang('Type') .'</th>
				<th class="table-cell">'. lang('Amount') .'</th>
				<th class="table-cell">'. lang('Location') .'</th>
				<th class="table-cell">'. lang('Commission') .'</th>
				</tr></thead><tfoot><tr>
				<th>'. lang('Total') .'</th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th>'. price($totalAmt) .'</th>
				<th></th>
				<th>'. price($totalCom) .'</th>
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