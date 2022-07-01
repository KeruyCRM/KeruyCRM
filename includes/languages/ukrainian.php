<?php

/**
 * Set language short code here (ISO 639-1 Code)
 * See Language Code Reference
 * http://www.w3schools.com/tags/ref_language_codes.asp
 */
define('TEXT_APP_LANGUAGE_SHORT_CODE', 'uk');

/**
 * Set the text direction: (ltr or rtl)
 * ltr - the writing direction is left-to-right. This is default
 * rtl - the writing direction is right-to-left
 */
define('TEXT_APP_LANGUAGE_TEXT_DIRECTION', 'ltr');

define('TEXT_FIELDTYPE_INPUT_TITLE', 'Поле для вводу');
define('TEXT_FIELDTYPE_INPUT_NUMERIC_TITLE', 'Числове поле');
define('TEXT_FIELDTYPE_INPUT_NUMERIC_COMMENTS_TITLE', 'Числове поле в коментарях');
define('TEXT_FIELDTYPE_INPUT_URL_TITLE', 'Поле для URL');
define('TEXT_FIELDTYPE_INPUT_DATE_TITLE', 'Дата з календарем');
define('TEXT_FIELDTYPE_INPUT_DATETIME_TITLE', 'Дата з календарем і вибором часу');
define('TEXT_FIELDTYPE_INPUT_FILE_TITLE', 'Поле для завантаження файлу');
define('TEXT_FIELDTYPE_ATTACHMENTS_TITLE', 'Вкладення');
define('TEXT_FIELDTYPE_TEXTAREA_TITLE', 'Поле для тексту');
define('TEXT_FIELDTYPE_TEXTAREA_WYSIWYG_TITLE', 'Поле для тексту з редактором');
define('TEXT_FIELDTYPE_DROPDOWN_TITLE', 'Список, що розкривається');
define('TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TITLE', 'Список, що випадає з кількома значеннями');
define('TEXT_FIELDTYPE_CHECKBOXES_TITLE', 'Прапорці');
define('TEXT_FIELDTYPE_RADIOBOXES_TITLE', 'Перемикачі');
define('TEXT_FIELDTYPE_FORMULA_TITLE', 'mySQL Формула');
define('TEXT_FIELDTYPE_USERS_TITLE', 'Користувачі');
define('TEXT_FIELDTYPE_GROUPEDUSERS_TITLE', 'Група користувачів');
define('TEXT_FIELDTYPE_ENTITY_TITLE', 'Сутність');
define('TEXT_FIELDTYPE_ACTION_TITLE', 'Дія');
define('TEXT_FIELDTYPE_ID_TITLE', 'ID');
define('TEXT_FIELDTYPE_DATEADDED_TITLE', 'Дата додавання');
define('TEXT_FIELDTYPE_CREATEDBY_TITLE', 'Створено');
define('TEXT_FIELDTYPE_USER_STATUS_TITLE', 'Статус користувача');
define('TEXT_FIELDTYPE_USER_ACCESSGROUP_TITLE', 'Група доступу');
define('TEXT_FIELDTYPE_USER_FIRSTNAME_TITLE', 'Ім’я');
define('TEXT_FIELDTYPE_USER_LASTNAME_TITLE', 'Прізвище');
define('TEXT_FIELDTYPE_USER_EMAIL_TITLE', 'E-mail');
define('TEXT_FIELDTYPE_USER_PHOTO_TITLE', 'Фото');
define('TEXT_FIELDTYPE_USER_LANGUAGE_TITLE', 'Мова');
define('TEXT_FIELDTYPE_USER_USERNAME_TITLE', 'Логін');
define('TEXT_FIELDTYPE_USER_PASSWORD_TITLE', 'Пароль');
define('TEXT_FIELDTYPE_USER_SKIN_TITLE', 'Зовнішній вигляд');
define('TEXT_FIELDTYPE_PROGRESS_TITLE', 'Прогрес');

define('TEXT_FIELDTYPE_INPUT_TOOLTIP', 'Просте текстове поле введення');
define('TEXT_FIELDTYPE_INPUT_NUMERIC_TOOLTIP', 'Це поле використовується для чисел');
define(
    'TEXT_FIELDTYPE_INPUT_NUMERIC_COMMENTS_TOOLTIP',
    'Дане поле призначене для відображення в коментарях. Значення цього поля дорівнює сумі введених чисел в коментарях.'
);
define(
    'TEXT_FIELDTYPE_INPUT_URL_TOOLTIP',
    'Текстове поле для URL-адрес. Значення поля буде автоматично перетворюватися в URL'
);
define('TEXT_FIELDTYPE_INPUT_DATE_TOOLTIP', 'Календар буде автоматично доданий до цього поля');
define('TEXT_FIELDTYPE_INPUT_DATETIME_TOOLTIP', 'Календар з вибором часу буде автоматично доданий до цього поля');
define('TEXT_FIELDTYPE_INPUT_FILE_TOOLTIP', 'Дозволяє завантажити один файл');
define('TEXT_FIELDTYPE_ATTACHMENTS_TOOLTIP', 'Дозволяє завантажувати декілька файлів відразу');
define('TEXT_FIELDTYPE_TEXTAREA_TOOLTIP', 'Багаторядкове поле для тексту');
define('TEXT_FIELDTYPE_TEXTAREA_WYSIWYG_TOOLTIP', 'Візуальний редактор буде автоматично доданий до цього поля');
define(
    'TEXT_FIELDTYPE_DROPDOWN_TOOLTIP',
    'Список, що випадає, з опціями, які можна визначити, натиснувши на ім’я поля в списку полів.'
);
define(
    'TEXT_FIELDTYPE_DROPDOWN_MULTIPLE_TOOLTIP',
    'Список, що випадає, з живим пошуком і можливістю вибору декількох значень.'
);
define(
    'TEXT_FIELDTYPE_CHECKBOXES_TOOLTIP',
    'Прапорці з опціями, які можна визначити, натиснувши на ім’я поля в списку полів.'
);
define(
    'TEXT_FIELDTYPE_RADIOBOXES_TOOLTIP',
    'Перемикачі з опціями, які можна визначити, натиснувши на ім’я поля в списку полів.'
);
define('TEXT_FIELDTYPE_FORMULA_TOOLTIP', 'Значення цього поля буде розраховуватися за формулою, зазначеною нижче.');
define(
    'TEXT_FIELDTYPE_USERS_TOOLTIP',
    'Спеціальне поле дозволяє призначати користувачів до елементів і налаштовувати доступ користувачів до елементів.'
);
define(
    'TEXT_FIELDTYPE_GROUPEDUSERS_TOOLTIP',
    'Спеціальне поле, що дозволяє призначати групу користувачів до елементів і налаштовувати доступ користувачів до елементів.'
);
define('TEXT_FIELDTYPE_ENTITY_TOOLTIP', 'Спеціальне поле, що дозволяє пов’язувати елементи з існуючої сутністю.');
define('TEXT_FIELDTYPE_USER_PASSWORD_TOOLTIP', 'Залиште поле порожнім і пароль буде створено автоматично.');
define('TEXT_FIELDTYPE_PROGRESS_TOOLTIP', 'Автоматично створює список від 1 до 100');

define('TEXT_BUTTON_LOGIN', 'Логін');
define('TEXT_BUTTON_SAVE', 'Зберегти');
define('TEXT_BUTTON_CLOSE', 'Закрити');
define('TEXT_BUTTON_EDIT', 'Редагувати');
define('TEXT_BUTTON_DELETE', 'Видалити');
define('TEXT_BUTTON_CHANGE', 'Змінити');
define('TEXT_BUTTON_ADD_FORM_TAB', 'Додати нову вкладку');
define('TEXT_BUTTON_SORT_FORM_TAB', 'Сортувати вкладки');
define('TEXT_BUTTON_PREVIEW_FORM', 'Попередній перегляд форми');
define('TEXT_BUTTON_ADD_NEW_FIELD', 'Додати нове поле');
define('TEXT_BUTTON_ADD_NEW_VALUE', 'Додати значення');
define('TEXT_BUTTON_RESTORE_PASSWORD', 'Відновити пароль');
define('TEXT_BUTTON_SEARCH', 'Пошук');
define('TEXT_BUTTON_CREATE_BACKUP', 'Створити резервну копію');
define('TEXT_BUTTON_DOWNLOAD', 'Завантажити');
define('TEXT_BUTTON_RESTORE', 'Відновити');
define('TEXT_BUTTON_ADD_COMMENT', 'Додати коментар');
define('TEXT_BUTTON_ADD_NEW_REPORT', 'Додати звіт');
define('TEXT_BUTTON_CONFIGURE_FILTERS', 'Фільтри');
define('TEXT_BUTTON_CONFIGURE_SORTING', 'Сортування');
define('TEXT_BUTTON_ADD_NEW_REPORT_FILTER', 'Додати фільтр');
define('TEXT_BUTTON_CREATE_SUB_VALUE', 'Додати вкладене значення');
define('TEXT_BUTTON_REMOVE_ALL_FILTERS', 'Видалити всі фільтри');
define('TEXT_BUTTON_REMOVE_FILTER', 'Видалити фільтр');
define('TEXT_BUTTON_CONTINUE', 'Продовжити');
define('TEXT_BUTTON_IMPORT', 'Імпорт');
define('TEXT_BUTTON_BIND', 'Зв’язати');
define('TEXT_BUTTON_CREATE', 'Створити');

define('TEXT_ERROR', 'Помилка:');
define('TEXT_ERROR_GENERAL', 'Деякі поля обов’язкові для заповнення. Вони візначені вище.');
define('TEXT_ERROR_REQUIRED', 'Це поле є обов’язковим для заповнення!');
define('TEXT_ERROR_FILE_EXTENSION', 'Цей тип файлу не допускається.');
define('TEXT_ERROR_REQUIRED_NUMBER', 'Будь ласка, введіть число.');
define('TEXT_ERROR_USEREMAIL_EXIST', 'E-mail вже існує!');
define('TEXT_ERROR_USERNAME_EXIST', 'Ім’я користувача вже існує!');
define('TEXT_ERROR_USEREMAIL_EMPTY', 'E-mail користувача не може бути порожнім.');
define('TEXT_ERROR_USERNAME_EMPTY', 'Ім’я користувача не може бути порожнім.');
define('TEXT_ERROR_FORMULA_CALCULATION', 'Сутність "%s". Поле "%s" [%s]. Помилка розрахунку формули "%s".');
define('TEXT_ERROR_USER_DELETE', 'Ви не можете видалити власний обліковий запис');
define('TEXT_ERROR_PASSWORD_CONFIRMATION', 'Підтвердження паролю має співпадати з вашим паролем.');
define('TEXT_ERROR_PASSWORD_LENGTH', 'Ваш пароль повинен містити не менше ' . CFG_PASSWORD_MIN_LENGTH . ' символів.');
define('TEXT_ERROR_DEFAULT_LDAP_GROUP', 'Група доступу для користувачів LDAP не налаштована. LDAP вхід не дозволено.');
define(
    'TEXT_ERROR_ITEM_HAS_SUB_ITEM',
    'Ви не можете видалити цей елемент, тому що він має вкладені елементи в сутностях: %s'
);
define('TEXT_ERROR_DELETE_USER_GROUP', 'Ви не можете видалити цю групу, тому що %s користувачі призначені на цю групу');

define('TEXT_MENU_DASHBOARD', 'Головна');
define('TEXT_MENU_LOGIN', 'Логін');
define('TEXT_MENU_LDAP_LOGIN', 'LDAP Логін');
define('TEXT_MENU_CONFIGURATION', 'Налаштування');
define('TEXT_MENU_COMMON_SETTINGS', 'Загальні налаштування');
define('TEXT_MENU_APPLICATION', 'Додаток');
define('TEXT_MENU_EMAIL_OPTIONS', 'Параметри електронної пошти');
define('TEXT_MENU_LDAP', 'LDAP');
define('TEXT_MENU_LOGIN_PAGE', 'Сторінки входу');
define('TEXT_MENU_APPLICATION_STRUCTURE', 'Структура додатка');
define('TEXT_MENU_USERS_ACCESS_GROUPS', 'Групи користувачів');
define('TEXT_MENU_TOOLS', 'Інструменти');
define('TEXT_MENU_BACKUP', 'Резервне копіювання бази даних');
define('TEXT_MENU_SERVER_INFO', 'Про сервер');
define('TEXT_MENU_SUPPORT', 'Підтримка');
define('TEXT_MENU_REPORT_FORUM', 'Форум');
define('TEXT_MENU_CONTACT_US', 'Контакти');
define('TEXT_MENU_ENTITIES_LIST', 'Сутності додатка');
define('TEXT_MENU_IMPORT_DATA', 'Імпорт даних');
define('TEXT_MENU_CHECK_VERSION', 'Перевірка версії');
define('TEXT_MENU_EXTENSION', 'Доповнення');

define('TEXT_HEADING_LDAP', 'Налаштування LDAP');
define('TEXT_LDAP_USE', 'Використовувати LDAP');
define('TEXT_LDAP_SERVER_NAME', 'Ім’я сервера LDAP');
define(
    'TEXT_LDAP_SERVER_NAME_NOTES',
    'Якщо використовується LDAP, вкажіть хост або IP-адресу сервера LDAP. Крім цього, ви можете вказати посилання. Наприклад, ldap://hostname:port/.'
);
define('TEXT_LDAP_SERVER_PORT', 'Порт сервера LDAP');
define(
    'TEXT_LDAP_SERVER_PORT_NOTES',
    'Ви можете вказати порт, який повинен використовуватися для з’єднання з сервером LDAP замість порту за замовчуванням 389.'
);
define('TEXT_LDAP_BASE_DN', 'Основне ім’я LDAP [ <var>dn</var> ]');
define(
    'TEXT_LDAP_BASE_DN_NOTES',
    'Унікальне ім’я (Distinguished Name), що визначає інформацію про користувача, наприклад <samp>o=My Company,c=US</samp>.'
);
define('TEXT_LDAP_UID', 'Ідентифікаційний номер LDAP [ <var>uid</var> ]');
define(
    'TEXT_LDAP_UID_NOTES',
    'Це ключ, за допомогою якого проводиться пошук заданого ідентифікатора входу в систему. Наприклад, <var>uid</var>, <var>sn</var> і так далі.'
);
define('TEXT_LDAP_USER_FILTER', 'Фільтр імені користувача LDAP');
define(
    'TEXT_LDAP_USER_FILTER_NOTES',
    'Надалі ви можете обмежити діапазон розшукуваних об’єктів за допомогою додаткових фільтрів. Наприклад, результатом <samp>objectClass=posixGroup</samp> буде <samp>(&amp;(uid=$username)(objectClass=posixGroup))</samp>.'
);
define('TEXT_LDAP_EMAIL_ATTRIBUTE', 'E-mail-атрибут LDAP');
define(
    'TEXT_LDAP_EMAIL_ATTRIBUTE_NOTES',
    'Задайте ім’я атрибута E-mail користувача (якщо він існує) для автоматичного присвоєння email-адрес новим користувачам. Якщо це поле залишити порожнім, то E-mail-адреси користувачів, які вперше увійшли на конференцію, також будуть порожніми.'
);
define('TEXT_LDAP_USER_DN', 'Користувач LDAP [ <var>dn</var> ]');
define(
    'TEXT_LDAP_USER_DN_NOTES',
    'Залиште поле порожнім для використання анонімного з’єднання. Якщо поле заповнене, застосунок використовує вказане ім’я при з’єднанні з сервером LDAP для пошуку правильного користувача. Наприклад, <samp>uid=Username,ou=MyUnit,o=MyCompany,c=US</samp>. Потрібно для серверів Active Directory.'
);
define('TEXT_LDAP_PASSWORD', 'Пароль LDAP');
define(
    'TEXT_LDAP_PASSWORD_NOTES',
    'Залиште поле порожнім для використання анонімного з’єднання. В іншому випадку введіть пароль для вищевказаного користувача. Потрібно для серверів Active Directory.<br /><em><strong>Увага:</strong> цей пароль буде збережено в базі даних у незашифрованому вигляді і буде видно всім, хто має доступ до бази або до цієї сторінки налаштувань.</em>'
);
define('TEXT_LDAP_IS_NOT_ENABLED', 'LDAP не включено');
define('TEXT_LDAP_ERROR_NOT_AVAILABLE', 'PHP LDAP розширення не доступно. Перевірте конфігурацію сервера.');
define('TEXT_LDAP_ERROR_CONNECTION', 'Не вдається підключитися до LDAP-сервера.');
define(
    'TEXT_LDAP_ERROR_BINDING',
    'Помилка з’єднання з сервером при використанні зазначених імені користувача і паролю.'
);
define(
    'TEXT_LDAP_ERROR_INCORRECT_PASSWORD',
    'Ви вказали невірний пароль. Будь ласка, перевірте пароль і спробуйте ще раз.'
);
define(
    'TEXT_LDAP_ERROR_INCORRECT_USERNAME',
    'Ви вказали невірне ім’я користувача. Будь ласка, перевірте своє ім’я та спробуйте ще раз.'
);

define('TEXT_HEADING_EMAIL_OPTIONS', 'Параметри електронної пошти');
define('TEXT_EMAIL_USE_NOTIFICATION', 'Використовувати повідомлення по електронній пошті ');
define('TEXT_EMAIL_SUBJECT_LABEL', 'Ярлик для теми листа');
define('TEXT_EMAIL_AMOUNT_PREVIOUS_COMMENTS', 'Кількість коментарів у листі');
define('TEXT_EMAIL_COPY_SENDER', 'Відсилати копію листа відправнику');
define('TEXT_EMAIL_SEND_FROM_SINGLE', 'Надсилати листи з однієї адреси електронної пошти ');
define('TEXT_EMAIL_ADDRESS_FROM', 'Адреса електронної пошти');
define('TEXT_EMAIL_NAME_FROM', 'Ім’я');
define('TEXT_EMAIL_USE_SMTP', 'Використовувати SMTP');
define('TEXT_EMAIL_SMTP_SERVER', 'SMTP сервер');
define('TEXT_EMAIL_SMTP_PORT', 'SMTP порт');
define('TEXT_EMAIL_SMTP_ENCRYPTION', 'SMTP шифрування');
define('TEXT_EMAIL_SMTP_LOGIN', 'SMTP логін');
define('TEXT_EMAIL_SMTP_PASSWORD', 'SMTP пароль');
define('TEXT_EMAIL_SMTP_CONFIGURATION', 'Налаштування SMTP');
define('TEXT_HEADING_USER_REGISTRATION_EMAIL', 'Шаблон листа для реєстрації нового користувача');
define('TEXT_MENU_USER_REGISTRATION_EMAIL', 'Реєстрація нового користувача');
define('TEXT_NEW_USER_DEFAULT_EMAIL_SUBJECT', 'Ваш обліковий запис було створено в ' . CFG_APP_NAME);
define('TEXT_REGISTRATION_EMAIL_SUBJECT', 'Тема:');
define('TEXT_REGISTRATION_EMAIL_SUBJECT_NOTE', 'Тема за замовчуванням: "' . TEXT_NEW_USER_DEFAULT_EMAIL_SUBJECT);
define('TEXT_REGISTRATION_EMAIL_BODY', 'Текст листа:');
define('TEXT_REGISTRATION_EMAIL_BODY_NOTE', 'Дані для входу будуть автоматично включені в нижню частину листа');

define('TEXT_NAV_ENTITY', 'Сутність');
define('TEXT_NAV_GENERAL_CONFIG', 'Загальна конфігурація');
define('TEXT_NAV_FIELDS_CONFIG', 'Конфігурація полів');
define('TEXT_NAV_FIELDS_CHOICES_CONFIG', 'Опції');
define('TEXT_NAV_VIEW_CONFIG', 'Налаштування відображення');
define('TEXT_NAV_FORM_CONFIG', 'Налаштування форми');
define('TEXT_NAV_LISTING_CONFIG', 'Налаштування списку');
define('TEXT_NAV_LISTING_FILTERS_CONFIG', 'Фільтри за замовчуванням');
define('TEXT_NAV_USER_PUBLIC_PROFILE_CONFIG', 'Публічний профіль');
define('TEXT_NAV_ACCESS_CONFIG', 'Доступ');
define('TEXT_NAV_ENTITY_ACCESS', 'Налаштування доступу');
define('TEXT_NAV_FIELDS_ACCESS', 'Доступ до полів');
define('TEXT_NAV_SWITCH', 'Переключитися до');
define('TEXT_NAV_COMMENTS_CONFIG', 'Налаштування коментарів');
define('TEXT_NAV_COMMENTS_ACCESS', 'Доступ до коментарів');
define('TEXT_NAV_COMMENTS_FIELDS', 'Форма коментарів');
define('TEXT_NAV_COMMENTS_FORM_CONFIG', 'Налаштування форми коментарів');

define('TEXT_USERNAME', 'Ім’я користувача');
define('TEXT_PASSWORD', 'Пароль');
define('TEXT_ARE_YOU_SURE', 'Ви впевнені?');
define('TEXT_CONFIGURATION', 'Конфігурація');
define('TEXT_WARNING', 'Попередження');
define('TEXT_DELETE', 'Видалити');
define('TEXT_YES', 'Так');
define('TEXT_NO', 'Ні');
define('TEXT_NAME', 'Ім’я');
define('TEXT_DESCRIPTION', 'Опис');
define('TEXT_SORT_ORDER', 'Сортування');
define('TEXT_HEADING_DELETE', 'Видалити?');
define('TEXT_DEFAULT_DELETE_CONFIRMATION', 'Ви впевнені, що хочете видалити "%s"?');
define('TEXT_NO_RECORDS_FOUND', 'Записів не знайдено');
define(
    'TEXT_DISPLAY_NUMBER_OF_ITEMS',
    'Відображено <nobr><strong>%s</strong> - <strong>%s</strong></nobr> <nobr>(всього <strong>%s</strong></nobr> елементів)'
);
define('TEXT_PREVNEXT_TITLE_PREVIOUS_PAGE', 'Попередня сторінка');
define('TEXT_PREVNEXT_TITLE_NEXT_PAGE', 'Наступна сторінка');
define('TEXT_PREVNEXT_TITLE_PAGE_NO', 'Сторінка %d');
define('TEXT_PREVNEXT_TITLE_PREV_SET_OF_NO_PAGE', 'Попередні %d сторінок');
define('TEXT_PREVNEXT_TITLE_NEXT_SET_OF_NO_PAGE', 'Наступні %d сторінок');
define('TEXT_WARN_DELETE_SUCCESS', 'Запис <b>%s</b> було успішно видалено');
define('TEXT_WIDTH', 'Ширина');
define('TEXT_HEIGHT', 'Висота');
define('TEXT_ADD', 'Додати');
define('TEXT_VIEW_ALL', 'Показати всі');
define('TEXT_INFO', 'Інформація');
define('TEXT_ACTION', 'Дія');
define('TEXT_ID', '#');
define('TEXT_DATE_ADDED', 'Дата додавання');
define('TEXT_CREATED_BY', 'Створено');
define('TEXT_SEARCH', 'Пошук');
define('TEXT_SEARCH_RESULT_FOR', 'Пошук за "<b>%s</b>"');
define('TEXT_ACTIVE', 'Активний');
define('TEXT_INACTIVE', 'Неактивний');
define('TEXT_IS_DEFAULT', 'За замовчуванням?');
define('TEXT_IS_LDAP_DEFAULT', 'За замовчуванням для LDAP?');
define('TEXT_ADMINISTRATOR', 'Адміністратор');

