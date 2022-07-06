<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
require_once ('common/ShortUrl.php');
require_once('common/InStyle.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$affiliate_id = isset ($_GET['affiliate_id']) ? $_GET['affiliate_id'] : 0 ;
//https handling
$set->webAddress = ($set->isHttps?$set->webAddressHttps:$set->webAddress);

switch ($act) {
	default:
			$pageTitle = lang('Get Tracking Code');
			$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'.$set->SSLprefix.'admin/products.php?act=products">'.lang('Products').'</a></li>
				<li><a href="'.$set->SSLprefix.'admin/edit_products.php?act=products&id='.$id.'&tab=creative_material">'.lang('Edit Products').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
			$set->content = '
			
			<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
			
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
			';
			
			
					// Retrieve the affiliate_id.
                $affiliate_id = retrieveAffiliateId($affiliate_id);
					
					// All Affiliates.
					$affiliateqq = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
					$allAffiliates = '<option selected value="0">'.lang('To All Afiliates').'</option>';
					while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {		   
							$allAffiliates .= '<option value="'.$affiliateww['id'].'"'.($affiliateww['id'] == $affiliate_id ? ' selected' : '').'>['.$affiliateww['id'].'] '
											  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
					}
					
					$db=dbGet($banner_id,"merchants_creative");
					$adminww    = dbGet($db['admin_id'], 'admins');
		
              
                
                
		if ($fParam) {
                    $freeParam = '-f' . $fParam;
                }
		if ($subid) {
                    $subid = '&subid=' . $subid;
                }
                
                //die('Test: ' . $affiliate_id);
				$tag = 'a' . $affiliate_id . '-b' . $banner_id . '-p' . $profile_id . $freeParam.$subid;  // Creat CTag.
				
		//		print_r($db);die;
				if ($db['type'] == "link") {
					$link = $set->webAddress.'click.php?ctag='.$tag;
					$code = '<!-- '.$set->webTitle.' Affiliate Code -->
							<a href="'.$link.'" target="_blank">'.$db['title'].'</a>
							<!-- // '.$set->webTitle.' Affiliate Code -->';
					$preview = '<a href="'.$link.'" target="_blank">'.$db['title'].'</a>';
					
				}  else {
					$link = $set->webAddress . 'ad.g?ctag=' . $tag;
					
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
														document.location.href = "' . $set->webAddress . 'click.php?ctag=' . $tag . '";
												};
										</script>
						<!-- // ' . $set->webTitle . ' Affiliate Code -->';
								
					$preview = getBanner($db['file'], 80);
				}
				// var_dump($db);
		// die();        
				
				
		//if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.'view.php?ctag='.$tag.'" alt="'.$db['alt'].'" title="'.$db['alt'].'" />';
		if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.'view.php?ctag='.$tag.'" alt="'.$db['title'].'" title="'.$db['title'].'" />';
		// if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.$db['file'].'" alt="'.$db['alt'].'" title="'.$db['alt'].'" />';
		if ($db['type'] == "link") $srcFile = $db['title'];
		if ($db['type'] != "mobileleader" AND $db['type'] != "mobilesplash") $codelink = '<br />
		<table>
			<tr>
				<td style="width:100px">'.lang('Direct Link').':</td><td><input type="text" name="" value="'.str_replace("ad.g","click.php",$link).'" onclick="this.focus(); this.select();" style="width: 350px;" /></td>
			</tr><tr>
				<td valign="top">'.lang('HTML Code').':</td><td><textarea cols="47" rows="5" onclick="this.focus(); this.select();"><a href="'.str_replace("ad.g","click.php",$link).'">'.$srcFile.'</a></textarea></td>
			</tr>
		</table>';
					
					$set->content .= '<form method="get" id="getTrackingCodeForAffiliate">
                                                                                    <input type="hidden" name="act" value="edit_banner" />
                                                                                    <input type="hidden" name="id" value="'.$id.'" />
																					<input type="hidden" name="banner_id" value="'.$banner_id.'" />
																					<div class="normalTableTitle">'.lang('Tracking Code').'</div>
								<table class="normal" border="0" cellpadding="0" cellspacing="5">
									<tr>
												<td width="150" align="left">'.lang('Select Affiliate').':</td>
												<td>
												<div class="ui-widget">'
                                                                                            . '<!-- name="affiliate_id" -->'
                                                                                            . '<select id="combobox">'
                                                                                            . '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
                                                                                            . $allAffiliates
                                                                                            .'</select>
                                                                                            </div>
											</td>
									</tr><tr>
								
									</tr>  <tr><td>'.lang('Choose Profile').':</td><td><select name="profile_id" style="width: 213px;"><option selected value="">'.lang('Choose Profile').'</option>'
																					.$allProfiles.
																					
																					'</select></td></tr>
																					
                                                                                    <tr><td>'.lang('Dynamic Parameter').':</td><td><input type="text" name="fParam" value="'.$_GET['fParam'].'" style="width: 200px;" /></td>
																					'.(empty($subid) ? '<td> <div onclick="javascript:showDiv();">+</div></td>' : '' ).'
																					</tr>
																					<tr>
																					
																					<td style="'.(!empty($subid) ? '' : 'display:none;').'" id="tow">'.lang('Dynamic Parameter2').':</td><td style="'.(!empty($subid) ? '' : 'display:none;').'" id="tow2"><input type="text" name="subid" value="'.$_GET['subid'].'" style="width: 200px;" />
																					
																					</td>
																					</tr>
                                                                                    <tr><td></td><td><input type="submit" onclick="return getCodeValidation()" value="'.lang('Get Code').'" /></td></tr>
                                                                                    </table>
                                                                                  </form>'; 
		if ($affiliate_id && !($db['type'] == "mail" || $db['type'] == "content")) {
                                                                                    $set->content .= '
																					<table>
																					<tr>
																					<td style="width:100px;" valign="middle">Javascript:</td>
																					<td><textarea cols="56" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.$code.'</textarea></td>
																					</tr>
																					</table>'.$codelink .'';
                                                                                    
                                                                                } else if ($affiliate_id && ($db['type'] == "mail" || $db['type'] == "content")) {
												$url = str_replace('//','/',$set->webAddress . $set->basepage) . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag;
												$set->content .='<div class="blueText">'.($db['type'] == "mail" ? 'E-Mail' : ($db['type'] == "Content"? 'Content' :'Script')).' '.lang('Affiliated HTML Code').':</div><div><textarea name="db[scriptCode111]" cols="50" rows="8">'. str_replace('{ctag}',$tag,$db['scriptCode']).'</textarea></div>';
												$set->content .='<br><br><div class="blueText">'.lang('Direct Link').':&nbsp;&nbsp;&nbsp;<a href="' . $set->SSLprefix.$set->basepage . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag . '" target="_blank" style="font-size: 15px;  color: green;">' . lang('Click here to') . ' ' . lang('open in new window') 	. '</a>
												<br><input type="text" name="" value="'. $url.'" onclick="this.focus(); this.select();" style="width: 330px;" />
												</div>';
										}
								
									
								if($set->facebookshare  and(($db['type'] == "link" or $db['type']=='image') && $db['file'] && $affiliate_id)){
									$fullurl =$set->webAddress.'facebookshare.php?brand='.rawurlencode($merchantww['name']).'&name='.rawurlencode($db['title']).'&image='.rawurlencode($db['file']).'&url='.rawurlencode(str_replace("ad.g","click.php",$link)).'&date='.rawurlencode($db['last_update']).'&pta='.rawurlencode(time());
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
										$set->content .= '<table><tr><td style="width:100px">'.lang('Facebook Share').':</td><td><textarea cols="45" rows="8">'.$set->webAddress.'facebookshare.php?brand='.rawurlencode($merchantww['name']).'&name='.rawurlencode($db['title']).'&image='.rawurlencode($db['file']).'&url='.rawurlencode(str_replace("ad.g","click.php",$link)).'&date='.rawurlencode($db['last_update']).'&pta='.rawurlencode(time()).'</textarea></td></tr></table>';
										} else {
										$set->content .= '<table><tr><td style="width:100px">'.lang('Facebook Share').':</td><td><textarea cols="45" rows="2">'.$fullurl.'</textarea></td></tr></table>';
									}
									}
									
								
									if($set->qrcode  and (($db['type'] == "link" or $db['type']=='image') && $db['file'] && $affiliate_id)){
									//$qr = new BarcodeQR();
									//$qr->url(str_replace("ad.g","click.php",$link),1);
									/* $qrCode = file_get_contents($set->webAddress."common/BarcodeQR.php?link=".$link);
									//die ($qrCode);
									$base64 = 'data:image/PNG;base64,' . base64_encode($qrCode ); */
									
									include_once('common/BarcodeQR.php');
									
									
									$qrImagePath = 'files/qr/' . $db['id']. '/';
									
									// $qrImagePath = 'files/qr/' . $ww['id']. '/qrcode.png';
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
								
										$set->content .= '<table><tr><td style="width:100px">'.lang('QR Code').'</td><td><img src='.$file.' /></td></tr></table></div>';
									}
									
								$set->content.='</td>
						</tr></table>
					</div>
	<style>
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
                                        </script>
                                        ';
		theme();
		break;
	}

?>