<div class="row">
    <div class="col-md-12">
        <ul class="list-inline mail-filters">
            <li id="button_create_mail"><?php
                echo button_tag(
                    '<i class="fa fa-plus"></i> ' . TEXT_BUTTON_CREATE,
                    url_for('ext/mail/create'),
                    true
                ) ?></li>
            <li><?php
                echo select_tag(
                    'mail_folders',
                    mail_accounts::get_folders_choices(),
                    $app_mail_filters['folder'],
                    ['class' => 'form-control']
                ) ?></li>
            <?php
            if (is_mobile()) {
                echo '
                            <li>
                                <button type="button" id="mail_fetch_all" class="btn btn-default"><i class="fa fa-refresh" aria-hidden="true"></i></button>
                                <button class="btn btn-default mail-fetch-all-loading hidden"><div class="fa fa-spinner fa-spin"></div></buttton>
                            </li>';
            } else {
                echo '<li id="button_delete_selected"><button title="' . TEXT_DELETE_SELECTED . '" onClick="delete_selected_mail()" type="button" class="btn btn-default"><i class="fa fa-trash-o"></i></button></li>';
            }
            ?>

            <li id="button_empty_trash"><?php
                echo button_tag(
                    '<i class="fa fa-trash-o"></i>' . TEXT_EXT_EMPTY_TRASH,
                    url_for('ext/mail/empty_trash'),
                    true,
                    ['class' => 'btn btn-default']
                ) ?></li>

            <?php
            $account_choices = mail_accounts::get_choices_by_user('email', true, TEXT_EXT_ALL_MAIL_ACCOUNTS);
            if (count($account_choices) > 2) {
                echo '<li>' . select_tag(
                        'mail_accounts_id',
                        $account_choices,
                        $app_mail_filters['accounts_id'],
                        ['class' => 'form-control']
                    ) . '</li>';
            }
            ?>

            <li>
                <form id="mail_search_form">
                    <div class="input-group input-medium">
                        <div class="input-icon">
                            <i class="fa fa-times" id="mail_relest_search"></i>
                            <?php
                            echo input_tag(
                                'mail_search',
                                $app_mail_filters['search'],
                                ['class' => 'form-control input-medium', 'placeholder' => TEXT_SEARCH]
                            ) ?>
                        </div>
                        <span class="input-group-btn">
							<button type="submit" class="btn btn-info"><i class="fa fa-search"></i></button>
						</span>
                    </div>
                </form>
            </li>
            <li>
                <a href="<?php
                echo url_for('ext/mail/filters') ?>" class="btn btn-default" title="<?php
                echo TEXT_FILTERS ?>"><i class="fa fa-cog" aria-hidden="true"></i></a>
            </li>
        </ul>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div id="email_listing"></div>
    </div>
</div>

<script>
    function load_items_listing(listing_container, page, search_keywords) {
        $('#' + listing_container).append('<div class="data_listing_processing"></div>');
        $('#' + listing_container).css("opacity", 0.5);

        var filters = $('#track_changes_filters').serializeArray();

        $('#' + listing_container).load('<?php echo url_for("ext/mail/listing") ?>', {
                page: page,
                folder: $("#mail_folders").val(),
                accounts_id: $("#mail_accounts_id").val(),
                search: $('#mail_search').val(),
                count_accounts: <?php echo count($account_choices) ?>},
            function (response, status, xhr) {
                if (status == "error") {
                    $(this).html('<div class="alert alert-error"><b>Error:</b> ' + xhr.status + ' ' + xhr.statusText + '<div>' + response + '</div></div>')
                }

                $('#' + listing_container).css("opacity", 1);

                $('#mail_fetch_all').removeClass('hidden');
                $('.mail-fetch-all-loading').addClass('hidden')

                appHandleUniformInListing()
            }
        );
    }

    function check_action_buttons() {
        if ($('#mail_folders').val() == 'trash') {
            $('#button_empty_trash').show()
            $('#button_delete_selected').hide()
        } else {
            $('#button_empty_trash').hide()
            $('#button_delete_selected').show()
        }
    }


    $(function () {
        load_items_listing('email_listing', 1, '');

        check_action_buttons();

        $('#mail_folders').change(function () {
            load_items_listing('email_listing', 1, '');
            check_action_buttons()
        })

        $('#mail_accounts_id').change(function () {
            load_items_listing('email_listing', 1, '');
        })

        $('#mail_search_form').submit(function () {
            load_items_listing('email_listing', 1, '');
            return false;
        })

        $('#mail_relest_search').click(function () {
            $('#mail_search').val('')
            load_items_listing('email_listing', 1, '');
        })

    });


</script> 