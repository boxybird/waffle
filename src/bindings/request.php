<?php

use BoxyBird\Waffle\App;
use Illuminate\Http\Request;

/**
 * Create a request from server variables, and bind it to the container; optional
 */
App::getInstance()->instance('Illuminate\Http\Request', Request::capture());

App::getInstance()->singleton('request', function ($app) {
    return $app->get('Illuminate\Http\Request');
});
