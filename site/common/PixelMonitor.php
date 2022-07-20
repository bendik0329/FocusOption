<?php

/*
 * CAUTION!
 * Following columns of `affiliates` must be kept up to date: 
 * 1. `accounts_pixel_params_replacing`
 * 2. `sales_pixel_params_replacing`
 */
$currentDomain = ('http'.$set->SSLswitch.'://' . $_SERVER['HTTP_HOST']);
$arrayOfMerchantsCreatives = array();


function getItemCreatives($id,$type,$p_banner_id){
	
	global $arrayOfMerchantsCreatives,$affiliate_id_value;
	
	
	$whereAff ="";
	if (!empty($affiliate_id_value) && $type=='merchant'){
		$whereAff = "  and (mrc.promotion_id =0 or mrc.promotion_id in (
                            select id from merchants_promotions where merchant_id in (".$id .") and affiliate_id in (".$affiliate_id_value.",0)
                            )  )";
	}
	
	
	if($type=='merchant'){
		$selected_type = "merchant";
		$sql = "select mrc.id, mrc.title from merchants_creative mrc where 2=2 and mrc.merchant_id=" . $id . " and mrc.valid = 1 " . $whereAff ;
	}
	else{
		$selected_type = "product";
		$sql = "select id, title from merchants_creative where product_id=" . $id . " and valid = 1";
	}
	
	
	if ($selected_type == "product"){
		
		if (empty($arrayOfMerchantsCreatives['p'][$id])){
		
			$qqCreative = function_mysql_query($sql, __FILE__);
			// $listCreatives_wizard = "<option value=''>". lang("Choose Creative")."</option>";
			if($qqCreative){
				while($wwCreatives = mysql_fetch_assoc($qqCreative)){
					$arrayOfMerchantsCreatives['p'][$id][] = $wwCreatives;
				}
				
			}
		}
		// else
			{
			
			foreach ($arrayOfMerchantsCreatives['p'][$id] as $wwCreatives){
				
				$listCreatives_wizard .= "<option value='". $wwCreatives['id'] ."' ". ($wwCreatives['id']==$p_banner_id ?'selected':'') .">". $wwCreatives['title'] ."</option>";
			}
		
	}
	}
	
	if ($selected_type == "merchant"){
		
		if (empty($arrayOfMerchantsCreatives['m'][$id])){
		
			$qqCreative = function_mysql_query($sql, __FILE__);
	// $listCreatives_wizard = "<option value=''>". lang("Choose Creative")."</option>";
			if($qqCreative){
				while($wwCreatives = mysql_fetch_assoc($qqCreative)){
					$arrayOfMerchantsCreatives['m'][$id][] = $wwCreatives;
				}
				
			}
			
		}
		// else 
		{
			foreach ($arrayOfMerchantsCreatives['m'][$id] as $wwCreatives){
				
				$listCreatives_wizard .= "<option value='". $wwCreatives['id'] ."' ". ($wwCreatives['id']==$p_banner_id ?'selected':'') .">". $wwCreatives['title'] ."</option>";
			}
		}
	}
	
	$listCreatives_wizard = "<option value=0>". lang("All Creatives")."</option>".$listCreatives_wizard;
	return $listCreatives_wizard;
}

if(isset($pixel_id) && !empty($pixel_id)){
	$qry     = "SELECT * FROM pixel_monitor WHERE id = '" . $pixel_id . "' ORDER BY id ASC";
	$pixel_data = mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
	
	$pixel_banner_id = $pixel_data['banner_id'];
	
	if (!empty($pixel_data['merchant_id'])){
		$theId =  $pixel_data['merchant_id'] ;
		$theType = 'merchant';
	}else{
		$theType = 'product';
		$theId = $pixel_data['product_id'];
	}
	
	//$listCreatives_wizard = getItemCreatives($theId,$theType,$pixel_banner_id);
	
	 //$listCreatives_wizard = '<option value="0" class="">All Creatives</option>'.	$listCreatives_wizard ;
	
	 
	
	$qqCreative = function_mysql_query($sql, __FILE__);
	$listCreatives_wizard = "<option value=''>". lang("Choose Creative")."</option>";
	if($qqCreative){
		while($wwCreatives = mysql_fetch_assoc($qqCreative)){
			$listCreatives_wizard .= "<option value='". $wwCreatives['id'] ."' ". ($wwCreatives['id']==$pixel_banner_id?'selected':'') .">". $wwCreatives['title'] ."</option>";
		} 
	}	
	
}

$qry     = "SELECT * FROM pixel_monitor WHERE affiliate_id = '" . ($userLevel == "affiliate"?$set->userInfo['id']:$_GET['id']) . "' ORDER BY id ASC";
$pixelqq = function_mysql_query($qry,__FILE__,__FUNCTION__);
$i       = 1;
	$trigerOptions= "";
	$blockTypeArray =array();
		$avapo=explode("|",$set->combinedPixelOption);
		for ($i=0; $i<count($avapo); $i++) {
								$isActive = 1;
								$crName = $avapo[$i];
								if (strpos('*' . $avapo[$i],'-')==1) {
									$x = str_replace('-','',strtolower($avapo[$i]));
									array_push($blockTypeArray, $x);
								}else {
									$trigerOptions.='<option value="'.strtolower($avapo[$i]).'"' . ((isset($pixel_id) && $pixel_data['type']==strtolower($avapo[$i]))?' selected ':'')  .' >'.lang(strtoupper($avapo[$i])).'</option>';
									/* <option value="lead">'.lang('Lead').'</option><option value="account">'.lang('Account').'</option><option value="sale">'.lang('Sale').'</option> */
									/* $crName = ltrim($avapo[$i],'-'); */
								}
								if (empty($crName))
									continue;
		}
$isClientSideExists = false;
while ($pixelww = mysql_fetch_assoc($pixelqq)) {
		$pixel_banner_id = $pixelww['banner_id'];
		if(!empty($pixelww['merchant_id'])){
			$theType = 'merchant';
			$theId = $pixelww['merchant_id'];
			// $sql = "select id, title from merchants_creative where merchant_id=" . $pixelww['merchant_id'] . " and valid = 1";
			
		}
		else{
			$theId = $pixelww['product_id'];
			$theType = 'product';
			// $sql = "select id, title from merchants_creative where product_id=" . $pixelww['product_id'] . " and valid = 1";
		}
			$listCreatives = getItemCreatives($theId,$theType,$pixel_banner_id);
			if ($_GET['nniirr']==1){
				
				var_dump($arrayOfMerchantsCreatives);
			var_dump($listCreatives);
			die();
			}
			
		
		
	/* 	
		$qqCreative = function_mysql_query($sql, __FILE__);
		$listCreatives = "<option value=0>". lang("All Creatives")."</option>";
		if($qqCreative){
			while($wwCreatives = mysql_fetch_assoc($qqCreative)){
				$listCreatives .= "<option value='". $wwCreatives['id'] ."' ". ($wwCreatives['id']==$pixelww['banner_id']?'selected':'') .">". $wwCreatives['title'] ."</option>";
			}
		} */	
		if($pixelww['method'] == "client"){
			$isClientSideExists = true;
		}
    $boolPixelIsActive = !empty($pixelww['valid']);
	$showTest = true;
	if((!empty($pixelww['pixelCode']) && preg_match('/({param}|{clickid}|{click_id}|{subid}|{param2}|{p2}|{p1}|{dynamic_parameter}|{dynamic_parameter2})/', strtolower($pixelww['pixelCode'])) === 1) || $pixelww['method']=='client' ) { 
	$showTest = false;
	}
	$cnt = $i+1;
	if(!empty($pixelww['merchant_id']))
		$selected_type = "merchant";
	else
		$selected_type = "product";
		
/* 	
	echo '<br><br>';
	var_dump($pixelww);
	echo '<br><br>'; */

	
    $listPixels .= '
	<tr '.($i % 2 ? 'class="trLine"' : '').'>
        <form id="pixelEditForm" class="pixelEditForm" action="'.$set->SSLprefix.$set->basepage.'" method="post">
		<input type="hidden" name="pixelType" value="'.(!empty($pixelww['merchant_id'])?'merchants':'products').'"/>
		<td>'.(!empty($pixelww['merchant_id'])?lang('Merchant'):lang('Product')).'</td>
		<input type="hidden" name="ids[]" value="'.$pixelww['id'].'" />
        <td align="center"'.(!empty($pixelww['merchant_id'])?"":"style='display:none'").'><input type="hidden" name="id" value="'.$_GET['id'].'" /><select name="merchant_id[]" style="width:140px" data-pixel_id = "'. $pixelww['id'] .'" data-banner_id = "'. $pixelww['banner_id'] .'" data-selected_type = "'. $selected_type .'">'.listMerchants($pixelww['merchant_id']).'</select></td>
        <td align="center"'.(empty($pixelww['merchant_id'])?"":"style='display:none'").'><input type="hidden" name="id" value="'.$_GET['id'].'" /><select name="product_id[]" style="width:140px" data-pixel_id = "'. $pixelww['id'] .'" data-banner_id = "'. $pixelww['banner_id'] .'" data-selected_type = "'. $selected_type .'">'.listProducts($pixelww['product_id']).'</select></td>
        <td align="center"><select name="banner_id" style="width:140px !important" id="banner_id_'. $pixelww['id'] .'" data-pixel_id = "'. $pixelww['id'] .'">'. $listCreatives .'</select></td>
        <td align="center"><textarea name="pixelCode[]" cols="45" rows="3" onblur="checkHtml(this,'. $cnt .')">'.$pixelww['pixelCode'].'</textarea></td>
        <td align="center">
            <select name="type[]">
				'. (strtolower($pixelww['type']) != "account" && in_array('account',$blockTypeArray) ? '' : '<option value="account" '.(strtolower($pixelww['type']) == "account" ? 'selected="selected"' : '').'>'.lang('Account').'</option>').
				 (strtolower($pixelww['type']) != "sale" && in_array('sale',$blockTypeArray) ? '' : '<option value="sale" '.(strtolower($pixelww['type']) == "sale" ? 'selected="selected"' : '').'>'.lang('FTD').'</option>').
				 (strtolower($pixelww['type']) != "lead" && in_array('lead',$blockTypeArray) ? '' : '<option value="Lead" '.(strtolower($pixelww['type']) == "lead" ? 'selected="selected"' : '').'>'.lang('Lead').'</option>') .
				 (strtolower($pixelww['type']) != "qftd" && in_array('qftd',$blockTypeArray) ? '' : '<option value="qftd" '.(strtolower($pixelww['type']) == "qftd" ? 'selected="selected"' : '').'>'.lang('Qualified FTD').'</option>') .
				 (strtolower($pixelww['type']) != "install" && in_array('install',$blockTypeArray) ? '' : '<option value="install" '.(strtolower($pixelww['type']) == "install" ? 'selected="selected"' : '').'>'.lang('Installation').'</option>') .
                '<!--option value="sale" '.(strtolower($pixelww['type']) == "sale" ? 'selected="selected"' : '').'>'.lang('FTD').'</option>
                <option value="lead" '.(strtolower($pixelww['type']) == "lead" ? 'selected="selected"' : '').'>'.lang('Lead').'</option-->
            </select>
        </td>
        ' . ($pixelww['totalFired'] > 0 ? '<td align="center" class="tooltip"><a class="inline btnTotalFired" href="#pixel_logs" data-pixel_id="' . $pixelww['id'] . '" data-affiliate_id="' . $pixelww['affiliate_id'] . '">' 
                . $pixelww['totalFired'] . '</a><span class="tooltiptext">'. lang("Click for details") .'</span></td>' : '<td align="center"></a></td>') . '
        <td align="center">
            <select name="method[]" id="dbmethod'.$cnt .'">
                <option value="post" '.(strtolower($pixelww['method']) == "post" ? 'selected="selected"' : '').'>'.lang('Server To Server').' - POST</option>
                <option value="get" '.(strtolower($pixelww['method']) == "get" ? 'selected="selected"' : '').'>'.lang('Server To Server').' - GET</option>
                <option value="client" '.(strtolower($pixelww['method']) == "client" ? 'selected="selected"' : '').'>'.lang('Client Side').'</option>
            </select>
        </td>
        <td align="center">
            <a href="javascript:void(0)">
                <img style="width:15px;height:15px;" 
                     src="' . ($boolPixelIsActive ? $set->SSLprefix.'images/docs_green.jpg' : $set->SSLprefix.'images/docs_red.png') . '" 
                     alt="' . ($boolPixelIsActive ? 'Deactivate pixel' : 'Activate pixel') . '" 
                     title="' . ($boolPixelIsActive ? 'Deactivate pixel' : 'Activate pixel') . '" 
                     '.($userLevel!="affiliate"?'onclick="return pixelActivation(this, ' . $pixelww['id'] . ');"':'') .' />
            </a>
        </td>
        <!--td align="center">'.($showTest ? '<input name="testPixel" type="submit" value="'.lang('Test').'"  style="padding-left:7px;padding-right:7px;"/>':'').' &nbsp;&nbsp;&nbsp;&nbsp;<input name="editPixel" type="submit" value="'.lang('Update').'" style="padding-left:7px;padding-right:7px;" '. ($userLevel == "affiliate"?' onclick="return confirmPixelUpdate();"':'') .'/>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="deletePixel" value="'.lang('Delete').'" style="padding-left:7px;padding-right:7px;" '. ($userLevel == "affiliate"?' onclick="return confirmPixelDelete();"':'') .'/></td-->
        <td align="center">
			<!--div class="dropdown1">
                    <ul class="dropbtn icons btn-right showLeft" onclick="showDropdown(this)">
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
				<div id="myDropdown" class="dropdown-content" style="display:none">
					<input type="submit"  name="editPixel" value='.lang('Update').'><br/>
					<input type="submit" name="deletePixel" value='.lang('Delete').'><br/>
					<input type="submit" name="editWizard" value="'.lang('Edit with wizard').'"><br/>
					'.($showTest ? '<input type="submit" name="testPixel" value='.lang('Test').'><br/>':'').' 
				</div-->
				<a href="#" data-jq-dropdown="#jq-dropdown-'. $i .'" class="dropdown-dots">
				<span class="horizontalDots">...</span>
				</a>
				<div id="jq-dropdown-'. $i .'"class="jq-dropdown-custom-fix jq-dropdown jq-dropdown-anchor-right jq-dropdown-tip">
					<ul class="jq-dropdown-menu">
						<li><a href="javascript:void(0)"><input type="submit"  name="editPixel" value='.lang('Update').'  '. ($userLevel == "affiliate"?' onclick="return confirmPixelUpdate();"':'') .'></a></li>
						<li><a href="javascript:void(0)"><input type="submit" name="deletePixel" value='.lang('Delete').'  '. ($userLevel == "affiliate"?' onclick="return confirmPixelDelete();"':'') .'></a></li>
						<li><a href="'. $set->SSLprefix.$set->basepage.'?act=new&id='.$id.'&pixel_id='. $pixelww['id'] .'&toggleTo=api_access' .'">'.  lang('Edit with Wizard') .'</a></li>
						'.($showTest ? '<li><a href="javascript:void(0)"><input type="submit" name="testPixel" value='.lang('Test').'></a></li>':'').' 
					</ul>
				</div>
		</td>
        </form>
    </tr>';
    $i++;
}
$set->content .= '
    <script type="text/javascript">
		$(document).ready(function(){
				$("select[name=\'merchant_id[]\']").on("change",function(){
					val =$(this).val();
					pixel_id = $(this).data("pixel_id");
					banner_id = $(this).data("banner_id");
					selected_type = $(this).data("selected_type");
					loadListCreatives("merchant",val,banner_id,selected_type,pixel_id);
				});
				$("select[name=\'product_id[]\']").on("change",function(){
					val =$(this).val();
					pixel_id = $(this).data("pixel_id");
					banner_id = $(this).data("banner_id");
					selected_type = $(this).data("selected_type");
					loadListCreatives("product",val,banner_id,selected_type,pixel_id);
				});			
		});       
		
		function loadListCreatives(type,id,banner_id,selected_type,pixel_id){
			creative_url = "'.$set->SSLprefix.'ajax/LoadPixelCreatives.php";
			$.post(creative_url,{type:type,id:id,selected_banner_id:banner_id,selected_type:selected_type,listpixels:1},
					function(res) {
					if(pixel_id==""){
						$("select[name=\'db[banner_id]\']").html(res);
					}
					else{
							if(type=="product"){
								$("select[id=banner_id_"+pixel_id+"]").html(res);
							}
							else if(type=="merchant"){
								$("select[id=banner_id_"+pixel_id+"]").html(res);
							}
					}
					});
		}
