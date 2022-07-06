<?php

ini_set('max_execution_time', 10);

//Prevent direct browsing of report
$userlevel = $set->userInfo['level'];
if(!defined('DirectBrowse')) {
	$path = "http".$set->SSLswitch."://" . $_SERVER[HTTP_HOST];
	header("Location: " .  $path . "/" . $userlevel );
}

$pageTitle = lang('Commissions Debts Report');
$set->breadcrumb_title =  lang($pageTitle);
$set->pageTitle = '
        <ul class="breadcrumb">
            <li><a href="'.$set->SSLprefix.$userlevel.'/">'.lang('Dashboard').'</a></li>
            <li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
            <li><a style="background:none !Important;"></a></li>
        </ul>';
		
$set->content .= '
        <script type="text/javascript" src="'.$set->SSLprefix.'js/tableExport/tableExport.js"></script>
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
		</style>
		';

// filename to export
$filename = "Commissions_Debts_data_" . date('YmdHis');
	

$listReport = '';

$currentYear = date("Y");
$year = isset($year) ? $year : $currentYear;
$month = isset($month) ? $month : '';

$merchant_id = (isset($merchant_id) && $merchant_id > 0) ? $merchant_id : 0;
$merchantsA = getMerchants($merchant_id, 1);


// data					
$commissionArray = array();

foreach ($merchantsA as $ww) {

    $merchantName = strtolower($ww['name']);
    $merchantID = $ww['id'];
  

    $sql_commissions = "SELECT tcd.*, ta.username, tm.name as merchant_name FROM commissions_debts tcd "
                    . " LEFT JOIN affiliates ta ON tcd.affiliate_id = ta.id "
                    . " LEFT JOIN merchants tm ON tcd.merchant_id = tm.id "
                    . " WHERE tcd.merchant_id = '" . $ww['id'] . "' "
                    . (isset($year) && $year != '' ? ' AND tcd.year = ' . $year . ' ' : '')
                    . (isset($month) && $month != '' ? ' AND tcd.month = ' . $month . ' ' : '')
                    . (isset($affiliate_id) && $affiliate_id != '' ? ' AND tcd.affiliate_id = ' . $affiliate_id . ' ' : '')
                    . " ORDER BY tcd.year, tcd.month DESC"
                    ;
    
    
    $commissionArrayResult = function_mysql_query($sql_commissions);
    while ($commissionResultItem = mysql_fetch_assoc($commissionArrayResult)) {
        $commissionArray[] = $commissionResultItem;
    }

    
}

$l=0;
foreach ($commissionArray as $key => $com) {
    $listReport .= '
        <tr>
            <td style="text-align: left;">'.$com['merchant_name'].'</td>
            <td style="text-align: left;">'.$com['merchant_id'].'</td>
            <td style="text-align: left;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$com['affiliate_id'].'" target="_blank">'.$com['affiliate_id'].'</a></td>
            <td style="text-align: left;"><a href="'. $set->SSLprefix.$userlevel .'/affiliates.php?act=new&id='.$com['affiliate_id'].'" target="_blank">'.$com['username'].'</a></td>
            <td style="text-align: left;">'.$com['year'].'</a></td>
            <td style="text-align: left;">'.$com['month'].'</td>
            <td style="text-align: left;">'.price($com['commissions']).'</td>
        </tr>';

    $l++;
}

// set up sort table scripts !!
if ($l > 0) {
    $set->sortTableScript = 1;
}
$set->sortTable = 1;
$set->totalRows = $l;

