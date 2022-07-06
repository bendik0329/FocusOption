<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require '../common/database.php';
require '../common/config.php';
require '../func/func_string.php';
require '../func/func_global.php';
require '../func/func_form.php';
require '../func/func_db.php';


$affiliate_id = $_POST['affiliate_id'];
$affiliate_merchants = $_POST['affiliate_merchants'];

$dateBeforeMonthsAgo =  date("Y-m-d H:i:s", strtotime( date( "Y-m-d H:i:s", strtotime( date("Y-m-d H:i:s") ) ) . "-1 week" ) );
$affwhere = $_POST["affwhere"];

if (!empty($affwhere)) {
		$AffMerchants = empty($affiliate_merchants) ? 0 : $affiliate_merchants;
		
		//feaured creatives
		$haveFeatured = false;
	if($set->ShowOnlyFeaturedCreativesWhenGotSome == 1){	
	$qry = "SELECT * from merchants_creative mrc 
                        WHERE mrc.valid=1 and  mrc.file NOT LIKE '%tmp%' and  mrc.affiliateReady=1 and  mrc.featured = 1  and  merchant_id in (".$affwhere .") ORDER BY RAND() LIMIT " . (5 - $bannersCount);
	
		$bnrqq = function_mysql_query($qry,__FILE__);
		while ($bnrww = mysql_fetch_assoc($bnrqq)) {
                    $l++;
					$haveFeatured = true;
					$getBanner = $bnrww;
                    $merchantInfo = dbGet($bnrww['merchant_id'],"merchants");
                    $bannersCount++;
                    $dimension = $getBanner['width'].'x'.$getBanner['height'];

                    if ($dimension == '0x0') {
                        $dimension = '';
                    }
                    
                    $listBanners .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
                        <td>'.lang(ucwords($getBanner['type'])).'</td>
                        <td align="center" class="img-wrap">'.($getBanner['type']=='link' ? $getBanner['title'] :  getFixedSizeBanner($getBanner['file'],50,50)).'</td>
                        <td><a href="/affiliate/creative.php?merchant='.$merchantInfo['id'].'">'.$merchantInfo['name'].'</a></td>
                        <td>'.lang(listLangs($getBanner['language_id'],1)).'</td>
                        <td>'.$dimension.'</td>
                        <!--td><a href="javascript:void(0);" onclick="NewWin(\'/affiliate/creative.php?act=get_code&id='.$getBanner['id'].'\',\'getCode\',\'1000\',\'600\',\'1\');">'.lang('Get Tracking Code').'</a></td-->
						<td><a href="'.$set->webAddress. 'affiliate/creative.php?act=get_code&id='.$getBanner['id'].'" class="inline">'.lang('Get Tracking Code').'</a></td>
						<!--td><a href="'.$set->webAddress. ltrim($set->basepage, '/').'?act=get_code&id='.$getBanner['id'].'" class="inline">'.lang('Get Tracking Code').'</a></td-->
                    </tr>';
					$topCreativesCount++;
                }
	}
		
		
		if(($set->ShowOnlyFeaturedCreativesWhenGotSome && !$haveFeatured) || !$set->ShowOnlyFeaturedCreativesWhenGotSome){	
		$qry = "SELECT stb.banner_id,stb.merchant_id FROM traffic stb 
                        inner join merchants_creative mrc on mrc.id = stb.banner_id
                        WHERE stb.rdate> '".$dateBeforeMonthsAgo."' and  mrc.valid=1 and (mrc.promotion_id =0 or mrc.promotion_id in (
                            select id from merchants_promotions where merchant_id in (".$affwhere .") and affiliate_id in (".$affiliate_id.",0)
                            )  )
                            and   stb.merchant_id in (".$affwhere .") AND stb.views > '1000'  AND mrc.file NOT LIKE '%tmp%'  and  mrc.affiliateReady=1 "
                        . "GROUP BY stb.banner_id ORDER BY stb.views DESC, RAND() "
                        . "LIMIT 2";
		 //die ($qry);
		if (false){
		$bnrqq = function_mysql_query($qry,__FILE__);
		while ($bnrww = mysql_fetch_assoc($bnrqq)) {
                    $l++;
                    $getBanner = dbGet($bnrww['banner_id'],"merchants_creative");
                    $merchantInfo = dbGet($getBanner['merchant_id'],"merchants");
                    $bannersCount++;
                    $dimension = $getBanner['width'].'x'.$getBanner['height'];

                    if ($dimension == '0x0') {
                        $dimension = '';
                    }
                    
                    $listBanners .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
                        <td>'.lang(ucwords($getBanner['type'])).'</td>
                        <td align="center" class="img-wrap">'.($getBanner['type']=='link' ? $getBanner['title'] :  getFixedSizeBanner($getBanner['file'],50,50)).'</td>
                        <td><a href="/affiliate/creative.php?merchant='.$merchantInfo['id'].'">'.$merchantInfo['name'].'</a></td>
                        <td>'.lang(listLangs($getBanner['language_id'],1)).'</td>
                        <td>'.$dimension.'</td>
                        <!--td><a href="javascript:void(0);" onclick="NewWin(\'/affiliate/creative.php?act=get_code&id='.$getBanner['id'].'\',\'getCode\',\'1000\',\'600\',\'1\');">'.lang('Get Tracking Code').'</a></td-->
						<td><a href="'.$set->webAddress. 'affiliate/creative.php?act=get_code&id='.$getBanner['id'].'" class="inline">'.lang('Get Tracking Code').'</a></td>
						<!--td><a href="'.$set->webAddress. ltrim($set->basepage, '/').'?act=get_code&id='.$getBanner['id'].'" class="inline">'.lang('Get Tracking Code').'</a></td-->
                    </tr>';
					$topCreativesCount++;
                }
		}
		
		$qry = "SELECT reg.banner_id,reg.merchant_id FROM data_reg reg
					inner join merchants_creative mrc on mrc.id = reg.banner_id
					WHERE mrc.valid=1 and (mrc.promotion_id =0 or mrc.promotion_id in (
							select id from merchants_promotions where merchant_id in (".$affwhere .") and affiliate_id='".$affiliate_id."'
							)  )
					and  mrc.file NOT LIKE '%tmp%' and  mrc.affiliateReady=1 and   reg.merchant_id in (".$affwhere.") GROUP BY banner_id ORDER BY RAND() LIMIT " . (5 - $bannersCount);
					
				
			$bnrqq = function_mysql_query($qry,__FILE__);
			while ($bnrww = mysql_fetch_assoc($bnrqq)) {
                    $l++;
                    $getBanner = dbGet($bnrww['banner_id'],"merchants_creative");
                    $merchantInfo = dbGet($getBanner['merchant_id'],"merchants");
                    $bannersCount++;
                    $dimension = $getBanner['width'].'x'.$getBanner['height'];

                    if ($dimension == '0x0') {
                        $dimension = '';
                    }
                    
                    $listBanners .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
                        <td>'.lang(ucwords($getBanner['type'])).'</td>
                        <td align="center" class="img-wrap">'.($getBanner['type']=='link' ? $getBanner['title'] : getFixedSizeBanner($getBanner['file'],50,50)).'</td>
                        <td><a href="/affiliate/creative.php?merchant='.$merchantInfo['id'].'">'.$merchantInfo['name'].'</a></td>
                        <td>'.lang(listLangs($getBanner['language_id'],1)).'</td>
                        <td>'.$dimension.'</td>
                        <!--td><a href="javascript:void(0);" onclick="NewWin(\'/affiliate/creative.php?act=get_code&id='.$getBanner['id'].'\',\'getCode\',\'1000\',\'600\',\'1\');">'.lang('Get Tracking Code').'</a></td-->
						<td><a href="'.$set->webAddress. 'affiliate/creative.php?act=get_code&id='.$getBanner['id'].'" class="inline cboxElement">'.lang('Get Tracking Code').'</a></td>
						
                    </tr>';
					$topCreativesCount++;
			}				
		
      if ($bannersCount<5) {          
                $qry = "SELECT stb.banner_id,stb.merchant_id FROM traffic stb 
                        inner join merchants_creative mrc on mrc.id = stb.banner_id
                        WHERE mrc.valid=1 and (mrc.promotion_id =0 or mrc.promotion_id in (
                                select id from merchants_promotions where merchant_id in (".$affwhere .") and affiliate_id='".$affiliate_id."'
                                )  )
                        and  mrc.file NOT LIKE '%tmp%' and  mrc.affiliateReady=1 and   stb.merchant_id in (".$affwhere .") GROUP BY banner_id ORDER BY RAND() LIMIT " . (5 - $bannersCount);
                    
		$bnrqq=function_mysql_query($qry,__FILE__);
								
		$a = 0;		
		while ($bnrww=mysql_fetch_assoc($bnrqq)) {
			$l++;
			$a++;
			$banners_ids .= $bnrww['banner_id'] .',';
			$getBanner = dbGet($bnrww['banner_id'],"merchants_creative");
			$merchantInfo = dbGet($getBanner['merchant_id'],"merchants");
			$listBanners .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.lang(ucwords($getBanner['type'])).'</td>
							<td align="center" class="img-wrap">'.($getBanner['type']=='link' ? $getBanner['title'] :  getFixedSizeBanner($getBanner['file'],50,50)).'</td>
							<td><a href="/affiliate/creative.php?merchant='.$merchantInfo['id'].'">'.$merchantInfo['name'].'</a></td>
							<td>'.lang(listLangs($getBanner['language_id'],1)).'</td>
							<td>'.(($getBanner['width']>0 && $getBanner['height']>0) ? $getBanner['width'].'x'.$getBanner['height'] : '').'</td>
							<!--td><a href="javascript:void(0);" onclick="NewWin(\'/affiliate/creative.php?act=get_code&id='.$getBanner['id'].'\',\'getCode\',\'900\',\'500\',\'1\');">'.lang('Get Tracking Code').'</a></td-->
							<td><a href="'.$set->webAddress. 'affiliate/creative.php?act=get_code&id='.$getBanner['id'].'" class="inline cboxElement">'.lang('Get Tracking Code').'</a></td>
						</tr>';
			$topCreativesCount++;
			}
	  }
			// echo $l;
		if ($a<5 ){
			
			
				$banners_ids = empty($banners_ids) ? 0 : $banners_ids;
				$qry = "select * from merchants_creative where not id in (".rtrim($banners_ids,',').") and valid = 1 and  file NOT LIKE '%tmp%' and  affiliateReady=1
				and merchant_id in (".$affwhere .") limit ".(5-$a). " ;
				";
		// die ($qry);
		$bnrqq=function_mysql_query($qry,__FILE__);
								
								
		while ($bnrww=mysql_fetch_assoc($bnrqq)) {
			$l++;
			// var_dump($bnrww);die();
			$banners_ids .= $bnrww['banner_id'] .',';
			// $getBanner = dbGet($bnrww['banner_id'],"merchants_creative");
			$merchantInfo = dbGet($bnrww['merchant_id'],"merchants");
			$listBanners .= '<tr '.($l % 2 ? 'class="trLine"' : '').'>
							<td>'.lang(ucwords($bnrww['type'])).'</td>
							<td align="center" class="img-wrap">'.($bnrww['type']=='link' ? $bnrww['title'] :  getFixedSizeBanner($bnrww['file'],50,50)).'</td>
							<td><a href="/affiliate/creative.php?merchant='.$merchantInfo['id'].'">'.$merchantInfo['name'].'</a></td>
							<td>'.lang(listLangs($bnrww['language_id'],1)).'</td>
							<td>'.(($bnrww['width']>0 && $bnrww['height']>0) ? $bnrww['width'].'x'.$bnrww['height'] : '').'</td>
							<!--td><a href="javascript:void(0);" onclick="NewWin(\'/affiliate/creative.php?act=get_code&id='.$bnrww['id'].'\',\'getCode\',\'900\',\'500\',\'1\');">'.lang('Get Tracking Code').'</a></td-->
							<td><a href="'.$set->webAddress. 'affiliate/creative.php?act=get_code&id='.$bnrww['id'].'" class="inline cboxElement">'.lang('Get Tracking Code').'</a></td>
						</tr>';
			$topCreativesCount++;
			}
		}
		
}
}
		//products 
		$active_products = ltrim(str_replace('|',',',$_POST['active_products']),',');
		
		if (empty($active_products))
			$active_products= 0;
		
	//if (!empty($featured_prods)){
		/* $fPqq = mysql_query("SELECT GROUP_CONCAT(id) as prods FROM products_items WHERE valid!='0' and id in (".$active_products. ")  and featured=1");
		$fPww = mysql_fetch_assoc($fPqq);
		$featured_prods = $fPww['prods']; */
	//}
	//if (!empty($featured_prods)){
		//$qry = "SELECT * FROM merchants_creative WHERE product_id>0 and  product_id in ( ". $featured_prods .") and valid > 0 ORDER BY featured DESC limit 5";
		$havePFeatured = false;
		if($set->ShowOnlyFeaturedCreativesWhenGotSome){
				$qry = "SELECT * FROM products_items WHERE  valid!='0' and featured = 1 and id in (".$active_products. ") limit 5";
		
		
		$pQry=mysql_query($qry);	 
		
		while($pww = mysql_fetch_assoc($pQry)){
			$havePFeatured = true;
			$l1++;
			//$productInfo = dbGet($pww['product_id'],"products_items");
			$productsCount++;
			//$dimension = $pww['width'].'x'.$pww['height'];

			if ($dimension == '0x0') {
				$dimension = '';
			}
			$allRecLangTitle = "";
			$productLangID = !empty($pww['languages']) ? $pww['languages'] : 0;
			if(strpos($productLangID ,"|")){
			$allRecLang = explode ("," , $productLangID);
			$allRecLangTitle = array();
				foreach($allRecLang as $k=>$lang_id){
					$sql = "SELECT title from languages where id = " . $lang_id;
					$myLangs = mysql_fetch_assoc(function_mysql_query($sql, __FILE__));
					$allRecLangTitle[] = $myLangs['title'];
			}
			$allRecLangTitle = implode(",",$allRecLangTitle);
			}
			else{
				$allRecLangTitle =  lang(listLangs($productLangID,1));
			}
			// die ('{1}:   {' . $productLangID.'}');
			
			$listProductBanners .= '<tr '.($l1 % 2 ? 'class="trLine"' : '').' data-test="test">
				<td align="center" class="img-wrap">'.getFixedSizeBanner($pww['image'],50,50).'</td>
				<td><a href="/affiliate/getProductCreatives.php?product_id='.$pww['id'].'">'.$pww['title'].'</a></td>
				<td>'.$allRecLangTitle.'</td>
				<!--td>'.$dimension.'</td-->
				<td>'.lang(ucwords($pww['type'])).'</td>
				<td><a href="'.$set->webAddress. 'affiliate/getProductCreatives.php?product_id='.$pww['id'].'">'.lang('Get Tracking Code').'</a></td>
				
			</tr>';
			$topPCreativesCount++;
		}
		}
		
		
		if(($set->ShowOnlyFeaturedCreativesWhenGotSome && !$havePFeatured) || !$set->ShowOnlyFeaturedCreativesWhenGotSome){	
		$qry = "SELECT * FROM products_items WHERE  valid!='0' and id in (".$active_products. ") limit 5";
		
		
		$pQry=mysql_query($qry);	 
		
		while($pww = mysql_fetch_assoc($pQry)){
			$havePFeatured = true;
			$l1++;
			//$productInfo = dbGet($pww['product_id'],"products_items");
			$productsCount++;
			//$dimension = $pww['width'].'x'.$pww['height'];

			if ($dimension == '0x0') {
				$dimension = '';
			}
			$allRecLangTitle = "";
			$productLangID = !empty($pww['languages']) ? $pww['languages'] : 0;
			if(strpos($productLangID ,"|")){
			$allRecLang = explode ("," , $productLangID);
			$allRecLangTitle = array();
				foreach($allRecLang as $k=>$lang_id){
					$sql = "SELECT title from languages where id = " . $lang_id;
					$myLangs = mysql_fetch_assoc(function_mysql_query($sql, __FILE__));
					$allRecLangTitle[] = $myLangs['title'];
			}
			$allRecLangTitle = implode(",",$allRecLangTitle);
			}
			else{
				$allRecLangTitle =  lang(listLangs($productLangID,1));
			}
			// die ('{1}:   {' . $productLangID.'}');
			
			$listProductBanners .= '<tr '.($l1 % 2 ? 'class="trLine"' : '').' data-test="test">
				<td align="center" class="img-wrap">'.getFixedSizeBanner($pww['image'],50,50).'</td>
				<td><a href="/affiliate/getProductCreatives.php?product_id='.$pww['id'].'">'.$pww['title'].'</a></td>
				<td>'.$allRecLangTitle.'</td>
				<!--td>'.$dimension.'</td-->
				<td>'.lang(ucwords($pww['type'])).'</td>
				<td><a href="'.$set->webAddress. 'affiliate/getProductCreatives.php?product_id='.$pww['id'].'">'.lang('Get Tracking Code').'</a></td>
				
			</tr>';
			$topPCreativesCount++;
		}
		}
	//}
		
		$data = array();

if (!empty(		$listBanners))
		$data['creatives'] = $listBanners;
if (!empty(		$listProductBanners))
		$data['products'] = $listProductBanners;
		

if (!empty($data))
echo json_encode($data);
die;
?>