define('TEXT_LOGIN_DETAILS', 'Деталі для входу');
define('TEXT_MY_ACCOUNT', 'Мій обліковий запис');
define('TEXT_CHANGE_PASSWORD', 'Змінити пароль');
define('TEXT_LOGOFF', 'Вихід');
define('TEXT_EMAIL', 'E-mail');
define('TEXT_ENTITIES_HEADING', 'Сутності додатка');
define('TEXT_ADD_NEW_ENTITY', 'Додати нову сутність');
define('TEXT_HEADING_ENTITY_INFO', 'Інформація про сутність');
define('TEXT_HEADING_ENTITY_CONFIGURATION', 'Конфігурація сутності');
define('TEXT_HEADING_NEW_FORM_TAB', 'Нова вкладка форми');
define('TEXT_HEADING_EDIT_FORM_TAB', 'Редагувати вкладку форми');
define('TEXT_CREATE_SUB_ENTITY', 'Створити вкладену сутність');
define('TEXT_LISTING_CFG_INFO', 'Просто перемістіть поля між боксами для включення або виключення полів у списку');
define('TEXT_FIELDS_IN_LISTING', 'Поля у списку');
define('TEXT_FIELDS_EXCLUDED_FROM_LISTING', 'Виключені зі списку');
define('TEXT_WARN_DELETE_ENTITY_USERS', 'Ви не можете видалити сутність <b>%s</b> тому що це зарезервована сутність');
define(
    'TEXT_WARN_DELETE_ENTITY_HAS_PARENT',
    'Ви не можете видалити сутність <b>%s</b> тому що вона має вкладені суті. Видаліть спочатку вкладені сутності.'
);
define(
    'TEXT_WARN_DELETE_ENTITY_HAS_ITEMS',
    'Ви не можете видалити сутність <b>%s</b> тому що є додані дані. Видаліть всі дані для цієї сутності.'
);
define(
    'TEXT_WARN_DELETE_FROM_TAB',
    'Ви не можете видалити вкладку форми <b>%s</b> з полями.<br> Перемістіть поля на іншу вкладку форми.'
);
define('TEXT_TITLES', 'Заголовки');
define('TEXT_MENU_TITLE', 'Назва меню');
define('TEXT_MENU_TITLE_TOOLTIP', 'Це значення буде використовуватися в головному навігаційному меню');
define('TEXT_LISTING_HEADING', 'Заголовок списка');
define('TEXT_LISTING_HEADING_TOOLTIP', 'Це значення буде використовуватися в списку елементів.');
define('TEXT_WINDOW_HEADING', 'Заголовок вікна');
define(
    'TEXT_WINDOW_HEADING_TOOLTIP',
    'Це значення буде використовуватися у спливаючому вікні при вставці або редагуванні елемента.'
);
define('TEXT_INSERT_BUTTON_TITLE', 'Кнопка додавання');
define('TEXT_INSERT_BUTTON_TITLE_TOOLTIP', 'Це значення буде використовуватися на кнопці для додавання елемента.');
define('TEXT_EMAIL_SUBJECT_NEW_ITEM', 'Тема листа для нового елемента');
define('TEXT_EMAIL_SUBJECT_NEW_ITEM_TOOLTIP', 'Це значення буде використовуватися при створенні нового елемента');
define('TEXT_EMAIL_SUBJECT_NEW_COMMENT', 'Тема листа для нового коментаря');
define('TEXT_EMAIL_SUBJECT_NEW_COMMENT_TOOLTIP', 'Це значення буде використовуватися при додаванні нового коментаря');
define('TEXT_DEFAULT_EMAIL_SUBJECT_NEW_ITEM', 'Новий запис:');
define('TEXT_DEFAULT_EMAIL_SUBJECT_NEW_COMMENT', 'Новий коментар:');
define('TEXT_COMMENTS_TITLE', 'Коментарі');
define('TEXT_USE_COMMENTS', 'Використовувати коментарі');
define(
    'TEXT_USE_COMMENTS_TOOLTIP',
    'Дозволити додавати коментарі до елементів. Якщо ця опція включена, перевірте "Налаштування коментарів" в меню зліва.'
);
define('TEXT_COMMENTS_ACCESS_INFO', 'Налаштуйте, які групи користувачів будуть мати доступ до коментарів.');
define('TEXT_COMMENT_INFO', 'Коментар');
define('TEXT_COMMENT_WAS_DELETED', 'Коментар був видалений');
define(
    'TEXT_COMMENTS_FORM_CFG_INFO',
    'Ви можете налаштувати, які поля будуть доступні для редагування у формі коментарів.'
);
define('TEXT_AVAILABLE_FIELDS', 'Доступні поля');
define('TEXT_FIELDS_IN_COMMENTS_FORM', 'Поля в формі коментарів');
define('TEXT_HEADING_FIELD_INFO', 'Інформація про поле');
define('TEXT_GENERAL_INFO', 'Основна інформація');
define('TEXT_FORM_TAB', 'Вкладка форми');
define('TEXT_TOOLTIP', 'Підказка');
define('TEXT_SHORT_NAME', 'Коротка назва');
define('TEXT_IS_HEADING', 'Заголовок?');
define('TEXT_IS_REQUIRED', 'Обов’язкове поле?');
define('TEXT_TYPE', 'Тип');
define('TEXT_REQUIRED_MESSAGE', 'Повідомлення');
define('TEXT_FORM_TAB_INFO', 'Вкладка форми, на якій буде розміщено поле.');
define('TEXT_FIELD_NAME_INFO', 'Назва поля');
define('TEXT_FIELD_SHORT_NAME_INFO', 'Коротка назва, яка буде використовуватися в списку.');
define(
    'TEXT_IS_HEADING_INFO',
    'Якщо поле є заголовком, то значення цього поля буде використовуватися як заголовок на сторінці елемента і як тема листа. Тільки одне поле можна встановити в якості заголовка.'
);
define('TEXT_FIELD_TYPE_INFO', 'Тип впливає на відображення поля.');
define(
    'TEXT_IS_REQUIRED_INFO',
    'Оберіть цю опцію, якщо поле є обов’язковим. За замовчуванням користувач бачить повідомлення: "Це поле обов’язково!". Також ви можете ввести персоналізоване повідомлення нижче.'
);
define('TEXT_REQUIRED_MESSAGE_INFO', 'Повідомлення "Це поле обов’язково!" буде замінено.');
define('TEXT_TOOLTIP_INFO', 'Текст для підказки.');
define('TEXT_FORMULA', 'Формула');
define(
    'TEXT_FORMULA_TIP',
    'Використовуйте [ID поля] для встановлення значення поля у формулі. Приклад: ([36]+[54])/2 де 36 і 54 - ідентифікатори числових полів.'
);
define('TEXT_ENTER_WIDTH', 'Оберіть ширину поля.');
define('TEXT_INPUT_SMALL', 'Невелике поле ');
define('TEXT_INPUT_MEDIUM', 'Середнє поле');
define('TEXT_INPUT_LARGE', 'Велике поле');
define('TEXT_INPUT_XLARGE', 'Дуже велике поле');
define('TEXT_ENTER_HEIGHT', 'Введіть висоту в пікселях (наприклад 60) або залиште поле порожнім.');
define('TEXT_HEADING_VALUE_INFO', 'Деталі');
define('TEXT_BACKGROUND_COLOR', 'Колір фона');
define('TEXT_PARENT', 'Батьківський');
define('TEXT_CHOICES_PARENT_INFO', 'Виберіть батьківське значення');
define('TEXT_CHOICES_NAME_INFO', 'Введіть ім’я');
define('TEXT_CHOICES_IS_DEFAULT_INFO', 'Це значення буде вибрано за замовчуванням при створенні елемента.');
define('TEXT_CHOICES_BACKGROUND_COLOR_INFO', 'Значення у списку буде відображатися з вибраним кольором фону.');
define('TEXT_CHOICES_SORT_ORDER_INFO', 'Введіть сортування для значень в списку.');
define('TEXT_CHOICES_USERS_INFO', 'Оберіть користувачів, які будуть призначені до цього значення.');
define('TEXT_ALLOW_SEARCH', 'Використовувати для пошуку?');
define('TEXT_ALLOW_SEARCH_TIP', 'Ця опція дозволяє використовувати дане поле для пошуку елементів.');
define('TEXT_USERS_GROUPS', 'Група користувачів');
define('TEXT_VIEW_ACCESS', 'Перегляд');
define('TEXT_VIEW_ONLY_ACCESS', 'Тільки перегляд');
define('TEXT_CREATE_ONLY_ACCESS', 'Тільки створення');
define('TEXT_CREATE_ACCESS', 'Створити');
define('TEXT_UPDATE_ACCESS', 'Оновити');
define('TEXT_DELETE_ACCESS', 'Видалити');
define('TEXT_VIEW_ASSIGNED_ACCESS', 'Переглядати тільки призначені');
define('TEXT_ACCESS_UPDATED', 'Доступ оновлено!');
define(
    'TEXT_ENTITY_ACCESS_INFO',
    'На цій сторінці ви можете налаштувати доступ до кожної групи користувачів.<br>Для відкриття доступу виберіть опцію "Перегляд" і налаштуйте доступ для Створення/Редагування/Видалення.<br>Опція "Переглядати тільки назначені" дозволяє переглядати тільки ті елементи, на які назначені користувачі.'
);
define('TEXT_ADMINISTRATOR_FULL_ACCESS', 'Адміністратор має повний доступ');
define('TEXT_FIELDS', 'Поля');
define('TEXT_ACCESS', 'Доступ');
define('TEXT_HEADING_APPLICATION', 'Налаштування додатка');
define('TEXT_APPLICATION_NAME', 'Назва додатка');
define('TEXT_APPLICATION_SHORT_NAME', 'Коротка назва додатка');
define('TEXT_APPLICATION_LOGO', 'Логотип');
define('TEXT_APPLICATION_TIMEZONE', 'Часовий пояс');
define('TEXT_ROWS_PER_PAGE', 'Рядків на сторінці');
define('TEXT_DATE_FORMAT', 'Формат дати');
define('TEXT_DATETIME_FORMAT', 'Формат дати/часу');
define(
    'TEXT_DATE_FORMAT_INFO',
    'більше про формат дати див. <a target="_blanck" href="http://php.net/manual/en/function.date.php">http://php.net/manual/ru/function.date.php</a>'
);
define('TEXT_MIN_PASSWORD_LENGTH', 'Мінімальна довжина паролю користувача');
define('TEXT_HEADING_LOGIN_PAGE_CONFIGURATION', 'Налаштування сторінки входу');
define('TEXT_LOGIN_PAGE_HEADING', 'Заголовок сторінки');
define('TEXT_LOGIN_PAGE_CONTENT', 'Опис сторінки');
define('TEXT_LOGIN_PAGE_BACKGROUND', 'Зображення для фону');
define(
    'TEXT_LOGIN_PAGE_BACKGROUND_INFO',
    'Зображення розтягується на всю ширину сторінки. Рекомендується використовувати зображення 1920х1200'
);
define('TEXT_CONFIGURATION_UPDATED', 'Налаштування успішно оновлені');
define('TEXT_HEADING_USER_GROUP_INFO', 'Інформація про групу');
define('TEXT_HEADING_USERS_ACCESS_GROUPS', 'Групи доступу для користувачів');
define('TEXT_ADD_NEW_USER_GROUP', 'Додати нову групу');
define('TEXT_SORT_GROUPS', 'Сортувати групи');
define('TEXT_VIEW_ONLY', 'Тільки перегляд');
define('TEXT_HIDE', 'Приховати');
define('TEXT_HEADING_LOGIN', 'Вхід');
define('TEXT_HEADING_LDAP_LOGIN', 'LDAP Вхід');
define('TEXT_HEADING_CHANGE_PASSWORD', 'Змінити пароль');
define('TEXT_NEW_PASSWORD', 'Новий пароль');
define('TEXT_PASSWORD_CONFIRMATION', 'Підтвердження паролю');
define('TEXT_PASSWORD_UPDATED', 'Ваш пароль було успішно оновлено');
define('TEXT_USER_PASSWORD_UPDATED', 'Пароль було успішно оновлено');
define('TEXT_USER_NOT_FOUND', 'Немає відповідності для імені користувача або паролю.');
define('TEXT_USER_IS_NOT_ACTIVE', 'Ваш аккаунт неактивний');
define('TEXT_PASSWORD_FORGOTTEN', 'Забули пароль?');
define('TEXT_HEADING_RESTORE_PASSWORD', 'Відновити пароль');
define('TEXT_RESTORE_PASSWORD_EMAIL_SUBJECT', CFG_APP_NAME . ' - Новий пароль');
define('TEXT_RESTORE_PASSWORD_EMAIL_BODY', 'Новий пароль було запитано. Дані для входу в ' . CFG_APP_NAME . ':');
define('TEXT_RESTORE_PASSWORD_SUCCESS', 'Новий пароль відправлено на адресу електронної пошти.');
define('TEXT_REMEMBER_ME', 'Запам’ятати мене');
define('TEXT_HEADING_MY_ACCOUNT', 'Мій аккаунт');
define('TEXT_ACCOUNT_UPDATED', 'Аккаунт оновлено');
define('TEXT_ACCESS_FORBIDDEN', 'Доступ заборонено');
define('TEXT_ACCESS_FORBIDDEN_MESSAGE', 'Вибачте, у вас немає доступу до цієї сторінки.');
define('TEXT_NO_ACCESS', 'Ви не маєте доступу');
define('TEXT_BUTTON_INFO', 'Інформація');
define('TEXT_RESET_SEARCH', 'Скидання');
define('TEXT_HEADING_DB_BACKUP', 'Резервне копіювання бази даних');
define('TEXT_BACKUP_CREATED', 'Резервна копія створена');
define('TEXT_DOWNLOAD', 'Завантажити');
define('TEXT_RESTORE', 'Відновити');
define('TEXT_BACKUP_DELETED', 'Файл видалено');
define('TEXT_FILE_NOT_FOUND', 'Файл не знайдено');
define(
    'TEXT_DB_RESTORE_CONFIRMATION',
    'Ви впевнені, що хочете відновити дані з "%s" ? <br><b>Примітка:</b> всі наявні дані будуть замінені.'
);
define('TEXT_BACKUP_RESTORED', 'База відновлена');
define('TEXT_COMMENTS', 'Коментарі');
define('TEXT_ATTACHMENTS', 'Вкладення');
define('TEXT_REPORTS', 'Звіти');
define('TEXT_STANDARD_REPORTS', 'Стандартні звіти');
define('TEXT_HEADING_REPORTS', 'Звіти');
define('TEXT_IN_MENU', 'Відображати в меню');
define('TEXT_IN_DASHBOARD', 'Відображати на головній');
define('TEXT_HEADING_REPORTS_INFO', 'Інформація про звіт');
define('TEXT_WARN_DELETE_REPORT_SUCCESS', 'Звіт видалено');
define('TEXT_REPORT_ENTITY', 'Сутність');
define('TEXT_VIEW', 'Переглянути');
define('TEXT_PLEASE_WAIT_FILES_LOADING', 'Будь ласка, зачекайте, файли завантажуються.');
define('TEXT_ADD_ATTACHMENTS', 'Додати вкладення');
define('TEXT_HEADING_SERVER_INFORMATION', 'Інформація про сервер');
define('TEXT_HEADING_FILTERS_FOR_REPORT', 'Фільтри для звіту:');
define('TEXT_HEADING_FILTERS_FOR', 'Фільтри для:');
define('TEXT_HEADING_REPORTS_SORTING', 'Налаштування сортування');
define('TEXT_FIELDS_FOR_SORTING', 'Поля для сортування');
define('TEXT_FIELDS_EXCLUDED_FROM_SORTING', 'Виключити з сортування');
define(
    'TEXT_LISTING_SORTING_CFG_INFO',
    'Просто перемістіть поля між боксами, щоб включити або виключити поля для сортування.<br>Натисніть на значок зі стрілкою, щоб змінити тип сортування.'
);
define('TEXT_REPORT_NOT_FOUND', 'Звіт не знайдено!');
define('TEXT_FIELD', 'Поле');
define('TEXT_VALUES', 'Значення');
define('TEXT_HEADING_REPORTS_FILTER_INFO', 'Інформація про фільтр');
define('TEXT_SELECT_FIELD', 'Оберіть поле');
define('TEXT_WARN_DELETE_FILTER_SUCCESS', 'Фільтр видалено');
define('TEXT_WARN_DELETE_ALL_FILTERS_SUCCESS', 'Фільтри видалені');
define('TEXT_FILTERS_CONDITION', 'Стан');
define('TEXT_FILTERS_CONDITION_TOOLTIP', 'Включити або виключити обрані значення');
define('TEXT_CONDITION_INCLUDE', 'Включити');
define('TEXT_CONDITION_EXCLUDE', 'Виключити');
define(
    'TEXT_FILTERS_NUMERIC_FIELDS_TOOLTIP',
    'Приклади значень: <ul><li>1|2|3<br>означає значення=1 або значення=2 або значення=3</li><li>>5&<10<br> означає значення>5 і значення<10</li></ul>'
);
define('TEXT_DISPLAY_USERS_AS', 'Показати як');
define('TEXT_DISPLAY_USERS_AS_DROPDOWN', 'Список, що розкривається');
define('TEXT_DISPLAY_USERS_AS_CHECKBOXES', 'Прапорці');
define('TEXT_DISPLAY_USERS_AS_DROPDOWN_MULTIPLE', 'Список, що випадає з вибором кількох значень');
define('TEXT_DISPLAY_USERS_AS_TOOLTIP', 'Використовуйте прапорці для можливості вибору декількох користувачів');
define('TEXT_USERS_LIST', 'Список користувачів');
define('TEXT_FILTER_BY_DAYS', 'Фільтр по днях');
define(
    'TEXT_FILTER_BY_DAYS_TOOLTIP',
    'Доступні значення "0" - поточна дата, "-1" - попередній день, "+1" - наступний день. Ви також можете вказати кілька значень, наприклад "-1&2&3"'
);
define('TEXT_FILTER_BY_DATES', 'Фільтр за датою');
define('TEXT_FILTER_BY_DATES_TOOLTIP', 'Вказати період');
define('TEXT_FILTER_BY_USERS', 'Фільтр по користувачах');
define('TEXT_FILTER_BY_VALUES', 'Фільтр за значеннями');
define('TEXT_DATE_FROM', 'Від');
define('TEXT_DATE_TO', 'До');
define('TEXT_DESCENDING_ORDER', 'За зменшенням');
define('TEXT_ASCENDING_ORDER', 'За зростанням');
define(
    'TEXT_LISTING_FILTERS_CFG_INFO',
    'Встановіть фільтри, які будуть використовуватися за замовчуванням для списку елементів'
);
define('TEXT_SELECT_ENTITY', 'Оберіть сутність');
define(
    'TEXT_FIELDTYPE_ENTITY_SELECT_ENTITY_TOOLTIP',
    'Елементи з цієї сутності будуть використовуватися в якості значень для цього поля.'
);
define('TEXT_DETAILS', 'Деталі');
define('TEXT_LANGUAGE', 'Мова');
define('TEXT_SKIN', 'Зовнішній вигляд');
define(
    'TEXT_SKIN_TOOLTIP',
    'Якщо зовнішній вигляд за замовчуванням не заданий, користувачі можуть змінювати зовнішній вигляд.<br>Обраний зовнішній вигляд буде встановлений для всіх користувачів і можливість змінювати зовнішній вигляд буде прихована.'
);
define('TEXT_CHANGE_SKIN', 'Змінити зовнішній вигляд');
define('TEXT_COMMENT', 'Коментар');
define('TEXT_PREVIOUS_COMMENTS', 'Попередні коментарі');
define('TEXT_PAGE_NOT_FOUND_HEADING', 'Сторінку не знайдено!');
define('TEXT_PAGE_NOT_FOUND_CONTENT', 'Сторінка, яку ви відкриваєте, не існує.');
define('TEXT_TOP_ENTITIES', 'Сутності');
define('TEXT_SUB_ENTITIES', 'Вкладені сутності');
define('TEXT_FILTER_FIELD_VALUES_NOT_AVAILABLE', 'Значення цього поля не доступні в звіті.');
define('TEXT_NEW_PROJECT_VERSION', 'Нова версія!');
define('TEXT_NEW_PROJECT_VERSION_INFO', 'Нова версія "KeruyCRM %s" доступна для завантаження');
define('TEXT_REMOVE_INSTALL_FOLDER', 'Будь ласка, видаліть папку "install"');
define('TEXT_WITH_SELECTED', 'З обраними');
define('TEXT_EXPORT', 'Експорт');
define('TEXT_HEADING_EXPORT', 'Експорт');
define('TEXT_BUTTON_EXPORT', 'Експорт');
define('TEXT_SELECT_FIELD_TO_EXPORT', 'Оберіть поля для експорту');
define('TEXT_FILENAME', 'Ім’я файлу');
define('TEXT_PLEASE_SELECT_ITEMS', 'Будь ласка, виберіть елементи');
define('TEXT_INTERNAL_FIELDS', 'Внутрішні поля');
define('TEXT_RELATIONSHIP_HEADING', 'Зв’язок');
define('TEXT_URL_HEADING', 'Url');
define('TEXT_MORE_ACTIONS', 'Інші дії');
define('TEXT_EXPORT_COMMENTS', 'Експорт коментарів');
define('TEXT_HEADING_IMPORT_DATA', 'Імпорт даних');
define('TEXT_HEADING_IMPORT_DATA_TO', 'Імпорт даних до: %s');
define('TEXT_IMPORT_DATA_INFO', 'Ви можете імпортувати дані з таблиці в форматі Excel');
define('TEXT_PARENT_ITEM_ID', 'ID батьківського елемента');
define('TEXT_PARENT_ITEM_ID_INFO', 'Введіть ID батьківського елемента із сутності: %s');
define(
    'TEXT_PARENT_ITEM_ID_NOT_FOUND',
    'Елемент #%s не знайдений у сутності %s. Будь ласка, введіть правильний ідентифікатор елемента.'
);
define('TEXT_FILE_NOT_LOADED', 'Файл не завантажено');
define(
    'TEXT_IMPORT_BIND_FIELDS',
    'Зв’яжіть поля за допомогою стовпців таблиці нижче і почніть імпорт. Колонки, які не зв’язані з полем, не будуть імпортуватися.'
);
define('TEXT_BIND_FIELD', 'Прив’язати поле');
define('TEXT_HEADING_BIND_FIELD', 'Прив’язати поле');
define('TEXT_NONE', 'Жодного');
define('TEXT_IMPORT_FIRST_ROW', 'Імпортувати перший рядок?');
define('TEXT_DATEPICKER_DAYS', '"Неділя", "Понеділок", "Вівторок", "Середа", "Четвер", "П’ятниця", "Субота"');
define('TEXT_DATEPICKER_DAYSSHORT', '"Ндл", "Пнд", "Втр", "Срд", "Чтв", "Птн", "Суб", "Ндл"');
define('TEXT_DATEPICKER_DAYSMIN', '"Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб", "Нд"');
define(
    'TEXT_DATEPICKER_MONTHS',
    '"січня", "лютого", "березня", "квітня", "травня", "червня", "липня", "серпня", "вересеня", "жовтня", "листопада", "грудня"'
);
define(
    'TEXT_DATEPICKER_MONTHSSHORT',
    '"Січ", "Лют", "Бер", "Кві", "Тра", "Чер", "Лип", "Сер", "Вер", "Жов", "Лис", "Гру"'
);
define('TEXT_DATEPICKER_TODAY', 'Сьогодні');
define('TEXT_USE_SEARCH', 'Використовувати миттєвий пошук');
define('TEXT_USE_SEARCH_INFO', 'Відображати миттєві результати під час введення');
define('TEXT_SELECT_SOME_VALUES', 'Оберіть значення');
define('TEXT_NO_RESULTS_MATCH', 'Немає збігів з');
define('TEXT_USER_PWD_CHANGED_EMAIL_SUBJECT', 'Ваш пароль змінено');
define('TEXT_USER_PWD_CHANGED_EMAIL_BODY', 'Ваш пароль змінено адміністратором');
define('TEXT_DATE_BACKGROUND', 'Колір фону');
define('TEXT_DATE_BACKGROUND_TOOLTIP', 'Прострочені дати будуть виділятися вибраним кольором.');
define('TEXT_USE_EDITOR_IN_COMMENTS', 'Використовувати HTML редактор');
define('TEXT_USE_EDITOR_IN_COMMENTS_TOOLTIP', 'Використовувати HTML редактор для тексту коментаря');
define('TEXT_HEADING_CHECK_VERSION', 'Перевірка версії');
define('TEXT_VERSION_INFO', 'Ви використовуєте "KeruyCRM"');
define('TEXT_UPDATES_INSTALLED', 'Оновлення успішно встановлені');
define(
    'TEXT_USER_PUBLIC_PROFILE_CFG_INFO',
    'Публічний профіль відображається при наведенні курсору на ім’я користувача. За замовчуванням відображається фото користувача.<br>Просто перемістіть поля між боксами для включення або виключення полів в профілі.'
);
define('TEXT_FIELDS_IN_USER_PUBLIC_PROFILE', 'Поля в профілі');
define('TEXT_FIELDS_EXCLUDED_FROM_USER_PUBLIC_PROFILE', 'Виключені з профілю');
define('TEXT_USER_PUBLIC_PROFILE_NO_FIELDS', 'Немає доступних полів для відображення в профілі.');
define('TEXT_HEADING_EXTENSION', 'Доповнення');
define(
    'TEXT_EXTENSION_INFO',
    'Доповнення розширить ваш додаток додавши такі можливості як: діаграма Ганта, календар, графічний звіт, шаблони, облік часу, інформаційні сторінки і багато іншого.'
);
define('TEXT_PERIOD', 'Період');
define('TEXT_DAILY', 'Щодня');
define('TEXT_MONTHLY', 'Щомісяця');
define('TEXT_YEARLY', 'Річний');
define('TEXT_FIRST_DAY_OF_WEEK', 'Перший день тижня');
define('TEXT_FIRST_DAY_OF_WEEK_INFO', 'Буде відображатися як в перший день тижня в календарі');
define('TEXT_SETTINGS', 'Налаштування');
define('TEXT_FULL_ACCESS', 'Повний доступ');
define('TEXT_MORE_INFO', 'Детальніше');
define('TEXT_STEP', 'Крок');
define('TEXT_SAVING', 'Збереження...');
define('TEXT_UNDO', 'Скасувати');
define('TEXT_REDO', 'Повернути');
define('TEXT_INSERT', 'Вставити');
define('TEXT_MOVE_UP', 'Перемістити в гору');
define('TEXT_MOVE_DOWN', 'Перемістити до низу');
define('TEXT_ZOOM_OUT', 'Зменшити');
define('TEXT_ZOOM_IN', 'Збільшити');
define('TEXT_PRINT', 'Роздрукувати');
define('TEXT_MENU_DONATE', 'Допомогти проекту');
define(
    'TEXT_ERROR_NO_HEADING_FIELD',
    'Не знайдено поля, встановленого як Заголовок. Будь ласка встановіть поле як Заголовок на сторінці налаштування полів.'
);
define('TEXT_ERROR_LOADING_DATA', 'Помилка завантаження даних');
define('TEXT_CONFIGURE_DASHBOARD', 'Налаштування головної сторінки');
define(
    'TEXT_CONFIGURE_DASHBOARD_INFO',
    'Просто перемістіть звіти між боксами для включення або виключення звітів на головній сторінці'
);
define('TEXT_REPORTS_ON_DASHBOARD', 'Звіти на головній');
define('TEXT_MY_REPORTS', 'Мої звіти');
define('TEXT_CONFIGURE_THEME', 'Налаштування теми');
define('TEXT_SIDEBAR', 'Бокова панель');
define('TEXT_SIDEBAR_POSITION', 'Положення панелі');
define('TEXT_SCALE', 'Масштаб');
define('TEXT_DEFAULT', 'За замовчуванням');
define('TEXT_SIDEBAR_FIXED', 'Зафіксована');
define('TEXT_SIDEBAR_POS_LEFT', 'Зліва');
define('TEXT_SIDEBAR_POS_RIGHT', 'Справа');
define('TEXT_SCALE_REDUCED', 'Зменшений');

