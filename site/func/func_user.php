<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function adminPermissionCheck($page=""){
	global $set;
	
		$page = strtolower($page);

    if ($set->userInfo['userType']=='restricted' && $set->userInfo['level']=='admin'){

		if (
		$page=='permissions' ||
		$page=='admins' ||
		$page=='maintenance' ||
		$page=='configuration' ||
		$page=='permissions' ||
		$page=='api' ||
		$page=='campaignrelation' ||
            $page=='pendingdeposit' 
            ){
		return false;
            }
	
		
	}

    if ($set->userInfo['userType']=='teamlead' && $set->userInfo['level']=='admin'){
        if (
            $page=='pendingdeposit' ||
            $page=='maintenance' ||
            $page=='configuration'
            ){
                return false;
            }
    }
    
        
	
		return 	true;

}


function getMyFavoritsReports(){
	global $set;
	$level =$set->userInfo['level'];
	$array  = array();
	if (empty($level)){
			$level = $_SERVER['REQUEST_URI'];
			$level = ltrim($level,'/');
			$exp = explode('/',$level);
			$level = $exp[0];
	}
	
	$id =$set->userInfo['id'];
	
	if (!empty($id) && !empty($level)){
		
		
		$q = "select * from users_reports where level = '" . $level. "' and user_id = ". $id;
		
		$rsc = function_mysql_query($q,__FILE__,__FUNCTION__);
		while ($row = mysql_fetch_assoc($rsc)){
			$array[$row['id']] = $row;
		}
		
		
	}

	
		return $array;
	
}


function trimExplode($delim, $string, $onlyNonEmptyValues=0){
    $temp = explode($delim,$string);
    $newtemp=array();
    while(list($key,$val)=each($temp))      {
        if (!$onlyNonEmptyValues || strcmp("",trim($val)))      {
            $newtemp[]=trim($val);
        }
    }
    reset($newtemp);
    return $newtemp;
}

function LoginEvent ($loginEventArray,$justCheck=false,$loginAttempts = '') {
		
		
		/* if (!$justCheck) {
		var_dump($justCheck);
		var_dump($loginEventArray);
		echo '<br>';
		die();
		} */
				
			if ($loginEventArray['affiliate_valid']==NULL && $loginEventArray['affiliate_id']==NULL) {
				$loginEventArray['affiliate_valid']=-1;
			}
			if ($loginEventArray['affiliate_id']==NULL) {
				$loginEventArray['affiliate_id']=-1;
			}
			
			if ($justCheck==false) {
			$qry = "insert into loginHistory (`login_as_affiliate_by_user_id`,`error`,`type`,`username`,`password`,`affiliate_id`,`affiliate_valid`,`ip`,`refe`,`HTTP_USER_AGENT`,`REMOTE_ADDR`,`attempt`) values ('" . 
			$loginEventArray['login_as_affiliate_by_user_id'] 			. "','" .$loginEventArray['error'] 			. "','" .			$loginEventArray['type'] 			. "','" .			$loginEventArray['username'] 			. "','" .			strlen($loginEventArray['password']) 			. "','" .
			$loginEventArray['affiliate_id'] 			. "','" . $loginEventArray['affiliate_valid'] 			. "','" .			$loginEventArray['ip'] 			. "','" .			$loginEventArray['refe'] 			. "','" .
			$loginEventArray['HTTP_USER_AGENT'] 			. "','" .			$loginEventArray['REMOTE_ADDR'] 						. "', 'login')";
			
			// die ($qry);
			function_mysql_query($qry,__FILE__,__FUNCTION__);
			}
			
			if($loginAttempts !="")
				$qry ="select error, affiliate_id,id,admin_id_force_allow from loginHistory where  rdate >= DATE_SUB(NOW(),INTERVAL 1 HOUR)  and ip = '". $loginEventArray['ip']  . "' order by id desc limit " . $loginAttempts;
			else
				$qry ="select error, affiliate_id,id,admin_id_force_allow from loginHistory where  rdate >= DATE_SUB(NOW(),INTERVAL 1 HOUR)  and ip = '". $loginEventArray['ip']  . "' order by id desc limit 3";
			
				//die($qry);
			$rsc = function_mysql_query($qry,__FILE__,__FUNCTION__);
			
			$count=0;
			while ($row = mysql_fetch_assoc($rsc)){
				if ($row['admin_id_force_allow']>0) {
					return -1;
				}
				
				if ($row['affiliate_id'] >-1){
					break;
				}
				// var_dump($row);
			
				if ($row['affiliate_id']==-1 || $row['error']==true){
					$count ++;
				}
				
			}
			return $count;
			
}

