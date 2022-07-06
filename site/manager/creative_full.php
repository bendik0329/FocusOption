<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
//require_once('common/BarcodeQR.php');
require_once ('common/ShortUrl.php');
require_once('common/InStyle.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/manager/";
if (!isManager()) _goto($lout);

$appTable = 'merchants_creative';
$appProm = 'merchants_promotions';

$group_id="";
if ($set->userInfo['level']=='manager')  
$group_id = $set->userInfo['group_id'] ;

$set->webAddress = $set->isHttps?$set->webAddressHttps:$set->webAddress;

$max_upload = 15; // Max Upload Per Page
//die('Act: ' . $act);

$isNew = (isset($_GET['new']) && $_GET['new']==1 ? 1 : 0);
//die(var_dump($isNew));

switch ($act) {
	// ----------------------------------------- [ Edit Banner ] -----------------------------------------
	case "deletePromotions":
				
				if ($set->AllowManagerEditrCreative==0){
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
				}
		
				// var_dump($_POST);
				
				$title = isset($_POST['promotionName']) ? $_POST['promotionName'] : "";
				$merchant_id = isset($_POST['merchant_id']) ? $_POST['merchant_id'] : "";
				if (!empty($title)) {
				if ($_POST['deleteAllWithSamePrefix']=='on') {
					$title = str_replace(substr($title, strrpos($title, '_') + 0),'',$title);
				$qry = "delete from $appProm where title like '".$title."_%' AND merchant_id=". $merchant_id;
						// die($qry);
				}else{
				$qry = "delete from $appProm where title = '".$title."' AND merchant_id=". $merchant_id;
				}
				function_mysql_query(
				$qry,__FILE__);
				}
				
			if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
		
			break;
	
	
	case "deletecreative":
	
				$merchant_id = isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "1";
			if ($set->AllowManagerEditrCreative==0){
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
			}
		
		
				// var_dump($_GET);
				$id = isset($_GET['id']) ? $_GET['id'] : "";
				if ($id) {
				function_mysql_query("update merchants_creative set valid = -1 where id = " .$id,__FILE__);
				}	
	
				
				//$qrys = $qrys .'merchant_id=' . (isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "1");
				$qrys = $qrys .'&type=' .( isset($_GET['type']) ? $_GET['type'] : "");
				$qrys = $qrys .'&category_id=' .(isset($_GET['category_id']) ? $_GET['category_id'] : "");
				$qrys = $qrys .'&lang=' .( isset($_GET['lang']) ? $_GET['lang'] : "");
				$qrys = $qrys .'&creativedimenstion=' .( isset($_GET['creativedimenstion']) ? $_GET['creativedimenstion'] : "");
				$qrys = $qrys .'&promotion=' .( isset($_GET['promotion']) ? $_GET['promotion'] : "");
				$qrys = $qrys .'&valid=' .( isset($_GET['valid']) ? $_GET['valid'] : 1);
				$qrys = $qrys .'&q=' .( isset($_GET['q']) ? $_GET['q'] : "");
				
	
				if ($merchant_id>0)
					_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id.$qrys);
				else  {
					
					_goto($set->SSLprefix.$set->basepage.'?1=1'.$qrys);
				}
		
			break;
				case "restore":
				// var_dump($_GET);
				$id = isset($_GET['id']) ? $_GET['id'] : "";
				$merchant_id = isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "1";
				
						if ($set->AllowManagerEditrCreative==0){
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
						}
		
		
				
				if ($id) {
				function_mysql_query("update merchants_creative set valid = 0 where id = " .$id,__FILE__);
				}	
				
				if ($merchant_id>0)
					_goto($set->SSLprefix.$set->basepage.'?act=trash&merchant_id='.$merchant_id);
				else  {
					// die ('gerger');
					_goto($set->SSLprefix.$set->basepage.'?act=trash');
				}
		
			break;
			

	case "del_banner":
		
			if ($set->AllowManagerEditrCreative==0) {
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
			}
		
		
		
		$ww=dbGet($id,$appTable);
		if (file_exists($ww['file'])) ftDelete($ww['file']);
		function_mysql_query("DELETE FROM ".$appTable." WHERE id='".$ww['id']."'",__FILE__);
		die('<a href="'.$set->SSLprefix.$set->basepage.'?merchant_id='.$ww['merchant_id'].'">'.lang('Go Back').'!</a>');
		break;
		
	case "save_banner":
	
			if ($set->AllowManagerEditrCreative==0){
					if ($merchant_id>0)
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$merchant_id);
			else 
			_goto($set->SSLprefix.$set->basepage);
			}
		
		
		
		$db['last_update'] = dbDate();
		
		if (chkUpload('file')) {
			$getOldBanner = dbGet($db['id'], $appTable);
			
			if (file_exists($getOldBanner['file'])) {
				ftDelete($getOldBanner['file']);
				$oldFileName = explode("/",$getOldBanner['file']);
				$oldFileName = explode(".",$oldFileName[count($oldFileName)-1]);
			}
			$db['url'] = trim($db['url']);
			
			$randomFolder =mt_rand(10000000, 99999999);
			$folder = 'files/banners/tmp/' . $randomFolder ."/";
			 if (!is_dir('files/banners/tmp')) {
				 mkdir('files/banners/tmp');
			 }
			 if (!is_dir($folder)) {
				 mkdir($folder);
			 }
			
			$db['file'] = UploadFile('file', '5120000', 'jpg,gif,swf,jpeg,png', ($oldFileName[0] ? $oldFileName[0] : ''), $folder);
			$exp        = explode(".", $db['file']);
			$ext        = strtolower($exp[count($exp) - 1]);
			
			
			if ($ext == "swf") {
				$db['type'] = "flash";
			} elseif ($db['scriptCode']) {
				$db['type'] == "script";
			} else {
				$db['type'] = "image";
			}
			list($db['width'], $db['height']) = getimagesize($db['file']);
		}

		
		if ($valid) {
			$db['valid'] = 1; 
		} else {
			$db['valid'] = 0;
		}
		// var_dump($db);
		// die();
		dbAdd($db, $appTable);
		
		_goto($set->SSLprefix.$set->basepage . '?act=edit_banner&id=' . $db['id'] . '&ty=1');
		break;

		
	case "edit_banner":
	
	
		$webAddress = ($typeURL == 2?$set->webAddressHttps:$set->webAddress);
		
		
		$db         = dbGet($id, $appTable);
		$merchantww = dbGet($db['merchant_id'], 'merchants');
		$adminww    = dbGet($db['admin_id'], 'admins');
		
                // Retrieve the affiliate_id.
                $affiliate_id = retrieveAffiliateId($affiliate_id);
                
                
		if ($fParam) {
                    $freeParam = '&p1=' . $fParam;
                }
		if ($subid) {
                    $subid = '&p2=' . $subid;
                }
                
                //die('Test: ' . $affiliate_id);
		$tag = 'a' . $affiliate_id . '-b' . $db['id'] . '-p' . $profile_id . $freeParam.$subid;  // Creat CTag.
		
		if ($db['type'] == "link") {
			$link = $webAddress.'click.php?ctag='.$tag;
			$code = '<!-- '.$set->webTitle.' Affiliate Code -->
					<a href="'.$link.'" target="_blank">'.$db['title'].'</a>
					<!-- // '.$set->webTitle.' Affiliate Code -->';
			$preview = '<a href="'.$link.'" target="_blank">'.$db['title'].'</a>';
			
		} else if ($db['type'] == "widget") {
			$link = $webAddress.'view.php?ctag='.$tag;
			$code = '<!-- '.$set->webTitle.' Affiliate Code -->
					<iframe frameborder="0" src="'.$webAddress.'view.php?ctag='.$tag.'" style="width: '.$db['width'].'px; height: '.$db['height'].'px;" scrolling="no"></iframe>
					<!-- // '.$set->webTitle.' Affiliate Code -->';

			if (strpos($db['iframe_url'],'iframe')==1)
				$preview = $db['iframe_url'];
			else
				$preview = '<iframe frameborder="0" src="'.$db['iframe_url'].'?ctag='.$tag.'&width='.$db['width'].'&height='.$db['height'].'" style="width: 500px; height: 250px;" scrolling="yes"></iframe>';
				
		} else if ($db['type'] == "script") {
			$link = $webAddress.'ad.g?ctag='.$tag;
			$code = '<!-- '.$set->webTitle.' Affiliate Code -->
					<iframe src="'.$link.'" width="'.$db['width'].'" height="'.$db['height'].'" frameborder="0" scrolling="no"></iframe>
					<!-- // '.$set->webTitle.' Affiliate Code -->';
			$preview = $db['scriptCode'];
			
		}  else if ($db['type'] == "mail" OR $db['type'] == "content") {
			$code = '<!-- '.$set->webTitle.' Affiliate Code -->
			<!-- // '.$set->webTitle.' Affiliate Code -->';
			
			if ($db['type'] == "content") {
				$actType = 'showContent'; 
			} else {
		    	 $actType = 'showMail'; 
			}
			
			//$preview = '<iframe src="'.$set->basepage.'?act='.$actType.'&id='.$db['id'].'&ctag='.$tag.'" width="100%" height="500" frameborder="0" scrolling="no" zoom="50%"></iframe><br /><a href="'.$set->basepage.'?act='.$actType.'&id='.$db['id'].'&ctag='.$tag.'" target="_blank" style="font-size: 20px; font-weight: bold; color: green;">'.lang('Click here to'). ' '.lang('open in new window').'</a>';
			
			$preview = '<iframe src="' . $set->SSLprefix.$set->basepage . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag 
						. '" width="100%" height="500" frameborder="1" scrolling="no" zoom="50%">
						</iframe>
						<br />
						<a href="' . $set->SSLprefix.$set->basepage . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag . '" target="_blank" style="font-size: 20px; font-weight: bold; color: green;">' 
							. lang('Click here to') . ' ' . lang('open in new window') 
						. '</a>';
			
			// die ($preview);
			
		} else {
			$link = $webAddress . 'ad.g?ctag=' . $tag;
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
                                                document.location.href = "' . $webAddress . 'click.php?ctag=' . $tag . '";
                                        };
                                </script>
				<!-- // ' . $set->webTitle . ' Affiliate Code -->';
                        
			//$preview = getBanner($db['file'], 80);
			$preview = strpos($db['file'],'/tmp')?'<img src="../images/wheel.gif">' : getBanner($db['file'], 80);
		}
        // var_dump($db);
