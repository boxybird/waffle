<?php

use BoxyBird\Waffle\App;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;

App::getInstance()->singleton('queue.worker', function ($app) {
    $queue = $app->get('queue');
    $events = $app->get('events');
    $handler = $app->get('exception.handler');
    
    $isDownForMaintenance = function () {
        return false;
    };

    return new Worker($queue, $events, $handler, $isDownForMaintenance);
});
