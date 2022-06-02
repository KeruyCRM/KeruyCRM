<?php

$attachments_form_token = md5($app_user['id'] . time() . 'mail');

//$timestamp = time();
//$form_token = md5($app_user['id'] . $timestamp);

$html = '
      
      <input style="cursor: pointer" type="file" name="uploadifive_attachments_upload" id="uploadifive_attachments_upload" />

      <script type="text/javascript">

      var is_file_uploading = null;

  		$(function() {
  			$("#uploadifive_attachments_upload").uploadifive({
  				"auto"             : true,
          "dnd"              : false,
          "buttonClass"      : "btn btn-default btn-upload",
          "buttonText"       : "<i class=\"fa fa-upload\"></i> ' . TEXT_ADD_ATTACHMENTS . '",
  				"formData"         : {
  									   "timestamp" : ' . time() . ',
  									   "token"     : "' . $attachments_form_token . '"
  				                     },
  				"queueID"          : "uploadifive_queue_list",
          "fileSizeLimit" : "' . CFG_SERVER_UPLOAD_MAX_FILESIZE . 'MB",
  				"uploadScript"     : "' . url_for('ext/mail/accounts', 'action=attachments_upload', true) . '",
          "onUpload"         :  function(filesToUpload){
            is_file_uploading = true;
          },
  				"onQueueComplete" : function(file, data) {
            is_file_uploading = null
            $(".uploadifive-queue-item.complete").fadeOut();
            $("#uploadifive_attachments_list").append("<div class=\"loading_data\"></div>");
            $("#uploadifive_attachments_list").load("' . url_for(
        'ext/mail/accounts',
        'action=attachments_preview&token=' . $attachments_form_token
    ) . '");

          }
  			});

        $("button[type=submit]").bind("click",function(){
            if(is_file_uploading)
            {
              alert("' . TEXT_PLEASE_WAYIT_FILES_LOADING . '"); return false;
            }
          });

  		});
	</script>
    ';


echo $html;
?>

<div id="uploadifive_attachments_list"></div>
<div id="uploadifive_queue_list"></div>

<script>
    function mail_attachment_remove(id) {
        $('.attachment-row-' + id).hide();

        $.ajax({
            type: 'POST',
            url: '<?php echo url_for("ext/mail/accounts", "action=attachment_delete") ?>',
            data: {id: id}
        }).done(function () {
            $("#uploadifive_attachments_list").load("<?php echo url_for(
                'ext/mail/accounts',
                'action=attachments_preview&token=' . $attachments_form_token
            ) ?>");
        })
    }
</script>
