<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

/**
 * This function will be called from the "save_deal" case.
 */
require_once('common/global.php');
$userLevel = "manager";

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/". $userLevel."/";
if (!isManager()) _goto($lout);

ini_set('memory_limit', '1024M');


$appTable = 'affiliates';
$appNotes = 'affiliates_notes';
$appDeals = 'affiliates_deals';
$appProfiles = 'affiliates_profiles';

function doPost($url){
	$parse_url=parse_url($url);
	$da = fsockopen($parse_url['host'], 80, $errno, $errstr);
	if (!$da) {
		echo "$errstr ($errno)<br/>\n";
		echo $da;
		} else {
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
	}

	
function listFields($field='',$memberField='') {
	$arr = Array(
		"id" => lang("Affiliate ID"),
		"username" => lang("Username"),
		"mail" => lang("E-Mail"),
		"website" => lang("Website"),
		"profile" => lang("Profiles"),
		"first_name" => lang("First Name"),
		"last_name" => lang("Last Name")
		);
		
		if (strlen($memberField)>0) {
			$arr["member"] = lang(ucwords($memberField));
		}
	foreach ($arr AS $k => $v) $html .= '<option value="'.$k.'" '.($k == $field ? 'selected' : '').'>'.$v.'</option>';
	return $html;
	}

if($act == "valid"){
	
		$db=dbGet($id,$appTable);
		// var_dump($db);
		// die();
		if ($db['valid']) $valid='0'; else $valid='1';
		$password = 1;
		if($db['password'] == "")
			$password = 0;
		if ($db['id']>0 &&  ($valid==1))
			echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');setStatus('. $db['id'].','. $valid .','. $password .')" style="cursor: pointer;">'.xvPic($valid).'</a>';		
		else
			echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');setStatus('. $db['id'].','. $valid .','. $password .');send_email('. $db['id'] .');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		
		//echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');send_email('. $db['id'] .');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		/*if ($db['id']>0 &&  ($valid==1))
		// die ('geger');
			//$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowActivated" and valid=1',__FILE__));
			sendTemplate($mailCode['mailCode'],$db['id']);
			// var_dump($mailCode);
			// die();
		} */
		
		
		
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		
		
		
		/* 
		if ($valid==1) {
		$mailCode = 'AffiliateAccountIsNowActivated'; */
		$affiliate_id = $id;
		/* 
		sendTemplate($mailCode,$affiliate_id);
		} */
			
		die;
		
	
}
if($act == "send_email"){
	
		$db=dbGet($id,$appTable);
		// var_dump($db);
		// die();
		if ($db['valid']) $valid='0'; else $valid='1';
		
		//echo '<a onclick="ajax(\''.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		if ($db['id']>0 &&  ($valid==1)) {
				
		// die ('geger');
			$mailCode = mysql_fetch_assoc(function_mysql_query('SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowActivated" and valid=1',__FILE__,__FUNCTION__));
			sendTemplate($mailCode['mailCode'],$db['id']);
			// var_dump($mailCode);
			// die();
		}
		
		
		
		//updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		
		
		
		/* 
		if ($valid==1) {
		$mailCode = 'AffiliateAccountIsNowActivated'; */
		$affiliate_id = $id;
		/* 
		sendTemplate($mailCode,$affiliate_id);
		} */
			echo "Mail Sent!";
		die;
		
	
}

if($_REQUEST['editPixel']){

	$act = 'editPixel';

}else if($_REQUEST['deletePixel']){

	$act = 'deletePixel';

}else if($_REQUEST['testPixel']){

	$act = 'testPixel';

}