function RegisterEvent ($regEventArray,$justCheck=false,$loginAttempts = '') {
		
		
			if ($regEventArray['affiliate_valid']==NULL && $regEventArray['affiliate_id']==NULL) {
				$regEventArray['affiliate_valid']=-1;
			}
			if ($regEventArray['affiliate_id']==NULL) {
				$regEventArray['affiliate_id']=-1;
			}
			
			if ($justCheck==false) {
			$qry = "insert into loginHistory (`error`,`type`,`username`,`password`,`affiliate_id`,`affiliate_valid`,`ip`,`refe`,`HTTP_USER_AGENT`,`REMOTE_ADDR`,`attempt`) values ('" . 
			$regEventArray['error'] 			. "','" .			$regEventArray['type'] 			. "','" .			$regEventArray['username'] 			. "','" .			strlen($regEventArray['password']) 			. "','" .
			$regEventArray['affiliate_id'] 			. "','" . $regEventArray['affiliate_valid'] 			. "','" .			$regEventArray['ip'] 			. "','" .			$regEventArray['refe'] 			. "','" .
			$regEventArray['HTTP_USER_AGENT'] 			. "','" .			$regEventArray['REMOTE_ADDR'] 						. "' , 'register')";
			
			// die ($qry);
			function_mysql_query($qry,__FILE__,__FUNCTION__);
			}
			
		/* 	if($loginAttempts !="")
				$qry ="select error, affiliate_id,id,admin_id_force_allow from loginHistory where  rdate >= DATE_SUB(NOW(),INTERVAL 1 HOUR)  and ip = '". $regEventArray['ip']  . "' order by id desc limit " . $loginAttempts;
			else
				$qry ="select error, affiliate_id,id,admin_id_force_allow from loginHistory where  rdate >= DATE_SUB(NOW(),INTERVAL 1 HOUR)  and ip = '". $regEventArray['ip']  . "' order by id desc limit 3";
			 */
			 
			$qry = "select count(id) as countReg from loginHistory where rdate > date_sub(now(), interval 1 minute) and attempt='register'";
				//die($qry);
			$rsc = function_mysql_query($qry,__FILE__,__FUNCTION__);
			
			$row= mysql_fetch_assoc($rsc);
			/* $count=0;
			while ($row = mysql_fetch_assoc($rsc)){
				if ($row['admin_id_force_allow']>0) {
					return -1;
				}
				
				if ($row['affiliate_id'] >-1){
					break;
				}
				// var_dump($row);
			
				if ($row['affiliate_id']==-1 || $row['error']==false){
					$count ++;
				}
				
			} */
			
			return $row['countReg'];
			
}
			
			

