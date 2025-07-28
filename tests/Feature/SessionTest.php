<?php

test('it can store a value in the session', function (): void {
    waffle_session()->put('test', 'test');
    $session_has = waffle_session()->has('test');

    expect($session_has)->toBeTrue();
});

test('it can retrieve a value from the session', function (): void {
    waffle_session()->put('test', 'test');
    $session_value = waffle_session()->get('test');

    expect($session_value)->toBe('test');
});

test('it can forget a value from the session', function (): void {
    waffle_session()->put('test', 'test');
    waffle_session()->forget('test');
    $session = waffle_session()->get('test');

    expect($session)->toBeNull();
});

test('it can flush the entire session', function (): void {
    waffle_session()->put('test', 'test');
    waffle_session()->flush();
    $session = waffle_session()->get('test');

    expect($session)->toBeNull();
});