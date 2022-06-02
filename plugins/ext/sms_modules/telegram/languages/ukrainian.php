<?php

define('TEXT_MODULE_TELEGRAM_TITLE', 'Telegram');
define('TEXT_MODULE_TELEGRAM_BOT_TOKEN', 'Ідентифікатор бота (token)');
define(
    'TEXT_MODULE_TELEGRAM_BOT_TOKEN_DESCRIPTION',
    'Повідомлення будуть надходити від імені цього бота.<br>
В налаштуваннях СМС замість телефону вкажіть ваш &lt;chat_id&gt;<br>
chat_id можна отримати з об`єкта Update, отриманого за допомогою getUpdates після того, як ви напише вашому боту.<br>
Приклад: https://api.telegram.org/bot&lt;bot_token&gt;/getUpdates
'
);
