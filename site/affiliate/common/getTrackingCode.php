<?php 
	if($creativetype == "product"){
		$ww=dbGet($creative_id,'merchants_creative');
	}
	else{
		$ww=dbGet($id,$appTable);	
	}

		$merchantww=dbGet($ww['merchant_id'],"merchants");
		$set->pageTitle = lang('Get Banner Code');
		//print_r($ww);die;
		if (!$ww['id'] OR !$ww['valid']) _goto();
		//print_r($_GET);die;
		//die;
		$profileqq=function_mysql_query("SELECT id,name,url FROM affiliates_profiles WHERE affiliate_id='".$set->userInfo['id']."' AND valid='1' ORDER BY id ASC",__FILE__);
		while ($profileww=mysql_fetch_assoc($profileqq)) {
			$listProfiles .= '<option value="'.$profileww['id'].'" '.($profileww['id'] == $profile_id ? 'selected' : '').'>['.$profileww['name'].'] '.$profileww['url'].' (Site ID: '.$profileww['id'].')</option>';
			if (!$setProfile) $setProfile = ($profile_id == $profileww['id'] ? $profileww['id'] : '');
		}
		if ($fParam) $freeParam = '&p1='.$fParam;
		
		$productTagPart = $ww['product_id'] > 0 ? '-g'.$ww['product_id'].'' : '';
		
		if ($subid) $subid = '&p2='.$subid;
		$tag='a'.$set->userInfo['id'].'-b'.$ww['id'].$productTagPart.'-p'.$setProfile.$freeParam.$subid; // Creat CTag
		$webAddress = ($typeURL == 2?$set->webAddressHttps: $set->webAddress);

		if ($ww['type'] == "link") {
                    $link = $webAddress.'click.php?ctag='.$tag;
                    $code = '<!-- '.$set->webTitle.' Affiliate Code -->
                    <a href="'.$link.'" target="_blank">'.$ww['title'].'</a>
                    <!-- // '.$set->webTitle.' Affiliate Code -->';
                    $preview = '<a href="'.$link.'" target="_blank">'.$ww['title'].'</a>';
                } else if ($ww['type'] == "widget") {
                                $link = $webAddress.'view.php?ctag='.$tag;
                                $code = '<!-- '.$set->webTitle.' Affiliate Code -->
                        <iframe frameborder="0" src="'.$webAddress.'view.php?ctag='.$tag.'" style="width: '.$ww['width'].'px; height: '.$ww['height'].'px;" scrolling="no"></iframe>
                        <!-- // '.$set->webTitle.' Affiliate Code -->';

                        if (strpos($ww['iframe_url'],'iframe')==1)
                                        $preview = $ww['iframe_url'];
                                                else
                                        $preview = '<iframe frameborder="0" src="'.$ww['iframe_url'].'?ctag='.$tag.'&width='.$ww['width'].'&height='.$ww['height'].'" style="width: 400px; height: 250px;" scrolling="yes"></iframe>';

                } else if ($ww['type'] == "script") {
                            $link = $webAddress.'ad.g?ctag='.$tag;
                            $code = '<!-- '.$set->webTitle.' Affiliate Code -->
                    <iframe src="'.$link.'" width="'.$ww['width'].'" height="'.$ww['height'].'" frameborder="0" scrolling="no"></iframe>
                    <!-- // '.$set->webTitle.' Affiliate Code -->';
                    $preview = $ww['scriptCode'];
                } else if ($ww['type'] == "mail" OR  strtolower($ww['type']) == "content") {
                            $code = '<!-- '.$set->webTitle.' Affiliate Code -->
                    <!-- // '.$set->webTitle.' Affiliate Code -->';

                    if (strtolower($ww['type']) == "content") {
                            $actType = 'showContent'; }
                     else {
                            $actType = 'showMail'; 
                     }
                    
					if($creativetype == "product")
						$preview = '<iframe src="'.$set->SSLprefix.'affiliate/creative.php'.'?act='.$actType.'&id='.$ww['id'].'&ctag='.$tag.'" width="100%" height="500" frameborder="1" scrolling="no" zoom="50%"></iframe><br /><a href="'.$set->SSLprefix.'affiliate/creative.php'.'?act='.$actType.'&id='.$ww['id'].'&ctag='.$tag.'" target="_blank" style="font-size: 20px; font-weight: bold; color: green;">'.lang('Click here to'). ' '.lang('open in new window').'</a>';
					else
						$preview = '<iframe src="'.$set->SSLprefix.'affiliate/creative.php'.'?act='.$actType.'&id='.$ww['id'].'&ctag='.$tag.'" width="100%" height="500" frameborder="1" scrolling="no" zoom="50%"></iframe><br /><a href="'.$set->SSLprefix.'affiliate/creative.php'.'?act='.$actType.'&id='.$ww['id'].'&ctag='.$tag.'" target="_blank" style="font-size: 20px; font-weight: bold; color: green;">'.lang('Click here to'). ' '.lang('open in new window').'</a>';

                } else {
                    
                    $link = $webAddress . 'ad.g?ctag=' . $tag;
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
                    
				$preview = '
				<div class="outerBox_preview">
					<div class="wrapper_preview">
						<img class="popupTrackingWindowImg" src="'. $ww['file'].  '" alt="" />
					</div>
				</div>
				';
				
                }
                
