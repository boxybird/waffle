<?php

test('it can build a uri with all its components', function () {
    $uri = waffle_uri()->of('https://example.com')
        ->withScheme('https')
        ->withHost('test.com')
        ->withPort(8000)
        ->withPath('/users')
        ->withQuery(['page' => 2])
        ->withFragment('section-1');

    expect($uri->value())->toBe('https://test.com:8000/users?page=2#section-1');
});