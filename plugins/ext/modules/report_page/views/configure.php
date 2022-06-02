<ul class="page-breadcrumb breadcrumb">
    <li><?php
        echo link_to(TEXT_EXT_REPORT_DESIGNER, url_for('ext/report_page/reports')) ?><i class="fa fa-angle-right"></i>
    </li>
    <?php
    if (isset($app_entities_cache[$report_info['entities_id']])) {
        echo '<li>' . $app_entities_cache[$report_info['entities_id']]['name'] . '<i class="fa fa-angle-right"></i></li>';
    }
    ?>
    <li><?php
        echo $report_info['name'] ?></li>
</ul>


<?php
$use_editor = $report_info['use_editor'];

$blocks_dropdown = new report_page\blocks_dropdown($report_info['id']);
echo $blocks_dropdown->render();

?>

<?php
echo form_tag(
    'report_form',
    url_for('ext/report_page/configure', 'action=save&id=' . $_GET['id']),
    ['class' => 'form-horizontal']
) ?>

<div class="row">
    <div class="col-md-12">
        <?php
        echo textarea_tag(
            'description',
            $report_info['description'],
            ['class' => ($use_editor != 1 ? 'code_mirror' : ''), 'mode' => 'xml', 'size' => 600]
        ) ?>

        <br>

        <?php
        echo submit_tag(TEXT_BUTTON_SAVE) . ' ' . button_tag(
                TEXT_BUTTON_CANCEL,
                url_for('ext/report_page/reports'),
                false,
                ['class' => 'btn btn-default']
            ) ?>

        <br>

    </div>
</div>

</form>

<?php
echo app_include_codemirror(['javascript', 'sql', 'php', 'clike', 'css', 'xml']) ?>

<script>
    $(function () {
        use_eidtor = <?php echo $use_editor ?>

        if (use_eidtor == 1) {
            use_editor_full('description', true)

            $('.insert_block_to_description').click(function () {
                html = $(this).attr('data_insert').trim();
                CKEDITOR.instances.description.insertText(html);
            })
        } else {
            appHandleCodeMirror()

            $('.insert_block_to_description').click(function () {
                html = $(this).attr('data_insert').trim();
                insert_to_code_mirror('description', html)
            })
        }
    })
</script>


