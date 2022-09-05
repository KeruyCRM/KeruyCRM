<?php

namespace Tools;

class Portlets
{
    public $is_collapsed, $name;

    public function __construct($name, $default_status = false)
    {
        global $app_user;

        $this->name = $name;

        $check_query = db_query(
            "select id, is_collapsed from app_portlets where name='" . db_input(
                $this->name
            ) . "' and users_id='" . $app_user['id'] . "'"
        );
        if ($check = db_fetch_array($check_query)) {
            if ($check['is_collapsed'] == 1) {
                $this->is_collapsed = true;
            } else {
                $this->is_collapsed = false;
            }
        } else {
            $this->is_collapsed = $default_status;
        }
    }

    public function render_body()
    {
        $html = ' data_portlet_id="' . $this->name . '"';

        if ($this->is_collapsed) {
            $html .= 'style="display:none"';
        }

        return $html;
    }

    public function button_css()
    {
        return $this->is_collapsed ? 'expand' : 'collapse';
    }

    public static function set_status($name, $status)
    {
        global $app_user;

        if (!strlen($name)) {
            return false;
        }

        $check_query = db_query(
            "select id from app_portlets where name='" . db_input_protect(
                $name
            ) . "' and users_id='" . $app_user['id'] . "'"
        );
        if ($check = db_fetch_array($check_query)) {
            db_perform('app_portlets', ['is_collapsed' => $status], 'update', "id='" . $check['id'] . "'");
        } else {
            db_perform('app_portlets', [
                'name' => db_input_protect($name),
                'users_id' => $app_user['id'],
                'is_collapsed' => $status,
            ]);
        }
    }

    public static function delete_by_user_id($user_id)
    {
        //db_query("delete from app_portlets where users_id={$user_id}");

        \K::model()->db_delete_row('app_portlets', $user_id, 'users_id');
    }
}