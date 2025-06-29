<?php

use BoxyBird\Waffle\App;
use BoxyBird\Waffle\Worker;

App::getInstance()->singleton('queue.worker', function ($app): Worker {
    $queue = $app->get('queue');
    $events = $app->get('events');
    $handler = $app->get('exception.handler');

    $isDownForMaintenance = function (): false {
        return false;
    };

    return new Worker($queue, $events, $handler, $isDownForMaintenance, null, $app);
});
