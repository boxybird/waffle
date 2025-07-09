<?php

use BoxyBird\Waffle\App;
use Illuminate\Bus\Dispatcher;
use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;

App::getInstance()->singleton(BusDispatcher::class, fn(): Dispatcher => new Dispatcher(App::getInstance()));
