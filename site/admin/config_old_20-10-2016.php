<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
if (!isAdmin()) _goto('/admin/');

$countriesArr = array();		
countriesList();
function countriesList(){
	 global $countriesArr;
		/*foreach($countriesArr as $key=>$val){
			$html .= '<option value="'.$key.'">'.$val.'</option>';
		}
		return $html; */
		
		$sql = "SELECT * FROM countries where id>1";
		$strCountries   = function_mysql_query($sql,__FILE__,__FUNCTION__);
		while ($countries = mysql_fetch_assoc($strCountries)){
			$countriesArr[$countries['id']] = $countries['title'];
			$html .= '<option value="'.$countries['id'].'">'.$countries['title'].'</option>';
		}
		return $html;
		
}

$listofcolors = '
	
									<option  value="" selected>'.lang('System Default').'</option>
									<option style="background-color: white" value="white">'.lang('White').'</option>
									<option style="background-color: red" value="red">'.lang('Red').'</option>
									 <option style="background-color: lightgreen" value="lightgreen">'.lang('Light Green').'</option>
									 <option style="background-color: green" value="green">'.lang('Green').'</option>
									 <option style="background-color: blue" value="blue">'.lang('Blue').'</option>
									 <option style="background-color: lightblue" value="lightblue">'.lang('Light Blue').'</option>
									  <option style="background-color: Yellow" value="yellow">'.lang('Yellow').'</option>
									  <option style="background-color: lightyellow" value="lightyellow">'.lang('Light Yellow').'</option>
									  <option style="background-color: Purple" value="purple">'.lang('Purple').'</option>
									  <option style="background-color: pink" value="pink">'.lang('Pink').'</option>
									  <option style="background-color: orange" value="orange">'.lang('Orange').'</option>
   									<option style="background-color: Gray" value="gray">'.lang('Gray').'</option>
   									<option style="background-color: LightGray" value="lightgray">'.lang('Light Gray').'</option>';
									
									

