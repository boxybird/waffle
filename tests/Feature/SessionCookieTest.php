<?php

use BoxyBird\Waffle\App;
use Illuminate\Container\Container;

/**
 * Regression coverage for the cache-friendly session cookie behavior.
 *
 * The session cookie must NOT be emitted unless the session actually holds
 * data. A Set-Cookie header stops Pressable/Varnish from storing the response
 * at all, so stamping one onto anonymous traffic means the page cache never
 * warms.
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
    $flags = $resolved->getValue($app);
    unset($flags['session']);
    $resolved->setValue($app, $flags);

    $cookie_name = $app->get('config')->get('session.cookie');
    unset($_COOKIE[$cookie_name]);

    remove_all_filters('waffle/should_send_session_cookie');
    remove_all_filters('waffle/session_has_data');
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

test('it does not resolve the session just to make the decision', function (): void {
    waffle_should_send_session_cookie();

    expect(App::getInstance()->resolved('session'))->toBeFalse();
});

test('it sends the session cookie when the session holds data', function (): void {
    waffle_session()->put('probe', 'yes'); // resolves the session singleton

    expect(waffle_should_send_session_cookie())->toBeTrue();
});

test('it sends the session cookie when the session holds flash data', function (): void {
    waffle_session()->flash('status', 'saved');

    expect(waffle_should_send_session_cookie())->toBeTrue();
});

test('reading from the session does not send the cookie', function (): void {
    // Starting a session seeds `_token` and an empty `_flash`, which must not
    // count as usage -- otherwise a single read makes the page uncacheable.
    $value = waffle_session()->get('does_not_exist');

    expect($value)->toBeNull()
        ->and(App::getInstance()->resolved('session'))->toBeTrue()
        ->and(waffle_should_send_session_cookie())->toBeFalse();
});

test('it stops sending the cookie once the session data is removed', function (): void {
    $session = waffle_session();

    $session->put('probe', 'yes');
    expect(waffle_should_send_session_cookie())->toBeTrue();

    $session->forget('probe');
    expect(waffle_should_send_session_cookie())->toBeFalse();
});

test('an existing cookie alone does not force the cookie to be re-sent', function (): void {
    // Otherwise a visitor who used a session once would be pinned to uncached
    // responses for as long as they kept the cookie.
    $cookie_name = App::getInstance()->get('config')->get('session.cookie');
    $_COOKIE[$cookie_name] = str_repeat('a1b2c3d4', 5);

    expect(waffle_should_send_session_cookie())->toBeFalse();
});

test('the waffle/session_has_data filter can override the emptiness check', function (): void {
    waffle_session()->get('does_not_exist');

    add_filter('waffle/session_has_data', fn(): bool => true);

    expect(waffle_should_send_session_cookie())->toBeTrue();
});

test('the waffle/should_send_session_cookie filter can force the cookie on', function (): void {
    add_filter('waffle/should_send_session_cookie', fn (): bool => true);

    expect(waffle_should_send_session_cookie())->toBeTrue();
});

test('the waffle/should_send_session_cookie filter can force the cookie off', function (): void {
    waffle_session()->put('probe', 'yes'); // would normally force true

    add_filter('waffle/should_send_session_cookie', fn (): bool => false);

    expect(waffle_should_send_session_cookie())->toBeFalse();
});
