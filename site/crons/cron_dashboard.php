<?php

ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 900);

require(__DIR__ .'/../common/database.php');
require(__DIR__ .'/../func/func_debug.php');


$fromDate = (strtotime($_GET['fromDate']))?date('Y-m-d',strtotime($_GET['fromDate'])):date('Y-m-d');
$toDate = (strtotime($_GET['toDate']))?date('Y-m-d',strtotime($_GET['toDate'])):date('Y-m-d');



if($_GET['yesterday'] == 1){
    $uxfromDate = strtotime($fromDate.' - 7 days');
    $uxtoDate = strtotime($toDate);
}else{
    $uxfromDate = strtotime($fromDate);
    $uxtoDate = strtotime($toDate);    
}

for($day = $uxfromDate;$day<=$uxtoDate;$day = ($day + (60*60*24))){
    
    $currentDay = date('Y-m-d',$day);
    
    echo "<p>".$currentDay."</p>";
    
    $DefaulInsert = function_mysql_query("insert ignore into Dashboard (MerchantID, AffiliateID, Date) select m.id,y.id,'".$currentDay."' from merchants m join (select id from affiliates where valid=1) y where m.valid=1");

    // ADD Impressions and Clicks
    function_mysql_query("update Dashboard D inner join (select t.MerchantID,t.AffiliateID,sum(Impressions) as views,sum(Clicks) as clicks FROM `merchants_creative_stats` t  where t.Date>='".$currentDay."' and t.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' group by t.MerchantID, t.AffiliateID) AS cv on     D.MerchantID = cv.MerchantID and D.AffiliateID=cv.AffiliateID and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'   SET D.Clicks=cv.clicks,D.Impressions=cv.views");

    // Installations
    function_mysql_query("update Dashboard D inner join    (select di.merchant_id, di.affiliate_id, count(1) as installations from data_install di where di.rdate>'".$currentDay."' and di.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'  group by di.merchant_id, di.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.Install = ins.installations");

    // Leads / Demo / Real
    $leads_query = "
    update Dashboard D inner join    (
        SELECT dr.merchant_id,dr.affiliate_id,r.count_type as reals,y.count_type as demos,l.count_type as leads FROM `data_reg`dr 
        left join (SELECT dr1.merchant_id,dr1.affiliate_id,count(dr1.type) as count_type,dr1.type FROM `data_reg`dr1 where dr1.rdate>='".$currentDay."' and dr1.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and dr1.type='demo' group by dr1.merchant_id, dr1.affiliate_id ) y on dr.affiliate_id=y.affiliate_id and dr.merchant_id=y.merchant_id 
        left join (SELECT dr2.merchant_id,dr2.affiliate_id,count(dr2.type) as count_type,dr2.type FROM `data_reg`dr2 where dr2.rdate>='".$currentDay."' and dr2.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and dr2.type='lead' group by dr2.merchant_id, dr2.affiliate_id ) l on dr.affiliate_id=l.affiliate_id and dr.merchant_id=l.merchant_id 
        left join (SELECT dr3.merchant_id,dr3.affiliate_id,count(dr3.type) as count_type,dr3.type FROM `data_reg`dr3 where dr3.rdate>='".$currentDay."' and dr3.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and dr3.type='real' group by dr3.merchant_id, dr3.affiliate_id ) r on dr.affiliate_id=r.affiliate_id and dr.merchant_id=r.merchant_id 
        where dr.rdate>='".$currentDay."' and dr.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' group by dr.merchant_id, dr.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.Leads = ins.leads, D.Demo = ins.demos, D.RealAccount = ins.reals";
        //echo $leads_query;
    function_mysql_query($leads_query);
    
    // FTD RAW
    //function_mysql_query("update Dashboard D inner join (SELECT ds.merchant_id,ds.affiliate_id,  count(distinct ds.trader_id) as rowftd,sum(ds.amount) as rawftdamount FROM	`data_sales` AS ds      where ds.trader_id not in  (select dso.trader_id from `data_sales` AS dso where dso.rdate < '".$currentDay."' and dso.type = 'deposit' ) and ds.rdate > '".$currentDay."' AND ds.rdate < '".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type = 'deposit' group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.RawFTD = ins.rowftd, D.RawFTDAmount = ins.rawftdamount");
    function_mysql_query("UPDATE Dashboard D INNER JOIN (SELECT dr.merchant_id,dr.affiliate_id,  COUNT(DISTINCT dr.trader_id) AS rowftd,SUM(dr.ftdamount) AS rawftdamount FROM data_reg AS dr WHERE dr.initialftddate >= '".$currentDay."' AND dr.initialftddate < '".date('Y-m-d',strtotime($currentDay.' + 1 DAY '))."' and dr.type = 'real' GROUP BY dr.merchant_id,dr.affiliate_id ) ins ON D.MerchantID = ins.merchant_id AND D.AffiliateID = ins.affiliate_id AND D.Date >= '".$currentDay."' AND D.Date < '".date('Y-m-d ',strtotime($currentDay.' + 1 DAY '))."' SET D.RawFTD = ins.rowftd, D.RawFTDAmount = ins.rawftdamount");


    // FTD
    $query_ftd = "UPDATE
    Dashboard D
INNER JOIN(
    SELECT
        dr.merchant_id,
        dr.affiliate_id,
        COUNT(DISTINCT dr.trader_id) AS ftd,
        SUM(dr.ftdamount) AS ftdamount
    FROM
        data_reg AS dr
    INNER JOIN merchants mr ON
        dr.merchant_id = mr.id
    LEFT OUTER JOIN affiliates_deals affd1 ON
        affd1.id =(
        SELECT
            id
        FROM
            affiliates_deals affd2
        WHERE
            affd2.dealType = 'min_cpa' AND affd2.rdate <= '".date('Y-m-d ',strtotime($currentDay.' + 1 DAY '))."' AND affd2.affiliate_id = dr.affiliate_id AND affd2.merchant_id = dr.merchant_id
        ORDER BY
            affd2.rdate
        DESC
    LIMIT 1
    )
WHERE
    dr.initialftddate >= '".$currentDay."' AND dr.initialftddate < '".date('Y-m-d',strtotime($currentDay.' + 1 DAY '))."' AND dr.type = 'real' AND dr.ftdamount >= IFNULL(
        affd1.amount,
        mr.min_cpa_amount
    )
GROUP BY
    dr.merchant_id,
    dr.affiliate_id
) Y
ON
    Y.merchant_id = D.MerchantID AND Y.affiliate_id = D.AffiliateID AND D.Date >= '".$currentDay."' AND D.Date < '".date('Y-m-d',strtotime($currentDay.' + 1 DAY '))."'
SET
    D.FTD = Y.ftd,
    D.FTDAmount = Y.ftdamount";
    //function_mysql_query("UPDATE Dashboard D INNER JOIN( SELECT drg.merchant_id, drg.affiliate_id, COUNT(drg.ftdamount) AS ftd, SUM(drg.ftdamount) AS ftdamount FROM `data_reg` drg WHERE FTDqualificationDate >= '".$currentDay."' AND FTDqualificationDate < '".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' GROUP BY drg.merchant_id, drg.affiliate_id ) Y ON Y.merchant_id = D.MerchantID AND Y.affiliate_id = D.AffiliateID AND D.Date >= '".$currentDay."' AND D.Date < '".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' SET D.FTD = Y.ftd, D.FTDAmount = Y.ftdamount");
    function_mysql_query($query_ftd);



    // Deposits / Bonus / Withdrawal / ChargeBack
    function_mysql_query("update Dashboard D inner join    (select ds.merchant_id,ds.affiliate_id, count(ds.amount) as count_amount,sum(ds.amount) as sum_amount from data_sales ds inner join data_reg dr on ds.trader_id=dr.trader_id and dr.type<>'demo' where ds.rdate>='".$currentDay."' and ds.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type='deposit' group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.Deposits = ins.count_amount, D.DepositsAmount = ins.sum_amount");
    function_mysql_query("update Dashboard D inner join    (select ds.merchant_id,ds.affiliate_id, count(ds.amount) as count_amount,sum(ds.amount) as sum_amount from data_sales ds inner join data_reg dr on ds.trader_id=dr.trader_id and dr.type<>'demo' where ds.rdate>='".$currentDay."' and ds.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type='bonus' group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.Bonus = ins.sum_amount");
    function_mysql_query("update Dashboard D inner join    (select ds.merchant_id,ds.affiliate_id, count(ds.amount) as count_amount,sum(ds.amount) as sum_amount from data_sales ds inner join data_reg dr on ds.trader_id=dr.trader_id and dr.type<>'demo' where ds.rdate>='".$currentDay."' and ds.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type='withdrawal' group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.Withdrawal = ins.sum_amount");
    function_mysql_query("update Dashboard D inner join    (select ds.merchant_id,ds.affiliate_id, count(ds.amount) as count_amount,sum(ds.amount) as sum_amount from data_sales ds inner join data_reg dr on ds.trader_id=dr.trader_id and dr.type<>'demo' where ds.rdate>='".$currentDay."' and ds.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type='chargeback' group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.ChargeBack = ins.sum_amount");

    
    // Pending Deposit
    function_mysql_query("update Dashboard D inner join    (select ds.merchant_id,ds.affiliate_id, count(ds.amount) as count_amount,sum(ds.amount) as sum_amount from data_sales_pending ds inner join data_reg dr on ds.trader_id=dr.trader_id and dr.type<>'demo' where ds.rdate>='".$currentDay."' and ds.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type='deposit' group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.PendingDeposits = ins.count_amount, D.PendingDepositsAmount = ins.sum_amount");
    
    
    // Commissions
    function_mysql_query("update Dashboard D inner join    (select c.merchantID as merchant_id, c.affiliateID as affiliate_id, SUM(c.Commission) as commissions from commissions c where c.Date>='".$currentDay."' and c.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' group by c.merchantID, c.affiliateID) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.Commission = ins.commissions");

    // PNL
    $query_pnl = "update Dashboard D inner join    (select ds.merchant_id,ds.affiliate_id, count(ds.amount) as count_amount,sum(ds.amount) as sum_amount from data_sales ds inner join data_reg dr on ds.trader_id=dr.trader_id and dr.initialftddate>'0000-00-00 00:00:00' and dr.type<>'demo' where ds.rdate>='".$currentDay."' and ds.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type='PNL' group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.PNL = (ins.sum_amount * -1)";
    function_mysql_query($query_pnl);
    
    
    
    
    // Active Traders
    function_mysql_query("update Dashboard D inner join    (SELECT drg.merchant_id,drg.affiliate_id, count(*) as active_traders FROM `data_reg` drg WHERE FTDqualificationDate >= '".$currentDay."' AND FTDqualificationDate < '".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and type<>'demo' group by drg.merchant_id,drg.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' set D.ActiveTrader = ins.active_traders");


    // TotalMicroPayments & MicroPaymentsAmount
    if ($set->showMicroPaymentsOnReports==1 && !empty($set->showMicroPaymentsOnReportsRate)){
        function_mysql_query("update Dashboard D inner join    (select ds.merchant_id,ds.affiliate_id, count(ds.amount) as count_amount,sum(ds.amount) as sum_amount from data_sales ds inner join data_reg dr on ds.trader_id=dr.trader_id and dr.type<>'demo' where ds.rdate>='".$currentDay."' and ds.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type='deposit' and ds.amount <= ".$set->showMicroPaymentsOnReportsRate." group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."'     set D.TotalMicroPayments = ins.count_amount, D.MicroPaymentsAmount = ins.sum_amount");
    }


    //Volume
    function_mysql_query("update Dashboard D inner join    (select ds.merchant_id,ds.affiliate_id, count(ds.amount) as count_amount,sum(ds.amount) as sum_amount from data_sales ds inner join data_reg dr on ds.trader_id=dr.trader_id and dr.initialftddate>'0000-00-00 00:00:00' and dr.type<>'demo' where ds.rdate>='".$currentDay."' and ds.rdate<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' and ds.type='volume' group by ds.merchant_id,ds.affiliate_id) ins on D.MerchantID = ins.merchant_id and D.AffiliateID=ins.affiliate_id and D.Date>='".$currentDay."' and D.Date<'".date('Y-m-d',strtotime($currentDay.' + 1 day'))."' set D.Volume = ins.sum_amount");
    
    
    if(!empty($_GET['debug'])){
        echo $query_pnl.'<br><br>';
    }
    
}

?>
