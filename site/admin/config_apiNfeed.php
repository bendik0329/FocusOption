<?php

$qry     = "SELECT * FROM config_api_n_feeds ORDER BY id ASC";
$feedsqq = function_mysql_query($qry,__FILE__,__FUNCTION__);
$total_feeds= mysql_num_rows($feedsqq);

$set->webAddress = ($set->isHttps?$set->webAddressHttps:$set->webAddress);

$set->content .='

<table width="100%" border="0" cellpadding="5" cellspacing="0" class="tblDetails">
						<tbody>
						';
							if ($set->userInfo['userType'] == "sys") {
						
						
						
							$creativefeedUrl = "";
				
			/* 	if (!empty($set->apiStaticIP) && !empty($set->apiToken)) {
					// $creativefeedUrl = $set->webAddress .'/affiliate/apifeed.php?apiToken='. $db['apiToken'].'&apiStaticIP='. $db['apiStaticIP'].'&affiliate_id='. $id  .'&fromdate=' . date("Y-m-01",strtotime("-1 Months"));
					$creativefeedUrl = $set->webAddress .'api/feeds/creative.php?apiToken='. $set->apiToken;
					
				} */
				
				
				
				
						
						$apiFeed = '
											
												<tr class="feed1" style="padding:5px 0px;">
													<td align="center"><select name="db[apiAccessType]" id="apiAccessType" onchange="checkApiAccessType(this)">
																						<option  value="" selected>' . lang('None') . '</option>
																						<option value="affiliates">' . lang('Affiliates') . '</option>
																						<option value="creative">' . lang('Creative') . '</option>
																						<option value="merchants">' . lang('Merchants') . '</option>
																						<option value="getFTDs">' . lang('Get FTDs') . '</option>
																						<option value="leadStatus">' . lang('Lead Status') . '</option>
																						<option value="pushmerchants">' . lang('Push Merchant') . '</option>
																					</select></td>
													<td align="center"><select name="db[outputType]" id="outputType">
																						<option  value="XML" selected>' . lang('XML') . '</option>
																						<option value="JSON">' . lang('JSON') . '</option>
																					</select></td>
													
													<td align="center"><textarea  name="db[apiStaticIP]" id="apiStaticIP" rows="3" style="width:110px;"></textarea></td>
													<td align="center"><span><input style="width: 240px;" id="apiToken" type="text" name="db[apiToken]"/></span><span>&nbsp;</span><span><button type="button" id="putDefaultTextGuid1" onClick="putDefaultTextGuid(this)">'.lang('Generate').'</button></span></td>
													<td align="center"><textarea style="width:400px;" rows="3" id="feedurl" type="text"  readonly ></textarea></td>
													<td align="center"><div style="float:left;"><label class="switch"><input type="checkbox" name="status" id="status"/><div class="slider round"></div></label></div></td>
													<td align="center">
													<input type="hidden" value="0" name="feed_id" id="feed_id">
													<input type="button" id="testButton" value="'.lang('Save').'" onClick="return saveApiFeed(this,0)"/>
													<input type="button" value="'.lang('Delete').'" onClick="return deleteFeed(this)" style="display:none;" class="deleteBtn"/>
													</td>
													
												</tr>
												
											
									';
								
								$set->content .= '
								<tr>
								<td colspan=3 valign="top"><span style="padding:5px;">'.lang('API Access ').':</span><div class="question tooltip"><span class="tooltiptext">'. $tooltips['APIAccesstocreativelist'] .'</span></div>&nbsp;&nbsp;<div class="exclamation tooltip"><span class="tooltiptext">'. lang("Only available for Affiliate Buddies Admin.") .'</span></div>
								<p class="success config_msg"></p>
								</td></tr>
								<tr><td colspan=3><input type="button" value="'.lang('Add New').'" onClick="addNewFeedRow()"/></td></tr>
								<tr><td colspan=3>
								<table class="normal apifeedTable" width="100%" border="0" cellpadding="0" cellspacing="0">
												<thead><tr>
													<td align="center">'.lang('Access Type').'</td>
													<td align="center">'.lang('Output Type').'</td>
													<td align="center">'.lang('Company Static IP').'</td>
													<td align="center">'.lang('Token').'</td>
													<td align="center">'.lang('FeedUrl').'</td>
													<td align="center">'.lang('Status').'</td>
													<td align="center">'.lang('Action').'</td>
												</tr></thead>
								<tfoot>';
								if($total_feeds>0){
									while($feedsww = mysql_fetch_assoc($feedsqq)){
										if (!empty($feedsww['apiStaticIP']) && !empty($feedsww['apiToken'])) {
											// $creativefeedUrl = $set->webAddress .'/affiliate/apifeed.php?apiToken='. $db['apiToken'].'&apiStaticIP='. $db['apiStaticIP'].'&affiliate_id='. $id  .'&fromdate=' . date("Y-m-01",strtotime("-1 Months"));
											if($feedsww['apiAccessType'] == "affiliates")
												$creativefeedUrl = $set->webAddress .'api/feeds/getAffiliates.php?apiToken='. $feedsww['apiToken'];
											else if($feedsww['apiAccessType'] == "merchants")
												$creativefeedUrl = $set->webAddress .'api/feeds/merchants.php?apiToken='. $feedsww['apiToken'];
											else if($feedsww['apiAccessType'] == "leadStatus")
												$creativefeedUrl = $set->webAddress .'api/feeds/accountStatus.php?apiToken='. $feedsww['apiToken'];
											else if($feedsww['apiAccessType'] == "getFTDs")
												$creativefeedUrl = $set->webAddress .'api/feeds/getFTDs.php?apiToken='. $feedsww['apiToken'];
											else if($feedsww['apiAccessType'] == "pushmerchants")
												$creativefeedUrl = $set->webAddress .'api/add/pushMerchants.php?apiToken='. $feedsww['apiToken']."&name=&type=";
											else
												$creativefeedUrl = $set->webAddress .'api/feeds/creative.php?apiToken='. $feedsww['apiToken'];
											
										}
										
										//display rows from table
										$set->content .= '<tr class="feed1" style="padding:5px 0;">
													<td align="center"><select name="db[apiAccessType]" id="apiAccessType" onchange="checkApiAccessType(this)">
																						<option '.($feedsww['apiAccessType']=='' ? " selected " : "").' value="" selected>' . lang('None') . '</option>
																						<option '.($feedsww['apiAccessType']=='creative' ? " selected " : "").'value="creative">' . lang('Creative') . '</option>
																						<option '.($feedsww['apiAccessType']=='merchants' ? " selected " : "").'value="merchants">' . lang('Merchants') . '</option>
																						<option '.($feedsww['apiAccessType']=='affiliates' ? " selected " : "").'value="affiliates">' . lang('Affiliates') . '</option>
																						<option '.($feedsww['apiAccessType']=='leadStatus' ? " selected " : "").'value="leadStatus">' . lang('Lead Status') . '</option>
																						<option '.($feedsww['apiAccessType']=='getFTDs' ? " selected " : "").'value="getFTDs">' . lang('Get FTDs') . '</option>
																						<option '.($feedsww['apiAccessType']=='pushmerchants' ? " selected " : "").'value="pushmerchants">' . lang('Push Merchants') . '</option>
																					</select></td>
													<td align="center"><select name="db[outputType]" id="outputType">
																						<option  value="XML" '.($feedsww['outputType']=='XML' ? " selected " : "").'>' . lang('XML') . '</option>
																						<option value="JSON" '.($feedsww['outputType']=='JSON' ? " selected " : "").'>' . lang('JSON') . '</option>
																					</select></td>
																					
													<td align="center"><textarea  name="db[apiStaticIP]" id="apiStaticIP" rows="3" style="width:110px;">'. $feedsww['apiStaticIP'] .'</textarea></td>
													<td align="center"><span><input style="width: 240px;" id="apiToken" type="text" name="db[apiToken]" value="'.$feedsww['apiToken'].'" /></span><span>&nbsp;</span><span><button type="button" id="putDefaultTextGuid1" onClick="putDefaultTextGuid(this)">'.lang('Generate').'</button></span></td>
													<td align="center"><textarea style="width:400px;" rows="3" type="text" id="feedurl"  readonly >'.$creativefeedUrl .'</textarea></td>
													<!--td align="center"> '.(empty($creativefeedUrl) || $feedsww['apiAccessType']=='None' || $feedsww['apiAccessType']=='' ?'<span style="color:red;">'.lang('Inactive') .'</span>' : '<span style="color:green;">'.lang('Active')).'</span></td-->
													<td align="center"><div style="float:left;"><label class="switch"><input type="checkbox" name="status" id="status" '.($feedsww['status']==1 ? 'checked="checked"' : '').' /><div class="slider round"></div></label></div></td>
													<td align="center">
													<input type="hidden" value="'. $feedsww['id'] .'" name="feed_id" id="feed_id">
													<input type="submit" value="'.lang('Update').'" onClick="return saveApiFeed(this)"/>
													<input type="button" value="'.lang('Delete').'" onClick="return deleteFeed(this)"/>
													</td>
													
												</tr>';
										
									}
								}
								else{
								$set->content.=$apiFeed;
								}
								$set->content.='</tfoot>
								</table>
								
								</td>
							</tr>
							<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
							<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
							<script>
								
								function S4() {
								return (((1+Math.random())*0x10000)|0).toString(16).substring(1); 
								}
								
								/* $(".putDefaultTextGuid").on("click",function()	{
											// debugger;
									guid = (S4() + S4() + "-" + S4() + "-4" + S4().substr(0,3) + "-" + S4() + "-" + S4() + S4() + S4()).toLowerCase();
									$("#apiToken").val(guid);
									
									//return false;
								});  */
								
								function putDefaultTextGuid(e){
									// debugger;
									guid = (S4() + S4() + "-" + S4() + "-4" + S4().substr(0,3) + "-" + S4() + "-" + S4() + S4() + S4()).toLowerCase();
									//$("#apiToken").val(guid);
									$(e).closest("tr").find("#apiToken").val(guid);
								}
						
								var feed = 2;
								function addNewFeedRow(){
												
									
									$(".defaultFeedRow tr").find("tr").removeClass("feed1");
									var lastRow = $(".defaultFeedRow tr").html();
									$(".apifeedTable tfoot").append("<tr class=\'feed"+ feed +"\' >" + lastRow + "</tr>");
									  feed++;
								}
								
								function deleteFeed(e){
									feed_id = $(e).closest("tr").find("#feed_id").val();
									
									var objParams = { 
											type:"delete",
											id:feed_id
										};
									
									$.post("'.$set->SSLprefix.'ajax/configApiFeedsActions.php", objParams, function(res) {
										if(res=="deleted"){
											$(e).closest("tr").hide();
											$(".config_msg").html("Feed deleted").css("color","green");
										}
										else {
											$(".config_msg").html("Problem in saving data. Try Again.").css("color","red");
										}
									});
									
								}
								
								function saveApiFeed(e,id){
									if($(e).closest("tr").find("#apiAccessType").val() == ""){
										$.prompt("'.lang('Please select Access Type').'", {
												top:200,
												title: "ERROR",
												buttons: { "'.lang('Ok').'":true},
												submit: function(e,v,m,f){
													if(v){
													//
													}
													else{
														//
													}
												}
										});
										return false;
									}
									//if(checkApiAccessType(0)){
									id= $(e).closest("tr").find("#feed_id").val();
									
									if(id==0){
										 var objParams = { 
											apiAccessType        : $(e).closest("tr").find("#apiAccessType").val(),
											apiToken        : $(e).closest("tr").find("#apiToken").val(),
											apiStaticIP        : $(e).closest("tr").find("#apiStaticIP").val(),
											outputType        : $(e).closest("tr").find("#outputType").val(),
											createdByUserID : '. $set->userInfo['id'] .',
											status: $(e).closest("tr").find("#status").val(),
											type: "save",
											id:0
										};
									}
									else{
										var objParams = { 
											apiAccessType        : $(e).closest("tr").find("#apiAccessType").val(),
											apiToken        : $(e).closest("tr").find("#apiToken").val(),
											apiStaticIP        : $(e).closest("tr").find("#apiStaticIP").val(),
											outputType        : $(e).closest("tr").find("#outputType").val(),
											createdByUserID : '. $set->userInfo['id'] .',
											status: $(e).closest("tr").find("#status").val(),
											type: "update",
											id:id
										};
									}
									
									$.post("'.$set->SSLprefix.'ajax/configApiFeedsActions.php", objParams, function(res) {
										if(res==0 || res ==false || res=="false"){
											
										}
										else{
											res= $.parseJSON(res);
										}
										if (res.type=="Save") {
											$(e).closest("tr").find("#feed_id").val(res.id);
											$(e).closest("tr").find("#testButton").val("Update");
											
											if(res.apiStaticIP !="" && res.apiToken!="")
											{
												if(res.apiAccessType == "affiliates")
													url = "'.$set->webAddress .'api/feeds/getAffiliates.php?apiToken="+res.apiToken;
												else if(res.apiAccessType == "merchants")
													url = "'.$set->webAddress .'api/feeds/merchants.php?apiToken="+res.apiToken;
												else if(res.apiAccessType == "leadStatus")
													url = "'.$set->webAddress .'api/feeds/accountStatus.php?apiToken="+res.apiToken;
												else if(res.apiAccessType == "getFTDs")
													url = "'.$set->webAddress .'api/feeds/getFTDs.php?apiToken="+res.apiToken;
												else if(res.apiAccessType == "pushmerchants")
													url = "'.$set->webAddress .'api/add/pushMerchants.php?apiToken="+res.apiToken + "&name=&type=";
												else
													url = "'.$set->webAddress .'api/feeds/creative.php?apiToken="+res.apiToken;
											
												$(e).closest("tr").find("#feedurl").val(url);
											}
											$(e).closest("tr").find(".deleteBtn").show();
											
											$(".config_msg").html("API Feed saved successfully").css("color","green");
										}
										else if (res.type=="update") {
											
											if(res.apiStaticIP !="" && res.apiToken!="")
											{
												if(res.apiAccessType == "affiliates")
													url = "'.$set->webAddress .'api/feeds/getAffiliates.php?apiToken="+res.apiToken;
												else if(res.apiAccessType == "merchants")
													url = "'.$set->webAddress .'api/feeds/merchants.php?apiToken="+res.apiToken;
												else if(res.apiAccessType == "leadStatus")
													url = "'.$set->webAddress .'api/feeds/accountStatus.php?apiToken="+res.apiToken;
												else if(res.apiAccessType == "getFTDs")
													url = "'.$set->webAddress .'api/feeds/getFTDs.php?apiToken="+res.apiToken;
												else if(res.apiAccessType == "pushmerchants")
													url = "'.$set->webAddress .'api/add/pushMerchants.php?apiToken="+res.apiToken +"&name=&type=";
												else
													url = "'.$set->webAddress .'api/feeds/creative.php?apiToken="+res.apiToken;
											
												$(e).closest("tr").find("#feedurl").val(url);
											}
										
											$(e).closest("tr").find("#feedurl").val(url);
											
											$(".config_msg").html("Details Updated.").css("color","green");
										}
										else if (2 === res) {
											$.prompt("'.lang('You cannot have row for same Access Type').'", {
												top:200,
												title: "ERROR",
												buttons: { "'.lang('Ok').'":true},
												submit: function(e,v,m,f){
													if(v){
													
													}
													else{
													}
												}
											});
										} else {
											$(".config_msg").html("Problem in saving data. Try Again.").css("color","red");
										}
									});
									//}
									return false;
									
								}
								
								function checkApiAccessType(e){
									accesstype="";
									ret = true;
									$("select[name=\'db[apiAccessType]\']:visible").each(function(){
										if(accesstype == $(this).val()){
											obj = $(this);
											
											$.prompt("'.lang('You cannot have row for same Access Type').'", {
												top:200,
												title: "ERROR",
												buttons: { "'.lang('Ok').'":true},
												submit: function(e,v,m,f){
													if(v){
													obj.focus();
													ret= false;
													}
													else{
														ret = true;
													}
												}
											});
										}
										accesstype = $(this).val();
										if(ret==false)
											return false;
										else
											return true;
									});
									
									return true;
								}
							</script>';
								
							}
						$set->content .= '
						</tbody>
						</table>
						<table class="defaultFeedRow" style="display:none">'. $apiFeed .'</table>';
						

?>