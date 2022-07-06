<?php

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}


$countryArray = [];
$pageTitle = lang('Country Report');
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
		$filename = "country_data_" . date('YmdHis');
        
        $where = ' 1 = 1 ';
            
                /**
                 * '$group_id' MUST be changed to the actual group-id, when outside the '/admin/' area.
                 */
				 if ($userlevel=='manager')
					 $group_id = $set->userInfo['group_id'];
				 else 
					$group_id  = null;
                $where    .= empty($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';
                
		if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }
                
		if ($affiliate_id) {
                    $where .= " AND affiliate_id='".$affiliate_id."' ";
                }
                
		if ($country_id) {
                    $where  .= " AND country_id='".$country_id."' ";
                }

		$where_main = $where;
		$where_main =  str_replace('affiliate_id','traffic.affiliate_id', $where_main) ;
		$where_main =  str_replace('merchant_id','traffic.merchant_id', $where_main);
		$where_main =  str_replace('country_id','traffic.country_id', $where_main);

		$install_main = $where;
		$install_main =  str_replace('affiliate_id','data_install.affiliate_id', $install_main) ;
		$install_main =  str_replace('merchant_id','data_install.merchant_id', $install_main);
		$install_main =  str_replace('country_id','data_install.country', $install_main);

		$traders_main = $where;
		$traders_main =  str_replace('affiliate_id','ReportTraders.AffiliateID', $traders_main) ;
		$traders_main =  str_replace('merchant_id','ReportTraders.MerchantID', $traders_main);
		$traders_main =  str_replace('country_id','ReportTraders.Country', $traders_main);

		$sql = "SELECT *, sum(traffic.clicks) as total_clicks, sum(traffic.views) as total_views from traffic WHERE ".$where_main ." AND traffic.merchant_id > 0 and traffic.rdate >= '".$from."' AND traffic.rdate <='".$to. "' ". $orderBy ."  GROUP BY country_id";

		$uids = [];
		$data = [];

		$clickArray = [];
		$traficData = function_mysql_query($sql,__FILE__);
		$traficDataFull = mysql_fetch_assoc($traficData);


		while($item = mysql_fetch_assoc($traficData)){
			if (!empty($item['uid'])) {
				$uids[] = $item['uid'];
			}
			$data[$item['id']] = $item;
		}


		$uidString = implode(',', $uids);

		$sqlMerchants = "SELECT * from merchants";
		$MerchantsData = function_mysql_query($sqlMerchants,__FILE__);
		$MerchantsDataItems = [];
		while($item = mysql_fetch_assoc($MerchantsData)){
			$MerchantsDataItems[$item['id']]['id'] = $item['id'];
			$MerchantsDataItems[$item['id']]['name'] = $item['name'];
		}

		$sqlInstallations = "SELECT COUNT(affiliate_id) AS installations from data_install WHERE ".$install_main." AND data_install.rdate >= '".$from."' AND data_install.rdate <='".$to. "' GROUP BY country ";
		$InstallationsData = function_mysql_query($sqlInstallations,__FILE__);
		$InstallationsDataItems = [];
		while($item = mysql_fetch_assoc($InstallationsData)){
            $InstallationsDataItems[$item['country']] = $item['installations'];
		}

		$qq = function_mysql_query($sql,__FILE__);
		while ($trafficRow = mysql_fetch_assoc($qq)) {
			$trafficRow['country_id'] = $trafficRow['country_id']=='' ? '-' : $trafficRow['country_id'];

			if (!isset($countryArray[$trafficRow['country_id']]))  {
				$countryArray[$trafficRow['country_id']]['clicks'] = $trafficRow['total_clicks'];
				$countryArray[$trafficRow['country_id']]['views'] = $trafficRow['total_views'];
			}
			else{
				$countryArray[$trafficRow['country_id']]['clicks'] =  $trafficRow['total_clicks'];
				$countryArray[$trafficRow['country_id']]['views'] = $trafficRow['total_views'];
			}

			$countryArray[$trafficRow['country_id']]['country'] = $trafficRow['country_id'];
			$countryArray[$trafficRow['country_id']]['type'] = $trafficRow['type'];
			$countryArray[$trafficRow['country_id']]['merchant'] = $MerchantsDataItems[$trafficRow['merchant_id']];
		}

		$sqlReportTraders = "SELECT *, SUM(Volume) as Volume, SUM(WithdrawalAmount) as WithdrawalAmount,
  			SUM(CASE TraderStatus WHEN 'leads' THEN 1 ELSE 0 END) leads,
  			SUM(CASE TraderStatus WHEN 'demo' THEN 1 ELSE 0 END) demo,
  			SUM(CASE TraderStatus WHEN 'real' THEN 1 ELSE 0 END) reals,
  			SUM(NextDeposits) as NextDeposits,
  			SUM(FTDAmount) as FTDAmount,
  			SUM(BonusAmount) as BonusAmount,
  			SUM(ChargeBackAmount) as ChargeBackAmount,
  			SUM(NetDeposit) as NetDeposit,
  			SUM(PNL) as PNL,
  			SUM(Commission) as Commission,
  			SUM(DepositAmount) as DepositAmount,
  			SUM(CASE WHEN (PNL > 0 OR NextDeposits > 0 OR Volume > 0 ) THEN 1 ELSE 0 END) as Qftd,
  			SUM(CASE FirstDeposit WHEN FirstDeposit > '0000-00-00 00:00:00' THEN 1 ELSE 0 END) as FirstDeposit
  			FROM ReportTraders
  			WHERE Date >= '".$from."' AND ".$traders_main." GROUP BY Country";
		$ReportTradersData = function_mysql_query($sqlReportTraders,__FILE__);
