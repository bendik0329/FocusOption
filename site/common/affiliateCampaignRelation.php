<?php
if (empty($userLevel))
	die('.');

if (empty($merchant_id))
$merchant_id= isset($_GET['merchant_id']) ? $_GET['merchant_id'] : (isset($_POST['merchant_id']) ?  $_POST['merchant_id'] : 0);

if (empty($merchant_id))
$merchant_id= isset($_GET['merchantid']) ? $_GET['merchantid'] : (isset($_POST['merchantid']) ?  $_POST['merchantid'] : 0);

if (empty($merchant_id)){
$merchant_id= isset($_GET['mid']) ? $_GET['mid'] : (isset($_POST['mid']) ?  $_POST['mid'] : 0);
}
// var_dump($_GET);
// die ('hey there3:  '.   $merchant_id);
if(!isset($merchant_id) || empty($merchant_id)){
	$mqq = function_mysql_query("SELECT id FROM merchants where valid=1",__FILE__);
					
	$mww = mysql_fetch_assoc($mqq);

	if (mysql_num_rows($mqq) <= 1) { 
		_goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $mww['id']);
	}
}

$r = mysql_fetch_assoc( function_mysql_query("select count(id) as cnt from merchants where valid = 1 and apiType='spot'	",__FILE__,__FUNCTION__));
$showSpotFeature =$r['cnt'];

	function getTag($tag, $endtag, $xml) {
		if (!$endtag) $endtag=$tag;
		preg_match_all("/".$tag."(.*?)".$endtag."/", $xml, $matches);
		return $matches[1][0];
		}

		$ignoreMerchantid = 0;
		$forceLoad = false;
		if (empty($merchant_id)) 
		$merchant_id= aesDec($_COOKIE['mid']);
		if (!empty($merchant_id)) { 
		$qq = "select (id) as id from merchants where id = " . $merchant_id;
		// die ($qq);
				$bb = function_mysql_query($qq,__FILE__,__FUNCTION__);
				$ww=mysql_fetch_assoc($bb);
			if ($merchant_id != $ww['id']) {
				$forceLoad= true;
			}
		}
			if ($merchant_id ==0  or $merchant_id =='' or $forceLoad) {
				$qq = "select min(id) as id from merchants limit 1";
				$bb = function_mysql_query($qq,__FILE__,__FUNCTION__);
				$ww=mysql_fetch_assoc($bb);
			$merchant_id = $ww['id'];
			}

	
// $merchant_id= isset($_GET['merchantid']) ? $_GET['merchantid'] : ( isset($_POST['merchantid']) ? $_POST['merchantid']  :1);	
if (empty($merchant_id))
		$merchant_id= isset($_GET['merchant_id']) ? $_GET['merchant_id'] : (isset($_POST['merchant_id']) ?  $_POST['merchant_id'] : 0);
		// die ('mer: ' . $merchant_id);	
	
	function setDefaultCampForAffiliates() {
			$merQry = "select  merchantid, affiliateID from affiliates_campaigns_relations where affiliateID>0 and merchantid >0 group by affiliateID,merchantid  ";
			$rsc = function_mysql_query($merQry,__FILE__,__FUNCTION__);
			while ($row  = mysql_fetch_assoc($rsc)) {
					$intQry = "select count(id) as cnt , affiliates_campaigns_relations.id , affiliates_campaigns_relations.merchantid , affiliates_campaigns_relations.affiliateID  from affiliates_campaigns_relations where isDefaultCamp=1 and merchantid = ".$row['merchantid']. "  and affiliateID= " .$row['affiliateID'] . "  ";
						$int_row  = mysql_fetch_assoc( function_mysql_query($intQry,__FILE__,__FUNCTION__));
							if ($int_row['cnt']==0 ) {
									$firstCamp = mysql_fetch_assoc(function_mysql_query("select id from affiliates_campaigns_relations where merchantid = ".$row['merchantid']. "  and affiliateID= ". $row['affiliateID'] . " limit 1",__FILE__,__FUNCTION__));
									$updateQry = "update  affiliates_campaigns_relations set isDefaultCamp=1 where id in ( ".$firstCamp['id']." );";
									function_mysql_query($updateQry,__FILE__,__FUNCTION__);
							}
			}
	}
			
			


if($_REQUEST['saveSingleCamp']){

	$act = 'saveSingleCamp';

}else if($_REQUEST['deletesingleCamp']){

	$act = 'deletesingleCamp';

}else if($_REQUEST['saveBulkCamp']){

	$act = 'saveBulkCamp';
	
}else if($_REQUEST['AutoRelateUnRelatedAffiliatesToFreeCamp']){

	$act = 'AutoRelateUnRelatedAffiliatesToFreeCamp';

	}else if($_REQUEST['importCampaignsFromSpot']){

	$act = 'importCampaignsFromSpot';


}else if($_REQUEST['forceParamsForTracker_sub']){

	$act = 'forceParamsForTracker_sub';

}

