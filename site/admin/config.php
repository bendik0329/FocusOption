<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);
if (!adminPermissionCheck('configuration')) _goto($lout);

include_once('config_options_tooltips.php');
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

switch ($act) {
	case "save":
	
		$randomFolder =mt_rand(10000000, 99999999);
		$folder = 'files/design/tmp/' . $randomFolder ."/";
		
		 if (!is_dir('files/design')) {
			 mkdir('files/design');
		 } 
		 
		 if (!is_dir('files/design/tmp')) {
			 mkdir('files/design/tmp');
		 }
		 
		 if (!is_dir($folder)) {
			 mkdir($folder);
		 }
		 
		if($_FILES['affiliateLoginImage']['name'] != ""){
			if(chkUpload('affiliateLoginImage')){
				$db['affiliateLoginImage'] = UploadFile('affiliateLoginImage', '5120000', 'jpg,jpeg,swf,bmp,gif,png', '', $folder);
                                    
					if (empty($db['affiliateLoginImage'])) {
						if (!empty($errors)) {
						_goto($set->SSLprefix.$set->basepage . '?errors=' . json_encode($errors));
					} 
				}
			}
		}
		if($_FILES['adminLoginImage']['name'] != ""){
			if(chkUpload('adminLoginImage')){
				$db['adminLoginImage'] = UploadFile('adminLoginImage', '5120000', 'jpg,jpeg,swf,bmp,gif,png', '', $folder);
                                    
					if (empty($db['adminLoginImage'])) {
						if (!empty($errors)) {
						_goto($set->SSLprefix.$set->basepage . '?errors=' . json_encode($errors));
					} 
				}
			}
		}
		 if($_FILES['logoPath']['name'] != ""){
			if(chkUpload('logoPath')){
					$db['logoPath'] = UploadFile('logoPath', '5120000', 'jpg,jpeg,swf,bmp,gif,png', '', $folder);
                                    
						if (empty($db['logoPath'])) {
							if (!empty($errors)) {
								_goto($set->SSLprefix.$set->basepage . '?errors=' . json_encode($errors));
							} 
					}
			}
		}
		if($_FILES['secondaryPoweredByLogo']['name'] != ""){
			if(chkUpload('secondaryPoweredByLogo')){
					$db['secondaryPoweredByLogo'] = UploadFile('secondaryPoweredByLogo', '5120000', 'jpg,jpeg,swf,bmp,gif,png', '', $folder);
                                    
						if (empty($db['secondaryPoweredByLogo'])) {
							if (!empty($errors)) {
								_goto($set->SSLprefix.$set->basepage . '?errors=' . json_encode($errors));
							} 
					}
			}
		}
		if($_FILES['billingLogoPath']['name'] != ""){
			if(chkUpload('billingLogoPath')){
					$db['billingLogoPath'] = UploadFile('billingLogoPath', '5120000', 'jpg,jpeg,swf,bmp,gif,png', '', $folder);
                                    
						if (empty($db['billingLogoPath'])) {
							if (!empty($errors)) {
								_goto($set->SSLprefix.$set->basepage . '?errors=' . json_encode($errors));
							} 
					}
			}
		}
		if($_FILES['faviconPath']['name'] != ""){
			if(chkUpload('faviconPath')){
					$db['faviconPath'] = UploadFile('faviconPath', '5120000', 'jpg,jpeg,swf,bmp,gif,png,ico', '', $folder);
                                    
						if (empty($db['faviconPath'])) {
							if (!empty($errors)) {
								_goto($set->SSLprefix.$set->basepage . '?errors=' . json_encode($errors));
							} 
					}
			}
		} 

		
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
		
		$base_url =  "http".$set->SSLswitch."://" . $_SERVER['HTTP_HOST'];
		$terms_path = "/files/";
		
		
		//handle terms

			switch($terms){
			case "browse_terms":
		
								$dir = $_SERVER["DOCUMENT_ROOT"];
								$target_dir = $dir. "/files/";
								$randomFolder =mt_rand(10000000, 99999999);
								$terms_path = "/files/tmp/" . $randomFolder . "/";
								$folder = $dir . '/files/tmp/' . $randomFolder ."/";
								 if (!is_dir($dir .'/files/tmp')) {
									 mkdir($dir .'/files/tmp');
								 }
								 if (!is_dir($folder)) {
									 mkdir($folder);
								 }
								//echo $target_dir;die;
								$target_file = $folder .  $_FILES["terms_link_file"]["name"];
							
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
			switch($merchant_terms){
			case "browse_terms_merchant":
							
								$dir = $_SERVER["DOCUMENT_ROOT"];
								$target_dir = $dir. "/files/";
								$randomFolder =mt_rand(10000000, 99999999);
								$terms_path = "/files/tmp/" . $randomFolder . "/";
								$folder = $dir . '/files/tmp/' . $randomFolder ."/";
								 if (!is_dir($dir .'/files/tmp')) {
									 mkdir($dir .'/files/tmp');
								 }
								 if (!is_dir($folder)) {
									 mkdir($folder);
								 }
								//echo $target_dir;die;
								
								$target_file = $folder .  $_FILES["merchants_terms_link_file"]["name"];
							
								$uploadOk = 1;
								$imageFileType = pathinfo($_FILES["merchants_terms_link_file"]["name"],PATHINFO_EXTENSION);
								
								if($imageFileType != "html" && $imageFileType != "HTML") {
									$error =  "Sorry, only HTML file is allowed.";
									
									_goto($set->uri . "?error=". urlencode($error) . "&tab=terms_and_conditions");
								}
								else{
									if (move_uploaded_file($_FILES["merchants_terms_link_file"]["tmp_name"], $target_file)) {
										$ty =  "The file ". basename( $_FILES["merchants_terms_link_file"]["name"]). " has been uploaded.";
										$db['merchants_terms_link'] = $base_url . $terms_path . $_FILES["merchants_terms_link_file"]["name"]; 
										//$db['merchants_terms_link'] = $base_url . $folder . $_FILES["merchants_terms_link_file"]["name"]; 
									} else {
										$error =  "Sorry, there was an error uploading your file.";
										_goto($set->uri . "?error=". urlencode($error) . "&tab=terms_and_conditions");
									}
								}
								break;
				
				case "html_terms_merchant":
							$dir = $_SERVER['DOCUMENT_ROOT'];
							$myfile = file_put_contents($dir. "/files/terms_merchants.html", $merchants_terms_link_html);
							$db['merchants_terms_link'] = $base_url . $terms_path . "terms_merchants.html"; 
							break;
				default:
							//default - input case
							break;
			}
		
		if ($combinedPY!=''){
			$db['availablePayments']=$combinedPY;
		}
		if ($combinedQL!=''){
			$db['availableQualifications']=$combinedQL;
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
		if ($ShowQualificationOnChart) $db['ShowQualificationOnChart'] = 1; else $db['ShowQualificationOnChart'] = 0;
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
			
			if ($deal_pnl) $db['deal_pnl'] = 1; else $db['deal_pnl'] = 0;
			if ($deal_cpl) $db['deal_cpl'] = 1; else $db['deal_cpl'] = 0;
			if ($blockAccessForManagerAndAdmins) $db['blockAccessForManagerAndAdmins'] = 1; else $db['blockAccessForManagerAndAdmins'] = 0;
			if ($deal_cpm) $db['deal_cpm'] = 1; else $db['deal_cpm'] = 0;
			if ($deal_revshare) $db['deal_revshare'] = 1; else $db['deal_revshare'] = 0;
			if ($deal_revshare_spread) $db['deal_revshare_spread'] = 1; else $db['deal_revshare_spread'] = 0;
			if ($deal_geoLocation) $db['deal_geoLocation'] = 1; else $db['deal_geoLocation'] = 0;
			if ($deal_cpi) $db['deal_cpi'] = 1; else $db['deal_cpi'] = 0;
			if ($isNetwork) $db['isNetwork'] = 1; else $db['isNetwork'] = 0;
			
			if ($showProductsPlace) $db['showProductsPlace'] = 1; else $db['showProductsPlace'] = 0;
			if ($showAdvertiserModule) $db['showAdvertiserModule'] = 1; else $db['showAdvertiserModule'] = 0;
			if ($ShowEmailsOnTraderReportForAdmin) $db['ShowEmailsOnTraderReportForAdmin'] = 1; else $db['ShowEmailsOnTraderReportForAdmin'] = 0;
			if ($ShowPhonesOnTraderReportForAdmin) $db['ShowPhonesOnTraderReportForAdmin'] = 1; else $db['ShowPhonesOnTraderReportForAdmin'] = 0;
		}
			if ($qualifiedCommissionOnCPAonly) $db['qualifiedCommissionOnCPAonly'] = 1; else $db['qualifiedCommissionOnCPAonly'] = 0;
			if ($AllowAffiliateDuplicationOnCampaignRelation) $db['AllowAffiliateDuplicationOnCampaignRelation'] = 1; else $db['AllowAffiliateDuplicationOnCampaignRelation'] = 0;
			if ($hideInvoiceSectionOnAffiliateRegPage) $db['hideInvoiceSectionOnAffiliateRegPage'] = 1; else $db['hideInvoiceSectionOnAffiliateRegPage'] = 0;
			if ($hideCommissionOnTraderReportForRevDeal) $db['hideCommissionOnTraderReportForRevDeal'] = 1; else $db['hideCommissionOnTraderReportForRevDeal'] = 0;
			if ($hideMarketingSectionOnAffiliateRegPage) $db['hideMarketingSectionOnAffiliateRegPage'] = 1; else $db['hideMarketingSectionOnAffiliateRegPage'] = 0;
			if ($introducingBrokerInterface) $db['introducingBrokerInterface'] = 1; else $db['introducingBrokerInterface'] = 0;
			if ($showMiminumDepositOnAffAccount) $db['showMiminumDepositOnAffAccount'] = 1; else $db['showMiminumDepositOnAffAccount'] = 0;
			if ($multiMerchantsPerTrader) $db['multiMerchantsPerTrader'] = 1; else $db['multiMerchantsPerTrader'] = 0;
			if ($ShowNextDepositsColumn) $db['ShowNextDepositsColumn'] = 1; else $db['ShowNextDepositsColumn'] = 0;
			if ($showMicroPaymentsOnReports) $db['showMicroPaymentsOnReports'] = 1; else $db['showMicroPaymentsOnReports'] = 0;
			if ($showCampaignOnTraderReport) $db['showCampaignOnTraderReport'] = 1; else $db['showCampaignOnTraderReport'] = 0;
			if ($ShowIMUserOnAffiliatesList) $db['ShowIMUserOnAffiliatesList'] = 1; else $db['ShowIMUserOnAffiliatesList'] = 0;
			if ($ShowOnlyFeaturedCreativesWhenGotSome) $db['ShowOnlyFeaturedCreativesWhenGotSome'] = 1; else $db['ShowOnlyFeaturedCreativesWhenGotSome'] = 0;
			if ($ShowAffiliateTypes) $db['ShowAffiliateTypes'] = 1; else $db['ShowAffiliateTypes'] = 0;
			if ($ShowEmailsOnTraderReportForAffiliate) $db['ShowEmailsOnTraderReportForAffiliate'] = 1; else $db['ShowEmailsOnTraderReportForAffiliate'] = 0;
			if ($allowCapthaOnReg) $db['allowCapthaOnReg'] = 1; else $db['allowCapthaOnReg'] = 0;
			if ($captureAffiliatesRegistration) $db['captureAffiliatesRegistration'] = 1; else $db['captureAffiliatesRegistration'] = 0;
			if ($allowCapthaOnReset) $db['allowCapthaOnReset'] = 1; else $db['allowCapthaOnReset'] = 0;
			if ($allowCapthaOnMerchantReset) $db['allowCapthaOnMerchantReset'] = 1; else $db['allowCapthaOnMerchantReset'] = 0;
			if ($allowCapthaOnMerchantReg) $db['allowCapthaOnMerchantReg'] = 1; else $db['allowCapthaOnMerchantReg'] = 0;
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
			if ($blockTrafficFromInactiveAffiliate) $db['blockTrafficFromInactiveAffiliate'] = 1; else $db['blockTrafficFromInactiveAffiliate'] = 0;
if ($writeFinalTrackingUrlToLog) $db['writeFinalTrackingUrlToLog'] = 1; else $db['writeFinalTrackingUrlToLog'] = 0;
			if ($showAllCreativesToAffiliate) $db['showAllCreativesToAffiliate'] = 1; else $db['showAllCreativesToAffiliate'] = 0;
			if ($AllowSecuredTrackingCode) $db['AllowSecuredTrackingCode'] = 1; else $db['AllowSecuredTrackingCode'] = 0;
			if ($isOffice365) $db['isOffice365'] = 1; else $db['isOffice365'] = 0;
			if ($showVolumeForAffiliate) $db['showVolumeForAffiliate'] = 1; else $db['showVolumeForAffiliate'] = 0;
			if ($showAffiliateRiskForAffiliate) $db['showAffiliateRiskForAffiliate'] = 1; else $db['showAffiliateRiskForAffiliate'] = 0;
			if ($showDCPAonAffiliateComStruc) $db['showDCPAonAffiliateComStruc'] = 1; else $db['showDCPAonAffiliateComStruc'] = 0;
			if ($affiliateNewsletterCheckboxValue) $db['affiliateNewsletterCheckboxValue'] = 1; else $db['affiliateNewsletterCheckboxValue'] = 0;
			if ($BlockLoginUntillEmailVerification) $db['BlockLoginUntillEmailVerification'] = 1; else $db['BlockLoginUntillEmailVerification'] = 0;
			if ($exportLangCreativeNameWithParam) $db['exportLangCreativeNameWithParam'] = 1; else $db['exportLangCreativeNameWithParam'] = 0;
			if ($showTitleOnLoginPage) $db['showTitleOnLoginPage'] = 1; else $db['showTitleOnLoginPage'] = 0;
			if ($showDealTypeHistoryToAM) $db['showDealTypeHistoryToAM'] = 1; else $db['showDealTypeHistoryToAM'] = 0;
			if ($AllowDealChangesByManager) $db['AllowDealChangesByManager'] = 1; else $db['AllowDealChangesByManager'] = 0;
			if ($showProductsPlaceToManager) $db['showProductsPlaceToManager'] = 1; else $db['showProductsPlaceToManager'] = 0;
			if ($AllowManagerEditrCreative) $db['AllowManagerEditrCreative'] = 1; else $db['AllowManagerEditrCreative'] = 0;
			if ($disableAutoCompleteOnLogin) $db['disableAutoCompleteOnLogin'] = 1; else $db['disableAutoCompleteOnLogin'] = 0;
			if ($activateLogs) $db['activateLogs'] = 1; else $db['activateLogs'] = 0;
			if ($hideSubAffiliation) $db['hideSubAffiliation'] = 1; else $db['hideSubAffiliation'] = 0;
			if ($overrideByCoupon) $db['overrideByCoupon'] = 1; else $db['overrideByCoupon'] = 0;
			if ($autoRelateNewAffiliateToAllMerchants) $db['autoRelateNewAffiliateToAllMerchants'] = 1; else $db['autoRelateNewAffiliateToAllMerchants'] = 0;
			if ($autoRelateNewAffiliateToAllProducts) $db['autoRelateNewAffiliateToAllProducts'] = 1; else $db['autoRelateNewAffiliateToAllProducts'] = 0;
			if ($showGroupValuesOnAffReg) $db['showGroupValuesOnAffReg'] = 1; else $db['showGroupValuesOnAffReg'] = 0;
			if($showGroupsLanguages!="") 
				$db['showGroupsLanguages'] = $showGroupsLanguages;
			$db['AskDocSentence'] =  $AskDocSentence;
			
			
			

			 // var_dump($db);
			 // die();
		
		if($set->activateLogs){
		 //activity logs
		$fields =array();
		$fields['ip'] = $set->userInfo['ip'];
		$fields['user_id'] = $set->userInfo['id'];
		$fields['theChange'] = json_encode($db);
		$fields['country'] = '';
		$fields['location'] = 'Configuration - Save';
		$fields['userType'] = $set->userInfo['level'];
		$fields['_file_'] = __FILE__;
		$fields['_function_'] = 'Save Configuration';
		
		$ch      = curl_init();					
		$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		curl_close($ch);
		}
		
		
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
		
		_goto($set->SSLprefix.$set->basepage . "?tab=" . $tab);
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
			<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.'admin/config.php" class="arrow-left">'.lang('Configuration').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		$selected_countries = "";
		
		$paidlistofcolors = '
	
									<option  value="" selected>'.lang('System Default').'</option>
									<option style="background-color: white" value="white" '. ($set->paidStatusBGColor=='white'?'selected':'') .'>'.lang('White').'</option>
									<option style="background-color: red" value="red" '. ($set->paidStatusBGColor=='red'?'selected':'') .'>'.lang('Red').'</option>
									 <option style="background-color: lightgreen" value="lightgreen" '. ($set->paidStatusBGColor=='lightgreen'?'selected':'') .'>'.lang('Light Green').'</option>
									 <option style="background-color: green" value="green" '. ($set->paidStatusBGColor=='green'?'selected':'') .'>'.lang('Green').'</option>
									 <option style="background-color: blue" value="blue" '. ($set->paidStatusBGColor=='blue'?'selected':'') .'>'.lang('Blue').'</option>
									 <option style="background-color: lightblue" value="lightblue" '. ($set->paidStatusBGColor=='lightblue'?'selected':'') .'>'.lang('Light Blue').'</option>
									  <option style="background-color: Yellow" value="yellow" '. ($set->paidStatusBGColor=='yellow'?'selected':'') .'>'.lang('Yellow').'</option>
									  <option style="background-color: lightyellow" value="lightyellow" '. ($set->paidStatusBGColor=='lightyellow'?'selected':'') .'>'.lang('Light Yellow').'</option>
									  <option style="background-color: Purple" value="purple" '. ($set->paidStatusBGColor=='purple'?'selected':'') .'>'.lang('Purple').'</option>
									  <option style="background-color: pink" value="pink" '. ($set->paidStatusBGColor=='pink'?'selected':'') .'>'.lang('Pink').'</option>
									  <option style="background-color: orange" value="orange" '. ($set->paidStatusBGColor=='orange'?'selected':'') .'>'.lang('Orange').'</option>
   									<option style="background-color: Gray" value="gray" '. ($set->paidStatusBGColor=='gray'?'selected':'') .'>'.lang('Gray').'</option>
   									<option style="background-color: LightGray" value="lightgray" '. ($set->paidStatusBGColor=='lightgray'?'selected':'') .'>'.lang('Light Gray').'</option>';
									
$pendinglistofcolors = '
	
									<option  value="" selected>'.lang('System Default').'</option>
									<option style="background-color: white" value="white" '. ($set->pendingStatusBGColor=='white'?'selected':'') .'>'.lang('White').'</option>
									<option style="background-color: red" value="red" '. ($set->pendingStatusBGColor=='red'?'selected':'') .'>'.lang('Red').'</option>
									 <option style="background-color: lightgreen" value="lightgreen" '. ($set->pendingStatusBGColor=='lightgreen'?'selected':'') .'>'.lang('Light Green').'</option>
									 <option style="background-color: green" value="green" '. ($set->pendingStatusBGColor=='green'?'selected':'') .'>'.lang('Green').'</option>
									 <option style="background-color: blue" value="blue" '. ($set->pendingStatusBGColor=='blue'?'selected':'') .'>'.lang('Blue').'</option>
									 <option style="background-color: lightblue" value="lightblue" '. ($set->pendingStatusBGColor=='lightblue'?'selected':'') .'>'.lang('Light Blue').'</option>
									  <option style="background-color: Yellow" value="yellow" '. ($set->pendingStatusBGColor=='yellow'?'selected':'') .'>'.lang('Yellow').'</option>
									  <option style="background-color: lightyellow" value="lightyellow" '. ($set->pendingStatusBGColor=='lightyellow'?'selected':'') .'>'.lang('Light Yellow').'</option>
									  <option style="background-color: Purple" value="purple" '. ($set->pendingStatusBGColor=='purple'?'selected':'') .'>'.lang('Purple').'</option>
									  <option style="background-color: pink" value="pink" '. ($set->pendingStatusBGColor=='pink'?'selected':'') .'>'.lang('Pink').'</option>
									  <option style="background-color: orange" value="orange" '. ($set->pendingStatusBGColor=='orange'?'selected':'') .'>'.lang('Orange').'</option>
   									<option style="background-color: Gray" value="gray" '. ($set->pendingStatusBGColor=='gray'?'selected':'') .'>'.lang('Gray').'</option>
   									<option style="background-color: LightGray" value="lightgray" '. ($set->pendingStatusBGColor=='lightgray'?'selected':'') .'>'.lang('Light Gray').'</option>';
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
		
		 /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  /*width: 60px;*/
			  width: 48px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 20px;
			 width: 43px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 12px;
			  width: 12px;
			  left: 3px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
		
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
			height:63vh;
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
		
		.question{
			background: url("../images/question.png");
			background-repeat:no-repeat;
			width:16px;
			height:16px;
			top:4px;
			left:5px;
		}
		.exclamation{
			background: url("../images/exclamation.png");
			background-repeat:no-repeat;
			width:16px;
			height:16px;
			top:4px;
			left:5px;
		}
	
		
		.etabs { margin: 0; padding: 0; }
		.tab { display: inline-block; zoom:1; *display:inline; background: #eee; border: solid 1px #999; border-bottom: none; -moz-border-radius: 4px 4px 0 0; -webkit-border-radius: 4px 4px 0 0; }
		.tab a { font-size: 14px; line-height: 2em; display: block; padding: 0 10px; outline: none; }
		.tab a:hover { text-decoration: underline; }
		.tab.active { background: #fff; padding-top: 6px; position: relative; top: 1px; border-color: #666; }
		.tab a.active { font-weight: bold; }
		.tab-container .panel-container { background: #fff; border: solid #666 1px; padding: 10px; -moz-border-radius: 0 4px 4px 4px; -webkit-border-radius: 0 4px 4px 4px; }
				  
		
		@media screen and (max-width: 290px) {
			.main{
			   margin-left:35%;
			}
		}
		</style>
		<script src="'.$set->SSLprefix.'js/easytabs/jquery.easytabs.min.js" type="text/javascript"></script>
		<script>
		$(document).ready(function(){
			show_hide_tabs("basic_information");
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
				
				if(open_tab=="apifeeds"){
					$(".btnsave").hide();
				}
				else{
					$(".btnsave").show();
				}
				
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
			$("#tab-container2").easytabs();
		});
		</script>
		<aside>
		<ul class="vertical">
		  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">'. lang('Configuration') .'</span><form style="display:inline-flex;float:right;">
		  <div class="filter">'.lang('Find') .': </div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div></li>
		  <li><a href="javascript:void(0)" data-tab="all">'. lang('All') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="basic_information" class="active">'. lang('Basic Information') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="display">'. lang('Display') .'</a></li>'
		  . ($set->userInfo['userType'] == "sys"?'
		  <li><a href="javascript:void(0)" data-tab="creatives">'. lang('Creatives') .'</a></li>':'').'
		  <li><a href="javascript:void(0)" data-tab="deal_type_options">'. lang('Deal Types') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="email_settings">'. lang('Email SMTP') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="integration">'. lang('Integration') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="payments">'. lang('Payments') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="registration_settings">'. lang('Registration') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="tracker">'. lang('Tracker') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="apifeeds">'. lang('API and Feed') .'</a></li>'
		  . ($set->userInfo['userType'] == "sys"?'
		  <li><a href="javascript:void(0)" data-tab="documents">'. lang('Documents') .'</a></li>':'').'
		  <li><a href="javascript:void(0)" data-tab="design">'. lang('Design') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="advanced">'. lang('Advanced') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="terms_and_conditions">'. lang('Terms And Conditions') .'</a></li>
		  <li><a href="javascript:void(0)" data-tab="billing">'. lang('Billing') .'</a></li>
		</ul>
		</aside>
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
		$set->show_real_ftd = mysql_result(function_mysql_query('SELECT showRealFtdToAff FROM settings LIMIT 0,1',__FILE__),0,0);
		$allLangs = "";
/* 				$langqq=mysql_query("SELECT * FROM languages WHERE valid='1' ORDER BY title ASC");
				$allLangOptions = "";
				while ($langww=mysql_fetch_assoc($langqq)) { */
				$langsArr = array("English"=>"ENG","Russian"=>"RUS","Dutch"=>"GER","French"=>"FRA","Italian"=>"ITA","Espaniol"=>"ESP","Arabic"=>"ARA","Chinese"=>"CHI","Portugese"=>"POT","Hebrew"=>"HEB","Japanese"=>"JAP");
				$ex = explode(',',$set->multi_languages);
				foreach ($ex as $langww){
				
					//for multilang
					$allLangOptions   .= '<option value="'.$langww.'">'.$langww.'</option>';
					if($set->defaultLangOfSystem == ""){
						$allLangs .= "<option value=". $langsArr[$langww] . ($set->defaultLangOfSystem== "ENG"?" selected":'') .">". $langww ."</option>";
					}
					else{
					$allLangs .= "<option value=". $langsArr[$langww] . ($set->defaultLangOfSystem== $langsArr[$langww] ?" selected":'') .">". $langww ."</option>";
					}
		}

		$set->content .= '<form id="frmMain"method="post" enctype="multipart/form-data">
					<input type="hidden" name="act" value="save" />
					
					<div id="basic_information" data-tab="basic_information" class="config_tabs">
					<div class="normalTableTitle" data-tab2="basic_information">'.lang('Basic Information').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
						<tr>
							<td width="150" align="left">'.lang('Software Name').':</td>
							<td><input type="text" name="db[webTitle]" value="'.$set->webTitle.'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'. $tooltips['webTitle'] .'</span></div></td>
						</tr>
						<tr>
							<td align="left">'.lang('Number of rows after search').':</td>
							<td><input type="text" name="db[rowsNumberAfterSearch]" value="'.$set->rowsNumberAfterSearch.'" style="width: 50px; text-align: center;" maxlength="4" /><div class="question tooltip"><span class="tooltiptext">'. $tooltips['rowsNumberAfterSearch'].'</span></div></td>
						</tr>
						<tr>
							<td align="left" width=30%>'.lang('Session timeout after total minutes of').':</td>
							<td><input type="text" name="db[login_session_duration]" value="'.$set->login_session_duration.'" style="width: 50px; text-align: center;" maxlength="4" /><div class="question tooltip"><span class="tooltiptext">'. $tooltips['login_session_duration'].'</span></div></td>
						</tr>
						<tr>
									<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="ShowAffiliateTypes" '.($set->ShowAffiliateTypes ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.$tooltips['ShowAffiliateTypes'].'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['ShowAffiliateTypes'].'</span></div></div></td>
						</tr>'
						. ($set->userInfo['userType'] == "sys"?'<tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="export" '.($set->export ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Export To CSV').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['export'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						</tr>
						<tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="multi" '.($set->multi ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div>'.lang('Multi Languages').' '.($set->multi ? '
						<input type="hidden" name="db[multi_languages]" id="multi_languages"/>
						<select name ="multipleLangs" id = "multipleLangs" multiple="multiple">
						'. $allLangOptions .'
						</select><div class="question tooltip"><span class="tooltiptext">'.  $tooltips['multi'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div>
						
						' : '').'</div></td>
						</tr>
						<tr>
						<td colspan=2>'.lang('Default System Interface Language').'
						<select name="db[defaultLangOfSystem]">
						'. $allLangs .'
						</select><div class="question tooltip"><span class="tooltiptext">'. $tooltips['defaultLangOfSystem'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'.  lang("Only available for Affiliate Buddies Admin.") .'</span></div>
						</td>
						</tr>
						<tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="isNetwork" '.($set->isNetwork ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Is Network').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['isNetwork']  .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						</tr><tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="blockAccessForManagerAndAdmins" '.($set->blockAccessForManagerAndAdmins ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Block Access For Manager And Admins').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['blockAccessForManagerAndAdmins'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						</tr><tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="multiMerchantsPerTrader" '.($set->multiMerchantsPerTrader ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Multi Merchants Per Trader').'<br>(' .lang("Networks can't have that option checked").')<div class="question tooltip"><span class="tooltiptext">'.$tooltips['multiMerchantsPerTrader']  .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>		
						</tr><tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="isBasicVer" '.($set->isBasicVer ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Is Basic system?').'<br>(' .lang("many features will be blocked").')<div class="question tooltip"><span class="tooltiptext">'. $tooltips['isBasicVer']  .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						</tr><tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="showDocumentsModule" '.($set->showDocumentsModule ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Documents Module').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['showDocumentsModule'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>		
						</tr><tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="introducingBrokerInterface" '.($set->introducingBrokerInterface ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('introducing Broker Interface').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['introducingBrokerInterface'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						</tr><tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="showProductsPlace" '.($set->showProductsPlace ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Products Place').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['showProductsPlace'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>		
						</tr><tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="showAdvertiserModule" '.($set->showAdvertiserModule ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Advertiser Module').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['showAdvertiserModule'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>		
						</tr>
						':'').'<tr>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="ShowGraphOnDashBoards" '.($set->ShowGraphOnDashBoards ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Graph On DashBoards').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['ShowGraphOnDashBoards'].'</span></div></td>
						</tr>
						<tr>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showAgreementsModule" '.($set->showAgreementsModule ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Agreements Module').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['showAgreementsModule'].'</span></div></td>
						</tr><tr>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showInvoiceModule" '.($set->showInvoiceModule ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Invoice Module').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['showInvoiceModule'].'</span></div></td>
						</tr>
						
						<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="blockAffiliateLogin" '.($set->blockAffiliateLogin ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Temporary block affiliates login and registration').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['blockAffiliateLogin'] .'</span></div></div></td>		
						</tr>
						<tr>
							<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="AllowDealChangesByManager" '.($set->AllowDealChangesByManager ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Allow Affiliate Manager To Change Deals').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['AllowDealChangesByManager']  .'</span></div></div></td>		
						</tr>

						<tr>
							<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="AllowManagerEditrCreative" '.($set->AllowManagerEditrCreative ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Allow Affiliate Manager To Upload & Edit Creatives').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['AllowManagerEditrCreative'] .'</span></div></div></td>		
						</tr>
						<tr>
							<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="disableAutoCompleteOnLogin" '.($set->disableAutoCompleteOnLogin ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Disable auto complete on login for any non affiliate login').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['disableAutoCompleteOnLogin'] .'</span></div></div></td>		
						</tr>
						<tr>
							<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="activateLogs" '.($set->activateLogs ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Activate Logs').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['activateLogs'] .'</span></div></div></td>		
						</tr>
						<tr>
							<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="hideSubAffiliation" '.($set->hideSubAffiliation ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Hide Sub Affiliation').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['hideSubAffiliation'] .'</span></div></div></td>		
						</tr>
						</tbody>
						</table>
						</div>
					</div>
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
					<div id="billing" data-tab="billing" class="config_tabs">
						<div class="normalTableTitle" data-tab2="billing">'.lang('Billing').'</div>
						<div align="left" style="background: #EFEFEF;">
							<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails">
									<thead>
									<tr >
									<td colspan=4 style="text-align:left">'.lang('Choose the background color for the following status').':<td>
									</tr>
									</thead>
									<tbody>
									<tr>
										<td valign="middle" width="100px">'.lang('Paid').':<div class="question tooltip"><span class="tooltiptext">'. $tooltips['paid']  .'</span></div></td>
										<td width="100px">
										<select name="db[paidStatusBGColor]" style="background-color: white" onchange="this.style.backgroundColor = this.options[this.selectedIndex].style.backgroundColor;">
										' . $paidlistofcolors . '
										 </select>
										</td>
										<td valign="middle" width="100px">'.lang('Pending').':<div class="question tooltip"><span class="tooltiptext">'. $tooltips['pending']  .'</span></div></td>
										<td width="100px">
										<select name="db[pendingStatusBGColor]" style="background-color: white" onchange="this.style.backgroundColor = this.options[this.selectedIndex].style.backgroundColor;">
											'.$pendinglistofcolors . '
										 </select>
										</td>
									</tr>
									<tr>
									<td colspan=4>
										'. lang('System Company Details') .':<div class="question tooltip"><span class="tooltiptext">'. $tooltips['companyDetails']  .'</span></div><br/><br/>
										<textarea name="db[systemCompanyDetails]" id="companyDetails" cols="80" rows="40">'.  (!empty($set->systemCompanyDetails) ? $set->systemCompanyDetails : "") .'</textarea>
										
									</td>
									</tr>
									
									
									</tbody>
								</table>
								</div>
					</div>
					
					<div id="display" data-tab="display" class="config_tabs">
					<div class="normalTableTitle" data-tab2="display">'.lang('Admin & Manager').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
							<tr>
								<td colspan=2>'.lang("Select Currency").': <select name="db[currency]">
								<option value="&#8362;" '. ($set->currency=='&#8362;'?' selected':'') .'>ILS</option>
								<option value="£" '. ($set->currency=='£'?' selected':'') .'>GBP</option>
								<option value="€" '. ($set->currency=='€'?' selected':'') .'>EUR</option>
								<option value="$" '. (!isset($set->currency)?'selected':($set->currency=='$'?' selected':'')) .'>USD</option>
								</select><div class="question tooltip"><span class="tooltiptext">'. $tooltips['currency'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>
							</tr>
							<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="ShowIMUserOnAffiliatesList" '.($set->ShowIMUserOnAffiliatesList ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'. lang('Show IMUser On Affiliates List And Report From Admin') .'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['ShowIMUserOnAffiliatesList'].'</span></div></div></td>
							</tr>
							
								<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="ShowNextDepositsColumn" '.($set->ShowNextDepositsColumn ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'. lang('Show Next Deposits Column') .'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['ShowNextDepositsColumn'].'</span></div></div></td>
							</tr>
							<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="showCampaignOnTraderReport" '.($set->showCampaignOnTraderReport ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'. lang('Show Campaign On Trader Report') .'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showCampaignOnTraderReport'].'</span></div></div></td>
							</tr>
							<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="showMicroPaymentsOnReports" '.($set->showMicroPaymentsOnReports ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'. lang('Show Micro Payments On Reports') .'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showMicroPaymentsOnReports'].'</span></div>
								<input type="text" id="showMicroPaymentsOnReportsRate" name="db[showMicroPaymentsOnReportsRate]" style="width:100px" value="'.$set->showMicroPaymentsOnReportsRate.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['showMicroPaymentsOnReportsRate'] .'</span></div></div>
								</td>
							</tr>
							'.($set->userInfo['userType'] == "sys" ?
							'<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="ShowEmailsOnTraderReportForAdmin" '.($set->ShowEmailsOnTraderReportForAdmin ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Emails On Trader Report For Admin').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['ShowEmailsOnTraderReportForAdmin'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
							</tr><tr>
							
							
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="ShowPhonesOnTraderReportForAdmin" '.($set->ShowPhonesOnTraderReportForAdmin ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Phone Number On Trader Report For Admin').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['ShowPhonesOnTraderReportForAdmin'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
							</tr>' : '' ).'
							<tr>
								<td valign="middle">'.lang("Brand's powered by text").':</td>
								<td><input type="text" id="brandsPoweredbyText" name="db[brandsPoweredbyText]" style="width:500px" value="'.$set->brandsPoweredbyText.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['brandsPoweredbyText'] .'</span></div></td>
							</tr>
							<tr>
								<td valign="middle">'.lang("Affiliate's Static Data for").':</td>
								<td><select name="db[affiliateStaticReportMonths]" style="width: 150px;">
								<option value="1" '. ($set->affiliateStaticReportMonths=="1" ? ' selected ' : '').'>1 '.lang('month').'</option>
								<option value="2"  '. ($set->affiliateStaticReportMonths=="2" ? ' selected ' : 'selected').'>2 '.lang('months').'</option>
								<option value="3"  '. ($set->affiliateStaticReportMonths=="3" ? ' selected ' : '').'>3 '.lang('months').'</option>
								</select><div class="question tooltip"><span class="tooltiptext">'.$tooltips['affiliateStaticDataFor'] .'</span></div></td>
							</tr>
							<tr>
									<td colspan=2>
										
									</td>
							</tr>
						</tbody>
						</table>
					</div>
					<div class="normalTableTitle" data-tab2="design">'.lang('Affiliate').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showMiminumDepositOnAffAccount" '.($set->showMiminumDepositOnAffAccount ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show '. ptitle('Minimum Deposit').' On Affiliate Account').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showMiminumDepositOnAffAccount']  .'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="hideInvoiceSectionOnAffiliateRegPage" '.($set->hideInvoiceSectionOnAffiliateRegPage ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Hide Invoice Section On Affiliate Reg Page').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['hideInvoiceSectionOnAffiliateRegPage'] .'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals" '.($set->hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Hide drill down links on invoice for affiliates with non revenue share deals").'.<div class="question tooltip"><span class="tooltiptext">'.$tooltips['hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals'] .'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="show_credit_as_default_for_new_affiliates" '.($set->show_credit_as_default_for_new_affiliates ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Show credit as default for a new affiliates").'.<div class="question tooltip"><span class="tooltiptext">'. $tooltips['show_credit_as_default_for_new_affiliates'] .'</span></div></div></td>								
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showDeskNameOnAffiliateDashboard" '.($set->showDeskNameOnAffiliateDashboard ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Show Desk Name On Affiliate Dashboard").'.<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showDeskNameOnAffiliateDashboard'] .'</span></div></div></td>								
							</tr>
							<tr>				
							<td><div style="float:left;"><label class="switch"><input type="checkbox" name="ShowQualificationOnChart" '.($set->ShowQualificationOnChart ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Show Qualification On Chart Instead Of FTDs").'.<div class="question tooltip"><span class="tooltiptext">'. $tooltips['ShowQualificationOnChart'] .'</span></div></div></td>								
							</tr>
							<tr>
								<td>'.lang('Show Qualification On Chart Since Date').': <input type="text" style="width: 100px" name="db[ShowQualificationOnChartSince]" value="'.$set->ShowQualificationOnChartSince.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['ShowQualificationOnChartSince']  .'</span></div></td>		
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showDCPAonAffiliateComStruc" '.($set->showDCPAonAffiliateComStruc ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Show DCPA for Affiliates on commission structure").'.<div class="question tooltip"><span class="tooltiptext">'.$tooltips['showDCPAonAffiliateComStruc'] .'</span></div></div></td>								
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showAllCreativesToAffiliate" '.($set->showAllCreativesToAffiliate ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show All merchants creatives to affiliates').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showAllCreativesToAffiliate'].'</span></div></div></td>		
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="hideMarketingSectionOnAffiliateRegPage" '.($set->hideMarketingSectionOnAffiliateRegPage ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Hide Marketing Section On Affiliate Reg Page').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['hideMarketingSectionOnAffiliateRegPage'].'</span></div></div></td>	
							</tr>
							<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="displayLastMessageFieldsOnReports" '.($set->displayLastMessageFieldsOnReports ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Last Message Fields On Reports For Admin').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['displayLastMessageFieldsOnReports'] .'</span></div></div></td>
							</tr>
							<tr>
									<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="hideBrandsDescriptionfromAffiliateFooter" '.($set->hideBrandsDescriptionfromAffiliateFooter ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Hide brands description from affiliate footer').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['hideBrandsDescriptionfromAffiliateFooter']  .'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="hideCommissionOnTraderReportForRevDeal" '.($set->hideCommissionOnTraderReportForRevDeal ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Hide commission column on trader report for Revenue-Share deal').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['hideCommissionOnTraderReportForRevDeal']  .'</span></div></div></td>
							</tr>
							<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="ShowOnlyFeaturedCreativesWhenGotSome" '.($set->ShowOnlyFeaturedCreativesWhenGotSome ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'. lang('Show Only Featured Creatives When Got Some') .'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['ShowOnlyFeaturedCreativesWhenGotSome'].'</span></div></div></td>
							</tr>
							<tr>
								<td>'.lang('Date of monthly payment').': <input type="text" style="width: 100px" name="db[dateOfMonthlyPayment]" value="'.$set->dateOfMonthlyPayment.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['dateOfMonthlyPayment']  .'</span></div></td>		
							</tr>
							
							<tr>
								<td>'.lang("Default timeframe for affiliates dashboard").':&nbsp;
											<select name="db[defaultTimeFrameForAffiliate]" style="width: 150px;"><option value="">'.lang('Default').'
								</option>
								<option value="Today" '. ($set->defaultTimeFrameForAffiliate=="Today" ? ' Selected ' : '').'>'.lang('Today').'</option>'.lang('Today').'
								<option value="Yesterday"  '. ($set->defaultTimeFrameForAffiliate=="Yesterday" ? ' selected ' : '').'>'.lang('Yesterday').'</option>'.lang('Yesterday').'
								<option value="This Week"  '. ($set->defaultTimeFrameForAffiliate=="This Week" ? ' selected ' : '').'>'.lang('This Week').'</option>'.lang('This Week').'
								<option value="Last Week"  '. ($set->defaultTimeFrameForAffiliate=="Last Week" ? ' selected ' : '').'>'.lang('Last Week').'</option>'.lang('Last Week').'
								<option value="This Month"  '. ($set->defaultTimeFrameForAffiliate=="This Month" ? ' selected ' : '').'>'.lang('This Month').'</option>'.lang('This Month').'
								<option value="Last Month"  '. ($set->defaultTimeFrameForAffiliate=="Last Month" ? ' selected ' : '').'>'.lang('Last Month').'</option>'.lang('Last Month').'
								</select><div class="question tooltip"><span class="tooltiptext">'. $tooltips['defaultTimeFrameForAffiliate'] .'</span></div>
								</td>
							</tr>
							<tr>
								<td>'.lang("Default timeframe for affiliates reports").':&nbsp;
											<select name="db[defaultTimeFrameForAffiliateReports]" style="width: 150px;"><option value="">'.lang('Default').'
								</option>
								<option value="Today" '. ($set->defaultTimeFrameForAffiliateReports=="Today" ? ' Selected ' : '').'>'.lang('Today').'</option>'.lang('Today').'
								<option value="Yesterday"  '. ($set->defaultTimeFrameForAffiliateReports=="Yesterday" ? ' selected ' : '').'>'.lang('Yesterday').'</option>'.lang('Yesterday').'
								<option value="This Week"  '. ($set->defaultTimeFrameForAffiliateReports=="This Week" ? ' selected ' : '').'>'.lang('This Week').'</option>'.lang('This Week').'
								<option value="Last Week"  '. ($set->defaultTimeFrameForAffiliateReports=="Last Week" ? ' selected ' : '').'>'.lang('Last Week').'</option>'.lang('Last Week').'
								<option value="This Month"  '. ($set->defaultTimeFrameForAffiliateReports=="This Month" ? ' selected ' : '').'>'.lang('This Month').'</option>'.lang('This Month').'
								<option value="Last Month"  '. ($set->defaultTimeFrameForAffiliateReports=="Last Month" ? ' selected ' : '').'>'.lang('Last Month').'</option>'.lang('Last Month').'
								</select><div class="question tooltip"><span class="tooltiptext">'. $tooltips['defaultTimeFrameForAffiliateReports'] .'</span></div>
								</td>
							</tr>
						</tbody>
						</table>
					</div>
					<div class="normalTableTitle" data-tab2="design">'.lang('Manager').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
							<tbody>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showCreditForAM" '.($set->showCreditForAM ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Credit amount for Affiliate Managers').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showCreditForAM'] .'</span></div></div></td>
							</tr>
							<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="showDealTypeHistoryToAM" '.($set->showDealTypeHistoryToAM ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Deal Type History To Affiliate Manager').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showDealTypeHistoryToAM'] .'</span></div></div></td>		
							</tr>
													<tr>
						<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="showProductsPlaceToManager" '.($set->showProductsPlaceToManager ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Products Place To Manager').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['showProductsPlaceToManager']  .'</span></div></div></td>		
						</tr>
							</tbody>
						</table>
					</div>
					</div>
					'. ($set->userInfo['userType'] == "sys"?'
					<div id="creatives" data-tab="creatives" class="config_tabs">
					<div class="normalTableTitle" data-tab2="creatives">'.lang('Creatives').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
							<tr>
									<td width="25%"><div style="float:left;"><label class="switch"><input type="checkbox" name="creative_iframe" '.($set->creative_iframe ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('IFrame (Widget)').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['creative_iframe'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
									<td><div style="float:left;"><label class="switch"><input type="checkbox" name="creative_mobile_leader" '.($set->creative_mobile_leader ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Mobile Leaderboard').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['creative_mobile_leader'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
									<td><div style="float:left;"><label class="switch"><input type="checkbox" name="creative_mobile_splash" '.($set->creative_mobile_splash ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Mobile Splash').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['creative_mobile_splash'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
									<td><div style="float:left;"><label class="switch"><input type="checkbox" name="creative_email" '.($set->creative_email ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('E-mail').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['creative_email'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
									<td><div style="float:left;"><label class="switch"><input type="checkbox" name="creative_html5" '.($set->creative_html5 ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show HTML 5').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['creative_html5'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
							</tr>
							</tbody>
							</table>
					</div>
					</div>':'').'
					<div id ="deal_type_options" data-tab="deal_type_options" class="config_tabs">
						<div class="normalTableTitle" data-tab2="deal_type_options">'.lang('Deal Types').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						'. ($set->userInfo['userType'] == "sys"?'<tr>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_cpl" '.($set->deal_cpl ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('CPL').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['deal_cpl'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_pnl" '.($set->deal_pnl ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('PNL').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['deal_pnl'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
					    <td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_cpm" '.($set->deal_cpm ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('CPM').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['deal_cpm']  .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_cpc" '.($set->deal_cpc ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('CPC').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['deal_cpc'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						</tr>
						<tr>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_tier" '.($set->deal_tier ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Tier Program').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['deal_tier'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showPositionsRevShareDeal" '.($set->showPositionsRevShareDeal ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Positions Rev Share Deal').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['showPositionsRevShareDeal'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_revshare" '.($set->deal_revshare ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Revenue Share').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['deal_revshare']  .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_revshare_spread" '.($set->deal_revshare_spread ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Revenue Share Spread').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['deal_revshare_spread'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						</tr>
						<tr>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_cpi" '.($set->deal_cpi ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('CPI').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['deal_cpi'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						<td><div style="float:left;"><label class="switch"><input type="checkbox" name="deal_geoLocation" '.($set->deal_geoLocation ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Geo Location Deals').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['deal_geoLocation'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
						</tr>
						':'').'
						<tr>
							<td align="left">'.lang('Sub Affiliate Commission').':</td>
							<td><input type="text" name="db[sub_com]" value="'.$set->sub_com.'" style="width: 100px; text-align: center;" maxlength="3" />%<div class="question tooltip"><span class="tooltiptext">'. $tooltips['sub_com'] .'</span></div></td>
						</tr>
						<tr>
							<td align="left">'.lang('Sub Affiliate Commission Level').':</td>
							<td><input type="text" name="db[sub_com_level]" value="'.$set->sub_com_level.'" style="width: 100px; text-align: center;" maxlength="3" /><div class="question tooltip"><span class="tooltiptext">'. $tooltips['sub_com_level'] .'</span></div></td>
						</tr>
						<tr>
							<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="qualifiedCommissionOnCPAonly" '.($set->qualifiedCommissionOnCPAonly ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Set The Qualified Commission For CPA Only').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['qualifiedCommissionOnCPAonly'] .'</span></div></div></td>
						</tr>
						<tr>
							<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="hasContinuousyCommissionType" '.($set->hasContinuousyCommissionType ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Has Continuousy Commission Type').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['hasContinuousyCommissionType'] .'</span></div></div></td>
						</tr>
						</table>
						</div>
						
						
						
					<div class="normalTableTitle" >'.lang('Qualification Methods').'</div>
					<div align="left" style="background: #EFEFEF;">
					<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails">
					<thead>
					<tr>
							<td align="center"><b>'.lang('Qualification Method').'</b></td>
							<td align="center"><b>'.lang('Is Active').'</b></td>						
					</tr>
					</thead>
					<tbody>
					<tr>
							<td colspan="2" height="10"></td>
					</tr>
					
							';
							
		
							
						 $avapm=explode("|",$set->availableQualifications);
						 
						 
						 
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
								<td align="center"><div><label class="switch"><input class="qlChk" type="checkbox" name="'.$pyName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /><div class="slider round"></div></label></div></td>
								<td align="center">'.ucwords(trim(splitStringByCapitalLetters($pyName))).'<div class="question tooltip"><span class="tooltiptext availableQualifications">'.$tooltips['availableQualifications'] .'</span></div></td>
								</tr>';
					
							
							}
							
							
							
							
							$set->content.='
					</table>
								<input type="hidden" id="combinedQL" name="combinedQL"/>
								<script type="text/javascript">
							
									
									$(document).ready(function(){
										
										$(".qlChk").change(function(e){
											var str = "";
											$( ".qlChk" ).each(function() {
												if(str!=""){
													str+="|";
												}
												str+=$(this).is(":checked") ? $(this).attr("name") : "-"+$(this).attr("name");
											});
											$("#combinedQL").val(str);
										});
									});
									
								</script>
					</div>
					
						
						
						
						</div>
					
					
					<div id="email_settings" data-tab="email_settings" class="config_tabs">
					<div class="normalTableTitle" data-tab2="email_settings">'.lang('Email SMTP').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
									<tr>
										<td align="left">'.lang('From Name').':</td>
										<td><input type="text" name="db[fromName]" value="'.$set->fromName.'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'. $tooltips['fromName'] .'</span></div></td>
									</tr><tr>
										<td align="left">'.lang('From E-mail').':</td>
										<td><input type="text" name="db[webMail]" value="'.$set->webMail.'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'. $tooltips['webMail'] .'</span></div></td>
									</tr><tr>
						<td align="left">'.lang('Reply To').':</td>
										<td><input type="text" name="db[mail_replyTo]" value="'.$set->mail_replyTo.'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'. $tooltips['mail_replyTo']  .'</span></div></td>
									</tr><tr>
						
								<td width="150" align="left">'.lang('Mail Server').':</td>
								<td><input type="text" name="db[mail_server]" value="'.$set->mail_server.'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'. $tooltips['mail_server']  .'</span></div></td>
							</tr><tr>
								<td align="left">'.lang('Mail Username').':</td>
								<td><input type="text" name="db[mail_username]" value="'.$set->mail_username.'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'.$tooltips['mail_username'].'</span></div></td>
							</tr><tr>
								<td align="left">'.lang('Mail Password').':</td>
								<td><input type="password" name="db[mail_password]" value="'.$set->mail_password.'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'.$tooltips['mail_password'] .'</span></div></td>
</tr><tr>
								<td align="left">'.lang('Mail Port').':</td>
								<td><input type="text" name="db[mail_Port]" value="'.$set->mail_Port.'" style="width: 50px;" /><div class="question tooltip"><span class="tooltiptext">'.$tooltips['mail_Port'] .'</span></div></td>
							</tr><tr>
								<td align="left" colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="SMTPAuth" '.($set->SMTPAuth ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Requierd Authentication').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['SMTPAuth']  .'</span></div></div></td>
							</tr><tr>
								<td align="left">'.lang('Type of encrypted connection').':</td>
								<td align="left">
								<select name="db[SMTPSecure]" style="width: 150px;" >
									<option value="">'.lang('None').'</option>
									<option value="SSL" '.(strtolower($set->SMTPSecure)=="ssl" ? ' selected' : '').'>'.lang('SSL').'</option>'.lang('SSL').'	
									<option value="TLS" '.(strtolower($set->SMTPSecure)=="tls" ? ' selected' : '').'>'.lang('TLS').'</option>'.lang('TLS').'
									</select><div class="question tooltip"><span class="tooltiptext">'.$tooltips['SMTPSecure']  .'</span></div></td>
							</tr>
							<tr>
						
							<td colspan =2><div style="float:left;"><label class="switch"><input type="checkbox" name="isOffice365" '.($set->isOffice365 ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Is Local SMTP Server?').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['isOffice365'] .'</span></div></div></td></tr>';
							
						//if ($set->userInfo['id'] == "1") {
						if ($set->userInfo['userType'] == "sys") {
							$set->content .='<tr><td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="isSmtpDebugMode" '.($set->isSmtpDebugMode ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Is Smtp Debug Mode?').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['isSmtpDebugMode']  .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td></tr>';
						}
					$set->content .='
								
							</tr>
							<tr>
								<td valign="top">'.lang('Email Header Image HTML').':</td>
								<td>
								<textarea name="db[emailHeaderImageURL]" id="emailHeaderImageURL" cols="60" rows="4">'.  (!empty($set->emailHeaderImageURL) ? $set->emailHeaderImageURL : "") .'</textarea>
								
								<div class="question tooltip"><span class="tooltiptext">'. $tooltips['emailHeaderImageURL']  .'</span></div></td>
							</tr>
							<tr>
								<td valign="top">'.lang('Email Footer HTML').':</td>
								<td>
								
								<textarea name="db[emailFooterImageURL]" id="emailFooterImageURL" cols="60" rows="4">'.  (!empty($set->emailFooterImageURL) ? $set->emailFooterImageURL : "") .'</textarea>
								<div class="question tooltip"><span class="tooltiptext">'. $tooltips['emailFooterImageURL'] .'</span></div></td>
							</tr>
							<tr>
								<td valign="top">'.lang('Email Signature').' ,  ('.lang('put your html code for signature').') :<div class="question tooltip"><span class="tooltiptext">'. $tooltips['emailSignature'].'</span></div></td>
								<td><textarea name="db[emailSignature]" cols="81" rows="10">'.$set->emailSignature.'</textarea></td>
							</tr>
						</tbody>
						</table>
					</div>
					</div>
					
					<div id="integration" data-tab="integration" class="config_tabs">
					<div class="normalTableTitle" data-tab2="integration">'.lang('Integration').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
							'. ($set->userInfo['userType'] == "sys"?'<tr>
								<td valign="top">'.lang('Base URL').':</td>
								<td><input type="text" id="sitebaseurl" name="db[sitebaseurl]" style="width:300px" value="'.$set->sitebaseurl.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['sitebaseurl'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>
							</tr>
							<tr>
								<td valign="top">'.lang('Cron URL').':</td>
								<td><input type="text" id="cronUrls" name="db[cronUrls]" style="width:300px" value="'.$set->cronUrls.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['cronUrls'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>
							</tr>
							<tr>
								<td valign="top">'.lang('Cron Time Difference Sheet').':</td>
								<td><input type="text" id="cronRecordsTimeDif" name="db[cronRecordsTimeDif]" style="width:20px" value="'.$set->cronRecordsTimeDif.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['cronRecordsTimeDif'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>
							</tr>
							<tr>
								<td valign="top">'.lang('Cron PNL - number of run a day').':</td>
								<td><input type="text" id="cronPnlRunAday" name="db[cronPnlRunAday]" style="width:20px" value="'.$set->cronPnlRunAday.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['cronPnlRunAday'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>
							</tr>
							<tr>
									<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="AllowAffiliateDuplicationOnCampaignRelation" '.($set->AllowAffiliateDuplicationOnCampaignRelation ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Allow Affiliate Duplications On CampaignRelation Page').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['AllowAffiliateDuplicationOnCampaignRelation'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
							</tr>
							<tr>
								<td valign="middle">'.lang('Push leads URL for new IB registered in the system').':</td>
								<td><input type="text" id="IBpushLeadOnRegistrationUrl" name="db[IBpushLeadOnRegistrationUrl]" style="width:600px" value="'.($set->IBpushLeadOnRegistrationUrl).'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['IBpushLeadOnRegistrationUrl'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>
							</tr>':'');
							if ($set->userInfo['userType'] == "sys") {
						
						
						
							/* $creativefeedUrl = "";
				
				if (!empty($set->apiStaticIP) && !empty($set->apiToken)) {
					// $creativefeedUrl = $set->webAddress .'/affiliate/apifeed.php?apiToken='. $db['apiToken'].'&apiStaticIP='. $db['apiStaticIP'].'&affiliate_id='. $id  .'&fromdate=' . date("Y-m-01",strtotime("-1 Months"));
					$creativefeedUrl = $set->webAddress .'api/feeds/creative.php?apiToken='. $set->apiToken;
					
				}
				
				
						
						$apiFeed = '
											
												
												<tr>
													<td align="center"><select name="db[apiAccessType]">
																						<option '.($set->apiAccessType=='' ? " selected " : "").' value="" selected>' . lang('None') . '</option>
																						<option '.($set->apiAccessType=='creative' ? " selected " : "").'value="creative">' . lang('Creative') . '</option>
																						<option '.($set->apiAccessType=='merchants' ? " selected " : "").'value="merchants">' . lang('Merchants') . '</option>
																						<option '.($set->apiAccessType=='affiliates' ? " selected " : "").'value="affiliates">' . lang('Affiliates') . '</option>
																						<option '.($set->apiAccessType=='leadStatus' ? " selected " : "").'value="leadStatus">' . lang('Lead Status') . '</option>
																					</select></td>
													
													<td align="center"><input type="text" name="db[apiStaticIP]" value="'.$set->apiStaticIP.'"  style="width:100px;"/></td>
													<td align="center"><span><input style="width: 240px;" id="apiToken" type="text" name="db[apiToken]" value="'.$set->apiToken.'" /></span><span>&nbsp;</span><span><button id="putDefaultTextGuid">'.lang('Generate').'</button></span></td>
													<td align="center"><textarea style="width:400px;height:50px;" type="text"  readonly >'.$creativefeedUrl .'</textarea></td>
													<td align="center">'.(empty($creativefeedUrl) || $set->apiAccessType=='None' || $set->apiAccessType=='' ?lang('Inactive') : lang('Active')).'</td>
													<td align="center">
													<input type="submit" value="'.lang('Update').'" />
													</td>
													
												</tr>
												
											
									'; 

								
								$set->content .= '
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
								
								
								<td colspan=3 valign="top"><span style="padding:5px;">'.lang('API Access ').':</span><div class="question tooltip"><span class="tooltiptext">'. $tooltips['APIAccesstocreativelist'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div><br/>
								<table class="normal" width="100%" border="0" cellpadding="0" cellspacing="0">
												<thead><tr>
													<td align="center">'.lang('Access Type').'</td>
													<td align="center">'.lang('Company Static IP').'</td>
													<td align="center">'.lang('Token').'</td>
													<td align="center">'.lang('FeedUrl').'</td>
													<td align="center">'.lang('Status').'</td>
													<td align="center">'.lang('Action').'</td>
												</tr></thead>
								<tfoot>
								'.$apiFeed.'
								'.$apiFeed.'
								'.$apiFeed.'
								'.$apiFeed.'
								</tfoot>
								</table>
								
								</td>
							</tr>
						
		<div style="margin-top:20px;margin-left:15px;">* '.lang('Update empty values to delete active permission').'.</div>
										
										<script>
								
								function S4() {
								return (((1+Math.random())*0x10000)|0).toString(16).substring(1); 
								}

								 $("#putDefaultTextGuid").click(function()	{
											// debugger;
									guid = (S4() + S4() + "-" + S4() + "-4" + S4().substr(0,3) + "-" + S4() + "-" + S4() + S4() + S4()).toLowerCase();
											$("#apiToken").val(guid);
											
											return false;
										});  
										
								</script>
								
						';
								*/
							}
						$set->content .= '
						<tr>
							<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="hidePendingProcessHighAmountDeposit" '.($set->hidePendingProcessHighAmountDeposit ? 'checked="checked"' : '').'  value=1/><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Disable pending process of high amount of deposits').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['hidePendingProcessHighAmountDeposit'].'</span></div></div></td>
							</tr>
							<tr>
								<td valign="top">'.lang('Pending deposits amount limit').':</td>
								<td><input type="text" id="pendingDepositsAmountLimit" name="db[pendingDepositsAmountLimit]" style="width:100px" value="'.$set->pendingDepositsAmountLimit.'"/>$<div class="question tooltip"><span class="tooltiptext">'. $tooltips['pendingDepositsAmountLimit'] .'</span></div></td>
							</tr>
							'.($set->userInfo['userType'] == "sys" ? '
							<tr>
								<td valign="top">'.lang('PNL / Revshare table name').':</td>
								<td><input type="text" id="pnlTable" name="db[pnlTable]" style="width:100px" value="'.$set->pnlTable.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['pnlTable'] .'</span></div></td>
							</tr>' : '' ).'
						</tbody>
						</table>
					</div>
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
									
									$crDisplayName = strtolower($crName)=='sale' ? 'FTD' : $crName;
									
									$set->content .= '												
									<tr '.($l % 2 ? 'class="trLine"' : '').'>
									<td align="center"><div><label class="switch"><input class="pxChk" type="checkbox" name="'.$crName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /><div class="slider round"></div></label></div></td>
									<td align="center">'.ucwords($crDisplayName).'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['combinedPixelOption_' . $crName]  .'</span></div></td>
									
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
					</div>
					</div>
					
					<div id="apifeeds" data-tab="apifeeds" class="config_tabs">
					<div class="normalTableTitle" data-tab2="apifeeds">'.lang('API and Feeds').'</div>
					<div align="left" style="background: #EFEFEF;">
						';
						include ('config_apiNfeed.php');
						
						$set->content .='</div>
					</div>
					
					
					<div id="payments" data-tab="payments" class="config_tabs">
					<div class="normalTableTitle" data-tab2="payments">'.lang('Payments').'</div>
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
								<td align="center"><div><label class="switch"><input class="pyChk" type="checkbox" name="'.$pyName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /><div class="slider round"></div></label></div></td>
								<td align="center">'.ucwords(trim($pyName)=='chinaunio'?'China UnionPay':$pyName).'<div class="question tooltip"><span class="tooltiptext availablePayments_' . ($pyName=="chinaunio"?'China UnionPay':$pyName) .'">'.$tooltips['availablePayments_' . ($pyName=="chinaunio"?'China UnionPay':$pyName)] .'</span></div></td>
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
								<td align="center"><div><label class="switch"><input class="crChk" type="checkbox" name="'.$crName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /><div class="slider round"></div></label></div></td>
								<td align="center">'.ucwords($crName).'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['availableCurrencies_' . $crName] .'</span></div></td>
								
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
					</div>
					</div>
					<script type="text/javascript" src="'.$set->SSLprefix.'js/bootstrap.min.js"></script>
					<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
					<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
					<div id="registration_settings" data-tab="registration_settings" class="config_tabs">
					<div class="normalTableTitle" data-tab2="registration_settings">'.lang('Registration').'</div>
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
									
									if (!empty($avapm[$i])){
											$isActive = 1;
											$pyName = $avapm[$i];
											if (strpos('*' . $avapm[$i],'-')==1) {
													$isActive=0;
													$pyName = ltrim($avapm[$i],'-');
											}
											
											$set->content .= '												
											<tr '.($l % 2 ? 'class="trLine"' : '').'>
											<td align="center">'.ucwords(trim(splitStringByCapitalLetters($pyName))).'</td>
											<td align="center"><div><label class="switch"><input class="mnChk" type="checkbox" name="'.$pyName.'" value="'.$isActive.'" '.(($isActive==1) ? 'checked' : '').' /><div class="slider round"></div></label><div class="question tooltip" style="top:-8px"><span class="tooltiptext" >'.  $tooltips['mustFields_'.$pyName] .'</span></div></div></td>
											
											</tr>';
									}
							
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
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails"">
						<tbody>
						<input type="hidden" name="db[hideCountriesOnRegistration]" id="selected_countries"/>
							<tr>
								<td>'. lang('Hide Countries On Registration') . ' . <select name="hideCountries" id="hideCountries" style="width: 100px;" multiple="multiple">'.countriesList().'</select> <div class="question tooltip"><span class="tooltiptext">'.$tooltips['hideCountries'] .'</span></div> &nbsp;&nbsp; '. ($selected_countries!=""?'('. lang('Selected Countries : ') . lang($selected_countries) . ')':'').'</td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="captureAffiliatesRegistration" '.($set->captureAffiliatesRegistration ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Capture Affiliates Registration').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['captureAffiliatesRegistration'].'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="allowCapthaOnReg" '.($set->allowCapthaOnReg ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Activate CAPTCHA On Registration').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['allowCapthaOnReg'].'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="BlockLoginUntillEmailVerification" '.($set->BlockLoginUntillEmailVerification ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Block login until email verification is done').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['BlockLoginUntillEmailVerification'].'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="allowCapthaOnReset" '.($set->allowCapthaOnReset ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Activate CAPTCHA On Reset Password').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['allowCapthaOnReset'].'</span></div></div></td>
							</tr>
							<tr>
							<td><div style="float:left;"><label class="switch"><input type="checkbox" name="allowCapthaOnMerchantReset" '.($set->allowCapthaOnMerchantReset ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Activate CAPTCHA On Merchants Reset Password').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['allowCapthaOnMerchantReset'].'</span></div></div></td>
							</tr>
							<tr>							
							<td><div style="float:left;"><label class="switch"><input type="checkbox" name="allowCapthaOnMerchantReg" '.($set->allowCapthaOnMerchantReg ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Activate CAPTCHA On Merchants Registration').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['allowCapthaOnMerchantReg'].'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="autoRelateSubAffiliate" '.($set->autoRelateSubAffiliate ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Auto Relate New SubAffiliates To Affiliate's Parent Group").'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['autoRelateSubAffiliate'] .'</span></div></div></td>
							</tr><tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="autoRelateNewAffiliateToAllMerchants" '.($set->autoRelateNewAffiliateToAllMerchants ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Auto Relate New Affiliate To All Merchants").'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['autoRelateNewAffiliateToAllMerchants'] .'</span></div></div></td>						
						</tr><tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="autoRelateNewAffiliateToAllProducts" '.($set->autoRelateNewAffiliateToAllProducts ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Auto Relate New Affiliate To All Products").'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['autoRelateNewAffiliateToAllProducts'] .'</span></div></div></td>
							</tr>
							<tr>
									<td><div style="float:left;"><label class="switch"><input type="checkbox" name="pending" '.($set->pending ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Approve manually new affiliates registration').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['ApproveManuallyNewAffiliatesRegistration'].'</span></div></div></td>
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showGroupValuesOnAffReg" '.($set->showGroupValuesOnAffReg ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang("Show Groups Values on Affiliate Registration").' <select name="showGroupsLanguages">
								<option value="">-Select-</option>
								<option value="group_name" '. ($set->showGroupsLanguages =="group_name"? 'selected' : '')  .'>Group Name</option>
								<option value="language_name"'. ($set->showGroupsLanguages =="language_name"? 'selected' : '') .'>Language Name</option>
								<option value="display_lname"'. ($set->showGroupsLanguages =="display_lname"? 'selected' : '') .'>Display Language Name</option></select><div class="question tooltip"><span class="tooltiptext">'. $tooltips['showGroupValuesOnAffReg_options'] .'</span></div></div></td>
							</tr>
							<tr>
								<td>'.lang('Terms And Condition text on affiliate registration').'<input type="text" style="width: 600px" name="db[termsCheckboxCaption]" value="'.$set->termsCheckboxCaption.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['termsCheckboxCaption'] .'</span></div>'.lang("Don't forget to write 'Terms and Conditions' inside the textbox").'</td>
							</tr>
							<tr>
								<td>'.lang('Newsletter sign-up text on affiliate registration').'<input type="text" style="width: 600px" name="db[newsletterCheckboxCaption]" value="'.$set->newsletterCheckboxCaption.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['newsletterCheckboxCaption'] .'</span></div></td>		
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="db[affiliateNewsletterCheckboxValue]" '.($set->affiliateNewsletterCheckboxValue ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Select recieve newsletter checkbox as default on affiliate registration').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['affiliateNewsletterCheckboxValue'] .'</span></div></div></td>		
							</tr>
							</tbody>
							</table>
							<div class="normalTableTitle" data-tab2="registration_settings">'.lang('Affiliate').'</div>
							<div align="left" style="background: #EFEFEF;">
							<table width="100%" border="0" cellpadding="5" cellspacing="0" class="normal tblDetails"">
							<tbody>
									<tr>
										<td>'.lang("Default Qualify Commission For New Affiliates").':&nbsp;
										<select  id="empnt" name="db[def_qualify_type_for_affiliates]" style="width: 292px;">
													<option value="0"  '.($set->def_qualify_type_for_affiliates == 0 ? 'selected' : ''). '>'.lang('None').'</option>
													<option value="1" '.($set->def_qualify_type_for_affiliates == 1 ? 'selected' : ''). '>'.lang('Merchant Default').'</option></select><div class="question tooltip"><span class="tooltiptext">'. $tooltips['def_qualify_type_for_affiliates']  .'</span></div></td>
										</tr>
										
										<tr>
										<td>'.lang("Default Profile Permission For New Affiliate").':&nbsp;
										<select  id="empnt" name="db[def_profilePermissionsForAffiliate]" style="width: 292px;">
													<option value="0"  '.($set->def_profilePermissionsForAffiliate == 0 ? 'selected' : ''). '>'.lang('Default View').'</option>
													<option value="-1" '.($set->def_profilePermissionsForAffiliate == -1 ? 'selected' : ''). '>'.lang('Automatic By Deal').'</option></select><div class="question tooltip"><span class="tooltiptext">'. $tooltips['def_profilePermissionsForAffiliate'] .'</span></div></td>
										</tr>
							</tbody>
							</table>
							</div>
					</div>
					</div>
					<div id="tracker" data-tab="tracker" class="config_tabs">
					<div data-tab2="tracker" class="normalTableTitle">'.lang('Tracker').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
							
							<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="blockTrafficFromInactiveAffiliate" '.($set->blockTrafficFromInactiveAffiliate ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Block Traffic From Inactive Affiliates').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['blockTrafficFromInactiveAffiliate'] .'</span></div></div></td>								
						</tr>
						<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="writeFinalTrackingUrlToLog" '.($set->writeFinalTrackingUrlToLog ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Log Tracking Data To Log File').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['writeFinalTrackingUrlToLog'] .'</span></div></div></td>								
						</tr>
						
						<tr>
								<td valign="top">'.lang('Force push ctag with the following parameters in additional to the default ctag parameter').':</td>
								<td><input type="text" id="forceParamsForTracker" name="db[forceParamsForTracker]" style="width:500px" value="'.$set->forceParamsForTracker.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['forceParamsForTracker'].'</span></div></td>
							</tr>
							<tr>
								<td valign="top">'.lang('Allow Secured Tracking Code').':</td>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="AllowSecuredTrackingCode" '.($set->AllowSecuredTrackingCode ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;"><div class="question tooltip"><span class="tooltiptext">'. $tooltips['AllowSecuredTrackingCode'] .'</span></div></div></td>	
							</tr>
							<tr>
								<td valign="top">'.lang('Display affiliate id on tracker url with the following field name').':</td>
								<td><input type="text" id="exportAffiliateIDonTrackerFieldName" name="db[exportAffiliateIDonTrackerFieldName]" style="width:500px" value="'.$set->exportAffiliateIDonTrackerFieldName.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['exportAffiliateIDonTrackerFieldName'].'</span></div><br/>
								<span style="font-size:9px" align="left">'.lang('Leave blank to deactivate this feature').'.</span>
								</td>
							</tr>
							<tr>
								<td valign="top">'.lang('Export Creative Name With Parameter').':</td>
								<td><input type="text" id="exportCreativeNameWithParam" name="db[exportCreativeNameWithParam]" style="width:500px" value="'.$set->exportCreativeNameWithParam.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['exportCreativeNameWithParam'].'</span></div></td>
							</tr>
							<tr>
								<td valign="top">'.lang('Export Language OF Creative With Parameter').':</td>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="exportLangCreativeNameWithParam" '.($set->exportLangCreativeNameWithParam ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Export Creative Name Language With Param').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['exportLangCreativeNameWithParam'] .'</span></div>
								<br/><span style="font-size:9px" align="left">'.lang('Leave blank to deactivate this feature').'.</span></div>
							</td>
							</tr>
							<tr>
								<td valign="top">'.lang('Display Profile Source on tracker url with the followinf field name').':</td>
								<td><input type="text" id="exportProfileNameToTrackerFieldName" name="db[exportProfileNameToTrackerFieldName]" style="width:500px" value="'.$set->exportProfileNameToTrackerFieldName.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['exportProfileNameToTrackerFieldName'] .'</span></div>
								<br/><span style="font-size:9px" align="left">'.lang('Leave blank to deactivate this feature').'.</span>
								</td>
							</tr>
						<tr>
							<td valign="top">'.lang('UTM tags for landing page').':</td>
							<td><input text="text" id="utmtags" name="db[utmtags]" style="width:400px" value="'.$set->utmtags.'"/><span><button id="putDefaultText">'.lang('Click for default').'</button></span><div class="question tooltip"><span class="tooltiptext">'. $tooltips['utmtags'].'</span></div></td>
								<script>
								$("#putDefaultText").click(function()	{
									$("#utmtags").val("&utm_source=affiliateBuddies&utm_campaign={AffUserName}&utm_medium=affiliate&utm_term={AffiliateID}&utm_content={BannerID}");
									return false;
								}); 
							</script>
						</tr>
						<tr>
							<td valign="top">'.lang('META Tags For Tracking Header').':<div class="question tooltip"><span class="tooltiptext">'. $tooltips['metaTrackingHeader'].'</span></div></td>
							<td><textarea name="db[metaTrackingHeader]" cols="81" rows="10">'.$set->metaTrackingHeader.'</textarea></td>
							
						</tr>
						<tr>
							<td valign="top">'.lang('Force override coupon tracker than other trackers').':</td>
							<td><div style="float:left;"><label class="switch"><input type="checkbox" id="overrideByCoupon" name="db[overrideByCoupon]"  value="'.$set->overrideByCoupon.'"  '.($set->overrideByCoupon ? 'checked="checked"' : '').'/><div class="slider round"></div></label></div><div class="question tooltip"><span class="tooltiptext">'.$tooltips['overrideByCoupon'].'</span></div></td>
						</tr>
							'. ($set->userInfo['userType'] == "sys"?'<tr>
								<td valign="top">'.lang('Default Tracking URL').':</td>		
								<td><input type="text" style="width: 480px" name="db[defaultTrackingUrl]" value="'.$set->defaultTrackingUrl.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['defaultTrackingUrl']  .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>
							</tr>':'').'
						</table>
						
					</div>
					<div data-tab2="tracker" class="normalTableTitle">'.lang('Methods').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tr>
								<td width="25%"><div style="float:left;"><label class="switch"><input type="checkbox" name="qrcode" '.($set->qrcode ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('QR Code').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['qrcode'] .'</span></div></div></td>
								<td width="25%"><div style="float:left;"><label class="switch"><input type="checkbox" name="facebookshare" '.($set->facebookshare ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Facebook Share').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['facebookshare']  .'</span></div></div></td>
							</tr>
							</table>
							</div>
						</div>
						'. ($set->userInfo['userType'] == "sys" || true?'
					<!-- Documents-->
					<div id="documents" data-tab="documents" class="config_tabs">
					<div class="normalTableTitle" data-tab2="documents">'.lang('Documents').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="showRequierdDocsOnAffiliateDash" '.($set->showRequierdDocsOnAffiliateDash ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Requierd Documents Popup On Affiliate Dashboard').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showRequierdDocsOnAffiliateDash']  .'</span></div>&nbsp;&nbsp;<div class="_exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>		
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="AskDocTypePassport" '.($set->AskDocTypePassport ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Ask For Passport Document Verification From Affiliates').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['AskDocTypePassport'] .'</span></div>&nbsp;&nbsp;<div class="_exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>		
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="AskDocTypeAddress" '.($set->AskDocTypeAddress ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Ask For Address Document Verification From Affiliates').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['AskDocTypeAddress'] .'</span></div>&nbsp;&nbsp;<div class="_exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>		
							</tr>
							<tr>
								<td><div style="float:left;"><label class="switch"><input type="checkbox" name="AskDocTypeCompany" '.($set->AskDocTypeCompany ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Ask For Company Document Verification From Affiliates').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['AskDocTypeCompany'] .'</span></div>&nbsp;&nbsp;<div class="_exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>		
							</tr>
							<tr>
								<td>'.lang('Show the following sentence for affiliates with missing verification documents').'<input type="text" style="width: 560px" name="AskDocSentence" value="'.$set->AskDocSentence.'"/><div class="question tooltip"><span class="tooltiptext">'.$tooltips['AskDocTypeCompany']  .'</span></div>&nbsp;&nbsp;<div class="_exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>		
							</tr>
							</tbody>
							</table>
					</div>
					</div>':'').'
					<!-- Design -->
					<div id="design" data-tab="design" class="config_tabs">
					<div class="normalTableTitle" data-tab2="design">'.lang('Design').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
							<tr>
										<td width="30%" align="left">'.lang('Dashboard Main Title').':</td>
										<td><input type="text" name="db[dashBoardMainTitle]" value="'.$set->dashBoardMainTitle.'" style="width: 250px;" />	<span style="font-size:9px" align="left">'.lang('Leave blank for system default').'.</span><div class="question tooltip"><span class="tooltiptext">'.$tooltips['dashBoardMainTitle'] .'</span></div></td>
							</tr>
							<tr>
									<td colspan="2"><div style="float:left;"><label class="switch"><input type="checkbox" name="showTitleOnLoginPage" '.($set->showTitleOnLoginPage ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Show Title On Login Page').'<div class="question tooltip"><span class="tooltiptext">'. $tooltips['showTitleOnLoginPage']  .'</span></div></div></td>
							</tr>
							<tr>
									<td valign="top">'.lang('Affiliate Login Background Image Path').':</td>
									<td>'. fileField('affiliateLoginImage',(strpos($set->affiliateLoginImage,"/tmp")?'../images/wheel.gif':$set->affiliateLoginImage)) .'&nbsp;&nbsp|&nbsp;&nbsp;<a href="javascript:void(0)" data-field="affiliateLoginImage" class="reset">'. lang('Reset') .'</a><!--input text="text" id="affiliateLoginImage" name="db[affiliateLoginImage]" style="width:600px" value="'.$set->affiliateLoginImage.'"/--><div class="question tooltip"><span class="tooltiptext">'. $tooltips['affiliateLoginImage'] .'</span></div></td>
							</tr>
							<tr>
									<td valign="top">'.lang('Admin and Manager Login Background Image Path').':</td>
									<td>'. fileField('adminLoginImage',(strpos($set->adminLoginImage,"/tmp")?'../images/wheel.gif':$set->adminLoginImage)) .'&nbsp;&nbsp|&nbsp;&nbsp;<a href="javascript:void(0)" data-field="adminLoginImage" class="reset">'. lang('Reset') .'</a><!--input text="text" id="adminLoginImage" name="db[adminLoginImage]" style="width:600px" value="'.$set->adminLoginImage.'"/--><div class="question tooltip"><span class="tooltiptext">'. $tooltips['adminLoginImage'] .'</span></div></td>
							</tr>
							<tr>
										<td align="left">'.lang('Charts Themes').':</td>
										<td><select  name="db[chartTheme]">
										<option value="default"'. ($set->chartTheme=='default'?' selected':'') .'>'. lang('Default') .'</option>
										<option value="dark_unica" '. ($set->chartTheme=='dark_unica'?'selected':'') .'>'. lang('Dark Unica') .'</option>
										<option value="sand_signika" '. ($set->chartTheme=='sand_signika'?'selected':'') .'>'. lang('Sand Signika') .'</option>
										<option value="grid_light" '. ($set->chartTheme=='grid_light'?'selected':'') .'>'. lang('Grid Light') .'</option>
										</select><div class="question tooltip"><span class="tooltiptext">'.  $tooltips['chartTheme'].'</span></div></td>
							</tr>'. ($set->userInfo['userType'] == "sys"?'
							<tr>
								<td valign="top">'.lang('Logo URL').':</td>
								<td>'. fileField('logoPath',(strpos($set->logoPath,"/tmp")?'../images/wheel.gif':$set->logoPath)) .'&nbsp;&nbsp|&nbsp;&nbsp;<a href="javascript:void(0)" data-field="logoPath" class="reset">'. lang('Reset') .'</a><!--input type="text" id="logoPath" name="db[logoPath]" style="width:600px" value="'.$set->logoPath.'"/--><div class="question tooltip"><span class="tooltiptext">'.$tooltips['logoPath'].'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div>
								</td>
							</tr>
							<tr>
								<td valign="top">'.lang('Billing Logo URL').':</td>
								<td>'. fileField('billingLogoPath',(strpos($set->billingLogoPath,"/tmp")?'../images/wheel.gif':$set->billingLogoPath)) .'&nbsp;&nbsp|&nbsp;&nbsp;<a href="javascript:void(0)" data-field="billingLogoPath" class="reset">'. lang('Reset') .'</a><!--input type="text" id="billingLogoPath" name="db[billingLogoPath]" style="width:600px" value="'.$set->billingLogoPath.'"/--><div class="question tooltip"><span class="tooltiptext">'. $tooltips['billingLogoPath'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div>
								</td>
							</tr>
							<tr>
								<td valign="top">'.lang('Favicon URL').':</td>
								<td>'. fileField('faviconPath',(strpos($set->faviconPath,"/tmp")?'../images/wheel.gif':$set->faviconPath)) .'&nbsp;&nbsp|&nbsp;&nbsp;<a href="javascript:void(0)" data-field="faviconPath" class="reset">'. lang('Reset') .'</a><!--input type="text" id="faviconPath" name="db[faviconPath]" style="width:600px" value="'.($set->faviconPath).'"/--><div class="question tooltip"><span class="tooltiptext">'.$tooltips['faviconPath'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div>
								</td>
							</tr>
							<tr>
								<td valign="top">'.lang('Secondary Powered By Logo').':</td>
								<td>'. fileField('secondaryPoweredByLogo',(strpos($set->secondaryPoweredByLogo,"/tmp")?'../images/wheel.gif':$set->secondaryPoweredByLogo)) .'&nbsp;&nbsp|&nbsp;&nbsp;<a href="javascript:void(0)" data-field="secondaryPoweredByLogo" class="reset">'. lang('Reset') .'</a><!--input type="text" id="secondaryPoweredByLogo" name="db[secondaryPoweredByLogo]" style="width:600px" value="'.$set->secondaryPoweredByLogo.'"/--><div class="question tooltip"><span class="tooltiptext">'.$tooltips['secondaryPoweredByLogo'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div>
								
								</td>
							</tr>
							<tr>
								<td valign="top">'.lang('Secondary Powered By Logo HREF URL').':</td>
								<td><input type="text" id="secondaryPoweredByLogoHrefUrl" name="db[secondaryPoweredByLogoHrefUrl]" style="width:600px" value="'.$set->secondaryPoweredByLogoHrefUrl.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['secondaryPoweredByLogoHrefUrl']  .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></td>
							</tr>
							<tr>
								<td colspan=2><div style="float:left;"><label class="switch"><input type="checkbox" name="hidePoweredByABLogo" '.($set->hidePoweredByABLogo ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div><div style="padding-top:0px;">'.lang('Hide Powered By AB Logo From Login').'<div class="question tooltip"><span class="tooltiptext">'.$tooltips['hidePoweredByABLogo'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div></div></td>
							</tr>':'').'
						</tbody>
						</table>
					</div>
					</div>
					<!-- Advanced -->
					<div id="advanced" data-tab="advanced" class="config_tabs">
					<div class="normalTableTitle" data-tab2="advanced">'.lang('Advanced').'</div>
					<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
							<tr>
								<td valign="top">'.lang('Analytics Code').':<div class="question tooltip"><span class="tooltiptext">'.$tooltips['analyticsCode'] .'</span></div></td>
								<td><textarea name="db[analyticsCode]" cols="81" rows="10">'.$set->analyticsCode.'</textarea></td>
							</tr>
							<tr>
								<td>'.lang('Number of failure attempts to login').'</td><td><input type="text" style="width: 30px" name="db[numberOfFailureLoginsAttempts]" value="'.$set->numberOfFailureLoginsAttempts.'"/><div class="question tooltip"><span class="tooltiptext">'. $tooltips['numberOfFailureLoginsAttempts']  .'</span></div></td>		
							</tr>
							<tr>
								<td valign="top">'.lang('Affiliate Registration Image Pixel Code').':<div class="question tooltip"><span class="tooltiptext">'. $tooltips['affiliateRegistrationPixel']  .'</span></div></td>
								<td><textarea name="db[affiliateRegistrationPixel]" cols="81" rows="10">'.$set->affiliateRegistrationPixel.'</textarea></td>
							</tr>
						</tbody>
						</table>
					</div>
					</div>
					
					<!-- Terms and Conditions -->
					<div data-tab="terms_and_conditions" id="terms_and_conditions" class="config_tabs">
					<div data-tab2="terms_and_conditions" class="normalTableTitle">'.lang('Terms and Conditions').'</div>
					<div align="left" style="background: #EFEFEF;">
					
					<div id="tab-container2" class="tab-container">
						 <ul class="etabs">
							<li class="tab"><a href="#tabs1-affiliates">'. lang('Affiliates') .'</a></li>
							' . ($set->showAdvertiserModule?'
							<li class="tab"><a href="#tabs1-merchants">'. lang('Merchants') .'</a></li>':'') .'
						 </ul>
						 <div class="panel-container">
							<div id="tabs1-affiliates">
											
											<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
											'.(!empty($set->terms_link)?'<tr style="padding:5px"><td colspan=3>'.lang('Current Terms & Conditions URL').': '. (strpos($set->terms_link,"/tmp/")? '<span style="color:red"><strong><i>' . lang('System is looking for viruses. Please refresh the page in a minute.') .'</i></strong></span>':'<a href= "'. (strpos($set->terms_link,"/tmp/")?'':$set->terms_link) .'" target="_blank">'. (strpos($set->terms_link,"/tmp/")?lang('System is looking for viruses. Please refresh the page in a minute.'):$set->terms_link) .'</a>').'</td></tr>':'').'
											<tr>
														<td colspan="2" height="5"></td>
												
											</tr>
											<tr>
											<td colspan=3 align="left">'.lang('Terms & Conditions').':</td>
											</tr>
											<tr>
												<td>
												<div style="width:50px;float:left;"><input type="radio" name="terms" value="input_terms"/></div>
												<div style="float:left;"><input type="text" name="db[terms_link]" value="'.(strpos($set->terms_link,"/tmp/")?'':($set->terms_link ? $set->terms_link : 'http'.$set->SSLswitch.'://')).'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'.$tooltips['terms_link'] .'</span></div></div>
												<div style="clear:both;height:10px"></div>
												<div style="width:50px;float:left;"><input type="radio" name="terms" value="browse_terms"/></div>
												<div style="float:left;"><input type="file" name="terms_link_file" style="width: 220px;" />('.lang('HTML file only').')<div class="question tooltip"><span class="tooltiptext">'. $tooltips['terms_link_file'] .'</span></div></div>
												<div style="clear:both;height:10px"></div>
												<div style="width:50px;float:left;"><input type="radio" name="terms" value="html_terms"/></div>
												<div style="float:left;"><textarea name="terms_link_html" id="contentMail" cols="80" rows="40">'.  (strpos($set->terms_link,"/tmp/")?'':(!empty($set->terms_link) ? @file_get_contents($set->terms_link) : "")) .'</textarea></div>
												<div class="question tooltip"><span class="tooltiptext">'.$tooltips['terms_link_html'].'</span></div>
												</td>
											</tr>
											<tr>
													<td colspan="2" height="20"></td>
											</tr>
											</table>
							
							
							</div>' . ($set->showAdvertiserModule?'
							<div id="tabs1-merchants">
								
											<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
											'.(!empty($set->terms_link)?'<tr style="padding:5px"><td colspan=3>'.lang('Current Terms & Conditions URL').': <a href= "'. $set->merchants_terms_link .'" target="_blank">'. $set->merchants_terms_link .'</a></td></tr>':'').'
											<tr>
														<td colspan="2" height="5"></td>
												
											</tr>
											<tr>
											<td colspan=3 align="left">'.lang('Terms & Conditions').':</td>
											</tr>
											<tr>
												<td>
												<div style="width:50px;float:left;"><input type="radio" name="merchant_terms" value="input_terms_merchant"/></div>
												<div style="float:left;"><input type="text" name="db[merchants_terms_link]" value="'.($set->merchants_terms_link ? $set->merchants_terms_link : 'http://').'" style="width: 250px;" /><div class="question tooltip"><span class="tooltiptext">'.$tooltips['merchants_terms_link'] .'</span></div></div>
												<div style="clear:both;height:10px"></div>
												<div style="width:50px;float:left;"><input type="radio" name="merchant_terms" value="browse_terms_merchant"/></div>
												<div style="float:left;"><input type="file" name="merchants_terms_link_file" style="width: 220px;" />('.lang('HTML file only').')<div class="question tooltip"><span class="tooltiptext">'. $tooltips['merchants_terms_link_file'] .'</span></div></div>
												<div style="clear:both;height:10px"></div>
												<div style="width:50px;float:left;"><input type="radio" name="merchant_terms" value="html_terms_merchant"/></div>
												<div style="float:left;"><textarea name="merchants_terms_link_html" id="merchantContentMail" cols="80" rows="40">'.  (!empty($set->merchants_terms_link) ? @file_get_contents($set->merchants_terms_link) : "") .'</textarea></div>
												<div class="question tooltip"><span class="tooltiptext">'.$tooltips['terms_link_html'].'</span></div>
												</td>
											</tr>
											<tr>
													<td colspan="2" height="20"></td>
											</tr>
											</table>
							
							</div>':'').'
						</div>
					</div>
					
					
					</div></div>
					<link rel="stylesheet" type="text/css" href="/css/jquery.cleditor.css" />
						<script type="text/javascript" src="/js/jquery.cleditor.js"></script>
						<script type="text/javascript">
							$(document).ready(function () {
								
								e = $("#contentMail").cleditor({
									width:        800,
									height:       400,
									
									});
								e.change(function(){
									$("input:radio[name=terms]").attr("checked",true);
								});	
									$("#merchantContentMail").cleditor({
									width:        800,
									height:       400
									});
									
									$("#companyDetails").cleditor({
									width:        800,
									height:       400
									});
									
									
									$(".reset").on("click",function(){
											
											var field = $(this).data("field");
											$.post("'.$set->SSLprefix.'ajax/updateSettingField.php",{type:reset,fieldname:field},function(res){
												 
											});
											
									});
									
								});
								
						</script>
					
					
					
					<div class="btnsave" align="center" style="margin-top:20px;bottom: 35px;right: 40%;"><input type="submit" value="'.lang('Save').'" /></div>
					</form>
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
					</script>
					';	
	$set->content .= "</div>";
		theme();
		break;
	}

?>>