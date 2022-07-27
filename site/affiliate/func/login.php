<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */
function isLogin() {
	global $set,$_SESSION;
	
	/* 	$time = ": " . time();
		echo 'time: ' . $time .'<br>';
		echo 'session time: ' . $_SESSION['loggedin_time'] .'<br>';
		echo 'set->login_session_duration: ' . $set->login_session_duration .'<br>';
		die();
		 */
	
	
	$logged_in_affiliate_id = $set->userInfo['id'];
	$logged_in_affiliate_allowed_merchants = $set->userInfo['merchants'];
	
	
	if (!isset($logged_in_affiliate_id) || empty(($logged_in_affiliate_id))){
		$refe = ($set->refe);
		$host = (parse_url($refe));
		
		
		// when logged-in as affiliate
		if ((strtolower($host['host']) == strtolower($_SERVER['HTTP_HOST'])) && (!empty($refe)) && strpos($refe,'/affiliates.php?act=new&id=')>0){
			
	
			$affiliate_id_from_refe = explode('id=',$refe);
			$aff_id_from_refe = $affiliate_id_from_refe[1];
			if (!empty($aff_id_from_refe) && is_numeric($aff_id_from_refe)){
				$logged_in_affiliate_id = $affiliate_id_from_refe[1];
				
				if($set->captureAffiliateLogs){
					if ($set->outputLoginLogs==1 ) writeToLog("8. isLogin() - from Reference - " .$refe);
					if ($set->outputLoginLogs==1 ) writeToLog("9. isLogin() - aftre captured from Reference - logged_in_affiliate_id - " .$logged_in_affiliate_id);
				}
				
				$affiliate_id_from_refe = explode('&',$logged_in_affiliate_id);
				$logged_in_affiliate_id = $affiliate_id_from_refe[0];
				
				$affRow = getAffiliateRow($logged_in_affiliate_id);
				$logged_in_affiliate_allowed_merchants=$affRow['merchants'];
				$set->userInfo['id'] = $logged_in_affiliate_id;
			
				$set->userInfo['merchants'] = $logged_in_affiliate_allowed_merchants;
				$set->userInfo['group_id'] = $affRow['group_id'];
			
				
			}
			/* else
				$logged_in_affiliate_id = -99; */
		}
}

	if($set->captureAffiliateLogs){
					if ($set->outputLoginLogs==1 ) writeToLog("12. isLogin() - set->userInfo['id'] - " .$set->userInfo['id']);
					if ($set->outputLoginLogs==1 ) writeToLog("13. isLogin() - SESSION['aff_session_id'] - " .$_SESSION['aff_session_id']);
	}
	$sql= "SELECT id,username,password FROM affiliates WHERE id='".$_SESSION['aff_session_id']."' AND valid='1'";
	//die($sql);
	$resulta=function_mysql_query($sql,__FILE__,__FUNCTION__);
	$chk=mysql_fetch_assoc($resulta);
	
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($_SESSION['aff_session_id'] == $chk['id'] AND $session_serial == $_SESSION['aff_session_serial']) return true;

	
	return false;
	}




function affiliateInfo() {
	global $set,$_SESSION;
	if (!isLogin()) return false;
	
	if($set->captureAffiliateLogs){
					if ($set->outputLoginLogs==1 ) writeToLog("3. affiliateInfo() - aff_session_id - " .$_SESSION['aff_session_id']);
					if ($set->outputLoginLogs==1 ) writeToLog("4. affiliateInfo() - set->userInfo['id'] - " .$set->userInfo['id']);
	}
	
	$resulta=function_mysql_query("SELECT * FROM affiliates WHERE id='".$_SESSION['aff_session_id']."' AND valid='1'",__FILE__,__FUNCTION__);
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($session_serial == $_SESSION['aff_session_serial']) return $chk;
	return false;
	}
	
