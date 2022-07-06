<?php

/* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */

function UploadFile($filename, $maxsize = 5120000, $format = 'jpg', $ChosenName = '', $path = '') {
		global $set;
	$arrExtensions = explode(',', $format);
    $uploaded_file = '';
    $arrayFile     = [
        '1' => 'a','2' => 'b','3' => 'c','4' => 'd','5' => 'e','6' => 'f','7' => 'g','8' => 'h','9' => 'i',
        '10' => 'j','11' => 'k','12' => 'l','13' => 'm','14' => 'n','15' => 'o','16' => 'p','17' => 'q',
        '18' => 'r','19' => 's','20' => 't','21' => 'u','22' => 'v','23' => 'w','24' => 'x','25' => 'y',
        '26' => 'z','27' => 'A','28' => 'B','29' => 'C','30' => 'D','31' => 'E','32' => 'F','33' => 'G',
        '34' => 'H','35' => 'I','36' => 'J','37' => 'K','38' => 'L','39' => 'M','40' => 'N','41' => 'O',
        '42' => 'P','43' => 'Q','44' => 'R','45' => 'S','46' => 'T','47' => 'U','48' => 'V','49' => 'W',
        '50' => 'X','51' => 'Y','52' => 'Z',
    ];
    
    if (!$maxsize) {
        $maxsize = 5120000;
    }

    if (is_array($_FILES[$filename]['name'])) {
        for ($i = 0; $i < count($_FILES[$filename]['name']); $i++) {
            $eof = pathinfo($_FILES[$filename]['name'][$i], PATHINFO_EXTENSION);
            $eof = strtolower($eof);
            if (!in_array($eof, $arrExtensions)) {
                return false;
            }
            
            if (
                $_FILES[$filename]['size'][$i] > $maxsize && 
                !in_array($eof, ['pdf', 'pps'])
            ) {
                return false;
            }
        }
        
    } else {
        $eof = pathinfo($_FILES[$filename]['name'], PATHINFO_EXTENSION);
		$eof = strtolower($eof);
        if (!in_array($eof, $arrExtensions)) {
            return false;
        }
        
        if (
            $_FILES[$filename]['size'] > $maxsize && 
            !in_array($eof, ['pdf', 'pps'])
        ) { 
            return false;
        }

    }
    
    if (!$path) {
        $path = $set->SSLprefix.'files/';
    }

	
    if (!is_dir($path)) {
        $path = $set->SSLprefix.'files/';
    }
    
	if (is_array($_FILES[$filename]['name'])) {
        $arrUploadedFilesPaths = [];
        
        for ($i = 0; $i < count($_FILES[$filename]['name']); $i++) {
            if ($ChosenName) {
                $uploaded_file = $ChosenName . '.' . $eof;
            } else {
                $uploaded_file = time() . $arrayFile[rand(1, 30)] . substr(time(), -2) 
                               . $arrayFile[rand(30, 52)] 
                               . $arrayFile[rand(5, 42)] . '.' . $eof;
            }
            
            $uploaded_file = $path . $uploaded_file;
            move_uploaded_file($_FILES[$filename]['tmp_name'][$i], $uploaded_file);
            chmod($uploaded_file, 0777);
            $arrUploadedFilesPaths[] = $uploaded_file;
        }
        
        return $arrUploadedFilesPaths;
        
    } else {
        if ($ChosenName) { 
            $uploaded_file = $ChosenName . '.' . $eof;
        } else {
            $uploaded_file = time() . $arrayFile[rand(1, 30)] . substr(time(), -2) 
                           . $arrayFile[rand(30, 52)] 
                           . $arrayFile[rand(5, 42)] . '.' . $eof;
        }
        $uploaded_file = $path . $uploaded_file;
        move_uploaded_file($_FILES[$filename]['tmp_name'], $uploaded_file);
        chmod($uploaded_file, 0777);
        return $uploaded_file;
    }
}


