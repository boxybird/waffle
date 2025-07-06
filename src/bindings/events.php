<?php

use BoxyBird\Waffle\App;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Events\Dispatcher;

App::getInstance()->singleton('events', fn($app): Dispatcher => new Dispatcher($app));

App::getInstance()->bind(DispatcherContract::class, fn(App $app) => $app->get('events'));

