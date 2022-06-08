<?php

use Illuminate\Http\Request;

/**
 * Create a request from server variables, and bind it to the container; optional
 */
$request = Request::capture();
$waffle_app->instance('Illuminate\Http\Request', $request);

$waffle_app->singleton('request', function () use ($request) {
    return $request;
});
