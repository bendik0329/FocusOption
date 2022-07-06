<?php 

require ('common/database.php');
$rowsInBatch= 50000;
$couner = 0 ;

$debug = isset($_GET['debug']) ? $_GET['debug'] : 0;


$runThisThing = true;

$wherePart = " and admin_id = 0 ";
$wherePart = " and admin_id > 0 ";
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
echo ($counter+1). ':  ' . ($counter+1)*$rowsInBatch.' Records updated...<br>';
	$runThisThing = false;
	$q = "select id from traffic2 where 1=1 ".$wherePart . "order by id desc limit 1 ;";
	$q = "select id from traffic2 where 1=1 order by id desc limit 1 ;";
	if ($debug)
		echo $q .'<Br>';
	$getMax = mysql_fetch_assoc(mysql_query($q));
	if (empty($getMax['id']))
		$getMax['id'] = 0;
	
	$q = "select id from traffic  where 1=1 ".$wherePart . " order by id desc limit 1 ;";
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


if (strpos($wherePart,'admin_id > 0')===false){
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

}

die ('done');


