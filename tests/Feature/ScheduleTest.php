<?php

test('scheduler can immediately execute scheduled task', function (): void {
    $value = 1;

    waffle_schedule()->call(function () use (&$value): void {
        $value = 2;
    })->now();

    expect($value)->toBe(2);
});

test('scheduler can register task with custom hook name', function (): void {
    $value = 1;

    waffle_schedule()
        ->as('namespaced_hook_name')
        ->call(function () use (&$value): void {
            $value = 2;
        })
        ->now();

    expect($value)->toBe(2)
        ->and(has_action('namespaced_hook_name'))->toBeTrue();
});

test('scheduler does not leak hook_name between named and anonymous callers', function (): void {
    $named_marker = null;
    $anon_marker = null;

    waffle_schedule()
        ->as('regression_named_hook')
        ->call(function () use (&$named_marker): void {
            $named_marker = 'named';
        });

    waffle_schedule()
        ->call(function () use (&$anon_marker): void {
            $anon_marker = 'anon';
        });

    do_action('regression_named_hook');

    expect($named_marker)->toBe('named')
        ->and($anon_marker)->toBeNull();
});