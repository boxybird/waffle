<?php

use BoxyBird\Waffle\App;
use Illuminate\Contracts\Auth\Guard;
use BoxyBird\Waffle\Guard as WaffleGuard;

App::getInstance()->singleton(Guard::class, fn(): WaffleGuard => new WaffleGuard());
