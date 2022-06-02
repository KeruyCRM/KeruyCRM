<?php

class mailing
{
    public $entity_id;

    public $item_id;

    public $item_info;

    public $module;

    public $module_id;

    function __construct($entity_id, $item_id)
    {
        $this->entity_id = $entity_id;

        $this->item_id = $item_id;

        $item_query = db_query(
            "select e.* from app_entity_" . $this->entity_id . " e where id='" . $this->item_id . "'",
            false
        );
        if ($item = db_fetch_array($item_query)) {
            $this->item_info = $item;
        }
    }

    function get_contact_email($rules)
    {
        if (isset($this->item_info['field_' . $rules['contact_email_field_id']])) {
            $email = $this->item_info['field_' . $rules['contact_email_field_id']];

            if (strlen($email)) {
                return $email;
            }
        }

        return false;
    }

    function get_prev_contact_email($rules, $prev_item_info)
    {
        if (isset($prev_item_info['field_' . $rules['contact_email_field_id']])) {
            $email = $prev_item_info['field_' . $rules['contact_email_field_id']];

            if (strlen($email)) {
                return $email;
            }
        }

        return false;
    }

    function get_contact_fields($rules)
    {
        $fields = [];

        if (strlen($rules['contact_fields'])) {
            $text_pattern = new fieldtype_text_pattern;

            foreach (preg_split('/\r\n|\r|\n/', $rules['contact_fields']) as $extra_field) {
                $extra_field = explode('=', $extra_field);

                $fields[trim($extra_field[0])] = $text_pattern->output_singe_text(
                    trim($extra_field[1]),
                    $this->entity_id,
                    $this->item_info
                );
            }
        }

        return $fields;
    }

    function get_contact_fields_to_update($rules, $prev_item_info)
    {
        $fields = [];

        if (strlen($rules['contact_fields'])) {
            $text_pattern = new fieldtype_text_pattern;

            foreach (preg_split('/\r\n|\r|\n/', $rules['contact_fields']) as $extra_field) {
                $extra_field = explode('=', $extra_field);

                $field_id = str_replace(['[', ']'], '', trim($extra_field[1]));

                if ($this->item_info['field_' . $field_id] != $prev_item_info['field_' . $field_id]) {
                    $fields[trim($extra_field[0])] = $text_pattern->output_singe_text(
                        trim($extra_field[1]),
                        $this->entity_id,
                        $this->item_info
                    );
                }
            }
        }

        return $fields;
    }

    function subscribe()
    {
        $rules_query = db_query(
            "select r.*, m.module from app_ext_subscribe_rules r, app_ext_modules m where r.entities_id='" . $this->entity_id . "' and r.contact_email_field_id>0 and length(contact_list_id)>0 and m.id=r.modules_id and m.is_active=1"
        );
        while ($rules = db_fetch_array($rules_query)) {
            if ($contact_email = $this->get_contact_email($rules)) {
                $module = new $rules['module'];
                $module->subscribe(
                    $rules['modules_id'],
                    $rules['contact_list_id'],
                    $contact_email,
                    $this->get_contact_fields($rules)
                );
            }
        }
    }


    function delete()
    {
        $rules_query = db_query(
            "select r.*, m.module from app_ext_subscribe_rules r, app_ext_modules m where r.entities_id='" . $this->entity_id . "' and r.contact_email_field_id>0 and length(contact_list_id)>0 and m.id=r.modules_id and m.is_active=1"
        );
        while ($rules = db_fetch_array($rules_query)) {
            if ($contact_email = $this->get_contact_email($rules)) {
                $module = new $rules['module'];
                $module->delete($rules['modules_id'], $rules['contact_list_id'], $contact_email);
            }
        }
    }

    function update($prev_item_info)
    {
        $rules_query = db_query(
            "select r.*, m.module from app_ext_subscribe_rules r, app_ext_modules m where r.entities_id='" . $this->entity_id . "' and r.contact_email_field_id>0 and length(contact_list_id)>0 and m.id=r.modules_id and m.is_active=1"
        );
        while ($rules = db_fetch_array($rules_query)) {
            if ($contact_email = $this->get_contact_email($rules)) {
                $module = new $rules['module'];
                $module->update(
                    $rules['modules_id'],
                    $rules['contact_list_id'],
                    $contact_email,
                    $this->get_contact_fields_to_update($rules, $prev_item_info),
                    $this->get_prev_contact_email($rules, $prev_item_info)
                );
            }
        }
    }
}