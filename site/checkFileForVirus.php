<?php
require_once('common/database.php');
require_once('func/func_debug.php');
require_once('func/func_common.php');
require_once('func/func_user.php');

/*  if(checkUserFirewallIP("3.3.3.3"))
 {
	 echo "IP Exists";
 }
 else{
	 
	 echo "IP doesnot Exists";
 }
 die; */

//$arrTables = ['merchants_creative','sub_banners','products_items','settings'];
$arrTables = ['merchants_creative','sub_banners','products_items','documents','settings','admins'];
//$arrTables = ['products_items'];



foreach($arrTables as $k=>$table){

if($table == "settings"){
	$sql = "select * from ". $table ." where terms_link LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'terms_link');
	
	//merchants_terms_link
	$sql = "select * from ". $table ." where merchants_terms_link LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'merchants_terms_link');
	
	//affiliateLoginImage
	$sql = "select * from ". $table ." where affiliateLoginImage LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'affiliateLoginImage');
	
	//adminLoginImage
	$sql = "select * from ". $table ." where adminLoginImage LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'adminLoginImage');
	
	//adminLoginImage
	$sql = "select * from ". $table ." where adminLoginImage LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'adminLoginImage');
	
	//logoPath
	$sql = "select * from ". $table ." where logoPath LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'logoPath');
	
	//billingLogoPath
	$sql = "select * from ". $table ." where billingLogoPath LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'billingLogoPath');
	
	//faviconPath
	$sql = "select * from ". $table ." where faviconPath LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'faviconPath');
	
	//secondaryPoweredByLogo
	$sql = "select * from ". $table ." where secondaryPoweredByLogo LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'secondaryPoweredByLogo');
	
}	
if($table == "admins"){
		$sql = "select * from ". $table ." where bigPic LIKE '%/tmp/%'";
		echo $sql . "<br/>";	
		$qq = function_mysql_query($sql,__FILE__);
		processBanners($qq,$table,'bigPic');
}

if($table == "documents"){
		$sql = "select * from ". $table ." where path LIKE '%/tmp/%'";
		echo $sql . "<br/>";	
		$qq = function_mysql_query($sql,__FILE__);
		processBanners($qq,$table,'path');
}
else if($table=="merchants_creative" || $table == "products_items" || $table=="sub_banners"){
	$sql = "select * from ". $table ." where " . ($table == "products_items"?'image':'file') . " LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	if($table == "products_items")
		processBanners($qq,$table,'image');
	else
		processBanners($qq,$table);
	
}

if($table == "products_items"){
	$sql = "select * from ". $table ." where terms_and_conditions LIKE '%/tmp/%'";
	echo $sql . "<br/>";
	$qq = function_mysql_query($sql,__FILE__);
	processBanners($qq,$table,'terms_and_conditions');
}


}

/* 
$sql2 = "select * from sub_banners where file LIKE '%/tmp/%'";
$qq2 = function_mysql_query($sql2,__FILE__);

processBanners($qq2);


$sql2 = "select * from sub_banners where file LIKE '%/tmp/%'";
$qq2 = function_mysql_query($sql2,__FILE__);

processBanners($qq2);
 */

// $data = json_decode(file_get_contents("http://exchanger.affiliatets.com/getBPs.php"));
$data = json_decode(file_get_contents("http://exchanger.affiliatets.com/getBPs.json"));
if($data !=""){
	foreach($data as $key=>$row){
		if(!checkUserFirewallIP($row->ip))
		{
			$sql = "select id from users_firewall where IPs = '". $row->ip . "' ";
			$exists = mysql_fetch_assoc(mysql_query($sql));
			if (!isset($exists['id'])){
			$sql = "insert into users_firewall (IPs, valid,comment) values('". $row->ip ."',". $row->valid . ", '" . $row->reason . "') ";
			mysql_query($sql);
			}
		}
	}
}
 
