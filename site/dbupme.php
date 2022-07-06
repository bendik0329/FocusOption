<?php
require_once('common/database.php');
if (!isset($_GET['doit']))
		die();

function get_data($url) {
/* 	$ch = curl_init();
	$timeout = 5;
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	$data = curl_exec($ch);
	curl_close($ch);
	return $data; */
	
	
	//extract data from the post
//set POST variables
// $url = 'http://domain.com/get-post.php';
$fields = array(
	/* 'lname' => urlencode($_POST['last_name']),
	'fname' => urlencode($_POST['first_name']),
	'title' => urlencode($_POST['title']),
	'company' => urlencode($_POST['institution']),
	'age' => urlencode($_POST['age']),
	'email' => urlencode($_POST['email']),
	'phone' => urlencode($_POST['phone']) */
	'a' => "1"
);

//url-ify the data for the POST
foreach($fields as $key=>$value) 
{ $fields_string .= $key.'='.$value.'&'; }
rtrim($fields_string, '&');

//open connection
$ch = curl_init();

//set the url, number of POST vars, POST data
curl_setopt($ch,CURLOPT_URL, $url);
curl_setopt($ch,CURLOPT_POST, count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

//execute post
$result = curl_exec($ch);
//close connection
curl_close($ch);
return $result;


}
function runQuery($qry=""){
	// die (':::  ' . $qry);
	$res = mysql_query($qry);
if ($res) {
	unset ($res);
	return '- ok';
} else {
  /* output an error if desired... or don't */
	unset ($res);
  return 'There an error: '. mysql_error();
}
}

echo 'Starting to update<br>';
$string = get_data("http://exchanger.affiliatets.com/qryUpdates1110.txt");
// var_dump($string);
// die('---------------------');
$exp = explode(';',$string);
$counter=1;

foreach($exp as $expItem) {
	// var_dump($expItem);
	// echo '<Br>';
	$p = trim($expItem);
  if (!empty($p)) {
	echo '<br><span style="font-size:16px;color:black;">'.$counter . ' Qry: ' . $expItem . ';</span><br>';
	$newQry = $expItem ;
	// die ($newQry);
	$a = runQuery($newQry);
	
	if ($a =='- ok') {
		echo  '<span style="font-size:20px;font-weight:bold;color:green;">'.$a. '</span><br>';
	}
	else
		echo  '<span style="font-size:20px;font-weight:bold;color:red;">'.$a. '</span><br>';
	
	
	$counter++;
} 
}
die('<br>DONE');

?>