//new defines for version 1.5
define('TEXT_FIELDTYPE_RELATED_RECORDS_TITLE', 'Пов’язані записи');
define('TEXT_FIELDTYPE_RELATED_RECORDS_TOOLTIP', 'Спеціальне поле, що дозволяє пов’язувати записи між сутностями');
define(
    'TEXT_FIELDTYPE_RELATED_RECORDS_SELECT_ENTITY_TOOLTIP',
    'Записи обраної сутності можуть зв’язуватися з сутністю'
);
define('TEXT_BUTTON_ADD', 'Додати');
define('TEXT_BUTTON_LINK', 'Зв’язати');
define('TEXT_BUTTON_DELETE_RELATION', 'Видалити зв’язок');
define('TEXT_LINK_RECORD', 'Зв’язати запис');
define('TEXT_SEARCH_RECORD_BY_ID', 'Знайти запис за номером (ID):');
define('TEXT_ENTITY', 'Сутність');
define('TEXT_FILTERS_DISPLAY', 'Відображати');
define('TEXT_FILTERS_DISPLAY_WITH_RELATED_RECORDS', 'Відображати зі зв’язаними записами');
define('TEXT_FILTERS_DISPLAY_WITHOUT_RELATED_RECORDS', 'Відображати без пов’язаних записів');
define('TEXT_FILTERS_FOR_ENTITY', 'Фільтри для сутності');
define('TEXT_FILTERS_FOR_ENTITY_SHORT', 'Для сутності');
define('TEXT_REPORT', 'Звіт');
define('TEXT_ADD_IN', 'Додати до');
define('TEXT_SELECT_AN_OPTION', 'Оберіть один варіант');
define('TEXT_SELECT_SOME_OPTIONS', 'Оберіть деякі опції');
define('TEXT_DISPLAY_IN_MENU', 'Відображати в меню');
define('TEXT_MENU_ICON_TITLE', 'Іконка меню');
define(
    'TEXT_MENU_ICON_TITLE_TOOLTIP',
    'Введіть назву іконки з бібліотеки <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">Font Awesome</a> или <a href="https://icons8.com/line-awesome" target="_blank">Line Awesome</a><br>Наприклад: fa-bell или la-bell.'
);

//new defines for version 1.6
define('TEXT_FIELDTYPE_INPUT_MASKED', 'Поле з маскою введення');
define('TEXT_FIELDTYPE_INPUT_MASKED_TOOLTIP', 'Для введення даних у певному форматі (дата, телефон і т.і.)');
define('TEXT_INPUT_FIELD_MASK', 'Маска');
define(
    'TEXT_INPUT_FIELD_MASK_TIP',
    'Наприклад: 99/99/9999 або (999) 999-9999 або a*-999-a999<br>
<ul>
  <li>a - Користувач повинен ввести букву (A-Z,a-z)</li>
  <li>я - Користувач повинен ввести букву (А-Я, а-я)</li>
  <li>9 - Користувач повинен ввести цифру (0-9)</li>
  <li>* - Користувач повинен ввести букву або цифру </li>
</ul>'
);
define('TEXT_MENU_REVIEWS', 'Відгуки');

define('TEXT_NO_NUMERIC_FIELDS', 'Числові поля відсутні.');
define(
    'TEXT_EXTENSION_LICENSE_KEY_INFO',
    'При покупці Додатку вам потрібно ввести доменне ім’я <span class="label label-sm label-warning">%s</span>, щоб отримати ключ продукту.'
);
define('TEXT_URL_PREFIX', 'Префікс');
define(
    'TEXT_URL_PREFIX_TIP',
    'Якщо в посиланні відсутній префікс то префікс "http://" буде автоматично додано до посилання. Якщо ви хочете використовувати інший префікс за замовчуванням (наприклад "sip:"), введіть його в цьому полі.'
);
define('TEXT_URL_PREVIEW_TEXT', 'Текст посилання');
define(
    'TEXT_URL_PREVIEW_TEXT_TIP',
    'За замовчуванням на посиланні використовується текст "Переглянути". Ви можете ввести власний текст або ввести "none", щоб показати значення поля в посилання.'
);
define('TEXT_SEARCH_HELP', 'Рекомендації з пошуку');
define('TEXT_SEARCH_HELP_INFO_FIELDS', 'Ви можете зазначити поля за якими буде здійснюватись пошук.');
define(
    'TEXT_SEARCH_HELP_INFO_FIELDS_EXAMPLE',
    'Якщо поля не вказані, пошук здійснюється за всіма доступними полями в яких дозволений пошук.'
);
define(
    'TEXT_SEARCH_HELP_INFO_ANDOR',
    'При пошуку, Ви можете розділяти ключові слова і фрази приводами прийменниками *AND*, *OR*.'
);
define(
    'TEXT_SEARCH_HELP_INFO_ANDOR_EXAMPLE',
    'Наприклад, Ви можете ввести <u>Задача1 AND Задача2</u>.В результаті будуть виведені записи, що містять обидва слова. Проте, якщо Ви заносите <u>Задача1 OR Задача2</u>, Ви отримаєте список, який містить обидва або одне зі слів, заданих в пошуку. Якщо слова не розділяються символами AND або OR, пошук буде працювати з визначенням OR.'
);
define('TEXT_SEARCH_HELP_INFO_QUOTES', 'Ви можете також знайти точно задані слова, беручи їх в лапки.');
define(
    'TEXT_SEARCH_HELP_INFO_QUOTES_EXAMPLE',
    'Наприклад, якщо Ви шукаєте <u>"Моя задача"</u>, Ви отримаєте список записів, які містять цю фразу цілком.'
);
define('TEXT_SEARCH_HELP_INFO_BRACKETS', 'Дужки можуть використовуватися, щоб управляти порядком логічних дій.');
define(
    'TEXT_SEARCH_HELP_INFO_BRACKETS_EXAMPLE',
    'Наприклад, Ви можете ввести <u>Комп’ютери and (кишенькові or ноутбуки)</u>'
);
define('TEXT_ERROR_INVALID_KEYWORDS', 'Пошуковий запит складено невірно');
define('TEXT_SEARCH_IN_COMMENTS', 'Шукати в коментарях');
define('TEXT_GO_TO', 'Перейти до');
define('TEXT_FIELDTYPE_IMAGE_TITLE', 'Поле для завантаження зображення');
define('TEXT_FIELDTYPE_IMAGE_TOOLTIP', 'Дозволяє завантажити одне зображення');
define('TEXT_PREVIEW_IMAGE_SIZE', 'Ширина зображення');
define('TEXT_PREVIEW_IMAGE_SIZE_TIP', 'Максимальна ширина зображення при відображенні (за замовчуванням 250px)');
define('TEXT_USE_IMAGE_PREVIEW', 'Попередній перегляд');
define('TEXT_USE_IMAGE_PREVIEW_TIP', 'Використовувати попередній перегляд для зображень');
define('TEXT_NOTIFY_WHEN_CHANGED', 'Повідомляти при зміні');
define('TEXT_NOTIFY_WHEN_CHANGED_TIP', 'Призначені користувачі будуть повідомлені, коли значення зміниться.');
define('TEXT_DEFAULT_EMAIL_SUBJECT_UPDATED_ITEM', 'Запис оновлено:');
define('TEXT_EMAIL_SUBJECT_UPDATED_ITEM', 'Тема листа для оновленого елемента');
define('TEXT_EMAIL_SUBJECT_UPDATED_ITEM_TOOLTIP', 'Це значення буде використовуватися при оновленні елементу');
define('TEXT_ENTER_VALUE', 'Введіть значення');
define('TEXT_ENTER_CORRECT_VALUE', 'Введіть правильне значення');
define('TEXT_DAYS_BEFORE_DATE', 'Кілька днів до вказаної дати');
define('TEXT_DAYS_BEFORE_DATE_TIP', 'Введіть кілька днів і колір фону щоб виділити поле до зазначеної дати,');
define('TEXT_FIELD_SETTINGS', 'Налаштування поля');
define('TEXT_FIELDS_IN_POPUP', 'Поля в спливаючому вікні');
define(
    'TEXT_FIELDS_IN_LISTING_RELATED_ITEMS',
    'Відмітьте поля, які будуть відображатися в списку пов’язаних записів. Так як ширина списку обмежена використовуйте тільки ключові поля. Інші необхідні поля можуть відображатися в спливаючому вікні.'
);
define(
    'TEXT_FIELDS_IN_POPUP_RELATED_ITEMS',
    'Відмітьте поля які будуть відображатися в спливаючому вікні при наведенні на пов’язаний запис.'
);
define('TEXT_CONDITION_EMPTY_VALUE', 'Порожнє значення');
define('TEXT_DISPLAY_USER_NAME_ORDER', 'Відображення імені користувача');
define('TEXT_FIRSTNAME_LASTNAME', 'Ім’я Прізвище');
define('TEXT_LASTNAME_FIRSTNAME', 'Прізвище Ім’я');
define('TEXT_FILTER_BY_MONTH', 'Фільтр по місяцях');
define('TEXT_FILTER_BY_YEAR', 'Фільтр по роках');
define('TEXT_FILTER_BY_OVERDUE_DATE', 'Тільки прострочені дати');
define(
    'TEXT_FILTER_BY_MONTH_TOOLTIP',
    'Доступні значення: "0" - поточний місяць, "-1" - попередній місяць, "+1" - наступний місяць. Також Ви можете вказати кілька значень: "-1&2&3"'
);
define(
    'TEXT_FILTER_BY_YEAR_TOOLTIP',
    'Доступні значення: "0" - поточний рік, "-1" - попередній рік, "+1" - наступний рік. Також Ви можете вказати кілька значень: "-1&2&3"'
);
define('TEXT_RESERVED_FORM_TAB', 'Зарезервована вкладка. Ви можете змінити тільки назву.');
define(
    'TEXT_FIELDTYPE_RELATED_RECORDS_TOOLTIP_EXTRA',
    '
  <span class="help-block">
  	<span class="label label-sm label-info">Примітка:</span>
	дане поле не відображається в формі створення запису. Додати зв’язок можна вже після створення запису.
  </span>'
);

//new defines for version 1.7
define('TEXT_ASSIGNED_TO', 'Призначено на');
define('TEXT_DISPLAY_IN_HEADER', 'Відображати у верхньому меню');
define(
    'TEXT_DISPLAY_IN_HEADER_TOOLTIP',
    'Іконка звіту буде відображатися у верхньому навігаційному меню. Дані звіту будуть оновлюватися кожну хвилину.'
);
define('TEXT_LISTING_HORIZONTAL_SCROLL', 'Горизонтальна прокрутка списку');
define(
    'TEXT_LISTING_HORIZONTAL_SCROLL_INFO',
    'За замовчуванням горизонтальна прокрутка списку застосовується до всіх полів. Ви можете зафіксувати кілька полів з лівого боку.'
);
define('TEXT_NUMBER_FIXED_FIELD', 'Кількість зафіксованих полів');
define(
    'TEXT_NUMBER_FIXED_FIELD_INFO',
    'Введіть кількість зафіксованих полів, до яких горизонтальна прокрутка не буде застосовуватися.'
);
define('TEXT_DISPLAY_IN_MAIN_COLUMN', 'Відображати в основний колонці');
define(
    'TEXT_DISPLAY_IN_MAIN_COLUMN_INFO',
    'За замовчуванням, пов’язані записи відображаються в правій колонці. Після установки даної опції, пов’язані записи будуть відображатися окремим списком на сторінці запису'
);
define(
    'TEXT_SEARCH_RECORD_BY_ID_NAME_TIP',
    'Знайти запис за номером або назвою. Залиште поле порожнім, щоб відобразити всі записи.'
);
define('TEXT_DISPLAY_NAME_AS_LINK', 'Відображати назву як посилання');
define('TEXT_DISPLAY_NAME_AS_LINK_INFO', 'Назва запису буде посилатися на сторінку запису');
define('TEXT_UNLINK', 'Видалити зв’язок');
define('TEXT_SELECT_ALL', 'Обрати все');
define('TEXT_CALCULATE_TOTALS', 'Обчислювати суму в списку записів');
define('TEXT_CALCULATE_TOTALS_INFO', 'Сума значень даного поля буде обчислюватися в списку записів');
define('TEXT_DISPLAY_COMMENTS_ID', 'Відображати номер (ID) коментаря');
define('TEXT_DISPLAY_COMMENTS_TOOLTIP', 'За номером можна легко послатися на потрібний коментар');
define('TEXT_ERROR_COMMENTS_FORM_GENERAL', 'Жодне з полів не заповнено');
define('TEXT_SELECT_HEADING_FIELD', 'Оберіть поле, котре буде відображатися як заголовок в списку');
define('TEXT_ERROR_IMAGE_FILE_IS_NOT_UPLOADED', 'Файл не завантажено. Максимальний розмір файлу %s Мб.');
define('TEXT_FIELDTYPE_PARENT_ITEM_ID_TITLE', 'Зв’язок');
define('TEXT_APPLIED_FILTERS', 'Встановлені фільтри');
define('TEXT_NO_FILTERS_SETUP', 'Немає фільтрів');
define('TEXT_DEFAULT_FILTERS', 'Фільтри за замовчуванням');
define('TEXT_SAVE_FILTERS', 'Зберегти фільтри');
define('TEXT_UPDATE', 'Оновити');
define('TEXT_DELETE_FILTERS', 'Видалити фільтри');
define('TEXT_MESSAGE_FILTER_SAVED', 'Фільтр збережено');
define('TEXT_COPYRIGHT_NAME', 'Авторскі права');
define(
    'TEXT_COPYRIGHT_NAME_TOOLTIP',
    'Введіть назву вашої компанії. Текст відображається в нижній частині сторінки. Символ &copy; додається автоматично.'
);
define('TEXT_POWERED_BY', 'За підтримки');
define('TEXT_POWERED_BY_TITLE', 'Універсальна система управління проектами');
define('TEXT_MENU_GLOBAL_LISTS', 'Глобальні списки');
define('TEXT_HEADING_GLOBAL_LISTS', 'Глобальні списки');
define(
    'TEXT_GLOBAL_LISTS_INFO',
    'Глобальні списки можуть быти застосовані в будь-яких сутностях для типів полів: "Список, що випадає", "Прапорці", "Перемикачі".'
);
define('TEXT_HEADING_GLOBAL_LIST_INFO', 'Глобальний список');
define('TEXT_GLOBAL_LIST_CHOICES_CONFIG', 'Опції');
define('TEXT_BUTTON_SORT', 'Сортувати');
define('TEXT_SORT_VALUES', 'Сортувати значення');
define('TEXT_USE_GLOBAL_LIST', 'Використовувати глобальний список');
define('TEXT_USE_GLOBAL_LIST_TOOLTIP', 'Значення з обраного списку будуть використовуватися для даного поля');
define('TEXT_BUTTON_CANCEL', 'Відміна');
define('TEXT_NUMBER_FORMAT', 'Формат відображення числа');
define(
    'TEXT_NUMBER_FORMAT_INFO',
    'Введіть: число знаків після коми (0-9) / роздільник дробової частини / роздільник тисяч. Допустимі значення для роздільника: точка, кома, пробіл або символ *, при якому роздільник не застосовується. <br> Залиште поле порожнім, щоб не використовувати формат відображення.'
);
define(
    'TEXT_NUMBER_FORMAT_INFO_NOTE',
    'Зверніть увагу: цей формат використовується за замовчуванням тільки при створенні нового числового поля. При відображенні числа використовуються індивідуальні настройки числового поля.'
);
define('TEXT_BUTTON_SEND_TEST_EMAIL', 'Відправити тестового листа');
define('TEXT_EMAIL_USE_SMTP_INFO', 'Лист з темою "%s" буде відправлено за адресою: %s');
define('TEXT_TEST_EMAIL_SUBJECT', 'Це перевірка електронної пошти');
define('TEXT_EMAIL_SENT', 'Лист успішно відправлено');
define('TEXT_DEFAULT_TEXT', 'Текст за замовчуванням');
define('TEXT_DEFAULT_TEXT_INFO', 'Може быти порожнім, або, наприклад, введіть  "Будь ласка, оберіть"');
define('TEXT_DEFAULT_DATE', 'Дата за замовчуванням');
define(
    'TEXT_DEFAULT_DATE_INFO',
    'Залиште порожнім або введіть число. Наприклад: 0 - поточна дата, 5 - поточна дата + 5 днів.'
);
define('TEXT_FIELDTYPE_BOOLEAN_TITLE', 'Логічне поле');
define('TEXT_FIELDTYPE_BOOLEAN_TOOLTIP', 'В логічному полі можуть зберігатися тільки два значення: "Так" і "Ні"');
define('TEXT_BOOLEAN_TRUE', 'Так');
define('TEXT_BOOLEAN_FALSE', 'Ні');
define('TEXT_BOOLEAN_TRUE_VALUE', 'Значення "Так"');
define('TEXT_BOOLEAN_TRUE_VALUE_INFO', 'Залиште поле порожнім або введіть ваше значення');
define('TEXT_BOOLEAN_FALSE_VALUE', 'Значення "Ні"');
define('TEXT_BOOLEAN_FALSE_VALUE_INFO', 'Залиште поле порожнім або введіть ваше значення');
define('TEXT_FIELDTYPE_TEXT_PATTERN', 'Текст за шаблоном');
define('TEXT_FIELDTYPE_TEXT_PATTERN_TOOLTIP', 'Спеціальне поле, яке дозволяє відобразити текст в заданому шаблоні.');
define('TEXT_PATTERN', 'Шаблон');
define(
    'TEXT_ENTER_TEXT_PATTERN_INFO',
    'Використовуйте [ID поля] для встановлення значення поля в шаблоні.<br> Приклад: "[36]ваш текст[54]" де 36 і 54 - ідентифікатори полів.<br>Також, доступні: [id], [date_added], [created_by], [parent_item_id], [current_user_name]'
);
define(
    'TEXT_FIELDTYPE_USER_STATUS_TOOLTIP',
    'Неактивні користувачі не можуть увійти в систему і не отримують повідомлення по електронній пошті'
);
define('TEXT_APP_LOGO_URL', 'Посилання для лого');
define('TEXT_APP_LOGO_URL_TOOLTIP', 'Буде відкриватися в новому вікні');
define('TEXT_USERS_CONFIGURATION', 'Налаштування користувачів');
define('TEXT_ALLOW_CHANGE_USERNAME', 'Дозволити змінювати ім’я користувача');
define('TEXT_ALLOW_REGISTRATION_WITH_THE_SAME_EMAIL', 'Дозволити реєстрацію з однаковими email');
define('TEXT_FIELDS_TYPES_GROUP_INPUT_FIELDS', 'Поле введення');
define('TEXT_FIELDS_TYPES_GROUP_DATES', 'Дата');
define('TEXT_FIELDS_TYPES_GROUP_UPLOAD', 'Завантаження');
define('TEXT_FIELDS_TYPES_GROUP_TEXT', 'Текст');
define('TEXT_FIELDS_TYPES_GROUP_LIST', 'Список');
define('TEXT_FIELDS_TYPES_GROUP_USERS', 'Користувачі');
define('TEXT_FIELDS_TYPES_GROUP_ENTITY', 'Сутність');
define('TEXT_DOWNLOAD_ALL_ATTACHMENTS', 'Завантажити все');
define('TEXT_FILTER_BY_WEEK', 'Фильтр по тижнях');
define(
    'TEXT_FILTER_BY_WEEK_TOOLTIP',
    'Доступні значення: "0" - поточний тиждень, "-1" - попередній тиждень, "+1" - наступний тиждень. Так само ви можете вказати кілька значень: "-1&2&3"'
);
define('TEXT_HIDE_FIELD_NAME', 'Приховати назву поля');
define(
    'TEXT_HIDE_FIELD_NAME_TIP',
    'З метою економії місця, ви можете приховати назву поля при відображенні списку користувачів.'
);
define('TEXT_LAST_COMMENT_DATE', 'Дата останнього коментаря');
define('TEXT_HIDE_FIELD_IF_EMPTY', 'Приховати поле, якщо значення порожнє');
define('TEXT_HIDE_FIELD_IF_EMPTY_TIP', 'На сторінці запису поле буде приховано, якщо значення поля порожнє');
define('TEXT_TOOLTIP_DISPLAY_AS_ICON', 'Відображати як іконку');
define('TEXT_TOOLTIP_DISPLAY_AS_ICON_INFO', 'За замовчуванням, текст підказки відображається під полем');
define('TEXT_MENU_DATABASE_EXPORT', 'Експорт додатка');
define('TEXT_DATABASE_EXPORT_APPLICATION', 'Експорт шаблону додатка');
define('TEXT_BUTTON_EXPORT_DATABASE', 'Експорт шаблону додатка');
define(
    'TEXT_DATABASE_EXPORT_EXPLANATION',
    'Ця функція створює шаблон вашого додатка. <br> Експортуються тільки ті дані, які відносяться до налаштувань вашого додатка. <br> За допомогою шаблону, ви можете клонувати ваш додаток або поділитися ним.'
);
define(
    'TEXT_DATABASE_EXPORT_TOOLTIP',
    '<b>Примітка:</b> експортується тільки поточний користувач і його Логін/Пароль, які використовуються для входу в систему після встановлення шаблону. <br> Для встановлення шаблону, файл шаблону потрібно скопіювати в папку /backups на вашому сервері, і на сторінці "Створення резервних копій бази даних" відновити базу даних на основі шаблону.'
);
define('TEXT_COPY_FIELDS', 'Копіювати поля');
define('TEXT_COPY', 'Копіювати');
define('TEXT_PLEASE_SELECT_FIELDS', 'Будь ласка, оберіть поля');
define('TEXT_SELECT_FORM_TAB', 'Оберіть вкладку форми');
define('TEXT_FIELDS_COPY_SUCCESS', 'Поля успішно скопійовано');
define('TEXT_HEADING_TEMPLATE', 'Шаблон заголовка');
define('TEXT_HEADING_TEMPLATE_INFO', 'За замовчуванням використовується поле, встановлене як "Заголовок"');
define('TEXT_IS_UNIQUE_FIELD_VALUE', 'Унікальне поле');
define('TEXT_IS_UNIQUE_FIELD_VALUE_TIP', 'Введене значення буде перевірятися на унікальність для даної сутності');
define(
    'TEXT_PLEASE_WAIT_UNIQUE_FIELDS_CHECKING',
    'Выконується перевірка даних. Повторіть спробу через декілька секунд.'
);
define('TEXT_UNIQUE_FIELD_VALUE_ERROR', 'Значення поля повинно бути унікальним');
define('TEXT_UNIQUE_FIELD_VALUE_ERROR_GENERAL', 'Деякі поля повинні бути унікальними. Вони відзначені вище.');
define(
    'TEXT_IMPORT_DATA_TOOLTIP',
    'Перед початком імпорту, ви повинні підготувати дані в Excel:
<ul>
  <li>формат дати: YYYY-MM-DD</li>	
  <li>формат дати і часу: YYYY-MM-DD HH:MM</li>
  <li>формат чисел: 20000.00 (кількість знаків після коми - будь-яке, роздільник дробової частини - точка, роздільник тисяч - не використовується)</li>
  <li>логічне поле: true | false</li>
</ul>
Продублюйте поля, які потребують підготовки, і за допомогою формул в Excel підготуйте дані. Дані потрібно перезберегти, як значення, інакше імпортуються самі формули.'
);

