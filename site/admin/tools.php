<?php

chdir('../');
require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);


switch($act){
	case "move_trader":
		if ($actBtn == "Find") _goto('tools.php?trader_id='.$trader_id.'&merchant_id='.$merchant_id);
		if (!$new_affiliate_id OR !$trader_id) _goto('tools.php');
		
		$qq = function_mysql_query("SELECT * FROM data_reg_".strtolower(listMerchants($merchant_id,1))." WHERE trader_id = '".$trader_id."'",__FILE__);
		while($ww = mysql_fetch_assoc($qq)) $newCtag = str_replace("a".$ww['affiliate_id'],"a".$new_affiliate_id,$ww['ctag']);
		
		function_mysql_query("UPDATE data_reg_".strtolower(listMerchants($merchant_id,1))." SET ctag = '".$newCtag."',affiliate_id = '".$new_affiliate_id."' WHERE trader_id = '".$trader_id."'",__FILE__);
		function_mysql_query("UPDATE data_sales_".strtolower(listMerchants($merchant_id,1))." SET ctag = '".$newCtag."',affiliate_id = '".$new_affiliate_id."' WHERE trader_id = '".$trader_id."'",__FILE__);
		
		_goto('tools.php?trader_id='.$trader_id.'&ok=1&aff_id='.$new_affiliate_id.'&merchant_id='.$merchant_id);
		break;
	
	default:
		$set->pageTitle = lang('Move Trader to another affiliate');
		if ($trader_id) {

			$currentAff = mysql_fetch_assoc(function_mysql_query("SELECT * FROM data_reg_".strtolower(listMerchants($merchant_id,1))." WHERE trader_id = '".$trader_id."'",__FILE__));
			$affiliateqq=function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);
			while ($affiliateww=mysql_fetch_assoc($affiliateqq)) $allAffiliates .= '<option value="'.$affiliateww['id'].'" '.($db['refer_id'] == $affiliateww['id'] ? 'selected' : '').'>['.$affiliateww['id'].'] '.$affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
			
			$amountqq = function_mysql_query("SELECT rdate,type,amount FROM data_sales_".strtolower(listMerchants($merchant_id,1))." WHERE trader_id='".$trader_id."' ORDER BY rdate ASC",__FILE__);
			while ($amountww=mysql_fetch_assoc($amountqq)) {
				if ($amountww['type'] == "deposit") {
					$depositAmount += $amountww['amount'];
					if (!$ftdAmount) {
						$ftdAmount = $amountww['amount'];
						$ftdDate = $amountww['rdate'];
						}
					} else if ($amountww['type'] == "bonus") $bonusAmount += $amountww['amount'];
					else if ($amountww['type'] == "withdrawal") $withdrawalAmount += $amountww['amount'];
				}
			
			}
			
		$set->content .= '
		<div align="left" style="padding: 10px; line-height: 20px;">
			<form action="'.$set->SSLprefix.'admin/tools.php" method="post">
				<input type="hidden" name="act" value="move_trader" />
				<table>
					<tr><td>'.lang('Trader ID').':</td><td><input type="text" name="trader_id" value="'.$trader_id.'" style="background: #FFFFFF; width: 120px; text-align:center;" /> <select name="merchant_id">'.listMerchants($merchant_id).'</select> <input type="submit" name="actBtn" value="Find" /></td></tr>
					'.($trader_id ? '<tr><td>'.lang('Current Affiliate').':</td><td><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$currentAff['affiliate_id'].'" target="_blank"><b>'.($currentAff['affiliate_id'] ? $currentAff['affiliate_id'] : 'Cant Find trader ID').'</b></a></td></tr>
					'.($currentAff['trader_id'] ? '<tr><td style="padding-right: 10px;">'.lang('Change Trader To').':</td><td><select '.(!$currentAff['affiliate_id'] ? 'disabled' : '').' name="new_affiliate_id" style="padding:5px;"><option>Choose One</option>'.$allAffiliates.'</select>' : '').'</td></tr>' : '').'
				</table>
				'.($currentAff['trader_id'] ? '<hr />
				<table>
					<tr><td style="font-weight: bold; text-decoration: underline;">'.lang('Trader Records Information').':</td></tr>
					<tr><td>'.lang('Trader ID').':</td><td>'.$currentAff['trader_id'].'</td></tr>
					<tr><td>'.lang('Current Affiliate').':</td><td><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$currentAff['affiliate_id'].'" target="_blank">'.$currentAff['affiliate_id'].'</a></td></tr>
					<tr><td>'.lang('Registration Date').':</td><td>'.date("d/m/Y", strtotime($currentAff['rdate'])).'</td></tr>
					<tr><td>'.lang('First Deposit Date').':</td><td>'.date("d/m/Y", strtotime($ftdDate)).'</td></tr>
					<tr><td>'.lang('First Deposit Amount').':</td><td>'. $set->currency .' '.number_format($ftdAmount,2).'</td></tr>
					<tr><td>'.lang('Total Deposit Amount').':</td><td>'. $set->currency .' '.number_format($depositAmount,2).'</td></tr>
					<tr><td>'.lang('Total Withdrawal Amount').':</td><td>'. $set->currency .' '.number_format($withdrawalAmount,2).'</td></tr>
					<tr><td>'.lang('Total Bonus Amount').':</td><td>'. $set->currency .' '.number_format($bonusAmount,2).'</td></tr>
				</table>' : '').'
				<br />
				'.($currentAff['trader_id'] ? '<input type="submit" name="actBtn" value="'.lang('Change').'" />' : '').' '.($ok ? '<div style="color: green; font-size: 14px; font-weight: bold; padding-top: 5px;">Trader ID: <u>'.$trader_id.'</u> was successfully moved to affiliate id: <u>'.$aff_id.'</u>!</div>' : '').'
			</form>
		</div>';
		
		theme();
		break;
}
?>