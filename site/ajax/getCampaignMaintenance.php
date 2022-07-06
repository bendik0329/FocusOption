<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
require '../common/database.php';
require_once '../func/func_string.php';

/**
 * Load merchants info dynamically via ajax call.
 */
 
 
 $merchant_id = isset($_GET['merchant_id']) ? $_GET['merchant_id'] : ( isset($_POST['merchant_id']) ? $_POST['merchant_id'] : "");

	$campaignsArrayMismatch = array();
		$sql = "select count(id) as cnt ,campaign_id, 'data_sales' as type from data_sales where affiliate_id = 500 and (campaign_id > 0 OR campaign_id != '')  " . (!empty($merchant_id) ? ' and merchant_id = "' . $merchant_id .'" ' : '' )." group by campaign_id";
		$rsc = function_mysql_query($sql,__FILE__);
		while ($crow = mysql_fetch_assoc($rsc)){
			$campaignsArrayMismatch[$crow['campaign_id']] += $crow['cnt'];
		}
		unset($rsc);
		$sql = "select count(id) as cnt ,campaign_id, 'data_reg' as type from data_reg where affiliate_id = 500 and (campaign_id > 0 OR campaign_id != '')  " . (!empty($merchant_id) ? ' and merchant_id = "' . $merchant_id .'" ' : '' )." group by campaign_id";
		$rsc = function_mysql_query($sql,__FILE__);
		while ($crow = mysql_fetch_assoc($rsc)){
			$campaignsArrayMismatch[$crow['campaign_id']] += $crow['cnt'];
		}
		unset($rsc);
		$sql = "select count(id) as cnt ,campaign_id, 'data_stats' as type from data_stats where affiliate_id = 500 and (campaign_id > 0 OR campaign_id != '')  " . (!empty($merchant_id) ? ' and merchant_id = "' . $merchant_id .'" ' : '' )." group by campaign_id";
		$rsc = function_mysql_query($sql,__FILE__);
		while ($crow = mysql_fetch_assoc($rsc)){
			$campaignsArrayMismatch[$crow['campaign_id']] += $crow['cnt'];
		}
		unset($rsc);
		$sql = "select count(id) as cnt ,campaign_id, 'data_sales_pending' as type from data_stats where affiliate_id = 500 and (campaign_id > 0 OR campaign_id != '')  " . (!empty($merchant_id) ? ' and merchant_id = "' . $merchant_id .'" ' : '' )." group by campaign_id";
		$rsc = function_mysql_query($sql,__FILE__);
		while ($crow = mysql_fetch_assoc($rsc)){
			$campaignsArrayMismatch[$crow['campaign_id']] += $crow['cnt'];
		}
		unset($rsc);
		
		
	/* 	
		$sql = "select distinct(campaign_id) from data_reg where affiliate_id=500 and not campaign_id = '' union 
					  select distinct(campaign_id) from data_sales where affiliate_id=500 and not campaign_id = '' union
					  select distinct(campaign_id) from data_stats where affiliate_id=500 and not campaign_id = '' union
					  select distinct(campaign_id) from data_sales_pending where affiliate_id=500 and not campaign_id = '' ORDER BY `campaign_id` ASC";
		
		
		$campaignsqq = function_mysql_query($sql,__FILE__);
		$maintenanceStr = "";
		$l=0;
		while($campaignsww  = mysql_fetch_assoc($campaignsqq)){ */
		foreach ($campaignsArrayMismatch as $campaignsww=>$cnt){
			// var_dump($campaignsww);
			// die();
			$maintenanceStr .="<tr  ".($l % 2 ? 'class="trLine"' : '').">";
			
			$sql = "select acr.affiliateID,acr.profile_id, aff.username  from affiliates_campaigns_relations acr  left join affiliates aff on aff.id=acr.affiliateID where acr.campID = '". $campaignsww."'";
			$campRow = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
			
			$maintenanceStr .="<td align='center'>". $campaignsww ."</td>";
			$maintenanceStr .= "<td align='center'>". $campRow['affiliateID'] ."</td>";
			$maintenanceStr .="<td align='center'>". $campRow['username'] ."</td>";
			$maintenanceStr .="<td align='center'>". $campRow['profile_id'] ."</td>";
			
			
/* 		$sql = "select sum(id) as mismatch from (select count(id) as id from data_reg where affiliate_id=500 and  campaign_id = '". $campaignsww['campaign_id']  ."' union 
					  select count(id) as id from data_sales where affiliate_id=500 and campaign_id = '". $campaignsww['campaign_id'] ."' union
					  select count(id) as id from data_stats where affiliate_id=500 and campaign_id = '". $campaignsww['campaign_id'] ."' union
					 select count(id) as id from data_sales_pending where affiliate_id=500 and campaign_id = '". $campaignsww['campaign_id'] ."') a";
				 
			
			
		$countMismatch = mysql_fetch_assoc(function_mysql_query($sql)); */
		// $maintenanceStr .="<td align='center'>". $countMismatch['mismatch'] ."</td>";
		$maintenanceStr .="<td align='center'>". $cnt ."</td>";
		if($campRow['affiliateID'] == 0)
			$maintenanceStr .="<td align='center'><div class='tooltip'><input id='btnFix' class='btnFix'  style=\"background-color:lightgray;\"type='button' data-updateAffId = ".  $campRow['affiliateID'] ." data-updateCampId = ". $campaignsww  ." value=\"".lang("Fix") ."\" /><span class='tooltiptext'>". lang("Please relate first and then click fix.") ."</span></div></td>";
		else
			$maintenanceStr .="<td align='center' ><input id='btnFix' class='btnFix' type='button' data-updateAffId = ".  $campRow['affiliateID'] ." data-updateCampId = ". $campaignsww  ." value='".lang("Fix")."'></td>";
		
		$maintenanceStr .= "</tr>"; 
		$l++;
		}
		
		if($maintenanceStr == ""){
			$maintenanceStr .="<tr>";
			$maintenanceStr .="<td colspan=6 align=center>". lang('No records found. ') ."</td>";
			$maintenanceStr .="</tr>";
		}
		
		echo $maintenanceStr; die;