function processBanners ($resource,$table,$field = ""){
	global $set;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.virustotal.com/vtapi/v2/file/scan');
	curl_setopt($ch, CURLOPT_POST, True);
	curl_setopt($ch, CURLOPT_VERBOSE, 1); // remove this if your not debugging
	curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); // please compress data
	curl_setopt($ch, CURLOPT_USERAGENT, "gzip, My php curl client");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER ,True);
	while($ww = mysql_fetch_assoc($resource)){

	/* $dir = "files/banners/tmp";
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if($file != "." && $file != "..")
				{
	 */				
					$dbfile = $field !=""?$ww[$field] : $ww['file'];
					
					echo "<br/>" .$ww[$field] . "</br>";
					//handling domain name in terms and merchants term link (because in this case realpath will be empty and virus api will not work)
					
			if($table == "settings" && ($field=='merchants_terms_link' || $field == 'terms_link') || ($table == "products_items" && $field == "terms_and_conditions")){
						
						$arrterms = explode("/",$dbfile);
						unset($arrterms[0]);
						unset($arrterms[1]);
						unset($arrterms[2]);
						$dbfile =  implode("/",$arrterms);
					}
					else if ($table == "documents" || $table == "admins"){
						$arrterms = explode("/",$dbfile);
						unset($arrterms[0]);
						$dbfile =  implode("/",$arrterms);
					}
					echo $dbfile . "<br/>";
					$file_name_with_full_path = realpath($dbfile); 
					echo $file_name_with_full_path . "<br/>";
					$arrFile = explode("/",$dbfile);
					$removeFolder = $arrFile;
					if($table == "documents"  || $table == 'products_items'){
						/* unset($removeFolder[count($arrFile)-1]);
						$removeFolderName = implode("/",$removeFolder); */
						unset($removeFolder[count($arrFile)-1]);
						unset($removeFolder[count($arrFile)-2]);
						$removeFolderName = implode("/",$removeFolder);
						
					}
					else{
						unset($removeFolder[count($arrFile)-1]);
						$removeFolderName = implode("/",$removeFolder);
					}
					$filename = $arrFile[count($arrFile)-1];

					$api_key = '81660080d26ffa8db3f304a09e52d39c5e01fc040af59f0451edec0f749eecdb';

					$cfile = curl_file_create($file_name_with_full_path);
					 
					$post = array('apikey' => $api_key,'file'=> $cfile);
					
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
					 
					$result=curl_exec ($ch);
					$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
					
					if ($status_code == 200) { // OK
					  $js = json_decode($result, true);
					  if($js['response_code'] == 1){
						  echo $file . "<br/>";
						
						 if($table == "admins")
						 {
							 rename  ( $file_name_with_full_path , "files/managers/" . $filename);
							$sql = "update admins set bigPic = '". $set->webAddress ."files/managers/" . $filename . "' where id=" . $ww['id'];
						 }
						 
						 if($table == "settings" && $field == "terms_link")
						 {
							 rename  ( $file_name_with_full_path , "files/" . $filename);
							$sql = "update settings set terms_link = '". $set->webAddress ."files/" . $filename . "'";
						 }
						 else if($table == "settings" && $field == "merchants_terms_link"){
							rename  ( $file_name_with_full_path , "files/" . $filename);
							$sql = "update settings set merchants_terms_link = '". $set->webAddress ."files/" . $filename . "'";
						 }
						 else if($table == "settings" && $field == "affiliateLoginImage"){
							rename  ( $file_name_with_full_path , "files/design/" . $filename);
							$sql = "update settings set affiliateLoginImage= '". $set->webAddress ."files/design/" . $filename . "'";
						 }
						 else if($table == "settings" && $field == "adminLoginImage"){
							rename  ( $file_name_with_full_path , "files/design/" . $filename);
							$sql = "update settings set adminLoginImage= '". $set->webAddress ."files/design/" . $filename . "'";
						 }
						 else if($table == "settings" && $field == "logoPath"){
							rename  ( $file_name_with_full_path , "files/design/" . $filename);
							$sql = "update settings set logoPath= '". $set->webAddress ."files/design/" . $filename . "'";
						 }
						 else if($table == "settings" && $field == "secondaryPoweredByLogo"){
							rename  ( $file_name_with_full_path , "files/design/" . $filename);
							$sql = "update settings set secondaryPoweredByLogo= '". $set->webAddress ."files/design/" . $filename . "'";
						 }
						 else if($table == "settings" && $field == "billingLogoPath"){
							rename  ( $file_name_with_full_path , "files/design/" . $filename);
							$sql = "update settings set billingLogoPath= '". $set->webAddress ."files/design/" . $filename . "'";
						 }
						 else if($table == "settings" && $field == "faviconPath"){
							rename  ( $file_name_with_full_path , "files/design/" . $filename);
							$sql = "update settings set faviconPath= '". $set->webAddress ."files/design/" . $filename . "'";
						 }
						 if($table == "merchants_creative" && strpos($file_name_with_full_path,'/products/')){
							 rename  ( $file_name_with_full_path , "files/products/" . $filename);
							  $sql = "update merchants_creative set file = 'files/products/" . $filename . "' where id=" . $ww['id'];
						 }
						 if($table == "merchants_creative" && strpos($file_name_with_full_path,'/banners/')){
							 rename  ( $file_name_with_full_path , "files/banners/" . $filename);
							  $sql = "update merchants_creative set file = 'files/banners/" . $filename . "' where id=" . $ww['id'];
						 }
						  if($table == "products_items" && strpos($file_name_with_full_path,'/products/') && $field == "image"){
							
							  rename  ( $file_name_with_full_path , "files/products/" . $filename);
							  $sql = "update products_items set image = 'files/products/" . $filename . "' where id=" . $ww['id'];
						  }
						  //products_items - Terms and Conditions
						  if ($table =="products_items" && $field == 'terms_and_conditions'){
							  rename  ( $file_name_with_full_path , "files/products/terms/" . $filename);
							$sql = "update products_items set terms_and_conditions= '". $set->webAddress ."files/products/terms/" . $filename . "' where id=" . $ww['id'];
						  }
							 
						  if(strpos($file_name_with_full_path,'/sub_banners/')){
							  rename  ( $file_name_with_full_path , "files/sub_banners/" . $filename);
							  $sql = "update sub_banners set file = 'files/sub_banners/" . $filename . "' where id=" . $ww['id'];
						  }
						  elseif (strpos($file_name_with_full_path,'/sub_banners/')){
							  rename  ( $file_name_with_full_path , "files/banners/" . $filename);
							  $sql = "update merchants_creative set file = 'files/banners/" . $filename . "' where id=" . $ww['id'];
						  }
						  elseif (strpos($file_name_with_full_path,'/agreements/')){
							  $folder = "files/agreements/" . $ww['affiliate_id'];
							  if (!is_dir($folder)) {
								 mkdir($folder);
							 }
							  rename  ( $file_name_with_full_path , "files/agreements/" . $ww['affiliate_id'] ."/" . $filename);
							  $sql = "update documents set path = '../files/agreements/"  . $ww['affiliate_id']."/" .$filename . "' where id=" . $ww['id'];
						  }
						  elseif (strpos($file_name_with_full_path,'/documents/')){
							$folder = "files/documents/" . $ww['affiliate_id'];
							  if (!is_dir($folder)) {
								 mkdir($folder);
							 }
							  rename  ( $file_name_with_full_path , "files/documents/" . $ww['affiliate_id'] ."/" . $filename);
							  $sql = "update documents set path = '../files/documents/"  . $ww['affiliate_id']."/" .$filename . "' where id=" . $ww['id'];
						  } 
						  elseif (strpos($file_name_with_full_path,'/invoices/')){
							    $folder = "files/invoices/" . $ww['affiliate_id'];
							  if (!is_dir($folder)) {
								 mkdir($folder);
							 }
							  rename  ( $file_name_with_full_path , "files/invoices/" . $ww['affiliate_id'] ."/" . $filename);
							  $sql = "update documents set path = '../files/invoices/"  . $ww['affiliate_id']."/" .$filename . "' where id=" . $ww['id'];
						  }
						  
						  function_mysql_query($sql);
						   if($table =="merchants_creative"){
							$sql = "update merchants_creative set affiliateReady = 1 where id=" . $ww['id'];
							function_mysql_query($sql);
							}
						  //unlink($file_name_with_full_path);
						  RemoveEmptySubFolders($removeFolderName);
					  }
					  else if($js['response_code']==-2){
							//function_mysql_query("update merchants_creative set valid = -1 and file = 'images/banner_block.jpg' where id=" . $ww['id']);
							echo $file . " is under process";
					  }
					  else if($js['response_code'] == 0){
						  if($table =="merchants_creative")
							function_mysql_query("update merchants_creative set valid = -1 and file = 'images/banner_block.jpg' where id=" . $ww['id']);
						  if($table =="products_items")
							function_mysql_query("update products_items set valid = -1 and image = 'images/banner_block.jpg' where id=" . $ww['id']);
						 if($table == "merchants_creative" && strpos($file_name_with_full_path,'/products/'))
							 function_mysql_query("update merchants_creative set valid = -1 and file = 'images/banner_block.jpg' where id=" . $ww['id']);
						if($table =="settings" ){
							function_mysql_query("update settings set ". $field ." = '';");
						}
						if($table =="documents")
							function_mysql_query("update documents set path = 'images/banner_block.jpg' where id=" . $ww['id'] );
						if($table =="sub_banners")
							function_mysql_query("update sub_banners set file = 'images/banner_block.jpg' where id=" . $ww['id'] );
						  if($table =="admins")
							function_mysql_query("update admins set bigPic= 'images/banner_block.jpg' where id=" . $ww['id'] );
						  
							unlink($file_name_with_full_path);
							RemoveEmptySubFolders($removeFolderName);
					  }
					 
					  
					} else {  // Error occured
					  print($result);
					}
				/* }
			
			}
			closedir($dh);
		} */
	}
		curl_close ($ch);
}



	
function RemoveEmptySubFolders($path)
{
  $empty=true;
  foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
  {
     $empty &= is_dir($file) && RemoveEmptySubFolders($file);
  }
  return $empty && rmdir($path);
}