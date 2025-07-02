<?php

test('it can handle a basic GET request', function () {
    $request = waffle_app()->make('request');

    waffle_router(function ($router) {
        $router->get('/', fn() => 'Hello World');
    }, false);

    $response = waffle_app()->make('router')->dispatch($request);

    expect($response->getContent())->toBe('Hello World');
});