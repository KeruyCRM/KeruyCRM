<div id="mind_map_options" data-url="<?php
echo url_for('ext/mind_map_reports/view_map', 'id=' . $reports['id'] . '&path=' . $app_path . '&action=save') ?>"
     data-is-editable="<?php
     echo $mind_map->is_editable() ?>"></div>

<?php
echo input_hidden_tag('data-item-url', url_for('items/info'));
echo input_hidden_tag('data-item-path', '&path=' . $app_path);
echo input_hidden_tag(
    'data-item-prepare-new',
    url_for('ext/mind_map_reports/view_map', 'id=' . $reports['id'] . '&path=' . $app_path . '&action=prepare_new_item')
);
echo input_hidden_tag('data-item-shape', $reports['shape']);
?>

<script>
    var mind_map_json = '<?php echo $mind_map->get_json() ?>';
</script>