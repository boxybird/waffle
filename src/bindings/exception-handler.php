<?php

use BoxyBird\Waffle\App;
use BoxyBird\Waffle\ExceptionHandler;

App::getInstance()['exception.handler'] = new ExceptionHandler;