// die();        
		
		
//if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.'view.php?ctag='.$tag.'" alt="'.$db['alt'].'" title="'.$db['alt'].'" />';
if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$webAddress.'view.php?ctag='.$tag.'" alt="'.$db['title'].'" title="'.$db['title'].'" />';
// if ($db['type'] == "image") $srcFile = '<img border="0" src="'.$set->webAddress.$db['file'].'" alt="'.$db['alt'].'" title="'.$db['alt'].'" />';
if ($db['type'] == "link") $srcFile = $db['title'];
if ($db['type'] != "mobileleader" AND $db['type'] != "mobilesplash") $codelink = '
	<tr>
		<td>'.lang('Direct Link').':</td><td><input type="text" name="" value="'.str_replace("ad.g","click.php",$link).'" onclick="this.focus(); this.select();" style="width: 350px;" /></td>
	</tr><tr>
		<td valign="top">'.lang('HTML Code').':</td><td><textarea cols="47" rows="5" onclick="this.focus(); this.select();"><a href="'.str_replace("ad.g","click.php",$link).'">'.$srcFile.'</a></textarea></td>
	</tr>
';

// All Affiliates.

$affiliateqq = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ".(!empty($group_id) ? " and group_id = " . $group_id : "" )." ORDER BY id ASC",__FILE__);

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

$allProfiles = '';

if ($affiliate_id) {
    $qry = "SELECT id,name,url FROM affiliates_profiles WHERE affiliate_id='".$affiliate_id."' AND valid='1' ORDER BY id ASC";
	// die ($qry);
	$profileqq = function_mysql_query($qry,__FILE__);
    
    while ($profileww = mysql_fetch_assoc($profileqq)) {
        $allProfiles .= '<option value="'.$profileww['id'].'" '.($profileww['id'] == $profile_id ? 'selected' : '').'>'.$profileww['id'].' ['.$profileww['name'].'] '
                     .  $profileww['url'].' (Site ID: '.$profileww['id'].')</option>';
    }
} else {
    // $allProfiles should be empty.
    $allProfiles = '';
}


