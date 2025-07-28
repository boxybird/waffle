<?php

test('it can store a value in the cache', function (): void {
    $cache = waffle_cache()->put('test', 'test', 10);

    expect($cache)->toBeTrue();
});

test('it can retrieve a value from cache or store it if it does not exist', function (): void {
    $cache = waffle_cache()->remember('remember', 10, fn(): string => 'remember');

    expect($cache)->toBe('remember');
});

test('it can retrieve a value from the cache', function (): void {
    $cache = waffle_cache()->get('test');

    expect($cache)->toBe('test');
});

test('it can delete a value from the cache', function (): void {
    $cache = waffle_cache()->delete('test');

    expect($cache)->toBeTrue();
});

test('it returns null when retrieving a non-existent value', function (): void {
    $cache = waffle_cache()->get('non_existent_key');

    expect($cache)->toBeNull();
});

test('it returns the cached value from remember without executing the closure', function (): void {
    waffle_cache()->put('remember_me', 'I am here', 10);

    $cache = waffle_cache()->remember('remember_me', 10, fn(): string => 'A different value');

    expect($cache)->toBe('I am here');
});

test('it can store and retrieve different data types', function (): void {
    waffle_cache()->put('array', ['a', 'b'], 10);
    waffle_cache()->put('int', 123, 10);
    waffle_cache()->put('bool', true, 10);

    expect(waffle_cache()
        ->get('array'))->toBe(['a', 'b'])
        ->and(waffle_cache()->get('int'))->toBe(123)
        ->and(waffle_cache()->get('bool'))->toBeTrue();
});

test('it can flush the entire cache', function (): void {
    $cache = waffle_cache()->flush();

    expect($cache)->toBeTrue();
});