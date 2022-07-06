<?php

if (!empty($errors)) {

		} else {
			
			if ($form_type == "2") {
                            $max_upload = 5;
			} elseif ($form_type > "2") {
                            $max_upload = 1;
			}
			
                        
			// Flag, that indicate whether default values are in use.
			$boolUseDefaultName = false;
                        
			/*for ($i = 1; $i <= $max_upload; $i++) {
				unset($db);
				$insert             = 0;
				$db['language_id']  = $creative_lang;
				$db['promotion_id'] = $creative_promotion;
				$db['category_id']  = $category_id;
				$db['url']          = $creative_url;
				$db['admin_id']     = $set->userInfo['id'];
				$db['merchant_id']  = $merchant_id;
				$db['rdate']        = dbDate();
				$db['last_update']  = dbDate();
				$db['valid']        = $creative_status;
				
				if ($form_type == "1") {
                                    
                                    $db['title'] = (!empty($_POST['name1_' . $i]) ? $_POST['name1_' . $i] : $creative_name . '_' . $i);
                                    
                                    if (chkUpload('file1_' . $i)) {
                                        $db['file'] = UploadFile('file1_' . $i, '5120000', 'jpg,gif,swf,jpeg,png', '', 'files/banners/');
                                        $exp        = explode(".", $db['file']);
                                        $ext        = strtolower($exp[count($exp) - 1]);
                                        
                                        if ($ext == "swf") { 
                                            $db['type'] = "flash"; 
                                        } else { 
                                            $db['type'] = "image";
                                        }
                                        
                                        list($db['width'], $db['height']) = getimagesize($db['file']);
                                        $db['alt'] = (isset($_POST['alt1_' . $i]) ? $_POST['alt1_' . $i] : $creative_alt);
                                        $insert    = 1;
                                    }
                                    
				} elseif ($form_type == "2") {
					if (empty($_POST['name2_' . $i]) && 1 == $i) {
                                            $db['title'] = $_POST['creative_name'];
                                            $boolUseDefaultName = true;
					    
					} else {
                                            $db['title'] = $_POST['name2_' . $i];
					}
					
					if (empty($_POST['alt2_' . $i])) {
                                            $db['alt'] = $_POST['creative_alt'];
					} else {
                                            $db['alt'] = $_POST['alt2_' . $i];
					}
					
					if (isset($db['title']) && !empty($db['title'])) { 
                                            $db['type'] = "link";
					    $insert     = 1; 
					}
				
				} elseif ($form_type == "3") {
                                    $db['title']      = (!empty($_POST['name3_' . $i]) ? $_POST['name3_' . $i] : $creative_name . '_' . $i);
                                    $db['type']       = "widget";
                                    $db['height']     = $_POST['iframe3_height_' . $i];
                                    $db['width']      = $_POST['iframe3_width_' . $i];
                                    $db['iframe_url'] = $_POST['iframe3_url_' . $i];
                                    
                                    if ($db['title'] && $db['iframe_url'] != "http://" && $db['iframe_url']) {
                                            $insert = 1;
                                    }
					
				} elseif ($form_type == "4") {
                                    if (chkUpload('file4_' . $i)) {
                                        $db['title'] = $_POST['name4_' . $i];
                                        $db['file']  = UploadFile('file4_' . $i, '5120000', 'jpg,gif,swf,jpeg,png', '', 'files/banners/');
                                        $exp         = explode(".", $db['file']);
                                        $ext         = strtolower($exp[count($exp) - 1]);
                                        $db['type']  = "mobileleader";

                                        list($db['width'], $db['height']) = getimagesize($db['file']);
                                        $db['alt'] = (!empty($_POST['alt4_' . $i]) ? $_POST['alt4_' . $i] : $creative_alt);
                                        $insert    = 1;
                                    }
						
				} elseif ($form_type == "5") {
					if (chkUpload('file5_' . $i)) {
                                            $db['title'] = $_POST['name5_' . $i];
                                            $db['file']  = UploadFile('file5_' . $i, '5120000', 'jpg,gif,swf,jpeg,png', '', 'files/banners/');
                                            $exp         = explode(".", $db['file']);
                                            $ext         = strtolower($exp[count($exp) - 1]);
                                            $db['type']  = "mobilesplash";

                                            list($db['width'], $db['height']) = getimagesize($db['file']);
                                            $db['alt'] = (!empty($_POST['alt5_' . $i]) ? $_POST['alt5_' . $i] : $creative_alt);
                                            $insert    = 1;
					}
					
				} elseif ($form_type == "6") {
                                    $db['type']       = "mail";
                                     $db['title'] = (!empty($_POST['name6_' . $i]) ? $_POST['name6_' . $i] : $_POST['creative_name']); ;
									 
									 
									 $db['alt'] = (!empty($_POST['alt6_' . $i]) ? $_POST['alt6_' . $i] : $creative_alt);
									$inlinecss        = new instyle();
                                    $strConvertedHtml = $inlinecss->convert($_POST['scriptCode6']);
                                    $db['scriptCode'] = $strConvertedHtml;
                                    $insert           = 1;
					
				} else if ($form_type == "7") {
                                    $db['type'] = "content";
                                    $db['title'] = (!empty($_POST['name7_' . $i]) ? $_POST['name7_' . $i] : $_POST['creative_name']); ;
									 $db['alt'] = (!empty($_POST['alt7_' . $i]) ? $_POST['alt7_' . $i] : $creative_alt);
									$inlinecss        = new instyle();
                                    $strConvertedHtml = $inlinecss->convert($_POST['scriptCode7']);
                                    $db['scriptCode'] = $strConvertedHtml;
                                    $insert           = 1;
				}
				
				if ($insert) {
                                    dbAdd($db, $appTable, array('scriptCode'));
				}
				
				// Stop the loop in case of default values usage.
				if ($boolUseDefaultName) {
                                    break;
				}
			}*/
			
			if($set->userInfo['level']=='admin')
			$creative_alt = $_POST['creative_alt'];
			
			for ($i = 1; $i <= $max_upload; $i++) {
				unset($db);
				$insert             = 0;
				$db['language_id']  = $creative_lang;
				$db['promotion_id'] = $creative_promotion;
				$db['category_id']  = $category_id;
				$db['url']          = trim($creative_url);
				$db['admin_id']     = $set->userInfo['id'];
				if($mainBannerType == "products")
					$db['product_id']  = $product_id;
				else
					$db['merchant_id']  = $merchant_id;
				
				$db['rdate']        = dbDate();
				$db['last_update']  = dbDate();
				$db['valid']        = $creative_status;
                 
				if ($form_type == '1') {
                                    if (chkUpload('file1_' . $i)) {
										
                                        // '$db['file']' is a path to the file.
										$randomFolder =mt_rand(10000000, 99999999);
										$folder = 'files/banners/tmp/' . $randomFolder ."/";
										 if (!is_dir('files/banners/tmp')) {
											 mkdir('files/banners/tmp');
										 }
										 if (!is_dir($folder)) {
											 mkdir($folder);
										 }
										 
                                        $db['file'] = UploadFile('file1_' . $i, '5120000', 'jpg,jpeg,swf,bmp,gif,png,ico,pdf,pps', '', $folder);
									
                                        if (empty($db['file'])) {
                                            if (!empty($errors)) {
												if($mainBannerType=="products")
                                                _goto($set->SSLprefix.$set->basepage . '?act=products&tab=creative_material&id=' . $product_id . '&errors=' . json_encode($errors));
												else
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&errors=' . json_encode($errors));
                                            } else {
												if($mainBannerType=="products")
                                                _goto($set->SSLprefix.$set->basepage . '?act=products&tab=creative_material&id=' . $product_id);
												else
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id);
                                            }
                                        }
                                        
                                        $intNumberOfInserts = count($db['file']);
                                       // print_r($db['file']);
									   
                                        for ($intCnt = 0; $intCnt < $intNumberOfInserts; $intCnt++) {
											
											$nm = $intCnt +1;
											
											$title = "";
											if(!empty($_POST['name1_' . $i]))
											$title = $_POST['name1_' . $i] . "_" . $nm;
											
                                            $arrData                 = [];
                                            $arrData['file']         = $db['file'][$intCnt];
                                            $arrData['language_id']  = $creative_lang;
                                            $arrData['promotion_id'] = $creative_promotion;
                                            $arrData['category_id']  = $category_id;
                                            $arrData['url']          = trim($creative_url);
                                            $arrData['admin_id']     = $set->userInfo['id'];
											if($mainBannerType == "products")
												$arrData['product_id']  = $product_id;
											else
												$arrData['merchant_id']  = $merchant_id;
											
                                            $arrData['rdate']        = dbDate();
                                            $arrData['last_update']  = dbDate();
                                            $arrData['valid']        = $creative_status;
                                            $arrData['title']        = !empty($title) ? $title : $creative_name . '_' . $nm;
                                            $exp                     = explode('.', $arrData['file']);
                                            $ext                     = strtolower($exp[count($exp) - 1]);
                                            $arrData['type']         = 'swf' == $ext ? 'flash' : 'image';
											
                                            $arrData['alt']          = !empty($_POST['alt1_' . $i]) ? $_POST['alt1_' . $i] : $creative_alt;
                                            
                                            list($arrData['width'], $arrData['height']) = getimagesize($db['file'][$intCnt]);
											
                                            dbAdd($arrData, $appTable, array('scriptCode')); // merchants_creative.
                                        }
                                    }
                                    
				} elseif ($form_type == "2") {
					if (empty($_POST['name2_' . $i]) && 1 == $i) {
                                            $db['title'] = $_POST['creative_name'];
                                            $boolUseDefaultName = true;
					    
					} else {
                                            $db['title'] = $_POST['name2_' . $i];
					}
					
					if (empty($_POST['alt2_' . $i])) {
                                            $db['alt'] = $_POST['creative_alt'];
					} else {
                                            $db['alt'] = $_POST['alt2_' . $i];
					}
					
					if (isset($db['title']) && !empty($db['title'])) { 
                                            $db['type'] = "link";
					    $insert     = 1; 
					}
					$db['affiliateReady'] = 1;
				
				} elseif ($form_type == "3") {
                                    $db['title']      = (!empty($_POST['name3_' . $i]) ? $_POST['name3_' . $i] : $creative_name . '_' . $i);
                                    $db['type']       = "widget";
                                    $db['height']     = $_POST['iframe3_height_' . $i];
                                    $db['width']      = $_POST['iframe3_width_' . $i];
                                    $db['iframe_url'] = $_POST['iframe3_url_' . $i];
                                    $db['affiliateReady'] = 1;
                                    if ($db['title'] && $db['iframe_url'] != "http://" && $db['iframe_url']) {
                                            $insert = 1;
                                    }
					
				} elseif ($form_type == "4") {
                                    if (chkUpload('file4_' . $i)) {
                                        $db['title'] = $_POST['name4_' . $i];
										$randomFolder =mt_rand(10000000, 99999999);
										$folder = 'files/banners/tmp/' . $randomFolder ."/";
										 if (!is_dir('files/banners/tmp')) {
											 mkdir('files/banners/tmp');
										 }
										 if (!is_dir($folder)) {
											 mkdir($folder);
										 }
                                        $db['file']  = UploadFile('file4_' . $i, '5120000', 'jpg,jpeg,swf,bmp,gif,png,ico,pdf,pps', '', $folder);
                                        
                                        if (empty($db['file'])) {
                                            if (!empty($errors)) {
												if($mainBannerType=="products")
                                                _goto($set->SSLprefix.$set->basepage . '?act=products&tab=creative_material&id=' . $product_id . '&errors=' . json_encode($errors));
												else
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&errors=' . json_encode($errors));
                                            } else {
												if($mainBannerType=="products")
                                                _goto($set->SSLprefix.$set->basepage . '?act=products&tab=creative_material&id=' . $product_id);
												else
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id);
                                            }
                                        }
                                        
                                        $exp        = explode(".", $db['file']);
                                        $ext        = strtolower($exp[count($exp) - 1]);
                                        $db['type'] = "mobileleader";
                                        
                                        list($db['width'], $db['height']) = getimagesize($db['file']);
                                        $db['alt'] = (!empty($_POST['alt4_' . $i]) ? $_POST['alt4_' . $i] : $creative_alt);
                                        $insert    = 1;
                                    }
				    
				} elseif ($form_type == "5") {
					if (chkUpload('file5_' . $i)) {
                                            $db['title'] = $_POST['name5_' . $i];
											$randomFolder =mt_rand(10000000, 99999999);
											$folder = 'files/banners/tmp/' . $randomFolder ."/";
											 if (!is_dir('files/banners/tmp')) {
												 mkdir('files/banners/tmp');
											 }
											 if (!is_dir($folder)) {
												 mkdir($folder);
											 }
                                            $db['file']  = UploadFile('file5_' . $i, '5120000', 'jpg,jpeg,swf,bmp,gif,png,ico,pdf,pps', '', $folder);
                                            
                                             if (empty($db['file'])) {
                                            if (!empty($errors)) {
												if($mainBannerType=="products")
                                                _goto($set->SSLprefix.$set->basepage . '?act=products&tab=creative_material&id=' . $product_id . '&errors=' . json_encode($errors));
												else
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&errors=' . json_encode($errors));
                                            } else {
												if($mainBannerType=="products")
                                                _goto($set->SSLprefix.$set->basepage . '?act=products&tab=creative_material&id=' . $product_id);
												else
                                                _goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id);
                                            }
                                        }
                                            
                                            $exp        = explode(".", $db['file']);
                                            $ext        = strtolower($exp[count($exp) - 1]);
                                            $db['type'] = "mobilesplash";

                                            list($db['width'], $db['height']) = getimagesize($db['file']);
                                            $db['alt'] = (!empty($_POST['alt5_' . $i]) ? $_POST['alt5_' . $i] : $creative_alt);
                                            $insert    = 1;
					}
					
				} elseif ($form_type == "6") {
                                    $db['type']       = "mail";
                                     $db['title'] = (!empty($_POST['name6_' . $i]) ? $_POST['name6_' . $i] : $_POST['creative_name']); ;
									 
									 
									 $db['alt'] = (!empty($_POST['alt6_' . $i]) ? $_POST['alt6_' . $i] : $creative_alt);
									$inlinecss        = new instyle();
                                    $strConvertedHtml = $inlinecss->convert($_POST['scriptCode6']);
                                    $db['scriptCode'] = $strConvertedHtml;
                                    $db['affiliateReady'] = 1;
                                    $insert           = 1;
				    
				} elseif ($form_type == "7") {
                                    $db['type'] = "content";
                                    $db['title'] = (!empty($_POST['name7_' . $i]) ? $_POST['name7_' . $i] : $_POST['creative_name']); ;
									 $db['alt'] = (!empty($_POST['alt7_' . $i]) ? $_POST['alt7_' . $i] : $creative_alt);
									$inlinecss        = new instyle();
                                    $strConvertedHtml = $inlinecss->convert($_POST['scriptCode7']);
                                    $db['scriptCode'] = $strConvertedHtml;
									$db['affiliateReady'] = 1;
                                    $insert           = 1;
				}
				
                            if ($insert) {
                                dbAdd($db, $appTable, array('scriptCode'));
                            }
                            
                            // Stop the loop in case of default values usage.
                            if ($boolUseDefaultName) {
                                break;
                            }
			}
			if($set->activateLogs){
			 //activity logs
			$fields =array();
			$fields['ip'] = $set->userInfo['ip'];
			$fields['user_id'] = $set->userInfo['id'];
			$fields['theChange'] = json_encode($db);
			$fields['country'] = '';
			$fields['location'] = 'Creatives - Add Creatives';
			$fields['userType'] = $set->userInfo['level'];
			$fields['_file_'] = __FILE__;
			$fields['_function_'] = 'Add Creatives';
			
			$ch      = curl_init();					
			$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
			
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$result = curl_exec($ch);
			curl_close($ch);
			}
			if (!empty($errors)) {
				if($mainBannerType=="products")
				_goto($set->SSLprefix.$set->basepage . '?act=products&tab=creative_material&id=' . $product_id . '&errors=' . json_encode($errors));
				else
				_goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id . '&errors=' . json_encode($errors));
			} else {
				if($mainBannerType=="products")
				_goto($set->SSLprefix.$set->basepage . '?act=products&tab=creative_material&id=' . $product_id);
				else
				_goto($set->SSLprefix.$set->basepage . '?merchant_id=' . $merchant_id);
			}

		}
	// break;

?>