// All Affiliates

	$pageTitle = lang('Get Creative Code');
				$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

		$set->content .= '<div align="left">
						<table width="99%"><tr>
							<td width="50%" align="left" valign="top" >
								<div class="normalTableTitle">'.lang('Creative Details').':</div><br />
								<form method="post" enctype="multipart/form-data">
								<input type="hidden" name="db[id]" value="'.$db['id'].'" />
								'.($db['mail'] ? '<input type="hidden" name="mailCode" value="1" />' : '').'
								<input type="hidden" name="act" value="save_banner" />
								<table cellspacing="8" cellpadding="0">
								'.($ty ? '<tr><td colspan="2" style="color:green;font-weight:bold;font-size: 14px;" class="">- '.lang('The page is up to date').' ('.dbDate().')</td></tr>' : '').'
								<tr><td class="blueText">ID:</td><td class="greenText">#'.$db['id'].'</td></tr>
								<tr><td class="blueText">'.lang('Uploaded By').':</td><td class="greenText">'.$adminww['first_name'].' '.$adminww['last_name'].'</td></tr>
								<tr><td class="blueText">'.lang('Uploaded Date').':</td><td class="greenText">'.dbDate($db['rdate']).'</td></tr>
								<tr><td class="blueText">'.lang('Last Update').':</td><td class="greenText">'.dbDate($db['last_update']).'</td></tr>
								<tr><td class="blueText"></td><td class="greenText"><input type="checkbox" name="valid" '.($db['valid'] ? 'checked' : '').' />'.lang('Publish Banner').'</td></tr>
								<tr><td class="blueText">'.lang('Merchant Name').':</td><td class="greenText">'.$merchantww['name'].'</td></tr>
								<tr><td class="blueText">'.lang('Creative Type').':</td><td class="greenText">'.ucwords($db['type']).'</td></tr>
								<tr><td class="blueText">'.lang('Creative Category').':</td><td align="left"><select name="db[category_id]" style="width: 130px;"><option value="">'.lang('General').'</option>'.listCategory($db['category_id'],$merchant_id).'</select></td></tr>' .
								(strtolower($db['type'])!='link' ? '
								<tr><td class="blueText">'.lang('Creative Size').':</td><td class="greenText">'.($db['width']).' X '.($db['height']). '</td></tr>' : '' ) . '
								<tr><td class="blueText">'.lang('Language').':</td><td><select name="db[language_id]" style="width: 263px;">'.listLangs($db['language_id']).'</select></td></tr>
								<tr><td class="blueText">'.lang('Promotion').':</td><td><select name="db[promotion_id]" style="width: 263px;"><option value="">'.lang('General').'</option>'.listPromotions($db['promotion_id'],$db['merchant_id'],"","",0).'</select></td></tr>
								<tr><td class="blueText">'.lang('Creative Name').':</td><td><input type="text" name="db[title]" value="'.$db['title'].'" style="width: 250px;" /></td></tr>
								<tr><td class="blueText">'.lang('Creative URL').':</td><td><input type="text" id="edit_creative_url" name="db[url]" value="'.str_replace('"','&quot;',(trim($db['url']))).'" style="width: 250px;" /></td></tr>
								<tr><td class="blueText">'.lang('Creative ALT').':</td><td><input type="text" name="db[alt]" value="'.$db['alt'].'" style="width: 250px;" /></td></tr>
								' . ($set->AllowManagerEditrCreative==1 ? '
								'.($db['type'] == "flash" || $db['type'] == "image" || $db['type'] == "mobilesplash" || $db['type'] == "mobileleader" ? '
									<tr><td class="blueText">'.lang('Creative File').':</td><td>'.(strpos($db['file'],"/tmp")?fileField('file',"../images/wheel.gif"):fileField('file',$db['file'])).'</td></tr>
									' : ($db['type'] == "script" || $db['type'] == "mail" || $db['type'] == "content" ? '
									<tr><td class="blueText">'.($db['type'] == "mail" ? 'E-Mail' : ($db['type'] == "Content"? 'Content' :'Script')).' Code:</td><td><textarea name="db[scriptCode]" cols="50" rows="8">'.$db['scriptCode'].'</textarea></td></tr>
									'.($db['type'] == "script" ? '<tr><td class="blueText">'.lang('Width').':</td><td><input type="text" name="db[width]" value="'.$db['width'].'" /></td></tr>
									<tr><td class="blueText">'.lang('Height').':</td><td><input type="text" name="db[height]" value="'.$db['height'].'" /></td></tr>' : '').'
									' : '
									<tr><td class="blueText">'.lang('IFrame URL').':</td><td><input type="text" name="db[iframe_url]" value="'.str_replace('"','&quot;',($db['iframe_url'])).'" /></td></tr>
									<tr><td class="blueText">'.lang('Width').':</td><td><input type="text" name="db[width]" value="'.$db['width'].'" /></td></tr>
									<tr><td class="blueText">'.lang('Height').':</td><td><input type="text" name="db[height]" value="'.$db['height'].'" /></td></tr>
									')).'
								<tr><td class="blueText"></td><td><input type="submit" value="'.lang('Save').'" /></td></tr>':'').'
							</table>
							</form>
							</td><td align="left" valign="top"  style="text-align:center;">
									<div class="normalTableTitle">'.lang('Preview').':</div><br />
								'.($db['type'] == "image" || $db['type'] == "flash" || $db['type'] == "mobileleader" || $db['type'] == "mobilesplash" ? 
								(strpos($db['file'],"/tmp")?'<img class="popupTrackingWindowImg" src="../images/wheel.gif"><br/></br>' . lang('System is checking for virus. Please refresh in a minute.'):
								'<div class="outerBox_preview">
									<div class="wrapper_preview">
										<img class="popupTrackingWindowImg" src="'. $db['file'].  '" alt="" />
									</div>
								</div>
								') : $preview) .'
									<br /><br /><hr /><br />
									<div class="normalTableTitle">'.lang('Get Tracking Code For Affiliate').'</div>
                                                                            
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
                                                                        
                                                                            <form method="get" id="getTrackingCodeForAffiliate">
                                                                                    <input type="hidden" name="act" value="edit_banner" />
                                                                                    <input type="hidden" name="id" value="'.$id.'" />
                                                                                    <table>
                                                                                    <tr>
                                                                                        <td>'.lang('Choose Affiliate').':</td>'
                                                                                     . '<td>'
                                                                                            .'<div class="ui-widget" style="text-align:left;">'
                                                                                            . '<!-- name="affiliate_id" -->'
                                                                                            . '<select id="combobox">'
                                                                                            . '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
                                                                                            . $allAffiliates
                                                                                            .'</select>
                                                                                            </div>
                                                                                        </td>
                                                                                    </tr>
                                                                                    
                                                                                    <tr><td>'.lang('Choose Profile').':</td><td><select name="profile_id" style="width: 213px;"><option selected value="">'.lang('Choose Profile').'</option>'
																					.$allProfiles.
																					
																					'</select></td></tr>
																					
                                                                                    <tr><td>'.lang('Dynamic Parameter').':</td><td><input type="text" name="fParam" value="'.$_GET['fParam'].'" style="width: 200px;" /></td>
																					'.(empty($subid) ? '<td> <div onclick="javascript:showDiv();">+</div></td>' : '' ).'
																					</tr>
																					<tr>
																					
																					<td style="'.(!empty($subid) ? '' : 'display:none;').'" id="tow">'.lang('Dynamic Parameter2').':</td><td style="'.(!empty($subid) ? '' : 'display:none;').'" id="tow2"><input type="text" name="subid" value="'.$_GET['subid'].'" style="width: 200px;" />
																					
																					</td>
																					</tr>
																					
																						' . ($set->AllowSecuredTrackingCode ==1?'
																						<tr><td colspan=2 height=5px></td></tr>
																					<tr><td align="left">
																					'. lang('URL') .': </td><td><select name="typeURL"  style="width: 210px;">
																					<option value=1 '. (isset($_GET['typeURL'])?($_GET['typeURL']==1?'selected':''):' selected') .'>'. lang('Default URL') . '</option>
																					<option value=2 '. (isset($_GET['typeURL'])?($_GET['typeURL']==2?'selected':''):'') .'>'. lang('Secured URL') . '</option></select>
																					</td></tr><tr><td colspan=2 height=5px></td></tr>':'').'
																					
                                                                                    <tr>'.(strpos($db['file'],'/tmp')?'':'<td></td><td><input type="submit" onclick="return getCodeValidation()" value="'.lang('Get Code').'" /></td>') .'</tr>
                                                                                    </table>
                                                                                  </form>';
                
										if ($affiliate_id && !($db['type'] == "mail" || $db['type'] == "content")) {
                                                                                    $set->content .= '
																					<table><tbody><tr><td vertical-align="middle">
																					Javascript</td><td><textarea cols="56" rows="10" onclick="this.focus(); this.select();" style="overflow: auto; font-size: 11px;">'.$code.'</textarea></td></tr>'.$codelink.'</tbody></table>';
                                                                                    
                                                                                } else if ($affiliate_id && ($db['type'] == "mail" || $db['type'] == "content")) {
												$url = str_replace('//','/',$set->webAddress . $set->basepage) . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag;
												$set->content .='<div class="blueText">'.($db['type'] == "mail" ? 'E-Mail' : ($db['type'] == "Content"? 'Content' :'Script')).' '.lang('Affiliated HTML Code').':</div><div><textarea name="db[scriptCode111]" cols="50" rows="8">'. str_replace('{ctag}',$tag,$db['scriptCode']).'</textarea></div>';
												$set->content .='<br><br><div class="blueText">'.lang('Direct Link').':&nbsp;&nbsp;&nbsp;<a href="' . $set->basepage . '?act=' . $actType . '&id=' . $db['id'] . '&ctag=' . $tag . '" target="_blank" style="font-size: 15px;  color: green;">' . lang('Click here to') . ' ' . lang('open in new window') 	. '</a>
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
									$fullurl =  'http://'.$_SERVER[HTTP_HOST] . '/facebookshare.php?q=' . $url.'&pta='.rawurlencode(time());
									$dispShorterLink =1 ;
									//die ($fullurl);
																
									}
										if ($dispShorterLink==0) {
										$set->content .= '<table><tr><td style="vertical-align:top;width:74px;">'.lang('Facebook Share').'</td><td><textarea cols="45" rows="8">'.$set->webAddress.'facebookshare.php?brand='.rawurlencode($merchantww['name']).'&name='.rawurlencode($db['title']).'&image='.rawurlencode($db['file']).'&url='.rawurlencode(str_replace("ad.g","click.php",$link)).'&date='.rawurlencode($db['last_update']).'&pta='.rawurlencode(time()).'</textarea></td></tr></table>';
										} else {
										$set->content .= '<table><tr><td style="vertical-align:top;width:74px;">'.lang('Facebook Share').': </td><td><textarea cols="45" rows="2">'.$fullurl.'</textarea></td></tr></table>';
									}
									}
									
	
									if($set->qrcode  and (($db['type'] == "link" or $db['type']=='image') && $db['file'] && $affiliate_id)){
									//$qr = new BarcodeQR();
									//$qr->url(str_replace("ad.g","click.php",$link),1);
									//$qrCode = file_get_contents($set->webAddress."common/BarcodeQR.php?link=".$link);
									//die ($qrCode);
									//$base64 = 'data:image/PNG;base64,' . base64_encode($qrCode );
									
									
									include_once('common/BarcodeQR.php');
									
									
									$qrImagePath = 'files/qr/' . $db['id']. '/';
									
									// $qrImagePath = 'files/qr/' . $ww['id']. '/qrcode.png';
									$folder = 'files/qr/' . $db['id'];
									 if (!is_dir('files/qr')) {
										 mkdir('files/qr');
									 }
									 if (!is_dir($folder)) {
										 mkdir($folder);
									 }
									$qr = new BarcodeQR();
									$qr->url(str_replace("ad.g","click.php",$link),1);
									$qr->draw(150,$qrImagePath);
								
									
								
									$file = $set->webAddress .$qrImagePath . "qrcode.png";	

								
										$set->content .= '<table><tr><td style="vertical-align:top">'.lang('QR Code').':</td><td><img src='.$file.' /></td></tr></table>';
									}
									
								$set->content.='</td>
						</tr></table>
					</div>
	<style>
						#bottom_table , 
						.headerSite,
						.headerSite + *,
						.headerSite + * +*{
							display:none!important;
						}
						body .content{
							padding-bottom:0;
						}
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
											
											$(document).ready(function(){
													$("#edit_creative_url").blur(function() {
												  var input = $(this);
												  var val = input.val();
												  if (val && !val.match(/^http([s]?):\/\/.*/)) {
													input.val("http://" + val);
												  }
												});
												});
                                        </script>
                                        ';
		theme();
		break;
	
	// ----------------------------------------- [ Edit Banner ] -----------------------------------------
	
                
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'bnr_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	
	case "add":
	
		if (!empty($errors)) {

		} else {
			
			if ($form_type == "2") {
                            $max_upload = 5;
			} elseif ($form_type > "2") {
                            $max_upload = 1;
			}
			
                        
			// Flag, that indicate whether default values are in use.
			$boolUseDefaultName = false;
                        
			/*for ($i = 1; $i <= $max_upload; $i++) {
				unset($db);
				$insert             = 0;
				$db['language_id']  = $creative_lang;
				$db['promotion_id'] = $creative_promotion;
				$db['category_id']  = $category_id;
				$db['url']          = $creative_url;
				$db['admin_id']     = $set->userInfo['id'];
				$db['merchant_id']  = $merchant_id;
				$db['rdate']        = dbDate();
				$db['last_update']  = dbDate();
				$db['valid']        = $creative_status;
				
				if ($form_type == "1") {
                                    
                                    $db['title'] = (!empty($_POST['name1_' . $i]) ? $_POST['name1_' . $i] : $creative_name . '_' . $i);
                                    
                                    if (chkUpload('file1_' . $i)) {
                                        $db['file'] = UploadFile('file1_' . $i, '5120000', 'jpg,gif,swf,jpeg,png', '', 'files/banners/');
                                        $exp        = explode(".", $db['file']);
                                        $ext        = strtolower($exp[count($exp) - 1]);
                                        
                                        if ($ext == "swf") { 
                                            $db['type'] = "flash"; 
                                        } else { 
                                            $db['type'] = "image";
                                        }
                                        
                                        list($db['width'], $db['height']) = getimagesize($db['file']);
                                        $db['alt'] = (isset($_POST['alt1_' . $i]) ? $_POST['alt1_' . $i] : $creative_alt);
                                        $insert    = 1;
                                    }
                                    
				} elseif ($form_type == "2") {
					if (empty($_POST['name2_' . $i]) && 1 == $i) {
                                            $db['title'] = $_POST['creative_name'];
                                            $boolUseDefaultName = true;
					    
					} else {
                                            $db['title'] = $_POST['name2_' . $i];
					}
					
					if (empty($_POST['alt2_' . $i])) {
                                            $db['alt'] = $_POST['creative_alt'];
					} else {
                                            $db['alt'] = $_POST['alt2_' . $i];
					}
					
					if (isset($db['title']) && !empty($db['title'])) { 
                                            $db['type'] = "link";
					    $insert     = 1; 
					}
				
				} elseif ($form_type == "3") {
                                    $db['title']      = (!empty($_POST['name3_' . $i]) ? $_POST['name3_' . $i] : $creative_name . '_' . $i);
                                    $db['type']       = "widget";
                                    $db['height']     = $_POST['iframe3_height_' . $i];
                                    $db['width']      = $_POST['iframe3_width_' . $i];
                                    $db['iframe_url'] = $_POST['iframe3_url_' . $i];
                                    
                                    if ($db['title'] && $db['iframe_url'] != "http://" && $db['iframe_url']) {
                                            $insert = 1;
                                    }
					
				} elseif ($form_type == "4") {
                                    if (chkUpload('file4_' . $i)) {
                                        $db['title'] = $_POST['name4_' . $i];
                                        $db['file']  = UploadFile('file4_' . $i, '5120000', 'jpg,gif,swf,jpeg,png', '', 'files/banners/');
                                        $exp         = explode(".", $db['file']);
                                        $ext         = strtolower($exp[count($exp) - 1]);
                                        $db['type']  = "mobileleader";

                                        list($db['width'], $db['height']) = getimagesize($db['file']);
                                        $db['alt'] = (!empty($_POST['alt4_' . $i]) ? $_POST['alt4_' . $i] : $creative_alt);
                                        $insert    = 1;
                                    }
						
				} elseif ($form_type == "5") {
					if (chkUpload('file5_' . $i)) {
                                            $db['title'] = $_POST['name5_' . $i];
                                            $db['file']  = UploadFile('file5_' . $i, '5120000', 'jpg,gif,swf,jpeg,png', '', 'files/banners/');
                                            $exp         = explode(".", $db['file']);
                                            $ext         = strtolower($exp[count($exp) - 1]);
                                            $db['type']  = "mobilesplash";

                                            list($db['width'], $db['height']) = getimagesize($db['file']);
                                            $db['alt'] = (!empty($_POST['alt5_' . $i]) ? $_POST['alt5_' . $i] : $creative_alt);
                                            $insert    = 1;
					}
					
				} elseif ($form_type == "6") {
                                    $db['type']       = "mail";
                                     $db['title'] = (!empty($_POST['name6_' . $i]) ? $_POST['name6_' . $i] : $_POST['creative_name']); ;
									 
									 
									 $db['alt'] = (!empty($_POST['alt6_' . $i]) ? $_POST['alt6_' . $i] : $creative_alt);
									$inlinecss        = new instyle();
                                    $strConvertedHtml = $inlinecss->convert($_POST['scriptCode6']);
                                    $db['scriptCode'] = $strConvertedHtml;
                                    $insert           = 1;
					
				} else if ($form_type == "7") {
                                    $db['type'] = "content";
                                    $db['title'] = (!empty($_POST['name7_' . $i]) ? $_POST['name7_' . $i] : $_POST['creative_name']); ;
									 $db['alt'] = (!empty($_POST['alt7_' . $i]) ? $_POST['alt7_' . $i] : $creative_alt);
									$inlinecss        = new instyle();
                                    $strConvertedHtml = $inlinecss->convert($_POST['scriptCode7']);
                                    $db['scriptCode'] = $strConvertedHtml;
                                    $insert           = 1;
				}
				
				if ($insert) {
                                    dbAdd($db, $appTable, array('scriptCode'));
				}
				
				// Stop the loop in case of default values usage.
				if ($boolUseDefaultName) {
                                    break;
				}
			}*/
			
                        for ($i = 1; $i <= $max_upload; $i++) {
				unset($db);
				$insert             = 0;
				$db['language_id']  = $creative_lang;
				$db['promotion_id'] = $creative_promotion;
				$db['category_id']  = $category_id;
				$db['url']          = trim($creative_url);
				$db['admin_id']     = $set->userInfo['id'];
				$db['merchant_id']  = $merchant_id;
				$db['rdate']        = dbDate();
				$db['last_update']  = dbDate();
				$db['valid']        = $creative_status;
                                
				if ($form_type == '1') {
                                    if (chkUpload('file1_' . $i)) {
                                        // '$db['file']' is a path to the file.
										
										$randomFolder =mt_rand(10000000, 99999999);
										$folder = 'files/banners/tmp/' . $randomFolder ."/";
										 if (!is_dir('files/banners/tmp')) {
											 mkdir('files/banners/tmp');
										 }
										 if (!is_dir($folder)) {
											 mkdir($folder);
										 }
										 
                                        $db['file'] = UploadFile('file1_' . $i, '5120000', 'jpg,jpeg,swf,bmp,gif,png,ico,pdf,pps', '',$folder);
                                        
                                        if (empty($db['file'])) {
                                            if (!empty($errors)) {
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&errors=' . json_encode($errors));
                                            } else {
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id);
                                            }
                                        }
                                        
                                        $intNumberOfInserts = count($db['file']);
                                        
                                        for ($intCnt = 0; $intCnt < $intNumberOfInserts; $intCnt++) {
											
											$nm = $intCnt +1;
											
											$title = "";
											if(!empty($_POST['name1_' . $i]))
											$title = $_POST['name1_' . $i] . "_" . $nm;
										
                                            $arrData                 = [];
                                            $arrData['file']         = $db['file'][$intCnt];
                                            $arrData['language_id']  = $creative_lang;
                                            $arrData['promotion_id'] = $creative_promotion;
                                            $arrData['category_id']  = $category_id;
                                            $arrData['url']          = trim($creative_url);
                                            $arrData['admin_id']     = $set->userInfo['id'];
                                            $arrData['merchant_id']  = $merchant_id;
                                            $arrData['rdate']        = dbDate();
                                            $arrData['last_update']  = dbDate();
                                            $arrData['valid']        = $creative_status;
                                            $arrData['title']        = !empty($title) ? $title : $creative_name . '_' . $i;
                                            $exp                     = explode('.', $arrData['file']);
                                            $ext                     = strtolower($exp[count($exp) - 1]);
                                            $arrData['type']         = 'swf' == $ext ? 'flash' : 'image';
                                            $arrData['alt']          = (isset($_POST['alt1_' . $i]) ? $_POST['alt1_' . $i] : $creative_alt);
                                            
                                            list($arrData['width'], $arrData['height']) = getimagesize($arrData['file']);
											
                                            dbAdd($arrData, $appTable, array('scriptCode')); // merchants_creative.
                                        }
                                    }
                                    
				} elseif ($form_type == "2") {
					if (empty($_POST['name2_' . $i]) && 1 == $i) {
                                            $db['title'] = $_POST['creative_name'];
                                            $boolUseDefaultName = true;
					    
					} else {
                                            $db['title'] = $_POST['name2_' . $i];
					}
					
					if (empty($_POST['alt2_' . $i])) {
                                            $db['alt'] = $_POST['creative_alt'];
					} else {
                                            $db['alt'] = $_POST['alt2_' . $i];
					}
					
					if (isset($db['title']) && !empty($db['title'])) { 
                                            $db['type'] = "link";
					    $insert     = 1; 
					}
				
				} elseif ($form_type == "3") {
                                    $db['title']      = (!empty($_POST['name3_' . $i]) ? $_POST['name3_' . $i] : $creative_name . '_' . $i);
                                    $db['type']       = "widget";
                                    $db['height']     = $_POST['iframe3_height_' . $i];
                                    $db['width']      = $_POST['iframe3_width_' . $i];
                                    $db['iframe_url'] = $_POST['iframe3_url_' . $i];
                                    
                                    if ($db['title'] && $db['iframe_url'] != "http://" && $db['iframe_url']) {
                                            $insert = 1;
                                    }
					
				} elseif ($form_type == "4") {
                                    if (chkUpload('file4_' . $i)) {
                                        $db['title'] = $_POST['name4_' . $i];
										$randomFolder =mt_rand(10000000, 99999999);
										$folder = 'files/banners/tmp/' . $randomFolder ."/";
										 if (!is_dir('files/banners/tmp')) {
											 mkdir('files/banners/tmp');
										 }
										 if (!is_dir($folder)) {
											 mkdir($folder);
										 }
										 
                                        $db['file']  = UploadFile('file4_' . $i, '5120000', 'jpg,jpeg,swf,bmp,gif,png,ico,pdf,pps', '',$folder);
                                        
                                        if (empty($db['file'])) {
                                            if (!empty($errors)) {
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&errors=' . json_encode($errors));
                                            } else {
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id);
                                            }
                                        }
                                        
                                        $exp        = explode(".", $db['file']);
                                        $ext        = strtolower($exp[count($exp) - 1]);
                                        $db['type'] = "mobileleader";
                                        
                                        list($db['width'], $db['height']) = getimagesize($db['file']);
                                        $db['alt'] = (!empty($_POST['alt4_' . $i]) ? $_POST['alt4_' . $i] : $creative_alt);
                                        $insert    = 1;
                                    }
				    
				} elseif ($form_type == "5") {
					if (chkUpload('file5_' . $i)) {
                                            $db['title'] = $_POST['name5_' . $i];
											$randomFolder =mt_rand(10000000, 99999999);
										$folder = 'files/banners/tmp/' . $randomFolder ."/";
										 if (!is_dir('files/banners/tmp')) {
											 mkdir('files/banners/tmp');
										 }
										 if (!is_dir($folder)) {
											 mkdir($folder);
										 }
                                            $db['file']  = UploadFile('file5_' . $i, '5120000', 'jpg,jpeg,swf,bmp,gif,png,ico,pdf,pps', '',$folder);
                                            
                                            if (empty($db['file'])) {
                                                if (!empty($errors)) {
                                                    _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&errors=' . json_encode($errors));
                                                } else {
                                                    _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id);
                                                }
                                            }
                                            
                                            $exp        = explode(".", $db['file']);
                                            $ext        = strtolower($exp[count($exp) - 1]);
                                            $db['type'] = "mobilesplash";

                                            list($db['width'], $db['height']) = getimagesize($db['file']);
                                            $db['alt'] = (!empty($_POST['alt5_' . $i]) ? $_POST['alt5_' . $i] : $creative_alt);
                                            $insert    = 1;
					}
					
				} elseif ($form_type == "6") {
                                    $db['type']       = "mail";
                                     $db['title'] = (!empty($_POST['name6_' . $i]) ? $_POST['name6_' . $i] : $_POST['creative_name']); ;
									 
									 
									 $db['alt'] = (!empty($_POST['alt6_' . $i]) ? $_POST['alt6_' . $i] : $creative_alt);
									$inlinecss        = new instyle();
                                    $strConvertedHtml = $inlinecss->convert($_POST['scriptCode6']);
                                    $db['scriptCode'] = $strConvertedHtml;
                                    $insert           = 1;
				    
				} elseif ($form_type == "7") {
                                    $db['type'] = "content";
                                    $db['title'] = (!empty($_POST['name7_' . $i]) ? $_POST['name7_' . $i] : $_POST['creative_name']); ;
									 $db['alt'] = (!empty($_POST['alt7_' . $i]) ? $_POST['alt7_' . $i] : $creative_alt);
									$inlinecss        = new instyle();
                                    $strConvertedHtml = $inlinecss->convert($_POST['scriptCode7']);
                                    $db['scriptCode'] = $strConvertedHtml;
                                    $insert           = 1;
				}
				
                            if ($insert) {
                                dbAdd($db, $appTable, array('scriptCode'));
                            }
                            
                            // Stop the loop in case of default values usage.
                            if ($boolUseDefaultName) {
                                break;
                            }
			}
                        
			if (!empty($errors)) {
                            _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&errors=' . json_encode($errors));
			} else {
                            _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id);
			}
		}
	// break;
	
	
	
	case "trash":
		$pageTitle = lang('Creative Materials'). ' ' .lang('Recycle Bin');
					$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

		// $merchant_id = '1';
		$numqq = function_mysql_query("SELECT id FROM merchants",__FILE__);
		if (!$merchant_id) {
			$numww = mysql_fetch_assoc($numqq);
			
			if (mysql_num_rows($numqq) <= 1) { 
				_goto($set->SSLprefix.$set->basepage . '?act=trash&merchant_id=' . $numww['id']);
			}
			
			        $set->content .= '<script>var hash = "";</script>';
                    //$set->content  = '<script>var hash = window.location.hash ? "#tab_3" : "";</script>';
			$set->content .= '<div align="center"><b>' . lang('Choose merchant to manage his creative material') 
						  . ':</b><br /><br /><select onchange="location.href=\'' . $set->SSLprefix.$set->basepage . '?'.($isNew==1 ? 'new=1&' : '' ) .'act=trash&' . 'merchant_id=\'+this.value + hash;"><option value="">' 
						  . lang('Choose Merchant') . '</option>' . listMerchants() . '</select></div>';
                        
			theme();
			die();
			
		} else {
			$set->pageTitle .= ' ' . listMerchants($merchant_id, 1);
						
		}
		
          // die ('grgtr');      
		$qq = function_mysql_query("SELECT * FROM " . $appProm . " WHERE merchant_id='" . $merchant_id . "' ORDER BY title ASC",__FILE__);
		$distincPromotions = array();
		while ($ww = mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!in_array($ww['title'],$distincPromotions)) {
			$promotions_list .='<option id="'.$ww['id'].'">'.$ww['title'] . '</option>';
			$distincPromotions[] = $ww['title'];
			}
			
			
			$PromList .= '<tr ' . ($l % 2 ? 'class="trLine"' : '') . '>
						<td>' . $ww['id'] . '</td>
						<td align="left">' . $ww['title'] . '</td>
						<td align="center">' . dbDate($ww['rdate']) . '</td>
						<td align="center">' . ($ww['affiliate_id'] ? $ww['affiliate_id'] : lang('ALL')) . '</td>
						<td align="center"><a href="' . $set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&prom_id=' . $ww['id'] . '">' . lang('Edit') . '</a></td>
						<td align="center" id="promlng_' . $ww['id'] . '"><a onclick="ajax(\'' . $set->SSLprefix.$set->basepage . '?act=prom_valid&id=' . $ww['id'] . '\',\'promlng_' 
						. $ww['id'] . '\');" style="cursor: pointer;">' . xvPic($ww['valid']) . '</a></td>
					</tr>';
		}
		
		if ($prom_id) {
			$db = dbGet($prom_id, $appProm);
		}
		
		if ($set->AllowManagerEditrCreative==1)
		if (mysql_num_rows($numqq) > 1) { 
			$set->content .= '<div align="right" style="padding-bottom: 10px;"><b>' . lang('Switch Merchant') . ':</b> <select onchange="location.href=\'' 
						  . $set->SSLprefix.$set->basepage . '?act=trash&merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id,0,0,1) . '</select></div>';
		}
		
		
		
			
		if ($merchant) $where .= " AND merchant_id='".$merchant."'";
		if ($type) $where .= " AND type='".$type."'";
		if ($lang) $where .= " AND language_id='".$lang."'";
		if ($promotion) $where .= " AND promotion_id='".$promotion."'";
		if ($category_id) $where .= " AND category_id='".$category_id."'";
		if ($width) $where .= " AND width='".$width."'";
		if ($height) $where .= " AND height='".$height."'";
		
			
		
