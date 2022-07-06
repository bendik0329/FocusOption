<?php 

require ('common/database.php');
$rowsInBatch= 50000;
$rowsInBatch= 1;
$couner = 0 ;
$silence = 1;

$debug = isset($_GET['debug']) ? $_GET['debug'] : 0;


$runThisThing = true;

$wherePart = " and admin_id = 0 ";
$wherePart = "";

$q = "CREATE TABLE traffic2 SELECT * FROM traffic LIMIT 0;";
mysql_query($q);


$q = "ALTER TABLE `traffic2` DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;";
mysql_query($q);


$q = "ALTER TABLE traffic2 MODIFY id int NOT NULL AUTO_INCREMENT;";
mysql_query($q);


$q = "ALTER TABLE `traffic2` ADD `unixRdate` INT(10) NOT NULL , ADD INDEX `unixdate` (`unixRdate`);";
mysql_query($q);



$q  = 
'
ALTER TABLE `traffic2`
PARTITION BY RANGE(unixRdate) (
PARTITION p0 VALUES LESS THAN (UNIX_TIMESTAMP("2016-06-20 00:00:00")),
PARTITION p1 VALUES LESS THAN (UNIX_TIMESTAMP("2016-06-27 00:00:00")),
PARTITION p2 VALUES LESS THAN (UNIX_TIMESTAMP("2016-07-04 00:00:00")),
PARTITION p3 VALUES LESS THAN (UNIX_TIMESTAMP("2016-07-11 00:00:00")),
PARTITION p4 VALUES LESS THAN (UNIX_TIMESTAMP("2016-07-18 00:00:00")),
PARTITION p5 VALUES LESS THAN (UNIX_TIMESTAMP("2016-07-25 00:00:00")),
PARTITION p6 VALUES LESS THAN (UNIX_TIMESTAMP("2016-08-01 00:00:00")),
PARTITION p7 VALUES LESS THAN (UNIX_TIMESTAMP("2016-08-08 00:00:00")),
PARTITION p8 VALUES LESS THAN (UNIX_TIMESTAMP("2016-08-15 00:00:00")),
PARTITION p9 VALUES LESS THAN (UNIX_TIMESTAMP("2016-08-22 00:00:00")),
PARTITION p10 VALUES LESS THAN (UNIX_TIMESTAMP("2016-08-29 00:00:00")),
PARTITION p11 VALUES LESS THAN (UNIX_TIMESTAMP("2016-09-05 00:00:00")),
PARTITION p12 VALUES LESS THAN (UNIX_TIMESTAMP("2016-09-12 00:00:00")),
PARTITION p13 VALUES LESS THAN (UNIX_TIMESTAMP("2016-09-19 00:00:00")),
PARTITION p14 VALUES LESS THAN (UNIX_TIMESTAMP("2016-09-26 00:00:00")),
PARTITION p15 VALUES LESS THAN (UNIX_TIMESTAMP("2016-10-03 00:00:00")),
PARTITION p16 VALUES LESS THAN (UNIX_TIMESTAMP("2016-10-10 00:00:00")),
PARTITION p17 VALUES LESS THAN (UNIX_TIMESTAMP("2016-10-17 00:00:00")),
PARTITION p18 VALUES LESS THAN (UNIX_TIMESTAMP("2016-10-24 00:00:00")),
PARTITION p19 VALUES LESS THAN (UNIX_TIMESTAMP("2016-10-31 00:00:00")),
PARTITION p20 VALUES LESS THAN (UNIX_TIMESTAMP("2016-11-07 00:00:00")),
PARTITION p21 VALUES LESS THAN (UNIX_TIMESTAMP("2016-11-14 00:00:00")),
PARTITION p22 VALUES LESS THAN (UNIX_TIMESTAMP("2016-11-21 00:00:00")),
PARTITION p23 VALUES LESS THAN (UNIX_TIMESTAMP("2016-11-28 00:00:00")),
PARTITION p24 VALUES LESS THAN (UNIX_TIMESTAMP("2016-12-05 00:00:00")),
PARTITION p25 VALUES LESS THAN (UNIX_TIMESTAMP("2016-12-12 00:00:00")),
PARTITION p26 VALUES LESS THAN (UNIX_TIMESTAMP("2016-12-19 00:00:00")),
PARTITION p27 VALUES LESS THAN (UNIX_TIMESTAMP("2016-12-26 00:00:00")),
PARTITION p28 VALUES LESS THAN (UNIX_TIMESTAMP("2017-01-02 00:00:00")),
PARTITION p29 VALUES LESS THAN (UNIX_TIMESTAMP("2017-01-09 00:00:00")),
PARTITION p30 VALUES LESS THAN (UNIX_TIMESTAMP("2017-01-16 00:00:00")),
PARTITION p31 VALUES LESS THAN (UNIX_TIMESTAMP("2017-01-23 00:00:00")),
PARTITION p32 VALUES LESS THAN (UNIX_TIMESTAMP("2017-01-30 00:00:00")),
PARTITION p33 VALUES LESS THAN (UNIX_TIMESTAMP("2017-02-06 00:00:00")),
PARTITION p34 VALUES LESS THAN (UNIX_TIMESTAMP("2017-02-13 00:00:00")),
PARTITION p35 VALUES LESS THAN (UNIX_TIMESTAMP("2017-02-20 00:00:00")),
PARTITION p36 VALUES LESS THAN (UNIX_TIMESTAMP("2017-02-27 00:00:00")),
PARTITION p37 VALUES LESS THAN (UNIX_TIMESTAMP("2017-03-06 00:00:00")),
PARTITION p38 VALUES LESS THAN (UNIX_TIMESTAMP("2017-03-13 00:00:00")),
PARTITION p39 VALUES LESS THAN (UNIX_TIMESTAMP("2017-03-20 00:00:00")),
PARTITION p40 VALUES LESS THAN (UNIX_TIMESTAMP("2017-03-27 00:00:00")),
PARTITION p41 VALUES LESS THAN (UNIX_TIMESTAMP("2017-04-03 00:00:00")),
PARTITION p42 VALUES LESS THAN (UNIX_TIMESTAMP("2017-04-10 00:00:00")),
PARTITION p43 VALUES LESS THAN (UNIX_TIMESTAMP("2017-04-17 00:00:00")),
PARTITION p44 VALUES LESS THAN (UNIX_TIMESTAMP("2017-04-24 00:00:00")),
PARTITION p45 VALUES LESS THAN (UNIX_TIMESTAMP("2017-05-01 00:00:00")),
PARTITION p46 VALUES LESS THAN (UNIX_TIMESTAMP("2017-05-08 00:00:00")),
PARTITION p47 VALUES LESS THAN (UNIX_TIMESTAMP("2017-05-15 00:00:00")),
PARTITION p48 VALUES LESS THAN (UNIX_TIMESTAMP("2017-05-22 00:00:00")),
PARTITION p49 VALUES LESS THAN (UNIX_TIMESTAMP("2017-05-29 00:00:00")),
PARTITION p50 VALUES LESS THAN (UNIX_TIMESTAMP("2017-06-05 00:00:00")),
PARTITION p51 VALUES LESS THAN (UNIX_TIMESTAMP("2017-06-12 00:00:00")),
PARTITION p52 VALUES LESS THAN (UNIX_TIMESTAMP("2017-06-19 00:00:00")),
PARTITION p53 VALUES LESS THAN (UNIX_TIMESTAMP("2017-06-26 00:00:00")),
PARTITION p54 VALUES LESS THAN (UNIX_TIMESTAMP("2017-07-03 00:00:00")),
PARTITION p55 VALUES LESS THAN (UNIX_TIMESTAMP("2017-07-10 00:00:00")),
PARTITION p56 VALUES LESS THAN (UNIX_TIMESTAMP("2017-07-17 00:00:00")),
PARTITION p57 VALUES LESS THAN (UNIX_TIMESTAMP("2017-07-24 00:00:00")),
PARTITION p58 VALUES LESS THAN (UNIX_TIMESTAMP("2017-07-31 00:00:00")),
PARTITION p59 VALUES LESS THAN (UNIX_TIMESTAMP("2017-08-07 00:00:00")),
PARTITION p60 VALUES LESS THAN (UNIX_TIMESTAMP("2017-08-14 00:00:00")),
PARTITION p61 VALUES LESS THAN (UNIX_TIMESTAMP("2017-08-21 00:00:00")),
PARTITION p62 VALUES LESS THAN (UNIX_TIMESTAMP("2017-08-28 00:00:00")),
PARTITION p63 VALUES LESS THAN (UNIX_TIMESTAMP("2017-09-04 00:00:00")),
PARTITION p64 VALUES LESS THAN (UNIX_TIMESTAMP("2017-09-11 00:00:00")),
PARTITION p65 VALUES LESS THAN (UNIX_TIMESTAMP("2017-09-18 00:00:00")),
PARTITION p66 VALUES LESS THAN (UNIX_TIMESTAMP("2017-09-25 00:00:00")),
PARTITION p67 VALUES LESS THAN (UNIX_TIMESTAMP("2017-10-02 00:00:00")),
PARTITION p68 VALUES LESS THAN (UNIX_TIMESTAMP("2017-10-09 00:00:00")),
PARTITION p69 VALUES LESS THAN (UNIX_TIMESTAMP("2017-10-16 00:00:00")),
PARTITION p70 VALUES LESS THAN (UNIX_TIMESTAMP("2017-10-23 00:00:00")),
PARTITION p71 VALUES LESS THAN (UNIX_TIMESTAMP("2017-10-30 00:00:00")),
PARTITION p72 VALUES LESS THAN (UNIX_TIMESTAMP("2017-11-06 00:00:00")),
PARTITION p73 VALUES LESS THAN (UNIX_TIMESTAMP("2017-11-13 00:00:00")),
PARTITION p74 VALUES LESS THAN (UNIX_TIMESTAMP("2017-11-20 00:00:00")),
PARTITION p75 VALUES LESS THAN (UNIX_TIMESTAMP("2017-11-27 00:00:00")),
PARTITION p76 VALUES LESS THAN (UNIX_TIMESTAMP("2017-12-04 00:00:00")),
PARTITION p77 VALUES LESS THAN (UNIX_TIMESTAMP("2017-12-11 00:00:00")),
PARTITION p78 VALUES LESS THAN (UNIX_TIMESTAMP("2017-12-18 00:00:00")),
PARTITION p79 VALUES LESS THAN (UNIX_TIMESTAMP("2017-12-25 00:00:00")),
PARTITION p80 VALUES LESS THAN (UNIX_TIMESTAMP("2018-01-01 00:00:00"))
);
  ';
  
