<?php

class cryptopro
{
    public $title;

    public $site;

    function __construct()
    {
        $this->title = TEXT_MODULE_CRYPTOPRO_TITLE;
        $this->site = 'https://www.cryptopro.ru';
        $this->api = 'https://www.cryptopro.ru/support/docs';
        $this->version = '1.0';
        $this->country = 'RU';
    }

    public function configuration()
    {
        $cfg = [];

        if (!class_exists('CPSignedData')) {
            $cfg[] = [
                'key' => 'check',
                'type' => 'text',
                'title' => '',
                'default' => alert_error(
                    '<b>Ошибка:</b> для корректной работы модуля на сервере должно быть установлено программное обеспечение КриптоПро (Расширение для PHP).<br><a href="https://keruy.com.ua/index.php?p=102" target="_balnk">Инструкция по установке</a>.'
                ),
            ];
        }

        $cfg[] = [
            'key' => 'check',
            'type' => 'text',
            'title' => 'Проверка данных пользователя при добавлении сертификата:',
            'default' => 'Укажите поля, данные из которых будут сверяться c сертификатом при его добавлении.',
        ];

        $default_choices = ['0' => TEXT_NO, '1' => TEXT_YES];

        $choices = [];
        $choices[0] = TEXT_NO;
        $fields_query = db_query(
            "select id, name from app_fields where entities_id = 1 and type in ('fieldtype_input_protected','fieldtype_input','fieldtype_textarea')"
        );
        while ($fields = db_fetch_array($fields_query)) {
            $choices[$fields['id']] = $fields['name'];
        }


        $cfg[] = [
            'key' => 'check_firstname',
            'type' => 'dorpdown',
            'choices' => $default_choices,
            'title' => 'Имя',
            'default' => 0,
            'params' => ['class' => 'form-control input-small'],
        ];

        $cfg[] = [
            'key' => 'check_lastname',
            'type' => 'dorpdown',
            'choices' => $default_choices,
            'title' => 'Фамилия',
            'default' => 0,
            'params' => ['class' => 'form-control input-small'],
        ];

        $cfg[] = [
            'key' => 'check_middlename',
            'type' => 'dorpdown',
            'choices' => $choices,
            'title' => 'Отчество',
            'description' => 'Выберите поле в котором хранится отчество пользователя',
            'default' => 0,
            'params' => ['class' => 'form-control input-large'],
        ];

        $cfg[] = [
            'key' => 'check_email',
            'type' => 'dorpdown',
            'choices' => $default_choices,
            'title' => 'E-mail',
            'default' => 0,
            'params' => ['class' => 'form-control input-small'],
        ];


        $cfg[] = [
            'key' => 'check_inn',
            'type' => 'dorpdown',
            'choices' => $choices,
            'title' => 'ИНН',
            'default' => 0,
            'params' => ['class' => 'form-control input-large'],
        ];

        $cfg[] = [
            'key' => 'check_company',
            'type' => 'dorpdown',
            'choices' => $choices,
            'title' => 'Организация',
            'default' => 0,
            'params' => ['class' => 'form-control input-large'],
        ];


        return $cfg;
    }

    function select_certificate($form_action_url = 'login')
    {
        switch ($form_action_url) {
            case 'login':
                $form_action_url = url_for('users/signature_login', 'action=login');
                break;
            case 'account':
                echo '<h3 class="page-title">Мой сертификат электронной подписи</h3>';
                $form_action_url = url_for('users/signature_account', 'action=update');
                break;
        }
        require('plugins/ext/digital_signature_modules/cryptopro/components/select_certificate.php');
    }

    function account_certificate($module_id)
    {
        global $app_fields_cache, $app_user;

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        require('plugins/ext/digital_signature_modules/cryptopro/components/account_certificate.php');
    }

    static function getOID($OID, $certificate)
    {
        if (preg_match('/\/' . $OID . '=([^\/]+)/', $certificate, $matches)) {
            return $matches[1];
        } else {
            return false;
        }
    }

    function delete()
    {
        global $app_user;

        db_query("delete from app_ext_cryptopro_certificates where users_id='" . $app_user['id'] . "'");

        redirect_to('users/signature_account');
    }

