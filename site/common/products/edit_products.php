<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once ('common/ShortUrl.php');
require_once('common/InStyle.php');

ini_set('memory_limit', '768M');

$appTable = 'products';

//https handling
$set->webAddress = ($set->isHttps?$set->webAddressHttps:$set->webAddress);

$max_upload = 15; // Max Upload Per Page

switch ($act) {
	case "edit_banner":
		$appTable = "merchants_creative";
		$mainBannerType="products";
		include("common/creatives/edit_banner.php");
		theme();
		break;
	
	case "add":
			$appTable = "merchants_creative";
			$mainBannerType="products";
			include("common/creatives/save_banner_case.php");
		_goto($set->SSLprefix.$set->basepage . '?act=products&id=' . $id . '&tab=creative_material&ty=1');
		break;
	
	case "save_banner":
		$appTable = "merchants_creative";
			include("common/creatives/edit_save_banner.php");
		_goto($set->SSLprefix.$set->basepage . '?act=edit_banner&id=' . $db['id'] . '&ty=1');
		break;
	
	case "showMail":
		$appTable = "merchants_creative";
		$db = dbGet($id, $appTable);
		// var_dump($db);
			// die();
			
			
		if ($db['type'] != "mail") die("ERROR");
		echo str_replace("{ctag}", $ctag,  $db['scriptCode']);
		die();
		break;
	
	case "showContent":
		$appTable = "merchants_creative";
		$db=dbGet($id,$appTable);
		if ($db['type'] != "content") die("ERROR");
		echo str_replace("{ctag}",$ctag,$db['scriptCode']);
		die();
		break;
	
	case 'upload_terms':

		$base_url =  "http".$set->SSLswitch."://" . $_SERVER['HTTP_HOST'];
		$terms_path = "/files/products/terms/";
		$dir = $_SERVER["DOCUMENT_ROOT"];
		 if (!is_dir($dir .'/files/products')) {
			 mkdir($dir .'/files/products');
		 }
		 if (!is_dir($dir .'/files/products/terms')) {
			 mkdir($dir .'/files/products/terms');
		 }
			//handle terms
	
			switch($terms){
			case "browse_terms":
						
							$randomFolder =mt_rand(10000000, 99999999);
							$terms_path = "/files/products/terms/tmp/" . $randomFolder . "/";
								$folder = $dir . '/files/products/terms/tmp/' . $randomFolder ."/";
								
								 if (!is_dir($dir .'/files/products/terms/tmp')) {
									 mkdir($dir .'/files/products/terms/tmp');
								 }
								 if (!is_dir($folder)) {
									 mkdir($folder);
								 }
								//echo $target_dir;die;
								$imageFileType = pathinfo($_FILES["terms_link_file"]["name"],PATHINFO_EXTENSION);
								$filename =  "terms_" . $id . ".". $imageFileType;
								$target_file = $folder . $filename;
							
								$uploadOk = 1;
								
							
								
								if($imageFileType != "html" && $imageFileType != "HTML") {
									$error =  "Sorry, only HTML file is allowed.";
									
									_goto($set->SSLprefix.$set->uri . "?error=". urlencode($error) . "&tab=terms_and_conditions");
								}
								else{
									
									if (move_uploaded_file($_FILES["terms_link_file"]["tmp_name"], $target_file)) {
										$ty =  "The file ". basename( $_FILES["terms_link_file"]["name"]). " has been uploaded.";
										$db['terms_and_conditions'] = $base_url . $terms_path . $filename; 
									} else {
										$error =  "Sorry, there was an error uploading your file.";
										_goto($set->SSLprefix.$set->uri . "?error=". urlencode($error) . "&tab=terms_and_conditions");
									}
								}
								break;
				
				case "html_terms":
							$dir = $_SERVER['DOCUMENT_ROOT'];
							$myfile = file_put_contents($dir. "/files/products/terms/terms_". $id .".html", $terms_link_html);
							$db['terms_and_conditions'] = $base_url . $terms_path . "terms_". $id .".html"; 
							break;
				default:
							//default - input case
							$db['terms_and_conditions'] = $terms_and_conditions;
							break;
			}
			
			$sql ="update products_items set terms_and_conditions='".$db['terms_and_conditions']."' where id='".$id."'";
			function_mysql_query($sql,__FILE__,__FUNCTION__);
		
			_goto($set->SSLprefix.$set->basepage.'?act=products&id='.$id .'&tab=terms_and_conditions');
		break;
		
	case "item_valid":
		$db=dbGet($id,$appTable.'_items');
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable.'_items',"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=item_valid&id='.$db['id'].'\',\'product_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "upload_banner":
	
		$item_id = $db['item_id'];
		if (!$db['title']) $errors['title'] = 1;
		if (empty($errors)) {
				unset($db['item_id']);
				$db['last_update'] = dbDate();

				if (chkUpload('file')) {
				
						$getOldBanner=dbGet($db['id'],"merchants_creative");
						if (file_exists($getOldBanner['file'])) ftDelete($getOldBanner['file']);
						
						$randomFolder =mt_rand(10000000, 99999999);
						$folder = 'files/products/tmp/' . $randomFolder ."/";
						 if (!is_dir('files/products/tmp')) {
							 mkdir('files/products/tmp');
						 }
						 if (!is_dir($folder)) {
							 mkdir($folder);
						 }
						
						$files = UploadFile('file','5120000','jpg,gif,swf,jpeg,png','',$folder);
						if(!is_array($files)){
									$db['file']  =$files;
									$exp=explode(".",$db['file']);
									$ext = strtolower($exp[count($exp)-1]);
									if ($ext == "swf") $db['type'] = "flash";
										else $db['type'] = "image";
										
									//$db['type'] = "product";
									$db['product_id'] = $item_id;
									list($db['width'],$db['height']) = getimagesize($db['file']);
									if (!$db['id']) $db['rdate'] = dbDate();
									if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
									dbAdd($db,"merchants_creative");
						}
						else{
							foreach ($files as $key=>$file){
									$db['file']  =$file;
									$exp=explode(".",$db['file']);
									$ext = strtolower($exp[count($exp)-1]);
									if ($ext == "swf") $db['type'] = "flash";
										else $db['type'] = "image";
										
									//$db['type'] = "product";
									$db['product_id'] = $item_id;
									list($db['width'],$db['height']) = getimagesize($db['file']);
									if (!$db['id']) $db['rdate'] = dbDate();
									if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
									
									dbAdd($db,"merchants_creative");
							}
						}
			}
			else{
					$db['last_update'] = dbDate();
				if($db['id']){
					$getOldBanner=dbGet($db['id'],"merchants_creative");
					$db['file'] = $getOldBanner['file'];
				}
					$db['product_id'] = $item_id;
					if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
					if($featured)
						$db['featured'] = 1;
					else
						$db['featured'] = 0;
					
					dbAdd($db,"merchants_creative");
			}			
		}
	_goto($set->SSLprefix.$set->basepage.'?act=products&id='.$item_id.'&tab=creative_material');
		break;
	case "item_add":

		if (!$db['title']) $errors['title'] = 1;
		if (!$db['url']) $errors['url'] = 1;
		if (empty($errors)) {
			
			 if($languages!=""){
				$arrLangs = explode(",",$languages);
				$db['languages'] = implode("|", $arrLangs);
			} 
			$item_id = $db['id'];
			//update url of all banners
			/* $q = "update merchants_creative set url =  '" . trim($db['url']). "' where product_id = ". $item_id;
			// var_dump($db);
			// die ($q);
			mysql_query($q); */
			
			$db[rdate] = dbDate();
			//$db[valid] = isset($db[valid])?1:0;
			/* if ($langs) $db['languages'] = '|'.implode('|',$langs).'|';
				else $db['languages'] = ''; */
				
				$randomFolder =mt_rand(10000000, 99999999);
				$folder = 'files/products/tmp/' . $randomFolder ."/";
				 if (!is_dir('files/products/tmp')) {
					 mkdir('files/products/tmp');
				 }
				 if (!is_dir($folder)) {
					 mkdir($folder);
				 }
			if(!isset($db['featured'])){
				$db['featured'] = 0;
			}
			if(!isset($db['isRTL'])){
				$db['isRTL'] = 0;
			}
			if (chkUpload('image')) $db['image'] = UploadFile('image','5120000','jpg,gif,jpeg,png','',$folder);
			dbAdd($db,$appTable.'_items');
			_goto($set->SSLprefix.$set->basepage.'?act=products&id='. $id.'&tab=basic_information');
			}
			else 
			{
				//var_dump($errors);
			}
			
			case "item_tech_add":

		// if (!$db['title']) $errors['title'] = 1;
		if (!$db['param']) $errors['param'] = 1;
		if (empty($errors)) {
			
			 if($languages!=""){
				$arrLangs = explode(",",$languages);
				$db['languages'] = implode("|", $arrLangs);
			} 
			$item_id = $db['id'];
			$id = $db['id'];
			//update url of all banners
	//		$q = "update merchants_creative set url =  '" . trim($db['url']). "' where product_id = ". $item_id;
			// var_dump($db);
			// die ($q);
			// mysql_query($q);
			
			$db[rdate] = dbDate();
			$db[ignoreOtherInternalParameters] = isset($db[ignoreOtherInternalParameters])?1:0;
			
			/* if ($langs) $db['languages'] = '|'.implode('|',$langs).'|';
				else $db['languages'] = ''; */
			if (chkUpload('image')) $db['image'] = UploadFile('image','5120000','jpg,gif,jpeg,png','','files/products/');
			dbAdd($db,$appTable.'_items');
			_goto($set->SSLprefix.$set->basepage.'?act=products&id='. $id.'&tab=basic_information');
			}
			else 
			{
				// echo 'error: <Br>';
				// var_dump($errors);
			}
	
	case "deal_add":
		$affiliate_id = retrieveAffiliateId($affiliate_id);
		$affiliate_id = is_numeric($affiliate_id)?$affiliate_id:'';
		$db['affiliate_id'] = $affiliate_id;
		
		dbAdd($db,$appTable.'_items');
		_goto($set->SSLprefix.$set->basepage.'?act=products&id=' .$id.'&tab=deal_type');
		break;
		
	case 'get_code':
	
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
					
					$db=dbGet($creative_id,"merchants_creative");
					$adminww    = dbGet($db['admin_id'], 'admins');
		
              
                
                
		if ($fParam) {
                    $freeParam = '-f' . $fParam;
                }
		if ($subid) {
                    $subid = '&subid=' . $subid;
                }
                
                //die('Test: ' . $affiliate_id);
				$tag = 'a' . $affiliate_id . '-b' . $creative_id . '-p' . $profile_id . $freeParam.$subid;  // Creat CTag.
				
		//		print_r($db);die;
				if ($db['type'] == "link") {
					$link = $set->webAddress.'click.php?ctag='.$tag;
					$code = '<!-- '.$set->webTitle.' Affiliate Code -->
							<a href="'.$link.'" target="_blank">'.$db['title'].'</a>
							<!-- // '.$set->webTitle.' Affiliate Code -->';
					$preview = '<a href="'.$link.'" target="_blank">'.$db['title'].'</a>';
					
				}  
				else if ($db['type'] == "mail" OR  strtolower($db['type']) == "content") {
                            $code = '<!-- '.$set->webTitle.' Affiliate Code -->
                    <!-- // '.$set->webTitle.' Affiliate Code -->';

                    if (strtolower($db['type']) == "content") {
                            $actType = 'showContent'; }
                     else {
                            $actType = 'showMail'; 
                     }
                    
					$preview = '<iframe src="'.$set->SSLprefix.'admin/creative.php'.'?act='.$actType.'&id='.$db['id'].'&ctag='.$tag.'" width="100%" height="500" frameborder="1" scrolling="no" zoom="50%"></iframe><br /><a href="'.$set->SSLprefix.'affiliate/creative.php'.'?act='.$actType.'&id='.$ww['id'].'&ctag='.$tag.'" target="_blank" style="font-size: 20px; font-weight: bold; color: green;">'.lang('Click here to'). ' '.lang('open in new window').'</a>';
					
                }
				else {
					
					
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
                                                                                    <input type="hidden" name="act" value="get_code" />
                                                                                    <input type="hidden" name="id" value="'.$id.'" />
																					<input type="hidden" name="creative_id" value="'.$creative_id.'" />
																					
																					
								<table class="normal" border="0" cellpadding="0" cellspacing="5" width="100%">
								<tr>
								<td valign="top" width="55%">
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
                                                                                    <tr><td></td>'. (strpos($db['file'],'/tmp/')? '':'<td><input type="submit" onclick="return getCodeValidation()" value="'.lang('Get Code').'" /></td>').'</tr>
                                                                                    </table>
																					
																					';
																					
																					if ($affiliate_id && !($db['type'] == "mail" || $db['type'] == "content")) {
                                                                                    $set->content .= '
																					<table>
																					<tr>
																					<td style="width:100px;" valign="middle">Javascript:</td>
																					<td><textarea cols="56" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.$code.'</textarea></td>
																					</tr>
																					
																					</table>'.$codelink .'';
                                                                                    
                                                                                } else if ($affiliate_id && ($db['type'] == "mail" || $db['type'] == "content")) {
																					
												$url = $set->webAddress . "admin/creative.php" . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag;
												$set->content .='<div class="blueText">'.($db['type'] == "mail" ? 'E-Mail' : ($db['type'] == "Content"? 'Content' :'Script')).' '.lang('Affiliated HTML Code').':</div><div><textarea name="db[scriptCode111]" cols="50" rows="8">'. str_replace('{ctag}',$tag,$db['scriptCode']).'</textarea></div>';
												$set->content .='<br><br><div class="blueText">'.lang('Direct Link').':&nbsp;&nbsp;&nbsp;<a href="admin/creative.php?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag . '" target="_blank" style="font-size: 15px;  color: green;">' . lang('Click here to') . ' ' . lang('open in new window') 	. '</a>
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
									$fullurl =  'http'.$set->SSLswitch.'://'.$_SERVER[HTTP_HOST] . '/facebookshare.php?q=' . $url.'&pta='.rawurlencode(time());
									$dispShorterLink =1 ;
									//die ($fullurl);
																
									}
										if ($dispShorterLink==0) {
										$set->content .= '<table><tr><td style="width:100px">'.lang('Facebook Share').':</td><td><textarea cols="45" rows="8">'.$set->webAddress.'facebookshare.php?brand='.rawurlencode($merchantww['name']).'&name='.rawurlencode($db['title']).'&image='.rawurlencode($db['file']).'&url='.rawurlencode(str_replace("ad.g","click.php",$link)).'&date='.rawurlencode($db['last_update']).'&pta='.rawurlencode(time()).'</textarea></td></tr></table>';
										} else {
										$set->content .= '<table><tr><td style="width:100px">'.lang('Facebook Share').':</td><td><textarea cols="45" rows="2">'.$fullurl.'</textarea></td></tr></table>';
									}
									}
									
																					
																				$set->content .='</td>
																				
																				<td width="45%" align="left" valign="top" style="text-align:center;">
								<div class="normalTableTitle">'.lang('Preview').':</div><br />
								'.$preview. '
							
																				</td>
																				</tr>
																				</table>
                                                                                  </form>'; 
		
								
									/*if($set->qrcode  and (($db['type'] == "link" or $db['type']=='image') && $db['file'] && $affiliate_id)){
									//$qr = new BarcodeQR();
									//$qr->url(str_replace("ad.g","click.php",$link),1);
									$qrCode = file_get_contents($set->webAddress."common/BarcodeQR.php?link=".$link);
									//die ($qrCode);
									$base64 = 'data:image/PNG;base64,' . base64_encode($qrCode );
								
										$set->content .= '<table><tr><td style="width:100px">'.lang('QR Code').'</td><td><img src='.$base64.' /></td></tr></table></div>';
									}*/
									
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
										$set->content .='<style>
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

										theme();
										break;
	case "products":
	
		$set->breadcrumb_title = lang('Edit Products');
		$set->pageTitle = '
		<script>
			$(".config_tabs").hide();
		</script>
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.$userLevel.'/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.$userLevel.'/products.php?act=products">'.lang('Products Place').'</a></li>
			<li><a href="'.$set->SSLprefix.$userLevel.'/edit_products.php?act=products&id='.$id.'" class="arrow-left">'.lang('Edit Product').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		$set->content = '
		<script type="text/javascript" src="'.$set->SSLprefix.'js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
			<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
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
		$set->content .='<style>
			  /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 60px;
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
			
		</style>';
		$set->content .='<style>		
		aside {
					width: 20%;
					background-color: #f1f1f1;
					position:fixed;
					height:100vh;
			
			}
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
			
			$("#filter_form").submit(function(){
				var tab_text = $("#config_filter").val();
				show_hide_tabs(tab_text,"search");
				return false;
			});
			
		});
		</script>
		<aside>
		<ul class="vertical">
  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">'. lang('Sections') .'</span><form onsubmit="return false;" style="display:inline-flex;float:right;">
  <div class="filter">'.lang('Find') .': </div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div></li>
  <li><a href="javascript:void(0)" data-tab="all">'. lang('All') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="basic_information" class="active">'. lang('Basic Information') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="technical_settings" class="active">'. lang('Technical Settings') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="deal_type">'. lang('Default Deal Type') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="creative_material">'. lang('Creative Material') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="pixels">'. lang('Pixels') .'</a></li>
   <li><a href="javascript:void(0)" data-tab="terms_and_conditions">'. lang('Terms and Conditions') .'</a></li>
  </ul></aside>
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
else{
		$set->content.="
			<script type='text/javascript'>
			$(document).ready(function(){
			var hash = 'basic_information';
			 if(hash != '')
			 {
				$('a[data-tab=\"basic_information\"]').click();
				$('a[data-tab=\"basic_information\"]').addClass('active');
			 }
			});
		</script>";
}
		$pixel_affiliate_id = isset($affiliate_id)?$affiliate_id:0;
		$pixel_affiliate_id = retrieveAffiliateId($pixel_affiliate_id);
		
		if ($id) {
			$db = dbGet($id,$appTable.'_items');
			$pageTitle = lang('Editing Product').': '.$db['title'] .  '&nbsp;&nbsp;&nbsp;['. $db['id'] .']'   ;
		} 
		
		
		//countries list
		function countriesList(){
				$sql = "SELECT * FROM countries where id>1";
				$strCountries   = function_mysql_query($sql,__FILE__,__FUNCTION__);
				while ($countries = mysql_fetch_assoc($strCountries)){
					$countriesArr[$countries['id']] = $countries['title'];
					$html .= '<option value="'.$countries['id'].'">'.$countries['title'].'</option>';
				}
				return $html;
		}
		
		
		$catqq=mysql_query("SELECT * FROM ".$appTable."_cats ORDER BY id ASC");
		while ($catww=mysql_fetch_assoc($catqq)) $catsList .= '<option value="'.$catww['id'].'" '.($catww['id'] == $db['cat_id'] ? 'selected="selected"' : '').'>'.$catww['title'].'</option>';
		
	
		//$set->pageTitle = 'Products';
		
		$lang_exp=explode("|",$db['languages']);
		$i=0;
		
		$langqq=mysql_query("SELECT * FROM languages WHERE valid='1' ORDER BY title ASC");
		$allLangOptions = "";
		while ($langww=mysql_fetch_assoc($langqq)) {
			$allLangOptions .= '<option value="'.$langww['id'].'">'.$langww['title'].'</option>';
		}
			
		$merqq=mysql_query("SELECT * FROM merchants WHERE 1=1 ORDER BY lower(name) ASC");
		$allMerchants = "";
		while ($langww=mysql_fetch_assoc($merqq)) {
			$allMerchants .= '<option value="'.$langww['id'].'"  '.($langww['id']==$db['merchant_id'] ? ' selected ' : '').'>'.$langww['name'].'</option>';
		}
			
		$allAffiliateArr = array();
		$pixelAffilates = "";
		
		$affiliateqq=mysql_query("SELECT * FROM affiliates WHERE valid='1' ORDER BY id ASC");
		if (!(isset($_GET['affiliate_id']) && !empty($_GET['affiliate_id']))) {
			$allAffiliates = '<option selected value="0">'.lang('Choose Affiliate').'</option>';
			$pixelAffilates = '<option selected value="0">'.lang('Choose Affiliate').'</option>';
		}
		
		while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {		   
		/*    if (isset($_GET['affiliate_id']) && !empty($_GET['affiliate_id'])) {
				$allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $affiliate_id ? 'selected' : '').'>['.$affiliateww['id'].'] '
								  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
		   } else { */
			
			$pixelAffilates .= '<option value="'.$affiliateww['id'].'"'.($pixel_affiliate_id == $affiliateww['id'] ? 'selected' : '').'>['.$affiliateww['id'].'] '
								  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
								  
		
				$allAffiliates .= '<option value="'.$affiliateww['id'].'"'.($pixel_affiliate_id == $affiliateww['id'] ? 'selected' : '').'>['.$affiliateww['id'].'] '
								  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
		   //}
		   $allAffiliateArr[$affiliateww['id']] = $affiliateww; 
		}
		
		if($pixel_affiliate_id != ""){
			
				$pixel_affiliate_data = $allAffiliateArr[$pixel_affiliate_id];
				//print_r($pixel_affiliate_data);die;
			
		}
		
		if ($id) {
			$banner=dbGet($banner_id,"merchants_creative");
			$l=0;
			
			if(isset($db['languages']) && $db['languages']!=""){
					$arrLangs = explode("|", $db['languages']);
					$db["languages"] = implode(",",$arrLangs);
			}
		include ('common/affiliate_status.php');
		$set->content .= '<form id="frmProductItem" method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="item_add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<input type="hidden" name="languages" id="languages"/>
						<div id="basic_information" data-tab="basic_information" class="config_tabs" style="display:none">
						<div class="normalTableTitle" data-tab2="basic_information" style="cursor: pointer;">'.$pageTitle.'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" class="tblDetails">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td colspan="3" height="5" class="errors" style="color:red;"></td></tr>
							<!--tr><td>'.lang('Active').'</td><td><label class="switch"><input type="checkbox" name="db[valid]" class="valid"  value=1 '. ($db['valid']?'checked':'') .'><div class="slider round"></div></label></td></tr-->
							
							';
								$statusTxt = "value='" . $db['valid'] ."'";
																				
											// $statusTxt = "value='" . lang('Active') ."'";
											$ulStatus = "User Status";
											if($db['valid'] == 0){
												// $statusTxt = "value='" . lang('Inactive') ."'";
												$ulStatus = lang('Inactive');
											}
											else if($db['valid'] == -1){
												// $statusTxt = "value='" . lang('Deleted') ."'";
												$ulStatus = lang('Deleted');
											}
											else if($db['valid'] == 2){
												// $statusTxt = "value='" . lang('Deleted') ."'";
												$ulStatus = lang('Pending');
											}
											else if($db['valid'] == 3){
												// $statusTxt = "value='" . lang('Rejected') ."'";
												$ulStatus = lang('PreLaunch');
											}
											else{
												$ulStatus = lang('Active');
											}
											

											$set->content .='<tr><td>'.lang('Active').'</td><td align="left">
												<div id="dd" class="wrapper-dropdown-3" tabindex="1">
													<span>'. $ulStatus .'</span>
													<ul class="dropdown_new">
														<li><a href="#" data-valid = 1>'. lang('Active') .'</a></li>
														<li><a href="#" data-valid = 2>'. lang('Pending') .'</a></li>
														<li><a href="#" data-valid = 3>'. lang('PreLaunch') .'</a></li>
														<li><a href="#" data-valid = 0>'. lang('Inactive') .'</a></li>
														<!--li><a href="#" data-valid = -1>'. lang('Deleted') .'</a></li-->
													</ul>
												</div>
												<input type="hidden" id="db_valid" name="db[valid]" '. $statusTxt .' /></td></tr>
												';
							$prodTypes = "
							<option value='Binary Option' ". ($db['type']=='Binary Option'?'selected':'') .">Binary Option</option>
							<option value='Forex' ". ($db['type']=='Forex'?'selected':'') .">Forex</option>
							<option value='Casino' ". ($db['type']=='Casino'?'selected':'') .">Casino</option>
							<option value='Bingo' ". ($db['type']=='Bingo'?'selected':'') .">Bingo</option>
							<option value='Sports' ". ($db['type']=='Sports'?'selected':'') .">Sports</option>
							<option value='Other' ". ($db['type']=='Other'?'selected':'') .">Other</option>
							";
							$set->content.='
							<tr><td align="left">'.lang('Product Name').'<span style="color:red">*</span>:</td><td><input  style="WIDTH: 12.2%;"  type="text" name="db[title]" id="title" value="'.$db['title'].'" /></td></tr>
							<tr><td align="left">'.lang('URL').':</td><td><input  style="WIDTH: 435px;" type="text" id="url" name="db[url]" value="'.$db['url'].'" /></td></tr>
							<tr><td align="left">'.lang('Category').':</td><td><select  style="WIDTH: 13%;"  name="db[cat_id]"><option value="">'.lang('Choose Category').'</option>'.$catsList.'</select></td></tr>
							<tr><td align="left">'.lang('Type').':</td><td><select  style="WIDTH: 15%;"  name="db[type]"><option value="">'.lang('Choose Type').'</option>'.$prodTypes.'</select></td></tr>
							<!--<tr><td align="left">'.lang('Affiliate').':</td><td><div class="ui-widget"><select id="combobox" name="db[affiliate_id]"><option value="">'.lang('Choose Affiliate').'</option>'.$allAffiliates.'</select></div></td></tr>
							<tr><td align="left">'.lang('CPA Amount Affiliate').':</td><td><input  style="WIDTH: 12.2%;"  type="text" name="db[cpaAmountAffiliate]" value="'.$db['cpaAmountAffiliate'].'" /></td></tr>
							<tr><td align="left">'.lang('CPA Amount').':</td><td><input  style="WIDTH: 12.2%;"  type="text" name="db[cpaAmount]" value="'.$db['cpaAmount'].'" /></td></tr>-->
							
							<tr><td align="left">'.lang('Merchant').':</td><td><select  style="WIDTH: 13%;"  name="db[merchant_id]"><option value="">'.lang('Choose Merchant').'</option>'.$allMerchants.'</select></td></tr>
							<tr><td align="left" valign="top">'.lang('Languages').':</td><td><!--<table><tr>'.$allLangs.'</tr></table>-->
							<select name="lang" requierd id="lang1" style="width: 100px;" multiple="multiple">'.$allLangOptions.'</select>
							</td></tr>
							<tr><td align="left" valign="top">'.lang('Description').':</td><td><textarea name="db[text]" cols="60" rows="6">'.$db['text'].'</textarea></td></tr>
							<tr><td align="left">'.lang('Logo').':</td><td>'.(strpos($db['image'],'/tmp/')?fileField('image','../images/wheel.gif'):fileField('image',$db['image'])).'</td></tr>
							<tr><td align="left">'. lang('Featured') .'</td><td><div><label class="switch" div style="float:left"><input type="checkbox" id="featured" name="db[featured]" value=1 '.($db['featured']==1 ? 'checked' : '').' ><div class="slider round"></div></label><div style="padding-top:6px"></div></td></tr>
							<tr><td align="left">'. lang('Is right to left layout') .'</td><td><div><label class="switch" div style="float:left"><input type="checkbox" id="isRTL" name="db[isRTL]" value=1 '.($db['isRTL']==1 ? 'checked' : '').' ><div class="slider round"></div></label><div style="padding-top:6px"></div></td></tr>
							<tr><td align="left">'.lang('Payment Terms').':</td><td><input  style="width:300px;"  type="text" name="db[terms]" value="'.$db['terms'].'" /></td></tr>
							<tr><td align="left">'.lang('Countries Allowed').':</td><td><select name="allowedCountries" id="allowedCountries" style="width: 100px;" multiple="multiple">'. countriesList() .'
							</select>
							<input type="hidden" name="db[countries_allowed]" id="selected_countries"/>
							</td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</div></div>
						</form>
						<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
					<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
						<script>
						$(document).ready(function(){
							$("#url").blur(function() {
						  var input = $(this);
						  var val = input.val();
						  if (val && !val.match(/^http([s]?):\/\/.*/)) {
							input.val("http'.$set->SSLswitch.'://" + val);
						  }
						});
						
						$("#frmProductItem").on("submit",function(){
							if($("#title").val()=="" || $("#param").val() =="")
							{
								$(".errors").html("'. lang("Missing required fields.") .'").show();
								return false;
							}
							else{
								$(".errors").hide();
								return true;
							}
						});
						
						$("#allowedCountries option[value=\'-\']").remove();
						$("#allowedCountries").multipleSelect({
									width: 200,
									placeholder: "'.lang('Select Country').'"
								});
						$("#allowedCountries").change(function(){
							$("#selected_countries").val($(this).val());
						});
						
						var selects = "'. $db['countries_allowed'] .'";
						
						$("#allowedCountries").multipleSelect("setSelects",[  '. $db['countries_allowed'] .' ]);
						
						});
						</script>
						';
							
							
		$set->content .= '<form id="frmProductItem" method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="item_tech_add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<div id="technical_settings" data-tab="technical_settings" class="config_tabs" style="display:none">
						<div class="normalTableTitle" data-tab2="technical_settings" style="cursor: pointer;">'.lang('Technical Settings').'</div>
						<div align="left" style="background: #EFEFEF;">
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" class="tblDetails">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td colspan="3" height="5" class="errors" style="color:red;"></td></tr>
						
							<tr><td align="left">'.lang('Tracking Parameter Name').'<span style="color:red">*</span>:</td><td><input  style="WIDTH: 12.2%;" type="text" name="db[param]" id="param" value="'.$db['param'].'" /></td></tr>
							<tr><td align="left">'.lang('Product API Code').':</td><td><input  style="WIDTH: 12.2%;" type="text" name="db[productAPICode]" id="productAPICode" value="'.$db['productAPICode'].'" /></td></tr>
							<tr><td align="left">'.lang('Push Unique ID with name (not mandatory)').'<span style="color:red"></span>:</td><td><input  style="WIDTH: 12.2%;" type="text" name="db[exportUniqueIdWithName]" id="exportUniqueIdWithName" value="'.$db['exportUniqueIdWithName'].'" /></td></tr>
							<tr><td>'.lang('Ignore other internal parameters').'</td><td><label class="switch"><input type="checkbox" name="db[ignoreOtherInternalParameters]" class="valid"  value=1 '. ($db['ignoreOtherInternalParameters']?'checked':'') .'><div class="slider round"></div></label></td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</div></div>
						</form>
						<script>
						$(document).ready(function(){
							$("#url").blur(function() {
						  var input = $(this);
						  var val = input.val();
						  if (val && !val.match(/^http([s]?):\/\/.*/)) {
							input.val("http'.$set->SSLswitch.'://" + val);
						  }
						});
						
						$("#frmProductItem").on("submit",function(){
							if($("#title").val()=="" || $("#param").val() =="")
							{
								$(".errors").html("'. lang("Missing required fields.") .'").show();
								return false;
							}
							else{
								$(".errors").hide();
								return true;
							}
						});
						});
						</script>
						';
						
			$set->content .='
						<div id="deal_type" data-tab="deal_type" class="config_tabs" style="display:none">
						<div class="normalTableTitle" data-tab2="deal_type" style="cursor: pointer;">'.lang('Default Deal Type').'</div>
						<div align="left" style="background: #EFEFEF;">
						<form method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="item_add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" class="tblDetails">
							<tr><td colspan="3" height="5"></td></tr>
							<!--tr><td align="left">'.lang('Affiliate').':</td><td><div class="ui-widget"><select id="combobox" name="db[affiliate_id]"><option value="">'.lang('Choose Affiliate').'</option>'.$allAffiliates.'</select></div></td></tr-->
							<tr><td colspan=3 height="5">
							<table class="normal" style="width:98%">
							<thead>
							<tr><td>'. lang('Cost Per Installation') .'</td><!--<td>'. lang('Cost Per Click') .'</td>--><td>'. lang('Cost Per Lead') .'</td><td>'. lang('Cost Per Account') .'</td><td>'. lang('Cost Per Sale') .'</td></tr></thead>
							<tr>
							<td align="center">$<input  style="width: 150px;"  type="text" name="db[cpi]" value="'.$db['cpi'].'" /></td>
						<!--	<td align="center">$<input  style="width: 150px;"  type="text" name="db[cpc]" value="'.$db['cpc'].'" /></td> -->
							<td align="center">$<input  style="width: 150px;"  type="text" name="db[cpllead]" value="'.$db['cpllead'].'" /></td>
							<td align="center">$<input  style="width: 150px;"  type="text" name="db[cplaccount]" value="'.$db['cplaccount'].'" /></td>
							<td align="center">$<input  style="width: 150px;"  type="text" name="db[cpa]" value="'.$db['cpa'].'" /></td>
							</tr></table></td></tr>
							<tr><td align="left" colspan=3 height=5><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table></form>
						</div></div>';
			$set->content .='<div id="creative_material" data-tab="creative_material" class="config_tabs" style="display:none">';
			if ($id) $where .= " AND product_id='".$id."'";
			if ($type) $where .= " AND type='".$type."'";
			if ($lang) $where .= " AND language_id='".$lang."'";
			if ($promotion) $where .= " AND promotion_id='".$promotion."'";
			if ($category_id) $where .= " AND category_id='".$category_id."'";
			if ($width) $where .= " AND width='".$width."'";
			if ($height) $where .= " AND height='".$height."'";
			if ($q) $where .= " AND (lower(title) LIKE '%".strtolower($q)."%' OR id='".$q."')";
			if ($creativedimenstion<>'')  {
			$spltA = explode("X",$creativedimenstion);
				$where .= " AND width='".$spltA[0]."' AND height='".$spltA[1]."'";
			}
			
			$isDownloadSession = false;
			if (isset($_GET['download']) && strlen($_GET['download'])>0) {
					$isDownloadSession = true;
			}
			$appTable = 'merchants_creative';
			$getPos = $set->itemsLimit;
			$pgg=$_GET['pg'] * $getPos;
			if ($isDownloadSession) 
				$creativeQry  = "SELECT * FROM ".$appTable." WHERE type not like 'coupon' and product_id>0 and product_id='".$id."' ".$where." ORDER BY id DESC";
			else
				$creativeQry  = "SELECT * FROM ".$appTable." WHERE type not like 'coupon' and product_id>0 and product_id='".$id."' ".$where." ORDER BY id DESC LIMIT $pgg,$getPos";
			
			$qqCreatives = function_mysql_query($creativeQry);
			$bottomNav = GetPages($appTable,"WHERE merchant_id='".$merchant_id."' ".$where,$pg,$getPos);
			//$qq=mysql_query("SELECT * FROM merchants_creative WHERE  and  product_id='".$db['id']."' and valid >= 0 ORDER BY id DESC");
			while ($ww=mysql_fetch_assoc($qqCreatives)) {
				$l++;
					if (!empty($ww['file']))
					$fileArrays[] = $ww['file'];
				
				list($width1,$height1) = @getimagesize($ww["file"]);
				$totalTraffic = mysql_fetch_assoc(mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE product_id='". $db['id'] ."' and banner_id='".$ww['id']."'"));
				$allCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td><span class="dimantion-wrap" style="display:none">'.$width1 .'x'. $height1 .'</span>'.$ww['id'].'</td>
								<td>
								<!--a href="'.$set->SSLprefix.$set->basepage.'?act=products&id='.$ww['product_id'].'&banner_id='.$ww['id'].'&tab=creative_material">'.lang('Edit').'</a-->
								<a href="'.$set->webAddress. ltrim($set->basepage, '/').'?act=edit_banner&id='.$ww['id'].'" class="inline">'.lang('Edit').'</a>
								&nbsp;|&nbsp;<a style="cursor:pointer;" data-bannerid = '. $ww['id'] .' class="delete_banner">'.lang('Delete').(strpos($ww['file'],'/tmp/')?'':'&nbsp;|&nbsp;<a href="'.$set->webAddress. ltrim($set->basepage, '/').'?act=get_code&id='.$ww['product_id'].'&creative_id='.$ww['id'].'" class="inline">'.lang('Get Tracking Code').'</a>').'</td>
								<td>'.$ww['title'].'</td>'
								.(strpos($ww['file'],'/tmp/')?'<td align="center" class="tooltip">'.($ww['type'] == "image" || $ww['type'] == "flash" ? '<img src="../images/wheel.gif" width=28 height=28><span class="tooltiptext" style="padding-bottom:15px">'. lang("System is checking for virus. Please refresh in a minute.") .'</span>' : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>':
								'<td align="center" class="img-wrap">'.($ww['type'] == "image" || $ww['type'] == "flash" ? getFixedSizeBanner($ww['file'],50,50) : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>').'
								<td align="center">'.$ww['type'].'</td>
								<td align="center">'.($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</td>
								<td align="center">'.number_format($totalTraffic['totalViews'],0).'</td>
								<td align="center">'.number_format($totalTraffic['totalClicks'],0).'</td>
								<td align="center" id="bbnr_'.$ww['id'].'">
									<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid_banner&id='.$ww['id'].'\',\'bbnr_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a>
									'.($delete ? '<a href="'.$set->SSLprefix.$set->basepage.'?act=del_banner&id='.$ww['id'].'">.</a>' : '').'
								</td>
							</tr>';
				}
			
			
			
			$set->content .= getImageGrowerScript();
				
			if ($isDownloadSession) {
			
			$isDownloadSession=false;
				$file_names = $fileArrays ; //array('iMUST Operating Manual V1.3a.pdf','iMUST Product Information Sheet.pdf');

				//Archive name
				// $archive_file_name=$name.'iMUST_Products.zip';

				//Download Files path
				// $file_path=$_SERVER['DOCUMENT_ROOT'];//.$set->ftp_path ;// '/Harshal/files/';
				// die($file_path);
				
				// zipFilesAndDownload($file_names,$archive_file_name,$file_path);
				$result = create_zip($file_names,'files/bannersPackage.zip');
				//create_zip($file_names,$archive_file_name,$file_path);

	
		}
		
		
			
			$set->content .= '
			<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
			<script>
			$(document).ready(function(){
			$(".delete_banner").on("click", function(){
				
				banner_id = $(this).data("bannerid");
			
			$.prompt("'.lang('Are you sure you want to delete this banner?').'", {
					top:200,
					title: "'.lang('Delete Banner').'",
					buttons: { "Yes": true, "Cancel": false },
					submit: function(e,v,m,f){
						if(v){
							var url = "'.$set->SSLprefix.$userLevel.'/edit_products.php?act=del_banner&id="+ banner_id;
							window.location.href= url;
						}
						else{
						}
					}
				});
			});
			});
			</script>
			';
			if(isset($banner_id) && !empty($banner_id)) : 
			$bannerSectionTitle = lang('Edit'); 
			$uploadText = lang('Save');
			else: 
			$bannerSectionTitle = lang('Add New'); 
			$uploadText = lang('Upload');
			endif;
			
			$langsArray = array();
		  $qry = "select distinct language_id as  language_id from  merchants_creative where valid=1";//.$where;
		  $qq=function_mysql_query($qry,__FILE__);
		  while ($ww=mysql_fetch_assoc($qq)) {
		   $langsArray[]=$ww['language_id'];
		  }
		  
		$listOfActiveLangs = listLangs($lang,0,$langsArray);
			/* $set->content .= '
			<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="act" value="upload_banner" />
				<input type="hidden" name="db[id]" value="'.$banner['id'].'" />
				<input type="hidden" name="db[item_id]" value="'.$id.'" />
				<input type="hidden" name="db[url]" value="'.$db['url'].'" />
				<div id="creative_material" data-tab="creative_material" class="config_tabs" style="display:none">
				<div data-tab2="creative_material" class="normalTableTitle" style="cursor: pointer;">'.$bannerSectionTitle.lang(' Creative Material').'</div>
				<div align="left" style="background: #EFEFEF;">
				<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" class="tblDetails">
				<tr><td align="left">'.lang('Title').':</td><td><input type="text" name="db[title]" value="'.$banner['title'].'" /></td></tr>
				<tr><td align="left">'.lang('Type').':</td><td>
				<select name="db[type]">
				<option value="">'. lang('Select') .'</option>
				<option value="link" '. ($banner['type']=='link'?'selected':'') .'>'.lang('Text Link').'</option>
				<option value="image" '. ($banner['type']=='image'?'selected':'') .'>'.lang('Banner').'</option>
				</select>
				</tr>
				<tr><td align="left">'.lang('Language').':</td><td><select name="db[language_id]"><option value="">'. lang('Select') .'</option>'. listLangs($db['language_id']) .'</select></tr>
				<tr><td align="left">'.lang('File').':</td><td>
					'.(isset($banner['id'])?'<input type="file" name="file"/>':'<input type="file" name="file[]" multiple=true/>') . (isset($banner['file'])?'<a href="'. $banner['file'] .'" target="_blank">Preview</a>':'') .'
				</td></tr>
				<tr><td>'.lang('Publish').'</td><td><label class="switch"><input type="checkbox" name="valid" class="valid"  value=1 '. ($banner['valid']?'checked':'') .'><div class="slider round"></div></label></td></tr>
				<tr><td align="left">'. lang('Featured') .'</td><td><div><label class="switch" div style="float:left"><input type="checkbox" id="featured" name="featured" value=1 '.($banner['featured']==1 ? 'checked' : '').' ><div class="slider round"></div></label><div style="padding-top:6px"></div></td></tr>
				<tr><td></td><td align="left"><input type="submit" value="'.$uploadText.'" /></td></tr>
				</table>
			</form> */
			
			
			$wwproduct = mysql_fetch_assoc(function_mysql_query("select title from products_items where id=". $id ,__FILE__));
			$product_name = $wwproduct['title'];
			$mainBannerType = "products";
			
			include('common/creatives/add_banner_form.php');
			
			$set->content .='<hr />
			
			
			<div class="normalTableTitle" style="cursor: pointer;">'.lang('Creatives List').'</div>';
			$appTable = "merchants_creative";
			$mainBannerType = "products";
			include("common/creatives/banner_filters.php");
			$set->content.='
			<table width="100%" border="0" cellpadding="2" cellspacing="0" bgcolor="#EFEFEF" class="normal">
				<thead>
					<tr>
						<td>#</td>
						<td>'.lang('Options').'</td>
						<td>'.lang('Creative Name').'</td>
						<td>'.lang('Preview').'</td>
						<td>'.lang('Format').'</td>
						<td>'.lang('Size').' ('.lang('Width').' x '.lang('Height').')</td>
						<td>'.lang('Impressions').'</td>
						<td>'.lang('Clicks').'</td>
						<td>'.lang('Active').'</td>
					</tr></thead><tfoot>'.$allCreative.'</tfoot>
			</table>
		
		<div align="left" style="padding: 5px;">'.$bottomNav.'</div></div>';
		if ($result) $set->content .= '<script type="text/javascript">
			$(document).ready(function(){
				var scrollToElement = $("#formSearch");
					$(\'html,body\').animate({scrollTop:(scrollToElement.offset().top-210)}, 0);
				});
			</script>';
			}
			
			/*****************PIXEL SECTION***********************/
			// die (md5(trim($db['randomKey'])));
			$set->content .='
						<div id="pixels" data-tab="pixels" class="config_tabs" style="display:none">
						<div class="normalTableTitle" data-tab2="pixels" style="cursor: pointer;">'.lang('Pixels').'</div>
						<div align="left" style="background: #EFEFEF;">
						<form action="'.$set->SSLprefix.$userLevel.'/edit_products.php?act=products&id='. $id .'&tab=pixels" method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="products" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" class="tblDetails">';
							//if(isset($pixel_affiliate_data)){
								$token = $id . "-". $db['randomKey'] ."-". md5($db['randomKey']);
							$set->content .= '<table class="normal" style="width:98%">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td>'. lang('Please implement these postback pixels  on the relevant pages for tracking.') .'</td></tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td><div style="float:left;padding-top:5px;min-width:100px">'.lang('Lead').'</div><div style="float:left"><label class="switch"><input type="checkbox" name="leadPixel" value="lead" class="pixel_data"><div class="slider round"></div></label></div></td></tr>
							<tr style="display:none;" class="pixelLeadStr"><td><textarea style="width:900px" name="pixelLeadVal">'. $set->webAddress .'postback.php?token='. $token .'&type=lead&internal_id={internal_id}&btag={btag}&email={email}&fname={fname}&country={2isoCountryCode}</textarea></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td><div style="float:left;padding-top:5px;min-width:100px">'.lang('Account').'</div><div style="float:left"><label class="switch"><input type="checkbox" name="accountPixel"  value="account"class="pixel_data"><div class="slider round"></div></label></div></td></tr>
							<tr style="display:none;" class="pixelAccountStr"><td><textarea style="width:900px" name="pixelAccountVal">'. $set->webAddress .'postback.php?token='. $token .'&type=account&internal_id={internal_id}&btag={btag}&email={email}&fname={fname}&country={2isoCountryCode}&trader_id={trader_id}&merchant_id={merchant_id}</textarea></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td><div style="float:left;padding-top:5px;min-width:100px">'.lang('Sale').'</div><div style="float:left"><label class="switch"><input type="checkbox" name="salePixel" class="pixel_data" value="sale"><div class="slider round"></div></label></div></td></tr>
							<tr style="display:none;" class="pixelSaleStr"><td><textarea style="width:900px" name="pixelSaleVal">'. $set->webAddress .'postback.php?token='. $token .'&type=sale&btag={btag}&internal_id={internal_id}&trader_id={trader_id}&merchant_id={merchant_id}&usdamount={usdamount}</textarea></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td><div style="float:left;padding-top:5px;min-width:100px">'.lang('Installation').'</div><div style="float:left"><label class="switch"><input type="checkbox" name="installationPixel" class="pixel_data" value="installation"><div class="slider round"></div></label></div></td></tr>
							<tr style="display:none;" class="pixelInstallationStr"><td><textarea style="width:900px" name="pixelInstallationVal">'. $set->webAddress .'postback.php?token='. $token .'&type=installation&btag={btag}&internal_id={internal_id}&trader_id={trader_id}&merchant_id={merchant_id}&usdamount={usdamount}</textarea></td></tr>
							</tr>
							</table>';
							//}
							$set->content .='</td></tr>
							<!--tr><td align="left" colspan=3 height=5><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr-->
						</table>
						</form>
						</div></div>';
			$set->content .="<script>
				$(document).ready(function(){
					$('.pixel_data').change(function(){
						if($('input[name=\'leadPixel\']').is(':checked')){
							$('.pixelLeadStr').show();
						}
						else{
							$('.pixelLeadStr').hide();
						}
						if($('input[name=\'accountPixel\']').is(':checked')){
							$('.pixelAccountStr').show();
						}
						else{
							$('.pixelAccountStr').hide();
						}
						if($('input[name=\'salePixel\']').is(':checked')){
							$('.pixelSaleStr').show();
						}
						else{
							$('.pixelSaleStr').hide();
						}
						if($('input[name=\'installationPixel\']').is(':checked')){
							$('.pixelInstallationStr').show();
						}
						else{
							$('.pixelInstallationStr').hide();
						}
					});
				});
			</script>";
			
			$set->content .= '<script>
						$("#lang1 option[value=\'\']").remove();
						$("#lang1").multipleSelect({
									width: 200,
									placeholder: "Select Languages"
								});
						$("#lang1").change(function(){
							$("#languages").val($(this).val());
						}); 
						
						var selects = "'. $db['languages'] .'";
						
						$("#lang1").multipleSelect("setSelects",[  '. $db['languages'] .' ]);
				</script>';
				
			$set->content .='
			<div id="terms_and_conditions" data-tab="terms_and_conditions" class="config_tabs" style="display:none">
		<div class="normalTableTitle" style="cursor: pointer;" data-tab2="terms_and_conditions">'.lang('Terms and Conditions').'</div>
		<form method="POST" name="termsDetailsForm" enctype="multipart/form-data">
		<input type="hidden" name="act" value="upload_terms">
		<input type="hidden" name="product_id" value="'. $id .'">
		<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0" class="tblTermsDetails" >
			<tr><td width=200 style="padding:10px 0">'.lang('Current Terms & Conditions URL').': </td><td><b>'.(strpos($db['terms_and_conditions'],"/tmp/")? '<span style="color:red"><strong><i>' . lang('System is looking for viruses. Please refresh the page in a minute.') .'</i></strong></span>':'<a href= "'. (strpos($db['terms_and_conditions'],"/tmp/")?'':$db['terms_and_conditions']) .'" target="_blank">'. (strpos($db['terms_and_conditions'],"/tmp/")?lang('System is looking for viruses. Please refresh the page in a minute.'):$db['terms_and_conditions']) .'</a>').'</b></td></tr>
			<tr><td colspan="2" height="5"></td></tr>
			<tr>
			<td colspan=2 align="left">'.lang('Terms & Conditions').':</td>
			</tr>
			<tr>
				<td colspan=2>
				<div style="width:50px;float:left;"><input type="radio" name="terms" value="input_terms"/></div>
				<div style="float:left;"><input type="text" name="terms_and_conditions"  style="width: 250px;" value="'.(strpos($db['terms_and_conditions'],"/tmp/")?'':($db['terms_and_conditions'] ? $db['terms_and_conditions'] : 'http'.$set->SSLswitch.'://')).'"/></div>
				<div style="clear:both;height:10px"></div>
				<div style="width:50px;float:left;"><input type="radio" name="terms" value="browse_terms"/></div>
				<div style="float:left;">'. fileField('terms_link_file',(strpos($db['terms_and_conditions'],"/tmp")?'../images/wheel.gif':$db['terms_and_conditions'])) .'<span style="padding:0 0 0 10px">('.lang('HTML file only').')</span></div>
				<div style="clear:both;height:10px"></div>
				<div style="width:50px;float:left;"><input type="radio" name="terms" value="html_terms"/></div>
				<div style="float:left;"><textarea name="terms_link_html" id="contentTerms" cols="80" rows="40">'.  (strpos($db['terms_and_conditions'],"/tmp/")?'':(!empty($db['terms_and_conditions']) ? @file_get_contents($db['terms_and_conditions']) : "")) .'</textarea></div>
				</td>
			</tr>
			<tr><td colspan="2" height="20"></td></tr>
			<tr><td></td><td><input type="submit" value="'.lang('Save').'" /> </td></tr>
		</table>
		</form>
		</div>
		<link rel="stylesheet" type="text/css" href="'.$set->SSLprefix.'css/jquery.cleditor.css" />
		<script type="text/javascript" src="'.$set->SSLprefix.'js/jquery.cleditor.js"></script>
		<script>
		$(document).ready(function(){
			e = $("#contentTerms").cleditor({
				width:        800,
				height:       400,
				});
				e.change(function(){
									$("input:radio[name=terms]").attr("checked",true);
								});	
		});
		</script>
	';
				
				$set->content.="</div>";
		theme();
		break;

	/* ------------------------------------ [ Manage products ] ------------------------------------ */
	case "del_banner":
		$ww=dbGet($id,"merchants_creative");
		if (file_exists($ww['file'])) ftDelete($ww['file']);
		mysql_query("update  merchants_creative set valid=-1 WHERE id='".$id."'");
		//die('<a href="'.$set->basepage.'?act=products&id='.$ww['product_id'].'">'.lang('Go Back').'!</a>');
		_goto($set->SSLprefix.$set->basepage.'?act=products&id='.$ww['product_id'] .'&tab=creative_material');
		break;
		
	case "valid_banner":
		$db=dbGet($id,"merchants_creative");
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit("merchants_creative","valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid_banner&id='.$db['id'].'\',\'bbnr_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "valid":
		$db=dbGet($id,$appTable.'_cats');
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable.'_cats',"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'product_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	/* case "add":
		if (!$db['title']) $errors['title'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			$db[valid] = 0;
			dbAdd($db,$appTable.'_cats');
			_goto($set->basepage);
			}
			 */
			
			
			case "delete":
		
		if ($id>0)
		{
		
			$qry = "delete from ". $appTable."_cats where id = " . $id ;
			// die ($qry);
			mysql_query($qry) ; 
			_goto($set->basepage);
		}
	break;	
			
			

	default:
		$pageTitle = 'Manage Product Categories';
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.$userLevel.'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$qry = "SELECT * FROM ".$appTable."_cats ORDER BY id ASC";
		// die($qry);
		$qq=mysql_query($qry);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$productList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td>'.$ww['id'].'</td>
						<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?id='.$ww['id'].'">'.lang('Edit').'</a><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><a href="'.$set->SSLprefix.$set->basepage.'?act=delete&id='.$ww['id'].'">'.lang('Delete').'</a></td>
						<td>'.$ww['title'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center" id="product_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'product_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a>
						
						</td>
					</tr>';
			}
		if ($id) {
			$db = dbGet($id,$appTable.'_cats');
		$set->content = '<form method="post">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<div class="btn"><a href="'.$set->basepage.'">'.lang('Add New').'</a></div>
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Edit Product Category').'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">'.lang('Category Name').':</td><td><input type="text" name="db[title]" value="'.$db['title'].'" '.($errors['title'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Product Categories List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td align="center">'.lang('Options').'</td>
								<td>'.lang('Product Name').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Available').'</td>
							</tr></thead><tfoot>'.$productList.'</tfoot>
						</table>';
						
					
						
		}else {
			
				$set->content = '<form method="post">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Product').'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">'.lang('Product Name').':</td><td><input type="text" name="db[title]" value="'.$db['title'].'" '.($errors['title'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Products List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td align="center">'.lang('Options').'</td>
								<td>'.lang('Product Name').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Available').'</td>
							</tr></thead><tfoot>'.$productList.'</tfoot>
						</table>';
		}
		
		
	
		
		
		theme();
		break;
	}

?>