<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function isManager() {
	global $set,$_SESSION;
	$resulta=function_mysql_query("SELECT id,username,password FROM admins WHERE id='".$_SESSION['session_id']."' AND valid='1' AND level='manager'",__FILE__,__FUNCTION__);
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($_SESSION['session_id'] == $chk['id'] AND $session_serial == $_SESSION['session_serial']) {
		// if(((time() - $_SESSION['loggedin_time']) > $set->login_session_duration )){ 
		if(!isset($_SESSION['loggedin_time']) || (abs(time() - $_SESSION['loggedin_time']) < $set->login_session_duration *60)){
			$_SESSION['loggedin_time'] = time(); 
			return true; 
			return true; 
		} 
	}
	return false;
	}
	
function managerInfo() {
	global $set,$_SESSION;
	if (!isManager()) return false;
	$resulta=function_mysql_query("SELECT admins.id, admins.preferedCurrency,admins.rdate, admins.ip, admins.chk_ip, admins.valid, admins.lang, admins.level, admins.username, admins.password, admins.first_name, admins.last_name, admins.email, admins.lastactive, admins.logged, admins.group_id, admins.phone, admins.IMUser, admins.zopimChat, admins.bigPic, merchants.producttype AS productType FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE admins.id='".$_SESSION['session_id']."' AND admins.valid='1' AND admins.level='manager'",__FILE__,__FUNCTION__);
	//$resulta=function_mysql_query("SELECT admins.id, admins.rdate, admins.ip, admins.chk_ip, admins.valid, admins.lang, admins.level, admins.username, admins.password, admins.first_name, admins.last_name, admins.email, admins.lastactive, admins.logged, admins.group_id, admins.phone, admins.IMUser, admins.zopimChat, admins.bigPic, merchants.producttype AS productType FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE admins.id='".$_SESSION['session_id']."' AND admins.valid='1'");
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
	
	$groupName[] = '<a href="/manager/">'.lang('DashBoard').'</a>';
	
	
	$linkNameGroupCounter=1;
	$groupName[] = lang('Affiliates');
		$linkName[1][] = '<a href="/manager/affiliates_list.php?act=search">'.lang('Affiliates List').'</a>';
		// $SublinkName[1][0][0] = '<a href="/manager/affiliates.php?act=search">'.lang('Search Affiliates').'</a>';
		$linkName[1][] = '<a href="/manager/affiliates.php?act=new">'.lang('Add Affiliate').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="/manager/affiliates_list.php?act=pending">'.lang('Pending Affiliates').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="/manager/affiliates_list.php?act=search&logged=1">'.lang('Online Affiliates').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="/manager/affiliatesStatus.php">'.lang('Affiliates Category').'</a>';
		
		
			$linkNameGroupCounter=2;
			// $linkNameGroupCounter++;
	
	$groupName[] = '<a href="/manager/creative.php#tab_3">'.lang('Marketing Tools').'</a>';
		$linkName[$linkNameGroupCounter][0] = '<a href="/manager/creative.php#tab_3">'.lang('Creative Materials').'</a>';
		if ($set->AllowManagerEditrCreative==1)
		$SublinkName[$linkNameGroupCounter][0][] = '<a href="/manager/creative.php?new=1">'.lang('Add New').'</a>';
		$SublinkName[$linkNameGroupCounter][0][] = '<a href="/manager/creative.php#tab_3">'.lang('Browse').'</a>';
		if ($set->AllowManagerEditrCreative==1)
		$SublinkName[$linkNameGroupCounter][0][] = '<a href="/manager/creative.php?act=trash">'.lang('Recycle Bin').'</a>';
	if ($set->AllowManagerEditrCreative==1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/promotion.php"').'>'.lang('Promotions').'</a>';
		// $linkName[$linkNameGroupCounter][] = '<a href="/manager/languages.php">'.lang('Creative Languages').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':'  href="/manager/coupons.php"').'>'.lang('Coupons').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/creativeCategories.php"').'>'.lang('Categories').'</a>';
		 $linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':'  href="/manager/messages.php"').'>'.lang('Messages').'</a>';
		 $linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=ActiveCreatiesStats"').'>'.lang('Active Creatives Stats').'</a>';

		
		/* 
		
	$groupName[] = '<a href="/manager/creative.php">'.lang('Creative Materials').'</a>';
		$linkName[2][0] = '<a href="/manager/creative.php">'.lang('Search Creative Material').'</a>';
		$linkName[2][1] = '<a href="/manager/coupons.php">'.lang('Coupons').'</a>';
		$linkName[2][2] = '<a href="/manager/creativeCategories.php">'.lang('Categories').'</a>'; */
	
$linkNameGroupCounter=3;
	$groupName[] = '<a href="/manager/reports.php">'.lang('Reports').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/reports.php"').'>'.lang('Quick Summary Report').'</a>';

		//if (strpos($set->reportsToHide,'commissions_debts')<1)
		//$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=commissions_debts"').'>'.lang('Commissions Debts Report').'</a>';

		if (strpos($set->reportsToHide,'commission-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ). ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=commission"').'>'.lang('Commission Report').'</a>';
		if (strpos($set->reportsToHide,'traffic-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=clicks"').'>'.lang('Clicks Report').'</a>';
		if (strpos($set->reportsToHide,'clicks-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=traffic"').'>'.lang('Referral Report').'</a>';
		if (strpos($set->reportsToHide,'creative-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=creative"').'>'.lang('Creative Report').'</a>';
	if (strpos($set->reportsToHide,'landingp-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=LandingPage"').'>'.lang('Landing Pages Report').'</a>';
		if (strpos($set->reportsToHide,'country-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=country"').'>'.lang('Country Report').'</a>';
		if (strpos($set->reportsToHide,'trader-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a href="/manager/reports.php?act=trader">'.lang(ptitle('Trader Report')).'</a>';
		if (strpos($set->reportsToHide,'tansaction-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=transactions"').'>'.lang(ptitle('Transaction Report')).'</a>';
		if (strpos($set->reportsToHide,'tradersts-am')<1 && false)
		$linkName[$linkNameGroupCounter][] = '<a ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':'  href="/manager/reports.php?act=stats"').'>'.lang(ptitle('Trader Stats Report')).'</a>';
		if (strpos($set->reportsToHide,'affiliate-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':'  href="/manager/reports.php?act=affiliate"').'>'.lang('Affiliate Report').'</a>';
		
		if (strpos($set->reportsToHide,'profile-am')<1)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/reports.php?act=profile"').'>'.lang('Profile Report').'</a>';


	
	$myReports = getMyFavoritsReports();
		if (!empty($myReports)){
			
			$linkNameGroupCounter++;
	
	$groupName[] = '<a href="/manager/myFavorites.php">'.lang('My Reports').'</a>';
	$linkName[$linkNameGroupCounter][] = '<a href="/manager/myFavorites.php">'.lang('Manage Favorits Reports').'</a>';
	foreach ($myReports as $report){
	
	$newurl = $report['url'];
	$newurl =str_replace("://","",$newurl);
	$urlParts = explode('/',$newurl,2);
	$newurl = $urlParts[1];
	
	$linkName[$linkNameGroupCounter][] = '<a href="/'.$newurl.'">'.$report['report_name'].'</a>';
	}
			
	}
	

	
	/* 
	$groupName[] = '<a href="/manager/reports.php">'.lang('Reports').'</a>';
		if (strpos($set->reportsToHide,'quick-am')<1)
		$linkName[3][] = '<a href="/manager/reports.php">'.lang('Quick Summary Report').'</a>';
		if (strpos($set->reportsToHide,'traffic-am')<1)
		$linkName[3][] = '<a href="/manager/reports.php?act=traffic">'.lang('Traffic Report').'</a>';
		if (strpos($set->reportsToHide,'creative-am')<1)
		$linkName[3][] = '<a href="/manager/reports.php?act=creative">'.lang('Creative Report').'</a>';
		if (strpos($set->reportsToHide,'trader-am')<1)
		$linkName[3][] = '<a href="/manager/reports.php?act=trader">'.lang(ptitle('Trader Report')).'</a>';

		if (strpos($set->reportsToHide,'affiliate-am')<1)
		$linkName[3][] = '<a href="/manager/reports.php?act=affiliate">'.lang('Affiliate Report').'</a>';
		if (strpos($set->reportsToHide,'profile-am')<1)
		$linkName[3][] = '<a href="/manager/reports.php?act=profile">'.lang('Profile Report').'</a>';
	 */
		$linkNameGroupCounter ++;
	$groupName[] = '<a href="/manager/sub.php?act=creative">'.lang('Sub Affiliation').'</a>';
		$linkName[$linkNameGroupCounter][] = '<a href="/manager/sub.php?act=creative">'.lang('Creative Materials').'</a>';

	if ($set->introducingBrokerInterface)
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/sub.php"').'>' .lang('Sub IB').' ' .lang('Reports'). '</a>';
	else
		$linkName[$linkNameGroupCounter][] = '<a  ' . ($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/manager/sub.php"').'>'. lang('Sub Affiliation').' ' .lang('Reports'). '</a>';


		
	 	$linkNameGroupCounter ++;
	$openTickets = mysql_result(function_mysql_query("SELECT COUNT(id) FROM affiliates_tickets WHERE status='open' " . ($set->isNetwork ==1 ? " AND merchantID='".aesDec($_COOKIE['mid'])."' " : "")." AND ticket_id='0' AND group_id='".$set->userInfo['group_id']."'",__FILE__,__FUNCTION__),0);
	
		$groupName[] = lang('Management');
	$linkName[$linkNameGroupCounter][0] = '<a href="/manager/notes.php">'.lang('Manager Notes CRM').'</a>';
	$linkName[$linkNameGroupCounter][1] = '<a href="/manager/logs.php?act=search">'.lang('API Integration').'</a>';
	$linkName[$linkNameGroupCounter][2] = '<a href="/manager/affiliateCampaignRelation.php">'.lang('Affiliate Campaign Relation').'</a>';
		if ($set->isNetwork) {
	$linkName[$linkNameGroupCounter][3] = '<a href="/manager/merchants.php">'.lang('Merchants Info').'</a>';
		}
		
		
	$groupName[] = '<a href="/manager/tickets.php">'.lang('Tickets').' ('.$openTickets.')</a>';
	$groupName[] = '<a href="/manager/help.php">'.lang('help') .'</a>';

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
	
	function listManagerAffiliates($affiliate_id=0,$text=0,$category="") {
	global $set;
	if ($text) {
		$ww=mysql_fetch_assoc(function_mysql_query("SELECT id,username FROM affiliates WHERE valid='1' AND id='".$affiliate_id."'",__FILE__,__FUNCTION__));
		$html = $ww['username'];
		} else {
		$qq=function_mysql_query("SELECT id,username FROM affiliates WHERE valid='1' AND group_id=" . $set->userInfo['group_id'] . 
		($category!="" ? " AND status_id = " . $category :"") . 
		" ORDER BY username ASC",__FILE__,__FUNCTION__);
		while ($ww=mysql_fetch_assoc($qq)) $html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $affiliate_id ? 'selected' : '').'>'.$ww['username'].' [Site ID: '.$ww['id'].']</option>';
		}
	return $html;
	}
?>