    function update($module_id)
    {
        global $app_user, $app_fields_cache, $alerts;

        //Обращение к криптографическим функциям
        $cert = $this->check('users/signature_update');

        $thumbprint = $cert->get_Thumbprint();
        $certbase64 = $cert->Export(0);

        //check fields
        if (!$this->check_before_update($module_id, $certbase64)) {
            redirect_to('users/signature_account');
        }

        //check if thumbprint already exist
        $cryptopro_certificates_query = db_query(
            "select id from app_ext_cryptopro_certificates where users_id!='" . $app_user['id'] . "' and thumbprint='" . $thumbprint . "'"
        );
        if ($cryptopro_certificates = db_fetch_array($cryptopro_certificates_query)) {
            $alerts->add('Сертификат уже зарегистрирован в системе.', 'error');
            redirect_to('users/signature_account');
        }

        $cryptopro_certificates_query = db_query(
            "select id from app_ext_cryptopro_certificates where users_id='" . $app_user['id'] . "'"
        );
        if ($cryptopro_certificates = db_fetch_array($cryptopro_certificates_query)) {
            $sql_data = [
                'thumbprint' => $thumbprint,
                'certbase64' => $certbase64,
            ];

            db_perform('app_ext_cryptopro_certificates', $sql_data, 'update', "users_id='" . $app_user['id'] . "'");
        } else {
            $sql_data = [
                'users_id' => $app_user['id'],
                'thumbprint' => $thumbprint,
                'certbase64' => $certbase64,
            ];

            db_perform('app_ext_cryptopro_certificates', $sql_data);
        }

        redirect_to('users/signature_account');
    }

    function check_before_update($module_id, $certbase64)
    {
        global $alerts, $app_user;

        try {
            $certPEM = "-----BEGIN CERTIFICATE-----\n" . $certbase64 . "\n" . "-----END CERTIFICATE-----\n";
            $res = openssl_x509_read($certPEM);

            if ($res) {
                $CertData = openssl_x509_parse($res);
            }
        } catch (Exception $e) {
            exit($e->getMessage());
        }

        $INN = cryptopro::getOID('1.2.643.3.131.1.1', $CertData['name']);

        if (strlen($INN) == 0) {
            $INN = (isset($CertData['subject']['INN']) ? $CertData['subject']['INN'] : '');
        }

        $cfg = modules::get_configuration($this->configuration(), $module_id);

        $error = false;

        $cert_firstname = '';
        $cert_middlename = '';

        if (isset($CertData['subject']['GN'])) {
            $subject_gn = explode(' ', $CertData['subject']['GN']);

            $cert_firstname = $subject_gn[0];

            if (isset($subject_gn[1])) {
                $cert_middlename = $subject_gn[1];
            }
        }

        //check email
        if ($cfg['check_email'] == 1 and isset($CertData['subject']['emailAddress'])) {
            if (trim($app_user['email']) != trim($CertData['subject']['emailAddress'])) {
                $error = true;
                $alerts->add('Ошибка: E-mail адрес в личном кабинете не совпадает с данными в сертификате.', 'error');
            }
        }

        //Имя
        if ($cfg['check_firstname'] == 1 and strlen($cert_firstname)) {
            if (trim($app_user['fields']['field_7']) != trim($cert_firstname)) {
                $error = true;
                $alerts->add('Ошибка: Имя в личном кабинете не совпадает с данными в сертификате.', 'error');
            }
        }

        //фамилия
        if ($cfg['check_lastname'] == 1 and isset($CertData['subject']['SN'])) {
            if (trim($app_user['fields']['field_8']) != trim($CertData['subject']['SN'])) {
                $error = true;
                $alerts->add('Ошибка: Фамилия в личном кабинете не совпадает с данными в сертификате.', 'error');
            }
        }


        //отчество
        if ($cfg['check_middlename'] > 0 and strlen($cert_middlename)) {
            if (strlen($app_user['fields']['field_' . $cfg['check_middlename']])) {
                if (trim($app_user['fields']['field_' . $cfg['check_middlename']]) != trim($cert_middlename)) {
                    $error = true;
                    $alerts->add('Ошибка: Отчество в личном кабинете не совпадает с данными в сертификате.', 'error');
                }
            }
        }

        //
        if ($cfg['check_inn'] > 0 and strlen($INN)) {
            if (strlen($app_user['fields']['field_' . $cfg['check_inn']])) {
                if (trim($app_user['fields']['field_' . $cfg['check_inn']]) != trim($INN)) {
                    $error = true;
                    $alerts->add('Ошибка: ИНН в личном кабинете не совпадает с данными в сертификате.', 'error');
                }
            }
        }

        //
        if ($cfg['check_company'] > 0 and isset($CertData['subject']['O'])) {
            if (strlen($app_user['fields']['field_' . $cfg['check_company']])) {
                if (trim($app_user['fields']['field_' . $cfg['check_company']]) != trim($CertData['subject']['O'])) {
                    $error = true;
                    $alerts->add(
                        'Ошибка: Организация в личном кабинете не совпадает с данными в сертификате.',
                        'error'
                    );
                }
            }
        }

        /*
        echo 'User INN = '  . $app_user['fields']['field_' . $cfg['check_inn']] . '<br>';
        echo 'Cert INN=' . $INN . '<br>';
        print_rr($CertData);
        exit();
        */


        return ($error ? false : true);
    }

