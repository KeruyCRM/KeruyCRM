<?php

namespace Tools\FieldsTypes;

class fieldtype_input_email
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_INPUT_EMAIL_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[] = ['title' => \K::$fw->TEXT_DISPLAY_AS_LINK, 'name' => 'display_as_link', 'type' => 'checkbox'];

        $cfg[] = [
            'title' => \K::$fw->TEXT_ALLOW_SEARCH,
            'name' => 'allow_search',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_ALLOW_SEARCH_TIP
        ];

        $cfg[] = [
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

        $cfg[] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[] = [
            'title' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE,
            'name' => 'is_unique',
            'type' => 'dropdown',
            'choices' => fields_types::get_is_unique_choices(_POST('entities_id')),
            'tooltip_icon' => \K::$fw->TEXT_IS_UNIQUE_FIELD_VALUE_TIP,
            'params' => ['class' => 'form-control input-large']
        ];
        $cfg[] = [
            'title' => \K::$fw->TEXT_ERROR_MESSAGE,
            'name' => 'unique_error_msg',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP,
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_UNIQUE_FIELD_VALUE_ERROR,
            'params' => ['class' => 'form-control input-xlarge']
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Tools\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get('width') .
                ' fieldtype_input field_' . $field['id'] .
                ($field['is_heading'] == 1 ? ' autofocus' : '') .
                ($field['is_required'] == 1 ? ' required email noSpace' : '') .
                ($cfg->get('is_unique') > 0 ? ' is-unique' : ''),
            'type' => 'email'
        ];
        $attributes = fields_types::prepare_uniquer_error_msg_param($attributes, $cfg);

        return input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);
    }

    public function process($options)
    {
        return db_prepare_input($options['value']);
    }

    public function output($options)
    {
        $cfg = new \Tools\Fields_types_cfg($options['field']['configuration']);

        if (isset($options['is_export'])) {
            return $options['value'];
        } elseif ($cfg->get('display_as_link') == 1) {
            $html = '<a href="mailto:' . $options['value'] . '" target="_blank">' . $options['value'] . '</a>';

            if (is_ext_installed() and \K::$fw->CFG_MAIL_INTEGRATION and mail_accounts_users::has_access()) {
                $html = '<a href="javascript: open_dialog(\'' . url_for(
                        'ext/mail/create',
                        'mail_to=' . $options['value'] . '&path=' . $options['field']['entities_id'] . '-' . $options['item']['id']
                    ) . '\')" >' . $options['value'] . '</a>';
            }

            return $html;
        } else {
            return $options['value'];
        }
    }
}
