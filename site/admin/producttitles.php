<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'producttitles';

switch ($act) {
	case "titleDel":
		function_mysql_query("DELETE FROM producttitles WHERE id='".$id."'",__FILE__);
		_goto($set->SSLprefix.$set->basepage.'?act=title&type='.$type);
		break;

	case "titleSave":
		for ($i=0;$i<=count($ids); $i++) {
		//echo "UPDATE producttitles SET source='".$Source[$i]."',Casino='".$Casino[$i]."',Sports='".$Sports[$i]."' WHERE id='".$ids[$i]."'<br>";
		$qry = "UPDATE producttitles SET Casino='".$Casino[$i]."',SportsBetting='".$Sports[$i]."',BinaryOption='".$BinaryOption[$i]."',Forex='".$Forex[$i]."',Download='".$Download[$i]."',Gaming='".$Gaming[$i]."',Mobile='".$Mobile[$i]."',Ecommerce='".$Ecommerce[$i]."',Dating='".$Dating[$i]."',Rummy='".$Rummy[$i]."',Bingo='".$Bingo[$i]."' WHERE id='".$ids[$i]."'";
		//die($qry);
		function_mysql_query($qry,__FILE__);
		}
		_goto($set->SSLprefix.$set->basepage.'?act=title&type='.$type);
		break;

	default:
		$set->pageTitle = lang('Products Titles');
		$qq=function_mysql_query("SELECT * FROM producttitles ".($type ? "WHERE Casino = ''" : "")."ORDER BY id DESC",__FILE__);
		$l=0;
		while ($ww=mysql_fetch_assoc($qq)) {
			if ($l % 2) $backColor = 'F4F4F4'; else $backColor = 'FFFFFF'; $l++;
			$titleList .= '<tr style="background: #'.(!$ww['Casino'] ? 'F4FD00' : $backColor).';">
					<td align="center"><input type="hidden" name="ids[]" value="'.$ww['id'].'" />'.$ww['id'].'</td>
					<td align="left">'.$ww['source'].'</td>
					<td align="center"><input type="text" name="Casino[]" value="'.$ww['Casino'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Sports[]" value="'.$ww['Sports'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="BinaryOption[]" value="'.$ww['BinaryOption'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Forex[]" value="'.$ww['Forex'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Download[]" value="'.$ww['Download'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Gaming[]" value="'.$ww['Gaming'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Mobile[]" value="'.$ww['Mobile'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Ecommerce[]" value="'.$ww['Ecommerce'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Dating[]" value="'.$ww['Dating'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Rummy[]" value="'.$ww['Rummy'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><input type="text" name="Bingo[]" value="'.$ww['Bingo'].'" dir="ltr" style="width: 120px;" /></td>
					<td align="center"><a onclick="return confirmation(\''.lang("Are you sure?").'\',\''.$set->basepage.'?act=titleDel&id='.$ww[id].'&$type='.$type.'\');" style="cursor: pointer;">'.lang('Delete').'</a></td>
				</tr>';
			}
		$set->content .= '
			<form action="'.$set->SSLprefix.$set->basepage.'" method="post">
			<input type="hidden" name="act" value="titleSave" />
			<input type="hidden" name="type" value="'.$type.'" />
			<table><tr>
				<td><a href="'.$set->SSLprefix.$set->basepage.'"><div class="btn">'.lang('All Titles').'</div></a></td>
				<td><a href="'.$set->SSLprefix.$set->basepage.'?act=title&type=none"><div class="btn">'.lang('None Titles').'</div></a></td>
			</tr></table>
			<div class="normalTableTitle">'.lang('Products Titles').'</div>
			<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
				<thead>
				<tr>
					<td style="font-weight: bold;">'.lang('ID').'</td>
					<td style="text-align: left; font-weight: bold;">'.lang('Source').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Casino').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Sports').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Binary Option').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Forex').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Download').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Gaming').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Mobile').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Ecommerce').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Dating').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Rummy').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Bingo').'</td>
				</tr></thead><tfoot><tr>
				</tr>
			'.$titleList.'
				<tr>
					<td colspan="4" align="center" height="40"><input type="submit" value="'.lang('Save Changes').'" class="saveButton" /></td>
				</tr>
			</tfoot>
			</table>
			</form>
			';
		theme();
		break;
	###################################### [ Product Titles ] ######################################
	
	###################################### [ APPLICATION ] ######################################
	case "apps":
		adminHeader(1); perfTree($act);
		$set->pageTitle = lang('Applications');
		$qq=function_mysql_query("SELECT * FROM apps",__FILE__);
		$l=0;
		while ($ww=mysql_fetch_assoc($qq)) {
			if ($l % 2) $backColor = 'F4F4F4'; else $backColor = 'FFFFFF'; $l++;
			$titleList .= '<tr style="background: #'.$backColor.';">
					<td align="center">'.$ww[id].'</td>
					<td align="center"><input type="checkbox" name="active_'.$ww[id].'" '.($ww[active] ? 'checked' : '').' /></td>
					<td align="center"><input type="text" name="title_'.$ww[id].'" value="'.$ww[title].'" dir="ltr" /></td>
					<td align="center"><input type="text" name="file_'.$ww[id].'" value="'.$ww[file].'" dir="ltr" /></td>
					<td align="center"><input type="text" name="tmb_'.$ww[id].'" value="'.$ww[tmb].'" dir="ltr" /></td>
				</tr>';
			}
		$set->content .= '
			<table width="100%" border="0" cellspacing="0">
				<form method="post">
				<input type="hidden" name="act" value="appsSave">
				<tr class="tableTitle">
					<td align="center" style="font-weight: bold;">#</td>
					<td align="center" style="font-weight: bold;"></td>
					<td align="center" style="font-weight: bold;">'.lang('Title').'</td>
					<td align="center" style="font-weight: bold;">'.lang('File').'</td>
					<td align="center" style="font-weight: bold;">'.lang('Thumbnail').'</td>
				</tr>
			'.$titleList.'
				<tr style="background: #'.$backColor.';">
					<td align="center"></td>
					<td align="center"><input type="checkbox" name="active_0" /></td>
					<td align="center"><input type="text" name="title_0" dir="ltr" /></td>
					<td align="center"><input type="text" name="file_0" dir="ltr" /></td>
					<td align="center"><input type="text" name="tmb_0" dir="ltr" /></td>
				</tr><tr>
					<td colspan="5" align="center" height="40"><input type="submit" value="'.lang('Save Changes').'" class="saveButton" /></td>
				</tr>
				</form>
			</table>';
		theme();
		break;

	case "appsSave":
		adminHeader(1);
		
		$qq=function_mysql_query("SELECT * FROM apps",__FILE__);
		while($ww=mysql_fetch_assoc($qq)) {
			$active = '';
			$title='title_'.$ww[id];
			$file='file_'.$ww[id];
			$tmb='tmb_'.$ww[id];
			$active='active_'.$ww[id];
			if ($$active) $active = '1'; else $active = '0';

			if ($$title AND $$file) {
				if (!$$tmb) $$tmb = 'img/apps/app.png';
				$chk=mysql_num_rows(function_mysql_query("SELECT id FROM apps WHERE id='".$ww[id]."'",__FILE__));
				if ($chk) function_mysql_query("UPDATE apps SET title='".LatinReplace($$title)."',file='".$$file."',tmb='".$$tmb."',active='".$active."' WHERE id='".$ww[id]."'",__FILE__);
				}
			}
		
		if ($title_0 AND $file_0) { // Add new application
			if ($active_0) $active_0 = '1'; else $active_0 = '0';
			if (!$tmb_0) $tmb_0 = 'img/apps/app.png';
			function_mysql_query("INSERT INTO apps VALUES (NULL, '".LatinReplace($title_0)."', '".$file_0."','".$tmb_0."','".$active_0."')",__FILE__); //or die(mysql_error());
			}
		
		_goto($set->SSLprefix.$set->basepage.'?act=apps');
		break;
	###################################### [ APPLICATION ] ######################################
	
	###################################### [ Performence ] ######################################
	
	case "savePerf":
		adminHeader();
		if (isAdmin(2) OR isAdmin(1)) {
			if ($mailCopy) $mailCopy = '1'; else $mailCopy = '0';
			if ($set->langHEB) UpdateUnit("settings","webTitleHEB='".LatinReplace($webTitleHEB)."'","id='1'");
			if ($set->Source) UpdateUnit("settings","webTitleENG='".LatinReplace($webTitleENG)."'","id='1'");
			UpdateUnit("settings","webDesHEB='".LatinReplace($webDesHEB)."'","id='1'");
			UpdateUnit("settings","webDesENG='".LatinReplace($webDesENG)."'","id='1'");
			UpdateUnit("settings","webKeyHEB='".LatinReplace($webKeyHEB)."'","id='1'");
			UpdateUnit("settings","webKeyENG='".LatinReplace($webKeyENG)."'","id='1'");
			UpdateUnit("settings","webMail='".$webMail."'","id='1'");
			UpdateUnit("settings","mailCopy='".$mailCopy."'","id='1'");
			UpdateUnit("settings","itemsPerPage='".$itemsPerPage."'","id='1'");
			UpdateUnit("settings","headerMail='".$db[headerMail]."'","id='1'");
			UpdateUnit("settings","footerMail='".$db[footerMail]."'","id='1'");
			}
		if ($username) UpdateUnit("auth","username='".$username."'","id='".$set->adminuid."'");
		if ($password) {
			UpdateUnit("auth","password='".encode($password)."'","id='".$set->adminuid."'");
			setcookie($set->setpassadmin,encode($password),time()+60*60*24*30);
			}
		_goto($set->SSLprefix.$set->basepage.'?act=perf');
		break;
	
	case "perf":
		adminHeader(); perfTree($act);
		$set->pageTitle = lang('Website Settings');
		$usr = mysql_fetch_assoc(function_mysql_query("SELECT * FROM auth WHERE id='".$set->adminuid."'",__FILE__));
		$set->content .= '<form method="post"><input type="hidden" name="act" value="savePerf" /><table>';
		$set->content .= '<tr><td>'.lang('Username').':</td><td><input type="text" name="username" value="'.$usr[username].'"></td></tr>';
		$set->content .= '<tr><td>'.lang('Password').':</td><td><input type="text" name="password" value="'.decode($usr[password]).'"></td></tr>';
		if (isAdmin(2) OR isAdmin(1)) {
			if ($set->langHEB) $set->content .= '<tr><td>'.lang('Website title').' '.lang('in hebrew').' ('.lang('Home Page').'):</td><td><input type="text" name="webTitleHEB" value="'.$set->webTitleHEB.'" dir="rtl" /></td></tr>';
			if ($set->Source) $set->content .= '<tr><td>'.lang('Website title').' '.lang('in english').' ('.lang('Home Page').'):</td><td><input type="text" name="webTitleENG" value="'.$set->webTitleENG.'" dir="ltr" /></td></tr>';
			if ($set->langHEB) $set->content .= '<tr><td>'.lang('Website description').' '.lang('in Hebrew').' ('.lang('Home Page').'):</td><td><input type="text" name="webDesHEB" value="'.$set->webDesHEB.'" /></td></tr>';
			if ($set->Source) $set->content .= '<tr><td>'.lang('Website description').' '.lang('in English').' ('.lang('Home Page').'):</td><td><input type="text" name="webDesENG" value="'.$set->webDesENG.'" /></td></tr>';
			if ($set->langHEB) $set->content .= '<tr><td>'.lang('Website keywords').' '.lang('in Hebrew').' ('.lang('Home Page').'):</td><td><input type="text" name="webKeyHEB" value="'.$set->webKeyHEB.'" /></td></tr>';
			if ($set->Source) $set->content .= '<tr><td>'.lang('Website keywords').' '.lang('in English').' ('.lang('Home Page').'):</td><td><input type="text" name="webKeyENG" value="'.$set->webKeyENG.'" /></td></tr>';
			$set->content .= '<tr><td>'.lang('Webmaster E-Mail').':</td><td><input type="text" name="webMail" value="'.$set->webMail.'" dir="ltr"> <input type="checkbox" name="mailCopy" '.($set->mailCopy ? 'checked' : '').' /> '.lang('Send copies to E-Mail').'</td></tr>';
			$set->content .= '<tr><td>'.lang('Items Per Page').':</td><td><input type="text" name="itemsPerPage" value="'.$set->itemsPerPage.'" onkeypress="return numbersOnly();" onpaste="return false" style="text-align: center; width: 40px;" /></td></tr>';
			$title[] = lang('Header Title');	$title[] = lang('Footer Title');
			$name[] = 'headerMail';		$name[] = 'footerMail';
			$value[] = $set->headerMail;	$value[] = $set->footerMail;
			$set->content .= '<tr><td valign="top">'.lang('Out E-mail design').':</td><td>'.textBox($title,$name,$value).'</td></tr>';
			}
		$set->content .= '<tr><td></td><td><input type="submit" value="'.lang('Save Changes').'" class="saveButton" /></td></tr>';
		$set->content .= '</table></form>';
		theme();
		break;
		
	// -------------------------------------------------------------------------------------------------------------------------
	
	case "saveGoogle":
		adminHeader();
		UpdateUnit("settings","googleTag='".$googleTag."'","id='1'");
		UpdateUnit("settings","analyticsCode='".$analyticsCode."'","id='1'");
		UpdateUnit("settings","webmasterTool='".$webmasterTool."'","id='1'");
		UpdateUnit("settings","conversionContact='".$conversionContact."'","id='1'");
		UpdateUnit("settings","conversionOrder='".$conversionOrder."'","id='1'");
		_goto($set->basepage.'?act=google');
		break;
	
	case "google":
		adminHeader(); perfTree($act);
		$set->pageTitle = lang('Google Tags');
		$set->content .= '<form method="post">';
		$set->content .= '<input type="hidden" name="act" value="saveGoogle" />';
		$set->content .= '<table>';
		$set->content .= '<tr><td valign="top">'.lang('Google Webmaster Tool Script').':</td><td><textarea name="webmasterTool" cols="70" rows="2" class="notEditor" dir="ltr" style="overflow: auto;">'.$set->webmasterTool.'</textarea></td></tr>';
		$set->content .= '<tr><td valign="top">'.lang('Google Analytics Script').':</td><td><textarea name="analyticsCode" cols="70" rows="8" class="notEditor" dir="ltr">'.$set->analyticsCode.'</textarea></td></tr>';
		if (is_install('contact.php')) $set->content .= '<tr><td valign="top">'.lang('Google Conversion Tool For Contact').':</td><td><textarea name="conversionContact" cols="70" rows="8" class="notEditor" dir="ltr" style="overflow: auto;">'.$set->conversionContact.'</textarea></td></tr>';
		if (is_install('catalog.php')) $set->content .= '<tr><td valign="top">'.lang('Google Conversion Tool For Orders').':</td><td><textarea name="conversionOrder" cols="70" rows="8" class="notEditor" dir="ltr" style="overflow: auto;">'.$set->conversionOrder.'</textarea></td></tr>';
		$set->content .= '<tr><td valign="top">'.lang('Google Tag Manager').':</td><td><textarea name="googleTag" cols="70" rows="2" class="notEditor" dir="ltr" style="overflow: auto;">'.$set->googleTag.'</textarea></td></tr>';
		$set->content .= '<tr><td></td><td><input type="submit" value="'.lang('Save Changes').'" class="saveButton" /></td></tr>';
		$set->content .= '</table></form>';
		theme();
		break;
	
	// -------------------------------------------------------------------------------------------------------------------------
	
	case "savePerfLang":
		adminHeader(1);
		UpdateUnit("settings","webLang='".$webLang."'","id='1'");
		if ($set->dbwebLang == "BOTH") UpdateUnit("settings","mainLang='".$mainLang."'","id='1'");
		UpdateUnit("settings","defThemeHEB='".$defThemeHEB."'","id='1'");
		UpdateUnit("settings","defThemeENG='".$defThemeENG."'","id='1'");
		_goto($set->SSLprefix.$set->basepage.'?act=perfLang');
		break;
	
	case "perfLang":
		adminHeader(1); perfTree($act);
		$set->pageTitle = lang('Language Settings');
		$set->content .= '<form method="post"><input type="hidden" name="act" value="savePerfLang" /><table>';
		$set->content .= '<tr><td>'.lang('Website Language').'</td><td>
			<select name="webLang">
				<option value="HEB" '.($set->dbwebLang == "HEB" ? 'selected' : '').'>'.lang('Hebrew').'</option>
				<option value="ENG" '.($set->dbwebLang == "ENG" ? 'selected' : '').'>'.lang('English').'</option>
				<option value="BOTH" '.($set->dbwebLang == "BOTH" ? 'selected' : '').'>'.lang('Hebrew and English').'</option>
			</select></td></tr>';
		if ($set->dbwebLang == "BOTH") $set->content .= '<tr><td>'.lang('Main Language').'</td><td>
			<select name="mainLang">
				<option value="HEB" '.($set->mainLang == "HEB" ? 'selected' : '').'>'.lang('Hebrew').'</option>
				<option value="ENG" '.($set->mainLang == "ENG" ? 'selected' : '').'>'.lang('English').'</option>
			</select></td></tr>';
		$hebtempqq=function_mysql_query("SELECT id,title".$set->lang." AS title FROM theme WHERE valid='1'",__FILE__);
		while ($hebtempww=mysql_fetch_assoc($hebtempqq)) $allHEBTemp .= '<option value="'.$hebtempww[id].'" '.($hebtempww[id] == $set->defThemeHEB ? 'selected' : '').'>'.$hebtempww[title].'</option>';
		$engtempqq=function_mysql_query("SELECT id,title".$set->lang." AS title FROM theme WHERE valid='1'",__FILE__);
		while ($engtempww=mysql_fetch_assoc($engtempqq)) $allENGTemp .= '<option value="'.$engtempww[id].'" '.($engtempww[id] == $set->defThemeENG ? 'selected' : '').'>'.$engtempww[title].'</option>';
		if ($set->langHEB) $set->content .= '<tr><td>'.lang('Template').' '.lang('in hebrew').'</td><td><select name="defThemeHEB"><option value="">'.lang('Without').'</option>'.$allHEBTemp.'</select></td></tr>';
		if ($set->Source) $set->content .= '<tr><td>'.lang('Template').' '.lang('in english').'</td><td><select name="defThemeENG"><option value="">'.lang('Without').'</option>'.$allENGTemp.'</select></td></tr>';
		$set->content .= '<tr><td></td><td><input type="submit" value="'.lang('Save Changes').'" class="saveButton" /></td></tr>';
		$set->content .= '</table></form>';
		theme();
		break;

	// -------------------------------------------------------------------------------------------------------------------------

	case "saveperfGlobal":
		adminHeader(1);
		$cookieExpire = round($cookieExpire * 60);
		UpdateUnit("settings","homePage='".$homePage."'","id='1'");
		UpdateUnit("settings","advanced='".$advanced."'","id='1'");
		UpdateUnit("settings","cookieExpire='".$cookieExpire."'","id='1'");
		UpdateUnit("settings","textarea='".$textarea."'","id='1'");
		UpdateUnit("settings","textarea_width='".$textarea_width."'","id='1'");
		UpdateUnit("settings","textarea_height='".$textarea_height."'","id='1'");
		UpdateUnit("settings","credit='".$credit."'","id='1'");
		UpdateUnit("settings","mobileSite='".($mobileSite ? '1' : '0')."'","id='1'");
		UpdateUnit("settings","mail_server='".$mail_server."'","id='1'");
		UpdateUnit("settings","mail_username='".$mail_username."'","id='1'");
		UpdateUnit("settings","mail_password='".$mail_password."'","id='1'");
		_goto($set->SSLprefix.$set->basepage.'?act=perfGlobal');
		break;
	
	case "perfGlobal":
		adminHeader(1); perfTree($act);
		$set->pageTitle = lang('Global Settings');
		$set->content .= '<form method="post"><input type="hidden" name="act" value="saveperfGlobal" /><table>';
		$qq=function_mysql_query("SELECT id,title".$set->lang." AS title FROM content",__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) $contentPages .= '<option value="'.$ww[id].'" '.($ww[id] == $set->homePage ? 'selected' : '').'>'.$ww[title].'</option>';
		$set->content .= '<tr><td>'.lang('Home page text').':</td><td><select name="homePage"><option value="">'.lang('Select').'</option>'.$contentPages.'</select></td></tr>';
		$set->content .= '<tr><td>'.lang('Advanced Field').':</td><td>
			<select name="advanced">
				<option value="0">'.lang('No').'</option>
				<option value="1" '.($set->advanced ? 'selected' : '').'>'.lang('Yes').'</option>
			</select></td></tr>';
		$set->content .= '<tr><td>'.lang('Cookie Time').':</td><td><input type="text" name="cookieExpire" value="'.round($set->cookieExpire / 60,0).'" dir="ltr" style="text-align: center; width: 60px;"> <span class="Must">* '.lang('in minutes').'</span></td></tr>';
		$set->content .= '<tr><td>'.lang('HTML Editor').':</td><td>
			<select name="textarea">
				<option value="0">'.lang('No').'</option>
				<option value="1" '.($set->textarea ? 'selected' : '').'>'.lang('Yes').'</option>
			</select></td></tr>';
		$set->content .= '<tr><td>'.lang('HTML Width').':</td><td><input type="text" name="textarea_width" value="'.$set->textarea_width.'" onkeypress="return numbersOnly();" onpaste="return false" style="text-align: center; width: 60px;" /></td></tr>';
		$set->content .= '<tr><td>'.lang('HTML Height').':</td><td><input type="text" name="textarea_height" value="'.$set->textarea_height.'" onkeypress="return numbersOnly();" onpaste="return false" style="text-align: center; width: 60px;" /></td></tr>';
		$set->content .= '<tr><td valign="top">'.lang('Site Credit').':</td><td><textarea name="credit" style="width: 300px; height: 100px;" dir="ltr">'.$set->credit.'</textarea></td></tr>';
		$set->content .= '<tr><td></td><td><input type="checkbox" name="mobileSite" '.($set->mobileSite || $mobileSite ? 'checked' : '').' /> '.lang('Allow Mobile Website Mode').'</td></tr>';
		$set->content .= '<tr><td colspan="2"><b><u>'.lang('Mail Server Settings').':</u></b></td></tr>';
		$set->content .= '<tr><td>'.lang('Mail Server').':</td><td><input type="text" name="mail_server" value="'.$set->mail_server.'" dir="ltr" /></td></tr>';
		$set->content .= '<tr><td>'.lang('Mail Username').':</td><td><input type="text" name="mail_username" value="'.$set->mail_username.'" dir="ltr" /></td></tr>';
		$set->content .= '<tr><td>'.lang('Mail Password').':</td><td><input type="text" name="mail_password" value="'.$set->mail_password.'" dir="ltr" /></td></tr>';
		$set->content .= '<tr><td></td><td><input type="submit" value="'.lang('Save Changes').'" class="saveButton" /></td></tr>';
		$set->content .= '</table></form>';
		theme();
		break;
	
	###################################### [ Performence ] ######################################
	
	###################################### [ Files Search ] #####################################
	case "search":
		adminHeader(1); perfTree($act);
		$formats[] = "txt";
		$formats[] = "php";
		$formats[] = "js";
		$formats[] = "htm";
		$formats[] = "html";
		$formats[] = "css";

		if ($fedDir = opendir(getcwd())) while (($file=readdir($fedDir)) !== false) if ($file != "." AND $file != ".." AND $file != $set->basepage) if (is_dir($file)) $dirsList .= '<option value="'.$file.'/" '.($_POST[path] == $file.'/' ? 'selected' : '').'>'.$file.'</option>';
		$set->content .= '<form method="post">';
		$set->content .= '<input type="hidden" name="result" value="1" />';
		$set->content .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>';
		$set->content .= '<td align="'.$set->right.'"><b>'.lang('Search For').':</b> <input type="text" name="q" value="'.stripslashes($q).'" dir="ltr" /> <select name="path"><option value="">'.lang('All Directories').'</option>'.$dirsList.'</select> <input type="submit" value="'.lang('Search').'" /></td>';
		$set->content .= '<td align="'.$set->left.'"><table><tr>';
		$set->content .= '<td><input type="checkbox" name="formatX" value="All" '.($formatX == "All" || !$format ? 'checked' : '').' /> <b>'.lang('All').'</b></td>';
		for ($i=0; $i<= count($formats)-1; $i++) $set->content .= '<td><input type="checkbox" name="format[]" value="'.$formats[$i].'" '.(@in_array($formats[$i],$format) ? 'checked' : '').' /> '.$formats[$i].'</td>';
		$set->content .= '</tr></table></td></tr></table>';
		$set->content .= '</form>';

		function searchFile($fullFile="",$q="") {
			if (!$q OR !$fullFile) return false;
			$openFile = fopen($fullFile,"r");
			$handle = fread($openFile,filesize($fullFile));
			fclose($openFile);
			if (strpos($handle,stripslashes($q))) return true; else return false;
			}

			$set->content .= '<table width="100%" id="height" cellpadding="0" cellspacing="0"><tr><td width="30%" align="'.$set->right.'" valign="top">';
		if ($_POST[result]) {
			if ($formatX == "All") $format = $formats;
			$set->content .= '<u><b>'.lang('Results').':</b></u><br />';
			function readFiles($path="",$q="") {
				global $set,$format;
				if (!$q) return false;
				$dirNew=str_replace(getcwd(),"",$path);
				if ($fedDir = opendir($path)) {
					while (($file=readdir($fedDir)) !== false) {
						if ($file != "." AND $file != ".." AND $file != $set->basepage) {
							if (is_dir($file)) {
								$content .= readFiles($file.'/',$q);
								} else {
									$exp=explode(".",$file);
									if (in_array($exp[count($exp)-1],$format)) if (searchFile($dirNew.$file,$q)) $content .= '<a href="'.$set->basepage.'?act=openFile&fullFile='.$dirNew.$file.'&q='.$q.'" target="openFile">'.$dirNew.$file.'</a><br />';
								}
							}
						}
					}
				return $content;
				}
			if (!$path) $path = getcwd();
			$set->content .= readFiles($path,$q);
			}
		$set->content .= '</td><td width="70%" height="100%" valign="top" style="border: 1px #B2B2B2 solid;"><iframe width="100%" height="550" name="openFile" frameborder="0"></iframe></td>';
		$set->content .= '</table>';
		theme();
		break;
		
	case "openFile":
		header('content-type: text/html; charset=UTF-8');
		$openFile = fopen($fullFile,"r");
		$handle = fread($openFile,filesize($fullFile));
		fclose($openFile);
		$q = stripslashes($q);
		$content .= '<pre>';
		$find=Array("%3C","%3E","<",">");
		$replace=Array("&lt;","&gt;","&lt;","&gt;");
		$q = str_replace($find,$replace,$q);
		$content .= '<span style="color: #555555;">'.str_replace($q,'<span style="background: yellow; color: #000000; font-weight: bold;">'.$q.'</span>',htmlspecialchars($handle)).'</span>';
		$content .= '</pre>';
		print $content;
		break;
	
	case "phpinfo":
		adminHeader();
		phpinfo();
		break;
	
	##################################### [ Files Search ] #####################################
	
	case "logout":
		adminHeader();
		setcookie($set->setuseradmin,'',time()-60);
		setcookie($set->setidadmin,'',time()-60);
		setcookie($set->setpassadmin,'',time()-60);
		_goto($set->SSLprefix.$set->basepage);
		break;
		}

?>