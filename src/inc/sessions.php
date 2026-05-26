<?php

/**
 * Defer creating and sending session cookie until WordPress is ready
 */

use BoxyBird\Waffle\App;

/**
 * Decide whether the session cookie should be written on this response.
 *
 * Only emit it when the session was actually used during this request, or the
 * visitor already carries one. Stamping Set-Cookie on a fresh anonymous request
 * defeats full-page caches (Pressable batcache, Varnish), which refuse to serve
 * a cached response that carries a cookie. The `waffle/should_send_session_cookie`
 * filter lets callers force the decision (e.g. per-route).
 */
if (!function_exists('waffle_should_send_session_cookie')) {
    function waffle_should_send_session_cookie(): bool
    {
        $app = App::getInstance();

        $cookie_name = $app->get('config')->get('session.cookie');

        $should_send = $app->resolved('session') || isset($_COOKIE[$cookie_name]);

        return (bool) apply_filters('waffle/should_send_session_cookie', $should_send);
    }
}

add_action('send_headers', function (): void {
    if (!waffle_should_send_session_cookie()) {
        return;
    }

    $app = App::getInstance();

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
 * Defer saving session until the last possible moment
 */
add_action('shutdown', function (): void {
    $app = App::getInstance();

    // Nothing touched the session this request -> nothing to persist. Avoids a
    // needless DB write (and an empty session row) on every anonymous hit.
    if ($app->resolved('session')) {
        $app->get('session')->save();
    }
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
function waffle_delete_expired_sessions_callback(): void
{
    global $wpdb;

    $app = App::getInstance();

    $lifetime = $app->get('config')->get('session.lifetime');

    $app->get('db')->table($wpdb->prefix.'waffle_sessions')
        ->where('last_activity', '<', time() - ($lifetime * 60))
        ->delete();
}

add_action('waffle_delete_expired_sessions', 'waffle_delete_expired_sessions_callback');