function adminMenu($onlyArray = false) {
	global $set,$_SESSION;
	
	
	
	$merchantIDs = ($set->userInfo['merchants']);
		$noMerchants = 0;
		if ($merchantIDs=='' || empty($merchantIDs))
		$noMerchants = 1;
		
		
		
	$groupName = Array();
	$linkName = Array();
	
	$counter=0;
	$groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/">'.lang('DashBoard').'</a>';
	
	
	if ($noMerchants==0){
		$counter++;
		$groupName[] = '<a href="'.$set->SSLprefix.'affiliate/creative.php">'.lang('Marketing Tools').'</a>';
		if (strpos(' '.$set->menuToHide,'subaffiliates')<1) {
			$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/creative.php"').'>'.lang('Creative Materials').'</a>';
			$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/sub.php?act=creative"').'>'.lang('Sub Affiliate Creatives').'</a>';
		}
	
	
	
	$counter++;
	$groupName[] = '<a href="'.$set->SSLprefix.'affiliate/reports.php">'.lang('Reports').'</a>';
	
		// if (strpos($set->reportsToHide,'quick')<1)
		if (allowView('af-quick',$deal,'reports'))
		$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php"').'>'.lang('Quick Summary Report').'</a>';
			
	// 		if (allowView('af-comm',$deal,'reports'))
	// 	$linkName[$counter][] = '<a '.($set->isBasicVer ==1  && false ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=commission"').'>'.lang('Commission Report').'</a>';
	
	// 	if (allowView('af-clicks',$deal,'reports'))
	// 	$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=clicks"').'>'.lang('Clicks Report').'</a>';
	// 	if (allowView('af-creative',$deal,'reports'))
	// 	$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=banner"').'>'.lang('Creative Report').'</a>';
	// if (allowView('af-landing',$deal,'reports'))
	// 	$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=landingPage"').'>'.lang('Landing Page Report').'</a>';
	// 	if (allowView('af-trader',$deal,'reports'))
	// 	$linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader">'.lang(ptitle('Trader Report')).'</a>';
	
	// if (allowView('af-trnsct',$deal,'reports')){
	//     if($set->userInfo['id'] == 1302){
	// 	    $linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=transactions"').'>'.lang(ptitle('Transactions Report')).'</a>';
	//     }		
	// }
	// 	$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=pixelsLogs"').'>'.lang(ptitle('pixels Logs Report')).'</a>';
		
	
	
	// if (allowView('|af-install',$deal,'reports'))
	// 	$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=install"').'>'.lang(ptitle('Install Report')).'</a>';
	
	
	// 	if (allowView('af-profile',$deal,'reports'))
	// 	$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=profile"').'>'.lang('Profile Report').'</a>';
	
	
		if(!$set->hideSubAffiliation){
		if (strpos(' '.$set->menuToHide,'subaffiliates')<1) {
		
		$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/reports.php?act=sub"').'>'.lang('Sub Affiliates Report').'</a>';
		}
	//	$counter++;
	//$groupName[$counter] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/affiliate/sub.php"').'>'.lang('Sub Affiliates').'</a>';
	//	$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/affiliate/sub.php"').'>'.lang('Sub Reports').'</a>';
	
	}
	}
		$myReports = getMyFavoritsReports();
		if (!empty($myReports)){
			
			$linkNameGroupCounter++;
	$counter++;
	$groupName[] = '<a href="'.$set->SSLprefix.'affiliate/myFavorites.php">'.lang('My Reports').'</a>';
	$linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/myFavorites.php">'.lang('Manage Favorits Reports').'</a>';
	foreach ($myReports as $report){
	
	$newurl = $report['url'];
	$newurl =str_replace("://","",$newurl);
	$urlParts = explode('/',$newurl,2);
	$newurl = $urlParts[1];
	
	$linkName[$counter][] = '<a href="'.$set->SSLprefix.$newurl.'">'.$report['report_name'].'</a>';
	}
			
	}
		
		/* $linkName[2][] = '<a href="/affiliate/reports.php?act=traffic">'.lang('Traffic Report').'</a>';
		if (strpos($set->reportsToHide,'creative')<1) */

	if ($set->showProductsPlace==1) {
		$counter++;
	$groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/products.php?featured=1">'.lang('Markets Place').'</a>';
        $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/products.php?featured=1">'.lang('Creatives & Tracking').'</a>';
        $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/product_reports.php">'.lang('Reports').'</a>';
	
	}
	$counter++;
	$groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/profiles.php">'.lang('Profiles').'</a>';
	/* if (strpos(' '.$set->menuToHide,'subaffiliates')<1) {
		$counter++;
	$groupName[$counter] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/affiliate/sub.php"').'>'.lang('Sub Affiliates').'</a>';
		$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/affiliate/sub.php"').'>'.lang('Sub Reports').'</a>';
	
		$linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="/affiliate/sub.php?act=creative"').'>'.lang('Creative Materials').'</a>';
	} */
	
	

	$counter++;
	$groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/account.php">'.lang('My Account').'</a>';
        $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/account.php">'.lang('Account Details').'</a>';
		$linkName[$counter][] = '<a href="#">'.lang('Document').'</a>';
        if (!empty($set->showDocumentsModule)) {
            $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/documents.php">'.lang('Documents').'</a>';
        }
        
        $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/account.php?act=payment&id='.$set->userInfo['id'].'">'.lang('Payment Method Details').'</a>';
        $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/account.php?act=commission">'.lang('Commission Structure').'</a>';
        // $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/billing.php">'.lang('Billing').'</a>';
        // $linkName[$counter][] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/PixelMonitor.php?act=new"').'>'.lang('Pixel Monitor').'</a>';
		// $linkName[$counter][] = '<a href="'.$set->terms_link.'" target="_blank">'.lang('Terms and Conditions').'</a>';
		
		// $counter++;
		// $groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/messages.php">'.lang('Messages').'</a>';
		
		$counter++;
		$groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/billing.php">'.lang('Billing').'</a>';

		$counter++;
		$groupName[$counter] = '<a '.($set->isBasicVer ==1 ? $set->ProFeatureTooltip : '' ).  ($set->isBasicVer ==1 ?'':' href="'.$set->SSLprefix.'affiliate/PixelMonitor.php?act=new"').'>'.lang('Pixel Monitor').'</a>';

		$counter++;
		$groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/terms_condition.php">'.lang('Terms and Conditions').'</a>';
	
		// if($set->isNetwork){
		$openTickets = mysql_result(function_mysql_query("SELECT COUNT(id) FROM affiliates_tickets WHERE status='open' AND ticket_id='0' AND affiliate_id='".$set->userInfo['id']."'",__FILE__,__FUNCTION__),0);
		$counter++;
		// $groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/tickets.php">'.lang('Support').' ('.$openTickets.')</a>';
		$groupName[$counter] = '<a href="'.$set->SSLprefix.'affiliate/tickets.php">'.lang('Support').'</a>';
	// }else{
		// $groupName[] = '<a href="/affiliate/tickets.php">'.lang('Support').'</a>';
	// }
		// $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/tickets.php?act=new">'.lang('New Ticket').'</a>';
		// $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/tickets.php">'.lang('Search Ticket').'</a>';
		// $linkName[$counter][] = '<a href="'.$set->SSLprefix.'affiliate/faq.php">'.lang('FAQ').'</a>';
	
		$counter++;
		$groupName[$counter] = '<a href="/affiliate/?act=logout">'.lang('Logout').'</a>';
        if($onlyArray){
            return [
                'group' => $groupName,
                'list' => $linkName
            ];
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
				$secLevelMenu .= '</li>'.($subMenu ? '</ul>' : '');
				}
			$secLevelMenu .= '</ul>';
			}
		$listMenu .= '<li'.(count($linkName[$i]) > 0 ? ' class="dir"' : '').' '.($i == "0" ? 'style="background: #FFF;"' : '').'>'.$groupName[$i];
		$listMenu .= $secLevelMenu;
		$listMenu .= '</li>';
		}
	
	$html .= '<ul id="nav" class="dropdown dropdown-horizontal">'.$listMenu.'</ul>';
	return $html;
	}
	
	
	
	
