<?php

namespace Models\Main\Items;

class Forms_wizard
{
    private $entities_id,
        $form_name,
        $entity_cfg,
        $is_form_wizard,
        $is_form_wizard_progress_bar;

    public function __construct($form_name, $entities_id, $entity_cfg)
    {
        $this->entities_id = $entities_id;
        $this->form_name = $form_name;
        $this->entity_cfg = $entity_cfg;

        //for process form 
        if (is_array($this->entity_cfg)) {
            $this->is_form_wizard = $this->entity_cfg['is_form_wizard'];
            $this->is_form_wizard_progress_bar = $this->entity_cfg['is_form_wizard_progress_bar'];
        } else {
            $this->is_form_wizard = $this->entity_cfg->get('is_form_wizard', 0);
            $this->is_form_wizard_progress_bar = $this->entity_cfg->get('is_form_wizard_progress_bar');
        }
    }

    public function is_active()
    {
        return $this->is_form_wizard;
    }

    public function ajax_modal_template_footer($action_button_title = null)
    {
        if ($action_button_title === null) {
            $action_button_title = \K::$fw->TEXT_SAVE;
        }
        $html_bar = '';

        if ($this->is_form_wizard_progress_bar == 1) {
            $html_bar = '
            <div class="row">                    
                <div class="col-md-12 col-wizard-progress">    
                    <div id="bar" class="progress progress-striped" role="progressbar">
                        <div class="progress-bar progress-bar-info" ></div>
                    </div>
                </div>
            </div>    
            ';
        }

        $html = '
            <div class="modal-footer">
                <div id="form-error-container"></div>
                
                ' . $html_bar . '
                <div class="row">    
                    <div class="col-md-6 col-xs-6" style="text-align: left">
                        <a href="#" class="btn btn-default btn-wizard-previous"><i class="fa fa-angle-left"></i> ' . \K::$fw->TEXT_PREVIOUS . '</a>
                    </div>
                    <div class="col-md-6 col-xs-6">
                        <div class="fa fa-spinner fa-spin primary-modal-action-loading"></div>	
                        <a href="#" type="button" class="btn btn-info btn-wizard-next">' . \K::$fw->TEXT_NEXT . ' <i class="fa fa-angle-right"></i></a>
                        <button type="submit" class="btn btn-primary btn-primary-modal-action btn-wizard-finish">' . $action_button_title . '</button>
                    </div>
                </div>   
                
            </div>
            
            <script>
            $(function() { 
                app_check_form_tabs_is_visible()
                
                $("#' . $this->form_name . '").bootstrapWizard({
                        tabClass: "nav nav-tabs",		
                        withVisible: false,
                        nextSelector: ".btn-wizard-next",
                        previousSelector: ".btn-wizard-previous, btn-wizard-previous2",
                        finishSelector: ".btn-wizard-finish",
                        onTabShow: function (tab, navigation, index) 
                        {
                            var total = navigation.find(\'li:not(".dropdown")\').length;
                            var current = index+1;
                            var $percent = (current / total) * 100;
                            $("#' . $this->form_name . '").find(".progress-bar").css({width: $percent + "%"}).html("' . \K::$fw->TEXT_STEP . ' " + current + " ' . \K::$fw->TEXT_OF . ' "+total);
                        },
                        onNext: function (tab, navigation, index) 
                        {
                            is_valid = true
                            tab_id = tab.attr("cfg_tab_id")
                            
                            $("#"+tab_id+" .required").each(function(){
                                if(!form_vlidator_' . $this->form_name . '.element(this))
                                {
                                    is_valid = false
                                }
                            })
                            
                            if(!is_valid)
                            {
                                return false;
                            }                                                        
                        }
                });
                
               ' . (IS_AJAX ? 'appHandleUniform()' : '') . '
                   
            });
            </script>
            ';

        return $html;
    }
}