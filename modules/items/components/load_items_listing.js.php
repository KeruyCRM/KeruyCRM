<script>
    function load_items_listing(listing_container,page)
    {
        //parse listing id
        listing_data = listing_container.replace('entity_items_listing', '').split('_');

        //set default redirect
        redirect_to = 'report_'+listing_data[0];

        //set default path
        path = listing_data[1];

        //replace default path by current path
        if($('#entity_items_listing_path').length)
    {
        path = $('#entity_items_listing_path').val()
        redirect_to = '';
    }

        //replace default path by subentity path in info page
        if($('#subentity'+parseInt(path)+'_items_listing_path').length)
    {
        path = $('#subentity'+path+'_items_listing_path').val()
        redirect_to = 'parent_item_info_page';
    }

        //force custom redirect
        if($('#'+listing_container+'_redirect_to').length)
    {
        redirect_to = $('#'+listing_container+'_redirect_to').val();
    }

        //set redirect to dashboard if it's dashboard page
        if($('#dashboard-reports-container').length)
    {
        redirect_to = 'dashboard';
    }

        if($('#dashboard-reports-group-container').length)
    {
        redirect_to = 'reports_groups'+$('#dashboard-reports-group-container').attr('data_id');
    }

        $('#'+listing_container).append('<div class="data_listing_processing"></div>');

        $('#'+listing_container).css("opacity", 0.5);

        //prepare search fields id
        var use_search_fields = [];
        $.each($("."+listing_container+"_use_search_fields:checked"), function(){
        use_search_fields.push($(this).val());
    });

        $('#'+listing_container).load('<?php echo url_for("items/listing")?>',
    {
        redirect_to: redirect_to,
        path:path,
        reports_entities_id:listing_data[1],
        reports_id:listing_data[0],
        listing_container:listing_container,
        page:page,
        search_keywords:$('#'+listing_container+'_search_keywords').val(),
        use_search_fields: use_search_fields.join(','),
        search_in_comments: $('#'+listing_container+'_search_in_comments').prop('checked'),
        search_in_all: $('#'+listing_container+'_search_in_all').prop('checked'),
        search_type_and: $('#'+listing_container+'_search_type_and').prop('checked'),
        search_type_match: $('#'+listing_container+'_search_type_match').prop('checked'),
        search_reset:$('#'+listing_container+'_search_reset').val(),
        listing_order_fields:$('#'+listing_container+'_order_fields').val(),
        listing_order_fields_changed:$('#'+listing_container+'_order_fields').attr('is_changed'),
        has_with_selected:$('#'+listing_container+'_has_with_selected').val(),
        force_display_id:$('#'+listing_container+'_force_display_id').val(),
        force_popoup_fields:$('#'+listing_container+'_force_popoup_fields').val(),
        force_filter_by:$('#'+listing_container+'_force_filter_by').val(),
    },
        function(response, status, xhr) {
        if (status == "error") {
        $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText+'<div>'+response +'</div></div>')
    }

        $('#'+listing_container).css("opacity", 1);

        appHandleUniformInListing()

        //prevent double click on button
        $('.prevent-double-click').click(function(){
        $(this).attr('disabled','disabled')
    })

        handle_itmes_select(listing_container)

        app_handle_listing_horisontal_scroll($(this))

        app_handle_listing_fixed_table_header($(this),'<?php echo $app_module_path ?>')


        //editable cell
        $('#'+listing_container+' .editable-cell').click(function(e){
        if (e.target.tagName.toLowerCase() != 'a' && e.target.tagName.toLowerCase() != 'sup')
    {
        $(this).closest('tr').addClass('editable-cell-row-selected')

        open_dialog("<?php echo url_for(
        'items/form_single_field'
    )?>&path="+$(this).attr('data_path')+'&field_id='+$(this).attr('data_field_id')+'&report_id='+$(this).attr('data_report_id')+'&page='+$(this).attr('data_page'))

        let that  = $(this);

        $('#ajax-modal').on('hidden.bs.modal', function () {
        that.closest('tr').removeClass('editable-cell-row-selected')
    })
    }
    })

        //select row
        $('#'+listing_container+' .items_checkbox').each(function(){
        if($(this).attr('checked')=='checked')
    {
        $(this).closest('tr').addClass('row-selected')
    }
    })

        <?php
        if (isset($reports_info)) {
            echo '
                    app_handle_listing_resizer($(this),"' . url_for(
                    'reports/reports',
                    'action=set_listing_col_width&reports_id=' . $reports_info['id']
                ) . '");
        
                    app_handle_listing_slimscroll($(this),"' . $app_module_path . '");
  					';
        }
        ?>
    }
        );


    }

    function handle_itmes_select_all(listing_container)
    {
        $('#' + listing_container + ' .select_all_items').click()
    }

    function handle_itmes_select_currnt_page(listing_container)
    {
        $('#' + listing_container + ' .select_all_items_current_page').click()
    }

    function handle_itmes_select_reset(listing_container)
    {
        $('#' + listing_container + ' .select_all_items').attr('checked', true);
        $('#'+listing_container+' .select_all_items_current_page').attr('checked',false);
        $('#'+listing_container+' .select_all_items').click()
    }

    function handle_itmes_select(listing_container)
    {
        //select on current page
        $('#' + listing_container + ' .select_all_items_current_page').click(function () {

            listing_data = listing_container.replace('entity_items_listing', '').split('_');

            reports_id = listing_data[0];

            if ($(this).attr('checked') == 'checked') {
                items = [];
                $('#' + listing_container + ' .items_checkbox').each(function () {
                    if ($(this).attr('checked') != 'checked') {
                        $(this).attr('checked', true)
                        $('#' + listing_container + ' #uniform-items_' + $(this).val() + ' span').addClass('checked')
                        $(this).closest('tr').addClass('row-selected')
                        items.push($(this).val())
                    }
                })

                $.ajax({
                    type: "POST",
                    url: '<?php echo url_for("items/select_items", "action=select_on_page")?>',
                    data: {items: items, reports_id: reports_id, path: listing_data[1]}
                });
            } else {
                items = [];
                $('#' + listing_container + ' .items_checkbox').each(function () {
                    $(this).attr('checked', false)
                    $('#' + listing_container + ' #uniform-items_' + $(this).val() + ' span').removeClass('checked')
                    $(this).closest('tr').removeClass('row-selected')
                    items.push($(this).val())
                })

                $.ajax({
                    type: "POST",
                    url: '<?php echo url_for("items/select_items", "action=deselect_on_page")?>',
                    data: {items: items, reports_id: reports_id, path: listing_data[1]}
                });
            }
        })


        //select single item
        $('#'+listing_container+' .items_checkbox').click(function(){

        listing_data = listing_container.replace('entity_items_listing','').split('_');

        //set default path
        path = listing_data[1];

        if($('#entity_items_listing_path').length){
        listing_data[1] = $('#entity_items_listing_path').val()
    }

        reports_id = listing_data[0];

        if($('#'+listing_container+'_use_reports_id').length)
    {
        reports_id = $('#'+listing_container+'_use_reports_id').val();
    }

        //select row
        if($(this).attr('checked')=='checked')
    {
        $(this).closest('tr').addClass('row-selected')
    }
        else
    {
        $(this).closest('tr').removeClass('row-selected')
    }

        $.ajax({type: "POST",url: '<?php echo url_for(
        "items/select_items",
        "action=select"
    )?>',data: {id:$(this).val(),checked: $(this).attr('checked'),reports_id: reports_id,path:listing_data[1]}});
    })

        //force select all itesm if listing type is not table
        $('.' + listing_container +'_select_all_items_force').click(function(){
        data_conteiner = $(this).attr('data-container-id');
        $('#'+data_conteiner+' .select_all_items').trigger( "click" );
        $(this).before('<div class="ajax-loading-small"></div>');
    })


        //select all items
        $('#'+listing_container+' .select_all_items').click(function(){

        listing_data = listing_container.replace('entity_items_listing','').split('_');

        //set default path
        path = listing_data[1];

        if($('#entity_items_listing_path').length){
        path = $('#entity_items_listing_path').val()
    }

        //replace default path by subentity path in info page
        if($('#subentity'+parseInt(path)+'_items_listing_path').length)
    {
        path = $('#subentity'+path+'_items_listing_path').val()
    }

        //prepare search fields id
        var use_search_fields = [];
        $.each($("."+listing_container+"_use_search_fields:checked"), function(){
        use_search_fields.push($(this).val());
    });


        //add loading
        $(this).before('<div class="ajax-loading-small"></div>');

        var obj = $(this);

        use_reports_id = 0
        if($('#'+listing_container+'_use_reports_id').length)
    {
        use_reports_id = $('#'+listing_container+'_use_reports_id').val();
    }
        //alert(use_reports_id)

        $.ajax({type: "POST",
        url: '<?php echo url_for("items/select_items", "action=select_all")?>',
        data: {
        id:$(this).val(),
        checked: $(this).attr('checked'),
        reports_id: $(this).val(),
        use_reports_id:use_reports_id,
        path:path,
        is_tree_view: $(this).attr('is_tree_view'),
        search_keywords:$('#'+listing_container+'_search_keywords').val(),
        use_search_fields: use_search_fields.join(','),
        search_in_comments: $('#'+listing_container+'_search_in_comments').prop('checked'),
        search_in_all: $('#'+listing_container+'_search_in_all').prop('checked'),
        search_type_and: $('#'+listing_container+'_search_type_and').prop('checked'),
        search_type_match: $('#'+listing_container+'_search_type_match').prop('checked'),
        listing_order_fields:$('#'+listing_container+'_order_fields').val(),
        force_display_id:$('#'+listing_container+'_force_display_id').val(),
        force_filter_by:$('#'+listing_container+'_force_filter_by').val(),
    }}).done(function(){

        $('.ajax-loading-small').remove()

        if(obj.attr('checked'))
    {
        $('#'+listing_container+' .items_checkbox').each(function(){
        $(this).attr('checked',true)
        $('#'+listing_container+' #uniform-items_'+$(this).val()+' span').addClass('checked')

        $(this).closest('tr').addClass('row-selected')
    })
    }
        else
    {
        $('#'+listing_container+' .items_checkbox').each(function(){
        $(this).attr('checked',false)
        $('#'+listing_container+' #uniform-items_'+$(this).val()+' span').removeClass('checked')

        $(this).closest('tr').removeClass('row-selected')
    })
    }

    });


    })
    }
</script> 