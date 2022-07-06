<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 500);

require_once('common/global.php');

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

$globalWhere = ' 1 = 1 AND ';

//Prevent  direct browse on reports pages under reports directory
define('DirectBrowse', TRUE);
$set->content .='<style>	
			aside {
					width: 20%;
					background-color: #f1f1f1;
					position:fixed;
					height:100vh;
			
			}
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
		<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
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
		<aside>
		<ul class="vertical">
  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">'. lang('Reports') .'</span><form onsubmit="return false;" style="display:inline-flex;float:right;">
  <div class="filter">'.lang('Find') .': </div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div></li>
  <li><a href="'.$set->SSLprefix.'admin/product_reports.php" data-tab="quick" '. (!isset($act)?'class=active':'') .'>'. lang('Quick Summary Report') .'</a></li>
  <li><a href="'.$set->SSLprefix.'admin/product_reports.php?act=accounts" data-tab="accounts"'. ($act=='accounts'?'class=active':'') .  '>'. lang('Accounts Report') .'</a></li>
  <li><a href="'.$set->SSLprefix.'admin/product_reports.php?act=affiliates" data-tab="affiliates"'. ($act=='affiliates'?'class=active':'') .  '>'. lang('Affiliates Report') .'</a></li>
  <li><a href="'.$set->SSLprefix.'admin/product_reports.php?act=traffic" data-tab="traffic"'. ($act=='traffic'?'class=active':'') .  '>'. lang('Traffic Report') .'</a></li>
  </ul>
  </aside>
  <div class="main">';

  
  if($userlevel == 'manager')
	$group_id       = $set->userInfo['group_id'];
if($userlevel == 'advertiser'){
	$merchant_id       = $set->userInfo['relatedMerchantID'];
	if (!empty($merchant_id)){
		$product_idsRSC =function_mysql_query("select id from products_items where merchant_id in (". $merchant_id. ") and valid != 0");
		$product_ids ="";
		while ($row = mysql_fetch_assoc($product_idsRSC)){
			$product_ids .=$row['id'].',';
		}
		$product_id = rtrim($product_ids,',');
	}
}

// All Affiliates.
if(isset($affiliate_id)){
	$affiliate_id = retrieveAffiliateId($affiliate_id);	
}
 if($userlevel == 'manager'){
	 $qqAff = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' AND group_id='".$set->userInfo['group_id']."'  ORDER BY id ASC",__FILE__);
 }
 else{
	$qqAff = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
 }
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

switch ($act) {
		default:
		$fields = getReportsHiddenCols("productQuickReport","admin",$set->userInfo['id']);
		if($fields){
			$set->productQuickHiddenCols = $fields;
		}
		include $report_path . '/common/product_reports/quick.php';
		break;
		
                
	case "accounts":
		$fields = getReportsHiddenCols("productAccountReport","admin",$set->userInfo['id']);
		if($fields){
			$set->productAccountHiddenCols = $fields;
		}
		include $report_path. '/common/product_reports/accounts.php';
		break;
	
	
	
	case "affiliates":
		$fields = getReportsHiddenCols("productAffiliateReport","admin",$set->userInfo['id']);
		if($fields){
			$set->productAffiliateHiddenCols = $fields;
		}
				include $report_path . "/common/product_reports/affiliates.php";
		break;
                
                
    
	case "traffic":
	$fields = getReportsHiddenCols("productTrafficReport","admin",$set->userInfo['id']);
		if($fields){
			$set->productTrafficHiddenCols = $fields;
		}
				include $report_path . "/common/product_reports/traffic.php";
		break;
                
                
        
}

$set->content .="</div>";
	
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