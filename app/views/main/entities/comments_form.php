<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->TEXT_NAV_COMMENTS_FORM_CONFIG ?></h3>

<p><?= \K::$fw->TEXT_COMMENTS_FORM_CFG_INFO ?></p>

<table style="width: 100%; max-width: 960px;">
    <tr>
        <td valign="top" width="50%">
            <fieldset>
                <legend><?= \K::$fw->TEXT_FIELDS_IN_COMMENTS_FORM ?></legend>
                <div class="cfg_listing">
                    <ul id="fields_in_comments" class="sortable">
                        <?php
                        //while ($v = db_fetch_array($fields_query)) {
                        foreach (\K::$fw->fields_query as $v) {
                            $v = $v->cast();

                            echo '<li id="fields_' . $v['id'] . '"><div>' . \Models\Main\Fields_types::get_option(
                                    $v['type'],
                                    'name',
                                    $v['name']
                                ) . '</div></li>';
                        }
                        ?>
                    </ul>
                </div>
            </fieldset>
            <?= \Helpers\Html::button_tag(
                \K::$fw->TEXT_BUTTON_ADD_FORM_TAB,
                \Helpers\Urls::url_for(
                    'main/entities/comments_forms_tabs_form',
                    'entities_id=' . \K::$fw->GET['entities_id']
                )
            ) ?>
            <div class="forms_tabs" style="max-width: 960px;">
                <ol id="forms_tabs_ol" class="sortable_tabs sortable">
                    <?php
                    //while ($tabs = db_fetch_array(\K::$fw->tabs_query))
                    foreach (\K::$fw->tabs_query as $tabs):
                        $tabs = $tabs->cast();
                        ?>
                        <li id="forms_tabs_<?= $tabs['id'] ?>">
                            <div>
                                <div class="cfg_form_tab">
                                    <div class="cfg_form_tab_heading">
                                        <table width="100%">
                                            <tr>
                                                <td>
                                                    <b><?= $tabs['name'] ?></b>
                                                </td>
                                                <td class="align-right">
                                                    <?= \Helpers\Html::button_icon_edit(
                                                        \Helpers\Urls::url_for(
                                                            'main/entities/comments_forms_tabs_form',
                                                            'id=' . $tabs['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                                                        )
                                                    ) . ' ' . \Helpers\Html::button_icon_delete(
                                                        \Helpers\Urls::url_for(
                                                            'main/entities/comments_forms_tabs_delete',
                                                            'id=' . $tabs['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                                                        )
                                                    ); ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="cfg_forms_fields">
                                        <?php
                                        echo '
  <ul id="forms_tabs_' . $tabs['id'] . '" class="sortable">
';
                                        $fields_query = \K::model()->db_query_exec(
                                            "select f.*, t.name as tab_name from app_fields f, app_comments_forms_tabs t where f.type not in (" . \Models\Main\Fields_types::get_reserved_types_list(
                                            ) . ") and  f.entities_id = ? and f.comments_forms_tabs_id = t.id and f.comments_forms_tabs_id = ? order by t.sort_order, t.name, f.sort_order, f.name",
                                            [
                                                \K::$fw->GET['entities_id'],
                                                $tabs['id']
                                            ],
                                            'app_fields,app_comments_forms_tabs'
                                        );

                                        //while ($v = db_fetch_array($fields_query)) {
                                        foreach ($fields_query as $v) {
                                            echo '
    <li id="fields_' . $v['id'] . '">
      <div>
        <table width="100%">
          <tr>
            <td>' . \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</td>            
          </tr>
        </table>
      </div>
    </li>';
                                        }
                                        echo '
  </ul>
';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php
                    endforeach; ?>
                </ol>
            </div>
        </td>
        <td style="padding-left: 25px;" valign="top">
            <fieldset>
                <legend><?= \K::$fw->TEXT_AVAILABLE_FIELDS ?></legend>
                <div class="cfg_listing">
                    <ul id="available_fields" class="sortable">
                        <?php
                        //while ($v = db_fetch_array($fields_query)) {
                        foreach (\K::$fw->fields_query2 as $v) {
                            echo '<li id="fields_' . $v['id'] . '"><div>' . \Models\Main\Fields_types::get_option(
                                    $v['type'],
                                    'name',
                                    $v['name']
                                ) . '</div></li>';
                        }
                        ?>
                    </ul>
                </div>
            </fieldset>
        </td>
    </tr>
</table>

<script>
    $(function () {
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
                        'main/entities/comments_form/set_fields',
                        'entities_id=' . \K::$fw->GET['entities_id']
                    )?>',
                    data: data
                });
            }
        });

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
                    url: '<?= \Helpers\Urls::url_for('main/entities/comments_form/sort_tabs')?>',
                    data: data
                });
            }
        });
    });
</script>