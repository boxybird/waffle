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