function pixelActivation(_this, intPixelId) {
	var objParams = { 
                action   : "pixel_activation",
                pixel_id : intPixelId
				};
        $.post("'.$set->SSLprefix.'ajax/PixelActions.php", objParams, function(res) {
			if ("1" == res) {
                  //  console.log("'.$set->SSLprefix.'ajax/PixelActions.php - success\n");
                    var _alt = $(_this).attr("alt");
                    //alert("Deactivate pixel" == _alt ? "Pixel is deactivated" : "Pixel is activated");
                    $(_this).attr("alt", "Deactivate pixel" == _alt ? "Activate pixel" : "Deactivate pixel");
                    $(_this).attr("title", "Deactivate pixel" == _alt ? "Activate pixel" : "Deactivate pixel");
                    $(_this).attr("src", "Deactivate pixel" == _alt ? "images/docs_red.png" : "images/docs_green.jpg");
                } else {
                   // console.log("'.$set->SSLprefix.'ajax/PixelActions.php - failure\n\n" + res + "\n\n");
                  //  alert("No action has been taken due to an error");
                }
            });
            return false;
        }
        function pixelParamsUpdate(_this, intAffId, strType, strParam) {
            var objParams = { 
                action       : ("account" == strType ? "account_pixel_params_update" : "sale_pixel_params_update"),
                affiliate_id : intAffId,
                param        : strParam
            };
			
            $.post("'.$set->SSLprefix.'ajax/PixelActions.php", objParams, function(res) {
                if ("1" == res) {
                  //  console.log("'.$set->SSLprefix.'ajax/PixelActions.php - success\n");
                    var _alt = $(_this).attr("alt");
                    //alert("Deactivate" == _alt ? "{" + strParam + "} is deactivated" : "{" + strParam + "} is activated");
                    $(_this).attr("alt", "Deactivate" == _alt ? "Activate" : "Deactivate");
                    $(_this).attr("title", "Deactivate" == _alt ? "Activate" : "Deactivate");
                    $(_this).attr("src", "Deactivate" == _alt ? "images/docs_red.png" : "images/docs_green.jpg");
                } else {
                  //  console.log("'.$set->SSLprefix.'ajax/PixelActions.php - failure\n\n" + res + "\n\n");
                  //  alert("No action has been taken due to an error");
                }
            });
            return false;
        }
    </script>
';
$theID = isset($_GET['id']) && !empty($_GET['id']) ? $_GET['id'] : $set->userInfo['id'];

$sql = 'SELECT `accounts_pixel_params_replacing` AS accounts_pixel_params_replacing, '
        . '`sales_pixel_params_replacing` AS sales_pixel_params_replacing '
        . 'FROM `affiliates` '
        . 'WHERE `id` = ' . mysql_real_escape_string($theID) . ' '
        . 'LIMIT 0, 1;';
	
$arrAffiliatePixelParams = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
$activeAccountPixels =array();
$activeSalesPixels =array();
if (!empty($arrAffiliatePixelParams)) { 
	$activeAccountPixels =  explode(",",$arrAffiliatePixelParams['accounts_pixel_params_replacing']);
	$activeSalesPixels =  explode(",",$arrAffiliatePixelParams['sales_pixel_params_replacing']);
}

//$arrAccountPixelParams     = json_decode($arrAffiliatePixelParams['accounts_pixel_params_replacing'], true);

$accountPixels = array(
	"ctag"=>"Trader ID from the platform",
	"trader_id"=>"Campaign Parameter",
	"trader_alias"=>"Username",
	"type"=>"Type of the account",
	"affiliate_id"=>"Affiliate ID",
	"uid"=>"AffiliateTS unique user internal click id",
	"dynamic_parameter"=>"Dynamic Parameter",
	"dynamic_parameter"=>"Dynamic Parameter2"
	);
	$salesPixels = array(
	"ctag"=>"Campaign Parameter",
	"trader_id"=>"Trader ID from the platform",
	"tranz"=>"Transaction ID",
	"type"=>"Type of the account",
	"currency"=>"Account Currency",
	"amount"=>"Amount of the transaction",
	"affiliate_id"=>"Affiliate ID",
	"uid"=>"AffiliateTS internal unique user click id",
	"dynamic_parameter"=>"Dynamic Parameter",
	"dynamic_parameter"=>"Dynamic Parameter2"
	);

$arrAccountPixelParams     = $accountPixels;
$arrSalePixelParams        =  $salesPixels;
$strHtmlAccountPixelParams = '<b><u>' . lang('Get Lead/Accounts Pixel') . ':</u></b><br />';
$strHtmlSalePixelParams    = '<b><u>' . lang('Get FTDs Pixel') . ':</u></b><br />';
$acc = 0;

foreach ($arrAccountPixelParams as $k => $v) {
	// var_dump($arrAccountPixelParams);
	// die();
	if($userLevel == "affiliate"){
		  if (in_array($k,$activeAccountPixels)) {
				$acc = 1;
                $strHtmlAccountPixelParams .= '<b>{' . $k . '}</b>&nbsp;-&nbsp;<label style="valign:bottom;">' 
                                           .  lang($v) . '</label><br />';
            }
	}
	else{
    $strHtmlAccountPixelParams .= '<a href="javascript:void(0)"><img style="width:15px;height:15px;" '
                               .  'src="' . (!in_array($k,$activeAccountPixels) ? $set->SSLprefix.'images/docs_red.png' : $set->SSLprefix.'images/docs_green.jpg') 
                               .  '" alt="' . (!in_array($k,$activeAccountPixels) ? 'Activate' : 'Deactivate') 
                               .  '" title="' . (!in_array($k,$activeAccountPixels) ? 'Activate' : 'Deactivate') 
                               .  '" onclick="return pixelParamsUpdate(this, ' . $theID . ', \'account\', \'' . $k . '\');" '
                               .  '/></a>&nbsp;-&nbsp;<b>{' . $k . '}</b>&nbsp;-&nbsp;<label style="valign:bottom;">' . lang($v) . '</label><br />';
	}  
	unset($k, $v);
}
//}
$sale = 0;
if ($arrSalePixelParams)
foreach ($arrSalePixelParams as $k => $v) {
	if($userLevel == "affiliate"){
		 if (in_array($k,$activeSalesPixels)) {
				$sale =1;
                $strHtmlSalePixelParams .= '<b>{' . $k . '}</b>&nbsp;-&nbsp;<label style="valign:bottom;">' 
                                        .  lang($v) . '</label><br />';
            }
	}
	else{
    $strHtmlSalePixelParams .= '<a href="javascript:void(0)"><img style="width:15px;height:15px;" '
                            .  'src="' . (!in_array($k,$activeSalesPixels) ? $set->SSLprefix.'images/docs_red.png' : $set->SSLprefix.'images/docs_green.jpg') 
                            .  '" alt="' . (!in_array($k,$activeSalesPixels) ? 'Activate' : 'Deactivate') 
                            .  '" title="' . (!in_array($k,$activeSalesPixels) ? 'Activate' : 'Deactivate') 
                            .  '" onclick="return pixelParamsUpdate(this, ' . $theID . ', \'sale\',  \'' . $k . '\');" '
                            .  '/></a>&nbsp;-&nbsp;<b>{' . $k . '}</b>&nbsp;-&nbsp;<label style="valign:bottom;">' . lang($v) . '</label><br />';
    }
    unset($k, $v);
}


