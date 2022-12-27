<?php

use BoxyBird\Waffle\App;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Bind database instance to container
 */
$capsule = new Capsule;

$capsule->addConnection([
    'driver'   => defined('DB_DRIVER') ? DB_DRIVER : 'mysql',
    'host'     => DB_HOST,
    'database' => DB_NAME,
    'username' => DB_USER,
    'password' => DB_PASSWORD,
    'charset'  => DB_CHARSET,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

App::getInstance()->singleton('db', function () use ($capsule) {
    return $capsule;
});
