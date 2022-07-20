<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);
if (!adminPermissionCheck('maintenance')) _goto($lout);

		//$set->pageTitle = lang('Maintenance');
		$set->breadcrumb_title = lang('Maintenance');
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.'admin/maintenance.php" class="arrow-left">'.lang('Maintenance').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		
		$set->content .= '<style>		
			ul.vertical {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 20%;
			background-color: #f1f1f1;
			position:fixed;
			height:65vh;
			overflow:auto;
			min-width:80px;
			display:block;
		}

		ul.vertical li a {
			display: block;
			color: #000;
			padding: 8px 0 8px 16px;
			text-decoration: none;
		}

		ul.vertical li a:hover:not(.active) {
			background-color: #555;
			color:white;
		}

		ul.vertical a.active {
			background-color: #555;
			color:white;
		}	
		
		ul.vertical li.heading_active {
			padding: 8px 0 8px 5px;
			text-decoration: none;
			color:white;
			height: 32px;
		}
		ul.vertical  li input#config_filter
		{
			    width: 101px;
				/*margin-right: 5px;
				margin-left: 5px;
				margin-top: 10px;
				margin-bottom: 10px;*/
		}
		ul.vertical  li input#btnFilter
		{
			padding-left:0px !Important;
			padding-right:0px !important;
			width:100px !important;
		}
		.main {
			margin-left:20%;
			padding:1px 16px;
			height:100%;
		}
		
		.filter{
			padding-top:10px;
			padding-right:2px;
		}
		
		@media screen and (max-width: 290px) {
			.main{
			   margin-left:35%;
			}
		}
		</style>
		<script>
		$(document).ready(function(){
			$("ul.vertical li a").on("click",function(){
				var tabtoopen = $(this).data("tab");
				action = $("#"+tabtoopen+" form").attr("action");
				
				if(typeof action === "undefined"){
					action = window.location.href;
					
				}
				
				act = action.split(/[?#]/)[0];
				act = act + "?tab=" + tabtoopen;
				$("#" + tabtoopen+" form").attr("action", act);
				
				 $(this).toggleClass("active").parent().siblings().find("a").removeClass("active");
				show_hide_tabs(tabtoopen,"li");
			});
			
			
			function show_hide_tabs(open_tab,type){
				$(".config_tabs").hide();
				
				if(type=="search"){
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
						$(".config_tabs").hide();
						$(".config_tabs").each(function(k,tab){
							var search_txt = $(tab).find("div.normalTableTitle").text();
							
							search_txt = search_txt.toLowerCase();
							open_tab = open_tab.toLowerCase();
								console.log(search_txt + " -----" + open_tab);
							if(search_txt.search(open_tab)!==-1){
								$(this).show();
								txt = $(this).data("tab");
								$("ul.vertical li a").each(function(){
								
									if(txt == $(this).data("tab")){
										$(this).css("color","grey");
									}
								});
							}
							else{
								$(this).hide();
								txt = $(this).data("tab");
								$("ul.vertical li a").each(function(){
									if(txt == $(this).data("tab")){
										$(this).css("color","black");
									}
								});
							}
						});
					}
				}
				else{
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
					$(".config_tabs").each(function(k,tab){
						if($(tab).data("tab") == open_tab){
							$(this).show();
						}
						else{
						}
					});
					}
					
				}
			}

			$("#config_filter").on("keyup",function(){
				tabtoopen = $(this).val();
				if(tabtoopen == "")
				{
					$(".config_tabs").show();
					$("ul.vertical li a").css("color","black");
				}
				else{
				show_hide_tabs(tabtoopen,"search");
				}
			});
		});
		</script>
		<ul class="vertical">
  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">'. lang('Sections') .'</span><form onsubmit="return false;" style="display:inline-flex;float:left;">
  <div class="filter">'.lang('Find') .': </div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div></li>
  <li><a href="javascript:void(0)" data-tab="all">'. lang('All') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="missing_trader_handler">'. lang('Missing Trader Handler') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="test_removal_tool">'. lang('Test Removal Tool') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="switching_tests_into_demo_account">'. lang('Switch Tests Into Demo Account') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="trader_id_switcher">'. lang('Trader ID Switcher') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="ticket_ownership_charger">'. lang('Tickets Ownership Changer') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="commissionExplorer_tab">'. lang('Commission Explorer') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="qualificationReset_tab">'. lang('Qualification Reset') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="manageReportsFields_tab">'. lang('Manage Reports Fields') .'</a></li>
  </ul>
