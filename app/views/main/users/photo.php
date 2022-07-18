<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_FIELDTYPE_USER_PHOTO_TITLE) ?>

<?= \Helpers\Html::form_tag('user_photo_form', \Helpers\Urls::url_for('main/users/photo/save')) ?>
<div class="modal-body ">


    <center class="uploadifive-user-photo">
        <input style="cursor: pointer" type="file" name="uploadifive_user_photo" id="uploadifive_user_photo"/>
        <button type="button" class="btn btn-default btn-camera" style="display:none"><i
                    class="fa fa-camera"></i> <?= \K::$fw->TEXT_CAMERA ?> </button>

        <div id="uploadifive_user_photo_queue"></div>
    </center>

    <div class="snap-photo-box" style="display:none">
        <video id="camera_video" style="width: 100%; max-height: 350px;" autoplay></video>
        <center>
            <button type="button" class="btn btn-default btn-snap-photo"><i
                        class="fa fa-camera"></i> <?= \K::$fw->TEXT_SNAP_PHOTO ?> </button>
        </center>
        <canvas id="canvas" style="display:none"></canvas>
    </div>


    <div class="user-photo-container" style="display:none">

        <div class="btn-group cropper-buttons">
            <button type="button" class="btn btn-default" data-method="zoom" data-option="0.1">
                <span class="fa fa-search-plus"></span>
            </button>
            <button type="button" class="btn btn-default" data-method="zoom" data-option="-0.1">
                <span class="fa fa-search-minus"></span>
            </button>

            <button type="button" class="btn btn-default" data-method="rotate" data-option="-90">
                <span class="fa fa-undo"></span>
            </button>
            <button type="button" class="btn btn-default" data-method="rotate" data-option="90">
                <span class="fa fa-repeat"></span>
            </button>
        </div>


        <img id="user_photo_image" src="<?= \K::$fw->DOMAIN ?>images/pixel_trans.gif" alt="" style="max-height: 450px;">
    </div>

</div>

<?= \Helpers\App::ajax_modal_template_footer() ?>
</form>

<script src="<?= \K::$fw->DOMAIN ?>js/cropper/4.1.0/dist/cropper.js"></script>
<script src="<?= \K::$fw->DOMAIN ?>js/cropper/4.1.0/dist/jquery-cropper.js"></script>
<link rel="stylesheet" href="<?= \K::$fw->DOMAIN ?>js/cropper/4.1.0/dist/cropper.css">

<?php
$mime_types = \Tools\FieldsTypes\Fieldtype_attachments::get_mime_types();
$fileTypeList = [];
foreach (['gif', 'jpg', 'png'] as $v) {
    foreach ($mime_types[$v] as $vv) {
        $fileTypeList[] = $vv;
    }
}
?>

