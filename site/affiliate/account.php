<?php
/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */


require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin())
    _goto($lout);



$merchantIDs = ($set->userInfo['merchants']);
// die ('111:    ' . $merchantIDs);


$merchantIDs = str_replace('|',",",$merchantIDs);
$merchantsArr = explode(',',$merchantIDs);



				$LowestLevelDeal = 'ALL';
				if (isset($set->userInfo['dealsArray'])) {
$dealsArray = $set->userInfo['dealsArray'];
	// $allbrabdrsc = function_mysql_query($sql,__FILE__);
				// while ($brandsRow = mysql_fetch_assoc($allbrabdrsc)) {
				foreach ($dealsArray as $dealItem=>$value) {
				foreach ($merchantsArr as $brandsRow) {
					if ($brandsRow['id']==$dealItem) {
						
						$LowestLevelDeal = getLowestLevelDeal($LowestLevelDeal, $value);
						break;
					}
					}
				}
				}
			   $deal = $LowestLevelDeal;
/*
$showRev = 0;

if ($set->hideNetRevenueForNonRevDeals){
	$qry = "SELECT amount FROM `affiliates_deals` WHERE affiliate_id='".$set->userInfo['id']."' and dealType='revenue' and amount>0 and merchant_id in (". $merchantIDs . ")";
	$revrslt=mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
	if ($revrslt['amount']>0) {
		$showRev = 1;
	}else{
		$showRev = 0;
	}
}else{
	$showRev = 1;
}


$showFtdAmount = 0;
*/




$showRev = 0;
$showFtdAmount = 0;
$merchantsArr2 = Array();

for($i=0;$i<count($merchantsArr);$i++){
	
	$merID = $merchantsArr[$i];
	$merchantsArr2[$merID] = new stdClass();
	
	if ($set->hideNetRevenueForNonRevDeals){
		$qry = "SELECT amount FROM `affiliates_deals` WHERE affiliate_id='".$set->userInfo['id']."' and dealType='revenue' and amount>0 and merchant_id=".$merID;
		$revrslt=mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
		if ($revrslt['amount']>0) {
			$merchantsArr2[$merID]->showRev = 1;
			$showRev = 1;
			//$showRev = 0;
		}else{
			
			$merchantsArr2[$merID]->showRev = 0;
		}
	}else{
		$merchantsArr2[$merID]->showRev = 1;
		$showRev = 1;
	}



	if ($set->hideFTDamountForCPADeals){
		$qry = "SELECT amount FROM `affiliates_deals` WHERE affiliate_id='".$set->userInfo['id']."' and dealType='cpa' and amount>0 and merchant_id=".$merID;
		$cpaslt=mysql_fetch_assoc(function_mysql_query($qry,__FILE__));
		if ($cpaslt['amount']>0) {
			$showFtdAmount=1;
			$merchantsArr2[$merID]->showFtdAmount = 1;
		}else{
			$merchantsArr2[$merID]->showFtdAmount = 0;
		}
	} else {
		$showFtdAmount= 1;
		$merchantsArr2[$merID]->showFtdAmount = 1;
	}
}




