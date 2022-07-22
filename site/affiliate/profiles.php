<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);

$appTable = 'affiliates_profiles';

switch ($act) {
	case "save_profile";
		$db['rdate'] = dbDate();
		


		if (isset($id)) {
			$db['id'] = $id;
			}
			else
			$db[valid] = 1;
		
		

		$db['affiliate_id'] = $set->userInfo['id'];
		dbAdd($db,$appTable);
		_goto($set->SSLprefix.$set->basepage.'?ty=1');
		break;

	
	

			
		
	default:
		$pageTitle = lang('Website Profiles');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		// List Profiles
		$profileqq=function_mysql_query("SELECT * FROM ".$appTable." WHERE affiliate_id='".$set->userInfo['id']."' ORDER BY id DESC",__FILE__);
		while ($profileww=mysql_fetch_assoc($profileqq)) {
			$l++;
			$listProfiles .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$profileww['id'].'</td>
							<td>'.$profileww['name'].'</td>
							<td>'.$profileww['url'].'</td>
							<td>'.$profileww['description'].'</td>
							<td>'.$profileww['source_traffic'].'</td>
							<td id="profile_'.$profileww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=profile_valid&id='.$profileww['id'].'\',\'profile_'.$profileww['id'].'\');" style="cursor: pointer;">'.xvPic($profileww['valid']).'</a></td>
							<td align="left" style="font-weight:bold;text-align:center;"><a data-name="'.$profileww['name'].'" data-source_traffic="'.$profileww['source_traffic'].'" data-description="'.$profileww['description'].'" data-url="'.$profileww['url'].'" data-id="'.$profileww['id'].'" href="/affiliate/profiles.php?act=save_profile&id='.$profileww['id'].'">'.lang('Edit').'</a></td>
							<!--td>'.xvPic($profileww['valid']).'</td-->
							
						</tr>';
			}
		
		$set->content .= '<div class="normalTableTitle">'.lang('').'</div>
						<div style="width: 100%; background: #F8F8F8;">
						<form action="'.$set->SSLprefix.$set->basepage.'" method="post">
						<input type="hidden" name="act" value="save_profile" />
						<input type="hidden" name="db[affiliate_id]" value="'.$id.'" />
							<div align="left" style="padding: 10px 0;">
								<table><tr>
								'.($ty ? '<tr><td colspan="7" align="left" style="color: green; font-size: 14px;"><img border="0" width="30" src="images/alert/alert_v.png" alt="" align="absmiddle" /> '.lang('Your website profile has added successfully').'</td></tr><tr height="30"></td></tr>' : '').'
								<td align="left" width="100" class="blueText profile-form-label">'.lang('Profile Name').': <input class="profile-input" id="db_name" type="text" name="db[name]" value="" style="width: 250px;" /></td>
							
								<td align="left" class="blueText profile-form-label ">'.lang('URL').':<input class="profile-input" id="db_url" type="text" name="db[url]" value="http://" style="width: 250px;" /></td>
								
				
							
								<td align="left" class="blueText profile-form-label ">'.lang('Description').':<input class="profile-input" id="db_description" type="text" name="db[description]" value="" style="width: 250px;" /></td>
								
								
								<td align="left" class="blueText profile-form-label ">'.lang('Traffic Source').':<input class="profile-input" id="db_source_traffic" type="text" name="db[source_traffic]" value="" style="width: 250px;" /></td>
								
								
								<td align="right" class="save-button-p"><input class="save-button-profile" type="submit" value="'.lang('Save').'" /></td>
							</tr></table>
							</form>
							</div>
							</div>
							

						<div class="billing-page-table">
							<div class="row">
								<div class="col-md-12">
									<div class="top-performing-creative h-full">
										<div class="performing-creative-table profile-t">
											<div class="table-responsive">
											<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
											<thead><tr>
												<th>#</th>
												<th style="text-align: center;">'.lang('Profile Name').'</th>
												<th>'.lang('URL').'</th>
												<th>'.lang('Description').'</th>
												<th>'.lang('Traffic Source').'</th>
												<th>'.lang('Available').'</th>
												<th style="text-align:center;">'.lang('Action').'</th>
											</tr></thead><tfoot>'.$listProfiles.'</tfoot>
										</table>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>



							
								<script>
						$("[data-id]").click(function() {
							$("#db_id").val($(this).data("id"));
							$("#db_name").val($(this).data("name"));
							$("#db_source_traffic").val($(this).data("source_traffic"));
							$("#db_description").val($(this).data("description"));
							$("#db_url").val($(this).data("url"));
							$("#id_hidden").val($(this).data("id"));
							return false;
						});

						
					


						</script>';
						
						
		
		theme();
		break;
		
		
		case "profile_valid":
		$appProfiles = 'affiliates_profiles';
		$db=dbGet($id,$appProfiles);
		
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appProfiles,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=profile_valid&id='.$db['id'].'\',\'profile_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
		
		/*
				$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		*/
		
	}

?>