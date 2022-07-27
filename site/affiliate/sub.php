<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
chdir('../');
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);

/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();
getPermissions();

$dealsArray = $set->userInfo['dealsArray'];

switch ($act) {
	
	default:
		if ($set->introducingBrokerInterface) 
		$pageTitle = lang('Sub IBs');
	else
		$pageTitle = lang('Sub Affiliates');
	
	$set->breadcrumb_title =  lang($pageTitle);

    $set->pageTitle = '
    <style>
    .pageTitle{
        padding-left:0px !important;
    }
    </style>
    <ul class="breadcrumb">
        <li><a href="'.$set->SSLprefix.'affilaite/">'.lang('Dashboard').'</a></li>
        <li><a href="'. $set->SSLprefix.$set->uri .'">Reports - '.lang($pageTitle).'</a></li>
        <li><a style="background:none !Important;"></a></li>
    </ul>';
	
	
	
		$sql = 'SELECT * FROM merchants WHERE  valid = 1 ';

        $merchantsArray = array();
        $displayForex = 0;
        $mer_rsc = function_mysql_query($sql,__FILE__);
        while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {

            if (strtolower($arrMerchant['producttype'])=='forex')
                $displayForex = 1;

            $merchantsArray[$arrMerchant['id']] = $arrMerchant;
        }
		
		
		$from = strTodate($from);
		$to = strTodate($to);
		$from= str_replace('--','',$from);
		$to= str_replace('--','',$to);

		// List Merchants
		$viewsSum=0;
		$clicksSum=0;
		$totalLeads=0;
		$totalDemo=0;
		$totalReal=0;
		$newFTD=0;
		$ftdAmount=0;
		$totalBonus=0;
		$totalWithdrawal=0;
		$totalPNL=0;

                // Sub commission.
           /*     $sql = 'SELECT sub_com AS sub_com FROM affiliates WHERE valid = 1 AND id = ' . $set->userInfo['id'] . ' LIMIT 0, 1;';
                $arrAffSubCom = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                $floatAffSubCom = (float) $arrAffSubCom['sub_com'];
                unset($arrAffSubCom);*/
                
                $sql = "SELECT id,username FROM affiliates WHERE valid = 1 AND refer_id = " . $set->userInfo['id'];

		$affiliateqq = function_mysql_query($sql,__FILE__);
                
        $hasResults = false;
		while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {

	 	$total_leads = $total_demo = $total_real = $new_ftd = $totalDeposits = $depositsAmount = $bonus = $withdrawal = $chargeback = $thisComis = $totalDeposits = $depositsAmount = 0;
        $hasResults=true;

		$total                   = [];
		$sql = "SELECT SUM(views) AS impressions, SUM(clicks) AS clicks FROM sub_stats WHERE affiliate_id='".$affiliateww['id']."'";
		$arrClicksAndImpressions = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
		$total['viewsSum']       = $arrClicksAndImpressions['impressions'];
		$total['clicksSum']      = $arrClicksAndImpressions['clicks'];



			$line_views = 0;
			$line_clicks = 0;
			$line_leads = 0;
			$line_demo = 0;
			$line_real = 0;
			$line_ftd = 0;
			$line_lots = 0;
			$line_ftd_amount = 0;
			$line_deposits = 0;
			$line_deposits_amount = 0;
			$line_bonus = 0;
			$line_withdrawal = 0;
			$line_comission = 0;
			$line_pnl = 0;

			//clicks and views
			$line_views = $total['viewsSum'];
			$line_clicks = $total['clicksSum'];

                        // List of wallets.
                        $arrWallets = [];
                        $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1;";
                        $resourceWallets = function_mysql_query($sql,__FILE__);

                        while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                            $arrWallets[$arrWallet['wallet_id']] = false;
                            unset($arrWallet);
                        }

             $merQry = "SELECT * FROM merchants WHERE valid='1' ORDER BY id;";
			$merchantqq = function_mysql_query($merQry,__FILE__);

						$allbrabdrsc = function_mysql_query($merQry,__FILE__);
		$LowestLevelDeal = 'ALL';
		while ($brandsRow = mysql_fetch_assoc($allbrabdrsc)) {

				foreach ($dealsArray as $dealItem=>$value) {
					if ($brandsRow['id']==$dealItem) {

						$LowestLevelDeal = getLowestLevelDeal($LowestLevelDeal, $value);
						break;
					}
				}
		}

		$deal = $LowestLevelDeal;




	// while ($merchantww = mysql_fetch_assoc($merchantqq)) {
			foreach ($merchantsArray as $merc) {

				// var_dump($merc);
				// die();

				$merchantww  = $merc;


                                // Check if this is a first itaration on given wallet.
                                $needToSkipMerchant = $arrWallets[$merchantww['wallet_id']];





				$l++;
				$ftd_amount['amount'] = 0;
                                $ftd = 0;


                                //$arrClicksAndImpressions = getClicksAndImpressions($from, $to, $merchantww['id'], $affiliateww['id']);
								/* $arrClicksAndImpressions = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS impressions, SUM(clicks) AS clicks FROM sub_stats WHERE affiliate_id='".$affiliateww['id']."'",__FILE__));
                                $total['viewsSum']       = $arrClicksAndImpressions['impressions'];
                                $total['clicksSum']      = $arrClicksAndImpressions['clicks']; */


				$regqq=function_mysql_query("SELECT id,type FROM data_reg where merchant_id ='". $merchantww['id'] ."' and affiliate_id='".$affiliateww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
				while ($regww=mysql_fetch_assoc($regqq)) {
					if ($regww['type'] == "lead") $total_leads++;
					if ($regww['type'] == "demo") $total_demo++;
					if ($regww['type'] == "real") $total_real++;
					}

				$salesqq=function_mysql_query("SELECT type,amount FROM data_sales WHERE merchant_id = '".$merchantww['id'] . "' and  affiliate_id='".$affiliateww['id']."' AND rdate BETWEEN '".$from."' AND '".$to."'",__FILE__);
				while ($salesww=mysql_fetch_assoc($salesqq)) {
					if ($salesww['type'] == "deposit") {
						$depositsAmount += $salesww['amount'];
						$totalDeposits++;
						}
					if ($salesww['type'] == "bonus") $bonus += $salesww['amount'];
					if ($salesww['type'] == "withdrawal") $withdrawal += $salesww['amount'];
					if ($salesww['type'] == "chargeback") $chargeback += $salesww['amount'];
					if ($salesww['type'] == "volume") $volume += $salesww['volume'];
                                }


                                $ftdUsers = '';

                                if (!$needToSkipMerchant) {
                                    $arrFtds = getTotalFtds($from, $to, $affiliateww['id'], $merchantww['id'], $merchantww['wallet_id']);

                                    foreach ($arrFtds as $arrFtd) {
                                        $new_ftd++;
                                        $ftd_amount['amount'] += $totalftd['amount'];
                                        $beforeNewFTD = $ftd;
                                        getFtdByDealType($merchantww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $ftd_amount['amount'], $ftd);

                                        if ($beforeNewFTD != $ftd || count($arrFtds)==1) {
                                            $arrFtd['isFTD'] = true;
                                       //     $thisComis += (getCommission($from, $to, 0, -1, $arrDealTypeDefaults, $arrFtd) / 100) * $floatAffSubCom;
                                        }
                                        unset($arrFtd);
                                    }
                                }


				if ($merchantww['producttype']=='forex' ) {
						//lots
						$sql = 'SELECT turnover AS totalTurnOver,trader_id,rdate,affiliate_id,profile_id,banner_id FROM data_stats  '
										 . 'WHERE affiliate_id = ' . $affiliateww['id']
                                         . ' and merchant_id = "' . $merchantww['id'] . '" AND rdate BETWEEN "' . $from . '" AND "' . $to . '" '
							 ;


					$traderStatsQ = function_mysql_query($sql,__FILE__);
					while($ts = mysql_fetch_assoc($traderStatsQ)){

						if($ts['affiliate_id']==null) {
								continue;
						}

						// if (!in_array($ww['id'] . '-' .  $ts['trader_id'],$tradersProccessedForLots)) {
								$totalLots  = $ts['totalTurnOver'];
								// echo $totalLots
									$tradersProccessedForLots[$merchantww['id'] . '-' . $ts['trader_id']] = $merchantww['id'] . '-' . $ts['trader_id'];
									$lotdate = $ts['rdate'];
									$ex = explode(' ' , $lotdate);
									$lotdate = $ex[0];
									$row = [
												'merchant_id'  => $merchantww['id'],
												'affiliate_id' => $ts['affiliate_id'],
												'rdate'        => $lotdate,
												'banner_id'    => $ts['banner_id'],
												'trader_id'    => $ts['trader_id'],
												'profile_id'   => $ts['profile_id'],
												'type'       => 'lots',
												'amount'       =>  $totalLots,
									];
									// var_dump($row);
									// die();
									// die ($floatAffSubCom);
							//	$a = getCommission($from, $to, 0, $group_id, $arrDealTypeDefaults, $row)* $floatAffSubCom/100;
								// echo 'com: ' . $a .'<br>';
							//	$thisComis += $a;
						// }
					$line_lots +=$totalLots;
					}
				}

				if ($set->deal_pnl == 1) {
						$dealsForAffiliate['pnl']=1;
								$line_pnl  = 0;


								// {
								/* if (!in_array($merchantID . '-' .  $arrRes['trader_id'],$tradersProccessedForPNL)) {
									$tradersProccessedForPNL[$arrMerchant['id'] . '-' . $arrRes['trader_id']] = $arrRes['trader_id']; */
									// {

									// die ($where);


										$pnlRecordArray=array();

										$pnlRecordArray['affiliate_id']  = $set->userInfo['id'];
										$pnlRecordArray['merchant_id']  = $merchantww['id'];
										$pnlRecordArray['trader_id']  = (isset($trader_id) ? $trader_id: "");
										$pnlRecordArray['banner_id']  = $banner_id;
										$pnlRecordArray['profile_id']  = $profile_id;
										$pnlRecordArray['group_id']  = $group_id;
										$pnlRecordArray['searchInSql']  = $searchInSql;
										$pnlRecordArray['fromdate']  = $from;
										$pnlRecordArray['todate']  = $to;




									if ($dealsForAffiliate['pnl']>0){
										$sql = generatePNLquery($pnlRecordArray,false);
									}
									else	{  // just show the total sum pnl for this affiliate, no need to calculate the pnl.
										$sql = generatePNLquery($pnlRecordArray,true);
									}




											 //echo ($sql).'<Br>';
											 $pnlCom = 0;
								$traderStatsQ = function_mysql_query($sql,__FILE__);
								while($ts = mysql_fetch_assoc($traderStatsQ)){
												$pnlamount = ($ts['amount']*-1);
												$row = [
													'merchant_id'  => $ts['merchant_id'],
													'affiliate_id' => $ts['affiliate_id'],
													'rdate'        => $ts['rdate'],
													'banner_id'    => $ts['banner_id'],
													'trader_id'    => $ts['trader_id'],
													'profile_id'   => $ts['profile_id'],
													'type'       => 'pnl',
												 'amount'       =>  $pnlamount,
												 'initialftddate'       =>  $ts['initialftddate']
												 ];


												$line_pnl = $line_pnl + $pnlamount;


											//$a = getCommission($from, $to, 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// die ('getcom: ' .$a );
										if ($dealsForAffiliate['pnl']>0){

											$tmpCom = getCommission($ts['rdate'], $ts['rdate'], 0, $arrRes['group_id'], $arrDealTypeDefaults, $row);
											// echo 'com: ' . $tmpCom.'<br>';
												$pnlCom +=$tmpCom;
												$thisComis += $tmpCom;
										}
								}
						}



				$line_leads += $total_leads;
				$line_demo += $total_demo;
				$line_real += $total_real;
				$line_ftd += $new_ftd;
				$line_ftd_amount += $ftd_amount['amount'];
				$line_deposits += $totalDeposits;
				$line_deposits_amount += $depositsAmount;
				$line_bonus += $bonus;
				$line_withdrawal += $withdrawal;
				$line_chargeback += $chargeback;
				$line_comission += $thisComis;

                            // Mark given wallet as processed.
                            $arrWallets[$merchantww['wallet_id']] = true;
                        }

			$chartAffiliates[] = $affiliateww['id'];
			$listMerchants .= '<tr>
                            <td style="color: #646464;"><b>'.$affiliateww['id'].'</b></td>
                            <td style="color: #646464;"><b>'.$affiliateww['username'].'</b></td>
					'. (allowView('af-impr',$deal,'fields') ? '
							<td align="center">'.$line_views.'</td>
                            										' : '' ).'
					'. (allowView('af-clck',$deal,'fields') ? '
                            <td align="center">'.$line_clicks.'</td>
												' : '' ).'
					'. (allowView('af-lead',$deal,'fields') ? '
                            <td align="center">'.number_format($line_leads,0).'</td>
												' : '' ).'
					'. (allowView('af-demo',$deal,'fields') ? '
                            <td align="center">'.number_format($line_demo,0).'</td>
												' : '' ).'
					'. (allowView('af-real',$deal,'fields') ? '
                            <td align="center">'.number_format($line_real,0).'</td>
												' : '' ).'
					'. (allowView('af-ftd',$deal,'fields') ? '
                            <td align="center">'.$line_ftd.'</td>
												' : '' ).'
					'. (allowView('af-ftda',$deal,'fields') ? '
                            <td align="center">'.price($line_ftd_amount).'</td>
												' : '' ).'
					'. (allowView('af-depo',$deal,'fields') ? '
                            <td align="center">'.$line_deposits.'</td>
												' : '' ).'
					'. (allowView('af-depoam',$deal,'fields') ? '
                            <td align="center">'.price($line_deposits_amount).'</td>
												' : '' ).'
					'. (allowView('af-bns',$deal,'fields') ? '
                            <td align="center">'.price($line_bonus).'</td>
												' : '' ).'
					'. (allowView('af-withd',$deal,'fields') ? '
                            <td align="center">'.price($line_withdrawal).'</td>
												' : '' ).'
					'. (allowView('af-chrgb',$deal,'fields') ? '
                            <td align="center">'.price($line_chargeback).'</td>
												' : '' ).'
												
					'. (allowView('af-vlm',$deal,'fields' )  && $displayForex==1 ? '
							<td align="center">'.price($line_lots).'</td>
							':'').
							($set->deal_pnl==1 ? '
							<td align="center">'.price($line_pnl).'</td>
							':'').'

					
                            <td align="center">'.price($line_comission).'</td>
                        </tr>';
                        
			$viewsSum+=$line_views;
			$clicksSum+=$line_clicks;
			$totalLeads+=$line_leads;
			$totalDemo+=$line_demo;
			$totalReal+=$line_real;
			$newFTD+=$line_ftd;
			$total_deposits+=$line_deposits;
			$total_depositsAmount+=$line_deposits_amount;
			$ftdAmount+=$line_ftd_amount;
			$totalBonus+=$line_bonus;
			$totalWithdrawal+=$line_withdrawal;
			$totalChargeback+=$line_chargeback;
			$totalSumLots+=$line_lots;
			$totalCommission += $line_comission;
			$totalPNL += $line_pnl;
                }
		$l++;
		// List Merchants
		
		$set->rightBar = '<form action="'.$set->basepage.'" method="get">
				<input type="hidden" name="act" value="main" />
				<table><tr>
					<td>'.timeFrame($from,$to).'</td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr></table>
			</form>';
		
		$month[] = date("Y-m-01", strtotime("-5 Month"));
		$month[] = date("Y-m-01", strtotime("-4 Month"));
		$month[] = date("Y-m-01", strtotime("-3 Month"));
		$month[] = date("Y-m-01", strtotime("-2 Month"));
		$month[] = date("Y-m-01", strtotime("-1 Month"));
		$month[] = date("Y-m-01");
		
		for ($fid=0; $fid<=count($chartAffiliates)-1; $fid++) $whereAffiliates[] = "affiliate_id='".$chartAffiliates[$fid]."'";
		if (count($whereAffiliates) > 0) $where = "(".implode(" OR ",$whereAffiliates).") AND";
		for ($i=0; $i<=count($month)-1; $i++) {
				$new_ftd=0;
				$total_real=0;
				$qq=function_mysql_query("SELECT id,name FROM merchants WHERE valid='1' ORDER BY pos",__FILE__);
				if ($where) while ($ww=mysql_fetch_assoc($qq)) {
					$sql="
					SELECT DISTINCT trader_id
					FROM data_sales 
					WHERE merchant_id ='" . $ww['id'] . "' and  ".$where." rdate BETWEEN '".$month[$i]."' AND '".date("Y-m-d",strtotime("-1 Day", strtotime("+1 Month",strtotime($month[$i]))))."' AND type='deposit' AND trader_id NOT IN 
						(SELECT trader_id FROM data_sales WHERE merchant_id ='" . $ww['id'] . "' and ".$where." rdate < '".$month[$i]."' AND type='deposit' GROUP BY trader_id) 
					GROUP BY trader_id";
					$new_ftd+=mysql_num_rows(function_mysql_query($sql,__FILE__));
$query = "SELECT COUNT(id) FROM data_reg where merchant_id ='" .($ww['id'])."' and ".$where." type='real' AND rdate BETWEEN '".$month[$i]."' AND '".date("Y-m-d",strtotime("-1 Day",strtotime("+1 Month",strtotime($month[$i]))))."'";
					  // die($query);

					$total_real+=mysql_result(function_mysql_query($query,__FILE__),0);
					}
					$buildData[] = '[\''.date("M, Y",strtotime($month[$i])).'\','.($total_real < 1 ? '0' : $total_real).','.($new_ftd < 1 ? '0' : $new_ftd).']'."\n";
					$buildRow2 = array("date"=>date("M, Y",strtotime($month[$i])),"accounts"=>($total_real < 1 ? '0' : $total_real),"ftds"=>($new_ftd < 1 ? '0' : $new_ftd));
					$buildData2[] = $buildRow2;
				
					if( $new_ftd == 0 || $total_real==0)
					$conversion ='0%';
					else
					$conversion = ($new_ftd/$total_real) * 100 ."%";
					
					$coversionbuildRow2 = array("Year"=>date("Y",strtotime($month[$i])),"month"=>date("M",strtotime($month[$i])),"date"=>date("M, Y",strtotime($month[$i])),"conversion"=>$conversion);
					$coversionbuildData2[] = $coversionbuildRow2;
				
				}
			
		$set->content .= '
		<div style="font-family: Arial; font-size: 14px;">';
		
		if ($set->introducingBrokerInterface) 
			$set->content .= '<b>'.lang('Your Introducing Broker Link').':</b> <a href="'.$set->webAddress.'?IB=1&ctag=a'.$set->userInfo['id'].'-b0-p" target="_blank">'.$set->webAddress.'?IB=1&ctag=a'.$set->userInfo['id'].'-b0-p</a><br />';
		else
			$set->content .= '<b>'.lang('Your Affiliate Link').':</b> <a href="'.$set->webAddress.'?ctag=a'.$set->userInfo['id'].'-b0-p" target="_blank">'.$set->webAddress.'?ctag=a'.$set->userInfo['id'].'-b0-p</a><br />';
			
			
		$set->content .= '
		</div>
		<br />';
		
		
		
		if ($set->ShowGraphOnDashBoards) {
		$set->content .= '
		<!--script type="text/javascript" src="https://www.google.com/jsapi"></script>
		<script type="text/javascript">
		google.load("visualization", "1", {packages:["corechart"]});
		google.setOnLoadCallback(drawChart);
		function drawChart() {
		var data = google.visualization.arrayToDataTable([
			[\'Month\', \'Acc.\', \'FTD\'],
			'.($buildData ? implode(",",$buildData) : '').'
			]);

		var options = {
			title: \''.$title.'\',
			pointSize: 3,
			fontSize: 11,
			colors: [\'#205E9F\',\'#019C01\',\'red\']
			};

		var chart = new google.visualization.AreaChart(document.getElementById(\'chart_div\'));
			chart.draw(data, options);
			}
		</script>
		<div id="chart_div" style="width: 100%; height: 100px;margin-bottom: 15px;">
			<div align="center">
				<img border="0" src="/images/ajax.gif" alt="" align="absmiddle" /><br /><br /><b>'.lang('Loading').'...</b>
			</div>
		</div-->';
		
		$set->content .='<script src="'.$set->SSLprefix.'js/highcharts.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
		<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
		<link rel="stylesheet" href="'.$set->SSLprefix.'js/unslider/dist/css/unslider.css">
		<script src="'.$set->SSLprefix.'js/unslider/dist/js/unslider.js"></script>
		<script type="text/javascript">
		' . highchartThemes() . '
		
	    function drawChart2(jsonData) {
		';
		
		if($set->chartTheme == "dark_unica"){
			$tooltip_color = "#ffffff";
		}
		else{
			$tooltip_color = "#000000";
		}
		$set->content .= "
			
	
		 var processed_accounts = new Array();
		 var processed_ftds = new Array();
		 var processed_conversions = new Array();
            for (i = 0; i < jsonData.length; i++) {
                processed_accounts.push([jsonData[i].date, parseInt(jsonData[i].accounts)]);
                processed_ftds.push([jsonData[i].date, parseInt(jsonData[i].ftds)]);
            }
		
		
		$('#chart_div').highcharts({
        chart: {
            zoomType: false,
			height: 250
        },
		 credits: {
			enabled: false
		  },
        title: {
            text: '". lang('Monthly Performance') ."'
        },
        xAxis: [{
            categories: [],
        }],
        yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: 'FTDs',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: 'Accounts',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[0]
                }
            },
            opposite: true
        }],
        tooltip: {
            shared: true,
			formatter: function() {
				
				
                var s = [];
                a = 0;
				account = 0;
				ftd = 0;
				
				
                $.each(this.points, function(i, point) {
					
					symbol = '●';
			
                    s.push('<span style=\"color:' + point.series.color + '\">' + symbol + ' </span><span style=\"color:". $tooltip_color .";\">'+ point.series.name +' : '+
                        '<b>' + point.y +'</b><span>');
						if(i==0)
							account = point.y;
						if(i==1)
							ftd = point.y;
                });
                
				if(ftd == 0 || account==0){
					conversion = 0;
				}
				else{
					conversion = (ftd/account) * 100
					conversion = conversion.toFixed(3);
					s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Conversion') ."  : '+'<b>' +
                        conversion +'%</b><span>')
                }
				
				return s.join(' <br/> ');
            },
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            x: -100,
            verticalAlign: 'top',
            y: 0,
            floating: true,
            backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF',
        },
		/* plotOptions:{
                series:{
                    point: {
                        events:{
                            click: function(e) {                              
							var month = this.name;
								$.prompt('".lang('Do you want to see the chart data for') . " '+ this.name, {
												top:200,
												title: '". lang ('Performance Chart') ."',
												buttons: { '".lang('Yes')."': true, '".lang('Cancel')."': false },
												submit: function(e,v,m,f){
													if(v){
														converttodate(month);
													}
													else{
														//
													}
								
											}
								});                        
                    }
                }
            }
			}
		}, */
        series: [
		{
			data: processed_accounts,
			type: 'column',
			yAxis: 1,
			 name: '". lang('Accounts') ."',
		},
		{
			data: processed_ftds,
			type: 'spline',
			name: '". lang('FTDs') ."',
        }
		]
    });
	}
	function drawChart3(jsonData) {
		";
		
		if($set->chartTheme == "dark_unica"){
			$tooltip_color = "#ffffff";
		}
		else{
			$tooltip_color = "#000000";
		}
		$set->content .= "
		var processed_2015 = new Array();
		 var processed_2016 = new Array();
		 allzero_2015 = true;
		for (i = 0; i < jsonData.length; i++) {
			if(jsonData[i].Year == '2015'){
				if(parseFloat(jsonData[i].conversion) == 0){
					if(allzero_2015 == false)
					{
						allzero_2015 == false;
					}
				}
				else{
					allzero_2015 = false;
				}
				processed_2015.push([jsonData[i].month, parseFloat(jsonData[i].conversion)]);
			}
			else{
				processed_2016.push([jsonData[i].month, parseFloat(jsonData[i].conversion)]);
			}
			
		}
		
		 $('#chart_div2').highcharts({
        title: {
            text: '". lang('Monthly Conversions') ."',
            x: -20 //center
        },
		 credits: {
			enabled: false
		  },
        xAxis: {
            categories: [],
			crosshair: {
			width: 1,
			color:'#000000'
		  },
        },
       yAxis: [{ // Primary yAxis
            labels: {
                format: '{value}',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
            title: {
                text: '". lang('Monthly Conversions') ."',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            },
        }],
        tooltip: {
			crosshairs: true,
            shared: true,
			formatter: function() {
                var s = [];
				var lastMonthVal = 0;
				var newMonthVal;
				var valforarrow = 0;
				changedYearVal = 0;
				
				thisYear = 0;
				lastYear = 0;
				points = this.points;
				points = points.reverse();
                $.each(points, function(i, point) {
				var symbol;
				
                switch ( this.point.graphic.symbolName ) {
                 case 'circle':
					symbol = '●';
					break;
				case 'diamond':
					symbol = '♦';
					break;
				case 'square':
					symbol = '■';
					break;
				case 'triangle':
					symbol = '▲';
					break;
				case 'triangle-down':
					symbol = '▼';
					break;
                }
					
					if(point.series.name == '2016'){
						if(point.x > 0){
							var index = this.series.xData.indexOf(this.x);
							var lastMonthVal = this.series.yData[index-1];
							if(lastMonthVal > 0){
								
								newMonthVal = (point.y - lastMonthVal) / lastMonthVal; 
								newMonthVal = newMonthVal * 100;
								
								valforarrow = newMonthVal.toFixed(2);
								
								if(isNaN(newMonthVal)){
									newMonthVal = '-';
								}
								else
								newMonthVal = newMonthVal.toFixed(2) + '%';
							}
							else{
								valforarrow = 0;
								newMonthVal = '-';
							}
						}
						else{
							newMonthVal = '-';
						}
					}
					
					
					x = point.series.xAxis.names[point.x];
					
					//conversion = Math.round(point.y*1000)/1000;
					conversion = point.y;
					conversion =conversion.toFixed(3);
                    s.push('<span style=\"color:' + point.series.color + '\">' + symbol + ' </span><span style=\"color:". $tooltip_color .";\">'+ x+ ' ' + this.series.name + ': '+
                        '<b>' + conversion +'%</b><span>');
					
					
					if(!allzero_2015){
						if(point.series.name == '2015'){
							thisYear = point.y;
						}
						else 
							lastYear = point.y;
					
						changedYearVal = (thisYear -lastYear) / lastYear;
						changedYearVal = changedYearVal*100;
					} 
                });
			if(!allzero_2015){
				yearvalforarrow = changedYearVal;
				if(isNaN(changedYearVal)){
					changedYearVal = '-';
				}
				else{
					changedYearVal = changedYearVal.toFixed(2) + '%';
				}
			}
			if(newMonthVal !=''){
					if(valforarrow < 0){
						   s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes from last month') ."  : '+'<b>'+ newMonthVal +'</b></span><span style=\"color:#ff0000;\">▼</span>');
						 }
						 else if(valforarrow > 0){
							 s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes from last month') ."  : '+'<b>'+ newMonthVal +'</b><span></span><span style=\"color:#338a16;\">▲</span>');
						 }
						 else{
							  s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes from last month') ."  : '+'<b>'+ newMonthVal +'</b><span>');
						 }
					}
			 if(!allzero_2015){
					 if(yearvalforarrow < 0){
					   s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes since last year') ."  : '+'<b>'+ changedYearVal +'</b></span><span style=\"color:#ff0000;\">▼</span>');
					 }
					 else if(yearvalforarrow >  0){
						 s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes since last year') ."  : '+'<b>'+ changedYearVal +'</b><span></span><span style=\"color:#338a16;\">▲</span>');
					 }
					 else{
						  s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes since last year') ."  : '+'<b>'+ changedYearVal +'</b><span>');
					 }
			 }
				return s.join(' <br/> ');
            },
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle',
            borderWidth: 0
        },
        series: [{
			showInLegend : allzero_2015?false:true,
            name: '2015',
			legendIndex:1,
            data:(allzero_2015?'':processed_2015)
        }, {
            name: '2016',
			legendIndex:0,
            data: processed_2016
        }]
		});
		
	";
	
	$agent = $set->userInfo['level'];
	if($agent == "")
		$agent = "affiliate";
	
	$set->content .='}
		
		function converttodate(dt){
				arr_dt  = dt.split(",");
				var m = new Array();
				m["Jan"] = 1;
				m["Feb"] = 2;
				m["Mar"] = 3;
				m["Apr"] = 4;
				m["May"] = 5;
				m["Jun"] = 6;
				m["Jul"] = 7;
				m["Aug"] = 8;
				m["Sep"] = 9;
				m["Oct"] = 10;
				m["Nov"] = 11;
				m["Dec"] = 12;
				month = m[arr_dt[0]];
				year = arr_dt[1];
				year = year.trim();
				
				var d = new Date(year, month, 0);
				lastDay = d.getDate(); 
				
				start_date = "01"+"-"+month+"-"+year;
				last_date = lastDay+"-"+month+"-"+year;
				from = "from="+ start_date +"&to="+ last_date;
				url = "'. $set->webAddress .$agent.'/reports.php?" + from;
				var win = window.open(url, "_blank");
				win.focus();
		}
    </script>
	<div class="my-slider">
			<div style="float:right;margin-right:10px;margin-top:5px;position:absolute;left:95%;z-index:99999;" class="refresh1"><a class="refresha tooltip" onclick="refreshChart2();" style="font-size: 11px; cursor: pointer;display:none" >'. ($set->chartTheme=='dark_unica'?'<img src=\'images/refresh_white.png\' width=20 />':'<img src=\'images/refresh_black.png\' width=30 />') . '</a></div>
			<ul>
				<li data-slide="0" data-name="'.lang('Performance Chart').'" class="unslider-active">
				<div id="chart_div" style="width: 100%; height: 250px;margin-bottom: 15px;">
					<div align="center">
						<img border="0" src="'.$set->SSLprefix.'images/ajax.gif" alt="" align="absmiddle" /><br /><br /><b>'.lang('Loading').'...</b>
					</div>
				</div>
				</li>
				<li data-slide="1" data-name="'.lang('Conversion Chart').'">
				<div id="chart_div2" style="width: 100%; height: 250px;margin-bottom: 15px;">
					<div align="center">
						<img border="0" src="'.$set->SSLprefix.'images/ajax.gif" alt="" align="absmiddle" /><br /><br /><b>'.lang('Loading').'...</b>
					</div>
				</div>
				</li>
			</ul>
		</div>	
			jsonData = '. json_encode($buildData2) .';
			drawChart2(jsonData);
			
			jsonData2 = '. json_encode($coversionbuildData2) .';
			drawChart3(jsonData2);
			
		window.onload = function() {
			
				$(".my-slider").unslider({
					arrows: false,
					dots:true
				});
				$(".my-slider").show();
			}
			
			$(document).ready(function(){
				
				$(".my-slider").mouseover(function(){
					$(".refresha").show();
				});
				$(".my-slider").mouseout(function(){
					$(".refresha").hide();
				});

				$(".unslider-nav li").on("click",function(){
					slide = $(this).data("slide");
					if(slide == 0){
						$(".refresh1").html("<a class=\"refresha tooltip\" id=\'refreshData2\' onclick=\"refreshChart2();\" style=\"font-size: 11px; cursor: pointer;display:none\">'. ($set->chartTheme=='dark_unica'?'<img src=\''.$set->SSLprefix.'images/refresh_white.png\' width=20 />':'<img src=\''.$set->SSLprefix.'images/refresh_black.png\' width=30 />') . '</a>")
					}
					else{
						$(".refresh1").html("<a class=\"refresha tooltip\" id=\'refreshData3\' onclick=\"refreshChart3();\" style=\"font-size: 11px; cursor: pointer;display:none\">'. ($set->chartTheme=='dark_unica'?'<img src=\''.$set->SSLprefix.'images/refresh_white.png\' width=20 />':'<img src=\''.$set->SSLprefix.'images/refresh_black.png\' width=30 />') . '</a>")
					}
				});
			});
			</script>
			<style>
			.unslider-nav ol{
				padding:10px;
				text-align:center;
			}
			.unslider-nav ol li{
				display:inline;
				cursor:pointer;
			}
			</style>
			';
		
		
		}
		
		$set->content .= '
			<table width="100%" border="0" cellpadding="4" cellspacing="5"><tr>
					'. (allowView('af-impr',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Impressions').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.($viewsSum ? $viewsSum : '0').'</span>
				</td>
					' : '' ).'
					'. (allowView('af-clck',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Clicks').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.($clicksSum ? $clicksSum : '0').'</span>
				</td>
										' : '' ).'
					'. (allowView('af-lead',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Leads').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.$totalLeads.'</span>
				</td>
									' : '' ).'
					'. (allowView('af-demo',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Demo').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.$totalDemo.'</span>
				</td>
									' : '' ).'
					'. (allowView('af-real',$deal,'fields') ? '
				<td class="dashStat">
					'.lang(ptitle('Real Account')).'<br />
					<span style="font-size: 18px; font-weight: bold;">'.$totalReal.'</span>
				</td>
									' : '' ).'
					'. (allowView('af-ftd',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('FTD').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.number_format($newFTD,0).'</span>
				</td>
									' : '' ).'
					'. (allowView('af-ftda',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('FTD Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($ftdAmount,2).'</span>
				</td>
									' : '' ).'
					'. (allowView('af-depo',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Deposits').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.number_format($total_deposits,0).'</span>
				</td>
									' : '' ).'
					'. (allowView('af-depoam',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Deposits Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($total_depositsAmount,2).'</span>
				</td>
									' : '' ).'
					'. (allowView('af-bns',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Bonus').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalBonus,2).'</span>
				</td>
									' : '' ).'
					'. (allowView('af-withd',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Withdrawal').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalWithdrawal,2).'</span>
				</td>
									' : '' ).'
					'. (allowView('af-chrgb',$deal,'fields') ? '
				<td class="dashStat">
					'.lang('Chargeback').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalChargeback,2).'</span>
				</td>
					' : '' ).'
					
							'. (allowView('af-vlm',$deal,'fields') &&  $displayForex==1 ? '
				<td class="dashStat">
					'.lang('Lots').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalSumLots,2).'</span>
				</td>':'').

				($set->deal_pnl==1 ? '
				<td class="dashStat">
					'.lang('PNL').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalPNL,2).'</span>
				</td>':'').'
				
				
					
				<td class="dashStat">
					'.lang('Your commission').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalCommission,2).'</span>
				</td>
			</tr></table>';
		
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
		
		$set->content .= '<form style="margin-top:10px;" method="get">
					<input type="hidden" name="search" value="1" />
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
					<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
					</div><div style="clear:both"></div>
					
					</form>';
		

		$tableContentheader= '<div class="specialTableTitle" style="margin-top: 10px!important;">'.lang('Sub Affiliates Preformance').'</div>';
		
		$tableContent ='
		<table class="'. ($listMerchants!=""?'tablesorter':'normal').'" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
							<tr>
								<th>'.($set->introducingBrokerInterface ? lang('Introduce Broker ID') : lang('Affiliate ID')).'</th>
								<th>'.($set->introducingBrokerInterface ? lang('IB Username') : lang('Affiliate Username')).'</th>
								'. (allowView('af-impr',$deal,'fields') ? '
								<th align="center">'.lang('Impression').'</th>
								': '').'
								'. (allowView('af-clck',$deal,'fields') ? '
								<th align="center">'.lang('Clicks').'</th>
								': '').'
								'. (allowView('af-lead',$deal,'fields') ? '
								<th align="center">'.lang('Leads').'</th>
								': '').'
								'. (allowView('af-demo',$deal,'fields') ? '
								<th align="center">'.lang('Demo').'</th>
								': '').'
								'. (allowView('af-real',$deal,'fields') ? '
								<th align="center">'.lang(ptitle('Accounts')).'</th>
								': '').'
								'. (allowView('af-ftd',$deal,'fields') ? '
								<th align="center">'.lang('FTD').'</th>
								': '').'
								'. (allowView('af-ftda',$deal,'fields') ? '
								<th align="center">'.lang('FTD Amount').'</th>
								': '').'
								'. (allowView('af-depo',$deal,'fields') ? '
								<th align="center">'.lang('Deposits').'</th>
								': '').'
								'. (allowView('af-depoam',$deal,'fields') ? '
								<th align="center">'.lang('Deposits Amount').'</th>
								': '').'
								'. (allowView('af-bns',$deal,'fields') ? '
								<th align="center">'.lang('Bonus').'</th>
								': '').'
								'. (allowView('af-withd',$deal,'fields') ? '
								<th align="center">'.lang('Withdrawal').'</th>
								': '').'
								'. (allowView('af-chrgb',$deal,'fields') ? '
								<th align="center">'.lang('Chargeback').'</th>
								': '').'
									'. (allowView('af-vlm',$deal,'fields') && $displayForex==1 ? '
								<th align="center">'.lang('Lots').'</th>
								':'')
								. ($set->deal_pnl==1 ? '
								<th align="center">'.lang('PNL').'</th>
								':'').'
								<th align="center">% '.lang('Your commission').'</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th align="left"><b>'.lang('Total').':</b></th>
								<th align="center"></th>
								'. (allowView('af-impr',$deal,'fields') ? '
								<th align="center">'.$viewsSum.'</th>
								' : '' ).'
								'. (allowView('af-clck',$deal,'fields') ? '
								<th align="center">'.$clicksSum.'</th>
								' : '' ).'
								'. (allowView('af-lead',$deal,'fields') ? '
								<th align="center">'.number_format($totalLeads,0).'</th>
								' : '' ).'
								'. (allowView('af-demo',$deal,'fields') ? '
								<th align="center">'.number_format($totalDemo,0).'</th>
								' : '' ).'
								'. (allowView('af-real',$deal,'fields') ? '

								<th align="center">'.number_format($totalReal,0).'</th>
																' : '' ).'
								'. (allowView('af-ftd',$deal,'fields') ? '
								<th align="center">'.$newFTD.'</th>
																' : '' ).'
								'. (allowView('af-ftda',$deal,'fields') ? '
								
								<th align="center">'.price($ftdAmount).'</th>
																' : '' ).'
								'. (allowView('af-depo',$deal,'fields') ? '
								<th align="center">'.$total_deposits.'</th>
																' : '' ).'
								'. (allowView('af-depoam',$deal,'fields') ? '
								<th align="center">'.price($total_depositsAmount).'</th>
																' : '' ).'
								'. (allowView('af-bns',$deal,'fields') ? '
								<th align="center">'.price($totalBonus).'</th>
																' : '' ).'
								'. (allowView('af-withd',$deal,'fields') ? '
								<th align="center">'.price($totalWithdrawal).'</th>
																' : '' ).'
								'. (allowView('af-chrgb',$deal,'fields') ? '
								<th align="center">'.price($totalChargeback).'</th>
																' : '' ).'
																
									'. (allowView('af-chrgb',$deal,'fields') &&  $displayForex==1 ? '
								<th align="center">'.price($totalSumLots).'</th>
								':'')
								. ($set->deal_pnl==1 ? '
								<th align="center">'.price($totalPNL).'</th>
								':'').'
								
								<th align="center">'.price($totalCommission).'</th>
							</tr>
						</tfoot>
						<tbody>
						'.$listMerchants.'
					</table>';
				$tableContentFooter	 = getPager();
		
		$set->content .= $tableContentheader.$tableContent.$tableContentFooter;
		if ($hasResults)
		excelExporter($tableContent,'Master-Affiliate-'.$set->userInfo['id']. '---');
		
		theme();
		break;
	
	
                
                
	case "get_code":
		$ww=dbGet($id,"sub_banners");
		$set->basepage = 'Get Banner Code';
		if (!$ww['id'] OR !$ww['valid']) _goto();
		$profileqq=function_mysql_query("SELECT id,name,url FROM affiliates_profiles WHERE affiliate_id='".$set->userInfo['id']."' AND valid='1' ORDER BY id ASC",__FILE__);
		while ($profileww=mysql_fetch_assoc($profileqq)) {
			$listProfiles .= '<option value="'.$profileww['id'].'" '.($profileww['id'] == $profile_id ? 'selected' : '').'>['.$profileww['name'].'] '.$profileww['url'].' (Site ID: '.$profileww['id'].')</option>';
			if (!$setProfile) $setProfile = ($profile_id == $profileww['id'] ? $profileww['id'] : '');
			}
			
		$tag='a'.$set->userInfo['id'].'-b'.$ww['id'].'-p'.$setProfile; // Creat CTag
		
if ($ww['type'] == "link") {
$srcFile = $ww['title'];
$link = $set->webAddress.'click_sub.php?ctag='.$tag;
$code = '<!-- '.$set->webTitle.' Affiliate Code -->
<a href="'.$link.'" target="_blank">'.$ww['title'].'</a>
<!-- // '.$set->webTitle.' Affiliate Code -->';
$preview = '<a href="'.$link.'" target="_blank">'.$ww['title'].'</a>';
	} else if ($ww['type'] == "script") {
	$srcFile = 'Click Here';
	$link = $set->webAddress.'sub.g?ctag='.$tag;
	$code = '<!-- '.$set->webTitle.' Affiliate Code -->
<iframe src="'.$link.'" width="'.$ww['width'].'" height="'.$ww['height'].'" frameborder="0" scrolling="no"></iframe>
<!-- // '.$set->webTitle.' Affiliate Code -->';
$preview = $ww['scriptCode'];
	} else {
	$link = $set->webAddress.'sub.g?ctag='.$tag;
$code = '<!-- '.$set->webTitle.' Affiliate Code -->
<script type= "text/javascript" language="javascript" src="'.$link.'"></script>
<!-- // '.$set->webTitle.' Affiliate Code -->';
$preview = getBanner($ww['file'],80);
}
if ($ww['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.$ww['file'].'" alt="" />';
$codelink = '<br />
<table>
	<tr>
		<td>'.lang('Direct Link').':</td><td><input type="text" name="" value="'.str_replace("sub.g","click_sub.php",$link).'" onclick="this.focus(); this.select();" style="width: 300px;" /></td>
	</tr><tr>
		<td valign="top">'.lang('HTML Code').':</td><td><textarea cols="40" rows="4" onclick="this.focus(); this.select();"><a href="'.str_replace("sub.g","click_sub.php",$link).'">'.$srcFile.'</a></textarea></td>
	</tr>
</table>';

	$set->pageTitle = lang('Get Tracking Code');
			
						$set->content.='<style>
						#bottom_table , 
						.headerSite,
						.headerSite + *,
						.headerSite + * +*{
							display:none!important;
						}
						body .content{
							padding-bottom:0;
							 margin-top: 1%;
							margin-left: 1%;
						}
						.content td div:last-child img{
							vertical-align: top;
						}
						
						
					</style>';
					
	$set->content .= '<form method="get">
					<input type="hidden" name="act" value="get_code" />
					<input type="hidden" name="id" value="'.$id.'" />
					<b>'.lang('Choose Profile').':</b> <select name="profile_id" onchange="form.submit();"><option value="">'.lang('General').'</option>'.$listProfiles.'</select><br />
					<table width="100%" border="0" cellpadding="0" cellspacing="3">
						<tr>
							<td width="50%" align="left" valign="top">
								<div class="normalTableTitle">'.lang('Preview').':</div><br />
								<img style="width:700px;max-height:500px;max-width:100%;" src="'. $ww['file'].  '" alt=""/>
							</td>
							<td width="50%" align="left" valign="top">
								<div class="normalTableTitle">'.lang('Copy code to your site').':</div>
								<textarea cols="85" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.$code.'</textarea>'.$codelink.'
							</td>
						</tr>
					</table>
					</form>';
		theme();
		break;
	
	case "creative":
			if ($set->introducingBrokerInterface) 
			$pageTitle = lang('Sub IB Creative Materials');
		else
			$pageTitle = lang('Sub Affiliation Creative Materials');
		
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">Marketing Tools - Sub Affiliation Creative Materials</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		
		if ($type) $where .= " AND type='".$type."'";
		
		$getPos = 30;
		$pgg=$_GET['pg'] * $getPos;
		
		$qq=function_mysql_query("SELECT * FROM sub_banners WHERE valid='1' ".$where." ORDER BY id DESC LIMIT $pgg,$getPos",__FILE__);
		$bottomNav = GetPages("sub_banners","WHERE valid='1' ".$where,$pg,$getPos);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM sub_stats WHERE affiliate_id='".$set->userInfo['id']."' AND banner_id='".$ww['id']."'",__FILE__));
			$tag='a'.$set->userInfo['id'].'-b'.$ww['id'].'-p'.$setProfile; // Creat CTag
					
			if ($ww['type'] == "link") {
				$srcFile = $ww['title'];
				$link = $set->webAddress.'click_sub.php?ctag='.$tag;
				$code = '<!-- '.$set->webTitle.' Affiliate Code -->
				<a href="'.$link.'" target="_blank">'.$ww['title'].'</a>
				<!-- // '.$set->webTitle.' Affiliate Code -->';
				$preview = '<a href="'.$link.'" target="_blank">'.$ww['title'].'</a>';
			}
			else if ($ww['type'] == "script") {
				$srcFile = 'Click Here';
				$link = $set->webAddress.'sub.g?ctag='.$tag;
				$code = '<!-- '.$set->webTitle.' Affiliate Code -->
				<iframe src="'.$link.'" width="'.$ww['width'].'" height="'.$ww['height'].'" frameborder="0" scrolling="no"></iframe>
				<!-- // '.$set->webTitle.' Affiliate Code -->';
				$preview = $ww['scriptCode'];
			} 
			else {
				$link = $set->webAddress.'sub.g?ctag='.$tag;
				$code = '<!-- '.$set->webTitle.' Affiliate Code -->
				<script type= "text/javascript" language="javascript" src="'.$link.'"></script>
				<!-- // '.$set->webTitle.' Affiliate Code -->';
				$preview = getBanner($ww['file'],80);
			}
			if ($ww['type'] == "image") $srcFile = '<img src="'.$set->webAddress.$ww['file'].'" width="173" height="173" alt="" />';
			$codelink = '<br />';
			$allCreative .= '<div class="show-creative-table">
										<div class="table-responsive"><table class="creative-table">
										<tfoot>
										<tr>
										<td class="creative-img">
										'.$srcFile.'
										</td>
										<td class="creative-details">
											<div class="creative-details-table">
												<div class="creative-details-list">
													<strong>Creative Name</strong>
													<p>'.$ww['title'].'</p>
												</div>
												<div class="creative-details-list">
													<strong>Format</strong>
													<p>'.($ww['type'] == "image" || $ww['type'] == "flash" ? getFixedSizeBanner($ww['file'],50,50) : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</p>
												</div>
												<div class="creative-details-list">
													<strong>Landing URL</strong>
													<p>'.$ww['url'].'</p>
												</div>
												<div class="creative-details-list">
													<strong>Size (WxH)</strong>
													<p>'.($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</p>
												</div>
												<div class="creative-details-list">
													<strong>Impressions</strong>
													<p>'.number_format($totalTraffic['totalViews'],0).'</p>
												</div>
												<div class="creative-details-list">
													<strong>Clicks</strong>
													<p>'.number_format($totalTraffic['totalClicks'],0).'</p>
												</div>
											</div>
										</td>
										<td class="creative-copy-link">
									<div class="copy-link">
										<div class="copy-link-heading">
											<h4>Click URL</h4>
										</div>
										<div class="copy-link-input">
											<input type="text" name="" value="'.str_replace("sub.g","click_sub.php",$link).'" onclick="this.focus(); this.select();" />
										</div>
										<div class="copy-buttons">
											<button type="button" class="ClickURL" onclick="return copy_url()">Copy Click URL <img src="../assets/images/img-new/copy.svg"></button>
											<!-- <span class="custom-tooltip" id="custom-tooltip" style="display:none;">Copied!</span> -->
											<button type="button" class="btn GetHTML" data-toggle="modal" data-target="#exampleModalCenter">
												Get HTML code <span>&#60;/&#62;</span> 
											  </button>
											  
											  <!-- Modal -->
											  <div class="modal fade HtmlCode-modal" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
												<div class="modal-dialog modal-dialog-centered" role="document">
												  <div class="modal-content html-modal-content">
													<div class="modal-header html-model-header">
													  <h5 class="modal-title" id="exampleModalLongTitle">HTML code</h5>
													  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													  </button>
													</div>
													<div class="modal-body html-model-body">
													  <div class="html-code-body">
													  <form method="get">
														<div class="profile-div">
															<div class="profile-section">
																<div class="profile-lable">
																	<input type="hidden" name="act" value="get_code" />
																	<input type="hidden" name="id" value="'.$id.'" />
																	<b>'.lang('Choose Profile').':</b> <br />
																	<div class="profile-lable-input">
																		<div class="form-group">
																			<select class="form-control" id="exampleFormControlSelect1" name="profile_id" onchange="form.submit();"><option value="">'.lang('General').'</option>'.$listProfiles.'</select>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="text-area-div">
															<textarea id="creative_code" onclick="this.focus(); this.select();">'.$code.'</textarea>'.$codelink.'	
														</div>
													</form>
												</div>
											</div>
													<div class="modal-footer html-model-footer">
													  <div class="html-code-footer-button">
													  <button onclick="return copy_code('.$ww['id'].')">Copy code <img src="../assets/images/img-new/copyWhite.svg"></button>
													  <button id="dwn-btn">Download code<img src="../assets/images/img-new/coding.svg"></button>
													  <button onclick="return textToPng();">Download image<img src="../assets/images/img-new/image.svg"></button>
													  </div>
													</div>
												  </div>
												</div>
											  </div>

										</div>
									</div>
								</td>
									</tr></tfoot>
									</table></div></div>
									<script>
										function copy_code(id){
											var copyText = document.getElementById("creative_code");
											var copyText = copyText.value;

											var r = document.createRange();
											r.selectNode(document.getElementById("creative_code"));
											window.getSelection().removeAllRanges();
											window.getSelection().addRange(r);
											document.execCommand("copy");
											window.getSelection().removeAllRanges();
											$("#creative_code").focus(); 
											$("#creative_code").select();
										}
									</script>
									<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
									<script>
										function download(filename, text) {
											var element = document.createElement("a");
											element.setAttribute("href", "data:text/plain;charset=utf-8," + encodeURIComponent(text));
											element.setAttribute("download", filename);
										element.style.display = "none";
										document.body.appendChild(element);
										element.click();
										document.body.removeChild(element);
									}

									// Start file download.
									document.getElementById("dwn-btn").addEventListener("click", function(){
											// Generate download of hello.txt file with some content
										var text = document.getElementById("creative_code").value;
										var filename = "file.txt";
										download(filename, text);
									}, false);

									function textToPng() {
										html2canvas($("#creative_code"), {
											onrendered: function (canvas) {
												var img = canvas.toDataURL("image/png")
												window.open(img);
											}
										});
							
									}
									</script>
									';
								
			
			}
		
                        
				
					
	$set->content .= getImageGrowerScript();
					
	$set->content .= '	<div class="sub-affiliation-page">
	<div class="sub-search-section creative-page-filter">
		<div class="search-section">
			<div class="search-wrp">
				<p>Search creative</p>
				<div class="search-box">
					<input type="text" name="q" value="'.$q.'" />
					<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
				</div>
			</div>
		</div>
		<div class="sub-img-section">
			<div class="sub-creative-img">
				<div class="form-group">
					<label for="exampleFormControlSelect1">Creative type:</label>
					<select class="form-control img-select" id="content_type" name="type">
						<option value="">'.lang('All').'</option>
						<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
						<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
						<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
					</select>
				</div>
			</div>
		</div>
	</div>';
	if($allCreative!=''){
			$set->content .=  '<div class="creatives-data-wrp">'
								.$allCreative.'
							</div>';
		}

		$set->content .= '</div>';	
						
                $set->content .= '<form method="get" action="'.$set->SSLprefix.$set->basepage.'?act=creative">  
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>
								<td align="left" class="blueText">'.lang('Creative Type').':</td>
								<td align="left">
									<select id="content_type" name="type" style="width: 150px;">
										<option value="">'.lang('All').'</option>
										<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
										<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
										<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
									</select>
									</td>
								<td></td>
								<td width="80"></td>
								<td align="right"><input id="redirect" type="submit" value="'.lang('Search').'" /></td>
							</tr></table>
							</form>
                                                        <script>
                                                            $("#redirect").click(function() {
                                                                var type = $("#content_type").val();
                                                                var url  = window.location.href.split("?")[0].split("#")[0] + "?act=creative&type=" + type;
                                                                window.location.href = url;
                                                                return false;
                                                            });
                                                        </script>
						</div>
                                                
						<div class="normalTableTitle">'.lang('Creatives List') . (!empty($merchant_id) ? ' '.lang('for').': <b>'.listMerchants($merchant_id,1) : "" ).'</b></div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td class="creative-name" >'.lang('Creative Name').'</td>
								<td>'.lang('Preview').'</td>
								<td>'.lang('Format').'</td>
								<td>'.lang('Size').' ('.lang('Width').' x '.lang('Height').')</td>
								<td>'.lang('Landing URL').'</td>
								<td>'.lang('Impressions').'</td>
								<td>'.lang('Clicks').'</td>
							</tr></thead><tfoot>'.$allCreative.'</tfoot>
						</table><br />
					<div align="left">'.$bottomNav.'</div>';
		theme();
		break;
	
	}

?>