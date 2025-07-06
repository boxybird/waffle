<?php

use BoxyBird\Waffle\App;
use BoxyBird\Waffle\Scheduler;

App::getInstance()->singleton(Scheduler::class, Scheduler::class);