<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php
\K::$fw->listing_sql_query = '';

if (strlen(\K::$fw->POST['search_keywords']) > 0) {
    echo '<div class="alert alert-info">' . sprintf(
            \K::$fw->TEXT_SEARCH_RESULT_FOR,
            htmlspecialchars(\K::$fw->POST['search_keywords'])
        ) . ' <span onClick="reset_search()" class="reset_search">' . \K::$fw->TEXT_RESET_SEARCH . '</span></div>';

    //require(component_path('items/add_search_comments_query'));
    echo \K::view()->render(\Helpers\Urls::components_path('main/items/add_search_comments_query'));
}
?>

    <div class="table-scrollable">
    <table class="table table-striped table-bordered table-hover">
    <thead>
    <tr>
        <?php
        if (\K::$fw->user_has_comments_access) echo '<th>' . '</th>' ?>
        <?php
        if (\K::$fw->entity_cfg->get('display_comments_id') == 1) echo '<th>' . \K::$fw->TEXT_ID . '</th>' ?>
        <th width="100%"><?= \K::$fw->TEXT_COMMENTS ?></th>
        <th><?= \K::$fw->TEXT_DATE_ADDED ?></th>
    </tr>
    </thead>
    <tbody>
<?php

$fields_access_schema = \Models\Main\Users\Users::get_fields_access_schema(
    \K::$fw->current_entity_id,
    \K::$fw->app_user['group_id']
);
$choices_cache = \Models\Main\Fields_choices::get_cache();

$html = '';
/*$listing_sql = "select * from app_comments where entities_id='" . db_input(
        \K::$fw->current_entity_id
    ) . "' and items_id='" . db_input(\K::$fw->current_item_id) . "' " . \K::$fw->listing_sql_query . " order by id desc";*/

$listing_sql = \Tools\Split_page::makeQuery('app_comments', [
    'entities_id = ? and items_id = ? ' . \K::$fw->listing_sql_query,
    \K::$fw->current_entity_id,
    \K::$fw->current_item_id
], ['order' => 'id desc']);

$listing_split = new \Tools\Split_page($listing_sql, 'items_comments_listing');
$listing_split->listing_function = 'load_comments_listing';
$items_query = \K::model()->db_fetch_split(
    $listing_split->sql_query()
);

