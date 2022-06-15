<?php

/**
 * Require config binding
 */
require __DIR__ . '/bindings/config.php';

/**
 * Require files binding
 */
require __DIR__ . '/bindings/files.php';

/**
 * Require the guard binding
 */
require __DIR__ . '/bindings/guard.php';

/**
 * Require database binding
 */
require __DIR__ . '/bindings/db.php';

/**
 * Require cache binding
 */
require __DIR__ . '/bindings/cache.php';

/**
 * Require session binding
 */
require __DIR__ . '/bindings/session.php';

/**
 * Require validation binding
 */
require __DIR__ . '/bindings/validation.php';

/**
 * Require the request binding
 */
require __DIR__ . '/bindings/request.php';

/**
 * Require the encrypter binding
 */
require __DIR__ . '/bindings/encrypter.php';

/**
 * Require helper functions
*/
require_once __DIR__ . '/inc/functions.php';

/**
 * Require Carbon Fields
*/
require_once __DIR__ . '/inc/carbon.php';

/**
 * Defer creating and sending session cookie until WordPress is ready
 */
add_action('send_headers', function () {
    $app = BoxyBird\Waffle\App::getInstance();

    $config = $app->get('config');
    $session_manager = $app->get('session');

    $cookie = new Symfony\Component\HttpFoundation\Cookie(
        $session_manager->getName(),
        $session_manager->getId(),
        time() + ($config->get('session.lifetime', 120) * 60),
        $config->get('session.path', '/'),
        $config->get('session.domain', null),
        $config->get('session.secure', true),
        $config->get('session.httponly', true),
        $config->get('session.raw', false),
        $config->get('session.same_site', 'lax')
    );

    setcookie(
        $cookie->getName(),
        $cookie->getValue(),
        $cookie->getExpiresTime(),
        $cookie->getPath(),
        $cookie->getDomain(),
        $cookie->isSecure(),
        $cookie->isHttpOnly()
    );
});

/**
 * Defer saving session until last possible moment
 */
add_action('wp_footer', function () {
    $app = BoxyBird\Waffle\App::getInstance();

    $app->get('session')->save();
}, PHP_INT_MAX);