    function login($module_id)
    {
        global $alerts, $app_fields_cache;


        //Обращение к криптографическим функциям
        $cert = $this->check('users/signature_login');

        $Thumbprint = $cert->get_Thumbprint();

        //print_rr($_POST);

        $cert_username = iconv(
            'WINDOWS-1251',
            'UTF-8',
            $cert->GetInfo('0')
        );// Владелец сертификата из сертификата, вместо данных из запроса: db_prepare_input($_POST['cert_username']);

        // найти запись, где совпадают отпечатки сертификата из присланной подписи и пользователя в системе

        $cryptopro_certificates_query = db_query(
            "select users_id from app_ext_cryptopro_certificates where thumbprint='" . $Thumbprint . "'"
        );
        if ($cryptopro_certificates = db_fetch_array($cryptopro_certificates_query)) {
            app_session_register('app_logged_users_id', $cryptopro_certificates['users_id']);
            two_step_verification::approve();
        }

        users_login_log::fail($cert_username);
        $alerts->add('Сертификат: ' . $cert_username . ' (' . $Thumbprint . ') не зарегистрирован в системе.', 'error');
        redirect_to('users/signature_login');
    }

    function check($redirect_to)
    {
        global $alerts;

        $SignatureCheckResult = false;

        $data = ""; // т.к. подпись присоединенная - данные есть в sgn
        $sgn = $_POST['signed']; // подпись и данные, в base64
        $detached = 0; // присоединенная подпись
        $IgnoreUntrustCA = 1;// для тестовых

        if (strlen($sgn) < 1000) {
            $alerts->add('Ошибка - нет подписанных данных в запросе', 'error');
            redirect_to($redirect_to);
        }

        try {
            $SignedData = new \CPSignedData();
            $SignedData->set_ContentEncoding(BASE64_TO_BINARY);
        } catch (Exception $e) {
            $SignatureCheckResultStr = "Ошибка при обращении к криптографическим функциям. " . $e->getMessage();
            $alerts->add($SignatureCheckResultStr, 'error');
            redirect_to($redirect_to);
            //return;//
        }


        /*
         if ($data) { // пример, если будет отсоединенная подпись //
         $SignedData->set_Content(base64_encode($data));
         }
         */


        try {
            $check = $SignedData->VerifyCades($sgn, 0x01, $detached);
            $SignatureCheckResult = true;
            $SignatureCheckResultStr = "Успешно проверена подпись и цепочка сертификатов";
        } catch (Exception $e) {
            /*
             0x800B010A: Не удается построить цепочку сертификатов для доверенного корневого центра => установить через certmgr сертификат УЦ
             */

            $SignatureCheckResultStr = $e->getMessage();

            if (strpos($e->getMessage(), "0x800B010A")) {
                $signObject = $SignedData->get_Signers();
                $sObj = $signObject->get_Item(1);
                $cert = $sObj->get_Certificate();

                $SignatureCheckResultStr = "Подпись корректна, но нет доверия к корневому сертификату УЦ";
                if ($cert) {
                    $SignatureCheckResultStr = $SignatureCheckResultStr . ": " . iconv(
                            'WINDOWS-1251',
                            'UTF-8',
                            $cert->GetInfo('1')
                        );
                }

                if ($IgnoreUntrustCA) {
                    $SignatureCheckResult = true;
                }
            }
            if (strpos($e->getMessage(), "0x80091004")) {
                $SignatureCheckResultStr = "Подпись повреждена (Invalid cryptographic message), код ошибки: 0x80091004";
            }
        }


        if (!$SignatureCheckResult) {
            $alerts->add($SignatureCheckResultStr, 'error');
            redirect_to($redirect_to);
            //return;//
        }


        $SignedContentDecoded = "";
        // если подпись проверена - получение информации о сертификате подписи
        try {
            $signObject = $SignedData->get_Signers();
            $sObj = $signObject->get_Item(1);
            $cert = $sObj->get_Certificate();

            $Thumbprint = $cert->get_Thumbprint();


            if ($detached == 0) {
                $SignedContent = $SignedData->get_Content(); // данные возвращаются в base64
                $SignedContentDecoded = base64_decode($SignedContent);
                //перекодировка, если нужно... $text = iconv('WINDOWS-1251', 'UTF-8', $SignedContentDecoded);
            }
        } catch (Exception $e) {
            $alerts->add('Ошибка при получении данных из сертификата. ' . $e->getMessage(), 'error');
            redirect_to($redirect_to);
            //return;//
        }


        if ($SignedContentDecoded <> $_POST['form_session_token']) {
            // if ($SignedContentDecoded <> $Thumbprint) // для тестов - токен=отпечатку сертификата
            {
                $alerts->add(
                    'Ошибка при проверке токена, некорректные данные: [' . $SignedContentDecoded . ']',
                    'error'
                );
                redirect_to($redirect_to);
            }
        }

        return $cert;
    }

