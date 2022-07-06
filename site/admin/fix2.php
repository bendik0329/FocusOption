<?php


require_once('common/global.php');

if (!isAdmin()) _goto('/admin/');

$q = "delete from `chart_data`  where level='manager'";
 mysql_query($q);

 die ('done');
 
$q = "delete from translate where id > 1321";
 mysql_query($q);

 

 
		$qr = "select * from affiliates where group_id>0 ";
$rsc = mysql_query($qr);
while ($row = mysql_fetch_assoc($rsc))		
		{
	$affiliate_id = $row['id'];
	$group_id = $row['group_id'];
	
		function_mysql_query("UPDATE data_reg SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
		function_mysql_query("UPDATE data_sales SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
		function_mysql_query("UPDATE data_sales_pending SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
		function_mysql_query("UPDATE data_stats SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	function_mysql_query("UPDATE affiliates SET group_id='".$group_id."' WHERE id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	function_mysql_query("UPDATE affiliates_notes SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	
	// function_mysql_query("UPDATE stats_banners SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	function_mysql_query("UPDATE traffic SET group_id='".$group_id."' WHERE affiliate_id='".$affiliate_id."'",__FILE__,__FUNCTION__);
	
		}
	
	
		$set->breadcrumb_title = lang('Maintenance');
		$set->pageTitle = $set->breadcrumb_title;
	
	
		// mysql_query("TRUNCATE TABLE  `fieldsSortOrder`;");
	
		
		$set->content.="Done";
		theme();
		break;
	

?>