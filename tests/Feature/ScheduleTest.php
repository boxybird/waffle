<?php

test('it can run a scheduled task', function () {
    $value = 1;

    waffle_schedule()->call(function () use (&$value) {
        $value = 2;
    })->now();

    expect($value)->toBe(2);
});