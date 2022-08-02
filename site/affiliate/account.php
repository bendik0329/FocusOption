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
		$pageTitle = lang('My Account - Account Details');
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
		<form action="'.$set->SSLprefix.$set->basepage.'?act=save" method="post" onsubmit="return checkUpdate();" autocomplete="off">
			<div class="account-details">
				<div class="ac-details">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="Account-tab" data-toggle="tab" href="#Account" role="tab" aria-controls="Account" aria-selected="true">Account</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="Contact-tab" data-toggle="tab" href="#Contact" role="tab" aria-controls="Contact" aria-selected="false">Contact</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="Invoice-tab" data-toggle="tab" href="#Invoice" role="tab" aria-controls="Invoice" aria-selected="false">Invoice</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="Website-tab" data-toggle="tab" href="#Website" role="tab" aria-controls="Website" aria-selected="false">Website</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="Market-tab" data-toggle="tab" href="#Market" role="tab" aria-controls="Market" aria-selected="false">Market</a>
						</li>
					</ul>
					
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="Account" role="tabpanel" aria-labelledby="Account-tab">
							<div class="row">
								<div class="col-lg-6">
									<div class="ac-details-tab">
										<div class="tab-input-acc">
											<label for="exampleInputname">Username</label>
											<input type="name" class="form-control required" id="exampleInputname" value="'.$db['username'].'" aria-describedby="emailHelp" disabled="disabled" placeholder="">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">Password</label>
											<input type="password" class="form-control" name="password" id="password" value="" aria-describedby="emailHelp" placeholder="">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">Repeat password</label>
											<input type="password" class="form-control" name="repassword" id="repassword" value="" aria-describedby="emailHelp" placeholder="">
										</div>
									</div>
									<div class="tac-check-acc">
										<div class="tab-acc-check-box">
											<input type="checkbox" class="form-check-input" id="exampleCheck1" name="newsletter" '.($db['newsletter'] ? 'checked="checked"' : '').'>
											<label class="form-check-label" for="exampleCheck1">'.lang('Yes, I would like to receive the Affiliate newsletter').'</label>
										</div>
										
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="Contact" role="tabpanel" aria-labelledby="Contact-tab">
							<div class="row">
								<div class="col-lg-6">
									<div class="ac-details-tab">
										<div class="tab-input-acc">
											<label for="exampleInputname">Company Name:</label>
											<input type="name" class="form-control" id="exampleInputname"  name="db[company]" value="'.$db['company'].'" aria-describedby="emailHelp" placeholder="">
										</div>
										<div class="tab-input-acc mb-0">
											<label for="exampleInputname">salutation:</label>
											</div>
										<div class="salutation">
											<div class="salutation-check-box">
												<input type="radio" name="db[gender]" id="one" value="male" '.($db['gender'] == "male" || !$db['gender'] ? 'checked="checked"' : '').'>
												<label for="one">Mr. </label>
											</div>
											<div class="salutation-check-box">
												<input type="radio" name="db[gender]" id="two" value="female" '.($db['gender'] == "female" ? 'checked="checked"' : '').'>
												<label for="two">Ms. </label>
											</div>
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">First Name:</label>
											<input type="text" class="form-control" id="exampleInputpassword" name="db[first_name]" value="'.$db['first_name'].'" aria-describedby="emailHelp" placeholder="">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">Last Name:</label>
											<input type="text" class="form-control" id="exampleInputpassword" name="db[last_name]" value="'.$db['last_name'].'" aria-describedby="emailHelp" placeholder="">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">E-mail:</label>
											<input type="Email" class="form-control" id="exampleInputpassword" aria-describedby="emailHelp" placeholder="" name="db[mail]" value="'.$db['mail'].'">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">Phone Number:</label>
											<input type="number" class="form-control" id="exampleInputpassword" aria-describedby="emailHelp" placeholder="" name="db[phone]" value="'.$db['phone'].'" >
										</div>
										<div class="tab-input-acc">
											<div class="form-group">
												<label for="exampleFormControlSelect1">Instant Messaging Type:</label>
												<select name="db[IMUserType]" class="form-control" id="exampleFormControlSelect1">
													<option value="">'.lang('Choose I.M. Type').'</option>
													<option value="Skype" '.($db['IMUserType'] == "Skype" ? 'selected="selected"' : '').'>'.lang('Skype').'</option>
													<option value="MSN" '.($db['IMUserType'] == "MSN" ? 'selected="selected"' : '').'>'.lang('MSN').'</option>
													<option value="Google Talk" '.($db['IMUserType'] == "Google Talk" ? 'selected="selected"' : '').'>'.lang('Google Talk').'</option>
													<option value="QQ" '.($db['IMUserType'] == "QQ" ? 'selected="selected"' : '').'>'.lang('QQ').'</option>
													<option value="ICQ" '.($db['IMUserType'] == "ICQ" ? 'selected="selected"' : '').'>'.lang('ICQ').'</option>
													<option value="Yahoo" '.($db['IMUserType'] == "Yahoo" ? 'selected="selected"' : '').'>'.lang('Yahoo').'</option>
													<option value="AIM" '.($db['IMUserType'] == "AIM" ? 'selected="selected"' : '').'>'.lang('AIM').'</option>
												</select>
											</div>
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">Instant Messaging Account</label>
											<input type="text" class="form-control" id="exampleInputpassword" name="db[IMUser]" value="'.$db['IMUser'].'" aria-describedby="emailHelp" placeholder="">
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="Invoice" role="tabpanel" aria-labelledby="Invoice-tab">
							<div class="row">
								<div class="col-lg-6">
									<div class="ac-details-tab">
										<div class="tab-input-acc">
											<label for="exampleInputname">Street:</label>
											<input type="name" class="form-control" id="exampleInputname" aria-describedby="emailHelp" placeholder="" name="db[street]" value="'.$db['street'].'">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">Postal / Zip Code:</label>
											<input type="text" class="form-control" id="exampleInputpassword" aria-describedby="emailHelp" placeholder="" name="db[postalCode]" value="'.$db['postalCode'].'">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">City:</label>
											<input type="text" class="form-control" id="exampleInputpassword" aria-describedby="emailHelp" placeholder="" name="db[city]" value="'.$db['city'].'">
										</div>
										<div class="tab-input-acc">
											<div class="form-group">
												<label for="exampleFormControlSelect1">Country</label>
												<select class="form-control" id="exampleFormControlSelect1" name="db[country]">
													<option value="">'.lang('Choose Your Country...').'</option>'.getCountry($db['country']).'
												</select>
											</div>
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="Website" role="tabpanel" aria-labelledby="Website-tab">
							<div class="row">
								<div class="col-lg-6">
									<div class="ac-details-tab">
										<div class="tab-input-acc">
											<label for="exampleInputname">Website 1:</label>
											<input type="name" class="form-control" id="exampleInputname" aria-describedby="emailHelp" placeholder="" name="db[website]" value="'.($db['website'] ? $db['website'] : 'http://').'">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">Website 2:</label>
											<input type="text" class="form-control" id="exampleInputpassword" name="db[website2]" value="'.($db['website2'] ? $db['website2'] : 'http://').'" aria-describedby="emailHelp" placeholder="">
										</div>
										<div class="tab-input-acc">
											<label for="exampleInputpassword">Website 3:</label>
											<input type="text" class="form-control" id="exampleInputpassword" name="db[website3]" value="'.($db['website3'] ? $db['website3'] : 'http://').'" aria-describedby="emailHelp" placeholder="">
										</div>		
									</div>
									
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="Market" role="tabpanel" aria-labelledby="Market-tab">
							<div class="row">
								<div class="col-lg-6">
									<div class="ac-details-tab">
										<div class="market-select">
											<div class="select-ul">
											<select id="q1" size="10" multiple="true">'.$unselectedItems.'</select>
											</div>
											<div class="arrow-button">
												<div class="a-button" id="right-button">
													<button id="btnRight"> <i class="fa fa-angle-right"></i> </button>
												</div>
												<div class="a-button" id="left-button">
													<button id="btnLeft"> <i class="fa fa-angle-left"></i> </button>
												</div>
											</div>
											<div class="select-ul">
												<select id="q2" name="db[q2]" multiple="true">'.$selectedItems.'</select>
											</div>
										</div>
									</div>
									
								</div>
							</div>
						</div>
						<div class="tac-check-acc">
							<div class="save-button">
								<input type="submit" class="mt-0" value="'.lang('Save').'" />
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		<script>
			$("#btnRight").click(function (e) {
			    var selectedOpts = $("#q1 option:selected");
			    if (selectedOpts.length == 0) {
			      alert("Nothing to move.");
			      e.preventDefault();
			    }
			    $("#q2").append($(selectedOpts).clone());
			    $(selectedOpts).remove();
			    e.preventDefault();
			  });

			  $("#btnLeft").click(function (e) {
			    var selectedOpts = $("#q2 option:selected");
			    if (selectedOpts.length == 0) {
			      alert("Nothing to move.");
			      e.preventDefault();
			    }
			    $("#q1").append($(selectedOpts).clone());
			    $(selectedOpts).remove();
			    e.preventDefault();
			  });

		</script>';
		
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

			// echo "<pre>";
			// print_r($commissionStructure);

            if (!empty($commissionStructure)) {
                foreach ($commissionStructure as $column_merchant) {
                    ?>
                    <!-- <table class="normal" width="90%" border="0" cellpadding="10" cellspacing="10" style="border-bottom:1px #999 solid;">
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
                    </table> -->
										
					<div class="account-table creative-page-filter ">
						<div class="top-performing-creative h-full com-page">
							<div class="search-wrp Commission-Structure-s">
								<p>Search creative</p>
								<div class="search-box">
									<input type="text" name="q" value="">
									<button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
								</div>
							</div>
							<div class="performing-creative-table">
								<div class="table-responsive">
									<table class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
										<thead>
											<tr>
											<th scope="col">#</th>
											<th scope="col"><?= lang('Merchant') ?></th>
											<th scope="col"><?= lang('PNL'); ?></th>
											<th scope="col"><?= lang('Deposit Range'); ?></th>
											<th scope="col"><?= lang('CPA'); ?></th>
											<th scope="col"><?= lang('PCPA'); ?></th>
											</tr>
										</thead>
										<tfoot class="topCreativesCls">
											<tr class="trLine">
												<td>1</td>
												<?php 
													if(empty($column_merchant['tier']) ){
														?>
															<td><?= $column_merchant['merchant_name'] ?></td>
														<?php
													}
												?>
												<td>
													<?php 
														print_r($column_merchant['other']['PNL']);
													?>
												</td>
												<td class="img-wrap">Passport</td>
												<td>
													<?php 
														print_r($column_merchant['other']['CPA']);
													?>
												</td>
												<td>
												<?php 
														print_r($column_merchant['other']['DCPA']);
													?>
												</td>
											</tr>
										</tfoot>
									</table>
								</div>
							</div>
						</div>	
					</div>

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
