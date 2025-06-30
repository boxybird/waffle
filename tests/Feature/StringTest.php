<?php

test('it can chain multiple string manipulations', function () {
    $string = waffle_str('hello world')
        ->replace('world', 'universe')
        ->snake()
        ->upper()
        ->value();

    expect($string)->toBe('HELLO_UNIVERSE');
});