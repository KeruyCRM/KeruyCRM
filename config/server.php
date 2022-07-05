<?php

// Define the webserver and path parameters
\K::fw()->set('DIR_FS_CATALOG', substr(__DIR__, 0, -6));
\K::fw()->set('DIR_FS_UPLOADS', \K::$fw->DIR_FS_CATALOG . 'uploads/');

\K::fw()->mset([
    // secure webserver
    'ENABLE_SSL' => false,

    //Configure server host to build urls correctly in cron
    //Enter [http or https]+[domainname]+[catalog] for example: https://mycompany.com/mypm/
    'CRON_HTTP_SERVER_HOST' => '',

    //developer mode
    //in developer mode DB and PHP logs stored in "log" folder
    'DEV_MODE' => false,

    //Use LDAP login only. Default login page will be disabled
    'CFG_USE_LDAP_LOGIN_ONLY' => false,

    //list of available plugins separated by comma
    'AVAILABLE_PLUGINS' => 'ext',

    // * DIR_FS_* = Filesystem directories (local/physical)
    'DIR_FS_ATTACHMENTS' => \K::$fw->DIR_FS_UPLOADS . 'attachments/',
    'DIR_FS_ATTACHMENTS_PREVIEW' => \K::$fw->DIR_FS_UPLOADS . 'attachments_preview/',
    'DIR_FS_IMAGES' => \K::$fw->DIR_FS_UPLOADS . 'images/',
    'DIR_FS_USERS' => \K::$fw->DIR_FS_UPLOADS . 'users/',
    'DIR_FS_BACKUPS' => \K::$fw->DIR_FS_CATALOG . 'backups/',
    'DIR_FS_BACKUPS_AUTO' => \K::$fw->DIR_FS_CATALOG . 'backups/auto/',
    'DIR_FS_TMP' => \K::$fw->DIR_FS_CATALOG . 'tmp/',
    'DIR_FS_CACHE' => \K::$fw->DIR_FS_CATALOG . 'cache/',

    //// * DIR_WS_* = Webserver directories (virtual/URL)
    'DIR_WS_UPLOADS' => 'uploads/',
    'DIR_WS_ATTACHMENTS' => 'uploads/attachments/',
    'DIR_WS_ATTACHMENTS_PREVIEW' => 'uploads/attachments_preview/',
    'DIR_WS_IMAGES' => 'uploads/images/',
    'DIR_WS_USERS' => 'uploads/users/',
    'DIR_WS_MAIL_ATTACHMENTS' => 'uploads/mail/',
    'DIR_WS_TEMPLATES' => 'uploads/templates/',
    'DIR_WS_CUSTOM_CSS_FILE' => 'css/custom.css',

    'SESSION_NAME' => 'sid',

    //Session Directory
    //If sessions are file based, store them in this directory.
    'SESSION_WRITE_DIRECTORY' => 'memcached=127.0.0.1:11211',

    //session handler
    // leave empty '' for default handler or set to 'mysql'
    'STORE_SESSIONS' => 'mysql',

    //Session Force Cookie Use
    //Force the use of sessions when cookies are only enabled.
    'SESSION_FORCE_COOKIE_USE' => true,
    'SESSION_COOKIE_DOMAIN' => '',
    'SESSION_COOKIE_PATH' => '',

    //force set_mode
    'DB_FORCE_SQL_MODE' => true, //true or false
    'DB_SET_SQL_MODE' => '', //to remove STRICT_TRANS_TABLE

    'TYPE_DATABASE' => 'mysql',//sqlite?
    'TTL_SCHEMA' => 3600,
    'TTL_APP' => 3600,
]);