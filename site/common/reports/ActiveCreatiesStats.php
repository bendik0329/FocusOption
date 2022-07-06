<?php
//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$pageTitle = lang('Active Creatives Stats');
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.$userlevel.'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		
		$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
		$filename = "ActiveCreatiesStats_" . date('YmdHis');
	
	$affiliatesArray = array();
	
	if($userlevel == 'manager')
	$group_id       = $set->userInfo['group_id'];

		$listReport = '';
                
          
		$l = -1;
	                
                    $l++;
      
                    $merchantName = strtolower($ww['name']);
                    $merchantID = $ww['id'];
                    
     
                            $searchInSql = " BETWEEN '" . $from . "' AND '" . $to . "' ";
                            
                            $formula  = $ww['rev_formula'];
                            $fromDate = $from;
                            $toDate   = $to;
                            
          
                            $merchantName = strtolower($ww['name']);
                            $merchantID = $ww['id'];

							$banner_id = empty($banner_id) ? 0 : $banner_id;

                           
                            /*
						$q = "select affiliate_id, banner_id,rdate , 'traffic' as event from traffic where rdate between '" .$from . "' and '" . $to. "'  ".(!empty($group_id) ? " and group_id = " . $group_id : "" ) . " and banner_id in ( " . $banner_id . " )  and banner_id>0 group by affiliate_id,banner_id ";
						$rsc = function_mysql_query($q);
						while ($row = mysql_fetch_assoc($rsc)){
							if (!isset($affiliatesArray[$row['affiliate_id']][$row['banner_id']]) || (isset($affiliatesArray[$row['affiliate_id']][$row['banner_id']]['rdate']) && $affiliatesArray[$row['affiliate_id']][$row['banner_id']]['rdate']>$row['rdate']))
							$affiliatesArray[$row['affiliate_id']][$row['banner_id']]  = $row;
						}


						$q = "select affiliate_id, banner_id,rdate, 'registration' as event from data_reg where rdate between '" .$from . "' and '" . $to ."' ".(!empty($group_id) ? " and group_id = " . $group_id : "" ) . " and banner_id in ( " . $banner_id . " )  and banner_id>0 group by affiliate_id,banner_id ";
						$rsc = function_mysql_query($q);
						while ($row = mysql_fetch_assoc($rsc)){
							if (!isset($affiliatesArray[$row['affiliate_id']][$row['banner_id']]) || (isset($affiliatesArray[$row['affiliate_id']][$row['banner_id']]['rdate']) && $affiliatesArray[$row['affiliate_id']][$row['banner_id']]['rdate']>$row['rdate']))
							$affiliatesArray[$row['affiliate_id']][$row['banner_id']]  = $row;
						}
						
						$q = "select affiliate_id, banner_id,rdate, 'transaction' as event from data_sales where rdate between '" .$from . "' and '" . $to ."' ".(!empty($group_id) ? " and group_id = " . $group_id : "" ) . " and banner_id in ( " . $banner_id . " )  and banner_id>0 group by affiliate_id,banner_id ";
						$rsc = function_mysql_query($q);
						while ($row = mysql_fetch_assoc($rsc)){
							if (!isset($affiliatesArray[$row['affiliate_id']][$row['banner_id']]) || (isset($affiliatesArray[$row['affiliate_id']][$row['banner_id']]['rdate']) && $affiliatesArray[$row['affiliate_id']][$row['banner_id']]['rdate']>$row['rdate']))
							$affiliatesArray[$row['affiliate_id']][$row['banner_id']]  = $row;
						}
						
						$q = "select affiliate_id, banner_id,rdate, 'stats' as event from data_stats where rdate between '" .$from . "' and '" . $to. "' ".(!empty($group_id) ? " and group_id = " . $group_id : "" ) . " and banner_id in ( " . $banner_id . " )  and banner_id>0 group by affiliate_id,banner_id ";
						$rsc = function_mysql_query($q);
						while ($row = mysql_fetch_assoc($rsc)){
							if (!isset($affiliatesArray[$row['affiliate_id']][$row['banner_id']]) || (isset($affiliatesArray[$row['affiliate_id']][$row['banner_id']]['rdate']) && $affiliatesArray[$row['affiliate_id']][$row['banner_id']]['rdate']>$row['rdate']))
							$affiliatesArray[$row['affiliate_id']][$row['banner_id']]  = $row;
						}
					

		$bannersRsc =function_mysql_query("select title,id,merchant_id,url,type from merchants_creative where id in (". $banner_id . ") ");
		$bannersArray = array();
		while ($row = mysql_fetch_assoc($bannersRsc)){
			$bannersArray[$row['id']] = $row;
		}
		
		$AffsRsc =function_mysql_query("select id,username from affiliates where 1=1 " . (!empty($group_id) ? " and group_id =  " . $group_id  : "" ));
		$allAffiliatesArray = array();
		while ($row = mysql_fetch_assoc($AffsRsc)){
			$allAffiliatesArray[$row['id']] = $row;
		}
		*/
		

		
	$sql = "SELECT SUM(mcs.Impressions) AS total_views, SUM(mcs.Clicks) AS total_clicks, mcs.*, mc.title, mc.url, aff.first_name, aff.last_name, 'traffic' as event
