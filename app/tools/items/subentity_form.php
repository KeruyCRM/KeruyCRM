<?php

namespace Tools\Items;

class Subentity_form
{
    public $entities_id;
    public $items_id;
    public $cfg;
    public $field_id;
    public $field_name;

    public function __construct($entities_id, $items_id, $field_id)
    {
        global $app_fields_cache;

        $this->entities_id = $entities_id;
        $this->items_id = ($items_id > 0 ? $items_id : false);
        $this->cfg = new fields_types_cfg($app_fields_cache[$entities_id][$field_id]['configuration']);
        $this->field_id = $field_id;
        $this->field_name = $app_fields_cache[$entities_id][$field_id]['name'];
    }

    public function render_button()
    {
        global $app_path, $app_items_form_name, $public_form;

        $html = '';

        $button_title = (strlen($this->cfg->get('button_title')) ? $this->cfg->get('button_title') : \K::f3(
        )->TEXT_ADD);
        $btn_css = 'btn-color-' . $this->field_id;

        if (strlen($this->cfg->get('button_icon'))) {
            $button_title = app_render_icon($this->cfg->get('button_icon')) . ' ' . $button_title;
        }

        switch ($this->cfg->get('fields_display')) {
            case 'column':
            case 'row':
                $html = '<button type="button" id="subentity_form_btn' . $this->field_id . '" class="btn ' . $btn_css . ' subentity_form_btn">' . $button_title . '</button>';
                $html .= '
                    <script>
                    $("#subentity_form_btn' . $this->field_id . '").click(function(){
                        subentity_form' . $this->field_id . '_add()
                    })
                    </script>
                    ';

                //autoinsert form
                if ((int)$this->cfg->get('auto_insert') > 0 and !$this->items_id) {
                    $html .= '
                        <script>
                        $(function(){
                            for(i=0;i<' . (int)$this->cfg->get('auto_insert') . ';i++)
                            {
                                subentity_form' . $this->field_id . '_add()
                            }
                        })                            
                        </script>
                    ';
                }
                break;
            case 'window':
                $url_params = 'is_submodal=true&redirect_to=subentity_form_' . $this->entities_id . '_' . $this->field_id . '&entities_id=' . $this->entities_id . '&fields_id=' . $this->field_id . '&current_entity_id=' . $this->cfg->get(
                        'entity_id'
                    ) . '&path=' . ($this->items_id ? $app_path . '/' : '') . $this->cfg->get(
                        'entity_id'
                    ) . '&form_name=' . $app_items_form_name;

                if ($app_items_form_name == 'public_form' and isset($public_form['id'])) {
                    $url_params .= '&public_form_id=' . $public_form['id'];
                }

                $submodal_url = url_for('subentity/form', $url_params);
                $html = '<button type="button" id="subentity_form_btn' . $this->field_id . '" class="btn btn-default btn-submodal-open btn-submodal-open-chosen subentity_form_btn ' . $btn_css . '" data-parent-entity-item-id="" data-field-id="" data-submodal-url="' . $submodal_url . '">' . $button_title . '</button>';
                break;
        }

        $html = '<div style="text-align: ' . $this->cfg->get('button_position') . '">' . $html . '</div>';
        $html .= app_button_color_css($this->cfg->get('button_color'), $btn_css);

        return $html;
    }

    public function render_js()
    {
        $html = '
            <script>
            function subentity_form' . $this->field_id . '_add()
            {
                $("#subentity_form_btn' . $this->field_id . '").attr("disabled","disabled")
                    
                rows_count = $("#subentity_form' . $this->field_id . '_rows_count").val();    
                rows_count = parseInt(rows_count)+1;
                $("#subentity_form' . $this->field_id . '_rows_count").val(rows_count); 
                $("#fields_' . $this->field_id . '").val(rows_count)    
                                                        
                $.ajax({
                    method: "POST",
                    url: "' . url_for(
                'subentity/form',
                'path=' . $this->entities_id . '&action=add&entities_id=' . $this->entities_id . '&items_id=' . $this->items_id . '&fields_id=' . $this->field_id
            ) . '",
                    data: {rows_count: rows_count}
                }).done(function( data ) {
                    $("#subentity_form' . $this->field_id . '").append(data)
                    $("#subentity_form_btn' . $this->field_id . '").attr("disabled",false)
                    $(window).resize();
                    
                    appHandleUniform()
                    
                    subentity_form' . $this->field_id . '_check()

                })
                
            }
            
