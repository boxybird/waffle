<?php

use Illuminate\Contracts\Auth\Guard;
use BoxyBird\Waffle\Guard as WaffleGuard;

$app->singleton(Guard::class, function ($app) {
    return new WaffleGuard();
});