// object(stdClass)#2 (121) { ["id"]=> string(1) "1" ["qrcode"]=> string(1) "1" ["facebookshare"]=> string(1) "1" ["webTitle"]=> string(34) "Affiliates-Money Affiliate Program" ["webMail"]=> string(28) "support@affiliates-money.com" ["mail_server"]=> string(19) "smtp.googlemail.com" ["mail_username"]=> string(28) "support@affiliates-money.com" ["mail_password"]=> string(8) "4Nub*bM9" ["pending"]=> string(1) "0" ["creative_iframe"]=> string(1) "1" ["creative_mobile_leader"]=> string(1) "1" ["creative_mobile_splash"]=> string(1) "1" ["creative_email"]=> string(1) "1" ["deal_cpl"]=> string(1) "1" ["deal_cpm"]=> string(1) "0" ["deal_cpc"]=> string(1) "1" ["deal_tier"]=> string(1) "1" ["export"]=> string(1) "1" ["terms_link"]=> string(38) "http://www.affiliates-money.com/terms/" ["multi"]=> string(1) "0" ["multi_languages"]=> string(24) "English,Chinese,Japanese" ["sub_com"]=> string(1) "5" ["show_deposit"]=> string(1) "0" ["revenue_formula"]=> string(48) "{deposits}-({bonus}+{withdrawals}+{chargebacks})" ["qualify_type"]=> string(6) "trades" ["qualify_amount"]=> string(1) "0" ["analyticsCode"]=> string(0) "" ["isNetwork"]=> string(1) "0" ["reportsToHide"]=> string(34) "|-traffic|-traffic-am|-traffic-ad|" ["showMiminumDepositOnAffAccount"]=> string(1) "0" ["defaultTimeFrameForAffiliate"]=> string(10) "This Month" ["multiMerchantsPerTrader"]=> string(1) "0" ["hideNetRevenueForNonRevDeals"]=> string(1) "1" ["hideFTDamountForCPADeals"]=> string(1) "1" ["SMTPSecure"]=> string(3) "TLS" ["SMTPAuth"]=> string(1) "1" ["mail_Port"]=> string(3) "587" ["availablePayments"]=> string(50) "paypal|skrill|wire|neteller|webmoney|chinaunionpay" ["autoRelateSubAffiliate"]=> string(1) "0" ["dashBoardMainTitle"]=> string(16) "Affiliates Money" ["showRealFtdToAff"]=> string(1) "0" ["show_credit_as_default_for_new_affiliates"]=> string(1) "1" ["showCreditForAM"]=> string(1) "1" ["blockAffiliateLogin"]=> string(1) "0" ["showAllCreativesToAffiliate"]=> string(1) "1" ["showVolumeForAffiliate"]=> string(1) "0" ["showAffiliateRiskForAffiliate"]=> string(1) "0" ["newsletterCheckboxCaption"]=> string(107) "I would like to receive the monthly payment statement, account information & affiliate newsletter by email." ["affiliateNewsletterCheckboxValue"]=> string(1) "1" ["utmtags"]=> string(0) "" ["showDCPAonAffiliateComStruc"]=> string(1) "0" ["hideWithdrawalAmountForCPADeals"]=> string(1) "1" ["hideBonusAmountForCPADeals"]=> string(1) "1" ["hideDepositAmountForCPADeals"]=> string(1) "1" ["hideTotalDepositForCPADeals"]=> string(1) "1" ["showDealTypeHistoryToAM"]=> string(1) "1" ["hideFrozenOnCPAdeals"]=> string(1) "1" ["AllowDealChangesByManager"]=> string(1) "1" ["autoRelateNewAffiliateToAllMerchants"]=> string(1) "0" ["emailFooterImageURL"]=> string(0) "" ["emailHeaderImageURL"]=> string(0) "" ["rowsNumberAfterSearch"]=> string(2) "20" ["emailSignature"]=> string(0) "" ["hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals"]=> string(1) "1" ["showPositionsRevShareDeal"]=> string(1) "0" ["dateOfMonthlyPayment"]=> string(0) "" ["extraAgreement2Name"]=> string(0) "" ["extraAgreement2Link"]=> string(0) "" ["extraAgreement3Name"]=> string(0) "" ["extraAgreement3Link"]=> string(0) "" ["availableCurrencies"]=> string(23) "GBP|USD|CYN|EUR|AUD|CAD" ["forceParamsForTracker"]=> string(0) "" ["showDocumentsModule"]=> string(1) "0" ["AskDocTypeCompany"]=> string(1) "0" ["AskDocTypeAddress"]=> string(1) "0" ["AskDocTypePassport"]=> string(1) "0" ["AskDocSentence"]=> string(0) "" ["hideMarketingSectionOnAffiliateRegPage"]=> string(1) "0" ["hideInvoiceSectionOnAffiliateRegPage"]=> string(1) "0" ["affiliateLoginImage"]=> string(66) "http://www.affiliates-money.com/wp-content/uploads/2015/12/new.jpg" ["AllowAffiliateDuplicationOnCampaignRelation"]=> string(1) "0" ["ShowIMUserOnAffiliatesList"]=> string(1) "0" ["introducingBrokerInterface"]=> string(1) "0" ["hideCountriesOnRegistration"]=> string(0) "" ["ShowEmailsOnTraderReportForAffiliate"]=> string(1) "0" ["ShowEmailsOnTraderReportForAdmin"]=> string(1) "1" ["blockAccessForManagerAndAdmins"]=> string(1) "0" ["combinedPixelOption"]=> string(17) "lead|account|sale" ["CouponTrackerIsStrongerThanCtag"]=> string(1) "1" ["apiAccessType"]=> string(0) "" ["apiStaticIP"]=> string(0) "" ["apiToken"]=> string(0) "" ["ShowGraphOnDashBoards"]=> string(1) "1" ["isOffice365"]=> string(1) "1" ["defaultLangOfSystem"]=> string(3) "ENG" ["exportAffiliateName"]=> string(0) "" ["logoPath"]=> string(54) "http://app.affiliates-money.com/images/header/logo.png" ["billingLogoPath"]=> string(0) "" ["faviconPath"]=> string(0) "" ["showTitleOnLoginPage"]=> string(1) "1" ["secondaryPoweredByLogo"]=> string(0) "" ["secondaryPoweredByLogoHrefUrl"]=> string(0) "" ["hidePoweredByABLogo"]=> string(1) "0" ["path_dir"]=> string(0) "" ["ftp_server"]=> string(9) "localhost" ["ftp_user"]=> string(30) "ftp99@app.affiliates-money.com" ["ftp_pass"]=> string(8) "g34bt4t3" ["ftp_path"]=> string(13) "/public_html/" ["basepage"]=> string(21) "/admin/affiliates.php" ["uri"]=> string(52) "/admin/affiliates.php?act=new&id=500&toggleTo=tab_10" ["http_host"]=> string(24) "app.affiliates-money.com" ["webAddress"]=> string(32) "http://app.affiliates-money.com/" ["webAddressHttp"]=> string(32) "http://app.affiliates-money.com/" ["webAddressHttps"]=> string(33) "https://app.affiliates-money.com/" ["Https"]=> string(3) "off" ["userIP"]=> string(13) "141.101.99.97" ["refe"]=> string(83) "http://app.affiliates-money.com/admin/affiliates.php?act=new&id=500&toggleTo=tab_10" ["itemsLimit"]=> int(15) ["getFolder"]=> array(3) { [0]=> string(0) "" [1]=> string(5) "admin" [2]=> string(14) "affiliates.php" } ["userInfo"]=> array(22) { ["id"]=> string(1) "1" ["preferedCurrency"]=> string(3) "USD" ["rdate"]=> string(19) "2012-11-12 20:53:10" ["ip"]=> string(14) "162.158.93.209" ["chk_ip"]=> string(0) "" ["valid"]=> string(1) "1" ["relatedMerchantID"]=> string(1) "1" ["lang"]=> string(3) "ENG" ["level"]=> string(5) "admin" ["username"]=> string(9) "affiliate" ["password"]=> string(32) "b6946f7997e67608397333856a0b95ce" ["first_name"]=> string(3) "Nir" ["last_name"]=> string(5) "Cohen" ["email"]=> string(25) "info@affiliatebuddies.com" ["lastactive"]=> string(19) "2016-01-18 11:52:29" ["logged"]=> string(1) "1" ["group_id"]=> string(1) "0" ["phone"]=> string(0) "" ["IMUser"]=> string(0) "" ["zopimChat"]=> string(0) "" ["bigPic"]=> string(0) "" ["productType"]=> string(12) "BinaryOption" } ["multiMerchants"]=> string(1) "0" }
//ADD New
$set->content .= '<link href="'.$set->SSLprefix.'js/Smart-Wizard/styles/demo_style.css" rel="stylesheet" type="text/css">
<link href="'.$set->SSLprefix.'js/Smart-Wizard/styles/smart_wizard.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="'.$set->SSLprefix.'js/Smart-Wizard/js/jquery.smartWizard.js"></script>
<script>
var HashCode = "";
var  paramsize = 0;
var pixelGetPostMethod = "post";
var pixelGetPostURL = "";
</script>
<style>
.jq-dropdown-menu li a{
	font-size: 14px;
    color: #000 !important
}
.jq-dropdown-menu input[type=submit]{
	background-color:transparent !important;
	text-transform:capitalize !important;
	padding-left:10px !important;
	color:black !important;
}
.wizard_clientSide{
	bottom: 50px;
    position: absolute;
    width: 80%;
}
td.pixeltypesradios label {
    -webkit-appearance: button;
    /* WebKit */
    -moz-appearance: button;
    /* Mozilla */
    -o-appearance: button;
    /* Opera */
    -ms-appearance: button;
    /* Internet Explorer */
    appearance: button;
    /* CSS3 */
	color: #FFF;
    background-color: #ff931e;
    border: 0;
    font-size: 14px;
    text-transform: uppercase;
    padding: 5px 30px 5px 30px;
    cursor: pointer;
	border:1px solid #ff931e;
}
input[name=wizard_pixelType] {
    display:none
}
.swMain .buttonNextNew,.btnTotalFired {
  margin:5px 3px 0 3px;
  padding:5px;
  text-decoration: none;
  text-align: center;
  font: bold 13px Verdana, Arial, Helvetica, sans-serif;
  width:100px;
  color:#FFF !important;
  outline-style:none;
  background-color:   #5A5655;
  border: 1px solid #5A5655;
  -moz-border-radius  : 5px; 
  -webkit-border-radius: 5px;    
}
.btnTotalFired{
	display:block;
	width:80px !Important;
}
a.btnTotalFired:hover{
	opacity:0.92 !important;
}
</style>
';
$set->content .= '
	<div class="pixelmonitor">
		<div class="pixelmonitor-div creative-page-filter">
			<div class="search-wrp">
				<p>Search creative</p>
				<div class="search-box">
					<input type="text" name="q" value="'.$q.'" />
					<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
				</div>
			</div>
			<div class="new-pixel-moniter">
				<button type="button" class="btn" data-toggle="modal" data-target="#exampleModalCenter">
					New Pixel Monitor
				</button>
		  
		  <!-- Modal -->
		  <div class="modal fade HtmlCode-modal" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
		  <div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content html-modal-content">
			  <div class="modal-header html-model-header">
				<h5 class="modal-title" id="exampleModalLongTitle">New Pixel Monitor</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				  <span aria-hidden="true">&times;</span>
				</button>
			  </div>
			  <div class="modal-body html-model-body">
				<div class="html-code-body">
				<div id="tabs1-wizard">
				<form id="frmWizard"  method="POST">
				<input type="hidden" name="act" value="save_wizard_pixel" />
				<input type="hidden" name="pixel_id" value="'. (isset ($pixel_id)?$pixel_id:0).'">
				<input type="hidden" name="affiliate_id" value="'.$id.'" />
				<!-- Tabs -->
						<div id="wizard" class="swMain">
				  <ul>
					  <li><a href="#step-1">
					<label class="stepNumber">1</label>
					<span class="stepDesc">
					   '. lang('Pixel Type') .'
					</span>
				</a></li>
					  <li><a href="#step-2">
					<label class="stepNumber">2</label>
					<span class="stepDesc">
					   '. lang("Trigger") .'
					   
					</span>
				</a></li>
					  <li><a href="#step-3">
					<label class="stepNumber">3</label>
					<span class="stepDesc">
					   '. lang('Pixel Code') .'
					   
					</span>
				 </a></li>
					  <li><a href="#step-4">
					<label class="stepNumber">4</label>
					<span class="stepDesc">
					   '. lang("Method") .'
					   
					</span>
				</a></li>
				<li><a href="#step-5">
					<label class="stepNumber">5</label>
					<span class="stepDesc">
					   '. ($userLevel == "affiliate"? lang("Finish") .'<br />
					   <small>'. lang('') .'</small>':lang("Activate") .'<br />
					   <small>'. lang('Activate Pixel') .'</small>').'
					</span>
				</a></li>
				  </ul>
				  <div id="step-1" class="step-1-modal">	
				<h2 class="StepTitle">'. lang("Step 1 : Select Pixel Type") .'</h2>
				<table cellspacing="3" cellpadding="3" align="center">
						  <tr>
							<td align="center" colspan="3">&nbsp;</td>
						  </tr>
						'.($set->showProductsPlace==1?'
						  <tr>
							<td class="pixeltypesradios" align="left" colspan=2>
							'.(isset($pixel_id)&&!empty($pixel_id)?'
							  <label><input type="radio"  name="wizard_pixelType" value="products" '. (!empty($pixel_data['product_id'])?'checked':'') .'>'. lang('Products') .'</label>
							  <label><input type="radio"  name="wizard_pixelType" value="merchants" '. (!empty($pixel_data['merchant_id'])?'checked':'') .'> '. lang('Merchants') .'</label>'
							  : 
							  '<label><input type="radio"  name="wizard_pixelType" value="products"> '. lang('Products') .'</label>
							  <label><input type="radio"  name="wizard_pixelType" value="merchants"> '. lang('Merchants') .'</label>').'
						  </td>
							<td align="left"><span id="msg_pixelType" style="color:red"></span>&nbsp;</td>
						  </tr>
						<tr class="wizard_productRow" '.(isset($pixel_id)?(!empty($pixel_data['product_id'])?'style="display:block"':'style="display:none"'):'style="display:none"') .'>
							<td align="left">'. lang('Select Product') .' :</td>
							<td align="left">
							 <select name="wizard_productId" style="width:140px" >'.listProducts(isset($pixel_id) && !empty($pixel_data['product_id'])?$pixel_data['product_id']:0).'</select>
						  </td>
							<td align="left"><span id="msg_product" style="color:red"></span>&nbsp;</td>
						  </tr>':'<input type="hidden"  name="wizard_pixelType" value="merchants" '. (!empty($pixel_data['merchant_id'])?'checked':'') .'>'). 
						($set->showProductsPlace==1 && !isset($pixel_id)?'<tr class="wizard_merchantRow" style="display:none">':(isset($pixel_id)?(!empty($pixel_data['merchant_id'])?'
						<tr class="wizard_merchantRow">':'
						<tr class="wizard_merchantRow" style="display:none">'):'
						<tr class="wizard_merchantRow select-new-pixel" >')) .'<td align="left" class="pix-modal-details pb-0">'. lang('Select Merchants') .' :</td>
							<td align="left">
							 <select class="select-mer" name="wizard_merchantId" style="width:140px">'.listMerchants( isset($pixel_id) && !empty($pixel_data['merchant_id'])?$pixel_data['merchant_id']:0 ).'</select>
						  </td>
							<td align="left"><span id="msg_merchant" style="color:red"></span>&nbsp;</td>
						  </tr> 
						<tr  class="wizard_creatives" '.(isset($pixel_id)?'':'style="display:none"').'>
						<td colspan=2 style="text-align:left" class="pix-modal-details">'. lang("What creative you would like to relate this pixel to") .': </td>
						</tr>
						<tr  class="wizard_creatives" '.(isset($pixel_id)?'':'style="display:none"').'>
						<td colspan=2 style="text-align:left" class="o-step-label">
						  <label><input type="radio"  name="wizard_banners" value="all_banners" '. ($pixel_data['banner_id']==0?'checked':'') .'>'. lang('All Creatives') .'</label>
						  <label><input type="radio"  name="wizard_banners" value="select_banner" '. (!empty($pixel_data['banner_id'])?'checked':'') .'> 
						  <select name="wizard_selectBanner" style="width:140px;">
						  '.(isset($pixel_id)?
						  $listCreatives_wizard:'')
						  .'
						  </select></label>
						</td>
						</tr>
					 </table>          			
			</div>
				  <div id="step-2">
				<h2 class="StepTitle">'. lang("Step 2 : Trigger") .'</h2>	
				<table cellspacing="3" cellpadding="3" align="center">
						  <tr>
							<td align="center" colspan="3">&nbsp;</td>
						  </tr>        
						  <tr class="select-trigger-tr">
							<td align="left" class="SelectTrigger">'. lang('Select Trigger') .'</td>
							<td align="left">
							  <select name="wizard_trigger" class="select-mer">
										' . $trigerOptions . '
								</select>
						  </td>
							<td align="left"><span id="msg_trigger" style="color:red"></span>&nbsp;</td>
						  </tr>
					 </table>        
			</div>                      
				  <div id="step-3" style="min-height:300px;height:auto"> 
				<h2 class="StepTitle">'. lang('Step 3 : Create Pixel Code') .'</h2>	
					<table cellspacing="3" cellpadding="3" align="center">
						  <tr>
							<td align="center" colspan="3">&nbsp;</td>
						  </tr>        
						  <tr class="select-trigger-tr">
							<td align="left" valign="top" class="pix-modal-details pb-0">'. lang('Pixel Code') .'</td>
							<td align="left">
							  <textarea class="pixel-code-textarea" id="wizard_pixelCode" name="wizard_pixelCode" rows=5 class="txtBox">'. (isset($pixel_id) && !empty($pixel_data['pixelCode'])?$pixel_data['pixelCode']:'') .'</textarea>
						  </td>
							<td align="left"><span id="msg_pixelCode" style="color:red"></span>&nbsp;</td>
						  </tr>
					  </table>              
			</div>
				  <div id="step-4">
				<h2 class="StepTitle">'. lang(' Step 4 : Select Method') .'</h2>	
				<table cellspacing="3" cellpadding="3" align="center">
						  <tr>
							<td align="center" colspan="3">&nbsp;</td>
						  </tr>
						 '.(isset($pixel_id)?($pixel_data['method']!="client"?'
							<tr class="pixelMethodType">
							<td align="center" colspan=2 class="step-4-method">
							  <input type="radio"  name="wizard_pixelMethodType" value="automatic" '.(!isset($pixel_id)?'checked':'').'> '. lang('Automatic') .'
							  <input type="radio"  name="wizard_pixelMethodType" value="manual" '.(isset($pixel_id)?'checked':'').'> '. lang('Manual') .'
							</td>
						  </tr>
						<tr>
							<td align="center" colspan="3">&nbsp;</td>
						  </tr>
						<tr class="pixelMethod_automatic" '.(isset($pixel_id)?'style="display:none"':'').'>
							<td align="left" colspan=2 class="otherPixelMethodLine" style="display:none">
							<p>'. lang('Please click') . ' <a href="#" class="buttonNextNew">' . lang('Next') .'</a> ' .  lang('if pixel has fired.') .'<p><br/>
							<p>' . lang('If not, Please click the button below to try another way of firing the pixel.') .'</p>
							</td>
						</tr>
						  <tr class="pixelMethod_automatic" '.(isset($pixel_id)?'style="display:none"':'').'>
							<td align="center" colspan=3 >
							  <input type="button" name="pixelCode_test" value="'. lang('Test Now') .'">
						  </td>
							<td align="left"><span id="msg_method"></span>&nbsp;</td>
						  </tr>':''):'
						<tr class="pixelMethodType">
							<td align="left" colspan=2>
							<div class="step-4-label">
							  <input type="radio"  name="wizard_pixelMethodType" value="automatic" checked> '. lang('Automatic') .'
							  </div>
							  <div class="step-4-label">							  
							  <input type="radio"  name="wizard_pixelMethodType" value="manual"> '. lang('Manual') .'
							  </div>
							  </td>
						  </tr>
						<tr>
							<td align="center" colspan="3">&nbsp;</td>
						  </tr>
						<tr class="pixelMethod_automatic">
							<td align="left" colspan=2 class="otherPixelMethodLine" style="display:none">
							<p>'. lang('Please click') . ' <a href="#" class="buttonNextNew">' . lang('Next') .'</a> ' .  lang('if pixel has fired.') .'<p><br/>
							<p>' . lang('If not, Please click the button below to try another way of firing the pixel.') .'</p>
							</td>
						</tr>
						  <tr class="pixelMethod_automatic">
							<td align="left" colspan=3 class="test-now-button">
							  <input type="button" name="pixelCode_test" value="'. lang('Test Now') .'">
						  </td>
							<td align="left"><span id="msg_method"></span>&nbsp;</td>
						  </tr>
						').'
						<tr class="pixelMethod_manual" '.(isset($pixel_id)?'':'style="display:none"').'>
							<td align="left" colspan=2 >
							  <select name="wizard_pixelmethod" id="pixelmethod">
							  '.(isset($pixel_id)?($pixel_data['method']=="client"?'
							  <option value="client" selected>'.lang('Client Side').'</option>
							  ':'
								<option value="post" '. ($pixel_data['method']=='post'?'selected':'') .'>'.('Server To Server').' - POST</option>
								<option value="get" '. ($pixel_data['method']=='get'?'selected':'') .'>'.('Server To Server').' - GET</option>
							  '):'').'
							  </select>
						  </td>
							<td align="left"><span id="msg_method" style="color:red"></span>&nbsp;</td>
						  </tr>          			
					 </table>                 	
				<table cellspacing="3" cellpadding="3" align="center" class="wizard_clientSide"  '.(isset($pixel_id)&&$pixel_data['method']=="client"?'style="display:block"':'style="display:none"').'>
				<tr>
				<td>
					*  '.lang('Client Side pixels works with Accounts and Leads types'). '<br/>
				</td>
				</tr>
				<tr>
				<td>
				*  ' . lang('In order to use Client Side pixels you need to fire the result you get from the following url').': <u>' . $set->webAddress . 'pixel.php?act={ account  /  lead }&clientMode=1&ctag={ FROM THE TRACKING LINK } </u>
				</td>
				</tr>
				</table>			   
			</div>
			<div id="step-5">
				<h2 class="StepTitle">'. ($userLevel =="affiliate" ? lang('') : lang('Step 5 : Activate Pixel')) .'</h2>	
				<table cellspacing="3" cellpadding="3" align="center" class="step-5-finish">
						  <tr>
							<td align="center" colspan="3">&nbsp;</td>
						  </tr>        
						'.($userLevel =="affiliate"?'
						<tr>
							<td><h3 class="finish-step">'. lang("The pixel will be idle until the Affiliate Manager will approve it.") .'</h3></td>
						  </tr>          			
						<tr>
						
						':'
						  <tr>
							<td><h3>'. lang("Would you like to activate the pixel?") .'</h3></td>
							<td align="left" colspan=2>
							<input type="hidden" name="wizard_pixelValid" value='. (isset($pixel_id)?$pixel_data['valid']:0) .'>
						  </td>
						  </tr>          			
						<tr>
						<td colspan=3>
						<input type="button" name="wizard-activatePixel" value="'.lang('Activate') . '" style="font-size:28px;margin:0 auto;display:table-cell">
						</td>
						</tr>').'
					 </table>                 			
			</div>
			  </div>
				<!-- End SmartWizard Content -->  		
				</form> 
		</div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
			</div>
		</div>
		<div class="pixel-table">
			<div class="row">
				<div class="col-md-12">
					<div class="top-performing-creative h-full">
							<div class="performing-creative-table">
								<div class="table-responsive">
									<table class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
										<thead>
											<tr>
											<th scope="col">'.lang('Pixel Type').'</th>
											<th scope="col">'.lang('Merchant').'</th>
											<th scope="col">'.lang('Creative').'</th>
											<th scope="col">'.lang('Pixel Code').'</th>
											<th scope="col">'.lang('Type').'</th>
											<th scope="col" class="text-align-center">'.lang('Total Fired').'</th>
											<th scope="col">'.lang('Method').'</th>
											<th scope="col" class="text-align-center">'.lang('Status').'</th>
											<th scope="col">'.lang('Action').'</th>
											</tr>
										</thead>
										<tfoot>
											<tr>
													<td align="center"> <select name="pixelType" id="pixelType"><option value="merchants" selected>'.lang('Merchants').'</option>'.($set->showProductsPlace==1 ? '<option value="products">'.lang('Products').'</option>':'').'</select></td>
						                            <td align="left" style="display:none;" class="pixelProducts"><select name="db[product_id]" style="width:140px">'.listProducts().'</select></td>
													<td align="left" class="pixelMerchants"><select name="db[merchant_id]" style="width:140px">'.listMerchants().'</select></td>
													<td align="left"><select name="db[banner_id]" class="form-control">'.$listCreatives.'</select></td>
						                            <td align="center"><input type="text" name="db[pixelCode]" onblur="checkHtml(this,0)" class="table-input"></input></td>
						                            <td align="center"><select name="db[type]" class="form-control">
															<!--option value="lead">'.lang('Lead').'</option><option value="account">'.lang('Account').'</option><option value="sale">'.lang('FTD').'</option-->
															' . $trigerOptions . '
														</select></td>
						                            <td align="center">'.$set->totalFired.'</td>
						                            <td align="center"><select class="form-control" name="db[method]" id="dbmethod0">
															<option value="post">'.('Server To Server').' - POST</option>
															<option value="get">'.('Server To Server').' - GET</option>
															<option value="client">'.('Client Side').'</option></select></td>
						                            <td class="span-green"><span></span></td>
						                            <td align="center">
						                            	<div class="dropdown">
															<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
															<i class="fa fa-ellipsis-v"></i>
															</button>
															<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
																<input type="submit"  class="dropdown-item" value="'.lang('Add New').'" />
															</div>
														</div>
													</td>
						                    </tr>
										</tfoot>
									</table>
								</div>
							</div>
					</div>
				</div>
			</div>
		</div>
	</div>


<div data-tab="api_access"  style="padding-top:20px;">
<div class="normalTableTitle" style="cursor: pointer;">'.(isset($pixel_id) && !empty($pixel_id)?lang('Edit Pixel'):lang('New')).'</div>
    <div id="tab_10" style="width: 100%; background: #F8F8F8;">
	<div id="tab-container3" class="tab-container">
		 <ul class="etabs">
		   <li class="tab" id="tab-2"><a href="#tabs1-wizard">'. lang('Wizard') .'</a></li> 
		   <li class="tab" id="tab-1"><a href="#tabs1-manual">'. lang('Manual') .'</a></li>
		 </ul>
		  <div class="panel-container">
	<div id="tabs1-wizard">
			<form id="frmWizard"  method="POST">
			<input type="hidden" name="act" value="save_wizard_pixel" />
			<input type="hidden" name="pixel_id" value="'. (isset ($pixel_id)?$pixel_id:0).'">
            <input type="hidden" name="affiliate_id" value="'.$id.'" />
			<!-- Tabs -->
					<div id="wizard" class="swMain">
  			<ul>
  				<li><a href="#step-1">
                <label class="stepNumber">1</label>
                <span class="stepDesc">
                   '. lang('Pixel Type') .'<br />
                   <small>'. lang('Product or Merchant') .'</small>
                </span>
            </a></li>
  				<li><a href="#step-2">
                <label class="stepNumber">2</label>
                <span class="stepDesc">
                   '. lang("Trigger") .'<br />
                   <small>'. lang('Select Trigger Type') .'</small>
                </span>
            </a></li>
  				<li><a href="#step-3">
                <label class="stepNumber">3</label>
                <span class="stepDesc">
                   '. lang('Pixel Code') .'<br />
                   <small>'. lang('Enter Pixel Code') .'</small>
                </span>
             </a></li>
  				<li><a href="#step-4">
                <label class="stepNumber">4</label>
                <span class="stepDesc">
                   '. lang("Method") .'<br />
                   <small>'. lang('Select Method') .'</small>
                </span>
            </a></li>
			<li><a href="#step-5" id="active-step-5">
                <label class="stepNumber">5</label>
                <span class="stepDesc">
                   '. ($userLevel == "affiliate"? lang("Finish") .'<br />
                   <small>'. lang('Finish Pixel') .'</small>':lang("Activate") .'<br />
                   <small>'. lang('Activate Pixel') .'</small>').'
                </span>
            </a></li>
  			</ul>
  			<div id="step-1">	
            <h2 class="StepTitle">'. lang("Step 1 : Select Pixel Type") .'</h2>
            <table cellspacing="3" cellpadding="3" align="center">
          			<tr>
                    	<td align="center" colspan="3">&nbsp;</td>
          			</tr>
					'.($set->showProductsPlace==1?'
          			<tr>
                    	<td class="pixeltypesradios" align="left" colspan=2>
						'.(isset($pixel_id)&&!empty($pixel_id)?'
                    	  <label><input type="radio"  name="wizard_pixelType" value="products" '. (!empty($pixel_data['product_id'])?'checked':'') .'>'. lang('Products') .'</label>
						  <label><input type="radio"  name="wizard_pixelType" value="merchants" '. (!empty($pixel_data['merchant_id'])?'checked':'') .'> '. lang('Merchants') .'</label>'
						  : 
						  '<label><input type="radio"  name="wizard_pixelType" value="products"> '. lang('Products') .'</label>
						  <label><input type="radio"  name="wizard_pixelType" value="merchants"> '. lang('Merchants') .'</label>').'
                      </td>
                    	<td align="left"><span id="msg_pixelType" style="color:red"></span>&nbsp;</td>
          			</tr>
					<tr class="wizard_productRow" '.(isset($pixel_id)?(!empty($pixel_data['product_id'])?'style="display:block"':'style="display:none"'):'style="display:none"') .'>
                    	<td align="right">'. lang('Select Product') .' :</td>
                    	<td align="left">
                    	 <select name="wizard_productId" style="width:140px" >'.listProducts(isset($pixel_id) && !empty($pixel_data['product_id'])?$pixel_data['product_id']:0).'</select>
                      </td>
                    	<td align="left"><span id="msg_product" style="color:red"></span>&nbsp;</td>
          			</tr>':'<input type="hidden"  name="wizard_pixelType" value="merchants" '. (!empty($pixel_data['merchant_id'])?'checked':'') .'>'). 
					($set->showProductsPlace==1 && !isset($pixel_id)?'<tr class="wizard_merchantRow" style="display:none">':(isset($pixel_id)?(!empty($pixel_data['merchant_id'])?'
					<tr class="wizard_merchantRow">':'
					<tr class="wizard_merchantRow" style="display:none">'):'
					<tr class="wizard_merchantRow" >')) .
					'<td align="right">'. lang('Select Merchants') .' :</td>
                    	<td align="left">
                    	 <select name="wizard_merchantId" style="width:140px">'.listMerchants( isset($pixel_id) && !empty($pixel_data['merchant_id'])?$pixel_data['merchant_id']:0 ).'</select>
                      </td>
                    	<td align="left"><span id="msg_merchant" style="color:red"></span>&nbsp;</td>
          			</tr> 
					
					<tr>
					<td colspan=2 style="height:20px"></td>
					</tr>
					<tr  class="wizard_creatives" '.(isset($pixel_id)?'':'style="display:none"').'>
					<td colspan=2 style="text-align:center">'. lang("What creative you would like to relate this pixel to") .': </td>
					</tr>
					<tr  class="wizard_creatives" '.(isset($pixel_id)?'':'style="display:none"').'>
					<td colspan=2 style="text-align:center">
					  <label><input type="radio"  name="wizard_banners" value="all_banners" '. ($pixel_data['banner_id']==0?'checked':'') .'>'. lang('All Creatives') .'</label>
					  <label><input type="radio"  name="wizard_banners" value="select_banner" '. (!empty($pixel_data['banner_id'])?'checked':'') .'> 
					  <select name="wizard_selectBanner" style="width:140px;">
					  '.(isset($pixel_id)?
					  $listCreatives_wizard:'')
					  .'
					  </select></label>
					</td>
					</tr>
  			   </table>          			
        </div>
  			<div id="step-2">
            <h2 class="StepTitle">'. lang("Step 2 : Trigger") .'</h2>	
            <table cellspacing="3" cellpadding="3" align="center">
          			<tr>
                    	<td align="center" colspan="3">&nbsp;</td>
          			</tr>        
          			<tr>
                    	<td align="right">'. lang('Select Trigger') .'</td>
                    	<td align="left">
                    	  <select name="wizard_trigger">
									' . $trigerOptions . '
							</select>
                      </td>
                    	<td align="left"><span id="msg_trigger" style="color:red"></span>&nbsp;</td>
          			</tr>
  			   </table>        
        </div>                      
  			<div id="step-3" style="min-height:300px;height:auto"> 
            <h2 class="StepTitle">'. lang('Step 3 : Create Pixel Code') .'</h2>	
				<table cellspacing="3" cellpadding="3" align="center">
          			<tr>
                    	<td align="center" colspan="3">&nbsp;</td>
          			</tr>        
          			<tr>
                    	<td align="right" valign="top">'. lang('Pixel Code') .'</td>
                    	<td align="left">
                    	  <textarea id="wizard_pixelCode" name="wizard_pixelCode" rows=5 class="txtBox">'. (isset($pixel_id) && !empty($pixel_data['pixelCode'])?$pixel_data['pixelCode']:'') .'</textarea>
						  <br/><br/>
						  <input type="button" name="btnGetPixelCode" class="btnGetPixelCode" id="btnGetPixelCode" value = "'. lang('Analyze') .'" disabled>
                      </td>
                    	<td align="left"><span id="msg_pixelCode" style="color:red"></span>&nbsp;</td>
          			</tr>
          		</table>              
        </div>
  			<div id="step-4">
            <h2 class="StepTitle">'. lang(' Step 4 : Select Method') .'</h2>	
            <table cellspacing="3" cellpadding="3" align="center">
          			<tr>
                    	<td align="center" colspan="3">&nbsp;</td>
          			</tr>
					 '.(isset($pixel_id)?($pixel_data['method']!="client"?'
						<tr class="pixelMethodType">
                    	<td align="center" colspan=2>
						  <input type="radio"  name="wizard_pixelMethodType" value="automatic" '.(!isset($pixel_id)?'checked':'').'> '. lang('Automatic') .'
						  <input type="radio"  name="wizard_pixelMethodType" value="manual" '.(isset($pixel_id)?'checked':'').'> '. lang('Manual') .'
						</td>
          			</tr>
					<tr>
                    	<td align="center" colspan="3">&nbsp;</td>
          			</tr>
					<tr class="pixelMethod_automatic" '.(isset($pixel_id)?'style="display:none"':'').'>
                    	<td align="left" colspan=2 class="otherPixelMethodLine" style="display:none">
						<p>'. lang('Please click') . ' <a href="#" class="buttonNextNew">' . lang('Next') .'</a> ' .  lang('if pixel has fired.') .'<p><br/>
						<p>' . lang('If not, Please click the button below to try another way of firing the pixel.') .'</p>
						</td>
					</tr>
          			<tr class="pixelMethod_automatic" '.(isset($pixel_id)?'style="display:none"':'').'>
                    	<td align="center" colspan=3 >
                    	  <input type="button" name="pixelCode_test" value="'. lang('Test Now') .'">
                      </td>
                    	<td align="left"><span id="msg_method"></span>&nbsp;</td>
          			</tr>':''):'
					<tr class="pixelMethodType">
                    	<td align="center" colspan=2>
						  <input type="radio"  name="wizard_pixelMethodType" value="automatic" checked> '. lang('Automatic') .'
						  <input type="radio"  name="wizard_pixelMethodType" value="manual"> '. lang('Manual') .'
						</td>
          			</tr>
					<tr>
                    	<td align="center" colspan="3">&nbsp;</td>
          			</tr>
					<tr class="pixelMethod_automatic">
                    	<td align="left" colspan=2 class="otherPixelMethodLine" style="display:none">
						<p>'. lang('Please click') . ' <a href="#" class="buttonNextNew">' . lang('Next') .'</a> ' .  lang('if pixel has fired.') .'<p><br/>
						<p>' . lang('If not, Please click the button below to try another way of firing the pixel.') .'</p>
						</td>
					</tr>
          			<tr class="pixelMethod_automatic">
                    	<td align="center" colspan=3 >
                    	  <input type="button" name="pixelCode_test" value="'. lang('Test Now') .'">
                      </td>
                    	<td align="left"><span id="msg_method"></span>&nbsp;</td>
          			</tr>
					').'
					<tr class="pixelMethod_manual" '.(isset($pixel_id)?'':'style="display:none"').'>
                    	<td align="left" colspan=2 >
                    	  <select name="wizard_pixelmethod" id="pixelmethod">
						  '.(isset($pixel_id)?($pixel_data['method']=="client"?'
						  <option value="client" selected>'.lang('Client Side').'</option>
						  ':'
							<option value="post" '. ($pixel_data['method']=='post'?'selected':'') .'>'.('Server To Server').' - POST</option>
							<option value="get" '. ($pixel_data['method']=='get'?'selected':'') .'>'.('Server To Server').' - GET</option>
						  '):'').'
						  </select>
                      </td>
                    	<td align="left"><span id="msg_method" style="color:red"></span>&nbsp;</td>
          			</tr>          			
  			   </table>                 	
			<table cellspacing="3" cellpadding="3" align="center" class="wizard_clientSide"  '.(isset($pixel_id)&&$pixel_data['method']=="client"?'style="display:block"':'style="display:none"').'>
			<tr>
			<td>
				*  '.lang('Client Side pixels works with Accounts and Leads types'). '<br/>
			</td>
			</tr>
			<tr>
			<td>
			*  ' . lang('In order to use Client Side pixels you need to fire the result you get from the following url').': <u>' . $set->webAddress . 'pixel.php?act={ account  /  lead }&clientMode=1&ctag={ FROM THE TRACKING LINK } </u>
			</td>
			</tr>
			</table>			   
        </div>
		<div id="step-5">
            <h2 class="StepTitle">'. ($userLevel =="affiliate" ? lang('Step 5 : Finish Pixel') : lang('Step 5 : Activate Pixel')) .'</h2>	
            <table cellspacing="3" cellpadding="3" align="center">
          			<tr>
                    	<td align="center" colspan="3">&nbsp;</td>
          			</tr>        
					'.($userLevel =="affiliate"?'
					<tr>
						<td><h3>'. lang("The pixel will be idle until the Affiliate Manager will approve it.") .'</h3></td>
          			</tr>          			
					<tr>
					<td colspan=3 style="text-align:center">
					<input type="button" name="finishNew" class="buttonFinishNew" value="' . lang('Finish') .'" style="font-size:28px;margin:0 auto;display:table-cell">
					</td>
					':'
          			<tr>
						<td><h3>'. lang("Would you like to activate the pixel?") .'</h3></td>
                    	<td align="left" colspan=2>
						<input type="hidden" name="wizard_pixelValid" value='. (isset($pixel_id)?$pixel_data['valid']:0) .'>
                      </td>
          			</tr>          			
					<tr>
					<td colspan=3>
					<input type="button" name="wizard-activatePixel" value="'.lang('Activate') . '" style="font-size:28px;margin:0 auto;display:table-cell">
					</td>
					</tr>').'
  			   </table>                 			
        </div>
  		</div>
			<!-- End SmartWizard Content -->  		
			</form> 
	</div>
		<div id="tabs1-manual">
            <form action="'.$set->SSLprefix.$set->basepage.'" method="post">
            <input type="hidden" name="act" value="save_pixel" />
            <input type="hidden" name="db[affiliate_id]" value="'.$id.'" />
            <table class="normal tblDetails" width="100%" border="0" cellpadding="1" cellspacing="0">
                    <thead><tr>
							<td width="40px" align="left" style="text-align: left;" >'.lang('Pixel Type').'</td>
                            <td width="40px" align="left" style="text-align: left;" >'.lang('Merchant') .($set->showProductsPlace==1 ? '/'.lang('Product') : '' ).'</td>
                            <td width="40px" align="left" style="text-align: left;" >'.lang('Creatives').'</td>
							<td  width="40px" align="center">'.lang('Pixel Code').'</td>
                            <td align="center">'.lang('Type').'</td>
                            <td width="40px"align="center">'.lang('Total Fired').'</td>
                            <td align="center">'.lang('Method').'</td>
                            <td width="40px" align="center">' . lang('Status') . '</td>
                            <td align="center">'.lang('Actions').'</td>
                    </tr></thead><tfoot><tr>
							<td align="center"> <select name="pixelType" id="pixelType"><option value="merchants" selected>'.lang('Merchants').'</option>'.($set->showProductsPlace==1 ? '<option value="products">'.lang('Products').'</option>':'').'</select></td>
                            <td align="left" style="display:none;" class="pixelProducts"><select name="db[product_id]" style="width:140px">'.listProducts().'</select></td>
							<td align="left" class="pixelMerchants"><select name="db[merchant_id]" style="width:140px">'.listMerchants().'</select></td>
							<td align="left"><select name="db[banner_id]" style="width:140px">'.$listCreatives.'</select></td>
                            <td align="center"><textarea name="db[pixelCode]" cols="45" rows="3" onblur="checkHtml(this,0)"></textarea></td>
                            <td align="center"><select name="db[type]">
									<!--option value="lead">'.lang('Lead').'</option><option value="account">'.lang('Account').'</option><option value="sale">'.lang('FTD').'</option-->
									' . $trigerOptions . '
								</select></td>
                            <td align="center"></td>
                            <td align="center"><select name="db[method]" id="dbmethod0">
									<option value="post">'.('Server To Server').' - POST</option>
									<option value="get">'.('Server To Server').' - GET</option>
									<option value="client">'.('Client Side').'</option></select></td>
                            <td></td>
                            <td align="center"><input type="submit" value="'.lang('Add New').'" /></td>
                    </tr></tfoot>
            </table>
            </form>
		</div>
	</div>
	</div>
	</div>
	';
//modal code
$set->content .= '
<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
    <div class="modal-header">
      <span class="close">&times;</span>
      <h2>'. lang('Analyze Pixel Code') .'</h2>
    </div>
    <div class="modal-body">
      <p>
	  <textarea name="modalPixelCode" id="modalPixelCode" rows=5 cols=100 readonly></textarea>
	  <p>'. lang('Please select the value you want for the following parameter:') .'</p>
	  <p>'. lang('If the affiliate expect to get his parameter back with the pixel fire use {p1}') .'</p>
	  <div class="params" style="text-align:center"></div>
	  </p>
      <p>
	  <input type="button" id="previous" name="previous" value="'. lang('Previous') . '">&nbsp;&nbsp;
	  <input type="button" id="next" name="next" value="'. lang('Next') . '">&nbsp&nbsp;
	  <input type="button" id="done" name="done" value="'. lang('Done') . '" style="float:right">
	  </p>
    </div>
    <!--div class="modal-footer">
      <h3>Modal Footer</h3>
    </div-->
  </div>
</div>
<script>
// Get the modal
var modal = document.getElementById("myModal");
// Get the button that opens the modal
var btn = document.getElementById("btnGetPixelCode");
// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];
// When the user clicks the button, open the modal 
/* btn.onclick = function() {
    modal.style.display = "flex";
}
 */
// When the user clicks on <span> (x), close the modal
span.onclick = function() {
	url = $("#modalPixelCode").val();
	console.log(HashCode);
	if(HashCode != "" && typeof HashCode != "undefined"){
		url += "#" + HashCode;
	}
	$("#wizard_pixelCode").val(url);
	modal.style.display = "none";
    modal.style.display = "none";
}
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
    if (event.target == modal) {
		url = $("#modalPixelCode").val();
		console.log(HashCode);
		if(HashCode != "" && typeof HashCode != "undefined"){
			url += "#" + HashCode;
		}
		$("#wizard_pixelCode").val(url);
		modal.style.display = "none";
        modal.style.display = "none";
    }
}
</script>';
//LIST allready added Pixels 
$set->content .='
<style>
	.tooltip{
		display:table-cell !important;
	}
	</style>
<div data-tab="api_access"  style="padding-top:20px;">
<div class="normalTableTitle" style="cursor: pointer;">'.lang('List Pixels').'</div>
    <div id="tab_10" style="width: 100%; background: #F8F8F8;">
	'.(empty($listPixels)?"":'
    <div style="color:green;"><b>* '.($userLevel=="affiliate"? lang('Sale event fire the pixel on any first deposit made by the affiliate') :lang('FTD event fire the pixel on any first deposit made by the affiliate')).'.</b></div></br>
	'. ($userLevel =="affiliate"?
	'  <script type="text/javascript">
                        function confirmPixelUpdate() {
                            return confirm("Edited pixel will be invalid untill manager\'s confirmation");
                        }
                        function confirmPixelDelete() {
                            return confirm("Chosen pixel will be deleted");
                        }
		</script>':'')
	.'
	 <form action="'.$set->SSLprefix.$set->basepage.'" method="post">
            <input type="hidden" name="act" value="save_pixel" />
            <input type="hidden" name="db[affiliate_id]" value="'.$id.'" />
            <table class="normal tblDetails" width="100%" border="0" cellpadding="1" cellspacing="0">
                    <thead><tr>
							<td width="40px" align="left" style="text-align: left;" >'.lang('Pixel Type').'</td>
                            <td width="40px" align="left" style="text-align: left;" >'.lang('Merchant') .($set->showProductsPlace==1 ? '/'.lang('Product') : '' ).'</td>
							<td  width="40px" align="center">'.lang('Creative').'</td>
							<td  width="40px" align="center">'.lang('Pixel Code').'</td>
                            <td align="center">'.lang('Type').'</td>
                            <td width="40px"align="center">'.lang('Total Fired').'</td>
                            <td align="center">'.lang('Method').'</td>
                            <td width="40px" align="center">' . lang('Status') . '</td>
                            <td align="center">'.lang('Actions').'</td>
                    </tr></thead>' . $listPixels . '
			</table>
		</form>
	<table>
	<tr class="clientside" '. ($isClientSideExists?'':'style="display:none"') .'>
		<td style="width:10%;">*  '.lang('Client Side pixels works with Accounts and Leads types'). '</td>
	</tr>
	<tr class="clientside" '. ($isClientSideExists?'':'style="display:none"') .'>
	<td>*  ' . lang('In order to use Client Side pixels you need to fire the result you get from the following url').': <u>' . $set->webAddress . 'pixel.php?act={ account  /  lead }&clientMode=1&ctag={ FROM THE TRACKING LINK } </u></td>
		</tr>
	</table>
	<table>
		<tr>
		<td style="width:350px;">*  '.lang('Variables with green button will be display to this affiliate').'</td>
	</tr>
    <tr><td>&nbsp;</td>
    </tr>
	</table>
	').'
	'.
	($userLevel == "affiliate" && $acc == 0 && $sale ==0?'':'
	<table>
    <tr>
    <td> <b>'.lang('Parameters Replacing').':</b><br /></td>
    </tr>
    <tr>
        <td width="300" valign="top">' . $strHtmlAccountPixelParams . '</td>
        <td valign="top">' . $strHtmlSalePixelParams . '</td>
    </tr>
    </table>
	</div></div>');
	
	
	$set->content .= "
	<script type='text/javascript'>
    $(document).ready(function(){
		
		//load creatives on tab manual
		merchant_val = $('select[name=\'db[merchant_id]\']').val();
		loadListCreatives('merchant',merchant_val,0,'merchant','');
		
		$('select[name=\'db[product_id]\']').on('change',function(){
			prodval = $(this).val();
			loadListCreatives('product',prodval,0,'product','');
		});
		$('select[name=\'pixelType\']').on('change',function(){
			if($(this).val() == 'merchants'){
				merchant_val = $('select[name=\'db[merchant_id]\']').val();
				loadListCreatives('merchant',merchant_val,0,'merchant','');
			}
			else{
				prodval =  $('select[name=\'db[product_id]\']').val();
				loadListCreatives('product',prodval,0,'product','');
			}
		});
		
		$('select[name=\'db[merchant_id]\']').on('change',function(){
			merchant_val = $(this).val();
			loadListCreatives('merchant',merchant_val,0,'merchant','');
		});
		
		".(!empty($pixel_id)?"
		
		loadCreatives ('merchant',merchant_val,". (isset($pixel_id) && !empty($pixel_data['banner_id'])?$pixel_data['banner_id']:0) .",'". $selected_type .  "');":'')."

		". ($set->showProductsPlace ==false?"
			loadCreatives ('merchant',merchant_val,". (isset($pixel_id) && !empty($pixel_data['banner_id'])?$pixel_data['banner_id']:0) .",'". $selected_type .  "');
			$('tr.wizard_creatives').show();
		":"")
		.($set->showProductsPlace==1?"
		  //wizard Pixel Type handle
		$('[name=wizard_pixelType]').on('change',function(){
			if($(this).val() =='products')
			{
				$('tr.wizard_productRow').show();
				$('tr.wizard_merchantRow').hide();
				$('tr.wizard_creatives').show();
				product_id = $('select[name=wizard_productId]').val();
				".(isset($pixel_id) && $selected_type == "product" ?(!empty($pixel_data["banner_id"])?"$('input:radio[value=select_banner]').prop('checked',true);":'') :"$('input:radio[value=all_banners]').prop('checked',true);") ."
				loadCreatives ('product',product_id,". (isset($pixel_id) && !empty($pixel_data['banner_id'])?$pixel_data["banner_id"]:0) .",'". $selected_type .  "');
			}
			if($(this).val() =='merchants')
			{
				$('tr.wizard_merchantRow').show();
				$('tr.wizard_productRow').hide();
				$('tr.wizard_creatives').show();
				merchant_id = $('select[name=wizard_merchantId]').val();
				".(isset($pixel_id) && $selected_type == "merchant" ?(!empty($pixel_data["banner_id"])?"$('input:radio[value=select_banner]').prop('checked',true);":'') :"$('input:radio[value=all_banners]').prop('checked',true);") ."
				loadCreatives ('merchant',merchant_id,". (isset($pixel_id) && !empty($pixel_data['banner_id'])?$pixel_data["banner_id"]:0) .",'". $selected_type .  "');
			}
			affId = localStorage[". (isset($id)?$id:0) ."];
			if(!affId){
				jsonData = new Object();
				jsonData.affiliate_id = ".  (isset($id)?$id:0) .";
				localStorage[".  (isset($id)?$id:0) ."] = JSON.stringify(jsonData);
			}
		});":'')."
		$('select[name=wizard_merchantId]').on('change',function(){
			loadCreatives('merchant',$(this).val(),". (isset($pixel_id)&& !empty($pixel_data['banner_id'])?$pixel_data["banner_id"]:0) .",'". $selected_type .  "');
		});
		$('select[name=wizard_productId]').on('change',function(){
			loadCreatives('product',$(this).val(),". (isset($pixel_id)&& !empty($pixel_data['banner_id'])?$pixel_data["banner_id"]:0) .",'". $selected_type .  "');
		});
		function loadCreatives(type,id,banner_id,selected_type){
			creative_url = '".$set->SSLprefix."ajax/LoadPixelCreatives.php';
			$.post(creative_url,{type:type,id:id,selected_banner_id:banner_id,selected_type:selected_type},
					function(res) {
							if(type=='product'){
								$('select[name=wizard_selectBanner]').html(res);
							}
							else if(type=='merchant'){
								$('select[name=wizard_selectBanner]').html(res);
							}
					});
		}
		
		$('select[name=wizard_selectBanner]').on('change',function(){
			if($(this).val() != 0 || $(this).val() != ''){
				$('input:radio[value=select_banner]').prop('checked',true);
			}
		});
		$('.buttonNextNew').on('click',function(){
			$('.actionBar .buttonNext').trigger('click');
			return false;
		});
		$('.buttonFinishNew').on('click',function(){
			$('.actionBar .buttonFinish').trigger('click');
			return false;
		});
		$('[name=wizard_pixelMethodType]').on('change',function(){
			if($(this).val() =='automatic')
			{
				$('tr.pixelMethod_manual').hide();
				$('tr.pixelMethod_automatic').show();
			}
			if($(this).val() =='manual')
			{
				$('tr.pixelMethod_manual').show();
				$('tr.pixelMethod_automatic').hide()
			}
		});
		$('[name=pixelCode_test]').on('click',function(){
			var pixelurl = $('#wizard_pixelCode').val();
			var testPixelUrl = pixelGetPostURL;
			if(affId){
				pixelLocalData = affId;
				pixelLocalData =JSON.parse(affId);
				if(typeof pixelLocalData.pixelUrl != 'undefined'){
					testPixelUrl = pixelLocalData.pixelUrl;
				}
				else
				{
					//
				}
			}
			$.prompt('<label>". lang("Put your test tracking link here") .": <br/><input type=\'text\' name=\'test_link\' style=\'width:85%\' required value=\''+ testPixelUrl +'\'></label>', {
								top:200,
								title: '". lang('Test Pixel Automatically') ."',
								buttons: { '".lang('Check')."': true, '".lang('Cancel')."': false },
								submit: function(e,v,m,f){
									if(v){
										name = $('[name=test_link]').val();
										if(name != ''){
											myTestUrl = name;
											$('[name=wizard_pixelmethod]').val(pixelGetPostMethod);
											var testUrl = '".$currentDomain."/common/textPixel.php?url=' + encodeURIComponent(name) + '&pixelurl=' + encodeURIComponent(pixelurl)+ '&method='+pixelGetPostMethod;
											localData = localStorage['". $id. "']; 
											localData = JSON.parse(localData);
											localData.pixelUrl = myTestUrl;
											pixelGetPostMethod = 'get';
											pixelGetPostURL = myTestUrl;
											$('[name=test_link]').val(myTestUrl);
											localStorage['" . $id .  "'] = JSON.stringify(localData);
											/* v = window.open(testUrl);
											setTimeout(function(){
												v.close();
											},1000); */
												$.post(testUrl,
												function(res) {
														res = JSON.parse(res);
														if(typeof res.display !== 'undefined' && res.display == 1){
															$('.otherPixelMethodLine').show();
															$('[name=pixelCode_test]').val( '". lang('Try Again') ."')
														}
														else{
															$('.otherPixelMethodLine').hide();
															$('[name=pixelCode_test]').val( '". lang('Test Now') ."')
														}
												});
										}
										else{
											$('[name=test_link]').css('border','1px solid red');
											return false;
										}
									}
									else{
										//
									}
						}
				});			
		});
    	// Smart Wizard     	
  		$('#wizard').smartWizard({transitionEffect:'slideleft',onLeaveStep:leaveAStepCallback,onFinish:onFinishCallback,enableFinishButton:false,keyNavigation:false});
      function leaveAStepCallback(obj){
        var step_num= obj.attr('rel');
        return validateSteps(step_num);
      }
      function onFinishCallback(){
       if(validateAllSteps()){
        $('#frmWizard').submit();
       }
      }
		});
    function validateAllSteps(){
       var isStepValid = true;
       if(validateStep1() == false){
         isStepValid = false;
         $('#wizard').smartWizard('setError',{stepnum:1,iserror:true});         
       }else{
         $('#wizard').smartWizard('setError',{stepnum:1,iserror:false});
       }
	   if(validateStep2() == false){
         isStepValid = false;
         $('#wizard').smartWizard('setError',{stepnum:2,iserror:true});         
       }else{
         $('#wizard').smartWizard('setError',{stepnum:2,iserror:false});
       }
       if(validateStep3() == false){
         isStepValid = false;
         $('#wizard').smartWizard('setError',{stepnum:3,iserror:true});         
       }else{
         $('#wizard').smartWizard('setError',{stepnum:3,iserror:false});
       }
	   if(validateStep4() == false){
         isStepValid = false;
         $('#wizard').smartWizard('setError',{stepnum:4,iserror:true});         
       }else{
         $('#wizard').smartWizard('setError',{stepnum:4,iserror:false});
       }
		if(!isStepValid){
          $('#wizard').smartWizard('showMessage','". lang('Please correct the errors in the steps and continue')."');
       }
       return isStepValid;
    } 	
		function validateSteps(step){
		  var isStepValid = true;
      // validate step 1
      if(step == 1){
        if(validateStep1() == false ){
          isStepValid = false; 
		  msg = '". lang('Please correct the errors in step') . "' +step+ '" . lang(' and click next.') . "';
		  $('#wizard').smartWizard('showMessage',msg);
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});         
        }else{
          $('#wizard').smartWizard('hideMessage');
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
        }
      }
	  if(step == 2){
        if(validateStep2() == false ){
          isStepValid = false; 
		  msg = '". lang('Please correct the errors in step') . "' +step+ '" . lang(' and click next.') . "';
          $('#wizard').smartWizard('showMessage',msg);
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});         
        }else{
          $('#wizard').smartWizard('hideMessage');
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
        }
      }
      // validate step3
      if(step == 3){
        if(validateStep3() == false ){
          isStepValid = false; 
        msg = '". lang('Please correct the errors in step') . "' +step+ '" . lang(' and click next.') . "';
		$('#wizard').smartWizard('showMessage',msg);
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});         
        }else{
          $('#wizard').smartWizard('hideMessage');
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
        }
      } 
	  if(step == 4){
        if(validateStep4() == false ){
          isStepValid = false; 
          msg = '". lang('Please correct the errors in step') . "' +step+ '" . lang(' and click next.') . "';
          $('#wizard').smartWizard('showMessage',msg);
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:true});         
        }else{
			localStorage.removeItem('". $id ."');
          $('#wizard').smartWizard('hideMessage');
          $('#wizard').smartWizard('setError',{stepnum:step,iserror:false});
        }
      } 
      return isStepValid;
    }
		function validateStep1(){
       var isValid = true;
		".($set->showProductsPlace==1?"
       // Validate Pixel Type
       var un = $('[name=wizard_pixelType]:checked').val();
       if(un=='' || typeof un=='undefined')
	   {
         isValid = false;
         $('#msg_pixelType').html('". lang("Select Pixel type"). "').show();
       }else{
		 $('#msg_pixelType').html('').hide();
       }
       // validate password
	   if(un == 'products'){
		   var p = $('[name=wizard_productId]').val();
		   if(p== '' || typeof p=='undefined')
		   {
			 isValid = false;
			 $('#msg_product').html('". lang("Please select product.") ."').show();         
		   }else{
			 $('#msg_product').html('').show();         
		   }
	   }
	   else if(un == 'merchants')
	   {
		   var p = $('[name=wizard_merchantId]').val();
		   if(p == '' || typeof p=='undefined')
		   {
			 isValid = false;
			 $('#msg_merchant').html('". lang("Please select merchant.") ."').show();         
		   }else{
			 $('#msg_merchant').html('').show();         
		   }
	   }":"
	   var p = $('[name=wizard_merchantId]').val();
	   if(p == '' || typeof p=='undefined')
	   {
		 isValid = false;
		 $('#msg_merchant').html('". lang("Please select merchant.") ."').show();         
	   }else{
		 $('#msg_merchant').html('').show();         
	   }
	   ") ."
       return isValid;
    }
	function validateStep2(){
       var isValid = true; 
       // Validate Pixel Type
       var un = $('[name=wizard_trigger]').val();
       if(un=='' || typeof un=='undefined')
	   {
         isValid = false;
         $('#msg_trigger').html('". lang("Select Trigger Type"). "').show();
       }else{
		   ". (isset($pixel_id)? 
		   "
		   val = '". urlencode($pixel_data['pixelCode']) ."';
			if(val.indexOf('?') == -1 && val.indexOf('[')== -1 && val.indexOf('{')== -1) {
				$('#btnGetPixelCode').attr('disabled',true);
			}
			else{
				$('#btnGetPixelCode').attr('disabled',false);
			}
		"
		   :'')."
		 $('#msg_trigger').html('').hide();
       }
       return isValid;
    }
    function validateStep3(){
      var isValid = true;    
	  var pixelCode = $('#wizard_pixelCode').val();
       if(pixelCode == ''){
			isValid = false;
           $('#msg_pixelCode').html('". lang('Pixel Code is empty. Please fill it.') ."').show();           
         }else{
			affId = localStorage[".  (isset($id)?$id:0) ."];
			if(affId){
				pixelLocalData = affId;
				pixelLocalData =JSON.parse(affId);
				if(typeof pixelLocalData.pixelUrl != 'undefined'){
					$('.otherPixelMethodLine').show();
					$('[name=pixelCode_test]').val( '". lang('Try Again') ."')
				}
				else
				{
					$('.otherPixelMethodLine').hide();
					//$('.pixelCode_test').val('". lang('Try Again') ."')
				}
			}
          $('#msg_pixelCode').html('').hide();
         }
		return isValid;
    }
	function validateStep4(){
      var isValid = true;    
	  var pixelmethod = $('#pixelmethod').val();
       if(pixelmethod ==''){
			isValid = false;
           $('#msg_method').html('". lang("Pixel Method is empty. Please fill it.") ."').show();           
         }else{
          $('#msg_method').html('').hide();
         }
		return isValid;
    }
	//client side pixel handling (If its a tag - display only client side optoin in methods dropdown on 4rth step)
	$('#wizard_pixelCode').on('blur',function(){
		code  =  $(this).val();
		if(/<[a-z][\s\S]*>/i.test(code)){
			opts = '<option value=\'client\'>".lang('Client Side')."</option>';
			$('.pixelMethod_manual').show();
			$('.pixelMethodType').hide();
			$('.wizard_clientSide').show();
			$('#pixelmethod').html(opts);
		}
		else{
			". (isset($pixel_id)?"":"
			$('.pixelMethod_manual').hide();")."
			$('.pixelMethodType').show();
			opts = '<option value=\'post\'>".lang('Server To Server')." - POST</option><option value=\'get\'>".lang('Server To Server')." - GET</option>';
			$('#pixelmethod').html(opts);
			$('.wizard_clientSide').hide();
		}
	});
	//check for ? to Analyze the input code
	$('#wizard_pixelCode').on('keyup',function(){
		val = $(this).val();
		if(val.indexOf('?') == -1 && val.indexOf('[')== -1 && val.indexOf('{')== -1) {
			$('#btnGetPixelCode').attr('disabled',true);
		}
		else{
			$('#btnGetPixelCode').attr('disabled',false);
		}
	});
	$('[name=wizard-activatePixel]').on('click',function(){
		$('input[name=wizard_pixelValid]').val(1);
		$.prompt('". lang("Pixel is activated now.") ."',{
					top:200,
					title: '". lang('Activate Pixel') ."',
					buttons: { '".lang('Ok')."': true},
					submit: function(e,v,m,f){
						if(v){
							//
						}
						else{
							//
						}
					}
		})
	});
		$('.btnGetPixelCode').on('click',function(){
		url = $('[name=wizard_pixelCode]').val();
		
		testurl = url.split('#');
		HashCode = testurl[1];
		testurl = testurl[0];
		var objParams = { 
                pixel_url   : testurl,
				type:'". $userLevel."',
				".($userLevel=="affiliate"?"affiliate_id:" . $set->userInfo['id']:'')."
				
		};
		$('#modalPixelCode').val(testurl);
		$.post('".$set->SSLprefix."ajax/generateWizardPixelCode.php',
                objParams,
                function(res) {
						if(res !== false){
								data = res.split('~~');
								paramsize = data[0];
								res = data[1];
								//console.log(res);
								$('.params').html(res)
								 $('.params table tr').each(function(e) {
									if (e != 0)
										$(this).hide();
									else{
										paramName = $(this).find('td:first-child').text();
										paramValue = $(this).find('select option:selected').text();
										
										$('#modalPixelCode').highlightWithinTextarea(onInputRegexAgain);
										function onInputRegexAgain(input) {
											if(paramName.indexOf('[')!= -1){
												var regex = new RegExp( '\['+ paramName +'*\]' , 'g');
											}
											else if(paramName.indexOf('{')!= -1){
												newInput = paramName;
												var regex = new RegExp( newInput , 'g');
											}
											else{
												newInput = paramName+'='+paramValue;
												var regex = new RegExp( newInput , 'g');
											}
											return regex;
										}
									}
								});
								if(paramsize >1){
									$('#next').show();
									$('#previous').hide();
								}
								else{
									$('#next').hide();
									$('#previous').hide();
								}
								modal.style.display = 'flex';
						}
				});
		});
