<?php

$field_query = db_query("select * from app_fields where id='" . _GET('fields_id') . "'");
if (!$field = db_fetch_array($field_query)) {
    redirect_to('dashboard/page_not_found');
}

$cfg = new fields_types_cfg($field['configuration']);

if (!fieldtype_digital_signature::has_sign_access($current_entity_id, $current_item_id, _GET('fields_id'))) {
    redirect_to('dashboard/access_forbidden');
}


$module_info_query = db_query(
    "select * from app_ext_modules where id='" . $cfg->get(
        'module_id'
    ) . "' and type='digital_signature' and is_active=1"
);
if ($module_info = db_fetch_array($module_info_query)) {
    modules::include_module($module_info, 'digital_signature');

    $module = new $module_info['module'];
} else {
    redirect_to('dashboard/page_not_found');
}

switch ($app_module_action) {
    case 'sign':

        $sign_source_signature = (isset($_POST['sign_source_signature']) ? $_POST['sign_source_signature'] : false);
        $sign_file_signature = (isset($_POST['sign_file_signature']) ? $_POST['sign_file_signature'] : false);

        //check if there are fields to sign
        if (!$sign_source_signature and !$sign_file_signature) {
            redirect_to('items/info', 'path=' . $app_path);
        }

        //check if sign exist
        $check_query = db_query(
            "select id from  app_ext_signed_items where fields_id='" . _GET(
                'fields_id'
            ) . "' and entities_id='" . $current_entity_id . "' and items_id='" . $current_item_id . "' and users_id='" . $app_user['id'] . "'"
        );
        if ($check = db_fetch_array($check_query)) {
            redirect_to('items/info', 'path=' . $app_path);
        }

        //get user cert
        $cryptopro_certificates_query = db_query(
            "select certbase64,thumbprint from app_ext_cryptopro_certificates where users_id='" . $app_user['id'] . "'"
        );
        if (!$cryptopro_certificates = db_fetch_array($cryptopro_certificates_query)) {
            redirect_to('users/signature_account');
        }

        $CertBase64 = $cryptopro_certificates['certbase64'];

        //check if data in account is changed
        if (!$module->check_before_update($module_info['id'], $CertBase64)) {
            redirect_to('items/info', 'path=' . $app_path);
        }

        //check all signatures
        $SignatureCheckResult = '';
        $cert = false;

        if ($sign_source_signature) {
            if (!cryptopro::SignatureCheck(base64_decode($_POST['sign_source_base64']), $sign_source_signature, 1, 0)) {
                $alerts->add(TEXT_ERROR . ' ' . $SignatureCheckResult, 'error');
                redirect_to('items/info', 'path=' . $app_path);
            }

            if ($cryptopro_certificates['thumbprint'] != $cert->get_Thumbprint()) {
                $alerts->add(TEXT_ERROR . ' сертификат подписи не совпадает с сертификатом в личном кабинете.');
                redirect_to('items/info', 'path=' . $app_path);
            }
        }

        if ($sign_file_signature) {
            foreach ($_POST['sign_file_name'] as $key => $filename) {
                $file = attachments::parse_filename($filename);

                if (!cryptopro::SignatureCheck(
                    file_get_contents($file['file_path']),
                    $_POST['sign_file_signature'][$key],
                    1,
                    0
                )) {
                    $alerts->add(TEXT_ERROR . ' ' . $SignatureCheckResult, 'error');
                    redirect_to('items/info', 'path=' . $app_path);
                }

                if ($cryptopro_certificates['thumbprint'] != $cert->get_Thumbprint()) {
                    $alerts->add(TEXT_ERROR . ' сертификат подписи не совпадает с сертификатом в личном кабинете.');
                    redirect_to('items/info', 'path=' . $app_path);
                }
            }
        }

        //end


        try {
            $certPEM = "-----BEGIN CERTIFICATE-----\n" . $CertBase64 . "\n" . "-----END CERTIFICATE-----\n";
            $res = openssl_x509_read($certPEM);

            if ($res) {
                $CertData = openssl_x509_parse($res);

                //print_rr($CertData);

                if (!isset($CertData['subject']['SN'])) {
                    $CertData['subject']['SN'] = '';
                }
                if (!isset($CertData['subject']['GN'])) {
                    $CertData['subject']['GN'] = '';
                }
                if (!isset($CertData['subject']['title'])) {
                    $CertData['subject']['title'] = '';
                }
                if (!isset($CertData['subject']['SNILS'])) {
                    $CertData['subject']['SNILS'] = '';
                }
                if (!isset($CertData['subject']['INN'])) {
                    $CertData['subject']['INN'] = '';
                }
                if (!isset($CertData['subject']['OGRN'])) {
                    $CertData['subject']['OGRN'] = '';
                }
                if (!isset($CertData['subject']['ST'])) {
                    $CertData['subject']['ST'] = '';
                }
                if (!isset($CertData['subject']['L'])) {
                    $CertData['subject']['L'] = '';
                }
                if (!isset($CertData['subject']['O'])) {
                    $CertData['subject']['O'] = '';
                }
                if (!isset($CertData['subject']['street'])) {
                    $CertData['subject']['street'] = '';
                }

                if (!isset($CertData['issuer']['ST'])) {
                    $CertData['issuer']['ST'] = '';
                }
                if (!isset($CertData['issuer']['street'])) {
                    $CertData['issuer']['street'] = '';
                }

                $SNILS = cryptopro::getOID('1.2.643.100.3', $CertData['name']);
                $INN = cryptopro::getOID('1.2.643.3.131.1.1', $CertData['name']);
                $OGRN = cryptopro::getOID('1.2.643.100.1', $CertData['name']);
                $OGRNIP = cryptopro::getOID('1.2.643.100.5', $CertData['name']);

                if (strlen($SNILS) == 0) {
                    $SNILS = $CertData['subject']['SNILS'];
                    $INN = $CertData['subject']['INN'];
                    $OGRN = $CertData['subject']['OGRN'];
                    $OGRNIP = $CertData['subject']['UNDEF'];
                }
                //print_rr($CertData);

            }
        } catch (Exception $e) {
            $alerts->add($e->getMessage(), 'error');
            redirect_to('items/info', 'path=' . $app_path);
        }

        //insert sign to item

        $name = (strlen(
            $CertData['subject']['SN']
        ) ? $CertData['subject']['SN'] . " " . $CertData['subject']['GN'] : $CertData['subject']['CN']);

        $sql_data = [
            'entities_id' => $current_entity_id,
            'items_id' => $current_item_id,
            'fields_id' => _GET('fields_id'),
            'users_id' => $app_user['id'],
            'date_added' => time(),
            'name' => $name,
            'company' => $CertData['subject']['O'],
            'position' => $CertData['subject']['title'],
            'inn' => $INN,
            'ogrn' => $OGRN . $OGRNIP,
        ];

        db_perform('app_ext_signed_items', $sql_data);
        $signed_items_id = db_insert_id();

        //sign text
        if ($sign_source_signature) {
            $sql_data = [
                'signed_items_id' => $signed_items_id,
                'signed_text' => base64_decode($_POST['sign_source_base64']),
                'signature' => db_prepare_input($sign_source_signature),
            ];

            db_perform('app_ext_signed_items_signatures', $sql_data);
        }

        //sign files
        if ($sign_file_signature) {
            foreach ($_POST['sign_file_name'] as $key => $filename) {
                $sql_data = [
                    'signed_items_id' => $signed_items_id,
                    'signed_text' => '',
                    'singed_filename' => db_prepare_input($filename),
                    'signature' => db_prepare_input($_POST['sign_file_signature'][$key]),
                ];

                db_perform('app_ext_signed_items_signatures', $sql_data);
            }
        }


        //run process
        if ($cfg->get('run_process') > 0) {
            $all_users_singed = true;

            if ($cfg->get('assigned_to') > 0 and isset(
                    $app_fields_cache[$current_entity_id][$cfg->get(
                        'assigned_to'
                    )]
                )) {
                $item_query = db_query(
                    "select  e.field_" . $cfg->get(
                        'assigned_to'
                    ) . " as assigned_users from app_entity_" . $current_entity_id . " e where id='" . $current_item_id . "'"
                );
                if ($item = db_fetch_array($item_query)) {
                    foreach (explode(',', $item['assigned_users']) as $user_id) {
                        $check_query = db_query(
                            "select id from app_ext_signed_items where fields_id='" . _GET(
                                'fields_id'
                            ) . "' and entities_id='" . $current_entity_id . "' and items_id='" . $current_item_id . "' and users_id='" . $user_id . "'"
                        );
                        if (!$check = db_fetch_array($check_query)) {
                            $all_users_singed = false;
                        }
                    }
                }
            }

            if ($all_users_singed) {
                redirect_to(
                    'items/processes',
                    'action=run&id=' . $cfg->get('run_process') . '&path=' . $app_path . '&redirect_to=items_info'
                );
            }
        } else {
            $alerts->add(TEXT_EXT_SUCCESSFULLY_SIGNED, 'success');
        }

        redirect_to('items/info', 'path=' . $app_path);

        break;
}