//$items_query = db_query($listing_split->sql_query);
//while ($item = db_fetch_array($items_query)) {
foreach ($items_query as $item) {
    $item = $item->cast();

    $html_action_column = '';
    if (\K::$fw->user_has_comments_access) {
        $html_action_column = '
      <td class="nowrap">
      ' . ((\Models\Main\Users\Users::has_comments_access(
                    'delete',
                    \K::$fw->access_rules->get_comments_access_schema()
                ) and ($item['created_by'] == \K::$fw->app_user['id'] or \K::$fw->app_user['group_id'] == 0 or \Models\Main\Users\Users::has_comments_access(
                        'full',
                        \K::$fw->access_rules->get_comments_access_schema()
                    ))) ? \Helpers\Html::button_icon_delete(
                    \Helpers\Urls::url_for(
                        'main/items/comments_delete',
                        'id=' . $item['id'] . '&path=' . \K::$fw->POST['path']
                    )
                ) . '<br>' : '') . '
      ' . ((\Models\Main\Users\Users::has_comments_access(
                    'update',
                    \K::$fw->access_rules->get_comments_access_schema()
                ) and ($item['created_by'] == \K::$fw->app_user['id'] or \K::$fw->app_user['group_id'] == 0 or \Models\Main\Users\Users::has_comments_access(
                        'full',
                        \K::$fw->access_rules->get_comments_access_schema()
                    ))) ? \Helpers\Html::button_icon_edit(
                    \Helpers\Urls::url_for(
                        'main/items/comments_form',
                        'id=' . $item['id'] . '&path=' . \K::$fw->POST['path']
                    )
                ) . '<br>' : '') . '
			' . (\Models\Main\Users\Users::has_comments_access(
                'create',
                \K::$fw->access_rules->get_comments_access_schema()
            ) ? \Helpers\Html::button_icon(
                \K::$fw->TEXT_REPLY,
                'fa fa-reply',
                \Helpers\Urls::url_for(
                    'main/items/comments_form',
                    'reply_to=' . $item['id'] . '&path=' . \K::$fw->POST['path']
                )
            ) : '') . '
      </td>
    ';
    }

    $html_fields = '';
    $comments_fields_query = \K::model()->db_query_exec(
        "select f.*, ch.fields_value from app_comments_history ch, app_fields f where comments_id = ? and f.id = ch.fields_id order by ch.id",
        $item['id'],
        'app_comments_history,app_fields'
    );

    //while ($field = db_fetch_array($comments_fields_query)) {
    foreach ($comments_fields_query as $field) {
        //check field access
        if (isset($fields_access_schema[$field['id']])) {
            if ($fields_access_schema[$field['id']] == 'hide') {
                continue;
            }
        }

        $output_options = [
            'class' => $field['type'],
            'value' => $field['fields_value'],
            'field' => $field,
            'path' => \K::$fw->POST['path'],
            'is_listing' => true,
            'is_comments_listing' => true,
        ];

        $html_fields .= '                      
        <tr><th>&bull;&nbsp;' . \Models\Main\Fields_types::get_option(
                $field['type'],
                'name',
                $field['name']
            ) . ':&nbsp;</th><td>' . \Models\Main\Fields_types::output($output_options) . '</td></tr>           
    ';
    }

    if (strlen($html_fields) > 0) {
        $html_fields = '<table class="comments-history">' . $html_fields . '</table>';
    }

    $output_options = [
        'class' => 'fieldtype_attachments',
        'value' => $item['attachments'],
        'path' => \K::$fw->POST['path'],
        'field' => [
            'entities_id' => \K::$fw->current_entity_id,
            'configuration' => json_encode(
                ['use_image_preview' => \K::$fw->entity_cfg->get('image_preview_in_comments', 0)]
            )
        ],
        'item' => ['id' => \K::$fw->current_item_id]
    ];

    $attachments = \Models\Main\Fields_types::output($output_options);

    if (\K::$fw->entity_cfg->get('use_editor_in_comments') != 1) {
        $item['description'] = nl2br($item['description']);
    }

    $photo = '';
    if (\K::$fw->entity_cfg->get('disable_avatar_in_comments', 0) != 1 and $item['created_by']) {
        $photo = \Helpers\App::render_user_photo(\K::$fw->app_users_cache[$item['created_by']]['photo']);
    }

    $html .= '
    <tr>
      ' . $html_action_column . ' 
      ' . (\K::$fw->entity_cfg->get('display_comments_id') == 1 ? '<td>' . $item['id'] . '</td>' : '') . '   
      <td style="white-space: normal;">
        			<div class="ckeditor-images-content-prepare"><div class="fieldtype_textarea_wysiwyg">' . \Helpers\Urls::auto_link_text(
            $item['description']
        ) . '</div></div>' .
        $attachments .
        $html_fields .
        '</td>
      <td class="nowrap">' .
        \Helpers\App::format_date_time($item['date_added']) .
        ($item['created_by'] > 0 ? '<br><span ' . \Models\Main\Users\Users::render_public_profile(
                \K::$fw->app_users_cache[$item['created_by']],
                true
            ) . '>' . \K::$fw->app_users_cache[$item['created_by']]['name'] . '</span><br>' . $photo : '') . '</td>
    </tr>
  ';
}

if ($listing_split->number_of_rows() == 0) {
    $html .= '
    <tr>
      <td colspan="4">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td>
    </tr>
  ';
}

$html .= '
  </tbody>
</table>
</div>
';

//add pager
$html .= '
  <table width="100%">
    <tr>
      <td>' . $listing_split->display_count() . '</td>
      <td align="right">' . $listing_split->display_links() . '</td>
    </tr>
  </table>
';

echo $html;