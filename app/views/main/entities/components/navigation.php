<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?php

$entities_info = \K::model()->db_find('app_entities', \K::$fw->GET['entities_id']);
$entities_cfg = \Models\Main\Entities::get_cfg(\K::$fw->GET['entities_id']);

$breadcrumb = [];

$breadcrumb[] = '<li>' . \Helpers\Urls::link_to(
        \K::$fw->TEXT_MENU_ENTITIES_LIST,
        \Helpers\Urls::url_for('main/entities/entities')
    ) . '<i class="fa fa-angle-right"></i></li>';

//get parents
if (count($parents = \Models\Main\Entities::get_parents(\K::$fw->GET['entities_id'])) > 0) {
    krsort($parents);

    foreach ($parents as $id) {
        $parent_entity_info = \K::model()->db_find('app_entities', $id);
        $breadcrumb[] = '<li>' . \Helpers\Urls::link_to(
                $parent_entity_info['name'],
                \Helpers\Urls::url_for('main/entities/entities_configuration', 'entities_id=' . $id)
            ) . '<i class="fa fa-angle-right"></i></li>';
    }
}

$breadcrumb[] = '<li>' . \Helpers\Urls::link_to(
        $entities_info['name'],
        \Helpers\Urls::url_for('main/entities/entities_configuration', 'entities_id=' . \K::$fw->GET['entities_id'])
    ) . '</li>';

?>

<ul class="page-breadcrumb breadcrumb">
    <?= implode('', $breadcrumb) ?>
</ul>

<div class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only"></span>
            <span class="fa fa-bar "></span>
            <span class="fa fa-bar fa-align-justify"></span>
            <span class="fa fa-bar"></span>
        </button>
        <a class="navbar-brand " href="<?= \Helpers\Urls::url_for(
            'main/entities/entities_configuration',
            'entities_id=' . \K::$fw->GET['entities_id']
        ) ?>"><?= $entities_info['name'] ?></a>
    </div>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <?= \Models\Main\Entities::render_goto_menu(\K::$fw->GET['entities_id']) ?>
            <li class="nav_entities_configuration">
                <?= \Helpers\Urls::link_to(
                    \K::$fw->TEXT_NAV_GENERAL_CONFIG,
                    \Helpers\Urls::url_for(
                        'main/entities/entities_configuration',
                        'entities_id=' . \K::$fw->GET['entities_id']
                    )
                ) ?>
            </li>
            <li class="nav_fields nav_fields_choices">
                <?= \Helpers\Urls::link_to(
                    \K::$fw->TEXT_NAV_FIELDS_CONFIG,
                    \Helpers\Urls::url_for('main/entities/fields', 'entities_id=' . \K::$fw->GET['entities_id'])
                ) ?>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-hover="dropdown"
                   data-toggle="dropdown"><?= \K::$fw->TEXT_NAV_VIEW_CONFIG ?> <i class="fa fa-angle-down"></i></a>
                <ul class="dropdown-menu">
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_FORM_CONFIG,
                            \Helpers\Urls::url_for('main/entities/forms', 'entities_id=' . \K::$fw->GET['entities_id'])
                        ) ?>
                    </li>
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_FORMS_FIELDS_DISPLAY_RULES,
                            \Helpers\Urls::url_for(
                                'main/forms_fields_rules/rules',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_LISTING_CONFIG,
                            \Helpers\Urls::url_for(
                                'main/entities/listing_types',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_FILTERS_PANELS,
                            \Helpers\Urls::url_for(
                                'main/filters_panels/panels',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_ITEM_PAGE_CONFIG,
                            \Helpers\Urls::url_for(
                                'main/entities/item_page_configuration',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>

                    <?php
                    if (\K::$fw->GET['entities_id'] == 1): ?>
                        <li>
                            <?= \Helpers\Urls::link_to(
                                \K::$fw->TEXT_NAV_USER_PUBLIC_PROFILE_CONFIG,
                                \Helpers\Urls::url_for(
                                    'main/entities/user_public_profile',
                                    'entities_id=' . \K::$fw->GET['entities_id']
                                )
                            ) ?>
                        </li>
                    <?php
                    endif ?>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-hover="dropdown"
                   data-toggle="dropdown"><?= \K::$fw->TEXT_NAV_ACCESS_CONFIG ?> <i class="fa fa-angle-down"></i></a>
                <ul class="dropdown-menu">
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_ENTITY_ACCESS,
                            \Helpers\Urls::url_for('main/entities/access', 'entities_id=' . \K::$fw->GET['entities_id'])
                        ) ?>
                    </li>
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_FIELDS_ACCESS,
                            \Helpers\Urls::url_for(
                                'main/entities/fields_access',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_ACCESS_RULES,
                            \Helpers\Urls::url_for(
                                'main/access_rules/fields',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_RECORDS_VISIBILITY,
                            \Helpers\Urls::url_for(
                                'main/records_visibility/rules',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-hover="dropdown"
                   data-toggle="dropdown"><?= \K::$fw->TEXT_NAV_COMMENTS_CONFIG ?> <i class="fa fa-angle-down"></i></a>
                <ul class="dropdown-menu">
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_COMMENTS_ACCESS,
                            \Helpers\Urls::url_for(
                                'main/entities/comments_access',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>
                    <li>
                        <?= \Helpers\Urls::link_to(
                            \K::$fw->TEXT_NAV_COMMENTS_FIELDS,
                            \Helpers\Urls::url_for(
                                'main/entities/comments_form',
                                'entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?>
                    </li>
                </ul>
            </li>

            <?php
            $choices = [];

            $choices[] = [
                'title' => \K::$fw->TEXT_HELP_SYSTEM,
                'url' => \Helpers\Urls::url_for('main/help_pages/pages', 'entities_id=' . \K::$fw->GET['entities_id'])
            ];

            if (\Helpers\App::is_ext_installed()) {
                $choices[] = [
                    'title' => \K::$fw->TEXT_EXT_EMAIL_SENDING_RULES,
                    'url' => \Helpers\Urls::url_for(
                        'ext/email_sending/rules',
                        'entities_id=' . \K::$fw->GET['entities_id']
                    )
                ];
            }

            $html = '';
            if (count($choices)) {
                $html .= '
  		<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-hover="dropdown" data-toggle="dropdown">' . \K::$fw->TEXT_EXTRA . ' <i class="fa fa-angle-down"></i></a>
					<ul class="dropdown-menu">';

                foreach ($choices as $v) {
                    $html .= '<li>' . \Helpers\Urls::link_to($v['title'], $v['url']) . '</li>';
                }

                $html .= '
  		</ul>
		</li>';
            }

            echo $html;
            ?>
        </ul>
    </div>
    <!-- /.navbar-collapse -->
</div>

<script>
    $(function () {
        $('.nav_<?= \K::$fw->app_action ?>').addClass('active');

        $('.nav_entities_goto').click(function () {
            if (!$(this).hasClass('tree-table-menu-active')) {
                $(this).addClass('tree-table-menu-active')

                if (app_language_text_direction == 'rtl') {
                    $('.nav_entities_goto .dropdown-menu li').css('text-align', 'right')
                }

                setTimeout(function () {
                    $('.tree-table-menu').treetable()
                }, 100)
            }
        })
    });
</script>