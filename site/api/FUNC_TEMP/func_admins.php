<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function isAdmin() {
	global $set,$_SESSION;
	$resulta=mysql_query("SELECT id,username,password FROM admins WHERE id='".$_SESSION['session_id']."' AND valid='1' AND level='admin'");
	$chk=mysql_fetch_assoc($resulta);
	$session_serial = md5($chk['username'].$chk['password'].$chk['id']);
	if ($_SESSION['session_id'] == $chk['id'] AND $session_serial == $_SESSION['session_serial']) return true;
	return false;
	}
	
function adminInfo() {
	global $set,$_SESSION;
	if (!isAdmin()) return false;
	$resulta=mysql_query("SELECT admins.id, admins.rdate, admins.ip, admins.chk_ip, admins.valid, admins.lang, admins.level, admins.username, admins.password, admins.first_name, admins.last_name, admins.email, admins.lastactive, admins.logged, admins.group_id, admins.phone, admins.IMUser, admins.zopimChat, admins.bigPic, merchants.producttype AS productType FROM admins LEFT JOIN merchants ON admins.relatedMerchantID=merchants.id WHERE admins.id='".$_SESSION['session_id']."' AND admins.valid='1'");
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
	
	$groupName[] = '<a href="/admin/">'.lang('DashBoard').'</a>';
	
	$groupName[] = lang('Affiliation Management');
		$linkName[1][0] = '<a href="/admin/affiliates.php?act=search">'.lang('Affiliates List').'</a>';
        $SublinkName[1][0][] = '<a href="/admin/affiliates.php?act=search">'.lang('Search Affiliates').'</a>';
		$SublinkName[1][0][] = '<a href="/admin/affiliates.php?act=new">'.lang('Add Affiliate').'</a>';
		$SublinkName[1][0][] = '<a href="/admin/affiliates.php?act=pending">'.lang('Pending Affiliates').'</a>';
		$linkName[1][] = '<a href="/admin/merchants.php">'.lang('Merchant Info').'</a>';
		$linkName[1][] = '<a href="/admin/languages.php">'.lang('Languages').'</a>';
	
	$groupName[] = lang('Marketing Tools');
		$linkName[2][] = '<a href="/admin/creative.php">'.lang('Creative Materials').'</a>';
		$linkName[2][] = '<a href="/admin/messages.php">'.lang('Messages').'</a>';
		
	$groupName[] = '<a href="/admin/reports.php">'.lang('Reports').'</a>';
		$linkName[3][] = '<a href="/admin/reports.php">'.lang('Quick Summary Report').'</a>';
		$linkName[3][] = '<a href="/admin/reports.php?act=traffic">'.lang('Traffic Report').'</a>';
		$linkName[3][] = '<a href="/admin/reports.php?act=banner">'.lang('Creative Report').'</a>';
		$linkName[3][] = '<a href="/admin/reports.php?act=trader">'.lang(ptitle('Trader Report')).'</a>';
		$linkName[3][] = '<a href="/admin/reports.php?act=affiliate">'.lang('Affiliate Report').'</a>';
		$linkName[3][] = '<a href="/admin/reports.php?act=group">'.lang('Groups Report').'</a>';
		
	$groupName[] = '<a href="/admin/admins.php?act=admins">'.lang('Management').'</a>';
		$linkName[4][] = '<a href="/admin/admins.php?act=admins">'.lang('Admins').'</a>';		
		$linkName[4][] = '<a href="/admin/admins.php?type=manager">'.lang('Affiliate Managers').'</a>';
		$linkName[4][] = '<a href="/admin/groups.php">'.lang('Groups').'</a>';
		$linkName[4][] = '<a href="/admin/mails.php">'.lang('E-mail Templates').'</a>';
		$linkName[4][] = '<a href="/admin/notes.php">'.lang('Affiliate Manager Notes').'</a>';
		if ($set->userInfo['id'] == "1") $linkName[4][] = '<a href="/admin/translate.php">'.lang('Translations').'</a>';
		$linkName[4][] = '<a href="/admin/config.php">'.lang('Configuration').'</a>';
		$linkName[4][] = '<a href="/admin/maintenance.php">'.lang('Maintenance').'</a>';
		$linkName[4][] = '<a href="/admin/logs.php?act=cron">API Integration</a>';
	
	$groupName[] = '<a href="/admin/sub.php?act=creative">'.lang('Sub Affiliation').'</a>';
		$linkName[5][] = '<a href="/admin/sub.php?act=creative">'.lang('Creative Materials').'</a>';
		// $linkName[5][] = '<a href="/admin/sub.php?act=master">Master Affiliates Report</a>';
		// $linkName[5][] = '<a href="/admin/sub.php?act=sub">Sub Affiliates Report</a>';
	
	$openTickets = mysql_result(mysql_query("SELECT COUNT(id) FROM affiliates_tickets WHERE status='open' AND ticket_id='0'"),0);
	
	$groupName[] = '<a href="/admin/tickets.php">'.lang('Tickets').' ('.$openTickets.')</a>';
	
	$groupName[] = '<a href="/admin/billing.php">'.lang('Billing').'</a>';
		$linkName[7][] = '<a href="/admin/billing.php">'.lang('Affiliate Billing').'</a>';
		$linkName[7][] = '<a href="/admin/tag.php">'.lang(ptitle('Traders Tag')).'</a>';
	if ($set->userInfo['id'] == "1") {
		$groupName[] = '<a href="/admin/logs.php">Developer</a>';
			$linkName[8][] = '<a href="/admin/logs.php?act=cron">Cron Job</a>';
			$linkName[8][] = '<a href="/admin/translate.php">'.lang('Translations').'</a>';
			$linkName[8][] = '<a href="/admin/producttitles.php">'.lang('Products Titles').'</a>';
			$linkName[8][] = '<a href="/admin/config.php">'.lang('Configuration').'</a>';
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
	$qq=mysql_query("SELECT name FROM merchants WHERE valid='1' ORDER BY pos");
	while ($ww=mysql_fetch_assoc($qq)) {
		$merchantName = strtolower($ww['name']);
		mysql_query("UPDATE data_reg_".$merchantName." SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'");
		mysql_query("UPDATE data_sales_".$merchantName." SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'");
		}
	mysql_query("UPDATE stats_banners SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'");
	mysql_query("UPDATE affiliates SET group_id='".$group_id."' WHERE id='".$affiliate_id."'");
	mysql_query("UPDATE affiliates_notes SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'");
	}
	
	/*
	function getTag($tag, $endtag, $xml) {
		if (!$endtag) $endtag=$tag;
		preg_match_all("/".$tag."(.*?)".$endtag."/", $xml, $matches);
		return $matches[1][0];
		}
		

	
	function doPost($url){
		$parse_url=parse_url($url);
		$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
		if (!$da) {
			echo "$errstr ($errno)<br/>\n";
			echo $da;
			} else {
			$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
			$params .= "Host: ".$parse_url['host']."\r\n";
			$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$params .= "User-Agent: BestForexPartners Agent\r\n";
			$params .= "Content-Length: ".strlen($parse_url['query'])."\r\n";
			$params .= "Connection: close\r\n\r\n";
			$params .= $parse_url['query'];
			fputs($da, $params);
			while (!feof($da)) $response .= fgets($da);
			fclose($da);
			
			// split the result header from the content
			$result = explode("\r\n\r\n", $response, 2);
			$content = isset($result[1]) ? $result[1] : '';
			return $content;
			}
		}

		
	function getUSD($price='0',$to='USD') {
		if (strtolower($to) == "usd") return $price;
		if (strtolower($to) == "rub") return round($price*0.03,2);
			else {
			$qq=mysql_query("SELECT rate FROM currencies WHERE lower(coin)='".strtolower($to)."'");
			$ww=mysql_fetch_assoc($qq);
			return round($price*$ww['rate'],2);
			}
		}
	
	function getSiteID($string="") {
		if (!$string) return false;
		$exp=explode("_",$string); // a_20b_115
		return substr($exp[1], 0, -1);
		}
	
	function setLog($text='',$site_id=0,$flag='green') {
		if (!$text) return false;
		$qq=mysql_query("SELECT id FROM logs WHERE text='".$text."' AND merchant_id='".$site_id."'");
		$ww=mysql_fetch_assoc($qq);
		if ($ww['id']) {
			updateUnit("logs","rdate='".dbDate()."'","id='".$ww['id']."'");
			return false;
			} else mysql_query("INSERT INTO logs (rdate,flag,merchant_id,text) VALUES ('".dbDate()."','".$flag."','".$site_id."','".mysql_escape_string($text)."')");
		}
	
	function btagValid($tag='') { // a_20b_100
		if (!$tag) return false;
		if ($tag==null OR $tag=='null' OR $tag=='NULL' OR trim($tag)==false) return false;
		$exp=explode("_",$tag);
		if ($exp[0] == "a" AND substr($exp[1],-1) == "b") return true;
		return false;
		}
	
	function ctagValid($tag='') { // a20-b100-p
		if (!$tag) return false;
		if ($tag==null OR $tag=='null' OR $tag=='NULL' OR trim($tag)==false) return false;
		$exp=explode("-",$tag);
		if (substr($exp[0],0,1) == "a" AND substr($exp[1],0,1) == "b") return true;
		return false;
		}
	
	function ctagMarketValid($tag='') { // a-20-b100-p
		if (!$tag) return false;
		if ($tag==null OR $tag=='null' OR $tag=='NULL' OR trim($tag)==false) return false;
		$exp=explode("-",$tag);
		if (substr($exp[0],0,1) == "a" AND substr($exp[2],0,1) == "b") return true;
		return false;
		}
	*/
?>