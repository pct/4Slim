<?php

require 'Slim/Slim.php';
require 'Slim/Views/HaangaView.php';
require 'lib/idiorm.php';
require 'config.php';

define('PROJECT_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR);

ORM::configure('mysql:dbname='.$_config['db_name'].';host='.$_config['db_host']);
ORM::configure('username', $_config['db_user']);
ORM::configure('password', $_config['db_pass']);
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

ORM::configure('id_column_overrides', array(
));

$slim_settings = array(
    'mode'               => $_config['mode'],
    'debug'              => $_config['debug'], 
    'log.enable'         => $_config['log_enable'],
    'view'               => new HaangaView('./lib/Haanga', './tpl', './tpl_cache'),
    'templates.path'     => './tpl',
    'cookies.secret_key' => $_config['cookies_secret_key'],
);

$app = new Slim($slim_settings);
require 'app.php';
$app->run();

?>
