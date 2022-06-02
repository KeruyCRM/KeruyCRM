<form class="form-inline" role="form" id="track_changes_filters" action="<?php
echo url_for('ext/track_changes/view', 'action=export&reports_id=' . $app_reports['id']) ?>" method="post">
    <div class="form-group">
        <label for="type"></label>
        <?php
        echo select_tag('type', track_changes::get_type_chocies(), '', ['class' => 'form-control']) ?>
    </div>

    <?php
    $choices = [];
    $entities_query = db_query(
        "select e.id, e.name as entity_name from app_ext_track_changes_entities te left join app_entities e on te.entities_id=e.id, app_ext_track_changes t where t.id=te.reports_id and te.reports_id='" . $app_reports['id'] . "' order by e.name"
    );
    while ($entities = db_fetch_array($entities_query)) {
        //check access
        if (!users::has_users_access_name_to_entity('view', $entities['id'])) {
            continue;
        }

        $choices[$entities['id']] = $entities['entity_name'];
    }

    if (count($choices) > 1) {
        $choices = ['' => ''] + $choices;
    }

    ?>
    <div class="form-group">
        <label for="filter_entities_id">&nbsp;<i class="fa fa-sitemap"></i></label>
        <?php
        echo select_tag('entities_id', $choices, '', ['class' => 'form-control']) ?>
    </div>

    <?php
    $choices = ['' => ''];
    $listing_sql = "select created_by from app_ext_track_changes_log where reports_id='" . $app_reports['id'] . "' and created_by>0 group by created_by";
    $items_query = db_query($listing_sql);
    while ($items = db_fetch_array($items_query)) {
        $choices[$items['created_by']] = (isset($app_users_cache[$items['created_by']]) ? $app_users_cache[$items['created_by']]['name'] : TEXT_EXT_PUBLIC_FORM);
    }

    asort($choices);
    ?>
    <div class="form-group">
        <label for="filter_users_id">&nbsp;<i class="fa fa-user" aria-hidden="true"></i></label>
        <?php
        echo select_tag('created_by', $choices, '', ['class' => 'form-control']) ?>
    </div>

    <div class="form-group">

        <div class="input-group input-large datepicker input-daterange daterange-filter">
			<span class="input-group-addon">
				<i class="fa fa-calendar"></i>
			</span>
            <?php
            echo input_tag('from', '', ['class' => 'form-control', 'placeholder' => TEXT_DATE_FROM]) ?>
            <span class="input-group-addon">
				<i style="cursor:pointer" class="fa fa-refresh" aria-hidden="true" title="<?php
                echo TEXT_EXT_RESET ?>" onClick="reset_date_rane_filter('daterange-filter')"></i>
			</span>
            <?php
            echo input_tag('to', '', ['class' => 'form-control', 'placeholder' => TEXT_DATE_TO]) ?>
        </div>
    </div>


    <div class="form-group">
        <div class="input-group input-small ">
            <?php
            echo input_tag('id', '', ['class' => 'form-control', 'placeholder' => TEXT_ID]) ?>
            <span class="input-group-addon" style="cursor:pointer"
                  onClick="load_items_listing('track_changes_listing',1)">
				<i class="fa fa-search" aria-hidden="true" title="<?php
                echo TEXT_SEARCH ?>"></i>
			</span>
        </div>
    </div>

    <div class="form-group">
        <button type="button" class="btn btn-default" title="<?php
        echo TEXT_EXPORT ?>" onClick="export_track_changes()"><i class="fa fa-file-excel-o"></i></button>
    </div>

</form>

<script>
    $(function () {
        $('#track_changes_filters .form-control').change(function () {
            load_items_listing('track_changes_listing', 1)
        })
    })

    function reset_date_rane_filter(class_name) {
        $('.' + class_name + ' [name=from]').val('')
        $('.' + class_name + ' [name=to]').val('')

        load_items_listing('track_changes_listing', 1)
    }

    function set_filter_by_id(entities_id, itmes_id) {
        $('#entities_id').val(entities_id)
        $('#id').val(itmes_id)

        load_items_listing('track_changes_listing', 1)
    }

    function export_track_changes() {
        $('#track_changes_filters').submit();
    }

</script>