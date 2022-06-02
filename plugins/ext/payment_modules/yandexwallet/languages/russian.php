<?php

define('TEXT_MODULE_YANDEXWALLET_TITLE', 'ЮMoney');
define('TEXT_MODULE_YANDEXWALLET_ID', 'Номер кошелька');
define(
    'TEXT_MODULE_YANDEXWALLET_INFO',
    'Если вы хотите мгновенно узнавать о поступлении денег и сразу идентифицировать каждый перевод, включите <a href="https://yoomoney.ru/transfer/myservices/http-notification" target="_blank">HTTP-уведомления</a><br>Url уведомления: ' . url_for_file(
        'api/ipn.php?module_id='
    ) . '%s'
);
define('TEXT_MODULE_YANDEXWALLET_PAYMENT_TYPE_WALLET', 'ЮMoney');
define('TEXT_MODULE_YANDEXWALLET_PAYMENT_TYPE_CC', 'Банковской картой');
define('TEXT_MODULE_YANDEXWALLET_PAYMENT_COMPLATED', 'Зачислено');
