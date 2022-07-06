<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');


$r = mysql_fetch_assoc( function_mysql_query("select count(id) as cnt from merchants where valid = 1 and apiType='spot'	",__FILE__));
$showSpotFeature =$r['cnt'];

	function getTag($tag, $endtag, $xml) {
		if (!$endtag) $endtag=$tag;
		preg_match_all("/".$tag."(.*?)".$endtag."/", $xml, $matches);
		return $matches[1][0];
		}
	
	
	
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

		$ignoreMerchantid = 0;
		$forceLoad = false;
		$merchant_id= aesDec($_COOKIE['mid']);
		if (!empty($merchant_id)) { 
		$qq = "select (id) as id from merchants where id = " . $merchant_id;
				$bb = function_mysql_query($qq,__FILE__);
				$ww=mysql_fetch_assoc($bb);
			if ($merchant_id != $ww['id']) {
				$forceLoad= true;
			}
		}
			if ($merchant_id ==0  or $merchant_id =='' or $forceLoad) {
				$qq = "select min(id) as id from merchants limit 1";
				$bb = function_mysql_query($qq,__FILE__);
				$ww=mysql_fetch_assoc($bb);
			$merchant_id = $ww['id'];
			}

	
// $merchant_id= isset($_GET['merchantid']) ? $_GET['merchantid'] : ( isset($_POST['merchantid']) ? $_POST['merchantid']  :1);	
		$merchant_id= isset($_GET['merchant_id']) ? $_GET['merchant_id'] : (isset($_POST['merchant_id']) ?  $_POST['merchant_id'] : 1);
		// die ('mer: ' . $merchant_id);	
	
	
			
		

switch ($act) {

		
	case "clear_jobs":
		// function_mysql_query("TRUNCATE TABLE  cron_logs",__FILE__);
		_goto($set->SSLprefix.$set->basepage);
		break;
	/* case "updateProfile":
		$sql = "update `affiliates_campaigns_relations` set profile_id=".$_GET['pid'] . " where id=" . $_GET['id'];
		function_mysql_query($sql,__FILE__);
		echo "1";
		exit;
		break; */
	default:
		//$set->pageTitle = lang('API Integration - Cron Job');
		
		$set->breadcrumb_title =  lang('API Integration - Cron Job');
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.'admin/logs.php?act=cron" class="arrow-left">'.lang('API Integration - Cron Job').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';


		$qq=function_mysql_query("(
SELECT * 
FROM cron_logs
WHERE TYPE =  'auto'
ORDER BY id DESC 
LIMIT 15
)
UNION ALL (

SELECT * 
FROM cron_logs
WHERE TYPE =  ''
ORDER BY id DESC
)",__FILE__);

// var_dump($set);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$listReport .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
					<td>'.$ww['id'].'</td>
					<td>'.$ww['merchant_name'].'</td>
					<td>'.dbDate($ww['startscan']).'</td>
					<td>'.dbDate($ww['lastscan']).'</td>
					<td>'.$ww['reg_total'].'</td>
					<td>'.$ww['sales_total'].'</td>
					<td>'.$ww['type'].'</td>
				</tr>';
			}
			
			// die ($set->cronUrls);
		// if ($set->userInfo['id'] == "1") 
			$set->content .= '<table><tr>
				<td><select id="merchants_id"><option value="">' 
						  . lang('Choose Merchant') . '</option>' . listMerchants() . '</select></td>
				<td><input type="text" id="date" value="'.date("Y-m-d").'" style="text-align: center; width: 100px;" /></td>
				<td><div class="btn"><a id="manuallyScan" href="javascript:void(0);"  target="_blank">'.lang('Run Scan Manually').'</a></div></td>
				<td><div class="btn"><a id="monthlyScan" href="javascript:void(0);"  target="_blank">'.lang('Scan Monthly Manually').'</a></div></td>
				<div>&nbsp;&nbsp;&nbsp;</div>
				<td><div class="btn"><a href="'.$set->SSLprefix.$set->basepage.'?act=clear_jobs">'.lang('Clear Cron Jobs Logs').'</a></div></td>
			</tr></table>
				<br />';
		$set->content .= '
			<div class="normalTableTitle">'.lang('Cron Job').'</div>
			<div style="background: #F8F8F8;">
				<table width="100%" class="normal" border="0" cellpadding="3" cellspacing="0">
					<thead><tr>
						<td>#</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Start Scan').'</td>
						<td>'.lang('Last Scan').'</td>
						<td>'.lang('Total Accounts').'</td>
						<td>'.lang('Total Sales').'</td>
						<td>'.lang('Type').'</td>
					</tr></thead><tfoot>'.$listReport.'</tfoot>
					
				</table>
				<br><div>   *  '.lang('This table contains all manually actions and last 15 automatic process.').'</div>
			</div>';
			
			$set->content .= '<script>
			manualUrl = "'. (empty($set->cronUrls) ? $set->webAddress . '/cronjob.php' : $set->cronUrls ) .'?m_date="+$("#date").val();
			monthlyUrl = "'. (empty($set->cronUrls) ? $set->webAddress . '/cronjob.php' : $set->cronUrls ) .'?m_date="+$("#date").val() + "monthly=1";
			url = "";
			chk = "";
			
			$("#merchants_id").on("change",function(){
				
				brand = $(this).val();
				var loc = location.search;
				$.get("'.$set->SSLprefix.'ajax/getMerchantCronUrl.php", { merchant_id: brand}, function(res) {
						if(res!=""){
							
							url = res;
							chk = /\??(?:([^=]+)=([^&]*)&?)/g.test(res);
						
						}
				});
				
				
				
			});
			
			$("#monthlyScan").on("click",function(e){
				e.preventDefault();
				if(url=="")
					window.open(monthlyUrl + "?m_date=" + $("#date").val() + "&monthly=1");
				else{
					if(chk === true)
						window.open(url + "&m_date=" + $("#date").val() + "&monthly=1");
					else
						window.open(url + "?m_date=" + $("#date").val() + "&monthly=1");
				}
			});
			
			$("#manuallyScan").on("click",function(e){
				e.preventDefault();
				if(url=="")
					window.open(monthlyUrl + "?m_date=" + $("#date").val());
				else
				{
					if(chk === true)
						window.open(url + "&m_date=" + $("#date").val());
					else
						window.open(url + "?m_date=" + $("#date").val());
				}
			});
			
			</script>';
			
							theme();
		break;
		
}
			



?>