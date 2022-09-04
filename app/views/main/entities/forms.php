<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->TEXT_NAV_FORM_CONFIG ?></h3>

<p><?= \K::$fw->TEXT_FORM_CONFIG_INFO ?></p>

<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_ADD_FORM_TAB,
    \Helpers\Urls::url_for('main/entities/forms_tabs_form', 'entities_id=' . \K::$fw->GET['entities_id'])
) . '&nbsp;' ?>

<div class="btn-group">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><?= \K::$fw->TEXT_SETTINGS ?><i
                class="fa fa-angle-down"></i></button>
    <ul class="dropdown-menu" role="menu">
        <li>
            <?= \Helpers\Urls::link_to_modalbox(
                \K::$fw->TEXT_FORM_WIZARD,
                \Helpers\Urls::url_for('main/entities/forms_wizard', 'entities_id=' . \K::$fw->GET['entities_id'])
            ) ?>
        </li>
        <li>
            <?= \Helpers\Urls::link_to(
                \K::$fw->TEXT_TAB_GROUPS,
                \Helpers\Urls::url_for('main/forms_tabs/groups', 'entities_id=' . \K::$fw->GET['entities_id'])
            ) ?>
        </li>
    </ul>
</div>

<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_ADD_JAVASCRIPT,
    \Helpers\Urls::url_for('main/entities/forms_custom_js', 'entities_id=' . \K::$fw->GET['entities_id']),
    true,
    ['class' => 'btn btn-default']
) . '&nbsp;' ?>
<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_HIDDEN_FIELDS . " (" . \K::$fw->count_hidden_form_fields . ")",
    \Helpers\Urls::url_for('main/entities/forms_hidden_fields', 'entities_id=' . \K::$fw->GET['entities_id']),
    true,
    ['class' => 'btn btn-default']
) ?>

