<?php

use BoxyBird\Waffle\App;
use Illuminate\Events\Dispatcher;
use Illuminate\Events\EventServiceProvider;

(new EventServiceProvider(App::getInstance()))->register();

App::getInstance()->instance('Illuminate\Contracts\Events\Dispatcher', new Dispatcher(App::getInstance()));
