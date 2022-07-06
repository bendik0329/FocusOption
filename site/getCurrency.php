<?php
	require_once('common/database.php');
	function getTag($tag, $endtag, $xml) {
		if (!$endtag) 
			$endtag=$tag;
		preg_match_all("/".$tag."(.*?)".$endtag."/", $xml, $matches);
		return $matches[1][0];
	}
	$url = 'http://exchanger.affiliatets.com/feed.xml';
	$xml_report = file_get_contents($url) or die("Feed not working");
	$find = Array("\n","\t"," ");
	$replace = Array("","","");
	$xml_report = str_replace($find,$replace,$xml_report);
	preg_match_all("/<curr>(.*?)<\/curr>/",$xml_report,$xml);
	$sets = '';
	$i=0;
	foreach($xml[1] AS $xml_line) {
		$currKey = getTag('<currKey>','<\/currKey>',$xml_line);
		$fromCurr = getTag('<fromCurr>','<\/fromCurr>',$xml_line);
		$toCurr = getTag('<toCurr>','<\/toCurr>',$xml_line);
		$val = getTag('<val>','<\/val>',$xml_line);
		$lastUpdate = getTag('<lastUpdate>','<\/lastUpdate>',$xml_line);
		
		if($i>0){
			$sets.=',';
		}
		
		$i=1;
	
		$sets.='("'.$currKey.'",';
		$sets.='"'.$fromCurr.'",';
		$sets.='"'.$toCurr.'",';
		$sets.=$val;
		$sets.=',';
		$sets.='NOW())';
	
	}
	
	//echo 'INSERT INTO exchange_rates (currKey, fromCurr, toCurr, val, lastUpdate) VALUES '.$sets.' ON DUPLICATE KEY UPDATE val=VALUES(val)';
	$qry = 'INSERT INTO exchange_rates (currKey, fromCurr, toCurr, val, lastUpdate) VALUES '.$sets.' ON DUPLICATE KEY UPDATE val=VALUES(val), lastUpdate=NOW();';
	if ($_GET['debug']==1)
		echo $qry . '<br>';
	mysql_query($qry); //OR die(mysql_error());
	echo 'Currencies are up to date!';
	
?>