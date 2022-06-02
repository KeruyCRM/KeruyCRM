<?php
echo ajax_modal_template_header(TEXT_EXT_AVAILABLE_MODULES) ?>

<?php
echo form_tag(
    'install_form',
    url_for('ext/modules/modules', 'action=install&type=' . $_GET['type']),
    ['class' => 'form-horizontal']
) ?>
<div class="modal-body">
    <div class="form-body">

        <?php
        $modules = new modules($_GET['type']);
        $available_modules = $modules->get_available_modules();

        if (count($available_modules)) {
            $html = '
                <table class="table table-hover">
                <thead>
                    <tr>
                        <th>' . TEXT_COUNTRY . '</th>
                        <th>' . TEXT_NAME . '</th> 
                        <th>' . TEXT_URL . '</th>
                    </tr>
                </thead>';
            $count = 0;
            foreach ($available_modules as $country => $modules_list) {
                sort($modules_list);

                foreach ($modules_list as $modules) {
                    $params = ($count == 0 ? ['checked' => 'checked'] : []);

                    $params['id'] = 'module_' . $modules;

                    $module = new $modules;

                    $html .= '
                            <tr>
                                <td>' . (strlen($country) ? $country : TEXT_INTERNATIONAL) . '</td>
                                <td><label>' . input_radiobox_tag('module', $modules, $params) . ' ' . $module->title . '</label></td>
                                <td><a href="' . $module->site . '" target="_blank">' . str_replace(
                            ['http://', 'https://', 'www.'],
                            '',
                            $module->site
                        ) . '</a></td>
                            </tr>	
                                    ';


                    $count++;
                }
            }

            $html .= '</table>';
        } else {
            $html = TEXT_NO_RECORDS_FOUND;
        }

        echo $html;
        ?>

    </div>
</div>

<?php
echo ajax_modal_template_footer((count($available_modules) ? TEXT_EXT_BUTTON_INSTALL_MODULE : 'hide-save-button')) ?>

</form>

<script>
    $(function () {
        $('#install_form').validate();
    });
</script>   