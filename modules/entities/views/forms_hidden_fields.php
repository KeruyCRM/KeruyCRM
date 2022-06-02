<?php
echo ajax_modal_template_header(TEXT_HIDDEN_FIELDS_IN_FORM) ?>

<?php
$entities_id = _GET('entities_id');
$cfg = new entities_cfg($_GET['entities_id']);
?>

<?php
echo form_tag(
    'fields_form',
    url_for('entities/forms_hidden_fields', 'action=save&entities_id=' . $_GET['entities_id']),
    ['class' => 'form-horizontal', 'enctype' => 'multipart/form-data']
) ?>
<div class="modal-body ajax-modal-width-790">
    <div class="form-body">


        <p><?php
            echo TEXT_HIDDEN_FIELDS_IN_FORM_TIP ?></p>

        <?php
        $chocies = [];

        $where_sql = " and f.type not in (" . fields_types::get_type_list_excluded_in_form() . ")";
        $fields_query = fields::get_query($entities_id, $where_sql);
        while ($fields = db_fetch($fields_query)) {
            $chocies[$fields->id] = $fields->name;
        }
        ?>
        <div class="form-group">
            <div class="col-md-12">
                <?php
                echo select_tag(
                    'hidden_form_fields[]',
                    $chocies,
                    $cfg->get('hidden_form_fields'),
                    ['class' => 'form-control chosen-select', 'multiple' => 'multiple']
                ) ?>
            </div>
        </div>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form> 