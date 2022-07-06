<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 500);

require_once('common/global.php');
require_once('common/subAffiliateData.php');
$report_path = $_SERVER['DOCUMENT_ROOT'];

$loginLevel = "manager";
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/".$loginLevel."/";
if (!isManager()  ) _goto($lout);

$group_id = $set->userInfo['group_id'];

$merchantsArray = array();
		$displayForex = $isCasino = $isSportbet = 0;
		$merchantsAr = getMerchants(0,1);
		
		// $mer_rsc = function_mysql_query($sql,__FILE__);
		// while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
		foreach ($merchantsAr as $arrMerchant) {
			// var_dump($arrMerchant);
			// echo '<br>';
			
			
			if (strtolower($arrMerchant['producttype'])=='forex')
				$displayForex = 1;
			if (strtolower($arrMerchant['producttype'])=='sportsbetting')
				$isSportbet = 1;
			if (strtolower($arrMerchant['producttype'])=='casino')
				$isCasino = 1;
		
			$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}
					
					

 $hideDemoAndLeads = hideDemoAndLeads();
	
/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();
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

// All Affiliates.

if(isset($affiliate_id) && $affiliate_id !="Choose Affiliate"){
	$affiliate_id = retrieveAffiliateId($affiliate_id);	
}
else{
	$affiliate_id = "";
}
$sql ="SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' AND group_id='".$set->userInfo['group_id']."' ORDER BY id ASC";
$qqAff = function_mysql_query($sql,__FILE__);

if (!(isset($affiliate_id) && !empty($affiliate_id))) {
	$listOfAffiliates = '<option selected value="">'.lang('Choose Affiliate').'</option>';
}

while ($affiliateww = mysql_fetch_assoc($qqAff)) {		   
   if (isset($affiliate_id) && !empty($affiliate_id)) {
		$listOfAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $affiliate_id ? 'selected' : '').'>['.$affiliateww['id'].'] '
						  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
   }
   else {
		$listOfAffiliates .= '<option value="'.$affiliateww['id'].'">['.$affiliateww['id'].'] '
						  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
   }
}		

//Prevent  direct browse on reports pages under reports directory
define('DirectBrowse', TRUE);




switch ($act) {
		default:
		$fields = getReportsHiddenCols("quickSummaryReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminQuickSummaryHiddenCols = $fields;
			}
		include $report_path . '/common/reports/quick.php';
		break;
	
	case "quick_new":	
		$fields = getReportsHiddenCols("quickSummaryReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminQuickSummaryHiddenCols = $fields;
			}
		include $report_path . '/common/reports/quick_new.php';
		break;

	case "quick_old":	
		$fields = getReportsHiddenCols("quickSummaryReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminQuickSummaryHiddenCols = $fields;
			}
		include $report_path . '/common/reports/quick_old.php';
		break;
                
	case "traffic":
		$fields = getReportsHiddenCols("trafficReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminTrafficHiddenCols = $fields;
			}
		include $report_path. '/common/reports/traffic.php';
		break;
	


case "ActiveCreatiesStats":
	$fields = getReportsHiddenCols("activeCreativeStatsReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminActiveCreativeStatsHiddenCols = $fields;
			}
		include $report_path. '/common/reports/ActiveCreatiesStats.php';
		break;
	

	
	/* 
                
	case "banner":
		include $report_path . '/common/reports/banner.php';
		break;
	 */


                
        case "trader":
		$fields = getReportsHiddenCols("traderReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminTraderHiddenCols = $fields;
			}
                include $report_path.'/common/reports/trader.php';
		break;
                
		                
        case "subtraders":
			$fields = getReportsHiddenCols("subTradersReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminSubTradersHiddenCols = $fields;
			}
                include  $report_path.'/common/reports/subtraders.php';
		break;
                
		
		
	
        case "transactions":
		$fields = getReportsHiddenCols("transactionsReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminTransactionsHiddenCols = $fields;
			}
               include $report_path. "/common/reports/transactions.php";
		break;
        
		
		
		case "install":
		$fields = getReportsHiddenCols("installReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminInstallHiddenCols = $fields;
			}
               include $report_path. "/common/reports/install.php";
		break;
                
		
	
        
	
	case "affiliate":
	$fields = getReportsHiddenCols("affiliatesReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminAffiliatesHiddenCols = $fields;
			}
				include $report_path . "/common/reports/affiliates.php";
		break;
                
                
                 
     
                
                
        case "profile":
			$fields = getReportsHiddenCols("profileReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminProfileHiddenCols = $fields;
			}
				include $report_path . "/common/reports/profile.php";
            break;
                
    
case "country":
			$fields = getReportsHiddenCols("countryReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminCountryHiddenCols = $fields;
			}
			include $report_path . "/common/reports/country.php";
		break;
	

case "creative":
		$fields = getReportsHiddenCols("creativesReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminCreativesHiddenCols = $fields;
			}
		include $report_path . "/common/reports/creative.php";
		break;	
		
		
		
		
		
		
case "LandingPage":
$fields = getReportsHiddenCols("landingPagesReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminLandingPagesHiddenCols = $fields;
			}
		include $report_path . "/common/reports/landingPage.php";
		break;	
		
		
		
		case "clicks":
		$fields = getReportsHiddenCols("clicksReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminClicksHiddenCols = $fields;
			}
		include  $report_path. "/common/reports/clicks.php";
		break;
		
		case "commission":
		$fields = getReportsHiddenCols("commissionReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminCommissionHiddenCols = $fields;
			}
		include $report_path . "/common/reports/commission.php";
		break;
		
		case "commission_new":
		$fields = getReportsHiddenCols("commissionReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminCommissionHiddenCols = $fields;
			}
		include $report_path . "/common/reports/commission_new.php";
		break;
		
		case "sub":
		$fields = getReportsHiddenCols("subReport","manager",$set->userInfo['id']);
			if($fields){
				$set->adminSubHiddenCols = $fields;
			}
		include $report_path . "/common/reports/sub.php";
		break;
        
}

	
$fileName =   $report_path . "/".$loginLevel."/csv/report.csv";
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