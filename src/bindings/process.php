<?php

use BoxyBird\Waffle\App;
use Illuminate\Process\Factory;

App::getInstance()->singleton(Factory::class, fn($app): Factory => new Factory);