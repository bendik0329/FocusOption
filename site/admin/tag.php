<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'traders_tag';

function listOfStatus($status='') {
	$arr = Array("revenue","pending","fraud","chargeback","duplicates","withdrawal","other");
	for ($i=0; $i<=count($arr)-1; $i++) $html .= '<option value="'.$arr[$i].'" '.($arr[$i] == $status ? 'selected' : '').'>'.lang(strtoupper($arr[$i])).'</option>';
	return $html;
	}

switch ($act) {
	/* ------------------------------------ [ Manage Languages ] ------------------------------------ */
	
	case "valid":
		$db=dbGet($id,$appTable);
		if ($db['valid']) $valid='0'; else $valid='1';
		updateUnit($appTable,"valid='".$valid."'","id='".$db['id']."'");
		echo '<a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$db['id'].'\',\'lng_'.$db['id'].'\');" style="cursor: pointer;">'.xvPic($valid).'</a>';
		die();
		break;
		
	case "delete":
		$sql = "delete from " . $appTable . " where id=" . $id;
		mysql_query($sql);
		_goto($set->SSLprefix.$set->basepage);
		break;
		
	case "add":
		if (!$db['trader_id']) $errors['trader_id'] = 1;
		// if ($db['status'] == "revenue" AND !$db['revenue']) $errors['revenue'] = 1;
		if (empty($errors)) {
			if (!$db['id']) $db[rdate] = dbDate();
			$db['added_by'] = $set->userInfo['id'];
			$db['valid'] = 1;
			dbAdd($db,$appTable);
			_goto($set->SSLprefix.$set->basepage);
			}
	
	default:
		
		$pageTitle = lang(ptitle('Traders Tag'));
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
		if ($id) $db = dbGet($id,$appTable);
		$sql = "SELECT * FROM ".$appTable." ORDER BY id DESC";
		$qq=function_mysql_query($sql,__FILE__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$l++;
			// $MerchantInfo = dbGet($ww['merchant_id'],"merchants");
			$qry = "SELECT affiliate_id FROM data_reg WHERE merchant_id = ".$ww['merchant_id']." and trader_id='".$ww['trader_id']."'";
			// echo ($qry.'<br>');
			
			$getAffiliate = mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
			$affiliateInfo = dbGet($getAffiliate['affiliate_id'],"affiliates");
			$langList .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						<td align="center">'.$ww['id'].'</td>
						<td align="center"><a href="'.$set->SSLprefix.$set->basepage.'?id='.$ww['id'].'">'.lang('Edit').'</a> | <a href="'.$set->SSLprefix.$set->basepage.'?act=delete&id='.$ww['id'].'">'.lang('Delete').'</a></td>
						<td align="center">'.$ww['trader_id'].'</td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'" target="_blank">'.$affiliateInfo['id'].'</a></td>
						<td align="center"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'" target="_blank">'.$affiliateInfo['username'].'</a></td>
						<td align="center">'.dbDate($ww['rdate']).'</td>
						<td align="center">'.strtoupper(listMerchants($ww['merchant_id'],1,1)).'</td>
						<td align="center">'.$ww['admin_revenue'].'%</td>
						<td align="center">'.$ww['revenue'].'%</td>
						<td align="center">'.strtoupper($ww['status']).'</td>
						<td align="left">'.strtoupper($ww['notes']).'</td>
						<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>
						<td align="center">'.xvPic($ww['calReport']).'</td>
					</tr>';
			}
		$set->content = '<form method="post">
						<input type="hidden" name="act" value="add" />
						'.($db['id'] ? '<input type="hidden" name="db[id]" value="'.$db['id'].'" />' : '').'
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Tag').'</div>
						<div id="tab_1">
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">'.lang(ptitle('Trader ID')).':</td><td><input type="text" id="trader_id" name="db[trader_id]" value="'.$db['trader_id'].'" '.($errors['trader_id'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><td align="left">'.lang('Merchant').':</td><td><select name="db[merchant_id]">'.listMerchants($db['merchant_Id']).'</select></td></tr>
							<tr><td></td><td align="left"><input type="button" id="btnLoad" value="'.lang('Load').'" /></td></tr>
							</table>
							<table id="statusTable" style="'. (!empty($_GET['id']) ?  "" : "display:none" ). '" width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF">
							<tr><td align="left" width=25%>'.lang('Status').':</td><td><select name="db[status]">'.listOfStatus($db['status']).'</select></td></tr>
							<tr><td align="left">'.lang('Admin Revenue').' %:</td><td><input type="text" name="db[admin_revenue]" value="'.$db['admin_revenue'].'" '.($errors['revenue'] ? 'style="border: 1px red solid;"' : '').' maxlength="2" style="text-align: center;" /></td></tr>
							<tr><td align="left">'.lang('Affiliate Revenue').' %:</td><td><input type="text" name="db[revenue]" value="'.$db['revenue'].'" '.($errors['revenue'] ? 'style="border: 1px red solid;"' : '').' maxlength="2" style="text-align: center;" /></td></tr>
							<tr><td align="left">'.lang('Notes').':</td><td><input type="text" name="db[notes]" value="'.$db['notes'].'" /></td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</div>
						</form>
						<script>
						$("#btnLoad").click(function(){
							trader_id = $("#trader_id").val();
							if(trader_id==""){
									$.fancybox({ 
									 closeBtn:false, 
									  minWidth:"250", 
									  minHeight:"180", 
									  autoCenter: true, 
									  afterClose:function(){
										  $("#trader_id").focus();
									  },			  
									  content: "<h1><div style=\'float:left;\'><img src=\''.$set->SSLprefix.'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'. lang('Please enter Trader ID.') .'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
								  });
								  $("#trader_id").focus();
								return false;
							}
							$.get("'.$set->SSLprefix.'ajax/checkValidTrader.php?trader_id="+trader_id, function(res) {
								try {
									if(res){
										$("#statusTable").show();
									}
									else{
											$.fancybox({ 
											 closeBtn:false, 
											  minWidth:"250", 
											  minHeight:"180", 
											  autoCenter: true, 
											  afterClose:function(){
												  $("#trader_id").focus();
											  },			  
											  content: "<h1><div style=\'float:left;\'><img src=\''.$set->SSLprefix.'images/warning-3.png\' width=\'30px\' height=\'30px\' ></div>&nbsp;&nbsp;<span>'.lang('Error').'</span></h1><div align=\'center\'><div id=\'alert1\'  style=\'margin-top:40px\'><h2>'. lang('Please enter a valid Trader ID.') .'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\''.lang('Continue').'\' onClick=\'$.fancybox.close()\'></div></div>" 
										  });
										  $("#trader_id").focus();
										return false;
									}
								} catch (error) {
									console.log(error);
								}
							});
						});
						</script>
						<hr />
						<div class="normalTableTitle">'.lang('Tags List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td align="center">#</td>
								<td align="center">'.lang('Actions').'</td>
								<td align="center">'.lang(ptitle('Trader ID')).'</td>
								<td align="center">'.lang('Affiliate ID').'</td>
								<td align="center">'.lang('Affiliate Username').'</td>
								<td align="center">'.lang('Added Date').'</td>
								<td align="center">'.lang('Merchant ID').'</td>
								<td align="center">'.lang('Admin Revenue').' %</td>
								<td align="center">'.lang('Revenue').' %</td>
								<td align="center">'.lang('Status').'</td>
								<td align="left">'.lang('Notes').'</td>
								<td align="center">'.lang('Available').'</td>
								<td align="center">'.lang('Calculated').'</td>
							</tr></thead><tfoot>'.$langList.'</tfoot>
						</table>';
		theme();
		break;
	}

?>