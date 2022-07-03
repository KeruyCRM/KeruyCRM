<?php

namespace Tools;

class Settings
{
    private $settings;

    function __construct($settings, $defaults = [])
    {
        $this->settings = [];

        //set array
        if (is_array($settings)) {
            $this->settings = $settings;
        } //set json
        elseif (strlen($settings)) {
            $this->settings = json_decode($settings, true);
        }

        //set default values
        foreach ($defaults as $k => $v) {
            if (!isset($this->settings[$k])) {
                $this->settings[$k] = $v;
            }
        }
    }

    function get_settings()
    {
        return $this->settings;
    }

    function get($k, $default = '')
    {
        if (isset($this->settings[$k])) {
            return $this->settings[$k];
        } else {
            return $default;
        }
    }
}