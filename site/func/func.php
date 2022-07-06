<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function _goto($url="") {
	global $set;
	if (!$url) $url = '/';
	header("Location: $url",true,301);
	die();
	}

function GetPages($tbl, $where = "",$pg="0",$itemlimit="10",$qry="") {
	// die ();
	global $set, $QUERY_STRING,$_GET;
	if ($_GET['pageURL']) {
		$expPage=explode("?",$_GET['pageURL']);
		$QUERY_STRING=$expPage[1];
		}
	if ($QUERY_STRING) {
		$exp=explode("pg", $QUERY_STRING);
		$expresult=$exp[0];
		$lastLen = substr($expresult,-1,1);
		if ($expresult) $QUERY_STRING="?".$expresult.($lastLen == "&" ? '' : '&');
			 else $QUERY_STRING="?";
		} else {
		$QUERY_STRING="?";
		}
	$GetPages .='<table><tr>';
	if($qry!==""){
		$wwdata = function_mysql_query($qry);
		$nump = mysql_num_rows($wwdata);
		//echo $nump;die;
	}	
	else
	$nump=mysql_result(function_mysql_query("SELECT COUNT(id) AS total FROM ".$tbl." ".$where,__FILE__,__FUNCTION__),0);
	// $nump = $nump['total'];
	$nump = $nump /  $itemlimit;
	if(intval($nump) != $nump) {
		$nump = intval($nump) + 1;
		}

	if ($pg != 0) {
		$lastpg= $pg-1;
		$GetPages .='<td><div style="padding-top: 2px; width: 20px; height: 18px; background: #eeedec; border: 1px #000000 solid; text-align: center;"><a href="'.$set->basepage.($lastpg > 0 ? $QUERY_STRING.'pg='.$lastpg : substr($QUERY_STRING,0,-1)).'" style="font-family: Tahoma; font-weight: bold; color: #231f20;">«</a></div></td>'; 
		}
	$nump=$nump-1;

	for($i = 0; $i<= $nump; $i++) {
		$b =  $i +1;
		if ($i == $pg) {
			$GetPages .='<td><div style="padding-top: 2px; width: 20px; height: 18px; background: #231f20; color: #cdb230; border: 1px #000000 solid; font-weight: bold; font-family: Tahoma; text-align: center;">'.$b.'</div></td>';
			} else {
			if ($i >= ($pg-8) AND $i <= ($pg+8)) $GetPages .='<td><div style="padding-top: 2px; width: 20px; height: 18px; background: #eeedec; border: 1px #000000 solid; text-align: center;"><a href="'.$set->basepage.($i > 0 ? $QUERY_STRING.'pg='.$i : substr($QUERY_STRING,0,-1)).'" style="font-family: Tahoma; font-weight: bold; color: #231f20;">'.$b.'</a></div></td>';
			}
		}
	if ($pg != $nump) {
	$nextpg= $pg + 1;
		$GetPages .='<td><div style="padding-top: 2px; width: 20px; height: 18px; background: #eeedec; border: 1px #000000 solid; text-align: center;"><a href="'.$set->basepage.($nextpg > 0 ? $QUERY_STRING.'pg='.$nextpg : substr($QUERY_STRING,0,-1)).'" style="font-family: Tahoma; font-weight: bold; color: #231f20;">»</a></div></td>';
	}
	$GetPages .='</tr></table>';
	if ($nump < 0) return false;
	return $GetPages;
	}
	
function err($name) {
	global $errors;
	if ($errors[$name]) return ' style="color: red;"';
	}
	
function getCountries($country_id=0) {
	$qq=function_mysql_query("SELECT countrySHORT,countryLONG FROM ip2country where 3=3 GROUP BY countrySHORT ORDER BY countryLONG ASC",__FILE__,__FUNCTION__);
	while ($ww=mysql_fetch_assoc($qq)) $html .= '<option value="'.$ww['countrySHORT'].'" '.($ww['countrySHORT'] == $country_id ? 'selected="selected"' : '').'>'.$ww['countryLONG'].'</option>';
	return $html;
	}
	
	
