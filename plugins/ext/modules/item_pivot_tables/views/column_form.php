<?php
echo ajax_modal_template_header(TEXT_COLUMNS) ?>

<?php
echo form_tag(
    'reports_form',
    url_for(
        'ext/item_pivot_tables/calc',
        'action=save&reports_id=' . _get::int('reports_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<?php
echo input_hidden_tag('type', 'column') ?>
<div class="modal-body">
    <div class="form-body">


        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_NAME ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_FORMULA ?></label>
            <div class="col-md-9">
                <?php
                echo textarea_tag('formula', $obj['formula'], ['class' => 'form-control required']) ?>
                <?php
                echo tooltip_text(TEXT_EXT_ITEM_PIVOT_TABLES_FORMULA_TIP) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_SORT_ORDER ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('sort_order', $obj['sort_order'], ['class' => 'form-control input-small']) ?>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#reports_form').validate();
    });
</script>  

 