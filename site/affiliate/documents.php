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
                <li><a href="'. $set->SSLprefix.$set->uri .'"> My Account - '.lang($pageTitle).'</a></li>
                <li><a style="background:none !Important;"></a></li>
            </ul>';
            
    $sql               = 'SELECT * FROM `documents` WHERE `affiliate_id` = ' . $set->userInfo['id'] . ';';
    $resourceDocuments = function_mysql_query($sql,__FILE__);
    $strDocumentsHtml  = '';
    $boolTrLine        = false;
    $arrDocStatuses    = ['not_reviewed', 'disapproved', 'approved'];
    $i = 1;
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
                          .  '<td>' . $i . '</td>'
                          .  '<td>' . $arrDocument['id'] . '</td>'
                          // . '<td>' . $arrDocument['rdate'] .'</td>'
                          .  '<td>'.(strpos($arrDocument['path'],'/tmp/')?'<span class="tooltiptext" style="margin:0; max-width:410px; text-align:left; width:100%;">'. lang("System is checking for virus. Please refresh in a minute.") .'</span>':'<a href="javascript:void(0)" onclick="return displayDocument(\'' . $arrDocument['path'] . '\',\''.$eof.'\');">' 
                          . $arrFileName[0] . '</a>')
                        //   <img src="'.$set->SSLprefix.'images/wheel.gif" width=32>
                          . '</td>'
                          .  '<td>' . substr($strDocType, 0, -1) . '</td>'
                          . '<td>' . $arrDocument['rdate'] .'</td>'
                          // .  '<td>' . $strStatusHtml . ('Disapproved' == $strStatusHtml ? $strHtmlDeleteDisapproved : '') 
                          . '</td>'
                          .  '<td>'
                          .   (strpos($arrDocument['path'],'/tmp/')?'':'<a href="javascript:void(0)" onclick="return displayDocument(\'' . $arrDocument['path'] . '\',\''.$eof.'\');">' . lang('View') . '</a>')
                          .  '&nbsp;&nbsp;'
                          .  '</td>'
                          .  '<td>'
                          .  '-'
                          .  '</td>'
                          .  '</tr>';
        $i++;
        
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
            // alert("TEST");
            $(".modal").modal("hide");
            $(".doc-file-modal").show();
            $(".chosse-file").hide();
            return uploadFile("'.$set->SSLprefix.'ajax/UploadDocuments.php", "form_upload_documents", "document_upload", postData, objOptions);
        }
    </script>';
    
    

   

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
                            $(".doc-file-modal").show();
                            $(".chosse-file").addClass("d-none");
                        } else {
                            console.log(res);
                        }
                    });
                }
                
                return false;
            }
        </script>
    ';
    // style="width: 100%; background: #F8F8F8;
    $set->content .= '
    <style>
        span.ui-icon.ui-icon-closethick {
            display:none;
        }       
            </style>
            
    <div class="account-table creative-page-filter ">
                        <div class="top-performing-creative h-full com-page">
                        <div class="documents-page-uplord-button">
                            <div class="search-wrp Commission-Structure-s">
                                <p>Search documents</p>
                                <div class="search-box">
                                    <input type="text" name="q" value="">
                                    <button type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <div class="uplord-new-doc">
                            <a href="javascript:void(0)" 
                            onclick="return uploadNewDocument();">
                               <button type="button" class="btn uplord-button" data-toggle="modal" data-target="#exampleModalCenter">
                               ' . lang('Upload new document') . '
                                </button>

                                <!-- Modal -->
                                <div class="modal fade doc-modal-smoll" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <form id="form_upload_documents" enctype="multipart/form-data" method="POST" action="'.$set->SSLprefix.'ajax/UploadSignedAgreement.php">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h4 class="modal-title" id="exampleModalLongTitle">Upload new document</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="uplord-doc-section">
                                                        <div class="duc-type-heading">' . lang('Document type') . ':</div>
                                                        <div class="doc-type-select">
                                                            <select name="doc_type">
                                                                <option value="Passport_Driving_Licence" selected>' . lang('Passport/Driving Licence') . '</option>
                                                                <option value="Address_Verification">' . lang('Address Verification') . '</option>
                                                                <option value="Company_Verification">' . lang('Company Verification') . '</option>
                                                            </select>
                                                        </div>
                                                        <div class="dhrec-input">
                                                            <label class="doc-file-modal" for="id_document_upload">
                                                                <p class="choose_doc">Choose document <i class="fa fa-file"></i></p>
                                                                <input class="d-none" type="file" name="document_upload" id="id_document_upload" />
                                                            </label>
                                                            <div class="chosse-file d-none">
                                                                <p id="file_chhosen"></p>
                                                                <span id="delete_image">Delete</span>
                                                            </div>
                                                        </div>                                  
                                                        <div class="submit-doc">
                                                            <a href="javascript:void(0)" 
                                                               onclick="return submitDocument();" 
                                                               >
                                                                ' . lang(' Submit document <i class="fa fa-upload"></i>') . '
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </a></div>
                            </div>
                            <div class="performing-creative-table doc-table-p">
                                <div class="table-responsive">
                                    <table id="table_id" class="table" width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <thead>
                                            <tr>
                                            <th scope="col">'.lang('#').'</th>
                                            <th scope="col">'.lang('ID').'</th>
                                            <th scope="col">'.lang('Document Name').'</th>
                                            <th scope="col">'.lang('Type').'</th>
                                            <th scope="col">'.lang('Date Recieved').'</th>
                                            <th scope="col">'.lang('Status').'</th>
                                            <th scope="col">'.lang('Action').'</th>
                                            </tr>
                                        </thead>
                                        <tfoot class="topCreativesCls">
                                        ';
    
    $set->content .= $strDocumentsHtml . '
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>  
                    </div>
            
    </form><br />
       
        <br /><br />
        </div><br />

        <script>
        $("#id_document_upload").change(function(e){
            var file = $("#id_document_upload")[0].files[0]
            if (file){
                $(".doc-file-modal").hide();
                $(".chosse-file").removeClass("d-none");
                $("#file_chhosen").html(file.name);
            }
        });
        $("#delete_image").click(function(e){
            if (confirm("Chosen document will be deleted")) {
                $(".doc-file-modal").show();
                $("#file_chhosen").html("");
                $(".chosse-file").addClass("d-none");
            }
        });
           
        </script>';
    
    //     <a href="javascript:void(0)" 
    //     style="float:left;"
    //     onclick="$(\'#document_dialog\').dialog(\'close\');">
    //  <img style="position:absolute;float:right!important;top:-15px;right:-15px;" src="images/x_btn.png" />
    //  </a>

    // <a href="javascript:void(0)" 
    // onclick="return uploadNewDocument();">
    theme();
    