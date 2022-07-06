<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/manager/";
if (!isManager()) _goto($lout);
switch ($act) {
	
		
	default:
		$set->pageTitle = 'API Integration';
		
		/////////////////////////////////////////////// GET FREE CAMPS:
		if(isset($_REQUEST['getFreeCamps'])){
			$merchantid= aesDec($_COOKIE['mid']);
			$totalCampaigns = mysql_result(function_mysql_query('SELECT count(id) FROM affiliates_campaigns_relations WHERE  merchantid = "' . $merchantid .'" and affiliateID=0',__FILE__),0,0);
			echo $totalCampaigns;
			die();
			
		}
		
			
			
		////////////////////////////////////////////// STANDARD FLOW:
		//$qrr = 'SELECT affiliates_campaigns_relations.id, affiliates_campaigns_relations.affiliateID AS affID, affiliates.username,affiliates_campaigns_relations.campID AS campID FROM affiliates_campaigns_relations INNER JOIN affiliates ON affiliates_campaigns_relations.affiliateID=affiliates.id';
			$qrr = 'SELECT affiliates_campaigns_relations.id, affiliates_campaigns_relations.affiliateID AS affID, affiliates.username,affiliates_campaigns_relations.campID AS campID FROM affiliates_campaigns_relations INNER JOIN affiliates ON affiliates_campaigns_relations.affiliateID=affiliates.id where affiliates.group_id = ' . $set->userInfo['group_id'];			

//		die ($qrr);
		$relationsQ = function_mysql_query($qrr,__FILE__);
		$relationsStr = '';
		$l=0;
		while($row=mysql_fetch_assoc($relationsQ)){
			$l++;
			$relationsStr.= '<tr '.($l % 2 ? 'class="trLine"' : '').' rel="'.$row['id'].'">
								<td style="text-align:center; padding-top:8px; padding-bottom:8px">'.$row['affID'].'</td>
								<td style="text-align:center" class="affs" rel="'.$row['affID'].'">'.$row['username'].'</td>
								<td style="text-align:center" class="camps" rel="'.$row['campID'].'">'.$row['campID'].'</td>
								<td class="deleteCA"><span style="cursor:pointer; cursor:hand">Delete</span></td>
							</tr>';
		}
		
		////////////////////////////////////////////// END OF STANDARD FLOW
		
		
		
		////////////////////////////////////////////// AFFILIATES [FLOW,EDIT,ADD]
		$merchantid= aesDec($_COOKIE['mid']);
		$affiliatesQ = function_mysql_query('SELECT af.id,af.username FROM affiliates af where af.group_id = ' . $set->userInfo['group_id'] . ' and af.id not in (select ac.affiliateID from affiliates_campaigns_relations ac where merchantid = "' . $merchantid .'" and  ac.affiliateID!=0 '.(isset($_REQUEST['affID']) ? 'AND ac.affiliateID!='.$_REQUEST['affID'] : '').') and  af.valid=1 order by af.username ASC',__FILE__);
		
		
		$affiliatesStr='';
		while($row=mysql_fetch_assoc($affiliatesQ)){
			$affiliatesStr.= '<option value='.$row['id'].' '.((isset($_REQUEST['affID']) AND $_REQUEST['affID']==$row['id']) ? 'selected' : '').'>'.$row['username'].'</option>';
			if(isset($_REQUEST['affID']) AND $_REQUEST['affID']==$row['id']){
				$defaultAff = $row['username'];
			}
		}
		
		if(isset($_REQUEST['updateAff']) AND isset($_REQUEST['val'])){
			//echo 'UPDATE affiliates_campaigns_relations SET affiliateID='.$_REQUEST['val'].' WHERE id='.$_REQUEST['updateAff'];
			function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID='.$_REQUEST['val'].' WHERE id='.$_REQUEST['updateAff'],__FILE__);
			$row = mysql_fetch_assoc(function_mysql_query('SELECT username FROM affiliates WHERE id='.$_REQUEST['val'],__FILE__));
			die($row['username']);
		}
		
		if(isset($_REQUEST['ajaxAff'])){
			echo '<select class="affSelect" name="affID"><option id=-1>Select Affiliate</option>'.$affiliatesStr.'</select><input class="affSave" type="button" value="'.lang('Save').'"/><input type="button" class="affCancel" value="'.lang('Cancel').'"/>
			<script type="text/javascript">
				$( document ).ready(function() {
					$(".affCancel").click(function(){
						$(this).parent().html("'.$defaultAff.'");
					});
					$(".affSave").click(function(){
						//$(this).parent().html($(this).parent().find(".affSelect").val());
						var td = this;
						$.ajax({
							url: "http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&updateAff='.$_REQUEST['rid'].'&val="+$(this).parent().find(".affSelect").val(),
							dataType: "HTML",
						}).done(function(response) {
							console.log(response);
							var id = $(td).parent().find(".affSelect").val();
							$(td).parent().attr("rel",id);
							$(td).parent().html(response);
							getTotalFreeCamps();
						});
					});
				});
			</script>
			';
			die();
		}
		
		
		////////////////////////////////////////////// END OF AFFILIATES
		
		
		
		////////////////////////////////////////////// CAMPAIGNS [FLOW,EDIT,ADD]
		
		
		$campaignsQ = function_mysql_query('SELECT * FROM affiliates_campaigns_relations WHERE  merchantid = "' . $merchantid .'" and affiliateID=0 '.(isset($_REQUEST['campID']) ? ' OR campID="'.$_REQUEST['campID'].'"' : '').' ORDER BY campID ASC',__FILE__); //OR die(mysql_error());
		//die('SELECT * FROM affiliates_campaigns_relations WHERE  merchantid = "' . $merchantid .'" and affiliateID=0 '.(isset($_REQUEST['campID']) ? ' OR campID='.$_REQUEST['campID'] : '').' ORDER BY campID ASC');
		$campaignsStr = '';
		while($row=mysql_fetch_assoc($campaignsQ)){
			$campaignsStr.= '<option value='.$row['campID'].' '.((isset($_REQUEST['campID']) AND $_REQUEST['campID']==$row['campID']) ? 'selected' : '').'>'.$row['campID'].'</option>';
		}
		
		
		if(isset($_REQUEST['updateCamp']) AND isset($_REQUEST['val'])){
			//echo 'UPDATE affiliates_campaigns_relations SET affiliateID='.$_REQUEST['val'].' WHERE id='.$_REQUEST['updateAff'];
			$currAff = mysql_fetch_assoc(function_mysql_query('SELECT affiliateID FROM affiliates_campaigns_relations WHERE  merchantid = "' . $merchantid .'" and id='.$_REQUEST['updateCamp'],__FILE__));
			if(!$currAff['affiliateID']){
				//die('An error occured, no affiliate was found.');
				$currAff['affiliateID'] = -1;
			}else{
				function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID=0 WHERE  merchantid = "' . $merchantid .'" and id='.$_REQUEST['updateCamp'],__FILE__);
			}
			function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID='.$currAff['affiliateID'].' WHERE  merchantid = "' . $merchantid .'" and affiliateID=0 AND campID='.$_REQUEST['val'],__FILE__); //OR die(mysql_error());
			$currID = mysql_result(function_mysql_query('SELECT id FROM affiliates_campaigns_relations WHERE campID='.$_REQUEST['val'],__FILE__),0,0);
			die($_REQUEST['val'].'|'.$currID);
		}
		
		if(isset($_REQUEST['ajaxCamp'])){
			echo '<select name="affID" class="campSelect"><option id=-1>Select Campaign</option>'.$campaignsStr.'</select><input class="campSave" type="button" value="'.lang('Save').'"/><input class="campCancel" type="button" value="'.lang('Cancel').'"/>
			<script type="text/javascript">
				$( document ).ready(function() {
					$(".campCancel").click(function(){
						$(this).parent().html($(this).parent().find(".campSelect").val());
					});
					$(".campSave").click(function(){
						var td = this;
						$.ajax({
							url: "http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&updateCamp='.$_REQUEST['rid'].'&val="+$(this).parent().find(".campSelect").val(),
							dataType: "HTML",
						}).done(function(response) {
							var id = $(td).parent().find(".campSelect").val();
							response = response.split("|");
							$(td).parent().attr("rel",$(td).parent().find(".campSelect").val());
							$(td).parent().parent().attr("rel",response[1]);
							$(td).parent().html(response[0]);
							getTotalFreeCamps();
						});
					});
				});
			</script>
			';
			die();
		}
		
		////////////////////////////////////////////// END OF CAMPAIGNS


		////////////////////////////////////////////// DELETE BTN ACTION

		if(isset($_REQUEST['deleteCamp']) AND isset($_REQUEST['rid'])){
			function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID=0 WHERE  merchantid = "' . $merchantid .'" and id='.$_REQUEST['rid'],__FILE__);
			die();
		}
		
		////////////////////////////////////////////// END OF DELETE



		////////////////////////////////////////////// ADD NEW ACTION 



		if(isset($_REQUEST['newRel']) AND isset($_REQUEST['aff']) AND isset($_REQUEST['camp'])){
			function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID="'.$_REQUEST['aff'].'" WHERE campID="'.$_REQUEST['camp'].'"',__FILE__);
			$row = mysql_fetch_assoc(function_mysql_query('SELECT * FROM affiliates_campaigns_relations WHERE campID="'.$_REQUEST['camp'].'"',__FILE__));
			$affName = mysql_fetch_assoc(function_mysql_query('SELECT username FROM affiliates WHERE id='.$_REQUEST['aff'],__FILE__));
			echo $_REQUEST['aff'].'|'.$affName['username'].'|'.$_REQUEST['camp'].'|'.$row['id'].'|'.$row['affiliateID'].'|'.$row['campID'];
			die();
		}

		
		if(isset($_REQUEST['addNew'])){
			echo '<tr '.($l % 2 ? 'class="trLine"' : '').' rel="rid">
						<td class="affID" style="text-align:center; padding-top:8px; padding-bottom:8px">-</td>
						<td style="text-align:center" class="affs" rel="affID"><select name="affID" class="affSelect"><option id=-1>Select Affiliate</option>'.$affiliatesStr.'</select></td>
						<td style="text-align:center" class="camps" rel="campID"><select name="campID" class="campSelect"><option id=-1>Select Campaign</option>'.$campaignsStr.'</select>&nbsp;&nbsp;<input class="newSave" type="button" value="'.lang('Save').'"/></td>
						<td class="deleteCA"><span style="cursor:pointer; cursor:hand">Delete</span></td>
					</tr>
					
					<script type="text/javascript">

						$( document ).ready(function() {
							$(".newSave").click(function(){
								var td = this;
								var td2 = $(this).parent().parent();
								$.ajax({
									url: "http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&newRel=1&aff="+$(".affSelect").val()+"&camp="+$(".campSelect").val(),
									dataType: "HTML",
								}).done(function(response) {
									var id = $(td).parent().find(".campSelect").val();
									response = response.split("|");
									$(td).parent().html(response[2]);
									$(td).parent().attr("rel",response[5]);
									$(td).parent().parent().find(".affs").attr("rel",response[4]);
									$(td).parent().parent().attr("rel",response[3]);
									
									td2.find(".affs").attr("rel",response[4]);
									td2.find(".camps").attr("rel",response[5]);
									td2.attr("rel",response[3]);
									
									td2.find(".affs").html(response[1]);
									td2.find(".affID").html(response[0]);
									
									$(".addNew").slideDown();
									//getTotalFreeCamps();
									var currCampTotal = Number($("#totalFreeCamps").html());
									currCampTotal--;
									$("#totalFreeCamps").html(currCampTotal);
									
									return;
									$(td).parent().attr("rel",$(td).parent().find(".campSelect").val());
									$(td).parent().parent().attr("rel",response[1]);
									$(td).parent().html(response[0]);
									$(td).parent().html(response[0]);
									
								});
							});
						});
					</script>
					
					';
			$l++;
			die();
		}
		
		
		////////////////////////////////////////////// END OF ADD NEW




		////////////////////////////////////////////// FLOW
		
		$campID = mysql_fetch_assoc(function_mysql_query('SELECT extraMemberParamName AS title FROM merchants WHERE id='.aesDec($_COOKIE['mid']),__FILE__));
		
		$set->content .= '
		
			<div class="normalTableTitle" style="margin-top:30px">Affiliates Campaigns Relations <span style="font-size:14px"> - </span><span style="font-size:14px">'. lang('Total Free Campaigns').': <span id="totalFreeCamps"></span></span></div>
			<div style="font-size:12px; padding:5px">'.lang('Please click on affilaite Username OR Campaign ID to edit the values').'.</div>
			<table id="caTable" cellspacing=0 cellpadding=3 border=0 width="100%" class="normal" >
				<thead>
				<tr>
					<td style="width:100px">'.lang('Affilaite ID').'</td>
					<td style="width:300px">'.lang('Affilaite Username').'</td>
					<td style="width:300px">'.($campID['title'] ? $campID['title'] : lang('Campaign')).'</td>
					<td style="width:50px"></td>
				</tr>
				</thead>
				'.$relationsStr.'
				
			</table>
			<div class="addNew" style="padding:8px; background:LIGHTGREY; color:#000; text-align:center; font-weight:bold; text-decoration:underline; cursor:hand; cursor:pointer">'.lang('Add new').'</div></tfoot>
			
			<script type="text/javascript">
				$( document ).ready(function() {
				
					function getTotalFreeCamps(){
						$.ajax({
							url: "http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&getFreeCamps=1",
							dataType: "HTML",
						}).done(function(response) {
							$("#totalFreeCamps").html(response);
						});
					}

					function handleAffs(e){
						console.log($(this).attr("rel"));
						var td = this;
						$.ajax({
							url: "http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&ajaxAff=1&affID="+$(this).attr("rel")+"&rid="+$(this).parent().attr("rel"),
							dataType: "HTML",
						}).done(function(response) {
							if(response==""){
								alert("'.lang('Error occurred. Please contact support').'");
								return;
							}
							console.log(response);
							$(td).html(response);
							getTotalFreeCamps();
						});
					}

					function handleCamps(e){
						console.log($(this).attr("rel"));
						var td = this;
						$.ajax({
							url: "http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&ajaxCamp=1&campID="+$(this).attr("rel")+"&rid="+$(this).parent().attr("rel"),
							dataType: "HTML",
						}).done(function(response) {
							if(response==""){
								alert("'.lang('Error occurred. Please contact support').'");
								return;
							}
							console.log(response);
							$(td).html(response);
							getTotalFreeCamps();
						});
					}

					function deleteCA(e){
						if(!confirm("'.lang('Are you sure you want to delete this Campaign-Affiliate relation?').'")){
							return;
						}
						var td = this;
						
						$.ajax({
							url: "http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&deleteCamp=1&rid="+$(this).parent().attr("rel"),
							dataType: "HTML",
						}).done(function(response) {
							$(td).parent().slideUp("fast");
							getTotalFreeCamps();
						});
					}

					function addNew(e){
						$(".addNew").slideUp();
						$.ajax({
							url: "http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&addNew=1",
							dataType: "HTML",
						}).done(function(response) {
							console.log(response);
							$("#caTable tr:last").after(response);
							getTotalFreeCamps();
						});
					}


					$(".affs").on("dblclick",handleAffs);
					
					$(".camps").on("dblclick",handleCamps);
					
					$(".deleteCA").on("click",deleteCA);
					
					$(".addNew").on("click",addNew);
					
					
					getTotalFreeCamps();
					
				});
			</script>
				
			<!--
			<div class="normalTableTitle" style="margin-top:30px">Affiliates Campaigns Relations</div>
			<div style="background: #F8F8F8;">
				<form method="POST">
					<table cellspacing=10 cellpadding=0 border=0>
						<tr>
							<td>Affiliate:</td>
							<td><select name="affID"><option id=-1>Select Affiliate</option>'.$affiliatesStr.'</select></td>
						</tr>
						<tr>
							<td>Campaign:</td>
							<td><select name="campID"><option id=-1>Select Campaign</option>'.$campaignsStr.'</select></td>
						</tr>
						<tr>
							<td><input type="submit" value="Save"/></td>
						</tr>
					</table>
				</form>
			</div>-->';
			
			
		
		theme();
		break;
	}

?>