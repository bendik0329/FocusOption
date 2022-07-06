<?php
$affiliateDealsTierArry = array(); // for affiliates tier deals array;
$creativesRowsArray = array(); // for creatives loaded array;
$affiliatesRowsArray = array(); // for all affiliates details;
$merchantsPromotions = array(); 
$languagesArray = array(); 
$merchantsArray = array(); // for all merchants;
$affiliatesDealsCPA = array();
$affiliatesDealsCPI = array();
$affiliatesDealsCPL = array();
$affiliatesDealsREV = array();
$groupsArray = array();
$affiliatesDealsREVLimit = array();
$affiliateDealByType = array();
$existingDealTypesForAffiliateArray = array();
$existingDealTypesAllMerchantsForAffiliateArray = array();
$affiliateFullTierDeal = array();
$affiliateCommissionCPLArray = array();
$affiliateCommissionCPMCPCArray = array();
$ftdByDealTypeArray = array();
$countryLongNameArray = array();
$listCategoryArr = array();
$counriesList = array();
$allTradersWithTag = array();
$listOfProducts = array();

$AffiliatesDealOnFtdDate = array();

$dynamicFiltersLoaded = false;
$dynamicFilters = array();

function listDynamicFilters($dynamic_filter=0,$onlyValid=1,$optionsStructure=false){
	global $dynamicFilters,$dynamicFiltersLoaded;
	
	if (!$dynamicFiltersLoaded){

		$sql = "select * from dynamic_filters";
		$prdcResource=  function_mysql_query($sql,__FILE__);
		while ($row = mysql_fetch_assoc($prdcResource)){
			$dynamicFilters[$row['id']] = $row;
		}
		$dynamicFiltersLoaded = true;
	}
		
		if (!$optionsStructure && $onlyValid==0)
			return $dynamicFilters;
		

	$newOutput="";
	foreach ($dynamicFilters as $dynamicfilterItem){
		if ($onlyValid && $dynamicfilterItem['valid']==0 )
			continue;
		
		$dynamicName = empty($dynamicfilterItem['caption']) ? $dynamicfilterItem['name'] : $dynamicfilterItem['caption'];
		$newOutput .= '<option value="'.$dynamicfilterItem['id'].'" '.($dynamic_filter == $dynamicfilterItem['id'] ? 'selected="selected"' : '').'>'.$dynamicName.'</option>' ;
	}
		return $newOutput;
		


}


function getMerchantType($merchant_id){
	
	
	if (is_numeric($merchant_id) && !empty($merchant_id)){
		$merchantww =getMerchants($merchant_id,0);
		
		
		
		if (strtolower($merchantww[0]['producttype']) == 'casino' || strtolower($merchantww['producttype'][0]) == 'sportsbetting') {
			return 'casino';
		}
		return strtolower($merchantww[0]['producttype']);
	}
	return "binary";
}


function getMerchants($merchant_id = 0,$onlyValid= 0) {
		global $merchantsArray;
		if (count($merchantsArray)==0) {
			$rscmer = function_mysql_query("select *,id as merchant_id from merchants",__FILE__,__FUNCTION__);
			 while ($row = mysql_fetch_array($rscmer)) {
				 $merchantsArray[$row['id']] = $row;
			 }
		}

		if ($merchant_id>0 && $onlyValid==0){
			$a[]= 	$merchantsArray[$merchant_id];
			return $a;
		}

		if ($merchant_id==0 && $onlyValid==0)
			return $merchantsArray;

		if ($merchant_id>0 && $onlyValid==1) {
			
			if ($merchantsArray[$merchant_id]['valid']==1){
				$a[]= 	$merchantsArray[$merchant_id];	
				return $a;
			}
			else
				return "";
		}


		if ($merchant_id==0 && $onlyValid==1) {
			$onlyValidMerchants=array();
			foreach($merchantsArray as $mer){
				if ($mer['valid']==1){
				$onlyValidMerchants[$mer['id']] = $mer;
				}
			}
			return $onlyValidMerchants;
			
		}
			return "";
}



/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function getUSD($price='0',$from='USD') {
	if (strtolower($from) == "usd"){
		return $price;
	} else {
		$qq=function_mysql_query("SELECT val,rate FROM exchange_rates WHERE lower(fromCurr)='".strtolower($from)."'",__FILE__,__FUNCTION__);
		$ww=mysql_fetch_assoc($qq);

		if($ww['rate'] > 0){
			return round($price*($ww['val'] * (1 - $ww['rate']/100)),2);
		}else{
			return round($price*$ww['val'],2);
		}
	}
}

function getExistingTierDealRowData($deal, $activeDeals){
	
	foreach ($activeDeals as $activeRow){
		
		if ($activeRow['id'] == $deal['id'])
		{
			if ($activeRow['amount'] == $deal['amount'] && 
				$activeRow['tier_pcpa'] == $deal['tier_pcpa'] && 
				$activeRow['tier_amount'] == $deal['tier_amount'] ){
				return $activeRow;
			}
			else {
/* 				echo 'deal:<br><Br>';
				var_dump($deal);
				echo '<Br><Br>activeRow:<Br>';
				var_dump($activeRow);
				die(); */
			}
		}
	}
	return array();
	
}

function getExistingTierDealRowDataByID($row_id, $activeDeals){
	if (!empty($activeDeals))
	foreach ($activeDeals as $activeRow){
		if ($activeRow['id'] == $row_id){
		return $activeRow;
		}
	}
	return array();
	
}

function getCurrentActiveTierDeal($tier_merchant, $affiliate_id , $networkWheremid,$testParam="1"){

// echo 'network: ' . $networkWheremid.'<br>';

					 	 $qry = "SELECT * FROM `affiliates_deals` deal11 where " . $testParam . " = " . $testParam . " and 
						 deal11.valid=1  " .(isset($tier_merchant)&&$tier_merchant!=""?' and deal11.merchant_id= ' . $tier_merchant :' and deal11.merchant_id= ' . $tier_merchant)  .$networkWheremid. "  and deal11.affiliate_id = '".$affiliate_id."' and  deal11.dealType <> 'tier'  order by rdate desc limit 1;";
						// die ($qry);
						
						$lastNonTierRow = mysql_fetch_assoc(function_mysql_query($qry));
						if (!isset($lastNonTierRow['id']))
							$lastNonTierRow['id'] =0;
						

					 	 $qry = "SELECT * FROM `affiliates_deals` deal12
        WHERE  " . $testParam . " = " . $testParam . " and 
		deal12.id > ".$lastNonTierRow['id']. " and deal12.valid=1  " .(isset($tier_merchant)&& $tier_merchant!=""?' and deal12.merchant_id= ' . $tier_merchant :' and deal12.merchant_id= ' . $tier_merchant)  .$networkWheremid. "  and deal12.affiliate_id = '".$affiliate_id."' and deal12.tier_type='ftd_amount' and deal12.dealType = 'tier' 
               order by deal12.rdate asc";
		// deal12.valid=1  " .(isset($tier_merchant)&&$tier_merchant!=""?' and deal12.merchant_id= ' . $tier_merchant :' and deal12.merchant_id= ' . $defaultTierMer)  .$networkWheremid. "  and deal12.affiliate_id = '".$affiliate_id."' and deal12.tier_type='ftd_amount' and deal12.dealType = 'tier' 
		
		// die ($qry);
	$takeqq = function_mysql_query($qry,__FILE__,__FUNCTION__);
				
                           

					$tierDealActiveDeals = array();
					while ($takeww = mysql_fetch_assoc($takeqq)) {
						
						// var_dump($takeww);
						// echo '<br><Br>';
						
						
						// if ($lastNonTierRow['id']<=$takeww['id'])
							// break;
						$tierDealActiveDeals[$takeww['tier_amount']] = $takeww;
					}
						unset ($takeww);


						 $beforeSortArray = $tierDealActiveDeals;
						unset ($tierDealActiveDeals);
						$tierDealActiveDeals = array();
						
						foreach ($beforeSortArray as $takeww){
							
							$rankEx = explode ('-',$takeww['tier_amount']);
							$rank  = trim($rankEx[0]);
							$tierDealActiveDeals[$rank]= $takeww ;
						} 
						 ksort($tierDealActiveDeals);
						 return $tierDealActiveDeals;
		}

function resetAllActiveTierDeals($merchant_id,$affiliate_id,$testParam="2"){
		
		$tierDealActiveDeals = getCurrentActiveTierDeal($merchant_id, $affiliate_id , "",$testParam);
		foreach ($tierDealActiveDeals as $activeTierDealRow){
		if ($activeTierDealRow['amount']==0 && $activeTierDealRow['tier_pcpa'] ==0)
			continue;
		
		$sql = 	
						"INSERT INTO  `affiliates_deals` (`valid` ,`rdate` ,`admin_id` ,`merchant_id` ,`affiliate_id` ,`dealType` ,`amount` ,`tier_amount` ,`tier_pcpa` ,`tier_type`)VALUES (
									1, '".date('Y-m-d H:i:s',strtotime("-1 second"))."',  ".$activeTierDealRow['admin_id'].",  '".$activeTierDealRow['merchant_id']."',  '".$activeTierDealRow['affiliate_id']."',  '".$activeTierDealRow['dealType']."',  '0',  '".$activeTierDealRow['tier_amount']."',  '0',  '".$activeTierDealRow['tier_type']."');";

					function_mysql_query($sql);
		}
		
}
		
function processDealType($strSqlTable, $arrCheckDeal, $set, $affiliate_id, $deal_merchant, $strDealType, $globalValue, $dealTypeAmount, $strDealPost,$country="")
{
	global $set;
	
		 // if ($deal_merchant=='1') {
			 // var_dump ($strDealType);
			 // die ('done');
		 // }
        $debug = 0;
        
	if ($debug) {
            echo(print_r(array(
                    'strSqlTable' => $strSqlTable, 
                    'arrCheckDeal' => $arrCheckDeal, 
                    'affiliate_id' => $affiliate_id, 
                    'deal_merchant' => $deal_merchant, 
                    'strDealType' => $strDealType, 
                    'globalValue' => $globalValue, 
                    'dealTypeAmount' => $dealTypeAmount, 
                    'strDealPost' => $strDealPost,
                    'geo' => $country
            )));
			echo '<Br>';
        }
	/*
	Array ( [strSqlTable] => affiliates_deals 
			[arrCheckDeal] => Array ( [id] => 1024 [merchant_id] => 22 [amount] => 1 ) 
			[affiliate_id] => 502 [deal_merchant] => 22 [strDealType] => dcpa [globalValue] => [dealTypeAmount] => [strDealPost] => deal_dcpa )
	*/
	//echo '1<br>';
	// die ('test');
	
	
	if (empty($deal_merchant)) {
		return 0;
	}
	
	$arrParams = array();
	
	if ($arrCheckDeal['id']) {
		$arrParams['id'] = $arrCheckDeal['id'];
	}
	
	$arrParams['rdate']        = dbDate();
	$datetimeMinusSecond = date("Y-m-d H:i:s", strtotime("-3 seconds", strtotime($arrParams['rdate'])));
	
	
	$arrParams['admin_id']     = $set->userInfo['id'];
	$arrParams['affiliate_id'] = $affiliate_id;
	$arrParams['merchant_id']  = $deal_merchant;
	$arrParams['dealType']     = $strDealType;
	$arrParams['geo']     = $country;
	
	$oldDealTypeAmount   = trim($arrCheckDeal['amount']);
	
	
	/*
	if ($strDealPost =='deal_min_cpa') $idx = 0;
	if ($strDealPost =='deal_cpa') $idx = 1;
	if ($strDealPost =='deal_dcpa') $idx = 2;
	if ($strDealPost =='deal_revenue') $idx = 3;
	if ($strDealPost =='deal_revenue_spread') $idx = 4;
	if ($strDealPost =='deal_cpl') $idx = 5;
	if ($strDealPost =='deal_cpc') $idx = 6;
	if ($strDealPost =='deal_cpm') $idx = 7;
	*/
	
	for ($idx = 0; $idx < count($_POST['deal_merchant']); $idx++) {
		$arrDealTypes = array('deal_min_cpa', 'deal_cpa', 'deal_dcpa', 'deal_revenue', 'deal_revenue_spread','deal_lots', 'deal_cpi','deal_cpl', 'deal_cpc', 'deal_pnl', 'deal_cpm');
		
		// $resetTierQuery= "update affiliates_deals set valid=0 where dealType='tier' and valid=1 and affiliate_id = '" .  $arrParams['affiliate_id'] . "' and merchant_id ='" . $arrParams['merchant_id']. "'";
		// var_dump($_POST);
		// die();
		
		
		
		$testParam = "2";
		resetAllActiveTierDeals($deal_merchant,$affiliate_id,$testParam);
		
		
		
		
		// die ('func global 291: ' . $resetTierQuery);
		
		foreach ($arrDealTypes as $dealType) {
			
			if ($deal_merchant == $_POST['deal_merchant'][$idx] && $strDealPost == 'deal_' . $strDealType ) {
			

				$currentDealTypeAmount = $_POST[$dealType][$idx];
				
				$globalValue = trim($globalValue);
				
		
				if ($oldDealTypeAmount == $dealTypeAmount && $globalValue=='') {
					continue;
				}
				
				if ((!empty($globalValue) || '0' == $globalValue) && $globalValue > -1) {
					// '$globalValue' can not be empty, but can be equal to zero(string or numeric).
					if ($debug == 1) {
                                            echo 'global not empty or global 0 <br>';
                                        }
                     
					mysql_query($resetTierQuery);
					 
					$sql = "INSERT INTO affiliates_deals (rdate,admin_id,affiliate_id,merchant_id,dealType,amount ". ($set->deal_geoLocation==1  ? ",geo" : "")." ) 
						VALUES ('" . $arrParams['rdate'] . "','" . $arrParams['admin_id'] . "','" 
								   . $arrParams['affiliate_id'] . "','" . $arrParams['merchant_id'] . "','" . $arrParams['dealType'] . "'," . $globalValue . "". ($set->deal_geoLocation==1  ? ",'". $country ."' " : "").");";
						// die		   ($sql);
					return function_mysql_query($sql,__FILE__,__FUNCTION__);
				}
				
				if ($dealTypeAmount == '0') {
					$debug = 0;
					if ($debug == 1) {
						echo 'dealtype is 0<br>';
						echo 'old is '.$oldDealTypeAmount.'<br>';
						echo 'new is '. $currentDealTypeAmount.'<br>';
						echo 'id is '. $arrParams['id'].'<br>';
					die('!@#$%^&');
					}
					if ($arrParams['id'] || (empty($oldDealTypeAmount) && ($oldDealTypeAmount !== $currentDealTypeAmount))) {
						
						if ($oldDealTypeAmount !== $currentDealTypeAmount) { 
							
							mysql_query($resetTierQuery);
							
							$qry = "INSERT INTO affiliates_deals (rdate,admin_id,affiliate_id,merchant_id,dealType,amount". ($set->deal_geoLocation==1  ? ",geo" : "").") 
								VALUES ('" . $datetimeMinusSecond . "','" . $arrParams['admin_id'] . "','" 
										   . $arrParams['affiliate_id'] . "','" . $arrParams['merchant_id'] . "','" . $arrParams['dealType'] . "', 0". ($set->deal_geoLocation==1  ? ",'". $country ."' " : "").");";
							
                                                        // INSERT INTO affiliates_deals (rdate,admin_id,affiliate_id,merchant_id,dealType,amount) 
                                                        // VALUES ('2015-02-26 16:33:20','15','658','22','min_cpa', 0);
							return function_mysql_query($qry,__FILE__,__FUNCTION__);
						}
					}
					
				} elseif (empty($dealTypeAmount)) {
					// If an amount for given deal-type and given affiliate and given merchant_id updated for the first time,
					// then avoid insertion of NULL.
					
					$sql = "SELECT COUNT(id) AS count FROM affiliates_deals 
						    WHERE affiliate_id = " . $affiliate_id . " AND merchant_id = " . $deal_merchant . " AND dealType = '" . $strDealType . "';";
				    
					$arrResult = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
					$count     = (int) $arrResult['count'];
					
					if ($count === 0) {
						return false;
					}
					
					if ($debug == 1) {
						echo $dealTypeAmount . ' (dealTypeAmount) is empty<br>';
						echo $currentDealTypeAmount . ' (currentDealTypeAmount) value<br>';
						echo $oldDealTypeAmount . ' (oldDealTypeAmount) value<br>';
					}
					
					//echo '1 Old: ' . $oldDealTypeAmount . '<br>New: ' . $dealTypeAmount . '<br>Current: ' . $currentDealTypeAmount . '<hr><br>';
					
					if ($oldDealTypeAmount !== $currentDealTypeAmount && $currentDealTypeAmount!="") {
						//echo "Dasdasddsad";die;
						//echo '2 Old: ' . $oldDealTypeAmount . '<br>New: ' . $dealTypeAmount . '<br>Current: ' . $currentDealTypeAmount . '<hr><br>';continue;
						
						if ($debug == 1) {
							echo '4<br>' . 'DealMerchant: ' .  $deal_merchant . '<br>' . print_r($arrParams) . '<br>$oldDealTypeAmount: ' 
										 . $oldDealTypeAmount . '<br>$currentDealTypeAmount: ' . $currentDealTypeAmount;
						}

						
						mysql_query($resetTierQuery);
/* 						
						$qry = "INSERT INTO affiliates_deals (rdate,admin_id,affiliate_id,merchant_id,dealType,amount) 
							VALUES ('" . $arrParams['rdate'] . "','" . $arrParams['admin_id'] . "','" 
									   . $arrParams['affiliate_id'] . "','" . $arrParams['merchant_id'] . "','" . $arrParams['dealType'] . "', NULL);";
									    */
									   $qry = "INSERT INTO affiliates_deals (rdate,admin_id,affiliate_id,merchant_id,dealType,amount". ($set->deal_geoLocation==1  ? ",geo" : "").") 
							VALUES ('" . $datetimeMinusSecond . "','" . $arrParams['admin_id'] . "','" 
									   . $arrParams['affiliate_id'] . "','" . $arrParams['merchant_id'] . "','" . $arrParams['dealType'] . "', 0". ($set->deal_geoLocation==1  ? ",'". $country ."' " : "").");";
									   
									   
									   // echo $qry;die;
						return function_mysql_query($qry,__FILE__,__FUNCTION__);
					}
					
					
				} elseif ($dealTypeAmount > 0) {
					$arrParams['amount'] = ('' != $globalValue &&  $globalValue != -1) ? $globalValue : $dealTypeAmount;
					
					if ($oldDealTypeAmount !== $currentDealTypeAmount || $globalValue) {
											$a = dbInsert($arrParams, $strSqlTable);
											mysql_query($resetTierQuery);
                                            return $a;
					}
				}
			}
		}
	}
}



function listMerchants($id=0,$text=0,$ignoreValid=0,$orderbyName=0) {
	global $set;
	$where='';
	if($set->userInfo['level']=='manager' AND $set->isNetwork){
		$where.=' AND id='.aesDec($_COOKIE['mid']);
	}
	$qq=function_mysql_query("SELECT id,name,type FROM merchants WHERE ".($ignoreValid ? "1=1" : "valid='1'")." ORDER BY type, " . ($orderbyName==1 ? " lower(name)" : "pos" ),__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) {
		if ($text AND $id == $ww['id']) return $ww['name'];
		if ($set->getFolder[1] == "affiliate" AND !chkMerchant($ww['id'])) continue;
		// if (!$ww['valid'] AND !$set->adminInfo['id']) continue;
		
		/* if ($ww['type'] != $currentType) {
			$currentType = $ww['type'];
			$html .= '</optgroup><optgroup label="'.typeName($ww['type']).' Brokers">';
			} */
			$html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $id ? 'selected="selected"' : '').'>'.$ww['name'].'</option>';
		}
	return $html;
	}
	
function listDisplayTypes($selected){
	global $set;
	$html = '<option value="daily" '.($selected == "daily" ? 'selected' : '').'>'.lang('Daily').'</option>';
	$html .= '<option value="weekly" '.($selected == "weekly" ? 'selected' : '').'>'.lang('Weekly').'</option>';
	$html .= '<option value="monthly" '.($selected == "monthly" ? 'selected' : '').'>'.lang('Monthly').'</option>';
	
	return $html;
	}

function typeName($type='') {

	$type = strtolower($type);

	if (!$type) return false;
	if ($type == "binary") $name = 'Binary Option';
		else if ($type == "forex") $name = 'Forex';
		else if ($type == "casino") $name = 'Casino';
		else if ($type == "sportbook") $name = 'Sport Book';
		else if ($type == "rummy") $name = 'Rummy';
	return $name;
	}
	
function listLangs($id=0,$text=0,$showOptionsByValuesArrayOnly=array()) {
global $languagesArray;
/* 
	$qq=function_mysql_query("SELECT id,title FROM languages WHERE valid='1' ORDER BY title ASC");
	while ($ww=mysql_fetch_assoc($qq)) {
		if ($text AND $id == $ww['id']) return $ww['title'];
		$html .= '<option '. (!empty($showOptionsByValuesArrayOnly) && !(in_array($ww['id'],$showOptionsByValuesArrayOnly)) ? 'style="display:none" ' : '')  .' value="'.$ww['id'].'" '.($ww['id'] == $id ? 'selected' : '').'>'.lang($ww['title']).'</option>';
		}
	return $html;
	
	
	 */

		

		if (empty($languagesArray)){
				// $qq=function_mysql_query("SELECT id,title FROM languages WHERE valid='1' ORDER BY title ASC",__FILE__,__FUNCTION__);
				$qq=function_mysql_query("SELECT id,title FROM languages WHERE 1=1 ORDER BY title ASC",__FILE__,__FUNCTION__);
				while ($ww=mysql_fetch_assoc($qq)) {
					$languagesArray[$ww['id']] = $ww;

				}
		}
	
	foreach ($languagesArray as $ww){
		
		
		
		if ($text AND $id == $ww['id']) return $ww['title'];
		
		$style="";
		
		if (count($showOptionsByValuesArrayOnly)<>'0') {
		//	if ($text && $id)
			//die ('<option '.  $style .' value="'.$ww['id'].'" '.($ww['id'] == $id ? ' selected ' : '').'>'.lang($ww['title']).'</option>');
		// echo 'id: ' . $id . '<br>' . 	'text: ' . $text . '<br>';
			// die();
		if (!in_array($ww['id'],$showOptionsByValuesArrayOnly)) {
					$style= ' style="display:none;" ';
			}
		}

		
		$html .= '<option '.  $style .' value="'. str_replace('\"','',$ww['id']).'" '.($ww['id'] == $id ? ' selected ' : '').'>'.lang($ww['title']).'</option>';
		}
		// die ($html);
		return $html;
		
	
	
	}
	
function listMulti($selected='') {
	global $set;
	$exp=explode(",",$set->multi_languages);
	for ($i=0; $i<count($exp); $i++) $html .= '<option value="'.substr(strtoupper($exp[$i]),0,3).'" '.(substr(strtoupper($exp[$i]),0,3) == $selected ? 'selected' : '').'>'.lang($exp[$i]).'</option>';
	return $html;
	}
	
function listCreativeCategoryByMerchant($id=0,$mer_id) {
	$html = '';
        //$rsc = mysql_query("SELECT id,merchant_id,categoryname,valid FROM merchants_creative_categories WHERE merchant_id = ".(int)$mer_id." ORDER BY lcase(categoryname) ASC");
        $rsc = mysql_query("SELECT mcc.id,mcc.merchant_id,mcc.categoryname,mcc.valid FROM merchants_creative_categories mcc LEFT JOIN merchants_creative mcv ON mcv.category_id = mcc.id WHERE mcv.merchant_id=".(int)$mer_id." GROUP BY mcc.id ORDER BY lcase(mcc.categoryname) ASC");
        
        
        while ($row = mysql_fetch_assoc($rsc)){
            $html .= '<option value="'.$row['id'].'" '.($row['id'] == $id ? 'selected' : '').'>'.($row['categoryname']).'</option>';
        }
        return $html;
}

function listCategory($id=0,$mer_id=0,$text=0,$ignoreValid=0) {
	global $listCategoryArr,$set;
	$returnedArr = array();
	if (empty($listCategoryArr)){
		
		$q = "SELECT id,merchant_id,categoryname,valid FROM merchants_creative_categories ORDER BY lcase(categoryname) ASC";
		$rsc = mysql_query($q);
		while ($row = mysql_fetch_assoc($rsc)){
			$listCategoryArr[$row['merchant_id']][] = $row;
		}
	}


		if ($set->userInfo['level']=='admin' || $set->userInfo['level']=='manager' || $set->userInfo['level']=='advertiser'){
			
			$arr = getMerchants(0,0);
			foreach ($arr as $mer){
				$mer_id .= "," . $mer['id'];
			}
			$mer_id = ltrim($mer_id,',');
		}
		else
			$mer_id = str_replace('|',',',($set->userInfo['merchants']));

		if (empty($mer_id)) 
			$mer_id= 0;
	
		$allowMerchantsForAffiliate = explode(',',$mer_id);

		foreach ($allowMerchantsForAffiliate as $allowMerchantForAffiliate){
			
			$creativesCategoriesForMerchants = $listCategoryArr[$allowMerchantForAffiliate];
			if (!$creativesCategoriesForMerchants)
				continue;

			foreach ($creativesCategoriesForMerchants as $craetive_category){
				if ($ignoreValid==0 && $craetive_category['valid']!=1)
					continue;
				
				// $returnedArr[] = $craetive_category;
				$returnedArr[$craetive_category['id']] = $craetive_category;
				
		}

		
	}
	
	
	/*
	$second_part_of_query = ($ignoreValid==0 ? " and valid='1' " : " ") .$where." ORDER BY lcase(categoryname) ASC";
	if (empty($listCategoryArr[$second_part_of_query]))
		
	
	$qry = "SELECT id,categoryname,valid FROM merchants_creative_categories WHERE 1=1 " . $second_part_of_query;
	// die ($qry);
	$qq=function_mysql_query($qry,__FILE__,__FUNCTION__); 
	$counter = 0;
	while ($ww=mysql_fetch_assoc($qq)) {
	*/
	foreach ($returnedArr as $ww){
	
		if ($text AND $id == $ww['id']) {
			
			if ( $ww['valid']==0 && $ignoreValid ==1 && $id>0 && $text!='') 
				return $ww['categoryname'] . ' <span style="color:red">(' . lang('Inactive') . ')</span>';		
		return $ww['categoryname'];
		}
		$counter++;
		// $html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $id ? 'selected' : '').'>'.lang($ww['categoryname']).'</option>';
		$html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $id ? 'selected' : '').'>'.($ww['categoryname']).'</option>';
		}
                
	if (!$id AND $text) $html = lang('General');
	
	return $html;
	}

function getPromotion($id){
	global $merchantsPromotions;
	if (empty($id)){
		
		return array('title' => lang('General'), 'id'=>0 ,  'affiliate_id'=>0 , 'valid'=>1);
	}
	if (empty($merchantsPromotions)){
		$qry = "SELECT * FROM merchants_promotions WHERE 1=1  ORDER BY lcase(title) ASC";
		$qq=function_mysql_query($qry,__FILE__,__FUNCTION__);
		$counter = 0;
		while ($ww=mysql_fetch_assoc($qq)) {
			$merchantsPromotions[$ww['id']] = $ww;
		}
	}
	return $merchantsPromotions[$id];
	
}
function listPromotions($id=0,$mer_id=0,$text=0,$aff_id=0,$ignoreValid=0,$addAffiliateZeroPromotions=0) {
	global $set,$merchantsPromotions;
	
	if (!$id AND $text) $html = lang('General');
	
	if (empty($merchantsPromotions)){
		$qry = "SELECT * FROM merchants_promotions WHERE 1=1  ORDER BY lcase(title) ASC";
		$qq=function_mysql_query($qry,__FILE__,__FUNCTION__);
		$counter = 0;
		while ($ww=mysql_fetch_assoc($qq)) {
			$merchantsPromotions[$ww['id']] = $ww;
		}
	}
	
	$counter = 0;
/* 	
	$qry = "SELECT id,title,valid FROM merchants_promotions WHERE 1=1 " . ($ignoreValid==0 ? " and valid>-1 " : "  ") .$where." ORDER BY lcase(title) ASC";
	$qq=function_mysql_query($qry,__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) { */
	foreach ($merchantsPromotions as $ww){
		
		if ($addAffiliateZeroPromotions>0){
			if ($aff_id>0){
				if (!in_array($ww['affiliate_id'],[$aff_id,0]) && !in_array($aff_id, explode('|',$ww['additional_affiliates'])))
					continue;
			}
			else {
				if ($ww['affiliate_id']!=0)
					continue;
			}
		}
		else {
	if ($aff_id>0 && !in_array($ww['affiliate_id'],[$aff_id]) && !in_array($aff_id, explode('|',$ww['additional_affiliates'])))
					continue;
	}
	
	
	if ($mer_id ==0)
	$mer_id = str_replace('|',',',($set->userInfo['merchants']));
	$mer_id=ltrim($mer_id,',');
	$mer_id_arr  = explode(',',$mer_id);

	if ($mer_id>0 && !in_array($ww['merchant_id'],$mer_id_arr))
		continue;
	
// VVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVV
	 
/* 	if ($mer_id) $where = " AND merchant_id in (".$mer_id.")";
	if ($addAffiliateZeroPromotions>0) {
		if ($aff_id>0) {
		$where .= " AND affiliate_id in (0,".$aff_id.")";
		}
		else
			$where .= " AND affiliate_id in (0)";
	}
	else {
	if ($aff_id>0) $where .= " AND affiliate_id in (".$aff_id.")";
	}
	
	 */
	
	/* 
	if ($mer_id ==0)
	$mer_id = str_replace('|',',',($set->userInfo['merchants']));
	$mer_id=ltrim($mer_id,',');
	 
	if ($mer_id) $where = " AND merchant_id in (".$mer_id.")";
	if ($addAffiliateZeroPromotions>0) {
		if ($aff_id>0) {
		$where .= " AND affiliate_id in (0,".$aff_id.")";
		}
		else
			$where .= " AND affiliate_id in (0)";
	}
	else {
	if ($aff_id>0) $where .= " AND affiliate_id in (".$aff_id.")";
	} */
	
	
		if ($text AND $id == $ww['id']) {
			
			if ( ($ww['valid']==0 )&& $ignoreValid ==1 && $id>0 && $text!='') { 
				return $ww['title'] . ' <span style="color:red">(' . lang('Inactive') . ')</span>';		
			}
			else if($ww['valid']==-1) {
				return $ww['title'] . ' <span style="color:red">(' . lang('Deleted') . ')</span>';		
			}
		return $ww['title'];
		}
		$counter++;
		$html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $id ? 'selected' : '').'>'.$ww['title'].'</option>';
	}

	
	
	return $html;
	}

