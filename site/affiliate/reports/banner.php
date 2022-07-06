<?php
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/affiliate" );
}

$globalWhere = " tb1.affiliate_id = " . $set->userInfo['id'] . " and ";

	$pageTitle = lang('Creative Report');
	$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'.$set->SSLprefix. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
            
                if (strpos($set->reportsToHide, 'creative') > 0) {
                    _goto($set->SSLprefix.'affiliate/');
                }
				
	$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
	<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>';
	$filename = "creatives_data_" . date('YmdHis');
		
	$sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
        $merchantsArray = array();
		$displayForex = 0;
		$mer_rsc = function_mysql_query($sql,__FILE__);
		while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
				if (strtolower($arrMerchant['producttype'])=='forex')
						$displayForex = 1;
					$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}
                
		if ($merchant_id) $where = " AND merchant_id='".$merchant_id."'";
		if ($banner_id) $where = " AND banner_id='".$banner_id."'";
		if ($profile_id) $where .= " AND profile_id='".$profile_id."'";
                
        // There is no need to run through the list of wallets.
		
        $globalWhere = str_replace('tb1.affiliate_id', 'affiliate_id', $globalWhere);
		$globalWhereSales = str_replace('affiliate_id', 'tb1.affiliate_id', $globalWhere);
		$earliestTimeForNetRev = date('Y-m-d H:i:s');
                
                
                /*
		$sql = "SELECT merchants.id AS merchant_id, merchants.wallet_id AS wallet_id, merchants.showDataForAffiliateSince, stats_banners.*, SUM(stats_banners.views) AS totalViews, SUM(stats_banners.clicks) AS totalClicks "
                     . "FROM traffic stats_banners "
                     . "INNER JOIN merchants ON stats_banners.merchant_id = merchants.id AND stats_banners.rdate > merchants.showDataForAffiliateSince "
                     . "WHERE ".$globalWhere." stats_banners.merchant_id>0 and stats_banners.rdate BETWEEN '".$from."' and '".$to."' ".$where." "
                     . "GROUP BY stats_banners.banner_id";
                */
		// $showRealFtd = mysql_result(function_mysql_query('SELECT showRealFtdToAff FROM settings LIMIT 0,1',__FILE__), 0, 0);
                
                
                $creatives_stats_where = '';
		$creatives_stats_join = '';
		
                $creatives_stats_where .= " AND AffiliateID='".$set->userInfo['id']."' ";
		
		if ($merchant_id) {
                    $creatives_stats_where .= " AND MerchantID='".$merchant_id."' ";
                }
                
		if ($banner_id) {
                    $creatives_stats_where .= " AND BannerID='".$banner_id."' ";
                }
                
                $sql = "SELECT CONCAT(Date,MerchantID,AffiliateID,BannerID) as id, MerchantID as merchant_id, AffiliateID as affiliate_id, SUM(Impressions) AS totalViews, SUM(Clicks) AS totalClicks, BannerID as banner_id "
                        . "FROM merchants_creative_stats ".$creatives_stats_join." "
                        . "WHERE (Date BETWEEN '" . $from . "' AND '" . $to ."') ".$creatives_stats_where." GROUP BY BannerID";

                
		$qq = function_mysql_query($sql,__FILE__);
             
		
		$merchantIDs = ($set->userInfo['merchants']);
		if (empty($merchantIDs))
			$merchantIDs = 0;
		else
			$merchantIDs=ltrim($merchantIDs,',');
		
		$merchantIDs = str_replace('|',",",$merchantIDs);
		if (empty($merchantIDs))
			$merchantID = 0;
		else 
			$merchantIDs=ltrim($merchantIDs,',');
		
		$qry = "select * from merchants where valid = 1 and id in (" . $merchantIDs . ")";
		// die ($qry);
		$rsc = function_mysql_query($qry,__FILE__);
		$merchantsArray = array();
		while ($row = mysql_fetch_assoc($rsc)) {
			$merchantsArray[$row['id']] = $row;
		}
				
		// if (true)
		while ($ww = mysql_fetch_assoc($qq)) 
		{

			
			$deal = AffiliateDealType($ww['merchant_id'],$arrDealTypeDefaults);
			
			
			if (!$deal)
	continue;
			
			$showDataForAffiliateSince = $set->showDataForAffiliateSince;
			$showDataForAffiliateSinceTB1='';
			$showDataForAffiliateSinceWhere = '';
                        
			if ($showDataForAffiliateSince>'2000-11-30 00:00:00')  {
				$showDataForAffiliateSinceWhere = " and rdate>'" . $showDataForAffiliateSince . "' ";
				$showDataForAffiliateSinceTB1 = 'tb1.'.$showDataForAffiliateSince ; 
			}
				
			$qqry9 = "SELECT id,title,type FROM merchants_creative WHERE id='".$ww['banner_id']."'";
			$bannerInfo = mysql_fetch_assoc(function_mysql_query($qqry9,__FILE__));
			
			if ($type && $bannerInfo['type'] != $type) {
                            continue;
                        }
                        
			$totalLeads=0;
			$totalDemo=0;
			$totalReal=0;
			$ftd=0;
			$totalCPI=0;
			$ftd_amount=0;
			$depositingAccounts=0;
			$sumDeposits=0;
			$bonus=0;
			$cpaAmount=0;
			$withdrawal=0;
			$volume=0;
			$lots=0;
			$totalCom=0;
                        $real_ftd = 0;
			$real_ftd_amount = 0;
                        
			// $merchantww=dbGet($ww['merchant_id'],"merchants");
			 $merchantww=$merchantsArray[$ww['merchant_id']];
			// var_dump($merchantww);
			// die();
			$merchantID = $merchantww['id'];
			$merchantName = strtolower($merchantww['name']);
			
                        $qry5 = "SELECT 
SUM(cm.Commission) as comms, 
SUM(IF(dr.type='lead', 1, 0)) AS total_leads, 
SUM(IF(dr.type='demo', 1, 0)) AS total_demo, 
SUM(IF(dr.type='real', 1, 0)) AS total_real 
FROM data_reg dr 
LEFT JOIN commissions cm ON dr.trader_id = cm.traderID AND cm.Date BETWEEN '".$from."' AND '".$to."'
WHERE dr.merchant_id = '" . $merchantID . "' and   dr.affiliate_id = '".$set->userInfo['id']."' and  dr.banner_id='".$bannerInfo['id']."' AND dr.rdate BETWEEN '".$from."' AND '".$to."' GROUP BY dr.banner_id ";

			$regqq=function_mysql_query($qry5,__FILE__);
                        
                        $arrTierCplCountCommissionParams = [];
                        $regww=mysql_fetch_assoc($regqq);
                        if($regww){
                            $totalLeads = $regww['total_leads'];
                            $totalDemo = $regww['total_demo'];
                            $totalReal = $regww['total_real'];
                        }

				
			$listReport .= '
				<tr>
				<td style="text-align: left;">'.$bannerInfo['id'].'</td>
				<td style="text-align: left;">'.($bannerInfo['id'] ? $bannerInfo['title'] : lang('BANNER REMOVED')).'</td>
				<td style="text-align: left;">'.$merchantww['name'].'</td>
				<td style="text-align: left;">'.$bannerInfo['type'].'</td>
				'.(allowView('af-impr',$deal,'fields') ? '
				<td>'.@number_format($ww['totalViews'],0).'</td>
				' : '' ) .'
				
				'.(allowView('af-clck',$deal,'fields') ? '
				<td>'.@number_format($ww['totalClicks'],0).'</td>
				' : '' ) .'
				'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi? '
				<td>'.@number_format($totalCPI,0).'</td>
				' : '' ) .'
				<td>'.@number_format(($ww['totalClicks']/$ww['totalViews'])*100,2).' %</td>
				<td>'.@number_format(($totalReal/$ww['totalClicks'])*100,2).' %</td>
				<td>'.@number_format(($ftd/$ww['totalClicks'])*100,2).' %</td>' . 
				
				(!(strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino')
					?
					 (allowView('af-lead',$deal,'fields') ? 
					'<td><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=lead">'.$totalLeads.'</a></td>'
					 : '' ).'
					 '. (allowView('af-demo',$deal,'fields') ? '	
					   <td><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=demo">'.$totalDemo.'</a></td>'
					: '') 
					: '') .
					
				 (allowView('af-real',$deal,'fields') ?
				'<td><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=real">'.$totalReal.'</a></td>
				' : '' ).' 
				'. (allowView('af-ftd',$deal,'fields') ?'
				<td><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=ftd">'.$ftd.'</a></td>
				' : '' ).' 
				'.(allowView('af-ftda',$deal,'fields') ? '<td>'.price($ftd_amount).'</td>' : '').'
				
            	'.(allowView('af-tftd',$deal,'fields') ? '<td><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=totalftd">'.$real_ftd.'</a></td>' : '').'
				'.(allowView('af-tftda',$deal,'fields') ? '<td>'.price($real_ftd_amount).'</td>' : '').'
				'.( (allowView('af-depo',$deal,'fields')) ? 
				  '<td><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&merchant_id='.$merchantww['id'].'&banner_id='.$bannerInfo['id'].'&type=deposit">'.$depositingAccounts.'</a></td>'
				  : '' ) .'
				'.( (allowView('af-depoam',$deal,'fields')) ? 
			 '<td>'.price($sumDeposits).'</td>'
			 :'').'
				'.( (allowView('af-vlm',$deal,'fields')) ? '<td style="text-align: center;">'.price($volume).'</td>' : '') . '
				'.( (allowView('af-bns',$deal,'fields')) ? 
				 '<td>'.price($bonus).'</td>' : '' ).'
				 '.( (allowView('af-withd',$deal,'fields')) ? 
				 '<td>'.price($withdrawal).'</td>' : '' ).'
				 '.( (allowView('af-chrgb',$deal,'fields')) ? 
				 '<td>'.price($chargeback).'</td>' : '' ).'
				'.( (allowView('af-ntrv',$deal,'fields')) ? '<td style="text-align: center;">'.price($netRevenue).'</td>' : '') . '
			'.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')? '
				<td>'.price($totalPNL).'</td> ' : '').'
				'./*.'<td>'.price($totalCom).'</td>'.*/'
			</tr>';
			
			$totalImpressions += $ww['totalViews'];
			$totalClicks += $ww['totalClicks'];
			$totalCPIM += $totalCPI;
			$totalLeadsAccounts += $totalLeads;
			$totalDemoAccounts += $totalDemo;
			$totalRealAccounts += $totalReal;
			$totalFTD += $ftd;
			$totalRealFtd += $real_ftd;
			$totalRealFtdAmount += $real_ftd_amount;
			$totalDeposits += $depositingAccounts;
                        
			// if ($merchantsArr2[$merchantID]->showFtdAmount) {
                            $totalFTDAmount += $ftd_amount;
			// }
			$totalSumPNL += $totalPNL;
			$totalDepositAmount += $sumDeposits;
			$totalVolume += $volume;
			$totalBonusAmount += $bonus;
			$totalWithdrawalAmount += $withdrawal;
			$totalChargeBackAmount += $chargeback;
			
			// if ($merchantsArr2[$merchantID]->showRev) {
                            $totalNetRevenue += $netRevenue;
			// }
			
			$totalComs += $totalCom;
			$l++;
		}
                
		if ($l > 0) {
                    $set->sortTableScript = 1;
                }
                
                $set->sortTable = 1;
