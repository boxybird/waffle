<?php

use BoxyBird\Waffle\App;
use Illuminate\Container\Container;

/**
 * Regression coverage for the cache-friendly session cookie behavior.
 *
 * The session cookie must NOT be emitted on a fresh anonymous request (it would
 * defeat Pressable/Varnish full-page caching), but MUST be emitted when the
 * session was used or the visitor already carries a cookie.
 *
 * These tests exercise the decision via waffle_should_send_session_cookie()
 * rather than the send_headers hook, since setcookie()/headers require a real
 * HTTP context. The container is a process-wide singleton in the test suite, so
 * we reset the resolution state of `session` before each test to keep the
 * "untouched request" case deterministic regardless of test order.
 */
function reset_session_resolution(): void
{
    $app = App::getInstance();

    $app->forgetInstance('session');

    // forgetInstance() clears the cached instance but not Container::$resolved,
    // so resolved('session') would otherwise stay true once any test resolves
    // the session. Clear the flag directly for an isolated starting point.
    $resolved = (new ReflectionClass(Container::class))->getProperty('resolved');
    $resolved->setAccessible(true);
    $flags = $resolved->getValue($app);
    unset($flags['session']);
    $resolved->setValue($app, $flags);

    $cookie_name = $app->get('config')->get('session.cookie');
    unset($_COOKIE[$cookie_name]);

    remove_all_filters('waffle/should_send_session_cookie');
}

beforeEach(function (): void {
    reset_session_resolution();
});

afterEach(function (): void {
    reset_session_resolution();
});

test('it does not send the session cookie for an untouched anonymous request', function (): void {
    expect(waffle_should_send_session_cookie())->toBeFalse();
});

test('it sends the session cookie when the session was used this request', function (): void {
    waffle_session()->put('probe', 'yes'); // resolves the session singleton

    expect(waffle_should_send_session_cookie())->toBeTrue();
});

test('it sends the session cookie when the visitor already carries one', function (): void {
    $cookie_name = App::getInstance()->get('config')->get('session.cookie');
    $_COOKIE[$cookie_name] = 'existing-session-id';

    expect(waffle_should_send_session_cookie())->toBeTrue();
});

test('the waffle/should_send_session_cookie filter can force the cookie on', function (): void {
    add_filter('waffle/should_send_session_cookie', fn (): bool => true);

    expect(waffle_should_send_session_cookie())->toBeTrue();
});

test('the waffle/should_send_session_cookie filter can force the cookie off', function (): void {
    $cookie_name = App::getInstance()->get('config')->get('session.cookie');
    $_COOKIE[$cookie_name] = 'existing-session-id'; // would normally force true

    add_filter('waffle/should_send_session_cookie', fn (): bool => false);

    expect(waffle_should_send_session_cookie())->toBeFalse();
});
