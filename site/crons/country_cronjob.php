<?php
set_time_limit(0);
header("Pragma: no-cache");
header("Expires: 0");
ini_set("memory_limit","512M");
// require_once('../common/global.php');
// chdir('../');
// require_once(__DIR__ .'/../common/global.php');
require_once(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');


//ini_set('error_reporting', E_ALL);
//ini_set('display_errors', '1');

$debug = isset($_GET['debug']) ? $_GET['debug'] : 0;

// $siteURL = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
// $siteURL = substr($siteURL, 0, strrpos( $siteURL, '/'));
 
/* $tmpName = str_replace('/cronjob.php','',$set->sitebaseurl)."/";
if (strpos(strtolower($tmpName), strtolower($set->http_host))>0)
	$siteURL = str_replace('/cronjob.php','',$set->sitebaseurl)."/" ;
else
 $siteURL = $set->http_host.str_replace('/cronjob.php','',$set->sitebaseurl)."/" ;
	 */
$merchant_id=isset($_GET['merchantid']) ? $_GET['merchantid'] :( isset($_GET['merchant_id']) ? $_GET['merchant_id'] : "") ;

function getTag($tag, $endtag, $xml) {
	if (!$endtag) $endtag=$tag;
	preg_match_all("/".$tag."(.*?)".$endtag."/", $xml, $matches);
	if (isset($matches[1][0])) return $matches[1][0];
	}
function concatUrlParts($firstPart, $secondPart) {
	global $debug;
	if ($debug>0)
		echo 'Starting concat<br>';
	// if (strpos($firstPart,'?')>0) {
	if(	strpos($secondPart,'?')>0) {
	
	if ($debug>0)
	echo 'End concat<br>';
	return $firstPart .   $secondPart;
	}
	
	else 	if (strpos($firstPart,'?')>0 ) {
		if ($debug>0)
	echo 'End concat<br>';
			return $firstPart . '&'.  $secondPart;
	}
	else {
		if ($debug>0)
	echo 'End concat<br>';
	return $firstPart . '?'.  $secondPart;
	}
	die ('Error in concat<br>');
}

$find = Array("\n","\t");
$replace = Array("","");
echo '<br>Starting time' . date("h:i:s") . '<br>';
$countries2isoJson = '{"Bangladesh":"BD","Belgium":"BE","Burkina Faso":"BF","Bulgaria":"BG","Bosnia and Herzegovina":"BA","Barbados":"BB","Wallis and Futuna":"WF","Saint Barthelemy":"BL","Bermuda":"BM","Brunei":"BN","Brunei Darussalam":"BN","Bolivia":"BO","Bahrain":"BH","Burundi":"BI","Benin":"BJ","Bhutan":"BT","Jamaica":"JM","Bouvet Island":"BV","Botswana":"BW","Samoa":"WS","Bonaire, Saint Eustatius and Saba ":"BQ","Brazil":"BR","Bahamas":"BS","Jersey":"JE","Belarus":"BY","Belize":"BZ","Russia":"RU","Rwanda":"RW","Serbia":"RS","East Timor":"TL","Reunion":"RE","Turkmenistan":"TM","Tajikistan":"TJ","Romania":"RO","Tokelau":"TK","Guinea-Bissau":"GW","Guam":"GU","Guatemala":"GT","South Georgia and the South Sandwich Islands":"GS","Greece":"GR","Equatorial Guinea":"GQ","Guadeloupe":"GP","Japan":"JP","Guyana":"GY","Guernsey":"GG","French Guiana":"GF","Georgia":"GE","Grenada":"GD","United Kingdom":"GB","Gabon":"GA","El Salvador":"SV","Guinea":"GN","Gambia":"GM","Greenland":"GL","Gibraltar":"GI","Ghana":"GH","Oman":"OM","Tunisia":"TN","Jordan":"JO","Croatia":"HR","Haiti":"HT","Hungary":"HU","Hong Kong":"HK","Honduras":"HN","Heard Island and McDonald Islands":"HM","Venezuela":"VE","Puerto Rico":"PR","Palestinian Territory":"PS","Palau":"PW","Portugal":"PT","Svalbard and Jan Mayen":"SJ","Paraguay":"PY","Iraq":"IQ","Panama":"PA","French Polynesia":"PF","Papua New Guinea":"PG","Peru":"PE","Pakistan":"PK","Philippines":"PH","Pitcairn":"PN","Poland":"PL","Saint Pierre and Miquelon":"PM","Zambia":"ZM","Western Sahara":"EH","Estonia":"EE","Egypt":"EG","South Africa":"ZA","Ecuador":"EC","Italy":"IT","Vietnam":"VN","viet nam":"VN","Solomon Islands":"SB","Ethiopia":"ET","Somalia":"SO","Zimbabwe":"ZW","Saudi Arabia":"SA","Spain":"ES","Eritrea":"ER","Montenegro":"ME","Moldova, Republic of":"MD","Moldova  Republic of":"MD","Moldova":"MD","Madagascar":"MG","Saint Martin":"MF","Morocco":"MA","Monaco":"MC","Uzbekistan":"UZ","Myanmar":"MM","Mali":"ML","Macao":"MO","Mongolia":"MN","Marshall Islands":"MH","Macedonia  the Former Yugoslav Republic of":"MK","Macedonia":"MK","Mauritius":"MU","Malta":"MT","Malawi":"MW","Maldives":"MV","Falkland Islands (Malvinas)":"MV","Martinique":"MQ","Northern Mariana Islands":"MP","Montserrat":"MS","Mauritania":"MR","Isle of Man":"IM","Uganda":"UG","Tanzania":"TZ","Tanzania  United Republic of":"TZ","Malaysia":"MY","Mexico":"MX","Israel":"IL","France":"FR","British Indian Ocean Territory":"IO","Saint Helena":"SH","Finland":"FI","Fiji":"FJ","Falkland Islands":"FK","Micronesia  Federated States of":"FM","Micronesia":"FM","Faroe Islands":"FO","Nicaragua":"NI","Netherlands Antilles":"NL","Netherlands":"NL","Norway":"NO","Namibia":"NA","Vanuatu":"VU","New Caledonia":"NC","Niger":"NE","Norfolk Island":"NF","Nigeria":"NG","New Zealand":"NZ","Nepal":"NP","Nauru":"NR","Niue":"NU","Cook Islands":"CK","Kosovo":"XK","Cote D\'Ivoire":"CI","Ivory Coast":"CI","Switzerland":"CH","Colombia":"CO","China":"CN","Cameroon":"CM","Chile":"CL","Cocos Islands":"CC","Canada":"CA","Congo":"CG","Republic of the Congo":"CG","Central African Republic":"CF","Democratic Republic of the Congo":"CD","Czech Republic":"CZ","Cyprus":"CY","Christmas Island":"CX","Costa Rica":"CR","Curacao":"CW","Cape Verde":"CV","Cuba":"CU","Swaziland":"SZ","Syrian Arab Republish":"SY","Syria":"SY","Sint Maarten":"SX","Kyrgyzstan":"KG","Kenya":"KE","South Sudan":"SS","Suriname":"SR","Kiribati":"KI","Cambodia":"KH","Saint Kitts and Nevis":"KN","Comoros":"KM","Sao Tome and Principe":"ST","Slovakia":"SK","South Korea":"KR","Slovenia":"SI","North Korea":"KP","Korea  Republic of":"KP","Kuwait":"KW","Senegal":"SN","San Marino":"SM","Sierra Leone":"SL","Seychelles":"SC","Kazakhstan":"KZ","Cayman Islands":"KY","Singapore":"SG","Sweden":"SE","Sudan":"SD","Dominican Republic":"DO","Dominica":"DM","Djibouti":"DJ","Denmark":"DK","virgin islands, british":"VG","Virgin Islands  British":"VG","British Virgin Islands":"VG","Germany":"DE","Yemen":"YE","Algeria":"DZ","United States":"US","Uruguay":"UY","Mayotte":"YT","United States Minor Outlying Islands":"UM","Lebanon":"LB","Saint Lucia":"LC","Lao People\'s Democratic Republic":"LA","Laos":"LA","Tuvalu":"TV","Taiwan  Province of China":"TW","Taiwan":"TW","Taiwan, Province of China":"TW","Trinidad and Tobago":"TT","Turkey":"TR","Sri Lanka":"LK","Liechtenstein":"LI","Latvia":"LV","Tonga":"TO","Lithuania":"LT","Luxembourg":"LU","Liberia":"LR","Lesotho":"LS","Thailand":"TH","French Southern Territories":"TF","Togo":"TG","Chad":"TD","Turks and Caicos Islands":"TC","Libya":"LY","Libyan Arab Jamahiriya":"LY","Vatican":"VA","Saint Vincent and the Grenadines":"VC","United Arab Emirates":"AE","Andorra":"AD","Antigua and Barbuda":"AG","Afghanistan":"AF","Anguilla":"AI","U.S. Virgin Islands":"VI","Virgin Islands  U.s.":"VI","Iceland":"IS","Iran  Islamic Republic of":"IR","Iran":"IR","Armenia":"AM","Albania":"AL","Angola":"AO","Antarctica":"AQ","American Samoa":"AS","Argentina":"AR","Australia":"AU","Austria":"AT","Aruba":"AW","India":"IN","Aland Islands":"AX","Azerbaijan":"AZ","Ireland":"IE","Indonesia":"ID","Ukraine":"UA","Qatar":"QA","Mozambique":"MZ"}';
$countriesArr = json_decode($countries2isoJson);


set_time_limit(0);

function doCurlPost($url){
	

	$arrUrl  = explode('?', $url);
	
	$ch      = curl_init();
	
	$tmpparams = $arrUrl[1];
	$exp = explode('&',$tmpparams);
	
	$params = array();
	foreach ($exp as $item) {
		$itm = explode('=',$item);
		$params[$itm[0]] = $itm[1];
	}

	curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
	curl_setopt($ch, CURLOPT_POST, 1);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,20); 
	curl_setopt($ch, CURLOPT_TIMEOUT, 50); //timeout in seconds
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	
	$error = strtolower(getTag('<error>','<\/error>',$result));
	
	if ($error=='noresults') {
		echo '<span style="color:red">' .ucwords(getTag('<error>','<\/error>',$result)) . '</span>';
		return '';
	} else {
		return $result;
	
	}

}



	$parse_url=parse_url($url);



 function doPost($url,$forceGet=false)
{
	$arrUrl  = explode('?', $url);
	$ch      = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
	curl_setopt($ch, CURLOPT_POST, !$forceGet);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	// var_dump($result);
	$error = strtolower(getTag('<error>','<\/error>',$result));
	
	if ($error=='noresults') {
		echo '<span style="color:red">' .ucwords(getTag('<error>','<\/error>',$result)) . '</span>';
		return '';
	} else {
		return $result;
	
	}
}

