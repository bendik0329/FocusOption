<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');
$logintype = 'manager';

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/". $logintype."/";
if (!isManager()) _goto($lout);

$appTable = 'merchants_creative_categories';
$set->pageTitle = lang('Creative Categories');
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
		
		if ($id>0)
		{
		
			$qry = "delete from merchants_creative_categories where id = " . $id ;
			// die ($qry);
			mysql_query($qry) ; 
			_goto($set->SSLprefix.$set->basepage);
		}
	break;	
			
			
			
	case "add":
		//var_dump($_POST);die;
		
		$categoryname = $db['categoryname'];
		
		if (empty($categoryname))  $errors['id'] = 1;
		if (empty($errors)) {
			
			if (!isset($categoryname)) {
			$db[valid] = 0;
		}
		
			// $db['alt'] = $code;
			// $db['title'] = $code;
			// $db['type'] = 'coupon';
		if ($db['id']=='') { 
			// $db['rdate'] = date('Y-m-d H:i:s');
			// $db['valid'] = $db['language_id'] = $db['promotion_id'] = $db['admin_id'] = 1;
			// $db['width'] = $db['height']  = 0;
			// $db['file'] = $db['url']  = '.';
			
			$qq=mysql_query("SELECT * FROM merchants where valid = 1");
			$ww=mysql_fetch_assoc($qq);
			$db['merchant_id'] = $ww['id'];
			
		   $qry = "INSERT INTO merchants_creative_categories ( `merchant_id`, `categoryname`, `valid`) VALUES
				( '".$db['merchant_id'] ."','".$categoryname."', 1)";
				
			
		}
		else {
			if ($db['id']>0)
			$qry = "update merchants_creative_categories set categoryname='" . $categoryname . "'  , merchant_id ='" . $db['merchant_id'] . "'  where id = " . $db['id'];
			
		}
			mysql_query($qry);
			
			_goto($set->SSLprefix.$set->basepage);
	}
	
	
	
	
	
	default:
	
			$row = '';
			$qq=mysql_query("SELECT * from ". $appTable);
			$dataRows .= '';
		while ($ww=mysql_fetch_assoc($qq)) {
			
			if (!empty($id) && $id == $ww['id'])
				$row = $ww;
				
			
			$l++;
		//	$totalAffiliates=mysql_result(mysql_query("SELECT COUNT(id) FROM merchants_creative WHERE type='coupon' and id='".$ww['id']."'"),0);
			$dataRows .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
						' . ( '<td>'.$ww['id'].'</td>').'
						' . ( '<td align="center"><a href="'.$set->SSLprefix.$logintype.'/creativeCategories.php?act=edit&id='.$ww['id'].'" data-id="' . $ww['id'] . '" data-categoryname= "'. $ww['categoryname'] . '">'.lang('Edit').'</a><span>&nbsp;&nbsp;&nbsp;</span>' ) .'
						<a href="'.$set->SSLprefix.$logintype.'/creativeCategories.php?act=delete&id='.$ww['id'].'">'.lang('Delete').'</a></td>
						<td>'.$ww['categoryname'].'</td>
						
						
						
						' . ($excel ? : '' . '<td align="center" id="lng_'.$ww['id'].'"><a onclick="ajax(\''.$set->SSLprefix.$set->basepage.'?act=valid&id='.$ww['id'].'\',\'lng_'.$ww['id'].'\');" style="cursor: pointer;">'.xvPic($ww['valid']).'</a></td>').'
					</tr>';
			}
		//$totalAffiliates=mysql_result(mysql_query("SELECT COUNT(id) FROM affiliates WHERE status_id='0'"),0);
		
		
			
		
		$set->content = '<form method="post" >
						<input type="hidden" name="act" value="add" />
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Add New Creative Category').'</div>
						<table width="100%" border="0" cellpadding="0" cellspacing="5" bgcolor="#EFEFEF" id="tab_1" style="">
							<tr><td colspan="3" height="5"></td></tr>
							<tr><td align="left">'.lang('Category Name').':</td><td><input id="db_categoryname" type="text" name="db[categoryname]" value="'. (!empty($row['categoryname']) ? ($row['categoryname']) :   $db['categoryname']) .'" '.($errors['categoryname'] ? 'style="border: 1px red solid;"' : '').' /></td></tr>
							<tr><input id="id_hidden" type="hidden" name="db[id]" /></td></tr>
							<tr><td></td><td align="left"><input type="submit" value="'.lang('Save').'" /></td></tr>
							<tr><td colspan="3" height="5"></td></tr>
						</table>
						</form>
						<hr />';
					
/*					$set->content .=($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				
					</div><div style="clear:both"></div>' : '');*/
					
					$set->content .='</br>
						
						<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_1\').slideToggle(\'fast\');">'.lang('Creative Category List').'</div>
						<table class="normal" width="100%" border="0" cellpadding="2" cellspacing="0">
							<thead>
							<tr>
								<td>#</td>
								<td>'.lang('Actions').'</td>
								<td>'.lang('Category Name').'</td>
								<td>'.lang('Valid').'</td>
							</tr></thead><tfoot>'.$dataRows.'</tfoot>
						</table>
						<script>
						$("[data-id]").click(function() {
							$("#db_categoryname").val($(this).data("categoryname"));
							
							$("#id_hidden").val($(this).data("id"));
							return false;
						});
						</script>'.
						
					
						excelExporter($dataRows,'categoryname');
		theme();
		break;
	}

?>