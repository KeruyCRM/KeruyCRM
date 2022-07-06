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

\K::$fw->PROJECT_VERSION = '2.0.0 alpha';

if (file_exists('config/database.php')) {
    include 'config/database.php';
}
include 'config/server.php';
include 'config/security.php';

\K::$fw->LANGUAGE = 'xx';
\K::$fw->FALLBACK = 'xx';

\K::$fw->PREFIX = 'TEXT_';
\K::$fw->LOCALES = 'app/languages/';

$plugins = \K::fw()->split(\K::$fw->AVAILABLE_PLUGINS);
foreach ($plugins as $plugin) {
    \K::$fw->PREFIX = 'TEXT_' . strtoupper($plugin) . '_';
    \K::$fw->LOCALES = 'app/languages/' . $plugin . '/';
}

\K::$fw->route(
    'GET|POST @mainRouterAction: /@extensionName/@moduleName/@controllerName/@actionName',
    '\Controllers\@extensionName\@moduleName\@controllerName->@actionName'
);

\K::$fw->route(
    'GET|POST @mainRouterAction: /@extensionName/@moduleName/@controllerName',
    '\Controllers\@extensionName\@moduleName\@controllerName->index'
);

\K::$fw->route(
    'GET|POST @mainRouterAction: /@extensionName/@moduleName',
    '\Controllers\@extensionName\@moduleName\@moduleName->index'
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