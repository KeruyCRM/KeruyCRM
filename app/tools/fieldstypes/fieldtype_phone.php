<?php

namespace Tools\FieldsTypes;

class Fieldtype_phone
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_PHONE];
    }

    public function get_configuration()
    {
        self::prepare_procedure();

        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => [
                'input-small' => \K::$fw->TEXT_INPUT_SMALL,
                'input-medium' => \K::$fw->TEXT_INPUT_MEDIUM,
                'input-large' => \K::$fw->TEXT_INPUT_LARGE,
                'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE
            ],
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_INPUT_FIELD_MASK,
            'name' => 'mask',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_INPUT_FIELD_PHONE_MASK_TIP,
            'params' => ['class' => 'form-control']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => fields_types::get_is_unique_choices(_POST('entities_id')),
            'tooltip_icon' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];
        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        if (\Helpers\App::is_ext_installed()) {
            $modules = new modules('telephony');
            $choices = $modules->get_active_modules();

            $cfg[\K::$fw->TEXT_EXT_TELEPHONY_MODULE][] = [
                'title' => \K::$fw->TEXT_EXT_TELEPHONY_MODULE,
                'name' => 'telephony_module',
                'type' => 'dropdown',
                'choices' => ['' => ''] + $choices,
                'tooltip_icon' => \K::$fw->TEXT_EXT_FIELDTYPE_PHONE_TELEPHONY_MODULE_INFO,
                'params' => ['class' => 'form-control input-large']
            ];

            $modules = new modules('sms');
            $choices = $modules->get_active_modules();

            $cfg[\K::$fw->TEXT_EXT_TELEPHONY_MODULE][] = [
                'title' => \K::$fw->TEXT_EXT_SMS_MODULE,
                'name' => 'sms_module',
                'type' => 'dropdown',
                'choices' => ['' => ''] + $choices,
                'tooltip_icon' => \K::$fw->TEXT_EXT_FIELDTYPE_PHONE_SMS_MODULE_INFO,
                'params' => ['class' => 'form-control input-large']
            ];

            $cfg[\K::$fw->TEXT_EXT_TELEPHONY_MODULE][] = [
                'title' => \K::$fw->TEXT_EXT_SHOW_CALL_SMS_HISTORY,
                'name' => 'show_history',
                'type' => 'checkbox'
            ];
        }

        return $cfg;
    }

    public static function prepare_procedure()
    {
        $sql = "
CREATE FUNCTION  `keruycrm_regex_replace`(pattern VARCHAR(1000),replacement VARCHAR(1000),original VARCHAR(1000))
RETURNS VARCHAR(1000)
DETERMINISTIC
BEGIN 
 DECLARE temp VARCHAR(1000); 
 DECLARE ch VARCHAR(1); 
 DECLARE i INT;
 SET i = 1;
 SET temp = '';
 IF original REGEXP pattern THEN 
  loop_label: LOOP 
   IF i>CHAR_LENGTH(original) THEN
    LEAVE loop_label;  
   END IF;
   SET ch = SUBSTRING(original,i,1);
   IF NOT ch REGEXP pattern THEN
    SET temp = CONCAT(temp,ch);
   ELSE
    SET temp = CONCAT(temp,replacement);
   END IF;
   SET i=i+1;
  END LOOP;
 ELSE
  SET temp = original;
 END IF;
 RETURN temp;
END";

        $is_function = false;
        //$check_query = db_query("SHOW FUNCTION STATUS WHERE Db = '" . \K::$fw->DB_name . "'");
        $check_query = \K::model()->db_query_exec(
            'SHOW FUNCTION STATUS WHERE Db = ' . \K::model()->quote(\K::$fw->DB_name)
        );
        //while ($check = db_fetch_array($check_query)) {
        foreach ($check_query as $check) {
            if ($check['Name'] == 'keruycrm_regex_replace') {
                $is_function = true;
            }
        }

        if (!$is_function) {
            \K::model()->db_query_exec($sql);
        }
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_required'] == 1 ? ' required' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : ''),
        ];

        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        $script = '';

        if (strlen($cfg->get('mask')) > 0) {
            $script = '
        <script>
          jQuery(function($){
             $(".field_' . $field['id'] . '").mask("' . $cfg->get('mask') . '");
          });
        </script>
      ';
        }

        return input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes) . $script;
    }

    public function process($options)
    {
        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        //return non-formated value if export
        if (isset($options['is_export'])) {
            return $options['value'];
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $phone_number = $options['value'];

        if (!strlen($phone_number)) {
            return '';
        }

        if (strlen($cfg->get('telephony_module'))) {
            $module_info_query = db_query(
                "select * from app_ext_modules where id='" . $cfg->get(
                    'telephony_module'
                ) . "' and type='telephony' and is_active=1"
            );
            if ($module_info = db_fetch_array($module_info_query)) {
                modules::include_module($module_info, 'telephony');

                $module = new $module_info['module'];
                $phone_number = $module->prepare_url($module_info['id'], $phone_number, $options);
            }
        }

        if (strlen($cfg->get('sms_module'))) {
            $module_info_query = db_query(
                "select * from app_ext_modules where id='" . $cfg->get(
                    'sms_module'
                ) . "' and type='sms'  and is_active=1"
            );
            if ($module_info = db_fetch_array($module_info_query)) {
                $phone_number .= '&nbsp;&nbsp;<a title="' . \K::$fw->TEXT_EXT_SMS . '" href="javascript: open_dialog(\'' . url_for(
                        'items/send_sms',
                        'path=' . $options['path'] . '&module_id=' . $module_info['id'] . '&field_id=' . $options['field']['id'] . '&item_id=' . $options['item']['id']
                    ) . '\')"><i class="fa fa-commenting-o" aria-hidden="true"></i></a>';
            }
        }

        if ($cfg->get('show_history')) {
            $phone_number .= '&nbsp;&nbsp;<a title="' . \K::$fw->TEXT_EXT_HISTORY . '" href="javascript: open_dialog(\'' . url_for(
                    'items/call_history',
                    'path=' . $options['path'] . '&phone=' . preg_replace('/\D/', '', $options['value'])
                ) . '\')"><i class="fa fa-history" aria-hidden="true"></i></a>';
        }

        return $phone_number;
    }
}