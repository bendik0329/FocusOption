<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
//if (!isManager() or !$set->isNetwork) _goto('/manager/');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/manager/";
if (!isManager() or !$set->isNetwork ) _goto($lout);

$appTable = 'merchants';

switch ($act) {
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'merchant_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
	
case "relateaffiliates":
		
		
	//	$affiliatesQ = function_mysql_query('SELECT id,merchants FROM affiliates WHERE valid=1 AND id NOT IN (SELECT DISTINCT affiliate_id FROM affiliates_deals WHERE merchant_id='.$id.')',__FILE__);
		$affiliatesQ = function_mysql_query('SELECT id,merchants FROM affiliates WHERE valid=1',__FILE__);
		//$defaultCPA = mysql_result(function_mysql_query('SELECT cpa_amount FROM merchants WHERE id='.$id,__FILE__),0,0);
		while($row = mysql_fetch_assoc($affiliatesQ)){
			$mr = ($row['merchants']!='' ? '|' : '').$row['merchants'].($row['merchants']!='' ? '|' : '');
			// |29| , |2|10|23|3|5|16|19|4|6|13|17|7|11|22|18|1|9|20|12|21|
			if(strpos($mr,'|'.$id.'|')===false){
				// if($mr!=''){
					// $mr.='|';
				// }
				$mr.=$id;
				
				$mr = ltrim($mr,'|');
				function_mysql_query('UPDATE affiliates SET merchants="'.$mr.'" WHERE id='.$row['id'],__FILE__);
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
	
		   $affiliatesQ = function_mysql_query('SELECT id,merchants FROM affiliates WHERE valid=1',__FILE__);
		   
			while($row = mysql_fetch_assoc($affiliatesQ)){
				
				$mr = $row['merchants'];

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
				
				
				function_mysql_query('UPDATE affiliates SET merchants="'.$mr.'" WHERE id='.$row['id'],__FILE__);
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
	
	case "add":
		if (!$db['name']) $errors['name'] = 1;
		if (!$db['website']) $errors['website'] = 1;
		if (!$db['params']) $errors['params'] = 1;
		if (empty($errors)) {
			$db[rdate] = dbDate();
			if ($valid) $db['valid']=1; else $db['valid'] = 0;
			if ($campaignispartofparams) $db['campaignispartofparams']=1; else $db['campaignispartofparams'] = 0;
			dbAdd($db,$appTable);
			_goto($set->SSLprefix.$set->basepage);
			}
	
	case "new":
		if ($id) {
		
		if ($id !=aesDec($_COOKIE['mid']))
		_goto($set->basepage);
		
		
			$db=dbGet($id,$appTable);
			$pageTitle = lang('Editing').' '.$db['name'];
			}  else {
			_goto($set->basepage.'?act=list');
			$pageTitle = lang('ADD NEW MERCHANT');
			}
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
		for ($i=1; $i<=5; $i++) {
			$listContacts .= '<tr>
							<td align="center">'.$i.'</td>
							<td><input type="text" name="db[name_'.$i.']" value="'.$db['name_'.$i].'" /></td>
							<td><input type="text" name="db[mail_'.$i.']" value="'.$db['mail_'.$i].'" /></td>
							<td><input type="text" name="db[phone_'.$i.']" value="'.$db['phone_'.$i].'" /></td>
						</tr><tr>
							<td colspan="4" height="10"></td>
						</tr>';
			}
			
		$qry ="SELECT COLUMN_NAME AS name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '".$ss->db_name."' AND TABLE_NAME = 'producttitles' and not column_name ='id' and not column_name ='source' order by column_name";
		$productListQ = function_mysql_query($qry,__FILE__);
		
		$productListStr = '';
		while($row = mysql_fetch_assoc($productListQ)){
			$productListStr.='<option value="'.$row['name'].'" '.($db['producttype']==$row['name'] ? 'selected' : '').'>'.$row['name'].'</option>';
		}
		
		$set->content .= '<form action="'.$set->SSLprefix.$set->basepage.'?act=add" method="post">
						<input type="hidden" name="db[id]" value="'.$db['id'].'" />
						<div class="normalTableTitle">'.lang('Merchant Details').'</div>
						<div align="center" style="width: 1000px; background: #EFEFEF;">
						<table width="98%" border="0" cellpadding="0" cellspacing="0">
							<tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" '.err('name').'>'.lang('Merchant Name').': <span class="required">*</span></td>
								<td align="left"><input type="text" name="db[name]" value="'.$db['name'].'" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" '.err('website').'>'.lang('Website').': <span class="required">*</span></td>
								<td align="left"><input type="text" name="db[website]" value="'.($db['website'] ? $db['website'] : 'http://').'" / style="width:250px;"></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" '.err('producttype').'>'.lang('Product Type').':</td>
								<td align="left">
								<!--<input type="text" name="db[pos]" value="'.$db['pos'].'" style="width: 250px;" />-->
								<select name="db[producttype]">
									<option value=-1>Choose Product Type</option>
									'.$productListStr.'
								</select>
								</td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
							<td align="left" width="185" '.err('pos').'>'.lang('Position').':</td>
								<td align="left"><input type="text" name="db[pos]" value="'.$db['pos'].'" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" '.err('email').'>'.lang('E-Mail').':</td>
								<td align="left"><input type="text" name="db[email]" value="'.$db['email'].'" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr>
							</table>
							<div class="normalTableTitle">'.lang('Technical Configuration').'</div>
						<div style="width: 1000px; background: #EFEFEF;">
						<table width="98%" border="0" cellpadding="0" cellspacing="0">
						<tr>
								<td colspan="2" height="10"></td>
							</tr>
							<tr>
								<td align="left" width="185" '.err('params').'>'.lang('Params').':</td>
								<td align="left"><input type="text" name="db[params]" value="'.$db['params'].'" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" '.err('campaignid').'>'.lang('Default Campaign ID').':</td>
								<td align="left"><input type="text" name="db[campaignid]" value="'.$db['campaignid'].'" style="width: 250px;" /></td>
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" '.err('campaignparamname').'>'.lang('Campaign Parameter Name').':</td>
								<td align="left"><input type="text" name="db[campaignparamname]" value="'.$db['campaignparamname'].'" style="width: 250px;" /></td>
							</tr><tr>
							<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left" width="185" '.err('campaignispartofparams').'>'.lang('Is CampaignID parameter should be part of the Btag Parameters?').':</td>
								<td align="left"><input type="checkbox" name="campaignispartofparams" '.($db['campaignispartofparams'] ? 'checked="checked"' : '').' />
												
								
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr>
							<tr>
								<td align="left" width="185" '.err('').'>'.lang('Logo URL').':</td>
								<td align="left"><input type="text" name="db[LogoURL]" value="'.$db['LogoURL'].'" style="width: 250px;" /></td>
								
							</tr><tr>
								<td colspan="2" height="10"></td>
							</tr>';
							if ($set->userInfo['id'] == "1") {
							$set->content .= '
							<tr>
								<td align="left" width="185" '.err('StylingURL').'>'.lang('Styling URL').':</td>
								<td align="left"><input type="text" name="db[StylingURL]" value="'.$db['StylingURL'].'" style="width: 250px;" /></td>
								
							</tr>'; }
														
							$set->content.='<tr>
								<td colspan="2" height="30"></td>
							</tr>
							<tr>
								<td colspan="2"><div>* An extra parameter for the landing url. Use campID or affID as value (optional):</div></td>
							</tr>
							<tr>
								<td colspan="2" height="5"></td>
							</tr>
							<tr id="empn">
								<td align="left" width="185" '.err('memberParameter').'>'.lang('Member Parameter Name').':</td>
								<td align="left"><input id="empnt" type="text" name="db[extraMemberParamName]" value="'.$db['extraMemberParamName'].'" style="width: 250px;" /></td>
								
							</tr>';
							
							$set->content.='<tr>
									<td colspan="2" height="10"></td>
								</tr><tr id="empv" style="'.(($db['extraMemberParamName']!='' AND $db['extraMemberParamName']!=NULL) ? '' : 'display:none').'">
								<td align="left" width="185" '.err('memberParameterV').'>'.lang('Member Parameter Value').':</td>
								<td align="left"><input id="empvt" type="text" name="db[extraMemberParamValue]" value="'.$db['extraMemberParamValue'].'" style="width: 250px;" /></td>
								
							</tr>
							
							
							<script type="text/javascript">
								$(document).ready(function(){
									$("#empn").keyup(function(e){
										
										//console.log($("#empnt").val().length);
										
										if($("#empnt").val().length>0){
											$("#empv").show();
										}else{
											$("#empv").hide();
										}
									});
								});
							</script>
							
							';
							
							
							
							$set->content .= '<tr>
								<td colspan="2" height="10"></td>
							</tr>
							</div>
						</table>
						</div>
						<br />
						<div class="normalTableTitle">'.lang('Actions').'</div>
						<div style="width: 1000px; background: #EFEFEF;">
						<table width="98%" border="0" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF">
							<tr>
								<td colspan="3" height="10"></td>
							</tr><tr>
								<td width="30" align="left"><input type="checkbox" name="valid" '.($db['valid'] ? 'checked' : '').' />
								<td align="left" colspan="2">'.lang('Publish').'</td></td>
							</tr><tr>
								<td colspan="3" height="10"></td>
							</tr>
						</table>
						</div>
						<br />
						<div class="normalTableTitle">'.lang('Default Commissions').'</div>
						<div style="width: 1000px; background: #EFEFEF;">
						<table width="95%" border="0" cellpadding="0" cellspacing="0" bgcolor="#EFEFEF">
							<tr>
								<td colspan="2" height="10"></td>
							</tr><tr>
								<td align="left">'.lang(ptitle('Minimum Deposit')).'</td>
								<td align="left">'. $set->currency .' <input type="text" name="db[min_cpa_amount]" value="'.$db['min_cpa_amount'].'" style="text-align: center;" /></td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr><tr>
								<td align="left">'.lang('CPA').'</td>
								<td align="left">'. $set->currency .'  <input type="text" name="db[cpa_amount]" value="'.$db['cpa_amount'].'" style="text-align: center;" /></td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr><tr>
								<td align="left">'.lang('DCPA').'</td>
								<td align="left">% <input type="text" name="db[dcpa_amount]" value="'.$db['dcpa_amount'].'" style="text-align: center;" /></td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr><tr>
								<td align="left">'.lang('Revenue').'</td>
								<td align="left">% <input type="text" name="db[revenue_amount]" value="'.$db['revenue_amount'].'" style="text-align: center;" /></td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr>'.($set->deal_cpl ? '<tr>
								<td align="left">'.lang('CPL').'</td>
								<td align="left">'. $set->currency .'  <input type="text" name="db[cpl_amount]" value="'.$db['cpl_amount'].'" style="text-align: center;" /></td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr>' : '').($set->deal_cpc ? '<tr>
								<td align="left">'.lang('CPC').'</td>
								<td align="left">'. $set->currency .'  <input type="text" name="db[cpc_amount]" value="'.$db['cpc_amount'].'" style="text-align: center;" /></td>
							</tr><tr>
								<td colspan="2" height="5"></td>
							</tr>' : '').($set->deal_cpm ? '<tr>
								<td align="left">'.lang('CPM').'</td>
								<td align="left">'. $set->currency .'  <input type="text" name="db[cpm_amount]" value="'.$db['cpm_amount'].'" style="text-align: center;" /></td>
							</tr>' : '').'<tr>
								<td colspan="3" height="10"></td>
							</tr>
							
						</table>
						</div>
						<br />
						<div align="right" style="width: 1000px;"><input type="submit" value="'.lang('Save').'" /></div>
						</form>';
		theme();
		break;
	
	default:
		$qr = "SELECT * FROM ".$appTable." where id='".aesDec($_COOKIE['mid'])  ."' ORDER BY pos";
		//die($qr);
		$qq=function_mysql_query($qr,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			$merchantList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td align="center">'.$ww['id'].'</td>
						<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?act=new&id='.$ww['id'].'">'.lang('Edit').'</a></td>
						<td align="left">'.$ww['name'].'</td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center">'.strtoupper($ww['producttype']).'</td>
						<td align="left"><a href="'.$ww['website'].'" target="_blank">'.$ww['website'].'</a></td>
						<td align="center">'. $set->currency .' '.$ww['min_cpa_amount'].'</td>
						<td align="center">'. $set->currency .' '.$ww['cpa_amount'].'</td>
						<td align="center">% '.$ww['dcpa_amount'].'</td>
						<td align="center">% '.$ww['revenue_amount'].'</td>
						'.($set->deal_cpl ? '<td align="center">'. $set->currency .' '.$ww['cpl_amount'].'</td>':'').'
						'.($set->deal_cpc ? '<td align="center">'. $set->currency .' '.$ww['cpc_amount'].'</td>':'').'
						'.($set->deal_cpm ? '<td align="center">'. $set->currency .' '.$ww['cpm_amount'].'</td>':'').'
						<td align="center">'.$ww['pos'].'</td>
						<td align="center" id="merchant_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'merchant_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
						<td align="center" id="merchant_'.$ww['id'].'">
							<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=relateaffiliates&id='.$ww['id'].'\',\'merchant_'.$ww['id'].'\'); setTimeout(function(){location.reload();},500);" style="cursor: pointer;">Relate All</a>
							<span>&nbsp;&nbsp;&nbsp;</span>
							<a onclick="var r=confirm(\'Are you sure you want to delete all affiliate relations to this merchant?\'); if(r==true){ajax(\''.$set->SSLprefix.$set->basepage.'?act=unrelateaffiliates&id='.$ww['id'].'\',\'merchant_'.$ww['id'].'\'); setTimeout(function(){location.reload();},500);}" style="cursor: pointer;">Unrelate All</a>
						</td>
					</tr>';
			}
		$set->content = '<div class="normalTableTitle">'.lang('MERCHANTS LIST').'</div>
					<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
						<thead>
						<tr>
							<td align="center">#</td>
							<td align="center">'.lang('Actions').'</td>
							<td style="text-align: left;">'.lang('Merchant Name').'</td>
							<td align="center">'.lang('Last Update').'</td>
							<td align="center">'.lang('Type').'</td>
							<td style="text-align: left;">'.lang('Merchant Website').'</td>
							<td align="center">'.lang(ptitle('Minimum Deposit')).'</td>
							<td align="center">'.lang('CPA').'</td>
							<td align="center">'.lang('DCPA').'</td>
							<td align="center">'.lang('Revenue Share').'</td>
							'.($set->deal_cpl ? '<td align="center">'.lang('CPL').'</td>' : '').'
							'.($set->deal_cpc ? '<td align="center">'.lang('CPC').'</td>' : '').'
							'.($set->deal_cpm ? '<td align="center">'.lang('CPM').'</td>' : '').'
							<td align="center">'.lang('Position').'</td>
							<td align="center">'.lang('Published').'</td>
							<td align="center">Affiliates Relation</td>
						</tr>
						</thead>
						<tfoot>'.$merchantList.'</tfoot>
					</table>';
		theme();
		break;
	}

?>