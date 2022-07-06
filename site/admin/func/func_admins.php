<?php
// die('grtgrt');
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function isAdmin() {
	global $set,$_SESSION;
	$resulta=function_mysql_query("SELECT id,username,password FROM admins WHERE id='".$_SESSION['session_id']."' AND valid='1' AND level='admin'",__FILE__,__FUNCTION__);
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($_SESSION['session_id'] == $chk['id'] AND $session_serial == $_SESSION['session_serial']) {
		/* if ($_GET['dbb']) {
			var_dump($_SESSION);
			echo '<br>';
			echo 'loggedin: ' . $_SESSION['loggedin_time'] . '<br>';
			echo 'time: ' . time() . '<br>';
			echo $set->login_session_duration . '<br>';
			die();
		} */
		 if(!isset($_SESSION['loggedin_time']) || (abs(time() - $_SESSION['loggedin_time']) < $set->login_session_duration *60)){
			$_SESSION['loggedin_time'] = time(); 
			return true; 
		 } else {
			 if ($_GET['ddd']) {
			  echo time().'<br>';
			 var_dump($_SESSION);
			 die(); 
			 }
		 }
	}
	return false;
	}
	
	
function adminInfo() {
	global $set,$_SESSION;
	if (!isAdmin()) return false;
	$resulta=function_mysql_query("SELECT admins.id, admins.preferedCurrency, admins.rdate, admins.ip, admins.chk_ip, admins.valid, admins.relatedMerchantID, admins.lang, admins.level, admins.username, admins.password, admins.first_name, admins.last_name, admins.email, admins.lastactive, admins.logged, admins.group_id, admins.phone, admins.IMUser, admins.zopimChat, admins.bigPic, admins.userType, merchants.producttype AS productType FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE admins.id='".$_SESSION['session_id']."' AND admins.valid='1'",__FILE__,__FUNCTION__);
	//$resulta=function_mysql_query("SELECT admins.id, admins.preferedCurrency, admins.rdate, admins.ip, admins.chk_ip, admins.valid, admins.relatedMerchantID, admins.lang, admins.level, admins.username, admins.password, admins.first_name, admins.last_name, admins.email, admins.lastactive, admins.logged, admins.group_id, admins.phone, admins.IMUser, admins.zopimChat, admins.bigPic, merchants.producttype AS productType FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE admins.id='".$_SESSION['session_id']."' AND admins.valid='1'",__FILE__);
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($session_serial == $_SESSION['session_serial']) return $chk;
	return false;
	}
	
