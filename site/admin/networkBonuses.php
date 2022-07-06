<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

$appTable = 'network_bonus';


// Change Network Bonus
if ($bonus_db['title'] AND $bonus_db['min_ftd'] AND $bonus_db['bonus_amount']) {
			if ($bonus_db_valid) $bonus_db['valid'] = 1;
			dbAdd($bonus_db,"network_bonus");
			}

		if (count($bonus_ids) > 0) {
			for ($i=0; $i<count($bonus_ids); $i++) {
				if ($bonus_delete[$i]) {
					function_mysql_query("DELETE FROM ". $appTable ." WHERE id='".$bonus_ids[$i]."'",__FILE__);
					continue;
					}
					
				$dt =  date("Y-m-01",strtotime($bonus_rdate[$i]));
				function_mysql_query("UPDATE ". $appTable ." SET valid='".($bonus_valid[$i] ? '1' : '0')."', title='".$bonus_title[$i]."', group_id='".$bonus_group_id[$i]."', min_ftd='".$bonus_min_ftd[$i]."', bonus_amount='".$bonus_bonus_amount[$i]."', rdate='". $dt ."' WHERE id='".$bonus_ids[$i]."'",__FILE__);
		}
		_goto($set->basepage . '?merchant_id=' . $merchant_id);
		die;
}

		

$isNew = (isset($_GET['new']) && $_GET['new']==1 ? 1 : 0);
//die(var_dump($isNew));

		$pageTitle = lang('Network Bonuses for');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		// $merchant_id = '1';
		$numqq = function_mysql_query("SELECT id FROM merchants",__FILE__);
              
		if (!$merchant_id) {
			$numww = mysql_fetch_assoc($numqq);

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
	
		$sql = "SELECT * FROM ". $appTable ." WHERE merchant_id=" . $merchant_id . " ORDER BY group_id ASC, bonus_amount ASC";
		
		$bonusqq = function_mysql_query($sql,__FILE__);
		$i = 1;
		while($bonusww=mysql_fetch_assoc($bonusqq)) {
				$listBonuses .= '<tr '.($i % 2 ? 'class="trLine"' : '').'>
								<td>'.$i.'<input type="hidden" name="bonus_ids[]" value="'.$bonusww['id'].'" /></td>
								<td align="center"><input type="text" name="bonus_title[]" value="'.$bonusww['title'].'" style="width: 250px;" /></td>
								<td align="center"><select name="bonus_group_id[]" style="width: 150px;"><option value="99999">'.lang('All').'</option><option value="">'.lang('General').'</option>'.listGroups($bonusww['group_id']).'</select></td>
								<td align="center"><input type="text" name="bonus_min_ftd[]" value="'.$bonusww['min_ftd'].'" maxlength="5" style="width: 100px; text-align: center;" /></td>
								<td align="center">'. $set->currency .'  <input type="text" name="bonus_bonus_amount[]" value="'.$bonusww['bonus_amount'].'" style="width: 100px; text-align: center;" maxlength="5" /></td>
								<td align="center"><input name="bonus_rdate[]" class="rdate" class="date-picker" value="'.date('F Y',strtotime($bonusww['rdate'])).'"/></td>
								<td align="center"><input type="checkbox" name="bonus_valid[]" '.($bonusww['valid'] ? 'checked="checked"' : '').' /> '.lang('Active').' <input type="checkbox" name="bonus_delete[]" /> '.lang('Delete').'</td>
								<td align="center"></td>
							</tr>';
				$i++;
		}
		$set->content .= '<div class="normalTableTitle" data-tab2="network_bonus" >'.lang('Network Bonuses').'</div>
					<form method="POST">
					<div align="left" style="background: #EFEFEF;">
					<input type="hidden" value="'. $merchant_id .'" name="merchant_id"/>
						<table class="normal tblDetails" width="100%" border="0" cellpadding="5" cellspacing="0">
							<thead><tr>
								<td width="80" align="center">#</td>
								<td align="center">'.lang('Bonus Description').'</td>
								<td align="center">'.lang('Group').'</td>
								<td align="center">'.lang('Min. FTDs').'</td>
								<td align="center">'.lang('Bonus Amount').'</td>
								<td align="center">'.lang('Time').'</td>
								<td align="center">'.lang('Active').'</td>
								<td align="center">'.lang('Actions').'</td>
								<td></td>
							</tr></thead><tfoot>'.$listBonuses.'<tr style="background: #D9D9D9;">
								<td></td>
								<td align="center"><input type="text" name="bonus_db[title]" value="" style="width: 250px;" /></td>
								<td align="center"><select name="bonus_db[group_id]" style="width: 150px;"><option value="99999">'.lang('All').'</option><option value="">'.lang('General').'</option>'.listGroups().'</select></td>
								<td align="center"><input type="text" name="bonus_db[min_ftd]" value="" maxlength="5" style="width: 100px; text-align: center;" /></td>
								<td align="center">'. $set->currency .'  <input type="text" name="bonus_db[bonus_amount]" value="" style="width: 100px; text-align: center;" maxlength="5" /></td>
								<td align="center"><input name="bonus_db[rdate]" class="rdate" class="date-picker" /></td>
								<td align="center"><input type="checkbox" name="bonus_db_valid" /> '.lang('Active').'</td>
								<td align="center"><input type="submit" value="'.lang('Save').'" /></td>
							</tfoot></tr>
						</table>
					</div>
					</form>
					</div>
			<style>
			.ui-datepicker-calendar {
					display: none;
			}
			</style>
			<link href="../css/redmond/jquery-ui.css" media="screen" rel="stylesheet" type="text/css" />
			<script>
			$("document").ready(function(){
						$(".rdate").datepicker({
							 changeMonth: true,
							changeYear: true,
							showButtonPanel: true,
							dateFormat: "MM yy",
							onClose: function(dateText, inst) { 
								$(this).datepicker("setDate", new Date(inst.selectedYear, inst.selectedMonth, 1));
							}
						});
										
			});
			</script>
			
					';
		
	
	
	
			


		// var_dump($_GET);
		// die();
			
		
		
		theme();
		
?>