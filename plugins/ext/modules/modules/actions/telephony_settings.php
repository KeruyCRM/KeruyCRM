<?php

switch ($app_module_action) {
    case 'set_incoming_call_field':

        if (isset($_POST['incoming_call_field'])) {
            configuration::set('CFG_INCOMING_CALL_FIELD', implode(',', $_POST['incoming_call_field']));
        }

        exit();
        break;
    case 'get_entities_fields':

        $entities_id = (int)$_POST['entities_id'];

        configuration::set('CFG_INCOMING_CALL_ENTITY', $entities_id);

        if ($entities_id == 0) {
            die('');
        }

        $html = '';

        $choices = [];
        $fields_query = db_query(
            "select * from app_fields where type in ('fieldtype_phone') and entities_id='" . db_input(
                $entities_id
            ) . "'"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }

        $html .= '
         <div class="form-group">
          	<label class="col-md-3 control-label" for="allowed_groups">' . TEXT_PHONE . '</label>
            <div class="col-md-9">	
          	   ' . select_tag(
                'incoming_call_field',
                $choices,
                CFG_INCOMING_CALL_FIELD,
                [
                    'class' => 'form-control input-large required chosen-select',
                    'multiple' => 'multiple',
                    'onChange' => 'set_incoming_call_field()'
                ]
            ) . '
               ' . tooltip_text(TEXT_EXT_INCOMING_CALL_PHONE_INFO) . '
            </div>			
          </div>               		
        ';


        echo $html;

        exit();
        break;
}		