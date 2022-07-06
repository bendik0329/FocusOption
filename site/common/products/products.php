<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
$appTable = "products";

switch ($act) {
	
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
								$target_file = $folder .  $_FILES["terms_link_file"]["name"];
							
								$uploadOk = 1;
								
								$imageFileType = pathinfo($_FILES["terms_link_file"]["name"],PATHINFO_EXTENSION);
								
								if($imageFileType != "html" && $imageFileType != "HTML") {
									$error =  "Sorry, only HTML file is allowed.";
									
									_goto($set->SSLprefix.$set->uri . "?error=". urlencode($error) . "&tab=terms_and_conditions");
								}
								else{
									
									if (move_uploaded_file($_FILES["terms_link_file"]["tmp_name"], $target_file)) {
										$ty =  "The file ". basename( $_FILES["terms_link_file"]["name"]). " has been uploaded.";
										$db['terms_and_conditions'] = $base_url . $terms_path . $_FILES["terms_link_file"]["name"]; 
									} else {
										$error =  "Sorry, there was an error uploading your file.";
										_goto($set->SSLprefix.$set->uri . "?error=". urlencode($error) . "&tab=terms_and_conditions");
									}
								}
								break;
				
				case "html_terms":
							$dir = $_SERVER['DOCUMENT_ROOT'];
							$myfile = file_put_contents($dir. "/files/products/terms/terms.html", $terms_link_html);
							$db['terms_and_conditions'] = $base_url . $terms_path . "terms.html"; 
							break;
				default:
							//default - input case
							$db['terms_and_conditions'] = $terms_and_conditions;
							break;
			}
			
			function_mysql_query("update products_items set terms_and_conditions='".$db['terms_and_conditions']."' where id='".$product_id."'");
		
			_goto($set->SSLprefix.$set->basepage.'?act=products&product_id='.$product_id .'&tab=terms_and_conditions');
		break;
	
	case "item_valid":
		$db=dbGet($id,$appTable.'_items');
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable.'_items',"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=item_valid&id='.$db['id'].'\',\'product_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "del_product":
		$ww=dbGet($id,$appTable.'_items');
		if (file_exists($ww['file'])) ftDelete($ww['file']);
		mysql_query("UPDATE  ".$appTable."_items set valid = -1 WHERE id='".$ww['id']."'");
		_goto($set->SSLprefix.$set->basepage.'?act=products&tab=product_list');
		break;
		
	case "upload_banner":
		$db['last_update'] = dbDate();
		if (chkUpload('file')) {
			$getOldBanner=dbGet($db['id'],"product_banners");
			if (file_exists($getOldBanner['file'])) ftDelete($getOldBanner['file']);
			$randomFolder =mt_rand(10000000, 99999999);
			$folder = 'files/products/tmp/' . $randomFolder ."/";
			 if (!is_dir('files/products/tmp')) {
				 mkdir('files/products/tmp');
			 }
			 if (!is_dir($folder)) {
				 mkdir($folder);
			 }
			$db['file'] = UploadFile('file','5120000','jpg,gif,swf,jpeg,png','',$folder);
			$exp=explode(".",$db['file']);
			$ext = strtolower($exp[count($exp)-1]);
			if ($ext == "swf") $db['type'] = "flash";
				else $db['type'] = "image";
			list($db['width'],$db['height']) = getimagesize($db['file']);
			}
		if (!$db['id']) $db['rdate'] = dbDate();
		if ($valid) $db['valid'] = 1; else $db['valid'] = 0;
		dbAdd($db,"product_banners");
		_goto($set->SSLprefix.$set->basepage.'?act=products&id='.$db['item_id']);
		break;
	
	case "item_add":

		if (!$db['title']) $errors['title'] = 1;
		if (!$db['url']) $errors['url'] = 1;
		if (empty($errors)) {
			
			 if($languages!=""){
				$arrLangs = explode(",",$languages);
				$db['languages'] = implode("|", $arrLangs);
			} 
		
			$db[rdate] = dbDate();
			$db[valid] = 0;
			$db['randomKey'] =  substr(sha1(mt_rand() . microtime()), mt_rand(0,35), 5);
			
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
		
			if (chkUpload('image')) $db['image'] = UploadFile('image','5120000','jpg,gif,jpeg,png','',$folder);
			dbAdd($db,$appTable.'_items');
			_goto($set->SSLprefix.$set->basepage.'?act=products&tab=product_list');
			}
	
	case "relateaffiliates":
		
		
	//	$affiliatesQ = function_mysql_query('SELECT id,merchants FROM affiliates WHERE valid=1 AND id NOT IN (SELECT DISTINCT affiliate_id FROM affiliates_deals WHERE merchant_id='.$id.')',__FILE__);
		$affiliatesQ = function_mysql_query('SELECT id,products FROM affiliates',__FILE__);
		//$defaultCPA = mysql_result(function_mysql_query('SELECT cpa_amount FROM merchants WHERE id='.$id,__FILE__),0,0);
		while($row = mysql_fetch_assoc($affiliatesQ)){
			$mr = ($row['products']!='' ? '|' : '').$row['products'].($row['products']!='' ? '|' : '');
			// |29| , |2|10|23|3|5|16|19|4|6|13|17|7|11|22|18|1|9|20|12|21|
			if(strpos($mr,'|'.$id.'|')===false){
				// if($mr!=''){
					// $mr.='|';
				// }
				$mr.=$id;
				
				$mr = ltrim($mr,'|');
				function_mysql_query('UPDATE affiliates SET products="'.$mr.'" WHERE id='.$row['id'],__FILE__);
				//echo 'UPDATE affiliates SET merchants="'.$mr.'" WHERE id='.$row['id'];
				//die();
			}else{
			}
			
			
			//function_mysql_query('INSERT INTO affiliates_deals (rdate,admin_id,merchant_id,affiliate_id,dealType,amount) VALUES (NOW(),1,'.$id.','.$row['id'].',2,"'.$defaultCPA.'")',__FILE__);
			
			//die('affiliateid: ' . $row['id']);
		}
		
		//echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'merchant_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
	case "unrelateaffiliates":
	
		   $affiliatesQ = function_mysql_query('SELECT id,products FROM affiliates',__FILE__);
		   
			while($row = mysql_fetch_assoc($affiliatesQ)){
				
				$mr = $row['products'];

				if(strpos('|'.$mr.'|','|'.$id.'|')===false){
					
				
				
				}else{
				
					$mr2 = '';
					$mr = explode('|',$mr);
					
					for($i=0;$i<count($mr);$i++){
						//die($mr[$i]);
						if($mr[$i]!=$id){
							if($mr2!=''){
								$mr2.='|';
							}
							 $mr2.= $mr[$i];
						}
					}
					$mr = trim($mr2,'|');
				}
				
				
				function_mysql_query('UPDATE affiliates SET products="'.$mr.'" WHERE id='.$row['id'],__FILE__);
				//echo 'UPDATE affiliates SET merchants="'.$mr.'" WHERE id='.$row['id'];
				//die();
				//function_mysql_query('INSERT INTO affiliates_deals (rdate,admin_id,merchant_id,affiliate_id,dealType,amount) VALUES (NOW(),1,'.$id.','.$row['id'].',2,"'.$defaultCPA.'")',__FILE__);
				//die();
			}
		//	die('affiliateid: ' . $row['id']);
			
	
	//echo 'delete from  `affiliates_deals` where merchant_id='.$id;
	//delete from `affiliates_deals` where merchant_id= $merchantID
	die();
	break;
	
	case "products":
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
			$("#url").blur(function() {
				  var input = $(this);
				  var val = input.val();
				  if (val && !val.match(/^http([s]?):\/\/.*/)) {
					input.val("http'.$set->SSLswitch.'://" + val);
				  }
				});
		
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
  <li><a href="javascript:void(0)" data-tab="product_list" class="active">'. lang('Product List') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="add_new_product">'. lang('Add New Product') .'</a></li>
  <li style="display:none"><a href="javascript:void(0)" data-tab="terms_and_conditions">'. lang('Terms and Conditions') .'</a></li>
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

  
		if ($id) {
			$db = dbGet($id,$appTable.'_items');
			$pageTitle = lang('Editing Product').': '.$db['title'];
			} else $pageTitle = lang('Add New Product');
		$catqq=mysql_query("SELECT * FROM ".$appTable."_cats ORDER BY id ASC");
		while ($catww=mysql_fetch_assoc($catqq)) $catsList .= '<option value="'.$catww['id'].'" '.($catww['id'] == $db['cat_id'] ? 'selected="selected"' : '').'>'.$catww['title'].'</option>';
		
	
		
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
		
		$pageTitle = 'Products';
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix. $userLevel .'/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$lang_exp=explode("|",$db['languages']);
		$i=0;
		$langqq=mysql_query("SELECT * FROM languages WHERE valid='1' ORDER BY title ASC");
		$allLangOptions = "";
		while ($langww=mysql_fetch_assoc($langqq)) {
			//$allLangOptions[$langww['id']] = $langww['title'];
			$allLangOptions .= '<option value="'.$langww['id'].'">'.$langww['title'].'</option>';
			if ($i >= 5) {
				$allLangs .= '</tr><tr>';
				$i=0;
				}
			if ($i < 5) {
				$allLangs .= '<td><label><input type="checkbox" name="langs[]" value="'.$langww['id'].'" '.(@in_array($langww['id'],$lang_exp) ? 'checked="checked"' : '').' /> '.$langww['title'].'</label></td>';
				$i++;
				}
			}
			
			
		$affiliateqq=mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC");
		// while ($affiliateww=mysql_fetch_assoc($affiliateqq)) $allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $db['affiliate_id'] ? 'selected' : '').'>'.$affiliateww['username'].' ['.$affiliateww['id'].'] ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
		
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
		
		if ($id) {
			$banner=dbGet($banner_id,"merchants_creative");
			$l=0;
			
			if(isset($db['languages']) && $db['languages']!=""){
					$arrLangs = explode("|", $db['languages']);
					$db["languages"] = implode(",",$arrLangs);
			}
			
			$qq=mysql_query("SELECT * FROM merchants_creative WHERE product_id>0 and  product_id='".$db['id']."' ORDER BY id DESC");
			while ($ww=mysql_fetch_assoc($qq)) {
				$l++;
				$totalTraffic = mysql_fetch_assoc(mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM product_stats_banners WHERE banner_id='".$ww['id']."' AND product_id='".$db['id']."'"));
				$allCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$ww['id'].'</td>
								<td><a href="'.$set->SSLprefix.$set->basepage.'?act=products&id='.$ww['item_id'].'&banner_id='.$ww['id'].'">'.lang('Edit').'</a></td>
								<td>'.$ww['title'].'</td>
								<td align="center" class="img-wrap">'.($ww['type'] == "image" || $ww['type'] == "flash" ? getBanner($ww['file'],'25') : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>
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
				
			$set->content .= '
			<form method="post" enctype="multipart/form-data">
			<input type="hidden" name="act" value="upload_banner" />
				<input type="hidden" name="db[id]" value="'.$banner['id'].'" />
				<input type="hidden" name="db[item_id]" value="'.$id.'" />
				<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Add Banner').'</div>
				<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_3">
				<tr><td align="left">'.lang('Title').':</td><td><input type="text" name="db[title]" value="'.$banner['title'].'" /></td></tr>
				<tr><td align="left">'.lang('File').':</td><td>'.fileField('file',$banner['file']).'</td></tr>
				<tr><td></td><td><input type="checkbox" name="valid" checked />'.lang('Publish').'</td></tr>
				<tr><td></td><td align="left"><input type="submit" value="'.lang('Upload').'" /></td></tr>
				</table>
			</form><hr />
			<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_4\').slideToggle(\'fast\');">'.lang('Banners List').'</div>
			<table width="100%" border="0" cellpadding="2" cellspacing="0" bgcolor="#EFEFEF" id="tab_4" class="normal">
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
			</table><hr />
			';
			
			}
		
		$set->content .= '
		<script type="text/javascript" src="'.$set->SSLprefix.'js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
		<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
		<!-- jQuery UI Autocomplete css -->
                                                                        <script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
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
                                                                            /* width: 174px; */
                                                                            background: white;
                                                                            border-radius: inherit;
                                                                            border-color: #CECECE;
                                                                            color: #1F0000;
                                                                            font-weight: inherit;
                                                                            font-size: inherit;
                                                                          }
                                                                          .ui-autocomplete { 
                                                                            height: 200px; 
                                                                            /* width:  310px; */
																			    width: 13%;
                                                                            overflow-y: scroll; 
                                                                            overflow-x: hidden;
                                                                          }
                                                                        </style>
		';
	$prodTypes = "
							<option value='Binary Option' ". ($db['type']=='Binary Option'?'selected':'') .">Binary Option</option>
							<option value='Forex' ". ($db['type']=='Forex'?'selected':'') .">Forex</option>
							<option value='Casino' ". ($db['type']=='Casino'?'selected':'') .">Casino</option>
							<option value='Bingo' ". ($db['type']=='Bingo'?'selected':'') .">Bingo</option>
							<option value='Sports' ". ($db['type']=='Sports'?'selected':'') .">Sports</option>
							<option value='Other' ". ($db['type']=='Other'?'selected':'') .">Other</option>
							";
		$set->content .= '<form id="frmAddBanner" method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="item_add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<input type="hidden" name="languages" id="languages"/>
						<div id="add_new_product" data-tab="add_new_product" class="config_tabs" style="display:none">
						<div class="normalTableTitle" style="cursor: pointer;" data-tab2="add_new_product">'.$pageTitle.'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1" class="tblDetails">
							<tr><td colspan="3" height="5" ></td></tr>
							<tr><td colspan="3" height="5" class="errors" style="color:red;"></td></tr>
							<tr><td align="left">'.lang('Product Name').'<span style="color:red">*</span>:</td><td><input  style="WIDTH: 12.2%;"  type="text" id="title" name="db[title]" value="'.$db['title'].'" /></td></tr>
							<tr><td align="left">'.lang('URL').'<span style="color:red">*</span>:</td><td><input  style="WIDTH: 435px;" type="text" id="url" name="db[url]" value="'.$db['url'].'" /></td></tr>
							<tr><td align="left">'.lang('Category').':</td><td><select  style="WIDTH: 20%;"  name="db[cat_id]"><option value="">'.lang('Choose Category').'</option>'.$catsList.'</select></td></tr>
							<tr><td align="left">'.lang('Type').':</td><td><select  style="WIDTH: 15%;"  name="db[type]"><option value="">'.lang('Choose Type').'</option>'.$prodTypes.'</select></td></tr>
							<!--<tr><td align="left">'.lang('Affiliate').':</td><td><div class="ui-widget"><select id="combobox" name="db[affiliate_id]"><option value="">'.lang('Choose Affiliate').'</option>'.$allAffiliates.'</select></div></td></tr>
							<tr><td align="left">'.lang('CPA Amount Affiliate').':</td><td><input  style="WIDTH: 12.2%;"  type="text" name="db[cpaAmountAffiliate]" value="'.$db['cpaAmountAffiliate'].'" /></td></tr>
							<tr><td align="left">'.lang('CPA Amount').':</td><td><input  style="WIDTH: 12.2%;"  type="text" name="db[cpaAmount]" value="'.$db['cpaAmount'].'" /></td></tr>-->
							<tr><td align="left" valign="top">'.lang('Languages').':</td><td><!--<table><tr>'.$allLangs.'</tr></table>-->
							<select name="lang" id="lang" style="width: 100px;" multiple="multiple">'.$allLangOptions.'</select>
							</td></tr>
							<tr><td align="left">'.lang('Tracking Parameter Name').'<span style="color:red">*</span>:</td><td><input  style="WIDTH: 12.2%;" type="text" name="db[param]" id="param" value="'.$db['param'].'" /></td></tr>
							<tr><td align="left">'.lang('Product API Code').':</td><td><input  style="WIDTH: 12.2%;" type="text" name="db[productAPICode]" id="productAPICode" value="'.$db['productAPICode'].'" /></td></tr>
							<tr><td align="left" valign="top">'.lang('Description').':</td><td><textarea name="db[text]" cols="60" rows="6">'.$db['text'].'</textarea></td></tr>
							<tr><td align="left">'.lang('Logo').':</td><td>'.(strpos($db['image'],'/tmp/')?fileField('image','../images/wheel.gif'):fileField('image',$db['image'])).'</td></tr>
							<tr><td align="left">'. lang('Featured') .'</td><td><div><label class="switch" div style="float:left"><input type="checkbox" id="featured" name="db[featured]" value=1 '.($db['featured']==1 ? 'checked' : '').' ><div class="slider round"></div></label><div style="padding-top:6px"></div></td></tr>
							<tr><td align="left">'. lang('Is right to left layout') .'</td><td><div><label class="switch" div style="float:left"><input type="checkbox" id="isRTL" name="db[isRTL]" value=1 '.($db['isRTL']==1 ? 'checked' : '').' ><div class="slider round"></div></label><div style="padding-top:6px"></div></td></tr>
							<tr><td align="left">'.lang('Payment Terms').':</td><td><input  style="width:300px;"  type="text" name="db[terms]" value="'.$db['terms'].'" /></td></tr>
							<tr><td align="left">'.lang('Countries Allowed').':</td><td><select name="allowedCountries" id="allowedCountries" style="width: 100px;" multiple="multiple">'. countriesList() .'
							</select>
							<input type="hidden" name="db[countries_allowed]" id="selected_countries"/>
							</td></tr>
							<tr><td></td><td align="left"><input type="submit"  value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						</div>
						<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
						<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
						<script>
						$("form#frmAddBanner").on("submit",function(){
							if($("#title").val()=="" || $("#url").val()=="" || $("#param").val()=="")
							{
								$(".errors").html("'. lang('Missing required fields.') .'")
								return false;
							}
							return true;
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
						
						</script>
						';
				$atype = isset($_POST['atype'])?$_POST['atype']:1;
				if($atype !="")
					$where .= " and valid " . ($atype==1? ">-1" : "=" . $atype);
			$sql = "SELECT * FROM ".$appTable."_items WHERE 1=1 ". $where ." ORDER BY id ASC";
			$qq=function_mysql_query($sql,__FILE__,__FUNCTION__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$l++;
				$catName = dbGet($ww['cat_id'],$appTable."_cats");
				$exp=explode("|",$ww['languages']);
				unset($languagesList);
				for ($i=0; $i<=count($exp); $i++) {
					if (!$exp[$i]) continue;
					$langww = dbGet($exp[$i],"languages");
					$languagesList[] = $langww['title'];
					}
				$affInfo = dbGet($ww['affiliate_id'],"affiliates");
				$creativeList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>
							<td align="center"><a href="'.$set->SSLprefix.$userLevel.'/edit_products.php?act=products&id='.$ww['id'].'">'.lang('Edit').'</a>&nbsp;|&nbsp;<a style="cursor:pointer;" data-productid='. $ww['id'] .' class="delete_product">'.lang('Delete').'</a></td>
							'.(strpos($ww['image'],'/tmp/')?'<td align="center" class="tooltip"><img src="../images/wheel.gif" width=28><span class="tooltiptext" style="padding-bottom:15px">'. lang("System is checking for virus. Please refresh in a minute.") .'</span></td>':'
							<td align="center" class="img-wrap">'. getFixedSizeBanner($ww['image'],50,50) .'</td>').'
							<td>'.$ww['title'].'</td>
							<td align="center">'.dbDate($ww['rdate']).'</td>
							<td align="center">'.$catName['title'].'</td>
							<!--<td align="center"><a href="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=products&id='.$ww['affiliate_id'].'">'.$affInfo['username'].'</a></td>
							<td align="center">'. $set->currency .' '.$ww['cpaAmountAffiliate'].'</td>
							<td align="center">'. $set->currency .' '.$ww['cpaAmount'].'</td>-->
							<td align="center">'.@implode(", ",$languagesList).'</td>
							<td align="center"><a href="javascript:void(0)" onclick=openTrackingURL("'.$ww['url'].'")>'.$ww['url'].'</a></td>
							<td align="center" id="product_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=item_valid&id='.$ww['id'].'\',\'product_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
							<td  align="center" id="product_'.$ww['id'].'">
							<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=relateaffiliates&id='.$ww['id'].'\',\'product_'.$ww['id'].'\'); setTimeout(function(){location.reload();},500);" style="cursor: pointer;">Relate All</a>
							<span>&nbsp;&nbsp;&nbsp;</span>
							<a onclick="var r=confirm(\'Are you sure you want to delete all affiliate relations to this product?\'); if(r==true){ajax(\''.$set->SSLprefix.$set->basepage.'?act=unrelateaffiliates&id='.$ww['id'].'\',\'product_'.$ww['id'].'\'); setTimeout(function(){location.reload();},500);}" style="cursor: pointer;">Unrelate All</a>
							</td>
						</tr>';
				}
			
			$set->content .= '
			<form method="post"><div style="text-align:right;margin-right:0px;margin-bottom:20px;">'. lang('Show') .': <select name="atype" style="width:150px;" onchange="form.submit();">
			<option value="" '. ($_POST['atype']==1?'selected':'') .'>'. lang('All') .'</option>
			<option value=1 '. (!isset($_POST['atype'])?' selected':$_POST['atype']==1?' selected':'') .'>'. lang('Active') .'</option>
			</select></div></form>
			<div id="product_list" data-tab="product_list" class="config_tabs">
			<div class="normalTableTitle" style="cursor: pointer;" data-tab2="product_list">'.lang('Products List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0" class="tblDetails">
							<thead>
							<tr>
								<td>#</td>
								<td align="center">'.lang('Options').'</td>
								<td>'.lang('Logo').'</td>
								<td>'.lang('Product Name').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Category').'</td>
								<!--<td align="center">'.lang('Affiliate').'</td>
								<td align="center">'.lang('CPA Amount Affiliate').'</td>
								<td align="center">'.lang('CPA Amount').'</td>-->
								<td align="center">'.lang('Languages').'</td>
								<td align="center">'.lang('URL').'</td>
								<td align="center">'.lang('Active').'</td>
								<td width="150" align="center">'.lang('Affiliate Relations').'</td>
							</tr></thead><tfoot>'.$creativeList.'</tfoot>
						</table></div>';
			$set->content .= '<script>
						$("#lang option[value=\'\']").remove();
						$("#lang").multipleSelect({
									width: 200,
									placeholder: "'.lang('Select Languages').'"
								});
						$("#lang").change(function(){
							$("#languages").val($(this).val());
						}); 
						
						var selects = "'. $db['languages'] .'";
						console.log(selects);
						$("#lang").multipleSelect("setSelects",[  '. $db['languages'] .' ]);
						
						function openTrackingURL(url){
							$.prompt("'.lang('This link is for preview only.  This URL is not a tracking link').'", {
								top:200,
								title: "'.lang('Preview').'",
								buttons: { "'.lang('Ok').'": true, "'. lang('Cancel') .'": false },
								submit: function(e,v,m,f){
									if(v){
										window.open(url);
									}
									else{
									}
								}
							});
						}
						
				</script>';
				
				$set->content .= '
			<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
			<script>
			$(document).ready(function(){
			$(".delete_product").on("click", function(){
				
				product_id = $(this).data("productid");
			
			$.prompt("'.lang('Are you sure you want to delete this product?').'", {
					top:200,
					title: "'.lang('Delete Product').'",
					buttons: { "'.lang('Yes').'": true, "'. lang('Cancel') .'": false },
					submit: function(e,v,m,f){
						if(v){
							var url = "'. $userLevel .'/products.php?act=del_product&id="+ product_id;
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
				$set->content .= '
	<style>
	
	.img-wrap img {
		transition: all 0.5s ease;
	}
	
	.img-wrap img:not(.small-scale) {
		display: block;
		max-width: 100%;
		height: auto;
	}
	

	
	.img-wrap img.small-scale {
		display: block;
		max-height: 200px;
		width: auto;
	}
	
	.creative-name {
		width: 10%;
	}
	</style>';
	
	//Terms and Conditions Block
	
	if(isset($product_id)){
		$dbProd = dbGet($product_id,"products_items");
	}
	
	$productsList = "";
	$sql = "select * from ". $appTable . "_items where 1=1 order by id ASC";
	$qqProducts = function_mysql_query($sql,__FILE__, __FUNCTION__);
	while($wwProducts = mysql_fetch_assoc($qqProducts)){
		if(isset($product_id) && $product_id == $wwProducts['id'])
		$productsList .= "<option value=" . $wwProducts['id'] . " selected>". $wwProducts['title'] ."</option>";
		else
		$productsList .= "<option value=" . $wwProducts['id'] . ">". $wwProducts['title'] ."</option>";
	}
	
	$set->content.='
		<div id="terms_and_conditions" data-tab="terms_and_conditions" class="config_tabs" style="display:none">
		<div class="normalTableTitle" style="cursor: pointer;" data-tab2="terms_and_conditions">'.lang('Terms and Conditions').'</div>
		<form method="GET" name="termsForm">
		<input type="hidden" name="act" value="products">
		<input type="hidden" name="tab" value="terms_and_conditions">
		<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0" class="tblDetails">
		<tr><td colspan="3" height="5"></td></tr>
		<tr><td colspan="3" height="5" class="errTerms"></td></tr>
		<tr><td width="200px">'. lang('Select Product') .': </td><td align="left"><select name="product_id"><option value="">'. lang('Select') .'</option>'. $productsList .'</select></td>
		<td align="left"><input type="submit" value="'.lang('Load Terms and Conditions').'" /></td>
		</tr>
		
		</table>
		</form>
		
		<form method="POST" name="termsDetailsForm" '.(isset($product_id)?'':'style="display:none"').' enctype="multipart/form-data">
		<input type="hidden" name="act" value="upload_terms">
		<input type="hidden" name="product_id" value="'. $product_id .'">
		<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0" class="tblTermsDetails" >
			<tr><td width=200 style="padding:10px 0">'.lang('Current Terms & Conditions URL').': </td><td><b>'.(strpos($dbProd['terms_and_conditions'],"/tmp/")? '<span style="color:red"><strong><i>' . lang('System is looking for viruses. Please refresh the page in a minute.') .'</i></strong></span>':'<a href= "'. (strpos($dbProd['terms_and_conditions'],"/tmp/")?'':$dbProd['terms_and_conditions']) .'" target="_blank">'. (strpos($dbProd['terms_and_conditions'],"/tmp/")?lang('System is looking for viruses. Please refresh the page in a minute.'):$dbProd['terms_and_conditions']) .'</a>').'</b></td></tr>
			<tr><td colspan="2" height="5"></td></tr>
			<tr>
			<td colspan=2 align="left">'.lang('Terms & Conditions').':</td>
			</tr>
			<tr>
				<td colspan=2>
				<div style="width:50px;float:left;"><input type="radio" name="terms" value="input_terms"/></div>
				<div style="float:left;"><input type="text" name="terms_and_conditions"  style="width: 250px;" value="'.(strpos($dbProd['terms_and_conditions'],"/tmp/")?'':($dbProd['terms_and_conditions'] ? $dbProd['terms_and_conditions'] : 'http'.$set->SSLswitch.'://')).'"/></div>
				<div style="clear:both;height:10px"></div>
				<div style="width:50px;float:left;"><input type="radio" name="terms" value="browse_terms"/></div>
				<div style="float:left;">'. fileField('terms_link_file',(strpos($dbProd['terms_and_conditions'],"/tmp")?$set->SSLprefix.'images/wheel.gif':$dbProd['terms_and_conditions'])) .'<span style="padding:0 0 0 10px">('.lang('HTML file only').')</span></div>
				<div style="clear:both;height:10px"></div>
				<div style="width:50px;float:left;"><input type="radio" name="terms" value="html_terms"/></div>
				<div style="float:left;"><textarea name="terms_link_html" id="contentTerms" cols="80" rows="40">'.  (strpos($dbProd['terms_and_conditions'],"/tmp/")?'':(!empty($dbProd['terms_and_conditions']) ? @file_get_contents($dbProd['terms_and_conditions']) : "")) .'</textarea></div>
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
				
				$("form[name=termsForm]").on("submit",function(e){
					
					if($("select[name=product_id]").val() == ""){
						$(".errTerms").html("'. lang("Please select Product") .'").css("color","red");
						$("form[name=termsDetailsForm]").hide();
						return false;
					}
					return true;
					
				});
		});
		</script>
	';
	
	
				$set->content .= "</div>";//main div closed
		theme();
		break;

	/* ------------------------------------ [ Manage products ] ------------------------------------ */
	case "del_banner":
		$ww=dbGet($id,"merchants_creative");
		if (file_exists($ww['file'])) ftDelete($ww['file']);
		mysql_query("DELETE FROM merchants_creative WHERE id='".$ww['id']."'");
		die('<a href="'.$set->SSLprefix.$set->basepage.'?act=products&id='.$ww['product_id'].'">'.lang('Go Back').'!</a>');
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
		echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'product_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "add":
		if (!$db['title']) $errors['title'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			$db[valid] = 0;
			dbAdd($db,$appTable.'_cats');
			_goto($set->basepage);
			}
			
			
			
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
						<td align="center"><a href="'.$set->basepage.'?id='.$ww['id'].'">'.lang('Edit').'</a><span>&nbsp;&nbsp;|&nbsp;&nbsp;</span><a href="'.$set->basepage.'?act=delete&id='.$ww['id'].'">'.lang('Delete').'</a></td>
						<td>'.$ww['title'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center" id="product_'.$ww['id'].'"><a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'product_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a>
						
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
								<td align="center">'.lang('Active').'</td>
							</tr></thead><tfoot>'.$productList.'</tfoot>
						</table>';
						
		}else {
			
				$set->content = '<form method="post">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="db[id]" value="'.$id.'" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Category').'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">'.lang('Category Name').':</td><td><input type="text" name="db[title]" value="'.$db['title'].'" '.($errors['title'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Category List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td align="center">'.lang('Options').'</td>
								<td>'.lang('Product Name').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Active').'</td>
							</tr></thead><tfoot>'.$productList.'</tfoot>
						</table>';
		}
		theme();
		break;
	}

?>