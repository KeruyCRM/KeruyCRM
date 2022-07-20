<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \Helpers\App::ajax_modal_template_header(\K::$fw->TEXT_CHANGE_SKIN) ?>

    <div class="skins-list">
        <ul>
            <?php
            $skins = \Helpers\App::app_get_skins_choices(false);
            foreach ($skins as $skin => $name): ?>
                <li>
                    <?= $name; ?>
                    <div style="border: 1px solid #b9b9b9; margin: 5px; width: 80px; height: 80px; cursor: pointer; background: white;"
                         onClick="location='<?= \Helpers\Urls::url_for(
                             'main/users/change_skin/change_skin',
                             'set_skin=' . $skin,
                             true
                         ); ?>'">
                        <?= \Helpers\Html::image_tag('css/skins/' . $skin . '/' . $skin . '.png'); ?>
                    </div>
                </li>
            <?php
            endforeach ?>
        </ul>
    </div>

<?= \Helpers\App::ajax_modal_template_footer('hide-save-button') ?>