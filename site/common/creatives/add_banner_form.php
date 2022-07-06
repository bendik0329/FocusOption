<?php

for ($i = 1; $i <= $max_upload; $i++) {  
                        // '.fileField('file1_'.$i,'').'    '.fileField('file4_'.$i,'').'     '.fileField('file5_'.$i,'').'  
			$l++;
			$listCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$i.'</td>
								<td style="text-align: left;"><input type="text" name="name1_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td align="center"><input type="file" multiple="true" name="file1_' . $i . '[]" /></td>
								<td><input type="text" name="alt1_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
							</tr>';
			if ($i <= "5") $listCreative2 .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$i.'</td>
								<td style="text-align: left;"><input type="text" name="name2_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td><input type="text" name="alt2_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
							</tr>';
			if ($i <= "1") $listCreative3 = '<tr class="trLine">
								<td style="text-align: left;"><input type="text" name="name3_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td><input type="text" name="iframe3_url_'.$i.'" value="http://" id="fieldClear" style="width: 280px;" /></td>
								<td>Width x Height<br /><input type="text" name="iframe3_width_'.$i.'" value="" id="fieldClear" style="width: 30px; text-align: center;" /> x <input type="text" name="iframe3_height_'.$i.'" value="" id="fieldClear" style="width: 30px; text-align: center;" /></td>
							</tr>';
			if ($i <= "1") $listCreative4 = '<tr class="trLine">
								<td style="text-align: left;"><input type="text" name="name4_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td align="center"><input type="file" name="file4_' . $i . '" /></td>
								<td><input type="text" name="alt4_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
							</tr>';
			if ($i <= "1") $listCreative5 = '<tr class="trLine">
								<td style="text-align: left;"><input type="text" name="name5_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td align="center"><input type="file" name="file5_' . $i . '" /></td>
								<td><input type="text" name="alt5_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
							</tr>';
                }
		
	
