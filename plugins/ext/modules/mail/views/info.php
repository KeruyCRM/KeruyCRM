<ul class="page-breadcrumb breadcrumb">
    <?php
    $folders = mail_accounts::get_folders_choices();

    echo '
			<li>' . link_to($folders[$app_mail_filters['folder']], url_for('ext/mail/accounts')) . '<i class="fa fa-angle-right"></i></li>
			<li>' . (substr_count(
            $email_info['to_email'],
            ','
        ) ? '<span title="' . $email_info['to_email'] . '"><i class="fa fa-envelope-o"></i> (' . (substr_count(
                    $email_info['to_email'],
                    ','
                ) + 1) . ')</span>' : $email_info['to_email']) . '</li>';
    ?>
</ul>

<?php
$related_items = new mail_related_items($email_info['accounts_id'], $email_info['groups_id']); ?>

<div class="row">
    <div class="<?php
    echo($related_items->has_related_items() ? 'col-md-8' : 'col-md-12') ?>">

        <div class="portlet portlet-item-description">
            <div class="portlet-title">
                <div class="caption">
                    <?php
                    echo htmlspecialchars($email_info['subject']) ?>
                </div>
                <div class="tools">
                    <a href="<?php
                    echo url_for('ext/mail/info', 'action=delete_mail_group&id=' . $email_info['groups_id']) ?>"
                       title="<?php
                       echo TEXT_DELETE ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                </div>
            </div>
            <div class="portlet-body">

                <?php

                $has_star = false;
                $mail_query = db_query(
                    "select * from app_ext_mail where groups_id='" . _get::int(
                        'id'
                    ) . "' " . ($app_mail_filters['folder'] == 'trash' ? 'and in_trash=1' : 'and in_trash=0') . " and is_star=1 limit 1"
                );
                if ($mail = db_fetch_array($mail_query)) {
                    $has_star = true;
                }

                $first_email_id = 0;
                $last_email_id = 0;

                $html = '';
                $email_query = db_query(
                    "select * from app_ext_mail where groups_id='" . _get::int(
                        'id'
                    ) . "' " . ($app_mail_filters['folder'] == 'trash' ? 'and in_trash=1' : 'and in_trash=0') . " order by date_added"
                );
                $count_emails = db_num_rows($email_query);
                $count = 1;
                while ($email = db_fetch_array($email_query)) {
                    if (!$has_star and $count_emails > 4 and $count == 2) {
                        $html .= '
       <div class="email-explan-area">          	
          <div class="email-explan-area-count">' . ($count_emails - 3) . '</div>
     			<div class="email-explan-area-border"></div>
       </div>
    ';
                    }


                    $is_active = (($count_emails > 1 and $count != $count_emails)) ? true : false;;
                    $is_open = ($count == $count_emails or $email['is_star'] or in_array(
                            $email['id'],
                            $new_emails_list
                        )) ? true : false;
                    $is_hidden = ((!$has_star and $count_emails > 4 and ($count != 1 and $count != $count_emails and $count != $count_emails - 1)) ? true : false);

                    $mail_info = new mail_info($email);

                    $html .= '
	<div class="email-bar email-bar-' . $email['id'] . ($is_hidden ? ' hidden' : '') . ($is_active ? ' active' : '') . ($is_open ? ' open' : ' closed') . '">
		' . (strlen(
                            $email['error_msg']
                        ) ? '<div class="label label-warning"><i class="fa fa-exclamation-triangle"></i> ' . TEXT_EXT_MESSAGE_HAS_NOT_BEEN_SENT . ' ' . TEXT_ERROR . ' ' . $email['error_msg'] . '</div>' : '') . '
	   <div class="row email-heading-bar" data_mail_id="' . $email['id'] . '">       		
	     <div class="col-md-8 email-heading">' . (strlen(
                            $email['from_name']
                        ) ? '<span class="email-from-name">' . $email['from_name'] . '</span> <span class="email-from-email">&lt;' . $email['from_email'] . '&gt;</span>' : '<span class="email-from-name">' . $email['from_email'] . '</span>') . '</div>
	     <div class="col-md-4 email-date">' . $mail_info->render_attachments_icon() . $mail_info->render_date() . '</div>
	   </div> 		 
	   <div class="email-body-short email-body-short-' . $email['id'] . '" data_mail_id="' . $email['id'] . '">' . mb_substr(
                            strip_tags((strlen($email['body']) ? $email['body'] : $email['body_text'])),
                            0,
                            160
                        ) . '</div>			
	   <div class="email-body email-body-' . $email['id'] . '">
				<div class="row email-to-bar">
				  <div class="col-md-8 email-to-info">
						<div class="mail-list-box">' . TEXT_EXT_EMAIL_TO . ': ' . $mail_info->render_mail_to(
                        ) . '</div>' . $mail_info->render_headers() . '	   			
	   			</div>
					<div class="col-md-4 email-tools">
	   				<i class="fa fa-star mail-star ' . ($email['is_star'] ? 'mail-star-active' : '') . '" data_mail_id="' . $email['id'] . '"></i>
	     			<i class="fa fa-reply" data_mail_id="' . $email['id'] . '" title="' . TEXT_BUTTON_REPLY . '" onClick="open_dialog(\'' . url_for(
                            'ext/mail/reply',
                            'mail_id=' . $email['id']
                        ) . '\')"></i>
	     			<i class="fa fa-arrow-right" data_mail_id="' . $email['id'] . '" title="' . TEXT_BUTTON_FORWARD . '" onClick="open_dialog(\'' . url_for(
                            'ext/mail/forward',
                            'mail_id=' . $email['id']
                        ) . '\')"></i>
	     			<a href="' . url_for(
                            'ext/mail/info',
                            'action=delete_mail&id=' . $email['groups_id'] . '&mail_id=' . $email['id']
                        ) . '"><i class="fa fa-trash-o mail-trash" data_mail_id="' . $email['id'] . '" title="' . TEXT_DELETE . '"></i></a>
						<i class="fa fa-cog" data_mail_id="' . $email['id'] . '" title="' . TEXT_EXT_FILTER_MESSAGE_LIKE_THIS . '" onClick="open_dialog(\'' . url_for(
                            'ext/mail/filters_form',
                            'mail_id=' . $email['id']
                        ) . '\')"></i>
	   			</div>
				</div>
	     		' . (strlen(
                            $email['body']
                        ) ? '<iframe scrolling="no" class="email-body-iframe" id="email_body_iframe_' . $email['id'] . '" onload="javascript: resize_mail_iframe(' . $email['id'] . ');" src="' . url_for(
                                'ext/mail/info',
                                'id=' . $email['groups_id'] . '&mail_id=' . $email['id'] . '&action=get_body'
                            ) . '"></iframe>' : nl2br($email['body_text'])) . '
	     		' . $mail_info->render_attachments() . '
	   </div>  		
  ';

                    $html .= '
     </div>';

