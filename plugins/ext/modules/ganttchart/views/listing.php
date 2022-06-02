<?php

$breadcrumb = [];

$breadcrumb[] = '<li>' . link_to(
        TEXT_EXT_GANTTCHART_REPORT,
        url_for('ext/ganttchart/configuration')
    ) . '<i class="fa fa-angle-right"></i></li>';
$breadcrumb[] = '<li>' . $reports['name'] . '<i class="fa fa-angle-right"></i></li>';
$breadcrumb[] = '<li>' . TEXT_EXT_CHART_CONFIGURE_LISTING . '</li>';


$exclude_fiedls_types = "'fieldtype_action','fieldtype_related_records','fieldtype_section','fieldtype_mapbbcode','fieldtype_qrcode','fieldtype_barcode','fieldtype_image','fieldtype_image_ajax','fieldtype_attachments','fieldtype_textarea','fieldtype_textarea_wysiwyg','fieldtype_input_file'";
?>

<ul class="page-breadcrumb breadcrumb">
    <?php
    echo implode('', $breadcrumb) ?>
</ul>

<h3 class="page-title"><?php
    echo TEXT_EXT_CHART_CONFIGURE_LISTING ?></h3>

<div><?php
    echo TEXT_EXT_CHART_CONFIGURE_LISTING_IFNO ?></div>

<table style="width: 100%; max-width: 960px;">
    <tr>
        <td valign="top" width="50%">
            <fieldset>
                <legend><?php
                    echo TEXT_FIELDS_IN_LISTING ?></legend>
                <div class="cfg_listing">
                    <ul id="fields_in_listing" class="sortable">
                        <?php
                        $fields_query = db_query(
                            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where  f.entities_id='" . db_input(
                                $reports['entities_id']
                            ) . "' and f.id not in(" . $reports['start_date'] . "," . $reports['end_date'] . ") and f.type not in ({$exclude_fiedls_types}) and f.forms_tabs_id=t.id order by f.listing_sort_order"
                        );
                        while ($v = db_fetch_array($fields_query)) {
                            if (in_array($v['id'], explode(',', $reports['fields_in_listing']))) {
                                echo '<li id="form_fields_' . $v['id'] . '"><div>' . fields_types::get_option(
                                        $v['type'],
                                        'name',
                                        $v['name']
                                    ) . '</div></li>';
                            }
                        }
                        ?>
                    </ul>
                </div>

            </fieldset>

        </td>
        <td style="padding-left: 25px;" valign="top">

            <fieldset>
                <legend><?php
                    echo TEXT_FIELDS_EXCLUDED_FROM_LISTING ?></legend>
                <div class="cfg_listing">
                    <ul id="fields_excluded_from_listing" class="sortable">
                        <?php
                        $fields_query = db_query(
                            "select f.*, t.name as tab_name from app_fields f, app_forms_tabs t where  f.entities_id='" . db_input(
                                $reports['entities_id']
                            ) . "' and f.id not in(" . $reports['start_date'] . "," . $reports['end_date'] . ")  and f.type not in ({$exclude_fiedls_types}) and f.forms_tabs_id=t.id order by t.sort_order, t.name, f.sort_order, f.name"
                        );
                        while ($v = db_fetch_array($fields_query)) {
                            if (!in_array($v['id'], explode(',', $reports['fields_in_listing']))) {
                                echo '<li id="form_fields_' . $v['id'] . '"><div>' . fields_types::get_option(
                                        $v['type'],
                                        'name',
                                        $v['name']
                                    ) . '</div></li>';
                            }
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
                    url: '<?php echo url_for("ext/ganttchart/configuration", "action=sort_fields&id=" . $_GET["id"])?>',
                    data: data
                });
            }
        });

    });
</script>



