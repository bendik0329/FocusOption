<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
ini_set('memory_limit', '3072M');
ini_set('max_execution_time', 500);

require_once('common/global.php');
require_once('common/subAffiliateData.php');

$report_path = $_SERVER['DOCUMENT_ROOT'];

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);


$merchantsArray = array();
		$displayForex = $isCasino = $isSportbet = 0;
		$merchantsAr = getMerchants(0,1);
		
		// $mer_rsc = function_mysql_query($sql,__FILE__);
		// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
		foreach ($merchantsAr as $arrMerchant) {
			// var_dump($arrMerchant);
			// echo '<br>';
			
			
			if (strtolower($arrMerchant['producttype'])=='forex' && $displayForex==0)
				$displayForex = 1;
			if (strtolower($arrMerchant['producttype'])=='sportsbetting' && $displayForex==0)
				$isSportbet = 1;
			if (strtolower($arrMerchant['producttype'])=='casino' && $displayForex==0)
				$isCasino = 1;
		
			$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}

// All Affiliates.

if(isset($affiliate_id) && $affiliate_id !="Choose Affiliate"){
	$affiliate_id = retrieveAffiliateId($affiliate_id);	
}
else{
	$affiliate_id = "";
}

$qqAff = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);

if (!(isset($affiliate_id) && !empty($affiliate_id))) {
	$listOfAffiliates = '<option selected value="">'.lang('Choose Affiliate').'</option>';
}

while ($affiliateww = mysql_fetch_assoc($qqAff)) {		   
   if (isset($affiliate_id) && !empty($affiliate_id)) {
		$listOfAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $affiliate_id ? 'selected' : '').'><!--email_off-->['.$affiliateww['id'].'] '
						  .  strip_tags($affiliateww['username']).' ('.strip_tags($affiliateww['first_name']).' '.strip_tags($affiliateww['last_name']).')<!--/email_off--></option>';
   }
   else {
		$listOfAffiliates .= '<option value="'.$affiliateww['id'].'"><!--email_off-->['.$affiliateww['id'].'] '
						  .  strip_tags($affiliateww['username']).' ('.strip_tags($affiliateww['first_name']).' '.strip_tags($affiliateww['last_name']).')<!--/email_off--></option>';
   }
}		


$showPNL = $set->deal_pnl==1;
 $hideDemoAndLeads = hideDemoAndLeads();
	
/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();
// var_dump($arrDealTypeDefaults);
// die();

$allCountriesArray = getDBCountries();

//Add Reports to My Fav Logic
if($isFav){

	
	switch($auto_time_frame){
		case 1: // Today
			$from = date("d/m/Y");
			$to = date("d/m/Y") . " 23:59:59";
			break;
		case 2: // Yesterday
			$from =  date("d/m/Y",strtotime('-1 day'));
			$to =  date("d/m/Y",strtotime('-1 day')) . " 23:59:59";
			break;
		case 6: // This Week
			$today = date("d/m/Y");
			$from = date("d/m/Y",strtotime("-7 days"));
			$to = date("d/m/Y") . " 23:59:59";
			break;
		case 3: // This Month
			$from = date('01/m/Y',strtotime('this month'));
			$to = date('t/m/Y',strtotime('this month')) . " 23:59:59";
			break;
		case 4: // Last Month
			$from = date('01/m/Y',strtotime('last month'));
			$to = date('t/m/Y',strtotime('last month')) . " 23:59:59";
			break;
		case 5: // This Year
			$from = date('01/01/Y',strtotime('this year'));
			$to = date('t/12/Y',strtotime('this year')) . " 23:59:59";
			break;
		case 7: // Last Year
			$from = date('01/01/Y',strtotime('last year'));
			$to = date('t/12/Y',strtotime('last year')) . " 23:59:59";
			break;
		
	}
	
}


$from = strTodate($from);
$to   = strTodate($to);

$from = sanitizeDate($from);
$to   = sanitizeDate($to);

commonGlobalSetTimeRange($from, $to);

$globalWhere = ' 1 = 1 AND ';

//Prevent  direct browse on reports pages under reports directory
define('DirectBrowse', TRUE);



