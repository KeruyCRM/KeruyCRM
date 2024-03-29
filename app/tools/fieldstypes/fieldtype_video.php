<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Tools\FieldsTypes;

class Fieldtype_video
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_VIDEO_TITLE];
    }

    public function get_configuration()
    {
        $cfg = [];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'width',
            'type' => 'dropdown',
            'choices' => ['input-large' => \K::$fw->TEXT_INPUT_LARGE, 'input-xlarge' => \K::$fw->TEXT_INPUT_XLARGE],
            'tooltip_icon' => \K::$fw->TEXT_ENTER_WIDTH,
            'params' => ['class' => 'form-control input-medium']
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY,
            'name' => 'hide_field_if_empty',
            'type' => 'checkbox',
            'tooltip_icon' => \K::$fw->TEXT_HIDE_FIELD_IF_EMPTY_TIP
        ];

        $cfg[\K::$fw->TEXT_SETTINGS][] = [
            'title' => \K::$fw->TEXT_HIDE_FIELD_NAME,
            'name' => 'hide_field_name',
            'type' => 'checkbox'
        ];

        $cfg[\K::$fw->TEXT_VIDEO_PLAYER][] = [
            'title' => \K::$fw->TEXT_WIDTH,
            'name' => 'video_width',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_DEFAULT . ': 300',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_VIDEO_PLAYER][] = [
            'title' => \K::$fw->TEXT_HEIGHT,
            'name' => 'video_height',
            'type' => 'input',
            'params' => ['class' => 'form-control input-small']
        ];

        $cfg[\K::$fw->TEXT_VIDEO_PLAYER][] = [
            'title' => \K::$fw->TEXT_BUTTON_TITLE,
            'name' => 'button_title',
            'type' => 'input',
            'tooltip_icon' => \K::$fw->TEXT_BUTTON_DISPLAYS_IN_LISTING,
            'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_VIEW,
            'params' => ['class' => 'form-control input-large']
        ];

        $cfg[\K::$fw->TEXT_VIDEO_PLAYER][] = [
            'title' => \K::$fw->TEXT_HIDE_VIDEO_PLAYER,
            'name' => 'hide_video_player',
            'type' => 'checkbox'
        ];

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        $cfg = new \Models\Main\Fields_types_cfg($field['configuration']);

        $attributes = [
            'class' => 'form-control ' . $cfg->get(
                    'width'
                ) . ' fieldtype_video field_' . $field['id'] . ($field['is_required'] == 1 ? ' required noSpace' : '')
        ];

        return \Helpers\Html::input_tag('fields[' . $field['id'] . ']', $obj['field_' . $field['id']], $attributes);
    }

    public function process($options)
    {
        return \K::model()->db_prepare_input($options['value']);
    }

    public function output($options)
    {
        if (!strlen($options['value'])) {
            return '';
        }

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        //return video url
        if (isset($options['is_export']) or isset($options['is_email'])) {
            return $options['value'];
        }

        //render video button
        if (isset($options['is_listing']) or $cfg->get('hide_video_player') == 1) {
            $path = $options['path'];

            if (substr($path, -strlen('-' . $options['item']['id'])) != '-' . $options['item']['id']) {
                $path .= '-' . $options['item']['id'];
            }

            $button_title = (strlen($cfg->get('button_title')) ? $cfg->get('button_title') : \K::$fw->TEXT_VIEW);
            $url = \Helpers\Urls::url_for(
                'main/items/videopopup',
                'path=' . $path . '&field_id=' . $options['field']['id']
            );
            return \Helpers\Urls::link_to_modalbox($button_title, $url, ['class' => 'btn btn-default']);
        }

        //render video
        return self::render_video($options['value'], $cfg);
    }

    public static function render_video($url, $cfg)
    {
        $html = '';
        $video_width = (strlen($cfg->get('video_width')) ? (int)$cfg->get('video_width') : 300);
        $video_height = (strlen($cfg->get('video_height')) ? (int)$cfg->get('video_height') : 0);
        $video_css = 'style="width: 100%; max-width: ' . $video_width . 'px; ' . ($video_height > 0 ? 'height: ' . $video_height . 'px' : '') . '"';

        switch (true) {
            case (strstr($url, 'youtube.com') or strstr($url, 'youtu.be')):
                if (preg_match(
                    "#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#",
                    $url,
                    $matches
                )) {
                    $html = '<iframe ' . $video_css . ' class="youtube-video-frame" src="https://www.youtube.com/embed/' . $matches[0] . '?rel=0&showinfo=0&ecver=1" frameborder="0" allowfullscreen></iframe>';
                }
                break;
            case strstr($url, 'vimeo.com'):
                if (preg_match(
                    '%^https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)(?:[?]?.*)$%im',
                    $url,
                    $matches
                )) {
                    $html = '<iframe ' . $video_css . ' src="https://player.vimeo.com/video/' . $matches[3] . '"  frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
                }
                break;
            case (substr($url, -4) == '.mp4' or substr($url, -4) == '.MP4'):
                $html = '<video ' . $video_css . ' controls><source src="' . $url . '" type="video/mp4">' . \K::$fw->TEXT_VIDEO_TAG_NOT_SUPPORTED . '</video>';
                break;
            case (substr($url, -4) == '.ogg' or substr($url, -4) == '.OGG'):
                $html = '<video ' . $video_css . ' controls><source src="' . $url . '" type="video/ogg">' . \K::$fw->TEXT_VIDEO_TAG_NOT_SUPPORTED . '</video>';
                break;
            case (substr($url, -5) == '.webm' or substr($url, -5) == '.WEBM'):
                $html = '<video ' . $video_css . ' controls><source src="' . $url . '" type="video/webm">' . \K::$fw->TEXT_VIDEO_TAG_NOT_SUPPORTED . '</video>';
                break;
            default:
                $html = '<a href="' . $url . '" target="_blank">' . \Helpers\App::app_crop_str($url) . '</a>';
                break;
        }

        return $html;
    }
}