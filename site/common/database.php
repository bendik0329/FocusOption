<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

/*---------------------------------- [ Database Details ] ----------------------------------
*/

$ss = new StdClass();
// $ss->db_hostname 	= 	"affiliatetsdb";
// $ss->db_username	= 	"root";
// $ss->db_password 	= 	"root";
// $ss->db_name 		= 	"affiliatets";

$ss->db_hostname    =  "207.246.242.156";
$ss->db_username   =  "445094_devsite";
$ss->db_password   =  "gf638j@hfjY";
$ss->db_name      =  "445094_devsite";


$con=@mysql_connect($ss->db_hostname, $ss->db_username, $ss->db_password);
mysql_select_db($ss->db_name);

mysql_query("SET NAMES 'UTF8'",$con);

$setqq = mysql_query("SELECT * FROM settings WHERE id='1'");
$set = mysql_fetch_object($setqq);

$set->path_dir = '';

// -------------------------------------- [ FTP Details ] --------------------------------------

$set->ftp_server 	= 	'localhost';
$set->ftp_user 	= 	'';
$set->ftp_pass 	= 	'';
$set->ftp_path 	= 	'/public_html/';

?>
