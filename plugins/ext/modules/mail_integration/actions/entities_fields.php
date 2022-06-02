<?php

$accounts_entities_query = db_query(
    "select me.*, ma.name as server_name,e.name as entities_name from app_ext_mail_accounts_entities me left join app_ext_mail_accounts ma on me.accounts_id=ma.id left join app_entities e on me.entities_id=e.id where  me.id='" . _get::int(
        'account_entities_id'
    ) . "' order by id"
);
if (!$accounts_entities = db_fetch_array($accounts_entities_query)) {
    redirect_to('ext/mail_integration/entities');
}

$filters_id = (isset($_GET['filters_id']) ? _get::int('filters_id') : 0);

switch ($app_module_action) {
    case 'render_template_field':

        if ($_POST['fields_id'] > 0) {
            $fields_info = db_find('app_fields', $_POST['fields_id']);
            $fields_info_cfg = new fields_types_cfg($fields_info['configuration']);

            if (isset($_POST['id'])) {
                $obj = db_find('app_ext_mail_accounts_entities_fields', $_POST['id']);
                $value = ['field_' . $fields_info['id'] => $obj['value']];
            } else {
                $value = ['field_' . $fields_info['id'] => ''];
            }

            $params = [
                'form' => 'comment',
                'parent_entity_item_id' => 0,
                'is_new_item' => true,
            ];

            //handle copy value for users field or doropdown if uses global list
            if (in_array(
                    $fields_info['type'],
                    ['fieldtype_users', 'fieldtype_users_ajax', 'fieldtype_input_masked', 'fieldtype_input_email']
                ) or (in_array($fields_info['type'], ['fieldtype_dropdown']) and $fields_info_cfg->get(
                        'use_global_list'
                    ) > 0)) {
                if (strstr($obj['value'], '[')) {
                    $field_value = ['field_' . $fields_info['id'] => ''];
                    $extra_value = $obj['value'];
                } else {
                    $field_value = $value;
                    $extra_value = '';
                }

                $html = fields_types::render($fields_info['type'], $fields_info, $field_value, $params);
            } elseif (in_array($fields_info['type'], ['fieldtype_input_date', 'fieldtype_input_datetime'])) {
                if (strlen($obj['value']) >= 10) {
                    $field_value = $value;
                    $extra_value = '';
                } else {
                    $field_value = ['field_' . $fields_info['id'] => ''];
                    $extra_value = $obj['value'];
                }

                $html = fields_types::render($fields_info['type'], $fields_info, $field_value, $params);

                $html .= TEXT_DAY . input_tag(
                        'fields_extra[' . $fields_info['id'] . ']',
                        $extra_value,
                        ['class' => 'form-control input-small']
                    ) . tooltip_text(TEXT_EXT_DATE_FIELD_ALLOWED_VALUES . '<br>' . TEXT_EXT_SPACE_TO_RESET);
            } elseif (in_array($fields_info['type'], ['fieldtype_dropdown_multiple'])) {
                $params['form'] = '';
                $html = fields_types::render($fields_info['type'], $fields_info, $value, $params);
            } else {
                $html = fields_types::render($fields_info['type'], $fields_info, $value, $params);
            }

            $html .= '
            <script>
              $(".field_' . $fields_info['id'] . '").removeClass("required").removeClass("number")
            </script>
          ';

            echo $html;
        }

        exit();

        break;

    case 'save':
        $field = db_find('app_fields', $_POST['fields_id']);


        $value = (isset($_POST['fields'][$field['id']]) ? $_POST['fields'][$field['id']] : '');

        $extra_value = (isset($_POST['fields_extra'][$field['id']]) ? $_POST['fields_extra'][$field['id']] : '');

        if (strlen($extra_value)) {
            $value = $extra_value;
        } else {
            //prepare process options
            $process_options = [
                'class' => $field['type'],
                'value' => $value,
                'field' => $field,
                'is_new_item' => true,
            ];

            $value = fields_types::process($process_options);
        }

        $sql_data = [
            'account_entities_id' => $accounts_entities['id'],
            'filters_id' => $filters_id,
            'fields_id' => $field['id'],
            'value' => $value,
        ];

        if (isset($_GET['id'])) {
            db_perform(
                'app_ext_mail_accounts_entities_fields',
                $sql_data,
                'update',
                "id='" . db_input($_GET['id']) . "'"
            );
        } else {
            db_perform('app_ext_mail_accounts_entities_fields', $sql_data);
        }


        redirect_to(
            'ext/mail_integration/entities_fields',
            'account_entities_id=' . $accounts_entities['id'] . ($filters_id > 0 ? '&filters_id=' . $filters_id : '')
        );
        break;

    case 'delete':
        if (isset($_GET['id'])) {
            db_query("delete from app_ext_mail_accounts_entities_fields where id='" . db_input($_GET['id']) . "'");

            redirect_to(
                'ext/mail_integration/entities_fields',
                'account_entities_id=' . $accounts_entities['id'] . ($filters_id > 0 ? '&filters_id=' . $filters_id : '')
            );
        }
        break;
}