//new defines for version 1.8
define(
    'TEXT_WARN_DELETE_ENTITY_HAS_RELATIONSHIP',
    'Сутність <b>%s</b> не можливо видалити, тому що встановлені наступні зв’язки: <br>%s.<br><br>Видаліть всі зв’язки перед видаленням сутності.'
);
define('TEXT_SEARCH_IN_ALL', 'Відключити фільтри');
define('TEXT_SEARCH_TYPE_AND', 'Шукати всі слова');
define('TEXT_SEARCH_TYPE_MATCH', 'Точний збіг');
define('TEXT_SEARCH_HELP_INFO_CONFIGURATION', 'Налаштування пошуку');
define(
    'TEXT_SEARCH_HELP_INFO_CONFIGURATION_DESCRIPTION',
    '
			<ul>
				<li>Відключити фільтри<br>Дана опція розширює область пошуку, відключивши фільтри.</li>
				<li>Шукати всі слова<br>Будуть показані записи, які містять всі слова.</li>
				<li>Точний збіг<br>Будуть показані записи, які містять точний збіг ключевих слів.</li>				
			</ul>'
);
define('TEXT_IS_ACTIVE_FILTER', 'Активний фільтр?');
define(
    'TEXT_IS_ACTIVE_FILTER_INFO',
    'Неактивні фільтри відображаються в списку встановлених фільтрів, але не використовуються при вибірці даних'
);
define('TEXT_SAVE_AS_TEMPLATE', 'Зберегти як шаблон');
define(
    'TEXT_SAVE_AS_TEMPLATE_INFO',
    'Вибрані значення можна зберегти як шаблон. Шаблони доступні в списку встановлених фільтрів.'
);
define('TEXT_MY_TEMPLATES', 'Мої шаблони');
define('TEXT_ENTER_TEMPLATE_NAME', 'Введіть назву шаблону');
define('TEXT_SAVE_TEMPLATE_NOTE', 'Позначені поля будуть збережені в новий шаблон');
define('TEXT_TEMPLATES_FIELDS', 'Поля');
define('TEXT_TEMPLATE_ALREADY_EXIST', 'Шаблон "%s" уже існує');
define('TEXT_TEMPLATES_UPDATE_FIELDS', 'Оновити вибрані поля для цього шаблону');
define('TEXT_SELECT_TEMPLATE', 'Вибрати шаблон');
define('TEXT_ADD_NEW_TEMPLATE', 'Новий шаблон');
define('TEXT_BUTTON_UPDATE', 'Оновити');
define('TEXT_UPDATE_SELECTED_TEMPLATE_INFO', 'Позначені поля будуть оновлені для обраного шаблону');
define('TEXT_MENU_CONFIGURATION_MENU', 'Налаштування меню');
define('TEXT_CONFIGURATION_MENU_EXPLAIN', 'У вас є можливість налаштувати додаткові розділи головного меню.');
define('TEXT_ADD_NEW_MENU_ITEM', 'Добавити розділ меню');
define('TEXT_SORT', 'Сортувати');
define('TEXT_SELECT_ENTITIES', 'Выберіть сутності');
define('TEXT_FIELDTYPE_INPUT_VPIC_TITLE', 'vPIC');
define(
    'TEXT_FIELDTYPE_INPUT_VPIC_TOOLTIP',
    'Поле для вводу з кнопкою "розшифрувати" дозволяє розшифрувати ідентифікаційний номер автомобіля (vin) на регульовані види транспортного засобу з використанням сервісу <a href="https://vpic.nhtsa.dot.gov/" target="_blank">vpic.nhtsa.dot.gov</a>'
);
define('TEXT_FIELDS_TYPES_GROUP_SPECIAL_FIELDS', 'Спеціальні');
define('TEXT_DECODE_VIN', 'Розшифрування ідентифікаційного номеру автомобіля');
define('TEXT_VPIC_AUTO_FILL_FIELDS', 'Автоматично заповнювати поля');
define(
    'TEXT_VPIC_AUTO_FILL_FIELDS_TIP',
    'Дані будуть автоматично заповнюватись, якщо ім’я поля співпадають з іменем поля характеристики'
);
define('TEXT_VPIC_OTHER_DETAILS', 'Характеристики');
define(
    'TEXT_VPIC_OTHER_DETAILS_TIP',
    'За замовчуванням перевіряються наступні характеристики: Make, Manufacturer Name, Model, Model Year, Vehicle Type, Body Class<br>Якщо вам потрібно більше, введіть назву через кому'
);
define('TEXT_DATA_SAVED', 'Дані збережено');
define('TEXT_DESELECT', 'Зняти виділення');
define('TEXT_DELETE_SELECTED_CONFIRMATION', 'Ви впевнені що хочете видалити обрані записи?');
define('TEXT_QUICK_COMMENT', 'Швидкий коментар');
define('TEXT_COMMENT_PLACEHOLDER', 'Введіть ваш коментар тут');
define('TEXT_REPLY', 'Відповісти');
define(
    'TEXT_USERS_IMPORT_NOTE',
    'При імпорті користувачів, обов’язковими являються наступні поля: Ім’я, Прізвище, E-mail. Якщо ім’я користувача не вказано, то використовується ім’я користувача з E-mail<br>
		<b>Зверніть увагу:</b> при імпорті користувачів повідомлення про нові облікові записи не відправляються. Це пов’язано з тим, що веб-сервери мають обмеження на кількість відправки пошти за секунду.<br>
		Імпортованим користувачам необхідно використовувати форму відновлення паролю для отримання свого паролю.'
);
define('TEXT_USERS_IMPORT_USERS_GROUP', 'Оберіть групу доступу користувачів');
define('TEXT_IMPORT_BIND_FIELDS_ERROR', 'Помилка імпорту: не обрано поля які зв’язані з стовпцями таблиці');
define(
    'TEXT_IMPORT_BIND_USERS_FIELDS_ERROR',
    'Помилка імпорту: обов’язковими являються наступні поля: Ім’я, Прізвище, E-mail'
);
define('TEXT_IMPORT_SET_PWD_AS_USERNAME', 'Використовувати ім’я користувача в якості паролю');
define('TEXT_USERS_IMPORT_ERROR', 'Наступні користувачі не були імпортованими, так як уже існують:');
define('TEXT_SIZE', 'Розмір');
define('TEXT_BACKUP_TYPE_AUTO', 'Автоматично');
define('TEXT_BACKUP_FOLDER', 'Папка резервного копіювання');
define('TEXT_BACKUP_DESCRIPTION_TIP', 'Введіть коментар до резервної копії БД або залиште поле пустим');
define('TEXT_BUTTON_DB_RESTORE_FROM_FILE', 'Відновити БД з файлу');
define('TEXT_FILE', 'Файл');
define('TEXT_MAX_FILE_SIZE', 'Максимальний розмір файлу %s Мб.');
define(
    'TEXT_CRON_BACKUP',
    'Резервне копіювання за допомогою <a href="https://keruy.com.ua/index.php?p=70" target="_blank"><u>cron</u></a>'
);
define('TEXT_MENU_MAINTENANCE_MODE', 'Режим обслуговування');
define('TEXT_HEADING_MAINTENANCE_MODE', 'Режим обслуговування');
define('TEXT_MAINTENANCE_MODE', 'Режим обслуговування');
define(
    'TEXT_MAINTENANCE_MODE_NOTE',
    'Якщо режим обслуговування включено, то увійти в систему зможуть тільки адміністратори.<br>Інші користувачі, які були авторизовані, автоматично вийдуть з додатку.<br>На сторінці входу буде відображено повідомлення, яке настроюється нижче.'
);
define('TEXT_MESSAGE_HEADING', 'Заголовок повідомлення');
define('TEXT_MESSAGE_CONTENT', 'Зміст повідомлення');
define('TEXT_MAINTENANCE_MESSAGE_HEADING', 'Включено режим обслуговування!');
define('TEXT_MAINTENANCE_MESSAGE_CONTENT', 'Доступ дозволений тільки адміністраторам');
define(
    'TEXT_FORMULA_TIP_USAGE',
    'Ви можете використовувати в формулі синтаксис MySql: умовний оператор "if([344]>10,[321],0)", математичні функції "ABS([23])" і службові поля "id, date_added, create_by"'
);
define('TEXT_MENU_ATTACHMENTS', 'Вкладення');
define('TEXT_HEADING_ATTACHMENTS_CONFIGURATION', 'Налаштування вкладень');
define('TEXT_MAX_UPLOAD_FILE_SIZE', 'Максимальний розмір завантажуваного файлу');
define(
    'TEXT_MAX_UPLOAD_FILE_SIZE_TIP',
    '<a href="https://keruy.com.ua/index.php?p=10" target="_blank">' . 'Як змінити це значення?' . '</a>'
);
define('TEXT_RESIZE_IMAGES', 'Змінювати розмір зображень');
define(
    'TEXT_RESIZE_IMAGES_TIP',
    'Для економії місця на сервері, ви можете зменшувати завантажувані зображення.<br>При включенні опції завантажувані зображення будуть масштабуватись по ширині або висоті, вказаній нижче.'
);
define('TEXT_MAX_IMAGE_WIDTH', 'Максимальна ширина зображення');
define('TEXT_MAX_IMAGE_HEIGHT', 'Максимальна висота зображення');
define('TEXT_ENTER_VALUES_IN_PIXELS_OR_LEAVE_BLANK', 'Введіть розмір в пікселях або залиште поле пустим.');
define('TEXT_IMAGES_TYPES', 'Тип зображень');
define('TEXT_RESIZE_IMAGES_TYPES_TIP', 'Вкажіть типи зображень, для яких буде застосовуватись масштабування');
define('TEXT_SKIP_IMAGE_RESIZE', 'Не застосовувати масштабування для зображень');
define(
    'TEXT_SKIP_IMAGE_RESIZE_TIP',
    'Введіть розмір в пікселях або залиште поле пустим.<br>Якщо ширина або висота зображення перевищує вказаний розмір, масштабування не буде застосовуватись.'
);
define('TEXT_RECAPTCHA_VERIFY_ROBOT', 'Будь ласка, підтвердіть, що ви не робот!');
define('TEXT_MENU_SECURITY', 'Безпека');
define('TEXT_HEADING_SECURITY_CONFIGURATION', 'Налаштування бепеки');
define('TEXT_STATUS', 'Статус');
define('TEXT_RECAPTCHA_SITE_KEY', 'Ключ');
define('TEXT_RECAPTCHA_SECRET_KEY', 'Секретний ключ');
define(
    'TEXT_RECAPTCHA_INFO',
    'reCAPTCHA  буде відображитись на сторінці входу. <a href="https://www.google.com/recaptcha/intro/index.html" target="_blank">Детальніше про reCAPTCHA</a>'
);
define(
    'TEXT_RECAPTCHA_HOW_ENABLE',
    'Для включення відкрийте файл "config/security.php", змініть CFG_RECAPTCHA_ENABLE на true, введіть ключ в CFG_RECAPTCHA_KEY і секретний ключ в CFG_RECAPTCHA_SECRET_KEY.'
);
define('TEXT_RESTRICTED_COUNTRIES', 'Обмеження по країнах');
define('TEXT_ALLOWED_COUNTRIES', 'Дозволені країни');
define(
    'TEXT_RESTRICTED_COUNTRIES_INFO',
    'На сторінці входу буде виконуватись провірка країни користувача. Країна користувача визначається по IP. Якщо країна відсутня в списку дозволених або не визначається, сторінка входу буде не доступною.'
);
define(
    'TEXT_RESTRICTED_COUNTRIES_HOW_ENABLE',
    'Для включення відкрийте файл "config/security.php", змініть CFG_RESTRICTED_COUNTRIES_ENABLE на true і введіть список дозволених країн через кому в CFG_ALLOWED_COUNTRIES_LIST, наприклад "RU,US"'
);
define('TEXT_RESTRICTED_BY_IP', 'Обмеження по IP');
define(
    'TEXT_RESTRICTED_BY_IP_INFO',
    'На сторінці входу буде виконуватись провірка IP користувача. Якщо IP відсутній в списку дозволених або не визначається, сторінка входу буде не доступною.'
);
define(
    'TEXT_RESTRICTED_BY_IP_HOW_ENABLE',
    'Для включення відкрийте файл "config/security.php", змініть CFG_RESTRICTED_BY_IP_ENABLE на true і введіть список дозволених IP через кому в CFG_ALLOWED_IP_LIST, наприклад "192.168.2.1,192.168.2.2"'
);
define('TEXT_ALLOWED_IP', 'Дозволені IP');
define('TEXT_DISPLAY_COUNTER_ON_DASHBOARD', 'Відображати як лічильник на головній');
define('TEXT_STATISTICS', 'Статистика');
define('TEXT_COUNTERS', 'Лічільники');
define('TEXT_NOTIFICATIONS_SCHEDULE', 'Відомості по розкладу');
define(
    'TEXT_NOTIFICATIONS_SCHEDULE_TIP',
    'Перед включенням даної можливості необхідно налаштувати CRON_HTTP_SERVER_HOST в файлі config/server.php і створити завдання, яке буде виконуватись кожну годину (<a href="https://keruy.com.ua/index.php?p=70" target="_blank"><u>cron</u></a>):'
);
define(
    'TEXT_NOTIFICATIONS_SCHEDULE_INFO',
    'Користувачі зможуть налаштовувати відомості по розкладу для стандартних звітів'
);
define('TEXT_DAY', 'День');
define('TEXT_TIME', 'Час');
define('TEXT_NOTIFICATION', 'Відомості');
define(
    'TEXT_REPORTS_NOTIFICATION_EMAIL',
    'Вітаємо.<p>Ви отримали це повідомлення тому, що слідкуєте за звітом "%s".</p>'
);
define('TEXT_TECHNICAL_SUPPORT', 'Відділ технічної підтримки');
define(
    'TEXT_TECHNICAL_SUPPORT_INFO',
    'Внутрішні відомості, такі як відновлення паролю і інші, будуть відсилатись від email технічної підтримки'
);
define('TEXT_DISABLE_NOTIFICATIONS', 'Відключити відомості');
define(
    'TEXT_DISABLE_NOTIFICATIONS_INFO',
    'Ви не будете отримувати відомості по електронній пошті для записів на які ви назначені'
);
define('TEXT_HEADER_TOP_MENU', 'Верхнє меню');
define(
    'TEXT_CONFIGURE_HOT_REPORTS_INFO',
    'Просто перемістіть звіти між боксами для включення або виключення звітів в верхньому меню'
);
define('TEXT_URL', 'Url');
define('TEXT_DEFAULT_VALUE', 'Значення за замовчуванням');
define('TEXT_DEFAULT_VALUE_INFO', 'Введіть значення за замовчуванням або залиште поле пустим');
define('TEXT_DO_NOT_NOTIFY', 'Не повідомляти');
define('TEXT_DO_NOT_NOTIFY_INFO', 'Назначені користувачі не будуть отримувати повідомлення');
define('TEXT_IS_ACTIVE', 'Активний');
define('TEXT_SEND_ON_SCHEDULE', 'Відправляти по розкладу');
define('TEXT_SEND_EMAILS_ON_SCHEDULE', 'Відправляти листи по розкладу');
define(
    'TEXT_SEND_EMAILS_ON_SCHEDULE_INFO',
    'Перед включенням даної можливості необхідно встановити завдання за розкладом кожну хвилину (<a href="https://keruy.com.ua/index.php?p=70" target="_blank"><u>cron</u></a>):'
);
define('TEXT_MAXIMUM_NUMBER_EMAILS', 'Максимальне число листів');
define(
    'TEXT_MAXIMUM_NUMBER_EMAILS_INFO',
    'Максимальне число листів, яке можливо відправити при виконанні Cron за один раз'
);
define(
    'TEXT_SEND_EMAILS_ON_SCHEDULE_DESCRIPTION',
    'Перед включенням даної можливості провірте <a href="https://keruy.com.ua/index.php?p=8" target="_blank"><u>обмеження на відправлення пошти</u></a> з вашого сервера.'
);
define('TEXT_MAILER_ERROR', 'Помилка відправлення повідомлення для %s');
define('TEXT_HIDE_INSERT_BUTTON_IN_REPORTS', 'Приховати кнопку добавлення в звітах');
define('TEXT_DISABLE_NOTIFICATIONS_FIELDS_INFO', 'Назначені користувачі не будуть отримувати відомості');
define('TEXT_FROM_TO', 'з %s по %s');

//new defines for version 1.9
define('TEXT_MOVE_LEFT', 'Перемістити вліво');
define('TEXT_MOVE_RIGHT', 'Перемістити вправо');
define('TEXT_LIMITED_ACCESS', 'Обмежений доступ');
define('TEXT_SEARCH_USERS', 'Пошук користувачів');
define('TEXT_USER_IS_NOT_FOUND', 'Користувач не знайдений');
define('TEXT_USERS', 'Користувачі');
define('TEXT_FIELDTYPE_MAPBBCODE_TITLE', 'Мапа');
define(
    'TEXT_FIELDTYPE_MAPBBCODE_TOOLTIP',
    'Поле вводу дозволяє вводити координати мапи і відображати мапу відповідно до заданих координат. За допомогою вбудованого редактора мапи, у вас є можливість виділити на мапі область, намалювати лінію або вставити маркер.'
);
define('TEXT_DEFAULT_POSITION', 'Позиція за замовчуванням');
define('TEXT_DEFAULT_POSITION_TIP', 'ведіть координати на мапі. Наприклад: 45.26329,34.10156');
define('TEXT_DEFAULT_ZOOM', 'Масштаб за замовчуванням');
define('TEXT_DEFAULT_ZOOM_TIP', 'Масштаб мапи. Наприклад: 8');
define('TEXT_OPEN_MAP_EDITOR', 'Відкрити редактор мап');
define('TEXT_USERS_NOTIFICATIONS', 'Мої повідомлення');
define('TEXT_DELETE_SELECTED', 'Видалити обране');
define('TEXT_DISABLE_EMAIL_NOTIFICATIONS', 'Відключити сповіщення поштою');
define('TEXT_DISABLE_INTERNAL_NOTIFICATIONS', 'Відключити внутрішні повідомлення');
define('TEXT_DISABLE_INTERNAL_NOTIFICATIONS_INFO', 'Верхнє меню "Мої повідомлення" буде відключено.');
define('TEXT_DISABLE_HIGHLIGHT_UNREAD', 'Відключити виділення непрочитаних записів');
define('TEXT_DISABLE_HIGHLIGHT_UNREAD_INFO', 'Буде відключено підсвічування непрочитаних записів.');
define('TEXT_FIELDTYPE_BARCODE_TITLE', 'Штрих-код');
define(
    'TEXT_FIELDTYPE_BARCODE_TOOLTIP',
    'В формі записів значення поля зберігається як число, а в шаблоні експорту це поле виводиться у вигляді зображення штрих-коду.'
);
define('TEXT_FIELDTYPE_BARCODE_HEIGHT_TIP', 'Висота штрих-коду. За замовчуванням 30 пікселів.');
define('TEXT_FIELDTYPE_BARCODE_TYPE', 'Тип штрих-коду');
define('TEXT_FIELDTYPE_BARCODE_TYPE_TIP', 'За замовчуванням C128');
define('TEXT_DISPLAY_FIELD_VALUE', 'Відображати значення поля');
define('TEXT_FIELDTYPE_BARCODE_DISPLAY_TIP', 'За замовчуванням при експорті відображається тільки штрих-код');
define('TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING', 'Спосіб генерації значення');
define(
    'TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING_TIP',
    'Використовуйте [ID поля] для встановлення значення поля в шаблоні.<br> Приклад: "[36]ваш текст[54]" де 36 та 54 - ідентифікатори полів вводу.<br>[auto:10] - значення буде сгенеровано автоматично. 10 - довжина значення'
);
define(
    'TEXT_FIELDTYPE_BARCODE_METHOD_GENERATING_TIP_ICON',
    'Введіть метод генераціи або залиште поле порожнім. Значення буде автоматично сгенеровно при натисканні на кнопку "Зберегти"'
);
define('TEXT_INPUT_FIELD_MASK_DEFINITIONS', 'Визначення маски');
define(
    'TEXT_INPUT_FIELD_MASK_DEFINITIONS_TIP',
    'Тепер ви можете задати свої власні визначення маски. Введіть кожне визначення в новому рядку, наприклад:<br>~=[+-]<br>h=[A-Fa-f0-9]'
);
define(
    'TEXT_INPUT_FIELD_MASK_DEFINITIONS_TIP_ICON',
    '
[abc]	- Будь-які символи між дужками<br>
[^abc] - Будь-який символ не в дужках<br>
[0-9] - Будь-яка цифра в дужках<br>
(x|y)- Будь-який з цих варіантів
'
);
define('TEXT_FIELDTYPE_QRCODE_TITLE', 'QR-код');
define('TEXT_FIELDTYPE_QRCODE_TOOLTIP', 'Поле для генераціи QR кода');
define('TEXT_QRCODE_PATTERN', 'Шаблон для кодування');
define('TEXT_HIDE_FIELD_ON_INFO_PAGE', 'Приховати поле на сторінці запису');
define('TEXT_CODE_ERROR_CORRECTION', 'Код корекції помилок (ECC):');
define('TEXT_CODE_ERROR_CORRECTION_L', 'L - мінімум');
define('TEXT_CODE_ERROR_CORRECTION_M', 'M - достатньо');
define('TEXT_CODE_ERROR_CORRECTION_Q', 'Q - багато');
define('TEXT_CODE_ERROR_CORRECTION_H', 'H - максимум');
define('TEXT_PIXEL_SIZE', 'Розмір пікселя');
define('TEXT_MENU_USERS_REGISTRATION', 'Реєстрація користувачів');
define('TEXT_PUBLIC_REGISTRATION', 'Публічна реєстрація');
define('TEXT_USE_PUBLIC_REGISTRATION', 'Ввімкнить публічну реєстрацію');
define(
    'TEXT_PUBLIC_REGISTRATION_USER_GROUP',
    'Оберіть групу доступу, яка буде призначатися користувачеві при публічній реєстрації.'
);
define('TEXT_REGISTRATION_BUTTON_TITLE', 'Кнопка реєстрації');
define('TEXT_BUTTON_REGISTRATION', 'Реєстрація');
define('TEXT_REGISTRATION_NEW_USER', 'Реєстрація нового користувача');
define('TEXT_BUTTON_BACK', 'Назад');
define('TEXT_NOTE', 'Примітка');
define('TEXT_ADMINISTRATOR_NOTE', 'Примітка адміністратора');
define('TEXT_DISABLE_CHANGE_PWD', 'Заборонити зміну пароля');
define('TEXT_SELECT_USERS_GROUPS', 'Оберіть групи користувачів');
define('TEXT_SEND', 'Відправити');
define('TEXT_COLUMN', 'Стовпчик');
define('TEXT_COLUMN_IMPORT_INFO', 'Вкажіть стовпчик для імпорту');
define('TEXT_SORT_LIKE_FILE', 'Сортування як у файлі');
define('TEXT_DISABLE_ATTACHMENTS', 'Відключити вкладення');
define('TEXT_SHOW', 'Показати');
define('TEXT_APP_LANGUAGE_TIP', 'Користувачі можуть змінити мову в Особистому Кабінеті.');
define('TEXT_DISABLE_USERS_DEPENDENCY', 'Відключити залежність');
define(
    'TEXT_DISABLE_USERS_DEPENDENCY_INFO',
    'За замовчуванням, список користувачів залежить від призначених користувачів в батьківському записі.'
);
define('TEXT_CALCULATE_AVERAGE_VALUE', 'Обчислити середнє значення');
define('TEXT_REDIRECTS', 'Переходи');
define('TEXT_REDIRECT_AFTER_ADDING', 'Перехід після додавання запису');
define('TEXT_REDIRECT_AFTER_CLICK_HEADING', 'Перехід після натискання на заголовок запису');
define('TEXT_REDIRECT_TO_SUBENTITY', 'Переходити до списку дочірніх записів (якщо існують)');
define('TEXT_REDIRECT_TO_LISTING', 'Залишатися на поточній сторінці');
define('TEXT_REDIRECT_TO_INFO', 'Переходити на сторінку запису');
define('TEXT_DISPLAYS_ASSIGNED_ITEMS_ONLY', 'Відображає тільки призначені записи');
define('TEXT_DISPLAYS_ASSIGNED_ITEMS_ONLY_INFO', 'Додає фільтр по поточному користувачеві');
define('TEXT_ROWS_PER_PAGE_IF_NOT_SET', 'Якщо не встановлено, відображаються всі записи.');
define('TEXT_CONFIGURE_FILTERS', 'Налаштувати фильтри');

//new defines for version 2.0
define('TEXT_IFRAME', 'iFrame');
define('TEXT_BUTTON_SEND', 'Відправити');
define('TEXT_FIELDTYPE_INPUT_EMAIL_TITLE', 'Поле для E-mail');
define('TEXT_FIELDTYPE_INPUT_EMAIL_TOOLTIP', 'Поле для введення E-mail адреси');
define('TEXT_ERROR_REQUIRED_EMAIL', 'Будь ласка, введіть адресу електронної пошти.');
define('TEXT_DISPLAY_AS_LINK', 'Відображати як посилання');
define('TEXT_FIELDTYPE_SECTION', 'Секція');
define(
    'TEXT_FIELDTYPE_SECTION_TOOLTIP',
    'Спеціальний тип поля призначений для групування елементів форми. Назва поля є заголовком секції.'
);
define('TEXT_NAV_FORMS_FIELDS_DISPLAY_RULES', 'Правила відображення полів');
define('TEXT_FORMS_FIELDS_DISPLAY_RULES', 'Правила відображення полів у формі');
define('TEXT_BUTTON_ADD_NEW_RULE', 'Додати нове правило');
define('TEXT_RULE_FOR_FIELD', 'Правило для поля');
define(
    'TEXT_FORMS_FIELDS_DISPLAY_RULES_INFO',
    'Для полів типу список у вас є можливість налаштувати відображення полів, в залежності від обраного значення списку.<a href="https://keruy.com.ua/index.php?p=64" target="_blank">Детальніше</a>'
);
define('TEXT_DISPLAY_FIELDS', 'Відобразити поля');
define('TEXT_HIDE_FIELDS', 'Приховати поля');
define('TEXT_SELECT_FIELD_VALUES', 'Виберіть значення поля');
define('TEXT_EDIT_FIELDS', 'Редагувати поля');
define('TEXT_FIELDTYPE_RANDOM_VALUE', 'Випадкове значення');
define('TEXT_FIELDTYPE_RANDOM_VALUE_TOOLTIP', 'Генерує випадкове значення із зазначених символів');
define('TEXT_VALUE_LENGTH', 'Довжина значення');
define('TEXT_CHARACTERS', 'Символи');
define(
    'TEXT_CHARACTERS_TIP',
    'За замовчуванням використовуються тільки цифри. Введіть свої символи, з яких буде генеруватися значення.'
);
define('TEXT_SPLIT_VALUE', 'Розділити значення');
define('TEXT_SPLIT_VALUE_INFO', 'Значення можна розділити на кілька частин. Введіть кількість частин.');
define('TEXT_SPLIT_VALUE_CHAR', 'Роздільник');
define('TEXT_SPLIT_VALUE_CHAR_INFO', 'За замовчуванням "-"');
define('TEXT_START_ROW', 'Початок рядка');
define('TEXT_START_ROW_TIP', 'В значення можна додати початок та кінець');
define('TEXT_END_ROW', 'Кінець рядка');
define('TEXT_INTERNAL_FIELD_NOTE', 'Зарезервоване поле в системі.');
define('TEXT_RECORD_NOT_FOUND', 'Запис не знайдено');