var_dump($sqlReportTraders);
		$ReportTradersDataItems = [];
		while($item = mysql_fetch_assoc($ReportTradersData)){
            $item['Country'] = $item['Country']=='' ? '-' : $item['Country'];

            $countryArray[$item['Country']]['country'] = $item['Country'];
            $countryArray[$item['Country']]['type'] = $item['Type'];
            $countryArray[$item['Country']]['volume'] = $item['Volume'];
            $countryArray[$item['Country']]['withdrawal'] = $item['WithdrawalAmount'];
            $countryArray[$item['Country']]['leads'] = $item['leads'];
            $countryArray[$item['Country']]['demo'] = $item['demo'];
            $countryArray[$item['Country']]['real'] = $item['reals'];

            $countryArray[$item['Country']]['cpi'] = $InstallationsDataItems[$item['Country']];

            $countryArray[$item['Country']]['depositingAccounts'] = $item['NextDeposits'];
            $countryArray[$item['Country']]['real_ftd'] = $item['demo'];
            $countryArray[$item['Country']]['ftd'] = $item['FirstDeposit'];
            $countryArray[$item['Country']]['ftd_amount'] = $item['FTDAmount'];
            $countryArray[$item['Country']]['sumDeposits'] = $item['DepositAmount'];
            $countryArray[$item['Country']]['bonus'] = $item['BonusAmount'];
            $countryArray[$item['Country']]['chargeback'] = $item['ChargeBackAmount'];
            $countryArray[$item['Country']]['netRevenue'] = $item['NetDeposit'];
            $countryArray[$item['Country']]['pnl'] = $item['PNL'];
            $countryArray[$item['Country']]['totalCom'] = $item['Commission'];
            $countryArray[$item['Country']]['Qftd'] = $item['Qftd'];
        }

					//DISPLAY Report
					foreach($countryArray as $data){
						if ($data['views']>0 || $data['clicks']>0 || $data['leads'] >0 || $data['demo'] >0 || $data['real'] >0 || $data['cpi']
						 || $data['depositingAccounts'] >0 
						 || $data['real_ftd'] >0 
						 || $data['ftd'] >0 
						 || $data['Qftd'] >0 
						 || $data['ftd_amount'] >0 
						 || $data['real_ftd_amount'] >0 
						 || $data['chargeback'] >0 
						 || $data['withdrawal'] >0 
						 || $data['bonus'] >0 
						 || $data['totalCom'] >0 
						 || $data['netRevenue'] >0 
						 || $data['volume'] >0 
						){
						    

							$country = $allCountriesArray[$data['country']];
							$country = empty($country)?$data['country']:$country;
							if($country == "-"){
							    $country = $allCountriesArray[''];
							    
							}
							 
							$listReport .= '
								<tr>
									<td style="text-align: left;" title="'.$country.'">'.$country.'</td>
								
								
									<td>'.@number_format($data['views'],0).'</td>
									<td>'.@number_format($data['clicks'],0).'</td>
									'.($set->deal_cpi?'<td>'.@number_format($data['cpi'],0).'</td>':'').'
									<td>'.@number_format(($data['clicks']/$data['views'])*100,2).' %</td>
									<td>'.@number_format(($data['real']/$data['clicks'])*100,2).' %</td>
									<td>'.@number_format(($data['ftd']/$data['clicks'])*100,2).' %</td>
									<td>'.@price($data['totalCom']/$data['clicks']).'</td>
									<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=lead&country_id='.$data['country'].'">'.$data['leads'].'</a></td>
									<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=demo&country_id='.$data['country'].'">'.$data['demo'].'</a></td>
									<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=real&country_id='.$data['country'].'">'.$data['real'].'</a></td>
									<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=ftd&country_id='.$data['country'].'">'.$data['ftd'].'</a></td>
									<td>'.price($data['ftd_amount']).'</td>
									<td><a href="/'.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'type=totalftd&country_id='.$data['country'].'">'.$data['real_ftd'].'</a></td>
								<!--	<td>'.price($data['real_ftd_amount']).'</td>
									<td><a href="/'.$userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.(!is_null($merchant)?$merchant:0).'&type=deposit&country_id='.$data['country'].'">'.$data['depositingAccounts'].'</a></td> -->
									<td>'.price($data['sumDeposits']).'</td>
									<td style="text-align: center;">'.price($data['volume']).'</td>
									<td>'.price($data['bonus']).'</td>
									<td>'.price($data['withdrawal']).'</td>
									<td>'.price($data['chargeback']).'</td>
									<td style="text-align: center;">'.price($data['netRevenue']).'</td>
									'.($set->deal_pnl?'<td style="text-align: center;">'.price($data['pnl']).'</td>':'').'
									'.('<td style="text-align: center;">'.($data['Qftd']).'</td>').'
									<td>'.price($data['totalCom']).'</td>
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
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="act" value="country" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Country').'</td>
					<td width=160>'.lang('Affiliate ID').'</td>
					
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
					<td><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" /-->
					<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
					</td>
					
					<td style="padding-left:20px"><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer;" onclick="$(\'#countryData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer;" onclick="$(\'#countryData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle"  class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
			//width 2400
				$tableStr='<table  class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="countryTbl">
					<thead><tr  class="table-row">
						
						<th  class="table-cell">'.lang('Country').'</th>
						
						
						<th class="table-cell">'.lang('Impressions').'</th>
						<th class="table-cell">'.lang('Clicks').'</th>'
						.($set->deal_cpi?'<th class="table-cell">'.lang('Installation').'</th>':'').'
						<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
						<th class="table-cell">'.lang(ptitle('Click to Account')).'</th>
						<th class="table-cell">'.lang(ptitle('Click to Sale')).'</th>
						<th class="table-cell">EPC</th>
						<th class="table-cell">'.lang(ptitle('Lead')).'</th>
						<th class="table-cell">'.lang(ptitle('Demo')).'</th>
						<th class="table-cell">'.lang(ptitle('Accounts')).'</th>
						<th class="table-cell">'.lang('FTD').'</th>
						<th class="table-cell">'.lang('FTD Amount').'</th>
					<!--	<th class="table-cell">'.lang('RAW FTD').'</th> 
						<th class="table-cell">'.lang('RAW FTD Amount').'</th> -->
						<th class="table-cell">'.lang('Total Deposits').'</th>
						<th class="table-cell">'.lang('Deposit Amount').'</th>
						<th class="table-cell">'.lang('Volume').'</th>
						<th class="table-cell">'.lang('Bonus Amount').'</th>
						<th class="table-cell">'.lang('Withdrawal Amount').'</th>
						<th class="table-cell">'.lang('ChargeBack Amount').'</th>
						<th class="table-cell">'.lang(ptitle('Net Revenue')).'</th>
						'.($set->deal_pnl?'<th class="table-cell">'.lang(ptitle('PNL')).'</th>':'').'
						<th class="table-cell">'.lang(ptitle('Active Traders')).'</th>
						<th class="table-cell">'.lang('Commission').'</th>
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th><b>'.lang('Total').':</b></th>
					
						<th>'.$totalImpressions.'</th>
						<th>'.$totalClicks.'</th>
						'.($set->deal_cpi?'<th>'.$totalCPI.'</th>':'').'
						<th>'.@number_format(($totalClicks/$totalViews)*100,2).' %</th>
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
						'.($set->deal_pnl?'<th>'.price($totalSumPnl).'</th>':'').'
						<th>'.$totalQFTD.'</th>
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
					txt = "<table id=\'countryData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#countryTbl")[0].config.rowsCopy).each(function() {
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
											
											saveReportToMyFav(name, \'country\',user,level,type);
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
			
		//excelExporter($tableStr, 'Country');
		$set->content.=$tableStr.'</div>'.getPager();
		
			//MODAL
		$myReport = lang("Country");
		include "common/ReportFieldsModal.php";
		
		theme();

?>
