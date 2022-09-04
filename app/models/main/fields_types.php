<?php

namespace Models\Main;

class Fields_types
{
    public static function get_reserved_types()
    {
        return [
            'fieldtype_action',
            'fieldtype_id',
            'fieldtype_date_added',
            'fieldtype_date_updated',
            'fieldtype_created_by',
            'fieldtype_parent_item_id',
        ];
    }

    public static function get_reserved_data_types()
    {
        return [
            'fieldtype_id',
            'fieldtype_date_added',
            'fieldtype_created_by',
            'fieldtype_parent_item_id',
            'fieldtype_date_updated',
        ];
    }

    public static function get_users_types()
    {
        return [
            'fieldtype_user_status',
            'fieldtype_user_accessgroups',
            'fieldtype_user_firstname',
            'fieldtype_user_lastname',
            'fieldtype_user_email',
            'fieldtype_user_photo',
            'fieldtype_user_username',
            'fieldtype_user_language',
            'fieldtype_user_skin',
            'fieldtype_user_last_login_date',
        ];
    }

    public static function get_attachments_types()
    {
        return [
            'fieldtype_input_file',
            'fieldtype_attachments',
            'fieldtype_image',
            'fieldtype_image_ajax',
        ];
    }

    public static function get_numeric_types()
    {
        return [
            'fieldtype_input_numeric',
            'fieldtype_input_numeric_comments',
            'fieldtype_formula',
            'fieldtype_js_formula',
            'fieldtype_mysql_query',
            'fieldtype_ajax_request',
            'fieldtype_nested_calculations',
        ];
    }

    public static function get_types_for_filters()
    {
        return [
            'fieldtype_checkboxes',
            'fieldtype_radioboxes',
            'fieldtype_created_by',
            'fieldtype_date_added',
            'fieldtype_date_updated',
            'fieldtype_boolean',
            'fieldtype_boolean_checkbox',
            'fieldtype_dropdown',
            'fieldtype_progress',
            'fieldtype_autostatus',
            'fieldtype_dropdown_multiple',
            'fieldtype_dropdown_multilevel',
            'fieldtype_formula',
            'fieldtype_js_formula',
            'fieldtype_mysql_query',
            'fieldtype_input_date',
            'fieldtype_input_datetime',
            'fieldtype_input_numeric',
            'fieldtype_input_numeric_comments',
            'fieldtype_grouped_users',
            'fieldtype_users',
            'fieldtype_users_ajax',
            'fieldtype_entity',
            'fieldtype_related_records',
            'fieldtype_image_map',
            'fieldtype_hours_difference',
            'fieldtype_days_difference',
            'fieldtype_years_difference',
            'fieldtype_months_difference',
            'fieldtype_auto_increment',
            'fieldtype_tags',
            'fieldtype_entity_ajax',
            'fieldtype_user_roles',
            'fieldtype_entity_multilevel',
            'fieldtype_users_approve',
            'fieldtype_dynamic_date',
            'fieldtype_access_group',
            'fieldtype_stages',
            'fieldtype_php_code',
            'fieldtype_jalali_calendar',
            'fieldtype_nested_calculations',
            'fieldtype_color',
        ];
    }

    public static function get_types_for_search()
    {
        return [
            'fieldtype_user_firstname',
            'fieldtype_user_lastname',
            'fieldtype_user_email',
            'fieldtype_attachments',
            'fieldtype_auto_increment',
            'fieldtype_barcode',
            'fieldtype_id',
            'fieldtype_image',
            'fieldtype_image_ajax',
            'fieldtype_input_email',
            'fieldtype_input_file',
            'fieldtype_input_masked',
            'fieldtype_input_url',
            'fieldtype_input_vpic',
            'fieldtype_input',
            'fieldtype_phone',
            'fieldtype_random_value',
            'fieldtype_text_pattern_static',
            'fieldtype_textarea_wysiwyg',
            'fieldtype_textarea',
            'fieldtype_todo_list',
            'fieldtype_input_encrypted',
            'fieldtype_input_protected',
            'fieldtype_textarea_encrypted',
            'fieldtype_input_ip',
            'fieldtype_input_dynamic_mask',
            'fieldtype_tags',
        ];
    }

    public static function get_types_excluded_in_form()
    {
        return [
            'fieldtype_related_records',
            'fieldtype_formula',
            'fieldtype_mysql_query',
            'fieldtype_text_pattern',
            'fieldtype_qrcode',
            'fieldtype_parent_value',
            'fieldtype_days_difference',
            'fieldtype_hours_difference',
            'fieldtype_years_difference',
            'fieldtype_months_difference',
            'fieldtype_text_pattern_static',
            'fieldtype_user_last_login_date',
            'fieldtype_yandex_map',
            'fieldtype_google_map',
            'fieldtype_google_map_directions',
            'fieldtype_dynamic_date',
            'fieldtype_signature',
            'fieldtype_digital_signature',
            'fieldtype_items_by_query',
            'fieldtype_php_code',
            'fieldtype_process_button',
            'fieldtype_nested_calculations',
        ];
    }

