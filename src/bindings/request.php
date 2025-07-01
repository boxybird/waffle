<?php

use BoxyBird\Waffle\App;
use Illuminate\Http\Request;

/**
 * Create a request from server variables and bind it to the container; optional
 */
App::getInstance()->instance(Request::class, Request::capture());

App::getInstance()->singleton('request', fn($app) => $app->get(Request::class));