function adminMenu() {
	global $set;
	
	$groupName = Array();
	$linkName = Array();
	$SublinkName = Array();
	
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/">'.lang('DashBoard').'</a>';
	$linkNameGroupCounter=1;
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/affiliates_list.php?act=search">'.lang('Affiliates').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/affiliates_list.php?act=search">'.lang('Affiliates List').'</a>';
        // $SublinkName[1][0][] = '<a href="'.$set->SSLprefix.'admin/affiliates.php?act=search">'.lang('Search Affiliates').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/affiliates.php?act=new">'.lang('Add Affiliate').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/affiliates_list.php?act=pending">'.lang('Pending Affiliates').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/affiliates_list.php?act=search&logged=1">'.lang('Online Affiliates').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/affiliatesStatus.php">'.lang('Affiliates Category').'</a>';
		
		$linkNameGroupCounter++;
	
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/creative.php#tab_3">'.lang('Marketing Tools').'</a>';
		$linkName[$linkNameGroupCounter][0] = '<a href="'.$set->SSLprefix.'admin/creative.php#tab_3">'.lang('Creative Materials').'</a>';
		$SublinkName[$linkNameGroupCounter][0][] = '<a href="'.$set->SSLprefix.'admin/creative.php?new=1">'.lang('Add New').'</a>';
		$SublinkName[$linkNameGroupCounter][0][] = '<a href="'.$set->SSLprefix.'admin/creative.php#tab_3">'.lang('Browse').'</a>';
		$SublinkName[$linkNameGroupCounter][0][] = '<a href="'.$set->SSLprefix.'admin/creative.php?act=trash">'.lang('Recycle Bin').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/promotion.php"').'>'.lang('Promotions').'</a>';
		if ($set->deal_geoLocation==1)
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/geoZones.php">'.lang('GEO Location Zones').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/languages.php">'.lang('Creative Languages').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':'  href="'.$set->SSLprefix.'admin/coupons.php"').'>'.lang('Coupons').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/creativeCategories.php"').'>'.lang('Categories').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':'  href="'.$set->SSLprefix.'admin/messages.php"').'>'.lang('Messages For Affiliates').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':'  href="'.$set->SSLprefix.'admin/messages-advertisers.php"').'>'.lang('Messages For Advertisers').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=ActiveCreatiesStats"').'>'.lang('Active Creatives Stats').'</a>';



		if ($set->showProductsPlace==1) {
			$linkNameGroupCounter++;
			$groupName[] = '<a href="'.$set->SSLprefix.'admin/products.php?act=products">'.lang('Markets Place').'</a>';
			//$linkName[$linkNameGroupCounter][0] = '<a href="'.$set->SSLprefix.'admin/products.php?act=cats">'.lang('Categories').'</a>';
			$linkName[$linkNameGroupCounter][0] = '<a href="'.$set->SSLprefix.'admin/product_categories.php">'.lang('Categories').'</a>';
			$linkName[$linkNameGroupCounter][1] = '<a href="'.$set->SSLprefix.'admin/products.php?act=products">'.lang('Products').'</a>';
			$linkName[$linkNameGroupCounter][2] = '<a href="'.$set->SSLprefix.'admin/product_reports.php">'.lang('Reports').'</a>';
		}
		
		
		$linkNameGroupCounter++;
		
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/reports.php">'.lang('Reports').'</a>';

		if (strpos($set->reportsToHide,'commissions_debts')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=commissions_debts"').'>'.lang('Commissions Debts Report').'</a>';

		if (strpos($set->reportsToHide,'commission-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=commission"').'>'.lang('Commission Report').'</a>';
	
		
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php"').'>'.lang('Quick Summary Report').'</a>';
		if (strpos($set->reportsToHide,'clicks-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=clicks"').'>'.lang('Clicks Report').'</a>';
		if (strpos($set->reportsToHide,'traffic-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=traffic"').'>'.lang('Referral Report').'</a>';
		if (strpos($set->reportsToHide,'creative-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=creative"').'>'.lang('Creative Report').'</a>';
	if (strpos($set->reportsToHide,'landingp-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=LandingPage"').'>'.lang('Landing Pages Report').'</a>';
		if (strpos($set->reportsToHide,'country-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=country"').'>'.lang('Country Report').'</a>';
		if (strpos($set->reportsToHide,'trader-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/reports.php?act=trader">'.lang(ptitle('Trader Report')).'</a>';
		if (strpos($set->reportsToHide,'tansaction-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=transactions"').'>'.lang(ptitle('Transaction Report')).'</a>';
		if (strpos($set->reportsToHide,'tradersts-ad')<1 && false)
		$linkName[$linkNameGroupCounter][] = '<a ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':'  href="'.$set->SSLprefix.'admin/reports.php?act=stats"').'>'.lang(ptitle('Trader Stats Report')).'</a>';
		if (strpos($set->reportsToHide,'affiliate-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':'  href="'.$set->SSLprefix.'admin/reports.php?act=affiliate"').'>'.lang('Affiliate Report').'</a>';
		
		if (strpos($set->reportsToHide,'dynamicf-ad')<1 && $set->showDynamicFilters==1 )
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=dynamic_filters"').'>'.lang('Dynamic Filters Report').'</a>';
		
		if (strpos($set->reportsToHide,'groups-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=group"').'>'.lang('Groups Report').'</a>';
		if (strpos($set->reportsToHide,'profile-ad')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=profile"').'>'.lang('Profile Report').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=sub"').'>'.lang('Sub Affiliates Report').'</a>';
		
		if ($set->deal_cpi==1){
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=install"').'>'.lang('Install Report').'</a>';
		}
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/reports.php?act=pixelsLogs"').'>'.lang('Pixel Logs Report').'</a>';
		
		$myReports = getMyFavoritsReports();
		if (!empty($myReports)){
			
			$linkNameGroupCounter++;
	
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/myFavorites.php">'.lang('My Reports').'</a>';
	$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/myFavorites.php">'.lang('Manage Favorits Reports').'</a>';
	foreach ($myReports as $report){
	
	$newurl = $report['url'];
	$newurl =str_replace("://","",$newurl);
	$urlParts = explode('/',$newurl,2);
	$newurl = $urlParts[1];
	
	$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.$newurl.'">'.$report['report_name'].'</a>';
	}
			
	}
		
		
$linkNameGroupCounter++;
	
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/admins.php?act=admins">'.lang('Management').'</a>';
	$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/merchants.php">'.lang('Merchants').'</a>';
		
		if (adminPermissionCheck('admins'))
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/admins.php?act=admins">'.lang('Admins').'</a>';		
		$linkName[$linkNameGroupCounter][] = '<a ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':'  href="'.$set->SSLprefix.'admin/admins.php?type=manager"').'>'.(  lang('Affiliate Managers') ).'</a>';
		if($set->showAdvertiserModule)
		$linkName[$linkNameGroupCounter][] = '<a ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':'  href="'.$set->SSLprefix.'admin/admins.php?type=advertiser"').'>'.(  lang('Advertisers') ).'</a>';
	
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/groups.php"').'>'.lang('Groups').'</a>';
		if ($set->showDynamicFilters==1 )
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/dynamic_filters.php"').'>'.lang('Dynamic Filters').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/mails.php">'.lang('E-mail Templates').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/notes.php"').'>'.lang('Manager Notes CRM').'</a>';
                $linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/exchange_rates.php">'.lang('Exchange Rates').'</a>';

                
	$linkNameGroupCounter++;
	
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/config.php">'.lang('Tools').'</a>';
		
		if ($set->userInfo['userType'] == "sys") $linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/translate.php">'.lang('Translations').'</a>';
	if (adminPermissionCheck('maintenance'))
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/maintenance.php"').'>'.lang('Maintenance').'</a>';
	if (adminPermissionCheck('configuration'))
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/config.php">'.lang('Configuration').'</a>';
		if (adminPermissionCheck('api'))
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/logs.php?act=cron"').'>'.lang('API Integration').'</a>';
	if (adminPermissionCheck('campaignrelation'))
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/affiliateCampaignRelation.php"').'>'.lang('Affiliate Campaign Relation').'</a>';
		if ($set->userInfo['userType'] == "sys")
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/design_layouts.php"').'>'.lang('Design').'</a>';
		
		if ($set->hidePendingProcessHighAmountDeposit == 0 &&  (adminPermissionCheck('pendingdeposit')))
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/pendingDepositsApproval.php"').'>'.lang('Pending Approval Deposits').'</a>';
	
	$linkNameGroupCounter++;
	
	$groupName[] = '<a href="#">'.lang('Security').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/loginhistory.php">'.lang('Login History').'</a>';
		if (adminPermissionCheck('permissions'))
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/permissions.php"').'>'.lang('Affiliates Permissions').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/users_firewall.php"').'>'.lang('Users Firewall').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/logactivity.php"').'>'.lang('Activity Log').'</a>';
		
		
		
	
	
	
	
	if(!$set->hideSubAffiliation){
	$linkNameGroupCounter++;
		if ($set->introducingBrokerInterface)
		$groupName[] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/sub.php?act=creative"').'>'.lang('Sub IB').'</a>';
		else
		$groupName[] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/sub.php?act=creative"').'>'.lang('Sub Affiliation').'</a>';

		
			if ($set->introducingBrokerInterface)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/sub.php"').'>' .lang('Sub IB').' ' .lang('Reports'). '</a>';
		else
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/sub.php"').'>'. lang('Sub Affiliation').' ' .lang('Reports'). '</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/sub.php?act=creative"').'>'.lang('Creative Materials').'</a>';

	}
		
		// $linkName[5][] = '<a href="/admin/sub.php?act=master">Master Affiliates Report</a>';
		// $linkName[5][] = '<a href="/admin/sub.php?act=sub">Sub Affiliates Report</a>';
	
	$openTickets = mysql_result(function_mysql_query("SELECT COUNT(id) FROM affiliates_tickets WHERE status='open' AND ticket_id='0'",__FILE__,__FUNCTION__),0);
	
	$linkNameGroupCounter++;
	
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/tickets.php">'.lang('Tickets').' ('.$openTickets.')</a>';
	$linkNameGroupCounter++;
	// die ($set->ProFeatureTooltip);
	$groupName[] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/billing.php"').'>'.lang('Billing').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/billing.php"').'>'.lang('Affiliate Billing').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'admin/tag.php"').'>'.lang(ptitle('Traders Tag')).'</a>';
	
	
	$linkNameGroupCounter++;
	
	$groupName[] = '<a href="'.$set->SSLprefix.'admin/help.php" target="_blank">Help</a>';
				$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/usermanual.php" target="_blank">'.lang('User Manual').'</a>';
				
				
	$linkNameGroupCounter++;
	
	if ($set->userInfo['id'] == "1") {
		$groupName[] = '<a href="'.$set->SSLprefix.'admin/logs.php">Developer</a>';
			$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/logs.php?act=cron">Cron Job</a>';
			$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/translate.php">'.lang('Translations').'</a>';
			$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/producttitles.php">'.lang('Products Titles').'</a>';
			$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/config.php">'.lang('Configuration').'</a>';
			$linkName[$linkNameGroupCounter][] = '<a href="'.$set->SSLprefix.'admin/fix.php?resetsort">'.lang('Reset Sort').'</a>';
		}
			
	
	for ($i=0; $i<=count($groupName)-1; $i++) {
		unset($secLevelMenu);
		if (count($linkName[$i]) > 0) {
			$secLevelMenu .= '<ul>';
			for ($b=0; $b<=count($linkName[$i])-1; $b++) {
				unset($subMenu);
				for ($c=0; $c<=count($SublinkName[$i][$b])-1; $c++) $subMenu .= '<li>'.$SublinkName[$i][$b][$c].'</li>';
				$secLevelMenu .= '<li'.($subMenu ? ' class="dir"' : '').'>'.$linkName[$i][$b];
				if ($subMenu) $secLevelMenu .= '<ul>'.$subMenu.'</ul>';
				$secLevelMenu .= '</li>';
				}
			$secLevelMenu .= '</ul>';
			}
		$listMenu .= '<li'.(count($linkName[$i]) > 0 ? ' class="dir"' : '').'>'.$groupName[$i];
		$listMenu .= $secLevelMenu;
		$listMenu .= '</li>';
		}
	
	$html .= '<ul id="nav" class="dropdown dropdown-horizontal">'.$listMenu.'</ul>';
	
	return $html;
	}
	
function chgGroup($affiliate_id=0,$group_id=0) {
	//$qq=function_mysql_query("SELECT name FROM merchants WHERE valid='1' ORDER BY pos",__FILE__,__FUNCTION__);
	//while ($ww=mysql_fetch_assoc($qq)) 
	{
		// $merchantName = strtolower($ww['name']);
		function_mysql_query("UPDATE data_reg SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
		function_mysql_query("UPDATE data_sales SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
		function_mysql_query("UPDATE data_sales_pending SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
		function_mysql_query("UPDATE data_stats SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
		}
	function_mysql_query("UPDATE affiliates SET group_id='".$group_id."' WHERE id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	function_mysql_query("UPDATE affiliates_notes SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	
	// function_mysql_query("UPDATE stats_banners SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	function_mysql_query("UPDATE traffic SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	}
?>