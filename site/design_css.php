<?php
require_once('common/global.php');
if (!isAdmin()) _goto('/admin/');
switch ($act) {
	case "generate":
			$class="";
			foreach($html_attribite_type as $key=>$row){
					if($class !=$row)
						{
							if($key!=0){
								$css .= "}";
							}
								$css .= $row. "{";
								$css .= $attribute_name[$key] . ":".$attribute_value[$key] .";";
								
								$class = $row;
						}
						else{
							$css .= $attribute_name[$key] . ":".$attribute_value[$key] .";";
							$class = $row;
						}
						
						
						if(count($html_attribite_type)-1 == $key)
						{
							$css .= "}";
						}
						$prop_value = str_replace('"','\"',$attribute_value[$key]);
						$sql = 'update design_css set attribute_value = "' . $prop_value . '" where id = ' . $attribute_id[$key];
						function_mysql_query($sql,__FILE__);
			}
			
			
			
			
			$filename = "css/style.css";
			file_put_contents($filename,$css);
			_goto($set->uri);
			break;
	default:
		$set->pageTitle = lang('Design CSS');
		$sql = "SELECT * from design_css where design_id=".$design  . " and valid =1";
		
		$results = function_mysql_query($sql,__FILE__);
		if(!empty($results)){
			$l=0;
			$set->content="<script src='js/jscolor.min.js'></script>";
			$set->content.="<script>
			function setTextColor(picker) {
				document.getElementsByTagName('body')[0].style.color = '#' + picker.toString()
			}
			</script>";
			$set->content .= '<div style="margin-bottom:50px !Important;">
			<form method="post"><input type="hidden" name="act" value="generate"><div style="width:97%;text-align:right;padding-bottom:10px;"><input type="submit" value="'.lang('Generate CSS').'"></div>';
			$set->content .= "<table class='normal' width='98%' border='0' cellpadding='3' cellspacing='0'>";
			$set->content .= "<thead>
			<tr><td>". lang('Class Name') ."</td><td>". lang('Property Name') ."</td><td>". lang('Propety value') ."</tr>
			</thead><tbody>";
			while($row = mysql_fetch_assoc($results)){
					
					
					$set->content .="<tr><input type='hidden' name='attribute_id[]' value='". $row['id'] ."'>";
					$set->content .= "<td style='text-align: center;' width='30%'>". $row['html_attribute_type'] ."<input type='hidden' name='html_attribite_type[]' value = '". $row['html_attribute_type'] ."'></td>";
					$set->content .= "<td style='text-align: center;' width='20%'>". $row['attribute_name'] ."<input type='hidden' name='attribute_name[]' value='".  $row['attribute_name']."'></td>";
					if(strpos($row['attribute_value'],'#') === 0)
					$set->content .= "<td style='text-align: center;' width='58%'><input type='text' name='attribute_value[]' class='jscolor {hash:true}'  value='". $row['attribute_value'] ."' style='width:50%'/><td>";
					else
					$set->content .= "<td style='text-align: center;' width='58%'><input type='text' name='attribute_value[]'class ='attribute_value' value='". $row['attribute_value'] ."' style='width:50%'/></td>";
					$set->content .= "</tr>";
					
					
					$l++;
				}
				$set->content .= '</tbody></table><div style="width:97%;text-align:center;margin-top:20px;"><input type="submit" value="'.lang('Generate CSS').'"></div></form></div>';
		}				
		theme();
		break; 
	
	
	
	
	}
	
	
?>