switch ($act) {

	case "updateProfile":
		//array(0) { } array(5) { ["act"]=> string(13) "updateProfile" ["mid"]=> string(1) "1" ["aid"]=> string(3) "841" ["cid"]=> string(2) "89" ["pid"]=> string(1) "3" }
		$merchant_id = $_GET['mid'];
		$campaign_id = $_GET['cid'];
		$profile_id = $_GET['pid'];
		$affiliate_id = $_GET['aid'];
		
		if ($merchant_id>0 && !empty($campaign_id) && $profile_id>0 && $affiliate_id>0) {
			$qry = "update affiliates_campaigns_relations set profile_id = " . $profile_id . " where affiliateID = " . $affiliate_id . " and campID = '" . $campaign_id . "' and merchantid = " . $merchant_id;
			// die ($qry);
			function_mysql_query($qry,__FILE__,__FUNCTION__);
		}
	break;
	
	case 'fixCampaigns':
		// die ('frefre');
		$sql = "update data_reg set affiliate_id = " . $aff_id . " where merchant_id = ".$merchant_id ." and affiliate_id=500 and campaign_id='" . $camp_id."'";
		function_mysql_query($sql);
		$sql = "update data_stats set affiliate_id = " . $aff_id . " where merchant_id = ".$merchant_id ." and affiliate_id=500 and campaign_id='" . $camp_id."'";
		function_mysql_query($sql);
		$sql = "update data_sales set affiliate_id = " . $aff_id . " where merchant_id = ".$merchant_id ." and affiliate_id=500 and campaign_id='" . $camp_id."'";
		function_mysql_query($sql);
		$sql = "update data_sales_pending set affiliate_id = " . $aff_id . " where merchant_id = ".$merchant_id ." and affiliate_id=500 and campaign_id='" . $camp_id."'";
		function_mysql_query($sql);
                $sql = "update ReportTraders set AffiliateID = " . $aff_id . " where MerchantID = ".$merchant_id ." and AffiliateID=500 and CampaignID='" . $camp_id."'";
		function_mysql_query($sql);
		
		// die('--');
		
		_goto($set->SSLprefix.$set->basepage);
		break;

	case "forceParamsForTracker_sub":
			 $qq  = 'update settings set forceParamsForTracker="' .$forceParamsForTracker .'"';
// die ($qq);	
	$bb = function_mysql_query($qq,__FILE__,__FUNCTION__);
		 _goto($set->SSLprefix.$set->basepage);
		break;	
		
	
	case "importCampaignsFromSpot":
		
		
$rsc = (function_mysql_query("select * from merchants where valid = 1",__FILE__,__FUNCTION__));		
while ( $ww = mysql_fetch_assoc($rsc)) {
	
		$qry = "select distinct campID as campid from affiliates_campaigns_relations where merchantid = " . $ww['id'];
		// die ($qry);
		$rsc = function_mysql_query($qry,__FILE__,__FUNCTION__);
		$camps = array();
		while ($row  = mysql_fetch_assoc($rsc)) {
			$camps[$row['campid']] = $row['campid'];
		}
		// var_dump($camps);
		// die();
		
		
		$api_url = $ww['APIurl'] ;
$api_user =  $ww['APIuser'];
$api_pass = $ww['APIpass'];
$api_label =  $ww['name'];
$api_whiteLabel =  $ww['name'];

		$url = $api_url.'?api_username='.$api_user.'&api_password='.$api_pass.'&';
		$foundrows = true;
		$page=0;
		while ($foundrows){
			$foundrows= false;
		$url .= ("MODULE=Campaign&COMMAND=view&page=".$page);

		$xml_report = doSpotPost($url);
	
			
		
		preg_match_all("/<data_[0-9]+>(.*?)<\/data_[0-9]+>/",$xml_report,$xml);
		
		foreach($xml[1] AS $xml_line) {
			$foundrows=true;
			// var_dump($xml_line);
			// echo '<br><Br>';
			$id = getTag('<id>','<\/id>',$xml_line);
			// die ('id : ' . $id);
			// $id = $xml_line['id'];
			
			if ($id>-1) {
				// $name = $xml_line['name'];
				$name = getTag('<name>','<\/name>',$xml_line);
				if (empty($name))
					$name = "-";
				if (in_array($id,$camps)) {
					// echo 'exists:     id: ' . $id . '<br>';
				}
				else { 
					// $qry = "insert into affiliates_campaigns_relations ('campID','merchantid') values ('" . $id . "','". mysql_real_escape_string($name) . "');";
					$qry = "INSERT INTO `affiliates_campaigns_relations` ( `campID`,`name`,  `affiliateID`, `profile_id`, `isDefaultCamp`, `merchantid`) VALUES ( '" . $id . "','". mysql_real_escape_string($name) . "', '0', '0', '0', ".$ww['id'].");";  

					
					 // echo ($qry). '<br>';
					function_mysql_query($qry,__FILE__,__FUNCTION__);
				
				}
			}
		}

		$page++;
		}
		
		// die ($A);		
}


	 _goto($set->SSLprefix.$set->basepage);
		break;	
	
	case "AutoRelateUnRelatedAffiliatesToFreeCamp":
		 
			
		// $qq  = 'select id,campID from affiliates_campaigns_relations where affiliateID = 0';
		 $qq  = 'select id,username from affiliates where id>500 and id not in (select affiliateID from affiliates_campaigns_relations where affiliateID > 0) and username in (select campID from affiliates_campaigns_relations where affiliateID= 0)';
		 $bb = function_mysql_query($qq,__FILE__,__FUNCTION__);
		while ($affiliateRow = mysql_fetch_assoc($bb)) {
				
				// $afQry  = "select id from affiliates where id>500 and username like '". $campIDs['campID'] . "'";
				// $UnrelatedAff = mysql_fetch_assoc (function_mysql_query($afQry,__FILE__));
				// $verifyFreeCampQry = 'select id,campID from affiliates_campaigns_relations where affiliateID =' . $UnrelatedAff['id'];
				// $verifyFreeCamp = mysql_fetch_assoc (function_mysql_query($verifyFreeCampQry,__FILE__));				
				// if ($verifyFreeCamp == null || empty($verifyFreeCamp['id'])) 
				{
						//$qry = "update affiliates_campaigns_relations set affiliateID = " . $UnrelatedAff['id'] . ' where id = ' . $campIDs['id'].';';
						$qry = "update affiliates_campaigns_relations set affiliateID = " . $affiliateRow['id'] . " where campID = '" . $affiliateRow['username']."';";
						
				function_mysql_query($qry,__FILE__,__FUNCTION__);	
				}
					
		}		
					
					
			 $qq  = 'select id,campID from affiliates_campaigns_relations where affiliateID = 0';
		 $bb = function_mysql_query($qq,__FILE__,__FUNCTION__);
		while ($campIDs = mysql_fetch_assoc($bb)) {		
		$afQry  = 'select id from affiliates where id>500 and id not in (select affiliateID from affiliates_campaigns_relations where affiliateID > 0) ';
					$resourceAff = function_mysql_query($afQry,__FILE__,__FUNCTION__);
					while ($UnrelatedAff = mysql_fetch_assoc($resourceAff)) {
						$qry = "update affiliates_campaigns_relations set affiliateID = " . $UnrelatedAff['id'] . ' where id = ' . $campIDs['id'].';';
						function_mysql_query($qry,__FILE__,__FUNCTION__);
						break;
					}
		}
		
		_goto($set->SSLprefix.$set->basepage);
			
	break;	
	

	
	case "deletesingleCamp":
		
		 //$qq  = 'Delete from affiliates_campaigns_relations WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' affiliateID=0 and campID = "' . $delcampID. '"';
		 $qq  = 'Delete from affiliates_campaigns_relations WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' affiliateID=0 and id = "' . $delcampID. '"';
		 // $qq  = 'Delete from affiliates_campaigns_relations WHERE  affiliateID=0 and campID = "' . $delcampID. '"';
		//die ($qq);
		$bb = function_mysql_query($qq,__FILE__,__FUNCTION__);
		setDefaultCampForAffiliates();
		
		_goto($set->SSLprefix.$set->basepage);
			
	break;
	
		case "saveBulkCamp":
		
			$prefix = $_POST['Prefix'];
			$from = $_POST['fromNumber'];
			$to = $_POST['toNumber'];

			if ($from>0 and $to>0 and $from<$to) {
				for ($i = $from; $i <= $to; $i++) {
					$campID = $prefix . $i;
					
					$qq  = 'select id from affiliates_campaigns_relations WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' campID = "' . $campID . '"';	
					$bb = function_mysql_query($qq,__FILE__,__FUNCTION__);
					
					$ww=mysql_fetch_assoc($bb);
					if ($ww['id']>0) { 
					
					} else {
					
							$qry = "INSERT INTO `affiliates_campaigns_relations` ( `campID`, `affiliateID`, `merchantid`) VALUES ( '". $campID . "', '0', '". $merchant_id ."');";
							
							$bb = function_mysql_query($qry,__FILE__,__FUNCTION__);
					}
					
				}
			}
			
	
		_goto($set->SSLprefix.$set->basepage);
			
	break;
	
	
	case "saveSingleCamp":
	
		//echo $ids.'<BR>';
		//echo $pixelCode[0];
		//die ('save');

		if ($campID <>'') { 
				
				$qq  = 'select id from affiliates_campaigns_relations WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = ' . $merchant_id .' and ' : '' ).' campID = "' . $campID . '"';	
				// die ($qq);
				$bb = function_mysql_query($qq,__FILE__,__FUNCTION__);
				
				$ww=mysql_fetch_assoc($bb);
				if ($ww['id']>0) { 
				} else {
						
						$qry = "INSERT INTO `affiliates_campaigns_relations` ( `campID`,`name`, `affiliateID`, `merchantid`) VALUES ( '". $campID . "','". $name . "', '0', '". $merchant_id ."');";
						// die ($qry);
						$bb = function_mysql_query($qry,__FILE__,__FUNCTION__);
				}
		}
		
		
		_goto($set->SSLprefix.$set->basepage.'?merchant_id=' . $merchant_id);
	
	break;

	case "make_default_campaign": 
		
		if ($_GET['mid']>0 &&  $_GET['affiliate_id']>0 && !empty($_GET['campaign'])){
			$qry = "update affiliates_campaigns_relations set isDefaultCamp = 0 where affiliateID = " . $_GET['affiliate_id'] . " and merchantid = " . $_GET['mid'] . " ;" ;
			function_mysql_query($qry,__FILE__,__FUNCTION__);
			$qry = "update affiliates_campaigns_relations set isDefaultCamp = 1 where affiliateID = " . $_GET['affiliate_id'] . " and merchantid = " . $_GET['mid'] . " and campID = " . $_GET['campaign'] . " ;" ;
			// die ($qry);
			
			function_mysql_query($qry,__FILE__,__FUNCTION__);
			
		}
	
		// var_dump($_GET);
		// die();
		
	
	_goto ($set->SSLprefix.$set->basepage);	
	break;
	

	default:
		//$set->pageTitle = lang('Affiliate Campaign Relation');
		$set->breadcrumb_title =  lang('Affiliate Campaign Relation');
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		
		
									
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.$userLevel.'/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.$userLevel.'/affiliateCampaignRelation.php" class="arrow-left">'.lang('Affiliate Campaign Relation').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		
			
			
			
		/////////////////////////////////////////////// GET FREE CAMPS:
		if(isset($_REQUEST['getFreeCamps'])){
			//$merchant_id= aesDec($_COOKIE['mid']);
			$totalCampaigns = mysql_result(function_mysql_query('SELECT count(id) FROM affiliates_campaigns_relations WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' affiliateID=0',__FILE__,__FUNCTION__),0,0);
			echo $totalCampaigns;
			die();
			
		}
		
			

		//MAINTENANCE
	
		
		
		////////////////////////////////////////////// STANDARD FLOW:
		
	$qry = 	'SELECT merchants.name as merchant_name ,  affiliates_campaigns_relations.merchantid as merchant_id ,affiliates_campaigns_relations.profile_id,affiliates_campaigns_relations.isDefaultCamp, affiliates_campaigns_relations.id, affiliates_campaigns_relations.name as name , affiliates_campaigns_relations.affiliateID AS affID, affiliates.username,affiliates_campaigns_relations.campID AS campID 
				FROM affiliates_campaigns_relations INNER JOIN affiliates ON affiliates_campaigns_relations.affiliateID=affiliates.id inner join merchants where merchants.id = affiliates_campaigns_relations.merchantid
				' .(" and affiliates_campaigns_relations.merchantid= " . $merchant_id) .  '
				ORDER BY CAST(`affiliates_campaigns_relations`.`affiliateID` as SIGNED INTEGER)  DESC';
				// ORDER BY CAST(`affiliates_campaigns_relations`.`campID` as SIGNED INTEGER)  DESC';
		// die ($qry);
		$relationsQ = function_mysql_query($qry,__FILE__,__FUNCTION__);
		
		$relationsStr = '';
		$l=0;
		
		$lastRowAffiliateID= 0;
		$style = "";
		
		
		
		
		$affiliatesAndProfilesArray = array();
		$counter=0;
		$profilesOptionText = "";
		if(!empty($relationsQ)){
		while($row=mysql_fetch_assoc($relationsQ)){
			$l++;
			
			/* <td style="width:10%">'.lang('Affilaite ID').'</td>
					<td style="width:20%">'.lang('Affilaite Username').'</td>
					<td style="width:5%">'.lang('Profile').'</td>
					<td style="width:15%">'.($campID['title'] ? ucwords($campID['title']) : lang('Campaign ID')).'</td>
					<td style="width:20%">'.(lang('Campaign Name')).'</td>
					<td style="width:10%">'.(lang('Is Default Campaign')).'</td>
					<td style="width:15%">'.lang('Action').'</td> */
			// $isDefaultCampTest = '<button onclick="window.location.href='/page2'">'.lang('Set As Default').'</button> ';
			if ($row['isDefaultCamp']==1) 
			$isDefaultCampTest = '<span style="font-weight:bold" >'.lang('Default for this Affiliate').'</span>';
		else
			$isDefaultCampTest = '<button onclick="window.location.href=\''.$userLevel.'/affiliateCampaignRelation.php?act=make_default_campaign&mid='.$row['merchant_id'].'&affiliate_id='.$row['affID'].'&campaign='.$row['campID'].'\'">'.lang('Set As Default').'</button> ';
	
/* 
echo 'lastrowid ' . $lastRowAffiliateID . '<br>';	
echo 'affID ' . $row['affID'] . '<br><br>';
 */
	
		if (!$affiliatesAndProfilesArray[$row['affID']]>0) {
			
		$qq = "SELECT * FROM affiliates_profiles s where affiliate_id = " . $row['affID'] . " and valid=1;";
		// echo $qq .'<br>';
		$prfRsc = function_mysql_query($qq,__FILE__,__FUNCTION__);
			while ($prflRslt = mysql_fetch_assoc($prfRsc)) {
			// var_dump($prfRslt);
			// echo '<br>';
			$affiliatesAndProfilesArray[$row['affID']][$counter]['id'] =  $prflRslt['id'];
			$affiliatesAndProfilesArray[$row['affID']][$counter]['name'] =  $prflRslt['name'];
			$counter++;
			}
			unset ($prfRsc);
		}		
	// var_dump($affiliatesAndProfilesArray);
	// die();
	
		$profilesArray = $affiliatesAndProfilesArray[$row['affID']];
		// var_dump($profilesArray);
		// echo '<br>';
		$profilesOptionText = '';
		
		if (!empty($profilesArray))
		foreach	 ($affiliatesAndProfilesArray[$row['affID']] as $profilesArray) {
			// echo '1<br>';
			if (empty($profilesOptionText))
				$profilesOptionText = '<option id="-1">' . lang('Default'). '</option>';
			$profilesOptionText .='<option id="'.$profilesArray['id'].'" ' .($profilesArray['id']==$row['profile_id'] ? " selected " : "" ). ' >' . $profilesArray['name'] . '</option>';
		}

		if ($lastRowAffiliateID==0 || $lastRowAffiliateID == $row['affID']) {
			$lastRowAffiliateID = $row['affID'];
			$style="";
			$showBorder = false;
		}
		else //	if ($lastRowAffiliateID == $row['affID']) 
		{
			
			$style= ' style="border-top:1px solid black;" ';
			$showBorder= true;
				$lastRowAffiliateID = $row['affID'];
		}
			
		
			
			$relationsStr.= '<tr ' . ' '  .($showBorder ? " class=\"showBorder\" " : '').' ' .($l % 2 ? 'class="trLine"' : '').' rel="'.$row['id'].'">
								<td style="text-align:center;width:10%; padding-top:8px; padding-bottom:8px"><a href="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=new&id='.$row['affID'].'" target="_blank">'.$row['affID'].'</a></td>
								<td style="text-align:center;width:20%;" class="affs" rel="'.$row['affID'].'"><a href="'.$set->SSLprefix.$userLevel.'/affiliates.php?act=new&id='.$row['affID'].'" target="_blank">'.$row['username'].'</a></td>
								
								'. (empty($profilesOptionText)? '<td style="text-align:center;width:5%;" class="profile_id" rel="'.$row['profile_id'].'">'.(!$row['profile_id'] ? lang("Default") : "" ) .'</td>' : 
								'<td style="text-align:center;width:5%;" class="profile_id" rel="'.$row['profile_id'].'"><select onchange="updateProfileID(this)" style="width:132px;">'. $profilesOptionText .'</select></td>' ) . '
								
								<td style="text-align:center;width:15%;" class="merchant_name" rel="'.$row['merchant_name'].'">'.$row['merchant_name'].'</td>
								<td style="text-align:center;width:15%;" class="camps" rel="'.$row['campID'].'">'.$row['campID'].'</td>
								<td style="text-align:center;width:20%;" class="campname" rel="'.$row['name'].'">'.$row['name'].'</td>
								<td style="text-align:center;width:10%;" class="isDefaultCamp" rel="'.$row['isDefaultCamp'].'">'.$isDefaultCampTest.'</td>
								<td class="deleteCA" style="text-align:center;width:25%;"><span style="cursor:pointer; cursor:hand">'.lang('Delete').'</span>
									<input type="hidden" name="affiliate_id1" value="'.$row['affID'].'"/>
									<input type="hidden" name="campaign_id" value="'.$row['campID'].'"/>
									<input type="hidden" name="merchant_id" value="'.$row['merchant_id'].'"/>
								</td>
							</tr>';
		}
		}
		
		////////////////////////////////////////////// END OF STANDARD FLOW
		
		
		
		////////////////////////////////////////////// AFFILIATES [FLOW,EDIT,ADD]
		//$merchant_id= aesDec($_COOKIE['mid']);
		
		$qry = 'SELECT af.id,af.username FROM affiliates af where 1=1  
		'. ($set->AllowAffiliateDuplicationOnCampaignRelation==1 ? ' ' : ' and af.id not in (select ac.affiliateID from affiliates_campaigns_relations ac WHERE 1=1 ' . 
		($ignoreMerchantid==0 ? ' and merchantid = "' . $merchant_id .'"  ' : '' ).  ' and ac.affiliateID!=0  '.(isset($_REQUEST['affID']) ? ' ac.affiliateID!='.$_REQUEST['affID'] : '')
		.') ' ).  ' and  af.valid=1 order by lower(af.username) ASC';
		
		
		
		// die ($qry);
		
		$affiliatesQ = function_mysql_query($qry,__FILE__,__FUNCTION__);
		
		$affiliatesStr='';
		while($row=mysql_fetch_assoc($affiliatesQ)){
			//$affiliatesStr.= '<option value='.$row['id'].' '.((isset($_REQUEST['affID']) AND $_REQUEST['affID']==$row['id']) ? 'selected' : '').'>'.$row['username'].' -  ' .$row['id']. '</option>';
			$affiliatesStr.= '<option value='.$row['id'].' '.((isset($_REQUEST['affID']) AND $_REQUEST['affID']==$row['id']) ? 'selected' : '').'>['.$row['id'].'] '
                          .  $row['username'] .'</option>';
			if(isset($_REQUEST['affID']) AND $_REQUEST['affID']==$row['id']){
				$defaultAff = $row['username'];
			}
		}
		
		if(isset($_REQUEST['updateAff']) AND isset($_REQUEST['val'])){
			//echo 'UPDATE affiliates_campaigns_relations SET affiliateID='.$_REQUEST['val'].' WHERE id='.$_REQUEST['updateAff'];
			function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID='.$_REQUEST['val'].' WHERE id='.$_REQUEST['updateAff'],__FILE__,__FUNCTION__);
			$row = mysql_fetch_assoc(function_mysql_query('SELECT username FROM affiliates WHERE id='.$_REQUEST['val'],__FILE__,__FUNCTION__));
			die($row['username']);
		}
		
		if(isset($_REQUEST['ajaxAff'])){
			
			echo '<select class="affSelect" name="affID"><option id=-1>'.lang('Select Affiliate').'</option>'.$affiliatesStr.'</select><input class="affSave" type="button" value="'.lang('Save').'"/><input type="button" class="affCancel" value="'.lang('Cancel').'"/>
			<script type="text/javascript">
				$( document ).ready(function() {
					
					
					$(".affCancel").click(function(){
						$(this).parent().html("'.$defaultAff.'");
					});
					$(".affSave").click(function(){
						//$(this).parent().html($(this).parent().find(".affSelect").val());
						var td = this;
						$.ajax({
							url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&updateAff='.$_REQUEST['rid'].'&val="+$(this).parent().find(".affSelect").val(),
							dataType: "HTML",
						}).done(function(response) {
							//console.log(response);
							var id = $(td).parent().find(".affSelect").val();
							$(td).parent().attr("rel",id);
							$(td).parent().html(response);
							getTotalFreeCamps();
						});
					});
				});
				
			</script>
			';
			die();
		}
		
		
		////////////////////////////////////////////// END OF AFFILIATES
		
		
		
		////////////////////////////////////////////// CAMPAIGNS [FLOW,EDIT,ADD]
		
		
		$lqry = 'SELECT * FROM affiliates_campaigns_relations WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' affiliateID=0 '.(isset($_REQUEST['campID']) ? ' OR campID="'.$_REQUEST['campID'].'"' : '').
		'ORDER BY CAST(`affiliates_campaigns_relations`.`campID` as SIGNED INTEGER)  DESC';
		// ' ORDER BY lower(campID) ASC';
		
		$campaignsQ = function_mysql_query($lqry,__FILE__,__FUNCTION__) OR die(mysql_error());
		//die('SELECT * FROM affiliates_campaigns_relations WHERE  merchantid = "' . $merchant_id .'" and affiliateID=0 '.(isset($_REQUEST['campID']) ? ' OR campID='.$_REQUEST['campID'] : '').' ORDER BY campID ASC');
		$campaignsStr = '';
		while($row=mysql_fetch_assoc($campaignsQ)){
			$campaignsStr.= '<option value='.$row['campID'].' '.((isset($_REQUEST['campID']) AND $_REQUEST['campID']==$row['campID']) ? 'selected' : '').'>'.$row['campID'].' - ' .$row['name'] .'</option>';
		}
		
		
		if(isset($_REQUEST['updateCamp']) AND isset($_REQUEST['val'])){
			//echo 'UPDATE affiliates_campaigns_relations SET affiliateID='.$_REQUEST['val'].' WHERE id='.$_REQUEST['updateAff'];
			$currAff = mysql_fetch_assoc(function_mysql_query('SELECT affiliateID FROM affiliates_campaigns_relations WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' id='.$_REQUEST['updateCamp'],__FILE__,__FUNCTION__));
			if(!$currAff['affiliateID']){
				//die('An error occured, no affiliate was found.');
				$currAff['affiliateID'] = -1;
			}else{
				function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID=0 WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' id='.$_REQUEST['updateCamp'],__FILE__,__FUNCTION__);
			}
			function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID='.$currAff['affiliateID'].' WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' affiliateID=0 AND campID='.$_REQUEST['val'],__FILE__,__FUNCTION__); //OR die(mysql_error());
			$currID = mysql_result(function_mysql_query('SELECT id FROM affiliates_campaigns_relations WHERE campID='.$_REQUEST['val'],__FILE__,__FUNCTION__),0,0);
			die($_REQUEST['val'].'|'.$currID);
		}
		
		if(isset($_REQUEST['ajaxCamp'])){
			echo '<select name="affID" class="campSelect"><option id=-1>'.lang('Select Campaign').'</option>'.$campaignsStr.'</select><input class="campSave" type="button" value="'.lang('Save').'"/><input class="campCancel" type="button" value="'.lang('Cancel').'"/>
			<script type="text/javascript">
				$( document ).ready(function() {
					$(".campCancel").click(function(){
						$(this).parent().html($(this).parent().find(".campSelect").val());
					});
					$(".campSave").click(function(){
						var td = this;
						$.ajax({
							url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&updateCamp='.$_REQUEST['rid'].'&val="+$(this).parent().find(".campSelect").val(),
							dataType: "HTML",
						}).done(function(response) {
							var id = $(td).parent().find(".campSelect").val();
							response = response.split("|");
							$(td).parent().attr("rel",$(td).parent().find(".campSelect").val());
							$(td).parent().parent().attr("rel",response[1]);
							$(td).parent().html(response[0]);
							getTotalFreeCamps();
						});
					});
				});
			</script>
			';
			die();
		}
		
		////////////////////////////////////////////// END OF CAMPAIGNS


		////////////////////////////////////////////// DELETE BTN ACTION

		if(isset($_REQUEST['deleteCamp']) AND isset($_REQUEST['rid'])){
			$sql = 'UPDATE affiliates_campaigns_relations SET affiliateID=0 WHERE ' . ($ignoreMerchantid==0 ? ' merchantid = "' . $merchant_id .'" and ' : '' ).' id='.$_REQUEST['rid'];
			//$sql = 'UPDATE affiliates_campaigns_relations SET affiliateID=0 WHERE  id='.$_REQUEST['rid'];
			function_mysql_query($sql,__FILE__,__FUNCTION__);
			die();
		}
		
		////////////////////////////////////////////// END OF DELETE



		////////////////////////////////////////////// ADD NEW ACTION 



		if(isset($_REQUEST['newRel']) AND isset($_REQUEST['aff']) AND isset($_REQUEST['camp'])){
			$affID = retrieveAffiliateId($_REQUEST['aff']);
			function_mysql_query('UPDATE affiliates_campaigns_relations SET affiliateID="'.$affID.'" WHERE campID="'.$_REQUEST['camp'].'"',__FILE__,__FUNCTION__);
			$row = mysql_fetch_assoc(function_mysql_query('SELECT * FROM affiliates_campaigns_relations WHERE campID="'.$_REQUEST['camp'].'"',__FILE__,__FUNCTION__));
			$affName = mysql_fetch_assoc(function_mysql_query('SELECT username FROM affiliates WHERE id='.$affID,__FILE__,__FUNCTION__));
			echo $_REQUEST['aff'].'|'.$affName['username'].'|'.$_REQUEST['camp'].'|'.$row['id'].'|'.$row['affiliateID'].'|'.$row['campID'];
			die();
		}

		
		if(isset($_REQUEST['addNew'])){
			echo '<tr '.($l % 2 ? 'class="trLine"' : '').' rel="rid">
						<td class="affID" style="text-align:center; padding-top:8px; padding-bottom:8px">-</td>
						<td style="text-align:center" class="affs" rel="affID">
						<div class="ui-widget" style="margin-left:-17px;text-align:left;">'
								. '<!-- name="affiliate_id" -->'
								. '<select name="affID" class="affSelect" id="combobox">'
								. '<option id=-1>'.lang('Select Affiliate').'</option>' 
								. $affiliatesStr
								.'</select>
							</div>
							</td>
						<td></td>
						<td></td>
						<td style="text-align:center" class="camps" rel="campID"><select name="campID" class="campSelect"><option id=-1>'.lang('Select Campaign').'</option>'.$campaignsStr.'</select>&nbsp;&nbsp;<input class="newSave" type="button" value="'.lang('Save').'"/></td>
						<td style="text-align:center"></td>
						<td></td>
						<td class="deleteCA" style="    text-align: center;"><span style="cursor:pointer; cursor:hand">'.lang('Delete').'</span></td>
					</tr>
					<!-- jQuery UI Autocomplete css -->
                                                                        <style>
                                                                        .custom-combobox {
                                                                            position: relative;
                                                                            display: inline-block;
                                                                          }
                                                                          .custom-combobox-toggle {
                                                                            position: absolute;
                                                                            top: 0;
                                                                            bottom: 0;
                                                                            margin-left: -1px;
                                                                            background: white;
                                                                            border-radius: inherit;
                                                                            border-color: #CECECE;
                                                                            border-left: 0;
                                                                            color: #1F0000;
                                                                          } 
                                                                          .custom-combobox-input {
                                                                            margin: 0;
                                                                            padding: 5px 10px;
                                                                            width: 174px;
                                                                            background: white;
                                                                            border-radius: inherit;
                                                                            border-color: #CECECE;
                                                                            color: #1F0000;
                                                                            font-weight: inherit;
                                                                            font-size: inherit;
                                                                          }
                                                                          .ui-autocomplete { 
                                                                            height: 200px; 
                                                                            width:  310px;
                                                                            overflow-y: scroll; 
                                                                            overflow-x: hidden;
                                                                          }
                                                                        </style>
																		<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
					<script type="text/javascript">

						$( document ).ready(function() {
							
					
						
						
						
							$(".newSave").click(function(){
								var td = this;
								var td2 = $(this).parent().parent();
								$.ajax({
									url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&newRel=1&aff="+$("input[name=affiliate_id").val()+"&camp="+$(".campSelect").val()+"&merchant_id='.$merchant_id.'",
									dataType: "HTML",
								}).done(function(response) {
									var id = $(td).parent().find(".campSelect").val();
									response = response.split("|");
									$(td).parent().html(response[2]);
									$(td).parent().attr("rel",response[5]);
									$(td).parent().parent().find(".affs").attr("rel",response[4]);
									$(td).parent().parent().attr("rel",response[3]);
									
									td2.find(".affs").attr("rel",response[4]);
									td2.find(".camps").attr("rel",response[5]);
									td2.attr("rel",response[3]);
									
									td2.find(".affs").html(response[1]);
									td2.find(".affID").html(response[0]);
									
									$(".addNew").slideDown();
									//getTotalFreeCamps();
									var currCampTotal = Number($("#totalFreeCamps").html());
									currCampTotal--;
									$("#totalFreeCamps").html(currCampTotal);
									
									return;
									$(td).parent().attr("rel",$(td).parent().find(".campSelect").val());
									$(td).parent().parent().attr("rel",response[1]);
									$(td).parent().html(response[0]);
									$(td).parent().html(response[0]);
									
								});
							});
						});
					</script>
					
					';
			$l++;
			
			setDefaultCampForAffiliates();
			die();
		}
		
		
		////////////////////////////////////////////// END OF ADD NEW




		////////////////////////////////////////////// FLOW
		if (empty($merchant_id))
		$merchant_id = aesDec($_COOKIE['mid']);
	
	
		$campID = mysql_fetch_assoc(function_mysql_query('SELECT extraMemberParamName AS title FROM merchants WHERE id='.$merchant_id,__FILE__,__FUNCTION__));
		$merchantRow ="";
		if(isset($merchant_id) && !empty($merchant_id))
		$merchantRow = mysql_fetch_assoc(function_mysql_query('SELECT * from  merchants WHERE id=' . $merchant_id,__FILE__,__FUNCTION__));
	
		
		$set->content .=  '<style>
		
		.showBorder td {
			 border-top:1pt solid black;
		}
		
		
		</style>';
		
		
		
			$set->content .=  "
			<script type='text/javascript' src='".$set->SSLprefix."js/impromptu/dist/jquery-impromptu.min.js'></script>
			<link rel='stylesheet' href='".$set->SSLprefix."js/impromptu/dist/jquery-impromptu.min.css'/>
			<script>
function updateProfileID (el) {
	// debugger;
	el = $(el);
	var row = el.parents('tr:first');
	
	 $.get( 
		'".$set->SSLprefix.$userLevel."/affiliateCampaignRelation.php?act=updateProfile'
		+'&mid='+row.find('input[name=\"merchant_id\"]').val()
		+'&aid='+row.find('input[name=\"affiliate_id1\"]').val()
		+'&cid='+row.find('input[name=\"campaign_id\"]').val()
		+'&pid='+el.find(':selected').attr('id')
		+'&id='+row.attr('rel')
	).done(function(){
		$.prompt('".lang('The Profile for this affiliate has been saved.')."', {
			top:200,
			title: '".lang('Profile Saved')."',
			buttons: { '".lang('OK')."': true}
		});
	});
}
</script>";

if(!empty($_GET['merchant_id'])){
	$wwM = mysql_fetch_assoc(function_mysql_query("select name from merchants where id = " . $_GET['merchant_id'],__FILE__,__FUNCTION__));
}

		$set->content .= '
			
			<div class="normalTableTitle" style="margin-top:30px">'.lang('Affiliates Campaigns Relations')." : " .  (!empty($wwM)?$wwM['name']:'') .' <span style="font-size:14px"> - </span><span style="font-size:14px">'. lang('Total Free Campaigns').' : <span id="totalFreeCamps"></span></span></div>
			<form id="campaignsForm" action="'.$set->SSLprefix.$set->basepage.'" method="post">
			<!--h2>'.lang('Campaigns Management').'</h2--><br>';
			
			
			$a = 				  '<div align="left"><script>var hash = "";</script><div align="left" style="font-size:16px;"><b>'.lang('Choose merchant').':</b><br /><br /><select onchange="location.href=\''  . $set->basepage . '?merchant_id=\' + this.value">

<option value="">' 	  . lang('Choose Merchant') . '</option>' . listMerchants($_GET['merchant_id']) . '</select></div></div><br><br><br>';
						  
						  
						  
						  // die ($a);
			// die ('mer: ' . $merchant_id);
			$set->content .=$a;
			
			if(isset($_GET['merchant_id']) && $_GET['merchant_id'] !=""){
			$set->content .= '<td align="left" style="font-weight:bold;">'.lang('Add New').'</td><br>
			<td>'.lang('Campaign ID').':<input  type="text" name="campID"  id="campID"  style="width: 150px;" /></td>
			<input  type="hidden" name="merchant_id"  value="'.$merchant_id.'" />
			<td>'.lang('Name').':<input  type="text" name="name"  style="width: 150px;" /></td>
			<input name="saveSingleCamp" type="submit" value="'.lang('Save').'" />';
			
			if(isset($merchant_id) && !empty($merchant_id) && (strtolower($merchantRow['producttype'])== 'sportbet' ||  strtolower($merchantRow['producttype']) == 'casino') && strtolower($merchantRow['apiType']) == 'spot'){
				$set->content .='
				<br><br><br><br>
				<td align="left">'.lang('Add New (BULK)').':</td><br>
				<td align="left">( '.lang('Example').' ABC001 - ABC010 )</td><br><br>
				<td>'.lang('Prefix').':<input  type="text" name="Prefix"  style="width: 100px;" /></td>
				<td>&nbsp;&nbsp;&nbsp;'.lang('From').':&nbsp;<input  type="text" name="fromNumber" 	 style="width: 50px;" /></td>
				<td>&nbsp;&nbsp;&nbsp;'.lang('To').':&nbsp;<input  type="text" name="toNumber"  style="width: 50px;" /></td>
				
				<input name="saveBulkCamp" type="submit" value="'.lang('Save Bulk').'" />';
			}
			$set->content .= '
			<br/><br/><br><br>
			<td align="left">'.lang('Delete free campaign').':</td></tr><tr>';
		
			
			//$merchant_id= aesDec($_COOKIE['mid']);


						  

						  
			$q = 'SELECT * FROM affiliates_campaigns_relations WHERE  merchantid = "' . $merchant_id .'" and affiliateID=0';
			
			// die ($q);
			$qq = (function_mysql_query($q,__FILE__,__FUNCTION__));
			$cnt = 0;
						  
			$set->content.='<select name="delcampID"><option id=-1>'.lang('Choose a free campaign').'</option>';
			while ($ww=mysql_fetch_assoc($qq)) {
			$cnt++;
			$set->content.='<option id='.$cnt.' value='. $ww['id'] .'>'.$ww['campID'].' - '.$ww['name'].'</option>';
			}
			
			
			$set->content.='
			</select>
			<input type="submit" name="deletesingleCamp" value="'.lang('Delete').'"/>';
			
			if( (strtolower($merchantRow['producttype'])== 'sportbet' ||  strtolower($merchantRow['producttype']) == 'casino')){
				$set->content .= '
				<br>
				<br><br><br>
				<td align="left">'.lang('Auto Relate All UnRelated Affiliates To Free Campaigns').':</td>
				
				<input type="submit" name="AutoRelateUnRelatedAffiliatesToFreeCamp" value="'.lang('Run').'"/></td>
				<br><br/>';
				
			}
			if(isset($merchant_id) && !empty($merchant_id) && strtolower($merchantRow['apiType']) == 'spot' && (strtolower($merchantRow['producttype'])!= 'sportbet' &&  strtolower($merchantRow['producttype']) != 'casino')){
				$set->content .= '
				' . ($showSpotFeature ? '
				<br><td align="left">'.lang('Import Campaigns from SpotOptions').':</td>
				<input type="submit" name="importCampaignsFromSpot" value="'.lang('Run').'"/></td>' : '');
			}
			$set->content .= '</form>
		<br><br><br><br/>
			
			<div id="tab-container2" class="tab-container">
						 <ul class="etabs">
							<li class="tab"><a href="#tabs1-manuallyrelate">'. lang('Manually Relate') .'</a></li>
							<li class="tab"><a id="maintenanceTab" href="#tabs1-maintenance">'. lang('Maintenance') .'</a></li>
						 </ul>
						 <div class="panel-container">
							<div id="tabs1-manuallyrelate">
			
			<h3>'.lang('Manually Relate').'</h3>
			<div style="font-size:12px; padding:5px">'.lang('Please click on affiliate Username OR Campaign ID to edit the values').'.</div>
			<table id="caTable" cellspacing=0 cellpadding=3 border=0 width="100%" class="normal" >
				<thead>
				<tr>
					<td style="width:10%">'.lang('Affilaite ID').'</td>
					<td style="width:15%">'.lang('Affilaite Username').'</td>
					<td style="width:5%">'.lang('Profile').'</td>
					<td style="width:15%">'.($campID['title'] ? ucwords($campID['title']) : lang('Merchant')).'</td>
					<td style="width:10%">'.($campID['title'] ? ucwords($campID['title']) : lang('Campaign ID')).'</td>
					<td style="width:15%">'.(lang('Campaign Name')).'</td>
					<td style="width:10%">'.(lang('Is Default Campaign')).'</td>
					<td style="width:15%">'.lang('Action').'</td>
				</tr>
				</thead>
				'.$relationsStr.'
				
			</table>
			<div class="addNew" style="padding:8px; background:LIGHTGREY; color:#000; text-align:center; font-weight:bold; text-decoration:underline; cursor:hand; cursor:pointer">'.lang('Add new').'</div></tfoot>
			
			</div>
			<div id="tabs1-maintenance">
				
					<table id="caTable" cellspacing=0 cellpadding=3 border=0 width="100%" class="normal" >
						<thead>
						<tr>
							<td style="width:10%">'.lang('Campaign ID').'</td>
							<td style="width:10%">'.lang('Affilaite ID').'</td>
							<td style="width:15%">'.lang('Affilaite Username').'</td>
							<td style="width:5%">'.lang('Profile').'</td>
							<td style="width:15%">'.lang('Mismatch Records').'</td>
							<td style="width:15%">'.lang('Action').'</td>
						</tr>
						</thead>
						<tbody id="nextTimeDoItRight">
						
						</tbody>
					</table>
				
			</div>
			</div>
			</div>
			<script type="text/javascript">
				$( document ).ready(function() {
						
						$("#campID").on("keypress",function(event){
							data = $(this).val();
							len = data.length -1;
							if(len <= 13 && event.key != "=" && event.key != "?" && event.key !="/" && event.key != ":"){
								return true;
							}
							else
								return false;
							
						});
					
							$.ajax({
							url: "'.$set->SSLprefix.'ajax/getCampaignMaintenance.php?merchant_id='.$merchant_id.'",
							dataType: "HTML",
						}).done(function(response) {
							$("#nextTimeDoItRight").html(response);
							//console.log(response);
						});
						
						
					$(document).delegate(".btnFix", "click",function(){
					var affId = $(this).data("updateaffid");
					var campId = $(this).data("updatecampid");
					if(affId == 0)
						return;
					
						$.ajax({
							url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?act=fixCampaigns&merchant_id='.$merchant_id.'&camp_id="+ campId + "&aff_id="+affId
						}).done(function(response) {
							$("#totalFreeCamps").html(response);
						});
					
					});
					$("#tab-container2").easytabs();
					function getTotalFreeCamps(){
						$.ajax({
							url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&getFreeCamps=1",
							dataType: "HTML",
						}).done(function(response) {
							$("#totalFreeCamps").html(response);
						});
					}

					function handleAffs(e){
						//console.log($(this).attr("rel"));
						var td = this;
						$.ajax({
							url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&ajaxAff=1&affID="+$(this).attr("rel")+"&rid="+$(this).parent().attr("rel"),
							dataType: "HTML",
						}).done(function(response) {
							if(response==""){
								alert("'.lang('Error occurred. Please contact support').'");
								return;
							}
							//console.log(response);
							$(td).html(response);
							getTotalFreeCamps();
						});
					}

					function handleCamps(e){
						//console.log($(this).attr("rel"));
						var td = this;
						$.ajax({
							url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&ajaxCamp=1&campID="+$(this).attr("rel")+"&rid="+$(this).parent().attr("rel"),
							dataType: "HTML",
						}).done(function(response) {
							if(response==""){
								alert("'.lang('Error occurred. Please contact support').'");
								return;
							}
							//console.log(response);
							$(td).html(response);
							getTotalFreeCamps();
						});
					}

					function deleteCA(e){
						if(!confirm("'.lang('Are you sure you want to delete this Campaign-Affiliate relation?').'")){
							return;
						}
						var td = this;
						
						$.ajax({
							url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&deleteCamp=1&rid="+$(this).parent().attr("rel"),
							dataType: "HTML",
						}).done(function(response) {
							$(td).parent().slideUp("fast");
							getTotalFreeCamps();
						});
					}

					function addNew(e){
						$(".addNew").slideUp();
						$.ajax({
							url: "https://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&addNew=1",
							dataType: "HTML",
						}).done(function(response) {
							//console.log(response);
							$("#caTable tr:last").after(response);
							getTotalFreeCamps();
						});
					}


					$(".affs").on("dblclick",handleAffs);
					
					$(".camps").on("dblclick",handleCamps);
					
					$(".deleteCA").on("click",deleteCA);
					
					$(".addNew").on("click",addNew);
					
					
					getTotalFreeCamps();
					
					$("#maintenanceTab").on("click",function(){
						
						$.ajax({
							url: "'.$set->SSLprefix.'ajax/getCampaignMaintenance.php?merchant_id='.$merchant_id.'",
							dataType: "HTML",
						}).done(function(response) {
							$("#nextTimeDoItRight").html(response);
							//console.log(response);
						});
						
					});
					
				});
			</script>
			<script src="'.$set->SSLprefix.'js/easytabs/jquery.easytabs.min.js" type="text/javascript"></script>
				<style>
				.etabs { margin: 0; padding: 0; }
		.tab { display: inline-block; zoom:1; *display:inline; background: #eee; border: solid 1px #999; border-bottom: none; -moz-border-radius: 4px 4px 0 0; -webkit-border-radius: 4px 4px 0 0; }
		.tab a { font-size: 14px; line-height: 2em; display: block; padding: 0 10px; outline: none; }
		.tab a:hover { text-decoration: underline; }
		.tab.active { background: #fff; padding-top: 6px; position: relative; top: 1px; border-color: #666; }
		.tab a.active { font-weight: bold; }
		.tab-container .panel-container { background: #fff; border: solid #666 1px; padding: 10px; -moz-border-radius: 0 4px 4px 4px; -webkit-border-radius: 0 4px 4px 4px; }
				</style>
			<!--
			<div class="normalTableTitle" style="margin-top:30px">'.lang('Affiliates Campaigns Relations').'</div>
			<div style="background: #F8F8F8;">
				<form method="POST">
					<table cellspacing=10 cellpadding=0 border=0>
						<tr>
							<td>'.lang('Affiliate').':</td>
							<td><select name="affID"><option id=-1>'.lang('Select Affiliate').'</option>'.$affiliatesStr.'</select></td>
						</tr>
						<tr>
							<td>'.lang('Campaign').':</td>
							<td><select name="campID"><option id=-1>'.lang('Select Campaign').'</option>'.$campaignsStr.'</select></td>
						</tr>
						<tr>
							<td><input type="submit" value="Save"/></td>
						</tr>
					</table>
				</form>
			</div>-->';
	
			}
/* 	$forceParamsForTracker = $set->forceParamsForTracker;
	
		$set->content .=  '
		<form id="forceParamsForTracker_form" action="'.$set->basepage.'" method="post">
			<h3>'.lang('Force Tracker Parameters').'</h3>
			<td>'.lang('Params').':<input  type="text" name="forceParamsForTracker"  value="'.$forceParamsForTracker.'" style="width: 150px;" /></td>
			<td><input type="submit"  name="forceParamsForTracker_sub" value="Save"/></td>
		</form>
			';
	 */		
		
		theme();
		break;
		
	}

?>