function listGroups($id=0,$text=0,$showOnlyRequestedOption=0) {
	
	$qq=function_mysql_query("SELECT id,title FROM groups WHERE valid='1' ".($showOnlyRequestedOption==1 && $id>0 ? " and id = " . $id : "" )." ORDER BY title ASC",__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) {
		
		if ($text)  {
			if ($id == $ww['id']) {
				return $ww['title'];
			}
		}
		else {
		$html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $id ? 'selected' : '').'>'.$ww['title'].'</option>';
		}
	}
	if (!$id AND $text) $html = lang('General');
	return $html;
	}
	
	
	function listStatus($id=0,$text=0,$created_by=0) {
	$qq=function_mysql_query("SELECT id,title FROM affiliates_status WHERE valid='1' ".
	($created_by != 0 ? ' AND created_by_admin_id = ' . $created_by : '')
	. " ORDER BY title ASC",__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) {
		if ($text AND $id == $ww['id']) return $ww['title'];
		$html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $id ? 'selected' : '').'>'.$ww['title'].'</option>';
		}
	if (!$id AND $text) $html = lang('General');
	return $html;
	}
	
function getBanner($file,$percent_resizing=25) {
	global $set;
	$exp=explode(".",$file);
	$ext = strtolower($exp[count($exp)-1]);
	if ($ext == "swf") $type = "flash";
		else if ($ext == "gif" OR $ext == "jpg" OR $ext == "jpeg" OR $ext == "png") $type = "image";
	if ($type != "flash" AND $type != "image") return false;
	list($width,$height) = @getimagesize($file);

	$w = round((($percent_resizing/100)*$width));
	$h = round((($percent_resizing/100)*$height));
	
	if ($type == "image") {
		if($w == 0 && $h == 0)
			$html = '';
		else
			$html = '<img border="0" src="'.$file.'" width="'.$w.'" height="'.$h.'" alt="" />';
		} else if ($type == "flash") {
		$html = '<OBJECT id="affMV737" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="'.$w.'" height="'.$h.'">
				<param name="movie" value="'.$file.'">
				<param name="wmode" value="transparent">
				<param name="allowScriptAccess" value="always">
				<param name="flashvars" value="creativeURL='.$set->webAddress.'banner_qa.php">
				<embed src="'.$file.'" width="'.$w.'" height="'.$h.'" flashvars="creativeURL='.$set->webAddress.'banner_qa.php" allowScriptAccess="always" NAME="AffMV737" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></embed>
			</object>';
		} else return false;
	return $html;
	}

function listAffiliates($affiliate_id=0,$text=0) {
	global $set;
	if ($text) {
		$ww=mysql_fetch_assoc(function_mysql_query("SELECT id,username FROM affiliates WHERE valid='1' AND id='".$affiliate_id."'",__FILE__,__FUNCTION__));
		$html = $ww['username'];
		} else {
		$qq=function_mysql_query("SELECT id,username FROM affiliates WHERE valid='1' ORDER BY username ASC",__FILE__,__FUNCTION__);
		while ($ww=mysql_fetch_assoc($qq)) $html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $affiliate_id ? 'selected' : '').'>'.$ww['username'].' [Site ID: '.$ww['id'].']</option>';
		}
	return $html;
	}
        
        

function getPayment($paymentID = 0,$aff = 0)
{
	global $set, $appTable;
        
	if (!$paymentID) {
            return false;
        }
        
	$payInfo       = mysql_fetch_assoc(function_mysql_query("SELECT * FROM payments_details WHERE paymentID='".$paymentID."' ORDER BY id DESC LIMIT 1",__FILE__,__FUNCTION__));
	
	$affiliateInfo = getAffiliateRow($payInfo['affiliate_id']);
	
	// $affiliateInfo = dbGet($payInfo['affiliate_id'],"affiliates");
	
	$paidInfo      = mysql_fetch_assoc(function_mysql_query("SELECT * FROM payments_paid WHERE paymentID='".$paymentID."'",__FILE__,__FUNCTION__));
        
	if ($affiliateInfo['paymentMethod'] == "bank") {
		$paymentInfo = '
			'.lang('Payment Method').': <b>'.lang('Wire Transfer').'</b><br />
			'.lang('Bank Name').': <b>'.$affiliateInfo['pay_bank'].'</b><br />
			'.lang('Bank Address').': <b>'.$affiliateInfo['pay_account'].'</b><br />
			'.lang('Bank City').': <b>'.$affiliateInfo['pay_branch'].'</b><br />
			'.lang('Bank Country').': <b>'.$affiliateInfo['pay_email'].'</b><br />
			'.lang('Swift').': <b>'.$affiliateInfo['pay_swift'].'</b><br />
			'.lang('Account Name').': <b>'.$affiliateInfo['pay_firstname'].' '.$affiliateInfo['pay_lastname'].'</b>
			';
		} else if ($affiliateInfo['paymentMethod'] == "moneyBookers") {
		$paymentInfo = '
			'.lang('Payment Method').': <b>MoneyBookers (Skrill)</b><br />
			'.lang('MoneyBookers Address').': <b>'.$affiliateInfo['pay_email'].'</b><br />
			'.lang('MoneyBookers Name').': <b>'.$affiliateInfo['pay_firstname'].' '.$affiliateInfo['pay_lastname'].'</b>
			';
		} else if ($affiliateInfo['paymentMethod'] == "webmoney") {
		$paymentInfo = '
			'.lang('Payment Method').': <b>WebMoney</b><br />
			'.lang('WebMoney Address').': <b>'.$affiliateInfo['pay_email'].'</b><br />
			'.lang('WebMoney Name').': <b>'.$affiliateInfo['pay_firstname'].' '.$affiliateInfo['pay_lastname'].'</b>
			';
		} else if ($affiliateInfo['paymentMethod'] == "neteller") {
		$paymentInfo = '
			'.lang('Payment Method').': <b>Neteller</b><br />
			'.lang('Neteller Address').': <b>'.$affiliateInfo['pay_email'].'</b><br />
			'.lang('Neteller Name').': <b>'.$affiliateInfo['pay_firstname'].' '.$affiliateInfo['pay_lastname'].'</b>
			';
		} else if ($affiliateInfo['paymentMethod'] == "paypal") {
		$paymentInfo = '
			'.lang('Payment Method').': <b>PayPal</b><br />
			'.lang('PayPal Address').': <b>'.$affiliateInfo['pay_account'].'</b><br />
			'.lang('PayPal Name').': <b>'.$affiliateInfo['pay_firstname'].' '.$affiliateInfo['pay_lastname'].'</b>
			';
		}
		else 
			$paymentInfo = '
			'.lang('Payment Method').': <b>'.$affiliateInfo['paymentMethod'].'</b><br />
			'.lang('Address').': <b>'.$affiliateInfo['pay_account'].'</b><br />
			'.lang('Name').': <b>'.$affiliateInfo['pay_firstname'].' '.$affiliateInfo['pay_lastname'].'</b>
			';
	$sub_total = 0;
	$qq=function_mysql_query("SELECT id,name FROM merchants ORDER BY pos",__FILE__,__FUNCTION__);
	$listMerchants .= '<option value="">'.lang('Select Merchant').'</option>';
        
	while ($ww = mysql_fetch_assoc($qq)) {
            $l++;
            $listMerchants .= '<option value="'.$ww['id'].'">'.$ww['name'].'</option>';
            unset($line_total, $totalTraders);
            
            $sql = "SELECT * FROM payments_details "
                    . "WHERE affiliate_id='".$payInfo['affiliate_id']."' AND merchant_id='".$ww['id']."' AND month='".$payInfo['month'] 
                    . "' AND year='".$payInfo['year']."' AND paymentID='".$paymentID."' AND reportType != 'sub' "
                    . "GROUP BY reportType;";

            $progqq = function_mysql_query($sql,__FILE__,__FUNCTION__);

			
			//hide or show hyperlink for affiliate
			
			
			
			
			
                            
                            
							  $revrslt = getAffiliateCPADeal($ww['id'],$set->userInfo['id']);
							$displayHyperLink = 1;
                            if ($revrslt['amount'] > 0 && $set->userInfo['id']>0  && $set->userInfo['level']!='admin' && $set->userInfo['level']!='manager' && $set->hideDrillDownOnInvoiceForAffiliatesWithNonRevDeals==1) 
								{
                                $displayHyperLink = 0;
                            }
							
			// end hide
			
            while ($progww = mysql_fetch_assoc($progqq)) {
                $sql = "SELECT *,SUM(amount) AS amount,COUNT(id) AS total_ftd FROM payments_details "
                        . "WHERE affiliate_id='".$payInfo['affiliate_id']."' AND merchant_id='".$ww['id']."' AND month='".$payInfo['month'] 
                        . "' AND year='".$payInfo['year']."' AND paymentID='".$paymentID 
                        . "' AND reportType='".$progww['reportType']."'";

                $totalTraders = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                $line_total += round($totalTraders['amount'], 2);
                $listPayments .= '<tr style="background: #F1F1F1;">
                        <td align="left"><b>'.strtoupper($ww['name']).'</b></td>
                        <td align="center">'.strtoupper($progww['reportType']).'</td>
                        <td align="center">
                            ' . ($displayHyperLink==1 ?
										'<a href="'.$set->webAddress.($aff ? 'affiliate' : $set->getFolder[1]).'/billing.php?act=details&reportType=' 
                                       . $progww['reportType'].'&status='.$status.'&invoice='.$payInfo['paymentID'].'&merchant_id='.$ww['id'].'" target="_blank">' 
                                       . $totalTraders['total_ftd'] .'</a>' :  $totalTraders['total_ftd']) .'
                            
                        </td>
                        <td align="center">'.price($totalTraders['amount']).'</td>
                </tr>';
                $l++;
            }
            
            $sql = "SELECT * FROM payments_details "
                    . "WHERE affiliate_id='".$payInfo['affiliate_id']."' AND merchant_id='".$ww['id']."' AND month='".$payInfo['month'] 
                    . "' AND year='".$payInfo['year']."' AND paymentID='".$paymentID."' AND reportType != 'sub' AND status='pending' "
                    . "GROUP BY reportType";
            
            $progqq = function_mysql_query($sql,__FILE__,__FUNCTION__);
            
            while ($progww = mysql_fetch_assoc($progqq)) {
                $sql = "SELECT *,SUM(amount) AS amount,COUNT(id) AS total_ftd FROM payments_details "
                        . "WHERE affiliate_id='".$payInfo['affiliate_id']."' AND merchant_id='".$ww['id']."' AND month='".$payInfo['month'] 
                        . "' AND year='".$payInfo['year']."' AND paymentID='".$paymentID 
                        . "' AND reportType='".$progww['reportType']."' AND status='pending'";
                
                $totalTraders = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                $line_total -= round($totalTraders['amount'], 2);
                $listPayments .= '<tr style="background: #FFF;">
                        <td align="left"><b>'.strtoupper($ww['name']).' ('.lang('Pending').')</b></td>
                        <td align="center">'.strtoupper($progww['reportType']).'</td>
                        <td align="center">
                            <a href="' . $set->webAddress.($aff ? 'affiliate' : $set->getFolder[1]).'/billing.php?act=details&reportType=' 
                                       . $progww['reportType'].'&status=pending&invoice='.$payInfo['paymentID'].'&merchant_id='.$ww['id'].'" target="_blank">' 
                                       . $totalTraders['total_ftd'].'
                            </a>
                        </td>
                        <td align="center">'.price('-'.$totalTraders['amount']).'</td>
                </tr>';
                $l++;
            }
            
            $sql = "SELECT * FROM payments_details "
                    . "WHERE affiliate_id='".$payInfo['affiliate_id']."' AND merchant_id='".$ww['id']."' AND month='".$payInfo['month'] 
                    . "' AND year='".$payInfo['year']."' AND paymentID='".$paymentID."' AND reportType != 'sub' AND status='canceled' "
                    . "GROUP BY reportType;";
            
            $progqq = function_mysql_query($sql,__FILE__,__FUNCTION__);
            
            while ($progww = mysql_fetch_assoc($progqq)) {
                $sql = "SELECT *,SUM(amount) AS amount,COUNT(id) AS total_ftd FROM payments_details "
                        . "WHERE affiliate_id='".$payInfo['affiliate_id']."' AND merchant_id='".$ww['id'] 
                        . "' AND month='".$payInfo['month']."' AND year='".$payInfo['year']."' AND paymentID='".$paymentID 
                        . "' AND reportType='".$progww['reportType']."' AND status='canceled';";
                
                $totalTraders = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                $line_total -= round($totalTraders['amount'], 2);
                $listPayments .= '<tr style="background: #F1F1F1;">
                        <td align="left"><b>'.strtoupper($ww['name']).' ('.lang('Canceled').')</b></td>
                        <td align="center">'.strtoupper($progww['reportType']).'</td>
                        <td align="center"><a href="'.$set->webAddress.($aff ? 'affiliate' : $set->getFolder[1]).'/billing.php?act=details&reportType='.$progww['reportType'].'&status=canceled&invoice='.$payInfo['paymentID'].'&merchant_id='.$ww['id'].'" target="_blank">'.$totalTraders['total_ftd'].'</a></td>
                        <td align="center">'.price('-'.$totalTraders['amount']).'</td>
                </tr>';
                $l++;
            }
	    
            $sub_total += $line_total;
        }
        
        
	// Get Bonuses
        $sql = "SELECT * FROM payments_details "
                . "WHERE month='".$payInfo['month']."' AND year='".$payInfo['year']."' AND reportType='bonus' AND paymentID='".$paymentID 
                . " ' AND status='approved';";
        
	$networkBonus = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
        
	if ($networkBonus['id']) {
            $listPayments .= '<tr style="background: #FFF;">
                            <td align="left"><b>'.$networkBonus['reason'].'</b></td>
                            <td align="center">'.price($networkBonus['amount']).'</td>
                            <td align="center">1</td>
                            <td align="center">'.price($networkBonus['amount']).'</td>
                    </tr>';
            $sub_total += $networkBonus['amount'];
        }
        
	$l++;
	
        $sql = "SELECT COUNT(id) AS totalSubTraders, SUM(amount) AS amount FROM payments_details "
                . "WHERE month='".$payInfo['month']."' AND year='".$payInfo['year'] 
                . "' AND reportType='sub' AND paymentID='".$paymentID."';";
        
	$totalSub = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
        
	if ($affiliateInfo['sub_com'] AND $totalSub['amount'] > 0) {
            $listPayments .= '<tr style="background: #F1F1F1;">
                            <td align="left"><b>'.lang('Sub Affiliate Commission').'</b></td>
                            <td align="center">'.$affiliateInfo['sub_com'].'%</td>
                            <td align="center">'.($set->getFolder[1] == "admin" ? '<a href="'.$set->webAddress.($aff ? 'affiliate' : $set->getFolder[1]).'/billing.php?act=details&reportType=sub&invoice='.$payInfo['paymentID'].'&type=sub" target="_blank">'.$totalSub['totalSubTraders'].'</a>' : $totalSub['totalSubTraders']).'</td>
                            <td align="center">'.price($totalSub['amount']).'</td>
                    </tr>';
            $sub_total += $totalSub['amount'];
        }

	$thisimageurl = (strpos($set->billingLogoPath,"/tmp")===false?$set->billingLogoPath:'');
if (empty($thisimageurl))
$thisimageurl = (strpos($set->logoPath,"/tmp")===false?$set->logoPath:'');
if($thisimageurl!="")
$logobillingurl = (strpos(' ' .strtolower($thisimageurl),'http://')>0 ? $thisimageurl :  $set->webAddress.$thisimageurl);
else
	$logobillingurl = "";
