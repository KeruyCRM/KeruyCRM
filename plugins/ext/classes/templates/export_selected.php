<?php

class export_selected
{

    static function get_position_choices()
    {
        $choices = [
            'in_listing' => TEXT_IN_LISTING,
            'menu_with_selected' => TEXT_EXT_MENU_WITH_SELECTED,
            'menu_export' => TEXT_EXT_EXPORT_BUTTON,
        ];

        return $choices;
    }

    static function get_users_templates_by_position($entities_id, $position, $url_params = '')
    {
        global $app_user;

        $templates_list = [];

        $html = '';

        $templates_query = db_query(
            "select ep.* from app_ext_export_selected ep, app_entities e where ep.is_active=1 and e.id=ep.entities_id and find_in_set('" . str_replace(
                '_dashboard',
                '',
                $position
            ) . "',ep.button_position) and ep.entities_id='" . db_input(
                $entities_id
            ) . "' and (find_in_set(" . $app_user['group_id'] . ",users_groups) or find_in_set(" . $app_user['id'] . ",assigned_to)) order by ep.sort_order, ep.name"
        );
        while ($templates = db_fetch_array($templates_query)) {
            $button_title = (strlen($templates['button_title']) ? $templates['button_title'] : $templates['name']);
            $button_icon = (strlen($templates['button_icon']) ? $templates['button_icon'] : '');

            $style = (strlen($templates['button_color']) ? 'color: ' . $templates['button_color'] : '');

            switch ($position) {
                case 'menu_export':
                    $html .= '<li>' . link_to_modalbox(
                            '<i class="fa ' . $button_icon . '"></i> ' . $button_title,
                            url_for('ext/with_selected/export', 'templates_id=' . $templates['id'] . $url_params),
                            ['style' => $style]
                        ) . '</li>';
                    break;
                case 'in_listing':
                    $html .= '&nbsp;&nbsp;' . button_tag(
                            $button_title,
                            url_for('ext/with_selected/export', 'templates_id=' . $templates['id'] . $url_params),
                            true,
                            ['class' => 'btn btn-primary btn-xlsx-template-' . $templates['id']],
                            $button_icon
                        );
                    $html .= self::prepare_button_css($templates);
                    break;
                case 'menu_with_selected':
                    $templates_list[] = [
                        'id' => $templates['id'],
                        'name' => $button_title,
                        'entities_id' => $templates['entities_id'],
                        'button_icon' => $button_icon,
                        'style' => $style
                    ];
                    break;
                case 'menu_with_selected_dashboard':
                    $html .= '<li>' . link_to_modalbox(
                            '<i class="fa ' . $button_icon . '"></i> ' . $button_title,
                            url_for('ext/with_selected/export', 'templates_id=' . $templates['id'] . $url_params),
                            ['style' => $style]
                        ) . '</li>';
                    break;
            }
        }

        switch ($position) {
            case 'in_listing':
            case 'menu_with_selected_dashboard':
                return $html;
                break;
            case 'menu_with_selected':
                return $templates_list;
                break;
            case 'menu_export':
                if (strlen($html)) {
                    return '                       
                        <div class="btn-group">
                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" data-hover="dropdown">
                                <i class="fa fa-download"></i> ' . TEXT_EXPORT . ' <i class="fa fa-angle-down"></i>
                            </button>
                            <ul class="dropdown-menu" role="menu">                                       
                                ' . $html . '												
                            </ul>
                        </div>                        
                            ';
                } else {
                    return '';
                }
                break;
        }
    }

    public static function prepare_button_css($buttons)
    {
        $css = '';

        if (strlen($buttons['button_color'])) {
            $rgb = convert_html_color_to_RGB($buttons['button_color']);
            $rgb[0] = $rgb[0] - 25;
            $rgb[1] = $rgb[1] - 25;
            $rgb[2] = $rgb[2] - 25;
            $css = '
                    <style>
                            .btn-xlsx-template-' . $buttons['id'] . '{
                                    background-color: ' . $buttons['button_color'] . ';
                                    border-color: ' . $buttons['button_color'] . ';
                            }
                            .btn-primary.btn-xlsx-template-' . $buttons['id'] . ':hover,
                            .btn-primary.btn-xlsx-template-' . $buttons['id'] . ':focus,
                            .btn-primary.btn-xlsx-template-' . $buttons['id'] . ':active,
                            .btn-primary.btn-xlsx-template-' . $buttons['id'] . '.active{
                              background-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
                              border-color: rgba(' . $rgb[0] . ',' . $rgb[1] . ',' . $rgb[2] . ',1);
                            }
                    </style>
			';
        }

        return $css;
    }

    static function delele_blocks_by_template_id($template_id)
    {
        $block_query = db_query(
            "select id from app_ext_export_selected_blocks where templates_id='" . $template_id . "' and parent_id=0"
        );
        while ($block = db_fetch_array($block_query)) {
            self::delele_block($block['id']);
        }
    }

    static function delele_block($block_id)
    {
        db_query("delete from app_ext_export_selected_blocks where id=" . $block_id);

        $block_query = db_query("select id from app_ext_export_selected_blocks where parent_id='" . $block_id . "'");
        while ($block = db_fetch_array($block_query)) {
            self::delele_block($block['id']);
        }
    }

}
