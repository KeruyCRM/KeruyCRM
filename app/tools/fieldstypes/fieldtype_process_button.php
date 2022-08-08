<?php

namespace Tools\FieldsTypes;

class Fieldtype_process_button
{
    public $options;

    public function __construct()
    {
        $this->options = ['title' => \K::$fw->TEXT_FIELDTYPE_PROCESS_BUTTON_TITLE];
    }

    public function get_configuration($params = [])
    {
        $cfg = [];

        if (\Helpers\App::is_ext_installed()) {
            $choices = [];
            $choices[''] = '';
            $processes_query = db_query(
                "select p.*, e.name as entities_name from app_ext_processes p, app_entities e where e.id=p.entities_id and e.id='" . $params['entities_id'] . "' order by p.sort_order, e.name, p.name"
            );
            while ($processes = db_fetch_array($processes_query)) {
                $choices[$processes['id']] = (($processes['name'] == $processes['button_title'] or strlen(
                        $processes['button_title']
                    ) == 0) ? $processes['name'] : $processes['name'] . ' (' . $processes['button_title'] . ')');
            }

            $cfg[] = [
                'title' => \K::$fw->TEXT_EXT_PROCESSES,
                'tooltip_icon' => \K::$fw->TEXT_EXT_SELECT_BUTTONS_TO_DISPLAY,
                'name' => 'process_button',
                'type' => 'dropdown',
                'choices' => $choices,
                'params' => ['class' => 'form-control chosen-select', 'multiple' => 'multiple']
            ];

            $cfg[] = [
                'title' => \K::$fw->TEXT_DISPLAY_AS,
                'tooltip_icon' => \K::$fw->TEXT_EXT_MULTIPLE_BUTTONS_DISPLAY_TYPE,
                'name' => 'display_as',
                'type' => 'dropdown',
                'choices' => [
                    'inline' => \K::$fw->TEXT_INLINE_LIST,
                    'inrow' => \K::$fw->TEXT_EXT_EXTRA_ROWS,
                    'grouped' => \K::$fw->TEXT_EXT_BUTTON_GROUP,
                    'dropdown' => \K::$fw->TEXT_FIELDTYPE_DROPDOWN_TITLE
                ],
                'params' => ['class' => 'form-control input-medium']
            ];

            $cfg[] = [
                'title' => \K::$fw->TEXT_BUTTON_TITLE,
                'name' => 'button_title',
                'type' => 'input',
                'params' => ['class' => 'form-control input-medium'],
                'tooltip' => \K::$fw->TEXT_DEFAULT . ': ' . \K::$fw->TEXT_ACTION,
                'form_group' => ['form_display_rules' => 'fields_configuration_display_as:dropdown']
            ];

            $cfg[] = [
                'title' => \K::$fw->TEXT_EXT_PROCESS_BUTTON_COLOR,
                'name' => 'button_color',
                'type' => 'colorpicker',
                'form_group' => ['form_display_rules' => 'fields_configuration_display_as:dropdown']
            ];
        } else {
            $cfg[] = ['html' => app_alert_warning(\K::$fw->TEXT_EXTENSION_REQUIRED), 'type' => 'html'];
        }

        return $cfg;
    }

    public function render($field, $obj, $params = [])
    {
        return '';
    }

    public function process($options)
    {
        return '';
    }