    public static function get_types_excluded_in_sorting()
    {
        return [
            'fieldtype_action',
            'fieldtype_text_pattern',
            'fieldtype_qrcode',
            'fieldtype_mapbbcode',
            'fieldtype_parent_value',
            'fieldtype_google_map',
            'fieldtype_google_map_directions',
            'fieldtype_items_by_query',
        ];
    }

    public static function get_types_excluded_in_email()
    {
        return [
            'fieldtype_image_map',
            'fieldtype_image_map_nested',
            'fieldtype_mind_map',
            'fieldtype_mapbbcode',
            'fieldtype_google_map',
            'fieldtype_google_map_directions',
        ];
    }

    public static function skip_import_field_types()
    {
        //skip reserved
        $skip_fields = self::get_reserved_types_list();

        //skip not allowed
        $skip_fields .= ",'fieldtype_nested_calculations','fieldtype_access_group','fieldtype_user_roles','fieldtype_users_approve','fieldtype_autostatus','fieldtype_google_map','fieldtype_mysql_query','fieldtype_formula','fieldtype_days_difference','fieldtype_hours_difference','fieldtype_users','fieldtype_input_numeric_comments','fieldtype_input_file','fieldtype_attachments','fieldtype_related_records','fieldtype_parent_value'";

        //skip users fields
        $skip_fields .= ",'fieldtype_user_status','fieldtype_user_accessgroups','fieldtype_user_photo','fieldtype_user_language','fieldtype_user_skin'";

        return $skip_fields;
    }

    public static function get_types_for_filters_list()
    {
        return \K::model()->quoteToString(self::get_types_for_filters());
        //return "'" . implode("','", self::get_types_for_filters()) . "'";
    }

    public static function get_users_types_list()
    {
        return \K::model()->quoteToString(self::get_users_types());
        //return "'" . implode("','", self::get_users_types()) . "'";
    }

    public static function get_reserved_types_list()
    {
        return \K::model()->quoteToString(self::get_reserved_types());
        //return "'" . implode("','", self::get_reserved_types()) . "'";
    }

    public static function get_attachments_types_list()
    {
        return \K::model()->quoteToString(self::get_attachments_types());
        //return "'" . implode("','", self::get_attachments_types()) . "'";
    }

    public static function get_reserved_data_types_list()
    {
        return \K::model()->quoteToString(self::get_reserved_data_types());
        //return "'" . implode("','", self::get_reserved_data_types()) . "'";
    }

    public static function get_type_list_excluded_in_form()
    {
        return \K::model()->quoteToString(self::get_types_excluded_in_form()) . ',' . self::get_reserved_types_list();
        /*return "'" . implode(
                "','",
                self::get_types_excluded_in_form()
            ) . "'," . self::get_reserved_types_list();*/
    }

    public static function get_type_list_excluded_in_sorting()
    {
        return \K::model()->quoteToString(self::get_types_excluded_in_sorting());
        //return "'" . implode("','", self::get_types_excluded_in_sorting()) . "'";
    }

    public static function get_types_for_search_list()
    {
        return \K::model()->quoteToString(self::get_types_for_search());
        //return "'" . implode("','", self::get_types_for_search()) . "'";
    }

    public static function get_reserved_filed_name_by_type($type)
    {
        $field_name = '';

        switch ($type) {
            case 'fieldtype_id':
                $field_name = 'id';
                break;
            case 'fieldtype_date_added':
                $field_name = 'date_added';
                break;
            case 'fieldtype_created_by':
                $field_name = 'created_by';
                break;
        }

        return $field_name;
    }

