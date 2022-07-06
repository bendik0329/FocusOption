<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
function dbGet($db,$tb="") {
	global $set;
	if ($db AND $tb) $qq=function_mysql_query("SELECT * FROM ".$tb." WHERE id='".$db."'",__FILE__,__FUNCTION__);
		else return false;
	$ww=mysql_fetch_assoc($qq);
	return $ww;
	}

function dbAdd($db, $tb = "", $arrOptions = array()) {
	global $set;
	if (!$tb) $tb = $db['tb'];
	if (!$tb) return false;
	$num = 0;
        
	if ($db['id']) {
            $num=mysql_result(function_mysql_query("SELECT COUNT(id) FROM ".$tb." WHERE id='".$db['id']."'",__FILE__,__FUNCTION__),0);
        }
        
	if ($num > 0) {
            $id = dbUpdate($db, $tb);
        } else {
            $id = dbInsert($db, $tb, $arrOptions);
        }
        
	return $id;
}
	
	
function dbAdd2($db,$tb="") {
	global $set;
	if (!$tb) $tb = $db['tb'];
	if (!$tb) return false;
	$num = 0;
	if ($db['id']) $num=mysql_result(function_mysql_query("SELECT COUNT(id) FROM ".$tb." WHERE id='".$db['id']."'",__FILE__,__FUNCTION__),0);
	if ($num > 0) $id = dbUpdate2($db,$tb);
		 else $id = dbInsert2($db,$tb);

	return $id;
}
	

	

function dbUpdate($db,$tb="") {
	global $set;
	
	if (!$tb) $tb = $db['tb'];
	$chkF = array_keys($db);
	foreach ($db AS $k => $v) $chkF[$k] = 1;
	$qq = function_mysql_query("SHOW FIELDS FROM ".$tb,__FILE__,__FUNCTION__);
	$arr = Array();
	while ($ww=mysql_fetch_assoc($qq)) {
		$arr[] = $ww['Field'];
	}

		for ($i=0; $i<=count($arr)-1; $i++) 
		if ($arr[$i] != "id" AND $chkF[$arr[$i]]) 
			$portQuery[] = $arr[$i]."='". mysql_real_escape_string($db[$arr[$i]])."'";
		
	if (count($arr) >= 1) $portQuery = implode(",", $portQuery);
	$query = "UPDATE ".$tb." SET ".$portQuery." WHERE id='".$db['id']."'";
	//baba($query,1,1);
	//die($query);
	function_mysql_query($query,__FILE__,__FUNCTION__);
	
	return $db['id'];
	}
	
	
function dbUpdate2($db,$tb="") {
	global $set;
	
	if (!$tb) $tb = $db['tb'];
	$chkF = array_keys($db);
	foreach ($db AS $k => $v) $chkF[$k] = 1;
	$qq = function_mysql_query("SHOW FIELDS FROM ".$tb,__FILE__,__FUNCTION__);
	$arr = Array();
	while ($ww=mysql_fetch_assoc($qq)) {
		$arr[] = $ww['Field'];
	}

	for ($i=0; $i<=count($arr)-1; $i++) if ($arr[$i] != "id" AND $chkF[$arr[$i]]) $portQuery[] = $arr[$i]."='".$db[$arr[$i]]."'";
	if (count($arr) >= 1) $portQuery = implode(",", $portQuery);
	$query = "UPDATE ".$tb." SET ".$portQuery." WHERE id='".$db['id']."'";
	die($query);
	function_mysql_query($query,__FILE__,__FUNCTION__);
	
	return $db['id'];
	}
	
	
function dbInsert($db, $tb = "", $arrOptions = array()) {
	global $set;
        
	if (count($db) >= 1) {
		foreach ($db as $key => $val) {
			if ($key != 'id') {
				if (in_array($key, $arrOptions)) {
					$befValues[] = $key;
					$portQuery[] = "'" . mysql_real_escape_string($val) . "'";
				} else {
				    if ($key != 'sub_com_level') {
                        $befValues[] = $key;
                        $portQuery[] = "'" . mysql_real_escape_string(LatinReplace($val)) . "'";
                    }

				}
			}
		}
		$befValues = implode(",", $befValues);
		$portQuery = implode(",", $portQuery);
	} else {
		return false;
	}
	$query = "INSERT INTO ".$tb." (".$befValues.") VALUES (".$portQuery.")";
        // die($query);///////////////////////////////////////////////////////////////////////
	if ($befValues AND $portQuery) function_mysql_query($query,__FILE__,__FUNCTION__);
	return mysql_insert_id();
}


function dbInsert2($db,$tb="") {
	global $set;

	if (count($db) >= 1) {
		foreach ($db AS $key => $val) {
			if ($key != 'id') {
				$befValues[]=$key;
				$portQuery[]="'".LatinReplace($val)."'";
				}
			}
		$befValues = implode(",", $befValues);
		$portQuery = implode(",", $portQuery);
		} else return false;

	$query = "INSERT INTO ".$tb." (".$befValues.") VALUES (".$portQuery.")";
	die($query);
	if ($befValues AND $portQuery) function_mysql_query($query,__FILE__,__FUNCTION__);
	return mysql_insert_id();
}
	
	
function dbDelete($id,$tb) {
	function_mysql_query("DELETE FROM ".$tb." WHERE id='".$id."'",__FILE__,__FUNCTION__);
	}
	
function dbValid($id,$tb) {
	global $set;
	$qq=function_mysql_query("SELECT * FROM ".$tb." WHERE id='".$id."'",__FILE__,__FUNCTION__);
	$ww=mysql_fetch_assoc($qq);
	if ($ww[valid] == "1") $valid=0; else $valid=1;
	function_mysql_query("UPDATE ".$tb." SET valid='".$valid."' WHERE id='".$id."'",__FILE__,__FUNCTION__);
	return $valid;
	}
	
function dbDate($date="") {
	if ($date == "0000-00-00 00:00:00") return false;
	if ($date) $date=date("d/m/Y H:i", strtotime($date));
		else $date=date("Y-m-d H:i:s", time());
	return $date;
	}
	
function UpdateUnit($tbl, $string, $where) {
	$sql="UPDATE ".$tbl." SET ".$string." WHERE ".$where;
	function_mysql_query($sql,__FILE__,__FUNCTION__);
	}

function xvPic($num,$type='') {
	global $set;
	if ($type) {
		if ($num==1 AND $type == "1") {
				$pic='v'; 
		}
			else if ($num==-1)
				$pic='x2';
			else 
				$pic='x';
			
			
			
		} else {
		if ($num==1) {
			$pic='v';	
		}
		else if ($num==-1)
			$pic='x2';
		else if ($num==-2)
			$pic='xgray';
		else 
			$pic='x';
		
		}
		// die (' .   ' . $pic);
	return '<img border="0" src="'.$set->SSLprefix.'images/'.$pic.'.png" alt="" style="vertical-align: middle;" />';
	}
	
	


function clearInjection($param,$type='varchar') {
	if ($param=='') {
		return "";
	}
	if ($type=='int') {
		if (is_numeric($param)) {
			return $param;
		}
		else {
			return -99;
		}
	}
	else {
		$param = mysql_real_escape_string($param);
		return $param;
	}
}

function get_enum_values( $table, $field )
{
		
    $qry = "SHOW COLUMNS FROM {$table} WHERE Field = '{$field}'" ;
	// die ($qry);
	$type = mysql_fetch_assoc(mysql_query( $qry));
    $type = $type['Type'];
	preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $enum = explode("','", $matches[1]);
    return $enum;
}

?>