    public function output($options)
    {
        global $buttons_css_holder;

        $cfg = new \Models\Main\Fields_types_cfg($options['field']['configuration']);

        $html = '';
        $buttons_css = '';
        $buttons_links = [];
        $buttons_urls = [];

        if (is_array($cfg->get('process_button')) and count($cfg->get('process_button'))) {
            $processes = new processes($options['field']['entities_id']);
            $processes->items_id = $options['item']['id'];
            $buttons_list = $processes->get_buttons_list('', implode(',', $cfg->get('process_button')));

            foreach ($buttons_list as $buttons) {
                $check_buttons_filters = $processes->check_buttons_filters($buttons);

                $is_dialog = ((strlen(
                        $buttons['confirmation_text']
                    ) or $buttons['allow_comments'] == 1 or $buttons['preview_prcess_actions'] == 1 or $processes->has_enter_manually_fields(
                        $buttons['id']
                    )) ? true : false);
                $params = (!$is_dialog ? '&action=run' : '') . ((isset($options['reports_id']) and isset($_POST['page'])) ? '&gotopage[' . $options['reports_id'] . ']=' . $_POST['page'] : '');
                $css = (!$is_dialog ? ' prevent-double-click' : '');

                $rdirect_to = ((isset($options['redirect_to']) and strlen(
                        $options['redirect_to']
                    )) ? $options['redirect_to'] : 'items');

                if (!isset($options['reports_id'])) {
                    $rdirect_to = 'items_info';
                }

                $path = $options['path'];

                if (substr($path, -strlen('-' . $options['item']['id'])) != '-' . $options['item']['id']) {
                    $path .= '-' . $options['item']['id'];
                }

                if ($rdirect_to == 'parent_item_info_page') {
                    $path_info = items::parse_path($path);
                    $rdirect_to = 'item_info_page' . $path_info['parent_entity_id'] . '-' . $path_info['parent_entity_item_id'];
                }

                //buttons list
                if (!$check_buttons_filters) {
                    if ($processes->button_has_warnign_text($buttons)) {
                        $buttons_links[] = button_tag(
                            $buttons['button_title'],
                            url_for('items/processes_warning', 'id=' . $buttons['id'] . '&path=' . $path),
                            true,
                            ['class' => 'btn btn-primary btn-sm btn-process-' . $buttons['id'] . $css],
                            $buttons['button_icon']
                        );
                    }
                } else {
                    $buttons_links[] = button_tag(
                        $buttons['button_title'],
                        url_for(
                            'items/processes',
                            'id=' . $buttons['id'] . '&path=' . $path . '&redirect_to=' . $rdirect_to . $params
                        ),
                        $is_dialog,
                        ['class' => 'btn btn-primary btn-sm btn-process-' . $buttons['id'] . $css],
                        $buttons['button_icon']
                    );
                }

                //buttons url
                $url_color = (strlen(
                    $buttons['button_color']
                ) ? 'style="color: ' . $buttons['button_color'] . '"' : '');

                //check buttons filters
                if (!$check_buttons_filters) {
                    if ($processes->button_has_warnign_text($buttons)) {
                        $buttons_urls[] = '<a ' . $url_color . ' onclick="open_dialog(\'' . url_for(
                                'items/processes_warning',
                                'id=' . $buttons['id'] . '&path=' . $path
                            ) . '\')" class="link-to-modalbox">' . app_render_icon(
                                $buttons['button_icon']
                            ) . ' ' . $buttons['button_title'] . '</a>';
                    }
                } //prepare buttons
                elseif ($is_dialog) {
                    $buttons_urls[] = '<a ' . $url_color . ' onclick="open_dialog(\'' . url_for(
                            'items/processes',
                            'id=' . $buttons['id'] . '&path=' . $path . '&redirect_to=' . $rdirect_to . $params
                        ) . '\')" class="link-to-modalbox">' . app_render_icon(
                            $buttons['button_icon']
                        ) . ' ' . $buttons['button_title'] . '</a>';
                } else {
                    $buttons_urls[] = '<a ' . $url_color . ' href="' . url_for(
                            'items/processes',
                            'id=' . $buttons['id'] . '&path=' . $path . '&redirect_to=' . $rdirect_to . $params
                        ) . '" class="link-to-modalbox">' . app_render_icon(
                            $buttons['button_icon']
                        ) . ' ' . $buttons['button_title'] . '</a>';
                }

                //button csss
                if (!isset($buttons_css_holder[$buttons['id']])) {
                    $buttons_css_holder[$buttons['id']] = $processes->prepare_button_css($buttons);
                    $buttons_css .= $buttons_css_holder[$buttons['id']];
                }
            }

            switch ($cfg->get('display_as')) {
                case 'inline':
                    $html = implode(' ', $buttons_links);
                    break;
                case 'inrow':
                    $html = implode('<br>', $buttons_links);
                    break;
                case 'grouped':
                    $html = '<div class="btn-group btn-group-sm" style="display: inline-flex">' . implode(
                            '',
                            $buttons_links
                        ) . '</div>';
                    break;
                case 'dropdown':
                    if (count($buttons_urls)) {
                        $html = '
                            <div class="btn-group btn-group-sm ">
                                    <button class="btn btn-primary btn-process-0 dropdown-toggle btn-process-button-dropdown" type="button" data-toggle="dropdown" data-boundary="window" aria-expanded="false">
                                    ' . (strlen($cfg->get('button_title')) ? $cfg->get('button_title') : \K::$fw->TEXT_ACTION) . ' <i class="fa fa-angle-down"></i>
                                    </button>
                                    <ul class="dropdown-menu" role="menu">
                                        <li>
                                            ' . implode('</li><li>', $buttons_urls) . '
                                        <li>			
                                    </ul>
                            </div>
                            ';
                    }

                    if (strlen($cfg->get('button_color')) and !isset($buttons_css_holder[0])) {
                        $buttons_css_holder[0] = $processes->prepare_button_css(
                            ['id' => 0, 'button_color' => $cfg->get('button_color')]
                        );
                        $buttons_css .= $buttons_css_holder[0];
                    }
                    break;
            }

            $html .= $buttons_css;
        }

        return $html;
    }
}