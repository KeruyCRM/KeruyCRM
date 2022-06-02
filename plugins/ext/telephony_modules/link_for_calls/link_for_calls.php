<?php


class link_for_calls
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_LINK_FOR_CALLS_TITLE;
        $this->site = '';
        $this->api = '';
        $this->version = '1.0';
    }

    public function configuration()
    {
        $cfg = [];


        $cfg[] = [
            'key' => 'prefix',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_LINK_FOR_CALLS_LINK_PREFIX,
            'description' => TEXT_MODULE_LINK_FOR_CALLS_LINK_PREFIX_INFO,
            'params' => ['class' => 'form-control input-xlarge required'],
        ];

        return $cfg;
    }

    function prepare_url($module_id, $phone_number, $options)
    {
        global $alerts, $app_user;

        $phone_number_val = preg_replace('/\D/', '', $phone_number);

        $params = '';

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        if (preg_match_all('/\[(\w+)\]/', $cfg['prefix'], $matches)) {
            $url = str_replace('[phone]', $phone_number_val, $cfg['prefix']);

            foreach ($matches[1] as $matches_key => $matches_v) {
                if (isset($app_user['fields']['field_' . $matches_v])) {
                    $url = str_replace('[' . $matches_v . ']', $app_user['fields']['field_' . $matches_v], $url);
                }
            }

            $params = 'target="_new"';
        } else {
            $url = $cfg['prefix'] . $phone_number_val;
        }

        return '<a ' . $params . ' href="' . $url . '"><i class="fa fa-phone" aria-hidden="true"></i> ' . $phone_number . '</a>';
    }

}