define('TEXT_DISPLAY_AS_COUNTER', 'Відображати як лічильник');
define('TEXT_DISPLAY_ICON', 'Відображати іконку');
define('TEXT_COLOR', 'Колір');
define('TEXT_EXTRA_FIELDS', 'Додаткові поля');
define('TEXT_DASHBOARD_REPORT_EXTRA_FIELDS_INFO', 'Буде раховуватися сума для обраних полів');
define('TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_TITLE', 'Багаторівневий список, що випадає');
define('TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_TOOLTIP', 'Кожен рівень зі списку значень відображається як окремий список');
define('TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_LEVEL_SETTINGS', 'Налаштування рівнів');
define(
    'TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_LEVEL_SETTINGS_INFO',
    'Введіть назву для кожного рівня вкладеності з нового рядка. Перший рядок - перший рівень і т.д.'
);
define(
    'TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_LEVEL_SETTINGS_TIP',
    'Наприклад:<br>&nbsp;&nbsp;Країна<br>&nbsp;&nbsp;Область<br>Або:<br>&nbsp;&nbsp;Країна, Виберіть країну<br>&nbsp;&nbsp;Область, Виберіть область'
);
define('TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_VALUE_DISPLAY', 'Власний стовпчик для кожного значення');
define(
    'TEXT_FIELDTYPE_DROPDOWN_MULTILEVEL_VALUE_DISPLAY_TIP',
    'За замовчуванням, вибрані значення будуть відображатися в одному стовпці'
);
define('TEXT_COLUMNS_IMPORT', 'Імпортувати стовпчики');
define(
    'TEXT_COLUMNS_IMPORT_INFO',
    'Для імпорту простого списку вкажіть 1 - буде імпортовано перший стовпчик. Для імпорту залежних значень, таких як (країна/область) вкажіть 2. У такому випадку, будуть зчитуватися перші два стовпчика у файлі. Значення в рядках не повинні бути порожніми.'
);
define(
    'TEXT_DASHBOARD_DEFAULT_MSG',
    '<h3 class="page-title">Ласкаво просимо!</h3><p>Це - головна сторінка системи. Зараз вона порожня, але ви можете відобразити тут різні звіти та лічильники.</p><p>Для того, щоб відобразити ваш перший звіт, виберіть зліва вкладку "Звіти" та створіть необхідний вам звіт, встановивши при створенні галочку "Відображати на головній".</p>'
);
define(
    'TEXT_DASHBOARD_DEFAULT_ADMIN_MSG',
    '<h3 class="page-title">Вас вітає система "KeruyCRM" - ваш новий помічник в управлінні справами!</h3> 
<p>Це - головна сторінка вашої системи. Зараз вона порожня, але ви можете відобразити тут різні звіти та лічильники.</p> 
<p>Для того, щоб відобразити ваш перший звіт, виберіть зліва вкладку "Звіти" та створіть необхідний вам звіт, встановивши при створенні галочку "Відображати на головній".</p>
<p>Якщо ви ще не встигли ознайомитися з системою більш детально, рекомендуємо відвідати розділ <a href="https://keruy.com.ua/index.php" target="_blank">Документація</a>. Там Ви знайдете різні поради по налаштуванню системи.</p>
<p>Залишилися питання? Ви завжди можете звернутися до нас по допомогу! </p>
<p><a href="https://keruy.com.ua/" target="_blank">keruy.com.ua</a></p>'
);
define('TEXT_FILES_UPLOAD_LIMIT', 'Обмеження завантаження');
define(
    'TEXT_FILES_UPLOAD_LIMIT_TIP',
    'Максимальна кількість файлів, які можуть бути завантажені. Залиште поле порожнім, щоб прибрати будь-які обмеження.'
);
define('TEXT_FILES_UPLOAD_SIZE_LIMIT', 'Обмеження розміру файлу');
define('TEXT_FILES_UPLOAD_SIZE_LIMIT_TIP', 'Максимальний розмір файлу, в МБ');
define('TEXT_FILE_TOO_LARGE', 'Занадто великий файл');
define(
    'TEXT_MAXIMUM_UPLOAD_LIMIT',
    'Перевищено ліміт на кількість файлів, що завантажуються. Максимальна кількість файлів: '
);
define('TEXT_COMPLETED', 'Завершено');
define('TEXT_CANCELLED', 'Скасовано');
define('TEXT_FROM_SESSION_ERROR', 'Помилка відправки форми. Будь ласка, спробуйте ще раз.');
define('TEXT_ERROR_MESSAGE', 'Повідомлення про помилку');
define(
    'TEXT_UNIQUE_FIELD_VALUE_ERROR_MSG_TIP',
    'Повідомлення про помилку відображається в разі, якщо поле не унікальне.'
);
define('TEXT_ICON', 'Іконка');
define('TEXT_HIDDEN_FIELDS', 'Приховані поля');
define('TEXT_HIDDEN_FIELDS_TIP', 'Приховані поля не відображаються у формі');
define('TEXT_NAV_ITEM_PAGE_CONFIG', 'Налаштування сторінки запису');
define('TEXT_COLUMNS_SIZE', 'Розміри стовпчиків');
define(
    'TEXT_ITEM_PAGE_COLUMNS_SIZE',
    'Сторінка запису розділена на два стовпчики, у яких Ви зможете налаштувати розмір.'
);
define('TEXT_ITEM_DETAILS_POSITION', 'Деталі запису');
define(
    'TEXT_ITEM_DETAILS_POSITION_INFO',
    'За замовчуванням, поля запису розділені на два стовпчика. Ви можете об’єднати їх в один.'
);
define('TEXT_ONE_COLUMN', 'Один стовпчик');
define('TEXT_TWO_COLUMNS', 'Два стовпчика');
define('TEXT_ITEM_HIDDEN_PAGE_INFO', 'За замовчуванням відображаються всі поля.');
define('TEXT_LEFT_COLUMN', 'Лівий стовпчик');
define('TEXT_RIGHT_COLUMN', 'Правий стовпчик');
define(
    'TEXT_ITEM_DETAILS_SUM_ENTITIES',
    'Щоб відобразити записи з вкладених сутностей, просто вкажіть позицію на сторінці. Також, ви можете налаштувати власні фільтри списку для кожної сутності.'
);
define('TEXT_ITEM_PAGE_PARENT_ITEM', 'Сторінка запису батьківської сутності');
define('TEXT_HIDE_IN_TOP_MENU', 'Приховати в верхньому меню');
define('TEXT_TOP_LEVEL', 'Верхній рівень');
define('TEXT_CALCULATE_AVERAGE_VALUE_INFO', 'Середнє значення відображається в списку записів');
define('TEXT_REPORTS_CREATE_ACCESS', 'Стандартні звіти');
define('TEXT_UPDATE_SELECTED_ACCESS', 'Оновити вибрані');
define('TEXT_DELETE_SELECTED_ACCESS', 'Видалити вибрані');
define('TEXT_EXPORT_ACCESS', 'Експорт запису');
define('TEXT_EXPORT_SELECTED_ACCESS', 'Експортувати вибрані');
define('TEXT_VIEW_ALL_ACTION_WIDTH_ASSIGNED_ACCESS', 'Перегляд всіх. Дії з призначеними');
define(
    'TEXT_ENTITY_ACCESS_INFO_EXTRA',
    '<br>Опція "Перегляд всіх. Дії з призначеними" дозволяє виконувати дії тільки з призначеними записами.<br>Опція "Оновити вибрані" буде доступна при встановленому додатку. <a href="https://keruy.com.ua/index.php?p=89" target="_blank">Детальніше.</a>'
);
define('TEXT_CONDITION_NOT_EMPTY_VALUE', 'Непорожнє значення');
define('TEXT_FIELDTYPE_AUTOSTATUS_TITLE', 'Автоматичний статус');
define(
    'TEXT_FIELDTYPE_AUTOSTATUS_TOOLTIP',
    'Після створення поля, створіть необхідні статуси, натиснувши на ім’я поля в списку полів. Статус буде встановлюватися автоматично при створенні або редагуванні запису, в залежності від налаштованих фільтрів. <a href="https://keruy.com.ua/index.php?p=16" target="_blank">Детальніше</a>.'
);
define(
    'TEXT_FIELDTYPE_AUTOSTATUS_OPTIONS_TIP',
    'Для кожної опції встановіть фільтри, натиснувши на назву опції. Значення буде встановлюватися, якщо стан запису збігається з встановленими фільтрами. Перевірка станів виконується в заданому сортуванні та, якщо стан знайдено, перевірка припиняється.'
);
define('TEXT_FILTERS', 'Фільтри');
define('TEXT_AUTOUPDATE', 'Автоматичне оновлення');
define(
    'TEXT_DISPLAY_IN_HEADER_AUTOUPDATE_TOOLTIP',
    'Дані звіту оновлюються кожну хвилину. Дана опція збільшує навантаження на сервер. Використовуйте її там, де це дійсно необхідно.'
);
define('TEXT_PREFIX', 'Префікс');
define('TEXT_SUFFIX', 'Суфікс');
define('TEXT_PREVIEW_IMAGE_SIZE_IN_LISTING', 'Ширина зображення в списку');
define('TEXT_MARK_AS_READ', 'Відзначити, як прочитані');
define('TEXT_DISPLAY_NUMBER_OF_ITEMS_OPEN_REPORT', 'Відображено %s. Перейти до звіту<i class="fa fa-angle-right"></i>');
define('TEXT_DISABLE_CHECK_FOR_UPDATES', 'Не перевіряти оновлення');
define('TEXT_EXAMPLE', 'Приклад');
define('TEXT_DISPLAY_FILE_DATE_ADDED', 'Показувати дату завантаження файлу');
define('TEXT_NEW_FEATURES_FOR_YOUR_BUSINESS', 'Нові можливості для вашого бізнесу');
define('TEXT_ONE_OFF_CHARGE', 'Разовий платіж');
define('TEXT_UPDATES_FOR_FREE', 'Наступні оновлення безкоштовні');
define('TEXT_FREE_SUPPORT', 'Безкоштовна підтримка');
define('TEXT_BUY_EXTENSION', 'Купити Доповнення');
define('TEXT_EXTENSION_FEATURES', 'Можливості Доповнення');
define(
    'TEXT_EXTENSION_FEATURES_INFO',
    'В Доповнення входить набір звітів та інструментів для більш ефективного планування та управління.'
);
define(
    'TEXT_EXTENSION_FEATURES_LIST',
    '<h4>Основні можливості</h4>,Публічні форми,Автоматизація бізнес процесів,Rest API,Формули та Функції,Календар,Шаблони,<h4>Інструменти</h4>,Завдання що повторюються,Валюти,Множинне оновлення,Онлайн чат,Інформаційні сторінки,Облік часу та тимчасові звіти,<h4>Звіти</h4>,Воронкоподібна діаграма,Історія змін,Зведені звіти,Загальні звіти,Звіти по шкалі часу,Діаграма Ганта,Графіки та діаграми,Канбан-доска,Mind map,План-схема,Мапа,<h4>Модулі</h4>,Платіжні модулі,СМС повідомлення,Модулі збереження файлів,Розумнє введення'
);
define('TEXT_HIDE_ADMIN', 'Приховати адміністраторів');
define('TEXT_ACTION_IMPORT_DATA', 'Імпортувати дані');
define('TEXT_ACTION_UPDATE_DATA', 'Оновити дані');
define('TEXT_ACTION_UPDATE_AND_IMPORT_DATA', 'Оновити і імпортувати нові');
define('TEXT_UPDATE_BY_FIELD', 'Оновити по полю');
define('TEXT_USE_COLUMN', 'Використовувати стовпчик');
define('TEXT_UPDATE_SETTINGS', 'Налаштування оновлення');
define('TEXT_COUNT_ITEMS_ADDED', 'Додано записів');
define('TEXT_COUNT_ITEMS_UPDATED', 'Оновлене записів');
define('TEXT_SEND_NOTIFICATION', 'Відправляти повідомлення');
define(
    'TEXT_REGISTRATION_SEND_NOTIFICATION_INFO',
    'Вибрані користувачі отримують повідомлення по електронній пошті при створенні нового облікового запису користувача'
);
define('TEXT_SERVER_LOAD', 'Навантаження на сервер');
define('TEXT_REPORTS_IN_HEADER_MENU', 'Звіти в верхньому меню');
define(
    'TEXT_SERVER_LOAD_INFO',
    'Тут ви можете налаштувати функції кешування додатка для зниження навантаження на сервер.'
);
define('TEXT_USE_CACHE', 'Використовувати кеш');
define('TEXT_CACHE_LIVETIME', 'Час життя кешу');
define('TEXT_CACHE_LIVETIME_INFO', 'В секундах');
define(
    'TEXT_REPORTS_IN_HEADER_MENU_CACHE_INFO',
    'За замовчуванням, звіти в верхньому меню оновлюються кожного разу при оновленні сторінки, що створює навантаження на сервер при роботі з великою кількістю звітів і користувачів.'
);
define('TEXT_CACHE_FOLDER', 'Папка кешу');

//new defines for version 2.1
define('TEXT_PIVOT_ACCESS_TABLE', 'Зведена таблиця доступу');
define('TEXT_COPY_ACCESS', 'Копіювати доступ');
define('TEXT_SELECT_ENTITY_TO_COPY_ACCESS', 'Виберіть сутності для копіювання доступу.');
define('TEXT_COPY_ACCESS_INFO', 'Існуючі права доступу будуть перезаписані.');
define('TEXT_USERS_ALERTS', 'Сповіщення користувачів');
define(
    'TEXT_USERS_ALERTS_INFO',
    'На цій сторінці у вас є можливість створювати оповіщення для обраних користувачів або груп користувачів. <a href="https://keruy.com.ua/index.php?p=15" target="_blank">Детальніше</a>.'
);
define('TEXT_LOCATION', 'Розташування');
define('TEXT_LOCATION_ON_DASHBOARD', 'На головній сторінці');
define('TEXT_LOCATION_ON_ALL_PAGES', 'На всіх сторінках');
define('TEXT_TITLE', 'Назва');
define('TEXT_ALERT_INFO', 'Інформація');
define('TEXT_ALERT_SUCCESS', 'Порада');
define('TEXT_ALERT_WARNING', 'Попередження');
define('TEXT_ALERT_DANGER', 'Термінове повідомлення');
define('TEXT_DISPLAY_DATE', 'Дата відображення');
define('TEXT_DB_RESTORE_PROCESS', 'Відновлення бази даних');
define(
    'TEXT_DB_RESTORE_PROCESS_INFO',
    'Цей процес може зайняти кілька хвилин, не закривайте вікно браузера.<br><br>У разі виникнення помилки, відновіть базу даних за допомогою phpMyAdmin.'
);
define(
    'TEXT_IF_NOT_ASSIGNED_DISPLAY_EVERYONE',
    'Якщо група і користувачі не вказані, повідомлення буде відображатися всім.'
);
define('TEXT_FLOWCHART', 'Блок-схема');
define(
    'TEXT_ENTITIES_FLOWCHART_INFO',
    'На блок-схемі відображені сутності та зв’язки. З метою економії місця, зв’язок з зарезервованої сутністю "Користувачі" (типи полів "Користувачі" і "Група користувачів") не відображається стрілкою.'
);
define(
    'TEXT_PUBLIC_REGISTRATION_USER_GROUP_MULTIPLE',
    'Якщо вказано кілька груп, користувачі зможуть самостійно вибрати групу при реєстрації.'
);
define('TEXT_HEADING_WIDTH_BASED_CONTENT', 'Обчислювати ширину поля на основі вмісту');
define('TEXT_HEADING_WIDTH_BASED_CONTENT_INFO', 'За замовчуванням, поле заголовка має ширину 100%');

//new defines for version 2.2
define('TEXT_VALUE', 'Значення');
define(
    'TEXT_CHOICES_VALUE_INFO',
    'Значення може використовуватися в обчисленнях формули. <a href="https://keruy.com.ua/index.php?p=25#get_value" target="_blank">Детальніше</a>'
);
define('TEXT_FIELDS_TYPES_GROUP_NUMERIC', 'Числове поле');
define('TEXT_FIELDTYPE_JS_FORMULA_TITLE', 'JS Формула');
define(
    'TEXT_FIELDTYPE_JS_FORMULA_TOOLTIP',
    'Значення цього поля буде розраховуватися за формулою, зазначеною нижче. Значення розраховується безпосередньо у формі додавання запису. Ви можете використовувати синтаксис JavaScript у формулі.'
);
define(
    'TEXT_JS_FORMULA_TIP',
    'Використовуйте [ID поля] для встановлення значення поля у формулі. Приклад: ([36]+[54])/2 де 36 та 54 - ідентифікатори числових полів. <a href="https://keruy.com.ua/index.php?p=72" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_JS_FORMULA_ERROR', 'Помилка розрахунку формули');
define('TEXT_DISPLAY_CHOICES_VALUES', 'Відображати значення опцій');
define('TEXT_DISPLAY_CHOICES_VALUES_TIP', 'Встановлені значення опцій будуть відображатися в списку, наприклад: (+10)');
define('TEXT_NAV_ACCESS_RULES', 'Правила доступу');
define('TEXT_ACCESS_ALLOCATION_RULES', 'Правила розподілу доступу');
define(
    'TEXT_ACCESS_ALLOCATION_RULES_INFO',
    'На цій сторінці у вас є можливість налаштувати доступ користувачів в залежності від значень обраного поля. <a href="https://keruy.com.ua/index.php?p=28" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_ADD_FIELD', 'Додати поле');
define('TEXT_ACCESS_RULES_FOR_FIELD', 'Правила розподілу доступу для поля "%s"');
define('TEXT_ACCESS_RULES_FOR_FIELD_INFO', 'Налаштуйте правила доступу для обраних значень поля.');
define('TEXT_ACCESS_RULES_SELECT_ACCESS', 'Якщо значення не обрані, користувачі матимуть доступ лише на перегляд.');
define('TEXT_ACCESS_RULES_FIELDS_VIEW_ONLY_ACCESS', 'Виберіть поля, доступ до яких буде тільки на перегляд.');
define('TEXT_USE_DEFAULT_IF_NOT_SELECTED', 'Якщо значення не обрано, будуть використовуватися стандартні параметри.');
define('TEXT_FIELDTYPE_TODO_LIST_TITLE', 'Список справ');
define(
    'TEXT_FIELDTYPE_TODO_LIST_TOOLTIP',
    'Спеціальний тип поля дозволяє вам створювати список справ (підзадач) в рамках одного завдання. Кожна підзадача вводиться з нового рядка в текстовому полі. Символ * на початку рядка означає, що підзадача завершена. <a href="https://keruy.com.ua/index.php?p=26" target="_blank"><u>Детальніше</u></a>.'
);
define('TEXT_AUTO_ADD_COMMENT', 'Автоматично додавати коментар');
define('TEXT_OPEN_COMMENT_FORM', 'Відкривати форму коментаря');
define('TEXT_FIELDTYPE_TODO_LIST_USE_COMMENTS_INFO', 'Використовувати коментарі при позначці в списку');
define('TEXT_FOR_SUCCESSFUL_CHECK', 'Текст для успішної позначки');
define('TEXT_FOR_UNCHECK', 'Текст для зняття позначки');
define('TEXT_SELECT_REPORTS', 'Виберіть звіти');
define('TEXT_SORT_ITEMS_IN_LIST', 'Для сортування елементів в списку, просто перетягніть їх.');
define('TEXT_REPORTS_GROUPS', 'Групи звітів');
define(
    'TEXT_REPORTS_GROUPS_INFO',
    'Групуйте звіти за визначеною сутністю або даними і виводіть їх на окремій сторінці. <a href="https://keruy.com.ua/index.php?p=68" target="_blank"><u>Детальніше</u></a>'
);
define('TEXT_SECTIONS', 'Секції');
define('TEXT_CONFIGURE_DASHBOARD_SECTION_INFO', 'Кожна секція складається з двох колонок для звітів.');
define('TEXT_ADD_SECTION', 'Додати секцію');
define('TEXT_REPORT_ALREADY_ASSIGNED', 'Цей звіт вже використовується.');
define('TEXT_RELATED_ENTITIES_BY_FIELD_ENTITY', 'Пов’язані суті по полю "Сутність"');
define(
    'TEXT_RELATED_ENTITIES_BY_FIELD_ENTITY_INFO',
    'Щоб відобразити записи з пов’язаних сутностей, просто вкажіть позицію на сторінці. Також, ви можете налаштувати власні фільтри списку для кожної сутності.'
);
define('TEXT_FIELDTYPE_PARENT_VALUE_TITLE', 'Значення з батьківської сутності');
define(
    'TEXT_FIELDTYPE_PARENT_VALUE_TOOLTIP',
    'Даний тип поля призначений для відображення значення з батьківської сутності. Зверніть увагу, що даний тип поля не можна застосувати у формулах. Щоб використовувати у формулі значення з батьківської сутності, використовуйте функцію SELECT.'
);
define('TEXT_LDAP_FIRSTNAME', 'Атрибут "Ім’я"');
define('TEXT_LDAP_FIRSTNAME_NOTE', 'Наприклад: givenname');
define('TEXT_LDAP_LASTNAME', 'Атрибут "Прізвище"');
define('TEXT_LDAP_LASTNAME_NOTE', 'Наприклад: sn');
define('TEXT_USE_LDAP_LOGIN_ONLY', 'Використовувати тільки LDAP вхід');
define('TEXT_LDAP_GROUP_FILTER', 'Фільтр LDAP');
define(
    'TEXT_LDAP_GROUP_FILTER_INFO',
    'При використанні LDAP, новий користувач буде автоматично поміщений у цю групу, якщо його обліковий запис задовольняє цьому фільтру.'
);

//new defines for version 2.3
define('TEXT_ADD_COMMENT_CREATE_RELATED_ITEM', 'Додавати коментар при створенні зв’язку');
define('TEXT_ADD_COMMENT_DELETE_RELATED_ITEM', 'Додавати коментар при видаленні зв’язку');
define('TEXT_ADD_COMMENT_WITHOUT_NOTIFICATION', 'Додати коментар без повідомлення');
define('TEXT_ADD_COMMENT_WITH_NOTIFICATION', 'Додати коментар з повідомленням');
define('TEXT_FIELDTYPE_MYSQL_QUERY_TITLE', 'MySQL запит');
define(
    'TEXT_FIELDTYPE_MYSQL_QUERY_TOOLTIP',
    'Спеціальний тип поля для створення користувацького MySQL запиту до обраної сутності. <a href="https://keruy.com.ua/index.php?p=24" target="_blank"><u>Детальніше</u></a>.'
);
define('TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_ENTITY', 'Вибрати з сутності');
define('TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_ENTITY_TOOLTIP', 'Виберіть сутність до якої буде будуватися MySQL запит');
define('TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_QUERY', 'Вибрати поле');
define(
    'TEXT_FIELDTYPE_MYSQL_QUERY_SELECT_QUERY_TIP',
    'Вкажіть поле, яке буде вибрано в запиті.<br>Наприклад: [12]. Можна використовувати функції MySQL.'
);
define('TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY', 'Умова');
define(
    'TEXT_FIELDTYPE_MYSQL_QUERY_WHERE_QUERY_TIP',
    'Використовуючи ID полів, задайте умови того, що повинно бути вибрано в запиті. Наприклад: [50]=[12]. Можна використовувати функції MySQL.'
);
define('TEXT_FIELDTYPE_MYSQL_QUERY_DYNAMIC_QUERY', 'Виконувати динамічно');
define(
    'TEXT_FIELDTYPE_MYSQL_QUERY_DYNAMIC_QUERY_INFO',
    'За замовчуванням запит виконується при додаванні/редагуванні запису. Встановивши цю опцію запит буде виконуватися безпосередньо при перегляді запису.'
);
define('TEXT_HIDE_ADD_BUTTON_RULES', 'Приховати кнопку "Додати"');
define(
    'TEXT_HIDE_ADD_BUTTON_RULES_INFO',
    'Встановіть фільтри для сутності "%s" при яких кнопка "Додати" буде прихована для сутності "%s".'
);
define('TEXT_FIELDTYPE_IMAGE_MAP_TITLE', 'План-схема');
define(
    'TEXT_FIELDTYPE_IMAGE_MAP_TOOLTIP',
    'Це спеціальний тип поля для відображення користувацьких мап, планів поверхів, створення користувацьких міток маркерів на мапі і багато іншого. Після створення поля натисніть на назву і додайте варіанти мап і завантажте своє зображення для кожного варіанта. <a href="https://keruy.com.ua/index.php?p=18" target="_blank"><u>Детальніше</u></a>'
);
define('TEXT_MAP_SETTINGS', 'Налаштування мапи');
define('TEXT_IMAGE', 'Зображення');
define(
    'TEXT_IMAGE_MAP_FILENAME_INFO',
    'Завантажте зображення яке буде використовуватися для побудови мапи.<br><b>Зверніть увагу:</b> зміна зображення мапи може привести до того, що позиції елементів мапи можуть не вписатися в нове зображення мапи.'
);
define('TEXT_IMAGE_MAP_FILENAME_DESCRIPTION', 'Мінімальний розмір зображення мапи - 512x512px.');
define(
    'TEXT_FIELDTYPE_IMAGE_MAP_BACKGROUND_COLOR_INFO',
    'До маркерів на мапі буде застосовуватися фон зі значень списку. Також до кожного значення списку можна завантажити свою іконку.'
);
define(
    'TEXT_FIELDTYPE_IMAGE_MAP_OPTIONS_TIP',
    'Для опцій верхнього рівня завантажте зображення, яке буде використовуватися на мапі.'
);
define('TEXT_ICONS', 'Іконки');
define(
    'TEXT_FIELDTYPE_IMAGE_MAP_ICONS_TIP',
    'Завантажте іконки розміром 24x24. <a href="https://www.iconfinder.com" target="_blank">Пошук іконок </a>'
);
define('TEXT_EXT_PB_USER_AGREEMENT_TEXT', 'Посилання на сторінку "Угода з користування"');
define(
    'TEXT_EXT_PB_USER_AGREEMENT_TEXT_INFO',
    'Додайте текст з посиланням на сторінку "Угода з користування" на вашому сайті. В формі з’явиться обов’язкова опція, яку користувач повинен встановити перед відправкою форми. '
);
define('TEXT_FIELDTYPE_MIND_MAP_TITLE', 'Діаграма зв’язків (Mind map)');
define(
    'TEXT_FIELDTYPE_MIND_MAP_TOOLTIP',
    'Спеціальний тип поля для побудови діаграми зв’язків у вигляді дерева схеми, на якій зображені слова, ідеї, завдання пов’язані гілками, що відходять від центрального поняття або ідеї. <a href="https://keruy.com.ua/index.php?p=19" target="_blank"><u>Детальніше</u></a>'
);
define(
    'TEXT_MIND_MAP_START_TIP',
    'Натисніть "Tab", щоб вставити дочірній елемент, "Enter", щоб встави споріднений елемент.'
);
define('TEXT_SAVE', 'Зберегти');
define('TEXT_RESET', 'Скидання');
define('TEXT_RESET_MAP_CONFIRM', 'Скинути поточну мапу і почати нову?');
define('TEXT_LAYOUT', 'Макет');
define('TEXT_SHAPE', 'Форма');
define('TEXT_NUMBER', 'Число');
define('TEXT_SUM', 'Сума');
define('TEXT_AVERAGE', 'Середнє');
define('TEXT_MINIMUM', 'Мінімальне');
define('TEXT_MAXIMUM', 'Максимальне');
define('TEXT_ON_TOP', 'Зверху');
define('TEXT_ON_BOTTOM', 'Знизу');
define('TEXT_ON_RIGHT', 'Справа');
define('TEXT_ON_LEFT', 'Зліва');
define('TEXT_GRAPH', 'Діаграма');
define('TEXT_TREE', 'Дерево');
define('TEXT_MAP', 'Мапа');
define('TEXT_INHERIT', 'Наслідувати');
define('TEXT_AUTOMATIC', 'Автоматично');
define('TEXT_BOX', 'Коробка');
define('TEXT_ELLIPSE', 'Елліпс');
define('TEXT_UNDERLINE', 'Підкреслити');
define('TEXT_INSERT_CHILD', 'Вставити дочірній елемент');
define('TEXT_INSERT_SIBLING', 'Вставити споріднений елемент');
define('TEXT_EDIT', 'Редагувати');
define('TEXT_SET_VALUE', 'Встановити значення');
define('TEXT_CENTER_MAP', 'Центр мапи');
define('TEXT_ERROR_HEADING_FIELD_ONLY_INPUT_SUPPORT', 'Використовуйте поле вводу в якості заголовка.');
define('TEXT_START', 'Початок');
define(
    'TEXT_LISTING_CONFIGURATION_INFO',
    'На цій сторінці ви можете налаштувати варіанти зовнішнього вигляду списків записів. <a href="https://keruy.com.ua/index.php?p=65" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_TABLE', 'Таблиця');
