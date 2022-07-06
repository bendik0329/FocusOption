<?php
include '../common/database.php';
include '../func/func_views.php';
include '../func/func_global.php';
include '../func/func_debug.php';
include 'AffiliateTS.php';
?>
<html>
    <head>
        <title>Cron Job</title>
    </head>
    <body>    
        <?php
        try {
            $fromDate = date('Y-m-d');
            $toDate = date('Y-m-d', strtotime($fromDate . ' +1 day'));

            $merchantId = (int) $_REQUEST['merchantId'];

            $api = new AffiliateTS($merchantId, $ss->db_hostname, $ss->db_username, $ss->db_password, $ss->db_name);

            // 1) Traders
            if (!empty($_REQUEST['fromDate'])) {
                $fromDate = date('Y-m-d', strtotime($_REQUEST['fromDate']));
                $toDate = date('Y-m-d', strtotime($fromDate . ' +1 day'));
            }

            if ($fromDate > $toDate) {
                die('Error incorrect dates');
            }
            
            ?>
        
            <p>
                <b>From</b>: <?= $fromDate;?><br>
                <b>To</b>: <?= $toDate;?>
            </p>
        
            <?php
            
            if ($fromDate > $toDate) {
                die('Error incorrect dates');
            }


            $result = $api->integrations()->getLeads([
                'FromDate' => $fromDate,
                'ToDate' => $toDate,
            ]);

            if (!empty($_GET['debug'])) {
                echo "<pre>";
                var_dump($result);
                echo "</pre>";
            }


            $message = [];

            foreach ($result as $item) {
                $message[$item['trader_id']] = $api->internal()->createCustomer($item['created_date'], $item['btag'], $item['sale_status'], $item['country'], $item['trader_id'], $item['client_status'], $item['email'], $item['first_name'].' '.$item['last_name']);
            }

            echo "<pre>";
            print_r($message);
            echo "</pre>";

            echo '<br>----------------------<br>';

            $errors = [];

            $result = $api->integrations()->getTransactions([
                'FromDate' => $fromDate,
                'ToDate' => $toDate,
            ]);

            if (!empty($_GET['debug'])) {
                echo "<pre>";
                var_dump($result);
                echo "</pre>";
            }


            $message = [];
            foreach ($result as $item) {
                $message[$item['trader_id'] . '-' . $item['transaction_id']] = $api->internal()->createTransaction($item['created_time'], $item['trader_id'], $item['transaction_id'], $item['transaction_type'], $item['currency'], $item['amount']);
            }

            echo "<pre>";
            print_r($message);
            echo "</pre>";

            updateInitialFTD(1);
            updateTraderValue(0);
            getNonQualifiedFTD(0, date('Y-m-d H:i:s', strtotime('-1 year', strtotime(date('Y-m-d H:i:s')))));
            normalizeAffiliatesGroupId();
        } catch (Exception $e) {
            die($e->getMessage());
        }
        ?>
    </body>
</html>