if (!empty($merchant_id)) 
	$wwList = mysql_query("select * from merchants where valid<>0 apiType='spot' and id = " . $merchant_id);
else {
	$q = "select * from merchants where  valid<>0 and apiType='spot' and  not APIurl =''";
	$wwList = mysql_query($q);
	// die ($q);
}


$countriesArray = array();
$rsc = mysql_query("select * from countries");
while ($row = mysql_fetch_assoc($rsc)){
	$countriesArray[$row['code']] = $row['id'];
}


while ($ww  =  mysql_fetch_assoc($wwList)) {

$defaultAffiliateID = $ww['defaultAffiliateID'];
$defaultBtag='a'.$defaultAffiliateID.'-b-p-f'.$ww['id'];

	
echo '<span style="color:orange;font-size:18px;">Proccessing the brand: ' . strtoupper($ww['name']) . '   ('.$ww['id'].')</span><br>';
$api_url = $ww['APIurl'] ;
$api_user =  $ww['APIuser'];
$api_pass = $ww['APIpass'];
$api_label =  $ww['name'];
$api_whiteLabel =  $ww['name'];

echo '<style type="text/css">html,body { font-size: 11px; font-family: Tahoma; } </style>';


			echo '<span style="color:green"><br>'.$api_whiteLabel . '<br></span>';
			

	
		$campscount++;
		 if($campscount % 4 ==0) {
			 usleep(500000);
		 }
		 $totalRowsFoundSoFar = 0;
		 
		
			
			
		$totalRowsFoundThisPage = 0;
		// echo '<hr /><b>Connecting to Customers\'s & Database (Campaign ID:'.$current_Campaign.') <span style="color:blue">Page: <u>'.$page.'</u>...</span></b><br />';
		
		
		
		
		// $url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Customer&COMMAND=view&api_whiteLabel='.$api_label.($ignoreCampaign>0 ? '' :'&FILTER[campaignid]='.$current_Campaign).'&FILTER[regTime][min]='.$scanDateFrom.'&FILTER[regTime][max]='.$scanDateTo.'&page='.$page;
		$url = concatUrlParts($api_url,'api_username='.$api_user.'&api_password='.$api_pass.'&MODULE=Country&COMMAND=view');
		echo 'url : ' . $url . '<br>';
		$intProcessed=0;
		$xml_report=doCurlPost($url);
		
		// var_dump($xml_report);
		
		// die();
		// $xml_report=doPost($url);
if (strlen($xml_report)<200 || $debug==1) {
		echo '<pre>';
		print_r($xml_report,TRUE);
		echo '</pre><br>';
	}
	
		// preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		preg_match_all("/<data_[0-9]+>([\s\S]*?)<\/data_[0-9]+>/",$xml_report,$xml);
		$arrXmlTest = array();
		
			if ($debug ==1) 		{ echo '<br>debug: ' ; var_dump($xml_report); }
		foreach($xml[1] AS $xml_line) {
			
			
			$title = getTag('<name>','<\/name>',$xml_line);
		if (strtolower($title)=="any")
			continue;
		
		
		$intProcessed++;
			
			
			
			$db['spotID'] = getTag('<id>','<\/id>',$xml_line);
			$db['2isoCode'] = getTag('<iso>','<\/iso>',$xml_line);
			$db['isBlocked'] = getTag('<block>','<\/block>',$xml_line);
			$db['allowReg'] = getTag('<allowRegistration>','<\/allowRegistration>',$xml_line);
			$db['allowDeposit'] = getTag('<allowDeposit>','<\/allowDeposit>',$xml_line);
			$db['country_id'] = $countriesArray[$db['2isoCode']];
			$db['merchant_id'] = $ww['id'];
			if ($db['isBlocked']==0 && $db['allowDeposit']==1  && $db['allowReg'] ==1)
				$db['agregateValid'] = 1;
			else 
				$db['agregateValid'] = 0;
			
			$db['rdate'] = date('Y-m-d H:i:s');
			
			 /* var_dump($xml_line);
			echo '<Br><br>';
			
			var_dump($db);
			die();  */
						
			$qq = "SELECT id from merchants_countries where merchant_id = " . $db['merchant_id'] . " AND country_id=".$db['country_id'] . " limit 1;";
			
			if ($debug) {
			
			echo '<Br><Br>';
			var_dump($xml_line);
			echo '<Br>';
			echo $qq.'<Br><Br>';
			
			}
			$chkDouble=mysql_fetch_assoc(mysql_query($qq));
			
			if ($chkDouble['id']) {
		
				$q = ("UPDATE merchants_countries SET agregateValid=".$db['agregateValid']."
																		,allowdeposit=".$db['allowdeposit']." 
																		,allowReg=".$db['allowReg']." 
																		,isBlocked=".$db['isBlocked']." 
																		,2isoCode='".$db['2isoCode']."'
																		,rdate='".$db['rdate']."'
																		WHERE id = ".$chkDouble['id']);
			}
			else
			{
				
				$q = "INSERT INTO `merchants_countries`( `2isoCode`,`merchant_id`, `country_id`, `rdate`, `internal_valid`, `allowReg`, `allowDeposit`, `isBlocked`, `agregateValid`) VALUES ('".
				$db['2isoCode'] . "'," .$db['merchant_id'] . "," . $db['country_id'].",'" . $db['rdate'] . "',1,".$db['allowReg'] . ",".$db['allowDeposit'] . ",".$db['isBlocked'] . ",".$db['agregateValid'] . ")";
			}
// die ($q);
mysql_query($q);

	}

		if ($intProcessed>0)
			echo '<span color:blue;font:size:14px;>Total Processed: ' . $intProcessed . '</span><br><br>';
	else
			echo 'Total Processed: ' . $intProcessed . '<br>';

}
	echo '<br>Ending time' . date('h:i:s') . '<br>';
	echo 'Cron is done!';
die();
	exit;

?>
