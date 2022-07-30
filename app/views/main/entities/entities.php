<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_ENTITIES_HEADING ?></h3>
<p><?= \K::$fw->TEXT_ENTITIES_PAGE_INFO ?></p>

<div class="row">
    <div class="col-md-9" style="padding-bottom: 5px;">
        <?= \Helpers\Html::button_tag(
            \K::$fw->TEXT_ADD_NEW_ENTITY,
            \Helpers\Urls::url_for('main/entities/entities_form')
        ) . ' ' .
        \Helpers\Html::button_tag(
            '<i class="fa fa-sort-amount-asc"></i> ' . \K::$fw->TEXT_SORT,
            \Helpers\Urls::url_for('main/entities/entities_sort'),
            true,
            ['class' => 'btn btn-default']
        ) . ' ' .
        \Helpers\Html::button_tag(
            '<i class="fa fa-list"></i> ' . \K::$fw->TEXT_ENTITIES_GROUPS,
            \Helpers\Urls::url_for('main/entities/entities_groups'),
            false,
            ['class' => 'btn btn-default']
        ) . ' ' .
        \Helpers\Html::button_tag(
            '<i class="fa fa-sitemap"></i> ' . \K::$fw->TEXT_FLOWCHART,
            \Helpers\Urls::url_for('main/entities/entities_flowchart'),
            false,
            ['class' => 'btn btn-default']
        ) . ' ' .
        \Helpers\Html::button_tag(
            '<i class="fa fa-exchange"></i> ' . \K::$fw->TEXT_CHANGE_STRUCTURE,
            \Helpers\Urls::url_for('main/entities/entities_change_structure'),
            true,
            ['class' => 'btn btn-default']
        ) ?>

    </div>
    <div class="col-md-3">
        <?php
        $choices = \Models\Main\Entities_groups::get_choices();

        if (count($choices)) {
            echo \Helpers\Html::form_tag(
                    'entities_filter_form',
                    \Helpers\Urls::url_for('main/entities/entities/set_entities_filter')
                ) .
                \Helpers\Html::select_tag(
                    'entities_filter',
                    \Models\Main\Entities_groups::get_choices(),
                    \K::$fw->entities_filter,
                    ['class' => 'form-control  ', 'onChange' => 'this.form.submit()']
                ) .
                '</form>';
        }
        ?>
    </div>
</div>