switch ($act) {
	case "payment_save":
                //////////////////  AFFILIATE PAYMENT SAVE begin ////////////////////////////////////////////
                include '../public_html/common/AffiliatePaymentSave.php';
                //////////////////  AFFILIATE PAYMENT SAVE end //////////////////////////////////////////////
                // No need for // break;
                        
                        
	case "payment":
            //////////////////////////// ACCOUNT begin //////////////////////////////////////////
            include '../public_html/common/AffiliatePaymentDetails.php';
            //////////////////////////// ACCOUNT end ///////////////////////////////////////////
            theme();
            break;
                
                
                
	case "save":
		if ($errors) {
            
			} else {
			unset($db['username']);
			$db['id'] = $set->userInfo['id'];
			$db['ip'] = $set->userIP;
            if ($password)
                $db['password'] = md5($password);
			$db['udate'] = dbDate();
			$db['valid'] = 1;
            if ($newsletter)
                $db['newsletter'] = 1;
            else
                $db['newsletter'] = 0;
			// Strip Tags
			$db['first_name'] = strip_tags($db['first_name']);
			$db['last_name'] = strip_tags($db['last_name']);
			$db['phone'] = strip_tags($db['phone']);
			$db['country'] = strip_tags($db['country']);
			$db['website'] = strip_tags($db['website']);
			$db['mail'] = strip_tags($db['mail']);
			// Strip Tags
			$lastID=dbAdd($db,"affiliates");
			_goto($set->basepage.'?ty=1');
			}
	
	default:
		$pageTitle = lang('Account Details');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		$db=dbGet($set->userInfo['id'],"affiliates");
        if ($db['id'] != $set->userInfo['id'])
            _goto('/affiliate/');
		$arr = Array("1" => "Africa",
			"2" => "Afro Eurasia",
			"3" => "Americas",
			"4" => "Asia",
			"5" => "Australia",
			"6" => "Eurasia",
			"7" => "Europe",
			"8" => "North America",
			"9" => "South America",
			"10" => "United Kingdom",
			"11" => "World Wide");
		$expMarketInfo=explode(",",$db['marketInfo']);
		for ($i=1; $i<=11; $i++) {
            if (@in_array($i, $expMarketInfo))
                $selectedItems .= '<option value="' . $i . '">' . $arr[$i] . '</option>';
            else
                $unselectedItems .= '<option value="' . $i . '">' . $arr[$i] . '</option>';
			}
		$set->content .= '
		<div align="center">
		<form action="'.$set->SSLprefix.$set->basepage.'?act=save" method="post" onsubmit="return checkUpdate();" autocomplete="off">
		<table width="975" border="0" cellpadding="0" cellspacing="0">
			<tr>
				<td colspan="3" height="10"></td>
			</tr><tr>
				<td width="325" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" height="38" style="background: url('.$set->SSLprefix.'images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('ACCOUNT INFORMATION').'</td>
						</tr><tr>
							<td height="225" valign="top" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Username').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" value="'.$db['username'].'" disabled="disabled" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Password').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="password" name="password" id="password" value="" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Repeat Password').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="password" name="repassword" id="repassword" value="" style="width: 283px;" /></td>
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bottom.jpg);"></td>
						</tr><tr>
							<td height="10"></td>
						</tr><tr>
							<td align="left" height="38" style="background: url('.$set->SSLprefix.'images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('WEBSITE INFORMATION').'</td>
						</tr><tr>
							<td height="195" valign="top" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Website').' 1:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website]" id="website" value="'.($db['website'] ? $db['website'] : 'http://').'" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Website').' 2:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website2]" value="'.($db['website2'] ? $db['website2'] : 'http://').'" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Website').' 3:</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[website3]" value="'.($db['website3'] ? $db['website3'] : 'http://').'" style="width: 283px;" /></td>
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td>
				<td width="325" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" height="38" style="background: url('.$set->SSLprefix.'images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('INVOICE INFORMATION').'</td>
						</tr><tr>
							<td height="226" valign="top" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Street').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[street]" value="'.$db['street'].'" id="street" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Postal / Zip Code').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[postalCode]" value="'.$db['postalCode'].'" id="postalCode" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('City').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><input type="text" name="db[city]" value="'.$db['city'].'" id="city" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Country').':</td>
									</tr><tr>
										<td align="left" style="padding-left: 10px;"><select name="db[country]" style="width: 295px;" id="country"><option value="">'.lang('Choose Your Country...').'</option>'.getCountry($db['country']).'</select></td>
									</tr>
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bottom.jpg);"></td>
						</tr><tr>
							<td height="10"></td>
						</tr><tr>
							<td align="left" height="38" style="background: url('.$set->SSLprefix.'images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('MARKET INFORMATION').'</td>
						</tr><tr>
							<td height="184" valign="top" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bg.jpg); padding-left: 10px; padding-top: 10px;">
								<table><tr>
									<td>
										<select id="q1" size="10" multiple="true" style="width: 130px; height: 150px; overflow: auto; border: 1px #CECECE solid;">'.$unselectedItems.'</select>
									</td>
									<td>
										<table border="0" cellpadding="0" cellspacing="0">
										<tr><td><img border="0" src="'.$set->SSLprefix.'images/reg/right.jpg" alt="" onclick="moveMultiple(\'q1\',\'q2\',\'q2\',\'update\'); return false;" style="cursor: pointer;" /></td></tr>
										<tr><td height="3"></td></tr>
										<tr><td><img border="0" src="'.$set->SSLprefix.'images/reg/left.jpg" alt="" onclick="moveMultiple(\'q2\',\'q1\',\'q2\',\'update\'); return false;" style="cursor: pointer;" /></td></tr>
										</table>
									</td>
									<td><select id="q2" multiple="true" style="width: 130px; height: 150px; overflow: auto; border: 1px #CECECE solid;">'.$selectedItems.'</select></td>
								</tr></table>
								<input type="hidden" name="db[marketInfo]" id="update" value="'.$db['marketInfo'].'" />
							</td>
						</tr><tr>
							<td height="12" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td>
				<td width="325" rowspan="1" valign="top">
					<table width="325" border="0" cellpadding="0" cellspacing="0">
						<tr>
							<td align="left" height="38" style="background: url('.$set->SSLprefix.'images/reg/reg_box_top.jpg); padding-left: 10px; color: #FFFFFF; font-family: Calibri; font-size: 14px;">'.lang('CONTACT INFORMATION').'</td>
						</tr><tr>
							<td height="480" valign="top" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bg.jpg);">
								<table width="325" border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Company Name').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[company]" value="'.$db['company'].'" style="width: 283px;" /></td>
									</tr><tr>
										<td align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('Salutation').':</td>
										<td align="right" style="padding-right: 20px;"><input type="radio" name="db[gender]" value="male" '.($db['gender'] == "male" || !$db['gender'] ? 'checked="checked"' : '').' /> '.lang('Mr').'. <input type="radio" name="db[gender]" value="female" '.($db['gender'] == "female" ? 'checked="checked"' : '').' /> '.lang('Ms').'.</td>
									</tr><tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('First Name').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[first_name]" id="first_name" value="'.$db['first_name'].'" style="width: 283px;" /></td>
									</tr><tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Last name').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[last_name]" id="last_name" value="'.$db['last_name'].'" style="width: 283px;" /></td>
									</tr><tr>';
										if ($_SESSION['isam']==0) {
										$set->content .= '
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('E-mail').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[mail]" value="'.$db['mail'].'" id="mail" style="width: 283px;" /></td>
									</tr><tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;"><span class="required">*</span> '.lang('Phone number').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[phone]" value="'.$db['phone'].'" id="phone" style="width: 283px;" /></td>
									</tr><tr>
									
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('I.M. type').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;">
											<select name="db[IMUserType]" style="width: 295px;">
												<option value="">'.lang('Choose I.M. Type').'</option>
												<option value="Skype" '.($db['IMUserType'] == "Skype" ? 'selected="selected"' : '').'>'.lang('Skype').'</option>
												<option value="MSN" '.($db['IMUserType'] == "MSN" ? 'selected="selected"' : '').'>'.lang('MSN').'</option>
												<option value="Google Talk" '.($db['IMUserType'] == "Google Talk" ? 'selected="selected"' : '').'>'.lang('Google Talk').'</option>
												<option value="QQ" '.($db['IMUserType'] == "QQ" ? 'selected="selected"' : '').'>'.lang('QQ').'</option>
												<option value="ICQ" '.($db['IMUserType'] == "ICQ" ? 'selected="selected"' : '').'>'.lang('ICQ').'</option>
												<option value="Yahoo" '.($db['IMUserType'] == "Yahoo" ? 'selected="selected"' : '').'>'.lang('Yahoo').'</option>
												<option value="AIM" '.($db['IMUserType'] == "AIM" ? 'selected="selected"' : '').'>'.lang('AIM').'</option>
											</select>
										</td>
									</tr><tr>
										<td colspan="2" align="left" height="24" style="padding-left: 10px; color: #515151; font-family: Arial; font-size: 14px;">'.lang('I.M. account').':</td>
									</tr><tr>
										<td colspan="2" align="left" style="padding-left: 10px;"><input type="text" name="db[IMUser]" value="'.$db['IMUser'].'" style="width: 283px;" /></td>
									</tr>';
									}
									$set->content .= '
								</table>
							</td>
						</tr><tr>
							<td height="12" style="background: url('.$set->SSLprefix.'images/reg/reg_box_bottom.jpg);"></td>
						</tr>
					</table>
				</td>
			</tr><tr>
				<td colspan="3" height="10"></td>
			</tr><tr>
				<td colspan="2" align="left">
					<input type="checkbox" name="newsletter" '.($db['newsletter'] ? 'checked="checked"' : '').' /> '.lang('Yes, I would like to receive the Affiliate newsletter').'
				</td>
				<td align="right" style="padding-right: 10px;">
					<input type="submit" value="'.lang('Save').'" />
				</td>
			</tr>
		</table>
		</form>
		</div>';
		
		theme();
		break;
	
	
	case "commission":
		$pageTitle = lang('Your Commission Structure');
		$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
		
		$l=0;
		$merchantqq=function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY id ASC",__FILE__);
		
		$showFields= array();
		$showForex = 0;
		
		while ($merchantww=mysql_fetch_assoc($merchantqq)) {
			
			$showForex = strtolower($merchantww['producttype'])=='forex' ? 1 : 0;
			
			
			// die('frr');
            if (!chkMerchant($merchantww['id']))
                continue;
			
			
            unset($min_cpaAmount);
            unset($cpaAmount);
            unset($revenueAmount);
            unset($cplAmount);
            unset($cpcAmount);
			
            $sql = "SELECT *
FROM affiliates_deals
WHERE id IN (
    SELECT MAX(id)
    FROM affiliates_deals WHERE merchant_id='" . $merchantww['id'] . "' AND affiliate_id='" . $set->userInfo['id'] . "'  AND dealType='tier' and valid=1
    GROUP BY tier_amount
)";

            $commissionStructure[$merchantww['id']]['merchant_name'] = $merchantww['name'];
			
			$takeqq=function_mysql_query($sql,__FILE__);
			if (mysql_num_rows($takeqq) > 0) {
				while ($takeww=mysql_fetch_assoc($takeqq)) {

                    $commissionStructure[$merchantww['id']]['tier'][$takeww['tier_amount']] = [
                        'tier_amount' => $takeww['tier_amount'],
                        'amount' => $set->currency . ' ' . number_format($takeww['amount'], 2),
                        'tier_pcpa' => number_format($takeww['tier_pcpa'], 2) . '%',
                    ];
					
					$showFields['tier']=1;
					
                    /*
					$tierDealType .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td align="left"><b>'.$merchantww['name'].'</b></td>
							<td>'. $set->currency .' '.$takeww['tier_amount'].'</td>
							<td>'. $set->currency .' '.number_format($takeww['amount'],2).'</td>
							<td>% '.number_format($takeww['tier_pcpa'],2).'</td>
						</tr>';
                     */
						$l++;
					}
            }
					

				
				$dealTypes = array();
				$row = array();
				$row['rdate'] =  date('Y-m-d H:i:s');
				$row['affiliate_id'] = $set->userInfo['id'];
				$row['merchant_id'] = $merchantww['id'];
				$merDef = getMerchantDealTypeDefaults();
				$dealTypes = extractDealTypes($row, $merDef);
		
				
				
				$min_cpaAmount = getValue($dealTypes['min_cpa']);
				$cpaAmount = getValue($dealTypes['cpa']);
				$dcpaAmount = getValue($dealTypes['dcpa']);
				$revenueAmount = getValue($dealTypes['revenue']);
				$cplAmount = getValue($dealTypes['cpl']);
				$cpcAmount = getValue($dealTypes['cpc']);
				$pnlAmount = getValue($dealTypes['pnl']);
				$cpmAmount = getValue($dealTypes['cpm']);
				$revenueSpreadAmount = getValue($dealTypes['revenue_spread']);
				$positions_rev_shareAmount = getValue($dealTypes['positions_rev_share']);
				$lotsAmount = getValue($dealTypes['lots']);
				$cpiAmount = getValue($dealTypes['cpi']);
				
				
					if ($cpaAmount['amount']>0)
					$showFields['cpa']=1;

				if ($min_cpaAmount['amount']>0)
					$showFields['minDep']=1;
				if ($cplAmount['amount']>0)
					$showFields['cpl']=1;
				if ($cpmAmount['amount']>0)
					$showFields['cpm']=1;
				if ($cpcAmount['amount']>0)
					$showFields['cpc']=1;
				if ($dcpaAmount['amount']>0)
					$showFields['dcpa']=1;
					if ($pnlAmount['amount']>0)
					$showFields['pnl']=1;
				
				
								if ($lotsAmount['amount']>0)
					$showFields['lots']=1;
				
								if ($revenueAmount['amount']>0)
					$showFields['rev']=1;
				
								if ($positions_rev_shareAmount['amount']>0)
					$showFields['posrev']=1;
				
				if ($cpiAmount['amount']>0)
					$showFields['cpi']=1;
				
                                
                                $affiliate_geo_cpa = "";
                                
                                $sql_find = 'SELECT * FROM cpa_countries_groups WHERE merchant_id = "' . $merchantww['id'] . '"';
                                $resource = function_mysql_query($sql_find, __FILE__, __FUNCTION__);
                                while ($my_country = mysql_fetch_assoc($resource)) {

                                    $sql_find_my = 'SELECT * FROM cpa_group_delas WHERE group_id = "' . $my_country['id'] . '" AND merchant_id = "' . $merchantww['id'] . '" AND affiliate_id = "' . $set->userInfo['id'] . '" ORDER BY rdate DESC LIMIT 1';
                                    $resource_my = function_mysql_query($sql_find_my, __FILE__, __FUNCTION__);
                                    $my_last_value = mysql_fetch_assoc($resource_my);
                                    
                                    if(!empty($my_last_value['value'])){
                                        $affiliate_geo_cpa .= '<tr><td>'.$set->currency.'&nbsp;'.(!empty($my_last_value['value'])?$my_last_value['value']:$my_country['value']).'</td><td>'.(implode(', ', explode('|', $my_country['countries']))).'</td></tr>';
                                    }
                                }
                                
                                if(!empty($affiliate_geo_cpa)){
                                    $affiliate_geo_cpa_tmp = $affiliate_geo_cpa;
                                    $affiliate_geo_cpa = '<div class="question tooltip tooltip-big"><span class="tooltiptext"><table class="table"><tr><td><b>Amount</b></td><td><b>Country</b></td></tr>'.$affiliate_geo_cpa_tmp.'</table></span></div>';
                                }
                                


            $commissionStructure[$merchantww['id']]['other'][lang(ptitle('Minimum Deposit'))] = number_format($min_cpaAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('CPA')] = number_format($cpaAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('DCPA')] = number_format($dcpaAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('Revenue')] = number_format($revenueAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('Positions Rev. Share')] = number_format($positions_rev_shareAmount['amount'], 2);
            $commissionStructure[$merchantww['id']]['other'][lang('PNL')] = number_format($pnlAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('CPL')] = number_format($cplAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('CPC')] = number_format($cpcAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('CPM')] = number_format($cpmAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('Lots')] = number_format($lotsAmount['amount'], 0);
            $commissionStructure[$merchantww['id']]['other'][lang('CPI')] = number_format($cpiAmount['amount'], 0);


            /*
					$listDealType .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td align="left"><b>'.$merchantww['name'].'</b></td>'.
							($min_cpaAmount['amount']>0  && $set->showMiminumDepositOnAffAccount ==1?  '<td>'. $set->currency .' '.number_format($min_cpaAmount['amount'],0).'</td>' : '<td></td>') .'
							'.($cpaAmount['amount'] >0 ? '<td>'. $set->currency .' '.number_format($cpaAmount['amount'],0).' '.$affiliate_geo_cpa.'</td>' : ($showFtdAmount ? '<td></td>' : '<td></td>')).'
							'.($dcpaAmount['amount']>0 && $set->showDCPAonAffiliateComStruc ? '<td>% '.number_format($dcpaAmount['amount'],0).'</td>' : '<td></td>').'
							'. (allowView('af-ntrv',$deal,'fields')  && $revenueAmount['amount'] >0    ? '<td>% '.number_format($revenueAmount['amount'],0).'</td>' : ($showRev ? '<td></td>' : '<td></td>')).'
							'. (allowView('af-ntrv',$deal,'fields') && $positions_rev_shareAmount['amount']>0      ? '<td>% '.number_format($positions_rev_shareAmount['amount'], 2).'</td>' : '<td></td>').'
							'.($set->deal_pnl && $pnlAmount['amount']>0 ? '<td>% '.number_format($pnlAmount['amount'],0).'</td>' : '<td></td>').'
							'.($set->deal_cpl && $cplAmount['amount']>0 ? '<td>'. $set->currency .' '.number_format($cplAmount['amount'],0).'</td>' : '<td></td>').'
							'.($set->deal_cpc && $cpcAmount['amount']>0 ? '<td>'. $set->currency .' '.number_format($cpcAmount['amount'],0).'</td>' : '<td></td>').'
							'.($set->deal_cpm  && $cpmAmount['amount']>0? '<td>'. $set->currency .' '.number_format($cpmAmount['amount'],0).'</td>' : '<td></td>').'
							'.($showForex==1  && $lotsAmount['amount']>0? '<td>'. $set->currency .' '.number_format($lotsAmount['amount'],0).'</td>' : '<td></td>').'
							'.($cpiAmount['amount']>0? '<td>'. $set->currency .' '.number_format($cpiAmount['amount'],0).'</td>' : '<td></td>').'
						</tr>';
             */


					$l++;
				}

			
        ob_start();
        ?>
        <div align="center">
            <div class="normalTableTitle"><?= lang('Your Account Commission Structure'); ?></div>
                            <style>
                                .question {
                                    background: url(../images/question.png);
                                    background-repeat: no-repeat;
                                    width: 16px;
                                    height: 16px;
                                    top: 4px;
                                    left: 5px;
			}
                                .question.tooltip.tooltip-big .tooltiptext {
                                    bottom: auto;
                                    top: 100%;
                                    width: 350px;
                                }
                                .question.tooltip.tooltip-big .tooltiptext table{
                                    max-width: 400px;
                                }
                                .question.tooltip.tooltip-big .tooltiptext table td{
                                    color: #bfbfbf;
                                    padding: 0px 5px;
                                    vertical-align: top;
                                }
                                .question.tooltip.tooltip-big .tooltiptext table td:first-child{
                                    text-aligh: right;
                                }
                                .question.tooltip.tooltip-big .tooltiptext table td:last-child{
                                    text-align: left;
                                }
                            </style>

            <?php
            if (!empty($commissionStructure)) {
                foreach ($commissionStructure as $column_merchant) {
                    ?>
			
                    <table class="normal" width="90%" border="0" cellpadding="10" cellspacing="10" style="border-bottom:1px #999 solid;">
                        <tr>
                            <td width="50%" valign="top">

                                <table class="normal" border="0" cellpadding="5" cellspacing="5">
                                    <thead>


                                        <tr>
                                            <td style="text-align: left;" width="200"><?= lang('Merchant') ?></td>
                                            <?php
                                            $column_other_count = 0;
                                            foreach ($column_merchant['other'] as $column_name => $column_value) {
                                                if ($column_value > 0) {
                                                    ?>
                                                    <td style="min-width: 50px"><?= $column_name ?></td>    
                                                    <?php
                                                    $column_other_count++;
                                                }
                                            }
                                            if(!$column_other_count){
                                                ?><td>&mdash;</td><?php
                                            }
                                            ?>
                                        </tr>



                                    </thead>
                                    <tbody>

                                        <tr>
                                            <td align="left"><b><?= $column_merchant['merchant_name'] ?></b></td>
                                            <?php
                                            if(!$column_other_count){
                                                ?><td>&mdash;</td><?php
                                            }
                                            foreach ($column_merchant['other'] as $column_name => $column_value) {
                                                if ($column_value > 0) {
                                                    ?>
                                                    <td align="center">
                                                        <?php
                                                        if ($column_name == lang('DCPA') || $column_name == lang('Revenue') || $column_name == lang('Positions Rev. Share') || $column_name == lang('PNL')) {
                                                            echo $column_value . ' %';
                                                        } else {
                                                            echo $set->currency . ' ' . $column_value;
                                                        }
                                                        ?>

                                                    </td>    
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tr>

                                    </tbody>
                                </table>
                            </td>
                            <td valign="top">


                                <table class="normal" border="0" cellpadding="5" cellspacing="5">
                                    <thead>


                                        <tr>
                                            <td style="text-align: left;" width="200"><?= lang('Merchant') ?></td>
                                            <td><?= lang('Deposit Range'); ?></td>
                                            <td><?= lang('CPA'); ?></td>
                                            <td><?= lang('PCPA'); ?></td>
                                        </tr>



                                    </thead>
                                    <tbody>


                                        <?php
                                        if(empty($column_merchant['tier']) ){
                                            ?>
                                            <tr>
                                                <td align="left"><b><?= $column_merchant['merchant_name'] ?></b></td>
                                                <td>&mdash;</td>
                                                <td>&mdash;</td>
                                                <td>&mdash;</td>
                                            </tr>
                                            <?php
                                        }else{
                                            $column_tier_count = 0;
                                            foreach ($column_merchant['tier'] as $column_name => $column_value) {
                                                $tmp_amount = str_replace('$','',$column_value['amount']);
                                                $tmp_amount_pcpa = str_replace('$','',$column_value['tier_pcpa']);
                                                if((int)$tmp_amount == 0 && (int)$tmp_amount_pcpa == 0){
                                                    continue;
                                                }
                                                $column_tier_count++;
                                                ?>
                                                <tr>
                                                    <td align="left"><b><?= $column_merchant['merchant_name'] ?></b></td>
                                                    <td><?= $column_value['tier_amount']; ?></td>
                                                    <td><?= $column_value['amount']; ?></td>
                                                    <td><?= $column_value['tier_pcpa']; ?></td>
                                                </tr>
                                                <?php
                                            }
                                            if(!$column_tier_count){
                                                ?>
                                                <tr>
                                                    <td align="left"><b><?= $column_merchant['merchant_name'] ?></b></td>
                                                    <td>&mdash;</td>
                                                    <td>&mdash;</td>
                                                    <td>&mdash;</td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>


                                    </tbody>
                                </table>


                            </td>
                        </tr>
                    </table>
                    
                    <?php
                }
                ?>
            </div>
            <?php
        }

        $set->content .= ob_get_contents();
        ob_clean();

        /*
          if (!empty($listDealType) || !empty($tierDealType)) {

          $set->content .= '

						<div align="center">
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td style="text-align: left;">'.lang('Merchant').'</td>
									'.($showFields['tier'] && $tierDealType ? '
									<td>'.lang('Deposit Range').'</td>
									<td>'.lang('CPA').'</td>
									<td>'.lang('PCPA').'</td>
									' : ($showFields['minDep']  && $set->showMiminumDepositOnAffAccount ==1 ? '<td>'.lang(ptitle('Minimum Deposit')).'</td>' : '<td></td>').'
									'.($showFields['cpa'] ? '<td>'.lang('CPA').'</td>' : '<td></td>').'
									'.($showFields['dcpa'] && $set->showDCPAonAffiliateComStruc?  '<td>'.lang('DCPA').'</td>' : '<td></td>').'
									'. (allowView('af-ntrv',$deal,'fields')   && $showFields['rev']   ? '<td>'.lang('Revenue').'</td>' : '<td></td>').'
									'. (allowView('af-ntrv',$deal,'fields')  && $showFields['posrev']      ? '<td>'.lang('Positions Rev. Share').'</td>' : '<td></td>').'
									'.($showFields['pnl']   ? '<td>'.lang('PNL').'</td>' : '<td></td>').'
									'.($showFields['cpl']   ? '<td>'.lang('CPL').'</td>' : '<td></td>').'
									'.($showFields['cpc']   ? '<td>'.lang('CPC').'</td>' : '<td></td>').'
									'.($showFields['cpm'] &&  $set->deal_cpm ? '<td>'.lang('CPM').'</td>' : '<td></td>')).'
									'.($showFields['lots'] && $showForex==1  ? '<td>'.lang('Lots').'</td>' : '<td></td>').'
									'.($showFields['cpi'] ? '<td>'.lang('CPI').'</td>' : '<td></td>').'
								</tr></thead><tfoot>'.($tierDealType ? $tierDealType : $listDealType).'</tfoot>
							</table>
						</div>';
          }

         */
			
			$sql = "select * from products_items where valid >-1";
			$qqProducts = function_mysql_query($sql,__FILE__); 
			$productsCommissionArr = array();
			while($wwProducts = mysql_fetch_assoc( $qqProducts)){
				$sql = "select * from products_affiliates_deals where   product_id = " . $wwProducts['id'] . " and affiliate_id = " . $set->userInfo['id'] . " and valid = 1";
				$qqProductDeals = function_mysql_query($sql,__FILE__);
				$productsCommissionArr[$wwProducts['id']]['product_name'] = $wwProducts['title'];
				while($wwProductDeals = mysql_fetch_assoc($qqProductDeals)){
						if($wwProductDeals['dealType'] == "cpi")
							$productsCommissionArr[$wwProducts['id']]['cpi'] = $wwProductDeals['amount'];
						
						if($wwProductDeals['dealType'] == "cpc")
							$productsCommissionArr[$wwProducts['id']]['cpc'] = $wwProductDeals['amount']; 
						
						if($wwProductDeals['dealType'] == "cpllead")
							$productsCommissionArr[$wwProducts['id']]['cpllead'] = $wwProductDeals['amount']; 
							
						if($wwProductDeals['dealType'] == "cplaccount")
							$productsCommissionArr[$wwProducts['id']]['cplaccount'] = $wwProductDeals['amount']; 
						
						if($wwProductDeals['dealType'] == "cpa")
							$productsCommissionArr[$wwProducts['id']]['cpa'] = $wwProductDeals['amount']; 
				}
			}
			
			$listProductDeals = "";
			$l = 0;
			if(!empty($productsCommissionArr)){
				
				foreach($productsCommissionArr as $pKey=>$comm){
					$l++;
					$listProductDeals .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
					<td align=left>'. $comm['product_name'] .'</td>
					<td>'. $set->currency .' '. number_format($comm['cpi'],2)  .'</td>
					<td>'. $set->currency .' '. number_format($comm['cpc'],2)  .'</td>
					<td>'. $set->currency .' '. number_format($comm['cpllead'],2) .'</td>
					<td>'. $set->currency .' '. number_format($comm['cplaccount'],2)  .'</td>
					<td>'. $set->currency .' '. number_format($comm['cpa'],2)  .'</td>
					</tr>
					';
				}
			}
		
		if ($set->showProductsPlace==1)
			$set->content .= '<div class="normalTableTitle">'.lang('Products Commission Structure').'</div>
						<div align="center">
							<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
								<thead><tr>
									<td style="text-align: left;">'.lang('Product').'</td>
									<td>'.lang('Cost Per Installation').'</td>
									<td>'.lang('Cost Per Click').'</td>
									<td>'.lang('Cost Per Lead').'</td>
									<td>'.lang('Cost Per Account').'</td>
									<td>'.lang('Cost Per Sale').'</td>
								</tr></thead><tfoot>'. $listProductDeals .'</tfoot>
							</table>
						</div>';			
			
			theme();
		break;
	}
?>
