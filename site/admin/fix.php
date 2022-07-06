<?php


require_once('common/global.php');

$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);

					

		//$set->pageTitle = lang('Maintenance');
		$set->breadcrumb_title = lang('Maintenance');
		$set->pageTitle = $set->breadcrumb_title;
	
	
		mysql_query("TRUNCATE TABLE  `fieldsSortOrder`;");
	
		
		$set->content.="Done";
		theme();
		break;
	

?>