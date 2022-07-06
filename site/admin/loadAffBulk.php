<?php

require_once('common/global.php');

if (!isAdmin()) _goto('/admin/');

echo '<div style="font-family:ARIAL; font-size:12px">';
$file = file_get_contents('admin/affBulk.csv');

$rows = str_getcsv($file, "\n");
for($i=1;$i<count($rows);$i++){
	$row = $rows[$i];
	$row = explode(",",$row);

	$group = mysql_fetch_assoc(function_mysql_query('SELECT id FROM groups WHERE title="'.$row[6].'"',__FILE__));

	if(!$group['id']){
		function_mysql_query('INSERT INTO groups (rdate,valid,title) VALUES (NOW(),1,"'.$row[6].'")',__FILE__);
		echo '<BR>Adding new group: '.$row[6].'<BR>';
		$group = mysql_fetch_assoc(function_mysql_query('SELECT id FROM groups WHERE title="'.$row[6].'"',__FILE__));
		if(!$group['id']){
			echo 'Error with group';
			continue;
		}
	}

	function_mysql_query('INSERT INTO affiliates (rdate,mail,id,username,first_name,last_name,website,group_id,password) VALUES (NOW(),"'.$row[0].'",'.$row[1].',"'.$row[2].'","'.$row[3].'","'.$row[4].'","'.$row[5].'",'.$group['id'].',"'.md5('123456').'") ON DUPLICATE KEY UPDATE group_id='.$group['id'],__FILE__);
	echo 'Handeling affiliate: '.$row[2].'<BR><BR>';
	
}

echo '</div>';
mysql_close();
//echo '<BR><BR><BR>'.$file;


?>