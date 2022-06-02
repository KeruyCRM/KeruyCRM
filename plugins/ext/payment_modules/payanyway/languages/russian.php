<?php

define('TEXT_MODULE_PAYANYWAY_TITLE', 'PayAnyWay');
define('TEXT_MODULE_PAYANYWAY_ID', 'Идентификатор');
define(
    'TEXT_MODULE_PAYANYWAY_ID_INFO',
    'Идентификатор магазина в системе MONETA.RU<br>Url уведомления (Pay URL): ' . url_for_file(
        'api/ipn.php?module_id='
    ) . '%s'
);
define('TEXT_MODULE_PAYANYWAY_CODE_ID', 'Код проверки целостности данных');
define('TEXT_MODULE_PAYANYWAY_CODE_ID_INFO', 'Устанавливается магазином при настройке счёта.');
define('TEXT_MODULE_PAYANYWAY_PAYMENT_COMPLATED', 'Зачислено');