<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ? ] */
require_once('common/global.php');


$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);
if (!adminPermissionCheck('pendingdeposit')) _goto($lout);

$affiliate_id = isset ($_GET['affiliate_id']) ? $_GET['affiliate_id'] : 0 ;
$merchant_id = isset ($_GET['merchant_id']) ? $_GET['merchant_id'] : 0 ;

$from = strTodate($from);
$to   = strTodate($to);

$from = sanitizeDate($from);
$to   = sanitizeDate($to);

commonGlobalSetTimeRange($from, $to);

switch ($act) {
	
	case "addToExcluded":
			print_r($_POST);die;
		break;
	case "delete":
			
			
	case "update":
			
	case "add":
			
	case "duplicate":
			
	case "valid":
			
			break;
	default:
	
			$where = " WHERE 1 = 1";
			 if(isset($status)){
				if($status == 'pending')
					$where .= ' AND data_sales.pendingRelationRecord =0';
				elseif($status == 'approved')
					$where .= ' AND data_sales.pendingRelationRecord =1';
				else{
					$where .= '';
				}
			}
			
			// All Affiliates.
			if(isset($affiliate_id)){
				$affiliate_id = retrieveAffiliateId($affiliate_id);	
			}

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
			
			//$set->pageTitle = lang('Pending Deposits Approval');
			$set->breadcrumb_title =  lang('Pending Deposits Approval');
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
				<li><a href="'.$set->SSLprefix.'admin/pendingDepositsApproval.php" class="arrow-left">'.lang('Pending Deposits Approval').'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			$set->content .= '
			<script type="text/javascript">
		$(document).ready(function(){
			
			$("ul.vertical li a").on("click",function(){
				var tabtoopen = $(this).data("tab");
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
								txt = $(this).data("tab");
								$("ul.vertical li a").each(function(){
								
									if(txt == $(this).data("tab")){
										$(this).css("color","grey");
									}
								});
							}
							else{
								$(this).hide();
								txt = $(this).data("tab");
								$("ul.vertical li a").each(function(){
									if(txt == $(this).data("tab")){
										$(this).css("color","black");
									}
								});
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
						}
					});
					}
					
				}
			}
		});
		</script>
			';
			$set->content .= '<aside><ul class="vertical">
  <li class="heading_active"><span style="font-size:14px;float:left;width:95px;padding-top:8px;">'. lang('Sections') .'</span></li>
  <li><a href="javascript:void(0)" data-tab="all" class="active">'. lang('All') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="report">'. lang('Report') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="exclude">'. lang('Exclude') .'</a></li>
  <li><a href="javascript:void(0)" data-tab="include">'. lang('Include') .'</a></li>
  </ul></aside>
