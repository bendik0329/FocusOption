<?php
//ini_set('display_errors', '1');
//ini_set('error_reporting', E_ALL ^ E_NOTICE);

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
/*
function strTodate($date='') {
	if (!$date) return date("Y-m-d");
	$exp=explode("/",$date);
	return $exp[2].'-'.$exp[1].'-'.$exp[0];
	}
*/

/**
 * OLD VERSION
 */
/*function strTodate($date='') {
	if (!$date) return date("Y-m-d H:i:s");
	$exp=explode(" ",$date);
	$exp1=explode("/",$exp[0]);
	//$exp2=explode(":",$exp[1]);
	if($test){
		var_dump($date);
	}
	return $exp1[2].'-'.$exp1[1].'-'.$exp1[0] .' '.$exp[1];
}*/

function strTodate($date = '')
{
	if (empty($date)) {
		return date('Y-m-d H:i:s');
	}
	
	$exp  = explode(' ', $date);
	$exp1 = explode('/', $exp[0]);
	//die(print_r($exp1));//////////////////////////
	return $exp1[2] . '-' . $exp1[1] . '-' . $exp1[0] . ' ' . $exp[1];
}



/**
 * @return string
 */
function createValidDate($str)
{
	return commonGlobalSetYmd($str);
    /*$arrDate = explode(' ', trim($str));
	$strYmd  = $arrDate[0];
	$arrYmd  = explode('-', $strYmd);
	
	$strYear  = $arrYmd[0];
	$strMonth = $arrYmd[1];
	$strDate  = $arrYmd[2];
	
	unset($arrYmd, $strYmd, $arrDate);
	return $strYear . '-' . $strDate . '-' . $strMonth;*/
}


/**
 * A "work around" function, intended to solve the "function timeFrame" problem.
 *
 * @param  string
 * @return string
 */
function createValidDateWorkAround($strInputDate)
{
	$arrDateTime = explode(' ', $strInputDate);
	$strH_i_s    = $arrDateTime[1];
	$strDate     = $arrDateTime[0];
	$arrDate 	 = explode('-', $strDate);
	$strMonth    = '';
	$strYear     = '';
	
	if (5 == count($arrDate)) {
		// Current format is "d-m-Y".
		$strDate  = $arrDate[2];
		$strMonth = $arrDate[3];
		$strYear  = $arrDate[4];
	} else {
		// Current format is "Y-m-d".
		$strYear  = $arrDate[0];
		$strMonth = $arrDate[1];
		$strDate  = $arrDate[2];
	}
	
	//return $strDate . '-' . $strMonth . '-' . $strYear . ' ' . $strH_i_s;
	return $strDate . '-' . $strMonth . '-' . $strYear;
}


/**
 * A "work around" function, intended to solve the "wrong date format ('--' prefix)" problem.
 *
 * @param  string
 * @return string
 *
 */
function sanitizeDate($strDate)
{
	$strDate = trim($strDate);
	return str_replace('--', '', $strDate);
}


/**
 * @return string
 */
