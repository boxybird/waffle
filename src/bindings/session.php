<?php

use BoxyBird\Waffle\App;
use Illuminate\Session\SessionManager;

/**
 * Bind session instance to container
 */
App::getInstance()->singleton('session', function ($app) {
    if (!$app->get('db')->schema()->hasTable('waffle_sessions')) {
        $app->get('db')->schema()->create('waffle_sessions', function ($table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity')->index();
        });
    }

    $session_manager = new SessionManager($app);

    $cookie_name = $session_manager->getName();

    if (isset($_COOKIE[$cookie_name])) {
        if ($session_id = $_COOKIE[$cookie_name]) {
            $session_manager->setId($session_id);
        }
    }

    // Boot the session
    $session_manager->start();

    return $session_manager;
});