function allowView($viewName,$dealTypeRelated,$location ='fields',$isDebug=false){
	global $set;//,$_SESSION;

	
			 	 if ($_GET['dddd']==1 ) {
	var_dump($set->userInfo);
	echo '<Br><Br>';
	
	$isDebug=1;
	 	 }
		 
		 
	
	
	if ($isDebug) {
			$pram =  "viewName: " . $viewName . '<Br>';
			$pram .= "dealTypeRelated: " . $dealTypeRelated . '<Br>';
			$pram .= "location: " . $location . '<Br>';
		echo ($pram).'<br>';;
	}
	
	 $dealView = $set->userInfo['PermissionProfiles'];//[strtoupper($dealTypeRelated)];
	 // var_dump($dealView);
	 // die();
	 $permissionProfile = array();
	
	 if ($set->userInfo['profilePermissionID']==-1) {
		 foreach ($dealView as $dv) {
			 
		if ($isDebug) {
				$pram =  '<Br>';
				$pram =  '<Br>';
				$pram =  "dv: " . $dv . '<Br>';
				// var_dump($db);
				$pram .= "dealTypeRelated: " . $dealTypeRelated . '<Br>';
				
			echo ($pram).'<br>';;
		}
		
			 
			 if ($dv['defaultViewForDealType']!=strtoupper($dealTypeRelated)) {
					continue;
			 }else {
				 $permissionProfile = $dv;
				 break;
			 }
		 }
				 
				
		 
	}
	else {
				 $permissionProfile =  $set->userInfo['PermissionProfiles'];
	}
	
	if (count($dealView)==1){
	reset($dealView);
$key = key($dealView);

$dealView = ($dealView[$key]);
	}
	
	
	 
	 
	
		 if (!isset($dealView) || $permissionProfile==null) {
			$dealView = $set->userInfo['PermissionProfiles'][0];
		 }
		
	 

	if ($location=='fields') {
	$attributes = $dealView['fieldsPermissions'];
	 		if ($isDebug==1) {
				echo 'att: ' . $attributes .'<br>viewname: ' . $viewName;
				// die();
			}
	
	
		$exp = explode('|',$attributes);
		if (in_array('-'.$viewName, $exp)){
		// if (strpos($attributes,'|-'.$viewName.'|')!==false) {
			if ($isDebug==1) {
				
			echo '0deal: ' . $dealView['name'] . '<br>';
			echo '0name: ' . $viewName . '       false<br>';
			}
			return false;
		}
		if (in_array($viewName, $exp)){
		// if (strpos($attributes,'|'.$viewName.'|')!==false) {
			
			
			if ($isDebug==1) {
			echo '1deal: ' . $dealView['name'] . '<br>';
				echo '1name: ' . $viewName . '       true<br>';
			}
			return true;
		}
	}
	elseif ($location=='reports') {
	$exp = explode('|',$attributes);
	
	// $attributes = $dealView['reportsPermissions'];
	 		if ($isDebug==1) {
				echo 'att: ' . $attributes .'<br>viewname: ' . $viewName;
				// die();
			}
		if (in_array('-'.$viewName, $exp)){
		// if (strpos($attributes,'|-'.$viewName.'|')!==false || strpos($attributes,'-'.$viewName)!==false) {
			if ($isDebug==1) {
				
			echo '0deal: ' . $dealView['name'] . '<br>';
			echo '0name: ' . $viewName . '       false<br>';
			}
			return false;
		}
		if (in_array($viewName, $exp)){
		// if (strpos($attributes,'|'.$viewName.'|')!==false) {
			
			
			if ($isDebug==1) {
			echo '1deal: ' . $dealView['name'] . '<br>';
				echo '1name: ' . $viewName . '       true<br>';
			}
			return true;
		}
	}
		
		return true;
	// var_dump( $dealView);
	// die();

}

