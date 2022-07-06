<?php
	
	
		
		
		$typesArray = array();
		
		
		if($mainBannerType=="products")
		$qry = "select distinct type as type from  ".$appTable." WHERE type not like 'coupon' and product_id in (".$id.") ";//.$where;
		else
		$qry = "select distinct type as type from  ".$appTable." WHERE type not like 'coupon' and merchant_id in (".$merchant_id.") ";//.$where;
	
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$typesArray[]=$ww['type'];
			
		}
		$langsArray = array();
		if($mainBannerType=="products")
		$qry = "select distinct language_id as  language_id from  ".$appTable." WHERE product_id in (".$id.") ";//.$where;
		else
		$qry = "select distinct language_id as  language_id from  ".$appTable." WHERE merchant_id in (".$merchant_id.") ";//.$where;
	
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$langsArray[]=$ww['language_id'];
		}
		
		
		
		
		$comboq = "SELECT count(CONCAT(width, 'x', height)) as count, CONCAT(width, ' X ', height) as dim,`merchants_creative`.height ,`merchants_creative`.width FROM `merchants_creative` WHERE merchant_id = '".$merchant_id."' and  `merchants_creative`.height>0 and `merchants_creative`.width>0 and `merchants_creative`.valid =1
group by CONCAT(width, ' X ', height) order by height, width";

$combolist = '<select name="creativedimenstion" style="width: 140px;"><option value="">'.lang('Show All').'</option>';
$qqcombo=function_mysql_query($comboq,__FILE__);
		while ($wwcombo=mysql_fetch_assoc($qqcombo)) {
			$combolist.= '<option value="'.str_replace(' ','',$wwcombo['dim']) .'">'. $wwcombo['dim'] . '  (' . $wwcombo['count']. ')'.'</option>';
		}
$combolist.='</select>';


$set->content .= '
						<form method="get" id="formSearch">
						<input type="hidden" name="result" value="1" />
						'.($mainBannerType=="products"?'<input type="hidden" name="id" value="'.$id.'" />
						<input type="hidden" name="tab" value="creative_material" />
						<input type="hidden" name="act" value="products" />':'
						<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />').'
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>';
					
							
						if($mainBannerType!="products"){
						
							if($set->userInfo['level'] == 'admin'){
								if (mysql_num_rows($numqq) > 1) { 
									$set->content .= '<td  class="blueText" style="padding-bottom: 10px;">' . lang('Switch Merchant') . ':</td>';
								}
							}
							else if($set->userInfo['level'] == 'manager' && $set->AllowManagerEditrCreative==1)
							{
									if (mysql_num_rows($numqq) > 1) { 
										$set->content .= '<td  class="blueText" style="padding-bottom: 10px;">' . lang('Switch Merchant') . ':</td>';
									}
								
							}
						}
						
			$set->content .= '<td align="left" class="blueText">'.lang('Creative Type').':</td>
								<td align="left" class="blueText">'.lang('Category').':</td>
								<td align="left" class="blueText"><span class="langHover">'.lang('Language').':</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img class="imgSettingsLng" style="display:none;width:15%;cursor:pointer;" src="../images/settings.png" title="'.lang('Add and change languages list').'"onclick="javascript:window.open(\'http://devs.affiliatebuddies.com/admin/languages.php\')"/></td>
									<td style="width:20px;"></td>
										<td align="left" class="blueText">'.lang('Width').':</td>
								<td align="left" class="blueText">'.lang('Height').':</td>
								<td align="left" width="110px" ><span class="blueText">'.lang('Choose Size').':</span><br><span style="font-size:10px">'.lang('Width'). ' X ' . lang('Height') . ' ( '.lang('Count').' )</span></td>
								<td style="width:20px";></td>
								<td align="left" class="blueText">'.lang('Promotion').':</td>
								<td align="left" class="blueText">'.lang('Available').':</td>
								<td align="left" class="blueText">'.lang('Creative Name / ID').':</td>
										
						
						</tr>
						<tr>
						';
						
						
						
						if($mainBannerType!="products"){	
						if (mysql_num_rows($numqq) > 1) {
									$set->content .= '	
											<td><select onchange="location.href=\''  . $set->basepage . '?merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id) . '</select></td>
										
									';
						}
						}
		$set->content .= '
								<td align="left">
									<select name="type" style="width: 100px;">
										<option value="">'.lang('All').'</option>
										' . (in_array("image",$typesArray) ? '<option value="image" '.($type == "image" ? 'selected' : '').'>'.lang('Image').'</option>' : "") . '
										' . (in_array("mobileleader",$typesArray) ? '<option value="mobileleader" '.($type == "mobileleader" ? 'selected' : '').'>'.lang('Mobile Leader').'</option>' : "") . '
										' . (in_array("mobilesplash",$typesArray) ? '<option value="mobilesplash" '.($type == "mobilesplash" ? 'selected' : '').'>'.lang('Mobile Splash').'</option>' : "") . '
										' . (in_array("flash",$typesArray) ? '<option value="flash" '.($type == "flash" ? 'selected' : '').'>'.lang('Flash').'</option>' : "") . '
										' . (in_array("widget",$typesArray) ? '<option value="widget" '.($type == "widget" ? 'selected' : '').'>'.lang('Widget').'</option>' : "") . '
										' . (in_array("link",$typesArray) ? '<option value="link" '.($type == "link" ? 'selected' : '').'>'.lang('Text Link').'</option>' : "") . '
										' . (in_array("mail",$typesArray) ? '<option value="mail" '.($type == "mail" ? 'selected' : '').'>'.lang('E-Mail').'</option>' : "") . '
										' . (in_array("content",$typesArray) ? '<option value="content" '.($type == "content" ? 'selected' : '').'>'.lang('Content').'</option>' : "") . '
									</select>
									</td>
								<td align="left"><select name="category_id" style="width: 100px;"><option value="">'.lang('All').'</option>'.listCategory($category_id,$merchant_id).'</select></td>
								
								<td align="left"><select name="lang" id="lang" style="width: 100px;"><option value="">'.lang('All').'</option>'.listLangs($lang,0,$langsArray).'</select></td>
								<td style="width:20px";></td>
								<td align="left"><input type="text" name="width" value="'.$width.'" style="width: 40px; text-align: center;" /></td>
								<td align="left"><input type="text" name="height" value="'.$height.'" style="width: 40px; text-align: center;" /></td>
								<td align="left" width="130">'.$combolist.'</td>
								<td style="width:20px";></td>
								<td align="left"><select name="promotion" style="width: 140px;"><option value="">'.lang('General').'</option>'.listPromotions($promotion,$merchant_id).'</select></td>
								<td align="left"><select name="valid" style="width: 110px;"><option '. ($valid==0 ? 'selected' :'') .'value="0">'.lang('Show All').'</option><option '. ($valid==1 ? 'selected' :'') .' value="1">'.lang('Active Only').'</option></select></td>
								<td align="left"><input type="text" name="q" value="'.$q.'" style="width: 120px;" /></td>
								<td style="width:20px";></td>
								<td><input type="submit" value="'.lang('Search').'" /></td><td>&nbsp;&nbsp;</td><td><input name="download" type="submit" value="'.lang('Download').'" /></td>
								
								
								
							</tr></table>
						</div>
							</form>';
							?>
                       