switch ($act) {
		default:
		$fields = getReportsHiddenCols("quickSummaryReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminQuickSummaryHiddenCols = $fields;
		}
		include $report_path . '/common/reports/quick.php';
		break;
		
	case "quick_new":
		$fields = getReportsHiddenCols("quickSummaryReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminQuickSummaryHiddenCols = $fields;
		}
		include $report_path . '/common/reports/quick_new.php';
		break;

	case "quick_old":
		$fields = getReportsHiddenCols("quickSummaryReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminQuickSummaryHiddenCols = $fields;
		}
		include $report_path . '/common/reports/quick_old.php';
		break;		
		                
	case "commission":
		$fields = getReportsHiddenCols("commissionReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminCommissionHiddenCols = $fields;
		}
		include $report_path. '/common/reports/commission.php';
		break;

	case "commission_old":
		$fields = getReportsHiddenCols("commissionReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminCommissionHiddenCols = $fields;
		}
		include $report_path. '/common/reports/commission_old.php';
		break;
		
	case "ActiveCreatiesStats":
		$fields = getReportsHiddenCols("activeCreativeStatsReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminActiveCreativeStatsHiddenCols = $fields;
		}
		include $report_path. '/common/reports/ActiveCreatiesStats.php';
		break;
	
	case "traffic":
		$fields = getReportsHiddenCols("trafficReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminTrafficHiddenCols = $fields;
		}
		include $report_path. '/common/reports/traffic.php';
		break;
	
	case "banner":
		include $report_path . '/common/reports/banner.php';
		break;
                
	case "trader":
		$fields = getReportsHiddenCols("traderReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminTraderHiddenCols = $fields;
		}
		include $report_path.'/common/reports/trader.php';
		break;
	
	case "trader_test":
		$fields = getReportsHiddenCols("traderReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminTraderHiddenCols = $fields;
		}
		include $report_path.'/common/reports/trader_test.php';
		break;
	
	case "trader_new":
		$fields = getReportsHiddenCols("traderReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminTraderHiddenCols = $fields;
		}
		include $report_path.'/common/reports/trader_new.php';
		break;
	
	case "trader_old":
		$fields = getReportsHiddenCols("traderReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminTraderHiddenCols = $fields;
		}
		include $report_path.'/common/reports/trader_old.php';
		break;
	
	case "sub":
		$fields = getReportsHiddenCols("subReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminSubHiddenCols = $fields;
		}
		include $report_path.'/common/reports/sub.php';
		break;
			
	case "subtraders":
		$fields = getReportsHiddenCols("subTradersReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminSubTradersHiddenCols = $fields;
		}
		include  $report_path.'/common/reports/subtraders.php';
		break;

	case "transactions":
		$fields = getReportsHiddenCols("transactionsReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminTransactionsHiddenCols = $fields;
		}
		include $report_path. "/common/reports/transactions.php";
		break;
		
	case "stats":
		include $report_path."/common/reports/stats.php";
		break;
		
	case "pixelsLogs":
		include $report_path."/common/reports/pixelsLogs.php";
		break;

	case "affiliate":
		$fields = getReportsHiddenCols("affiliatesReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminAffiliatesHiddenCols = $fields;
		}
		include $report_path . "/common/reports/affiliates.php";
		break;

	case "affiliate_new":
		$fields = getReportsHiddenCols("affiliatesReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminAffiliatesHiddenCols = $fields;
		}
		include $report_path . "/common/reports/affiliates_new.php";
		break;
		
	case "affiliate_old":
		$fields = getReportsHiddenCols("affiliatesReport","admin",$set->userInfo['id']);
			if($fields){
				$set->adminAffiliatesHiddenCols = $fields;
			}
			include $report_path . "/common/reports/affiliates_old.php";
		break;
                
	case "install":
		$fields = getReportsHiddenCols("installReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminInstallHiddenCols = $fields;
		}
		include $report_path . "/common/reports/install.php";
		break;
	
	case "group":
		$fields = getReportsHiddenCols("groupReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminGroupHiddenCols = $fields;
		}
		include $report_path . "/common/reports/group.php";
		break;
		
	case "dynamic_filters":
		$fields = getReportsHiddenCols("dynamicFiltersReports","admin",$set->userInfo['id']);
		if($fields){
			$set->adminGroupHiddenCols = $fields;
		}
		include $report_path . "/common/reports/dynamic_filters_r.php";
		break;
		
	case "profile":
		$fields = getReportsHiddenCols("profileReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminProfileHiddenCols = $fields;
		}
		include $report_path . "/common/reports/profile.php";
		break;
    
	case "country":
		$fields = getReportsHiddenCols("countryReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminCountryHiddenCols = $fields;
		}
		include $report_path . "/common/reports/country.php";
		break;
	
	case "creative":
		$fields = getReportsHiddenCols("creativesReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminCreativesHiddenCols = $fields;
		}
		include $report_path . "/common/reports/creative.php";
		break;	
		
	case "LandingPage":
		$fields = getReportsHiddenCols("landingPagesReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminLandingPagesHiddenCols = $fields;
		}
		include $report_path . "/common/reports/landingPage.php";
		break;	
		
	case "clicks":
		$fields = getReportsHiddenCols("clicksReport","admin",$set->userInfo['id']);
		if($fields){
			$set->adminClicksHiddenCols = $fields;
		}
		include  $report_path. "/common/reports/clicks.php";
		break;
		
	case "commission":
		include $report_path . "/common/reports/commission.php";
		break;
		
	case "commission_new":
		include $report_path . "/common/reports/commission_new.php";
		break;

	case "commissions_debts":
		include $report_path. '/common/reports/commissions_debts.php';
		break;
		
}

	
$fileName =   $report_path . "/admin/csv/report.csv";
$openFile = fopen($fileName, 'w'); 
// fwrite($openFile, $csvContent); 
fclose($openFile); 
header("Expires: 0");
header("Pragma: no-cache");
header("Content-type: application/ofx");
header("Content-Disposition: attachment; filename=".date('Ymd').'-'.$fileName);
for ($i=0; $i<=count($csvContent)-1; $i++) echo implode(",",$csvContent[$i])."\n";
die();

?>