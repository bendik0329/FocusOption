<?php 
@session_start();

require '../common/database.php';
require '../common/config.php';
require '../func/func_string.php';
require '../func/func_global.php';
require '../func/func_form.php';
require '../func/func_db.php';

global $set;

if(isset($_GET['countryPieDays']) && $_GET['countryPieDays'] != null && is_numeric($_GET['countryPieDays']))
{
        $today_date = date('Y-m-d');
		$past_date = date('Y-m-d',strtotime('-'.$_GET['countryPieDays'].'day'));
        $data = mysql_query('SELECT COUNT(CountryID) as country_count, CountryID as country_iso2 FROM merchants_creative_stats WHERE Date >= "'.$past_date.'" AND Date <= "'.$today_date.'" AND AffiliateID = '.$_GET['data_id'].' GROUP BY CountryID');
        $data_result_x = [];
		$data_result_y = [];
		if (mysql_num_rows($data) > 0) {
			while($row = mysql_fetch_assoc($data)) {
				$data_result_x[] = '"'.$row['country_iso2'].'"';
				$data_result_y[] = $row['country_count'];
                echo json_encode(['x_axis'=> $data_result_x,'y_axis'=>$data_result_y]);
			}
		} else {
            echo json_encode(['x_axis'=>"data is not availble"]);
        }
}

?>