FROM merchants_creative_stats mcs
INNER JOIN affiliates aff ON aff.id = mcs.AffiliateID
INNER JOIN merchants_creative mc ON mc.id = mcs.BannerID
WHERE mcs.BannerID > 0 AND BannerID in ( " . implode(',',explode(',',$banner_id)) . " ) AND (Date BETWEEN '" . $from . "' AND '" . $to ."')
GROUP BY mcs.AffiliateID, mcs.BannerID";
        
		$rsc = function_mysql_query($sql);
                while ($row = mysql_fetch_assoc($rsc)){			
			
			$listReport .= '
				<tr>
				<td style="text-align: center;">'.$row['BannerID'].'</td>
				<td style="text-align: center;"><a href="'.$row['url'].'" target="_blank">'.$row['title'].'</a></td>
                                <td style="text-align: center;">'.$row['total_views'].'</td>
                                <td style="text-align: center;">'.$row['total_clicks'].'</td>
				<td style="text-align: center;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$row['AffiliateID'].'" target="_blank">'.$row['AffiliateID'].'</a></td>
				<td style="text-align: center;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$row['AffiliateID'].'" target="_blank">'.$row['first_name'].' '.$row['last_name'].'</a></td>
				<td style="text-align: center;">'.date("Y-m-d h:i:s", strtotime($row['Date'])) .'</a></td>
				<td style="text-align: center;">'. lang(ucwords($row['event'])) .'</td>
				
			</tr>';
			
			
			
			$l++;
		}
		
		
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable  = 1;
		
		$set->content .= '
		<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form method="get">
			<input type="hidden" name="act" value="ActiveCreatiesStats" />
				<table><tr>
						<td>'.lang('Period').'</td>
						<td>'.lang('Creative IDs').' (' .lang('seperated by').' ,)'. '</td>
					</tr><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" id="banner_id" style="width: 60px; text-align: center;" /></td>
					
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#ActiveCreatiesStats\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#ActiveCreatiesStats\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>
		</div>
		<div style="height:20px;"></div>
		
		<div class="normalTableTitle" style="width: 100%">'.lang('Active Creaties Stats').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
		<div style="background: #F8F8F8;">';
			$tableStr = '
			<table width="100%" class="tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="quickTbl">
				<thead><tr>
				<th>'. lang('Creative ID') .'</th>
				<th>'. lang('Creative Name') .'</th>
                                <th>'. lang('Impressions') .'</th>
                                <th>'. lang('Clicks') .'</th>
				<th>'. lang('Affiliate ID') .'</th>
				<th>'. lang('Affiliate Username') .'</th>
				<th>'. lang('Date') .'</th>
				<th>'. lang('Type') .'</th>
				</tr></thead><!--tfoot><tr>
				<th>'. lang('Total') .'</th>

				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				
				</tr></tfoot-->
				<tbody>
				'.$listReport.'
			</table>
			'.(!empty($listReport)?'
			<script>
				$(document).ready(function(){
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'ActiveCreatiesStats\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#quickTbl")[0].config.rowsCopy).each(function() {
						txt += "<tr>" + $(this).html()+"</tr>";
					});
					txt += "</tbody>";
					txt += "<tfoot>" + tfoot + "</tfoot>";
					txt += "</table>";
					$("body").append("<div style=\'display:none\'>"+ txt +"</div>");
				});
				</script>
			':'');
		
		
		$set->content .= $tableStr . '</div>' . getPager();
		
		//MODAL
		$myReport = lang("Active Creatives Stats");
		include "common/ReportFieldsModal.php";
		
		theme();
		
?>