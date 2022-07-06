<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
/*
 * CAUTION!
 * Following columns of `affiliates` must be kept up to date: 
 * 1. `accounts_pixel_params_replacing`
 * 2. `sales_pixel_params_replacing`
 */

require_once('common/global.php');

if (empty($id)) {
	$affiliate_id_value = $set->userInfo['id'];
}
else {
	$affiliate_id_value = $id;
}


/**
 * Generate a dropdown list of merchants.
 * 
 * @param  array $arrMerchants
 * @param  int   $intMerchantId
 * @return string
 */
function getHtmlMerchants($arrMerchants, $intMerchantId = 0)
{
    $sql = 'SELECT * FROM `merchants` WHERE `id` IN ('  . implode(',', $arrMerchants) . ') AND `valid` = 1;';
    $resource = function_mysql_query($sql,__FILE__);
    $strHtmlMerchants = '';
    
    while ($arrMerchant = mysql_fetch_assoc($resource)) {
        $strSelected       = $intMerchantId == $arrMerchant['id'] ? ' selected ' : '';
        $strHtmlMerchants .= '<option ' . $strSelected . ' value="' . $arrMerchant['id'] . '">' . $arrMerchant['name'] . '</option>';
        unset($arrMerchant, $strSelected);
    }
    return $strHtmlMerchants;
}

function listAffiliateProducts( $selected_id = 0){
	global $set;
	$products_list = ltrim(str_replace ("|","," , $set->userInfo['products']),',');
	if(empty($products_list)){
		return "";
	}
	$sql = "SELECT id,title FROM products_items WHERE id in (".  $products_list .")";
	$qq=function_mysql_query( $sql ,__FILE__);
	while ($ww=mysql_fetch_assoc($qq)) {
		if ($text AND $id == $ww['id']) 
				return $ww['name'];
			$html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $selected_id ? 'selected' : '').'>'.$ww['title'].'</option>';
		/* echo 'wwid = ' . $ww['id']. '<br>';
		echo 'id = ' . $id. '<br>'; */
		}
		return $html;
}

/* function doPost($url){
	$parse_url=parse_url($url);
	$da = @fsockopen($parse_url['host'], 80, $errno, $errstr);
	if ($da) {
		$params ="POST ".$parse_url['path']." HTTP/1.1\r\n";
		$params .= "Host: ".$parse_url['host']."\r\n";
		$params .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$params .= "User-Agent: ".$set->webTitle." Agent\r\n";
		$params .= "Content-Length: ".strlen($parse_url['query'])."\r\n";
		$params .= "Connection: close\r\n\r\n";
		$params .= $parse_url['query'];
		fputs($da, $params);
		while (!feof($da)) $response .= fgets($da);
		fclose($da);
		
		// split the result header from the content
		$result = explode("\r\n\r\n", $response, 2);
		$content = isset($result[1]) ? $result[1] : '';
		return $content;
		}
	} */


$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);

$pageTitle = lang('Pixel Monitor');
$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
$set->content   = '';

if (isset($_POST['deletePixel'])) {
    $_GET['act'] = 'deletePixel';
} elseif (isset($_POST['editPixel'])) {
    $_GET['act'] = 'editPixel';
} elseif (isset($_POST['testPixel'])) {
    $_GET['act'] = 'testPixel';
} elseif (isset($_POST['act'])) {
    $_GET['act'] = $_POST['act'];
}