// if ($ww['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.$ww['file'].'" alt="'.$ww['alt'].'" title="'.$ww['alt'].'" />';
//if ($ww['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.'view.php?ctag='.$tag.'" alt="'.$ww['alt'].'" title="'.$ww['alt'].'" />';
if ($ww['type'] == "image") $srcFile = '<img border="0"  src="'.$webAddress.'view.php?ctag='.$tag.'" alt="'.$ww['title'].'" title="'.$ww['title'].'" />';
if ($ww['type'] == "link") $srcFile = $ww['title'];
if ($ww['type'] != "mobileleader" AND $ww['type'] != "mobilesplash") $codelink = '
	<tr>
		<td>'.lang('Direct Link').':</td><td><input type="text" name="" value="'.(strpos($ww['file'],'/tmp')?'':str_replace("ad.g","click.php",$link)).'" onclick="this.focus(); this.select();" style="width: 330px;" /></td>
	</tr><tr>
		<td valign="top">'.lang('HTML Code').':</td><td><textarea cols="45" rows="6" onclick="this.focus(); this.select();"><a href="'.(strpos($ww['file'],'/tmp')?str_replace("ad.g","click.php",''):str_replace("ad.g","click.php",$link)).'">'.(strpos($ww['file'],'/tmp')?'':$srcFile).'</a></textarea></td>
	</tr>
