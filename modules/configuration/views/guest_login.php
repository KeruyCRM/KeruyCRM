<h3 class="page-title"><?php
    echo TEXT_GUEST_LOGIN ?></h3>

<p><?php
    echo TEXT_GUEST_LOGIN_INFO ?></p>

<?php
echo form_tag(
    'cfg',
    url_for('configuration/save', 'redirect_to=configuration/guest_login'),
    ['class' => 'form-horizontal']
) ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label"><?php
            echo TEXT_ENABLE_GUEST_LOGIN ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[ENABLE_GUEST_LOGIN]',
                $default_selector,
                CFG_ENABLE_GUEST_LOGIN,
                ['class' => 'form-control input-small']
            ); ?>
        </div>
    </div>

    <?php
    $choices = [];
    $choices[0] = TEXT_NONE;
    $users_query = db_query(
        "select u.*,a.name as group_name from app_entity_1 u left join app_access_groups a on a.id=u.field_6 where field_6>0 order by u.field_8, u.field_7"
    );
    while ($users = db_fetch_array($users_query)) {
        $choices[$users['group_name']][$users['id']] = $users['field_8'] . ' ' . $users['field_7'];
    }

    ?>

    <div class="form-group">
        <label class="col-md-3 control-label"><?php
            echo TEXT_USER ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[GUEST_LOGIN_USER]',
                $choices,
                CFG_GUEST_LOGIN_USER,
                ['class' => 'form-control input-xlarge chosen-select required']
            ); ?>
            <?php
            echo tooltip_text(TEXT_GUEST_LOGIN_USER_INFO) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"><?php
            echo TEXT_BUTTON_TITLE ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag(
                'CFG[GUEST_LOGIN_BUTTON_TITLE]',
                CFG_GUEST_LOGIN_BUTTON_TITLE,
                ['class' => 'form-control input-medium']
            ); ?>
            <?php
            echo tooltip_text(TEXT_DEFAULT . ': ' . TEXT_LOGIN_AS_GUEST) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label"><?php
            echo TEXT_URL ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag(
                'url',
                url_for('users/guest_login'),
                ['class' => 'form-control input-xlarge select-all', 'readonly' => 'readonly']
            ); ?>
        </div>
    </div>


    <?php
    echo submit_tag(TEXT_BUTTON_SAVE) ?>

</div>
</form>

<script>
    $(function () {
        $('#cfg').validate({ignore: ''});
    });
</script> 