$set->content.='
	<script type="text/javascript">
		function activate(e){
                    $("."+e).attr("readonly",false);
                    $("."+e).css("background","#fff");
             //       console.log(e + " activate 111");
		} 
		
		function deactivate(e){
                    $("."+e).attr("readonly",true);
                    $("."+e).val("");
                    $("."+e).css("background","#e2e3e3");
            //        console.log(e + " activate 222");
		}
		
		function isEmpty(e){
                    return !($("."+e).val().length>0 && $("."+e).val()!=0 && $("."+e).val()!="0" && $("."+e).val()!="");
		}
		
	</script>';


	
		$pageTitle = lang('Affiliates List');
                
		if ($act == "pending") {
			$search = 1;
			$where .= " AND valid='0'";
			$pageTitle = lang('Pending Affiliates');
			}
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
		if ($logged) $where .= " AND logged='1'";
		updateUnit($appTable,"logged='0'","lastactive <= '".date("Y-m-d H:i:s",strtotime("-20 Minutes"))."'");
		
		if ($q AND $field) {
					
			if($field=='id'){
				$field='affiliates.id';
			}
		if ($field == "id") $where .= " AND lower(".$field.")='".$q."'";
				else 
				{
					if (strtolower($field)=='website') { 
						$where .= " AND (website LIKE '%".strtolower($q)."%' or website2 LIKE '%".strtolower($q)."%' or website3 LIKE '%".strtolower($q)."%'  )";
					}
					elseif (strtolower($field)=='member') { 
					   $where .= " AND affiliates.id in (SELECT affiliateID FROM  `affiliates_campaigns_relations` WHERE  `campID` LIKE  '%".strtolower($q)."%' )";
					}
					elseif (strtolower($field)=='profile') { 
					   $where .= " AND affiliates.id in (select affiliate_id as id from affiliates_profiles where url like '%".strtolower($q)."%' )";
					 
					   
					   
					   
					} else {
					   $where .= " AND lower(".$field.") LIKE '%".strtolower($q)."%'";
					}
			}
			
		}
		elseif ($q &&   strtolower($field)=='') { 
					   
					   $where .= " AND (
						lower(affiliates.last_name) LIKE lower('%".strtolower($q)."%') or 
						lower(affiliates.first_name) LIKE lower('%".strtolower($q)."%') or 
						lower(affiliates.mail) LIKE lower('%".strtolower($q)."%') or 
						lower(affiliates.username) LIKE lower('%".strtolower($q)."%') or 
						lower(affiliates.website) LIKE lower('%".strtolower($q)."%') or 
						lower(affiliates.website2) LIKE lower('%".strtolower($q)."%') 
						)";
		}
		
		//die ($where);
		if ($userLevel=='manager') {
			$where .= " AND group_id='".$set->userInfo['group_id']."'";
		}
		else		{
			if ($group_id >= "0") $where .= " AND group_id='".$group_id."'";
		}
		
		if ($status_id >= "0") $where .= " AND status_id='".$status_id."'";
		if (!empty($utype)) $where .= " AND type='".$utype."'";
		
		if (!empty($selected_merchants)) $where .= " AND merchants IN (". $selected_merchants .")";
		
		if (!empty($utype)) $where .= " AND type='".$utype."'";

		if ($show_account=='-1') {
			$where .= " AND valid='-1'";
		}
		elseif ($show_account==='0'){
			$where .= " AND valid=0";
		}
		elseif ($show_account==1){
			$where .= " AND valid=1";
		}
		elseif ($show_account==-2){
			$where .= " AND valid=-2";
		}
		elseif ($show_account==""){
			$where .= "";
		}
		
		$getPos = $set->itemsLimit;
		
		// var_dump($set);
		// die();
                
                $pgg = $pg * $getPos;
                $sql = '';
                
                
                $boolShowDocs          = !empty($set->showDocumentsModule);
                $intAskDocTypeCompany  = empty($set->AskDocTypeCompany) ? 0 : 1;
                $intAskDocTypeAddress  = empty($set->AskDocTypeAddress) ? 0 : 1;
                $intAskDocTypePassport = empty($set->AskDocTypePassport) ? 0 : 1;
                $intDocsToIssue        = $intAskDocTypeCompany + $intAskDocTypeAddress + $intAskDocTypePassport;
                
                if ($boolShowDocs) {
                    $sql = "SELECT affiliates.*, acr.campID, acr.affiliateID,    
                                CONCAT(
                                    (IF(0 < (SELECT COUNT(*) AS count FROM `documents` WHERE `affiliate_id` = affiliates.id AND `type` = 'Passport_Driving_Licence') , 1, 0)) + 
                                    (IF(0 < (SELECT COUNT(*) AS count FROM `documents` WHERE `affiliate_id` = affiliates.id AND `type` = 'Company_Verification') , 1, 0)) + 
                                    (IF(0 < (SELECT COUNT(*) AS count FROM `documents` WHERE `affiliate_id` = affiliates.id AND `type` = 'Address_Verification') , 1, 0))
                                    , ' / " . $intDocsToIssue . "'
                                ) AS docs_fracture    
                            FROM affiliates AS affiliates 
                            LEFT JOIN affiliates_campaigns_relations AS acr ON affiliates.id = acr.affiliateID  
                            WHERE 1 = 1 " . $where . " 
							group by affiliates.id 
                            ORDER BY affiliates.id DESC;";
                    
                } else {
                    $sql = "SELECT affiliates.*, acr.campID, acr.affiliateID FROM affiliates "
                        . "LEFT JOIN affiliates_campaigns_relations acr ON affiliates.id=acr.affiliateID "
                        . "WHERE 1 = 1 " . $where . " ".
						"group by affiliates.id "
                        . "ORDER BY affiliates.id DESC;";
                }
                
                // die ($sql);
		$qq = function_mysql_query($sql,__FILE__,__FUNCTION__); //  LIMIT $pgg,$getPos
		$query = 'SELECT extraMemberParamName AS title FROM merchants' ; // WHERE id='.aesDec($_COOKIE['mid'];
		$campID = mysql_fetch_assoc(function_mysql_query($query,__FILE__,__FUNCTION__));
		
		$memberField = '';
		
			if($campID['title']){
				//if($ww['campID']){
					$memberField = $campID['title'];
					
				//////////////////////////////////////////////////////////////////////////	$affList .= '<td align="left">'.$ww['campID'].'</td>';
					
					//echo 'SELECT extraMemberParamName AS title FROM merchants WHERE merchant_id='.aesDec($_COOKIE['mid']);
					//die();
				}else{
				//////////////////////////////////////////////////////////////////////	$affList .= '<td align="left"></td>';
				}	
			//}
			
         $affiliateGroups = affiliateGroupsArray();        
		$affiliateStatus = affiliateStatusArray();
		while ($ww = mysql_fetch_assoc($qq)) {
                        
						// var_dump($ww);
						// die();
                        $intShowRed    = 0;
                        $intShowYellow = 0;
                        $intShowGreen  = 0;
                        
                        if (!empty($intAskDocTypeCompany)) {
                            $sql = "SELECT doc_status AS doc_status FROM `documents`  
                                    WHERE affiliate_id = " . $ww['id'] . " AND `type` = 'Company_Verification' 
                                    ORDER BY id DESC
                                    LIMIT 0, 1;";
                            
                            $arrRes = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                            
                            if (empty($arrRes) || 'disapproved' == $arrRes['doc_status']) {
                                $intShowRed++;
                            } elseif (!empty($arrRes) && 'not_reviewed' == $arrRes['doc_status']) {
                                $intShowYellow++;
                            } elseif (!empty($arrRes) && 'approved' == $arrRes['doc_status']) {
                                $intShowGreen++;
                            }
                        }
                        
                        if (!empty($intAskDocTypeAddress)) {
                            $sql = "SELECT doc_status AS doc_status FROM `documents`  
                                    WHERE affiliate_id = " . $ww['id'] . " AND `type` = 'Address_Verification' 
                                    ORDER BY id DESC
                                    LIMIT 0, 1;";
                            
                            $arrRes = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                            
                            if (empty($arrRes) || 'disapproved' == $arrRes['doc_status']) {
                                $intShowRed++;
                            } elseif (!empty($arrRes) && 'not_reviewed' == $arrRes['doc_status']) {
                                $intShowYellow++;
                            } elseif (!empty($arrRes) && 'approved' == $arrRes['doc_status']) {
                                $intShowGreen++;
                            }
                        }
                        
                        if (!empty($intAskDocTypePassport)) {
                            $sql = "SELECT doc_status AS doc_status FROM `documents`  
                                    WHERE affiliate_id = " . $ww['id'] . " AND `type` = 'Passport_Driving_Licence' 
                                    ORDER BY id DESC
                                    LIMIT 0, 1;";
                            
                            $arrRes = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                            
                            if (empty($arrRes) || 'disapproved' == $arrRes['doc_status']) {
                                $intShowRed++;
                            } elseif (!empty($arrRes) && 'not_reviewed' == $arrRes['doc_status']) {
                                $intShowYellow++;
                            } elseif (!empty($arrRes) && 'approved' == $arrRes['doc_status']) {
                                $intShowGreen++;
                            }
                        }
                        
                        
                        
                        if (!empty($intShowRed)) {
                            $ww['doc_status_img'] = $set->SSLprefix.'images/docs_red.png';
                            $ww['doc_status_alt'] = 'Some documents are missing, or some documents has been disapproved';
                        } elseif (!empty($intShowYellow)) {
                            $ww['doc_status_img'] = $set->SSLprefix.'images/docs_yellow.png';
                            $ww['doc_status_alt'] = 'Issued documents has not been reviewed';
                        } else {
                            $ww['doc_status_img'] = $set->SSLprefix.'images/docs_green.png';
                            $ww['doc_status_alt'] = 'All the documents has been issued and approved';
                        }
                        
                        
			$l++;
			$affList .= '<tr>
					<td>'.$ww['id'].'</td>';
				/* 	
				if($ww['valid']==1):
						$affList .= ($excel ? '' : '<td align="center"><a href="'.$userLevel.'/affiliates.php?act=new&id='.$ww['id'].'">'.lang('Edit').'</a>'.( $show_account==1 || $show_account=="" && $act!="pending" ? ' | <a href="/?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank">'.lang('Login').'</a>':'').'</td>');
				else:
						$affList .= ($excel ? '' : '<td align="center"><a href="'.$userLevel.'/affiliates.php?act=new&id='.$ww['id'].'">'.lang('Edit').'</a>'. ( $show_account==1 || $show_account=="" && $act!="pending" ? ' | <div class="tooltip"><a href="javascript:void(0);">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please activate the account to login.") .'</span>':'').'</td>');
				endif; */
				
				if($ww['valid']==1):
							if($ww['password'] == ""):							
								$affList .= ($excel ? '' : '<td align="center" style="min-width:85px"><a href="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=new&id='.$ww['id'].'" style="float:left;padding:0 6px;">'.lang('Edit').'</a>'. ( $show_account==1 || $show_account=="" && $act!="pending" ? '<span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><div class="tooltip"><a href="javascript:void(0);" oncontextmenu="return false">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please set password to login.") .'</span></div>':'').'</td>');
							else:
								$affList .= ($excel ? '' : '<td align="center" style="min-width:85px"><a href="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=new&id='.$ww['id'].'" style="float:left;padding:0 6px;">'.lang('Edit').'</a>'. ( $show_account==1 || $show_account=="" && $act!="pending" ? '<span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><a href="'.$set->SSLprefix.'?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank" oncontextmenu="return false">'.lang('Login').'</a></div>':'').'</td>');
							endif;
						else:
								$affList .= ($excel ? '' : '<td align="center" style="min-width:85px"><a href="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=new&id='.$ww['id'].'" style="float:left;padding:0 6px;">'.lang('Edit').'</a>'. ( $show_account==1 || $show_account=="" && $act!="pending" ? '<span style="float:left;"> | </span><div class="test'. $ww['id'] .'" style="float:left;padding:0 6px;"><div class="tooltip"><a href="javascript:void(0);" oncontextmenu="return false">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please activate the account to login.") .'</span></div>':'').'</td>');
						endif;
					
			$affList .='<div class="deactive_text'. $ww['id'] .'" style="display:none"><div class="tooltip"><a href="javascript:void(0);">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please activate the account to login.") .'</span></div>';
			$affList .='<div class="no_password_text'. $ww['id'] .'" style="display:none"><div class="tooltip"><a href="javascript:void(0);">'.lang('Login').'</a><span class="tooltiptext">'. lang("Please set password to login.") .'</span></div>';
			$affList .='<div class="active_text'. $ww['id'] .'" style="display:none"><a href="'.$set->SSLprefix.'?act=login&username='.$ww['username'].'&password='.$ww['password'].'&admin='.$set->userInfo['id'].'" target="_blank">'.lang('Login').'</a></div>';
				

			$affList .='<td align="left">'.$ww['username'].'</td>';

			if($campID['title']){
				if($ww['campID']){
					
					$affList .= '<td align="left">'.$ww['campID'].'</td>';
					
					//echo 'SELECT extraMemberParamName AS title FROM merchants WHERE merchant_id='.aesDec($_COOKIE['mid']);
					//die();
				}else{
					$affList .= '<td align="left"></td>';
				}	
			}
			$password = 1;
			if($ww['password']==0){
				$password = 0;
			}
			
			$affList .= '
			<td align="center"><a href="mailto:'.$ww['mail'].'">'.$ww['mail'].'</a></td>
					'.($set->ShowAffiliateTypes ? '<td align="left">'.ucwords($ww['type']).'</td>' : '' ).'
					<td align="left">'.$ww['first_name'].'</td>
					<td align="left">'.$ww['last_name'].'</td>
					'.($set->ShowIMUserOnAffiliatesList ? '<td align="left">'.$ww['IMUser'].'</td>' : ''). '
					<td align="center">'.getCountry($ww['country'],1).'</td>
					<td align="left" class="website">'.(trim($ww['website'])=='http'.$set->SSLswitch.'://' ? '' : '<a href="'.addHttpIfNeeded($ww['website']).'" target="_blank">'. ($excel ? addHttpIfNeeded($ww['website']) :  charLimit($ww['website'],30)).'</a>').'</td>
					<td align="left" style="display:none">'.(trim($ww['website2'])=='http'.$set->SSLswitch.'://' ? '' : '<a href="'.addHttpIfNeeded($ww['website2']).'" target="_blank">'.addHttpIfNeeded($ww['website2']).'</a>').'</td>
					<td align="left" style="display:none">'.(trim($ww['website3'])=='http'.$set->SSLswitch.'://' ? '' : '<a href="'.addHttpIfNeeded($ww['website3']).'" target="_blank">'.addHttpIfNeeded($ww['website3']).'</a>').'</td>
					<td align="center">'.(isset($ww['group_id']) && !empty($ww['group_id'])?$affiliateGroups[$ww['group_id']]:lang('General')).'</td>
					<td align="center">'.(isset($ww['status_id']) && !empty($ww['status_id'])?$affiliateStatus[$ww['status_id']]:lang('General')).'</td>
					<td align="center">'.dbDate($ww['rdate']).'</td>
					<td align="center">'.dbDate($ww['lastvisit']).'</td>
					'.($excel ?  '<td align="center">'.$ww['newsletter'].'</td>' : '').'
                                        ' . ($boolShowDocs ? '<td align="center"><img title="' . ucwords(lang($ww['doc_status_alt'])) . '" alt="' . ucwords(lang($ww['doc_status_alt'])) . '" src="' . $ww['doc_status_img'] . '" style="width:15px;height:15px;" />&nbsp;' . $ww['docs_fracture'] . '</td>' : '') . '
					<td align="center" class="logged" data-logged="'. $ww['logged'] .'">'.($excel ? ($ww['logged']=='logged_1' ? 'Yes' : 'No') : '<img border="0" src="'.$set->SSLprefix.$userLevel.'/images/logged_'.$ww['logged'].'.png" alt="'.dbDate($ww['lastactive']).'" title="'.dbDate($ww['lastactive']).'" />').'</td>
					<td align="center" class="valid" data-active="'. $ww['valid'] .'"  id="lng_'.$ww['id'].'">'.($excel ? ($ww['valid'] ? 'Yes' : 'No') : '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');setStatus('. $ww['id'] .','. $ww['valid'] .','. $password .');send_email('. $ww['id'].')" style="cursor: pointer;">'.xvPic($ww['valid']=='-1'?0:$ww['valid']).'</a>').'</td>
				</tr>';
			}
		$set->sortTable = 1;
		if ($l > 0) $set->sortTableScript = 1;
				$online=mysql_result(function_mysql_query("SELECT COUNT(id) FROM ".$appTable." WHERE logged='1' " . $where,__FILE__,__FUNCTION__),0);
		$qry = "SELECT COUNT(id) FROM ".$appTable . " where 1=1 " . $where;
		$totalAffiliates=mysql_result(function_mysql_query($qry,__FILE__,__FUNCTION__),0);
		$field = isset($_GET['field']) ? $_GET['field'] : "";
		$filename = "AffiliatesList_" . date('YmdHis');
		$set->content = '
	<script>
	function send_email(aff_id){
		ajax(\''.$set->SSLprefix.$set->basepage.'?act=send_email&id=\' + aff_id);
		
	}
	function setStatus(id,valid,password){
		if(password == 0)
		{
			$(".test" + id).html($(".no_password_text"+id).html());
		}
		else if(valid == 0)
		{
			$(".test"+id).html($(".active_text"+id).html());
		}
		else{
			$(".test"+id).html($(".deactive_text"+id).html());
		}
	}
	</script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="'.$set->SSLprefix.'css/multiple-select.css"/>
		<form method="get">
					<input type="hidden" name="search" value="1" />
					<input type="hidden" id="selected_merchants" name="selected_merchants">
					<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Search Affiliate').'</div>
					<div id="tab_1" style="width: 100%; background: #F8F8F8;">
					<table width="98%" border="0" cellpadding="0" cellspacing="5">
						<tr><td colspan="3" height="5"></td></tr>
						<tr>
							<td width="160" align="left">'.lang('Search').':</td><td><input type="text" name="q" value="'.$q.'" /></td>
							<td width="100"><select name="field" style="width: 120px;"> '.lang('In').' <option id="0" value="">'.lang('Choose Filter').'</option>'.listFields($field, $memberField).'</select></td>

							' . ($userLevel=='admin' ? '
							<td width="100"><select name="group_id" style="width: 100px;"><option value="">'.lang('All Groups').'</option><option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listGroups($group_id).'</select></td>
							' : '' ).'
							<td width="100"><select name="status_id" style="width: 120px;"><option value="">'.lang('All Categories').'</option><option value="0" '.($status_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>'.listStatus($status_id).'</select></td>
							<td width="100"><select name="merchant_id" id="merchant_id" style="width: 100px;" multiple="multiple"><option value="">'.lang('All Merchants').'</option>'.listMerchants(0).'</select></td>
							<td width="100"><select name="show_account" id="show_account" style="width: 130px;">
							<option value=1 '. ($show_account===1?"selected":"") .'>'.lang('Active Accounts').'</option>
							<option value="-1"'. ($show_account=='-1'?"selected":"") .'>'.lang('Deleted Accounts').'</option>
							<option value="0"'.  ($show_account=='0'?"selected":"") .'>'.lang('Pending Accounts').'</option>
							<option value="-2"'.  ($show_account=='-2'?"selected":"") .'>'.lang('Rejected Accounts').'</option>
							<option value=""'. ($show_account==""?"selected":"") .'>'.lang('Show All Accounts').'</option></select></td>
							
							'.($set->ShowAffiliateTypes ? '<td width="100"><select name="utype" style="width: 100px;"><option value="" '.($utype == "" ? " selected " : "") . '>'.lang('All Types').'</option>
							
							<option value = "Affiliate" '.($utype == "Affiliate" ? 'selected="selected"' : '').'>'. lang('Affiliate') .'</option>
							<option value = "IB" '.($utype == "IB" ? 'selected="selected"' : '').'>'. lang('IB') .'</option>
							<option value = "WhileLabel" '.($utype == "WhileLabel" ? 'selected="selected"' : '').'>'. lang('WhiteLabel') .'</option>
							<option value = "PortfolioManager" '.($utype == "PortfolioManager" ? 'selected="selected"' : '').'>'. lang('Porfolio Manager') .'</option>
							</select></td>'
							: '' ).'
							<td align="left"><input  type="submit" value="'.lang('Search').'" /></td>
							<td align="left" width="60%">
								'.lang('Total Online').': <a href="'.$set->basepage.'?act=search&logged=1"><b>'.$online.'</b></a> / '.lang('Total Affiliates').': <b>'.$totalAffiliates.'</b>
							</td>
						</tr>
					</table>
						<script src="'.$set->SSLprefix.'js/multiple-select.js"></script>
					<script>
						$("#merchant_id option[value=\'\']").remove();
						$("#merchant_id").multipleSelect({
									width: 200,
									placeholder: "Select Merchant"
								});
						$("#merchant_id").change(function(){
							$("#selected_merchants").val($(this).val());
						});
						var selects = "'. $_GET['selected_merchants'] .'";
						
						$("#merchant_id").multipleSelect("setSelects",[  '. $_GET['selected_merchants'] .'  ]);
						$("form").submit(function() {
							this.merchant_id.disable = true;
							return true; 
						});
					</script>
					
					
					'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer;" onclick="$(\'#affiliatesData\').tableExport({type:\'csv\',escape:\'false\',ignoreColumn:[1],tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
					<div class="exportCSV" style="float:left"><a style="cursor:pointer;" onclick="$(\'#affiliatesData\').tableExport({type:\'excel\',escape:\'false\',ignoreColumn:[1],tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
					</div><div style="clear:both"></div>
					
					</div>
					</form>
					<hr />
					<div class="normalTableTitle">'.$pageTitle.'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
					<script>
						
							
							$("[name=q]").keyup(function() {
							var inputValue = $("[name=q]").val();
								
								if (isNaN(inputValue)) {
									if (inputValue == 0) {
										//$("[name=field]").val("");
									} else if (inputValue.indexOf("@") != -1) {
										$("[name=field]").val("mail");
									} else {
								//		$("[name=field]").val("");
									}
								} else if (inputValue < 1) {
									$("[name=field]").val("");
								} else {
									$("[name=field]").val("id");
								}
							});
							
					</script>';
					
					$tableStr = '<table class=" table '.(!empty($affList)?'tablesorter':'normal'). ' mdlReportFields" width="100%" border="0" cellpadding="0" cellspacing="0" id="affiliatesList">
						<thead>
						<tr class="table-row">
							<th  class="table-cell">'.lang('Affiliate ID').'</th>
							'.($excel ? '' : '<th  class="table-cell" width="90px">'.lang('Actions').'</th>').'
							<th class="table-cell" align="left">'.lang('Username').'</th>
							'.($campID['title'] ? '<th class="table-cell" align="left">'.ucwords(lang($campID['title'])).'</th>' : '').'
							<th  class="table-cell" align="center">'.lang('E-Mail').'</th>
							'.($set->ShowAffiliateTypes? '<th class="table-cell" align="center">'.lang('Type').'</th>' : '').'
							<th class="table-cell" align="left">'.lang('First Name').'</th>
							<th class="table-cell" align="left">'.lang('Last Name').'</th>
							'.($set->ShowIMUserOnAffiliatesList ? '<th class="table-cell" align="center">'.lang('IMUser').'</th>' : ''). '
							<th class="table-cell" align="center">'.lang('Country').'</th>
							<th class="table-cell" align="left">'.lang('Website URL').'</th>
							<th class="table-cell" align="left" style="display:none">'.lang('Website').' 2</th>
							<th class="table-cell" align="left" style="display:none">'.lang('Website').' 3'.'</th>
							<th class="table-cell" align="center">'.lang('Group').'</th>
							<th class="table-cell" align="center">'.lang('Category').'</th>
							<th  class="table-cell"align="center">'.lang('Registration Date').'</th>
							<th class="table-cell" align="center">'.lang('Last Visit').'</th>
							'.($excel ?  '<th class="table-cell" align="center">'.lang('Newsletter').'</th>' : '').'
                                                        ' . ($boolShowDocs ? '<th class="table-cell" align="center">' . lang('Docs') . '</th>' : '') . '
							<th class="table-cell" align="center">'.lang('Logged').'</th>
							<th class="table-cell" align="center">'.lang('Active').'</th>
						</tr></thead><tfoot></tfoot>
						<tbody>
						'.$affList.'
					</table>
					'.(!empty($affList)?'
					<script>
				$(document).ready(function(){
					thead = $("thead").html();
					tfoot = $("tfoot").html();
					txt = "<table id=\'affiliatesData\' class=\'mdlReportFieldsData\'>";
					txt += "<thead>" + thead + "</thead>";
					txt += "<tbody>";
					$($("#affiliatesList")[0].config.rowsCopy).each(function() {
						txt += "<tr>" + $(this).html()+"</tr>";
					});
					txt += "</tbody>";
					txt += "<tfoot>" + tfoot + "</tfoot>";
					txt += "</table>";
					$("body").append("<div style=\'display:none\' class=\'dumpdata\'>"+ txt +"</div>");
					$(".dumpdata .website").each(function(k,v){
						href = $(v).find("a").attr("href");
						$(this).html(href);
					});
					$(".dumpdata .logged").each(function(k,v){
						logged = $(v).data("logged");
						$(this).html(logged);
					});
					$(".dumpdata .valid").each(function(k,v){
						valid = $(v).data("active");
						$(this).html(valid);
					});
				});
				</script>
					':'');
					
					//excelExporter($tableStr,'Affiliate_list');
					$set->content.=$tableStr.'
					</div>'.getPager();
					
					//MODAL
					$fields = getReportsHiddenCols("affiliateListReport","manager",$set->userInfo['id']);
					if($fields){
						$set->AffiliateListHiddenCols = $fields;
					}
					$userlevel = "manager";
					$myReport = lang("Affiliate List");
					include "common/ReportFieldsModal.php";
		theme();
	
	
?>