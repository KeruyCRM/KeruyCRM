<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<?= \K::view()->render(\Helpers\Urls::components_path('main/entities/navigation')); ?>

    <h3 class="page-title"><?= \Helpers\Urls::link_to(
            \K::$fw->TEXT_NAV_LISTING_CONFIG,
            \Helpers\Urls::url_for('main/entities/listing_types', 'entities_id=' . \K::$fw->GET['entities_id'])
        ) . ' <i class="fa fa-angle-right"></i> ' . \Models\Main\Listing_types::get_type_title(
            \K::$fw->listing_types['type']
        ) . ' <i class="fa fa-angle-right"></i> ' . \K::$fw->TEXT_SECTIONS ?></h3>


<?= \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_ADD,
    \Helpers\Urls::url_for(
        'main/entities/listing_sections_form',
        'listing_types_id=' . \K::$fw->listing_types['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
    )
) . ' ' . \Helpers\Html::button_tag(
    \K::$fw->TEXT_BUTTON_SORT,
    \Helpers\Urls::url_for(
        'main/entities/listing_sections_sort',
        'entities_id=' . \K::$fw->GET['entities_id'] . '&listing_types_id=' . \K::$fw->listing_types['id']
    ),
    true,
    ['class' => 'btn btn-default']
) ?>

    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover">
            <thead>
            <tr>
                <th><?= \K::$fw->TEXT_ACTION ?></th>
                <th><?= \K::$fw->TEXT_TITLE ?></th>
                <th width="100%"><?= \K::$fw->TEXT_FIELDS ?></th>
                <th><?= \K::$fw->TEXT_ALIGN ?></th>
                <th><?= \K::$fw->TEXT_SORT_ORDER ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (count(\K::$fw->filters_query) == 0) {
                echo '<tr><td colspan="5">' . \K::$fw->TEXT_NO_RECORDS_FOUND . '</td></tr>';
            } ?>
            <?php
            //while ($v = db_fetch_array($filters_query)):
            foreach (\K::$fw->filters_query as $v):
                $v = $v->cast();
                ?>
                <tr>
                    <td style="white-space: nowrap;"><?= \Helpers\Html::button_icon_delete(
                            \Helpers\Urls::url_for(
                                'main/entities/listing_sections_delete',
                                'id=' . $v['id'] . '&listing_types_id=' . \K::$fw->listing_types['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        )
                        . ' ' . \Helpers\Html::button_icon_edit(
                            \Helpers\Urls::url_for(
                                'main/entities/listing_sections_form',
                                'id=' . $v['id'] . '&listing_types_id=' . \K::$fw->listing_types['id'] . '&entities_id=' . \K::$fw->GET['entities_id']
                            )
                        ) ?></td>
                    <td><?= $v['name'] ?></td>
                    <td><?php

                        if (strlen($v['fields'])) {
                            $choices = [];
                            /*$fields_query = db_query(
                                "select * from app_fields where id in (" . $v['fields'] . ") order by field(id," . $v['fields'] . ")"
                            );*/

                            $fields_query = \K::model()->db_fetch('app_fields', [
                                'id in (' . $v['fields'] . ')'
                            ], ['order' => 'field(id,' . $v['fields'] . ')'], 'type,name');

                            //while ($fields = db_fetch_array($fields_query)) {
                            foreach ($fields_query as $fields) {
                                $fields = $fields->cast();

                                $choices[] = \Models\Main\Fields_types::get_option(
                                    $fields['type'],
                                    'name',
                                    $fields['name']
                                );
                            }

                            echo implode('<br>', $choices);
                        }

                        ?></td>
                    <td><?= \Models\Main\Listing_types::get_sections_align_icon(
                            $v['text_align']
                        ) . ' ' . \K::$fw->align_choices[$v['text_align']] ?></td>
                    <td><?= $v['sort_order'] ?></td>
                </tr>
            <?php
            endforeach; ?>
            </tbody>
        </table>
    </div>

<?= \Helpers\Urls::link_to(
    \K::$fw->TEXT_BUTTON_BACK,
    \Helpers\Urls::url_for('main/entities/listing_types', 'entities_id=' . \K::$fw->GET['entities_id']),
    ['class' => 'btn btn-default']
) ?>