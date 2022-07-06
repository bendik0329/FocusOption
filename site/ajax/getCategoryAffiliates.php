<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';
require '../func/func_string.php';
$category = $_GET['category'];
$group = (isset($_GET['group']) || $_GET['group']!=""?$_GET['group']:'');

$html = "";
$qq=mysql_query("SELECT id,username FROM affiliates WHERE valid='1' ".($category!="" ? " AND status_id = " . $category :"") .($group!="" ? " AND group_id = " . $group :"") . " ORDER BY username ASC");
$html .= '<option>'. lang('All Affiliates') .'</option>';
while ($ww=mysql_fetch_assoc($qq)) 
	$html .= '<option value="'.$ww['id'].'">'.$ww['username'].' [Site ID: '.$ww['id'].']</option>';

echo $html;
die;
