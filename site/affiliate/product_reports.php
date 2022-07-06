<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 500);

require_once('common/global.php');

$report_path = $_SERVER['DOCUMENT_ROOT'];

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);



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

$allCountriesArray = getDBCountries();

$from = strTodate($from);
$to   = strTodate($to);

$from = sanitizeDate($from);
$to   = sanitizeDate($to);

commonGlobalSetTimeRange($from, $to);

$globalWhere = 'affiliate';



//Prevent  direct browse on reports pages under reports directory
define('DirectBrowse', TRUE);
$set->content .='<style>		
			ul.vertical {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 20%;
			background-color: #f1f1f1;
			position:fixed;
			height:65vh;
			overflow:auto;
			min-width:80px;
			display:block;
		}

		ul.vertical li a {
			display: block;
			color: #000;
			padding: 8px 0 8px 16px;
			text-decoration: none;
		}

		ul.vertical li a:hover:not(.active) {
			background-color: #555;
			color:white;
		}

		ul.vertical a.active {
			background-color: #555;
			color:white;
		}	
		
		ul.vertical li.heading_active {
			padding: 8px 0 8px 5px;
			text-decoration: none;
			color:white;
			height: 32px;
		}
		ul.vertical  li input#config_filter
		{
			    width: 101px;
				/*margin-right: 5px;
				margin-left: 5px;
				margin-top: 10px;
				margin-bottom: 10px;*/
		}
		ul.vertical  li input#btnFilter
		{
			padding-left:0px !Important;
			padding-right:0px !important;
			width:100px !important;
		}
		.main {
			margin-left:20%;
			padding:1px 16px;
			height:100%;
		}
		
		.filter{
			padding-top:10px;
			padding-right:2px;
		}
		
		@media screen and (max-width: 290px) {
			.main{
			   margin-left:35%;
			}
		}
		</style>
		<script>
		$(document).ready(function(){
			$("ul.vertical li a").on("click",function(){
				var tabtoopen = $(this).data("tab");
				 $(this).toggleClass("active").parent().siblings().find("a").removeClass("active");
				show_hide_tabs(tabtoopen,"li");
			});
			
			
			
			
			function show_hide_tabs(open_tab,type){
				$(".config_tabs").hide();
				
				if(type=="search"){
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
						$(".config_tabs").hide();
						$(".config_tabs").each(function(k,tab){
							var search_txt = $(tab).find("div.normalTableTitle").text();
							
							search_txt = search_txt.toLowerCase();
							open_tab = open_tab.toLowerCase();
								console.log(search_txt + " -----" + open_tab);
							if(search_txt.search(open_tab)!==-1){
								$(this).show();
								txt = $(this).data("tab");
								$("ul.vertical li a").each(function(){
								
									if(txt == $(this).data("tab")){
										$(this).css("color","grey");
									}
								});
							}
							else{
								$(this).hide();
								txt = $(this).data("tab");
								$("ul.vertical li a").each(function(){
									if(txt == $(this).data("tab")){
										$(this).css("color","black");
									}
								});
							}
						});
					}
				}
				else{
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
					$(".config_tabs").each(function(k,tab){
						if($(tab).data("tab") == open_tab){
							$(this).show();
						}
						else{
						}
					});
					}
					
				}
			}

			$("#config_filter").on("keyup",function(){
				tabtoopen = $(this).val();
				if(tabtoopen == "")
				{
					$(".config_tabs").show();
					$("ul.vertical li a").css("color","black");
				}
				else{
				show_hide_tabs(tabtoopen,"search");
				}
			});
			
			$("#filter_form").submit(function(){
				var tab_text = $("#config_filter").val();
				show_hide_tabs(tab_text,"search");
				return false;
			});
			
		});
		</script>
		<ul class="vertical">
  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">'. lang('Reports') .'</span><form onsubmit="return false;" style="display:inline-flex;float:right;">
  <div class="filter">'.lang('Find') .': </div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div></li>
  <li><a href="'.$set->SSLprefix.'affiliate/product_reports.php" data-tab="quick" '. (!isset($act)?'class=active':'') .'>'. lang('Quick Summary Report') .'</a></li>
  <li><a href="'.$set->SSLprefix.'affiliate/product_reports.php?act=accounts" data-tab="accounts"'. ($act=='accounts'?'class=active':'') .  '>'. lang('Accounts Report') .'</a></li>
  </ul>
  <div class="main">';

switch ($act) {
		default:
		$fields = getReportsHiddenCols("productQuickReport","affiliate",$set->userInfo['id']);
		if($fields){
			$set->productQuickHiddenCols = $fields;
		}
		include $report_path . '/common/product_reports/quick.php';
		break;
		
                
	case "accounts":
		$fields = getReportsHiddenCols("productAccountReport","affiliate",$set->userInfo['id']);
		if($fields){
			$set->productAccountHiddenCols = $fields;
		}
		include $report_path. '/common/product_reports/accounts.php';
		break;
	
	
	
		
		case "clicks":
		include  $report_path. "/common/product_reports/clicks.php";
		break;
		
		
/*                 
	case "banner":
		include $report_path . '/common/reports/banner.php';
		break;
	


                
        case "trader":
                include $report_path.'/common/reports/trader.php';
		break;
                
		                
        case "subtraders":
                include  $report_path.'/common/reports/subtraders.php';
		break;
                
		
		
	
        case "transactions":
               include $report_path. "/common/reports/transactions.php";
		break;
                
		
		
		
	case "stats":
		include $report_path."/common/reports/stats.php";
		break;
		
	
        
	
	case "affiliate":
				include $report_path . "/common/reports/affiliates.php";
		break;
                
                
                 
                
                
                
	
	case "group":
               include $report_path . "/common/reports/group.php";
		break;
		
		
                
                
                
                
        case "profile":
				include $report_path . "/common/reports/profile.php";
            break;
                
    
case "country":
			include $report_path . "/common/reports/country.php";
		break;
	

case "creative":
		include $report_path . "/common/reports/creative.php";
		break;	
		
		
		
		
		
		
case "LandingPage":
		include $report_path . "/common/reports/landingPage.php";
		break;	
		
		
		
		case "commission":
		include $report_path . "/common/reports/commission.php";
		break;
 */        
}

$set->content .="</div>";
	
$fileName =   $report_path . "/affiliate/csv/report.csv";
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