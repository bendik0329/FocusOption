<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);

$appTable = 'products_items';

switch ($act) {
	
	
	default:
		$pageTitle = lang('Products Place');
			$set->breadcrumb_title = $pageTitle;
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'">'.lang('Dashboard').'</a></li>
			<li><a href="'. $set->SSLprefix.$set->uri .'">'.$pageTitle.'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		
			$set->content .='	
			<style>
			.fancybox-opened {
				width:610px!important;
			}
			.fancybox-inner {
				height: 580px!important;
				width: 600px!important;
					background-color: white;
			}
			/* .prodDealsCls{
				-webkit-column-count:2;
				-moz-column-count: 2;
				column-count: 2;
				padding:10px 0;
			} */
			
			.prodDealsCls div{
				float:left;
				padding:10px 0;
				margin-right:20px;
			}
		
			.dealsCls{
				color:#417ba7;
				font-size:14px;
				font-weight:bold;
			}
			.dealsAmtCls{
				color:#3baf49;
				font-size:14px;
			}
			.previewBtn{
				background-color:#fff !important;
				color:#417ba7 !important;
				width:230px;
				margin:10px;
				font-family: Verdana;
				text-transform:capitalize !important;
			}
			.promoteBtn{
				background-color:#3baf49 !important;
				color:#fff !important;
				width:230px;
				margin:10px;
				text-transform:capitalize !important;
				
			}
			.clsRows{
				background-color:#EFEFEF;
				margin-bottom:10px;
			}
		
			.prodIdCls{
				padding: 10px;
				color: lightgray !important;
				font-size: 14px !important;
				font-family: Verdana;
			}
			.descCls{
				font-family: Verdana;
				font-size: 14px;
			}
			.titleCls{
				color: #417ba7 !important; 
				font-size: 20px !important; 
				font-family: Verdana !important;
				text-decoration:underline;
			}
			.titleDiv{
				margin-bottom: 10px !important;
			}
			</style>
			';
		
		
		if ($lang) $whereLang .= " AND languages LIKE '|".$lang."|'";
		if ($selected_categories) $where .= " AND cat_id in (". $selected_categories .")";
		
		if(!isset($selected_categories)  && isset($parent_id)){
			$sql = "select id from products_cats where parent_id = " . $parent_id  ." or id=". $parent_id ." and valid = 1";
			$res = function_mysql_query($sql);
			$snt = 0;
			$subCats = "";
			while($wwSubCats = mysql_fetch_assoc($res)){
				if($snt == 0){
					$subCats = "(" . $wwSubCats['id'];
				}
				else
					$subCats .= "," . $wwSubCats['id'];
				 
				$snt ++;
			}
			if($snt >0){
				$subCats .= ")";
			}
			$where .=" AND cat_id in " . $subCats;
		}
		
		if ($keyword){ 
			if(is_numeric($keyword))
				$where .= " AND id = ". $keyword;
			else
				$where .= " AND title like '%". $keyword ."%'";
		}
		$active_products = ltrim(str_replace('|',',',$set->userInfo['products']),',');
		
		if (empty($active_products))
			$active_products= 0;
		
		
		$sql = "SELECT * FROM ".$appTable." WHERE valid!='0' and id in (".$active_products. ") ".$where." ".$whereLang." ORDER BY id DESC";
		$qq=mysql_query($sql);
		$hasProductsInList = false;
		$allProductsArray = array();
		$allFeaturedProductsArray = array();
		$isFeatured = false;
		
		$sql = "select *, featured as total_featured from " . $appTable . " where valid!= 0 and id in (".$active_products.") and featured=1" . $whereLang . " order by id DESC";
		$wwFeatured = mysql_fetch_assoc(function_mysql_query($sql));
		if(isset($wwFeatured['total_featured']) && $wwFeatured['total_featured']>=1)
			$isFeatured = true;
		
		while ($ww=mysql_fetch_assoc($qq)) {
			if($ww['featured']){
				$allFeaturedProductsArray[$ww['id']] = $ww;
			}
			$allProductsArray[$ww['id']] = $ww;
		}
		
		$arrListProducts = array();
		if($isFeatured && $featured){
			$arrListProducts = $allFeaturedProductsArray;
		}
		else{
			$arrListProducts = $allProductsArray;
		}
		
		foreach($arrListProducts as $productK =>$ww){
			$hasProductsInList = true;
			$arrDealTypeDefaults = getProductsDealTypeDefaults($ww['id']);
		
			$q = "select pad.* from products_affiliates_deals pad inner join affiliates af on af.id = pad.affiliate_id where 
							pad.valid=1 and af.valid=1 and pad.affiliate_id = " . $set->userInfo['id'] . " and pad.product_id = " . $ww['id'];
			
			$dealRsc = (function_mysql_query($q,__FILE__ ));
			while ($AffDealsArryRow =  mysql_fetch_array($dealRsc)) {
				
 
				if ($AffDealsArryRow['dealType']=='cplaccount')
					$AffDealsArry['cplaccount'] = $AffDealsArryRow['amount'];
				else if ($AffDealsArryRow['dealType']=='cpllead')
					$AffDealsArry['cpllead'] = $AffDealsArryRow['amount'];
				else if ($AffDealsArryRow['dealType']=='cpa')
					$AffDealsArry['cpa'] = $AffDealsArryRow['amount'];
				else if ($AffDealsArryRow['dealType']=='cpc')
					$AffDealsArry['cpc'] = $AffDealsArryRow['amount'];
				else if ($AffDealsArryRow['dealType']=='cpi')
					$AffDealsArry['cpi'] = $AffDealsArryRow['amount'];
			
			
			
			}
			
			
			if (empty($AffDealsArry['cpa']) && empty($AffDealsArry['cplaccount']) && empty($AffDealsArry['cpllead']) && empty($AffDealsArry['cpc']) && empty($AffDealsArry['cpi'])){
			  $AffDealsArry = $arrDealTypeDefaults;
			}
			
			
			 
			 
			$affDealsNew = "";
			$dealTypeCnt = 0;
			$dealTypeCnt1 = 0;
			$totalAmt = 0;
			$affDealName = "";
			if(!empty($AffDealsArry['cpllead'] )){
				$affDeals .= lang("Lead") . " - " ."$". $AffDealsArry['cpllead'] ;
				$affDealsNew .= "<div><span class='dealsCls'>".lang("Lead") . ": " ."</span><span class='dealsAmtCls'>$". $AffDealsArry['cpllead'] . "</span></div>" ;
				$dealTypeCnt1++;
				$dealTypeCnt = 1;
				$totalAmt += $AffDealsArry['cpllead'];
				$affDealName = lang("Lead");
			}
			
		
		
			if(!empty($AffDealsArry['cplaccount'] ))
			{
				$affDeals .=  (!empty($affDeals)? "<br/>": "") . lang("Account") . " - " . "$". $AffDealsArry['cplaccount'] ;
				$affDealsNew .= "<div><span class='dealsCls'>".lang("Account") . ": " ."</span><span class='dealsAmtCls'>$". $AffDealsArry['cplaccount'] . "</span></div>" ;
				$dealTypeCnt1++;
				if($dealTypeCnt >=1) {
					$dealTypeCnt = 2;
				}
				else
					$affDealName = lang("Account");
				$totalAmt += $AffDealsArry['cplaccount'];
			}
			
				
			
			if(!empty($AffDealsArry['cpa'] ))
			{
				$affDeals .= (!empty($affDeals)? "<br/>": "") .  lang("Sale") ." - " ."$". $AffDealsArry['cpa'];
				$affDealsNew .= "<div><span class='dealsCls'>".lang("Sale") . ": " ."</span><span class='dealsAmtCls'>$". $AffDealsArry['cpa'] . "</span></div>" ;
				$dealTypeCnt1++;
				if($dealTypeCnt >=2) $dealTypeCnt = 3;
				else
					$affDealName = lang("Sale");
				$totalAmt += $AffDealsArry['cpa'];
			}
			
			if(!empty($AffDealsArry['cpc'] ))
			{
				$affDeals .= (!empty($affDeals)? "<br/>": "") .  lang("Cost Per Click") ." - " ."$". $AffDealsArry['cpc'];
				$affDealsNew .= "<div><span class='dealsCls'>".lang("Cost Per Click") . ": " ."</span><span class='dealsAmtCls'>$". $AffDealsArry['cpc'] . "</span></div>" ;
				$dealTypeCnt1++;
				if($dealTypeCnt >=3) $dealTypeCnt = 4;
				else
					$affDealName = lang("Cost Per Click");
				$totalAmt += $AffDealsArry['cpc'];
			}
			if(!empty($AffDealsArry['cpi'] ))
			{
				$affDeals .= (!empty($affDeals)? "<br/>": "") .  lang("Installation") ." - " ."$". $AffDealsArry['cpi'];
				$affDealsNew .= "<div><span class='dealsCls'>".lang("Installation") . ": " ."</span><span class='dealsAmtCls'>$". $AffDealsArry['cpi'] . "</span></div>" ;
				$dealTypeCnt1++;
				if($dealTypeCnt >=4) $dealTypeCnt = 5;
				else
					$affDealName = lang("Installation");
				$totalAmt += $AffDealsArry['cpi'];
			}
			
			

			
			$l++;
			$tag='a-'.$set->userInfo['id'].'-b'.$ww['id'].'-p'; // Create CTag
			
			$freeParam= isset($_GET['f_param']) && !empty($_GET['f_param']) ? '-f'. $_GET['f_param'] : "";
			
			// die ($freeParam);
		$tag='a'.$set->userInfo['id'].'-b'.$ww['id'].'-p'.$setProfile.$freeParam;
		$link = $set->webAddress.'click.php?ctag='.$tag;
		
		
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
                    
					
					if ($set->qrcode) {
					// die ('grege');
					$qrCode = file_get_contents($set->webAddress."common/BarcodeQR.php?link=".$link);
					
									$base64 = 'data:image/PNG;base64,' . base64_encode($qrCode );
					}
									
			
					
					$previewURL = $ww['url']. (strpos($ww['url'],'?')>0 ? '&': '?' ) . 'ctag='.$tag;
					$description = str_replace("\n", "<br />", $ww['text']);
					if(strlen($description) >245){
						$description = substr(strip_tags($description),0,244);
						$description .= "...<a href='javascript:void(0)' data-id=".  $ww['id']." class='promoteBtn' style='padding:0 5px'>". lang("more info") ."</a>";
					}
					
					$RTLstyle = $ww['isRTL'] ? "     text-align: right;    direction: rtl; " : "" ;
					
					
				$allCreative .= '
					<tr>
					<td colspan=3 style="height:4px"></td>
					</tr>
					<tr class="clsRows">
						<!--td width="50" valign="top" class="idCls">'. $ww['id'] .'</td-->
						<td width="80%" align="left" valign="top" style="padding: 10px;">
							<table style="width:100%"><tr><td style="width:20%" valign="top">
							<div class="outerBox_preview">
									<div class="wrapper_preview">
										<img border="0" src="'.$ww['image'].'" alt=""/>
									</div>
								</div>
							</td>
							<td valign="top" style="width:60%;position: relative;left: -15px;">
							<div class="titleDiv" style="'.$RTLstyle.'"><span class="titleCls" >'.$ww['title'] .'</span><span class="prodIdCls">['. $ww['id'] .']</span></div>
							<div class="descCls" style="'.$RTLstyle.'">'.$description.'</div>
							<div style="padding-top:10px"><div class="prodDealsCls">'. ($dealTypeCnt1!=0?'<div><span class="dealsCls">' . lang("Deal Type") .': </span><span class="dealsAmtCls">' .($dealTypeCnt1 >1  ? lang("Hybrid") : $affDealName) . "</span></div>". ( $dealTypeCnt1 <=1 ? "<div><span class='dealsCls'>" . lang("Payout") .": </span><span class='dealsAmtCls'>$" . $totalAmt . "</span></div>":''):'').  '<div><span class="dealsCls">'. lang("Payment Terms") .': 	</span><span class="dealsAmtCls">'. ($ww['terms']==""?' - ' : $ww['terms']) .'</span></div>'  .'</div></div>
							</td>
							</tr>
							</table>
						</td>
						<td>
						<input class="previewBtn" type="button" name="preview" id="preview" value="'. lang('Landing Page Preview') .'" data-url="'. $previewURL .'"/>
						<input class="promoteBtn" type="button" name="promote" id="promote" value="'. lang('Promote Now') .'" data-id='. $ww['id'] .'>
						</td>
						<!--td align="center" width="120"><a href="'.$previewURL.'" target="_blank" style="color: #1a5dcd; font-family: Arial;">'.lang('Landing Page Preview').'</a></td>
						<td  align="center" width="100"><a style="color: #1a5dcd; font-family: Arial;" href="affiliate/getProductCreatives.php?product_id='. $ww['id'] .'">'.lang('Choose Creative').'</a></td-->
					</tr>
				';
			
			
			
			}
			if (!$hasProductsInList){
				$allCreative = lang('No Active Products');
			}
			
	/* 	$sql = "SELECT pc.id,pc.title FROM products_cats pc inner join products_items p on p.cat_id = pc.id WHERE pc.valid='1' ORDER BY pc.title ASC";
		$catqq=function_mysql_query($sql);
		$listDDCategories = "";
		while ($catww=mysql_fetch_assoc($catqq)) {
			$totalItems=mysql_num_rows(mysql_query("SELECT id FROM products_items WHERE valid='1' AND cat_id='".$catww['id']."' ".$whereLang.""));
			//$listCategories .= '<tr><td align="left" height="30" style="background: #'.($cat_id == $catww['id'] ? 'E9E9E9' : 'F5F5F5').'; border-bottom: 1px #e3e3e3 solid; padding: 0 5px 0 5px;" onmouseover="this.style.background=\'#e9e9e9\';" onmouseout="this.style.background=\'#'.($cat_id == $catww['id'] ? 'E9E9E9' : 'F5F5F5').'\';"><a href="'.$set->basepage.'?id='.$catww['id'].'" style="font-family: Arial;">'.$catww['title'].' ('.$totalItems.')</a></td></tr>';
			//$listDDCategories .= "<option value=" .$catww['id']  .">" . $catww['title'] . "</option>";
		} */
		
		$categories = getAffiliateProductCategoriesList();
		
		if($isFeatured)
		{
			$listCats .="<li><h2 ".($featured?"style='background-color:grey'":'').">";
			$listCats .= " <a href='". $set->SSLprefix."affiliate/products.php?featured=1' ".($featured?"style='color:black'":"style='color:white'").">". lang('Top Offers') ."</a></h2></li>";
		}
		
		$listCats .="<li><h2 ". (!isset($selected_categories) && !isset($parent_id) && (!$isFeatured || !$featured)?"style='background-color:whitesmoke;'":'') . ">";
	
		$listCats .= " <a href='". $set->SSLprefix."affiliate/products.php' " .(!isset($selected_categories) && !isset($parent_id) && (!$isFeatured || !$featured)?"style='color:black'":'style="color:white;"').">". lang('All Products') ."</a></h2></li>";
		
		
		foreach ($categories as $cat):
					$listDDCategories .= "<optgroup label='". $cat['title'] ."'>";
					$listCats .= "
					<li>
						<input type='checkbox' ". (isset($parent_id)?($parent_id == $cat['id']?"":'checked'):"checked") .">
						<i></i>
						<h2>". $cat['title'] ."</h2>";
						if(isset($cat['sub_categories'])){
							$sc = 0;
							foreach($cat['sub_categories'] as $sub_category):
							$listDDCategories .= "<option value=" .$sub_category['id']  .">" . $sub_category['title'] . "</option>";
							if($sc == 0)
							{
								$listCats .="<p style='padding-left:20px;'>";
								$listCats .= " - <a href='". $set->SSLprefix."affiliate/products.php?parent_id=". $cat['id'] ."' " . (!isset($selected_categories)?($parent_id == $cat['id']?'style="color:black"':''):"") .">". lang("View All") ."</a><br/>";
							}
							$listCats .= " - <a href='". $set->SSLprefix."affiliate/products.php?selected_categories=".  $sub_category['id'] ."&parent_id=". $cat['id'] ."' ". (isset($selected_categories)?($selected_categories == $sub_category['id']?'style="color:black"':''):"") .">". $sub_category['title'] ."</a><br/>";
							$sc++;
							endforeach;
							if($sc !=0){
								$listCats .="</p>";
							}
						}
						else{
							$listCats .= '<p style="padding-left:20px;">';
							$listCats .= " - <a href='". $set->SSLprefix."affiliate/products.php?parent_id=". $cat['id'] ."' " . (!isset($selected_categories)?($parent_id == $cat['id']?'style="color:black"':''):"") .">". lang("View All") ."</a><br/>";
							$listCats .= lang("- No Sub Categories -") .'</p>';
						}
				  
				  $listCats .="</li>";
			endforeach;
		
		
		$set->content .= '<table style="width: 100%;">
		<tr><td>
		<form method="get">
		<input type="hidden" id="selected_categories" name="selected_categories">
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Search Creative Material').'</div>
						<div id="tab_3">
						
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table>
							
							<tr>
								<!--td align="left" class="blueText">'.lang('Category List').':</td-->
								<td align="left" class="blueText">'.lang('Language').':</td>
								<td align="left" class="blueText">'.lang('Name/ID').':</td>
								<td></td>
							</tr>
							<tr>
								<!--td align="left"><select name="cat_id" id="cat_id" style="width: 100px;" multiple="multiple">
								'.$listDDCategories.'
								</select></td-->
								<td align="left"><select name="lang"><option value="">All</option>'.listLangs($lang).'</select></td>
								<td align="left"><input type="text" name="keyword" value="'. $keyword .'" /></td>
								<td align="left"><input type="submit" value="'.lang('Search').'" /></td>
							</tr>
							</table>
						</div>
						</form>
						</td>
						<!--<td style="width:50%">
						
						<form style="float:right;" method="get">
						<input type="hidden" name="id" value="'.$_GET['id'].'"/>
						<span>'.lang('Parameter').':</span>
						<input name="f_param" type="text" value="'.$_GET['f_param'].'"/>
						<input type="submit" value="'.lang('Update Dynamic Parameter').'"/>
						</form>
						</td>-->
						</tr>
						</table>

						<hr />
						<table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:aliceblue">
							<tr>
								<td width="75%" valign="top">
									<div class="normalTableTitle">'.lang('Product\'s List').'</div>
										<table width="100%" border="0" cellpadding="2" cellspacing="0"class="normal">
											<tfoot class="prodtfootcls">
											'.$allCreative.' 
											<tfoot>
										</table>
								</td>
								<td width="25%" valign="top">
								<div style="margin:0 10px" class="normalTableTitle">'.lang('Product Categories').'</div>
									<ul class="accordian">
											'. $listCats .'
									</ul>
								</td>
							</tr>
						</table>';
						
		
		
		$set->content .='
		
		<script type="text/javascript" src="'.$set->SSLprefix.'js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
			<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
			<script>
						$("#cat_id option[value=\'\']").remove();
						$("#cat_id").multipleSelect({
									width: 200,
									placeholder: "Select category"
								});
						$("#cat_id").change(function(){
								$("#selected_categories").val($(this).val());
						}); 
						
						var selects = "'. $selected_categories .'";
						$("#cat_id").multipleSelect("setSelects",[  '.$selected_categories .' ]);
			
					$(".previewBtn").on("click",function(){
						url = $(this).data("url");
						$.prompt("'.lang('The link is for preview only.  This URL is not a tracking link').'", {
								top:200,
								title: "'.lang('Preview').'",
								buttons: { "'.lang('Ok').'": true, "'. lang('Cancel') .'": false },
								submit: function(e,v,m,f){
									if(v){
										var win = window.open(url, "_blank");
										win.focus();
									}
									else{
									}
								}
							});
						
					});
					
					$(".promoteBtn").on("click",function(){
						id=$(this).data("id");
						window.location.href= "'. $set->webAddress .'affiliate/getProductCreatives.php?product_id="+id;
					});
					
	</script>
	<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
	<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
	'; 
		
		theme();
		break;
	

	
	}

?>