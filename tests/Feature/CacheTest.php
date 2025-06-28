<?php

test('can put cache', function () {
    $cache = waffle_cache()->put('test', 'test', 10);

    expect($cache)->toBeTrue();
});

test('can get cache', function () {
    $cache = waffle_cache()->get('test');

    expect($cache)->toBe('test');
});

test('can delete cache', function () {
    $cache = waffle_cache()->delete('test');

    expect($cache)->toBeTrue();
});

test('can flush cache', function () {
    $cache = waffle_cache()->flush();

    expect($cache)->toBeTrue();
});
