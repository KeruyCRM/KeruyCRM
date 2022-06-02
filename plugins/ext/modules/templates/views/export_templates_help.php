<?php
echo ajax_modal_template_header(TEXT_HELP) ?>

    <div class="modal-body ajax-modal-width-790">
        <?php
        echo '	
	<b>' . TEXT_ENTITY . '</b>	
	<p>' . TEXT_EXT_TEMPLATES_FIELDS_NOTES . '<br>' . TEXT_EXT_TEMPLATES_FIELDS_NOTES2 . '<br>' . TEXT_EXT_TEMPLATES_FIELDS_NOTES3 . '</p>
			
	<b>' . TEXT_FIELDTYPE_RELATED_RECORDS_TITLE . '</b>
	<p>' . TEXT_EXT_TEMPLATES_EXPORT_ENTITY_NOTES . '</p>
        <p>' . TEXT_EXT_TEMPLATES_EXPORT_ENTITY_TREE_NOTES . '</p>   
					
	<b>' . TEXT_ATTACHMENTS . '</b>
	<p>' . TEXT_EXT_TEMPLATES_EXPORT_ATTACHMENTS . '</p>		
			
	<b>' . TEXT_COMMENTS . '</b>
	<p>' . TEXT_EXT_TEMPLATES_EXPORT_COMMENTS . '</p>		
			
	<b>' . TEXT_EXT_CURRENCIES . '</b>
	<p>' . TEXT_EXT_TEMPLATES_EXPORT_NUM2STR_NOTES . '</p>		
			
';


        ?>
    </div>
<?php
echo ajax_modal_template_footer('hide-save-button') ?>