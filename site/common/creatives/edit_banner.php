<?php

$webAddress = ($typeURL == 2?$set->webAddressHttps:$set->webAddress);
	
		$db         = dbGet($id, $appTable);
		if($mainBannerType == "products")
		$productww = dbGet($db['product_id'], 'products_items');
		else
		$merchantww = getMerchants($db['merchant_id']);

		$adminww    = dbGet($db['admin_id'], 'admins');
		
                // Retrieve the affiliate_id.
                $affiliate_id = retrieveAffiliateId($affiliate_id);


// var_dump($merchantww);
// die();                
                
		if ($fParam) {
                    // $freeParam = '-f' . $fParam;
                    $freeParam = '&p1=' . $fParam;
                }
		if ($subid) {
                    $subid = '&p2=' . $subid;
                }
                
                //die('Test: ' . $affiliate_id);
		$tag = 'a' . $affiliate_id . '-b' . $db['id'] . '-p' . $profile_id . $freeParam.$subid;  // Creat CTag.
		
		if ($db['type'] == "link") {
			$link = $webAddress.'click.php?ctag='.$tag;
			$code = '<!-- '.$set->webTitle.' Affiliate Code -->
					<a href="'.$link.'" target="_blank">'.$db['title'].'</a>
					<!-- // '.$set->webTitle.' Affiliate Code -->';
			$preview = '<a href="'.$link.'" target="_blank">'.$db['title'].'</a>';
			
		} else if ($db['type'] == "widget") {
			$link = $webAddress.'view.php?ctag='.$tag;
			$code = '<!-- '.$set->webTitle.' Affiliate Code -->
					<iframe frameborder="0" src="'.$webAddress.'view.php?ctag='.$tag.'" style="width: '.$db['width'].'px; height: '.$db['height'].'px;" scrolling="no"></iframe>
					<!-- // '.$set->webTitle.' Affiliate Code -->';

			if (strpos($db['iframe_url'],'iframe')==1)
				$preview = $db['iframe_url'];
			else
				$preview = '<iframe frameborder="0" src="'.$db['iframe_url'].'?ctag='.$tag.'&width='.$db['width'].'&height='.$db['height'].'" style="width: 500px; height: 250px;" scrolling="yes"></iframe>';
				
		} else if ($db['type'] == "script") {
			$link = $webAddress.'ad.g?ctag='.$tag;
			$code = '<!-- '.$set->webTitle.' Affiliate Code -->
					<iframe src="'.$link.'" width="'.$db['width'].'" height="'.$db['height'].'" frameborder="0" scrolling="no"></iframe>
					<!-- // '.$set->webTitle.' Affiliate Code -->';
			$preview = $db['scriptCode'];
			
		}  else if ($db['type'] == "mail" OR $db['type'] == "content") {
			$code = '<!-- '.$set->webTitle.' Affiliate Code -->
			<!-- // '.$set->webTitle.' Affiliate Code -->';
			
			if ($db['type'] == "content") {
				$actType = 'showContent'; 
			} else {
		    	 $actType = 'showMail'; 
			}
			
			//$preview = '<iframe src="'.$set->basepage.'?act='.$actType.'&id='.$db['id'].'&ctag='.$tag.'" width="100%" height="500" frameborder="0" scrolling="no" zoom="50%"></iframe><br /><a href="'.$set->basepage.'?act='.$actType.'&id='.$db['id'].'&ctag='.$tag.'" target="_blank" style="font-size: 20px; font-weight: bold; color: green;">'.lang('Click here to'). ' '.lang('open in new window').'</a>';
			
			$preview = '<iframe src="' .$set->SSLprefix. $set->basepage . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag 
						. '" width="100%" height="500" frameborder="1" scrolling="no" zoom="50%">
						</iframe>
						<br />
						<a href="' .$set->SSLprefix. $set->basepage . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag . '" target="_blank" style="font-size: 20px; font-weight: bold; color: green;">' 
							. lang('Click here to') . ' ' . lang('open in new window') 
						. '</a>';
			
			// die ($preview);
			
		} else {
			$link = $webAddress . 'ad.g?ctag=' . $tag;
			/*$code = '<!-- '.$set->webTitle.' Affiliate Code -->
				<script type= "text/javascript" language="javascript" src="'.$link.'"></script>
				<!-- // '.$set->webTitle.' Affiliate Code -->';*/
                        
                        $code = '<!-- ' . $set->webTitle . ' Affiliate Code -->
                                <div id="containerSWF">
                                        <script type= "text/javascript" language="javascript" src="' . $link . '"></script>
                                </div>
                                <script type="text/javascript">
                                        var _object = document.querySelector("#containerSWF OBJECT");
                                        _object.onmousedown = function() {
                                                document.location.href = "' . $webAddress . 'click.php?ctag=' . $tag . '";
                                        };
                                </script>
				<!-- // ' . $set->webTitle . ' Affiliate Code -->';
                  
			//$preview = strpos($db['file'],'/tmp')?'<img src="../images/wheel.gif">' : getBanner($db['file'], 80);
			$preview = '
				<div class="outerBox_preview">
					<div class="wrapper_preview">
						'. (strpos($db['file'],'/tmp')?'<img src="'.$set->SSLprefix.'images/wheel.gif">':'<img class="popupTrackingWindowImg" src="'. $db['file'].  '" alt="" />').'
					</div>
				</div>
				';
			
		}
        // var_dump($db);
