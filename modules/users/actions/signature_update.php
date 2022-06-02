<?php

if (!strlen(CFG_LOGIN_DIGITAL_SIGNATURE_MODULE)) {
    redirect_to('users/account');
}

$module_info_query = db_query(
    "select * from app_ext_modules where id='" . (int)CFG_LOGIN_DIGITAL_SIGNATURE_MODULE . "' and type='digital_signature' and is_active=1"
);
if ($module_info = db_fetch_array($module_info_query)) {
    modules::include_module($module_info, 'digital_signature');

    $module = new $module_info['module'];
} else {
    redirect_to('users/account');
}