    public static function get_tooltip($fieldtype)
    {
        $tooltip = '';

        switch ($fieldtype) {
            case 'fieldtype_input':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_TOOLTIP;
                break;
            case 'fieldtype_input_numeric':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_NUMERIC_TOOLTIP;
                break;
            case 'fieldtype_input_numeric_comments':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_NUMERIC_COMMENTS_TOOLTIP;
                break;
            case 'fieldtype_input_url':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_URL_TOOLTIP;
                break;
            case 'fieldtype_input_date':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_DATE_TOOLTIP;
                break;
            case 'fieldtype_input_datetime':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_DATETIME_TOOLTIP;
                break;
            case 'fieldtype_input_file':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_FILE_TOOLTIP;
                break;
            case 'fieldtype_attachments':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_ATTACHMENTS_TOOLTIP;
                break;
            case 'fieldtype_image':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_IMAGE_TOOLTIP;
                break;
            case 'fieldtype_textarea':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_TEXTAREA_TOOLTIP;
                break;
            case 'fieldtype_textarea_wysiwyg':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_TEXTAREA_WYSIWYG_TOOLTIP;
                break;
            case 'fieldtype_text_pattern':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_TEXT_PATTERN_TOOLTIP;
                break;
            case 'fieldtype_boolean_checkbox':
            case 'fieldtype_boolean':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_BOOLEAN_TOOLTIP;
                break;
            case 'fieldtype_dropdown':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_DROPDOWN_TOOLTIP;
                break;
            case 'fieldtype_dropdown_multiple':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TOOLTIP;
                break;
            case 'fieldtype_dropdown_multilevel':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_TOOLTIP;
                break;
            case 'fieldtype_checkboxes':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_CHECKBOXES_TOOLTIP;
                break;
            case 'fieldtype_radioboxes':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_RADIOBOXES_TOOLTIP;
                break;
            case 'fieldtype_formula':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_FORMULA_TOOLTIP;
                break;
            case 'fieldtype_users':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_USERS_TOOLTIP;
                break;
            case 'fieldtype_grouped_users':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_GROUPEDUSERS_TOOLTIP;
                break;
            case 'fieldtype_entity':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_ENTITY_TOOLTIP;
                break;
            case 'fieldtype_progress':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_PROGRESS_TOOLTIP;
                break;
            case 'fieldtype_related_records':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_RELATED_RECORDS_TOOLTIP . \K::$fw->TEXT_FIELDTYPE_RELATED_RECORDS_TOOLTIP_EXTRA;
                break;
            case 'fieldtype_input_masked':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_MASKED_TOOLTIP;
                break;
            case 'fieldtype_input_vpic':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_VPIC_TOOLTIP;
                break;
            case 'fieldtype_mapbbcode':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_MAPBBCODE_TOOLTIP;
                break;
            case 'fieldtype_barcode':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_BARCODE_TOOLTIP;
                break;
            case 'fieldtype_qrcode':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_QRCODE_TOOLTIP;
                break;
            case 'fieldtype_input_email':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_EMAIL_TOOLTIP;
                break;
            case 'fieldtype_section':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_SECTION_TOOLTIP;
                break;
            case 'fieldtype_random_value':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_RANDOM_VALUE_TOOLTIP . ' ' . \K::$fw->TEXT_FIELDTYPE_RANDOM_VALUE_UNIQUE_TOOLTIP;
                break;
            case 'fieldtype_autostatus':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_AUTOSTATUS_TOOLTIP;
                break;
            case 'fieldtype_js_formula':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_JS_FORMULA_TOOLTIP;
                break;
            case 'fieldtype_todo_list':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_TODO_LIST_TOOLTIP;
                break;
            case 'fieldtype_parent_value':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_PARENT_VALUE_TOOLTIP;
                break;
            case 'fieldtype_mysql_query':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_MYSQL_QUERY_TOOLTIP;
                break;
            case 'fieldtype_image_map':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_IMAGE_MAP_TOOLTIP;
                break;
            case 'fieldtype_mind_map':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_MIND_MAP_TOOLTIP;
                break;
            case 'fieldtype_days_difference':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_DAYS_DIFFERENCE_TOOLTIP;
                break;
            case 'fieldtype_hours_difference':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_HOURS_DIFFERENCE_TOOLTIP;
                break;
            case 'fieldtype_auto_increment':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_AUTO_INCREMENT_TOOLTIP;
                break;
            case 'fieldtype_text_pattern_static':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_TEXT_PATTERN_STATIC_TOOLTIP;
                break;
            case 'fieldtype_years_difference':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_YEARS_DIFFERENCE_TOOLTIP;
                break;
            case 'fieldtype_phone':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_PHONE_TOOLTIP;
                break;
            case 'fieldtype_google_map':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_TOOLTIP;
                break;
            case 'fieldtype_input_protected':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_PROTECTED_TOOLTIP;
                break;
            case 'fieldtype_tags':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_TAGS_TOOLTIP;
                break;
            case 'fieldtype_entity_ajax':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_ENTITY_AJAX_TOOLTIP;
                break;
            case 'fieldtype_user_roles':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_USER_ROLES_TOOLTIP;
                break;
            case 'fieldtype_entity_multilevel':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_ENTITY_MULTILEVEL_TOOLTIP;
                break;
            case 'fieldtype_months_difference':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_MONTHS_DIFFERENCE_TOOLTIP;
                break;
            case 'fieldtype_users_approve':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_USERS_APPROVE_TOOLTIP;
                break;
            case 'fieldtype_google_map_directions':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_TOOLTIP;
                break;
            case 'fieldtype_dynamic_date':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_DYNAMIC_DATE_TOOLTIP;
                break;
            case 'fieldtype_access_group':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_ACCESS_GROUP_TOOLTIP;
                break;
            case 'fieldtype_signature':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_SIGNATURE_TOOLTIP;
                break;
            case 'fieldtype_stages':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_STAGES_TOOLTIP;
                break;
            case 'fieldtype_iframe':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_IFRAME_TOOLTIP;
                break;
            case 'fieldtype_time':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_TIME_TOOLTIP;
                break;
            case 'fieldtype_digital_signature':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_DIGITAL_SIGNATURE_TOOLTIP;
                break;
            case 'fieldtype_ajax_request':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_AJAX_REQUEST_TOOLTIP;
                break;
            case 'fieldtype_users_ajax':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_USERS_AJAX_TOOLTIP;
                break;
            case 'fieldtype_items_by_query':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_ITEMS_BY_QUERY_TOOLTIP;
                break;
            case 'fieldtype_php_code':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_PHP_CODE_TOOLTIP;
                break;
            case 'fieldtype_process_button':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_PROCESS_BUTTON_TOOLTIP;
                break;
            case 'fieldtype_video':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_VIDEO_TOOLTIP;
                break;
            case 'fieldtype_input_encrypted':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_ENCRYPTED_TOOLTIP;
                break;
            case 'fieldtype_textarea_encrypted':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_ENCRYPTED_TOOLTIP;
                break;
            case 'fieldtype_jalali_calendar':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_JALALI_CALENDAR_TOOLTIP;
                break;
            case 'fieldtype_subentity_form':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_SUBENTITY_FORM_TOOLTIP;
                break;
            case 'fieldtype_input_ip':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_IP_TOOLTIP;
                break;
            case 'fieldtype_input_dynamic_mask':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_TOOLTIP;
                break;
            case 'fieldtype_nested_calculations':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_NESTED_CALCULATIONS_TOOLTIP;
                break;
            case 'fieldtype_image_ajax':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_IMAGE_TOOLTIP;
                break;
            case 'fieldtype_color':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_COLOR_TOOLTIP;
                break;
            case 'fieldtype_image_map_nested':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_IMAGE_MAP_NESTED_TOOLTIP;
                break;
            case 'fieldtype_yandex_map':
                $tooltip = \K::$fw->TEXT_FIELDTYPE_YANDEX_MAP_TOOLTIP;
                break;
        }

        return $tooltip;
    }