// die();        
		
		
//if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.'view.php?ctag='.$tag.'" alt="'.$db['alt'].'" title="'.$db['alt'].'" />';
if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$webAddress.'view.php?ctag='.$tag.'" alt="'.$db['title'].'" title="'.$db['title'].'" />';
// if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.$db['file'].'" alt="'.$db['alt'].'" title="'.$db['alt'].'" />';
if ($db['type'] == "link") $srcFile = $db['title'];
if ($db['type'] != "mobileleader" AND $db['type'] != "mobilesplash") $codelink = '
	<tr>
		<td align="left">'.lang('Direct Link').':</td><td><input type="text" name="" value="'.str_replace("ad.g","click.php",$link).'" onclick="this.focus(); this.select();" style="width: 350px;" /></td>
	</tr><tr>
		<td valign="top" align="left">'.lang('HTML Code').':</td><td><textarea cols="47" rows="5" onclick="this.focus(); this.select();"><a href="'.str_replace("ad.g","click.php",$link).'">'.$srcFile.'</a></textarea></td>
	</tr>
';





	if($set->activateLogs){
	//activity logs
	$fields =array();
	$fields['ip'] = $set->userInfo['ip'];
	$fields['user_id'] = $set->userInfo['id'];
	$fields['theChange'] = json_encode($db);
	$fields['country'] = '';
	$fields['location'] = 'Creatives - Get Code';
	$fields['userType'] = $set->userInfo['level'];
	$fields['_file_'] = __FILE__;
	$fields['_function_'] = 'Get Code';

	$ch      = curl_init();					
	$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec($ch);
	curl_close($ch);
	}

// All Affiliates.
if($set->userInfo['level']=='manager')
	$affiliateqq = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ".(!empty($group_id) ? " and group_id = " . $group_id : "" )." ORDER BY id ASC",__FILE__);
else
	$affiliateqq = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);

if (!(isset($_GET['affiliate_id']) && !empty($_GET['affiliate_id']))) {
	$allAffiliates = '<option selected value="0">'.lang('Choose Affiliate').'</option>';
}

while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {		   
   if (isset($_GET['affiliate_id']) && !empty($_GET['affiliate_id'])) {
	    $allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $affiliate_id ? 'selected' : '').'>['.$affiliateww['id'].'] '
                          .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
   } else {
	    $allAffiliates .= '<option value="'.$affiliateww['id'].'">['.$affiliateww['id'].'] '
                          .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
   }
}

$allProfiles = '';

if ($affiliate_id) {
    $qry = "SELECT id,name,url FROM affiliates_profiles WHERE affiliate_id='".$affiliate_id."' AND valid='1' ORDER BY id ASC";
	// die ($qry);
	$profileqq = function_mysql_query($qry,__FILE__);
    
    while ($profileww = mysql_fetch_assoc($profileqq)) {
        $allProfiles .= '<option value="'.$profileww['id'].'" '.($profileww['id'] == $profile_id ? 'selected' : '').'>'.$profileww['id'].' ['.$profileww['name'].'] '
                     .  $profileww['url'].' (Site ID: '.$profileww['id'].')</option>';
    }
} else {
    // $allProfiles should be empty.
    $allProfiles = '';
}