switch ($act) {
	case "save":
	
		if($db['hideCountriesOnRegistration']!=""){
			$arrCountries = explode(",",$db['hideCountriesOnRegistration']);
			/*$co = array();
			foreach($arrCountries as $key=>$value){
					$co[] = $countriesArr[$value];
			}
			
			$db['hideCountriesOnRegistration'] = implode("|", $co);*/
			$db['hideCountriesOnRegistration'] = implode("|", $arrCountries);
			
		}
	
		$db['id'] = 1;
		
		$base_url =  "http://" . $_SERVER['HTTP_HOST'];
		$terms_path = "/files/";
		//handle terms
			switch($terms){
			case "browse_terms":
		
								$dir = $_SERVER["DOCUMENT_ROOT"];
								$target_dir = $dir. "/files/";
								//echo $target_dir;die;
								$target_file = $target_dir .  $_FILES["terms_link_file"]["name"];
							
								$uploadOk = 1;
								$imageFileType = pathinfo($_FILES["terms_link_file"]["name"],PATHINFO_EXTENSION);
								if($imageFileType != "html" && $imageFileType != "HTML") {
									$error =  "Sorry, only HTML file is allowed.";
									
									_goto($set->uri . "?error=". urlencode($error) . "&tab=terms_and_conditions");
								}
								else{
									if (move_uploaded_file($_FILES["terms_link_file"]["tmp_name"], $target_file)) {
										$ty =  "The file ". basename( $_FILES["terms_link_file"]["name"]). " has been uploaded.";
										$db['terms_link'] = $base_url . $terms_path . $_FILES["terms_link_file"]["name"]; 
									} else {
										$error =  "Sorry, there was an error uploading your file.";
										_goto($set->uri . "?error=". urlencode($error) . "&tab=terms_and_conditions");
									}
								}
								break;
				
				case "html_terms":
							$dir = $_SERVER['DOCUMENT_ROOT'];
							$myfile = file_put_contents($dir. "/files/terms.html", $terms_link_html);
							$db['terms_link'] = $base_url . $terms_path . "terms.html"; 
							break;
				default:
							//default - input case
							break;
			}
		
		if ($combinedPY!=''){
			$db['availablePayments']=$combinedPY;
		}
		if ($combinedMN!=''){
			$db['mustFields']=$combinedMN;
		}
		if ($combinedCR!=''){
			$db['availableCurrencies']=$combinedCR;
		}
		if ($combinedPO!=''){
			$db['combinedPixelOption']=$combinedPO;
		}

		if ($pending) $db['pending'] = 1; else $db['pending'] = 0;

		
		if ($SMTPAuth) $db['SMTPAuth'] = 1; else $db['SMTPAuth'] = 0;
		if ($show_deposit) $db['show_deposit'] = 1; else $db['show_deposit'] = 0;
		if ($show_real_ftd) $db['showRealFtdToAff'] = 1; else $db['showRealFtdToAff'] = 0;
		if ($show_credit_as_default_for_new_affiliates) $db['show_credit_as_default_for_new_affiliates'] = 1; else $db['show_credit_as_default_for_new_affiliates'] = 0;
		if ($showCreditForAM) $db['showCreditForAM'] = 1; else $db['showCreditForAM'] = 0;
		//if ($set->userInfo['id'] == "1") {
			if ($set->userInfo['userType'] == "sys") {
			if ($isSmtpDebugMode) $db['isSmtpDebugMode'] = 1; else $db['isSmtpDebugMode'] = 0;
			
		if ($showParamTwoOnReports) $db['showParamTwoOnReports'] = 1; else $db['showParamTwoOnReports'] = 0;
			
			
			
			if ($creative_iframe) $db['creative_iframe'] = 1; else $db['creative_iframe'] = 0;
			if ($creative_mobile_leader) $db['creative_mobile_leader'] = 1; else $db['creative_mobile_leader'] = 0;
			if ($creative_mobile_splash) $db['creative_mobile_splash'] = 1; else $db['creative_mobile_splash'] = 0;
			if ($creative_html5) $db['creative_html5'] = 1; else $db['creative_html5'] = 0;
			if ($creative_email) $db['creative_email'] = 1; else $db['creative_email'] = 0;
			if ($facebookshare) $db['facebookshare'] = 1; else $db['facebookshare'] = 0;
			if ($qrcode) $db['qrcode'] = 1; else $db['qrcode'] = 0;

			if ($isBasicVer) $db['isBasicVer'] = 1; else $db['isBasicVer'] = 0;
			if ($deal_cpc) $db['deal_cpc'] = 1; else $db['deal_cpc'] = 0;
			if ($showPositionsRevShareDeal) $db['showPositionsRevShareDeal'] = 1; else $db['showPositionsRevShareDeal'] = 0;
			if ($showRequierdDocsOnAffiliateDash) $db['showRequierdDocsOnAffiliateDash'] = 1; else $db['showRequierdDocsOnAffiliateDash'] = 0;
			if ($showDocumentsModule) $db['showDocumentsModule'] = 1; else $db['showDocumentsModule'] = 0;
			
			//agrement modules flag
			if ($showAgreementsModule) $db['showAgreementsModule'] = 1; else $db['showAgreementsModule'] = 0;
			//invoice module flag
			if ($showInvoiceModule) $db['showInvoiceModule'] = 1; else $db['showInvoiceModule'] = 0;
			
			if ($deal_cpl) $db['deal_cpl'] = 1; else $db['deal_cpl'] = 0;
			if ($deal_pnl) $db['deal_pnl'] = 1; else $db['deal_pnl'] = 0;
			if ($blockAccessForManagerAndAdmins) $db['blockAccessForManagerAndAdmins'] = 1; else $db['blockAccessForManagerAndAdmins'] = 0;
			if ($deal_cpm) $db['deal_cpm'] = 1; else $db['deal_cpm'] = 0;
			if ($deal_revshare) $db['deal_revshare'] = 1; else $db['deal_revshare'] = 0;
			if ($deal_revshare_spread) $db['deal_revshare_spread'] = 1; else $db['deal_revshare_spread'] = 0;
			if ($isNetwork) $db['isNetwork'] = 1; else $db['isNetwork'] = 0;
			if ($showProductsPlace) $db['showProductsPlace'] = 1; else $db['showProductsPlace'] = 0;
		}
			if ($AllowAffiliateDuplicationOnCampaignRelation) $db['AllowAffiliateDuplicationOnCampaignRelation'] = 1; else $db['AllowAffiliateDuplicationOnCampaignRelation'] = 0;
			if ($hideInvoiceSectionOnAffiliateRegPage) $db['hideInvoiceSectionOnAffiliateRegPage'] = 1; else $db['hideInvoiceSectionOnAffiliateRegPage'] = 0;
			if ($hideCommissionOnTraderReportForRevDeal) $db['hideCommissionOnTraderReportForRevDeal'] = 1; else $db['hideCommissionOnTraderReportForRevDeal'] = 0;
			if ($hideMarketingSectionOnAffiliateRegPage) $db['hideMarketingSectionOnAffiliateRegPage'] = 1; else $db['hideMarketingSectionOnAffiliateRegPage'] = 0;
			if ($introducingBrokerInterface) $db['introducingBrokerInterface'] = 1; else $db['introducingBrokerInterface'] = 0;
			if ($showMiminumDepositOnAffAccount) $db['showMiminumDepositOnAffAccount'] = 1; else $db['showMiminumDepositOnAffAccount'] = 0;
			if ($multiMerchantsPerTrader) $db['multiMerchantsPerTrader'] = 1; else $db['multiMerchantsPerTrader'] = 0;
			if ($ShowIMUserOnAffiliatesList) $db['ShowIMUserOnAffiliatesList'] = 1; else $db['ShowIMUserOnAffiliatesList'] = 0;
			if ($ShowAffiliateTypes) $db['ShowAffiliateTypes'] = 1; else $db['ShowAffiliateTypes'] = 0;
			if ($ShowEmailsOnTraderReportForAdmin) $db['ShowEmailsOnTraderReportForAdmin'] = 1; else $db['ShowEmailsOnTraderReportForAdmin'] = 0;
			if ($ShowEmailsOnTraderReportForAffiliate) $db['ShowEmailsOnTraderReportForAffiliate'] = 1; else $db['ShowEmailsOnTraderReportForAffiliate'] = 0;
			if ($allowCapthaOnReg) $db['allowCapthaOnReg'] = 1; else $db['allowCapthaOnReg'] = 0;
			if ($allowCapthaOnReset) $db['allowCapthaOnReset'] = 1; else $db['allowCapthaOnReset'] = 0;
			if ($autoRelateSubAffiliate) $db['autoRelateSubAffiliate'] = 1; else $db['autoRelateSubAffiliate'] = 0;
			if ($hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals) $db['hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals'] = 1; else $db['hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals'] = 0;
			if ($hideNetRevenueForNonRevDeals) $db['hideNetRevenueForNonRevDeals'] = 1; else $db['hideNetRevenueForNonRevDeals'] = 0;
			if ($hideFTDamountForCPADeals) $db['hideFTDamountForCPADeals'] = 1; else $db['hideFTDamountForCPADeals'] = 0;
			
			if ($hideTotalDepositForCPADeals) $db['hideTotalDepositForCPADeals'] = 1; else $db['hideTotalDepositForCPADeals'] = 0;
			
			
			if ($AskDocTypePassport  ) $db['AskDocTypePassport'] = 1; else $db['AskDocTypePassport'] = 0;
			if ($AskDocTypeAddress) $db['AskDocTypeAddress'] = 1; else $db['AskDocTypeAddress'] = 0;
			if ($AskDocTypeCompany) $db['AskDocTypeCompany'] = 1; else $db['AskDocTypeCompany'] = 0;
			
			
			if ($hideDepositAmountForCPADeals) $db['hideDepositAmountForCPADeals'] = 1; else $db['hideDepositAmountForCPADeals'] = 0;
			if ($hideBonusAmountForCPADeals) $db['hideBonusAmountForCPADeals'] = 1; else $db['hideBonusAmountForCPADeals'] = 0;
			if ($hideWithdrawalAmountForCPADeals) $db['hideWithdrawalAmountForCPADeals'] = 1; else $db['hideWithdrawalAmountForCPADealsy'] = 0;
			if ($displayLastMessageFieldsOnReports) $db['displayLastMessageFieldsOnReports'] = 1; else $db['displayLastMessageFieldsOnReports'] = 0;
			
		
		//if ($set->userInfo['id'] == "1") {		
			if ($hidePendingProcessHighAmountDeposit) $db['hidePendingProcessHighAmountDeposit'] = 1; else $db['hidePendingProcessHighAmountDeposit'] = 0;
		if ($set->userInfo['userType'] == "sys") {		

			if ($deal_tier) $db['deal_tier'] = 1; else $db['deal_tier'] = 0;
			if ($export) $db['export'] = 1; else $db['export'] = 0;
			if ($multi) $db['multi'] = 1; else $db['multi'] = 0;
		
			
			if ($hidePoweredByABLogo) $db['hidePoweredByABLogo'] = 1; else $db['hidePoweredByABLogo'] = 0;
			if ($hideBrandsDescriptionfromAffiliateFooter) $db['hideBrandsDescriptionfromAffiliateFooter'] = 1; else $db['hideBrandsDescriptionfromAffiliateFooter'] = 0;
		
			
			if ($ShowGraphOnDashBoards) $db['ShowGraphOnDashBoards'] = 1; else $db['ShowGraphOnDashBoards'] = 0;
			
		}
			if ($blockAffiliateLogin) $db['blockAffiliateLogin'] = 1; else $db['blockAffiliateLogin'] = 0;
			if ($showAllCreativesToAffiliate) $db['showAllCreativesToAffiliate'] = 1; else $db['showAllCreativesToAffiliate'] = 0;
			if ($isOffice365) $db['isOffice365'] = 1; else $db['isOffice365'] = 0;
			if ($showVolumeForAffiliate) $db['showVolumeForAffiliate'] = 1; else $db['showVolumeForAffiliate'] = 0;
			if ($showAffiliateRiskForAffiliate) $db['showAffiliateRiskForAffiliate'] = 1; else $db['showAffiliateRiskForAffiliate'] = 0;
			if ($showDCPAonAffiliateComStruc) $db['showDCPAonAffiliateComStruc'] = 1; else $db['showDCPAonAffiliateComStruc'] = 0;
			if ($affiliateNewsletterCheckboxValue) $db['affiliateNewsletterCheckboxValue'] = 1; else $db['affiliateNewsletterCheckboxValue'] = 0;
			if ($exportLangCreativeNameWithParam) $db['exportLangCreativeNameWithParam'] = 1; else $db['exportLangCreativeNameWithParam'] = 0;
			if ($showTitleOnLoginPage) $db['showTitleOnLoginPage'] = 1; else $db['showTitleOnLoginPage'] = 0;
			if ($showDealTypeHistoryToAM) $db['showDealTypeHistoryToAM'] = 1; else $db['showDealTypeHistoryToAM'] = 0;
			if ($AllowDealChangesByManager) $db['AllowDealChangesByManager'] = 1; else $db['AllowDealChangesByManager'] = 0;
			if ($AllowManagerEditrCreative) $db['AllowManagerEditrCreative'] = 1; else $db['AllowManagerEditrCreative'] = 0;
			if ($overrideByCoupon) $db['overrideByCoupon'] = 1; else $db['overrideByCoupon'] = 0;
			if ($autoRelateNewAffiliateToAllMerchants) $db['autoRelateNewAffiliateToAllMerchants'] = 1; else $db['autoRelateNewAffiliateToAllMerchants'] = 0;
			if ($showGroupValuesOnAffReg) $db['showGroupValuesOnAffReg'] = 1; else $db['showGroupValuesOnAffReg'] = 0;
			if($showGroupsLanguages!="") 
				$db['showGroupsLanguages'] = $showGroupsLanguages;
			 //var_dump($db);
			 //die();
			$db['AskDocSentence'] =  $AskDocSentence;
	
		dbAdd($db, 'settings');
		
		// Change Network Bonus
		/*if ($bonus_db['title'] AND $bonus_db['min_ftd'] AND $bonus_db['bonus_amount']) {
			if ($bonus_db_valid) $bonus_db['valid'] = 1;
			dbAdd($bonus_db,"network_bonus");
			}
		if (count($bonus_ids) > 0) {
			for ($i=0; $i<count($bonus_ids); $i++) {
				if ($bonus_delete[$i]) {
					function_mysql_query("DELETE FROM network_bonus WHERE id='".$bonus_ids[$i]."'",__FILE__);
					continue;
					}
				function_mysql_query("UPDATE network_bonus SET valid='".($bonus_valid[$i] ? '1' : '0')."', title='".$bonus_title[$i]."', group_id='".$bonus_group_id[$i]."', min_ftd='".$bonus_min_ftd[$i]."', bonus_amount='".$bonus_bonus_amount[$i]."' WHERE id='".$bonus_ids[$i]."'",__FILE__);
				}
			}*/
		
		_goto($set->basepage . "?tab=" . $tab);
		break;
		
	default:

		//$set->pageTitle = lang('Configuration');
		$set->breadcrumb_title = lang('Configuration');
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="admin/">'.lang('Dashboard').'</a></li>
			<li><a href="admin/config.php" class="arrow-left">'.lang('Configuration').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		$selected_countries = "";
		if(isset($set->hideCountriesOnRegistration) && $set->hideCountriesOnRegistration!=""){
			
				$arrCountries = explode("|", $set->hideCountriesOnRegistration);
				
				
				$co = [];
				$newCountriesArr = $countriesArr;
				
				foreach($arrCountries as $key=>$value){
					$co[] = $newCountriesArr[$value];
				}
				$selected_countries = implode(",",$co);
				$set->hideCountriesOnRegistration = implode(",",$arrCountries);
				
		}
		
		$set->content  = '<style>		
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
				action = $("form#frmMain").attr("action");
				
				if(typeof action === "undefined"){
					action = window.location.href;
					
				}
				
				act = action.split(/[?#]/)[0];
				act = act + "?tab=" + tabtoopen;
				$("form#frmMain").attr("action", act);
				
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
							
							if(search_txt.search(open_tab)!==-1){
								$(this).show();
							}
							else{
								$(this).hide();
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
							console.log("out")
						}
					});
					}
					
				}
			}

			$("#config_filter").on("keyup",function(){
				var txtVal = $(this).val();
				if (txtVal != "") {
					$(".tblDetails").show();
					$.each($(".tblDetails"), function (i, o) {
						var match = $("tbody td:contains-ci(\'" + txtVal + "\'),thead td:contains-ci(\'" + txtVal + "\')", this);
						match.parent().siblings().hide();                         //  <<=== [LINE ADD]
						
						if (match.length > 0) {
							$(match).parent("tr").show();
							$(this).parent().prev().show();
							
							txt = $(this).parent().prev().data("tab2");
							$("ul.vertical li a").each(function(){
								console.log(txt + " ---- "+$(this).data("tab"));
									if(txt == $(this).data("tab")){
										$(this).css("color","grey");
									}
								});
						}
						else {
							$(this).hide();
							$(this).parent().prev().hide();
							
							txt = $(this).parent().prev().data("tab2");
							$("ul.vertical li a").each(function(){
								
									if(txt == $(this).data("tab")){
										$(this).css("color","black");
									}
							});
						}
					});
				} else {
					// When there is no input or clean again, show everything back
					$(".tblDetails").parent().prev().show();
					$(".tblDetails,.tblDetails tr").show();
					$(".config_tabs").show();
					$("ul.vertical li a").css("color","black");
				}
				if($(".tblDetails:visible").length == 0)
				{
					$(".config_tabs:first").find(".message").remove();
					$(".config_tabs:first").append("<p class=\"message\">Sorry No results found!</p>");
					$(".btnsave").hide();
				}
				else{
					$(".message").remove();
					$(".btnsave").show();
				}
			});


			
			// jQuery expression for case-insensitive filter
			$.extend($.expr[":"], {
				"contains-ci": function (elem, i, match, array) {
					return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
				}
			});
			
			$("#filter_form").submit(function(){
				var tab_text = $("#config_filter").val();
				show_hide_tabs(tab_text,"search");
				return false;
			});
			
		});
		</script>
		<aside>
		<ul class="vertical">
  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">'. lang('Configuration') .'</span><form style="display:inline-flex;float:right;">
  <div class="filter">'.lang('Find') .': </div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div></li>
  <li><a href="javascript:void(0)" data-tab="all">'. lang('All') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="affiliate_program_settings">'. lang('Affiliate Program Settings') .'</a></li>
  <!--<li><a href="javascript:void(0)" data-tab="network_bonus">'. lang('Network Bonuses')  .'</a></li>-->
    <li><a href="javascript:void(0)" data-tab="smtp_server">'. lang('SMTP Server') .'</a></li>
	';
	if ($set->userInfo['userType'] == "sys") {
  $set->content .= '<li><a href="javascript:void(0)" data-tab="creative_options">'. lang('Creative Options') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="deal_type_options">'. lang('Deal Type Options') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="more_options">'. lang('More Options') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="tracking_options_visibility">'. lang('Tracking Options Visibility') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="integration_configuration">'.lang('Integration Configuration') .'</a></li>';
	}
  $set->content .='<li><a href="javascript:void(0)" data-tab="other_settings">'.lang('Other Settings') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="billing_report">'.lang('Billing Report') .' - ' .lang('Payments Display Configuration') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="affiliate_registration_mandatory_fields">'.lang('Affiliate Registration Mandatory Fields') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="registration_settings">'.lang('Registration Settings') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="affiliate_display_configuration">'.lang('Affiliate Display Configuration') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="affiliate_manager_display_configuration">'.lang('Affiliate Manager Display Configuration') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="available_payments_method">'.lang('Available Payments Method') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="pixel_triggers">'.lang('Pixel Triggers') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="tracker_configuration">'.lang('Tracker Configuration') .'</a></li>';
  if ($set->showDocumentsModule || $set->userInfo['userType'] == "sys") {	
  $set->content .='<li><a href="javascript:void(0)" data-tab="documents">'.lang('Documents') .'</a></li>';
  }
$set->content .=' <li><a href="javascript:void(0)" data-tab="terms_and_conditions">'.lang('Terms and Conditions') .'</a></li></ul></aside>
<div class="main">';

