<?php

namespace Helpers;

class Urls
{
    public static function redirect_to($module, $prams = '', $token = false)
    {
        if (\K::$fw->AJAX) {
            echo '<script>window.top.location.href="' . self::url_for($module, $prams, $token) . '"</script>';
            exit();
        }

        \K::fw()->reroute(self::url_for($module, $prams, $token));
    }

    public static function is_ssl()
    {
        return ((ENABLE_SSL or IS_HTTPS == 'on') ? true : false);
    }

    public static function url_for($module, $params = '', $token = false)
    {
        $self = pathinfo($_SERVER['PHP_SELF']);
        $self['dirname'] = str_replace("\\", "/", $self['dirname']);
        $path = $self['dirname'] . (substr($self['dirname'], -1) != '/' ? '/' : '');

        $params = (strlen($params) > 0 ? '?' . $params : '');

        if (\K::$fw->IS_CRON) {
            $url = \K::$fw->CRON_HTTP_SERVER_HOST . $module . $params;
        } else {
            $url = \K::$fw->SCHEME . '://' . \K::$fw->HOST . $path . $module . $params;
        }

        if ($token) {
            $url .= (strlen($params) > 0 ? '&' : '?') . \K::security()->addTokenToUrl();
        }

        return $url;
    }

    public static function url_for_file($file)
    {
        $scheme = (is_ssl() ? 'https://' : 'http://');
        $host = $_SERVER['HTTP_HOST'];

        $self = pathinfo($_SERVER['PHP_SELF']);
        $self['dirname'] = str_replace("\\", "/", $self['dirname']);
        $path = $self['dirname'] . (substr($self['dirname'], -1) != '/' ? '/' : '');

        return $scheme . $host . $path . $file;
    }

    public static function link_to($name, $url, $attributes = [])
    {
        return '<a href="' . $url . '" ' . \Helpers\Html::tag_attributes_to_html($attributes) . '>' . $name . '</a>';
    }

    public static function link_to_modalbox($name, $url, $attributes = [])
    {
        if (!isset($attributes['class'])) {
            $attributes['class'] = '';
        }

        $attributes['class'] .= ' link-to-modalbox';

        return '<a onClick="open_dialog(\'' . $url . '\')" ' . tag_attributes_to_html(
                $attributes
            ) . '>' . $name . '</a>';
    }

    public static function components_path($path)
    {
        $module_array = explode('/', $path);

        $extension = $module_array[0];
        $module = $module_array[1];
        $component = $module_array[2];

        return $extension . '/' . $module . '/components/' . $component . '.php';
    }

    public static function get_all_get_url_params($exclude_array = '')
    {
        global $_GET;

        if (!is_array($exclude_array)) {
            $exclude_array = [];
        }

        $params = [];
        if (is_array($_GET) && (sizeof($_GET) > 0)) {
            reset($_GET);
            foreach ($_GET as $key => $value) {
                if (is_string($value) && (strlen($value) > 0) && ($key != session_name(
                        )) && ($key != 'error') && ($key != 'module') && (!in_array(
                        $key,
                        $exclude_array
                    )) && ($key != 'x') && ($key != 'y')) {
                    $params[] = $key . '=' . rawurlencode(stripslashes($value));
                }
            }
        }

        return implode('&', $params);
    }

    public static function auto_link_text($text)
    {
        $pattern = '/(?s)<pre[^<]*>.*?<\\/pre>(*SKIP)(*F)|([^"]|^)(((http[s]?:\/\/(.+(:.+)?$)?))[a-z0-9](([-a-z0-9]+\.)*\.[a-z]{2,})?\/?[a-z0-9()$.,_\/~#&=:;%+!?-]+)/i';

        $result = preg_replace_callback($pattern, 'callback_prepare_link_in_text', $text);

        if ($result != null) {
            return $result;
        } else {
            return $text;
        }
    }

    public static function callback_prepare_link_in_text($matches)
    {
        $scheme = (is_ssl() ? 'https://' : 'http://');
        $host = $_SERVER['HTTP_HOST'];

        $self = pathinfo($_SERVER['PHP_SELF']);
        $path = $self['dirname'];

        $current_path = $scheme . $host . $path;

        $href = $matches[2];

        if (strstr($href, $current_path)) {
            $url = str_replace($current_path, '', $href);
        } else {
            $url = $href;
        }

        if (strlen($url) > 60) {
            $url = substr($url, 0, 25) . '...' . substr($url, -25);
        }

        return $matches[1] . '<a target="_blank" href="' . $href . '" title="' . $href . '">' . $url . '</a>';
    }
}