';


	$set->pageTitle = lang('Get Tracking Code');
	
	
	$set->content.='<style>
						#bottom_table , 
						.headerSite,
						.headerSite + *,
						.headerSite + * +*{
							display:none!important;
						}
						body .content{
							padding-bottom:0;
							 margin-top: 1%;
							margin-left: 1%;
						}
						.content td div:last-child img{
							vertical-align: top;
						}
						
						
					</style>
					<script>
					function showDiv() {
																						td = document.getElementById(\'tow\');
																						td.style.display = "";
																						td = document.getElementById(\'tow2\');
																						td.style.display = "";
											}
					</script>
					
					
					';
					
	$set->content .= '<form method="get">
					<input type="hidden" name="act" value="get_code" />';
					if($creativetype == "product"){
					$set->content .='<input type="hidden" name="creative_id" value="'.$creative_id.'" />';
					}
					else{
						$set->content .='<input type="hidden" name="id" value="'.$id.'" />';
					}
					$set->content .='
					<table width=100%><tr><td><b>'.lang('Choose Profile').':</b></td> <td style="padding-right:3%"><select name="profile_id"><option value="">'.lang('General').'</option>'.$listProfiles.'</select> </td><td style="margin-left:10px;">
					'.lang('Dynamic Parameter').':</td><td width=150px ><input type="text" name="fParam" value="'.$_GET['fParam'].'" /></td>
																										'.(empty($subid) && $ww['isOverrideTrackingLink']==0 ? '<td  style="margin-left:10px;padding-right:3%;"> <div onclick="javascript:showDiv();" style="padding-right:3%;">+</div></td>' : '' ).'
																					
																					
																					
																					<td style="'.(!empty($subid) ? '' : 'display:none;').'padding-right:3%;" id="tow">'.lang('Dynamic Parameter2').':</td><td style="'.(!empty($subid) ? '' : 'display:none;').'padding-right:5%;" id="tow2"><input type="text" name="subid" value="'.$_GET['subid'].'" style="width: 200px;" />
																					
																					</td>
																					' . ($set->AllowSecuredTrackingCode ==1?'
																					<td align="left">
																					'. lang('URL') .': </td><td style="padding-right:3%"><select name="typeURL">
																					<option value=1 '. (isset($_GET['typeURL'])?($_GET['typeURL']==1?'selected':''):' selected') .'>'. lang('Default URL') . '</option>
																					<option value=2 '. (isset($_GET['typeURL'])?($_GET['typeURL']==2?'selected':''):'') .'>'. lang('Secured URL') . '</option></select>
																					</td>':'').'
					
					'. (strpos($ww['file'],'/tmp')?'':'<td><input type="submit" value="'.lang('Get Code').'" /></td>').'</tr>
					</table>
					<table style="padding-top:10px" width="99%" border="0" cellpadding="0" cellspacing="3">
						<tr>
							<td width="55%" align="left" valign="top" style="text-align:center;">
								<div class="normalTableTitle">'.lang('Preview').':</div><br />
								'.$preview. '
							</td>
							<td width="45%" align="left" valign="top">
								<div class="normalTableTitle">'.lang('Copy code to your site').':</div>';
							//($ww['type'] == "mail" ? '<div align="center" style="padding-top: 20px;"><a href="'.$set->basepage.'?act=showMail&id='.$ww['id'].'&ctag='.$tag.'" target="_blank" style="font-size: 20px; font-weight: bold; color: green;">Open in new window</a></div>' : '<br>JavaScript:<textarea cols="55" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.$code.'</textarea>'.$codelink).'

									$CreativeType = strtolower($ww['type']);
									if ($ww['isOverrideTrackingLink']==1){
									
	$gurl = $set->webAddress.'/click.php?ctag=' .  $tag. '&justOutputFinalURL=1';
								$gurl = file_get_contents($gurl);
								
								
								
													$set->content .='
												<table>
										<tr>
		<td>'.lang('Direct Link').':</td><td><input type="text" name="" value="'.(strpos($ww['file'],'/tmp')?'':str_replace("ad.g","click.php",$gurl)).'" onclick="this.focus(); this.select();" style="width: 330px;" /></td>
	</tr><tr>
		<td valign="top">'.lang('HTML Code').':</td><td><textarea cols="45" rows="6" onclick="this.focus(); this.select();"><a href="'.(strpos($ww['file'],'/tmp')?str_replace("ad.g","click.php",''):str_replace("ad.g","click.php",$gurl)).'">'.(strpos($ww['file'],'/tmp')?'':$srcFile).'</a></textarea></td>
	</tr>
	</table>
	';
	
	
										
									}
									else {
										
										if  ( ($CreativeType == "mail" || $CreativeType == "content")) {
											$url = $set->webAddress . $set->basepage . '?act=' . $actType . '&id=' . $ww['id'] . '&ctag=' . $tag;
											
												$set->content .='<div class="blueText">'.($ww['type'] == "mail" ? 'E-Mail' : ($CreativeType == "content"? 'Content' :'Script')).' '.lang('Affiliated HTML Code').':</div><div><textarea  cols="50" rows="8">'. (strpos($ww['file'],'/tmp')?'':str_replace('{ctag}',$tag,$ww['scriptCode'])).'</textarea></div>';
												$set->content .='<br><br><div class="blueText">'.lang('Direct Link').':&nbsp;&nbsp;&nbsp;'.(strpos($ww['file'],'/tmp')?'':'<a href="' . $set->SSLprefix.'affiliate/creative.php' . '?act=' . $actType . '&id=' . $ww['id'] . '&ctag=' . $tag . '" target="_blank" style="font-size: 15px;  color: green;">' . lang('Click here to') . ' ' . lang('open in new window') 	. '</a>').'
												<br><input type="text" name="" value="'. (strpos($ww['file'],'/tmp')?'':$url).'" onclick="this.focus(); this.select();" style="width: 330px;" />
												</div>';
										}
										else {
											$set->content .='<table><tr><td vertical-align="middle">JavaScript:</td><td><textarea cols="55" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.(strpos($ww['file'],'/tmp')?'':$code).'</textarea></td></tr>'.$codelink.'</table>';
										}															
									
	
	
		
		
	
							if($set->facebookshare  and ((strpos( '_' . $ww['type'],'link')>0 or strpos( '_' . $ww['type'],'image')>0) && $ww['file'] &&  $set->userInfo['id'])){
								
								 // die ('file: '  . $db['file']);
									
									
									
									$fullurl =$set->webAddress.'facebookshare.php?brand='.rawurlencode($merchantww['name']).'&name='.rawurlencode($ww['title']).'&image='.rawurlencode($ww['file']).'&url='.rawurlencode(str_replace("ad.g","click.php",$link)).'&date='.rawurlencode($ww['last_update']).'&pta='.rawurlencode(time());
									$shortUrl = new ShortUrl();
									$url = $shortUrl->urlToShortCode($fullurl);
									$dispShorterLink = 0;
									//$url =  $shortUrl->shortCodeToUrl(7);
									if (strlen($url)>0) { 
									$fullurl =  'http://'.$_SERVER[HTTP_HOST] . '/facebookshare.php?q=' . $url.'&pta='.rawurlencode(time());
									$dispShorterLink =1 ;
									//die ($fullurl);
																
									}
										if ($dispShorterLink==0) {
										$set->content .= '<table><tr><td style="vertical-align:top;width:70px">'.lang('Facebook Share').':</td><td><textarea cols="42" rows="9">'.(strpos($ww['file'],'/tmp')?'':$set->webAddress.'facebookshare.php?brand='.rawurlencode($merchantww['name']).'&name='.rawurlencode($db['title']).'&image='.rawurlencode($db['file']).'&url='.rawurlencode(str_replace("ad.g","click.php",$link)).'&date='.rawurlencode($db['last_update']).'&pta='.rawurlencode(time())).'</textarea></td></tr></table>';
										} else {
										$set->content .= '<table><tr><td style="vertical-align:top;width:70px">'.lang('Facebook Share').':</td><td><textarea cols="42" rows="3">'.(strpos($ww['file'],'/tmp')?'':$fullurl).'</textarea></td></tr></table>';
									}
									}
									
									
									

			if($set->qrcode  and ((strpos( '_' . $ww['type'],'link')>0 or strpos( '_' . $ww['type'],'image')>0) && $ww['file'] &&  $set->userInfo['id'])){
									
									//$qr = new BarcodeQR();
									//$qr->url(str_replace("ad.g","click.php",$link),1);
									
									include_once('common/BarcodeQR.php');
									
									
									$qrImagePath = 'files/qr/' . $ww['id']. '/';
									
									// $qrImagePath = 'files/qr/' . $ww['id']. '/qrcode.png';
									$folder = 'files/qr/' . $ww['id'];
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
								
										$set->content.= '<br><div style="vertical-align:top">'.lang('QR Code').':&nbsp;'.(strpos($ww['file'],'/tmp')?'':'<img src='.$file.' />').'</div>';
									} ;
									
									}

$set->content .= '

							</td>
						</tr>
					</table>
					</form>';