<?php


/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */
require_once('common/global.php');

if (!isAdmin()) {
	$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
    _goto($lout);
}

/**
 * Retrieve deal types defaults.
 */
$arrDealTypeDefaults = getMerchantDealTypeDefaults();
$selectedPaymentMethod = isset($_GET['selectedPaymentMethod']) ? $_GET['selectedPaymentMethod'] : "";
// var_dump($_GET);
// die();
// die ($selectedPaymentMethod );
$appTable = 'payments_details';

if($_REQUEST['save']){

	$act = 'save';

}else if($_REQUEST['delete']){
//die('loo');
	$act = 'delete';
}



switch ($act) {

case "delete":
		
		//echo $ids[0].'<BR>';
		//echo $pixelCode[0];
		$qry = 'delete from ' .$appTable .' where month=' . $month[0] . ' and year = ' .$year;
                if ($affiliate_id>0){
			$qry .= ' and affiliate_id = ' . $affiliate_id;
                }
		function_mysql_query($qry,__FILE__,__FUNCTION__);
		_goto($set->SSLprefix.$set->basepage.'/admin/billing.php');
			
	break;
case "deleterecord":
		$qry = 'delete from ' .$appTable .' where paymentID="' . $pid.'"';
		function_mysql_query($qry,__FILE__,__FUNCTION__);
		_goto($set->SSLprefix.$set->basepage.'/admin/billing.php');
	break;
	
/*
	case "save":
	
		//echo $ids.'<BR>';
		//echo $pixelCode[0];
		$qry = 'update pixel_monitor set merchant_id=' .$merchant_id[0].' ,pixelCode = "' .$pixelCode[0] .'" , type = "' .$type[0]. '" where id=' . $ids[0];	
		//die ($qry);
		function_mysql_query($qry,__FILE__);
		_goto($set->basepage.'?act=new&id='.$id.'&toggleTo=tab_10#tab_10');
//		_goto($set->basepage.'?act=new&id='.$id.'#tab_10');
		//die('JUST EDIT PIXEL...');
	
	break;
*/	
	default:
	
	
	$set->content  = '<style>		
			  /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 60px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 25px;
			 width: 50px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 16px;
			  width: 16px;
			  left: 4px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
	
			aside {
					width: 20%;
					background-color: #f1f1f1;
					position:fixed;
					height:100vh;
			
			}
			ul.vertical {
			list-style-type: none;
			margin: 0;
			padding: 0;
			width: 20%;
			background-color: #f1f1f1;
			position:fixed;
			height:65vh;
			overflow:auto;
			min-width:80px;
			display:block;
		}

		ul.vertical li a {
			display: block;
			color: #000;
			padding: 8px 0 8px 16px;
			text-decoration: none;
		}

		ul.vertical li a:hover:not(.active) {
			background-color: #555;
			color:white;
		}

		ul.vertical a.active {
			background-color: #555;
			color:white;
		}	
		
		ul.vertical li.heading_active {
			padding: 8px 0 8px 5px;
			text-decoration: none;
			color:white;
			height: 32px;
		}
		ul.vertical  li input#config_filter
		{
			    width: 101px;
				/*margin-right: 5px;
				margin-left: 5px;
				margin-top: 10px;
				margin-bottom: 10px;*/
		}
		ul.vertical  li input#btnFilter
		{
			padding-left:0px !Important;
			padding-right:0px !important;
			width:100px !important;
		}
		.main {
			margin-left:20%;
			padding:1px 16px;
			height:100%;
		}
		
		.filter{
			padding-top:10px;
			padding-right:2px;
		}
		
		
		@media screen and (max-width: 290px) {
			.main{
			   margin-left:35%;
			}
		}
		</style>
		<script>
		$(document).ready(function(){
			$("ul.vertical li a").on("click",function(){
				var tabtoopen = $(this).data("tab");
				action = $("form#frmMain").attr("action");
				
				if(typeof action === "undefined"){
					action = window.location.href;
					
				}
				
				act = action.split(/[?#]/)[0];
				act = act + "?tab=" + tabtoopen;
				$("form#frmMain").attr("action", act);
				
				 $(this).toggleClass("active").parent().siblings().find("a").removeClass("active");
				show_hide_tabs(tabtoopen,"li");
			});
			
			
			
			
			function show_hide_tabs(open_tab,type){
				$(".config_tabs").hide();
				if(type=="search"){
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
						$(".config_tabs").hide();
						$(".config_tabs").each(function(k,tab){
							var search_txt = $(tab).find("div.normalTableTitle").text();
							
							search_txt = search_txt.toLowerCase();
							open_tab = open_tab.toLowerCase();
							
							if(search_txt.search(open_tab)!==-1){
								$(this).show();
							}
							else{
								$(this).hide();
							}
						});
					}
				}
				else{
					if(open_tab == "all"){
						$(".config_tabs").each(function(k,tab){
							$(this).show();
						});
					}
					else{
					$(".config_tabs").each(function(k,tab){
						if($(tab).data("tab") == open_tab){
							$(this).show();
						}
						else{
							console.log("out")
						}
					});
					}
					
				}
			}

			$("#config_filter").on("keyup",function(){
				var txtVal = $(this).val();
				if (txtVal != "") {
					$(".tblDetails").show();
					$.each($(".tblDetails"), function (i, o) {
						var match = $("tbody td:contains-ci(\'" + txtVal + "\'),thead td:contains-ci(\'" + txtVal + "\')", this);
						match.parent().siblings().hide();                         //  <<=== [LINE ADD]
						
						if (match.length > 0) {
							$(match).parent("tr").show();
							$(this).parent().prev().show();
							
							txt = $(this).parent().prev().data("tab2");
							$("ul.vertical li a").each(function(){
								console.log(txt + " ---- "+$(this).data("tab"));
									if(txt == $(this).data("tab")){
										$(this).css("color","grey");
									}
								});
						}
						else {
							$(this).hide();
							$(this).parent().prev().hide();
							
							txt = $(this).parent().prev().data("tab2");
							$("ul.vertical li a").each(function(){
								
									if(txt == $(this).data("tab")){
										$(this).css("color","black");
									}
							});
						}
					});
				} else {
					// When there is no input or clean again, show everything back
					$(".tblDetails").parent().prev().show();
					$(".tblDetails,.tblDetails tr").show();
					$(".config_tabs").show();
					$("ul.vertical li a").css("color","black");
				}
				if($(".tblDetails:visible").length == 0)
				{
					$(".config_tabs:first").find(".message").remove();
					$(".config_tabs:first").append("<p class=\"message\">'.lang('Sorry No results found!').'</p>");
					$(".btnsave").hide();
				}
				else{
					$(".message").remove();
					$(".btnsave").show();
				}
			});
			
		});
		</script>
		<aside>
		<ul class="vertical">
  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">'. lang('Sections') .'</span><!--form style="display:inline-flex;float:right;">
  <div class="filter">'.lang('Find') .': </div><div style="margin-right:10px;"><input type="text" name="config_filter" id="config_filter"></form></div--></li>
  <li><a href="javascript:void(0)" data-tab="all" class="active">'. lang('All') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="generatePayments">'. lang('Generate Payments') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="invoicesAndPayments">'. lang('Browse invoices and payments') .'</a></li>
  </ul></aside>
  <div class="main">';
  
  	if(isset($affiliate_id)){
			$affiliate_id = retrieveAffiliateId($affiliate_id);	
		}
	
		$pageTitle = lang('Billing');
		$set->breadcrumb_title =  lang($pageTitle);
	$set->pageTitle = '
	<style>
	.pageTitle{
		padding-left:0px !important;
	}
	</style>
	<ul class="breadcrumb">
		<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
		<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
		<li><a style="background:none !Important;"></a></li>
	</ul>';
		if ($month) $where = ($month>0) ? " AND month='".$month."'" : " ";
		if ($year) $where .= $year>0 ? " AND year='".$year."'" : " ";
		
		if ($affiliate_id ) $where .= " AND affiliate_id='".$affiliate_id."'";
		
                $sql = "SELECT paymentID,affiliate_id,month,year,reason FROM ".$appTable." WHERE 1 ".$where." GROUP BY paymentID";
				// die ($sql);
		$qq=function_mysql_query($sql,__FILE__,__FUNCTION__);
                
		$l=1;
		
		$mid = $_COOKIE['mid'];
		 $mid = (aesDec($mid));
		if(!is_numeric($mid))
			 $mid = $set->userInfo['relatedMerchantID'];
		 
		$clrs=mysql_fetch_assoc(function_mysql_query("SELECT PaymentColors FROM merchants where id = " . $mid,__FILE__,__FUNCTION__));
		$pendingC='';
		$paidC = '';
		if ($clrs) {
		$pc =  ($clrs['PaymentColors']);
		$pcArr = explode("|",$pc);
		$pendingC = explode(":",$pcArr[0]);
		$pendingC=$pendingC[1];
		$paidC = explode(":",$pcArr[1]);
		$paidC = $paidC[1];
		}
		
		while ($ww=mysql_fetch_assoc($qq)) {
			unset($payAddress);
			$affiliateInfo=dbGet($ww['affiliate_id'],"affiliates");
			$amount=mysql_fetch_assoc(function_mysql_query("SELECT SUM(amount) AS amount FROM ".$appTable." WHERE paymentID='".$ww['paymentID']."' AND status='approved'",__FILE__,__FUNCTION__));
			$totalFTDs=mysql_fetch_assoc(function_mysql_query("SELECT COUNT(id) AS totalFTD FROM ".$appTable." WHERE paymentID='".$ww['paymentID']."' AND status='approved' AND reportType != 'bonus'",__FILE__,__FUNCTION__));
			$paid=mysql_fetch_assoc(function_mysql_query("SELECT extras,id,total,paid,sentMail FROM payments_paid WHERE paymentID='".$ww['paymentID']."'",__FILE__,__FUNCTION__));
			if ($status == "paid" AND !$paid['paid'])
				continue;
		else if ($status == "pending" AND $paid['paid'])
			continue;
			 $lineAmount = ($paid['id'] ? $paid['total'] : $amount['amount']);
			 // $lineAmount = ($paid['id'] ? $paid['total'] : 0);
		/* 	if ($paid['id']) {
			$lineAmount = $paid['total'] ; // : $amount['amount']);
			}
			else {
				$lineAmount = $paid['total'];
			}
				 */
			
			
				if (!empty($paid['extras']) && $lineAmount ==0) {
				$exp = explode('[var]',$paid['extras']);
				$totalExtras = 0;
				foreach ($exp as $a) {
						$exp_inner = explode('|',$a);
						 $tmpVal = ($exp_inner[2])*($exp_inner[3]) ;
						 
						 $totalExtras +=  intval($tmpVal);
						
				}
				
			// die ('- : ' .$totalExtras);
				$lineAmount= $totalExtras;
				}
			
			
			
			
				
			$paymentMethod = $affiliateInfo['paymentMethod'];			
			if ($paymentMethod == "paypal") {
								$payAddress = $affiliateInfo['pay_account'];
			}
			else if (strtolower($paymentMethod) == "moneybookers")  {
								$payAddress = $affiliateInfo['pay_email'];
			}
			else if ($paymentMethod == "bank")  {
								$payAddress = (empty($affiliateInfo['pay_iban']) ? '' : 'IBAN: ' . $affiliateInfo['pay_iban']) . '   '. (empty($affiliateInfo['pay_swift']) ? '' : 'Swift: ' . $affiliateInfo['pay_swift']);
			}
			else if ($paymentMethod == "neteller") 
								$payAddress = $affiliateInfo['pay_email'] . ' ('. $affiliateInfo['account_number']. ')';
			else 
								$payAddress = $affiliateInfo['pay_email'];
			
			$gen_selectedPaymentMethod = strtolower($selectedPaymentMethod);
			// if  ($gen_selectedPaymentMethod=='skrill')
				// $gen_selectedPaymentMethod = "moneybookers";
			
		
			if (!empty($gen_selectedPaymentMethod) AND $gen_selectedPaymentMethod != strtolower($affiliateInfo['paymentMethod']) ) continue; // Filting Payment Method
			
			$paidStatus = ($paid['paid'] ? ('Paid') : ('Pending') . '...');
			
			$bckColor = '';
			if ($paidStatus=='Paid') {
					
					if ($paidC=='') 
					$paidC =( $l % 2 ? '#EFEFEF;"' : '');
					$bckColor = $paidC;
					
				}
				if ($paidStatus=='Pending...') {
					if ($pendingC=='') 
					$pendingC =( $l % 2 ? '#EFEFEF;"' : '');
					
					$bckColor = $pendingC;
					
				}
				
				/* var_dump($paid);
				die(); */
	 		// if ((!isset($_GET['valid']) || $valid == 0) && empty($paid['extras'])) {
			if (($lineAmount==0) && !empty($paid['extras'])) {
				// $lineAmount = $paid['extras'];
				// die( $paid['extras']);
			}
				
	 		if ($valid == 0 && empty($paid['extras'])) {
				// die ('1');
					//echo '<script>alert("Test");</script>';
					if (($lineAmount)> 0 || $lineAmount< 0  || !empty($paid['extras'])) {
						// && $totalFTDs['totalFTD'] == 0 
					 
					}
					else {
							continue;
							}
			} 
                    // echo '<br>' .$lineAmount.'<br>'    ;
					
			$dt = $ww['month']."/".$ww['year'];
					
			//$block= '<tr '.($l % 2 ? 'class="trLine" style: "background='. $bckColor  . '">
			$block= '<tr '.'class="trLine" style= "background:'. $bckColor .'">
					<td>'.$l.'</td>
					<td width="180" style="text-align: left;"><a href="'.$set->SSLprefix.$set->basepage.'?act=view&paymentID='.$ww['paymentID'].'" target="_blank">'.lang('View').'</a> | <a class="deletePayment" data-pdate="'. $dt .'" data-affID = "'. $affiliateInfo['id']  .'" data-id="'. $ww['paymentID'] . '" style="cursor:pointer">'.lang('Delete').'</a>'.($paid['paid'] ? ($paid['sentMail'] ? ' | <span style="color: green;">'.lang('Sent').'!</span> | <a href="'.$set->SSLprefix.$set->basepage.'?act=sendInvoice&paymentID='.$ww['paymentID'].'&affiliate_id='.$affiliateInfo['id'].'" style="font-size: 10px;">Send Again</a>' : ' | <a href="'.$set->SSLprefix.$set->basepage.'?act=sendInvoice&paymentID='.$ww['paymentID'].'&affiliate_id='.$affiliateInfo['id'].'">'.lang('Send Invoice').'</a>') : '').' </td>
					<td style="text-align: left;">'.$ww['month'].' / '.$ww['year'].'</td> 
					<td style="text-align: left;">'.$ww['paymentID'].'</td>
					<td style="text-align: left;"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'">'.$affiliateInfo['username'].'</a></td>
					<td style="text-align: left;"><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'">'.$affiliateInfo['first_name'].' '.$affiliateInfo['last_name'].'</a></td>
					<td><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id='.$affiliateInfo['id'].'">'.$affiliateInfo['id'].'</a></td>
					<td>'.$ww['month'].'/'.$ww['year'].'</td>
					<td>'.$totalFTDs['totalFTD'].'</td>
					<td>'.price($lineAmount).'</td>
					<td style="text-align: left;">'.strtoupper($affiliateInfo['paymentMethod']).'</td>
					<td style="text-align: left;">'.strtoupper($affiliateInfo['preferredCurrency']).'</td>
					<td style="text-align: left;">'.$payAddress.'</td>
					<td>'.$ww['reason'].'</td>
					<td>'.lang($paidStatus).'</td>
					</tr>';
			
			$listReport .=$block;
			$totalFTD += $totalFTDs['totalFTD'];
			$totalAmount += $lineAmount;
			$l++;
			}
		for ($i=1; $i<=12; $i++) $listMonths .= '<option value="'.$i.'" '.($i == $month ? 'selected' : '').'>'.$i.'</option>';
		
		for ($i=date("Y"); $i>=2012; $i--) $listYears .= '<option value="'.$i.'" '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
		//for ($i=2012; $i<=date("Y"); $i++) $listYears .= '<option value="'.$i.'" '.($i == $year ? 'selected' : '').'>'.$i.'</option>';
		if ($ty) {
			$set->content .= '
				<script type="text/javascript">
					$(document).ready(function() {
						alert(\''.lang('Invoice E-mail has Sent').'\');
						});
				</script>';
			}
		$set->content .='<style>
			  /* The switch - the box around the slider */
			.switch {
			  position: relative;
			  display: inline-block;
			  width: 60px;
			  height: 25px;
			}

			/* Hide default HTML checkbox */
			.switch input {display:none;}

			/* The slider */
			.slider {
			  position: absolute;
			  cursor: pointer;
			  top: 0;
			  left: 0;
			  right: 0;
			  bottom: 0;
			  background-color: #ccc;
			  -webkit-transition: .4s;
			  transition: .4s;
			  height: 25px;
			 width: 50px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 16px;
			  width: 16px;
			  left: 4px;
			  bottom: 4px;
			  background-color: white;
			  -webkit-transition: .4s;
			  transition: .4s;
			}

			input:checked + .slider {
			  background-color: #2196F3;
			}

			input:focus + .slider {
			  box-shadow: 0 0 1px #2196F3;
			}

			input:checked + .slider:before {
			  -webkit-transform: translateX(26px);
			  -ms-transform: translateX(26px);
			  transform: translateX(26px);
			}

			/* Rounded sliders */
			.slider.round {
			  border-radius: 34px;
			}

			.slider.round:before {
			  border-radius: 50%;
			}
			
		</style>';
	
		$qqAff = function_mysql_query("SELECT id,username,first_name,last_name FROM affiliates WHERE valid='1' ORDER BY id ASC",__FILE__);

			if (!(isset($affiliate_id) && !empty($affiliate_id))) {
				$listOfAffiliates = '<option selected value="">'.lang('Choose Affiliate').'</option>';
			}

			while ($affiliateww = mysql_fetch_assoc($qqAff)) {		   
			   if (isset($affiliate_id) && !empty($affiliate_id)) {
					$listOfAffiliates .= '<option value="'.$affiliateww['id'].'" '.($affiliateww['id'] == $affiliate_id ? 'selected' : '').'>['.$affiliateww['id'].'] '
									  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
			   }
			   else {
					$listOfAffiliates .= '<option value="'.$affiliateww['id'].'">['.$affiliateww['id'].'] '
									  .  $affiliateww['username'].' ('.$affiliateww['first_name'].' '.$affiliateww['last_name'].')</option>';
			   }
			}
		$set->content .= '
		<div id="generatePayments" data-tab="generatePayments" class="config_tabs">
			<div class="normalTableTitle" style="cursor: pointer;">'.lang('Generate Payments').'</div>
			<div id="tab_1" align="left" style="background: #EFEFEF; padding: 10px;">
				<form action="'.$set->SSLprefix.$set->basepage.'" method="get" id="frmGenerate">
					<input type="hidden" id="act" name="act" value="generate" />
					'.lang('Generate Payments').': <select name="month" style="width: 100px;">'.$listMonths.'</select> / <select name="year" style="width: 100px;">'.$listYears.'</select> '.lang('Affiliate ID').': 
					
					<!--input type="text" name="affiliate_id" value="0" style="width: 100px; text-align: center;" /--> 
					
					<span class="ui-widget" >'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox"  >'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</span>
					
					<input type="submit" value="'.lang('Generate').'" style="margin-left:20px"/>
				<span style="position: relative;top: 10px;left: 20px;"><label class="switch"><label class="switch"><input type="checkbox" id="emptyInvoice" name="emptyInvoice" value=1 '. (isset($_GET['emptyInvoice']) && $_GET['emptyInvoice'] == 1?'checked':'') .'><span class="slider round"></span></label></span><span style="margin-left:20px;">Generate New Empty Invoice</span>
				</form>
				<script>
				$(document).ready(function(){
					$("#emptyInvoice").change(function(){
						if($(this).is(":checked")){
							$("#act").val("create");
						}
						else{
							$("#act").val("generate");
						}
					});
				});
				</script>
			</div>
			<br />
			<!--div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_3\').slideToggle(\'fast\');">'.lang('Generate New Empty Invoice').'</div>
			<div id="tab_3" align="left" style="background: #EFEFEF; padding: 10px;">
				<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
					<input type="hidden" name="act" value="create" />
					'.lang('Monthly Invoice').': <select name="month" style="width: 100px;">'.$listMonths.'</select> / <select name="year" style="width: 100px;">'.$listYears.'</select> 
					'.lang('Affiliate ID').': <input type="text" name="affiliate_id" value="'.$affiliate_id.'" style="width: 100px;" />
					<input type="submit" value="'.lang('Create').'" />
				</form>
			</div-->
			</div>';
			
			$listMonths = '<option value="0">'.lang('All').'</option>' . $listMonths;
		$listYears = '<option value="0">'.lang('All').'</option>' . $listYears;
		$set->content.='
		<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
		<script>
		$(document).ready(function(){
			
				$(".deletePayment").on("click",function(){
					var rec_id = $(this).data("id");
					var payment_dt = $(this).data("pdate");
					var affID = $(this).data("affid");
					$.prompt("'. lang("Are you sure you want to delete <br/>Invoice for Date :"). ' "+ payment_dt + "' . lang("<br/>For Affiliate ID : ") .' "+ affID, {
																	top:200,
																	title: "'. lang("Billing Module") .'",
																	buttons: { "'.lang("Yes").'": true, "'. lang('No') .'": false },
																	submit: function(e,v,m,f){
																		if(v){
																			p_id=rec_id;
																			url="'.$set->SSLprefix.$set->basepage . '?act=deleterecord&pid="+ p_id;
																			window.location.href=url;
																		} else {
																			
																		}
							}
						});
		});
		});
		</script>
		';
		$set->content .= '<script src="js/autocomplete.js"></script>
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
			width: 120px;
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
		</style>';
		$set->content .= '
		<div id="invoicesAndPayments" data-tab="invoicesAndPayments" class="config_tabs">
			<div class="normalTableTitle" style="cursor: pointer;" onclick="$(\'#tab_2\').slideToggle(\'fast\');">'.lang('Search Payments').'</div>
			<div id="tab_2" align="left" style="background: #EFEFEF; padding: 10px;">
				<form id="form1" action="'.$set->basepage.'" method="get">
					<select id="monthSelect" name="month" style="width: 100px;">'.$listMonths.'</select> / <select id="yearSelect" name="year" style="width: 100px;">'.$listYears.'</select> 
					'.lang('Status').': <select name="status" style="width: 100px;">
						<option value="">'.lang('All').'</option>
						<option value="pending" '.($status == "pending" ? 'selected="selected"' : '').'>'.lang('Pending').'...</option>
						<option value="paid" '.($status == "paid" ? 'selected="selected"' : '').'>'.lang('Paid').'</option>
					</select>
					'.lang('Payment Method').': <select name="selectedPaymentMethod" style="width: 140px;">
						<option value="" '.($selectedPaymentMethod == ""  ? 'selected="selected"' : '').'>'.lang('All').'</option>
						<option value="bank" '.($selectedPaymentMethod == "bank" ? 'selected="selected"' : '').'>'.lang('Wire Transfer').'</option>
						<option value="Skrill" '.($selectedPaymentMethod == "Skrill" ? 'selected="selected"' : '').'>'.lang('Skrill').'</option>
						<option value="paypal" '.($selectedPaymentMethod == "paypal" ? 'selected="selected"' : '').'>'.lang('PayPal').'</option>
						<option value="webmoney" '.($selectedPaymentMethod == "webmoney" ? 'selected="selected"' : '').'>'.lang('Web Money').'</option>
						<option value="neteller" '.($selectedPaymentMethod == "neteller" ? 'selected="selected"' : '').'>'.lang('Neteller').'</option>
						<option value="chinaunionpay" '.($selectedPaymentMethod == "chinaunionpay" ? 'selected="selected"' : '').'>'.lang('China Union Pay').'</option>
					</select>
					'.lang('Affiliate ID').': <!--input type="text" id="affiliate_id" name="affiliate_id" value="'.$affiliate_id.'" style="width: 100px;" /-->
						<span class="ui-widget" >'
								. '<!-- name="affiliate_id2" -->'
								. '<select id="combobox2">'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select></span>
								
                                        <label style="margin-left:20px">'.lang('Show empty invoices').':</label>&nbsp;
                                        <select name="valid" style="width:180px;;">
                                            <option '. ($valid==0 ? 'selected' :'') .' value="0">'.lang('No').'</option>'
                                            . '<option '. ($valid==1 ? 'selected' :'') .' value="1">'.lang('Yes').'</option>
                                        </select>
					<input  type="submit" value="'.lang('View').'" />';
					if(!$set->isNetwork) {
					$set->content .= '
					<input name="delete" type="submit" value="'.lang('Delete').'" 
							onClick="return confirmation()"/>';
							
					}
					$set->content .= '
				</form>
			</div>
					<script>
					$("#combobox2").combobox();
						function confirmation() {
							var aff = isNaN(document.getElementById("affiliate_id").value) || !document.getElementById("affiliate_id").value ? " '.lang('ALL').' " : " '.lang('affiliate').' " + document.getElementById("affiliate_id").value;
							
							var msg = "'.lang('Are you sure you want to delete').' " + aff + " '.lang('payments for').' " 
							            + document.getElementById("yearSelect").value + "-" + document.getElementById("monthSelect").value + "?";
							
							return confirm(msg);
						}
					</script>
						'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<div class="exportCSV" style="float:left"><a href="'.$set->uri.(strpos($set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div><div style="clear:both"></div>' : '').'
				
			<br />
			<div class="normalTableTitle">'.lang('Billing Reports').'</div>
			
			<div style="background: #F8F8F8;">';
			$instTable ='
				<table width="100%" class="normal" border="0" cellpadding="3" cellspacing="0">
					<thead><tr>
						<td>#</td>
						<td style="text-align: left;">'.lang('Actions').'</td>
						<td style="text-align: left;">'.lang('Date').'</td>
						<td style="text-align: left;">'.lang('Payment ID').'</td>
						<td style="text-align: left;">'.lang('Username').'</td>
						<td style="text-align: left;">'.lang('Full Name').'</td>
						<td>'.lang('Affiliate ID').'</td>
						<td>'.lang('Month').' / '.lang('Year').'</td>
						<td>'.lang('Total FTD').'</td>
						<td width="100">'.lang('Total').'</td>
						<td style="text-align: left;">'.lang('Payment Method').'</td>
						<td style="text-align: left;">'.lang('Preferred Currency').'</td>
						<td style="text-align: left;">'.lang('Payment Address').'</td>
						<td>'.lang('Reason').'</td>
						<td>'.lang('Payment Status').'</td>
					</tr></thead><tfoot>'.$listReport.'
					<tr style="background: #F8F8F8;">
						<td  style="text-align: left;">'.lang('Total').': <b>'.($l-1).'</b></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td>'.$totalFTD.'</td>
						<td>'.price($totalAmount).'</td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
					</tr>
				</tfoot>
				</table>';
				
				$set->content .= $instTable .'
			</div>
			</div>';
			$set->content.="</div>";
		excelExporter($instTable, 'Billing');
		// $set->content .= $tableStr . '</div>' . getPager();
		theme();
		break;
	
	case "create":
            $affiliate_id = retrieveAffiliateId($affiliate_id);
		$getAffiliate=dbGet($affiliate_id,"affiliates");
		if (!$getAffiliate['id']) _goto($set->basepage);
		$paymentID = 'BF'.$year.($month < 10 ? '0'.$month : $month).$affiliate_id.rand('5','9').rand('1','5').'Pm';
                
                $sql = "INSERT INTO payments_details (rdate,status,month,year,paymentID,merchant_id,affiliate_id) "
                        . "VALUES ('".dbDate()."','approved','".$month."','".$year."','".$paymentID."','0','".$affiliate_id."')";
                
		function_mysql_query($sql,__FILE__,__FUNCTION__);
		_goto($set->basepage.'?act=view&paymentID='.$paymentID);
		break;
	
                
	case "sendInvoice":
            $sql = "SELECT * FROM payments_details WHERE paymentID='".$paymentID."'";
            $getInvoice = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
            
            if (!$getInvoice['id']) {
                _goto($set->basepage);
            }
            $affiliate_id = retrieveAffiliateId($affiliate_id);
            $affiliateInfo = dbGet($affiliate_id, "affiliates");
            $set->sendTo   = $affiliateInfo['mail'];
            $set->subject  = $affiliateInfo['username'] . ' - Invoice #' . $getInvoice['paymentID'] . ' ' . $getInvoice['month'] . '/' . $getInvoice['year'] . ' - ' . $set->webTitle;
            $set->body     = getPayment($paymentID, 1);
            sendMail(0, 0);
            updateUnit("payments_paid", "sentMail='1'", "paymentID='" . $getInvoice['paymentID'] . "'");
            _goto($set->refe . ($set->refe == $set->webAddress . "admin/billing.php" ? '?' : '&') . 'ty=1');
            break;
	
        
	case "view":
	
            $set->content .= getPayment($paymentID);
            theme(1);
            break;
	
        
        
	case "paid":
            //exit(print_r($extra, true));
            /*Array
            (
                [merchant_id] => Array
                    (
                        [0] => 22
                        [1] => 
                    )
                [deal] => Array
                    (
                        [0] => 
                        [1] => 
                    )
                [unit_price] => Array
                    (
                        [0] => 100
                        [1] => 0
                    )
                [quantity] => Array
                    (
                        [0] => 3
                        [1] => 0
                    )
            )*/
            
            //exit(print_r($db, true));
            /*Array
            (
                [paymentID] => DE12014105076MO
                [month] => 10
                [year] => 2014
                [affiliate_id] => 507
                [id] => 22
                [amount_gap_from_previous_month] => 0
                [total] => 677.2
                [transaction_id] => 
                [notes] => 
            )*/
            
            
            $db['rdate'] = dbDate();
            $db['paid']  = ($paid ? '1' : '0');
            
            for ($i = 0; $i <= count($extra['deal']) - 1; $i++) {
                unset($extra_db);
                
                if (!isset($extra['merchant_id'][$i]) && !$extra['deal'][$i]) {
                    continue;
                }
                
                $extra_db[] = $extra['merchant_id'][$i];
                $extra_db[] = $extra['deal'][$i];
                $extra_db[] = $extra['unit_price'][$i];
                $extra_db[] = $extra['quantity'][$i];
                
                if (count($extra_db) > 0 && $extra['unit_price'][$i] && $extra['quantity'][$i]) {
                    $allExtra[] = implode('|', $extra_db);
                }
            }
            
            if (count($allExtra) > 0) {
                $db['extras'] = implode('[var]', $allExtra);
            } else {
                $db['extras'] = '';
            }
            
            //exit(print_r($allExtra, true));
            /*Array
            (
                [0] => 22||100|3
            )*/
            
            
            //exit(print_r($db, true));
            /*Array
            (
                [paymentID] => DE12014105076MO
                [month] => 10
                [year] => 2014
                [affiliate_id] => 507
                [id] => 22
                [amount_gap_from_previous_month] => 0
                [total] => 677.2
                [transaction_id] => 
                [notes] => 
                [rdate] => 2015-08-16 09:42:21
                [paid] => 0
                [extras] => 22||100|3
            )*/
            
            $sql       = "SELECT COUNT(`id`) AS count FROM `payments_paid` WHERE `paymentID` = '" . $db['paymentID'] . "';";
            $arrCntTmp = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
            
            if (empty($arrCntTmp['count'])) {
                dbInsert($db, 'payments_paid');
            } else {
                if (array_key_exists('amount_gap_from_previous_month', $db)) {
                    unset($db['amount_gap_from_previous_month']);
                }
                
                if (array_key_exists('credit_gap_from_previous_month', $db)) {
                    unset($db['credit_gap_from_previous_month']);
                }
                
                dbUpdate($db, 'payments_paid');
            }
            
            $sql = "UPDATE affiliates 
                    SET credit='" . mysql_real_escape_string($db['creditLeft']) . "' 
                    WHERE id='" . $db['affiliate_id'] . "';";
            
            function_mysql_query($sql,__FILE__,__FUNCTION__);
            _goto('/admin/billing.php?act=view&paymentID=' . $db['paymentID']);
            break;
            
            
            
	// --------------------------------------------------------------------------------------------------------------------
	
	case "generate":
	
		$affiliate_id = retrieveAffiliateId($affiliate_id);
            
            
		if ($affiliate_id > 0) {
			$affWhere = " AND id='".$affiliate_id."'";
			function_mysql_query("DELETE FROM ".$appTable." WHERE affiliate_id='".$affiliate_id."' AND month='".$month."' AND year='".$year."'",__FILE__,__FUNCTION__);
			function_mysql_query("DELETE FROM payments_paid WHERE affiliate_id='".$affiliate_id."' AND month='".$month."' AND year='".$year."'",__FILE__,__FUNCTION__);
			}
		$pageTitle = lang('Generating Payments...');
		$set->breadcrumb_title =  lang($pageTitle);
	$set->pageTitle = '
	<style>
	.pageTitle{
		padding-left:0px !important;
	}
	</style>
	<ul class="breadcrumb">
		<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
		<li><a href="'.$set->SSLprefix.'admin/billing.php">'.lang('Billing').'</a></li>
		<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
		<li><a style="background:none !Important;"></a></li>
	</ul>';
		$invoiceDate = $year.'-'.($month < 10 ? '0'.$month : $month).'-01';
		$currentDate = date("Y-m-01",strtotime("+1 Month",strtotime($invoiceDate)));
                
                
                
		if (date("Y-m-d") < $currentDate) _goto($set->basepage);
                
		$getInvoices=mysql_result(function_mysql_query("SELECT COUNT(id) FROM ".$appTable." WHERE month='".$month."' AND year='".$year."'",__FILE__,__FUNCTION__),0);
		// if ($getInvoices > 0) _goto($set->basepage);
		$set->content .= '<script type="text/javascript">
				var affiliate_ids = new Array();
				var affiliateNames = new Array();
				';
			$affNum=0;
			$sql = "SELECT * FROM affiliates WHERE valid='1' ".$affWhere." ORDER BY id ASC";
			$boolHasResults = false;
			$qq=function_mysql_query($sql,__FILE__,__FUNCTION__);
			while ($ww=mysql_fetch_assoc($qq)) {
				$chkExist = mysql_fetch_assoc(function_mysql_query("SELECT id FROM ".$appTable." WHERE month='".$month."' AND year='".$year."' AND affiliate_id='".$ww['id']."'",__FILE__,__FUNCTION__));
				if ($chkExist['id']) continue;
				$set->content .= 'affiliate_ids['.$affNum.'] = \''.$ww['id'].'\';';
				$set->content .= 'affiliateNames['.$affNum.'] = \''.$ww['username'].'\';';
				$affNum++;
				$boolHasResults=true;
				}
                                
			if ($boolHasResults) {
			$set->content .= '
				var currectNum = 0;
				function generatePayments() {
					ajax(\''.$set->SSLprefix.$set->basepage.'?act=generatePayment&month='.$month.'&year='.$year.'&affiliate_id=\'+affiliate_ids[currectNum],\'generateTo\');
					currectNum = currectNum + 1;
					gid(\'nowMail\').innerText = currectNum;
					gid(\'perC\').innerText = Math.round((currectNum / '.$affNum.') * 100)+\'%\';
					gid(\'statusGrapth\').style.width = Math.round((currectNum / '.$affNum.') * 396)+\'px\';
					if (currectNum < '.$affNum.') setTimeout(\'generatePayments()\',800);
						 else gid(\'compl\').innerHTML = \'Payments Completed!<br /><a href="'.$set->SSLprefix.$set->basepage.'?month='.$month.'&year='.$year.'">Payments List</a>\';
					}
					window.onload = function() {
						generatePayments();
						}
				</script>';
			}
			else {
			$set->content .= '
				var currectNum = 0;
				function generatePayments() {
					
					currectNum = currectNum + 1;
					gid(\'nowMail\').innerText = currectNum;
					gid(\'perC\').innerText = Math.round((1) * 100)+\'%\';
					gid(\'statusGrapth\').style.width = Math.round((currectNum / '.$affNum.') * 396)+\'px\';
					if (currectNum < '.$affNum.') setTimeout(\'generatePayments()\',800);
						 else gid(\'compl\').innerHTML = \'No Affiliates To Run!<br /><a href="'.$set->SSLprefix.$set->basepage.'?month='.$month.'&year='.$year.'">Payments List</a>\';
					}
					window.onload = function() {
						generatePayments();
						}
				</script>';	
			}
			
		$set->content .= '<table align="center" width="400">
					<tr>
						<td align="left" style="font-family: Arial;"><b>Generate Payments...</b></td>
						<td align="right"><span id="generateTo" style="font-family: Arial;"></span></td>
					</tr><tr>
						<td colspan="2" height="16" background="/admin/images/payment_block.gif"><div id="statusGrapth" style="width: 1px; height: 16px; background: url(/admin/images/payment_block_bg.gif);"></div></td>
					</tr><tr>
						<td align="left" style="font-family: Arial;"><span id="nowMail">0</span>/'.$affNum.'</td>
						<td align="right"><span id="perC" style="font-weight: bold; font-family: Arial;">0%</span></td>
					</tr><tr>
						<td colspan="2" height="5"></td>
					</tr><tr>
						<td colspan="2" align="center"><div id="compl" style="color: green; font-weight: bold;"></div></td>
					</tr>
				</table>';
		theme();
		break;
                
                
		
	case "generatePayment":
		// NEW.
		$affiliate_id = retrieveAffiliateId($affiliate_id);
		$getAffiliate = dbGet($affiliate_id, 'affiliates');
		
		$paymentID    = 'DE'.rand('1', '5').$year.($month < 10 ? '0'.$month : $month).$affiliate_id.rand('5', '9').'MO';
		$num          = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		$from         = $year . '-' . ($month < 10 ? '0' . $month : $month) . '-01';
		$to           = $year . '-' . ($month < 10 ? '0' . $month : $month) . '-' . $num . ' 23:59:59'; // NEW.
		
		$sql = "SELECT id FROM " . $appTable 
			 . " WHERE month = '" . $month . "' AND year = '" . $year 
			 . "' AND affiliate_id = '" . $getAffiliate['id'] . "';";
		
		$chkExist = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
		
		if ($chkExist['id']) {
                    echo '<b>', $getAffiliate['username'], ' </b> [', $getAffiliate['id'], '] - <b>Exist!</b>';
                    exit;
		}
		
		
		$qq = function_mysql_query("SELECT * FROM merchants WHERE valid='1' ORDER BY pos",__FILE__,__FUNCTION__);
		
		while ($ww = mysql_fetch_assoc($qq)) {
			$merchantName = strtolower($ww['name']);
			$merchantID   = $ww['id'];
                        
                    // NEW FLOW.
						$arrCommissions       = [];
						
							
                    $comsQuery = "SELECT merchantID, affiliateId, traderID as trader_id, Type as type, DealType as deal_type, COUNT(*) AS quantity, SUM(Amount) AS amount, SUM(Commission) AS commission  FROM commissions WHERE affiliateId = " . $affiliate_id . " AND merchantID = ". $merchantID ." AND DATE>= '".$from."' AND DATE <= '".$to."' GROUP BY traderID, TYPE, DealType";
                    $allCommissions = function_mysql_query($comsQuery,__FILE__);
							
							
                    if (mysql_num_rows($allCommissions) === false || mysql_num_rows($allCommissions) == 0) {
							continue;
						}
						
				
                    while ($arrQuantityCommission = mysql_fetch_assoc($allCommissions)) {
						
                        $strDealType = $arrQuantityCommission['deal_type'];
								$intQuantity     = $arrQuantityCommission['quantity'];
								$floatCommission = $arrQuantityCommission['commission'];
								$strTraderId     = $arrQuantityCommission['trader_id'];
								
                        if($arrQuantityCommission['type'] == 'Bonus'){
                            $strDealType = 'Bonus';
									}
									
                        $withdrawal = 0;
                            
							$status           = 'approved';

                            if ($status == 'approved' && !empty($strTraderId)) {
                                $sql = "SELECT id, status, notes FROM traders_tag "
                                     . "WHERE merchant_id = " . $ww['id'] . " AND trader_id = " . $strTraderId;
                                
                                $tag = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                                
                                if (
                                    $tag['status'] == 'fraud'      || $tag['status'] == 'withdrawal' || 
                                    $tag['status'] == 'chargeback' || $tag['status'] == 'duplicates'
                                ) {
                                    $status = 'canceled';
                                }
                                
                                if ($tag['status'] == 'pending' || $tag['status'] == 'other') {
                                    $status = 'pending';
                                }
                                
                                $reason = isset($tag['notes']) && !empty($tag['notes']) ? $tag['notes'] : $tag['status'];
                                $sql    = "UPDATE traders_tag SET calReport = '1' WHERE id = '" . $tag['id'] . "';";
                                function_mysql_query($sql,__FILE__,__FUNCTION__);
                            }
                            
                            $sql = "INSERT INTO " . $appTable 
                                    . " (rdate, status, reportType, month, year, paymentID, merchant_id, affiliate_id, 
                                        trader_id, amount, deposit, withdrawal,reason) 
                                    VALUES ('" . dbDate() . "', '" . $status . "', '" . $strDealType . "', '" . $month . "', '" . $year . "', '" 
                                           . $paymentID . "', '" . $ww['id'] . "', '" . $affiliate_id . "', '" . $strTraderId . "', '" 
                                       . $floatCommission . "', '" . $cpaAmount . "', '" . $withdrawal . "', '" . $reason . "');";
                            
                            if (function_mysql_query($sql,__FILE__,__FUNCTION__)) {
                                $myReportType = $status == 'approved' ? $myReportType + 1 : $myReportType;
                            }

                        
                        $status = 'approved';
                    }
                } // End of merchants loop.
                
                // Add Network Bonus to the Affiliate, that reached the goal!
                if ($myReportType > 0) {
                    $sql = "SELECT * FROM network_bonus "
                         . " WHERE min_ftd <= '" . $myReportType . "' AND (group_id = '99999' OR group_id = '" . $getAffiliate['group_id'] . "') "
                         . " ORDER BY min_ftd DESC LIMIT 0, 1;";
                    
                    $getBonus = mysql_fetch_assoc(function_mysql_query($sql,__FILE__,__FUNCTION__));
                    
                    if ($getBonus['id']) {
                        $sql = "INSERT INTO " . $appTable . " (rdate, status, reportType, month, year, paymentID, affiliate_id, amount, reason)  
                                VALUES ('" . dbDate() . "', 'approved', 'bonus', '" . $month . "', '" . $year . "', '" 
                                           . $paymentID . "', '" . $affiliate_id . "', '" . $getBonus['bonus_amount'] . "', '" . $getBonus['title'] . "');";
                        
                        function_mysql_query($sql,__FILE__,__FUNCTION__);
                    }
                }
		
		echo '<b>' , $getAffiliate['username'] , ' </b> [' , $getAffiliate['id'] , ']';
		break;
	
	
                
                
	case "details":
            $reportType  = isset($reportType)  && !empty($reportType)  ? " AND pd.reportType = '" . $reportType . "' " : '';
            $status      = isset($status)      && !empty($status)      ? " AND pd.status = '" . $status . "' " : '';
            $invoice     = isset($invoice)     && !empty($invoice)     ? " AND pd.paymentID = '" . $invoice . "' " : '';
            $merchant_id = isset($merchant_id) && !empty($merchant_id) ? ' AND pd.merchant_id = ' . $merchant_id . ' ' : '';
            $strReport   = '';
            $i           = 0;
            
            $sql = "SELECT pd.*, aff.username AS aff_username, dr.country AS trader_country, m.name AS merchant_name, "
                    . "("
                    .   "SELECT MIN(rdate) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id"
                    . ") AS deposit_date, "
                    . "("
                    . " SELECT amount FROM data_sales "
                    . " WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'deposit' ORDER BY rdate DESC LIMIT 0, 1"
                    . ") AS ftd_amount, "
                    . "("
                    . " SELECT COUNT(id) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'deposit'"
                    . " ) AS total_deposits, "
                    . "("
                    . " SELECT SUM(amount) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'deposit'"
                    . " ) AS deposits_amount, "
                    . "("
                    . " SELECT IFNULL(SUM(amount), 0) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'bonus'"
                    . " ) AS bonus_amount, "
                    . "("
                    . " SELECT IFNULL(SUM(amount), 0) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'volume'"
                    . " ) AS volume_amount, "
                    . "("
                    . " SELECT IFNULL(SUM(amount), 0) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'chargeback'"
                    . " ) AS chargeback_amount, "
                    . "("
                    . " SELECT IFNULL(SUM(amount), 0) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'withdrawal'"
                    . " ) AS withdrawal_amount, m.producttype AS producttype, "
                    . "(CASE 
                            WHEN (LOWER(m.producttype) = 'casino' OR LOWER(m.producttype) = 'sportsbetting') THEN 
                                (SELECT COUNT(id) FROM data_stats WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'bets') 
                            ELSE 
                                (SELECT COUNT(id) FROM data_sales WHERE trader_id = pd.trader_id AND merchant_id = pd.merchant_id AND type = 'positions') 
                        END) AS trades "
                    . "FROM payments_details AS pd "
                    . "INNER JOIN affiliates AS aff ON aff.id = pd.affiliate_id "
                    . "INNER JOIN data_reg AS dr ON dr.trader_id = pd.trader_id AND  pd.merchant_id = dr.merchant_id "
                    . "INNER JOIN merchants AS m ON m.id = pd.merchant_id "
                    . "WHERE 1 = 1 " . $reportType . $status . $invoice . $merchant_id
                    . 'ORDER BY rdate DESC;';
            
            $resource = function_mysql_query($sql,__FILE__,__FUNCTION__);
            
            while ($arrRow = mysql_fetch_assoc($resource)) {
                $strWhere = 'WHERE 1 = 1 ' 
                          . (isset($_GET['merchant_id']) && !empty($_GET['merchant_id']) ? ' AND merchant_id = ' . $_GET['merchant_id'] . ' ' : '') 
                          . (isset($arrRow['trader_id']) && !empty($arrRow['trader_id']) ? ' AND trader_id = ' . $arrRow['trader_id'] . ' ' : '');
                
                $floatRevenue  = getRevenue($strWhere, $arrRow['producttype']);
                $strReport    .= '<tr>
                                    <td>' . $arrRow['trader_id'] . '</td>
                                    <td>' . date('d/m/Y', strtotime($arrRow['rdate'])) . '</td>
                                    <td><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id=' . $arrRow['affiliate_id'] . '">' . $arrRow['affiliate_id'] . '</a></td>
                                    <td><a href="'.$set->SSLprefix.'admin/affiliates.php?act=new&id=' . $arrRow['affiliate_id'] . '">' . $arrRow['aff_username'] . '</a></td>
                                    <td>' . longCountry($arrRow['trader_country']) . '</td>
                                    <td>' . $arrRow['merchant_id'] . '</td>
                                    <td>' . $arrRow['merchant_name'] . '</td>
                                    <td>' . $arrRow['deposit_date'] . '</td>
                                    <td>' . round($arrRow['ftd_amount'], 2) . '</td>
                                    <td>' . $arrRow['total_deposits'] . '</td>
                                    <td>' . round($arrRow['deposits_amount'], 2)  . '</td>
                                    <td>' . round($arrRow['bonus_amount'], 2) . '</td>
                                    <td>' . round($arrRow['volume_amount'], 2) . '</td>
                                    <td>' . round($arrRow['chargeback_amount'], 2) . '</td>
                                    <td>' . round($arrRow['withdrawal_amount'], 2) . '</td>
                                    <td>' . round($floatRevenue, 2) . '</td>
                                    <td>' . $arrRow['trades'] . '</td>
                                    <td>' . round($arrRow['amount'], 2) . '</td>
                                    <td>' . ucwords($arrRow['status']) . '</td>
                                    <td>' . $arrRow['reason'] . '</td>
                              </tr>';
                
                unset($arrRow, $strWhere, $floatRevenue);
                $i++;
            }
            
            if ($i > 0) {
                $set->sortTableScript = 1;
            }
            
            $pageTitle  = lang('Invoice') . ' #' . $invoice . ' ' . lang('Report');
			$set->breadcrumb_title =  lang($pageTitle);
	$set->pageTitle = '
	<style>
	.pageTitle{
		padding-left:0px !important;
	}
	</style>
	<ul class="breadcrumb">
		<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
		<li><a href="'.$set->SSLprefix.'admin/billing.php">'.lang('Billing').'</a></li>
		<li><a href="'. $set->uri .'">'.lang($pageTitle).'</a></li>
		<li><a style="background:none !Important;"></a></li>
	</ul>';
            $set->sortTable  = 1;
            $set->totalRows  = $i;
            $set->content   .= '<div class="normalTableTitle" style="width: 1995px;">'.$pageTitle.'</div>
                    <div style="background: #F8F8F8;">
                        <table width="2000" class="tablesorter" border="0" cellpadding="0" cellspacing="0">
                            <thead><tr>
                                    <th>'.lang(ptitle('Trader ID')).'</th>
                                    <th>'.lang('Registration Date').'</th>
                                    <th>'.lang('Affiliate ID').'</th>
                                    <th>'.lang('Affiliate Username').'</th>
                                    <th>'.lang('Country').'</th>
                                    <th>'.lang('Merchant ID').'</th>
                                    <th>'.lang('Merchant Name').'</th>
                                    <th>'.($type == "deposit" ? lang(ptitle('Deposit Date')) : lang(ptitle('First Deposit'))).'</th>
                                    <th>'.lang('FTD Amount').'</th>
                                    <th>'.lang(ptitle('Total Deposits')).'</th>
                                    <th>'.($type == "deposit" ? lang(ptitle('Deposit Amount')) : lang(ptitle('Deposits Amount'))).'</th>
                                    <th>'.lang('Bonus  Amount').'</th>
                                    <th>'.lang('Volume').'</th>
                                    <th>'.lang('Chargeback Amount').'</th>
                                    <th>'.lang('Withdrawal Amount').'</th>
                                    <th>'.lang('Net Revenue').'</th>
                                    <th>'.lang(ptitle('Trades')).'</th>
                                    <th>'.lang('Commission').'</th>
                                    <th>'.lang('Status').'</th>
                                    <th>'.lang('Notes').'</th>
                            </tr></thead><tfoot></tfoot>
                            <tbody>' . $strReport . '
                        </table>
                    </div>' . getPager();
            theme();
            break;
	
                
	case "changeStatus":
		function_mysql_query("UPDATE payments_details SET status='".$_GET['status']."' WHERE id='".$_GET['details_id']."'",__FILE__,__FUNCTION__); //or die("error");
		die('changed');
		break;
		
            
	case "changeNotes":
		$getNotes = mysql_fetch_assoc(function_mysql_query("SELECT id FROM traders_tag WHERE id='".$_GET['id']."'",__FILE__,__FUNCTION__));
		if ($getNotes['id']) function_mysql_query("UPDATE traders_tag SET notes='".mysql_real_escape_string($_GET['text'])."' WHERE id='".$_GET['id']."'",__FILE__,__FUNCTION__); //or die("error");
			else function_mysql_query("INSERT INTO traders_tag (valid,trader_id,rdate,merchant_id,status,notes) VALUES ('1','".$_GET['trader_id']."','".dbDate()."','".$_GET['merchant_id']."','other','".$_GET['text']."')",__FILE__,__FUNCTION__); //or die("error");
		die('changed');
		break;
	
}

exit;
?>
