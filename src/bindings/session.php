<?php

use BoxyBird\Waffle\App;
use Illuminate\Session\SessionManager;

/**
 * Bind session instance to container
 */
App::getInstance()->singleton('session', function ($app): SessionManager {
    global $wpdb;

    $table_name = $wpdb->prefix.'waffle_sessions';

    if (!$app->get('db')->schema()->hasTable($table_name)) {
        $app->get('db')->schema()->create($table_name, function ($table): void {
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

    if (isset($_COOKIE[$cookie_name]) && $session_id = $_COOKIE[$cookie_name]) {
        $session_manager->setId($session_id);
    }

    // Boot the session
    $session_manager->start();

    return $session_manager;
});
