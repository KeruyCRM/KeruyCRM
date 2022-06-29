<?php

namespace Tools;

class Fields_types_options_cfg
{
    public $cfg;

    public function __construct($options)
    {
        $this->cfg = $options;
    }

    public function has($key)
    {
        if (isset($this->cfg[$key])) {
            return true;
        } else {
            return false;
        }
    }

    public function get($key, $defautl = '')
    {
        if (isset($this->cfg[$key])) {
            return $this->cfg[$key];
        } else {
            return $defautl;
        }
    }
}