function getLowestLevelDeal ($currentLevel, $levelToCheck) {
	// die ($currentLevel); //ALL
	// die ($levelToCheck); //cpa
	
	if ($levelToCheck=='ALL' || $levelToCheck=='')
		$levelToCheck= 7;
	else if ($levelToCheck=='REV')
		$levelToCheck= 5;
	else if ($levelToCheck=='DCPA')
		$levelToCheck= 4;
	else if ($levelToCheck=='CPA')
		$levelToCheck= 3;
	else if ($levelToCheck=='CPL')
		$levelToCheck= 2;
	else if ($levelToCheck=='CPC')
		$levelToCheck= 1;
	
	
	if ($currentLevel=='ALL' || $currentLevel=='')
		$currentLevel= 7;
	else if ($currentLevel=='REV')
		$currentLevel= 5;
	else if ($currentLevel=='DCPA')
		$currentLevel= 4;
	else if ($currentLevel=='CPA')
		$currentLevel= 3;
	else if ($currentLevel=='CPL')
		$currentLevel= 2;
	else if ($currentLevel=='CPC')
		$currentLevel= 1;

	// die ('a: ' . $currentLevel);
	
	if ($levelToCheck<$currentLevel){
		$currentLevel= $levelToCheck;
	}
	
	
	
	if ($currentLevel==7)
		$currentLevel= 'ALL';
	else if ($currentLevel==5)
		$currentLevel= 'REV';
	else if ($currentLevel==4)
		$currentLevel= 'DCPA';
	else if ($currentLevel==3)
		$currentLevel= 'CPA';
	else if ($currentLevel==2)
		$currentLevel= 'CPL';
	else if ($currentLevel==1)
		$currentLevel= 'CPC';


	return $currentLevel;
}
	
