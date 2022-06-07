<script>
    var form_vlidator_<?php echo $app_items_form_name ?> = false

    $(function() {

//add method to not accept space  	
    jQuery.validator.addMethod("noSpace", function (value, element) {
        return value == '' || value.trim().length != 0;
    }, '<?php echo addslashes(TEXT_ERROR_REQUIRED) ?>');

    jQuery.validator.addMethod("digitsCustom", function(value, element) {
    return this.optional(element) || /^-?\d+/.test(value);
}, '<?php echo addslashes(TEXT_ERROR_REQUIRED_DIGITS) ?>');

//start form validation                    
    form_vlidator_<?php echo $app_items_form_name ?> = $('#<?php echo $app_items_form_name ?>').validate({ignore:'.ignore-validation',

    //rules for ckeditor
    rules:{
    <?php echo fields::render_required_ckeditor_rules($current_entity_id); ?>
    <?php echo fields::render_unique_fields_rules($current_entity_id, $current_item_id ?? 0); ?>
},

    //custom error messages
    messages: {
    <?php echo fields::render_required_messages($current_entity_id); ?>
},

    submitHandler: function(form)
{

    $('.btn-primary-modal-action',form).prop('disabled',true);

    //include custom js code in submit handler
    <?php echo(strlen(trim($entity_cfg->get('javascript_onsubmit'))) ? $app_global_vars->apply_to_text(
    $entity_cfg->get('javascript_onsubmit')
) : '') ?>

    //replace submit button to Loading to stop double submit
    app_prepare_modal_action_loading(form)

    //update ckeditor fields
    if(CKEDITOR.instances)
{
    for ( instance in CKEDITOR.instances )
{
    CKEDITOR.instances[instance].updateElement();
}
}

    <?php

    //handle users validation
    if ($current_entity_id == 1) {
        if ($app_redirect_to == 'parent_modal') {
            echo '                                        
                    $.ajax({
                        type: "POST",
                        url: "' . url_for('users/validate_form', (isset($_GET['id']) ? 'id=' . $_GET['id'] : '')) . '",
                        data: { username: $("#fields_12").val(), useremail: $("#fields_9").val(),password: $("#password").val() }
                      })
                      .done(function( msg ) {                          
                          msg = msg.trim()      
                          if(msg=="success")
                          {
                              $.ajax({type: "POST",
                                url: $("#' . $app_items_form_name . '").attr("action"),
                                data: $("#' . $app_items_form_name . '").serializeArray()
                                }).done(function(item_id) {
                                	field_id = $("#sub-items-form").attr("data-field-id")
                                	parent_entity_item_id = $("#sub-items-form").attr("data-parent-entity-item-id")
                  		     
                                	current_field_values = $("#fields_"+field_id).val();
                  		     
                                	$("#fields_"+field_id+"_rendered_value").html(\'<div style="width: 18px;"><div class="ajax-loading-small"></div></div>\')
                                	$("#fields_"+field_id+"_rendered_value").load("' . url_for(
                    'items/render_field_value',
                    'path=' . $app_path
                ) . '&fields_id="+field_id+"&item_id="+item_id+"&parent_entity_item_id="+parent_entity_item_id+"&current_field_values="+current_field_values)
                          
                                	close_sub_dialog()
                                });
                          }
                          else
                          {
                            $("div#form-error-container").html("<div class=\"note note-danger\">"+msg+"</div>");
                      			$("div#form-error-container").show();
                            $("div#form-error-container").delay(5000).fadeOut();
                            
                            $(".btn-primary-modal-action").show();
                            $(".primary-modal-action-loading").css("visibility","hidden");
                            $(".btn-primary-modal-action",form).prop("disabled",false);
                          }
                      });
                  ';
        } else {
            echo 'validate_user_form(form,\'' . url_for(
                    'users/validate_form',
                    (isset($_GET['id']) ? 'id=' . $_GET['id'] : '')
                ) . '\');';
        }
    } //handle add item from gantt
    elseif (strstr($app_redirect_to, 'ganttreport')) {
        echo '
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray()
                }).done(function(data) {
                  $("#ajax-modal").modal("hide")
                   gantt_save(data);
                });
            ';
    } //handle add item from clalendar
    elseif (strstr($app_redirect_to, 'calendarreport')) {
        echo '
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray() 
                }).done(function() {
                  $("#ajax-modal").modal("hide")
                  $("#calendar' . str_replace('calendarreport', '', $app_redirect_to) . '").fullCalendar("refetchEvents");
                });
            ';
    } //handle add item from pivot clalendar
    elseif (strstr($app_redirect_to, 'pivot_calendars')) {
        $calendar_entity_id = str_replace('pivot_calendars', '', $app_redirect_to);
        $calendar_id = pivot_calendars::get_calendar_id_by_calendar_entity($calendar_entity_id);
        echo '
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray()
                }).done(function() {
                  $("#ajax-modal").modal("hide")
                  $("#calendar' . $calendar_id . '").fullCalendar("refetchEvents");
                });
            ';
    } elseif (strstr($app_redirect_to, 'resource_timeline')) {
        $calendar_entity_id = str_replace('resource_timeline', '', $app_redirect_to);
        $calendar_id = resource_timeline::get_calendar_id_by_calendar_entity($calendar_entity_id);
        echo '
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray()
                }).done(function() {
                  $("#ajax-modal").modal("hide")
                  $("#resource_timeline' . $calendar_id . '").fullCalendar("refetchEvents");
                });
            ';
    } //handle subentity form
    elseif (strstr($app_redirect_to, 'subentity_form')) {
        $subentity_form_params = explode('_', str_replace('subentity_form_', '', $app_redirect_to));
        $subentity_form_add_url = url_for(
            'subentity/form',
            'path=' . $subentity_form_params[0] . '&action=add_item&entities_id=' . $subentity_form_params[0] . '&fields_id=' . $subentity_form_params[1] . '&redirect_to=' . $app_redirect_to
        );
        $subentity_form_load_url = url_for(
            'subentity/form',
            'path=' . $subentity_form_params[0] . '&action=load_items&entities_id=' . $subentity_form_params[0] . '&fields_id=' . $subentity_form_params[1] . '&form_name=' . ($app_user['id'] == 0 ? 'public_form' : $app_items_form_name)
        );
        echo '
              $.ajax({type: "POST",
                url: "' . $subentity_form_add_url . '",
                data: $("#' . $app_items_form_name . '").serializeArray()
                }).done(function(item_id) {                		
                    $("#subentity_form' . $subentity_form_params[1] . '").load("' . $subentity_form_load_url . '",function(){
                        app_handle_submodal_open_btn("subentity_form' . $subentity_form_params[1] . '")
                        subentity_form' . $subentity_form_params[1] . '_check()
                    })
                    
                    close_sub_dialog()
                });
            ';
    } //handle sub items form submit
    elseif ($app_redirect_to == 'parent_modal') {
        echo '
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray()
                }).done(function(item_id) {
                	field_id = $("#sub-items-form").attr("data-field-id")	                	
                	parent_entity_item_id = $("#sub-items-form").attr("data-parent-entity-item-id")
                   
                	current_field_values = $("#fields_"+field_id).val();
                		
                	$("#fields_"+field_id+"_rendered_value").html(\'<div style="width: 18px;"><div class="ajax-loading-small"></div></div>\')
                	$("#fields_"+field_id+"_rendered_value").load("' . url_for(
                'items/render_field_value',
                'path=' . $app_path
            ) . '&fields_id="+field_id+"&item_id="+item_id+"&parent_entity_item_id="+parent_entity_item_id+"&current_field_values="+current_field_values)
                		
                	close_sub_dialog()
                });
            ';
    } elseif ($entity_cfg->get(
            'redirect_after_adding'
        ) == 'form' and !isset($_GET['id']) and ($app_redirect_to == '' or substr(
                $app_redirect_to,
                0,
                7
            ) == 'report_' or $app_redirect_to == 'parent_item_info_page')) {
        $reports_id = 0;

        if (substr($app_redirect_to, 0, 7) == 'report_') {
            $reports_id = str_replace('report_', '', $app_redirect_to);
        } elseif ($app_redirect_to == 'parent_item_info_page') {
            $report_query = db_query(
                "select * from app_reports where entities_id='" . $current_entity_id . "' and reports_type='parent_item_info_page'"
            );
            if ($report = db_fetch_array($report_query)) {
                $reports_id = $report['id'];
            }
        } else {
            $reports_info = reports::create_default_entity_report($current_entity_id, 'entity', $current_path_array);
            $reports_id = $reports_info['id'];
        }

        $listing_container = 'entity_items_listing' . $reports_id . '_' . $current_entity_id;

        echo '              
              $.ajax({type: "POST",
                url: $("#' . $app_items_form_name . '").attr("action"),
                data: $("#' . $app_items_form_name . '").serializeArray()
                }).done(function() {
                  if($("#' . $app_items_form_name . '").attr("save_and_close")==1)
                  {
                    $("#ajax-modal").modal("hide")
                  }
                  else
                  {
                    open_dialog(\'' . url_for(
                'items/form',
                'path=' . $app_path . '&redirect_to=' . $app_redirect_to . '&save_success_msg=1'
            ) . '\')   
                  }
                  load_items_listing(\'' . $listing_container . '\',1);    
                });
            ';
    } //default form submit if no errors
    else {
        echo 'form.submit();';
    }
    ?>
},

    //custom erro placment to handle radio etc. 
    errorPlacement: function(error, element) {
    if (element.attr("type") == "radio")
{
    error.insertAfter(element.parents(".radio-list"))
}
    else if (element.attr("type") == "checkbox")
{
    error.insertAfter(element.parents(".checkbox-list"))
}
    else if(element.hasClass('single-checkbox'))
{
    error.insertAfter(".single-checkbox-"+element.attr("id"));
}
    else if(element.hasClass('fieldtype_entity_ajax') || element.hasClass('fieldtype_entity_multilevel'))
{
    error.insertAfter("#"+element.attr("id")+"_select2_on");
}
    else
{
    error.insertAfter(element);
}
},

    //custom invalid handler
    invalidHandler: function(e, validator) {
    var errors = validator.numberOfInvalids();
    if (errors)
{
    var message = '<?php echo TEXT_ERROR_GENERAL ?>';

    $("#<?php echo $app_items_form_name ?> #form-error-container").html('<div class="alert alert-danger">'+message+'</div>').show().delay(5000).fadeOut();

    //auto open tabs with erros
    app_highlight_form_tab_name_with_errors('<?php echo $app_items_form_name ?>')
}
}
});
//end form validation    


    /*
     * start vpic vin decoder
     */
    $('.vpic-vin-decoder').click(function(){
    field_id = $(this).attr('data-field-id');
    vin_number = $('#fields_'+field_id).val()
    $('#field_'+field_id+'_vin_data').html('<div class="fa-ajax-loader fa fa-spinner fa-spin"></div>');
    $('#field_'+field_id+'_vin_data').load('<?php echo url_for(
    'dashboard/vpic',
    'action=input_vin_decode'
) ?>',{field_id:field_id,vin_number:vin_number})
})
    /* end vpic vin decoder */


//start btn-submodal-open
    app_handle_submodal_open_btn('<?php echo $app_items_form_name ?>')


//end btn-submodal-open


//curecny convert
    app_currency_converter('#<?php echo $app_items_form_name ?>')

//check if there is no active tab
    app_check_active_form_tab('#<?php echo $app_items_form_name ?>')

//check visible tabs
    app_check_form_tabs_is_visible()
});

</script>


<!-- include form fields display rules  -->
<?php require(component_path('items/forms_fields_rules.js')); ?>

<?php
//insert custom javascript code
if (strlen(trim($entity_cfg->get('javascript_in_from')))) {
    echo '
        <script>
                ' . $app_global_vars->apply_to_text($entity_cfg->get('javascript_in_from')) . '
        </script>
        ';
}
?>
	                                                                      