<div class="main">';
	if(isset($tab)){
		$set->content.="
			<script type='text/javascript'>
			$(document).ready(function(){
			var hash = '". $tab ."';
			 if(hash != '')
			 {
				$('a[data-tab=".$tab."]').click();
			 }
			});
		</script>";
}	
		$brandQ = function_mysql_query('SELECT id,LOWER(name) AS name FROM merchants WHERE valid=1 ORDER BY name ASC',__FILE__);
		$brandStr = '';
		$count=0;
		$brandStr.='<option value="-1">'.lang('Select Brand').'</option>';
		while($brandRow = mysql_fetch_assoc($brandQ)){
						//die($_POST['mt_traderID']);
						$brandStr.='<option '.(((isset($_POST['mt_brand']) && $_POST['mt_brand']==$brandRow['id']) OR mysql_num_rows($brandQ)==1) ? 'selected' : '').' value="'.$brandRow['id'].'">'.$brandRow['name'].'</option>';
						$count++;
		}
		
		$brandQ = function_mysql_query('SELECT id,LOWER(name) FROM merchants ORDER BY id ASC LIMIT 0,1',__FILE__);
		$brand = mysql_result($brandQ,0,1);
		$brandid = mysql_result($brandQ,0,0);
		/*
		$brand = mysql_result(function_mysql_query('SELECT id,LOWER(name) FROM merchants ORDER BY id ASC LIMIT 0,1',__FILE__),0,0);
		$brandid = $brand['id'];
		$brand = $brand['name'];
		*/
		$missingTrader='
		<div id="missing_trader_handler" data-tab="missing_trader_handler" class="config_tabs" style="margin-bottom:20px">
		<div class="normalTableTitle" data-tab2="missing_trader_handler">'.lang(ptitle('Missing Trader Handler')).'</div>
		<div style="text-align:left; margin-top:10px; margin-left:10px">
		<form method="POST"><select style="height:29px; margin-right:10px" name="mt_brand">'.$brandStr.'</select><input type="text" style="margin-right:10px" name="mt_traderID" value="'.(isset($_POST['mt_traderID']) && !isset($_POST['demoRemoval']) ? trim($_POST['mt_traderID']) : '').'"/><input type="submit" value="'.lang(ptitle('Search Trader')).'"/></form>';
		
		if(isset($_POST['mt_traderID']) && isset($_POST['mt_brand']) && $_POST['mt_brand']!=-1 && $_POST['mt_traderID'] && !isset($_POST['demoRemoval'])){
			
			$brand = $_POST['mt_brand'];
			$mthQ = 'SELECT ctag FROM data_reg WHERE merchant_id='.$brand.' AND trader_id="'.(trim($_POST['mt_traderID'])). '" ';
			// var_dump($_POST);
			
			// die ($mthQ);
			$row = mysql_fetch_assoc(function_mysql_query($mthQ,__FILE__));
			if(!$row['ctag']){
				$missingTrader.='<BR>'.lang('No trader was found width id').':"'.$_POST['mt_traderID'].'" on MerchantID: "'.$brand.'"';
			}else{
				$ctag = $row['ctag'];
				$orgCtag = $ctag;
				if(!isset($_POST['mt_affID'])){
					
					$ctag = explode('-',$ctag);
					$affID = substr($ctag[0],1);
					getDefaultAffiliateID();
					$missingTrader.='<div style="margin-top:10px"><form method="POST" id="frmMissingTrader">
					<input type="hidden" name="mt_brand" value="'.$brand.'"/>
					<input type="hidden" name="mt_traderID" value="'.(trim($_POST['mt_traderID'])).'"/>
					<span>'.lang('Current Affiliate ID').':</span><input type="text" style="margin-left:10px;margin-right:10px" name="mt_affID" id="mt_affID" value="'.$affID.'"/>
					<input type="submit" value="'.lang('Update Affiliate').'"/></form></div>
					<script>
					$(document).ready(function(){
						$("#frmMissingTrader").on("submit",function(){
							affId = $("#mt_affID").val();
							if(affId == "" || affId == 0){
								
								$.prompt("'. lang('Any trader has to be related to an affiliate, you may set ') . $set->defaultAffiliateID . lang(' as affiliate id ') . '", {
											top:200,
											title: "'. lang('Missing Trader Handler') .'",
											buttons: { "'.lang('Ok').'": true},
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
							else{
							return true;
							}
						});
					});
					</script>
					
					';
				
				}else{
					
					// if(!isset($_POST['mt_traderID']) OR $_POST['mt_traderID']=='' OR !is_numeric($_POST['mt_traderID'])){
					if(!isset($_POST['mt_traderID']) OR $_POST['mt_traderID']=='' ){
						$missingTrader.='Error occured #1172, '.lang('please contact Affiliate Buddies support team').'.';
					}
					if(!isset($_POST['mt_affID']) OR $_POST['mt_affID']=='' OR !is_numeric($_POST['mt_affID'])){
						$missingTrader.='Error occured #1131, '.lang('please contact Affiliate Buddies support team').'.';
					}
					$ctag = explode('-',$ctag);
					$affID = trim($_POST['mt_affID']);
					$affiliate = mysql_fetch_assoc(function_mysql_query("select * from affiliates where id= " . $affID,__FILE__));
					
					$ctag[0] = 'a'.intval($affID);
					$ctag = implode('-',$ctag);
					$missingTrader.=lang('Affiliate ID changed from').' ----> '.$orgCtag.' ------ '.lang('to').' ----> '.$ctag.'<BR><BR>';
					$countTraders = mysql_fetch_assoc(function_mysql_query('SELECT COUNT(id) AS c FROM data_reg WHERE merchant_id='.$brand.' AND  trader_id="'.($_POST['mt_traderID']). '" ',__FILE__));
					
					
					$q = 'SELECT producttype from merchants where id='.$brand;
					// die ($q);
					$producttype = mysql_fetch_assoc(function_mysql_query($q,__FILE__));
					$producttype = $producttype['producttype'];
					// echo $q .'<br><Br>';
					// die ('f:   ' . $countTraders['c']);
					if($countTraders['c']==1 || (strtolower($producttype)=='forex' && $countTraders['c']>0)){
						function_mysql_query('UPDATE data_reg SET ctag="'.$ctag.'", affiliate_id='.$affID.' , group_id = '.$affiliate['group_id'].' WHERE merchant_id = "'. $brand . '" and  trader_id="'.($_POST['mt_traderID']) .'" ' ,__FILE__). '" '; //OR die(mysql_error());
						//die('UPDATE data_reg SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE  merchant_id = "'. $brand . '" and  trader_id='.intval($_POST['mt_traderID']));
						function_mysql_query('UPDATE data_sales SET ctag="'.$ctag.'", affiliate_id='.$affID.', group_id = '.$affiliate['group_id'] .' WHERE merchant_id = "'. $brand . '" and trader_id="'.($_POST['mt_traderID']) .'" ' ,__FILE__). '" '; //OR die(mysql_error());
						function_mysql_query('UPDATE data_stats SET ctag="'.$ctag.'", affiliate_id='.$affID.', group_id = '.$affiliate['group_id'] .' WHERE merchant_id = "'. $brand . '" and trader_id="'.($_POST['mt_traderID']) .'" ' ,__FILE__). '" '; //OR die(mysql_error());
						function_mysql_query('UPDATE data_sales_pending SET ctag="'.$ctag.'", affiliate_id='.$affID.', group_id = '.$affiliate['group_id'] .' WHERE merchant_id = "'. $brand . '" and trader_id="'.($_POST['mt_traderID']) . '" ',__FILE__); //OR die(mysql_error());
						//function_mysql_query('UPDATE data_reg_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE affiliate_id=500 AND trader_id='.intval($_POST['mt_traderID']));
						//function_mysql_query('UPDATE data_sales_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE affiliate_id=500 AND trader_id='.intval($_POST['mt_traderID']));
						$missingTrader.='<div style="font-weight:bold; font-size:18px">'.lang('Done').'!</div>';
					}else{
						$missingTrader.='Error occured #2166, '.lang('please contact Affiliate Buddies support team').'. ';
					}
					// $missingTrader.='</div>';
				}
			}
		}else if(isset($_POST['mt_traderID']) && isset($_POST['mt_brand']) && !isset($_POST['demoRemoval'])){
			$missingTrader.='<p style="font-size:12px; color:RED">'.lang('Please select brand & insert a Trader ID').'.</p>';
		}
		
		
					
		$set->content.=$missingTrader.'</div></div>';
		
		
		
		
		// remove demo
		
		
			$demoRemovalBlock='
		<div   id="test_removal_tool" data-tab="test_removal_tool" class="config_tabs" style="margin-bottom:20px">
		<div class="normalTableTitle" data-tab2="test_removal_tool">'.lang(ptitle('Tests Removal Tool')).'</div>
		<div style="text-align:left; margin-top:10px; margin-left:10px">
		<form method="POST">
		<input type="hidden" name="demoRemoval" value="1"/>
		<select style="height:29px; margin-right:10px" name="mt_brand">'.$brandStr.'</select><input type="text" style="margin-right:10px" name="mt_traderID_removal" value="'.(isset($_POST['mt_traderID_removal']) ? trim($_POST['mt_traderID_removal']) : '').'"/>
		<input type="submit" onClick="return confirmationDelete()" value="'.lang(ptitle('Remove Trader Records')).'"/></form>
			<script>
						function confirmationDelete() {
							var msg = "'.lang('Are you sure you want to delete all trader\'s records?') .'\n'.lang('This action is irreversible'). ' !!!";
							return confirm(msg);
						}
    		</script>

		';
		
		
		if( isset($_POST['mt_traderID_removal']) && isset($_POST['mt_brand']) && $_POST['mt_brand']!=-1 && $_POST['mt_traderID_removal'] && $_POST['demoRemoval']==1){
			
			$brand = $_POST['mt_brand'];
			$qrr = 'SELECT id FROM data_reg WHERE merchant_id='.$brand.' AND trader_id="'.(trim($_POST['mt_traderID_removal'])). '" ' ;
			
			$row = mysql_fetch_assoc(function_mysql_query($qrr,__FILE__));
			if(!$row['id']){
				$demoRemovalBlock.='<BR>'.lang('No trader was found width id').':"'.$_POST['mt_traderID_removal'].'" on MerchantID: "'.$brand.'"';
			}else{
				
				$admin_id=($set->userInfo['id']);
				$qrr = 'SELECT * FROM data_reg WHERE merchant_id='.$brand.' AND trader_id='.(trim($_POST['mt_traderID_removal']));
				$rsc = function_mysql_query($qrr,__FILE__);
				while ($row = mysql_fetch_assoc($rsc )) {
					$fields = json_encode($row,true);
					
					$insQry = "INSERT INTO `data_recycle`(`recordRdate`,`trader_id`, `merchant_id`, `admin_id`, `data_table`, `fields`) VALUES ('".$row['rdate']."','".$row['trader_id']."','".$row['merchant_id']."','".$admin_id."','data_reg','".mysql_real_escape_string($fields)."')";
					function_mysql_query($insQry,__FILE__);
					$check = 'SELECT * FROM data_recycle WHERE data_table="data_reg" and merchant_id='.$row['merchant_id'].' AND trader_id="'.$row['trader_id'].'"';
					 // die ($check);
					$checkRow = mysql_fetch_assoc(function_mysql_query($check,__FILE__));
					if ($checkRow['id']){
						$deleteQry = "delete from data_reg where trader_id = '" . $checkRow['trader_id'] . "' and merchant_id = " . $checkRow['merchant_id'];
						function_mysql_query($deleteQry,__FILE__);
					}
				}
				
				$qrr = 'SELECT * FROM data_sales WHERE merchant_id='.$brand.' AND trader_id='.(trim($_POST['mt_traderID_removal']));
				$rsc = function_mysql_query($qrr,__FILE__);
				while ($row = mysql_fetch_assoc($rsc )) {
					$fields = json_encode($row,true);
					$insQry = "INSERT INTO `data_recycle`(`recordRdate`,`trader_id`, `tranz_id`, `merchant_id`, `admin_id`, `data_table`, `fields`) VALUES ('".$row['rdate']."','".$row['trader_id']."','".$row['tranz_id']."','".$row['merchant_id']."','".$admin_id."','data_sales','".mysql_real_escape_string($fields)."')";
					function_mysql_query($insQry,__FILE__);
					$check = 'SELECT * FROM data_recycle WHERE data_table="data_sales" and merchant_id='.$row['merchant_id'].' AND trader_id='.$row['trader_id'] .' AND tranz_id="'.$row['tranz_id'] .'"';
					
					// die ($check);
					$checkRow = mysql_fetch_assoc(function_mysql_query($check,__FILE__));
					if ($checkRow['id']){
						$deleteQry = "delete from data_sales where trader_id = '" . $checkRow['trader_id'] . "' and merchant_id = " . $checkRow['merchant_id'].' AND tranz_id="'.$checkRow['tranz_id'] .'"';
						function_mysql_query($deleteQry,__FILE__);
					}
				}
				
				$qrr = 'SELECT * FROM data_stats WHERE merchant_id='.$brand.' AND trader_id='.(trim($_POST['mt_traderID_removal']));
				$rsc = function_mysql_query($qrr,__FILE__);
				while ($row = mysql_fetch_assoc($rsc )) {
					$fields = json_encode(mysql_real_escape_string($row),true);
					$insQry = "INSERT INTO `data_recycle`(`recordRdate`,`trader_id`, `tranz_id`, `merchant_id`, `admin_id`, `data_table`, `fields`) VALUES ('".$row['rdate']."','".$row['trader_id']."','".$row['tranz_id']."','".$row['merchant_id']."','".$admin_id."','data_stats','".mysql_real_escape_string($fields)."')";
					function_mysql_query($insQry,__FILE__);
					$check = 'SELECT * FROM data_recycle WHERE data_table="data_stats" and merchant_id='.$row['merchant_id'].' AND trader_id='.$row['trader_id'] .' AND tranz_id="'.$checkRow['tranz_id'] .'"';
					
					$checkRow = mysql_fetch_assoc(function_mysql_query($check,__FILE__));
					if ($checkRow['id']){
						$deleteQry = "delete from data_stats where trader_id = '" . $checkRow['trader_id'] . "' and merchant_id = " . $checkRow['merchant_id'].' AND tranz_id="'.$checkRow['tranz_id'] .'"';
						function_mysql_query($deleteQry,__FILE__);
					}
				}
				
				if(!isset($_POST['mt_state'])){
					
					
					$demoRemovalBlock.='<div style="margin-top:10px">
					<!--form method="POST">
					<input type="hidden" name="mt_brand" value="'.$brand.'"/>
					<input type="hidden" name="mt_traderID_removal" value="'.(trim($_POST['mt_traderID_removal'])).'"/>
					<span>'.lang('Current State').':</span><select  style="margin-left:10px;margin-right:10px" name="mt_state"><option id="1">'.lang('Active').'</option><option id="0">'.lang('Hidden').'</option></select>
					<input type="submit" value="'.lang('Update State').'"/></form--></div>';

				}else{
					
					if(!isset($_POST['mt_traderID_removal']) OR $_POST['mt_traderID_removal']=='' OR !is_numeric($_POST['mt_traderID_removal'])){
						$demoRemovalBlock.='Error occured #1177, '.lang('please contact Affiliate Buddies support team').'.';
					}
					if(!isset($_POST['mt_state']) OR $_POST['mt_state']=='' OR !is_numeric($_POST['mt_state'])){
						$demoRemovalBlock.='Error occured #1137, '.lang('please contact Affiliate Buddies support team').'.';
					}
					
					$valid = trim($_POST['mt_state']);

				
					$demoRemovalBlock.=lang('State changed from').' ----> '.$orgValid.' ------ '.lang('to').' ----> '.$valid.'<BR><BR>';
					$countTraders = mysql_fetch_assoc(function_mysql_query('SELECT COUNT(id) AS c FROM data_reg WHERE merchant_id='.$brand.' AND  trader_id='.($_POST['mt_traderID_removal']),__FILE__));
					if($countTraders['c']==0){
						//////function_mysql_query('UPDATE data_reg SET ctag="'.$ctag.'", affiliate_id='.$valid.' , group_id = '.$affiliate['group_id'].' WHERE merchant_id = "'. $brand . '" and  trader_id='.intval($_POST['mt_traderID']),__FILE__); //OR die(mysql_error());
						//die('UPDATE data_reg SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE  merchant_id = "'. $brand . '" and  trader_id='.intval($_POST['mt_traderID']),__FILE__);
					/////////////	function_mysql_query('UPDATE data_sales SET ctag="'.$ctag.'", affiliate_id='.$valid.', group_id = '.$affiliate['group_id'] .' WHERE trader_id='.intval($_POST['mt_traderID']),__FILE__); //OR die(mysql_error());
					///////////////	function_mysql_query('UPDATE data_stats SET ctag="'.$ctag.'", affiliate_id='.$valid.', group_id = '.$affiliate['group_id'] .' WHERE trader_id='.intval($_POST['mt_traderID']),__FILE__); //OR die(mysql_error());
						//function_mysql_query('UPDATE data_reg_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE affiliate_id=500 AND trader_id='.intval($_POST['mt_traderID']),__FILE__);
						//function_mysql_query('UPDATE data_sales_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE affiliate_id=500 AND trader_id='.intval($_POST['mt_traderID']),__FILE__);
						$demoRemovalBlock.='<div style="font-weight:bold; font-size:18px">'.lang('Done').'!</div>';
					}else{
						$demoRemovalBlock.='Error occured #2167, '.lang('please contact Affiliate Buddies support team').'. ';
					}
					$demoRemovalBlock.='</div>';
				}
				
				
				
				$demoRemovalBlock.='<div style="font-weight:bold; font-size:18px">'.lang('Done').'!</div>';
				
			}
		}else if(isset($_POST['mt_traderID_removal']) && isset($_POST['mt_brand'])){
			$demoRemovalBlock.='<p style="font-size:12px; color:RED">'.lang('Please select brand & insert a Trader ID').'.</p>';
		}
		
		
					
		$set->content.=$demoRemovalBlock.'</div></div>';
		
		
		
		
		
		
		// switching into demo
		
		
			$switchingIntoDemo='
			<div id="switching_tests_into_demo_account" data-tab="switching_tests_into_demo_account" class="config_tabs" style="margin-bottom:20px">
		<div class="normalTableTitle" data-tab="switching_tests_into_demo_account">'.lang(ptitle('Switch Tests Into Demo Account')).'</div>
		<div style="text-align:left; margin-top:10px; margin-left:10px">
		<form method="POST">
		<input type="hidden" name="demoSwitching" value="1"/>
		<select style="height:29px; margin-right:10px" name="mt_brand">'.$brandStr.'</select><input type="text" style="margin-right:10px" name="mt_traderID_switch" value="'.(isset($_POST['mt_traderID_switch']) ? trim($_POST['mt_traderID_switch']) : '').'"/>
		<input type="submit" onClick="return confirmationSwitch()" value="'.lang(ptitle('Switch Trader Into Demo')).'"/></form>
			<script>
						function confirmationSwitch() {
							var msg = "'.lang('Are you sure you want to switch trader\'s type into demo?') .'\n'.lang('This action is irreversible'). ' !!!";
							return confirm(msg);
						}
    		</script>

		';
		
		
		if( isset($_POST['mt_traderID_switch']) && isset($_POST['mt_brand']) && $_POST['mt_brand']!=-1 && $_POST['mt_traderID_switch'] && $_POST['demoSwitching']==1){
			
			$brand = $_POST['mt_brand'];
			$qrr = 'SELECT id FROM data_reg WHERE merchant_id='.$brand.' AND trader_id='.(trim($_POST['mt_traderID_switch']));
			$row = mysql_fetch_assoc(function_mysql_query($qrr,__FILE__));
			if(!$row['id']){
				$switchingIntoDemo.='<BR>'.lang('No trader was found width id').':"'.$_POST['mt_traderID_switch'].'" on MerchantID: "'.$brand.'"';
			}else{
				
				$admin_id=($set->userInfo['id']);
				$qrr = 'update data_reg set type = "demo"  WHERE merchant_id='.$brand.' AND trader_id="'.(trim($_POST['mt_traderID_switch'])) . '" ';
				$rsc = function_mysql_query($qrr,__FILE__);
				
				$switchingIntoDemo.='<div style="font-weight:bold; font-size:18px">'.lang('Done').'!</div>';
				}
				
				
			}
		else if(isset($_POST['mt_traderID_switch']) && isset($_POST['mt_brand'])){
			$switchingIntoDemo.='<p style="font-size:12px; color:RED">'.lang('Please select brand & insert a Trader ID').'.</p>';
		}
		
		
					
		$set->content.=$switchingIntoDemo.'</div></div>';
		
		
		
		
	// TRADER ID CHANGER
	
	$missingTrader='
	<div id="trader_id_switcher" data-tab="trader_id_switcher" class="config_tabs" style="margin-bottom:20px">
		<div class="normalTableTitle" data-tab2="trader_id_switcher">'.lang(ptitle('Trader ID Switcher')).'</div>
		<div>'.lang('This feature helps Admins to replace trader id to another based on crm requierments') .'</div><br>
		<div style="color:red;font-weight:bold;">'.lang('THIS ACTION CANNOT BE UNDONE') .'</div><br>
		<div style="text-align:left; margin-top:10px; margin-left:10px">
		<form method="POST"><select style="height:29px; margin-right:10px" name="mt_brand">'.$brandStr.'</select><input type="text" style="margin-right:10px" name="existingTraderID" value="'.(isset($_POST['existingTraderID']) ? trim($_POST['existingTraderID']) : '').'"/><input type="submit" value="'.lang(ptitle('Search Trader')).'"/></form>';
		
		if(isset($_POST['existingTraderID']) && isset($_POST['mt_brand']) && $_POST['mt_brand']!=-1 && $_POST['existingTraderID']){
			if (!chkSecure($code) && isset($_POST['newTraderID'])) {
					$error =  lang('Please type the capcha currently');
					$missingTrader.='<div>' . $error . '</div>';
			}
			else {
			$brand = $_POST['mt_brand'];
			$row = mysql_fetch_assoc(function_mysql_query('SELECT trader_id FROM data_reg WHERE merchant_id='.$brand.' AND trader_id="'.(trim($_POST['existingTraderID'])) . '"',__FILE__));
			if(!$row['trader_id']){
				$missingTrader.='<BR>'.lang('No trader was found width id').':"'.$_POST['existingTraderID'].'" on MerchantID: "'.$brand.'"';
			}else{
				$existingTraderID = $row['trader_id'];
				
				if(!isset($_POST['newTraderID'])){
					
					
					
					$missingTrader.='<div style="margin-top:10px"><form method="POST">
					<input type="hidden" name="mt_brand" value="'.$brand.'"/>
					<input type="hidden" name="existingTraderID" value="'.(trim($_POST['existingTraderID'])).'"/>
					<br>
					<div style="padding-top: 5px;">'.secureCode() . '</div>
					
					<script>
						function confirmation() {
							
							
							var msg = "'.lang('Are you sure you want to change trader id from one to another?') .'\n'.lang('This action is irreversible'). ' !!!";
							return confirm(msg);
						}
					</script>
					<br>
					<span>'.lang('Current Trader ID').':</span><input type="text" style="margin-left:10px;margin-right:10px" name="newTraderID" value="'.$existingTraderID.'"/>
					<input type="submit" value="'.lang('Update Trader ID').'" onClick="return confirmation()"/>
					<br>
					</form></div>
					
					';
				
				}else{
					
					if(!isset($existingTraderID) OR $existingTraderID=='' OR !is_numeric(str_replace('-','',$existingTraderID))){
						// die ('gfergerger:  ' . $existingTraderID);
						$missingTrader.='Error occured #1172, '.lang('please contact Affiliate Buddies support team').'.';
					}
					if(!isset($newTraderID) OR $newTraderID=='' OR !is_numeric(str_replace('-','',$newTraderID))){
						$missingTrader.='Error occured #1131, '.lang('please contact Affiliate Buddies support team').'.';
					}
				
					$missingTrader.=lang('Trader ID changed from').' ----> '.$existingTraderID.' ------ '.lang('to').' ----> '.$newTraderID.'<BR><BR>';
					// $countTraders = mysql_fetch_assoc(function_mysql_query('SELECT COUNT(id) AS c FROM data_reg WHERE merchant_id='.$brand.' AND  trader_id="'.$existingTraderID.'"',__FILE__));
					// if($countTraders['c']==1)
					if (true)
					{
						// function_mysql_query('UPDATE data_reg SET ctag="'.$ctag.'", affiliate_id='.$affID.' , group_id = '.$affiliate['group_id'].' WHERE merchant_id = "'. $brand . '" and  trader_id='.intval($_POST['mt_traderID']),__FILE__); //OR die(mysql_error());
						//die('UPDATE data_reg SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE  merchant_id = "'. $brand . '" and  trader_id='.intval($_POST['mt_traderID']));
						$regQry = 'UPDATE data_reg SET trader_id="'.$newTraderID.'" ' .' WHERE trader_id="'.$existingTraderID. '"';
						// die($regQry);
						function_mysql_query($regQry,__FILE__); //OR die(mysql_error());
						$regQry = 'UPDATE data_sales SET trader_id="'.$newTraderID.'" ' .' WHERE trader_id="'.$existingTraderID. '"';
						// die($regQry);
						function_mysql_query($regQry,__FILE__); //OR die(mysql_error());
						$regQry = 'UPDATE data_stats SET trader_id="'.$newTraderID.'" ' .' WHERE trader_id="'.$existingTraderID. '"';
						// die($regQry);
						function_mysql_query($regQry,__FILE__); //OR die(mysql_error());
						// function_mysql_query('UPDATE data_sales SET trader_id="'.$newTraderID.'" ' .' WHERE trader_id='.$existingTraderID,__FILE__); //OR die(mysql_error());
						// function_mysql_query('UPDATE data_stats SET trader_id="'.$newTraderID.'" ' .' WHERE trader_id='.$existingTraderID,__FILE__); //OR die(mysql_error());
						//function_mysql_query('UPDATE data_reg_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE affiliate_id=500 AND trader_id='.intval($_POST['mt_traderID']),__FILE__);
						//function_mysql_query('UPDATE data_sales_'.$brand.' SET ctag="'.$ctag.'", affiliate_id='.$affID.' WHERE affiliate_id=500 AND trader_id='.intval($_POST['mt_traderID']),__FILE__);
						$missingTrader.='<div style="font-weight:bold; font-size:18px">'.lang('Done').'!</div>';
					}else{
						$missingTrader.='Error occured #2167, '.lang('please contact Affiliate Buddies support team').'. ';
					}
					$missingTrader.='</div>';
				}
			}
			}
		}else if(isset($existingTraderID) && isset($_POST['mt_brand'])){
			$missingTrader.='<p style="font-size:12px; color:RED">'.lang('Please select brand & insert a Trader ID').'.</p>';
		}
		
		
					
		$set->content.=$missingTrader.'</div></div>';
		
		
		
		
		
		
		///////////////////////////////////////////////////////////////////////////////////////////////// Tickets: 
		
		
		$ticketsWhere = 'affiliates_tickets.ticket_id=0 AND affiliates_tickets.status="open"';
		$ticketsError = '';
		$ticketsOK = '';
		$fromGroup = -1;
		$toGroup = -1;
		if(isset($_POST['fromGroup']) AND isset($_POST['toGroup']) AND $_POST['fromGroup']>-1 AND $_POST['toGroup']>-1 AND $_POST['fromGroup']!=$_POST['toGroup']){
			
			//logs:
			$rowsToChangeQ = function_mysql_query('SELECT affiliates_tickets.id FROM affiliates_tickets INNER JOIN (SELECT id FROM affiliates_tickets WHERE ticket_id=0 AND group_id='.$_POST['fromGroup'].' AND status="open")t1 ON t1.id=affiliates_tickets.id WHERE status="open" AND group_id='.$_POST['fromGroup'],__FILE__);
			$logsSet = '';
			while($rowToChange = mysql_fetch_assoc($rowsToChangeQ)){
				$logsSet.='("Ticket update","Updating ticket '.$rowToChange['id'].' from group='.$_POST['fromGroup'].' to group='.$_POST['toGroup'].' by adminID '.$_SESSION['session_id'].'","'.$rowToChange['id'].'","'.$_POST['fromGroup'].'","'.$_POST['toGroup'].'"),';
			}
			function_mysql_query('INSERT INTO logs (title,description,var1,var2,var3) VALUES '.rtrim($logsSet,','),__FILE__);
			

			//update tickets:
			function_mysql_query('UPDATE affiliates_tickets INNER JOIN (SELECT id FROM affiliates_tickets WHERE ticket_id=0 AND group_id='.$_POST['fromGroup'].' AND status="open")t1 ON t1.id=affiliates_tickets.id SET group_id='.$_POST['toGroup'].' WHERE status="open" AND group_id='.$_POST['fromGroup'],__FILE__);
			//approve message:
			$ticketsOK = mysql_affected_rows().' '.lang('Tickets & comments were updated').'!';
		}else if(isset($_POST['fromGroup']) AND isset($_POST['toGroup'])){
			$ticketsError = 'Please select valid groups.';
			$fromGroup = $_POST['fromGroup'];
			$toGroup = $_POST['toGroup'];
		}
		
		
		
		$groupsQ = function_mysql_query('SELECT 

			groups.id, 
			groups.title, 
			admins.username AS currentAM, 
			admins.id AS adminID, 
			COUNT(affiliates_tickets.id) AS ticketsPerGroup,
			COUNT(DISTINCT affiliates_tickets.affiliate_id) AS affiliatesCount

			FROM 
			groups
			LEFT JOIN admins ON groups.id=admins.group_id
			LEFT JOIN affiliates_tickets ON groups.id=affiliates_tickets.group_id

			WHERE '.$ticketsWhere.'

			GROUP BY groups.id

			HAVING COUNT(affiliates_tickets.id)>0'
		,__FILE__
		);
		
		
		$groupsStr = '';
		$general = mysql_fetch_assoc(function_mysql_query('SELECT COUNT(id) AS total FROM affiliates_tickets WHERE group_id=0 AND '.$ticketsWhere,__FILE__));
		$groupsStr.='<option value=-1>'.lang('Select Group').'</option>';
		if($general['total']>0){
			$groupsStr.='<option value=0 '.($fromGroup==0 ? 'selected' : '').'>General ('.$general['total'].' tickets)</option>';
		}
		while($row=mysql_fetch_assoc($groupsQ)){
			$groupsStr.='<option value='.$row['id'].' '.($fromGroup==$row['id'] ? 'selected' : '').'>'.$row['title'].' ('.$row['ticketsPerGroup'].' tickets)</option>';
		}
		
		
		
		$allGroupsQ = function_mysql_query('SELECT * FROM groups WHERE valid=1',__FILE__);
		
		$allGroupsStr = '';
		$allGroupsStr.='<option value=-1>'.lang('Select Group').'</option>';
		$allGroupsStr.='<option value=0 '.($toGroup==0 ? 'selected' : '').'>'.lang('General').'</option>';
		while($row = mysql_fetch_assoc($allGroupsQ)){
			$allGroupsStr.='<option value='.$row['id'].' '.($toGroup==$row['id'] ? 'selected' : '').'>'.$row['title'].'</option>';
		}
		
		$TicketsOC='
			<div id="ticket_ownership_charger" data-tab="ticket_ownership_charger" class="config_tabs" style="margin-bottom:20px">
			<div data-tab2="ticket_ownership_charger"class="normalTableTitle">'.lang('Tickets Ownership Changer').'</div>
			<div style="text-align:left; margin-top:10px; margin-left:10px">
				<form method="POST" onSubmit="if(document.getElementById(\'fromGroup\').selectedIndex>0 && document.getElementById(\'toGroup\').selectedIndex>0){ return true; } alert(\'Please select Groups to continue\'); return false;">
					<div style="color:RED; font-weight:bold">'.$ticketsError.'</div>
					<table cellspacing=5 cellpadding=0 border=0>
						<tr><td>Select tickets group: </td><td><select id="fromGroup" style="height:29px; margin-right:10px; width:250px" name="fromGroup">'.$groupsStr.'</select></td></tr>
						<tr><td>Convert the ticket\'s group to: </td><td><select id="toGroup" style="height:29px; margin-right:10px; width:250px" name="toGroup">'.$allGroupsStr.'</select></td></tr>
						<tr><td colspan=2><input type="submit" value="Change Tickets Ownership"/></td></tr>
					</table>
				</form>
				<div style="color:BLUE; font-weight:bold">'.$ticketsOK.'</div>
			</div></div>';
			
		//$set->content .= '</div>';
		
		$set->content.=$TicketsOC;
	
	
	
		$comExError=$comex_response="";
		
	if (isset($_GET['comex_merchant_id']) && isset($_GET['comex_trader_id']) && isset($_GET['comex_event_date'])){
		$arrDealTypeDefaults = getMerchantDealTypeDefaults();
		$arrRow['merchant_id'] = $_GET['comex_merchant_id'];
		$arrRow['trader_id'] = $_GET['comex_trader_id'];
		$arrRow['affiliate_id'] = $_GET['comex_affiliate_id'];
		$eventDatePost = trim($_GET['comex_event_date']);
		$arrRow['rdate'] = $eventDatePost;
		
		if (empty ($arrRow['affiliate_id'])){
		$qqq = "select * from data_reg where merchant_id = " . $arrRow['merchant_id'] . " and trader_id = '" . $arrRow['trader_id'] . "' limit 1;";
		
		$traderRow = mysql_fetch_assoc(mysql_query($qqq));
		
		// $arrRow['affiliate_id'] = $traderRow['affiliate_id'];
		}
		
		$deal =  extractDealTypes($arrRow, $arrDealTypeDefaults);
		
			$foundDealYet = false;
			foreach($deal as $child=>$value) {
		
		
				
				if (!empty($value['amount'])){
					$foundDealYet = true;
					$comex_response .= '<span stlyle="font-weight:bold;color:black;">'.$child. ":</span> " .$value['amount']. "<br>";
				}
				// echo ': ' . $foundDealYet.'<br>';
			}
				if (!$traderRow['id'] && !empty ($arrRow['affiliate_id']))
					$comExError .= '<span stlyle="font-weight:bold;color:black;">'.lang('No Such Trader In That Merchant In That Date').'<br>';
				else if (!$foundDealYet)
						$comExError .= '<span stlyle="font-weight:bold;color:black;">'.lang('Commission not found').'<br>';
	}
	
	
		$commissionExplorer = '
					<div id="commissionExplorer_tab" data-tab="commissionExplorer_tab" class="config_tabs" style="margin-bottom:20px">
			<div data-tab2="commissionExplorer_tab"class="normalTableTitle">'.lang('Commission Explorer').'</div>
			<div style="text-align:left; margin-top:10px; margin-left:10px">
				<form method="GET" >
					<div style="color:RED; font-weight:bold">'.$comExError.'</div>
					<table cellspacing=5 cellpadding=0 border=0>
						<tr><td>'.lang('Merchant ID').'</td><td>
						<input type="text" name="comex_merchant_id" value="'.intval(trim($_GET['comex_merchant_id'])).'"/>
						</td></tr>
						
						<tr><td>'.lang('Trader ID').'</td><td>
						<input type="text" name="comex_trader_id" value="'.(trim($_GET['comex_trader_id'])).'"/>
						</td>  
						<td>'.lang('Affiliate ID').'</td><td>
						<input type="text" name="comex_affiliate_id" value="'.intval(trim($_GET['comex_affiliate_id'])).'"/>
						</td></tr>
						
						<tr><td>'.lang('Event Date').' (YYYY-MM-DD)</td><td>
						<input type="text" name="comex_event_date" value="'.($eventDatePost).'"/>
						</td></tr>
						<tr><td colspan=2><input type="submit" value="Get Relevant Deal"/></td></tr>
					</table>
				</form>
				<div style="color:BLUE; font-weight:bold">'.$comex_response.'</div>
			</div>
			
			</div>';
			
			// var_dump($_POST);
// die();

		$set->content.=$commissionExplorer;
		$affiliateqq = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
		$allAffiliates = '<option selected value="">'.lang('Choose Affiliate').'</option>';
		while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {		   
				$allAffiliates .= '<option value="'.$affiliateww['id'].'">['.$affiliateww['id'].'] '
								  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
		}
		/* $from = strTodate($from);
		$to   = strTodate($to);

		$from = sanitizeDate($from);
		$to   = sanitizeDate($to);
		
		$from = createValidDateWorkAround($from);
		$to   = createValidDateWorkAround($to); */
		
		$from = "";
		$to = "";
	
	
		$qualificationExplorer = '
					<div id="qualificationReset_tab" data-tab="qualificationReset_tab" class="config_tabs" style="margin-bottom:20px">
			<div data-tab2="qualificationReset_tab"class="normalTableTitle">'.lang('Qualification Reset').'</div>
			<div style="text-align:left; margin-top:10px; margin-left:10px">
			<div class="message" style="color:BLUE;font-weight:bold"></div>
				<form method="GET" id="frmQualificationReset">
					<table cellspacing=5 cellpadding=0 border=0>
						<tr><td>'.lang('Brand').'</td><td>
						<select style="width:210px" name="merchant_id" id="QR_merchant_id">'.$brandStr.'</select>
						</td></tr>
						<tr><td>'.lang('Affiliate').'</td><td>
						 <div class="ui-widget" style="text-align:left;">
								<!-- name="affiliate_id" -->
								<select id="combobox">
								<!--option value="">'.lang('Choose Affiliate').'</option-->'
								. $allAffiliates
								.'</select>
								</div>
								<input type="hidden" id="QR_affiliate_id" name="QR_affiliate_id">
						</td></tr>
						
						<tr><td>'.lang('Trader ID').'</td><td>
						<input type="text" name="QR_trader_id" id="QR_trader_id"/>
						</td> 
						<td width=80><b>'.lang('OR').'</b></td><td>
						<b>'.lang('From').':</b> 
						<input type="text" name="from" id="date_from" value="" style="padding: 3px;" style="width:90px!important;" /> 
						<b>'.lang('To').':</b> 
						<input type="text" name="to" value="" id="date_to" style="padding: 3px;" style="width:90px!important;" />
						</td></tr>
						
						<tr><td colspan=2><input type="button" class="btnCheckRecords" onclick="getDataFromDataReg(\'getCount\')" value="'. lang('Continue') .'"/></td></tr>
					</table>
				</form>
				<br/>
				<div style="color:BLUE; font-weight:bold;display:none;font-size:14px;" class="recsToUpdate"><span></span>'. lang(" record(s) are going to affect.") .'<p>
				<div class="radios_div" style="color:black; font-weight:normal;display:none;font-size:12px;">
				<input type="radio" name="date_type" value="automatic" checked>'. lang('Automatically – Let the system update the qualification date') .' <br/><br/>
				<input type="radio" name="date_type" value="manual">'. lang('Set qualification date manually') .'
				</div>
				<p class="user_defined_dt" style="display:none">
				<input type="text" name="date_change" value="" id="date_change" style="padding: 3px;" style="width:90px!important;" />
				</p>
				<p class="resetQualification">
				<input type="button"  onclick="getDataFromDataReg(\'updateRecs\')" value="'. lang('Reset Qualification Date') .'"/></p>
				<p>
				</div>
			</div>
			
			</div>
			<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
			  <!-- jQuery UI Autocomplete css -->
				<style>
				.custom-combobox {
					position: relative;
					display: inline-block;
				  }
				  .custom-combobox-toggle {
					position: absolute;
					top: 0;
					bottom: 0;
					margin-left: -1px;
					background: white;
					border-radius: inherit;
					border-color: #CECECE;
					border-left: 0;
					color: #1F0000;
				  } 
				  .custom-combobox-input {
					margin: 0;
					padding: 5px 10px;
					width: 174px;
					background: white;
					border-radius: inherit;
					border-color: #CECECE;
					color: #1F0000;
					font-weight: inherit;
					font-size: inherit;
				  }
				  .ui-autocomplete { 
					height: 200px; 
					width:  310px;
					overflow-y: scroll; 
					overflow-x: hidden;
				  }
				   /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  /*width: 60px;*/
			  width: 48px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 20px;
			 width: 43px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 12px;
			  width: 12px;
			  left: 3px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
		
				</style>
				<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
				<!--link href="'.$set->SSLprefix.'css/redmond/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" /-->
			<link href="'.$set->SSLprefix.'css/jquery-ui-timepicker-addon.css" media="screen" rel="stylesheet" type="text/css" />
			<script type="text/javascript" src="'.$set->SSLprefix.'js/jquery-ui-timepicker-addon.js"></script>
			<script>
				$(document).ready(function(){
					
					$("input:radio[name=\'date_type\']").on("change",function(){
						if($(this).is(":checked")){
							if($(this).val() == "automatic"){
								$(".user_defined_dt").hide();
							}
							else{
								$(".user_defined_dt").show();
							}
						}
					});
					
					$("#date_from").val(convertYmdToDmy($("#date_from").val()));
				
					$("#date_to").val(convertYmdToDmy($("#date_to").val()));
					
					$("#date_change").datetimepicker({
							timeFormat: "HH:mm:ss",
							dateFormat:"yy-mm-dd"
						});
					
					$("#date_from").datepicker({
						dateFormat: \'yy/mm/dd\',
						onSelect: function() {
							$("#dateSelect").val(8);
						}
					});
					
					$("#date_to").datepicker({
						dateFormat: \'yy/mm/dd\',
						onSelect: function() {
							$("#dateSelect").val(8);
						}
					});
					$("#date_to,#date_from").keypress(function (e) {
						$("#dateSelect").val(8);
					});
					
					$("#date_from").datepicker("setDate","");
					$("#date_to").datepicker("setDate","");
					
				
					
				});
					function getDataFromDataReg(type){
						
						
						var intCurrentMerchant = $("#QR_merchant_id").val();
						var intCurrentAffiliate = $("input[name=\'affiliate_id\']").val();
						var intTraderId = $("#QR_trader_id").val();
						var dateFrom = $("#date_from").val();
						var dateTo = $("#date_to").val();
						var dateChange = $("#date_change").val();
						var dateType = $("input:radio[name=\'date_type\']:checked").val();
						
						if(type == "getCount"){
								
							if(intCurrentMerchant < 0 ){
								$(".message").html ("'. lang('Please select brand.') .'").css("color","red");
								return false;
							}
							
							if(intCurrentAffiliate == ""){
								$(".message").html ("'. lang('Please select affiliate.') .'").css("color","red");
								return false;
							}
							
							if(intTraderId == "" && (dateFrom == "" && dateTo == "") ){
								$(".message").html ("'. lang('Please enter trader id or select date range.') .'").css("color","red");
								return false;
							}
								
						}
						
						$(".message").hide();
						//$(".btnCheckRecords").on("click",function(){
						
						
						var strAjaxAddr = "' . $_SERVER['SERVER_HOST'] . '/ajax/getQualifiedRecordsToUpdate.php?merchant_id=" 
						+ intCurrentMerchant 
						+ "&affiliate_id=" 
						+ intCurrentAffiliate + "&trader_id=" + intTraderId + "&date_from=" + dateFrom + "&date_to=" + dateTo  + "&type=" + type +"&date_change="+ dateChange
						+ "&dataType=" + dateType;
					
						 $.get(strAjaxAddr, function(res) {
							try {
								if(res === "true"){
									//$(".message").html("'. lang('Records updated.') .'");
										$.prompt("'. lang('All records updated.') .'", {
											top:200,
											title: "'. lang('Qualification Reset') .'",
											buttons: { "'.lang('Ok').'": true},
											submit: function(e,v,m,f){
												if(v){
													//
													$(".recsToUpdate").hide();
													$(".recsToUpdate span").html("0");
													$(".recsToUpdate .resetQualification").hide();
													$(".recsToUpdate #date_change").hide();
													$(".radios_div").hide();
													$("#frmQualificationReset input[name=\'affiliate_id\'],#frmQualificationReset input[id=\'QR_trader_id\'],#frmQualificationReset input[id=\'date_from\'],#frmQualificationReset input[id=\'date_to\'], #frmQualificationReset select").val("");
													$("#date_change").val("");
												}
												else{
													//
												}
											}
										});
								}
								else{
									
									if(res != 0)
									{
										$(".recsToUpdate").show();
										$(".recsToUpdate span").html(res);
										$(".recsToUpdate #date_change").show();
										$(".recsToUpdate .resetQualification").show();
										$(".radios_div").show();
									}
									else{
										$(".recsToUpdate").show();
										$(".recsToUpdate span").html(res);
										$(".resetQualification").hide();
										$(".recsToUpdate #date_change").hide();
										$(".radios_div").hide();
									}
								}
							} catch (error) {
								console.log(error);
							}
						});
						
					//});
					}
				function convertYmdToDmy(strDate) {
					var arrDate = strDate.split("-");
					
					if (2 === arrDate[0].length) {
						// Current format is "d-m-Y".
						return strDate;
					} else {
						// Current format is "Y-m-d".
						return arrDate[2] + "/" + arrDate[1] + "/" + arrDate[0];
					}
				}
				function convertToDmy(strDate) {
					var arrDate = strDate.split("-");
					
					if (2 === arrDate[0].length) {
						// Current format is "d-m-Y".
						return arrDate[0] + "/" + arrDate[1] + "/" + arrDate[2];
					} else {
						// Current format is "Y-m-d".
						return arrDate[2] + "/" + arrDate[1] + "/" + arrDate[0];
					}
				}
				</script>
			';
		
		$set->content.=$qualificationExplorer;
		
		$sql = "select * from admins where level = 'admin' and valid = 1";
		$admins = function_mysql_query($sql);
		$listAdmins .= "<option value = ''>". lang('Select') ."</option>";
		while ($wwA = mysql_fetch_assoc($admins)){
			$listAdmins .= "<option value = " . $wwA['id'] . ">". $wwA['username'] ."</option>";
		}
		
		$sql = "select * from admins where level = 'manager' and valid = 1";
		$managers = function_mysql_query($sql);
		$listManagers .= "<option value = ''>". lang('Select') ."</option>";
		while ($wwM = mysql_fetch_assoc($managers)){
			$listManagers .= "<option value = " . $wwM['id'] . ">". $wwM['username'] ."</option>";
		}
		
		$manageReportsFields= '
			<div id="manageReportsFields_tab" data-tab="manageReportsFields_tab" class="config_tabs" style="margin-bottom:20px">
			<div data-tab2="manageReportsFields_tab"class="normalTableTitle">'.lang('Manage Reports Fields').'</div>
			<div style="text-align:left; margin-top:10px; margin-left:10px">
			<form method="POST" class="frmManageReports">
					<div class="rf_error" style="color:RED; font-weight:bold"></div>
					<table cellspacing=5 cellpadding=0 border=0>
						<tr><td>'.lang('Select User Level').': </td><td><select id="userLevel" style="height:29px; margin-right:10px; width:250px" name="userLevel">
						<option value="">'. lang('Choose') .'</option>
						<option value="admin">'. lang('Admin') .'</option>
						<option value="manager">'. lang('Manager') .'</option>
						<option value="affiliate">'. lang('Affiliate') .'</option>
						<option value="all">'. lang('All') .'</option>
						</select></td></tr>
						
						<tr style="display:none" class="affiliate_choose"><td>'. lang("Select Affiliate") .': </td><td>
						<select name="rf_affiliates"><option value = "">'. lang('Select') .'</option>'. listAffiliates() .'</select>
						</td></tr>
						<tr style="display:none" class="manager_choose"><td>'. lang("Select Manager") .': </td><td>
						<select name="rf_managers">'. $listManagers .'</select>
						</td></tr>
						<tr style="display:none" class="admin_choose"><td>'. lang("Select Admin") .': </td><td>
						<select name="rf_admins">'. $listAdmins .'</select>
						</td></tr>
						
						<tr><td colspan=2><input id="resetBtn" type="button" value="'. lang('Reset') .'"/></td></tr>
					</table>
				</form>
			</div>
			</div>
			<script type="text/javascript">
			$("#userLevel").on("change",function(){
				
				if($(this).val() == "affiliate"){
					$(".affiliate_choose").show();
					$(".manager_choose").hide();
					$(".admin_choose").hide();
				}
				
				if($(this).val() == "admin"){
					$(".affiliate_choose").hide();
					$(".manager_choose").hide();
					$(".admin_choose").show();
				}
				
				if($(this).val() == "manager"){
					$(".affiliate_choose").hide();
					$(".manager_choose").show();
					$(".admin_choose").hide();
				}
				
				if($(this).val() == "all"){
					$(".affiliate_choose").hide();
					$(".manager_choose").hide();
					$(".admin_choose").hide();
					
					$("[name=rf_admins]").val("");
					$("[name=rf_affiliates]").val("");
					$("[name=rf_managers]").val("");
					
				}
				
			});
			
			
			$("#resetBtn").on("click",function(){
					var level = $("#userLevel").val();
					
					if(level == ""){
						$(".rf_error").html("'. lang("Select User Level.") .'");
						return false;
					}
					
					var id = "";
					if(level=="admin"){
						id = $("[name=rf_admins]").val();
					}
					if(level=="manager"){
						id = $("[name=rf_managers]").val();
					}
					
					if(level=="affiliate"){
						id = $("[name=rf_affiliates]").val();
					}
					
					if(level != "" && level !="all" && id!=""){
						$(".rf_error").html("");
					}
					else{
						$(".rf_error").html("'. lang("Select ") .'" + level + " '. lang("id") .'.");
						return false;
						
					}
					
					$.prompt("'. lang('Would you like to reset the displaying fields set for your selection?') . '" , {
											top:200,
											title: "'. lang('Manage Reports Fields') .'",
											buttons: { "'.lang('Yes').'": true,"'.lang('Cancel').'": false},
											submit: function(e,v,m,f){
												if(v){
													
													
													
													$.get("'.$set->SSLprefix.'ajax/UpdateReportsFields.php?userLevel=" + level + "&id=" + id, function(res) {
															try {
																if(res === 1){
																	$(".rf_error").html("'. lang("Successfully reset the reports fields.") .'");
																}
																else{
																	$(".rf_error").html("'. lang("Error in resetting data reports fields.") .'");
																}
															} catch (error) {
																console.log(error);
															}
														});
												}
												else{
													//
												}
											}
									});
				});
			</script>
			';
		
		$set->content .= $manageReportsFields;
		
		$set->content .= '</div>';
		theme();
		break;
	

?>