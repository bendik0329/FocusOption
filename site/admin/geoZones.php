<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);
if ($set->deal_geoLocation==0 ) _goto($lout);
if (!adminPermissionCheck('geoZones')) _goto($lout);

$affiliate_id = isset ($_GET['affiliate_id']) ? $_GET['affiliate_id'] : 0 ;

switch ($act) {
	
	
	case "delete_country_in_zone":
			$id = $profile_id;
			$sql = "delete from geoTargetingZonesRelation where id=" . $id;
			function_mysql_query($sql ,__FILE__);
			_goto($set->SSLprefix.$set->basepage);
			break;
			
	case "delete_zone":
			$id = $profile_id;
			$sql = "delete from geoTargetingZones where id=" . $id;
			function_mysql_query($sql ,__FILE__);
			_goto($set->SSLprefix.$set->basepage);
			break;
			
	
	case "update":
	
			 $affiliate_id = retrieveAffiliateId($_POST['affiliate_id']);
			 $merchant_id = ($_POST['merchant_id']);
	
			$sql = "select * from geoTargetingZones where id = " . $id . " limit 1";
			$geoTargetingZones = mysql_fetch_assoc(function_mysql_query($sql ,__FILE__));
			$inputarray = [];
			$inputarray['name'] = $geoZone_name;
			$inputarray['defaultViewForDealType'] = $defaultView;
			$inputarray['affiliate_id'] = $affiliate_id;
			if(!empty($checkedReports))
			$inputarray['reportsPermissions'] = $checkedReports;
			if(!empty($checkedFields))
			$inputarray['fieldsPermissions'] = $checkedFields;
			
			foreach($inputarray as $field=>$value)
			{
				if(!is_numeric($value) )
					$values .= "$field = '$value',";
				else
					$values .= "$field = $value,";
			}
			$values = substr($values,0,strLen($values)-1);
			$sql = "UPDATE geoTargetingZones SET " . $values .  " WHERE id = " . $id;
			function_mysql_query($sql ,__FILE__);
			_goto($set->SSLprefix.$set->basepage.'?id='.$id . '&admin_id=' . $geoTargetingZones['created_by_admin_id']);
			break;
	
	case "add":
			
			$affiliate_id = retrieveAffiliateId($affiliate_id);
			$affiliate_id = is_numeric($affiliate_id)?$affiliate_id:'';
			
			$inputarray = [];
			$inputarray['name'] = $geoZone_name;
			$inputarray['defaultViewForDealType'] = $defaultView;
			$inputarray['affiliate_id'] = $selectAffiliate;
			$inputarray['reportsPermissions'] = $checkedReports;
			$inputarray['fieldsPermissions'] = $checkedFields;
			$inputarray['created_by_admin_id'] = $set->userInfo['id'];
			$fields = implode(',',array_keys($inputarray));
			$values = implode(',', array_map(function($value) {
				if(!is_numeric($value)) {
					return '"' . $value . '"';
				} else {
					return $value;
				}
			}, array_values($inputarray)));
		
			$sql = "INSERT into geoTargetingZones ($fields) values ($values)";
			function_mysql_query($sql ,__FILE__);
			_goto($set->SSLprefix.$set->basepage);
			break;
	
	case "duplicate":
			$id = $profile_id;
			
			$sql = "select * from geoTargetingZones where id = $id";
			$result = function_mysql_query($sql ,__FILE__);
			
			$duplicateRow = mysql_fetch_assoc($result);
			
			unset($duplicateRow['id']);
			unset($duplicateRow['rdate']);
			$duplicateRow['created_by_admin_id'] = $set->userInfo['id'];
			$duplicateRow['defaultViewForDealType'] = "";
			$fields = implode(',',array_keys($duplicateRow));
			$values = implode(',', array_map(function($value) {
				if(!is_numeric($value)) {
					return '"' . $value . '"';
				} else {
					return $value;
				}
			}, array_values($duplicateRow)));
			
			$sql = "INSERT into geoTargetingZones ($fields) values ($values)";
			function_mysql_query($sql ,__FILE__);
			_goto($set->SSLprefix.$set->basepage);
			
			break;
	case "valid":
			$id = $profile_id;
			
			$sql = "update geoTargetingZones  set valid = $valid where id = $id";
			
			$result = function_mysql_query($sql ,__FILE__);
			
			_goto($set->SSLprefix.$set->basepage);
			
			break;
	default:
			$pageTitle = lang('GEO Targeting Zones');
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			//All Deal Types
			$allDealTypes  = '<option selected value="">'.lang('Choose Deal Type').'</option>';
			$allDealTypes .= '<option  value='. lang('CPA') .'>'.lang('CPA').'</option>';
			$allDealTypes .= '<option  value='. lang('DCPA') .'>'.lang('DCPA').'</option>';
			$allDealTypes .= '<option  value='. lang('CPL') .'>'.lang('CPL').'</option>';
			$allDealTypes .= '<option  value='. lang('REV') .'>'.lang('REV').'</option>';
			
			
			$set->content = '
			
			<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
			<script>
			$(document).ready(function(){
				var addbtn = "'. lang("Add").'";
				var proSettingtxt = "'. lang('GEO Zone Settings') .'"
				
				
				$("form").submit(function(e){
					if($("#defaultView").val()==""){
						$(".error").html("This field is required.")
						$("#defaultView").focus();
						return false;
					}
					if($("#permission_name").val()==""){
						$(".error").html("This field is required.")
						$("#permission_name").focus();
						return false;
					}
				})
				
				$(".addrow").on("click",function(e){
					e.preventDefault();
					$(".error").html("");
					$("#act").val("add");
					$("#profile_id").val("");
					//$("#selectAffiliate").val("").combobox("refresh");
					$("#selectAffiliate").val("");
					$("#permission_name").val("");
					$("#defaultView").val("");
					$("input[type=checkbox]:checked").removeAttr("checked");
					$("#btnSubmit").val(addbtn);
					$("#profile_form").show();
					$("#defaultView ").append("<option value=\'None\'>'.lang('None').'</option>");
				});';
				if(isset($_GET['id']) && !empty($_GET['id'])){
					$set->content .= '
					$(document).ready(function(){
						loadFormData('. $_GET['id'] .','. $_GET['admin_id'] .');
					});';
				}
				$set->content .= 'function loadFormData(id,createdbyadminid){
					
					if(id===0){
						$("#defaultView ").find("option").remove().end().append("<option value=\'General\' selected>'.lang('General').'</option>");
					}
					else{
						if(createdbyadminid > 0){
							$("#defaultView ").find("option").remove().end().append(\''. $allDealTypes .'\');
							$("#defaultView ").append("<option value=\'None\'>'.lang('None').'</option>");
						}
						else{
							$("#defaultView ").find("option").remove().end().append(\''. $allDealTypes .'\');
						}
					}
					
				
					$("#profile_form").show();
					$.get("'.$set->SSLprefix.'ajax/loadProfilesPermissionsByAffiliateId.php", { profile_id: id}, function(res) {
						res = JSON.parse(res);
						if (typeof res["failed"] == "undefined") {
								$("#profile_settings").html(proSettingtxt + " - " + res.id + " " + res.name);
								//$( "#selectAffiliate" ).combobox();
								$("#selectAffiliate").val(res.affiliate_id)
								
								 $("#combobox").combobox("autocomplete", res.affiliate_id);
								
								
								$("#permission_name").val(res.name);
								$("#defaultView").val(res.defaultViewForDealType);
								$("#profile_form").show();
								
								var fields = res.fieldsPermissions;
								var reports = res.reportsPermissions;
								
								if(fields != ""){
									
									var arrfields = fields.split("|");
									$.each(arrfields,function(k,v){
										if(v!=""){												
												if(v.indexOf("-")===0){
													$("input[value=\'" + v+ "\']").prop("checked", false);
												}
												else{
													$("input[value=\'" + v+ "\']").prop("checked", true);
												}
										}
									});
									
								}
								
								if(reports != ""){
									
									var arrreports = reports.split("|");
									$.each(arrreports,function(k,v){
										if(v!=""){													
											if(v.indexOf("-")===0){
												console.log(v);
												$("input[value=\'" + v+ "\']").prop("checked", false);
											}
											else{
												$("input[value=\'" + v+ "\']").prop("checked", true);
											}
										}
									});
									
								}
								
								
						}
						else{
							alert("Request Failed");
						}
				});
				}
				
				$(".editrow").click(function(e){
					e.preventDefault();
					$(".error").html("");
					$("input[type=checkbox]:checked").removeAttr("checked");
					$("#act").val("update");
					var id = $(this).data("id");
					$("#profile_id").val(id);
					$("#createdByAdminId").val($(this).data("adminid"));
					
					
					loadFormData(id,$(this).data("adminid"));
					
					
				});
				$(".duplicaterow").click(function(e){
					e.preventDefault();
					$("#act").val("duplicate");
					$("#profile_form").show();
				});
				
				$(".deleterow").click(function(){
					var id = $(this).data("id");
					if (confirm("Are you sure you want to delete?")) {
						window.location.href="'. $set->SSLprefix.$set->basepage.'?act=delete&profile_id="+id;
					}
				})
				
				$(".reports").click(function(){
					var checkedReports = $(".reports:checkbox").map(function() {
						if($(this).is(":checked"))
							return this.value;
						else
							return "-" + this.value;
					}).get();
					$("#checkedReports").val(checkedReports.join("|"));
				});
				
			
				$(".fields").click(function(){
					var checkedFields = $(".fields:checkbox").map(function() {
						if($(this).is(":checked"))
							return this.value;
						else
							return "-" + this.value;
					}).get();
					$("#checkedFields").val(checkedFields.join("|"));
				});
				
			});
				
			</script>
			 <!-- jQuery UI Autocomplete css -->
			<style>
			.error{
				color:red;
			}
			.editrow,.duplicaterow,.deleterow{
				cursor:pointer;
			}
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
			width: 120px;
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
			  width: 60px;
			  height: 34px;
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
			  height: 25px;
			 width: 50px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 16px;
			  width: 16px;
			  left: 4px;
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
			';
			
			$i=1;
			
			$affiliates = getAllAffiliatesForPermissions();
			$permissionsqq=function_mysql_query("SELECT * FROM geoTargetingZones",__FILE__);
			
			
			while($permissionsww=mysql_fetch_assoc($permissionsqq)) {
				$affiliate = $permissionsww['affiliate_id']==0?' - ':$affiliates[$permissionsww['affiliate_id']];
				$valid = $permissionsww['valid']==1?0:1;
				$hrefValid = $set->SSLprefix.$set->basepage.'?act=valid&profile_id='.$permissionsww['id'].'&valid='. $valid;
				$deletebtn = $permissionsww['created_by_admin_id']>0?'<a class="deleterow" data-id='. $permissionsww['id'] .'>'. lang('Delete') .'</a>&nbsp;|&nbsp;':'';
				$listPermissions .= '<tr '.($i % 2 ? 'class="trLine"' : '') .'id="'. $permissionsww['id'] .'">
								<td align="center">'.$permissionsww['id'].'</td>
								<td align="center">'.$permissionsww['defaultViewForDealType'].'</td>
								<td align="center">'.$permissionsww['name'].'</select></td>
								<td align="center">'.$permissionsww['rdate'].'</td>
								<td align="center"><a href="'.($permissionsww['affiliate_id']>0 ?  '/admin/affiliates.php?act=new&id='.$permissionsww['affiliate_id'].'">' : 'javascript:void(0);">').$affiliate.'</a></td>';
								if($permissionsww['id']==0)
										$listPermissions .='<td align="center"></td>';
								else
										$listPermissions .='<td align="center" id="lng_'.$permissionsww['id'].'"><a href="'.  $hrefValid .'" style="cursor: pointer;">'.xvPic($permissionsww['valid']).'</a>';
								
								$listPermissions .='<td align="center"><a class="editrow" data-id='. $permissionsww['id'] .' data-adminid= "'. $permissionsww['created_by_admin_id'] .'">'. lang('Edit') .'</a>&nbsp;|&nbsp;'. $deletebtn .'<a href="'. $set->SSLprefix.$set->basepage .'?act=duplicate&profile_id='. $permissionsww['id'] .'" data-id='. $permissionsww['id'] .'>'. lang('Duplicate') .'</a></td>
							</tr>';
				$i++;
				}


					$set->content .= '<span><div class="btn"><a class="addrow">'.lang('Add New GEO Zone').'</a></div>&nbsp;&nbsp;&nbsp;
					
					 </span>
					<div class="normalTableTitle">'.lang('Zones').'</div>
						<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
							<thead><tr>
								<th>ID</th>
								<th align="center">'.lang('Default View For Deal Type').'</th>
								<th align="center">'.lang('Name').'</th>
								<th align="center">'.lang('Date').'</th>
								<th align="center">'.lang('Affiliate').'</th>
								<th align="center">'.lang('Valid').'</th>
									<th align="center">'.lang('Actions').'</th>
							</tr></thead><tbody>'.$listPermissions.'
						</tbody></table>
					</div><br/><br/>';
					
					
					// All Affiliates.
					$affiliateqq = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
					$allAffiliates = '<option selected value="0">'.lang('To All Afiliates').'</option>';
					while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {		   
							$allAffiliates .= '<option value="'.$affiliateww['id'].'">['.$affiliateww['id'].'] '
											  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
					}
					
					
					
					//Report Permissions
					$reportPermissions = "";
					$reportsqq = function_mysql_query("SELECT * FROM permissionsDescription WHERE type='reports' and valid=1 ",__FILE__);
					$fieldsArray = array();
					while($reportsww = mysql_fetch_assoc($reportsqq)){
					
					if (!isset($fieldsArray[$reportsww['key']]))
						$fieldsArray[$reportsww['key']] = $reportsww['key'];
					else
						continue;
					
					
							$reportPermissions .= "<div>";
							$reportPermissions .= '<div style="float:left"><label class="switch"><input type="checkbox" name="reports" class="reports" value="'. $reportsww['key'] .'"><div class="slider round"></div></label></div>';
							$reportPermissions .= "<div style='display:none;float:left;padding-top:5px;min-width:100px'>". $reportsww['key']  ."</div>";
							$reportPermissions .= "<div style='padding-top:5px;width:auto'>". lang($reportsww['description']) ."</div>";
							$reportPermissions .= "</div><div style='clear:both'></div>";
					}
					
					unset ($fieldsArray);
					
					//Field Permissions
					$fieldsPermissions = "";
					$fieldsqq = function_mysql_query("SELECT * FROM permissionsDescription WHERE type='fields' and valid=1 ",__FILE__);
					$fieldsArray = array();
					while($fieldsww = mysql_fetch_assoc($fieldsqq)){
					
					if (!isset($fieldsArray[$fieldsww['key']]))
						$fieldsArray[$fieldsww['key']] = $fieldsww['key'];
					else
						continue;
					
							$fieldsPermissions .= "<div>";
							$fieldsPermissions .= '<div style="float:left"><label class="switch"><input type="checkbox" class="fields" value="'. $fieldsww['key'] .'"><div class="slider round"></div></label></div>';
							$fieldsPermissions .= "<div style='display:none;float:left;padding-top:5px;min-width:100px'>". $fieldsww['key']  ."</div>";
							$fieldsPermissions .= "<div style='float:left;padding-top:5px;width:auto'>". lang($fieldsww['description']) ."</div>";
							$fieldsPermissions .= "</div><div style='clear:both'></div>";
					}
					
					$set->content .= '<div id="profile_form" style="display:none;"><form method="post">
					<input type="hidden" name="act" id="act" value="update" />
					<input type="hidden" id="profile_id" name="profile_id" value="">
					<input type="hidden" id="createdByAdminId" name="createdByAdminId" value="">
					<div class="normalTableTitle" id="profile_settings">'.lang('Profile Settings').'</div>
					<div align="left" style="background: #EFEFEF;">
								<table border="0" cellpadding="0" cellspacing="5">
									<tr>
												<td width="150" align="left">'.lang('Select Affiliate').':</td>
												<td>
												<!--div class="ui-widget"-->
												<!--select id="selectAffiliate" name="selectAffiliate">'. lang('Choose Affiliate'). $allAffiliates.'</select>
											</div-->
											<div class="ui-widget">'
												. '<!-- name="affiliate_id" -->'
												. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
												. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
												. $allAffiliates
												.'</select>
												</div>
											</td>
									</tr><tr>
									<td style="padding-top:10px"></td>
									</tr><tr>
										<td width="150" align="left">'.lang('Name').':</td>
										<td><input type="text" name="permission_name" id="permission_name" value="'.$set->permission_name.'" style="width: 250px;" />
										<span class="error"></td>
									</tr><tr>
									<td style="padding-top:10px"></td>
									</tr><tr>
												<td width="150" align="left">'.lang('Default view for deal type').':</td>
												<td>
												<select id="defaultView" name="defaultView">'.$allDealTypes.
												'</select><br/><span class="error"></span></td>
									</tr><tr>
									<td style="padding-top:10px"></td>
									</tr><tr>
									<td valign="top">'.lang('Report Permissions').':</td>
									<td>'. $reportPermissions .'</td>
									<input type="hidden" name="checkedReports" id="checkedReports">
									</tr><tr>
									<td style="padding-top:10px"></td>
									</tr><tr>
										<td valign="top" width="150" align="left">'.lang('Fields Permissions').':</td>
										<td>'. $fieldsPermissions .'</td>
										<input type="hidden" name="checkedFields" id="checkedFields">
									</tr>
									<tr>
									<td><input type="submit" name="Update" id="btnSubmit" value="'.lang("Update") .'"/> </td>
									</tr>
								</table>
						</form></div>'; 
					
		theme();
		break;
	}

?>