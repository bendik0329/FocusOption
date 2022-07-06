<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
require_once('common/global.php');


$lout = !empty($set->SSLprefix) ? $set->SSLprefix : "/admin/";
if (!isAdmin())
    _goto($lout);

$appTable = 'exchange_rates';

if ($_POST['act'] == 'update') {

    if (!empty($db['id']) && (!empty($db['rate']) || $db['rate'] >= 0)) {
        
        $db['rate'] = intval($db['rate']);
        
        if($db['rate'] >= 0 && $db['rate'] <= 100){
            dbAdd($db, $appTable);    
        }else{
            _goto($set->SSLprefix . $set->basepage.'?error=1');    
        }
        
        _goto($set->SSLprefix . $set->basepage.'');
    }
}



$set->breadcrumb_title = lang('Exchange Rates');
$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="' . $set->SSLprefix . 'admin/">' . lang('Dashboard') . '</a></li>
			<li><a href="' . $set->SSLprefix . 'admin/exchange_rates.php" class="arrow-left">' . lang('Exchange Rates') . '</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';


$qq = function_mysql_query("SELECT * FROM ".$appTable." ORDER BY toCurr ASC", __FILE__);


while ($ww = mysql_fetch_assoc($qq)) {
    $l++;
    $listReport .= '<tr ' . ($l % 2 ? 'class="trLine"' : '') . '>
                            <td>' . $ww['fromCurr'] . ' / ' .  $ww['toCurr'] . '</td>
                            <td>' . $ww['lastUpdate'] . '</td>
                            <td>' . $ww['val'] . ' ' . (!empty($ww['rate'])?'<br>('.($ww['val'] * (1 - $ww['rate']/100)).')':'') . '</td>
                            <td>
                            <form action="/admin/exchange_rates.php" method="post">
                                <input type="hidden" name="act" value="update" />
                                <input type="hidden" name="db[id]" value="' . $ww['id'] . '" />
                                <input type="text" name="db[rate]" value="' . $ww['rate'] . '" />%
                                <input type="submit" value="' . lang('Update') . '" />
                            </form>        
                            </td>
                    </tr>';
}

$set->content .= '<div class="normalTableTitle">' . lang('Exchange Rates') . '</div>';
$set->content .= '        
    <div style="background: #F8F8F8;">
    '.((!empty($_GET['error']))?'<span style="color: red;display: block; text-align: center;padding: 20px;">Error: The value must be between 0 and 100.</span>':'').'
        <table width="50%" style=" margin: auto;" class="normal" border="0" cellpadding="3" cellspacing="0">
            <thead><tr>
                    <td>' . lang('Currency') . '</td>
                    <td>' . lang('Last Update') . '</td>
                    <td>' . lang('Value') . '</td>
                    <td>' . lang('Rate') . '</td>
            </tr></thead>
            <tfoot>' . $listReport . '</tfoot>

        </table>
    </div>';



$set->content .= '<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/tableExport.js"></script>
		<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/filesaver.js"></script>
		<script type="text/javascript" src="' . $set->SSLprefix . 'js/tableExport/jquery.base64.js"></script>
		<script type="text/javascript" src="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.js"></script>
			<link rel="stylesheet" href="' . $set->SSLprefix . 'js/impromptu/dist/jquery-impromptu.min.css"/>              
		<script src="' . $set->SSLprefix . 'js/autocomplete.js"></script>
			<script>
			$(document).ready(function(){
				$("#combobox").combobox("autocomplete","");
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


theme();