mysql_query($q);
  
$q  = 
"
ALTER TABLE `traffic2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rdate` (`rdate`),
  ADD KEY `affiliate_id` (`affiliate_id`),
  ADD KEY `merchant_id` (`merchant_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `banner_merchant` (`merchant_id`,`banner_id`),
  ADD KEY `uid` (`uid`);
  ";
  
mysql_query($q);
  

// die();

while ($runThisThing){

if (!$silence)
echo ($counter+1). ':  ' . ($counter+1)*$rowsInBatch.' Records updated...<br>';
	$runThisThing = false;
	$q = "select id from traffic2 where 1=1 order by id desc limit 1 ;";
	
	if ($debug)
		echo $q .'<Br>';
	$getMax = mysql_fetch_assoc(mysql_query($q));
	if (empty($getMax['id']))
		$getMax['id'] = 0;
	
	$q = "select id from traffic  where 1=1  order by id desc limit 1 ;";
	if ($debug)
		echo $q .'<Br>';
	$getMaxOrigin = mysql_fetch_assoc(mysql_query($q));
	if ($getMaxOrigin['id'] == $getMax['id'])
		die('Nothing to do, all good...');
		$runThisThing=true;
	/* 
	$q = "INSERT INTO traffic2
SELECT *
FROM traffic where id > " . $getMax['id'] . " 
order by id limit " . ($counter * $rowsInBatch). " , " . $rowsInBatch; */


// SELECT * ,  unix_timestamp(rdate)  as unixRdate
$item  = mysql_fetch_assoc(mysql_query("select admin_id from traffic where id > " .$getMax['id'] . $wherePart ." 
	order by id limit " .  $rowsInBatch));


if ($item['admin_id'] == 0){
	$q = "INSERT INTO traffic2
	SELECT *
	FROM traffic where id > " . $getMax['id'] . $wherePart ." 
	order by id limit " .  $rowsInBatch;
}
else {
	
	$q = "
	INSERT INTO `traffic2`(`id`, `rdate`, `unixRdate`, `ctag`, `uid`, `ip`, `admin_id`, `affiliate_id`, `group_id`, `banner_id`, `merchant_id`, `profile_id`, `language_id`, `promotion_id`, `last_update`, `valid`, `title`, `bannerType`, `type`, `width`, `height`, `file`, `url`, `alt`, `platform`, `os`, `osVersion`, `browser`, `broswerVersion`, `userAgent`, `country_id`, `refer_url`, `param`, `param2`, `param3`, `param4`, `param5`, `views`, `clicks`, `product_id`) 

			  
			  
			  
			  select  `id`, `rdate`, `unixRdate`, concat( 'a' , affiliate_id , '-b' , banner_id , '-u' , uid , '-p' , profile_id  ) as`ctag`, `uid`, `ip`, `admin_id`, `affiliate_id`, `group_id`, `banner_id`, `merchant_id`, `profile_id`, `language_id`, `promotion_id`, `last_update`, `valid`, `title`, `bannerType`, `type`, `width`, `height`, `file`, `url`, `alt`, `platform`, `os`, `osVersion`, `browser`, `broswerVersion`, `userAgent`, `country_id`, `refer_url`, `param`, `param2`, `param3`, `param4`, `param5`, `views`, `clicks`, `product_id`

  from 
(select `id` as  `id` ,
 `rdate` as  `rdate` ,
 `unixRdate` as  `unixRdate` ,
  `ctag` as   `uid` ,
  `uid` as  `ip` ,
 `ip` as  `admin_id` ,
 `admin_id` as  `affiliate_id` ,
 `affiliate_id` as  `group_id` ,
 `group_id` as  `banner_id` ,
 `banner_id` as  `merchant_id` ,
 `merchant_id` as  `profile_id` ,
 `profile_id` as  `language_id` ,
 `language_id` as  `promotion_id` ,
 `promotion_id` as  `last_update` ,
 `last_update` as  `valid` ,
 `valid` as  `title` ,
 `title` as  `bannerType` ,
 `bannerType` as  `type` ,
 `type` as  `width` ,
 `width` as  `height` ,
 `height` as  `file` ,
 `file` as  `url` ,
 `url` as  `alt` ,
 `alt` as  `platform` ,
 `platform` as  `os` ,
 `os` as  `osVersion` ,
 `osVersion` as  `browser` ,
 `browser` as  `broswerVersion` ,
 `broswerVersion` as  `userAgent` ,
 `userAgent` as  `country_id` ,
 `country_id` as  `refer_url` ,
 `refer_url` as  `param` ,
 `param` as  `param2` ,
 `param2` as  `param3` ,
 `param3` as  `param4` ,
 `param4` as  `param5` ,
 `param5` as  `views` ,
 `views` as  `clicks` ,
 `clicks` as  `product_id` 
from 
traffic 

where id > " . $getMax['id'] . $wherePart ." 
	order by id limit " .  $rowsInBatch ."
	
 ) l
			  
";	
	
}
if ($debug)
echo ($q).'<Br><Br><Br>';

	$counter++;


	mysql_query($q);
	
	$updateUnix = "update traffic set unixRdate = unix_timestamp(rdate) where unixRdate = 0;";
	mysql_query($updateUnix);
// die('----------');
}

die ('done');


