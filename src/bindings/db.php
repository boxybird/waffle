<?php

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Bind database instance to container
 */
$app->singleton('db', function () {
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
    // $capsule->bootEloquent();

    return $capsule;
});
