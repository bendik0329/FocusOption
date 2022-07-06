<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';
require '../common/database.php';
require '../func/func_string.php';
require '../func/func_file.php';

if ( 
    $_POST['type'] == "product" && !empty($_POST["id"])
) {
	$select = "";
	
		
	
		$sql = "select id, title from merchants_creative where product_id=" . $_POST['id'] . " and valid = 1";
		$rsc = mysql_query($sql);
		if(isset($_POST['listpixels']))
			$options .= "<option value = 0>" . lang('All Creatives') . "</option>";
		else
			$options .= "<option value = ''>" . lang('Choose Creative') . "</option>";
		if($rsc){
			while($prodRow = mysql_fetch_assoc($rsc)){
				if($_POST['selected_type'] == "product")
				$select =   $_POST['selected_banner_id'] == $prodRow['id']?' selected':'' ;
				$options .= "<option value = " . $prodRow['id'] . $select .">" . $prodRow['title'] . "</option>";
			}
		}
    
	echo $options;
	exit;
	
}
else{
	
	$sql = "select id, title from merchants_creative where merchant_id=" . $_POST['id']. " and valid = 1";

		$rsc = mysql_query($sql);
		if(isset($_POST['listpixels']))
			$options .= "<option value = 0>" . lang('All Creatives') . "</option>";
		else
			$options .= "<option value = ''>" . lang('Choose Creative') . "</option>";
		if($rsc){
			while($merRow = mysql_fetch_assoc($rsc)){
				if($_POST['selected_type'] == "merchant")
				$select =   $_POST['selected_banner_id'] == $merRow['id']?' selected':'' ;
				$options .= "<option value = " . $merRow['id']  . $select .  ">" . $merRow['title'] . "</option>";
			}
		}
    
	echo $options;
	exit;
	
}
exit;
