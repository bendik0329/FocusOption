<?php
    
    if (!empty($set->showDocumentsModule)) {
        $sql               = 'SELECT * FROM `documents` WHERE `affiliate_id` = ' . (empty($_GET['id']) ? 500 : $_GET['id']) . ' and type NOT IN ("Agreement","Invoice");';
		
        $resourceDocuments = mysql_query($sql);
        $strDocumentsHtml  = '';
        $boolTrLine        = false;
        $arrDocStatuses    = ['not_reviewed', 'disapproved', 'approved'];
        
        while ($arrDocument = mysql_fetch_assoc($resourceDocuments)) {
            $arrFileName   = explode('.', $arrDocument['name']);
            $arrDocType    = explode('_', $arrDocument['type']);
            $strStatusHtml = '<select class="select_document_status">';
            $strDocType    = '';

            foreach ($arrDocStatuses as $status) {
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

            $strStatusHtml .= '</select>';

            foreach ($arrDocType as $type) {
                $strDocType .= ucwords($type) . ' ';
                unset($type);
            }

            if ('Passport Driving Licence ' == $strDocType) {
                $strDocType = 'Passport / Driving Licence';
            }
			$eof = strtolower(pathinfo($arrDocument['path'], PATHINFO_EXTENSION));
            $strDocumentsHtml .= '<tr ' . ($boolTrLine ? 'class="trLine"' : '') 
                              . ' id="tr_doc_' . $arrDocument['id'] . '" data-path="' . $arrDocument['path'] . '">'
                              .  '<td>' . $arrDocument['id'] . '</td>'
                              .  '<td>' . $arrDocument['rdate'] .'</td>'
                              .  '<td class= "tooltip">'.(strpos($arrDocument['path'],'/tmp/')?'<img src="'.$set->SSLprefix.'images/wheel.gif" width=32><span class="tooltiptext" style="padding-bottom:15px">'. lang("System is checking for virus. Please refresh in a minute.") .'</span>':'<a href="javascript:void(0)" onclick="return displayDocument(\'' . $arrDocument['path'] . '\',\''.$eof .'\');">' 
                              . $arrFileName[0] . '</a>')
                              . '</td>'
                              .  '<td>' . substr($strDocType, 0, -1) . '</td>'
                              .  '<td>' . $strStatusHtml . '</td>'
                              .  '<td>'
                              .  (strpos($arrDocument['path'],'/tmp/')?'':'<a href="javascript:void(0)" onclick="return displayDocument(\'' . $arrDocument['path'] . '\',\''.$eof .'\');">' . lang('Download') . '</a>')
                              .  '&nbsp;&nbsp;'
                              .  '<a href="javascript:void(0)" onclick="return deleteDocument(' . $arrDocument['id'] . ', \'' . $arrDocument['path'] . '\');">' 
                              . lang('Delete') . '</a></td>'
                              .  '</tr>';

            unset($arrDocument, $arrFileName, $strDocType, $arrDocType, $strStatusHtml);
            $boolTrLine = $boolTrLine ? false : true;
        }

        unset($sql , $resourceDocuments);

        $set->content .= '
        <script src="'.$set->SSLprefix.'js/ajax_file_upload.js"></script>
        <script type="text/javascript">
		var fileUpload = false;
            function submitDocument() {
				fileUpload = true;
                var intAffId     = $("#form_upload_documents [name=affiliate_id]").val();
                var strMonthYear = $("#form_upload_documents [name=monthyear]").val();
                var strDocType   = $("#form_upload_documents [name=doc_type]").val();
				var user_id   = $("#form_upload_documents [name=loggedin_user]").val();

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
                    doc_type     : strDocType,
					loggedin_user: user_id
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
			<input type="hidden" name="loggedin_user" id="loggedin_user" value="'. $set->userInfo['id'] .'"/>
                <table>
                    <tr>
                        <td>' . lang('Choose date') . ':</td>
                        <td><input type="text" name="monthyear" /></td>
                    </tr>
                    <tr>
                        <td>' . lang('Affiliate ID') . ':</td>
                        <td>
                            <input type="text" name="affiliate_id" value='. $_GET['id'] .' />
                        </td>
                    </tr>
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
            </form>
        </div>

        <div id="document_fancybox" style="display:none;">
            <iframe id="document_fancybox_iframe" style="width:500px;height:400px;"></iframe>
        </div>';

        $set->content .= '
            <script type="text/javascript">
			
                $(function() {
                    var currentDate = new Date();  
					$("[name=monthyear]").datepicker({
                        showMonthAfterYear: true,
                        disabled: true,
                        dateFormat: "yy-mm-dd",
                    });
					$("[name=monthyear]").datepicker().datepicker("setDate", new Date("yy-mm-dd"));
                    var dialogSigned = $("#document_dialog").dialog({
                        position: { my: "center", at: "center", of: window },
                        autoOpen: false,
                        resizable: false,
                        draggable: false,
                        height: 195,
                        width: 370,
                        modal: true,
                        title: "Upload new document",
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
							if(fileUpload == true){
								var $tabValue = window.location.href;
								var $withoutHash = $tabValue.substr(0,$tabValue.indexOf("#"));
								
								loc=$withoutHash.replace(/&?toggleTo=([^&]$|[^&]*)/i, "");
								window.location.href= loc + "&toggleTo=verification_documents#tabs1-document";
								window.location.reload(true);
							}
                        }
                    });
                });

                function uploadNewDocument() {
                    $("#document_dialog").dialog("open");
                    return false;
                }

                function displayDocument(strPath,ext) {
                     $("#document_fancybox_iframe").attr("src", '.urldecode('+ strPath +') .');
					
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
                    if (confirm("'.lang('Chosen document will be deleted').'")) {
                        var obj = {
                            id   : intId,
                            path : strPath
                        };

                        $.post("'.$set->SSLprefix.'ajax/DeleteDocument.php", obj, function(res) {
                            if ("1" == res) {
                                var $tabValue = window.location.href;
								var $withoutHash = $tabValue.substr(0,$tabValue.indexOf("#"));
								
								loc=$withoutHash.replace(/&?toggleTo=([^&]$|[^&]*)/i, "");
								window.location.href= loc + "&toggleTo=verification_documents#tabs1-document";
								window.location.reload(true);
                            } else {
                                console.log(res);
                            }
                        });
                    }

                    return false;
                }
            </script>
        ';

        $set->content .= '<!--<div class="normalTableTitle" style="cursor: pointer;">'.lang('Documents').'</div>-->
                                <div id="tab_13" style="width: 100%; background: #F8F8F8;">
                                    <table class="normal tblDetails" width="100%" border="0" cellpadding="3" cellspacing="0">
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

        $set->content .= $strDocumentsHtml . '</tfoot></table><br />
            <div style="float:right;">
            <!--a href="javascript:void(0)" style="cursor:pointer;background-color:#2FB956;color:#fff;text-transform:uppercase;
                     font-size:14px;border:0;padding:5px 30px 5px 30px;" onclick="alert(\'Statuses saved!\');return false;">' . lang('Save Status') . '
            </a>&nbsp;-->
            <a href="javascript:void(0)" 
               style="cursor:pointer;background-color:#2FB956;color:#fff;text-transform:uppercase;
                      font-size:14px;border:0;padding:5px 30px 5px 30px;" 
               onclick="return uploadNewDocument();">' . lang('Upload new document') . '
            </a></div>
            <br /><br />
            </div>
		
            <script type="text/javascript">
                $(".select_document_status").change(function() {
                    var arrDocId = $(this).parent().parent().attr("id").split("_");
                    var strValue = $(this).val();
                    var obj      = {
                        param : "doc_status",
                        id    : arrDocId[arrDocId.length - 1],
                        value : strValue
                    };

                    $.post("'.$set->SSLprefix.'ajax/UpdateDocuments.php", obj, function(res) {
                        if ("1" == res) {
                            //alert("Document status was updated!");
							
							$.fancybox({ 
						 closeBtn:false, 
						  minWidth:"250", 
						  minHeight:"180", 
						  autoCenter: true, 
						  afterLoad:function(){
							  setTimeout(function () { $.fancybox.close(); }, 3000); 
						  },
						  content: "<div align=\'center\' style=\'margin-top:40px;\'><div id=\'alert1\' style=\'display:block;margin:0px auto;\'><h2>'.lang('Document status was updated.').'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\'Continue\' onClick=\'$.fancybox.close()\'></div></div>" 
						  });
                        } else {
                            console.log("\n" + res + "\n");
                            //alert("'.lang('Document status was not updated due to unexpected error').'");
							$.fancybox({ 
						 closeBtn:false, 
						  minWidth:"250", 
						  minHeight:"180", 
						  autoCenter: true, 
						  afterLoad:function(){
							  setTimeout(function () { $.fancybox.close(); }, 3000); 
						  },
						  content: "<div align=\'center\' style=\'margin-top:40px;\'><div id=\'alert1\' style=\'display:block;margin:0px auto;\'><h2>'.lang('Document status was not updated due to unexpected error.').'</h2></div><div style=\'margin-top:30px;\'><input type=\'button\' class=\'btnContinue\' class=\'btnContinue\' value=\'Continue\' onClick=\'$.fancybox.close()\'></div></div>" 
						  });
                        }
                    });
                    
                    return false;
                });
            </script>
            ';
    }

