<?php
@session_start();

if (!((!empty($_SESSION['session_id']) && !empty($_SESSION['session_serial'])) || (!empty($_SESSION['aff_session_id']) && !empty($_SESSION['aff_session_serial'])))) {
    die('Access Denied');
}

require_once '../func/func_debug.php';

if ( 
    isset($_POST['affiliate_id']) && !empty($_POST['affiliate_id']) && 
    is_numeric($_POST['affiliate_id']) && 
    isset($_POST['monthyear']) && !empty($_POST['monthyear']) && 
    isset($_POST['doc_type']) && !empty($_POST['doc_type']) && 
    (isset($_FILES['document_upload']['name']) && 
    !empty($_FILES['document_upload']['name']) && 
    is_uploaded_file($_FILES['document_upload']['tmp_name']) && 
    $_FILES['document_upload']['size'] < 5000000) || (isset($_FILES['agreement_upload']['name']) && 
    !empty($_FILES['agreement_upload']['name']) && 
    is_uploaded_file($_FILES['agreement_upload']['tmp_name']) && 
    $_FILES['agreement_upload']['size'] < 5000000) || (isset($_FILES['invoice_upload']['name']) && 
    !empty($_FILES['invoice_upload']['name']) && 
    is_uploaded_file($_FILES['invoice_upload']['tmp_name']) && 
    $_FILES['invoice_upload']['size'] < 5000000)
) {
    require '../common/database.php';
    require '../func/func_string.php';
    
    $date          = new \DateTime();
    $arrDate       = explode('-', $_POST['monthyear']);
    $yearMonth     = $arrDate[0] . '-' . $arrDate[1] . '-' . $arrDate[2] . ' ' . $date->format('H:i:s');
    $randomFolder =mt_rand(10000000, 99999999);
		
			
	if(isset($_FILES['document_upload'])){
		$strTargetDir  = '../files/documents';
		 if (!is_dir('../files/documents')) {
			 mkdir('../files/documents');
		}
			 
		$folder = '../files/documents/tmp/' . $randomFolder ;
			 if (!is_dir('../files/documents/tmp')) {
				 mkdir('../files/documents/tmp');
			 }
    }
	elseif(isset($_FILES['agreement_upload'])){
		$strTargetDir  = '../files/agreements';
		if (!is_dir('../files/agreements')) {
			 mkdir('../files/agreements');
		}
		$folder = '../files/agreements/tmp/' . $randomFolder ;
			 if (!is_dir('../files/agreements/tmp')) {
				 mkdir('../files/agreements/tmp');
			 }
	}
	elseif(isset($_FILES['invoice_upload'])){
		$strTargetDir  = '../files/invoices';
		if (!is_dir('../files/invoices')) {
			 mkdir('../files/invoices');
		}
		$folder = '../files/invoices/tmp/' . $randomFolder;
			 if (!is_dir('../files/invoices/tmp')) {
				 mkdir('../files/invoices/tmp');
			 }
	}
	
     if (!is_dir($folder)) {
			 mkdir($folder);
	 }
   /*  if (!is_dir($strTargetDir)) {
        mkdir($strTargetDir);
    } */
    
    //$strTargetDir .= '/' . $_POST['affiliate_id'];
    $folder .= '/' . $_POST['affiliate_id'];
    
    /* if (!is_dir($strTargetDir)) {
        mkdir($strTargetDir);
    } */
    
	if (!is_dir($folder)) {
			 mkdir($folder);
	 }
	 
    //$strDir               = $strTargetDir;
    $strDir               = $folder;
	$strTargetDir = $folder;
	if(isset($_FILES['document_upload'])){
		$strTargetFile        = $strTargetDir . '/' . basename($_FILES['document_upload']['name']);
    }
	elseif(isset($_FILES['agreement_upload'])){
		$strTargetFile        = $strTargetDir . '/' . basename($_FILES['agreement_upload']['name']);
	}
	elseif(isset($_FILES['invoice_upload'])){
		$strTargetFile        = $strTargetDir . '/' . basename($_FILES['invoice_upload']['name']);
	}
	
	$strExtension         = pathinfo($strTargetFile, PATHINFO_EXTENSION);
	$strExtension         = strtolower($strExtension);
	$arrAllowedExtensions = ['jpg', 'jpeg', 'png', 'doc', 'docx', 'pdf', 'gif', 'txt'];
		
	$uploaded_by_admin_id = isset($_POST['loggedin_user'])?$_POST['loggedin_user']:0;
	
    if (!in_array($strExtension, $arrAllowedExtensions)) {
        echo json_encode(['error' => 'Unsupported file extension.']);
        exit;
    }
    
	if(isset($_FILES['document_upload']))
		$filename = basename($_FILES['document_upload']['name']);
	elseif(isset($_FILES['agreement_upload']))
		$filename = basename($_FILES['agreement_upload']['name']);
	elseif(isset($_FILES['invoice_upload']))
		$filename = basename($_FILES['invoice_upload']['name']);
	
    $sql = "INSERT INTO `documents` (`rdate`, `name`, `path`, `affiliate_id`, `type`, `valid`,`uploaded_by_admin_id`)
            VALUES (
                '" . mysql_real_escape_string($yearMonth) . "', 
                '" . mysql_real_escape_string($filename) . "', 
                '" . mysql_real_escape_string($strTargetFile) . "', 
                 " . mysql_real_escape_string($_POST['affiliate_id']) . ", 
                '" . mysql_real_escape_string($_POST['doc_type']) . "', 
                 " . mysql_real_escape_string(1) . ",
				 " . mysql_real_escape_string( $uploaded_by_admin_id ) . "
            );";
	
    if (!function_mysql_query($sql,__FILE__,__FUNCTION__)) {
        echo json_encode(['error' => 'Failed to insert a new record']);
        exit;
    }
    
    $intLastInsertId = mysql_insert_id();
	
	if(isset($_FILES['document_upload']))
		$strTargetFile   = $strDir . '/' . $intLastInsertId . '_' . basename($_FILES['document_upload']['name']);
	elseif(isset($_FILES['agreement_upload']))
		$strTargetFile   = $strDir . '/' . $intLastInsertId . '_' . basename($_FILES['agreement_upload']['name']);
	elseif(isset($_FILES['invoice_upload']))
		$strTargetFile   = $strDir . '/' . $intLastInsertId . '_' . basename($_FILES['invoice_upload']['name']);
		
    
    $sql             = "UPDATE `documents` "
                     . "SET `path` = '" . mysql_real_escape_string($strTargetFile) . "' "
                     . "WHERE `id` = " . mysql_real_escape_string($intLastInsertId) . ';';
    
    if (!function_mysql_query($sql,__FILE__,__FUNCTION__)) {
        echo json_encode(['error' => 'Failed to update a new record']);
        exit;
    }
    
	if(isset($_FILES['document_upload'])){
			if (!move_uploaded_file($_FILES['document_upload']['tmp_name'], $strTargetFile)) {
				echo json_encode(['error' => 'Cannot upload the file']);
				exit;
			} else {
				echo json_encode(['success' => 'true']);
				exit;
			}
		}
	elseif(isset($_FILES['agreement_upload'])){
			if (!move_uploaded_file($_FILES['agreement_upload']['tmp_name'], $strTargetFile)) {
				echo json_encode(['error' => 'Cannot upload the file']);
				exit;
			} else {
				echo json_encode(['success' => 'true']);
				exit;
			}
	}
	elseif(isset($_FILES['invoice_upload'])){
			if (!move_uploaded_file($_FILES['invoice_upload']['tmp_name'], $strTargetFile)) {
				echo json_encode(['error' => 'Cannot upload the file']);
				exit;
			} else {
				echo json_encode(['success' => 'true']);
				exit;
			}
	}
    
}

echo json_encode(['error' => 'Validation failed']);
exit;
