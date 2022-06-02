<?php

switch ($app_module_action) {
    case 'save':
        $sql_data = [
            'is_default' => (isset($_POST['is_default']) ? $_POST['is_default'] : 0),
            'title' => $_POST['title'],
            'code' => strtoupper($_POST['code']),
            'symbol' => $_POST['symbol'],
            'value' => $_POST['value'],
            'sort_order' => $_POST['sort_order'],
        ];

        //reset default
        if (isset($_POST['is_default'])) {
            db_query("update app_ext_currencies set is_default = 0");
            $sql_data['value'] = 1;
        }

        if (isset($_GET['id'])) {
            db_perform('app_ext_currencies', $sql_data, 'update', "id='" . db_input(_get::int('id')) . "'");
        } else {
            db_perform('app_ext_currencies', $sql_data);
        }

        redirect_to('ext/currencies/currencies');
        break;

    case 'delete':
        $obj = db_find('app_ext_currencies', $_GET['id']);

        db_delete_row('app_ext_currencies', $_GET['id']);

        $alerts->add(sprintf(TEXT_WARN_DELETE_SUCCESS, $obj['title']), 'success');

        redirect_to('ext/currencies/currencies');
        break;
    case 'update':

        if (isset($_POST['module'])) {
            $module = $_POST['module'];

            $path = DIR_FS_CATALOG . 'plugins/ext/currencies_modules/';

            if (is_file($path . $module . '.php')) {
                $currencies = new currencies;
                $currencies->update($module);

                configuration::set('CFG_CURRENCIES_UPDATE_MODULE', $module);
            }
        }
        redirect_to('ext/currencies/currencies');
        break;
    case 'save_widget':

        configuration::set(
            'CFG_CURRENCIES_WIDGET_USERS_GROUPS',
            (isset($_POST['users_groups']) ? implode(',', $_POST['users_groups']) : '')
        );

        redirect_to('ext/currencies/currencies');
        break;
}