<?php

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_PROCESSES,
        url_for('ext/processes/processes')
    ) . '<i class="fa fa-angle-right"></i></li>';

$breadcrumb[] = '<li>' . link_to(
        $app_process_info['name'],
        url_for('ext/processes/actions', 'process_id=' . $app_process_info['id'])
    ) . '<i class="fa fa-angle-right"></i></li>';
$breadcrumb[] = '<li>' . TEXT_NAV_FORM_CONFIG . '</li>';

$process_form = new process_form($app_process_info['id']);

//print_rr($process_form->process_fields);
//print_rr($process_form->process_fields_in_tabs);
?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>

<p><?php
    echo TEXT_EXT_PROCESS_FORM_CFG_INFO ?></p>

<?php
echo button_tag(
    TEXT_BUTTON_ADD_FORM_TAB,
    url_for('ext/processes/process_form_tab', 'process_id=' . $app_process_info['id'])
) ?>


<div class="forms_tabs">
    <ol id="forms_tabs_ol" class="sortable_tabs sortable">

        <li id="forms_tabs_0" style="cursor:default; margin-bottom: 15px;">
            <div class="cfg_form_tab">
                <?php

                $html = '
  <h4>' . TEXT_FIELDS_IN_FORM . '</h4>  
  <ul id="forms_tabs_0" class="sortable" style="max-width: 950px;">
';
                foreach ($process_form->process_fields as $v) {
                    if ($process_form->is_field_in_tab($v['id'])) {
                        continue;
                    }

                    $html .= '
    <li id="form_fields_' . $v['id'] . '" class="' . $v['type'] . '">
      <div>
        <table width="100%">
          <tr>
            <td>' . entities::get_name_by_id($v['entities_id']) . ': ' . fields_types::get_option(
                            $v['type'],
                            'name',
                            $v['name']
                        ) . '</td>            
          </tr>
        </table>
      </div>
    </li>';
                }
                $html .= '
  </ul>
';

                echo $html;
                ?>
            </div>
        </li>

        <?php

        $tabs_query = db_fetch_all(
            'app_ext_process_form_tabs',
            "process_id='" . $app_process_info['id'] . "' order by  sort_order, name"
        );
        while ($tabs = db_fetch_array($tabs_query)) {
            ?>
            <li id="forms_tabs_<?php
            echo $tabs['id'] ?>" style="cursor:default; margin-bottom: 15px;">
                <div>
                    <div class="cfg_form_tab">

                        <div class="cfg_form_tab_heading" style="cursor:move">
                            <table width="100%">
                                <tr>
                                    <td>
                                        <h4><?php
                                            echo $tabs['name'] ?></h4>
                                    </td>
                                    <td class="align-right">
                                        <?php
                                        echo button_icon_edit(
                                            url_for(
                                                'ext/processes/process_form_tab',
                                                'id=' . $tabs['id'] . '&process_id=' . $app_process_info['id']
                                            )
                                        );
                                        echo ' ' . button_icon_delete(
                                                url_for(
                                                    'ext/processes/process_form',
                                                    'action=delete_tab&id=' . $tabs['id'] . '&process_id=' . $app_process_info['id']
                                                ),
                                                false,
                                                ['confirm' => TEXT_ARE_YOU_SURE]
                                            );

                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="cfg_forms_fields">
                            <?php
                            echo '
  <ul id="forms_tabs_' . $tabs['id'] . '" class="sortable" style="max-width: 950px;">
';
                            if (strlen($tabs['fields'])) {
                                $fields_query = db_query(
                                    "select * from app_fields where id in ({$tabs['fields']}) order by field(id,{$tabs['fields']})"
                                );
                                while ($v = db_fetch_array($fields_query)) {
                                    if (!$process_form->is_field_in_form($v['id'])) {
                                        continue;
                                    }

                                    echo '
        <li id="form_fields_' . $v['id'] . '" class="' . $v['type'] . '">
          <div>
            <table width="100%">
              <tr>
                <td>' . entities::get_name_by_id($v['entities_id']) . ': ' . fields_types::get_option(
                                            $v['type'],
                                            'name',
                                            $v['name']
                                        ) . '</td>            
              </tr>
            </table>
          </div>
        </li>';
                                }
                            }
                            echo '
  </ul>
';


                            //handle rows
                            $html = '<ol class="sortable sortable_rows" id="forms_rows_' . $tabs['id'] . '">';
                            $rows_query = db_query(
                                "select * from app_ext_process_form_rows where process_id='" . _GET(
                                    'process_id'
                                ) . "' and forms_tabs_id='" . $tabs['id'] . "' order by sort_order",
                                false
                            );
                            if (db_num_rows($rows_query)) {
                                while ($rows = db_fetch_array($rows_query)) {
                                    $html_row = '<div class="row">';

                                    for ($i = 1; $i <= $rows['columns']; $i++) {
                                        $html_row .= '
                    <div class="col-md-' . $rows['column' . $i . '_width'] . '">
                      <ul class="sortable" id="forms_rows_' . $tabs['id'] . '_' . $rows['id'] . '_' . $i . '">';


                                        if (strlen($rows['column' . $i . '_fields'])) {
                                            $fields_query = db_query(
                                                "select * from app_fields where id in ({$rows['column' . $i . '_fields']}) order by field(id,{$rows['column' . $i . '_fields']})"
                                            );
                                            while ($v = db_fetch_array($fields_query)) {
                                                if (!$process_form->is_field_in_form($v['id'])) {
                                                    continue;
                                                }

                                                $html_row .= '
                    <li id="form_fields_' . $v['id'] . '" class="' . $v['type'] . '">
                      <div>
                        <table width="100%">
                          <tr>
                            <td>' . entities::get_name_by_id($v['entities_id']) . ': ' . fields_types::get_option(
                                                        $v['type'],
                                                        'name',
                                                        $v['name']
                                                    ) . '</td>                        
                          </tr>
                        </table>
                      </div>
                    </li>';
                                            }
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
                            <div>' . button_icon_edit(
                                            url_for(
                                                'ext/processes/process_form_row',
                                                'id=' . $rows['id'] . '&process_id=' . $_GET['process_id'] . '&forms_tabs_id=' . $tabs['id']
                                            )
                                        ) . '</div>
                            <div style="padding-top: 3px;">' . button_icon_delete(
                                            url_for(
                                                'ext/processes/process_form',
                                                'action=delete_row&id=' . $rows['id'] . '&process_id=' . $_GET['process_id']
                                            ),
                                            false,
                                            ['confirm' => TEXT_ARE_YOU_SURE]
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
                            <?php
                            echo button_tag(
                                TEXT_ADD_ROW,
                                url_for(
                                    'ext/processes/process_form_row',
                                    'process_id=' . $_GET['process_id'] . '&forms_tabs_id=' . $tabs['id']
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

<?php
echo '<a href="' . url_for('ext/processes/processes') . '" class="btn btn-default">' . TEXT_BUTTON_BACK . '</a>' ?>


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
                    url: '<?php echo url_for(
                        "ext/processes/process_form",
                        "action=sort_fields&process_id=" . _GET('process_id')
                    ) ?>',
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
                    url: '<?php echo url_for(
                        "ext/processes/process_form",
                        "action=sort_tabs&process_id=" . _GET('process_id')
                    ) ?>',
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
                    url: '<?php echo url_for(
                        "ext/processes/process_form",
                        "action=sort_rows&process_id=" . _GET('process_id')
                    ) ?>',
                    data: data
                });
            }
        });
    }
</script> 
