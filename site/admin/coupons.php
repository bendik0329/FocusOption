<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
$logintype = 'admin';
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/". $logintype."/";
if (!isAdmin()) _goto($lout);

$appTable = 'merchants_creative';
$pageTitle = lang('Affiliates Coupons');
$set->breadcrumb_title =  lang($pageTitle);
$set->pageTitle = '
<style>
.pageTitle{
	padding-left:0px !important;
}
</style>
<ul class="breadcrumb">
	<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
	<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
	<li><a style="background:none !Important;"></a></li>
</ul>';

switch ($act) {
	/* ------------------------------------ [ Manage Groups ] ------------------------------------ */
	
	case "valid":

	$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		//echo '<a  style="cursor: pointer;">'.xvPic($valid).'</a>';
		 echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
			
	case "delete":
		
		if ($id>-1)
		{
		
			$qry = "delete from merchants_creative where  type='coupon' and id = " . $id ;
			// die ($qry);
			function_mysql_query($qry,__FILE__) ; 
			_goto($set->SSLprefix.$set->basepage);
		}
	break;	
			
			
			
	case "add":
		//var_dump($_POST);die;
		
		$code = $db['code'];
		
		if (empty($code))  $errors['id'] = 1;
		if (empty($errors)) {
			
			if (!isset($code)) {
			$db[valid] = 0;
		}
		
			$db['alt'] = $code;
			$db['title'] = $code;
			$db['type'] = 'coupon';
		if ($db['id']=='') { 
			$db['rdate'] = date('Y-m-d H:i:s');
			$db['valid'] = $db['language_id'] = $db['promotion_id'] = $db['admin_id'] = 1;
			$db['width'] = $db['height']  = 0;
			$db['file'] = $db['url']  = '.';
			
			$db['affiliate_id'] =  retrieveAffiliateId($_POST['affiliate_id']);
			
			$qq=function_mysql_query("SELECT min(id) as id FROM merchants where valid = 1",__FILE__);
			$ww=mysql_fetch_assoc($qq);
			$db['merchant_id'] = $ww['id'];
		   $qry = "INSERT INTO merchants_creative ( `rdate`, `last_update`, `valid`, `admin_id`, `merchant_id`, `language_id`, `promotion_id`, `title`, `type`, `width`, `height`, `file`, `url`, `iframe_url`, `alt`, `scriptCode`, `affiliate_id`) VALUES
				( now(), now(), '1', '1', '".$db['merchant_id'] ."', '1', '1', '".$code."', 'coupon', '0', '0', '.', '.', '.', '".$code."', '', '".$db['affiliate_id']."')";
			// die ($qry);
		}
		else {
			if ($db['id']>-1)
			$qry = "update merchants_creative set alt='" . $code . "' ,  title='" . $code . "' , affiliate_id ='" . $db['affiliate_id'] . "'  where id = " . $db['id'];
			
		}
			function_mysql_query($qry,__FILE__);
			//dbAdd($db,$appTable);
			_goto($set->SSLprefix.$set->basepage);
	}
	
	
	
	
	
	default:
	
			$row = '';
			$qq=function_mysql_query("SELECT mc.*, af.username as affiliate_username FROM merchants_creative mc left join affiliates af on af.id = mc.affiliate_id where mc.type='coupon' ORDER BY mc.id ASC",__FILE__);
			$langList .= ($excel ? '<tr><td>'.lang('Coupon Name').'</td><td>'.lang('Date').'</td><td>'.lang('Affiliate ID').'</td><td>'.lang('Affiliate Username').'</td></tr>' : '');
		while ($ww=mysql_fetch_assoc($qq)) {
			
			if (!empty($id) && $id == $ww['id'])
				$row = $ww;
				$ww['code']=$ww['title'];
			
			$l++;
		//	$totalAffiliates=mysql_result(function_mysql_query("SELECT COUNT(id) FROM merchants_creative WHERE type='coupon' and id='".$ww['id']."'",__FILE__),0);
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						' . ($excel ? '' : '<td>'.$ww['id'].'</td>').'
						' . ($excel ? '' : '<td align="center"><a class="test" href="'.$set->SSLprefix.$logintype.'/coupons.php?act=edit&id='.$ww['id'].'" data-affid="' . $ww['affiliate_id'] . '" data-id="' . $ww['id'] . '" data-code= "'. $ww['code'] . '">'.lang('Edit').'</a><span>&nbsp;&nbsp;&nbsp;</span>' ) .'
						<a href="'.$set->SSLprefix.$logintype.'/coupons.php?act=delete&id='.$ww['id'].'">'.lang('Delete').'</a></td>
						<td>'.$ww['code'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center"><a href="'.$set->SSLprefix.$logintype.'/affiliates.php?act=new&id='.$ww['affiliate_id'].'">'.$ww['affiliate_id'].'</a></td>
						<td align="center"><a href="'.$set->SSLprefix.$logintype.'/affiliates.php?act=new&id='.$ww['affiliate_id'].'">'.$ww['affiliate_username'].'</a></td>
						
						' . ($excel ? : '' . '<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>').'
					</tr>';
			}
		//$totalAffiliates=mysql_result(function_mysql_query("SELECT COUNT(id) FROM affiliates WHERE status_id='0'",__FILE__),0);
		
		// All Affiliates.
			$affiliateqq = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);

			if (!(isset($_GET['affiliate_id']) && !empty($_GET['affiliate_id']))) {
				$allAffiliates = '<option selected value="0">'.lang('Choose Affiliate').'</option>';
			}

			while ($affiliateww = mysql_fetch_assoc($affiliateqq)) {		   
			   if (isset($row['affiliate_id']) && !empty($row['affiliate_id'])) {
					$allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $row['affiliate_id'] ? 'selected' : '').'>['.$affiliateww['id'].'] '
									  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
			   }
			   elseif (isset($ww['affiliate_id']) && !empty($db['affiliate_id'])) {
					$allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $db['affiliate_id'] ? 'selected' : '').'>['.$affiliateww['id'].'] '
									  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
			   } else {
					$allAffiliates .= '<option value="'.$affiliateww['id'].'">['.$affiliateww['id'].'] '
									  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
			   }
			}
			
		
		$set->content = '<form method="post" >
						<input type="hidden" name="act" value="add" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Coupon Code').'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1" style="">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">'.lang('Coupon Name').':</td><td><input id="db_code" type="text" name="db[code]" value="'. (!empty($row['code']) ? ($row['code']) :   $db['code']) .'" '.($errors['code'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><td align="left">'.lang('Affiliate ID').':</td><td>
							
							<!--input id="db_affiliate" type="text" name="db[affiliate_id]" value="'. (!empty($row['affiliate_id']) ? ($row['affiliate_id']) :   $db['affiliate_id']) .'" '.($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '').' /><input id="id_hidden" type="hidden" name="db[id]" />
							
							<div class="ui-widget" style="margin-left:-17px;text-align:left;">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" name="db[affiliate_id]" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $allAffiliates
								.'</select>
								</div>
							
							</td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />';
					
					$set->content .=($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->SSLprefix.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				
					</div><div style="clear:both"></div>' : '');
					
					$set->content .='</br>
						
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Coupon Codes List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td>'.lang('Coupon Name').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Related Affiliate ID').'</td>
								<td align="center">'.lang('UserName').'</td>
								<td align="center">'.lang('Available').'</td>
							</tr></thead><tfoot>'.$langList.'</tfoot>
						</table>
						<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
						<script>
							$("[data-id]").click(function() {
							$("#db_code").val($(this).data("code"));
							id = $(this).data("affid");
							try{
							 $("#combobox").combobox("autocomplete", id);
							 }
							 catch(e){
								 console.log(e);
							 }
							$("#id_hidden").val($(this).data("id"));
							return false;
						});
						
						</script>
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
						'.
						
					
						excelExporter($langList,'coupons');
		theme();
		break;
	}

?>