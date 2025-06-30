<?php

test('it can store a value in the cache', function () {
    $cache = waffle_cache()->put('test', 'test', 10);

    expect($cache)->toBeTrue();
});

test('it can retrieve a value from cache or store it if it does not exist', function () {
    $cache = waffle_cache()->remember('remember', 10, function () {
        return 'remember';
    });

    expect($cache)->toBe('remember');
});

test('it can retrieve a value from the cache', function () {
    $cache = waffle_cache()->get('test');

    expect($cache)->toBe('test');
});

test('it can delete a value from the cache', function () {
    $cache = waffle_cache()->delete('test');

    expect($cache)->toBeTrue();
});

test('it can flush the entire cache', function () {
    $cache = waffle_cache()->flush();

    expect($cache)->toBeTrue();
});