<div class="forms_tabs">
    <ol id="forms_tabs_ol" class="sortable_tabs sortable">
        <?php
        foreach (\K::$fw->tabs_tree as $tabs) {
            if ($tabs['is_folder']) {
                continue;
            }

            $tab_is_reserved = \Models\Main\Forms_tabs::is_reserved($tabs['id']);

            ?>
            <li id="forms_tabs_<?php
            echo $tabs['id'] ?>" style="cursor:default; margin-bottom: 15px;">
                <div>
                    <div class="cfg_form_tab">
                        <?php
                        if (\K::$fw->count_tabs > 0): ?>
                            <div class="cfg_form_tab_heading" style="cursor:move">
                                <table width="100%">
                                    <tr>
                                        <td>
                                            <h4><?= (strlen(
                                                    $tabs['parent_name']
                                                ) ? $tabs['parent_name'] . ': ' : '') . $tabs['name'] ?></h4>
                                            <?php
                                            if ($tab_is_reserved) {
                                                echo \Helpers\App::tooltip_text(\K::$fw->TEXT_RESERVED_FORM_TAB);
                                            }
                                            ?>
                                        </td>
                                        <td class="align-right">
                                            <?php
                                            echo \Helpers\Html::button_icon_edit(
                                                \Helpers\Urls::url_for(
                                                    'main/entities/forms_tabs_form',
                                                    'id=' . $tabs['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                                                )
                                            );

                                            if (!$tab_is_reserved) {
                                                echo ' ' . \Helpers\Html::button_icon_delete(
                                                        \Helpers\Urls::url_for(
                                                            'main/entities/forms_tabs_delete',
                                                            'id=' . $tabs['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                                                        )
                                                    );
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        <?php
                        endif ?>
                        <div class="cfg_forms_fields">
                            <?php
                            echo '
  <ul id="forms_tabs_' . $tabs['id'] . '" class="sortable" style="max-width: 950px;">
';
                            $fields_query = \K::model()->db_query_exec(
                                "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_reserved_types_list(
                                ) . ") and f.entities_id = ? and f.forms_tabs_id = t.id and f.forms_tabs_id = ? and length(forms_rows_position) = 0 order by t.sort_order, t.name, f.sort_order, f.name",
                                [
                                    \K::$fw->GET['entities_id'],
                                    $tabs['id']
                                ],
                                'app_fields,app_forms_tabs'
                            );

                            //while ($v = db_fetch_array($fields_query)) {
                            foreach ($fields_query as $v) {
                                echo '
    <li id="form_fields_' . $v['id'] . '" class="' . $v['type'] . '">
      <div>
        <table width="100%">
          <tr>
            <td>' . \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</td>
            <td class="align-right">' . (!in_array(
                                        $v['type'],
                                        \Models\Main\Fields_types::get_users_types()
                                    ) ? \Helpers\Html::button_icon_edit(
                                            \Helpers\Urls::url_for(
                                                'main/entities/fields_form',
                                                'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&redirect_to=forms'
                                            )
                                        ) . ' ' . \Helpers\Html::button_icon_delete(
                                            \Helpers\Urls::url_for(
                                                'main/entities/fields_delete',
                                                'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&redirect_to=forms'
                                            )
                                        ) : '') . '</td>
          </tr>
        </table>
      </div>
    </li>';
                            }
                            echo '
  </ul>
';
                            //handle rows
                            $html = '<ol class="sortable sortable_rows" id="forms_rows_' . $tabs['id'] . '">';
                            /*$rows_query = db_query(
                                "select * from app_forms_rows where entities_id='" . _GET(
                                    'entities_id'
                                ) . "' and forms_tabs_id='" . $tabs['id'] . "' order by sort_order",
                                false
                            );*/

                            $rows_query = \K::model()->db_fetch('app_forms_rows', [
                                'entities_id = ? and forms_tabs_id = ?',
                                \K::$fw->GET['entities_id'],
                                $tabs['id']
                            ], ['order' => 'sort_order']);

                            if (count($rows_query)) {
                                //while ($rows = db_fetch_array($rows_query)) {
                                foreach ($rows_query as $rows) {
                                    $rows = $rows->cast();

                                    $html_row = '<div class="row">';

                                    for ($i = 1; $i <= $rows['columns']; $i++) {
                                        $html_row .= '
                    <div class="col-md-' . $rows['column' . $i . '_width'] . '">
                      <ul class="sortable" id="forms_rows_' . $tabs['id'] . '_' . $rows['id'] . '_' . $i . '">';

                                        $fields_query = \K::model()->db_query_exec(
                                            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_reserved_types_list(
                                            ) . ") and  f.entities_id = ? and f.forms_tabs_id = t.id and f.forms_tabs_id = ? and forms_rows_position = ? order by t.sort_order, t.name, f.sort_order, f.name",
                                            [
                                                \K::$fw->GET['entities_id'],
                                                $tabs['id'],
                                                $rows['id'] . ":" . $i
                                            ],
                                            'app_fields,app_forms_tabs'
                                        );

                                        //while ($v = db_fetch_array($fields_query)) {
                                        foreach ($fields_query as $v) {
                                            $html_row .= '
                <li id="form_fields_' . $v['id'] . '" class="' . $v['type'] . '">
                  <div>
                    <table width="100%">
                      <tr>
                        <td>' . \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</td>
                        <td class="align-right">' . (!in_array(
                                                    $v['type'],
                                                    \Models\Main\Fields_types::get_users_types()
                                                ) ? \Helpers\Html::button_icon_edit(
                                                        \Helpers\Urls::url_for(
                                                            'main/entities/fields_form',
                                                            'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&redirect_to=forms'
                                                        )
                                                    ) . ' ' . \Helpers\Html::button_icon_delete(
                                                        \Helpers\Urls::url_for(
                                                            'main/entities/fields_delete',
                                                            'id=' . $v['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&redirect_to=forms'
                                                        )
                                                    ) : '') . '</td>
                      </tr>
                    </table>
                  </div>
                </li>';
                                        }

                                        $html_row .= '
                      </ul>
                    </div>';
                                    }

                                    $html_row .= '</div>';

                                    $html .= '
                <li id="forms_rows_' . $rows['id'] . '" class="sortable_rows_li" style="cursor:default">
                    <table>
                        <tr>
                            <td class="sortable_rows_handler" style="cursor:move;  border: 2px dotted gray;"></td>
                            <td width="100%" style="padding-left: 15px; padding-right: 15px;">' . $html_row . '</td>                        
                          <td>
                            <div>' . \Helpers\Html::button_icon_edit(
                                            \Helpers\Urls::url_for(
                                                'main/entities/forms_rows',
                                                'id=' . $rows['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&forms_tabs_id=' . $tabs['id']
                                            )
                                        ) . '</div>
                            <div style="padding-top: 3px;">' . \Helpers\Html::button_icon_delete(
                                            \Helpers\Urls::url_for(
                                                'main/entities/forms_rows_delete',
                                                'id=' . $rows['id'] . '&entities_id=' . \K::$fw->GET['entities_id'] . '&redirect_to=forms'
                                            )
                                        ) . '</div></td>
                        </tr>    
                    </table>
                </li>
                ';
                                }
                            }

                            $html .= '</ol>';

                            echo $html;

                            ?>
                        </div>
                        <div>
                            <?= \Helpers\Html::button_tag(
                                '<i class="fa fa-plus"></i> ' . \K::$fw->TEXT_BUTTON_ADD_NEW_FIELD,
                                \Helpers\Urls::url_for(
                                    'main/entities/fields_form',
                                    'entities_id=' . \K::$fw->GET['entities_id'] . '&forms_tabs_id=' . $tabs['id'] . '&redirect_to=forms'
                                ),
                                true,
                                ['class' => 'btn btn-default']
                            ) ?>
                            <?= \Helpers\Html::button_tag(
                                \K::$fw->TEXT_ADD_ROW,
                                \Helpers\Urls::url_for(
                                    'main/entities/forms_rows',
                                    'entities_id=' . \K::$fw->GET['entities_id'] . '&forms_tabs_id=' . $tabs['id']
                                ),
                                true,
                                ['class' => 'btn btn-default']
                            ) ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php
        }
        ?>
    </ol>
