<?php
echo ajax_modal_template_header(TEXT_HEADING_COPY) ?>

<?php
echo form_tag(
    'form-copy-to',
    url_for('ext/with_selected/copy_single', 'action=copy_single&path=' . $_GET['path']),
    ['class' => 'form-horizontal']
) ?>


<div class="modal-body ajax-modal-width-790">
    <div id="modal-body-content">
        <p><?php
            echo TEXT_COPY_SINGLE_CONFIRMATION ?></p>

        <?php
        $entity_info = db_find('app_entities', $current_entity_id);
        if ($entity_info['parent_id'] > 0) {
            $report_info = reports::create_default_entity_report($entity_info['id'], 'entity_menu');

            //check if parent reports was not set
            if ($report_info['parent_id'] == 0) {
                reports::auto_create_parent_reports($report_info['id']);

                $report_info = db_find('app_reports', $report_info['id']);
            }

            $path_parsed = items::parse_path($_GET['path']);
            $parent_path_info = items::get_path_info(
                $path_parsed['parent_entity_id'],
                $path_parsed['parent_entity_item_id']
            );
            $copy_to_default = $parent_path_info['full_path'] . '/' . $current_entity_id;

            $choices = [];
            $path_parsed = items::parse_path($app_path);
            $choices[$path_parsed['parent_entity_item_id']] = items::get_heading_field(
                $path_parsed['parent_entity_id'],
                $path_parsed['parent_entity_item_id']
            );
            $selected = $path_parsed['parent_entity_item_id'];


            echo '
      		<div class="form-group">
                    <label class="col-md-3 control-label" for="settings_copy_comments">' . TEXT_COPY_TO . '</label>
                    <div class="col-md-9">    						
                    ' . select_entities_tag(
                    'copy_to',
                    $choices,
                    $selected,
                    [
                        'entities_id' => $entity_info['parent_id'],
                        'class' => 'form-control required',
                        'data-placeholder' => TEXT_ENTER_VALUE
                    ]
                ) . '
                    </div>
		</div>
                ';
        }

        require(component_path('ext/with_selected/copy_options'));

        //check nested
        $check_query = db_query(
            "select id from app_entity_{$current_entity_id} where parent_id={$current_item_id} limit 1"
        );
        if ($check = db_fetch_array($check_query)) {
            echo '
                <div class="form-group">
                    <label class="col-md-3 control-label" for="settings_copy_comments">' . TEXT_EXT_COPY_NESTED_ITEMS . '</label>
                    <div class="col-md-9">
                      ' . select_tag(
                    'settings[copy_nested_items]',
                    ['0' => TEXT_NO, '1' => TEXT_YES],
                    0,
                    ['class' => 'form-control input-small']
                ) . '
                    </div>
                </div>
								';
        }

        ?>

        <div class="form-group">
            <label class="col-md-3 control-label"><?php
                echo TEXT_EXT_NUMBER_OF_COPIES ?></label>
            <div class="col-md-9"><?php
                echo input_tag(
                    'number_of_copies',
                    1,
                    ['class' => 'form-control input-small', 'type' => 'number', 'max' => 100, 'min' => 1]
                ) ?></div>
        </div>

    </div>
</div>
<?php
echo ajax_modal_template_footer(TEXT_BUTTON_COPY) ?>


</form>

<script>
    $(function () {
        $('#form-copy-to').submit(function () {

            $('button[type=submit]', this).css('display', 'none')
            $('#modal-body-content').css('visibility', 'hidden').css('height', '1px');
            $('#modal-body-content').after('<div class="ajax-loading"></div>');

            $('#modal-body-content').load($(this).attr('action'), $(this).serializeArray(), function () {
                $('.ajax-loading').css('display', 'none');
                $('#modal-body-content').css('visibility', 'visible').css('height', 'auto');
            })

            return false;
        })
    })
</script>