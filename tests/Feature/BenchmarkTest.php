<?php

test('it can measure the execution time of closures', function (): void {
    $measure = waffle_benchmark()->measure([
        'sleep 100 microseconds' => fn(): null => usleep(100),
        'sleep 200 microseconds' => fn(): null => usleep(200),
    ]);

    expect($measure)->toMatchArray([
        'sleep 100 microseconds' => is_float($measure['sleep 100 microseconds']),
        'sleep 200 microseconds' => is_float($measure['sleep 200 microseconds']),
    ]);
});