<?php
echo ajax_modal_template_header($template_info['name']) ?>

<?php
echo form_tag(
        'export-form',
        url_for('items/xml_export', 'path=' . $_GET['path'] . '&templates_id=' . $_GET['templates_id'])
    ) . input_hidden_tag('action', 'export') ?>

<div class="modal-body ">

    <p>
        <?php

        if (strlen($template_info['template_filename'])) {
            $item = items::get_info($current_entity_id, $current_item_id);

            $pattern = new fieldtype_text_pattern;
            $filename = $pattern->output_singe_text($template_info['template_filename'], $current_entity_id, $item);

            if ($template_info['transliterate_filename'] == 1) {
                if (!extension_loaded('intl')) {
                    die(alert_error(sprintf(TEXT_PHP_EXTENSION_REQUIRED, 'intl')));
                }

                $filename = app_transliterate_string($filename);
            }
        } else {
            $filename = $template_info['name'] . ' ' . $app_entities_cache[$current_entity_id]['name'] . ' ' . $current_item_id;
        }

        echo TEXT_FILENAME . '<br>' . input_tag('filename', $filename, ['class' => 'form-control input-large required']
            );
        ?>
    </p>


</div>

<?php
echo ajax_modal_template_footer(TEXT_EXPORT)
?>

</form>  

