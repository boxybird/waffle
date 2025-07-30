<?php

test('it can create a carbon instance and format a date for humans', function (): void {
    $initial = waffle_carbon()->now()->subDays(20);
    $carbon = waffle_carbon()->setDate($initial->year, $initial->month, $initial->day)->diffForHumans();

    expect($carbon)->toBe('2 weeks ago');
});