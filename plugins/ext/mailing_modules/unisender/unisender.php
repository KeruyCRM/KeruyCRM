<?php

require('plugins/ext/mailing_modules/unisender/api/UnisenderApi.php');

class unisender
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = 'UniSender';
        $this->site = 'https://unisender.com';
        $this->api = 'https://www.unisender.com/ru/support/category/integration/api/';
        $this->version = '1.0';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'api_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_UNISENDER_API_KEY,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function get_list_id_choices($module_id)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $uni = new Unisender\ApiWrapper\UnisenderApi($cfg['api_key']);

        $result = $uni->getLists();

        $choices = [];

        if ($result) {
            $result = json_decode($result, true);

            if (isset($result['error'])) {
                echo alert_error('<b>' . $this->title . ':</b> [' . $result['code'] . '] ' . $result['error']);
            } else {
                if (isset($result['result'])) {
                    foreach ($result['result'] as $v) {
                        $choices[$v['id']] = $v['title'];
                    }
                }
            }
        }

        return $choices;
    }

    function subscribe($module_id, $contact_list_id, $contact_email, $contact_fields)
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $uni = new Unisender\ApiWrapper\UnisenderApi($cfg['api_key']);

        $params = [
            'list_ids' => $contact_list_id,
            'fields' => ['email' => $contact_email],
            'overwrite' => 2,
            'double_optin' => 3
        ];

        if (count($contact_fields)) {
            foreach ($contact_fields as $k => $v) {
                $params['fields'][$k] = $v;
            }
        }

        //subscribe
        $result = $uni->subscribe($params);

        if ($result) {
            $result = json_decode($result, true);

            if (isset($result['error'])) {
                $alerts->add(
                    $this->title . ' ' . TEXT_ERROR . ' [' . $result['code'] . '] ' . $result['error'],
                    'error'
                );
            }
        }
    }

    function update($module_id, $contact_list_id, $contact_email, $contact_fields, $prev_contact_email)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        //check if email updated
        if ($contact_email == $prev_contact_email) {
            //check if there are fields to update
            if (count($contact_fields)) {
                $this->subscribe($module_id, $contact_list_id, $contact_email, $contact_fields);
            }
        } else {
            //delete previous email
            $this->delete($module_id, $contact_list_id, $prev_contact_email);

            //subscribe new account with new email
            $this->subscribe($module_id, $contact_list_id, $contact_email, $contact_fields);
        }
    }

    function delete($module_id, $contact_list_id, $contact_email)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $uni = new Unisender\ApiWrapper\UnisenderApi($cfg['api_key']);

        $params = ['contact_type' => 'email', 'contact' => $contact_email];

        $uni->unsubscribe($params);
    }

}