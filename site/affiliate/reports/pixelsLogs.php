<?php
//Prevent direct browsing of report
// $userlevel = $set->userInfo['level'];
$userlevel = 	"affiliate";
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}
$affiliate_id = $set->userInfo['id'];


$netdeposit = isset($_GET['netdeposit']) ? $_GET['netdeposit'] : 0;

$countriesLong = getLongCountries();
 
	$set->breadcrumb_title =  lang(ptitle('Pixel Logs Report'));
	$set->pageTitle = '
	<style>
	.pageTitle{
		padding-left:0px !important;
	}
	</style>
	<ul class="breadcrumb">
		<li><a href="'.$set->SSLprefix.$userlevel.'/">'.lang('Dashboard').'</a></li>
		<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang(ptitle('Pixel Logs Report')).'</a></li>
		<li><a style="background:none !Important;"></a></li>
	</ul>';
		$set->content .= '<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/jquery.base64.js"></script>
		<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>              
		<script src="'.$set->SSLprefix.'js/autocomplete.js"></script>
			<script>
			$(document).ready(function(){
				$("#combobox").combobox("autocomplete","'. $_GET['affiliate_id'] .'");
			});
			</script>
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
		$filename = "pixelLogs_data_" . date('YmdHis');
		
			
          
                
            
					
					if ($userlevel=='manager'){
						$group_id = $set->userInfo['group_id'];
						if ($group_id) $where .= " AND group_id='".$group_id."' ";
					}
					else  {
						if ($group_id) $where .= " AND group_id='".$group_id."' ";
					}
						
                    if ($affiliate_id) $where .= " AND affiliate_id='".$affiliate_id."' ";
                    if ($banner_id) $where .= " AND banner_id='".$banner_id."' ";
                    if ($profile_id) $where .= " AND profile_id='".$profile_id."' ";
                    if ($trader_id) $where .= " AND trader_id='".$trader_id."' ";
					if ($param) $where .= " AND freeParam='".$param."' ";
					if ($param2) $where .= " AND freeParam2='".$param2."' ";
					if (!empty($merchant_id)) $where .= " AND merchant_id='".$merchant_id."' ";
					// if ($hidedemo) $where .= " AND not type='demo' ";
                    
               
		    	
                    if ($country_id) {
                        $where .= " AND country='".$country_id."' ";
                    }
                    
                    $ftd = $totalTraders = $depositAmount = $total_deposits = $ftdAmount = $volumeAmount = 0;
                    $totalCom = $bonusAmount  = $withdrawalAmount = $chargebackAmount = $revenueAmount = 0;
                    $spreadAmount = $pnl = 0;
                    $ftdUsers = '';
		    
			
			
			
	
							$where = str_replace('merchant_id', 'pm.merchant_id', $where);
                            // $where = str_replace('trader_id', 'dr.trader_id', $where);
                            $where = str_replace('group_id', 'af.group_id', $where);
                            $where = str_replace('affiliate_id', 'pm.affiliate_id', $where);
                            $where = str_replace('banner_id', 'pm.banner_id', $where);
                            // $where = str_replace('trader_alias', 'dr.trader_alias', $where);
                            // $where = str_replace('type', 'dr.type', $where);
                            // $where = str_replace('country', 'dr.country', $where);
                   
				   
				   
				
				   
				   
							$sql = "SELECT pl.id as plid,
							pm.* , pl.*,af.username, mr.name , af.group_id , mr.id , af.id as affiliate_id
							
							
							FROM pixel_logs AS pl
                                  
								  
								  left join pixel_monitor pm on 
								  pm.id = pl.pixelCode								  
								  	  
								  left join merchants mr on 
								  pm.merchant_id = mr.id
								  
								  
								    
								  left join affiliates af on 
								  pm.affiliate_id = af.id 
								  
							
                                    WHERE 2=2 and " . $globalWhere 
                                            . " pl.dateTime BETWEEN '" . $from . "' AND '" . $to . "' "
                                            . " AND pm.merchant_id >0 "
                                            
                                            .$whereType
											
                                            . $where
											
									
                                    . " ORDER BY pl.dateTime ASC;";
									
							//}
		
				
			
                            $resource = function_mysql_query($sql,__FILE__);
               while ($arrRes = mysql_fetch_assoc($resource)) {
			    $listReport .= '
                        <tr>
                            <td>'.$arrRes['plid'].'</td>
                            <td>'.$arrRes['dateTime'].'</td>
                            <td>'.ucwords($arrRes['type']).'</td>
                            <td>'.strtoupper($arrRes['method']).'</td>
                            <td>'.$arrRes['firedUrl'].'</td>
                            <td>'.$arrRes['pixelResponse'].'</td>
                            <td>'.$arrRes['totalFired'].'</td>
                            <td>'.($arrRes['valid']==1 ? lang('Active'): lang('Blocked')).'</td>
                            <td>'.$arrRes['affiliate_id'].'</td>
							<td><a href="/'. $userlevel .'/affiliates.php?act=new&id='.$arrRes['affiliate_id'].'" target="_blank">'.$arrRes['username'].'</a></td>
                            <td>'.$arrRes['merchant_id'].'</td>
                            <td>'.$arrRes['name'].'</td>
                            <td>'.$arrRes['product_id'].'</td>
                            <td>'.$arrRes['banner_id'].'</td>
                            <td>'.$arrRes['group_id'].'</td>
                        </tr>';
                    }
                        
			   
		if ($l > 0) {
                    $set->sortTableScript = 1;
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
			  height: 20px;
			 width: 43px;
			}

			.slider:before {
			  position: absolute;
			  content: "";
			  height: 12px;
			  width: 12px;
			  left: 3px;
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
                $set->sortTable = 1;
		$set->totalRows = $l;
		$set->content .= '<div class="normalTableTitle"style="width: 1600px;">'.lang('Report Search').'</div>
			<div style="background: #F8F8F8;">
				<form id="frmRepo" action="'.$set->SSLprefix.$set->basepage.'" method="get" onsubmit = "return submitReportsForm(this)">
				<input type="hidden" name="act" value="pixelsLogs" />
				
				<table border="0" cellpadding="1" cellspacing="1">
					<tr>
						<td style="min-width:360px;">'.lang('Period').'</td>
						<td>'.lang('Merchant').'</td>
						<td>'.lang('Country').'</td>
						
						<td style="padding-left:20px">'.lang('Banner ID').'</td>
						
						'.($userlevel=='manager' ? '' : '<td>'.lang('Group').'</td>').'
						
						<td></td>
					</tr>
					
					<tr>
						<td>'.timeFrame($from, $to).'</td>
						<td><select name="merchant_id" style="width: 150px;"><option value="">'.lang('All Merchants').'</option>'.listMerchants($merchant_id).'</select></td>
						
						<td><select name="country_id" style="width: 150px;"><option value="">'.lang('All').'</option>'.getCountries($country_id).'</select></td>
						
						<td style="padding-left:20px"><input type="text" name="banner_id" value="'.$banner_id.'" id="fieldClear" style="width: 60px; text-align: center;" /></td>
						
						'.($userlevel=='manager' ? '' : '
						<td><select name="group_id">
                                                        <option value="">'.lang('All Groups').'</option>'
                                                        . '<option value="0" '.($group_id == "0" ? 'selected="selected"' : '').'>'.lang('General').'</option>' 
                                                        . listGroups($group_id) 
                                                . '</select>
                                                </td> ' ).'
							
							<td><input type="submit" value="'.lang('View').'" /></td>
					</tr>
					
					<tr>

							<td>'.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#pixelsLogsData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
				<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#pixelsLogsData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
				</div>
				'. getFavoritesHTML() .'
				</td><td colspan=8></td>

						
					</tr>
				</table>
				</form>
			</div>
			<div style="height: 20px;"></div>
			<div class="normalTableTitle"   class="table">'.lang('Report Results').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
			<div style="background: #F8F8F8;">';
			//width 2600
				$tableStr ='<table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="pixelsLogsTbl">
					<thead><tr   class="table-row">
						<th class="table-cell">'.lang('Pixel Fire ID').'</th>
						<th class="table-cell">'.lang(('Date')).'</th>
						<th class="table-cell">'.lang(('Type')).'</th>
						<th class="table-cell">'.lang(('Method')).'</th>
						<th class="table-cell">'.lang(('Fired URL')).'</th>
						<th class="table-cell">'.lang(('Response')).'</th>
						<th class="table-cell">'.lang(('All Time Fired')).'</th>
						<th class="table-cell">'.lang(('Pixel State')).'</th>
						<th class="table-cell">'.lang(('Affiliate ID')).'</th>
						<th class="table-cell">'.lang(('Affiliate Username')).'</th>
						<th class="table-cell">'.lang(('Merchant ID')).'</th>
						<th class="table-cell">'.lang(('Merchant')).'</th>
						<th class="table-cell">'.lang(('Product ID')).'</th>
						<th class="table-cell">'.lang(('Banner ID')).'</th>
						<th class="table-cell">'.lang(('Group ID')).'</th>
						
					</tr></thead>
					<tfoot>
					
					</tfoot>
					<tbody>
					'.$listReport.'
				</table>
				<script>
				$(document).ready(function(){
					
					$(".saveReport").on("click",function(){
						$.prompt("<label>'. lang("Provide name for report") .': <br/><input type=\'text\' name=\'report_name\' value=\'\' style=\'width:80wh\' required></label><div class=\'err_message\' style=\'color:red\'></div>", {
								top:200,
								title: "'. lang('Add to Favorites') .'",
								buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
								submit: function(e,v,m,f){
									if(v){
										name = $("[name=report_name]").val();
										if(name != ""){
											
											url = window.location.href;
											user = "'. $set->userInfo['id'] .'";
											level = "'. $userlevel .'";
											type = "add";
											
											saveReportToMyFav(name, \'pixelsLogs\',user,level,type);
										}
										else{
											$(".err_message").html("'. lang("Enter Report name.") .'");
											return false;
										}
									}
									else{
										//
									}
								}
							});
					});
					try{
						thead = $("thead").html();
						tfoot = $("tfoot").html();
						txt = "<table id=\'pixelsLogsData\' class=\'mdlReportFieldsData\'>";
						txt += "<thead>" + thead + "</thead>";
						txt += "<tbody>";
						$($("#pixelsLogsTbl")[0].config.rowsCopy).each(function() {
							txt += "<tr>" + $(this).html()+"</tr>";
						});
						txt += "</tbody>";
						txt += "<tfoot>" + tfoot + "</tfoot>";
						txt += "</table>";
						$("body").append("<div style=\'display:none\'>"+ txt +"</div>");
					}
					catch(e){
						//exception
					}
					$(".saveReport").on("click",function(){
						$.prompt("<label>'. lang("Provide name for report") .': <br/><input type=\'text\' name=\'report_name\' value=\'\' style=\'width:80wh\' required></label><div class=\'err_message\' style=\'color:red\'></div>", {
								top:200,
								title: "'. lang('Add to Favorites') .'",
								buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
								submit: function(e,v,m,f){
									if(v){
										name = $("[name=report_name]").val();
										if(name != ""){
											
											url = window.location.href;
											user = "'. $set->userInfo['id'] .'";
											level = "'. $userlevel .'";
											type = "add";
											
											saveReportToMyFav(name, \'pixelsLogs\',user,level,type);
										}
										else{
											$(".err_message").html("'. lang("Enter Report name.") .'");
											return false;
										}
									}
									else{
										//
									}
								}
							});
					});
				});
				
				</script>
				
				';
				// $tableStr .= getSingleSelectedMerchant();
				// $tableStr .= getValidateTraderMerchantScript();
				$set->content.=$tableStr.'
			</div>'.getPager();
			
			$set->content .='
			<script>
				$("#table_type").change(function(){
						var tbl = $(this).find(":selected").data("table");
						$("#table").val(tbl);
				});
			</script>
			';
			
			//MODAL
		$myReport = lang("pixelsLogs");
		include "common/ReportFieldsModal.php";
			

		theme();

?>