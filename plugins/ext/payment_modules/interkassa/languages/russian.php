<?php

define('TEXT_MODULE_INTERKASSA_TITLE', 'InterKassa');
define('TEXT_MODULE_INTERKASSA_ID', 'Идентификатор кассы');
define('TEXT_MODULE_INTERKASSA_ID_INFO', ' Должен соответствовать Вашему идентификатору кассы.');
define(
    'TEXT_MODULE_INTERKASSA_ID_DESCRIPTION',
    '<span class="label label-warning">Важно:</span> на странице "URL взаимодействия" установить "разрешить переопределять в запросе" для всех URL. Http код успешного ответа: 200'
);
define('TEXT_MODULE_INTERKASSA_SECRET_KEY', 'Секретный ключ');
define(
    'TEXT_MODULE_INTERKASSA_SECRET_KEY_INFO',
    'Необходим если установлена опция "Проверять подпись в форме запроса платежа". Алгоритм подписи MD5.'
);