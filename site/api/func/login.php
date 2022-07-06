<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function isLogin() {
	global $set,$_SESSION;
	$resulta=mysql_query("SELECT id,username,password FROM affiliates WHERE id='".$_SESSION['aff_session_id']."' AND valid='1'");
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($_SESSION['aff_session_id'] == $chk['id'] AND $session_serial == $_SESSION['aff_session_serial']) return true;
	return false;
	}
	
function affiliateInfo() {
	global $set,$_SESSION;
	if (!isLogin()) return false;
	$resulta=mysql_query("SELECT * FROM affiliates WHERE id='".$_SESSION['aff_session_id']."' AND valid='1'");
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($session_serial == $_SESSION['aff_session_serial']) return $chk;
	return false;
	}
	
function adminMenu() {
	global $set,$_SESSION;
	
	$groupName = Array();
	$linkName = Array();
	
	$groupName[] = '<a href="/affiliate/" style="color: #000000;">'.lang('DashBoard').'</a>';
	
	$groupName[] = '<a href="/affiliate/creative.php">'.lang('Marketing Tools').'</a>';
	
	$groupName[] = '<a href="/affiliate/reports.php">'.lang('Reports').'</a>';
		$linkName[2][] = '<a href="/affiliate/reports.php">'.lang('Quick Summary Report').'</a>';
		$linkName[2][] = '<a href="/affiliate/reports.php?act=traffic">'.lang('Traffic Report').'</a>';
		$linkName[2][] = '<a href="/affiliate/reports.php?act=banner">'.lang('Creative Report').'</a>';
		$linkName[2][] = '<a href="/affiliate/reports.php?act=trader">'.lang('Trader Report').'</a>';

	$groupName[] = '<a href="/affiliate/profiles.php">'.lang('Profiles').'</a>';
	$groupName[] = '<a href="/affiliate/sub.php">'.lang('Sub Affiliates').'</a>';
		$linkName[4][] = '<a href="/affiliate/sub.php">'.lang('Sub Reports').'</a>';
		$linkName[4][] = '<a href="/affiliate/sub.php?act=creative">'.lang('Creative Materials').'</a>';
	
	$groupName[] = '<a href="/affiliate/account.php">'.lang('My Account').'</a>';
		$linkName[5][] = '<a href="/affiliate/account.php">'.lang('Account Details').'</a>';
		$linkName[5][] = '<a href="/affiliate/account.php?act=payment">'.lang('Payment Method Details').'</a>';
		$linkName[5][] = '<a href="/affiliate/account.php?act=commission">'.lang('Commission Structure').'</a>';
		$linkName[5][] = '<a href="/affiliate/billing.php">'.lang('Billing').'</a>';
	
	$groupName[] = '<a href="/affiliate/tickets.php">'.lang('Support').'</a>';
		$linkName[6][] = '<a href="/affiliate/tickets.php?act=new">'.lang('New Ticket').'</a>';
		$linkName[6][] = '<a href="/affiliate/tickets.php">'.lang('Search Ticket').'</a>';
		
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
?>