            function subentity_form' . $this->field_id . '_remove(rows_count,item_id)
            {
                if(confirm("' . addslashes(\K::f3()->TEXT_ARE_YOU_SURE) . '"))
                {
                    $("#subentity_form' . $this->field_id . ' #suentity_form_row_"+rows_count).remove();
                    $(window).resize();
                    
                    $.ajax({
                        method:"POST", 
                        url:"' . url_for(
                'subentity/form',
                'path=' . $this->entities_id . '&action=remove_item&entities_id=' . $this->entities_id . '&fields_id=' . $this->field_id
            ) . '",
                        data: {row: item_id}
                    })

                    subentity_form' . $this->field_id . '_check()
                }
            }
            
            function subentity_form' . $this->field_id . '_itemrow_remove(row)
            {
                if(confirm("' . addslashes(\K::f3()->TEXT_ARE_YOU_SURE) . '"))
                {
                    $("#subentity_form' . $this->field_id . ' #itemrow_"+row).remove();
                    $(window).resize();
                    
                    $.ajax({
                        method:"POST", 
                        url:"' . url_for(
                'subentity/form',
                'path=' . $this->entities_id . '&action=remove_item&entities_id=' . $this->entities_id . '&fields_id=' . $this->field_id
            ) . '",
                        data: {row: row}
                    })
                    
                    subentity_form' . $this->field_id . '_check()
                }
            }
            
            function subentity_form' . $this->field_id . '_check()
            {
                max_rows_count = parseInt($("#subentity_form' . $this->field_id . '_max_rows_count").val());            
                current_rows_count = $(".suentity-form-row-' . $this->field_id . '").length;
                $("#fields_' . $this->field_id . '").val((current_rows_count>0 ? current_rows_count:""))    
                $("#fields_' . $this->field_id . '-error").remove()
                                                    
                if(current_rows_count>=max_rows_count && max_rows_count>0)
                {                 
                    $("#subentity_form_btn' . $this->field_id . '").hide()
                }
                else
                {
                    $("#subentity_form_btn' . $this->field_id . '").show()
                }
            }
            
            subentity_form' . $this->field_id . '_check()
                
            </script>
            ';

        return $html;
    }

    public function render_form($rows_count, $form_item_id = false)
    {
        switch ($this->cfg->get('fields_display')) {
            case 'column':
                return $this->render_form_column($rows_count, $form_item_id);
                break;
            case 'row':
                return $this->render_form_row($rows_count, $form_item_id);
                break;
        }
    }

    public function render_form_column($rows_count, $form_item_id = false)
    {
        $form_fields = $this->get_form_fields($rows_count, $form_item_id);

        //print_rr($form_fields);

        $html = '
        <div id="suentity_form_row_' . $rows_count . '" class="suentity-form-row-' . $this->field_id . '">   
           <h3 class="form-section">' . ($this->cfg->get(
                'hide_field_name'
            ) != 1 ? $this->field_name : '') . ($this->cfg->get(
                'has_count'
            ) == 1 ? ' <span>' . $rows_count . '</span>' : '') . '</h3>
           <button onClick="subentity_form' . $this->field_id . '_remove(' . $rows_count . ',' . $form_item_id . ')"  type="button" class="btn btn-default btn-subentity-form-remove" title="' . addslashes(
                \K::f3()->TEXT_DELETE
            ) . '"><i class="las la-times"></i></button>
           ';

        foreach ($form_fields as $field) {
            $html .= '            
                <div class="row form-group form-group-' . $field['id'] . ' form-group-' . $field['type'] . '">
	          	<label class="col-md-3 control-label" for="fields_' . $field['id'] . '">' .
                ($field['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                ($field['tooltip_display_as'] == 'icon' ? tooltip_icon($field['tooltip']) : '') .
                $field['name'] .
                '</label>
	            <div class="col-md-9">	
	          	  <div id="fields_' . $field['id'] . '_rendered_value">' . $field['html'] . '</div>
	              ' . ($field['tooltip_display_as'] != 'icon' ? tooltip_text($field['tooltip']) : '') . '
	            </div>			
	          </div> 
                ';
        }

        $html .= '
           </div>
           ';

        return $html;
    }

    public function render_form_row($rows_count, $form_item_id = false)
    {
        $form_fields = $this->get_form_fields($rows_count, $form_item_id);

        //print_rr($form_fields);

        $html = '                        
        <div id="suentity_form_row_' . $rows_count . '" class="suentity-form-row-' . $this->field_id . '">              
        <table class="suentity_form_row_table">
            <tr>
                <td width="100%" style="padding-right: 15px;">
                    <div class="row">
           ';

        $column_width = [];
        foreach (explode(',', $this->cfg->get('column_width')) as $v) {
            $column_width[] = (int)$v;
        }

        foreach ($form_fields as $k => $field) {
            $html .= '            
                <div class="col-md-3 form-group-' . $field['id'] . ' form-group-' . $field['type'] . '" ' . ((isset($column_width[$k]) and $column_width[$k] > 0) ? 'style="width: ' . $column_width[$k] . '%"' : '') . '>
	          	<label class="control-label" for="fields_' . $field['id'] . '">' .
                ($field['is_required'] == 1 ? '<span class="required-label">*</span>' : '') .
                ($field['tooltip_display_as'] == 'icon' ? tooltip_icon($field['tooltip']) : '') .
                $field['name'] .
                '</label>
	            <div>	
	          	  <div id="fields_' . $field['id'] . '_rendered_value">' . $field['html'] . '</div>
	              ' . ($field['tooltip_display_as'] != 'icon' ? tooltip_text($field['tooltip']) : '') . '
	            </div>			
	          </div> 
                ';
        }

        $html .= '
                    </div>
                </td>
                <td class="control-label"><button onClick="subentity_form' . $this->field_id . '_remove(' . $rows_count . ',' . $form_item_id . ')"  type="button" class="btn btn-default btn-subentity-form-row-remove" title="' . addslashes(
                \K::f3()->TEXT_DELETE
            ) . '"><i class="las la-times"></i></button></td>
                </tr>
           </table>
           </div>
           
           ';

        return $html;
    }

    public function get_form_fields($rows_count, $form_item_id = false)
    {
        $fields_in_form = (is_array($this->cfg->get('fields_in_form')) ? implode(
            ',',
            $this->cfg->get('fields_in_form')
        ) : '');

        if (!strlen($fields_in_form)) {
            return [];
        }

        $obj = [];

        if ($form_item_id) {
            $obj = db_find('app_entity_' . $this->cfg->get('entity_id'), $form_item_id);
        } else {
            $obj = db_show_columns('app_entity_' . $this->cfg->get('entity_id'));
        }

        $is_new_item = (!$form_item_id ? true : false);

        $form_fields = [];

        $fields_query = db_query(
            "select * from app_fields where id in (" . $fields_in_form . ") and  entities_id='" . db_input(
                $this->cfg->get('entity_id')
            ) . "' order by field(id," . $fields_in_form . ")"
        );
        while ($fields = db_fetch_array($fields_query)) {
            //render field
            $html = fields_types::render(
                $fields['type'],
                $fields,
                $obj,
                ['parent_entity_item_id' => $this->items_id, 'form' => 'item', 'is_new_item' => $is_new_item]
            );

            //remove width for rows
            if ($this->cfg->get('fields_display') == 'row') {
                $html = str_replace(['input-xsmall', 'input-small', 'input-medium', 'input-large', 'input-xlarge'],
                    '',
                    $html);
            }

            //prepare ids
            if (preg_match_all('/name="fields\[(\d+)\]"/', $html, $matches)) {
                //print_rr($matches);

                foreach ($matches[1] as $field_id) {
                    $html = str_replace(
                        'name="fields[' . $field_id . ']"',
                        'name="subentityform' . $this->field_id . '_fields[' . (!$form_item_id ? 'row' . $rows_count : $form_item_id) . '][' . $field_id . ']"',
                        $html
                    );
                    $html = str_replace(
                        'id="fields_' . $field_id . '"',
                        'id="subentityform' . $this->field_id . '_fields_' . (!$form_item_id ? 'row_' . $rows_count : $form_item_id) . '_' . $field_id . '"',
                        $html
                    );
                }
            }

            //prepare ids for checkboxes or multiple select
            if (preg_match_all('/name="fields\[(\d+)\]\[\]"/', $html, $matches)) {
                //print_rr($matches);

                foreach ($matches[1] as $field_id) {
                    $html = str_replace(
                        'name="fields[' . $field_id . '][]"',
                        'name="subentityform' . $this->field_id . '_fields[' . (!$form_item_id ? 'row' . $rows_count : $form_item_id) . '][' . $field_id . '][]"',
                        $html
                    );
                    $html = str_replace(
                        'id="fields_' . $field_id . '_',
                        'id="subentityform' . $this->field_id . '_fields_' . (!$form_item_id ? 'row_' . $rows_count : $form_item_id) . '_' . $field_id . '_',
                        $html
                    );
                }
            }

            $form_fields[] = [
                'id' => $fields['id'],
                'name' => $fields['name'],
                'type' => $fields['type'],
                'tooltip' => $fields['tooltip'],
                'is_required' => $fields['is_required'],
                'tooltip_display_as' => $fields['tooltip_display_as'],
                'html' => $html,
            ];
        }

        return $form_fields;
    }

    public function save_form()
    {
        global $app_subentity_form_items;

        switch ($this->cfg->get('fields_display')) {
            case 'column':
            case 'row':
                //print_rr($_POST);
                $subentityform = $_POST['subentityform' . $this->field_id . '_fields'] ?? [];
                $this->save_form_post($subentityform);

                //reset data after save;
                $_POST['subentityform' . $this->field_id . '_fields'] = [];
                break;
            case 'window':
                $subentityform = $app_subentity_form_items[$this->field_id] ?? [];
                $this->save_form_post($subentityform);

                //reset data after save
                $app_subentity_form_items[$this->field_id] = [];
                break;
        }
    }

    public function save_form_post($subentityform)
    {
        global $app_fields_cache, $app_user, $app_subentity_form_items_deleted;

        $current_entity_id = $this->cfg->get('entity_id');

        //print_rr($subentityform);
        //exit();

        $form_items_list = [];

        foreach ($subentityform as $item_id => $fields) {
            $sql_data = [];
            $choices_values = new choices_values($current_entity_id);

            foreach ($fields as $field_id => $field_value) {
                $field = $app_fields_cache[$current_entity_id][$field_id];

                if (is_array($field_value) and !in_array($field['type'], ['fieldtype_tags', 'fieldtype_image_ajax'])) {
                    $field_value = implode(',', $field_value);
                }

                $process_options = [
                    'class' => $field['type'],
                    'value' => $field_value,
                    'field' => $field,
                    'is_new_item' => true,
                    'current_field_value' => '',
                    'item' => [],
                ];

                $sql_data['field_' . $field['id']] = fields_types::process($process_options);

                //prepare choices values for fields with multiple values
                $choices_values->prepare($process_options);
            }

            //check empty post fields
            if ($this->cfg->get('fields_display') !== 'window' and is_array($this->cfg->get('fields_in_form'))) {
                foreach ($this->cfg->get('fields_in_form') as $field_id) {
                    if (!isset($sql_data['field_' . $field_id])) {
                        $sql_data['field_' . $field_id] = '';
                    }
                }
            }

            //print_rr($sql_data);
            //exit();

            if (is_numeric($item_id)) {
                $sql_data['date_updated'] = time();
                db_perform('app_entity_' . $current_entity_id, $sql_data, 'update', "id='" . $item_id . "'");

                //insert choices values for fields with multiple values
                $choices_values->process($item_id);

                //autoupdate all field types
                fields_types::update_items_fields($current_entity_id, $item_id);

                if (is_ext_installed()) {
                    //run actions after item update
                    $processes = new processes($current_entity_id);
                    $processes->run_after_update($item_id);
                }
            } else {
                $sql_data['date_added'] = time();
                $sql_data['created_by'] = $app_user['id'];
                $sql_data['parent_item_id'] = $this->items_id;
                db_perform('app_entity_' . $current_entity_id, $sql_data);
                $item_id = db_insert_id();

                //insert choices values for fields with multiple values
                $choices_values->process($item_id);

                //autoupdate all field types
                fields_types::update_items_fields($current_entity_id, $item_id);

                items::send_new_item_nofitication($current_entity_id, $item_id);

                if (is_ext_installed()) {
                    //subscribe
                    $modules = new modules('mailing');
                    $mailing = new mailing($current_entity_id, $item_id);
                    $mailing->subscribe();

                    //run actions after item insert
                    $processes = new processes($current_entity_id);
                    $processes->run_after_insert($item_id);
                }
            }

            $form_items_list[] = $item_id;
        }

        //delete items
        if (isset($app_subentity_form_items_deleted[$this->field_id]) and is_array(
                $app_subentity_form_items_deleted[$this->field_id]
            ) and count($app_subentity_form_items_deleted[$this->field_id])) {
            //print_rr($app_subentity_form_items_deleted[$this->field_id]);
            //exit();

            $items_query = db_query(
                "select id from app_entity_{$current_entity_id} where parent_item_id={$this->items_id}  and id in (" . implode(
                    ',',
                    $app_subentity_form_items_deleted[$this->field_id]
                ) . ")"
            );
            while ($items = db_fetch_array($items_query)) {
                items::delete($current_entity_id, $items['id']);
            }
        }
    }

    public function render_items()
    {
        global $app_subentity_form_items;

        switch ($this->cfg->get('fields_display')) {
            case 'column':
            case 'row':
                return $this->render_items_list();
                break;
            case 'window':

                if (!$this->items_id) {
                    return ['rows_count' => 0, 'html' => ''];
                }

                //reset items
                $app_subentity_form_items[$this->field_id] = [];

                $items_query = db_query(
                    "select * " . fieldtype_input_encrypted::prepare_query_select(
                        $this->cfg->get('entity_id')
                    ) . " from app_entity_" . $this->cfg->get('entity_id') . " where parent_item_id=" . $this->items_id
                );
                while ($items = db_fetch_array($items_query)) {
                    $fields = [];
                    foreach ($items as $field_id => $field_value) {
                        if (strstr($field_id, 'field_')) {
                            if (is_array($field_value)) {
                                $field_value = implode(',', $field_value);
                            }

                            $fields[str_replace('field_', '', $field_id)] = $field_value;
                        }
                    }

                    $app_subentity_form_items[$this->field_id][$items['id']] = $fields;
                }

                //print_rr($app_subentity_form_items);

                return $this->render_items_listing_preview();
                break;
        }
    }

    public function render_items_list()
    {
        if (!$this->items_id) {
            return ['rows_count' => 0, 'html' => ''];
        }

        $rows_count = 0;
        $html = '';
        $items_query = db_query(
            "select id from app_entity_" . $this->cfg->get('entity_id') . " where parent_item_id=" . $this->items_id
        );
        while ($items = db_fetch_array($items_query)) {
            $rows_count++;
            $html .= $this->render_form($rows_count, $items['id']);
        }

        return ['rows_count' => $rows_count, 'html' => $html];
    }

    public function render_items_listing_preview()
    {
        global $app_subentity_form_items;

        if (!count($app_subentity_form_items)) {
            return ['rows_count' => 0, 'html' => ''];
        }

        switch ($this->cfg->get('listing_type')) {
            case 'table':
                return $this->render_items_listing_table();
                break;
            case 'list':
                return $this->render_items_listing_list();
                break;
        }
    }

    public function get_listing_fields()
    {
        $fields_in_listing = (is_array($this->cfg->get('fields_in_listing')) ? implode(
            ',',
            $this->cfg->get('fields_in_listing')
        ) : '');

        if (!strlen($fields_in_listing)) {
            return [];
        }

        $listing_fields = [];

        $fields_query = db_query(
            "select * from app_fields where id in (" . $fields_in_listing . ") and  entities_id='" . db_input(
                $this->cfg->get('entity_id')
            ) . "' order by field(id," . $fields_in_listing . ")"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $listing_fields[] = $fields;
        }

        return $listing_fields;
    }

    public function render_items_listing_table()
    {
        global $app_subentity_form_items, $app_path, $app_items_form_name;

        $listing_fields = $this->get_listing_fields();

        if (!count($listing_fields) or !count($app_subentity_form_items[$this->field_id])) {
            return ['rows_count' => 0, 'html' => ''];
        }

        $column_width = [];
        foreach (explode(',', $this->cfg->get('column_width')) as $v) {
            $column_width[] = (int)$v;
        }

        $html = '
            <table class="table table-striped table-bordered table-hover subentity-form-table">
                <thead>
                    <tr>
            ';

        foreach ($listing_fields as $col => $field) {
            $html .= '<th ' . (isset($column_width[$col]) ? 'style="width: ' . $column_width[$col] . '%"' : '') . '>' . $field['name'] . '</th>';
        }

        $html .= '
                <th style="width: 65px;"></th>
                </tr>
            </thead>
            <tbody>
            ';

        //print_rr($app_subentity_form_items);
        //print_rr($listing_fields);

        foreach ($app_subentity_form_items[$this->field_id] as $row => $item) {
            $html .= '<tr id="itemrow_' . $row . '" class="suentity-form-row-' . $this->field_id . '">';

            foreach ($listing_fields as $field) {
                if (!isset($item[$field['id']])) {
                    $html .= '<td></td>';
                    continue;
                }

                $output_options = [
                    'class' => $field['type'],
                    'value' => self::prepare_item_value_by_field_type($field, $item[$field['id']]),
                    'field' => $field,
                    'item' => [],
                    'is_export' => true,
                    'is_print' => true,
                    'reports_id' => 0,
                    'path' => '',
                    'path_info' => []
                ];

                $html .= '<td id="subentity-form-cell-' . $field['id'] . '">' . fields_types::output(
                        $output_options
                    ) . '</td>';
            }

            $url_params = 'is_submodal=true&redirect_to=subentity_form_' . $this->entities_id . '_' . $this->field_id . '_' . $row . '&entities_id=' . $this->entities_id . '&fields_id=' . $this->field_id . '&current_entity_id=' . $this->cfg->get(
                    'entity_id'
                ) . '&path=' . $this->cfg->get('entity_id') . '&form_name=' . $app_items_form_name;
            $submodal_url = url_for('subentity/form', $url_params);
            $edit_button = '<button type="button" class="btn btn-default btn-xs purple btn-submodal-open" data-parent-entity-item-id="" data-field-id="" data-submodal-url="' . $submodal_url . '"><i class="fa fa-edit"></i></button>';

            $html .= '<td style="white-space:nowrap">' . $edit_button . ' <button onClick="subentity_form' . $this->field_id . '_itemrow_remove(\'' . $row . '\')"  type="button" class="btn btn-default btn-xs purple" title="' . addslashes(
                    \K::f3()->TEXT_DELETE
                ) . '"><i class="fa fa-times" aria-hidden="true"></i></button></td>';

            $html .= '</tr>';
        }

        $html .= '            
            </tbody>
            </table>
            ';

        return ['rows_count' => 0, 'html' => $html];
    }

    public function render_items_listing_list()
    {
        global $app_subentity_form_items, $app_path, $app_items_form_name;

        $listing_fields = $this->get_listing_fields();

        if (!count($listing_fields)) {
            ['rows_count' => 0, 'html' => ''];
        }

        $column_width = [];
        foreach (explode(',', $this->cfg->get('column_width')) as $v) {
            $column_width[] = (int)$v;
        }

        //print_rr($app_subentity_form_items);

        $html = '';

        foreach ($app_subentity_form_items[$this->field_id] as $row => $item) {
            $html .= '
                <div id="itemrow_' . $row . '" class="suentity-form-row-' . $this->field_id . '">
                    <div class="item-panel">                    
                        <div class="row">';

            $url_params = 'is_submodal=true&redirect_to=subentity_form_' . $this->entities_id . '_' . $this->field_id . '_' . $row . '&entities_id=' . $this->entities_id . '&fields_id=' . $this->field_id . '&current_entity_id=' . $this->cfg->get(
                    'entity_id'
                ) . '&path=' . $this->cfg->get('entity_id') . '&form_name=' . $app_items_form_name;
            $submodal_url = url_for('subentity/form', $url_params);
            $edit_button = '<button type="button" class="btn btn-default btn-xs purple btn-submodal-open" data-parent-entity-item-id="" data-field-id="" data-submodal-url="' . $submodal_url . '"><i class="fa fa-edit"></i></button>';

            $html .= '<div class="item-panel-action-btn">' . $edit_button . ' <button onClick="subentity_form' . $this->field_id . '_itemrow_remove(\'' . $row . '\')"  type="button" class="btn btn-default btn-xs purple" title="' . addslashes(
                    \K::f3()->TEXT_DELETE
                ) . '"><i class="fa fa-times" aria-hidden="true"></i></button></div>';

            foreach ($listing_fields as $col => $field) {
                if (isset($item[$field['id']])) {
                    $output_options = [
                        'class' => $field['type'],
                        'value' => self::prepare_item_value_by_field_type($field, $item[$field['id']]),
                        'field' => $field,
                        'item' => [],
                        'is_export' => true,
                        'is_print' => true,
                        'reports_id' => 0,
                        'path' => '',
                        'path_info' => []
                    ];

                    $output_value = fields_types::output($output_options);
                } else {
                    $output_value = '';
                }

                $html .= '
                    <div class="col-md-3" ' . (isset($column_width[$col]) ? 'style="width: ' . $column_width[$col] . '%"' : '') . '>  
                        <span class="item-panel-heading">' . $field['name'] . ': </span>' . $output_value . '
                    </div>';
            }

            $html .= '
                        </div>
                    </div>
                </div>';
        }

        return ['rows_count' => 0, 'html' => $html];
    }

    public static function prepare_item_value_by_field_type($field, $value)
    {
        if (is_array($value)) {
            $value = implode(',', $value);
        }

        switch ($field['type']) {
            case 'fieldtype_input_date':
            case 'fieldtype_input_datetime':
                if (!is_numeric($value)) {
                    $value = (int)get_date_timestamp($value);
                }
                break;

            case 'fieldtype_time':
                if (!is_numeric($value)) {
                    $time = new fieldtype_time;
                    $value = $time->process([
                        'value' => $value,
                    ]);
                }
                break;
        }

        return $value;
    }
}