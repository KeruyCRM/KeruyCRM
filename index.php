<?php

use Enums\EnumSystem;

const KERUY = 1;

$fw = require 'core/base.php';
$fw->AUTOLOAD = 'app/';

EnumSystem::VERSION->set('0.1 alpha');
EnumSystem::PACKAGE->set('KERUY.CRM');

//Init configs
Config::instance();
//Init events
Event::instance();
//Init languages
Language::instance();
//Init routes
Route::instance();

Core::fw()->run();