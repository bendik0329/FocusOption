<?php

require_once('common/global.php');

//SQL to fetch banner stats for current and previous month
$sql = 'SELECT * FROM `stats_banners` WHERE rdate BETWEEN (CURRENT_DATE() - INTERVAL 1 MONTH) AND CURRENT_DATE()';
$results = function_mysql_query($sql,__FILE__);
try{
		if(!empty($results)){
			
			//loop to go through banner stats data
			$bannersArray = array();
			$r = 0;
			while($row = mysql_fetch_assoc($results)){
				//preparing an array
				$bannersArray[$r]['rdate'] = date('Y-m-d H:i:s', strtotime($row['rdate']));
				$bannersArray[$r]['ctag'] = $row['ctag'];
				$bannersArray[$r]['affiliate_id'] = $row['affiliate_id'];
				$bannersArray[$r]['group_id'] = $row['group_id'];
				$bannersArray[$r]['merchant_id'] = $row['merchant_id'];
				$bannersArray[$r]['banner_id'] = $row['banner_id'];
				$bannersArray[$r]['profile_id'] = $row['profile_id'];
				$bannersArray[$r]['views'] = $row['views'];
				$bannersArray[$r]['clicks'] = $row['clicks'];
				$r++;
			}
			
			// preparing SQL for each row for multiple insert in single query
			$sql = array(); 
			foreach( $bannersArray as $row ) {
				$sql[] = '("'.$row['rdate'].'", "'.$row['ctag'].'",' . $row['affiliate_id'] .',' . $row['group_id'] .',' . $row['merchant_id'] .',' . $row['banner_id'] .',' . $row['profile_id'] .',' . $row['views'] .',' . $row['clicks'] .')';
			}
			
			//Query to insert data in TRAFFIC table
			$qry = 'INSERT INTO traffic (rdate,ctag,affiliate_id,group_id,merchant_id,banner_id,profile_id,views,clicks) VALUES '.implode(',', $sql);
			function_mysql_query($qry,__FILE__);
			
			echo "INSERTED <b>". count($bannersArray) ."</b> ROWS FROM <b>STATS_BANNER</b> TABLE TO <b>TRAFFIC</b> TABLE.";
			die;
		}
		else{
			echo "NO DATA FOUND.";
		}
}
catch(Exception $e){
	
	echo "There is error while fetching and inserting data.";
	die;
}

?>