// OLD VERSION.
/*function UploadFile($filename, $maxsize="5120000",$format="jpg", $ChosenName="", $path="") {
    global $set, $_FILES;
    
    //echo print_r($_FILES, true), '<br /><br />';
    
    if (!$maxsize) {
        $maxsize = "5120000";
    }
    
    if ($_FILES[$filename]['size'] > $maxsize) {
        return false;
    }
    
    $uploaded_file = "";
    $exp = explode(",", $format);
    $count_exp = count($exp) - 1;
    $chkOK = Array();
    
    for ($i = 0; $i <= $count_exp; $i++) {
        $chkOK[$exp[$i]] = "ok";
    }
    
    if (!$path) {
        $path = "files/";
    }
    
    if (!is_dir($path)) {
        $path = "files/";
    }
    
    // UPLOADING CODE
    if ($_FILES[$filename]["name"]) {
        $fileeof = explode(".", $_FILES[$filename]["name"]);
        $num = count($fileeof) - 1;
        $eof = strtolower($fileeof[$num]);
        
        if ($chkOK[$eof] != "ok") {
            return false;
        }
        
        $arrayFile = array(
            "1" => "a","2" => "b","3" => "c","4" => "d","5" => "e","6" => "f","7" => "g","8" => "h","9" => "i",
            "10" => "j","11" => "k","12" => "l","13" => "m","14" => "n","15" => "o","16" => "p","17" => "q",
            "18" => "r","19" => "s","20" => "t","21" => "u","22" => "v","23" => "w","24" => "x","25" => "y",
            "26" => "z","27" => "A","28" => "B","29" => "C","30" => "D","31" => "E","32" => "F","33" => "G",
            "34" => "H","35" => "I","36" => "J","37" => "K","38" => "L","39" => "M","40" => "N","41" => "O",
            "42" => "P","43" => "Q","44" => "R","45" => "S","46" => "T","47" => "U","48" => "V","49" => "W",
            "50" => "X","51" => "Y","52" => "Z",
        );
        
        if ($ChosenName) { 
            $uploaded_file = $ChosenName . "." . $eof;
        } else {
            $uploaded_file = time().$arrayFile[rand(1,30)] . substr(time(), -2) . $arrayFile[rand(30, 52)] . $arrayFile[rand(5, 42)] . "." . $eof;
        }
        
        $uploaded_file = $path.$uploaded_file;

        // $ftpID = ftp_connect($set->ftp_server,21) or die('Cannot connect to ftp server');
        // $result = ftp_login($ftpID, $set->ftp_user, $set->ftp_pass);

        // $fp = fopen($_FILES[$filename]["tmp_name"], 'r');
        // ftp_fput($ftpID, $set->ftp_path.$uploaded_file, $fp, FTP_ASCII);
        // ftp_site($ftpID,'CHMOD 0777 '.$set->ftp_path.$uploaded_file);
        // ftp_close($ftpID);
        move_uploaded_file($_FILES[$filename]["tmp_name"], $uploaded_file);
        chmod($uploaded_file, 0777);
    }
    
    // UPLOADING CODE
    return $uploaded_file;
}*/



function ftCopy($sor,$des) {
	global $set;
	
	$ftpID = ftp_connect($set->ftp_server,21) or die('Cannot connect to ftp server');
	$result = ftp_login($ftpID, $set->ftp_user, $set->ftp_pass);
	
	$fp = fopen($sor, 'r');
	ftp_fput($ftpID, $set->ftp_path.$des, $fp, FTP_ASCII);
	ftp_site($ftpID,'CHMOD 0777 '.$set->ftp_path.$des);
	ftp_close($ftpID);
	
	return true;
	}


function ftDelete($file) {
	global $set;

	if (!file_exists($file)) return false;
	unlink($file);
	// $ftpID = ftp_connect($set->ftp_server,21) or die('Cannot connect to ftp server');
	// $result = ftp_login($ftpID, $set->ftp_user, $set->ftp_pass);
	
	// ftp_delete($ftpID, $set->ftp_path.$file);
	// ftp_close($ftpID);
	
	return true;
	}
	
function uploadField($pic) {
	global $set;
	$html = '<input type="file" name="pic" style="width: 200px;"> '.($pic ? '<a href="'.$pic.'" target="_blank">Preview image</a>' : '');
	return $html;
	}

function fileField($fieldName, $var) {
	$html = '<input type="file" name="'.$fieldName.'"> '.($var ? '<br /><a href="'.$var.'" target="_blank">Preview</a>' : '');
	return $html;
	}

/*function chkUpload($fName="") {
	global $set,$_FILES;
	if (is_uploaded_file($_FILES[$fName]['tmp_name'])) return true;
		 else return false;
	}*/

function chkUpload($fName = '')
{
    if (is_array($_FILES[$fName]['tmp_name'])) {
        foreach ($_FILES[$fName]['tmp_name'] as $file) {
            if (!is_uploaded_file($file)) {
                unset($file);
                return false;
            }
            unset($file);
        }
        
    } elseif (is_uploaded_file($_FILES[$fName]['tmp_name'])) {
        return true;
    } else {
        return false;
    }
    
    return true;
}
        

function writeToFile($filename="",$msg="") {
	if (!$filename OR !$msg) return false;
	$fileadd=fopen($filename,"w+");
	fputs($fileadd, $msg."\n");
	fclose($fileadd);
	return true;
	}
function openFile($filename="") {
	if (!$filename) return false;
	$handle=fopen($filename,"r");
	$content = @fread($handle, filesize($filename));
	fclose($handle);
	return $content;
	}

	
