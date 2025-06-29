<?php

use BoxyBird\Waffle\App;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Bind database instance to container
 */
$capsule = new Capsule;

$capsule->addConnection([
    'driver'   => defined('DB_DRIVER') ? DB_DRIVER : 'mysql',
    'host'     => defined('DB_HOST') ? DB_HOST : 'localhost',
    'database' => defined('DB_NAME') ? DB_NAME : null,
    'username' => defined('DB_USER') ? DB_USER : null,
    'password' => defined('DB_PASSWORD') ? DB_PASSWORD : null,
    'charset'  => defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

App::getInstance()->singleton('db', function () use ($capsule): Capsule {
    return $capsule;
});