//		if ($categoryname) $where .= " AND categoryid='".$categoryid."'";
		if ($q) $where .= " AND (lower(title) LIKE '%".strtolower($q)."%' OR id='".$q."')";
		if ($creativedimenstion<>'')  {
		$spltA = explode("X",$creativedimenstion);
			$where .= " AND width='".$spltA[0]."' AND height='".$spltA[1]."'";
		}

		
		
		$typesArray = array();
		$isDownloadSession = false;
		if (isset($_GET['download']) && strlen($_GET['download'])>0) {
				$isDownloadSession = true;
		}
		
		$qry = "select distinct type as type from  ".$appTable." WHERE type not like 'coupon' and merchant_id in (".$merchant_id.") ";//.$where;
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$typesArray[]=$ww['type'];
			
		}
		$langsArray = array();
		$qry = "select distinct language_id as  language_id from  ".$appTable." WHERE merchant_id in (".$merchant_id.") ";//.$where;
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$langsArray[]=$ww['language_id'];
		}
		

		
		$getPos = $set->itemsLimit;
		$pgg=$_GET['pg'] * $getPos;
		if ($isDownloadSession) 
			$creativeQry  = "SELECT * FROM ".$appTable." WHERE valid=-1 and type not like 'coupon' and merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC";
		else
			$creativeQry  = "SELECT * FROM ".$appTable." WHERE valid = -1 and type not like 'coupon' and merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC LIMIT $pgg,$getPos";
		
		// die($creativeQry);
		$qq=function_mysql_query($creativeQry,__FILE__);
		$bottomNav = GetPages($appTable,"WHERE merchant_id='".$merchant_id."' ".$where,$pg,$getPos);
		$fileArrays = array();
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!empty($ww['file']))
			$fileArrays[] = $ww['file'];
			
			// $q = "SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE banner_id='".$ww['id']."' AND merchant_id='".$ww['merchant_id']."'";
			
			$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE  rdate> DATE_SUB(curdate(), INTERVAL 1 WEEK) and banner_id='".$ww['id']."' AND merchant_id='".$ww['merchant_id']."'",__FILE__));
			$allCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>
							<td><a href="'.$set->SSLprefix.$set->basepage.'?act=restore&merchant_id='.$ww['merchant_id'].'&id='.$ww['id'].'">'.lang('Restore').'</a></td>
							<td align="left">'.$ww['title'].'</td>
							<td align="center">'.($ww['type'] == "image" || $ww['type'] == "flash" || $ww['type'] == "mobilesplash" || $ww['type'] == "mobileleader" ? getFixedSizeBanner($ww['file'],80,80) : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>
							<td align="center">'.ucwords($ww['type']).'</td>
							<td align="center">'. ($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</td>
							<td align="center">'.listLangs($ww['language_id'],1).'</td>
							<td align="center">'.listPromotions($ww['promotion_id'],$merchant_id,1,"",1).'</td>
							<td align="center">'.listCategory($ww['category_id'],$category_id,1,1).'</td>
							
							
							
							<td align="center"><a href="'.$ww['url'].'" target="_blank" title="'.$ww['url'].'">'.$ww['url'].'</a></td>
							<td align="center">'.$ww['alt'].'</td>
							<td align="center">'.number_format($totalTraffic['totalViews'],0).'</td>
							<td align="center">'.number_format($totalTraffic['totalClicks'],0).'</td>
							<td align="center" id="bnr_'.$ww['id'].'">
								
							</td>
						</tr>';
			}
		// var_dump ($fileArrays);
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
		
          
		
		$comboq = "SELECT count(CONCAT(width, 'x', height)) as count, CONCAT(width, ' X ', height) as dim,`merchants_creative`.height ,`merchants_creative`.width FROM `merchants_creative` WHERE merchant_id = '".$merchant_id."' and  `merchants_creative`.height>0 and `merchants_creative`.width>0 and `merchants_creative`.valid =1
