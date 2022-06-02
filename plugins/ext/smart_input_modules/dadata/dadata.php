<?php

class dadata
{

    public $title;
    public $site;
    public $types_choices;

    function __construct()
    {
        $this->title = TEXT_MODULE_DADATA_TITLE;
        $this->site = 'https://dadata.ru';
        $this->api = 'https://dadata.ru/suggestions/usage/';
        $this->version = '4.0';
        $this->country = 'RU';

        $this->types_choices = [];
        $this->types_choices['ADDRESS'] = TEXT_MODULE_DADATA_TYPE_ADDRESS;
        $this->types_choices['PARTY'] = TEXT_MODULE_DADATA_TYPE_PARTY;
        $this->types_choices['BANK'] = TEXT_MODULE_DADATA_TYPE_BANK;
        $this->types_choices['NAME'] = TEXT_MODULE_DADATA_TYPE_NAME;
        $this->types_choices['EMAIL'] = TEXT_MODULE_DADATA_TYPE_EMAIL;

        $this->types_choices['country'] = TEXT_MODULE_DADATA_TYPE_COUNTRY;
        $this->types_choices['currency'] = TEXT_MODULE_DADATA_TYPE_CURRENCY;
        $this->types_choices['postal_office'] = TEXT_MODULE_DADATA_TYPE_POSTAL_OFFICE;
        $this->types_choices['fns_unit'] = TEXT_MODULE_DADATA_TYPE_FNS_UNIT;
        $this->types_choices['okved2'] = TEXT_MODULE_DADATA_TYPE_OKVED2;
        $this->types_choices['okpd2'] = TEXT_MODULE_DADATA_TYPE_OKPD2;
        $this->types_choices['fms_unit'] = TEXT_MODULE_DADATA_TYPE_FMS_UNIT;
        $this->types_choices['car_brand'] = TEXT_MODULE_DADATA_TYPE_CAR_BRAND;
    }

    public function configuration()
    {
        $cfg = [];

        $cfg[] = [
            'key' => 'api_key',
            'type' => 'input',
            'default' => '',
            'title' => TEXT_EXT_API_KEY,
            'params' => ['class' => 'form-control input-large required'],
        ];

        $cfg[] = [
            'key' => 'count',
            'type' => 'input',
            'default' => '5',
            'title' => TEXT_MODULE_DADATA_MAX_COUNT,
            'description' => TEXT_MODULE_DADATA_MAX_COUNT_INFO,
            'params' => ['class' => 'form-control input-small'],
        ];

        $cfg[] = [
            'key' => 'minChars',
            'type' => 'input',
            'default' => '1',
            'title' => TEXT_MODULE_DADATA_MIN_CHARS,
            'description' => TEXT_MODULE_DADATA_MIN_CHARS_INFO,
            'params' => ['class' => 'form-control input-small'],
        ];

        return $cfg;
    }

    public function render_itnegration_type_name($type)
    {
        return (isset($this->types_choices[$type]) ? $this->types_choices[$type] : $type);
    }

    public function render_itnegration_types($type)
    {
        $html = '
        			<div class="form-group">
						  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . TEXT_TYPE . '</label>
						    <div class="col-md-9">
						  	  ' . select_tag(
                'type',
                $this->types_choices,
                $type,
                ['class' => 'form-control input-large required']
            ) . '
						    </div>
						  </div>
        			';

        return $html;
    }

    public function render_itnegration_rules($rules, $entity_field_html = '')
    {
        $html = $entity_field_html . '
        			<div class="form-group">
						  	<label class="col-md-3 control-label" for="cfg_sms_send_to_record_number">' . TEXT_RULE_FOR_FIELD . '</label>
						    <div class="col-md-9">
						  	  ' . textarea_tag('rules', $rules, ['class' => 'form-control input-xlarge']) . '
						  	  ' . tooltip_text(TEXT_MODULE_DADATA_RULES_INFO) . '
						    </div>
						  </div>
        			';

        return $html;
    }

    public function render_js_includes($module_id)
    {
        $html = '
			<link href="https://cdn.jsdelivr.net/npm/suggestions-jquery@18.11.1/dist/css/suggestions.min.css" type="text/css" rel="stylesheet" />		
			<!--[if lt IE 10]>
			<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ajaxtransport-xdomainrequest/1.0.1/jquery.xdomainrequest.min.js"></script>
			<![endif]-->
			<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/suggestions-jquery@18.11.1/dist/js/jquery.suggestions.min.js"></script>		
		';

        return $html;
    }

    public function render($module_id, $rules)
    {
        $html = '';

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $html .= '
				<script type="text/javascript">
					$(function(){
						$("#fields_' . $rules['fields_id'] . '").suggestions({
					        token: "' . $cfg['api_key'] . '",
					        type: "' . $rules['type'] . '",
					        count: ' . (($cfg['count'] > 0 and $cfg['count'] < 20) ? $cfg['count'] : 5) . ',
					        minChars: ' . ($cfg['minChars'] > 0 ? $cfg['minChars'] : 1) . ',
					        		
					        /* Вызывается, когда пользователь выбирает одну из подсказок */
					        onSelect: function(suggestion) {
					            //console.log(suggestion);
					        		' . $this->render_on_select(trim($rules['rules'])) . '					        		
					        }
					    });
					})				    
				</script>
				';

        return $html;
    }

    public function render_on_select($rules)
    {
        $html = '';

        if (strlen($rules)) {
            foreach (preg_split('/\r\n|\r|\n/', $rules) as $value) {
                $value_array = explode('=', $value);
                $field_id = trim(str_replace(['[', ']'], '', $value_array[0]));
                $value = trim($value_array[1]);

                if (strstr($value, '_date') or strstr($value, 'valid_from') or strstr($value, 'valid_to')) {
                    $html .= '
                            try{

                                    var value = "";

                                    if(suggestion.' . $value . ')
                                    {
                                            var date = new Date(suggestion.' . $value . ');
                                            month = date.getMonth()+1
                                            value	= date.getFullYear()+"-"+(month<9 ? "0"+month:month)+"-"+(date.getDate()<9 ? "0"+date.getDate():date.getDate());							
                                    }

                                    $("#fields_' . $field_id . '").val(value);
                            }			
                            catch (err)
                            {
                                    console.error(err)
                            }									
                            ' . "\n";
                } else {
                    $html .= '
                            try{
                                    $("#fields_' . $field_id . '").val(suggestion.' . $value . ').trigger("focusout");
                            }			
                            catch (err)
                            {
                                    console.error(err)
                            }			
                            ';
                }
            }
        }

        return $html;
    }

}
