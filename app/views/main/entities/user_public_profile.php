<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

<h3 class="page-title"><?= \K::$fw->TEXT_NAV_USER_PUBLIC_PROFILE_CONFIG ?></h3>

<div><?= \K::$fw->TEXT_USER_PUBLIC_PROFILE_CFG_INFO ?></div>

<table style="width: 100%; max-width: 960px;">
    <tr>
        <td valign="top" width="50%">
            <fieldset>
                <legend><?= \K::$fw->TEXT_FIELDS_IN_USER_PUBLIC_PROFILE ?></legend>
                <div class="cfg_listing">
                    <ul id="fields_in_profile" class="sortable">
                        <?php
                        //while ($v = db_fetch_array($fields_query)) {
                        foreach (\K::$fw->fields_query_in as $v) {
                            echo '<li id="form_fields_' . $v['id'] . '"><div>' . \Models\Main\Fields_types::get_option(
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
        <td style="padding-left: 25px;" valign="top">
            <fieldset>
                <legend><?= \K::$fw->TEXT_FIELDS_EXCLUDED_FROM_USER_PUBLIC_PROFILE ?></legend>
                <div class="cfg_listing">
                    <ul id="fields_excluded_from_profile" class="sortable">
                        <?php
                        //while ($v = db_fetch_array($fields_query)) {
                        foreach (\K::$fw->fields_query_notin as $v) {
                            echo '<li id="form_fields_' . $v['id'] . '"><div>' . \Models\Main\Fields_types::get_option(
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
                        'main/entities/user_public_profile/sort_fields',
                        'entities_id=' . \K::$fw->GET["entities_id"]
                    )?>',
                    data: data
                });
            }
        });
    });
</script>