$merchant_id = clearInjection($merchant_id,'int');
		
		$set->content .= '
			<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="banner" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td>'.lang('Banner ID').'</td>
					<td>'.lang('Creative Type').'</td>
					
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><input type="text" name="banner_id" value="'.$banner_id.'" /></td>
					<td><select name="type" style="width: 150px;">
						<option value="">'.lang('All').'</option>
						<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
						<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>
						<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>
						<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
						<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>
						<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
						<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>
						<option value="coupon" '.($type == "coupon" ? 'selected' : '').'>'.lang('Coupon').'</option>
					</select></td>
					'.$platformParam .'
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#creativeTbl\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#creativeTbl\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				<div style="clear:both"></div>
			</div>
			<div style="height: 20px;"></div>
			
			<div class="normalTableTitle"  class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			
			<div style="background: #F8F8F8;">';
				//width 2000
			$tableStr='
				<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0"  id="creativeTbl">
					<thead><tr  class="table-row">
						<th style="text-align: left;" class="table-cell">'.lang('Creative ID').'</th>
						<th style="text-align: left;" class="table-cell">'.lang('Creative Name').'</th>
						<th style="text-align: left;" class="table-cell">'.lang('Merchant').'</th>
						<th class="table-cell">'.lang('Type').'</th>
						'.(allowView('af-impr',$deal,'fields') ?'
						<th class="table-cell">'.lang('Impressions').'</th>
						': '' ).'
						'.(allowView('af-clck',$deal,'fields') ?'
						<th class="table-cell">'.lang('Clicks').'</th>
						': '' ).'
						'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi ?'
						<th class="table-cell">'.lang('Installation').'</th>
						': '' ).'
						<th class="table-cell">'.lang('Click Through Ratio (CTR)').'</th>
						<th class="table-cell">'.lang('Click to Account').'</th>
						<th class="table-cell">'.lang('Click to Sale').'</th>'
						. (!(strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') ? 
						
						(allowView('af-lead',$deal,'fields') ?
						'<th class="table-cell">'.lang(ptitle('Lead')).'</th>
						': '' ).'
						'.(allowView('af-demo',$deal,'fields') ?'
						<th class="table-cell">'.lang(ptitle('Demo')).'</th>
						': '' )
						
						: '') . '
						
						'.(allowView('af-demo',$deal,'fields') ?
						'<th class="table-cell">'.lang('Accounts').'</th>
						' : '' ) . '
						'.(allowView('af-ftd',$deal,'fields') ? '
						<th class="table-cell">'.lang('FTD').'</th>
						' : '' ) . '
						'.(allowView('af-ftda',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('FTD Amount').'</th>
						' : '' ) . '
						'.(allowView('af-tftd',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('RAW FTD').'</th>
						' : '' ) . '
						'.(allowView('af-tftda',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('RAW FTD Amount').'</th>
						' : '' ) . '
						
						'.(allowView('af-depo',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('Total Deposits').'</th>
						' : '' ) . '
						'.(allowView('af-depoam',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('Deposit Amount').'</th>
						' : '' ) . '
						'.(allowView('af-vlm',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('Volume').'</th>
						' : '' ) . '
							'.(allowView('af-bns',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('Bonus Amount').'</th>
						' : '' ) . '
							'.(allowView('af-withd',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('Withdrawal Amount').'</th>
						' : '' ) . '
							'.(allowView('af-chrgb',$deal,'fields') ? 
						 '<th class="table-cell">'.lang('ChargeBack Amount').'</th>
						' : '' ) . '	
						'.(allowView('af-ntrv',$deal,'fields') ? 
						 '<th class="table-cell">'.lang(ptitle('Net Deposit')).'</th>
						' : '' ) . '
						'.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')? '
							<th class="table-cell">'.lang(ptitle('PNL')).'</th> ' : '').'
						'./*.'<th class="table-cell">'.lang('Commission').'</th>'.*/'
					</tr></thead><tfoot><tr>
						'.($display_type ? '<th></th>' : '').'
						<th style="text-align: left;"><b>'.lang('Total').':</b></th>
						<th></th>
						<th></th>
						<th></th>
						
						'.(allowView('af-impr',$deal,'fields') ?'
						<th style="text-align: left;">'.$totalImpressions.'</th>
						' : '' ).'
						'.(allowView('af-clck',$deal,'fields') ?'
						<th style="text-align: left;">'.$totalClicks.'</th>
						' : '' ).'
						'.(allowView('af-instl',$deal,'fields') && $set->deal_cpi?'
						<th style="text-align: left;">'.$totalCPIM.'</th>
						' : '' ).'
						<th style="text-align: left;">'.@number_format(($totalClicks/$totalImpressions)*100,2).' %</th>
						<th style="text-align: left;">'.@number_format(($totalRealAccounts/$totalClicks)*100,2).' %</th>
						<th style="text-align: left;">'.@number_format(($totalFTD/$totalClicks)*100,2).' %</th>'
						. (!(strtolower($merchantww['producttype']) == 'sportsbetting' || strtolower($merchantww['producttype']) == 'casino') ? 
							 
							(allowView('af-demo',$deal,'fields') ?
							 '<th style="text-align: left;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=lead">'.$totalLeadsAccounts.'</a></th>'
							 : '' ) . 
							(allowView('af-lead',$deal,'fields') ?
							   '<th style="text-align: left;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=demo">'.$totalDemoAccounts.'</a></th>'
							   : '' )
							   
							: '') . 
							(allowView('af-real',$deal,'fields') ?
						'<th style="text-align: left;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=real">'.$totalRealAccounts.'</a></th>'
						: '' ).
						(allowView('af-ftd',$deal,'fields') ?
						'<th style="text-align: left;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=ftd">'.$totalFTD.'</a></th>'
						: '' ).
						(allowView('af-ftda',$deal,'fields') ?
						'<th style="text-align: left;">'.price($totalFTDAmount).'</th>' 
						: '').
						(allowView('af-tftd',$deal,'fields') ?
						'<th style="text-align: left;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=totalftd">'.$totalFTD.'</a></th>' 
						: '').'
						'. (allowView('af-tftda',$deal,'fields') ?
						'<th style="text-align: left;">'.price($totalFTDAmount).'</th>' 
						: '').'
						'. (allowView('af-depo',$deal,'fields') ?
						'<th style="text-align: left;"><a href="'.$set->SSLprefix.'affiliate/reports.php?act=trader&from='.date("Y/m/d", strtotime($from)).'&to='.date("Y/m/d", strtotime($to)).'&type=deposit">'.$totalDeposits.'</a></th>': '').'
						'. (allowView('af-depoam',$deal,'fields') ?
						 '<th style="text-align: left;">'.price($totalDepositAmount).'</th>' : '') . ' 
						 '. (allowView('af-vlm',$deal,'fields') ?
						 '<th>'.price($totalVolume).'</th>' : '') .' 
						 '. (allowView('af-bns',$deal,'fields') ?
						 '<th style="text-align: left;">'.price($totalBonusAmount).'</th>' : ''). '
						 '. (allowView('af-withd',$deal,'fields') ?
							'<th style="text-align: left;">'.price($totalWithdrawalAmount).'</th>' : '' ).'
						 '. (allowView('af-chrgb',$deal,'fields') ?
                                 '<th style="text-align: left;">'.price($totalChargeBackAmount).'</th>' 
                                           :''         ).'
						 '. (allowView('af-ntrv',$deal,'fields') ? '<th>'.price($totalNetRevenue).'</th>' : '').'
						 '.($set->deal_pnl==1 && allowView('af-pnl',$deal,'fields')? '
							<th>'.price($totalSumPNL).'</th> ' : '').'
						'./*.'<th style="text-align: left;">'.price($totalComs).'</th>'.*/'
					</tr></tfoot>
					<tbody>
					'.$listReport.'
				</table>
				<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
				<script>
				$(document).ready(function(){
					$(".saveReport").on("click",function(){
						$.prompt("<label>'. lang("Provide name for report") .': <br/><input type=\'text\' name=\'report_name\' value=\'\' style=\'width:80wh\' required></label><div class=\'err_message\' style=\'color:red\'></div>", {
								top:200,
								title: "'. lang('Add to Favorites') .'",
								buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
								submit: function(e,v,m,f){
									if(v){
										name = $("[name=report_name]").val();
										if(name != ""){
											
											url = window.location.href;
											user = "'. $set->userInfo['id'] .'";
											level = "affiliate";
											type = "add";
											
											saveReportToMyFav(name, \'banner\',user,level,type);
										}
										else{
											$(".err_message").html("'. lang("Enter Report name.") .'");
											return false;
										}
									}
									else{
										//
									}
								}
							});
					});
				});
				
				</script>
				';
			
			//excelExporter($tableStr,'Creative');
			$set->content.=$tableStr.'</div>'.getPager();
			
		
			//MODAL
		$myReport = lang("Creatives");
		include "common/ReportFieldsModal.php";
			
                    theme();
					
?>