</div>

<script>
    $(function () {
        //sortable fields
        $("ul.sortable").sortable({
            connectWith: "ul",
            update: function (event, ui) {
                data = '';
                $("ul.sortable").each(function () {
                    data = data + '&' + $(this).attr('id') + '=' + $(this).sortable("toArray")
                });

                data = data.slice(1)
                $.ajax({
                    type: "POST",
                    url: '<?= \Helpers\Urls::url_for(
                        'main/entities/forms/sort_fields',
                        'entities_id=' . \K::$fw->GET['entities_id']
                    )?>',
                    data: data
                });
            }
        });

        //sortable tabs
        $("ol.sortable_tabs").sortable({
            handle: '.cfg_form_tab_heading',
            update: function (event, ui) {

                data = '';
                $("ol.sortable_tabs").each(function () {
                    data = data + '&' + $(this).attr('id') + '=' + $(this).sortable("toArray")
                });
                data = data.slice(1)
                $.ajax({
                    type: "POST",
                    url: '<?= \Helpers\Urls::url_for('main/entities/forms/sort_tabs')?>',
                    data: data
                });
            }
        });

        forms_rows_sortable();
    });

    function forms_rows_sortable() {
        $("ol.sortable_rows").sortable({
            connectWith: "ol.sortable_rows",
            handle: '.sortable_rows_handler',
            update: function (event, ui) {
                data = $(this).attr('id') + '=' + $(this).sortable("toArray");

                $.ajax({
                    type: "POST",
                    url: '<?= \Helpers\Urls::url_for(
                        'main/entities/forms_rows/sort_rows',
                        'entities_id=' . \K::$fw->GET['entities_id']
                    )?>',
                    data: data
                });
            }
        });
    }
</script>