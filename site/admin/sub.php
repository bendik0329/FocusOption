<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
require_once('common/subAffiliateData.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'sub_banners';

//https handling
$set->webAddress = ($set->isHttps?$set->webAddressHttps:$set->webAddress);

ini_set('memory_limit', '-1');
set_time_limit(0);
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
switch ($act) {
	
	// ----------------------------------------- [ Edit Banner ] -----------------------------------------
	
		default:
		
		
	$affiliate_id = isset($_GET['affiliate_id']) ? $_GET['affiliate_id'] : '0' ;
	if ($affiliate_id =='')
		$affiliate_id = 0;
		// if ($affiliate_id>0) 
//			$SESSION['affiliate_id'] = $affiliate_id;
	//	else 	if ($affiliate_id==0 && isset($SESSION['affiliate_id']) && $SESSION['affiliate_id']>0 ) {
		//	$affiliate_id = 	$SESSION['affiliate_id'] ;
		//}
	
	
	
	if ($set->introducingBrokerInterface) {
		$set->pageTitle = lang('Sub IBs');
	}
	else{
		$pageTitle = lang('Sub Affiliates') .' ' . lang('Report')   . ($affiliate_id>0 ? " " . lang('For') . ' #' . $affiliate_id : '') ;
		
			$set->breadcrumb_title = $pageTitle;
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="' . $set->SSLprefix.$set->uri . '" class="arrow-left">'.$pageTitle.'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

	}
		
		$sql = 'SELECT * FROM merchants WHERE  valid = 1 ';
                    $merchantsArray = array();
					$displayForex = 0;
					$mer_rsc = function_mysql_query($sql,__FILE__);
					while ($arrMerchant = mysql_fetch_assoc($mer_rsc)) {
						
						if (strtolower($arrMerchant['producttype'])=='forex')
							$displayForex = 1;
					
						$merchantsArray[$arrMerchant['id']] = $arrMerchant;
		}

					
		
		
		
			$qry = "select * from affiliates where id in ( select distinct (refer_id) as id from affiliates) and valid = 1";
		$rsc = function_mysql_query($qry,__FILE__);
		
		// var_dump($_POST);
		// die();
		$allAffiliates = "";
	
		while ($row = mysql_fetch_assoc($rsc)) {
		$allAffiliates .= '<option '.($row['id']==$affiliate_id ? " selected " : "").' value="'.$row['id'].'">'. $row['username'] . ' (' . $row['id'] . ')</option>';
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
                $sql = 'SELECT sub_com AS sub_com FROM affiliates WHERE valid = 1 AND id = ' . $affiliate_id . ' LIMIT 0, 1;';
                // echo $sql;
				$arrAffSubCom = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                $floatAffSubCom = (float) $arrAffSubCom['sub_com'];
				
                unset($arrAffSubCom);
                
                $sql = "SELECT id,username FROM affiliates WHERE valid = 1 AND refer_id = " . $affiliate_id;
		$affiliateqq = function_mysql_query($sql,__FILE__);
             
			 $hasResults = false;
			 if ($affiliate_id>0)  
		while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {
		
	 			 $total_leads =  $total_demo=  $total_real= $new_ftd= $totalDeposits=  $depositsAmount= $bonus=  $withdrawal= $chargeback= $thisComis = $totalDeposits = $depositsAmount =  0;

			 
			 $hasResults=true;
			 
			
			
                        // List of wallets.
                        $arrWallets = [];
                        $sql = "SELECT DISTINCT wallet_id AS wallet_id FROM merchants WHERE valid = 1;";
                        $resourceWallets = function_mysql_query($sql,__FILE__);
                        
                        while ($arrWallet = mysql_fetch_assoc($resourceWallets)) {
                            $arrWallets[$arrWallet['wallet_id']] = false;
                            unset($arrWallet);
                        }
                        
                        
			// $merchantqq = function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY id;",__FILE__);
                        
			// while ($merchantww = mysql_fetch_assoc($merchantqq)) {
			
				$data = getSubAffiliateData($from,$to,$affiliateww['id'],$affiliate_id,'full',"admin",$group_id);
			
			$chartAffiliates[] = $affiliateww['id'];
			$listMerchants .= '<tr>
                            <td style="color: #646464;"><b>'.$affiliateww['id'].'</b></td>
                            <td style="color: #646464;"><b><a target="_blank" href="admin/affiliates.php?act=new&id='. $affiliateww['id'] .'">'.$affiliateww['username'].'</a></b></td>
                            <td align="center">'.$data['views'].'</td>
                            <td align="center">'.$data['clicks'].'</td>
                            <td align="center">'.number_format($data['leads'],0).'</td>
                            <td align="center">'.number_format($data['demo'],0).'</td>
                            <td align="center">'.number_format($data['real'],0).'</td>
                            <td align="center">'.$data['ftd'].'</td>
                            <td align="center">'.price($data['ftd_amount']).'</td>
                            <td align="center">'.$data['deposits'].'</td>
                            <td align="center">'.price($data['deposits_amount']).'</td>
                            <td align="center">'.price($data['bonus']).'</td>
                            <td align="center">'.price($data['withdrawal']).'</td>
                            <td align="center">'.price($data['chargeback']).'</td>
                            	'. ( $displayForex==1 ? '
							<td align="center">'.price($data['lots']).'</td>
							':'')
							. ( $set->deal_pnl==1 ? '
							<td align="center">'.price($data['pnl']).'</td>
							':'').'
                            <td align="center">'.price($data['commission']).'</td>
                        </tr>';
                        
			$viewsSum+=$data['views'];
			$clicksSum+=$data['clicks'];
			$totalLeads+=$data['leads'];
			$totalDemo+=$data['demo'];
			$totalReal+=$data['real'];
			$newFTD+=$data['ftd'];
			$total_deposits+=$data['deposits'];
			$total_depositsAmount+=$data['deposits_amount'];
			$ftdAmount+=$data['ftd_amount'];
			$totalBonus+=$data['bonus'];
			$totalWithdrawal+=$data['withdrawal'];
			$totalChargeback+=$data['chargeback'];
			$totalSumLots+=$data['lots'];
			$totalCommission += $data['commission'];
			$totalPNL += $data['pnl'];
                }
		$l++;
		// List Merchants
		
		$set->rightBar = '<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
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
					WHERE merchant_id ='" . $ww['id'] . "' and  ".$where." rdate BETWEEN '".$month[$i]."' AND '".date("Y-m-d",strtotime("-1 Day", strtotime("+1 Month",strtotime($month[$i]))))." 23:59:59' AND type='deposit' AND trader_id NOT IN 
						(SELECT trader_id FROM data_sales WHERE merchant_id ='" . $ww['id'] . "' and ".$where." rdate < '".$month[$i]."' AND type='deposit' GROUP BY trader_id) 
					GROUP BY trader_id";
					$new_ftd+=mysql_num_rows(function_mysql_query($sql,__FILE__));
$query = "SELECT COUNT(id) FROM data_reg where merchant_id ='" .($ww['id'])."' and ".$where." type='real' AND rdate BETWEEN '".$month[$i]."' AND '".date("Y-m-d",strtotime("-1 Day",strtotime("+1 Month",strtotime($month[$i]))))." 23:59:59'";
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
		
/* 		if ($set->introducingBrokerInterface) 
			$set->content .= '<b>'.lang('Your Introducing Broker Link').':</b> <a href="'.$set->webAddress.'?IB=1&ctag=a'.$set->userInfo['id'].'-b0-p" target="_blank">'.$set->webAddress.'?IB=1&ctag=a'.$set->userInfo['id'].'-b0-p</a><br />';
		else
			$set->content .= '<b>'.lang('Your Affiliate Link').':</b> <a href="'.$set->webAddress.'?ctag=a'.$set->userInfo['id'].'-b0-p" target="_blank">'.$set->webAddress.'?ctag=a'.$set->userInfo['id'].'-b0-p</a><br />';
			 */

			 
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
				<img border="0" src="'.$set->SSLprefix.'images/ajax.gif" alt="" align="absmiddle" /><br /><br /><b>'.lang('Loading').'...</b>
			</div>
		</div-->';
		
		
		
		$set->content .='<script src="'.$set->SSLprefix.'js/highcharts.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
		<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
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
            text: '".  lang('Monthly Performance') ."'
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
                text: '". lang('Conversions') ."',
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
				url = "'. $set->SSLprefix .$agent.'/reports.php?" + from;
				var win = window.open(url, "_blank");
				win.focus();
		}
    </script>
	<link rel="stylesheet" href="'.$set->SSLprefix.'js/unslider/dist/css/unslider.css">
	<script src="'.$set->SSLprefix.'js/unslider/dist/js/unslider.js"></script>
	
		
		<div class="my-slider">
			<div style="float:right;margin-right:10px;margin-top:5px;position:absolute;left:95%;z-index:99999;" class="refresh1"><a class="refresha tooltip" onclick="refreshChart2();" style="font-size: 11px; cursor: pointer;display:none" >'. ($set->chartTheme=='dark_unica'?'<img src=\''.$set->SSLprefix.'images/refresh_white.png\' width=20 />':'<img src=\''.$set->SSLprefix.'images/refresh_black.png\' width=30 />') . '</a></div>
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
		
		<script>
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
				<td class="dashStat">
					'.lang('Impressions').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.($viewsSum ? $viewsSum : '0').'</span>
				</td>
				<td class="dashStat">
					'.lang('Clicks').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.($clicksSum ? $clicksSum : '0').'</span>
				</td>
				<td class="dashStat">
					'.lang('Leads').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.$totalLeads.'</span>
				</td>
				<td class="dashStat">
					'.lang('Demo').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.$totalDemo.'</span>
				</td>
				<td class="dashStat">
					'.lang(ptitle('Real Account')).'<br />
					<span style="font-size: 18px; font-weight: bold;">'.$totalReal.'</span>
				</td>
				<td class="dashStat">
					'.lang('FTD').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.number_format($newFTD,0).'</span>
				</td>
				<td class="dashStat">
					'.lang('FTD Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($ftdAmount,2).'</span>
				</td>
				<td class="dashStat">
					'.lang('Deposits').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.number_format($total_deposits,0).'</span>
				</td>
				<td class="dashStat">
					'.lang('Deposits Amount').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($total_depositsAmount,2).'</span>
				</td>
				<td class="dashStat">
					'.lang('Bonus').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalBonus,2).'</span>
				</td>
				<td class="dashStat">
					'.lang('Withdrawal').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalWithdrawal,2).'</span>
				</td>
				<td class="dashStat">
					'.lang('Chargeback').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalChargeback,2).'</span>
				</td>
				
					'. ( $displayForex==1 ? '
				<td class="dashStat">
					'.lang('Lots').'<br />
					<span style="font-size: 18px; font-weight: bold;">'.price($totalSumLots,2).'</span>
				</td>':''). 
				( $set->deal_pnl==1 ? '
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
					<form method="get">
					<!--input type="hidden" name="affiliate_id" value="'.$affiliate_id.'" /-->
					<input type="hidden" name="auto_time_frame" value="'.$auto_time_frame.'" />
					<input type="hidden" name="from" value="'.$from.'" />
					<input type="hidden" name="to" value="'.$to.'" />
			<table>
			<tr>
			<td style="margin-left:50px;">'.lang('Choose Master Affiliate').':<select name="affiliate_id" onchange="form.submit();">
			<option value="">'.lang('None').'</option>
				'.$allAffiliates.'</select></td><td></td><td>
									
									
			'.($set->export ? '<td><div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
					<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
					</div></td><div style="clear:both"></div>
					</tr>
					</table>
					</form>';
		
		$tableContentheader= '<div class="specialTableTitle" style="margin-top: 10px!important;">'.lang('Merchant Preformance').'</div>';
		
		$tableContent ='
		<table class="'. ($listMerchants!=""?'tablesorter':'normal').'" width="100%" border="0" cellpadding="0" cellspacing="0">
		<thead>
							<tr>
								<th>'.($set->introducingBrokerInterface ? lang('Introduce Broker ID') : lang('Affiliate ID')).'</th>
								<th>'.($set->introducingBrokerInterface ? lang('IB Username') : lang('Affiliate Username')).'</th>
								<th align="center">'.lang('Impression').'</th>
								<th align="center">'.lang('Clicks').'</th>
								<th align="center">'.lang('Leads').'</th>
								<th align="center">'.lang('Demo').'</th>
								<th align="center">'.lang(ptitle('Accounts')).'</th>
								<th align="center">'.lang('FTD').'</th>
								<th align="center">'.lang('FTD Amount').'</th>
								<th align="center">'.lang('Deposits').'</th>
								<th align="center">'.lang('Deposits Amount').'</th>
								<th align="center">'.lang('Bonus').'</th>
								<th align="center">'.lang('Withdrawal').'</th>
								<th align="center">'.lang('Chargeback').'</th>
									'. ( $displayForex==1 ? '
								<th align="center">'.lang('Lots').'</th>
								':''). ( $set->deal_pnl==1 ? '
								<th align="center">'.lang('PNL').'</th>
								':'').'
								<th align="center">% '.lang('Your commission').'</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th align="left"><b>'.lang('Total').':</b></th>
								<th align="center"></th>
								<th align="center">'.$viewsSum.'</th>
								<th align="center">'.$clicksSum.'</th>
								<th align="center">'.number_format($totalLeads,0).'</th>
								<th align="center">'.number_format($totalDemo,0).'</th>
								<th align="center">'.number_format($totalReal,0).'</th>
								<th align="center">'.$newFTD.'</th>
								<th align="center">'.price($ftdAmount).'</th>
								<th align="center">'.$total_deposits.'</th>
								<th align="center">'.price($total_depositsAmount).'</th>
								<th align="center">'.price($totalBonus).'</th>
								<th align="center">'.price($totalWithdrawal).'</th>
								<th align="center">'.price($totalChargeback).'</th>
									'. ( $displayForex==1 ? '
								<th align="center">'.price($totalSumLots).'</th>
								':''). ( $set->deal_pnl==1 ? '
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
		excelExporter($tableContent,'Master-Affiliate-'.$set->$affiliate_id. '---');
		
		theme();
		break;
	
	
	
	
	case "del_banner":
		$ww=dbGet($id,$appTable);
		if (file_exists($ww['file'])) ftDelete($ww['file']);
		function_mysql_query("DELETE FROM ".$appTable." WHERE id='".$ww['id']."'",__FILE__);
		die('<a href="'.$set->SSLprefix.$set->basepage.'?merchant_id='.$ww['merchant_id'].'">Go Back!</a>');
		break;
		
	case "save_banner":
		$db['last_update'] = dbDate();
		if (chkUpload('file')) {
			$getOldBanner=dbGet($db['id'],$appTable);
			if (file_exists($getOldBanner['file'])) ftDelete($getOldBanner['file']);
			$randomFolder =mt_rand(10000000, 99999999);
			$folder = 'files/sub_banners/tmp/' . $randomFolder ."/";
			 if (!is_dir('files/sub_banners/tmp')) {
				 mkdir('files/sub_banners/tmp');
			 }
			 if (!is_dir($folder)) {
				 mkdir($folder);
			 }
			$db['file'] = UploadFile('file','5120000','jpg,gif,swf,jpeg,png','',$folder);
			$exp=explode(".",$db['file']);
			$ext = strtolower($exp[count($exp)-1]);
			if ($ext == "swf") $db['type'] = "flash";
				else if ($ext == "jpg" OR $ext == "jpeg" OR $ext == "gif" OR $ext == "png") $db['type'] = "image";
				else $db['type'] = "link";
			list($db['width'],$db['height']) = getimagesize($db['file']);
			}
		if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
		dbAdd($db,$appTable);
		_goto($set->SSLprefix.$set->basepage.'?act=edit_banner&id='.$db['id'].'&ty=1');
		break;
	
	case "edit_banner":
		$db=dbGet($id,$appTable);
		$adminww=dbGet($db['admin_id'],"admins");
		$tag='a'.$affiliate_id.'-b'.$db['id'].'-p'.$profile_id; // Creat CTag
		if ($db['type'] == "link" OR $db['type'] == "image") {
$link = $set->webAddress.'click_sub.php?ctag='.$tag;
$code = '<!-- '.$set->webTitle.' Affiliate Code -->
<a href="'.$link.'" target="_blank">'.$db['title'].'</a>
<!-- // '.$set->webTitle.' Affiliate Code -->';
$preview = '<a href="'.$link.'" target="_blank">'.$db['title'].'</a>';
	} else {
	$link = $set->webAddress.'sub.g?ctag='.$tag;
$code = '<!-- '.$set->webTitle.' Affiliate Code -->
<script type= "text/javascript" language="javascript" src="'.$link.'"></script>
<!-- // '.$set->webTitle.' Affiliate Code -->';
$preview = getBanner($db['file'],80);
}
if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.$db['file'].'" alt="" />';
$codelink = '<br />
<table>
	<tr>
		<td>'.lang('Direct Link').':</td><td><input type="text" name="" value="'.str_replace("sub.g","click_sub.php",$link).'" onclick="this.focus(); this.select();" style="width: 300px;" /></td>
	</tr><tr>
		<td valign="top">'.lang('HTML Code').':</td><td><textarea cols="40" rows="4" onclick="this.focus(); this.select();"><a href="'.str_replace("sub.g","click_sub.php",$link).'">'.$srcFile.'</a></textarea></td>
	</tr>
</table>';
// All Affiliates
$affiliateqq=function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
while ($affiliateww=mysql_fetch_assoc($affiliateqq)) $allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $affiliate_id ? 'selected' : '').'>['.$affiliateww['id'].'] '.$affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';

if ($affiliate_id) {
	$profileqq=function_mysql_query("SELECT id,name,url FROM affiliates_profiles WHERE affiliate_id='".$affiliate_id."' AND valid='1' ORDER BY id ASC",__FILE__);
	while ($profileww=mysql_fetch_assoc($profileqq)) $allProfiles .= '<option value="'.$profileww['id'].'" '.($profileww['id'] == $profile_id ? 'selected' : '').'>['.$profileww['name'].'] '.$profileww['url'].' (Site ID: '.$profileww['id'].')</option>';
	}

// All Affiliates

	//$set->pageTitle = lang('Get Creative Code');
		$set->breadcrumb_title =  lang('Get Creative Code');
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'" class="arrow-left">'.lang('Get Creative Code').'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
	$set->content .= '<div align="left">
					<table width="100%"><tr>
						<td width="60%" align="left" valign="top">
							<div class="normalTableTitle">'.lang('Creative Details').':</div><br />
							<form method="post" enctype="multipart/form-data">
							<input type="hidden" name="db[id]" value="'.$db['id'].'" />
							<input type="hidden" name="act" value="save_banner" />
							<table cellspacing="8" cellpadding="0">
							'.($ty ? '<tr><td colspan="2" class="Must">- '.lang('The page is up to date').' ('.dbDate().')</td></tr>' : '').'
							<tr><td class="blueText">'.lang('ID').':</td><td class="greenText">#'.$db['id'].'</td></tr>
							<tr><td class="blueText">'.lang('Uploaded By').':</td><td class="greenText">'.$adminww['first_name'].' '.$adminww['last_name'].'</td></tr>
							<tr><td class="blueText">'.lang('Uploaded Date').':</td><td class="greenText">'.dbDate($db['rdate']).'</td></tr>
							<tr><td class="blueText">'.lang('Last Update').':</td><td class="greenText">'.dbDate($db['last_update']).'</td></tr>
							<tr><td class="blueText"></td><td class="greenText"><input type="checkbox" name="valid" '.($db['valid'] ? 'checked' : '').' /> '.lang('Publish Banner').'</td></tr>
							<tr><td class="blueText">'.lang('Creative Name').':</td><td><input type="text" name="db[title]" value="'.$db['title'].'" /></td></tr>
							<tr><td class="blueText">'.lang('Creative URL').':</td><td><input type="text" name="db[url]" value="'.$db['url'].'" /></td></tr>
							<tr><td class="blueText">'.lang('Creative ALT').':</td><td><input type="text" name="db[alt]" value="'.$db['alt'].'" /></td></tr>
							<tr><td class="blueText">'.lang('Creative File').':</td><td>'.(strpos($db['file'],"/tmp/")?fileField('file','../images/wheel.gif'):fileField('file',$db['file'])).'</td></tr>
							<tr><td class="blueText"></td><td><input type="submit" value="'.lang('Save').'" /></td></tr>
						</table>
						</form>
						</td><td align="left" valign="top">
							'.($db['type'] == "image" || $db['type'] == "flash" ? '
								<div class="normalTableTitle">'.lang('Preview').':</div><br />
								'.(strpos($db['file'],"/tmp/")?'<div width="100%" style="text-align:center"><img src="../images/wheel.gif" alt=""/><br/><br/><span style="padding-bottom:15px">'. lang("System is checking for virus. Please refresh in a minute.") .'</span></div>':'<img style="max-width:100%;max-height:500px;" src="'. $db['file'].  '" alt=""/>'):$preview).'
								<br /><br /><hr /><br />
								<div class="normalTableTitle">'.lang('Get Tracking Code').'</div>
								<form method="get">
									<input type="hidden" name="act" value="edit_banner" />
									<input type="hidden" name="id" value="'.$id.'" />
									<table>
									<tr><td>'.lang('Choose Affiliate').':</td><td><select name="affiliate_id" '. (strpos($db['file'],'/tmp/')?'':'onchange="form.submit();"').'><option value="">'.lang('Choose Affiliate').'</option>'.$allAffiliates.'</select></td></tr>
									<tr><td>'.lang('Choose Profile').':</td><td><select name="profile_id" '. (strpos($db['file'],'/tmp/')?'':'onchange="form.submit();"').'><option value="">'.lang('Choose Profile').'</option>'.$allProfiles.'</select></td></tr>
									</table>
								</form>
								'.($affiliate_id ? '<textarea cols="55" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.$code.'</textarea>' : '').'
								'.($affiliate_id ?$codelink:'').'
							</td>
					</tr></table>
				</div>	<style>
						#bottom_table , 
						.headerSite,
						.headerSite + *,
						.headerSite + * +*{
							display:none!important;
						}
						body .content{
							padding-bottom:0;
						}
						.content td div:last-child img{
							vertical-align: top;
						}
						
						
					</style>';
		theme();
		break;
	
	// ----------------------------------------- [ Edit Banner ] -----------------------------------------
	
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'bnr_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "add":
		$insert = 0;
		$db['merchant_id'] = $merchant_id;
		$db['url'] = $creative_url;
		$db['admin_id'] = $set->userInfo['id'];
		$db['rdate'] = dbDate();
		$db['last_update'] = dbDate();
		$db['valid'] = $creative_status;
		$db['title'] = $creative_name;
		$db['alt'] = $creative_alt;
		
		if (chkUpload('file')) {
			$randomFolder =mt_rand(10000000, 99999999);
			$folder = 'files/sub_banners/tmp/' . $randomFolder ."/";
			 if (!is_dir('files/sub_banners/tmp')) {
				 mkdir('files/sub_banners/tmp');
			 }
			 if (!is_dir($folder)) {
				 mkdir($folder);
			 }
			$db['file'] = UploadFile('file','5120000','jpg,gif,swf,jpeg,png','',$folder);
			$exp=explode(".",$db['file']);
			$ext = strtolower($exp[count($exp)-1]);
			if ($ext == "swf") $db['type'] = "flash"; 
				else $db['type'] = "image";
			list($db['width'],$db['height']) = getimagesize($db['file']);
			$insert = 1;
			} else {
			if ($creative_name) $insert = 1;
			$db['type'] = "link";
			}
			
		if ($db['title'] AND $insert) dbAdd($db,$appTable);
		_goto($set->SSLprefix.$set->basepage.'?act=creative');
		break;
	
	case "creative":
		if ($set->introducingBrokerInterface) 
			$pageTitle = lang('Sub IB Creative Materials');
		else
			$pageTitle = lang('Sub Affiliation Creative Materials');
		
		$set->breadcrumb_title =  $pageTitle;
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
			<li><a href="'. $set->SSLprefix.$set->uri .'" class="arrow-left">'.$pageTitle.'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		if ($type) $where .= " AND type='".$type."'";
		
		$getPos = 20;
		$pgg=$_GET['pg'] * $getPos;
		
		$qq=function_mysql_query("SELECT * FROM ".$appTable." WHERE merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC LIMIT $pgg,$getPos",__FILE__);
		$bottomNav = GetPages($appTable,"WHERE merchant_id='".$merchant_id."' ".$where,$pg,$getPos);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE banner_id='".$ww['id']."' AND merchant_id='".$ww['merchant_id']."'",__FILE__));
			$allCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>
							<!--td><a href="javascript:void(0);" onclick="NewWin(\''.$set->SSLprefix.$set->basepage.'?act=edit_banner&id='.$ww['id'].'\',\'editbanner\',\'1000\',\'800\',\'1\');">Edit</a></td-->
							<td><a href="'.$set->webAddress. ltrim($set->basepage, '/').'?act=edit_banner&id='.$ww['id'].'" class="inline">Edit</a></td>
							<td>'.$ww['title'].'</td>'.
							(strpos($ww['file'],'/tmp')?'<td align="center" class="tooltip">'.($ww['type'] == "image" || $ww['type'] == "flash" ? '<img src="../images/wheel.gif" width=28 height=28><span class="tooltiptext" style="padding-bottom:15px">'. lang("System is checking for virus. Please refresh in a minute.") .'</span>' : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>':
							'<td align="center" class="img-wrap">'.($ww['type'] == "image" || $ww['type'] == "flash" ? getFixedSizeBanner($ww['file'],50,50) : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>').'
							<td align="center">'.$ww['type'].'</td>
							<td align="center" class="dimantion-wrap">'.($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</td>
							<td align="center">'.$ww['url'].'</td>
							<td align="center">'.$ww['alt'].'</td>
							<td align="center">'.number_format($totalTraffic['totalViews'],0).'</td>
							<td align="center">'.number_format($totalTraffic['totalClicks'],0).'</td>
							<td align="center" id="bnr_'.$ww['id'].'">
								<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'bnr_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a>
								'.($delete ? '<a href="'.$set->SSLprefix.$set->basepage.'?act=del_banner&id='.$ww['id'].'">.</a>' : '').'
							</td>
						</tr>';
			}
		
		$set->content .= '<form method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="merchant_id_from_get" value="'.$merchant_id.'" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Add New Crative Material for').': <b>'.listMerchants($merchant_id,1).'</b></div>
						<div id="tab_3">
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>
								<td align="left" class="blueText"><b>' . lang('Choose Merchant') 
						  . ':</td><td><select name="merchant_id"><option value="">' 
						  . lang('Choose Merchant') . '</option>' . listMerchants($merchant_id) . '</select></td>
						  </tr><tr>
								<td align="left" class="blueText">'.lang('Default Creative Name').':</td>
								<td align="left"><input type="text" name="creative_name" value="" style="width: 150px;" /></td>
								<td></td>
								<td align="left" class="blueText">'.lang('Landing URL').':</td>
								<td align="left"><input type="text" name="creative_url" value="http://" style="width: 250px;" /></td>
								<td></td>
								<td align="left" class="blueText">'.lang('Banner File').':</td>
								<td align="left" class="blueText">'.fileField('file','').'</td>
							</tr><tr>
								<td align="left" class="blueText">'.lang('Status').':</td>
								<td align="left">
									<select name="creative_status" style="width: 163px;">
										<option value="1">Active</option>
										<option value="0">Inactive</option>
									</select>
								</td>
								<td></td>
								<td align="left" class="blueText">'.lang('ALT').':</td>
								<td align="left"><input type="text" name="creative_alt" value="" style="width: 250px;" /></td>
								<td></td>
								<td colspan="2" align="left"><input type="submit" value="'.lang('Save & Upload').'" /></td>
							</tr></table>
						</div>
						</form>
						<hr />
						   <script>var hash = "";</script>
						<form method="get">
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table>
							<tr><td align="left" class="blueText"><b>' . lang('Filter By Merchant') 
						  . ':</td><td><select onchange="location.href=\'' . $set->SSLprefix.$set->basepage . '?act=creative&merchant_id=\'+this.value + hash;"><option value="">' 
						  . lang('Choose Merchant') . '</option>' . listMerchants() . '</select></td></tr>
							<tr>
								<td align="left" class="blueText">'.lang('Creative Type').':</td>
								<td align="left">
									<select name="type" style="width: 164px;">
										<option value="">'.lang('All').'</option>
										<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>
										<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>
										<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>
									</select>
									</td>
								<td></td>
								<td width="80"></td>
								<td align="right"><input type="submit" value="'.lang('Search').'" /></td>
							</tr></table>
							</form>
						</div>
						
						<div class="normalTableTitle">'.lang('Creatives List for').': <b>'.(!empty($merchant_id)?listMerchants($merchant_id,1):"").'</b></div>
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
								<td>'.lang('ALT').'</td>
								<td>'.lang('Impressions').'</td>
								<td>'.lang('Clicks').'</td>
								<td>'.lang('Avafdilable').'</td>
							</tr></thead><tfoot>'.$allCreative.'</tfoot>
						</table><br />
					<div align="left">'.$bottomNav.'</div>';

		$set->content .= getImageGrowerScript();
		
		
		theme();
		break;
	
	/* ------------------------------------ [ Promotions ] ------------------------------------ */
	
	case "prom_valid":
		$db=dbGet($id,$appProm);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appProm,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=prom_valid&id='.$db['id'].'\',\'promlng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "prom_add":
		if (!$db['title']) $errors['title'] = 1;
		if (!$db['merchant_id']) $errors['merchant_id'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			$db[valid] = 0;
			dbAdd($db,$appProm);
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
			}
		die();
		break;
	/* ------------------------------------ [ Promotions ] ------------------------------------ */
	
	
	
	
	
	
	
	

	
	}

?>