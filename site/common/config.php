<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
############################## [ GLOBAL ] ##############################
	if ($_GET['set'] OR $_POST['set']) { unset($_GET['set']); unset($_POST['set']); }
	if ($_GET['baseURL']) $set->basepage = $_GET['baseURL'];
		else $set->basepage = $_SERVER['SCRIPT_NAME'];
	if ($_GET['pageURL']) $set->uri = $_GET['pageURL'];
		else $set->uri = $set->basepage.($_SERVER[QUERY_STRING] ? '?'.$_SERVER[QUERY_STRING] : '');
	$set->http_host = $_SERVER[HTTP_HOST];
	$set->webAddress = 'https://'.$set->http_host.'/'.$set->path_dir;
	$set->webAddressHttp = 'https://'.$set->http_host.'/'.$set->path_dir;
	$set->webAddressHttps = 'https://'.$set->http_host.'/'.$set->path_dir;
	if ($_SERVER[HTTPS]) $set->Https = 'on'; else $set->Https = 'off';
	$set->userIP = $_SERVER[REMOTE_ADDR];
	$set->refe = $_SERVER[HTTP_REFERER];
	$seoPage = explode("/",$_SERVER[REQUEST_URI]);
	if ($set->basepage == "seo.php") $set->seoPage = $seoPage[count($seoPage)-1];
	//$set->login_session_duration = 60; //in minutes
	//$set->login_session_duration = 60; //in minutes
	// $set->pnlTable = "data_sales"; //data_stats
	$set->AffiliateBuddiesVersion = "2.53";
	
	$set->disableRefreshCharts = false;
	$set->isHttps = true;//$_SERVER['REQUEST_SCHEME']=='https' ? true : false;
	
	

if (!isset($set->numberOfFailureLoginsAttempts) || empty($set->numberOfFailureLoginsAttempts))
	$set->numberOfFailureLoginsAttempts = 10;
	
	
	
		$SSLprefix  = "";
	$SSLswitch = "";
	if ($set->isHttps){
		$SSLprefix =  'https://'. $_SERVER['HTTP_HOST'] . "/";
		$SSLswitch= "s";
		
		
		$set->logoPath = str_replace('http://','https://'  , $set->logoPath);
	}
	$set->SSLswitch = $SSLswitch;
	$set->SSLprefix = $SSLprefix;
	
############################## [ GLOBAL ] ##############################

?>