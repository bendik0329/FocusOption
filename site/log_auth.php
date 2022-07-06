<?php

	// NXCS2 [Encode in UTF-8 Without BOM] [☺]

	$V = array();
	$V['autherror'] = "Error";
	$V['welcome'] = "Affiliate Buddies - Affiliate Software License";
	$userAdmin = array();
	$userAdmin['login'] = 'wise';
	$userAdmin['pass'] = 'group';
	function authenticate() {
		global $V;

		header('WWW-Authenticate: Basic realm="'.str_replace(" ", "", $V["welcome"]).'"');
		header('HTTP/1.0 401 Unauthorized');
		print '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8"/> 
	<title>Authentication Required</title>
	<style type="text/css">
		* {
			font-size: 12px;
			font-family: Tahoma;
			line-height: 18px;
		}
	</style>
</head>
<body dir="ltr">
	<div align="center" style="padding-top: 9%;">
		<table><tr>
			<td align="left"><img border="0" src="http://www.wisegroup.co.il/images/auth/auto.jpg" alt="" /></td>
			<td align="left">
				<div style="padding: 10px; background: #0072C6; text-align: left;"><a href="/"><img border="0" src="http://www.wisegroup.co.il/images/auth/logo.png" alt="" /></a></div><br />
				401 - Unauthorized: Access is denied due to invalid credentials.<br />
				<b>You do not have permission to view this directory or page using the credentials that you supplied.</b><br /><br />
				<u>Your IP has been logged and reported:</u> <b>'.$_SERVER['REMOTE_ADDR'].'</b> ['.date("d/m/Y H:i:s").']<br /><br />
				<a href="/">« back to home page</a>
			</td>
		</tr></table>
	</div>
</body>
</html>';
		exit;
	}

	if ((!(isset($_SERVER['PHP_AUTH_USER']))) || (($_POST['SeenBefore'] == 1) && ($_POST['OldAuth'] == $_SERVER['PHP_AUTH_USER'])))
		authenticate();
	else if ($_SERVER['PHP_AUTH_USER'] != $userAdmin["login"] OR $_SERVER['PHP_AUTH_PW'] != $userAdmin["pass"])
		authenticate();

?>