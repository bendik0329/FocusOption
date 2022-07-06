<?php

function convertSize($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

//ini_set('display_errors', '1');
//ini_set('error_reporting', E_ALL ^ E_NOTICE);

$globalCounter=0;

/**
 * Filter some dangerous phrases from sql requests
 * @param $query
 * @return bool|string
 */
function sqlMalWareFilter($query)
{
    $queryString = strtolower($query);
    $injections  = array("select*", "and sleep","for delay", "create user", "grand", "flush", ";select ", ";where", ";drop", ";alter", "char(", "avdsinjectionheader", "var _object = document.querySelector");

    foreach ($injections as $injection) {
        if (strpos($queryString, $injection)) {
            writeToLog('---MALWARE-SQL-ATTEMPT-----');
            writeToLog('SQL: '.$queryString);
            writeToLog('Injection issue: '.$injection);
            writeToLog('-----------');

            return false;
        }
    }

    return $query;
}

function function_mysql_query($query,$origin="",$functionName="") {

	global $globalCounter;
	

	if (false){
		writeToLog('-----------');
		writeToLog($query);
		writeToLog($origin);
		writeToLog($functionName);
		writeToLog('-----------');
	}
		
	// $_GET['dbg1']=1;
	
	$debug=false;
	if ($_GET['dbg1']) {
		
		if ($globalCounter==0){
			if (!$justQueries){
				echo 'query'. '|' . 'counter'. '|'. 'origin'. '|'. 'functionName'. '|'.'line memory'.'|'.'memory'.'|'.'seconds'.'|'.'timestamp'.'<br>';
			}
			else {
				echo 'query'. '|' . 'Counter'. '|'. 'origin'. '|'. 'functionName'. '|' . 'memory' .'|'.'timestamp'.'<br>';
			}
		}
			
		
		$debug=true;
	}
		
	$justQueries=false;
	if ($_GET['jq']==1)
		$justQueries=true;
		

	if ($_GET['dbg1']>0 && !$justQueries)	{
		$memoryBefore	=memory_get_usage(true);
		$starttime = microtime(true);
	}

	if ($justQueries || $_GET['dbg1']>0){
		$globalCounter++;
	}
	
	if ($justQueries && $_GET['dbg1']>0){
		echo $query. '|' . $globalCounter. '|'. $origin. '|'. $functionName. '|' . convertSize(memory_get_usage(true)) .'|'.date('Y-m-d H:i:s').'<br>';
	}

	$query = sqlMalWareFilter($query);
	$rsc = mysql_query($query) ;//or die(mysql_error());
	
			
		if ($_GET['dbg1']>0 && !$justQueries)	{
			 $endtime = microtime(true);
			$seconds = $endtime-$starttime;
			echo $query. '|' . $globalCounter. '|'. $origin. '|'. $functionName. '|'.convertSize(bcsub(memory_get_usage(),$memoryBefore,3)).'|'.convertSize(memory_get_usage(true)).'|'.$seconds.'|'.date('Y-m-d H:i:s').'<br>';
		}
	return $rsc;
}


function output_memory($description,$origin="",$functionName="") {
	
	global $globalCounter;
	$justQueries = false;
	$debug=false;
	if ($_GET['dbg1']==2)
		$debug=true;
		
	
	if ($debug){
	$globalCounter++;
	 
	}
	
	
	
if ($_GET['dbg1']==2 && !$justQueries)	
	echo $description. '|' . $globalCounter. '|'. $origin. '|'. $functionName. '|'.'|'.convertSize(memory_get_usage(true)).'|'.$seconds.'|'.date('Y-m-d H:i:s').'<br>';

	return $rsc;
}


	
?>