function getPermissions() {
	global $set,$_SESSION;
	$profile = $set->userInfo['profilePermissionID'];
	
	$profiles = array();
	if ($profile>-1) {
		
			$qry = "select * from permissionProfile where id = ".  $set->userInfo['profilePermissionID'] . ' and valid=1 limit 1;';
			$row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
			// var_dump($row);
			// die();
			if ($row['id']>-1)
				$profiles[$row['id']] = $row;
		}
		else {
				/* 	$qry = "select * from permissionProfile where id = 0 limit 1;";
					$row = mysql_fetch_assoc(function_mysql_query($qry));
					if ($row['id']>-1)
						$profiles[$row['id']] = $row;
	} */
	
	// var_dump($profiles);
	// die ($qry);
	$where  = " and valid=1 and affiliate_id in (0" . (!empty($set->userInfo['id']) ? ",". $set->userInfo['id'] : "") . ") order by id desc  ";




	if (!isset($row[0])) {
		$qry = "select * from permissionProfile where defaultViewForDealType='DCPA' ".$where." limit 1;";
		$row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
		if ($row['id']>0)
			$profiles['DCPA'] = $row;
	}
	if (!isset($row[0])) {
		$qry = "select * from permissionProfile where defaultViewForDealType='CPA'  ".$where."  limit 1;";
		$row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
		if ($row['id']>0)
			$profiles['CPA'] = $row;
	}
		if (!isset($row[0])) {
		$qry = "select * from permissionProfile where defaultViewForDealType='CPL'  ".$where."  limit 1;";
		$row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
		if ($row['id']>0)
			$profiles['CPL'] = $row;
	}
		if (!isset($row[0])) {
		$qry = "select * from permissionProfile where defaultViewForDealType='REV'  ".$where."  limit 1;";
		$row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
		if ($row['id']>0)
			$profiles['REV'] = $row;
	}
}
	
	// var_dump($profiles);
	// die('===');
	if (empty($profiles)){
		
					$qry = "select * from permissionProfile where id = 0 limit 1;";
					$row = mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
					if ($row['id']>0) {
						
						$profiles[$row['id']] = $row;
					}
	}
	
	$set->userInfo['PermissionProfiles'] = $profiles;
	
	
	
	
	
	$merchantqq = function_mysql_query("select * from merchants",__FILE__,__FUNCTION__);
		$dealsArray = array();
        while ($merchantww = mysql_fetch_assoc($merchantqq)) {
			$deal = AffiliateDealType($merchantww['id'],$arrDealTypeDefaults);
			$dealsArray[$merchantww['id']] = $deal;
		}
	
	$set->userInfo['dealsArray'] = $dealsArray;

$hasRevDeal = false;
foreach($dealsArray as $deal){
	if (strtolower($deal)=='rev'){
		$hasRevDeal=true;
	}
}
	
$set->userInfo['hasRevDeal'] = $hasRevDeal;
}


?>
