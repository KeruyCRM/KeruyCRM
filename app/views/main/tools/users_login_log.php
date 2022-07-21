<?php

if (!defined('KERUY_CRM')) {
    exit;
} ?>
<h3 class="page-title"><?= \K::$fw->TEXT_USERS_LOGIN_LOG ?></h3>


<form class="form-inline" role="form" id="users_login_log_filters" action="#" method="post">
    <div class="form-group">
        <label for="type"><?= \K::$fw->TEXT_TYPE ?></label>
        <?= \Helpers\Html::select_tag(
            'type',
            \Models\Main\Users\Users_login_log::get_type_choiсes(),
            '',
            ['class' => 'form-control']
        ) ?>
    </div>

    <div class="form-group">
        <table>
            <tr>
                <td><label for="created_by">&nbsp;<?= \K::$fw->TEXT_USERS ?></label></td>
                <td><?= \Helpers\Html::select_tag(
                        'users_id',
                        ['' => \K::$fw->TEXT_NONE] + \Models\Main\Users\Users::get_choices(),
                        (isset(\K::$fw->GET['users_id']) ? (int)\K::$fw->GET['users_id'] : ''),
                        ['class' => 'form-control input-large chosen-select']
                    ) ?></td>
            </tr>
        </table>
    </div>

    <div class="form-group" style="float:right">
        <?= '<a class="btn btn-default" href="' . \Helpers\Urls::url_for(
            "main/tools/users_login_log/reset",
            '',
            true
        ) . '" onclick="return confirm(\'' . htmlspecialchars(
            \K::$fw->TEXT_ARE_YOU_SURE
        ) . '\')">' . \K::$fw->TEXT_DELETE_DATA . '</a>'; ?>
    </div>

</form>

<script>
    $(function () {
        $('#users_login_log_filters .form-control').change(function () {
            load_items_listing('users_login_log_listing', 1)
        })
    })
</script>

<div class="row">
    <div class="col-md-12">
        <div id="users_login_log_listing"></div>
    </div>
</div>

<script>
    function load_items_listing(listing_container, page, search_keywords) {
        $('#' + listing_container).append('<div class="data_listing_processing"></div>');
        $('#' + listing_container).css("opacity", 0.5);

        var filters = $('#users_login_log_filters').serializeArray();

        $('#' + listing_container).load('<?= \Helpers\Urls::url_for("main/tools/users_login_log/listing") ?>', {
                page: page,
                filters: filters
            },
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
        load_items_listing('users_login_log_listing', 1, '');
    });

</script> 