define('TEXT_LIST', 'Список');
define('TEXT_GRID', 'Плитка');
define('TEXT_MOBILE', 'Мобільний пристрій');
define('TEXT_NOT_REQUIRED_FIELD', 'Не обов’язкове поле');
define('TEXT_DISPLAY_FIELD_NAMES', 'Відображати назву полів');
define('TEXT_ALIGN', 'Вирівнювання');
define('TEXT_ALIGN_LEFT', 'По лівому краю');
define('TEXT_ALIGN_CENTER', 'По центру');
define('TEXT_ALIGN_RIGHT', 'По правому краю');
define('TEXT_DISPLAY_AS', 'Показати як');
define('TEXT_INLINE_LIST', 'Рядок');
define(
    'TEXT_SECTION_WIDTH_TIP',
    'Введіть відсотки або пікселі. Наприклад. 100% або 150px. Значення за замовчуванням - авто.'
);
define(
    'TEXT_GRID_WIDTH_INFO',
    'Введіть мінімальну ширину поля в пікселях.<br>Наприклад: 250. Кількість полів в рядку буде розраховуватися автоматично в залежності від ширини екрану.'
);
define('TEXT_IMPORT', 'Імпортувати');
define(
    'TEXT_LDAP_INFO',
    'Для аутентифікації в системі KeruyCRM користувачі зможуть використовувати свої LDAP дані. <a href="https://keruy.com.ua/index.php?p=9" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_FIELDTYPE_DAYS_DIFFERENCE_TITLE', 'Різниця в днях');
define(
    'TEXT_FIELDTYPE_DAYS_DIFFERENCE_TOOLTIP',
    'Спеціальний тип поля, який вираховує різницю в днях між двома датами. <a href="https://keruy.com.ua/index.php?p=88" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_HOLIDAYS', 'Святкові дати');
define('TEXT_START_DATE', 'Дата початку');
define('TEXT_END_DATE', 'Дата закінчення');
define('TEXT_EXCLUDE_LAST_DAY', 'Виключити останній день');
define('TEXT_EXCLUDE_WEEK_DAYS', 'Виключити дні тижня');
define('TEXT_EXCLUDE_HOLIDAYS', 'Виключити свята');
define('TEXT_CURRENT_DATE', 'Поточна дата');
define('TEXT_FIELDTYPE_HOURS_DIFFERENCE_TITLE', 'Різниця в годинах');
define(
    'TEXT_FIELDTYPE_HOURS_DIFFERENCE_TOOLTIP',
    'Спеціальний тип поля, який вираховує різницю в годинах між двома датами. <a href="https://keruy.com.ua/index.php?p=88" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_WORKING_HOURS', 'Робочий час');
define('TEXT_WORKING_HOURS_INFO', 'Для розрахунку різниці буде використовуватися тільки робочий час.');
define(
    'TEXT_HOLIDAYS_INFO',
    'Святкові дати можна виключити при розрахунку різниці днів. <a href="https://keruy.com.ua/index.php?p=88" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_LOGIN_AS', 'Ввійти як <b>%s</b>');
define('TEXT_LOGIN_BACK_AS_ADMIN', 'Повернутися в систему як адміністратор');

//new defines for version 2.4
define('TEXT_FIELDTYPE_BOOLEAN_CHECKBOX_TITLE', 'Логічне поле (Прапорець)');
define('TEXT_DISPLAY_IN_LISTING', 'Відображати в списку');
define('TEXT_COUNT_RELATED_ITEMS', 'Кількість пов’язаних записів');
define('TEXT_LIST_RELATED_ITEMS', 'Список з пов’язаними записами');
define('TEXT_HEADING_PATTER_IN_LISTING', 'Шаблон заголовка в списку');
define('TEXT_HIDE_FIELD_IF_NO_RECORDS', 'Приховати поле при відсутності записів');
define('TEXT_HIDE_BUTTONS', 'Приховати кнопки');
define('TEXT_CHANGE_STRUCTURE', 'Змінити структуру');
define(
    'TEXT_CHANGE_STRUCTURE_INFO',
    'Увага: дана операція призведе до зміни записів для обраної сутності. Рекомендується зробити резервну копію бази даних перед початком дій.'
);
define('TEXT_MOVE_ENTITY', 'Перемістити сутність');
define('TEXT_MOVE_TO_PARENT_ITEM_INFO', 'Всі записи будуть переміщені в обраний елемент.');
define('TEXT_ENTITY_STRUCTURE_CHANGED', 'Структура сутностей змінена.');
define('TEXT_FIELDTYPE_AUTO_INCREMENT_TITLE', 'Автоінкремент');
define(
    'TEXT_FIELDTYPE_AUTO_INCREMENT_TOOLTIP',
    'При додаванні нового запису, значення поля збільшується на один. Поле в формі відображається як поле введення, і значення можна відкоригувати.'
);
define('TEXT_FIELDTYPE_TEXT_PATTERN_STATIC', 'Статичний текст по шаблону');
define(
    'TEXT_FIELDTYPE_TEXT_PATTERN_STATIC_TOOLTIP',
    'Спеціальне поле, яке дозволяє відобразити текст в заданому шаблоні. Значення генерується при додаванні/зміни запису. Є можливість пошуку за значенням.'
);
define('TEXT_TOOLTIP_ON_ITEM_PAGE', 'Підказка на сторінці запису');
define('TEXT_DISPLAY_ON_ITEM_PAGE', 'Відображати на сторінці запису');
define('TEXT_EXTRA', 'Додатково');
define('TEXT_EMAIL_SUBJECT', 'Тема повідомлення');
define('TEXT_ENCRYPT_FILE_NAME', 'Шифрувати ім’я файлу');
define('TEXT_ENCRYPT_FILE_NAME_TIP', 'За замовчуванням ім’я файлу зашифровано з метою безпеки.');
define(
    'TEXT_FIELDTYPE_DAYS_DIFFERENCE_DYNAMIC_INFO',
    'За замовчуванням різниця розраховується при додаванні/редагуванні запису. Використовуйте цей параметр для динамічного розрахунку з поточною датою.'
);
define('TEXT_USERS_LOGIN_LOG', 'Журнал входу користувачів');
define('TEXT_IP', 'IP');
define('TEXT_LOGIN_ATTEMPT', 'Спроба входу');
define('TEXT_SUCCESSFUL_LOGIN', 'Успішний вхід');
define('TEXT_FIELDTYPE_USER_LAST_LOGIN_DATE', 'Дата останього входу');
define('TEXT_DELETE_DATA', 'Видалити дані');
define('TEXT_FIELDTYPE_YEARS_DIFFERENCE_TITLE', 'Різниця в роках');
define(
    'TEXT_FIELDTYPE_YEARS_DIFFERENCE_TOOLTIP',
    'Спеціальний тип поля, який вираховує різницю в роках між двома датами. <a href="https://keruy.com.ua/index.php?p=88" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_CALCULATE_DIFFERENCE_DAYS', 'Обчислювати різницю в днях');
define('TEXT_FIELDTYPE_PHONE', 'Телефон');
define(
    'TEXT_FIELDTYPE_PHONE_TOOLTIP',
    'Спеціальний тип поля для введення телефону. Можна налаштувати для здійснення дзвінків. <a href="https://keruy.com.ua/index.php?p=62" target="_blank"><u>Детальніше.</u></a>'
);
define(
    'TEXT_INPUT_FIELD_PHONE_MASK_TIP',
    'Наприклад: +38(067)999-99-99<br>
<ul>  
  <li>9 - Користувач повинен ввести цифру (0-9)</li>  
</ul>'
);
define('TEXT_FIELDTYPE_DATE_UPDATED_TITLE', 'Дата оновлення');
define('TEXT_MIN_VALUE', 'Мінімальне значення');
define('TEXT_MIN_VALUE_WARNING', 'Введіть значення, яке більше або дорівнює {0}.');
define('TEXT_MAX_VALUE', 'Максимальне значення');
define('TEXT_MAX_VALUE_WARNING', 'Введіть значення, яке менше або дорівнює {0}.');
define('TEXT_MIN_MAX_VALUE_TIP', 'Введіть число або введіть [ID] поля для використання значення поля в правилі.');
define('TEXT_PHONE', 'Телефон');
define('TEXT_ERROR_REQUIRED_DIGITS', 'Будь ласка, введіть ціле число.');
define('TEXT_DASHBOARD_CONFIGURATION', 'Налаштування головної сторінки');
define(
    'TEXT_DASHBOARD_CONFIGURATION_INFO',
    'На головну сторінку можна додати інформаційні блоки або сторінки. В інфо блоці можна виводити інформацію про поточного користувача. <a href="https://keruy.com.ua/index.php?p=62" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_ADD_INFO_BLOCK', 'Додати інформаційний блок');
define('TEXT_ADD_PAGE', 'Додати сторінку');
define('TEXT_ADD_INFO_SECTIONS_INFO', 'Інформаційні блоки можна об’єднати в секції.');
define('TEXT_COLUMNS', 'Стовпчики');
define('TEXT_POSITION', 'Позиція');
define('TEXT_DASHBOARD_BLOCK_SECTION_INFO', '[user_name] - ім’я користувача');
define('TEXT_AUTHORIZED_USER_BY_DEFAULT', 'Авторизований користувач за замовчуванням');
define(
    'TEXT_AUTHORIZED_USER_BY_DEFAULT_INFO',
    'При додаванні нового запису авторизований користувач призначається за замовчанням'
);
define('TEXT_HELP_SYSTEM', 'Довідкова система');
define(
    'TEXT_HELP_SYSTEM_INFO',
    'Створюйте інформаційні сторінки та оголошення для конкретної сутності. <a href="https://keruy.com.ua/index.php?p=27" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_ADD_ANNOUNCEMENT', 'Додати оголошення');
define('TEXT_HIDE_COUNT_OF_RECORDS', 'Приховати кількість записів');
define('TEXT_SUM_BY_FIELD', 'Сума по полю');
define('TEXT_COUNTER_SUM_BY_FIELD_INFO', 'За замовчуванням відображається кількість записів.');
define('TEXT_ITEMS_LISTING', 'Список записів');
define('TEXT_HELP', 'Допомога');
define('TEXT_ALLOW_LOGIN_FOR_USERS', 'Дозволити вхід для користувачів');
define('TEXT_ALLOWED_EXTENSIONS', 'Дозволені розширення');
define('TEXT_ALLOWED_EXTENSIONS_TIP', 'Введіть розширення через кому, наприклад: gif,jpg,sql,zip');
define(
    'TEXT_ENTITIES_PAGE_INFO',
    'KeruyCRM дозволяє створювати власну базу даних шляхом додавання нових сутностей і зв’язків. <a href="https://keruy.com.ua/index.php?p=12" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_SHOW_USERS', 'Показувати користувачів');
define('TEXT_IN_LISTING', 'У списку');
define('TEXT_IN_ITEM_PAGE', 'На сторінці запису');
define('TEXT_FIELDTYPE_GROUPEDUSERS_SHOW_USERS_TIP', 'Разом з назвою групи буде відображатися список користувачів.');
define('TEXT_SHOW_USERS_ACCESS_GROUP', 'Показувати групу доступу');
define('TEXT_CHANGE_COL_WIDTH_IN_LISTING', 'Змінювати ширину стовпчиків в списку');
define('TEXT_ENTITY_MOVE_TO', 'Перемістити до');

//new defines for version 2.5
define('TEXT_VALUE_VIEW_ONLY_INFO', 'Значення поля є доступним лише для перегляду.');
define('TEXT_FIELDTYPE_AUTO_INCREMENT_SEPARATE_NUMBERING', 'Роздільна нумерація для кожної батьківського запису');
define('TEXT_MAPS', 'Мапи');
define('TEXT_FIELDTYPE_GOOGLE_MAP_TITLE', 'Мапа Google');
define(
    'TEXT_FIELDTYPE_GOOGLE_MAP_TOOLTIP',
    'Поле для відображення маркеру на мапі. Координати маркера визначаються автоматично відповідно до введеної адреси. <a href="https://keruy.com.ua/index.php?p=17" target="_blank"><u>Докладніше.</u></a>'
);
define('TEXT_API_KEY', 'Ключ API');
define(
    'TEXT_FIELDTYPE_GOOGLE_MAP_API_KEY_TIP',
    'Ви маєте отримати ключ API за допомогою консолі Google Cloud Platform. <u>GeoLocation API</u> и <u>Map Embed API</u> мають бути увімкнені. <a href="https://developers.google.com/maps/documentation/javascript/get-api-key#quick-guide" target="_blank">Докладніше.</a>'
);
define('TEXT_ADDRESS', 'Адреса');
define(
    'TEXT_ADDRESS_PATTERN_INFO',
    'Використовуйте одне чи декілька полів, в яких зберігається адреса.<br>Наприклад: "[36], [54]" де 36 та 54 - ідентифікатори полів.'
);
define('TEXT_WIDTH_INPUT_TIP', 'Введіть у відсотках або пікселях. Наприклад: 100% или 250px');
define('TEXT_HEIGHT_INPUT_TIP', 'Введіть висоту у пікселях. Наприклад: 250px');
define('TEXT_FIELDTYPE_INPUT_PROTECTED_TITLE', 'Захищене поле');
define(
    'TEXT_FIELDTYPE_INPUT_PROTECTED_TOOLTIP',
    'Спеціальний тип поля для захисту інформації користувача. Це поле дозволяє повністю обмежити та сховати такі дані, як ідентифікаційний номер, відобразивши лише останні 3-4 символи.'
);
define('TEXT_REPLACE_WITH_SYMBOL', 'Замінити на символ');
define('TEXT_DISCLOSE_NUMBER_FIRST_LETTERS', 'Розкрити кількість перших літер');
define('TEXT_DISCLOSE_NUMBER_LAST_LETTERS', 'Розкрити кількість останніх літер');
define('TEXT_FIELDTYPE_INPUT_PROTECTED_USERS_GROUPS_TIP', 'Оберіть групи користувачів, які бачитимуть повне значення.');
define('TEXT_FILTERS_PANELS', 'Панелі Фільтрів');
define('TEXT_DEFAULT_FILTER_PANEL', 'Панель фільтрів за замовчуванням');
define(
    'TEXT_DEFAULT_FILTER_PANEL_INFO',
    'Ви можете вимкнути дану панель або відобразити її для визначених груп користувачів.  <a href="https://keruy.com.ua/index.php?p=67" target="_blank"><u>Докладніше.</u></a>'
);
define('TEXT_QUICK_FILTERS_PANELS', 'Панелі швидких фільтрів');
define(
    'TEXT_QUICK_FILTER_PANEL_INFO',
    'Зазначені панелі використовуються для фільтрування даних відповідно до найчастіше вживаних параметрів. Ви можете створити горизонтальну чи вертикальну панель швидких фільтрів. В налаштуваннях панелі можна обрати поля та значення, які будуть використані для фільтрів. <a href="https://keruy.com.ua/index.php?p=67" target="_blank"><u>Докладніше.</u></a>'
);
define('TEXT_CONFIGURE', 'Налаштувати');
define(
    'TEXT_FILTERS_PANELS_ACCESS_INFO',
    'Оберіть групи користувачів, які матимуть доступ до панелі фільтрів. За замовчуванням панель є доступною для всіх користувачів.'
);
define('TEXT_HORIZONTAL', 'Горизонтальна');
define('TEXT_VERTICAL', 'Вертикальна');
define('TEXT_FIELDS_CONFIGURATION', 'Налаштування полів');
define('TEXT_PANES_FILTERS_FIELDS_CONFIGURATION_INFO', 'Додайте поля, що будуть використовуватися в панелі фільтрів.');
define('TEXT_PANEL', 'Панель');
define('TEXT_RESET_FILTERS', 'Скинути фільтри');
define('TEXT_ENTER_LIST_HEIGHT', 'Введіть висоту списку у пікселях.');
define('TEXT_CLEAR', 'Очистити');
define('TEXT_EXCLUDE_VALUES', 'Виключити значення');
define('TEXT_ACTIVE_FILTERS', 'Активні фільтри');
define('TEXT_ACTIVE_FILTERS_INFO', 'Список оновлюється автоматично після обрання значення фільтра.');
define('TEXT_USER', 'Користувач');
define('TEXT_OK', 'OK');
define('TEXT_BUTTON_REPLY', 'Відповісти');
define('TEXT_BUTTON_FORWARD', 'Переслати');
define(
    'TEXT_WARNING_ITEM_HAS_SUB_ITEM',
    '<b>Увага:</b> сутність "%s" має вкладені сутності.<br>Всі записи із вкладених сутностей будуть видалені.'
);
define('TEXT_CONFIRM_DELETE', 'Підтвердить видалення');
define('TEXT_BUTTON_EMPTY', 'Очистити');
define('TEXT_FIELDTYPE_TAGS_TITLE', 'Тегі');
define(
    'TEXT_FIELDTYPE_TAGS_TOOLTIP',
    'Спеціальний тип поля, який дозволяє обрати вже існуючу або динамічно створити нову опцію у списку, що розкривається.'
);
define('TEXT_NO_RESULTS_FOUND', 'Збігів не знайдено');
define('TEXT_SEARCHING', 'Пошук…');
define('TEXT_LOADING_MORE_RESULTS', 'Завантаження даних…');
define('TEXT_RESULTS_COULD_NOT_BE_LOADED', 'Неможливо завантажити результати.');
define('TEXT_AUTOMATICALLY_CREATE_TAG', 'Автоматично створювати тег');
define(
    'TEXT_AUTOMATICALLY_CREATE_TAG_TIP',
    'Після введення пробілу або коми в рядку пошуку, тег буде додано автоматично.'
);
define('TEXT_FIELDTYPE_ENTITY_AJAX_TITLE', 'Сутність, список, що випадає (ajax)');
define(
    'TEXT_FIELDTYPE_ENTITY_AJAX_TOOLTIP',
    'Спеціальне поле для списків з великою кількістю даних. Дозволяє встановити зв’язок з існуючою сутністю. Відображається у вигляді списку, до якого дані завантажуються за допомогою ajax запиту.'
);
define('TEXT_SEARCH_BY_FIELDS', 'Пошук за полями');
define(
    'TEXT_SEARCH_BY_FIELDS_INFO',
    'Вкажіть поля, за якими буде здійснено пошук записів. За замовчуванням використовується Заголовок.'
);
define('TEXT_COPY_VALUES', 'Копіювати значення');
define(
    'TEXT_COPY_FIELD_VALUES_INFO',
    'Значення з обраного запису можна скопіювати в поточну форму.<br>Вкажіть зв’язки полів в новому рядку в форматі:<br> 
[13]=[14]<br>
13 - id поля обраної сутності<br>
14 - id поля поточної сутності'
);
define('TEXT_DELETE_BY_CREATOR_ONLY', 'Видаляється лише користувачем');
define('TEXT_FIELDTYPE_USER_ROLES_TITLE', 'Ролі користувачів');
define(
    'TEXT_FIELDTYPE_USER_ROLES_TOOLTIP',
    'Цей тип поля дозволяє призначити користувача на запис та встановити йому додаткову роль доступу для вкладених сутностей. Після створення поля, натисніть на назву поля та додайте необхідні ролі користувачів. <a href="https://keruy.com.ua/index.php?p=77" target="_blank"><u>Докладніше.</u></a>'
);
define('TEXT_USER_ROLES', 'Ролі користувачів');
define(
    'TEXT_USER_ROLES_ENTITIES_WARNING',
    'Ролі користувачів можуть бути налаштовані лише для вкладених сутностей. В поточній сутності немає вкладених сутностей.'
);
define(
    'TEXT_USER_ROLES_INFO',
    'Після додавання ролі, натисніть на назву та налаштуйте доступ. Якщо була додана лише одна роль, то вона призначатиметься користувачу автоматично.'
);
define(
    'TEXT_USER_ROLES_ACCESS_INFO',
    'Оберіть необхідні сутності та налаштуйте доступ. Під час призначення ролі користувачу, правила доступу до обраних сутностей будуть замінені основними правилами для груп користувачів.'
);
define('TEXT_ROLE', 'Роль');
define('TEXT_FIELDTYPE_ENTITY_MULTILEVEL_TITLE', 'Багаторівневий список сутностей');
define(
    'TEXT_FIELDTYPE_ENTITY_MULTILEVEL_TOOLTIP',
    'Список значень формується на підставі обраного значення батьківської сутності.'
);
define(
    'TEXT_FIELDTYPE_ENTITY_MULTILEVEL_SELECT_ENTITY_TOOLTIP',
    'Оберіть сутність останнього рівня в ієрархії сутностей.'
);
define('TEXT_HIDE_PLUS_BUTTON', 'Сховати кнопку "+"');
define('TEXT_FIELDS_EXPORT', 'Експорт полів');
define(
    'TEXT_FIELDS_EXPORT_INFO',
    'Цей функціонал призначений для перенесення полів з налаштуваннями в інший додаток. Обрані поля експортується в  файл в форматі xml. Для імпорту нових полів скористайтеся кнопкой <i class="fa fa-upload"></i>'
);
define('TEXT_IMPORT_FIELDS', 'Імпортувати поля');
define('TEXT_CONTINUE', 'Продовжити');
define('TEXT_IMPORTED_FIELDS', 'Імпортовано полів: %s');
define(
    'TEXT_IMPORT_FIELDS_INFO',
    'Для імпорту полів використовуйте xml файл, який був отриманий під час експорту полів.<br><b>Зверніть увагу:</b> імпортовані поля матимуть нові ID.<br>Якщо ID полів застосовуються в формулах, то формули слід скорегувати.'
);
define(
    'TEXT_SELECT_USER_GROUPS_COMMON_INFO',
    'Оберіть групи користувачів, що матимуть доступ. За замовчуванням доступ мають всі користувачі.'
);
define(
    'TEXT_LISTING_CONFIG_ACCESS_INFO',
    'Оберіть групи користувачів, що матимуть доступ до налаштувань списку. За замовчуванням налаштування полів  списку є доступним для всіх користувачів.'
);
define('TEXT_CURRENT_USER', 'Поточний користувач');
define('TEXT_FIELDTYPE_MONTHS_DIFFERENCE_TITLE', 'Різниця в місяцях');
define(
    'TEXT_FIELDTYPE_MONTHS_DIFFERENCE_TOOLTIP',
    'Спеціальний тип поля, який розраховує різницю в місяцях між двома датами.'
);
define('TEXT_CALCULATE_DIFFERENCE_DAYS_INFO', 'Під час розрахунку різниці між датами буде враховано кількість днів.');
define('TEXT_HIDE_DROPDOWN', 'Сховати список');
define('TEXT_FIELDTYPE_USERS_APPROVE_TITLE', 'Затвердити');
define(
    'TEXT_FIELDTYPE_USERS_APPROVE_TOOLTIP',
    'Цей тип поля дозволяє обрати список користувачів для затвердження запису або додавання підпису.'
);
define('TEXT_BUTTON_TITLE', 'Назва кнопки');
define('TEXT_APPROVE', 'Затвердити');
define('TEXT_CONFIRMATION_WINDOW', 'Вікно підтвердження');
define('TEXT_CONFIRMATION_TEXT', 'Текст підтвердження');
define('TEXT_ADD_COMMENT', 'Додати коментар');
define('TEXT_COMMENT_TEXT', 'Текст коментаря');
define('TEXT_APPROVED', 'Затверджено');
define('TEXT_FIELDTYPE_USERS_APPROVE_FILTERS_INFO', 'Встановіть фільтри, за яких кнопка "Затвердити" стає доступною.');
define('TEXT_BUTTON', 'Кнопка');
define('TEXT_ALL_USERS_APPROVED', 'Всі користувачі затвердили');
define(
    'TEXT_ALL_USERS_APPROVED_INFO',
    'Оберіть дію, яка буде виконуватися після підтвердження запису всіма користувачами. Дію можна налаштувати у <a href="https://keruy.com.ua/index.php?p=31" target="_blank">Доповнення->Автоматизація дій</a>.'
);
define('TEXT_PLEASE_PROVIDE_SIGNATURE', 'Будь-ласка, поставте свій підпис.');
define('TEXT_SIGNATURE', 'Підпис');
define('TEXT_WIDTH_IN_ITEM_PAGE', 'Ширина на сторінці запису');
define('TEXT_WIDTH_IN_ITEM_PAGE_INFO', 'Ширина зображення в пікселях під час відображення на сторінці запису.');
define('TEXT_WIDTH_IN_PRINT_PAGE', 'Ширина на сторінці друку');
define('TEXT_WIDTH_IN_PRINT_PAGE_INFO', 'Ширина зображення в пікселях під час відображення на сторінці друку.');
define('TEXT_DELETION', 'Видалення');
define('TEXT_CONFIRM_DELETION', 'Підтверджувати видалення');
define(
    'TEXT_ALLOWS_DELETE_IF_HAS_DELETE_ACCESS',
    'Дозволити видалення, якщо у користувача є доступ до видалення запису'
);
define('TEXT_RULES', 'Правила');