</script>
	";
	$set->content .= '
	<link href="'.$set->SSLprefix.'js/highlight_textarea/jquery.highlight-within-textarea.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="'.$set->SSLprefix.'js/highlight_textarea/jquery.highlight-within-textarea.js"></script>
	<script>
	$(document).ready(function(){
				$("#done").on("click",function(){
						url = $("#modalPixelCode").val();
						console.log(HashCode);
						if(HashCode != "" && typeof HashCode != "undefined"){
							url += "#" + HashCode;
						}
						$("#wizard_pixelCode").val(url);
						modal.style.display = "none";
				});
				$("#next").on("click",function(){
					var paramName = "";
					var paramValue = "";
				if($(".params tr:visible").next().data("count") == paramsize || $(".params tr:visible").next().data("count") == "undefined"){
					$("#next").hide();
				}
				if ($(".params tr:visible").next().length != 0){
					$("#previous").attr("disabled",false);
					$("#previous").show();
					$(".params tr:visible").next().show().prev().hide();
					paramName = $(".params tr:visible").find("td:first-child").text();
					paramValue = $(".params tr:visible").find("select option:selected").text();
					$("#modalPixelCode").highlightWithinTextarea(onInputRegexAgain);
					function onInputRegexAgain(input) {
						if(paramName.indexOf("[")!= -1){
							var regex = new RegExp( "\["+ paramName +"*\]" , "g");
						}
						else if(paramName.indexOf("{")!= -1){
							newInput = paramName;
							var regex = new RegExp( newInput , "g");
						}
						else{
							newInput = paramName+"="+paramValue;
							var regex = new RegExp( newInput , "g");
						}
						return regex;
					}
				}
				else {
					$(this).attr("disabled",true);
					$(this).hide();
					return false;
				}
				return false;
			});
			$("#previous").on("click",function(){
				if($(".params tr:visible").prev().data("count") == 1 || $(".params tr:visible").prev().data("count") == "undefined"){
					$("#previous").hide();
				}
				if ($(".params tr:visible").prev().length != 0){
					$("#next").attr("disabled",false);
					$("#next").show();
					$(".params tr:visible").prev().show().next().hide();
					paramName = $(".params tr:visible").find("td:first-child").text();
					paramValue = $(".params tr:visible").find("select option:selected").text();
					$("#modalPixelCode").highlightWithinTextarea(onInputRegexAgain);
					function onInputRegexAgain(input) {
						if(paramName.indexOf("[")!= -1){
							var regex = new RegExp( "\["+ paramName +"*\]" , "g");
						}
						else if(paramName.indexOf("{")!= -1){
							newInput = paramName;
							var regex = new RegExp( newInput , "g");
						}
						else{
							newInput = paramName+"="+paramValue;
							var regex = new RegExp( newInput , "g");
						}
						return regex;
					}
				}
				else {
					$(this).attr("disabled",true);
					$(this).hide();
					return false;
				}
				return false;
			});
	});
	function replaceSelectedParam(e){
		paramName = $(e).closest("tr").find("td:first-child").text();
		paramValue = $(e).data("val");
		$(e).data("val",$(e).val());
		pixelCode = $("#modalPixelCode").val();
		newValue = $(e).data("val");
		if(paramName.indexOf("[")!= -1 || paramName.indexOf("{")!= -1){
			console.log(paramName + "-" + newValue);
			code = pixelCode.replace(paramName,newValue);
		}
		else
			code = pixelCode.replace(paramName+"="+paramValue,paramName+"="+newValue);
		
		$("#modalPixelCode").val(code);
		$("#modalPixelCode").highlightWithinTextarea(onInputRegexAgain);
		function onInputRegexAgain(input) {
						if(newValue.indexOf("[")!= -1){
							var regex = new RegExp( "\["+ newValue +"*\]" , "g");
						}
						else if(newValue.indexOf("{")!= -1){
							newInput = newValue;
							var regex = new RegExp( newInput , "g");
						}
						else{
							newInput = paramName+"="+newValue;
							var regex = new RegExp( newInput , "g");
						}
						return regex;
					}
	}
	</script>
	';
    $set->content .= '<div id="pixel_logs" style="display: none; border: "1px  grey solid" ,height: "220px", width:"250px;">
            <center>
            <h2><u>' . lang('Pixels Fired History') . '</u></h2>
            <table border="1" style="width: 99%;">
                    <thead>
                        <tr>
                            <td><label style="color: White">' . lang('ID') . '</label></td>
                            <td><label style="color: White">' . lang('URL') . '</label></td>
                            <td><label style="color: White">' . lang('Date') . '</label></td>
                            <td><label style="color: White">' . lang("Response From Pixel's Owner") . '</label></td>
                        </tr>
                    </thead>
                    <tbody>
                            <!-- Pixel Logs. -->
                            <!-- Data will be loaded via an ajax call. --> 
                    </tbody>
            </table>
            </center>
    </div>