<script>

    $(function () {

        $('.modal-footer .btn-primary-modal-action').hide();

        if (is_public_layout()) {
            $('#sub-items-form .btn-close,#sub-items-form .close').hide()
        }

        var $image = $('#user_photo_image');

        //upload photo
        $("#uploadifive_user_photo").uploadifive({
            auto: true,
            dnd: false,
            fileType: <?= "['" . implode("','", $fileTypeList) . "']" ?>,
            fileTypeExtra: "gif,jpg,png,jpeg",
            buttonClass: "btn btn-default btn-upload",
            buttonText: "<i class=\"fa fa-upload\"></i> <?= \K::$fw->TEXT_SELECT_IMAGE ?>",
            queueID: "uploadifive_user_photo_queue",
            fileSizeLimit: "4MB",
            multi: false,
            uploadScript: "<?= \Helpers\Urls::url_for('main/users/photo/upload') ?>",
            formData         : {
                "form_session_token" : "<?= \K::$fw->app_session_token ?>"
            },
            onUpload: function (filesToUpload) {

            },
            onUploadComplete: function (file, data) {
                if (data == 'error') return false;

                //alert(data)

                $('.uploadifive-user-photo').hide()
                $('.user-photo-container').show()
                $('.modal-footer .btn-primary-modal-action').show();

                data = JSON.parse(data)

                $('#user_photo_form').attr('attr_filename', data.name);

                $image.attr('src', '<?= \K::$fw->DOMAIN ?>uploads/users/' + data.file + '?r=' + Math.random())

                $image.cropper({
                    aspectRatio: 1 / 1,
                    autoCropArea: 0.5,
                    minCropBoxWidth: 150,
                    dragMode: 'move',
                    viewMode: 1
                });

                $image.on('load', function () {
                    jQuery(window).resize();
                })

            },
            onError: function (errorType) {

            },
            onCancel: function () {

            }
        });

        //snap photo
        // Get access to the camera!
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({video: true}).then(function (stream) {
                $('.btn-camera').show()
            });
        }

        let video_width = 0
        let video_height = 0

        $('.btn-camera').click(function () {
            $('.uploadifive-user-photo').hide()
            $('.snap-photo-box').show()

            var video = document.getElementById('camera_video');

            // Get access to the camera!
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                // Not adding `{ audio: true }` since we only want video now
                navigator.mediaDevices.getUserMedia({video: true}).then(function (stream) {
                    //video.src = window.URL.createObjectURL(stream);
                    video.srcObject = stream;
                    video.play();

                    //get video widht/height
                    video.addEventListener("loadedmetadata", function (e) {
                        video_width = this.videoWidth,
                            video_height = this.videoHeight;

                        var canvas = document.getElementById('canvas');
                        canvas.width = video_width
                        canvas.height = video_height

                        jQuery(window).resize();

                    }, false);
                });
            }
        })

        $('.btn-snap-photo').click(function () {

            $('.snap-photo-box').hide();
            $('.snap-photo-box').after('<div class="fa fa-spinner fa-spin snap-photo-spin"></div>')

            // Elements for taking the snapshot
            var canvas = document.getElementById('canvas');
            var context = canvas.getContext('2d');
            var video = document.getElementById('camera_video');

            //alert(video_width+ '  - ' +video_height)

            context.drawImage(video, 0, 0, video_width, video_height);

            //$image.attr('src',canvas.toDataURL("image/png"));

            $.ajax({
                type: 'POST',
                url: $('#user_photo_form').attr('action'),
                data: {
                    img: canvas.toDataURL("image/png"),
                    filename: '',
                }
            }).done(function (data) {

                $('.user-photo-container').show()

                data = JSON.parse(data)

                $('#user_photo_form').attr('attr_filename', data.name);

                $image.attr('src', data.file + '?r=' + Math.random());
                //alert(data.file)

                $image.cropper({
                    aspectRatio: 1 / 1,
                    autoCropArea: 0.5,
                    minCropBoxWidth: 150,
                    dragMode: 'move',
                    viewMode: 1
                });

                $image.on('load', function () {
                    $('.snap-photo-spin').remove()
                    $('.modal-footer .btn-primary-modal-action').show();

                    jQuery(window).resize();
                })
            })

        })

        //process form
        $('#user_photo_form').submit(function () {
            //alert(1)
            result = $image.cropper('getCroppedCanvas');

            let filename = $(this).attr('attr_filename')

            app_prepare_modal_action_loading($(this))

            $.ajax({
                type: 'POST',
                url: $(this).attr('action'),
                data: {
                    img: result.toDataURL(),
                    filename: filename
                }
            }).done(function () {

                $('.user-photo-preview').html('<img src="' + $image.attr('src') + '?r=' + Math.random() + '" class="user-photo-in-form"><input type="hidden" name="user_photo" value="' + filename + '">')

                $(".btn-delete-user-photo").show();
                $("#delete_user_photo").val(0)

                if (is_dialog()) {
                    close_sub_dialog()
                } else {
                    $('#ajax-modal').modal('toggle');
                }
            })

            return false;
        })

        var options = {
            aspectRatio: 1 / 1,
            crop: function (e) {
                $dataX.val(Math.round(e.detail.x));
                $dataY.val(Math.round(e.detail.y));
                $dataHeight.val(Math.round(e.detail.height));
                $dataWidth.val(Math.round(e.detail.width));
                $dataRotate.val(e.detail.rotate);
                $dataScaleX.val(e.detail.scaleX);
                $dataScaleY.val(e.detail.scaleY);
            }
        };

        // Methods
        $('.cropper-buttons').on('click', '[data-method]', function () {
            var $this = $(this);
            var data = $this.data();
            var cropper = $image.data('cropper');
            var cropped;
            var $target;
            var result;

            if ($this.prop('disabled') || $this.hasClass('disabled')) {
                return;
            }

            if (cropper && data.method) {
                data = $.extend({}, data); // Clone a new one

                if (typeof data.target !== 'undefined') {
                    $target = $(data.target);

                    if (typeof data.option === 'undefined') {
                        try {
                            data.option = JSON.parse($target.val());
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }

                cropped = cropper.cropped;

                switch (data.method) {
                    case 'rotate':
                        if (cropped && options.viewMode > 0) {
                            $image.cropper('clear');
                        }
                        break;
                }

                result = $image.cropper(data.method, data.option, data.secondOption);

                switch (data.method) {
                    case 'rotate':
                        if (cropped && options.viewMode > 0) {
                            $image.cropper('crop');
                        }
                        break;

                    case 'scaleX':
                    case 'scaleY':
                        $(this).data('option', -data.option);
                        break;
                }
            }
        });
    })
</script>