$set->content .= '<form method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="form_type" value="1" id="form_type" />
						'.($mainBannerType == "products"?'<input type="hidden" name="product_id" value="'.$id.'" />':'<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />').'
                           
						'.($mainBannerType == "products"?
						'<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Add New Crative Material for').': <b>'.$product_name.'</b></div>':'
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Add New Crative Material for').': <b>'.listMerchants($merchant_id,1,0,1).'</b></div>').'
						<div id="tab_3" ' .($isNew==1 ? 'style="display:block;">' : 'style="display:none;">' ). 
						'<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>
								<td align="left" width="100" class="blueText">'.lang('Language').':</td>
								<td align="left"><select name="creative_lang" style="width: 292px;">'.listLangs().'</select></td>
								<td width="80"></td>
								<td align="left" class="blueText">'.lang('Promotion').':</td>
								<td align="left"><select name="creative_promotion" style="width: 100px;"><option value="">'.lang('General').'</option>'.listPromotions(0,$merchant_id,"","",1).'</select></td>
								
								<td width="80"></td>
								<td align="left" class="blueText">'.lang('Default Creative Name').':</td>
								<td align="left"><input type="text" name="creative_name" value="" style="width: 280px;" /></td>
								<td width="80"></td>
								<td align="left" class="blueText">'.lang('Category').':</td>
								<td align="left"><select name="category_id" style="width: 120px;"><option value="">'.lang('All').'</option>'.listCategory($category_id,$merchant_id).'</select></td>
							</tr><tr>
								<td align="left" class="blueText">'.lang('Landing URL').':</td>
								<td align="left">
								<input type="text" id="creative_url" name="creative_url" value="http'.$set->SSLswitch.'://" style="width: 280px;" /'.
								(isset($errors['creative_url']) ? '<span style="color:RED">*</span>' : '').'</td>
								
								<td></td>
								<td align="left" class="blueText">'.lang('Status').':</td>
								<td align="left">
									<select name="creative_status" style="width: 100px;">
										<option value="1">'.lang('Active').'</option>
										<option value="0">'.lang('Inactive').'</option>
									</select>
								</td>
								<td></td>
								<td align="left" class="blueText">'.lang('ALT').':</td>
								<td align="left"><input type="text" name="creative_alt" value="" style="width: 280px;" /></td>
							</tr></table>
						</div>
						<div style="padding-bottom:10px" class="material_type_label blueText">'. lang('Choose Creative Material Type:') .'</div>
						<div class="tabs banner_type_tabs">
							<div class="tab" rel="1" name="banner_image_flash">'.lang('Banner Image / Flash').'</div>
							<div class="tab" rel="2" name="text_link">'.lang('Text Link').'</div>
							'.($set->creative_iframe ? '<div class="tab" rel="3" name="iframe_widget">'.lang('IFrame (Widget)').'</div>' : '').'
							'.($set->creative_mobile_leader ? '<div class="tab" rel="4" name="mobile_leader_board">'.lang('Mobile LeaderBoard').'</div>' : '').'
							'.($set->creative_mobile_splash ? '<div class="tab" rel="5" name="mobile_splash">'.lang('Mobile Splash').'</div>' : '').'
							'.($set->creative_email ? '<div class="tab" rel="6">'.lang('E-mail').'</div>' : '').'
						<div class="tab" rel="7">'.lang('Content').'</div>
						</div>
						<div class="tab_open" id="tab_1">
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td width="50">#</td>
									<td style="text-align: left;">'.lang('Banner Name').'</td>
									<td align="center">'.lang('Upload File').'</td>
									<td align="center">'.lang('ALT').'</td>
								</tr></thead><tfoot>'.$listCreative.'</tfoot>
							</table>
						</div>
						<div class="tab_open" id="tab_2">
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td width="50">#</td>
									<td width="200" style="text-align: left;">'.lang('Link Name').'</td>
									<td align="center">'.lang('ALT').'</td>
								</tr></thead><tfoot>'.$listCreative2.'</tfoot>
							</table>
						</div>
						'.($set->creative_iframe ? '<div class="tab_open" id="tab_3">
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td width="200" style="text-align: left;">'.lang('IFrame / Widget Name').'</td>
									<td align="center">'.lang('Widget URL').'</td>
									<td align="center">'.lang('Widget Size').'</td>
								</tr></thead><tfoot>'.$listCreative3.'</tfoot>
							</table>
						</div>' : '').'
						'.($set->creative_mobile_leader ? '<div class="tab_open" id="tab_4">
							<div class="comment">
								'.lang('Mobile Leaderboard Banner should be on').' <b>320x50</b>
							</div>
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td style="text-align: left;">'.lang('Banner Name').'</td>
									<td align="center">'.lang('Upload File').'</td>
									<td align="center">'.lang('ALT').'</td>
								</tr></thead><tfoot>'.$listCreative4.'</tfoot>
							</table>
						</div>' : '').'
						'.($set->creative_mobile_splash ? '<div class="tab_open" id="tab_5">
							<div class="comment">
								'.lang('Mobile Splash Banner should be').' <b>350x350</b>
							</div>
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td style="text-align: left;">'.lang('Banner Name').'</td>
									<td align="center">'.lang('Upload File').'</td>
									<td align="center">'.lang('ALT').'</td>
								</tr></thead><tfoot>'.$listCreative5.'</tfoot>
							</table>
						</div>' : '').'
						'.($set->creative_email ? '<div class="tab_open" id="tab_6">
							<textarea name="scriptCode6" rows="20" placeholder="'.lang('Paste the HTML code here').'..." style="width: 99%;"></textarea>
							<p></p>
							<span>'.lang('Create tracking url by adding') . ' <u>'.'http'.$set->SSLswitch.'://'.$_SERVER[HTTP_HOST] . '/click.php?ctag={ctag}&subid={subid}</u> ' . lang('in the html code').'.</span>
							<p></p>
						</div>' : '').'
						<div class="tab_open" id="tab_7">
							<textarea name="scriptCode7" rows="20" placeholder="'.lang('Paste the HTML content code here').'..." style="width: 99%;"></textarea>
							<p></p>
							<span>'.lang('Create tracking url by adding') . ' <u>'.'http'.$set->SSLswitch.'://'.$_SERVER[HTTP_HOST] . '/click.php?ctag={ctag}&subid={subid}</u> ' . lang('in the html code').'.</span>
							<p></p>
						</div>
						<script type="text/javascript">
							$("div.tab").on("click", function() {
								$(this).parent().find("div.tab.selected").attr("class", "tab");
								$(this).attr("class", "tab selected");
								var getID = $(this).attr("rel");
								$("div.tab_open").slideUp();
								$("div#tab_"+getID).slideDown();
								$("#form_type").val(getID);
								});
						$(document).ready(function(){
							$("#creative_url").blur(function() {
						  var input = $(this);
						  var val = input.val();
						  if (val && !val.match(/^http([s]?):\/\/.*/)) {
							input.val("http://" + val);
						  }
						});
						});
						
						</script>
						<div style="clear:both;padding: 10px;"><input id="submit_creative_material" type="submit" value="'.lang('Save').'"/></div>
						</div>
						</form>
						
						<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
						<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
						<script>
						
					
							/** 
							 * @return bool
							 */
							function creativeDefaultValuesExist() {
								return $("[name=creative_name]").val().trim().length != 0;
							}
							
							/**
							 * @return bool
							 */
							function setDefaultsCreative() {
								if (!$("[name=creative_url]").val()) {
									alert("' . lang('Please enter the Landing URL') . '");
									return false;
								} else if ("http://" == $("[name=creative_url]").val().trim()) {
									//alert("' . lang('Please enter the Landing URL') . '");
									$.prompt("'.lang('Please enter the Landing URL').'", {
										top:200,
										title: "'.lang('Creative Material').'",
										buttons: { "'.lang('OK').'": true}
									});
									return false;
								}
								
								var strSelectedTabName = $(".banner_type_tabs>div.selected").text();
								if(strSelectedTabName == ""){
									$(".material_type_label").css("color","red");
									$.prompt("'.lang('Please select creative material type.').'", {
										top:200,
										title: "'.lang('Creative Material').'",
										buttons: { "'.lang('OK').'": true}
									});
									return false;
								}
								if ("' . lang('Banner Image / Flash') . '" == strSelectedTabName) {
									/**
								     * "Banner Image / Flash" validation.
								     */
									//if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
												if("" == $("[name=\'file1_" + i + "[]\']").val()){
														isValid = false;
												}
												else{
														isValid = true;
												}
												break;
												/* if (0 != $("[name=name1_" + i + "]").val().trim().length) { 
														isValid = true;
													break;
												} */
										}
										
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
												$.prompt("'.lang('New creatives type banner are requierd uploading images.<br/> Please click \"choose files\" or change creative type').'", {
													top:200,
													title: "'.lang('Creative Material').'",
													buttons: { "'.lang('OK').'": true}
												}); 
											}
											return false;
										}
									//}
								}
								else if ("' . lang('Text Link') . '" == strSelectedTabName) {
									/**
								     * "Text link" validation.
								     */
									if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
											if (0 != $("[name=name2_" + i + "]").val().trim().length) { 
												isValid = true;
												break;
											}
										}
										
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									}
														
								}
								else if ("' . lang('IFrame (Widget)') . '" == strSelectedTabName) {
									/**
								     * "IFrame (Widget)" validation.
								     */
									if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
											if(typeof $("[name=name3_" + i + "]").val() != "undefined"){
												if (0 != $("[name=name3_" + i + "]").val().trim().length) { 
													isValid = true;
													break;
												}
											}
										}
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									}
											
								}
								else if ("' . lang('Mobile LeaderBoard') . '" == strSelectedTabName) {
									/**
								     * "Mobile LeaderBoard" validation.
								     */
									if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
											if(typeof $("[name=name4_" + i + "]").val() != "undefined"){
											if (0 != $("[name=name4_" + i + "]").val().trim().length) { 
												isValid = true;
												break;
											}
											}
										}
										
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									} 
								}
								else if ("' . lang('Mobile Splash') . '" == strSelectedTabName) {
									/**
								     * "Mobile Splash" validation.
								     */
									if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
											if(typeof $("[name=name5_" + i + "]").val() != "undefined"){
												if (0 != $("[name=name5_" + i + "]").val().trim().length) { 
													isValid = true;
													break;
												}
											}
										}
										
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									} 
								}
							
							else if ("' . lang('E-mail') . '" == strSelectedTabName) {
									/**
								     * "E-mail" validation.
								     */
								
										var isValid = false;
										
										
											if(typeof $("[name=scriptCode6]").val() != "undefined"){
												if (0 != $("[name=scriptCode6]").val().trim().length) { 
													
													email_val= $("[name=scriptCode6]").val();
													isValid = true;
													if(email_val.indexOf("{ctag}") === -1){
															$.prompt("'.lang('Please implement the special email tracking code for the call to action event').'", {
																top:200,
																title: "'.lang('Creative Material').'",
																buttons: { "'.lang('OK').'": true}
															});
															return false;
													}
													
												}
											}
										
										if (!isValid) {
											if (0 == $("[name=scriptCode6]").val().trim().length) {
												$("[name=scriptCode6]").css("border-color", "Red");
											} else {
												$("[name=scriptCode6]").css("border-color", "#CECECE");
											}
											return false;
										}
									} 
								
							}
							
							$("#submit_creative_material").click(function() {
								return setDefaultsCreative(); 
								
							});
						</script>';


?>