<div class="table-scrollable" style="overflow-x:visible;overflow-y:visible; ">
    <table class="tree-table table table-striped table-bordered table-hover">
        <thead>
        <tr>
            <th><?= \K::$fw->TEXT_ACTION ?></th>
            <th><?= \K::$fw->TEXT_ID ?></th>
            <th><?= \K::$fw->TEXT_GROUP ?></th>
            <th width="100%"><?= \K::$fw->TEXT_NAME ?></th>
            <th><?= \K::$fw->TEXT_NOTE ?></th>
            <th><?= \K::$fw->TEXT_SORT_ORDER ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (count(\K::$fw->entities_list) == 0) {
            echo '<tr><td colspan="4">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
        } ?>
        <?php
        foreach (\K::$fw->entities_list as $v):
            ?>
            <tr>
                <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                        \Helpers\Urls::url_for('main/entities/entities_delete', 'id=' . $v['id'])
                    ) . ' ' .
                    \Helpers\Html::button_icon_edit(
                        \Helpers\Urls::url_for('main/entities/entities_form', 'id=' . $v['id'])
                    ) . ' ' .
                    \Helpers\Html::button_icon(
                        \K::$fw->TEXT_CREATE_SUB_ENTITY,
                        'fa fa-plus',
                        \Helpers\Urls::url_for('main/entities/entities_form', 'parent_id=' . $v['id'])
                    ) . ' ' .
                    (\Models\Main\Entities::has_subentities($v['id']) > 1 ? \Helpers\Html::button_icon(
                        \K::$fw->TEXT_SORT,
                        'fa fa-sort-amount-asc',
                        \Helpers\Urls::url_for('main/entities/entities_sort', 'parent_id=' . $v['id'])
                    ) : '')
                    ?></td>
                <td><?= $v['id'] ?></td>
                <td><?= \Models\Main\Entities_groups::get_name_by_id($v['group_id']) ?></td>
                <td style="white-space: nowrap">
                    <?= '<div class="tt" data-tt-id="entity_' . $v['id'] . '" ' . ($v['parent_id'] > 0 ? 'data-tt-parent="entity_' . $v['parent_id'] . '"' : '') . '></div>' ?>
                    <div class="btn-group">
                        <button type="button" type="button" class="btn btn-default"
                                onClick="location.href='<?= \Helpers\Urls::url_for(
                                    'entities/entities_configuration',
                                    'entities_id=' . $v['id']
                                ) ?>'"><?= $v['name'] ?></button>
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"
                                data-hover="dropdown"><i class="fa fa-angle-down"></i></button>
                        <ul class="dropdown-menu" role="menu">
                            <li><?= \Helpers\Urls::link_to(
                                    \K::$fw->TEXT_NAV_GENERAL_CONFIG,
                                    \Helpers\Urls::url_for(
                                        'main/entities/entities_configuration',
                                        'entities_id=' . $v['id']
                                    )
                                ) ?></li>
                            <li><?= \Helpers\Urls::link_to(
                                    \K::$fw->TEXT_NAV_FIELDS_CONFIG,
                                    \Helpers\Urls::url_for('main/entities/fields', 'entities_id=' . $v['id'])
                                ) ?></li>
                            <li class="dropdown-submenu">
                                <a href="#"><?= \K::$fw->TEXT_NAV_VIEW_CONFIG ?></a>
                                <ul class="dropdown-menu">
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_FORM_CONFIG,
                                            \Helpers\Urls::url_for('main/entities/forms', 'entities_id=' . $v['id'])
                                        ) ?></li>
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_FORMS_FIELDS_DISPLAY_RULES,
                                            \Helpers\Urls::url_for(
                                                'main/forms_fields_rules/rules',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_LISTING_CONFIG,
                                            \Helpers\Urls::url_for(
                                                'main/entities/listing_types',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_FILTERS_PANELS,
                                            \Helpers\Urls::url_for(
                                                'main/filters_panels/panels',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_ITEM_PAGE_CONFIG,
                                            \Helpers\Urls::url_for(
                                                'main/entities/item_page_configuration',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                    <?php
                                    if ($v['id'] == 1): ?>
                                        <li><?= \Helpers\Urls::link_to(
                                                \K::$fw->TEXT_NAV_USER_PUBLIC_PROFILE_CONFIG,
                                                \Helpers\Urls::url_for(
                                                    'main/entities/user_public_profile',
                                                    'entities_id=' . $v['id']
                                                )
                                            ) ?></li>
                                    <?php
                                    endif ?>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#"><?= \K::$fw->TEXT_NAV_ACCESS_CONFIG ?></a>
                                <ul class="dropdown-menu">
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_ENTITY_ACCESS,
                                            \Helpers\Urls::url_for('main/entities/access', 'entities_id=' . $v['id'])
                                        ) ?></li>
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_FIELDS_ACCESS,
                                            \Helpers\Urls::url_for(
                                                'main/entities/fields_access',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_ACCESS_RULES,
                                            \Helpers\Urls::url_for(
                                                'main/access_rules/fields',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_RECORDS_VISIBILITY,
                                            \Helpers\Urls::url_for(
                                                'main/records_visibility/rules',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                </ul>
                            </li>
                            <li class="dropdown-submenu">
                                <a href="#"><?= \K::$fw->TEXT_NAV_COMMENTS_CONFIG ?></a>
                                <ul class="dropdown-menu">
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_COMMENTS_ACCESS,
                                            \Helpers\Urls::url_for(
                                                'main/entities/comments_access',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                    <li><?= \Helpers\Urls::link_to(
                                            \K::$fw->TEXT_NAV_COMMENTS_FIELDS,
                                            \Helpers\Urls::url_for(
                                                'main/entities/comments_form',
                                                'entities_id=' . $v['id']
                                            )
                                        ) ?></li>
                                </ul>
                            </li>

                            <?php
                            $choices = [];

                            $choices[] = [
                                'title' => \K::$fw->TEXT_HELP_SYSTEM,
                                'url' => \Helpers\Urls::url_for('main/help_pages/pages', 'entities_id=' . $v['id'])
                            ];

                            if (\Helpers\App::is_ext_installed()) {
                                $choices[] = [
                                    'title' => \K::$fw->TEXT_EXT_EMAIL_SENDING_RULES,
                                    'url' => \Helpers\Urls::url_for(
                                        'ext/email_sending/rules',
                                        'entities_id=' . $v['id']
                                    )
                                ];
                            }

                            $html = '';
                            if (count($choices)) {
                                $html .= '
  		<li class="dropdown-submenu">
				<a href="#">' . \K::$fw->TEXT_EXTRA . '</a>
					<ul class="dropdown-menu">';

                                foreach ($choices as $menu) {
                                    $html .= '<li>' . \Helpers\Urls::link_to($menu['title'], $menu['url']) . '</li>';
                                }

                                $html .= '
  		</ul>
		</li>';
                            }

                            echo $html;
                            ?>
                        </ul>
                    </div>

                </td>
                <td><?= \Helpers\App::tooltip_icon($v['notes'], 'left') ?></td>
                <td><?= $v['sort_order'] ?></td>
            </tr>
        <?php
        endforeach ?>
        </tbody>
    </table>
</div>