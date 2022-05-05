<?php

use Illuminate\Session\SessionManager;

/**
 * Bind session instance to container
 */
$app->singleton('session', function ($app) {
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
