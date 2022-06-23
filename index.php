<?php

const KERUY_CRM = 1;

$f3 = require 'core/base.php';

$f3->PROJECT_VERSION = '2.0.0 alpha';
$f3->PACKAGE = 'KeruyCRM';
$f3->AUTOLOAD = 'app/';
$f3->UI = 'template/';
$f3->DEBUG = 3;

$f3->CACHE = true;
$f3->TTL_SCHEMA = 3600;
$f3->TTL_APP = 3600;

$f3->TOKEN_LIFE = 600;//* 60 * 24;//??
$f3->TOKEN_LENGTH = 32;

$f3->SESSION_CHECK_IP = false;
$f3->SESSION_CHECK_BROWSER = true;

$f3->DOMAIN = $f3->SCHEME . '://' . $f3->HOST . '/';
$f3->FOLDER_ADMIN = 'admin';
$f3->URI_ADMIN = '/' . $f3->FOLDER_ADMIN;
$f3->URL_ADMIN = $f3->DOMAIN . $f3->FOLDER_ADMIN;

$f3->LOCALES = 'app/languages/';

$f3->FALLBACK = 'en';

$f3->TYPE_DATABASE = 'mysql';//sqlite?

if (file_exists('config/database.php')) {
    include 'config/database.php';
}

$f3->route(
    'GET|POST @mainRouterAction: /@moduleName/@controllerName/@actionName',
    '\Controllers\@moduleName\@controllerName->@actionName'
);

$f3->route(
    'GET|POST @mainRouter: /@moduleName/@controllerName',
    '\Controllers\@moduleName\@controllerName->@controllerName'
);


$f3->route(
    'GET|POST /set/install/@action/@lang',
    '\Controllers\Set\Install->@action'
);
$f3->route(
    'GET|POST /set/install/@action',
    '\Controllers\Set\Install->@action'
);

$f3->redirect('GET /install', '/set/install/index');
$f3->redirect('GET /', '/module/dashboard');


//$f3->route('GET /example [ajax]','Page->getFragment');
//$f3->route('GET /example [sync]','Page->getFull');

//require_once 'app/routing.php';
//set off session warning

$f3->run();