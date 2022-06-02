<?php

define('TEXT_MODULE_LINK_FOR_CALLS_TITLE', 'Посилання для дзвінків');
define('TEXT_MODULE_LINK_FOR_CALLS_LINK_PREFIX', 'URL Префікс');
define(
    'TEXT_MODULE_LINK_FOR_CALLS_LINK_PREFIX_INFO',
    '<p>Цей модуль перетворює номер телефону на URL. Введіть префікс URL, наприклад: sip:, callto:, tel:</p>
Як префікс можна ввести власний url, наприклад:<br>
<i>http://localhost/cgi-bin/app.com?Number=[phone]&Account=@[13]</i><br>
[phone] - номер телефону із поля Телефон.<br>
[13] - поле введення із сутності користувачі, що замінюється на значення поточного користувача.'
);