//new defines for version 2.6
define('TEXT_ACCESS_CONFIGURATION', 'Налаштування доступу');
define('TEXT_SEND_NOTIFICATION_TO_ASSIGNED_ONLY', 'Надсилати повідомлення лише призначеним користувачам');
define(
    'TEXT_SEND_COMMENTS_NOTIFICATION_TO_ASSIGNED_INFO',
    'За замовченням, під час додавання коментаря сповіщення отримають користувачі, які мають призначення на запис, а також користувачі, які брали участь в обговоренні.'
);
define('TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_TITLE', 'Карта Google маршрути');
define(
    'TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_TOOLTIP',
    'Поле для відображення декількох маркерів на мапі. Координати маркерів визначаються автоматично за адресами, що введені. За визначеними маркерами можливо прокласти маршрут. <a href="https://keruy.com.ua/index.php?p=17" target="_blank"><u>Докладніше.</u></a>'
);
define('TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_ADDRESS_TIP', 'Введіть декілька адресів з нового рядку.');
define('TEXT_MARKER', 'Маркер');
define('TEXT_LABEL', 'Мітка');
define('TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_LABEL_TIP', 'Ви можете ввести мітки для кожної адреси в новому рядку.');
define('TEXT_LABEL_COLOR', 'Колір мітки');
define(
    'TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_ICONS_TIP',
    'Ви можете встановити іконку для кожної адреси. Введіть http адресу іконки в новому рядку.'
);
define('TEXT_DIRECTIONS', 'Напрямки');
define('TEXT_MODE', 'Режим');
define(
    'TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_MODE_TIP',
    'Для того щоб увімкнути розрахунок напрямків, слід зазначити, який режим транспортування слід використовувати. <a href="https://developers-dot-devsite-v2-prod.appspot.com/maps/documentation/javascript/directions?hl=ru#DirectionsRequests" target="_blank">Докладніше.</a><br><b>Примечание:</b>  Directions API має бути увімкнений в console.cloud.google.com<br><b>Зверніть увагу:</b> призначені користувачами маркери та іконки не працюють під час розрахунку напрямків.'
);
define('TEXT_OPTIMIZE_WAYPOINTS', 'Оптимізувати шляхові точки');
define('TEXT_PROVIDE_ROUTE_ALTERNATIVES', 'Альтернативні маршрути');
define('TEXT_AVOID_FERRIES', 'Уникати паромів');
define('TEXT_AVOID_HIGHWAYS', 'Уникати шосе');
define('TEXT_AVOID_TOLLS', 'Уникати платних проїздів');
define('TEXT_TRIM_VALUE', 'Обрізати значення');
define('TEXT_TRIM_VALUE_INFO', 'Може бути використано, якщо Вам необхідно отримати частину значення поля.');
define(
    'TEXT_TRIM_VALUE_EXAMPLE',
    'Буде використано PHP функцію <a href="https://www.php.net/manual/ru/function.substr.php" target="_blank">substr</a>.<br>В полі введіть int $start [, int $length ]. Наприклад: 0,4'
);
define('TEXT_DISPLAY_LAST_COMMENT_IN_LISTING', 'Відображати останній коментар у списку');
define('TEXT_DISPLAY_LAST_COMMENT_IN_LISTING_INFO', 'Коментар буде відображено разом з заголовком.');
define(
    'TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_FIELDS_IN_POPUP_TIP',
    'Введіть ID полів через кому для кожної адреси з нового рядка.'
);
define(
    'TEXT_FORM_CONFIG_INFO',
    'На цій сторінці у Вас є можливість відсортувати поля у формі, створити додаткові вкладки для полів а також налаштувати свій JavaScript код у формі. <a href="https://keruy.com.ua/index.php?p=64" target="_blank">Докладніше</a>.'
);
define('TEXT_ADD_JAVASCRIPT', 'Додати JavaScript');
define('TEXT_JAVASCRIPT_IN_FORM', 'JavaScript у формі');
define('TEXT_JAVASCRIPT_IN_FORM_INFO', 'Введений JavaScript буде вбудовано до форми під час її відображення.');
define(
    'TEXT_JAVASCRIPT_ONSUBMIT_FORM_INFO',
    'Введений JavaScript буде викликаний у випадку onSubmit. Для того щоб зупинити надсилання форми використовуйте: return false;'
);
define('TEXT_DEFAULT_NOTIFICATIONS', 'Сповіщення за замовченням');
define(
    'TEXT_DEFAULT_NOTIFICATIONS_INFO',
    'Сповіщення за замовченням надсилаються всім призначеним користувачам. Використовуйте тип поля "Користувачі" для призначення користувачів на запис.<br>Для більш гнучких налаштувань сповіщень використовуйте <a href="https://keruy.com.ua/index.php?p=75" target="_blank"><u>правила надсилання пошти</u></a>.'
);
define('TEXT_HIDE_CHECKBOXES_IF_NO_ACCESS', 'Сховати прапорці, якщо відсутні права на редагування поля');
define('TEXT_BODY', 'Тіло');
define('TEXT_END', 'Кінець');
define('TEXT_ALLOW_PUBLIC_ACCESS', 'Дозволити публічний доступ');
define('TEXT_ENTER_TEXT_PATTERN_INFO_SHORT', 'Використовуйте [ID поля] для встановлення значення поля в шаблоні.');
define(
    'TEXT_PUBLIC_ATTACHMENTS_TIP',
    'Оберіть вкладення, для яких буде дозволено публічний доступ за спеціальним посиланням. Дана опція використовується під час експорту даних через api, xml  тощо.'
);
define('TEXT_DISPLAY_ONLY_ASSIGNED_RECORDS', 'Відображати лише призначені записи');
define(
    'TEXT_DISPLAY_ONLY_ASSIGNED_RECORDS_INFO',
    'Зазначене правило застосовуватиметься, якщо для обраної сутності встановлено права "' . TEXT_VIEW_ASSIGNED_ACCESS . '"'
);
define('TEXT_HIDE_COUNTER_IF_NO_RECORDS', 'Сховати лічильник, якщо немає записів');
define('TEXT_HEADER', 'Верхній колонтитул');
define('TEXT_FOOTER', 'Нижній колонтитул');
define('TEXT_FIELDTYPE_DYNAMIC_DATE_TITLE', 'Динамічна дата');
define(
    'TEXT_FIELDTYPE_DYNAMIC_DATE_TOOLTIP',
    'Даний тип поля призначений для відображення дати, що розраховується за MySQL формулою. Формула має повертати значення до timestamp. Формат дати визначте в налаштуваннях поля. Цей тип поля може бути застосовано в календарномі звіті або діаграмі Ганта.'
);
define(
    'TEXT_GLOBAL_LIST_USER_NOTES',
    'Дана опція використовується, якщо список застосовується до поля "Група користувачів".'
);
define('TEXT_RECORDS_VISIBILITY', 'Видимість записів');
define(
    'TEXT_RECORDS_VISIBILITY_INFO',
    'Налаштуйте видимість записів залежно від значення полів. <a href="https://keruy.com.ua/index.php?p=82" target="_blank"><u>Докладніше</u></a>'
);
define('TEXT_ADD_RULE', 'Додати правило');
define('TEXT_USERS_GROUPS_FOR_RULE_TIP', 'Оберіть групи користувачів, для яких буде застосовано дане правило.');
define('TEXT_LINKED_ENTITIES', 'Пов’язані сутності');
define(
    'TEXT_RECORDS_VISIBILITY_LINK_ENTITY_INFO',
    'Встановити зв’язок між сутністю Користувач та поточною сутністю за допомогою полів, що використовують однаковий глобальний список або сутність.'
);
define('TEXT_MAX_DATE', 'Максимальна дата');
define('TEXT_MIN_DATE', 'Мінімальна дата');
define('TEXT_OVERDUE_DATES', 'Прострочені дати');
define('TEXT_DISABLE_COLOR', 'Вимкнути колір');
define('TEXT_DISABLE_COLOR_BY_FIELD_TIP', 'Оберіть поле та значення, за яких виділення кольором буде вимкнено.');
define('TEXT_HIDE_ACCESS_GROUP', 'Сховати призначення групи зі списку');
define('TEXT_USE_GROUPS_TIP', 'Будуть відображені користувачі лише зазначених груп.');
define(
    'TEXT_FIELDTYPE_ENTITY_MULTILEVEL_PARENT_FIELD_TIP',
    'Значення батьківського елементу буде використовуватися із обраного поля.'
);
define('TEXT_DIGITAL_SIGNATURE_LOGIN', 'Вхід за ЕЦП');
define('TEXT_DIGITAL_SIGNATURE_LOGIN_INFO', 'Оберіть модуль ЕЦП, який буде використовуватися для входу користувачів.');
define('TEXT_EXTENSION_REQUIRED', 'Потребує доповнення.');
define('TEXT_FIELDTYPE_ACCESS_GROUP_TITLE', 'Групи доступу користувачів');
define(
    'TEXT_FIELDTYPE_ACCESS_GROUP_TOOLTIP',
    'Ви можете призначити групу доступу користувачів на запис. Всі користувачі із обраної групи матимуть доступ до запису.'
);
define('TEXT_FIELDTYPE_ACCESS_GROUP_USERS_GROUP_TIP', 'Будуть відображені лише визначені групи.');
define(
    'TEXT_FIELDTYPE_ACCESS_GROUP_NOTIFY_TIP',
    'Сповіщення відправлятимуться всім користувачам обраних груп. Рекомендовано увімкнути опцію "Надсилати за розкладом " в налаштуваннях електронної пошти.'
);
define('TEXT_MULTI_LEVEL_IMPORT', 'Багаторівневий імпорт');
define(
    'TEXT_MULTI_LEVEL_IMPORT_INFO',
    'Зазначте сутність необхідного рівня вкладення для імпорту багаторівневих записів.'
);
define(
    'TEXT_MULTI_LEVEL_IMPORT_NOTE',
    'Обов’язковим правилом є прив’язування кожного поля, що позначений як заголовок, для кожного рівня дерева сутностей.'
);
define('TEXT_MULTI_LEVEL_IMPORT_HEADING_ERROR', 'Помилка: відсутнє поле заголовку для сутності "%s".');
define('TEXT_HEADING', 'Заголовок');
define('TEXT_FIELDTYPE_SIGNATURE_TITLE', 'Підпис');
define(
    'TEXT_FIELDTYPE_SIGNATURE_TOOLTIP',
    'Поле дозволяє додати ім’я та підпис. Не пов’язане з користувачами додатку.'
);
define('TEXT_ENTER_YOUR_NAME', 'Введіть Ваше ім’я');
define('TEXT_DISPLAY_PROGRESS_BAR', 'Відобразити індикатор виконання');
define('TEXT_MIN_WIDTH', 'Мінімальна ширина');
define('TEXT_PROGRESS_BAR', 'Індикатор виконання');
define('TEXT_FIELDTYPE_STAGES_TITLE', 'Етапи');
define(
    'TEXT_FIELDTYPE_STAGES_TOOLTIP',
    'Спеціальний тип поля, що відображає панель етапів на сторінці запису, за допомогою якої можна швидку перейти до наступного етапу. Після створення поля, натисніть на назву аби створити етапи. <a href="https://keruy.com.ua/index.php?p=81" target="_blank"><u>Докладніше</u></a>'
);
define('TEXT_STAGES_PANEL', 'Панель етапів');
define('TEXT_TRIANGLE', 'Трикутник');
define('TEXT_RECTANGLE', 'Прямокутник');
define('TEXT_DOT', 'Точка');
define('TEXT_CIRCLE', 'Коло');
define('TEXT_ACTION_BY_CLICK', 'Дія за кліком');
define('TEXT_ALLOW_CHANGING_VALUE', 'Дозволити зміну значення');
define('TEXT_ALLOW_CHANGING_VALUE_NEXT_STEP', 'Дозволити зміну значення лише на наступному етапі');
define('TEXT_ACTIVE_ITEM_COLOR', 'Колір активного елементу');
define('TEXT_CONFIRM_ACTION', 'Підтвердьте дію');
define(
    'TEXT_FIELDTYPE_STAGES_ACTION_TIP',
    'У Вас є можливість підключити процес із автоматизації на будь-якому етапі. Якщо процесо обрано, він буде виконуватися під час переходу на цей етап.'
);
define('TEXT_FIELDTYPE_IFRAME_TITLE', 'Iframe');
define('TEXT_FIELDTYPE_IFRAME_TOOLTIP', 'Просте поле вводу для введення url-адреси, яка буде відкрита в iframe');
define('TEXT_SCROLL_BAR', 'Полоса прокрутки');
define('TEXT_EXTRA_PARAMS', 'Додаткові параметри');
define('TEXT_FIELDTYPE_IFRAME_EXTRA_PARAMS_TIP', 'Будуть включені до в тегу iframe');
define('TEXT_2STEP_VERIFICATION', 'Двоетапна аутентифікація');
define(
    'TEXT_2STEP_VERIFICATION_INFO',
    'Якщо увімкнено, то користувачу під час входу до системи окрім облікових даних необхідно  буде ввести спеціальний код, який буде надісланий на визначений email або SMS. <a href="https://keruy.com.ua/index.php?p=83" target="_blank"><u>Докладніше.</u></a>'
);
define('TEXT_ENABLE_TEXT_2STEP_VERIFICATION', 'Увімкнути двоетапну аутентифікацію');
define('TEXT_SEND_CODE_BY', 'Надсилати код через');
define(
    'TEXT_2STEP_VERIFICATION_EMAIL_SUBJECT',
    'Вхід до ' . $_SERVER['HTTP_HOST'] . ' ' . i18n_date('d F Y h:i', time())
);
define('TEXT_2STEP_VERIFICATION_EMAIL_BODY', 'Код для входу: %s');
define('TEXT_CODE_FROM_EMAIL', 'Код із Email');
define('TEXT_CODE_FROM_EMAIL_INFO', 'Вам надіслано повідомлення з кодом на адресу %s');
define('TEXT_INCORRECT_CODE', 'Неправильний код');
define('TEXT_CODE_FROM_SMS', 'Код із SMS');
define('TEXT_CODE_FROM_SMS_INFO', 'Вам надіслано SMS повідомлення з кодом на номер %s');
define('TEXT_MAIN_FEATURES', 'Загальні можливості');
define(
    'TEXT_EXT_FEATURES_LIST',
    'Календарь,Діаграма Ганта,Воронкоподібна діаграма,Канбан-дошка,Зведені звіти,Онлайн чат,Інтеграція з поштою,Телефонія,SMS,API'
);
define('TEXT_FULL_LIST_OF_FEATURES', 'Повний список можливостей');

//new defines for version 2.7
define(
    'TEXT_FIELDTYPE_BARCODE_ACCEPTED_TYPE_TIP',
    'Всі типи підтримують різні набори символів або мають обов’язкову довжину. Будь ласка, дивіться в Вікіпедії підтримувані символи і довжину для кожного типу. Найбільш використовувані типи - CODE_128 і CODE_39, так як краще підтримуються сканерами.'
);
define('TEXT_OVERDUE_DATE_WITH_TIME', 'Прострочені дати з урахуванням часу');
define('TEXT_FIELDTYPE_TIME', 'Час');
define(
    'TEXT_FIELDTYPE_TIME_TOOLTIP',
    'Поле для введення часу в форматі: "години": "хвилини". У базі даних значення поля зберігається в хвилинах.'
);
define('TEXT_CALENDAR', 'Календар');
define('TEXT_SUM_IN_COMMENTS', 'Сума в коментарях');
define('TEXT_SUM_IN_COMMENTS_INFO', ' Значення цього поля дорівнюватиме сумі введених значень в коментарях.');
define('TEXT_DISPLAY_PREFIX_SUFFIX_IN_FORM', 'Показати префікс/суфікс у формі');
define(
    'TEXT_PHP_EXTENSION_REQUIRED',
    '<b>Помилка:</b> Потрібно розширення PHP "<b>%s</b>". Перевірте php.ini на вашому сервері, щоб включити це розширення.'
);
define('TEXT_LOGIN_BY_PHONE_NUMBER', 'Вхід за номером телефону');
define('TEXT_ALLOW_LOGIN_BY_PHONE_NUMBER', 'Дозволити вхід за номером телефону');
define(
    'TEXT_ALLOW_LOGIN_BY_PHONE_NUMBER_INFO',
    'На сторінці входу з’явиться додаткове посилання на сторінку "' . TEXT_LOGIN_BY_PHONE_NUMBER . '"'
);
define('TEXT_ENTER_YOUR_PHONE_NUMBER', 'Введіть ваш номер телефону');
define(
    'TEXT_FIELDTYPE_ENTITY_MYSQL_QUERY_TIP',
    'Додайте MySQL умову для вибірки тільки певних записів. <a href="https://keruy.com.ua/index.php?p=78" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_HIGHLIGHT_ROW', 'Виділити рядок');
define(
    'TEXT_LISTING_HIGHLIGHT_ROW_INFO',
    'Встановіть значення полів, при яких буде виділятися кольором весь рядок в списку. Дані правила застосовуються до всіх списків. <br> Ви можете встановити кілька правил, і вони будуть застосовуватися в порядку сортування.'
);
define('TEXT_FIELD_ACCESS_INFO', 'Налаштуйте доступ до поля для кожної групи користувачів.');
define('TEXT_YOU_CANT_DELETE_YOURSELF', 'Ви не можете видалити себе');
define('TEXT_DOCUMENTATION', 'Документація');
define('TEXT_NEWS', 'Новини');
define('TEXT_USER_ACTIVATION', 'Активація користувача');
define('TEXT_BY_EMAIL', 'По електронній пошті');
define('TEXT_MANUALLY', 'Вручну');
define(
    'TEXT_PUBLIC_REGISTRATION_TIP',
    'Ваші клієнти/підрядники зможуть самостійно реєструватися в системі. <a href="https://keruy.com.ua/index.php?p=14" target="_blank"><u>Детальніше.</u></a>'
);
define(
    'TEXT_USER_ACTIVATION_AUTOMATIC_TIP',
    'Обліковий запис користувача автоматично активується після реєстрації, і користувач відразу отримує доступ до додатка.'
);
define(
    'TEXT_USER_ACTIVATION_BY_EMAIL_TIP',
    'Обліковий запис активується після підтвердження Email адреси. На Email адресу користувача буде відправлений спеціальний код.'
);
define('TEXT_USER_ACTIVATION_MANUALLY_TIP', 'Адміністратор вручну перевіряє і активовує нових користувачів.');
define('TEXT_REGISTRATION_SUCCESS_PAGE', 'Сторінка успішної реєстрації');
define('TEXT_EMAIL_ABOUT_USER_ACTIVATION', 'Лист про активацію користувача');
define('TEXT_REGISTRATION_SUCCESS_PAGE_HEADING', 'Ваш аккаунт створений і неактивний на даний момент');
define(
    'TEXT_REGISTRATION_SUCCESS_PAGE_DESCRIPTION',
    'Найближчим часом ваш обліковий запис буде перевірено. Ви отримаєте повідомлення про активацію вашого облікового запису.'
);
define('TEXT_USER_ACTIVATION_EMAIL_SUBJECT', 'Ваш обліковий запис активовано');
define('TEXT_USER_ACTIVATION_EMAIL_BODY', 'Увійдіть в додаток за наступним посиланням %s');
define('TEXT_EMAIL_VERIFICATION_EMAIL_SUBJECT', 'Підтвердження адреси електронної пошти');
define(
    'TEXT_EMAIL_VERIFICATION_EMAIL_BODY',
    '
<p>Доброго дня,<br>
Ви отримали цей лист, оскільки вказали дану поштову адресу при реєстрації на сайті "<a href="' . url_for(
        'users/login',
        '',
        true
    ) . '">' . CFG_APP_NAME . '</a>".</p>
<p>Нам необхідно переконатися в тому, що саме Ви є власником цієї e-mail адреси.</p>
<p>Для підтвердження електронної скриньки, введіть наступний код: <b>%s</b></p>
<p><small>Цей лист був відправлений автоматично.<br>
Якщо Ви вважаєте, що отримали його помилково, просто ігноруйте його.</small></p>
'
);
define('TEXT_EMAIL_VERIFIED', 'Поштова адреса підтверджена');
define('TEXT_EMAIL_NOT_VERIFIED', 'Email не підтверджено');
define('TEXT_CHECK_SPAM_FOLDER', 'Якщо лист не прийде протягом п’яти хвилин, перевірте папку "Спам".');
define('TEXT_RESEND_CODE', 'Повторно відправити код');
define('TEXT_RESEND_CODE_TIP', 'Для повторного відправлення коду введіть ваш логін/пароль.');
define('TEXT_CHANGE_EMAIL', 'Змінити адресу електронної пошти');
define('TEXT_HIDE_COORDINATES_IN_FORM', 'Приховати координати в формі');
define('TEXT_FIELDTYPE_DIGITAL_SIGNATURE_TITLE', 'Цифровий підпис');
define(
    'TEXT_FIELDTYPE_DIGITAL_SIGNATURE_TOOLTIP',
    'Це поле призначене для роботи з модулями цифрового підпису, що дозволяють підписувати документи. <a href="https://keruy.com.ua/index.php?p=84" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_MODULE', 'Модуль');
define('TEXT_FIELDS_FOR_SIGNATURE', 'Поля для підпису');
define('TEXT_DIGITAL_SIGNATURE', 'Електронний підпис');
define('TEXT_FIELDTYPE_AJAX_REQUEST_TITLE', 'Ajax запит');
define(
    'TEXT_FIELDTYPE_AJAX_REQUEST_TOOLTIP',
    'Спеціальний тип поля, що дозволяє вам виконати обчислення на льоту в формі запису. <a href="https://keruy.com.ua/index.php?p=99" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_PHP_CODE', 'PHP код');
define('TEXT_DEBUG_MODE', 'Режим налагодження');
define('TEXT_CUSTOM_CSS', 'Користувацький CSS');
define('TEXT_CUSTOM_CSS_INFO', 'Ви можете додати свої власні стилі, які перевизначать CSS за замовчуванням.');
define('TEXT_FILE_PATH', 'Шлях до файлу');
define('TEXT_ERROR_FOLDER_NOT_WRITABLE', 'Помилка: папка "%s" недоступна для запису!');
define('TEXT_SET_FILTERS_FOR_ACTION_BUTTON', 'Встановіть фільтри, при яких кнопка дії буде доступна.');
define('TEXT_HIDE_ENTITY_NAME', 'Приховати назву сутності');
define('TEXT_TOOLBAR', 'Панель інструментів');
define('TEXT_IN_ONE_LINE', 'В один рядок');
define('TEXT_FIELDTYPE_USERS_AJAX_TITLE', 'Користувачі Ajax');
define(
    'TEXT_FIELDTYPE_USERS_AJAX_TOOLTIP',
    'Поле відображається у вигляді випадаючого списку, дані в який завантажуються ajax запитом. Вибрані користувачі зі списку вважаються призначеними на запис і отримують повідомлення.'
);
define('TEXT_DISPLAY_SETTINGS', 'Налаштування відображення');
define('TEXT_HIDE_EMPTY_BLOCK', 'Приховати порожній блок');
define('TEXT_HIDE_BY_CONDITION', 'Приховати за умовою');
define(
    'TEXT_HIDE_BY_CONDITION_SUBENTITY_INFO',
    'Встановіть фільтри для батьківського запису, при яких вкладена сутність буде прихована на сторінці запису.'
);
define('TEXT_SEPARATOR', 'Роздільник');
define('TEXT_RECORDS_VISIBILITY_EMPTY_VALUE_INFO', 'Для обраних полів буде додана перевірка на порожнє значення.');
define('TEXT_FIELDTYPE_ITEMS_BY_QUERY_TITLE', 'Список записів по MySql запиту');
define(
    'TEXT_FIELDTYPE_ITEMS_BY_QUERY_TOOLTIP',
    'Спеціальний тип поля, який відображає список записів по запиту користувача до бази даних. <a href="https://keruy.com.ua/index.php?p=101" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_NUMBER_OF_RECORDS', 'Кількість записів');
define('TEXT_TOTAL', 'Всього');
define('TEXT_ERROR_REQUIRED_URL', 'Будь ласка, введіть коректну адресу.');
define(
    'TEXT_EXTENSION_REQUIRED_URL',
    '<a href="https://keruy.com.ua/extension" target="_blank">' . TEXT_EXTENSION_REQUIRED . '</a>'
);

//new defines for version 2.8
define('TEXT_JS_CODE', 'JS код');
define(
    'TEXT_CODE_ON_ITEM_PAGE',
    'Введений код буде вбудовуватися на сторінку запису. <a href="https://keruy.com.ua/index.php?p=66" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_FIELDTYPE_PHP_CODE_TITLE', 'PHP код');
define(
    'TEXT_FIELDTYPE_PHP_CODE_TOOLTIP',
    'Спеціальний тип поля, що дозволяє вам виконати власний PHP код. <a href="https://keruy.com.ua/index.php?p=104" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_RUN_DYNAMIC', 'Виконувати динамічно');
