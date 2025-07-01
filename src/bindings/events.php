<?php

use BoxyBird\Waffle\App;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Events\EventServiceProvider;

(new EventServiceProvider(App::getInstance()))->register();

App::getInstance()->instance(DispatcherContract::class, new Dispatcher(App::getInstance()));
