<?php

use BoxyBird\Waffle\App;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\CallableDispatcher;
use Illuminate\Routing\Contracts\CallableDispatcher as CallableDispatcherContract;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\Router;
use Illuminate\Routing\UrlGenerator;

App::getInstance()->singleton('router', function ($app): Router {
    $router = new Router(new Dispatcher($app), $app);

    $app->singleton(CallableDispatcherContract::class, fn($app): CallableDispatcher => new CallableDispatcher($app));

    $app->singleton(UrlGenerator::class, fn($app): \Illuminate\Routing\UrlGenerator => new UrlGenerator($router->getRoutes(), $app->make('request')));

    $app->bind('redirect', fn($app): Redirector => new Redirector($app->make(UrlGenerator::class)));

    return $router;
});