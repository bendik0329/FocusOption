<?php

$numOfTicks = 100;

$signupDate = $db['rdate'];

 $set->content .='
 <script src="//cdnjs.cloudflare.com/ajax/libs/d3/3.0.8/d3.min.js" charset="utf-8"></script>
  <script src="'.$set->SSLprefix.'js/d3-timeline/src/d3-timeline.js"></script>
 <link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'js/tooltipster/css/tooltipster.bundle.min.css" />
  <link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'js/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-punk.min.css" />
  	<link href="../css/jquery-ui-timepicker-addon.css" media="screen" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../js/jquery-ui-timepicker-addon.js"></script>
	<script src="js/horizontal-timeline/js/jquery.mobile.custom.min.js"></script>
		<script src="js/horizontal-timeline/js/main.js"></script> <!-- Resource jQuery -->
		<link rel="stylesheet" href="js/horizontal-timeline/css/reset.css"> <!-- CSS reset -->
		<link rel="stylesheet" href="js/horizontal-timeline/css/style.css"> <!-- Resource style -->
		<script src="js/horizontal-timeline/js/modernizr.js"></script>
  <style>
  .scrollable{
	  width:100%;
	  height:	100%;
  }
			a.showDelete {
   position: absolute;
    margin-left: -24px;
    margin-top: 5px;
	
			}
			.mainTD{
				min-width:300px;
				background-color: lightgray;
			}
			.red{
				color:red;
				font-weight:bold;
			}
			.signup_txt{
				margin-top: 10px;
				font-weight: bold;
				font-style: italic;
			}
			.subTable{
				width:100%;
			}
			.subTable thead td {
				padding:5px;
			}
			.subTable .heading {
				font-size:14px;
				text-align:center;
				color:white;
			}
			.deals_rdate{
				padding:4px;
			}
			.defaultDeal td{
				padding:10px 0;
				text-align:center;
			}
			.events-content input{
				// width:140px !important;
				width:127px !important;
			}
			.historySubmitBtn {
				width: 165px!important;
			}
			
			.noData{
				text-align: center;
				font-size: 14px;
				font-weight: bold;
			}
			.tick{
				cursor:pointer;
			}
			rect{
				cursor:pointer;
			}
			
  </style>
  <script type="text/javascript" src="'.$set->SSLprefix.'js/tooltipster/js/tooltipster.bundle.min.js"></script>
								<!-- Deal-types history -o->
								<!--div style="padding-top:20px;">
								<div class="normalTableTitle" style="cursor: pointer;">'.lang('Deal Types History').'</div-->
								
								<!--<div id="tab_history" style="width: 100%; display: '.($note_id ? 'block' : 'none').'">-->
								<div id="tab_history" style="width: 100%;">
									<div>
										<table width="100%">
											<tr>
												<td width="110px">' . lang('Choose a merchant') . ':</td>
												<td width="125px">
													<select id="select_merchant">
														<!-- Content will be loaded via ajax call. -->
													</select>
												</td>
												<td>
													<input type="submit" id="load_deal_types_history" value="' . lang('Load') . '" />
												</td>
												<td class="zoomout" style="display:none;float:right">
													<input type="button" id="btnzoomout" value="' . lang('All Time Histroy') . '" />
												</td>
											</tr>
										</table>
									</div>
									<div id="div_deal_type_history">
										<!-- Content will be loaded via ajax call. -->
									</div>
									<div id="timeline6">
										<!-- Content will be loaded via ajax call. -->
									</div>
									<div id="test123"></div>
									<script>
									
										(function() {
                                                                                    $.get("'.$set->SSLprefix.'ajax/LoadMerchants.php?affiliate_id=' . $_GET['id'] . '", function(res) {
                                                                                        try {
                                                                                            res = JSON.parse(res);
																							var cnt = 0;
                                                                                            if (res["success"]) {
                                                                                                for (var i = 0; i < res["success"].length; i++) {
																									if(res["success"][i]["id"] == 0){
                                                                                                    $("<option>").attr("value", res["success"][i]["id"])
                                                                                                                 .text("'. lang("Choose Merchant") .'")
                                                                                                                 .appendTo("#select_merchant");
																									}
																									else{
																										$("<option>").attr("value", res["success"][i]["id"])
                                                                                                                 .text(res["success"][i]["name"])
                                                                                                                 .appendTo("#select_merchant");
																									}
																												 cnt++;
                                                                                                }
                                                                                          
																								/* if(cnt==1){
																									intMerchantId = res["success"][0]["id"];
																									$("#timeline6").html("");
																									loadDealTypeHistory(intMerchantId);
																								} */
																							  }
                                                                                            
                                                                                        } catch (error) {
                                                                                            //console.log(error);
                                                                                        }
                                                                                    });
										})();
										
										$("#load_deal_types_history").click(function() {
											var intMerchantId = $("#select_merchant").val();
											$("#timeline6").html("");
											$("#test123").html("");
											
											//load history for current month
											
											d = new Date();
											
											month = d.getMonth()+1;
											year = d.getFullYear();
											if (d.getMonth() == 11) {
												var current = new Date(d.getFullYear() + 1, 0, 1);
											} else {
												var current = new Date(d.getFullYear(), d.getMonth() + 1, 1);
											}
											
											endmonth = current.getMonth() +1;
											console.log(endmonth);
											var endyear = current.getFullYear();
											
											
											startdate1 = year + "-" + month + "-01 00:00:00";
											enddate1 = endyear + "-" + endmonth + "-01 00:00:00";
											
											loadDealTypeHistory(intMerchantId,startdate1,enddate1);
											$(".zoomout").show();
											
											return false;
										});
										
										$("#btnzoomout, #btnzoomout1").click(function(){
											var intMerchantId = $("#select_merchant").val();
											$("#timeline6").html("");
											$("#test123").html("");
											loadDealTypeHistory(intMerchantId);
											$(".zoomout").hide();
											return false;
										});
										
										function loadDealTypeHistory1(intMerchantId){
											$.get("'.$set->SSLprefix.'ajax/LoadDealTypesHistory1.php?is_admin=1&affiliate_id=' . $_GET['id'] . '&rdate='. $db['rdate'] .'&merchant_id=" + intMerchantId, function(res) {
										
												    res = JSON.parse(res);
												    
													if (res["success"]) {
														$("#div_deal_type_history").html(res["success"]);
														var amtMerchantDefault = false;
														var amtNumeric = false;
														$(".amt").each(function(key,ele){
															if(false === isNaN(parseInt($(this).val())))
															{
																amtNumeric = true;
															}
															else if($(this).val() == "'. lang('Merchant Default') .'"){
																amtMerchantDefault = true;
															}
														});
														if(amtMerchantDefault == true && amtNumeric == true){
															$(".amt").each(function(key,ele){
																if($(this).val() == "'. lang('Merchant Default') .'"){
																	$(this).val(0);
																}
															});
														}
													}
													
											});
										}
										 var dateClick = false;
										function loadDealTypeHistory(intMerchantId,startdate,enddate){
											
											width = parseInt($("#timeline6").outerWidth());
											console.log($("#div_deal_type_history").outerWidth());
											$.get("'.$set->SSLprefix.'ajax/LoadDealTypesHistory3.php?is_admin=1&affiliate_id=' . $_GET['id'] . '&rdate='. $db['rdate'] .'&merchant_id=" + intMerchantId+"&startdate="+ startdate +"&enddate="+ enddate, function(res) {
											//try {
												    res = JSON.parse(res);
													
													if(res.noData){
														$("#timeline6").addClass("noData").html("'. lang("No Data Found for this month.") .'");
													}
													else{
														$("#timeline6").removeClass("noData");
												    jsonData = res.jsonData;
													
													info = res.info;
													
													if (jsonData) {
														var labelColorTestData = jsonData;
																
																  //var width = 800;
																  $(".tooltip1").tooltipster({});
																 
																  function timelineLabelColor() {
																	var chart = d3.timeline()
																	  .beginning(info.startingTime*1000) 
																	  .ending(info.CurrentTime*1000)
																	  .stack() // toggles graph stacking
																	  .ticks(100)
																	  .margin({left:70, right:30, top:0, bottom:0})
																	  
																	  .click(function (d, i, datum) {
																			// d is the current rendering object
																			// i is the index during d3 rendering
																			// datum is the data object
																			console.log($(d).hasClass("timeline-label"));
																			'.(!isAdmin?'
																			timelineTxt = "";
																			timelineTxt +=  "<table id=\'deal_types_history\' style=\'margin-left:40%\'><tbody><tr>";
																			timelineTxt +="<td valign=\'top\' class=\'mainTD\'><table class=\'subTable\'><thead>";
																			timelineTxt +="<tr><td colspan=\'4\' class=\'heading\'>"+ d.deal+"</td></tr>";
																			timelineTxt += "<tr><td style=\'color:white;\'>' . strtoupper(lang('DATE')) . '</td><td style=\'color:white;\'>' . lang('COMMISSION')  . '</td></tr></thead><tbody>";
																			timelineTxt += "<tr><td style=\'width: 145px;\'>"+ d.rdate +"</td><td style=\'width: 140px;\'>$"+d.amount+"</td>";
																			if(d.deal=="Tier"){
																				timelineTxt += "<td style=\'width: 140px;\'>"+d.tier_amount+"</td><td style=\'width: 140px;\'>"+d.tier_pcpa+"</td>";
																			}
																			timelineTxt +="</tr>";
																			timelineTxt += "</tbody></table>";
																			':'
																			timelineTxt = "";
																			if(d.deal=="Tier")
																				timelineTxt +=  "<table id=\'deal_types_history\' style=\'margin-left:20%\'><tbody><tr>";
																			else
																				timelineTxt +=  "<table id=\'deal_types_history\' style=\'margin-left:40%\'><tbody><tr>";
																			
																			timelineTxt +="<td valign=\'top\' class=\'mainTD\'><table class=\'subTable\'><thead>";
																			timelineTxt +="<tr><td colspan=\'4\' class=\'heading\'>"+ d.deal+"</td></tr>";
																			timelineTxt += "<tr><td style=\'color:white;\'>' . strtoupper(lang('DATE')) . '</td><td style=\'color:white;\'>' . lang('COMMISSION')  . '</td>";
																			
																			if(d.deal=="Tier"){
																				timelineTxt += "<td style=\'color:white;\'>' . strtoupper(lang('Tier Amount')) . '</td>";
																				timelineTxt += "<td style=\'color:white;\'>' . strtoupper(lang('Tier PCPA')) . '</td>";
																			}
																		
																			timelineTxt += "</tr></thead><tbody>";
																			//timelineTxt += "<tr><td style=\'width: 140px;\'>"+ d.rdate +"</td><td style=\'width: 140px;\'>$"+d.amount+"</td></tr>";
																			timelineTxt += "<tr><td><input class = \'deals_rdate\' type=\'text\'  data-rdate_id=\'"+ d.id +"\' value=\'"+d.rdate+"\' readonly/></td>";
																			timelineTxt +="<td><input type=\'text\' style=\'width: 140px;\'  data-amount_id=\'" +d.id +"\' value=\'" + d.amount +"\'>$</td>";
																			if(d.deal=="Tier"){
																			timelineTxt +="<td><input type=\'text\'  data-tier_amount_id=\'" +d.id +"\' value=\'" + d.tier_amount +"\'></td>";
																			timelineTxt +="<td><input type=\'text\'  data-tier_pcpa_id=\'" +d.id +"\' value=\'" + d.tier_pcpa +"\'></td>";
																			}
																			timelineTxt += "</tr>";
																			timelineTxt += "<tr><td colspan=2 text-align=\'center\'><input type=\'submit\' class=\'historySubmitBtn\' onclick=\'return false;\' value=\'' . lang('Save changes') . '\'/></td></tr>";
																			timelineTxt += "</tbody></table>";
																			
																			$("#test123").html(timelineTxt);
																			$(".deals_rdate").datetimepicker({
																				timeFormat: "HH:mm:ss",
																				dateFormat:"yy-mm-dd",
																				onClose:function(dt,inst){
																					
																					var id    = $.trim($(this).data("rdate_id"));
																					var rdate = dt;
																					return updateDealTypesHistory("rdate", id, rdate);
																					
																				}
																			});
																			
																			$("[data-amount_id]").change(function() {
																					var id     = $.trim($(this).attr("data-amount_id"));
																																var amount = $(this).val();
																																
																					if (isNaN(amount) || amount === "") {
																																	amount = "NULL";
																					}
																					return updateDealTypesHistory("amount", id, amount);
																				});
																				
																				$("[data-tier_pcpa_id]").change(function() {
																					var id     = $.trim($(this).attr("data-tier_pcpa_id"));
																																var amount = $(this).val();
																																
																					if (isNaN(amount) || amount === "") {
																																	amount = "NULL";
																					}
																					return updateDealTypesHistory("tier_pcpa", id, amount);
																				});
																				
																				$("[data-tier_amount_id]").change(function() {
																					var id     = $.trim($(this).attr("data-tier_amount_id"));
																					var amount = $(this).val();
																					return updateDealTypesHistory("tier_amount", id, amount);
																				});
																				
																			') .'
																			
																			$( "#deal_types_history tr td input" ).click(function() {
				
													$( "#deal_types_history tr td a" ).remove(".showDelete");
													// $( "input[name="data-rdate_id"]" ).remove(".showDelete");
													$( "#deal_types_history tr td a" ).remove(".showDelete");
													
													
													 
												  var a = ($(this).attr(\'data-amount_id\'));
													  if (!a) {
														  a = ($(this).attr(\'data-rdate_id\'));
													  }
													// $(this).addClass("showDelete");
													
														$(this).after("<a onclick = \"if (! confirm(\''.lang('Are you sure you want to delete').'?\')) { return false; }\" href=\"/admin/affiliates.php?act=delete&id='.$id.'\" class=\"showDelete\"><img src=\"images/x.png\"/></a>");
														
														var _href = $("a.showDelete").attr("href");
														$("a.showDelete").attr("href", _href + "&deldth=" + a);
													
													//$("div[data-rdate_id==\"a\"]").remove();
													//$("div[data-amount_id==\"a\"]").remove();
													//$( "#deal_types_history tr td" ).remove(".showDelete");
													//$( "#deal_types_history tr td" ).remove(".showDelete");
													
													
													
													});
																			
											  }).mouseover(function (d, i, datum) {
																
																	end_time = new Date(d.end_date);
																	//end_time = cleanDate("/Date("+ d.ending_time +")/"); 
																	//end_time = end_time.toGMTString();
																	end_time  = end_time.getFullYear() + "-" + (end_time.getMonth()+1)+"-"+end_time.getDate() + " "+ end_time.getHours() + ":" + end_time.getMinutes() + ":" + end_time.getSeconds();
																	
																	if(datum.label == "PNL" || datum.label == "DCPA" || datum.label == "Net Deposit" || datum.label == "Positions Rev Share" ){
																		tooltip_content = d.rdate + \'<br>\' + end_time + "<br> "+d.amount + "%";
																	}
																	else
																	tooltip_content = d.rdate + \'<br>\' + end_time + "<br>'. $set->currency .' "+d.amount;
																	
																		
																$("#"+d.id).tooltipster({
																   theme: \'tooltipster-punk\',
																  contentAsHTML : true,
																  content: tooltip_content});
																	$("#" + d.id).tooltipster(\'open\');
																		// d is the current rendering object
																		// i is the index during d3 rendering
																		// datum is the data object
																	  })
																	  ;
																	
																	var svg = d3.select("#timeline6").append("svg").attr("width",width)
																	  .datum(labelColorTestData).call(chart);
	
																	svg.selectAll(".axis text")  // select all the text elements for the xaxis
																	  .attr("transform", function(d) {
																		  return "translate(" + this.getBBox().height*-2 + "," + (this.getBBox().height + 10) + ")rotate(-45)";
																	});
																	
																	if(typeof startdate == "undefined" ){
																	dt = new Date("'. $signupDate .'");
																	day =  dt.getDate();
																	month = getMonthName(dt.getMonth()+1);
																	year = dt.getFullYear().toString().substr(-2);
																		
																	svg.select(".axis").append("g").attr("class", "tick signup").attr("transform","translate(100,0)").append("text").text("'.lang("Sign Up").' [ " + day +" " + month + " " + year + "] ")
																	.attr("dy",".71em").attr("y","9").attr("x","-23" )
																	  .attr("transform", function(d) {
																		  return "translate(" + this.getBBox().height*-2 + "," + (this.getBBox().height + 10) + ")rotate(-45)";
																	}).style("text-anchor", "middle");
																	}
																	else{
																		
																		dt = new Date(startdate);
																		day =  dt.getDate();
																		month = getMonthName(dt.getMonth()+1);
																		year = dt.getFullYear().toString().substr(-2);
																		
																		day = pad(day, 2); 
																		
																		svg.select(".axis").append("g").attr("class", "tick signup").attr("transform","translate(100,0)").append("text").text(day +" " + month + " " + year )
																	  .attr("dy",".71em").attr("y","9").attr("x","0" )
																	  .attr("transform", function(d) {
																		  return "translate(" + this.getBBox().height*-2 + "," + (this.getBBox().height + 10) + ")rotate(-45)";
																		}).style("text-anchor", "middle");
																	}
																	
																	svg.selectAll(".timeline-label").on("click" , function(d){
																		return false;
																	});
																	svg.selectAll(".axis text").on("click" , function(d){
																		if(dateClick == true){
																			var intMerchantId = $("#select_merchant").val();
																			$("#timeline6").html("");
																			//$("#timeline6").remove();
																			$("#test123").html(""); 
																			loadDealTypeHistory(intMerchantId);
																			dateClick = false;
																		}
																		else{
																			dateClick = true;
																			if(typeof d.getMonth === "function"){
																				month = d.getMonth()+1;
																				year = d.getFullYear();
																				if (d.getMonth() == 11) {
																					var current = new Date(d.getFullYear() + 1, 0, 1);
																				} else {
																					var current = new Date(d.getFullYear(), d.getMonth() + 1, 1);
																				}
																				
																				endmonth = current.getMonth()+1;
																				var endyear = current.getFullYear();
																				
																				
																				startdate1 = year + "-" + month + "-01 00:00:00";
																				enddate1 = endyear + "-" + endmonth + "-01 00:00:00";
																				var intMerchantId = $("#select_merchant").val();
																				$("#timeline6").html("");
																				$("#test123").html(""); 
																				loadDealTypeHistory(intMerchantId,startdate1,enddate1);
																				$(".zoomout").show();
																			}
																		}
																	});
																	
																	
																  }
																  
																  timelineLabelColor();
																
																function cleanDate(d) {
																return new Date(+d.replace(/\/Date\((\d+)\)\//, \'$1\'));
															}
														//$("#div_deal_type_history").html(res["success"]);
														var amtMerchantDefault = false;
														var amtNumeric = false;
														$(".amt").each(function(key,ele){
															if(false === isNaN(parseInt($(this).val())))
															{
																amtNumeric = true;
															}
															else if($(this).val() == "'. lang('Merchant Default') .'"){
																amtMerchantDefault = true;
															}
														});
														if(amtMerchantDefault == true && amtNumeric == true){
															$(".amt").each(function(key,ele){
																if($(this).val() == "'. lang('Merchant Default') .'"){
																	$(this).val(0);
																}
															});
														}
													}
													
													}
												/* } catch (error) {
													console.log(error);
												} */
											});
										}
										function getMonthName($m){
											arr = new Array();
											arr[1] = "'. lang("Jan") .'";
											arr[2] = "'. lang("Feb") .'";
											arr[3] = "'. lang("Mar") .'";
											arr[4] = "'. lang("Apr") .'";
											arr[5] = "'. lang("May") .'";
											arr[6] = "'. lang("Jun") .'";
											arr[7] = "'. lang("Jul") .'";
											arr[8] = "'. lang("Aug") .'";
											arr[9] = "'. lang("Sep") .'";
											arr[10] = "'. lang("Oct") .'";
											arr[11] = "'. lang("Nov") .'";
											arr[12] = "'. lang("Dec") .'";
											
											return arr[$m];
											
										}
										function pad(n, width, z) {
										  z = z || "0";
										  n = n + "";
										  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
										}
										
									</script>
									<script>
										/**
										 * Performs an ajax call in order to update chosen deal-type record.
										 *
										 * @param  string     subject
										 * @param  int        id
										 * @param  int|string value
										 * @return bool
										 */
										function updateDealTypesHistory(subject, id, value) {
											$.post("ajax/UpdateDealTypesHistory.php", 
											       {
													   subject: subject,
													   id     : id,
													   value  : value
												   },
												   function(res) {
													   try {
														    res = JSON.parse(res);
															
															if (res["success"] && subject === "rdate") {
																
																//alert( res["success"]);
																	$.fancybox({ 
																 closeBtn:false, 
																  minWidth:"250", 
																  minHeight:"180", 
																  autoCenter: true, 
																  afterClose:function(){
																	  
																  },			  
																  content: "<div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +res["success"] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'\'></div></div>" 
																  });
																  
																
															} else if (res["success"] && subject === "amount") {
																
																//alert(res["success"]);
																$.fancybox({ 
																 closeBtn:false, 
																  minWidth:"250", 
																  minHeight:"180", 
																  autoCenter: true, 
																  afterClose:function(){
																	 
																  },			  
																  content: "<div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +res["success"] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
																  });
																    
																
															}
															else if (res["success"] && subject === "tier_amount" ||  subject === "tier_pcpa") {
														
																//alert(res["success"]);
																	$.fancybox({ 
																 closeBtn:false, 
																  minWidth:"250", 
																  minHeight:"180", 
																  autoCenter: true, 
																  afterClose:function(){
																	  
																  },			  
																  content: "<div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>" +res["success"] +"</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
																  });
																
																
																 
															}
															
															else {
																//console.log(res["error"]);
															}
														    
															$(".btnContinue").on("click",function(){
																  var intMerchantId = $("#select_merchant").val();
                                                                 $("#timeline6").html("");
                                                                 $("#test123").html("");
																 loadDealTypeHistory(intMerchantId);
																 $.fancybox.close()
																  });
															
													   } catch (error) {
														   //console.log(error);
													   }
												   });
											return false;
										}
										
									
										
										
								  </script>
								</div>
								' ;