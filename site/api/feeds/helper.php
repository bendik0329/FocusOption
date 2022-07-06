<?



	function getip($ip=null,$debug=false){
  if(empty($ip))
  {
   $ip = '';
	   if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
		   $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
	}
	
	if (empty($ip)){
		if (isset($_SERVER))  {
			   if (!empty($_SERVER['HTTP_CLIENT_IP']))
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			   else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			   else if (!empty($_SERVER['HTTP_X_FORWARDED']))
				$ip = $_SERVER['HTTP_X_FORWARDED'];
			   else if (!empty($_SERVER['HTTP_FORWARDED_FOR']))
				$ip = $_SERVER['HTTP_FORWARDED_FOR'];
			   else if (!empty($_SERVER['HTTP_FORWARDED']))
				$ip = $_SERVER['HTTP_FORWARDED'];
			   else if (!empty($_SERVER['REMOTE_ADDR']))
				$ip = $_SERVER['REMOTE_ADDR'];
			   else
				$ip = '';
	   }
	   else {
		   if (getenv('HTTP_X_FORWARDED_FOR'))
			return getenv('HTTP_X_FORWARDED_FOR');

		if (getenv('HTTP_CLIENT_IP'))
			return getenv('HTTP_CLIENT_IP');

		return getenv('REMOTE_ADDR');
	   }
	}
  }
  return $ip;
 }
	
	

	
	
function getipold(){
	$ip = "";	
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
	
}
return $ip;
	
}
//function to check IPs
function checkIPInList($ip,$IPslist){
	
	$arrIPs = explode("|",$IPslist);
	
	$chk = in_array($ip,$arrIPs);
	
	if($chk){
		return true;
	}
	return false;
	
}