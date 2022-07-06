<?php
require('common/global.php');
$a = clearInjection($_GET['a']);
$i = clearInjection($_GET['i']);
$w = clearInjection($_GET['w']);
if ($action == "refresh") {
	
	
	function_mysql_query("DELETE FROM chart_data WHERE level='".$a."' AND member_id='".$i."'",__FILE__);
	function_mysql_query("DELETE FROM chart_data WHERE fulldate <= '".date("Y-m",strtotime("-1 Year"))."-01'",__FILE__);
	}
echo chart('0',$a,$i,$w);
exit;

?>