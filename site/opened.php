<?php
require('common/global.php');

if ($_GET['trackingCode']) {
	$trackingCode = clearInjection($_GET['trackingCode']);
	function_mysql_query("UPDATE mail_sent SET opened=opened+1 WHERE trackingCode='".$trackingCode."' AND opened != '0'",__FILE__);
	function_mysql_query("UPDATE mail_sent SET opened=opened+1,opened_time='".dbDate()."' WHERE trackingCode='".$trackingCode."' AND opened='0'",__FILE__);
	}

header('Content-type: image/gif');

?>