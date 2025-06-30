<?php

test('it can map over a collection and convert it to an array', function () {
    $items = waffle_collection(['one', 'two', 'three'])
        ->map(function ($item) {
            return strtoupper($item);
        })
        ->toArray();

    expect($items)->toBe(['ONE', 'TWO', 'THREE']);
});