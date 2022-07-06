<?php

/*NOTES: 
REMOVE spaces in between that class name and '{'
REMOVE comments from file
*/

require_once('common/global.php');

if($parsetype == "db" && (isset($design) && !empty($design))){
			
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
						/* elseif($row["html_attribute_type"] == '@font-face' && $a==2){
						
							$css .= "}";
							$a=0;
						} */
						else{
							if($name == "@font-face"){
								
							}
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
		$filename = "css/style_db.css";
		
		file_put_contents($filename,$css);die;
}
elseif($parsetype == "css"){
		$file = file_get_contents('css/cleaned_style.css');
        
        $element = explode('}', $file);

        foreach ($element as $element) {
		    // get the name of the CSS element
            $a_name = explode('{', $element);
            $name = trim($a_name[0]);

            // get all the key:value pair styles
            $a_styles = explode(';', $element);

            // remove element name from first property element
            $a_styles[0] = str_replace($name . '{', '', $a_styles[0]);

            // loop through each style and split apart the key from the value
            $count = count($a_styles);
			
		    for ($a=0; $a<=$count-1; $a++) {
                if (trim($a_styles[$a]) != '') {
                    $a_key_value = explode(':', $a_styles[$a]);		
                    // build the master css array
					//echo $a_key_value[0] . "<br/>";
					if($name == '@font-face'){
					if(isset($css_array[$name][trim($a_key_value[0])])){
						$val = $css_array[$name][trim($a_key_value[0])];
						if($val != trim($a_key_value[1])){
							
								$testarra[$name][trim($a_key_value[0])] = trim($a_key_value[1]);
								
						}
						else{

						}
					}
					else{
                    $css_array[$name][trim($a_key_value[0])] = trim($a_key_value[1]);
					}
					}
					else{
						$css_array[$name][trim($a_key_value[0])] = trim($a_key_value[1]);
					}
                }
            }               
        }
	
	if(isset($testarra) || !empty($testarra)){
	
			$sql = array();
			foreach($testarra as $classname=>$properties)
			{
				foreach($properties as $prop_name=>$prop_value){
					
					if(strpos($classname,"#") !== false) 
						$type = "id";
					elseif(strpos($classname,".") !== false) 
						$type = "class";
					else
						$type = "attribute";
				
						$prop_value = str_replace('"','\"',$prop_value);
						//echo $prop_value;
						$sql[] = '(1, "'.$type.'","' . $classname.'","' . $prop_name .'","' . $prop_value .'")';
					
				}
			}
			
			if(!empty($sql)){
				$qry = 'INSERT INTO design_css (design_id,type,html_attribute_type,attribute_name,attribute_value) VALUES '.implode(',', $sql);
				//echo $qry;die;
				function_mysql_query($qry,__FILE__);
				
				echo count($sql) . " Rows inserted in design_css table.";
				
			}
			
	}
	if(isset($css_array) || !empty($css_array)){
	
			$sql = array();
			foreach($css_array as $classname=>$properties)
			{
				foreach($properties as $prop_name=>$prop_value){
					
					if(strpos($classname,"#") !== false) 
						$type = "id";
					elseif(strpos($classname,".") !== false) 
						$type = "class";
					else
						$type = "attribute";
				
						$prop_value = str_replace('"','\"',$prop_value);
						//echo $prop_value;
						$sql[] = '(1, "'.$type.'","' . $classname.'","' . $prop_name .'","' . $prop_value .'")';
					
				}
			}
			
			if(!empty($sql)){
				$qry = 'INSERT INTO design_css (design_id,type,html_attribute_type,attribute_name,attribute_value) VALUES '.implode(',', $sql);
				//echo $qry;die;
				function_mysql_query($qry,__FILE__);
				
				echo count($sql) . " Rows inserted in design_css table.";
				
			}
	}
	
	die;
	
}

?>