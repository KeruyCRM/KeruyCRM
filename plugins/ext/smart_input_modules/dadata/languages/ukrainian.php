<?php

define('TEXT_MODULE_DADATA_TITLE', 'Сервис DaData.ru');
define('TEXT_MODULE_DADATA_MAX_COUNT', 'Количество подсказок');
define(
    'TEXT_MODULE_DADATA_MAX_COUNT_INFO',
    'Максимальное количество подсказок в выпадающем списке. По умолчанию 5. Не может быть больше 20.'
);
define('TEXT_MODULE_DADATA_MIN_CHARS', 'Минимальная длина текста');
define('TEXT_MODULE_DADATA_MIN_CHARS_INFO', 'Минимальная длина текста, после которой включаются подсказки.');
define('TEXT_MODULE_DADATA_TYPE_ADDRESS', 'Адрес');
define('TEXT_MODULE_DADATA_TYPE_PARTY', 'Организация');
define('TEXT_MODULE_DADATA_TYPE_BANK', 'Банк');
define('TEXT_MODULE_DADATA_TYPE_NAME', 'ФИО');
define('TEXT_MODULE_DADATA_TYPE_EMAIL', 'Email');
define(
    'TEXT_MODULE_DADATA_RULES_INFO',
    'Данные правила служат для заполнения конкретных полей, после выбора подсказки. Пример правила:<br>
[248] = data.city<br>
[247] = data.country<br>
где 248 и 247 - это ID полей ввода в форме, а data.city и data.country возвращаемые параметры  подсказки.<br>
Полный список возвращаемых параметров вы найдете в <a href="https://dadata.ru/suggestions/usage/" target="_blank">документации</a>.'
);

define('TEXT_MODULE_DADATA_TYPE_COUNTRY', 'Страны');
define('TEXT_MODULE_DADATA_TYPE_CURRENCY', 'Валюты');
define('TEXT_MODULE_DADATA_TYPE_POSTAL_OFFICE', 'Почтовые отделения');
define('TEXT_MODULE_DADATA_TYPE_FNS_UNIT', 'Налоговые инспеции');
define('TEXT_MODULE_DADATA_TYPE_OKVED2', 'ОКВЭД');
define('TEXT_MODULE_DADATA_TYPE_OKPD2', 'ОКПД');
define('TEXT_MODULE_DADATA_TYPE_FMS_UNIT', 'Кем выдан паспорт');
define('TEXT_MODULE_DADATA_TYPE_CAR_BRAND', 'Марки автомобилей');