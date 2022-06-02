<?php
echo ajax_modal_template_header(TEXT_EXT_CALCULATIONS) ?>

<?php
echo form_tag(
    'reports_form',
    url_for(
        'ext/item_pivot_tables/calc',
        'action=save&reports_id=' . _get::int('reports_id') . (isset($_GET['id']) ? '&id=' . $_GET['id'] : '')
    ),
    ['class' => 'form-horizontal']
) ?>
<?php
echo input_hidden_tag('type', 'calc') ?>
<div class="modal-body">
    <div class="form-body">


        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_NAME ?></label>
            <div class="col-md-9">
                <?php
                echo input_tag('name', $obj['name'], ['class' => 'form-control input-xlarge required']) ?>
            </div>
        </div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="name"><?php
                echo TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_QUERY . fields::get_available_fields_helper(
                        $reports['related_entities_id'],
                        'select_query'
                    ) ?></label>
            <div class="col-md-9">
                <?php
                echo tooltip_text(TEXT_ENTITY . ': ' . $app_entities_cache[$reports['related_entities_id']]['name']) ?>
                <?php
                echo textarea_tag(
                    'select_query',
                    $obj['select_query'],
                    ['class' => 'form-control  textarea-small required']
                ) ?>
                <?php
                echo tooltip_text(TEXT_EXT_ITEM_PIVOT_TABLES_SELECT_QUERY_TIP) ?>
            </div>
        </div>

        <h3 class="form-section"><?php
            echo TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY ?></h3>
        <p class="form-section-description"><?php
            echo TEXT_EXT_ITEM_PIVOT_TABLES_WHERE_QUERY_TIP; ?></p>

        <?php
        $html = '		
	<div class="table-scrollable ">	
		<table class="table table-striped table-bordered table-hover">
			<tr>
				<th></th>
				<th>' . TEXT_FIELD . '</th>
				<th>' . TEXT_ENTITY . '</th>
			</tr>
				<td>[current_item_id]</td>
				<td>' . TEXT_EXT_CURRENT_ITEM_ID . '</td>
				<td>' . $app_entities_cache[$reports['entities_id']]['name'] . '</td>
			<tr>
		</tr>';
        $fields_query = db_query(
            "select id, name, configuration, entities_id from app_fields where id in (" . $reports['related_entities_fields'] . ") order by field(id," . $reports['related_entities_fields'] . ")"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $html .= '
  		<tr>
  			<td>[field_' . $fields['id'] . '_value]</td>    		
    		<td>' . $fields['name'] . '</td>
    		<td>' . $app_entities_cache[$fields['entities_id']]['name'] . '</td>
    	</tr>';
        }

        $html .= '
    </table>
   </div>';

        echo $html;


        $entities_list = [];
        $entities_list[] = $reports['related_entities_id'];

        $parrent_entities = entities::get_parents($reports['related_entities_id']);

        if (count($parrent_entities) > 0) {
            $entities_list = array_merge($entities_list, $parrent_entities);
        }

        $where_query = (strlen($obj['where_query']) ? json_decode($obj['where_query'], true) : []);

        $count = 0;
        foreach ($entities_list as $entity_id) {
            ?>

            <div class="form-group">
                <label class="col-md-3 control-label" for="name"><?php
                    echo TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY . fields::get_available_fields_helper(
                            $entity_id,
                            'where_query_' . $entity_id
                        ) ?></label>
                <div class="col-md-9">
                    <?php
                    echo tooltip_text(TEXT_ENTITY . ': ' . $app_entities_cache[$entity_id]['name']) ?>
                    <?php
                    echo textarea_tag(
                        'where_query[' . $entity_id . ']',
                        (isset($where_query[$entity_id]) ? $where_query[$entity_id] : ''),
                        ['class' => 'form-control  textarea-small']
                    ) ?>
                    <?php
                    echo($count == 0 ? TEXT_EXAMPLE . ': [13]=[current_item_id] ' : '') ?>
                </div>
            </div>
            <?php

            $count++;
        }
        ?>


    </div>
</div>

<?php
echo ajax_modal_template_footer() ?>

</form>

<script>
    $(function () {
        $('#reports_form').validate();
    });
</script>  

 