if(isset($tab)){
		$set->content.="
			<script type='text/javascript'>
			$(document).ready(function(){
			var hash = '". $tab ."';
			 if(hash != '')
			 {
				$('a[data-tab=".$tab."]').click();
			 }
			});
		</script>";
}
		/*
		$brandQ = function_mysql_query('SELECT LOWER(name) AS name FROM merchants WHERE valid=1 ORDER BY name ASC',__FILE__);
		$brandStr = '';
		$count=0;
		$brandStr.='<option value="-1">Select Brand</option>';
		while($brandRow = mysql_fetch_assoc($brandQ)){
						//die($_POST['mt_traderID']);
						$brandStr.='<option '.(((isset($_POST['mt_brand']) && $_POST['mt_brand']==$brandRow['name']) OR mysql_num_rows($brandQ)==1) ? 'selected' : '').' value="'.$brandRow['name'].'">'.$brandRow['name'].'</option>';
						$count++;
		}

		$brand = mysql_result(function_mysql_query('SELECT LOWER(name) FROM merchants ORDER BY id ASC LIMIT 0,1',__FILE__),0,0);
		$missingTrader='<div style="position:relative; height:1px; margin-top:20px; margin-bottom:20px; background:#000"></div>
		<div class="normalTableTitle">'.('Missing Trader Handler').'</div>
		<div style="text-align:left; margin-top:10px; margin-left:10px">
		<form method="POST"><select style="height:29px; margin-right:10px" name="mt_brand">'.$brandStr.'</select><input type="text" style="margin-right:10px" name="mt_traderID" value="'.(isset($_POST['mt_traderID']) ? trim($_POST['mt_traderID']) : '').'"/><input type="submit" value="Search Trader"/></form>';

		if(isset($_POST['mt_traderID']) && isset($_POST['mt_brand']) && $_POST['mt_brand']!=-1 && $_POST['mt_traderID']){
			$brand = $_POST['mt_brand'];		
			$row = mysql_fetch_assoc(function_mysql_query('SELECT ctag FROM data_reg_'.$brand.' WHERE trader_id='.intval(trim($_POST['mt_traderID'])),__FILE__));
			$ctag = $row['ctag'];
			$orgCtag = $ctag;
			if(!isset($_POST['mt_affID'])){
				$ctag = explode('-',$ctag);
				$affID = substr($ctag[0],1);
				$missingTrader.='<div style="margin-top:10px"><form method="POST"><input type="hidden" name="mt_traderID" value="'.intval(trim($_POST['mt_traderID'])).'"/><input type="text" style="margin-right:10px" name="mt_affID" value="'.$affID.'"/><input type="submit" value="Update Aff"/></form></div>';
			}else{
				if(!isset($_POST['mt_traderID']) OR $_POST['mt_traderID']=='' OR !is_numeric($_POST['mt_traderID'])){
					$missingTrader.='Error occured #1172, please contact Affiliate Buddies support team.';
				}
				if(!isset($_POST['mt_affID']) OR $_POST['mt_affID']=='' OR !is_numeric($_POST['mt_affID'])){
					$missingTrader.='Error occured #1131, please contact Affiliate Buddies support team.';
				}
				$ctag = explode('-',$ctag);
				$affID = trim($_POST['mt_affID']);
				$ctag[0] = 'a'.intval($affID);
				$ctag = implode('-',$ctag);
				$missingTrader.='Affiliate ID changed from ----> '.$orgCtag.' ------ to ----> '.$ctag.'<BR><BR>';
				$countTraders = mysql_fetch_assoc(function_mysql_query('SELECT COUNT(id) AS c FROM data_reg_'.$brand.' WHERE trader_id='.intval($_POST['mt_traderID']),__FILE__));
				if($countTraders['c']==1){
					function_mysql_query('UPDATE data_reg_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE  trader_id='.intval($_POST['mt_traderID']),__FILE__);
					function_mysql_query('UPDATE data_sales_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE trader_id='.intval($_POST['mt_traderID']),__FILE__);
					//function_mysql_query('UPDATE data_reg_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE affiliate_id=500 AND trader_id='.intval($_POST['mt_traderID']),__FILE__);
					//function_mysql_query('UPDATE data_sales_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE affiliate_id=500 AND trader_id='.intval($_POST['mt_traderID']),__FILE__);
					$missingTrader.='<div style="font-weight:bold; font-size:18px">Done!</div>';
				}else{
					$missingTrader.='Error occured #2166, please contact Affiliate Buddies support team. ';
				}
				$missingTrader.='</div>';
			}
		}else if(isset($_POST['mt_traderID']) && isset($_POST['mt_brand'])){
			$missingTrader.='<p style="font-size:12px; color:RED">Please select brand & insert a Trader ID.</p>';
		}
		
		*/
		$set->show_real_ftd = mysql_result(function_mysql_query('SELECT showRealFtdToAff FROM settings LIMIT 0,1',__FILE__),0,0);

		$set->content .= '<form id="frmMain"method="post" enctype="multipart/form-data">
					<input type="hidden" name="act" value="save" />
					<div id="affiliate_program_settings" data-tab="affiliate_program_settings" class="config_tabs">
					<div class="normalTableTitle" data-tab2="affiliate_program_settings">'.lang('Affiliate Program Settings').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
						<tr>
							<td width="50%" align="left" valign="top">
								<table border="0" cellpadding="5" cellspacing="0">
								<tbody>
									<tr>
										<td width="150" align="left">'.lang('Dashboard Main Title').':</td>
										<td width="250"><input type="text" name="db[dashBoardMainTitle]" value="'.$set->dashBoardMainTitle.'" style="width: 250px;" />	<span style="font-size:9px" align="left">'.lang('Leave blank for system default').'.</span></td>
									
									</tr><tr>
									<td style="padding-top:10px"></td>
									</tr><tr>
												<td><input type="checkbox" name="showTitleOnLoginPage" '.($set->showTitleOnLoginPage ? 'checked="checked"' : '').' /> '.lang('Show Title On Login Page').'</td>
										</tr><tr>
									<td style="padding-top:10px"></td>
									</tr><tr>
									<td valign="top">'.lang('Affiliate Login Background Image Path').':</td>
									<td><input text="text" id="affiliateLoginImage" name="db[affiliateLoginImage]" style="width:600px" value="'.$set->affiliateLoginImage.'"/></td>
									</tr><tr>
										<td width="150" align="left">'.lang('Software Name').':</td>
										<td><input type="text" name="db[webTitle]" value="'.$set->webTitle.'" style="width: 250px;" /></td>
									</tr><tr>
										<td align="left">'.lang('Sub Affiliate Commission').':</td>
										<td><input type="text" name="db[sub_com]" value="'.$set->sub_com.'" style="width: 100px; text-align: center;" maxlength="3" />%</td>
									</tr><tr>
										<td align="left">'.lang('Number of rows after search').':</td>
										<td><input type="text" name="db[rowsNumberAfterSearch]" value="'.$set->rowsNumberAfterSearch.'" style="width: 50px; text-align: center;" maxlength="4" /></td>
									</tr><tr>
										<td align="left">'.lang('Session timeout after total minutes of').':</td>
										<td><input type="text" name="db[login_session_duration]" value="'.$set->login_session_duration.'" style="width: 50px; text-align: center;" maxlength="4" /></td>
									</tr><tr>
										<td align="left">'.lang('Charts Themes').':</td>
										<td><select  name="db[chartTheme]">
										<option value="default"'. ($set->chartTheme=='default'?' selected':'') .'>'. lang('Default') .'</option>
										<option value="dark_unica" '. ($set->chartTheme=='dark_unica'?'selected':'') .'>'. lang('Dark Unica') .'</option>
										<option value="sand_signika" '. ($set->chartTheme=='sand_signika'?'selected':'') .'>'. lang('Sand Signika') .'</option>
										<option value="grid_light" '. ($set->chartTheme=='grid_light'?'selected':'') .'>'. lang('Grid Light') .'</option>
										</select></td>
									</tr><tr>
									<td colspan=2><input type="checkbox" name="ShowIMUserOnAffiliatesList" '.($set->ShowIMUserOnAffiliatesList ? 'checked="checked"' : '').' /> '.lang('Show IMUser On Affiliates List And Report From Admin').'</td>
									</tr><tr>
									<td colspan=2><input type="checkbox" name="displayLastMessageFieldsOnReports" '.($set->displayLastMessageFieldsOnReports ? 'checked="checked"' : '').' /> '.lang('Show Last Message Fields On Reports For Admin').'</td>
									</tr><tr>
									<td colspan=2><input type="checkbox" name="ShowEmailsOnTraderReportForAdmin" '.($set->ShowEmailsOnTraderReportForAdmin ? 'checked="checked"' : '').' /> '.lang('Show Emails On Trader Report For Admin').'</td>
									</tr><tr>
									<td colspan=2><input type="checkbox" name="ShowAffiliateTypes" '.($set->ShowAffiliateTypes ? 'checked="checked"' : '').' /> '.lang('Show Affiliate Types').'</td>
									</tr><tr>
									<td colspan=2><input type="checkbox" name="qualifiedCommissionOnCPAonly" '.($set->qualifiedCommissionOnCPAonly ? 'checked="checked"' : '').' /> '.lang('Set The Qualified Commission For CPA Only').'</td>
									
									</tr>
									</tbody>
								</table>
							</td>
							<!--td width="50%" align="left" valign="top">
								<table border="0" cellpadding="0" cellspacing="5">
									<tr>
										<td align="left" valign="top" style="padding-top: 8px;">'.lang('Revenue Calculation').':</td>
										<td align="left"><input type="text" name="db[revenue_formula]" value="'.$set->revenue_formula.'" style="width: 300px;" /><br /><span style="font-size: 11px;"><b>'.lang('Variables').':</b> {deposits} / {bonus} / {withdrawals} / {chargebacks}</span></td>
									</tr><tr>
										<td align="left">'.lang('Qualify Commission').':</td>
										<td align="left"><input type="radio" name="db[qualify_type]" value="trades" '.($set->qualify_type == "trades" ? 'checked="checked"' : '').' /> '.lang('Min. Trades').' &nbsp; <input type="radio" name="db[qualify_type]" value="volume" '.($set->qualify_type == "volume" ? 'checked="checked"' : '').' /> '.lang('Min. Volume').' <input type="text" name="db[qualify_amount]" value="'.$set->qualify_amount.'" style="width: 100px; text-align: center;" maxlength="5" /></td>
									</tr>
								</table>
							</td-->
						</tr></tbody></table>
						</div>
						</div>
						
						<!--<div data-tab="network_bonus" class="config_tabs">-->
						';
			
			/* $i=1;
			$bonusqq=function_mysql_query("SELECT * FROM network_bonus ORDER BY group_id ASC, bonus_amount ASC",__FILE__);
			
			
			while($bonusww=mysql_fetch_assoc($bonusqq)) {
				$listBonuses .= '<tr '.($i % 2 ? 'class="trLine"' : '').'>
								<td>'.$i.'<input type="hidden" name="bonus_ids[]" value="'.$bonusww['id'].'" /></td>
								<td align="center"><input type="text" name="bonus_title[]" value="'.$bonusww['title'].'" style="width: 250px;" /></td>
								<td align="center"><select name="bonus_group_id[]" style="width: 150px;"><option value="99999">'.lang('All').'</option><option value="">'.lang('General').'</option>'.listGroups($bonusww['group_id']).'</select></td>
								<td align="center"><input type="text" name="bonus_min_ftd[]" value="'.$bonusww['min_ftd'].'" maxlength="5" style="width: 100px; text-align: center;" /></td>
								<td align="center">$ <input type="text" name="bonus_bonus_amount[]" value="'.$bonusww['bonus_amount'].'" style="width: 100px; text-align: center;" maxlength="5" /></td>
								<td align="center"><input type="checkbox" name="bonus_valid[]" '.($bonusww['valid'] ? 'checked="checked"' : '').' /> '.lang('Active').' <input type="checkbox" name="bonus_delete[]" /> '.lang('Delete').'</td>
								<td align="center"></td>
							</tr>';
				$i++;
				} */


					$set->content .= '<!--<div class="normalTableTitle" data-tab2="network_bonus" >'.lang('Network Bonuses').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
							<thead><tr>
								<td width="80" align="center">#</td>
								<td align="center">'.lang('Bonus Description').'</td>
								<td align="center">'.lang('Group').'</td>
								<td align="center">'.lang('Min. FTDs').'</td>
								<td align="center">'.lang('Bonus Amount').'</td>
								<td align="center">'.lang('Actions').'</td>
								<td></td>
							</tr></thead><tfoot>'.$listBonuses.'<tr style="background: #D9D9D9;">
								<td></td>
								<td align="center"><input type="text" name="bonus_db[title]" value="" style="width: 250px;" /></td>
								<td align="center"><select name="bonus_db[group_id]" style="width: 150px;"><option value="99999">'.lang('All').'</option><option value="">'.lang('General').'</option>'.listGroups().'</select></td>
								<td align="center"><input type="text" name="bonus_db[min_ftd]" value="" maxlength="5" style="width: 100px; text-align: center;" /></td>
								<td align="center">$ <input type="text" name="bonus_db[bonus_amount]" value="" style="width: 100px; text-align: center;" maxlength="5" /></td>
								<td align="center"><input type="checkbox" name="bonus_db_valid" /> '.lang('Active').'</td>
								<td align="center"><input type="submit" value="'.lang('Save').'" /></td>
							</tfoot></tr>
						</table>
					</div>
					</div>-->
					
					<div id="smtp_server" data-tab="smtp_server" class="config_tabs">
					<div class="normalTableTitle" data-tab2="smtp_server" >'.lang('SMTP Server').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0" >
						<tr>
										<td align="left">'.lang('Software Outbox E-mail').':</td>
										<td><input type="text" name="db[webMail]" value="'.$set->webMail.'" style="width: 250px;" /></td>
									</tr><tr>
						<td align="left">'.lang('Reply To').':</td>
										<td><input type="text" name="db[mail_replyTo]" value="'.$set->mail_replyTo.'" style="width: 250px;" /></td>
									</tr><tr>
						
								<td width="150" align="left">'.lang('Mail Server').':</td>
								<td><input type="text" name="db[mail_server]" value="'.$set->mail_server.'" style="width: 250px;" /></td>
							</tr><tr>
								<td align="left">'.lang('Mail Username').':</td>
								<td><input type="text" name="db[mail_username]" value="'.$set->mail_username.'" style="width: 250px;" /></td>
							</tr><tr>
								<td align="left">'.lang('Mail Password').':</td>
								<td><input type="password" name="db[mail_password]" value="'.$set->mail_password.'" style="width: 250px;" /></td>
