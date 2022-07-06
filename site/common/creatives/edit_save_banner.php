<?php

		$db['last_update'] = dbDate();
		
		if (chkUpload('file')) {
			$getOldBanner = dbGet($db['id'], $appTable);
			
			if (file_exists($getOldBanner['file'])) {
				ftDelete($getOldBanner['file']);
				$oldFileName = explode("/",$getOldBanner['file']);
				$oldFileName = explode(".",$oldFileName[count($oldFileName)-1]);
			}
			$db['url'] = trim($db['url']);
			
			$randomFolder =mt_rand(10000000, 99999999);
			$folder = 'files/banners/tmp/' . $randomFolder ."/";
			 if (!is_dir('files/banners/tmp')) {
				 mkdir('files/banners/tmp');
			 }
			 if (!is_dir($folder)) {
				 mkdir($folder);
			 }
			if(!isset($db['featured'])){
				$db['featured'] = 0;
			}
			$db['file'] = UploadFile('file', '5120000', 'jpg,gif,swf,jpeg,png', ($oldFileName[0] ? $oldFileName[0] : ''), $folder);
			$exp        = explode(".", $db['file']);
			$ext        = strtolower($exp[count($exp) - 1]);
			
			
			if ($ext == "swf") {
				$db['type'] = "flash";
			} elseif ($db['scriptCode']) {
				$db['type'] == "script";
			} else {
				$db['type'] = "image";
			}
			list($db['width'], $db['height']) = getimagesize($db['file']);
		}

		
		if ($valid) {
			$db['valid'] = 1; 
		} else {
			$db['valid'] = 0;
		}
		// var_dump($db);
		// die();
		dbAdd($db, $appTable);
		if($set->activateLogs){
		//activity logs
		$fields =array();
		$fields['ip'] = $set->userInfo['ip'];
		$fields['user_id'] = $set->userInfo['id'];
		$fields['theChange'] = json_encode($db);
		$fields['country'] = '';
		$fields['location'] = 'Creatives - Save Creative';
		$fields['userType'] = $set->userInfo['level'];
		$fields['_file_'] = __FILE__;
		$fields['_function_'] = 'save_banner';
		
		$ch      = curl_init();					
		$url  = 'http'.$set->SSLswitch.'://'.$_SERVER['HTTP_HOST']."/ajax/saveLogActivity.php";
		
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$result = curl_exec($ch);
		curl_close($ch);
		
		}

?>