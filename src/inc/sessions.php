<?php

/**
 * Defer creating and sending session cookie until WordPress is ready
 */
add_action('send_headers', function (): void {
    $app = BoxyBird\Waffle\App::getInstance();

    $config = $app->get('config');
    $session_manager = $app->get('session');

    $cookie = new Symfony\Component\HttpFoundation\Cookie(
        $session_manager->getName(),
        $session_manager->getId(),
        time() + ($config->get('session.lifetime') * 60),
        $config->get('session.path', '/'),
        $config->get('session.domain', null),
        $config->get('session.secure', true),
        $config->get('session.httponly', true),
        $config->get('session.raw', false),
        $config->get('session.same_site', 'lax')
    );

    setcookie(
        $cookie->getName(),
        (string) $cookie->getValue(),
        [
            'expires' => $cookie->getExpiresTime(),
            'path' => $cookie->getPath(),
            'domain' => $cookie->getDomain(),
            'secure' => $cookie->isSecure(),
            'httponly' => $cookie->isHttpOnly()
        ],
    );
});

/**
 * Defer saving session until last possible moment
 */
add_action('wp_footer', function (): void {
    $app = BoxyBird\Waffle\App::getInstance();

    $app->get('session')->save();
}, PHP_INT_MAX);

/**
 * Create a cron job to handle session cleanup
 */
if (!wp_next_scheduled('waffle_delete_expired_sessions')) {
    wp_schedule_event(time(), 'hourly', 'waffle_delete_expired_sessions');
}

/**
 * Cleanup expired sessions
 */
add_action('waffle_delete_expired_sessions', function (): void {
    $app = BoxyBird\Waffle\App::getInstance();

    $lifetime = $app->get('config')->get('session.lifetime');

    $app->get('db')->table('waffle_sessions')
        ->where('last_activity', '<', time() - ($lifetime * 60))
        ->delete();
});
