<?php
require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);
switch ($act) {
	case "delete":
			$id = $design_id;
			$sql = "delete from design_layout where id=" . $id;
			function_mysql_query($sql ,__FILE__);		
			_goto($set->SSLprefix.$set->basepage);
			break;
	case "default":
			$id = $design_id;
			function_mysql_query("update design_layout set isDefault = 0",__FILE__);
			$sql = "update  design_layout set isDefault = 1 , created_by_admin_id=0 where id=" . $id;
			function_mysql_query($sql ,__FILE__);
			_goto($set->SSLprefix.$set->basepage);
			break;
	case "valid":
		$id = $design_id;
			
			$sql = "update design_layout  set active = $valid where id = $id";
			
			$result = function_mysql_query($sql ,__FILE__);
			
			_goto($set->SSLprefix.$set->basepage);
			
			break;
	case "duplicate":
			$id = $design_id;
			
			$sql = "select * from design_layout where id = $id";
		
			$result = function_mysql_query($sql ,__FILE__);
			
			$duplicateRow = mysql_fetch_assoc($result);
			
			unset($duplicateRow['id']);
			unset($duplicateRow['rdate']);
			$duplicateRow['created_by_admin_id'] = $set->userInfo['id'];
			$duplicateRow['isDefault'] = 0;
			$fields = implode(',',array_keys($duplicateRow));
			$values = implode(',', array_map(function($value) {
				if(!is_numeric($value)) {
					return '"' . $value . '"';
				} else {
					return $value;
				}
			}, array_values($duplicateRow)));
			
			$sql = "INSERT into design_layout ($fields) values ($values)";
			function_mysql_query($sql ,__FILE__);
			_goto($set->SSLprefix.$set->basepage);
			
			break;
	default:
		//$set->pageTitle = lang('Design Layouts');
		$set->breadcrumb_title =  lang('Design Layouts');
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.'admin/design_layouts.php" class="arrow-left">'.lang('Design Layouts').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		$sql = "SELECT * from design_layout order by id DESC";
		
		$results = function_mysql_query($sql,__FILE__);
		$set->content .= display_results($results);
				theme();
		break;
	}
	function display_results($results){
		
		if(!empty($results)){
			$listRecords = "<style>.editrow,.duplicaterow,.deleterow,.defaultrow{
				cursor:pointer;
			}</style>
			<script>
			$(document).ready(function(){
				
				$('.duplicaterow').click(function(e){
					e.preventDefault();
					$('#act').val('duplicate');
				
				});
				
				$('.defaultrow').click(function(e){
					e.preventDefault();
					$('#act').val('default');
				
				});
				
				$('.deleterow').click(function(){
					var id = $(this).data('id');
					if (confirm('Are you sure you want to delete?')) {
						window.location.href='".$set->SSLprefix."admin/design_layouts.php?act=delete&design_id='+id;
					}
				})
			});
			</script><form name='layout_form'><input type='hidden' name='act' id='act' value='update' />
					<input type='hidden' id='design_id' name='design_id' value=''>";
			$a = 0;
			while($ww=mysql_fetch_assoc($results)) {
				$valid = $ww['active']==1?0:1;
				$hrefValid =$set->SSLprefix. $set->basepage.'admin/design_layouts.php?act=valid&design_id='.$ww['id'].'&valid='. $valid;
				$deletebtn = $ww['id'] >1 && $ww['isDefault']==0?($ww['created_by_admin_id']>0?'<a class="deleterow" data-id='. $ww['id'] .'>'. lang('Delete') .'</a>&nbsp;|&nbsp;':''):'';
				$defaultbtn = $ww['isDefault'] ==0?'<a href="' . $set->SSLprefix.$set->basepage.'admin/design_layouts.php?act=default&design_id='. $ww['id'] .'">'. lang('Set as Default') .'</a>&nbsp;|&nbsp;':'';
				$listRecords .= '<tr '.($i % 2 ? 'class="trLine"' : '') .'id="'. $ww['id'] .'">
								<td align="center">'.$ww['id'].'</td>
								<td align="center">'.$ww['name'].'</select></td>
								<td align="center">'.$ww['rdate'].'</td>
								<td align="center" id="lng_'.$ww['id'].'"><a href="'.  $hrefValid .'" style="cursor: pointer;">'.xvPic($ww['active']).'</a>';
								
								$listRecords .='<td align="center"><a class="editrow" href="'.$set->SSLprefix.'admin/design_css.php?design='. $ww['id'] .'">'. lang('Edit') .'</a>&nbsp;|&nbsp;'. $deletebtn .   $defaultbtn.'<a href="'. $set->SSLprefix.$set->basepage .'admin/design_layouts.php?act=duplicate&design_id='. $ww['id'] .'" data-id='. $ww['id'] .'>'. lang('Duplicate') .'</a></td>
							</tr>';
				$i++;
			}
			
			$data .= "<div class='normalTableTitle'>".lang('Design Layouts')."</div><table class='normal' width='100%' border='0' cellpadding='3' cellspacing='0' style='border-left:1px dotted #ddd;'>";
			$data .= "<thead>
			<tr><td>". lang('Id') ."</td><td>". lang('Layout Name') ."</td><td>". lang('Date') ."</td><td>". lang('Valid') ."</td><td>". lang('Actions') ."</td></tr>
			</thead><tbody>". $listRecords;
				
				$data .= '</tbody></table></form>';
		}
	return $data;
	}
	
			
?>
