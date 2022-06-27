<?php

const KERUY_CRM = 1;

$fw = require 'core/base.php';
$fw->AUTOLOAD = 'app/';
\K::keruy();

\K::$fw->PACKAGE = 'KeruyCRM';

\K::$fw->UI = 'template/';
\K::$fw->DEBUG = 3;
\K::$fw->CACHE = true;

\K::$fw->DOMAIN = \K::$fw->SCHEME . '://' . \K::$fw->HOST . '/';
\K::$fw->FOLDER_ADMIN = 'admin';
\K::$fw->URI_ADMIN = '/' . \K::$fw->FOLDER_ADMIN;
\K::$fw->URL_ADMIN = \K::$fw->DOMAIN . \K::$fw->FOLDER_ADMIN;

\K::$fw->LOCALES = 'app/languages/';

\K::$fw->FALLBACK = 'en';

\K::$fw->mset([
    'PROJECT_VERSION' => '2.0.0 alpha',
    'TYPE_DATABASE' => 'mysql',//sqlite?
    'TTL_SCHEMA' => 3600,
    'TTL_APP' => 3600,
    'TOKEN_LIFE' => 600,
    'TOKEN_LENGTH' => 32,
    'SESSION_CHECK_IP' => false,
    'SESSION_CHECK_BROWSER' => true,
    'CFG_VERIFICATION_CODE_LENGTH' => 6,
]);

if (file_exists('config/database.php')) {
    include 'config/database.php';
}

\K::$fw->route(
    'GET|POST @mainRouterAction: /@moduleName/@controllerName/@actionName',
    '\Controllers\@moduleName\@controllerName->@actionName'
);

\K::$fw->route(
    'GET|POST @mainRouter: /@moduleName/@controllerName',
    '\Controllers\@moduleName\@controllerName->index'
);

\K::$fw->route(
    'GET|POST /set/install/@action/@lang',
    '\Controllers\Set\Install->@action'
);
\K::$fw->route(
    'GET|POST /set/install/@action',
    '\Controllers\Set\Install->@action'
);

\K::$fw->redirect('GET /install', '/set/install/index');
\K::$fw->redirect('GET /', '/module/dashboard');

//\K::$fw->route('GET /example [ajax]','Page->getFragment');
//\K::$fw->route('GET /example [sync]','Page->getFull');

//require_once 'app/routing.php';

\K::$fw->run();