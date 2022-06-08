<?php

use Illuminate\Session\SessionManager;

/**
 * Bind session instance to container
 */
$waffle_app->singleton('session', function ($waffle_app) {
    $session_manager = new SessionManager($waffle_app);

    $cookie_name = $session_manager->getName();

    if (isset($_COOKIE[$cookie_name])) {
        if ($session_id = $_COOKIE[$cookie_name]) {
            $session_manager->setId($session_id);
        }
    }

    // Create the session table if it doesn't exist.
    if (!get_transient('waffle_sessions_table_exists')) {
        set_transient('waffle_sessions_table_exists', true);

        // Double check if session table doesn't
        // exist as transient may be manually deleted.
        if (!waffle_db()->schema()->hasTable('waffle_sessions')) {
            waffle_db()->schema()->create('waffle_sessions', function ($table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->text('payload');
                $table->integer('last_activity')->index();
            });
        }
    }

    // Boot the session
    $session_manager->start();

    return $session_manager;
});
