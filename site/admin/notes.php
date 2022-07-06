<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'affiliates_notes';
$pageTitle = lang('Managers Notes');
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
switch ($act) {
	default:
		$from = strTodate($from).' 00:00:00';
		$to = strTodate($to).' 23:59:59';
		if ($status) $where = " AND status='".$status."'";
		if (!$sortBy) $sortBy = 'rdate';
		$qq=function_mysql_query("SELECT * FROM ".$appTable." WHERE ".$sortBy." BETWEEN '".$from."' AND '".$to."' ".$where." ORDER BY ".$sortBy." DESC",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			unset($bgColor);
			$adminInfo = dbGet($ww['admin_id'],"admins");
			$affiliateInfo = dbGet($ww['affiliate_id'],"affiliates");
			$groupInfo = dbGet($affiliateInfo['group_id'],"groups");
			if ($ww['status'] == "inprocess") $bgColor = 'style="background: #d4deff;"';
				else if ($ww['status'] == "closed") $bgColor = 'style="background: #d4ffdd;"';
			if ($ww['issue_date'] < dbDate() AND $ww['status'] != "closed") $bgColor = 'style="background: #ffd4d4;"';
			$noteList .= '<tr '.($l % 2 ? 'class="trLine"' : '').' '.$bgColor.'>
						<td>'.$ww['id'].'</td>
						'.($excel ? '' : '<td><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'&note_id='.$ww['id'].'#notesPlace" target="_blank">'.lang('Edit').'</a></td>').'
						<td>'.dbDate($ww['rdate']).'</td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/admins.php?act=new&id='.$adminInfo['id'].'" target="_blank">'.$adminInfo['first_name'].' '.$adminInfo['last_name'].'</a></td>
						<td>'.dbDate($ww['issue_date']).'</td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$ww['affiliate_id'].'</a></td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$ww['affiliate_id'].'" target="_blank">'.$affiliateInfo['username'].'</a></td>
						<td align="center">'.($groupInfo['id'] ? $groupInfo['title'] : lang('General')).'</td>
						<td align="center">'.round(floor((strtotime($ww['closed_date'] == "0000-00-00 00:00:00" ? dbDate() : $ww['closed_date'])-strtotime($ww['rdate']))/(60*60*24))+1).' '.lang('Day(s)').'</td>
						<td align="center">'.$ww['notes'].'</td>
						<td align="center"><b>'.lang(strtoupper($ww['status'])).'</b></td>
					</tr>';
			$l++;
			}
		$set->content = '
		<form method="get">
			<table><tr>
				<td>'.timeFrame($from,$to).'</td>
				<td><b>'.lang('Status').':</b> <select name="status">
				<option value="">'.lang('All').'</option>
				<option value="open" '.($status == "open" ? 'selected="selected"' : '').'>'.lang('Open').'</option>
				<option value="inprocess" '.($status == "inprocess" ? 'selected="selected"' : '').'>'.lang('In Process').'</option>
				<option value="closed" '.($status == "closed" ? 'selected="selected"' : '').'>'.lang('Closed').'</option>
			</select></td>
				<td><b>'.lang('Sort By').':</b> <select name="sortBy">
					<option value="rdate" '.($sortBy == "rdate" ? 'selected="selected"' : '').'>'.lang('Created Date').'</option>
					<option value="issue_date" '.($sortBy == "issue_date" ? 'selected="selected"' : '').'>'.lang('Due Date').'</option>
				</select></td>
				<td><input type="submit" value="'.lang('View').'" /></td>
				'.($set->export ? '<td><div class="exportCSV" style="float:left"><a href="'.$set->SSLprefix.$set->uri.(strpos($set->SSLprefix.$set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
					<div class="exportCSV" style="float:left"><a href="'.$set->SSLprefix.$set->uri.(strpos($set->SSLprefix.$set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
					</div><div style="clear:both"></div></td>' : '').'
			</tr></table>
		</form>
		<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Managers Notes').'</div>';
						$tableStr='<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								'.($excel ? '' : '<td align="center">'.lang('Actions').'</td>').'
								<td align="center">'.lang('Created Date').'</td>
								<td align="center">'.lang('Added By').'</td>
								<td align="center">'.lang('Due Date').'</td>
								<td align="center">'.lang('Affiliate ID').'</td>
								<td align="center">'.lang('Affiliate Username').'</td>
								<td align="center">'.lang('Affiliate Group').'</td>
								<td align="center">'.lang('Processing Time').'</td>
								<td align="center">'.lang('Notes').'</td>
								<td align="center">'.lang('Status').'</td>
							</tr></thead><tfoot>'.$noteList.'</tfoot>
						</table>';
						$set->content.=$tableStr;
						excelExporter($tableStr,'Affiliate_manager_notes');
		theme();
		break;
	
	case "xml":
		$from = strTodate($from).' 00:00:00';
		$to = strTodate($to).' 23:59:59';
		if ($status) $where = " AND status='".$status."'";
		if (!$sortBy) $sortBy = 'rdate';
		$qq=function_mysql_query("SELECT * FROM ".$appTable." WHERE ".$sortBy." BETWEEN '".$from."' AND '".$to."' ".$where." ORDER BY ".$sortBy." DESC",__FILE__);
		
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('ID'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Created Date'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Added By'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Due Date'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Affiliate ID'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Affiliate Username'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Affiliate Group'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Processing Time'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Notes'));
		$csvContent[0][] = iconv('UTF-8', 'windows-1255', lang('Status'));
		$i=1;
		while ($ww=mysql_fetch_assoc($qq)) {
			$adminInfo = dbGet($ww['admin_id'],"admins");
			$affiliateInfo = dbGet($ww['affiliate_id'],"affiliates");
			$groupInfo = dbGet($affiliateInfo['group_id'],"groups");
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['id']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', dbDate($ww['rdate']));
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $adminInfo['first_name'].' '.$adminInfo['last_name']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', dbDate($ww['issue_date']));
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $ww['affiliate_id']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', $affiliateInfo['username']);
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', ($groupInfo['id'] ? $groupInfo['title'] : lang('General')));
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', round(floor((strtotime($ww['closed_date'] == "0000-00-00 00:00:00" ? dbDate() : $ww['closed_date'])-strtotime($ww['rdate']))/(60*60*24))+1).' '.lang('Day(s)'));
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', str_replace(","," ",$ww['notes']));
			$csvContent[$i][] = iconv('UTF-8', 'windows-1255', strtoupper($ww['status']));
			
			$i++;
			}
		$fileName = $set->SSLprefix."admin/csv/report.csv";
		$openFile = fopen($fileName, 'w'); 
		// fwrite($openFile, $csvContent); 
		fclose($openFile); 
		header("Expires: 0");
		header("Pragma: no-cache");
		header("Content-type: application/ofx");
		header("Content-Disposition: attachment; filename=".$fileName);
		for ($i=0; $i<=count($csvContent)-1; $i++) echo implode(",",$csvContent[$i])."\n";
		die();
		break;
	
	}

?>