group by CONCAT(width, ' X ', height) order by height, width";

$combolist = '<select name="creativedimenstion" style="width: 140px;"><option value="">'.lang('Show All').'</option>';
$qqcombo=function_mysql_query($comboq,__FILE__);
		while ($wwcombo=mysql_fetch_assoc($qqcombo)) {
			$combolist.= '<option value="'.str_replace(' ','',$wwcombo['dim']) .'">'. $wwcombo['dim'] . '  (' . $wwcombo['count']. ')'.'</option>';
		}
$combolist.='</select>';

                
		
			$set->content .= '
						<br>
                                                
                                                <div class="normalTableTitle" style="cursor: pointer;" >'.lang('Trash').' ' . lang('Creatives List for').': <b>'.listMerchants($merchant_id,1).'</b></div>
                                                    <br>
						<div id="tab_41" ' .(!$isNew==1 ? '>' : 'style="display:none!important;">' );
                                                
						
						
							
						$set->content .= '
						<form method="get" id="formSearch">
						<input type="hidden" name="result" value="1" />
						<input type="hidden" name="act" value="trash" />
						<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>';
							
							if ($set->AllowManagerEditrCreative==1)
						if (mysql_num_rows($numqq) > 1) { 
			$set->content .= '<td  class="blueText" style="padding-bottom: 10px;">' . lang('Switch Merchant') . ':</td>';
						}
						
			$set->content .= '<td align="left" class="blueText">'.lang('Creative Type').':</td>
								<td align="left" class="blueText">'.lang('Category').':</td>
								<td align="left" class="blueText">'.lang('Language').':</td>
									<td style="width:20px";></td>
										<td align="left" class="blueText">'.lang('Width').':</td>
								<td align="left" class="blueText">'.lang('Height').':</td>
								<td align="left" width="110px" ><span class="blueText">'.lang('Choose Size').':</span><br><span style="font-size:10px">'.lang('Width'). ' X ' . lang('Height') . ' ( '.lang('Count').' )</span></td>
								<td style="width:20px";></td>
								<td align="left" class="blueText">'.lang('Promotion').':</td>
								
								<td align="left" class="blueText">'.lang('Creative Name / ID').':</td>
										
						
						</tr>
						<tr>
						';
						
						
						
						
						if (mysql_num_rows($numqq) > 1) {
									$set->content .= '	
											<td><select onchange="location.href=\''  . $set->SSLprefix.$set->basepage . '?merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id) . '</select></td>
										
									';
						}
		$set->content .= '
								<td align="left">
									<select name="type" style="width: 100px;">
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
									</td>
								<td align="left"><select name="category_id" style="width: 100px;"><option value="">'.lang('All').'</option>'.listCategory($category_id,$merchant_id).'</select></td>
								<td align="left"><select name="lang" style="width: 100px;"><option value="">'.lang('All').'</option>'.listLangs($lang,0,$langsArray).'</select></td>
								<td style="width:20px";></td>
								<td align="left"><input type="text" name="width" value="'.$width.'" style="width: 40px; text-align: center;" /></td>
								<td align="left"><input type="text" name="height" value="'.$height.'" style="width: 40px; text-align: center;" /></td>
								<td align="left" width="130">'.$combolist.'</td>
								<td style="width:20px";></td>
								<td align="left"><select name="promotion" style="width: 140px;"><option value="">'.lang('General').'</option>'.listPromotions($promotion,$merchant_id).'</select></td>
								
								<td align="left"><input type="text" name="q" value="'.$q.'" style="width: 120px;" /></td>
								<td style="width:20px";></td>
								<td><input type="submit" value="'.lang('Search').'" /></td><td>&nbsp;&nbsp;</td><td><input name="download" type="submit" value="'.lang('Download').'" /></td>
								
								
								
							</tr></table>
						</div>
							</form>
                                                  
						

						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td>'.lang('Creative Name').'</td>
								<td>'.lang('Preview').'</td>
								<td>'.lang('Type').'</td>
								<td>'.lang('Size (Width x Height)').'</td>
								<td>'.lang('Language').'</td>
								<td>'.lang('Promotion').'</td>
								<td>'.lang('Category').'</td>
								<td>'.lang('Landing URL').'</td>
								<td>'.lang('ALT').'</td>
								<td>'.lang('Impressions').'</td>
								<td>'.lang('Clicks').'</td>
								
							</tr></thead><tfoot>'.$allCreative.'</tfoot>
						</table><br />
                                                
					<div align="left" style="padding: 5px;">'.$bottomNav.'</div>';
					
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
		
		
	
	
	
		
	default:
		$pageTitle = lang('Creative Materials for');
					$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'manager/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';

		// $merchant_id = '1';
		$numqq = function_mysql_query("SELECT id FROM merchants where valid=1",__FILE__);
                
		if (!$merchant_id) {
			$numww = mysql_fetch_assoc($numqq);
			if( empty($numww) ){
				$set->content .= "<style>
				.noMerchantsDiv{
					    display: block;
						text-align: center;
						font-size: 24px;
						padding: 10px 0;
				}
				</style>";
				$set->content .="<div class='noMerchantsDiv'>".  lang("There are no active merchants in the system.") . "<a href='manager/index.php'>". lang("Click here") . "</a>" . lang(" to got to dashboard  ") ."</div>";
				theme();
				die();
			}
			if (mysql_num_rows($numqq) <= 1) { 
				_goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $numww['id']. ($isNew==1 ? "&new=1" : "" ));
			}
			
                        
                        $set->content .= '<script>var hash = "";</script>';
                        //$set->content  = '<script>var hash = window.location.hash ? "#tab_3" : "";</script>';
			$set->content .= '<div align="center"><b>' . lang('Choose merchant to manage his creative material') 
						  . ':</b><br /><br /><select onchange="location.href=\'' . $set->SSLprefix.$set->basepage . '?'.($isNew==1 ? 'new=1&': '') . 'merchant_id=\'+this.value + hash;"><option value="">' 
						  . lang('Choose Merchant') . '</option>' . listMerchants(0,0,0,1) . '</select></div>';
                        
			theme();
			die();
			
		} else {
			$set->pageTitle .= ' ' . listMerchants($merchant_id, 1);
					
		}
		
		$qq = function_mysql_query("SELECT * FROM " . $appProm . " WHERE merchant_id='" . $merchant_id . "' ORDER BY title ASC",__FILE__);
		$distincPromotions = array();
		while ($ww = mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!in_array($ww['title'],$distincPromotions)) {
			$promotions_list .='<option id="'.$ww['id'].'">'.$ww['title'] . '</option>';
			$distincPromotions[] = $ww['title'];
			}
			
			
			$PromList .= '<tr ' . ($l % 2 ? 'class="trLine"' : '') . '>
						<td>' . $ww['id'] . '</td>
						<td align="left">' . $ww['title'] . '</td>
						<td align="center">' . dbDate($ww['rdate']) . '</td>
						<td align="center">' . ($ww['affiliate_id'] ? $ww['affiliate_id'] : lang('ALL')) . '</td>
						<td align="center"><a href="' . $set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&prom_id=' . $ww['id'] . '">' . lang('Edit') . '</a></td>
						<td align="center" id="promlng_' . $ww['id'] . '"><a onclick="ajax(\'' . $set->SSLprefix.$set->basepage . '?act=prom_valid&id=' . $ww['id'] . '\',\'promlng_' 
						. $ww['id'] . '\');" style="cursor: pointer;">' . xvPic($ww['valid']) . '</a></td>
					</tr>';
		}
		
		if ($prom_id) {
			$db = dbGet($prom_id, $appProm);
		}
		
		if ($set->AllowManagerEditrCreative==1)
		if (mysql_num_rows($numqq) > 1) { 
			$set->content .= '<div align="right" style="padding-bottom: 10px;"><b>' . lang('Switch Merchant') . ':</b> <select onchange="location.href=\'' 
						  . $set->basepage . '?merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id,0,0,1) . '</select></div>';
		}
		
		$set->content .= getImageGrowerScript();

		// var_dump($_GET);
		// die();
			
		if ($merchant) $where .= " AND merchant_id='".$merchant."'";
		if ($type) $where .= " AND type='".$type."'";
		if ($lang) $where .= " AND language_id='".$lang."'";
		if ($promotion) $where .= " AND promotion_id='".$promotion."'";
		if ($category_id) $where .= " AND category_id='".$category_id."'";
		if ($width) $where .= " AND width='".$width."'";
		if ($height) $where .= " AND height='".$height."'";
		if ($valid==1) {
			$where .= " AND valid='".$valid."'";
		}
		elseif ($valid==0) {
			$where .= " AND valid>-1";
		}
