<h3 class="page-title"><?php
    echo TEXT_EXT_INSTALLATION . ' ' . PLUGIN_EXT_VERSION ?></h3>

<?php
echo '
		<a href="' . url_for(
        'ext/ext/install'
    ) . '" class="btn btn-primary" id="install_btn">' . TEXT_EXT_BUTTON_INSTALL . '</a>
		<div class="fa fa-spinner fa-spin hidden"></div>		
		';
?>

<script>
    $(function () {
        $('#install_btn').click(function () {
            $(this).addClass('hidden');
            $('.fa-spinner').removeClass('hidden')
        })
    })
</script>