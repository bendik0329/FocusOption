/* 
 * Upload a file via an ajax call.
 */

/**
 * Upload a file via ajax call.
 * 
 * @param  string action
 * @param  string formId
 * @param  string fileInputName
 * @param  object objPostData
 * @param  object objOptions
 * @return bool
 */
function uploadFile(action, formId, fileInputName, objPostData, objOptions) {
    var inProgressSrc       = ('in_progress' in objOptions) ? objOptions['in_progress'] : null;
    var doneSrc             = ('done_src' in objOptions) ? objOptions['done_src'] : null;
    var progressContainerId = ('progress_container_id' in objOptions) ? objOptions['progress_container_id'] : null;
    var optionsAvailable    = !(null == inProgressSrc || null == doneSrc || null == progressContainerId);
    
    var file     = document.querySelector('#' + formId + ' input[name=' + fileInputName + ']').files[0];
    var formData = new FormData();
    var reader   = new FileReader();
    
    if (file != null && file != undefined) {
        reader.readAsDataURL(file);
        formData.append(fileInputName, file);
    } else {
        alert("Please, choose a file to upload");
        return false;
    }
    
    for (var attr in objPostData) {
        if (objPostData[attr] != null && objPostData[attr] != undefined && objPostData[attr] != '') {
            formData.append(attr, objPostData[attr]);
        } else {
            alert("'" + attr + "' is missing");
            return false;
        }
    }
    
    if (optionsAvailable) {
        $('#' + progressContainerId).hide();
        $('#' + progressContainerId).css('width', '100%');
        $('#' + progressContainerId).attr('src', inProgressSrc).show();
    }

    $.ajax({
        type: 'POST',
        url : action,
        processData: false,
        contentType: false,
        data: formData,
        success: function(response) {
            console.log('Ajax log:\n' + response + '\n');
            
            try {
                response    = JSON.parse(response);
                var message = document.querySelector('#' + formId + ' input[name=' + fileInputName + ']').value;
		
                if (response['success']) {
                    message += '\n  was successfully uploaded!';
                    
                    if (optionsAvailable) {
                        $('#' + progressContainerId).css('width', '').attr('src', doneSrc);
                    }
                    
                } else {
                    message += '\n was not uploaded due to unexpected error (1).';
                    console.log(response['error']);
                }
                
            } catch (error) {
                message += '\n was not uploaded due to unexpected error (2).';
                console.log(error);
            }
	    
            // Reset initial input fields.
            for (var attr in objPostData) {
                if ($('#' + formId + ' input[name=' + attr + ']').attr('type') != 'hidden') {
                    $('#' + formId + ' input[name=' + attr + ']').val('');
                }
            }
	    
            document.querySelector('#' + formId + ' input[name=' + fileInputName + ']').value = '';
            
            if (!optionsAvailable) {
                alert(message);
            }
        }
    });
    
    return false;
}
