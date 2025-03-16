<?php

use BoxyBird\Waffle\App;
use Illuminate\Support\Facades\Facade;

$app = App::getInstance();

Facade::setFacadeApplication($app);
