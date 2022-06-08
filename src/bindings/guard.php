<?php

use Illuminate\Contracts\Auth\Guard;
use BoxyBird\Waffle\Guard as WaffleGuard;

$waffle_app->singleton(Guard::class, function ($waffle_app) {
    return new WaffleGuard();
});