// All Affiliates

	$pageTitle = lang('Get Creative Code');
				$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

			 
			$ex = explode ('.',$db['file']);
			
			$extension = strtoupper(array_values(array_slice($ex, -1))[0]);
			// die ($extension);
			$set->content .= "<style>
			.slider{
				height:23px !important;
			}
			</style>";
		$set->content .= '<div align="left">
						<table width="99%"><tr>
							<td width="50%" align="left" valign="top" >
								<div class="normalTableTitle">'.lang('Creative Details').':</div><br />
								<form id="frmEditCreative" method="post" enctype="multipart/form-data">
								<input type="hidden" name="db[id]" value="'.$db['id'].'" />
								'.($db['mail'] ? '<input type="hidden" name="mailCode" value="1" />' : '').'
								<input type="hidden" name="act" value="save_banner" />
								<table cellspacing="8" cellpadding="0">
								'.($ty ? '<tr><td colspan="2" style="color:green;font-weight:bold;font-size: 14px;" class="">- '.lang('The page is up to date').' ('.dbDate().')</td></tr>' : '').'
								<tr><td class="blueText">ID:</td><td class="greenText">#'.$db['id'].'</td></tr>
								<tr><td class="blueText">'.lang('Uploaded By').':</td><td class="greenText">'.$adminww['first_name'].' '.$adminww['last_name'].'</td></tr>
								<tr><td class="blueText">'.lang('Uploaded Date').':</td><td class="greenText">'.dbDate($db['rdate']).'</td></tr>
								<tr><td class="blueText">'.lang('Last Update').':</td><td class="greenText">'.dbDate($db['last_update']).'</td></tr>
								<tr><td class="blueText"></td><td class="greenText"><input type="checkbox" name="valid" '.($db['valid'] ? 'checked' : '').' />'.lang('Publish Banner').'</td></tr>
								'.($mainBannerType == "products"?'
								<tr><td class="blueText">'.lang('Product Name').':</td><td class="greenText">'.$productww['title'].'</td></tr>':'
								<tr><td class="blueText">'.lang('Merchant Name').':</td><td class="greenText">'.$merchantww['name'].'</td></tr>').'
								<tr><td class="blueText">'.lang('Creative Type').':</td><td class="greenText">'.ucwords($db['type']).'</td></tr>
								<tr><td class="blueText">'.lang('Creative Category').':</td><td align="left"><select name="db[category_id]" style="width: 250px;"><option value="">'.lang('General').'</option>'.listCategory($db['category_id'],$merchant_id).'</select></td></tr>' .
								(strtolower($db['type'])!='link' ? '
								<tr><td class="blueText">'.lang('Creative Size').':</td><td class="greenText">'.($db['width']).' X '.($db['height']). '</td></tr>' : '' ) . 
								(strtolower($db['type'])!='banner' ? '
								<tr><td class="blueText">'.lang('Creative Image Extension').':</td><td class="greenText">'.($extension). '</td></tr>' :'' ).'
								<tr><td class="blueText">'.lang('Method').':</td><td><select name="db[isOverrideTrackingLink]" style="width: 263px;"><option '.($db['isOverrideTrackingLink']==0 ? " selected " : "").'value="0">'.lang('Default').'</option><option '.($db['isOverrideTrackingLink']==1 ? " selected " : "").' value="1">'.lang('Direct - No Click Tracking').'</option></select></td></tr>
								<tr><td class="blueText">'.lang('Language').':</td><td><select name="db[language_id]" style="width: 263px;">'.listLangs($db['language_id']).'</select></td></tr>
								<tr><td class="blueText">'.lang('Promotion').':</td><td><select name="db[promotion_id]" style="width: 263px;"><option value="">'.lang('General').'</option>'.listPromotions($db['promotion_id'],$db['merchant_id'],"","",0).'</select></td></tr>
								<tr><td class="blueText">'.lang('Creative Name').':</td><td><input type="text" name="db[title]" value="'.$db['title'].'" style="width: 250px;" /></td></tr>
								<tr><td class="blueText">'.lang('Creative URL').':</td><td><input type="text" id="edit_creative_url" name="db[url]" value="'.str_replace('"','&quot;',(trim($db['url']))).'" style="width: 250px;" /></td></tr>
								<tr><td class="blueText">'.lang('Creative ALT').':</td><td><input type="text" name="db[alt]" value="'.$db['alt'].'" style="width: 250px;" /></td></tr>';
								$content =  ($db['type'] == "flash" || $db['type'] == "image" || $db['type'] == "mobilesplash" || $db['type'] == "mobileleader" ? '
									<tr><td class="blueText">'.lang('Creative File').':</td><td>'.(strpos($db['file'],"/tmp")?fileField('file',"../images/wheel.gif"):fileField('file',$db['file'])).'</td></tr>
									' : ($db['type'] == "script" || $db['type'] == "mail" || $db['type'] == "content" ? '
									<tr><td class="blueText">'.($db['type'] == "mail" ? 'E-Mail' : ($db['type'] == "Content"? 'Content' :'Script')).' Code:</td><td><textarea id="scriptcode" name="db[scriptCode]" cols="50" rows="8">'.$db['scriptCode'].'</textarea></td></tr>
									'.($db['type'] == "script" ? '<tr><td class="blueText">'.lang('Width').':</td><td><input type="text" name="db[width]" value="'.$db['width'].'" /></td></tr>
									<tr><td class="blueText">'.lang('Height').':</td><td><input type="text" name="db[height]" value="'.$db['height'].'" /></td></tr>' : '').'
									' : '
									<tr><td class="blueText">'.lang('IFrame URL').':</td><td><input type="text" name="db[iframe_url]" value="'.str_replace('"','&quot;',($db['iframe_url'])).'" /></td></tr>
									<tr><td class="blueText">'.lang('Width').':</td><td><input type="text" name="db[width]" value="'.$db['width'].'" /></td></tr>
									<tr><td class="blueText">'.lang('Height').':</td><td><input type="text" name="db[height]" value="'.$db['height'].'" /></td></tr>
									')).'
									<tr><td class="blueText"></td><td><div><label class="switch" div style="float:left"><input type="checkbox" id="featured" name="db[featured]" value=1 '.($db['featured']==1 ? 'checked' : '').' ><div class="slider round"></div></label><div style="padding-top:6px">'. lang('Featured') .'</div></td></tr>
								<tr><td class="blueText"></td><td><input type="submit" value="'.lang('Save').'" /></td></tr>';
								
								if($set->userInfo['level'] == 'manager'){
									if($set->AllowManagerEditrCreative==1)
										$set->content .= $content;
									else
										$set->content .= "";
								}
								else if($set->userInfo['level'] == "admin"){
									$set->content .= $content;
								}
								
							$set->content .='</table>
							</form>
							</td><td align="left" valign="top"  style="text-align:center;">
									<div class="normalTableTitle">'.lang('Preview').':</div><br />
								'.($db['type'] == "image" || $db['type'] == "flash" || $db['type'] == "mobileleader" || $db['type'] == "mobilesplash" ? 
								(strpos($db['file'],"/tmp")?'<img class="popupTrackingWindowImg" src="../images/wheel.gif"><br/></br>' . lang('System is checking for virus. Please refresh in a minute.'):
								'
										<div class="outerBox_preview">
										<div class="wrapper_preview">
											<img class="popupTrackingWindowImg" src="'. $db['file'].  '" alt="" />
										</div>
									</div>') : $preview) .'
									<br /><br /><hr /><br />
									<div  class="normalTableTitle">'.lang('Get Tracking Code For Affiliate').'</div>
                                                                            
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
                                                                        </style>
                                                                        
                                                                            <form method="get" id="getTrackingCodeForAffiliate" >
                                                                                    <input type="hidden" name="act" value="edit_banner" />
                                                                                    <input type="hidden" name="id" value="'.$id.'" />
                                                                                    <table>
                                                                                    <tr>
                                                                                        <td width="120px" align="left">'.lang('Choose Affiliate').':</td>'
                                                                                     . '<td>'
                                                                                            .'<div class="ui-widget" style="margin-left:-1px;text-align:left;">'
                                                                                            . '<!-- name="affiliate_id" -->'
                                                                                            . '<select id="combobox">'
                                                                                            . '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
                                                                                            . $allAffiliates
                                                                                            .'</select>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    
                                                                                    <tr><td align="left">'.lang('Choose Profile').':</td><td><select name="profile_id" style="width: 213px;"><option selected value="">'.lang('Choose Profile').'</option>'
																					.$allProfiles.
																					
																					'</select></td></tr>
																					
                                                                                    <tr><td align="left">'.lang('Dynamic Parameter').':</td><td><input type="text" name="fParam" value="'.$_GET['fParam'].'" style="width: 200px;" /></td>
																					'.(empty($subid) && $db['isOverrideTrackingLink']==0 ? '<td> <div onclick="javascript:showDiv();">+</div></td>' : '' ).'
																					</tr>
																					<tr>
																					
																					<td style="'.(!empty($subid) ? '' : 'display:none;').'" id="tow">'.lang('Dynamic Parameter2').':</td><td style="'.(!empty($subid) ? '' : 'display:none;').'" id="tow2"><input type="text" name="subid" value="'.$_GET['subid'].'" style="width: 200px;" />
																					
																					</td>
																					</tr>
																					
																					' . ($set->AllowSecuredTrackingCode ==1?'
																					<tr><td colspan=2 height=5px></td></tr>
																					<tr ><td align="left">
																					'. lang('URL') .': </td><td><select name="typeURL"  style="width: 210px;">
																					<option value=1 '. (isset($_GET['typeURL'])?($_GET['typeURL']==1?'selected':''):' selected') .'>'. lang('Default URL') . '</option>
																					<option value=2 '. (isset($_GET['typeURL'])?($_GET['typeURL']==2?'selected':''):'') .'>'. lang('Secured URL') . '</option></select>
																					</td></tr><tr><td colspan=2 height=5px></td></tr>':'').'
																					
                                                                                    <tr>
																					'.(strpos($db['file'],'/tmp')?'':'<td></td><td><input type="submit" onclick="return getCodeValidation()" value="'.lang('Get Code').'" /></td>') .'</tr>
                                                                                    </table>
                                                                                  </form>';
                
										if ($affiliate_id && !($db['type'] == "mail" || $db['type'] == "content")) {
											
											if ($db['isOverrideTrackingLink']==1){
											$gurl = $set->webAddress.'/click.php?ctag=' .  $tag. '&justOutputFinalURL=1';
												$gurl = file_get_contents($gurl);
												$set->content .='
												<table>
												<tr>
		<td align="left">'.lang('Direct Link').':</td><td><input type="text" name="" value="'.str_replace("ad.g","click.php",$gurl).'" onclick="this.focus(); this.select();" style="width: 350px;" /></td>
	</tr><tr>
		<td valign="top" align="left">'.lang('HTML Code').':</td><td><textarea cols="47" rows="5" onclick="this.focus(); this.select();"><a href="'.str_replace("ad.g","click.php",$gurl).'">'.$srcFile.'</a></textarea></td>
	</tr>
	</table>
	';								
	
											}
											else {
											
                                                                                    $set->content .= '
																					<table><tbody><tr><td vertical-align="middle" width="120px" align="left">
																					Javascript</td><td><textarea cols="56" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.$code.'</textarea></td></tr>'.$codelink.'</tbody></table>';
											}
                                                                                    
                                                                                } else if ($affiliate_id && ($db['type'] == "mail" || $db['type'] == "content")) {
																					
											if ($db['isOverrideTrackingLink']==1){
												
												
												
											
												
												
											}
											else {
												
												$url = str_replace('//','/',$set->webAddress . $set->basepage) . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag;
												$set->content .='<div class="blueText">'.($db['type'] == "mail" ? 'E-Mail' : ($db['type'] == "Content"? 'Content' :'Script')).' '.lang('Affiliated HTML Code').':</div><div><textarea name="db[scriptCode111]" cols="50" rows="8">'. str_replace('{ctag}',$tag,$db['scriptCode']).'</textarea></div>';
												$set->content .='<br><br><div class="blueText">'.lang('Direct Link').':&nbsp;&nbsp;&nbsp;<a href="' . $set->basepage . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag . '" target="_blank" style="font-size: 15px;  color: green;">' . lang('Click here to') . ' ' . lang('open in new window') 	. '</a>
												<br><input type="text" name="" value="'. $url.'" onclick="this.focus(); this.select();" style="width: 330px;" />
												</div>';
											}
										}
								
									
								if($set->facebookshare  and(($db['type'] == "link" or $db['type']=='image') && $db['file'] && $affiliate_id)){
									
									if ($db['isOverrideTrackingLink']==1){
												
		/* 										$gurl = $set->webAddress.'/click.php?ctag=' .  $tag. '&justOutputFinalURL=1';
												$gurl = file_get_contents($gurl);
												
												$set->content .='
												<table>
												<tr>
		<td align="left">'.lang('Direct Link').':</td><td><input type="text" name="" value="'.str_replace("ad.g","click.php",$gurl).'" onclick="this.focus(); this.select();" style="width: 350px;" /></td>
	</tr><tr>
		<td valign="top" align="left">'.lang('HTML Code').':</td><td><textarea cols="47" rows="5" onclick="this.focus(); this.select();"><a href="'.str_replace("ad.g","click.php",$gurl).'">'.$srcFile.'</a></textarea></td>
	</tr>
	</table>
	';				 */				
												
												
											}
											else {
												
												
									$fullurl =$set->webAddress.'facebookshare.php?brand='.rawurlencode($merchantww['name']).'&name='.rawurlencode($db['title']).'&image='.rawurlencode($db['file']).'&url='.rawurlencode(str_replace("ad.g","click.php",$link)).'&date='.rawurlencode($db['last_update']).'&pta='.rawurlencode(time());
									// var_dump($_GET);
									// var_dump($_POST);
									// die ($fullurl);
									$shortUrl = new ShortUrl();
									$url = $shortUrl->urlToShortCode($fullurl);
									$dispShorterLink = 0;
									//$url =  $shortUrl->shortCodeToUrl(7);
									if (strlen($url)>0) { 
									$fullurl =  'http'.$set->SSLswitch.'://'.$_SERVER[HTTP_HOST] . '/facebookshare.php?q=' . $url.'&pta='.rawurlencode(time());
									$dispShorterLink =1 ;
									//die ($fullurl);
																
									}
										if ($dispShorterLink==0) {
										$set->content .= '<table><tr><td style="vertical-align:top;text-align:left" width="124px">'.lang('Facebook Share').'</td><td><textarea cols="45" rows="8">'.$set->webAddress.'facebookshare.php?brand='.rawurlencode($merchantww['name']).'&name='.rawurlencode($db['title']).'&image='.rawurlencode($db['file']).'&url='.rawurlencode(str_replace("ad.g","click.php",$link)).'&date='.rawurlencode($db['last_update']).'&pta='.rawurlencode(time()).'</textarea></td></tr></table>';
										} else {
										$set->content .= '<table><tr><td style="vertical-align:top;text-align:left" width="124px">'.lang('Facebook Share').': </td><td><textarea cols="45" rows="2">'.$fullurl.'</textarea></td></tr></table>';
									}
									}
								}
									
	
									if(( $db['type']=='image') && $db['file'] && $affiliate_id ){
								

//popup

$gurl = str_replace("ad.g","click.php",$link);

if ($db['isOverrideTrackingLink']==1){
$gurl .=  '&justOutputFinalURL=1';
$gurl = file_get_contents($gurl);
	}									
												

$popupcode ='<!-- AffiliateTS Modal Start --><div class="affiliateTSmodal"><style>.modal{display:none;position:fixed;z-index:1;padding-top:200px;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:rgb(0,0,0);background-color:rgba(0,0,0,0.77);}.modal-pop-content{background-color:#fefefe;margin:auto;padding:20px;border:1pxsolid#888;width:60%;}.closepop{color:#aaaaaa;float:right;font-size:28px;font-weight:bold;}.closepop:hover,.closepop:focus{color:#000;text-decoration:none;cursor:pointer;}.modal-pop-content{border:2pxsolidblack;border-radius:10px;	text-align:center;}
</style><button id="myPOP-Btn" style="display:none;"></button><div id="mypopModal" class="modal">  <div class="modal-pop-content">    <span class="closepop">x</span>    <p>
<a href="'.$gurl.'" target="_blank"><img src="'.$webAddress.'view.php?ctag='.$tag.'" /></a>
</p>  </div></div><script>
document.addEventListener(\'DOMContentLoaded\', function() {
 modal.style.display = "block";});var modal = document.getElementById("mypopModal");var btn = document.getElementById("myPOP-Btn");var span = document.getElementsByClassName("closepop")[0];btn.onclick = function() {    modal.style.display = "block";}
span.onclick = function() {    modal.style.display = "none";}
window.onclick = function(event) {    if (event.target == modal) { modal.style.display = "none";    }}</script></div><!-- AffiliateTS Modal End -->';

$set->content .= '
																					<table><tbody><tr><td vertical-align="middle" width="120px" align="left">
																					PopUp<Br><Br><span stlye="font-size:0.88em">'.lang('Place this code right before the "body" tag').'</style></td><td><textarea cols="56" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.$popupcode.'</textarea></td></tr></tbody></table>';
																					

								
										
									}
									
									
									if($set->qrcode  and (($db['type'] == "link" or $db['type']=='image') && $db['file'] && $affiliate_id && $db['isOverrideTrackingLink']==0 )){
									//$qr = new BarcodeQR();
									//$qr->url(str_replace("ad.g","click.php",$link),1);
									//$qrCode = file_get_contents($set->webAddress."common/BarcodeQR.php?link=".$link);
									//die ($qrCode);
									//$base64 = 'data:image/PNG;base64,' . base64_encode($qrCode );
									
									include_once('common/BarcodeQR.php');
									
									
									$qrImagePath = 'files/qr/' . $db['id']. '/';
									
									// $qrImagePath = 'files/qr/' . $db['id']. '/qrcode.png';
									$folder = 'files/qr/' . $db['id'];
									 if (!is_dir('files/qr')) {
										 mkdir('files/qr');
									 }
									 if (!is_dir($folder)) {
										 mkdir($folder);
									 }
									$qr = new BarcodeQR();
									$qr->url(str_replace("ad.g","click.php",$link),1);
									$qr->draw(150,$qrImagePath);
								
									
									//$qrCode = file_get_contents($set->webAddress."common/BarcodeQR.php?link=".$link);
									//die ($qrCode);
									$file = $set->webAddress .$qrImagePath . "qrcode.png";	
									//$base64 = 'data:image/PNG;base64,' . base64_encode($set->webAddress ."qrcode.png");
								
										$set->content .= '<table><tr><td style="vertical-align:top;text-align:left" width="120px">'.lang('QR Code').':</td><td><img src='.$file.' /></td></tr></table>';
									}
									
									
									
								$set->content.='</td>
						</tr></table>
					</div>
	<style>
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
						
						
					</style>
                                        <script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
                                        <script>
                                            function showDiv() {
																						td = document.getElementById(\'tow\');
																						td.style.display = "";
																						td = document.getElementById(\'tow2\');
																						td.style.display = "";
											}
																					
											function getCodeValidation() {
												
                                                var code = $("[name=affiliate_id]").val();
                                                var firstIndex  = code.indexOf("[");
                                                var secondIndex = code.indexOf("]");
                                                code            = code.substring(firstIndex + 1 , secondIndex);
                                                
                                                if (
                                                    code === ""        ||
                                                    code === undefined ||
                                                    code === "0"       ||
                                                    code === 0         ||
                                                    code === "Choose+affiliate"
                                                ) {
                                                    return false;
                                                } else {
                                                    return true;
                                                }
                                            }
                                            
                                            function getQueryVariable(queryStringParam) {
                                                var query = window.location.search.substring(1);
                                                var vars  = query.split("&");

                                                for (var i = 0; i < vars.length; i++) {
                                                    var pair = vars[i].split("=");

                                                    if (pair[0] === queryStringParam) {
                                                        return pair[1];
                                                    }
                                                }
                                                return null;
                                            }
                                            
                                            /*(function() {
                                                var affiliate_id = getQueryVariable("[" + affiliate_id + "]");
                                                
                                                if (affiliate_id !== null) {
                                                    loadProfiles(affiliate_id);
                                                }
                                            })();*/
											
											$(document).ready(function(){
													$("#edit_creative_url").blur(function() {
												  var input = $(this);
												  var val = input.val();
												  if (val && !val.match(/^http([s]?):\/\/.*/)) {
													input.val("http://" + val);
												  }
												});
												
												$("#frmEditCreative").on("submit",function(){
													if($([name=scriptcode]).val().indexOf("{ctag}") === -1){
														$.prompt("'.lang('Please implement the special email tracking code for the call to action event').'", {
																top:200,
																title: "'.lang('Creative Material').'",
																buttons: { "'.lang('OK').'": true}
															});
															return false;
													}
												});
												
												});
                                        </script>
										<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
										<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
									
                                        ';
										?>