define(
    'TEXT_FIELDTYPE_PHP_CODE_RUN_DYNAMIC_INFO',
    'За замовчуванням код виконується при додаванні/редагуванні запису. Встановивши цю опцію код буде виконуватися безпосередньо при перегляді запису.'
);
define('TEXT_FIELDTYPE_PROCESS_BUTTON_TITLE', 'Кнопка процесу');
define(
    'TEXT_FIELDTYPE_PROCESS_BUTTON_TOOLTIP',
    'Спеціальний тип поля, за допомогою якого можна відобразити кнопку з автоматизації дій безпосередньо в списку записів. <a href="https://keruy.com.ua/index.php?p=105" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_FIELDTYPE_VIDEO_TITLE', 'Відео');
define(
    'TEXT_FIELDTYPE_VIDEO_TOOLTIP',
    'Поле введення для введення url-адреси з YouTube або Vimeo. Також ви можете ввести пряме посилання на відеофайл (.mp4 .ogg .webm).'
);
define('TEXT_VIDEO_PLAYER', 'Відеоплеєр');
define('TEXT_VIDEO_TAG_NOT_SUPPORTED', 'Ваш браузер не підтримує тег video.');
define('TEXT_BUTTON_DISPLAYS_IN_LISTING', 'Кнопка відображається в списку записів');
define('TEXT_HIDE_VIDEO_PLAYER', 'Приховати відеоплеєр на сторінці запису');
define('TEXT_FIELDTYPE_INPUT_ENCRYPTED_TITLE', 'Зашифроване поле вводу');
define('TEXT_HIDE_VALUE', 'Приховати значення');
define(
    'TEXT_FIELDTYPE_INPUT_ENCRYPTED_TOOLTIP',
    'Введене значення зберігається в базі в зашифрованому вигляді. Дані шифруються за допомогою спеціального ключа.'
);
define('TEXT_ENCRYPTION_KEY', 'Ключ шифрування');
define(
    'TEXT_ENCRYPTION_KEY_INFO',
    'Щоб додати ключ шифрування відкрийте <code>config/server.php</code> файл і <br>
вставте наступний рядок в кінці файлу: <code>define(\'DB_ENCRYPTION_KEY\',\'my_key\');</code><br>
Замість <code>my_key</code> введіть ваш ключ.<br>
<b>Увага:</b> не можна змінювати ключ для існуючих даних. Це призведе до втрати даних.'
);
define('TEXT_FIELDTYPE_TEXTAREA_ENCRYPTED_TITLE', 'Зашифроване поле для тексту');
define('TEXT_ENCRYPTION_KEY_ERROR', 'Помилка: для поля "%s" потрібно ключ шифрування. Перевірте налаштування поля.');
define('TEXT_ADD_ROW', 'Додати рядок');
define('TEXT_ROW', 'Рядок');
define('TEXT_COUNT_OF_COLUMNS', 'Кількість колонок');
define('TEXT_COLUMN_WIDTH', 'Ширина колонки');
define('TEXT_PREVIEW', 'Попередній перегляд');
define(
    'TEXT_FORMS_ROWS_INFO',
    'Після додавання рядка перемістіть поля в колонки. Зверніть увагу: ширина поля дорівнює ширині колонки.'
);
define('TEXT_FIELD_NAME_IN_NEW_ROW', 'Назва поля з нового рядка');
define('TEXT_CONFIRM', 'Підтвердити');

define('TEXT_FAVICON', 'Значок веб-сайту (Favicon)');
define('TEXT_ONE_COLUMN_TABS', 'Одна колонка (Вкладки)');
define('TEXT_ONE_COLUMN_ACCORDION', 'Одна колонка (Акордеон)');
define('TEXT_WINDOW_WIDTH', 'Ширина вікна');
define('TEXT_WIDE', 'Широке');
define('TEXT_XWIDE', 'Дуже Широке');
define('TEXT_FIELDTYPE_JALALI_CALENDAR_TITLE', 'Джалалі Календар');
define(
    'TEXT_FIELDTYPE_JALALI_CALENDAR_TOOLTIP',
    'Календар Джалалі-це сонячний календар, який все ще використовується в Ірані, а також в Афганістані.'
);
define('TEXT_PLAY_AUDIO_FILE', 'Відтворити аудіофайл');
define('TEXT_VALIDATE_URL', 'Перевірка url-адреси');
define('TEXT_DATE_FORMAT_IN_CALENDAR', 'Формат дати в календарі');
define('TEXT_FIELDTYPE_SUBENTITY_FORM_TITLE', 'Форма вкладеної сутності');
define(
    'TEXT_FIELDTYPE_SUBENTITY_FORM_TOOLTIP',
    'Цей тип дозволяє вбудувати форму вкладеної сутності в форму батьківської сутності. Це означає, що при додаванні батьківського запису одночасно будуть додаватися і записи для вкладеної сутності. <a href="https://keruy.com.ua/index.php?p=107" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_SUB_ENTITY', 'Вкладена сутність');
define('TEXT_FIELDS_DISPLAY', 'Відображення полів');
define('TEXT_INTO_COLUMN', 'В стовпчик');
define('TEXT_INTO_ROW', 'В рядок');
define('TEXT_IN_NEW_WINDOW', 'В новому вікні');
define('TEXT_FIELDS_IN_FORM', 'Поля в формі');
define(
    'TEXT_FIELDS_DISPLAY_IN_FOR_TYPE_ROW',
    'При такому відображенні ви можете використовувати обмежену кількість полів. Правила відображення полів у формі не підтримуються.'
);
define(
    'TEXT_FIELDS_DISPLAY_IN_FOR_TYPE_NEW_WINDOW',
    'При натисканні на кнопку форма відкриється у власному вікні. В цьому випадку підтримуються всі поля.'
);
define('TEXT_FIELDS_IN_LISTING_ON_FORM_PAGE', 'Додані записи відображаються в табличному списку на сторінці форми.');
define(
    'TEXT_ENTER_COLUMN_WIDTH_BY_COMMA',
    'Введіть ширину у відсотках для кожної колонки через кому.<br>Наприклад: 50,25,25'
);
define('TEXT_MAX_COUNT_RECORDS', 'Максимальна кількість записів');
define('TEXT_MAX_COUNT_RECORDS_IN_FORM_INFO', 'Обмежте кількість записів в формі');
define('TEXT_SHOW_NUMBER_OF_RECORDS', 'Показати кількість записів');
define('TEXT_INSERT_RECORD_AUTOMATICALLY', 'Автоматично вставляти запис');
define('TEXT_INSERT_RECORD_AUTOMATICALLY_INFO', 'Введіть кількість записів, які будуть автоматично додані в форму.');
define('TEXT_PDF_EXPORT_FONTS', 'Шрифти для експорту в pdf');
define(
    'TEXT_PDF_EXPORT_FONTS_INFO',
    'Для експорту даних в PDF форматі використовується бібліотека <a href="https://github.com/dompdf/dompdf" target="_blank">Dompdf</a>. На цій сторінці ви можете додати свої власні шрифти, які можна використовувати в шаблонах експорту. <a href="https://keruy.com.ua/index.php?p=108" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_FONTS_FOLDER', 'Папка шрифтів');
define('TEXT_ERROR_FILE_NOT_WRITABLE', 'Помилка: файл "%s" недоступний для запису!');
define('TEXT_ROTATE', 'Повернути');
define('TEXT_CREATE_ATTACHMENTS_PREVIEW', 'Створення окремого файлу для попереднього перегляду зображення');
define(
    'TEXT_CREATE_ATTACHMENTS_PREVIEW_TIP',
    'Застосовується якщо для поля "Вкладення" встановлена опція "Попередній перегляд"'
);
define('TEXT_FOLDER', 'Папка');
define('TEXT_FIELDTYPE_INPUT_IP_TITLE', 'IPv4 адреса');
define('TEXT_FIELDTYPE_INPUT_IP_TOOLTIP', 'Поле для вводу IPv4 адреси. В базі даних значення зберігається як число.');
define('TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_TITLE', 'Поле з динамічною маскою введення');
define(
    'TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_TOOLTIP',
    'Дозволяє створити маску введення для даних у яких немає фіксованої довжини. Також ви можете додати свій JavaScript код для визначення маски. Використовується така бібліотека: <a href="https://github.com/RobinHerbots/Inputmask" target="_blank">github.com/RobinHerbots/Inputmask</a>'
);
define(
    'TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_INFO',
    'Щоб визначити динамічну частину, використовуйте конструкцію {n, m} - це означає що символ може повторюватися від n до m раз.'
);
define(
    'TEXT_FIELDTYPE_INPUT_DYNAMIC_MASK_OPTIONAL_INFO',
    'Щоб визначити необов’язкову частину, використовуйте конструкцію [a] - це означає що символ можна пропустити.'
);
define('TEXT_BARCODE', 'Штрих-код');
define(
    'TEXT_ENTER_TEXT_PATTERN_DATE_INFO',
    '${Ymd} - поточна дата: ' . date(
        'Ymd'
    ) . ' (<a href="https://www.php.net/manual/ru/datetime.format.php" target="_blank">формат дати</a> можна коректувати)'
);
define('TEXT_BARCODE_GENERATED_AUTOMATICALLY', 'Штрих-код генеруєтся автоматично');
define('TEXT_FIELDTYPE_RANDOM_VALUE_UNIQUE_TOOLTIP', 'Кожне значення є унікальним для поточної сутності.');
define('TEXT_SCANNING_BARCODE', 'Сканування штрих-коду');
define('TEXT_SSL_REQUIRED', 'Потрібно SSL');
define('TEXT_CAMERA', 'Камера');

//new defines for version 2.9
define('TEXT_APPLY', 'Застосувати');
define('TEXT_CANCEL', 'Скасувати');
define('TEXT_ASSIGN_USER_TO_MULTIPLE_GROUPS', 'Призначати користувача на кілька груп');
define(
    'TEXT_ASSIGN_USER_TO_MULTIPLE_GROUPS_INFO',
    'Користувач зможе перемикатися між групами в особистому кабінеті. <a href="https://keruy.com.ua/index.php?p=116" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_DISPLAY_USER_GROUP_IN_MENU', 'Відображати назву групи доступу в меню');
define('TEXT_CHANGE_ACCESS_GROUP', 'Змінити групу доступу');
define('TEXT_LOGIN_AS_USER', 'Ввійти як');
define("TEXT_HOURLY", 'Щогодини');
define('TEXT_DISTANCE', 'Відстань');
define('TEXT_CALCULATE_TOTAL_DISTANCE', 'Обчислити загальну відстань');
define('TEXT_KILOMETERS', 'Кілометри');
define('TEXT_MILES', 'Милі');
define('TEXT_TOTAL_DISTANCE_IN_KILOMETERS', 'Загальна відстань в кілометрах');
define('TEXT_TOTAL_DISTANCE_IN_MILES', 'Загальна відстань в милях');
define('TEXT_SAVE_VALUE', 'Зберегти значення');
define(
    'TEXT_FIELDTYPE_GOOGLE_MAP_DIRECTIONS_SAVE_VALUE_TIP',
    'Виберіть числове поле, щоб зберегти відстань. Значення буде збережено в фоновому режимі після рендеринга карти.'
);
define('TEXT_ERROR_LIB', 'Библиотека PHP <b>%s</b> не встановлена на вашому веб-сервері');
define('TEXT_ENTER_VALUE_IN_PERCENT_OR_PIXELS', 'Введіть значення в процентах або пікселях. Наприклад: 50 або 50%');
define('TEXT_SOCIAL_LOGIN', 'Авторизація через соцмережі');
define('TEXT_ENABLE_SOCIAL_LOGIN', 'Включити авторизацію через соцмережі');
define('TEXT_ENABLE_SOCIAL_LOGIN_ONLY', 'Використовувати тільки авторизацію через соцмережі');
define('TEXT_SELECT_SOCIAL_NETWORKS', 'Виберіть соціальні мережі');
define('TEXT_APP_ID', 'ID додатки');
define('TEXT_VKONTAKTE', 'ВКонтакте');
define('TEXT_SECRET_KEY', 'Секретний ключ');
define('TEXT_LOGIN_WITH', 'Увійти через');
define('TEXT_CREATE_USER', 'Створювати користувача');
define('TEXT_CREATE_USER_AUTOMATICALLY', 'Автоматично створювати користувача');
define('TEXT_REDIRECT_TO_PUBLIC_REGISTRATION', 'Перенаправляти на форму публічної реєстрації');
define('TEXT_GOOGLE', 'Google');
define('TEXT_FACEBOOK', 'Facebook');
define('TEXT_REDIRECT_URI', 'Redirect URI');
define('TEXT_LINKEDIN', 'LinkedIn');
define('TEXT_TWITTER', 'Twitter');
define(
    'TEXT_SOCIAL_LOGIN_INFO',
    'На цій сторінці у вас є можливість налаштувати авторизацію через популярні соцмережі. <a href="https://keruy.com.ua/index.php?p=118" target="_blank"><u>Докладніше.</u></a>.'
);
define('TEXT_GUEST_LOGIN', 'Гостьовий вхід');
define(
    'TEXT_GUEST_LOGIN_INFO',
    'Налаштуйте гостьовий доступ до додатка без введення логіна і пароля. <a href="https://keruy.com.ua/index.php?p=120" target="_blank"><u>Докладніше.</u></a>'
);
define('TEXT_ENABLE_GUEST_LOGIN', 'Включити гостьовий вхід');
define('TEXT_LOGIN_AS_GUEST', 'Увійти як гість');
define(
    'TEXT_GUEST_LOGIN_USER_INFO',
    'Виберіть користувача, який буде автоматично входити в систему при гостьовому доступі.'
);
define('TEXT_TREE_TABLE', 'Деревовидна таблиця');
define('TEXT_FIELDTYPE_NESTED_CALCULATIONS_TITLE', 'Обчислення з вкладеними записами');
define(
    'TEXT_FIELDTYPE_NESTED_CALCULATIONS_TOOLTIP',
    'Спеціальний тип поля для виконання обчислень по вкладеним записам в рамках однієї гілки дерева в деревовидному списку. <a href="https://keruy.com.ua/index.php?p=123" target="_blank"><u>Докладніше.</u></a>'
    . '<p><b>Зверніть увагу:</b> обчислення виконуються тільки при додаванні / редагуванні запису в конкретній гілці дерева.</p>'
);
define('TEXT_FUNCTION', 'Функція');
define('TEXT_PERFORM_CALCULATION', 'Виконати обчислення');
define('TEXT_ONLY_AT_THE_TOP_LEVEL', 'Тільки в на верхньому рівні');
define('TEXT_ALL_OVER_TREE_BRANCH', 'По всій гілці дерева');
define('TEXT_FUNCTION_SUM', 'SUM - обчислює суму значень');
define('TEXT_FUNCTION_COUNT', 'COUNT - підраховує кількість записів');
define('TEXT_DISPLAY_NESTED_RECORDS', 'Відображати вкладені записи');
define('TEXT_CHANGE_PARENT_ITEM', 'Змінити батьківську запис');

//new defines for version 3.0
define('TEXT_UNIQUE_FOR_EACH_PARENT_RECORD', 'Унікальне для кожного батьківського запису');
define('TEXT_IS_UNIQUE', 'Унікальне?');
define('TEXT_ENTITIES_GROUPS', 'Групи сутностей');
define('TEXT_ENTITIES_GROUPS_INFO', 'Ви можете розділяти та фільтрувати сутності за групами.');
define('TEXT_GROUP', 'Група');
define('TEXT_STRONG_PASSWORD', 'Надійний пароль');
define('TEXT_STRONG_PASSWORD_TIP', 'Пароль повинен містити: цифри, символи верхнього регістру та спеціальні символи.');
define('TEXT_KEEP_CURRENT_FORM_OPEN', 'Залишити поточну форму відкритою');
define('TEXT_SAVE_AND_CLOSE', 'Зберегти та закрити');
define('TEXT_EDITABLE_FIELDS_IN_LISTING', 'Редаговані поля у списку');
define(
    'TEXT_EDITABLE_FIELDS_IN_LISTING_INFO',
    'Користувач може змінити значення окремого поля, клацнувши по комірці таблиці.'
);
define('TEXT_COLLAPSED', 'Згорнутий');
define('TEXT_ALLOW_CHANGE_FILE_NAME', 'Дозволити зміну імені файлу');
define('TEXT_DROP_DOWN_MENU_ON_HOVER', 'Випадаюче меню при наведенні миші');
define('TEXT_YOU_CANT_DELETE_FIELD', 'Ви не можете видалити поле "%s".');
define('TEXT_FIELD_USING_IN_FORMULA', 'Поле використовується у формулі');
define(
    'TEXT_DELETE_FIELD_WARNING',
    '<b>Увага:</b> видалення поля призведе до видалення всіх даних, пов’язаних із цим полем у базі даних.'
);
define('TEXT_TOGGLE_ON', 'Увімкнути');
define('TEXT_TOGGLE_OFF', 'Вимкнути');
define('TEXT_SELECT_IMAGE', 'Виберіть зображення');
define('TEXT_UPLOAD', 'Завантажити');
define('TEXT_SNAP_PHOTO', 'Зробити знімок');
define('TEXT_FIELDTYPE_IMAGE_AJAX_TITLE', 'Поле для завантаження зображення (Ajax)');
define('TEXT_EXCLUDE_VALUES_NOT_IN_LISTING', 'Виключити значення, які відсутні у списку записів');
define('TEXT_FIELDTYPE_COLOR_TITLE', 'Вибір кольору');
define(
    'TEXT_FIELDTYPE_COLOR_TOOLTIP',
    'Відображає кольори, які були налаштовані для опцій, безпосередньо в списку, що розкривається.'
);
define('TEXT_TARGET', 'Ціль');
define('TEXT_TARGET_BLANK', 'Нове вікно (_blank)');
define('TEXT_TARGET_SELF', 'Поточне вікно (_self)');
define('TEXT_SIGNATURE_REQUIRED', 'Потрібен підпис');
define('TEXT_DISABLE_USER_AVATAR', 'Вимкнути аватар користувача');
define('TEXT_CURRENT_USER_GROUP', 'Поточна група користувача');
define('TEXT_CUSTOM_HTML', 'Користувацький HTML');
define('TEXT_CUSTOM_HTML_INFO', 'Додайте код користувача в теги head и body. Підтримуються тільки HTML, CSS и JS.');
define('TEXT_ADD_CODE_END_OF_HEAD', 'Додайте код в кінці тега <head>');
define('TEXT_ADD_CODE_BEFORE_BODY', 'Додайте код перед тегом </body>');
define('TEXT_RESET_SORTING', 'Скинути сортування');
define('TEXT_VALUES_WILL_SORTED_BY_NAME', 'Значення будуть відсортовані за назвою');
define('TEXT_DISPLAY_PARENT_NAME', 'Відображати назву батька');
define('TEXT_SELECT_ALL_RECORDS', 'Вибрати усі записи');
define('TEXT_ON_CURRENT_PAGE_ONLY', 'Тільки на поточній сторінці');
define('TEXT_SELECTED_RECORDS', 'Вибрано записів: <b>%s</b>');
define('TEXT_RESET_SELECTION', 'Скинути вибір');
define(
    'TEXT_FIELDTYPE_AUTOSTATUS_ACTION_TIP',
    'Ви маєте можливість підключити процес з автоматизації до будь-якого статусу. Якщо процес вибраний, він буде виконуватись автоматично при переході на вказаний статус.'
);
define('VALUE_FROM_PARENT_ENTITY', 'Значення батьківської сутності');
define(
    'TEXT_FIELDTYPE_ENTITY_MULTILEVEL_PARENT_FIELD_VALUE_TIP',
    'Якщо створено окреме поле, в якому вибирається значення з батьківської сутності, виберіть це поле, щоб встановити взаємозв’язок списків. <a href="https://keruy.com.ua/index.php?p=126" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_TEXT', 'Текст');
define('TEXT_BACKGROUND', 'Фон');
define('TEXT_SAVE_AS', 'Зберегти як');
define("TEXT_ADD_RECORDS_TO_FAVORITES", "Додавати записи до обраного");
define(
    'TEXT_ADD_RECORDS_TO_FAVORITES_TIP',
    'Значок зірочки з’явиться на сторінці запису, що дозволить користувачам додавати записи до вибраного. <a href="https://keruy.com.ua/index.php?p=127" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_FAVORITES', 'Вибране');
define('TEXT_DISPLAYED', 'Відображено');
define('TEXT_DISPLAY_SEARCH_BAR', 'Показувати рядок пошуку');
define('TEXT_FIELDTYPE_IMAGE_MAP_NESTED_TITLE', 'План-схема для вкладених записів');
define(
    'TEXT_FIELDTYPE_IMAGE_MAP_NESTED_TOOLTIP',
    'Спеціальний тип поля для відображення карток користувача. Користувачі матимуть змогу завантажувати власні зображення карти. Маркери на карті – це записи з вкладеної сутності. <a href="https://keruy.com.ua/index.php?p=18" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_HIDDEN_FIELDS_IN_FORM', 'Приховані поля у формі');
define(
    'TEXT_HIDDEN_FIELDS_IN_FORM_TIP',
    'Ви можете приховати технічні поля для всіх груп користувачів, крім адміністраторів.'
);
define('TEXT_ALL_STAGES', 'Усі етапи');
define('TEXT_CONSISTENTLY', 'Послідовно');
define('TEXT_BRANCHING', 'Розгалуження');
define('TEXT_FIELDTYPE_STAGES_SHOW_CONSISTENTLY_TIP', 'Відображати лише один етап після поточного');
define('TEXT_FIELDTYPE_STAGES_SHOW_BRANCHING_TIP', 'Відображається лише поточна гілка у дереві етапів.');
define('TEXT_GLOBAL_VARS', 'Глобальні змінні');
define(
    'TEXT_GLOBAL_VARS_INFO',
    'Створюйте глобальні константи, які можна використовувати у власному PHP-коді або типі поля MySql Формула. <a href="https://keruy.com.ua/index.php?p=128" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_ADD_FOLDER', 'Додати папку');
define('TEXT_CUSTOM_PHP', 'Користувацький PHP');
define(
    'TEXT_CUSTOM_PHP_INFO',
    'Створюйте власні функції та класи, які можна використовувати в полі PHP Код або в PHP коді в автоматизації.  <a href="https://keruy.com.ua/index.php?p=129" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_F11_FULLSCREEN', 'F11 - повний екран');
define('TEXT_DISABLE', 'Вимкнути');
define('TEXT_DISPLAYS_AS_TAB', 'Відображається як вкладка');
define('TEXT_AUTO_BACKUP', 'Автобекап');
define('TEXT_AUTOMATIC_BACKUP', 'Автоматичне резервне копіювання');
define(
    'TEXT_AUTOMATIC_BACKUP_INFO',
    'Для запуску резервного копіювання за розкладом, вам потрібно встановити завдання розкладу (cron) в панелі управління вашого хостингу.'
);
define('TEXT_KEEP_FIELDS', 'Зберігати файли');
define('TEXT_ENTER_NUMBER_OF_DAYS', 'Введіть кількість днів');
define('TEXT_FORM_WIZARD', 'Form Wizard');
define('TEXT_TAB_GROUPS', 'Групи вкладок');
define(
    'TEXT_TAB_GROUPS_INFO',
    'Якщо є велика кількість вкладок, то для економії місця у формі ви можете згрупувати вкладки. <a href="https://keruy.com.ua/index.php?p=64" target="_blank"><u>Детальніше.</u></a>'
);
define(
    'TEXT_FORM_WIZARD_INFO',
    'Розділіть ввід даних по крокам за допомогою вкладок. Якщо цю опцію увімкнено, користувач повинен вводити дані крок за кроком. У цьому випадку кнопка Зберегти відображається на останній вкладці. <a href="https://keruy.com.ua/index.php?p=130" target="_blank"><u>Детальніше.</u></a>'
);
define('TEXT_NEXT', 'Далі');
define('TEXT_PREVIOUS', 'Назад');
define('TEXT_OF', 'з');
//new defines for version 3.1
define('TEXT_EMAILS_LAYOUT', 'Макет листа');
define(
    'TEXT_EMAILS_LAYOUT_INFO',
    'Створіть HTML макет для електронних листів у програмі. <a href="https://keruy.com.ua/index.php?p=132" target="_blank"><u>Докладніше.</u></a>'
);
define('TEXT_SEND_TEST_EMAIL_INFO', 'Лист з темою "%s" буде надіслано на адресу:');
define('TEXT_COUNTRY', 'Країна');
define('TEXT_INTERNATIONAL', 'Міжнародний');
define('TEXT_STEAM', 'Steam');
define('TEXT_DOMAIN', 'Доменне ім’я');
define('TEXT_HTML_CODE', 'HTML код');
define('TEXT_MYSQL_QUERY', 'MySQL запит');
define('TEXT_USE_HTML_EDITOR', 'Використовувати HTML редактор');
define('TEXT_EMPTY_VALUE', 'Порожнє значення');
define('TEXT_AVAILABLE_VALUES', 'Доступні значення');
define('TEXT_FIELDTYPE_YANDEX_MAP_TITLE', 'Мапа Яндекс');
define(
    'TEXT_FIELDTYPE_YANDEX_MAP_TOOLTIP',
    'Поле відображення маркера на карті. Координати маркера визначаються автоматично за введеною адресою. <a href="https://keruy.com.ua/index.php?p=135" target="_blank"><u>Докладніше.</u></a>'
);
define(
    'TEXT_FIELDTYPE_YANDEX_MAP_API_KEY_TIP',
    'Зайдіть на сторінку "<a href="https://developer.tech.yandex.ru/" target="_blank">Кабінету Розробника</a>" та натисніть кнопку "Отримати ключ". У спливаючому вікні виберіть сервіс "JavaScript API та HTTP Геокодер"'
);
define('TEXT_YANDEX', 'Яндекс');
