<?php
    
    /* Affiliate Software [ Encode in UTF-8 Without BOM ] [ ☺ ] */
    require_once('common/global.php');
    
 $lout = !empty($set->SSLprefix) ? $set->SSLprefix:"/affiliate/";
if (!isLogin()) _goto( $lout);


if (empty($set->showDocumentsModule)) {
        _goto($lout);
	
}
    
    if (
        isset($_POST['delete']) && isset($_POST['doc_id']) && 
        !empty($_POST['doc_id']) && isset($_POST['path']) &&  
        is_numeric($_POST['doc_id']) && !empty($_POST['path'])
    ) {
        $_POST['path'] = str_replace('../files', '../public_html/files', $_POST['path']);
        
        if (@unlink($_POST['path'])) {
            $sql = 'DELETE FROM `documents` WHERE `id` = ' . mysql_real_escape_string($_POST['doc_id']) . ';';
            @function_mysql_query($sql,__FILE__);
            unset($sql);
        }
    }
    
    
    $pageTitle    = lang('Documents');
	$set->breadcrumb_title =  lang($pageTitle);
			$set->pageTitle = '
			<style>
			.pageTitle{
				padding-left:0px !important;
			}
			</style>
			<ul class="breadcrumb">
				<li><a href="'.$set->SSLprefix.'affiliate/">'.lang('Dashboard').'</a></li>
				<li><a href="'. $set->SSLprefix.$set->uri .'">'.lang($pageTitle).'</a></li>
				<li><a style="background:none !Important;"></a></li>
			</ul>';
			
    $sql               = 'SELECT * FROM `documents` WHERE `affiliate_id` = ' . $set->userInfo['id'] . ';';
    $resourceDocuments = function_mysql_query($sql,__FILE__);
    $strDocumentsHtml  = '';
    $boolTrLine        = false;
    $arrDocStatuses    = ['not_reviewed', 'disapproved', 'approved'];
    
    while ($arrDocument = mysql_fetch_assoc($resourceDocuments)) {
        $arrFileName   = explode('.', $arrDocument['name']);
        $arrDocType    = explode('_', $arrDocument['type']);
        //$strStatusHtml = '<select class="select_document_status">';
        $strStatusHtml = '';
        $strDocType    = '';
        
        $arr = explode('_', $arrDocument['doc_status']);
        
        foreach ($arr as $doc) {
            $strStatusHtml .= ucwords($doc) . ' ';
            unset($doc);
        }
        
        $strStatusHtml = substr($strStatusHtml, 0, -1);
        
        /*foreach ($arrDocStatuses as $status) {
            $arrDocStatus = explode('_', $status);
            $strStatus    = '';

            foreach ($arrDocStatus as $stat) {
                $strStatus .= ucwords($stat) . ' ';
                unset($stat);
            }
            
            $strStatusHtml .= '<option ' . ($arrDocument['doc_status'] == $status ? 'selected' : '') 
                           .  ' value="' . $status . '">' 
                           .  substr($strStatus, 0, -1) 
                           .  '</option>';

            unset($status, $strStatus, $arrDocStatus);
        }

        $strStatusHtml .= '</select>';*/

        foreach ($arrDocType as $type) {
            $strDocType .= ucwords($type) . ' ';
            unset($type);
        }

        if ('Passport Driving Licence ' == $strDocType) {
            $strDocType = 'Passport / Driving Licence';
        }
		$eof = strtolower(pathinfo($arrDocument['path'], PATHINFO_EXTENSION));
		
		
        $strHtmlDeleteDisapproved = '&nbsp;<input type="submit" name="delete" style="font-size:10px!important;padding:1px!important;"  
                                     value="' . lang('Delete') . '" />
                                     <input type="hidden" name="doc_id" value="' . $arrDocument['id'] . '" />
                                     <input type="hidden" name="path" value="' . $arrDocument['path'] . '" />';
        
        $strDocumentsHtml .= '<tr ' . ($boolTrLine ? 'class="trLine"' : '') 
                          . ' id="tr_doc_' . $arrDocument['id'] . '" data-path="' . $arrDocument['path'] . '">'
                          .  '<td>' . $arrDocument['id'] . '</td>'
                          .  '<td>' . $arrDocument['rdate'] .'</td>'
                          .  '<td>'.(strpos($arrDocument['path'],'/tmp/')?'<img src="'.$set->SSLprefix.'images/wheel.gif" width=32><br/><span class="tooltiptext" style="padding-bottom:15px">'. lang("System is checking for virus. Please refresh in a minute.") .'</span>':'<a href="javascript:void(0)" onclick="return displayDocument(\'' . $arrDocument['path'] . '\',\''.$eof.'\');">' 
                          . $arrFileName[0] . '</a>')
                          . '</td>'
                          .  '<td>' . substr($strDocType, 0, -1) . '</td>'
                          .  '<td>' . $strStatusHtml . ('Disapproved' == $strStatusHtml ? $strHtmlDeleteDisapproved : '') 
                          . '</td>'
                          .  '<td>'
                          .   (strpos($arrDocument['path'],'/tmp/')?'':'<a href="javascript:void(0)" onclick="return displayDocument(\'' . $arrDocument['path'] . '\',\''.$eof.'\');">' . lang('View') . '</a>')
                          .  '&nbsp;&nbsp;'
                          .  '</td>'
                          .  '</tr>';
        
        unset($arrDocument, $arrFileName, $strDocType, $arrDocType, $strStatusHtml, $strHtmlDeleteDisapproved);
        $boolTrLine = $boolTrLine ? false : true;
    }
    
    unset($sql , $resourceDocuments);
    
    $date          = new \DateTime();
    $set->content .= '
    <script src="'.$set->SSLprefix.'js/ajax_file_upload.js"></script>
    <script type="text/javascript">
        function submitDocument() {
            var intAffId     = ' . $set->userInfo['id'] . ';
            var strMonthYear = "' . $date->format('Y-m-d') . '";
            var strDocType   = $("#form_upload_documents [name=doc_type]").val();
            
            if (
                undefined == intAffId ||
                0 == intAffId.length ||
                isNaN(intAffId)
            ) {
                alert("Affiliate ID is invalid");
                return false;
            }
            
            if (
                undefined == strMonthYear ||
                0 == strMonthYear.length
            ) {
                alert("Date is invalid");
                return false;
            }
            
            var postData = {
                affiliate_id : intAffId,
                monthyear    : strMonthYear,
                doc_type     : strDocType
            };
            
            var objOptions = {
                in_progress           : "images/8-0.gif",
                done_src              : "images/Ok.png",
                progress_container_id : "img_progress"
            };
            
            return uploadFile("'.$set->SSLprefix.'ajax/UploadDocuments.php", "form_upload_documents", "document_upload", postData, objOptions);
        }
    </script>
    
    <div id="document_dialog">
        <form id="form_upload_documents" enctype="multipart/form-data" method="POST" action="'.$set->SSLprefix.'ajax/UploadSignedAgreement.php">
            <table>
                <tr>
                    <td>' . lang('Document type') . ':</td>
                    <td>
                        <select name="doc_type">
                            <option value="Passport_Driving_Licence" selected>' . lang('Passport/Driving Licence') . '</option>
                            <option value="Address_Verification">' . lang('Address Verification') . '</option>
                            <option value="Company_Verification">' . lang('Company Verification') . '</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="file" name="document_upload" id="id_document_upload" /></td>
                </tr>
                <tr>
                    <td></td>
                    <td><img id="img_progress" style="display:none;height:15px;width100%;" src="" /></td>
                </tr>
                <tr>
                    <td>
                        <a href="javascript:void(0)" 
                           style="float:left;"
                           onclick="$(\'#document_dialog\').dialog(\'close\');">
                        <img style="position:absolute;float:right!important;top:-15px;right:-15px;" src="images/x_btn.png" />
                        </a>
                    </td>
                    <td align="left">
                        <a href="javascript:void(0)" 
                           onclick="return submitDocument();" 
                           style="float:right;padding:9px;border-radius:9px;color:#fff;background-color:#234B7F;cursor:pointer;">
                            ' . lang('Submit document') . '
                        </a>
                    </td>
                </tr>
            </table>
			<style>
		span.ui-icon.ui-icon-closethick {
display:none;
		}		
			</style>
        </form>
    </div>

    <div id="document_fancybox" style="display:none;">
        <iframe id="document_fancybox_iframe" style="width:500px;height:400px;"></iframe>
    </div>';

    $set->content .= '
        <script type="text/javascript">
            $(function() {
                $("[name=monthyear]").datepicker({
                    showMonthAfterYear: true,
                    disabled: true,
                    dateFormat: "yy-mm-dd"
                });
                
                var dialogSigned = $("#document_dialog").dialog({
                    position: { my: "center", at: "center", of: window },
                    autoOpen: false,
                    resizable: false,
                    draggable: false,
                    height: 140,
                    width: 370,
                    modal: true,
                    title: "'.lang('Upload New Document').'",
                    show: {
                        effect: "blind",
                        duration: 500
                    },
                    hide: {
                        effect: "explode",
                        duration: 500
                    },
                    open: function(event, ui) {
                        $("[name=monthyear]").datepicker("enable");
                    },
                    close: function(event, ui) {
                        window.location.reload(true);
                    }
                });
            });
            
            function uploadNewDocument() {
                $("#document_dialog").dialog("open");
                return false;
            }
            
            function displayDocument(strPath,ext) {
                $("#document_fancybox_iframe").attr("src", strPath);
				if(ext != "gif" && ext != "jpg" && ext != "png")
				window.location.href = "'.$set->SSLprefix.'common/downloadFile.php?filename=" + strPath;
				else{
					 $.fancybox({
						 href : "#document_fancybox"
					 });
				}
                return false;
            }
            
            function deleteDocument(intId, strPath) {
                if (confirm("Chosen document will be deleted")) {
                    var obj = {
                        id   : intId,
                        path : strPath
                    };
                    
                    $.post("ajax/DeleteDocument.php", obj, function(res) {
                        if ("1" == res) {
                            document.location.reload(true);
                        } else {
                            console.log(res);
                        }
                    });
                }
                
                return false;
            }
        </script>
    ';
    
    $set->content .= '<div id="tab_13" style="width: 100%; background: #F8F8F8;">
                        <form method="post">
                            <table class="normal" width="100%" border="0" cellpadding="3" cellspacing="0">
                                <thead>
                                    <tr style="background: #D9D9D9;">
                                        <td>'.lang('ID').'</td>
                                        <td>'.lang('Date Recieved').'</td>
                                        <td>'.lang('Document Name').'</td>
                                        <td>'.lang('Type').'</td>
                                        <td>'.lang('Status').'</td>
                                        <td>'.lang('Action').'</td>
                                    </tr>
                                </thead>
                                <tfoot>';
    
    $set->content .= $strDocumentsHtml . '</tfoot></table></form><br />
        <div style="float:right;">
        <a href="javascript:void(0)" 
           style="cursor:pointer;background-color:#2FB956;color:#fff;text-transform:uppercase;
                  font-size:14px;border:0;padding:5px 30px 5px 30px;" 
           onclick="return uploadNewDocument();">' . lang('Upload new document') . '
        </a></div>
        <br /><br />
        </div><br />';
    
    theme();
    