<div class="main">';
			
			$page = (isset($page) || !empty($page))?$page:1;
			$set->page = $page;
		
			
			$start_limit = $page==1?0:$set->rowsNumberAfterSearch * ($page -1);
			$end_limit = $set->rowsNumberAfterSearch * $page;
			
			$total_recs = $set->rowsNumberAfterSearch;
			
			if(isset($main)){
				$sql = "select count(dp.id) as total_records ,dr.type as traderStatus from  data_sales_pending dp
				inner join data_reg dr on dp.trader_id = dr.trader_id and dp.merchant_id = dr.merchant_id
				
			" . str_replace('data_sales.','dp.', $where)
			. (!empty($merchant_id) ? " and dp.merchant_id = " . $merchant_id :"")
			 . (!empty($affiliate_id) ? ' and dp.affiliate_id = ' . $affiliate_id :'')
			 . (!empty($trader_id) ? ' and dp.trader_id = ' . $trader_id :'')
			 ." and dp.rdate BETWEEN '".$from."' AND '".$to. "' " 
			 . " and dr.status<>'demo' "
			." order by dp.rdate desc";
			}
			else{
			$sql = "select count(ds.id) as total_records, max(ds.trader_id) from (select 'data_sales' as tbl, ds.id, ds.trader_id,ds.trader_alias,ds.affiliate_id,ds.merchant_id,ds.tranz_id,ds.rdate, ds.amount,ds.currency, ds.pendingRelationRecord,ds.created_by_admin_id from data_sales ds  
                            where ds.pendingRelationRecord = 1 AND ds.rdate BETWEEN '".$from."' AND '".$to. "' 
                            UNION 
			select 'data_sales_pending' as tbl, dp.id, dp.trader_id,dp.trader_alias,dp.affiliate_id,dp.merchant_id,dp.tranz_id,dp.rdate, dp.amount,dp.currency, dp.pendingRelationRecord,dp.created_by_admin_id from data_sales_pending dp
                            WHERE dp.rdate BETWEEN '".$from."' AND '".$to. "' 
			) as ds
			inner join data_reg dr on ds.trader_id = dr.trader_id and ds.merchant_id = dr.merchant_id 
			
			" . str_replace('data_sales.','ds.', $where)
			. (!empty($merchant_id) ? " and ds.merchant_id = " . $merchant_id :"")
			 . (!empty($affiliate_id) ? ' and ds.affiliate_id = ' . $affiliate_id :'')
			 . (!empty($trader_id) ? ' and ds.trader_id = ' . $trader_id :'')
			 ." and ds.rdate BETWEEN '".$from."' AND '".$to. "' " 
			 ." and dr.status<>'demo' "
			." order by ds.rdate desc";
			// die ('gre');
			}
			$totalRec = mysql_fetch_assoc(function_mysql_query($sql,__FILE__));
			
			$total_records = $totalRec['total_records'];
			$set->total_records = $total_records; 
		
		
			
			if(isset($main)){
			$sql = "select 'data_sales_pending' as tbl, dp.id,dr.type as traderStatus, dp.trader_id,dp.trader_alias,dp.affiliate_id,dp.merchant_id,dp.tranz_id,dp.rdate, dp.amount,dp.original_amount,dp.currency, dp.pendingRelationRecord,dp.created_by_admin_id from data_sales_pending dp  inner join data_reg dr on dp.trader_id = dr.trader_id and dp.merchant_id= dr.merchant_id 
			
			" . str_replace('data_sales.','dp.', $where)
			. (!empty($merchant_id) ? " and dp.merchant_id = " . $merchant_id :"")
			 . (!empty($affiliate_id) ? ' and dp.affiliate_id = ' . $affiliate_id :'')
			 . (!empty($trader_id) ? ' and dp.trader_id = ' . $trader_id :'')
			 ." and dp.rdate BETWEEN '".$from."' AND '".$to. "' " 
			." order by dp.rdate desc  limit " . $start_limit. ", " . $total_recs;
			// if ($_GET['aa'])
			// die ($sql);
			}
			else{
			$sql = "select dr.rdate as regdate,dr.type as traderStatus, data_sales.* from (
                            select 'data_sales' as tbl, ds.id, ds.trader_id,ds.trader_alias,ds.affiliate_id,ds.merchant_id,ds.tranz_id,ds.rdate, ds.amount,ds.original_amount,ds.currency, ds.pendingRelationRecord,ds.created_by_admin_id from data_sales ds   
                            where ds.pendingRelationRecord = 1 AND ds.rdate BETWEEN '".$from."' AND '".$to. "' 
                            UNION  
                            select 'data_sales_pending' as tbl, dp.id, dp.trader_id,dp.trader_alias,dp.affiliate_id,dp.merchant_id,dp.tranz_id,dp.rdate, dp.amount,dp.original_amount,dp.currency, dp.pendingRelationRecord,dp.created_by_admin_id 
                            from data_sales_pending dp
                            WHERE dp.rdate BETWEEN '".$from."' AND '".$to. "' 
                            ) data_sales inner join data_reg dr on data_sales.trader_id = dr.trader_id and data_sales.merchant_id= dr.merchant_id 
			" . $where
			. (!empty($merchant_id) ? " and data_sales.merchant_id = " . $merchant_id :"")
			 . (!empty($affiliate_id) ? ' and data_sales.affiliate_id = ' . $affiliate_id :'')
			 . (!empty($trader_id) ? ' and data_sales.trader_id = ' . $trader_id :'')
			 
			 ." and data_sales.rdate BETWEEN '".$from."' AND '".$to. "' " 
			." order by data_sales.rdate desc  limit " . $start_limit. ", " . $total_recs;
			
			}
			 //die ($sql);
			$qq = function_mysql_query($sql,__FILE__);
			$i=0;
			
			while($ww = mysql_fetch_assoc($qq)){
			$newfromdate = $ww['regdate'];// date("Y/m/d", strtotime($from);
				$link =  "";
				$type = "";
				if( $ww['tbl'] == 'data_sales'){
					 $link = lang('Convert to pending');
					 $type = 'pending';
					 $current_status = "approved";
				}
				else{
					 $link = lang('Convert to approved');
					 $type = 'approved';
					 $current_status = "pending";
				}
					$listRecords .= "<tr ".($i % 2 ? "class='trLine'" : '') .">
					<td align='center'>". $ww['id'] ."</td>
					<td align='center'>". $ww['merchant_id'] ."</td>
					<td align='center'><a href='".$set->SSLprefix."admin/affiliates.php?act=new&id=".$ww['affiliate_id']."' target='_blank'>". $ww['affiliate_id'] ."</a></td>
					<td align='center'><a href='".$set->SSLprefix."admin/reports.php?act=trader&from=".$newfromdate."&to=".date("Y/m/d", strtotime($to))."&trader_id=". $ww['trader_id'] ."'>". $ww['trader_id'] ."</a></td>
					<td align='center'>". ucwords($ww['traderStatus']) ."</td>
					<td align='center'>". $ww['tranz_id'] ."</td>
					<td align='center'>". $ww['rdate'] ."</td>
					<td align='center'><input type='text' value=". $ww['amount'] ." name='amount_" . $ww['id'] ."' id='amount_" . $ww['id'] ."' class='amt'/></td>
					<td align='center' ". (!empty($ww['original_amount'])?"style='background-color:#ffff9e'":"") ."><span class='original_amount_" . $ww['id'] ."' >". $ww['original_amount'] ."</span></td>
					<td align='center'>". $ww['currency'] ."</td>
					<td align='center'>". $ww['created_by_admin_id'] ."</td>
					<td align='center' style='text-transform:capitalize'>". $current_status ."</td>
					<td align='center'><a href='#' class='convert' data-type ='". $type ."' data-transid ='". $ww['id'] ."'>". $link ."</a></td>
					</tr>";
					//fix for amount in excel
					$listRecords2 .= "<tr ".($i % 2 ? "class='trLine'" : '') .">
					<td align='center'>". $ww['id'] ."</td>
					<td align='center'>". $ww['merchant_id'] ."</td>
					<td align='center'><a href='".$set->SSLprefix."admin/affiliates.php?act=new&id=".$ww['affiliate_id']."' target='_blank'>". $ww['affiliate_id'] ."</a></td>
					<td align='center'><a href='".$set->SSLprefix."admin/reports.php?act=trader&from=".$newfromdate."&to=".date("Y/m/d", strtotime($to))."&trader_id=". $ww['trader_id'] ."'>". $ww['trader_id'] ."</a></td>
					<td align='center'>". ucwords($ww['traderStatus']) ."</td>
					<td align='center'>". $ww['tranz_id'] ."</td>
					<td align='center'>". $ww['rdate'] ."</td>
					<td align='center'>".$ww['amount'] ."</td>
					<td align='center'>".$ww['original_amount'] ."</td>
					<td align='center'>". $ww['currency'] ."</td>
					<td align='center'>". $ww['created_by_admin_id'] ."</td>
					<td align='center'>". $current_status ."</td>
					<td align='center'>". $link ."</td>
					</tr>";
					$i++;
			}
			$set->content .= "<style>
			.convert{
				cursor:pointer;
			}
			</style>
			<script>
			jQuery(document).ready(function($){
				//$('.convert').unbind();
				$('.convert').on('click',function(e){
					e.preventDefault();
					var type=$(this).data('type');
					var id = $(this).data('transid');
					var amount = $('#amount_' + id).val();
					var original_amount = $('#amount_' + id).val();
					if(amount ==''){
					$('input#amount_' + id).css('borderColor','red');
						return false;
					}
					$.get('".$set->SSLprefix."ajax/pendingApprovedDateSales.php' , 
								   {
									   data_sale_type: type,
									   rowid : id,
									   admin_id:". $set->userInfo['id'] .",
									   amount : amount  
								   },
								   function(res){
									   if(res == 1){
										   window.location.href=window.location.href;
									   }
									   else
									   {
										   convertTo = type == '" . lang('pending')."'?'".lang('approved')."':'".lang('pending')."';
										   alert('".lang('There is a problem in converting this record to ')."' + convertTo);
									   }
						});
				});
				
				$('input[name=trader_id]').on('keyup',function(){
						if($(this).val()!=''){
							$('#date_from').val('". date("Y/m/d",strtotime('-100 year')) ."');
							$('#date_to').val('". date("Y/m/d",strtotime('+100 year')) ."');
						}
						else{
							$('#date_from').val('". date("Y/m/d") ."');
							$('#date_to').val('". date("Y/m/d") ."');
						}
					});
			});
			</script>
			
			";
			
			$set->content .= '<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
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
					<div id="report" data-tab="report" class="config_tabs" style="padding-bottom:20px;">
					<div class="normalTableTitle" style="width: 100%;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->SSLprefix.$set->basepage.'" method="get">
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Period').'</td>
					<td>'.lang('Merchant').'</td>
					<td width=160>'.lang('Affiliate ID').'</td>
					<td style="padding-left:10px">'.lang('Trader ID').'</td>
					<td>'.lang('Status').'</td>
					<td></td>
				</tr><tr>
					<td>
						'.timeFrame($from,$to).'
					</td>
					<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
					<td><!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" /--><div class="ui-widget">'
								. '<!-- name="affiliate_id" -->'
								. '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div></td>
					<td style="padding-left:10px"><input type="text" name="trader_id" value="'.$trader_id.'" /></td>
					<td><select name="status" style="width: 150px;">
						<option value="">'.lang('Show All Records').'</option>
						<option value="pending" '.($status == "pending" ? 'selected' : '').'>'.lang('Pending').'</option>
						<option value="approved" '.($status == "approved" ? 'selected' : '').'>'.lang('Approved').'</option>
					</select></td>
					<td><input type="submit" value="'.lang('View').'" /></td>
				</tr>
			</table>
			</form>
			'.($set->export ? '<div class="exportCSV" style="float:left"><a href="'.$set->SSLprefix.$set->uri.(strpos($set->SSLprefix.$set->uri,'?') ? '&' : '?').'excel=csv"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>
				<!--div class="exportCSV" style="float:left"><a href="'.$set->SSLprefix.$set->uri.(strpos($set->SSLprefix.$set->uri,'?') ? '&' : '?').'excel=xls"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div--><div style="clear:both"></div>' : '').'
			</div>';
					$set->content .= '<br/><div class="normalTableTitle">'.lang('Report Results').'</div>';
						$tableStr = '<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
							<thead><tr>
								<th>ID</th>
								<th align="center">'.lang('Merchant ID').'</th>
								<th align="center">'.lang('Affiliate ID').'</th>
								<th align="center">'.lang('Trader ID').'</th>
								<th align="center">'.lang('Trader Status').'</th>
								<th align="center">'.lang('Transaction ID').'</th>
								<th align="center">'.lang('Transaction Date').'</th>
								<th align="center">'.lang('Amount').'</th>
								<th align="center">'.lang('Original Amount').'</th>
								<th align="center">'.lang('Currency').'</th>
								<th align="center">'.lang('Last Admin Handled').'</th>
								<th align="center">'.lang('Status').'</th>
								<th align="center">'.lang('Actions').'</th>
							</tr></thead><tbody>'.$listRecords.'
						</tbody></table>';
					
					$excelTableStr =  '<table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
							<thead><tr>
								<th>ID</th>
								<th align="center">'.lang('Merchant ID').'</th>
								<th align="center">'.lang('Affiliate ID').'</th>
								<th align="center">'.lang('Trader ID').'</th>
								<th align="center">'.lang('Trader Status').'</th>
								<th align="center">'.lang('Transaction ID').'</th>
								<th align="center">'.lang('Transaction Date').'</th>
								<th align="center">'.lang('Original Amount').'</th>
								<th align="center">'.lang('Currency').'</th>
								<th align="center">'.lang('Last Admin Handled').'</th>
								<th align="center">'.lang('Actions').'</th>
							</tr></thead><tbody>'.$listRecords2.'
						</tbody></table>';
				
		excelExporter($excelTableStr, 'PendingDepositsApproval');
		$set->content.=$tableStr.'
		<br/><br/>'.getURLPager() . "</div><br><br>";
		
		//exclude affiliates list
		$sql = "select id, username from affiliates where pendingDepositExclude=1 and valid =1";
		$qqExclude = function_mysql_query($sql);
		$affList = "";
		$l=0;
		while($wwExclude = mysql_fetch_assoc($qqExclude)){
			
			$affList .= '<tr  '.($l % 2 ? 'class="trLine"' : '').' id = "'. $wwExclude['id'] .'">
			<td align="center">'. $wwExclude['id'] .'</td>
			<td align="center">'. $wwExclude['username'] .'</td>
			<td align="center"><a href="javascript:void(0)" data-id="'. $wwExclude['id'] .'" class="removeAff">'. lang('Remove') .'</a></td>
			</tr>';
			$l++;
		}
		
		//exclude affiliates list
		$qqInclude = function_mysql_query("select id, username from affiliates where pendingDepositInclude=1 and valid =1");
		$affListInclude = "";
		$l=0;
		while($wwInclude = mysql_fetch_assoc($qqInclude)){
			
			$affListInclude .= '<tr  '.($l % 2 ? 'class="trLine"' : '').' id = "'. $wwInclude['id'] .'">
			<td align="center">'. $wwInclude['id'] .'</td>
			<td align="center">'. $wwInclude['username'] .'</td>
			<td align="center"><a href="javascript:void(0)" data-id="'. $wwInclude['id'] .'" class="removeAffInclude">'. lang('Remove') .'</a></td>
			</tr>';
			$l++;
		}
		
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
                <table width="100%">
                <tr>
                    <td width="49%" valign="top">
                    
                        <div id="exclude" data-tab="exclude" class="config_tabs">
		<div class="normalTableTitle" style="width: 100%;">'.lang('Exclude').'</div>
		<br/>
		<div class="btn btnAddNew">'. lang('Add New') .'</div>
		<div id="myModal" class="modal">
							
							<input type="hidden" name="act" val="addToExcluded">
						  <!-- Modal content -->
						  <div class="modal-content" style="width:30%">
							<div class="modal-header">
							  <span class="close">&times;</span>
							  <h2>'. lang('Add Affiliate To Excludes List') .  '</span></h2>
							</div>
							<div class="modal-body">
								<p class="err"></p>
							  <p>'. lang('Please select affiliate:') .'</p>
								<p>
								<!--input type="text" name="affiliate_id1" id="affiliate_id1" style="width: 100px;" /-->
								<div class="ui-widget">'
								. '<!-- name="affiliate_id1" -->'
								. '<select id="combobox1">'
								. '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
								. $listOfAffiliates
								.'</select>
								</div>
								</p>
							  <p>
							  <input style="float:right" type="button" id="saveAffiliateToExcludes" name="saveAffiliateToExcludes" value="'. lang('Save') . '">&nbsp;&nbsp;
							  </p>
							  
							</div>

						  </div>
						
						</div>

		<div class="normalTableTitle" style="width: 100%;">'.lang('Excludes List').'</div>
		<div class="err_exclude" style="padding:15px;color:red;display:none"></div>
		<table class="normal excludesList" width="100%" border="0" cellpadding="3" cellspacing="0">
				<thead><tr>
					<th align="center">'.lang('Affiliate ID').'</th>
					<th align="center">'.lang('Affiliate Name').'</th>
					<th align="center">'.lang('Action').'</th>
					</tr></thead><tbody>'.$affList.'
		</tbody></table>

		</div>
                    </td>
                    <td>&nbsp;</td>
                    <td width="49%" valign="top">
                        <div id="include" data-tab="include" class="config_tabs">
                            <div class="normalTableTitle" style="width: 100%;">'.lang('Include').'</div>
                            <br/>
                            <div class="btn btnAddNewInclude">'. lang('Add New') .'</div>
                            <div id="myModalInclude" class="modal">

                                <input type="hidden" name="act" val="addToIncluded">
                                  <!-- Modal content -->
                                  <div class="modal-content" style="width:30%">
                                        <div class="modal-header">
                                          <span class="close close2">&times;</span>
                                          <h2>'. lang('Add Affiliate To Include List') .  '</span></h2>
                                        </div>
                                        <div class="modal-body">
                                                <p class="err"></p>
                                                <p>'. lang('Please select affiliate:') .'</p>
                                                <p>
                                                <!--input type="text" name="affiliate_id1" id="affiliate_id1" style="width: 100px;" /-->
                                                <div class="ui-widget">'
                                                . '<!-- name="affiliate_id1" -->'
                                                . '<select id="combobox2">'
                                                . '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
                                                . $listOfAffiliates
                                                .'</select>
                                                </div>
                                                </p>
                                          <p>
                                          <input style="float:right" type="button" id="saveAffiliateToIncludes" name="saveAffiliateToIncludes" value="'. lang('Save') . '">&nbsp;&nbsp;
                                          </p>

                                        </div>

                                  </div>

                            </div>

                            <div class="normalTableTitle" style="width: 100%;">'.lang('Includes List').'</div>
                            <div class="err_include" style="padding:15px;color:red;display:none"></div>
                            <table class="normal includesList" width="100%" border="0" cellpadding="3" cellspacing="0">
                                            <thead><tr>
                                                    <th align="center">'.lang('Affiliate ID').'</th>
                                                    <th align="center">'.lang('Affiliate Name').'</th>
                                                    <th align="center">'.lang('Action').'</th>
                                                    </tr></thead><tbody>'.$affListInclude.'
                            </tbody></table>

                        </div>
                        
                    </td>
                </tr>
                </table>
		<script type="text/javascript">
			//Code for displaying the modal on all reports to display/hide fields
							
						var modal = document.getElementById("myModal");
                    window.onclick = function(event) {
                            if (event.target == modal) {
                                    modal.style.display = "none";
                            }
                    }
                    $(".btnAddNew").on("click",function(){
                                    modal.style.display = "flex";
                    });

						var span = document.getElementsByClassName("close")[0];
						if(typeof span != "undefined"){
							span.onclick = function() {
								modal.style.display = "none";
							}
						}

                    var modal2 = document.getElementById("myModalInclude");

						window.onclick = function(event) {
                            if (event.target == modal2) {
                                    modal2.style.display = "none";
							}
						}
                    $(".btnAddNewInclude").on("click",function(){
                                    modal2.style.display = "flex";
						});
                    
                    var span2 = document.getElementsByClassName("close2")[0];
                    if(typeof span2 != "undefined"){
                            span2.onclick = function() {
                                    modal2.style.display = "none";
                            }
                    }
						
		$(document).ready(function(){
			 $( "#combobox1" ).combobox();
                         $( "#combobox2" ).combobox();
			$(".removeAff").on("click",function(){
                            if(confirm()){
					affId = $(this).data("id");
					
					$.post("'.$set->SSLprefix.'ajax/pendingDepositExclude.php",{affiliate_id:affId},function(){
						$("table.excludesList tr#"+ affId).remove();
						
						$(".err_exclude").html("'. lang("Affiliate removed from Excludes list.") .'").show();
						setTimeout(function(){
							$(".err_exclude").hide();
						},4000);
					});
                            }
					
			});
                        
                        $(".removeAffInclude").on("click",function(){
                            if(confirm()){
                                affId = $(this).data("id");

                                $.post("'.$set->SSLprefix.'ajax/pendingDepositInclude.php",{affiliate_id:affId},function(){
                                        $("table.includesList tr#"+ affId).remove();

                                        $(".err_include").html("'. lang("Affiliate removed from Included list.") .'").show();
                                        setTimeout(function(){
                                                $(".err_include").hide();
                                        },4000);
                                });
                            }
					
			});
			
			$("#saveAffiliateToExcludes").on("click",function(){
				
				var intCurrentAffiliate = $("input[name=\'affiliate_id\']:eq(1)").val();
				
				if(intCurrentAffiliate == "")
				{
					$(".err").html("'.lang('Please select affiliate.').'");
					return;
				}
				else{
					$.post("'.$set->SSLprefix.'ajax/pendingDepositExclude.php",{affiliate_id:intCurrentAffiliate,affiliate_exclude:1},function(res){
						console.log("reload");
						window.location.href=window.location.href;
					});
				}
				
			});
                        $("#saveAffiliateToIncludes").on("click",function(){
				
                                var intCurrentAffiliate = $("#myModalInclude input:eq(1)").val();

				if(intCurrentAffiliate == "")
				{
					$(".err").html("'.lang('Please select affiliate.').'");
					return;
				}
				else{
					$.post("'.$set->SSLprefix.'ajax/pendingDepositInclude.php",{affiliate_id:intCurrentAffiliate,affiliate_include:1},function(res){
						console.log("reload");
						window.location.href=window.location.href;
					});
				}
				
			});
		});
		</script>
		';
		
		$set->content .= '</div>';//MAIN div closed
		theme();
		break;
	}

?>
