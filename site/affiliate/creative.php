<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
require_once ('common/ShortUrl.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);


$appTable = 'merchants_creative';
$appProm = 'merchants_promotions';

$ll = isset($_GET['ll']) ? $_GET['ll'] : "";

//https handling
$set->webAddress = ($set->isHttps?$set->webAddressHttps:$set->webAddress);


switch ($act) {
	
	case "get_code":
		$appTable = 'merchants_creative';
		$creativetype = "creative";
		include('common/getTrackingCode.php');
                $set->print = 1;
		theme();
		break;
	

	default:
		$pageTitle = lang('Marketing Tools');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).' - Creative materials</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$numqq=function_mysql_query("SELECT id FROM merchants WHERE valid='1'",__FILE__);
		
		
		


		$merchantid = '';
		if (mysql_num_rows($numqq) == "1") {
			$numww = mysql_fetch_assoc($numqq);
			$_GET['merchant'] = $numww['id'];
			$merchantid = $_GET['merchant'];
			
			}
		
		if (!$set->showAllCreativesToAffiliate) {
		$inMerchants = str_replace('|',',',$set->userInfo['merchants']);
		$merchantid = $inMerchants;
		$inMerchants=ltrim($inMerchants,',');
		
		$inMerchants = empty($inMerchants) ? 0 : $inMerchants;
		
		$where.=" AND merchant_id IN (".$inMerchants.") ";
		}
		else {
			$qry = "select id from merchants where valid = 1";
			$rslt = function_mysql_query($qry,__FILE__);
			$merchantid = -1;
			while ($row=mysql_fetch_assoc($rslt)) {
				$merchantid .= ',' . $row['id'];
			}
		}
	$merchantid = empty($merchantid) ? 0 : $merchantid;
		
		
	$merchantid=ltrim($merchantid,',');
	
	
$typesArray = array();
	$qry = "select distinct type as type from  ".$appTable." WHERE product_id=0 and  merchant_id in (".$merchantid.") ";//.$where;
	// die ($qry);
	$qq=function_mysql_query($qry,__FILE__);
	while ($ww=mysql_fetch_assoc($qq)) {
		$typesArray[]=strtolower($ww['type']);
			
		}
	$langsArray = array();
		$qry = "select distinct language_id as  language_id from  ".$appTable." WHERE  product_id=0 and   merchant_id in (".$merchantid.") ";//.$where;
		// die($qry);
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$langsArray[]=$ww['language_id'];
		}
	
			$comboq = "SELECT count(CONCAT(width, 'x', height)) as count, CONCAT(width, ' X ', height) as dim,`merchants_creative`.height ,`merchants_creative`.width FROM `merchants_creative` WHERE product_id = 0 and merchant_id in ( ".$merchantid.") and  `merchants_creative`.height>0 and `merchants_creative`.width>0 and `merchants_creative`.valid =1
			group by CONCAT(width, ' X ', height) order by height, width";
	// die ($comboq);
			$combolist = '<select name="creativedimenstion"><option value="">'.lang('Show All').'</option>';
			$qqcombo=function_mysql_query($comboq,__FILE__);
				while ($wwcombo=mysql_fetch_assoc($qqcombo)) {
				$combolist.= '<option value="'.str_replace(' ','',$wwcombo['dim']) .'">'. $wwcombo['dim'] . '  (' . $wwcombo['count']. ')'.'</option>';
				}
			$combolist.='</select>';
			
			
		if ($_GET OR 1) {
			if ($merchant) $where .= " AND merchant_id='".$merchant."'";
			if ($type) $where .= " AND type='".$type."'";
			if ($lang) $where .= " AND language_id='".$lang."'";
			 $where .= " AND product_id=0 ";
			
			if ($promotion) $where .= " AND promotion_id='".$promotion."'";
			if ($category_id) $where .= " AND category_id='".$category_id."'";
			if ($width) $where .= " AND width='".$width."'";
			if ($height) $where .= " AND height='".$height."'";
			if ($q) $where .= " AND (lower(title) LIKE '%".strtolower($q)."%' OR id='".$q."')";
			//if ($merchant__id>0) $where .= " AND merchant_id = ".($merchant__id);
					if ($creativedimenstion<>'')  {
		$spltA = explode("X",$creativedimenstion);
			$where .= " AND width='".$spltA[0]."' AND height='".$spltA[1]."'";
		}
		// die ($where);
		
			
		//	$qry = "SELECT mc.* FROM ".$appTable." mc WHERE valid='1' ".$where." ORDER BY id DESC";
			$affiliatesPromotionQry ="select id from merchants_promotions where affiliate_id in (". $set->userInfo['id']. ") or (group_id=". $set->userInfo['group_id'] ." ) OR (additional_affiliates LIKE '%|".$set->userInfo['id']."|%');";
			//die ($affiliatesPromotionQry);
			$qq=function_mysql_query($affiliatesPromotionQry,__FILE__);
			
			$affiliatesPromotion = "-1,0";
			while ($ww=mysql_fetch_assoc($qq)) {
				$affiliatesPromotion .= ',' . $ww['id'];	
			}
				
			$affiliatesPromotion = empty($affiliatesPromotion) ? 0 : $affiliatesPromotion;
			
			$getPos = 50;
			$pgg=$_GET['pg'] * $getPos;
			
			$selectFields = "mc.id, mc.promotion_id, mc.type,mc.file,mc.title,mc.url, mc.category_id, mc.language_id, mc.rdate, mc.last_update,mc.merchant_id,mc.product_id,mc.valid,mc.width,mc.height";
			$sql = "select * from (SELECT 1 as prior, ". $selectFields ." FROM merchants_creative mc inner join merchants on mc.merchant_id = merchants.id and merchants.valid=1 WHERE mc.product_id=0 and mc.valid='1' and mc.promotion_id in (".$affiliatesPromotion.")
						union all
						select 0 as prior, ". $selectFields  ." from merchants_creative mc  inner join merchants on mc.merchant_id = merchants.id and merchants.valid=1 where mc.product_id=0 and mc.valid = '1' and mc.promotion_id not in (".$affiliatesPromotion.") and mc.promotion_id = 0 and mc.file NOT LIKE '%tmp%' and mc.affiliateReady = 1) as a 
						where 1=1 ".$where. " order by prior desc ,id desc";

			$limit = (!empty($ll) ? ' limit '.$ll : ' LIMIT ' . $pgg . ',' . $getPos);
			 $qry = $sql . $limit;
			
	 /* 
			$qry = "select * from (SELECT 1 as prior, mc.promotion_id, mc.merchant_id,mc.id,mc.title,mc.file,mc.type,mc.category_id,mc.width,mc.height,mc.product_id,mc.rdate,mc.last_update,mc.language_id
 FROM merchants_creative mc inner join merchants on mc.merchant_id = merchants.id and merchants.valid=1 WHERE mc.product_id=0 and mc.valid='1' and mc.promotion_id in (".$affiliatesPromotion.")
						union all
						select 0 as prior, mc.promotion_id, mc.merchant_id,mc.id,mc.title,mc.file,mc.type,mc.product_id,mc.category_id,mc.width,mc.height,mc.rdate,mc.last_update,mc.language_id
 from merchants_creative mc  inner join merchants on mc.merchant_id = merchants.id and merchants.valid=1 where mc.product_id=0 and mc.valid = '1' and mc.promotion_id not in (".$affiliatesPromotion.") and mc.promotion_id = 0 and mc.file NOT LIKE '%tmp%' and mc.affiliateReady = 1) as a 
						where 1=1 ".$where. " order by prior desc ,id desc"  . (!empty($ll) ? ' limit '.$ll.';' : '' ); */
	
			 // die ($qry);
			 
			$bottomNav = GetPages($appTable,$where,$pg,$getPos,$sql);

			if (isset($_GET['qa'])) die ($qry);
			$qq=function_mysql_query($qry,__FILE__);
			
			while ($ww=mysql_fetch_assoc($qq)) {
				$l++;
				/* var_dump($ww['file']);
				echo '<br>';
				echo '<br>'; */
				// $promInfo = mysql_fetch_assoc(function_mysql_query("SELECT valid,id,affiliate_id FROM merchants_promotions WHERE id='".$ww['promotion_id']."'",__FILE__));
				
				$promInfo = getPromotion($ww['promotion_id']);
				if (
					($promInfo['affiliate_id'] != $set->userInfo['id'] AND $promInfo['affiliate_id'] != '0' AND $promInfo['affiliate_id'] > 0 && !in_array($set->userInfo['id'], explode('|',$promInfo['additional_affiliates']))) 
					||
					$promInfo['valid']==-1){  continue; }
				
		
							/* 								<td align="center">'.($ww['type'] == "image" || $ww['type'] == "flash" || $ww['type'] == "mobileleader" || $ww['type'] == "mobilesplash" ? getFixedSizeBanner($ww['file'],80,80) : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>
							/								//  <td align="center">'.listPromotions($ww['promotion_id'],'',1,$set->userInfo['id'],1,1,1).($promInfo['affiliate_id'] == $set->userInfo['id'] ? ' (Special)' : '').'</td>
							*/ 				
					
					$text = 'copy_text_'.$ww['id'];
					if ($ww['type'] == "link") {
						$srcFile = $ww['title'];
						$link = $set->webAddress.'click_sub.php?ctag='.$tag;
						$code = '<!-- '.$set->webTitle.' Affiliate Code -->
						<a href="'.$link.'" target="_blank">'.$ww['title'].'</a>
						<!-- // '.$set->webTitle.' Affiliate Code -->';
						$preview = '<a href="'.$link.'" target="_blank">'.$ww['title'].'</a>';
					} 
					else if ($ww['type'] == "script") {
						$srcFile = 'Click Here';
						$link = $set->webAddress.'sub.g?ctag='.$tag;
						$code = '<!-- '.$set->webTitle.' Affiliate Code -->
						<iframe src="'.$link.'" width="'.$ww['width'].'" height="'.$ww['height'].'" frameborder="0" scrolling="no"></iframe>
						<!-- // '.$set->webTitle.' Affiliate Code -->';
						$preview = $ww['scriptCode'];
					} else {
						$link = $set->webAddress.'sub.g?ctag='.$tag;
						$code = '<!-- '.$set->webTitle.' Affiliate Code -->
						<script type= "text/javascript" language="javascript" src="'.$link.'"></script>
						<!-- // '.$set->webTitle.' Affiliate Code -->';
						$preview = getBanner($ww['file'],80);
					}
					if ($ww['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.$ww['file'].'" alt="" />';
						$codelink = '<br />';
						
						
					$allCreative .= '<div class="show-creative-table">
						<div class="table-responsive">
										<table class="creative-table">
										<tfoot>
										<tr>
										<td class="creative-img">
											<img src='.$ww['file'].' width="173" height="173">
										</td>
										<td class="creative-details">
											<div class="creative-details-table">
												<div class="creative-details-list">
													<strong>Creative Name</strong>
													<p>'.$ww['title'].'</p>
												</div>
												<div class="creative-details-list">
													<strong>'.lang(ucwords($ww['type'])).'</strong>
													<p>Image</p>
												</div>
												<div class="creative-details-list">
													<strong>Promotion</strong>
													<p>'.$promInfo['title'] .($promInfo['affiliate_id'] == $set->userInfo['id'] ? ' ('.lang('Special').')' : '').'</p>
												</div>
												<div class="creative-details-list">
													<strong>Category</strong>
													<p>'.(listCategory($ww['category_id'],$category_id,1,1)).'</p>
												</div>
												<div class="creative-details-list">
													<strong>Size (WxH)</strong>
													<p>'.(($ww['type'] == "link" || strtolower($ww['type']) == "mail" || strtolower($ww['type']) == "content" ) ? '' : $ww['width'].'x'.$ww['height']).'</p>
												</div>
												<div class="creative-details-list">
													<strong>Language</strong>
													<p>'.lang(listLangs($ww['language_id'],1)).'</p>
												</div>
											</div>
										</td>
										<td class="creative-copy-link">
									<div class="copy-link">
										<div class="copy-link-heading">
											<h4>Click URL</h4>
										</div>
										<div class="copy-link-input">
											<p id="copy_text_'.$ww['id'].'">'.$ww['url'].'</p>
										</div>
										<div class="copy-buttons">
											<button type="button" class="ClickURL" onclick="return copy_url('.$ww['id'].')">Copy Click URL <img src="../assets/images/img-new/copy.svg"></button>
											<!-- <span class="custom-tooltip" id="custom-tooltip'.$ww['id'].'" style="display:none;">Copied!</span> -->
											<button type="button" class="btn GetHTML" data-toggle="modal" data-target="#exampleModalCenter">
												Get HTML code <span>&#60;/&#62;</span> 
											  </button>
											  
											  <!-- Modal -->
											  <div class="modal fade HtmlCode-modal" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
												<div class="modal-dialog modal-dialog-centered" role="document">
												  <div class="modal-content html-modal-content">
													<div class="modal-header html-model-header">
													  <h5 class="modal-title" id="exampleModalLongTitle">HTML code</h5>
													  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													  </button>
													</div>
													<div class="modal-body html-model-body">
													  <div class="html-code-body">
														<div class="profile-div">
															<div class="profile-section">
																<div class="profile-lable">
																	<label>Profile</label>
																	<div class="profile-lable-input">
																		<div class="form-group">
																			<select class="form-control" id="exampleFormControlSelect1">
																			  <option>General1</option>
																			  <option>General2</option>
																			  <option>General3</option>
																			  <option>General4</option>
																			  <option>General5</option>
																			</select>
																		  </div>
																		<button class="plush">+</button>
																	</div>
																</div>
																<div class="DynamicParameter">
																	<label>Dynamic Parameter</label>
																	<div class="profile-lable-input">
																		<div class="form-group">
																			<input type="text"></input>
																		</div>
																		<button class="plush">+</button>
																	</div>
																</div>
															</div>
															<div class="get-code-button">
																<button>Get code</button>
															</div>
														</div>
														<div class="text-area-div">
														<textarea id="creative_code" onclick="this.focus(); this.select();">'.$code.'</textarea>'.$codelink.'
														</div>
													  </div>
													</div>
													<div class="modal-footer html-model-footer">
													  <div class="html-code-footer-button">
														<button onclick="return copy_code('.$ww['id'].')">Copy code <img src="../assets/images/img-new/copyWhite.svg"></button>
														<button id="dwn-btn">Download code<img src="../assets/images/img-new/coding.svg"></button>
														<button onclick="return textToPng();">Download image<img src="../assets/images/img-new/image.svg"></button>
													  </div>
													</div>
												  </div>
												</div>
											  </div>

										</div>
									</div>
								</td>
									</tr></tfoot>
									</table>
									</div>
									</div>
									<script>
										function copy_url(id) {
											var r = document.createRange();
											var text = "copy_text_"+id;
											r.selectNode(document.getElementById(text));
											window.getSelection().removeAllRanges();
											window.getSelection().addRange(r);
											document.execCommand("copy");
											window.getSelection().removeAllRanges();
											document.getElementById("custom-tooltip"+id).style.display = "inline";
											setTimeout( function() {
												document.getElementById("custom-tooltip"+id).style.display = "none";
											}, 1000);
										}
										function copy_code(id){
											var copyText = document.getElementById("creative_code");
											var copyText = copyText.value;

											var r = document.createRange();
											r.selectNode(document.getElementById("creative_code"));
											window.getSelection().removeAllRanges();
											window.getSelection().addRange(r);
											document.execCommand("copy");
											window.getSelection().removeAllRanges();
											$("#creative_code").focus(); 
											$("#creative_code").select();
										}
									</script>
									<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
									<script>
										function download(filename, text) {
											var element = document.createElement("a");
											element.setAttribute("href", "data:text/plain;charset=utf-8," + encodeURIComponent(text));
											element.setAttribute("download", filename);
										element.style.display = "none";
										document.body.appendChild(element);
										element.click();
										document.body.removeChild(element);
									}

									// Start file download.
									document.getElementById("dwn-btn").addEventListener("click", function(){
											// Generate download of hello.txt file with some content
										var text = document.getElementById("creative_code").value;
										var filename = "file.txt";
										download(filename, text);
									}, false);

									function textToPng() {
										html2canvas($("#creative_code"), {
											onrendered: function (canvas) {
												var img = canvas.toDataURL("image/png")
												window.open(img);
											}
										});
							
									}
								</script>
							';					
							
				}
				
			}
			


$set->content .= getImageGrowerScript();

$set->content .= '<div class="creative-page-filter">
					<div class="row align-items-center">
						<div class="col-lg-8">
							<div class="row">
								<div class="col-lg-3">
									<div class="filter-name">
										<h3>Creative type:</h3>
										<select name="type">
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
									</div>
								</div>
								<div class="col-lg-3">
									<div class="filter-name">
										<h3>Category:</h3>
										<select name="category_id">
										<option value="">'.lang('All').'</option>'.(listCategory($category_id,$merchant_id)).'
										</select>
									</div>
								</div>
								<div class="col-lg-2">
									<div class="filter-name">
										<h3>Language:</h3>
										<select name="lang">
										<option value="">'.lang('All').'</option>'.(listLangs($lang,0,$langsArray)).'
										</select>
									</div>
								</div>
								<div class="col-lg-2">
									<div class="filter-name">
										<h3>Size:</h3>
											'.$combolist.'
									</div>
								</div>
								<div class="col-lg-2">
									<div class="filter-name">
										<h3>Promotion:</h3>
										<select name="promotion">
										<option value="">'.lang('General').'</option>'.listPromotions($promotion,0,0,$set->userInfo['id'],0,1).'
										</select>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-4">
							<div class="search-wrp">
								<p>Search creative</p>
								<div class="search-box">
									<input type="text" name="q" value="'.$q.'" />
									<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="creatives-list-filter">
					<div class="row">
						<div class="col-md-6">
							<div class="creatives-list-filter-btn">
								<a href="#">Show Creative</a>
								<a href="#">Reset Search</a>
							</div>
						</div>
						<div class="col-md-6">
							<div class="creatives-list-filter-opt">
								<select>
									<option>Latest</option>
									<option>All</option>
								</select>
								<select>
									<option>100 Entries</option>
									<option>All</option>
								</select>
							</div>
						</div>
					</div>
				</div>';

		if($allCreative!=''){
			$set->content .=  '<div class="creatives-data-wrp">
								
										'.$allCreative.'
									
						</div>';
		}
			
		
		
		$set->content .= '<form method="get">
						<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>';
								
					if (mysql_num_rows($numqq) > 1) { 
			$set->content .= '<td  class="blueText" style="padding-bottom: 10px;">' . lang('Switch Merchant') . ':</td>';
						}
						
			
			
			
						$merchantsToDisplay = listMerchants($merchant);
								if ($set->showAllCreativesToAffiliate) {
										$merchantsToDisplay ='';
										$sql = "select * from merchants where  valid = 1";
										$qq=function_mysql_query($sql,__FILE__);
										while ($ww=mysql_fetch_assoc($qq)) {
											$merchantsToDisplay .= "<option value=".$ww['id'].">".lang($ww['name'])."</option>";
										}
								}
		
								if (mysql_num_rows($numqq) > 1) 
								//$set->content .= '<td align="left"  style="padding-bottom: 2px;">'.lang('Merchant').':&nbsp;&nbsp;&nbsp;<select name=[merchant__id] width="150px;">'.lang('Merchant').':</option>'.listMerchants($listmerch).'</select></td>';
								$set->content .= '
								<td align="left"><select name="merchant" style="width: 130px;">
								<option value="">'.lang('All').'</option>'.$merchantsToDisplay.'</select></td></tr></table></form>
						<hr />';
						
						$set->content .= '<div align="left" style="padding: 5px;">'.$bottomNav.'</div>';
		theme();
		break;
	
	/* ------------------------------------ [ Promotions ] ------------------------------------ */
	
	case "prom_valid":
		$db=dbGet($id,$appProm);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appProm,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=prom_valid&id='.$db['id'].'\',\'promlng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "prom_add":
		if (!$db['title']) $errors['title'] = 1;
		if (!$db['merchant_id']) $errors['merchant_id'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			$db[valid] = 0;
			dbAdd($db,$appProm);
			_goto($set->basepage.'?merchant_id='.$db['merchant_id']);
			}
	
	/* ------------------------------------ [ Promotions ] ------------------------------------ */
	
		case "showMail":
		$db=dbGet($id,$appTable);
		
		if ($_GET['p1'])
			$ctag .='-f'.mysql_real_escape_string($_GET['p1']);
		
		
		if ($db['type'] != "mail") die("ERROR");
		echo str_replace("{ctag}",$ctag,$db['scriptCode']);
		die();
		break;
	
	
	case "showContent":
		$db=dbGet($id,$appTable);
		if ($db['type'] != "content") die("ERROR");
		echo str_replace("{ctag}",$ctag,$db['scriptCode']);
		die();
		break;
	
	}

?>
