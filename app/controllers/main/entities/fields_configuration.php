<?php
/*
 * KeruyCRM (c)
 * https://keruy.com.ua
 */

namespace Controllers\Main\Entities;

class Fields_configuration extends \Controller
{
    private $app_layout = 'layout.php';

    public function __construct()
    {
        parent::__construct();
        \K::security()->checkCsrfToken();

        \Controllers\Main\Entities\_Module::top();
    }

    public function index()
    {
        if (isset(\K::$fw->POST['field_type'])) {
            $class = \K::$fw->POST['field_type'];

            if (class_exists($class)) {
                $field_type = new $class();

                //echo '<h3 class="form-section">' . fields_types::get_tooltip($_POST['field_type']) . '</h3>';

                $tooltip = \Models\Main\Fields_types::get_tooltip(\K::$fw->POST['field_type']);

                if (strlen($tooltip)) {
                    echo '
	    <div class="form-group">
	    	<label class="col-md-3 control-label">' . \K::$fw->TEXT_INFO . '</label>
	      <div class="col-md-9"><p class="form-control-static">' . $tooltip . '</p>
	      </div>			                                                                                                   
	    </div>
	  ';
                }

                if (method_exists($field_type, 'get_configuration')) {
                    echo \Models\Main\Fields_types::render_configuration(
                        $field_type->get_configuration(['entities_id' => \K::$fw->POST['entities_id']]),
                        \K::$fw->POST['id']
                    );
                }
            }
        }
    }
}