    static function is_base64_data($s)
    {
        return (bool)preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $s);
    }

    static function GetSignerCert($SignedData)
    {
        try {
            $signObject = $SignedData->get_Signers();
            $sObj = $signObject->get_Item(1);
            return $sObj->get_Certificate(
            ); // https://docs.microsoft.com/ru-ru/windows/win32/seccrypto/certificate?redirectedfrom=MSDN
            /* доступные атрибуты:
             $issuerName = $cert->get_IssuerName();
             $fromDate = $cert->get_ValidFromDate();
             $toDate = $cert->get_ValidToDate();
             $serialNumber = $cert->get_SerialNumber();
             $subjectName = $cert->get_SubjectName();
             $thumbPrint = $cert->get_Thumbprint();
             echo '<pre>Кому выдан сертификат: ' . $subjectName . '<br>';
             echo 'Начало действия: ' . $fromDate . ' UTC<br>';
             echo 'Завершение действия: ' . $toDate . ' UTC<br>';
             echo 'Серийный номер: ' . $serialNumber . '<br>';
             echo 'Отпечаток: ' . $thumbPrint . '<br>';
             echo 'УЦ: ' . $issuerName . '</pre>';	*/
        } catch (Exception $e) {
            //

        }
    }

    static function SignatureCheck($data, $sgn, $detached, $IgnoreUntrustCA)
    {
        global $cert, $SignatureCheckResult;

        try {
            $result = false;
            $start = microtime(true);
            $SignedData = new \CPSignedData();
            $SignedData->set_ContentEncoding(BASE64_TO_BINARY);

            if (strlen($data) == strlen($sgn)) // либо в вызывающем коде должно быть предусмотрено
            {
                $detached = 0;
                $data = "";
            }

            if ($data) {
                $SignedData->set_Content(base64_encode($data));
                // printf(date("Y-m-d H:i:s.") . gettimeofday() ["usec"] . " set_Content OK<br>");
            }

            if (self::is_base64_data($sgn) == false) {
                $sgn = base64_encode($sgn);
            }

            //printf(date("Y-m-d H:i:s.") . gettimeofday() ["usec"] . " Проверка VerifyCades... <br>");
            try {
                $check = $SignedData->VerifyCades($sgn, 0x01, $detached);
                $result = true;
                $SignatureCheckResult = "Успешно проверена подпись и цепочка сертификатов";
                // printf(date("Y-m-d H:i:s.") . gettimeofday() ["usec"] . '<font color=green> Результат: успешно ' . $check . '</font> ');
            } catch (Exception $e) {
                /*
                 0x800B010A: Не удается построить цепочку сертификатов для доверенного корневого центра => установить через certmgr сертификат УЦ
                 */
                $result = false;
                $SignatureCheckResult = $e->getMessage();
                //printf(date("Y-m-d H:i:s.") . gettimeofday() ["usec"] . '<font color=red> Результат: Ошибка - ' . $e->getMessage() . "</font><br>");
                if (strpos($e->getMessage(), "0x800B010A")) {
                    $cert = self::GetSignerCert($SignedData);
                    $SignatureCheckResult = "Подпись корректна, но нет доверия к корневому сертификату УЦ";
                    if ($cert) {
                        $SignatureCheckResult = $SignatureCheckResult . ": " . iconv(
                                'WINDOWS-1251',
                                'UTF-8',
                                $cert->GetInfo('1')
                            );
                    }

                    if ($IgnoreUntrustCA) {
                        $result = true;
                    }
                }
                if (strpos($e->getMessage(), "0x80091004")) {
                    $SignatureCheckResult = "Подпись повреждена (Invalid cryptographic message), код ошибки: 0x80091004";
                    //printf(date("Y-m-d H:i:s.") . gettimeofday() ["usec"] . '<font color=red> Результат: <b>Подпись повреждена (Invalid cryptographic message)</b></font><br>');
                }
            }


            try {
                $cert = self::GetSignerCert($SignedData);
                //$signObject = $SignedData->get_Signers();
                //$sObj = $signObject->get_Item(1);
                //$cert = $sObj->get_Certificate(); // https://docs.microsoft.com/ru-ru/windows/win32/seccrypto/certificate?redirectedfrom=MSDN
                /* доступные атрибуты:
                 $issuerName = $cert->get_IssuerName();
                 $fromDate = $cert->get_ValidFromDate();
                 $toDate = $cert->get_ValidToDate();
                 $serialNumber = $cert->get_SerialNumber();
                 $subjectName = $cert->get_SubjectName();
                 $thumbPrint = $cert->get_Thumbprint();
                 echo '<pre>Кому выдан сертификат: ' . $subjectName . '<br>';
                 echo 'Начало действия: ' . $fromDate . ' UTC<br>';
                 echo 'Завершение действия: ' . $toDate . ' UTC<br>';
                 echo 'Серийный номер: ' . $serialNumber . '<br>';
                 echo 'Отпечаток: ' . $thumbPrint . '<br>';
                 echo 'УЦ: ' . $issuerName . '</pre>';
                 */
                // printf(date("Y-m-d H:i:s.") . gettimeofday() ["usec"] . " Завершена проверка подписи<br>");
                if ($detached == 0) {
                    $SignedContent = $SignedData->get_Content();
                    if (strlen($SignedContent) < 5000) {
                        $SignedContentDecoded = base64_decode($SignedContent);
                        $text = iconv('WINDOWS-1251', 'UTF-8', $SignedContentDecoded);
                        // mb_detect_encoding='.mb_detect_encoding($SignedContentDecoded) ...
                        //printf(' Подписанные данные(base64):<br><textarea cols=100 rows=6>' . $SignedContent . '</textarea><br>');
                        //printf(' Подписанные данные(decode):<br>SignedContentDecoded:<br><textarea cols=100 rows=6>' . $SignedContentDecoded . '</textarea><br>');
                        //printf(' SignedContentDecoded(WINDOWS-1251) <br><textarea cols=100 rows=6>' . $text . '</textarea><br>');
                    } else {
                        //printf(date("Y-m-d H:i:s.") . gettimeofday() ["usec"] . ' Подписанные данные - много данных (' . strlen($SignedContent) . ' байт), не будет отображено<br>');
                    }
                }
            } catch (Exception $e) {
                $SignatureCheckResult = "Exception: " . $e->getMessage();
                $result = false;
                //$result = $result . ' Exception SignedData: ' . $e->getMessage();
                //printf('Exception SignedData: ' . $e->getMessage() . "<br>");
            }
        } catch (Exception $e) {
            $SignatureCheckResult = "Exception: " . $e->getMessage();
            $result = false;
        }

        return $result;
    }

}