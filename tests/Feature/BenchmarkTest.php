<?php

test('can measure execution time between sleep functions', function () {
    $measure = waffle_benchmark()->measure([
        'sleep 100 microseconds' => fn() => usleep(100),
        'sleep 200 microseconds' => fn() => usleep(200),
    ]);

    expect($measure)->toMatchArray([
        'sleep 100 microseconds' => is_float($measure['sleep 100 microseconds']),
        'sleep 200 microseconds' => is_float($measure['sleep 200 microseconds']),
    ]);
});