function excelExporter($tableStr,$title){
	
	$filename = $title."_data_" . date('YmdHis');
	
	if(isset($_REQUEST['excel'])){
		
		$tableStrArr = explode('<tfoot>',$tableStr);
		$header = $tableStrArr[0];
		
		$tableStrArr2 = explode('</tfoot>',$tableStrArr[1]);
		$data = str_replace('</table>','',$tableStrArr2[1]);
		
		$footer = '<tfoot>'.$tableStrArr2[0].'</tfoot></table>';
		
		$tableStr = $header.$data.$footer;
		
		$format = $_REQUEST['excel'];
		
		$tableStr = str_replace('<thead>','',$tableStr);
		$tableStr = str_replace('</thead>','',$tableStr);
		$tableStr = str_replace('<tfoot>','',$tableStr);
		$tableStr = str_replace('</tfoot>','',$tableStr);
		$tableStr = str_replace('<tbody>','',$tableStr);
		$tableStr = str_replace('</tbody>','',$tableStr);
		$tableStr = str_replace('</a>','</span>',$tableStr);
		$tableStr = str_replace('<a href','<span href',$tableStr);
		
		$tableStr = str_replace('<table width="2600" class="tablesorter" border="0" cellpadding="0" cellspacing="0">','<table border=1>',$tableStr);
		
		$tableStr = str_replace(",","",$tableStr);
	
		/*
		$tableStrArr = explode(',',$tableStr);
		
		for($i=0;$i<count($tableStrArr);$i++){
			$beforeComma = explode('>',$tableStrArr[$i]);
			$beforeComma = $beforeComma[count($beforeComma)-1];
			$afterComma = explode('<',$tableStrArr[$i+1]);
			$afterComma = $afterComma[0];
			
			$val = '"'.$beforeComma.','.$afterComma.'"';
			
			
			$restOfString = '';
			for($j=($i+1);$j<count($tableStrArr);$j++){
				$restOfString.=$tableStrArr[$j];
			}
			
			die($restOfString);
			
			$tableStrArr2 = explode($val,$tableStr);
			$tableStr = $tableStrArr2[0].'"'.$val.'"'.$restOfString;
		}
		*/
		
		
		if($format=='csv'){
			//include "func/simple_html_dom.php";
			
			
			
			
			$table = $tableStr;
			
			
			$html = str_get_html($table);
		
			header('Content-Encoding: UTF-8');
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-type: application/ms-excel');
			header('Content-Disposition: attachment; filename='.$filename.'.csv');
			header("Pragma: no-cache");
			header("Expires: 0");
			echo "\xEF\xBB\xBF";
			//$fp = fopen("php://output", "w");
			$csv = '';
			foreach($html->find('tr') as $element){
				$newRow = '';
				$td = array();
				foreach( $element->find('th') as $row)  
				{
					$td [] = $row->plaintext;
					if($newRow!=''){
						$newRow.=',';
					}
					$newRow.= $row->plaintext;
				}
				//fputcsv($fp, $td);
				

				$td = array();
				foreach( $element->find('td') as $row)  
				{
					//$value = iconv( 'ISO-8859-1' , 'UTF-8//IGNORE' ,$row->plaintext);
					if(false !== strpos($row->plaintext,"&#")){
						$str = str_replace("&#",';&#',$row->plaintext);
						$str = substr($str,1);
						$str = trim($str).";";
						$value = html_entity_decode($str, ENT_HTML401, 'UTF-8');
					}
					else{
						$value = $row->plaintext;
					}
					$td [] = $value;
					if($newRow!=''){
						$newRow.=',';
					}
					$newRow.= $value;
				}
				
				//fputcsv($fp, $td);
				
				if($csv!=''){
					$csv.='
					';
				}
				$csv.=$newRow;
			}
			echo $csv;
			
		}else if($format=='xls'){
	
			$tableStr = str_replace('border="0"','border="1"',$tableStr);
			header('Content-Type: text/html; charset=utf-8');
			header("Content-type: application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=\"".$filename.".xls\"");
			header("Pragma: no-cache"); 
			error_reporting(0);
		
			
			
			
			echo '
			<style>
			 th{
				background-color:#eeeeee;
			 }
			</style>';
			
			echo $tableStr;
			
		}
		
		die();
		
	}
}
			function toUTF8($str) { 
				if( mb_detect_encoding($str,"UTF-8, ISO-8859-8")!="UTF-8" ){ 
					return  iconv("windows-1255","utf-8",$str); 
				} else { 
					return $str; 
				} 
			}

			function cleanData($str) { 
				$str = preg_replace("/\t/", "\\t", $str);
				$str = preg_replace("/\r?\n/", "\\n", $str); 
				if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"'; 
				return $str;
			}
?>