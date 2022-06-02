<h3 class="page-title">Мой сертификат электронной подписи</h3>

<?php

$cryptopro_certificates_query = db_query(
    "select certbase64 from app_ext_cryptopro_certificates where users_id='" . $app_user['id'] . "'"
);
if ($cryptopro_certificates = db_fetch_array($cryptopro_certificates_query)) {
    $CertBase64 = $cryptopro_certificates['certbase64'];

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

            $html = '
                <table>
                    <tr>
                        <td>Владелец сертификата:</td>
                        <td><b>' . $CertData['subject']['CN'] . '</b></td>
                    </tr>
                    <tr>
                        <td>ФИО:</td>
                        <td>' . $CertData['subject']['SN'] . " " . $CertData['subject']['GN'] . '</td>
                    </tr>
                    <tr>
                        <td>Должность:</td>
                        <td>' . $CertData['subject']['title'] . '</td>
                    </tr>    
                
                ';

            if (isset($CertData['subject']['emailAddress'])) {
                $html .= '
                    <tr>
                        <td>EMail:</td>
                        <td>' . $CertData['subject']['emailAddress'] . '</td>
                    </tr>   
                ';
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


            $html .= '
                    <tr>
                        <td>Организация</td>
                        <td>' . $CertData['subject']['O'] . '</td>
                    </tr>
                    <tr>
                        <td>СНИЛС:</td>
                        <td>' . $SNILS . '</td>
                    </tr>
                    <tr>
                        <td>ИНН:</td>
                        <td>' . $INN . '</td>
                    </tr>
                    <tr>
                        <td>ОГРН/ОГРНИП:</td>
                        <td>' . $OGRN . $OGRNIP . '</td>
                    </tr>
                    <tr>
                        <td>Область</td>
                        <td>' . $CertData['subject']['ST'] . '</td>
                    </tr>
                    <tr>
                        <td>Город:</td>
                        <td>' . $CertData['subject']['L'] . '</td>
                    </tr>
                    <tr>
                        <td>Улица:</td>
                        <td>' . $CertData['subject']['street'] . '</td>
                    </tr>
                    <tr>
                        <td>Серийный номер:</td>
                        <td>' . $CertData['serialNumber'] . '</td>
                    </tr>
                    <tr>
                        <td>Период действия:</td>
                        <td>' . format_date(
                    strtotime((strlen($CertData['validFrom']) == 13 ? '20' : '') . $CertData['validFrom'])
                ) . " до " . format_date(
                    strtotime((strlen($CertData['validFrom']) == 13 ? '20' : '') . $CertData['validTo'])
                ) . '</td>
                    </tr>
                    <tr>
                        <td><hr></td>
                        <td><hr></td>
                    </tr>
                    <tr>
                        <td>УЦ:</td>
                        <td><b>' . $CertData['issuer']['CN'] . '</b></td>
                    </tr>
                    <tr>
                        <td>Организация:</td>
                        <td>' . $CertData['issuer']['O'] . '</td>
                    </tr>
                    <tr>
                        <td>УЦ.Область:</td>
                        <td>' . $CertData['issuer']['ST'] . '</td>
                    </tr>
                    <tr>
                        <td>УЦ.Город:</td>
                        <td>' . $CertData['issuer']['L'] . '</td>
                    </tr>
                    <tr>
                        <td>УЦ.Улица:</td>
                        <td>' . $CertData['issuer']['street'] . '</td>
                    </tr>
                    <tr>
                        <td><hr></td>
                        <td><hr></td>
                    </tr>
                    <tr>
                        <td>Использование ключа:</td>
                        <td>' . $CertData['extensions']['keyUsage'] . '</td>
                    </tr>
                    <tr>
                        <td>Идентификатор ключа субъекта:</td>
                        <td>' . str_replace(
                    ":",
                    "",
                    str_replace("keyid:", "", $CertData['extensions']['subjectKeyIdentifier'])
                ) . '</td>
                    </tr>
                    <tr>
                        <td>Идентификатор ключа УЦ:</td>
                        <td>' . substr(
                    str_replace(":", "", str_replace("keyid:", "", $CertData['extensions']['authorityKeyIdentifier'])),
                    0,
                    40
                ) . '</td>
                    </tr>
                    <tr>
                        <td><hr></td>
                        <td><hr></td>
                    </tr>                   
                ';


            $html .= '</table>';

            echo $html;
        }
    } catch (Exception $e) {
        echo alert_error($e->getMessage());
    }

    echo '<a href="' . url_for(
            'users/signature_update'
        ) . '" class="btn btn-primary">Обновить сертификат</a> <a href="' . url_for(
            'users/signature_account',
            'action=delete'
        ) . '" onclick="return confirm(\'' . TEXT_ARE_YOU_SURE . '\')" class="btn btn-default">Удалить</a>';
} else {
    echo app_alert_warning('Нет связанных сертификатов с вашей учетной записью.');
    echo '<a href="' . url_for('users/signature_update') . '" class="btn btn-primary">Добавить сертификат</a>';
}


