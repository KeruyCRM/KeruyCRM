<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<fieldset>
    <legend><?= \K::$fw->TEXT_NAV_LISTING_FILTERS_CONFIG ?></legend>
    <p><?= \K::$fw->TEXT_LISTING_FILTERS_CFG_INFO ?></p>

    <?= \Helpers\Html::button_tag(
        \K::$fw->TEXT_CONFIGURE_FILTERS,
        \Helpers\Urls::url_for(
            'main/entities/entityfield_filters',
            'fields_id=' . \K::$fw->fields_info['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
        ),
        false
    ) ?>
    <br><br>
</fieldset>
<div class="row">
    <div class="col-md-6">
        <fieldset>
            <legend><?= \K::$fw->TEXT_FIELDS_IN_POPUP ?></legend>
            <p><?= \K::$fw->TEXT_FIELDS_IN_POPUP_RELATED_ITEMS ?></p>

            <div class="checkbox-list">
                <?php
                //while ($v = db_fetch_array($fields_query)) {
                foreach (\K::$fw->fields_query as $v) {
                    echo '<label>' . \Helpers\Html::input_checkbox_tag(
                            'fields_in_popup[]',
                            $v['id'],
                            ['checked' => in_array($v['id'], explode(',', \K::$fw->cfg['fields_in_popup']))]
                        ) . ' ' . \Models\Main\Fields_types::get_option($v['type'], 'name', $v['name']) . '</label>';
                }
                ?>
            </div>
        </fieldset>
    </div>
</div>