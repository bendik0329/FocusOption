<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];

if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$pageTitle = lang('Clicks Report');

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
		$page = (isset($page) || !empty($page))?$page:1;
		$set->page = $page;
		
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

		$filename = "clicks_data_" . date('YmdHis');
		$start_limit = ($page == 1) ? 0 : $set->rowsNumberAfterSearch * ($page -1);
		$end_limit = $set->rowsNumberAfterSearch;// * $page;

$sql = "SELECT id, name FROM affiliates_profiles WHERE valid =1";
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
                if ($userlevel=='manager'){
					$group_id  = $set->userInfo['group_id'];

				}
				else
					$group_id  = null;


			// $where    .= empty($group_id) ? '' : ' AND group_id = ' . $group_id . ' ';



			if ($merchant_id) {
                    $where .= " AND merchant_id='".$merchant_id."' ";
                }



				if (!empty($group_id)) {
			$where .= " AND group_id='".$group_id."' ";
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
			$orderBy = "ORDER BY traffic.id DESC";
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
		 $where_main =  str_replace('group_id','t.group_id', $where_main) ;
		 $where_main =  str_replace('profile_id','t.profile_id', $where_main) ;
		 $where_main =  str_replace('banner_id','t.banner_id', $where_main) ;

		 $type_filter = "";
		 if($type == 'clicks' || empty($type))
		 $type_filter = ' and traffic.clicks > 0';
		 else if($type == "views")
		 $type_filter = ' and traffic.views > 0';

		 // Without limit for know, all records count for that conditions
        $sqlCount = "SELECT id from traffic WHERE ".$where . $type_filter ." AND traffic.merchant_id > 0". (!empty($unique_id) ? ' and traffic.uid = ' . $unique_id :'')." and traffic.rdate >= '".$from."' AND traffic.rdate <= '".$to. "' ". $orderBy ." ";

        $sql = "SELECT * from traffic WHERE ".$where . $type_filter ." AND traffic.merchant_id > 0". (!empty($unique_id) ? ' and traffic.uid = ' . $unique_id :'')." and traffic.rdate >= '".$from."' AND traffic.rdate <='".$to. "' ". $orderBy ."  limit " . $start_limit. ", " . $end_limit;

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

        // TODO: In current table is not used (commented for lesser request to database
      /*  $sqlLanguages = "SELECT * from languages";
        $languagesData = function_mysql_query($sqlLanguages,__FILE__);*/

        $totalRecords = function_mysql_query($sqlCount,__FILE__);
        $set->total_records = mysql_num_rows($totalRecords);

        $sqlMerchants = "SELECT * from merchants";
        $MerchantsData = function_mysql_query($sqlMerchants,__FILE__);
        $MerchantsDataItems = [];
        while($item = mysql_fetch_assoc($MerchantsData)){
            $MerchantsDataItems[$item['id']]['id'] = $item['id'];
            $MerchantsDataItems[$item['id']]['name'] = $item['name'];
        }

        $sqlMerchantsCreative = "SELECT * from merchants_creative";
        $MerchantsCreativeData = function_mysql_query($sqlMerchantsCreative,__FILE__);
        $MerchantsCreativeDataItems = [];
        while($item = mysql_fetch_assoc($MerchantsCreativeData)){
            $MerchantsCreativeDataItems[$item['id']]['id'] = $item['id'];
            $MerchantsCreativeDataItems[$item['id']]['title'] = $item['title'];
            $MerchantsCreativeDataItems[$item['id']]['url'] = $item['url'];
        }

        $sqlAffiliates = "SELECT * from affiliates";
        $AffiliatesData = function_mysql_query($sqlAffiliates,__FILE__);
        $AffiliatesDataItems = [];
        while($item = mysql_fetch_assoc($AffiliatesData)){
            $AffiliatesDataItems[$item['id']]['id'] = $item['id'];
            $AffiliatesDataItems[$item['id']]['username'] = $item['username'];
        }

        $sqlReportTraders = "SELECT * FROM ReportTraders  WHERE Date >= '".$from."' AND ClickDetails IN (".$uidString.")";
        $ReportTradersData = function_mysql_query($sqlReportTraders,__FILE__);
        
        $ReportTradersDataItems = [];
        while($item = mysql_fetch_assoc($ReportTradersData)){
            $ReportTradersDataItems[$item['ClickDetails']]['volume'] += $item['Volume'];
            $ReportTradersDataItems[$item['ClickDetails']]['trader_id'] = $item['TraderID'];
            $ReportTradersDataItems[$item['ClickDetails']]['trader_name'] = $item['TraderAlias'];

            switch ($item['Type']) {
                case 'lead': $ReportTradersDataItems[$item['ClickDetails']]['leads'] += 1;
                    break;
                case 'demo': $ReportTradersDataItems[$item['ClickDetails']]['demo'] += 1;
                    break;
                case 'real': $ReportTradersDataItems[$item['ClickDetails']]['real'] += 1;
                    break;
            }

            $ReportTradersDataItems[$item['ClickDetails']]['sale_status'] += $item['SaleStatus'];
            $ReportTradersDataItems[$item['ClickDetails']]['ftd'] += $item['FirstDeposit'];
            $ReportTradersDataItems[$item['ClickDetails']]['depositingAccounts'] += $item['TotalDeposits'];
            $ReportTradersDataItems[$item['ClickDetails']]['sumDeposits'] += $item['DepositAmount'];
            $ReportTradersDataItems[$item['ClickDetails']]['bonus'] += $item['BonusAmount'];
            $ReportTradersDataItems[$item['ClickDetails']]['withdrawal'] += $item['WithdrawalAmount'];
            $ReportTradersDataItems[$item['ClickDetails']]['chargeback'] += $item['ChargeBackAmount'];
            $ReportTradersDataItems[$item['ClickDetails']]['netRevenue'] += $item['NetDeposit'];
            $ReportTradersDataItems[$item['ClickDetails']]['pnl'] += $item['PNL'];

            if ($item['TotalDeposits'] > 0 || $item['Volume'] > 0 || $item['PNL'] > 0) {
                $ReportTradersDataItems[$item['ClickDetails']]['Qftd'] ++;
            }

            $ReportTradersDataItems[$item['ClickDetails']]['totalCom'] += $item['Commission'];

        }

       // while($item = mysql_fetch_assoc($traficData)){
        foreach($data as $item){
            $clickArray[$item['id']]['traffic_id'] = $item['id'];
            $clickArray[$item['id']]['uid'] = $item['uid'];
            $clickArray[$item['id']]['clicks'] = $item['clicks'];
            $clickArray[$item['id']]['views'] = $item['views'];
            $clickArray[$item['id']]['traffic_date'] = $item['rdate'];

            $clickArray[$item['id']]['type'] = $item['type'];

            $clickArray[$item['id']]['banner_id'] = $item['banner_id'];
            $clickArray[$item['id']]['banner_title'] = $MerchantsCreativeDataItems[$item['banner_id']]['title'];
            $clickArray[$item['id']]['banner_url'] = $MerchantsCreativeDataItems[$item['banner_id']]['url'];
            $clickArray[$item['id']]['profile_id'] = $item['profile_id'] == 0 ? '' : $item['profile_id'];
            $clickArray[$item['id']]['param'] = $item['param'];
            $clickArray[$item['id']]['param2'] = $item['param2'];
            $clickArray[$item['id']]['param3'] = $item['param3'];
            $clickArray[$item['id']]['param4'] = $item['param4'];
            $clickArray[$item['id']]['param5'] = $item['param5'];
            $clickArray[$item['id']]['refer_url'] = $item['refer_url'];
        //    $clickArray[$item['id']]['language'] = $languagesData[$item['language']]['title'];
            $clickArray[$item['id']]['country'] = $item['country_id'];
            $clickArray[$item['id']]['ip'] = $item['ip'];
            $clickArray[$item['id']]['merchant_name'] = $MerchantsDataItems[$item['merchant_id']]['name'];
            $clickArray[$item['id']]['affiliate_username'] = $AffiliatesDataItems[$item['affiliate_id']]['username'];
            $clickArray[$item['id']]['affiliate_id'] = $item['affiliate_id'];

            $clickArray[$item['id']]['platform'] = $item['platform'];

            $clickArray[$item['id']]['volume'] = $ReportTradersDataItems[$item['uid']]['volume'];
            $clickArray[$item['id']]['trader_id'] = $ReportTradersDataItems[$item['uid']]['trader_id'];
            $clickArray[$item['id']]['trader_name'] = $ReportTradersDataItems[$item['uid']]['trader_name'];
            $clickArray[$item['id']]['leads'] = $ReportTradersDataItems[$item['uid']]['leads'];
            $clickArray[$item['id']]['demo'] = $ReportTradersDataItems[$item['uid']]['demo'];
            $clickArray[$item['id']]['real'] = $ReportTradersDataItems[$item['uid']]['real'];
            $clickArray[$item['id']]['sale_status'] = $ReportTradersDataItems[$item['uid']]['sale_status'];
            $clickArray[$item['id']]['ftd_amount'] = $ReportTradersDataItems[$item['uid']]['ftd_amount'];

            $clickArray[$item['id']]['ftd'] = $ReportTradersDataItems[$item['uid']]['ftd'];
            $clickArray[$item['id']]['depositingAccounts'] = $ReportTradersDataItems[$item['uid']]['depositingAccounts'];
            $clickArray[$item['id']]['sumDeposits'] = $ReportTradersDataItems[$item['uid']]['sumDeposits'];
            $clickArray[$item['id']]['bonus'] = $ReportTradersDataItems[$item['uid']]['bonus'];
            $clickArray[$item['id']]['withdrawal'] = $ReportTradersDataItems[$item['uid']]['withdrawal'];
            $clickArray[$item['id']]['chargeback'] = $ReportTradersDataItems[$item['uid']]['chargeback'];
            $clickArray[$item['id']]['netRevenue'] = $ReportTradersDataItems[$item['uid']]['netRevenue'];
            $clickArray[$item['id']]['pnl'] = $ReportTradersDataItems[$item['uid']]['pnl'];
            $clickArray[$item['id']]['Qftd'] = $ReportTradersDataItems[$item['uid']]['Qftd'];
            $clickArray[$item['id']]['totalCom'] = $ReportTradersDataItems[$item['uid']]['totalCom'];

            if(empty($item['os']))
                $clickArray[$item['id']]['platform'] = "";


            $clickArray[$item['id']]['os'] = $item['os'];
            $clickArray[$item['id']]['osVersion'] = $item['osVersion'];

            $clickArray[$item['id']]['browser'] = $item['browser'];
            $clickArray[$item['id']]['browserVersion'] = $item['broswerVersion'];
        }

		foreach($clickArray as $data){
			$refer_url = $data['refer_url'];
		    if(strlen($data['refer_url'])>50)
				$refer_url = substr($data['refer_url'],0,49). "...";

			if(!empty($data['country']) && strlen($data['country'])==2){
				if (empty($allCountriesArray)){
					$allCountriesArray = getDBCountries();
				}

				$country_name = $allCountriesArray[$data['country'] ];
			}
			else
				$country_name = "";
			if(strtolower($country)=='any'){
				$country_name = "";
			}
			$listReport .= '
			<tr>
				<td style="text-align: left;">'.$data['traffic_id'].'</td>
				<td style="text-align: left;">'.$data['uid'] .'</td>
				<td style="text-align: center;">'.@number_format($data['views'],0).'</td>
				<td style="text-align: center;">'.@number_format($data['clicks'],0).'</td>
				<td style="text-align: left;">'.(!empty($data['affiliate_username']) ? '<a href="/'. $userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_id'] .'</a>' : $data['affiliate_id']).'</td>
				<td style="text-align: left;">'.(!empty($data['affiliate_username']) ? '<a href="/'. $userlevel .'/affiliates.php?act=new&id='.$data['affiliate_id'].'" target="_blank">'.$data['affiliate_username'] .'</a>' : $data['affiliate_username']).'</td>
				<td style="text-align: left;">'.$data['traffic_date'] .'</td>
				<td style="text-align: left;">'.ucwords($data['type']).'</td>
				<td style="text-align: left;">'. $data['merchant_name'] .'</td>
				<td style="text-align: left;"><a href="'.$data['banner_url'] . '" target="_blank">'. $data['banner_title'] . ' ('.$data['banner_id'] .')</a></td>
				<td style="text-align: left;">'. $data['profile_id'] .'</td>
				<td style="text-align: left;">'. $listProfiles[$data['profile_id']] .'</td>
				<td style="text-align: left;">'. $data['param'] .'</td>
				<td style="text-align: left;">'. $data['param2'] .'</td>
				<td style="text-align: left;">'. $data['param3'] .'</td>
				<td style="text-align: left;">'. $data['param4'] .'</td>
				<td style="text-align: left;">'. $data['param5'] .'</td>
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
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=lead">'.$data['leads'].'</a></td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=demo">'.$data['demo'].'</a></td>
				<td style="text-align: left;">'. $data['sale_status'] .'</td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=real">'.$data['real'].'</a></td>
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=ftd&trader_id='. $data['trader_id'] .'">'.$data['ftd'].'</a></td>
				<td>'.price($data['ftd_amount']).'</td>
			<!--	<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=totalftd&trader_id='. $data['trader_id'] .'">'.$data['real_ftd'].'</a></td>
				<td>'.price($data['real_ftd_amount']).'</td> -->
				<td><a href="/'. $userlevel .'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=deposit">'.$data['depositingAccounts'].'</a></td>
				<td>'.price($data['sumDeposits']).'</td>
				<td style="text-align: center;">'.price($data['volume']).'</td>
				<td>'.price($data['bonus']).'</td>
				<td>'.price($data['withdrawal']).'</td>
				<td>'.price($data['chargeback']).'</td>
				<td style="text-align: center;">'.price($data['netRevenue']).'</td>
				'.($set->deal_pnl?'<td style="text-align: center;">'.price($data['pnl']).'</td>':'').'
				<td><a href="/'. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$data['id'].'&type=activeTrader&trader_id='. $data['trader_id'] .'">'.$data['Qftd'].'</a></td>
				<td>'.price($data['totalCom']).'</td>
			</tr>';
		}


                if ($l > 0) {
                    $set->sortTableScript = 1;
                }

                $set->totalRows  = $l;
		$set->sortTable  = 1;

		
		$set->content   .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get" id="testForm" onsubmit = "return submitReportsForm(this)">
			<input type="hidden" name="sortBy" id="sortBy" value="" />
			<input type="hidden" name="sortOrder" id="sortOrder" value="" />
			<input type="hidden" name="act" value="clicks" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Unique ID').'</td>
					<td width=160>'.lang('Affiliate ID').'</td>
					<td style="padding-left:20px">'.lang('Trader ID').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="unique_id" value="'.$unique_id.'" /></td>
					<td><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" /-->
					<div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
					</td>
					<!--td style="padding-left:20px"><input type="text" name="trader_id" value="'.$trader_id.'" onblur="validateMerchant(this)"/></td-->
					<td style="padding-left:20px"><input type="text" name="trader_id" value="'.$trader_id.'" /></td>
					<!--td><input type="button" value="'.lang('View').'" onclick="validateForm()"/></td-->
					<td><input type="submit" value="'.lang('View').'" /></td>
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
			
			<div class="normalTableTitle" class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
			//width 2400
				$tableStr='<table class="table tablesorter mdlReportFields table" border="0" cellpadding="0" cellspacing="0" id="clicksTbl">
					<thead><tr class="table-row">
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="id">'.lang('ID').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="uid"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="uid">'.lang('UID').'</th>
						
						<th style="text-align: center;" class=" table-cell header ' . ($sortBy =="views"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"")  .'" data-sort="views">'.lang('Impression').'</th>
						<th style="text-align: center;" class=" table-cell header '. ($sortBy =="clicks"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="clicks">'.lang('Click').'</th>
						
						<th style="text-align: left;" class=" table-cell header  '. ($sortBy =="affiliate_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="affiliate_id">'.lang('Affiliate ID').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="affiliate_username"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="affiliate_username">'.lang('Affiliate Username').'</th>
						<th class=" table-cell header  '. ($sortBy =="rdate"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="rdate">'.lang('Date').'</th>
						<th class=" table-cell header  '. ($sortBy =="type"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="type">'.lang('Type').'</th>
						<th style="text-align: left;" class=" table-cell header  '. ($sortBy =="merchant_name"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="merchant_name">'.lang('Merchant').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="banner_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="banner_id">'.lang('Banner ID').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="profile_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="profile_id">'.lang('Profile ID').'</th>
						<th style="text-align: left;" >'.lang('Profile Name').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="param"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="param">'.lang('Param').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="param2"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="param2">'.lang('Param2').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="param3"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="param3">'.lang('Param3').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="param4"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="param3">'.lang('Param4').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="param5"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="param3">'.lang('Param5').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="refer_url"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="refer_url">'.lang('Refer URL').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="country_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="country_id">'.lang('Country').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="ip"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="ip">'.lang('IP').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="platform"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="platform">'.lang('Platform').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="os"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="os">'.lang('Operating System').'</th>
						<th style="text-align: left;"class=" table-cell header '. ($sortBy =="osVersion"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="osVersion">'.lang('OS Version').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="browser"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="browser">'.lang('Browser').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="broswerVersion"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="broswerVersion">'.lang('Broswer Version').'</th>
						
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="trader_id"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="trader_id">'.lang('Trader Id').'</th>
						<th style="text-align: left;" class=" table-cell header '. ($sortBy =="trader_alias"? $sortOrder=="ASC" || $sortOrder==""?"headerSortUp":"headerSortDown":"") .'" data-sort="trader_alias">'.lang('Trader Alias').'</th>
						<th class=" table-cell header">'.lang(ptitle('Lead')).'</th>
						<th class=" table-cell header">'.lang(ptitle('Demo')).'</th>
						<th class=" table-cell header">'.lang('Sale Status').'</th>
						<th class=" table-cell header">'.lang(ptitle('Accounts')).'</th>
						<th class=" table-cell header">'.lang('FTD').'</th>
						<th class=" table-cell" >'.lang('FTD Amount').'</th>
					<!--	<th class=" table-cell">'.lang('RAW FTD').'</th>
						<th class=" table-cell">'.lang('RAW FTD Amount').'</th> -->
						<th class=" table-cell">'.lang('Total Deposits').'</th>
						<th class=" table-cell">'.lang('Deposit Amount').'</th>
						<th class=" table-cell">'.lang('Volume').'</th>
						<th class=" table-cell">'.lang('Bonus Amount').'</th>
						<th class=" table-cell">'.lang('Withdrawal Amount').'</th>
						<th class=" table-cell">'.lang('ChargeBack Amount').'</th>
						<th class=" table-cell">'.lang(ptitle('Net Revenue')).'</th>
						'.($set->deal_pnl?'<th class=" table-cell">'.lang(ptitle('PNL')).'</th>':'').'
						<th class=" table-cell">'.lang('Active Traders').'</th>
						<th class=" table-cell">'.lang('Commission').'</th>
					</tr></thead><!--<tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						<th style="text-align: center;">'.$totalImpressions.'</th>
						<th style="text-align: center;">'.$totalClicks.'</th>
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
						<th></th>
						<th></th>
						<th></th>
						<th></th>
						<th><a href="'.$set->SSLprefix. $userlevel .'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>
						<th></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>
						<th style="text-align: left;">'.price($totalFTDAmount).'</th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalRealFtd.'</a></th>
						<th style="text-align: left;">'.price($totalRealFtdAmount).'</th>
						<th><a href="'.$set->SSLprefix.$userlevel.'/reports.php?act=transactions&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>
						<th style="text-align: left;">'.price($totalDepositAmount).'</th>
						<th>'.price($totalVolume).'</th>
						<th style="text-align: left;">'.price($totalBonusAmount).'</th>
						<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>
						<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>
						<th>'.price($totalNetRevenue).'</th>
						'.($set->deal_pnl?'<th>'.price($totalNetRevenue).'</th>':'').'
						<th style="text-align: left;">'.price(0).'</th>
						<th style="text-align: left;">'.price($totalComs).'</th>
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
											level = "'. $userlevel .'";
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
														buttons: { "'.lang('Export Advance Report').'": true, "'.lang('Export Basic Report').'": "true1", "'.lang('Cancel').'": false },
														submit: function(e,v,m,f){
															$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"../../images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>'.lang('Please be patient. Exporting data can take few minutes...') .'</span>");
														if(v || v=="true1"){
															
																if(v=="true1")
																	params = { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'",format:"csv"};
																else
																	params = { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'",format:"csv",adv:1};
																
																	$.ajax({
																		  method: "POST",
																		  url: "../../ajax/clicksReportExport.php",
																		  data: params
																		})
																		  .done(function( filepath ) {
																				filedata = $.parseJSON(filepath);
																				window.location.href = "common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
																				if(filedata.status == \'big\')
																				{
																						$.prompt("'.lang('File has been downloaded successfully. Because of limitation of excel, downloaded file contains only 65536 rows.').'", {
																								top:200,
																								title: "Export CSV",
																								buttons: { "'.lang('OK').'": true}
																						});
																				}
																			  	$(".ajaxloader").hide();
																		  })
																		  .always(function(){
																			  $(".ajaxloader").hide();
																		  })
																		  .fail(function() { 
																					window.location.href = "common/downloadFile.php?filename='. $_SERVER['CONTEXT_DOCUMENT_ROOT'] ."/files/exports/" . $filename.'.csv&unlinkfile=1";
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
								$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"../../images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>'.lang('Please be patient. Exporting data can take few minutes...') .'</span>");
								$.ajax({
									  method: "POST",
									  url: "'.$set->SSLprefix.'ajax/clicksReportExport.php",
									  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'", merchant_id:"'. $merchant_id .'", group_id:"'. $group_id .'", banner_id:"'. $banner_id .'", affiliate_id:"'. $affiliate_id .'", profile_id:"'. $profile_id .'", from:"'. $from .'", to:"'. $to .'", format:"csv"}
									})
									  .done(function( filepath ) {
												filedata = $.parseJSON(filepath);
												window.location.href = "common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
												$(".ajaxloader").hide();
										  
									  }).fail(function() { 
												window.location.href = "common/downloadFile.php?filename='. $set->webAddress ."ajax/" . $filename.'.csv&unlinkfile=1";
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
																$(".ajaxloader").html("").show().append("<img style=\"margin:0px 10px\" src=\"../../images/ajax-loader.gif\"><span style=\'top:-3px;position:relative\'>'.lang('Please be patient. Exporting data can take few minutes...') .'</span>");
																$.ajax({
																	  method: "POST",
																	  url: "'.$set->SSLprefix.'ajax/clicksReportExport.php",
																	  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'", merchant_id : "'. $merchant_id .'", group_id : "'. $group_id .'", banner_id : "'. $banner_id .'", affiliate_id : "'. $affiliate_id .'", profile_id : "'. $profile_id .'", from : "'. $from .'", to : "'. $to .'", format:"xlsx"}
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
																					window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename='. $_SERVER['DOCUMENT_ROOT'] ."/ajax/" . $filename.'.xls&unlinkfile=1";
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
									  data: { sql: "'.$ajaxSql.'" , where : "'. $where .'",filename:"'. $filename .'", merchant_id : "'. $merchant_id .'", group_id : "'. $group_id .'", banner_id : "'. $banner_id .'", affiliate_id : "'. $affiliate_id .'", profile_id : "'. $profile_id .'", from : "'. $from .'", to : "'. $to .'", format:"xlsx"}
									})
									  .done(function( filepath ) {
												filedata = $.parseJSON(filepath);
												window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename=" + filedata.file+"&unlinkfile=1";
												$(".ajaxloader").hide();
										  
									  }).fail(function() { 
												window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename='. $set->SSLprefix."ajax/" . $filename.'.csv&unlinkfile=1";
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
					// $tableStr .= getSingleSelectedMerchant();
		// $tableStr .= getValidateTraderMerchantScript('testForm');	
		//excelExporter($tableStr, 'Clicks');
		$set->content.=$tableStr.'</div>'.getURLPager();
		
		
		//MODAL
		$myReport = lang("Clicks");
		include "common/ReportFieldsModal.php";
		
		theme();


?>