</div>
<link type="text/css" rel="stylesheet" href="'.$set->SSLprefix.'js/jquery_dropdowns/jquery.dropdown.css" />
<script type="text/javascript" src="'.$set->SSLprefix.'js/jquery_dropdowns/jquery.dropdown.min.js"></script>
<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
    <script>
	$(document).ready(function(){
		$("#tab-container3").easytabs();
		$("select[name=\'method[]\']").on("change",function(){
				client = false;
				$("select[name=\'method[]\']").each(function(){
					if($(this).val() == "client"){
							client  = true;
					}
				});
				if(client){
					$(".clientside").show();
				}
				else{
					if($("#dbmethod0").val() == "client"){
						$(".clientside").show();
					}
					else{
						$(".clientside").hide();
					}
				}
		});
		$("#dbmethod0").on("change",function(){
				if($(this).val() == "client"){
					$(".clientside").show();
				}
				else{
					$(".clientside").hide();
				}
		});
		$("#pixelType").on("change",function(){
			if($(this).val() =="products")
			{
				$(".pixelProducts").show();
			}
			else{
				$(".pixelProducts").hide();
			}
			if($(this).val() =="merchants")
			{
				$(".pixelMerchants").show();
			}
			else{
				$(".pixelMerchants").hide();
			}
		});
	});
        $(".inline").colorbox({inline:true,border: "1px black solid" ,height: "400px", width:"50%"});
        $(".inline").click(function() {
            var elem = $(this);
            $.get("'.$set->SSLprefix.'ajax/loadPixelLogs.php",
                { "pixel_id": elem.data("pixel_id") },
                function(res) {
                    try {
                        res = JSON.parse(res);
                        var strTr = "";
                        for (var i = 0; i < res["success"].length; i++) {
							var respCode = res["success"][i]["pixelResponse"];
							respCode = respCode ? respCode.split(" ") : [\'0\', \'\'];
							var color = "white";
							if (respCode[0]==200) {
								color = "lightgreen";
							}
							else if (respCode[0]==301 || respCode[0]==307) {
								color = "yellow";
							}
							else if (respCode[0]==403 || respCode[0]==500 || respCode[0]==400 || respCode[0]==401|| respCode[0]==404 || respCode[0]==501 || respCode[0]==503|| respCode[0]==550) {
								color = "red";
							}
							else  {
								color = "white";
							}
                            strTr += "<tr><td>" + (i + 1) + "</td>" 
                                  +  "<td ><textarea style=\"width:98%\" rows=3 cols=60 readonly>" + decodeURIComponent(res["success"][i]["URL"])
                                  +  "</textarea></td><td>" + res["success"][i]["Date"] 
                                  +  "</td><td style=\"background-color:" +  color +  ";\">" + res["success"][i]["pixelResponse"]  + "</td></tr>";
                        }
                        $("#pixel_logs table tbody").html(strTr);
                    } catch (error) {
                        console.log(JSON.stringify(error));
                    }
                });
            $("#pixel_logs").show();
        });
        $(document).bind("cbox_closed", function() {
            $("#pixel_logs").hide();
        });
		function checkHtml(e,i){
			val = e.value;
			if(val!= ""){
			if(/<[a-z][\s\S]*>/i.test(val)){
				$.prompt("'.lang('System recognized it as HTML. Do you want to automatically selects method as Client Side?').'", {
					top:200,
					title: "'. lang('Pixel Monitor') .'",
					buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
					submit: function(e,v,m,f){
						if(v){
							$("#dbmethod" + i).val("client");
							$(".clientside").show();
						}
						else{
							//
						}
					}
				});
			}
			else{
//				alert("its not HTML");
			}
			}
		}
    </script>
';
/* * CAUTION!
* Following columns of `affiliates` must be kept up to date: 
 * 1. `accounts_pixel_params_replacing`
 * 2. `sales_pixel_params_replacing` 
 */
