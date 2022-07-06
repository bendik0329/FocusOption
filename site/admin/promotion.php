<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
//require_once('common/BarcodeQR.php');
require_once ('common/ShortUrl.php');
require_once('common/InStyle.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'merchants_creative';
$appProm = 'merchants_promotions';

$max_upload = 15; // Max Upload Per Page
//die('Act: ' . $act);

$isNew = (isset($_GET['new']) && !empty($_GET['new']));
//die(var_dump($isNew));

switch ($act) {
	// ----------------------------------------- [ Edit Banner ] -----------------------------------------
	case "deletePromotions":
				 //var_dump($_POST);die;
				
				$title = isset($_POST['promotionName']) ? $_POST['promotionName'] : "";
				$merchant_id = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : "";
				/* if (!empty($title)) {
				if ($_POST['deleteAllWithSamePrefix']=='on') {
					$title = str_replace(substr($title, strrpos($title, '_') + 0),'',$title);
								// $qry = "delete from $appProm where title like '".$title."_%' AND merchant_id=". $merchant_id;
				$qry = "update $appProm set valid=-1 where title like '".$title."_%' AND merchant_id=". $merchant_id;
						// die($qry);
				}else{
				// $qry = "delete from $appProm where title = '".$title."' AND merchant_id=". $merchant_id;
				$qry = "update $appProm set valid=-1  where title = '".$title."' AND merchant_id=". $merchant_id;
				}
				function_mysql_query(
				$qry,__FILE__);
				} */
				$qry = "delete from " . $appProm . " where id = " . $promotionName;
				function_mysql_query(
				$qry,__FILE__);
			if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
		
			break;
	
	case "switchToGeneral":
		
		$sql = function_mysql_query("update merchants_creative set promotion_id = 0 where promotion_id = ". $promotion_id);
		if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
		
		break;
                
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'bnr_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	
	
	
		
	default:
		$pageTitle = lang('Promotion List');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		// $merchant_id = '1';
		$numqq = function_mysql_query("SELECT id FROM merchants",__FILE__);
                
		if (!$merchant_id) {
			$numww = mysql_fetch_assoc($numqq);
			
			if (mysql_num_rows($numqq) <= 1) { 
				_goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $numww['id']);
			}
			
                        
                        $set->content .= '<script>var hash = "";</script>';
        
			$set->content .= '<div align="center"><b>' . lang('Choose merchant to manage his creative material') 
						  . ':</b><br /><br /><select onchange="location.href=\'' . $set->SSLprefix.$set->basepage . '?'.($isNew==1 ? 'new=1&': '') . 'merchant_id=\'+this.value + hash;"><option value="">' 
						  . lang('Choose Merchant') . '</option>' . listMerchants(0,0,0,1) . '</select></div>';
                        
			theme();
			die();
			
		} else {
			$set->pageTitle .= ' - ' . listMerchants($merchant_id, 1);
		}
		
		$qq = function_mysql_query("SELECT mp.*,g.title as group_name FROM " . $appProm . " mp LEFT JOIN  groups g on g.id=mp.group_id WHERE mp.valid>-1 and mp.merchant_id='" . $merchant_id . "' ORDER BY title ASC",__FILE__);
		$distincPromotions = array();
		while ($ww = mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!in_array($ww['title'],$distincPromotions)) {
			$promotions_list .='<option value="'.$ww['id'].'">'.$ww['title'] . '</option>';
			$distincPromotions[] = $ww['title'];
			}
			
			
			$PromList .= '<tr ' . ($l % 2 ? 'class="trLine"' : '') . '>
						<td>' . $ww['id'] . '</td>
						<td align="left">' . $ww['title'] . '</td>
						<td align="center">' . dbDate($ww['rdate']) . '</td>
						<td align="center">' . ($ww['group_name'] ? $ww['group_name'] :'') . '</td>
						<td align="center">' . ($ww['affiliate_id'] ? $ww['affiliate_id'] : lang('ALL')) . '</td>
						<td align="center"><a href="' . $set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&prom_id=' . $ww['id'] . '">' . lang('Edit') . '</a></td>
						<td align="center" id="promlng_' . $ww['id'] . '"><a onclick="ajax(\'' . $set->SSLprefix.$set->basepage . '?act=prom_valid&id=' . $ww['id'] . '\',\'promlng_' 
						. $ww['id'] . '\');" style="cursor: pointer;">' . xvPic($ww['valid']) . '</a></td>
					</tr>';
		}
		
		if ($prom_id) {
			$db = dbGet($prom_id, $appProm);
		}
		
		if (mysql_num_rows($numqq) > 1) { 
			$set->content .= '<div align="right" style="padding-bottom: 10px;"><b>' . lang('Switch Merchant') . ':</b> <select onchange="location.href=\'' 
						  . $set->SSLprefix.$set->basepage . '?merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id,0,0,1) . '</select></div>';
		}
		
		$set->content .= '<form id="frmPromotion" method="post">
						<input type="hidden" name="act" value="prom_add" />
						'.($db['id'] ? '<input type="hidden" name="db[id]" value="'.$db['id'].'" />' : '').'
						<input type="hidden" name="db[merchant_id]" value="'.$merchant_id.'" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#maintab_1\').slideToggle(\'fast\');">'.lang('Add New Promotion').'</div>
						<div id="maintab_1" align="center" style="background: #EFEFEF; display: '.(!$prom_id ? 'block' : 'block').';">

                         <table >
						 <tr><td>                      
						<table width="110%" border="0" cellpadding="0" cellspacing="5">
							<tr><td colspan="3" height="5"></td></tr>
							<tr>
								<td align="left">'.lang('Promotion Name').':</td>
								<td><input type="text" name="db[title]" value="'.$db['title'].'" '.
								
								(isset($errors['title']) ? 'style="border: 1px red solid;"' : '').' style="width: 250px;" /></td>
							</tr>
							<tr style="height:60px!important;">
								<td align="left">'.lang('Relate To').':</td>
								<td align="left">'.lang('Group').':<select name="group_id" style="width: 224px;">
                                                <option value="-1">'.lang('None').'</option>'
                                                . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>' 
                                                    . lang('General') 
                                                . '</option>' 
                                                . listGroups($group_id) 
                                            . '</select>
								'.('<span style="padding:40px;">/</span>').'
								'.lang('Affiliates ID').': <input type="text" id="affiliate_id" name="db[affiliate_id]" value="'.((!empty($db['additional_affiliates']))?(implode('|',array_filter(explode('|',$db['additional_affiliates']), function($value) { return !is_null($value) && $value !== ''; }))):$db['affiliate_id']).'" /> <span style="font-size: 11px;">('.lang('Leave empty for all the affiliates'). '. ' .lang('Use | for multiple affiliates').')</span></td>
							</tr><tr>
								<td width="15%"></td>
							</tr><tr>
								
								<td width="15%"></td>
								<td width="15px"><input type="checkbox" name="autoApproveAll" '.($db['valid'] ? 'checked' : '').' />'.lang('Auto Approve New Promotions').'</td>
							</tr><tr>
								<td width="15%"></td>
							
								
								<td align="left"><input type="button" value="'.lang('Save').'" onclick="CheckPromotionVals()"/></td>
							</tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</td>
						
										
						</tr></table>
						</div>
						</form>
						<br />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#maintab_2\').slideToggle(\'fast\');">'.lang('Promotions List').'</div>
                                                <div id="maintab_2" ' .($isNew==1 || true ? '>' : 'style="display:none;">' ). 
						'
						<form method="post" id="frmDelPromotion"  >
						<input type="hidden" name="act" id="act" value="deletePromotions" />
						<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />
						<input type="hidden" name="promotion_id" id="promotion_id"/>
						<table><tr>
						<td align="left">'.lang('Delete by promotion name').'
						<select name="promotionName" id="promotion_name" style="margin-left:10px;margin-top:5px;width: 292px;"><option value=0">'.(lang('Choose Promotion')).'</option>'.$promotions_list.'</select>' 
						. '	</td>
								<td style="width:40px";></td>
								<td>
								<input type="checkbox" name="deleteAllWithSamePrefix" />'.lang('Delete Promotion With Any Related Affiliates').'<br>('.lang('Example: promo_700 , promo_701 , promo_702...').')</td>
								<td style="width:40px";></td>
								<td><input type="button" value="'.lang('Delete').'" onclick="return confirmation()"/></td>
								</tr></table>
						</form>
						<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
						<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
						<script>
						var PromotionConfirmMessage = "'.lang('This promotion is related to creatives. Those creatives would not display after this deletion. Would you like to continue?').'";
						var PromotionConfirmMessage_2 = "'.lang('Are you sure you want to delete?').'";
						var YesText = "'.lang('Yes').'";
						var  PromotionHeading = "'.lang('Promotion').'";
						var  switchToGeneral = "'.lang('Switch all banners to General promotion').'";
						var  CancelText = "'.lang('Cancel').'";
						function CheckPromotionVals(){
							if($("#affiliate_id").val() == ""){
								bln = 0;
								$.prompt("'.lang('Are you sure you want to create the promotion for <b>all</b> affiliates?').'", {
												top:200,
												title: "Promotions",
												buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
												submit: function(e,v,m,f){
													if(v){
														$("#frmPromotion").submit();
													}
													else{
														//return false;
													}
												}
									});
							}
							else{
								$("#frmPromotion").submit();
							}
							
						}
					</script>
					
							<br>	
						
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
                                                    <thead>
							<tr>
								<td>#</td>
								<td style="text-align: left;">'.lang('Promotion Name').'</td>
								<td style="text-align: center;">'.lang('Added Date').'</td>
								<td style="text-align: center;">'.lang('Group ID').'</td>
								<td style="text-align: center;">'.lang('Affiliate ID').'</td>
								<td style="text-align: center;">'.lang('Actions').'</td>
								<td style="text-align: center;">'.lang('Available').'</td>
							</tr>
                                                    </thead>
                                                    <tfoot>'.$PromList.'</tfoot>
						</table>
						</div>
					<br />
					
					<script>
	$(document).ready(function(){
		$(".inline").colorbox({iframe:true,border: "1px black solid" ,height: "95%", width:"95%",fixed:true});

	});
	</script>
	';
	
	
	
			

					
		if ($result) $set->content .= '<script type="text/javascript">
			$(document).ready(function(){
				var scrollToElement = $("#formSearch");
					$(\'html,body\').animate({scrollTop:(scrollToElement.offset().top-210)}, 0);
				});
			</script>
                        ';
		
                $set->content .='</div>';
/*		popup errors! - need to be fix
		if($errors['creative_url']!=null){
			$set->content.='<script type="text/javascript">alert("'.$errors['creative_url'].'");</script>';
		}
*/
		
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
	
	$title = isset($_POST['db']['title'])? $_POST['db']['title'] : "";
	
	if (strpos($title,'_')>0){
		
		$reversedParts = explode('_', strrev($title), 2);
		$lastPart =  strrev($reversedParts[0]); // outputs "four"
		if (is_numeric($lastPart))
		$title = str_replace(substr($title, strrpos($title, '_') + 0),'',$title);
	}

	$db['title']=$title;
	
	$aff = isset($_POST['db']['affiliate_id'])? $_POST['db']['affiliate_id'] : "";
	if ($title=='')
		_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
	
	$group_id = isset($_POST['group_id']) && $_POST['group_id']>-1 ? $_POST['group_id'] : -1;
	$autoApproveAll = isset($_POST['autoApproveAll']) && 'on'==$_POST['autoApproveAll'] ? 1 : 0;
	if (empty($aff)) {
		
		if ($group_id > -1 ) {
			/* $qry = "select id from affiliates where valid = 1 and group_id = " . $group_id;
			$rsc = function_mysql_query($qry,__FILE__);
			while ($row = mysql_fetch_assoc($rsc)) {
					
					if (!$db['title']) $errors['title'] = 1;
						if (!$db['merchant_id']) $errors['merchant_id'] = 1;
						if (empty($errors)) {
							$affiliate_id = $row['id'];
							$db['affiliate_id'] = $affiliate_id;
							$db['rdate'] = dbDate();
							$db['title'] = $title. '_'.$db['affiliate_id'];
							$db['valid'] = $autoApproveAll;
							dbAdd($db,$appProm);
						}
				} */
				$affiliate_id = 0;
				$db['affiliate_id'] = $affiliate_id;
				$db['group_id'] = $group_id;
				$db['rdate'] = dbDate();
				$db['title'] = $title. '_'.$group_id;
				$db['valid'] = $autoApproveAll;
				dbAdd($db,$appProm);
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
			}
			else {

				if (!$db['title']) $errors['title'] = 1;
					if (!$db['merchant_id']) $errors['merchant_id'] = 1;
					if (empty($errors)) {
						$db['rdate'] = dbDate();
						
						$db['valid'] = $autoApproveAll;
						dbAdd($db,$appProm);
						_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
					
					}
			}
			
	} 
	else if (!empty($aff)) {
		
            $affiliate_ids = array_map('trim', explode('|', $aff));
        
            if(count($affiliate_ids) > 1){
                 
                $db['affiliate_id'] = $affiliate_ids[0];
                $db['additional_affiliates']  = '|'.implode('|', $affiliate_ids).'|';
                $db['rdate'] = dbDate();
                $db['title'] = $title;
                $db['valid'] = $autoApproveAll;
                dbAdd($db,$appProm);
                 
            }else{
	foreach ($affiliate_ids as $aff_id) {
            
             
	if (!$db['title']) $errors['title'] = 1;
		if (!$db['merchant_id']) $errors['merchant_id'] = 1;
		if (empty($errors)) {
			$db['affiliate_id'] = $aff_id;
			$db['rdate'] = dbDate();
			$db['title'] = $title. '_'.$db['affiliate_id'];
			$db['valid'] = $autoApproveAll;
			dbAdd($db,$appProm);
		}
	}
            }
            
	

			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
            
	}
	
	
	/* ------------------------------------ [ Promotions ] ------------------------------------ */
	
	}

?>