function getCountry($id="",$text=0) {
	global $set,$counriesList;
	/* $arr = Array("1" => "Afghanistan",
		"2" => "Albania",
		"3" => "Algeria",
		"4" => "American Samoa",
		"5" => "Andorra",
		"6" => "Angola",
		"7" => "Anguilla",
		"8" => "Antarctica",
		"9" => "Antigua & Barbuda",
		"10" => "Argentina",
		"11" => "Armenia",
		"12" => "Arctic Ocean",
		"13" => "Aruba",
		"14" => "Ashmore",
		"15" => "Atlantic Ocean",
		"16" => "Australia",
		"17" => "Austria",
		"18" => "Azerbaijan",
		"19" => "Bahamas",
		"20" => "Bahrain",
		"21" => "Baker Island",
		"22" => "Bangladesh",
		"23" => "Barbados",
		"24" => "Bassas da India",
		"25" => "Belarus",
		"26" => "Belgium",
		"27" => "Belize",
		"28" => "Benin",
		"29" => "Bermuda",
		"30" => "Bhutan",
		"31" => "Bolivia",
		"32" => "Bosnia & Herzegovina",
		"33" => "Botswana",
		"34" => "Bouvet Island",
		"35" => "Brazil",
		"36" => "British Virgin Islands",
		"37" => "Brunei",
		"38" => "Bulgaria",
		"39" => "Burkina Faso",
		"40" => "Burundi",
		"41" => "Cambodia",
		"42" => "Cameroon",
		"43" => "Canada",
		"44" => "Cape Verde",
		"45" => "Cayman Islands",
		"46" => "Central African Republic",
		"47" => "Chad",
		"48" => "Chile",
		"49" => "China",
		"50" => "Christmas Island",
		"51" => "Clipperton Island",
		"52" => "Cocos Islands",
		"53" => "Colombia",
		"54" => "Comoros",
		"55" => "Cook Islands",
		"56" => "Coral Sea Islands",
		"57" => "Costa Rica",
		"58" => "Cote d'Ivoire",
		"59" => "Croatia",
		"60" => "Cuba",
		"61" => "Cyprus",
		"62" => "Czech Republic",
		"63" => "Denmark",
		"64" => "Democratic Republic",
		"65" => "Djibouti",
		"66" => "Dominica",
		"67" => "Dominican Republic",
		"68" => "East Timor",
		"69" => "Ecuador",
		"70" => "Egypt",
		"71" => "El Salvador",
		"72" => "Equatorial Guinea",
		"73" => "Eritrea",
		"74" => "Estonia",
		"75" => "Ethiopia",
		"76" => "Europa Island",
		"77" => "Falkland Islands",
		"78" => "Faroe Islands",
		"79" => "Fiji",
		"81" => "Finland",
		"80" => "France",
		"82" => "French Guiana",
		"83" => "French Polynesia",
		"84" => "French Southern",
		"85" => "Gabon",
		"86" => "Gambia",
		"87" => "Gaza Strip",
		"88" => "Georgia",
		"89" => "Germany",
		"90" => "Ghana",
		"91" => "Gibraltar",
		"92" => "Glorioso Islands",
		"93" => "Greece",
		"94" => "Greenland",
		"95" => "Grenada",
		"96" => "Guadeloupe",
		"97" => "Guam",
		"98" => "Guatemala",
		"99" => "Guernsey",
		"100" => "Guinea",
		"101" => "Guinea-Bissau",
		"102" => "Guyana",
		"103" => "Haiti",
		"104" => "Heard Island",
		"105" => "Honduras",
		"106" => "HONG KONG",
		"107" => "Howland Island",
		"108" => "Hungary",
		"109" => "Iceland",
		"110" => "India",
		"111" => "Indian Ocean",
		"112" => "Indonesia",
		"113" => "Iran, Islamic Republic Of",
		"114" => "Iraq",
		"115" => "Ireland",
		"116" => "Isle of Man",
		"117" => "Israel",
		"118" => "Italy",
		"119" => "Jamaica",
		"120" => "Jan Mayen",
		"121" => "Japan",
		"122" => "Jarvis Island",
		"123" => "Jersey",
		"124" => "Johnston Atoll",
		"125" => "Jordan",
		"126" => "Juan de Nova Island",
		"127" => "Kazakhstan",
		"128" => "Kenya",
		"129" => "Kingman Reef",
		"130" => "Kiribati",
		"131" => "Kerguelen Archipelago",
		"132" => "Kosovo",
		"133" => "Kuwait",
		"134" => "Kyrgyzstan",
		"135" => "Laos",
		"136" => "Latvia",
		"137" => "Lebanon",
		"138" => "Lesotho",
		"139" => "Liberia",
		"140" => "Libya",
		"141" => "Liechtenstein",
		"142" => "Lithuania",
		"143" => "Luxembourg",
		"144" => "Macau",
		"145" => "Macedonia",
		"146" => "Madagascar",
		"147" => "Malawi",
		"148" => "Malaysia",
		"149" => "Maldives",
		"150" => "Mali",
		"151" => "Malta",
		"152" => "Marshall Islands",
		"153" => "Martinique",
		"154" => "Mauritania",
		"155" => "Mauritius",
		"156" => "Mayotte",
		"157" => "Mexico",
		"158" => "Micronesia",
		"159" => "Midway Islands",
		"160" => "Moldova",
		"161" => "Monaco",
		"162" => "Mongolia",
		"163" => "Montenegro",
		"164" => "Montserrat",
		"165" => "Morocco",
		"166" => "Mozambique",
		"167" => "Myanmar",
		"168" => "Namibia",
		"169" => "Nauru",
		"170" => "Navassa Island",
		"171" => "Nepal",
		"172" => "Netherlands",
		"173" => "Netherlands Antilles",
		"174" => "New Caledonia",
		"175" => "New Zealand",
		"176" => "Nicaragua",
		"177" => "Niger",
		"178" => "Nigeria",
		"179" => "Niue",
		"180" => "Norfolk Island",
		"181" => "North Korea",
		"182" => "North Sea",
		"183" => "Northern Mariana Islands",
		"184" => "Norway",
		"185" => "Oman",
		"186" => "Pacific Ocean",
		"187" => "Pakistan",
		"188" => "Palau",
		"189" => "Palmyra Atoll",
		"190" => "Panama",
		"191" => "Papua New Guinea",
		"192" => "Paracel Islands",
		"193" => "Paraguay",
		"194" => "Peru",
		"195" => "Philippines",
		"196" => "Pitcairn Islands",
		"197" => "Poland",
		"198" => "Portugal",
		"199" => "Puerto Rico",
		"200" => "Qatar",
		"201" => "Reunion",
		"202" => "Republic of the Congo",
		"203" => "Romania",
		"204" => "Russia",
		"205" => "Rwanda",
		"206" => "Saint Helena",
		"207" => "Saint Kitts & Nevis",
		"208" => "Saint Lucia",
		"209" => "Saint Pierre & Miquelon",
		"210" => "Saint Vincent",
		"211" => "Samoa",
		"212" => "San Marino",
		"213" => "Sao Tome & Principe",
		"214" => "Saudi Arabia",
		"215" => "Senegal",
		"216" => "Serbia",
		"217" => "Seychelles",
		"218" => "Sierra Leon",
		"219" => "Singapore",
		"220" => "Slovakia",
		"221" => "Slovenia",
		"222" => "Solomon Islands",
		"223" => "Somalia",
		"224" => "South Africa",
		"225" => "South Georgia",
		"226" => "South Korea",
		"227" => "Spain",
		"228" => "Spratly Islands",
		"229" => "Sri Lanka",
		"230" => "Sudan",
		"231" => "Suriname",
		"232" => "Svalbard",
		"233" => "Swaziland",
		"234" => "Sweden",
		"235" => "Switzerland",
		"236" => "Syria",
		"237" => "Taiwan, Province Of China",
		"238" => "Tajikistan",
		"239" => "Tanzania",
		"240" => "Thailand",
		"241" => "Togo",
		"242" => "Tokelau",
		"243" => "Tonga",
		"244" => "Trinidad & Tobago",
		"245" => "Tromelin Island",
		"246" => "Tunisia",
		"247" => "Turkey",
		"248" => "Turkmenistan",
		"249" => "Turks & Caicos Islands",
		"250" => "Tuvalu",
		"251" => "Uganda",
		"252" => "Ukraine",
		"253" => "United Arab Emirates",
		"254" => "United Kingdom",
		"255" => "USA",
		"256" => "Uruguay",
		"257" => "Vienna",
		"258" => "Vanuatu",
		"259" => "Venezuela",
		"260" => "Viet Nam",
		"261" => "Virgin Islands",
		"262" => "Wake Island",
		"263" => "Wallis and Futuna",
		"264" => "West Bank",
		"265" => "Western Sahara",
		"266" => "Yemen",
		"267" => "Yugoslavia",
		"268" => "Zambia",
		"269" => "Zimbabwe"); */
		
		if (empty($counriesList)){
			$sql = "SELECT * FROM countries where id>1";
			$strCountries   = function_mysql_query($sql,__FILE__,__FUNCTION__);
			
			while ($countries = mysql_fetch_assoc($strCountries)){
				$counriesList[$countries['id']] = $countries['title'];
			}
		}
		
		
		if ($text) $html = $counriesList[$id];
		else {
			$excludeCountries = trimExplode('|',$set->hideCountriesOnRegistration,1);

			foreach ($counriesList AS $i => $val) {
				if (count($excludeCountries)>0) {   //  exclusions mode
						/* foreach ($excludeCountries AS $k=>$a) {
							if (strtolower($a)==strtolower($val)) {
								//echo $a;
								continue;
							}
							else {
								$html .= '<option value="'.$i.'" '.($id == $i ? 'selected' : '').'>'.$val.'</option>';
								break;
							}
						} */
						//FIX by Shalini
						if(in_array($i, $excludeCountries)){
							continue;
						}
						else{
							$html .= '<option value="'.$i.'" '.($id == $i ? 'selected' : '').'>'.$val.'</option>';
						}
				}
				else { // normal mode without exclusions
				$html .= '<option value="'.$i.'" '.($id == $i ? 'selected' : '').'>'.$val.'</option>';
				}
			}
		}
	return $html;
	}
	
	
	function chgAffiliateStatus($affiliate_id=0,$newStatus="",$OldStatus="") {
	if (empty($affiliate_id))
		return;
	if ($newStatus==1 && $OldStatus == -2)
	{
		
			$emq = 'SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowActivated" and valid=1';

			$mailCode = mysql_fetch_assoc(function_mysql_query($emq,__FILE__,__FUNCTION__));
			sendTemplate($mailCode['mailCode'],$affiliate_id);
			
	}else if ($newStatus==0 && $OldStatus == -2)
	{
		
			$emq = 'SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowPending" and valid=1';

			$mailCode = mysql_fetch_assoc(function_mysql_query($emq,__FILE__,__FUNCTION__));
			sendTemplate($mailCode['mailCode'],$affiliate_id);
	}
	else if ($newStatus==-2 && $OldStatus > -1)
	{
		
			$emq = 'SELECT mailCode,title FROM mail_templates WHERE mailCode="AffiliateAccountIsNowRejected" and valid=1';

			$mailCode = mysql_fetch_assoc(function_mysql_query($emq,__FILE__,__FUNCTION__));
			sendTemplate($mailCode['mailCode'],$affiliate_id);
	}
	}
	
	
	
	


	function deleteOldLoginHistory(){
		
		$sql = "delete from loginHistory where rdate <= DATE_SUB(NOW(), INTERVAL 14 DAY)";
		function_mysql_query($sql);
		
	}
		
	//function to get the Report's hidden fields
	function getReportsHiddenCols($report,$level,$user_id){
		$location = $level . "->" . $report;
		$sql = "select * from reports_fields where userlevel='$level' and location='$location' and user_id=" . $user_id;
		$res = function_mysql_query($sql, __FILE__,__FUNCTION__);
		
		if($res){
			$wwFields = mysql_fetch_assoc($res);
			return $wwFields['removed_fields'];
		}
		return false;
	}
	
	function getValidateTraderMerchantScript($frm=""){
		$frm = empty($frm) ? "frmRepo" : $frm;
		$str = '
		<script>
		function validateMerchant(e){
					
					val = $(e).val();
					checkMerchant(val,0);
					
					
				}
				
				function checkMerchant(val,frmSubmit){
					if(val !== ""){
						merchant_id = $("select[name=\'merchant_id\']").val();
						if(merchant_id == ""){
							$.prompt("'. lang("Please select merchant.") .'", {
								top:200,
								title: "'. lang('Trader Report') .'",
								buttons: { "'.lang('Ok').'": true},
								submit: function(e,v,m,f){
									if(v){
										//
									}
									else{
										//
									}
								}
							});
						}
						else{
							if(frmSubmit==1){
								$("#'. $frm .'").submit();
							}
						}
					}
					else{
						if(frmSubmit==1){
								$("#'. $frm .'").submit();
							}
					}
				}
				
				
				function validateForm(){
					
					val = $("input[name=\'trader_id\']").val();
					checkMerchant(val,1);
					
					
				}
		</script>
		';
		return $str;
		
	}
	
	
	function getSingleSelectedMerchant(){
		
		$str = '
		<script>
		$(document).ready(function(){
			if($("select[name=\'merchant_id\'] option").length <=2){
				$("select[name=\'merchant_id\'] option:last").attr("selected","selected");
			}
			else{
				$("select[name=\'merchant_id\'] option:first").attr("selected","selected");
			}
		})	;
		</script>
		';
		return $str;
	}
	
?>
