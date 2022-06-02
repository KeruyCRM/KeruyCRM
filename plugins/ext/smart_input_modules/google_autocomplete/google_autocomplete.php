<?php

class google_autocomplete
{

    public $title;
    public $site;
    public $types_choices;

    function __construct()
    {
        $this->title = TEXT_MODULE_GOOGLE_AUTOCOMPLETE_TITLE;
        $this->site = 'https://developers.google.com';
        $this->api = 'https://developers.google.com/maps/documentation/javascript/places-autocomplete#place_autocomplete_service';
        $this->version = '1.0';
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'api_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_EXT_API_KEY,
            'params' => ['class' => 'form-control input-xlarge required'],
        ];

        $cfg[] = [
            'key' => 'country_restriction',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_MODULE_GOOGLE_AUTOCOMPLETE_COUNTRY_RESCTRICTION,
            'description' => TEXT_MODULE_GOOGLE_AUTOCOMPLETE_COUNTRY_RESCTRICTION_INFO,
            'params' => ['class' => 'form-control input-large required'],
        ];

        return $cfg;
    }

    public function render_itnegration_type_name($type)
    {
        return (isset($this->types_choices[$type]) ? $this->types_choices[$type] : $type);
    }

    public function render_itnegration_types($type)
    {
        return '';
    }

    public function render_itnegration_rules($rules, $entity_field_html = '')
    {
        $html = $entity_field_html;

        return $html;
    }

    public function render_js_includes($module_id)
    {
        global $is_google_map_script;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $html = '';

        if ($is_google_map_script != true) {
            $html = '<script src="https://maps.googleapis.com/maps/api/js?key=' . $cfg['api_key'] . '&libraries=places"></script>';
            $is_google_map_script = true;
        }

        return $html;
    }

    public function render($module_id, $rules)
    {
        $html = '';

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $options = '';
        if (strlen($cfg['country_restriction'])) {
            $countries = explode(',', $cfg['country_restriction']);
            $countries = array_map(function ($v) {
                return trim($v);
            }, $countries);
            $options .= ',componentRestrictions: { country: ["' . implode('","', $countries) . '"] }';
        }

        $html .= '            
            <script type="text/javascript">                
                $(function(){                
                    $("#fields_' . $rules['fields_id'] . '").izoAutocomplete({                        
                        onInput: (obj,value) => {
                            obj.matchedChoices = []
                            
                            if(value.length<3)
                            {
                                obj.popup()
                                return false;
                            }
                            
                            const service = new google.maps.places.AutocompleteService();
                            
                           
                            service.getPlacePredictions({ input: value ' . $options . ' }, function (predictions, status) {
                                if (status != google.maps.places.PlacesServiceStatus.OK || !predictions) {
                                  console.error("Error in google.maps.places: "+status)
                                  return;
                                }
                                predictions.forEach((prediction) => {
                                    obj.matchedChoices.push(prediction.description)                            
                                });
                                
                                obj.popup()
                              });                                                        
                        }
                    })                                            
                })
            </script>
            ';

        return $html;
    }


}