// content
$set->content .= '
<div class="normalTableTitle" style="width: 99.5%;">'.lang('Report Search').'</div>
    <div style="background: #F8F8F8;">
    <form id="frmRepo" method="get" onsubmit = "return submitReportsForm(this);">
        <input type="hidden" name="act" value="commissions_debts" />
        <table>
            <tr>
                <td>'.lang('Year').'</td>
                <td>'.lang('Month').'</td>
                <td>'.lang('Merchant').'</td>
                '.($userlevel == "admin" || $userlevel == "manager" ? '<td width=160>'.lang('Affiliate ID').'</td>':'').'
                <td></td>
            </tr>
            <tr>
                <td><select name="year" style="width: 80px;">
                <option '.($year==$currentYear ? ' selected ' : '').'value="'.$currentYear.'">'.$currentYear.'</option>'.
                '<option '.($year==$currentYear-1 ? ' selected ' : '').'value="'.($currentYear-1).'">'.($currentYear-1).'</option>'.
                '<option '.($year==$currentYear-2 ? ' selected ' : '').'value="'.($currentYear-2).'">'.($currentYear-2).'</option>'.
                '<option '.($year==$currentYear-3 ? ' selected ' : '').'value="'.($currentYear-3).'">'.($currentYear-3).'</option>'.
                '<option '.($year==$currentYear-4 ? ' selected ' : '').'value="'.($currentYear-4).'">'.($currentYear-4).'</option>'.
                '<option '.($year==$currentYear-5 ? ' selected ' : '').'value="'.($currentYear-5).'">'.($currentYear-5).'</option>'.
                '<option '.($year==$currentYear-6 ? ' selected ' : '').'value="'.($currentYear-6).'">'.($currentYear-6).'</option>'.
                '</select></td>
                <td><select name="month" style="width: 50px;">
                <option '.($month=='' ? ' selected ' : '').'value="">'.lang('All').'</option>'.
                '<option '.($month=='1' ? ' selected ' : '').'value="1">'.lang('Jan').'</option>'.
                '<option '.($month=='2' ? ' selected ' : '').'value="2">'.lang('Feb').'</option>'.
                '<option '.($month=='3' ? ' selected ' : '').'value="3">'.lang('Mar').'</option>'.
                '<option '.($month=='4' ? ' selected ' : '').'value="4">'.lang('Apr').'</option>'.
                '<option '.($month=='5' ? ' selected ' : '').'value="5">'.lang('May').'</option>'.
                '<option '.($month=='6' ? ' selected ' : '').'value="6">'.lang('Jun').'</option>'.
                '<option '.($month=='7' ? ' selected ' : '').'value="7">'.lang('Jul').'</option>'.
                '<option '.($month=='8' ? ' selected ' : '').'value="8">'.lang('Aug').'</option>'.
                '<option '.($month=='9' ? ' selected ' : '').'value="9">'.lang('Sep').'</option>'.
                '<option '.($month=='10' ? ' selected ' : '').'value="10">'.lang('Oct').'</option>'.
                '<option '.($month=='11' ? ' selected ' : '').'value="11">'.lang('Nov').'</option>'.
                '<option '.($month=='12' ? ' selected ' : '').'value="12">'.lang('Dec').'</option>'.
                '</select></td>
                <td><select name="merchant_id" style="width: 150px;"><option value="0">'.lang('All').'</option>'.listMerchants($merchant_id).'</select></td>
                <td>
                    <!--input type="text" name="affiliate_id" value="'.$affiliate_id.'" id="affiliate_id" style="width: 60px; text-align: center;" /-->
                    <div class="ui-widget">'
                    . '<!-- name="affiliate_id" -->'
                    . '<select id="combobox" '. ($errors['affiliate_id'] ? 'style="border: 1px red solid;"' : '') .'>'
                    . '<!--option value="">'.lang('Choose Affiliate').'</option-->' 
                    . $listOfAffiliates
                    .'</select>
                    </div>
                </td>
                <td><input type="submit" value="'.lang('View').'" /></td>
            </tr>
        </table>
    </form>
    '.($set->export ? '<div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#commissionData\').tableExport({type:\'csvbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to CSV').'" title="'.lang('Export to CSV').'" align="absmiddle" /> <b>'.lang('Export to CSV').'</b></a></div>':'').'
    <div class="exportCSV" style="float:left"><a style="cursor:pointer" onclick="$(\'#commissionData\').tableExport({type:\'excelbig\',escape:\'false\',tableName:\''.  $filename .'\'});"><img border="0" src="'.$set->SSLprefix.'images/excel.png" alt="'.lang('Export to XLS').'" title="'.lang('Export to XLS').'" align="absmiddle" /> <b>'.lang('Export to XLS').'</b></a>
    </div>
    '. getFavoritesHTML() .'
    <div style="clear:both"></div>
</div>
<div style="height:20px;"></div>
<div class="normalTableTitle" class="table">'.lang('Commissions Debts Report').'<span style="float:right"><img class="imgReportFieldsSettings" style="padding-top:6px;width:55%;cursor:pointer;" src="'.$set->SSLprefix.'images/settings.png"/></span></div>
<div style="background: #F8F8F8;">';
//style="width: 99.5%;"

$tableStr = '
    <table class="table tablesorter mdlReportFields" border="0" cellpadding="0" cellspacing="0" id="commissionTbl">
        <thead>
            <tr class="table-row">
                <th class="table-cell">'. lang('Merchant Name') .'</th>
                <th class="table-cell">'. lang('Merchant ID') .'</th>
                <th class="table-cell">'. lang('Affiliate ID') .'</th>
                <th class="table-cell">'. lang('Affiliate Name') .'</th>
                <th class="table-cell">'. lang('Year') .'</th>
                <th class="table-cell">'. lang('Month') .'</th>
                <th class="table-cell">'. lang('Commissions') .'</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>        
        <tbody>
        '.$listReport.'
    </table>

    <script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
    <link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>   
    <script>
        $(document).ready(function(){
            try{
            thead = $("thead").html();
            tfoot = $("tfoot").html();
            txt = "<table id=\'commissionData\' class=\'mdlReportFieldsData\'>";
            txt += "<thead>" + thead + "</thead>";
            txt += "<tbody>";
            $($("#commissionTbl")[0].config.rowsCopy).each(function() {
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
                                    
                                    saveReportToMyFav(name, \'commissions_debts\',user,level,type);
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

//$tableStr .= getSingleSelectedMerchant();
$set->content .= $tableStr . '</div>' . getPager();

//MODAL
$myReport = lang("Commissions Debts");
include "common/ReportFieldsModal.php";


theme();
		
?>