switch ($_GET['act']) {
    case 'deletePixel':
        $sql = 'DELETE FROM `pixel_monitor` WHERE `id` = ' . mysql_real_escape_string($_POST['ids'][0]) . ';';
        function_mysql_query($sql,__FILE__);
        _goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $set->userInfo['id']);
	break;
    
    
    case 'editPixel':
		
		if (isset($_POST['pixelCode'][0])) {
				if($_POST['pixelType'] == 'products'){
				$sql = "UPDATE `pixel_monitor` "
				 . "SET `product_id` = " . mysql_real_escape_string($_POST['product_id'][0]) . ", "
				 . "`method` = '" . mysql_real_escape_string($_POST['method'][0]) . "', "
				 . "`pixelCode` = '" . trim(mysql_real_escape_string($_POST['pixelCode'][0])) . "', "
				 . "`type` = '" . strtolower(mysql_real_escape_string($_POST['type'][0])) . "',
					 banner_id=". $_POST['banner_id'] .", "
				 . "`valid` = " . mysql_real_escape_string(0) . " "
				 
				 . "WHERE `id` = " . mysql_real_escape_string($_POST['ids'][0]) . ";";	
			}
			else{
				$sql = "UPDATE `pixel_monitor` "
				 . "SET `merchant_id` = " . mysql_real_escape_string($_POST['merchant_id'][0]) . ", "
				 . "`method` = '" . mysql_real_escape_string($_POST['method'][0]) . "', "
				 . "`pixelCode` = '" . trim(mysql_real_escape_string($_POST['pixelCode'][0])) . "', "
				 . "`type` = '" . strtolower(mysql_real_escape_string($_POST['type'][0])) . "', 
				 `banner_id`=". $_POST['banner_id'] .", "
				 . "`valid` = " . mysql_real_escape_string(0) . " "
				 . "WHERE `id` = " . mysql_real_escape_string($_POST['ids'][0]) . ";";	
			}
		}
			
        function_mysql_query($sql,__FILE__);
		
		$sql = "insert into affiliates_notes (valid, rdate, admin_id, edited_by, affiliate_id, advertiser_id, group_id,notes, issue_date, status) values (1,'". date("Y-m-d H:i:s") ."',-1,-1,". $set->userInfo['id'] .", -1, ". $set->userInfo['group_id'] .", '". lang('pixel waiting for his approval') ."','". date("Y-m-d H:i:s") ."','inprocess')";
		mysql_query($sql);
		
        _goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $set->userInfo['id']);
	break;
    
    
    case 'testPixel':
        $pxl  = $_POST['pixelCode'][0];
        $pxla = preg_replace("/{[-a-zA-Z0-9 _]+}/", "1234", $pxl);
        $a    = '';
        
        if (strpos($pxla, 'ttp') < 3) {
            $a = doPost($pxla); 
        }
        
        _goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $set->userInfo['id']);
	break;
    
    
    //case 'save_pixel':
    case 'save_pixel':
	
        if (isset($_POST['db']['pixelCode'])) {
            $_POST['db']['pixelCode'] = htmlentities($_POST['db']['pixelCode']);
            $_POST['db']['pixelCode'] = str_replace("'", '"', $_POST['db']['pixelCode']);
            // var_dump($db);
			// die();
			if($pixelType == 'products'){
				$sql = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `product_id`, `pixelCode`,`method`, `totalFired`, `rdate`,`banner_id`) 
                    VALUES
                   ('" . mysql_real_escape_string($_POST['db']['type']) . "',"
                       . mysql_real_escape_string(0) . ","
                       . mysql_real_escape_string($_POST['db']['affiliate_id']) . "," 
                       . mysql_real_escape_string($_POST['db']['product_id']) . ",'" 
                       . trim(mysql_real_escape_string($_POST['db']['pixelCode'])) . "','" 
                       . mysql_real_escape_string($_POST['db']['method']) . "'," 
                       . mysql_real_escape_string(0) . ",NOW(),
					   ". $_POST['db']['banner_id'] .");";  
			}
			else{
            $sql = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `merchant_id`, `pixelCode`,`method`, `totalFired`, `rdate`,`banner_id`) 
                    VALUES
                   ('" . mysql_real_escape_string($_POST['db']['type']) . "',"
                       . mysql_real_escape_string(0) . ","
                       . mysql_real_escape_string($_POST['db']['affiliate_id']) . "," 
                       . mysql_real_escape_string($_POST['db']['merchant_id']) . ",'" 
                       . trim(mysql_real_escape_string($_POST['db']['pixelCode'])) . "','" 
                       . mysql_real_escape_string($_POST['db']['method']) . "'," 
                       . mysql_real_escape_string(0) . ",NOW(),
					    ". $_POST['db']['banner_id'] .");";    
			}
			
            //die ($sql);
            function_mysql_query($sql,__FILE__);
			
			//insert new record to affiliate notes (managers Notes CRM)
			
			$sql = "insert into affiliates_notes (valid, rdate, admin_id, edited_by, affiliate_id, advertiser_id, group_id,notes, issue_date, status) values (1,'". date("Y-m-d H:i:s") ."',-1,-1,". $set->userInfo['id'] .", -1, ". $set->userInfo['group_id'] .", '". lang('pixel waiting for his approval') ."','". date("Y-m-d H:i:s") ."','inprocess')";
			mysql_query($sql);
			
        }
        
        _goto($set->SSLprefix.$set->basepage . '?act=new&id=' . $_POST['db']['affiliate_id']);
        break;
    
	case "save_wizard_pixel":
	
			if($wizard_banners == "all_banners"){
				$banner_id = 0;
			}
			else{
				$banner_id = $wizard_selectBanner;
			}
			if(isset($pixel_id) && !empty($pixel_id)){
					if($wizard_pixelType == 'merchants'){
							   $qry = "UPDATE pixel_monitor "
                        . "SET merchant_id='".$wizard_merchantId."',method='" .$wizard_pixelmethod. "', "
                        . "    pixelCode='".trim(mysql_real_escape_string($wizard_pixelCode))."',type='".$wizard_trigger."', banner_id=". $banner_id ." WHERE id=".$pixel_id;
					}
					else{
						  $qry = "UPDATE pixel_monitor "
                        . "SET product_id='".$wizard_productId."',method='" .$wizard_pixelmethod. "', "
                        . "    pixelCode='".trim(mysql_real_escape_string($wizard_pixelCode))."',type='".$wizard_trigger."', banner_id=". $banner_id ." WHERE id=".$pixel_id;
					}
					
				function_mysql_query($qry,__FILE__,__FUNCTION__);
            }
			else{
				
						if($wizard_pixelType == 'merchants'){
							$qry = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `merchant_id`, `pixelCode`,`method`, `totalFired`,`banner_id`) VALUES
							   ('".$wizard_trigger."', 0 ,".$set->userInfo['id'].",".$wizard_merchantId.",'". trim(mysql_real_escape_string($wizard_pixelCode))."','". ($wizard_pixelmethod)."',0,". $banner_id.")"; 
						}
						else{
							   $qry = "INSERT INTO `pixel_monitor`(`type`, `valid`, `affiliate_id`, `product_id`, `pixelCode`,`method`, `totalFired`,`banner_id`) VALUES
							   ('".$wizard_trigger."', 0,".$set->userInfo['id'].",".$wizard_productId.",'". trim(mysql_real_escape_string($wizard_pixelCode))."','". ($wizard_pixelmethod)."',0,". $banner_id.")";
						}
						
					 function_mysql_query($qry,__FILE__,__FUNCTION__);

			}
            _goto($set->SSLprefix.$set->basepage.'?act=new&id='.$affiliate_id_value ."&toggleTo=api_access");
            break;
    
    case 'new':
    default:
	
		$userLevel = "affiliate";
		
		$set->content .= '<script src="'.$set->SSLprefix.'js/easytabs/jquery.easytabs.min.js" type="text/javascript"></script>';
		$set->content .='<style>
				    .etabs { margin: 0; padding: 0; }
					.tab { display: inline-block; zoom:1; *display:inline; background: #eee; border: solid 1px #999; border-bottom: none; -moz-border-radius: 4px 4px 0 0; -webkit-border-radius: 4px 4px 0 0; }
					.tab a { font-size: 14px; line-height: 2em; display: block; padding: 0 10px; outline: none; }
					.tab a:hover { text-decoration: underline; }
					.tab.active { background: #fff; padding-top: 6px; position: relative; top: 1px; border-color: #666; }
					.tab a.active { font-weight: bold; }
					.tab-container .panel-container { background: #fff; border: solid #666 1px; padding: 10px; -moz-border-radius: 0 4px 4px 4px; -webkit-border-radius: 0 4px 4px 4px; }
				  </style>';
		
		$set->content .= include('common/PixelMonitor.php');
		
        theme();
        break;
}
/*
 * CAUTION!
 * Following columns of `affiliates` must be kept up to date: 
 * 1. `accounts_pixel_params_replacing`
 * 2. `sales_pixel_params_replacing`
 */