//		if ($categoryname) $where .= " AND categoryid='".$categoryid."'";
		if ($q) $where .= " AND (lower(title) LIKE '%".strtolower($q)."%' OR id='".$q."')";
		if ($creativedimenstion<>'')  {
		$spltA = explode("X",$creativedimenstion);
			$where .= " AND width='".$spltA[0]."' AND height='".$spltA[1]."'";
		}

		
		
		$typesArray = array();
		$isDownloadSession = false;
		if (isset($_GET['download']) && strlen($_GET['download'])>0) {
				$isDownloadSession = true;
		}
		
		$qry = "select distinct type as type from  ".$appTable." WHERE type not like 'coupon' and merchant_id in (".$merchant_id.") ";//.$where;
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$typesArray[]= strtolower($ww['type']);
			
		}
		$langsArray = array();
		$qry = "select distinct language_id as  language_id from  ".$appTable." WHERE merchant_id in (".$merchant_id.") ";//.$where;
		$qq=function_mysql_query($qry,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$langsArray[]=$ww['language_id'];
		}
		

		
		$getPos = $set->itemsLimit;
		$pgg=$_GET['pg'] * $getPos;
		if ($isDownloadSession) 
			$creativeQry  = "SELECT * FROM ".$appTable." WHERE type not like 'coupon' and merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC";
		else
			$creativeQry  = "SELECT * FROM ".$appTable." WHERE type not like 'coupon' and merchant_id='".$merchant_id."' ".$where." ORDER BY id DESC LIMIT $pgg,$getPos";
		
		
		$qq=function_mysql_query($creativeQry,__FILE__);
		$bottomNav = GetPages($appTable,"WHERE merchant_id='".$merchant_id."' ".$where,$pg,$getPos);
		$fileArrays = array();
		 
		 	
				
				//$qrys = $qrys .'merchant_id=' . (isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "1");
				$qrys = $qrys .'&type=' .( isset($_GET['type']) ? $_GET['type'] : "");
				$qrys = $qrys .'&category_id=' .(isset($_GET['category_id']) ? $_GET['category_id'] : "");
				$qrys = $qrys .'&lang=' .( isset($_GET['lang']) ? $_GET['lang'] : "");
				$qrys = $qrys .'&creativedimenstion=' .( isset($_GET['creativedimenstion']) ? $_GET['creativedimenstion'] : "");
				$qrys = $qrys .'&promotion=' .( isset($_GET['promotion']) ? $_GET['promotion'] : "");
				$qrys = $qrys .'&valid=' .( isset($_GET['valid']) ? $_GET['valid'] : 1);
				$qrys = $qrys .'&q=' .( isset($_GET['q']) ? $_GET['q'] : "");
				// die ($qrys);
		 				
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			
			if (!empty($ww['file']))
			$fileArrays[] = $ww['file'];
			
			
			$totalTraffic = mysql_fetch_assoc(function_mysql_query("SELECT SUM(views) AS totalViews, SUM(clicks) AS totalClicks FROM traffic WHERE  rdate> DATE_SUB(curdate(), INTERVAL 1 WEEK) and banner_id='".$ww['id']."' AND merchant_id='".$ww['merchant_id']."'",__FILE__));
			$allCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.$ww['id'].'</td>
							<td><a href="'.$set->webAddress.ltrim($set->basepage, '/').'?act=edit_banner&id='.$ww['id'].'" class="inline">'.lang('Edit').'</a>'.($set->AllowManagerEditrCreative==1 ? '&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'.$set->SSLprefix.$set->basepage.'?act=deletecreative&merchant_id='.$ww['merchant_id'].'&id='.$ww['id'].$qrys.'">'.lang('Delete').'</a>':'').'</td>
							<td align="left">'.$ww['title'].'</td>'
							.(strpos($ww['file'],"/tmp")?
							'<td align="center" class="tooltip">'.($ww['type'] == "image" || $ww['type'] == "flash" || $ww['type'] == "mobilesplash" || $ww['type'] == "mobileleader" ?  '<img src="images/wheel.gif" width=28 height =28><span class="tooltiptext" style="padding-bottom:15px">'. lang("System is checking for virus. Please refresh in a minute.") .'</span>' : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>':
							'<td align="center" class="img-wrap">'.($ww['type'] == "image" || $ww['type'] == "flash" || $ww['type'] == "mobilesplash" || $ww['type'] == "mobileleader" ? getFixedSizeBanner($ww['file'],50,50) : '<a href="javascript:void(0);">'.$ww['title'].'</a>').'</td>') .
							
							'<td align="center">'.ucwords($ww['type']).'</td>
							<td align="center" class="dimantion-wrap">'. ($ww['type'] == "link" ? '' : $ww['width'].'x'.$ww['height']).'</td>
							<td align="center">'.listLangs($ww['language_id'],1).'</td>
							<td align="center">'.listPromotions($ww['promotion_id'],$merchant_id,1,"",1).'</td>
							<td align="center">'.listCategory($ww['category_id'],$category_id,1,1).'</td>
							
							
							
							<td align="center"><a href="'.$ww['url'].'" target="_blank" title="'.$ww['url'].'">'.$ww['url'].'</a></td>
							<td align="center">'.$ww['alt'].'</td>
							<td align="center">'.number_format($totalTraffic['totalViews'],0).'</td>
							<td align="center">'.number_format($totalTraffic['totalClicks'],0).'</td>
							<td align="center" id="bnr_'.$ww['id'].'">
								'.($set->AllowManagerEditrCreative==1 ? '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'bnr_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a>' : xvPic($ww['valid']) ).'
								'.($delete ? '<a href="'.$set->SSLprefix.$set->basepage.'?act=del_banner&id='.$ww['id'].'">.</a>' : '').'
							</td>
						</tr>';
			}
		// var_dump ($fileArrays);
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
		
                
		for ($i = 1; $i <= $max_upload; $i++) {  
                        // '.fileField('file1_'.$i,'').'    '.fileField('file4_'.$i,'').'     '.fileField('file5_'.$i,'').'  
			$l++;
			$listCreative .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$i.'</td>
								<td style="text-align: left;"><input type="text" name="name1_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td align="center"><input type="file" multiple="true" name="file1_' . $i . '[]" /></td>
								<td><input type="text" name="alt1_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
							</tr>';
			if ($i <= "5") $listCreative2 .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
								<td>'.$i.'</td>
								<td style="text-align: left;"><input type="text" name="name2_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td><input type="text" name="alt2_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
							</tr>';
			if ($i <= "1") $listCreative3 = '<tr class="trLine">
								<td style="text-align: left;"><input type="text" name="name3_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td><input type="text" name="iframe3_url_'.$i.'" value="http://" id="fieldClear" style="width: 280px;" /></td>
								<td>Width x Height<br /><input type="text" name="iframe3_width_'.$i.'" value="" id="fieldClear" style="width: 30px; text-align: center;" /> x <input type="text" name="iframe3_height_'.$i.'" value="" id="fieldClear" style="width: 30px; text-align: center;" /></td>
							</tr>';
			if ($i <= "1") $listCreative4 = '<tr class="trLine">
								<td style="text-align: left;"><input type="text" name="name4_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td align="center"><input type="file" name="file4_' . $i . '" /></td>
								<td><input type="text" name="alt4_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
							</tr>';
			if ($i <= "1") $listCreative5 = '<tr class="trLine">
								<td style="text-align: left;"><input type="text" name="name5_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
								<td align="center"><input type="file" name="file5_' . $i . '" /></td>
								<td><input type="text" name="alt5_'.$i.'" value="" id="fieldClear" style="width: 280px;" /></td>
							</tr>';
                }
		
		$comboq = "SELECT count(CONCAT(width, 'x', height)) as count, CONCAT(width, ' X ', height) as dim,`merchants_creative`.height ,`merchants_creative`.width FROM `merchants_creative` WHERE merchant_id = '".$merchant_id."' and  `merchants_creative`.height>0 and `merchants_creative`.width>0 and `merchants_creative`.valid =1
