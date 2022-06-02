<?php

require('plugins/ext/mailing_modules/mail_chimp/lib/MailChimp.php');

use \DrewM\MailChimp\MailChimp;

class mail_chimp
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_MAILCHIMP_TITLE;
        $this->site = 'https://mailchimp.com';
        $this->api = 'https://developer.mailchimp.com';
        $this->version = '1.0';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'api_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_MAILCHIMP_API_KEY,
            'params' => ['class' => 'form-control input-large required'],
        ];


        return $cfg;
    }

    function get_list_id_choices($module_id)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $MailChimp = new MailChimp($cfg['api_key']);

        $result = $MailChimp->get('lists');

        if (!$MailChimp->success()) {
            echo alert_error('<b>' . $this->title . ':</b> ' . $MailChimp->getLastError());
        }

        $choices = [];

        foreach ($result['lists'] as $obj) {
            $choices[$obj['id']] = $obj['name'];
        }

        return $choices;
    }

    function subscribe($module_id, $contact_list_id, $contact_email, $contact_fields)
    {
        global $alerts;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $MailChimp = new MailChimp($cfg['api_key']);

        $params = ['email_address' => $contact_email, 'status' => 'subscribed'];

        if (count($contact_fields)) {
            $params['merge_fields'] = $contact_fields;
        }

        //subscribe
        $result = $MailChimp->post("lists/$contact_list_id/members", $params);

        if (!isset($result['status'])) {
            $result['status'] = '';
        }

        if (!$MailChimp->success() and $result['status'] != 400) {
            $alerts->add(
                $this->title . ' ' . TEXT_ERROR . ' ' . $MailChimp->getLastError() . '<br>' . print_r($result, true),
                'error'
            );
        }
    }

    function update($module_id, $contact_list_id, $contact_email, $contact_fields, $prev_contact_email)
    {
        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $MailChimp = new MailChimp($cfg['api_key']);

        //check if email updated
        if ($contact_email == $prev_contact_email) {
            //check if there are fields to update
            if (count($contact_fields)) {
                $subscriber_hash = $MailChimp->subscriberHash($contact_email);
                $result = $MailChimp->patch(
                    "lists/$contact_list_id/members/$subscriber_hash",
                    ['merge_fields' => $contact_fields]
                );
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

        $MailChimp = new MailChimp($cfg['api_key']);

        $subscriber_hash = $MailChimp->subscriberHash($contact_email);

        $MailChimp->delete("lists/$contact_list_id/members/$subscriber_hash");
    }

}