// die ($logobillingurl);
        
	$html .= '
	<style type="text/css">
		html,body {
			background: none;
			}
		html,body,table,tr,td {
			font-family: arial;
			}
	</style>
	<div align="center">
	<form action="'.$set->basepage.'" method="post">
	<input type="hidden" name="act" value="paid" />
	<input type="hidden" name="db[paymentID]" value="'.$paymentID.'" />
	<input type="hidden" name="db[month]" value="'.$payInfo['month'].'" />
	<input type="hidden" name="db[year]" value="'.$payInfo['year'].'" />
	<input type="hidden" name="db[affiliate_id]" value="'.$payInfo['affiliate_id'].'" />
	<table width="700" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="3" height="10"></td>
		</tr><tr>
			<td width="55%" align="left">'. (!empty($logobillingurl)?'<img border="0" src="'. $logobillingurl .'" alt="" style="max-height:100px" />':'').'</td>
			<td width="45%" align="left" style="color: #5F5F5F;">
				<span style="font-size: 26px; font-weight: bold;">'.lang('Affiliate Payment Form').'</span><br />
				<span style="line-height: 20px;">
					'.lang('Payment #').' <b>'.$paymentID.'</b><br />
					'.lang('Month').': <b>'.$payInfo['month'].'/'.$payInfo['year'].'</b>
				</span>
			</td>
		</tr><tr>
			<td colspan="3" height="10"></td>
		</tr><tr>
			<td colspan="3" height="1" bgcolor="#EFEFEF"></td>
		</tr><tr>
			<td colspan="3" height="10"></td>
		</tr><tr><td colspan=3>
		<table width=100%  border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left" style="font-size: 11px; line-height: 15px;" valign="top">
				<b>'.lang('Payable to').':</b><br />
				'.$paymentInfo.'
			</td>
			<td align="left" style="font-size: 11px; line-height: 15px;" valign="top">
				<b>'.lang('Affiliate Information').':</b><br />
				'.lang('Affiliate').' <b>#'.$affiliateInfo['id'].'</b><br />
				'.lang('Full Name').': '.$affiliateInfo['first_name'].' '.$affiliateInfo['last_name'].'<br />
				'.lang('Username').': '.$affiliateInfo['username'].'<br />
				'.lang('Country').': '.getCountry($affiliateInfo['country'],1).'<br />
				'.lang('Phone').': '.$affiliateInfo['phone'].'<br />
				'.lang('E-Mail').': '.$affiliateInfo['mail'].'<br />
			</td>
			<td valign="top">
			'. $set->systemCompanyDetails .'
			</td>
			</tr>
			</table>
			</td>
		</tr><tr>
			<td colspan="3" height="10"></td>
		</tr><tr>
			<td colspan="3" height="1" bgcolor="#EFEFEF"></td>
		</tr><tr>
			<td colspan="3" height="10"></td>
		</tr>
	</table>
	<div style="text-align: left; width: 700px;">
		<b>'.lang('Commission Payments').':</b><br /><br />
		<table id="table_commission_payments" width="700" border="1" bordercolor="#B2B2B2" cellpadding="4" cellspacing="0">
			<tr style="background: #C9C9C9;">
				<td align="left"><b>'.lang('Merchant').'</b></td>
				<td align="center"><b>'.lang('Deal Type').'</b></td>
				<td align="center"><b>'.lang('Quantity').'</b></td>
				<td align="center"><b>'.lang('Total Price').'</b></td>
			</tr>'.$listPayments.'<tr>
				<td colspan="3" align="right"><b>'.lang('Sub Total').':</b></td>
				<td align="center"><b>'.price($sub_total).'</b></td>
			</tr>
		</table><br />
                <script>
                    (function() {
                        var isWhite = false,
                            count   = 0,
                            length  = $("#table_commission_payments tbody tr").length;
                        
                        $("#table_commission_payments tbody tr").each(function() {
                            if (count != 0 && count != length - 1) {
                                if (isWhite) {
                                    $(this).css("background", "#F1F1F1");
                                    isWhite = false;
                                } else {
                                    $(this).css("background", "#FFF");
                                    isWhite = true;
                                }
                            }
                            count++;
                        });
                    })();
                </script>
                ';
                
		if ($paidInfo['paid']) {
			if ($paidInfo['extras']) {
                            $extra_exp=explode("[var]",$paidInfo['extras']);
                            for ($i=0; $i<=count($extra_exp)-1; $i++) {
                                $line_exp=explode("|",$extra_exp[$i]);
                                $line_price = round($line_exp[2] * $line_exp[3]);
                                $listExtras .= '<tr style="background: #'.($i % 2 ? 'F1F1F1' : 'FFF').';">
                                <td align="left"><b>'.($line_exp[0] == "0" ? 'OTHER' : strtoupper(listMerchants($line_exp[0],1,1))).'</b></td>
                                <td align="left">'.$line_exp[1].'</td>
                                <td align="center">'.price($line_exp[2]).'</td>
                                <td align="center">'.$line_exp[3].'</td>
                                <td align="center">'.price($line_price).'</td>
                                </tr>';

                                $extra_total += $line_price;
                            }
                            
                            $html .= '<b>'.lang('Extra Payments').':</b><br />
                            <table width="700" border="1" cellpadding="4" cellspacing="0" bordercolor="#B2B2B2">
                                    <tr style="background: #C9C9C9;">
                                            <td width="130" align="left"><b>'.lang('Merchant').'</b></td>
                                            <td width="170" align="left"><b>'.lang('Deal').'</b></td>
                                            <td width="90" align="center"><b>'.lang('Unit Price').'</b></td>
                                            <td width="90" align="center"><b>'.lang('Quantity').'</b></td>
                                            <td width="60" align="center"><b>'.lang('Price').'</b></td>
                                    </tr>'.$listExtras.'
                            </table>';
                        }
                                
                } else {
                    $extra_exp=explode("[var]",$paidInfo['extras']);
                    
                    for ($i=0; $i<=count($extra_exp)-1; $i++) {
			$line_exp=explode("|",$extra_exp[$i]);
			$line_price = round($line_exp[2] * $line_exp[3]);
			if (!$line_exp[3] AND !$line_exp[2]) continue;
			$listExtras .= '<tr style="background: #'.($i % 2 ? 'F1F1F1' : 'FFF').';">
			<td align="left"><select name="extra[merchant_id][]" style="width: 130px;">'.listMerchants($line_exp[0]).'<option value="0" '.(!isset($line_exp[0]) ? 'selected' : '').'>Other</option></select></td>
			<td align="left"><input type="text" name="extra[deal][]" value="'.$line_exp[1].'" style="width: 155px;" /></td>
			<td align="center">'. $set->currency .'  <input type="text" name="extra[unit_price][]" id="unit_price_'.($i+1).'" value="'.$line_exp[2].'" onkeyup="calExtra(\''.($i+1).'\');" style="width: 60px; text-align: center;" /></td>
			<td align="center"><input type="text" name="extra[quantity][]" value="'.$line_exp[3].'" id="quantity_'.($i+1).'" onkeyup="calExtra(\''.($i+1).'\');" style="width: 60px; text-align: center;" /></td>
			<td align="center" id="extraSum_'.($i+1).'">'.price($line_price).'</td>
		</tr>';
			$extra_total += $line_price;
			}
		$html .= '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>
			<td align="left"><b>'.lang('Extra Payments').':</b></td>
			<td align="right"><a href="javascript:addLine();">'.lang('Add Row').' »</a></td>
		</tr></table>
		<br />
		<table width="700" border="1" cellpadding="4" cellspacing="0" bordercolor="#B2B2B2">
			<tr style="background: #C9C9C9;">
				<td width="130" align="left"><b>'.lang('Merchant').'</b></td>
				<td width="170" align="left"><b>'.lang('Deal').'</b></td>
				<td width="90" align="center"><b>'.lang('Unit Price').'</b></td>
				<td width="90" align="center"><b>'.lang('Quantity').'</b></td>
				<td width="60" align="center"><b>'.lang('Price').'</b></td>
			</tr>
		</table>
		<div id="extra_0">
		<table width="700" border="1" cellpadding="4" cellspacing="0" bordercolor="#B2B2B2">
			'.($paidInfo['paid'] ? $listExtras : $listExtras.'<tr style="background: #E3E3E3;">
				<td width="130" align="left"><select name="extra[merchant_id][]" style="width: 130px;">'.$listMerchants.'<option value="0">'.lang('Other').'</option></select></td>
				<td width="170" align="left"><input type="text" name="extra[deal][]" value="" style="width: 155px;" /></td>
				<td width="90" align="center">'. $set->currency .'  <input type="text" name="extra[unit_price][]" id="unit_price_0" value="0" onkeyup="calExtra(\'0\');" style="width: 60px; text-align: center;" /></td>
				<td width="90" align="center"><input type="text" name="extra[quantity][]" value="0" id="quantity_0" onkeyup="calExtra(\'0\');" style="width: 60px; text-align: center;" /></td>
				<td width="60" align="center" id="extraSum_0">'. $set->currency .' 0</td>
			</tr>').'
		</table>
		</div>
		<div id="extraLines"></div>
		<input type="hidden" id="totalLines" value="0" />
		<input type="hidden" name="sub_total" value="'.$sub_total.'" />
		<input type="hidden" name="db[id]" value="'.$paidInfo['id'].'" />
		';
		}
                
                
                $sub_totals = 0;
                $newCredit  = 0;
                
		if (!$paidInfo['paid'] || true) {
                    $sub_totals = $sub_total;
                }
                
		if ($paidInfo['paid']) {
                    $usedCredit = $paidInfo['usedCredit'];
                } else {
                    $credit = $affiliateInfo['credit'];
                    
                    if ($sub_total >= $credit) {
                        $sub_total -= $credit;
                        $newCredit  = 0;
                        $usedCredit = $credit;
                    } else {
                        $sub_total  = 0;
                        $newCredit  = round($credit - $sub_totals, 2);
                        $usedCredit = $sub_totals;
                    }
                }
                
                
                // Check for a "gaps" from previous month.
                // If found, then add them to sub total and current credit accordingly.
                // Begin.
                $sql = "SELECT STR_TO_DATE(CONCAT(`year` , '-' , `month`), '%Y-%m') AS last_date, 
                        IFNULL(`amount_gap_from_previous_month`, 0) AS amount_gap_from_previous_month, 
                        IFNULL(`credit_gap_from_previous_month`, 0) AS credit_gap_from_previous_month  
                        FROM `payments_paid` 
                        WHERE `affiliate_id` = " . $payInfo['affiliate_id'] . "  
                          AND STR_TO_DATE(CONCAT(`year` , '-' , `month`), '%Y-%m') <> 
                              STR_TO_DATE('" . $payInfo['year'] . '-' . $payInfo['month'] . "', '%Y-%m')    
                        ORDER BY last_date DESC 
                        LIMIT 0, 1;";
                
                $arrGaps            = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                $floatGapAmount     = (float) $arrGaps['amount_gap_from_previous_month'];
                $floatGapCredit     = (float) $arrGaps['credit_gap_from_previous_month'];
                $sub_total         += $floatGapAmount;
                $newCredit         += $floatGapCredit;
                $creditDisplayHtml  = ($paidInfo['paid'] ? (float) $paidInfo['creditLeft'] : round($newCredit, 2));
                $strGapAmountHtml   = '';
                $strGapCreditHtml   = empty($floatGapCredit) ? '' : '<tr>
                    <td width="604" align="right"><b>' . lang('Credit from previous month') . ':</b></td>
                    <td align="center" style="font-weight: bold;">
                        <input type="hidden" name="db[credit_gap_from_previous_month]" value="' . $newCredit . '" />' . price($floatGapCredit) . '
                    </td>
                </tr>';
                // End.
                
                
                // Calculate total including extras and gaps.
                // If gaps are still there, then update "amount_gap_from_previous_month", "credit_gap_from_previous_month" accordingly.
                if (($floatGapAmount + $extra_total + $sub_totals) < 0) {
                    $strGapAmountHtml = '
                    <tr>
                        <td width="604" align="right"><b>' . lang('Gap from previous month') . ':</b></td>
                        <td align="center" style="font-weight: bold;">
                            <input type="hidden" name="db[amount_gap_from_previous_month]" 
                                   value="' . ($floatGapAmount + $extra_total + $sub_totals) . '" />' . price($floatGapAmount) . '
                        </td>
                    </tr>';
                } else {
                    $strGapAmountHtml = '
                    <tr>
                        <td width="604" align="right"><b>' . lang('Gap from previous month') . ':</b></td>
                        <td align="center" style="font-weight: bold;">
                            <input type="hidden" name="db[amount_gap_from_previous_month]" value="0" />' . price($floatGapAmount) . '
                        </td>
                    </tr>';
                }
                
                $floatTotalPayment = $floatGapAmount
                                   + $newCredit  // "credit_left" (possibly negative) + "credit_gap" (possibly negative).
                                   + $extra_total
                                   + $sub_totals;
                
		$html .= '<table width="700" border="1" bordercolor="#B2B2B2" cellpadding="4" cellspacing="0">' 
                      .  $strGapAmountHtml . $strGapCreditHtml . '
			<tr>
				<td width="604" align="right"><b>'.lang('Extra Total').':</b></td>
				<td align="center" id="extraPayment" style="font-weight: bold;">'.price($extra_total).'</td>
			</tr><tr>
				<td align="right"><b>'.lang('Commission Total').':</b></td>
				<td align="center" style="font-weight: bold;">'.price($sub_totals).'</td>
			</tr>'.($paidInfo['creditLeft'] > 0 || $paidInfo['usedCredit'] > 0 || $affiliateInfo['credit'] > 0 ? '<tr>
				<td align="right"><b>'.lang('Usage Credit').':</b> <input type="hidden" name="db[usedCredit]" value="'.($paidInfo['paid'] ? $paidInfo['usedCredit'] : $usedCredit).'" /></td>
				<td align="center" style="font-weight: bold;">'.price('-'.($paidInfo['paid'] ? $paidInfo['usedCredit'] : $usedCredit)).'</td>
			</tr><tr>
				<td align="right"><b>'.lang('Credit Left').':</b> <input type="hidden" name="db[creditLeft]" value="' . $creditDisplayHtml . '" /></td>
				<td align="center" style="font-weight: bold;">'.price($creditDisplayHtml).'</td>
			</tr>' : '').'<tr>
				<td align="right"><b>'.lang('Total Payment').':</b> <input type="hidden" id="total" name="db[total]" value="' . $floatTotalPayment . '" /></td>
				<td align="center" id="totalPayment" style="font-weight: bold;">'.price($floatTotalPayment).'</td>
			</tr>
		</table>';
		
                
	if (!$paidInfo['paid']) {
            $html .= '</div>
                <table width="700" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                                <td height="10"></td>
                        </tr><tr>
                                <td height="1" bgcolor="#EFEFEF"></td>
                        </tr><tr>
                                <td height="10"></td>
                        </tr><tr>
                                <td align="left">
                                        '.lang('Transaction ID').': <input type="text" name="db[transaction_id]" value="'.$paidInfo['transaction_id'].'" /><br />
                                        <label><input type="checkbox" name="paid" '.($paidInfo['paid'] ? 'checked' : '').' /> '.lang('Checked if the payment has sent').', '.lang('and send to the affiliate').'.</label><br />
                                        <br />
                                        '.lang('Notes').':<br />
                                        <textarea name="db[notes]" cols="57" rows="4">'.$paidInfo['notes'].'</textarea>
                                </td>
                                <td align="right" valign="bottom"><input type="submit" value="'.lang('Save Changes').'" /></td>
                        </tr>
                </table>
                
                </div>
                </form>
                <script type="text/javascript">
                    function calExtra(id) {
                            var calPrice = parseInt(gid(\'unit_price_\'+id).value)*parseInt(gid(\'quantity_\'+id).value);
                            gid(\'extraSum_\'+id).innerHTML = \''. $set->currency .' \'+calPrice.toFixed(2);
                            reCalc();
                            }
                    function addLine() {
                            var totalLines = parseInt(gid(\'totalLines\').value)+parseInt(1);
                            gid(\'totalLines\').value = totalLines;
                            html = \'<div id="extra_\'+totalLines+\'"><table width="700" border="1" cellpadding="4" cellspacing="0" bordercolor="#B2B2B2"><tr style="background: #E3E3E3;"><td width="130" align="left"><select name="extra[merchant_id][]" style="width: 130px;">'.listMerchants().'<option value="0">Other</option></select></td><td width="170" align="left"><input type="text" name="extra[deal][]" value="" style="width: 155px;" /></td><td width="90" align="center">'. $set->currency .' <input type="text" name="extra[unit_price][]" id="unit_price_\'+totalLines+\'" value="0" onkeyup="calExtra(\\\'\'+totalLines+\'\\\');" style="width: 60px; text-align: center;" /></td><td width="90" align="center"><input type="text" name="extra[quantity][]" value="0" id="quantity_\'+totalLines+\'" onkeyup="calExtra(\\\'\'+totalLines+\'\\\');" style="width: 60px; text-align: center;" /></td><td width="60" align="center" id="extraSum_\'+totalLines+\'">'. $set->currency .' 0</td></tr></table></div>\';

                            var divTag = document.createElement("div");
                            divTag.id = "someID";
                            divTag.setAttribute("align","center");
                            divTag.style.margin = "0px auto";
                            divTag.className ="dynamicDiv";
                            divTag.innerHTML = html;
                            gid(\'extraLines\').appendChild(divTag);
                            }
                    function reCalc() {
                            var intNum = 0;
                            for (i=0; i<=gid(\'totalLines\').value; i++) {
                                    if (gid(\'extraSum_\'+i)) intNum = parseInt(intNum)+parseInt(gid(\'extraSum_\'+i).innerHTML.replace(\''. $set->currency .'  \',\'\'));
                                    }
                            gid(\'extraPayment\').innerHTML = \''. $set->currency .' \'+intNum.toFixed(2);
                            var getTotal = (parseInt(intNum)+parseInt(\''.$sub_total.'\')).toFixed(2);
                            gid(\'total\').value = getTotal;
                            gid(\'totalPayment\').innerHTML = \''. $set->currency .' \'+getTotal;
                            }
                </script>';
            
        } else {
                $html .= '
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                        <td colspan="2" height="10"></td>
                </tr><tr>
                        <td colspan="2" height="1" bgcolor="#EFEFEF"></td>
                </tr><tr>
                        <td colspan="2" height="10"></td>
                </tr><tr>
                        <td width="50%" align="left">'.($paidInfo['transaction_id'] ? '<u>'.lang('Transaction ID').':</u><br /><b>'.$paidInfo['transaction_id'].'</b>' : '').'</td>
                        <td width="50%" align="left">'.($paidInfo['notes'] ? '<u>'.lang('Notes').':</u><br /><b>'.$paidInfo['notes'].'</b>' : '').'</td>
                </tr><tr>
                        <td colspan="2" height="10"></td>
                </tr><tr>
                        <td colspan="2" height="1" bgcolor="#EFEFEF"></td>
                </tr><tr>
                        <td colspan="2" height="10"></td>
                </tr><tr>
                        <td colspan="2" align="left" style="color: #B1B1B1; font-family: Verdana; font-size: 18px; line-height: 26px;">'.lang('Thank you for your business').'<br />'.$set->webTitle.'</td>
                </tr></table>';
        }
        
        return $html;
    }
	

	
	
function longCountry($short="") {
	global $countryLongNameArray;
	if (empty($countryLongNameArray)) {
		if (!$short) return false;
		$ww=mysql_fetch_assoc(function_mysql_query("SELECT countryLONG FROM ip2country WHERE 2=2 and lower(countrySHORT)='".strtolower($short)."' LIMIT 1",__FILE__,__FUNCTION__));
		 $countryLongNameArray[strtolower($short)]= $ww['countryLONG'];
		}
		
		if (empty($short)){
			return $countryLongNameArray;
		}
		else {
			return $countryLongNameArray[strtolower($short)];
		}
	}
	
// for debugging
function baba($str,$dump=0,$todie=0){
	global $baba;
	if($baba){
		if($dump){
			var_dump($str);
			echo '<BR>';
		}else{
			echo $str.'<BR>';
		}
		if($todie){
			die();
		}
	}
}


/**
 * Get total FTDs according to given parameters.
 *
 * @param  string $strFrom
 * @param  string $strTo
 * @param  int    $intAffId 
 * @param  int    $intMerchantId
 * @param  int    $intWalletId
 * @param  int    $intGroupId
 * @param  int    $bannerId
 * @param  int    $profileId
 * @param  string $strTypeOfTimePeriod
 * @param  int    $traderId
 * @return array
 */
function getTotalFtds(
    $strFrom             = '', 
    $strTo               = '', 
    $intAffId            = 0, 
    $intMerchantId       = 0, 
    $intWalletId         = 0, 
    $intGroupId          = -1, 
    $bannerId            = 0, 
    $profileId           = 0, 
    $strTypeOfTimePeriod = '', 
    $traderId            = 0,
	$country = '',
	$isProductSession=false,
	$getQualifiedOnly=0,
	$getByDynamicFilterID=-1
	
)
{
	
	
	
    global $set;
	
    $strSql        = '';
    $strWhereRdate = '';
    $arrResult     = [];
    $tb1_rdate     = ' AND tb1.rdate ';
    
    /**
     * Following block intends to make "search-type" (daily, monthly, weekly, none) filter work.
     */
    /* if (!empty($strTypeOfTimePeriod)) {
        if (false !== strpos($strTypeOfTimePeriod, '=')) {
            $tb1_rdate = ' AND DATE(tb1.rdate) ';
        }
    } */
    
    if (empty($strTypeOfTimePeriod)) {
        $strWhereRdate = !empty($strFrom) && !empty($strTo) ?  " BETWEEN '" . $strFrom . "' AND '" . $strTo . "' " : '';
    } else {
        // $strWhereRdate = $tb1_rdate . $strTypeOfTimePeriod;
        $strWhereRdate =  $strTypeOfTimePeriod;
    }
    
	
		
					// $filterDateBy= " and ((tb1.FTDqualificationDate " . $strWhereRdate . ") OR (tb1.initialftddate " . $strWhereRdate.")) " ;
				if ($getQualifiedOnly)
					 $filterDateBy= " and ((tb1.FTDqualificationDate " . $strWhereRdate . ") ) " ;
				 else
					$filterDateBy= " and (tb1.initialftddate " . $strWhereRdate.") " ;
					// die ($filterDateBy);
					// $select = " tb1.FTDqualificationDate as rdate , ";
				
					
					
					
	// var_dump($mer);
	// die();
    if ($isProductSession==false) {
		$mer  = getMerchants($intMerchantId,0);
									// INNER JOIN data_reg ON tb1.trader_id = data_reg.trader_id AND data_reg.status <> 'frozen' AND data_reg.type <> 'demo' 
		if (empty($set->multiMerchants)) {
									//--INNER JOIN traffic t ON t.uid = tb1.uid
									/*
									////////////////                                experimental!!
									
			$strSql = "SELECT tb1.merchant_id,tb1.banner_id, tb1.amount, tb1.rdate, tb1.affiliate_id, tb1.trader_id, tb1.id as id ,reg.country AS country
							".(strtolower($mer['producttype'])=='forex' ? " , (select count(*) from data_reg dg1 where dg1.trader_id=data_reg.trader_id group by dg1.trader_id) as sub_trader_count " : ""). " FROM data_sales  AS tb1
									INNER JOIN (select country,trader_id,merchant_id from data_reg  where data_reg.status <> 'frozen'  AND data_reg.type <> 'demo' ) as reg  ON tb1.trader_id = reg.trader_id and reg.merchant_id = tb1.merchant_id
									WHERE 1 = 1 " . (empty($intMerchantId) ? '' : " AND tb1.merchant_id = " . $intMerchantId) .  $strWhereRdate .   
													" AND tb1.type = 'deposit' 
													" . (empty($intAffId) ? '' : " AND tb1.affiliate_id = " . $intAffId) . 
													(empty($bannerId) ? '' : ' AND tb1.banner_id = ' . $bannerId) . 
													 (empty($country) ? '' : ' AND tb1.country = "'.$country.'"') . 
													(empty($profileId) ? '' : ' AND tb1.profile_id = ' . $profileId) . 
													(empty($traderId) ? '' : ' AND tb1.trader_id = ' . $traderId) . 
													(($intGroupId==-1) ? '' : ' AND tb1.group_id = '.$intGroupId).
													" AND tb1.trader_id NOT IN 
													(
													 SELECT trader_id FROM data_sales 
													 WHERE "
													 . (empty($intMerchantId) ? '' : " merchant_id = " . $intMerchantId . " AND ") 
													 . (empty($intAffId) ? '' : " affiliate_id = " . $intAffId . " AND ") 
													 . (empty($traderId) ? '' : " trader_id = " . $traderId . " AND ") 
													.(($intGroupId==-1) ? '' : ' group_id = '.$intGroupId . " and " )
													 . " rdate < tb1.rdate AND type = 'deposit' 
													 GROUP BY trader_id
													) 
									GROUP BY tb1.trader_id;";
									// die('this is it: ' . $strSql);
				
				 */
				
				$strSql = "SELECT dr.FTDqualificationDate ,dr.merchant_id,dr.banner_id, ds.amount, dr.initialftddate as rdate , ds.rdate as data_sale_rdate, dr.affiliate_id, dr.trader_id, ds.id as id ,dr.country AS country
							".(strtolower($mer['producttype'])=='forex' ? " , (select count(*) from data_reg dg1 where dg1.trader_id=data_reg.trader_id 
							"
							. (empty($intMerchantId) ? '' : " and merchant_id = " . $intMerchantId . " ") 
													 . (empty($intAffId) ? '' : " AND affiliate_id = " . $intAffId) 
													. (empty($bannerId) ? '' : ' AND banner_id = ' . $bannerId) 
													.(empty($traderId) ? '' : ' AND trader_id = ' . $traderId) 
													 .(empty($country) ? '' : ' AND country = "'.$country.'"') 
													.(empty($profileId) ? '' : ' AND profile_id = ' . $profileId) 
													.(($intGroupId==-1) ? '' : ' AND group_id = '.$intGroupId).
							"
							
							group by dg1.trader_id) as sub_trader_count " : "");
				
				// $strSql = "select tb1.* from data_reg tb1 where 1=1 "  /////////////////  is it right?
				$strSql .= " from (select * from data_reg tb1 where 1=1 and initialftddate>'0000-00-00 00:00:00'  "
													. (empty($intMerchantId) ? '' : " and tb1.merchant_id = " . $intMerchantId . " ") 
													 . (empty($intAffId) ? '' : " AND tb1.affiliate_id = " . $intAffId) 
													. (empty($bannerId) ? '' : ' AND tb1.banner_id = ' . $bannerId) 
													.(empty($traderId) ? '' : ' AND tb1.trader_id = ' . $traderId) 
													.(($getByDynamicFilterID)==-1 ? '' : ' AND tb1.dynamic_filter = ' . $getByDynamicFilterID) 
													 .(empty($country) ? '' : ' AND tb1.country = "'.$country.'"') 
													.(empty($profileId) ? '' : ' AND tb1.profile_id = ' . $profileId) 
													.(($intGroupId==-1) ? '' : ' AND tb1.group_id = '.$intGroupId)
													. " and tb1.status <> 'frozen'  AND tb1.type<>'demo' " 
													.$filterDateBy .
													" ) dr inner join data_sales ds on
													dr.merchant_id = ds.merchant_id and 
													dr.trader_id = ds.trader_id and 
													dr.initialftdtranzid = ds.tranz_id 
													where ds.type='deposit' ";

													// " ) dr inner join data_sales ds on dr.initialftdtranzid = ds.tranz_id and dr.merchant_id = ds.merchant_id  where ds.type='deposit' ";
		} else {
			/* $strSql = "SELECT tb1.merchant_id,tb1.banner_id, tb1.amount, tb1.rdate, tb1.affiliate_id, tb1.trader_id, tb1.id as id ,tb1.country AS country,t.id as traffic_id FROM data_sales AS tb1
									INNER JOIN traffic t ON t.uid = tb1.uid
									 */
			/* $strSql = "SELECT tb1.merchant_id,tb1.banner_id, tb1.amount, tb1.rdate, tb1.affiliate_id, tb1.trader_id, tb1.id as id ,data_reg.country AS country 
									".(strtolower($mer['producttype'])=='forex' ? " , (select count(*) from data_reg dg1 where dg1.trader_id=data_reg.trader_id group by dg1.trader_id) as sub_trader_count " : ""). " FROM data_sales  AS tb1
									INNER JOIN data_reg ON tb1.trader_id = data_reg.trader_id AND data_reg.status <> 'frozen'  AND data_reg.type <> 'demo' 
									WHERE 1 = 1 " . (empty($intWalletId) ? '' : " AND tb1.merchant_id IN (SELECT id FROM merchants AS merchants WHERE merchants.wallet_id = " . $intWalletId . ") ") 
											. $strWhereRdate . " AND tb1.type = 'deposit' 
											" . (empty($intAffId) ? '' : "AND tb1.affiliate_id = " . $intAffId) . 
											(empty($bannerId) ? '' : ' AND tb1.banner_id = ' . $bannerId) . 
											 (empty($country) ? '' : ' AND tb1.country = "'.$country.'"') . 
											(empty($profileId) ? '' : ' AND tb1.profile_id = ' . $profileId) . 
											(empty($traderId) ? '' : ' AND tb1.trader_id = ' . $traderId) . 
											(empty($intGroupId) ? '' : ' AND tb1.group_id = '.$intGroupId).
											" AND tb1.trader_id NOT IN 
													(
													   SELECT trader_id FROM data_sales 
													   WHERE " . (empty($intWalletId) ? '' : " merchant_id IN (
																			SELECT id FROM merchants AS merchants WHERE merchants.wallet_id = " . $intWalletId . "
																	) AND ") . " 
																	rdate < tb1.rdate AND type = 'deposit' 
													   GROUP BY trader_id
													) 
									GROUP BY tb1.trader_id;"; */
									
									
			$strSql = "SELECT tb1.merchant_id,tb1.banner_id, tb1.amount, tb1.rdate, tb1.affiliate_id, tb1.trader_id, tb1.id as id ,reg.country AS country , reg.FTDqualificationDate
									".(strtolower($mer['producttype'])=='forex' ? " , (select count(*) from data_reg dg1 where dg1.trader_id=data_reg.trader_id group by dg1.trader_id) as sub_trader_count " : ""). " FROM data_sales  AS tb1
									INNER JOIN (select country,trader_id,merchant_id from data_reg  where data_reg.status <> 'frozen'  AND data_reg.type <> 'demo' ) as reg  ON tb1.trader_id = reg.trader_id and reg.merchant_id = tb1.merchant_id
									WHERE 2 = 2 " . (empty($intWalletId) ? '' : " AND tb1.merchant_id IN (SELECT id FROM merchants AS merchants WHERE merchants.wallet_id = " . $intWalletId . ") ") 
											. $filterDateBy . " AND tb1.type = 'deposit' 
											" . (empty($intAffId) ? '' : "AND tb1.affiliate_id = " . $intAffId) . 
											(empty($bannerId) ? '' : ' AND tb1.banner_id = ' . $bannerId) . 
											 (empty($country) ? '' : ' AND tb1.country = "'.$country.'"') . 
											 (($getByDynamicFilterID)==-1 ? '' : ' AND tb1.dynamic_filter = ' . $getByDynamicFilterID) .
											(empty($profileId) ? '' : ' AND tb1.profile_id = ' . $profileId) . 
											(empty($traderId) ? '' : ' AND tb1.trader_id = ' . $traderId) . 
											(($intGroupId==-1) ? '' : ' AND tb1.group_id = '.$intGroupId).
											" AND tb1.trader_id NOT IN 
													(
													   SELECT trader_id FROM data_sales 
													   WHERE " . (empty($intWalletId) ? '' : " merchant_id IN (SELECT id FROM merchants AS merchants WHERE merchants.wallet_id = " . $intWalletId . " ) AND ") 
																	 . (empty($intMerchantId) ? '' : " merchant_id = " . $intMerchantId . " AND ") 
													 . (empty($intAffId) ? '' : " affiliate_id = " . $intAffId . " AND ") 
													 .(($getByDynamicFilterID)==-1 ? '' : ' dynamic_filter = ' . $getByDynamicFilterID. " AND " ) 
													 . (empty($traderId) ? '' : " trader_id = " . $traderId . " AND ") 
													. (($intGroupId==-1) ? '' : '  group_id = '.$intGroupId. " and " )
													 
													 
																	. " 
																	rdate < tb1.rdate AND type = 'deposit' 
													   GROUP BY wallet_id , trader_id
													) 
									GROUP BY tb1.wallet_id , tb1.trader_id;";
		}
	}
	else
	{
				
									
/* 			$strSql = "SELECT tb1.product_id,tb1.banner_id, tb1.amount, tb1.rdate, tb1.affiliate_id, tb1.trader_id, tb1.id as id ,data_reg.country AS country
									FROM data_sales  AS tb1
									INNER JOIN data_reg ON tb1.trader_id = data_reg.trader_id AND data_reg.status <> 'frozen' AND data_reg.type <> 'demo' 
									WHERE 1 = 1 " . (empty($intMerchantId) ? '' : " AND tb1.product_id = " . $intMerchantId) .  $strWhereRdate .   
													" AND tb1.type = 'deposit' 
													" . (empty($intAffId) ? '' : " AND tb1.affiliate_id = " . $intAffId) . 
													(empty($bannerId) ? '' : ' AND tb1.banner_id = ' . $bannerId) . 
													 (empty($country) ? '' : ' AND tb1.country = "'.$country.'"') . 
													(empty($profileId) ? '' : ' AND tb1.profile_id = ' . $profileId) . 
													(empty($traderId) ? '' : ' AND tb1.trader_id = ' . $traderId) . 
													(($intGroupId==-1) ? '' : ' AND tb1.group_id = '.$intGroupId).
													" AND tb1.trader_id NOT IN 
													(
													 SELECT trader_id FROM data_sales 
													 WHERE 1=1 " . (empty($intMerchantId) ? '' : " and product_id = " . $intMerchantId . "  ") . " and rdate < tb1.rdate AND type = 'deposit' 
													 GROUP BY product_id , trader_id
													) 
									GROUP BY tb1.product_id , tb1.trader_id;";

 */
									$strSql = "SELECT data_reg.FTDqualificationDate , data_reg.initialftddate as rdate ,tb1.product_id,tb1.banner_id, tb1.amount, tb1.rdate as data_sales_rdate, tb1.affiliate_id, tb1.trader_id, tb1.id as id ,data_reg.country AS country
									FROM data_sales  AS tb1
									INNER JOIN data_reg ON tb1.trader_id = data_reg.trader_id AND initialftddate>'0000-00-00 00:00:00' and data_reg.status <> 'frozen' AND data_reg.type <> 'demo' 
									WHERE 3 = 3 " . (empty($intMerchantId) ? '' : " AND tb1.product_id = " . $intMerchantId) .  str_replace('tb1','data_reg',$filterDateBy) .   
													" AND tb1.type = 'deposit' 
													" . (empty($intAffId) ? '' : " AND tb1.affiliate_id = " . $intAffId) . 
													(empty($bannerId) ? '' : ' AND tb1.banner_id = ' . $bannerId) . 
													 (empty($country) ? '' : ' AND tb1.country = "'.$country.'"') . 
													(empty($profileId) ? '' : ' AND tb1.profile_id = ' . $profileId) . 
													(($getByDynamicFilterID)==-1 ? '' : ' AND tb1.dynamic_filter = ' . $getByDynamicFilterID)  . 
													(empty($traderId) ? '' : ' AND tb1.trader_id = ' . $traderId) . 
													(($intGroupId==-1) ? '' : ' AND tb1.group_id = '.$intGroupId).
													" AND tb1.trader_id NOT IN 
													(
													 SELECT trader_id FROM data_sales 
													 WHERE 1=1 " . (empty($intMerchantId) ? '' : " and product_id = " . $intMerchantId . "  ") . " and rdate < tb1.rdate AND type = 'deposit' 
													 GROUP BY product_id , trader_id
													) 
									GROUP BY tb1.product_id , tb1.trader_id;";
									// die('this is it: ' . $strSql);
	}
	
	
    if (isset($_GET['debug'])) {
		var_dump($set->userInfo['group_id']);
		echo '<br><BR>';
        if (empty($intGroupId)) {
            echo print_r([$strSql], true), '<hr>';
        } else {
            echo print_r([$intGroupId => $strSql], true), '<hr>';
        }
    }
    
	// die ($strSql);
	
	$resource = function_mysql_query($strSql,__FILE__,__FUNCTION__);
	$traders = array();
	
    while ($arrRow = mysql_fetch_assoc($resource)) {
		if (!isset($traders[$arrRow['merchant_id']][$arrRow['trader_id']])){
			$traders[$arrRow['merchant_id']][$arrRow['trader_id']] = $arrRow['trader_id'];
			$arrResult[] = $arrRow;
			unset($arrRow);
		}
    }
    
    return $arrResult;
}


function calculateCasinoRevenue($amount=0,$type=""){
	
	if (empty($type))
		return "";
	
	$recordArry = array();
	$recordArry[$type] = $amount; 
	
	    if (empty($recordArry['bets'])) $recordArry['bets'] = 0;
                    if (empty($recordArry['wins'])) $recordArry['wins'] = 0;
                    if (empty($recordArry['static'])) $recordArry['static'] = 0;
                    if (empty($recordArry['jackpot'])) $recordArry['jackpot'] = 0;
                    if (empty($recordArry['bonuses'])) $recordArry['bonuses'] = 0;
                    if (empty($recordArry['removed_bonuses'])) $recordArry['removed_bonuses'] = 0;
                    
                    return (float) $recordArry['static'] + 
                           (float) $recordArry['bets'] - 
                           (float) $recordArry['wins'] - 
                           (float) $recordArry['jackpot'] - 
                           (float) $recordArry['bonuses'] + 
                           (float) $recordArry['removed_bonuses'];
}
			

function getRevenue(
    $where,
    $merchantType        = 'casino',
    $sumDeposits         = 0,
    $bonus               = 0,
    $withdrawal          = 0,
    $pnl                 = 0,
    $turnoverAmount      = 0,
    $spreadAmount        = 0,
    $formula             = 0,
    $intProfileId        = null,
    $chargeback          = 0
) 
{
	$strSqlProfileId = '';
	
	if (!empty($intProfileId)) {
            $strSqlProfileId = ' AND profile_id = ' . $intProfileId . ' ';
	}
	
	if (empty($where))
	$where = " where 1=1 " ;
	if (strtolower($merchantType) == 'casino' && false) {
		$where .= empty($strSqlProfileId) ? '' : $strSqlProfileId;
                
                $strSqlStatic = "SELECT SUM(amount) AS amount FROM `data_stats` " . $where . " AND type = 'static'";
                //echo 'sql: ', $strSqlStatic, '<hr>';///////////////////////////////////////////////////////////////////////////
		$getStatic = mysql_fetch_assoc(function_mysql_query($strSqlStatic,__FILE__,__FUNCTION__));
		
		//if (empty($getStatic['amount'])) {
                    $strSqlBets = "SELECT SUM(amount) AS amount FROM `data_stats` " . $where . " AND type = 'bets'";
                    $bets = mysql_fetch_assoc(function_mysql_query($strSqlBets,__FILE__,__FUNCTION__));
                    
                    $strSqlWins = "SELECT SUM(amount) AS amount FROM `data_stats` " . $where . " AND type = 'wins'";
                    $wins = mysql_fetch_assoc(function_mysql_query($strSqlWins,__FILE__,__FUNCTION__));
                    
                    $strSqlJackpot = "SELECT SUM(amount) AS amount FROM `data_stats` " . $where . " AND type = 'jackpot'";
                    $jackpot = mysql_fetch_assoc(function_mysql_query($strSqlJackpot,__FILE__,__FUNCTION__));
                    
                    $strSqlBonuses = "SELECT SUM(amount) AS amount FROM `data_stats` " . $where . " AND type = 'bonuses'";
                    $bonuses = mysql_fetch_assoc(function_mysql_query($strSqlBonuses,__FILE__,__FUNCTION__));
                    
                    $strSqlRevenueBonuses = "SELECT SUM(amount) AS amount FROM `data_stats` " . $where . " AND type = 'removed_bonuses'";
                    $removed_bonuses = mysql_fetch_assoc(function_mysql_query($strSqlRevenueBonuses,__FILE__,__FUNCTION__));
                    
                    /* if (isset($_GET['debug_get_revenue'])) {
                        echo print_r([
                            $strSqlStatic, $strSqlBets, $strSqlWins, 
                            $strSqlJackpot, $strSqlBonuses, $strSqlRevenueBonuses,
                        ], true), '<hr>';
                    } */
                    
                    if (empty($bets['amount'])) $bets['amount'] = 0;
                    if (empty($wins['amount'])) $wins['amount'] = 0;
                    if (empty($jackpot['amount'])) $jackpot['amount'] = 0;
                    if (empty($bonuses['amount'])) $bonuses['amount'] = 0;
                    if (empty($removed_bonuses['amount'])) $removed_bonuses['amount'] = 0;
                    
                    //return (float) $bets['amount'] - (float) $wins['amount'] - (float) $jackpot['amount'] - (float) $bonuses['amount'] + (float) $removed_bonuses['amount'];
                    return (float) $getStatic['amount'] + 
                           (float) $bets['amount'] - 
                           (float) $wins['amount'] - 
                           (float) $jackpot['amount'] - 
                           (float) $bonuses['amount'] + 
                           (float) $removed_bonuses['amount'];
		    
		//} else {
                    //return (float) $getStatic['amount'];
		//}
		
	} elseif (strtolower($merchantType) == 'sportsbetting') {
		
		$revSQL = 'SELECT SUM(amount) FROM (SELECT trader_id, type, 
									IF(
									(type=10 OR type=12),
									SUM(amount),
									(IF((type=13 OR type=11 OR type=15),(SUM(amount)*-1),0))) as amount
					FROM data_stats '.$where.$strSqlProfileId.' GROUP BY trader_id,type 

					UNION ALL 
								   
					SELECT trader_id, type, 
								   IF((type = 10 OR type=12 OR type=6), (SUM(amount)*-1), 0) as amount
					FROM data_sales '.$where.$strSqlProfileId.' GROUP BY trader_id,type
								  
					)t1';
		$netRevenue = mysql_result(function_mysql_query($revSQL,__FILE__,__FUNCTION__),0,0);
		
		if(isset($_REQUEST['revQ'])){
			baba($revSQL);
		}
		
		if(isset($_REQUEST['revQp'])){
			if($netRevenue>0){
				baba('<BR><BR>'.$netRevenue.'<BR>'.$revSQL);
			}
		}
		
	// }elseif(strtolower($merchantType)=='forex' OR strtolower($merchantType)=='binary' || strtolower($merchantType)=='binaryoption'){
	}else
		{
		
		$a = $sumDeposits;
		$b = $bonus;
		$c = abs($withdrawal);
		$d = $chargeback;
		$e = $pnl;
		$f = $turnoverAmount;
		$g = $spreadAmount;
		
		$string = str_replace(Array('{deposits}','{bonus}','{withdrawals}','{chargebacks}','{pnl}','{turnover}','{spread}','{chargebacks}','{pnl}','{turnover}','{spread}'),Array('a','b','c','d','e','f','g'),$formula);
		$netRevenue=eval('return ' . preg_replace('/([a-zA-Z])+/', '\$$1', $string) . ';');
		
	}
	
	return $netRevenue;
}


/**
 * Retrieve default deal types values.
 *
 * @param  void
 * @return array
 */
function getMerchantDealTypeDefaults()
{
	$strQuery = "SELECT cpa_amount AS cpa_amount, dcpa_amount AS dcpa_amount, revenue_amount AS revenue_amount, lots_amount AS lots_amount,pnl_amount AS pnl_amount,
						revenue_spread_amount AS revenue_spread_amount, cpl_amount AS cpl_amount, cpi_amount AS cpi_amount,
						cpc_amount AS cpc_amount, cpm_amount AS cpm_amount, min_cpa_amount AS min_cpa_amount, 
						positions_rev_share AS positions_rev_share_amount
				FROM merchants 
				ORDER BY id ASC 
				LIMIT 0, 1;";
	// die ($strQuery);
	$arrDealTypeDefaults = mysql_fetch_assoc(function_mysql_query($strQuery,__FILE__,__FUNCTION__));
	unset($strQuery);
	return $arrDealTypeDefaults;
}



function getProductsDealTypeDefaults($product_id=0)
{
	$strQuery = "SELECT id,cpa AS cpa,  cpllead AS cpllead,cplaccount, min_deposit,cpc,cpi
					FROM products_items where valid !=0 and id = " . $product_id . "
				;";
				/* 
				ORDER BY id ASC 
				LIMIT 0, 1;"; */
				
	
	$arrDealTypeDefaults = mysql_fetch_assoc(function_mysql_query($strQuery,__FILE__,__FUNCTION__));
	unset($strQuery);
	return $arrDealTypeDefaults;
}

/**
 * Returns unquoted string.
 * Capable to deal with single or double quotes.
 *  
 * @param  string $str
 * @return string
 */
function stripQuotes($str)
{
    $strUnquoted = '';
    
    for ($i = 0; $i < strlen($str); $i++) {
        if ('"' != $str[$i] && "'" != $str[$i]) {
            $strUnquoted .= $str[$i];
        }
    }
    return $strUnquoted;
}


/**
 * This function intended to help to deal with "search-type" filter issue.
 * 
 * @param  string $str
 * @return array
 */
function getDateRangeFromSoCalledSearchType($str)
{
    $arrResult = array();
    
    if (false !== strpos($str, '=')) {
        $arrTmp            = explode('=', $str);
        $strDate           = stripQuotes(trim($arrTmp[1]));
        $arrResult['from'] = $strDate . ' 00:00:00';
        $arrResult['to']   = $strDate . ' 23:59:59';
        
    } else {
        $strTmp        = str_replace('between', '|', strtolower($str));
        $strTmp        = str_replace('and',     '|', $strTmp);
        $arrTmp        = explode('|', $strTmp);
        $fromDateFound = false;
        
        foreach ($arrTmp as $strValue) {
            $strValue = trim($strValue);
            
            if (!empty($strValue)) {
                if ($fromDateFound) {
                    $strTo = stripQuotes($strValue);
                    if (false === strpos($strTo, ' ')) {
                        $arrResult['to'] = $strTo . ' 23:59:59';
                    } else {
                        $arrResult['to'] = $strTo;
                    }
                    
                } else {
                    $arrResult['from'] = stripQuotes($strValue);
                    $fromDateFound     = true;
                }
            }
            unset($strValue);
        }
    }
    return $arrResult;
}


/**
 * Returns an array, consists from date-time ranges and an appropriate "revenue share".
 *
 * @param  string $strDateFrom
 * @param  string $strDateTo
 * @param  int    $intMerchantId
 * @param  int    $intAffId
 * @param  array  $arrDealTypeDefaults
 * @param  string $strTypeOfTimePeriod
 * @return array
 */
function getRevenueDealTypeByRange($strDateFrom, $strDateTo, $intMerchantId, $intAffId, $arrDealTypeDefaults, $strTypeOfTimePeriod = '')
{
	
	/* echo 'strDateForm: ' . $strDateFrom.'<br>';
	echo 'strDateTo: ' . $strDateTo.'<br>';
	echo 'intMerchantId: ' . $intMerchantId.'<br>';
	echo 'intAffId: ' . $intAffId.'<br>';
	echo 'arrDealTypeDefaults: ' . $arrDealTypeDefaults.'<br>';
	echo 'strTypeOfTimePeriod: ' . $strTypeOfTimePeriod.'<br>';
	die(); */
    $rdate = ' rdate ';
   
    if (!empty($strTypeOfTimePeriod)) {
        if (false !== strpos($strTypeOfTimePeriod, '=')) {
            $rdate = ' DATE(rdate) ';
        }
    }
    
    $arrSqlResult    = array();
    $arrRangesResult = array();
	
	
	$affiliateDealRevenue =   getAffiliatesDealsREV($intMerchantId, $intAffId  , $strDateFrom,$strDateTo, $strTypeOfTimePeriod);
	
	if (!empty($affiliateDealRevenue))
	foreach ($affiliateDealRevenue as $arrRow) {
        $arrSqlResult[] = array(
                'rdate'   => $arrRow['rdate'], 
                'revenue' => $arrRow['revenue'],
        );
    }
	
    
    if (empty($arrSqlResult) && empty($strTypeOfTimePeriod)) {
        return array(
            array(
                'from'    => $strDateFrom,
                'to'      => $strDateTo,
                'revenue' => $arrDealTypeDefaults['revenue_amount'],
            ),
        );
        
    } elseif (empty($arrSqlResult) && !empty($strTypeOfTimePeriod)) {
        $arrDateRange = getDateRangeFromSoCalledSearchType($strTypeOfTimePeriod);
        
        return array(
            array(
                'from'    => $arrDateRange['from'],
                'to'      => $arrDateRange['to'],
                'revenue' => 0,
            ),
        );
    }
    
/*     
    $sql = "SELECT amount AS revenue FROM affiliates_deals
                WHERE affiliate_id = " . $intAffId 
                . " AND merchant_id = " . $intMerchantId . " AND " . $rdate 
                . (empty($strTypeOfTimePeriod) ? " <= '" . $arrSqlResult[0]['rdate'] . "' " : $strTypeOfTimePeriod) 
                . " AND dealType = 'revenue' 
                ORDER BY rdate LIMIT 0, 1;";
    

    
	$arrFirstAmount = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__)); */
	
	 $arrFirstAmount = getAffiliatesDealsREVLimit($intMerchantId, $intAffId  , $strDateFrom,$strDateTo, $strTypeOfTimePeriod,$arrSqlResult);
		 
	
	
    $firstAmount    = is_null($arrFirstAmount['revenue']) || empty($arrFirstAmount) ? $arrDealTypeDefaults['revenue_amount'] : $arrFirstAmount['revenue'];
    
    $arrRangesResult[] = array(
        'from'    => $strDateFrom,
        'to'      => $arrSqlResult[0]['rdate'],
        'revenue' => $firstAmount,
    );
    
    for ($i = 0; $i < count($arrSqlResult); $i++) {
        $arrRangesResult[] = array(
            'from'    => $arrSqlResult[$i]['rdate'],
            'to'      => (count($arrSqlResult) > $i + 1 ? $arrSqlResult[$i + 1]['rdate'] : $strDateTo),
            'revenue' => $arrSqlResult[$i]['revenue'],
        );
    }
    return $arrRangesResult;
}


/**
 * Calculates amount of clicks and impressions according to given parameters.
 * Returns array with two keys "impressions" and "clicks".
 * 
 * @param  null |string $from
 * @param  null |string $to
 * @param  null |int    $merchantId
 * @param  null |int    $affiliateId
 * @param  null |int    $groupId
 * @param  null |int    $profileId
 * @param  null |string $strTypeOfTimePeriod
 * @return array
 */
function convertTimeStampToUnixTimeStamp($rdate){
	$rdate = new DateTime($rdate);
	return $rdate->getTimestamp();
 }
 
 function getTotalClicksAndImpressions($from = null, $to = null, $merchantId = null, $affiliateId = null, $groupId = null){
	 return getClicksAndImpressions($from,$to,$merchantId,$affiliateId,$groupId,null,null,null,null,null,null,true);
 }
 
 function getClicksAndImpressions($from = null, $to = null, $merchantId = null, $affiliateId = null, $groupId = null, $profileId = null, $strTypeOfTimePeriod = null,$banner_id= null,$country = null,$isProductSession=false,$uid=null,$sumAll = false)
{
// $strWhereRdate = !is_null($from) && !is_null($to) ? " sb.rdate BETWEEN '" . $from . "' AND '" . $to . "' " : ' 1 = 1 ';
	
	// echo convertTimeStampToUnixTimeStamp($from) ." ------- " . convertTimeStampToUnixTimeStamp($to);die;
    $strWhereRdate = !is_null($from) && !is_null($to) ? " sb.unixRdate BETWEEN '" . convertTimeStampToUnixTimeStamp($from) . "' AND '" . convertTimeStampToUnixTimeStamp($to) . "' " : ' 1 = 1 ';
	 
    //$strWhereRdate = !is_null($from) && !is_null($to) ? " sb.rdate BETWEEN '" . $from . "' AND '" . $to . "' " : ' 1 = 1 ';
    /* $sql = 'SELECT IFNULL(SUM(sb.views), 0) AS impressions, IFNULL(SUM(sb.clicks), 0) AS clicks '
            . 'FROM stats_banners AS sb '
            . (is_null($affiliateId) ? '' : 'INNER JOIN affiliates AS aff ON aff.id = sb.affiliate_id AND aff.valid = 1 ')
            . (is_null($groupId) ? '' : "INNER JOIN (SELECT id, title FROM groups where valid = 1 union select 0 as id , 'General' as title) AS groups ON groups.id = sb.group_id ")
            . (is_null($merchantId) ? '' : 'INNER JOIN merchants AS merchants ON merchants.id = sb.merchant_id AND merchants.valid = 1 ')
            . 'WHERE ' . (is_null($groupId) ? '' : ' sb.group_id = ' . $groupId . ' AND ') 
            . (is_null($affiliateId) ? '' : ' sb.affiliate_id = ' . $affiliateId . ' AND ') 
            . (is_null($merchantId) ? '' : ' sb.merchant_id = ' . $merchantId . ' AND ')
            . (is_null($profileId) ? '' : ' sb.profile_id = ' . $profileId . ' AND ')
            . (is_null($strTypeOfTimePeriod) ? $strWhereRdate : " sb.rdate " . $strTypeOfTimePeriod . " ");
    
 */

 if ($isProductSession==true) {
      /*$sql = 'SELECT IFNULL(SUM(sb.views), 0) AS impressions, IFNULL(SUM(sb.clicks), 0) AS clicks '
            . 'FROM traffic AS sb '
            . ' inner JOIN affiliates AS aff ON aff.id = sb.affiliate_id AND aff.valid = 1 '
            . (empty($groupId) ? '' : "INNER JOIN (SELECT id, title FROM groups where valid = 1 union select 0 as id , 'General' as title) AS groups ON groups.id = sb.group_id ")
            . (empty($merchantId) ? '' : 'INNER JOIN products_items AS products ON products.id = sb.product_id AND products.valid != 0 ')
            
            . 'WHERE sb.type="traffic" and ' . (empty($groupId) ? '' : ' sb.group_id = ' . $groupId . ' AND ') 
            . (empty($affiliateId) ? '' : ' sb.affiliate_id = ' . $affiliateId . ' AND ') 
            . (empty($banner_id) ? '' : ' sb.banner_id = ' . $banner_id . ' AND ') 
            . (empty($isProductSession) ? '' : ' sb.product_id = ' . $merchantId . ' AND ')
            . (empty($uid) ? '' : ' sb.uid = "' . $uid . '" AND ')
            . (empty($profileId) ? '' : ' sb.profile_id = ' . $profileId . ' AND ')
			. (empty($country) ? '' : ' sb.country_id ="' . $country . '" AND ')
            
            . (empty($strTypeOfTimePeriod) ? $strWhereRdate : " sb.rdate " . $strTypeOfTimePeriod . " ");  
			*/
			/*
		 function_mysql_query("CREATE TEMPORARY TABLE tmp_traffic AS 
			SELECT sb.affiliate_id, sb.views, sb.clicks, sb.product_id FROM traffic AS sb "
			. ' inner JOIN affiliates AS aff ON aff.id = sb.affiliate_id AND aff.valid = 1 '
            . (empty($groupId) ? '' : "INNER JOIN (SELECT id, title FROM groups where valid = 1 union select 0 as id , 'General' as title) AS groups ON groups.id = sb.group_id ")
            . (empty($merchantId) ? '' : 'INNER JOIN products_items AS products ON products.id = sb.product_id AND products.valid != 0 ')
            . 'WHERE sb.type="traffic" and ' . (empty($groupId) ? '' : ' sb.group_id = ' . $groupId . ' AND ') 
            . (empty($affiliateId) ? '' : ' sb.affiliate_id = ' . $affiliateId . ' AND ') 
            . (empty($banner_id) ? '' : ' sb.banner_id = ' . $banner_id . ' AND ') 
            . (empty($isProductSession) ? '' : ' sb.product_id = ' . $merchantId . ' AND ')
            . (empty($uid) ? '' : ' sb.uid = "' . $uid . '" AND ')
            . (empty($profileId) ? '' : ' sb.profile_id = ' . $profileId . ' AND ')
			. (empty($country) ? '' : ' sb.country_id ="' . $country . '" AND ')
            
            . (empty($strTypeOfTimePeriod) ? $strWhereRdate : " sb.rdate " . $strTypeOfTimePeriod));
		
			function_mysql_query("CREATE INDEX idx_tmp_traffic_1 ON tmp_traffic (affiliate_id);");
			function_mysql_query("CREATE INDEX idx_tmp_traffic_2 ON tmp_traffic (product_id);");
			
			$sql = "SELECT tra.affiliate_id, SUM(tra.views) AS impressions, SUM(tra.clicks) AS clicks FROM tmp_traffic AS tra  "
			. ' inner JOIN affiliates AS aff ON aff.id = tra.affiliate_id AND aff.valid = 1 '
            . (empty($groupId) ? '' : " INNER JOIN (SELECT id, title FROM groups where valid = 1 union select 0 as id , 'General' as title) AS groups ON groups.id = tra.group_id ")
            . (empty($merchantId) ? '' : ' INNER JOIN products_items AS products ON products.id = tra.product_id AND products.valid != 0 ')
            . 'WHERE 1=1 '
            . (empty($affiliateId) ? '' : ' AND tra.affiliate_id = ' . $affiliateId) 
            . (empty($banner_id) ? '' : ' AND tra.banner_id = ' . $banner_id) 
            . (empty($isProductSession) ? '' : ' AND tra.product_id = ' . $merchantId)
            . (empty($uid) ? '' : ' AND tra.uid = "' . $uid . '"')
            . (empty($profileId) ? '' : ' AND tra.profile_id = ' . $profileId)
			. (empty($country) ? '' : ' AND tra.country_id ="' . $country);
			 //die ($sql);
		  */
			 
			 	
		 $sql = " 
		SELECT sb.affiliate_id, sum(sb.views) as impressions , SUM(sb.clicks) as clicks , sb.merchant_id,sb.group_id,sb.banner_id,sb.profile_id,sb.country_id,sb.product_id FROM traffic AS sb "
		. (empty($affiliateId) ? '' : 'INNER JOIN affiliates AS aff ON aff.id = sb.affiliate_id AND aff.valid = 1 ')
		. 'WHERE sb.type="traffic" and ' . (empty($groupId) ? '' : ' sb.group_id = ' . $groupId . ' AND ') 
		. (!empty($affiliateId) && $affiliateId>0 ?  ' sb.affiliate_id = ' . $affiliateId . ' AND ' : ' ') 
		. (empty($banner_id) ? '' : ' sb.banner_id = ' . $banner_id . ' AND ') 
		// . (empty($merchantId) ? '' : ' sb.merchant_id = ' . $merchantId . ' AND ')
		. (empty($profileId) ? '' : ' sb.profile_id = ' . $profileId . ' AND ')
		. (empty($uid) ? '' : ' sb.uid = "' . $uid . '" AND ')
		. (empty($isProductSession) ? '' : ' sb.product_id = ' . $merchantId . ' and ')
		. (empty($country) ? '' : ' sb.country_id ="' . $country . '" AND ')
		. (empty($strTypeOfTimePeriod) ? $strWhereRdate : " sb.rdate " . $strTypeOfTimePeriod . " ")
		. ($affiliateId=='-1' ? ' group by sb.affiliate_id ' : '') . ";"
		;
 }
 else {
	      
		  
		  
		  
		  /*  $sql = 'SELECT sb.affiliate_id,IFNULL(SUM(sb.views), 0) AS impressions, IFNULL(SUM(sb.clicks), 0) AS clicks '
            . 'FROM traffic AS sb '
            . (empty($affiliateId) ? '' : 'INNER JOIN affiliates AS aff ON aff.id = sb.affiliate_id AND aff.valid = 1 ')
            . (empty($groupId) ? '' : "INNER JOIN (SELECT id, title FROM groups where valid = 1 union select 0 as id , 'General' as title) AS groups ON groups.id = sb.group_id ")
            . (empty($merchantId) ? '' : 'INNER JOIN merchants AS merchants ON merchants.id = sb.merchant_id AND merchants.valid = 1 ')
            . 'WHERE sb.type="traffic" and ' . (empty($groupId) ? '' : ' sb.group_id = ' . $groupId . ' AND ') 
            . (!empty($affiliateId) && $affiliateId>0 ?  ' sb.affiliate_id = ' . $affiliateId . ' AND ' : ' ') 
            . (empty($banner_id) ? '' : ' sb.banner_id = ' . $banner_id . ' AND ') 
            . (empty($merchantId) ? '' : ' sb.merchant_id = ' . $merchantId . ' AND ')
            . (empty($profileId) ? '' : ' sb.profile_id = ' . $profileId . ' AND ')
			. (empty($country) ? '' : ' sb.country_id ="' . $country . '" AND ')
            . (empty($strTypeOfTimePeriod) ? $strWhereRdate : " sb.rdate " . $strTypeOfTimePeriod . " ")
			. ($affiliateId=='-1' ? ' group by sb.affiliate_id ' : '');  */

/* 
// sujeet solution
			
		 function_mysql_query("CREATE TEMPORARY TABLE tmp_traffic AS 
		SELECT sb.affiliate_id, sb.views, sb.clicks, sb.merchant_id,sb.group_id,sb.banner_id,sb.profile_id,sb.country_id,sb.product_id FROM traffic AS sb "
		//. (empty($affiliateId) ? '' : 'INNER JOIN affiliates AS aff ON aff.id = sb.affiliate_id AND aff.valid = 1 ')
		. (empty($groupId) ? '' : "INNER JOIN (SELECT id, title FROM groups where valid = 1 union select 0 as id , 'General' as title) AS groups ON groups.id = sb.group_id ")
		. (empty($merchantId) ? '' : 'INNER JOIN merchants AS merchants ON merchants.id = sb.merchant_id AND merchants.valid = 1 ')
		. 'WHERE sb.type="traffic" and ' . (empty($groupId) ? '' : ' sb.group_id = ' . $groupId . ' AND ') 
		. (!empty($affiliateId) && $affiliateId>0 ?  ' sb.affiliate_id = ' . $affiliateId . ' AND ' : ' ') 
		. (empty($banner_id) ? '' : ' sb.banner_id = ' . $banner_id . ' AND ') 
		. (empty($merchantId) ? '' : ' sb.merchant_id = ' . $merchantId . ' AND ')
		. (empty($profileId) ? '' : ' sb.profile_id = ' . $profileId . ' AND ')
		. (empty($country) ? '' : ' sb.country_id ="' . $country . '" AND ')
		. (empty($strTypeOfTimePeriod) ? $strWhereRdate : " sb.rdate " . $strTypeOfTimePeriod . " ")
		//. ($affiliateId=='-1' ? ' group by sb.affiliate_id ' : '') . ";"
		);
		
		function_mysql_query("CREATE INDEX idx_tmp_traffic_1 ON tmp_traffic (affiliate_id);");
		function_mysql_query("CREATE INDEX idx_tmp_traffic_2 ON tmp_traffic (merchant_id);"); 
 
	$sql = "SELECT tra.affiliate_id,SUM(tra.views) AS impressions, SUM(tra.clicks) AS clicks FROM tmp_traffic AS tra"
		. (empty($affiliateId) ? '' : ' INNER JOIN affiliates AS aff ON aff.id = tra.affiliate_id AND aff.valid = 1 ')
		. (empty($groupId) ? '' : " INNER JOIN (SELECT id, title FROM groups where valid = 1 union select 0 as id , 'General' as title) AS groups ON groups.id = tra.group_id ")
		. (empty($merchantId) ? '' : ' INNER JOIN merchants AS merchants ON merchants.id = tra.merchant_id AND merchants.valid = 1 ')
		. " WHERE 1 = 1 "
		. (empty($groupId) ? '' : ' AND tra.group_id = ' . $groupId) 
		. (!empty($affiliateId) && $affiliateId>0 ?  ' AND tra.affiliate_id = ' . $affiliateId : ' ') 
		. (empty($banner_id) ? '' : ' AND tra.banner_id = ' . $banner_id ) 
		. (empty($merchantId) ? '' : ' AND tra.merchant_id = ' . $merchantId)
		. (empty($profileId) ? '' : ' AND tra.profile_id = ' . $profileId)
		. (empty($country) ? '' : ' AND tra.country_id ="' . $country . '"')
		. ($affiliateId=='-1' ? ' group by tra.affiliate_id ' : '') . ";";
		*/
		
		
		if ($sumAll){
		 $sql = " 
		SELECT sb.affiliate_id, sum(sb.views) as impressions , SUM(sb.clicks) as clicks , sb.merchant_id,sb.group_id,sb.banner_id,sb.profile_id,sb.country_id,sb.product_id FROM traffic AS sb "
		. (empty($affiliateId) ? '' : 'INNER JOIN affiliates AS aff ON aff.id = sb.affiliate_id AND aff.valid = 1 ')
		. 'WHERE sb.type="traffic" and ' . (empty($groupId) ? '' : ' sb.group_id = ' . $groupId . ' AND ') 
		. (!empty($affiliateId) && $affiliateId>0 ?  ' sb.affiliate_id = ' . $affiliateId . ' AND ' : ' ') 
		. (empty($banner_id) ? '' : ' sb.banner_id = ' . $banner_id . ' AND ') 
		. (empty($merchantId) ? '' : ' sb.merchant_id = ' . $merchantId . ' AND ')
		. (empty($profileId) ? '' : ' sb.profile_id = ' . $profileId . ' AND ')
		. (empty($country) ? '' : ' sb.country_id ="' . $country . '" AND ')
		. (empty($strTypeOfTimePeriod) ? $strWhereRdate : " sb.rdate " . $strTypeOfTimePeriod . " ")
		. ($affiliateId=='-1' ? ' group by sb.affiliate_id ' : '') . ";"
		;
		
		}
		else
		if ($_GET['nnir']==1 || true ){
		
		$strWhereRdate = !is_null($from) && !is_null($to) ? " sb.unixRdate BETWEEN '" . convertTimeStampToUnixTimeStamp($from) . "' AND '" . convertTimeStampToUnixTimeStamp($to) . "' and " : ' 1 = 1 and ';
		
		
		$where = ""
		. (empty($strTypeOfTimePeriod) ? $strWhereRdate : " sb.rdate " . $strTypeOfTimePeriod . " and ")
		. (empty($merchantId) ? '' : ' sb.merchant_id = ' . $merchantId . ' AND ')
		. (!empty($affiliateId) && $affiliateId>0 ?  ' sb.affiliate_id = ' . $affiliateId . ' AND ' : ' ') 
		. (empty($groupId) ? '' : ' sb.group_id = ' . $groupId . ' AND ') 
		. (empty($banner_id) ? '' : ' sb.banner_id = ' . $banner_id . ' AND ') 
		. (empty($uid) ? '' : ' sb.uid = "' . $uid . '" AND ')
		. (empty($profileId) ? '' : ' sb.profile_id = ' . $profileId . ' AND ')
		. (empty($country) ? '' : ' sb.country_id ="' . $country . '" AND ')
		;
		
		// echo $where .'<br><Br>';
		$limit = " limit 12000 ";
		 $sql = "
		
SELECT affiliate_id,  impressions, Clicks as clicks   from (		
		 
		 SELECT DISTINCT ( affiliate_id ) AS affiliate_id 
        FROM   traffic AS sb 
        WHERE 1=1 and
		
		" . $where ." 
		
				 1=1
		 and 
		 sb.type='traffic' 
		 
		 ".$limit."
		 ) allTraficAffiliates 
		 
		 left join 
		 
		 (

SELECT traffic.* 
FROM   (SELECT DISTINCT ( affiliate_id ) AS affiliate_id 
        FROM   traffic AS sb 
        WHERE 1=1 and
		
		" . $where ." 
		
				 sb.views = 1
		 and 
		 sb.type='traffic' 
		 ".$limit."
		 ) affiliates 
       INNER JOIN (SELECT Count(sb.views) AS impressions, 
                          affiliate_id 
                   FROM   traffic AS sb 
                WHERE 1=1 and
		
		" . $where ." 
		
		 sb.views = 1
		 and 
		 sb.type='traffic' 
                   GROUP  BY affiliate_id) traffic using (affiliate_id)
				   ".$limit."
    
    ) imp
	
	using (affiliate_id)
    
    LEFT join 

    
    (
        SELECT traffic.* 
FROM   (SELECT DISTINCT ( affiliate_id ) AS affiliate_id 
        FROM   traffic AS sb 
          WHERE 1=1 and
		
		" . $where ." 
		
			 sb.clicks = 1
		 and 
		 sb.type='traffic' 
		 ".$limit."
		 ) affiliates 
       INNER JOIN (SELECT Count(sb.clicks) AS Clicks, 
                          affiliate_id 
                   FROM   traffic AS sb 
                      WHERE 1=1 and
		
		" . $where ." 
		
			 sb.clicks = 1
		 and 
		 sb.type='traffic' 
		 
                   GROUP  BY affiliate_id) traffic using (affiliate_id)
				   ".$limit."
        
        )
        clicks
        using (affiliate_id)
		";
		


		// die ($sql);
		}
		
	


 }
	//die($sql);
    if (isset($_GET['qa'])) {
        echo print_r([$sql], true), '<hr>';
    }
    // die ($sql);
	if ($affiliateId=="-1"){
			$rscc = function_mysql_query($sql,__FILE__,__FUNCTION__);
		
			$arr = array();
			while($row=mysql_fetch_assoc($rscc)){
				$arr[$row['affiliate_id']] = $row;
			}
			return $arr;
	}
	else{
		return mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
	}
}


/**
 * Extract amounts according to deal type.
 * 
 * @param  array $arrRow
 * @param  array $arrDealTypeDefaults
 * @return array
 */
 
 function meir($array,$stop=0){
	 echo "<pre>".print_r($array, true)."</pre>";
	 if ($stop==1)
		 die();
 }
 
function extractDealTypes($arrRow, $arrDealTypeDefaults, $test = '')
{
	global $existingDealTypesForAffiliateArray;
  
   if (!isset($arrRow['rdate'])) {
        return array();
    }
    
    $FinalAffiliateGeneratedDeal               = [];
    $affiliateJustActiveDealsAccordingToDateArr      = [];
    $arrDealTypesFromDefault = [];
    $arrDealTypes            = ['min_cpa', 'cpi','cpa', 'dcpa','pnl','lots', 'cpc', 'cpl', 'cpm', 'revenue', 'revenue_spread', 'positions_rev_share', 'tier'];
    $strRDate                = $arrRow['rdate'];
    $intAffId                = $arrRow['affiliate_id'];
    $intMerchantId           = $arrRow['merchant_id'];
    
	if (!isset($existingDealTypesForAffiliateArray[$intMerchantId][$intAffId])) {
		
		getExistingDealTypesForAffiliateArray($intMerchantId,$intAffId);
		
	}
				


    // find all relevants deals for this event.
	foreach ($arrDealTypes as $strDealType) {
		unset ($affiliateDealByTypeArr);
		
		$exsitingDeal = $existingDealTypesForAffiliateArray[$intMerchantId][$intAffId][$strDealType];
	
	
	
			    
			   
	
	
	if (isset($exsitingDeal)){
			// no need to check this deal... its not active for that date									
			
			$affiliateDealByTypeArr = getAffiliateDealByType($intMerchantId, $intAffId  , $strRDate,$strDealType);
			
		
			
			
			/* if ($test==5 && $_GET['nir'] && $strRDate>'2016-10-25 10:12:00'){
				echo '<Br>';
				echo '-----------------';
				var_dump($affiliateDealByTypeArr);
				echo '<Br>';
				var_dump($arrRow);
				echo '<Br>';
			echo '<Br>'.$strDealType.' : ' . $affiliateDealByTypeArr['amount'].'<Br>';
			} */
			 
		}

        if (empty($affiliateDealByTypeArr)) {
			
			$arrDealTypesFromDefault[$strDealType]['amount'] = $arrDealTypeDefaults[$strDealType . '_amount'];
			$arrDealTypesFromDefault[$strDealType]['rdate'] = '0000-00-00 00:00:00';
			// var_dump($arrDealTypesFromDefault);
			// die();
        } else {
		
			
			// echo '<br>this is: <br>';
			// var_dump($affiliateDealByTypeArr);
			// echo '<br>';
	
	
			if (count($affiliateDealByTypeArr)==1 && !empty($affiliateDealByTypeArr[0]['rdate']))
				$affiliateDealByTypeArr= $affiliateDealByTypeArr[0];
			
         //   if (count($affiliateDealByTypeArr)>0){
			//	$affiliateDealByTypeArr = $affiliateDealByTypeArr[0];
			//}
			
			$affiliateJustActiveDealsAccordingToDateArr[$strDealType]['amount'] = $affiliateDealByTypeArr['amount'];
			$affiliateJustActiveDealsAccordingToDateArr[$strDealType]['rdate'] = $affiliateDealByTypeArr['rdate'];
			
			
			
        }
        unset($strDealType);
    }
    
	/* if ($test==5 && $_GET['nir']){
		var_dump( $affiliateJustActiveDealsAccordingToDateArr);
		die ();
	} */
	
	// echo '<br>or this: <Br>';
	// var_dump($affiliateJustActiveDealsAccordingToDateArr);
	// echo '<br>';
	
    if (empty($affiliateJustActiveDealsAccordingToDateArr)) {
		/* if ($test==5 && $_GET['nir'] && $strRDate>'2016-10-25 10:12:00'){
		} */

		unset($FinalAffiliateGeneratedDeal);
		$FinalAffiliateGeneratedDeal = array();
        $FinalAffiliateGeneratedDeal= $arrDealTypesFromDefault;
    } else {



        foreach ($arrDealTypes as $strDealType) {
	

	/* if ($test==5 && $_GET['nir'] && $strRDate>'2016-10-25 10:12:00'){
			 echo '<Br>--------------<Br>deals found :   <br>';
			 var_dump($strDealType);
			 echo '<br>';
			 var_dump($affiliateJustActiveDealsAccordingToDateArr);
			 echo '<br>';
			 echo '-------------------<br>';
		 } */
		 
		 
            if (array_key_exists($strDealType, $affiliateJustActiveDealsAccordingToDateArr)) {
                $FinalAffiliateGeneratedDeal[$strDealType] = $affiliateJustActiveDealsAccordingToDateArr[$strDealType];
				
            } else {
				
                $FinalAffiliateGeneratedDeal[$strDealType] = 0;
            }
			unset($strDealType);
        }
    }

	
	 /* if ($test==5 && $_GET['nir'] && $strRDate>'2016-10-25 10:12:00'){
				echo 'mer: ' . $intMerchantId . ' .      aff: ' . $intAffId . '            date: ' . $strRDate . '       deal: ' . $strDealType.'<br>';
		var_dump( $exsitingDeal);
		echo '<br><br>';
		var_dump( $FinalAffiliateGeneratedDeal);
		echo '<br><br>--------------<br>';
		// die();
		// die ();
	}
 */

	// $affiliateRow =  function_mysql_query('SELECT id,qualify_amount,qualify_type FROM affiliates WHERE id = ' . $intAffId);
	// $FinalAffiliateGeneratedDeal['qualification'] = $affiliateRow;meir($FinalAffiliateGeneratedDeal);
			
			
		
		
    return $FinalAffiliateGeneratedDeal;
}


/**
 * Takes a part in FTD amount calculation.
 * Signature:   function ftdAmount($intMerchantId,    $arrTotalFtdRow, $arrDealTypeDefaults, &$strFtdUsers, &$intCurrentFtdAmount, &$intNewFtdCounter)
 * Call sample:          ftdAmount($merchantww['id'], $totalftd,       $arrDealTypeDefaults, $ftdUsers,     $ftd_amount['amount'], $new_ftd)
 * 
 * @param  int     $intMerchantId
 * @param  array   $arrTotalFtdRow
 * @param  array   $arrDealTypeDefaults
 * @param  string &$strFtdUsers
 * @param  int    &$intCurrentFtdAmount
 * @param  int    &$intNewFtdCounter
 * @return void
 */
function hasValidDate($string=""){
if (empty($string))
	return NULL;

if ($string=='0000-00-00 00:00:00')
	return false;

return true;
}
 
 function getFtdByDealType($intMerchantId, $arrTotalFtdRow, $arrDealTypeDefaults, &$strFtdUsers, &$intCurrentFtdAmount, &$intNewFtdCounter,$qualifiedOnly=false)
{
	global $traderIssue ;
	//$counter=0; $traderidtotrack = $arrTotalFtdRow['trader_id'] ==3254653;
	
	if (!isset($arrTotalFtdRow['merchant_id']) || $arrTotalFtdRow['merchant_id']==null || $arrTotalFtdRow['merchant_id']=="NULL" || empty($arrTotalFtdRow['merchant_id']) )
		$arrTotalFtdRow['merchant_id'] = $intMerchantId;
	
	$ShowQualifiedOnly = false;
	if ($qualifiedOnly && hasValidDate($arrTotalFtdRow['FTDqualificationDate']))
		$ShowQualifiedOnly = true;
	else if(!$qualifiedOnly){
		$ShowQualifiedOnly = true;
	}
	
		
	
	if ($ShowQualifiedOnly){
		$arrTotalFtdRow['merchant_id'] = $intMerchantId;
		
		
		if ($qualifiedOnly){
		 $arrTotalFtdRow['rdate'] = $arrTotalFtdRow['FTDqualificationDate'] ;  // if ftd is qualified then its real ftd date should be the qualified date.                              
		}
		else
			$arrTotalFtdRow['rdate'] = $arrTotalFtdRow['rdate'] ;  	
		
	
		// echo $arrTotalFtdRow['trader_id'].'<br>';
		
		$arrResults     = extractDealTypes($arrTotalFtdRow, $arrDealTypeDefaults);

	
		if (isset($arrTotalFtdRow[$intMerchantId]['trader_id'])) {
			global $ftdByDealTypeArray;
			if(isset($ftdByDealTypeArray[$arrTotalFtdRow[$intMerchantId]['trader_id']])){
				$arrResult = $ftdByDealTypeArray[$arrTotalFtdRow[$intMerchantId]['trader_id']];
			}
			else{
				$strSql    = "SELECT status FROM data_reg WHERE merchant_id = ". $intMerchantId ." and trader_id = '" . $arrTotalFtdRow['trader_id'] . "'";
				$arrResult = mysql_fetch_assoc(function_mysql_query($strSql,__FILE__,__FUNCTION__));
				$ftdByDealTypeArray[$arrTotalFtdRow[$intMerchantId]['trader_id']] = $arrResult;
			}
			if ('frozen' == $arrResult['status']) {
				unset($arrResults, $arrResult, $strSql);
				$traderIssue = "Frozen";
				return;
			}
		}
		
		
		
	// echo $arrTotalFtdRow['trader_id'].'<br>';

		$ftdAmount =$arrTotalFtdRow['amount'];
		$minimumAmount =  getValue($arrResults['min_cpa']);
		
		
		if ($_GET['nnir']==3){
 		echo '<br><br>arrTotalFtdRow<br>';
		var_dump($arrTotalFtdRow);
 		echo '<br><br>Deals<br>';
		var_dump($arrResults);
 		echo '<br><br>';
 		echo '<br><br>';
	echo 'ftdamount: ' . $ftdAmount . '     >=      minimumAmount:   '  . $minimumAmount['amount'];
		echo '<br>-------------------<br><br>'; 
		}
		
		if ($arrTotalFtdRow['amount'] >= $minimumAmount['amount']) {
			if ($strFtdUsers != '') {
				$strFtdUsers .= ',';
			}
		
		
			$strFtdUsers         .= $arrTotalFtdRow['trader_id'];
			$intCurrentFtdAmount += $arrTotalFtdRow['amount'];
			$intNewFtdCounter++;
			unset($arrResults, $arrResult, $strSql);
		}else {
			$traderIssue = "Below minimum deposit";
		}
	}
	
}

/**
 * Returns one of the following:
 *     1. If bill exists, returns the bill, with appropriate details (approved?, ...).
 *     2. Returns '0', if '$set->qualify_amount' > amount of new traders.
 */
 /*
function getCom(
		$aff_id, 
		$merchant_id, 
		$trader_id           = '', 
		$sdate               = '', 
		$edate               = '', 
		$dealType            = 'deal', 
		$default             = 0, 
		$billing             = 0, 
		$onlyRev             = 0, 
		$banner_id           = 0, 
		$group_id            = -1, 
		$arrDealTypesAmounts = array(),
		$ftdRow              = null,
		$intProfileId        = null,
		$netRevenue          = null
)
{

	
	$strSqlProfileId = '';
	
	if (!is_null($intProfileId)) {
		$strSqlProfileId = ' AND profile_id = ' . $intProfileId . ' ';
	}
        
	global $set, $test2;
	
	$boolInDebugMode = isset($_GET['debug']);
	
	
	if (!$aff_id AND !$trader_id AND !$dealType) return false;
	if (!$merchant_id) return false;
	if (!$sdate) $sdate = date("Y-m-d");
	if (!$edate) $edate = date("Y-m-d");
	
	// Checking if this trader has paid
        $sqlOldPayment = "SELECT id, status, reportType, amount FROM payments_details 
                          WHERE trader_id = '" . $trader_id . "' AND affiliate_id = '" . $aff_id . "' AND merchant_id = '" . $merchant_id . "' 
                                AND month = '" . date('m', strtotime($sdate)) . "' AND year = '" . date('Y', strtotime($sdate)) . "'";
        
	$oldPayment = mysql_fetch_assoc(function_mysql_query($sqlOldPayment,__FILE__));
	if ($oldPayment['id']) {
            if ($oldPayment['status'] == "approved") {
                return ($billing ? $oldPayment['reportType'] . '|' : '') . $oldPayment['amount'];
            } else { 
                return ($billing ? $oldPayment['reportType'] . '|' : '') . '0';
            }	
	}
	
        
	$getDefault = null;
	$mt         = null;
	$arrRow = getMerchants($merchant_id,0);
	// $resource   = function_mysql_query( 'SELECT * FROM merchants WHERE id = ' . $merchant_id,__FILE__);
	{
	// while ($arrRow = mysql_fetch_assoc($resource)) {
		// There is only one iteration in this loop.
		$mt         = strtolower($arrRow['producttype']);
		$getDefault = $default == 0 ? $default : $arrRow;
		unset($arrRow);
	}
	
        
	// Check Qualify Lead by Volume OR Trades, Return 0 If not Qualify
	if ($set->qualify_amount > 0) {
		if ($mt == 'forex') {
			$gvQ = "SELECT COUNT(id) AS numTrades, SUM(amount) AS amount FROM data_stats 
					WHERE merchant_id = '" . ($merchant_id) . "' and type = 'position' AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $aff_id . "'" . $strSqlProfileId;
		} else {
			// The 'type' has changed from 'position' to 'volume'.
			$gvQ = "SELECT COUNT(id) AS numTrades, SUM(amount) AS amount FROM data_sales 
					WHERE merchant_id = '" . ($merchant_id) . "' and type = 'volume' AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $aff_id . "'" . $strSqlProfileId;
		}
		
		$getVolume = mysql_fetch_assoc(function_mysql_query($gvQ,__FILE__));
		
		if ($set->qualify_type == "trades") {
			if ($boolInDebugMode) {
				echo '$set->qualify_type == "trades"</br>';
			}
			
			if ($getVolume['numTrades'] < $set->qualify_amount) {
				return '0';
			}
			
		} else {
			if ($getVolume['amount'] < $set->qualify_amount) {
				return '0';
			}
		}
	}
	
        
	$com = $billing ? '' : 0;
	
	if ($dealType == "deal") {
		// CONTROL gets here.
		
		if ($onlyRev) {
			$qr = "SELECT  * FROM data_stats where tranz_id = '" . $ftdRow['tranz_id'] . "' 
												and merchant_id =" . $merchant_id . " and trader_id = '" .$ftdRow['trader_id']. "'" . $strSqlProfileId;
		} else {
			$qr = "SELECT  * FROM data_sales where tranz_id = '" . $ftdRow['tranz_id'] . "' 
												and merchant_id =" . $merchant_id . " and trader_id = '" .$ftdRow['trader_id']. "'" . $strSqlProfileId;
		}
	
		$traderInfo = mysql_fetch_assoc(function_mysql_query($qr,__FILE__));
		
		if (!$traderInfo) {
                    $traderInfo = $ftdRow;
		}
		
		if ($traderInfo['trader_id']) {
		
                        //Starting Tier deal			
			// Check Tier Deal...
			$tierqq = function_mysql_query("SELECT * FROM affiliates_deals WHERE affiliate_id = '" . $aff_id . "' AND merchant_id = '" .$merchant_id . "' AND dealType = 'tier' ORDER BY amount ASC",__FILE__);
			
			if (mysql_num_rows($tierqq) > 0) {
				if ($boolInDebugMode) {
					echo 'Current deal-type is tier<hr>';
				}
				
				while ($tierww = mysql_fetch_assoc($tierqq)) {
					$tierRange = explode("-",$tierww['tier_amount']);
					
					if ($traderInfo['amount'] >= $tierRange[0] AND $traderInfo['amount'] <= $tierRange[1]) {
						if ($tierww['tier_pcpa']) 
							$tierPCPA = round(($traderInfo['amount']*$tierww['tier_pcpa'])/100,2);
						return ($billing ? 'tier|' : '').round($tierww['amount']+$tierPCPA,2);
					}
				}
				
				return '0';
			}
			
			
			$lots    = array();
			$dCpa    = array();
			$minCpa  = array();
			$cpa     = array();
			$revenue = array();
		
			$arrAffIdMerchantIdRdate = array();
			$arrAffIdMerchantIdRdate['affiliate_id'] = $aff_id;
			$arrAffIdMerchantIdRdate['merchant_id'] = $merchant_id;
			$arrAffIdMerchantIdRdate['rdate'] = $set->tempRdate;
			$AffDealsArry = extractDealTypes($arrAffIdMerchantIdRdate ,$arrDealTypesAmounts);
			 
                        
			$dCpa['amount'] =  $AffDealsArry['dcpa'];
			$lots['amount'] =  $AffDealsArry['lots'];
			$minCpa['amount'] =  $AffDealsArry['min_cpa'];
			$cpa['amount'] =  $AffDealsArry['cpa'];
			$revenue['amount'] =  $AffDealsArry['revenue'];
			$revenue_spread['amount'] =  $AffDealsArry['revenue_spread'];
			$cpl['amount'] =  $AffDealsArry['cpl'];
			$cpm['amount'] =  $AffDealsArry['cpm'];
			$cpc['amount'] =  $AffDealsArry['cpc'];
			
                        
			if ($default) {
				// CONTROL GETS HERE...
				if (!$dCpa['amount'] AND $getDefault['dcpa_amount'] > 0) 
					$dCpa['amount'] = $getDefault['dcpa_amount'];

					
				if (!$minCpa['amount'] AND $getDefault['min_cpa_amount'] > 0) 
					$minCpa['amount'] = $getDefault['min_cpa_amount'];
					
				if (!$cpa['amount'] AND $getDefault['cpa_amount'] > 0) 
					$cpa['amount'] = $getDefault['cpa_amount'];
			}

			if ($cpa['amount']) {
				
				if ($dCpa['amount']) {
					
					// CONTROL GETS HERE...
					if (($minCpa['amount'] > 0 AND $traderInfo['amount'] >= $minCpa['amount']) OR (!$minCpa['amount'])) {
						if ($boolInDebugMode) {
							echo '$minCpa[amount] is presented<hr>';
						}
						
						if ($billing) {
							return ($billing ? 'dcpa|' : '').round((($traderInfo['amount'] > $cpa['amount'] ? $cpa['amount'] : $traderInfo['amount']) * $dCpa['amount']) / 100, 2);
						} else {
							// CONTROL GETS HERE...
							$com = ($billing ? 'dcpa|' : '').round((($traderInfo['amount'] > $cpa['amount'] ? $cpa['amount'] : $traderInfo['amount']) * $dCpa['amount']) / 100, 2);
							// '$com' => 53.28
                                                        // Should be $com without 'billing'.
						}
					}
				}
				
                                
				if ($minCpa['amount'] > 0 AND $traderInfo['amount'] >= $minCpa['amount']) {
				
					if ($billing) {
						return ($billing ? 'cpa|' : '') . $cpa['amount'];
					} else {
						// CONTROL GETS HERE...
						//die('foo');
						$com = ($billing ? 'cpa|' : '') . $cpa['amount'];   // Should be $com without 'billing'.
						// '$com' => 12
					}
					
			    } elseif (!$minCpa['amount']) { 
				
					if ($billing AND !$onlyRev) {
						return ($billing ? 'cpa|' : '') . $cpa['amount'];
					} else {
						$com = $cpa['amount'];
					}
				}
			}
		}
		
		if ($onlyRev) {
			$com = 0;
		}
		
                
		if ($default){
			// CONTROL GETS HERE...
			if (!$revenue['id'] AND $getDefault['revenue_amount'] > 0){
				$revenue['amount'] = $getDefault['revenue_amount'];
			}
		}
		
		if ($revenue['amount']) {
			
			$traderInfoTotal = mysql_fetch_assoc(function_mysql_query(
													"SELECT SUM(amount) AS amount 
													FROM data_sales 
													WHERE merchant_id = '" . $merchant_id . "' 
														AND rdate BETWEEN '" . $sdate . "' AND '" . $edate . "' AND type = 'deposit' 
														AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $aff_id . "'" . $strSqlProfileId
														,__FILE__
												)
								);
			
			// $traderInfoTotal ==> // Array ( [amount] => 1000 )
			
			// $merchantInfo = mysql_fetch_assoc(function_mysql_query("SELECT * FROM merchants WHERE id = '" . $merchant_id . "'",__FILE__));
			$merchantInfo = getMerchants($merchant_id,0);
			$productType  = $merchantInfo['producttype'];
			
			// $productType ==> 'Forex'
			
			if (strtolower($merchantInfo['producttype']) == 'sportsbetting' OR strtolower($merchantInfo['producttype']) == 'casino') {
				if (isset($_REQUEST['revQ'])) {
					echo '<BR>COM:<BR>';
				}
				
				$revenueAmount = getRevenue(
					'WHERE merchant_id = ' . $merchantInfo['id'] . ' ' . ($banner_id != 0 ? ' AND banner_id = "' . $banner_id . '" ' : '') . ' ' 
										   . ($group_id != -1 ? ' AND group_id = "' . $group_id . '" ' : '') 
										   . ' AND rdate BETWEEN "' . $sdate . '" AND "' . $edate . '" AND trader_id = "' . $trader_id 
										   . '" AND affiliate_id = "' . $aff_id . '"', 
				    $merchantInfo['producttype']
				);
				
			} else {
				
				if (trim($set->revenue_formula)) {
					$saleqq = function_mysql_query(
								"SELECT type, amount FROM data_sales 
								WHERE merchant_id = '" . ($merchant_id) . "' AND  rdate BETWEEN '" . $sdate . "' AND '" . $edate . "' 
																			 AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $aff_id . "'" . $strSqlProfileId
																			 ,__FILE__
							);
					
					while ($saleww = mysql_fetch_assoc($saleqq)) {
						if ($saleww['type'] == "bonus") {
							$bonus = $saleww['amount'];
						}
						
						if ($saleww['type'] == "withdrawal") {
							$withdrawal = $saleww['amount'];
						}
						
						if ($saleww['type'] == "chargeback") {
							$chargeback = $saleww['amount'];
						}
					}
					
					// Non of the IFs within the loop above is not accessed.
					// '$saleww' (only one iteration)  Array ( [0] => Array ( [type] => deposit [amount] => 1000 ) )
					
					$a             = $traderInfoTotal['amount']; // Deposits
					$b             = $bonus;                     // Bonus Amount
					$c             = $withdrawal;                // Withdrawals Amount
					$d             = $chargeback;                // ChargeBacks Amount
					$string        = str_replace(array('{deposits}', '{bonus}', '{withdrawals}', '{chargebacks}'), array('a', 'b', 'c', 'd'), $set->revenue_formula);
					$revenueAmount = eval('return ' . preg_replace('/([a-zA-Z0-9])+/', '\$$1', $string) . ';');
					
				} else {
					$revenueAmount = $traderInfoTotal['amount'];
				}
			}
			
		    
			if ($billing) {
				return ($billing ? 'revenue|' : '') . round(($revenueAmount * $revenue['amount']) / 100, 2);
                                
			} else {
				// CONTROL GETS HERE...
				// THE DISASTER OCCURS HERE...
				
				$com = $com + round(($revenueAmount * $revenue['amount']) / 100, 2);
				
			}
			
			if ($mt != 'forex') {
				if ($boolInDebugMode) {
					echo 'Returning commission, if ($mt != forex)<hr>';
				}
				
				return $com;
			}
			
		} elseif ($com != '' AND $com > 0) {
			if ($mt != 'forex') {
				return $com;
			}
		}
		
		if ($mt == 'forex') {
			$srevenue['amount'] = $revenue_spread['amount'];
			
			if ($srevenue['amount']) {
                            $sql = "SELECT SUM(amount) AS amount FROM data_sales 
                                    WHERE merchant_id = '" . $merchant_id . "' AND rdate BETWEEN '" . $sdate . "' AND '" . $edate . "' 
                                        AND type = 'deposit' AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $aff_id . "'" . $strSqlProfileId;
                            
                            $traderInfoTotal = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
			    				
                            // $merchantInfo = mysql_fetch_assoc(function_mysql_query("SELECT * FROM merchants WHERE id = '" . $merchant_id . "'",__FILE__));
							$merchantInfo = getMerchants($merchant_id,0);
                            $productType  = $merchantInfo['producttype'];
                            
                            $sql = 'SELECT SUM(spread) AS sa FROM data_stats 
                                    WHERE type = "position" AND affiliate_id = "' . $aff_id . '" AND merchant_id = "' . $merchant_id . '" 
                                                            AND rdate BETWEEN "' . $sdate . '" AND "' . $edate . '" '
                                                         . 'AND trader_id = "' . $trader_id . '"' . $strSqlProfileId;
                            
                            $raQ = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));		
                            $revenueAmount = $raQ['sa'];
			    
				if ($billing) {
					return ($billing ? 'revenueSpread|' : '').round(($revenueAmount * $srevenue['amount']) / 100, 2);
				} else {
					$com = $com + round(($revenueAmount * $srevenue['amount']) / 100, 2);
				}
				return $com;
				
                                
			} elseif ($com != '' && $com > 0) {
				return $com;
			}
		}
		
		$netRevenue = is_null($netRevenue) ? 0 : $netRevenue;
		
		$cpl = mysql_fetch_assoc(function_mysql_query(
                        "SELECT id, amount FROM affiliates_deals 
                        WHERE affiliate_id = '" . $aff_id . "' AND merchant_id = '" . $merchant_id . "' AND dealType = 'cpl' LIMIT 1"
						,__FILE__
                ));
		
		if ($default) {  
			if (!$cpl['amount'] AND $getDefault['cpl_amount'] > 0) { 
				$cpl['amount'] = $getDefault['cpl_amount'];
			}
		}
		
		if ($cpl['amount']) {
			if ($boolInDebugMode) {
				echo '$cpl[amount] is presented<hr>';
			}
			
			$traderInfo = mysql_fetch_assoc(function_mysql_query(
												"SELECT * FROM data_reg 
												WHERE merchant_id = '" . $merchant_id . "' AND trader_id = '" . $trader_id . "' 
													AND affiliate_id = '" . $aff_id . "' AND rdate BETWEEN '" . $sdate . "' AND '" . $edate . "'" . $strSqlProfileId
													,__FILE__
											)
							);
			
			if ($traderInfo['id']) {
				return ($billing ? 'cpl|' : '') . $cpl['amount'];
			}
		}
		
	}
        elseif ($dealType == "cpc" OR $dealType == "cpm") {
		
		if ($boolInDebugMode) {
			echo 'Deal-Type: CPC or CPM<hr>';
		}
		
		$totalTraffic = mysql_fetch_assoc(function_mysql_query(
				"SELECT SUM(views) AS views, SUM(clicks) AS clicks FROM traffic 
				WHERE rdate >= '" . $sdate . "' AND rdate <= '" . $edate . "' AND affiliate_id = '" . $aff_id . "' AND merchant_id = '" . $merchant_id . "'"
				,__FILE__
			)
		);
		
		if ($dealType == "cpc") {
			$cpc = mysql_fetch_assoc(function_mysql_query(
										"SELECT id, amount FROM affiliates_deals 
										WHERE affiliate_id = '" . $aff_id . "' AND merchant_id = '" . $merchant_id . "' AND dealType = 'cpc' LIMIT 1"
										,__FILE__
									)
					);
			
			if ($default) {  
				if (!$cpc['amount'] AND $getDefault['cpc_amount'] > 0) {
					$cpc['amount'] = $getDefault['cpc_amount'];
				}
			}
			
			if ($cpc['amount'] > 0) { 
				return ($billing ? 'cpc|' : '') . round($totalTraffic['clicks'] * $cpc['amount'], 2);
			}
			
		} elseif ($dealType == "cpm") {
			$cpm = mysql_fetch_assoc(function_mysql_query(
										"SELECT id, amount FROM affiliates_deals 
										WHERE affiliate_id = '" . $aff_id . "' AND merchant_id = '" . $merchant_id . "' AND dealType = 'cpm' LIMIT 1"
										,__FILE__
									)
					);
			
			if ($default) {
				if (!$cpm['amount'] AND $getDefault['cpm_amount'] > 0) {
					$cpm['amount'] = $getDefault['cpm_amount'];
				}
			}
			
			if ($cpm['amount'] > 0) {
                            return ($billing ? 'cpm|' : '') . round(($totalTraffic['views'] / 1000) * $cpc['amount'], 2);
			}
		}
	}
	
	if ($default) {
            return '0';
	    
	} else { 
            return getCom($aff_id, $merchant_id, $trader_id, $sdate, $edate, $dealType, 1, $billing, $onlyRev, $banner_id, $group_id, $arrDealTypesAmounts, $ftdRow, $intProfileId);
	}
}
	
*/
	
	

function autoRelateAllMerchantsToAff($AffiliateID = 0)
	{
		if ($AffiliateID == 0) {
			return ;
		}
		$isFirst = true;
		$allMerString = ''; 
		$allMerchantsRslt = function_mysql_query('select id from merchants where valid = 1',__FILE__,__FUNCTION__);
		while($row = mysql_fetch_assoc($allMerchantsRslt)){

			if ($isFirst) {
				$allMerString = $row['id'];
				$isFirst = false;
			}
			else
				$allMerString .= '|'.$row['id'];
		}
		
		if (strlen($allMerString)>0 && $AffiliateID > 0)
		function_mysql_query('UPDATE affiliates SET merchants="'.$allMerString.'" WHERE id='.$AffiliateID,__FILE__,__FUNCTION__);
	}
	
	
	function autoRelateAllProductsToAff($AffiliateID = 0)
	{
		if ($AffiliateID == 0) {
			return ;
		}
		$isFirst = true;
		$allMerString = ''; 
		$allMerchantsRslt = function_mysql_query('select id from products_items where valid > 0',__FILE__,__FUNCTION__);
		while($row = mysql_fetch_assoc($allMerchantsRslt)){

			if ($isFirst) {
				$allMerString = $row['id'];
				$isFirst = false;
			}
			else
				$allMerString .= '|'.$row['id'];
		}
		
		if (strlen($allMerString)>0 && $AffiliateID > 0)
		function_mysql_query('UPDATE affiliates SET products="'.$allMerString.'" WHERE id='.$AffiliateID,__FILE__,__FUNCTION__);
	}
	
	
	
	
	/**
	 * AUTO RELATE CAMPAIGN TO AFFILIATE
	 */
		function autoRelateCampToAff($merchantID = 0)
	{
		
		$debug = '';
		$qryextention = '';
		
		if ($merchantID > 0) {
			$qryextention = ' merchantid = ' . $merchantID . ' and ';
		}
		
		$sql = 'SELECT id, toAutoRelateCampToAff FROM merchants WHERE ' . $qryextention . 'valid = 1';
		//die ($sql);
		$merchants = function_mysql_query($sql,__FILE__,__FUNCTION__);
		
		while($row = mysql_fetch_assoc($merchants)) {
			if ($row['toAutoRelateCampToAff'] == 0) {
				continue;
			}
			
		$query = 'SELECT MAX(id) AS lastID FROM affiliates 
						WHERE affiliates.id NOT IN 
							(SELECT affiliateID FROM affiliates_campaigns_relations 
								WHERE merchantid =  "' . $row['id'] . '" and affiliateID > 0) LIMIT 0, 1';
		$debug = $debug . '<br>'	. $query;
		 // die (var_dump($query));
			//die ($query);
			
			$aff = mysql_fetch_assoc(function_mysql_query($query,__FILE__,__FUNCTION__));
			//$aff = mysql_fetch_assoc(function_mysql_query($query) || die(mysql_error()));
					
		   
			
			if ($aff['lastID'] > 0) {
				$query = 'SELECT id, campID FROM affiliates_campaigns_relations 
								WHERE  merchantid = "' . $row['id'] . '" and affiliateID = 0 limit 0,1';
								
				$camp = mysql_fetch_assoc(
							function_mysql_query(
								$query
								,__FILE__,__FUNCTION__) 	);
								//WHERE  merchantid = "' . $row['id'] . '" and affiliateID = 0') || die(mysql_error()	)	);
				
				$debug = $debug . '<br><br>'	. $query;
				
				if ($camp['campID']!=null)	{
				
						$query = 'UPDATE affiliates_campaigns_relations 
							SET affiliateID = ' . $aff['lastID'] . ' 
							WHERE merchantid = ' . $row['id'] . ' AND campID = "' . $camp['campID'] . '"';
				$debug = $debug . '<br><br>'	. $query;			
						function_mysql_query(
							$query,__FILE__,__FUNCTION__); 
						
				}
			}
		}
		
		// die ($debug);
	}
	
	
	//////////////////////////////////////////////////// Set table order:
		/*
	function setTable($arr, $name, $type, $breaker=''){
	
		
		$str = '';
		$resNum = mysql_fetch_assoc(function_mysql_query('SELECT COUNT(id) AS total FROM fieldsSortOrder WHERE name="'.$name.'" AND productType="'.$type.'" AND newPos>-1 ORDER BY newPos ASC, defaultPos ASC'));
		
		$rowsQ = function_mysql_query('SELECT * FROM fieldsSortOrder WHERE name="'.$name.'" AND productType="'.$type.'" ORDER BY newPos ASC, defaultPos ASC');
		
		$breakerPos = ceil($resNum['total']/2);
		$count = 0;
		while($row = mysql_fetch_assoc($rowsQ)){
			
			for($i=0;$i<count($arr);$i++){
				
				if($row['fieldName']!=$arr[$i]->id OR $arr[$i]->used){
					
					continue;
				}
				
				
				$arr[$i]->used = 1;
				
				if($row['newPos']>-1){
					$str.=$arr[$i]->str;
					$count++;
					if($count==$breakerPos){
						$str.=$breaker;
					}
				}
				
			}
		}
		
		
		
		for($i=0;$i<count($arr);$i++){
		
			if(!$arr[$i]->used){
				$maxDefault = mysql_fetch_assoc(function_mysql_query('SELECT MAX(defaultPos) AS mdp FROM fieldsSortOrder WHERE name="'.$name.'" AND productType="'.$type.'"'));
				if($maxDefault['mdp']){
					$maxDefault['mdp'] = $maxDefault['mdp']+1;
				}else{
					$maxDefault['mdp'] = 1;
				}
				function_mysql_query('INSERT INTO fieldsSortOrder (name, productType,fieldname,defaultPos,newPos) VALUES ("'.$name.'","'.$type.'","'.$arr[$i]->id.'",'.$maxDefault['mdp'].',1000)') OR die(mysql_error());
				$str.=$arr[$i]->str;
				$count++;
				if($count==$breakerPos){
					$str.=$breaker;
				}
			}
			
		}
				
		return $str;
		
		
	}
	
	*/
	
	
	function setTable($arr, $name, $type, $breaker='',$arrLen=1,$hideDemonLead=false){
	
	// die ('hide: ' . $hideDemoAndLeads);
		$strArr = Array();
			// var_dump($arr);
			// die();

		for($j=0;$j<$arrLen;$j++){
			
			$str = '';
			$resNum = mysql_fetch_assoc(function_mysql_query('SELECT COUNT(id) AS total FROM fieldsSortOrder WHERE name="'.$name.'" AND productType="'.$type.'" AND newPos>-1 ORDER BY newPos ASC, defaultPos ASC',__FILE__,__FUNCTION__));
			
			$rowsQ = function_mysql_query('SELECT * FROM fieldsSortOrder WHERE newPos>-1 and name="'.$name.'" AND productType="'.$type.'" ORDER BY newPos ASC, defaultPos ASC',__FILE__,__FUNCTION__);
			$num = $resNum['total'];
			
			if ($hideDemonLead)
				$num= $num -2;
			
			$breakerPos = ceil($num/2);
			$count = 0;
			
			while($row = mysql_fetch_assoc($rowsQ)){
				
				for($i=0;$i<count($arr);$i++){
					
				// var_dump($arr);
				// die();
					if($row['fieldName']!=$arr[$i]->id OR $arr[$i]->used[$j]){
						continue;
					}
					
					@$arr[$i]->used[$j] = 1;
					
					if($row['newPos']>-1){
						if($arrLen>1){
							//echo $arr[$i]->str[$j].'<BR>';
							$str.=$arr[$i]->str[$j];
						}else{
							$str.=$arr[$i]->str;
						}
						$count++;
						if($count==$breakerPos){
							$str.=$breaker;
						}
					}
					
				}
			}
			
			
			
			
			for($i=0;$i<count($arr);$i++){
			
				if(!$arr[$i]->used[$j]){
					$maxDefault = mysql_fetch_assoc(function_mysql_query('SELECT MAX(defaultPos) AS mdp FROM fieldsSortOrder WHERE name="'.$name.'" AND productType="'.$type.'"',__FILE__,__FUNCTION__));
					if($maxDefault['mdp']){
						$maxDefault['mdp'] = $maxDefault['mdp']+1;
					}else{
						$maxDefault['mdp'] = 1;
					}
					if (!empty($name) && !empty($arr[$i]->id))
					function_mysql_query('INSERT INTO fieldsSortOrder (name, productType,fieldname,defaultPos,newPos) VALUES ("'.$name.'","'.$type.'","'.$arr[$i]->id.'",'.$maxDefault['mdp'].',1000)',__FILE__,__FUNCTION__) OR die(mysql_error());
					if($arrLen>1){
						$str.=$arr[$i]->str[$j];
					}else{
						$str.=$arr[$i]->str;
					}
					$count++;
					if($count==$breakerPos){
						$str.=$breaker;
					}
				}
				
			}
			
			array_push($strArr,$str);
			
		}
		
		if($arrLen==1){
			return $strArr[0];
		}
		
		return $strArr;
		
		
	}
	


/**
 * Adds a new entity to a billing array.
 * 
 * @param  array  &$arrBilling
 * @param  string  $strDealType
 * @param  float   $floatCommission
 * @param  int     $intTraderId
 * @param  bool    $needToIncreaseQuantity
 * @return void
 */        
function addToBillingArray(&$arrBilling, $strDealType, $floatCommission, $intTraderId, $needToIncreaseQuantity = false)
{
    $arrBilling[$strDealType] = [
        'quantity'   => $needToIncreaseQuantity ? 1 : 0, 
        'commission' => $floatCommission,
        'trader_id'  => $intTraderId,
    ];
}


function getProductCommission(    $arrDealTypesDefaultAmounts = array(),    $transactionRow      = null,$modeBilling         = false,$isDebug=false){
	
	 global $set;
    $com             = 0;
    $arrBilling      = [];


    $product_id      = $transactionRow['product_id'];
    $affiliate_id     = $transactionRow['affiliate_id'];
    $banner_id        = $transactionRow['banner_id'];
    $trader_id        = $transactionRow['trader_id'];
    
	if ($transactionRow['dealtype']=='cpc'){
		getDefaultAffiliateID();
		$affiliate_id = $set->defaultAffiliateID;
	}
	
	$affiliateRow = getAffiliateRow($affiliate_id);

$q = "select pad.* from products_affiliates_deals pad inner join affiliates af on af.id = pad.affiliate_id where pad.valid=1 and af.valid=1 and pad.affiliate_id = " . $affiliate_id;

$dealRsc = (function_mysql_query($q,__FILE__ ,__FUNCTION__));
			while ($AffDealsArryRow =  mysql_fetch_array($dealRsc)) {
				
 
				if ($AffDealsArryRow['dealType']=='cplaccount')
					$AffDealsArry['cplaccount'] = $AffDealsArryRow['amount'];
				else if ($AffDealsArryRow['dealType']=='cpllead')
					$AffDealsArry['cpllead'] = $AffDealsArryRow['amount'];
				else if ($AffDealsArryRow['dealType']=='cpa')
					$AffDealsArry['cpa'] = $AffDealsArryRow['amount'];
				else if ($AffDealsArryRow['dealType']=='cpc')
					$AffDealsArry['cpc'] = $AffDealsArryRow['amount'];
				else if ($AffDealsArryRow['dealType']=='cpi')
					$AffDealsArry['cpi'] = $AffDealsArryRow['amount'];
			
			
			
			}
			
			if (empty($AffDealsArry['cpi']) &&empty($AffDealsArry['cpc']) &&empty($AffDealsArry['cpa']) && empty($AffDealsArry['cplaccount']) && empty($AffDealsArry['cpllead'])){
			  $AffDealsArry = $arrDealTypeDefaults;
			}
			
			
    
/* if ($isDebug) {
var_dump($AffDealsArry);	
die ();
} */



    if($transactionRow['dealtype']=='cpa'){
		
		// if (($minCpa['amount'] > 0 AND $transactionRow['amount'] >= $minCpa['amount']) OR (!$minCpa['amount'])) {
			
			if ($modeBilling) {
							addToBillingArray($arrBilling, 'cpa', $AffDealsArry['cpa'], $transactionRow['trader_id'], true);
							// return $arrBilling;
				}
				$com += round ($AffDealsArry['cpa']);
	}
      else if($transactionRow['dealtype']=='cplaccount'){
			if ($modeBilling) {
							addToBillingArray($arrBilling, 'cplaccount', $AffDealsArry['cplaccount'], $transactionRow['trader_id'], true);
							
				}
				$com += round ($AffDealsArry['cplaccount']);
	}
	else if($transactionRow['dealtype']=='cpllead'){
			if ($modeBilling) {
							addToBillingArray($arrBilling, 'cpllead', $AffDealsArry['cpllead'], $transactionRow['trader_id'], true);
							
				}
				$com += round ($AffDealsArry['cpllead']);
	}
	else if($transactionRow['dealtype']=='cpi'){
			if ($modeBilling) {
							addToBillingArray($arrBilling, 'cpi', $AffDealsArry['cpi'], $transactionRow['trader_id'], true);
							
				}
				$com += round ($AffDealsArry['cpi']);
	}
	else if($transactionRow['dealtype']=='cpc'){
			if ($modeBilling) {
	
						$arrBilling['cpc'] = [
								'quantity'   => $transactionRow['count'],
								'commission' => $floatCommission,
								'trader_id'  => 0,
						];
							
							// addToBillingArray($arrBilling, 'cpc', $AffDealsArry['cpc'], $transactionRow['trader_id'], true);
							
				}
				$com += round ($AffDealsArry['cpc'])*$transactionRow['count'];
	}
    
    
    return $modeBilling ? $arrBilling : $com;

	}

 function getTradersTagForTrader($merchant_id = "" , $trader_id = ""){
	 global $allTradersWithTag;
	 if (empty($merchant_id) || empty($trader_id))
		return 0;
	 
	 if (empty ($allTradersWithTag)){
		$q = "select * from traders_tag where valid =1 ";
		$rscr = mysql_query($q);
		while ($tradertagrow = mysql_fetch_assoc($rscr)){
			$allTradersWithTag[$tradertagrow['merchant_id']][$tradertagrow['trader_id']]['status'] = $tradertagrow['status'];
			$allTradersWithTag[$tradertagrow['merchant_id']][$tradertagrow['trader_id']]['notes'] = $tradertagrow['notes'];
		}
	 }
	 return ($allTradersWithTag[$merchant_id][$trader_id] );
 }

 
 
  
 function checkQualification($qualifiedArray,$affiliateRow,$transactionRow,$onlyRevenueShare,$limitToDate="") {
	global $set;
	
		$merchantType = $qualifiedArray['merchant_type'];
		 $set->qualify_type  = $qualifiedArray['merchant_qualify_type'];
		$floatQualifyAmount = 		$qualifiedArray['merchant_qualify_amount'] ;
		 // $strSqlProfileId = 		$qualifiedArray['strSqlProfileId'] ;

		 
	if (isset($transactionRow['FTDqualificationDate']) &&  hasValidDate($transactionRow['FTDqualificationDate']))	{
	
	return "1"; // already checked and found qualified
 }
	

		if ($affiliateRow['qualify_type']=='default') {
			$qualify_type = $qualifiedArray['merchant_qualify_type'];
			$floatQualifyAmount =   (float)$qualifiedArray['merchant_qualify_amount'];
			
		}
		else if ($affiliateRow['qualify_type']=='') {
			$floatQualifyAmount = 0;
		}
		else if ($affiliateRow['qualify_type']=='trades' || $affiliateRow['qualify_type']=='volume' || $affiliateRow['qualify_type']=='lots' || $affiliateRow['qualify_type']=='totalmd') {
			$qualify_type = $affiliateRow['qualify_type'];
			$floatQualifyAmount =   (float)$affiliateRow['qualify_amount'];
		}
	
	


	
	if (!isset($transactionRow['traderHasFTD']) || $transactionRow['traderHasFTD']==true )
	{
	$checkQualification = true;
	if ($set->qualifiedCommissionOnCPAonly ==1) {
		
			
		 
		$checkQualification =  $transactionRow['isFTD'] ? true : false;
	}
	else {
		
		$checkQualification = $onlyRevenueShare == 1 || $transactionRow['isFTD'] ? true : false;
	}
	
	
/* 	if ($transactionRow['trader_id']=='10164434'){
		var_dump($floatQualifyAmount);
		echo '<br><Br><br>';
		var_dump($checkQualification);
		echo '<br><Br><br>';
		var_dump($merchantType);
		die ('-00--');
	} */
	
		// (($onlyRevenueShare == 1 || $transactionRow['isFTD']) || ($onlyRevenueShare == 1 || $transactionRow['isFTD']))) {

    // Check Qualify Lead by Volume OR Trades, Return 0 If not Qualify
    if ($floatQualifyAmount > 0 && $checkQualification) {
		$trader_id = $transactionRow['trader_id'];
		$merchant_id = $transactionRow['merchant_id'];
		$affiliate_id = $transactionRow['affiliate_id'];
		
		if (strtolower($merchantType) == 'forex') {
				$typevalue = "position";
				$table = "data_stats";
		}else {
			$table = "data_sales";
			$typevalue = "volume";
		}
		
		
		if ($qualify_type == "trades") {
					
				/*  	 echo 'only? : ' . $onlyRevenueShare .'<br>';
					echo '<Br>';
					var_dump($transactionRow);
					echo '<Br>';
					echo '<Br>';   */
					  $gvQ = "SELECT COUNT(id) AS numTrades FROM ".$table." 
							WHERE merchant_id = '" . ($merchant_id) . "' and type = '".$typevalue."' AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $affiliate_id . "'" . $strSqlProfileId
							;
							
				}
				else if ($qualify_type == "volume") {
							
								$gvQ = "SELECT SUM(amount) AS amount FROM ".$table."  
							WHERE merchant_id = '" . ($merchant_id) . "' and type = '".$typevalue."' AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $affiliate_id . "'" . $strSqlProfileId;
				} else if ($qualify_type == "lots") {
					$gvQ = "SELECT SUM(turnover) AS amount FROM ".$table."  
							WHERE merchant_id = '" . ($merchant_id) . "' and type = '".$typevalue."' AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $affiliate_id . "'" . $strSqlProfileId;
							
				}
			else if ($qualify_type=='totalmd') {
				  $gvQ = "SELECT sum(amount) AS totalDeposit FROM data_sales 
						WHERE merchant_id = '" . ($merchant_id) . "' and type = 'deposit'  AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $affiliate_id . "'" . $strSqlProfileId;
			}
			
			
		
			
			if ($gvQ=="") {
				
			}
			else {
			
			$gvQ .=(!empty($limitToDate) ? " rdate <'". $limitToDate . "'  " : "" );
			
			
			if ($qualify_type == "trades" && !empty($transactionRow['trades'])) {
				$getVolume['numTrades'] = $transactionRow['trades'];
			}
			else {
				$getVolume = mysql_fetch_assoc(function_mysql_query($gvQ,__FILE__,__FUNCTION__));
			}

			/* echo '<Br>';
			echo '<Br>';
			var_dump($transactionRow);
			echo '<Br>';
			echo '<Br>';
			var_dump($getVolume);
			die();
			 */
			
			
			
			if ($qualify_type == "trades") {
				if ($getVolume['numTrades'] < $floatQualifyAmount) {
					return '0';
				}
				
			} elseif ($qualify_type == "totalmd") {
				if ($getVolume['totalDeposit'] < $floatQualifyAmount) {
					return '0';
				}
				
			} elseif ($qualify_type == "volume") {
				if ($getVolume['amount'] < $floatQualifyAmount) {
					return '0';
				}
			} elseif ($qualify_type == "lots") {
				if ($getVolume['amount'] < $floatQualifyAmount) {
					return '0';
				}
				
			} else {
				if ($getVolume['amount'] < $floatQualifyAmount) {
					return '0';
				}
			}
			}
    }
    }
	return '1';
}


/**
 * Returns one of the following:
 *     1. If bill exists, returns the bill, with appropriate details (approved?, ...).
 *     2. Returns '0', if 'qualify_amount' > amount of new traders.
 */

 function getValue($value=""){
	 if (!isset($value) || empty($value) || $value==null){
		 $value=array();
		 $value['amount']=0;
 }
	else if ($value['amount']==null){
		 $value['amount']=0;
	}
	
	 // var_dump($value);
	 // echo '<Br>';
	 return $value;
 }
 
 
 function hasContinuousyCommissionType($detailsArray,$arrDealTypesAmounts){
	 
	 $currentEventDeal = extractDealTypes($detailsArray, $arrDealTypesAmounts,5);
	 
	 $detailsArray['rdate'] = $detailsArray['initialftddate'];
	 
	 if ($_GET['explain']==2 && $detailsArray['type']=='lots')  	{
	 echo '<Br><Br>damdam: ';
	 var_dump($detailsArray);
	 echo '<Br><Br>';
	 }
	 
	 $FTDEventDateDeal = extractDealTypes($detailsArray, $arrDealTypesAmounts,5);
	 $hasOneTypeOfCommissionSinceFTDTillEvent = true;
	 $typeOfCom = "";
	 foreach ($FTDEventDateDeal as $deal=>$val){
		 
		 if ($val['amount']>0 && $val['amount']!=null ){
		 $FTDtypeOfCom[] = empty($typeOfCom) ? $deal : $typeOfCom;
		 }
	 }
	 
	 
	 $typeOfCom = "";
	 foreach ($currentEventDeal as $deal=>$val){
		 
		 if ($val['amount']>0 && $val['amount']!=null){
		 $EventTypeOfCom[] = empty($typeOfCom) ? $deal : $typeOfCom;
		 }
	 }
	 
	 $ShareTypes= array('pnl','lots','revenue','revenue_spread','positions_rev_share');
	 $OneTimeTypes= array('min_cpa','cpa','dcpa','tier','cpc','cpm','cpl');
	 
	 if ($set->continuityOfShareTypeCommission ==1){
	
	 
		 if (
		 (items_in_array($EventTypeOfCom,$ShareTypes) && items_in_array($FTDtypeOfCom,$ShareTypes))
		 ||
		 (items_in_array($FTDtypeOfCom,$OneTimeTypes) && items_in_array($EventTypeOfCom,$OneTimeTypes))
		 
		 )
		return true;
	 
		return false;
	 }
else { 
// if deal type is not like on ftd date then it shouldn't get commission	 


		if ($FTDtypeOfCom==$EventTypeOfCom){
			if ($_GET['explain']>0)  			echo 'deals were the same on ftd and on the event date<Br>';
			return true;
		}
		else {
			if ($_GET['explain']>0 )  	{
				
				echo 'deals were NOT the same on ftd and on the event date<Br>';
				
			}
			return false;
		}
	 
}
 }
 
 function items_in_array($items,$inArray){
	 $isinarray = true;
	 foreach ($items as $item){
		 if (in_array($item,$inArray) && $isinarray){
			 
		 }
		 else 
		 {
			 return false;
		 }
	 }
	 return $isinarray;
 }
 
 function isHybride($dealsArr){
	 
	 $dealsArr = array_filter($dealsArr);
	
	 if (
	 ($dealsArr['min_cpa']['amount'] || $dealsArr['cpa']['amount']  || $dealsArr['cpl']['amount']  || $dealsArr['cpc']['amount'] )
	 &&
	 ($dealsArr['lots']['amount'] || $dealsArr['pnl']['amount']  || $dealsArr['revshare']['amount']  )
	 )
	 return true;
	 
	 if ($_GET['debug2']==1)  			echo 'no hybride<Br>';
	 /* var_dump($dealsArr);
	 die(); */
	 return false;
 }
 
function isTierDealRelevantToThisRecord($row,$deals){
	if ($_GET['nir']==1){
		echo '<br>WWW:<Br><Br>';
		var_dump($row);
		echo '<br><Br><Br>';
		var_dump($deals);
		echo '<br><Br>------------------------------------------<Br>';
	}
	
	$date = '0000-00-00 00:00:00';
	$selectedDeal = "";
	
	foreach ($deals as $name=>$deal){
		if (empty($deal)) {
			continue;
		}
                
		
		if (isset($deal['rdate']) && $date<$deal['rdate'] && strtolower($name)=='tier'){
			$date = $deal['rdate'];
			$selectedDeal = $name;
		
		}
	}
	
	if ($selectedDeal=='tier'){
		return true;
	}
	
	return false;
}
 
/*
function addCommission($merchentId, $affiliateId, $date, )
{

}*/
// TODO: That part code for product commission. Previous code is not compatible with that code.
// TODO: After rewrite all code, that part need to be add to main

function addCommissionForProductActions($merchantId, $merchantName, $affiliateId, $date, $traderId, $productId, $typeDeal)
{
    if(!empty($merchantId) && !empty($typeDeal) && !empty($productId)){

        //$productDealCommissionsSQL = "SELECT * FROM products_affiliates_deals WHERE product_id = ".(int)$productId." AND admin_id = ".(int)$merchantId." AND affiliate_id = ".(int)$affiliateId." AND valid = 1 GROUP BY dealType ORDER BY rdate DESC  ";
        $productDealCommissionsSQL = "SELECT * FROM products_affiliates_deals WHERE product_id = ".(int)$productId." AND affiliate_id = ".(int)$affiliateId." AND valid = 1 GROUP BY dealType ORDER BY rdate DESC  ";
        $productDealCommissionsResource = function_mysql_query($productDealCommissionsSQL,__FILE__);

        $commission = 0;
        while ($commissions = mysql_fetch_assoc($productDealCommissionsResource)) {
            if ($commissions['dealType'] == $typeDeal) {
                $commission = $commissions['amount'];
            }
        }
		
		// default value for product
        if ($commission == 0) {
            $productDealCommissionsSQL = "SELECT * FROM products_items WHERE product_id = ".(int)$productId." AND merchant_id = ".(int)$merchantId." ";
            $productDealCommissionsResource = function_mysql_query($productDealCommissionsSQL,__FILE__);
            $commissions = mysql_fetch_assoc($productDealCommissionsResource);
            if (!empty($commissions[0][$typeDeal])) {
                $commission = $commissions[0][$typeDeal];
            }
        }

        $affiliate = getAffiliateRow($affiliateId);
        $commissionRecord['id'] = strtotime($date);
        $commissionRecord['rdate'] = $date;
        $commissionRecord['location'] = lang($typeDeal);
        $commissionRecord['commission'] += $commission;
        $commissionRecord['trader_id'] = $traderId;
        $commissionRecord['amount'] = $commission;
        $commissionRecord['type'] = lang("Product");
        $commissionRecord['merchant_id'] = $merchantId;
        $commissionRecord['merchant_name'] = $merchantName;
        $commissionRecord['affiliate_id'] = $affiliateId;
        $commissionRecord['affiliate_name'] = $affiliate['username'];

        return $commissionRecord;

    }

    return [];
}

// TODO: End that part 
 
function getCommission(
    $startdate           = '', 
    $enddate             = '', 
    $onlyRevenueShare    = 0, 
    $group_id            = -1, 
    $arrDealTypesAmounts = array(),
    $transactionRow      = null,
    $isTest              = false,
    $modeBilling         = false
)
{
    global $set,$AffiliatesDealOnFtdDate;

	
	if ($transactionRow['type']=='pnl' && $_GET['test']==1){
    	var_dump($transactionRow);
    	echo '<Br><Br>';
    	die();
    }




	if ($_GET['trackCom']==1) 	echo 'com-1 : ' . $com . '<br>';
	

	// if ($_GET['trackCom==1'] && $transactionRow['trader_id']=='3233235' & $transactionRow['type']=='pnl'){

  /* if ($transactionRow['isFTD']==true|| ($transactionRow['type']<>'volume' && isset($transactionRow['type']))){
  var_dump($transactionRow);
  echo 'isTierDealFlow:' . $isTierDealFlow.'<br>';
  echo 'onlyRevenueShare:' . $onlyRevenueShare.'<br>';
  echo 'transactionRow:' . $transactionRow['isFTD'].'<br>';
  
		die ('---');
  } */

		if ($_GET['explain']==1)  			echo '------------<Br><Br>';
  
  

 // if ( $transactionRow['trader_id']=='3247507'){
 
if (!$modeBilling){

	$tradersTagRes = getTradersTagForTrader($transactionRow['merchant_id'],$transactionRow['trader_id']);
	if (isset($tradersTagRes) && (!empty($tradersTagRes['notes']) || !empty($tradersTagRes['status']) )){
		if ($_GET['explain']==1)  			echo 'trader tag exclude<Br>';
		// die ('gerger');
		return 0;  
	}
	// todo : support partial payment based on db 
}
	
	
	// var_dump($transactionRow);
	// echo '<Br>';
	
    $strSqlProfileId = '';
    $com             = 0;
    $arrBilling      = [];


	/* 
	if ($onlyRevenueShare==1){
		var_dump($transactionRow);
		echo '<br>';
		
	}
	 */
	
    if (!is_null($transactionRow['profile_id'])) {
        $strSqlProfileId = ' AND profile_id = ' . $transactionRow['profile_id'] . ' ';
    }
    
	/* if ($transactionRow['isFTD']){
		echo '<br>';
		var_dump($transactionRow);
		die('---');
	} */
	
    $merchant_id      = $transactionRow['merchant_id'];
    $affiliate_id     = $transactionRow['affiliate_id'];
    
    $banner_id        = $transactionRow['banner_id'];
    $trader_id        = $transactionRow['trader_id'];
    
	// var_dump($transactionRow);
	// echo'<br>';
	// die();
	// $merchantRow = $merchantsArray[$merchant_id];

	
	
	$merchantRow = getMerchants($merchant_id,0);
		
	if (count($merchantRow)==1)
		$merchantRow = $merchantRow[0];
    
	
	
	
	if ($modeBilling) {
        $sqlOldPayment = "SELECT id, status, reportType, amount FROM payments_details 
                          WHERE trader_id = '" . $trader_id . "' AND affiliate_id = '" . $affiliate_id . "' AND merchant_id = '" . $merchant_id . "' 
                                AND month = '" . date('m', strtotime($startdate)) . "' AND year = '" . date('Y', strtotime($startdate)) . "'  
                          LIMIT 0, 1;";
        
	$arrOldPayment = mysql_fetch_assoc(function_mysql_query($sqlOldPayment,__FILE__,__FUNCTION__));
        
	if (!empty($arrOldPayment)) {
            if ($arrOldPayment['status'] == 'approved') {
                addToBillingArray($arrBilling, $arrOldPayment['reportType'], $arrOldPayment['amount'], $trader_id, false);
                return $arrBilling;
            } else {
                addToBillingArray($arrBilling, $arrOldPayment['reportType'], 0, $trader_id, false);
                return $arrBilling;
            }
	}
    }
	

		$floatQualifyAmount = (float) $merchantRow['qualify_amount'];
		$set->qualify_type = $merchantRow['qualify_type'];
		$merchantType       = strtolower($merchantRow['producttype']);
		
		$affiliateRow = getAffiliateRow($affiliate_id);
		
		$qualifiedArray['merchant_type'] = $merchantType;
		$qualifiedArray['merchant_qualify_type'] = $set->qualify_type;
		$qualifiedArray['merchant_qualify_amount'] = $floatQualifyAmount;
		$qualifiedArray['strSqlProfileId'] = $strSqlProfileId;
		

		$v = checkQualification($qualifiedArray,$affiliateRow,$transactionRow,$onlyRevenueShare);
		if ($_GET['debug']==1){
			echo '<Br>checkQualification: ';
			var_dump($v);
			echo '<Br>';
		}

        
		if (empty($v)){
			if ($_GET['debug']==1)
				echo 'qualification 1<br>';
			
			if ($_GET['explain']==1)  			echo 'not qualified<Br>';
			
			
			return $v;
		}
						
						

								
										
	   $arrAffIdMerchantIdRdate                 = array();
    $arrAffIdMerchantIdRdate['affiliate_id'] = $affiliate_id;
    $arrAffIdMerchantIdRdate['merchant_id']  = $merchant_id;
    
    if ($onlyRevenueShare) {
        $arrAffIdMerchantIdRdate['rdate'] = $startdate;
    } else {
		$arrAffIdMerchantIdRdate['rdate'] = $transactionRow['rdate'];
    }
	
	
	$AffDealsArry = extractDealTypes($arrAffIdMerchantIdRdate, $arrDealTypesAmounts,5);
	

	if ($_GET['debug2']==1){
	var_dump($AffDealsArry);
echo '<br>';
echo '<br>';
	}
	

	
	$continuousyVarsArray = $arrAffIdMerchantIdRdate;
	$continuousyVarsArray['initialftddate'] = !empty($transactionRow['initialftddate']) ? 	$transactionRow['initialftddate'] : $transactionRow['FTDqualificationDate'] ;
	
			
			


/// experimental !!!!!!!    if event's fts happend 	before the deal then this record should be ignored.
/* 	 if ($onlyRevenueShare){
			
		foreach ($AffDealsArry as $dealToCheck){
			// die();
				
			$continuousyVarsArray = $arrAffIdMerchantIdRdate;
			$continuousyVarsArray['initialftddate'] = $transactionRow['initialftddate'];
			
			
			
				
			if (!empty($dealToCheck) && isset($dealToCheck['rdate']) && $dealToCheck['rdate']> $transactionRow['initialftddate'] ){
				return 0;
			}
		}
	}  */
	

	
	// var_dump($AffDealsArry);
	// die();
	
	$isHybride = isHybride($AffDealsArry);
	if ($_GET['debug2']==1)  			echo 'is hybride ('.var_dump($isHybride). ')<Br>';
	// die ('isH: ' . $isHybride);
	
	
	
 	 if (!$isHybride &&
	 ( ($onlyRevenueShare || $transactionRow['type']== 'volume' || $transactionRow['type']== 'pnl' || $transactionRow['type']== 'lots') 
	 && !hasContinuousyCommissionType($transactionRow,$arrDealTypesAmounts))
	 // && !hasContinuousyCommissionType($continuousyVarsArray,$arrDealTypesAmounts))
	 )
	 {
		 /* $TmpFTDdateArr = $arrAffIdMerchantIdRdate;
		 $TmpFTDdateArr['rdate'] = $transactionRow['FTDqualificationDate'];
		 
		 
		  $AffiliatesDealOnFtdDate[$arrAffIdMerchantIdRdate['merchant_id']][$arrAffIdMerchantIdRdate['affiliate_id']] = extractDealTypes($TmpFTDdateArr, $arrDealTypesAmounts,5); */
		  // var_dump($AffiliatesDealOnFtdDate);
		 // die();
		 /* 
array(3) { ["affiliate_id"]=> string(4) "1731" ["merchant_id"]=> string(1) "1" ["rdate"]=> string(19) "2017-07-14 19:59:17" }
array(9) { ["merchant_id"]=> string(1) "1" ["affiliate_id"]=> string(4) "1731" ["rdate"]=> string(19) "2017-07-14 19:59:17" ["banner_id"]=> string(1) "0" ["trader_id"]=> string(8) "30107617" ["FTDqualificationDate"]=> string(19) "2017-06-18 00:26:16" ["profile_id"]=> string(1) "0" ["type"]=> string(6) "volume" ["amount"]=> string(3) "0.5" } */

		 // avoid problems of having double commission for CPA and rev if changing cpa to share deal after ftd.
		 // var_dump($transactionRow);
		 // die();
		if ($_GET['debug2']==1)  			echo 'is ftd: ' . $transactionRow['isFTD']. ')<Br>';
		if ($_GET['debug2']==1)  			echo 'no continuousy deal ('.$transactionRow['type']. ')<Br>';
		return 0;
	 }

	

 	
	// if ($onlyRevenueShare==1)
    

    // Check Tier Deal...
    $isTierDealFlow = false;
    //if (!$onlyRevenueShare && $transactionRow['isFTD']) { // OLD
	
	if ($_GET['trackCom']==1  && $transactionRow['isFTD']==true){
		echo '<br>AffDealsArry:<Br>';
		var_dump($AffDealsArry);
		echo '<br><Br>';
		echo '<Br>';
		var_dump($transactionRow);
		echo '<Br>';
		echo $com .'<br>';
		echo '<Br>';
		echo '<Br>';
		// die ('k');
	}
	
	$isit = isTierDealRelevantToThisRecord($transactionRow, $AffDealsArry);
	if ($_GET['nir']==1) echo 'isit: ' . $isit.'<br>';

    if (
	$isit
	 && (
		((!$onlyRevenueShare || isset($transactionRow['tier_type'])) && ($transactionRow['type']!='volume' && $transactionRow['type']!='pnl') )
		|| 
		((!$onlyRevenueShare && ($AffDealsArry['tier']['amount']>0)) && $transactionRow['isFTD'])
		)  
		){ 
	
		
		$results = getAffiliateFullTierDeal($merchant_id,$affiliate_id);
		krsort($results);
		
		
		if ($_GET['debug_cpa']==1 && $transactionRow['amount']>0){
			echo '<Br><Br>::<br>';
			var_dump($transactionRow);
			echo '<Br><Br>|||<br>';
			var_dump($results);
			echo '<Br><Br><br>';
			die();
			
		}
		if (!isset($transactionRow['amount']))
			$transactionRow['amount'] = $transactionRow['ftdamount'];
		
            
        /******************************************************************/		
        // The TierDeal array is grouped by date.
        if(!empty($results)){
            $tierDiealWithDateRange = [];
            foreach($results as $tierww) {
                $tierDiealWithDateRange[$tierww['tier_amount']][] = strtotime($tierww['rdate']);
            }
        }

    	/******************************************************************/

        
		
		foreach($results as $tierww) {
		    
		    
		    $tierDealStartNextDate = function($curentTierDeal) use ($tierDiealWithDateRange){
    		        $result = time();
    		        
    		        if(!empty($tierDiealWithDateRange[$curentTierDeal['tier_amount']])){
		                sort($tierDiealWithDateRange[$curentTierDeal['tier_amount']]);

		                $getKey = array_search(strtotime($curentTierDeal['rdate']), $tierDiealWithDateRange[$curentTierDeal['tier_amount']]);
                        
		                if($getKey !== false){
		                	// If 1 then this is the end date.
		                    if($getKey === 1){
		                        $getKey = 0;
		                    }
		                    if(!empty($tierDiealWithDateRange[$curentTierDeal['tier_amount']][($getKey+1)])){
		                        $result = $tierDiealWithDateRange[$curentTierDeal['tier_amount']][($getKey+1)];
		                    }
		                }
    		            
    		        }
    		        
    		        return $result;
    		        
    		    };
		    
		    if ($_GET['debug_cpa']){ // AND $transactionRow['id'] == 177874
		        
		        var_dump(date('Y-m-d H:i:s',$tierDealStartNextDate($tierww)));
		            
                print_r($tierDiealWithDateRange);
                print_r($tierww);
        		echo "\n";
        	}
		
		    
		    
			if(strtotime($transactionRow['rdate']) >= strtotime($tierww['rdate']) && strtotime($transactionRow['rdate']) <= $tierDealStartNextDate($tierww) && $tierww['valid'] == 1){

                    
            		if ($transactionRow['trader_id'] == '5574491' AND $_GET['debug_ftd']){
            		echo 'trans trader_id: '.$transactionRow['trader_id'].'<Br>';
            		echo 'trans rdate: '.$transactionRow['rdate'].'<Br>';
            		echo 'trans amount: '.$transactionRow['amount'].'<Br>';
            		echo 'tierww: '.$tierww['rdate'].'<Br><Br>';
            		}
            		
            	
			
			
				$isTierDealFlow = true;
				$tierRange      = explode('-', $tierww['tier_amount']);
				$tierRangeA = $tierRange[0];
				$tierRangeB = isset($tierRange[1]) ? $tierRange[1] :$tierRange[0] ;
				$tierPCPA       = 0;
				
				if ($transactionRow['amount'] >= $tierRangeA && $transactionRow['amount'] <= $tierRangeB) {
					
					
					if ($tierww['tier_pcpa']) {
						$tierPCPA = round(($transactionRow['amount'] * $tierww['tier_pcpa']) / 100, 2);
						
						if ($modeBilling) {
							addToBillingArray($arrBilling, 'tier', $tierPCPA, $trader_id, true);
							return $arrBilling;
						}
					}
					
					if ($modeBilling) {
						addToBillingArray($arrBilling, 'tier', round($tierww['amount'] + $tierPCPA, 2), $trader_id, true);
						return $arrBilling;
					}
					// die ($tierww['amount']. '         frr');

					return round($tierww['amount'] + $tierPCPA, 2);
				}
			}
        }
		
		
    }
    //End Tier deal
    
    $dCpa    = array();
    $minCpa  = array();
    $cpa     = array();
    $revenue = array();
    $pnl = array();
    $lots = array();
    
    
    //////////////////////////////// $AffDealsArry = extractDealTypes($arrAffIdMerchantIdRdate, $arrDealTypesAmounts);
	
			 
// echo '<br>1111111111:<br><Br>';			
  // var_dump($AffDealsArry);
  // die();
  
  
	/* 	if ($_GET['fewfwe'] && $onlyRevenueShare){
				var_dump($transactionRow);
				echo '<Br>';
				echo ('only: ' . $onlyRevenueShare);
				echo '<Br>';
				
				var_dump($AffDealsArry);
				die();
			
			} */
 
  
    $dCpa          = getValue($AffDealsArry['dcpa']);
    
    $minCpa        = getValue($AffDealsArry['min_cpa']);
    $cpa            = getValue($AffDealsArry['cpa']);
    $revenue        = getValue($AffDealsArry['revenue']);
    $pnl        = getValue($AffDealsArry['pnl']);
	
	
	
	
    $revenue_spread = getValue($AffDealsArry['revenue_spread']);
    $lots = getValue($AffDealsArry['lots']);
    $rev_share_position = getValue($AffDealsArry['rev_share_position']);
    $cpl            = getValue($AffDealsArry['cpl']);
    $cpi            = getValue($AffDealsArry['cpi']);
    $cpm            = getValue($AffDealsArry['cpm']);
    $cpc            = getValue($AffDealsArry['cpc']);
    
			

            		if ($transactionRow['trader_id'] == '5574491' AND $_GET['debug_ftd']){
                		echo 'trans trader_id: '.$transactionRow['trader_id'].'<Br>';
                		echo 'trans rdate: '.$transactionRow['rdate'].'<Br>';
                		echo 'trans amount: '.$transactionRow['amount'].'<Br>';
                		echo 'tierww: '.$tierww['rdate'].'<Br><Br>';
            		}
			
  
    if (($AffDealsArry == $arrDealTypesAmounts) && (!$dCpa['amount'] AND $merchantRow['dcpa_amount'] > 0)) 
            $dCpa['amount'] = $merchantRow['dcpa_amount'];
    
    if (($AffDealsArry == $arrDealTypesAmounts) && (!$lots['amount'] AND $merchantRow['lots'] > 0)) 
            $lots['amount'] = $merchantRow['lots'];
    
    if (($AffDealsArry == $arrDealTypesAmounts) && (!$minCpa['amount'] AND $merchantRow['min_cpa_amount'] > 0))
            $minCpa['amount'] = $merchantRow['min_cpa_amount'];
    
    if (($AffDealsArry == $arrDealTypesAmounts) && (!$cpa['amount'] AND $merchantRow['cpa_amount'] > 0)) 
            $cpa['amount'] = $merchantRow['cpa_amount'];
    
	if (($AffDealsArry == $arrDealTypesAmounts) && (!$pnl['amount'] AND $merchantRow['pnl_amount'] > 0)) 
            $pnl['amount'] = $merchantRow['pnl_amount'];
		
		
		
		
		
// if ($transactionRow['runningType'] == 'qualification' && $transactionRow['trader_id']=='3225106'){
 
										
										
// die ('grege');
  // CPA
  
 
    if ($isTierDealFlow == false && !$onlyRevenueShare && ($transactionRow['isFTD']) || $transactionRow['runningType']=='qualification') {
        
            if ($transactionRow['trader_id'] == '5574491' && $_GET['debugcpa']==1){
                echo "<pre>";
                echo 'minCpa: ';
                var_dump($minCpa);
                echo 'cpa: ';
                var_dump($cpa);
                echo 'dCpa: ';
                var_dump($dCpa);
                echo 'onlyrev: ' . var_dump($onlyRevenueShare) .'<Br>';
                echo 'isTierDealFlow: ' . var_dump($isTierDealFlow) .'<Br>';
                echo 'transactionRow isFTD: ' . print_r($transactionRow,1) .'<Br>';
                echo 'runningType: ' . $transactionRow['runningType'];
                var_dump($AffDealsArry);		
                
                echo "CPA condition: ";
                var_dump(!(float)$dCpa['amount']);echo '<Br>';
                var_dump((float) $minCpa['amount'] > 0);echo '<Br>';
                var_dump((float) $transactionRow['amount'] >= (float) $minCpa['amount']);echo '<Br>';
                echo '<Br><Br><br>';
                echo "</pre>";
            }
		
		if ((float) $cpa['amount'] > 0) {
            // if ($dCpa['amount']) {
			if ((float) $dCpa['amount'] > 0){
				
                if (($minCpa['amount'] >= 0 AND $transactionRow['amount'] >= $minCpa['amount']) OR (!$minCpa['amount'])) {

		/*			

					echo '$transactionRow[amount]' . $transactionRow['amount'] . '<br>';
					echo '$$cpa[amount]' . $cpa['amount'] . '<br>';
					echo '$$transactionRow[amount]'. $transactionRow['amount'] . '<br>';
					echo '$$dCpa[amount]' . $dCpa['amount'] . '<br>';
			
			$transactionRow[amount]27.96
$$cpa[amount]600
$$transactionRow[amount]27.96
$$dCpa[amount]50
13.98
				28											600				600					28									50
*/						
///   round((($traderInfo['amount'] > $cpa['amount'] ? $cpa['amount'] : $traderInfo['amount'])*$dCpa['amount'])/100,2);  // old method
			
					$com = round((((float)$transactionRow['amount'] > $cpa['amount'] ? $cpa['amount'] : (float)$transactionRow['amount']) * (float)$dCpa['amount']) / 100, 2);
                    // echo ($com) . '<br>';
                    if ($modeBilling) {
                        addToBillingArray($arrBilling, 'dcpa', $com, $trader_id, true);
                    }
                }
            }
            
            if (!(float)$dCpa['amount'] && (float) ($minCpa['amount'] > 0 || !$minCpa['amount']) && (float) $transactionRow['amount'] >= (float) $minCpa['amount']) {
                $com = $cpa['amount'];

                if ($transactionRow['trader_id'] == '5574491' && $_GET['debugcpa']==1){
                    echo "COM: " .$com;
                }

                if ($modeBilling) {
                    addToBillingArray($arrBilling, 'cpa', $com, $trader_id, true);
                }
            }
        }
    }
    
	if ($_GET['trackCom']==1) 	echo 'com-2 : ' . $com . '<br>';
	
	/////////////////////////// spread
      if ($merchantType == 'forex' ) {
	// die ('com: ' . $com);
		  
		
			if (!empty((float) $revenue_spread['amount']) &&  (float) $revenue_spread['amount'] > 0 && $set->deal_revshare_spread==1){
				// die($revenue_spread['amount']);
			// if($revenue_spread['amount']) {
					// die ('speard111');
		    /*         $sql =  "SELECT SUM(amount) AS amount FROM data_sales 
                     WHERE merchant_id = '" . $merchant_id . "' AND rdate BETWEEN '" . $startdate . "' AND '" . $enddate . "' 
                       AND type = 'deposit' AND trader_id = '" . $trader_id . "' "
                    . "AND affiliate_id = '" . $affiliate_id . "'" . $strSqlProfileId;

            $traderInfoTotal = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__)); */
			$merchantInfo = getMerchants($merchant_id,0);
            // $merchantInfo = mysql_fetch_assoc(function_mysql_query("SELECT * FROM merchants WHERE id = '" . $merchant_id . "'",__FILE__));
            $productType  = $merchantType;

            $sql =  'SELECT SUM(spread) AS sa FROM data_stats 
                     WHERE 1=1 and type = "position" AND affiliate_id = "' . $affiliate_id . '" AND merchant_id = "' . $merchant_id . '" 
                       AND rdate BETWEEN "' . $startdate . '" AND "' . $enddate 
                       . '" AND trader_id = "' . $trader_id . '"' . $strSqlProfileId;

            $raQ = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));		
            $revenueAmount = $raQ['sa'];
            $com += round(($revenueAmount * $revenue_spread['amount']) / 100, 2);
            
            if ($modeBilling) {
                addToBillingArray($arrBilling, 'revenue_spread', $com, $trader_id, true);
			}
			}
			
			if ($_GET['trackCom']==1) 	echo 'com-3 : ' . $com . '<br>';
		


		if($lots['amount']  && $transactionRow['type']=='lots') {
			
 $traderLotsAmount = ($transactionRow['amount']);
 

if (empty($traderLotsAmount)) {
    
		
			           $sql =  'SELECT SUM(spread) AS sa FROM data_stats 
                     WHERE 2=2 and type = "position" and turnover<>0.0  AND affiliate_id = "' . $transactionRow['affiliate_id'] . '" AND merchant_id = "' . $transactionRow['merchant_id'] . '" 
                       AND rdate BETWEEN "' . $startdate . '" AND "' . $enddate 
                       . '" AND trader_id = "' . $transactionRow['trader_id'] . '"' . $strSqlProfileId;

            $traderInfoTotal = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
}else {
            $traderInfoTotal['amount'] = $traderLotsAmount;
}
            //$merchantInfo = mysql_fetch_assoc(function_mysql_query("SELECT * FROM merchants WHERE id = '" . $transactionRow['merchant_id'] . "'"));
            $productType  = $merchantType;

            $traderCom = (($lots['amount'] *$traderInfoTotal['amount']));
			
			// var_dump($lots);
			
            $com += $traderCom;
            
            if ($modeBilling) {
                addToBillingArray($arrBilling, 'revenue_spread', $com, $transactionRow['trader_id'], true);
			}
			
		}
		
		if ($_GET['trackCom']==1) 	echo 'com-4 : ' . $com . '<br>';
	}
		      

    if ($onlyRevenueShare && $revenue['amount']) {
		
		
		
        if (strtolower($merchantType) == 'sportsbetting' || strtolower($merchantType) == 'casino') {
            $strWhere = 'WHERE merchant_id = ' . $merchant_id . ' ' . ($banner_id != 0 ? ' AND banner_id = "' . $banner_id . '" ' : '') . ' ' 
                    .   ($group_id != -1 ? ' AND group_id = "' . $group_id . '" ' : '') 
                    .   ' AND rdate BETWEEN "' . $startdate . '" AND "' . $enddate . '" AND trader_id = "' . $trader_id 
                    .   '" AND affiliate_id = "' . $affiliate_id . '"';
            
            $revenueAmount = $transactionRow['amount'];
        } else {
            
      
		
	
				
            
			if ($transactionRow['amount']<>0) {
				$revenueAmount = $transactionRow['amount'];
			}
			else {
			// NEW.
            $sql = "SELECT SUM(amount) AS amount FROM data_sales 
                    WHERE merchant_id = '" . $merchant_id . "' 
                      AND rdate BETWEEN '" . $startdate . "' AND '" . $enddate . "' AND type = 'deposit' 
                      " . (isset($trader_id) && !empty($trader_id) ? ' AND trader_id = ' . $trader_id : '') 
                        . " AND affiliate_id = '" . $affiliate_id . "'" . $strSqlProfileId;
            
            $traderInfoTotal = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
            

	
	
			
            if ($merchantRow['rev_formula']) {
				
                $sql = "SELECT type, amount FROM data_sales 
                        WHERE merchant_id = '" . ($merchant_id) . "' AND  rdate BETWEEN '" . $startdate . "' AND '" . $enddate . "' 
                          AND trader_id = '" . $trader_id . "' AND affiliate_id = '" . $affiliate_id . "'" . $strSqlProfileId;
                
                $saleqq = function_mysql_query($sql,__FILE__,__FUNCTION__);
                
                while ($saleww = mysql_fetch_assoc($saleqq)) {
                    if ($saleww['type'] == "bonus") {
                        $bonus = $saleww['amount'];
                    }
                    
                    if ($saleww['type'] == "withdrawal") {
                        $withdrawal = $saleww['amount'];
                    }
                    
                    if ($saleww['type'] == "chargeback") {
                        $chargeback = $saleww['amount'];
                    }
                }
                
                $a             = $traderInfoTotal['amount']; // Deposits
                $b             = $bonus;                     // Bonus Amount
                $c             = $withdrawal;                // Withdrawals Amount
                $d             = $chargeback;                // ChargeBacks Amount
                $string        = str_replace(array('{deposits}', '{bonus}', '{withdrawals}', '{chargebacks}'), array('a', 'b', 'c', 'd'), $merchantRow['rev_formula']);
                $revenueAmount = eval('return ' . preg_replace('/([a-zA-Z0-9])+/', '\$$1', $string) . ';');
				// die($revenueAmount);
                
            } else {
                $revenueAmount = $traderInfoTotal['amount'];
            }
        }
		}

		

        $com += round(($revenueAmount * $revenue['amount']) / 100, 2);
        // end else of binary option revenue
        
        if ($modeBilling) {
            addToBillingArray($arrBilling, 'revenue', $com, $trader_id, true);
        }
    }
    // end of only revenue share
	
	if ($_GET['trackCom']==1) 	echo 'com-5 : ' . $com . '<br>';
	
	

	   if ($transactionRow['type']=='pnl' && $set->deal_pnl==1 ) {
		

		/* if ($transactionRow['type']=='pnl' && $transactionRow['trader_id']=='3400704'){
     die()
 */
		
				
            
			if ($transactionRow['amount']<>0) {
				$pnlAmount = $transactionRow['amount'];
			
			}
			else {
			// NEW.
            $sql = "SELECT SUM(amount) AS amount FROM " . $set->pnlTable . " 
                    WHERE merchant_id = '" . $merchant_id . "' 
                      AND rdate BETWEEN '" . $startdate . "' AND '" . $enddate . "' AND type = 'pnl' 
                      " . (isset($trader_id) && !empty($trader_id) ? ' AND trader_id = ' . $trader_id : '') 
                        . " AND affiliate_id = '" . $affiliate_id . "'" . $strSqlProfileId;
            
            $traderInfoTotal = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
            

                $pnlAmount = $traderInfoTotal['amount'];
        }
		
/* 		if ($transactionRow['type']=='pnl' && $transactionRow['trader_id']=='3400704'){
	var_dump($pnl);
	echo '<br>$pnlAmount: ' ; 
	var_dump($pnlAmount);
	die();
	} */
		
        $com += round(($pnlAmount * $pnl['amount']) / 100, 2);

        // end else of binary option revenue
        
        if ($modeBilling) {
            addToBillingArray($arrBilling, 'PNL', $com, $trader_id, true);
        }
    }
    // end of only pnl share
	if ($_GET['trackCom']==1) 	echo 'com-6 : ' . $com . '<br>';
    
	
	//start position revenue share
	
	if ( $transactionRow['isFTD']==false && $transactionRow['amount']>0 && $transactionRow['type']== 'volume' && $AffDealsArry['positions_rev_share']>0) {
	
	if($_GET['debug2'] == 1){
	    echo " \n transactionRow:";
	    var_dump($transactionRow);
	    var_dump($AffDealsArry);
	    echo "\n transactionRow_END";
	}
	
	 $com += round(($transactionRow['amount'] * $AffDealsArry['positions_rev_share']['amount']) / 100, 2);
        
        
        if ($modeBilling) {
            addToBillingArray($arrBilling, 'revenue', $com, $trader_id, true);
        }
	}
	//end position revenue share
	
	if ($_GET['trackCom']==1) 	echo 'com-7 : ' . $com . '<br>';
        
		
	
	if ((float) $cpi['amount'] > 0 && $transactionRow['type']== 'install' ) {

	
				$com += $cpi['amount'] * 1;
				
				if ($modeBilling) {
					addToBillingArray($arrBilling, 'cpi', $com, $trader_id, true);
				}
    }
	if ($_GET['trackCom']==1) 	echo 'com-8 : ' . $com . '<br>';
	
		
		
		
    
    if ((float) $cpl['amount'] > 0) {

	$tier = false; // TODO
		global $affiliateCommissionCPLArray;
		if ($tier) {
			if (isset($affiliateCommissionCPLArray[$merchant_id][$trader_id][$affiliate_id]) ) {
					$totalAccounts = $affiliateCommissionCPLArray[$merchant_id][$trader_id][$affiliate_id] ;
			}
			else{
				$sql = "SELECT count(id) as count FROM data_reg 
						WHERE merchant_id = '" . $merchant_id . "' AND trader_id = '" . $trader_id . "' 
						  AND affiliate_id = '" . $affiliate_id . "' AND rdate BETWEEN '" . $startdate 
						  . "' AND '" . $enddate . "'" . $strSqlProfileId;

				$totalAccounts = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
				$affiliateCommissionCPLArray[$merchant_id][$trader_id][$affiliate_id]= 	$totalAccounts;
			}
			if (!empty($totalAccounts['count'])) {
				$com += $cpl['amount'] * $totalAccounts['count'];
				
				if ($modeBilling) {
					addToBillingArray($arrBilling, 'cpl', $com, $trader_id, true);
				}
			}
			
		}
		else {
				$com += $cpl['amount'] * 1;
				
				if ($modeBilling) {
					addToBillingArray($arrBilling, 'cpl', $com, $trader_id, true);
				}
		}
        
    }
	if ($_GET['trackCom']==1) 	echo 'com-9 : ' . $com . '<br>';
	
	

	if ((float) $cpm['amount'] > 0 || (float) $cpc['amount'] > 0) {
		global $affiliateCommissionCPMCPCArray;
		
		if (isset($affiliateCommissionCPMCPCArray[$merchant_id][$affiliate_id]) ) {
					$totalAccounts = $affiliateCommissionCPMCPCArray[$merchant_id][$affiliate_id] ;
		}
		else{
			$sql =  "SELECT SUM(views) AS views, SUM(clicks) AS clicks FROM traffic
					 WHERE rdate >= '" . $startdate . "' AND rdate <= '" . $enddate 
					  . "' AND affiliate_id = '" . $affiliate_id . "' AND merchant_id = '" . $merchant_id . "'";

			$totalTraffic = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
			$affiliateCommissionCPMCPCArray = $totalTraffic;
		}
		
        if ((float) $cpc['amount'] > 0) {
            $com += round($totalTraffic['clicks'] * $cpc['amount'], 2);
            
            if ($modeBilling) {
                addToBillingArray($arrBilling, 'cpc', $com, $trader_id, true);
            }
            
        } elseif ((float) $cpm['amount'] > 0) {
            $com += round(($totalTraffic['views'] / 1000) * $cpm['amount'], 2);
            
            if ($modeBilling) {
                addToBillingArray($arrBilling, 'cpm', $com, $trader_id, true);
            }
        }
    }
    if ($_GET['trackCom']==1) 	echo 'com-10 : ' . $com . '<br>';
	
	
    return $modeBilling ? $arrBilling : $com;
}


/**
 * Retrieve all affiliates.
 * 
 * @param  void
 * @return array
 */
function getAllAffiliates()
{
    $arrResult = array();
    $sql       = 'SELECT id,username,first_name,last_name FROM affiliates WHERE valid = 1 ORDER BY id ASC';
    $resource  = function_mysql_query($sql,__FILE__,__FUNCTION__);

    while ($arrRow = mysql_fetch_assoc($resource)) {
        $arrResult[] = array(
            'id'         => $arrRow['id'],
            'username'   => $arrRow['username'],
            'first_name' => $arrRow['first_name'],
            'last_name'  => $arrRow['last_name'],
        );
    }
    return $arrResult;
}


/**
 * Retrieve the affiliate_id in order to use it in 'ctag'.
 * 
 * @param  string $strAffiliateId
 * @return int
 */
function retrieveAffiliateId($strAffiliateId)
{
    $firstBraketPosition  = strpos($strAffiliateId, '[');
    $secondBraketPosition = strpos($strAffiliateId, ']');
	if ($firstBraketPosition !== false && $secondBraketPosition !== false){
    $intAffiliateId       = substr($strAffiliateId, $firstBraketPosition + 1, $secondBraketPosition - $firstBraketPosition - 1);
    return $intAffiliateId;
	}
	return $strAffiliateId;
}


/**
 * Gets a list of merchant IDs, related to given affiliate.
 * 
 * @param  int $intAffId
 * @return array
 */
function getAffiliatesMerchants($intAffId)
{
    /* $sql = 'SELECT merchants AS merchants FROM affiliates WHERE id = ' . $intAffId . ' LIMIT 0, 1;';
    $arrResult = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
	 */
	$arrResult = getAffiliateRow($intAffId);
	
	
    
    if (!is_array($arrResult) || empty($arrResult)) {
        return [];
    }
    return explode('|', $arrResult['merchants']);
}


/**
 * Get URl and retrieve domain.
 * Send query-string data via CURL POST to the domain.
 * 
 * @param  string $strUrl
 * @return mixed
 */
function curlPost($strUrl)
{
    $result = NULL;
    $arrUrl = explode('?', $strUrl);
    $ch     = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    
    if (isset($arrUrl[1])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
    }
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = curl_exec($ch);
    curl_close($ch);
    unset($arrUrl);
    
    return $result;
}



function hideDemoAndLeads()
{
	$arrMers = getMerchants(0,1);
	foreach ($arrMers as $arrMer) {
		if ($arrMer['showLeadsNdemo'])
			return false;
	/* (int)$showdemo =  $arrMer['showLeadsNdemo'];
		if ($showdemo == 1) {
			// die ('show');
			return false;
		} */
	}
	return true;
}


function addHttpIfNeeded ($urlStr = '') {
	$parsed = parse_url($urlStr);
	if (empty($parsed['scheme'])) {
		$urlStr = 'http://' . ltrim($urlStr, '/');
	}
	return $urlStr;
}


function doSpotPost($url)
{
	$arrUrl  = explode('?', $url);
	$ch      = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $arrUrl[0]);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $arrUrl[1]);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	// var_dump($result);
	// $error = strtolower(getTag('<error>','<\/error>',$result));
	
		return $result;
	
	
}


/* 
function getClientIP() {

    if (isset($_SERVER)) {

        if (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
            return $_SERVER["HTTP_X_FORWARDED_FOR"];

        if (isset($_SERVER["HTTP_CLIENT_IP"]))
            return $_SERVER["HTTP_CLIENT_IP"];

        return $_SERVER["REMOTE_ADDR"];
    }

    if (getenv('HTTP_X_FORWARDED_FOR'))
        return getenv('HTTP_X_FORWARDED_FOR');

    if (getenv('HTTP_CLIENT_IP'))
        return getenv('HTTP_CLIENT_IP');

    return getenv('REMOTE_ADDR');
}
 */


function AffiliateDealType($merchant_id,$merchantsDealsArray) {

global $set;	


// var_dump($merchantsDealsArray);
// die();

		$merchantIDs = ($set->userInfo['merchants']);
		$merchantIDs = str_replace('|',",",$merchantIDs);
		$merchantsArr = explode(',',$merchantIDs);
		// var_dump($merchantsArr);
		
		$hasAccess= false;
		foreach($merchantsArr as $mer) {
			
			// echo 'mer:' . $mer . '<Br>';
			// echo 'merchant_id:' . $merchant_id . '<Br>';
			
			
			if ($mer ==$merchant_id) {
				
				$hasAccess=true;
				break;
			}
				
		
		}
		if (!$hasAccess) {
			// return -5;
			return false;
		}
		
				
$arrRow['affiliate_id'] = $set->userInfo['id'];
$arrRow['merchant_id'] = $merchant_id;
$arrRow['rdate'] = date('Y-m-d');
// var_dump($arrRow);
// die();

$deals =  extractDealTypes($arrRow, $merchantsDealsArray);
// var_dump($deals);
// die();
// var_dump($deals);
if ($deals['revenue']['amount']>0 || $deals['revenue_spread']['amount']>0 || $deals['positions_rev_share']['amount']>0|| $deals['lots']['amount']>0) {
				// die ('ggee');
	return "REV";
}
else if ($deals['min_cpa']['amount']>0 || $deals['cpa']['amount']>0 || $deals['tier']['amount']>0) {
	return "CPA";
}
else if ($deals['dcpa']['amount']>0 ) {
	return "DCPA";
}
else if ($deals['cpl']['amount']>0) {
	return "CPL";
}
else if ($deals['cpi']['amount']>0) {
	return "CPI";
}
else if ($deals['cpc']['amount']>0) {
	return "CPC";
}


return "ALL";

// var_dump($deals);
// die();


	/* 	
		if (!empty($merchantIDs))
		for($i=0;$i<count($merchantsArr);$i++) {
			$qry = "SELECT amount FROM `affiliates_deals` "
							. "WHERE affiliate_id='".$set->userInfo['id']."' and dealType='cpa'  and merchant_id=".$merchantsArr[$i]
							." ORDER BY rdate DESC LIMIT 0, 1;"; 
			   // die ($qry);             
			$cpaslt=mysql_fetch_assoc(function_mysql_query($qry));
			if ($cpaslt['amount'] > 0) {
				$isDealCpa = true;
			}    
		}
 */	

}



function getAffiliateTierDeal($merchant_id,$affiliate_id) {
	global $affiliateDealsTierArry;
		if (isset($affiliateDealsTierArry[$merchant_id][$affiliate_id]) ) {
							
								$strAffDealType = 					$affiliateDealsTierArry[$merchant_id][$affiliate_id] ;
							}
							else {
								    $sql = "SELECT IFNULL(tier_type, NULL) AS tier_type FROM affiliates_deals "
                                 . "WHERE affiliate_id = '" . $affiliate_id . "' "
                                 . "AND merchant_id = '" . $merchant_id . "' AND dealType = 'tier' "
                                 . "ORDER BY id DESC "
                                 . "LIMIT 0, 1;";
                            
                            $strAffDealType   = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
							
								$affiliateDealsTierArry[$merchant_id][$affiliate_id] = $strAffDealType;
							}
return $strAffDealType;
}



function getAffiliateRow($affiliate_id,$ignoreValid=0) {
	global $affiliatesRowsArray;
	
	// if (empty($affiliate_id))
		// return null;
	
	if (isset($affiliatesRowsArray[$affiliate_id]))
								$arrAffiliate = $affiliatesRowsArray[$affiliate_id];
	else {
							if ($ignoreValid==1)
							$sql = 'SELECT * FROM affiliates WHERE 1 = 1 AND id = ' . $affiliate_id . ' LIMIT 0, 1;';
						else
							$sql = 'SELECT * FROM affiliates WHERE valid = 1 AND id = ' . $affiliate_id . ' LIMIT 0, 1;';
				
// die($sql);				
                            $arrAffiliate = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
							
								$affiliatesRowsArray[$affiliate_id]= 	$arrAffiliate ;
			}
							
							
return $arrAffiliate;
}



function getAffiliateCPADeal($merchant_id,$affiliate_id) {
	global $affiliatesDealsCPA;
	if (isset($affiliatesDealsCPA[$merchant_id][$affiliate_id]))
					$arrAffiliate = $affiliatesDealsCPA[$merchant_id][$affiliate_id];
	else {
							$qry = "SELECT amount FROM `affiliates_deals` WHERE affiliate_id='".$affiliate_id."' and dealType='cpa' and amount>0 and merchant_id=".$merchant_id . " order by id desc limit 1";
							// die ($qry);
							$rsc= function_mysql_query($qry,__FILE__,__FUNCTION__);
                            $arrAffiliate = mysql_fetch_assoc($rsc);
							
							$affiliatesDealsCPA[$merchant_id][$affiliate_id]= 	$arrAffiliate ;
	}
return $arrAffiliate;
}

 function getAffiliatesDealsREVLimit($intMerchantId, $intAffId  , $strDateFrom,$strDateTo, $strTypeOfTimePeriod,$arrSqlResult) {
	global $affiliatesDealsREVLimit; 
	if (empty($rdate))
		$rdate = " rdate " ;
	
		$processedDate = $rdate . (empty($strTypeOfTimePeriod) ? " <= '" . $arrSqlResult[0]['rdate'] . "' " : $strTypeOfTimePeriod);
		$processedDate = str_replace(' ','',$processedDate);
		$processedDate = str_replace('-','',$processedDate);
		$processedDate = str_replace(':','',$processedDate);
		$processedDate = str_replace('<=','',$processedDate);
		$processedDate = str_replace('/','',$processedDate);
		$processedDate = str_replace('.','',$processedDate);
		$processedDate = str_replace('_','',$processedDate);
				
			if (isset($affiliatesDealsREVLimit[$intMerchantId][$affiliate_id][$processedDate] ))
					$arrAffiliate = $affiliatesDealsREVLimit[$intMerchantId][$affiliate_id][$processedDate];
	else {

	$sql = "SELECT amount AS revenue FROM affiliates_deals
                WHERE affiliate_id = " . $intAffId 
                . " AND merchant_id = " . $intMerchantId . " AND " . $rdate 
                . (empty($strTypeOfTimePeriod) ? " <= '" . $arrSqlResult[0]['rdate'] . "' " : $strTypeOfTimePeriod) 
                . " AND dealType = 'revenue' 
                ORDER BY rdate LIMIT 0, 1;";
				// die ($sql);
	$arrAffiliate = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
							$affiliatesDealsREVLimit[$intMerchantId][$affiliate_id][$processedDate]= 	$arrAffiliate ;
	}
return $arrAffiliate;

 }
 
 function getAffiliateDealByType($intMerchantId, $affiliate_id  , $strRDate,$strDealType) {
	global $affiliatesDealsArray; 
			
		$processedDate = $strRDate;
		$processedDate = str_replace(' ','',$processedDate);
		$processedDate = str_replace('-','',$processedDate);
		$processedDate = str_replace(':','',$processedDate);
		$processedDate = str_replace('<=','',$processedDate);
		$processedDate = str_replace('/','',$processedDate);
		$processedDate = str_replace('.','',$processedDate);
		$processedDate = str_replace('_','',$processedDate);
		
		
		// if (isset($affiliatesDealsArray[$intMerchantId][$affiliate_id][$strDealType][$processedDate] ))
	// populate the array if not set	
	if (!isset($affiliatesDealsArray[$intMerchantId][$affiliate_id][$strDealType])) 
		{
	//else { 
		/* $sql = "SELECT amount AS amount FROM affiliates_deals 
                WHERE affiliate_id = " . $affiliate_id . " AND " . $intMerchantId . " = merchant_id 
				AND rdate <= '" . $strRDate . " 23:59:59' 
                    AND dealType = '" . $strDealType . "' 
                ORDER BY rdate DESC 
                LIMIT 0, 1;";
				 */
				
				/* 
				$sql = "SELECT rdate, amount AS amount FROM affiliates_deals 
                WHERE affiliate_id = " . $affiliate_id . " AND " . $intMerchantId . " = merchant_id 
				AND dealType = '" . $strDealType . "' 
				 and rdate <='" . $strRDate . "' 
				order by rdate desc";//  13/10/2016 removed limit 1
				 */
				
				$sql = "SELECT rdate, amount AS amount FROM affiliates_deals 
                WHERE affiliate_id = " . $affiliate_id . " AND " . $intMerchantId . " = merchant_id 
				AND dealType = '" . $strDealType . "' 
				 
				order by rdate desc";//  13/10/2016 removed limit 1
				
				
				//order by rdate desc limit 1";  
				// CHECK THIS IN THE FUTURE
				
				/* if ($strDealType=='min_cpa' && $_GET['nir']){
		var_dump( $sql);
		die ();
	} */
	// echo $sql.'<br>';
				
				$resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
				$arrResult = array();
				while($arrAffiliate = mysql_fetch_assoc($resource)){
							$arrResult[] =$arrAffiliate ;
				}
							// $affiliatesDealsArray[$intMerchantId][$affiliate_id][$strDealType][$processedDate]= 	$arrAffiliate ;
				$affiliatesDealsArray[$intMerchantId][$affiliate_id][$strDealType] = $arrResult;

	//}
		}
				
// fetch the relevant row from deals array

				// $arrAffiliate = $affiliatesDealsArray[$intMerchantId][$affiliate_id][$strDealType][$processedDate];
					$arrAffiliate = $affiliatesDealsArray[$intMerchantId][$affiliate_id][$strDealType];
					
					//TODO: make what was the last deal according to strRDate and dealtype=  $strDealType for merchant_id and affiliate_id
					 foreach ($arrAffiliate as $arrAffiliateDate){
								if 	(strtotime($arrAffiliateDate['rdate']) <= strtotime($strRDate)){
									return $arrAffiliateDate;
								}
					} 
		
return $arrResult;

 }
			

function getCreativeInfo ($banner_id,$justBasicData=0) {
	global $creativesRowsArray;
		$bannerInfo = ['id' => '', 'type' => '', 'title' => '',];
                    if (isset($banner_id) && !empty($banner_id)) {
						
						if ($justBasicData==1)
							$fields = ",mc.id,mc.type,mc.title";
						else 
							$fields = ",mc.* ";
						
						if (isset($creativesRowsArray[$banner_id]))
								$bannerInfo = $creativesRowsArray[$banner_id];
							else {
							$sql = "SELECT l.title as language_name".$fields." FROM merchants_creative mc  left join languages l on l.id = mc.language_id "
                                . "WHERE 1 = 1 AND mc.id = " . $banner_id . " "
                                . "LIMIT 0, 1;";
							
                            $bannerInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
							
								$creativesRowsArray[$banner_id]= 	$bannerInfo ;
							}
							
						
                    

                        // $bannerInfo = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
                    }
					return $bannerInfo;
}


function getExistingDealTypesForAffiliateArray($merchant_id,$affiliate_id) {
	global $existingDealTypesForAffiliateArray;
	
	
	if (!empty($existingDealTypesAllMerchantsForAffiliateArray[$merchant_id][$affiliate_id]) && empty($existingDealTypesForAffiliateArray[$merchant_id][$affiliate_id]))
			 $existingDealTypesForAffiliateArray[$merchant_id][$affiliate_id] = $existingDealTypesAllMerchantsForAffiliateArray[$merchant_id][$affiliate_id];
		
		
	
		if (empty($existingDealTypesForAffiliateArray[$merchant_id][$affiliate_id]))
		{	
	
					// $sql = "SELECT DISTINCT dealType as dealType FROM affiliates_deals WHERE affiliate_id =".$affiliate_id." AND ".$merchant_id." = merchant_id AND amount >0";
					// 2016-12-26
					$sql = "SELECT DISTINCT dealType as dealType FROM affiliates_deals WHERE affiliate_id =".$affiliate_id." AND ".$merchant_id." = merchant_id ";
					// die ($sql);
					$rsc = function_mysql_query($sql,__FILE__,__FUNCTION__);
					
					$hasDeal = false;
					//if (mysql_num_rows($rsc)>0){
						if (! $rsc){
						  // If sql returns false
						}
						else{
						while( $dealInfo = mysql_fetch_assoc($rsc)) {
									$hasDeal = true;
									$dealinfoType = strtolower($dealInfo['dealType']);
									$existingDealTypesForAffiliateArray[$merchant_id][$affiliate_id][$dealinfoType]= 	$dealinfoType;
									
									
									/* if ($dealType==$dealinfoType)
										$theDeal = $dealinfoType;
									die ($theDeal); */
						}
					}
					if ($hasDeal == false)
					{
					$existingDealTypesForAffiliateArray[$merchant_id][$affiliate_id][] =""; 
				}
		}
		// var_dump($existingDealTypesForAffiliateArray[$merchant_id][$affiliate_id]);
		// die();
	return $existingDealTypesForAffiliateArray[$merchant_id][$affiliate_id];
}


function getExistingDealTypesAllMerchantsForAffiliateArray($affiliate_id,$merchantDealTypeDefaults) {
	global $existingDealTypesAllMerchantsForAffiliateArray;
	
		$merchants = getMerchants(0,1);
		
		
		foreach ($merchants as $merchant) {
			$merchant_id = isset($merchant['merchant_id']) ? $merchant['merchant_id'] : $merchant['id'];
			
			
			
			if (empty($existingDealTypesAllMerchantsForAffiliateArray[$merchant_id][$affiliate_id]) 
			
			)
			{	
		
						$sql = "SELECT DISTINCT dealType as dealType FROM affiliates_deals WHERE affiliate_id =".$affiliate_id." AND ".$merchant_id." = merchant_id AND amount >0";
						 // echo ($sql);
						$rsc = function_mysql_query($sql,__FILE__,__FUNCTION__);
						
						$hasDeal = false;
						//if (mysql_num_rows($rsc)>0){
							if (! $rsc){
							  // If sql returns false
							}
							else{
							while( $dealInfo = mysql_fetch_assoc($rsc)) {
										$hasDeal = true;
										$dealinfoType = strtolower($dealInfo['dealType']);
										// die ('deal: ' . $dealinfoType);
										$existingDealTypesAllMerchantsForAffiliateArray[$merchant_id][$affiliate_id][$dealinfoType]= 	$dealinfoType;
										
										
										/* if ($dealType==$dealinfoType)
											$theDeal = $dealinfoType;
										die ($theDeal); */
							}
						}
						if ($hasDeal == false || true)  // small hack to get all deals + default for aff
						{
							
						foreach ($merchantDealTypeDefaults  as $defMerDeal=>$value) {
							// array(11) { ["cpa_amount"]=> string(1) "0" ["dcpa_amount"]=> string(1) "0" ["revenue_amount"]=> string(2) "30" ["lots_amount"]=> string(1) "0" ["pnl_amount"]=> string(1) "0" ["revenue_spread_amount"]=> string(1) "0" ["cpl_amount"]=> string(1) "0" ["cpc_amount"]=> string(1) "0" ["cpm_amount"]=> string(1) "0" ["min_cpa_amount"]=> string(1) "0" ["positions_rev_share_amount"]=> string(1) "0" }
							if ($value>0) {
								$dealName = trim(str_replace('_amount','',$defMerDeal));
							$existingDealTypesAllMerchantsForAffiliateArray[$merchant_id][$affiliate_id][$dealName] =$value; 
							}
						}
					}
			}
		// var_dump($existingDealTypesAllMerchantsForAffiliateArray[$merchant_id][$affiliate_id]);
		// die();
		}
	return $existingDealTypesAllMerchantsForAffiliateArray[$merchant_id][$affiliate_id];
}

function getAffiliatesDealsREV($intMerchantId, $intAffId  , $strDateFrom,$strDateTo, $strTypeOfTimePeriod) {
	global $affiliatesDealsREV; 
	$rsltArry = array();		
	$processedDate = $rdate . (empty($strTypeOfTimePeriod) ? " <= '" . $arrSqlResult[0]['rdate'] . "' " : $strTypeOfTimePeriod);
	$processedDate = str_replace(' ','',$processedDate);
	$processedDate = str_replace('-','',$processedDate);
	$processedDate = str_replace(':','',$processedDate);
	$processedDate = str_replace('<=','',$processedDate);
	$processedDate = str_replace('/','',$processedDate);
	$processedDate = str_replace('.','',$processedDate);
	$processedDate = str_replace('_','',$processedDate);
	if (isset($affiliatesDealsREV[$intMerchantId][$affiliate_id][$processedDate] ))
		$rsltArry = $affiliatesDealsREV[$intMerchantId][$affiliate_id][$processedDate];
	else {
		$rdate = ' rdate ';
		if (!empty($strTypeOfTimePeriod)) {
			if (false !== strpos($strTypeOfTimePeriod, '=')) {
				$rdate = ' DATE(rdate) ';
			}
		}
		 $sql = "SELECT amount AS revenue, rdate AS rdate FROM affiliates_deals
					WHERE affiliate_id = " . $intAffId 
					. " AND merchant_id = " . $intMerchantId . " AND " . $rdate 
					. (empty($strTypeOfTimePeriod) ? " BETWEEN '" . $strDateFrom . "' AND '" . $strDateTo . "' " : $strTypeOfTimePeriod) 
					. " AND dealType = 'revenue' 
					ORDER BY rdate;";
		$rsc = function_mysql_query($sql,__FILE__,__FUNCTION__);
		while ($arrAffiliate = mysql_fetch_assoc($rsc)){
			$rsltArry[]=$arrAffiliate;
		}
								$affiliatesDealsREV[$intMerchantId][$affiliate_id][$processedDate]= 	$rsltArry ;
	}
	return $rsltArry;
}




function getAffiliateFullTierDeal($merchant_id,$affiliate_id) {
	global $affiliateFullTierDeal;
	$rsltArry = array();
	// die ($affiliate_id);
		if (isset($affiliateFullTierDeal[$merchant_id][$affiliate_id]) ) {
				$strAffDealType = 					$affiliateFullTierDeal[$merchant_id][$affiliate_id] ;
				$rsltArry = $strAffDealType;
		}
		else {
			$counter= 0 ;
					$sql = "SELECT *,unix_timestamp(affiliates_deals.rdate) as unix FROM affiliates_deals "
                . "WHERE 5=5 and affiliate_id = '" . $affiliate_id . "' AND merchant_id = '" .$merchant_id . "' AND dealType = 'tier' "
                . "ORDER BY rdate desc, amount ASC";
                    $strAffDealType   = function_mysql_query($sql,__FILE__,__FUNCTION__);
					while ($arrAffiliate = mysql_fetch_assoc($strAffDealType)){
						// $rsltArry[$arrAffiliate['unix']] = $arrAffiliate;
						$rsltArry[$counter] = $arrAffiliate;
						$counter++;
					}
					$affiliateFullTierDeal[$merchant_id][$affiliate_id] = $rsltArry;
		}
		return $rsltArry;
}

function getDBCountries() {
			$rsltArry = array();
			$sql = "SELECT * FROM countries";
			$strCountries   = function_mysql_query($sql,__FILE__,__FUNCTION__);
			while ($countries = mysql_fetch_assoc($strCountries)){
				$rsltArry[$countries['code']] = $countries['title'];
			}
			return $rsltArry;

}

function affiliateGroupsArray(){
	global $groupsArray;
	if (empty($groupsArray)){
	$qq=function_mysql_query("SELECT id,title FROM groups WHERE valid='1' ORDER BY title ASC",__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) {
		$groupsArray[$ww['id']] = $ww['title'];
	}
	}
	return $groupsArray;
	
}

function affiliateStatusArray() {
	$statusArray = [];
	$qq=function_mysql_query("SELECT id,title FROM affiliates_status WHERE valid='1' ORDER BY title ASC",__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) {
		$statusArray[$ww['id']] = $ww['title'];
	}
	return $statusArray;	
}
//FUNCTION to fetch all affiliates
function getAllAffiliatesForPermissions() {
	$afArray = [];
	$qq=function_mysql_query("SELECT id,username,CONCAT(first_name, ' ' , last_name ) as name FROM affiliates WHERE  valid='1'",__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) {
		
		$afArray[$ww['id']] = $ww['name'];
	}
	return $afArray;	
}



function getLongCountries($tbl=""){
	
	$countryArray = [];
	
		$qq=function_mysql_query("SELECT c.id,upper(c.title) as countryLONG ,upper(c.code) as countrySHORT  FROM countries c where id >1",__FILE__,__FUNCTION__);
	if($tbl == 'stats'){
		// $qq=function_mysql_query("SELECT c.id,ipc.countryLONG FROM ip2country ipc INNER JOIN countries c on c.code = ipc.countrySHORT",__FILE__,__FUNCTION__);
		while ($ww=mysql_fetch_assoc($qq)) {		
			$countryArray[$ww['id']] = $ww['countryLONG'];
		}
	}
	else{
		// $qq=function_mysql_query("SELECT countrySHORT,countryLONG FROM ip2country",__FILE__,__FUNCTION__);
		while ($ww=mysql_fetch_assoc($qq)) {
			$countryArray[$ww['countrySHORT']] = $ww['countryLONG'];
		}
	}
	
	
	return $countryArray;
	
}

function listProducts($id=0,$text=0,$ignoreValid=0,$orderbyName=0) {
	global $listOfProducts;
	
	global $set;
	// die ($id);
	
	if (empty($listOfProducts)){
	$where='';
	if($set->userInfo['level']=='manager' AND $set->isNetwork){
		$where.=' AND id='.aesDec($_COOKIE['mid']);
	}
	$sql = "SELECT id,title FROM products_items WHERE ".($ignoreValid ? "1=1" : "valid!='0'")." ORDER BY  lower(title)";
	// die ($sql);
	$qq=function_mysql_query( $sql ,__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) {
		$listOfProducts[$ww['id']] = $ww;
		
		/* echo 'wwid = ' . $ww['id']. '<br>';
		echo 'id = ' . $id. '<br>'; */
		}
		// die();
	}
	
		
		foreach ($listOfProducts as $ww){
		
		if ($text AND $id == $ww['id']) 
				return $ww['name'];
			$html .= '<option value="'.$ww['id'].'" '.($ww['id'] == $id ? 'selected' : '').'>'.$ww['title'].'</option>';
		}
		
	
	
	return $html;
	}
	
function getFixedSizeBanner($file,$fixedwidth=0,$fixedheight=0) {
	global $set;
	$exp=explode(".",$file);
	$ext = strtolower($exp[count($exp)-1]);
	if ($ext == "swf") $type = "flash";
		else if ($ext == "gif" OR $ext == "jpg" OR $ext == "jpeg" OR $ext == "png") $type = "image";
	if ($type != "flash" AND $type != "image") return false;
	try{
	    if (file_exists($_SERVER["DOCUMENT_ROOT"] . '/' . $file)) {
            list($width,$height) = getimagesize($set->webAddress . $file);
        }

	}
	catch(Exception $e){
		$width = 0;
		$height = 0;
	}
	/* $w = round((($percent_resizing/100)*$width));
	$h = round((($percent_resizing/100)*$height)); */
$w = $width;
$h = $height;

	if ($type == "image") {
	 	if($w == 0 && $h == 0)
			$html = '';
		else 
			$html = '<img border="0" src="'.$file.'" width="'.$fixedwidth.'" height="'.$fixedheight.'" alt="" />';
		} else if ($type == "flash") {
		$html = '<OBJECT id="affMV737" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0" width="'.$w.'" height="'.$h.'">
				<param name="movie" value="'.$file.'">
				<param name="wmode" value="transparent">
				<param name="allowScriptAccess" value="always">
				<param name="flashvars" value="creativeURL='.$set->webAddress.'banner_qa.php">
				<embed src="'.$file.'" width="'.$w.'" height="'.$h.'" flashvars="creativeURL='.$set->webAddress.'banner_qa.php" allowScriptAccess="always" NAME="AffMV737" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></embed>
			</object>';
		} else return false;
	return $html;
	}
	
	function getProductCategoriesList($valid=0){
			if($valid)
				$where = "1=1";
			else
				$where = " valid = 1";
				
			$sql = "select * from products_cats where ". $where ." order by parent_id,id";
			$qqCats = function_mysql_query($sql);
			$result = array();
			while($wwCats = mysql_fetch_assoc($qqCats)){
					//$listCats .= "<li><div id=". $wwCats['id'] ." data-parentId=". $wwCats['parent_id'] .">". $wwCats['title'] ."</div></li>";
					
					if($wwCats['parent_id'] && array_key_exists($wwCats['parent_id'], $result)){
						$result[$wwCats['parent_id']]['sub_categories'][] = $wwCats;
					}
					else{
						$result[$wwCats['id']] = $wwCats;
					}
			}
			return $result;
	}
	
	function getAffiliateProductCategoriesList(){
			//$sql = "select * from products_cats where ". $where ." order by parent_id,id";
			//$sql= "SELECT pc.* FROM products_cats pc inner join products_items p on (p.cat_id = pc.id and p.valid!=0) WHERE pc.valid=1 ORDER BY pc.parent_id,pc.id ASC";
			$sql= "SELECT pc.* FROM products_cats pc  WHERE pc.valid=1 ORDER BY pc.parent_id,pc.id ASC";
			$qqCats = function_mysql_query($sql);
			$result = array();
			while($wwCats = mysql_fetch_assoc($qqCats)){
					//$listCats .= "<li><div id=". $wwCats['id'] ." data-parentId=". $wwCats['parent_id'] .">". $wwCats['title'] ."</div></li>";
					
					if($wwCats['parent_id'] && array_key_exists($wwCats['parent_id'], $result)){
						$result[$wwCats['parent_id']]['sub_categories'][] = $wwCats;
					}
					else{
						$result[$wwCats['id']] = $wwCats;
					}
			}
			return $result;
	}
	
	
	function processMicroPaymentRecord($record){
	global $set;
	
	if ($set->showMicroPaymentsOnReports==1 && !empty($set->showMicroPaymentsOnReportsRate) && $record['amount']<=$set->showMicroPaymentsOnReportsRate){
	// var_dump($record);
	// var_dump($set->showMicroPaymentsOnReportsRate);
	// die();
	
		return true;
	}
	
	return false;
}
	


/**
 * Get affiliate CPA Country group commission.
 * 
 * @param type $country
 * @param type $affiliate_id
 * @param type $merchant_id
 * @param type $date
 * @return type
 */
function getAffilliateCpaCountryGroupCommission($country, $affiliate_id, $merchant_id, $date)
{
    
    $result = 0;
    
    // Get merchant default commission
    $sql_find = 'SELECT * FROM cpa_countries_groups WHERE countries LIKE "%' . mysql_real_escape_string($country) . '%" AND merchant_id = "' . $merchant_id . '" LIMIT 1';
    $resource = function_mysql_query($sql_find, __FILE__, __FUNCTION__);
    if($resource){
        $my_last_value = mysql_fetch_assoc($resource);
        if($my_last_value){
            //$result = $my_last_value['value'];

            // Find affiliate commission
            $sql_find_my = 'SELECT * FROM cpa_group_delas WHERE group_id = "'.$my_last_value['id'].'" AND rdate <= "'.$date.'" AND merchant_id = "' . $merchant_id . '" AND affiliate_id = "' . $affiliate_id . '" ORDER BY rdate DESC LIMIT 1';
            $resource_my = function_mysql_query($sql_find_my, __FILE__, __FUNCTION__);
            if($resource_my){
                $my_last_value = mysql_fetch_assoc($resource_my);
                if($my_last_value){
                    $result =  $my_last_value['value'];
                }
            }
        }
    }


    return $result;
}
