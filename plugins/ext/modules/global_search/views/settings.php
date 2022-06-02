<h3 class="page-title"><?php
    echo TEXT_EXT_GLOBAL_SEARCH ?></h3>

<p><?php
    echo TEXT_EXT_GLOBAL_SEARCH_INFO ?></p>

<?php
echo form_tag('configuration_form', url_for('ext/global_search/settings', 'action=save'), ['class' => 'form-horizontal']
) ?>
<div class="form-body">

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_USE_GLOBAL_SEARCH"><?php
            echo TEXT_EXT_USE_GLOBAL_SEARCH ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[USE_GLOBAL_SEARCH]',
                app_get_boolean_choices(),
                CFG_USE_GLOBAL_SEARCH,
                ['class' => 'form-control input-small']
            ) ?>
        </div>
    </div>


    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_GLOBAL_SEARCH_DISPLAY_IN_HEADER"><?php
            echo TEXT_DISPLAY_IN_HEADER ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[GLOBAL_SEARCH_DISPLAY_IN_HEADER]',
                app_get_boolean_choices(),
                CFG_GLOBAL_SEARCH_DISPLAY_IN_HEADER,
                ['class' => 'form-control input-small']
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_GLOBAL_SEARCH_DISPLAY_IN_MENU"><?php
            echo TEXT_DISPLAY_IN_MENU ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[GLOBAL_SEARCH_DISPLAY_IN_MENU]',
                app_get_boolean_choices(),
                CFG_GLOBAL_SEARCH_DISPLAY_IN_MENU,
                ['class' => 'form-control input-small']
            ) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="allowed_groups"><?php
            echo TEXT_EXT_USERS_GROUPS ?></label>
        <div class="col-md-9">
            <?php
            echo select_tag(
                'CFG[GLOBAL_SEARCH_ALLOWED_GROUPS][]',
                access_groups::get_choices(),
                CFG_GLOBAL_SEARCH_ALLOWED_GROUPS,
                ['class' => 'form-control input-xlarge chosen-select', 'multiple' => 'multiple']
            ) ?>
            <?php
            echo tooltip_text(TEXT_EXT_GLOBAL_SEARCH_ACCESS_TIP) ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_GLOBAL_SEARCH_ROWS_PER_PAGE"><?php
            echo TEXT_ROWS_PER_PAGE ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag(
                'CFG[GLOBAL_SEARCH_ROWS_PER_PAGE]',
                CFG_GLOBAL_SEARCH_ROWS_PER_PAGE,
                ['class' => 'form-control input-small number']
            ); ?>
        </div>
    </div>

    <h3 class="form-section"><?php
        echo TEXT_FIELDTYPE_INPUT_TITLE ?></h3>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_GLOBAL_SEARCH_INPUT_TOOLTIP"><?php
            echo TEXT_TOOLTIP ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag(
                'CFG[GLOBAL_SEARCH_INPUT_TOOLTIP]',
                (defined('CFG_GLOBAL_SEARCH_INPUT_TOOLTIP') ? CFG_GLOBAL_SEARCH_INPUT_TOOLTIP : TEXT_SEARCH),
                ['class' => 'form-control input-medium']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_GLOBAL_SEARCH_INPUT_MIN"><?php
            echo TEXT_MIN_VALUE ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag(
                'CFG[GLOBAL_SEARCH_INPUT_MIN]',
                CFG_GLOBAL_SEARCH_INPUT_MIN,
                ['class' => 'form-control input-small number']
            ); ?>
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-3 control-label" for="CFG_GLOBAL_SEARCH_INPUT_MAX"><?php
            echo TEXT_MAX_VALUE ?></label>
        <div class="col-md-9">
            <?php
            echo input_tag(
                'CFG[GLOBAL_SEARCH_INPUT_MAX]',
                CFG_GLOBAL_SEARCH_INPUT_MAX,
                ['class' => 'form-control input-small number']
            ); ?>
        </div>
    </div>

</div>

<?php
echo submit_tag(TEXT_BUTTON_SAVE) ?>
</form>     