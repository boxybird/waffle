<?php

test('carbon works', function () {
    $initial = waffle_carbon()->now()->subMonths(5);
    $carbon = waffle_carbon()->setDate($initial->year, $initial->month, $initial->day)->diffForHumans();

    expect($carbon)->toBe('5 months ago');
});
