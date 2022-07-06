<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */
ini_set('memory_limit', '1512M');
ini_set('max_execution_time', 500);

require_once('common/global.php');
require_once('common/subAffiliateData.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);



$merchantsArray = array();
$displayForex = $isCasino = $isSportbet = 0;
$merchantsAr = getMerchants(0,1);
foreach ($merchantsAr as $arrMerchant) {
		if (strtolower($arrMerchant['producttype'])=='forex' && $displayForex==0)
			$displayForex = 1;
		if (strtolower($arrMerchant['producttype'])=='sportsbetting' && $displayForex==0)
			$isSportbet = 1;
		if (strtolower($arrMerchant['producttype'])=='casino' && $displayForex==0)
			$isCasino = 1;
		$merchantsArray[$arrMerchant['id']] = $arrMerchant;
}
$showPNL = $set->deal_pnl==1;
$hideDemoAndLeads = hideDemoAndLeads();
 
 
$debugshowhide= isset($_GET['debugshowhide']) ? $_GET['debugshowhide'] : false;
getPermissions();
$dealsArray = $set->userInfo['dealsArray'];

$merchant_id = clearInjection($merchant_id,'int');
$arrDealTypeDefaults = getMerchantDealTypeDefaults();

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

$merchantIDs = ($set->userInfo['merchants']);
$merchantIDs = str_replace('|',",",$merchantIDs);
$merchantsArr = explode(',',$merchantIDs);

			
$globalWhere = ' tb1.affiliate_id = ' . $set->userInfo['id'] . ' AND ';



function getActivePlatform(){
	global $platform;
	$qry = "select distinct platform from data_reg WHERE platform!='' order by platform";
	$platformParam = '';
	$qqq = function_mysql_query($qry,__FILE__);
	$isfirst = false;
	while ($wwa=mysql_fetch_assoc($qqq))  {
	if (!$isfirst) {
	$isfirst = true;
		$platformParam .= '<option value="-1">'.ucwords(lang('Choose Platform')).'</option>';
	}
		$platformParam .= '<option value="'.$wwa['platform'].'" '.($platform == $wwa['platform'] ? 'selected' : '').'>'.ucwords(lang($wwa['platform'])).'</option>';
	}
	if ($platformParam!='') {
		$platformParam = clearInjection($platformParam);
		$platformParam = '<td>
						<select name="platform" style="width: 150px;">' . $platformParam . 
							'
						</select>
					</td>';
	}
	
	return $platformParam;

}


$showRev = 0;
$hideFrozens = 0;
$showFtdAmount = 0;
$merchantsArr2 = Array();





$arrDealTypeDefaults = getMerchantDealTypeDefaults();
$allCountriesArray = getDBCountries();

$from = strTodate($from);
$to   = strTodate($to);

$from = sanitizeDate($from);
$to   = sanitizeDate($to);

commonGlobalSetTimeRange($from, $to);

$globalWhere = ' 1 = 1 AND ';

//Prevent  direct browse on reports pages under reports directory
define('DirectBrowse', TRUE);


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
$allowExcelPart = false;
switch ($act) {
		default:
		if(allowView('af-quick',$deal,'reports'))	 {
		
		$fields = getReportsHiddenCols("quickSummaryReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminQuickSummaryHiddenCols = $fields;
			}
			
		include 'reports/quick.php';
		$allowExcelPart=true;
		}
		break;
		
	case "quick_new":
		if(allowView('af-quick',$deal,'reports'))	 {
		
		$fields = getReportsHiddenCols("quickSummaryReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminQuickSummaryHiddenCols = $fields;
			}
			
		include 'reports/quick_new.php';
		$allowExcelPart=true;
		}
		break;

	case "quick_old":
		if(allowView('af-quick',$deal,'reports'))	 {
		
		$fields = getReportsHiddenCols("quickSummaryReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminQuickSummaryHiddenCols = $fields;
			}
			
		include 'reports/quick_old.php';
		$allowExcelPart=true;
		}
		break;
                
	case "traffic":
		if(allowView('af-traffic',$deal,'reports'))	{
			$fields = getReportsHiddenCols("trafficReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminTrafficHiddenCols = $fields;
			}
		include 'reports/traffic.php';
		$allowExcelPart=true;
		}
		break;
	
	
	case "install":
		if(allowView('af-install',$deal,'reports'))	{
	
			$fields = getReportsHiddenCols("installReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminInstallHiddenCols = $fields;
			}
		include 'reports/install.php';
		$allowExcelPart=true;
		}
		break;
	
                
	case "banner":
	if(allowView('af-creative',$deal,'reports'))	{
		$fields = getReportsHiddenCols("creativesReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminCreativesHiddenCols = $fields;
			}
		include 'reports/banner.php';
		$allowExcelPart=true;
		}
		break;
	
                
        case "trader":
		if(allowView('af-trader',$deal,'reports'))	{
			$fields = getReportsHiddenCols("traderReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminTraderHiddenCols = $fields;
			}
			
                include 'reports/trader.php';
				$allowExcelPart=true;
		}
		break;
		
        case "trader_new":
		if(allowView('af-trader',$deal,'reports'))	{
			$fields = getReportsHiddenCols("traderReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminTraderHiddenCols = $fields;
			}
			
                include 'reports/trader_new.php';
				$allowExcelPart=true;
		}
		break;

        case "subtraders":
		if(allowView('af-trader',$deal,'reports'))	 {
			
			$fields = getReportsHiddenCols("subTradersReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminSubTradersHiddenCols = $fields;
			}
			
                include  $report_path.'reports/subtraders.php';
				$allowExcelPart=true;
		}
		break;
                
                
        case "profile":
		if(allowView('af-profile',$deal,'reports'))	 {
			$fields = getReportsHiddenCols("profileReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminProfileHiddenCols = $fields;
			}
				include "reports/profile.php";
				$allowExcelPart=true;
		}
            break;
                
		
		case "clicks":
		if(allowView('af-clicks',$deal,'reports'))	 {
			$fields = getReportsHiddenCols("clicksReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminClicksHiddenCols = $fields;
			}
		include "reports/clicks.php";
		$allowExcelPart=true;
		}
		break;
		
		case "landingPage":
		if(allowView('af-landing',$deal,'reports'))	{
			$fields = getReportsHiddenCols("landingPagesReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminLandingPagesHiddenCols = $fields;
			}
		include "reports/landingPage.php";
		$allowExcelPart=true;
		}
		break;
       

	   	   
	   case "pixelsLogs":
		if($fields){
				$set->adminTransactionsHiddenCols = $fields;
			}
			
		include "reports/pixelsLogs.php";
		$allowExcelPart=true;
		
		break;
       
	   case "transactions":
		if(allowView('af-trnsct',$deal,'reports'))	{
		    // Show Transaction Report for affiliate id 1302

    			$fields = getReportsHiddenCols("transactionsReport","affiliate",$set->userInfo['id']);
    			if($fields){
    				$set->adminTransactionsHiddenCols = $fields;
    			}
    		    include "reports/transactions.php";
    		    $allowExcelPart=true;

		}
		break;

		case "commission":
		if(allowView('af-comm',$deal,'reports'))	{
				$fields = getReportsHiddenCols("commissionReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminCommissionHiddenCols = $fields;
			}
		include "reports/commission.php";
		$allowExcelPart=true;
		}
		break;
		
		case "commission_old":
		if(allowView('af-comm',$deal,'reports'))	{
				$fields = getReportsHiddenCols("commissionReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminCommissionHiddenCols = $fields;
			}
		include "reports/commission_old.php";
		$allowExcelPart=true;
		}
		break;
		
		case "sub":
		//if(allowView('af-comm',$deal,'reports'))	{
			$fields = getReportsHiddenCols("subReport","affiliate",$set->userInfo['id']);
			if($fields){
				$set->adminSubHiddenCols = $fields;
			}
		$report_path = $_SERVER['DOCUMENT_ROOT'];
		include "common/reports/sub.php";
		$allowExcelPart=true;
		//}
		break;
        
}

if ($allowExcelPart) {
$fileName =   "admin/csv/report.csv";
$openFile = fopen($fileName, 'w'); 
// fwrite($openFile, $csvContent); 
fclose($openFile); 
header("Expires: 0");
header("Pragma: no-cache");
header("Content-type: application/ofx");
header("Content-Disposition: attachment; filename=".date('Ymd').'-'.$fileName);
for ($i=0; $i<=count($csvContent)-1; $i++) echo implode(",",$csvContent[$i])."\n";
}
else {
	

	
	if (!empty($set->SSLprefix))
	{
		if ( $set->SSLprefix == rtrim(rtrim($set->SSLprefix,'/affiliate'),'/')){
		
                    _goto($set->SSLprefix.'/affiliate/');
		}
			else
                   _goto($set->SSLprefix.'/');
	}
		else{
		// die ($set->SSLprefix.'affiliate/');
               _goto($set->SSLprefix.'/affiliate');
		}	
/* 	$redirectTo = endsWith($set->SSLprefix,'affiliate/') ? $set->SSLprefix : $set->SSLprefix.'affiliate/';
	$redirectTo = startsWith($redirectTo,'affiliate/') ? '/'.$redirectTo : $redirectTo;
	// _goto('/affiliate/');
	// die ($redirectTo);
	_goto($redirectTo); */
			
}
die();

?>
