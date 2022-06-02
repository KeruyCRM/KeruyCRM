<?php
echo ajax_modal_template_header(TEXT_ADD) ?>

<?php
echo form_tag(
    'prepare_add_item_form',
    url_for('reports/view', 'reports_id=' . $_GET['reports_id']),
    ['class' => 'form-horizontal']
) ?>

<?php
$report_info_query = db_query(
    "select r.*,e.name as entities_name,e.parent_id as entities_parent_id from app_reports r, app_entities e where e.id=r.entities_id and r.id='" . _get::int(
        'reports_id'
    ) . "'"
);
$report_info = db_fetch_array($report_info_query);

$entity_info = db_find('app_entities', $report_info['entities_id']);
$entity_cfg = entities::get_cfg($report_info['entities_id']);

$button_title = (strlen($entity_cfg['insert_button']) > 0 ? $entity_cfg['insert_button'] : TEXT_ADD);

$parent_item_id = '';

$choices = [];

//prepare default value for dropdown
if (isset($_GET["related"])) {
    $related = explode('-', $_GET["related"]);
    $path_info = items::get_path_info($related[0], $related[1]);
    $parent_item_id = $path_info['full_path'] . '/' . $entity_info['id'];

    $choices[$parent_item_id] = items::get_heading_field($related[0], $related[1]);
}

if (isset($_GET["parent_item_id"])) {
    $related = explode('-', $_GET["parent_item_id"]);
    $path_info = items::get_path_info($related[0], $related[1]);
    $parent_item_id = $path_info['full_path'] . '/' . $entity_info['id'];

    $choices[$parent_item_id] = items::get_heading_field($related[0], $related[1]);
}
?>

<div class="modal-body">
    <div class="form-body">

        <div class="ajax-modal-width-790"></div>

        <div class="form-group">
            <label class="col-md-3 control-label" for="entities_id"><?php
                echo TEXT_ADD_IN ?></label>
            <div class="col-md-9">
                <?php
                echo select_tag('parent_item_id', $choices, $parent_item_id, ['class' => 'required']) ?>

                <label id="parent_item_id-error" class="error" for="parent_item_id"></label>
                <script>
                    $("#parent_item_id").on("change", function (e) {
                        $("#parent_item_id-error").hide();
                    });
                </script>
            </div>
        </div>

    </div>
</div>

<?php
echo ajax_modal_template_footer($button_title) ?>

</form>
<?php

$html = '
    <script>
        $(function(){	

        $("#parent_item_id").select2({		      
                    width: "100%",		      
                    dropdownParent: $("#ajax-modal"),
                    "language":{
                      "noResults" : function () { return "' . addslashes(TEXT_NO_RESULTS_FOUND) . '"; },
                                "searching" : function () { return "' . addslashes(TEXT_SEARCHING) . '"; },
                                "errorLoading" : function () { return "' . addslashes(
        TEXT_RESULTS_COULD_NOT_BE_LOADED
    ) . '"; },
                                "loadingMore" : function () { return "' . addslashes(TEXT_LOADING_MORE_RESULTS) . '"; }		    				
                    },	
                    allowClear: true,
                    placeholder: \'' . addslashes(TEXT_PLEASE_SELECT_ITEMS) . '\',
                    ajax: {
                            url: "' . url_for('reports/select2_prepare_add_item_json', 'action=select_items') . '",
                            dataType: "json",  
                            delay: 250,
                            type: "POST",
                            data: function (params) {
                                var query = {
                                  search: params.term,
                                  page: params.page || 1, 
                                  entity_id: ' . $entity_info['id'] . ',
                                  parent_entity_id: ' . $entity_info['parent_id'] . ',
                                }

                              // Query parameters will be ?search=[term]&page=[page]
                              return query;
                            },        				        				
                        },        				
                                templateResult: function (d) { return $(d.html); },      		        			
                        });

                    $("#parent_item_id").change(function (e) {
                        $("#parent_item_id-error").remove();
                    });

                })
        </script>
';

echo $html;


$params = "redirect_to=report_" . $report_info["id"];

if (strstr($app_redirect_to, 'calendarreport') or strstr($app_redirect_to, 'pivot_calendars') or strstr(
        $app_redirect_to,
        'resource_timeline'
    )) {
    $params = "redirect_to=" . $app_redirect_to . '&start=' . $_GET['start'] . '&end=' . $_GET['end'] . '&view_name=' . $_GET['view_name'];

    if (isset($_GET['resource_id'])) {
        $params .= '&resource_id=' . _GET('resource_id');
    }
} elseif (strstr($app_redirect_to, 'item_info_page') or strstr($app_redirect_to, 'kanban')) {
    $params = "redirect_to=" . $app_redirect_to . (isset($_GET['fields']) ? '&fields[' . key(
                $_GET['fields']
            ) . ']=' . current($_GET['fields']) : '');
}

if (isset($_GET['mail_groups_id'])) {
    $params .= '&mail_groups_id=' . $_GET['mail_groups_id'];
}


?>
<script>
    $(function () {

        $('#prepare_add_item_form').validate({
            ignore: '',
            submitHandler: function (form) {
                path = $('#parent_item_id').val();
                url = '<?php echo url_for(
                    "items/form",
                    $params . (isset($_GET["related"]) ? "&related=" . $_GET["related"] : "")
                ) ?>' + '&path=' + path;

                //close berfore open to apply modal width for next window
                $('#ajax-modal').modal('toggle');

                //open width dealy to wait once previous window will be closed
                setTimeout(function () {
                    open_dialog(url)
                }, 500)

                return false;
            }
        });

    });
</script>
