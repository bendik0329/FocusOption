<?php
require_once('common/global.php');
$lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/admin/";
if (!isAdmin()) _goto($lout);
switch ($act) {
	case "generate":
			
			$sql = "select * from design_css where design_id = " . $design;
			$results = function_mysql_query($sql,__FILE__);
			if(!empty($results)){
				$class = "";
				$css ="";
				$l=0;
				$a=0;
				while($row = mysql_fetch_assoc($results)){
						if($row["html_attribute_type"] == '@font-face'){
							if($a==0){
								$css .= $row["html_attribute_type"] . "{";
								$css .= $row['attribute_name'] . ":".$row['attribute_value'] .";";
								$a++;
							}
							elseif($a==2){
							$css .= "}";
							$css .= $row["html_attribute_type"] . "{";
							$css .= $row['attribute_name'] . ":".$row['attribute_value'] .";";
							$a=1;
							}
							else{
							$css .= $row['attribute_name'] . ":".$row['attribute_value'] .";";
							$a++;
							}
							
						}
						else{
							if($class !=$row["html_attribute_type"])
							{
								if($l!=0){
									$css .= "}";
								}
									$css .= $row["html_attribute_type"] . "{";
									$css .= $row['attribute_name'] . ":".$row['attribute_value'] .";";
									
									$class = $row["html_attribute_type"];
							}
							else{
								$css .= $row['attribute_name'] . ":".$row['attribute_value'] .";";
								$class = $row["html_attribute_type"];
							}
						}
						$l++;
						if(mysql_num_rows($results) == $l)
						{
							$css .= "}";
						}
				}
			}  			
			$filename =  $_SERVER['DOCUMENT_ROOT'] . "/css/style.css";
			file_put_contents($filename,$css);
			_goto($set->SSLprefix.$set->uri);
			break;
	case "search":
		//$set->pageTitle = lang('Design CSS');
		$set->breadcrumb_title =  lang('Design CSS');
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.'admin/design_css.php?act=search&design='. $design .'&sectionType='. $sectionType .'" class="arrow-left">'.lang('Design CSS').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		
		$sql = "SELECT * from design_css where valid =1";
		if(isset($searchKey) && $searchKey != ""){
			$sql .= " AND (html_attribute_type like '%" . strtolower($searchKey) . "%' OR attribute_name like '%" . strtolower($searchKey) . "%')";
		}
		if(isset($searchVal) && $searchVal != ""){
			$sql .= " AND attribute_value like '%" . strtolower($searchVal) . "%'";
		}
		if(isset($attributeType) && $attributeType != ""){
			$sql .= " AND type = '" . $attributeType . "'";
		}
		
		if(isset($sectionType) && $sectionType != ""){
			if($sectionType == 'table')
				$sql .= " AND (html_attribute_type LIKE '%table%' OR html_attribute_type LIKE '%thead%' OR html_attribute_type LIKE '%tfoot%' OR html_attribute_type LIKE '%tr%' OR html_attribute_type LIKE '%td%' OR html_attribute_type LIKE '%th%')";
			elseif($sectionType == 'button')
				$sql .= " AND (html_attribute_type LIKE '%button%' OR html_attribute_type LIKE '%btn%' OR html_attribute_type LIKE '%input[type=submit]%')";
			elseif($sectionType == 'general')
				$sql .= " AND (html_attribute_type LIKE '%@font%' OR html_attribute_type LIKE '%html%')";
			elseif($sectionType == 'input')
				$sql .= " AND (html_attribute_type LIKE '%@form%' OR html_attribute_type LIKE '%input%' OR html_attribute_type LIKE '%select%' OR html_attribute_type LIKE '%textarea%' OR html_attribute_type LIKE '%input%')";
			elseif($sectionType == 'tab')
				$sql .= " AND (html_attribute_type LIKE '%tab_over%' OR html_attribute_type LIKE '%tab_pin%' OR html_attribute_type LIKE '%div.tab%'  OR html_attribute_type LIKE '%tab_open%' OR html_attribute_type LIKE '%tab_out%')";
			else
				$sql .= " AND html_attribute_type LIKE '%" . $sectionType . "%'";
		}
		$results = function_mysql_query($sql,__FILE__);
		$set->content .= display_results($results);
		theme();
		break;
	default:
		//$set->pageTitle = lang('Design CSS');
		
		$set->breadcrumb_title =  lang('Design CSS');
		$set->pageTitle = '
		<style>
		.pageTitle{
			padding-left:0px !important;
		}
		</style>
		<ul class="breadcrumb">
			<li><a href="'.$set->SSLprefix.'admin/">'.lang('Dashboard').'</a></li>
			<li><a href="'.$set->SSLprefix.'admin/design_css.php?design='. $design .'" class="arrow-left">'.lang('Design CSS').'</a></li>
			<li><a style="background:none !Important;"></a></li>
		</ul>';
		
		$sql = "SELECT * from design_css where design_id=".$design  . " and valid =1";
		
		$results = function_mysql_query($sql,__FILE__);
		$set->content .= display_results($results);
				theme();
		break;
	}
	function display_results($results){
		$data = "";
		if(!empty($results)){
			$l=0;
			$data='<script src="'.$set->SSLprefix.'js/jscolor.min.js"></script>
			<script type="text/javascript" src="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.js"></script>
		   <link rel="stylesheet" href="'.$set->SSLprefix.'js/impromptu/dist/jquery-impromptu.min.css"/>
									
									<script type="text/javascript">
									//$("#frmgeneratecss").submit(function(){
											function checkGenerate(){
											var chk = false;
											var type = $(this).data("type");
											$.prompt("'.lang('Are you sure you want to override the current design?').'", {
												top:200,
												title: "Update CSS",
												buttons: { "'.lang('Yes').'": true, "'.lang('Cancel').'": false },
												submit: function(e,v,m,f){
													if(v){
														chk = true;
														$("#frmgeneratecss").submit();
													}
													else{
														chk = false;
													}
												}
											});
										}
								
								</script>';
		
			$data.="<script>
			$(document).ready(function($){
				
				
				
				$('#attributeType').change(function(){
					$('#searchKey').val('');
					$('#searchVal').val('');
					selecttype = $(this).val();
					if(selecttype != '')
					{
						$('.allrows').each(function(key,row){
								if(selecttype == $(this).data('csstype'))
								{
									$(this).show();
								}
								else{
										$(this).hide();
								}
								
						});
					}
					else{
						$('.allrows').each(function(key,row){
							$(this).show();
						});
					}
				});
				$('#searchKey').keyup(function(){
					searchtext = $(this).val();
					if(searchtext!=''){
						$('.allrows').each(function(key,row){
							if($(this).css('display') != 'none'){
								searchin = $(this).find('td:first').data('keys');
								console.log(searchin);
								if (searchin.toLowerCase().indexOf(searchtext) >= 0)
								{
									$(this).show();
								}
								else{
										$(this).hide();
								}
							}
						});
					}
					else{
						
						showRowsWithType();
						showRowsWithValue();
						
					}
				});
				
				
				
				$('#searchVal').keyup(function(){
					searchtext = $(this).val();
					if(searchtext!=''){
					$('.allrows').each(function(key,row){
						if($(this).css('display') != 'none'){
							searchin = $(this).find('td:last').data('vals');
								try{
									if (searchin.toLowerCase().indexOf(searchtext) >= 0)
									{
										$(this).show();
									}
									else{
											$(this).hide();
									}
								}
								catch(e){
									$(this).hide();
								}
						}
					});
					}
					else{
						showRowsWithType();
						showRowsWithKeys();
					}
				});
				
				
				function showRowsWithType(){
					selecttype = $('#attributeType').val();
					if(selecttype != '')
					{
						$('.allrows').each(function(key,row){
								if(selecttype == $(this).data('csstype'))
								{
									$(this).show();
								}
								else{
										$(this).hide();
								}
						});
					}
					else{
						$('.allrows').each(function(key,row){
							$(this).show();
						});
					}
				}
				
				function showRowsWithValue(){
					searchtext = $('#searchVal').val();
					if(searchtext!=''){
					$('.allrows').each(function(key,row){
						if($(this).css('display') != 'none'){
							searchin = $(this).find('td:last').data('vals');
								try{
									if (searchin.toLowerCase().indexOf(searchtext) >= 0)
									{
										$(this).show();
									}
									else{
											$(this).hide();
									}
								}
								catch(e){
									$(this).hide();
								}
						}
					});
					}
				}
				
				function showRowsWithKeys(){
					searchtext = $('#searchKey').val();
					if(searchtext!=''){
					$('.allrows').each(function(key,row){
								searchin = $(this).find('td:first').data('keys');
								console.log(searchin);
								if (searchin.toLowerCase().indexOf(searchtext) >= 0)
								{
									$(this).show();
								}
								else{
										$(this).hide();
								}
						});
					}
				}
			
			});
			
			function setTextColor(picker) {
				document.getElementsByTagName('body')[0].style.color = '#' + picker.toString()
			}
			function updateCss(e,id){
				console.log(id);
				 	$.post('ajax/UpdateDesignCssById.php', 
											       {
													   css: e.value,
													   id     : id
												   },
												   function(res) {
												   }); 
			}
			
			function update(e,color){
				var CSS_COLOR_NAMES = ['AliceBlue','AntiqueWhite','Aqua','Aquamarine','Azure','Beige','Bisque','Black','BlanchedAlmond','Blue','BlueViolet','Brown','BurlyWood','CadetBlue','Chartreuse','Chocolate','Coral','CornflowerBlue','Cornsilk','Crimson','Cyan','DarkBlue','DarkCyan','DarkGoldenRod','DarkGray','DarkGrey','DarkGreen','DarkKhaki','DarkMagenta','DarkOliveGreen','Darkorange','DarkOrchid','DarkRed','DarkSalmon','DarkSeaGreen','DarkSlateBlue','DarkSlateGray','DarkSlateGrey','DarkTurquoise','DarkViolet','DeepPink','DeepSkyBlue','DimGray','DimGrey','DodgerBlue','FireBrick','FloralWhite','ForestGreen','Fuchsia','Gainsboro','GhostWhite','Gold','GoldenRod','Gray','Grey','Green','GreenYellow','HoneyDew','HotPink','IndianRed','Indigo','Ivory','Khaki','Lavender','LavenderBlush','LawnGreen','LemonChiffon','LightBlue','LightCoral','LightCyan','LightGoldenRodYellow','LightGray','LightGrey','LightGreen','LightPink','LightSalmon','LightSeaGreen','LightSkyBlue','LightSlateGray','LightSlateGrey','LightSteelBlue','LightYellow','Lime','LimeGreen','Linen','Magenta','Maroon','MediumAquaMarine','MediumBlue','MediumOrchid','MediumPurple','MediumSeaGreen','MediumSlateBlue','MediumSpringGreen','MediumTurquoise','MediumVioletRed','MidnightBlue','MintCream','MistyRose','Moccasin','NavajoWhite','Navy','OldLace','Olive','OliveDrab','Orange','OrangeRed','Orchid','PaleGoldenRod','PaleGreen','PaleTurquoise','PaleVioletRed','PapayaWhip','PeachPuff','Peru','Pink','Plum','PowderBlue','Purple','Red','RosyBrown','RoyalBlue','SaddleBrown','Salmon','SandyBrown','SeaGreen','SeaShell','Sienna','Silver','SkyBlue','SlateBlue','SlateGray','SlateGrey','Snow','SpringGreen','SteelBlue','Tan','Teal','Thistle','Tomato','Turquoise','Violet','Wheat','White','WhiteSmoke','Yellow','YellowGreen'];

				var current_value = e.value;
						colorstr = color.toString();
					var currentCharPressed = current_value.slice(0, e.selectionStart).length;
					var stringWords = current_value.split(' ');
					
					$.each(stringWords, function( index, word ) {
						var wordIndexInString = current_value.indexOf(word);
						
						if(currentCharPressed >= wordIndexInString && currentCharPressed <= (wordIndexInString+word.length))
						{
							
							
							if(word.search(',') !== -1){
								var words = word.split(',');
								if(words[1] != ''){
									words[1] = '#' + colorstr.toUpperCase();
									
									var a = words.join(',');
									word= a;
									stringWords[index]  = word;
									return;
								}
							} 
							else{
								if(word.indexOf('#') === 0){
								
									stringWords[index] = '#' + colorstr.toUpperCase();
									return;
								}
								else if($.inArray(word,CSS_COLOR_NAMES)){
									
									if(word.search('!') !== -1){
									var words = word.split('!');
									if(words[0] != ''){
										words[0] = '#' + colorstr.toUpperCase();
										
										var a = words.join('!');
										word= a;
										stringWords[index]  = word;
										return;
									}
								}
							}
						}
					}
						
						/*console.log(wordIndexInString);
						console.log(word.length);*/
						
					});
					console.log(stringWords);
					var thereYouGo = stringWords.join(' ');
					e.value = thereYouGo;
					console.log(thereYouGo);
					
			}
			
			
			
			</script>";
			$data.= "<style>
				#section tbody tr td{
					padding : 5px 0 0 10px;
					width:100%;
					font-size:14px;
				}
				#section tbody{
					    width: 100%;
						display: block;
						background: rgb(248, 248, 248);
						
				}
				#section thead tr td{
					font-size:14px;
				}
				#divfix {
       bottom: 35px;
       right: 0;
       position: fixed;
       z-index: 3000;
        }
			</style>";
			$data .= '<div class="normalTableTitle" style="width: 100%;">'.lang('Filter CSS').'</div>
			<div style="background: #F8F8F8;">
			<form action="'.$set->basepage.'" method="get">
			<input type="hidden" name="act" value="search" />
			<input type="hidden" name="design" value="'. $_GET['design']. '" />
			<table border="0" cellpadding="3" cellspacing="2">
				<tr>
					<td>'.lang('Type').'</td>
					<td>'.lang('Search in Keys').'</td>
					<td>'.lang('Search in Values').'</td>
					<!--<td></td>-->
				</tr><tr>
					<td><select name="attributeType" id="attributeType" style="width: 150px;">'. getOptions() .'</select></td>
					<td><input type="text" id="searchKey" name="searchKey" value="'. $_GET['searchKey'] .'"/></td>
					<td><input type="text" id="searchVal" name="searchVal" value="'. $_GET['searchVal'] .'"/></td>
					<!--<td><input type="submit" value="'.lang('Search').'" /></td>-->
				</tr>
			</table>
			</form>
			</div>';
			$listRecords = "";
			$a = 0;
			while($row = mysql_fetch_assoc($results)){
					
					$listRecords .="<tr class='allrows' data-csstype = '". $row['type'] ."'><input type='hidden' name='attribute_id[]' value='". $row['id'] ."'>";
					$listRecords .= "<td class='allkeys' data-keys = '".  $row['attribute_name'] ." " . $row['html_attribute_type'] ."' style='text-align: center;' width='30%'>". $row['html_attribute_type'] ."<input type='hidden' name='html_attribite_type[]' value = '". $row['html_attribute_type'] ."'></td>";
					$listRecords .= "<td style='text-align: center;' width='20%'>". $row['attribute_name'] ."<input type='hidden' name='attribute_name[]' value='".  $row['attribute_name']."'></td>";
					if(strpos($row['attribute_value'],'#') === 0)
					$listRecords .= "<td class='allvals' data-vals ='". strtolower($row['attribute_value']) ."' style='text-align: center;' width='58%'><input type='text' name='attribute_value[]' class='jscolor {hash:true}'  value='". $row['attribute_value'] ."' style='width:50%' onclick='update(this,this.jscolor)' onblur='updateCss(this," . $row['id'].");'/><td>";
					else{
						//echo colors($row['attribute_value'],$row['id']);
						$listRecords .= colors($row['attribute_value'],$row['id']);
						//$listRecords .= "<td style='text-align: center;' width='58%'><input type='text' name='attribute_value[]'class ='attribute_value' value='". $row['attribute_value'] ."' style='width:50%' onblur='updateCss(this," . $row['id'].");'/></td>";
					}
					$listRecords .= "</tr>";
			
			}
			$data .= '<div style="margin-bottom:50px !Important;">
			<form method="post" id="frmgeneratecss"><input type="hidden" name="act" value="generate"><div style="width:97%;text-align:right;padding-bottom:10px;">';
			if($listRecords!=""){
			$data .= '<div class="btn"><a href ="'.$set->SSLprefix.'admin/design_layouts.php" >'. lang('Go back to Layouts') .'</a></div><!--&nbsp;&nbsp;<input type="submit" value="'.lang('Generate CSS').'">-->';
			}
			$data .= '</div>';
			
			$data .= "<div class='normalTableTitle'>".lang('CSS Designs')."</div><div style='float:left;padding:0px 5px 5px 5px;width:18%;'>". getLinks() ."</div><div><table class='normal' width='80%' border='0' cellpadding='3' cellspacing='0' style='border-left:1px dotted #ddd;'>";
			$data .= "<thead>
			<tr><td>". lang('Class Name') ."</td><td>". lang('Property Name') ."</td><td>". lang('Propety value') ."</td></tr>
			</thead><tbody>". $listRecords;
				
				$data .= '</tbody></table></div><div style="width:97%;text-align:center;margin-top:20px;">';
				if($listRecords!=""){
				$data .= '<div id="divfix"><input type="button"  onclick ="javascript:checkGenerate();" value="'.lang('Generate CSS').'"></div></div></form>';
				}
				$data.='</div>';
		}
	return $data;
	}
	
			function getOptions(){
			$options = "";
			if(isset($_GET['attributeType']) && $_GET['attributeType']==""){
					$options .='<option value="" selected>'.lang('All').'</option>';
			}
			else{
				$options .='<option value="">'.lang('All').'</option>';
			}
			if(isset($_GET['attributeType']) && $_GET['attributeType']=="class"){
					$options .='<option value="class" selected>'.lang('Classes').'</option>';
			}
			else{
				$options .='<option value="class">'.lang('Class').'</option>';
			}
			if(isset($_GET['attributeType']) && $_GET['attributeType']=="attribute"){
					$options .='<option value="attribute" selected>'.lang('Attributes').'</option>';
			}
			else{
				$options .='<option value="attribute">'.lang('Attribute').'</option>';
			}
			if(isset($_GET['attributeType']) && $_GET['attributeType']=="id"){
					$options .='<option value="id" selected>'.lang('IDs').'</option>';
			}
			else{
				$options .='<option value="id">'.lang('ID').'</option>';
			}
			return $options;
	}
	
	function getLinks(){
		$links = "";
		$links .="<table id='section' class='normal' width='100%' border='0' cellpadding='3' cellspacing='0'><thead><tr><td>". lang('SECTIONS') ."</td></tr></thead>
		<tbody>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."'>". lang('All')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=header'>". lang('Header')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=login'>". lang('Login Page')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=table'>". lang('Tables')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=button'>". lang('Buttons')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=footer'>". lang('Footer')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=menu'>". lang('Menus')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=input'>". lang('Controls')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=tabs'>". lang('Tabs')."</a></td></tr>
		<tr><td><a href='".$set->SSLprefix."admin/design_css.php?act=search&design=". $_GET['design']."&sectionType=general'>". lang('General')."</a></td></tr>
		</tbody></table>";
		return $links;
	}
	
	function colors($attrVal,$attrId){
	
		$arrColors = array("#F0F8FF"=>"AliceBlue","#FAEBD7"=>"AntiqueWhite","#00FFFF"=>"Aqua","#7FFFD4"=>"Aquamarine","#F0FFFF"=>"Azure","#F5F5DC"=>"Beige","#FFE4C4"=>"Bisque","#000000"=>"Black","#FFEBCD"=>"BlanchedAlmond","#0000FF"=>"Blue","#8A2BE2"=>"BlueViolet","#A52A2A"=>"Brown","#DEB887"=>"BurlyWood","#5F9EA0"=>"CadetBlue","#7FFF00"=>"Chartreuse","#D2691E"=>"Chocolate","#FF7F50"=>"Coral","#6495ED"=>"CornflowerBlue","#FFF8DC"=>"Cornsilk","#DC143C"=>"Crimson","#00FFFF"=>"Cyan","#00008B"=>"DarkBlue","#008B8B"=>"DarkCyan","#B8860B"=>"DarkGoldenRod","#A9A9A9"=>"DarkGray","#A9A9A9"=>"DarkGrey","#006400"=>"DarkGreen","#BDB76B"=>"DarkKhaki","#8B008B"=>"DarkMagenta","#556B2F"=>"DarkOliveGreen","#FF8C00"=>"Darkorange","#9932CC"=>"DarkOrchid","#8B0000"=>"DarkRed","#E9967A"=>"DarkSalmon","#8FBC8F"=>"DarkSeaGreen","#483D8B"=>"DarkSlateBlue","#2F4F4F"=>"DarkSlateGray","#2F4F4F"=>"DarkSlateGrey","#00CED1"=>"DarkTurquoise","#9400D3"=>"DarkViolet","#FF1493"=>"DeepPink","#00BFFF"=>"DeepSkyBlue","#696969"=>"DimGray","#696969"=>"DimGrey","#1E90FF"=>"DodgerBlue","#B22222"=>"FireBrick","#FFFAF0"=>"FloralWhite","#228B22"=>"ForestGreen","#FF00FF"=>"Fuchsia","#DCDCDC"=>"Gainsboro","#F8F8FF"=>"GhostWhite","#FFD700"=>"Gold","#DAA520"=>"GoldenRod","#808080"=>"Gray","#808080"=>"Grey","#008000"=>"Green","#ADFF2F"=>"GreenYellow","#F0FFF0"=>"HoneyDew","#FF69B4"=>"HotPink","#CD5C5C"=>"IndianRed","#4B0082"=>"Indigo","#FFFFF0"=>"Ivory","#F0E68C"=>"Khaki","#E6E6FA"=>"Lavender","#FFF0F5"=>"LavenderBlush","#7CFC00"=>"LawnGreen","#FFFACD"=>"LemonChiffon","#ADD8E6"=>"LightBlue","#F08080"=>"LightCoral","#E0FFFF"=>"LightCyan","#FAFAD2"=>"LightGoldenRodYellow","#D3D3D3"=>"LightGray","#D3D3D3"=>"LightGrey","#90EE90"=>"LightGreen","#FFB6C1"=>"LightPink","#FFA07A"=>"LightSalmon","#20B2AA"=>"LightSeaGreen","#87CEFA"=>"LightSkyBlue","#778899"=>"LightSlateGray","#778899"=>"LightSlateGrey","#B0C4DE"=>"LightSteelBlue","#FFFFE0"=>"LightYellow","#00FF00"=>"Lime","#32CD32"=>"LimeGreen","#FAF0E6"=>"Linen","#FF00FF"=>"Magenta","#800000"=>"Maroon","#66CDAA"=>"MediumAquaMarine","#0000CD"=>"MediumBlue","#BA55D3"=>"MediumOrchid","#9370DB"=>"MediumPurple","#3CB371"=>"MediumSeaGreen","#7B68EE"=>"MediumSlateBlue","#00FA9A"=>"MediumSpringGreen","#48D1CC"=>"MediumTurquoise","#C71585"=>"MediumVioletRed","#191970"=>"MidnightBlue","#F5FFFA"=>"MintCream","#FFE4E1"=>"MistyRose","#FFE4B5"=>"Moccasin","#FFDEAD"=>"NavajoWhite","#000080"=>"Navy","#FDF5E6"=>"OldLace","#808000"=>"Olive","#6B8E23"=>"OliveDrab","#FFA500"=>"Orange","#FF4500"=>"OrangeRed","#DA70D6"=>"Orchid","#EEE8AA"=>"PaleGoldenRod","#98FB98"=>"PaleGreen","#AFEEEE"=>"PaleTurquoise","#DB7093"=>"PaleVioletRed","#FFEFD5"=>"PapayaWhip","#FFDAB9"=>"PeachPuff","#CD853F"=>"Peru","#FFC0CB"=>"Pink","#DDA0DD"=>"Plum","#B0E0E6"=>"PowderBlue","#800080"=>"Purple","#FF0000"=>"Red","#BC8F8F"=>"RosyBrown","#4169E1"=>"RoyalBlue","#8B4513"=>"SaddleBrown","#FA8072"=>"Salmon","#F4A460"=>"SandyBrown","#2E8B57"=>"SeaGreen","#FFF5EE"=>"SeaShell","#A0522D"=>"Sienna","#C0C0C0"=>"Silver","#87CEEB"=>"SkyBlue","#6A5ACD"=>"SlateBlue","#708090"=>"SlateGray","#708090"=>"SlateGrey","#FFFAFA"=>"Snow","#00FF7F"=>"SpringGreen","#4682B4"=>"SteelBlue","#D2B48C"=>"Tan","#008080"=>"Teal","#D8BFD8"=>"Thistle","#FF6347"=>"Tomato","#40E0D0"=>"Turquoise","#EE82EE"=>"Violet","#F5DEB3"=>"Wheat","#FFFFFF"=>"White","#F5F5F5"=>"WhiteSmoke","#FFFF00"=>"Yellow","#9ACD32"=>"YellowGreen");
		$arrColors = array_map('strtolower', $arrColors);
		$arrColors = array_flip($arrColors);
	//	echo "<pre>";print_r($arrColors);
		if(strtolower($attrVal) == 'gray' || strtolower($attrVal) == 'grey') $attrVal = 'grey';
		if(strtolower($attrVal) == 'lightgray' || strtolower($attrVal) == 'lightgrey') $attrVal = 'lightgrey';
		if(strtolower($attrVal) == 'darkgray' || strtolower($attrVal) == 'darkgrey') $attrVal = 'darkgrey';
		if(strtolower($attrVal) == 'darkslategray' || strtolower($attrVal) == 'darkslategrey') $attrVal = 'darkslategrey';
		if(strtolower($attrVal) == 'lightslategray' || strtolower($attrVal) == 'lightslategrey') $attrVal = 'lightslategrey';
		if(array_key_exists(strtolower($attrVal),$arrColors)){
				return"<td class='allvals' data-vals ='". strtolower($arrColors[$attrVal]) ."' style='text-align: center;' width='58%'><input type='text' name='attribute_value[]' class='jscolor {hash:true}'  value='". $arrColors[$attrVal] ."' style='width:50%' onclick='update(this,this.jscolor)' onblur='updateCss(this," . $attrId.");'/><td>";
		}
		else{
				$chkColorValue = checkValueForColor($attrVal,$attrId);
				if(!$chkColorValue)
					return "<td class='allvals' data-vals ='". str_replace("'","",$attrVal) ."' style='text-align: center;' width='58%'><input type='text' name='attribute_value[]' class ='attribute_value' value='". $attrVal ."' style='width:50%' onclick='update(this,this.jscolor)' onblur='updateCss(this," . $attrId.");'/></td>";
				else
					return $chkColorValue;
					//return "<td class='allvals' data-vals ='". str_replace("'","",$attrVal) ."' style='text-align: center;' width='58%'><input type='text' name='attribute_value[]' class ='attribute_value' value='". $attrVal ."' style='width:50%' onblur='updateCss(this," . $attrId.");'/></td>";
		}
	}
	function checkValueForColor($val,$id){
		if($val=="") return false;
	$arrVals = explode(" ",$val);
		
		if(count($arrVals)<=1){
			false;
		}
		$arrColors = array("#F0F8FF"=>"AliceBlue","#FAEBD7"=>"AntiqueWhite","#00FFFF"=>"Aqua","#7FFFD4"=>"Aquamarine","#F0FFFF"=>"Azure","#F5F5DC"=>"Beige","#FFE4C4"=>"Bisque","#000000"=>"Black","#FFEBCD"=>"BlanchedAlmond","#0000FF"=>"Blue","#8A2BE2"=>"BlueViolet","#A52A2A"=>"Brown","#DEB887"=>"BurlyWood","#5F9EA0"=>"CadetBlue","#7FFF00"=>"Chartreuse","#D2691E"=>"Chocolate","#FF7F50"=>"Coral","#6495ED"=>"CornflowerBlue","#FFF8DC"=>"Cornsilk","#DC143C"=>"Crimson","#00FFFF"=>"Cyan","#00008B"=>"DarkBlue","#008B8B"=>"DarkCyan","#B8860B"=>"DarkGoldenRod","#A9A9A9"=>"DarkGray","#A9A9A9"=>"DarkGrey","#006400"=>"DarkGreen","#BDB76B"=>"DarkKhaki","#8B008B"=>"DarkMagenta","#556B2F"=>"DarkOliveGreen","#FF8C00"=>"Darkorange","#9932CC"=>"DarkOrchid","#8B0000"=>"DarkRed","#E9967A"=>"DarkSalmon","#8FBC8F"=>"DarkSeaGreen","#483D8B"=>"DarkSlateBlue","#2F4F4F"=>"DarkSlateGray","#2F4F4F"=>"DarkSlateGrey","#00CED1"=>"DarkTurquoise","#9400D3"=>"DarkViolet","#FF1493"=>"DeepPink","#00BFFF"=>"DeepSkyBlue","#696969"=>"DimGray","#696969"=>"DimGrey","#1E90FF"=>"DodgerBlue","#B22222"=>"FireBrick","#FFFAF0"=>"FloralWhite","#228B22"=>"ForestGreen","#FF00FF"=>"Fuchsia","#DCDCDC"=>"Gainsboro","#F8F8FF"=>"GhostWhite","#FFD700"=>"Gold","#DAA520"=>"GoldenRod","#808080"=>"Gray","#808080"=>"Grey","#008000"=>"Green","#ADFF2F"=>"GreenYellow","#F0FFF0"=>"HoneyDew","#FF69B4"=>"HotPink","#CD5C5C"=>"IndianRed","#4B0082"=>"Indigo","#FFFFF0"=>"Ivory","#F0E68C"=>"Khaki","#E6E6FA"=>"Lavender","#FFF0F5"=>"LavenderBlush","#7CFC00"=>"LawnGreen","#FFFACD"=>"LemonChiffon","#ADD8E6"=>"LightBlue","#F08080"=>"LightCoral","#E0FFFF"=>"LightCyan","#FAFAD2"=>"LightGoldenRodYellow","#D3D3D3"=>"LightGray","#D3D3D3"=>"LightGrey","#90EE90"=>"LightGreen","#FFB6C1"=>"LightPink","#FFA07A"=>"LightSalmon","#20B2AA"=>"LightSeaGreen","#87CEFA"=>"LightSkyBlue","#778899"=>"LightSlateGray","#778899"=>"LightSlateGrey","#B0C4DE"=>"LightSteelBlue","#FFFFE0"=>"LightYellow","#00FF00"=>"Lime","#32CD32"=>"LimeGreen","#FAF0E6"=>"Linen","#FF00FF"=>"Magenta","#800000"=>"Maroon","#66CDAA"=>"MediumAquaMarine","#0000CD"=>"MediumBlue","#BA55D3"=>"MediumOrchid","#9370DB"=>"MediumPurple","#3CB371"=>"MediumSeaGreen","#7B68EE"=>"MediumSlateBlue","#00FA9A"=>"MediumSpringGreen","#48D1CC"=>"MediumTurquoise","#C71585"=>"MediumVioletRed","#191970"=>"MidnightBlue","#F5FFFA"=>"MintCream","#FFE4E1"=>"MistyRose","#FFE4B5"=>"Moccasin","#FFDEAD"=>"NavajoWhite","#000080"=>"Navy","#FDF5E6"=>"OldLace","#808000"=>"Olive","#6B8E23"=>"OliveDrab","#FFA500"=>"Orange","#FF4500"=>"OrangeRed","#DA70D6"=>"Orchid","#EEE8AA"=>"PaleGoldenRod","#98FB98"=>"PaleGreen","#AFEEEE"=>"PaleTurquoise","#DB7093"=>"PaleVioletRed","#FFEFD5"=>"PapayaWhip","#FFDAB9"=>"PeachPuff","#CD853F"=>"Peru","#FFC0CB"=>"Pink","#DDA0DD"=>"Plum","#B0E0E6"=>"PowderBlue","#800080"=>"Purple","#FF0000"=>"Red","#BC8F8F"=>"RosyBrown","#4169E1"=>"RoyalBlue","#8B4513"=>"SaddleBrown","#FA8072"=>"Salmon","#F4A460"=>"SandyBrown","#2E8B57"=>"SeaGreen","#FFF5EE"=>"SeaShell","#A0522D"=>"Sienna","#C0C0C0"=>"Silver","#87CEEB"=>"SkyBlue","#6A5ACD"=>"SlateBlue","#708090"=>"SlateGray","#708090"=>"SlateGrey","#FFFAFA"=>"Snow","#00FF7F"=>"SpringGreen","#4682B4"=>"SteelBlue","#D2B48C"=>"Tan","#008080"=>"Teal","#D8BFD8"=>"Thistle","#FF6347"=>"Tomato","#40E0D0"=>"Turquoise","#EE82EE"=>"Violet","#F5DEB3"=>"Wheat","#FFFFFF"=>"White","#F5F5F5"=>"WhiteSmoke","#FFFF00"=>"Yellow","#9ACD32"=>"YellowGreen");
		$arrColors = array_map('strtolower', $arrColors);
		$arrColors = array_flip($arrColors);
	
		foreach($arrVals as $key=>$value){
			
			if(strstr($value,'!') !== false){
				$impVals = explode("!",$value);
				
				if(strtolower($impVals[0]) == 'gray' || strtolower($impVals[0]) == 'grey') $impVals[0] = 'grey';
				if(strtolower($impVals[0]) == 'lightgray' || strtolower($impVals[0]) == 'lightgrey') $impVals[0] = 'lightgrey';
				if(strtolower($impVals[0]) == 'darkgray' || strtolower($impVals[0]) == 'darkgrey') $impVals[0] = 'darkgrey';
				if(strtolower($impVals[0]) == 'darkslategray' || strtolower($impVals[0]) == 'darkslategrey') $impVals[0] = 'darkslategrey';
				if(strtolower($impVals[0]) == 'lightslategray' || strtolower($impVals[0]) == 'lightslategrey') $impVals[0] = 'lightslategrey';
				
				if(array_key_exists($impVals[0],$arrColors)){
						return"<td class='allvals' data-vals ='". $val ."' style='text-align: center;' width='58%'><input type='text' id='styleElement_". $id ."' name='attribute_value[]'  class='jscolor
						{valueElement:\"valueInput_".$id."\", styleElement:\"styleElement_". $id ."\"}' value='". $val ."' style='width:50%'  onclick='update(this,this.jscolor)' onblur='updateCss(this," . $id.");'/><input type='hidden' id='valueInput_".$id ."' value='". $arrColors[$impVals[0]] ."'><td>"; 
				}
				
				if((strstr($impVals[0],'#') !== false)){
					return"<td class='allvals' data-vals ='". $val ."' style='text-align: center;' width='58%'><input type='text' id='styleElement_". $id ."' name='attribute_value[]'  class='jscolor
						{valueElement:\"valueInput_".$id."\", styleElement:\"styleElement_". $id ."\"}' value='". $val ."' style='width:50%' onclick='update(this,this.jscolor)' onblur='updateCss(this," . $id.");'/><input type='hidden' id='valueInput_".$id ."' value='". str_replace("#","",$impVals[0]) ."'><td>";
				}

			}
			else{
				if(strtolower($value) == 'gray' || strtolower($value) == 'grey') $value = 'grey';
				if(strtolower($value) == 'lightgray' || strtolower($value) == 'lightgrey') $value = 'lightgrey';
				if(strtolower($value) == 'darkgray' || strtolower($value) == 'darkgrey') $value = 'darkgrey';
				if(strtolower($value) == 'darkslategray' || strtolower($value) == 'darkslategrey') $value = 'darkslategrey';
				if(strtolower($value) == 'lightslategray' || strtolower($value) == 'lightslategrey') $value = 'lightslategrey';
				
				if(array_key_exists($value,$arrColors)){
						return"<td class='allvals' data-vals ='". $val ."' style='text-align: center;' width='58%'><input type='text' id='styleElement_". $id ."' name='attribute_value[]'  class='jscolor
						{valueElement:\"valueInput_".$id."\", styleElement:\"styleElement_". $id ."\"}'value='". $val ."' style='width:50%' onclick='update(this,this.jscolor)'  onblur='updateCss(this," . $id.");'/><input type='hidden' id='valueInput_".$id ."' value='". $arrColors[$value] ."'><td>"; 
				}
				
				if((strstr($value,'#') !== false)){
				return"<td class='allvals' data-vals ='". $val ."' style='text-align: center;' width='58%'><input type='text' id='styleElement_". $id ."' name='attribute_value[]'  class='jscolor
						{valueElement:\"valueInput_".$id."\", styleElement:\"styleElement_". $id ."\"}' value='". $val ."' style='width:50%'  onclick='update(this,this.jscolor)'  onblur='updateCss(this," . $id.");'/><input type='hidden' id='valueInput_".$id ."' value='". str_replace("#","",$value) ."'><td>";
				}
			}
			
			
			/* if(strpos("#",$value) !== false || array_key_exists(strtolower($value),$arrColors)){
				return"<td class='allvals' data-vals ='". strtolower($arrColors[$value]) ."' style='text-align: center;' width='58%'><input type='text' name='attribute_value[]' class='jscolor {hash:true}'  value='". $val ."' style='width:50%' onchange='updateCss(this," . $id.");'/><td>";
			}
			return false; */
		}
		
		return false;
		
	}
	
?>