//get first eamil id		
                    if ($count == 1) {
                        $first_email_id = $email['id'];
                    }

//get last email id to reply	
                    if (!$email['is_sent'] or $count_emails == 1) {
                        $last_email_id = $email['id'];
                    }

                    $count++;
                }

                echo $html;


                //set first email ID to reply
                if (!$last_email_id) {
                    $last_email_id = $first_email_id;
                }

                echo '
	<div>
	  ' . button_tag(
                        '<i class="fa fa-reply" aria-hidden="true"></i> ' . TEXT_BUTTON_REPLY,
                        url_for('ext/mail/reply', 'mail_id=' . $last_email_id),
                        true
                    ) . '   		
	  ' . button_tag(
                        '<i class="fa fa-arrow-right" aria-hidden="true"></i> ' . TEXT_BUTTON_FORWARD,
                        url_for('ext/mail/forward', 'mail_id=' . $last_email_id),
                        true,
                        ['class' => 'btn btn-default']
                    ) . '
	</div>	   		
';

                ?>

            </div>
        </div>

        <div style="padding-bottom: 15px;">
            <?php
            echo '<a href="' . url_for(
                    'ext/mail/accounts'
                ) . '" class="btn btn-default"><i class="fa fa-angle-left" aria-hidden="true"></i> ' . TEXT_BUTTON_BACK . '</a> '; ?>
        </div>

    </div>
    <?php
    if ($related_items->has_related_items()) {
        echo '<div class="col-md-4">' . $related_items->related_items_listing() . '</div>';
    }
    ?>

</div>

<?php
require(component_path('items/load_items_listing.js')); ?>


<script>
    function resize_mail_iframe(id) {
        height = $('#email_body_iframe_' + id).contents().find('body').height()
        $('#email_body_iframe_' + id).height(height)
    }

    window.addEventListener('message', function (event) {
        if (id = event.data['id']) {
            console.log(id)
            //resize_mail_iframe(document.getElementById('email_body_iframe_'+id))
            //resize_mail_iframe(id)
            $('#email_body_iframe_' + id).height(event.data['height'])
        }
    })


    $(function () {

//expand
        $('.email-explan-area').click(function () {
            $('.email-bar').removeClass('hidden');
            $(this).hide();
        })

//email bar    
        $('.email-bar.active .email-heading-bar').click(function () {
            mail_id = $(this).attr('data_mail_id');
            if ($('.email-bar-' + mail_id).hasClass('closed')) {
                $('.email-bar-' + mail_id).removeClass('closed').addClass('open');
            } else {
                $('.email-bar-' + mail_id).removeClass('open').addClass('closed');
            }
        })

        $('.email-body-short').click(function () {
            mail_id = $(this).attr('data_mail_id');
            $('.email-bar-' + mail_id).removeClass('closed').addClass('open');
        })

//star email	
        $('.mail-star').click(function () {
            if ($(this).hasClass('mail-star-active')) {
                $(this).removeClass('mail-star-active');

                $.ajax({
                    method: 'POST',
                    url: '<?php echo url_for('ext/mail/info', 'action=unset_star&id=' . _get::int('id')) ?>',
                    data: {mail_id: $(this).attr('data_mail_id')}
                })
            } else {
                $(this).addClass('mail-star-active');

                $.ajax({
                    method: 'POST',
                    url: '<?php echo url_for('ext/mail/info', 'action=set_star&id=' . _get::int('id')) ?>',
                    data: {mail_id: $(this).attr('data_mail_id')}
                })
            }
        })

    })
</script>	  