<?php

class alerts
{

    function __construct()
    {
        $this->messages = [];
    }

    function count()
    {
        return count($this->messages);
    }

    function add($message, $type = '')
    {
        $class = '';
        switch ($type) {
            case 'error':
                $class = 'alert-danger';
                break;
            case 'warning':
                $class = 'alert-warning';
                break;
            case 'success':
                $class = 'alert-success';
                break;
            default:
                $class = 'alert-info';
                break;
        }

        $this->messages[] = ['params' => 'class="alert ' . $class . '"', 'text' => $message];
    }

    function output()
    {
        if (count($this->messages) == 0) {
            return '';
        }


        $output = [];
        foreach ($this->messages as $v) {
            $output[] = '<div ' . $v['params'] . '><button type="button" class="close" data-dismiss="alert">&times;</button>' . $v['text'] . '</div>';
        }

        //reset messages
        $this->messages = [];

        return implode("\n", $output);
    }

}

