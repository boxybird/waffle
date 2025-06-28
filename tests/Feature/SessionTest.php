<?php

test('can put session value', function () {
    waffle_session()->put('test', 'test');
    $session_has = waffle_session()->has('test');

    expect($session_has)->toBeTrue();
});

test('can get session value', function () {
    waffle_session()->put('test', 'test');
    $session_value = waffle_session()->get('test');

    expect($session_value)->toBe('test');
});

test('can forget session value', function () {
    waffle_session()->put('test', 'test');
    $session = waffle_session()->forget('test');

    expect($session)->toBeNull();
});

test('can flush session', function () {
    waffle_session()->put('test', 'test');
    $session = waffle_session()->flush();

    expect($session)->toBeNull();
});