function timeFrame($from = '', $to = '') 
{
	global $auto_time_frame, $defTimeFrame;
	
	$from = createValidDateWorkAround($from);
	$to   = createValidDateWorkAround($to);
	
	// ORIGINAL VERSION
	//die($from . '   ' . $to);//////////////////////////////////////////////   2014-12-16 2014-12-22 23:59:59
	//$from = date("d-m-Y", strtotime(createValidDate($from)));
	//$to   = date("d-m-Y", strtotime(createValidDate($to)));
	//die($from . '   ' . $to);//////////////////////////////////////////////   31-12-1969 31-12-1969
	
	$fromDate = date("Y/m/d");
	$toDate = date("Y/m/d");
	$oType1 = '$(\'#date_from\').val(\''.$fromDate.'\');
			$(\'#date_to\').val(\''.$toDate.'\');';
	
	$fromDate = date("Y/m/d", strtotime("-1 Day"));
	$toDate = date("Y/m/d", strtotime("-1 Day"));
	$oType2 = '$(\'#date_from\').val(\''.$fromDate.'\');
			$(\'#date_to\').val(\''.$toDate.'\');';

	$fromDate = date("Y-m-01");
	$toDate = date("Y/m/d",strtotime("-1 Day", strtotime("+1 Month",strtotime($fromDate))));
	$oType3 = '$(\'#date_from\').val(\''.date("Y/m/d",strtotime($fromDate)).'\');
			$(\'#date_to\').val(\''.$toDate.'\');';

	if (date("Y-m-01")==$fromDate  && date("m"==3) ) {
			$fromDate = date("Y-m-01", strtotime("-1 Month -3 Day"));
	}
	else {
		$fromDate = date("Y-m-01", strtotime("-1 Month"));
	}
	
	
	$toDate = date("Y/m/d", strtotime("-1 Day",strtotime("+1 Month",strtotime($fromDate))));
	$oType4 = '$(\'#date_from\').val(\''.date("Y/m/d",strtotime($fromDate)).'\');
			$(\'#date_to\').val(\''.$toDate.'\');';

	$fromDate = date("Y-01-01", strtotime("-1 Day"));
	$toDate = date("Y/m/d", strtotime("+1 Year", strtotime("-1 Day",strtotime($fromDate))));
	$oType5 = '$(\'#date_from\').val(\''.date("Y/m/d",strtotime($fromDate)).'\');
			$(\'#date_to\').val(\''.$toDate.'\');';
			
	$fromDate = date("Y/m/d", strtotime("-1 Week"));
	$toDate = date("Y/m/d");
	$oType6 = '$(\'#date_from\').val(\''.$fromDate.'\');
			$(\'#date_to\').val(\''.$toDate.'\');';
		
	$fromDate = '01/01/'.date("Y", strtotime("-1 Year"));
	$toDate = date("t", strtotime("-1 Year")).'/12/'.date("Y", strtotime("-1 Year"));
	$oType7 = '$(\'#date_from\').val(\''.$fromDate.'\');
			$(\'#date_to\').val(\''.$toDate.'\');';
	
	//baba('atf: '.$auto_time_frame,0,1);
	//die(print_r(array( $from, $to, $auto_time_frame, $defTimeFrame )));
	// Array ( [0] => 12/10/2014 [1] => 31/12/1969 [2] => [3] => ) 
	
	$html = '
		<select id="dateSelect"  name="auto_time_frame" onchange="chgDates(this.value);" style="width: 100px;">
			<option value="1" '.($auto_time_frame == "1" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==1) ? 'selected' : '')).'>'.lang('Today').'</option>
			<option value="2" '.($auto_time_frame == "2" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==2) ? 'selected' : '')).'>'.lang('Yesterday').'</option>
			<option value="6" '.($auto_time_frame == "6" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==6) ? 'selected' : '')).'>'.lang('This Week').'</option>
			<option value="3" '.($auto_time_frame == "3" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==3) ? 'selected' : '')).'>'.lang('Month to date').'</option>
			<option value="4" '.($auto_time_frame == "4" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==4) ? 'selected' : '')).'>'.lang('Last Month').'</option>
			<option value="5" '.($auto_time_frame == "5" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==5) ? 'selected' : '')).'>'.lang('This Year').'</option>
			<option value="7" '.($auto_time_frame == "7" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==7) ? 'selected' : '')).'>'.lang('Last Year').'</option>
			<option value="8" '.($auto_time_frame == "8" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==8) ? 'selected' : '')).'>'.lang('Custom').'</option>
			
		</select>
		<b>'.lang('From').':</b> 
		<input type="text" name="from" value="'.$from.'" id="date_from" style="padding: 3px;" style="width:90px!important;" /> 
		<b>'.lang('To').':</b> 
		<input type="text" name="to" value="'.$to.'" id="date_to" style="padding: 3px;" style="width:90px!important;" />
			
            <script type="text/javascript">
				function chgDates(o) {
					if (o == "1") {
						'.$oType1.'
						} else if (o == "2") {
						'.$oType2.'
						} else if (o == "3") {
						'.$oType3.'
						} else if (o == "4") {
						'.$oType4.'
						} else if (o == "5") {
						'.$oType5.'
						} else if (o == "6") {
						'.$oType6.'
						} else if (o == "7") {
						'.$oType7.'
						} else if (o == "8") {
						'.$oType8.'
						}
				}
				
				/**
				 * @return string
				 */
				function convertYmdToDmy(strDate) {
					var arrDate = strDate.split("-");
					
					if (2 === arrDate[0].length) {
						// Current format is "d-m-Y".
						return strDate;
					} else {
						// Current format is "Y-m-d".
						return arrDate[2] + "/" + arrDate[1] + "/" + arrDate[0];
					}
				}
				
				function convertToDmy(strDate) {
					var arrDate = strDate.split("-");
					
					if (2 === arrDate[0].length) {
						// Current format is "d-m-Y".
						return arrDate[0] + "/" + arrDate[1] + "/" + arrDate[2];
					} else {
						// Current format is "Y-m-d".
						return arrDate[2] + "/" + arrDate[1] + "/" + arrDate[0];
					}
				}
				
				$(function() {
					$("#date_from").val(convertYmdToDmy($("#date_from").val()));
					
					$("#date_to").val(convertYmdToDmy($("#date_to").val()));
					
					$("#date_from").datepicker({
						dateFormat: \'yy/mm/dd\',
						onSelect: function() {
							$("#dateSelect").val(8);
						}
					});
					
					$("#date_to").datepicker({
						dateFormat: \'yy/mm/dd\',
						onSelect: function() {
							$("#dateSelect").val(8);
						}
					});
					$("#date_to,#date_from").keypress(function (e) {
						$("#dateSelect").val(8);
					});
					
					$("#date_from").datepicker("setDate",convertToDmy("'. $from .'"));
					$("#date_to").datepicker("setDate",convertToDmy("'. $to .'"));
					
				});
			</script>
			
			';
			
	return $html;
}

/**
 * @return string
 */
function timeFramequick($from = '', $to = '') 
{
	global $auto_time_frame, $defTimeFrame;
	
	$from = createValidDateWorkAround($from);
	$to   = createValidDateWorkAround($to);
	
	// ORIGINAL VERSION
	//die($from . '   ' . $to);//////////////////////////////////////////////   2014-12-16 2014-12-22 23:59:59
	//$from = date("d-m-Y", strtotime(createValidDate($from)));
	//$to   = date("d-m-Y", strtotime(createValidDate($to)));
	//die($from . '   ' . $to);//////////////////////////////////////////////   31-12-1969 31-12-1969
	
	$fromDate = date("Y/m/d");
	$toDate = date("Y/m/d");
	$oType1 = '$(\'#date_from\').val(\''.$fromDate.'\');
			$(\'#date_to\').val(\''.$toDate.'\');';
	
	$fromDate = date("Y/m/d", strtotime("-1 Day"));
	$toDate = date("Y/m/d", strtotime("-1 Day"));
	$oType2 = '$(\'#date_from\').val(\''.$fromDate.'\');
			$(\'#date_to\').val(\''.$toDate.'\');';

	$fromDate = date("Y-m-01");
	$toDate = date("Y/m/d",strtotime("-1 Day", strtotime("+1 Month",strtotime($fromDate))));
	$oType3 = '$(\'#date_from\').val(\''.date("Y/m/d",strtotime($fromDate)).'\');
			$(\'#date_to\').val(\''.$toDate.'\');';

	if (date("Y-m-01")==$fromDate  && date("m"==3) ) {
			$fromDate = date("Y-m-01", strtotime("-1 Month -3 Day"));
	}
	else {
		$fromDate = date("Y-m-01", strtotime("-1 Month"));
	}
	
	
	$toDate = date("Y/m/d", strtotime("-1 Day",strtotime("+1 Month",strtotime($fromDate))));
	$oType4 = '$(\'#date_from\').val(\''.date("Y/m/d",strtotime($fromDate)).'\');
			$(\'#date_to\').val(\''.$toDate.'\');';

	$fromDate = date("Y-01-01", strtotime("-1 Day"));
	$toDate = date("Y/m/d", strtotime("+1 Year", strtotime("-1 Day",strtotime($fromDate))));
	$oType5 = '$(\'#date_from\').val(\''.date("Y/m/d",strtotime($fromDate)).'\');
			$(\'#date_to\').val(\''.$toDate.'\');';
			
	$fromDate = date("Y/m/d", strtotime("-1 Week"));
	$toDate = date("Y/m/d");
	$oType6 = '$(\'#date_from\').val(\''.$fromDate.'\');
			$(\'#date_to\').val(\''.$toDate.'\');';
		
	$fromDate = '01/01/'.date("Y", strtotime("-1 Year"));
	$toDate = date("t", strtotime("-1 Year")).'/12/'.date("Y", strtotime("-1 Year"));
	$oType7 = '$(\'#date_from\').val(\''.$fromDate.'\');
			$(\'#date_to\').val(\''.$toDate.'\');';
	
	//baba('atf: '.$auto_time_frame,0,1);
	//die(print_r(array( $from, $to, $auto_time_frame, $defTimeFrame )));
	// Array ( [0] => 12/10/2014 [1] => 31/12/1969 [2] => [3] => ) 
	
	$html = '
		<select id="dateSelect"  name="auto_time_frame" onchange="chgDates(this.value);" style="width: 100px;">
			<option value="1" '.($auto_time_frame == "1" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==1) ? 'selected' : '')).'>'.lang('Today').'</option>
			<option value="2" '.($auto_time_frame == "2" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==2) ? 'selected' : '')).'>'.lang('Yesterday').'</option>
			<option value="6" '.($auto_time_frame == "6" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==6) ? 'selected' : '')).'>'.lang('This Week').'</option>
			<option value="3" '.($auto_time_frame == "3" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==3) ? 'selected' : '')).'>'.lang('Month to date').'</option>
			<option value="4" '.($auto_time_frame == "4" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==4) ? 'selected' : '')).'>'.lang('Last Month').'</option>
			<option value="5" '.($auto_time_frame == "5" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==5) ? 'selected' : '')).'>'.lang('This Year').'</option>
			<option value="7" '.($auto_time_frame == "7" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==7) ? 'selected' : '')).'>'.lang('Last Year').'</option>
			<option value="8" '.($auto_time_frame == "8" ? 'selected' : ((!$auto_time_frame AND $defTimeFrame==8) ? 'selected' : '')).'>'.lang('Custom').'</option>
			
		</select></div></div>
		</div>
		<div class="col-lg-2 col-md-6 col-sm-12 col-12">
            <div class="q-summary-r">
                <h3>From:</h3>
                <div class="q-s-option from-to-input">
                	<input type="text" class="form-control" name="from" value="'.$from.'" id="date_from" />
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-6 col-sm-12 col-12">
            <div class="q-summary-r">
                <h3>To:</h3>
                <div class="q-s-option from-to-input">
                	<input type="text" class="form-control" name="to" value="'.$to.'" id="date_to" />
                
            <script type="text/javascript">
				function chgDates(o) {
					if (o == "1") {
						'.$oType1.'
						} else if (o == "2") {
						'.$oType2.'
						} else if (o == "3") {
						'.$oType3.'
						} else if (o == "4") {
						'.$oType4.'
						} else if (o == "5") {
						'.$oType5.'
						} else if (o == "6") {
						'.$oType6.'
						} else if (o == "7") {
						'.$oType7.'
						} else if (o == "8") {
						'.$oType8.'
						}
				}
				
				/**
				 * @return string
				 */
				function convertYmdToDmy(strDate) {
					var arrDate = strDate.split("-");
					
					if (2 === arrDate[0].length) {
						// Current format is "d-m-Y".
						return strDate;
					} else {
						// Current format is "Y-m-d".
						return arrDate[2] + "/" + arrDate[1] + "/" + arrDate[0];
					}
				}
				
				function convertToDmy(strDate) {
					var arrDate = strDate.split("-");
					
					if (2 === arrDate[0].length) {
						// Current format is "d-m-Y".
						return arrDate[0] + "/" + arrDate[1] + "/" + arrDate[2];
					} else {
						// Current format is "Y-m-d".
						return arrDate[2] + "/" + arrDate[1] + "/" + arrDate[0];
					}
				}
				
				$(function() {
					$("#date_from").val(convertYmdToDmy($("#date_from").val()));
					
					$("#date_to").val(convertYmdToDmy($("#date_to").val()));
					
					$("#date_from").datepicker({
						dateFormat: \'yy/mm/dd\',
						onSelect: function() {
							$("#dateSelect").val(8);
						}
					});
					
					$("#date_to").datepicker({
						dateFormat: \'yy/mm/dd\',
						onSelect: function() {
							$("#dateSelect").val(8);
						}
					});
					$("#date_to,#date_from").keypress(function (e) {
						$("#dateSelect").val(8);
					});
					
					$("#date_from").datepicker("setDate",convertToDmy("'. $from .'"));
					$("#date_to").datepicker("setDate",convertToDmy("'. $to .'"));
					
				});
			</script>
			
			';
			
	return $html;
}
	
	
	
function getPager() {
	global $set;
	$html = '<div id="pager" class="pager" '.' style="' . ($set->noFilter ? 'display: none;' : ''). 'position: inherit!important;">
			<form>
				<img src="'. $set->SSLprefix .'pages/images/icons/first.png" class="first"/>
				<img src="'.$set->SSLprefix.'pages/images/icons/prev.png" class="prev"/>
				<input type="text" class="pagedisplay" style="width: 60px; text-align: center;"  />
				<img src="'.$set->SSLprefix.'pages/images/icons/next.png" class="next"/>
				<img src="'.$set->SSLprefix.'pages/images/icons/last.png" class="last"/>
				&nbsp; &nbsp; 
				'.lang('Rows Per Page').': <input type="text" class="pagesize" value="'.$set->rowsNumberAfterSearch .'" style="width: 80px; text-align: center;" maxlength="5" />
				'.($set->totalRows ? lang('Total Rows').': <b>'.$set->totalRows.'</b>' : '').'
			</form>
		</div>';
	return $html;
	}

function getURLPager() {
	global $set;
	
	$query_string = $_SERVER['QUERY_STRING'];
	$arrQs = explode('&',$query_string);
	foreach($arrQs as $k=>$param){
		$arrParams = explode('=',$param);
		if($arrParams[0] == 'page'){
				unset($arrQs[$k]);
		}
	}
	
	$total_pages = ceil($set->total_records/$set->rowsNumberAfterSearch);
	
	$url = $set->basepage .'?' .  implode("&",$arrQs);
	
		
	if($total_pages == 0) 
		$first = $url;
	else
		$first = $url . '&page=1';
	
	if($set->page == 1){
			$prev = $url;
	}
	else{
		$pg = $set->page - 1;
		$prev = $url . '&page='. $pg;
	}
	
	
	
	$pg = $set->page + 1;
	
	if($set->page == $total_pages){
		$last = $url;
		$next =$url;
	}
	else{
		if($total_pages == 0) $next =  $url; else $next = $url . '&page='. $pg;
		
		if($total_pages == 0 || $set->page == $total_pages){
				$last=  $url;
		}
		else{
				$last = $url . '&page='. $total_pages;
		}
	}
	
	$html = '<div id="pager" class="pager" '.' style="' . ($set->noFilter ? 'display: none;' : ''). 'position: inherit!important;">
			<form>
				<a href="'. $first .'"><img src="'.$set->SSLprefix.'pages/images/icons/first.png"/></a> 
				<a href="'. $prev .'"><img src="'.$set->SSLprefix.'pages/images/icons/prev.png" class="prev1"/></a>
				<input type="text" class="pagedisplay1" style="width: 60px; text-align: center;"  value="'. $set->page ."/" . $total_pages .'"/>
				'.(($set->page == $total_pages)? '
				<a href="javascript:void(0);"><img src="'.$set->SSLprefix.'pages/images/icons/next.png" class="next1"/></a>
				<a href="javascript:void(0);"><img src="'.$set->SSLprefix.'pages/images/icons/last.png" class="last1"/></a>
				':'
				<a href="'. $next .'"><img src="'.$set->SSLprefix.'pages/images/icons/next.png" class="next1"/></a>
				<a href="'. $last .'"><img src="'.$set->SSLprefix.'pages/images/icons/last.png" class="last1"/></a>').'
				<!--&nbsp; &nbsp; 
				'.lang('Rows Per Page').': <input type="text" class="pagesize" value="'.$set->rowsNumberAfterSearch .'" style="width: 80px; text-align: center;" maxlength="5" />
				'.($set->totalRows ? lang('Total Rows').': <b>'.$set->totalRows.'</b>' : '').'-->
			</form>
		</div>';
	return $html;
	}

function isMustField($fieldName){
	global $set;
	if(!$set->mustFields){
		$thisId = aesDec($_COOKIE['mid']);
		if ($thisId=='') {
			$thisId = $set->id;
		}
		// $qry = 'SELECT mustFields AS fields FROM merchants WHERE id='.$thisId;
		$qry = 'SELECT mustFields AS fields FROM settings limit 1';// WHERE id='.$thisId;
// die ($qry);		
		
		$must = mysql_fetch_assoc(function_mysql_query($qry,__FILE__,__FUNCTION__));
		if($must['fields']){
			$set->mustFields = explode('|',$must['fields']);
		}else{
			$set->mustFields = Array();
		}
	}
	//else case added by shalini for dynamically checking mustfields
	else{
		$mustFields = explode('|',$set->mustFields);
		for($i=0;$i<count($mustFields);$i++){
			
			if($mustFields[$i]==$fieldName){
				return 1;
			}
		}
	}
	/* for($i=0;$i<count($set->mustFields);$i++){
		if($set->mustFields[$i]==$fieldName){
			return 1;
		}
	} */
	return 0;
}

function convertCurEx($amount,$from='USD') {
	global $set;
	$toCur = $set->userInfo['preferedCurrency'];
	$newAmount = $amount;
	if (strtoupper($from) ==strtoupper($toCur)) 
		return $newAmount;
	$currKey = $from.$toCur;
	$curs = ($set->currencies);
	foreach ($curs as $cur) {
		
		if (strtoupper($currKey)==strtoupper($cur['currKey'])) {
			$newAmount = (float)($newAmount) * (float)($cur['val']);
		}
	}
			
	return $newAmount;
}

	function getImageGrowerScript() {
$a = '
<script>
	$(document).ready(function(){
		$(".inline").colorbox({iframe:true,border: "1px black solid" ,height: "95%", width:"95%",fixed:true});
	
			$( ".img-wrap img" ).each(function( index ) {
			//console.log($(this).attr("src"));
			var dymantionsStr = $(this).closest("tr").find(".dimantion-wrap").text();
			console.log(dymantionsStr);
			var dymantionsArray = dymantionsStr.split("x");
			//console.log(dymantionsArray);
			var dymantionsRate = parseInt(dymantionsArray[0])/parseInt(dymantionsArray[1]);
			if(dymantionsRate < 0.7)
			{
				$(this).addClass("small-scale");
			}
			else if(dymantionsRate > 10) 
			{
				$(this).addClass("horizontal-scale");
			}
			else if(dymantionsRate > 0.7 && dymantionsRate < 2) 
			{
				$(this).addClass("square-scale");
			}
			console.log(dymantionsRate);
		});
		
		$( ".img-wrap img" ).hover(
			function() {
				var currentImage = $( this );
				currentImage.addClass("animate");
				setTimeout(function(){ 
					currentImage.removeClass("animate");
					//console.log("innn");
				}, 2000);
			}, function() {
				$( this ).removeClass("animate");
			}
		);
	});
	</script>
	<style>
	
	.img-wrap img {
		transition: all 0.5s ease;
		
	}
	
	.img-wrap img:not(.small-scale) {
		display: block;
		max-width: 100%;
		height: auto;
	}
	
	.img-wrap img.animate:not(.small-scale):not(.square-scale):not(.horizontal-scale):hover {
		transform: scale(5);
	}
	
	.img-wrap img.animate.small-scale:hover {
		transform: scale(7);
	}
	
	.img-wrap img.animate.square-scale:hover {
		transform: scale(11);
	}
	
	.img-wrap img.animate.horizontal-scale {
		/* width: 70%; */
	}
	
	.img-wrap img.animate.horizontal-scale:hover {
		transform: scale(15);
	}
	
	.img-wrap img.small-scale {
		display: block;
		max-height: 200px;
		width: auto;
	}
	.img-wrap {
		/* width:10%; */
		
	}
	
	.creative-name {
		width: 10%;
	}
	</style>
';
return $a;
	}


	function createGraphData($days = null)
	{
		if(isset($_GET['countryPie']) && $_GET['countryPie'] != null && is_numeric($_GET['countryPie'])){
			$days = $_GET['countryPie'];
		}
		$today_date = date('Y-m-d');
		$past_date = date('Y-m-d',strtotime($today_date. ' -'.$days.' day'));
		
		$data = mysql_query('SELECT COUNT(CountryID) as country_count, CountryID as country_iso2 FROM merchants_creative_stats WHERE Date >= "'.$past_date.'" AND Date <= "'.$today_date.'" AND AffiliateID = '.$set->userInfo['id'].' GROUP BY CountryID');
		
		$data_result_x = [];
		$data_result_y = [];
		if (mysql_num_rows($data) > 0) {
			while($row = mysql_fetch_assoc($data)) {
				$data_result_x[] = '"'.$row['country_iso2'].'"';
				$data_result_y[] = $row['country_count'];

			}
		}

		$x_axis = '['.implode(',',$data_result_x).']';
		$y_axis = '['.implode(',',$data_result_y).']';

		return isset($_REQUEST['countryPieDays']) && $_REQUEST['countryPieDays'] != null ? $_REQUEST['countryPieDays'] : 90; //isset($days) && $days != null ? $days : 90;
	}

	function getDeviceReport(){
		global $set;
		$days = 90;
		
		$html = '<div class="session-device-chart">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
		<link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css" rel="stylesheet" media="all">
					<canvas id="myChart" style="width:100%;height:249px"></canvas>
						<script>

						function onChangeCountryPieDays()
						{
							var data = document.getElementById("countryPieDays");
							var getvalue = data.options[data.selectedIndex].value;

							$.get( "' . $_SERVER['SERVER_HOST'] . '/ajax/getReportData.php?countryPieDays="+getvalue+"&data_id="+'.$set->userInfo['id'].', function(res) {
								try {
									console.log(response);
									var response = JSON.parse(res);
									var ctx = document.getElementById("myChart").getContext("2d");
										var barColors = [
											"#282560",
											"#F37A20",
											"#FF0000",
											"#FF0000"
										];

										setTimeout(()=>{
											new Chart(ctx, {
												type: "doughnut",
												data: {
													labels:response.x_axis,
													datasets: [{
														data: response.y_axis,
														backgroundColor: barColors,
														borderColor: [
															"rgba(255, 99, 132, 1)",
															"rgba(54, 162, 235, 1)",
															"rgba(255, 206, 86, 1)",
															"rgba(255, 206, 86, 1)"
														],
														borderWidth: 1
													}]
												},
												options: {
													legend: {
														position: "bottom"
													},
													title: {
													display: false,
													text: ""
													}
												}
											});
										},1000)

										
											} catch (error) {
												console.log(error);
											}
										});
						}

						onChangeCountryPieDays();
						</script>
				</div>';
		return $html;
	}

	
	function getFavoritesHTML(){
		global $set;
		if(isset($_GET['from']) && isset($_GET['to'])){
		$html = '
		<style>
		.hover13 figure:hover img {
				opacity: 1;
				-webkit-animation: flash 1.5s;
				animation: flash 1.5s;
			}
			@-webkit-keyframes flash {
				0% {
					opacity: .4;
				}
				100% {
					opacity: 1;
				}
			}
			@keyframes flash {
				0% {
					opacity: .4;
				}
				100% {
					opacity: 1;
				}
			}
			figure:hover+.tooltipHover {
				opacity: 1;
			}
			.column div span {
				color: #444;
				-webkit-transition: .3s ease-in-out;
				transition: .3s ease-in-out;
				opacity: 0;
				position: relative;
				font-weight: bold;
				bottom: 6px;
				font-size: 13px;
				left: -35px;
			}
			input[name=report_name]{
				width:300px;
			}
		</style>
		<div class="hover13 column"><div><figure style="display:inline"><a href="javascript:void(0)" class="saveReport" style="padding-left:10px;padding-right:10px;" ><img src="'.$set->SSLprefix.'images/star.png" width=28/></a></figure><span class="tooltipHover">'. lang("Add Report To Favorites.") .'</span></div></div>';
		
		}
		else
			$html = "";
		return $html;
	}
?>