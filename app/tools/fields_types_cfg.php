<?php

namespace Tools;

class Fields_types_cfg
{
    public $cfg;

    public function __construct($configuration)
    {
        if (strlen($configuration) > 0) {
            $this->cfg = json_decode($configuration, true);
        } else {
            $this->cfg = [];
        }
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