function fixPic($f,$w="75",$h="0",$output="",$prop="0")
{
	global $set;
	if (!file_exists($f)) return $f;

	list($RealWidth, $RealHeight) = GetImageSize($f);
	$choosenW = $w;
	$choosenH = $h;
	if (!$output) $output = $f;
		else ftCopy($f,$output);

	if ($prop == 1) {
		if ($w AND $h) {
			if ($w > $RealWidth AND $h > $RealHeight) {
				return $f;
			}
			
			$newW = round($h*($RealWidth/$RealHeight), 0);
			$newH = round($w*($RealHeight/$newW), 0);
			if ($newW > $w) $h = round($w*($RealHeight/$RealWidth), 0);
			if ($newH > $h) $w = round($h*($RealWidth/$RealHeight), 0);
			
		} else {
			if (!$h) $h = round($w*($RealHeight/$RealWidth), 0);
			if (!$w) $w = round($h*($RealWidth/$RealHeight), 0);
		}
		
	} else if ($prop == 2) {
		$w = $RealWidth;
		$h = $RealHeight;
		
		while (($w > $choosenW) || ($h > $choosenH)) {
			$w = 0.99*$w;
			$h = 0.99*$h;
		}

		$w = round($w);
		$h = round($h);
		
	} else {
		if (!$h) $h = round($w*($RealHeight/$RealWidth), 0);
		if (!$w) $w = round($h*($RealWidth/$RealHeight), 0);
	}

	$exp   = explode(".", $f); 
	$c_exp = count($exp); 
	$ext   = strtolower($exp[$c_exp - 1]);
	
	if ($ext=="jpg" OR $ext=="jpeg") {
		//header('Content-type: image/jpeg');
		
		if ($prop) {
			/*$image_p = imagecreatetruecolor($choosenW, $choosenH);
			$image   = imagecreatefromjpeg($output);
			$white   = imagecolorallocate($image_p, 255, 255, 255);
			imagefilledrectangle($image_p, 0, 0, $choosenW, $choosenH, $white);
			imagecopyresampled($image_p, $image, round(($choosenW-$w)/2,2), round(($choosenH-$h)/2,2), 0, 0, $w, $h, $RealWidth, $RealHeight);
			imagejpeg($image_p, $output, 100);*/
		} else {
			die('test');
			$image_p = @imagecreatetruecolor($w, $h);
			$image = @imagecreatefromjpeg($output);
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $w, $h, $RealWidth, $RealHeight);
			imagejpeg($image_p, $output, 100);
		}
			
	} else if ($ext=="gif") {
			header('Content-type: image/gif');
			// $output = image_resize_transparency($output, $output, $w, $h, $prop);// caused errors...
		
	} else if ($ext=="png") {
			header('Content-type: image/png');
			// $output = image_resize_transparency($output, $output, $w, $h, $prop);  // caused errors...
	}
	
	return $output;
}

function create_zip($files = array(),$destination = '',$overwrite = true) {
    //if the zip file already exists and overwrite is false, return false
    $debug=1;
	if(file_exists($destination) && !$overwrite) { return false; }
    //vars
    $valid_files = array();
    //if files were passed in...
    
	if(is_array($files)) {
		
        //cycle through each file
        foreach($files as $file) {
			// var_dump($file);
            //make sure the file exists
            if(file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    //if we have good files...
    if(count($valid_files)) {
        //create the archive
        $zip = new ZipArchive();
		if ($debug==1) {
				// var_dump($zip);
				// die();
		
		}
        // var_dump($destination);
		// die();
		
		if (file_exists($destination)) {
				$zip->open($destination, ZIPARCHIVE::OVERWRITE);
		} else {
			$zip->open($destination, ZIPARCHIVE::CREATE);
		}

/* 
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			
			// if ($debug==1)
				// die ('55');
            return false;
        } */
		
		
        //add the files
        foreach($valid_files as $file) {
            $zip->addFile($file,$file);
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

        //close the zip -- done!
        $zip->close();
	header("Content-type: application/zip"); 
				header("Content-Disposition: attachment; filename=$destination");
				header("Content-length: " . filesize($destination));
				header("Pragma: no-cache"); 
				header("Expires: 0"); 
				readfile("$destination");
        //check to make sure the file exists
        return file_exists($destination);
    }
    else
    {
        return false;
    }
}



				function zipFilesAndDownload($file_names,$archive_file_name,$file_path)
				{
					
					
					
					global $set;
					//echo $file_path;die;
					$zip = new ZipArchive();
					//create the file and throw the error if unsuccessful
					if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE )!==TRUE) {
					exit("cannot open <$archive_file_name>\n");
				}
				//add each files of $file_name array to archive
				foreach($file_names as $files)
				{
					$zip->addFile($file_path.$files,$files);
					//echo $file_path.$files,$files."

				}
				$zip->close();
				var_dump($zip);
				die();
				//then send the headers to foce download the zip file
				header("Content-type: application/zip"); 
				header("Content-Disposition: attachment; filename=$archive_file_name");
				header("Content-length: " . filesize($archive_file_name));
				header("Pragma: no-cache"); 
				header("Expires: 0"); 
				readfile("$archive_file_name");

				/* header("Content-type: application/zip"); 
				header("Content-Disposition: attachment; filename=$archive_file_name"); 
				header("Pragma: no-cache"); 
				header("Expires: 0"); 
				readfile("$archive_file_name"); */
				exit;
				}
	

function zip_dir($source, $target){

    $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST);

    $zip = new \ZipArchive();
    if($zip->open($target, \ZipArchive::CREATE) !== true)
      exit('cannot create zip');

    foreach($iterator as $file){
      if (!file_exists($file)) { die($file.' does not exist'); }
      if (!is_readable($file)) { die($file.' not readable'); }
      $zip->addFile($file);
      print $file . '<br>';
    }


    $zip->close();
    return $target;
}


	
	
?>