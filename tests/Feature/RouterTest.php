<?php

test('it can handle a basic GET request', function () {
    $response = null;

    waffle_router(function ($router) use (&$response) {
        $router->get('/', function () use (&$response) {
            $response = 'Hello World';

            return 'Hello World';
        });
    }, false);

    expect($response)->toBe('Hello World');
});