    public static function get_choices()
    {
        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_INPUT_FIELDS] = [
            'fieldtype_input',
            'fieldtype_input_masked',
            'fieldtype_input_dynamic_mask',
            'fieldtype_input_protected',
            'fieldtype_input_encrypted',
            'fieldtype_input_url',
            'fieldtype_input_ip',
            'fieldtype_video',
            'fieldtype_iframe',
            'fieldtype_input_email',
            'fieldtype_phone',
        ];

        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_NUMERIC] = [
            'fieldtype_input_numeric',
            'fieldtype_input_numeric_comments',
            'fieldtype_formula',
            'fieldtype_js_formula',
            'fieldtype_mysql_query',
            'fieldtype_ajax_request',
            'fieldtype_php_code',
            'fieldtype_nested_calculations',
        ];

        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_DATES] = [
            'fieldtype_input_date',
            'fieldtype_input_datetime',
            'fieldtype_jalali_calendar',
            'fieldtype_time',
            'fieldtype_dynamic_date',
            'fieldtype_years_difference',
            'fieldtype_months_difference',
            'fieldtype_days_difference',
            'fieldtype_hours_difference',
        ];

        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_TEXT] = [
            'fieldtype_textarea',
            'fieldtype_textarea_wysiwyg',
            'fieldtype_textarea_encrypted',
            'fieldtype_text_pattern',
            'fieldtype_text_pattern_static',
        ];

        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_UPLOAD] = [
            'fieldtype_attachments',
            'fieldtype_input_file',
            'fieldtype_image',
            'fieldtype_image_ajax',
        ];

        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_LIST] = [
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_dropdown_multilevel',
            'fieldtype_tags',
            'fieldtype_checkboxes',
            'fieldtype_radioboxes',
            'fieldtype_boolean',
            'fieldtype_boolean_checkbox',
            'fieldtype_progress',
            'fieldtype_color',
        ];

        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_USERS] = [
            'fieldtype_users',
            'fieldtype_users_ajax',
            'fieldtype_grouped_users',
            'fieldtype_access_group',
            'fieldtype_user_roles',
            'fieldtype_users_approve',
            'fieldtype_digital_signature',
        ];

        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_ENTITY] = [
            'fieldtype_entity',
            'fieldtype_entity_ajax',
            'fieldtype_entity_multilevel',
            'fieldtype_related_records',
            'fieldtype_parent_value',
            'fieldtype_items_by_query',
            'fieldtype_subentity_form'
        ];

        $fieldtypes[\K::$fw->TEXT_MAPS] = [
            'fieldtype_mapbbcode',
            'fieldtype_yandex_map',
            'fieldtype_google_map',
            'fieldtype_google_map_directions',
            'fieldtype_image_map',
            'fieldtype_image_map_nested',
            'fieldtype_mind_map',
        ];

        $fieldtypes[\K::$fw->TEXT_FIELDS_TYPES_GROUP_SPECIAL_FIELDS] = [
            'fieldtype_section',
            'fieldtype_random_value',
            'fieldtype_auto_increment',
            'fieldtype_autostatus',
            'fieldtype_stages',
            'fieldtype_todo_list',
            'fieldtype_process_button',
            'fieldtype_input_vpic',
            'fieldtype_barcode',
            'fieldtype_qrcode',
            'fieldtype_signature',
        ];

        $choices = [];
        foreach ($fieldtypes as $group => $fields) {
            foreach ($fields as $class) {
                $class = '\\Tools\\Fieldstypes\\' . ucfirst($class);
                $fieldtype = new $class();

                $choices[$group][$class] = $fieldtype->options['title'];
            }
        }

        return $choices;
    }

    public static function get_title($class)
    {
        $class = '\\Tools\\Fieldstypes\\' . ucfirst($class);
        $fieldtype = new $class();

        return $fieldtype->options['title'];
    }

    public static function render_field_name($name, $class, $fields_id)
    {
        $class = '\\Tools\\Fieldstypes\\' . ucfirst($class);
        $fieldtype = new $class();

        if (!isset($fieldtype->options['has_choices'])) {
            $fieldtype->options['has_choices'] = false;
        }

        if ($fieldtype->options['has_choices']) {
            return '<a href="' . \Helpers\Urls::url_for(
                    'main/entities/fields_choices',
                    'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . $fields_id
                ) . '"><i class="fa fa-list"></i>&nbsp;' . $name . '</a>';
        } elseif (in_array($class, ['fieldtype_related_records', 'fieldtype_entity'])) {
            return '<a href="' . \Helpers\Urls::url_for(
                    'main/entities/fields_settings',
                    'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . $fields_id
                ) . '"><i class="fa fa-gear"></i>&nbsp;' . $name . '</a>';
        } elseif (in_array(
            $class,
            ['fieldtype_image_map_nested', 'fieldtype_entity_ajax', 'fieldtype_entity_multilevel']
        )) {
            return '<a href="' . \Helpers\Urls::url_for(
                    'main/entities/entityfield_filters',
                    'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . $fields_id
                ) . '"><i class="fa fa-gear"></i>&nbsp;' . $name . '</a>';
        } elseif (in_array($class, ['fieldtype_user_roles'])) {
            return '<a href="' . \Helpers\Urls::url_for(
                    'main/entities/user_roles',
                    'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . $fields_id
                ) . '"><i class="fa fa-gear"></i>&nbsp;' . $name . '</a>';
        } elseif (in_array($class, ['fieldtype_users_approve', 'fieldtype_signature', 'fieldtype_digital_signature'])) {
            return '<a href="' . \Helpers\Urls::url_for(
                    'main/entities/fields_filters',
                    'entities_id=' . \K::$fw->GET['entities_id'] . '&fields_id=' . $fields_id
                ) . '"><i class="fa fa-gear"></i>&nbsp;' . $name . '</a>';
        } else {
            return $name;
        }
    }

    public static function render_configuration($cfg, $id)
    {
        $html = '';
        $configuration = [];

        $obj = \K::model()->db_find('app_fields', $id);

        if (strlen($obj['configuration']) > 0) {
            $configuration = self::parse_configuration($obj['configuration']);
        }

        //print_r($configuration);;        
        //prepare tabs
        $tabs = [];
        $tabs_cfg = [];
        $count = 1;
        foreach ($cfg as $tab_name => $v) {
            if (!is_numeric($tab_name)) {
                $tab_id = 'field_cfg_tab_' . $count;
                $tabs[$tab_id] = $tab_name;
                $tabs_cfg[$tab_id] = $v;
            }

            $count++;
        }

        //display tabs if exist
        if (count($tabs)) {
            $html .= '<ul class="nav nav-tabs">';
            $count_tabs = 0;
            foreach ($tabs as $tab_id => $tab_name) {
                $html .= '<li class="' . ($count_tabs == 0 ? 'active' : '') . '"><a href="#' . $tab_id . '" id="' . $tab_id . '_link"  data-toggle="tab">' . $tab_name . '</a></li>';
                $count_tabs++;
            }
            $html .= '</ul>';
        } else {
            $tabs_cfg[] = $cfg;
        }

        $html .= '<div class="tab-content">';

        $count_tabs = 0;
        foreach ($tabs_cfg as $tab_id => $cfg) {
            //prepare tabs content if exist tabs
            if ($count_tabs == 0 and count($tabs)) {
                $html .= '<div class="tab-pane fade active in" id="' . $tab_id . '">';
            } elseif (count($tabs)) {
                $html .= '<div class="tab-pane fade" id="' . $tab_id . '">';
            } else {
                $html .= '<div>';
            }

            foreach ($cfg as $tab_name => $v) {
                //handle tabls
                if (!is_numeric($tab_name)) {
                    if (!in_array($tab_name, $tabs)) {
                        $tabs[] = $tab_name;
                    }
                }

                //handle default value
                if (isset($v['name'])) {
                    if (!isset($configuration[$v['name']]) and isset($v['default'])) {
                        $configuration[$v['name']] = $v['default'];
                    }
                }

                $field = '';
                switch ($v['type']) {
                    case 'dropdown':
                        if (isset($v['params']['class']) and strstr(
                                $v['params']['class'],
                                'chosen-sortable'
                            ) and isset($configuration[$v['name']])) {
                            $v['params']['chosen_order'] = (is_array($configuration[$v['name']]) ? implode(
                                ',',
                                $configuration[$v['name']]
                            ) : $configuration[$v['name']]);
                        }

                        $field = \Helpers\Html::select_tag(
                            'fields_configuration[' . $v['name'] . ']' . (isset($v['params']['multiple']) ? '[]' : ''),
                            $v['choices'],
                            ($configuration[$v['name']] ?? ''),
                            ($v['params'] ?? [])
                        );
                        break;
                    case 'checkbox':
                        $field = '<div class="checkbox-list"><label class="checkbox-inline">' . \Helpers\Html::input_checkbox_tag(
                                'fields_configuration[' . $v['name'] . ']',
                                1,
                                ['checked' => ($configuration[$v['name']] ?? false)]
                            ) . '</label></div>';
                        break;
                    case 'colorpicker':
                        $field = '
	              <div class="input-group input-small color colorpicker-default" data-color="' . ($configuration[$v['name']] ?? '#ff0000') . '" >
	          	   ' . \Helpers\Html::input_tag(
                                'fields_configuration[' . $v['name'] . ']',
                                ($configuration[$v['name']] ?? ''),
                                ['class' => 'form-control input-small']
                            ) . '
	                <span class="input-group-btn">
	          				<button class="btn btn-default" type="button">&nbsp;</button>
	          			</span>
	          		</div>
	            ';
                        break;
                    case 'input-with-colorpicker':
                        $field = '              
	              <div class="input-group input-with-colorpicker">
	                ' . \Helpers\Html::input_tag(
                                'fields_configuration[' . $v['name'] . ']',
                                ($configuration[$v['name']] ?? ''),
                                ['class' => 'form-control input-xsmall']
                            ) . '
	                <div class="input-group-btn">             
	                  <div class="input-group input-small color colorpicker-default" data-color="' . ($configuration[$v['name'] . '_color'] ?? '#ff0000') . '" >                                
	              	   ' . \Helpers\Html::input_tag(
                                'fields_configuration[' . $v['name'] . '_color]',
                                ($configuration[$v['name'] . '_color'] ?? ''),
                                ['class' => 'form-control input-small']
                            ) . '
	                    <span class="input-group-btn">
	              				<button class="btn btn-default" type="button">&nbsp;</button>
	              			</span>
	              		</div>                
	                </div>
	              </div>
	            ';
                        break;
                    case 'input':
                        $field = \Helpers\Html::input_tag(
                            'fields_configuration[' . $v['name'] . ']',
                            ($configuration[$v['name']] ?? ''),
                            ($v['params'] ?? [])
                        );
                        break;
                    case 'file':
                        $value = ($configuration[$v['name']] ?? '');
                        $field = 'delete_file[' . $v['name'];
                        $field .= \Helpers\Html::input_hidden_tag('fields_configuration[' . $v['name'] . ']', $value);
                        break;
                    case 'textarea':
                        $field = \Helpers\Html::textarea_tag(
                            'fields_configuration[' . $v['name'] . ']',
                            ($configuration[$v['name']] ?? ''),
                            ($v['params'] ?? [])
                        );
                        break;
                    case 'code':
                    case 'code_small':
                        $v['params']['style'] = "height:310px;";
                        $v['params']['class'] = $v['params']['class'] . ' is_codemirror';
                        $field = \Helpers\Html::textarea_tag(
                            'fields_configuration[' . $v['name'] . ']',
                            ($configuration[$v['name']] ?? ''),
                            ($v['params'] ?? [])
                        );
                        $field .= \Helpers\App::app_include_codemirror(
                            ['javascript', 'sql', 'php', 'clike', 'css', 'xml']
                        );

                        $mode = $v['params']['mode'] ?? 'javascript';

                        if ($mode == 'php') {
                            $mode = '{name: "php",startOpen: true}';
                        } else {
                            $mode = '"' . $mode . '"';
                        }

                        $field .= '
	                <script>
                         var myCodeMirror' . $v['name'] . ' = false
                             
	                 var ' . $v['name'] . '_code  = function () {                             
                            myCodeMirror' . $v['name'] . ' = CodeMirror.fromTextArea(document.getElementById("fields_configuration_' . $v['name'] . '"), {
                            mode: ' . $mode . ',
                            lineNumbers: true,       
                            autofocus:true,
                            lineWrapping: true,
                            matchBrackets: true,
                            extraKeys: {
                    		     "F11": function(cm) {
                    		       cm.setOption("fullScreen", !cm.getOption("fullScreen"));
                    		     },
                    		     "Esc": function(cm) {
                    		      if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
                    		    },                    		    
                    		  }   
                          })                          
    	                 }
                        
                        if($("#' . $tab_id . '").css("display")!="none")    
                        {                        
                            setTimeout(' . $v['name'] . '_code, 100);
                        }
                        else
                        {                                                 
                            $("#' . $tab_id . '_link").click(function(){
                                if(!$(this).hasClass("active-codemirror"))
                                {
                                    setTimeout(' . $v['name'] . '_code, 300);
                                    $(this).addClass("active-codemirror")
                                }		
                            })
                        }
	                </script>     
	                ';
                        break;
                }

                if ($v['type'] == 'hidden') {
                    $html .= \Helpers\Html::input_hidden_tag(
                        'fields_configuration[' . $v['name'] . ']',
                        ($configuration[$v['name']] ?? '')
                    );
                } elseif ($v['type'] == 'section') {
                    $html .= '<h3 class="form-section">' . $v['title'] . '</h3>' . ($v['html'] ?? '');
                } elseif ($v['type'] == 'ajax') {
                    $html .= '<div id="fields_types_ajax_configuration_' . $v['name'] . '"></div>' . $v['html'];
                } elseif ($v['type'] == 'html') {
                    $html .= $v['html'];
                } else {
                    $html .= '
	        
	        <div class="form-group from_group_' . $v['name'] . '" ' . ((isset($v['form_group']) and is_array(
                                $v['form_group']
                            )) ? \Helpers\Html::tag_attributes_to_html($v['form_group']) : '') . '>
	        	<label class="col-md-3 control-label" for="' . \Helpers\Html::generate_id_from_name(
                            'fields_configuration[' . $v['name'] . ']'
                        ) . '">' .
                        (isset($v['tooltip_icon']) ? \Helpers\App::tooltip_icon(
                            $v['tooltip_icon']
                        ) : '') . $v['title'] .
                        '</label>
	          <div class="col-md-' . (in_array($v['type'], ['code']) ? '12' : '9') . '">' .
                        $field .
                        (isset($v['tooltip']) ? \Helpers\App::tooltip_text($v['tooltip']) : '') . '
	          </div>			
	        </div>
	        ';
                }
            }

            $count_tabs++;

            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '
      <script>
        $(".input-masked").each(function(){
          $.mask.definitions["~"]="[,. *]";
          $(this).mask($(this).attr("data-mask"));
        })
      </script>
    ';

        return $html;
    }

    public static function prepare_configuration($v)
    {
        return \Helpers\App::app_json_encode($v);
    }

    public static function parse_configuration($v)
    {
        if (strlen($v) > 0) {
            return json_decode($v, true);
        } else {
            return [];
        }
    }

    public static function render($class, $field, $obj, $params = [])
    {
        $class = ucfirst($class);
        $className = "\\Tools\\Fieldstypes\\{$class}";

        $fieldtype = new $className();

        return $fieldtype->render($field, $obj, $params);
    }

    public static function process($options = [])
    {
        $class = '\\Tools\\Fieldstypes\\' . ucfirst($options['class']);
        $fieldtype = new $class();

        return $fieldtype->process($options);
    }

    public static function output($options = [])
    {
        $class = '\\Tools\\Fieldstypes\\' . ucfirst($options['class']);
        $fieldtype = new $class();

        return $fieldtype->output($options);
    }

    public static function reports_query($options = [])
    {
        $class = '\\Tools\\Fieldstypes\\' . ucfirst($options['class']);
        $fieldtype = new $class();

        if (method_exists($fieldtype, 'reports_query')) {
            return $fieldtype->reports_query($options);
        } else {
            return $options['sql_query'];
        }
    }

    public static function get_option($class, $key, $default = '')
    {
        if (!strlen($class)) {
            return '';
        }

        $class = ucfirst($class);
        $className = "\\Tools\\Fieldstypes\\{$class}";

        $fieldtype = new $className();

        if ($key == 'name' and strlen($default) > 0) {
            return $default;
        } elseif (isset($fieldtype->options[$key])) {
            return $fieldtype->options[$key];
        } else {
            return $default;
        }
    }

    public static function recalculate_numeric_comments_sum($entity_id, $item_id)
    {
        /*$fields_query = db_query(
            "select f.* from app_fields f where f.type  in ('fieldtype_input_numeric_comments') and  f.entities_id='" . db_input(
                $entity_id
            ) . "' and f.comments_status=1 order by f.comments_sort_order, f.name"
        );*/

        $fields_query = \K::model()->db_fetch('app_fields', [
            'type = ? and entities_id = ? and comments_status = 1',
            'fieldtype_input_numeric_comments',
            $entity_id
        ], ['order' => 'comments_sort_order,name'], 'id');

        //while ($fields = db_fetch_array($fields_query)) {
        foreach ($fields_query as $fields) {
            $fields = $fields->cast();

            $total = 0;

            /*$comments_query = db_query(
                "select * from app_comments where entities_id='" . db_input($entity_id) . "' and items_id='" . db_input(
                    $item_id
                ) . "'"
            );*/

            $comments_query = \K::model()->db_fetch('app_comments', [
                'entities_id = ? and items_id = ?',
                $entity_id,
                $item_id
            ], [], 'id');

            //while ($comments = db_fetch_array($comments_query)) {
            foreach ($comments_query as $comments) {
                $comments = $comments->cast();

                /*$history_query = db_query(
                    "select * from app_comments_history where comments_id='" . db_input(
                        $comments['id']
                    ) . "' and fields_id='" . $fields['id'] . "'"
                );*/

                $history_query = \K::model()->db_fetch('app_comments_history', [
                    'comments_id = ? and fields_id = ?',
                    $comments['id'],
                    $fields['id']
                ], [], 'fields_value');

                //while ($history = db_fetch_array($history_query)) {
                foreach ($history_query as $history) {
                    $history = $history->cast();

                    $total += $history['fields_value'];
                }
            }

            $sql_data = ['field_' . (int)$fields['id'] => $total];

            \K::model()->db_perform('app_entity_' . (int)$entity_id, $sql_data, [
                'id = ?',
                $item_id
            ]);
        }
    }

    public static function get_types_wich_choices()
    {
        return [
            'fieldtype_dropdown',
            'fieldtype_dropdown_multiple',
            'fieldtype_radioboxes',
            'fieldtype_grouped_users',
            'fieldtype_checkboxes',
            'fieldtype_dropdown_multilevel'
        ];
    }

    public static function prepare_uniquer_error_msg_param($attributes, $cfg)
    {
        if ($cfg->get('is_unique') and strlen($cfg->get('unique_error_msg'))) {
            $attributes['data-unique-error-msg'] = htmlspecialchars($cfg->get('unique_error_msg'));
        }

        if ($cfg->get('is_unique')) {
            $attributes['unique-for-each-parent'] = $cfg->get('is_unique') == 2 ? 1 : 0;
        }

        return $attributes;
    }

    //use update_items_fields form any fields types where it's required
    public static function update_items_fields($current_entity_id, $item_id)
    {
        \K::$fw->fieldtype_mysql_query_force = true;

        //get item info
        $item_info = \K::model()->db_query_exec_one(
            'select e.* ' . \Tools\FieldsTypes\Fieldtype_formula::prepare_query_select(
                $current_entity_id,
                ''
            ) . ' from app_entity_' . (int)$current_entity_id . ' e where e.id = ?',
            $item_id
        );

        $forceCommit = \K::model()->forceCommit();

        //autoupdate fields in  fieldtype_mysql_query        
        \Tools\FieldsTypes\Fieldtype_mysql_query::update_items_fields($current_entity_id, $item_id, $item_info);

        //dynamic date
        \Tools\FieldsTypes\Fieldtype_dynamic_date::update_items_fields($current_entity_id, $item_id, $item_info);

        //autoupdate time diff
        \Tools\FieldsTypes\Fieldtype_days_difference::update_items_fields($current_entity_id, $item_id);
        \Tools\FieldsTypes\Fieldtype_hours_difference::update_items_fields($current_entity_id, $item_id);
        \Tools\FieldsTypes\Fieldtype_years_difference::update_items_fields($current_entity_id, $item_id);
        \Tools\FieldsTypes\Fieldtype_months_difference::update_items_fields($current_entity_id, $item_id);

        //maps
        \Tools\FieldsTypes\Fieldtype_google_map::update_items_fields($current_entity_id, $item_id, $item_info);
        \Tools\FieldsTypes\Fieldtype_google_map_directions::update_items_fields(
            $current_entity_id,
            $item_id,
            $item_info
        );
        \Tools\FieldsTypes\Fieldtype_yandex_map::update_items_fields($current_entity_id, $item_id, $item_info);

        //autoupdate static text pattern
        \Tools\FieldsTypes\Fieldtype_text_pattern_static::set($current_entity_id, $item_id, $item_info);

        //run php code
        \Tools\FieldsTypes\Fieldtype_php_code::run($current_entity_id, $item_id, $item_info);

        //subentity form
        \Tools\FieldsTypes\Fieldtype_subentity_form::update_items_fields($current_entity_id, $item_id);

        //barcode
        \Tools\FieldsTypes\Fieldtype_barcode::update_items_fields($current_entity_id, $item_id, $item_info);

        //prepare multiple users groups
        \Tools\FieldsTypes\Fieldtype_user_accessgroups::prepare_multiple_access_groups($current_entity_id, $item_id);

        //tree table recalculated count/sum
        \Tools\FieldsTypes\Fieldtype_nested_calculations::update_items_fields(
            $current_entity_id,
            $item_id,
            $item_info['parent_id']
        );

        \Tools\FieldsTypes\Fieldtype_user_photo::prepare_filename($current_entity_id, $item_info);

        //autoset fieldtype autostatus
        \Tools\FieldsTypes\Fieldtype_autostatus::set($current_entity_id, $item_id, $item_info);

        if ($forceCommit) {
            \K::model()->commit();
        }
    }

    public static function custom_error_handler($fields_id)
    {
        return '
          <label id="fields_' . $fields_id . '-error" class="error" for="fields_' . $fields_id . '"></label>
          <script>
              $("#fields_' . $fields_id . '").on("change", function(e) { 
                  $("#fields_' . $fields_id . '-error").hide(); 
              });
          </script>';
    }

    public static function is_empty_value($value, $type)
    {
        if (strlen($value) == 0 or ($value == 0 and in_array(
                    $type,
                    [
                        'fieldtype_dropdown',
                        'fieldtype_radioboxes',
                        'fieldtype_created_by',
                        'fieldtype_input_date',
                        'fieldtype_input_datetime',
                        'fieldtype_time',
                        'fieldtype_entity_multilevel'
                    ]
                ))) {
            return true;
        } else {
            return false;
        }
    }

    public static function get_is_unique_choices($entity_id)
    {
        $choices = [];
        $choices[0] = \K::$fw->TEXT_NO;
        $choices[1] = \K::$fw->TEXT_YES;

        if (\K::$fw->app_entities_cache[$entity_id]['parent_id'] > 0) {
            $choices[2] = \K::$fw->TEXT_UNIQUE_FOR_EACH_PARENT_RECORD;
        }

        return $choices;
    }

    public static function outputFormula($options)
    {
        //return non-formatted value if export
        if (isset($options['is_export']) and !isset($options['is_print'])) {
            return $options['value'];
        }

        $value = $options['value'];

        //just return value if not numeric (not numeric values can be returned using IF operator)
        if (!is_numeric($value)) {
            return $value;
        }

        //return value using number format
        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        if (strlen($cfg->get('number_format')) > 0 and strlen($value) > 0) {
            $format = explode('/', str_replace('*', '', $cfg->get('number_format')));

            $value = number_format($value, $format[0], $format[1], $format[2]);
        } elseif (strstr($value, '.')) {
            $value = number_format((float)$value, 2, '.', '');
        }

        //add prefix and suffix
        return (strlen($value) ? $cfg->get('prefix') . $value . $cfg->get('suffix') : '');
    }
}