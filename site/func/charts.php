<?php
/* function chart($type=0,$agent='admin',$id='',$wholeYear=0) {
	global $set,$c;
	$where='';
	$InitAffiliateID = 0 ;
	$group_id = 0;
	$InitManagerID  = 0;
	if ($agent == "manager") {
		$group_id = $set->userInfo['group_id'];
		$InitManagerID = $set->userInfo['group_id'];
		$InitManagerID= $group_id = $id;
		
		$where = "group_id='".$group_id."' AND ";
		$title = lang('Manager Performance');
		} else if ($agent == "affiliate") {
		$where = "affiliate_id='".($id ? $id : $set->userInfo['id'])."' AND ";
		$InitAffiliateID = ($id ? $id : $set->userInfo['id']);
		$title = lang('Affiliate Performance');
		} else {
		$title = lang('Network Performance');
		}
		
	if (!$c) {
		$html = '<script type="text/javascript" src="https://www.google.com/jsapi?callback"></script>
		<script type="text/javascript">
		google.load(\'visualization\', \'1\', {\'packages\':[\'corechart\']});
		function refreshData(action) {
			$.ajax({
				url: "/chart.php?c=1&a='.$agent.'&i='.($id ? $id : $set->userInfo['id']).'&w='.$wholeYear.'&action="+action,
				dataType:"json",
				success: function(data) { 
					drawChart(data); 
					}	,
				fail: function(data) { 
					return false;
					}
				});
			}
		window.onload = function() {
			refreshData();
			}
	    function drawChart(jsonData) {
			var options = {
				title: \''.$title.'\',
				pointSize: 3,
				fontSize: 11,
				colors: [\'#205E9F\',\'#019C01\',\'red\']
				};
			var data = new google.visualization.DataTable(jsonData);
			var chart = new google.visualization.AreaChart(document.getElementById(\'chart_div\'));
			chart.draw(data, options);
			}
		$("#refreshData").on("click",function() {
			$("#chart_div").html(\'<div align="center" style="padding-top: 25px;"><img border="0" src="images/ajax.gif" alt="" style="margin: 0 auto 0 auto;" /><br /><br /><b>'.lang('Please Wait, Loading Data...').'</b></div>\');
			refreshData(\'refresh\');
			});
    </script>
	<div id="chart_div" style="width: 100%; height: 100px;margin-bottom: 15px;">
		<div align="center" style="padding-top: 25px;">
			<img border="0" src="images/ajax.gif" alt="" style="margin: 0 auto 0 auto;" /><br /><br />
			<b>'.lang('Please Wait, Loading Data...').'</b>
		</div>
	</div>
	<div align="center" style="line-height: 25px; height: 25px;"><a id="refreshData" style="font-size: 11px; cursor: pointer;">'.lang('Refresh Graph').'</a></div>
    ';

		} else {
		
		if ($wholeYear) {
		$month[] = date("Y-m-01", strtotime("-11 Month"));
		$month[] = date("Y-m-01", strtotime("-10 Month"));
		$month[] = date("Y-m-01", strtotime("-9 Month"));
		$month[] = date("Y-m-01", strtotime("-8 Month"));
		$month[] = date("Y-m-01", strtotime("-7 Month"));
		$month[] = date("Y-m-01", strtotime("-6 Month"));

		}
	$month[] = date("Y-m-01", strtotime("-5 Month"));
	$month[] = date("Y-m-01", strtotime("-4 Month"));
	$month[] = date("Y-m-01", strtotime("-3 Month"));
	$month[] = date("Y-m-01", strtotime("-2 Month"));
	$month[] = date("Y-m-01", strtotime("-1 Month"));
	//$month[] = date("Y-m-01", strtotime("0 Month"));
	$month[] = date("Y-m-01");
	
				
	for ($i=0; $i<=count($month)-1; $i++) {
		$ftdUsers=0;
		$total_real=0;
		$chkExist=mysql_fetch_assoc(function_mysql_query("SELECT * FROM chart_data WHERE level='".$agent."'".($agent != "admin" ? " AND member_id='".$id."'" : "")." AND month='".date("m",strtotime($month[$i]))."' AND year='".date("Y",strtotime($month[$i]))."'",__FILE__,__FUNCTION__));
		if ($chkExist['id']) {
			$buildData[] = '{"c":[{"v":"'.date("M, Y",strtotime($month[$i])).'","f":null},{"v":'.$chkExist['accounts'].',"f":null},{"v":'.$chkExist['ftds'].',"f":null}]}';
			} else {
			$qq=function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY pos",__FILE__,__FUNCTION__);
			$tradersFtdArray = array();
			$new_ftd=0;
			
			while ($ww=mysql_fetch_assoc($qq)) {
				
				if ($agent=='manager')
					$InitManagerID = $id;
				
				
				$new_ftd_rslt = getTotalFtds($month[$i],date("Y-m-d",strtotime("-1 Day", strtotime("+1 Month",strtotime($month[$i])))).' 23:59:59', $InitAffiliateID, $ww['id'], $ww['wallet_id'], $InitManagerID);
				 foreach ($new_ftd_rslt as $arrFtd) {
				
                                        $beforeNewFTD = $new_ftd;
                                        getFtdByDealType($ww['id'], $arrFtd, $arrDealTypeDefaults, $ftdUsers, $arrFtd['amount'], $new_ftd);

                                        if ($beforeNewFTD != $new_ftd && !in_array($arrFtd['trader_id'],$tradersFtdArray)){
											$tradersFtdArray[$arrFtd['trader_id']]=$arrFtd['trader_id'];
                                        }
                                        unset($arrFtd);
				}
				$new_ftd =+ count($tradersFtdArray) ;
				if ($_GET['ddd']){
				echo 'new_ftd:   ' . $new_ftd. '<br>';
				}
				
				$qry = "SELECT COUNT(id) FROM data_reg WHERE merchant_id = '" . $ww['id'] . "' and  ".$where." type='real' AND rdate BETWEEN '".$month[$i]."' AND '".date("Y-m-d",strtotime("-1 Day", strtotime("+1 Month",strtotime($month[$i]))))." 23:59:59' ";
				if ($agent=='manager')
					$qry = $qry . ' and group_id = ' . $group_id;
				// die($qry);
				//function_mysql_query("INSERT INTO `logs` ( `title`, `description`, `var1`, `var2`, `var3`, `rdate`, `flag`, `merchant_id`, `text`, `ip`, `url`) VALUES ( '5', '".$qry."', '-', '-', '-', '2015-03-02 00:00:00', '', '0', '0', '', '-');");
				
				$total_real += mysql_result(function_mysql_query($qry,__FILE__,__FUNCTION__),0);
				}
				
				$buildRow = '{"c":[{"v":"'.date("M, Y",strtotime($month[$i])).'","f":null},{"v":'.$total_real.',"f":null},{"v":'.$new_ftd.',"f":null}]}';
				$buildData[] = $buildRow;
				if (date("Y-m",strtotime($month[$i])) != date("Y-m")) function_mysql_query("INSERT INTO chart_data (lastUpdate,fulldate,level,member_id,month,year,accounts,ftds) VALUES
				('".dbDate()."','".date("Y",strtotime($month[$i]))."-".date("m",strtotime($month[$i]))."-01','".$agent."','".$id."','".date("m",strtotime($month[$i]))."','".date("Y",strtotime($month[$i]))."','".$total_real."','".$new_ftd."')",__FILE__,__FUNCTION__);
			}
		}
		$html ='{
	"cols": [
		{"id":"","label":"'.lang('Month').'","pattern":"","type":"string"},
		{"id":"","label":"'.lang(ptitle('Account')).'","pattern":"","type":"number"},
		{"id":"","label":"'. lang(ptitle('FTD')).'","pattern":"","type":"number"}
		],
		"rows": ['.implode(',',$buildData).']
		}';
		}
	return $html;	
	
	}
	


 */






	
	function highchart($type=0,$agent='',$id='',$wholeYear=0) {
	global $set,$c;
	$where='';
	$InitAffiliateID = 0 ;
	$group_id = 0;
	$InitManagerID  = 0;
	/* if ($agent == "manager") {
		$group_id = $set->userInfo['group_id'];
		$InitManagerID = $set->userInfo['group_id'];
		$InitManagerID= $group_id = $id;
		// $where = "group_id='".$set->userInfo['group_id']."' AND ";
		$where = "group_id='".$group_id."' AND ";
		$title = lang('Manager Performance');
		} else if ($agent == "affiliate") {
		$where = "affiliate_id='".($id ? $id : $set->userInfo['id'])."' AND ";
		$InitAffiliateID = ($id ? $id : $set->userInfo['id']);
		$title = lang('Affiliate Performance');
		} else { */
		$title = lang('Monthly Performance');
		//}
		
		if ($agent == "affiliate") {
			$InitAffiliateID = $id ;
		}
		
		if ($agent=='manager') {
            $group_id = $set->userInfo['group_id'];
            $InitManagerID = $set->userInfo['group_id'];
            $InitManagerID= $group_id = $id;
        }
			
			
			$RunQualification = false;
		if ($set->ShowQualificationOnChart==1 && !empty($InitAffiliateID))
			$RunQualification = true;
		
		
	if (!$c) {

	
		$html = '<script src="'.$set->SSLprefix.'js/highcharts.js"></script>
		<script type="text/javascript" src="'.(!empty($set->SSLprefix) ?  $set->SSLprefix : '../' ) . 'js/impromptu/dist/jquery-impromptu.min.js"></script>
		<link rel="stylesheet" href="'.(!empty($set->SSLprefix) ?  $set->SSLprefix : '../' ) . 'js/impromptu/dist/jquery-impromptu.min.css"/>
		<script type="text/javascript">';
		
		$html .= highchartThemes();
		
		$html .='function refreshData2(action) {
			$.ajax({
				url: "'.$set->SSLprefix.'/highchart.php?c=1&a='.$agent.'&i='.($id ? $id : $set->userInfo['id']).'&w='.$wholeYear.'&action="+action,
				dataType:"json",
				success: function(data) { 
					drawChart2(data); 
					}	,
				fail: function(data) { 
					return false;
					}
				});
			}
		
	    function drawChart2(jsonData) {
		';
		
		if($set->chartTheme == "dark_unica"){
			$tooltip_color = "#ffffff";
		}
		else{
			$tooltip_color = "#000000";
		}
		$html .= "
			
	
		 var processed_accounts = new Array();
		 var processed_ftds = new Array();
		 var processed_conversions = new Array();
            for (i = 0; i < jsonData.length; i++) {
                processed_accounts.push([jsonData[i].date, parseInt(jsonData[i].accounts)]);
                processed_ftds.push([jsonData[i].date, parseInt(jsonData[i].ftds)]);
            }
		
		$('#chart_div2').highcharts({
        chart: {
            zoomType: false,
			height: 250
        },
		 credits: {
			enabled: false
		  },
        title: {
            text: '". $title ."'
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
                text: '".($RunQualification ? lang(ptitle("Active Traders")) : lang('FTDs'))."',
                style: {
                    color: Highcharts.getOptions().colors[1]
                }
            }
        }, { // Secondary yAxis
            title: {
                text: '".lang('Accounts')."',
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
        },";
		if ($set->userInfo['level'] == ""){
			if(allowView('af-quick',$deal,'reports')){
					$html .="plotOptions:{
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
					},";
			}
		}
		else{
			$html .="plotOptions:{
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
		},";
		}
        $html .= "series: [
		{
			data: processed_accounts,
			type: 'column',
			yAxis: 1,
			 name: '". lang('Accounts') ."',
		},
		{
			data: processed_ftds,
			type: 'spline',
			name: '".($RunQualification ? lang(ptitle("Active Traders")) : lang('FTDs'))."',
			
        }
		]
    });
	";
		$html.='}
		$(document).ready(function(){
		$("#refreshData2").on("click",function() {
			refreshChart2();
			});
			});
			function refreshChart2(){
			$("#chart_div2").html(\'<div align="center" style="padding-top: 25px;"><img border="0" src="'.$set->SSLprefix.'images/ajax.gif" alt="" style="margin: 0 auto 0 auto;" /><br /><br /><b>'.lang('Please Wait, Loading Data...').'</b></div>\');
			refreshData2(\'refresh\');
		}
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
	<div id="chart_div2" style="text-align:center;width: 100%; height: 250px;">
		<div align="center" style="padding-top: 25px;">
			<img border="0" src="'.$set->SSLprefix.'images/ajax.gif" alt="" style="margin: 0 auto 0 auto;" /><br /><br />
			<b>'.lang('Please Wait, Loading Data...').'</b>
		</div>
	</div>
	<!--div align="center" style="width:100%;line-height: 25px; height: 25px;"><a id="refreshData2" style="font-size: 11px; cursor: pointer;">'.lang('Refresh Graph').'</a></div-->
	<style>
	.chart-wrapper {
		 position: relative;
			padding-bottom: 40%;
			width:80%;
			float:left;
			text-align:center;
		}
	</style>
    ';

		} else {
		
		$html .= calculateHighchartsData($type,$agent,$id,$wholeYear,'performance');
		
		}
	return $html;	
	
	}
	
	function calculateHighchartsData($type=0,$agent='',$id='',$wholeYear=0,$charttype = "",$merchant_id=0){
		global $set,$c;
		
		$where='';
		$InitAffiliateID = 0 ;
		$group_id = 0;
		$InitManagerID  = 0;
		$html = "";
		
		if ($agent == "affiliate") {
			$InitAffiliateID = $id ;

            $sql = 'select merchants from affiliates where id = ' . $InitAffiliateID . ' limit 1';
            $arrMerchants = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
            $merchantIDs = str_replace('|', ",", $arrMerchants['merchants']);
            $merchantIDs = ltrim($merchantIDs,',');
            $_merchantList = '';

            if (!empty($merchantIDs)) {
                $_merchantList .= 'AND MerchantID IN ('.$merchantIDs.') ';
            }
		}
		
		if ($agent=='manager')
				$InitManagerID = $id;
			
        if ($agent=='advertiser'){
            $InitManagerID = $id;
            $merchant_id = $merchant_id;
        }
		
		
	if($charttype == "performance"){

            if ($wholeYear) {
                $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-11 Month"));
                $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-10 Month"));
                $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-9 Month"));
                $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-8 Month"));
                $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-7 Month"));
                $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-6 Month"));
            }

			$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-5 Month"));
			$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-4 Month"));
			$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-3 Month"));
			$month[] = date("Y-m-01", strtotime(date("Y-m-01"). " -2 Month"));
			$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-1 Month"));
			//$month[] = date("Y-m-01", strtotime("0 Month"));
			$month[] = date("Y-m-01");
			
	}
	else{
			
			/* if ($wholeYear) {
		//last year
		$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-11 Month")) , 01, date('Y',strtotime("-1 year"))));
		$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-10 Month")) , 01, date('Y',strtotime("-1 year"))));
		$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-9 Month")) , 01, date('Y',strtotime("-1 year"))));
		$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-8 Month")) , 01, date('Y',strtotime("-1 year"))));
		$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-7 Month")) , 01, date('Y',strtotime("-1 year"))));
		$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-6 Month")) , 01, date('Y',strtotime("-1 year"))));
		
	
		
		} */
	

	$lastYear = date('Y',strtotime("-1 year"));
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-6 Month")) , 01, date('Y',strtotime("-1 year"))));
	$month[] =date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-5 Month")) , 01, date('Y',strtotime("-1 year"))));
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-4 Month")) , 01, date('Y',strtotime("-1 year"))));
	
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-3 Month")) , 01, date('Y',strtotime("-1 year"))));
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-2 Month")) , 01, date('Y',strtotime("-1 year"))));
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime( $lastYear . "-" . date('m') . "-01" . " -1 Month")) , 01, date('Y',strtotime("-1 year"))));
	//$month[] = date("Y-m-01", strtotime("0 Month")); 

	//$month[] = date("Y-m-d", mktime(0, 0, 0, date('m') , 01, date('Y',strtotime("-1 year"))));
	 
	 
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-6 Month")) , 01, date('Y')));
	$month[] =date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-5 Month")) , 01, date('Y')));
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-4 Month")) , 01, date('Y')));
	
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-3 Month")) , 01, date('Y')));
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime("-2 Month")) , 01, date('Y')));
	$month[] = date("Y-m-d", mktime(0, 0, 0, date('m',strtotime( date('Y') . "-" . date('m') . "-01" . " -1 Month")) , 01, date('Y')));
	
	
	/* $m = date("m");
	$m = ltrim($m, 0);
	if($m>5){
		$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-5 Month"));
		$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-4 Month"));
		$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-3 Month"));
		$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-2 Month"));
		$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-1 Month"));
		//$month[] = date("Y-m-01", strtotime("0 Month"));
		$month[] = date("Y-m-01");
	}
	else{
		$m -=1;
	
		for($a=$m;$a>=1;$a--){
		
			$month[] = date("Y-m-01", strtotime(date("Y-m-01")."-".$a ." Month"));
		}
		// $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-4 Month"));
		// $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-3 Month"));
		// $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-2 Month"));
		// $month[] = date("Y-m-01", strtotime(date("Y-m-01")."-1 Month"));
		//$month[] = date("Y-m-01", strtotime("0 Month"));
		 $month[] = date("Y-m-01");
	} */
	
	//print_r($month);die;
	
	}

		
	for ($i=0; $i<=count($month)-1; $i++) {

		
		$RunQualification = false;
		if ($set->ShowQualificationOnChart==1 && ($set->ShowQualificationOnChartSince <($month[$i])) && !empty($InitAffiliateID))
			$RunQualification = true;
		

		
		$ftdUsers=0;
		$total_real=0;



		$sql = 	"SELECT * FROM chart_data WHERE 
								level='".$agent."'".($agent != "admin" ? " AND member_id='".$id."'" : "")." 
								AND month='".date("m",strtotime($month[$i]))."' 
								AND year='".date("Y",strtotime($month[$i]))." 23:59:59'";
		//die($sql);
		$chkExist=mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));

		if ($chkExist['id']) {
			$buildData[] = '{"c":[{"v":"'.date("M, Y",strtotime($month[$i])).'","f":null},{"v":'.$chkExist['accounts'].',"f":null},{"v":'.$chkExist['ftds'].',"f":null}]}';
			if($charttype == "performance")
				$buildData2[] = array("date"=>date("M, Y",strtotime($month[$i])),"accounts"=>$chkExist['accounts'],"ftds"=>$chkExist['ftds']);
			else{
				if( $chkExist['accounts'] == 0 || $chkExist['ftds']==0)
				$conversion ='0%';
				else
				$conversion = ($chkExist['ftds']/$chkExist['accounts']) * 100 ."%";
				$buildData2[] = array("Year"=>$chkExist['year'],"month"=>date("M",strtotime($month[$i])),"date"=>date("M, Y",strtotime($month[$i])),'conversion'=>$conversion);
				
			}
		} else {

		    switch ($agent) {
                case "admin": {
                    $dasboardDataResult = function_mysql_query("SELECT sum(FTD) as FTD, sum(RealAccount) as RealAccount, MAX(Date) as MaxDate from Dashboard WHERE Date>='".$month[$i]."' AND Date<='".date("Y-m-t",strtotime($month[$i]))."'");
                } break;
                case "manager": {
                    $dasboardDataResult = function_mysql_query("SELECT sum(Dashboard.FTD) as FTD, sum(Dashboard.RealAccount) as RealAccount, MAX(Dashboard.Date) as MaxDate from Dashboard RIGHT JOIN affiliates ON affiliates.id = Dashboard.AffiliateID WHERE affiliates.group_id = ".$id." AND Dashboard.Date>='".$month[$i]."' AND Dashboard.Date<='".date("Y-m-t",strtotime($month[$i]))."'");
                } break;
                default: {
                    $dasboardDataResult = function_mysql_query("SELECT sum(FTD) as FTD, sum(RealAccount) as RealAccount, MAX(Date) as MaxDate from Dashboard WHERE AffiliateID = ".$id." ".$_merchantList." AND Date>='".$month[$i]."' AND Date<='".date("Y-m-t",strtotime($month[$i]))."'");
                }
            }

            while ($dasboardData = mysql_fetch_assoc($dasboardDataResult)) {

                if($charttype == "performance")
                    $buildData2[] = array("date"=>date("M, Y",strtotime($month[$i])),"accounts"=>$dasboardData['RealAccount'] > 0 ? $dasboardData['RealAccount'] : 0,"ftds"=>$dasboardData['FTD'] > 0 ?$dasboardData['FTD'] : 0);
                else{
                    if( $dasboardData['RealAccount'] == 0 || $dasboardData['FTD'] == 0)
                        $conversion ='0%';
                    else {
                        $conversion = ($dasboardData['FTD']/$dasboardData['RealAccount']) * 100 ."%";
                    }
                    $buildData2[] = array("Year"=>date("Y",strtotime($month[$i])),"month"=>date("M",strtotime($month[$i])),"date"=>date("M, Y",strtotime($month[$i])),'conversion'=>$conversion);

                }

                if (date("Y-m",strtotime($month[$i])) != date("Y-m")) {
                    if (!$chkExist['id']) {
                        function_mysql_query("INSERT INTO chart_data (lastUpdate,fulldate,level,member_id,month,year,accounts,ftds) VALUES
						('".dbDate()."','".date("Y",strtotime($month[$i]))."-".date("m",strtotime($month[$i]))."-01','".$agent."','".$id."','".date("m",strtotime($month[$i]))."','".date("Y",strtotime($month[$i]))."  23:59:59','".$dasboardData['RealAccount']."','".$dasboardData['FTD']."')",__FILE__,__FUNCTION__);
                    }
                }
            }
        }
    }

		$html ='{
	"cols": [
		{"id":"","label":"'.lang('Month').'","pattern":"","type":"string"},
		{"id":"","label":"'.lang(ptitle('Account')).'","pattern":"","type":"number"},
		{"id":"","label":"'. lang(ptitle(($RunQualification ? 'Active Traders' : 'FTD') )).'","pattern":"","type":"number"}
		],
		"rows": ['.implode(',',$buildData2).']
		}';
		$html = json_encode($buildData2);
		return $html;
	}
	
	
	function conversionHighchart($type=0,$agent='',$id='',$wholeYear=0) {
	global $set,$c;
	$where='';
	$InitAffiliateID = 0 ;
	$group_id = 0;
	$InitManagerID  = 0;


    if ($agent=='manager') {
        $group_id = $set->userInfo['group_id'];
        $InitManagerID = $set->userInfo['group_id'];
        $InitManagerID= $group_id = $id;
    }
	if ($agent == "affiliate") {
			$InitAffiliateID = $id ;
	}
		
		
	/* if ($agent == "manager") {
		$group_id = $set->userInfo['group_id'];
		$InitManagerID = $set->userInfo['group_id'];
		$InitManagerID= $group_id = $id;
		// $where = "group_id='".$set->userInfo['group_id']."' AND ";
		$where = "group_id='".$group_id."' AND ";
		
		$title = lang('Manager Performance');
		} else if ($agent == "affiliate") {
		$where = "affiliate_id='".($id ? $id : $set->userInfo['id'])."' AND ";
		$InitAffiliateID = ($id ? $id : $set->userInfo['id']);
		$title = lang('Affiliate Performance');
		} else { */
		$title = lang('Monthly Conversions');
		//}
		
	if (!$c) {

		$html = '
		<script type="text/javascript">';
		
		$html .= highchartThemes();
		
		$html .='function refreshData3(action) {
			$.ajax({
				url: "'.$set->SSLprefix.'/conversionHighchart.php?c=1&a='.$agent.'&i='.($id ? $id : $set->userInfo['id']).'&w='.$wholeYear.'&action="+action,
				dataType:"json",
				success: function(data) { 
					drawChart3(data); 
					}	,
				fail: function(data) { 
					return false;
					}
				});
			}
	    function drawChart3(jsonData) {
		';
		
		if($set->chartTheme == "dark_unica"){
			$tooltip_color = "#ffffff";
		}
		else{
			$tooltip_color = "#000000";
		}
		$html .= "
		var processed_2015 = new Array();
		 var processed_2016 = new Array();
		 var months = new Array();
		 var datanew = new Array();
		 var currentYear = (new Date).getFullYear();
		 
		 allzero_2015 = true;
		for (i = 0; i < jsonData.length; i++) {
			months.push(jsonData[i].month); 
			if(jsonData[i].Year == (currentYear-1)){
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
		
		
		//console.log(processed_2015);
		//console.log(processed_2016);

		
		 $('#chart_div3').highcharts({
        title: {
            text: '". $title ."',
            x: -20 //center
        },
		 credits: {
			enabled: false
		  },
        xAxis: {
            categories: months,
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
				var symbolName =point.series.symbol ;
				
				switch ( symbolName) {
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
					
					
					//if(point.series.name == currentYear){
						//console.log(point.y);
						if(point.y > 0){
							var index = this.series.yData.indexOf(this.y);
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
					//}
					
					//console.log(point.x);
					x = point.series.xAxis.names[point.x];
					//console.log(point.series.xAxis);
					//conversion = Math.round(point.y*1000)/1000;
					conversion = point.y;
					conversion =conversion.toFixed(3);
                    /* s.push('<span style=\"color:' + point.series.color + '\">' + symbol + ' </span><span style=\"color:". $tooltip_color .";\">'+ x+ ' ' + this.series.name + ': '+
                        '<b>' + conversion +'%</b><span>'); */
						s.push('<span style=\"color:' + point.series.color + '\">' + symbol + ' </span><span style=\"color:". $tooltip_color .";\">'+ point.x+ ' ' + this.series.name + ': '+
                        '<b>' + conversion +'%</b><span>');
					
					
					if(!allzero_2015){
						if(point.series.name == (currentYear-1)){
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
				if(changedYearVal=='Infinity'){
					changedYearVal = '-';
				}
				else{
					if(isNaN(changedYearVal)){
						changedYearVal = '-';
					}
					else{
						changedYearVal = changedYearVal.toFixed(2) + '%';
					}
			}
			}
			if(newMonthVal !=''){
					if(valforarrow < 0){
						   s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes from last month') ."  : '+'<b>'+ (typeof newMonthVal=='undefined'? '-' : newMonthVal) +'</b></span><span style=\"color:#ff0000;\">▼</span>');
						 }
						 else if(valforarrow > 0){
							 s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes from last month') ."  : '+'<b>'+ (typeof newMonthVal=='undefined'? '-' : newMonthVal) +'</b><span></span><span style=\"color:#338a16;\">▲</span>');
						 }
						 else{
							  s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes from last month') ."  : '+'<b>'+ (typeof newMonthVal=='undefined'? '-' : newMonthVal) +'</b><span>');
						 }
					}
			 if(!allzero_2015){
					if(changedYearVal == '-'){
						s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes since last year') ."  : '+'<b>'+ changedYearVal +'</b><span>');
					}
					else{
						 if(yearvalforarrow < 0){
						   s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes since last year') ."  : '+'<b>'+ changedYearVal +'</b></span><span style=\"color:#ff0000;\">▼</span>');
						 }
						 else if(yearvalforarrow >  0){
							 s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes since last year') ."  : '+'<b>'+  changedYearVal +'</b><span></span><span style=\"color:#338a16;\">▲</span>');
						 }
						 else{
							  s.push ('<span style=\"color:". $tooltip_color .";\">". lang('Changes since last year') ."  : '+'<b>'+ changedYearVal +'</b><span>');
						 }
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
			showInLegend :true,
            name: (currentYear-1),
			legendIndex:1,
            data:processed_2015
        }, {
			showInLegend :true,
            name: currentYear,
			legendIndex:0,
            data: processed_2016
        }]
    });
	";
		$html.='}
		$(document).ready(function(){
		$("#refreshData3").on("click",function() {
				refreshChart3();
			});
		});
		function refreshChart3(){
			$("#chart_div3").html(\'<div align="center" style="padding-top: 25px;"><img border="0" src="'.$set->SSLprefix.'images/ajax.gif" alt="" style="margin: 0 auto 0 auto;" /><br /><br /><b>'.lang('Please Wait, Loading Data...').'</b></div>\');
			refreshData3(\'refresh\');
		}
    </script>
	<div id="chart_div3" style="text-align:center;width: 100%; height: 250px;">
		<div align="center" style="padding-top: 25px;">
			<img border="0" src="'.$set->SSLprefix.'images/ajax.gif" alt="" style="margin: 0 auto 0 auto;" /><br /><br />
			<b>'.lang('Please Wait, Loading Data...').'</b>
		</div>
	</div>
	
    '; 

		} else {
			$html .= calculateHighchartsData($type,$agent,$id,$wholeYear,'conversion');
		}
	return $html;	
	
	}
	
function highchartThemes(){
	global $set;
	$theme = "";
	if($set->chartTheme=="dark_unica"){
		$theme = "Highcharts.createElement('link', {
							   href: 'https://fonts.googleapis.com/css?family=Unica+One',
							   rel: 'stylesheet',
							   type: 'text/css'
							}, null, document.getElementsByTagName('head')[0]);

							Highcharts.theme = {
							   colors: ['#2b908f', '#90ee7e', '#f45b5b', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
								  '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
							   chart: {
								  backgroundColor: {
									 linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
									 stops: [
										[0, '#2a2a2b'],
										[1, '#3e3e40']
									 ]
								  },
								  style: {
									 fontFamily: \"'Unica One', sans-serif\"
								  },
								  plotBorderColor: '#606063'
							   },
							   title: {
								  style: {
									 color: '#E0E0E3',
									 textTransform: 'uppercase',
									 fontSize: '20px'
								  }
							   },
							   subtitle: {
								  style: {
									 color: '#E0E0E3',
									 textTransform: 'uppercase'
								  }
							   },
							   xAxis: {
								  gridLineColor: '#707073',
								  labels: {
									 style: {
										color: '#E0E0E3'
									 }
								  },
								  lineColor: '#707073',
								  minorGridLineColor: '#505053',
								  tickColor: '#707073',
								  title: {
									 style: {
										color: '#A0A0A3'

									 }
								  }
							   },
							   yAxis: {
								  gridLineColor: '#707073',
								  labels: {
									 style: {
										color: '#E0E0E3'
									 }
								  },
								  lineColor: '#707073',
								  minorGridLineColor: '#505053',
								  tickColor: '#707073',
								  tickWidth: 1,
								  title: {
									 style: {
										color: '#A0A0A3'
									 }
								  }
							   },
							   tooltip: {
								  backgroundColor: 'rgba(0, 0, 0, 0.85)',
								  style: {
									 color: '#F0F0F0'
								  }
							   },
							   plotOptions: {
								  series: {
									 dataLabels: {
										color: '#B0B0B3'
									 },
									 marker: {
										lineColor: '#333'
									 }
								  },
								  boxplot: {
									 fillColor: '#505053'
								  },
								  candlestick: {
									 lineColor: 'white'
								  },
								  errorbar: {
									 color: 'white'
								  }
							   },
							   legend: {
								  itemStyle: {
									 color: '#E0E0E3'
								  },
								  itemHoverStyle: {
									 color: '#FFF'
								  },
								  itemHiddenStyle: {
									 color: '#606063'
								  }
							   },
							   credits: {
								  style: {
									 color: '#666'
								  }
							   },
							   labels: {
								  style: {
									 color: '#707073'
								  }
							   },

							   drilldown: {
								  activeAxisLabelStyle: {
									 color: '#F0F0F3'
								  },
								  activeDataLabelStyle: {
									 color: '#F0F0F3'
								  }
							   },

							   navigation: {
								  buttonOptions: {
									 symbolStroke: '#DDDDDD',
									 theme: {
										fill: '#505053'
									 }
								  }
							   },

							   // scroll charts
							   rangeSelector: {
								  buttonTheme: {
									 fill: '#505053',
									 stroke: '#000000',
									 style: {
										color: '#CCC'
									 },
									 states: {
										hover: {
										   fill: '#707073',
										   stroke: '#000000',
										   style: {
											  color: 'white'
										   }
										},
										select: {
										   fill: '#000003',
										   stroke: '#000000',
										   style: {
											  color: 'white'
										   }
										}
									 }
								  },
								  inputBoxBorderColor: '#505053',
								  inputStyle: {
									 backgroundColor: '#333',
									 color: 'silver'
								  },
								  labelStyle: {
									 color: 'silver'
								  }
							   },

							   navigator: {
								  handles: {
									 backgroundColor: '#666',
									 borderColor: '#AAA'
								  },
								  outlineColor: '#CCC',
								  maskFill: 'rgba(255,255,255,0.1)',
								  series: {
									 color: '#7798BF',
									 lineColor: '#A6C7ED'
								  },
								  xAxis: {
									 gridLineColor: '#505053'
								  }
							   },

							   scrollbar: {
								  barBackgroundColor: '#808083',
								  barBorderColor: '#808083',
								  buttonArrowColor: '#CCC',
								  buttonBackgroundColor: '#606063',
								  buttonBorderColor: '#606063',
								  rifleColor: '#FFF',
								  trackBackgroundColor: '#404043',
								  trackBorderColor: '#404043'
							   },

							   // special colors for some of the
							   legendBackgroundColor: 'rgba(0, 0, 0, 0.5)',
							   background2: '#505053',
							   dataLabelsColor: '#B0B0B3',
							   textColor: '#C0C0C0',
							   contrastTextColor: '#F0F0F3',
							   maskColor: 'rgba(255,255,255,0.3)'
							};

							// Apply the theme
							Highcharts.setOptions(Highcharts.theme);";
				}
				else if($set->chartTheme=="sand_signika")
				{
					$theme = "/**
								 * Sand-Signika theme for Highcharts JS
								 * @author Torstein Honsi
								 */

								// Load the fonts
								Highcharts.createElement('link', {
								   href: 'https://fonts.googleapis.com/css?family=Signika:400,700',
								   rel: 'stylesheet',
								   type: 'text/css'
								}, null, document.getElementsByTagName('head')[0]);

								// Add the background image to the container
								Highcharts.wrap(Highcharts.Chart.prototype, 'getContainer', function (proceed) {
								   proceed.call(this);
								   this.container.style.background = 'url(http".$set->SSLswitch."://www.highcharts.com/samples/graphics/sand.png)';
								});


								Highcharts.theme = {
								   colors: ['#f45b5b', '#8085e9', '#8d4654', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
									  '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
								   chart: {
									  backgroundColor: null,
									  style: {
										 fontFamily: 'Signika, serif'
									  }
								   },
								   title: {
									  style: {
										 color: 'black',
										 fontSize: '16px',
										 fontWeight: 'bold'
									  }
								   },
								   subtitle: {
									  style: {
										 color: 'black'
									  }
								   },
								   tooltip: {
									  borderWidth: 0
								   },
								   legend: {
									  itemStyle: {
										 fontWeight: 'bold',
										 fontSize: '13px'
									  }
								   },
								   xAxis: {
									  labels: {
										 style: {
											color: '#6e6e70'
										 }
									  }
								   },
								   yAxis: {
									  labels: {
										 style: {
											color: '#6e6e70'
										 }
									  }
								   },
								   plotOptions: {
									  series: {
										 shadow: true
									  },
									  candlestick: {
										 lineColor: '#404048'
									  },
									  map: {
										 shadow: false
									  }
								   },

								   // Highstock specific
								   navigator: {
									  xAxis: {
										 gridLineColor: '#D0D0D8'
									  }
								   },
								   rangeSelector: {
									  buttonTheme: {
										 fill: 'white',
										 stroke: '#C0C0C8',
										 'stroke-width': 1,
										 states: {
											select: {
											   fill: '#D0D0D8'
											}
										 }
									  }
								   },
								   scrollbar: {
									  trackBorderColor: '#C0C0C8'
								   },

								   // General
								   background2: '#E0E0E8'

								};

								// Apply the theme
								Highcharts.setOptions(Highcharts.theme);";
				}
				else if ($set->chartTheme=="grid_light")
				{
					$theme = "/**
							 * Grid-light theme for Highcharts JS
							 * @author Torstein Honsi
							 */

							// Load the fonts
							Highcharts.createElement('link', {
							   href: 'https://fonts.googleapis.com/css?family=Dosis:400,600',
							   rel: 'stylesheet',
							   type: 'text/css'
							}, null, document.getElementsByTagName('head')[0]);

							Highcharts.theme = {
							   colors: ['#7cb5ec', '#f7a35c', '#90ee7e', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
								  '#55BF3B', '#DF5353', '#7798BF', '#aaeeee'],
							   chart: {
								  backgroundColor: null,
								  style: {
									 fontFamily: 'Dosis, sans-serif'
								  }
							   },
							   title: {
								  style: {
									 fontSize: '16px',
									 fontWeight: 'bold',
									 textTransform: 'uppercase'
								  }
							   },
							   tooltip: {
								  borderWidth: 0,
								  backgroundColor: 'rgba(219,219,216,0.8)',
								  shadow: false
							   },
							   legend: {
								  itemStyle: {
									 fontWeight: 'bold',
									 fontSize: '13px'
								  }
							   },
							   xAxis: {
								  gridLineWidth: 1,
								  labels: {
									 style: {
										fontSize: '12px'
									 }
								  }
							   },
							   yAxis: {
								  minorTickInterval: 'auto',
								  title: {
									 style: {
										textTransform: 'uppercase'
									 }
								  },
								  labels: {
									 style: {
										fontSize: '12px'
									 }
								  }
							   },
							   plotOptions: {
								  candlestick: {
									 lineColor: '#404048'
								  }
							   },


							   // General
							   background2: '#F0F0EA'

							};

							// Apply the theme
							Highcharts.setOptions(Highcharts.theme);";
				}
				else{
					$theme = "";
				}
				return $theme;
}


?>
