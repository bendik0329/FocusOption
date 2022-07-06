<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

/*---------------------------------- [ Database Details ] ----------------------------------
$ss = new StdClass();
$ss->db_hostname 	= 	"localhost";
$ss->db_username	= 	"affbud1_demo";
$ss->db_password 	= 	"buddies1979";
$ss->db_name 		= 	"affbud1_demo";
*/

$ss = new StdClass();
$ss->db_hostname 	= 	"localhost";
$ss->db_username	= 	"rightcom_usr8";
$ss->db_password 	= 	"Andrey777";
$ss->db_name 		= 	"rightcom_db";

$con=mysql_connect($ss->db_hostname, $ss->db_username, $ss->db_password);
mysql_select_db($ss->db_name);

mysql_query("SET NAMES 'UTF8'",$con);

$setqq = mysql_query("SELECT * FROM settings WHERE id='1'");
$set = mysql_fetch_object($setqq);

$set->path_dir = '';

// -------------------------------------- [ FTP Details ] --------------------------------------

$set->ftp_server 	= 	'localhost';
$set->ftp_user 	= 	'aff@rightcommission.com';
$set->ftp_pass 	= 	'aff500';
$set->ftp_path 	= 	'/public_html/';

?>