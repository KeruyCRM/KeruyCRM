<h3 class="page-title"><?php
    echo $app_reports['name'] ?></h3>

<?php
require(component_path('ext/track_changes/filters')) ?>

<div class="row">
    <div class="col-md-12">
        <div id="track_changes_listing"></div>
    </div>
</div>

<script>
    function load_items_listing(listing_container, page, search_keywords) {
        $('#' + listing_container).append('<div class="data_listing_processing"></div>');
        $('#' + listing_container).css("opacity", 0.5);

        var filters = $('#track_changes_filters').serializeArray();

        $('#' + listing_container).load('<?php echo url_for(
                "ext/track_changes/view",
                'reports_id=' . $app_reports['id'] . '&action=listing'
            ) ?>', {page: page, filters: filters},
            function (response, status, xhr) {
                if (status == "error") {
                    $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                }

                $('#' + listing_container).css("opacity", 1);

                appHandleUniformInListing()
            }
        );
    }


    $(function () {
        load_items_listing('track_changes_listing', 1, '');
    });


</script> 