</tr><tr>
								<td align="left">'.lang('Mail Port').':</td>
								<td><input type="text" name="db[mail_Port]" value="'.$set->mail_Port.'" style="width: 50px;" /></td>
							</tr><tr>
								<td align="left">'.lang('Requierd Authentication').':</td>
								<td align="left"><input type="checkbox" name="SMTPAuth" '.($set->SMTPAuth ? 'checked="checked"' : '').' /></td>
							</tr><tr>
								<td align="left">'.lang('Type of encrypted connection').':</td>
								<td align="left">
								<select name="db[SMTPSecure]" style="width: 150px;" >
									<option value="">'.lang('None').'</option>
									<option value="SSL" '.(strtolower($set->SMTPSecure)=="ssl" ? ' selected' : '').'>'.lang('SSL').'</option>'.lang('SSL').'	
									<option value="TLS" '.(strtolower($set->SMTPSecure)=="tls" ? ' selected' : '').'>'.lang('TLS').'</option>'.lang('TLS').'
									</select></td>
							</tr>
							<tr>
						
							<td colspan =2><input type="checkbox" name="isOffice365" '.($set->isOffice365 ? 'checked="checked"' : '').' /> '.lang('Is Office365 SMTP Server?').'</td></tr>';
							
						//if ($set->userInfo['id'] == "1") {
						if ($set->userInfo['userType'] == "sys") {
							$set->content .='<tr><td colspan=2><input type="checkbox" name="isSmtpDebugMode" '.($set->isSmtpDebugMode ? 'checked="checked"' : '').' /> '.lang('Is Smtp Debug Mode?').'</td></tr>';
						}
					$set->content .='
								
							</tr>
							

						</table>
					</div>
					</div>
					';
						//if ($set->userInfo['id'] == "1") {
						if ($set->userInfo['userType'] == "sys") {
							
							
							
				$creativefeedUrl = "";
				
				if (!empty($set->apiStaticIP) && !empty($set->apiToken)) {
					// $creativefeedUrl = $set->webAddress .'/affiliate/apifeed.php?apiToken='. $db['apiToken'].'&apiStaticIP='. $db['apiStaticIP'].'&affiliate_id='. $id  .'&fromdate=' . date("Y-m-01",strtotime("-1 Months"));
					$creativefeedUrl = $set->webAddress .'api/feeds/creative.php?apiToken='. $set->apiToken;
					
				}
				
				
				$apiFeed = '
				
						
																	
							<table class="normal" width="100%" border="0" cellpadding="0" cellspacing="0">
								<thead><tr>
									<td align="center">'.lang('Access Type').'</td>
									<td align="center">'.lang('Company Static IP').'</td>
									<td align="center">'.lang('Token').'</td>
									<td align="center">'.lang('FeedUrl').'</td>
									<td align="center">'.lang('Status').'</td>
									<td align="center">'.lang('Action').'</td>
								</tr></thead><tfoot>
								<tr>
									<td align="center"><select name="db[apiAccessType]">
                                                                        <option '.($set->apiAccessType=='' ? " selected " : "").' value="" selected>' . lang('None') . '</option>
                                                                        <option '.($set->apiAccessType=='creative' ? " selected " : "").'value="creative">' . lang('Creative') . '</option>
                                                                    </select></td>
									
									<td align="center"><input type="text" name="db[apiStaticIP]" value="'.$set->apiStaticIP.'"  style="width:100px;"/></td>
									<td align="center"><span><input style="width: 240px;" id="apiToken" type="text" name="db[apiToken]" value="'.$set->apiToken.'" /></span><span>&nbsp;</span><span><button id="putDefaultTextGuid">'.lang('Generate').'</button></span></td>
									<td align="center"><textarea style="width:400px;height:50px;" type="text"  readonly >'.$creativefeedUrl .'</textarea></td>
									<td align="center">'.(empty($creativefeedUrl) || $set->apiAccessType=='None' || $set->apiAccessType=='' ?lang('Inactive') : lang('Active')).'</td>
									<td align="center">
									<input type="submit" value="'.lang('Update').'" />
									</td>
									
								</tr>
								</tfoot>
							</table>
							<div style="margin-top:20px;margin-left:15px;">* '.lang('Update empty values to delete active permission').'.</div>
						
						<script>
				
				function S4() {
				return (((1+Math.random())*0x10000)|0).toString(16).substring(1); 
				}

				$("#putDefaultTextGuid").click(function()	{
					guid = (S4() + S4() + "-" + S4() + "-4" + S4().substr(0,3) + "-" + S4() + "-" + S4() + S4() + S4()).toLowerCase();
							$("#apiToken").val(guid);
							return false;
						}); 
						
				</script>';

				$allLangs = "";
				$langqq=mysql_query("SELECT * FROM languages WHERE valid='1' ORDER BY title ASC");
				$allLangOptions = "";
				while ($langww=mysql_fetch_assoc($langqq)) {
					//for multilang
					$allLangOptions   .= '<option value="'.$langww['title'].'">'.$langww['title'].'</option>';
					if($set->defaultLangOfSystem == ""){
						$allLangs .= "<option value=". $langww['lngCode'] . ($set->defaultLangOfSystem== "ENG"?" selected":'') .">". $langww['title'] ."</option>";
					}
					else{
					$allLangs .= "<option value=". $langww['lngCode'] . ($set->defaultLangOfSystem== $langww['lngCode']?" selected":'') .">". $langww['title'] ."</option>";
					}
				}
					
				$set->content .= '
				<link rel="stylesheet" href="../css/multiple-select.css"/>
				<script src="../js/multiple-select.js"></script>
				<script>
				$(document).ready(function(){
				$("#multipleLangs option[value=\'\']").remove();
						$("#multipleLangs").multipleSelect({
									width: 200,
									placeholder: "'.lang('Select Languages').'"
								});
						$("#multipleLangs").change(function(){
							$("#multi_languages").val($(this).val());
						}); 
						
						var selects = "'. $set->multi_languages .'";
						selects = selects.split(",");
				
						$("#multipleLangs").multipleSelect("setSelects",selects);
						$("#multipleLangs").multipleSelect("refresh");
						});
				</script>
						<div id="creative_options" data-tab="creative_options" class="config_tabs">
						<div class="normalTableTitle" data-tab2="creative_options">'.lang('Creative Options').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tr>
						<td width="25%"><input type="checkbox" name="creative_iframe" '.($set->creative_iframe ? 'checked="checked"' : '').' /> '.lang('IFrame (Widget)').'</td>
						<td><input type="checkbox" name="creative_mobile_leader" '.($set->creative_mobile_leader ? 'checked="checked"' : '').' /> '.lang('Mobile Leaderboard').'</td>
						<td><input type="checkbox" name="creative_mobile_splash" '.($set->creative_mobile_splash ? 'checked="checked"' : '').' /> '.lang('Mobile Splash').'</td>
						<td><input type="checkbox" name="creative_email" '.($set->creative_email ? 'checked="checked"' : '').' /> '.lang('E-mail').'</td>
						<td><input type="checkbox" name="creative_html5" '.($set->creative_html5 ? 'checked="checked"' : '').' /> '.lang('Show HTML 5').'</td>
						</tr>
						</table>
						</div>
						</div>
						
						<div id ="deal_type_options" data-tab="deal_type_options" class="config_tabs">
						<div class="normalTableTitle" data-tab2="deal_type_options">'.lang('Deal Type Options').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tr>
						<td width="25%"><input type="checkbox" name="deal_cpl" '.($set->deal_cpl ? 'checked="checked"' : '').' /> '.lang('CPL').'</td>
						<td width="25%"><input type="checkbox" name="deal_pnl" '.($set->deal_pnl ? 'checked="checked"' : '').' /> '.lang('PNL').'</td>
					    <td><input type="checkbox" name="deal_cpm" '.($set->deal_cpm ? 'checked="checked"' : '').' /> '.lang('CPM').'</td>
						<td><input type="checkbox" name="deal_cpc" '.($set->deal_cpc ? 'checked="checked"' : '').' /> '.lang('CPC').'</td>
						<td><input type="checkbox" name="deal_tier" '.($set->deal_tier ? 'checked="checked"' : '').' /> '.lang('Tier Program').'</td>
						<td><input type="checkbox" name="showPositionsRevShareDeal" '.($set->showPositionsRevShareDeal ? 'checked="checked"' : '').' /> '.lang('Show Positions Rev Share Deal').'</td>
						<td><input type="checkbox" name="deal_revshare" '.($set->deal_revshare ? 'checked="checked"' : '').' /> '.lang('Revenue Share').'</td>
						<td><input type="checkbox" name="deal_revshare_spread" '.($set->deal_revshare_spread ? 'checked="checked"' : '').' /> '.lang('Revenue Share Spread').'</td>
						</tr>
						</table>
						</div>
						</div>
						
						<div id="more_options" data-tab="more_options" class="config_tabs">
						<div class="normalTableTitle" data-tab2="more_options">'.lang('More Options').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tr>
						<td width="25%"><input type="checkbox" name="export" '.($set->export ? 'checked="checked"' : '').' /> '.lang('Export To CSV').'</td>
						</tr>
						<tr>
						<!--td><input type="checkbox" name="multi" '.($set->multi ? 'checked="checked"' : '').' /> '.lang('Multi Languages').' '.($set->multi ? '<input type="text" name="db[multi_languages]" value="'.$set->multi_languages.'" />' : '').'</td-->
						<td><input type="checkbox" name="multi" '.($set->multi ? 'checked="checked"' : '').' /> '.lang('Multi Languages').' '.($set->multi ? 
						
						'
						<input type="hidden" name="db[multi_languages]" id="multi_languages"/>
						<select name ="multipleLangs" id = "multipleLangs" multiple="multiple">
						'. $allLangOptions .'
						</select>
						
						' : '').'</td>
						</tr>
						<tr>
						<!--td width="25%">'.lang('Default System Interface Language').'<input type="text" name="db[defaultLangOfSystem]" value="'.($set->defaultLangOfSystem) .'" /></td-->
						<td width="25%">'.lang('Default System Interface Language').'
						<select name="db[defaultLangOfSystem]">
						'. $allLangs .'
						</select>
						</td>
						</tr>
						<tr>
						<td><input type="checkbox" name="isNetwork" '.($set->isNetwork ? 'checked="checked"' : '').' /> '.lang('Is Network').'</td>
						</tr><tr>
						<td><input type="checkbox" name="blockAccessForManagerAndAdmins" '.($set->blockAccessForManagerAndAdmins ? 'checked="checked"' : '').' /> '.lang('Block Access For Manager And Admins').'</td>
						</tr><tr>
						<td><input type="checkbox" name="multiMerchantsPerTrader" '.($set->multiMerchantsPerTrader ? 'checked="checked"' : '').' /> '.lang('Multi Merchants Per Trader').'<br>(' .lang("Networks can't have that option checked").')</td>		
						</tr><tr>
						<td><input type="checkbox" name="isBasicVer" '.($set->isBasicVer ? 'checked="checked"' : '').' /> '.lang('Is Basic system?').'<br>(' .lang("many features will be blocked").')</td>
						</tr><tr>
						<td><input type="checkbox" name="showDocumentsModule" '.($set->showDocumentsModule ? 'checked="checked"' : '').' /> '.lang('Show Documents Module').'</td>		
						</tr><tr>
						<td><input type="checkbox" name="introducingBrokerInterface" '.($set->introducingBrokerInterface ? 'checked="checked"' : '').' /> '.lang('introducing Broker Interface').'</td>
						</tr><tr>
						<td><input type="checkbox" name="showProductsPlace" '.($set->showProductsPlace ? 'checked="checked"' : '').' /> '.lang('Show Products Place').'</td>		
						</tr><tr>
						<td><input type="checkbox" name="ShowGraphOnDashBoards" '.($set->ShowGraphOnDashBoards ? 'checked="checked"' : '').' /> '.lang('Show Graph On DashBoards').'</td>
						</tr><tr>
						<td><input type="checkbox" name="showAgreementsModule" '.($set->showAgreementsModule ? 'checked="checked"' : '').' /> '.lang('Show Agreements Module').'</td>
						</tr><tr>
						<td><input type="checkbox" name="showInvoiceModule" '.($set->showInvoiceModule ? 'checked="checked"' : '').' /> '.lang('Show Invoice Module').'</td>
						</tr>
						
						
						</table>
						</div>
						</div>
						
						<!--<div class="normalTableTitle">
							<table width="100%" border="0" cellpadding="5" cellspacing="0"><tr>
								<td width="20%"><div class="normalTableTitle">'.lang('Creative Options').'</div></td>
								<td width="25%"><div class="normalTableTitle">'.lang('Deals Type').'</div></td>
								<td width="50%"><div class="normalTableTitle"><center>'.lang('More Options').'</center></div></td>
							</tr></table>
						</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0">
							<tr>
								<td width="25%"><input type="checkbox" name="creative_iframe" '.($set->creative_iframe ? 'checked="checked"' : '').' /> '.lang('IFrame (Widget)').'</td>
								<td width="25%"><input type="checkbox" name="deal_cpl" '.($set->deal_cpl ? 'checked="checked"' : '').' /> '.lang('CPL').'</td>
								<td width="25%"><input type="checkbox" name="deal_pnl" '.($set->deal_pnl ? 'checked="checked"' : '').' /> '.lang('PNL').'</td>
								<td width="25%"><input type="checkbox" name="export" '.($set->export ? 'checked="checked"' : '').' /> '.lang('Export To CSV').'</td>
								
								
							</tr><tr>
								<td><input type="checkbox" name="creative_mobile_leader" '.($set->creative_mobile_leader ? 'checked="checked"' : '').' /> '.lang('Mobile Leaderboard').'</td>
								<td><input type="checkbox" name="deal_cpm" '.($set->deal_cpm ? 'checked="checked"' : '').' /> '.lang('CPM').'</td>
								<td><input type="checkbox" name="multi" '.($set->multi ? 'checked="checked"' : '').' /> '.lang('Multi Languages').' '.($set->multi ? '<input type="text" name="db[multi_languages]" value="'.$set->multi_languages.'" />' : '').'</td>
								<td width="25%">'.lang('Default System Language').'<input type="text" name="db[defaultLangOfSystem]" value="'.($set->defaultLangOfSystem) .'" /></td>
							</tr><tr>
								<td><input type="checkbox" name="creative_mobile_splash" '.($set->creative_mobile_splash ? 'checked="checked"' : '').' /> '.lang('Mobile Splash').'</td>
								<td><input type="checkbox" name="deal_cpc" '.($set->deal_cpc ? 'checked="checked"' : '').' /> '.lang('CPC').'</td>
								<td><input type="checkbox" name="isNetwork" '.($set->isNetwork ? 'checked="checked"' : '').' /> '.lang('Is Network').'</td>
								<td><input type="checkbox" name="blockAccessForManagerAndAdmins" '.($set->blockAccessForManagerAndAdmins ? 'checked="checked"' : '').' /> '.lang('Block Access For Manager And Admins').'</td></tr><tr>
								
							</tr><tr>
								<td><input type="checkbox" name="creative_email" '.($set->creative_email ? 'checked="checked"' : '').' /> '.lang('E-mail').'</td>
								<td><input type="checkbox" name="deal_tier" '.($set->deal_tier ? 'checked="checked"' : '').' /> '.lang('Tier Program').'</td>
								<td><input type="checkbox" name="multiMerchantsPerTrader" '.($set->multiMerchantsPerTrader ? 'checked="checked"' : '').' /> '.lang('Multi Merchants Per Trader').'<br>(' .lang("Networks can't have that option checked").')</td>		
								<td><input type="checkbox" name="isBasicVer" '.($set->isBasicVer ? 'checked="checked"' : '').' /> '.lang('Is Basic system?').'<br>(' .lang("many features will be blocked").')</td>		
							</tr><tr>
								<td><input type="checkbox" name="creative_html5" '.($set->creative_html5 ? 'checked="checked"' : '').' /> '.lang('Show HTML 5').'</td>
								<td><input type="checkbox" name="showPositionsRevShareDeal" '.($set->showPositionsRevShareDeal ? 'checked="checked"' : '').' /> '.lang('Show Positions Rev Share Deal').'</td>
								<td><input type="checkbox" name="showDocumentsModule" '.($set->showDocumentsModule ? 'checked="checked"' : '').' /> '.lang('Show Documents Module').'</td>		
								
							</tr><tr>
								<td></td>
								<td><input type="checkbox" name="deal_revshare" '.($set->deal_revshare ? 'checked="checked"' : '').' /> '.lang('Revenue Share Spread').'</td>
								<td><input type="checkbox" name="introducingBrokerInterface" '.($set->introducingBrokerInterface ? 'checked="checked"' : '').' /> '.lang('introducing Broker Interface').'</td>
								<td><input type="checkbox" name="showProductsPlace" '.($set->showProductsPlace ? 'checked="checked"' : '').' /> '.lang('Show Products Place').'</td>		
								
							</tr><tr>
								<td></td>
								<td></td>
								<td><input type="checkbox" name="ShowGraphOnDashBoards" '.($set->ShowGraphOnDashBoards ? 'checked="checked"' : '').' /> '.lang('Show Graph On DashBoards').'</td>
								<td><input type="checkbox" name="showAgreementsModule" '.($set->showAgreementsModule ? 'checked="checked"' : '').' /> '.lang('Show Agreements Module').'</td>
								
							</tr><tr>
								<td></td>
								<td></td>
								<td><input type="checkbox" name="showInvoiceModule" '.($set->showInvoiceModule ? 'checked="checked"' : '').' /> '.lang('Show Invoice Module').'</td>
								<td></td>
							</tr>
						</table>
						</div>-->
					
						<div id="tracking_options_visibility" data-tab="tracking_options_visibility" class="config_tabs">
						<div class="normalTableTitle" data-tab2="tracking_options_visibility">'.lang('Tracking Options Visibility').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
							<tr>
								<td width="25%"><input type="checkbox" name="qrcode" '.($set->qrcode ? 'checked="checked"' : '').' /> '.lang('QR Code').'</td>
								<td width="25%"><input type="checkbox" name="facebookshare" '.($set->facebookshare ? 'checked="checked"' : '').' /> '.lang('Facebook Share').'</td>
							</tr>
						</table>
						</div>
						</div>
					
						<div id="integration_configuration" data-tab="integration_configuration" class="config_tabs">
						<div class="normalTableTitle" data-tab2="integration_configuration">'.lang('Integration Configuration').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
						<tr>
								
								<td valign="top">'.lang('Base URL').':</td>
								<td><input type="text" id="sitebaseurl" name="db[sitebaseurl]" style="width:300px" value="'.$set->sitebaseurl.'"/></td>
								
							
							</tr>
													<tr>
								
								<td valign="top">'.lang('Cron URL').':</td>
								<td><input type="text" id="cronUrls" name="db[cronUrls]" style="width:300px" value="'.$set->cronUrls.'"/></td>
								
							
							</tr>
																				<tr>
								
								<td valign="top">'.lang('Cron Time Differents').':</td>
								<td><input type="text" id="cronRecordsTimeDif" name="db[cronRecordsTimeDif]" style="width:20px" value="'.$set->cronRecordsTimeDif.'"/></td>
								
							
							</tr>
							
						<tr>
									<td><input type="checkbox" name="AllowAffiliateDuplicationOnCampaignRelation" '.($set->AllowAffiliateDuplicationOnCampaignRelation ? 'checked="checked"' : '').' /> '.lang('Allow Affiliate Duplications On CampaignRelation Page').'</td>
									</tr>
									
							<!--tr>
									<td><input type="checkbox" name="showParamTwoOnReports" '.($set->showParamTwoOnReports ? 'checked="checked"' : '').' /> '.lang('Show Param2 On Reports').'</td>
									</tr-->
						<tr>
								
								<td valign="top">'.lang('Logo URL').':</td>
								<td><input type="text" id="logoPath" name="db[logoPath]" style="width:600px" value="'.$set->logoPath.'"/></td>
								
							
							</tr>
							<tr>
								
								<td valign="top">'.lang('Billing Logo URL').':</td>
								<td><input type="text" id="billingLogoPath" name="db[billingLogoPath]" style="width:600px" value="'.$set->billingLogoPath.'"/></td>
								
							
							</tr>
							<tr>
								
								<td valign="top">'.lang('Favicon URL').':</td>
								<td><input type="text" id="faviconPath" name="db[faviconPath]" style="width:600px" value="'.($set->faviconPath).'"/></td>
								
							
							</tr>

							<tr>
								
								<td valign="top">'.lang('Secondary Powered By Logo').':</td>
								<td><input type="text" id="secondaryPoweredByLogo" name="db[secondaryPoweredByLogo]" style="width:600px" value="'.$set->secondaryPoweredByLogo.'"/></td>
								
							
							</tr>
							<tr>
								
								<td valign="top">'.lang('Secondary Powered By Logo HREF URL').':</td>
								<td><input type="text" id="secondaryPoweredByLogoHrefUrl" name="db[secondaryPoweredByLogoHrefUrl]" style="width:600px" value="'.$set->secondaryPoweredByLogoHrefUrl.'"/></td>
								
							
							</tr>
							<tr>
								
								<td valign="top">'.lang("Brand's powered by text").':</td>
								<td><input type="text" id="brandsPoweredbyText" name="db[brandsPoweredbyText]" style="width:600px" value="'.$set->brandsPoweredbyText.'"/></td>
								
							
							</tr>
							<tr>
									<td><input type="checkbox" name="hideBrandsDescriptionfromAffiliateFooter" '.($set->hideBrandsDescriptionfromAffiliateFooter ? 'checked="checked"' : '').' /> '.lang('Hide brands description from affiliate footer').'</td>
							</tr>
							<tr>
								
								<td><input type="checkbox" name="hidePoweredByABLogo" '.($set->hidePoweredByABLogo ? 'checked="checked"' : '').' /> '.lang('Hide Powered By AB Logo From Login').'</td>
								
							
							</tr>
							
							
							<tr>
								
								<td valign="top">'.lang('Push leads URL for new IB registered in the system').':</td>
								<td><input type="text" id="IBpushLeadOnRegistrationUrl" name="db[IBpushLeadOnRegistrationUrl]" style="width:600px" value="'.($set->IBpushLeadOnRegistrationUrl).'"/></td>
								
							
							</tr>
							<tr>
							<td>'.lang('Params').':<br>
							'.lang('email') . ' -  {emails}<br>'.
							lang('first_name') . ' -  {first_name}<br>'.
							lang('last_name') . ' -  {last_name}<br>'.
							lang('phone') . ' -  {phone}<br>'.
							lang('country') . ' -  {country}<br>'.
							lang('ip') . ' -  {ip}<br>'.'
							</td></tr>
				
							
							
							
							
							
							
							
							
							<tr>
								
								<td>&nbsp;</td>
								
								
							</tr><!--tr>
								
								<td valign="top">'.lang('Display Affiliate ID on the landing url with the following parameter').':</td>
								<td><input type="text" id="exportAffiliateName" name="db[exportAffiliateName]" style="width:600px" value="'.$set->exportAffiliateName.'"/></td>
								<td valign="top">* '.lang('Leave empty to disable').':</td>
								
							
							</tr>
							<tr>
								
								<td>&nbsp;</td>
								
								
							</tr-->
								
								
							<tr>
								
								
								<td colspan=3 valign="top"><span style="padding:5px;">'.lang('API Access to creative list').':</span><br/>
								'.$apiFeed.'
								</td>
							</tr>
						</table>
						</div>
						</div>';

						}

					$set->content .= '
					<div id="other_settings" data-tab="other_settings" class="config_tabs">
						<div class="normalTableTitle" data-tab2="other_settings" >'.lang('Other Settings').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails">
							<tr>
							<td><input type="checkbox" name="hidePendingProcessHighAmountDeposit" '.($set->hidePendingProcessHighAmountDeposit ? 'checked="checked"' : '').'  value=1/> '.lang('Disable pending process of high amount of deposits').'</td>
							</tr>
							<tr>
								
								<td valign="top">'.lang('Pending deposits amount limit').':</td>
								<td><input type="text" id="pendingDepositsAmountLimit" name="db[pendingDepositsAmountLimit]" style="width:100px" value="'.$set->pendingDepositsAmountLimit.'"/>$</td>
								
							
							</tr>
							
							<tr>
							
								
								<td>'.lang('Number of failure attempts to login').'</td><td><input type="text" style="width: 30px" name="db[numberOfFailureLoginsAttempts]" value="'.$set->numberOfFailureLoginsAttempts.'"/></td>		
								
							
							</tr>
							
							<tr><td><br></td></tr>
							<tr>
								
								<td valign="top">'.lang('Analytics Code').':</td>
								<td><textarea name="db[analyticsCode]" cols="81" rows="10">'.$set->analyticsCode.'</textarea></td>
							
							</tr>
							
							<tr><td><br></td></tr>
							<tr>
							
								<td valign="top">'.lang('Affiliate Registration Image Pixel Code').':</td>
								<td><textarea name="db[affiliateRegistrationPixel]" cols="81" rows="10">'.$set->affiliateRegistrationPixel.'</textarea></td>
						
							</td>
							</tr>
							<tr><td><br></td></tr>
							
							<tr>
							
							
								<td valign="top">'.lang('Email Header Image HTML').':</td>
								<td><input text="text" id="emailHeaderImageURL" name="db[emailHeaderImageURL]" style="width:600px" value="'.$set->emailHeaderImageURL.'"/></td>
								
							</tr>
							<tr>
							
								<td valign="top">'.lang('Email Footer HTML').':</td>
								<td><input text="text" id="emailFooterImageURL" name="db[emailFooterImageURL]" style="width:600px" value="'.$set->emailFooterImageURL.'"/></td>
								
							</tr>
							<tr>
							
								<td valign="top">'.lang('Email Signature').' ,  ('.lang('put your html code for signature').') :</td>
								
							
								<td><textarea name="db[emailSignature]" cols="81" rows="10">'.$set->emailSignature.'</textarea></td>
							
							</tr>
						</table>
						</div>
						</div>
					
						
						<div id="billing_report" data-tab="billing_report" class="config_tabs">
						<div class="normalTableTitle" data-tab2="billing_report">'.lang('Billing Report') .' - ' .lang('Payments Display Configuration').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails">
							<thead>
							<tr >
							<td colspan=4 style="text-align:left">'.lang('Choose the background color for the following status').':<td>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td valign="middle" width="100px">'.lang('Paid').':</td>
								<td width="100px">
								<select name="select" style="background-color: white" onchange="this.style.backgroundColor = this.options[this.selectedIndex].style.backgroundColor;">
								' . $listofcolors . '
								 </select>
								</td>
								<td valign="middle" width="100px">'.lang('Pending').':</td>
								<td width="100px">
								<select name="select" style="background-color: white" onchange="this.style.backgroundColor = this.options[this.selectedIndex].style.backgroundColor;">
									'.$listofcolors . '
								 </select>
								</td>
							</tr>
							</tbody>
						</table>
							</div>
								</div>
						
			
							
							<div id="affiliate_registration_mandatory_fields" data-tab="affiliate_registration_mandatory_fields" class="config_tabs">
							<div class="normalTableTitle" data-tab2="affiliate_registration_mandatory_fields">'.lang('Affiliate Registration Mandatory Fields').'</div>
    				<div align="left" style="background: #EFEFEF;">
					
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails"">
					<thead>
					<tr>
							<td align="center"><b>'.lang('Field Name').'</b></td>
							<td align="center"><b>'.lang('Is Mandatory').'</b></td>
					</tr>
					<thead>
					<tbody>
					<tr>
							<td colspan="2" height="10"></td>
					</tr>
					
							';
							
		
							
						 $avapm=explode("|",$set->mustFields);
						 
						 
						 
						$set->content .= '
						
								' ;
								
								
								for ($i=0; $i<count($avapm); $i++) {
								$isActive = 1;
								$pyName = $avapm[$i];
								if (strpos('*' . $avapm[$i],'-')==1) {
										$isActive=0;
										$pyName = ltrim($avapm[$i],'-');
								}
								
								$set->content .= '												
								<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td align="center">'.ucwords($pyName).'</td>
								<td align="center"><input class="mnChk" type="checkbox" name="'.$pyName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /></td>
								
								</tr>';
					
							
							}
							$set->content.='
								<input type="hidden" id="combinedMN" name="combinedMN"/>
								<script type="text/javascript">
									
									$(document).ready(function(){
										
										$(".mnChk").change(function(e){
											var str = "";
											$( ".mnChk" ).each(function() {
												if(str!=""){
													str+="|";
												}
												str+=$(this).is(":checked") ? $(this).attr("name") : "-"+$(this).attr("name");
											});
											$("#combinedMN").val(str);
										});
									});
									
								</script>
								
					</tr>
					</tbody>
					</table>
					</div>
					</div>';
							
							
							
							
							$set->content.='
							
							<script type="text/javascript" src="../js/bootstrap.min.js"></script>
							<link rel="stylesheet" href="../css/multiple-select.css"/>
							<script src="../js/multiple-select.js"></script>
				
							<div id="registration_settings" data-tab="registration_settings" class="config_tabs">
						<div class="normalTableTitle" data-tab2="registration_settings">'.lang('Registration Settings') .'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails">
							<input type="hidden" name="db[hideCountriesOnRegistration]" id="selected_countries"/>
							<tr>
							<br>
								<!--<td>'.lang('Hide Countries On Registration').'<input type="text" style="width: 600px"  name="db[hideCountriesOnRegistration]" value="'.$set->hideCountriesOnRegistration.'"/></td>-->
								<td>'. lang('Hide Countries On Registration') . ' . <select name="hideCountries" id="hideCountries" style="width: 100px;" multiple="multiple">'.countriesList().'</select> '. ($selected_countries!=""?'('. lang('Selected Countries : ') . lang($selected_countries) . ')':'').'</td>
							</tr>
							
							<tr>
								<td><input type="checkbox" name="allowCapthaOnReg" '.($set->allowCapthaOnReg ? 'checked="checked"' : '').' /> '.lang('Activate CAPTCHA On Registration').'</td>
								</tr>
								<tr>
							<br>
								<td><input type="checkbox" name="allowCapthaOnReset" '.($set->allowCapthaOnReset ? 'checked="checked"' : '').' /> '.lang('Activate CAPTCHA On Reset Password').'</td>
								</tr>
								
								<tr>
								<td><input type="checkbox" name="autoRelateSubAffiliate" '.($set->autoRelateSubAffiliate ? 'checked="checked"' : '').' /> '.lang("Auto Relate New SubAffiliates To Affiliate's Parent Group").'</td>
								</tr><tr>
								<td><input type="checkbox" name="autoRelateNewAffiliateToAllMerchants" '.($set->autoRelateNewAffiliateToAllMerchants ? 'checked="checked"' : '').' /> '.lang("Auto Relate New Affiliate To All Merchants").'</td>
								</tr>
								<tr>
									
										<td><input type="checkbox" name="pending" '.($set->pending ? 'checked="checked"' : '').' /> '.lang('Approve manually new affiliates registration').'</td>
										
									</tr>
									<tr>
								<td><input type="checkbox" name="showGroupValuesOnAffReg" '.($set->showGroupValuesOnAffReg ? 'checked="checked"' : '').' /> '.lang("Show Groups Values on Affiliate Registration").' <select name="showGroupsLanguages">
								<option value="">-Select-</option>
								<option value="group_name" '. ($set->showGroupsLanguages =="group_name"? 'selected' : '')  .'>Group Name</option>
								<option value="language_name"'. ($set->showGroupsLanguages =="language_name"? 'selected' : '') .'>Language Name</option>
								<option value="display_lname"'. ($set->showGroupsLanguages =="display_lname"? 'selected' : '') .'>Display Language Name</option></select></td>
								
								</tr>
								
								
						</table>
							</div>
							</div>
								
						<div id="affiliate_display_configuration" data-tab="affiliate_display_configuration" class="config_tabs">
						<div class="normalTableTitle" data-tab2="affiliate_display_configuration">'.lang('Affiliate Display Configuration') .'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails">
							<tr>
							<br>
								<td><input type="checkbox" name="showMiminumDepositOnAffAccount" '.($set->showMiminumDepositOnAffAccount ? 'checked="checked"' : '').' /> '.lang('Show '. ptitle('Minimum Deposit').' On Affiliate Account').'</td>
								</tr>
								<tr>
								<td><input type="checkbox" name="hideInvoiceSectionOnAffiliateRegPage" '.($set->hideInvoiceSectionOnAffiliateRegPage ? 'checked="checked"' : '').' /> '.lang('Hide Invoice Section On Affiliate Reg Page').'</td>
								
								
								</tr>
								<tr>
								<td><input type="checkbox" name="hideCommissionOnTraderReportForRevDeal" '.($set->hideCommissionOnTraderReportForRevDeal ? 'checked="checked"' : '').' /> '.lang('Hide commission column on trader report for Revenue-Share deal').'</td>
								
								
								</tr>
								<tr>
								<td><input type="checkbox" name="hideMarketingSectionOnAffiliateRegPage" '.($set->hideMarketingSectionOnAffiliateRegPage ? 'checked="checked"' : '').' /> '.lang('Hide Marketing Section On Affiliate Reg Page').'</td>
								</tr><tr>
								<td><input type="checkbox" name="hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals" '.($set->hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals ? 'checked="checked"' : '').' /> '.lang("Hide drill down links on invoice for affiliates with non revenue share deals").'.</td>
								</tr>
								<tr>
								<td><input type="checkbox" name="show_credit_as_default_for_new_affiliates" '.($set->show_credit_as_default_for_new_affiliates ? 'checked="checked"' : '').' /> '.lang("Show credit as default for a new affiliates").'.</td>								
								</tr>
								<tr>
								<td><input type="checkbox" name="showDCPAonAffiliateComStruc" '.($set->showDCPAonAffiliateComStruc ? 'checked="checked"' : '').' /> '.lang("Show DCPA for Affiliates on commission structure").'.</td>								
								</tr>
								
								<tr>
								<td><input type="checkbox" name="showAllCreativesToAffiliate" '.($set->showAllCreativesToAffiliate ? 'checked="checked"' : '').' /> '.lang('Show All merchants creatives to affiliates').'</td>		
							</tr>
							</tr>
								<tr>
								<td><input type="checkbox" name="blockAffiliateLogin" '.($set->blockAffiliateLogin ? 'checked="checked"' : '').' /> '.lang('Temporary block affiliates login and registration').'</td>		
							</tr>
							<tr>
								<td>'.lang('Newsletter sign-up text on affiliate registration').'<input type="text" style="width: 600px" name="db[newsletterCheckboxCaption]" value="'.$set->newsletterCheckboxCaption.'"/></td>		
							</tr>
								<tr>
								<td>'.lang('Date of monthly payment').'<input type="text" style="width: 100px" name="db[dateOfMonthlyPayment]" value="'.$set->dateOfMonthlyPayment.'"/></td>		
							</tr>
							
								<tr>
								<td><input type="checkbox" name="db[affiliateNewsletterCheckboxValue]" '.($set->affiliateNewsletterCheckboxValue ? 'checked="checked"' : '').' /> '.lang('Select recieve newsletter checkbox as default on affiliate registration').'</td>		
							</tr>
							
								<tr>
								<td>'.lang("Default timeframe for affiliates").':&nbsp;
											<select name="db[defaultTimeFrameForAffiliate]" style="width: 150px;"><option value="">'.lang('Default').'
								</option>
								<option value="Today" '. ($set->defaultTimeFrameForAffiliate=="Today" ? ' Selected ' : '').'>'.lang('Today').'</option>'.lang('Today').'
								<option value="Yesterday"  '. ($set->defaultTimeFrameForAffiliate=="Yesterday" ? ' selected ' : '').'>'.lang('Yesterday').'</option>'.lang('Yesterday').'
								<option value="This Week"  '. ($set->defaultTimeFrameForAffiliate=="This Week" ? ' selected ' : '').'>'.lang('This Week').'</option>'.lang('This Week').'
								<option value="Last Week"  '. ($set->defaultTimeFrameForAffiliate=="Last Week" ? ' selected ' : '').'>'.lang('Last Week').'</option>'.lang('Last Week').'
								<option value="This Month"  '. ($set->defaultTimeFrameForAffiliate=="This Month" ? ' selected ' : '').'>'.lang('This Month').'</option>'.lang('This Month').'
								<option value="Last Month"  '. ($set->defaultTimeFrameForAffiliate=="Last Month" ? ' selected ' : '').'>'.lang('Last Month').'</option>'.lang('Last Month').'
								</select>
								</td>
								</tr>
								<tr>
								<td>'.lang("Default Qualify Commission For New Affiliates").':&nbsp;
								<select  id="empnt" name="db[def_qualify_type_for_affiliates]" style="width: 292px;">
											<option value="0"  '.($set->def_qualify_type_for_affiliates == 0 ? 'selected' : ''). '>'.lang('None').'</option>
											<option value="1" '.($set->def_qualify_type_for_affiliates == 1 ? 'selected' : ''). '>'.lang('Merchant Default').'</option>
								</tr>
								
								<tr>
								<td>'.lang("Default Profile Permission For New Affiliate").':&nbsp;
								<select  id="empnt" name="db[def_profilePermissionsForAffiliate]" style="width: 292px;">
											<option value="0"  '.($set->def_profilePermissionsForAffiliate == 0 ? 'selected' : ''). '>'.lang('Default View').'</option>
											<option value="-1" '.($set->def_profilePermissionsForAffiliate == -1 ? 'selected' : ''). '>'.lang('Automatic By Deal').'</option>
								</tr>
								
								
								
								
								<!--tr>
								<td><input type="checkbox" name="ShowEmailsOnTraderReportForAffiliate" '.($set->ShowEmailsOnTraderReportForAffiliate ? 'checked="checked"' : '').' /> '.lang('Show Emails On Trader Report For All Affiliates').'</td>
								</tr><tr>
								<td><input type="checkbox" name="hideNetRevenueForNonRevDeals" '.($set->hideNetRevenueForNonRevDeals ? 'checked="checked"' : '').' /> '.lang("Hide 'NetRevenue' for non revenue share deals").'.</td>
								</tr><tr>
								</tr>
								<tr>
								    <td><input type="checkbox" name="hideFTDamountForCPADeals" '.($set->hideFTDamountForCPADeals ? 'checked="checked"' : '').' /> '.lang("Hide 'FTD amount' for CPA deals").'.</td>
								</tr>
								<tr>
								    <td><input type="checkbox" name="hideTotalDepositForCPADeals" '.($set->hideTotalDepositForCPADeals ? 'checked="checked"' : '').' /> '.lang("Hide 'Total Deposit' for CPA deals").'.</td>
								</tr>
								<tr>
								    <td><input type="checkbox" name="hideDepositAmountForCPADeals" '.($set->hideDepositAmountForCPADeals ? 'checked="checked"' : '').' /> '.lang("Hide 'Deposit amount' for CPA deals").'.</td>
								</tr>
								<tr>
								    <td><input type="checkbox" name="hideBonusAmountForCPADeals" '.($set->hideBonusAmountForCPADeals ? 'checked="checked"' : '').' /> '.lang("Hide 'Bonus Amount' for CPA deals").'.</td>
								</tr>
								<tr>
								    <td><input type="checkbox" name="hideWithdrawalAmountForCPADeals" '.($set->hideWithdrawalAmountForCPADeals ? 'checked="checked"' : '').' /> '.lang("Hide 'Withdrawal Amount' & 'ChargeBack Amount'  for CPA deals").'.</td>
								</tr>
								<tr>
								<td><input type="checkbox" name="showAffiliateRiskForAffiliate" '.($set->showAffiliateRiskForAffiliate ? 'checked="checked"' : '').' /> '.lang("Show Affiliate Risk for affiliates").'.</td>								
								</tr>
								<tr>
								<td><input type="checkbox" name="showVolumeForAffiliate" '.($set->showVolumeForAffiliate ? 'checked="checked"' : '').' /> '.lang("Show Volume For Affiliates").'.</td>								
								</tr>
								<tr>
										
										<td><input type="checkbox" name="show_deposit" '.($set->show_deposit ? 'checked="checked"' : '').' /> '.lang('Show Deposits & Withdrawals by default to the affiliate').'</td>
									
									</tr><tr>
										
										<td><input type="checkbox" name="show_real_ftd" '.($set->show_real_ftd ? 'checked="checked"' : '').' /> '.lang('Show Real FTD by default to the affiliate').'</td>								
							</tr-->

								<tr>
							<td colspan="2" height="10"></td>
					</tr>
						
	</table>
	</div>
	</div>
				<div id="affiliate_manager_display_configuration" data-tab="affiliate_manager_display_configuration" class="config_tabs">
					<div class="normalTableTitle" data-tab2="affiliate_manager_display_configuration">'.lang('Affiliate Manager Display Configuration') .'</div>
						<div align="left" style="background: #EFEFEF;">
						<table  width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails">
							<tr>
							<br>
								<td><input type="checkbox" name="showCreditForAM" '.($set->showCreditForAM ? 'checked="checked"' : '').' /> '.lang('Show Credit amount for Affiliate Managers').'</td>
							</tr>
							<tr>
							<td colspan="2" height="10"></td>
					</tr>							
								<tr>
								<td><input type="checkbox" name="showDealTypeHistoryToAM" '.($set->showDealTypeHistoryToAM ? 'checked="checked"' : '').' /> '.lang('Show Deal Type History To Affiliate Manager').'</td>		
							</tr>

								<tr>
								<td><input type="checkbox" name="AllowDealChangesByManager" '.($set->AllowDealChangesByManager ? 'checked="checked"' : '').' /> '.lang('Allow Affiliate Manager To Change Deals').'</td>		
							</tr>
							
							
							<tr>
							<td colspan="2" height="10"></td>
					</tr>
					<tr>
								<td><input type="checkbox" name="AllowManagerEditrCreative" '.($set->AllowManagerEditrCreative ? 'checked="checked"' : '').' /> '.lang('Allow Affiliate Manager To Upload & Edit Creatives').'</td>		
							</tr>
							
							
							<tr>
							<td colspan="2" height="10"></td>
					</tr>
				</table>
				</div>
				</div>
				<div id="available_payments_method" data-tab="available_payments_method" class="config_tabs">
							<div class="normalTableTitle" data-tab2="available_payments_method">'.lang('Available Payments Method').'</div>
    				<div align="left" style="background: #EFEFEF;">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails">
					<thead>
					<tr>
							<td align="center"><b>'.lang('Payment Name').'</b></td>
							<td align="center"><b>'.lang('Show Payment Method').'</b></td>
					</tr>
					</thead>
					<tbody>
					<tr>
							<td colspan="2" height="10"></td>
					</tr>
					
							';
							
		
							
						 $avapm=explode("|",$set->availablePayments);
						 
						 
						 
						$set->content .= '
						
								' ;
								
								
								for ($i=0; $i<count($avapm); $i++) {
								$isActive = 1;
								$pyName = $avapm[$i];
								if (strpos('*' . $avapm[$i],'-')==1) {
										$isActive=0;
										$pyName = ltrim($avapm[$i],'-');
								}
								
								$set->content .= '												
								<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td align="center"><input class="pyChk" type="checkbox" name="'.$pyName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /></td>
								<td align="center">'.ucwords($pyName).'</td>
								
								</tr>';
					
							
							}
							$set->content.='
								<input type="hidden" id="combinedPY" name="combinedPY"/>
								<script type="text/javascript">
									
									$(document).ready(function(){
										
										$(".pyChk").change(function(e){
											var str = "";
											$( ".pyChk" ).each(function() {
												if(str!=""){
													str+="|";
												}
												str+=$(this).is(":checked") ? $(this).attr("name") : "-"+$(this).attr("name");
											});
											$("#combinedPY").val(str);
										});
									});
									
								</script>
							
							
							
							
							
							
							
							<tr>
								<td style="padding-top:30px;clear:both;" colspan="5" height="8">&nbsp;</td>
						
					</tr>
					</tbody>
					<thead>
					<tr>
							<td align="center"><b>'.lang('Currency Name').'</b></td>
							<td align="center"><b>'.lang('Show Currency To Affiliate').'</b></td>
					</tr>
					</thead>
					<tbody>
					<tr>
							<td colspan="2" height="10"></td>
					</tr>
					
							';
							
						 $avacr=explode("|",$set->availableCurrencies);
						 
						 
						 
						$set->content .= '
						
								' ;
								
								
								for ($i=0; $i<count($avacr); $i++) {
								$isActive = 1;
								$crName = $avacr[$i];
								if (strpos('*' . $avacr[$i],'-')==1) {
										$isActive=0;
										$crName = ltrim($avacr[$i],'-');
								}
								
								$set->content .= '												
								<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td align="center"><input class="crChk" type="checkbox" name="'.$crName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /></td>
								<td align="center">'.ucwords($crName).'</td>
								
								</tr>';
					
							
							}
							$set->content.='
								<input type="hidden" id="combinedCR" name="combinedCR"/>
								<script type="text/javascript">
									
									$(document).ready(function(){
										
										$(".crChk").change(function(e){
											var str = "";
											$( ".crChk" ).each(function() {
												if(str!=""){
													str+="|";
												}
												str+=$(this).is(":checked") ? $(this).attr("name") : "-"+$(this).attr("name");
											});
											$("#combinedCR").val(str);
										});
									});
									
								</script>
							</tbody>	
					</table>
					</div></div>
';


						$set->content.=	'
					<div id="pixel_triggers" data-tab="pixel_triggers" class="config_tabs">
	
					<div data-tab2="pixel_triggers" class="normalTableTitle">'.lang('Pixel Triggers').'</div>
    				<div align="left" style="background: #EFEFEF;">
					
					<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
					<tr>
								<td colspan="2" height="5"></td>
						
					</tr>
							
						<tr>
							<td align="center"><b></b></td>
					
							<td align="center"><b>'.lang('Trigger').'</b></td>
					</tr>
					<tr>
							<td colspan="2" height="10"></td>
					</tr>
					
							';
							
						 $avapo=explode("|",$set->combinedPixelOption);
						 
						$set->content .= '
						
								' ;
								
								
								for ($i=0; $i<count($avapo); $i++) {
								$isActive = 1;
								$crName = $avapo[$i];
								if (strpos('*' . $avapo[$i],'-')==1) {
										$isActive=0;
										$crName = ltrim($avapo[$i],'-');
								}
								
								if (empty($crName))
									continue;
								
								$set->content .= '												
								<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td align="center"><input class="pxChk" type="checkbox" name="'.$crName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /></td>
								<td align="center">'.ucwords($crName).'</td>
								
								</tr>';
					
							
							}
							$set->content.='
								<input type="hidden" id="combinedPO" name="combinedPO"/>
								<script type="text/javascript">
									
									$(document).ready(function(){
										
										$(".pxChk").change(function(e){
											var str = "";
											$( ".pxChk" ).each(function() {
												if(str!=""){
													str+="|";
												}
												str+=$(this).is(":checked") ? $(this).attr("name") : "-"+$(this).attr("name");
											});
											$("#combinedPO").val(str);
										});
									});
									
								</script>
								
								
					
					</table>
					</div></div>
';	

$set->content.='
	<div id="tracker_configuration" data-tab="tracker_configuration" class="config_tabs">
	<div data-tab2="tracker_configuration" class="normalTableTitle">'.lang('Tracker Configuration').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
							<tr>
								
								<td valign="top">'.lang('Force push ctag with the following parameters in additional to the default ctag parameter').':</td>
								<td><input type="text" id="forceParamsForTracker" name="db[forceParamsForTracker]" style="width:600px" value="'.$set->forceParamsForTracker.'"/></td>
								
							
							</tr>
							
							<tr>
								
								<td valign="top">'.lang('Display affiliate id on tracker url with the following field name').':</td>
								<td><input type="text" id="exportAffiliateIDonTrackerFieldName" name="db[exportAffiliateIDonTrackerFieldName]" style="width:600px" value="'.$set->exportAffiliateIDonTrackerFieldName.'"/><br/>
								<span style="font-size:9px" align="left">'.lang('Leave blank to deactivate this feature').'.</span>
								</td>
							
							</tr><tr><td></td></tr><tr>
							
							
								
								<td valign="top">'.lang('Export Creative Name With Parameter').':</td>
								<td><input type="text" id="exportCreativeNameWithParam" name="db[exportCreativeNameWithParam]" style="width:600px" value="'.$set->exportCreativeNameWithParam.'"/></td>
								</tr><tr>
								<td valign="top">'.lang('Export Language OF Creative With Parameter').':</td>
								<td><input type="checkbox" name="exportLangCreativeNameWithParam" '.($set->exportLangCreativeNameWithParam ? 'checked="checked"' : '').' /> '.lang('Export Creative Name Language With Param').'
								<br/><span style="font-size:9px" align="left">'.lang('Leave blank to deactivate this feature').'.</span>
								</td>
								
								
								</tr><tr><td></td></tr><tr>
							
							</tr>
							
							<tr>
								
								<td valign="top">'.lang('Display Profile Source on tracker url with the followinf field name').':</td>
								<td><input type="text" id="exportProfileNameToTrackerFieldName" name="db[exportProfileNameToTrackerFieldName]" style="width:600px" value="'.$set->exportProfileNameToTrackerFieldName.'"/>
								<br/><span style="font-size:9px" align="left">'.lang('Leave blank to deactivate this feature').'.</span>
								</td>
														</tr>
						</tr><tr><td></td></tr><tr>
						
							
						
							
								<td valign="top">'.lang('UTM tags for landing page').':</td>
								<td><input text="text" id="utmtags" name="db[utmtags]" style="width:500px" value="'.$set->utmtags.'"/><span><button id="putDefaultText">'.lang('Click for default').'</button></span></td>
								<script>
						$("#putDefaultText").click(function()	{
							$("#utmtags").val("&utm_source=affiliateBuddies&utm_campaign=affiliate&utm_medium=affiliate&utm_term={AffiliateID}&utm_content={BannerID}");
							return false;
						}); 
						</script>
						
						
							</tr>
							<tr>
								
								<td valign="top">'.lang('Force override coupon tracker than other trackers').':</td>
								<td>
								
								<input type="checkbox" id="overrideByCoupon" name="db[overrideByCoupon]"  value="'.$set->overrideByCoupon.'"  '.($set->overrideByCoupon ? 'checked="checked"' : '').'/>
								

								
								</td>
								
							
							</tr>
							<tr>
								<td>&nbsp;</td>
								
							
							</table>
							
							</div></div>';
						
						

		//if ($set->showDocumentsModule || $set->userInfo['id'] == "1") {	
		if ($set->showDocumentsModule || $set->userInfo['userType'] == "sys") {	
				
				$set->content .= '							
								
	
	<div id="documents"data-tab="documents" class="config_tabs">
	<div data-tab2="documents" class="normalTableTitle">'.lang('Documents').'</div>
	<div align="left" style="background: #EFEFEF;">
					<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
					<tr>
								<td colspan="2" height="5"></td>
						
					</tr>
								
								<tr>
										
										<td><input type="checkbox" name="showRequierdDocsOnAffiliateDash" '.($set->showRequierdDocsOnAffiliateDash ? 'checked="checked"' : '').' /> '.lang('Show Requierd Documents Popup On Affiliate Dashboard').'</td>		
								</tr>
								<tr>
										<td><input type="checkbox" name="AskDocTypePassport" '.($set->AskDocTypePassport ? 'checked="checked"' : '').' /> '.lang('Ask For Passport Document Verification From Affiliates').'</td>		
								</tr>
								<tr>
										<td><input type="checkbox" name="AskDocTypeAddress" '.($set->AskDocTypeAddress ? 'checked="checked"' : '').' /> '.lang('Ask For Address Document Verification From Affiliates').'</td>		
								</tr>
								<tr>
										<td><input type="checkbox" name="AskDocTypeCompany" '.($set->AskDocTypeCompany ? 'checked="checked"' : '').' /> '.lang('Ask For Company Document Verification From Affiliates').'</td>		
								</tr>
								
								<tr>
									<td>'.lang('Show the following sentence for affiliates with missing verification documents').'<input type="text" style="width: 600px" name="AskDocSentence" value="'.$set->AskDocSentence.'"/></td>		
								</tr>
					<tr>
							<td colspan="2" height="20"></td>
					</tr>
					</table>
					</div></div>
					
							';
		}
	
			$set->content .='<div data-tab="terms_and_conditions" id="terms_and_conditions" class="config_tabs">
	<div data-tab2="terms_and_conditions" class="normalTableTitle">'.lang('Terms and Conditions').'</div>
	<div align="left" style="background: #EFEFEF;">
					
					
					<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
					'.(!empty($set->terms_link)?'<tr style="padding:5px"><td colspan=3>'.lang('Current Terms & Conditions URL').': <a href= "'. $set->terms_link .'" target="_blank">'. $set->terms_link .'</a></td></tr>':'').'
					<tr>
								<td colspan="2" height="5"></td>
						
					</tr>
					<tr>
					<td colspan=3 align="left">'.lang('Terms & Conditions').':</td>
					</tr>
					<tr>
						<td>
						<div style="width:50px;float:left;"><input type="radio" name="terms" value="input_terms"/></div>
						<div style="float:left;"><input type="text" name="db[terms_link]" value="'.($set->terms_link ? $set->terms_link : 'http://').'" style="width: 250px;" /></div>
						<div style="clear:both;height:10px"></div>
						<div style="width:50px;float:left;"><input type="radio" name="terms" value="browse_terms"/></div>
						<div style="float:left;"><input type="file" name="terms_link_file" style="width: 220px;" />('.lang('HTML file only').')</div>
						<div style="clear:both;height:10px"></div>
						<div style="width:50px;float:left;"><input type="radio" name="terms" value="html_terms"/></div>
						<div style="float:left;"><textarea name="terms_link_html" id="contentMail" cols="80" rows="40">'.  (!empty($set->terms_link) ? @file_get_contents($set->terms_link) : "") .'</textarea></div>
						</td>
					</tr>
					<tr>
							<td colspan="2" height="20"></td>
					</tr>
					</table>
					
					</div></div>
					<link rel="stylesheet" type="text/css" href="/css/jquery.cleditor.css" />
						<script type="text/javascript" src="/js/jquery.cleditor.js"></script>
						<script type="text/javascript">
							$(document).ready(function () {
								$("#contentMail").cleditor({
									width:        800,
									height:       400
									});
								});
						</script>
							';
						
			
	
	
							
							
	
							$set->content.='
												
								
						
</div><br>

	

	
	</div>
						<!--<div align="center" style="    padding-top: 54px;    padding-bottom: 30px;"><input type="submit" value="'.lang('Save').'" /></div>-->
						<div class="btnsave" align="center" style="    bottom: 35px;
       right: 40%;
       "><input type="submit" value="'.lang('Save').'" /></div>
					</form>
		<br><br>
		
			<script>
						$("#hideCountries option[value=\'-\']").remove();
						$("#hideCountries").multipleSelect({
									width: 200,
									placeholder: "Select Country"
								});
						$("#hideCountries").change(function(){
							$("#selected_countries").val($(this).val());
						});
						
						var selects = "'. $set->hideCountriesOnRegistration .'";
						
						$("#hideCountries").multipleSelect("setSelects",[  '. $set->hideCountriesOnRegistration .' ]);
						/* $("form").submit(function() {
							this.merchant_id.disable = true;
							return true; 
						}); */
								</script>
					';
					///// pending:yellow|paid:lightgray
	//				$set->content.=$missingTrader;
	$set->content .= "</div>";
		theme();
		break;
	}

?>