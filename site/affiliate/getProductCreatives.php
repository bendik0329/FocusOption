<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
require_once ('common/ShortUrl.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);


$appTable = 'products_items';

switch ($act) {

case 'get_code':
	$appTable = 'products_items';
	$creativetype = "product";
	include('common/getTrackingCode.php');
	theme();
	break;	
	
default:
		$pageTitle = lang('Product Details');
			$set->breadcrumb_title = $pageTitle;
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.'affiliate/products.php">'.lang('Products Place').'</a></li>
			<li><a href="'. $set->SSLprefix.$set->uri .'">'.$pageTitle.'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		
		$set->content .='	
			<style>
			.prodDealsCls{
				-webkit-column-count: 3;
				-moz-column-count: 3;
				column-count:3;
				padding:10px 0;
			}
			.prodDealsCls_threeCol{
				-webkit-column-count: 3;
				-moz-column-count: 3;
				column-count: 3;
				padding:10px 0;
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
			.advertiserBtn{
				color:#fff !important;
				width:230px;
				margin:10px;
				font-family: Verdana;
			}
			.contactBtn{
				background-color:#3baf49 !important;
				color:#fff !important;
				width:230px;
				margin:10px;
				font-family: Verdana;
				
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
			div.btn{
				background: url("'.$set->SSLprefix.'images/btn.jpg");
				color: #2A79CB;
				padding-left: 30px;
				font-size: 12px;
				font-family: Arial;
				font-weight: bold;
				width: 150px;
				height: 28px;
				line-height: 28px;
				text-align: left;
				cursor: pointer;
				margin-bottom: 5px;
				}
			div.btn:hover {
				background: url("'.$set->SSLprefix.'images/btn.jpg");
				color: #1C5794;
				padding-left: 30px;
				font-size: 12px;
				font-family: Arial;
				font-weight: bold;
				width: 150px;
				height: 28px;
				line-height: 28px;
				text-align: left;
				cursor: pointer;
				margin-bottom: 5px;
				}
			</style>
			';
		$products_list = ltrim(str_replace ("|","," , $set->userInfo['products']),',');
		if (empty($products_list))
			$products_list= 0;

	$sql = "SELECT pad.* , cat.title as category_name, cat.parent_id,( SELECT title FROM products_cats WHERE id = cat.parent_id) AS main_category  from ".$appTable." pad left join products_cats cat on cat.id = pad.cat_id WHERE pad.valid!=0 and pad.id=". $product_id .(empty($product_list) ? "" : " and pad.id IN (" . $products_list .  ") " )." ORDER BY pad.id DESC;";	
	$qq=mysql_query($sql);
	
	while ($ww=mysql_fetch_assoc($qq)) {
			
			$arrDealTypeDefaults = getProductsDealTypeDefaults($ww['id']);
		
			$q = "select pad.* from products_affiliates_deals pad inner join affiliates af on af.id = pad.affiliate_id where pad.valid=1 and af.valid=1 and pad.affiliate_id = " . $set->userInfo['id'] . " and pad.product_id = " . $ww['id'];
			//echo $q .'<br>';
			
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
			if (empty($AffDealsArry['cpa']) && empty($AffDealsArry['cplaccount']) && empty($AffDealsArry['cpllead']))
			  $AffDealsArry = $arrDealTypeDefaults;
			
		
			$affDealsNew = "";
			$dealTypeCnt = 0;
			$dealTypeCnt1 = 0;
			$totalAmt = 0;
			$affDealName = "";
			if(!empty($AffDealsArry['cpllead'] )){
				$affDeals .= lang("Lead") . " - " ."$". $AffDealsArry['cpllead'] ;
				$affDealsNew .= "<div><span class='dealsCls'>".lang("Lead") . ": " ."</span><span class='dealsAmtCls'>$". $AffDealsArry['cpllead'] . "</span></div>" ;
				$dealTypeCnt = 1;
				$dealTypeCnt1++;
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
				if($dealTypeCnt >=2) $dealTypeCnt = 3;
				else
					$affDealName = lang("Cost Per Click");
				$totalAmt += $AffDealsArry['cpc'];
			}
			
			if(!empty($AffDealsArry['cpi'] ))
			{
				$affDeals .= (!empty($affDeals)? "<br/>": "") .  lang("Installation") ." - " ."$". $AffDealsArry['cpi'];
				$affDealsNew .= "<div><span class='dealsCls'>".lang("Installation") . ": " ."</span><span class='dealsAmtCls'>$". $AffDealsArry['cpi'] . "</span></div>" ;
				$dealTypeCnt1++;
				if($dealTypeCnt >=2) $dealTypeCnt = 3;
				else
					$affDealName = lang("Installation");
				$totalAmt += $AffDealsArry['cpi'];
			}
			
			
		
			
			$l++;
		
				$tag='a'.$set->userInfo['id'].'-b'.$ww['id'].'-p'.$setProfile.$freeParam;				
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
									</style>
									';
			//Allowed Countries
			if($ww['countries_allowed']!=""){
			$sql = "SELECT GROUP_CONCAT(title) as title FROM countries where id in(". $ww['countries_allowed'] .")";
			$wwCountries   = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
			$strCountries = $wwCountries['title'];
			}
		
			$fullurl = strpos($ww['url'],'?')>0 ? $ww['url'].'&ctag='.$tag : $ww['url'].'?ctag='.$tag;
			$description = str_replace("\n", "<br />", $ww['text']);
			
			//status
			$product_status = "";
			if($ww['valid']==1){
				$product_status = lang('Active');
			}
			else if($ww['valid']==2){
				$product_status=lang('Pending');
			}
			else if($ww['valid']==3){
				$product_status=lang('PreLaunch');
			}
				$product_status=trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $product_status));
			
		$RTLstyle = $ww['isRTL'] ? "     text-align: right;    direction: rtl; " : "" ;
			
			
			$allCreative .= '
			<div style="margin: 10px 0 10px 0;background-color:#EFEFEF;">
				<table width="80%" border="0" cellpadding="0" cellspacing="0" style="border: 1px #e3e3e3 solid;margin:0 auto;">
					<tr>
						<td width="20%" valign="top" style="padding: 10px 0px;">
						<div class="outerBox_preview">
									<div class="wrapper_preview">
										<img border="0" src="'.$ww['image'].'" alt="" style="margin:0px"/>
									</div>
								</div>
						</td>
						<td> <!--style="position: relative;left: -85px;"-->
							<table>
							<tr>
						<td >
						<div class="titleDiv"  style="'.$RTLstyle.'"><span class="titleCls">'.$ww['title'] .'</span><span class="prodIdCls">['. $ww['id'] .']</span></div>
					</td>
					</tr>
					<tr>
					<td colspan=2>
					<div class="descCls" style="'.$RTLstyle.'">'.$description.'</div>
					</td>
					</tr>
					<tr>
					<td colspan=2>
					<div style="padding-top:10px"><div class="prodDealsCls_threeCol">'.($dealTypeCnt1!=0?'<div><span class="dealsCls">' . lang("Deal Type") .': </span><span class="dealsAmtCls">' .($dealTypeCnt1 >1  ? lang("Hybrid") : $affDealName) . "</span></div>". ( $dealTypeCnt1 <=1 ? "<div><span class='dealsCls'>" . lang("Payout") .": </span><span class='dealsAmtCls'>$" . $totalAmt . "</span></div>":''):'').  '<div><span class="dealsCls">'. lang("Payment Terms") .': 	</span><span class="dealsAmtCls">'. ($ww['terms']==""?' - ' : $ww['terms']) .'</span></div>'  .'</div></div>
					</td>
					</tr>
					<tr>
					<td colspan=2>'. ($ww['parent_id'] == 0?'<div><div class="prodDealsCls"><div><span class="dealsCls">' . lang("Main Category") .': </span><span class="dealsAmtCls">' . ($ww["category_name"]!=''?$ww["category_name"]:lang('General')) . '</span></div></div>':'
					<div><div class="prodDealsCls"><div><span class="dealsCls">' . lang("Main Category") .': </span><span class="dealsAmtCls">' . $ww["main_category"] . '</span></div><div><span class="dealsCls">' . lang("Sub Category") .': </span><span class="dealsAmtCls">' . $ww["category_name"] . '</span></div></div>
					').'
					</td>
					</tr>
					<tr>
					<td colspan=2>
					<div style="padding-top:10px"><span class="dealsCls">' . lang("Countries Allowed") .': </span><span class="dealsAmtCls">'. ($ww['countries_allowed']==""?"-":$strCountries) .'</span></div></div>
					</td>
					</tr>
					<tr>
					<td colspan=2>
					<div style="padding-top:10px"><span class="dealsCls">' . lang("Languages") .': </span><span class="dealsAmtCls">'. ($ww['languages']==""?"-": lang(listLangs($ww['languages'],1)) ).'</span></div></div>
					</td>
					</tr>
					<tr><td>
					<div style="padding-top:10px"><span class="dealsCls">'. lang('Product Status') .': </span><span class="dealsAmtCls">'. $product_status .'</span>
					</td></tr>
					<tr><td height=10px></td></tr>
					'.($ww['terms_and_conditions']!=""?'<tr>
					<td colspan=2><a href="'. $ww['terms_and_conditions'] .'" style="font-size:15px;" target="_blank">'. lang('By promoting this product you agree to').' ' .'<u>'.lang('Terms and Conditions').'</u>' .'</a></td>
					</tr>':'').'
					<tr><td height=10px></td></tr>
					<tr style="display:none">
					<td colspan=2>
					<input class="contactBtn" type="button" name="promote" id="promote" value="'. lang('Apply To Promote This Campaign') .'" style="width:320px;margin:10px 0px">
					</td>
					</tr>
							</table>
						</td>
						<td align="right" valign="top" style="padding: 10px;">
							<input class="advertiserBtn" type="button" name="preview" id="preview" value="'. lang('Advertiser Profile') .'" style="display:none"><br/>
							<input class="contactBtn" type="button" name="promote" id="promote" value="'. lang('Contact Advertiser') .'" style="display:none">
						</td>
					</tr>
					
				</table>
			</div>';
			
			
			$qq=mysql_query("SELECT * FROM merchants_creative WHERE product_id>0 and  product_id='".$product_id ."' and valid >= 0 ORDER BY id DESC");
			while ($ww=mysql_fetch_assoc($qq)) {
				$l++;
				list($width,$height) = @getimagesize($ww["file"]);
				$totalTraffic = mysql_fetch_assoc(mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE product_id='". $product_id ."' and banner_id='".$ww['id']."' and affiliate_id =". $set->userInfo['id']));
				$allBanners .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td><span class="dimantion-wrap" style="display:none">'.$width .'x'. $height .'</span>'.$ww['id'].'</td>
								<td>'.$ww['title'].'</td>
								<td align="center" class="img-wrap">'.($ww['type'] == "image" || $ww['type'] == "flash" ? '<img  style="    width:50px; max-height: 50px;    max-width: 100%;" border="0" src="'.$ww['file'].'" alt="" />' : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>
								<td align="center">'.$ww['type'].'</td>
								<td align="center">'.($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</td>
								<td align="center">'.number_format($totalTraffic['totalViews'],0).'</td>
								<td align="center">'.number_format($totalTraffic['totalClicks'],0).'</td>
								<td align="center" id="bbnr_'.$ww['id'].'">
								<!--a href="#getURL_'.$ww['id'].'" rel="fancybox" style="color: #1a5dcd; font-family: Arial;">'.lang('Get Tracking Code').'</a-->
								<a href="'.$set->webAddress. ltrim($set->basepage, '/').'?act=get_code&creative_id='.$ww['id'].'" class="inline">'.lang('Get Tracking Code').'</a>
								
								</td>
							</tr>';
								}
			
			$creatives .='<div class="normalTableTitle" style="cursor: pointer;">'.lang('Creatives List').'</div>
			<table width="100%" border="0" cellpadding="2" cellspacing="0" bgcolor="#EFEFEF" class="normal">
				<thead>
					<tr>
						<td>#</td>
						<td>'.lang('Creative Name').'</td>
						<td>'.lang('Preview').'</td>
						<td>'.lang('Format').'</td>
						<td>'.lang('Size').' ('.lang('Width').' x '.lang('Height').')</td>
						<td>'.lang('Impressions').'</td>
						<td>'.lang('Clicks').'</td>
						<td>'.lang('Options').'</td>
					</tr></thead><tfoot>'. $allBanners .'</tfoot>
			</table>
			
			';
			
			
			}
		$catqq=mysql_query("SELECT id,title FROM products_cats WHERE valid='1' ORDER BY title ASC");
		$listDDCategories = "";
		while ($catww=mysql_fetch_assoc($catqq)) {
			$totalItems=mysql_num_rows(mysql_query("SELECT id FROM products_items WHERE valid='1' AND cat_id='".$catww['id']."' ".$whereLang.""));
			$listCategories .= '<tr><td align="left" height="30" style="background: #'.($cat_id == $catww['id'] ? 'E9E9E9' : 'F5F5F5').'; border-bottom: 1px #e3e3e3 solid; padding: 0 5px 0 5px;" onmouseover="this.style.background=\'#e9e9e9\';" onmouseout="this.style.background=\'#'.($cat_id == $catww['id'] ? 'E9E9E9' : 'F5F5F5').'\';"><a href="'.$set->basepage.'?id='.$catww['id'].'" style="font-family: Arial;">'.$catww['title'].' ('.$totalItems.')</a></td></tr>';
			$listDDCategories .= "<option value=" .$catww['id']  .">" . $catww['title'] . "</option>";
			}
		$set->content .= '<!--table style="width: 100%;">
		<tr><td>
		<form method="get">
		<input type="hidden" id="selected_categories" name="selected_categories">
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Search Creative Material').'</div>
						<div id="tab_3">
						
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table>
							
							<tr>
								<td align="left" class="blueText">'.lang('Category List').':</td>
								<td align="left" class="blueText">'.lang('Language').':</td>
								<td align="left" class="blueText">'.lang('Name/ID').':</td>
								<td></td>
							</tr>
							<tr>
								<td align="left"><select name="cat_id" id="cat_id" style="width: 100px;" multiple="multiple">
								'.$listDDCategories.'
								</select></td>
								<td align="left"><select name="lang"><option value="">All</option>'.listLangs($lang).'</select></td>
								<td align="left"><input type="text" name="keyword" value="'. $keyword .'" /></td>
								<td align="left"><input type="submit" value="'.lang('Search').'" /></td>
							</tr>
							</table>
						</div>
						</form>
						</td-->
						<!--<td style="width:50%">
						
						<form style="float:right;" method="get">
						<input type="hidden" name="id" value="'.$_GET['id'].'"/>
						<span>'.lang('Parameter').':</span>
						<input name="f_param" type="text" value="'.$_GET['f_param'].'"/>
						<input type="submit" value="'.lang('Update Dynamic Parameter').'"/>
						</form>
						</td>-->
						<!--/tr>
						</table-->
						<div class="btn"><a href="'.$set->SSLprefix.'affiliate/products.php?featured=1">'.lang('Back To Products').'</a></div>
						<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
							<td width="100%" valign="top">
								<div class="normalTableTitle">'.lang('Product Details').'</div>
								'.$allCreative.'
							</td>
						</tr></table>';
		
			$set->content .= $creatives;
		$set->content .='<script type="text/javascript" src="'.$set->SSLprefix.'js/bootstrap.min.js"></script>
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
				</script>';
		
		$set->content .= getImageGrowerScript();
		
		
		theme();
		break;
	

	
	}

?>