group by CONCAT(width, ' X ', height) order by height, width";

$combolist = '<select name="creativedimenstion" style="width: 140px;"><option value="">'.lang('Show All').'</option>';
$qqcombo=function_mysql_query($comboq,__FILE__);
		while ($wwcombo=mysql_fetch_assoc($qqcombo)) {
			$combolist.= '<option value="'.str_replace(' ','',$wwcombo['dim']) .'">'. $wwcombo['dim'] . '  (' . $wwcombo['count']. ')'.'</option>';
		}
$combolist.='</select>';

                
		if ($set->AllowManagerEditrCreative==1)
		$set->content .= '<form method="post" enctype="multipart/form-data">
						<input type="hidden" name="act" value="add" />
						<input type="hidden" name="form_type" value="1" id="form_type" />
						<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />
                                                    
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Add New Crative Material for').': <b>'.listMerchants($merchant_id,1,0,1).'</b></div>
						<div id="tab_3" ' .($isNew==1 ? 'style="display:block;">' : 'style="display:none;">' ). 
						'<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>
								<td align="left" width="100" class="blueText">'.lang('Language').':</td>
								<td align="left"><select name="creative_lang" style="width: 292px;">'.listLangs().'</select></td>
								<td width="80"></td>
								<td align="left" class="blueText">'.lang('Promotion').':</td>
								<td align="left"><select name="creative_promotion" style="width: 100px;"><option value="">'.lang('General').'</option>'.listPromotions(0,$merchant_id,"","",1).'</select></td>
								
								<td width="80"></td>
								<td align="left" class="blueText">'.lang('Default Creative Name').':</td>
								<td align="left"><input type="text" name="creative_name" value="" style="width: 280px;" /></td>
								<td width="80"></td>
								<td align="left" class="blueText">'.lang('Category').':</td>
								<td align="left"><select name="category_id" style="width: 120px;"><option value="">'.lang('All').'</option>'.listCategory($category_id,$merchant_id).'</select></td>
							</tr><tr>
								<td align="left" class="blueText">'.lang('Landing URL').':</td>
								<td align="left">
								<input type="text" id="creative_url" name="creative_url" value="http'.$set->SSLswitch .'://" style="width: 280px;" /'.
								(isset($errors['creative_url']) ? '<span style="color:RED">*</span>' : '').'</td>
								
								<td></td>
								<td align="left" class="blueText">'.lang('Status').':</td>
								<td align="left">
									<select name="creative_status" style="width: 100px;">
										<option value="1">'.lang('Active').'</option>
										<option value="0">'.lang('Inactive').'</option>
									</select>
								</td>
								<td></td>
								<td align="left" class="blueText">'.lang('ALT').':</td>
								<td align="left"><input type="text" name="creative_alt" value="" style="width: 280px;" /></td>
							</tr></table>
						</div>
						<div style="padding-bottom:10px" class="material_type_label blueText">'. lang('Choose Creative Material Type:') .'</div>
						<div class="tabs banner_type_tabs">
							<div class="tab" rel="1" name="banner_image_flash">'.lang('Banner Image / Flash').'</div>
							<div class="tab" rel="2" name="text_link">'.lang('Text Link').'</div>
							'.($set->creative_iframe ? '<div class="tab" rel="3" name="iframe_widget">'.lang('IFrame (Widget)').'</div>' : '').'
							'.($set->creative_mobile_leader ? '<div class="tab" rel="4" name="mobile_leader_board">'.lang('Mobile LeaderBoard').'</div>' : '').'
							'.($set->creative_mobile_splash ? '<div class="tab" rel="5" name="mobile_splash">'.lang('Mobile Splash').'</div>' : '').'
							'.($set->creative_email ? '<div class="tab" rel="6">'.lang('E-mail').'</div>' : '').'
						<div class="tab" rel="7">'.lang('Content').'</div>
						</div>
						<div class="tab_open" id="tab_1">
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td width="50">#</td>
									<td style="text-align: left;">'.lang('Banner Name').'</td>
									<td align="center">'.lang('Upload File').'</td>
									<td align="center">'.lang('ALT').'</td>
								</tr></thead><tfoot>'.$listCreative.'</tfoot>
							</table>
						</div>
						<div class="tab_open" id="tab_2">
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td width="50">#</td>
									<td width="200" style="text-align: left;">'.lang('Link Name').'</td>
									<td align="center">'.lang('ALT').'</td>
								</tr></thead><tfoot>'.$listCreative2.'</tfoot>
							</table>
						</div>
						'.($set->creative_iframe ? '<div class="tab_open" id="tab_3">
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td width="200" style="text-align: left;">'.lang('IFrame / Widget Name').'</td>
									<td align="center">'.lang('Widget URL').'</td>
									<td align="center">'.lang('Widget Size').'</td>
								</tr></thead><tfoot>'.$listCreative3.'</tfoot>
							</table>
						</div>' : '').'
						'.($set->creative_mobile_leader ? '<div class="tab_open" id="tab_4">
							<div class="comment">
								'.lang('Mobile Leaderboard Banner should be on').' <b>320x50</b>
							</div>
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td style="text-align: left;">'.lang('Banner Name').'</td>
									<td align="center">'.lang('Upload File').'</td>
									<td align="center">'.lang('ALT').'</td>
								</tr></thead><tfoot>'.$listCreative4.'</tfoot>
							</table>
						</div>' : '').'
						'.($set->creative_mobile_splash ? '<div class="tab_open" id="tab_5">
							<div class="comment">
								'.lang('Mobile Splash Banner should be').' <b>350x350</b>
							</div>
							<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
								<thead>
								<tr>
									<td style="text-align: left;">'.lang('Banner Name').'</td>
									<td align="center">'.lang('Upload File').'</td>
									<td align="center">'.lang('ALT').'</td>
								</tr></thead><tfoot>'.$listCreative5.'</tfoot>
							</table>
						</div>' : '').'
						'.($set->creative_email ? '<div class="tab_open" id="tab_6">
							<textarea name="scriptCode6" rows="20" placeholder="'.lang('Paste the HTML code here').'..." style="width: 99%;"></textarea>
							<p></p>
							<span>'.lang('Create tracking url by adding') . ' <u>'.'http'.$set->SSLswitch.'://'.$_SERVER[HTTP_HOST] . '/click.php?ctag={ctag}&subid={subid}</u> ' . lang('in the html code').'.</span>
							<p></p>
						</div>' : '').'
						<div class="tab_open" id="tab_7">
							<textarea name="scriptCode7" rows="20" placeholder="'.lang('Paste the HTML content code here').'..." style="width: 99%;"></textarea>
							<p></p>
							<span>'.lang('Create tracking url by adding') . ' <u>'.'http'.$set->SSLswitch.'://'.$_SERVER[HTTP_HOST] . '/click.php?ctag={ctag}&subid={subid}</u> ' . lang('in the html code').'.</span>
							<p></p>
						</div>
						<script type="text/javascript">
							$("div.tab").on("click", function() {
								$(this).parent().find("div.tab.selected").attr("class", "tab");
								$(this).attr("class", "tab selected");
								var getID = $(this).attr("rel");
								$("div.tab_open").slideUp();
								$("div#tab_"+getID).slideDown();
								$("#form_type").val(getID);
								});
						$(document).ready(function(){
							$("#creative_url").blur(function() {
						  var input = $(this);
						  var val = input.val();
						  if (val && !val.match(/^http([s]?):\/\/.*/)) {
							input.val("http://" + val);
						  }
						});
						});
						
						</script>
						<div style="clear:both;padding: 10px;"><input id="submit_creative_material" type="submit" value="'.lang('Save').'"/></div>
						</div>
						</form>
						
						<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
						<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
						<script>
						
					
							/** 
							 * @return bool
							 */
							function creativeDefaultValuesExist() {
								return $("[name=creative_name]").val().trim().length != 0;
							}
							
							/**
							 * @return bool
							 */
							function setDefaultsCreative() {
								if (!$("[name=creative_url]").val()) {
									alert("' . lang('Please enter the Landing URL') . '");
									return false;
								} else if ("http://" == $("[name=creative_url]").val().trim()) {
									//alert("' . lang('Please enter the Landing URL') . '");
									$.prompt("'.lang('Please enter the Landing URL').'", {
										top:200,
										title: "'.lang('Creative Material').'",
										buttons: { "'.lang('OK').'": true}
									});
									return false;
								}
								
								var strSelectedTabName = $(".selected").text();
								if(strSelectedTabName == ""){
									$(".material_type_label").css("color","red");
									$.prompt("'.lang('Please select creative material type.').'", {
										top:200,
										title: "'.lang('Creative Material').'",
										buttons: { "'.lang('OK').'": true}
									});
									return false;
								}
								if ("' . lang('Banner Image / Flash') . '" == strSelectedTabName) {
									/**
								     * "Banner Image / Flash" validation.
								     */
									//if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
												if("" == $("[name=\'file1_" + i + "[]\']").val()){
														isValid = false;
												}
												else{
														isValid = true;
												}
												break;
												/* if (0 != $("[name=name1_" + i + "]").val().trim().length) { 
														isValid = true;
													break;
												} */
										}
										
										if (!isValid) {
												$.prompt("'.lang('New creatives type banner are requierd uploading images.<br/> Please click \"choose files\" or change creative type').'", {
													top:200,
													title: "'.lang('Creative Material').'",
													buttons: { "'.lang('OK').'": true}
												});
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									//}
								}
								else if ("' . lang('Text Link') . '" == strSelectedTabName) {
									/**
								     * "Text link" validation.
								     */
									if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
											if (0 != $("[name=name2_" + i + "]").val().trim().length) { 
												isValid = true;
												break;
											}
										}
										
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									}
														
								}
								else if ("' . lang('IFrame (Widget)') . '" == strSelectedTabName) {
									/**
								     * "IFrame (Widget)" validation.
								     */
									if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
											if (0 != $("[name=name3_" + i + "]").val().trim().length) { 
												isValid = true;
												break;
											}
										}
										
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									}
											
								}
								else if ("' . lang('Mobile LeaderBoard') . '" == strSelectedTabName) {
									/**
								     * "Mobile LeaderBoard" validation.
								     */
									if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
											if (0 != $("[name=name4_" + i + "]").val().trim().length) { 
												isValid = true;
												break;
											}
										}
										
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									} 
								}
								else if ("' . lang('Mobile Splash') . '" == strSelectedTabName) {
									/**
								     * "Mobile Splash" validation.
								     */
									if (!creativeDefaultValuesExist()) {
										var isValid = false;
										
										for (var i = 1; i <= 5; i++) {
											if (0 != $("[name=name5_" + i + "]").val().trim().length) { 
												isValid = true;
												break;
											}
										}
										
										if (!isValid) {
											if (0 == $("[name=creative_name]").val().trim().length) {
												$("[name=creative_name]").css("border-color", "Red");
											} else {
												$("[name=creative_name]").css("border-color", "#CECECE");
											}
											return false;
										}
									} 
								}
							}
							
							$("#submit_creative_material").click(function() {
								return setDefaultsCreative(); 
								
							});
						</script>
						<br>
                                                
                                                <div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_41\').slideToggle(\'fast\');">'.lang('Creatives List for').': <b>'.listMerchants($merchant_id,1,0,1).': </b>'.listMerchants($merchant_id,1).'</b></div>
                                                    <br>
						<div id="tab_41" ' .(!$isNew==1 ? '>' : 'style="display:none!important;">' );
                                                
						
						
							
						$set->content .= '
						<form method="get" id="formSearch">
						<input type="hidden" name="result" value="1" />
						<input type="hidden" name="merchant_id" value="'.$merchant_id.'" />
						<div align="left" style="padding-top: 10px; padding-bottom: 10px;">
							<table><tr>';
							
							if ($set->AllowManagerEditrCreative==1)
						if (mysql_num_rows($numqq) > 1) {
			$set->content .= '<td  class="blueText" style="padding-bottom: 10px;">' . lang('Switch Merchant') . ':</td>';
						}
						
			$set->content .= '<td align="left" class="blueText">'.lang('Creative Type').':</td>
								<td align="left" class="blueText">'.lang('Category').':</td>
								<td align="left" class="blueText">'.lang('Language').':</td>
									<td style="width:20px";></td>
										<td align="left" class="blueText">'.lang('Width').':</td>
								<td align="left" class="blueText">'.lang('Height').':</td>
								<td align="left" width="110px" ><span class="blueText">'.lang('Choose Size').':</span><br><span style="font-size:10px">'.lang('Width'). ' X ' . lang('Height') . ' ( '.lang('Count').' )</span></td>
								<td style="width:20px";></td>
								<td align="left" class="blueText">'.lang('Promotion').':</td>
								<td align="left" class="blueText">'.lang('Available').':</td>
								<td align="left" class="blueText">'.lang('Creative Name / ID').':</td>
										
						
						</tr>
						<tr>
						';
						
						
						
						
						if (mysql_num_rows($numqq) > 1) {
									$set->content .= '	
											<td><select onchange="location.href=\''  . $set->SSLprefix.$set->basepage . '?merchant_id=\'+this.value;"><option value="">'.lang('Please Choose Merchant').'</option>' . listMerchants($merchant_id) . '</select></td>
										
									';
						}
		$set->content .= '
								<td align="left">
									<select name="type" style="width: 100px;">
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
									</td>
								<td align="left"><select name="category_id" style="width: 100px;"><option value="">'.lang('All').'</option>'.listCategory($category_id,$merchant_id).'</select></td>
								<td align="left"><select name="lang" style="width: 100px;"><option value="">'.lang('All').'</option>'.listLangs($lang,0,$langsArray).'</select></td>
								<td style="width:20px";></td>
								<td align="left"><input type="text" name="width" value="'.$width.'" style="width: 40px; text-align: center;" /></td>
								<td align="left"><input type="text" name="height" value="'.$height.'" style="width: 40px; text-align: center;" /></td>
								<td align="left" width="130">'.$combolist.'</td>
								<td style="width:20px";></td>
								<td align="left"><select name="promotion" style="width: 140px;"><option value="">'.lang('General').'</option>'.listPromotions($promotion,$merchant_id).'</select></td>
								<td align="left"><select name="valid" style="width: 110px;"><option '. ($valid==0 ? 'selected' :'') .'value="0">'.lang('Show All').'</option><option '. ($valid==1 ? 'selected' :'') .' value="1">'.lang('Active Only').'</option></select></td>
								<td align="left"><input type="text" name="q" value="'.$q.'" style="width: 120px;" /></td>
								<td style="width:20px";></td>
								<td><input type="submit" value="'.lang('Search').'" /></td><td>&nbsp;&nbsp;</td><td><input name="download" type="submit" value="'.lang('Download').'" /></td>
								
								
								
							</tr></table>
						</div>
							</form>
                                                  
						

						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td>'.lang('Creative Name').'</td>
								<td>'.lang('Preview').'</td>
								<td>'.lang('Type').'</td>
								<td>'.lang('Size (Width x Height)').'</td>
								<td>'.lang('Language').'</td>
								<td>'.lang('Promotion').'</td>
								<td>'.lang('Category').'</td>
								<td>'.lang('Landing URL').'</td>
								<td>'.lang('ALT').'</td>
								<td>'.lang('Impressions').' *</td>
								<td>'.lang('Clicks').' *</td>
								<td>'.lang('Available').'</td>
							</tr></thead><tfoot>'.$allCreative.'</tfoot>
						</table><br />* '. lang('Last Week Stats').'<br />
                                                
					<div align="left" style="padding: 5px;">'.$bottomNav.'</div>';
					
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
	$aff = isset($_POST['db']['affiliate_id'])? $_POST['db']['affiliate_id'] : "";
	if ($title=='')
		_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
	
	$groupid = isset($_POST['group_id']) && $_POST['group_id']>-1 ? $_POST['group_id'] : -1;
	$autoApproveAll = isset($_POST['autoApproveAll']) && 'on'==$_POST['autoApproveAll'] ? 1 : 0;
	if (empty($aff)) {
		
		if ($groupid > -1 ) {
			$qry = "select id from affiliates where valid = 1 and group_id = " . $group_id;
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
				}
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
		
	$affiliate_ids = explode('|',$aff);
	foreach ($affiliate_ids as $aff_id) {
	// var_dump($aff_id);
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
			_goto($set->SSLprefix.$set->basepage.'?merchant_id='.$db['merchant_id']);
	}
	
	
	/* ------------------------------------ [ Promotions ] ------------------------------------ */
	
	case "showMail":
		$db = dbGet($id, $appTable);
		// var_dump($db);
			// die();
			
			
		if ($db['type'] != "mail") die("ERROR");
		echo str_replace("{ctag}", $ctag,  $db['scriptCode']);
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