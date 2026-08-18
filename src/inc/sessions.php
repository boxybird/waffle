<?php

/**
 * Defer creating and sending session cookie until WordPress is ready
 */

use BoxyBird\Waffle\App;
use Illuminate\Session\Store;

/**
 * Does the session hold anything the visitor needs a cookie for?
 *
 * Starting a session is not the same as using one: Illuminate seeds every store
 * with a `_token` and empty `_flash` bookkeeping, so a request that only read
 * from the session still looks non-empty. Those internals are ignored here,
 * which is what keeps a read-only `waffle_session()->get()` -- a theme checking
 * for a flash message, say -- from making the page uncacheable.
 */
if (!function_exists('waffle_session_has_data')) {
    function waffle_session_has_data(Store $session): bool
    {
        $data = $session->all();

        $flash = $data['_flash'] ?? [];

        unset($data['_token'], $data['_previous'], $data['_flash']);

        $has_data = $data !== [] || !empty($flash['old']) || !empty($flash['new']);

        return (bool) apply_filters('waffle/session_has_data', $has_data, $session);
    }
}

/**
 * Decide whether the session cookie should be written on this response.
 *
 * Only emit it when the session actually holds data. Stamping Set-Cookie on an
 * anonymous request defeats full-page caches (Pressable batcache, Varnish),
 * which refuse to store a response that carries a cookie. The
 * `waffle/should_send_session_cookie` filter lets callers force the decision
 * (e.g. per-route).
 */
if (!function_exists('waffle_should_send_session_cookie')) {
    function waffle_should_send_session_cookie(): bool
    {
        $app = App::getInstance();

        // Never resolve the session just to answer this -- that would cost a
        // database read on requests with no interest in the session.
        $should_send = $app->resolved('session')
            && waffle_session_has_data($app->get('session')->driver());

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
            'httponly' => $cookie->isHttpOnly(),
            'samesite' => $cookie->getSameSite(),
        ],
    );
});

/**
 * Defer saving session until the last possible moment
 */
add_action('shutdown', function (): void {
    $app = App::getInstance();

    // Nothing worth persisting -> no write. Avoids a needless DB round trip
    // (and an empty session row) on every anonymous hit.
    if (!$app->resolved('session')) {
        return;
    }

    $session = $app->get('session')->driver();

    if (waffle_session_has_data($session)) {
        $session->save();
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
