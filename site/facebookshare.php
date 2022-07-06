<?php
	//die ($fullurl);
	// nirs modification
	require('common/global.php');
	require_once ('common/ShortUrl.php');
	
	$q = $_GET['q'];
	
	
	if ($q<>'') {
		$shortUrl = new ShortUrl();
		$fullurl =  $shortUrl->shortCodeToUrl($q);
		$parts = parse_url($fullurl);
		parse_str($parts['query'], $query);
		
		
			
	$brand = $query['brand'];
	
	$name = rawurldecode($query['name']);
	$image = rawurldecode($query['image']);
	$imageType = explode('.',$image);
	$imageType = $imageType[1];
	$url = rawurldecode($query['url']);
	$d = rawurldecode($query['d']);
	$baseurl = 'http://'.$_SERVER[HTTP_HOST];
	}
	else {
	$brand = rawurldecode($_GET['brand']);
	$name = rawurldecode($_GET['name']);
	$image = rawurldecode($_GET['image']);
	$imageType = explode('.',$image);
	$imageType = $imageType[1];
	$url = rawurldecode($_GET['url']);
	$d = rawurldecode($_GET['d']);
	$baseurl = 'http://'.$_SERVER[HTTP_HOST];
	//$facebookurl = $url;
	}
	
	if ($imageType=='jpg')
	$imageType= 'jpeg';
?>

<html>
<head>
	<meta name="description" content="<?php echo $brand.' - '.$name;?>">
	<meta name="keywords" content="<?php echo $brand.' - '.$name;?>">
	<meta name="author" content="Affiliate Buddies">
	<meta name="copyright" content="Affiliate Buddies" />
	<meta name="application-name" content="application-name" />
	<!-- for Facebook -->
	<!-- 
	Title: Brand+name 
	Image: banner creative 
	url: itself 
	description: brand+name 
	twitter:image: 
	-->
 	<meta property="og:title" content="<?php echo $brand.' - '.$name;?>" />
	<meta property="og:type" content="article" />
	<meta itemprop="image primaryImageOfPage" property="og:image" content="<?php echo $baseurl.'/'.$image;?>" />
	<meta property="og:image:type" content="image/<?php echo $imageType;?>">
	<meta property="og:url" content="<?php echo 'http://'.$_SERVER[HTTP_HOST].$_SERVER[REQUEST_URI];?>" />
	<meta property="og:description" content="<?php echo $brand.' - '.$name;?>" />
	<meta property="twitter:image" content="<?php echo $baseurl.'/'. $image;?>" />
	<link rel="image_src" href="<?php echo $baseurl.'/'.$image;?>" />
	<META http-equiv="refresh" content="0;URL=<?php echo $url;?>"/>
	<title><?php echo $brand.' - '.$name;?></title>
</head>
<body bgcolor="#ffffff" class=" article-page">
		<center>Please wait...</center>
		<img src="<?php echo $baseurl.'/'. $image;?>"  width="300px" height="300px" />
	</body>
</html>