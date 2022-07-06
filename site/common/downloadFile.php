<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

$filename = $_GET['filename'];

$name = basename($filename);

$ext = pathinfo($filename, PATHINFO_EXTENSION);

$result = in_array($ext,['csv','xls','jpg','png','jpeg']);

if($result != true){
    die('Error Code: 911');
}

header("Content-Length: " . @filesize($filename));
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename=' . $name);

readfile($filename);

if(isset($_GET['unlinkfile'])){
    unlink($filename);
}
exit;

?>